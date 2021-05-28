<?php

if (!defined('_PS_VERSION_'))
    exit;

require_once dirname(__FILE__) . "/Front.php";

class TunnelVentePiedModuleFrontController extends Front
{
    protected static $TEMPLATE = "pied.tpl";

    /**
     * @var $id_product_pied
     *
     * @example $this->id_product_pied = Configuration::get('TUNNELVENTE_ID_PRODUCT_PIED');
     * @see INSERT INTO `ps_configuration` (`id_shop_group`, `id_shop`, `name`, `value`, `date_add`, `date_upd`)
     *      VALUES (null, null, 'TUNNELVENTE_ID_PRODUCT_PIED', '109', now(), now())
     */
    protected $id_product_pied;

    public function __construct()
    {
        parent::__construct();
        $this->id_product_pied = Configuration::get('TUNNELVENTE_ID_PRODUCT_PIED');
    }

    public function init()
    {
        $this->page_name = 'type de pied';
        parent::init();
        $this->display_column_left  = false;
        $this->display_column_right = false;

        if ($this->ajax && $this->isXmlHttpRequest()) {
            $back = false;

            if (Tools::isSubmit("taille")) {
                $taille = Tools::getValue("taille");
                if (!is_numeric($taille)) {
                    $this->errors[] = Tools::displayError('erreur : choisi la taille de sapin !');
                } else {
                    $npa          = (int) $this->getValueTunnelVent("npa");
                    $id_attribute = (int) $taille;
                    $products     = $this->getSapinDisponible($id_attribute, $npa, $this->getValueTunnelVent("type"));

                    if (count($products)) {
                        $sapin = $products[0]['id_product_attribute'];

                        $this->addValueTunnelVent("id_attribute_taille", $id_attribute);
                        $this->addValueTunnelVent('id_product_sapin', $sapin);
                    } else {
                        $this->errors[] = Tools::displayError('erreur : sapin demandé introuvable !');
                    }
                }
            } else if (Tools::isSubmit("back")) {
                $back = true;
            }

            $this->context->smarty->assign(
                array(
                    "back" => $back
                )
            );
        }

        $return = array(
            'errors'   => $this->errors,
            'hasError' => !empty($this->errors),
            'html'     => $this->getHtmlPied(),
            'numStep'  => 4,
        );

        die(Tools::jsonEncode($return));
    }

    protected function getHtmlPied()
    {
        $smarty                   = $this->context->smarty;
        $product                  = new Product($this->id_product_pied, null, $this->context->language->id);
        $default_product_attribut = [
            "id_product_attribute" => 0,
            "price_ttc"            => 0,
        ];

        $smarty->assign(
            array(
                "result"                   => $this->getListAttributeProductPied(),
                "product"                  => $product,
                "id_product_pied"          => ($this->getValueTunnelVent('id_product_pied')) ? $this->getValueTunnelVent('id_product_pied') : null,
                "default_product_attribut" => $default_product_attribut,
            )
        );

        return $smarty->fetch(dirname(__FILE__) . "/../../views/templates/front/" . self::$TEMPLATE);
    }

    private function getListAttributeProductPied()
    {
        $id_lang  = $this->context->language->id;
        $sql      = SqlRequete::getSqlProductAttributPied($this->id_product_pied, $this->getValueTunnelVent('npa'), $id_lang);
        $result   = Db::getInstance()->executeS($sql);
        $products = array();

        foreach ($result as $row) {
            $manager            = StockManagerFactory::getManager();
            $item_real_quantity = $manager->getProductRealQuantities(
                $row['id_product'],
                $row['id_product_attribute'],
                ($row['id_warehouse'] == '' ? null : array($row['id_warehouse'])),
                true
            );
            $row['price_ttc']   = number_format(Product::getPriceStatic($row["id_product"], true, $row['id_product_attribute']), 2);
            if ($item_real_quantity > 0)
                $products[] = $row;
        }

        return $products;
    }

}

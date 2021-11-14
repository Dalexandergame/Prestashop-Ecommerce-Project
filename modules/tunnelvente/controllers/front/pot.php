<?php

if (!defined('_PS_VERSION_'))
    exit;

require_once dirname(__FILE__) . "/Front.php";

class TunnelVentePotModuleFrontController extends Front {

    protected static $TEMPLATE = "pot.tpl";
    protected $id_product_pot;

    public function __construct() {
        parent::__construct();
        $this->id_product_pot = Configuration::get('TUNNELVENTE_ID_PRODUCT_POT');//53
    }

    public function init() {
        $this->page_name = 'taillespain';

        parent::init();

        $this->display_column_left  = false;
        $this->display_column_right = false;

        if ($this->ajax && $this->isXmlHttpRequest()) {
            $back = false;

            if (Tools::isSubmit('boule')) {
                $boule = Tools::getValue("boule");
                $this->addValueTunnelVent('id_product_boule', $boule);//$this->context->cookie->__set('id_product_boule', $boule);
            } else if (Tools::isSubmit("back")) {
                $back = true;
            }
            $this->context->smarty->assign(
                [
                    "back" => $back
                ]
            );

            $return = [
                'hasError' => !empty($this->errors),
                'errors' => $this->errors,
                'html' => $this->getHtmlPot(),
                'numStep' => 6,
                'supp' => $this->getValuesTunnelVent(),
            ];

            die(json_encode($return));
        }
    }

    protected function getHtmlPot() {
        $product = new Product($this->id_product_pot, null, $this->context->language->id);
        $smarty  = $this->context->smarty;

        $smarty->assign(
            [
                "result"                      => $this->getListAttributeProductPot(),
                "product"                     => $product,
                "last_id_product_pot_checked" => ($this->getValueTunnelVent('id_product_pot')/*$this->context->cookie->id_product_pot*/)?$this->getValueTunnelVent('id_product_pot')/*$this->context->cookie->id_product_pot*/:null,
                "skip_pot"                    => !in_array((int) $this->getValueTunnelVent('id_attribute_taille'), SqlRequete::$idAttrTailleSapinEnPot),
            ]
        );

        return $smarty->fetch(dirname(__FILE__) . "/../../views/templates/front/" . self::$TEMPLATE);

    }

    private function getListAttributeProductPot() {

        $id_lang = $this->context->language->id;

        $sql      = SqlRequete::getSqlProductAttributPot($this->id_product_pot,$this->getValueTunnelVent('npa'), $id_lang);
        $result   = Db::getInstance()->executeS($sql);
        $products = [];

        foreach ($result as $row) {
            $item_real_quantity = $this->stockGlobal->getQteAvendre(
                $row['id_product'],
                $row['id_product_attribute'],
                $row['id_warehouse'] == '' ? null : array($row['id_warehouse'])
            );

            if ($row['id_warehouse'] == 32 || $row['id_warehouse'] == 21) { // Paris exception
                $price = number_format(round($row["price"], 2), 2);
            } else {
                $price = number_format(round(Product::getPriceStatic($row["id_product"], true, $row['id_product_attribute']), 2), 2);
            }

            $row['price_ttc'] = $price;

            if($item_real_quantity > 0)
                $products[] = $row;
        }
        return $products ;
    }

}

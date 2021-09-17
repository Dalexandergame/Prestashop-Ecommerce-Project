<?php

if (!defined('_PS_VERSION_'))
    exit;

require_once dirname(__FILE__) . "/Front.php";

class TunnelVenteBouleModuleFrontController extends Front
{

    protected static $TEMPLATE = "boule.tpl";
    protected $id_product_boule;

    public function __construct()
    {
        parent::__construct();
        $this->id_product_boule = Configuration::get('TUNNELVENTE_ID_PRODUCT_BOULE');//52
    }

    public function init()
    {
        $this->page_name = 'taillespain';

        parent::init();

        $this->display_column_left  = false;
        $this->display_column_right = false;

        if ($this->ajax && $this->isXmlHttpRequest()) {
            if (Tools::isSubmit('recyclage')) {
                $recyclage = Tools::getValue("recyclage");

                if (!is_numeric($recyclage) && $recyclage < 0) {
                    $this->errors[] = Tools::displayError("erreur : Choisissez le type de recyclage !");
                } else {
                    $this->addValueTunnelVent('id_product_recyclage', $recyclage);
                }

                if ((int) Tools::getValue('mobile') == 1) {
                    $vals = [
                        $this->getValueTunnelVent('id_product_sapin'),
                        $this->getValueTunnelVent('id_product_pied'),
                    ];

                    foreach ($vals as $id_product_attribute) {
                        if ($id_product_attribute > 0) {
                            $sql        = 'SELECT id_product FROM ' . _DB_PREFIX_ . 'product_attribute WHERE id_product_attribute = ' . $id_product_attribute;
                            $id_product = (int) Db::getInstance()->getValue($sql);

                            if ($id_product > 0) {
                                $t = $this->addProductInCart(1, $id_product, $id_product_attribute);

                                if (!$t) {
                                    $_product       = new Product($id_product, false, $this->context->language->id);
                                    $this->errors[] = Tools::displayError("erreur : d'ajout de produit " . $_product->name);
                                }
                            } else {
                                $this->errors[] = Tools::displayError("erreur : d'ajout de produit " . $id_product);
                            }
                        }
                    }

                    $id_product_recyclage = (int) $this->getValueTunnelVent('id_product_recyclage');
                    if ($id_product_recyclage > 0 &&
                        ($id_product_recyclage == (int) Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_ECOSAPIN_GRATUIT') ||
                            $id_product_recyclage == (int) Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_SAPIN_SUISSE_PAYANT') ||
                            $id_product_recyclage == (int) Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_SAPIN_SUISSE_GRATUIT'))
                    ) {
                        $this->addProductInCart(1, $id_product_recyclage);
                    }

                    $npa = $this->getValueTunnelVent('npa');

                    //* save NPA in cart*/
                    if (!$this->context->cart->id)
                        $this->TestCart();

                    $cart = $this->context->cart;
                    $sql  = "UPDATE " . _DB_PREFIX_ . "cart  SET npa = '{$npa}' WHERE id_cart = {$cart->id}";

                    Db::getInstance()->execute($sql);
                    //* END */

                    $this->removeValueTunnelVent('id_product_sapin');
                    $this->removeValueTunnelVent('id_product_boule');
                    $this->removeValueTunnelVent('id_product_pot');
                    $this->removeValueTunnelVent('id_product_recyclage');
                    $this->removeValueTunnelVent('id_product_pied');

                    $return = [
                        'hasError' => !empty($this->errors),
                        'errors'   => $this->errors,
                        'html'     => "for mobile",
                        'numStep'  => 5,
                    ];

                    die(json_encode($return));
                }
            } else if (Tools::isSubmit("back")) {

            } else if ($this->getValueTunnelVent("id_attribute_taille") != Configuration::get('TUNNELVENTE_ID_ATTRIBUTE_PETIT_SAPIN_SUISSE')) {
                $this->errors[] = Tools::displayError("erreur : Choisissez le type de recyclage !");
            }
        }

        $isSapinSwiss = $this->getValueTunnelVent('type') == 13;

        $return = [
            'hasError' => !empty($this->errors),
            'errors'   => $this->errors,
            'html'     => $this->getHtmlBoules(),
            'numStep'  => $isSapinSwiss ? 6 : 5,
        ];

        die(json_encode($return));
    }

    protected function addProductInCart($quantity, $id_product, $id_product_attribute = null)
    {
        // Add cart if no cart found
        if (!$this->context->cart->id) {
            if (Context::getContext()->cookie->id_guest) {
                $guest                             = new Guest(Context::getContext()->cookie->id_guest);
                $this->context->cart->mobile_theme = $guest->mobile_theme;
            }

            $this->context->cart->add();

            if ($this->context->cart->id)
                $this->context->cookie->id_cart = (int) $this->context->cart->id;
        }

        $cart = $this->context->cart;
        /* @var $cart Cart */
        return $cart->updateQty($quantity, $id_product, $id_product_attribute);
    }

    private function getHtmlBoules()
    {
        $product = new Product($this->id_product_boule, null, $this->context->language->id);
        $smarty  = $this->context->smarty;

        $smarty->assign(
            [
                "petit_sapin_suisse" => ($this->getValueTunnelVent("id_attribute_taille") == Configuration::get('TUNNELVENTE_ID_ATTRIBUTE_PETIT_SAPIN_SUISSE')),
                "result"             => $this->getListAttributeProductBoule(),
                "product"            => $product,
                "id_product_boule"   => ($this->getValueTunnelVent('id_product_boule')/*$this->context->cookie->id_product_boule*/) ? $this->getValueTunnelVent('id_product_boule')/*$this->context->cookie->id_product_boule*/ : 0,
            ]
        );

        return stripslashes($smarty->fetch(dirname(__FILE__) . "/../../views/templates/front/" . self::$TEMPLATE));

    }

    private function getListAttributeProductBoule()
    {
        $id_lang = $this->context->language->id;

        $sql      = SqlRequete::getSqlProductAttributBoule($this->id_product_boule, $this->getValueTunnelVent('npa'), $id_lang);
        $result   = Db::getInstance()->executeS($sql);
        $products = [];

        foreach ($result as $row) {
            $item_real_quantity = $this->stockGlobal->getQteAvendre(
                $row['id_product'],
                $row['id_product_attribute'],
                $row['id_warehouse'] == '' ? null : array($row['id_warehouse'])
            );
            $row['price_ttc']   = number_format(Product::getPriceStatic($row["id_product"], true, $row['id_product_attribute']), 2);

            if ($item_real_quantity > 0)
                $products[] = $row;
        }
        return $products;
    }

}

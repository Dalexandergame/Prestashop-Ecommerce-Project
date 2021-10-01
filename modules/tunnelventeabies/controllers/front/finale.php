<?php

if (!defined('_PS_VERSION_'))
    exit;

require_once dirname(__FILE__) . "/FrontAbies.php";

class TunnelVenteAbiesFinaleModuleFrontController extends FrontAbies
{

    protected static $TEMPLATE = "finale.tpl";

    public function init()
    {
        $this->page_name = 'taillespain';
        parent::init();

        $this->display_column_left  = false;
        $this->display_column_right = false;

        if ($this->ajax && $this->isXmlHttpRequest()) {
            if (!empty(Tools::getValue("recyclage"))) {
                $recyclage = Tools::getValue("recyclage");
            } else {
                $recyclage = 0;
            }
            $this->addValueTunnelVent('id_product_recyclage', Tools::getValue("recyclage"));

            $cart                 = $this->context->cart;
            $npa                  = $this->getValueTunnelVent('npa');
            $autresapin           = (int) Tools::getValue("autresapin");
            $id_product_recyclage = (int) $this->getValueTunnelVent('id_product_recyclage');
            $vals                 = array(
                $this->getValueTunnelVent('id_product_sapin'),
                $this->getValueTunnelVent('id_product_pied'),
            );

            foreach ($vals as $id_product_attribute) {
                if ($id_product_attribute > 0) {
                    $sql        = 'SELECT id_product FROM ' . _DB_PREFIX_ . 'product_attribute WHERE id_product_attribute = ' . $id_product_attribute;
                    $id_product = (int) Db::getInstance()->getValue($sql);

                    if ($id_product > 0) {
                        $t = $this->addProductInCart($cart, 1, $id_product, $id_product_attribute);

                        if (!$t) {
                            $_product       = new Product($id_product, false, $this->context->language->id);
                            $this->errors[] = Tools::displayError("erreur : d'ajout de produit " . $_product->name);
                        }
                    } else {
                        $this->errors[] = Tools::displayError("erreur : d'ajout de produit " . $id_product);
                    }
                }
            }

            if ($id_product_recyclage == 66) {
                $this->addProductInCart($cart, 1, $id_product_recyclage);
            }

            if (!$cart->id) {
                $this->TestCart($this->context->cart);
            }

            $sql = "UPDATE " . _DB_PREFIX_ . "cart  SET npa = '{$npa}' WHERE id_cart = {$cart->id}";
            Db::getInstance()->execute($sql);

            $this->removeValueTunnelVent('id_product_sapin');
            $this->removeValueTunnelVent('id_product_pied');
            $this->removeValueTunnelVent('id_product_recyclage');


            $products               = NULL;
            $type                   = $this->getValueTunnelVent("type");
            $id_attribute_taille    = $this->getValueTunnelVent("id_attribute_taille");
            $id_category_accessoire = (int) Configuration::get('TUNNELVENTE_ID_CATEGORIE_ACCESSOIRE');


            if ($id_attribute_taille !=
                Configuration::get('TUNNELVENTE_ID_ATTRIBUTE_PETIT_SAPIN_SUISSE') &&
                $type != Configuration::get('TUNNELVENTE_ID_LITTLE_ECOSAPIN')
            ) {
                $products = self::getProducts($this->context->language->id, 0, 0, "id_product", 'ASC', $npa, $id_category_accessoire);
            }

            $this->context->smarty->assign(
                array(
                    "products" => $products,
                )
            );

            $return = array(
                'hasError' => !empty($this->errors),
                'errors'   => $this->errors,
                'html'     => $this->getHtmlAccessoir($autresapin),
                'numStep'  => 5,
                'sup'      => $this->getValuesTunnelVent(),
            );
            die(Tools::jsonEncode($return));
        }
    }

    protected function addProductInCart(&$cart, $quantity, $id_product, $id_product_attribute = null)
    {
        $this->TestCart($cart);

        return $cart->updateQty($quantity, $id_product, $id_product_attribute);
    }

    private function getHtmlAccessoir($autresapin)
    {
        $smarty = $this->context->smarty;

        $smarty->assign(
            array(
                "autresapin"    => $autresapin,
                'order_process' => Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order',
            )
        );

        return $smarty->fetch(dirname(__FILE__) . "/../../views/templates/front/" . self::$TEMPLATE);
    }

    protected function TestCart(&$cart)
    {
        if (!$cart->id) {
            if (Context::getContext()->cookie->id_guest) {
                $guest              = new Guest(Context::getContext()->cookie->id_guest);
                $cart->mobile_theme = $guest->mobile_theme;
            }
            $cart->add();

            if ($cart->id) {
                $this->context->cookie->id_cart = (int) $cart->id;
            }
        }
    }

    /**
     * Get all available products
     *
     * @param integer $id_lang Language id
     * @param integer $start Start number
     * @param integer $limit Number of products to return
     * @param string $order_by Field for ordering
     * @param string $order_way Way for ordering (ASC or DESC)
     * @return array Products details
     */
    protected static function getProducts($id_lang, $start, $limit, $order_by, $order_way, $npa, $id_category = false,
                                          $only_active = false, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }

        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die (Tools::displayError());
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'p';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        } elseif ($order_by == 'position') {
            $order_by_prefix = 'c';
        }

        if (strpos($order_by, '.') > 0) {
            $order_by        = explode('.', $order_by);
            $order_by_prefix = $order_by[0];
            $order_by        = $order_by[1];
        }

        $DefaultEntrepotByNPA = Configuration::get('TUNNELVENTE_DEFAULT_ENTROPOT_STOCK_DISPO');// Entrepot par defaut quand il y a pas de NPA dans la BDD
        $sqlEntrepotByNPA     = SqlRequeteAbies::getSqlEntrepotByNPA($npa, $context->shop->id);

        //test stock dispo pour cette NPA ou non
        $countEntrop = Db::getInstance()->getValue("SELECT COUNT(*) FROM ($sqlEntrepotByNPA) tEntropot");
        if ($countEntrop <= 0) {
            $sqlEntrepotByNPA = $DefaultEntrepotByNPA;
        }

        $sql = '(SELECT DISTINCT p.id_product,pl.name,pl.description
				FROM `' . _DB_PREFIX_ . 'product` p
				' . Shop::addSqlAssociation('product', 'p') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product` ' . Shop::addSqlRestrictionOnLang('pl') . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `' . _DB_PREFIX_ . 'supplier` s ON (s.`id_supplier` = p.`id_supplier`)' .
            ($id_category ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_product` c ON (c.`id_product` = p.`id_product`)' : '') . '
                                JOIN ' . _DB_PREFIX_ . 'stock st ON ( st.id_product = p.`id_product` )
				WHERE pl.`id_lang` = ' . (int) $id_lang .
            ($id_category ? ' AND c.`id_category` = ' . (int) $id_category : '') .
            ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') .
            ($only_active ? ' AND product_shop.`active` = 1' : '') . '
                                    AND st.`usable_quantity` > 0 AND st.id_warehouse IN(' . $sqlEntrepotByNPA . ')                                        
				ORDER BY ' . (isset($order_by_prefix) ? pSQL($order_by_prefix) . '.' : '') . '`' . pSQL($order_by) . '` ' . pSQL($order_way) .
            ($limit > 0 ? ' LIMIT ' . (int) $start . ',' . (int) $limit : '') . ' 
                         )';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }
}

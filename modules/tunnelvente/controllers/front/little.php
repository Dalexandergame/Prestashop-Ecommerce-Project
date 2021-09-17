<?php

if (!defined('_PS_VERSION_'))
    exit;

require_once dirname(__FILE__) . "/Front.php";

class TunnelVenteLittleModuleFrontController extends Front {

    protected static $TEMPLATE = "little.tpl";

    public function init() {
        $this->page_name = 'little';

        parent::init();

        $this->display_column_left  = false;
        $this->display_column_right = false;

        if ($this->ajax && $this->isXmlHttpRequest()) {
            
            if ($this->getValueTunnelVent("type") == Configuration::get('TUNNELVENTE_ID_LITTLE_ECOSAPIN')) {
                
                $id_category_little = (int) Configuration::get('TUNNELVENTE_ID_LITTLE_ECOSAPIN');
                $npa = $this->getValueTunnelVent('npa');
                $products = self::getProducts($this->context->language->id, 0, 0, "id_product", 'ASC',$npa, $id_category_little);                
                
                $smarty = $this->context->smarty;
                $smarty->assign(
                    [
                        "products" => $products,
                    ]
                );
                
            }else{
                $this->errors[] = Tools::displayError("erreur : Cochez Little ecosapin dans la liste des types !");
            }

            $return = [
                'hasError' => !empty($this->errors),
                'errors'   => $this->errors,
                'html'     => $this->getHtmlAccessoir(Configuration::get('TUNNELVENTE_ID_PRODUCT_MYLITTELECOSAPIN')),
                'numStep'  => 9,
                'sup'      => $this->getValuesTunnelVent(),
            ];

            die(json_encode($return));
        }
    }

    protected function addProductInCart($quantity, $id_product, $id_product_attribute = null) {         
        // Add cart if no cart found
        if (!$this->context->cart->id){
            if (Context::getContext()->cookie->id_guest){
                $guest = new Guest(Context::getContext()->cookie->id_guest);
                $this->context->cart->mobile_theme = $guest->mobile_theme;
            }
            $this->context->cart->add();
            if ($this->context->cart->id)
                    $this->context->cookie->id_cart = (int)$this->context->cart->id;
        }
        $cart = $this->context->cart;
        /* @var $cart Cart */
        return $cart->updateQty($quantity, $id_product, $id_product_attribute);
    }

    private function getHtmlAccessoir($autresapin) {
        $smarty = $this->context->smarty;
        $smarty->assign(
            [
                "autresapin"    => $autresapin,
                'order_process' => Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order',
            ]
        );

        return $smarty->fetch(dirname(__FILE__) . "/../../views/templates/front/" . self::$TEMPLATE);

    }

    protected function TestCart() {
        // Add cart if no cart found
        if (!$this->context->cart->id) {
            if (Context::getContext()->cookie->id_guest) {
                $guest = new Guest(Context::getContext()->cookie->id_guest);
                $this->context->cart->mobile_theme = $guest->mobile_theme;
            }

            $this->context->cart->add();
            if ($this->context->cart->id)
                $this->context->cookie->id_cart = (int) $this->context->cart->id;
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
    protected static function getProducts($id_lang, $start, $limit, $order_by, $order_way,$npa, $id_category = false, $only_active = false, Context $context = null)
	{
        if (!$context) { $context = Context::getContext(); }

		$front = true;
		if (!in_array($context->controller->controller_type, array('front', 'modulefront')))
		    { $front = false; }

            $sql = '(SELECT DISTINCT p.id_product,pl.name,pl.description
            FROM `'._DB_PREFIX_.'product` p
            '.Shop::addSqlAssociation('product', 'p').'
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.Shop::addSqlRestrictionOnLang('pl').')
            LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
            LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)'.
            '                               
            WHERE pl.`id_lang` = '.(int)$id_lang.
                ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').
                ($only_active ? ' AND product_shop.`active` = 1' : '').'
                                AND p.id_product = '.(int)Configuration::get('TUNNELVENTE_ID_PRODUCT_MYLITTELECOSAPIN').'
                    )
                      ';

		$rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		
		return ($rq);
	}

}

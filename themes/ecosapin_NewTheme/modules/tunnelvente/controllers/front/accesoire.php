<?php

if (!defined('_PS_VERSION_'))
    exit;

require_once dirname(__FILE__) . "/Front.php";

class tunnelventeaccesoireModuleFrontController extends Front {

    protected static $TEMPLATE = "accessoire.tpl";

    public function init() {
        $this->page_name = 'taillespain';
        parent::init();
        $this->display_column_left = false;
        $this->display_column_right = false;
        if ($this->ajax && $this->isXmlHttpRequest()) {
            $autresapin = false;
            if (Tools::isSubmit('autresapin')) {
                $autresapin = (int) Tools::getValue("autresapin");
                //*
                $vals = array($this->getValueTunnelVent('id_product_sapin'),
                    $this->getValueTunnelVent('id_product_boule'),
                    $this->getValueTunnelVent('id_product_pot'),
                );
                
                foreach ($vals as $id_product_attribute) {
                    if($id_product_attribute > 0){
                        $sql = 'SELECT id_product FROM ' . _DB_PREFIX_ . 'product_attribute WHERE id_product_attribute = ' . $id_product_attribute;
                        $id_product = (int) Db::getInstance()->getValue($sql);
                        if ($id_product > 0) {
                            $t = $this->addProductInCart(1, $id_product, $id_product_attribute);
                            if(!$t){
                               $_product =  new Product($id_product,false,$this->context->language->id);
                                $this->errors[] = Tools::displayError("erreur : d'ajout de produit ".$_product->name);
                            }
                        }
                        //*/
                        else{
                            $this->errors[] = Tools::displayError("erreur : d'ajout de produit ".$id_product);
//                            $this->errors[] = Tools::displayError("erreur test: id_product_attribute {$id_product_attribute} sql ".$sql);
                        }
                        //*/
                    }
                }

                $id_product_recyclage = (int) $this->getValueTunnelVent('id_product_recyclage');

                if ($id_product_recyclage > 0 && 
                        ($id_product_recyclage == (int) Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_ECOSAPIN_GRATUIT') || 
                        $id_product_recyclage == (int) Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_SAPIN_SUISSE_PAYANT') ||
                        $id_product_recyclage == (int) Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_SAPIN_SUISSE_GRATUIT'))) {
                    $this->addProductInCart(1, $id_product_recyclage);
                }
             //*/
                $npa = $this->getValueTunnelVent('npa');
                
                //* save NPA in cart*/
                if(!$this->context->cart->id)
                    $this->TestCart ();
                $cart = $this->context->cart;
                $sql = "UPDATE "._DB_PREFIX_."cart  SET npa = '{$npa}' WHERE id_cart = {$cart->id}";
                Db::getInstance()->execute($sql);
                //* END */

                $this->removeValueTunnelVent('id_product_sapin');
                $this->removeValueTunnelVent('id_product_boule');
                $this->removeValueTunnelVent('id_product_pot');
                $this->removeValueTunnelVent('id_product_recyclage');
                
                $id_category_accessoire = (int) Configuration::get('TUNNELVENTE_ID_CATEGORIE_ACCESSOIRE');// 12
                
                $products=NULL;
                $type = $this->getValueTunnelVent("type");
                $id_attribute_taille = $this->getValueTunnelVent("id_attribute_taille");
                
                
                if($id_attribute_taille != Configuration::get('TUNNELVENTE_ID_ATTRIBUTE_PETIT_SAPIN_SUISSE') && $type != Configuration::get('TUNNELVENTE_ID_LITTLE_ECOSAPIN')){
                    $products = self::getProducts($this->context->language->id, 0, 0, "id_product", 'ASC',$npa, $id_category_accessoire);                
                }
                
                $smarty = $this->context->smarty;
                $smarty->assign(array(
                    "products" => $products,                    
                ));
                
                
            }else{
                $this->errors[] = Tools::displayError("erreur : Choisissez une option !");
            }        
            $return = array(
                'hasError' => !empty($this->errors),
                'errors' => $this->errors,
                'html' => $this->getHtmlAccessoir($autresapin),
                'numStep' => 8,
                'sup' => $this->getValuesTunnelVent(),
            );
            die(Tools::jsonEncode($return));
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
        $smarty->assign(array(
            "autresapin" => $autresapin,
            'order_process' => Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order',
        ));
        $html = $smarty->fetch(dirname(__FILE__) . "/../../views/templates/front/" . self::$TEMPLATE);
        return $html;
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
       protected static function getProducts($id_lang, $start, $limit, $order_by, $order_way,$npa, $id_category = false,
		$only_active = false, Context $context = null)
	{
                if (!$context) { $context = Context::getContext(); }

		$front = true;
		if (!in_array($context->controller->controller_type, array('front', 'modulefront')))
                { $front = false; }

		if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way))
                { die (Tools::displayError()); }
		if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd')
                { $order_by_prefix = 'p'; }
		elseif ($order_by == 'name')
                { $order_by_prefix = 'pl'; }
		elseif ($order_by == 'position')
                { $order_by_prefix = 'c'; }

		if (strpos($order_by, '.') > 0){
			$order_by = explode('.', $order_by);
			$order_by_prefix = $order_by[0];
			$order_by = $order_by[1];
		}
                
                $DefaultEntrepotByNPA = Configuration::get('TUNNELVENTE_DEFAULT_ENTROPOT_STOCK_DISPO');// Entrepot par defaut quand il y a pas de NPA dans la BDD
                $sqlEntrepotByNPA = SqlRequete::getSqlEntrepotByNPA($npa);
                //test stock dispo pour cette NPA ou non
                $countEntrop = Db::getInstance()->getValue("SELECT COUNT(*) FROM ($sqlEntrepotByNPA) tEntropot");
                if($countEntrop <= 0){
                    $sqlEntrepotByNPA = $DefaultEntrepotByNPA;
                }
                
                $sql = '(SELECT DISTINCT p.id_product,pl.name,pl.description
				FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)'.
				($id_category ? 'LEFT JOIN `'._DB_PREFIX_.'category_product` c ON (c.`id_product` = p.`id_product`)' : '').'
                                JOIN ' . _DB_PREFIX_ . 'stock st ON ( st.id_product = p.`id_product` )
				WHERE pl.`id_lang` = '.(int)$id_lang.
					($id_category ? ' AND c.`id_category` = '.(int)$id_category : '').
					($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').
					($only_active ? ' AND product_shop.`active` = 1' : '').'
                                    AND st.`usable_quantity` > 0 AND st.id_warehouse IN('.$sqlEntrepotByNPA.')                                        
				ORDER BY '.(isset($order_by_prefix) ? pSQL($order_by_prefix).'.' : '').'`'.pSQL($order_by).'` '.pSQL($order_way).
				($limit > 0 ? ' LIMIT '.(int)$start.','.(int)$limit : '').' 
                         )'
                    ;
                
		$rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                
//		if ($order_by == 'price')
//			Tools::orderbyPrice($rq, $order_way);
//
//		foreach ($rq as &$row)
//			$row = Product::getTaxesInformations($row);

		return ($rq);
	}


}

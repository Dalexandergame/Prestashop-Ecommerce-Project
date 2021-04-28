<?php

require_once _PS_MODULE_DIR_ . '/suivicommandes/classes/SuiviOrder.php';
require_once _PS_MODULE_DIR_ . '/suivicommandes/classes/pdf/HTMLTemplateFichePdf.php';

class AdminStockSapinsVendusRestantsController extends ModuleAdminController 
{
    
    public $dateDebut = NULL;
    public $dateFin = NULL;
    public $warehouse_selected = NULL;
    public $id_lang = 2; //FR
    
    protected function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        if ( _PS_VERSION_ >= '1.7') {
            return Context::getContext()->getTranslator()->trans($string);
        } else {
            return parent::l($string, $class, $addslashes, $htmlentities);
        }
    }

    public function __construct() {
        $this->bootstrap = true;
        $this->list_no_link = true;
        parent::__construct();
        
        if(Tools::getValue("dateDebut") && Tools::getValue("dateFin") && Tools::getValue("warehouse_selected")){
            $this->dateDebut = Tools::getValue("dateDebut");
            $this->dateFin = Tools::getValue("dateFin");
            $this->warehouse_selected = Tools::getValue("warehouse_selected");
        }
        
    }

    public function getProductsAttributes()
	{
        
        $cat = array(Configuration::get('TUNNELVENTE_ID_ECOSAPIN'),Configuration::get('TUNNELVENTE_ID_SAPIN_SUISSE'),1); //ID categorie pour les kits deco et pots
        
        $sql = "SELECT p.id_product,pac.id_product_attribute,CONCAT(pl.name,'/',al.name) as sapin
                FROM " . _DB_PREFIX_ . "product as p
                JOIN "._DB_PREFIX_."product_lang as pl ON p.id_product = pl.id_product
                JOIN "._DB_PREFIX_."product_attribute as pa ON p.id_product = pa.id_product
                JOIN "._DB_PREFIX_."product_attribute_combination as pac ON pa.id_product_attribute = pac.id_product_attribute
                JOIN "._DB_PREFIX_."attribute_lang as al ON pac.id_attribute = al.id_attribute
                WHERE p.id_category_default IN (". implode(",", $cat).")
                AND pl.id_lang = $this->id_lang
                AND al.id_lang = $this->id_lang";
        
        $resultSapin = Db::getInstance()->executeS($sql);
       
        $vendus = $nonvendus = array();
        foreach($resultSapin as &$product){
            $QtySold = $this->getQtySold($product['id_product'],$product['id_product_attribute']);
            $product['qty_wait_delivery'] = $QtySold - $this->getQtyDelivered($product['id_product'],$product['id_product_attribute']);
            $product['qty_stock_now'] = $this->getQtyStockNow($product['id_product'],$product['id_product_attribute']);
            if($QtySold>0){
                array_push($vendus, $product);
            }
            else{
                array_push($nonvendus, $product);
            }
        }
        
        $sqlLittleEcosapin = "SELECT p.id_product,pa.id_product_attribute,concat(pl.name ,' - Pot/Deco : ',GROUP_CONCAT(al.name)) as sapin
                FROM " . _DB_PREFIX_ . "product as p
                JOIN "._DB_PREFIX_."product_lang as pl ON p.id_product = pl.id_product
                JOIN "._DB_PREFIX_."product_attribute as pa ON p.id_product = pa.id_product
                JOIN "._DB_PREFIX_."product_attribute_combination as pac ON pa.id_product_attribute = pac.id_product_attribute
                JOIN "._DB_PREFIX_."attribute_lang as al ON pac.id_attribute = al.id_attribute
                WHERE p.id_category_default = ".Configuration::get('TUNNELVENTE_ID_CATEGORIE_ACCESSOIRE')." 
                AND pl.id_lang = $this->id_lang
                AND al.id_lang = $this->id_lang
                GROUP BY pa.id_product_attribute";

        $resultLittleEcosapin = Db::getInstance()->executeS($sqlLittleEcosapin);
        
        foreach($resultLittleEcosapin as &$product){
            $QtySold = $this->getQtySold($product['id_product'],$product['id_product_attribute']);
            $product['qty_wait_delivery'] = $QtySold - $this->getQtyDelivered($product['id_product'],$product['id_product_attribute']);
            $product['qty_stock_now'] = $this->getQtyStockNow($product['id_product'],$product['id_product_attribute']);
            if($QtySold>0){
                array_push($vendus, $product);
            }
            else{
                array_push($nonvendus, $product);
            }
        }
        
        //Pour le bec arroseur et boule theodora qui n'ont pas d'attributs id_product 55 et 62
        $sqlBec = "SELECT p.id_product,pl.name as sapin
                FROM " . _DB_PREFIX_ . "product as p
                JOIN "._DB_PREFIX_."product_lang as pl ON p.id_product = pl.id_product
                WHERE p.id_category_default = ".Configuration::get('TUNNELVENTE_ID_CATEGORIE_ACCESSOIRE')." 
                AND p.id_product != 50
                AND pl.id_lang = $this->id_lang";
        
        $resultBec = Db::getInstance()->executeS($sqlBec);
        
        foreach($resultBec as &$product){
            $QtySold = $this->getQtySold($product['id_product']);
            $product['qty_wait_delivery'] = $QtySold - $this->getQtyDelivered($product['id_product']);
            $product['qty_stock_now'] = $this->getQtyStockNow($product['id_product']);
            if($QtySold>0){
                array_push($vendus, $product);
            }
            else{
                array_push($nonvendus, $product);
            }
        }
        
        return array($vendus,$nonvendus);        
	}

        public function getQtySold($idProduct,$idProductAttribute=NULL)
	{
		/*
            $sql = "SELECT SUM(od.product_quantity) as qty_total_vendue
                    FROM "._DB_PREFIX_."orders as o
                    JOIN "._DB_PREFIX_."order_detail as od ON o.id_order = od.id_order
                    LEFT JOIN "._DB_PREFIX_."product_attribute_combination as pac ON od.product_attribute_id = pac.id_product_attribute
                    WHERE od.product_id = $idProduct ";
                    if($idProductAttribute != NULL){
                        $sql .=" AND pac.id_product_attribute = $idProductAttribute ";
                    }
                    $sql .=" AND o.invoice_date between '$this->dateDebut' and '$this->dateFin'
                    AND od.id_warehouse = $this->warehouse_selected";
            
        */
        $sql = "
        	select 
			sum(od.product_quantity) 'qty_total_vendue'
			from "._DB_PREFIX_."order_detail od
			join "._DB_PREFIX_."orders o on o.id_order = od.id_order
			where 
			od.product_id = ".$idProduct."
			AND od.product_attribute_id = ".$idProductAttribute."
			AND o.date_add between '".$this->dateDebut."' and '".$this->dateFin."'
			AND od.id_warehouse = ".$this->warehouse_selected."
			AND o.current_state not in (6, 7, 8)
			group by od.product_id, od.product_attribute_id;
        ";
            $result = Db::getInstance()->executeS($sql);
            return (int)$result[0]["qty_total_vendue"];
        }
        
        public function getQtyDelivered($idProduct,$idProductAttribute=NULL)
	{
            $sql = "SELECT SUM(od.product_quantity) as qty_delivered
                    FROM "._DB_PREFIX_."suivi_orders as so
                    JOIN "._DB_PREFIX_."order_detail as od ON so.id_order = od.id_order
                    JOIN "._DB_PREFIX_."orders as o ON so.id_order = o.id_order
                    LEFT JOIN "._DB_PREFIX_."product_attribute_combination as pac ON od.product_attribute_id = pac.id_product_attribute
                    WHERE od.product_id = $idProduct ";
                    if($idProductAttribute != NULL){
                        $sql .=" AND pac.id_product_attribute = $idProductAttribute ";
                    }
                    $sql .=" AND o.current_state = 5
                    AND so.date_delivery between '$this->dateDebut' and '$this->dateFin'
                    AND so.id_warehouse = $this->warehouse_selected";

            $result = Db::getInstance()->executeS($sql);

            return (int)$result[0]["qty_delivered"];        
	}
        
        public function getQtyStockNow($idProduct,$idProductAttribute=NULL)
	{
        
        $sql = "SELECT s.physical_quantity
                FROM "._DB_PREFIX_."stock as s
                LEFT JOIN "._DB_PREFIX_."product_attribute_combination as pac ON s.id_product_attribute = pac.id_product_attribute
                WHERE s.id_product = $idProduct ";
                if($idProductAttribute != NULL){
                    $sql .=" AND pac.id_product_attribute = $idProductAttribute ";
                }
        $sql .=" AND s.id_warehouse = $this->warehouse_selected";
        
        $result = Db::getInstance()->executeS($sql);
        
        return (int)$result[0]["physical_quantity"];        
	}
        
    public function renderList()
    {
    
        $fieldslist = array(
            'sapin'=>array('title' => 'Sapin'),
            'qty_wait_delivery'=>array('title' => 'Quantité vendue en attente de livraison'),
            'qty_stock_now'=>array('title' => 'Quantité stock actuel')
        );
        
        $helper1 = new HelperList();
        $this->setHelperDisplay($helper1);
        $listVendus = $helper1->generateList($this->getProductsAttributes()[0], $fieldslist);
        
        $helper2 = new HelperList();
        $this->setHelperDisplay($helper2);
        $listNonVendus = $helper2->generateList($this->getProductsAttributes()[1], $fieldslist);
       
        $assign = array( 
            "token" => $this->token,
            "dateDebut" => $this->dateDebut,
            "dateFin" => $this->dateFin,
            "warehouse_selected" => $this->warehouse_selected,
            "warehouses" => $this->setSelectWarehouses(),
            "listVendus" => $listVendus,
            "listNonVendus" => $listNonVendus,
        );
            
            
        $smarty = $this->context->smarty;
        $tpl = $smarty->createTemplate(_PS_MODULE_DIR_ . '\suivicommandes\views\templates\admin\stock_restant.tpl');

        $tpl->assign($assign);
      
        return $tpl->fetch();
        
    }

    public function setSelectWarehouses()
    {
        $res = array();
        $warehouses = WarehouseCore::getWarehouses();
        
        foreach ($warehouses as $warehouse)
        {   
            $res[] = array(
                    'id' => $warehouse['id_warehouse'],
                    'name' => $warehouse['name'],
                    'selected' => ($warehouse['id_warehouse'] == $this->warehouse_selected) ? 'selected' : ''
                );
        }
        return $res;
    }
        
        

}

<?php

require_once _PS_MODULE_DIR_ . '/suivicommandes/classes/SuiviOrder.php';
require_once _PS_MODULE_DIR_ . '/suivicommandes/classes/pdf/HTMLTemplateFichePdf.php';

class AdminStockGlobalViewController extends ModuleAdminController
{
    
    protected function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        if ( _PS_VERSION_ >= '1.7') {
            return Context::getContext()->getTranslator()->trans($string);
        } else {
            return parent::l($string, $class, $addslashes, $htmlentities);
        }
    }
    

    public  $id_lang = 2; //FR
    public  $stock_manager;
    private $date_activity_start;
    private $date_activity_end;


    public function __construct($callParentConstructor = true)
    {
        $this->stock_manager = StockManagerFactory::getManager();
        $this->bootstrap     = true;
        $this->list_no_link  = true;

        $today = new \DateTime("now");
        $year  = $today->format("Y");
        $month = $today->format("m");

        // années d'acivité actuel
        $this->date_activity_start = $month > 6 ? "$year-07-01 00:00:00" : (intval($year) - 1) . "-07-01 00:00:00";
        $this->date_activity_end   = $month >= 6 ? (intval($year) + 1) . "-06-30 00:00:00" : "$year-06-30 00:00:00";

        if ($callParentConstructor)
            parent::__construct();
    }

    public function getProducts()
    {
        $result      = [];
        $sqlEcosapin = "SELECT p.id_product,pac.id_product_attribute,CONCAT(pl.name,'/',al.name,' (id:',p.id_product,',',pac.id_product_attribute,')') as sapin
                FROM " . _DB_PREFIX_ . "product as p
                JOIN " . _DB_PREFIX_ . "product_lang as pl ON p.id_product = pl.id_product
                JOIN " . _DB_PREFIX_ . "product_attribute as pa ON p.id_product = pa.id_product
                JOIN " . _DB_PREFIX_ . "product_attribute_combination as pac ON pa.id_product_attribute = pac.id_product_attribute
                JOIN " . _DB_PREFIX_ . "attribute_lang as al ON pac.id_attribute = al.id_attribute
                WHERE p.id_category_default IN (" . Configuration::get('TUNNELVENTE_ID_ECOSAPIN') . ")
                AND pl.id_lang = $this->id_lang
                AND al.id_lang = $this->id_lang";
        $result      = array_merge($result, Db::getInstance()->executeS($sqlEcosapin));

        $sqlSapinSuisse = "SELECT p.id_product,pac.id_product_attribute,CONCAT(pl.name,'/',al.name,' (id:',p.id_product,',',pac.id_product_attribute,')') as sapin
                FROM " . _DB_PREFIX_ . "product as p
                JOIN " . _DB_PREFIX_ . "product_lang as pl ON p.id_product = pl.id_product
                JOIN " . _DB_PREFIX_ . "product_attribute as pa ON p.id_product = pa.id_product
                JOIN " . _DB_PREFIX_ . "product_attribute_combination as pac ON pa.id_product_attribute = pac.id_product_attribute
                JOIN " . _DB_PREFIX_ . "attribute_lang as al ON pac.id_attribute = al.id_attribute
                WHERE p.id_category_default IN (" . Configuration::get('TUNNELVENTE_ID_SAPIN_SUISSE') . ")
                AND pl.id_lang = $this->id_lang
                AND al.id_lang = $this->id_lang";
        $result         = array_merge($result, Db::getInstance()->executeS($sqlSapinSuisse));

        $sqlKitsdeco = "SELECT p.id_product,pac.id_product_attribute,CONCAT(pl.name,'/',al.name,' (id:',p.id_product,',',pac.id_product_attribute,')') as sapin
                FROM " . _DB_PREFIX_ . "product as p
                JOIN " . _DB_PREFIX_ . "product_lang as pl ON p.id_product = pl.id_product
                JOIN " . _DB_PREFIX_ . "product_attribute as pa ON p.id_product = pa.id_product
                JOIN " . _DB_PREFIX_ . "product_attribute_combination as pac ON pa.id_product_attribute = pac.id_product_attribute
                JOIN " . _DB_PREFIX_ . "attribute_lang as al ON pac.id_attribute = al.id_attribute
                WHERE p.id_category_default IN (" . 1 /*ID categorie pour les kits deco et pots*/ . ")
                AND pl.id_lang = $this->id_lang
                AND al.id_lang = $this->id_lang";
        $result      = array_merge($result, Db::getInstance()->executeS($sqlKitsdeco));

        $sqlLittleEcosapin = "SELECT p.id_product,pa.id_product_attribute,concat(cl.name ,' - ',pl.name,' - ',al.name,' (id:',p.id_product,',',pac.id_product_attribute,')') as sapin
                FROM ps_product as p
                JOIN ps_product_lang as pl ON p.id_product = pl.id_product
                JOIN ps_category_lang as cl ON p.id_category_default = cl.id_category
                JOIN ps_product_attribute as pa ON p.id_product = pa.id_product
                JOIN ps_product_attribute_combination as pac ON pa.id_product_attribute = pac.id_product_attribute
                JOIN ps_attribute_lang as al ON pac.id_attribute = al.id_attribute
                WHERE p.id_product in (92,94) AND pl.id_lang = 2 AND al.id_lang = 2 AND cl.id_lang = 2
                ORDER BY sapin";
        $result            = array_merge($result, Db::getInstance()->executeS($sqlLittleEcosapin));

        //Pour le bec arroseur et boule theodora qui n'ont pas d'attributs id_product 55 et 62
        $sqlBec = "SELECT p.id_product,NULL as id_product_attribute,pl.name as sapin
                FROM " . _DB_PREFIX_ . "product as p
                JOIN " . _DB_PREFIX_ . "product_lang as pl ON p.id_product = pl.id_product
                WHERE p.id_category_default = " . Configuration::get('TUNNELVENTE_ID_CATEGORIE_ACCESSOIRE') . " 
                AND p.id_product != 50
                AND pl.id_lang = $this->id_lang";
//        $result = array_merge($result, Db::getInstance()->executeS($sqlBec));

        return $result;
    }

    public function getProductsAttributes()
    {
        $products   = $this->getProducts();
        $warehouses = WarehouseCore::getWarehouses();

        $fieldslist = array(
            'sapin'        => array('title' => 'Sapin'),
            'qty_stock'    => array('title' => 'stock physique'),
            'qty_vendu'    => array('title' => 'vendu'),
            'qty_a_vendre' => array('title' => 'à vendre'),
            'qty_livre'    => array('title' => 'livré'),
            'qty_a_livrer' => array('title' => 'à livrer'),
        );

        foreach ($warehouses as &$warehouse) {
            $result = array();

            foreach ($products as &$product) {
                $product['qty_vendu'] = $this->getQteVendu($product['id_product'], $product['id_product_attribute'], $warehouse['id_warehouse']);
                $product['qty_livre'] = $this->getQteLivre($product['id_product'], $product['id_product_attribute'], $warehouse['id_warehouse']);

                $qty_stock               = $this->stock_manager->getProductPhysicalQuantities($product['id_product'], $product['id_product_attribute'], $warehouse['id_warehouse']);
                $product['qty_a_vendre'] = $qty_stock - $product['qty_vendu'];
                $product['qty_a_livrer'] = $product['qty_vendu'] - $product['qty_livre'];
                $product['qty_stock']    = $product['qty_a_vendre'] + $product['qty_a_livrer'];

                if ($product['qty_vendu'] > 0 ||
                    $product['qty_livre'] > 0 ||
                    $product['qty_stock'] > 0 ||
                    $product['qty_a_vendre'] > 0 ||
                    $product['qty_a_livrer'] > 0)
                    array_push($result, $product);
            }

            $helper = new HelperList();
            $this->setHelperDisplay($helper);
            $helper->simple_header = true;
            $helper->listTotal     = count($result);
            $helper->row_hover     = true;
            $helper->title         = $warehouse["name"];
            $warehouse['products'] = $helper->generateList($result, $fieldslist);
        }

        return $warehouses;
    }

    public function getGlobalAttributes()
    {
        $warehouses = WarehouseCore::getWarehouses();

        $sqlEcosapin   = "SELECT p.id_product,pac.id_product_attribute,CONCAT(pl.name,'/',al.name) as sapin
                FROM " . _DB_PREFIX_ . "product as p
                JOIN " . _DB_PREFIX_ . "product_lang as pl ON p.id_product = pl.id_product
                JOIN " . _DB_PREFIX_ . "product_attribute as pa ON p.id_product = pa.id_product
                JOIN " . _DB_PREFIX_ . "product_attribute_combination as pac ON pa.id_product_attribute = pac.id_product_attribute
                JOIN " . _DB_PREFIX_ . "attribute_lang as al ON pac.id_attribute = al.id_attribute
                WHERE p.id_category_default IN (" . Configuration::get('TUNNELVENTE_ID_ECOSAPIN') . ")
                AND al.name not like '%coupé avec pied%'
                AND pl.id_lang = $this->id_lang
                AND al.id_lang = $this->id_lang";
        $productsEnPot = Db::getInstance()->executeS($sqlEcosapin);

        $sqlEcosapin   = "SELECT p.id_product,pac.id_product_attribute,CONCAT(pl.name,'/',al.name) as sapin
                FROM " . _DB_PREFIX_ . "product as p
                JOIN " . _DB_PREFIX_ . "product_lang as pl ON p.id_product = pl.id_product
                JOIN " . _DB_PREFIX_ . "product_attribute as pa ON p.id_product = pa.id_product
                JOIN " . _DB_PREFIX_ . "product_attribute_combination as pac ON pa.id_product_attribute = pac.id_product_attribute
                JOIN " . _DB_PREFIX_ . "attribute_lang as al ON pac.id_attribute = al.id_attribute
                WHERE p.id_category_default IN (" . Configuration::get('TUNNELVENTE_ID_ECOSAPIN') . ")
                AND al.name like '%coupé avec pied%'
                AND pl.id_lang = $this->id_lang
                AND al.id_lang = $this->id_lang";
        $productsCoupe = Db::getInstance()->executeS($sqlEcosapin);

        $fieldslist = array(
            'sapin'        => array('title' => 'Sapin'),
            'qty_stock'    => array('title' => 'stock physique'),
            'qty_vendu'    => array('title' => 'vendu'),
            'qty_a_vendre' => array('title' => 'à vendre'),
            'qty_livre'    => array('title' => 'livré'),
            'qty_a_livrer' => array('title' => 'à livrer'),
        );

        foreach ($warehouses as &$warehouse) {
            $result = array();

            $limit = 3;
            foreach ($productsEnPot as &$product) {
                $product['qty_vendu'] = $this->getQteVendu($product['id_product'], $product['id_product_attribute'], $warehouse['id_warehouse']);
                $product['qty_livre'] = $this->getQteLivre($product['id_product'], $product['id_product_attribute'], $warehouse['id_warehouse']);

                $qty_stock               = $this->stock_manager->getProductPhysicalQuantities($product['id_product'], $product['id_product_attribute'], $warehouse['id_warehouse']);
                $product['qty_a_vendre'] = $qty_stock - $product['qty_vendu'];
                $product['qty_a_livrer'] = $product['qty_vendu'] - $product['qty_livre'];
                $product['qty_stock']    = $product['qty_a_vendre'] + $product['qty_a_livrer'];

                if ($product['qty_vendu'] > 0 ||
                    $product['qty_livre'] > 0 ||
                    $product['qty_stock'] > 0 ||
                    $product['qty_a_vendre'] > 0 ||
                    $product['qty_a_livrer'] > 0) {
                    array_push($result, $product);
                    $limit--;
                }
                if ($limit == 0) break;
            }

            $limit = 3;
            foreach ($productsCoupe as &$product) {
                $product['qty_vendu'] = $this->getQteVendu($product['id_product'], $product['id_product_attribute'], $warehouse['id_warehouse']);
                $product['qty_livre'] = $this->getQteLivre($product['id_product'], $product['id_product_attribute'], $warehouse['id_warehouse']);

                $qty_stock               = $this->stock_manager->getProductPhysicalQuantities($product['id_product'], $product['id_product_attribute'], $warehouse['id_warehouse']);
                $product['qty_a_vendre'] = $qty_stock - $product['qty_vendu'];
                $product['qty_a_livrer'] = $product['qty_vendu'] - $product['qty_livre'];
                $product['qty_stock']    = $product['qty_a_vendre'] + $product['qty_a_livrer'];

                if ($product['qty_vendu'] > 0 ||
                    $product['qty_livre'] > 0 ||
                    $product['qty_stock'] > 0 ||
                    $product['qty_a_vendre'] > 0 ||
                    $product['qty_a_livrer'] > 0) {
                    array_push($result, $product);
                    $limit--;
                }
                if ($limit == 0) break;
            }

            $helper = new HelperList();
            $this->setHelperDisplay($helper);
            $helper->simple_header = true;
            $helper->listTotal     = count($result);
            $helper->row_hover     = true;
            $helper->title         = $warehouse["name"];
            $warehouse['products'] = $helper->generateList($result, $fieldslist);
        }

        return $warehouses;
    }

    public function getQteVendu($idProduct, $idProductAttribute = NULL, $idWarehouse = null)
    {
        $sql = "SELECT SUM(od.product_quantity) as qty_delivered
                FROM " . _DB_PREFIX_ . "orders as o
                JOIN " . _DB_PREFIX_ . "order_detail as od ON o.id_order = od.id_order
                LEFT JOIN " . _DB_PREFIX_ . "product_attribute_combination as pac ON od.product_attribute_id = pac.id_product_attribute
                WHERE od.product_id = $idProduct ";
        if ($idProductAttribute != NULL) {
            $sql .= " AND pac.id_product_attribute = $idProductAttribute ";
        }
        $sql .= " AND o.current_state in (2,5,10,12,18,20,21)";
        $sql .= " AND o.date_add between '$this->date_activity_start' and '$this->date_activity_end'";

        if (isset($idWarehouse))
            $sql .= " AND od.id_warehouse = $idWarehouse";

        $result = Db::getInstance()->executeS($sql);
        return count($result) ? (int) $result[0]["qty_delivered"] : 0;
    }

    public function getQteLivre($idProduct, $idProductAttribute = NULL, $idWarehouse = null)
    {
        $sql = "SELECT SUM(od.product_quantity) as qty_delivered
                FROM " . _DB_PREFIX_ . "orders as o
                JOIN " . _DB_PREFIX_ . "order_detail as od ON o.id_order = od.id_order
                LEFT JOIN " . _DB_PREFIX_ . "product_attribute_combination as pac ON od.product_attribute_id = pac.id_product_attribute
                WHERE od.product_id = $idProduct ";
        if ($idProductAttribute != NULL) {
            $sql .= " AND od.product_attribute_id = $idProductAttribute ";
        }
        $sql .= " AND o.current_state in (5, 21)";
        $sql .= " AND o.date_add between '$this->date_activity_start' and '$this->date_activity_end'";

        if (isset($idWarehouse))
            $sql .= " AND od.id_warehouse = $idWarehouse";

        $result = Db::getInstance()->executeS($sql);
        return count($result) ? (int) $result[0]["qty_delivered"] : 0;
    }

    public function getQteAvendre($idProduct, $idProductAttribute, $warehouse, $dummy = null)
    {
        $idWarehouse = $warehouse[0];
        $qty_stock   = $this->stock_manager->getProductPhysicalQuantities($idProduct, $idProductAttribute, $idWarehouse);
        $qty_vendu   = $this->getQteVendu($idProduct, $idProductAttribute, $idWarehouse);
        return $qty_stock - $qty_vendu;
    }

    public function renderList()
    {
        $list_warehouses = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "warehouse order by reference, name");
        $all_warehouses  = $this->getGlobalAttributes();
        $warehouses      = $this->getProductsAttributes();

        $assign = array(
            "list_warehouses" => $list_warehouses,
            "all_warehouses"  => $all_warehouses,
            "warehouses"      => $warehouses,
        );

        $smarty = $this->context->smarty;
        $tpl    = $smarty->createTemplate(_PS_MODULE_DIR_ . '\suivicommandes\views\templates\admin\stock_global.tpl');

        $tpl->assign($assign);

        return $tpl->fetch();
    }
}

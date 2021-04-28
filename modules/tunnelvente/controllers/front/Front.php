<?php

require_once dirname(__FILE__) . "/../../classes/SqlRequete.php";
require_once dirname(__FILE__) . '/../../classes/StepDetail.php';
require_once dirname(__FILE__) . '/../../classes/Step.php';
require_once dirname(__FILE__) . '/../../classes/Steps.php';
require_once dirname(__FILE__) . '/../../../suivicommandes/AdminStockGlobalView.php';

//require_once dirname(__FILE__) . '/../../classes/Functions.php';

class Front extends ModuleFrontControllerCore
{
//    use Functions;

    const TUNNELVENT = "TUNNELVENT";

    public static $steps;
    protected     $stockGlobal;
    protected     $id_product_sapins;
    private       $id_attributeRemoved;
    private       $id_types;

    function getIdProductSapins($cat)
    {

        $sql    = "SELECT id_product FROM " . _DB_PREFIX_ . "product
                   WHERE id_category_default IN (" . implode(",", $cat) . ")";
        $result = Db::getInstance()->executeS($sql);

        $idP = array();
        foreach ($result as $res) {
            $idP[] = $res["id_product"];
        }
        return $idP;
    }

    public function __construct()
    {
        parent::__construct();

        $this->id_types          = array(Configuration::get('TUNNELVENTE_ID_LITTLE_ECOSAPIN'), Configuration::get('TUNNELVENTE_ID_ECOSAPIN'), Configuration::get('TUNNELVENTE_ID_SAPIN_SUISSE'));
        $this->id_product_sapins = $this->getIdProductSapins(array(Configuration::get('TUNNELVENTE_ID_ECOSAPIN'), Configuration::get('TUNNELVENTE_ID_SAPIN_SUISSE')));
        $this->stockGlobal       = new AdminStockGlobalView(false);
    }

    /**
     *
     * @param string $key
     * @param int $value
     */
    public function addValueTunnelVent($key, $value)
    {
        $cookie = $this->context->cookie;
        /* @var $cookie Cookie */
        $vals       = $this->getValuesTunnelVent();
        $vals[$key] = $value;
        $cookie->__set(self::TUNNELVENT, serialize($vals));
        //if($key == "id_attribute_taille") die(var_dump($cookie));
    }

    protected function getValuesTunnelVent()
    {
        if ($this->context->cookie->{self::TUNNELVENT})
            return unserialize($this->context->cookie->{self::TUNNELVENT});
        return array();
    }

    protected function getValueTunnelVent($key)
    {
        $val = $this->getValuesTunnelVent();
        if (isset($val[$key])) {
            return $val[$key];
        }
        return false;
    }

    protected function removeValueTunnelVent($key)
    {
        $vals = $this->getValuesTunnelVent();
        if (isset($vals[$key])) {
            unset($vals[$key]);
        }
        $this->context->cookie->__set(self::TUNNELVENT, serialize($vals));
    }

    public static function getValTunnelVent($key)
    {
        $cookie = Context::getContext()->cookie;
        $vals   = array();
        /* @var $cookie Cookie */
        if ($cookie->{self::TUNNELVENT}) {
            $vals = unserialize($cookie->{self::TUNNELVENT});
        }
        if (isset($vals[$key])) {
            return $vals[$key];
        }
        return false;
    }

    /**
     *
     * @return Steps
     */
    public static function getSteps()
    {

        return self::$steps;
    }

    public function requete($npa)
    {
        //systeme de stock est activé
        $id_lang                   = $this->context->language->id;
        $sql                       = SqlRequete::getSqlAttribute($id_lang);
        $DefaultEntrepotByNPA      = Configuration::get('TUNNELVENTE_DEFAULT_ENTROPOT_STOCK_DISPO'); // Entrepot par defaut quand il y a pas de NPA dans la BDD
        $id_carrier_post           = Configuration::get('TUNNELVENTE_ID_CARRIER_POST');              // transporteur Post
        $this->id_attributeRemoved = array();                                                        // les sapins de taille 220/250 et 270/300 ne doivent pas être disponible si c’est une livraison par la poste.
        if (!class_exists("Region")) {
            require_once(_PS_MODULE_DIR_ . '/gszonevente/models/Region.php');
        }
        $region = Region::getRegionByNpa($npa);
        if (empty($region)) {
            $region = array('id_carrier' => $id_carrier_post);// transporteur Post Si npa n'existe pas
        }
        if (isset($region['id_carrier']) && (int) $region['id_carrier'] == $id_carrier_post) {
            $this->id_attributeRemoved = array(70, 71); // taille 220/250 et 270/300
        }
        if ($npa) {
            $sqlEntrepotByNPA = SqlRequete::getSqlEntrepotByNPA($npa);
            //test stock dispo pour cette NPA ou non
            $countEntrop = Db::getInstance()->getValue("SELECT COUNT(*) FROM ($sqlEntrepotByNPA) tEntropot");
            if ($countEntrop > 0) {
                $sql .= " AND st.id_warehouse IN($sqlEntrepotByNPA)";
            } else {
                $sql .= " AND st.id_warehouse IN($DefaultEntrepotByNPA)";
            }
            // attribute par Entrepot
        }
        $sql    = "SELECT DISTINCT id FROM ($sql) t";
        $result = Db::getInstance()->executeS($sql);

        //Ajouter l'affichage du sapain taille 90/110cm pour tous les NPA
        $sql2   = "SELECT atl.id_attribute as id,`name`, 1 as dispo FROM " . _DB_PREFIX_ . "attribute_lang  atl
                    JOIN `ps_attribute` attr ON attr.`id_attribute` = atl.`id_attribute`
                    WHERE id_lang = {$id_lang} AND (atl.id_attribute IN(" . $sql . ") ) ORDER BY position";
        $result = Db::getInstance()->executeS($sql2);
//         die($sql2);
        return $result;
    }

    public function getTypeDescription($id)
    {

        $id_lang = $this->context->language->id;
        $sql     = "SELECT description ,name FROM " . _DB_PREFIX_ . "category_lang
                   WHERE id_lang = {$id_lang} AND id_category = $id";
        $result  = Db::getInstance()->executeS($sql);

        return $result;

    }

    public function getProductByAttId($idAttribute, $type = NULL)
    {

        $id_lang = $this->context->language->id;
        $sql     = "SELECT p.id_product,p.id_category_default,attrl.id_attribute,attrl.name,stk.quantity, pattr.price 
FROM " . _DB_PREFIX_ . "product_attribute_combination atc
                JOIN `ps_product_attribute` pattr ON pattr.`id_product_attribute` = atc.`id_product_attribute`
                JOIN `ps_stock_available` stk ON stk.`id_product_attribute` = pattr.`id_product_attribute`
                JOIN `ps_product` p ON p.id_product = pattr.id_product
                JOIN ps_attribute_lang attrl ON attrl.id_attribute = atc.id_attribute
                WHERE atc.id_attribute = $idAttribute AND id_lang = $id_lang and p.active = 1";
        if ($type) {
            $sql .= " AND p.id_category_default = $type ";
        }

        $result = Db::getInstance()->executeS($sql);
        return $result;

    }

    /**
     *  list taille
     * @param type $npa
     * @return array
     */
    protected function getTypeDisponible($npa = 0)
    {

        $result = $this->requete($npa);
        $res    = $test = array();


//        array_push($test,9);
        foreach ($result as $value) {

            foreach ($this->getProductByAttId($value["id"]) as $cat) {
                array_push($test, $cat["id_category_default"]);
            }

        }
        $test = array_unique($test);

        foreach ($this->id_types as $typeId) {
            if (in_array($typeId, $test)) {
                $value = $this->getTypeDescription($typeId);
                $res[] = array(
                    'id'   => $typeId,
                    'name' => $value[0]["name"],
                    'desc' => $value[0]["description"]
                );

            }
        }

        return $res;
    }

    /**
     *  list taille
     * @param type $npa
     * @return array
     */
    protected function getTailleDisponible($npa, $type)
    {
        $warehouse   = Db::getInstance()->getValue(SqlRequete::getSqlEntrepotByNPA($npa));

        if (!$warehouse) $warehouse = 1;

        $queryResult = $this->requete($npa);
        $result      = array();

        foreach ($queryResult as $value) {
            if (!in_array($value['id'], $this->id_attributeRemoved)) {
                $product  = [];

                switch ($value['id']) {
                    case 12:
                        $product = $this->getProductByProductAttId(54, 1394, $type);
                        break;
                    case 14:
                        $product = $this->getProductByProductAttId(54, 1396, $type);
                        break;
                    case 17:
                        $product = $this->getProductByProductAttId(65, 1550, $type);
                        break;
                    case 20:
                        $product = $this->getProductByProductAttId(54, 1402, $type);
                        break;
                    case 70:
                        $product = $this->getProductByProductAttId(65, 1551, $type);
                        break;
                    case 71:
                        $product = $this->getProductByProductAttId(65, 1552, $type);
                        break;
                    case 880:
                        $product = $this->getProductByProductAttId(3, 7264, $type);
                        break;
                    case 2113:
                        $product = $this->getProductByProductAttId(65, 9337, $type);
                        break;
                }

                if (count($product)) {
                    $product  = $product[0];
                    $name     = explode("cm", $product["name"]);
                    $quantity = $this->stockGlobal->getQteAvendre($product["id_product"], $product["id_product_attribute"], [$warehouse], true);
                    $result[] = array(
                        'id'       => $product["id_attribute"],
                        'price'    => number_format(round($product["price"] + ($product["price"] * 0.025), 2), 2),
                        'name'     => (count($name) == 2? $name[0] . " cm": $value["name"]),
                        'type'     => (count($name) == 2? $name[1]: ""),
                        "enpot"    => in_array($value['id'], SqlRequete::$idAttrTailleSapinEnPot),
                        'image'    => $this->getImageByAttribute($product["id_attribute"]),
                        'quantity' => $quantity,
                    );
                }
            }
        }

        return array_unique($result, SORT_REGULAR);
    }

    /**
     * @param $idAttribute
     * @return string
     */
    public function getImageByAttribute($idAttribute)
    {
        switch ($idAttribute) {
            case 12:
                return 'en-pot2.jpg';
            case 14:
            case 880:
                return 'en-pot3.jpg';
            case 17:
            case 2113:
                return 'sap_suisse_1.png';
            case 20:
                return 'en-pot1.jpg';
            case 70:
                return 'sap_suisse_2.png';
            case 71:
                return 'sap_suisse_3.png';
        }
    }

    public function getProductByProductAttId($idProduct, $idProductAttribute, $type)
    {
        $id_lang = $this->context->language->id;
        $sql     = "SELECT p.id_product,p.id_category_default,pattr.id_product_attribute,attrl.id_attribute,attrl.name,stk.quantity, pattr.price 
                FROM ps_product_attribute_combination atc
                JOIN `ps_product_attribute` pattr ON pattr.`id_product_attribute` = atc.`id_product_attribute`
                JOIN `ps_stock_available` stk ON stk.`id_product_attribute` = pattr.`id_product_attribute`
                JOIN `ps_product` p ON p.id_product = pattr.id_product
                JOIN ps_attribute_lang attrl ON attrl.id_attribute = atc.id_attribute
                WHERE p.id_product = $idProduct AND pattr.id_product_attribute = $idProductAttribute AND p.id_category_default = $type AND id_lang = $id_lang AND p.active = 1";

        return Db::getInstance()->executeS($sql);
    }

    public function getSapinDisponible($id_attribute = 0, $npa = 0, $type = 0)
    {
        $id_lang = $this->context->language->id;
        if (!$npa) {
            $this->errors[] = Tools::displayError('erreur : saisir NPA !');
            return null;
        }
        if (!$id_attribute) {
            $this->errors[] = Tools::displayError('erreur : choisi la taille de sapin !');
            return null;
        }
        if (!$type) {
            $this->errors[] = Tools::displayError('erreur : choisi le type de sapin !');
            return null;
        }
        //systeme de stock est active
        $sqlEntrepotByNPA = SqlRequete::getSqlEntrepotByNPA($npa);

        $DefaultEntrepotByNPA = Configuration::get('TUNNELVENTE_DEFAULT_ENTROPOT_STOCK_DISPO');// Entrepot par defaut quand il y a pas de NPA dans la BDD
        //test stock dispo pour cette NPA ou non
        $countEntrop = Db::getInstance()->getValue("SELECT COUNT(*) FROM ($sqlEntrepotByNPA) tEntropot");
        if ($countEntrop <= 0) {
            $sqlEntrepotByNPA = $DefaultEntrepotByNPA;
        }
        //product_attribute
        $sql = SqlRequete::getSqlProductAttributAndImage($id_lang) . " WHERE id_attribute  = $id_attribute AND p.id_category_default = $type AND st.`usable_quantity` > 0 AND p.active = 1";


        //Parceque le petit sapin suisse est accessible pour toutes les NPA , on enleve la condition de l'entrepot
        if ($id_attribute != Configuration::get('TUNNELVENTE_ID_ATTRIBUTE_PETIT_SAPIN_SUISSE')) {
            $sql .= " AND st.id_warehouse IN($sqlEntrepotByNPA)";
        }
        $result   = Db::getInstance()->executeS($sql);
        $products = array();
        foreach ($result as $row) {
            $row['price_ttc'] = number_format(Product::getPriceStatic($row["id_product"], true, $row['id_product_attribute']), 2);
            $products[]       = $row;
        }

        return $products;
    }
}

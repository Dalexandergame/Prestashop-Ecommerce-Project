<?php

require_once dirname(__FILE__) . "/../../classes/SqlRequeteAbies.php";
require_once dirname(__FILE__) . '/../../classes/StepDetailAbies.php';
require_once dirname(__FILE__) . '/../../classes/StepAbies.php';
require_once dirname(__FILE__) . '/../../classes/StepsAbies.php';
require_once dirname(__FILE__) . '/../../../suivicommandes/controllers/admin/AdminStockGlobalViewController.php';

//require_once dirname(__FILE__) . '/../../classes/Functions.php';

class FrontAbies extends ModuleFrontControllerCore
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
        $this->stockGlobal       = new AdminStockGlobalViewController(false);
    }

    public function init()
    {
        parent::init();

        if ($this->ajax && $this->isXmlHttpRequest()) {
            $this->assignGeneralPurposeVariables();
        }
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
        $sql                       = SqlRequeteAbies::getSqlAttribute($id_lang);
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
            $sqlEntrepotByNPA = SqlRequeteAbies::getSqlEntrepotByNPA($npa);
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

    public function specialRequete($npa, $type)
    {
        //systeme de stock est activé
        $id_lang                   = $this->context->language->id;
        $sql                       = SqlRequeteAbies::getSpecialSqlAttribute($id_lang, $type);
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
            $sqlEntrepotByNPA = SqlRequeteAbies::getSqlEntrepotByNPA($npa);
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
     * @param string $npa
     * @return array
     */
    protected function getTailleDisponible($npa, $taille_only = true)
    {
        $warehouse   = Db::getInstance()->getValue(SqlRequeteAbies::getSqlEntrepotByNPA($npa));

        if (!$warehouse) $warehouse = 1;

        $today = new \DateTime("now");
        $year = $today->format("Y");
        $month = $today->format("m");
        $date_activity_start = $month > 6 ? "$year-07-01 00:00:00" : (intval($year) - 1) . "-07-01 00:00:00";
        $date_activity_end = $month > 6 ? (intval($year) + 1) . "-06-30 00:00:00" : "$year-06-30 00:00:00";

        $id_lang = $this->context->language->id;
        $product_id = 115;
        $status_vendu = '2,5,9,10,12,18,20,21,22,23,24,25';
        $sql = "SELECT DISTINCT group_concat(atl.id_attribute, ';', atl.name) as attributs,1 as dispo
            FROM ps_attribute_lang atl
                     JOIN ps_product_attribute_combination pac
                          ON atl.id_attribute = pac.id_attribute
                     JOIN ps_product_attribute pa
                          ON pa.id_product_attribute = pac.id_product_attribute
                     JOIN ps_stock st ON st.id_product_attribute = pa.id_product_attribute
            WHERE atl.id_lang = $id_lang
              AND pa.id_product = $product_id
              AND st.id_warehouse = $warehouse
              and (st.initial_quantity - IFNULL((
                                                    SELECT SUM(od.product_quantity) as qty_delivered
                                                    FROM ps_orders as o
                                                             JOIN ps_order_detail as od ON o.id_order = od.id_order
                                                    WHERE od.product_id = pa.id_product
                                                      AND od.product_attribute_id = pa.id_product_attribute
                                                      AND o.current_state in ('$status_vendu')
                                                      AND o.date_add between '$date_activity_start' and '$date_activity_end'
                                                      AND od.id_warehouse = st.id_warehouse
                                                ), 0)) > 0
            group by pa.id_product_attribute
    ";

        $attributs = [];
        $queryResult = Db::getInstance()->executeS($sql);

        foreach ($queryResult as &$item) {
            $attribut = explode(',', $item['attributs']);
            $taille = explode(';', $attribut[0]);

            $item['id'] = $taille[0];
            $item['name'] = $taille[1];
            $element['taille'] = $taille;
            $element['choix'] = explode(';', $attribut[1]);
            $element['essence'] = explode(';', $attribut[2]);
            $key = $element['taille'][0] . '-' . $element['choix'][0] . '-' . $element['essence'][0];
            $attributs[$key] = $element;

            unset($item['attributs']);
        }

        return $taille_only ? array_unique($queryResult, SORT_REGULAR) :
            [array_unique($queryResult, SORT_REGULAR), $attributs];
    }

    protected function getChoixDisponible($npa)
    {
        $warehouse   = Db::getInstance()->getValue(SqlRequeteAbies::getSqlEntrepotByNPA($npa));

        if (!$warehouse) $warehouse = 1;

        $queryResult = $this->specialRequete($npa, 1);

        return array_unique($queryResult, SORT_REGULAR);
    }

    protected function getEssenceDisponible($npa)
    {
        $warehouse   = Db::getInstance()->getValue(SqlRequeteAbies::getSqlEntrepotByNPA($npa));

        if (!$warehouse) $warehouse = 1;

        $queryResult = $this->specialRequete($npa, 2);

        return array_unique($queryResult, SORT_REGULAR);
    }

    protected function getAllCombinations($default = 0)
    {
        $returnCombines = [];
        $sql = "SELECT pa.*, psa.*, p.price as pprice, pal.name, pai.id_image as image FROM ps_product_attribute AS pa
                LEFT JOIN ps_product_attribute_combination AS psa ON psa.id_product_attribute = pa.id_product_attribute
                LEFT JOIN ps_product AS p ON p.id_product = pa.id_product 
                LEFT JOIN ps_attribute_lang pal ON pal.id_attribute = psa.id_attribute AND pal.id_lang = "  . $this->context->language->id . "
                LEFT JOIN ps_product_attribute_image pai ON pai.id_product_attribute = pa.id_product_attribute
                WHERE pa.id_product = 115;";

        $AllCombines = Db::getInstance()->executeS($sql);

        foreach ($AllCombines as $SingleCombine) {
            if (!empty($returnCombines[$SingleCombine['id_product_attribute']])) {
                $returnCombines[$SingleCombine['id_product_attribute']]['combineHash'] *= intval($SingleCombine['id_attribute']);
                $returnCombines[$SingleCombine['id_product_attribute']]['name'] .= " ". $SingleCombine['name'];

                if (empty($default)) {
                    if ($SingleCombine['default_on'] == 1) {
                        $returnCombines[$SingleCombine['id_product_attribute']][intval($SingleCombine['id_attribute'])] = true;
                    }
                } else {
                    if ($SingleCombine['id_product_attribute'] == $default) {
                        $returnCombines[$SingleCombine['id_product_attribute']][intval($SingleCombine['id_attribute'])] = true;
                    }
                }
            } else {
                $Total = $SingleCombine["pprice"] + $SingleCombine["price"];
                $RoundTotal = number_format(round($Total + ($Total * 0.025), 2), 2);
                $productImageAttr = '';
                if (!empty($SingleCombine['image'])) {
                    preg_match_all('/\d{1}/', $SingleCombine['image'], $imagePath);
                    $productImageAttr = __PS_BASE_URI__ . 'img/p/';
                    foreach ($imagePath[0] as $SingleImagePath) {
                        $productImageAttr .= $SingleImagePath . '/';
                    }
                    $productImageAttr .= $SingleCombine['image'] . '.jpg';
                }

                if (empty($default)) {
                    if ($SingleCombine['default_on'] == 1) {
                        $returnCombines[$SingleCombine['id_product_attribute']] = [
                            'combineHash' =>   intval($SingleCombine['id_attribute']),
                            'price'       =>  $RoundTotal,
                            'default'     =>  $SingleCombine['default_on'],
                            'name'        =>  $SingleCombine['name'],
                            'id'          =>  $SingleCombine['id_product_attribute'],
                            'image'       =>  $productImageAttr,
                            intval($SingleCombine['id_attribute'])  =>  true
                        ];
                    } else {
                        $returnCombines[$SingleCombine['id_product_attribute']] = [
                            'combineHash' =>   intval($SingleCombine['id_attribute']),
                            'price'       =>  $RoundTotal,
                            'default'     =>  $SingleCombine['default_on'],
                            'id'          =>    $SingleCombine['id_product_attribute'],
                            'image'       =>  $productImageAttr,
                            'name'        =>  $SingleCombine['name']
                        ];
                    }
                } else {
                    if ($SingleCombine['id_product_attribute'] == $default) {
                        $returnCombines[$SingleCombine['id_product_attribute']] = [
                            'combineHash' =>   intval($SingleCombine['id_attribute']),
                            'price'       =>  $RoundTotal,
                            'default'     =>  1,
                            'name'        =>  $SingleCombine['name'],
                            'id'          =>  $SingleCombine['id_product_attribute'],
                            'image'       =>  $productImageAttr,
                            intval($SingleCombine['id_attribute'])  =>  true
                        ];
                    } else {
                        $returnCombines[$SingleCombine['id_product_attribute']] = [
                            'combineHash' =>   intval($SingleCombine['id_attribute']),
                            'price'       =>  $RoundTotal,
                            'default'     =>  0,
                            'id'          =>    $SingleCombine['id_product_attribute'],
                            'image'       =>  $productImageAttr,
                            'name'        =>  $SingleCombine['name']
                        ];
                    }
                }
            }
        }

        return $returnCombines;
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
                return 'new_sap_suisse_1.png';
            case 20:
                return 'en-pot1.jpg';
            case 70:
                return 'new_sap_suisse_2.png';
            case 71:
                return 'new_sap_suisse_3.png';
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
                WHERE p.id_product = $idProduct AND pattr.id_product_attribute = $idProductAttribute AND id_lang = $id_lang AND p.active = 1";

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
        $sqlEntrepotByNPA = SqlRequeteAbies::getSqlEntrepotByNPA($npa);

        $DefaultEntrepotByNPA = Configuration::get('TUNNELVENTE_DEFAULT_ENTROPOT_STOCK_DISPO');// Entrepot par defaut quand il y a pas de NPA dans la BDD
        //test stock dispo pour cette NPA ou non
        $countEntrop = Db::getInstance()->getValue("SELECT COUNT(*) FROM ($sqlEntrepotByNPA) tEntropot");
        if ($countEntrop <= 0) {
            $sqlEntrepotByNPA = $DefaultEntrepotByNPA;
        }
        //product_attribute
        $sql = SqlRequeteAbies::getSqlProductAttributAndImage($id_lang) . " WHERE id_attribute  = $id_attribute AND p.id_category_default = $type AND st.`usable_quantity` > 0 AND p.active = 1";


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

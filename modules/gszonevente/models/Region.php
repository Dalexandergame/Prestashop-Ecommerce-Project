<?php

class Region extends ObjectModel {

    public $id_gszonevente_region;
    public $name;
    public $id_carrier;
    public $id_country;
    public $id_shop;


    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'gszonevente_region',
        'primary' => 'id_gszonevente_region',
//        'multilang' => false,
        'fields' => array(
            'id_gszonevente_region' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'name' => array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isGenericName', 'required' => true,),
            'id_carrier' => array('type' => self::TYPE_INT, 'lang' => false, 'validate' => 'isInt', 'required' => true,),
            'id_country' => array('type' => self::TYPE_INT, 'lang' => false, 'validate' => 'isInt', 'required' => true,),
        )
    );

    public function save($null_values = false, $autodate = true) {
        $return = parent::save($null_values, $autodate);
        $this->id_gszonevente_region = $this->id;
        return $return;
    }

    public function add($autoDate = true, $nullValues = false)
    {
        if (!$this->id_shop) {
            $this->id_shop = Context::getContext()->shop->id;
        }

        return parent::add($autoDate, $nullValues);
    }
    
    public static function getRegions(){
        
        $region = array();
        $sql = 'SELECT * FROM '._DB_PREFIX_.self::$definition['table'];
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
        foreach ($result as $row)
            $region[$row[self::$definition['primary']]] = $row;
        
        return $region;
    }
    public static function getRegionsDispo($id_warehouse =null){
        
        $region = array();
        $sql = 'SELECT * FROM '._DB_PREFIX_.self::$definition['table'];
        
        $sql .= ' WHERE id_gszonevente_region NOT IN(SELECT id_gszonevente_region FROM '._DB_PREFIX_.'gszonevente_region_warehouse';
        
        if(!is_null($id_warehouse)){    
           $sql .= ' WHERE id_warehouse != '.(int) $id_warehouse;
        }
        
        $sql .= ')';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
        foreach ($result as $row)
            $region[$row[self::$definition['primary']]] = $row;
        
        return $region;
    }
    
    public static function getRegionByNpa($npa){
        
        $sql = 'SELECT * FROM '._DB_PREFIX_.self::$definition['table'].' r
                JOIN `'._DB_PREFIX_.'gszonevente_npa` npa ON r.id_gszonevente_region = npa.id_gszonevente_region                
                WHERE npa.name = \''.$npa.'\' ';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);          
        return $result;
    }
    
    public static function getRegionsByWarehouse($id_warehouse){
        
        $region = array();
        $sql = 'SELECT r.* FROM '._DB_PREFIX_.self::$definition['table'].' r
            JOIN '._DB_PREFIX_.'gszonevente_region_warehouse rw
                ON r.id_gszonevente_region = rw.id_gszonevente_region
                WHERE rw.id_warehouse = '.(int) $id_warehouse;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
        foreach ($result as $row)
            $region[$row[self::$definition['primary']]] = $row;
        
        return $region;
    }
}

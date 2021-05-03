<?php

class Npa extends ObjectModel {

    public $id_gszonevente_npa;
    public $name;
    public $id_gszonevente_region;
    

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'gszonevente_npa',
        'primary' => 'id_gszonevente_npa',
//        'multilang' => false,
        'fields' => array(
            'id_gszonevente_npa' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'name' => array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isGenericName', 'required' => true,),
            'id_gszonevente_region' => array('type' => self::TYPE_INT, 'lang' => false, 'validate' => 'isInt', 'required' => true,),
        )
    );

    public function save($null_values = false, $autodate = true) {
        $return = parent::save($null_values, $autodate);
        $this->id_gszonevente_npa = $this->id;
        return $return;
    }
    
}

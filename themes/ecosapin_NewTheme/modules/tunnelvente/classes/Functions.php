<?php

trait Functions {
    
    function getIdProductSapins($cat) { 
        
        $sql = "SELECT id_product FROM " . _DB_PREFIX_ . "product
                   WHERE id_category_default IN (". implode(",", $cat).")";
        $result = Db::getInstance()->executeS($sql);
       
        $idP = array();
        foreach($result as $res){
            $idP[] = $res["id_product"];
        }
        return $idP;
    }
}

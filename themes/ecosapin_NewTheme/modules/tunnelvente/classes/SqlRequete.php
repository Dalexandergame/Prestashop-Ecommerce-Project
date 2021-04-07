<?php

class SqlRequete {

    public static $idAttrTailleSapin = array(12,13,14,15,16,17,18,19,20,21,70,71,21);//array(12,14,17,20,70,71);
    public static $idAttrTailleSapinEnPot = array(12,14,16,20);

    /**
     *  get ids & name attribute taille dispo
     * @param type $id_lang
     * @return string
     */
    public static function getSqlAttribute($id_lang) {
         $SQL_ATTRIBUT = "SELECT DISTINCT atl.id_attribute as id,atl.name  FROM " . _DB_PREFIX_ . "attribute_lang atl
                JOIN  " . _DB_PREFIX_ . "product_attribute_combination pac
                ON atl.id_attribute = pac.id_attribute
                JOIN  " . _DB_PREFIX_ . "product_attribute pa
                ON pa.id_product_attribute = pac.id_product_attribute      
                JOIN `" . _DB_PREFIX_ . "stock` st ON st.`id_product_attribute` = pa.id_product_attribute
                WHERE 
                atl.id_lang = %s
                AND st.`usable_quantity` > 0 
                AND atl.id_attribute IN(".  implode(",", self::$idAttrTailleSapin).") ";

        return sprintf($SQL_ATTRIBUT, $id_lang);
    }

    /**
     *  get id Entrepot par NPA
     * @param type $npa
     * @return string sql
     */
    public static function getSqlEntrepotByNPA($npa) {
        $sql = "SELECT  wh.id_warehouse FROM  ps_warehouse  wh  JOIN " . _DB_PREFIX_ . "gszonevente_region_warehouse  rw
               ON wh.id_warehouse = rw.id_warehouse                
               WHERE wh.deleted = 0 AND id_gszonevente_region IN(SELECT id_gszonevente_region FROM ps_gszonevente_npa gsnpa WHERE gsnpa.name = '%s' )
                ";
        return sprintf($sql, $npa);
    }

    /**
     *  get product_attribute and image
     * @param int $id_lang
     * @return string sql
     */
    public static function getSqlProductAttributAndImage($id_lang) {
       /*
        $sql = "SELECT pa.id_product_attribute, pa.id_product,pl.name,pl.description,pa.reference,pa.price,st.id_warehouse,i.`id_image`,pl.link_rewrite, il.`legend` FROM  " . _DB_PREFIX_ . "product_attribute_combination pac  
                    JOIN " . _DB_PREFIX_ . "stock st ON ( st.id_product_attribute = pac.id_product_attribute )
                    JOIN " . _DB_PREFIX_ . "product_attribute pa ON ( pa.id_product_attribute = pac.id_product_attribute )
                    JOIN " . _DB_PREFIX_ . "product_lang pl ON ( pl.id_product = pa.id_product  AND pl.`id_lang` = " . (int) ($id_lang) . " )
                    
                    LEFT JOIN  `" . _DB_PREFIX_ . "image` i ON (i.`id_product` = pa.`id_product`  AND i.`cover` = 1 )
                    LEFT JOIN `" . _DB_PREFIX_ . "image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = " . (int) ($id_lang) . ")
                     ";
        
        //**/
        $sql = "SELECT pa.id_product_attribute, pa.id_product,pl.name,pl.description,pa.reference,pa.price,st.id_warehouse,i.`id_image`,pl.link_rewrite, il.`legend` FROM  " . _DB_PREFIX_ . "product_attribute_combination pac  
            JOIN " . _DB_PREFIX_ . "stock st ON ( st.id_product_attribute = pac.id_product_attribute )
            JOIN " . _DB_PREFIX_ . "product_attribute pa ON ( pa.id_product_attribute = pac.id_product_attribute )
            JOIN " . _DB_PREFIX_ . "product_lang pl ON ( pl.id_product = pa.id_product  AND pl.`id_lang` = " . (int) ($id_lang) . " )
            JOIN " . _DB_PREFIX_ . "product p ON p.id_product = pa.id_product
            LEFT JOIN  `" . _DB_PREFIX_ . "product_attribute_image` pai ON (pai.`id_product_attribute` =pa.id_product_attribute )
            LEFT JOIN  `" . _DB_PREFIX_ . "image` i ON (i.`id_image` = pai.id_image )
            LEFT JOIN `" . _DB_PREFIX_ . "image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = " . (int) ($id_lang) . ")
             "    ;     
         //*/
        
        return $sql;
    }
    
    public static function getSqlProductAttributBoule($id_product_boule,$npa,$id_lang) {
        $DefaultEntrepotByNPA = Configuration::get('TUNNELVENTE_DEFAULT_ENTROPOT_STOCK_DISPO');// Entrepot par defaut quand il y a pas de NPA dans la BDD
        $sqlEntrepotByNPA = SqlRequete::getSqlEntrepotByNPA($npa);
        //test stock dispo pour cette NPA ou non
        $countEntrop = Db::getInstance()->getValue("SELECT COUNT(*) FROM ($sqlEntrepotByNPA) tEntropot");
        if($countEntrop <= 0){
            $sqlEntrepotByNPA = $DefaultEntrepotByNPA;
        }
            $sql = "SELECT pa.`id_product_attribute`,pa.price,attl.name,pa.id_product,att.color,i.`id_image`,pl.link_rewrite, il.`legend`, att.id_attribute FROM   " . _DB_PREFIX_ . "product_attribute pa 
                    JOIN " . _DB_PREFIX_ . "product_attribute_combination pac ON pac.id_product_attribute = pa.id_product_attribute
                    JOIN " . _DB_PREFIX_ . "attribute att ON att.id_attribute = pac.`id_attribute`
                    JOIN " . _DB_PREFIX_ . "attribute_lang attl ON att.id_attribute = attl.`id_attribute`
                    JOIN " . _DB_PREFIX_ . "product_lang pl ON ( pl.id_product = pa.id_product  AND pl.`id_lang` = " . (int) ($id_lang) . " )    
                    LEFT JOIN  `" . _DB_PREFIX_ . "product_attribute_image` pai ON (pai.`id_product_attribute` =pa.id_product_attribute )
                    LEFT JOIN  `" . _DB_PREFIX_ . "image` i ON (i.`id_image` = pai.id_image )
                    LEFT JOIN `" . _DB_PREFIX_ . "image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = " . (int) ($id_lang) . ")
                    JOIN " . _DB_PREFIX_ . "stock st ON ( st.id_product_attribute = pa.`id_product_attribute` )
                    WHERE pa.id_product = {$id_product_boule} AND attl.`id_lang` = {$id_lang}
                        AND st.`usable_quantity` > 0 AND st.id_warehouse IN(".$sqlEntrepotByNPA.")
                    ORDER BY att.`position`";
        return $sql;
    }
    
    public static function getSqlProductAttributPot($id_product_boule,$npa,$id_lang) {
        return self::getSqlProductAttributBoule($id_product_boule,$npa, $id_lang);
    }

}

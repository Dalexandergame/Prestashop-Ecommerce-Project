<?php

class SqlRequete
{
    public static $idAttrTailleSapin      = array(12, 14, 17, 20, 70, 71, 880, 2113);
    public static $idAttrTailleSapinEnPot = array(12, 14, 20, 880);

    /**
     *  get ids & name attribute taille dispo
     * @param type $id_lang
     * @return string
     */
    public static function getSqlAttribute($id_lang)
    {
        $SQL_ATTRIBUT = "SELECT DISTINCT atl.id_attribute as id,atl.name  FROM " . _DB_PREFIX_ . "attribute_lang atl
                JOIN  " . _DB_PREFIX_ . "product_attribute_combination pac
                ON atl.id_attribute = pac.id_attribute
                JOIN  " . _DB_PREFIX_ . "product_attribute pa
                ON pa.id_product_attribute = pac.id_product_attribute      
                JOIN `" . _DB_PREFIX_ . "stock` st ON st.`id_product_attribute` = pa.id_product_attribute
                WHERE 
                atl.id_lang = %s
                AND atl.id_attribute IN(" . implode(",", self::$idAttrTailleSapin) . ") ";

        return sprintf($SQL_ATTRIBUT, $id_lang);
    }

    /**
     *  get id Entrepot par NPA
     * @param type $npa
     * @return string sql
     */
    public static function getSqlEntrepotByNPA($npa)
    {
        $sql = "SELECT w.id_warehouse FROM ps_gszonevente_region r
                join ps_gszonevente_npa n on r.id_gszonevente_region = n.id_gszonevente_region
                join ps_warehouse_carrier w on w.id_carrier = r.id_carrier
                WHERE n.name = '%s'";
        return sprintf($sql, $npa);
    }

    /**
     *  get product_attribute and image
     * @param int $id_lang
     * @return string sql
     */
    public static function getSqlProductAttributAndImage($id_lang)
    {
        $sql = "
            SELECT pa.id_product_attribute, pa.id_product,pl.name,pl.description,pa.reference,pa.price,st.id_warehouse,i.`id_image`,pl.link_rewrite, il.`legend` FROM  " . _DB_PREFIX_ . "product_attribute_combination pac  
            JOIN " . _DB_PREFIX_ . "stock st ON ( st.id_product_attribute = pac.id_product_attribute )
            JOIN " . _DB_PREFIX_ . "product_attribute pa ON ( pa.id_product_attribute = pac.id_product_attribute )
            JOIN " . _DB_PREFIX_ . "product_lang pl ON ( pl.id_product = pa.id_product  AND pl.`id_lang` = " . (int) ($id_lang) . " )
            JOIN " . _DB_PREFIX_ . "product p ON p.id_product = pa.id_product
            LEFT JOIN  `" . _DB_PREFIX_ . "product_attribute_image` pai ON (pai.`id_product_attribute` =pa.id_product_attribute )
            LEFT JOIN  `" . _DB_PREFIX_ . "image` i ON (i.`id_image` = pai.id_image )
            LEFT JOIN `" . _DB_PREFIX_ . "image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = " . (int) ($id_lang) . ")
        ";
        return $sql;
    }


    public static function getSqlProductAttributBoule($id_product_boule, $npa, $id_lang)
    {
        $DefaultEntrepotByNPA = Configuration::get('TUNNELVENTE_DEFAULT_ENTROPOT_STOCK_DISPO');// Entrepot par defaut quand il y a pas de NPA dans la BDD
        $sqlEntrepotByNPA     = SqlRequete::getSqlEntrepotByNPA($npa);
        //test stock dispo pour cette NPA ou non
        $countEntrop = Db::getInstance()->getValue("SELECT COUNT(*) FROM ($sqlEntrepotByNPA) tEntropot");
        if ($countEntrop <= 0) {
            $sqlEntrepotByNPA = $DefaultEntrepotByNPA;
        }

        // années d'acivité actuel
        $today = new \DateTime("now");
        $year  = $today->format("Y");
        $month = $today->format("m");

        $date_activity_start = $month > 6 ? "$year-07-01 00:00:00" : (intval($year) - 1) . "-07-01 00:00:00";
        $date_activity_end   = $month >= 6 ? (intval($year) + 1) . "-06-30 00:00:00" : "$year-06-30 00:00:00";

        $sql = "SELECT pa.`id_product_attribute`,pa.price,attl.name,pa.id_product,att.color,i.`id_image`,pl.link_rewrite, il.`legend`, att.id_attribute, st.id_warehouse 
                    FROM " . _DB_PREFIX_ . "product_attribute pa 
                    JOIN " . _DB_PREFIX_ . "product_attribute_combination pac ON pac.id_product_attribute = pa.id_product_attribute
                    LEFT JOIN " . _DB_PREFIX_ . "product_attribute_shop pas ON ( pas.id_product_attribute = pa.id_product_attribute AND pas.`id_shop` = " . Context::getContext()->shop->id . " )
                    JOIN " . _DB_PREFIX_ . "attribute att ON att.id_attribute = pac.`id_attribute`
                    JOIN " . _DB_PREFIX_ . "attribute_lang attl ON att.id_attribute = attl.`id_attribute`
                    JOIN " . _DB_PREFIX_ . "attribute_shop atts ON ( att.id_attribute = atts.`id_attribute` AND atts.`id_shop` = " . Context::getContext()->shop->id . " )
                    JOIN " . _DB_PREFIX_ . "product_lang pl ON ( pl.id_product = pa.id_product  AND pl.`id_lang` = " . (int) ($id_lang) . " AND pl.`id_shop` = " . Context::getContext()->shop->id . " )    
                    LEFT JOIN `" . _DB_PREFIX_ . "product_attribute_image` pai ON (pai.`id_product_attribute` = pa.id_product_attribute )
                    LEFT JOIN `" . _DB_PREFIX_ . "image` i ON (i.`id_image` = pai.id_image )
                    LEFT JOIN `" . _DB_PREFIX_ . "image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = " . (int) ($id_lang) . ")
                    JOIN " . _DB_PREFIX_ . "stock st ON ( st.id_product_attribute = pa.`id_product_attribute` )
                    WHERE pa.id_product = {$id_product_boule} AND attl.`id_lang` = {$id_lang} AND st.id_warehouse IN(" . $sqlEntrepotByNPA . ")
                    AND 0 < st.physical_quantity - (
                        SELECT IFNULL(SUM(od2.product_quantity),0) as qty_delivered
                        FROM " . _DB_PREFIX_ . "orders as o2
                        JOIN " . _DB_PREFIX_ . "order_detail as od2 ON o2.id_order = od2.id_order
                        LEFT JOIN " . _DB_PREFIX_ . "product_attribute_combination as pac2 ON od2.product_attribute_id = pac2.id_product_attribute
                        WHERE od2.product_id = pa.id_product AND pac2.id_product_attribute = pa.`id_product_attribute`
                        AND od2.id_warehouse = st.id_warehouse
                        AND o2.current_state in (2,5,10,12,18,20,21)
                        AND o2.date_add between '$date_activity_start' and '$date_activity_end'
                    )
                    ORDER BY att.`position`";
        return $sql;
    }

    public static function getSqlProductAttributPot($id_product_boule, $npa, $id_lang)
    {
        return self::getSqlProductAttributBoule($id_product_boule, $npa, $id_lang);
    }

    public static function getSqlProductAttributPied($id_product_pied, $npa, $id_lang)
    {
        return self::getSqlProductAttributBoule($id_product_pied, $npa, $id_lang);
    }
}

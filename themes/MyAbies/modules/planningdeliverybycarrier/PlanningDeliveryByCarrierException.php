<?php

/**
 * PlanningDeliveryException class, PlanningDeliveryException.php
 * Planning delivery exceptions management
 * @category classes
 *
 */
class PlanningDeliveryByCarrierExceptionOver extends PlanningDeliveryByCarrierException {

    /**
     * Add an exception
     * @return boolean succeed
     */
    public static function add($date_from, $date_to,$nb_commandes = 0,$id_carrier = 0) {
        
        if (!Validate::isDate($date_from) || !Validate::isDate($date_to))
            die(Tools::displayError());
        
        if((int) $id_carrier == 0)
            return parent::add($date_from, $date_to);
       
        return self::addAndNbCommand($date_from, $date_to, $nb_commandes, $id_carrier);
    }
    /**
     * Add an exception
     * @return boolean succeed
     */
    protected static function addAndNbCommand($date_from, $date_to,$nb_commandes = 0,$id_carrier = 0) {
       
        return (Db::getInstance()->Execute('
		INSERT INTO `' . _DB_PREFIX_ . 'planning_delivery_carrier_exception`
		(`date_from`, `date_to`, `nb_commandes`, `id_carrier`) VALUES(
		\'' . pSQL($date_from) . '\',
		\'' . pSQL($date_to) . '\',
		\'' . pSQL($nb_commandes) . '\',
		' .(int) $id_carrier . ')'));
    }

    /**
     *  update nb commandes
     * 
     * @param int $id_planning_delivery_carrier_exception
     * @param int $nb_commandes
     * @return bool
     */
    public static function updateNbCommande($id_planning_delivery_carrier_exception,$nb_commandes) {
        $sql = 'UPDATE ' . _DB_PREFIX_ . 'planning_delivery_carrier_exception SET `nb_commandes`='.(int)$nb_commandes.' '
                . ' WHERE `id_planning_delivery_carrier_exception` = '. (int) $id_planning_delivery_carrier_exception;
        return (Db::getInstance()->Execute($sql));
    }
    /**
     * Get Exceptions
     * @return array Exceptions
     */
    public static function get($id_carrier = 0) {
        if ($id_carrier == 0)
            return Db::getInstance()->ExecuteS('
		SELECT pde.`id_planning_delivery_carrier_exception`, pde.`date_from`, pde.`date_to`, pde.`nb_commandes`, pde.`id_carrier`
        FROM `'._DB_PREFIX_.'planning_delivery_carrier_exception` pde
		ORDER BY pde.`date_from` ASC');

        return (Db::getInstance()->ExecuteS('
		SELECT pde.`id_planning_delivery_carrier_exception`, pde.`date_from`, pde.`date_to`, pde.`nb_commandes`, pde.`id_carrier`
        FROM `' . _DB_PREFIX_ . 'planning_delivery_carrier_exception` pde
                WHERE id_carrier = ' . $id_carrier . '
		ORDER BY pde.`date_from` ASC'));
    }

    /**
     * Get Exceptions
     * @return array Exceptions
     */
    public static function getDates($id_carrier = 0) {
        $dates = array();
        $exceptions = ($id_carrier == 0) ? parent::get() : self::get($id_carrier);
        foreach ($exceptions as $exception) {
            if ($exception['date_from'] == $exception['date_to']) {
                $date = new DateTime($exception['date_from']);
                $dates[] = $date->format('Y-m-d');
            } else {
                $begin = $exception['date_from'];
                $end = $exception['date_to'];
                $tmpDates = self::getDatesBetween($begin, $end);
                foreach ($tmpDates as $date)
                    $dates[] = $date;
            }
        }
        return (count($dates) > 0) ? '"' . implode('", "', array_unique($dates)) . '"' : '';
    }
    
    public static function getNbCommandeAndRealNbCommand() {
         
        $res = self::getAllNbCommandeAndRealNbCommand();
        $carriersDates = array();
        foreach ($res as $row) {
            $dates = array();
            if ($row['date_from'] == $row['date_to']) {
                $date = new DateTime($row['date_from']);
                $dates[] = $date->format('Y-m-d');
            } else {
                $begin = $row['date_from'];
                $end = $row['date_to'];
                $tmpDates = self::getDatesBetween($begin, $end);
                foreach ($tmpDates as $date)
                    $dates[] = $date;
            }
            $k = "carrier_".$row['id_carrier'];
            if(isset($carriersDates[$k])){
                $carriersDates[$k] = array_merge($carriersDates[$k], $dates);
            }else{
                $carriersDates[$k] = $dates;    
            }            
        }
        return $carriersDates;
    }
    
    public static function getAllNbCommandeAndRealNbCommand() {
        $sql = "SELECT pde.`id_planning_delivery_carrier_exception`,pde.`id_carrier`,pde.`date_from`,pde.`date_to`
                ,pde.`nb_commandes`,count(*) as real_nb_commande
                
               FROM `" . _DB_PREFIX_ . "planning_delivery_carrier_exception` pde 
               JOIN `" . _DB_PREFIX_ . "planning_delivery_carrier` pd ON ( pd.`date_delivery` BETWEEN pde.`date_from` AND pde.`date_to` )
               JOIN `" . _DB_PREFIX_ . "orders` o ON ( pd.`id_order` = o.`id_order` AND  o.`id_carrier` = pde.`id_carrier`)
               JOIN `" . _DB_PREFIX_ . "carrier` pc ON ( pc.`id_carrier` = o.`id_carrier` AND pc.`deleted` = 0 AND pc.`active` = 1 )

                WHERE  `nb_commandes` > 0 AND o.`current_state` NOT IN(6)

                group BY pde.`id_planning_delivery_carrier_exception` 
                HAVING  `nb_commandes` <= real_nb_commande
                ";
        return  Db::getInstance()->executeS($sql);
    }
    
    /**
     * 
     * @param type $npa
     * @return type
     */
    public static function getDateDisponibleByNPA($npa = null) {
        
        
        if (!class_exists("Region")) {
            require_once(_PS_MODULE_DIR_ . '/gszonevente/models/Region.php');
        }
        if(!$npa){
            if (!class_exists("Front")) {
                require_once(_PS_MODULE_DIR_ . '/tunnelvente/controllers/front/Front.php');
            }
            $npa = Front::getValTunnelVent('npa');
        }
        $region = Region::getRegionByNpa($npa);
        if(empty($region)){
            $region = array('id_carrier'=>Configuration::get('TUNNELVENTE_ID_CARRIER_POST'));// transporteur Post Si npa n'existe pas
        }
        $id_carrier = $region['id_carrier'];
        //-- AND pde1.`nb_commandes` > 0
//        $sql = "
//            SELECT pde1.* FROM `" . _DB_PREFIX_ . "planning_delivery_carrier_exception` pde1
//            WHERE pde1.`id_carrier` IN ({$id_carrier})
//            AND pde1.id_planning_delivery_carrier_exception NOT IN
//                (
//                SELECT id_planning_delivery_carrier_exception FROM (
//                    SELECT pde.`id_planning_delivery_carrier_exception`,pde.`id_carrier`,pde.`date_from`,pde.`date_to`
//                    ,pde.`nb_commandes`,count(*) as real_nb_commande
//
//                   FROM `" . _DB_PREFIX_ . "planning_delivery_carrier_exception` pde
//                   JOIN `" . _DB_PREFIX_ . "planning_delivery_carrier` pd ON ( pd.`date_delivery` BETWEEN pde.`date_from` AND pde.`date_to` )
//                   JOIN `" . _DB_PREFIX_ . "orders` o ON ( pd.`id_order` = o.`id_order` AND  o.`id_carrier` = pde.`id_carrier`)
//                   JOIN `" . _DB_PREFIX_ . "carrier` pc ON ( pc.`id_carrier` = o.`id_carrier` AND pc.`deleted` = 0 AND pc.`active` = 1 )
//
//                    WHERE  `nb_commandes` > 0 AND o.`current_state` NOT IN(6) AND pde.`id_carrier` IN ({$id_carrier})
//
//                    group BY pde.`id_planning_delivery_carrier_exception`
//                    HAVING  `nb_commandes` <= real_nb_commande
//                ) t
//                )
//            ";
        $sql = "SELECT 
                    pde1.*
                FROM
                    `ps_planning_delivery_carrier_exception` pde1
                WHERE pde1.`id_carrier` IN ($id_carrier)
                AND   pde1.id_planning_delivery_carrier_exception NOT IN (
                
                        SELECT 
                            id_planning_delivery_carrier_exception
                        FROM
                            (
                                -- carrier that exceeds the maximum number of orders
                                SELECT 
                                    pde.`id_planning_delivery_carrier_exception`,
                                    pde.`id_carrier`,
                                    pde.`date_from`,
                                    pde.`date_to`,
                                    pde.`max_places`,
                                    COUNT(*) AS real_nb_commande
                                FROM 
                                `ps_planning_delivery_carrier_exception` pde, 
                                `ps_planning_delivery_carrier` pd, 
                                `ps_orders` o, 
                                `ps_carrier` pc
                                WHERE 1=1
                                AND pd.`date_delivery` BETWEEN pde.`date_from` AND pde.`date_to`
                                AND pd.`id_order` = o.`id_order` 
                                AND pc.`id_carrier` = o.`id_carrier`
                                AND o.`id_carrier` = pde.`id_carrier`
                                AND pc.`deleted` = 0 
                                AND pc.`active` = 1
                                AND o.`current_state` NOT IN (6) 
                                AND pde.`id_carrier` IN ($id_carrier)
                                GROUP BY pde.`id_planning_delivery_carrier_exception`
                                HAVING real_nb_commande >= `max_places`
                            )            
                t)";
        return Db::getInstance()->executeS($sql);
    }
    
}

;

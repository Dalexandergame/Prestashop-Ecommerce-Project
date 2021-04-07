<?php

/**
 *
 * Planning retour exceptions management
 * @category classes
 *
 */
class PlanningRetourByCarrierExceptionOver  {

    /**
     * Add an exception
     * @return boolean succeed
     */
    public static function add($date_from, $date_to,$nb_commandes = 0,$id_carrier = 0) {
        
        if (!Validate::isDate($date_from) || !Validate::isDate($date_to))
            die(Tools::displayError());              
       
        return (Db::getInstance()->Execute('
		INSERT INTO `' . _DB_PREFIX_ . 'planning_retour_carrier_exception`
		(`date_from`, `date_to`, `nb_commandes`, `id_carrier`) VALUES(
		\'' . pSQL($date_from) . '\',
		\'' . pSQL($date_to) . '\',
                \'' . pSQL($nb_commandes) . '\',
		' .(int) $id_carrier . ')'));
    }

    /**
     * Get Exceptions
     * @return array Exceptions
     */
    public static function get($id_carrier = 0) {
        if ($id_carrier == 0)
            return (Db::getInstance()->ExecuteS('
		SELECT pde.`id_planning_retour_carrier_exception`, pde.`date_from`, pde.`date_to`, pde.`nb_commandes`, pde.`id_carrier`
        FROM `' . _DB_PREFIX_ . 'planning_retour_carrier_exception` pde                
		ORDER BY pde.`date_from` ASC'));

        return (Db::getInstance()->ExecuteS('
		SELECT pde.`id_planning_retour_carrier_exception`, pde.`date_from`, pde.`date_to`, pde.`nb_commandes`, pde.`id_carrier`
        FROM `' . _DB_PREFIX_ . 'planning_retour_carrier_exception` pde
                WHERE id_carrier = ' . $id_carrier . '
		ORDER BY pde.`date_from` ASC'));
    }
    
    /**
     *  update nb commandes
     * 
     * @param int $id_planning_retour_carrier_exception
     * @param int $nb_commandes
     * @return bool
     */
    public static function updateNbCommande($id_planning_retour_carrier_exception,$nb_commandes) {
        $sql = 'UPDATE ' . _DB_PREFIX_ . 'planning_retour_carrier_exception SET `nb_commandes`='.(int)$nb_commandes.' '
                . ' WHERE `id_planning_retour_carrier_exception` = '. (int) $id_planning_retour_carrier_exception;
        return (Db::getInstance()->Execute($sql));
    }

    /**
     * Get Exceptions
     * @return array Exceptions
     */
    public static function getDates($id_carrier = 0) {
        $dates = array();
        $exceptions =  self::get($id_carrier);
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
    
    /**
    * Delete an exception
    * @return boolean succeed
    */
    public static function delete($id_exception)
    {
            if (!Validate::isUnsignedId($id_exception))
                    die(Tools::displayError());
            return (Db::getInstance()->Execute('
            DELETE FROM `'._DB_PREFIX_.'planning_retour_carrier_exception`
            WHERE `id_planning_retour_carrier_exception` = '.(int)($id_exception)));
    }

    /**
    * Renvoie un tableau contenant toutes les dates, jour par jour,
    * comprises entre les deux dates passées en paramètre.
    * NB : les dates doivent être au format aaaa-mm-dd (mais on peut changer le parsing)
    * @param (string) $dStart : date de départ
    * @param (string) $dEnd : date de fin
    * @return (array) aDates : tableau des dates si succès
    * @return (bool) false : si échec
    */
    public static function getDatesBetween($dStart, $dEnd)
    {
            $iStart = strtotime ($dStart);
            $iEnd = strtotime ($dEnd);
            $aDates = array();

            if (false === $iStart || false === $iEnd)
                    return false;
            $aStart = explode ('-', str_replace(' 00:00:00', '', $dStart));
            $aEnd = explode ('-', str_replace(' 00:00:00', '', $dEnd));
            if (count ($aStart) !== 3 || count ($aEnd) !== 3)
                    return false;

            if (false === checkdate ($aStart[1], $aStart[2], $aStart[0]) || false === checkdate ($aEnd[1], $aEnd[2], $aEnd[0]) || $iEnd <= $iStart)
                    return false;
            for ($i = $iStart; $i < $iEnd + 86400; $i = strtotime ('+1 day', $i))
            {
                    $sDateToArr = strftime ('%Y-%m-%d', $i);
                    $aDates[] = $sDateToArr;
            }
            if (isset ($aDates) && !empty ($aDates))
                    return $aDates;
            else
                    return false;
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
        $sql = "SELECT pde.`id_planning_retour_carrier_exception`, pde.`id_carrier`, pde.`date_from`, pde.`date_to`
                , pde.`nb_commandes`, count(*) as real_nb_commande
                
               FROM `" . _DB_PREFIX_ . "planning_retour_carrier_exception` pde 
               JOIN `" . _DB_PREFIX_ . "planning_delivery_carrier` pd ON ( pd.`date_retour` BETWEEN pde.`date_from` AND pde.`date_to` )
               JOIN `" . _DB_PREFIX_ . "orders` o ON ( pd.`id_order` = o.`id_order` AND  o.`id_carrier` = pde.`id_carrier`)
               JOIN `" . _DB_PREFIX_ . "carrier` pc ON ( pc.`id_carrier` = o.`id_carrier` AND pc.`deleted` = 0 AND pc.`active` = 1 )

                WHERE  `nb_commandes` > 0 AND o.`current_state` NOT IN(6)

                group BY pde.`id_planning_retour_carrier_exception` 
                HAVING  `nb_commandes` <= real_nb_commande
                ";
        return Db::getInstance()->executeS($sql);
    }
    
}

;

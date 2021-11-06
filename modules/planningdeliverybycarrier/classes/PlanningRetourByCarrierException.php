<?php

/**
* PlanningDeliveryException class, PlanningDeliveryException.php
* Planning delivery exceptions management
* @category classes
*
* @author Roturier Alexandre <alexandre.roturier@gmail.com>
* @version 1.0
*
*/
class PlanningRetourByCarrierException
{
	/**
	 * Add an exception
	 * @return boolean succeed
	 */
	public static function add($date_from, $date_to, $maxPlaces, $id_carrier)
	{
		if (!Validate::isDate($date_from)
			|| !Validate::isDate($date_to))
			die(Tools::displayError());
		return (Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'planning_retour_carrier_exception`
		(`date_from`, `date_to`, `max_places`,`id_carrier`) VALUES(
		\''.pSQL($date_from).'\',
		\''.pSQL($date_to).'\',
		\''.pSQL($maxPlaces).'\',
		\''.pSQL($id_carrier).'\')'));
	}

	/**
	 * Get Exceptions
	 * @return array Exceptions
	 */
	public static function get()
    {
        $sql = '

        SELECT 
            ppde.`id_planning_retour_carrier_exception`,
            ppde.`date_from`,
            ppde.`date_to`,
            -- ppde.`nb_commandes`,
            ppde.`max_places`,
            ppde.`id_carrier`,
            carrier.`name`,
            (SELECT 
                    COUNT(*) AS real_nb_commande
                FROM
                    `ps_planning_retour_carrier_exception` pde,
                    `ps_planning_delivery_carrier` pd,
                    `ps_orders` o,
                    `ps_carrier` pc
                WHERE
                    1 = 1
                        AND pd.`date_retour` BETWEEN pde.`date_from` AND pde.`date_to`
                        AND pd.`id_order` = o.`id_order`
                        AND pc.`id_carrier` = o.`id_carrier`
                        AND o.`id_carrier` = pde.`id_carrier`
                        AND pc.`deleted` = 0
                        AND pc.`active` = 1
                        AND o.`current_state` NOT IN (6)
                        AND pde.`id_planning_retour_carrier_exception` = ppde.`id_planning_retour_carrier_exception`) nb_commandes
        FROM
            `ps_planning_retour_carrier_exception` ppde
                INNER JOIN
            `ps_carrier` carrier ON carrier.id_carrier = ppde.id_carrier
                INNER JOIN
            `ps_carrier_shop` pcs ON pcs.id_carrier =  carrier.id_carrier
                WHERE pcs.id_shop = '.(int)Context::getContext()->shop->id.';
        ORDER BY ppde.`date_from` ASC
        
        ';

        $cache_id = 'PlanningRetourByCarrierException::get'.md5($sql);

        if (!Cache::isStored($cache_id))
        {
            $list = Db::getInstance()->ExecuteS($sql);
            Cache::store($cache_id, $list);
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * Get Exceptions
     * @return array Exceptions
     */
    public static function getDates()
    {
        $dates = array();
        $exceptions = PlanningRetourByCarrierException::get();
        foreach ($exceptions as $exception)
        {
            if($exception['nb_commandes'] >= $exception['max_places']) continue;
            if ($exception['date_from'] == $exception['date_to'])
            {
                $date = new DateTime($exception['date_from']);
                $dates[] = $date->format('Y-m-d');
            }
            else
            {
                $begin = $exception['date_from'];
                $end = $exception['date_to'];
                $tmpDates = PlanningDeliveryByCarrierException::getDatesBetween ($begin, $end);
                foreach ($tmpDates as $date)
                    $dates[] = $date;
            }
        }
        return (count($dates) > 0) ? '"'.implode('", "', array_unique($dates)).'"' : '';
    }

    /**
     * Get Exceptions
     * @return array Exceptions
     */
    public static function getDatesByCarrier($carrier_id)
    {
        $dates = array();
        $exceptions = PlanningRetourByCarrierException::get();
        foreach ($exceptions as $exception)
        {
            if($exception['id_carrier'] != $carrier_id || $exception['nb_commandes'] >= $exception['max_places']) continue;
            if ($exception['date_from'] == $exception['date_to'])
            {
                $date = new DateTime($exception['date_from']);
                $dates[] = $date->format('Y-m-d');
            }
            else
            {
                $begin = $exception['date_from'];
                $end = $exception['date_to'];
                $tmpDates = PlanningRetourByCarrierException::getDatesBetween ($begin, $end);
                foreach ($tmpDates as $date)
                    $dates[] = $date;
            }
        }
        return (count($dates) > 0) ? '"'.implode('", "', array_unique($dates)).'"' : '';
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

    /**
     * Add an exception
     * @return boolean succeed
     */
    protected static function addAndNbCommand($date_from, $date_to,$nb_commandes = 0,$id_carrier = 0) {

        return (Db::getInstance()->Execute('
        INSERT INTO `' . _DB_PREFIX_ . 'planning_retour_carrier_exception`
        (`date_from`, `date_to`, `nb_commandes`, `id_carrier`) VALUES(
        \'' . pSQL($date_from) . '\',
        \'' . pSQL($date_to) . '\',
        \'' . pSQL($nb_commandes) . '\',
        ' .(int) $id_carrier . ')'));
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
        $carrier_id = $region['id_carrier'];
        if($carrier_id == 7) return ['allow poste'];

        $dates = array();
        $today = new DateTime((new DateTime())->format('Y-m-d') . ' 00:00:00.000');
        $exceptions = PlanningRetourByCarrierException::get();
        foreach ($exceptions as $exception)
        {
            if($exception['id_carrier'] != $carrier_id || $exception['nb_commandes'] >= $exception['max_places']) continue;
            if ($exception['date_from'] == $exception['date_to'])
            {
                $date = new DateTime($exception['date_from']);
                if($date < $today) continue;
                $dates[] = $date->format('Y-m-d');
            }
            else
            {
                $begin = $exception['date_from'];
                $end = $exception['date_to'];
                $tmpDates = PlanningRetourByCarrierException::getDatesBetween ($begin, $end);

                foreach ($tmpDates as $date) {
                    $date = new DateTime($date . ' 00:00:00.000');
                    if($date < $today) continue;
                    $dates[] = $date->format('Y-m-d');
                }
            }
        }
        return $dates;
    }
};

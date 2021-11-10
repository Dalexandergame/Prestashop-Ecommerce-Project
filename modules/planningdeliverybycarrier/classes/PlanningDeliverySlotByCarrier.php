<?php

/**
	* PlanningDeliverySlot class, PlanningDeliverySlot.php
	* Planning delivery slots management
	* @category classes
	*
	* @author Roturier Alexandre <alexandre.roturier@gmail.com>
	* @version 1.0
	*
	*/
class PlanningDeliverySlotByCarrier
{
	/**
	 * Add a Slot
	 *
	 * @return boolean succeed
	 */
	public static function add($id_lang, $name, $slot1, $slot2, $customers_max)
	{
		if (!Validate::isUnsignedId($id_lang)
			|| !Validate::isMessage($name)
			|| !Validate::isDate($slot1)
			|| !Validate::isDate($slot2))
			die(Tools::displayError());

		return (Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'planning_delivery_carrier_slot`
		(`id_lang`, `name`, `slot1`, `slot2`, `customers_max`) VALUES(
		'.(int)$id_lang.',
		\''.pSQL($name).'\',
		\''.pSQL($slot1).'\',
		\''.pSQL($slot2).'\',
		'.(int)$customers_max.')'));
	}

	/**
	 * Link a slot to a day
	 *
	 * @return boolean succeed
	 */
	public static function addToDay($id_planning_delivery_slot, $id_day, $id_carrier)
	{
		if (!Validate::isUnsignedId($id_planning_delivery_slot)
			|| !Validate::isUnsignedId($id_day)
			|| !Validate::isUnsignedId($id_carrier))
			die(Tools::displayError());
		return (Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'planning_delivery_carrier_slot_day`
		(`id_planning_delivery_carrier_slot`, `id_day`, `id_carrier`) VALUES(
		'.(int)$id_planning_delivery_slot.',
		'.(int)$id_day.',
		'.(int)$id_carrier.')'));
	}

	/**
	 * Update slot
	 *
	 * @return boolean succeed
	 */
	public static function update($id_planning_delivery_slot, $id_lang, $name, $slot1, $slot2, $customers_max)
	{
		if (!Validate::isUnsignedId($id_planning_delivery_slot)
			|| !Validate::isUnsignedId($id_lang)
			|| !Validate::isMessage($name)
			|| !Validate::isDate($slot1)
			|| !Validate::isDate($slot2))
			die(Tools::displayError());
		return (Db::getInstance()->Execute('
		UPDATE `'._DB_PREFIX_.'planning_delivery_carrier_slot` SET
		`name` = \''.pSQL($name).'\',
		`slot1` = \''.pSQL($slot1).'\',
		`slot2` = \''.pSQL($slot2).'\',
		`customers_max` = '.(int)$customers_max.'
		WHERE `id_planning_delivery_carrier_slot` = '.(int)$id_planning_delivery_slot.' AND
		`id_lang` = '.(int)$id_lang));
	}

	/**
	 * Get slot by Day
	 *
	 * @return array Slot
	 */
	public static function getByDay($id_day, $id_lang, $id_carrier)
	{
		if (!Validate::isUnsignedId($id_day)
			|| !Validate::isUnsignedId($id_lang)
			|| !Validate::isUnsignedId($id_carrier))
			die(Tools::displayError());
		return (Db::getInstance()->ExecuteS('
		SELECT pds.`id_planning_delivery_carrier_slot`, pds.`name`, pds.`slot1`, pds.`slot2`, pds.`customers_max`
		FROM `'._DB_PREFIX_.'planning_delivery_carrier_slot` pds
		INNER JOIN `'._DB_PREFIX_.'planning_delivery_carrier_slot_day` pdsd
		ON pds.`id_planning_delivery_carrier_slot` = pdsd.`id_planning_delivery_carrier_slot`
		WHERE pdsd.`id_day` = '.(int)$id_day.' AND
		pdsd.`id_carrier` = '.(int)$id_carrier.' AND
		pds.`id_lang` = '.(int)$id_lang));
	}

    /**
     * Get slot by Date
     *
     * @return array Slot
     */
    public static function getByDate($date, $id_lang, $id_carrier)
    {
        if (!Validate::isDate($date)
            || !Validate::isUnsignedId($id_lang)
            || !Validate::isUnsignedId($id_carrier))
            die(Tools::displayError());
        return (Db::getInstance()->ExecuteS('
		SELECT pds.`id_planning_delivery_carrier_slot`, pds.`name`, pds.`slot1`, pds.`slot2`, pdsd.`max_places`
		FROM `' . _DB_PREFIX_ . 'planning_delivery_carrier_slot` pds
		INNER JOIN `' . _DB_PREFIX_ . 'planning_delivery_carrier_exception` pdsd
		ON pds.`id_planning_delivery_carrier_slot` = pdsd.`slot_id`
		WHERE \'' . $date . '\' between pdsd.`date_from` and pdsd.`date_to` AND
		pdsd.`id_carrier` = ' . (int)$id_carrier . ' AND
		pds.`id_lang` = ' . (int)$id_lang));
    }

	/**
	 * Get slot by id
	 *
	 * @return array Slot
	 */
	public static function getNameById($id_slot, $id_lang)
	{
		if (!Validate::isUnsignedId($id_slot)
			|| !Validate::isUnsignedId($id_lang))
			die(Tools::displayError());
		$result = Db::getInstance()->getRow('
		SELECT pds.`name`
		FROM `'._DB_PREFIX_.'planning_delivery_carrier_slot` pds
		WHERE pds.`id_planning_delivery_carrier_slot` = '.(int)$id_slot.' AND
		pds.`id_lang` = '.(int)$id_lang);
		if (!isset($result['name']))
			return false;
		return $result['name'];
	}

	/*
	* Get slot by Day And Max Customers
	 *
	 * @return array Slot
	 */
	public static function isFull($date_delivery, $slot, $id_carrier)
	{
		$result = Db::getInstance()->ExecuteS('
			SELECT pd.`id_planning_delivery_carrier_slot`, pd.`date_delivery`
			FROM `'._DB_PREFIX_.'planning_delivery_carrier` pd
			LEFT JOIN `'._DB_PREFIX_.'order_history` oh ON (oh.`id_order` = pd.`id_order`)
			LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = pd.`id_order`)
			WHERE pd.`id_planning_delivery_carrier_slot` = '.(int)$slot['id_planning_delivery_carrier_slot'].'
			AND oh.`id_order_history` = (SELECT MAX(`id_order_history`)
			FROM `'._DB_PREFIX_.'order_history` moh WHERE moh.`id_order` = pd.`id_order` GROUP BY moh.`id_order`)
			AND pd.`date_delivery` = \''.$date_delivery.'\'
			AND o.`id_carrier` = \''.$id_carrier.'\'
			AND oh.`id_order_state` NOT IN (0, 6, 7, 8)
			AND pd.`id_order` != 0');
		if (count($result) >= $slot['max_places'])
			return true;
		return false;
	}

	/**
	 * Get Slots
	 *
	 * @return array Slots
	 */
	public static function get($id_lang)
	{
		if (!Validate::isUnsignedId($id_lang))
			die(Tools::displayError());
		return (Db::getInstance()->ExecuteS('
		SELECT pds.`id_planning_delivery_carrier_slot`, pds.`name`, pds.`slot1`, pds.`slot2`, pds.`customers_max`
		  FROM `'._DB_PREFIX_.'planning_delivery_carrier_slot` pds
		WHERE pds.`id_lang` = '.(int)$id_lang.'
		ORDER BY pds.`name` ASC'));
	}

	/**
	 * Delete slot by Day
	 *
	 * @return boolean succeed
	 */
	public static function deleteByDay($id_day, $id_carrier, $id_lang)
	{
		if (!Validate::isUnsignedId($id_day)
			|| !Validate::isUnsignedId($id_carrier))
			die(Tools::displayError());
		return (Db::getInstance()->Execute('
		DELETE pdcsd.* FROM `'._DB_PREFIX_.'planning_delivery_carrier_slot_day` pdcsd
		LEFT JOIN `'._DB_PREFIX_.'planning_delivery_carrier_slot` pdcs
		ON pdcs.`id_planning_delivery_carrier_slot` = pdcsd.`id_planning_delivery_carrier_slot`
		WHERE `id_day` = '.(int)$id_day.'
		AND `id_carrier` = '.(int)$id_carrier.'
		AND `id_lang` = '.(int)$id_lang));
	}

	/**
	 * Delete all reference of a slot
	 *
	 * @return boolean succeed
	 */
	public static function delete($id_planning_delivery_slot)
	{
		if (!Validate::isUnsignedId($id_planning_delivery_slot))
			die(Tools::displayError());
		$result = Db::getInstance()->Execute('
		DELETE FROM `'._DB_PREFIX_.'planning_delivery_carrier_slot_day`
		WHERE `id_planning_delivery_carrier_slot` = '.(int)$id_planning_delivery_slot);
		if ($result === false)
			return ($result);
		return (Db::getInstance()->Execute('
		DELETE FROM `'._DB_PREFIX_.'planning_delivery_carrier_slot`
		WHERE `id_planning_delivery_carrier_slot` = '.(int)$id_planning_delivery_slot));
	}

	public static function hideSlotsPosition($name)
	{
		return preg_replace('/^[0-9]+\./', '', $name);
	}
};

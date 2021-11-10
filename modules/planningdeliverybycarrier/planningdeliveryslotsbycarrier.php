<?php

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../classes/Validate.php');
require_once(dirname(__FILE__).'/../../classes/Db/Db.php');
require_once(dirname(__FILE__).'/../../classes/Tools.php');
require_once(dirname(__FILE__).'/classes/PlanningDeliverySlotByCarrier.php');
require_once(dirname(__FILE__).'/../../classes/Configuration.php');
require_once(dirname(__FILE__).'/planningdeliverybycarrier.php');
require_once(dirname(__FILE__).'/../../init.php');

$planning_delivery = new PlanningDeliveryByCarrier();

if (empty($_GET['id_lang']) === false)
{
	if (isset($_GET['dateText']) === true)
	{
		if (Validate::isDate($_GET['dateText']))
		{
			$dt = new DateTime($_GET['dateText']);
			$day_number = $dt->format('w');
			if ($day_number == 0)
				$day_number = 7;
			$slots = PlanningDeliverySlotByCarrier::getByDay($day_number, $_GET['id_lang'], $_GET['id_carrier']);
			if (!$_GET['onAdminPlanningDelivery'])
				echo '<label id="lab_delivery_slot" for="id_planning_delivery_slot">';
			else
				echo '<p id="lab_delivery_slot">';
			if (isset($slots) === true && count($slots))
			{
                echo $planning_delivery->l('Time\'s slot').' : ';
				$display_slots = false;
				foreach ($slots as $slot)
					if (!PlanningDeliverySlotByCarrier::isFull($_GET['dateText'], $slot))
						$display_slots = true;
				if ($display_slots)
				{
					echo '<select name="id_planning_delivery_slot" id="id_planning_delivery_slot">';
					echo '<option> - </option>';
					foreach ($slots as $slot)
						if (!PlanningDeliverySlotByCarrier::isFull($_GET['dateText'], $slot))
							echo '<option value="'.(int)$slot['id_planning_delivery_carrier_slot'].'">'.
							htmlspecialchars(PlanningDeliverySlotByCarrier::hideSlotsPosition($slot['name']), ENT_COMPAT, 'UTF-8').'</option>';
					echo '</select>';
				}
			}
			echo (!$_GET['onAdminPlanningDelivery']) ? '</label>' : '</p>';
		}
	}
	elseif (isset($_GET['id_day']) === true)
	{
		$slots = PlanningDeliverySlotByCarrier::get($_GET['id_lang']);
		if ((int)$_GET['id_day'])
			$selects = PlanningDeliverySlotByCarrier::getByDay($_GET['id_day'], $_GET['id_lang'], $_GET['id_carrier']);
		echo '<select name="id_planning_delivery_slot[]" id="id_planning_delivery_slot" multiple="true" style="height:100px;width:360px;">';
		foreach ($slots as $slot)
		{
			echo '<option value="'.(int)$slot['id_planning_delivery_carrier_slot'].'"';
			if (isset($selects) === true && count($selects))
			{
				foreach ($selects as $select)
					if ($select['id_planning_delivery_carrier_slot'] == $slot['id_planning_delivery_carrier_slot'])
						echo ' selected="selected"';
			}
			echo '>'.htmlspecialchars(PlanningDeliverySlotByCarrier::hideSlotsPosition($slot['name']).
			' // max : ('.$slot['customers_max'].')', ENT_COMPAT, 'UTF-8').'</option>';
		}
		echo '</select>';
	}
}

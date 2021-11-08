<?php

require_once(dirname(__FILE__).'/../../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../../init.php');
require_once(dirname(__FILE__).'/../planningdeliverybycarrier.php');

$planning_delivery = new PlanningDeliveryByCarrier();
$date_text = Tools::getValue('dateText');
$format = Tools::getValue('format');
$id_lang = Tools::getValue('id_lang');
$id_carrier = Tools::getValue('id_carrier');
$on_admin_planning_delivery = Tools::getValue('onAdminPlanningDelivery');
$id_day = Tools::getValue('id_day');

if ($id_lang)
{
	if ($date_text)
	{
		$d_format = (1 == $format) ? 'd/m/Y' : 'm/d/Y';
		$date_text = $planning_delivery->X_dateformat($date_text, $d_format, 'Y-m-d');		
		if (Validate::isDate($date_text))
		{
			$slots = PlanningDeliverySlotByCarrier::getByDate($date_text, $id_lang, $id_carrier);
			if (!$on_admin_planning_delivery) echo '<label id="lab_delivery_slot" for="id_planning_delivery_slot">';
			else echo '<p id="lab_delivery_slot">';
			echo $planning_delivery->l('Time\'s slot', 'PlanningDeliveryByCarrier').' : ';
			if (count($slots) > 0)
			{
				$display_slots = false;
				foreach ($slots as $slot)
				{
					$slot1 = str_replace($planning_delivery->datetime_simu, date('Y-m-d '), $slot['slot1']);
					if (!PlanningDeliverySlotByCarrier::isFull($date_text, $slot, $id_carrier)
						&& (date('Y-m-d') != $date_text || time() < strtotime($slot1)))
						$display_slots = true;
				}
				if ($display_slots)
				{
					echo '<select name="id_planning_delivery_slot" id="id_planning_delivery_slot">';
					foreach ($slots as $slot)
						if (!PlanningDeliverySlotByCarrier::isFull($date_text, $slot, $id_carrier))
							echo '<option value="'.(int)$slot['id_planning_delivery_carrier_slot'].'" ' . ($slot['id_planning_delivery_carrier_slot'] == $slots[0]['id_planning_delivery_carrier_slot']? 'selected':'') . '>'.
							htmlspecialchars(PlanningDeliverySlotByCarrier::hideSlotsPosition($slot['name']), ENT_COMPAT, 'UTF-8').'</option>';
					echo '</select>';
				}
				else
					echo '<br /><br /><span class="error">'.
					$planning_delivery->l('No slot is available for the dates selected.', 'PlanningDeliveryByCarrier').'</span>';
			}
			else
				echo '<br /><br /><span class="error">'.
				$planning_delivery->l('No slot is available for the dates selected.', 'PlanningDeliveryByCarrier').'</span>';
			echo (!$on_admin_planning_delivery) ? '</label>' : '</p>';
		}
	}
	elseif ($id_day)
	{
		$slots = PlanningDeliverySlotByCarrier::get((int)$id_lang);
		$selects = PlanningDeliverySlotByCarrier::getByDay((int)$id_day, (int)$id_lang, (int)$id_carrier);
		echo '<select name="id_planning_delivery_slot[]" id="id_planning_delivery_slot" multiple="true" style="height:100px;width:360px;">';
		if (count($slots) > 0)
		{
			foreach ($slots as $slot)
			{
				echo '<option value="'.(int)$slot['id_planning_delivery_carrier_slot'].'"';
				if (count($selects) > 0)
				{
					foreach ($selects as $select)
						if ($select['id_planning_delivery_carrier_slot'] == $slot['id_planning_delivery_carrier_slot'])
							echo ' selected="selected"';
				}
				echo '>'.htmlspecialchars(PlanningDeliverySlotByCarrier::hideSlotsPosition($slot['name']).
				' // max : ('.$slot['customers_max'].')', ENT_COMPAT, 'UTF-8').'</option>';
			}
		}
		echo '</select>';
	}
}

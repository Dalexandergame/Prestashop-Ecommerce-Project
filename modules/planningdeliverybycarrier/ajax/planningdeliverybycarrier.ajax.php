<?php

include(dirname(__FILE__).'/../../../config/config.inc.php');
include(dirname(__FILE__).'/../planningdeliverybycarrier.php');
//include(dirname(__FILE__).'/../planningdeliverybycarrier/planningdeliverybycarrier.php');

$planning_deliverybycarrier = new PlanningDeliveryByCarrier();

if (Tools::getValue('reset'))
	$planning_deliverybycarrier->resetDateDelivery((int)$_GET['id_cart']);
elseif (Tools::getValue('submitDateDelivery'))
	echo $planning_deliverybycarrier->ajaxUpdate();
else
{
	$id_carrier = Tools::getValue('id_carrier');
	echo $planning_deliverybycarrier->includeDatepicker('date_delivery', false, 1, 0, $id_carrier);
	echo $planning_deliverybycarrier->includeDatepickerRetour('date_retour', false, 1, 0, $id_carrier);
}

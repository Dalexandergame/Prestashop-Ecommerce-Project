<?php


include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/planningdeliverybycarrier.php');

$planning_deliverybycarrier = new PlanningDeliveryByCarrier();

$id_carrier = isset($_GET['id_carrier']) ? $_GET['id_carrier'] : 0;

echo $planning_deliverybycarrier->includeDatepicker('date_delivery', false, 0, $id_carrier);


?>

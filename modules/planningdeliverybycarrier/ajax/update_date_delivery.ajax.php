<?php

require_once(dirname(__FILE__).'/../../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../../init.php');
//require_once(dirname(__FILE__).'/../planningdeliverybycarrier.php');
require_once(dirname(__FILE__).'/../classes/PlanningDeliveryByCarrierException.php');
require_once(dirname(__FILE__).'/../classes/PlanningDeliveriesByCarrier.php');
require_once(dirname(__FILE__).'/../planningdeliverybycarrier.php');
require_once(_PS_THEME_DIR_.'modules/planningdeliverybycarrier/planningdeliverybycarrier.php');
require_once(dirname(__FILE__).'/../classes/PlanningDeliverySlotByCarrier.php');

$planning_delivery = new PlanningDeliveryByCarrier();
echo $planning_delivery->ajaxUpdateAdminOrder($_GET['id_order']);

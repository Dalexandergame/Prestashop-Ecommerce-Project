<?php

if (!defined('_CAN_LOAD_FILES_'))
    exit;

require_once(dirname(__FILE__) . '/classes/PlanningDeliveriesByCarrier.php');
require_once(dirname(__FILE__) . '/classes/PlanningDeliverySlotByCarrier.php');
require_once(dirname(__FILE__) . '/classes/PlanningDeliveryByCarrierException.php');
require_once(dirname(__FILE__) . '/classes/PlanningRetourByCarrierException.php');
require_once(dirname(__FILE__) . '/controllers/admin/AdminPlanningDeliveryByCarrierController.php');
require_once(dirname(__FILE__) . '/controllers/admin/AdminPlanningDeliveryByCarrierController.php');

class PlanningDeliveryByCarrier extends Module
{
    const INSTALL_SQL_FILE          = 'install.sql';
    const TODAY_DELIVERY_ID_CARRIER = 3;

    protected $_html         = '';
    protected $_post_errors  = array();
    protected $id_carrier    = 0;
    public    $datetime_simu = '2012-01-01 ';
    /** @var string Heure butoire pour la journée (ex. format: 17h00) */
    public    $today_max_hour = '23h59';
    protected $days           = array(
        '1' => 'Monday',
        '2' => 'Tuesday',
        '3' => 'Wednesday',
        '4' => 'Thursday',
        '5' => 'Friday',
        '6' => 'Saturday',
        '7' => 'Sunday'
    );
    protected $_defaultConfig = array(
        'PLANNING_DELIVERY_REQUIRED'    => 1,
        'PLANNING_DELIVERY_SLOT'        => 1,
        'PLANNING_DELIVERY_DAYS_BEFORE' => 0,
        'PLANNING_DELIVERY_NUMB_DAYS'   => 30,
        'PLANNING_DELIVERY_UNAV_DAYS'   => '6, 7'
    );
    protected $delimiters     = array(1 => ';', 2 => ', ');
    protected $enclosures     = array(1 => '"', 2 => '\'');

    public function __construct()
    {
        $this->name          = 'planningdeliverybycarrier';
        $this->tab           = 'shipping_logistics';
        $this->version       = 1.6;
        $this->author        = 'Speedyweb';
        $this->need_instance = 1;
        $this->module_key    = 'fb57700f4127432af9294723fceb115c';
        $this->bootstrap     = false;
        parent::__construct();
        $this->displayName = $this->l('Planning Delivery By Carrier');
        $this->description = $this->l('Allows users to choose the date and time of delivery');
        $this->initL();
    }

    public function install()
    {
        if (!$this->getTabId('AdminPlanningDeliveryByCarrier', 'planningdeliverybycarrier')) $this->addTab();

        if (!parent::install()
            || !$this->registerHook('header')
            || !$this->registerHook('displayAfterCarrier')
            || !$this->addTabPlanningDeliveryByCarrier()
            || !$this->addTabPlanningRetourByCarrier()
            || !$this->addTabPlanningDeliveryByCarrierMyLittel()
            || !$this->registerHook('actionCarrierProcess')
            || !$this->registerHook('orderProcessCarrier')
            || !$this->registerHook('newOrder')
            || !$this->registerHook('displayPDFInvoice')
            || !$this->registerHook('displayPDFDeliverySlip')
            || !$this->registerHook('backOfficeHeader')
            || !$this->registerHook('displayAdminOrder')
            || !$this->registerHook('displayAdminHomeStatistics')
            || ($this->checkVersion() > '1.5' && !$this->registerHook('dashboardZoneTwo'))
            || !$this->registerHook('export')
            || !Configuration::updateValue('PLANNING_DELIVERY_HOMEBACKOFFICE', 1)
            || !Configuration::updateValue('PLANNING_DELIVERY_CARRIERS', '')
            || !Configuration::updateValue('PLANNING_DELIVERY_UNAV_OSS', '0, 5, 6, 7, 8, 9'))
            return false;
        return true;
    }

    public function uninstall()
    {
        $this->deleteTab();
        return parent::uninstall();
    }

    public function addTab()
    {
        @copy(_PS_ROOT_DIR_ . '/modules/planningdeliverybycarrier/logo.gif', _PS_ROOT_DIR_ . '/img/t/AdminPlanningDeliveryByCarrier.gif');
        $tab             = new Tab();
        $tab->class_name = 'AdminPlanningDeliveryByCarrier';
        $tab->module     = 'planningdeliverybycarrier';
        $tab->id_parent  = Tab::getIdFromClassName('AdminParentShipping');
        $langs           = Language::getLanguages();
        foreach ($langs as $l) {
            if ($l['iso_code'] == 'fr') $tab->name[$l['id_lang']] = utf8_encode('Planning de livraisons');
            else $tab->name[$l['id_lang']] = 'Planning delivery';
        }
        return $tab->add();
    }

    public function getTabId($className, $module)
    {
        $row = Db::getInstance()->getRow('SELECT `id_tab` FROM ' . _DB_PREFIX_ . 'tab WHERE `class_name` = "' . $className . '" AND `module` = "' . $module . '"');
        return ($row ? $row['id_tab'] : false);
    }

    public function deleteTab()
    {
        @unlink(_PS_ROOT_DIR_ . '/img/t/planningdeliverybycarrier.gif');
        $idTab = $this->getTabId('AdminPlanningDeliveryByCarrier', 'planningdeliverybycarrier');
        if ($idTab) {
            Db::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'tab WHERE `id_tab` = ' . (int) ($idTab));
            Db::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'tab_lang WHERE `id_tab` = ' . (int) ($idTab));
        }
    }

    /***************************************************************************************************************/

    public function getContent()
    {
        $this->_checkCarrierGoToSettings();
        if ($this->id_carrier > 0) {
            $this->_displayQuickLinks();
            $this->_checkPlanningDeliveryRequired();
            $this->_checkAvailableDays();
            if (Configuration::get('PLANNING_DELIVERY_SLOT_' . $this->id_carrier)) {
                $this->_checkSlot();
                $this->_checkSlotDay();
            }
            return $this->_html . $this->_displayFormCarrier();
        } else {
            $this->id_carrier = 0;
            $this->_displayQuickLinks();
            $this->_checkCarriersGlobalSettings();
            $this->_checkException();
            return $this->_html . $this->_displayFormGlobal();
        }
    }

    protected function _displayFormGlobal()
    {
        $this->_displayFormCarrierGoToSettings();
        $this->_displayFormCarriersGlobalSettings();
        $this->_displayFormException();
        $this->_displayRetourFormException();
        $this->_displayInformation();
        return ($this->_html);
    }

    protected function _displayFormCarrier()
    {
        $this->_displayFormCarrierGoToSettings();
        $this->_displayFormGlobalSettings();
        $this->_displayFormAvailablesDays();
        if (Configuration::get('PLANNING_DELIVERY_SLOT_' . $this->id_carrier)) {
            $this->_displayFormSlots();
            $this->_displayFormDaySlot();
        }
        return ($this->_html);
    }

    protected function _checkCarrierGoToSettings()
    {
        if (Tools::isSubmit('subIdCarrier'))
            $this->id_carrier = (int) (Tools::getValue('subIdCarrier'));
        elseif (Tools::isSubmit('submitCarrierGoToSettings'))
            $this->id_carrier = (int) (Tools::getValue('goToCarrier'));
        elseif (Tools::isSubmit('submitReturnToGlobalSettings'))
            $this->id_carrier = 0;
    }

    protected function _checkCarriersGlobalSettings()
    {
        if (Tools::isSubmit('submitCarriersGlobalSettings')) {
            $planning_deliveryCarriers       = array_flip(Tools::getValue('carriersBox'));
            $planning_deliveryCarriersString = '';
            $delim                           = '';
            foreach ($planning_deliveryCarriers as $idCarrier => $carrier) {
                $planning_deliveryCarriersString .= $delim . $idCarrier;
                $delim                           = ', ';
            }
            Configuration::updateValue('PLANNING_DELIVERY_CARRIERS', $planning_deliveryCarriersString);
            $activeHookHomeBackOffice = Tools::getValue('active_hook_home_back_office');
            Configuration::updateValue('PLANNING_DELIVERY_HOMEBACKOFFICE', (int) ($activeHookHomeBackOffice));
            $unavailableOSs       = Tools::getValue('osPlanning');
            $unavailableOSsString = '0';
            foreach ($unavailableOSs as $idUnavailableOS)
                $unavailableOSsString .= ', ' . $idUnavailableOS;
            Configuration::updateValue('PLANNING_DELIVERY_UNAV_OSS', $unavailableOSsString);
            $this->_html .= '<div class="conf confirm">' . $this->l('Settings updated') . '</div>';
        }
    }

    protected function _checkPlanningDeliveryRequired()
    {
        if (Tools::isSubmit('submitPlanningRequired')) {
            $planningRequired = Tools::getValue('planning_required');
            if ((int) ($planningRequired) != 0) $planningRequired = 1;
            $activeSlot = Tools::getValue('active_slot');
            if ((int) ($activeSlot) != 0) $activeSlot = 1;
            Configuration::updateValue('PLANNING_DELIVERY_REQUIRED_' . $this->id_carrier, (int) ($planningRequired));
            Configuration::updateValue('PLANNING_DELIVERY_SLOT_' . $this->id_carrier, (int) ($activeSlot));
            Configuration::updateValue('PLANNING_DELIVERY_DAYS_BEFORE' . $this->id_carrier, (int) (Tools::getValue('days_before_first_date')));
            Configuration::updateValue('PLANNING_DELIVERY_NUMB_DAYS' . $this->id_carrier, (int) (Tools::getValue('number_of_days')));
            $this->_html .= '<div class="conf confirm">' . $this->l('Settings updated') . '</div>';
        }
    }

    protected function _checkAvailableDays()
    {
        if (Tools::isSubmit('submitAvailableDays')) {
            $availableDays = array_flip(Tools::getValue('daysBox'));
            if ($availableDays == null) {
                $availableDays = array();
                $this->_html   .= 'null';
            }
            $unavailableDays       = array_diff_key($this->days, $availableDays);
            $unavailableDaysString = '';
            $delim                 = '';
            foreach ($unavailableDays as $idUnavailableDay => $unavailableDay) {
                $unavailableDaysString .= $delim . $idUnavailableDay;
                $delim                 = ', ';
            }
            Configuration::updateValue('PLANNING_DELIVERY_UNAV_DAYS' . $this->id_carrier, $unavailableDaysString);
            $this->_html .= '<div class="conf confirm">' . $this->l('Settings updated') . '</div>';
        }
    }

    protected function _checkSlot()
    {
        $action_slot   = Tools::getValue('slot_action');
        $name          = Tools::getValue('slot');
        $slot1         = $this->datetime_simu . str_replace($this->l('h'), ':', Tools::getValue('slot1')) . ':00';
        $slot2         = $this->datetime_simu . str_replace($this->l('h'), ':', Tools::getValue('slot2')) . ':00';
        $customers_max = Tools::getValue('max_customers');
        if (Tools::isSubmit('submitSlot') && empty($action_slot) && !empty($name)) {
            PlanningDeliverySlotByCarrier::add((int) $this->context->language->id, $name, $slot1, $slot2, (int) ($customers_max));
            $this->_html .= '<div class="conf confirm">' . $this->l('Settings updated') . '</div>';
        } elseif (!empty($action_slot) && empty($name)) {
            $id_planning_delivery_slot = Tools::getValue('id_planning_delivery_slot');
            switch ($action_slot) {
                case 'edit':
                    PlanningDeliverySlotByCarrier::update($id_planning_delivery_slot,
                        Tools::getValue('slot_id_lang'),
                        Tools::getValue('slot_name'),
                        $this->datetime_simu . str_replace($this->l('h'), ':', Tools::getValue('slot_slot1')) . ':00',
                        $this->datetime_simu . str_replace($this->l('h'), ':', Tools::getValue('slot_slot2')) . ':00',
                        Tools::getValue('slot_customers_max')
                    );
                    $this->_html .= '<div class="conf confirm">' . $this->l('Settings updated') . '</div>';
                    break;
                case 'delete':
                    PlanningDeliverySlotByCarrier::delete($id_planning_delivery_slot);
                    $this->_html .= '<div class="conf confirm">' . $this->l('Settings updated') . '</div>';
                    break;
                default:
                    break;
            }
        }
    }

    protected function _checkSlotDay()
    {
        if (Tools::isSubmit('submitSlotDay')) {
            $id_planning_delivery_slots = Tools::getValue('id_planning_delivery_slot');
            $id_day                     = Tools::getValue('id_day');
            PlanningDeliverySlotByCarrier::deleteByDay($id_day, $this->id_carrier, (int) $this->context->language->id);
            if (empty($id_planning_delivery_slots) === false)
                foreach ($id_planning_delivery_slots as $id_planning_delivery_slot)
                    PlanningDeliverySlotByCarrier::addToDay($id_planning_delivery_slot, $id_day, $this->id_carrier);
            $this->_html .= '<div class="conf confirm">' . $this->l('Settings updated') . '</div>';
        }
    }

    protected function _checkException()
    {
        $action_exception        = Tools::getValue('exception_action');
        $retour_action_exception = Tools::getValue('retour_exception_action');
        $dateFrom                = Tools::getValue('date_from');
        $dateTo                  = Tools::getValue('date_to');
        $maxPlaces               = Tools::getValue('max_places');
        $id_carrier              = Tools::getValue('id_carrier');
        if (Tools::isSubmit('submitException') && empty($action_exception) && !empty($dateFrom)) {
            PlanningDeliveryByCarrierException::add($dateFrom, (empty($dateTo) ? $dateFrom : $dateTo), $maxPlaces, $id_carrier);
            $this->_html .= '<div class="conf confirm">' . $this->l('Settings updated') . '</div>';
        }
        if (Tools::isSubmit('submitRetourException') && empty($action_exception) && !empty($dateFrom)) {
            PlanningRetourByCarrierException::add($dateFrom, (empty($dateTo) ? $dateFrom : $dateTo), $maxPlaces, $id_carrier);
            $this->_html .= '<div class="conf confirm">' . $this->l('Settings updated') . '</div>';
        } elseif (!empty($retour_action_exception) && empty($dateFrom)) {
            $id_planning_retour_carrier_exception = Tools::getValue('id_planning_retour_carrier_exception');
            switch ($retour_action_exception) {
                case 'delete':
                    PlanningRetourByCarrierException::delete($id_planning_retour_carrier_exception);
                    $this->_html .= '<div class="conf confirm">' . $this->l('Settings updated') . '</div>';
                    break;
                default:
                    break;
            }
        } elseif (!empty($action_exception) && empty($dateFrom)) {
            $id_planning_delivery_carrier_exception = Tools::getValue('id_planning_delivery_carrier_exception');
            switch ($action_exception) {
                case 'delete':
                    PlanningDeliveryByCarrierException::delete($id_planning_delivery_carrier_exception);
                    $this->_html .= '<div class="conf confirm">' . $this->l('Settings updated') . '</div>';
                    break;
                default:
                    break;
            }
        }
    }

    /****** HOOKS *************************************************************************************************/

    public function hookHeader($params)
    {
        $this->context->controller->registerJavascript('ui-core-js', _PS_JS_DIR_ . 'jquery/ui/jquery.ui.core.min.js', ['position' => 'bottom', 'priority' => 80]);
        $this->context->controller->registerJavascript('date-picker', _PS_JS_DIR_ . 'jquery/ui/jquery.ui.datepicker.min.js', ['position' => 'bottom', 'priority' => 100]);
        $this->context->controller->registerJavascript('order-opc', _THEME_JS_DIR_ . 'order-opc.js', ['position' => 'bottom', 'priority' => 50]);
        $this->context->controller->registerJavascript('bootstrap-editable', _PS_JS_DIR_ . 'bootstrap-editable.min.js', ['position' => 'bottom', 'priority' => 80]);
        $this->smarty->assign('path_pd', __PS_BASE_URI__);
        return $this->display(__FILE__, 'header.tpl');
    }

    public function hookDisplayAdminHomeStatistics($params)
    {
        if (Configuration::get('PLANNING_DELIVERY_HOMEBACKOFFICE')) {
            $weekListGroupByDay    = array();
            $adminPlanningdelivery = new AdminPlanningDeliveryByCarrierController();
            $adminPlanningdelivery->getWeekList();
            if (count($adminPlanningdelivery->_weekList)) {
                foreach ($adminPlanningdelivery->_weekList as $delivery) $weekListGroupByDay[Tools::ucfirst($this->dateFR_S($delivery['date_delivery']))][$delivery['pdsname']][] = $delivery;
                $this->smarty->assign(array(
                        'displayWeekList'    => true,
                        'weekListGroupByDay' => $weekListGroupByDay,
                        'identifier'         => $this->identifier,
                        'orderToken'         => Tools::getAdminTokenLite('AdminOrders'),
                        'PS_SHOP_NAME'       => Configuration::get('PS_SHOP_NAME')
                    )
                );
                return $this->display(__FILE__, '_homeWeeklist.tpl');
            }
        }
    }

    public function hookDashboardZoneTwo($params)
    {
        if (Configuration::get('PLANNING_DELIVERY_HOMEBACKOFFICE')) {
            $weekListGroupByDay = array();
            $_weekList          = AdminPlanningDeliveryByCarrierController::getHomeWeekList();
            if (count($_weekList)) {
                foreach ($_weekList as $delivery) $weekListGroupByDay[Tools::ucfirst($this->dateFR_S($delivery['date_delivery']))][$delivery['pdsname']][] = $delivery;
                $this->smarty->assign(array(
                        'displayWeekList'    => true,
                        'weekListGroupByDay' => $weekListGroupByDay,
                        'identifier'         => $this->identifier,
                        'orderToken'         => Tools::getAdminTokenLite('AdminOrders'),
                        'PS_SHOP_NAME'       => Configuration::get('PS_SHOP_NAME')
                    )
                );
                return $this->display(__FILE__, '_homeWeeklist.tpl');
            }
        }
    }

    public function hookDisplayAdminOrder($params)
    {
        $order              = new Order($params['id_order']);
        $dateRetourRequired = $this->dateRetourRequired($order->id_cart);
        $includeDatepicker  = $this->includeDatepicker('date_delivery', false, 1, 1, $order->id_carrier);
        $includeDatepicker  .= $this->includeDatepickerRetour('date_retour', false, 1, 1, $order->id_carrier);
        $result             = Db::getInstance()->getRow('
                SELECT pd.`date_delivery`, pds.`name`, pd.`date_retour`
                FROM `' . _DB_PREFIX_ . 'planning_delivery_carrier` pd
                LEFT JOIN `' . _DB_PREFIX_ . 'planning_delivery_carrier_slot` pds
                ON pd.`id_planning_delivery_carrier_slot` = pds.`id_planning_delivery_carrier_slot`
                WHERE pd.`id_cart` = ' . $order->id_cart
        )
        ;
        $this->smarty->assign('order', $order);
        $this->smarty->assign('date_delivery', $result['date_delivery']);
        $this->smarty->assign('date_retour', $result['date_retour']);
        $this->smarty->assign('delivery_slot', PlanningDeliverySlotByCarrier::hideSlotsPosition($result['name']));
        $this->smarty->assign('includeDatepicker', $includeDatepicker);
        $this->smarty->assign('dateRetourRequired', $dateRetourRequired);
        return $this->display(__FILE__, 'admin-order.tpl');
    }

    public function ajaxUpdateAdminOrder($id_order)
    {
        $order         = new Order($id_order);
        $format        = 1;
        $date_retour   = Tools::getValue('date_retour');
        $date_delivery = Tools::getValue('date_delivery');
        $errors        = $this->requestProcessDateDelivery($order->id_cart, $order->id_carrier, Tools::getValue('date_delivery'), Tools::getValue('id_planning_delivery_slot'), $date_retour, $format);
        $values        = array('id_order' => $id_order);
        if ($format && !empty($date_delivery)) {
            $dFormat       = (1 == $format) ? 'd/m/Y' : 'm/d/Y';
            $date_delivery = $this->X_dateformat($date_delivery, $dFormat, 'Y-m-d');
        }
        if ($format && !empty($date_retour)) {
            $dFormat     = (1 == $format) ? 'd/m/Y' : 'm/d/Y';
            $date_retour = $this->X_dateformat($date_retour, $dFormat, 'Y-m-d');
        }
        if (Validate::isDate($date_retour)) {
            $values['date_retour'] = $date_retour;
        }
        if (Validate::isDate($date_delivery)) {
            $values['date_delivery'] = $date_delivery;
        }

        $values['date_upd'] = date('Y-m-d H:i:s');
        $oldRow             = Db::getInstance()->executeS("select * from ps_planning_delivery_carrier where id_order = " . $order->id);

        if (count($oldRow)) {
            Db::getInstance()->update('planning_delivery_carrier', $values, '`id_order`=' . (int) $order->id);
        } else {
            $values['id_order'] = $order->id;
            $values['id_cart']  = $order->id_cart;
            $values['date_add'] = date('Y-m-d H:i:s');
            Db::getInstance()->insert('planning_delivery_carrier', $values);
        }

        if (count($errors))
            $this->smarty->assign('pderrors', $errors);

        $result = Db::getInstance()->getRow('
                SELECT pd.`date_delivery`, pds.`name`, pd.`date_retour`
                FROM `' . _DB_PREFIX_ . 'planning_delivery_carrier` pd
                LEFT JOIN `' . _DB_PREFIX_ . 'planning_delivery_carrier_slot` pds
                ON pd.`id_planning_delivery_carrier_slot` = pds.`id_planning_delivery_carrier_slot`
                WHERE pd.`id_order` = ' . $order->id
        )
        ;
        $this->smarty->assign('order', $order);
        $this->smarty->assign('date_delivery', $result['date_delivery']);
        $this->smarty->assign('date_retour', $result['date_retour']);
        $this->smarty->assign('delivery_slot', PlanningDeliverySlotByCarrier::hideSlotsPosition($result['name']));
        return $this->display(__FILE__, 'ajax-update-admin-order.tpl');
    }

    public function ajaxUpdate($format = null)
    {
        $format = ($format) ? $format : Tools::getValue('format');
//        die("test");
        $this->requestProcessDateDelivery(Tools::getValue('id_cart'), Tools::getValue('id_carrier'), Tools::getValue('date_delivery'), Tools::getValue('id_planning_delivery_slot'), Tools::getValue('date_retour', null), Tools::getValue('format'));
    }

    public function ajaxProcessUpdatenbcommande()
    {
        $id_planning_delivery_carrier_exception = (int) Tools::getValue("id_planning_delivery_carrier_exception");
        $nb_commandes                           = (int) Tools::getValue("nb_commande");
        $success                                = PlanningDeliveryByCarrierException::updateNbCommande($id_planning_delivery_carrier_exception, $nb_commandes);
        if ($success) {
            $return = array('msg' => "la modification a bien été effectuée", 'success' => true);
        } else {
            $return = array('msg' => "erreur d'enregistrement", 'success' => false);
        }

        echo json_encode($return);
        return true;
    }

    public function ajaxProcessUpdatenbcommanderetour()
    {
        $id_planning_retour_carrier_exception = (int) Tools::getValue("id_planning_retour_carrier_exception");
        $nb_commandes                         = (int) Tools::getValue("nb_commande");
        $success                              = PlanningRetourByCarrierException::updateNbCommande($id_planning_retour_carrier_exception, $nb_commandes);
        if ($success) {
            $return = array('msg' => "la modification a bien été effectuée", 'success' => true);
        } else {
            $return = array('msg' => "erreur d'enregistrement", 'success' => false);
        }

        echo json_encode($return);
        return true;
    }

    public function resetDateDelivery($id_cart)
    {
        if (Validate::isUnsignedId($id_cart))
            return Db::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'planning_delivery_carrier WHERE `id_cart` = ' . (int) ($id_cart));
    }

    public function requestProcessDateDelivery($id_cart, $id_carrier, $date_delivery, $id_planning_delivery_slot, $date_retour = null, $format = false)
    {

        $errors            = $carriers_ids = array();
        $availableCarriers = explode(', ', Configuration::get('PLANNING_DELIVERY_CARRIERS'));
        foreach ($availableCarriers as $carrier) {
            $carriers_ids[] = $carrier;
        }

        if (in_array($id_carrier, $carriers_ids)) {
            $dateDeliveryRequired = Configuration::get('PLANNING_DELIVERY_REQUIRED_' . $id_carrier);
            $slotRequired         = Configuration::get('PLANNING_DELIVERY_SLOT_' . $id_carrier);

            $result = Db::getInstance()->getRow('
                        SELECT pd.`id_planning_delivery_carrier` as id_planning FROM `' . _DB_PREFIX_ . 'planning_delivery_carrier` pd WHERE pd.`id_cart` = ' . (int) ($id_cart)
            )
            ;

            $planning_delivery = (!$result) ? new PlanningDeliveriesByCarrier() : new PlanningDeliveriesByCarrier((int) ($result['id_planning']));

            $planning_delivery->id_cart                           = (int) ($id_cart);
            $planning_delivery->date_delivery                     = '';
            $planning_delivery->date_retour                       = null;
            $planning_delivery->id_planning_delivery_carrier_slot = 0;

            /* Date de livraison */
            if ($format && !empty($date_delivery)) {
                $dFormat       = (1 == $format) ? 'd/m/Y' : 'm/d/Y';
                $date_delivery = $this->X_dateformat($date_delivery, $dFormat, 'Y-m-d');
            }
            if (Validate::isDate($date_delivery)) {
                $planning_delivery->date_delivery = $date_delivery;
            } else {
                $errors[] = Tools::displayError($this->l('Delivery Date invalid'));
            }

            /* Créneau horaire */
            if ($slotRequired) {
                if (Validate::isUnsignedId($id_planning_delivery_slot)) $planning_delivery->id_planning_delivery_carrier_slot = $id_planning_delivery_slot;
                elseif ($dateDeliveryRequired) $errors[] = Tools::displayError($this->l('Invalid slot'));
            }
            /* Date de retour */
            $planning_delivery->test_date = $date_retour;
          //  if ($this->dateRetourRequiredByCart($planning_delivery->id_cart)) {
//                d($planning_delivery);
                if ($format && !empty($date_retour) && strlen($date_retour) >= 10 && $date_retour != 'undefined') {
                    $dFormat     = (1 == $format) ? 'd/m/Y' : 'm/d/Y';
                    $date_retour = $this->X_dateformat($date_retour, $dFormat, 'Y-m-d');
                    if (Validate::isDate($date_retour)) {
                        $planning_delivery->date_retour = $date_retour;
                    } else {
                        $errors[] = Tools::displayError($this->l('Date de retour invalid ' . $date_retour));
                    }
                } else {
                    $errors[] = Tools::displayError($this->l('Date de retour indéfini'));
                }
         //   }

//            d($planning_delivery);
            //todo planning insert code
            if (!empty($date_delivery)) $maj = (!$result) ? $planning_delivery->add() : $planning_delivery->update();
            /* TO DO: check this error if (!$maj) */
        }
        return $errors;
    }

    public function dateRetourRequiredByCart($id_cart)
    {

        $cart = new Cart($id_cart);
//        d($cart);
        $npa = (int) $cart->npa;
        // TODO:  adil => test si la date de retour est obilgatoir ou non

        if (!class_exists("Region")) {
            require_once(_PS_MODULE_DIR_ . '/gszonevente/models/Region.php');
        }
        if (!class_exists("Front")) {
            require_once(_PS_MODULE_DIR_ . '/tunnelvente/controllers/front/Front.php');
        }

        $region = Region::getRegionByNpa($npa);
        if (empty($region)) {
            $region = array('id_carrier' => Configuration::get('TUNNELVENTE_ID_CARRIER_POST'));// transporteur Post Si npa n'existe pas
        }


        $id_carrier_post = (int) Configuration::get('TUNNELVENTE_ID_CARRIER_POST');
        if ($cart) {
            foreach ($cart->getProducts() as $product) {
                if ($this->testIfRecyclage($product['id_product']) && (int) $region['id_carrier'] != $id_carrier_post)
                    return true;
            }
        }
        return false;
    }

    public static function checkVersion()
    {
        return _PS_VERSION_;
    }

    public function hookDisplayAfterCarrier($params)
    {

        $errors = array();

        $address = new Address((int) ($params['cart']->id_address_delivery));
        $country = new Country((int) ($address->id_country));
        $format  = ('US' != $country->iso_code) ? 1 : 2;

        $datepickerJs = $this->includeDatepicker('date_delivery', false, $format, 0);
        $datepickerJs .= $this->includeDatepickerRetour('date_retour', false, $format, 0);
        $opc          = Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order';

        $dFormat            = (1 == $format) ? 'd/m/Y' : 'm/d/Y';
        $dateRetourRequired = $this->dateRetourRequired();

        $result = Db::getInstance()->getRow('
			SELECT pd.`date_delivery`, pd.`date_retour`, pds.`name`, pd.`id_planning_delivery_carrier_slot` as id_slot
			FROM `' . _DB_PREFIX_ . 'planning_delivery_carrier` pd
			LEFT JOIN `' . _DB_PREFIX_ . 'planning_delivery_carrier_slot` pds
			ON pd.`id_planning_delivery_carrier_slot` = pds.`id_planning_delivery_carrier_slot`
			WHERE pd.`id_cart` = ' . (int) $this->context->cookie->id_cart
        )
        ;

//        d($result);
        if ($result) {
            if (Validate::isDate($result['date_delivery'])) {
                // Vérifier si la date est passée ou non, si elle est passée il faut la supprimer
                // resetDateDelivery($id_cart)
                $slotsAvalaibles = $this->getSlotsAvalaiblesByDateAndCarrier($params['cart']->id_carrier, $result['date_delivery'], (int) $this->context->language->id);
                if (!empty($result['date_delivery'])) {
                    $date_delivery = $this->X_dateformat($result['date_delivery'], 'Y-m-d', $dFormat);
                }
                $this->smarty->assign('date_delivery', $date_delivery);
                $this->smarty->assign('slotsAvalaibles', $slotsAvalaibles);
            } else
                $errors[] = Tools::displayError($this->l('Delivery Date invalid'));

            $slotRequired = Configuration::get('PLANNING_DELIVERY_SLOT_' . $params['cart']->id_carrier);
            if (Validate::isInt($result['id_slot']) && $result['id_slot'] > 0)
                $this->smarty->assign('delivery_slot', $result['id_slot']);
            elseif ($slotRequired)
                $errors[] = Tools::displayError($this->l('Invalid slot'));

            /* Date de retour */

            if ($dateRetourRequired) {
                if (Validate::isDate($result['date_retour'])) {
                    if (!empty($result['date_retour'])) {
                        $date_retour = $this->X_dateformat($result['date_retour'], 'Y-m-d', $dFormat);
                    }
                    $this->smarty->assign('date_retour', $date_retour);
                } else {
                    $errors[] = Tools::displayError($this->l('Date de retour invalid'));
                }
            }

            if (count($errors))
                $this->smarty->assign('pderrors', $errors);
        }
//        var_dump($this->context->cart->getProducts());
        $this->smarty->assign('dateRetourRequired', $dateRetourRequired);
        $this->smarty->assign('slotRequired', Configuration::get('PLANNING_DELIVERY_SLOT_' . $params['cart']->id_carrier));
        $this->smarty->assign('opc', $opc);
        $this->smarty->assign('format', $format);
        $this->smarty->assign('datepickerJs', $datepickerJs);
        $this->smarty->assign('id_cart', (int) $this->context->cookie->id_cart);
        $this->smarty->assign('ps_version', (int) (sprintf('%0-6s', str_replace('.', '', (string) (_PS_VERSION_)))));
        $this->smarty->assign('path', __PS_BASE_URI__ . 'modules/planningdeliverybycarrier/');
        $this->smarty->assign('base_dir', __PS_BASE_URI__);
        $this->smarty->assign('id_carrier_post', (int) Configuration::get('TUNNELVENTE_ID_CARRIER_POST'));
        return $this->display(__FILE__, 'order-carrier.tpl');
    }

    public function dateRetourRequired()
    {

        $cart = $this->context->cart;
        $npa  = $this->context->cookie->npa;
//        d($npa);
        // TODO:  adil => test si la date de retour est obilgatoir ou non

        if (!class_exists("Region")) {
            require_once(_PS_MODULE_DIR_ . '/gszonevente/models/Region.php');
        }
        if (!class_exists("Front")) {
            require_once(_PS_MODULE_DIR_ . '/tunnelvente/controllers/front/Front.php');
        }

        $region = Region::getRegionByNpa($npa);
//        d($region);
        if (empty($region)) {
            $region = array('id_carrier' => Configuration::get('TUNNELVENTE_ID_CARRIER_POST'));// transporteur Post Si npa n'existe pas
        }


        $id_carrier_post = (int) Configuration::get('TUNNELVENTE_ID_CARRIER_POST');
        if ($cart) {
            foreach ($cart->getProducts() as $product) {
                if ($this->testIfRecyclage($product['id_product']) && (int) $region['id_carrier'] != $id_carrier_post)
                    return true;
            }
        }
        return false;
    }

    public function testIfRecyclage($test)
    {
        $id_product_recyclage1 = (int) Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_ECOSAPIN_GRATUIT');
        $id_product_recyclage2 = (int) Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_SAPIN_SUISSE_PAYANT');
        $id_product_recyclage3 = (int) Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_SAPIN_SUISSE_GRATUIT');

        if ($test == $id_product_recyclage1 || $test == $id_product_recyclage2 || $test == $id_product_recyclage3) {
            return TRUE;
        }
        return FALSE;
    }

    public function hookNewOrder($params)
    {
        $idOrderState      = $params['orderStatus']->id;
        $carriers_ids      = array();
        $sql               = 'SELECT `id_carrier` FROM `' . _DB_PREFIX_ . 'orders` WHERE `id_order` = ' . $params['order']->id;
        $result            = Db::getInstance()->getRow($sql);
        $id_carrier        = $result['id_carrier'];
        $availableCarriers = explode(', ', Configuration::get('PLANNING_DELIVERY_CARRIERS'));
        foreach ($availableCarriers as $carrier)
            $carriers_ids[] = $carrier;
        if (in_array($id_carrier, $carriers_ids)) {
            $result = Db::getInstance()->getRow('
				SELECT pd.`id_planning_delivery_carrier`, pd.`id_planning_delivery_carrier_slot`, pd.`date_delivery`, pd.`date_retour`, c.`id_carrier`
				FROM `' . _DB_PREFIX_ . 'planning_delivery_carrier` pd
				LEFT JOIN `' . _DB_PREFIX_ . 'cart` c ON (pd.`id_cart` = c.`id_cart`)
				WHERE pd.`id_cart` = ' . (int) ($params['cart']->id)
            )
            ;

            //todo added for using date retour in email
            /* Disable this email notification following a product owner's request
             * on this task https://app.asana.com/0/562275675824157/1122814139965431

            $dateRetourRequired = $this->dateRetourRequired($params['cart']->id);
            $dateRetour = $dateRetourRequired? $result['date_retour']: null;
			if ($result && (int)$result['id_planning_delivery_carrier'] > 0)
			{
				$planning_delivery = new PlanningDeliveriesByCarrier((int)$result['id_planning_delivery_carrier']);
				$planning_delivery->id_order = $params['order']->id;
				if ($planning_delivery->update())
				{
					if ($idOrderState != _PS_OS_ERROR_ && $idOrderState != _PS_OS_CANCELED_)
					{
						if (Configuration::get('PLANNING_DELIVERY_SLOT_'.$result['id_carrier']))
							$this->sendMail('date_delivery_slot', array('lang' => $params['order']->id_lang, 'id_customer' => $params['customer']->id, 'date_delivery' => $result['date_delivery'], 'delivery_slot' => PlanningDeliverySlotByCarrier::getNameById($result['id_planning_delivery_carrier_slot'], (int)($params['order']->id_lang))));
						else
							$this->sendMail('date_delivery', array('lang' => $params['order']->id_lang, 'id_customer' => $params['customer']->id, 'date_delivery' => $result['date_delivery'], 'date_retour' => $dateRetour));
					}
				}
			}
            */
        }
    }

    public function includeDatepickerRetour($id, $time = false, $format = 1, $onAdminPlanningDelivery = 0, $id_carrier = false)
    {
        $return       = '';
        $carriers_ids = array();
        if ($onAdminPlanningDelivery == 1) {
            $return .= '<script type="text/javascript">';
            if (is_array($id)) foreach ($id as $id2) $return .= $this->bindDatepickerRetour($id2, $time, $format, 2);
            else $return .= $this->bindDatepickerRetour($id, $time, $format, $onAdminPlanningDelivery, $id_carrier);
            $return .= '</script>';
        } else {
            if (!$id_carrier) {


                $planningCarriers = explode(', ', Configuration::get('PLANNING_DELIVERY_CARRIERS'));
                // getCarriers($id_lang, $active = false, $delete = false, $id_zone = false, $ids_group = null, $modules_filters = self::PS_CARRIERS_ONLY)
                $carriers             = Carrier::getCarriers((int) $this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);
                $carrierAndNbCommande = PlanningRetourByCarrierException::getNbCommandeAndRealNbCommand();
                $return               .= '<script type="text/javascript">';

//                $return .= "alert('no');";
                $unavalaibleDates = "";
                $unavailableDays  = "";
                $cart             = $this->context->cart;
                if (class_exists('Region'))
                    $region = Region::getRegionByNpa($cart->npa);

                if (!empty($region)) {
                    $unavalaibleDates = PlanningRetourByCarrierException::getDatesByCarrier($region['id_carrier']) . ", ";
                    $unavailableDays  = str_replace('7', '0', Configuration::get('PLANNING_DELIVERY_UNAV_DAYS' . $region['id_carrier']));
                }

                foreach ($carriers as $carrier) {
                    $id_carrier = $carrier['id_carrier'];
                    if (in_array($id_carrier, $planningCarriers)) {
                        //$unavalaibleDates = PlanningRetourByCarrierException::getDatesByCarrier($id_carrier);
                        //$unavailableDays = str_replace('7', '0', Configuration::get('PLANNING_DELIVERY_UNAV_DAYS'.$carrier['id_carrier']));
                        $return .= '
                                            
                                            var unavalaibleDatesRetour' . $id_carrier . ' = [' . $unavalaibleDates . '];
                                            var unavalaibleDaysRetour' . $id_carrier . ' = [' . $unavailableDays . '];
                                            var delta_daysRetour' . $id_carrier . ' = ' . ((int) (Configuration::get('PLANNING_DELIVERY_DAYS_BEFORE' . $id_carrier)) - 1) . ';' .
                            ((float) (date('H.i')) >= (float) (str_replace('h', '.', $this->today_max_hour)) ? 'delta_daysRetour' . $id_carrier . ' += 1;' : '') . '
                                            var delta_daysRetour_end' . $id_carrier . ' = delta_daysRetour' . $id_carrier . ' + ' . (int) (Configuration::get('PLANNING_DELIVERY_NUMB_DAYS' . $id_carrier)) . ';
                                            var firstDateRetour' . $id_carrier . ' = addDaysToDate(new Date(), delta_daysRetour' . $id_carrier . ');
                                            var lastDateRetour' . $id_carrier . ' = addDaysToDate(new Date(), delta_daysRetour_end' . $id_carrier . ');';
                    }
                }
                $return .= '
                            function showSlots(dateText, inst) {
                                    var id_carrier_checked = getIdCarrierChecked();
                                    var path = "' . __PS_BASE_URI__ . 'modules/planningdeliverybycarrier/";
                                    var id_lang = ' . (int) ($this->context->language->id) . ';
                                    var format = ' . $format . ';
                                    getDaySlot(path, dateText, format, id_lang, ' . $onAdminPlanningDelivery . ', id_carrier_checked);
                            }
                            var datesRemoveRetour = ' . json_encode($carrierAndNbCommande) . ' ;
                            </script>';
            } else {
                $availableCarriers = explode(', ', Configuration::get('PLANNING_DELIVERY_CARRIERS'));
                foreach ($availableCarriers as $carrier) $carriers_ids[] = $carrier;
                if (in_array($id_carrier, $carriers_ids)) {
//                    d($id);
                    if (is_array($id)) foreach ($id as $id2) {
//                        $return .= "alert('yes$id');";
                        $return .= $this->bindDatepickerRetour($id2, $time, $format, $onAdminPlanningDelivery, $id_carrier);
                    }
                    else {
//                        $return .= "alert('$id, $time, $format, $onAdminPlanningDelivery, $id_carrier');";
                        $return .= $this->bindDatepickerRetour($id, $time, $format, $onAdminPlanningDelivery, $id_carrier);
                    }

                    $return .= '$("#choose_delivery_date").fadeIn(\'slow\');';
                }
            }
        }
        return $return;
    }

    public function bindDatepickerRetour($id, $time, $format = 1, $onAdminPlanningDelivery, $id_carrier = false)
    {
        $return = '';
        if ($onAdminPlanningDelivery == 1) {
            //todo changes made for enabling all days in admin order menu (comments in function enableDaysRetour(date))
            $unavalaibleDates = PlanningRetourByCarrierException::getDates($id_carrier);
            $unavailableDays  = str_replace('7', '0', Configuration::get('PLANNING_DELIVERY_UNAV_DAYS' . $id_carrier));
            $return           .= '
				var unavalaibleDatesRetour = [' . $unavalaibleDates . '];
				var unavalaibleDaysRetour = [' . $unavailableDays . '];
				var delta_daysRetour = ' . ((int) (Configuration::get('PLANNING_DELIVERY_DAYS_BEFORE' . $id_carrier)) - 1) . ';
				var delta_days_endRetour = delta_daysRetour + ' . (int) (Configuration::get('PLANNING_DELIVERY_NUMB_DAYS' . $id_carrier)) . ';
				var firstDateRetour = addDaysToDate(new Date(), delta_daysRetour);
				var lastDateRetour = addDaysToDate(new Date(), delta_days_endRetour);
				function addDaysToDate(old_date, delta_days) {
					var dMyDate = old_date;
					dMyDate.setDate((dMyDate.getDate()+delta_days));
					return dMyDate;
				}
				function enableDaysRetour(date) {
					var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();
					if (d < 10){d = "0"+d;}
					if (m+1 < 10){m = "0"+(m+1);}
					else{m = (m+1);}
					var day = date.getDay();
					//if ($.inArray(y + \'-\' + m + \'-\' + d, unavalaibleDatesRetour) != -1 && $.inArray(day, unavalaibleDaysRetour) == -1) {
					//	return [true];
					//}
					return [true];
				}
				function showSlots(dateText, inst){
					var path = "' . __PS_BASE_URI__ . 'modules/planningdeliverybycarrier/";
					var id_lang = ' . (int) ((int) $this->context->language->id) . ';
					var format = ' . $format . ';
					getDaySlot(path, dateText, format, id_lang, ' . $onAdminPlanningDelivery . ', ' . $id_carrier . ');
				}';
        } elseif ($onAdminPlanningDelivery == 2) {
            $return .= '
			$(function() {
				$("#' . $id . '").datepicker({
					prevText:"",
					nextText:"",
					dateFormat:"yy-mm-dd"' . ($time ? '+time' : '') . '});
			});';
            return $return;
        }
        $onSelect = (Configuration::get('PLANNING_DELIVERY_SLOT_' . $id_carrier)) ? "onSelect: showSlots, \n" : '';
        $dFormat  = (1 == $format) ? 'dd/mm/yy' : 'mm/dd/yy';
        $return   .= '
		$(function(){
			$("#' . $id . '").datepicker({
				prevText:"",
				nextText:"",
				beforeShowDay: enableDaysRetour, ' .
            $onSelect . '
				dateFormat:"' . $dFormat . '"' . ($time ? '+time' : '') . '});
		});
		$("#ui-datepicker-div").css("clip", "auto");';
        return $return;
    }

    public function hookActionCarrierProcess($params)
    {
        $oblige = 1;
        if (1 != Configuration::get('PS_ORDER_PROCESS_TYPE')) {
            $cart    = $params['cart'];
            $address = new Address((int) ($params['cart']->id_address_delivery));
            $country = new Country((int) ($address->id_country));
            $format  = ('US' != $country->iso_code) ? 1 : 2;
            $date_retour   = Tools::getValue('date_retour');
            $date_delivery = Tools::getValue('date_delivery');
            $delivery_message = Tools::getValue('delivery_message');
            $errors = [];

            if (($date_delivery && $date_retour && Tools::getValue('action') === "selectDeliveryOption")
                || ((empty($date_delivery) || empty($date_retour)) && Tools::getValue('confirmDeliveryOption') == 1))
            {
                $errors = $this->requestProcessDateDelivery((int)($cart->id), $cart->id_carrier, Tools::getValue('date_delivery'), Tools::getValue('id_planning_delivery_slot'), $date_retour, $format);

            }

            if (empty($delivery_message) && Tools::getValue('confirmDeliveryOption') == 1) {
                $errors[] = Tools::displayError($this->l('Please enter your message'));
            }

            if (count($errors) && $oblige == 1) {
               // $this->context->controller->step = 2;
                $this->smarty->assign('pderrors', $errors);
            }
        }
    }

    public function hookBackOfficeHeader($params)
    {
        // Chargement et dispatch des évènements AJAX pour prendre en compte le contexte du B.O.
        if (Tools::getValue('ajax') && Tools::getValue('id_day')) {
            ob_clean();
            $this->getPlanningDeliverySlotsByCarrier();
            exit;
        }
        if (Tools::getValue('renderCSV')) $this->renderCSV();
        $return = '<link href="' . __PS_BASE_URI__ . 'modules/planningdeliverybycarrier/css/admin_planningdeliverybycarrier' . (int) (Tools::substr(sprintf('%0-6s', str_replace('.', '', (string) (_PS_VERSION_))), 0, 2)) . '.css" rel="stylesheet" type="text/css" media="all" />';
        if ('adminplanningdeliverybycarrier' == Tools::strtolower(Tools::getValue('controller')) || 'adminorders' == Tools::strtolower(Tools::getValue('controller')))
            $return .= '<script type="text/javascript" src="' . __PS_BASE_URI__ . 'modules/planningdeliverybycarrier/js/datepickerSlot.js"></script>';
        $return .= "
<link href=\"//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.1/bootstrap3-editable/css/bootstrap-editable.css\" rel=\"stylesheet\"/>
<script src=\"//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.1/bootstrap3-editable/js/bootstrap-editable.min.js\"></script>
";
//<link href=\"//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css\" rel=\"stylesheet\">
        $return .= '<link href="' . _PS_JS_DIR_ . 'jquery/ui/themes/base/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" media="all" />';
        $return .= '<link href="' . _PS_JS_DIR_ . 'jquery/ui/themes/base/jquery.ui.theme.css" rel="stylesheet" type="text/css" media="all" />';
        return $return;
    }

    public function getPlanningDeliverySlotsByCarrier()
    {
        include_once(dirname(__FILE__) . '/ajax/planningdeliveryslotsbycarrier.ajax.php');
    }

    public function hookDisplayPDFDeliverySlip($params)
    {
        return $this->hookDisplayPDFInvoice($params);
    }

    public function hookDisplayPDFInvoice($params)
    {
        $orderInvoice      = $params['object'];
        $order             = new Order($orderInvoice->id_order);
        $availableCarriers = explode(', ', Configuration::get('PLANNING_DELIVERY_CARRIERS'));
        $carriers_ids      = array();
        foreach ($availableCarriers as $carrier)
            $carriers_ids[] = $carrier;
        if (in_array($order->id_carrier, $carriers_ids)) {
            $sql    = 'SELECT pd.*, pds.`name` FROM `' . _DB_PREFIX_ . 'planning_delivery_carrier` pd
				LEFT JOIN `' . _DB_PREFIX_ . 'planning_delivery_carrier_slot` pds
				ON pd.`id_planning_delivery_carrier_slot` = pds.`id_planning_delivery_carrier_slot`
				WHERE `id_order` = ' . (int) ($orderInvoice->id_order);
            $result = Db::getInstance()->getRow($sql);
            if ($result) {
                $this->smarty->assign('date_delivery', $result['date_delivery']);
                $this->smarty->assign('date_retour', $result['date_retour']);
                $this->smarty->assign('delivery_slot', (!empty($result['name']) ? PlanningDeliverySlotByCarrier::hideSlotsPosition($result['name']) : '-'));
                return $this->display(__FILE__, 'pdf-invoice.tpl');
            }
        }
    }

    public function renderCSV()
    {
        $datepickerFrom = Tools::getValue('datepickerFrom');
        $datepickerFrom = ($datepickerFrom != '') ? $datepickerFrom : date('Y-m-d H:i:s');
        $datepickerTo   = Tools::getValue('datepickerTo');
        $delimiter      = $this->delimiters[(int) (Tools::getValue('delimiter'))];
        $enclosure      = $this->enclosures[(int) (Tools::getValue('enclosure'))];
        $orderState     = (int) (Tools::getValue('orderState'));
        $carrier        = (int) (Tools::getValue('carrier'));

        $where = '';
        if ((int) ($orderState) != 0) $where .= ' AND o.`current_state` = ' . $orderState;
        if ((int) ($carrier) != 0) $where .= ' AND o.`id_carrier` = ' . $carrier;
        if ($datepickerTo != '') $where .= ' AND pd.`date_delivery` <= \'' . $datepickerTo . '\'';

        $query = 'SELECT o.`id_order`, ca.`name` as carrier_name, osl.`name`, o.`payment`, o.`total_products`,
			o.`invoice_number`, o.`delivery_number`, c.`id_customer`, c.`firstname`, c.`lastname`,
			CONCAT(ad.`address1`, " ", ad.`address2`, " ", ad.`postcode`, " ", ad.`city`) as delivery_address, pd.date_delivery, pds.`name` AS pdsname
			FROM `' . _DB_PREFIX_ . 'orders` AS o
			LEFT JOIN `' . _DB_PREFIX_ . 'customer` AS c ON c.`id_customer` = o.`id_customer`
			LEFT JOIN `' . _DB_PREFIX_ . 'address` AS ad ON ad.`id_address` = o.`id_address_delivery`
			LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON (os.`id_order_state` = o.`current_state`)
			LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = ' . (int) $this->context->language->id . ')
			LEFT JOIN `' . _DB_PREFIX_ . 'planning_delivery_carrier` pd ON (pd.`id_order` = o.`id_order`)
			LEFT JOIN `' . _DB_PREFIX_ . 'planning_delivery_carrier_slot` pds ON (pd.`id_planning_delivery_carrier_slot` = pds.`id_planning_delivery_carrier_slot`)
			LEFT JOIN `' . _DB_PREFIX_ . 'carrier` ca ON (o.`id_carrier` = ca.`id_carrier`)
			WHERE pd.`date_delivery` >= \'' . $datepickerFrom . '\' ' . $where . ' ORDER BY o.`date_add` DESC';
        $res   = Db::getInstance()->ExecuteS($query);

        if (count($res) > 0) {
            $titles = array(
                $this->l('id_order'), $this->l('carrier_name'), $this->l('name'), $this->l('payment'), $this->l('total_products'),
                $this->l('invoice_number'), $this->l('delivery_number'), $this->l('id_customer'), $this->l('firstname'), $this->l('lastname'),
                $this->l('adress'), $this->l('date_delivery'), $this->l('pdsname')
            );

            header('Content-type: text/csv');
            header('Content-Type: application/force-download; charset=UTF-8');
            header('Cache-Control: no-store, no-cache');
            header('Content-disposition: attachment; filename="' . $this->l('orders') . '-' . time() . '.csv"');

            $fp = fopen('php://output', 'w');

            $fields = array();
            foreach ($titles as $k => $field) $fields[$k] = utf8_decode($field);
            fputcsv($fp, $fields, $delimiter, $enclosure);

            foreach ($res as $fields) {
                foreach ($fields as $k => $field) {
                    $fields[$k] = utf8_decode($field);
                    if ('carrier_name' == $k && '0' == $field) $fields[$k] = Configuration::get('PS_SHOP_NAME');
                    if ('pdsname' == $k) $fields[$k] = utf8_decode(PlanningDeliverySlotByCarrier::hideSlotsPosition($field));
                }
                fputcsv($fp, $fields, $delimiter, $enclosure);
            }
            fclose($fp);
        }
        exit();
    }

    /***************************************************************************************************************/

    protected function _displayFormCarrierGoToSettings()
    {
        $this->_html             = '
		<script type="text/javascript" src="' . __PS_BASE_URI__ . 'js/jquery/ui/jquery.ui.core.min.js"></script>
		<script type="text/javascript" src="' . $this->_path . 'js/jquery.ui.timepicker.js"></script>
		<script type="text/javascript" src="' . $this->_path . 'js/functions.js"></script>
		<link href="' . __PS_BASE_URI__ . 'js/jquery/ui/themes/base/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
		<link href="' . __PS_BASE_URI__ . 'js/jquery/ui/themes/base/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" media="all" />
		<link href="' . $this->_path . 'css/jquery.ui.timepicker.css" rel="stylesheet" type="text/css" />
		<form action="' . $_SERVER['REQUEST_URI'] . '" method="post" name="settings_form">
			<fieldset><legend><img src="' . $this->_path . 'img/prefs.gif" alt="" title="" />' . $this->l('Settings by carrier') . '</legend>';
        $planingDeliveryCarriers = Configuration::get('PLANNING_DELIVERY_CARRIERS');
        $availableCarriers       = explode(', ', Configuration::get('PLANNING_DELIVERY_CARRIERS'));
        // getCarriers($id_lang, $active = false, $delete = false, $id_zone = false, $ids_group = null, $modules_filters = self::PS_CARRIERS_ONLY)
        $carriers    = Carrier::getCarriers((int) $this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);
        $this->_html .= '
				<label for="goToCarrier">' . ($this->checkVersion() > '1.5' ? '<i class="icon-AdminParentShipping"></i>' : '<img src="../img/t/AdminParentShipping.gif" />') . '' . $this->l('Carrier') . '</label>';
        if (empty($planingDeliveryCarriers))
            $this->_html .= '<div class="margin-form">' . $this->l('no carrier to configure') . '</div>';
        else {
            $this->_html .= '
					<div class="margin-form">
						<select name="goToCarrier" id="goToCarrier">';
            foreach ($carriers as $carrier)
                if (in_array($carrier['id_carrier'], $availableCarriers))
                    $this->_html .= '
								<option value="' . (int) ($carrier['id_carrier']) . '"' . ((int) ($carrier['id_carrier']) == (int) ($this->id_carrier) ? ' selected="selected" ' : '') . '>' . $carrier['name'] . ' (id:' . $carrier['id_carrier'] . ')</option>';
            $this->_html .= '
						</select>
						<input type="submit" name="submitCarrierGoToSettings" value="' . $this->l('Configure') . '" class="button" />';
            if ($this->id_carrier > 0)
                $this->_html .= '
							<input type="submit" name="submitReturnToGlobalSettings" value="' . $this->l('return to global settings') . '" class="button" />';
            $this->_html .= '
					</div>';
        }
        $this->_html .= '
			</fieldset>
		</form><br />';
    }

    protected function _displayFormCarriersGlobalSettings()
    {
        $this->_html       .= '
		<script type="text/javascript" src="' . $this->_path . 'js/functions.js"></script>
		<form action="' . $_SERVER['REQUEST_URI'] . '" method="post" name="settings_form">
			<fieldset><legend><img src="' . $this->_path . 'img/prefs.gif" alt="" title="" />' . $this->l('Globals settings') . '</legend>';
        $availableCarriers = explode(', ', Configuration::get('PLANNING_DELIVERY_CARRIERS'));
        // getCarriers($id_lang, $active = false, $delete = false, $id_zone = false, $ids_group = null, $modules_filters = self::PS_CARRIERS_ONLY)
        $carriers    = Carrier::getCarriers((int) $this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);
        $this->_html .= '
				<label>' . $this->l('Carriers') . '</label>
				<div class="margin-form">';
        foreach ($carriers as $carrier) {
            $this->_html .= '
					<label class="t" for="' . $carrier['id_carrier'] . '_' . $carrier['name'] . '"> ' . $carrier['name'] . '</label>
					<input type="checkbox" name="carriersBox[]" id="' . $carrier['id_carrier'] . '_' . $carrier['name'] . '" value="' . $carrier['id_carrier'] . '" ' . (in_array($carrier['id_carrier'], $availableCarriers) ? 'checked="checked" ' : '') . '/>';
        }
        $this->_html    .= '
				<p class="clear" style="display: block; width: 550px;">' .
            $this->l('Select the carrier for which the choice of the delivery date will be activated.') . '
				</p>
				</div>
				<label>' . $this->l('Listing homepage BO') . '</label>
				<div class="margin-form">
					<input type="radio" name="active_hook_home_back_office" id="active_hook_home_back_office_on" value="1" ' . (Configuration::get('PLANNING_DELIVERY_HOMEBACKOFFICE') ? 'checked="checked" ' : '') . '/>
					<label class="t" for="active_hook_home_back_office_on"> <img src="../img/admin/enabled.gif" alt="' . $this->l('Enabled') . '" title="' . $this->l('Enabled') . '" /></label>
					<input type="radio" name="active_hook_home_back_office" id="active_hook_home_back_office_off" value="0" ' . (!Configuration::get('PLANNING_DELIVERY_HOMEBACKOFFICE') ? 'checked="checked" ' : '') . '/>
					<label class="t" for="active_hook_home_back_office_off"> <img src="../img/admin/disabled.gif" alt="' . $this->l('Disabled') . '" title="' . $this->l('Disabled') . '" /></label>
					<p class="clear" style="display: block; width: 550px;">' .
            $this->l('Active recall of deliveries to be made on the homepage of Administration.') . '
					</p>
					<div class="clear"></div>
				</div>';
        $orderStates    = OrderState::getOrderStates((int) $this->context->language->id);
        $unavailableOSs = explode(', ', Configuration::get('PLANNING_DELIVERY_UNAV_OSS'));
        $this->_html    .= '
				<p>' . $this->l('Select the status of orders you do not want to appear in the summary of the deliveries to be made. You can select more than one by pressing the CTRL key.') . '</p><br />
				<label for="osPlanning">' . $this->l('Day') . '</label>
				<div class="margin-form">
					<select name="osPlanning[]" id="osPlanning" multiple="true" style="height:200px;width:360px;">';
        foreach ($orderStates as $orderState)
            $this->_html .= '
						<option value="' . (int) ($orderState['id_order_state']) . '" ' . (in_array($orderState['id_order_state'], $unavailableOSs) ? 'selected="selected" ' : '') . '>' . $orderState['name'] . '</option>';
        $this->_html .= '
					</select>
				</div>
				<div class="margin-form clear"><input type="submit" name="submitCarriersGlobalSettings" value="' . $this->l('Save') . '" class="button" /></div>';
        $this->_html .= '
			</fieldset>
		</form><br />';
    }

    protected function _displayFormGlobalSettings()
    {
        if (!Configuration::get('PLANNING_DELIVERY_DAYS_BEFORE' . $this->id_carrier) && Configuration::get('PLANNING_DELIVERY_DAYS_BEFORE' . $this->id_carrier) !== '0')
            Configuration::updateValue('PLANNING_DELIVERY_DAYS_BEFORE' . $this->id_carrier, $this->_defaultConfig['PLANNING_DELIVERY_DAYS_BEFORE']);

        if (!Configuration::get('PLANNING_DELIVERY_NUMB_DAYS' . $this->id_carrier))
            Configuration::updateValue('PLANNING_DELIVERY_NUMB_DAYS' . $this->id_carrier, $this->_defaultConfig['PLANNING_DELIVERY_NUMB_DAYS']);

        $this->_html .= '
		<script type="text/javascript" src="' . $this->_path . 'js/functions.js"></script>
		<form action="' . $_SERVER['REQUEST_URI'] . '" method="post" name="settings_form">
			<fieldset><legend><img src="' . $this->_path . 'img/prefs.gif" alt="" title="" />' . $this->l('Selected Date') . '</legend>
				<label style="display:none">' . $this->l('Selected date required') . '</label>
				<div class="margin-form" style="display:none">
					<input type="radio" name="planning_required" id="planning_required_on" value="1" checked="checked"/>
					<label class="t" for="planning_required_on"> <img src="../img/admin/enabled.gif" alt="' . $this->l('Enabled') . '" title="' . $this->l('Enabled') . '" /></label>
					<input type="radio" name="planning_required" id="planning_required_off" value="0" />
					<label class="t" for="planning_required_off"> <img src="../img/admin/disabled.gif" alt="' . $this->l('Disabled') . '" title="' . $this->l('Disabled') . '" /></label>
				</div>
				<label>' . $this->l('Slots') . '</label>
				<div class="margin-form">
					<input type="radio" name="active_slot" id="active_slot_on" value="1" ' . (Configuration::get('PLANNING_DELIVERY_SLOT_' . $this->id_carrier) ? 'checked="checked" ' : '') . '/>
					<label class="t" for="active_slot_on"> <img src="../img/admin/enabled.gif" alt="' . $this->l('Enabled') . '" title="' . $this->l('Enabled') . '" /></label>
					<input type="radio" name="active_slot" id="active_slot_off" value="0" ' . (!Configuration::get('PLANNING_DELIVERY_SLOT_' . $this->id_carrier) ? 'checked="checked" ' : '') . '/>
					<label class="t" for="active_slot_off"> <img src="../img/admin/disabled.gif" alt="' . $this->l('Disabled') . '" title="' . $this->l('Disabled') . '" /></label>
					<p class="clear" style="display: block; width: 550px;">' .
            $this->l('Active management of slots for selecting the time of delivery.') . '
					</p>
					<div class="clear"></div>
				</div>
				<label for="days_before_first_date">' . $this->l('Days before planning') . '</label>
				<div class="margin-form">
					<input type="text" name="days_before_first_date" id="days_before_first_date" value="' . Configuration::get('PLANNING_DELIVERY_DAYS_BEFORE' . $this->id_carrier) . '"/>

					<p class="clear" style="display: block; width: 550px;">' .
            $this->l('Indicates the number of days before the first date selectable by the user.') . '
					</p>
					<div class="clear"></div>
				</div>
				<label for="number_of_days">' . $this->l('Number of days') . '</label>
				<div class="margin-form">
					<input type="text" name="number_of_days" id="number_of_days" value="' . Configuration::get('PLANNING_DELIVERY_NUMB_DAYS' . $this->id_carrier) . '"/>
					<p class="clear" style="display: block; width: 550px;">' .
            $this->l('Indicates the number of days between the first date and the last date selectable by the user.') . '
					</p>
					<div class="clear"></div>
				</div>
				<div class="margin-form clear"><input type="submit" name="submitPlanningRequired" value="' . $this->l('Save') . '" class="button" /></div>
				<input type="hidden" name="subIdCarrier" value="' . $this->id_carrier . '" />';
        $this->_html .= '
			</fieldset>
		</form><br />';
    }

    protected function _displayFormAvailablesDays()
    {
        if (!Configuration::get('PLANNING_DELIVERY_UNAV_DAYS' . $this->id_carrier) && Configuration::get('PLANNING_DELIVERY_UNAV_DAYS' . $this->id_carrier) != '')
            Configuration::updateValue('PLANNING_DELIVERY_UNAV_DAYS' . $this->id_carrier, $this->_defaultConfig['PLANNING_DELIVERY_UNAV_DAYS']);
        $unavailableDays = explode(', ', Configuration::get('PLANNING_DELIVERY_UNAV_DAYS' . $this->id_carrier));

        $this->_html .= '
		<a id="planningdelivery_available_days" name="planningdelivery_available_days"></a>
		<form action="' . $_SERVER['REQUEST_URI'] . '" method="post" name="day_form">
			<fieldset><legend><img src="' . $this->_path . 'img/prefs.gif" alt="" title="" />' . $this->l('Available Days') . '&nbsp;<a href="javascript:{}" onclick="$.scrollTo(\'#planningdelivery_top\', 1200);"><img src="' . _PS_ADMIN_IMG_ . 'up.gif" alt="" /></a></legend>
				<label>' . $this->l('Available Days') . '</label>
				<div class="margin-form">';
        foreach ($this->days as $idDay => $day) {
            $this->_html .= '
					<label class="t" for="' . $day . '"> ' . Tools::ucfirst($this->l($day)) . '</label>
					<input type="checkbox" name="daysBox[]" id="' . $day . '" value="' . $idDay . '" ' . (!in_array($idDay, $unavailableDays) ? 'checked="checked" ' : '') . '/>';
        }
        $this->_html .= '
				</div>
				<div class="margin-form clear"><input type="submit" name="submitAvailableDays" value="' . $this->l('Save') . '" class="button" /></div>';
        $this->_html .= '
			<input type="hidden" name="subIdCarrier" value="' . $this->id_carrier . '" />
			</fieldset>
		</form><br />';
    }

    protected function _displayFormSlots()
    {
        $this->_html .= '
		<a id="planningdelivery_slots" name="planningdelivery_slots"></a>
		<form action="' . $_SERVER['REQUEST_URI'] . '" method="post" name="slot_form">
			<fieldset><legend><img src="' . $this->_path . 'img/prefs.gif" alt="" title="" />' . $this->l('Slots') . '&nbsp;<a href="javascript:{}" onclick="$.scrollTo(\'#planningdelivery_top\', 1200);"><img src="' . _PS_ADMIN_IMG_ . 'up.gif" alt="" /></a></legend>
				<label for="slot1">' . $this->l('From_hour') . '</label>
				<div class="margin-form">
					<input type="text" name="slot1" id="slot1" class="timepicker" /> ' . $this->l('to_hour') . ' <input type="text" name="slot2" id="slot2" class="timepicker" />
				</div>
				<label for="slot">' . $this->l('Label du créneau horaire') . '</label>
				<div class="margin-form">
					<input type="text" name="slot" id="slot" />
				</div>
				<label for="slot">' . $this->l('Customers max') . '</label>
				<div class="margin-form">
					<input type="text" size="3" name="max_customers" id="max_customers" />
					<input type="submit" name="submitSlot" value="' . $this->l('Add') . '" class="button" />

					<p class="clear" style="display: block; width: 550px;">' .
            $this->l('Create a new time slot.') . '<br/>' .
            $this->l('Once created, you must activate it for the desired days with the form below.') . '<br />' . $this->l('Be aware that the slots are independent in each language.') . '<br />' .
            $this->l('Max customers: Specifies the maximum number of customers for the time slot to add.') . '
					</p>

				</div>';

        $slots = PlanningDeliverySlotByCarrier::get((int) $this->context->language->id);

        if ($slots && count($slots)) {
            $this->_html .= '
				 <input type="hidden" name="id_planning_delivery_slot" id="id_planning_delivery_slot" />
				 <input type="hidden" name="slot_name" id="slot_name" />
				 <input type="hidden" name="slot_slot1" id="slot_slot1" />
				 <input type="hidden" name="slot_slot2" id="slot_slot2" />
				 <input type="hidden" name="slot_customers_max" id="slot_customers_max" />
				 <input type="hidden" name="slot_id_lang" id="slot_id_lang" value="' . (int) ((int) $this->context->language->id) . '" />
				 <input type="hidden" name="slot_action" id="slot_action" />
				 <br /><table cellspacing="0" class="table">
				 <thead>
				  <tr>
				   <th style="width:130px;">' . $this->l('Actions') . '</th>
				   <th style="width:260px;">' . $this->l('Slot') . '</th>
				   <th style="width:160px;">' . $this->l('Custumers') . '</th>
				   <th style="width:130px;">' . $this->l('Actions') . '</th>
				   <th style="width:260px;">' . $this->l('Slot') . '</th>
				   <th style="width:160px;">' . $this->l('Custumers') . '</th>
				   <th style="width:130px;">' . $this->l('Actions') . '</th>
				   <th style="width:260px;">' . $this->l('Slot') . '</th>
				   <th style="width:160px;">' . $this->l('Custumers') . '</th>
				  </tr>
				 </thead>
				 <tbody><tr>';
            $len         = count($slots);
            for ($i = 0; $i < $len; ++$i) {
                $slot1     = Tools::substr(str_replace($this->datetime_simu, '', $slots[$i]['slot1']), 0, 5);
                $slot2     = Tools::substr(str_replace($this->datetime_simu, '', $slots[$i]['slot2']), 0, 5);
                $class_odd = ($i % 2) ? '' : 'class="tr_odd"';

                $this->_html .= '
					<td style="border:0" ' . $class_odd . '><a href="javascript:;" onclick="editSlot(\'' . (int) ($slots[$i]['id_planning_delivery_carrier_slot']) . '\');"><img src="' . $this->_path . 'img/accept.png" alt="' . $this->l('Accept') . '" /></a>
					 <a href="javascript:;" onclick="deleteSlot(\'' . (int) ($slots[$i]['id_planning_delivery_carrier_slot']) . '\');"><img src="' . $this->_path . 'img/delete.png" alt="' . $this->l('Delete') . '" /></a></td>
					<td style="border:0" ' . $class_odd . '>
						<label style="width:50px">' . $this->l('Label') . '</label> <input type="text" id="slot_name_' . (int) ($slots[$i]['id_planning_delivery_carrier_slot']) . '" value="' . htmlspecialchars($slots[$i]['name'], ENT_COMPAT, 'UTF-8') . '" /><br />
						<label style="width:50px">' . $this->l('De') . '</label> <input type="text" class="timepicker" id="slot_slot1_' . (int) ($slots[$i]['id_planning_delivery_carrier_slot']) . '" value="' . $slot1 . '" /><br />
						<label style="width:50px">' . $this->l('A') . '</label> <input type="text" class="timepicker" id="slot_slot2_' . (int) ($slots[$i]['id_planning_delivery_carrier_slot']) . '" value="' . $slot2 . '" />
					</td>
					<td style="border:0" ' . $class_odd . '><input type="text" size="3" id="slot_customers_max_' . (int) ($slots[$i]['id_planning_delivery_carrier_slot']) . '" value="' . htmlspecialchars($slots[$i]['customers_max'], ENT_COMPAT, 'UTF-8') . '" /></td>';
                if (!(($i + 1) % 3) || ($i + 1) >= $len)
                    $this->_html .= '</tr><tr>';
            }
            if (!$len)
                $this->_html .= '</tr>';
            $this->_html .= '</tbody>
				</table>';
        }
        $this->_html .= '<input type="hidden" name="subIdCarrier" value="' . $this->id_carrier . '" /></fieldset></form><br />';
        $this->_html .= '
		<script type="text/javascript">
			$("document").ready(function(){
				$(".timepicker").timepicker({
					hourText: "' . $this->l('Heures') . '",
					minuteText: "' . $this->l('Minutes') . '",
					amPmText: ["' . $this->l('AM') . '", "' . $this->l('PM') . '"],
					timeSeparator: "' . $this->l(':') . '",
					nowButtonText: "' . $this->l('Now') . '",
					showNowButton: true,
					closeButtonText: "' . $this->l('Close') . '",
					showCloseButton: true,
					deselectButtonText: "' . $this->l('Deselect') . '",
					showDeselectButton: true
				});
			});
		</script>';
    }

    protected function _displayFormDaySlot()
    {
        $this->_html .= '
			<a id="planningdelivery_day_slots" name="planningdelivery_day_slots"></a>
			<form action="' . $_SERVER['REQUEST_URI'] . '" method="post" name="day_slot_form">
			<fieldset><legend><img src="' . $this->_path . 'img/prefs.gif" alt="" title="" />' . $this->l('Day/slots') . '&nbsp;<a href="javascript:{}" onclick="$.scrollTo(\'#planningdelivery_top\', 1200);"><img src="' . _PS_ADMIN_IMG_ . 'up.gif" alt="" /></a></legend>
				<p>' . $this->l('Select the time\'s slots corresponding to each day. You can select multiple slots by pressing the ctrl key.') . '</p><br />
				<label for="id_day">' . $this->l('Day') . '</label>
				<div class="margin-form">
				<select name="id_day" id="id_day" onchange="getDaySlot(\'' . $_SERVER['REQUEST_URI'] . '\', this.options[this.selectedIndex].value, 1, \'' . (int) ((int) $this->context->language->id) . '\', 1, \'' . (int) ($this->id_carrier) . '\');">';
        foreach ($this->days as $dayNumber => $day)
            $this->_html .= '
						<option value="' . (int) ($dayNumber) . '">' . $this->l($day) . '</option>';
        $this->_html .= '
					</select>
				</div>
				<label for="id_planning_delivery_slot">' . $this->l('Slots') . '</label>
				<div id="day_slots" class="margin-form">
				</div>
				<div class="margin-form clear"><input type="submit" name="submitSlotDay" value="' . $this->l('Save') . '" class="button" /></div>
				<script type="text/javascript">
					getDaySlot(\'' . $_SERVER['REQUEST_URI'] . '\', document.getElementById(\'id_day\').options[0].value, 1, \'' . (int) ((int) $this->context->language->id) . '\', false, \'' . (int) ($this->id_carrier) . '\');
				</script>
			<input type="hidden" name="subIdCarrier" value="' . $this->id_carrier . '" />
			</fieldset>
			</form><br />';
    }

    protected function _displayFormException()
    {
        $carriers = Carrier::getCarriers((int) $this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);

        $carrier_html_list = "<select id='id_carrier' name='id_carrier'>";
        if ($carriers) {
            foreach ($carriers as $row) {
                $carrier_html_list .= "<option value='" . $row['id_carrier'] . "' >" . $row['name'] . " (id: " . $row['id_carrier'] . ")</option>";
            }
        }
        $carrier_html_list .= "</select>";

        $this->_html .= $this->includeDatepicker(array('date_from', 'date_to'), false, 1, 1);
        $this->_html .= '
		<a id="planningdelivery_exceptions" name="planningdelivery_exceptions"></a>
		<form action="' . $_SERVER['REQUEST_URI'] . '" method="post" name="exception_form">
		<fieldset><legend><img src="' . $this->_path . 'img/prefs.gif" alt="" title="" />' . $this->l('Date de livraison') . '&nbsp;<a href="javascript:{}" onclick="$.scrollTo(\'#planningdelivery_top\', 1200);"><img src="' . _PS_ADMIN_IMG_ . 'up.gif" alt="" /></a></legend>
			<p>' . $this->l('Add as many exceptions as necessary.') . '</p><br />
			<label>' . $this->l('Date de livraison') . '</label>
			<div class="margin-form">
				<label class="t" for="date_from"> ' . $this->l('Date from') . '</label>
				<br>
				<input type="text" name="date_from" id="date_from"/>
				<br>
				<label class="t" for="date_to"> ' . $this->l('to') . '</label>
				<br>
				<input type="text" name="date_to" id="date_to"/>
				<br>
				<label class="t" for="max_places"> ' . $this->l('max places') . '</label>
				<br>
				<input type="number" name="max_places" id="max_places"/>
				<br>
				<label class="t" for="id_carrier">' . $this->l('carrier') . '</label>
				<br>
				' . $carrier_html_list . '
				<br>
				<input type="submit" name="submitException" value="' . $this->l('Add') . '" class="button" />
			</div>';

        $exceptions = PlanningDeliveryByCarrierException::get();

//        $id_planning_delivery_carrier_exception = (int) Tools::getValue("id_planning_delivery_carrier_exception");
//        $nb_commandes = (int) Tools::getValue("nb_commande");
//        $success = PlanningDeliveryByCarrierException::updateNbCommande($id_planning_delivery_carrier_exception, $nb_commandes);


        $len = count($exceptions);
        if ($len) {
            $this->_html .= '
				<input type="hidden" name="id_planning_delivery_carrier_exception" id="id_planning_delivery_carrier_exception" />
				<input type="hidden" name="exception_name" id="exception_name" />
				<input type="hidden" name="exception_action" id="slot_action" />
				<button id="toggleDeliveryTable">Afficher/Cacher Tableau</button>
				<br /><table class="table" id="deliveryTable" style="width:100%;">
				<thead>
				<tr>
				 <th style="width:40%;">' . $this->l('Restricted dates') . '</th>
				 <th style="width:10%;">' . $this->l('Commands') . '</th>
				 <th style="width:15%;">' . $this->l('Max places') . '</th>
				 <th style="width:45%;">' . $this->l('Carrier') . '</th>
				 <th style="width:5%;">' . $this->l('Actions') . '</th>
				</tr>
				</thead>
				<tbody>';
            foreach ($exceptions as $exception) {
//                $sql = "SELECT
//                            pde.`id_planning_delivery_carrier_exception`,
//                            pde.`id_carrier`,
//                            pde.`date_from`,
//                            pde.`date_to`,
//                            pde.`max_places`,
//                            COUNT(*) AS real_nb_commande
//                        FROM
//                            `ps_planning_delivery_carrier_exception` pde, `ps_planning_delivery_carrier` pd, `ps_orders` o, `ps_carrier` pc
//                        WHERE
//                            1 = 1
//                                AND pd.`date_delivery` BETWEEN pde.`date_from` AND pde.`date_to`
//                                AND pd.`id_order` = o.`id_order`
//                                AND pc.`id_carrier` = o.`id_carrier`
//                                AND o.`id_carrier` = pde.`id_carrier`
//                                AND pc.`deleted` = 0
//                                AND pc.`active` = 1
//                                AND o.`current_state` NOT IN (6)
//                                AND pde.`id_planning_delivery_carrier_exception` = {$exception['id_planning_delivery_carrier_exception']}";
//
//                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
//                $exception['nb_commandes'] = $result[0]["real_nb_commande"];
//                d($exception);
//                $exception['nb_commandes'] = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)[0]["real_nb_commande"];

                $isMaxReached = ($exception['nb_commandes'] >= $exception['max_places']);

                $this->_html .= '
					<tr>
					 <td> ' . ($exception['date_from'] == $exception['date_to'] ? $this->dateFR_S($exception['date_from']) : $this->l('From') . ' ' . $this->dateFR_S($exception['date_from']) . ' ' . $this->l('to') . ' ' . $this->dateFR_S($exception['date_to'])) . '</td>
					 <td> ' . '<span style="' . ($isMaxReached ?
                        'color:white;background:red;padding:5px;border-radius:5px;margin:1px;display:inline-block;'
                        :
                        'color:white;background:green;padding:5px;border-radius:5px;margin:1px;display:inline-block;'
                    ) . ';">' . $exception['nb_commandes'] . '</span></td>
					 <td> <a class="texte" data-type="text" data-pk="' . $exception['id_planning_delivery_carrier_exception'] . '">' . $exception['max_places'] . '</a></td>
					 <td> ' . $exception['name'] . '(id: ' . $exception['id_carrier'] . ')</td>
					 <td style="text-align:center;"><a href="javascript:;" onclick="deleteException(\'' . (int) ($exception['id_planning_delivery_carrier_exception']) . '\');"><img src="' . $this->_path . 'img/delete.png" alt="' . $this->l('Delete') . '" /></a></td>
					</tr>';
            }
            $this->_html .= '
				</tbody>
				</table>';
        }

        $token = Tools::getAdminTokenLite('AdminPlanningDeliveryByCarrier');

        $this->_html .= "
			</fieldset>
		</form><br />
		<script type=\"text/javascript\">

        $(document).ready(function() {
            $('#toggleDeliveryTable').on('click',function (e){
                e.preventDefault();
                $('#deliveryTable').toggle(500);
            });
            $('.texte').editable({
                url: 'index.php?controller=AdminPlanningDeliveryByCarrier&action=updatemaxplaces&ajax=true&token={$token}'
            });
        });
        </script>
		";
    }

//    protected function _displayFormException()
//    {
//        $availableCarriers = explode(', ', Configuration::get('PLANNING_DELIVERY_CARRIERS'));
//        $carriers = Carrier::getCarriers((int)$this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);
//        $mycarrier = array();
//        $carrierAndNbCommande = array();
//        foreach ( PlanningDeliveryByCarrierException::getAllNbCommandeAndRealNbCommand() as $r){
//            $carrierAndNbCommande[$r['id_planning_delivery_carrier_exception']] = $r;
//        }
//
//        foreach ($carriers as $carrier){
//            $mycarrier[$carrier['id_carrier']]   = $carrier;
//        }
//        $this->_html .= $this->includeDatepicker(array('date_from', 'date_to'), false, 1, 1);
//        $this->_html .= '
//            <a id="planningdelivery_exceptions" name="planningdelivery_exceptions"></a>
//            <form action="'.$_SERVER['REQUEST_URI'].'" method="post" name="exception_form">
//            <fieldset><legend><img src="'.$this->_path.'img/prefs.gif" alt="" title="" />'.$this->l('Exceptions').'&nbsp;<a href="javascript:{}" onclick="$.scrollTo(\'#planningdelivery_top\', 1200);"><img src="'._PS_ADMIN_IMG_.'up.gif" alt="" /></a></legend>
//                    <p>'.$this->l('Add as many exceptions as necessary.').'</p><br />
//                    <label>'.$this->l('Exception').'</label>
//                    <div class="margin-form">
//                            <label class="t" for="id_carrier_exception">'.($this->checkVersion() > '1.5' ? '<i class="icon-AdminParentShipping"></i>' : '<img src="../img/t/AdminParentShipping.gif" />').''.$this->l('Carrier').'</label>
//                            <select name="id_carrier_exception" id="id_carrierException">';
//        foreach ($carriers as $carrier)
//            if (in_array($carrier['id_carrier'], $availableCarriers))
//                $this->_html .= '
//                                            <option value="'.(int)($carrier['id_carrier']).'"'.((int)($carrier['id_carrier']) == (int)($this->id_carrier) ? ' selected="selected" ' : '').'>'.$carrier['name'].' (id:'.$carrier['id_carrier'].')</option>';
//        $this->_html .= '
//                            </select>
//                            <label class="t" for="nb_commandes"> '.$this->l('Nombre des commandes').'</label>
//                            <input type="text" name="nb_commandes" id="nb_commandes"/>
//                            <label class="t" for="date_from"> '.$this->l('Date from').'</label>
//                            <input type="text" name="date_from" id="date_from"/>
//                            <label class="t" for="date_to"> '.$this->l('to').'</label>
//                            <input type="text" name="date_to" id="date_to"/>
//                            <input type="submit" name="submitException" value="'.$this->l('Add').'" class="button" />
//                    </div>';
//
//        $exceptions = PlanningDeliveryByCarrierException::get();
//
//        $len = count($exceptions);
//        if ($len)
//        {
//            $this->_html .= '
//                            <input type="hidden" name="id_planning_delivery_carrier_exception" id="id_planning_delivery_carrier_exception" />
//                            <input type="hidden" name="exception_name" id="exception_name" />
//                            <input type="hidden" name="exception_action" id="slot_action" />
//                            <br /><table class="table" style="width:100%;">
//                            <thead>
//                            <tr>
//                             <th style="width:95%;">'.$this->l('Restricted dates').'</th>
//                             <th style="width:5%;">'.$this->l('Actions').'</th>
//                            </tr>
//                            </thead>
//                            <tbody>';
////                            foreach ($carriers as $carrier){
//
////                                    $exceptions = PlanningDeliveryByCarrierException::get($carrier['id_carrier']);
//            foreach ($exceptions as $exception){
//                if (in_array($exception['id_carrier'], $availableCarriers)){
//                    $html_updateNbCommand = '<span class="nb_commande" style="float: right;">'
//                        . '<label>'.( isset($carrierAndNbCommande[(int)$exception['id_planning_delivery_carrier_exception']])? '<span style="color:red">(nb commande : '.$carrierAndNbCommande[(int)$exception['id_planning_delivery_carrier_exception']]['real_nb_commande'].')</span>':'')
//                        .'nombre des commandes </label>'
//                        . '<input type="text" name="nb_commande" value="'.$exception['nb_commandes'].'" style="width:70px"/> '
//                        . ' <span class="updaet_nb_commande button" data-id="'.(int)($exception['id_planning_delivery_carrier_exception']).'"><i class="icon-pencil"></i> modifier</span>'
//                        . '</span>';
//                    $this->_html .= '
//                                            <tr>
//                                             <td> '.($exception['date_from'] == $exception['date_to'] ? $this->dateFR_S($exception['date_from']) : $this->l('From').' '.$this->dateFR_S($exception['date_from']).' '.$this->l('to').' '.$this->dateFR_S($exception['date_to'])).', '.$this->l('Carrier').' <strong>'.$mycarrier[$exception['id_carrier']]['name'].' (id:'.$exception['id_carrier'].')</strong>'
//                        .$html_updateNbCommand
//                        .'</td>
//                                             <td style="text-align:center;"><a href="javascript:;" onclick="deleteException(\''.(int)($exception['id_planning_delivery_carrier_exception']).'\');"><img src="'.$this->_path.'img/delete.png" alt="'.$this->l('Delete').'" /></a></td>
//                                            </tr>';
//                }
//            }
////                            }
//            $this->_html .= '
//                            </tbody>
//                            </table>';
//        }
//        $this->_html .= '
//                    </fieldset>
//            </form><br />
//            <script type="text/javascript">
//                $(document).ready(function(){
//                    $(".updaet_nb_commande").css("cursor","pointer").click(function(e){
//                        e.preventDefault();
//                        var $me = $(this),id = $me.data("id"),nbCommande = $me.parent().find("input").val();
//
//                        $.ajax({
//                            url : "'.$this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'",
//                            data : {
//                                    ajax : true,
//                                    action : "Updatenbcommande",
//                                    id_planning_delivery_carrier_exception : id,
//                                    nb_commande : nbCommande,
//                                    },
//                            type : "POST",
//                            dataType: "json",
//                            success : function(data){
//                                try {
//                                    if(data.success){
//                                        showSuccessMessage(data.msg);
//                                    }else{
//                                        showErrorMessage(data.msg);
//                                    }
//                                }catch(err) {
//                                    showNoticeMessage("erreur");
//                                }
//                            },
//                            error : function(msg){
//                                showNoticeMessage("erreur ");
//                            }
//                        });
//
//                    });
//                })
//            </script>
//            ';
//    }

    protected function _displayRetourFormException()
    {
        $carriers = Carrier::getCarriers((int) $this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);

        $carrier_html_list = "<select id='id_carrier' name='id_carrier'>";
        if ($carriers) {
            foreach ($carriers as $row) {
                $carrier_html_list .= "<option value='" . $row['id_carrier'] . "' >" . $row['name'] . " (id: " . $row['id_carrier'] . ")</option>";
            }
        }
        $carrier_html_list .= "</select>";

        $this->_html .= $this->includeDatepicker(array('date_from_r', 'date_to_r'), false, 1, 1);
        $this->_html .= '
		<a id="planningdelivery_exceptions" name="planningdelivery_exceptions"></a>
		<form action="' . $_SERVER['REQUEST_URI'] . '" method="post" name="retour_exception_form">
		<fieldset><legend><img src="' . $this->_path . 'img/prefs.gif" alt="" title="" />' . $this->l('Retours') . '&nbsp;<a href="javascript:{}" onclick="$.scrollTo(\'#planningdelivery_top\', 1200);"><img src="' . _PS_ADMIN_IMG_ . 'up.gif" alt="" /></a></legend>
			<p>' . $this->l('Add as many exceptions as necessary.') . '</p><br />
			<label>' . $this->l('Date de livraison') . '</label>
			<div class="margin-form">
				<label class="t" for="date_from_r"> ' . $this->l('Date from') . '</label>
				<br>
				<input type="text" name="date_from" id="date_from_r"/>
				<br>
				<label class="t" for="date_to_r"> ' . $this->l('to') . '</label>
				<br>
				<input type="text" name="date_to" id="date_to_r"/>
				<br>
				<label class="t" for="max_places"> ' . $this->l('max places') . '</label>
				<br>
				<input type="number" name="max_places" id="max_places"/>
				<br>
				<label class="t" for="id_carrier">' . $this->l('carrier') . '</label>
				<br>
				' . $carrier_html_list . '
				<br>
				<input type="submit" name="submitRetourException" value="' . $this->l('Add') . '" class="button" />
			</div>';

        $exceptions = PlanningRetourByCarrierException::get();

        $len = count($exceptions);
        if ($len) {
            $this->_html .= '
				<input type="hidden" name="id_planning_retour_carrier_exception" id="id_planning_retour_carrier_exception" />
				<input type="hidden" name="exception_name" id="exception_name" />
				<input type="hidden" name="retour_exception_action" id="slot_action" />
				<button id="toggleRetourTable">Afficher/Cacher Tableau</button>
				<br /><table class="table" id="retourTable" style="width:100%;">
				<thead>
				<tr>
				 <th style="width:40%;">' . $this->l('Restricted dates') . '</th>
				 <th style="width:10%;">' . $this->l('Commands') . '</th>
				 <th style="width:15%;">' . $this->l('Max places') . '</th>
				 <th style="width:45%;">' . $this->l('Carrier') . '</th>
				 <th style="width:5%;">' . $this->l('Actions') . '</th>
				</tr>
				</thead>
				<tbody>';
            foreach ($exceptions as $exception) {
//                $sql = "SELECT
//                            pde.`id_planning_retour_carrier_exception`,
//                            pde.`id_carrier`,
//                            pde.`date_from`,
//                            pde.`date_to`,
//                            pde.`max_places`,
//                            COUNT(*) AS real_nb_commande
//                        FROM
//                            `ps_planning_retour_carrier_exception` pde, `ps_planning_delivery_carrier` pd, `ps_orders` o, `ps_carrier` pc
//                        WHERE
//                            1 = 1
//                                AND pd.`date_retour` BETWEEN pde.`date_from` AND pde.`date_to`
//                                AND pd.`id_order` = o.`id_order`
//                                AND pc.`id_carrier` = o.`id_carrier`
//                                AND o.`id_carrier` = pde.`id_carrier`
//                                AND pc.`deleted` = 0
//                                AND pc.`active` = 1
//                                AND o.`current_state` NOT IN (6)
//                                AND pde.`id_planning_retour_carrier_exception` = {$exception['id_planning_retour_carrier_exception']}";
//
//                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
//                $exception['nb_commandes'] = $result[0]["real_nb_commande"];

                $isMaxReached = ($exception['nb_commandes'] >= $exception['max_places']);

                $this->_html .= '
					<tr>
					 <td> ' . ($exception['date_from'] == $exception['date_to'] ? $this->dateFR_S($exception['date_from']) : $this->l('From') . ' ' . $this->dateFR_S($exception['date_from']) . ' ' . $this->l('to') . ' ' . $this->dateFR_S($exception['date_to'])) . '</td>
					 <td> ' . '<span style="' . ($isMaxReached ?
                        'color:white;background:red;padding:5px;border-radius:5px;margin:1px;display:inline-block;'
                        :
                        'color:white;background:green;padding:5px;border-radius:5px;margin:1px;display:inline-block;'
                    ) . ';">' . $exception['nb_commandes'] . '</span></td>
					 <td> <a class="texte_retour" data-type="text" data-pk="' . $exception['id_planning_retour_carrier_exception'] . '">' . $exception['max_places'] . '</a></td>
					 <td> ' . $exception['name'] . '(id: ' . $exception['id_carrier'] . ')</td>
					 <td style="text-align:center;"><a href="javascript:;" onclick="deleteRetourException(\'' . (int) ($exception['id_planning_retour_carrier_exception']) . '\');"><img src="' . $this->_path . 'img/delete.png" alt="' . $this->l('Delete') . '" /></a></td>
					</tr>';
            }
            $this->_html .= '
				</tbody>
				</table>';
        }

        $token = Tools::getAdminTokenLite('AdminPlanningDeliveryByCarrier');

        $this->_html .= "
			</fieldset>
		</form><br />
		<script type=\"text/javascript\">
        $(document).ready(function() {
            $('#toggleRetourTable').on('click',function (e){
                e.preventDefault();
                $('#retourTable').toggle(500);
            });
            $('.texte_retour').editable({
                url: 'index.php?controller=AdminPlanningDeliveryByCarrier&action=updatemaxplacesretour&ajax=true&token={$token}'
            });
        });
        </script>
		";
    }

//    protected function _displayRetourFormException()
//    {
//        $availableCarriers = explode(', ', Configuration::get('PLANNING_DELIVERY_CARRIERS'));
//        $carriers = Carrier::getCarriers((int)$this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);
//        $mycarrier = array();
//        $carrierAndNbCommande = array();
//        foreach ( PlanningRetourByCarrierException::getAllNbCommandeAndRealNbCommand() as $r){
//            $carrierAndNbCommande[$r['id_planning_retour_carrier_exception']] = $r;
//        }
//        foreach ($carriers as $carrier){
//            $mycarrier[$carrier['id_carrier']]   = $carrier;
//        }
//        $this->_html .= $this->includeDatepicker(array('date_from_retour', 'date_to_retour'), false, 1, 1);
//        $this->_html .= '
//		<a id="planningretour_exceptions" name="planningretour_exceptions"></a>
//		<form action="'.$_SERVER['REQUEST_URI'].'" method="post" name="retour_exception_form">
//		<fieldset><legend><img src="'.$this->_path.'img/prefs.gif" alt="" title="" />'.$this->l('Retour').'&nbsp;<a href="javascript:{}" onclick="$.scrollTo(\'#planningretour_top\', 1200);"><img src="'._PS_ADMIN_IMG_.'up.gif" alt="" /></a></legend>
//			<p>'.$this->l('Add as many exceptions as necessary.').'</p><br />
//			<label>'.$this->l('Date de retour').'</label>
//			<div class="margin-form">
//				<label class="t" for="id_carrier_exception">'.($this->checkVersion() > '1.5' ? '<i class="icon-AdminParentShipping"></i>' : '<img src="../img/t/AdminParentShipping.gif" />').''.$this->l('Carrier').'</label>
//                                <select name="id_carrier_retour" id="id_carrierRetour">';
//        foreach ($carriers as $carrier)
//            if (in_array($carrier['id_carrier'], $availableCarriers))
//                $this->_html .= '
//                                                <option value="'.(int)($carrier['id_carrier']).'"'.((int)($carrier['id_carrier']) == (int)($this->id_carrier) ? ' selected="selected" ' : '').'>'.$carrier['name'].' (id:'.$carrier['id_carrier'].')</option>';
//        $this->_html .= '
//                                </select>
//                                <label class="t" for="nb_commandes"> '.$this->l('Nombre des commandes').'</label>
//                                <input type="text" name="nb_commandes" id="nb_commandes"/>
//				<label class="t" for="date_from_retour"> '.$this->l('Date from').'</label>
//				<input type="text" name="date_from" id="date_from_retour"/>
//				<label class="t" for="date_to_retour"> '.$this->l('to').'</label>
//				<input type="text" name="date_to" id="date_to_retour"/>
//				<input type="submit" name="submitRetourException" value="'.$this->l('Add').'" class="button" />
//			</div>';
//
//        $exceptions = PlanningRetourByCarrierException::get();
//
//        $len = count($exceptions);
//        if ($len)
//        {
//            $this->_html .= '
//				<input type="hidden" name="id_planning_retour_carrier_exception" id="id_planning_retour_carrier_exception" />
//				<input type="hidden" name="retour_exception_name" id="retour_exception_name" />
//				<input type="hidden" name="retour_exception_action" id="slot_action_r" />
//				<br /><table class="table" style="width:100%;">
//				<thead>
//				<tr>
//				 <th style="width:95%;">'.$this->l('Restricted dates').'</th>
//				 <th style="width:5%;">'.$this->l('Actions').'</th>
//				</tr>
//				</thead>
//				<tbody>';
////                               foreach ($carriers as $carrier){
//
////                                        $exceptions = PlanningRetourByCarrierException::get($carrier['id_carrier']);
//            foreach ($exceptions as $exception){
//                if (in_array($exception['id_carrier'], $availableCarriers)){
//                    $html_updateNbCommand = '<span class="nb_commande" style="float: right;">'
//                        . '<label>'.( isset($carrierAndNbCommande[(int)$exception['id_planning_retour_carrier_exception']])? '<span style="color:red">(nb commande : '.$carrierAndNbCommande[(int)$exception['id_planning_retour_carrier_exception']]['real_nb_commande'].')</span>':'')
//                        .'nombre des commandes </label>'
//                        . '<input type="text" name="nb_commande" value="'.$exception['nb_commandes'].'" style="width:70px"/> '
//                        . ' <span class="updaet_nb_commande_retour button" data-id="'.(int)($exception['id_planning_retour_carrier_exception']).'"><i class="icon-pencil"></i> modifier</span>'
//                        . '</span>';
//                    $this->_html .= '
//                                                <tr>
//                                                 <td> '.($exception['date_from'] == $exception['date_to'] ? $this->dateFR_S($exception['date_from']) : $this->l('From').' '.$this->dateFR_S($exception['date_from']).' '.$this->l('to').' '.$this->dateFR_S($exception['date_to'])).', '.$this->l('Carrier').' <strong>'.$mycarrier[$exception['id_carrier']]['name'].' (id:'.$exception['id_carrier'].')</strong>'
//                        .$html_updateNbCommand.
//                        '</td>
//                                                 <td style="text-align:center;"><a href="javascript:;" onclick="deleteRetourException(\''.(int)($exception['id_planning_retour_carrier_exception']).'\');"><img src="'.$this->_path.'img/delete.png" alt="'.$this->l('Delete').'" /></a></td>
//                                                </tr>';
//                }
//            }
////                                }
//            $this->_html .= '
//				</tbody>
//				</table>';
//        }
//        $this->_html .= '
//			</fieldset>
//		</form><br />
//                <script type="text/javascript">
//                    $(document).ready(function(){
//                        $(".updaet_nb_commande_retour").css("cursor","pointer").click(function(e){
//                            e.preventDefault();
//                            var $me = $(this),id = $me.data("id"),nbCommande = $me.parent().find("input").val();
//
//                            $.ajax({
//                                url : "'.$this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'",
//                                data : {
//                                        ajax : true,
//                                        action : "Updatenbcommanderetour",
//                                        id_planning_retour_carrier_exception : id,
//                                        nb_commande : nbCommande,
//                                        },
//                                type : "POST",
//                                dataType: "json",
//                                success : function(data){
//                                    try {
//                                        if(data.success){
//                                            showSuccessMessage(data.msg);
//                                        }else{
//                                            showErrorMessage(data.msg);
//                                        }
//                                    }catch(err) {
//                                        showNoticeMessage("erreur");
//                                    }
//                                },
//                                error : function(msg){
//                                    showNoticeMessage("erreur ");
//                                }
//                            });
//
//                        });
//                    })
//                </script>';
//    }

    protected function _displayInformation()
    {
        $this->_html .= '
		<a id="planningdelivery_information" name="planningdelivery_information"></a>
		<form action="#" method="post">
		<fieldset>
			<legend><img src="' . _PS_ADMIN_IMG_ . 'warning.gif" alt="!" />' . $this->l('Information') . '&nbsp;<a href="javascript:{}" onclick="$.scrollTo(\'#planningdelivery_top\', 1200);"><img src="' . _PS_ADMIN_IMG_ . 'up.gif" alt="" /></a></legend>
			<hr/>
			<p>' . $this->_displaySpeedyWeb() . '</p>
		</fieldset>
		</form>';
    }

    public function _displayQuickLinks()
    {
        $this->_html = '<a id="planningdelivery_top" name="planningdelivery_top"></a>';
        $this->_html .= '<h2>' . $this->displayName . '</h2>';
        if ($this->checkVersion() <= '1.5') $this->_html .= '<script type="text/javascript" src="' . _PS_JS_DIR_ . 'jquery/jquery.scrollto.js"></script>';
        if (!$this->id_carrier > 0) {
            $this->_html .= '
			<p>
				<a href="javascript:{}" onclick="$.scrollTo(\'#planningdelivery_exceptions\', 1200);"><img src="' . _PS_ADMIN_IMG_ . 'down.gif" alt="" />&nbsp;' . $this->l('Date de livraison & Retour') . '</a>
				&nbsp;
				<a href="javascript:{}" onclick="$.scrollTo(\'#planningdelivery_information\', 1200);"><img src="' . _PS_ADMIN_IMG_ . 'down.gif" alt="" />&nbsp;' . $this->l('Go to see informations') . '</a>
			</p><br/>';
        } else {
            $this->_html .= '
			<p>
				<a href="javascript:{}" onclick="$.scrollTo(\'#planningdelivery_available_days\', 1200);"><img src="' . _PS_ADMIN_IMG_ . 'down.gif" alt="" />&nbsp;' . $this->l('Go to set Available Days') . '</a>
				&nbsp;
				<a href="javascript:{}" onclick="$.scrollTo(\'#planningdelivery_slots\', 1200);"><img src="' . _PS_ADMIN_IMG_ . 'down.gif" alt="" />&nbsp;' . $this->l('Go to set slots') . '</a>
				&nbsp;
				<a href="javascript:{}" onclick="$.scrollTo(\'#planningdelivery_day_slots\', 1200);"><img src="' . _PS_ADMIN_IMG_ . 'down.gif" alt="" />&nbsp;' . $this->l('Go to set days/slots') . '</a>
				&nbsp;
			</p><br/>';
        }
    }

    public function _displaySpeedyWeb()
    {
        return $this->l('Realisation : ') . '<a href="http://www.speedyweb.fr" title="Cr&eacute;ation et r&eacute;f&eacute;rencement de sites web &agrave; Perpignan, Lille et Paris - SpeedyWeb">SpeedyWeb</a>';
    }

    /***************************************************************************************************************/

    public function getSlotsAvalaiblesByDateAndCarrier($id_carrier, $date_delivery, $id_lang)
    {
        $slotsAvalaibles = array();
        if (Validate::isDate($date_delivery)) {
            $dt         = new DateTime($date_delivery);
            $day_number = $dt->format('w');
            if ($day_number == 0) $dayNumber = 7;
            $slots = PlanningDeliverySlotByCarrier::getByDay($day_number, $id_lang, $id_carrier);
            if (isset($slots) === true && count($slots)) {
                $displaySlots = false;
                foreach ($slots as $slot)
                    if (!PlanningDeliverySlotByCarrier::isFull($date_delivery, $slot, $id_carrier))
                        $displaySlots = true;
                if ($displaySlots) {
                    foreach ($slots as $slot)
                        if (!PlanningDeliverySlotByCarrier::isFull($date_delivery, $slot, $id_carrier))
                            $slotsAvalaibles[(int) ($slot['id_planning_delivery_carrier_slot'])] = htmlspecialchars(PlanningDeliverySlotByCarrier::hideSlotsPosition($slot['name']), ENT_COMPAT, 'UTF-8');
                }
            }
        }
        return $slotsAvalaibles;
    }

    public function bindDatepicker($id, $time, $format, $onAdminPlanningDelivery, $id_carrier = false)
    {
        $return = '';
        if ($onAdminPlanningDelivery == 1) {
            $unavalaibleDates = PlanningDeliveryByCarrierException::getDates();
            $unavailableDays  = str_replace('7', '0', Configuration::get('PLANNING_DELIVERY_UNAV_DAYS' . $id_carrier));
            $return           .= '
				var unavalaibleDates = [' . $unavalaibleDates . '];
				var unavalaibleDays = [' . $unavailableDays . '];
				var delta_days = ' . ((int) (Configuration::get('PLANNING_DELIVERY_DAYS_BEFORE' . $id_carrier)) - 1) . ';
				var delta_days_end = delta_days + ' . (int) (Configuration::get('PLANNING_DELIVERY_NUMB_DAYS' . $id_carrier)) . ';
				var firstDate = addDaysToDate(new Date(), delta_days);
				var lastDate = addDaysToDate(new Date(), delta_days_end);
				function addDaysToDate(old_date, delta_days) {
					var dMyDate = old_date;
					dMyDate.setDate((dMyDate.getDate()+delta_days));
					return dMyDate;
				}
				function enableDays(date) {
					var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();
					if (d < 10){d = "0"+d;}
					if (m+1 < 10){m = "0"+(m+1);}
					else{m = (m+1);}
					var day = date.getDay();
					if ($.inArray(y + \'-\' + m + \'-\' + d, unavalaibleDates) != -1 && $.inArray(day, unavalaibleDays) == -1) {
						return [true];
					}
					return [false];
				}
				function showSlots(dateText, inst){
					var path = "' . __PS_BASE_URI__ . 'modules/planningdeliverybycarrier/";
					var id_lang = ' . (int) ((int) $this->context->language->id) . ';
					var format = ' . $format . ';
					getDaySlot(path, dateText, format, id_lang, ' . $onAdminPlanningDelivery . ', ' . $id_carrier . ');
				}';
        } elseif ($onAdminPlanningDelivery == 2) {
            $return .= '
			$(function() {
				$("#' . $id . '").datepicker({
					prevText:"",
					nextText:"",
					dateFormat:"yy-mm-dd"' . ($time ? '+time' : '') . '});
			});';
            return $return;
        }
        $onSelect = (Configuration::get('PLANNING_DELIVERY_SLOT_' . $id_carrier)) ? "onSelect: showSlots, \n" : '';
        $dFormat  = (1 == $format) ? 'dd/mm/yy' : 'mm/dd/yy';
        $return   .= '
		$(function(){                
			$("#date_delivery").datepicker({
				prevText:"",
				nextText:"",
				beforeShowDay: enableDays, ' .
            $onSelect . '
				dateFormat:"' . $dFormat . '"' . ($time ? '+time' : '') . '});
		});
		$("#ui-datepicker-div").css("clip", "auto");';
//        d($return);
        return $return;
    }

    public function includeDatepicker($id, $time = false, $format = 1, $onAdminPlanningDelivery = 0, $id_carrier = false)
    {
        $return       = '';
        $carriers_ids = array();
        if ($onAdminPlanningDelivery == 1) {
            if ($this->checkVersion() <= '1.5') $return = '<script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>';
            $iso = Db::getInstance()->getValue('SELECT iso_code FROM ' . _DB_PREFIX_ . 'lang WHERE `id_lang` = ' . (int) ((int) $this->context->language->id));
            if ($this->checkVersion() <= '1.5') if ($iso != 'en') $return .= '<script type="text/javascript" src="' . __PS_BASE_URI__ . 'js/jquery/datepicker/ui/i18n/ui.datepicker-' . $iso . '.js"></script>';
            $return .= '<script type="text/javascript">';
            if (is_array($id)) foreach ($id as $id2) $return .= $this->bindDatepicker($id2, $time, $format, 2);
            else $return .= $this->bindDatepicker($id, $time, $format, $onAdminPlanningDelivery, $id_carrier);
            $return .= '</script>';
        } else {
            if (!$id_carrier) {

                $return = '<script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>';
                $iso    = Db::getInstance()->getValue('SELECT iso_code FROM ' . _DB_PREFIX_ . 'lang WHERE `id_lang` = ' . (int) ((int) $this->context->language->id));
                if ($this->checkVersion() <= '1.5') if ($iso != 'en') $return .= '<script type="text/javascript" src="' . __PS_BASE_URI__ . 'js/jquery/ui/i18n/jquery.ui.datepicker-' . $iso . '.js"></script>';

                $planningCarriers = explode(', ', Configuration::get('PLANNING_DELIVERY_CARRIERS'));
                // getCarriers($id_lang, $active = false, $delete = false, $id_zone = false, $ids_group = null, $modules_filters = self::PS_CARRIERS_ONLY)
                $carriers             = Carrier::getCarriers((int) $this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);
                $carrierAndNbCommande = $this->getCarrierAndNbCommande();
//                            var_dump($carrierAndNbCommande);exit;
                $return .= '<script type="text/javascript">';

                $unavalaibleDates = "";
                $unavailableDays  = "";
                $cart             = $this->context->cart;
                if (class_exists('Region'))
                    $region = Region::getRegionByNpa($cart->npa);
                if (empty($region) && is_numeric($cart->npa)) {
                    $region = ["id_carrier" => Configuration::get('TUNNELVENTE_ID_CARRIER_POST')];
                }
                if (!empty($region)) {
                    $unavalaibleDates .= PlanningDeliveryByCarrierException::getDatesByCarrier($region['id_carrier']);
                    $unavailableDays  = str_replace('7', '0', Configuration::get('PLANNING_DELIVERY_UNAV_DAYS' . $region['id_carrier']));
                }
                $product_list = $cart->getProducts();
                foreach ($product_list as $product) {
                    if ($product["id_product"] == 53) {
                        $post_id = Configuration::get('TUNNELVENTE_ID_CARRIER_POST');
                        $arr     = [];
                        $arr1    = explode(', ', $unavalaibleDates);
                        $arr2    = explode(', ', PlanningDeliveryByCarrierException::getDatesByCarrier($post_id));

                        if (trim($arr1[0]) != "") {
                            $date = DateTime::createFromFormat('"Y-m-d"', $arr2[count($arr2) - 1]);
                            foreach ($arr1 as $obj) {
                                $dateObj = DateTime::createFromFormat('"Y-m-d"', $obj);
                                if ($dateObj > $date) {
                                    break;
                                }
                                $arr[] = $obj;
                            }
                        } else {
                            $arr             = $arr2;
                            $unavailableDays .= ', ' . str_replace('7', '0', Configuration::get('PLANNING_DELIVERY_UNAV_DAYS' . $post_id));
                        }

//                        $unavalaibleDates = implode(", ", trim($arr1[0]) != ""? array_intersect($arr1, $arr2): $arr2);
                        $unavalaibleDates = implode(", ", $arr);
                        break;
                    }
                }

                foreach ($carriers as $carrier) {
                    $id_carrier = $carrier['id_carrier'];
                    if (in_array($id_carrier, $planningCarriers)) {
                        $unavalaibleDates = PlanningDeliveryByCarrierException::getDatesByCarrier($id_carrier);
                        $unavailableDays = str_replace('7', '0', Configuration::get('PLANNING_DELIVERY_UNAV_DAYS'.$carrier['id_carrier']));
                        $return .= '
                                            //TODO: get dates by id_carrier
                                            var unavalaibleDates' . $id_carrier . ' = [' . $unavalaibleDates . '];
                                            var unavalaibleDays' . $id_carrier . ' = [' . $unavailableDays . '];
                                            var delta_days' . $id_carrier . ' = ' . ((int) (Configuration::get('PLANNING_DELIVERY_DAYS_BEFORE' . $id_carrier)) - 1) . ';' .
                            ((float) (date('H.i')) >= (float) (str_replace('h', '.', $this->today_max_hour)) ? 'delta_days' . $id_carrier . ' += 1;' : '') . '
                                            var delta_days_end' . $id_carrier . ' = delta_days' . $id_carrier . ' + ' . (int) (Configuration::get('PLANNING_DELIVERY_NUMB_DAYS' . $id_carrier)) . ';
                                            var firstDate' . $id_carrier . ' = addDaysToDate(new Date(), delta_days' . $id_carrier . ');
                                            var lastDate' . $id_carrier . ' = addDaysToDate(new Date(), delta_days_end' . $id_carrier . ');';
                    }
                }
//                d($unavalaibleDates);
                $return .= '
                            function showSlots(dateText, inst) {
                                    var id_carrier_checked = getIdCarrierChecked();
                                    var path = "' . __PS_BASE_URI__ . 'modules/planningdeliverybycarrier/";
                                    var id_lang = ' . (int) ($this->context->language->id) . ';
                                    var format = ' . $format . ';
                                    getDaySlot(path, dateText, format, id_lang, ' . $onAdminPlanningDelivery . ', id_carrier_checked);
                            };
                            var datesRemove = ' . json_encode($carrierAndNbCommande) . ' ;
                            </script>';
            } else {
                $availableCarriers = explode(', ', Configuration::get('PLANNING_DELIVERY_CARRIERS'));
                foreach ($availableCarriers as $carrier) $carriers_ids[] = $carrier;
                if (in_array($id_carrier, $carriers_ids)) {
                    if (is_array($id)) foreach ($id as $id2) $return .= $this->bindDatepicker($id2, $time, $format, $onAdminPlanningDelivery, $id_carrier);
                    else $return .= $this->bindDatepicker($id, $time, $format, $onAdminPlanningDelivery, $id_carrier);
                    $return .= '$("#choose_delivery_date").fadeIn(\'slow\');';
                }
            }
        }
        return $return;
    }

    protected function getCarrierAndNbCommande()
    {
        if (!count($this->_carrierAndNbCommande)) {
            $this->_carrierAndNbCommande = PlanningDeliveryByCarrierException::getNbCommandeAndRealNbCommand();
        }
        return $this->_carrierAndNbCommande;
    }

    protected $_carrierAndNbCommande = array();

    public function getPrestaVersion($type = 0)
    {
        $version = _PS_VERSION_;
        /* 1.5 / 1.6 / 1.x */
        if (1 == $type) $version = Tools::substr(sprintf('%0-6s', (string) _PS_VERSION_), 0, 3);
        return $version;
    }

    public function sendMail($mailName, $params)
    {
        $context = Context::getContext()->cloneContext();

        switch ($mailName) {
            case 'date_delivery':
                $id_customer = (int) $params['id_customer'];
                if ($id_customer <> 0) {
                    $user = new Customer ($id_customer);

                    //todo added for using date retour in email
                    switch ((int) $params['lang']) {
                        case 2://for FR
                            $dateRetourText = "Date de Retour";
                            break;
                        default:
                            $dateRetourText = "Return Date";
                    }
                    $date_retour = $params['date_retour'] == null ? "" :
                        "<span style=\"color:#333\"><strong>$dateRetourText :</strong></span> " . $this->dateFR_S($params['date_retour']) . "<br />";

                    $paramsArr = array(
                        '{firstname}'     => $user->firstname,
                        '{lastname}'      => $user->lastname,
                        '{date_delivery}' => $this->dateFR_S($params['date_delivery']),
                        '{date_retour}'   => $date_retour
                    );

                    Mail::Send((int) $params['lang'],
                        'date_delivery',
                        $this->l('Your delivery date'),
                        $paramsArr,
                        $user->email, null, (string) Configuration::get('PS_SHOP_EMAIL'),
                        (string) Configuration::get('PS_SHOP_NAME'), null, null, dirname(__FILE__) . '/mails/' . $this->getPrestaVersion(1) . '/'
                    );
                }
                break;

            case 'date_delivery_slot':
                $id_customer = (int) $params['id_customer'];
                if ($id_customer <> 0) {
                    $user                  = new Customer ($id_customer);
                    $id_shop               = (int) $user->id_shop;
                    $context->shop->id     = $id_shop;
                    $context->language->id = (int) $params['lang'];
                    if (file_exists(dirname(__FILE__) . '/mails/' . $this->getPrestaVersion(1) . '/fr/date_delivery_slot.txt')
                        && file_exists(dirname(__FILE__) . '/mails/' . $this->getPrestaVersion(1) . '/fr/date_delivery_slot.html'))
                        Mail::Send((int) $params['lang'],
                            'date_delivery_slot',
                            $this->l('Your delivery date'),
                            array(
                                '{firstname}'     => $user->firstname, '{lastname}' => $user->lastname, '{date_delivery}' => $this->dateFR_S($params['date_delivery']),
                                '{delivery_slot}' => htmlspecialchars(PlanningDeliverySlotByCarrier::hideSlotsPosition($params['delivery_slot']), ENT_COMPAT, 'UTF-8')
                            ),
                            (string) $user->email,
                            null,
                            (string) Configuration::get('PS_SHOP_EMAIL', null, null, $id_shop),
                            (string) Configuration::get('PS_SHOP_NAME', null, null, $id_shop),
                            null,
                            null,
                            dirname(__FILE__) . '/mails/' . $this->getPrestaVersion(1) . '/',
                            false,
                            $id_shop
                        );
                }
                break;

            default:
                return;
        }
    }

    public function dateFR_S($time)
    {
        setlocale(LC_TIME, 'fr');
        $mi = strftime('%m', strtotime($time));
        switch ($mi) {
            case '1' :
                $mi = $this->l('January');
                break;
            case '2' :
                $mi = $this->l('February');
                break;
            case '3' :
                $mi = $this->l('March');
                break;
            case '4' :
                $mi = $this->l('April');
                break;
            case '5' :
                $mi = $this->l('May');
                break;
            case '6' :
                $mi = $this->l('June');
                break;
            case '7' :
                $mi = $this->l('July');
                break;
            case '8' :
                $mi = $this->l('August');
                break;
            case '9' :
                $mi = $this->l('September');
                break;
            case '10' :
                $mi = $this->l('October');
                break;
            case '11' :
                $mi = $this->l('November');
                break;
            case '12' :
                $mi = $this->l('December');
                break;
        }
        $w = strftime('%w', strtotime($time));
        switch ($w) {
            case '1' :
                $w = $this->l('Monday');
                break;
            case '2' :
                $w = $this->l('Tuesday');
                break;
            case '3' :
                $w = $this->l('Wednesday');
                break;
            case '4' :
                $w = $this->l('Thursday');
                break;
            case '5' :
                $w = $this->l('Friday');
                break;
            case '6' :
                $w = $this->l('Saturday');
                break;
            case '0' :
                $w = $this->l('Sunday');
                break;
        }
        $mor1 = strftime('%d ', strtotime($time));
        $mor2 = strftime('%Y', strtotime($time));
        return $w . ' ' . $mor1 . ' ' . $mi . ' ' . $mor2 . ' ';
    }

    #[F]# X_dateformat	ex : X_dateformat("19/09/2011", 'd/m/Y', "y/m/d")
        # @param    $date    	string        Date au format str (selon la liste des paramètres de formatage acceptés)
        # @param    $inFormat   string        Format d'entrée (selon la liste des paramètres de formatage acceptés)
        # @param    $outFormat  string        Format de sortie
        # @return	string	 	Retourne la date formatée au format souhaité
        # PARAMETRES DU FORMAT D'ENTREE ACCEPTES
        # d 	Jour du mois, sur deux chiffres (avec un zéro initial) 	01 à 31
        # j 	Jour du mois sans les zéros initiaux 	1 à 31
        # m 	Mois au format numérique, avec zéros initiaux 	01 à 12
        # n 	Mois sans les zéros initiaux 	1 à 12
        # Y 	Année sur 4 chiffres 	Exemples : 1999 ou 2003
        # y 	Année sur 2 chiffres 	Exemples : 99 ou 03
    public function X_dateformat($date, $inFormat, $outFormat)
    {
        $tab_out       = array('m' => null, 'd' => null, 'y' => null);
        $param_formats = array('d', 'j', 'm', 'n', 'y', 'Y');
        $len           = Tools::strlen($inFormat);
        for ($i = 0; $i < $len; $i++)
            if (!in_array($inFormat[$i], $param_formats)) {
                $sep = $inFormat[$i];
                break;
            }
        $tab_inFormat = explode($sep, $inFormat);
        $tab_date     = explode($sep, $date);
        foreach ($tab_inFormat as $key => $val) {
            switch ($val) {
                case 'd':
                    $tab_out['d'] = $tab_date[$key];
                    break;
                case 'j':
                    $tab_out['d'] = $tab_date[$key];
                    break;
                case 'm':
                    $tab_out['m'] = $tab_date[$key];
                    break;
                case 'n':
                    $tab_out['m'] = $tab_date[$key];
                    break;
                case 'Y':
                    $tab_out['y'] = $tab_date[$key];
                    break;
                case 'y':
                    $tab_out['y'] = $tab_date[$key];
                    break;
            }
        }
//		$outDate = date($outFormat, strtotime($tab_out['y'].'-'.$tab_out['m'].'-'.$tab_out['d']));
//		if ($outDate == false) $outDate = date($outFormat, strtotime($tab_out['y'].'\\'.$tab_out['m'].'\\'.$tab_out['d']));
        $format_date = $tab_out['y'] . '-' . $tab_out['m'] . '-' . $tab_out['d'];
        $format_date = DateTime::createFromFormat(strlen($format_date) > 10 ? "Y-m-d h:i:s" : "Y-m-d", $format_date);
        $outDate     = !$format_date ? $format_date : date($outFormat, $format_date->getTimestamp());

        $return = (!$outDate) ? false : $outDate;
        return $return;
    }

    public function X_dateformat_over($date, $inFormat, $outFormat)
    {
        $date1   = strtr($date, '/', '-');
        $outDate = date($outFormat, strtotime($date1));
        $return  = (!$outDate) ? false : $outDate;
        return $return;
    }

    public function initL()
    {
        $this->l('Thank you indicate your delivery date.');
        $this->l('Thank you to select a time slot for your delivery.');
        $this->l('Time\'s slot');
        $this->l('No slot is available for the dates selected.');
    }

    public function addTabPlanningDeliveryByCarrier()
    {
        $tab = new Tab();
        $tab->class_name = 'AdminPlanningDeliveryByCarrier';
        $tab->module = 'planningdeliverybycarrier';
        $tab->id_parent = 60;
        $langs = Language::getLanguages();
        foreach ($langs as $l)
        {
            $tab->name[$l['id_lang']] = 'Planning de livraisons';
        }
        return $tab->add();
    }

    public function addTabPlanningRetourByCarrier()
    {
        $tab = new Tab();
        $tab->class_name = 'AdminPlanningRetourByCarrier';
        $tab->module = 'planningdeliverybycarrier';
        $tab->id_parent = 60;
        $langs = Language::getLanguages();
        foreach ($langs as $l)
        {
            $tab->name[$l['id_lang']] = 'Planning de retour';
        }
        return $tab->add();
    }

    public function addTabPlanningDeliveryByCarrierMyLittel()
    {
        $tab = new Tab();
        $tab->class_name = 'AdminPlanningDeliveryByCarrierMyLittel';
        $tab->module = 'planningdeliverybycarrier';
        $tab->id_parent = 60;
        $langs = Language::getLanguages();
        foreach ($langs as $l)
        {
            $tab->name[$l['id_lang']] = 'Planning de livraisons de MyLittle';
        }
        return $tab->add();
    }
}

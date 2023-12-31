<?php

if (!defined('_CAN_LOAD_FILES_'))
	exit;

 
require_once(_PS_THEME_DIR_.'/modules/planningdeliverybycarrier/PlanningDeliveryByCarrierException.php');
require_once(_PS_THEME_DIR_.'/modules/planningdeliverybycarrier/PlanningRetourByCarrierException.php');
require_once(_PS_THEME_DIR_.'/modules/planningdeliverybycarrier/PlanningDeliveriesByCarrier.php');

class PlanningDeliveryByCarrier_over extends PlanningDeliveryByCarrier
{
    protected $_carrierAndNbCommande = array();

    public function hookExtraCarrier($params) {

        $errors = array();
        
        $address = new Address((int) ($params['cart']->id_address_delivery));
        $country = new Country((int) ($address->id_country));
        $format = ('US' != $country->iso_code) ? 1 : 2;

        $datepickerJs = $this->includeDatepicker('date_delivery', false, $format, 0);
        $datepickerJs .= $this->includeDatepickerRetour('date_retour', false, $format, 0);
        $opc = Configuration::get('PS_ORDER_PROCESS_TYPE');
        
        $dFormat = (1 == $format) ? 'd/m/Y' : 'm/d/Y';
        $dateRetourRequired = $this->dateRetourRequired($params['cart']->id);
        
        $result = Db::getInstance()->getRow('
			SELECT pd.`date_delivery`, pd.`date_retour`, pds.`name`, pd.`id_planning_delivery_carrier_slot` as id_slot
			FROM `' . _DB_PREFIX_ . 'planning_delivery_carrier` pd
			LEFT JOIN `' . _DB_PREFIX_ . 'planning_delivery_carrier_slot` pds
			ON pd.`id_planning_delivery_carrier_slot` = pds.`id_planning_delivery_carrier_slot`
			WHERE pd.`id_cart` = ' . (int) $this->context->cookie->id_cart);

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
            
            if($dateRetourRequired){
                if (Validate::isDate($result['date_retour'])) { 
                    if (!empty($result['date_retour'])) {                        
                        $date_retour = $this->X_dateformat($result['date_retour'], 'Y-m-d', $dFormat);
                    }
                    $this->smarty->assign('date_retour', $date_retour);
                }else{
                    $errors[] = Tools::displayError($this->l('Date de retour invalid'));
                }
            }            
            
            if (count($errors))
                $this->smarty->assign('pderrors', $errors);
        }
        $this->smarty->assign('dateRetourRequired', $dateRetourRequired);
        $this->smarty->assign('slotRequired', Configuration::get('PLANNING_DELIVERY_SLOT_' . $params['cart']->id_carrier));
        $this->smarty->assign('opc', $opc);
        $this->smarty->assign('format', $format);
        $this->smarty->assign('datepickerJs', $datepickerJs);
        $this->smarty->assign('id_cart', (int) $this->context->cookie->id_cart);
        $this->smarty->assign('ps_version', (int) (sprintf('%0-6s', str_replace('.', '', (string) (_PS_VERSION_)))));
        $this->smarty->assign('path', __PS_BASE_URI__ . 'modules/planningdeliverybycarrier/');
        $this->smarty->assign('id_carrier_post', (int)  Configuration::get('TUNNELVENTE_ID_CARRIER_POST'));
        return $this->display(__FILE__, 'order-carrier.tpl');
    }
    
    public function dateRetourRequired($id_cart) {
        
        $cart = new Cart($id_cart);
        $npa = (int) $cart->npa;
        // TODO:  adil => test si la date de retour est obilgatoir ou non
        
        if (!class_exists("Region")) {
            require_once(_PS_MODULE_DIR_ . '/gszonevente/models/Region.php');
        }
        if (!class_exists("Front")) {
            require_once(_PS_MODULE_DIR_ . '/tunnelvente/controllers/front/Front.php');
        }
        
        $region = Region::getRegionByNpa($npa);
        if(empty($region)){
            $region = array('id_carrier'=>Configuration::get('TUNNELVENTE_ID_CARRIER_POST'));// transporteur Post Si npa n'existe pas
        }
        
       
        $id_carrier_post = (int)  Configuration::get('TUNNELVENTE_ID_CARRIER_POST');
        if($cart){
            foreach ($cart->getProducts() as $product) {
                if($this->testIfRecyclage($product['id_product']) && (int) $region['id_carrier'] != $id_carrier_post )
                    return true;
            }
        }        
        return false;
    }
    
    public function testIfRecyclage($test) {
        $id_product_recyclage1 = (int)  Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_ECOSAPIN_GRATUIT');
        $id_product_recyclage2 = (int)  Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_SAPIN_SUISSE_PAYANT');
        $id_product_recyclage3 = (int)  Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_SAPIN_SUISSE_GRATUIT');
        
        if($test == $id_product_recyclage1 || $test == $id_product_recyclage2 || $test == $id_product_recyclage3){
            return TRUE;
        }
        return FALSE;
    }
    
    public function hookDisplayPDFInvoice($params) {
        $orderInvoice = $params['object'];
        $order = new Order($orderInvoice->id_order);
        $availableCarriers = explode(', ', Configuration::get('PLANNING_DELIVERY_CARRIERS'));
        $carriers_ids = array();
        foreach ($availableCarriers as $carrier)
                $carriers_ids[] = $carrier;
        if (in_array($order->id_carrier, $carriers_ids))
        {
                $sql = 'SELECT pd.*, pds.`name` FROM `'._DB_PREFIX_.'planning_delivery_carrier` pd
                        LEFT JOIN `'._DB_PREFIX_.'planning_delivery_carrier_slot` pds
                        ON pd.`id_planning_delivery_carrier_slot` = pds.`id_planning_delivery_carrier_slot`
                        WHERE `id_order` = '.(int)($orderInvoice->id_order);
                $result = Db::getInstance()->getRow($sql);
                if ($result)
                {
                        $this->smarty->assign('date_retour', ( !empty($result['date_retour'])) ? $result['date_retour'] : '-' ) ;
                        $this->smarty->assign('date_delivery', $result['date_delivery']);				
                        $this->smarty->assign('delivery_slot', (!empty($result['name']) ? PlanningDeliverySlotByCarrier::hideSlotsPosition($result['name']) : '-'));
                        return $this->display(__FILE__, 'pdf-invoice.tpl');
                }
        }        
    }
    
    protected function _displayFormGlobal() {
        $this->_displayFormCarrierGoToSettings();
        $this->_displayFormCarriersGlobalSettings();
        $this->_displayFormException();        
        $this->_displayFormDateRetour();
        $this->_displayInformation();
        return ($this->_html);
    }
    
    protected function _displayFormDateRetour()
	{
            $availableCarriers = explode(', ', Configuration::get('PLANNING_DELIVERY_CARRIERS'));
            $carriers = Carrier::getCarriers((int)$this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);
            $mycarrier = array();
            $carrierAndNbCommande = array();
            foreach ( PlanningRetourByCarrierExceptionOver::getAllNbCommandeAndRealNbCommand() as $r){
                $carrierAndNbCommande[$r['id_planning_retour_carrier_exception']] = $r;  
            }
            foreach ($carriers as $carrier){
                   $mycarrier[$carrier['id_carrier']]   = $carrier;
            }
		$this->_html .= $this->includeDatepicker(array('date_from_retour', 'date_to_retour'), false, 1, 1);
		$this->_html .= '
		<a id="planningretour_exceptions" name="planningretour_exceptions"></a>
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post" name="retour_exception_form">
		<fieldset><legend><img src="'.$this->_path.'img/prefs.gif" alt="" title="" />'.$this->l('Retour').'&nbsp;<a href="javascript:{}" onclick="$.scrollTo(\'#planningretour_top\', 1200);"><img src="'._PS_ADMIN_IMG_.'up.gif" alt="" /></a></legend>
			<p>'.$this->l('Add as many exceptions as necessary.').'</p><br />
			<label>'.$this->l('Date de retour').'</label>
			<div class="margin-form">
				<label class="t" for="id_carrier_exception">'.($this->checkVersion() > '1.5' ? '<i class="icon-AdminParentShipping"></i>' : '<img src="../img/t/AdminParentShipping.gif" />').''.$this->l('Carrier').'</label>
                                <select name="id_carrier_retour" id="id_carrierRetour">';
                                foreach ($carriers as $carrier)
                                        if (in_array($carrier['id_carrier'], $availableCarriers))
                                                $this->_html .= '
                                                <option value="'.(int)($carrier['id_carrier']).'"'.((int)($carrier['id_carrier']) == (int)($this->id_carrier) ? ' selected="selected" ' : '').'>'.$carrier['name'].' (id:'.$carrier['id_carrier'].')</option>';
                                $this->_html .= '
                                </select>
                                <label class="t" for="nb_commandes"> '.$this->l('Nombre des commandes').'</label>
                                <input type="text" name="nb_commandes" id="nb_commandes"/>
				<label class="t" for="date_from_retour"> '.$this->l('Date from').'</label>
				<input type="text" name="date_from" id="date_from_retour"/>
				<label class="t" for="date_to_retour"> '.$this->l('to').'</label>
				<input type="text" name="date_to" id="date_to_retour"/>
				<input type="submit" name="submitRetourException" value="'.$this->l('Add').'" class="button" />
			</div>';

			$exceptions = PlanningRetourByCarrierExceptionOver::get();

			$len = count($exceptions);
			if ($len)
			{
				$this->_html .= '
				<input type="hidden" name="id_planning_retour_carrier_exception" id="id_planning_retour_carrier_exception" />
				<input type="hidden" name="retour_exception_name" id="retour_exception_name" />
				<input type="hidden" name="retour_exception_action" id="slot_action_r" />
				<br /><table class="table" style="width:100%;">
				<thead>
				<tr>
				 <th style="width:95%;">'.$this->l('Restricted dates').'</th>
				 <th style="width:5%;">'.$this->l('Actions').'</th>
				</tr>
				</thead>
				<tbody>';
//                               foreach ($carriers as $carrier){
                                    
//                                        $exceptions = PlanningRetourByCarrierExceptionOver::get($carrier['id_carrier']);
                                        foreach ($exceptions as $exception){
                                            if (in_array($exception['id_carrier'], $availableCarriers)){
                                                $html_updateNbCommand = '<span class="nb_commande" style="float: right;">'
                                                                        . '<label>'.( isset($carrierAndNbCommande[(int)$exception['id_planning_retour_carrier_exception']])? '<span style="color:red">(nb commande : '.$carrierAndNbCommande[(int)$exception['id_planning_retour_carrier_exception']]['real_nb_commande'].')</span>':'')
                                                                        .'nombre des commandes </label>' 
                                                                        . '<input type="text" name="nb_commande" value="'.$exception['nb_commandes'].'" style="width:70px"/> '
                                                                        . ' <span class="updaet_nb_commande_retour button" data-id="'.(int)($exception['id_planning_retour_carrier_exception']).'"><i class="icon-pencil"></i> modifier</span>'
                                                                    . '</span>';
                                                $this->_html .= '
                                                <tr>
                                                 <td> '.($exception['date_from'] == $exception['date_to'] ? $this->dateFR_S($exception['date_from']) : $this->l('From').' '.$this->dateFR_S($exception['date_from']).' '.$this->l('to').' '.$this->dateFR_S($exception['date_to'])).', '.$this->l('Carrier').' <strong>'.$mycarrier[$exception['id_carrier']]['name'].' (id:'.$exception['id_carrier'].')</strong>'
                                                         .$html_updateNbCommand.
                                                     '</td>
                                                 <td style="text-align:center;"><a href="javascript:;" onclick="deleteRetourException(\''.(int)($exception['id_planning_retour_carrier_exception']).'\');"><img src="'.$this->_path.'img/delete.png" alt="'.$this->l('Delete').'" /></a></td>
                                                </tr>';
                                            }
                                         }
//                                }
				$this->_html .= '
				</tbody>
				</table>';
			}
			$this->_html .= '
			</fieldset>
		</form><br />
                <script type="text/javascript">
                    $(document).ready(function(){
                        $(".updaet_nb_commande_retour").css("cursor","pointer").click(function(e){
                            e.preventDefault();
                            var $me = $(this),id = $me.data("id"),nbCommande = $me.parent().find("input").val();
                            
                            $.ajax({
                                url : "'.$this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'",
                                data : {
                                        ajax : true,
                                        action : "Updatenbcommanderetour",
                                        id_planning_retour_carrier_exception : id,
                                        nb_commande : nbCommande,
                                        },
                                type : "POST",
                                dataType: "json",
                                success : function(data){
                                    try {
                                        if(data.success){
                                            showSuccessMessage(data.msg);
                                        }else{
                                            showErrorMessage(data.msg);
                                        }
                                    }catch(err) {
                                        showNoticeMessage("erreur");
                                    }
                                },
                                error : function(msg){
                                    showNoticeMessage("erreur ");
                                }
                            });

                        });
                    })
                </script>';
	}
        
    public function getContent() {
        $this->_checkCarrierGoToSettings(); 
        if ($this->id_carrier > 0){
            $this->_displayQuickLinks();
            $this->_checkPlanningDeliveryRequired();
            $this->_checkAvailableDays();
            if (Configuration::get('PLANNING_DELIVERY_SLOT_'.$this->id_carrier)){
                $this->_checkSlot();
                $this->_checkSlotDay();
            }
            return $this->_html.$this->_displayFormCarrier();
        }else{
            $this->id_carrier = 0;
            $this->_displayQuickLinks();
            $this->_checkCarriersGlobalSettings();
            $this->_checkException();
            $this->_checkRetour();
            return $this->_html.$this->_displayFormGlobal();
        }
    }

    public function includeDatepicker($id, $time = false, $format = 1, $onAdminPlanningDelivery = 0, $id_carrier = false)
    {
            $return = '';
            $carriers_ids = array();
            if ($onAdminPlanningDelivery == 1)
            {
                    if ($this->checkVersion() <= '1.5') $return = '<script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>';
                    $iso = Db::getInstance()->getValue('SELECT iso_code FROM '._DB_PREFIX_.'lang WHERE `id_lang` = '.(int)((int)$this->context->language->id));
                    if ($this->checkVersion() <= '1.5') if ($iso != 'en') $return .= '<script type="text/javascript" src="'.__PS_BASE_URI__.'js/jquery/datepicker/ui/i18n/ui.datepicker-'.$iso.'.js"></script>';
                    $return .= '<script type="text/javascript">';
                    if (is_array($id)) foreach ($id as $id2) $return .= $this->bindDatepicker($id2, $time, $format, 2);
                    else $return .= $this->bindDatepicker($id, $time, $format, $onAdminPlanningDelivery, $id_carrier);
                    $return .= '</script>';
            }
            else
            {
                    if (!$id_carrier)
                    {
                            
                            $return = '<script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>';
                            $iso = Db::getInstance()->getValue('SELECT iso_code FROM '._DB_PREFIX_.'lang WHERE `id_lang` = '.(int)((int)$this->context->language->id));
                            if ($this->checkVersion() <= '1.5') if ($iso != 'en') $return .= '<script type="text/javascript" src="'.__PS_BASE_URI__.'js/jquery/ui/i18n/jquery.ui.datepicker-'.$iso.'.js"></script>';

                            $planningCarriers = explode(', ', Configuration::get('PLANNING_DELIVERY_CARRIERS'));
                            // getCarriers($id_lang, $active = false, $delete = false, $id_zone = false, $ids_group = null, $modules_filters = self::PS_CARRIERS_ONLY)
                            $carriers = Carrier::getCarriers((int)$this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);
                            $carrierAndNbCommande = $this->getCarrierAndNbCommande();
//                            var_dump($carrierAndNbCommande);exit;
                            $return .= '<script type="text/javascript">';
                            foreach ($carriers as $carrier)
                            {
                                    $id_carrier = $carrier['id_carrier'];
                                    if (in_array($id_carrier, $planningCarriers))
                                    {
                                            $unavalaibleDates = PlanningDeliveryByCarrierExceptionOver::getDates($id_carrier);
                                            $unavailableDays = str_replace('7', '0', Configuration::get('PLANNING_DELIVERY_UNAV_DAYS'.$carrier['id_carrier']));
                                            $return .= '
                                            //TODO: get dates by id_carrier
                                            var unavalaibleDates'.$id_carrier.' = ['.$unavalaibleDates.'];
                                            var unavalaibleDays'.$id_carrier.' = ['.$unavailableDays.'];
                                            var delta_days'.$id_carrier.' = '.((int)(Configuration::get('PLANNING_DELIVERY_DAYS_BEFORE'.$id_carrier)) - 1).';'.
                                            ((float)(date('H.i')) >= (float)(str_replace('h', '.', $this->today_max_hour)) ? 'delta_days'.$id_carrier.' += 1;' : '').'
                                            var delta_days_end'.$id_carrier.' = delta_days'.$id_carrier.' + '.(int)(Configuration::get('PLANNING_DELIVERY_NUMB_DAYS'.$id_carrier)).';
                                            var firstDate'.$id_carrier.' = addDaysToDate(new Date(), delta_days'.$id_carrier.');
                                            var lastDate'.$id_carrier.' = addDaysToDate(new Date(), delta_days_end'.$id_carrier.');';
                                    }
                            }
                            $return .= '
                            function showSlots(dateText, inst) {
                                    var id_carrier_checked = getIdCarrierChecked();
                                    var path = "'.__PS_BASE_URI__.'modules/planningdeliverybycarrier/";
                                    var id_lang = '.(int)($this->context->language->id).';
                                    var format = '.$format.';
                                    getDaySlot(path, dateText, format, id_lang, '.$onAdminPlanningDelivery.', id_carrier_checked);
                            };
                            var datesRemove = '.  json_encode($carrierAndNbCommande).' ;
                            </script>';
                    }
                    else
                    {
                            $availableCarriers = explode(', ', Configuration::get('PLANNING_DELIVERY_CARRIERS'));
                            foreach ($availableCarriers as $carrier) $carriers_ids[] = $carrier;
                            if (in_array($id_carrier, $carriers_ids))
                            {
                                    if (is_array($id)) foreach ($id as $id2) $return .= $this->bindDatepicker($id2, $time, $format, $onAdminPlanningDelivery, $id_carrier);
                                    else $return .= $this->bindDatepicker($id, $time, $format, $onAdminPlanningDelivery, $id_carrier);
                                    $return .= '$("#choose_delivery_date").fadeIn(\'slow\');';
                            }
                    }
            }
            return $return;
    }
    
    protected function getCarrierAndNbCommande() {
        if(!count($this->_carrierAndNbCommande)){
            $this->_carrierAndNbCommande = PlanningDeliveryByCarrierExceptionOver::getNbCommandeAndRealNbCommand();
        }
        return $this->_carrierAndNbCommande;
    }

    public function includeDatepickerRetour($id, $time = false, $format = 1, $onAdminPlanningDelivery = 0, $id_carrier = false)
    {
            $return = '';
            $carriers_ids = array();
            if ($onAdminPlanningDelivery == 1)
            {                   
                    $return .= '<script type="text/javascript">';
                    if (is_array($id)) foreach ($id as $id2) $return .= $this->bindDatepickerRetour($id2, $time, $format, 2);
                    else $return .= $this->bindDatepickerRetour($id, $time, $format, $onAdminPlanningDelivery, $id_carrier);
                    $return .= '</script>';
            }
            else
            {
                    if (!$id_carrier)
                    {
                            
                            
                            $planningCarriers = explode(', ', Configuration::get('PLANNING_DELIVERY_CARRIERS'));
                            // getCarriers($id_lang, $active = false, $delete = false, $id_zone = false, $ids_group = null, $modules_filters = self::PS_CARRIERS_ONLY)
                            $carriers = Carrier::getCarriers((int)$this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);
                            $carrierAndNbCommande = PlanningRetourByCarrierExceptionOver::getNbCommandeAndRealNbCommand();
                            $return .= '<script type="text/javascript">';
                            foreach ($carriers as $carrier)
                            {
                                    $id_carrier = $carrier['id_carrier'];
                                    if (in_array($id_carrier, $planningCarriers))
                                    {
                                            $unavalaibleDates = PlanningRetourByCarrierExceptionOver::getDates($id_carrier);
                                            $unavailableDays = str_replace('7', '0', Configuration::get('PLANNING_DELIVERY_UNAV_DAYS'.$carrier['id_carrier']));
                                            $return .= '
                                            
                                            var unavalaibleDatesRetour'.$id_carrier.' = ['.$unavalaibleDates.'];
                                            var unavalaibleDaysRetour'.$id_carrier.' = ['.$unavailableDays.'];
                                            var delta_daysRetour'.$id_carrier.' = '.((int)(Configuration::get('PLANNING_DELIVERY_DAYS_BEFORE'.$id_carrier)) - 1).';'.
                                            ((float)(date('H.i')) >= (float)(str_replace('h', '.', $this->today_max_hour)) ? 'delta_daysRetour'.$id_carrier.' += 1;' : '').'
                                            var delta_daysRetour_end'.$id_carrier.' = delta_daysRetour'.$id_carrier.' + '.(int)(Configuration::get('PLANNING_DELIVERY_NUMB_DAYS'.$id_carrier)).';
                                            var firstDateRetour'.$id_carrier.' = addDaysToDate(new Date(), delta_daysRetour'.$id_carrier.');
                                            var lastDateRetour'.$id_carrier.' = addDaysToDate(new Date(), delta_daysRetour_end'.$id_carrier.');';
                                    }
                            }
                            $return .= '
                            function showSlots(dateText, inst) {
                                    var id_carrier_checked = getIdCarrierChecked();
                                    var path = "'.__PS_BASE_URI__.'modules/planningdeliverybycarrier/";
                                    var id_lang = '.(int)($this->context->language->id).';
                                    var format = '.$format.';
                                    getDaySlot(path, dateText, format, id_lang, '.$onAdminPlanningDelivery.', id_carrier_checked);
                            }
                            var datesRemoveRetour = '.  json_encode($carrierAndNbCommande).' ;
                            </script>';
                    }
                    else
                    {
                            $availableCarriers = explode(', ', Configuration::get('PLANNING_DELIVERY_CARRIERS'));
                            foreach ($availableCarriers as $carrier) $carriers_ids[] = $carrier;
                            if (in_array($id_carrier, $carriers_ids))
                            {
                                    if (is_array($id)) foreach ($id as $id2) $return .= $this->bindDatepickerRetour($id2, $time, $format, $onAdminPlanningDelivery, $id_carrier);
                                    else $return .= $this->bindDatepickerRetour($id, $time, $format, $onAdminPlanningDelivery, $id_carrier);
                                    $return .= '$("#choose_delivery_date").fadeIn(\'slow\');';
                            }
                    }
            }
            return $return;
    }

        
    public function bindDatepickerRetour($id, $time, $format, $onAdminPlanningDelivery, $id_carrier = false){
        $return = '';
		if ($onAdminPlanningDelivery == 1)
		{
			$unavalaibleDates = PlanningRetourByCarrierExceptionOver::getDates($id_carrier);
			$unavailableDays = str_replace('7', '0', Configuration::get('PLANNING_DELIVERY_UNAV_DAYS'.$id_carrier));
			$return .= '
				var unavalaibleDatesRetour = ['.$unavalaibleDates.'];
				var unavalaibleDaysRetour = ['.$unavailableDays.'];
				var delta_daysRetour = '.((int)(Configuration::get('PLANNING_DELIVERY_DAYS_BEFORE'.$id_carrier)) - 1).';
				var delta_days_endRetour = delta_daysRetour + '.(int)(Configuration::get('PLANNING_DELIVERY_NUMB_DAYS'.$id_carrier)).';
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
					if ($.inArray(y + \'-\' + m + \'-\' + d, unavalaibleDatesRetour) != -1 && $.inArray(day, unavalaibleDaysRetour) == -1) {
						return [true];
					}
					return [false];
				}
				function showSlots(dateText, inst){
					var path = "'.__PS_BASE_URI__.'modules/planningdeliverybycarrier/";
					var id_lang = '.(int)((int)$this->context->language->id).';
					var format = '.$format.';
					getDaySlot(path, dateText, format, id_lang, '.$onAdminPlanningDelivery.', '.$id_carrier.');
				}';
		}
		elseif ($onAdminPlanningDelivery == 2)
		{
			$return .= '
			$(function() {
				$("#'.$id.'").datepicker({
					prevText:"",
					nextText:"",
					dateFormat:"yy-mm-dd"'.($time ? '+time' : '').'});
			});';
			return $return;
		}
		$onSelect = (Configuration::get('PLANNING_DELIVERY_SLOT_'.$id_carrier)) ? "onSelect: showSlots, \n" : '';
		$dFormat = (1 == $format) ? 'dd/mm/yy' : 'mm/dd/yy';
		$return .= '
		$(function(){
			$("#'.$id.'").datepicker({
				prevText:"",
				nextText:"",
				beforeShowDay: enableDaysRetour, '.
				$onSelect.'
				dateFormat:"'.$dFormat.'"'.($time ? '+time' : '').'});
		});
		$("#ui-datepicker-div").css("clip", "auto");';
		return $return;
    }
    
    public function bindDatepicker($id, $time, $format, $onAdminPlanningDelivery, $id_carrier = false) {
        $return = '';
        if ($onAdminPlanningDelivery == 1) {
            $unavalaibleDates = PlanningDeliveryByCarrierException::getDates();
            $unavailableDays = str_replace('7', '0', Configuration::get('PLANNING_DELIVERY_UNAV_DAYS' . $id_carrier));
            $return .= '
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
        $dFormat = (1 == $format) ? 'dd/mm/yy' : 'mm/dd/yy';
        $return .= '
		$(function(){                
			$("#date_delivery").datepicker({
				prevText:"",
				nextText:"",
				beforeShowDay: enableDays, ' .
                $onSelect . '
				dateFormat:"' . $dFormat . '"' . ($time ? '+time' : '') . '});
		});
		$("#ui-datepicker-div").css("clip", "auto");';
        return $return;
    }

    protected function _displayFormException()
    { 
        $availableCarriers = explode(', ', Configuration::get('PLANNING_DELIVERY_CARRIERS'));
        $carriers = Carrier::getCarriers((int)$this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);
        $mycarrier = array();
        $carrierAndNbCommande = array();
        foreach ( PlanningDeliveryByCarrierExceptionOver::getAllNbCommandeAndRealNbCommand() as $r){
            $carrierAndNbCommande[$r['id_planning_delivery_carrier_exception']] = $r;  
        }
        
        foreach ($carriers as $carrier){
               $mycarrier[$carrier['id_carrier']]   = $carrier;
        }
            $this->_html .= $this->includeDatepicker(array('date_from', 'date_to'), false, 1, 1);
            $this->_html .= '
            <a id="planningdelivery_exceptions" name="planningdelivery_exceptions"></a>
            <form action="'.$_SERVER['REQUEST_URI'].'" method="post" name="exception_form">
            <fieldset><legend><img src="'.$this->_path.'img/prefs.gif" alt="" title="" />'.$this->l('Exceptions').'&nbsp;<a href="javascript:{}" onclick="$.scrollTo(\'#planningdelivery_top\', 1200);"><img src="'._PS_ADMIN_IMG_.'up.gif" alt="" /></a></legend>
                    <p>'.$this->l('Add as many exceptions as necessary.').'</p><br />
                    <label>'.$this->l('Exception').'</label>
                    <div class="margin-form">
                            <label class="t" for="id_carrier_exception">'.($this->checkVersion() > '1.5' ? '<i class="icon-AdminParentShipping"></i>' : '<img src="../img/t/AdminParentShipping.gif" />').''.$this->l('Carrier').'</label>
                            <select name="id_carrier_exception" id="id_carrierException">';
                            foreach ($carriers as $carrier)
                                    if (in_array($carrier['id_carrier'], $availableCarriers))
                                            $this->_html .= '
                                            <option value="'.(int)($carrier['id_carrier']).'"'.((int)($carrier['id_carrier']) == (int)($this->id_carrier) ? ' selected="selected" ' : '').'>'.$carrier['name'].' (id:'.$carrier['id_carrier'].')</option>';
                            $this->_html .= '
                            </select>
                            <label class="t" for="nb_commandes"> '.$this->l('Nombre des commandes').'</label>
                            <input type="text" name="nb_commandes" id="nb_commandes"/>
                            <label class="t" for="date_from"> '.$this->l('Date from').'</label>
                            <input type="text" name="date_from" id="date_from"/>
                            <label class="t" for="date_to"> '.$this->l('to').'</label>
                            <input type="text" name="date_to" id="date_to"/>
                            <input type="submit" name="submitException" value="'.$this->l('Add').'" class="button" />
                    </div>';

                    $exceptions = PlanningDeliveryByCarrierExceptionOver::get();

                    $len = count($exceptions);
                    if ($len)
                    {
                            $this->_html .= '
                            <input type="hidden" name="id_planning_delivery_carrier_exception" id="id_planning_delivery_carrier_exception" />
                            <input type="hidden" name="exception_name" id="exception_name" />
                            <input type="hidden" name="exception_action" id="slot_action" />
                            <br /><table class="table" style="width:100%;">
                            <thead>
                            <tr>
                             <th style="width:95%;">'.$this->l('Restricted dates').'</th>
                             <th style="width:5%;">'.$this->l('Actions').'</th>
                            </tr>
                            </thead>
                            <tbody>';
//                            foreach ($carriers as $carrier){
                                
//                                    $exceptions = PlanningDeliveryByCarrierExceptionOver::get($carrier['id_carrier']);
                                    foreach ($exceptions as $exception){
                                        if (in_array($exception['id_carrier'], $availableCarriers)){
                                            $html_updateNbCommand = '<span class="nb_commande" style="float: right;">'                                                                        
                                                                        . '<label>'.( isset($carrierAndNbCommande[(int)$exception['id_planning_delivery_carrier_exception']])? '<span style="color:red">(nb commande : '.$carrierAndNbCommande[(int)$exception['id_planning_delivery_carrier_exception']]['real_nb_commande'].')</span>':'')
                                                                        .'nombre des commandes </label>'                                                                        
                                                                        . '<input type="text" name="nb_commande" value="'.$exception['nb_commandes'].'" style="width:70px"/> '
                                                                        . ' <span class="updaet_nb_commande button" data-id="'.(int)($exception['id_planning_delivery_carrier_exception']).'"><i class="icon-pencil"></i> modifier</span>'
                                                                    . '</span>';
                                            $this->_html .= '
                                            <tr>
                                             <td> '.($exception['date_from'] == $exception['date_to'] ? $this->dateFR_S($exception['date_from']) : $this->l('From').' '.$this->dateFR_S($exception['date_from']).' '.$this->l('to').' '.$this->dateFR_S($exception['date_to'])).', '.$this->l('Carrier').' <strong>'.$mycarrier[$exception['id_carrier']]['name'].' (id:'.$exception['id_carrier'].')</strong>'
                                                    .$html_updateNbCommand
                                                .'</td>
                                             <td style="text-align:center;"><a href="javascript:;" onclick="deleteException(\''.(int)($exception['id_planning_delivery_carrier_exception']).'\');"><img src="'.$this->_path.'img/delete.png" alt="'.$this->l('Delete').'" /></a></td>
                                            </tr>';
                                        }
                                    }
//                            }
                            $this->_html .= '
                            </tbody>
                            </table>';
                    }
                    $this->_html .= '
                    </fieldset>
            </form><br />
            <script type="text/javascript">
                $(document).ready(function(){
                    $(".updaet_nb_commande").css("cursor","pointer").click(function(e){
                        e.preventDefault();
                        var $me = $(this),id = $me.data("id"),nbCommande = $me.parent().find("input").val();
                        
                        $.ajax({
                            url : "'.$this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'",
                            data : {
                                    ajax : true,
                                    action : "Updatenbcommande",
                                    id_planning_delivery_carrier_exception : id,
                                    nb_commande : nbCommande,
                                    },
                            type : "POST",
                            dataType: "json",
                            success : function(data){
                                try {
                                    if(data.success){
                                        showSuccessMessage(data.msg);
                                    }else{
                                        showErrorMessage(data.msg);
                                    }
                                }catch(err) {
                                    showNoticeMessage("erreur");
                                }
                            },
                            error : function(msg){
                                showNoticeMessage("erreur ");
                            }
                        });
                        
                    });
                })
            </script>
            ';
    }
    
    public function ajaxProcessUpdatenbcommande() {
        $id_planning_delivery_carrier_exception = (int) Tools::getValue("id_planning_delivery_carrier_exception");
        $nb_commandes = (int) Tools::getValue("nb_commande");
        $success = PlanningDeliveryByCarrierExceptionOver::updateNbCommande($id_planning_delivery_carrier_exception, $nb_commandes);
        if($success){
            $return = array('msg'=>"la modification a bien été effectuée",'success'=>true);    
        }else{
            $return = array('msg'=>"erreur d'enregistrement",'success'=>false);    
        }
        
        echo json_encode($return);
        return true;
    }
    
    public function ajaxProcessUpdatenbcommanderetour() {
        $id_planning_retour_carrier_exception = (int) Tools::getValue("id_planning_retour_carrier_exception");
        $nb_commandes = (int) Tools::getValue("nb_commande");
        $success = PlanningRetourByCarrierExceptionOver::updateNbCommande($id_planning_retour_carrier_exception, $nb_commandes);
        if($success){
            $return = array('msg'=>"la modification a bien été effectuée",'success'=>true);    
        }else{
            $return = array('msg'=>"erreur d'enregistrement",'success'=>false);    
        }
        
        echo json_encode($return);
        return true;
    }

    protected function _checkException()
    {
            $action_exception = Tools::getValue('exception_action');
            $nb_commandes = Tools::getValue('nb_commandes');
            $dateFrom = Tools::getValue('date_from');
            $dateTo = Tools::getValue('date_to');
            $id_carrier = Tools::getValue('id_carrier_exception');
            if (Tools::isSubmit('submitException') && empty($action_exception) && !empty($dateFrom) && !empty($id_carrier))
            {
                PlanningDeliveryByCarrierExceptionOver::add($dateFrom, (empty($dateTo) ? $dateFrom : $dateTo ),$nb_commandes,$id_carrier);
                    $this->_html .= '<div class="conf confirm">'.$this->l('Settings updated').'</div>';
            }
            elseif (!empty($action_exception) && empty($dateFrom))
            {
                    $id_planning_delivery_carrier_exception = Tools::getValue('id_planning_delivery_carrier_exception');
                    switch ($action_exception)
                    {
                            case 'delete':
                                    PlanningDeliveryByCarrierException::delete($id_planning_delivery_carrier_exception);
                                    $this->_html .= '<div class="conf confirm">'.$this->l('Settings updated').'</div>';
                                    break;
                            default:
                                    break;
                    }
            }
    }
    protected function _checkRetour()
    {
        $action_exception = Tools::getValue('retour_exception_action');
        $nb_commandes = Tools::getValue('nb_commandes');
        $dateFrom = Tools::getValue('date_from');
        $dateTo = Tools::getValue('date_to');
        $id_carrier = Tools::getValue('id_carrier_retour');

        if (Tools::isSubmit('submitRetourException') && empty($action_exception) && !empty($dateFrom) && !empty($id_carrier))
        {
            PlanningRetourByCarrierExceptionOver::add($dateFrom, (empty($dateTo) ? $dateFrom : $dateTo ),$nb_commandes,$id_carrier);
                $this->_html .= '<div class="conf confirm">'.$this->l('Settings updated').'</div>';
        }
        elseif (!empty($action_exception) && empty($dateFrom))
        {
                $id_planning_retour_carrier_exception = Tools::getValue('id_planning_retour_carrier_exception');
                switch ($action_exception)
                {
                        case 'delete':
                                PlanningRetourByCarrierExceptionOver::delete($id_planning_retour_carrier_exception);
                                $this->_html .= '<div class="conf confirm">'.$this->l('Settings updated').'</div>';
                                break;
                        default:
                                break;
                }
        }
    }
    
    public function ajaxUpdate($format = null){
            $format = ($format) ? $format : Tools::getValue('format');                        
            $this->requestProcessDateDelivery(Tools::getValue('id_cart'), Tools::getValue('id_carrier'), Tools::getValue('date_delivery'), Tools::getValue('id_planning_delivery_slot'), Tools::getValue('date_retour',null), Tools::getValue('format'));
    }
    
    public function requestProcessDateDelivery($id_cart, $id_carrier, $date_delivery, $id_planning_delivery_slot, $date_retour= null, $format = false){
        
        $errors = $carriers_ids = array();
        $availableCarriers = explode(', ', Configuration::get('PLANNING_DELIVERY_CARRIERS'));
        foreach ($availableCarriers as $carrier){
            $carriers_ids[] = $carrier;
        }
        
        if (in_array($id_carrier, $carriers_ids))
        {
                $dateDeliveryRequired = Configuration::get('PLANNING_DELIVERY_REQUIRED_'.$id_carrier);
                $slotRequired = Configuration::get('PLANNING_DELIVERY_SLOT_'.$id_carrier);

                $result = Db::getInstance()->getRow('
                        SELECT pd.`id_planning_delivery_carrier` as id_planning FROM `'._DB_PREFIX_.'planning_delivery_carrier` pd WHERE pd.`id_cart` = '.(int)($id_cart));

                $planning_delivery = (!$result) ? new PlanningDeliveriesByCarrierOver() : new PlanningDeliveriesByCarrierOver((int)($result['id_planning']));
                $planning_delivery->id_cart = (int)($id_cart);
                $planning_delivery->date_delivery = '';
                $planning_delivery->date_retour = null;
                $planning_delivery->id_planning_delivery_carrier_slot = 0;

                /* Date de livraison */
                if ($format && !empty($date_delivery))
                {
                        $dFormat = (1 == $format) ? 'd/m/Y' : 'm/d/Y';
                        $date_delivery = $this->X_dateformat($date_delivery, $dFormat, 'Y-m-d');
                }
                if (Validate::isDate($date_delivery)){
                    $planning_delivery->date_delivery = $date_delivery;
                }elseif ($dateDeliveryRequired){
                    $errors[] = Tools::displayError($this->l('Delivery Date invalid'));
                }

                /* Créneau horaire */
                if ($slotRequired)
                {
                        if (Validate::isUnsignedId($id_planning_delivery_slot)) $planning_delivery->id_planning_delivery_carrier_slot = $id_planning_delivery_slot;
                        elseif ($dateDeliveryRequired) $errors[] = Tools::displayError($this->l('Invalid slot'));
                }
                
                /* Date de retour */
                if ($this->dateRetourRequired($planning_delivery->id_cart)){
                    if ($format && !empty($date_retour)){
                           $dFormat = (1 == $format) ? 'd/m/Y' : 'm/d/Y';
                           $date_retour = $this->X_dateformat($date_retour, $dFormat, 'Y-m-d');
                   }
                   if (Validate::isDate($date_retour)) {
                       $planning_delivery->date_retour = $date_retour;
                   }else{                    
                       $errors[] = Tools::displayError($this->l('Date de retour invalid'));
                   }
                }
            
                if ($planning_delivery->id_order){
                    $update_suivi_orders = "update ps_suivi_orders set date_delivery='{$planning_delivery->date_delivery}',
                    date_retour='{$planning_delivery->date_retour}' 
                    where id_order = '{$planning_delivery->id_order}'
                    ";
                    Db::getInstance()->execute($update_suivi_orders);
                }

                if (!empty($date_delivery)) $maj = (!$result) ? $planning_delivery->add() : $planning_delivery->update();
                /* TO DO: check this error if (!$maj) */
        }
        return $errors;
    }

    public function hookDisplayAdminOrder($params){
        $order = new Order($params['id_order']);
        $dateRetourRequired = $this->dateRetourRequired($order->id_cart);
        $includeDatepicker =  $this->includeDatepicker('date_delivery', false, 1, 1, $order->id_carrier);
        $includeDatepicker .= $this->includeDatepickerRetour('date_retour', false, 1, 1, $order->id_carrier);
        $result = Db::getInstance()->getRow('
                SELECT pd.`date_delivery`, pds.`name`, pd.`date_retour`
                FROM `'._DB_PREFIX_.'planning_delivery_carrier` pd
                LEFT JOIN `'._DB_PREFIX_.'planning_delivery_carrier_slot` pds
                ON pd.`id_planning_delivery_carrier_slot` = pds.`id_planning_delivery_carrier_slot`
                WHERE pd.`id_cart` = '.$order->id_cart);
        $this->smarty->assign('order', $order);
        $this->smarty->assign('date_delivery', $result['date_delivery']);
        $this->smarty->assign('date_retour', $result['date_retour']);
        $this->smarty->assign('delivery_slot', PlanningDeliverySlotByCarrier::hideSlotsPosition($result['name']));
        $this->smarty->assign('includeDatepicker', $includeDatepicker);
        $this->smarty->assign('dateRetourRequired', $dateRetourRequired);
        return $this->display(__FILE__, 'admin-order.tpl');
    }
    
    public function ajaxUpdateAdminOrder($id_order){
        $order = new Order($id_order);
        $format = 1;
        $date_retour = Tools::getValue('date_retour');
        $errors = $this->requestProcessDateDelivery($order->id_cart, $order->id_carrier, Tools::getValue('date_delivery'),Tools::getValue('id_planning_delivery_slot'), $date_retour, $format);
        $values = array('id_order'=>$id_order);
        if ($format && !empty($date_retour)){
                $dFormat = (1 == $format) ? 'd/m/Y' : 'm/d/Y';
                $date_retour = $this->X_dateformat($date_retour, $dFormat, 'Y-m-d');
        }
        if (Validate::isDate($date_retour)) {            
            $values['date_retour'] = $date_retour;
        }
        Db::getInstance()->update('planning_delivery_carrier', $values, '`id_cart`='.(int)$order->id_cart, 1);
        Db::getInstance()->execute("UPDATE ps_suivi_orders SET date_retour = '$date_retour' WHERE id_order = '$id_order' ");
        
        if (count($errors))
                $this->smarty->assign('pderrors', $errors);
        $result = Db::getInstance()->getRow('
                SELECT pd.`date_delivery`, pds.`name`, pd.`date_retour`
                FROM `'._DB_PREFIX_.'planning_delivery_carrier` pd
                LEFT JOIN `'._DB_PREFIX_.'planning_delivery_carrier_slot` pds
                ON pd.`id_planning_delivery_carrier_slot` = pds.`id_planning_delivery_carrier_slot`
                WHERE pd.`id_cart` = '.$order->id_cart);
        $this->smarty->assign('order', $order);
        $this->smarty->assign('date_delivery', $result['date_delivery']);
        $this->smarty->assign('date_retour', $result['date_retour']);
        $this->smarty->assign('delivery_slot', PlanningDeliverySlotByCarrier::hideSlotsPosition($result['name']));
        return $this->display(__FILE__, 'ajax-update-admin-order.tpl');
    }
    
}

<?php

require_once(dirname(__FILE__) . '/../../planningdeliverybycarrier.php');
require_once(dirname(__FILE__) . '/../../classes/PlanningDeliveriesByCarrier.php');

class AdminPlanningRetourByCarrierController extends ModuleAdminController
{
	public $toolbar_title;
        
        protected $id_product_retour = 0;
        protected $id_carrier_post = 0;
        protected $dateRetour = null;
        protected $carrier_selected = null;
        /** @var integer Default number of results in list per page */
	protected $_default_pagination = 4000;


        public function __construct()
	{
		$this->table = 'order';
		$this->className = 'PlanningDeliveryByCarrier';
		$this->lang = false;
		$this->addRowAction('view');
		$this->explicitSelect = true;
		$this->deleted = false;
		$this->context = Context::getContext();
                $id_carrier = (int) Configuration::get('TUNNELVENTE_ID_CARRIER_POST');
                $this->id_carrier_post = $id_carrier;
                $this->id_product_retour = (int) Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE');
//                $date_depart =  "IF(ca.`id_carrier` = $id_carrier,IF( dayofweek(pd.date_delivery - INTERVAL 2 DAY) in (1,7) ,DATE(pd.date_delivery - INTERVAL 4 DAY) , DATE(pd.date_delivery - INTERVAL 2 DAY)) ,DATE(pd.date_delivery))";
		$this->_select = '
		a.id_currency,
		a.id_order AS id_pdf,a.id_order AS message_livraison,
                a.id_order,psd.product_name,psd.product_quantity,psd.product_id, "-" as products,
		CONCAT(c.`firstname`, \'  \', c.`lastname`) AS `customer`,CONCAT(ad.`firstname`, \' \', ad.`lastname`) AS `ad_customer`,c.note,
		osl.`name` AS `osname`,
		os.`color`,
		"-" as adresse_client,
		`pd`.*, `pds`.`name` AS `pdsname`, `ca`.`name` AS `carriername`,
                CONCAT(ad.`phone`, \' | \', ad.`phone_mobile`) AS `telephone`,
                ad.*,st.`iso_code`,cart.npa
                ';

		$this->_join = '
		LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)
		LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = a.`current_state`)
		LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state`
		AND osl.`id_lang` = '.(int)$this->context->language->id.')

		LEFT JOIN `'._DB_PREFIX_.'planning_delivery_carrier` pd ON (pd.`id_order` = a.`id_order`)
		LEFT JOIN `'._DB_PREFIX_.'planning_delivery_carrier_slot` pds
		ON (pd.`id_planning_delivery_carrier_slot` = pds.`id_planning_delivery_carrier_slot`)
		LEFT JOIN `'._DB_PREFIX_.'carrier` ca ON (a.`id_carrier` = ca.`id_carrier`)';
                
                $this->_join .= '  
                        LEFT JOIN `ps_order_detail` psd ON (psd.`id_order` = a.`id_order`)
                        LEFT JOIN `ps_address` ad ON ( ad.`id_address` = a.`id_address_delivery`)
                        LEFT JOIN ps_state st ON st.`id_state` = ad.`id_state`
                        LEFT JOIN `ps_cart` cart ON ( cart.`id_cart` = a.`id_cart`)
                         ';
                if (Tools::isSubmit('submitDateLivraison')){
                    $dateLivraison = Tools::getValue("datepickerDatelivraison", NULL);
                    if(!is_null($dateLivraison))
                        $this->dateRetour = $dateLivraison;
                    
                    $carrier_selected = Tools::getValue("carrier_selected", NULL);
                    if(!is_null($carrier_selected))
                        $this->carrier_selected = $carrier_selected;
                }
		$this->_where = '
		AND os.`id_order_state` NOT IN ('.Configuration::get('PLANNING_DELIVERY_UNAV_OSS').')
                AND pd.date_retour = '.($this->dateRetour? '"'.$this->dateRetour.'"':'CURDATE()').'
                ';
                
                if(!is_null( $this->carrier_selected ) &&  (int) $this->carrier_selected > 0 ) { 
                    $this->_where .= ' AND a.`id_carrier` = '.(int)$this->carrier_selected.' ';
                }
                
                $this->context->smarty->assign(array(
                    'dateLivraison' => $this->dateRetour,
                ));

		$this->_orderBy = 'pd.date_retour';
		$this->_orderWay = 'ASC';

		$statuses_array = array();
		$statuses = OrderState::getOrderStates((int)$this->context->language->id);

		foreach ($statuses as $status)
			$statuses_array[$status['id_order_state']] = $status['name'];

		$this->fields_list = array(                
		'date_retour' => array(
			'title' => $this->l('Date de retour'),
			'filter_key' => 'pd!date_delivery',
			'callback' => 'displayDateDelivery',
			'width' => 90
		),
		'carriername' => array(
			'title' => $this->l('Carrier'),
			'callback' => 'displayCarrierName',
			'width' => 85
		),
//		'npa' => array(
//			'title' => $this->l('NPA'),
//			'width' => 50
//		),
                'adresse_client' => array(
                    'title' => $this->l('Adresse'),
                    'width' => 280,
                    'callback' => 'displayAdresseClient',
			 
		),
		'products' => array(
			'title' => $this->l('Produits'),
			'width' => 280,
			'callback' => 'displayProducts',
		),		
		'customer' => array(
			'title' => $this->l('Customer'),
			'tmpTableFilter' => true
		),
                'message_livraison' => array(
                        'title' => $this->l('Message livraison'),
                        'callback' => 'displayMessageLivraison',
		),
//		'city' => array(
//			'title' => $this->l('Ville'),
//			'tmpTableFilter' => true
//		),
//		'iso_code' => array(
//			'title' => $this->l('Canton'),
//			'tmpTableFilter' => true
//		),
//		'total_paid_tax_incl' => array(
//			'title' => $this->l('Total'),
//			'width' => 70,
//			'align' => 'right',
//			'prefix' => '<b>',
//			'suffix' => '</b>',
//			'type' => 'price',
//			'currency' => true
//		),
//		'payment' => array(
//			'title' => $this->l('Payment'),
//			'width' => 100
//		),
//		'osname' => array(
//			'title' => $this->l('Status'),
//			'color' => 'color',
//			'width' => 280,
//			'type' => 'select',
//			'list' => $statuses_array,
//			'filter_key' => 'os!id_order_state',
//			'filter_type' => 'int'
//		),
//		'date_add' => array(
//			'title' => $this->l('Date'),
//			'width' => 130,
//			'align' => 'right',
//			'type' => 'datetime',
//			'filter_key' => 'a!date_add'
//		),
                'telephone' => array(
                    'title' => $this->l('Téléphone'),
                    'width' => 130,
                    'callback' => 'displayPhone',
		),
		'id_pdf' => array(
			'title' => $this->l('PDF'),
			'width' => 35,
			'align' => 'center',
			'callback' => 'printPDFIcons',
			'orderby' => false,
			'search' => false,
			'remove_onclick' => true)
		);

		$this->shopLinkType = 'shop';
		$this->shopShareDatas = Shop::SHARE_ORDER;

		if (Tools::isSubmit('id_order'))
		{
			// Save context (in order to apply cart rule)
			$order = new Order((int)Tools::getValue('id_order'));
			if (!Validate::isLoadedObject($order))
				throw new PrestaShopException('Cannot load Order object');
			$this->context->cart = new Cart($order->id_cart);
			$this->context->customer = new Customer($order->id_customer);
		}

		parent::__construct();
                $this->list_simple_header = true;
	}

	public function printPDFIcons($id_order, $tr)
	{
		$order = new Order($id_order);
		$order_state = $order->getCurrentOrderState();
		if (!Validate::isLoadedObject($order_state) || !Validate::isLoadedObject($order))
			return '';

		$this->context->smarty->assign(array(
			'order' => $order,
			'order_state' => $order_state,
			'order_etiquette' => false,//$this->id_carrier_post == $order->id_carrier,
			'base_dir' => _PS_BASE_URL_.__PS_BASE_URI__,
			'tr' => $tr
		));

		return $this->createTemplate('_print_pdf_icon.tpl')->fetch();
	}

	/**
	 * displayDateDelivery
	 *
	 * @return
	 */
	public function displayDateDelivery($echo)
	{
		$time = $echo;
//                if($time == "NULL")
//                    return "-";
		$planning_delivery = New PlanningDeliveryByCarrier();
		return Tools::ucfirst($planning_delivery->dateFR_S($time));
	}

	/**
	 * displayCarrierName
	 *
	 * @return
	 */
	public function displayCarrierName($echo)
	{
		if ($echo == '0')
			$echo = Configuration::get('PS_SHOP_NAME');
		return $echo;
	}

	/**
	 * displayPdsName
	 *
	 * @return
	 */
	public function displayPdsName($echo)
	{
		return PlanningDeliverySlotByCarrier::hideSlotsPosition($echo);
	}

	/**
	 * displayProducts
	 *
	 * @return
	 */
	public function displayProducts($echo)
	{
            $products = "";
            foreach ($echo as $product) {
                $products .= "+ <span ".(($this->id_product_retour == $product['product_id'])?'style="color:red;"':"").">{$product['product_name']}</span> <strong>Qte({$product['product_quantity']})</strong><br>";
            }
		return $products;
	}
        
        public function displayPhone($echo,$tr){
            
            return str_replace('|', '<br>', $echo).'<span classes="hidden addrExport" data-adrexp="'. $tr['adresse_client']['address1'] . ' ' . $tr['adresse_client']['address2'] . ', ' . $tr['adresse_client']['postcode'] . ', ' . $tr['adresse_client']['city'].(!empty($tr['adresse_client']['iso_code'])? ' ('.$tr['adresse_client']['iso_code'].')':'').', Suisse"></sapn>';
        }
        
        public function displayMessageLivraison($id_order,$tr){
            $messages = Message::getMessagesByOrderId($id_order, false);             
            $messages_cleint = array();
            $msg_json = array();
            foreach ($messages as   $message) {
                if ($message['message'] != "Payment accepted.") {
                $messages_cleint[] = $message;
                $msg_json[] = $message['message'];
                }
            }
            $messages2 = Db::getInstance()->executeS(' SELECT message
			FROM '._DB_PREFIX_.'customer_thread ct
                        JOIN '._DB_PREFIX_.'customer_message cm ON ct.id_customer_thread = cm.id_customer_thread
			WHERE  ct.id_shop = '.(int)Context::getContext()->shop->id.'
                                AND cm.private = 1
				AND ct.id_order = '.(int)$id_order
		);
            foreach ($messages2 as   $message2) {
                 
                $messages_cleint[] = $message2;
                $msg_json[] = $message2['message'];
            }
            if(isset($tr['adresse_client']['note']) && !empty($tr['adresse_client']['note'])){
                $messages_cleint[] = array("message"=>$tr['adresse_client']['note']);
                $msg_json[] = $tr['adresse_client']['note'];
            }
            $this->context->smarty->assign(array(
                    'messages' => $messages_cleint,               
                    'messages_json' => json_encode($msg_json),
                    'base_dir' => _PS_BASE_URL_.__PS_BASE_URI__,
                    'tr' => $tr
            ));

            return $this->createTemplate('_message_livraison.tpl')->fetch();
        }
        
	/**
	 * displayAdresseClient
	 *
	 * @return
	 */
	public function displayAdresseClient($echo)
	{
            
            return "<strong>Client: </strong>".$echo['ad_customer'] . '. <br/> ' .$echo['address1'] . ' ' . $echo['address2'] . ' ' . $echo['postcode'] . ' ' . $echo['city'].(!empty($echo['iso_code'])? ' ('.$echo['iso_code'].')':''). ( (!empty($echo['company'] ))? '<br> <strong>Société : </strong>'.$echo['company']:'' );
	}
        
	/**
	 * getWeekList
	 *
	 * @return
	 */
	public function getWeekList()
	{
                        
		// Liste des dates de la semaine..
		$this->_weekList = Db::getInstance()->ExecuteS(
			'SELECT SQL_CALC_FOUND_ROWS
				a.*,
				a.id_order AS id_pdf,
				CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
				osl.`name` AS `osname`, os.`color` AS `oscolor`,
				IF((SELECT COUNT(so.id_order) FROM `'._DB_PREFIX_.'orders` so WHERE so.id_customer = a.id_customer
				AND so.valid = 1) > 1, 0, 1) as new,
				(SELECT COUNT(od.`id_order`) FROM `'._DB_PREFIX_.'order_detail` od
				WHERE od.`id_order` = a.`id_order` GROUP BY `id_order`) AS product_number,
                                
				`pd`.*,
				`pds`.`name` AS `pdsname`,
				`ca`.`name` AS `carriername`
			FROM `'._DB_PREFIX_.'orders` a
			LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)
			LEFT JOIN `'._DB_PREFIX_.'order_history` oh ON (oh.`id_order` = a.`id_order`)
			LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = oh.`id_order_state`)
			LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state`
			AND osl.`id_lang` = '.(int)$this->context->language->id.')
			LEFT JOIN `'._DB_PREFIX_.'planning_delivery_carrier` pd ON (pd.`id_order` = a.`id_order`)
			LEFT JOIN `'._DB_PREFIX_.'planning_delivery_carrier_slot` pds
			ON (pd.`id_planning_delivery_carrier_slot` = pds.`id_planning_delivery_carrier_slot`)
			LEFT JOIN `'._DB_PREFIX_.'carrier` ca ON (a.`id_carrier` = ca.`id_carrier`)
			WHERE oh.`id_order_history` = (SELECT MAX(`id_order_history`)
			FROM `'._DB_PREFIX_.'order_history` moh WHERE moh.`id_order` = a.`id_order` GROUP BY moh.`id_order`)
			AND os.`id_order_state` NOT IN ('.Configuration::get('PLANNING_DELIVERY_UNAV_OSS').')
			AND pd.date_retour = '.($this->dateRetour? '"'.$this->dateRetour.'"':'CURDATE()').'
                            '.((!is_null( $this->carrier_selected ) &&  (int) $this->carrier_selected > 0 ) ? ' AND a.`id_carrier` = '.(int)$this->carrier_selected.' ': '' )
                        .'
			ORDER BY `date_delivery` ASC '); 
	}

	/**
	 * getWeekList
	 *
	 * @return
	 */
	public static function getHomeWeekList()
	{
            $id_carrier = (int) Configuration::get('TUNNELVENTE_ID_CARRIER_POST');
            $carrier_selected = (int) Tools::getValue("carrier_selected", NULL);
            $date_depart =  "IF(ca.`id_carrier` = $id_carrier,IF( dayofweek(pd.date_delivery - INTERVAL 2 DAY) in (1,7) ,DATE(pd.date_delivery - INTERVAL 4 DAY) , DATE(pd.date_delivery - INTERVAL 2 DAY)) ,DATE(pd.date_delivery))";
		// Liste des dates de la semaine..
		$_weekList = Db::getInstance()->ExecuteS(
			'SELECT SQL_CALC_FOUND_ROWS
				a.*,
				a.id_order AS id_pdf,
				CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
				osl.`name` AS `osname`, os.`color` AS `oscolor`,
				IF((SELECT COUNT(so.id_order) FROM `'._DB_PREFIX_.'orders` so
				WHERE so.id_customer = a.id_customer AND so.valid = 1) > 1, 0, 1) as new,
				(SELECT COUNT(od.`id_order`) FROM `'._DB_PREFIX_.'order_detail` od
				WHERE od.`id_order` = a.`id_order` GROUP BY `id_order`) AS product_number,
                                '.$date_depart.' as date_depart,
				`pd`.*,
				`pds`.`name` AS `pdsname`,
				`ca`.`name` AS `carriername`
			FROM `'._DB_PREFIX_.'orders` a
			LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)
			LEFT JOIN `'._DB_PREFIX_.'order_history` oh ON (oh.`id_order` = a.`id_order`)
			LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = oh.`id_order_state`)
			LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state`
			AND osl.`id_lang` = '.(int)Context::getContext()->language->id.')
			LEFT JOIN `'._DB_PREFIX_.'planning_delivery_carrier` pd ON (pd.`id_order` = a.`id_order`)
			LEFT JOIN `'._DB_PREFIX_.'planning_delivery_carrier_slot` pds
			ON (pd.`id_planning_delivery_carrier_slot` = pds.`id_planning_delivery_carrier_slot`)
			LEFT JOIN `'._DB_PREFIX_.'carrier` ca ON (a.`id_carrier` = ca.`id_carrier`)
			WHERE oh.`id_order_history` = (SELECT MAX(`id_order_history`)
			FROM `'._DB_PREFIX_.'order_history` moh WHERE moh.`id_order` = a.`id_order` GROUP BY moh.`id_order`)
			AND os.`id_order_state` NOT IN ('.Configuration::get('PLANNING_DELIVERY_UNAV_OSS').')
			AND '.$date_depart.' = CURDATE()
                        '.((!is_null( $carrier_selected ) &&  $carrier_selected > 0 ) ? ' AND a.`id_carrier` = '.$carrier_selected.' ': '' )
                        .'
			ORDER BY `date_delivery` ASC ');
		return $_weekList;
	}

	/**
	 * displayWeekList
	 *
	 * @return
	 */
	public function displayWeekList()
	{
		$weekList = '';
		$weekListGroupByDay = array();
		//$planning_delivery = New PlanningDeliveryByCarrier();

		if (count($this->_weekList))
		{
			foreach ($this->_weekList as $delivery)
				//$weekListGroupByDay[Tools::ucfirst($planning_delivery->dateFR_S($delivery['date_delivery']))][PlanningDeliverySlotByCarrier::hideSlotsPosition($delivery['pdsname'])][] = $delivery;
				$weekListGroupByDay[Tools::ucfirst($delivery['carriername'])][PlanningDeliverySlotByCarrier::hideSlotsPosition($delivery['pdsname'])][] = $delivery;
			$this->tpl_list_vars['weekListGroupByDay'] = $weekListGroupByDay;
			$weekList = true;
		}

		$this->tpl_list_vars['identifier'] = $this->identifier;
		$this->tpl_list_vars['orderToken'] = Tools::getAdminTokenLite('AdminOrders');
		$this->tpl_list_vars['PS_SHOP_NAME'] = Configuration::get('PS_SHOP_NAME');
		$this->tpl_list_vars['path_weeklist'] = _PS_ROOT_DIR_.'/modules/planningdeliverybycarrier/views/templates/hook';

		return $weekList;
	}

	/**
	 * Function used to render the list to display for this controller
	 */
	public function renderList()
	{
		$this->getWeekList();

		// Assign vars specialy for the planningdeliverybycarrier module
		if (count($this->_weekList)) $this->tpl_list_vars['displayWeekList'] = $this->displayWeekList();
		else
		{
			$this->tpl_list_vars['displayWeekList'] = false;
			$this->displayWeekList();
		}
		$this->tpl_list_vars['orderStates'] = $this->setSelectOrderStates();
		$this->tpl_list_vars['carriers'] = $this->setSelectCarriers();

		if (PlanningDeliveryByCarrier::checkVersion() > '1.5') $this->tpl_list_vars['show_toolbar'] = false;

		if (!($this->fields_list && is_array($this->fields_list)))
			return false;
		$this->getList($this->context->language->id);                
               
		// Empty list is ok
		if (!is_array($this->_list))
			return false;
                
                $liste = array();
                $_fields_list = array_keys($this->fields_list);
                foreach ($this->_list as  $row) {
                    if(!isset($liste[$row['id_order']])){
                        $new_row = array();
                        foreach ($_fields_list as $k) {
                            $new_row[$k] = $row[$k];
                        }
                        $new_row['id_currency'] = $row['id_currency'];
                        $new_row['color'] = $row['color'];
                        $new_row['id_planning_delivery_carrier'] = $row['id_planning_delivery_carrier'];
                        $new_row['id_cart'] = $row['id_cart'];
                        $new_row['id_order'] = $row['id_order'];
                        $new_row['id_planning_delivery_carrier_slot'] = $row['id_planning_delivery_carrier_slot'];
                        $new_row['date_retour'] = $row['date_retour'];
                        $new_row['date_upd'] = $row['date_upd'];
                        $new_row['npa'] = $row['npa'];
                        $new_row['adresse_client'] = array(
                            'postcode' => $row['postcode'],
                            'address1' => $row['address1'],
                            'address2' => $row['address2'],
                            'city' => $row['city'],
                            'iso_code' => $row['iso_code'],
                            'company' => $row['company'],
                            'phone' => $row['phone'],
                            'phone_mobile' => $row['phone_mobile'],
                            'other' => $row['other'],
                            'note' => $row['note'],
                            'ad_customer' => $row['ad_customer'],
                        );
                        $new_row['products'] = array();
                        $new_row['products'][] = array(
                                    'product_id' => $row['product_id'],
                                    'product_name' => $row['product_name'],
                                    'product_quantity' => $row['product_quantity'],
                                );
                        $liste[$row['id_order']] = $new_row;
                    }else{
                        $liste[$row['id_order']]['products'][] = array(
                                    'product_id' => $row['product_id'],
                                    'product_name' => $row['product_name'],
                                    'product_quantity' => $row['product_quantity'],
                                );
                    }
                }
//                var_dump($liste);
                $this->_list = $liste;
                $this->_listTotal = count($this->_list);
//                echo $this->_listsql;exit;
                
		$helper = new HelperList();

		$this->setHelperDisplay($helper);
		$helper->tpl_vars = $this->tpl_list_vars;
		$helper->tpl_delete_link_vars = $this->tpl_delete_link_vars;

		// For compatibility reasons, we have to check standard actions in classes attributes
		foreach ($this->actions_available as $action)
		{
			if (!in_array($action, $this->actions) && isset($this->$action) && $this->$action)
				$this->actions[] = $action;
		}

		$list = $helper->generateList($this->_list, $this->fields_list); 

		$list = str_replace('index.php?controller=adminplanningretourbycarrier&amp;id_order=',
		'index.php?controller=adminorders&amp;id_order=', $list);
		$list = str_replace('index.php?controller=AdminPlanningRetourByCarrierController&amp;id_order=',
		'index.php?controller=adminorders&amp;id_order=', $list);
		$list = str_replace('vieworder&amp;token='.$this->token.'"',
		'vieworder&amp;token='.Tools::getAdminTokenLite('AdminOrders').'" target="_blank"', $list);

		return $list;
	}

	public function l($string, $class = 'AdminTab', $addslashes = false, $htmlentities = true)
	{
		$class = 'AdminPlanningDeliveryByCarrierController';
		$planning_delivery = New PlanningDeliveryByCarrier();
		return $planning_delivery->l($string, $class, $addslashes, $htmlentities);
	}

	public function initToolbar()
	{
		$res = parent::initToolbar();
		unset($this->toolbar_btn['new']);
		return $res;
	}

	public function setSelectOrderStates()
	{
		$return = '<option> '.$this->l('- All status -').' </option>';
		$orderStates = OrderState::getOrderStates((int)$this->context->language->id);
		foreach ($orderStates as $orderState)
			$return .= '<option value="'.$orderState['id_order_state'].'">'.$orderState['name'].'</option>';
		return $return;
	}

	public function setSelectCarriers()
	{
		$return = '<option> '.$this->l('- All Carriers -').' </option>';

		// getCarriers($id_lang, $active = false, $delete = false, $id_zone = false, $ids_group = null, $modules_filters = self::PS_CARRIERS_ONLY)
		$carriers = Carrier::getCarriers((int)$this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);
		foreach ($carriers as $carrier)
			$return .= '<option value="'.$carrier['id_carrier'].'" '.( ($carrier['id_carrier'] == (int) Tools::getValue("carrier_selected")) ? 'selected=""' : '' ).'>'.$carrier['name'].'</option>';
		return $return;
	}
        
        public function ajaxProcessSendMailsRetour() {
            $return = array('msg' => "erreur d'enregistrements", 'success' => false);
            $message = Tools::getValue("message");
            $subject = Tools::getValue("subject");
            $orders = Tools::getValue("orders_ids");
            if(!$orders || !count($orders)){
                echo json_encode(array('msg' => "Erreur : Vous devez selectionner au moins une commande !", 'success' => false));
                exit;
            }    
            if (!empty($message) && !empty($subject)) {
                $headers = 'From: info@ecosapin.ch' . "\r\n" .
                        'Reply-To: info@ecosapin.ch' . "\r\n";

                $request = "SELECT email FROM ps_orders po "
                        . "JOIN `ps_customer` pc ON po.`id_customer` = pc.`id_customer` "
                        . "WHERE po.`id_order` IN(" . implode(",", $orders) . " )";
                $listeClient = Db::getInstance()->executeS($request);
                foreach ($listeClient as $mailClient) {
                    mail($mailClient['email'], $subject, $message,$headers);
                }
                $return = array('msg' => "l'email à bien été envoyé", 'success' => true);
            } else {
                $return = array('msg' => "Erreur : remplir les champs sujet et message d'email", 'success' => false);
            }
            echo json_encode($return);
        }

}

<?php
/**
 *  * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2018 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 */
require_once _PS_MODULE_DIR_ . 'twintcw/lib/loader.php';

require_once _PS_MODULE_DIR_ . 'twintcw/lib/TwintCw/TranslationResolver.php';

if (!defined('_PS_VERSION_'))
	exit();

/**
 * TwintCw
 *
 * This class defines all central vars for the TwintCw modules.
 *       	   	 	  			 	  
 *
 * @author customweb GmbH
 */
class TwintCw extends Module {
	/**
	 *
	 * @var TwintCw_ConfigurationApi
	 */
	private $configurationApi = null;
	public $trusted = true;
	const CREATE_PENDING_ORDER_KEY = 'CREATE_PENDING_ORDER';
	private static $recordMailMessages = false;
	private static $recordedMailMessages = array();
	private static $instance = null;
	private static $cancellingCheckIsRunning = false;
	private static $logListenerRegistered = false;
	private $initialized = false;
	private static $requiresExecuted = false;
	
	
	/**
	 * This method init the module.
	 */
	public function __construct(){
		
		// We have to make sure we can reuse the instance later.
		if (self::$instance === null) {
			self::$instance = $this;
		}
		
		$this->name = 'twintcw';
		$this->tab = 'checkout';
		$this->version = preg_replace('([^0-9\.a-zA-Z]+)', '', '4.0.108');
		$this->author = 'customweb ltd';
		$this->currencies = true;
		$this->currencies_mode = 'checkbox';
		$this->bootstrap = true;
		
		parent::__construct();
		
		// The parent construct is required for translations       	   	 	  			 	  
		$this->displayName = TwintCw::translate('DISPLAY NAME');
		$this->description = TwintCw::translate('ACCEPTS PAYMENTS MAIN');
		$this->confirmUninstall = TwintCw::translate('DELETE CONFIRMATION');
		
		if (Module::isInstalled($this->name) && !empty($this->id)) {
			$this->checkForCancellingRunningTransaction();
		}
		
		if (!isset($_GET['configure']) && $this->context->controller instanceof AdminModulesController && method_exists('Module', 'isModuleTrusted') &&
				 (!Module::isInstalled($this->name) || !Module::isInstalled('mailhook'))) {
		 	require_once 'TwintCw/SmartyProxy.php';
			$this->context->smarty = new TwintCw_SmartyProxy($this->context->smarty);
			if (!isset($GLOBALS['cwrmUnTrustedMs'])) {
				$GLOBALS['cwrmUnTrustedMs'] = array();
			}
			$GLOBALS['cwrmUnTrustedMs'][] = 'twintcw';
		}
		
		
		$this->handleChangesForAuthController();
	}
	
	
	/**
	 * This method loads the additional required classes and initializes all the things required to run the module.
	 */
	private function initialize() {
		if ($this->initialized === false) {
			$this->initialized = true;
			self::loadClasses();
			
			if (Module::isInstalled($this->name)) {
				$migration = new Customweb_Database_Migration_Manager(TwintCw_Util::getDriver(), dirname(__FILE__) . '/updates/',
						_DB_PREFIX_ . 'twintcw_schema_version');
				$migration->migrate();
			}
			
			if (Module::isInstalled($this->name)) {
				$this->registerLogListener();
			}
		}
	}	
	
	private function checkLicense(){
		require_once 'Customweb/Licensing/TwintCw/License.php';
		$arguments = null;
		return Customweb_Licensing_TwintCw_License::run('5matkan8uec0ejl3', $this, $arguments);
	}

	final public function call_gcejm5pvubkvv7gf() {
		$arguments = func_get_args();
		$method = $arguments[0];
		$call = $arguments[1];
		$parameters = array_slice($arguments, 2);
		if ($call == 's') {
			return call_user_func_array(array(get_class($this), $method), $parameters);
		}
		else {
			return call_user_func_array(array($this, $method), $parameters);
		}
		
		
	}
	
	private static function loadClasses() {
		if (self::$requiresExecuted === false) {
			self::$requiresExecuted = true;
			
			require_once 'Customweb/Payment/ExternalCheckout/IContext.php';
require_once 'Customweb/Util/Invoice.php';
require_once 'Customweb/Core/Exception/CastException.php';
require_once 'Customweb/Licensing/TwintCw/License.php';
require_once 'Customweb/Payment/ExternalCheckout/IProviderService.php';
require_once 'Customweb/Core/Logger/Factory.php';
require_once 'Customweb/Core/Url.php';
require_once 'Customweb/Core/DateTime.php';
require_once 'Customweb/Core/String.php';
require_once 'Customweb/Database/Migration/Manager.php';
require_once 'Customweb/Payment/Authorization/ITransaction.php';

			require_once 'TwintCw/ConfigurationApi.php';
require_once 'TwintCw/Entity/Transaction.php';
require_once 'TwintCw/Entity/ExternalCheckoutContext.php';
require_once 'TwintCw/Util.php';
require_once 'TwintCw/LoggingListener.php';
require_once 'TwintCw/SmartyProxy.php';

			
			if (Module::isInstalled('mailhook')) {
				require_once rtrim(_PS_MODULE_DIR_, '/') . '/mailhook/MailMessage.php';
				require_once rtrim(_PS_MODULE_DIR_, '/') . '/mailhook/MailMessageAttachment.php';
				require_once rtrim(_PS_MODULE_DIR_, '/') . '/mailhook/MailMessageEvent.php';
			}
			
		}
	}
	

	private function getName(){
		return $this->name;
	}

	/**
	 * When pending orders are created, the stock may be reduced during the checkout.
	 * When
	 * the customer returns during the payment in the browser to the store, the stock is
	 * reserved for the customer, however he will never complete the payment. Hence we have to give
	 * the customer the option to cancel the running transaction.
	 */
	private function checkForCancellingRunningTransaction(){
		$controller = strtolower(Tools::getValue('controller'));
		if (($controller == 'order' || $controller == 'orderopc') && isset($this->context->cart) && !Configuration::get('PS_CATALOG_MODE') &&
				!$this->context->cart->checkQuantities()) {
			if ($this->isCreationOfPendingOrderActive() && self::$cancellingCheckIsRunning === false) {
				self::$cancellingCheckIsRunning = true;
				$originalCartId = $this->context->cart->id;
				TwintCw_Util::getDriver()->beginTransaction();
				$cancelledTransactions = 0;
				try {
					$transactions = TwintCw_Entity_Transaction::getTransactionsByOriginalCartId($originalCartId, false);
					foreach ($transactions as $transaction) {
						if ($transaction->getAuthorizationStatus() == Customweb_Payment_Authorization_ITransaction::AUTHORIZATION_STATUS_PENDING) {
							$transaction->forceTransactionFailing();
							$cancelledTransactions++;
						}
					}
					TwintCw_Util::getDriver()->commit();
				}
				catch (Exception $e) {
					$this->context->controller->errors[] = $e->getMessage();
					TwintCw_Util::getDriver()->rollBack();
				}
				if ($cancelledTransactions > 0) {
					$this->context->controller->errors[] = TwintCw::translate(
							"It seems as you have not finished the payment. We have cancelled the running payment.");
				}
			}
			self::$cancellingCheckIsRunning = false;
		}
	}

	public static function getInstance(){
		if (self::$instance === null) {
			self::$instance = new TwintCw();
		}
		
		return self::$instance;
	}

	/**
	 *
	 * @return TwintCw_ConfigurationApi
	 */
	public function getConfigApi(){
		$this->initialize();
		if (empty($this->id)) {
			throw new Exception("Cannot initiate the config api wihtout the module id.");
		}
		
		if ($this->configurationApi == null) {
			$this->configurationApi = new TwintCw_ConfigurationApi($this->id);
		}
		return $this->configurationApi;
	}

	/**
	 * This method installs the module.
	 *
	 * @return boolean if it was successful
	 */
	public function install(){
		$this->initialize();
		$this->installController('AdminTwintCwRefund', 'TWINT Refund');
		$this->installController('AdminTwintCwMoto', 'TWINT Moto');
		$this->installController('AdminTwintCwForm', 'TWINT', 1, 
				Tab::getIdFromClassName('AdminParentModulesSf'));
		$this->installController('AdminTwintCwTransaction', 'TWINT Transactions', 1);
		
		if (parent::install() && $this->installConfigurationValues() && $this->registerHook('adminOrder') && $this->registerHook('backOfficeHeader') &&
				 $this->registerHook('displayHeader') && $this->registerHook('displayCustomerAccountForm') && $this->registerHook('displayPDFInvoice')) {
			
			

			return true;
		}
		else {
			return false;
		}
	}

	public function installController($controllerName, $name, $active = 0, $parentId = null){
		$this->initialize();
		if ($parentId === null) {
			$parentId = Tab::getIdFromClassName('AdminParentOrders');
		}
		
		$tab_controller_main = new Tab();
		$tab_controller_main->active = $active;
		$tab_controller_main->class_name = $controllerName;
		foreach (Language::getLanguages() as $language) {
			//in Presta 1.5 the name length is limited to 32
			if (version_compare(_PS_VERSION_, '1.6') >= 0) {
				$tab_controller_main->name[$language['id_lang']] = substr($name, 0, 64);
			}
			else {
				//we have to cut the psp name otherwise, otherwise there could be an issue
				//where we can not distinguish the different controllers as all there visible names are identical
				if (strlen($name) > 32) {
					if (strpos($name, 'TWINT') !== false) {
						$name = str_replace('TWINT', '', $name);
						$length = strlen($name);
						if ($length < 32) {
							$pspName = substr('TWINT', 0, 32 - $length);
							$name = $pspName . $name;
						}
					}
				}
				$tab_controller_main->name[$language['id_lang']] = substr($name, 0, 32);
			}
		}
		$tab_controller_main->id_parent = $parentId;
		$tab_controller_main->module = $this->name;
		$tab_controller_main->add();
		$tab_controller_main->move(Tab::getNewLastPosition(0));
	}

	public function uninstall(){
		$this->initialize();
		$this->uninstallController('AdminTwintCwRefund');
		$this->uninstallController('AdminTwintCwMoto');
		$this->uninstallController('AdminTwintCwForm');
		$this->uninstallController('AdminTwintCwTransaction');
		
		return parent::uninstall() && $this->uninstallConfigurationValues();
	}

	public function uninstallController($controllerName){
		$this->initialize();
		$tab_controller_main_id = TabCore::getIdFromClassName($controllerName);
		$tab_controller_main = new Tab($tab_controller_main_id);
		$tab_controller_main->delete();
	}

	private function installConfigurationValues(){
		$this->getConfigApi()->updateConfigurationValue('CREATE_PENDING_ORDER', 'inactive');
		$this->getConfigApi()->updateConfigurationValue('OPERATION_MODE', 'test');
		$this->getConfigApi()->updateConfigurationValue('MERCHANT_UUID', '');
		$this->getConfigApi()->updateConfigurationValue('CERTIFICATE_STRING', '');
		$this->getConfigApi()->updateConfigurationValue('CERTIFICATE_PASSPHRASE', '');
		$this->getConfigApi()->updateConfigurationValue('TEST_MERCHANT_UUID', 'ad013fee-a5e8-4de9-ad7d-f3efe0164177');
		$this->getConfigApi()->updateConfigurationValue('TEST_CERTIFICATE_STRING', '-----BEGIN CERTIFICATE-----
MIIEejCCA2KgAwIBAgIBJzANBgkqhkiG9w0BAQsFADBRMQswCQYDVQQGEwJjaDEe
MBwGA1UEChMVQWROb3Z1bSBJbmZvcm1hdGlrIEFHMSIwIAYDVQQDExlUV0lOVCBQ
YXltZW50IEludGVncmF0aW9uMB4XDTE1MDYyNDA2NDE1MloXDTI1MDYyMTA2NDE1
MlowejELMAkGA1UEBhMCY2gxEjAQBgNVBAoTCUN1c3RvbXdlYjEhMB8GA1UEAxMY
VFdJTlQtVGVjaFVzZXIgQ3VzdG9td2ViMTQwMgYKCZImiZPyLGQBARMkMGU3Mjhl
ODgtZGI5Mi00M2E5LWJiZDUtZjNlNDEwYzE1YmEyMIIBIjANBgkqhkiG9w0BAQEF
AAOCAQ8AMIIBCgKCAQEA+mP9MrAPQzQRpB1tG+kA1OTEuMQbF049dL3W1ptbo0O9
tzw/jmmRm8FPesage8hUSsZ/yInIwsKCB/8/ApKm+qOZX4kEw0E8aSpcva3dHmHS
5rr3sNz/ZRk8skyBAlUECpmPKQ/fSMLlZ87rIvW7Y8kodhnL/Y5H+xHmWyhqOMKp
F2wjUUwQkGnf0x3Hv+yYzt5vHOjKUNOyPSuP9AlIc9dQqYMLCB7EXdncCIDQRcwU
EbsZ+IU+SOq9zEmYJ3Ztqpih/RP3hdmcrPHAnjM3O9pMtQg6YuXL06IR7/esjsrj
5RsSIOiyenofeOx3P+3btEVCSE3utxKZyfYuMaanHQIDAQABo4IBMjCCAS4wCQYD
VR0TBAIwADA/BglghkgBhvhCAQ0EMhYwTmV2aXMgS2V5Qm94IEdlbmVyYXRlZCBD
ZXJ0aWZpY2F0ZSB1c2luZyBPcGVuU1NMMAsGA1UdDwQEAwIDqDAdBgNVHSUEFjAU
BggrBgEFBQcDAQYIKwYBBQUHAwIwHQYDVR0OBBYEFOqaltfmWs96xVzJ88wOyDsN
+7Q1MIGBBgNVHSMEejB4gBSr68zVtfOKkp+Z+ErDlcUgrBpR0KFVpFMwUTELMAkG
A1UEBhMCY2gxHjAcBgNVBAoTFUFkTm92dW0gSW5mb3JtYXRpayBBRzEiMCAGA1UE
AxMZVFdJTlQgUGF5bWVudCBJbnRlZ3JhdGlvboIJAOJwo1TeThlPMBEGCWCGSAGG
+EIBAQQEAwIGwDANBgkqhkiG9w0BAQsFAAOCAQEAeQC3F3Ae9nqzt2VlQMnITY0j
fEHzP+dfBCOThdNsZUY1Wy4hqtxmFJ8esWp+h9u+zlqXh2dj0BNT1PNI5bmRV/Xf
vqTNZ7hCnQ1j1y8QaWPAo1koy0jaFRaVJ/u0dSvKtKuQj3Nh2Ba8fdCAlLqNevGc
HIWDTN9Il8+9cFNuh9X2h9H0wLw1pEjTQ5MdbY6JIG7Lqs0VucZS95rT5vMKZw2H
dl6BgzsUQ+m18MakLFUMok0BqW6KVDENpLYTrA+Ff8T4WiOdct1rIpzw5aWh4/ow
iPZcQJ15KoGadVZHFtCUzg7VWFL1B1sQm2M52ydBy1gebLgxvmxsDqXLgAGJLQ==
-----END CERTIFICATE-----
-----BEGIN RSA PRIVATE KEY-----
Proc-Type: 4,ENCRYPTED
DEK-Info: DES-EDE3-CBC,CEBA99B1C56178D0

XHqDIF781fUmHqh954bV4FMZi2NhAaV8HBV2LJBHJ6LVP3VEcFJyai1nzGClOzhM
rKEaW0T1OnBV5LK/EPSNIDRZyruluW0GrubrYA8dT8CWkR+Tg+pvr2SUvNX4vagZ
txBnpv7E97vXGWJokwKqFaJc2meZgq22SBCbc3tqtsFpZNGi2O5/IMEtqvG2txTe
MhQry53O0Ui4Uwxqkg/rXv0as+zkz54C9m/XC/tTWn3o1l+y+KOTKLx4yv02oycX
tS9ALLzxn+HS0WdNlHumBvtcqTO5o9hYepJ5JrBb4KFlCwjtDuHCe2DAdcq2IfaV
nDGgwoHpHmsK5czrjcgiDJBIayQm0N+1o7ye7OgNCeKh4cZLucEoX3I1RpF/9stf
isA9fjHGq11D7YGPca8MM1Upq+n0OHYc1ZIlHt60koRtL3RkfXhvzsvQyCLllm0N
hfyEujK9pwjOQVaCO+CmQvAzUaoQwj7D42QPKbXPmIvz91odE/ByqBUFHzNYJ9gq
qrS9sx6h1vQ4Ghp6VDPW+78N0nZ1Ws9iTr1KGwCTqRUGZ8Mqqgss00jvOoK/xvDy
Ah5q0SRXQqXWsPvOdSoIs9jK9vfAsoSIZyEz5VRaEoSb/Tln8w61rYoPYbFKYW/L
cip92iaIsJJZxpWJAufB7IFs02fQV/ZRPqKULEk75tH7cZYBNhEw+CW9rvyWZ7/i
1MO3Z7SD4haBt93o+T7IDLIgiwTyu/DUbblABI4cJGX+1h0SSj+yvjXdxythFN2R
rttsFfcgKTyMY5b0c/k4RmswtbkVS64WxxSWC4WYiPoxmZe5dZtmd6LHFs2xdnuQ
sicCUl0Ynn25eP3D50h0CNa/IP77SzIWiT6C/mqIJLI8CgrL+TGRI/Hs7LT0EXPG
UZmLN8+7BNNYidPNTmOc+N+Qt3qz2ZxMyQOoA0ZcyMadJr/WhPNQqRM1Tg6SGW1q
vR1twVnnuV7e6L7T0ri72wt9QrWVSS1hEvJiT7ClksP2cvoOBOKHNA7rwTpI4SJh
CB/WxfClWNpjjeB4jgSRJuhQ0AbxFkC4w7KR2PqQbTUycBdL2sbnnhrsmS2ZKLpJ
/V5TrdA/x/LxLlLbvQtFwYqiggcxOiZDqKbZRg1IjQcXnlPDuWVDqy4N62qzlw4m
7FrercyJaOj/YEoIRx15rMO+xDwYblp91bqYH1G6B9HnS9mv3VvznyKtWnMFUvJm
nOYom4Oytlne5pmYn4/I4NcOX+0ymKZ2MRjuLX3FdO5hVnilfqGPw0P6b7amEQ97
8oT1HgGidfDkboQgtYB1oAuggR1B75x30G3tmSvbTNmnbB8zLXV6Zik0TAOqqE1M
HST38OH/+D5kn2ZUKj5cZet5WuqRMXmPtVVO8fwwl2SdiuZNSW5M8jSVba+tEEZ4
CBNl0OFoxkTzunXUR/84bujBqcC1fATfoJWlUoqWIAGGwJUFfYTcfRyPZBG6L5Bq
fuaz5LdN/hC+KrVpMehuj3bzc7Dw6X4rngnlmgAragjaa0L2hFjcbnV1OkWzXm0r
rDHsoYzAp9CYrgthdVI7RQsSO8ujZed6H3WNHmTdhQB4SiWQIhvHeCQA7jvA+xqJ
-----END RSA PRIVATE KEY-----
			');
		$this->getConfigApi()->updateConfigurationValue('TEST_CERTIFICATE_PASSPHRASE', 'XYL5jCxWbwUw8i9mtb5C');
		$this->getConfigApi()->updateConfigurationValue('ORDER_ID_SCHEMA', '{id}');
		$this->getConfigApi()->updateConfigurationValue('POLL_TIMEOUT_SERVER', '30');
		$this->getConfigApi()->updateConfigurationValue('LOG_LEVEL', 'off');
		
		return true;
	}

	private function uninstallConfigurationValues(){
		$this->getConfigApi()->removeConfigurationValue('CREATE_PENDING_ORDER');
		$this->getConfigApi()->removeConfigurationValue('OPERATION_MODE');
		$this->getConfigApi()->removeConfigurationValue('MERCHANT_UUID');
		$this->getConfigApi()->removeConfigurationValue('CERTIFICATE_STRING');
		$this->getConfigApi()->removeConfigurationValue('CERTIFICATE_PASSPHRASE');
		$this->getConfigApi()->removeConfigurationValue('TEST_MERCHANT_UUID');
		$this->getConfigApi()->removeConfigurationValue('TEST_CERTIFICATE_STRING');
		$this->getConfigApi()->removeConfigurationValue('TEST_CERTIFICATE_PASSPHRASE');
		$this->getConfigApi()->removeConfigurationValue('ORDER_ID_SCHEMA');
		$this->getConfigApi()->removeConfigurationValue('POLL_TIMEOUT_SERVER');
		$this->getConfigApi()->removeConfigurationValue('LOG_LEVEL');
		
		return true;
	}


	/**
	 * The main method for the configuration page.
	 *
	 * @return string html output
	 */
	public function getContent(){
		$this->initialize();
		$this->context->controller->addCSS(_MODULE_DIR_ . $this->name . '/css/admin.css');
		
		$html = '';
		$html .= $this->checkLicense();
		
		if (isset($_POST['submit_twintcw'])) {
			
			if (isset($_POST[self::CREATE_PENDING_ORDER_KEY]) && $_POST[self::CREATE_PENDING_ORDER_KEY] == 'active') {
				$this->registerHook('actionMailSend');
				if (!self::isInstalled('mailhook')) {
					$html .= $this->displayError(
							TwintCw::translate(
									"The module 'Mail Hook' must be activated, when using the option 'create pending order', otherwise the mail sending behavior may be inappropriate."));
				}
			}
			
			$fields = $this->getConfigApi()->convertFieldTypes($this->getFormFields());
			$this->getConfigApi()->processConfigurationSaveAction($fields);
			$html .= $this->displayConfirmation(TwintCw::translate('Settings updated'));
		}
		
		$html .= $this->getConfigurationForm();
		
		return $html;
	}

	private function getConfigurationForm(){
		$link = new Link();
		$fields = $this->getConfigApi()->convertFieldTypes($this->getFormFields());
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get(
				'PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->id = (int) Tools::getValue('id_carrier');
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submit_twintcw';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab .
				 '&module_name=' . $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigApi()->getConfigurationValues($fields),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id 
		);
		
		$forms = array(
			array(
				'form' => array(
					'legend' => array(
						'title' => 'TWINT: ' . TwintCw::translate('Settings'),
						'icon' => 'icon-envelope' 
					),
					'input' => $fields,
					'submit' => array(
						'title' => TwintCw::translate('Save') 
					) 
				) 
			) 
		);
		
		return $helper->generateForm($forms);
	}

	protected function getFormFields(){
		$this->initialize();
		$fields = array(
			0 => array(
				'name' => 'CREATE_PENDING_ORDER',
 				'label' => $this->l("Create Pending Order"),
 				'desc' => $this->l("By creating pending orders the module will create a order before the payment is authorized. This not PrestaShop standard and may introduce some issues. However the module can send the order number to , which can reduce the overhead for the reconsilation. To use this feature the 'Mail Hook' module must be activated."),
 				'default' => 'inactive',
 				'type' => 'select',
 				'options' => array(
					'query' => array(
						0 => array(
							'id' => 'inactive',
 							'name' => $this->l("Inactive"),
 						),
 						1 => array(
							'id' => 'active',
 							'name' => $this->l("Active"),
 						),
 					),
 					'name' => 'name',
 					'id' => 'id',
 				),
 			),
 			1 => array(
				'name' => 'OPERATION_MODE',
 				'label' => $this->l("Operation Mode"),
 				'desc' => $this->l("Used to toggle test / live mode. The switching from
				test
				to live mode will change the used settings from test to live
				settings.
			"),
 				'default' => 'test',
 				'type' => 'select',
 				'options' => array(
					'query' => array(
						0 => array(
							'id' => 'test',
 							'name' => $this->l("Test Mode"),
 						),
 						1 => array(
							'id' => 'live',
 							'name' => $this->l("Live Mode"),
 						),
 					),
 					'name' => 'name',
 					'id' => 'id',
 				),
 			),
 			2 => array(
				'name' => 'MERCHANT_UUID',
 				'label' => $this->l("System ID"),
 				'desc' => $this->l("The system id found in the
				 backend under Sales Outlets /
				System ID.
			"),
 				'default' => '',
 				'type' => 'text',
 			),
 			3 => array(
				'name' => 'CERTIFICATE_STRING',
 				'label' => $this->l("Certificate"),
 				'desc' => $this->l("Here you can specify which certificate which will be
				used to encrypt the communication. The certificate can be found in
				the  backend under Settings /
				Order certificate from SwissSign. The file must be converted to a
				PEM file (see manual) and the content of the file copied into this
				field.
			"),
 				'default' => '',
 				'type' => 'textarea',
 			),
 			4 => array(
				'name' => 'CERTIFICATE_PASSPHRASE',
 				'label' => $this->l("Certificate Passphrase"),
 				'desc' => $this->l("Here you can set the passphrase for the certificate. The
				passphrase can be retrieved from the
				 backend up to five days after the
				certificate was created. If this period has run out a new
				certificate must be generated.
			"),
 				'default' => '',
 				'type' => 'text',
 			),
 			5 => array(
				'name' => 'TEST_MERCHANT_UUID',
 				'label' => $this->l("System ID (Test)"),
 				'desc' => $this->l("The system id found in the
				 backend under Sales Outlets /
				System ID.
			"),
 				'default' => 'ad013fee-a5e8-4de9-ad7d-f3efe0164177',
 				'type' => 'text',
 			),
 			6 => array(
				'name' => 'TEST_CERTIFICATE_STRING',
 				'label' => $this->l("Certificate (Test)"),
 				'desc' => $this->l("Here you can specify which certificate which will be
				used to encrypt the communication in test mode. The certificate can
				be found in the  backend under
				Settings / Order certificate from SwissSign. The file must be
				converted to a PEM file (see manual) and the content of the file
				copied into this field.
			"),
 				'default' => '-----BEGIN CERTIFICATE-----
MIIEejCCA2KgAwIBAgIBJzANBgkqhkiG9w0BAQsFADBRMQswCQYDVQQGEwJjaDEe
MBwGA1UEChMVQWROb3Z1bSBJbmZvcm1hdGlrIEFHMSIwIAYDVQQDExlUV0lOVCBQ
YXltZW50IEludGVncmF0aW9uMB4XDTE1MDYyNDA2NDE1MloXDTI1MDYyMTA2NDE1
MlowejELMAkGA1UEBhMCY2gxEjAQBgNVBAoTCUN1c3RvbXdlYjEhMB8GA1UEAxMY
VFdJTlQtVGVjaFVzZXIgQ3VzdG9td2ViMTQwMgYKCZImiZPyLGQBARMkMGU3Mjhl
ODgtZGI5Mi00M2E5LWJiZDUtZjNlNDEwYzE1YmEyMIIBIjANBgkqhkiG9w0BAQEF
AAOCAQ8AMIIBCgKCAQEA+mP9MrAPQzQRpB1tG+kA1OTEuMQbF049dL3W1ptbo0O9
tzw/jmmRm8FPesage8hUSsZ/yInIwsKCB/8/ApKm+qOZX4kEw0E8aSpcva3dHmHS
5rr3sNz/ZRk8skyBAlUECpmPKQ/fSMLlZ87rIvW7Y8kodhnL/Y5H+xHmWyhqOMKp
F2wjUUwQkGnf0x3Hv+yYzt5vHOjKUNOyPSuP9AlIc9dQqYMLCB7EXdncCIDQRcwU
EbsZ+IU+SOq9zEmYJ3Ztqpih/RP3hdmcrPHAnjM3O9pMtQg6YuXL06IR7/esjsrj
5RsSIOiyenofeOx3P+3btEVCSE3utxKZyfYuMaanHQIDAQABo4IBMjCCAS4wCQYD
VR0TBAIwADA/BglghkgBhvhCAQ0EMhYwTmV2aXMgS2V5Qm94IEdlbmVyYXRlZCBD
ZXJ0aWZpY2F0ZSB1c2luZyBPcGVuU1NMMAsGA1UdDwQEAwIDqDAdBgNVHSUEFjAU
BggrBgEFBQcDAQYIKwYBBQUHAwIwHQYDVR0OBBYEFOqaltfmWs96xVzJ88wOyDsN
+7Q1MIGBBgNVHSMEejB4gBSr68zVtfOKkp+Z+ErDlcUgrBpR0KFVpFMwUTELMAkG
A1UEBhMCY2gxHjAcBgNVBAoTFUFkTm92dW0gSW5mb3JtYXRpayBBRzEiMCAGA1UE
AxMZVFdJTlQgUGF5bWVudCBJbnRlZ3JhdGlvboIJAOJwo1TeThlPMBEGCWCGSAGG
+EIBAQQEAwIGwDANBgkqhkiG9w0BAQsFAAOCAQEAeQC3F3Ae9nqzt2VlQMnITY0j
fEHzP+dfBCOThdNsZUY1Wy4hqtxmFJ8esWp+h9u+zlqXh2dj0BNT1PNI5bmRV/Xf
vqTNZ7hCnQ1j1y8QaWPAo1koy0jaFRaVJ/u0dSvKtKuQj3Nh2Ba8fdCAlLqNevGc
HIWDTN9Il8+9cFNuh9X2h9H0wLw1pEjTQ5MdbY6JIG7Lqs0VucZS95rT5vMKZw2H
dl6BgzsUQ+m18MakLFUMok0BqW6KVDENpLYTrA+Ff8T4WiOdct1rIpzw5aWh4/ow
iPZcQJ15KoGadVZHFtCUzg7VWFL1B1sQm2M52ydBy1gebLgxvmxsDqXLgAGJLQ==
-----END CERTIFICATE-----
-----BEGIN RSA PRIVATE KEY-----
Proc-Type: 4,ENCRYPTED
DEK-Info: DES-EDE3-CBC,CEBA99B1C56178D0

XHqDIF781fUmHqh954bV4FMZi2NhAaV8HBV2LJBHJ6LVP3VEcFJyai1nzGClOzhM
rKEaW0T1OnBV5LK/EPSNIDRZyruluW0GrubrYA8dT8CWkR+Tg+pvr2SUvNX4vagZ
txBnpv7E97vXGWJokwKqFaJc2meZgq22SBCbc3tqtsFpZNGi2O5/IMEtqvG2txTe
MhQry53O0Ui4Uwxqkg/rXv0as+zkz54C9m/XC/tTWn3o1l+y+KOTKLx4yv02oycX
tS9ALLzxn+HS0WdNlHumBvtcqTO5o9hYepJ5JrBb4KFlCwjtDuHCe2DAdcq2IfaV
nDGgwoHpHmsK5czrjcgiDJBIayQm0N+1o7ye7OgNCeKh4cZLucEoX3I1RpF/9stf
isA9fjHGq11D7YGPca8MM1Upq+n0OHYc1ZIlHt60koRtL3RkfXhvzsvQyCLllm0N
hfyEujK9pwjOQVaCO+CmQvAzUaoQwj7D42QPKbXPmIvz91odE/ByqBUFHzNYJ9gq
qrS9sx6h1vQ4Ghp6VDPW+78N0nZ1Ws9iTr1KGwCTqRUGZ8Mqqgss00jvOoK/xvDy
Ah5q0SRXQqXWsPvOdSoIs9jK9vfAsoSIZyEz5VRaEoSb/Tln8w61rYoPYbFKYW/L
cip92iaIsJJZxpWJAufB7IFs02fQV/ZRPqKULEk75tH7cZYBNhEw+CW9rvyWZ7/i
1MO3Z7SD4haBt93o+T7IDLIgiwTyu/DUbblABI4cJGX+1h0SSj+yvjXdxythFN2R
rttsFfcgKTyMY5b0c/k4RmswtbkVS64WxxSWC4WYiPoxmZe5dZtmd6LHFs2xdnuQ
sicCUl0Ynn25eP3D50h0CNa/IP77SzIWiT6C/mqIJLI8CgrL+TGRI/Hs7LT0EXPG
UZmLN8+7BNNYidPNTmOc+N+Qt3qz2ZxMyQOoA0ZcyMadJr/WhPNQqRM1Tg6SGW1q
vR1twVnnuV7e6L7T0ri72wt9QrWVSS1hEvJiT7ClksP2cvoOBOKHNA7rwTpI4SJh
CB/WxfClWNpjjeB4jgSRJuhQ0AbxFkC4w7KR2PqQbTUycBdL2sbnnhrsmS2ZKLpJ
/V5TrdA/x/LxLlLbvQtFwYqiggcxOiZDqKbZRg1IjQcXnlPDuWVDqy4N62qzlw4m
7FrercyJaOj/YEoIRx15rMO+xDwYblp91bqYH1G6B9HnS9mv3VvznyKtWnMFUvJm
nOYom4Oytlne5pmYn4/I4NcOX+0ymKZ2MRjuLX3FdO5hVnilfqGPw0P6b7amEQ97
8oT1HgGidfDkboQgtYB1oAuggR1B75x30G3tmSvbTNmnbB8zLXV6Zik0TAOqqE1M
HST38OH/+D5kn2ZUKj5cZet5WuqRMXmPtVVO8fwwl2SdiuZNSW5M8jSVba+tEEZ4
CBNl0OFoxkTzunXUR/84bujBqcC1fATfoJWlUoqWIAGGwJUFfYTcfRyPZBG6L5Bq
fuaz5LdN/hC+KrVpMehuj3bzc7Dw6X4rngnlmgAragjaa0L2hFjcbnV1OkWzXm0r
rDHsoYzAp9CYrgthdVI7RQsSO8ujZed6H3WNHmTdhQB4SiWQIhvHeCQA7jvA+xqJ
-----END RSA PRIVATE KEY-----
			',
 				'type' => 'textarea',
 			),
 			7 => array(
				'name' => 'TEST_CERTIFICATE_PASSPHRASE',
 				'label' => $this->l("Certificate Passphrase (Test)"),
 				'desc' => $this->l("Here you can set the passphrase for the test
				certificate. The passphrase can be retrieved from the
				 backend up to five days after the
				certificate was created. If this period has run out a new
				certificate must be generated.
			"),
 				'default' => 'XYL5jCxWbwUw8i9mtb5C',
 				'type' => 'text',
 			),
 			8 => array(
				'name' => 'ORDER_ID_SCHEMA',
 				'label' => $this->l("Order Schema"),
 				'desc' => $this->l("Here you can modify what the order number looks like.
				The placeholder {id} will be replaced with the internal order number
				(e.g. 'MyShop-{id}'))
			"),
 				'default' => '{id}',
 				'type' => 'text',
 			),
 			9 => array(
				'name' => 'POLL_TIMEOUT_SERVER',
 				'label' => $this->l("Server Polling Timeout"),
 				'desc' => $this->l("The shop polls the transaction status until the
				transaction is successful. The polling
				timeout indicates the number
				of minutes the polling is executed
				before the transaction is
				cancelled.
			"),
 				'default' => '30',
 				'type' => 'text',
 			),
 			10 => array(
				'name' => 'LOG_LEVEL',
 				'label' => $this->l("Log Level"),
 				'desc' => $this->l("Messages of this or a higher level will be logged."),
 				'default' => 'off',
 				'type' => 'select',
 				'options' => array(
					'query' => array(
						0 => array(
							'id' => 'off',
 							'name' => $this->l("Off"),
 						),
 						1 => array(
							'id' => 'error',
 							'name' => $this->l("Error"),
 						),
 						2 => array(
							'id' => 'info',
 							'name' => $this->l("Info"),
 						),
 						3 => array(
							'id' => 'debug',
 							'name' => $this->l("Debug"),
 						),
 					),
 					'name' => 'name',
 					'id' => 'id',
 				),
 			),
 		);
		
		return $fields;
	}

	public function getPath(){
		return $this->_path;
	}

	public function hookDisplayHeader(){
		// In the one page checkout the CSS files are not loaded. This method adds therefore the missing CSS files on
		// this page.       	   	 	  			 	  
		if ($this->context->controller instanceof OrderOpcController) {
			$this->context->controller->addCSS(_MODULE_DIR_ . 'twintcw/css/style.css');
		}
	}

	public function hookDisplayBeforeShoppingCartBlock(){
		
		return '';
	}

	public function hookDisplayPDFInvoice($object){
		if (!isset($object['object'])) {
			return;
		}
		$orderInvoice = $object['object'];
		if (!($orderInvoice instanceof OrderInvoice)) {
			return;
		}
		$this->initialize();
		$transactions = TwintCw_Entity_Transaction::getTransactionsByOrderId($orderInvoice->id_order);
		$transactionObject = null;
		foreach ($transactions as $transaction) {
			if ($transaction->getTransactionObject() !== null && $transaction->getTransactionObject()->isAuthorized()) {
				$transactionObject = $transaction->getTransactionObject();
				break;
			}
		}
		if ($transactionObject === null) {
			return;
		}
		$paymentInformation = $transactionObject->getPaymentInformation();
		$result = '';
		if (!empty($paymentInformation)) {
			$result .= '<div class="twintcw-invoice-payment-information" id="twintcw-invoice-payment-information">';
			$result .= $paymentInformation;
			$result .= '</div>';
		}
		return $result;
	}
	
	
	private function handleChangesForAuthController(){
		
	}
	
	
	public function sortCheckouts($a, $b){
		if (isset($a['sortOrder']) && isset($b['sortOrder'])) {
			if ($a['sortOrder'] < $b['sortOrder']) {
				return -1;
			}
			else if ($a['sortOrder'] > $b['sortOrder']) {
				return 1;
			}
			else {
				return 0;
			}
		}
		else {
			return 0;
		}
	}

	public function hookBackOfficeHeader(){
		$id_order = Tools::getValue('id_order');
		
		// Check if we need to ask the customer to refund the amount       	   	 	  			 	  
		if ((isset($_POST['partialRefund']) || isset($_POST['cancelProduct'])) && !isset($_GET['confirmed']) && !(isset($_POST['generateDiscountRefund']) && $_POST['generateDiscountRefund']== 'on')) {
			$this->initialize();
			$transaction = current(TwintCw_Entity_Transaction::getTransactionsByOrderId($id_order));
			if (is_object($transaction) && $transaction->getTransactionObject() !== null &&
					 $transaction->getTransactionObject()->isPartialRefundPossible()) {
				$order = new Order($id_order);
				if ($order->module == ('twintcw_' . $transaction->getPaymentMachineName())) {
					$url = '?controller=AdminTwintCwRefund&token=' . Tools::getAdminTokenLite('AdminTwintCwRefund');
					$url .= '&' . Customweb_Core_Url::parseArrayToString($_POST);
					header('Location: ' . $url);
					die();
				}
			}
		}
		
		if (isset($_POST['submitTwintCwRefundAuto'])) {
			$this->initialize();
			try {
				$transaction = current(TwintCw_Entity_Transaction::getTransactionsByOrderId($id_order));
				$this->refundTransaction($transaction->getTransactionId(), self::getRefundAmount($_POST));
			}
			catch (Exception $e) {
				$this->context->controller->errors[] = TwintCw::translate("Could not refund the transaction: ") . $e->getMessage();
				unset($_POST['partialRefund']);
				unset($_POST['cancelProduct']);
			}
		}
		
		

		
	}

	public function hookActionMailSend($data){
		$this->initialize();
		if ($this->isCreationOfPendingOrderActive()) {
			if (!isset($data['event'])) {
				throw new Exception("No item 'event' provided in the mail action function.");
			}
			$event = $data['event'];
			if (!($event instanceof MailMessageEvent)) {
				throw new Exception("Invalid type provided by the mail send action.");
			}
			
			if (self::isRecordingMailMessages()) {
				foreach ($event->getMessages() as $message) {
					self::$recordedMailMessages[] = $message;
				}
				$event->setMessages(array());
			}
		}
	}

	public static function isRecordingMailMessages(){
		return self::$recordMailMessages;
	}

	public static function startRecordingMailMessages(){
		self::$recordMailMessages = true;
		self::$recordedMailMessages = array();
	}

	/**
	 *
	 * @return MailMessage[]
	 */
	public static function stopRecordingMailMessages(){
		self::$recordMailMessages = false;
		
		return self::$recordedMailMessages;
	}

	public function isCreationOfPendingOrderActive(){
		$this->initialize();
		$createPendingOrder = $this->getConfigApi()->getConfigurationValue(self::CREATE_PENDING_ORDER_KEY);
		
		if ($createPendingOrder == 'active') {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * This method extracts the refund amount from the POST data.
	 *
	 * @param array $data
	 * @return float amount
	 */
	public static function getRefundAmount($data){
		$amount = 0;
		$order_detail_list = array();
		$isPartial = false;
		if (isset($data['partialRefund'])){
			$isPartial = true;
		}
		
		if($isPartial){
			if(isset($data['refund_voucher_off']) && $data['refund_voucher_off'] == "2" && isset($data['refund_voucher_choose'])){
				return $data['refund_voucher_choose'];
			}
			
		}else{
			if(isset($data['refund_total_voucher_off']) && $data['refund_total_voucher_off'] == "2" && isset($data['refund_total_voucher_choose'])){
				return $data['refund_total_voucher_choose'];
			}
		}
		
		if (isset($data['partialRefundProduct'])) {
			foreach ($data['partialRefundProduct'] as $id_order_detail => $amount_detail) {
				$order_detail_list[$id_order_detail]['quantity'] = (int) $data['partialRefundProductQuantity'][$id_order_detail];
				
				if (empty($amount_detail)) {
					$order_detail = new OrderDetail((int) $id_order_detail);
					$order_detail_list[$id_order_detail]['amount'] = $order_detail->unit_price_tax_incl *
							 $order_detail_list[$id_order_detail]['quantity'];
				}
				else {
					$order_detail_list[$id_order_detail]['amount'] = (float) str_replace(',', '.', $amount_detail);
				}
				$amount += $order_detail_list[$id_order_detail]['amount'];
			}
			
			$shipping_cost_amount = (float) str_replace(',', '.', $data['partialRefundShippingCost']);
			if ($shipping_cost_amount > 0) {
				$amount += $shipping_cost_amount;
			}
		}
		
		// When the amount is not zero, we should consider also cancelQuantity. Otherwise the partialRefundProduct contains already the relevant stufff and
		// we do not need to take a look on cancelQuantity.
		if (isset($data['cancelQuantity']) && $amount == 0) {
			foreach ($data['cancelQuantity'] as $id_order_detail => $quantity) {
				$q = (int) $quantity;
				if ($q > 0) {
					$order_detail = new OrderDetail((int) $id_order_detail);
					$line_amount = $order_detail->unit_price_tax_incl * $q;
					$amount += $line_amount;
				}
			}
		}
		
		if($isPartial){
			if(isset($data['refund_voucher_off']) && $data['refund_voucher_off'] == "1"){
				$amount -= trim($data['order_discount_price']);
			}
		}else{
			if(isset($data['refund_total_voucher_off']) && $data['refund_total_voucher_off'] == "1"){
				$amount -= trim($data['order_discount_price']);
			}
		}
		
		
		return $amount;
	}

	/**
	 * This method is used to add a special info field in the order
	 * Tab.
	 *
	 * @param array $params Hook parameters
	 * @return string the html output
	 */
	public function hookAdminOrder($params){
		$html = '';
		
		$order = new Order((int) $params['id_order']);
		if (!strstr($order->module, 'twintcw')) {
			return '';
		}
		$this->initialize();
		$errorMessage = '';
		try {
			$this->processAdminAction();
		}
		catch (Exception $e) {
			$errorMessage = $e->getMessage();
		}
		
		$transactions = TwintCw_Entity_Transaction::getTransactionsByCartOrOrder($order->id_cart, $order->id);
		
		if (is_array($transactions) && count($transactions) > 0) {
			
			$activeTransactionId = false;
			if (isset($_POST['id_transaction'])) {
				$activeTransactionId = $_POST['id_transaction'];
			}
			
			$this->context->smarty->assign(
					array(
						'order_id' => $params['id_order'],
						'base_url' => _PS_BASE_URL_SSL_ . __PS_BASE_URI__,
						'transactions' => $transactions,
						'date_format' => $this->context->language->date_format_full,
						'errorMessage' => $errorMessage,
						'activeTransactionId' => $activeTransactionId 
					));
// 			$this->error = $errorMessage;
			
			$this->context->controller->addCSS(_MODULE_DIR_ . $this->name . '/css/admin.css');
			$this->context->controller->addJS(_MODULE_DIR_ . $this->name . '/js/admin.js');
			$html .= $this->evaluateTemplate('/views/templates/back/admin_order.tpl');
		}
		
		return $html;
	}

	public function getConfigurationValue($key, $langId = null){
		return $this->getConfigApi()->getConfigurationValue($key, $langId);
	}

	public function hasConfigurationKey($key, $langId = null){
		return $this->getConfigApi()->hasConfigurationKey($key, $langId);
	}

	private function processAdminAction(){
		if (isset($_POST['id_transaction'])) {
			
			
			if (isset($_POST['submitTwintCwRefund'])) {
				$amount = null;
				if (isset($_POST['refund_amount'])) {
					$amount = $_POST['refund_amount'];
				}
				
				$close = false;
				if (isset($_POST['close']) && $_POST['close'] == '1') {
					$close = true;
				}
				$this->refundTransaction($_POST['id_transaction'], $amount, $close);
			}
			
			

			
			if (isset($_POST['submitTwintCwCancel'])) {
				$this->cancelTransaction($_POST['id_transaction']);
			}
			
			

			
			if (isset($_POST['submitTwintCwCapture'])) {
				$amount = null;
				if (isset($_POST['capture_amount'])) {
					$amount = $_POST['capture_amount'];
				}
				
				$close = false;
				if (isset($_POST['close']) && $_POST['close'] == '1') {
					$close = true;
				}
				$this->captureTransaction($_POST['id_transaction'], $amount, $close);
			}
			
		}
	}
	
	
	public function refundTransaction($transactionId, $amount = null, $close = false){
		$this->initialize();
		$dbTransaction = TwintCw_Entity_Transaction::loadById($transactionId);
		$adapter = TwintCw_Util::createContainer()->getBean('Customweb_Payment_BackendOperation_Adapter_Service_IRefund');
		if ($dbTransaction->getTransactionObject() != null && $dbTransaction->getTransactionObject()->isRefundPossible()) {
			if ($amount !== null) {
				$items = Customweb_Util_Invoice::getItemsByReductionAmount(
						$dbTransaction->getTransactionObject()->getTransactionContext()->getOrderContext()->getInvoiceItems(), $amount, 
						$dbTransaction->getTransactionObject()->getCurrencyCode());
				$adapter->partialRefund($dbTransaction->getTransactionObject(), $items, $close);
			}
			else {
				$adapter->refund($dbTransaction->getTransactionObject());
			}
			TwintCw_Util::getEntityManager()->persist($dbTransaction);
		}
		else {
			throw new Exception("The given transaction is not refundable.");
		}
	}
	
	

	
	public function captureTransaction($transactionId, $amount = null, $close = false){
		$this->initialize();
		$dbTransaction = TwintCw_Entity_Transaction::loadById($transactionId);
		$adapter = TwintCw_Util::createContainer()->getBean('Customweb_Payment_BackendOperation_Adapter_Service_ICapture');
		if ($dbTransaction->getTransactionObject() != null && $dbTransaction->getTransactionObject()->isCapturePossible()) {
			if ($amount !== null) {
				$items = Customweb_Util_Invoice::getItemsByReductionAmount(
						$dbTransaction->getTransactionObject()->getTransactionContext()->getOrderContext()->getInvoiceItems(), $amount, 
						$dbTransaction->getTransactionObject()->getCurrencyCode());
				$adapter->partialCapture($dbTransaction->getTransactionObject(), $items, $close);
			}
			else {
				$adapter->capture($dbTransaction->getTransactionObject());
			}
			TwintCw_Util::getEntityManager()->persist($dbTransaction);
		}
		else {
			throw new Exception("The given transaction is not capturable.");
		}
	}
	
	

	
	public function cancelTransaction($transactionId){
		$this->initialize();
		$dbTransaction = TwintCw_Entity_Transaction::loadById($transactionId);
		$adapter = self::createContainer()->getBean('Customweb_Payment_BackendOperation_Adapter_Service_ICancel');
		if ($dbTransaction->getTransactionObject() != null && $dbTransaction->getTransactionObject()->isCancelPossible()) {
			$adapter->cancel($dbTransaction->getTransactionObject());
			TwintCw_Util::getEntityManager()->persist($dbTransaction);
		}
		else {
			throw new Exception("The given transaction cannot be cancelled.");
		}
	}
	
	private function evaluateTemplate($file){
		return $this->display(__FILE__, $file);
	}

	public function l($string, $specific = null, $id_lang = null){
		return self::translate($string, $specific);
	}

	public static function translate($string, $sprintf = null, $module = 'twintcw'){
		$stringOriginal = $string;
		$string = str_replace("\n", " ", $string);
		$string = preg_replace("/\t++/", " ", $string);
		$string = preg_replace("/( +)/", " ", $string);
		$string = preg_replace("/[^a-zA-Z0-9]*/", "", $string);
		
		$rs = Translate::getModuleTranslation($module, $string, $module, $sprintf);
		if ($string == $rs) {
			$rs = $stringOriginal;
		}
		
		if ($sprintf !== null && is_array($sprintf)) {
			$rs = Customweb_Core_String::_($rs)->format($sprintf);
		}
		
		if (version_compare(_PS_VERSION_, '1.6') > 0) {
			return htmlspecialchars_decode($rs);
		}
		else {
			return $rs;
		}
	}

	public static function getAdminUrl($controller, array $params, $token = true){
		if ($token) {
			$params['token'] = Tools::getAdminTokenLite($controller);
		}
		$id_lang = Context::getContext()->language->id;
		$path = Dispatcher::getInstance()->createUrl($controller, $id_lang, $params, false);
		$protocol = 'http://';
		$sslEnabled = Configuration::get('PS_SSL_ENABLED');
		$sslEverywhere = Configuration::get('PS_SSL_ENABLED_EVERYWHERE');
		if ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') || $sslEnabled == '1' || $sslEverywhere == '1'){
			$protocol = 'https://';
		}
		
		return $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER["SCRIPT_NAME"]) . '/' . ltrim($path, '/');
	}

	private static function getShopIds(){
		$shops = array();
		$rs = Db::getInstance()->query('
				SELECT
					id_shop
				FROM
					`' . _DB_PREFIX_ . 'shop`');
		foreach ($rs as $data) {
			$shops[] = $data['id_shop'];
		}
		return $shops;
	}
	
	private function registerLogListener(){
		if (!self::$logListenerRegistered) {
			self::$logListenerRegistered = true;
			$level = TwintCw::getInstance()->getConfigurationValue('log_level');
			if(strtolower($level) != 'off'){
				Customweb_Core_Logger_Factory::addListener(new TwintCw_LoggingListener());
			}
		}
	}
}

// Register own translation function in smarty       	   	 	  			 	  
if (!function_exists('cwSmartyTranslate')) {
	global $smarty;

	function cwSmartyTranslate($params, $smarty){
		$sprintf = isset($params['sprintf']) ? $params['sprintf'] : null;
		if (empty($params['mod'])) {
			throw new Exception(sprintf("Could not translate string '%s' because no module was provided.", $params['s']));
		}
		
		return TwintCw::translate($params['s'], $sprintf, $params['mod']);
	}
	smartyRegisterFunction($smarty, 'function', 'lcw', 'cwSmartyTranslate', false);
}




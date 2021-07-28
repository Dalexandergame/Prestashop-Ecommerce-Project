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

require_once 'Customweb/Twint/Exception/ConfigurationException.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Core/Stream/Input/String.php';



/**
 *
 * @author Sebastian Bossert
 * @Bean
 */
class Customweb_Twint_Configuration {
	/**
	 * How long the cronjob should poll before failing a transaction.
	 * In minutes.
	 *
	 * @var integer
	 */
	const POLLING_TIMEOUT = 60;
	/**
	 * In what intervals the cronjob should send update requests.
	 * In minutes.
	 *
	 * @var unknown
	 */
	const POLLING_INTERVAL = 15;
	
	/**
	 *
	 * @var Customweb_Payment_IConfigurationAdapter
	 */
	private $configurationAdapter = null;

	public function __construct(Customweb_Payment_IConfigurationAdapter $configurationAdapter){
		$this->configurationAdapter = $configurationAdapter;
	}

	public function getOrderSchema(){
		$value = $this->getConfigurationValue('order_id_schema');
		if (empty($value)) {
			$value = "{id}";
		}
		return $value;
	}

	public function getMerchantUuid(){
		if ($this->isLiveMode()) {
			return $this->getConfigurationValue('merchant_uuid', Customweb_I18n_Translation::__("System ID"));
		}
		else {
			return $this->getConfigurationValue('test_merchant_uuid', Customweb_I18n_Translation::__("System ID (Test)"));
		}
	}

	/**
	 *
	 * @return Customweb_Core_Stream_IInput
	 */
	public function getCertificate(){
		if ($this->isLiveMode()) {
			return new Customweb_Core_Stream_Input_String(
					$this->getConfigurationValue('certificate_string', Customweb_I18n_Translation::__("Certificate")), 'application/x-pem-file');
		}
		else {
			return new Customweb_Core_Stream_Input_String(
					$this->getConfigurationValue('test_certificate_string', Customweb_I18n_Translation::__("Certificate (Test)")), 
					'application/x-pem-file');
		}
	}

	public function getUrl(){
		if ($this->isLiveMode()) {
			return trim('https://service.twint.ch/merchant/service/TWINTMerchantServiceV2');
		}
		return trim('https://service-pat.twint.ch/merchant/service/TWINTMerchantServiceV2');
	}
	
	public function getAppSwitchUrl() {
		if($this->isLiveMode()) {
			return trim('https://service.twint.ch/appSwitch/v1/configs');
		}
		return trim('https://service-int.twint.ch/appSwitch/v1/configs');
	}

	/**
	 *
	 * @return string
	 */
	public function getCertificatePassphrase(){
		if ($this->isLiveMode()) {
			return $this->getConfigurationValue('certificate_passphrase', Customweb_I18n_Translation::__("Certificate Passphrase"));
		}
		else {
			return $this->getConfigurationValue('test_certificate_passphrase', Customweb_I18n_Translation::__("Certificate Passphrase (Test)"));
		}
	}

	public function isLiveMode(){
		return $this->getConfigurationValue('operation_mode') == 'live';
	}

	/**
	 * Retrieves a configuration value.
	 *
	 * @param string $key The key of the value to be retrieved
	 * @param string $label The translated label on the configuration value. If empty, the empty check will not be performed
	 * @return mixed
	 * @throws Customweb_Twint_Exception_ConfigurationException
	 */
	private function getConfigurationValue($key, $label = null, $language = null){
		$value = $this->configurationAdapter->getConfigurationValue($key, $language);
		if (is_string($value)) {
			$value = trim($value);
		}
		if ($label !== null && empty($value)) {
			throw new Customweb_Twint_Exception_ConfigurationException($label, $key, $language);
		}
		return $value;
	}
}


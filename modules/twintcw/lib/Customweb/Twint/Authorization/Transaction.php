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

require_once 'Customweb/Core/DateTime.php';
require_once 'Customweb/Twint/Configuration.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Payment/Authorization/DefaultTransaction.php';


/**
 *
 * @author Sebastian Bossert
 */
class Customweb_Twint_Authorization_Transaction extends Customweb_Payment_Authorization_DefaultTransaction {
	private $formKey;
	private $token;
	private $qrCode;

	/**
	 * Returns true if the server polling limit was exceeded.
	 *
	 * @param Customweb_Twint_Configuration $configuration
	 * @return boolean
	 */
	public function isPollTimeout(Customweb_Twint_Configuration $configuration){
		return Customweb_Core_DateTime::_($this->getCreatedOn())->add(new DateInterval('PT' . Customweb_Twint_Configuration::POLLING_TIMEOUT . 'M') )->getTimestamp() <=
				 time();
	}

	/**
	 * Sets the key use to create the hash to verify continue / cancel in the widget.
	 *
	 * @param string $key
	 */
	public function setFormKey($key){
		$this->formKey = $key;
	}

	/**
	 * Gets the key use to create the hash to verify continue / cancel in the widget.
	 *
	 * @return string
	 */
	public function getFormKey(){
		return $this->formKey;
	}

	public function isRefundClosable(){
		return false;
	}

	protected function getTransactionSpecificLabels(){
		$labels = array();
		$labels['operating_mode'] = $this->getModeLabel();
		return $labels;
	}

	/**
	 * Creates a label used to denote if the transaction is a test or live transaction.
	 *
	 * @return array
	 */
	private function getModeLabel(){
		$value = Customweb_I18n_Translation::__('Test');
		if ($this->isLiveTransaction()) {
			$value = Customweb_I18n_Translation::__('Live');
		}
		return array(
			'label' => Customweb_I18n_Translation::__('Operating Mode'),
			'value' => $value 
		);
	}

	public function setToken($token){
		$this->token = $token;
		return $this;
	}

	public function getToken(){
		return $this->token;
	}

	public function setQRCode($qrCode){
		$this->qrCode = $qrCode;
		return $this;
	}

	public function getQRCode(){
		return $this->qrCode;
	}
}

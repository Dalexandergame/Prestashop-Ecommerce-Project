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

require_once 'Customweb/Payment/AbstractContainer.php';
require_once 'Customweb/Twint/TwintService.php';
require_once 'Customweb/Twint/Storage/IStorageKey.php';
require_once 'Customweb/Twint/Storage/Adapter.php';



/**
 *
 * @author Sebastian Bossert
 */
class Customweb_Twint_Container extends Customweb_Payment_AbstractContainer {
	private $service;
	private $storage;

	public function __construct(Customweb_DependencyInjection_IContainer $container){
		parent::__construct($container);
		$this->service = $this->createService();
	}

	/**
	 *
	 * @param Customweb_Twint_Authorization_Transaction $transaction
	 * @return Customweb_Twint_Method_IMethod
	 */
	public function getPaymentMethodByTransaction(Customweb_Twint_Authorization_Transaction $transaction){
		return $this->getPaymentMethod($transaction->getPaymentMethod(), $transaction->getAuthorizationMethod());
	}

	/**
	 *
	 * @param Customweb_Payment_Authorization_IPaymentMethod $pm
	 * @param string $authorization
	 * @return Customweb_Twint_Method_IMethod
	 */
	public function getPaymentMethod(Customweb_Payment_Authorization_IPaymentMethod $pm, $authorization){
		return $this->getPaymentMethodFactory()->getPaymentMethod($pm, $authorization);
	}

	/**
	 *
	 * @return Customweb_Twint_Configuration
	 */
	public function getConfiguration(){
		return $this->getBean('Customweb_Twint_Configuration');
	}

	/**
	 *
	 * @return Customweb_Twint_Storage_CashRegister
	 */
	public function getCashRegister(){
		return $this->getBean('Customweb_Twint_Storage_CashRegister');
	}

	/**
	 *
	 * @return Customweb_Twint_Storage_Adapter
	 */
	public function getStorageAdapter(){
		return new Customweb_Twint_Storage_Adapter($this->getBean('Customweb_Storage_IBackend'), 
				Customweb_Twint_Storage_IStorageKey::STORAGE_SPACE_KEY);
	}

	/**
	 * 
	 * @return Customweb_Twint_TwintService
	 */
	public function getSoapService(){
		return $this->service;
	}

	/**
	 *
	 * @return Customweb_Twint_Method_Factory
	 */
	protected function getPaymentMethodFactory(){
		return $this->getBean('Customweb_Twint_Method_Factory');
	}

	private function createService(){
		$passPhrase = $this->getConfiguration()->getCertificatePassphrase();
		$certificate = $this->getConfiguration()->getCertificate();
		$url = $this->getConfiguration()->getUrl();
		$service = new Customweb_Twint_TwintService($certificate, $passPhrase, $url);
		return $service;
	}
}
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
require_once 'Customweb/Twint/StubBuilder/ConfirmOrder.php';
require_once 'Customweb/Payment/Authorization/ErrorMessage.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Twint/Configuration.php';
require_once 'Customweb/Twint/AbstractAdapter.php';
require_once 'Customweb/Twint/Util.php';
require_once 'Customweb/Twint/StubBuilder/MonitorOrder.php';



/**
 *
 * @author Sebastian Bossert
 *
 */
class Customweb_Twint_Update_StatusHandler extends Customweb_Twint_AbstractAdapter {
	private $responseReasons;
	const SUCCESS = 0;
	const SUCCESS_PARTIAL = 1;
	const PROCESSING_PAIRING = 80;
	const PROCESSING_USER = 90;
	const PROCESSING_MERCHANT = 91;
	const FAILED = 100;
	const FAILED_TIMEOUT = 101;
	const FAILED_MERCHANT = 102;
	const FAILED_USER = 103;

	public function __construct(Customweb_DependencyInjection_IContainer $container){
		parent::__construct($container);
		$this->responseReasons = array(
			self::SUCCESS => Customweb_I18n_Translation::__("Payment successful"),
			self::SUCCESS_PARTIAL => Customweb_I18n_Translation::__("Payment partially successful"),
			self::PROCESSING_PAIRING => Customweb_I18n_Translation::__("Processing order:. Waiting for pairing from user."),
			self::PROCESSING_USER => Customweb_I18n_Translation::__("Processing order: Waiting for confirmation from user."),
			self::PROCESSING_MERCHANT => Customweb_I18n_Translation::__("Processing order: Waiting for confirmation from merchant."),
			self::FAILED => Customweb_I18n_Translation::__("Order failed."),
			self::FAILED_TIMEOUT => Customweb_I18n_Translation::__("Order failed: Order timeout."),
			self::FAILED_MERCHANT => Customweb_I18n_Translation::__("Order failed: Merchant aborted the process."),
			self::FAILED_USER => Customweb_I18n_Translation::__("Order failed: Customer aborted the process.") 
		);
	}

	/**
	 * Sends a request to check the status of a transaction.
	 *
	 * @param string $id Payment id of transaction to monitor
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_MonitorOrderResponseElement
	 */
	public function monitor($id){
		$builder = new Customweb_Twint_StubBuilder_MonitorOrder($this->getContainer(), $id);
		$stub = $builder->build();
		$response = $this->getContainer()->getSoapService()->monitorOrder($stub);
		return $response;
	}

	/**
	 * Checks the twint status of the given transaction, and handles it accordingly (authorization, capture, fail)
	 *
	 * @param Customweb_Twint_Authorization_Transaction $transaction
	 */
	public function update(Customweb_Twint_Authorization_Transaction $transaction){
		if(!$transaction->getPaymentId()) {
			return;
		}
		
		$response = $this->monitor($transaction->getPaymentId());
		
		$code = $response->getOrder()->getStatus()->getReason()->getCode()->get();
		switch ($code) {
			case self::SUCCESS:
				if (!$transaction->isAuthorized() && !$transaction->isAuthorizationFailed()) {
					$transaction->authorize();
				}
				// Success means the order is fully completed on twints side, and no more captures are possible.
				if (!$transaction->isCaptured()) {
					$transaction->capture();
				}
				$transaction->setUpdateExecutionDate(null);
				break;
			case self::SUCCESS_PARTIAL:
				$this->fail($transaction, 
						Customweb_I18n_Translation::__(
								"The option partial success is not supported. Please contact TWINT to disable this feature on your account."));
				
				$transaction->setUpdateExecutionDate(null);
				break;
			case self::PROCESSING_MERCHANT:
				$this->handleSuccess($transaction, $response);
				break;
			case self::FAILED:
			case self::FAILED_TIMEOUT:
			case self::FAILED_MERCHANT:
			case self::FAILED_USER:
				$this->fail($transaction, $this->getResponseMessage($code));
				$transaction->setUpdateExecutionDate(null);
				break;
			case self::PROCESSING_PAIRING:
			case self::PROCESSING_USER:
			default:
				if ($transaction->isPollTimeout($this->getContainer()->getConfiguration())) {
					$this->timeout($transaction, $response);
				}
				else {
					$transaction->setUpdateExecutionDate(
							Customweb_Core_DateTime::_()->addMinutes(Customweb_Twint_Configuration::POLLING_INTERVAL));
				}
				break;
		}
	}

	private function timeout(Customweb_Twint_Authorization_Transaction $transaction, Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_MonitorOrderResponseElement $response){
		Customweb_Twint_Util::cancelOrder($this->getContainer(), $transaction->getPaymentId());
		$this->fail($transaction, Customweb_I18n_Translation::__("The timeout was exceeded."));
	}

	private function handleSuccess(Customweb_Twint_Authorization_Transaction $transaction, Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_MonitorOrderResponseElement $response){
		if ($transaction->isAuthorized() || $transaction->isAuthorizationFailed()) {
			return;
		}
		$transaction->authorize();
		$paymentMethod = $this->getContainer()->getPaymentMethodByTransaction($transaction);
		if (!$paymentMethod->isDeferredCapturingActive()) {
			$this->confirmOrder($transaction, $response);
		}
	}

	private function confirmOrder(Customweb_Twint_Authorization_Transaction $transaction, Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_MonitorOrderResponseElement $response){
		$amount = $transaction->getAuthorizationAmount();
		$confirmBuilder = new Customweb_Twint_StubBuilder_ConfirmOrder($this->getContainer(), $transaction, $amount);
		$confirmStub = $confirmBuilder->build();
		$response = $this->getContainer()->getSoapService()->confirmOrder($confirmStub);
		if ($response->getOrder()->getStatus()->getStatus()->getCode()->get() == 0) {
			$transaction->capture();
		}
		else {
			$this->fail($transaction, ($this->getResponseMessage($response->getOrder()->getStatus()->getReason()->getCode())));
		}
	}

	/**
	 * Sets the authorization to failed or adds an error message if that is not possible.
	 *
	 * @param Customweb_Twint_Authorization_Transaction $transaction
	 * @param Customweb_I18n_ILocalizableString $message
	 */
	private function fail(Customweb_Twint_Authorization_Transaction $transaction, $message){
		if ($transaction->isAuthorizationFailed() || $transaction->isAuthorized()) {
			$transaction->addErrorMessage(new Customweb_Payment_Authorization_ErrorMessage($message));
		}
		else {
			$transaction->setAuthorizationFailed($message);
		}
	}

	/**
	 * Attempts to retrieve a status string depending on the supplied code.
	 *
	 * @param string $reasonCode
	 * @return Customweb_I18n_ILocalizableString
	 */
	private function getResponseMessage($reasonCode){
		if (isset($this->responseReasons[$reasonCode])) {
			return $this->responseReasons[$reasonCode];
		}
		else {
			return Customweb_I18n_Translation::__("Error: The response could not be mapped.");
		}
	}
}

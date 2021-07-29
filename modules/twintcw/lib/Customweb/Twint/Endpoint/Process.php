<?php

require_once 'Customweb/Twint/Container.php';
require_once 'Customweb/Twint/Update/StatusHandler.php';
require_once 'Customweb/Payment/Endpoint/Controller/Abstract.php';
require_once 'Customweb/Payment/Authorization/ErrorMessage.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Twint/Method/Template/Token.php';
require_once 'Customweb/Twint/Util.php';
require_once 'Customweb/Core/Http/Response.php';


/**
 *
 * @author Sebastian Bossert
 * @Controller("process")
 *
 */
class Customweb_Twint_Endpoint_Process extends Customweb_Payment_Endpoint_Controller_Abstract {

	/**
	 * @Action("poll")
	 *
	 * @param Customweb_Core_Http_IRequest $request
	 * @param Customweb_Twint_Authorization_Transaction
	 * @return Customweb_Core_Http_Response
	 */
	public function poll(Customweb_Core_Http_IRequest $request){
		$transactionIdMap = $this->getTransactionId($request);
		$transaction = $this->getTransactionHandler()->findTransactionByTransactionExternalId($transactionIdMap['id']);
		for ($i = 0; $i < 5; $i++) {
			try {
				$this->getTransactionHandler()->beginTransaction();
				$transaction = $this->getTransactionHandler()->findTransactionByTransactionExternalId($transactionIdMap['id'], false);
				if ($transaction->isAuthorized()) {
					$state = 'COMPLETE';
					$this->getTransactionHandler()->rollbackTransaction();
					return $this->getJsonResponse($state, $transaction->getSuccessUrl());
				}
				else if ($transaction->isAuthorizationFailed()) {
					$state = 'COMPLETE';
					$this->getTransactionHandler()->rollbackTransaction();
					return $this->getJsonResponse($state, $transaction->getFailedUrl());
				}
				$url = $this->process($request, $transaction);
				$this->getTransactionHandler()->persistTransactionObject($transaction);
				$this->getTransactionHandler()->commitTransaction();
				switch ($url) {
					case $transaction->getSuccessUrl():
					case $transaction->getFailedUrl():
						$state = 'COMPLETE';
						break;
					default:
						$state = 'UNKNOWN';
						break;
				}
				return $this->getJsonResponse($state, $url);
			}
			catch (Customweb_Payment_Exception_OptimisticLockingException $lockingException) {
				$this->getTransactionHandler()->rollbackTransaction();
				if ($i == 4) {
					throw $lockingException;
				}
				sleep(1);
			}
		}
		return $this->getJsonResponse('UNKNOWN', "");
	}

	/**
	 * @Action("continue")
	 *
	 * @param Customweb_Core_Http_IRequest $request
	 * @param Customweb_Twint_Authorization_Transaction
	 * @return Customweb_Core_Http_Response
	 */
	public function processContinue(Customweb_Core_Http_IRequest $request, Customweb_Twint_Authorization_Transaction $transaction){
		$parameters = $request->getParameters();
		if (!$this->isHashCorrect($transaction, $parameters)) {
			$transaction->addErrorMessage(
					new Customweb_Payment_Authorization_ErrorMessage(
							Customweb_I18n_Translation::__('An attempt to process the transaction was made with an incorrect hash.')));
			return Customweb_Core_Http_Response::redirect($transaction->getFailedUrl());
		}

		$url = $this->process($request, $transaction);
		switch ($url) {
			case $transaction->getSuccessUrl():
			case $transaction->getFailedUrl():
				break;
			default:
				Customweb_Twint_Util::cancelOrder(new Customweb_Twint_Container($this->getContainer()),
						$transaction->getPaymentId());
				$url = $transaction->getFailedUrl();
				break;
		}

		return Customweb_Core_Http_Response::redirect($url);
	}

	/**
	 * @Action("template")
	 *
	 * @param Customweb_Core_Http_IRequest $request
	 * @param Customweb_Twint_Authorization_Transaction
	 * @return Customweb_Core_Http_Response
	 */
	public function template(Customweb_Core_Http_IRequest $request, Customweb_Twint_Authorization_Transaction $transaction){
		$response = null;
		try {
			$response = $this->getPaymentMethod($transaction)->sendStartOrderRequest($transaction, array());
		}
		catch (Exception $e) {
			$transaction->setAuthorizationFailed($e->getMessage());
			return Customweb_Core_Http_Response::redirect($transaction->getFailedUrl());
		}
		
		$orderId = $response->getOrderUUID();
		if (empty($orderId)) {
			$transaction->setAuthorizationFailed(Customweb_I18n_Translation::__("The order could not be started: The order id is missing."));
			return Customweb_Core_Http_Response::redirect($transaction->getFailedUrl());
		}

		$transaction->setPaymentId($orderId->get());

		try {
			$templateRenderer = new Customweb_Twint_Method_Template_Token($this->getContainer(), $response, $transaction);
			return Customweb_Core_Http_Response::_($templateRenderer->renderTemplate());
		}
		catch (Exception $exc) {
			Customweb_Twint_Util::cancelOrder(new Customweb_Twint_Container($this->getContainer()),
					$transaction->getPaymentId());
			$transaction->setAuthorizationFailed($exc->getMessage());
			$transaction->addErrorMessage(
					new Customweb_Payment_Authorization_ErrorMessage(
							Customweb_I18n_Translation::__("The transaction has been cancelled due to an error.")));
			return Customweb_Core_Http_Response::redirect($transaction->getFailedUrl());
		}
	}

	/**
	 * 
	 * @param Customweb_Twint_Authorization_Transaction $transaction
	 * @return Customweb_Twint_Method_Twint
	 */
	protected function getPaymentMethod(Customweb_Twint_Authorization_Transaction $transaction){
		return $this->getContainer()->getBean('Customweb_Twint_Method_Factory')->getPaymentMethod($transaction->getPaymentMethod(),
				$transaction->getAuthorizationMethod());
	}

	/**
	 * @Action("cancel")
	 *
	 * @param Customweb_Core_Http_IRequest $request
	 * @param Customweb_Twint_Authorization_Transaction
	 * @return Customweb_Core_Http_Response
	 */
	public function cancel(Customweb_Core_Http_IRequest $request){
		$transactionIdMap = $this->getTransactionId($request);
		$transaction = $this->getTransactionHandler()->findTransactionByTransactionExternalId($transactionIdMap['id']);
		$parameters = $request->getParameters();
		if (!$this->isHashCorrect($transaction, $parameters)) {
			throw new Exception(Customweb_I18n_Translation::__('An attempt to cancel the transaction was made with an incorrect hash.')->toString());
		}
		for ($i = 0; $i < 5; $i++) {
			try {
				$this->getTransactionHandler()->beginTransaction();
				$transaction = $this->getTransactionHandler()->findTransactionByTransactionExternalId($transactionIdMap['id'], false);
				if ($transaction->isAuthorized()) {
					$this->getTransactionHandler()->rollbackTransaction();
					return Customweb_Core_Http_Response::redirect($transaction->getSuccessUrl());
				}
				else if ($transaction->isAuthorizationFailed()) {
					$this->getTransactionHandler()->rollbackTransaction();
					return Customweb_Core_Http_Response::redirect($transaction->getFailedUrl());
				}
				$this->executeUpdate($transaction);
				if ($transaction->isAuthorized()) {
					$this->getTransactionHandler()->persistTransactionObject($transaction);
					$this->getTransactionHandler()->commitTransaction();
					return Customweb_Core_Http_Response::redirect($transaction->getSuccessUrl());
				}
				else if (!$transaction->isAuthorizationFailed()) {
					try {
						Customweb_Twint_Util::cancelOrder(new Customweb_Twint_Container($this->getContainer()),
								$transaction->getPaymentId());
						$transaction->setAuthorizationFailed(Customweb_I18n_Translation::__('The order has been cancelled.'));
					}
					catch (Exception $exc) {
						$message = new Customweb_Payment_Authorization_ErrorMessage(Customweb_I18n_Translation::__('The order has been cancelled.'),
								Customweb_I18n_Translation::__(
										"The order was cancelled by the customer. But the order could not be cancelled at TWINT. Error: @error"),
								array(
									"@error" => $exc->getMessage()
								));
						$transaction->setAuthorizationFailed($message);
					}
				}
				$this->getTransactionHandler()->persistTransactionObject($transaction);
				$this->getTransactionHandler()->commitTransaction();
			}
			catch (Customweb_Payment_Exception_OptimisticLockingException $lockingException) {
				$this->getTransactionHandler()->rollbackTransaction();
				if ($i == 4) {
					throw $lockingException;
				}
				sleep(1);
			}
		}
		return Customweb_Core_Http_Response::redirect($transaction->getFailedUrl());
	}

	/**
	 * Verifies if the hash is present in the parameters, and if it is correct.
	 *
	 * @param Customweb_Twint_Authorization_Transaction $transaction
	 * @param array $parameters
	 * @return boolean
	 */
	private function isHashCorrect(Customweb_Twint_Authorization_Transaction $transaction, array $parameters){
		if (isset($parameters['hash'])) {
			if (hash_hmac('sha1', $transaction->getExternalTransactionId(), $transaction->getFormKey()) == $parameters['hash']) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Updates the given transaction, and returns the appropriate url to redirect to.
	 *
	 * @param Customweb_Core_Http_IRequest $request
	 * @param Customweb_Twint_Authorization_Transaction $transaction
	 * @return string
	 */
	private function process(Customweb_Core_Http_IRequest $request, Customweb_Twint_Authorization_Transaction $transaction){
		$this->executeUpdate($transaction);
		if ($transaction->isAuthorized()) {
			$url = $transaction->getSuccessUrl();
		}
		else if ($transaction->isAuthorizationFailed()) {
			$url = $transaction->getFailedUrl();
		}
		else {
			$url = "";
		}

		return $url;
	}

	/**
	 * Executes an update on the transaction, and catches any occurring exceptions.
	 *
	 * @param Customweb_Twint_Authorization_Transaction $transaction
	 */
	private function executeUpdate(Customweb_Twint_Authorization_Transaction $transaction){
		try {
			$statusHandler = new Customweb_Twint_Update_StatusHandler($this->getContainer());
			$statusHandler->update($transaction);
		}
		catch (Exception $exc) {
		}
	}

	/**
	 * Creates a json object as response:
	 * {"status":"$status", "url":"$url", "timestamp": "time()"}
	 *
	 * @param string $status COMPLETE / UNKNOWN
	 * @param string $url
	 * @return Customweb_Core_Http_Response
	 */
	private function getJsonResponse($status, $url){
		$json = '{"status":"' . $status . '", "redirect":"' . $url . '", "timestamp": "' . time() . '"}';
		return Customweb_Core_Http_Response::_($json)->setContentType("application/json");
	}
}

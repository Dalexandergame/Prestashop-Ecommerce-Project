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

require_once 'Customweb/Twint/Authorization/Transaction.php';
require_once 'Customweb/Core/DateTime.php';
require_once 'Customweb/Payment/Authorization/Widget/IAdapter.php';
require_once 'Customweb/Twint/Configuration.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Twint/AbstractAdapter.php';
require_once 'Customweb/Core/Util/Rand.php';


/**
 *
 * @author Sebastian Bossert
 * @Bean
 */
class Customweb_Twint_Authorization_Widget_Adapter extends Customweb_Twint_AbstractAdapter implements 
		Customweb_Payment_Authorization_Widget_IAdapter {

	public function getAdapterPriority(){
		return 1000;
	}

	public function getAuthorizationMethodName(){
		return self::AUTHORIZATION_METHOD_NAME;
	}

	public function createTransaction(Customweb_Payment_Authorization_Widget_ITransactionContext $transactionContext, $failedTransaction){
		$transaction = new Customweb_Twint_Authorization_Transaction($transactionContext);
		$transaction->setLiveTransaction($this->getContainer()->getConfiguration()->isLiveMode());
		$transaction->setAuthorizationMethod($this->getAuthorizationMethodName());
		$transaction->setFormKey(Customweb_Core_Util_Rand::getRandomString(20));
		$transaction->setUpdateExecutionDate(Customweb_Core_DateTime::_()->addMinutes(Customweb_Twint_Configuration::POLLING_INTERVAL));
		return $transaction;
	}

	public function getWidgetHTML(Customweb_Payment_Authorization_ITransaction $transaction, array $formData){
		try {
			$url = $this->getContainer()->getEndpointAdapter()->getUrl('process', 'template',
					array(
						"cw_transaction_id" => $transaction->getExternalTransactionId() 
					));
			return "<meta http-equiv='Refresh' content='0; url=.$url.'><script type='text/javascript'>window.location = '$url';</script>";
		}
		catch (Exception $e) {
			$transaction->setAuthorizationFailed($e->getMessage());
			$failedMessage = Customweb_I18n_Translation::__("The transaction failed with message '@message'. Please try using another payment method",
					array(
						"@message" => $e->getMessage() 
					));
			$failUrl = $transaction->getFailedUrl();
			return "<div id='___module____-failed-widget'><p>$failedMessage</p><a class='btn button btn-warning' href='$failUrl'>Back</a></div>";
		}
	}

	public function getVisibleFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $paymentCustomerContext){
		return array();
	}

	public function isAuthorizationMethodSupported(Customweb_Payment_Authorization_IOrderContext $orderContext){
		return true;
	}

	public function isDeferredCapturingSupported(Customweb_Payment_Authorization_IOrderContext $orderContext, Customweb_Payment_Authorization_IPaymentCustomerContext $paymentContext){
		return true;
	}

	public function preValidate(Customweb_Payment_Authorization_IOrderContext $orderContext, Customweb_Payment_Authorization_IPaymentCustomerContext $paymentContext){
		$this->getContainer()->getPaymentMethod($orderContext->getPaymentMethod(), $this->getAuthorizationMethodName())->preValidate($orderContext,
				$paymentContext);
	}

	public function validate(Customweb_Payment_Authorization_IOrderContext $orderContext, Customweb_Payment_Authorization_IPaymentCustomerContext $paymentContext, array $formData){
		$this->getContainer()->getPaymentMethod($orderContext->getPaymentMethod(), $this->getAuthorizationMethodName())->validate($orderContext,
				$paymentContext, $formData);
	}
}
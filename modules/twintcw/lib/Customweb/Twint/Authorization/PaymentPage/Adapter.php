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
require_once 'Customweb/Twint/Configuration.php';
require_once 'Customweb/Twint/AbstractAdapter.php';
require_once 'Customweb/Payment/Authorization/PaymentPage/IAdapter.php';
require_once 'Customweb/Core/Util/Rand.php';


/**
 *
 * @author Sebastian Bossert
 * @Bean
 */
class Customweb_Twint_Authorization_PaymentPage_Adapter extends Customweb_Twint_AbstractAdapter implements 
		Customweb_Payment_Authorization_PaymentPage_IAdapter {

	public function getAdapterPriority(){
		return 100;
	}

	public function getAuthorizationMethodName(){
		return self::AUTHORIZATION_METHOD_NAME;
	}

	public function createTransaction(Customweb_Payment_Authorization_PaymentPage_ITransactionContext $transactionContext, $failedTransaction){
		$transaction = new Customweb_Twint_Authorization_Transaction($transactionContext);
		$transaction->setLiveTransaction($this->getContainer()->getConfiguration()->isLiveMode());
		$transaction->setAuthorizationMethod($this->getAuthorizationMethodName());
		$transaction->setFormKey(Customweb_Core_Util_Rand::getRandomString(20));
		$transaction->setUpdateExecutionDate(Customweb_Core_DateTime::_()->addMinutes(Customweb_Twint_Configuration::POLLING_INTERVAL));
		return $transaction;
	}

	public function getVisibleFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $paymentCustomerContext){
		return $this->getContainer()->getPaymentMethod($orderContext->getPaymentMethod(), $this->getAuthorizationMethodName())->getVisibleFormFields(
				$orderContext, $aliasTransaction, $failedTransaction, $paymentCustomerContext);
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

	public function getFormActionUrl(Customweb_Payment_Authorization_ITransaction $transaction, array $formData){
		return $this->getRedirectionUrl($transaction, $formData);
	}

	public function getParameters(Customweb_Payment_Authorization_ITransaction $transaction, array $formData){
		return array();
	}

	public function isHeaderRedirectionSupported(Customweb_Payment_Authorization_ITransaction $transaction, array $formData){
		return strlen($this->getRedirectionUrl($transaction, $formData)) < 2000;
	}

	public function getRedirectionUrl(Customweb_Payment_Authorization_ITransaction $transaction, array $formData){
		return $this->getContainer()->getEndpointAdapter()->getUrl('process', 'template',
				array(
					"cw_transaction_id" => $transaction->getExternalTransactionId() 
				));
	}
}
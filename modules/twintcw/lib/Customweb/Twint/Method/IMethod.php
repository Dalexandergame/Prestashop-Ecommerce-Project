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

require_once 'Customweb/Payment/Authorization/IPaymentMethodWrapper.php';



/**
 *
 * @author Sebastian Bossert
 */
interface Customweb_Twint_Method_IMethod extends Customweb_Payment_Authorization_IPaymentMethodWrapper {

	/**
	 * Cancels the given transaction.
	 *
	 * @param Customweb_Payment_Authorization_ITransaction $transaction
	 */
	public function cancel(Customweb_Twint_Authorization_Transaction $transaction);

	/**
	 * Refunds the given items on the given transaction.
	 *
	 * @param Customweb_Payment_Authorization_ITransaction $transaction
	 * @param unknown $items
	 * @param boolean $close
	 */
	public function partialRefund(Customweb_Twint_Authorization_Transaction $transaction, $items, $close);

	/**
	 * Captures the given items on the given transaction.
	 *
	 * @param Customweb_Payment_Authorization_ITransaction $transaction
	 * @param unknown $items
	 * @param unknown $close
	 */
	public function partialCapture(Customweb_Twint_Authorization_Transaction $transaction, $items, $close);

	/**
	 * Returns the form fields used to create a transaction.
	 *
	 * @param Customweb_Payment_Authorization_IOrderContext $orderContext
	 * @param Customweb_Twint_Authorization_Transaction | null $aliasTransaction
	 * @param Customweb_Twint_Authorization_Transaction | null $failedTransaction
	 * @param Customweb_Payment_Authorization_IPaymentCustomerContext $paymentCustomerContext
	 *
	 * @return array
	 */
	public function getVisibleFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $paymentCustomerContext);

	/**
	 * Validates the given data, and throws an exception if something invalid is found.
	 *
	 * @param Customweb_Payment_Authorization_IOrderContext $orderContext
	 * @param Customweb_Payment_Authorization_IPaymentCustomerContext $paymentContext
	 * @param array $formData
	 *
	 * @throws Exception
	 */
	public function validate(Customweb_Payment_Authorization_IOrderContext $orderContext, Customweb_Payment_Authorization_IPaymentCustomerContext $paymentContext, array $formData);

	/**
	 * True if the payment is deferred.
	 *
	 * @return boolean
	 */
	public function isDeferredCapturingActive();

	/**
	 * Creates the HTML which is displayed to the user.
	 *
	 * @param Customweb_Payment_Authorization_ITransaction $transaction
	 * @param array $formData
	 *
	 * @return string
	 */
	public function getWidgetHTML(Customweb_Payment_Authorization_ITransaction $transaction, array $formData);
}

<?php
/**
  * You are allowed to use this API in your web application.
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

class TwintCwWidgetModuleFrontController extends ModuleFrontController
{
	public $ssl = true;

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent(){
		
		
		require_once 'TwintCw/Util.php';
require_once 'TwintCw/Adapter/WidgetAdapter.php';
require_once 'TwintCw/Entity/Transaction.php';

		
		if (Module::isInstalled('mailhook')) {
			require_once rtrim(_PS_MODULE_DIR_, '/') . '/mailhook/MailMessage.php';
			require_once rtrim(_PS_MODULE_DIR_, '/') . '/mailhook/MailMessageAttachment.php';
			require_once rtrim(_PS_MODULE_DIR_, '/') . '/mailhook/MailMessageEvent.php';
		}
	
		$this->addCSS($this->module->getPath() . 'css/style.css', 'all');
		$this->display_column_left = false;


		parent::initContent();

		/* @var $module TwintCw_IPaymentMethod */
		$module = PaymentModule::getInstanceById(Tools::getValue('id_module', null));

		// Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
		$authorized = false;
		foreach (Module::getPaymentModules() as $m) {
			if ($m['name'] == $module->name) {
				$authorized = true;
				break;
			}
		}
		if (!$authorized) {
			die(Tools::displayError('This payment method is not available.'));
		}

		try {
			$adapter = $module->getAuthorizationAdapter($module->getOrderContext());
			$customerContext = TwintCw_Util::getPaymentCustomerContext($module->getOrderContext()->getCustomerId());
			$adapter->validate($module->getOrderContext(), $customerContext, $_REQUEST);
		}
		catch (Exception $e) {
			parent::initContent();
			$this->errors[] = $e->getMessage();
			$link = new Link();

			$url = $link->getPageLink('order', true, NULL);

			$this->redirectWithNotifications($url);
			return;
		}

		$transactionId = Tools::getValue('cw_transaction_id', null);
		$aliasTransactionId = null;
		$tempAliasId = Tools::getValue('cw_alias_id', null);
		if ($tempAliasId !== null) {
			if ($tempAliasId == 'new') {
				$aliasTransactionId = 'new';
			}
			else {
				$aliasTransaction = TwintCw_Entity_Transaction::loadById($tempAliasId);
				if ($aliasTransaction !== null && $aliasTransaction->getTransactionObject() != null && $aliasTransaction->getTransactionObject()->getTransactionContext()->getOrderContext()->getCustomerId() ===
						$module->getOrderContext()->getCustomerId()) {
							$aliasTransactionId = $tempAliasId;
						}
			}
		}

		if (empty($transactionId)) {
			$transaction = $module->createTransaction($module->getOrderContext(), $aliasTransactionId);
		}
		else {
			$transaction = TwintCw_Entity_Transaction::loadById($transactionId);
		}

		$wrapper = TwintCw_Util::getShopAdapterByPaymentAdapter(TwintCw_Util::getAuthorizationAdapter($transaction->getTransactionObject()->getAuthorizationMethod()));

		if (!($wrapper instanceof TwintCw_Adapter_WidgetAdapter)) {
			throw new Exception("Expect 'TwintCw_Adapter_WidgetAdapter'.");
		}

		$variables = array();
		$wrapper->prepareWithFormData($_REQUEST, $transaction);
		$variables['widget'] = $wrapper->getWidget();
		TwintCw_Util::getEntityManager()->persist($transaction);

		$this->context->smarty->assign($variables);

		$this->setTemplate('module:twintcw/views/templates/front/widget.tpl');
	}
}

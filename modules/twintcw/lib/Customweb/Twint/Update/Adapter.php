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

require_once 'Customweb/Twint/Container.php';
require_once 'Customweb/Core/DateTime.php';
require_once 'Customweb/Twint/Update/StatusHandler.php';
require_once 'Customweb/Payment/Authorization/ITransactionHistoryItem.php';
require_once 'Customweb/Payment/Update/IAdapter.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Twint/Configuration.php';
require_once 'Customweb/Payment/Authorization/DefaultTransactionHistoryItem.php';



/**
 *
 * @author Sebastian Bossert
 * @Bean
 *
 */
class Customweb_Twint_Update_Adapter implements Customweb_Payment_Update_IAdapter {
	private $container;

	public function __construct(Customweb_DependencyInjection_IContainer $container){
		$this->container = new Customweb_Twint_Container($container);
	}

	public function updateTransaction(Customweb_Payment_Authorization_ITransaction $transaction){
		/* @var $transaction Customweb_Twint_Authorization_Transaction */
		$statusHandler = new Customweb_Twint_Update_StatusHandler($this->getContainer());
		try {
			$statusHandler->update($transaction);
		}
		catch (Customweb_Payment_Exception_PaymentErrorException $pExc) {
			$transaction->addHistoryItem(
					new Customweb_Payment_Authorization_DefaultTransactionHistoryItem(
							Customweb_I18n_Translation::__("@message", array(
								"@message" => $pExc->getErrorMessage() 
							)), Customweb_Payment_Authorization_ITransactionHistoryItem::ACTION_LOG));
			$transaction->setUpdateExecutionDate(
					Customweb_Core_DateTime::_()->addMinutes(Customweb_Twint_Configuration::POLLING_INTERVAL));
		}
		catch (Exception $exc) {
			$transaction->setUpdateExecutionDate(
					Customweb_Core_DateTime::_()->addMinutes(Customweb_Twint_Configuration::POLLING_INTERVAL));
		}
	}

	protected function getContainer(){
		return $this->container;
	}
}
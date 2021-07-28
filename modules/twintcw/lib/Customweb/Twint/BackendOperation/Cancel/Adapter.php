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
require_once 'Customweb/Twint/AbstractAdapter.php';
require_once 'Customweb/Payment/BackendOperation/Adapter/Service/ICancel.php';



/**
 *
 * @author Sebastian Bossert
 * @Bean
 *
 */
class Customweb_Twint_BackendOperation_Cancel_Adapter extends Customweb_Twint_AbstractAdapter implements 
		Customweb_Payment_BackendOperation_Adapter_Service_ICancel {

	public function cancel(Customweb_Payment_Authorization_ITransaction $transaction){
		if (!$transaction instanceof Customweb_Twint_Authorization_Transaction) {
			throw new Exception("Transaction must be instance of Customweb_Twint_Authorization_Transaction.");
		}
		
		$this->getContainer()->getPaymentMethodByTransaction($transaction)->cancel($transaction);
	}
}
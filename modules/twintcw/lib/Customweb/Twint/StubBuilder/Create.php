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

require_once 'Customweb/Twint/StubBuilder/AbstractStartOrder.php';



/**
 *
 * @author Sebastian Bossert
 */
class Customweb_Twint_StubBuilder_Create extends Customweb_Twint_StubBuilder_AbstractStartOrder {

	/**
	 * Creates an order used for starting a transaction.
	 * 
	 * @param Customweb_Twint_Authorization_Transaction $transaction
	 * @param Customweb_DependencyInjection_IContainer $container
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderKindType $orderType
	 */
	public function __construct(Customweb_Twint_Authorization_Transaction $transaction, Customweb_DependencyInjection_IContainer $container, Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderKindType $orderType){
		parent::__construct($transaction, $container, $orderType, $transaction->getAuthorizationAmount());
	}

	protected function addQrCodeRendering(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderRequestElement $startOrder){
		return $startOrder->setQRCodeRendering(true);
	}
}
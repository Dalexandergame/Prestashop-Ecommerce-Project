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

require_once 'Customweb/Util/Rand.php';
require_once 'Customweb/Twint/StubBuilder/EnrollCashRegister.php';
require_once 'Customweb/Twint/StubBuilder/CancelOrder.php';



/**
 *
 * @author Sebastian Bossert
 */
class Customweb_Twint_Util {
	public static function generateCashRegisterId(Customweb_Twint_Container $container){
		$id = Customweb_Util_Rand::getRandomString(50);
		$stubBuilder = new Customweb_Twint_StubBuilder_EnrollCashRegister($container, $id);
		$stub = $stubBuilder->build();
		$container->getSoapService()->enrollCashRegister($stub);
		$container->getCashRegister()->saveCashRegisterId($id);
	}

	public static function cancelOrder(Customweb_Twint_Container $container, $orderId){
		$cancelBuilder = new Customweb_Twint_StubBuilder_CancelOrder($container, $orderId);
		$cancelStub = $cancelBuilder->build();
		$container->getSoapService()->cancelOrder($cancelStub);
	}
	
}
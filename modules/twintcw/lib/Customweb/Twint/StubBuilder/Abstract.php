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

require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/MerchantInformationType.php';
require_once 'Customweb/Twint/AbstractAdapter.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/Token50Type.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/UuidType.php';



/**
 *
 * @author Sebastian Bossert
 */
abstract class Customweb_Twint_StubBuilder_Abstract extends Customweb_Twint_AbstractAdapter {

	/**
	 * Creates the MerchantInformation stub
	 *
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType
	 */
	protected final function getMerchantInformation(){
		$config = $this->getContainer()->getConfiguration();
		//@formatter:off
		return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType::_()
				->setMerchantUUID(Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType::_()
						->set($config->getMerchantUuid()))
				->setCashRegisterId(Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type::_()
						->set($this->getCashRegisterId()));
		//@formatter:on
	}

	/**
	 * Returns the cash register id.
	 * Can be overwritten if a new one should be created (enrollCashRegister)
	 *
	 * @return string
	 */
	protected function getCashRegisterId(){
		return $this->getContainer()->getCashRegister()->getCashRegisterId();
	}
}
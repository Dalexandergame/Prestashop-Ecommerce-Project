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

require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CashRegisterType.php';
require_once 'Customweb/Twint/StubBuilder/Abstract.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/EnrollCashRegisterRequestElement.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/Token50Type.php';


/**
 * Stub builder used to create a cash register for the merchant.
 *
 * @author Sebastian Bossert
 */
class Customweb_Twint_StubBuilder_EnrollCashRegister extends Customweb_Twint_StubBuilder_Abstract {
	private $cashRegisterId;

	/**
	 * 
	 * @param Customweb_DependencyInjection_IContainer $container
	 * @param string $cashRegisterId The cash register id which should be enrolled
	 */
	public function __construct(Customweb_DependencyInjection_IContainer $container, $cashRegisterId){
		parent::__construct($container);
		$this->cashRegisterId = $cashRegisterId;
	}

	/**
	 * Creates the stub used in enrollCashRegister requests.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_EnrollCashRegisterRequestType
	 */
	public function build(){
		//@formatter:off
		$stub = Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_EnrollCashRegisterRequestElement::_()
						->setMerchantInformation($this->getMerchantInformation())
						->setFormerCashRegisterId(Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type::_())
						->setCashRegisterType(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CashRegisterType::EPOS());
		//@formatter:on
		return $stub;
	}

	protected function getCashRegisterId(){
		return $this->cashRegisterId;
	}
}
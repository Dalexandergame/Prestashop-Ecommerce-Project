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

require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/ConfirmOrderRequestElement.php';
require_once 'Customweb/Util/Currency.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/Token3Type.php';
require_once 'Customweb/Twint/StubBuilder/Abstract.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/UuidType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/PositiveDecimal.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/CurrencyAmountType.php';


/**
 * Stub builder to create confirmOrder (capture) reqeuests.
 *
 * @author Sebastian Bossert
 */
class Customweb_Twint_StubBuilder_ConfirmOrder extends Customweb_Twint_StubBuilder_Abstract {
	private $transaction;
	private $amount;
	private $isPartial;

	public function __construct(Customweb_DependencyInjection_IContainer $container, Customweb_Twint_Authorization_Transaction $transaction, $amount, $isPartial = false){
		parent::__construct($container);
		$this->transaction = $transaction;
		$this->amount = $amount;
		$this->isPartial = $isPartial;
	}

	public function build(){
		//@formatter:off
		$stub = Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ConfirmOrderRequestElement::_()
						->setMerchantInformation($this->getMerchantInformation())
						->setPartialConfirmation($this->isPartial)
						->setRequestedAmount($this->getRequestedAmount())
						->setOrderUUID(Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType::_()->set($this->transaction->getPaymentId()));
// 		@formatter:on
		return $stub;
	}

	protected final function getRequestedAmount(){
		// @formatter:off
		return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType::_()
			->setCurrency(
				Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token3Type::_()->set($this->transaction->getCurrencyCode()))
			->setAmount(
				Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_PositiveDecimal::_()
					->set(Customweb_Util_Currency::roundAmount($this->amount, $this->transaction->getCurrencyCode())));
		// @formatter:on
	}
}

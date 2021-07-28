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

require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/OrderRequestType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/PostingType.php';
require_once 'Customweb/Util/Currency.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/MerchantTransactionReferenceType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/Token3Type.php';
require_once 'Customweb/Twint/StubBuilder/Abstract.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/PositiveDecimal.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/CurrencyAmountType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/StartOrderRequestElement.php';
require_once 'Customweb/Payment/Util.php';


/**
 *
 * @author Sebastian Bossert
 */
abstract class Customweb_Twint_StubBuilder_AbstractStartOrder extends Customweb_Twint_StubBuilder_Abstract {
	private $transaction;
	private $orderType;
	private $amount;

	/**
	 * Creates the base for all startOrder requests.
	 *
	 * @param Customweb_Twint_Authorization_Transaction $transaction
	 * @param Customweb_DependencyInjection_IContainer $container
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderKindType $orderType
	 * @param double $amount
	 */
	public function __construct(Customweb_Twint_Authorization_Transaction $transaction, Customweb_DependencyInjection_IContainer $container, Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderKindType $orderType, $amount){
		parent::__construct($container);
		$this->transaction = $transaction;
		$this->amount = $amount;
		$this->orderType = $orderType;
	}

	/**
	 * Builds the stub used in startOrder requests
	 *
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderRequestType
	 */
	public function build(){
		//@formatter:off
		$startOrder = Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderRequestElement::_()
						->setMerchantInformation($this->getMerchantInformation())
						->setOrder($this->getOrder());
		//@formatter:on
		$startOrder = $this->addQrCodeRendering($startOrder);
		$startOrder->setUnidentifiedCustomer(true);
		
		return $startOrder;
	}

	/**
	 * Adds a link transaction to the order.
	 * Can be overwritten if a transaction must be added
	 *
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderRequestType $order
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderRequestType
	 */
	protected function addLink(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderRequestType $order){
		return $order;
	}

	/**
	 * Adds the QR-Code-Rendering element to the order.
	 * Can be overwritten if it should be set to true.
	 *
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderRequestElement $startOrder
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderRequestType
	 */
	protected function addQrCodeRendering(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderRequestElement $startOrder){
		return $startOrder;
	}

	/**
	 * Crates the order stub.
	 *
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderRequestType
	 */
	protected final function getOrder(){
		$order = Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderRequestType::_();
		//@formatter:off
		$order
			->setMerchantTransactionReference($this->getMerchantTransactionReference())
			->setPostingType(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PostingType::GOODS())
			->setRequestedAmount($this->getRequestedAmount())
			->setConfirmationNeeded($this->isConfirmationNeeded())
			->setType($this->getOrderType());
		//@formatter:on
		$order = $this->addLink($order);
		return $order;
	}

	protected function isConfirmationNeeded(){
		return $this->getContainer()->getPaymentMethodByTransaction($this->getTransaction())->isDeferredCapturingActive();
	}

	/**
	 * Gets the merchant transaction reference.
	 *
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_String
	 */
	protected final function getMerchantTransactionReference(){
		return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantTransactionReferenceType::_()->set(
				Customweb_Payment_Util::applyOrderSchemaImproved($this->getContainer()->getConfiguration()->getOrderSchema(),
						$this->getTransaction()->getExternalTransactionId(), 50));
	}

	/**
	 * Gets the order type to be used.
	 *
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderKindType
	 */
	protected final function getOrderType(){
		return $this->orderType;
	}

	/**
	 * Sets the order type to be used.
	 *
	 * @param unknown Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderKindType
	 */
	protected final function setOrderType($orderType){
		$this->orderType = $orderType;
	}

	/**
	 * Returns the amount to be used
	 *
	 * @return double
	 */
	protected final function getRequestedAmount(){
		// @formatter:off
		return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType::_()
			->setCurrency(
				Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token3Type::_()->set($this->getTransaction()->getCurrencyCode()))
			->setAmount(
				Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_PositiveDecimal::_()
					->set(Customweb_Util_Currency::roundAmount($this->amount, $this->getTransaction()->getCurrencyCode())));
		// @formatter:on
	}

	/**
	 * Returns the transaction used for the TWINT order
	 *
	 * @return Customweb_Twint_Authorization_Transaction
	 */
	protected final function getTransaction(){
		return $this->transaction;
	}
}

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

require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/MerchantInformationType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/UuidType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/MerchantTransactionReferenceType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/CurrencyAmountType.php';
require_once 'Customweb/Twint/Stubs/Org/W3/XMLSchema/Boolean.php';
/**
 * @XmlType(name="ConfirmOrderRequestType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ConfirmOrderRequestType {
	/**
	 * @XmlElement(name="MerchantInformation", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType
	 */
	private $merchantInformation;
	
	/**
	 * @XmlElement(name="OrderUuid", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	private $orderUuid;
	
	/**
	 * @XmlElement(name="MerchantTransactionReference", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantTransactionReferenceType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantTransactionReferenceType
	 */
	private $merchantTransactionReference;
	
	/**
	 * @XmlElement(name="RequestedAmount", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType
	 */
	private $requestedAmount;
	
	/**
	 * @XmlValue(name="PartialConfirmation", simpleType=@XmlSimpleTypeDefinition(typeName='boolean', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_Boolean'), namespace="http://service.twint.ch/merchant/types/v2")
	 * @var boolean
	 */
	private $partialConfirmation;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ConfirmOrderRequestType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ConfirmOrderRequestType();
		return $i;
	}
	/**
	 * Returns the value for the property merchantInformation.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType
	 */
	public function getMerchantInformation(){
		return $this->merchantInformation;
	}
	
	/**
	 * Sets the value for the property merchantInformation.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType $merchantInformation
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ConfirmOrderRequestType
	 */
	public function setMerchantInformation($merchantInformation){
		if ($merchantInformation instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType) {
			$this->merchantInformation = $merchantInformation;
		}
		else {
			throw new BadMethodCallException("Type of argument merchantInformation must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property orderUuid.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	public function getOrderUuid(){
		return $this->orderUuid;
	}
	
	/**
	 * Sets the value for the property orderUuid.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType $orderUuid
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ConfirmOrderRequestType
	 */
	public function setOrderUuid($orderUuid){
		if ($orderUuid instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType) {
			$this->orderUuid = $orderUuid;
		}
		else {
			throw new BadMethodCallException("Type of argument orderUuid must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property merchantTransactionReference.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantTransactionReferenceType
	 */
	public function getMerchantTransactionReference(){
		return $this->merchantTransactionReference;
	}
	
	/**
	 * Sets the value for the property merchantTransactionReference.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantTransactionReferenceType $merchantTransactionReference
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ConfirmOrderRequestType
	 */
	public function setMerchantTransactionReference($merchantTransactionReference){
		if ($merchantTransactionReference instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantTransactionReferenceType) {
			$this->merchantTransactionReference = $merchantTransactionReference;
		}
		else {
			throw new BadMethodCallException("Type of argument merchantTransactionReference must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantTransactionReferenceType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property requestedAmount.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType
	 */
	public function getRequestedAmount(){
		return $this->requestedAmount;
	}
	
	/**
	 * Sets the value for the property requestedAmount.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType $requestedAmount
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ConfirmOrderRequestType
	 */
	public function setRequestedAmount($requestedAmount){
		if ($requestedAmount instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType) {
			$this->requestedAmount = $requestedAmount;
		}
		else {
			throw new BadMethodCallException("Type of argument requestedAmount must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property partialConfirmation.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_Boolean
	 */
	public function getPartialConfirmation(){
		return $this->partialConfirmation;
	}
	
	/**
	 * Sets the value for the property partialConfirmation.
	 * 
	 * @param boolean $partialConfirmation
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ConfirmOrderRequestType
	 */
	public function setPartialConfirmation($partialConfirmation){
		if ($partialConfirmation instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_Boolean) {
			$this->partialConfirmation = $partialConfirmation;
		}
		else {
			$this->partialConfirmation = Customweb_Twint_Stubs_Org_W3_XMLSchema_Boolean::_()->set($partialConfirmation);
		}
		return $this;
	}
	
	
	
}
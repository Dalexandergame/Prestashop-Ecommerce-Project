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

require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/PostingType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/CurrencyAmountType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/MerchantTransactionReferenceType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/Token250Type.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/OrderLinkType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/OrderKindType.php';
require_once 'Customweb/Twint/Stubs/Org/W3/XMLSchema/Boolean.php';
/**
 * @XmlType(name="OrderRequestType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderRequestType {
	/**
	 * @XmlElement(name="PostingType", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PostingType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PostingType
	 */
	private $postingType;
	
	/**
	 * @XmlElement(name="RequestedAmount", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType
	 */
	private $requestedAmount;
	
	/**
	 * @XmlElement(name="MerchantTransactionReference", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantTransactionReferenceType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantTransactionReferenceType
	 */
	private $merchantTransactionReference;
	
	/**
	 * @XmlElement(name="CustomerBenefit", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType
	 */
	private $customerBenefit;
	
	/**
	 * @XmlElement(name="EReceiptUrl", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token250Type", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token250Type
	 */
	private $eReceiptUrl;
	
	/**
	 * @XmlElement(name="Link", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderLinkType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderLinkType
	 */
	private $link;
	
	/**
	 * @XmlAttribute(name="type", simpleType=@XmlSimpleTypeDefinition(typeName='OrderKindType', typeNamespace='http://service.twint.ch/merchant/types/v2', type='Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderKindType')) 
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderKindType
	 */
	private $type;
	
	/**
	 * @XmlAttribute(name="confirmationNeeded", simpleType=@XmlSimpleTypeDefinition(typeName='boolean', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_Boolean')) 
	 * @var boolean
	 */
	private $confirmationNeeded = 'false';
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderRequestType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderRequestType();
		return $i;
	}
	/**
	 * Returns the value for the property postingType.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PostingType
	 */
	public function getPostingType(){
		return $this->postingType;
	}
	
	/**
	 * Sets the value for the property postingType.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PostingType $postingType
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderRequestType
	 */
	public function setPostingType($postingType){
		if ($postingType instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PostingType) {
			$this->postingType = $postingType;
		}
		else {
			throw new BadMethodCallException("Type of argument postingType must be Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PostingType.");
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderRequestType
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderRequestType
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
	 * Returns the value for the property customerBenefit.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType
	 */
	public function getCustomerBenefit(){
		return $this->customerBenefit;
	}
	
	/**
	 * Sets the value for the property customerBenefit.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType $customerBenefit
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderRequestType
	 */
	public function setCustomerBenefit($customerBenefit){
		if ($customerBenefit instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType) {
			$this->customerBenefit = $customerBenefit;
		}
		else {
			throw new BadMethodCallException("Type of argument customerBenefit must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property eReceiptUrl.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token250Type
	 */
	public function getEReceiptUrl(){
		return $this->eReceiptUrl;
	}
	
	/**
	 * Sets the value for the property eReceiptUrl.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token250Type $eReceiptUrl
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderRequestType
	 */
	public function setEReceiptUrl($eReceiptUrl){
		if ($eReceiptUrl instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token250Type) {
			$this->eReceiptUrl = $eReceiptUrl;
		}
		else {
			throw new BadMethodCallException("Type of argument eReceiptUrl must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token250Type.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property link.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderLinkType
	 */
	public function getLink(){
		return $this->link;
	}
	
	/**
	 * Sets the value for the property link.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderLinkType $link
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderRequestType
	 */
	public function setLink($link){
		if ($link instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderLinkType) {
			$this->link = $link;
		}
		else {
			throw new BadMethodCallException("Type of argument link must be Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderLinkType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property type.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderKindType
	 */
	public function getType(){
		return $this->type;
	}
	
	/**
	 * Sets the value for the property type.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderKindType $type
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderRequestType
	 */
	public function setType($type){
		if ($type instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderKindType) {
			$this->type = $type;
		}
		else {
			throw new BadMethodCallException("Type of argument type must be Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderKindType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property confirmationNeeded.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_Boolean
	 */
	public function getConfirmationNeeded(){
		return $this->confirmationNeeded;
	}
	
	/**
	 * Sets the value for the property confirmationNeeded.
	 * 
	 * @param boolean $confirmationNeeded
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderRequestType
	 */
	public function setConfirmationNeeded($confirmationNeeded){
		if ($confirmationNeeded instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_Boolean) {
			$this->confirmationNeeded = $confirmationNeeded;
		}
		else {
			$this->confirmationNeeded = Customweb_Twint_Stubs_Org_W3_XMLSchema_Boolean::_()->set($confirmationNeeded);
		}
		return $this;
	}
	
	
	
}
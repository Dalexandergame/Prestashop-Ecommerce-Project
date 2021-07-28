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

require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/UuidType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/OrderStatusType.php';
require_once 'Customweb/Twint/Stubs/Org/W3/XMLSchema/DateTime.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/CurrencyAmountType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/PaymentAmountType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/OrderRequestType.php';
/**
 * @XmlType(name="OrderType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderType extends Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderRequestType {
	/**
	 * @XmlElement(name="Uuid", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	private $uuid;
	
	/**
	 * @XmlElement(name="Status", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderStatusType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderStatusType
	 */
	private $status;
	
	/**
	 * @XmlValue(name="CreationTimestamp", simpleType=@XmlSimpleTypeDefinition(typeName='dateTime', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_DateTime'), namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Xml_Binding_DateHandler_DateTime
	 */
	private $creationTimestamp;
	
	/**
	 * @XmlElement(name="AuthorizedAmount", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType
	 */
	private $authorizedAmount;
	
	/**
	 * @XmlElement(name="Fee", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType
	 */
	private $fee;
	
	/**
	 * @XmlValue(name="ProcessingTimestamp", simpleType=@XmlSimpleTypeDefinition(typeName='dateTime', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_DateTime'), namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Xml_Binding_DateHandler_DateTime
	 */
	private $processingTimestamp;
	
	/**
	 * @XmlList(name="PaymentAmount", type='Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PaymentAmountType', namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PaymentAmountType[]
	 */
	private $paymentAmount;
	
	public function __construct() {
		parent::__construct();
		$this->paymentAmount = new ArrayObject();
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderType();
		return $i;
	}
	/**
	 * Returns the value for the property uuid.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	public function getUuid(){
		return $this->uuid;
	}
	
	/**
	 * Sets the value for the property uuid.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType $uuid
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderType
	 */
	public function setUuid($uuid){
		if ($uuid instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType) {
			$this->uuid = $uuid;
		}
		else {
			throw new BadMethodCallException("Type of argument uuid must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property status.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderStatusType
	 */
	public function getStatus(){
		return $this->status;
	}
	
	/**
	 * Sets the value for the property status.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderStatusType $status
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderType
	 */
	public function setStatus($status){
		if ($status instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderStatusType) {
			$this->status = $status;
		}
		else {
			throw new BadMethodCallException("Type of argument status must be Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderStatusType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property creationTimestamp.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_DateTime
	 */
	public function getCreationTimestamp(){
		return $this->creationTimestamp;
	}
	
	/**
	 * Sets the value for the property creationTimestamp.
	 * 
	 * @param Customweb_Xml_Binding_DateHandler_DateTime $creationTimestamp
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderType
	 */
	public function setCreationTimestamp($creationTimestamp){
		if ($creationTimestamp instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_DateTime) {
			$this->creationTimestamp = $creationTimestamp;
		}
		else {
			$this->creationTimestamp = Customweb_Twint_Stubs_Org_W3_XMLSchema_DateTime::_()->set($creationTimestamp);
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property authorizedAmount.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType
	 */
	public function getAuthorizedAmount(){
		return $this->authorizedAmount;
	}
	
	/**
	 * Sets the value for the property authorizedAmount.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType $authorizedAmount
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderType
	 */
	public function setAuthorizedAmount($authorizedAmount){
		if ($authorizedAmount instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType) {
			$this->authorizedAmount = $authorizedAmount;
		}
		else {
			throw new BadMethodCallException("Type of argument authorizedAmount must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property fee.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType
	 */
	public function getFee(){
		return $this->fee;
	}
	
	/**
	 * Sets the value for the property fee.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType $fee
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderType
	 */
	public function setFee($fee){
		if ($fee instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType) {
			$this->fee = $fee;
		}
		else {
			throw new BadMethodCallException("Type of argument fee must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property processingTimestamp.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_DateTime
	 */
	public function getProcessingTimestamp(){
		return $this->processingTimestamp;
	}
	
	/**
	 * Sets the value for the property processingTimestamp.
	 * 
	 * @param Customweb_Xml_Binding_DateHandler_DateTime $processingTimestamp
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderType
	 */
	public function setProcessingTimestamp($processingTimestamp){
		if ($processingTimestamp instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_DateTime) {
			$this->processingTimestamp = $processingTimestamp;
		}
		else {
			$this->processingTimestamp = Customweb_Twint_Stubs_Org_W3_XMLSchema_DateTime::_()->set($processingTimestamp);
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property paymentAmount.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PaymentAmountType[]
	 */
	public function getPaymentAmount(){
		return $this->paymentAmount;
	}
	
	/**
	 * Sets the value for the property paymentAmount.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PaymentAmountType $paymentAmount
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderType
	 */
	public function setPaymentAmount($paymentAmount){
		if (is_array($paymentAmount)) {
			$paymentAmount = new ArrayObject($paymentAmount);
		}
		if ($paymentAmount instanceof ArrayObject) {
			$this->paymentAmount = $paymentAmount;
		}
		else {
			throw new BadMethodCallException("Type of argument paymentAmount must be ArrayObject.");
		}
		return $this;
	}
	
	/**
	 * Adds the given $item to the list of items of paymentAmount.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PaymentAmountType $item
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderType
	 */
	public function addPaymentAmount(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PaymentAmountType $item) {
		if (!($this->paymentAmount instanceof ArrayObject)) {
			$this->paymentAmount = new ArrayObject();
		}
		$this->paymentAmount[] = $item;
		return $this;
	}
	
	
}
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

require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/Token50Type.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/UuidType.php';
require_once 'Customweb/Twint/Stubs/Org/W3/XMLSchema/String.php';
/**
 * @XmlType(name="ExpressMerchantAuthorizationType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ExpressMerchantAuthorizationType {
	/**
	 * @XmlElement(name="TerminalId", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type
	 */
	private $terminalId;
	
	/**
	 * @XmlElement(name="SequenceCounter", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type
	 */
	private $sequenceCounter;
	
	/**
	 * @XmlElement(name="CustomerUuid", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	private $customerUuid;
	
	/**
	 * @XmlValue(name="Operation", simpleType=@XmlSimpleTypeDefinition(typeName='string', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_String'), namespace="http://service.twint.ch/merchant/types/v2")
	 * @var string
	 */
	private $operation;
	
	/**
	 * @XmlValue(name="ReservationTimestamp", simpleType=@XmlSimpleTypeDefinition(typeName='string', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_String'), namespace="http://service.twint.ch/merchant/types/v2")
	 * @var string
	 */
	private $reservationTimestamp;
	
	/**
	 * @XmlElement(name="OrderUuid", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	private $orderUuid;
	
	/**
	 * @XmlValue(name="RequestSignature", simpleType=@XmlSimpleTypeDefinition(typeName='base64Binary', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_String'), namespace="http://service.twint.ch/merchant/types/v2")
	 * @var string
	 */
	private $requestSignature;
	
	/**
	 * @XmlValue(name="RequestKey", simpleType=@XmlSimpleTypeDefinition(typeName='base64Binary', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_String'), namespace="http://service.twint.ch/merchant/types/v2")
	 * @var string
	 */
	private $requestKey;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ExpressMerchantAuthorizationType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ExpressMerchantAuthorizationType();
		return $i;
	}
	/**
	 * Returns the value for the property terminalId.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type
	 */
	public function getTerminalId(){
		return $this->terminalId;
	}
	
	/**
	 * Sets the value for the property terminalId.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type $terminalId
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ExpressMerchantAuthorizationType
	 */
	public function setTerminalId($terminalId){
		if ($terminalId instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type) {
			$this->terminalId = $terminalId;
		}
		else {
			throw new BadMethodCallException("Type of argument terminalId must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property sequenceCounter.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type
	 */
	public function getSequenceCounter(){
		return $this->sequenceCounter;
	}
	
	/**
	 * Sets the value for the property sequenceCounter.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type $sequenceCounter
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ExpressMerchantAuthorizationType
	 */
	public function setSequenceCounter($sequenceCounter){
		if ($sequenceCounter instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type) {
			$this->sequenceCounter = $sequenceCounter;
		}
		else {
			throw new BadMethodCallException("Type of argument sequenceCounter must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property customerUuid.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	public function getCustomerUuid(){
		return $this->customerUuid;
	}
	
	/**
	 * Sets the value for the property customerUuid.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType $customerUuid
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ExpressMerchantAuthorizationType
	 */
	public function setCustomerUuid($customerUuid){
		if ($customerUuid instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType) {
			$this->customerUuid = $customerUuid;
		}
		else {
			throw new BadMethodCallException("Type of argument customerUuid must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property operation.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_String
	 */
	public function getOperation(){
		return $this->operation;
	}
	
	/**
	 * Sets the value for the property operation.
	 * 
	 * @param string $operation
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ExpressMerchantAuthorizationType
	 */
	public function setOperation($operation){
		if ($operation instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_String) {
			$this->operation = $operation;
		}
		else {
			$this->operation = Customweb_Twint_Stubs_Org_W3_XMLSchema_String::_()->set($operation);
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property reservationTimestamp.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_String
	 */
	public function getReservationTimestamp(){
		return $this->reservationTimestamp;
	}
	
	/**
	 * Sets the value for the property reservationTimestamp.
	 * 
	 * @param string $reservationTimestamp
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ExpressMerchantAuthorizationType
	 */
	public function setReservationTimestamp($reservationTimestamp){
		if ($reservationTimestamp instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_String) {
			$this->reservationTimestamp = $reservationTimestamp;
		}
		else {
			$this->reservationTimestamp = Customweb_Twint_Stubs_Org_W3_XMLSchema_String::_()->set($reservationTimestamp);
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ExpressMerchantAuthorizationType
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
	 * Returns the value for the property requestSignature.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_String
	 */
	public function getRequestSignature(){
		return $this->requestSignature;
	}
	
	/**
	 * Sets the value for the property requestSignature.
	 * 
	 * @param string $requestSignature
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ExpressMerchantAuthorizationType
	 */
	public function setRequestSignature($requestSignature){
		if ($requestSignature instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_String) {
			$this->requestSignature = $requestSignature;
		}
		else {
			$this->requestSignature = Customweb_Twint_Stubs_Org_W3_XMLSchema_String::_()->set($requestSignature);
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property requestKey.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_String
	 */
	public function getRequestKey(){
		return $this->requestKey;
	}
	
	/**
	 * Sets the value for the property requestKey.
	 * 
	 * @param string $requestKey
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ExpressMerchantAuthorizationType
	 */
	public function setRequestKey($requestKey){
		if ($requestKey instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_String) {
			$this->requestKey = $requestKey;
		}
		else {
			$this->requestKey = Customweb_Twint_Stubs_Org_W3_XMLSchema_String::_()->set($requestKey);
		}
		return $this;
	}
	
	
	
}
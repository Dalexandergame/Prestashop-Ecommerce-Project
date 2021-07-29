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
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/Token50Type.php';
require_once 'Customweb/Twint/Stubs/Org/W3/XMLSchema/DateTime.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/MerchantTransactionReferenceType.php';
/**
 * @XmlType(name="FindOrderRequestType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_FindOrderRequestType {
	/**
	 * @XmlElement(name="MerchantUuid", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	private $merchantUuid;
	
	/**
	 * @XmlElement(name="MerchantAliasId", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type
	 */
	private $merchantAliasId;
	
	/**
	 * @XmlElement(name="CashRegisterId", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type
	 */
	private $cashRegisterId;
	
	/**
	 * @XmlValue(name="SearchStartDate", simpleType=@XmlSimpleTypeDefinition(typeName='dateTime', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_DateTime'), namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Xml_Binding_DateHandler_DateTime
	 */
	private $searchStartDate;
	
	/**
	 * @XmlValue(name="SearchEndDate", simpleType=@XmlSimpleTypeDefinition(typeName='dateTime', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_DateTime'), namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Xml_Binding_DateHandler_DateTime
	 */
	private $searchEndDate;
	
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
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_FindOrderRequestType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_FindOrderRequestType();
		return $i;
	}
	/**
	 * Returns the value for the property merchantUuid.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	public function getMerchantUuid(){
		return $this->merchantUuid;
	}
	
	/**
	 * Sets the value for the property merchantUuid.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType $merchantUuid
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_FindOrderRequestType
	 */
	public function setMerchantUuid($merchantUuid){
		if ($merchantUuid instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType) {
			$this->merchantUuid = $merchantUuid;
		}
		else {
			throw new BadMethodCallException("Type of argument merchantUuid must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property merchantAliasId.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type
	 */
	public function getMerchantAliasId(){
		return $this->merchantAliasId;
	}
	
	/**
	 * Sets the value for the property merchantAliasId.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type $merchantAliasId
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_FindOrderRequestType
	 */
	public function setMerchantAliasId($merchantAliasId){
		if ($merchantAliasId instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type) {
			$this->merchantAliasId = $merchantAliasId;
		}
		else {
			throw new BadMethodCallException("Type of argument merchantAliasId must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property cashRegisterId.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type
	 */
	public function getCashRegisterId(){
		return $this->cashRegisterId;
	}
	
	/**
	 * Sets the value for the property cashRegisterId.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type $cashRegisterId
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_FindOrderRequestType
	 */
	public function setCashRegisterId($cashRegisterId){
		if ($cashRegisterId instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type) {
			$this->cashRegisterId = $cashRegisterId;
		}
		else {
			throw new BadMethodCallException("Type of argument cashRegisterId must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property searchStartDate.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_DateTime
	 */
	public function getSearchStartDate(){
		return $this->searchStartDate;
	}
	
	/**
	 * Sets the value for the property searchStartDate.
	 * 
	 * @param Customweb_Xml_Binding_DateHandler_DateTime $searchStartDate
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_FindOrderRequestType
	 */
	public function setSearchStartDate($searchStartDate){
		if ($searchStartDate instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_DateTime) {
			$this->searchStartDate = $searchStartDate;
		}
		else {
			$this->searchStartDate = Customweb_Twint_Stubs_Org_W3_XMLSchema_DateTime::_()->set($searchStartDate);
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property searchEndDate.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_DateTime
	 */
	public function getSearchEndDate(){
		return $this->searchEndDate;
	}
	
	/**
	 * Sets the value for the property searchEndDate.
	 * 
	 * @param Customweb_Xml_Binding_DateHandler_DateTime $searchEndDate
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_FindOrderRequestType
	 */
	public function setSearchEndDate($searchEndDate){
		if ($searchEndDate instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_DateTime) {
			$this->searchEndDate = $searchEndDate;
		}
		else {
			$this->searchEndDate = Customweb_Twint_Stubs_Org_W3_XMLSchema_DateTime::_()->set($searchEndDate);
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_FindOrderRequestType
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_FindOrderRequestType
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
	
	
	
}
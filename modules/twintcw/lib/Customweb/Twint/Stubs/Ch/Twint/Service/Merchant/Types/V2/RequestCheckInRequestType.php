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
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/Token3000Type.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/Token50Type.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/UuidType.php';
require_once 'Customweb/Twint/Stubs/Org/W3/XMLSchema/Boolean.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/LoyaltyType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/RequestCustomerAliasType.php';
/**
 * @XmlType(name="RequestCheckInRequestType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCheckInRequestType {
	/**
	 * @XmlElement(name="MerchantInformation", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType
	 */
	private $merchantInformation;
	
	/**
	 * @XmlElement(name="OfflineAuthorization", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token3000Type", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token3000Type
	 */
	private $offlineAuthorization;
	
	/**
	 * @XmlElement(name="CouponCode", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type
	 */
	private $couponCode;
	
	/**
	 * @XmlElement(name="CustomerRelationUuid", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	private $customerRelationUuid;
	
	/**
	 * @XmlValue(name="UnidentifiedCustomer", simpleType=@XmlSimpleTypeDefinition(typeName='boolean', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_Boolean'), namespace="http://service.twint.ch/merchant/types/v2")
	 * @var boolean
	 */
	private $unidentifiedCustomer;
	
	/**
	 * @XmlElement(name="LoyaltyInformation", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_LoyaltyType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_LoyaltyType
	 */
	private $loyaltyInformation;
	
	/**
	 * @XmlElement(name="RequestCustomerRelationAlias", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCustomerAliasType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCustomerAliasType
	 */
	private $requestCustomerRelationAlias;
	
	/**
	 * @XmlValue(name="QRCodeRendering", simpleType=@XmlSimpleTypeDefinition(typeName='boolean', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_Boolean'), namespace="http://service.twint.ch/merchant/types/v2")
	 * @var boolean
	 */
	private $qRCodeRendering;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCheckInRequestType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCheckInRequestType();
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCheckInRequestType
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
	 * Returns the value for the property offlineAuthorization.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token3000Type
	 */
	public function getOfflineAuthorization(){
		return $this->offlineAuthorization;
	}
	
	/**
	 * Sets the value for the property offlineAuthorization.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token3000Type $offlineAuthorization
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCheckInRequestType
	 */
	public function setOfflineAuthorization($offlineAuthorization){
		if ($offlineAuthorization instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token3000Type) {
			$this->offlineAuthorization = $offlineAuthorization;
		}
		else {
			throw new BadMethodCallException("Type of argument offlineAuthorization must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token3000Type.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property couponCode.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type
	 */
	public function getCouponCode(){
		return $this->couponCode;
	}
	
	/**
	 * Sets the value for the property couponCode.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type $couponCode
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCheckInRequestType
	 */
	public function setCouponCode($couponCode){
		if ($couponCode instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type) {
			$this->couponCode = $couponCode;
		}
		else {
			throw new BadMethodCallException("Type of argument couponCode must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property customerRelationUuid.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	public function getCustomerRelationUuid(){
		return $this->customerRelationUuid;
	}
	
	/**
	 * Sets the value for the property customerRelationUuid.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType $customerRelationUuid
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCheckInRequestType
	 */
	public function setCustomerRelationUuid($customerRelationUuid){
		if ($customerRelationUuid instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType) {
			$this->customerRelationUuid = $customerRelationUuid;
		}
		else {
			throw new BadMethodCallException("Type of argument customerRelationUuid must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property unidentifiedCustomer.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_Boolean
	 */
	public function getUnidentifiedCustomer(){
		return $this->unidentifiedCustomer;
	}
	
	/**
	 * Sets the value for the property unidentifiedCustomer.
	 * 
	 * @param boolean $unidentifiedCustomer
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCheckInRequestType
	 */
	public function setUnidentifiedCustomer($unidentifiedCustomer){
		if ($unidentifiedCustomer instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_Boolean) {
			$this->unidentifiedCustomer = $unidentifiedCustomer;
		}
		else {
			$this->unidentifiedCustomer = Customweb_Twint_Stubs_Org_W3_XMLSchema_Boolean::_()->set($unidentifiedCustomer);
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property loyaltyInformation.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_LoyaltyType
	 */
	public function getLoyaltyInformation(){
		return $this->loyaltyInformation;
	}
	
	/**
	 * Sets the value for the property loyaltyInformation.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_LoyaltyType $loyaltyInformation
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCheckInRequestType
	 */
	public function setLoyaltyInformation($loyaltyInformation){
		if ($loyaltyInformation instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_LoyaltyType) {
			$this->loyaltyInformation = $loyaltyInformation;
		}
		else {
			throw new BadMethodCallException("Type of argument loyaltyInformation must be Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_LoyaltyType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property requestCustomerRelationAlias.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCustomerAliasType
	 */
	public function getRequestCustomerRelationAlias(){
		return $this->requestCustomerRelationAlias;
	}
	
	/**
	 * Sets the value for the property requestCustomerRelationAlias.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCustomerAliasType $requestCustomerRelationAlias
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCheckInRequestType
	 */
	public function setRequestCustomerRelationAlias($requestCustomerRelationAlias){
		if ($requestCustomerRelationAlias instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCustomerAliasType) {
			$this->requestCustomerRelationAlias = $requestCustomerRelationAlias;
		}
		else {
			throw new BadMethodCallException("Type of argument requestCustomerRelationAlias must be Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCustomerAliasType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property qRCodeRendering.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_Boolean
	 */
	public function getQRCodeRendering(){
		return $this->qRCodeRendering;
	}
	
	/**
	 * Sets the value for the property qRCodeRendering.
	 * 
	 * @param boolean $qRCodeRendering
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCheckInRequestType
	 */
	public function setQRCodeRendering($qRCodeRendering){
		if ($qRCodeRendering instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_Boolean) {
			$this->qRCodeRendering = $qRCodeRendering;
		}
		else {
			$this->qRCodeRendering = Customweb_Twint_Stubs_Org_W3_XMLSchema_Boolean::_()->set($qRCodeRendering);
		}
		return $this;
	}
	
	
	
}
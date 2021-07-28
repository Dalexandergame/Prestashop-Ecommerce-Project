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
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/OrderRequestType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CouponListType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/Token3000Type.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/UuidType.php';
require_once 'Customweb/Twint/Stubs/Org/W3/XMLSchema/Boolean.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/ExpressMerchantAuthorizationType.php';
/**
 * @XmlType(name="StartOrderRequestType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderRequestType {
	/**
	 * @XmlElement(name="MerchantInformation", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType
	 */
	private $merchantInformation;
	
	/**
	 * @XmlElement(name="Order", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderRequestType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderRequestType
	 */
	private $order;
	
	/**
	 * @XmlElement(name="Coupons", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponListType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponListType
	 */
	private $coupons;
	
	/**
	 * @XmlElement(name="OfflineAuthorization", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token3000Type", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token3000Type
	 */
	private $offlineAuthorization;
	
	/**
	 * @XmlElement(name="CustomerRelationUuid", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	private $customerRelationUuid;
	
	/**
	 * @XmlElement(name="PairingUuid", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	private $pairingUuid;
	
	/**
	 * @XmlValue(name="UnidentifiedCustomer", simpleType=@XmlSimpleTypeDefinition(typeName='boolean', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_Boolean'), namespace="http://service.twint.ch/merchant/types/v2")
	 * @var boolean
	 */
	private $unidentifiedCustomer;
	
	/**
	 * @XmlElement(name="ExpressMerchantAuthorization", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ExpressMerchantAuthorizationType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ExpressMerchantAuthorizationType
	 */
	private $expressMerchantAuthorization;
	
	/**
	 * @XmlValue(name="QRCodeRendering", simpleType=@XmlSimpleTypeDefinition(typeName='boolean', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_Boolean'), namespace="http://service.twint.ch/merchant/types/v2")
	 * @var boolean
	 */
	private $qRCodeRendering;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderRequestType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderRequestType();
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderRequestType
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
	 * Returns the value for the property order.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderRequestType
	 */
	public function getOrder(){
		return $this->order;
	}
	
	/**
	 * Sets the value for the property order.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderRequestType $order
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderRequestType
	 */
	public function setOrder($order){
		if ($order instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderRequestType) {
			$this->order = $order;
		}
		else {
			throw new BadMethodCallException("Type of argument order must be Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderRequestType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property coupons.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponListType
	 */
	public function getCoupons(){
		return $this->coupons;
	}
	
	/**
	 * Sets the value for the property coupons.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponListType $coupons
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderRequestType
	 */
	public function setCoupons($coupons){
		if ($coupons instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponListType) {
			$this->coupons = $coupons;
		}
		else {
			throw new BadMethodCallException("Type of argument coupons must be Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponListType.");
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderRequestType
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderRequestType
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
	 * Returns the value for the property pairingUuid.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	public function getPairingUuid(){
		return $this->pairingUuid;
	}
	
	/**
	 * Sets the value for the property pairingUuid.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType $pairingUuid
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderRequestType
	 */
	public function setPairingUuid($pairingUuid){
		if ($pairingUuid instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType) {
			$this->pairingUuid = $pairingUuid;
		}
		else {
			throw new BadMethodCallException("Type of argument pairingUuid must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType.");
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderRequestType
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
	 * Returns the value for the property expressMerchantAuthorization.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ExpressMerchantAuthorizationType
	 */
	public function getExpressMerchantAuthorization(){
		return $this->expressMerchantAuthorization;
	}
	
	/**
	 * Sets the value for the property expressMerchantAuthorization.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ExpressMerchantAuthorizationType $expressMerchantAuthorization
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderRequestType
	 */
	public function setExpressMerchantAuthorization($expressMerchantAuthorization){
		if ($expressMerchantAuthorization instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ExpressMerchantAuthorizationType) {
			$this->expressMerchantAuthorization = $expressMerchantAuthorization;
		}
		else {
			throw new BadMethodCallException("Type of argument expressMerchantAuthorization must be Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ExpressMerchantAuthorizationType.");
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderRequestType
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
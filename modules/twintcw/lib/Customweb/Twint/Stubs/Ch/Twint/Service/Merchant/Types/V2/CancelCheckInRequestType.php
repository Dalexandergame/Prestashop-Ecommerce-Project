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
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CancelCheckinReason.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/UuidType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CouponListType.php';
/**
 * @XmlType(name="CancelCheckInRequestType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CancelCheckInRequestType {
	/**
	 * @XmlElement(name="MerchantInformation", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType
	 */
	private $merchantInformation;
	
	/**
	 * @XmlElement(name="Reason", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CancelCheckinReason", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CancelCheckinReason
	 */
	private $reason;
	
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
	 * @XmlElement(name="Coupons", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponListType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponListType
	 */
	private $coupons;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CancelCheckInRequestType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CancelCheckInRequestType();
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CancelCheckInRequestType
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
	 * Returns the value for the property reason.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CancelCheckinReason
	 */
	public function getReason(){
		return $this->reason;
	}
	
	/**
	 * Sets the value for the property reason.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CancelCheckinReason $reason
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CancelCheckInRequestType
	 */
	public function setReason($reason){
		if ($reason instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CancelCheckinReason) {
			$this->reason = $reason;
		}
		else {
			throw new BadMethodCallException("Type of argument reason must be Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CancelCheckinReason.");
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CancelCheckInRequestType
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CancelCheckInRequestType
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CancelCheckInRequestType
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
	
	
	
}
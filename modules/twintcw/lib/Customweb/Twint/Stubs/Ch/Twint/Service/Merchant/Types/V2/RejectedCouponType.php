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

require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/Token100Type.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CouponRejectionReason.php';
/**
 * @XmlType(name="RejectedCouponType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RejectedCouponType {
	/**
	 * @XmlElement(name="CouponId", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token100Type", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token100Type
	 */
	private $couponId;
	
	/**
	 * @XmlElement(name="RejectionReason", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponRejectionReason", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponRejectionReason
	 */
	private $rejectionReason;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RejectedCouponType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RejectedCouponType();
		return $i;
	}
	/**
	 * Returns the value for the property couponId.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token100Type
	 */
	public function getCouponId(){
		return $this->couponId;
	}
	
	/**
	 * Sets the value for the property couponId.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token100Type $couponId
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RejectedCouponType
	 */
	public function setCouponId($couponId){
		if ($couponId instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token100Type) {
			$this->couponId = $couponId;
		}
		else {
			throw new BadMethodCallException("Type of argument couponId must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token100Type.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property rejectionReason.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponRejectionReason
	 */
	public function getRejectionReason(){
		return $this->rejectionReason;
	}
	
	/**
	 * Sets the value for the property rejectionReason.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponRejectionReason $rejectionReason
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RejectedCouponType
	 */
	public function setRejectionReason($rejectionReason){
		if ($rejectionReason instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponRejectionReason) {
			$this->rejectionReason = $rejectionReason;
		}
		else {
			throw new BadMethodCallException("Type of argument rejectionReason must be Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponRejectionReason.");
		}
		return $this;
	}
	
	
	
}
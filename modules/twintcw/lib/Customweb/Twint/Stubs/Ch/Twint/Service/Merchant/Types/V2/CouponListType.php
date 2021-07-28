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

require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CouponType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/RejectedCouponType.php';
/**
 * @XmlType(name="CouponListType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponListType {
	/**
	 * @XmlList(name="ProcessedCoupon", type='Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponType', namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponType[]
	 */
	private $processedCoupon;
	
	/**
	 * @XmlList(name="RejectedCoupon", type='Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RejectedCouponType', namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RejectedCouponType[]
	 */
	private $rejectedCoupon;
	
	public function __construct() {
		$this->processedCoupon = new ArrayObject();
		$this->rejectedCoupon = new ArrayObject();
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponListType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponListType();
		return $i;
	}
	/**
	 * Returns the value for the property processedCoupon.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponType[]
	 */
	public function getProcessedCoupon(){
		return $this->processedCoupon;
	}
	
	/**
	 * Sets the value for the property processedCoupon.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponType $processedCoupon
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponListType
	 */
	public function setProcessedCoupon($processedCoupon){
		if (is_array($processedCoupon)) {
			$processedCoupon = new ArrayObject($processedCoupon);
		}
		if ($processedCoupon instanceof ArrayObject) {
			$this->processedCoupon = $processedCoupon;
		}
		else {
			throw new BadMethodCallException("Type of argument processedCoupon must be ArrayObject.");
		}
		return $this;
	}
	
	/**
	 * Adds the given $item to the list of items of processedCoupon.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponType $item
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponListType
	 */
	public function addProcessedCoupon(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponType $item) {
		if (!($this->processedCoupon instanceof ArrayObject)) {
			$this->processedCoupon = new ArrayObject();
		}
		$this->processedCoupon[] = $item;
		return $this;
	}
	
	/**
	 * Returns the value for the property rejectedCoupon.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RejectedCouponType[]
	 */
	public function getRejectedCoupon(){
		return $this->rejectedCoupon;
	}
	
	/**
	 * Sets the value for the property rejectedCoupon.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RejectedCouponType $rejectedCoupon
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponListType
	 */
	public function setRejectedCoupon($rejectedCoupon){
		if (is_array($rejectedCoupon)) {
			$rejectedCoupon = new ArrayObject($rejectedCoupon);
		}
		if ($rejectedCoupon instanceof ArrayObject) {
			$this->rejectedCoupon = $rejectedCoupon;
		}
		else {
			throw new BadMethodCallException("Type of argument rejectedCoupon must be ArrayObject.");
		}
		return $this;
	}
	
	/**
	 * Adds the given $item to the list of items of rejectedCoupon.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RejectedCouponType $item
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponListType
	 */
	public function addRejectedCoupon(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RejectedCouponType $item) {
		if (!($this->rejectedCoupon instanceof ArrayObject)) {
			$this->rejectedCoupon = new ArrayObject();
		}
		$this->rejectedCoupon[] = $item;
		return $this;
	}
	
	
}
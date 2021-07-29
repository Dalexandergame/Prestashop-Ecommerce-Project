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

require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/LoyaltyType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CouponType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/UuidType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/KeyValueType.php';
/**
 * @XmlType(name="CustomerInformationType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CustomerInformationType {
	/**
	 * @XmlList(name="Loyalty", type='Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_LoyaltyType', namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_LoyaltyType[]
	 */
	private $loyalty;
	
	/**
	 * @XmlList(name="Coupon", type='Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponType', namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponType[]
	 */
	private $coupon;
	
	/**
	 * @XmlElement(name="CustomerRelationUuid", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	private $customerRelationUuid;
	
	/**
	 * @XmlList(name="Addendum", type='Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_KeyValueType', namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_KeyValueType[]
	 */
	private $addendum;
	
	public function __construct() {
		$this->loyalty = new ArrayObject();
		$this->coupon = new ArrayObject();
		$this->addendum = new ArrayObject();
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CustomerInformationType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CustomerInformationType();
		return $i;
	}
	/**
	 * Returns the value for the property loyalty.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_LoyaltyType[]
	 */
	public function getLoyalty(){
		return $this->loyalty;
	}
	
	/**
	 * Sets the value for the property loyalty.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_LoyaltyType $loyalty
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CustomerInformationType
	 */
	public function setLoyalty($loyalty){
		if (is_array($loyalty)) {
			$loyalty = new ArrayObject($loyalty);
		}
		if ($loyalty instanceof ArrayObject) {
			$this->loyalty = $loyalty;
		}
		else {
			throw new BadMethodCallException("Type of argument loyalty must be ArrayObject.");
		}
		return $this;
	}
	
	/**
	 * Adds the given $item to the list of items of loyalty.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_LoyaltyType $item
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CustomerInformationType
	 */
	public function addLoyalty(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_LoyaltyType $item) {
		if (!($this->loyalty instanceof ArrayObject)) {
			$this->loyalty = new ArrayObject();
		}
		$this->loyalty[] = $item;
		return $this;
	}
	
	/**
	 * Returns the value for the property coupon.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponType[]
	 */
	public function getCoupon(){
		return $this->coupon;
	}
	
	/**
	 * Sets the value for the property coupon.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponType $coupon
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CustomerInformationType
	 */
	public function setCoupon($coupon){
		if (is_array($coupon)) {
			$coupon = new ArrayObject($coupon);
		}
		if ($coupon instanceof ArrayObject) {
			$this->coupon = $coupon;
		}
		else {
			throw new BadMethodCallException("Type of argument coupon must be ArrayObject.");
		}
		return $this;
	}
	
	/**
	 * Adds the given $item to the list of items of coupon.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponType $item
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CustomerInformationType
	 */
	public function addCoupon(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponType $item) {
		if (!($this->coupon instanceof ArrayObject)) {
			$this->coupon = new ArrayObject();
		}
		$this->coupon[] = $item;
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CustomerInformationType
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
	 * Returns the value for the property addendum.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_KeyValueType[]
	 */
	public function getAddendum(){
		return $this->addendum;
	}
	
	/**
	 * Sets the value for the property addendum.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_KeyValueType $addendum
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CustomerInformationType
	 */
	public function setAddendum($addendum){
		if (is_array($addendum)) {
			$addendum = new ArrayObject($addendum);
		}
		if ($addendum instanceof ArrayObject) {
			$this->addendum = $addendum;
		}
		else {
			throw new BadMethodCallException("Type of argument addendum must be ArrayObject.");
		}
		return $this;
	}
	
	/**
	 * Adds the given $item to the list of items of addendum.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_KeyValueType $item
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CustomerInformationType
	 */
	public function addAddendum(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_KeyValueType $item) {
		if (!($this->addendum instanceof ArrayObject)) {
			$this->addendum = new ArrayObject();
		}
		$this->addendum[] = $item;
		return $this;
	}
	
	
}
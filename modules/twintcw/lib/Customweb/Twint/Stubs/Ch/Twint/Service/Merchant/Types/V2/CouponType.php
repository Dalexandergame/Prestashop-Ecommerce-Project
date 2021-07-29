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
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/CurrencyAmountType.php';
/**
 * @XmlType(name="CouponType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponType {
	/**
	 * @XmlElement(name="CouponId", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token100Type", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token100Type
	 */
	private $couponId;
	
	/**
	 * @XmlElement(name="CouponValue", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType
	 */
	private $couponValue;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponType();
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponType
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
	 * Returns the value for the property couponValue.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType
	 */
	public function getCouponValue(){
		return $this->couponValue;
	}
	
	/**
	 * Sets the value for the property couponValue.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType $couponValue
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponType
	 */
	public function setCouponValue($couponValue){
		if ($couponValue instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType) {
			$this->couponValue = $couponValue;
		}
		else {
			throw new BadMethodCallException("Type of argument couponValue must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType.");
		}
		return $this;
	}
	
	
	
}
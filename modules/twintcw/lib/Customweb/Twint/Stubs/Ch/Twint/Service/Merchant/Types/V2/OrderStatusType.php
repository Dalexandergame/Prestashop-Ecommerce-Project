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

require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CodeValueType.php';
/**
 * @XmlType(name="OrderStatusType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderStatusType {
	/**
	 * @XmlElement(name="Status", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CodeValueType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CodeValueType
	 */
	private $status;
	
	/**
	 * @XmlElement(name="Reason", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CodeValueType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CodeValueType
	 */
	private $reason;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderStatusType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderStatusType();
		return $i;
	}
	/**
	 * Returns the value for the property status.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CodeValueType
	 */
	public function getStatus(){
		return $this->status;
	}
	
	/**
	 * Sets the value for the property status.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CodeValueType $status
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderStatusType
	 */
	public function setStatus($status){
		if ($status instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CodeValueType) {
			$this->status = $status;
		}
		else {
			throw new BadMethodCallException("Type of argument status must be Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CodeValueType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property reason.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CodeValueType
	 */
	public function getReason(){
		return $this->reason;
	}
	
	/**
	 * Sets the value for the property reason.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CodeValueType $reason
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderStatusType
	 */
	public function setReason($reason){
		if ($reason instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CodeValueType) {
			$this->reason = $reason;
		}
		else {
			throw new BadMethodCallException("Type of argument reason must be Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CodeValueType.");
		}
		return $this;
	}
	
	
	
}
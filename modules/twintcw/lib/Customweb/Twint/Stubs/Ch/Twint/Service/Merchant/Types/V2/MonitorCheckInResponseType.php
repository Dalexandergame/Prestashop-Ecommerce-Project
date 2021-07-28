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

require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CheckInNotificationType.php';
/**
 * @XmlType(name="MonitorCheckInResponseType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_MonitorCheckInResponseType {
	/**
	 * @XmlElement(name="CheckInNotification", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckInNotificationType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckInNotificationType
	 */
	private $checkInNotification;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_MonitorCheckInResponseType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_MonitorCheckInResponseType();
		return $i;
	}
	/**
	 * Returns the value for the property checkInNotification.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckInNotificationType
	 */
	public function getCheckInNotification(){
		return $this->checkInNotification;
	}
	
	/**
	 * Sets the value for the property checkInNotification.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckInNotificationType $checkInNotification
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_MonitorCheckInResponseType
	 */
	public function setCheckInNotification($checkInNotification){
		if ($checkInNotification instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckInNotificationType) {
			$this->checkInNotification = $checkInNotification;
		}
		else {
			throw new BadMethodCallException("Type of argument checkInNotification must be Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckInNotificationType.");
		}
		return $this;
	}
	
	
	
}
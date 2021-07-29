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
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/NumericTokenType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/DataUriScheme.php';
/**
 * @XmlType(name="RequestCheckInResponseType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCheckInResponseType {
	/**
	 * @XmlElement(name="CheckInNotification", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckInNotificationType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckInNotificationType
	 */
	private $checkInNotification;
	
	/**
	 * @XmlElement(name="Token", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_NumericTokenType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_NumericTokenType
	 */
	private $token;
	
	/**
	 * @XmlElement(name="QRCode", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_DataUriScheme", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_DataUriScheme
	 */
	private $qRCode;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCheckInResponseType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCheckInResponseType();
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCheckInResponseType
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
	
	
	/**
	 * Returns the value for the property token.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_NumericTokenType
	 */
	public function getToken(){
		return $this->token;
	}
	
	/**
	 * Sets the value for the property token.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_NumericTokenType $token
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCheckInResponseType
	 */
	public function setToken($token){
		if ($token instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_NumericTokenType) {
			$this->token = $token;
		}
		else {
			throw new BadMethodCallException("Type of argument token must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_NumericTokenType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property qRCode.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_DataUriScheme
	 */
	public function getQRCode(){
		return $this->qRCode;
	}
	
	/**
	 * Sets the value for the property qRCode.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_DataUriScheme $qRCode
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCheckInResponseType
	 */
	public function setQRCode($qRCode){
		if ($qRCode instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_DataUriScheme) {
			$this->qRCode = $qRCode;
		}
		else {
			throw new BadMethodCallException("Type of argument qRCode must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_DataUriScheme.");
		}
		return $this;
	}
	
	
	
}
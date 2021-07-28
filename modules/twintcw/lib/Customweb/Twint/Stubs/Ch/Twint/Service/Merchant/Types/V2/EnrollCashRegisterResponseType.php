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

require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/BeaconSecurityType.php';
/**
 * @XmlType(name="EnrollCashRegisterResponseType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_EnrollCashRegisterResponseType {
	/**
	 * @XmlElement(name="BeaconSecurity", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_BeaconSecurityType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_BeaconSecurityType
	 */
	private $beaconSecurity;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_EnrollCashRegisterResponseType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_EnrollCashRegisterResponseType();
		return $i;
	}
	/**
	 * Returns the value for the property beaconSecurity.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_BeaconSecurityType
	 */
	public function getBeaconSecurity(){
		return $this->beaconSecurity;
	}
	
	/**
	 * Sets the value for the property beaconSecurity.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_BeaconSecurityType $beaconSecurity
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_EnrollCashRegisterResponseType
	 */
	public function setBeaconSecurity($beaconSecurity){
		if ($beaconSecurity instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_BeaconSecurityType) {
			$this->beaconSecurity = $beaconSecurity;
		}
		else {
			throw new BadMethodCallException("Type of argument beaconSecurity must be Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_BeaconSecurityType.");
		}
		return $this;
	}
	
	
	
}
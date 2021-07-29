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

require_once 'Customweb/Twint/Stubs/Org/W3/XMLSchema/String.php';
/**
 * @XmlType(name="PairingStatusType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PairingStatusType extends Customweb_Twint_Stubs_Org_W3_XMLSchema_String {
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PairingStatusType
	 */
	public static function NO_PAIRING() {
		return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PairingStatusType::_()->set('NO_PAIRING');
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PairingStatusType
	 */
	public static function PAIRING_IN_PROGRESS() {
		return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PairingStatusType::_()->set('PAIRING_IN_PROGRESS');
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PairingStatusType
	 */
	public static function PAIRING_ACTIVE() {
		return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PairingStatusType::_()->set('PAIRING_ACTIVE');
	}
	
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PairingStatusType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PairingStatusType();
		return $i;
	}
	
}
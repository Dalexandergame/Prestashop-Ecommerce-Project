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
 * @XmlType(name="CashRegisterType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CashRegisterType extends Customweb_Twint_Stubs_Org_W3_XMLSchema_String {
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CashRegisterType
	 */
	public static function POSSERVICED() {
		return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CashRegisterType::_()->set('POS-Serviced');
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CashRegisterType
	 */
	public static function POSSELFSERVICE() {
		return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CashRegisterType::_()->set('POS-Selfservice');
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CashRegisterType
	 */
	public static function POSVENDINGMACHINE() {
		return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CashRegisterType::_()->set('POS-VendingMachine');
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CashRegisterType
	 */
	public static function EPOS() {
		return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CashRegisterType::_()->set('EPOS');
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CashRegisterType
	 */
	public static function MPOS() {
		return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CashRegisterType::_()->set('MPOS');
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CashRegisterType
	 */
	public static function OTHER() {
		return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CashRegisterType::_()->set('OTHER');
	}
	
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CashRegisterType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CashRegisterType();
		return $i;
	}
	
}
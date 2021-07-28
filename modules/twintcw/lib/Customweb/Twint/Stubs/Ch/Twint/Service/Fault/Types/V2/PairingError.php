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

require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/BaseFault.php';
/**
 * @XmlType(name="PairingError", namespace="http://service.twint.ch/fault/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Fault_Types_V2_PairingError extends Customweb_Twint_Stubs_Ch_Twint_Service_Fault_Types_V2_BaseFault {
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Fault_Types_V2_PairingError
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Fault_Types_V2_PairingError();
		return $i;
	}
	
}
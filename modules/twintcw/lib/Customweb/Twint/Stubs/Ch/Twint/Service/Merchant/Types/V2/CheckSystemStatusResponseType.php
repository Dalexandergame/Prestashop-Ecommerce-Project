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

require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/OperationResultType.php';
/**
 * @XmlType(name="CheckSystemStatusResponseType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckSystemStatusResponseType {
	/**
	 * @XmlElement(name="Status", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OperationResultType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OperationResultType
	 */
	private $status;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckSystemStatusResponseType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckSystemStatusResponseType();
		return $i;
	}
	/**
	 * Returns the value for the property status.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OperationResultType
	 */
	public function getStatus(){
		return $this->status;
	}
	
	/**
	 * Sets the value for the property status.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OperationResultType $status
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckSystemStatusResponseType
	 */
	public function setStatus($status){
		if ($status instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OperationResultType) {
			$this->status = $status;
		}
		else {
			throw new BadMethodCallException("Type of argument status must be Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OperationResultType.");
		}
		return $this;
	}
	
	
	
}
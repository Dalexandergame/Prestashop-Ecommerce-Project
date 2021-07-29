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

require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/RejectionReasonType.php';
require_once 'Customweb/Twint/Stubs/Org/W3/XMLSchema/String.php';
/**
 * @XmlType(name="CouponRejectionReason", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponRejectionReason {
	/**
	 * @XmlElement(name="RejectionReason", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RejectionReasonType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RejectionReasonType
	 */
	private $rejectionReason;
	
	/**
	 * @XmlValue(name="Details", simpleType=@XmlSimpleTypeDefinition(typeName='string', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_String'), namespace="http://service.twint.ch/merchant/types/v2")
	 * @var string
	 */
	private $details;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponRejectionReason
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponRejectionReason();
		return $i;
	}
	/**
	 * Returns the value for the property rejectionReason.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RejectionReasonType
	 */
	public function getRejectionReason(){
		return $this->rejectionReason;
	}
	
	/**
	 * Sets the value for the property rejectionReason.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RejectionReasonType $rejectionReason
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponRejectionReason
	 */
	public function setRejectionReason($rejectionReason){
		if ($rejectionReason instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RejectionReasonType) {
			$this->rejectionReason = $rejectionReason;
		}
		else {
			throw new BadMethodCallException("Type of argument rejectionReason must be Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RejectionReasonType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property details.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_String
	 */
	public function getDetails(){
		return $this->details;
	}
	
	/**
	 * Sets the value for the property details.
	 * 
	 * @param string $details
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CouponRejectionReason
	 */
	public function setDetails($details){
		if ($details instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_String) {
			$this->details = $details;
		}
		else {
			$this->details = Customweb_Twint_Stubs_Org_W3_XMLSchema_String::_()->set($details);
		}
		return $this;
	}
	
	
	
}
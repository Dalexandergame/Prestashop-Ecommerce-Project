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

require_once 'Customweb/Twint/Stubs/Org/W3/XMLSchema/Date.php';
require_once 'Customweb/Twint/Stubs/Org/W3/XMLSchema/Boolean.php';
/**
 * @XmlType(name="GetCertificateValidityResponseType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_GetCertificateValidityResponseType {
	/**
	 * @XmlValue(name="CertificateExpiryDate", simpleType=@XmlSimpleTypeDefinition(typeName='date', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_Date'), namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Xml_Binding_DateHandler_Date
	 */
	private $certificateExpiryDate;
	
	/**
	 * @XmlValue(name="RenewalAllowed", simpleType=@XmlSimpleTypeDefinition(typeName='boolean', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_Boolean'), namespace="http://service.twint.ch/merchant/types/v2")
	 * @var boolean
	 */
	private $renewalAllowed;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_GetCertificateValidityResponseType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_GetCertificateValidityResponseType();
		return $i;
	}
	/**
	 * Returns the value for the property certificateExpiryDate.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_Date
	 */
	public function getCertificateExpiryDate(){
		return $this->certificateExpiryDate;
	}
	
	/**
	 * Sets the value for the property certificateExpiryDate.
	 * 
	 * @param Customweb_Xml_Binding_DateHandler_Date $certificateExpiryDate
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_GetCertificateValidityResponseType
	 */
	public function setCertificateExpiryDate($certificateExpiryDate){
		if ($certificateExpiryDate instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_Date) {
			$this->certificateExpiryDate = $certificateExpiryDate;
		}
		else {
			$this->certificateExpiryDate = Customweb_Twint_Stubs_Org_W3_XMLSchema_Date::_()->set($certificateExpiryDate);
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property renewalAllowed.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_Boolean
	 */
	public function getRenewalAllowed(){
		return $this->renewalAllowed;
	}
	
	/**
	 * Sets the value for the property renewalAllowed.
	 * 
	 * @param boolean $renewalAllowed
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_GetCertificateValidityResponseType
	 */
	public function setRenewalAllowed($renewalAllowed){
		if ($renewalAllowed instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_Boolean) {
			$this->renewalAllowed = $renewalAllowed;
		}
		else {
			$this->renewalAllowed = Customweb_Twint_Stubs_Org_W3_XMLSchema_Boolean::_()->set($renewalAllowed);
		}
		return $this;
	}
	
	
	
}
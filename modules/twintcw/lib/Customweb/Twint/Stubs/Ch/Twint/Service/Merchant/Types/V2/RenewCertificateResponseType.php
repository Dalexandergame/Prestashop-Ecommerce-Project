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
require_once 'Customweb/Twint/Stubs/Org/W3/XMLSchema/DateTime.php';
/**
 * @XmlType(name="RenewCertificateResponseType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RenewCertificateResponseType {
	/**
	 * @XmlValue(name="MerchantCertificate", simpleType=@XmlSimpleTypeDefinition(typeName='base64Binary', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_String'), namespace="http://service.twint.ch/merchant/types/v2")
	 * @var string
	 */
	private $merchantCertificate;
	
	/**
	 * @XmlValue(name="ExpirationDate", simpleType=@XmlSimpleTypeDefinition(typeName='dateTime', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_DateTime'), namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Xml_Binding_DateHandler_DateTime
	 */
	private $expirationDate;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RenewCertificateResponseType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RenewCertificateResponseType();
		return $i;
	}
	/**
	 * Returns the value for the property merchantCertificate.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_String
	 */
	public function getMerchantCertificate(){
		return $this->merchantCertificate;
	}
	
	/**
	 * Sets the value for the property merchantCertificate.
	 * 
	 * @param string $merchantCertificate
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RenewCertificateResponseType
	 */
	public function setMerchantCertificate($merchantCertificate){
		if ($merchantCertificate instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_String) {
			$this->merchantCertificate = $merchantCertificate;
		}
		else {
			$this->merchantCertificate = Customweb_Twint_Stubs_Org_W3_XMLSchema_String::_()->set($merchantCertificate);
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property expirationDate.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_DateTime
	 */
	public function getExpirationDate(){
		return $this->expirationDate;
	}
	
	/**
	 * Sets the value for the property expirationDate.
	 * 
	 * @param Customweb_Xml_Binding_DateHandler_DateTime $expirationDate
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RenewCertificateResponseType
	 */
	public function setExpirationDate($expirationDate){
		if ($expirationDate instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_DateTime) {
			$this->expirationDate = $expirationDate;
		}
		else {
			$this->expirationDate = Customweb_Twint_Stubs_Org_W3_XMLSchema_DateTime::_()->set($expirationDate);
		}
		return $this;
	}
	
	
	
}
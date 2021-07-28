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

require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/UuidType.php';
require_once 'Customweb/Twint/Stubs/Org/W3/XMLSchema/Integer.php';
require_once 'Customweb/Twint/Stubs/Org/W3/XMLSchema/String.php';
/**
 * @XmlType(name="BeaconSecurityType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_BeaconSecurityType {
	/**
	 * @XmlElement(name="BeaconUuid", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	private $beaconUuid;
	
	/**
	 * @XmlValue(name="MajorId", simpleType=@XmlSimpleTypeDefinition(typeName='int', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_Integer'), namespace="http://service.twint.ch/merchant/types/v2")
	 * @var integer
	 */
	private $majorId;
	
	/**
	 * @XmlValue(name="MinorId", simpleType=@XmlSimpleTypeDefinition(typeName='int', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_Integer'), namespace="http://service.twint.ch/merchant/types/v2")
	 * @var integer
	 */
	private $minorId;
	
	/**
	 * @XmlValue(name="BeaconInitString", simpleType=@XmlSimpleTypeDefinition(typeName='string', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_String'), namespace="http://service.twint.ch/merchant/types/v2")
	 * @var string
	 */
	private $beaconInitString;
	
	/**
	 * @XmlValue(name="BeaconSecret", simpleType=@XmlSimpleTypeDefinition(typeName='string', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_String'), namespace="http://service.twint.ch/merchant/types/v2")
	 * @var string
	 */
	private $beaconSecret;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_BeaconSecurityType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_BeaconSecurityType();
		return $i;
	}
	/**
	 * Returns the value for the property beaconUuid.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	public function getBeaconUuid(){
		return $this->beaconUuid;
	}
	
	/**
	 * Sets the value for the property beaconUuid.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType $beaconUuid
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_BeaconSecurityType
	 */
	public function setBeaconUuid($beaconUuid){
		if ($beaconUuid instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType) {
			$this->beaconUuid = $beaconUuid;
		}
		else {
			throw new BadMethodCallException("Type of argument beaconUuid must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property majorId.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_Integer
	 */
	public function getMajorId(){
		return $this->majorId;
	}
	
	/**
	 * Sets the value for the property majorId.
	 * 
	 * @param integer $majorId
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_BeaconSecurityType
	 */
	public function setMajorId($majorId){
		if ($majorId instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_Integer) {
			$this->majorId = $majorId;
		}
		else {
			$this->majorId = Customweb_Twint_Stubs_Org_W3_XMLSchema_Integer::_()->set($majorId);
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property minorId.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_Integer
	 */
	public function getMinorId(){
		return $this->minorId;
	}
	
	/**
	 * Sets the value for the property minorId.
	 * 
	 * @param integer $minorId
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_BeaconSecurityType
	 */
	public function setMinorId($minorId){
		if ($minorId instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_Integer) {
			$this->minorId = $minorId;
		}
		else {
			$this->minorId = Customweb_Twint_Stubs_Org_W3_XMLSchema_Integer::_()->set($minorId);
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property beaconInitString.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_String
	 */
	public function getBeaconInitString(){
		return $this->beaconInitString;
	}
	
	/**
	 * Sets the value for the property beaconInitString.
	 * 
	 * @param string $beaconInitString
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_BeaconSecurityType
	 */
	public function setBeaconInitString($beaconInitString){
		if ($beaconInitString instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_String) {
			$this->beaconInitString = $beaconInitString;
		}
		else {
			$this->beaconInitString = Customweb_Twint_Stubs_Org_W3_XMLSchema_String::_()->set($beaconInitString);
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property beaconSecret.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_String
	 */
	public function getBeaconSecret(){
		return $this->beaconSecret;
	}
	
	/**
	 * Sets the value for the property beaconSecret.
	 * 
	 * @param string $beaconSecret
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_BeaconSecurityType
	 */
	public function setBeaconSecret($beaconSecret){
		if ($beaconSecret instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_String) {
			$this->beaconSecret = $beaconSecret;
		}
		else {
			$this->beaconSecret = Customweb_Twint_Stubs_Org_W3_XMLSchema_String::_()->set($beaconSecret);
		}
		return $this;
	}
	
	
	
}
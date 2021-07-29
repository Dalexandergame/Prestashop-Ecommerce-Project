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
 * @XmlType(name="ErrorCode", namespace="http://service.twint.ch/fault/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Fault_Types_V2_ErrorCode {
	/**
	 * @XmlValue(name="Code", simpleType=@XmlSimpleTypeDefinition(typeName='string', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_String'), namespace="http://service.twint.ch/fault/types/v2")
	 * @var string
	 */
	private $code;
	
	/**
	 * @XmlValue(name="Status", simpleType=@XmlSimpleTypeDefinition(typeName='string', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_String'), namespace="http://service.twint.ch/fault/types/v2")
	 * @var string
	 */
	private $status;
	
	/**
	 * @XmlValue(name="DetailCode", simpleType=@XmlSimpleTypeDefinition(typeName='string', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_String'), namespace="http://service.twint.ch/fault/types/v2")
	 * @var string
	 */
	private $detailCode;
	
	/**
	 * @XmlValue(name="DetailDescription", simpleType=@XmlSimpleTypeDefinition(typeName='string', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_String'), namespace="http://service.twint.ch/fault/types/v2")
	 * @var string
	 */
	private $detailDescription;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Fault_Types_V2_ErrorCode
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Fault_Types_V2_ErrorCode();
		return $i;
	}
	/**
	 * Returns the value for the property code.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_String
	 */
	public function getCode(){
		return $this->code;
	}
	
	/**
	 * Sets the value for the property code.
	 * 
	 * @param string $code
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Fault_Types_V2_ErrorCode
	 */
	public function setCode($code){
		if ($code instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_String) {
			$this->code = $code;
		}
		else {
			$this->code = Customweb_Twint_Stubs_Org_W3_XMLSchema_String::_()->set($code);
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property status.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_String
	 */
	public function getStatus(){
		return $this->status;
	}
	
	/**
	 * Sets the value for the property status.
	 * 
	 * @param string $status
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Fault_Types_V2_ErrorCode
	 */
	public function setStatus($status){
		if ($status instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_String) {
			$this->status = $status;
		}
		else {
			$this->status = Customweb_Twint_Stubs_Org_W3_XMLSchema_String::_()->set($status);
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property detailCode.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_String
	 */
	public function getDetailCode(){
		return $this->detailCode;
	}
	
	/**
	 * Sets the value for the property detailCode.
	 * 
	 * @param string $detailCode
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Fault_Types_V2_ErrorCode
	 */
	public function setDetailCode($detailCode){
		if ($detailCode instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_String) {
			$this->detailCode = $detailCode;
		}
		else {
			$this->detailCode = Customweb_Twint_Stubs_Org_W3_XMLSchema_String::_()->set($detailCode);
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property detailDescription.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_String
	 */
	public function getDetailDescription(){
		return $this->detailDescription;
	}
	
	/**
	 * Sets the value for the property detailDescription.
	 * 
	 * @param string $detailDescription
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Fault_Types_V2_ErrorCode
	 */
	public function setDetailDescription($detailDescription){
		if ($detailDescription instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_String) {
			$this->detailDescription = $detailDescription;
		}
		else {
			$this->detailDescription = Customweb_Twint_Stubs_Org_W3_XMLSchema_String::_()->set($detailDescription);
		}
		return $this;
	}
	
	
	
}
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

require_once 'Customweb/Twint/Stubs/Org/W3/XMLSchema/Integer.php';
require_once 'Customweb/Twint/Stubs/Org/W3/XMLSchema/String.php';
/**
 * @XmlType(name="CodeValueType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CodeValueType extends Customweb_Twint_Stubs_Org_W3_XMLSchema_String {
	/**
	 * @XmlAttribute(name="code", simpleType=@XmlSimpleTypeDefinition(typeName='int', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_Integer')) 
	 * @var integer
	 */
	private $code;
	
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CodeValueType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CodeValueType();
		return $i;
	}
	/**
	 * Returns the value for the property code.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_Integer
	 */
	public function getCode(){
		return $this->code;
	}
	
	/**
	 * Sets the value for the property code.
	 * 
	 * @param integer $code
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CodeValueType
	 */
	public function setCode($code){
		if ($code instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_Integer) {
			$this->code = $code;
		}
		else {
			$this->code = Customweb_Twint_Stubs_Org_W3_XMLSchema_Integer::_()->set($code);
		}
		return $this;
	}
	
	
	
}
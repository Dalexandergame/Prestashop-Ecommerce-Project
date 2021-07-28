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
 * @XmlType(name="KeyValueType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_KeyValueType extends Customweb_Twint_Stubs_Org_W3_XMLSchema_String {
	/**
	 * @XmlAttribute(name="key", simpleType=@XmlSimpleTypeDefinition(typeName='token', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_String')) 
	 * @var string
	 */
	private $key;
	
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_KeyValueType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_KeyValueType();
		return $i;
	}
	/**
	 * Returns the value for the property key.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_String
	 */
	public function getKey(){
		return $this->key;
	}
	
	/**
	 * Sets the value for the property key.
	 * 
	 * @param string $key
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_KeyValueType
	 */
	public function setKey($key){
		if ($key instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_String) {
			$this->key = $key;
		}
		else {
			$this->key = Customweb_Twint_Stubs_Org_W3_XMLSchema_String::_()->set($key);
		}
		return $this;
	}
	
	
	
}
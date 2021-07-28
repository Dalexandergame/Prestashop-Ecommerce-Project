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
/**
 * @XmlType(name="ResponseHeaderType", namespace="http://service.twint.ch/header/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Header_Types_V2_ResponseHeaderType {
	/**
	 * @XmlElement(name="MessageId", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType", namespace="http://service.twint.ch/header/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	private $messageId;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Header_Types_V2_ResponseHeaderType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Header_Types_V2_ResponseHeaderType();
		return $i;
	}
	/**
	 * Returns the value for the property messageId.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	public function getMessageId(){
		return $this->messageId;
	}
	
	/**
	 * Sets the value for the property messageId.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType $messageId
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Header_Types_V2_ResponseHeaderType
	 */
	public function setMessageId($messageId){
		if ($messageId instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType) {
			$this->messageId = $messageId;
		}
		else {
			throw new BadMethodCallException("Type of argument messageId must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType.");
		}
		return $this;
	}
	
	
	
}
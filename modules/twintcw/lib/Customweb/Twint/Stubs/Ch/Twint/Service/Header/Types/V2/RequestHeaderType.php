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
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/Token50Type.php';
/**
 * @XmlType(name="RequestHeaderType", namespace="http://service.twint.ch/header/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Header_Types_V2_RequestHeaderType {
	/**
	 * @XmlElement(name="MessageId", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType", namespace="http://service.twint.ch/header/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	private $messageId;
	
	/**
	 * @XmlElement(name="ClientSoftwareName", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type", namespace="http://service.twint.ch/header/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type
	 */
	private $clientSoftwareName;
	
	/**
	 * @XmlElement(name="ClientSoftwareVersion", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type", namespace="http://service.twint.ch/header/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type
	 */
	private $clientSoftwareVersion;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Header_Types_V2_RequestHeaderType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Header_Types_V2_RequestHeaderType();
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Header_Types_V2_RequestHeaderType
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
	
	
	/**
	 * Returns the value for the property clientSoftwareName.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type
	 */
	public function getClientSoftwareName(){
		return $this->clientSoftwareName;
	}
	
	/**
	 * Sets the value for the property clientSoftwareName.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type $clientSoftwareName
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Header_Types_V2_RequestHeaderType
	 */
	public function setClientSoftwareName($clientSoftwareName){
		if ($clientSoftwareName instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type) {
			$this->clientSoftwareName = $clientSoftwareName;
		}
		else {
			throw new BadMethodCallException("Type of argument clientSoftwareName must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property clientSoftwareVersion.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type
	 */
	public function getClientSoftwareVersion(){
		return $this->clientSoftwareVersion;
	}
	
	/**
	 * Sets the value for the property clientSoftwareVersion.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type $clientSoftwareVersion
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Header_Types_V2_RequestHeaderType
	 */
	public function setClientSoftwareVersion($clientSoftwareVersion){
		if ($clientSoftwareVersion instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type) {
			$this->clientSoftwareVersion = $clientSoftwareVersion;
		}
		else {
			throw new BadMethodCallException("Type of argument clientSoftwareVersion must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type.");
		}
		return $this;
	}
	
	
	
}
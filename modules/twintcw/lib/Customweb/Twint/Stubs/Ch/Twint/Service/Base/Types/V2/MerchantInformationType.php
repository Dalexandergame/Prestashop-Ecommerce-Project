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
 * Restriction of the Base Merchant Information.
 * 			In contrary to that it MUST contain a CashRegister Id. Used as the default type for operations
 * 			within the *-POS Cases, where the Actions are performed by specific CashRegisters
 * 
 * @XmlType(name="MerchantInformationType", namespace="http://service.twint.ch/base/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType {
	/**
	 * @XmlElement(name="MerchantUuid", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType", namespace="http://service.twint.ch/base/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	private $merchantUuid;
	
	/**
	 * @XmlElement(name="MerchantAliasId", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type", namespace="http://service.twint.ch/base/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type
	 */
	private $merchantAliasId;
	
	/**
	 * @XmlElement(name="CashRegisterId", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type", namespace="http://service.twint.ch/base/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type
	 */
	private $cashRegisterId;
	
	/**
	 * @XmlElement(name="ServiceAgentUuid", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType", namespace="http://service.twint.ch/base/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	private $serviceAgentUuid;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType();
		return $i;
	}
	/**
	 * Returns the value for the property merchantUuid.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	public function getMerchantUuid(){
		return $this->merchantUuid;
	}
	
	/**
	 * Sets the value for the property merchantUuid.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType $merchantUuid
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType
	 */
	public function setMerchantUuid($merchantUuid){
		if ($merchantUuid instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType) {
			$this->merchantUuid = $merchantUuid;
		}
		else {
			throw new BadMethodCallException("Type of argument merchantUuid must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property merchantAliasId.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type
	 */
	public function getMerchantAliasId(){
		return $this->merchantAliasId;
	}
	
	/**
	 * Sets the value for the property merchantAliasId.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type $merchantAliasId
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType
	 */
	public function setMerchantAliasId($merchantAliasId){
		if ($merchantAliasId instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type) {
			$this->merchantAliasId = $merchantAliasId;
		}
		else {
			throw new BadMethodCallException("Type of argument merchantAliasId must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property cashRegisterId.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type
	 */
	public function getCashRegisterId(){
		return $this->cashRegisterId;
	}
	
	/**
	 * Sets the value for the property cashRegisterId.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type $cashRegisterId
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType
	 */
	public function setCashRegisterId($cashRegisterId){
		if ($cashRegisterId instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type) {
			$this->cashRegisterId = $cashRegisterId;
		}
		else {
			throw new BadMethodCallException("Type of argument cashRegisterId must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property serviceAgentUuid.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	public function getServiceAgentUuid(){
		return $this->serviceAgentUuid;
	}
	
	/**
	 * Sets the value for the property serviceAgentUuid.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType $serviceAgentUuid
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType
	 */
	public function setServiceAgentUuid($serviceAgentUuid){
		if ($serviceAgentUuid instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType) {
			$this->serviceAgentUuid = $serviceAgentUuid;
		}
		else {
			throw new BadMethodCallException("Type of argument serviceAgentUuid must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType.");
		}
		return $this;
	}
	
	
	
}
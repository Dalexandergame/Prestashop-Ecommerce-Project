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
 * @XmlType(name="GetCertificateValidityRequestType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_GetCertificateValidityRequestType {
	/**
	 * @XmlElement(name="MerchantUuid", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	private $merchantUuid;
	
	/**
	 * @XmlElement(name="MerchantAliasId", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type
	 */
	private $merchantAliasId;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_GetCertificateValidityRequestType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_GetCertificateValidityRequestType();
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_GetCertificateValidityRequestType
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_GetCertificateValidityRequestType
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
	
	
	
}
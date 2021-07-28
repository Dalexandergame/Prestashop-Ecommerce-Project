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

require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/MerchantInformationType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CustomerInformationType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/UuidType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/PairingStatusType.php';
/**
 * @XmlType(name="CheckInNotificationType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckInNotificationType {
	/**
	 * @XmlElement(name="MerchantInformation", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType
	 */
	private $merchantInformation;
	
	/**
	 * @XmlElement(name="CustomerInformation", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CustomerInformationType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CustomerInformationType
	 */
	private $customerInformation;
	
	/**
	 * @XmlElement(name="PairingUuid", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	private $pairingUuid;
	
	/**
	 * @XmlElement(name="PairingStatus", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PairingStatusType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PairingStatusType
	 */
	private $pairingStatus;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckInNotificationType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckInNotificationType();
		return $i;
	}
	/**
	 * Returns the value for the property merchantInformation.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType
	 */
	public function getMerchantInformation(){
		return $this->merchantInformation;
	}
	
	/**
	 * Sets the value for the property merchantInformation.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType $merchantInformation
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckInNotificationType
	 */
	public function setMerchantInformation($merchantInformation){
		if ($merchantInformation instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType) {
			$this->merchantInformation = $merchantInformation;
		}
		else {
			throw new BadMethodCallException("Type of argument merchantInformation must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property customerInformation.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CustomerInformationType
	 */
	public function getCustomerInformation(){
		return $this->customerInformation;
	}
	
	/**
	 * Sets the value for the property customerInformation.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CustomerInformationType $customerInformation
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckInNotificationType
	 */
	public function setCustomerInformation($customerInformation){
		if ($customerInformation instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CustomerInformationType) {
			$this->customerInformation = $customerInformation;
		}
		else {
			throw new BadMethodCallException("Type of argument customerInformation must be Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CustomerInformationType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property pairingUuid.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	public function getPairingUuid(){
		return $this->pairingUuid;
	}
	
	/**
	 * Sets the value for the property pairingUuid.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType $pairingUuid
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckInNotificationType
	 */
	public function setPairingUuid($pairingUuid){
		if ($pairingUuid instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType) {
			$this->pairingUuid = $pairingUuid;
		}
		else {
			throw new BadMethodCallException("Type of argument pairingUuid must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property pairingStatus.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PairingStatusType
	 */
	public function getPairingStatus(){
		return $this->pairingStatus;
	}
	
	/**
	 * Sets the value for the property pairingStatus.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PairingStatusType $pairingStatus
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckInNotificationType
	 */
	public function setPairingStatus($pairingStatus){
		if ($pairingStatus instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PairingStatusType) {
			$this->pairingStatus = $pairingStatus;
		}
		else {
			throw new BadMethodCallException("Type of argument pairingStatus must be Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PairingStatusType.");
		}
		return $this;
	}
	
	
	
}
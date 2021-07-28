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
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/OrderType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/PairingStatusType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/UuidType.php';
/**
 * @XmlType(name="MonitorOrderResponseType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_MonitorOrderResponseType {
	/**
	 * @XmlElement(name="MerchantInformation", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType
	 */
	private $merchantInformation;
	
	/**
	 * @XmlElement(name="Order", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderType
	 */
	private $order;
	
	/**
	 * @XmlElement(name="PairingStatus", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PairingStatusType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PairingStatusType
	 */
	private $pairingStatus;
	
	/**
	 * @XmlElement(name="CustomerRelationUuid", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	private $customerRelationUuid;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_MonitorOrderResponseType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_MonitorOrderResponseType();
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_MonitorOrderResponseType
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
	 * Returns the value for the property order.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderType
	 */
	public function getOrder(){
		return $this->order;
	}
	
	/**
	 * Sets the value for the property order.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderType $order
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_MonitorOrderResponseType
	 */
	public function setOrder($order){
		if ($order instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderType) {
			$this->order = $order;
		}
		else {
			throw new BadMethodCallException("Type of argument order must be Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderType.");
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_MonitorOrderResponseType
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
	
	
	/**
	 * Returns the value for the property customerRelationUuid.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	public function getCustomerRelationUuid(){
		return $this->customerRelationUuid;
	}
	
	/**
	 * Sets the value for the property customerRelationUuid.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType $customerRelationUuid
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_MonitorOrderResponseType
	 */
	public function setCustomerRelationUuid($customerRelationUuid){
		if ($customerRelationUuid instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType) {
			$this->customerRelationUuid = $customerRelationUuid;
		}
		else {
			throw new BadMethodCallException("Type of argument customerRelationUuid must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType.");
		}
		return $this;
	}
	
	
	
}
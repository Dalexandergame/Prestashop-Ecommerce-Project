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
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/OrderStatusType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/NumericTokenType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/DataUriScheme.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CustomerInformationType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/PairingStatusType.php';
/**
 * @XmlType(name="StartOrderResponseType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderResponseType {
	/**
	 * @XmlElement(name="OrderUuid", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	private $orderUuid;
	
	/**
	 * @XmlElement(name="OrderStatus", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderStatusType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderStatusType
	 */
	private $orderStatus;
	
	/**
	 * @XmlElement(name="Token", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_NumericTokenType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_NumericTokenType
	 */
	private $token;
	
	/**
	 * @XmlElement(name="QRCode", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_DataUriScheme", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_DataUriScheme
	 */
	private $qRCode;
	
	/**
	 * @XmlElement(name="CustomerInformation", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CustomerInformationType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CustomerInformationType
	 */
	private $customerInformation;
	
	/**
	 * @XmlElement(name="PairingStatus", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PairingStatusType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PairingStatusType
	 */
	private $pairingStatus;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderResponseType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderResponseType();
		return $i;
	}
	/**
	 * Returns the value for the property orderUuid.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	public function getOrderUuid(){
		return $this->orderUuid;
	}
	
	/**
	 * Sets the value for the property orderUuid.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType $orderUuid
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderResponseType
	 */
	public function setOrderUuid($orderUuid){
		if ($orderUuid instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType) {
			$this->orderUuid = $orderUuid;
		}
		else {
			throw new BadMethodCallException("Type of argument orderUuid must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property orderStatus.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderStatusType
	 */
	public function getOrderStatus(){
		return $this->orderStatus;
	}
	
	/**
	 * Sets the value for the property orderStatus.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderStatusType $orderStatus
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderResponseType
	 */
	public function setOrderStatus($orderStatus){
		if ($orderStatus instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderStatusType) {
			$this->orderStatus = $orderStatus;
		}
		else {
			throw new BadMethodCallException("Type of argument orderStatus must be Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderStatusType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property token.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_NumericTokenType
	 */
	public function getToken(){
		return $this->token;
	}
	
	/**
	 * Sets the value for the property token.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_NumericTokenType $token
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderResponseType
	 */
	public function setToken($token){
		if ($token instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_NumericTokenType) {
			$this->token = $token;
		}
		else {
			throw new BadMethodCallException("Type of argument token must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_NumericTokenType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property qRCode.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_DataUriScheme
	 */
	public function getQRCode(){
		return $this->qRCode;
	}
	
	/**
	 * Sets the value for the property qRCode.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_DataUriScheme $qRCode
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderResponseType
	 */
	public function setQRCode($qRCode){
		if ($qRCode instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_DataUriScheme) {
			$this->qRCode = $qRCode;
		}
		else {
			throw new BadMethodCallException("Type of argument qRCode must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_DataUriScheme.");
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderResponseType
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderResponseType
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
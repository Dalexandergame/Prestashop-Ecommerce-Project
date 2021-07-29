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

require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/MerchantTransactionReferenceType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/UuidType.php';
/**
 * @XmlType(name="OrderLinkType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderLinkType {
	/**
	 * @XmlElement(name="MerchantTransactionReference", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantTransactionReferenceType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantTransactionReferenceType
	 */
	private $merchantTransactionReference;
	
	/**
	 * @XmlElement(name="OrderUuid", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType
	 */
	private $orderUuid;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderLinkType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderLinkType();
		return $i;
	}
	/**
	 * Returns the value for the property merchantTransactionReference.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantTransactionReferenceType
	 */
	public function getMerchantTransactionReference(){
		return $this->merchantTransactionReference;
	}
	
	/**
	 * Sets the value for the property merchantTransactionReference.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantTransactionReferenceType $merchantTransactionReference
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderLinkType
	 */
	public function setMerchantTransactionReference($merchantTransactionReference){
		if ($merchantTransactionReference instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantTransactionReferenceType) {
			$this->merchantTransactionReference = $merchantTransactionReference;
		}
		else {
			throw new BadMethodCallException("Type of argument merchantTransactionReference must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantTransactionReferenceType.");
		}
		return $this;
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderLinkType
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
	
	
	
}
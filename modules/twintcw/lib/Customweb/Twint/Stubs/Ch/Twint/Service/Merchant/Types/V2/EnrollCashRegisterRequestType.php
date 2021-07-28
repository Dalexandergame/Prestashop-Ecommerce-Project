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
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CashRegisterType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/Token50Type.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/Token100Type.php';
/**
 * @XmlType(name="EnrollCashRegisterRequestType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_EnrollCashRegisterRequestType {
	/**
	 * @XmlElement(name="MerchantInformation", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType
	 */
	private $merchantInformation;
	
	/**
	 * @XmlElement(name="CashRegisterType", type="Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CashRegisterType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CashRegisterType
	 */
	private $cashRegisterType;
	
	/**
	 * @XmlElement(name="FormerCashRegisterId", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type
	 */
	private $formerCashRegisterId;
	
	/**
	 * @XmlElement(name="BeaconInventoryNumber", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token100Type", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token100Type
	 */
	private $beaconInventoryNumber;
	
	/**
	 * @XmlElement(name="BeaconDaemonVersion", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type
	 */
	private $beaconDaemonVersion;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_EnrollCashRegisterRequestType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_EnrollCashRegisterRequestType();
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
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_EnrollCashRegisterRequestType
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
	 * Returns the value for the property cashRegisterType.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CashRegisterType
	 */
	public function getCashRegisterType(){
		return $this->cashRegisterType;
	}
	
	/**
	 * Sets the value for the property cashRegisterType.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CashRegisterType $cashRegisterType
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_EnrollCashRegisterRequestType
	 */
	public function setCashRegisterType($cashRegisterType){
		if ($cashRegisterType instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CashRegisterType) {
			$this->cashRegisterType = $cashRegisterType;
		}
		else {
			throw new BadMethodCallException("Type of argument cashRegisterType must be Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CashRegisterType.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property formerCashRegisterId.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type
	 */
	public function getFormerCashRegisterId(){
		return $this->formerCashRegisterId;
	}
	
	/**
	 * Sets the value for the property formerCashRegisterId.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type $formerCashRegisterId
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_EnrollCashRegisterRequestType
	 */
	public function setFormerCashRegisterId($formerCashRegisterId){
		if ($formerCashRegisterId instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type) {
			$this->formerCashRegisterId = $formerCashRegisterId;
		}
		else {
			throw new BadMethodCallException("Type of argument formerCashRegisterId must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property beaconInventoryNumber.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token100Type
	 */
	public function getBeaconInventoryNumber(){
		return $this->beaconInventoryNumber;
	}
	
	/**
	 * Sets the value for the property beaconInventoryNumber.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token100Type $beaconInventoryNumber
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_EnrollCashRegisterRequestType
	 */
	public function setBeaconInventoryNumber($beaconInventoryNumber){
		if ($beaconInventoryNumber instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token100Type) {
			$this->beaconInventoryNumber = $beaconInventoryNumber;
		}
		else {
			throw new BadMethodCallException("Type of argument beaconInventoryNumber must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token100Type.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property beaconDaemonVersion.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type
	 */
	public function getBeaconDaemonVersion(){
		return $this->beaconDaemonVersion;
	}
	
	/**
	 * Sets the value for the property beaconDaemonVersion.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type $beaconDaemonVersion
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_EnrollCashRegisterRequestType
	 */
	public function setBeaconDaemonVersion($beaconDaemonVersion){
		if ($beaconDaemonVersion instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type) {
			$this->beaconDaemonVersion = $beaconDaemonVersion;
		}
		else {
			throw new BadMethodCallException("Type of argument beaconDaemonVersion must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type.");
		}
		return $this;
	}
	
	
	
}
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

require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/PositiveDecimal.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/Token3Type.php';
/**
 * @XmlType(name="CurrencyAmountType", namespace="http://service.twint.ch/base/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType {
	/**
	 * @XmlElement(name="Amount", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_PositiveDecimal", namespace="http://service.twint.ch/base/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_PositiveDecimal
	 */
	private $amount;
	
	/**
	 * @XmlElement(name="Currency", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token3Type", namespace="http://service.twint.ch/base/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token3Type
	 */
	private $currency;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType();
		return $i;
	}
	/**
	 * Returns the value for the property amount.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_PositiveDecimal
	 */
	public function getAmount(){
		return $this->amount;
	}
	
	/**
	 * Sets the value for the property amount.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_PositiveDecimal $amount
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType
	 */
	public function setAmount($amount){
		if ($amount instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_PositiveDecimal) {
			$this->amount = $amount;
		}
		else {
			throw new BadMethodCallException("Type of argument amount must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_PositiveDecimal.");
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property currency.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token3Type
	 */
	public function getCurrency(){
		return $this->currency;
	}
	
	/**
	 * Sets the value for the property currency.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token3Type $currency
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType
	 */
	public function setCurrency($currency){
		if ($currency instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token3Type) {
			$this->currency = $currency;
		}
		else {
			throw new BadMethodCallException("Type of argument currency must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token3Type.");
		}
		return $this;
	}
	
	
	
}
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

require_once 'Customweb/Twint/Stubs/Org/W3/XMLSchema/String.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/CurrencyAmountType.php';
/**
 * @XmlType(name="PaymentAmountType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PaymentAmountType {
	/**
	 * @XmlValue(name="PaymentMethod", simpleType=@XmlSimpleTypeDefinition(typeName='string', typeNamespace='http://www.w3.org/2001/XMLSchema', type='Customweb_Twint_Stubs_Org_W3_XMLSchema_String'), namespace="http://service.twint.ch/merchant/types/v2")
	 * @var string
	 */
	private $paymentMethod;
	
	/**
	 * @XmlElement(name="Amount", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType
	 */
	private $amount;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PaymentAmountType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PaymentAmountType();
		return $i;
	}
	/**
	 * Returns the value for the property paymentMethod.
	 * 
	 * @return Customweb_Twint_Stubs_Org_W3_XMLSchema_String
	 */
	public function getPaymentMethod(){
		return $this->paymentMethod;
	}
	
	/**
	 * Sets the value for the property paymentMethod.
	 * 
	 * @param string $paymentMethod
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PaymentAmountType
	 */
	public function setPaymentMethod($paymentMethod){
		if ($paymentMethod instanceof Customweb_Twint_Stubs_Org_W3_XMLSchema_String) {
			$this->paymentMethod = $paymentMethod;
		}
		else {
			$this->paymentMethod = Customweb_Twint_Stubs_Org_W3_XMLSchema_String::_()->set($paymentMethod);
		}
		return $this;
	}
	
	
	/**
	 * Returns the value for the property amount.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType
	 */
	public function getAmount(){
		return $this->amount;
	}
	
	/**
	 * Sets the value for the property amount.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType $amount
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_PaymentAmountType
	 */
	public function setAmount($amount){
		if ($amount instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType) {
			$this->amount = $amount;
		}
		else {
			throw new BadMethodCallException("Type of argument amount must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_CurrencyAmountType.");
		}
		return $this;
	}
	
	
	
}
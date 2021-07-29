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

require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/OrderType.php';
/**
 * @XmlType(name="FindOrderResponseType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_FindOrderResponseType {
	/**
	 * @XmlList(name="Order", type='Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderType', namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderType[]
	 */
	private $order;
	
	public function __construct() {
		$this->order = new ArrayObject();
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_FindOrderResponseType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_FindOrderResponseType();
		return $i;
	}
	/**
	 * Returns the value for the property order.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderType[]
	 */
	public function getOrder(){
		return $this->order;
	}
	
	/**
	 * Sets the value for the property order.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderType $order
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_FindOrderResponseType
	 */
	public function setOrder($order){
		if (is_array($order)) {
			$order = new ArrayObject($order);
		}
		if ($order instanceof ArrayObject) {
			$this->order = $order;
		}
		else {
			throw new BadMethodCallException("Type of argument order must be ArrayObject.");
		}
		return $this;
	}
	
	/**
	 * Adds the given $item to the list of items of order.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderType $item
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_FindOrderResponseType
	 */
	public function addOrder(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderType $item) {
		if (!($this->order instanceof ArrayObject)) {
			$this->order = new ArrayObject();
		}
		$this->order[] = $item;
		return $this;
	}
	
	
}
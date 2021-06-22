<?php

/*

* 2007-2011 PrestaShop

*

* NOTICE OF LICENSE

*

* This source file is subject to the Open Software License (OSL 3.0)

* that is bundled with this package in the file LICENSE.txt.

* It is also available through the world-wide-web at this URL:

* http://opensource.org/licenses/osl-3.0.php

* If you did not receive a copy of the license and are unable to

* obtain it through the world-wide-web, please send an email

* to license@prestashop.com so we can send you a copy immediately.

*

* DISCLAIMER

*

* Do not edit or add to this file if you wish to upgrade PrestaShop to newer

* versions in the future. If you wish to customize PrestaShop for your

* needs please refer to http://www.prestashop.com for more information.

*

*  @author PrestaShop SA <contact@prestashop.com>

*  @copyright  2007-2011 PrestaShop SA

*  @version  Release: $Revision: 7551 $

*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)

*  International Registered Trademark & Property of PrestaShop SA

*/



/* Inclusion pour la version 1.4 de Prestashop

*  ControllerFactory::includeController('ParentOrderController');

*/


class OrderOpcController extends OrderOpcControllerCore

{



	public function getInfosDateDelivery()

	{

		$result = Db::getInstance()->getRow('

			SELECT pd.`id_planning_delivery_carrier`, `date_delivery`, `id_planning_delivery_carrier_slot`

			FROM `'._DB_PREFIX_.'planning_delivery_carrier` pd

			WHERE pd.`id_cart` = '.(int)(self::$cart->id));

		return $result;

	}



	protected function _getPaymentMethods()

	{

		if (!$this->isLogged)

			return '<p class="warning">'.Tools::displayError('Please sign in to see payment methods').'</p>';

		if ($this->context->cart->OrderExists())

			return '<p class="warning">'.Tools::displayError('Error: this order has already been validated').'</p>';

		if (!$this->context->cart->id_customer || !Customer::customerIdExistsStatic($this->context->cart->id_customer) || Customer::isBanned($this->context->cart->id_customer))

			return '<p class="warning">'.Tools::displayError('Error: no customer').'</p>';

		$address_delivery = new Address($this->context->cart->id_address_delivery);

		$address_invoice = ($this->context->cart->id_address_delivery == $this->context->cart->id_address_invoice ? $address_delivery : new Address($this->context->cart->id_address_invoice));

		if (!$this->context->cart->id_address_delivery || !$this->context->cart->id_address_invoice || !Validate::isLoadedObject($address_delivery) || !Validate::isLoadedObject($address_invoice) || $address_invoice->deleted || $address_delivery->deleted)

			return '<p class="warning">'.Tools::displayError('Error: please choose an address').'</p>';

		if (count($this->context->cart->getDeliveryOptionList()) == 0 && !$this->context->cart->isVirtualCart())

		{

			if ($this->context->cart->isMultiAddressDelivery())

				return '<p class="warning">'.Tools::displayError('Error: There are no carriers available that deliver to some of your addresses').'</p>';

			else

				return '<p class="warning">'.Tools::displayError('Error: There are no carriers available that deliver to this address').'</p>';

		}

		if (!$this->context->cart->getDeliveryOption(null, false) && !$this->context->cart->isVirtualCart())

			return '<p class="warning">'.Tools::displayError('Error: please choose a carrier').'</p>';

		if (!$this->context->cart->id_currency)

			return '<p class="warning">'.Tools::displayError('Error: no currency has been selected').'</p>';



		/* If no planning carrier infos */

		$tab = self::getInfosDateDelivery();

		if (!class_exists('PlanningDeliveryByCarrier')) require_once(_PS_ROOT_DIR_.'/modules/planningdeliverybycarrier/planningdeliverybycarrier.php');

		$planning = new PlanningDeliveryByCarrier();

		if (!Validate::isDate($tab['date_delivery']))

			return '<p class="warning">'.Tools::displayError($planning->l('Thank you indicate your delivery date.', 'planningdeliverybycarrier')).'</p>';

		$slotRequired = Configuration::get('PLANNING_DELIVERY_SLOT_'.$this->context->cart->id_carrier);

		if ((!Validate::isUnsignedId($tab['id_planning_delivery_carrier_slot']) || $tab['id_planning_delivery_carrier_slot'] == 0) && $slotRequired)

			return '<p class="warning">'.Tools::displayError($planning->l('Thank you to select a time slot for your delivery.', 'planningdeliverybycarrier')).'</p>';



		if (!$this->context->cookie->checkedTOS && Configuration::get('PS_CONDITIONS'))

			return '<p class="warning">'.Tools::displayError('Please accept the Terms of Service').'</p>';



		/* If some products have disappear */

		if (!$this->context->cart->checkQuantities())

			return '<p class="warning">'.Tools::displayError('An item in your cart is no longer available, you cannot proceed with your order.').'</p>';



		/* Check minimal amount */

		$currency = Currency::getCurrency((int)$this->context->cart->id_currency);



		$minimalPurchase = Tools::convertPrice((float)Configuration::get('PS_PURCHASE_MINIMUM'), $currency);

		if ($this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS) < $minimalPurchase)

			return '<p class="warning">'.sprintf(

				Tools::displayError('A minimum purchase total of %d is required in order to validate your order.'),

				Tools::displayPrice($minimalPurchase, $currency)

			).'</p>';



		/* Bypass payment step if total is 0 */

		if ($this->context->cart->getOrderTotal() <= 0)

			return '<p class="center"><input type="button" class="exclusive_large" name="confirmOrder" id="confirmOrder" value="'.Tools::displayError('I confirm my order').'" onclick="confirmFreeOrder();" /></p>';



		$return = Hook::exec('displayPayment');

		if (!$return)

			return '<p class="warning">'.Tools::displayError('No payment method is available').'</p>';

		return $return;



	}

}

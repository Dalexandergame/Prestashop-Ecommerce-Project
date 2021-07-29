<?php

/**
 *  * You are allowed to use this API in your web application.
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

require_once 'Customweb/Twint/Container.php';
require_once 'Customweb/Payment/BackendOperation/Form/Abstract.php';
require_once 'Customweb/Form/Element.php';
require_once 'Customweb/Form/ElementGroup.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Form/IButton.php';
require_once 'Customweb/Form/Control/Html.php';
require_once 'Customweb/Form/Button.php';
require_once 'Customweb/Twint/Util.php';



/**
 *
 * @author Sebastian Bossert
 * @BackendForm
 *
 */
class Customweb_Twint_BackendOperation_Form_CashRegister extends Customweb_Payment_BackendOperation_Form_Abstract {
	private $TwintContainer;

	public function isProcessable(){
		return true;
	}

	public function getTitle(){
		return Customweb_I18n_Translation::__("Cash Register Generation");
	}

	public function getElementGroups(){
		$elementGroups = array();
		$elementGroups[] = $this->getCashRegisterElementGroup();
		return $elementGroups;
	}

	public function getButtons(){
		return array(
			$this->getGenerateButton() 
		);
	}

	public function process(Customweb_Form_IButton $pressedButton, array $formData){
		if ($pressedButton->getMachineName() == 'generate') {
			$this->generateCashRegister();
		}
	}

	/**
	 *
	 * @return Customweb_Form_IElementGroup
	 */
	protected function getCashRegisterElementGroup(){
		$id = $this->getTwintContainer()->getCashRegister()->getCashRegisterId();
		$group = new Customweb_Form_ElementGroup();
		$group->addElement($this->getCashRegisterElement($id));
		return $group;
	}

	/**
	 *
	 * @param string $text
	 * @return Customweb_Form_Element
	 */
	protected function getCashRegisterElement($text){
		$control = new Customweb_Form_Control_Html('twintcash-register-control', $text);
		$control->setRequired(false);
		$element = new Customweb_Form_Element(Customweb_I18n_Translation::__("Cash Register ID"), $control);
		$element->setRequired(false);
		$element->setDescription(
				Customweb_I18n_Translation::__(
						"The 'cash register' identifies your shop. If you ever need to change the id, you can generate a new id here."));
		return $element;
	}

	/**
	 *
	 * @return Customweb_Form_Button
	 */
	protected function getGenerateButton(){
		$button = new Customweb_Form_Button();
		$button->setMachineName("generate")->setTitle(Customweb_I18n_Translation::__("Generate Cash Register"))->setType(
				Customweb_Form_IButton::TYPE_SUCCESS);
		return $button;
	}

	protected function getTwintContainer(){
		if ($this->TwintContainer == null) {
			$this->TwintContainer = new Customweb_Twint_Container(parent::getContainer());
		}
		return $this->TwintContainer;
	}

	private function generateCashRegister(){
		Customweb_Twint_Util::generateCashRegisterId($this->gettwintContainer());
	}
}
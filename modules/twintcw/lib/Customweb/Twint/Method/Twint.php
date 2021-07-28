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
require_once 'Customweb/Twint/Update/StatusHandler.php';
require_once 'Customweb/Twint/Method/IMethod.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/OrderKindType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/UuidType.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/DataUriScheme.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/StartOrderResponseElement.php';
require_once 'Customweb/Util/Invoice.php';
require_once 'Customweb/Twint/Authorization/Transaction.php';
require_once 'Customweb/Payment/Authorization/AbstractPaymentMethodWrapper.php';
require_once 'Customweb/Util/Currency.php';
require_once 'Customweb/Twint/StubBuilder/ConfirmOrder.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Twint/Method/Template/Token.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/NumericTokenType.php';
require_once 'Customweb/Twint/StubBuilder/Create.php';
require_once 'Customweb/Twint/Util.php';
require_once 'Customweb/Twint/StubBuilder/Refund.php';


/**
 * @Method()
 *
 * @author Sebastian Bossert
 */
class Customweb_Twint_Method_Twint extends Customweb_Payment_Authorization_AbstractPaymentMethodWrapper implements 
		Customweb_Twint_Method_IMethod {
	private $container;
	private static $paymentInformationMap = array(
		'twint' => array(
			'machine_name' => 'Twint',
 			'method_name' => 'Twint',
 			'parameters' => array(
			),
 			'not_supported_features' => array(
			),
 			'supported_currencies' => array(
				0 => 'CHF',
 			),
 			'image_color' => 'data:image/png;base64,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',
 			'image_grey' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHgAAAAtEAAAAAAQ3CwJAAAFRklEQVR42u2af0wbVRzAq66admwJJBC29Q8aIWERzEggYRMCSsAfmE1DXNloUgaIuHW1MpxNwYGMMQNsSIm0BRooyArJ2GCFRIhOidWQyI8ua6QqzoYyh8Csrgllu7XPPh633pVDS7vU9Mf3/uj73t27u8+97/t+v+97pV36MG40ujUwtsSWUT4tPJrOD5wtbpQWSLhwCwIHgQMTmCE8fmNpCYDx8eSOAADOsGi14JEolSytHwNH1/fnAycxm8XxO8r8EDhUVLvfYgGUMjub87SfAfPCFtrBv8pXwn1SPwFO6R4fBy4Ihsnl4RU+DszS9uSCLYjJJGAzhD4LfCbBbAZbFr0+XeWTwLww4KaYTL5h2k7ASiWBQf1nr0H9VwxQr2nP33tlsfDeKpjbDDnD4q2HZmkPPckLS+4IFT1G4IfbPt/eOFQ33XUCaqvTI/kjmV9EDnDVadiCa8B9XAOlcJh0fk8ubNWl4ueK4zUavJ2uQudBi+EwUTtKhx+NbRwsctyzP39XFdp/8UXqu5XGuARsmx44KH1dQmuoMb1kh58Z3j208uXu0VPX2L3M/k6b2BXgsTLqF1MYAQHRzMfPnZ0FAE9aq5PgsYkJ2C6MQH2i6/GxdQ6VS0soNJJskyDVSS4B3wbt5TLaz88ic9ZP9irV55a/tpmNVT3H2sBvlZ4C75MSQZI7YFuSifqhgFi7nwrYET8MBjwlQq/GQ+Dv35DNfzb0aDKHdDVcV6B250HZ3m9+3cocxh+avHdehsPT+ZJM2J6XoewOw6CGvD4ZmLWe0Y+VQWsIFeGvlLigybCQX5GLc3j8qebhJta6uwJDaW0lV/esNefkIZJKzROeAysUaCHigEeQr9mQz0eRnQyMjgGA53fpKqQL2B4Dm7dfZDXU3Fx/g3dCmsOlkXfsl5/Z1rRQn3pX7Dkwh4kM0/HYAMjldH5dKnJG5L7o8QVs5yvBJSvZbN0EBmCQdz65Ztlyd03J7kq4IGspWxY0HP/kmmrduG1Sq9FqdBcYN924ZrkcLUagA2II0UK0eJIKGLkz4pUMhscGjJ2s5VfltL+AzHol6ezH57JqrlYvVv99fwbusRpL0wqwDzQ2mnvAuFMTsOEoabXIb6NxBwAPQ14EBsBYLNaLBJofkHazVTwnzhadn9Egvbe1ACvAls+6O8J4aEIjWhoTpYMjrtdDTadz7usVYAAux59Ke7/OtD6TJSnCxPeeWbmw5ru4EHfkgftzGA9GaM0F04cRPq47EhIvAz+sEr3Jzytvt07Z9lzftdp34sDbB5q+s+8vP0kvwMQ2kO0JMJ2PJxGDRVDL27nxSl4Gto9k/DtHik539/Uzjkp+//bGABzXHxWdl+GvadETL028G6qd7CgzmVABybHY9DqwPdF4mXeLe+vIlcNXLv0EwKdSiLrRnN0DRmMKfTMxNuMh6X8Ctk69+wvn/lsR3CbsD/sioq8kEuJWTpHN2T3g8AroqFpedSwbiCHJK8AoJjqZdX/OsUMfzaJMCyCzNsdsPC+le6vAdL5GQ04MYTR2rIy8ABzbSFWlvP2cMcuhGVRUuGOblm1jGwsj4EZdO+OFkfW8nVR90co3sc35Shwm1BPbiGtlYg8XSjxRuj7u1qsdp/f6SlWLsmqZrpqYcBUWwxQKX/oSQdvsW1Lx5H9VpaFoNERz8vFCfHhFXSpK8KnFYCDPN7/4thTbOFxCBWuxnEnwza9LLnw9zHqAUnqH9OQSQ4cffi5lCAVslPbBCpKvlNw9/AcAnNFKJXU0Df7lIQgcBA4Ce2djHKUl1wQSMGflH6/zD3joju8xAAAAAElFTkSuQmCC',
 		),
 	);
	/**
	 * How long the widget will wait between sending requests.
	 * 2 seconds.
	 *
	 * @var integer
	 */
	const UPDATE_INTERVAL = 2000;
	/**
	 * How long the widget will wait before timing a request out.
	 * 30 seconds.
	 *
	 * @var integer
	 */
	const UPDATE_TIMEOUT = 30000;

	public function __construct(Customweb_Payment_Authorization_IPaymentMethod $paymentMethod, Customweb_DependencyInjection_IContainer $container){
		parent::__construct($paymentMethod);
		if (!$container instanceof Customweb_Twint_Container) {
			$container = new Customweb_Twint_Container($container);
		}
		$this->container = $container;
	}

	public function getVisibleFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $paymentCustomerContext){
		$formFields = array();
		return $formFields;
	}
	
	public function getHtml(Customweb_Payment_Authorization_ITransaction $transaction, array $formData){
		if (!$transaction instanceof Customweb_Twint_Authorization_Transaction) {
			throw new Exception('Transaction must be of type Customweb_Twint_Authorization_Transaction.');
		}
		
		$response = $this->sendStartOrderRequest($transaction, $formData);
		
		$orderId = $response->getOrderUUID();
		if (empty($orderId)) {
			throw new Exception(Customweb_I18n_Translation::__("The order could not be started: The order id is missing."));
		}
		
		$id = $orderId->get();
		$transaction->setPaymentId($id);
		
		try {
			$templateRenderer = new Customweb_Twint_Method_Template_Token($this->getContainer(), $response, $transaction);
			$template = $templateRenderer->renderTemplate();
			
			return $template;
		}
		catch (Exception $exc) {
			Customweb_Twint_Util::cancelOrder($this->getContainer(), $id);
			throw $exc;
		}
	}

	public function getWidgetHTML(Customweb_Payment_Authorization_ITransaction $transaction, array $formData){
		if (!$transaction instanceof Customweb_Twint_Authorization_Transaction) {
			throw new Exception('Transaction must be of type Customweb_Twint_Authorization_Transaction.');
		}
		
		$response = $this->sendStartOrderRequest($transaction, $formData);
		
		$orderId = $response->getOrderUUID();
		if (empty($orderId)) {
			throw new Exception(Customweb_I18n_Translation::__("The order could not be started: The order id is missing."));
		}
		
		$id = $orderId->get();
		$transaction->setPaymentId($id);
		
		try {
			$templateRenderer = new Customweb_Twint_Method_Template_Token($this->getContainer(), $response, $transaction);
			$template = $templateRenderer->renderTemplate();
			
			return $template;
		}
		catch (Exception $exc) {
			Customweb_Twint_Util::cancelOrder($this->getContainer(), $id);
			throw $exc;
		}
	}

	public function cancel(Customweb_Twint_Authorization_Transaction $transaction){
		$transaction->cancelDry();
		
		Customweb_Twint_Util::cancelOrder($this->getContainer(), $transaction->getPaymentId());
		
		$transaction->cancel();
	}

	public function partialRefund(Customweb_Twint_Authorization_Transaction $transaction, $items, $close){
		$transaction->refundByLineItemsDry($items, $close);
		
		$amount = Customweb_Util_Invoice::getTotalAmountIncludingTax($items);
		
		$refundBuilder = new Customweb_Twint_StubBuilder_Refund($transaction, $this->getContainer(), $amount);
		$refundStub = $refundBuilder->build();
		$response = $this->getContainer()->getSoapService()->startOrder($refundStub);
		
		if ($response->getOrderStatus()->getStatus()->getCode()->get() == Customweb_Twint_Update_StatusHandler::PROCESSING_MERCHANT) {
			$confirmRefundBuilder = new Customweb_Twint_StubBuilder_ConfirmOrder($this->getContainer(), $transaction, $amount);
			$confirmStub = $confirmRefundBuilder->build();
			$this->getContainer()->getSoapService()->confirmOrder($confirmStub);
		}
		
		$transaction->refundByLineItems($items, $close);
	}

	public function partialCapture(Customweb_Twint_Authorization_Transaction $transaction, $items, $close){
		$transaction->partialCaptureByLineItemsDry($items, $close);
		
		$amount = Customweb_Util_Invoice::getTotalAmountIncludingTax($items);
		if (Customweb_Util_Currency::compareAmount(Customweb_Util_Invoice::getTotalAmountIncludingTax($transaction->getUncapturedLineItems()), $amount,
				$transaction->getCurrencyCode()) === 0) {
			$close = true;
		}
		
		$isPartial = !$close;
		
		$confirmBuilder = new Customweb_Twint_StubBuilder_ConfirmOrder($this->getContainer(), $transaction, $amount, $isPartial);
		$confirmStub = $confirmBuilder->build();
		$this->getContainer()->getSoapService()->confirmOrder($confirmStub);
		
		$transaction->partialCaptureByLineItems($items, $close);
	}

	public function validate(Customweb_Payment_Authorization_IOrderContext $orderContext, Customweb_Payment_Authorization_IPaymentCustomerContext $paymentContext, array $formData){
		return;
	}

	public function isDeferredCapturingActive(){
		return $this->getPaymentMethodConfigurationValue('capturing') == 'deferred';
	}

	/**
	 *
	 * @param Customweb_Twint_Authorization_Transaction $transaction
	 * @param array $formData
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderResponseElement
	 */
	public function sendStartOrderRequest(Customweb_Twint_Authorization_Transaction $transaction, array $formData){
		// prevent start order from being started twice
		if ($transaction->getPaymentId() != null) {
			return $this->createStartOrderResponseFromTransaction($transaction);
		}
		
		if ($this->getContainer()->getPaymentMethodByTransaction($transaction)->isDeferredCapturingActive()) {
			$orderType = Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderKindType::PAYMENT_DEFERRED();
		}
		else {
			$orderType = Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_OrderKindType::PAYMENT_IMMEDIATE();
		}
		
		$requestBuilder = new Customweb_Twint_StubBuilder_Create($transaction, $this->getContainer(), $orderType);
		$startOrderRequest = $requestBuilder->build();
		$response = $this->getContainer()->getSoapService()->startOrder($startOrderRequest);
		
		$this->saveStartOrderResponse($transaction, $response);
		
		return $response;
	}

	private function createStartOrderResponseFromTransaction(Customweb_Twint_Authorization_Transaction $transaction){
		$response = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderResponseElement();
		$response->setOrderUUID(Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType::_()->set($transaction->getPaymentId()));
		$response->setQRCode(Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_DataUriScheme::_()->set($transaction->getQRCode()));
		$response->setToken(Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_NumericTokenType::_()->set($transaction->getToken()));
		return $response;
	}

	private function saveStartOrderResponse(Customweb_Twint_Authorization_Transaction $transaction, Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderResponseElement $response){
		$temp = $response->getToken();
		if (!empty($temp)) {
			$transaction->setToken($temp->get());
		}
		$temp = $response->getOrderUUID();
		if (!empty($temp)) {
			$transaction->setPaymentId($temp->get());
		}
		$temp = $response->getQRCode();
		if (!empty($temp)) {
			$transaction->setQRCode($temp->get());
		}
	}

	protected function getPaymentInformationMap(){
		return self::$paymentInformationMap;
	}

	/**
	 *
	 * @return Customweb_Twint_Container
	 *
	 */
	protected function getContainer(){
		return $this->container;
	}
}

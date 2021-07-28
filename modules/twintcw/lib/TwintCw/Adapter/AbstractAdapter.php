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

require_once 'Customweb/Util/Url.php';
require_once 'Customweb/Util/Html.php';

require_once 'TwintCw/Util.php';
require_once 'TwintCw/Entity/Transaction.php';
require_once 'TwintCw/Adapter/IAdapter.php';
require_once 'TwintCw/FormRenderer.php';

abstract class TwintCw_Adapter_AbstractAdapter implements TwintCw_Adapter_IAdapter {
	
	/**
	 *
	 * @var Customweb_Payment_Authorization_IAdapter
	 */
	private $interfaceAdapter;
	
	/**
	 *
	 * @var Customweb_Payment_Authorization_IOrderContext
	 */
	private $orderContext;
	
	/**
	 *
	 * @var TwintCw_IPaymentMethod
	 */
	protected $paymentMethod;
	
	/**
	 *
	 * @var TwintCw_Entity_Transaction
	 */
	protected $failedTransaction = null;
	
	/**
	 *
	 * @var TwintCw_Entity_Transaction
	 */
	protected $aliasTransaction = null;
	
	/**
	 *
	 * @var int
	 */
	protected $aliasTransactionId = null;
	
	/**
	 *
	 * @var TwintCw_Entity_Transaction
	 */
	private $transaction = null;
	
	/**
	 *
	 * @var string
	 */
	private $redirectUrl = null;
	protected $context = null;
	protected $smarty = null;
	private static $frontendJSOutputted = false;

	public function __construct(){
		// Load context and smarty
		$this->context = Context::getContext();
		if (is_object($this->context->smarty)) {
			$this->smarty = $this->context->smarty->createData($this->context->smarty);
			$this->smarty->escape_html = false;
		}
	}

	/**
	 * This method returns a AJAX response, when the transaction is created with
	 * an AJAX call.
	 *
	 * @throws Exception
	 * @return string JavaScript which is executed on.
	 */
	abstract protected function getTransactionAjaxResponseCallback();

	public function setInterfaceAdapter(Customweb_Payment_Authorization_IAdapter $interface){
		$this->interfaceAdapter = $interface;
	}

	public function getInterfaceAdapter(){
		return $this->interfaceAdapter;
	}
	
	public function isHeaderRedirectionSupported(){
		require_once 'Customweb/Licensing/TwintCw/License.php';
		$arguments = null;
		return Customweb_Licensing_TwintCw_License::run('ksi4s91tvou0iog3', $this, $arguments);
	}

	final public function call_e8jgl3cut1m15kvq() {
		$arguments = func_get_args();
		$method = $arguments[0];
		$call = $arguments[1];
		$parameters = array_slice($arguments, 2);
		if ($call == 's') {
			return call_user_func_array(array(get_class($this), $method), $parameters);
		}
		else {
			return call_user_func_array(array($this, $method), $parameters);
		}
		
		
	}
	protected function setRedirectUrl($redirectUrl){
		$this->redirectUrl = $redirectUrl;
		return $this;
	}

	public function getRedirectionUrl(){
		return $this->redirectUrl;
	}

	public function handleAliasTransaction(TwintCw_IPaymentMethod $paymentMethod, Customweb_Payment_Authorization_IOrderContext $orderContext){
		$this->aliasTransaction = null;
		$this->aliasTransactionId = null;
		$this->paymentMethod = $paymentMethod;
		$this->orderContext = $orderContext;
		
		
	}

	public function prepareCheckout(TwintCw_IPaymentMethod $paymentMethod, Customweb_Payment_Authorization_IOrderContext $orderContext, $failedTransaction, $createTransaction){
		if ($failedTransaction !== null & !($failedTransaction instanceof TwintCw_Entity_Transaction)) {
			throw new Exception("The failed transaction is not of instance TwintCw_Entity_Transaction.");
		}
		
		$this->paymentMethod = $paymentMethod;
		$this->failedTransaction = $failedTransaction;
		$this->orderContext = $orderContext;
		
		$this->transaction = null;
		
		$this->handleAliasTransaction($paymentMethod, $orderContext);
		
		if ($createTransaction === true) {
			$this->createNewTransaction();
		}
		
		$transaction = $this->getTransaction();
		$this->preparePaymentFormPane();
		if ($transaction !== null && $transaction->getTransactionObject()->isAuthorizationFailed()) {
			$this->setRedirectUrl(
					Customweb_Util_Url::appendParameters($transaction->getTransactionObject()->getTransactionContext()->getFailedUrl(), 
							$transaction->getTransactionObject()->getTransactionContext()->getCustomParameters()));
		}
	}

	public function processTransactionCreationAjaxCall(){
		try {
			$this->executeValidation();
			$transaction = $this->createNewTransaction();
			$js = $this->getTransactionAjaxResponseCallback();
			$rs = array(
				'status' => 'success',
				'callback' => $js 
			);
		}
		catch (Exception $e) {
			$rs = array(
				'status' => 'error',
				'message' => $e->getMessage() 
			);
		}
		$this->persistTransaction();
		return $rs;
	}

	protected function getPaymentCustomerContext(){
		return TwintCw_Util::getPaymentCustomerContext($this->getOrderContext()->getCustomerId());
	}

	protected function executeValidation(){
		$this->getInterfaceAdapter()->validate($this->getOrderContext(), 
				TwintCw_Util::getPaymentCustomerContext($this->getOrderContext()->getCustomerId()), $this->getFormData());
	}

	protected function getFormData(){
		return $_REQUEST;
	}

	public function getCheckoutPageHtml($renderOnLoadJS){
		return $this->getPaymentFormPane($renderOnLoadJS);
	}

	public function getCheckoutPageForm(){
		return $this->getCheckoutPageHtml(false);
	}
	
	
	protected function getOrderContext(){
		return $this->orderContext;
	}

	/**
	 *
	 * @return TwintCw_Entity_Transaction
	 */
	protected final function createNewTransaction(){
		$orderContext = $this->getOrderContext();
		$this->transaction = $this->paymentMethod->createTransaction($this->getOrderContext(), $this->aliasTransactionId, 
				$this->getFailedTransactionObject());
		return $this->transaction;
	}

	/**
	 *
	 * @return TwintCw_Entity_Transaction
	 */
	public function getTransaction(){
		return $this->transaction;
	}

	protected function getAliasTransactionObject(){
		$aliasTransactionObject = null;
		$orderContext = $this->getOrderContext();
		
		if ($this->aliasTransactionId === 'new') {
			$aliasTransactionObject = 'new';
		}
		
		if ($this->aliasTransaction !== null && $this->aliasTransaction->getCustomerId() !== null &&
				 $this->aliasTransaction->getCustomerId() == $orderContext->getCustomerId()) {
			$aliasTransactionObject = $this->aliasTransaction->getTransactionObject();
		}
		
		return $aliasTransactionObject;
	}

	protected function getFailedTransactionObject(){
		$failedTransactionObject = null;
		$orderContext = $this->getOrderContext();
		if ($this->failedTransaction !== null && $this->failedTransaction->getCustomerId() !== null &&
				 $this->failedTransaction->getCustomerId() == $orderContext->getCustomerId()) {
			$failedTransactionObject = $this->failedTransaction->getTransactionObject();
		}
		return $failedTransactionObject;
	}

	protected function getPaymentFormPaneVariables($renderOnLoadJS){
		$templateVars = $this->getBaseVariables();
		
		$actionUrl = $this->getFormActionUrl();
		if ($actionUrl !== null && !empty($actionUrl)) {
			$templateVars['formActionUrl'] = $actionUrl;
		}
		
		
		

		$visibleFormFields = $this->getVisibleFormFields();
		$isAnyFieldMandatory = false;
		if ($visibleFormFields !== null && count($visibleFormFields) > 0) {
			$renderer = new TwintCw_FormRenderer($this->paymentMethod->getPaymentMethodName());
			$renderer->setRenderOnLoadJs($renderOnLoadJS);
			$templateVars['visibleFormFields'] = $renderer->renderElements($visibleFormFields);
			foreach ($visibleFormFields as $field) {
				if ($field->isRequired() || $field->getControl()->isRequired()) {
					$isAnyFieldMandatory = true;
				}
			}
		}
		else {
			$templateVars['visibleFormFields'] = null;
		}
		$templateVars['isAnyFieldMandatory'] = $isAnyFieldMandatory;
		
		$hiddenFormFields = $this->getHiddenFormFields();
		if ($hiddenFormFields !== null && count($hiddenFormFields) > 0) {
			$templateVars['hiddenFields'] = Customweb_Util_Html::buildHiddenInputFields($hiddenFormFields);
		}
		else {
			$templateVars['hiddenFields'] = null;
		}
		
		$templateVars['additionalOutput'] = $this->getAdditionalFormHtml();
		$templateVars['buttons'] = $this->getOrderConfirmationButton();
		$templateVars['error_message'] = null;
		
		if ($this->failedTransaction != null) {
			$errors = $this->failedTransaction->getTransactionObject()->getErrorMessages();
			$templateVars['error_message'] = end($errors);
		}
		
		return $templateVars;
	}

	protected function getPaymentFormPane($renderOnLoadJS){
		$this->smarty->assign($this->getPaymentFormPaneVariables($renderOnLoadJS));
		return $this->renderTemplate('form/pane.tpl');
	}

	protected function persistTransaction(){
		if ($this->getTransaction() !== null) {
			TwintCw_Util::getEntityManager()->persist($this->getTransaction());
		}
	}
	
	protected function getBaseVariables(){
		require_once 'Customweb/Licensing/TwintCw/License.php';
		$arguments = null;
		return Customweb_Licensing_TwintCw_License::run('qmg30ft4r5rus6fj', $this, $arguments);
	}

	final public function call_afhn71hktqn00li5() {
		$arguments = func_get_args();
		$method = $arguments[0];
		$call = $arguments[1];
		$parameters = array_slice($arguments, 2);
		if ($call == 's') {
			return call_user_func_array(array(get_class($this), $method), $parameters);
		}
		else {
			return call_user_func_array(array($this, $method), $parameters);
		}
		
		
	}
	private function setFrontendJsOutput($js){
		self::$frontendJSOutputted = $js;
	}

	private function getFrontendJsOutput(){
		return self::$frontendJSOutputted;
	}

	protected function getPaymentMethod(){
		return $this->paymentMethod;
	}

	protected function renderErrorMessage($message){
		$this->smarty->assign(array(
			'errorMessage' => $message 
		));
		return $this->renderTemplate('form/error.tpl');
	}

	protected function getAdditionalFormHtml(){
		return '';
	}

	/**
	 * Method to load some data before the payment pane is rendered.
	 */
	protected function preparePaymentFormPane(){}

	protected function getVisibleFormFields(){
		return array();
	}

	protected function getFormActionUrl(){
		return null;
	}

	protected function getHiddenFormFields(){
		return array();
	}

	protected function getOrderConfirmationButton(){
		return $this->renderTemplate('form/buttons.tpl');
	}

	protected function renderTemplate($template){
		$overloaded = false;
		$moduleName = 'twintcw';
		
		$templatePath = $this->getTemplatePath($template);
		$overloaded = false;
		if (strpos($templatePath, _PS_THEME_DIR_)) {
			$overloaded = true;
		}
		
		$this->smarty->assign(
				array(
					'module_dir' => __PS_BASE_URI__ . 'modules/' . $moduleName . '/',
					'module_template_dir' => ($overloaded ? _THEME_DIR_ : __PS_BASE_URI__) . 'modules/' . $moduleName . '/' 
				));
		$result = $this->context->smarty->createTemplate($this->getTemplatePath($template), null, null, $this->smarty)->fetch();
		
		return $result;
	}

	private function getTemplatePath($template){
		$moduleName = 'twintcw';
		$pathsToCheck = array(
			_PS_THEME_DIR_ . 'modules/' . $moduleName . '/' . $template,
			_PS_THEME_DIR_ . 'modules/' . $moduleName . '/views/templates/front/' . $template,
			_PS_MODULE_DIR_ . $moduleName . '/views/templates/front/' . $template 
		);
		
		foreach ($pathsToCheck as $path) {
			if (Tools::file_exists_cache($path)) {
				return $path;
			}
		}
		
		return null;
	}

	public function l($string, $specific = false){
		return TwintCw::translate($string);
	}
}
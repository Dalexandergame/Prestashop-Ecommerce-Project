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

require_once 'Customweb/Mvc/Template/RenderContext.php';
require_once 'Customweb/Twint/Method/Twint.php';
require_once 'Customweb/Util/Currency.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Twint/AbstractAdapter.php';
require_once 'Customweb/Mvc/Template/SecurityPolicy.php';
require_once 'Customweb/Util/JavaScript.php';



/**
 *
 * @author Sebastian Bossert
 */
abstract class Customweb_Twint_Method_Template_Abstract extends Customweb_Twint_AbstractAdapter {
	private $transaction;
	private $response;

	public function __construct(Customweb_DependencyInjection_IContainer $container, Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderResponseElement $response, Customweb_Twint_Authorization_Transaction $transaction){
		parent::__construct($container);
		$this->transaction = $transaction;
		$this->response = $response;
		$this->assetResolver = $this->getContainer()->getBean('Customweb_Asset_IResolver');
	}

	public abstract function renderTemplate();

	/**
	 * Initialises a template and renders it.
	 *
	 * @param String $templateName
	 * @param String $css
	 * @param array $variables
	 *
	 * @return String
	 */
	protected function createTemplate($templateName, $css, array $variables){		
		$templateRenderer = $this->getContainer()->getBean('Customweb_Mvc_Template_IRenderer');
		/* @var $templateRenderer Customweb_Mvc_Template_IRenderer */
		
		$templateContext = new Customweb_Mvc_Template_RenderContext();
		$templateContext->setSecurityPolicy(new Customweb_Mvc_Template_SecurityPolicy());
		$templateContext->setTemplate($templateName);
		
		$amount = $this->getCurrency() . " " . Customweb_Util_Currency::formatAmount($this->getAmount(), $this->getCurrency());
		
		$templateContext->addVariable('amount', $amount);
		$templateContext->addVariable('script', $this->getScript());
		$templateContext->addVariable('styleUrl', $this->resolve($css));
		
		$templateContext->addVariable('cancelText', Customweb_I18n_Translation::__('Return to order'));
		$templateContext->addVariable('cancelUrl', $this->getCancelUrl());
		
		
		foreach ($variables as $key => $value) {
			$templateContext->addVariable($key, $value);
		}
		
		return $templateRenderer->render($templateContext);
	}

	protected function getScript(){
		return $this->getPollingScript();
	}
	
	protected function getPollingScript(){
		$pollingFunction = $this->getPollingFunction();
		$jQuerySnippet = Customweb_Util_JavaScript::getLoadJQueryCode('1.11.2', 'cwJQuery', $pollingFunction);
		//@formatter:off
		return 
'<script type="text/javascript">
var twintCanRun = true;
' . $jQuerySnippet . '
function twintCancel() {
	twintCanRun = false;
	document.getElementById("twint-image-loading").style.visibility = "visible";
	return false;
}
</script>';
		//@formatter:on
	}

	protected function getPollingFunction(){
		//@formatter:off
		$pollingFunction =
'
(function twintPoll() {
	if(typeof window.jQuery == "undefined") {
		window.jQuery = cwJQuery;
	}
	if(twintCanRun === false) {
		document.getElementById("twint-cancel-form").submit();
		return;
	}
    setTimeout(function() {
        window.jQuery.ajax({
            url: "' . $this->getPollingUrl() . '",
            type: "POST",
            success: function(data) {
                if(data.status == "COMPLETE") {
            		window.location.replace(data.redirect);
            		return;
            	}
            	twintPoll();
            },
            dataType: "json",
            error: twintPoll,
           	cache: false,
            timeout: ' . Customweb_Twint_Method_Twint::UPDATE_TIMEOUT . '
        })
    },  ' . Customweb_Twint_Method_Twint::UPDATE_INTERVAL . ');
})';
		//@formatter:on
		return $pollingFunction;
	}

	protected function getPollingUrl(){
		return $this->getContainer()->getEndpointAdapter()->getUrl('process', 'poll', array(
			"cw_transaction_id" => $this->getId() 
		));
	}

	protected function getCancelUrl(){
		return $this->getContainer()->getEndpointAdapter()->getUrl('process', 'cancel', array(
			"cw_transaction_id" => $this->getId(),
			"hash" => hash_hmac('sha1', $this->getTransaction()->getExternalTransactionId(), $this->getTransaction()->getFormKey())
		));
	}

	protected function getContinueUrl(){
		return $this->getContainer()->getEndpointAdapter()->getUrl('process', 'continue', array(
			"cw_transaction_id" => $this->getId() 
		));
	}

	protected function getTransaction(){
		return $this->transaction;
	}

	protected function getResponse(){
		return $this->response;
	}

	protected function getId(){
		return $this->getTransaction()->getExternalTransactionId();
	}

	protected function getAmount(){
		return $this->getTransaction()->getAuthorizationAmount();
	}

	protected function getCurrency(){
		return $this->transaction->getCurrencyCode();
	}
	
	protected function resolve($asset){
		return $this->assetResolver->resolveAssetUrl($asset);
	}
}
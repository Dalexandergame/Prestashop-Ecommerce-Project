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

require_once 'Customweb/Twint/Method/Template/Abstract.php';
require_once 'Customweb/Core/Url.php';
require_once 'Customweb/Mobile/Detect.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Core/Http/Client/Factory.php';
require_once 'Customweb/Core/Http/Request.php';


/**
 *
 * @author Sebastian Bossert
 */
class Customweb_Twint_Method_Template_Token extends Customweb_Twint_Method_Template_Abstract {

	public function renderTemplate(){
		$responseImage = $this->getResponse()->getQRCode();
		if (!empty($responseImage)) {
			$image = $responseImage->get();
		}
		$responseToken = $this->getResponse()->getToken();
		if (!empty($responseToken)) {
			$token = $responseToken->get();
		}
		if (empty($token) && empty($image)) {
			throw new Exception(Customweb_I18n_Translation::__("The order was cancelled: QR-Code and Numeric Code were not returned."));
		}

		$tokens = array_map('intval', str_split($token));

		$variables = array(
			'issuers' => $this->getIssuers(),
			'appSwitchUrl' => $this->resolve('mobile/twint-redirect.js'),
			'numericCode' => $tokens,
			'numericCodeRaw' => $token,
			'qrCode' => $image,
			'pageTitle' => Customweb_I18n_Translation::__("TWINT AG - Payment")->toString(),
			'backToOrderText' => Customweb_I18n_Translation::__("Return to order")->toString(),
			'backText' => Customweb_I18n_Translation::__("Back")->toString(),
			'informationText' => Customweb_I18n_Translation::__("Information")->toString(),
			'chooseText' => Customweb_I18n_Translation::__("Choose your TWINT app")->toString(),
			'switchText' => Customweb_I18n_Translation::__("Switch to your app now")->toString(),
			'orText' => Customweb_I18n_Translation::__("OR")->toString(),
			'enterText' => Customweb_I18n_Translation::__("Enter this code<br/>in your twint app:")->toString(),
			'copyText' => Customweb_I18n_Translation::__("Copy code")->toString(),
			'otherBanksText' => Customweb_I18n_Translation::__("Other Banks")->toString(),
			'iOSLinkSuffix' => htmlentities('applinks/?al_applink_data={"app_action_type": "TWINT_PAYMENT","extras": {"code": "' . $token . '",},"referer_app_link": {"target_url": "", "url": "", "app_name": "EXTERNAL_WEB_BROWSER"}, "version": "6.0" }'),
			'step1' => Customweb_I18n_Translation::__('Open the TWINT App on your smartphone.')->toString(),
			'step2' => Customweb_I18n_Translation::__("Select the QR code symbol at the bottom right.")->toString(),
			'step3' => Customweb_I18n_Translation::__(
					"Point your smartphone camera at the QR code. Or select the 'Code' tab and enter the payment code.")->toString()
		);

		$template = 'token';
		$css = 'style.css';
		$detect = new Customweb_Mobile_Detect($this->getContainer()->getHttpRequest());
		if ($detect->isMobileDevice()) {
			$template = 'mobile/twint';
			//$css = 'mobile/style.css';
		}
		return $this->createTemplate($template, $css, $variables);
	}

	protected function getScript(){
		return $this->getCssScript() . parent::getScript();
	}

	protected function getIssuers(){
		$request = new Customweb_Core_Http_Request(new Customweb_Core_Url($this->getContainer()->getConfiguration()->getAppSwitchUrl()));
		$response = Customweb_Core_Http_Client_Factory::createClient()->send($request);
		$issuers = json_decode($response->getBody(), true);
		return $issuers['appSwitchConfigList'];
	}

	private function getCssScript(){
		$replacements = json_encode(
				array(
					array(
						'selector' => '.icon-info',
						'value' => 'url(' . $this->resolve('images/twint-icon-info.svg') . ')',
						'property' => 'backgroundImage'
						//'value' => 'url(' . $this->resolve('images/twint-icon-info.svg') . ') 0 0 / 32px 32px stretch no-repeat',
						//'property' => 'background'
					),
					array(
						'selector' => '.nav',
						'value' => 'url(' . $this->resolve('images/line.png') . ') 30 stretch',
						'property' => 'borderImage'
					),
					array(
						'selector' => '.twint-logo',
						'value' => 'url(' . $this->resolve('images/twint_logo_q_pos_bg.png') . ')',
						'property' => 'backgroundImage'
					),
					array(
						'selector' => '.a.button.back-button',
						'value' => 'url(' . $this->resolve('images/arrow_back_white.png') . ')',
						'property' => 'backgroundImage'
					),
					array(
						'selector' => '.a.button.info-button',
						'value' => 'url(' . $this->resolve('images/info_icon_white.png') . ')',
						'property' => 'backgroundImage'
					),
					array(
						'selector' => '.section.nav .hint',
						'value' => 'url(' . $this->resolve('images/line.png') . ') 30 stretch',
						'property' => 'borderImage'
					),
					array(
						'selector' => '.icon-app',
						'value' => 'url(' . $this->resolve('images/twint_logo_q_pos_bg.png') . ')',
						'property' => 'backgroundImage'
					),
					array(
						'selector' => '.back-link',
						'value' => 'url(' . $this->resolve('images/header-icon.svg') . ') 0 0 no-repeat',
						'property' => 'background'
					),
					array(
						'selector' => '.icon-pay',
						'value' => 'url(' . $this->resolve('images/twint-icon-qr_code.svg') . ')',
						'property' => 'background'
					),
					array(
						'selector' => '.icon-cam',
						'value' => 'url(' . $this->resolve('images/twint-icon-kamera.svg') . ')',
						'property' => 'background'
					),
					array(
						'selector' => '.app-chooser',
						'value' => 'url(' . $this->resolve('images/arrow.svg') . ') no-repeat right 12px top 22px',
						'property' => 'background'
					),
					array(
						'selector' => '.bank-ubs',
						'value' => 'url(' . $this->resolve('images/ubs.svg') . ') 0 0 / contain no-repeat',
						'property' => 'background'
					),
					array(
						'selector' => '.bank-raiffeisen',
						'value' => 'url(' . $this->resolve('images/raiffeisen.svg') . ') 0 0 / contain no-repeat',
						'property' => 'background'
					),
					array(
						'selector' => '.bank-pf',
						'value' => 'url(' . $this->resolve('images/pf.svg') . ') 0 0 / contain no-repeat',
						'property' => 'background'
					),
					array(
						'selector' => '.bank-zkb',
						'value' => 'url(' . $this->resolve('images/zkb.svg') . ') 0 0 / contain no-repeat',
						'property' => 'background'
					),
					array(
						'selector' => '.bank-cs',
						'value' => 'url(' . $this->resolve('images/cs.svg') . ') 0 0 / contain no-repeat',
						'property' => 'background'
					),
					array(
						'selector' => '.bank-bcv',
						'value' => 'url(' . $this->resolve('images/bcv.svg') . ') 0 0 / contain no-repeat',
						'property' => 'background'
					),
					array(
						'selector' => '.hint .image .icon-tipp',
						'value' => 'url(' . $this->resolve('images/twint-icon-tipps.svg') . ') no-repeat',
						'property' => 'background'
					)
				));
		// @formatter:off
		$script =
"<script type='text/javascript'>
var replacements = $replacements;
for(var j = 0; j < replacements.length; j++) {
	var elements = document.querySelectorAll(replacements[j].selector);
	for(var i = 0; i < elements.length; i++) {
		elements[i].style[replacements[j].property] = replacements[j].value;
	}
}
</script>";
		return $script;
	}
}
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

require_once 'Customweb/Soap/Client.php';
require_once 'Customweb/Core/Util/Rand.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/Token50Type.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/V2/TWINTMerchantService.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Header/Types/V2/RequestHeaderElement.php';
require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/UuidType.php';


/**
 *
 * @author Sebastian Bossert
 */
class Customweb_Twint_TwintService extends Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_V2_TWINTMerchantService {
	private $certificate;
	private $passphrase;

	public function __construct($certificate, $passphrase, $url){
		$this->certificate = $certificate;
		$this->passphrase = $passphrase;
		parent::__construct();
		$this->overrideLocation("http://service.twint.ch/merchant/v2", $url);
	}

	protected function createSoapClient(){
		$client = new Customweb_Soap_Client();
		$client->getHttpClient()->setClientCertificate($this->certificate)->setClientCertificatePassphrase($this->passphrase);
		return $client;
	}

	protected function createSoapCall($operationName, $data, $outputClassName, $soapActionName = null){
		$call = parent::createSoapCall($operationName, $data, $outputClassName);
		
		$header = new Customweb_Twint_Stubs_Ch_Twint_Service_Header_Types_V2_RequestHeaderElement();
		$header->setClientSoftwareName(Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type::_()->set("Customweb"));
		$header->setClientSoftwareVersion(Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_Token50Type::_()->set("4.0.108"));
		$header->setMessageId(
				Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_UuidType::_()->set(Customweb_Core_Util_Rand::getUuid()));
		
		$call->addSoapHeader($header);
		
		return $call;
	}
}
<?php

if (!function_exists('getallheaders'))
{
	function getallheaders()
	{
		$headers = array ();
		foreach ($_SERVER as $name => $value)
		{
			if (substr($name, 0, 5) == 'HTTP_')
			{
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
		return $headers;
	}
}

require_once(dirname(__FILE__).'/../../config/config.inc.php');

require_once('cherrycheckout.php');
require_once('class/CherryCheckoutAuth.php');

if (!defined('_PS_VERSION_')) exit;

class CherryCheckoutPrestaWebservice
{
	private $idShop = 0;
	private $module;

	public function __construct($messageEncrypted, $timestamp)
	{
		$this->module = new CherryCheckout();

		try {
			$this->idShop = $this->_getShopFromProvidedKeys($messageEncrypted, $timestamp);

			// No shop found, throw an error
			if (!$this->idShop) {
				throw new Exception ('module is not active for this shop');
			}
		}
		catch (\Exception $ex) {
			throw $ex;
		}
	}

	private function _getShopFromProvidedKeys($messageEncrypted, $timestamp) {

		// If multi shop is active, check them all
		if (Shop::isFeatureActive()) {
			$shops = Shop::getShops();
			foreach($shops as $s) {
				Shop::setContext(Shop::CONTEXT_SHOP, $s['id_shop']);

				// Get keys for this shop
				$keys = $this->module->getApiKeys();
				$hash = CherryCheckoutAuth::hashKeys($keys['publicKey'], $keys['privateKey'], $timestamp);

				if ($messageEncrypted == $hash) {
					return $s['id_shop'];
				}
			}
		}
		// Only one shop
		else {
			$keys = $this->module->getApiKeys();
			$hash = CherryCheckoutAuth::hashKeys($keys['publicKey'], $keys['privateKey'], $timestamp);

			if ($messageEncrypted == $hash) {
				$context = Context::getContext();
				return $context->shop->id;
			}
		}

		return false;
	}


	public function resetMe()
	{
		// For multi shop, set the good context
		if (Shop::isFeatureActive()) {
			Shop::setContext(Shop::CONTEXT_SHOP, $this->idShop);
		}

		try {
			$this->module->resetMe();

			return array(
				'result' => true,
				'message' => 'me reset'
			);
		}
		catch (Exception $ex) {
			return array(
				'result' => false,
				'message' => $ex->getMessage()
			);
		}
	}

	public function getLogs()
	{
		try {
			$logFile = $this->module->generateLogFile();

			return array(
				'result' => true,
				'message' => $logFile
			);
		}
		catch (\Exception $ex) {
			return array(
				'result' => false,
				'message' => $ex->getMessage()
			);
		}
	}
}

$response = null;

try {
	$headers = getallheaders();
	$authorization = null;
	$timestamp = false;

	// Get headers parameters
	foreach (getallheaders() as $name => $value) {
		if (strtolower($name) == 'authorization') {
			$authorization = explode(':', $value);
		}
		else if (strtolower($name) == 'timestamp') {
			$timestamp = $value;
		}
	}

	// Check authorization parameters
	if (count($authorization) != 2 || !$timestamp) {
		throw new Exception('wrong authorization parameters');
	}

	$messageEncrypted = $authorization[1];
	$webservice = new CherryCheckoutPrestaWebservice($messageEncrypted, $timestamp);
}
catch (\Exception $ex) {
	$response = array(
		'result' => false,
		'message' => $ex->getMessage()
	);
}


if (!isset($_GET['action'])) {
	$response = array(
		'result' => false,
		'message' => 'action parameter is missing'
	);
}
elseif (isset($webservice)) {
	switch ($_GET['action'])
	{
		case 'resetMe':
			$response = $webservice->resetMe();
			break;
		case 'getLogs':
			$response = $webservice->getLogs();
			break;
		default:
			$response = array(
				'result' => false,
				'message' => 'action parameter is wrong'
			);
			break;
	}
}

if ($response) {
	echo json_encode($response);
}

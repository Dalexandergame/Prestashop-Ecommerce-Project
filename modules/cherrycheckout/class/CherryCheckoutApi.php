<?php

if (!defined('_PS_VERSION_')) exit;

require_once('CherryCheckoutAuth.php');

class CherryCheckoutApi
{
	private $api;
	private $publicKey;
	private $privateKey;

	private $me;

	public function __construct($api, $publicKey, $privateKey)
	{
		$this->api = $api;
		$this->publicKey = $publicKey;
		$this->privateKey = $privateKey;
	}

	public function me($forceFromApi = false)
	{
		if ($this->me && !$forceFromApi)
		{
			return $this->me;
		}

		try
		{
			$me = $this->_get('/app/me');
			return $me;
		}
		catch (\Exception $ex)
		{
			throw $ex;
		}
	}

	public function updateMe($values)
	{
		try {
			$this->_post('PUT', '/app/me', $values);
		}
		catch (\Exception $ex) {
			throw $ex;
		}
	}

	public function createOrder($order)
	{
		try {
			$this->_post('POST', '/orders', $order);
		}
		catch (\Exception $ex) {
			throw $ex;
		}
	}

	public function updateOrder($trackId, $order)
	{
		try {
			$this->_post('PUT', '/orders/'.$trackId, $order);
		}
		catch (\Exception $ex) {
			throw $ex;
		}
	}

	private function _getRequestHeaders()
	{
		$timestamp = time();

		// Crypt the (publicKey + timestamp) with the privateKey, in a sha256 algo
		$myKeyEncrypted = CherryCheckoutAuth::hashKeys($this->publicKey, $this->privateKey, $timestamp);

		return array(
			'Content-type: application/json',
			'authorization: '.$this->publicKey.':'.$myKeyEncrypted,
			'timestamp: '.$timestamp
		);
	}

	private function _get($route)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->api . $route);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_getRequestHeaders());

		$response = curl_exec($ch);

		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		// If httpCode is not 200, return an error
		if ($httpCode != 200) {
			if ($response) {
				throw new \Exception($response);
			}
			else {
				throw new \Exception('No response from API');
			}
		}

		return json_decode($response);
	}

	private function _post($method, $route, $params)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->api . $route);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_getRequestHeaders());
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

		$response = curl_exec($ch);

		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		// If httpCode is not 200, return an error
		if ($httpCode != 200) {
			if ($response) {
				throw new \Exception($response);
			}
			else {
				throw new \Exception('No response from API');
			}
		}

		return json_decode($response);
	}

}

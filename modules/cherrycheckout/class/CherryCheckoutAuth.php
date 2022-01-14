<?php

if (!defined('_PS_VERSION_')) exit;

class CherryCheckoutAuth
{
	public static function hashKeys($publicKey, $privateKey, $timestamp)
	{
		return base64_encode(hash_hmac('sha256', $publicKey.$timestamp, $privateKey));
	}
}

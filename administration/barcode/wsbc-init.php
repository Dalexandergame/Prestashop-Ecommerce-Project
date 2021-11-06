<?php

/**********************************************************
 * Initialization and Configuration of SOAPClient
 * for access to Webservice Barcode
 *
 * This configuration is used in all Webservice Barcode PHP examples
 **********************************************************/

		
// Webservice Login & Endpoint URL

//$username = 'TU_124024257_02';
//$password = 'WQ1dEdK3fb';

//$username = 'TU_124024257_01';
//$password = 'Hb58rQvZBx';

$username = 'TUW003257';
$password = "jN'oE6az67{F";

//$username = 'TU_125985012_02';
//$password = 'aGu8h6TUwX';

//$endpoint_url = 'https://www.mypostbusiness.ch/wsbc/barcode/v2_1';
$endpoint_url = 'https://wsbc.post.ch/wsbc/barcode/v2_2';

// SOAP Config
$SOAP_wsdl_file_path='barcode_v2_2.wsdl';
$SOAP_config = array(

	 // Webservice Endpoint URL
  	 'location' => $endpoint_url,

	 // Webservice Barcode Login
  	 'login' => $username,
     'password' => $password,

	 // Optional proxy config
	 // (if you are behind a proxy):
     //'proxy_host' => 'proxy-host',
     //'proxy_port' => proxy-port,

	 // Optional proxy authentication
	 // (if your proxy needs a username and password):
     // 'proxy_login' => 'proxy-username',
     // 'proxy_password' => 'proxy-password',

	 // Encoding for strings
     // 'encoding' => 'ISO-8859-1',

//	  Addtional debug trace information:
      'trace' => true,

	 // Connection timeout (in seconds):
  	 // 'connection_timeout' => 90

   );


// SOAP Client Initialization
try {

  $SOAP_Client = new SoapClient($SOAP_wsdl_file_path, $SOAP_config);
}
catch (SoapFault $fault) {
	echo('Error in SOAP Initialization: '. $fault -> __toString() .'<br/>');
	exit;
}

?>

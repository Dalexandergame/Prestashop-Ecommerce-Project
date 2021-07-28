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

require_once 'Customweb/I18n/Translation.php';



/**
 * Exception class to handle exceptions involved with reading configuration values.
 *
 * @author Sebastian Bossert
 */
class Customweb_Twint_Exception_ConfigurationException extends Exception {
	private $key;
	private $label;
	private $language;

	public function __construct($label, $key, $language){
		$this->label = $label;
		$this->key = $key;
		$this->language = $language;
		if (empty($language)) {
			parent::__construct(
					Customweb_I18n_Translation::__("The configuration value for @label must not be empty (Key: @key).", 
							array(
								"@label" => $label,
								"@key" => $key 
							))->toString());
		}
		else {
			parent::__construct(
					Customweb_I18n_Translation::__("The configuration value for @label must not be empty (Key: @key, Language: @language).", 
							array(
								"@label" => $label,
								"@key" => $key,
								"@language" => $language 
							))->toString());
		}
	}
}
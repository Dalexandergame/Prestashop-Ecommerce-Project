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

require_once 'Customweb/Core/DateTime.php';
require_once 'Customweb/Xml/Binding/DateHandler/Date.php';
require_once 'Customweb/Xml/Binding/DateHandler/IDateFormatable.php';



/**
 *
 * @author Bjoern Hasselmann
 *
 */
class Customweb_Xml_Binding_DateHandler_Date extends Customweb_Core_DateTime implements Customweb_Xml_Binding_DateHandler_IDateFormatable {
	const EXTENDED_FORMAT = "[-]CCYY-MM-DD[Z|(+|-)hh:mm]";

	public static function _($time = null, $timezone = null){
		return new Customweb_Xml_Binding_DateHandler_Date($time, $timezone);
	}

	public function formatForXml(){
		return $this->format('Y-m-d');
	}

	public function get(){
		return $this;
	}

	public function __toString(){
		return $this->formatForXml();
	}
}
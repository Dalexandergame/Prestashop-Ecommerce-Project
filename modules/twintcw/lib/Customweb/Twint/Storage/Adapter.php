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

require_once 'Customweb/Storage/IBackend.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Twint/Exception/StorageException.php';


/**
 * Class which handles read / write operations to a field with locks.
 *
 * @author Sebastian Bossert
 *
 */
class Customweb_Twint_Storage_Adapter {
	private $backend;
	private $storageSpaceKey;

	/**
	 *
	 * @param Customweb_Storage_IBackend $backend
	 * @param string $storageSpaceKey
	 */
	public function __construct(Customweb_Storage_IBackend $backend, $storageSpaceKey){
		$this->backend = $backend;
		$this->storageSpaceKey = $storageSpaceKey;
	}

	public final function read($key){
		$this->backend->lock($this->storageSpaceKey, $key, Customweb_Storage_IBackend::SHARED_LOCK);
		$value = $this->backend->read($this->storageSpaceKey, $key);
		$this->backend->unlock($this->storageSpaceKey, $key);
		if ($value === null) {
			throw new Customweb_Twint_Exception_StorageException(
					Customweb_I18n_Translation::__("@key could not be read from storage.", array(
						"@key" => $key 
					)));
		}
		return $value;
	}

	public final function write($value, $key){
		$this->backend->lock($this->storageSpaceKey, $key, Customweb_Storage_IBackend::EXCLUSIVE_LOCK);
		$this->backend->write($this->storageSpaceKey, $key, $value);
		$this->backend->unlock($this->storageSpaceKey, $key);
	}
}
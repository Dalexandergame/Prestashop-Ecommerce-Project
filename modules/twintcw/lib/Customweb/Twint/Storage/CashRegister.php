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

require_once 'Customweb/Twint/Container.php';
require_once 'Customweb/Util/Rand.php';
require_once 'Customweb/Twint/StubBuilder/EnrollCashRegister.php';
require_once 'Customweb/Twint/Storage/IStorageKey.php';
require_once 'Customweb/Twint/Storage/Adapter.php';



/**
 *
 * @author Sebastian Bossert
 * @Bean
 */
class Customweb_Twint_Storage_CashRegister {
	private $storageAdapter;
	private $container;

	public function __construct(Customweb_DependencyInjection_IContainer $container){
		$this->container = new Customweb_Twint_Container($container);
		$this->storageAdapter = new Customweb_Twint_Storage_Adapter($container->getBean('Customweb_Storage_IBackend'), 
				Customweb_Twint_Storage_IStorageKey::STORAGE_SPACE_KEY);
	}

	public function getCashRegisterId(){
		try {
			return $this->storageAdapter->read($this->getKey());
		}
		catch (Customweb_Twint_Exception_StorageException $exc) {
		}
		$id = Customweb_Util_Rand::getRandomString(50);
		$stubBuilder = new Customweb_Twint_StubBuilder_EnrollCashRegister($this->container, $id);
		$this->container->getSoapService()->enrollCashRegister($stubBuilder->build());
		$this->saveCashRegisterId($id);
		return $id;
	}

	public function saveCashRegisterId($id){
		$this->storageAdapter->write($id, $this->getKey());
	}

	private function getKey(){
		$isLive = $this->container->getConfiguration()->isLiveMode();
		$merchantUuid = $this->container->getConfiguration()->getMerchantUuid();
		return base64_encode(sha1(Customweb_Twint_Storage_IStorageKey::CASH_REGISTER_ID_KEY . $isLive . $merchantUuid));
	}
}
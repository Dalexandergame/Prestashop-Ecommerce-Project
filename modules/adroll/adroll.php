<?php
/**
 * This file is part of the prestahsop-adroll module.
 *
 * (c) AdRoll
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Damián Nohales <damian.nohales@adroll.com>
 * @copyright AdRoll
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

class Adroll extends Module
{
    const ADROLL_BASE_URI = 'https://app.adroll.com';
    const WEBSERVICE_KEY_DESCRIPTION = 'AdRoll';

    public function __construct()
    {
        $this->name = 'adroll';
        $this->tab = 'advertising_marketing';
        $this->version = '1.4.0';
        $this->author = 'AdRoll';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        $this->module_key = 'cdb33dcb3a4de5b12b374b42563bf180';

        parent::__construct();

        $this->displayName = $this->l('AdRoll Integration');
        $this->description = $this->l('Integrates AdRoll to your store.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        if (!$this->registerHook('displayTop')) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        $this->notifyUninstall();

        if (!Configuration::deleteByName('ADROLL_ADVERTISABLE_ID') ||
            !Configuration::deleteByName('ADROLL_PIXEL_ID')) {
            return false;
        }

        return true;
    }

    private function getConfiguration($key)
    {
        return Configuration::get($key, null, null, Shop::getContextShopID(), null);
    }

    private function updateConfiguration($key, $value)
    {
        Configuration::updateValue($key, $value, false, null, Shop::getContextShopID());
    }

    private function notifyUninstall($advertisableId = null)
    {
        if ($advertisableId === null) {
            $advertisableId = $this->getConfiguration('ADROLL_ADVERTISABLE_ID');
        }

        if (!$advertisableId) {
            return;
        }

        $ch = curl_init(self::ADROLL_BASE_URI . '/prestashop/api/v2/notify_addon_uninstall');
        $data = json_encode(array(
            'advertisable' => $advertisableId
        ));
        curl_setopt_array($ch, array(
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-length: ' . Tools::strlen($data),
                'Content-type: application/json;charset=\"utf-8\"'
            )
        ));
        curl_exec($ch);
        curl_close($ch);
    }

    public function hookDisplayTop($params)
    {
        // PrestaShop 1.7 and 1.6 have differences on how they provide information
        // about product, customer, etc. So we normalize those variables in the
        // adroll_* smarty variables to be consistent in the view

        if ($this->context->controller instanceof ProductController) {
            $adrollSegments = 'prestashop_viewed_product';
        } elseif ($this->context->controller instanceof OrderConfirmationController) {
            $adrollSegments = 'prestashop_order_received';
        } elseif ($this->context->controller instanceof CartController) {
            $adrollSegments = 'prestashop_viewed_cart';
        } elseif (version_compare(_PS_VERSION_, '1.7', '>=') && $this->context->controller instanceof OrderController) {
            $adrollSegments = 'prestashop_viewed_checkout';
        } elseif ($this->context->controller instanceof OrderController) {
            if ($this->context->controller->step == 0) {
                $adrollSegments = 'prestashop_viewed_cart';
            } else {
                $adrollSegments = 'prestashop_viewed_checkout';
            }
        } elseif ($this->context->controller instanceof OrderOpcControllerCore) {
            $adrollSegments = 'prestashop_viewed_checkout';
        } elseif ($this->context->controller instanceof ParentOrderController) {
            $adrollSegments = 'prestashop_viewed_checkout';
        } else {
            $adrollSegments = null;
        }

        $this->context->smarty->assign(
            array(
                'adroll_advertisable_id' => $this->getConfiguration('ADROLL_ADVERTISABLE_ID'),
                'adroll_pixel_id' => $this->getConfiguration('ADROLL_PIXEL_ID'),
                'adroll_customer' => $this->context->customer,
                'adroll_product' => $this->context->controller instanceof ProductController ?
                    $this->context->controller->getProduct() :
                    null,
                'adroll_product_group' =>
                    Tools::strtolower($this->context->currency->iso_code)
                    . '_' .
                    $this->context->language->language_code,
                'adroll_order' => $this->context->controller instanceof OrderConfirmationController ?
                    new Order($this->context->controller->id_order) :
                    null,
                'adroll_currency_iso_code' => $this->context->currency->iso_code,
                'adroll_segments' => $adrollSegments
            )
        );
        return $this->display(__FILE__, 'pixel.tpl');
    }

    private function enableWebservice()
    {
        Configuration::updateValue('PS_WEBSERVICE', 1);
    }

    private function getWebserviceKeyPermissions()
    {
        return array(
            'languages' => array('GET'),
            'categories' => array('GET'),
            'combinations' => array('GET'),
            'configurations' => array('GET'),
            'currencies' => array('GET'),
            'products' => array('GET'),
            'specific_prices' => array('GET'),
            'stock_availables' => array('GET')
        );
    }

    private function getWebserviceKeyPermissionsToSet()
    {
        $permissions = array();
        foreach ($this->getWebserviceKeyPermissions() as $resourceName => $methods) {
            $permissions[$resourceName] = array();
            foreach ($methods as $method) {
                $permissions[$resourceName][$method] = 1;
            }
        }
        return $permissions;
    }

    private function setWebserviceKeyToCurrentShop($webserviceKeyId)
    {
        $shopId = Shop::getContextShopID();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT COUNT(*) AS c
            FROM `' . _DB_PREFIX_ . 'webservice_account_shop`
            WHERE `id_webservice_account` = ' . $webserviceKeyId . ' AND
                  `id_shop` = ' . $shopId);

        if ($result[0]['c'] == 0) {
            Db::getInstance()->insert(
                'webservice_account_shop',
                array(
                    'id_webservice_account' => $webserviceKeyId,
                    'id_shop' => $shopId
                ),
                false
            );
        }
    }

    private function getAdrollWebserviceKey()
    {
        $existentKeys = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT `id_webservice_account` AS `id`, `key`, `active`
            FROM `' . _DB_PREFIX_ . 'webservice_account`
            WHERE `description` = "' . self::WEBSERVICE_KEY_DESCRIPTION . '"
            ORDER BY `id_webservice_account` LIMIT 1');

        if (count($existentKeys) > 0) {
            $existentKey = $existentKeys[0];
            if (!$existentKey['active']) {
                $webserviceKey = new WebserviceKey($existentKey['id']);
                $webserviceKey->active = true;
                $webserviceKey->save();
            }
            $this->setWebserviceKeyToCurrentShop($existentKey['id']);
            WebserviceKey::setPermissionForAccount($existentKey['id'], $this->getWebserviceKeyPermissionsToSet());
            return $existentKey['key'];
        }

        $webserviceKey = new WebserviceKey();
        $webserviceKey->key = Tools::strtoupper(Tools::substr(sha1(rand()), 0, 32));
        $webserviceKey->description = self::WEBSERVICE_KEY_DESCRIPTION;
        $webserviceKey->save();
        $this->setWebserviceKeyToCurrentShop($webserviceKey->id);
        WebserviceKey::setPermissionForAccount($webserviceKey->id, $this->getWebserviceKeyPermissionsToSet());

        return $webserviceKey->key;
    }

    public function getContent()
    {
        if (Shop::getContextShopID() === null) {
            $this->context->smarty->assign(
                array(
                    'shop_tree' => Shop::getTree(),
                    'link_base' => $_SERVER['REQUEST_URI']
                )
            );
            return $this->display(__FILE__, 'shop_selector.tpl');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (Tools::getValue('remove', false)) {
                $advertisableId = $this->getConfiguration('ADROLL_ADVERTISABLE_ID');
                $this->updateConfiguration('ADROLL_ADVERTISABLE_ID', null);
                $this->updateConfiguration('ADROLL_PIXEL_ID', null);
                $this->notifyUninstall($advertisableId);
            } else {
                $this->updateConfiguration('ADROLL_ADVERTISABLE_ID', Tools::getValue('adroll_advertisable_id', null));
                $this->updateConfiguration('ADROLL_PIXEL_ID', Tools::getValue('adroll_pixel_id', null));
            }
        }

        $this->enableWebservice();
        $this->context->smarty->assign(
            array(
                'adroll_base_uri' => self::ADROLL_BASE_URI,
                'adroll_advertisable_id' => $this->getConfiguration('ADROLL_ADVERTISABLE_ID'),
                'adroll_pixel_id' => $this->getConfiguration('ADROLL_PIXEL_ID'),
                'shop_host' => Tools::getHttpHost(),
                'webservice_key' => $this->getAdrollWebserviceKey(),
                'shop_id' => Shop::getContextShopID(),
                'form_action' =>
                    AdminController::$currentIndex . '&configure=' .
                    $this->name . '&save' . $this->name .
                    '&token=' . Tools::getAdminTokenLite('AdminModules')
            )
        );

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->context->controller->addCSS('/modules/adroll/views/css/bootstrap.min.css');
        }

        return $this->display(__FILE__, 'settings.tpl');
    }
}

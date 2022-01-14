<?php

if (!defined('_PS_VERSION_')) exit;

require_once('class/CherryCheckoutApi.php');

class CherryCheckout extends Module
{
    private $api;
    private $meInCacheDuration = 60; // minutes
    private $log_prefix        = 'log_presta';

    public function __construct()
    {
        // TODO : logo.png : 57 * 57 px;

        $this->name                   = 'cherrycheckout';
        $this->tab                    = 'checkout';
        $this->version                = '2.0.0';
        $this->author                 = 'Cherry Checkout SA';
        $this->need_instance          = false;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.7');
        $this->bootstrap              = true;

        parent::__construct();

        $this->displayName      = 'Cherry Checkout';
        $this->description      = $this->l('module_description');
        $this->confirmUninstall = $this->l('module_confirm_uninstall');
    }

    public function install()
    {
        $this->_log('Module installation start', 10110);

        // Force the ALL_SHOP Context
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        // Check if there is API settings in configuration
        $apiSettings = $this->_getFromConfig('api_settings');

        // Set the default settings
        if (!$apiSettings || !$apiSettings['publicKey'] || !$apiSettings['privateKey']) {
            $apiSettings = array(
                'api'          => 'https://api.cherrycheckout.com/v2',
                'publicKey'    => '',
                'privateKey'   => '',
                'cancelStatus' => array('6', '7', '8'),
            );
            $this->_saveToConfig('api_settings', $apiSettings);
            $this->_log('Module default api settings saved', 10120);
        }

        if (Shop::isFeatureActive()) {

            $this->_log('Prestashop SHOPS feature is active', 10130);

            $shops = Shop::getShops();

            foreach ($shops as $s) {
                Shop::setContext(Shop::CONTEXT_SHOP, $s['id_shop']);
                try {
                    $this->_init(true);

                    $values                    = $this->_getPrestashopConfigValues();
                    $values['pluginInstalled'] = true;

                    $this->api->updateMe($values);
                    $this->_log('Module installing info sent to api. (Shop ' . $s['id_shop'] . ')', 10140);
                } catch (\Exception $ex) {
                    $this->_log('Unable to send module installing info to api (Shop ' . $s['id_shop'] . ') : ' . $ex->getMessage(), 30140);
                }
            }

            // Reforce the context to all shop
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        // Else, info will be sent on configuration page, no need to send it there


        // Create cherry product and save product settings in configuration
        $productSettings = $this->_createCherryProduct();
        $this->_saveToConfig('product_settings', $productSettings);
        $this->_log('Module product settings saved', 10150);

        if (parent::install() &&
            $this->registerHook('displayShoppingCartFooter') &&
            $this->registerHook('orderConfirmation') &&
            $this->registerHook('updateOrderStatus')) {

            $this->_log('Module installed well', 10160);
            return true;
        }

        $this->_log('Unable to install module', 30160);
        return false;
    }

    public function uninstall()
    {
        $this->_log('Module uninstallation start', 10210);

        // Before uninstalling, we try to make a call to API to notify the uninstall
        $values                    = $this->_getPrestashopConfigValues();
        $values['pluginInstalled'] = false;

        // If multiShop, send uninstalling info to all shops
        if (Shop::isFeatureActive()) {

            $shops = Shop::getShops();

            foreach ($shops as $s) {
                Shop::setContext(Shop::CONTEXT_SHOP, $s['id_shop']);
                try {
                    $this->_init(true);
                    $this->api->updateMe($values);
                    $this->_log('Module uninstalling info sent to api. (Shop ' . $s['id_shop'] . ')', 10220);
                } catch (\Exception $ex) {
                    $this->_log('Unable to send module uninstalling info to api (Shop ' . $s['id_shop'] . ') : ' . $ex->getMessage(), 30220);
                }
            }
        } // Only one shop
        else {
            try {
                $this->_init();
                $this->api->updateMe($values);
                $this->_log('Module uninstalling info sent to api.', 10221);
            } catch (\Exception $ex) {
                $this->_log('Unable to send module uninstalling info to api : ' . $ex->getMessage(), 30221);
            }
        }

        // Force the ALL_SHOP Context
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        // Delete product
        $this->_deleteCherryProduct();

        // /!\ Do not delete api settings
        $this->_deleteConfig('product_settings');
        $this->_log('Module product settings deleted', 10230);
        $this->_deleteConfig('me');
        $this->_log('Module product settings deleted', 10240);

        if (parent::uninstall()) {
            $this->_log('Module uninstalled well', 10250);
            return true;
        }

        $this->_log('Unable to uninstall module', 30250);
        return false;
    }

    public function disable($forceAll = false)
    {
        $this->_log('Module disabling start', 10310);

        $values                    = $this->_getPrestashopConfigValues();
        $values['pluginInstalled'] = false;

        try {
            $this->_init();
            $this->api->updateMe($values);
            $this->_log('Module disabling info sent to api', 10320);
        } catch (\Exception $ex) {
            $this->_log('Unable to send module disabling info to api : ' . $ex->getMessage(), 30320);
        }

        if (parent::disable($forceAll)) {
            $this->_log('Module disabled well', 10330);
            return true;
        }

        $this->_log('Unable to disable module', 30330);
        return false;
    }

    public function enable($forceAll = false)
    {
        $this->_log('Module enabling start', 10410);

        $values                    = $this->_getPrestashopConfigValues();
        $values['pluginInstalled'] = true;

        try {
            $this->_init();
            $this->api->updateMe($values);
            $this->_log('Module enabling info sent to api', 10420);
        } catch (\Exception $ex) {
            $this->_log('Unable to send module enabling info to api : ' . $ex->getMessage(), 30420);
        }

        if (parent::enable($forceAll)) {
            $this->_log('Module enabled well', 10430);
            return true;
        }

        $this->_log('Unable to enable module', 30430);
        return false;
    }


    private function _init($force = false)
    {
        $this->_emptyLogFolder();

        // Module already init, stop there
        if ($this->api && !$force) {
            return;
        }

        $settings = $this->_getFromConfig('api_settings');

        $this->api = new CherryCheckoutApi($settings['api'], $settings['publicKey'], $settings['privateKey']);
    }

    private function _getMe()
    {
        $me = $this->_getFromConfig('me');

        // Check how the me is old
        if ($me) {
            $old          = date_diff(new DateTime(), $me->configurationUpdatedAt);
            $totalMinutes = (($old->y * 365.25 + $old->m * 30 + $old->d) * 24 + $old->h) * 60 + $old->i + $old->s / 60;

            // Set me as obsolete (or not)
            $me->isObsolete = !!($totalMinutes > $this->meInCacheDuration);
        }

        if (!$me || $me->isObsolete) {

            try {
                // Reset me
                $me = $this->resetMe();
            } catch (\Exception $ex) {
                $this->_log('Unable to reset me : ' . $ex->getMessage(), 30510);
            }
        }

        return $me;
    }

    public function resetMe()
    {
        $this->_init();

        try {
            // Get me from API
            $me = $this->api->me(true);

            // Stock me in Configuration
            $me->configurationUpdatedAt = new DateTime();
            $this->_saveToConfig('me', $me);

            // Rename cherry product depending on contract (Donation and/or Contest)
            $this->_renameCherryProduct($me->contract->charityOrderPercentage, $me->contract->gameOrderPercentage);

            return $me;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function getContent()
    {
        $output = '';

        // If multi-shop is active, force user to be on a specific store to configure cherry checkout
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            return $this->displayError($this->l('configuration_multishop_error'));
        }

        // Soumission du formulaire de configuration
        if (Tools::isSubmit('submit' . $this->name) && Tools::getValue('api')) {
            // Get settings
            $api        = trim(Tools::getValue('api'));
            $publicKey  = trim(Tools::getValue('publicKey'));
            $privateKey = trim(Tools::getValue('privateKey'));

            $cancelStatus = array();
            foreach (OrderState::getOrderStates($this->context->language->id) as $v) {
                if (Tools::getValue('cancelStatus_' . $v['id_order_state'])) {
                    $cancelStatus[] = $v['id_order_state'];
                }
            }

            // Save settings
            $settings = array(
                'api'          => $api,
                'publicKey'    => $publicKey,
                'privateKey'   => $privateKey,
                'cancelStatus' => $cancelStatus,
            );

            $this->_saveToConfig('api_settings', $settings);
            $this->_log('Settings reset : ' . json_encode($settings), 10610);
        }

        // Try to get a /me, to check if settings are good
        try {
            $this->_init();

            // Get me from API
            $me = $this->resetMe();

            if (!$me) {
                throw new Exception('Me is undefined');
            }

            // Stock me in Configuration
            $me->configurationUpdatedAt = new DateTime();
            $this->_saveToConfig('me', $me);

            $output .= $this->displayConfirmation($this->l('configuration_seems_good'));

            // If we are in test mode, show it
            if ($me->mode == 'test' && !_PS_MODE_DEV_) {
                $output .= $this->displayError($this->l('configuration_mode_test'));
            } else if ($me->mode == 'prod' && _PS_MODE_DEV_) {
                $output .= $this->displayError($this->l('configuration_mode_prod'));
            }

            // Send Prestashop information to API
            try {
                $values                        = $this->_getPrestashopConfigValues();
                $values['pluginInstalled']     = true;
                $values['pluginInstalledMode'] = $me->mode;
                $this->api->updateMe($values);
            } catch (\Exception $ex) {
                $this->_log('Unable to send prestashop info to api : ' . $ex->getMessage(), 30620);
            }
        } catch (\Exception $ex) {
            $this->_deleteConfig('me');
            $this->_log('Configuration is wrong : ' . $ex->getMessage(), 30630);
            $output .= $this->displayError($this->l('configuration_seems_wrong'));
        }

        $output .= $this->_displayForm();
        return $output;
    }

    private function _getPrestashopConfigValues()
    {
        return array(
            'cms'                    => 'prestashop',
            'cmsVersion'             => _PS_VERSION_,
            'pluginInstalledVersion' => $this->version,
            'moduleApiEndpoint'      => _PS_BASE_URL_ . _MODULE_DIR_ . $this->name . '/webservice.php'
        );
    }

    private function _displayForm()
    {
        $orderStatus = array();

        foreach (OrderState::getOrderStates($this->context->language->id) as $os) {
            array_push($orderStatus, array(
                'id'   => (int) $os['id_order_state'],
                'name' => $os['name'],
            )
            );
        }

        $settingsForm[0]['form'] = array(
            'legend'      => array(
                'title' => $this->l('settings'),
                'icon'  => 'icon-cogs'
            ),
            'description' => $this->l('configuration_help_to_obtain_api_keys') . ' <a href="' . $this->l('doc_prestashop_href') . '" target="_blank">developers.cherrycheckout.com</a><br/><br/>' .
                $this->l('configuration_help_for_invoices_and_stats') . ' <a href="https://dashboard.cherrycheckout.com" target="_blank">dashboard.cherrycheckout.com</a>',
            'input'       => array(
                array(
                    'type'     => 'text',
                    'label'    => $this->l('configuration_label_api'),
                    'name'     => 'api',
                    'required' => true,
                    'size'     => 50
                ),
                array(
                    'type'        => 'text',
                    'label'       => $this->l('configuration_label_public_key'),
                    'name'        => 'publicKey',
                    'required'    => true,
                    'placeholder' => 'pk_',
                    'size'        => 50
                ),
                array(
                    'type'        => 'text',
                    'label'       => $this->l('configuration_label_private_key'),
                    'name'        => 'privateKey',
                    'required'    => true,
                    'placeholder' => 'sk_',
                    'size'        => 50
                ),
                array(
                    'type'   => 'checkbox',
                    'label'  => $this->l('configuration_label_order_status_cancel'),
                    'name'   => 'cancelStatus',
                    'values' => array(
                        'query' => $orderStatus,
                        'id'    => 'id',
                        'name'  => 'name'
                    )
                ),
            ),
            'submit'      => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );

        $helper                        = new HelperForm();
        $helper->module                = $this;
        $helper->name_controller       = $this->name;
        $helper->token                 = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex          = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = $helper->allow_employee_form_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->title                 = $this->displayName;
        $helper->show_toolbar          = true;
        $helper->toolbar_scroll        = true;
        $helper->submit_action         = 'submit' . $this->name;
        $helper->toolbar_btn           = array(
            'save' => array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '$configure=' . $this->name . '&save' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules')
            ),
            'back' => array(
                'desc' => $this->l('Back'),
                'href' => AdminController::$currentIndex . '$configure=' . $this->name . '&save' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules')
            )
        );

        // Get settings
        $settings = $this->_getFromConfig('api_settings');

        // Set API values
        $helper->fields_value['api']        = isset($settings['api']) ? $settings['api'] : '';
        $helper->fields_value['publicKey']  = isset($settings['publicKey']) ? $settings['publicKey'] : '';
        $helper->fields_value['privateKey'] = isset($settings['privateKey']) ? $settings['privateKey'] : '';

        // Set order cancel status values
        if ($settings['cancelStatus']) {
            foreach ($settings['cancelStatus'] as $cancelStatusId) {
                $helper->fields_value['cancelStatus_' . $cancelStatusId] = true;
            }
        }

        return $helper->generateForm($settingsForm);
    }


    /**
     * HOOKS
     */
    public function hookDisplayShoppingCartFooter($params)
    {
        try {
            $view = '';
            $this->_init();

            // Test if config is correct
            $me = $this->_getMe();
            if (!$me) {
                return '';
            }

            $productSettings = $this->_getFromConfig('product_settings');
            $cherryProduct   = new Product($productSettings['productId']);

            // Calculate the minimal cherries required
            $minimalCherries = $this->_getMinimalCherriesRequiredAccordingToCart($this->context->cart);
            $maximalCherries = $this->_getMaximalCherriesAccordingToCart($this->context->cart);

            // Get the cherries already in cart
            $cherriesFromCart = $this->context->cart->getProducts(false, $cherryProduct->id);

            // When customer add a cherry, or there is already cherry in cart, but quantity is not enough
            if ((Tools::isSubmit('cherrycheckout_submit') && !count($cherriesFromCart)) || (count($cherriesFromCart) && ($cherriesFromCart[0]['cart_quantity'] < $minimalCherries || $cherriesFromCart[0]['cart_quantity'] > $maximalCherries))) {
                // Add cherries in the cart
                $this->context->cart->deleteProduct($cherryProduct->id);
                $this->context->cart->updateQty($minimalCherries, $cherryProduct->id);
                $view .= '<script type="text/javascript">window.location = window.location.href;</script>';
            } // If we have no cherries added, show the plugin
            else if (!count($cherriesFromCart)) {
                $view .= $this->_getPluginContent($minimalCherries);
            }

            return $view;
        } catch (\Exception $ex) {
            $this->_log('[hookDisplayShoppingCartFooter] Error : ' . $ex->getMessage(), 31100);
            return '';
        }
    }

    public function hookOrderConfirmation($params)
    {
        try {
            // Check if there is a cherry product
            $productSettings = $this->_getFromConfig('product_settings');
            $cart            = $this->context->cart->getCartByOrderId($this->context->controller->id_order);
            $cherryProduct   = $cart->getProducts(false, $productSettings['productId']);

            // No cherry product, stop there
            if (!count($cherryProduct)) {
                return '';
            }

            $this->_init();

            // Test if config is correct
            $me = $this->_getMe();
            if (!$me) {
                return '';
            }

            // Create order to create
            $order = (isset($params['order'])) ? $params['order'] : $params['objOrder'];

            if (!$order) {
                throw new Exception('Unable to parse order');
            }

            $currency = (isset($params['currencyObj'])) ? $params['currencyObj'] : new Currency($order->id_currency);

            if (!$currency) {
                throw new Exception('Unable to parse currency');
            }

            $cherryPrice = (int) $cherryProduct[0]['cart_quantity'];

            $cherryApiOrder = array(
                'customer'    => array(
                    'firstname' => $this->context->customer->firstname,
                    'lastname'  => $this->context->customer->lastname,
                    'email'     => $this->context->customer->email,
                ),
                'cherryPrice' => $cherryPrice,
                'orderPrice'  => (float) ($order->total_paid - $cherryPrice),
                'currency'    => $currency->iso_code,
                'trackId'     => $order->reference
            );

            // Get customer gender
            if ($this->context->customer->id_gender) {
                $gender = new Gender((int) $this->context->customer->id_gender);
                switch ($gender->type) {
                    case 0:
                        $cherryApiOrder['customer']['gender'] = 'm';
                        break;
                    case 1:
                        $cherryApiOrder['customer']['gender'] = 'f';
                        break;
                }
            }

            // Get customer birthday
            if ($this->context->customer->birthday && $this->context->customer->birthday != '0000-00-00') {
                $cherryApiOrder['customer']['birthday'] = $this->context->customer->birthday;
            }

            $this->api->createOrder($cherryApiOrder);
        } catch (\Exception $ex) {
            $this->_log('[hookOrderConfirmation] Error : ' . $ex->getMessage(), 31200);
        }

        return '';
    }

    public function hookUpdateOrderStatus($params)
    {
        try {
            $productSettings  = $this->_getFromConfig('product_settings');
            $apiSettings      = $this->_getFromConfig('api_settings');
            $cancelableStatus = $apiSettings['cancelStatus'];

            // Check if there is a cherry product
            $cart = $params['cart'];
            if (!$cart) return;
            $cherryProduct = $cart->getProducts(false, $productSettings['productId']);

            // No cherry product, stop there
            if (!count($cherryProduct)) {
                return '';
            }

            $this->_init();

            // Test if config is correct
            $me = $this->_getMe();
            if (!$me) {
                return '';
            }

            // Check if old status is a cancelable status, and new one is not
            $oldOrder  = new Order($params['id_order']);
            $oldStatus = $oldOrder->current_state;
            $newStatus = $params['newOrderStatus']->id;

            // Status is the same between old and new order, stop there
            if (in_array($oldStatus, $cancelableStatus) == in_array($newStatus, $cancelableStatus)) {
                return '';
            }

            $statusApi = in_array($newStatus, $cancelableStatus) ? 'canceled' : 'validated';

            $cherryApiOrder = array(
                'status' => $statusApi
            );

            $this->api->updateOrder($oldOrder->reference, $cherryApiOrder);
        } catch (\Exception $ex) {
            $this->_log('[hookUpdateOrderStatus] Error : ' . $ex->getMessage(), 31300);
        }

        return '';
    }


    private function _createCherryProduct()
    {
        // 1.1 Create Tax for cherry checkout
        $tax                                     = new Tax();
        $tax->name[$this->context->language->id] = 'TVA Cherrycheckout 0%';
        $tax->rate                               = 0;
        $tax->active                             = 1;
        $tax->add();

        // 1.2 Create the Tax Rule Group
        $trg         = new TaxRulesGroup();
        $trg->name   = 'TVA Cherrycheckout (0%)';
        $trg->active = 1;
        $trg->add();

        // 1.3 Create the Tax Rule
        $tr                     = new TaxRule();
        $tr->id_tax_rules_group = $trg->id;
        $tr->id_country         = $this->context->country->id;
        $tr->description        = '';
        $tr->id_tax             = $tax->id;
        $tr->add();

        // 2. Create a hidden Category
        $cat                                             = new Category();
        $cat->name[$this->context->language->id]         = 'Cherry Checkout';
        $cat->id_parent                                  = 2;
        $cat->is_root_category                           = false;
        $cat->active                                     = 0;
        $cat->link_rewrite[$this->context->language->id] = "cherry-checkout";
        $cat->add();

        // 3. Create the Product, in the category, attached to the tax
        $product                                             = new Product();
        $product->active                                     = 1;
        $product->link_rewrite[$this->context->language->id] = 'cherry-checkout-product';
        $product->name[$this->context->language->id]         = 'Cherry Checkout'; // TODO : Add donation/contest attributes
        $product->id_category_default                        = $cat->id;
        $product->visibility                                 = 'none';
        $product->id_tax_rules_group                         = $trg->id;
        $product->price                                      = 1;
        $product->save();

        $product->addToCategories($cat->id);

        // Add image to product
        $image             = new Image();
        $image->id_product = $product->id;
        $image->position   = 1;
        $image->cover      = true;
        if ($image->add()) {
            $this->_copyCherryProductImage($image->id);
        }

        // Add quantity for all shops
        $shops = Shop::getShops();
        foreach ($shops as $s) {
            StockAvailable::setQuantity($product->id, 0, 9999999, $s['id_shop']);
            StockAvailable::setProductOutOfStock($product->id, 1, $s['id_shop']); // Enable the purchase, even if quantity is 0
        }

        // Create specific prices for each currencies, to prevent devise conversion
        $currencies = Currency::getCurrencies();
        foreach ($currencies as $currency) {
            $specificPrice                       = new SpecificPrice();
            $specificPrice->id_product           = (int) $product->id;
            $specificPrice->id_product_attribute = 0;
            $specificPrice->id_shop              = 0;
            $specificPrice->id_currency          = $currency['id_currency'];
            $specificPrice->id_country           = 0;
            $specificPrice->id_group             = 0;
            $specificPrice->id_customer          = 0;
            $specificPrice->price                = 1;
            $specificPrice->from_quantity        = 1;
            $specificPrice->reduction            = 0;
            $specificPrice->reduction_tax        = "1";
            $specificPrice->reduction_type       = "amount";
            $specificPrice->from                 = "0000-00-00 00:00:00";
            $specificPrice->to                   = "0000-00-00 00:00:00";
            $specificPrice->add();
        }

        $productSettings = array(
            'taxId'           => $tax->id,
            'taxRulesGroupId' => $trg->id,
            'taxRuleId'       => $tr->id,
            'categoryId'      => $cat->id,
            'productId'       => $product->id
        );

        return $productSettings;
    }

    private function _copyCherryProductImage($imageId)
    {
        $originalImageUrl = _PS_ROOT_DIR_ . '/modules/cherrycheckout/cherryProductImage.jpg'; // TODO : check image dimensions for a product
        $tmpFile          = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');

        $image     = new Image($imageId);
        $imagePath = $image->getPathForCreation();

        if (!ImageManager::checkImageMemoryLimit($originalImageUrl)) {
            return false;
        }

        // Method 1
        if (@copy($originalImageUrl, $tmpFile)) {

        } // Method 2
        elseif ($content = Tools::file_get_contents($originalImageUrl)) {
            $fp = fopen($tmpFile, 'w');
            fwrite($fp, $content);
            fclose($fp);
        } // Didn't work...
        else {
            unlink($tmpFile);
            return false;
        }

        ImageManager::resize($tmpFile, $imagePath . '.jpg');
        $imageTypes = ImageType::getImagesTypes('products');
        foreach ($imageTypes as $imageType) {
            ImageManager::resize($tmpFile, $imagePath . '-' . Tools::stripslashes($imageType['name']) . '.jpg', $imageType['width'], $imageType['height']);
        }

        // Remove tmpFile
        unlink($tmpFile);
        return true;
    }

    private function _renameCherryProduct($charityPart, $gamePart)
    {
        // Get Cherry Product
        $productSettings = $this->_getFromConfig('product_settings');
        $product         = new Product($productSettings['productId']);
        $languages       = Language::getLanguages(true);

        foreach ($languages as $language) {

            $productName = 'Cherry Checkout';

            if ($charityPart > 0 && $gamePart > 0) {
                switch ($language['iso_code']) {
                    case 'fr':
                        $productName = 'Cherry Checkout - Don et Concours';
                        break;
                    default:
                        $productName = 'Cherry Checkout - Donation and Contest';
                        break;
                }
            } else if ($charityPart > 0 && $gamePart <= 0) {
                switch ($language['iso_code']) {
                    case 'fr':
                        $productName = 'Cherry Checkout - Don';
                        break;
                    default:
                        $productName = 'Cherry Checkout - Donation';
                        break;
                }
            } else if ($charityPart <= 0 && $gamePart > 0) {
                switch ($language['iso_code']) {
                    case 'fr':
                        $productName = 'Cherry Checkout - Concours';
                        break;
                    default:
                        $productName = 'Cherry Checkout - Contest';
                        break;
                }
            }

            $product->name[$language['id_lang']] = $productName;
        }

        $product->save();
    }

    private function _deleteCherryProduct()
    {
        // Get IDs of tax/category/product to delete them
        $productSettings = $this->_getFromConfig('product_settings');

        // 1. Delete the Product
        $product = new Product($productSettings['productId']);
        $product->delete();

        // 2. Delete the Category
        $cat = new Category($productSettings['categoryId']);
        $cat->delete();

        // 3.1 Delete the Tax Rule
        $tr = new TaxRule($productSettings['taxRuleId']);
        $tr->delete();

        // 3.2 Delete the Tax Rule Group
        $trg = new TaxRulesGroup($productSettings['taxRulesGroupId']);
        $trg->delete();

        // 3.3 Delete the Tax
        $tax = new Tax($productSettings['taxId']);
        $tax->delete();
    }

    private function _getCartAmountWithoutCherries($cart)
    {
        $products        = $cart->getProducts(true);
        $productSettings = $this->_getFromConfig('product_settings');

        $cartAmount = $cart->getOrderTotal(true);

        // Subtract cherries
        foreach ($products as $product) {
            if ($product['id_product'] == $productSettings['productId']) {
                $cartAmount -= (float) $product['total_wt'];
            }
        }

        return (float) $cartAmount;
    }

    private function _getMinimalCherriesRequiredAccordingToCart($cart)
    {
        $me         = $this->_getMe();
        $priceScale = $me->contract->priceScale;
        $cartAmount = $this->_getCartAmountWithoutCherries($cart);

        foreach ($priceScale as $ps) {
            if (($cartAmount > (float) ($ps->from)) && $cartAmount <= (float) ($ps->to)) {
                return (int) $ps->amount;
            }
        }

        return (int) $priceScale[0]->amount;
    }

    private function _getMaximalCherriesAccordingToCart($cart)
    {
        $me = $this->_getMe();

        if (isset($me->allowCharityExtraPart)) {
            $allowCharityExtraPart = !!$me->allowCharityExtraPart;

            if (!$allowCharityExtraPart) {
                return $this->_getMinimalCherriesRequiredAccordingToCart($cart);
            }
        }

        return 1000;
    }

    private function _getPluginContent($minimalCherries)
    {
        $me = $this->_getMe();

        // Get the plugin in the customer language if possible
        $plugin = (array) $me->plugin;
        $plugin = isset($plugin[$this->context->language->iso_code]) ? $plugin[$this->context->language->iso_code] : $plugin['en'];

        // Insert the good price in the cherrycheckout form button
        $plugin = str_replace('{cherryPrice}', number_format((float) ($minimalCherries), 2) . ' ' . $this->context->currency->iso_code, $plugin);

        return $plugin;
    }


    private function _saveToConfig($configName, $value)
    {
        // For product settings, save it as global, for all shops
        if ($configName == 'product_settings') {
            Configuration::updateGlobalValue($this->name . '_' . $configName, base64_encode(serialize($value)));
        } else {
            Configuration::updateValue($this->name . '_' . $configName, base64_encode(serialize($value)));
        }
    }

    private function _getFromConfig($configName)
    {
        return unserialize(base64_decode(Configuration::get($this->name . '_' . $configName)));
    }

    private function _deleteConfig($configName)
    {
        Configuration::deleteByName($this->name . '_' . $configName);
    }


    private function _log($message, $errorCode = 0)
    {
        $severity = ($errorCode >= 3000) ? 2 : 1;

        if (version_compare(_PS_VERSION_, '1.6', '>=')) {
            PrestaShopLogger::addLog($message, $severity, $errorCode, $this->name, 1, true);
        } else {
            Logger::addLog($message, $severity, $errorCode, $this->name, 1, true);
        }
    }

    public function generateLogFile()
    {
        try {
            // Create a file
            $filename = _PS_MODULE_DIR_ . $this->name . '/log/' . $this->log_prefix . '_' . date('Y-m-d-H-i-s') . '.txt';

            if ($file = fopen($filename, 'w')) {
                $separator = "------------------------------------------------------------------\r\n";

                // Create summary
                fwrite($file, $separator);
                fwrite($file, "-- \t CherryCheckout Module for Prestashop " . _PS_VERSION_ . "\r\n");
                fwrite($file, "-- \r\n");
                fwrite($file, "-- \t Module :\t\t" . $this->name . "\r\n");
                fwrite($file, "-- \t Version :\t\t" . $this->version . "\r\n");
                fwrite($file, "-- \t Author :\t\t" . $this->author . "\r\n");
                fwrite($file, "-- \t Generated at :\t\t" . date('d/m/Y H:i:s') . "\r\n");
                fwrite($file, $separator);
                fwrite($file, "\r\n");

                // Get all logs from this module
                $db    = Db::getInstance();
                $query = 'SELECT error_code,message,date_add FROM ' . _DB_PREFIX_ . 'log WHERE object_type="' . $this->name . '" AND object_id=1 ORDER BY id_log DESC LIMIT 250;';
                if ($results = $db->executeS($query, true, false)) {
                    foreach ($results as $result) {
                        $line = '[' . $result['date_add'] . '] ' . $result['message'] . ' (ERROR_CODE: ' . $result['error_code'] . ')';
                        fwrite($file, $line . "\r\n");
                    }
                }

                fwrite($file, "\r\n");
                fwrite($file, $separator);
                fclose($file);
                return str_replace(_PS_MODULE_DIR_, _PS_BASE_URL_ . _MODULE_DIR_, $filename);
            } else {
                throw new \Exception('Unable to create log file');
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    private function _emptyLogFolder()
    {
        try {
            $files = scandir(_PS_MODULE_DIR_ . $this->name . '/log');

            if (!count($files)) {
                return true;
            }

            foreach ($files as $file) {

                // Check if file is a log file
                if (strpos($file, $this->log_prefix) !== false) {
                    $fileDate = substr(str_replace($this->log_prefix . '_', '', $file), 0, 10);

                    // If file is older than today, delete if
                    if ($fileDate < date('Y-m-d')) {
                        unlink(_PS_MODULE_DIR_ . $this->name . '/log/' . $file);
                    }
                }
            }

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }


    public function getApiKeys()
    {
        $settings = $this->_getFromConfig('api_settings');
        return array(
            'publicKey'  => $settings['publicKey'],
            'privateKey' => $settings['privateKey']
        );
    }

}

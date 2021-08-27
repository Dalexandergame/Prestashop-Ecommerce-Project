<?php
/**
 * Advanced Pack 5
 *
 * @author    Presta-Module.com <support@presta-module.com> - https://www.presta-module.com
 * @copyright Presta-Module 2021 - https://www.presta-module.com
 * @license   Commercial
 * @version   5.2.14
 *
 *           ____     __  __
 *          |  _ \   |  \/  |
 *          | |_) |  | |\/| |
 *          |  __/   | |  | |
 *          |_|      |_|  |_|
 */

if (!defined('_PS_VERSION_')) {
    exit;
}
include_once _PS_ROOT_DIR_ . '/modules/pm_advancedpack/AdvancedPack.php';
include_once _PS_ROOT_DIR_ . '/modules/pm_advancedpack/AdvancedPackCoreClass.php';
class pm_advancedpack extends AdvancedPackCoreClass
{
    const DEBUG = false;
    const PACK_CONTENT_SHOPPING_CART = 1;
    const PACK_CONTENT_BLOCK_CART = 2;
    const PACK_CONTENT_ORDER_CONFIRMATION_EMAIL = 3;
    const DISPLAY_SIMPLE = 'simple';
    const DISPLAY_ADVANCED = 'advanced';
    public static $_preventInfiniteLoop = false;
    public static $_preventCartSaveHook = false;
    public static $_preventUpdateQuantityCompleteHook = false;
    public static $_validateOrderProcess = false;
    public static $_addOutOfStockOrderHistory = false;
    public static $_updateQuantityProcess = false;
    public static $_productListQuantityToUpdate = array();
    public static $currentStockUpdate = array();
    public static $actionValidateOrderProcessing = false;
    public static $actionProductListModifierProcessing = false;
    public static $modulePrefix = 'AP5';
    protected $_defaultConfiguration = array(
        'displayMode' => 'advanced',
        'bootstrapTheme' => false,
        'enablePackCrossSellingBlock' => true,
        'limitPackNbCrossSelling' => 0,
        'orderByCrossSelling' => 'date_add_asc',
        'showImagesOnlyForCombinations' => false,
        'enableViewThisPackButton' => true,
        'viewThisPackButtonBackgroundColor' => array('#4ea870', '#4ea870'),
        'viewThisPackButtonFontColor' => '#ffffff',
        'enableBuyThisPackButton' => true,
        'buyThisPackButtonBackgroundColor' => array('#009ad0', '#009ad0'),
        'buyThisPackButtonFontColor' => '#ffffff',
        'showProductsThumbnails' => true,
        'showProductsPrice' => true,
        'showProductsAvailability' => false,
        'showProductsFeatures' => true,
        'showProductsShortDescription' => true,
        'showProductsLongDescription' => true,
        'showProductsQuantityWanted' => false,
        'autoScrollBuyBlock' => true,
        'tabActiveBackgroundColor' => array('#009ad0', '#007ab7'),
        'tabActiveFontColor' => '#ffffff',
        'tabActiveBorderColor' => '#0079b6',
        'ribbonBackgroundColor' => array('#1899cf', '#127bb8'),
        'ribbonFontColor' => '#ffffff',
        'ribbonBorderColor' => '#009ad0',
        'iconPlusFontColor' => '#000000',
        'iconRemoveFontColor' => '#000000',
        'iconCheckFontColor' => '#000000',
        'imageFormatProductZoom' => array('thickbox', 'default'),
        'imageFormatProductCover' => array('home', 'default'),
        'imageFormatProductCoverMobile' => array('home', 'default'),
        'imageFormatProductSlideshow' => array('cart', 'default'),
        'imageFormatProductFooterCover' => array('medium', 'default'),
        'addPrefixToOrderDetail' => true,
        'dynamicPriceCalculation' => true,
        'postponeUpdatePackSpecificPrice' => false,
    );
    protected $_cssMapTable = array(
        'tabActiveBackgroundColor' => array(
            array(
                'type' => 'bg_gradient',
                'selector' => '#ap5-pack-product-tab-list > li.active > a',
            ),
        ),
        'tabActiveFontColor' => array(
            array(
                'type' => 'color',
                'selector' => '#ap5-pack-product-tab-list > li.active > a',
            ),
        ),
        'tabActiveBorderColor' => array(
            array(
                'type' => 'border_color',
                'selector' => '#ap5-pack-product-tab-list > li.active > a',
            ),
        ),
        'ribbonBackgroundColor' => array(
            array(
                'type' => 'bg_gradient',
                'selector' => '.ap5-pack-product-content .ribbon',
            ),
            array(
                'type' => 'keyframes_spin',
                'selector' => 'keyframes_spin',
            ),
        ),
        'ribbonFontColor' => array(
            array(
                'type' => 'color',
                'selector' => '.ap5-pack-product-content .ribbon',
            ),
        ),
        'ribbonBorderColor' => array(
            array(
                'type' => 'border_top_color',
                'selector' => '.ap5-pack-product-content .ribbon:before, .ap5-pack-product-content .ribbon:after',
            ),
        ),
        'iconPlusFontColor' => array(
            array(
                'type' => 'color',
                'selector' => '.ap5-pack-product .ap5-pack-product-icon-plus:before',
            ),
        ),
        'iconRemoveFontColor' => array(
            array(
                'type' => 'color',
                'selector' => '.ap5-pack-product:hover .ap5-pack-product-icon-remove:after',
            ),
        ),
        'iconCheckFontColor' => array(
            array(
                'type' => 'color',
                'selector' => '.ap5-is-excluded-product .ap5-pack-product-icon-check:after',
            ),
        ),
        'viewThisPackButtonFontColor' => array(
            array(
                'type' => 'color',
                'selector' => '.ap5-product-footer-pack-name a span.ap5-view-pack-button',
            ),
        ),
        'viewThisPackButtonBackgroundColor' => array(
            array(
                'type' => 'bg_gradient',
                'selector' => '.ap5-product-footer-pack-name a span.ap5-view-pack-button',
            ),
        ),
        'buyThisPackButtonFontColor' => array(
            array(
                'type' => 'color',
                'selector' => '.ap5-product-footer-pack-name a span.ap5-buy-pack-button',
            ),
        ),
        'buyThisPackButtonBackgroundColor' => array(
            array(
                'type' => 'bg_gradient',
                'selector' => '.ap5-product-footer-pack-name a span.ap5-buy-pack-button',
            ),
        ),
    );
    protected $_file_to_check = array('views/css');
    public function __construct()
    {
        $this->need_instance = 0;
        $this->name = 'pm_advancedpack';
        $this->module_key = '7e2464eca3e8dc2d1a5a7e93da1d82b4';
        $this->author = 'Presta-Module';
        $this->tab = 'pricing_promotion';
        $this->version = '5.2.14';
        $this->displayName = 'Advanced Pack';
        $this->bootstrap = true;
        $this->description = $this->l('Add a product bundling strategy into your store, sell more !');
        parent::__construct();
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
            $this->_defaultConfiguration['imageFormatProductSlideshow'] = array('small', 'default');
            $this->_defaultConfiguration['imageFormatProductFooterCover'] = array('cart', 'default');
            $this->_defaultConfiguration['ribbonBackgroundColor'] = array('#2fb5d2', '#2fb5d2');
            unset($this->_cssMapTable['ribbonBorderColor']);
            unset($this->_cssMapTable['tabActiveBackgroundColor']);
            unset($this->_cssMapTable['tabActiveFontColor']);
            unset($this->_cssMapTable['tabActiveBorderColor']);
            unset($this->_cssMapTable['viewThisPackButtonFontColor']);
            unset($this->_cssMapTable['viewThisPackButtonBackgroundColor']);
            unset($this->_cssMapTable['buyThisPackButtonFontColor']);
            unset($this->_cssMapTable['buyThisPackButtonBackgroundColor']);
        }
        foreach (array('imageFormatProductZoom', 'imageFormatProductCover', 'imageFormatProductCoverMobile', 'imageFormatProductSlideshow', 'imageFormatProductFooterCover') as $k) {
            $this->_defaultConfiguration[$k] = implode('_', $this->_defaultConfiguration[$k]);
        }
    }
    public function install()
    {
        if (!parent::install()
            || !$this->installDatabase()
            || !$this->registerHook('displayHeader')
            || (version_compare(_PS_VERSION_, '1.7.0.0', '<') && !$this->registerHook('displayFooter'))
            || !$this->registerHook('displayFooterProduct')
            || !$this->registerHook('actionValidateOrder')
            || !$this->registerHook('moduleRoutes')
            || !$this->registerHook('displayOverrideTemplate')
            || !$this->registerHook('actionProductAdd')
            || !$this->registerHook('actionProductUpdate')
            || !$this->registerHook('actionProductDelete')
            || !$this->registerHook('actionCartSave')
            || !$this->registerHook('actionBeforeCartUpdateQty')
            || !$this->registerHook('displayShoppingCartFooter')
            || !$this->registerHook('actionObjectOrderAddAfter')
            || !$this->registerHook('actionObjectOrderUpdateAfter')
            || !$this->registerHook('actionObjectSpecificPriceAddAfter')
            || !$this->registerHook('actionObjectCombinationDeleteAfter')
            || !$this->registerHook('actionUpdateQuantity')
            || (version_compare(_PS_VERSION_, '1.7.0.0', '<') && !$this->registerHook('displayRightColumnProduct'))
            || !$this->registerHook('actionShopDataDuplication')
            || !$this->registerHook('actionObjectSpecificPriceDeleteAfter')
            || !$this->registerHook('actionProductListModifier')
            || !$this->registerHook('displayBackOfficeHeader')
            || (version_compare(_PS_VERSION_, '1.7.0.0', '<') && !$this->registerHook('displayAdminProductsExtra'))
            || !$this->registerHook('actionAdminControllerSetMedia')
            || (version_compare(_PS_VERSION_, '1.6.0.2', '>=') && !$this->registerHook('actionAdminProductsListingResultsModifier'))
            || (version_compare(_PS_VERSION_, '1.7.1.0', '>=') && !$this->registerHook('actionGetProductPropertiesAfter'))
            || (version_compare(_PS_VERSION_, '1.7.1.0', '>=') && !$this->registerHook('displayBeforeBodyClosingTag'))
            || (version_compare(_PS_VERSION_, '1.7.1.0', '>=') && !$this->registerHook('displayProductAdditionalInfo'))
            || (version_compare(_PS_VERSION_, '1.7.1.0', '>=') && !$this->registerHook('actionProductSave'))
            || !$this->_addCustomAttributeGroup()
            || !$this->_addAdminTab()
            || !$this->_updateModulePosition()
        ) {
            return false;
        }
        Configuration::updateGlobalValue('PS_SPECIFIC_PRICE_FEATURE_ACTIVE', '1');
        Configuration::updateGlobalValue('PS_COMBINATION_FEATURE_ACTIVE', '1');
        $this->_checkIfModuleIsUpdate(true, false, true);
        return true;
    }
    public function registerNewHooks($previous_version, $version)
    {
        $res = true;
        if (version_compare($previous_version, '5.1.0', '<')) {
            $res &= $this->registerHook('displayRightColumnProduct');
        }
        if (version_compare($previous_version, '5.1.1', '<')) {
            $res &= $this->registerHook('actionShopDataDuplication');
            $res &= $this->registerHook('actionObjectSpecificPriceDeleteAfter');
        }
        if (version_compare($previous_version, '5.1.3', '<')) {
            $res &= $this->registerHook('actionProductListModifier');
        }
        if (version_compare($previous_version, '5.2.0', '<')) {
            if (version_compare(_PS_VERSION_, '1.7.1.0', '>=')) {
                $this->registerHook('actionGetProductPropertiesAfter');
                $this->registerHook('displayBeforeBodyClosingTag');
                $this->registerHook('displayProductAdditionalInfo');
                $this->registerHook('actionProductSave');
            }
        }
        return $res;
    }
    private function _updateModulePosition()
    {
        $res = true;
        $hookList = array('displayFooterProduct', 'actionValidateOrder', 'displayRightColumnProduct', 'displayProductAdditionalInfo');
        foreach ($hookList as $hookName) {
            $idHook = Hook::getIdByName($hookName);
            if ($idHook) {
                foreach (Shop::getContextListShopID() as $idShop) {
                    $res &= Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'hook_module`
                        SET `position`=0
                        WHERE `id_module` = '.(int)$this->id.'
                        AND `id_hook` = '.(int)$idHook.' AND `id_shop` = '.$idShop);
                }
                $res &= $this->cleanPositions($idHook, Shop::getContextListShopID());
            }
        }
        if (!$res) {
            $this->context->controller->errors[] = $this->displayName . ' - ' . $this->l('Unable to update module position for hook right & left column');
        }
        return $res;
    }
    private function _addAdminTab()
    {
        $res = true;
        if (!Validate::isLoadedObject(Tab::getInstanceFromClassName('AdminPacks'))) {
            $adminTab = new Tab();
            foreach (Language::getLanguages(false) as $lang) {
                $adminTab->name[(int)$lang['id_lang']] = $this->l('Packs');
            }
            $adminTab->class_name = 'AdminPacks';
            $adminTab->id_parent = Tab::getInstanceFromClassName('AdminProducts')->id_parent;
            $adminTab->module = $this->name;
            $res &= $adminTab->add();
            $res &= $adminTab->updatePosition('l', 2);
        }
        if (!$res) {
            $this->context->controller->errors[] = $this->displayName . ' - ' . $this->l('Unable to add AdminTab "AdminPacks"');
        }
        return $res;
    }
    private function _addCustomAttributeGroup()
    {
        Configuration::updateValue('PS_COMBINATION_FEATURE_ACTIVE', true);
        $alreadyExists = (AdvancedPack::getPackAttributeGroupId() !== false);
        if (!$alreadyExists) {
            $attributeGroupObj = new AttributeGroup();
            $attributeGroupObj->is_color_group = false;
            $attributeGroupObj->group_type = 'select';
            foreach (Language::getLanguages(false) as $lang) {
                $attributeGroupObj->name[$lang['id_lang']] = 'AP5-Pack';
                $isoCode = Tools::strtolower($lang['iso_code']);
                if (in_array($isoCode, array('fr', 'be', 'lu'))) {
                    $attributeGroupObj->public_name[$lang['id_lang']] = 'Contenu du pack';
                } elseif (in_array($isoCode, array('es', 'ar', 'mx'))) {
                    $attributeGroupObj->public_name[$lang['id_lang']] = 'Contenido del pack';
                } elseif ($isoCode == 'it') {
                    $attributeGroupObj->public_name[$lang['id_lang']] = 'Contenuto della pacchetto';
                } elseif ($isoCode == 'nl') {
                    $attributeGroupObj->public_name[$lang['id_lang']] = 'Pak inhoud';
                } elseif ($isoCode == 'dk') {
                    $attributeGroupObj->public_name[$lang['id_lang']] = 'Pack indhold';
                } elseif (in_array($isoCode, array('de', 'at'))) {
                    $attributeGroupObj->public_name[$lang['id_lang']] = 'Packungsinhalt';
                } elseif (in_array($isoCode, array('pt', 'br'))) {
                    $attributeGroupObj->public_name[$lang['id_lang']] = 'Conteúdo da pacote';
                } else {
                    $attributeGroupObj->public_name[$lang['id_lang']] = 'Pack content';
                }
            }
            if (!$attributeGroupObj->add()) {
                $this->context->controller->errors[] = $this->displayName . ' - ' . $this->l('Unable to add custom attribute group');
                return false;
            } else {
                return true;
            }
        }
        return $alreadyExists;
    }
    protected function _updateDb()
    {
        $columnsToAdd = array(
            array('pm_advancedpack', 'allow_remove_product', 'tinyint(3) unsigned DEFAULT 0', 'fixed_price'),
            array('pm_advancedpack_cart_products', 'customization_infos', 'text', 'id_order'),
            array('pm_advancedpack_products_attributes', 'reduction_amount', 'decimal(20,6) unsigned DEFAULT "0.000000"', 'id_product_attribute'),
            array('pm_advancedpack_products_attributes', 'reduction_type', 'enum("amount","percentage") DEFAULT NULL', 'reduction_amount'),
            array('pm_advancedpack_cart_products', 'cleaned', 'tinyint(3) unsigned DEFAULT 0', 'customization_infos'),
        );
        foreach ($columnsToAdd as $columnInfos) {
            $this->_columnExists($columnInfos[0], $columnInfos[1], true, $columnInfos[2], (isset($columnInfos[3]) ? $columnInfos[3] : false));
        }
        $this->installDatabase();
        $orderPackIndex = Db::getInstance()->executeS('SHOW INDEX FROM `' . _DB_PREFIX_ . 'pm_advancedpack_cart_products` WHERE `Key_name` = "order_pack"');
        if (!empty($orderPackIndex)) {
            Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'pm_advancedpack_cart_products` DROP INDEX `order_pack`, ADD INDEX `order_pack_2` (`id_order`, `id_pack`, `cleaned`)');
        }
    }
    public function getContent()
    {
        if (Tools::getIsset('adminPackContentUpdate') && Tools::getIsset('getProductExtraInformations') && Tools::getValue('getProductExtraInformations') && Tools::getIsset('productId') && Tools::getValue('productId')) {
            $idProduct = (int)Tools::getValue('productId');
            $warehouseListId = array();
            $idWarehouse = 0;
            if (Validate::isUnsignedId($idProduct) && $idProduct > 0) {
                $productObj = new Product($idProduct, true, $this->context->language->id);
                if (Validate::isLoadedObject($productObj)) {
                    if (!empty($productObj->is_virtual)) {
                        $idWarehouse = null;
                    } elseif (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && Product::usesAdvancedStockManagement($productObj->id)) {
                        $warehouseList = Warehouse::getProductWarehouseList($productObj->id, ($productObj->hasAttributes() ? Product::getDefaultAttribute($productObj->id) : 0));
                        if (self::_isFilledArray($warehouseList)) {
                            foreach ($warehouseList as $warehouseRow) {
                                $warehouseListId[] = (int)$warehouseRow['id_warehouse'];
                            }
                            $warehouseListId = array_unique($warehouseListId);
                            if (sizeof($warehouseListId)) {
                                $idWarehouse = current($warehouseListId);
                            }
                        }
                    }
                }
            }
            die(Tools::jsonEncode(array('idWarehouse' => $idWarehouse, 'warehouseListId' => $warehouseListId)));
        } elseif (Tools::getIsset('adminPackContentUpdate') && Tools::getIsset('updatePackPriceSimulation') && Tools::getValue('updatePackPriceSimulation') && Tools::getIsset('productFormValues') && Tools::getValue('productFormValues')) {
            $productFormValues = $packProducts = array();
            parse_str(Tools::getValue('productFormValues'), $productFormValues);
            $packClassicPrice = $packClassicPriceWt = $packPrice = $packPriceWt = $totalPackEcoTax = $totalPackEcoTaxWt = 0;
            $packSettings = array('fixedPrice' => array());
            $idTaxRulesGroup = array();
            $advancedStockManagement = $advancedStockManagementAlert = false;
            if (((Configuration::hasKey('PS_FORCE_ASM_NEW_PRODUCT') && Configuration::get('PS_FORCE_ASM_NEW_PRODUCT')) || !Configuration::hasKey('PS_FORCE_ASM_NEW_PRODUCT')) && Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                $advancedStockManagement = true;
            }
            if (isset($productFormValues['ap5_price_rules']) && $productFormValues['ap5_price_rules'] == 3 && isset($productFormValues['ap5_fixed_pack_price'])) {
                if (is_array($productFormValues['ap5_fixed_pack_price'])) {
                    $packSettings['fixedPrice'] = $productFormValues['ap5_fixed_pack_price'];
                }
                $packSettings['fixedPrice'] = array_map('floatval', $packSettings['fixedPrice']);
                $classicFixedPrice = current($packSettings['fixedPrice']);
                $packSettings['fixedPrice'][1] = $classicFixedPrice;
                $packSettings['fixedPrice'][2] = $classicFixedPrice;
            }
            if (!isset($productFormValues['ap5_productList'])) {
                $productFormValues['ap5_productList'] = array();
            }
            list(, $useTax) = AdvancedPack::getAddressInstance();
            $combinationsInformations = array();
            foreach ($productFormValues['ap5_productList'] as $idProductPack) {
                $customCombinations = (isset($productFormValues['ap5_customCombinations-' . $idProductPack]) && $productFormValues['ap5_customCombinations-' . $idProductPack] ? $productFormValues['ap5_combinationInclude-' . $idProductPack] : array());
                $combinationsInformations[$idProductPack] = array();
                foreach ($customCombinations as $idProductAttribute) {
                    if (!isset($productFormValues['ap5_combinationReductionType-' . $idProductPack . '-' . $idProductAttribute])) {
                        $combinationsInformations[$idProductPack][$idProductAttribute] = array(
                            'reduction_amount' => null,
                            'reduction_type' => null,
                        );
                        continue;
                    }
                    $combinationsInformations[$idProductPack][$idProductAttribute] = array(
                        'reduction_amount' => ($productFormValues['ap5_combinationReductionType-' . $idProductPack . '-' . $idProductAttribute] == 'percentage' ? $productFormValues['ap5_combinationReductionAmount-' . $idProductPack . '-' . $idProductAttribute] / 100 : $productFormValues['ap5_combinationReductionAmount-' . $idProductPack . '-' . $idProductAttribute]),
                        'reduction_type' => $productFormValues['ap5_combinationReductionType-' . $idProductPack . '-' . $idProductAttribute],
                    );
                }
                $defaultCombinationId = (int)Product::getDefaultAttribute((int)$productFormValues['ap5_originalIdProduct-' . $idProductPack]);
                if (!empty($productFormValues['ap5_customCombinations-' . $idProductPack]) && !empty($productFormValues['ap5_defaultCombination-' . $idProductPack])) {
                    $defaultCombinationId = (int)$productFormValues['ap5_defaultCombination-' . $idProductPack];
                }
                $packProducts[] = array(
                    'id_pack' => (!empty($productFormValues['id_product']) ? $productFormValues['id_product'] : null),
                    'id_product_pack' => (is_numeric($idProductPack) && $idProductPack ? (int)$idProductPack : null),
                    'id_product' => $productFormValues['ap5_originalIdProduct-' . $idProductPack],
                    'quantity' => $productFormValues['ap5_quantity-' . $idProductPack],
                    'reduction_amount' => ($productFormValues['ap5_reductionType-' . $idProductPack] == 'percentage' ? $productFormValues['ap5_reductionAmount-' . $idProductPack] / 100 : $productFormValues['ap5_reductionAmount-' . $idProductPack]),
                    'reduction_type' => $productFormValues['ap5_reductionType-' . $idProductPack],
                    'exclusive' => (isset($productFormValues['ap5_exclusive-' . $idProductPack]) && $productFormValues['ap5_exclusive-' . $idProductPack] ? (int)$productFormValues['ap5_exclusive-' . $idProductPack] : 0),
                    'use_reduc' => (isset($productFormValues['ap5_useReduc-' . $idProductPack]) && $productFormValues['ap5_useReduc-' . $idProductPack] ? (int)$productFormValues['ap5_useReduc-' . $idProductPack] : 0),
                    'default_id_product_attribute' => $defaultCombinationId,
                    'combinationsInformations' => (isset($combinationsInformations[$idProductPack]) ? $combinationsInformations[$idProductPack] : array()),
                    'customCustomizationField' => (isset($productFormValues['ap5_customizationFields-' . $idProductPack]) && $productFormValues['ap5_customizationFields-' . $idProductPack] ? $productFormValues['ap5_customizationFieldInclude-' . $idProductPack] : array()),
                );
                $idTaxRulesGroup[] = (int)Product::getIdTaxRulesGroupByIdProduct((int)$productFormValues['ap5_originalIdProduct-' . $idProductPack]);
                if ($advancedStockManagement && !Product::usesAdvancedStockManagement((int)$productFormValues['ap5_originalIdProduct-' . $idProductPack])) {
                    $advancedStockManagementAlert = true;
                }
            }
            $idTaxRulesGroup = array_unique($idTaxRulesGroup);
            if (!sizeof($idTaxRulesGroup)) {
                $finalIdTaxRulesGroup = null;
            } elseif (sizeof($idTaxRulesGroup) == 1) {
                $finalIdTaxRulesGroup = (int)current($idTaxRulesGroup);
            } else {
                $finalIdTaxRulesGroup = 0;
            }
            $packProducts = AdvancedPack::getPackPriceTable($packProducts, $packSettings['fixedPrice'], (is_null($finalIdTaxRulesGroup) ? 0 : $finalIdTaxRulesGroup), $useTax, true);
            foreach ($packProducts as $packProduct) {
                $packClassicPrice += $packProduct['priceInfos']['productClassicPrice'] * $packProduct['priceInfos']['quantity'];
                $packClassicPriceWt += $packProduct['priceInfos']['productClassicPriceWt'] * $packProduct['priceInfos']['quantity'];
                $packPriceWt += $packProduct['priceInfos']['productPackPriceWt'] * $packProduct['priceInfos']['quantity'];
                $packPrice += $packProduct['priceInfos']['productPackPrice'] * $packProduct['priceInfos']['quantity'];
                $totalPackEcoTax += $packProduct['priceInfos']['productEcoTax'] * $packProduct['priceInfos']['quantity'];
                $totalPackEcoTaxWt += $packProduct['priceInfos']['productEcoTaxWt'] * $packProduct['priceInfos']['quantity'];
            }
            if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                $this->context->smarty->assign('link', Context::getContext()->link);
                $this->context->smarty->assign('pmlink', Context::getContext()->link);
            }
            $this->context->smarty->assign(array(
                'packClassicPrice' => $packClassicPrice,
                'packClassicPriceWt' => $packClassicPriceWt,
                'discountPercentage' => number_format((is_array($packProducts) && sizeof($packProducts) && $packPrice <= $packClassicPrice) ? ((1 - ($packPrice / $packClassicPrice)) * -100) : 0, 2),
                'packPrice' => $packPrice,
                'packPriceWt' => $packPriceWt,
                'totalPackEcoTax' => $totalPackEcoTax,
                'totalPackEcoTaxWt' => $totalPackEcoTaxWt,
                'totalFinal' => $packPrice + $totalPackEcoTax,
                'totalFinalWt' => $packPriceWt + $totalPackEcoTaxWt,
            ));
            die(Tools::jsonEncode(array('advancedStockManagementAlert' => $advancedStockManagementAlert, 'idTaxRulesGroup' => $finalIdTaxRulesGroup, 'html' => $this->display(__FILE__, 'views/templates/hook/' . Tools::substr(_PS_VERSION_, 0, 3) . '/admin-product-tab-pack-price-simulation.tpl'))));
        } elseif (Tools::getIsset('adminPackContentUpdate') && Tools::getIsset('addPackLine') && Tools::getValue('addPackLine') && Tools::getIsset('productId') && Tools::getValue('productId')) {
            $idProduct = (int)Tools::getValue('productId');
            if (Validate::isUnsignedId($idProduct) && $idProduct > 0) {
                $productObj = new Product($idProduct, true, $this->context->language->id);
                if (Validate::isLoadedObject($productObj)) {
                    $uniqid = uniqid(self::$modulePrefix);
                    $packContent = array(
                        $uniqid => array(
                            'id_product_pack' => $uniqid,
                            'id_product' => $idProduct,
                            'productObj' => $productObj,
                            'productCombinations' => $productObj->getAttributesResume($this->context->language->id),
                            'productCombinationsWhiteList' => array(),
                            'productHasRequiredCustomizationFields' => self::_isFilledArray($productObj->getRequiredCustomizableFields()),
                            'productCustomizationFields' => AdvancedPack::getProductPackCustomizationFields($productObj->id),
                            'productCustomizationFieldsWhiteList' => array(),
                            'exclusive' => 0,
                            'use_reduc' => 0,
                            'quantity' => 1,
                            'reduction_type' => 'percentage',
                            'reduction_amount' => 0,
                            'urlAdminProduct' => $this->context->link->getAdminLink('AdminProducts', true, array('id_product' => (int)$idProduct)),
                        )
                    );
                    if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                        $this->context->smarty->assign('link', Context::getContext()->link);
                        $this->context->smarty->assign('pmlink', Context::getContext()->link);
                    }
                    $this->context->smarty->assign(array(
                        'link' => $this->context->link,
                        'defaultCurrency' => Currency::getDefaultCurrency(),
                        'packContent' => $packContent
                    ));
                    die(Tools::jsonEncode(array('html' => $this->display(__FILE__, 'views/templates/hook/' . Tools::substr(_PS_VERSION_, 0, 3) . '/admin-product-tab-pack-table.tpl'))));
                }
            }
        } elseif (Tools::getIsset('adminPackContentUpdate') && Tools::getIsset('packContent')) {
            $packContent = $packContentJSON = array();
            foreach (Tools::getValue('packContent') as $idProductPack => $idProduct) {
                $productObj = new Product($idProduct, true, $this->context->language->id);
                $packContentJSON[$idProductPack] = array(
                    'id_product_pack' => $idProductPack,
                    'id_product' => $idProduct,
                );
                $packContent[$idProductPack] = array(
                    'id_product_pack' => $idProductPack,
                    'id_product' => $idProduct,
                    'productObj' => $productObj,
                    'productCombinations' => $productObj->getAttributesResume($this->context->language->id),
                    'productHasRequiredCustomizationFields' => self::_isFilledArray($productObj->getRequiredCustomizableFields()),
                    'productCombinationsWhiteList' => array(),
                    'productCustomizationFields' => AdvancedPack::getProductPackCustomizationFields($productObj->id),
                    'productCustomizationFieldsWhiteList' => array(),
                    'exclusive' => 0,
                    'use_reduc' => 0,
                    'quantity' => 0,
                    'reduction_type' => 'percentage',
                    'reduction_amount' => (float)0.10,
                );
            }
            if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                $this->context->smarty->assign('link', Context::getContext()->link);
                $this->context->smarty->assign('pmlink', Context::getContext()->link);
            }
            $this->context->smarty->assign(array(
                'link' => $this->context->link,
                'defaultCurrency' => Currency::getDefaultCurrency(),
                'packContent' => $packContent
            ));
            die(Tools::jsonEncode(array('packContent' => $packContentJSON, 'html' => $this->display(__FILE__, 'views/templates/hook/' . Tools::substr(_PS_VERSION_, 0, 3) . '/admin-product-tab-pack-table.tpl'))));
        } elseif (Tools::getIsset('adminProductList') && Tools::getIsset('q')) {
            $query = Tools::getValue('q', false);
            if (!$query or $query == '' or Tools::strlen($query) < 1) {
                die();
            }
            if ($pos = strpos($query, ' (ref:')) {
                $query = Tools::substr($query, 0, $pos);
            }
            $excludeIds = implode(',', array_map('intval', array_merge(AdvancedPack::getIdsPacks(true), AdvancedPack::getNativeIdsPacks())));
            $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, p.`cache_default_attribute`
                    FROM `'._DB_PREFIX_.'product` p
                    '.Shop::addSqlAssociation('product', 'p').'
                    LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = '.(int)Context::getContext()->language->id.Shop::addSqlRestrictionOnLang('pl').')
                    WHERE (pl.name LIKE \'%'.pSQL($query).'%\' OR p.reference LIKE \'%'.pSQL($query).'%\')'.
                    (!empty($excludeIds) ? ' AND p.id_product NOT IN ('.$excludeIds.') ' : ' ').
                    'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' .
                    ' GROUP BY p.id_product';
            $items = Db::getInstance()->executeS($sql);
            if ($items) {
                if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                    $finalTable = array();
                    foreach ($items as $item) {
                        $finalTable[] = array(
                            'id' => (int)$item['id_product'],
                            'name' => trim($item['name']),
                            'ref' => trim($item['reference']),
                            'image' => self::getThumbnailImageHTML((int)$item['id_product']),
                        );
                    }
                    die(Tools::jsonEncode($finalTable));
                } else {
                    foreach ($items as $item) {
                        echo str_replace('|', '-', trim($item['name']).(!empty($item['reference']) ? ' (ref: '.$item['reference'].')' : '')).'|'.(int)($item['id_product'])."\n";
                    }
                }
            }
            die;
        } elseif (Tools::getIsset('dismissRating') && Tools::getValue('dismissRating')) {
            Configuration::updateGlobalValue('PM_'.AdvancedPackCoreClass::$_module_prefix.'_DISMISS_RATING', 1);
            die;
        } else {
            if (Tools::isSubmit('processNativePackMigration')) {
                $this->processNativePackMigration();
                if (empty($this->context->controller->errors)) {
                    $this->context->controller->confirmations[] = $this->l('Native packs were successfully migrated');
                }
            } elseif (Tools::getIsset('submitModuleConfiguration') && Tools::isSubmit('submitModuleConfiguration') || Tools::getIsset('submitAdvancedStyles') && Tools::isSubmit('submitAdvancedStyles')) {
                $this->_postProcess();
                if (empty($this->context->controller->errors)) {
                    $this->context->controller->confirmations[] = $this->l('Configuration has successfully been saved');
                }
            }
            parent::getContent();
            if (!$this->_checkPermissions()) {
                return;
            }
            if (Tools::getValue('makeUpdate')) {
                $this->_checkIfModuleIsUpdate(true);
            }
            if (!$this->_checkIfModuleIsUpdate(false)) {
                return $this->fetchTemplate('new-version-detected.tpl');
            }
            $this->context->smarty->assign(array(
                'ps_version' => _PS_VERSION_,
                'adminPacksLink' => $this->context->link->getAdminLink('AdminPacks'),
                'addNewPackLabel' => $this->l('Add a new pack', 'AdminPacksController', null, false),
            ));
            return $this->showRating(true) . $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/admin/admin-add-pack-suggestion.tpl') . $this->renderForm() . $this->displaySupport();
        }
    }
    protected function processNativePackMigration()
    {
        foreach (AdvancedPack::getNativeIdsPacks() as $idNativePack) {
            $res = true;
            $packItems = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'pack` where id_product_pack = '.(int)$idNativePack);
            if (self::_isFilledArray($packItems)) {
                $nativePackObj = new Product($idNativePack);
                if (!Validate::isLoadedObject($nativePackObj)) {
                    $this->context->controller->errors[] = sprintf($this->l('There was an error while processing native pack conversion for ID %d'), $idNativePack);
                    continue;
                }
                $packPrice = $nativePackObj->getPrice(false);
                $fixedPrice = array();
                foreach (Group::getGroups($this->context->language->id, true) as $group) {
                    if (!isset($fixedPrice[(int)$group['id_group']])) {
                        $fixedPrice[(int)$group['id_group']] = $packPrice;
                    }
                }
                $res &= Db::getInstance()->insert('pm_advancedpack', array(
                    'id_pack' => $idNativePack,
                    'id_shop' => $this->context->shop->id,
                    'fixed_price' => json_encode($fixedPrice),
                    'allow_remove_product' => 0,
                ), true);
                $productPosition = 0;
                foreach ($packItems as $packItem) {
                    $res &= Db::getInstance()->insert('pm_advancedpack_products', array(
                        'id_pack' => $idNativePack,
                        'id_product' => (int)$packItem['id_product_item'],
                        'default_id_product_attribute' => (int)$packItem['id_product_attribute_item'],
                        'quantity' => (int)$packItem['quantity'],
                        'use_reduc' => 0,
                        'position' => $productPosition,
                        'reduction_amount' => 0,
                        'reduction_type' => 'percentage',
                        'exclusive' => 0,
                    ), true);
                    $productPosition++;
                }
                if ($res) {
                    Db::getInstance()->delete('pack', '`id_product_pack`='.(int)$idNativePack);
                    Db::getInstance()->update('product', array('cache_is_pack' => 0), 'id_product = '.(int)$idNativePack);
                    $nativePackObj->clearCache();
                    $this->_updatePackFields($idNativePack, true, true);
                } else {
                    $this->context->controller->errors[] = sprintf($this->l('There was an error while processing native pack conversion for ID %d'), $idNativePack);
                }
            }
        }
    }
    private function _postProcess()
    {
        if (Tools::getIsset('submitModuleConfiguration') && Tools::isSubmit('submitModuleConfiguration')) {
            $config = $this->_getModuleConfiguration();
            foreach (array('bootstrapTheme', 'enablePackCrossSellingBlock', 'enableViewThisPackButton', 'enableBuyThisPackButton', 'showImagesOnlyForCombinations', 'autoScrollBuyBlock', 'showProductsThumbnails', 'showProductsPrice', 'showProductsAvailability', 'showProductsFeatures', 'showProductsShortDescription', 'showProductsLongDescription', 'showProductsQuantityWanted', 'postponeUpdatePackSpecificPrice', 'addPrefixToOrderDetail', 'dynamicPriceCalculation') as $configKey) {
                $config[$configKey] = (bool)Tools::getValue($configKey);
            }
            foreach (array('tabActiveBackgroundColor', 'ribbonBackgroundColor', 'viewThisPackButtonBackgroundColor', 'buyThisPackButtonBackgroundColor') as $configKey) {
                $config[$configKey] = (array)Tools::getValue($configKey);
            }
            foreach (array('tabActiveFontColor', 'tabActiveBorderColor', 'ribbonFontColor', 'ribbonBorderColor', 'iconPlusFontColor', 'iconRemoveFontColor', 'iconCheckFontColor', 'viewThisPackButtonFontColor', 'buyThisPackButtonFontColor', 'imageFormatProductZoom', 'imageFormatProductCover', 'imageFormatProductCoverMobile', 'imageFormatProductSlideshow', 'imageFormatProductFooterCover', 'orderByCrossSelling') as $configKey) {
                $config[$configKey] = trim(Tools::getValue($configKey));
            }
            foreach (array('limitPackNbCrossSelling') as $configKey) {
                $config[$configKey] = (int)trim(Tools::getValue($configKey));
            }
            foreach (array('displayMode') as $configKey) {
                $config[$configKey] = ((Tools::getValue($configKey)) == 1 ? 'advanced' : 'simple');
            }
            $this->_setModuleConfiguration($config);
            $this->_updateAdvancedStyles(Tools::getValue('PM_AP5_ADVANCED_STYLES'));
        }
    }
    public function hookModuleRoutes()
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=') && !empty($this->context) && is_object($this->context) && !empty($this->context->controller) && is_object($this->context->controller) && $this->context->controller instanceof AdminControllerCore) {
            $allPostValues = Tools::getAllValues();
            if (!empty($allPostValues['form']['id_product']) && is_numeric($allPostValues['form']['id_product']) && (AdvancedPack::isValidPack($allPostValues['form']['id_product']) || Tools::getValue('is_real_new_pack'))) {
                $request = $this->getSfRequest();
                foreach ($request->request->keys() as $key) {
                    if (preg_match('/^combination_[0-9]+$/', $key)) {
                        $request->request->remove($key);
                    }
                }
                $pack = new Product($allPostValues['form']['id_product']);
                if (Validate::isLoadedObject($pack)) {
                    foreach ($pack->getAttributeCombinations() as $combination) {
                        $combinationArray = array(
                            'attribute_quantity' => $combination['quantity'],
                            'available_date_attribute' => $combination['available_date'],
                            'attribute_minimal_quantity' => $combination['minimal_quantity'],
                            'attribute_reference' => '',
                            'attribute_wholesale_price' => $combination['wholesale_price'],
                            'attribute_price' => $combination['price'],
                            'attribute_priceTI' => '',
                            'attribute_ecotax' => $combination['ecotax'],
                            'attribute_unity' => $combination['unit_price_impact'],
                            'attribute_weight' => $combination['weight'],
                            'attribute_isbn' => '',
                            'attribute_ean13' => '',
                            'attribute_upc' => '',
                            'id_product_attribute' => 0,
                        );
                        if (!(int) Configuration::get('PS_STOCK_MANAGEMENT')) {
                            unset($combinationArray['attribute_quantity']);
                        }
                        $request->request->set('combination_' . $combination['id_product_attribute'], $combinationArray);
                    }
                }
            }
        }
        return array(
            'module-pm_advancedpack-add_pack' => array(
                'controller' => 'add_pack',
                'rule' => 'pack/add/{id_pack}/ap5',
                'keywords' => array(
                    'id_pack'        => array('regexp' => '[0-9]+', 'param' => 'id_pack'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'pm_advancedpack',
                    'ajax' => 1
                )
            ),
            'module-pm_advancedpack-update_pack' => array(
                'controller' => 'update_pack',
                'rule' => 'pack/update/{id_pack}/ap5',
                'keywords' => array(
                    'id_pack'        => array('regexp' => '[0-9]+', 'param' => 'id_pack'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'pm_advancedpack',
                    'ajax' => 1
                )
            ),
            'module-pm_advancedpack-update_cart' => array(
                'controller' => 'update_cart',
                'rule' => 'pack/update_cart/ap5',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'pm_advancedpack',
                    'ajax' => 1
                )
            ),
        );
    }
    private static $sqlQueriesToRun = array();
    public function customShutdownProcess()
    {
        foreach (self::$sqlQueriesToRun as $k => $sqlQuery) {
            Db::getInstance()->execute($sqlQuery);
            unset(self::$sqlQueriesToRun[$k]);
        }
    }
    public function hookActionValidateOrder($params)
    {
        self::$_validateOrderProcess = false;
        self::$actionValidateOrderProcessing = true;
        if (!isset($params['order']) || !isset($params['cart']) || !isset($params['orderStatus']) || !Validate::isLoadedObject($params['order']) || !Validate::isLoadedObject($params['cart']) || !Validate::isLoadedObject($params['orderStatus'])) {
            self::$actionValidateOrderProcessing = false;
            if (self::DEBUG) {
                die;
            }
            return;
        }
        $order = $params['order'];
        $cart = $params['cart'];
        $orderStatus = $params['orderStatus'];
        if (self::DEBUG) {
            $params['order']->delete();
        }
        $orderHasPack = $outOfStock = $orderHasNoTaxPack = false;
        list($vatAddress, $useTax) = AdvancedPack::getAddressInstance();
        $vatAddress = new Address((int)($order->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
        $config = $this->_getModuleConfiguration();
        $preventStockUpdate = [];
        if (!isset($order->product_list) || !self::_isFilledArray($order->product_list)) {
            self::$actionValidateOrderProcessing = false;
            if (self::DEBUG) {
                die;
            }
            return;
        }
        foreach ($order->product_list as $orderProduct) {
            if (!(int)$orderProduct['id_product_attribute'] || !AdvancedPack::isValidPack((int)$orderProduct['id_product'])) {
                continue;
            }
            $orderHasPack = true;
            $packProducts = AdvancedPack::getPackContent((int)$orderProduct['id_product'], (int)$orderProduct['id_product_attribute']);
            if ($packProducts === false) {
                $packProducts = AdvancedPack::getPackContent((int)$orderProduct['id_product']);
            }
            if (!self::_isFilledArray($packProducts)) {
                continue;
            }
            $packFixedPrice = AdvancedPack::getPackFixedPrice((int)$orderProduct['id_product']);
            $packProducts = AdvancedPack::getPackPriceTable($packProducts, $packFixedPrice, AdvancedPack::getPackIdTaxRulesGroup((int)$orderProduct['id_product']), $useTax, true, true);
            foreach ($packProducts as $packProduct) {
                if (isset($packProduct['default_id_product_attribute']) && !isset($packProduct['id_product_attribute'])) {
                    $preventStockUpdate[(int)$orderProduct['id_product'] . '-' . (int)$orderProduct['id_product_attribute']] = true;
                    $packProduct['id_product_attribute'] = $packProduct['default_id_product_attribute'];
                    $packProduct['customization_infos'] = [];
                }
                $null = null;
                $product = new Product((int)$packProduct['id_product'], false, (int)$order->id_lang);
                $orderDetail = new OrderDetail();
                $orderDetail->id_shop = $order->id_shop;
                $orderDetail->id_order = $order->id;
                $orderDetail->product_id = (int)($packProduct['id_product']);
                $orderDetail->product_attribute_id = (int)($packProduct['id_product_attribute'] ? (int)($packProduct['id_product_attribute']) : null);
                $orderDetail->download_deadline = '0000-00-00 00:00:00';
                $orderDetail->download_hash = null;
                if ($id_product_download = ProductDownload::getIdFromIdProduct((int)($packProduct['id_product']))) {
                    $productDownload = new ProductDownload((int)($id_product_download));
                    $orderDetail->download_deadline = $productDownload->getDeadLine();
                    $orderDetail->download_hash = $productDownload->getHash();
                    unset($productDownload);
                }
                $orderContext = $this->context;
                if ($orderContext->shop->id != $orderDetail->id_shop) {
                    $shopContext = new Shop((int)$orderDetail->id_shop);
                    $orderContext = Context::getContext()->cloneContext();
                    $orderContext->shop = $shopContext;
                }
                $idTaxRules = (int)Product::getIdTaxRulesGroupByIdProduct((int)$packProduct['id_product'], $orderContext);
                $taxManager = TaxManagerFactory::getManager($vatAddress, $idTaxRules);
                $taxCalculator = $taxManager->getTaxCalculator();
                $orderDetail->ecotax = Tools::convertPrice((float)$product->ecotax, (int)$order->id_currency);
                if (!AdvancedPack::excludeTaxeOption()) {
                    if (version_compare(_PS_VERSION_, '1.6.0.11', '>=')) {
                        $orderDetail->id_tax_rules_group = $idTaxRules;
                    }
                    $orderDetail->tax_computation_method = (int)$taxCalculator->computation_method;
                }
                $orderDetail->ecotax_tax_rate = 0;
                if (!empty($product->ecotax)) {
                    $orderDetail->ecotax_tax_rate = Tax::getProductEcotaxRate($order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
                }
                $orderDetail->total_shipping_price_tax_incl = 0;
                $specific_price = null;
                $orderDetail->original_product_price = AdvancedPack::getPriceStaticPack((int)$packProduct['id_product'], false, (int)$packProduct['id_product_attribute'], 6, null, false, (bool)$packProduct['use_reduc'], 1, null, null, null, $null, true, true);
                $orderDetail->product_price = $orderDetail->original_product_price;
                $orderDetail->unit_price_tax_incl = (float)Tools::ps_round((float)$packProduct['priceInfos']['productPackPriceWt'] + $packProduct['priceInfos']['productEcoTaxWt'], 9);
                $orderDetail->unit_price_tax_excl = (float)Tools::ps_round((float)$packProduct['priceInfos']['productPackPrice'] + $packProduct['priceInfos']['productEcoTax'], 9);
                $orderDetail->total_price_tax_incl = (float)Tools::ps_round((float)$orderDetail->unit_price_tax_incl * (int)$packProduct['quantity'] * (int)$orderProduct['cart_quantity'], 9);
                $orderDetail->total_price_tax_excl = (float)Tools::ps_round((float)$orderDetail->unit_price_tax_excl * (int)$packProduct['quantity'] * (int)$orderProduct['cart_quantity'], 9);
                $orderDetail->purchase_supplier_price = (float)$product->wholesale_price;
                if ($product->id_supplier > 0 && ($supplier_price = (float)ProductSupplier::getProductPrice((int)$product->id_supplier, (int)$packProduct['id_product'], (int)$packProduct['id_product_attribute'], true)) > 0) {
                    $orderDetail->purchase_supplier_price = (float)$supplier_price;
                }
                $orderDetail->reduction_amount = 0.00;
                $orderDetail->reduction_percent = 0.00;
                $orderDetail->reduction_amount_tax_incl = 0.00;
                $orderDetail->reduction_amount_tax_excl = 0.00;
                if ($packProduct['reduction_amount'] > 0) {
                    if ($packProduct['reduction_type'] == 'amount') {
                        $orderDetail->reduction_amount = (float)Tools::ps_round($useTax ? $taxCalculator->addTaxes($packProduct['reduction_amount']) : $packProduct['reduction_amount'], 2);
                        $orderDetail->reduction_amount_tax_incl = (float)$orderDetail->reduction_amount;
                        $orderDetail->reduction_amount_tax_excl = (float)$packProduct['reduction_amount'];
                    } elseif ($packProduct['reduction_type'] == 'percentage') {
                        $orderDetail->reduction_percent = (float)$packProduct['reduction_amount'] * 100;
                    }
                }
                $orderDetail->group_reduction = (float)(Group::getReduction((int)($order->id_customer)));
                $quantityDiscount = SpecificPrice::getQuantityDiscount(
                    (int)$packProduct['id_product'],
                    (int)$order->id_shop,
                    (int)$cart->id_currency,
                    (int)$vatAddress->id_country,
                    (int)$this->context->customer->id_default_group,
                    (int)$packProduct['quantity'] * (int)$orderProduct['cart_quantity'],
                    false,
                    null,
                    null,
                    $null,
                    true,
                    true
                );
                $unitPrice = AdvancedPack::getPriceStaticPack(
                    (int)$packProduct['id_product'],
                    true,
                    ((int)$packProduct['id_product_attribute'] ? (int)$packProduct['id_product_attribute'] : null),
                    2,
                    null,
                    false,
                    (bool)$packProduct['use_reduc'],
                    1,
                    (int)$order->id_customer,
                    null,
                    (int)$order->{Configuration::get('PS_TAX_ADDRESS_TYPE')},
                    $null,
                    true,
                    true
                );
                $orderDetail->product_quantity_discount = 0.00;
                if ($quantityDiscount) {
                    $orderDetail->product_quantity_discount = $unitPrice;
                    if (Product::getTaxCalculationMethod((int)$order->id_customer) == PS_TAX_EXC) {
                        $orderDetail->product_quantity_discount = Tools::ps_round($unitPrice, 2);
                    }
                    if (isset($orderDetail->tax_calculator)) {
                        $orderDetail->product_quantity_discount -= $orderDetail->tax_calculator->addTaxes($quantityDiscount['price']);
                    }
                }
                $orderDetail->discount_quantity_applied = (($specific_price && $specific_price['from_quantity'] > 1) ? 1 : 0);
                $attributeDatas = AdvancedPack::getProductAttributeList((int)$packProduct['id_product_attribute'], $order->id_lang);
                if (!empty($config['addPrefixToOrderDetail'])) {
                    $orderDetail->product_name = $this->l('Pack') . ' ' . (int)$orderProduct['id_product'] . ' - ' . $product->name . ((isset($attributeDatas['attributes']) && $attributeDatas['attributes'] != null) ? ' - '.$attributeDatas['attributes'] : '');
                } else {
                    $orderDetail->product_name = $product->name . ((isset($attributeDatas['attributes']) && $attributeDatas['attributes'] != null) ? ' - '.$attributeDatas['attributes'] : '');
                }
                $orderDetail->product_quantity = (int)$packProduct['quantity'] * (int)$orderProduct['cart_quantity'];
                $productStockAvailable = StockAvailable::getQuantityAvailableByProduct((int)$packProduct['id_product'], (int)$packProduct['id_product_attribute']);
                if ($orderDetail->product_attribute_id != null) {
                    $productCombination = new Combination($orderDetail->product_attribute_id);
                    $orderDetail->product_ean13 = empty($productCombination->ean13) ? null : pSQL($productCombination->ean13);
                    $orderDetail->product_upc = empty($productCombination->upc) ? null : pSQL($productCombination->upc);
                    $orderDetail->product_reference = empty($productCombination->reference) ? null : pSQL($productCombination->reference);
                    $orderDetail->product_weight = (float)$product->weight + (float)$productCombination->weight;
                    if ($orderDetail->product_reference == null) {
                        $orderDetail->product_reference = empty($product->reference) ? null : pSQL($product->reference);
                    }
                    if ($orderDetail->product_ean13 == null) {
                        $orderDetail->product_ean13 = empty($product->ean13) ? null : pSQL($product->ean13);
                    }
                    if ($orderDetail->product_upc == null) {
                        $orderDetail->product_upc = empty($product->upc) ? null : pSQL($product->upc);
                    }
                } else {
                    $orderDetail->product_ean13 = empty($product->ean13) ? null : pSQL($product->ean13);
                    $orderDetail->product_upc = empty($product->upc) ? null : pSQL($product->upc);
                    $orderDetail->product_reference = empty($product->reference) ? null : pSQL($product->reference);
                    $orderDetail->product_weight = (float)$product->weight;
                }
                $orderDetail->product_supplier_reference = empty($product->supplier_reference) ? null : pSQL($product->supplier_reference);
                if ($product->id_supplier > 0) {
                    $product_supplier_reference = ProductSupplier::getProductSupplierReference((int)$packProduct['id_product'], (int)$packProduct['id_product_attribute'], (int)$product->id_supplier);
                    $orderDetail->product_supplier_reference = empty($product_supplier_reference) ? null : pSQL($product_supplier_reference);
                }
                $orderDetail->id_warehouse = 0;
                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                    $warehouseList = Warehouse::getProductWarehouseList($orderDetail->product_id, (int)$orderDetail->product_attribute_id, $order->id_shop);
                    if (!self::_isFilledArray($warehouseList)) {
                        $warehouseList = Warehouse::getProductWarehouseList($orderDetail->product_id, (int)$orderDetail->product_attribute_id);
                    }
                    if (self::_isFilledArray($warehouseList)) {
                        $defaultWarehouse = current($warehouseList);
                        $orderDetail->id_warehouse = (int)$defaultWarehouse['id_warehouse'];
                    }
                }
                $productQuantity = (int)(Product::getQuantity($orderDetail->product_id, $orderDetail->product_attribute_id));
                $orderDetail->product_quantity_in_stock = ($productQuantity - ((int)($packProduct['quantity']) * (int)$orderProduct['cart_quantity']) < 0) ?
                    $productQuantity : ((int)($packProduct['quantity'] * (int)$orderProduct['cart_quantity']));
                if ($orderStatus->id != Configuration::get('PS_OS_CANCELED') && $orderStatus->id != Configuration::get('PS_OS_ERROR')) {
                    $updateQuantity = true;
                    self::$_preventUpdateQuantityCompleteHook = true;
                    if (!StockAvailable::dependsOnStock((int)$packProduct['id_product'])) {
                        $updateQuantity = StockAvailable::updateQuantity((int)$packProduct['id_product'], (int)$packProduct['id_product_attribute'], -(int)$packProduct['quantity'] * (int)$orderProduct['cart_quantity']);
                    }
                    self::$_preventUpdateQuantityCompleteHook = false;
                    if ($updateQuantity) {
                        $productStockAvailable -= (int)$packProduct['quantity'] * (int)$orderProduct['cart_quantity'];
                    }
                    if ($productStockAvailable < 0 && Configuration::get('PS_STOCK_MANAGEMENT')) {
                        $outOfStock = true;
                    }
                    Product::updateDefaultAttribute((int)$packProduct['id_product']);
                }
                if (self::_isFilledArray($packProduct['customization_infos'])) {
                    foreach ($packProduct['customization_infos'] as $idCustomizationField => $customizationValue) {
                        if (!Tools::strlen($customizationValue)) {
                            continue;
                        }
                        $result = $cart->_addCustomization($orderDetail->product_id, $orderDetail->product_attribute_id, $idCustomizationField, Product::CUSTOMIZE_TEXTFIELD, $customizationValue, $orderDetail->product_quantity);
                        if ($result) {
                            $idCustomization = (int)Db::getInstance()->getValue(
                                'SELECT `id_customization`
                                FROM `' . _DB_PREFIX_ . 'customization`
                                WHERE `id_cart` = ' . (int)$cart->id . '
                                AND `id_product` = ' . (int)$orderDetail->product_id . '
                                AND `id_product_attribute` = ' . (int)$orderDetail->product_attribute_id . '
                                AND `quantity` = ' . (int)$orderDetail->product_quantity . '
                                ORDER BY `id_customization` DESC
                            ');
                            $orderDetail->id_customization = (int)$idCustomization;
                        }
                    }
                    foreach ($packProduct['customization_infos'] as $idCustomizationField => $customizationValue) {
                        if (!Tools::strlen($customizationValue)) {
                            continue;
                        }
                        Db::getInstance()->execute('
                            UPDATE `'._DB_PREFIX_.'customization`
                            SET `in_cart`=1, `id_address_delivery`=' . (int)$cart->id_address_delivery . '
                            WHERE `id_cart`=' . (int)$cart->id . '
                            AND `id_product`=' . (int)$orderDetail->product_id . '
                            AND `id_product_attribute`=' . (int)$orderDetail->product_attribute_id . '
                            AND `quantity`=' . (int)$orderDetail->product_quantity);
                    }
                }
                if ($orderDetail->add()) {
                    Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'pm_advancedpack_cart_products` SET `id_order`=' . (int)$orderDetail->id_order . ' WHERE `id_cart`=' . (int)$order->id_cart . ' AND `id_pack`=' . (int)$orderProduct['id_product'] . ' AND `id_product_attribute`=' . (int)$orderDetail->product_attribute_id);
                    if ($orderStatus->logable) {
                        ProductSale::addProductSale((int)$packProduct['id_product'], (int)$packProduct['quantity'] * (int)$orderProduct['cart_quantity']);
                    }
                    if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && StockAvailable::dependsOnStock((int)$packProduct['id_product'])) {
                        StockAvailable::synchronize((int)$packProduct['id_product'], $order->id_shop);
                    }
                    if ($taxCalculator == null || !($taxCalculator instanceof TaxCalculator) || count($taxCalculator->taxes) == 0 || $order->total_products <= 0) {
                        continue;
                    } else {
                        $ratio = $orderDetail->unit_price_tax_excl / $order->total_products;
                        $order_reduction_amount = $order->total_discounts_tax_excl * $ratio;
                        $discounted_price_tax_excl = $orderDetail->unit_price_tax_excl - $order_reduction_amount;
                        $values = '';
                        foreach ($taxCalculator->getTaxesAmount($discounted_price_tax_excl) as $id_tax => $amount) {
                            $total_amount = $amount * (int)$orderDetail->product_quantity;
                            $values .= '('.(int)$orderDetail->id.','.(int)$id_tax.','.(float)Tools::ps_round($amount, 6).','.(float)Tools::ps_round($total_amount, 6).'),';
                        }
                        self::$sqlQueriesToRun[] = 'DELETE FROM `'._DB_PREFIX_.'order_detail_tax` WHERE id_order_detail='.(int)$orderDetail->id;
                        $values = rtrim($values, ',');
                        self::$sqlQueriesToRun[] = 'INSERT INTO `'._DB_PREFIX_.'order_detail_tax` (id_order_detail, id_tax, unit_amount, total_amount) VALUES '.$values;
                    }
                }
            }
        }
        if ($orderHasPack) {
            $this->customShutdownProcess();
            register_shutdown_function(array($this, 'customShutdownProcess'));
            $orderDetailsList = OrderDetail::getList($order->id);
            if (self::_isFilledArray($orderDetailsList)) {
                foreach ($orderDetailsList as $orderDetailRow) {
                    if (empty((int)$orderDetailRow['product_attribute_id']) || !AdvancedPack::isValidPack((int)$orderDetailRow['product_id'])) {
                        continue;
                    }
                    AdvancedPack::updatePackStock((int)$orderDetailRow['product_id']);
                    $odObj = new OrderDetail((int)$orderDetailRow['id_order_detail']);
                    if ($odObj->delete()) {
                        if (!isset($preventStockUpdate[(int)$orderDetailRow['product_id'] . '-' . (int)$orderDetailRow['product_attribute_id']])) {
                            AdvancedPack::setStockAvailableQuantity((int)$orderDetailRow['product_id'], (int)$orderDetailRow['product_attribute_id'], 0, false);
                        }
                    }
                    if (!AdvancedPack::getPackIdTaxRulesGroup((int)$orderDetailRow['product_id'])) {
                        $orderHasNoTaxPack = true;
                        $packAttributesList = AdvancedPack::getIdProductAttributeListByIdPack((int)$orderDetailRow['product_id'], (int)$orderDetailRow['product_attribute_id']);
                        $vatDifference = AdvancedPack::getPackPrice((int)$orderDetailRow['product_id'], true, true, true, 6, $packAttributesList, array(), array(), true) - AdvancedPack::getPackPrice((int)$orderDetailRow['product_id'], false, true, true, 6, $packAttributesList, array(), array(), true);
                        $vatDifference = (float)($vatDifference * (int)$orderDetailRow['product_quantity']);
                        $order->total_products -= $vatDifference;
                        $order->total_paid_tax_excl -= Tools::ps_round($vatDifference, 6);
                        $order->total_paid_tax_excl = max(0, $order->total_paid_tax_excl);
                    }
                }
            }
            if ($orderHasNoTaxPack) {
                $order->save();
            }
            AdvancedPack::clearAP5Cache();
            if ($outOfStock && Configuration::get('PS_STOCK_MANAGEMENT')) {
                self::$_addOutOfStockOrderHistory = true;
            }
        }
        if (self::_isFilledArray(self::$_productListQuantityToUpdate)) {
            $this->_massUpdateQuantity(self::$_productListQuantityToUpdate);
            self::$_productListQuantityToUpdate = array();
        }
        self::$actionValidateOrderProcessing = false;
        if (self::DEBUG) {
            die;
        }
    }
    protected function getCurrentProduct($transformDescription = false)
    {
        if (is_object($this->context->controller) && $this->context->controller instanceof ProductController) {
            if (method_exists($this->context->controller, 'getProduct')) {
                $product = $this->context->controller->getProduct();
            }
            if (!Validate::isLoadedObject($product)) {
                $id_product = (int)Tools::getValue('id_product');
                if (Validate::isUnsignedId($id_product)) {
                    $product = new Product((int)$id_product, true, $this->context->language->id, $this->context->shop->id);
                    if ($transformDescription && Validate::isLoadedObject($product)) {
                        AdvancedPack::transformProductDescriptionWithImg($product);
                    }
                }
            }
            if (Validate::isLoadedObject($product)) {
                return $product;
            }
        }
        return false;
    }
    public function hookDisplayOverrideTemplate($params)
    {
        $config = $this->_getModuleConfiguration();
        $product = $this->getCurrentProduct();
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=') && isset($params['controller']) && $params['controller'] instanceof OrderConfirmationController) {
            $idCart = (int)Tools::getValue('id_cart', 0);
            $order = new Order(Order::getIdByCartId((int)$idCart));
            if (Validate::isLoadedObject($order)) {
                $orderPresenter = new PrestaShop\PrestaShop\Adapter\Order\OrderPresenter();
                $presentedOrder = $orderPresenter->present($order);
                if (version_compare(_PS_VERSION_, '1.7.5.0', '>=') && !empty($presentedOrder->products)) {
                    $presentedOrderProducts = $presentedOrder->products;
                } elseif (is_array($presentedOrder) && !empty($presentedOrder['products'])) {
                    $presentedOrderProducts = $presentedOrder['products'];
                }
                if (isset($presentedOrderProducts) && is_array($presentedOrderProducts)) {
                    $productAssembler = new ProductAssembler($this->context);
                    $imageRetriever = new PrestaShop\PrestaShop\Adapter\Image\ImageRetriever($this->context->link);
                    $presenterFactory = new ProductPresenterFactory($this->context);
                    $presentationSettings = $presenterFactory->getPresentationSettings();
                    $productPresenter = new PrestaShop\PrestaShop\Core\Product\ProductPresenter(
                        $imageRetriever,
                        $this->context->link,
                        new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter(),
                        new PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever(),
                        $this->context->getTranslator()
                    );
                    foreach ($presentedOrderProducts as &$presentedOrderProductsRow) {
                        if (empty($presentedOrderProductsRow['cover'])) {
                            $productPresenterArray = $productPresenter->present(
                                $presentationSettings,
                                $productAssembler->assembleProduct(array('id_product' => (int)$presentedOrderProductsRow['id_product'], 'id_product_attribute' => (int)$presentedOrderProductsRow['id_product_attribute'])),
                                $this->context->language
                            );
                            $presentedOrderProductsRow['cover'] = $productPresenterArray['cover'];
                        }
                    }
                    if (version_compare(_PS_VERSION_, '1.7.5.0', '>=')) {
                        $presentedOrder->offsetSet('products', $presentedOrderProducts, true);
                    } else {
                        $presentedOrder['products'] = $presentedOrderProducts;
                    }
                    $this->context->smarty->assign('order', $presentedOrder);
                }
            }
        }
        if (Validate::isLoadedObject($product) && AdvancedPack::isValidPack($product->id)) {
            if (!$product->checkAccess(isset(Context::getContext()->customer) ? Context::getContext()->customer->id : 0)) {
                return null;
            }
            $this->_assignSmartyVars('pack', $product->id);
            if (version_compare(_PS_VERSION_, '1.7.0.0', '>=') && Tools::getValue('action') == 'quickview') {
                $customJsDef = array(
                    'ap5_autoScrollBuyBlock' => ($config['displayMode'] == 'advanced' ? (bool)$config['autoScrollBuyBlock'] : false),
                    'ap5_updatePackURL' => self::getPackUpdateURL($product->id),
                    'ap5_isPS16' => true,
                    'ap5_bootstrapTheme' => true,
                    'ap5_displayMode' => $config['displayMode'],
                    'ap5_modalErrorTitle' => $this->l('An error has occurred'),
                );
                $this->context->smarty->assign('ap5_js_custom_vars', $customJsDef);
                $this->context->smarty->assign('ap5_dynamic_css_file', str_replace('{id_shop}', $this->context->shop->id, self::DYN_CSS_FILE));
                return 'module:' . $this->name . '/views/templates/front/' . Tools::substr(_PS_VERSION_, 0, 3) . '/pack-quickview.tpl';
            } elseif (version_compare(_PS_VERSION_, '1.7.0.0', '>=') && Tools::getIsset('action')) {
                return null;
            }
            if ($config['displayMode'] == self::DISPLAY_ADVANCED) {
                if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                    return 'module:' . $this->name . '/views/templates/front/' . Tools::substr(_PS_VERSION_, 0, 3) . '/pack.tpl';
                } else {
                    return $this->getTemplatePath('views/templates/front/' . Tools::substr(_PS_VERSION_, 0, 3) . '/pack.tpl');
                }
            }
        }
    }
    public function hookActionGetProductPropertiesAfter($params)
    {
        if (is_array($params) && isset($params['product']) && !empty($params['product']['id_product']) && AdvancedPack::isValidPack($params['product']['id_product'])) {
            $useTax = (Product::getTaxCalculationMethod((int)$this->context->cookie->id_customer) != 1);
            $packQuantityList = array();
            $packExcludeList = array();
            $packAttributesList = array();
            if ($this->context->controller instanceof pm_advancedpackupdate_packModuleFrontController) {
                $packQuantityList = $this->context->controller->getPackQuantityList();
                $packExcludeList = $this->context->controller->getPackExcludeList();
                $packAttributesList = $this->context->controller->getPackAttributesList();
            }
            $params['product']['price'] = AdvancedPack::getPackPrice((int)$params['product']['id_product'], true, true, true, 6, $packAttributesList, $packQuantityList, $packExcludeList, true);
            $params['product']['price_tax_exc'] = AdvancedPack::getPackPrice((int)$params['product']['id_product'], false, true, true, 6, $packAttributesList, $packQuantityList, $packExcludeList, true);
            $params['product']['classic_pack_price_tax_exc'] = AdvancedPack::getPackPrice((int)$params['product']['id_product'], false, false, true, 6, $packAttributesList, $packQuantityList, $packExcludeList, true);
            $params['product']['price_without_reduction'] = AdvancedPack::getPackPrice((int)$params['product']['id_product'], $useTax, false, true, 6, $packAttributesList, $packQuantityList, $packExcludeList, true);
            if (empty($useTax)) {
                $params['product']['reduction'] = $params['product']['classic_pack_price_tax_exc'] - $params['product']['price_tax_exc'];
            } else {
                $params['product']['reduction'] = $params['product']['price_without_reduction'] - $params['product']['price'];
            }
            $params['product']['orderprice'] = $params['product']['price_tax_exc'];
            $oosMessage = AdvancedPack::getPackOosMessage((int)$params['product']['id_product'], (int)$this->context->language->id, $packAttributesList, $packExcludeList);
            if ($oosMessage !== false) {
                $params['product']['quantity'] = 0;
                $params['product']['available_later'] = $oosMessage;
                $params['product']['out_of_stock'] = 1;
                $params['product']['allow_oosp'] = 1;
            } else {
                $params['product']['quantity'] = AdvancedPack::getPackAvailableQuantity((int)$params['product']['id_product'], $packAttributesList, $packQuantityList, $packExcludeList);
            }
            if (empty($packQuantityList) && empty($params['product']['allow_oosp']) && $params['product']['quantity'] <= 0 && AdvancedPack::isPackAvailableInAtLeastCombinations((int)$params['product']['id_product'])) {
                $params['product']['quantity_all_versions'] = AdvancedPack::PACK_FAKE_STOCK;
            } else {
                $params['product']['quantity_all_versions'] = $params['product']['quantity'];
            }
            if ($params['product']['reduction'] == 0 && isset($params['product']['specific_prices']) && is_array($params['product']['specific_prices']) && isset($params['product']['specific_prices']['reduction']) && $params['product']['specific_prices']['reduction'] > 0) {
                $params['product']['price_without_reduction'] = AdvancedPack::getPackPrice((int)$params['product']['id_product'], $useTax, false, true, 6, $packAttributesList, $packQuantityList, $packExcludeList, false);
            }
            $params['product']['ecotax'] = AdvancedPack::getPackEcoTax((int)$params['product']['id_product']);
            $params['product']['is_ap5_bundle'] = true;
            $params['product']['pack'] = true;
        }
    }
    private function _assignSmartyImageTypeVars()
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->context->smarty->assign('mobile_device', $this->context->isMobile());
            $this->context->smarty->assign('priceDisplay', Product::getTaxCalculationMethod((int)$this->context->cookie->id_customer));
            $this->context->smarty->assign('priceDisplayPrecision', _PS_PRICE_DISPLAY_PRECISION_);
            $this->context->smarty->assign('displayUnitPrice', false);
            $this->context->smarty->assign('displayPackPrice', false);
            $this->context->smarty->assign('pmlink', Context::getContext()->link);
        }
        $config = $this->_getModuleConfiguration();
        foreach ($config as $configKey => $configValue) {
            if (preg_match('/^imageFormat/', $configKey)) {
                $imageTypeSize = Image::getSize($configValue);
                $this->context->smarty->assign(array(
                    $configKey => $configValue,
                    $configKey.'Width' => ($imageTypeSize['width'] ? (int)$imageTypeSize['width'] : ''),
                    $configKey.'Height' =>($imageTypeSize['height'] ? (int)$imageTypeSize['height'] : '')
                ));
            }
        }
    }
    private function _assignSmartyVars($context, $idPack = null)
    {
        if ($context == 'pack' && $idPack) {
            $packAttributesList = array();
            $packErrorsList = array();
            $packFatalErrorsList = array();
            $packForceHideInfoList = array();
            $packContent = AdvancedPack::getPackContent($idPack, null, false, $packAttributesList);
            $packQuantityList = AdvancedPack::getPackAvailableQuantityList($idPack);
            if ($packContent !== false) {
                foreach ($packContent as $packProduct) {
                    $product = new Product((int)$packProduct['id_product']);
                    if (!isset($packAttributesList[$packProduct['id_product_pack']]) || !is_numeric($packAttributesList[$packProduct['id_product_pack']])) {
                        $defaultIdProductAttribute = (int)$packProduct['default_id_product_attribute'];
                    } else {
                        $defaultIdProductAttribute = (int)$packAttributesList[$packProduct['id_product_pack']];
                    }
                    if (Validate::isLoadedObject($product) && !$product->active) {
                        $packFatalErrorsList[(int)$packProduct['id_product_pack']][] = $this->getFrontTranslation('errorProductIsDisabled');
                        $packForceHideInfoList[(int)$packProduct['id_product_pack']] = true;
                        continue;
                    }
                    if (!empty($defaultIdProductAttribute) || $product->hasAttributes()) {
                        $defaultPackProductCombination = new Combination($defaultIdProductAttribute);
                        if (!Validate::isLoadedObject($defaultPackProductCombination) || $defaultPackProductCombination->id_product != $product->id) {
                            $packErrorsList[(int)$packProduct['id_product_pack']][] = $this->getFrontTranslation('errorWrongCombination');
                            $packForceHideInfoList[(int)$packProduct['id_product_pack']] = true;
                            continue;
                        }
                    }
                    if (Validate::isLoadedObject($product) && !$product->checkAccess(isset(Context::getContext()->customer) ? Context::getContext()->customer->id : 0)) {
                        $packFatalErrorsList[(int)$packProduct['id_product_pack']][] = $this->getFrontTranslation('errorProductAccessDenied');
                        $packForceHideInfoList[(int)$packProduct['id_product_pack']] = true;
                        continue;
                    }
                    if (Validate::isLoadedObject($product) && !$product->available_for_order) {
                        $packFatalErrorsList[(int)$packProduct['id_product_pack']][] = $this->getFrontTranslation('errorProductIsNotAvailableForOrder');
                        continue;
                    }
                    if (isset($packQuantityList[(int)$packProduct['id_product_pack']]) && array_sum($packQuantityList[(int)$packProduct['id_product_pack']]) <= 0) {
                        $packFatalErrorsList[(int)$packProduct['id_product_pack']][] = $this->getFrontTranslation('errorProductIsOutOfStock');
                        continue;
                    }
                    if (isset($packQuantityList[(int)$packProduct['id_product_pack']][$defaultIdProductAttribute]) && $packQuantityList[(int)$packProduct['id_product_pack']][$defaultIdProductAttribute] <= 0) {
                        $packErrorsList[(int)$packProduct['id_product_pack']][] = $this->getFrontTranslation('errorProductOrCombinationIsOutOfStock');
                        continue;
                    }
                }
            }
            $packContent = AdvancedPack::getPackContent($idPack, null, true);
            $config = $this->_getModuleConfiguration();
            $this->_assignSmartyImageTypeVars();
            $currentProduct = $this->getCurrentProduct(true);
            $currentProduct->ecotax = AdvancedPack::getPackEcoTax($idPack);
            $ecotax_rate = (float)Tax::getProductEcotaxRate($this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
            $ecotax_tax_amount = Tools::ps_round($currentProduct->ecotax, 2);
            if (Product::$_taxCalculationMethod == PS_TAX_INC && (int)Configuration::get('PS_TAX')) {
                $ecotax_tax_amount = Tools::ps_round($ecotax_tax_amount * (1 + $ecotax_rate / 100), 2);
            }
            $currentProduct->quantity = AdvancedPack::getPackAvailableQuantity($idPack);
            if ($config['displayMode'] == 'simple') {
                $currentProduct->customizable = false;
            }
            if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
                $this->context->smarty->assign('product', $currentProduct);
            }
            $this->context->smarty->assign(array(
                'packDisplayModeAdvanced' => ($config['displayMode'] == self::DISPLAY_ADVANCED),
                'packDisplayModeSimple' => ($config['displayMode'] == self::DISPLAY_SIMPLE),
                'packDeviceIsMobile' => (method_exists($this->context, 'isMobile') ? $this->context->isMobile() : false),
                'packDeviceIsTablet' => (method_exists($this->context, 'isTablet') ? $this->context->isTablet() : false),
                'bootstrapTheme' => (bool)$config['bootstrapTheme'],
                'productsPack' => $packContent,
                'productsPackUnique' => AdvancedPack::getPackContentGroupByProduct($packContent),
                'packAvailableQuantity' => AdvancedPack::getPackAvailableQuantity($idPack),
                'packAvailableQuantityList' => AdvancedPack::getPackAvailableQuantityList($idPack),
                'packMaxImagesPerProduct' => AdvancedPack::getMaxImagesPerProduct($packContent),
                'productsPackErrors' => $packErrorsList,
                'productsPackFatalErrors' => $packFatalErrorsList,
                'productsPackForceHideInfoList' => $packForceHideInfoList,
                'packAttributesList' => array(),
                'packAllowRemoveProduct' => AdvancedPack::getPackAllowRemoveProduct($idPack),
                'packExcludeList' => array(),
                'packQuantityList' => array(),
                'packShowProductsThumbnails' => (isset($config['showProductsThumbnails']) ? $config['showProductsThumbnails'] : $this->_defaultConfiguration['showProductsThumbnails']),
                'packShowProductsPrice' => (isset($config['showProductsPrice']) ? $config['showProductsPrice'] : $this->_defaultConfiguration['showProductsPrice']),
                'packShowProductsAvailability' => (isset($config['showProductsAvailability']) ? $config['showProductsAvailability'] : $this->_defaultConfiguration['showProductsAvailability']),
                'packShowProductsFeatures' => (isset($config['showProductsFeatures']) ? $config['showProductsFeatures'] : $this->_defaultConfiguration['showProductsFeatures']),
                'packShowProductsShortDescription' => (isset($config['showProductsShortDescription']) ? $config['showProductsShortDescription'] : $this->_defaultConfiguration['showProductsShortDescription']),
                'packShowProductsLongDescription' => (isset($config['showProductsLongDescription']) ? $config['showProductsLongDescription'] : $this->_defaultConfiguration['showProductsLongDescription']),
                'packShowProductsQuantityWanted' => (isset($config['showProductsQuantityWanted']) ? $config['showProductsQuantityWanted'] : $this->_defaultConfiguration['showProductsQuantityWanted']),
                'ecotax_tax_inc' => $ecotax_tax_amount,
                'ecotax_tax_exc' => Tools::ps_round($currentProduct->ecotax, 2),
                'groups' => null,
                'combinations' => null,
                'combinationImages' => null,
                'attributesCombinations' => array(),
                'content_only' => (int)Tools::getValue('content_only'),
            ));
        }
    }
    public function hookDisplayHeader($params)
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
            $config = $this->_getModuleConfiguration();
            if (!empty($config['dynamicPriceCalculation'])) {
                AdvancedPack::changeProductPropertiesCache();
            }
        }
        $product = $this->getCurrentProduct();
        if (Validate::isLoadedObject($product)) {
            if (AdvancedPack::isValidPack($product->id)) {
                $config = $this->_getModuleConfiguration();
                if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                    $this->context->controller->registerStylesheet('modules-' . $this->name . '-css-owl-carousel', 'modules/' . $this->name . '/views/css/owl.carousel.min.css', array('media' => 'all', 'priority' => 80));
                    $this->context->controller->registerStylesheet('modules-' . $this->name . '-css-animate', 'modules/' . $this->name . '/views/css/animate.min.css', array('media' => 'all', 'priority' => 80));
                    $this->context->controller->registerStylesheet('modules-' . $this->name . '-css-pack', 'modules/' . $this->name . '/views/css/pack-17.css', array('media' => 'all', 'priority' => 80));
                    $this->context->controller->registerStylesheet('modules-' . $this->name . '-css-dynamic', 'modules/' . $this->name . '/' . str_replace('{id_shop}', $this->context->shop->id, self::DYN_CSS_FILE), array('media' => 'all', 'priority' => 80));
                    $this->context->controller->registerJavascript('modules-' . $this->name . '-js-owl-carousel', 'modules/' . $this->name . '/views/js/owl.carousel.min.js', array('position' => 'bottom', 'priority' => 80));
                    $this->context->controller->registerJavascript('modules-' . $this->name . '-js-main', 'modules/' . $this->name . '/views/js/pack-17.js', array('position' => 'bottom', 'priority' => 80));
                } else {
                    $this->context->controller->addCSS($this->_path.'views/css/owl.carousel.min.css', 'all');
                    $this->context->controller->addCSS($this->_path.'views/css/animate.min.css', 'all');
                    $this->context->controller->addCSS($this->_path.'views/css/pack.css', 'all');
                    $this->context->controller->addCSS($this->_path.str_replace('{id_shop}', $this->context->shop->id, self::DYN_CSS_FILE), 'all');
                    $this->context->controller->addJS($this->_path.'views/js/owl.carousel.min.js');
                    $this->context->controller->addJS($this->_path.'views/js/pack.js');
                }
                if ($config['displayMode'] == self::DISPLAY_ADVANCED) {
                    $this->removeJSFromController(_THEME_JS_DIR_.'product.js');
                }
                Media::addJsDef(array(
                    'ap5_autoScrollBuyBlock' => ($config['displayMode'] == 'advanced' ? (bool)$config['autoScrollBuyBlock'] : false),
                    'ap5_updatePackURL' => self::getPackUpdateURL($product->id),
                    'ap5_isPS16' => true,
                    'ap5_bootstrapTheme' => true,
                    'ap5_displayMode' => $config['displayMode'],
                    'ap5_modalErrorTitle' => $this->l('An error has occurred'),
                ));
            } elseif (self::_isFilledArray(AdvancedPack::getIdPacksByIdProduct($product->id))) {
                $config = $this->_getModuleConfiguration();
                if (!empty($config['enablePackCrossSellingBlock'])) {
                    if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                        $this->context->controller->registerStylesheet('modules-' . $this->name . '-css-owl-carousel', 'modules/' . $this->name . '/views/css/owl.carousel.min.css', array('media' => 'all', 'priority' => 80));
                        $this->context->controller->registerStylesheet('modules-' . $this->name . '-css-animate', 'modules/' . $this->name . '/views/css/animate.min.css', array('media' => 'all', 'priority' => 80));
                        $this->context->controller->registerStylesheet('modules-' . $this->name . '-css-dynamic', 'modules/' . $this->name . '/' . str_replace('{id_shop}', $this->context->shop->id, self::DYN_CSS_FILE), array('media' => 'all', 'priority' => 80));
                        $this->context->controller->registerStylesheet('modules-' . $this->name . '-css-product-footer', 'modules/' . $this->name . '/views/css/product-footer-17.css', array('media' => 'all', 'priority' => 80));
                        $this->context->controller->registerJavascript('modules-' . $this->name . '-js-owl-carousel', 'modules/' . $this->name . '/views/js/owl.carousel.min.js', array('position' => 'bottom', 'priority' => 80));
                        $this->context->controller->registerJavascript('modules-' . $this->name . '-js-product-footer', 'modules/' . $this->name . '/views/js/product-footer-17.js', array('position' => 'bottom', 'priority' => 80));
                    } else {
                        $this->context->controller->addCSS($this->_path.'views/css/owl.carousel.min.css', 'all');
                        $this->context->controller->addCSS($this->_path.'views/css/animate.min.css', 'all');
                        $this->context->controller->addCSS($this->_path.str_replace('{id_shop}', $this->context->shop->id, self::DYN_CSS_FILE), 'all');
                        $this->context->controller->addCSS($this->_path.'views/css/product-footer.css', 'all');
                        $this->context->controller->addJS($this->_path.'views/js/owl.carousel.min.js');
                        $this->context->controller->addJS($this->_path.'views/js/product-footer.js');
                    }
                    Media::addJsDef(array(
                        'ap5_isPS16' => true,
                        'ap5_bootstrapTheme' => true,
                        'ap5_displayMode' => $config['displayMode'],
                    ));
                }
            }
        }
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->context->controller->registerJavascript('modules-' . $this->name . '-js-global', 'modules/' . $this->name . '/views/js/global-17.js', array('position' => 'bottom', 'priority' => 80));
            Media::addJsDef(array(
                'ap5_modalErrorTitle' => $this->l('An error has occurred'),
            ));
            $groupPriceDisplayMethod = (int)Group::getCurrent()->price_display_method;
            if ($groupPriceDisplayMethod || Configuration::get('PS_TAX_DISPLAY')) {
                $this->context->controller->registerJavascript('modules-' . $this->name . '-js-shopping-cart-refresh', 'modules/' . $this->name . '/views/js/shopping-cart-refresh-17.js', array('position' => 'bottom', 'priority' => 80));
                Media::addJsDef(array('ap5_cartRefreshUrl' => $this->context->link->getModuleLink('pm_advancedpack', 'ajax_cart', array('ajax' => 1, 'action' => 'refresh'))));
            }
            $cartPackProducts = $this->getFormatedPackAttributes($this->context->cart);
            Media::addJsDef(array('ap5_cartPackProducts' => $cartPackProducts));
            if (Validate::isLoadedObject($product) && (AdvancedPack::isValidPack($product->id) || self::_isFilledArray(AdvancedPack::getIdPacksByIdProduct($product->id))) && ($groupPriceDisplayMethod || Configuration::get('PS_TAX_DISPLAY') || AdvancedPack::getPackAllowRemoveProduct($product->id))) {
                $shoppingCartModuleInstance = Module::getInstanceByName('ps_shoppingcart');
                if (Validate::isLoadedObject($shoppingCartModuleInstance) && $shoppingCartModuleInstance->active) {
                    Media::addJsDef(array('ap5_modalAjaxUrl' => $this->context->link->getModuleLink('pm_advancedpack', 'ajax_modal')));
                }
            }
        }
    }
    public function hookActionProductSave($params)
    {
        if (Tools::getValue('new_pack') == 1 && isset($params['product'])) {
            // Note Team Validation - Requiert une globale pour obtenir l'objet Router
            global $kernel;
            $params['product']->addToCategories(array($this->context->shop->id_category));
            $sfRouter = $kernel->getContainer()->get('router');
            if (Tools::getValue('source_id_product')) {
                Tools::redirectAdmin($sfRouter->generate('admin_product_form', array('id' => $params['product']->id)) . '&is_real_new_pack=1&source_id_product=' . (int)Tools::getValue('source_id_product'));
            } else {
                Tools::redirectAdmin($sfRouter->generate('admin_product_form', array('id' => $params['product']->id)) . '&is_real_new_pack=1');
            }
        }
    }
    protected function duplicatePack(Product $originalPack, $newIdPack)
    {
        $res = Db::getInstance()->delete('specific_price', '`id_product`=' . (int)$newIdPack);
        $productPack = new Product((int)$newIdPack);
        $combinationList = $productPack->getAttributeCombinations($this->context->language->id);
        if (AdvancedPackCoreClass::_isFilledArray($combinationList)) {
            $combinationToDelete = array();
            foreach ($combinationList as $combinationRow) {
                $idProductAttribute = (int)$combinationRow['id_product_attribute'];
                if (!empty($idProductAttribute)) {
                    $combinationToDelete[] = $idProductAttribute;
                }
            }
            if (sizeof($combinationToDelete)) {
                foreach (array_chunk($combinationToDelete, 100) as $chunkOfCombinationToDelete) {
                    $res &= Db::getInstance()->delete('product_attribute', '`id_product`=' . (int)$newIdPack .' AND `id_product_attribute` IN ('. implode(',', $chunkOfCombinationToDelete) .')');
                    $res &= Db::getInstance()->delete('product_attribute_shop', '`id_product_attribute` IN ('. implode(',', $chunkOfCombinationToDelete) .')');
                    $res &= Db::getInstance()->delete('product_attribute_combination', '`id_product_attribute` IN ('. implode(',', $chunkOfCombinationToDelete) .')');
                    $res &= Db::getInstance()->delete('product_attribute_image', '`id_product_attribute` IN ('. implode(',', $chunkOfCombinationToDelete) .')');
                    $res &= Db::getInstance()->delete('stock_available', '`id_product`=' . (int)$newIdPack .' AND `id_product_attribute` IN ('. implode(',', $chunkOfCombinationToDelete) .')');
                }
            }
        }
        $packRows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'pm_advancedpack` WHERE `id_pack`=' . (int)$originalPack->id);
        foreach ($packRows as $packRow) {
            $packRow['id_pack'] = (int)$newIdPack;
            $res &= Db::getInstance()->insert('pm_advancedpack', $packRow, true);
        }
        $packProductsRows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'pm_advancedpack_products` WHERE `id_pack`=' . (int)$originalPack->id);
        foreach ($packProductsRows as $packProductsRow) {
            $packProductsRow['id_pack'] = (int)$newIdPack;
            $oldIdProductPack = (int)$packProductsRow['id_product_pack'];
            unset($packProductsRow['id_product_pack']);
            $res &= Db::getInstance()->insert('pm_advancedpack_products', $packProductsRow, true);
            $idProductPack = (int)Db::getInstance()->Insert_ID();
            if (empty($idProductPack)) {
                $res = false;
                continue;
            }
            $packProductsAttributesRows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'pm_advancedpack_products_attributes` WHERE `id_product_pack`=' . (int)$oldIdProductPack);
            foreach ($packProductsAttributesRows as $packProductsAttributesRow) {
                $packProductsAttributesRow['id_product_pack'] = (int)$idProductPack;
                Db::getInstance()->insert('pm_advancedpack_products_attributes', $packProductsAttributesRow, true);
            }
            $packProductsCustomizationRows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'pm_advancedpack_products_customization` WHERE `id_product_pack`=' . (int)$oldIdProductPack);
            foreach ($packProductsCustomizationRows as $packProductsCustomizationRow) {
                $packProductsCustomizationRow['id_product_pack'] = (int)$idProductPack;
                Db::getInstance()->insert('pm_advancedpack_products_customization', $packProductsCustomizationRow, true);
            }
        }
        $res &= $this->_updatePackFields($newIdPack);
        return $res;
    }
    public function hookActionProductAdd($params)
    {
        if (self::$_preventInfiniteLoop) {
            return;
        }
        $idProduct = false;
        if (isset($params['product']) && Validate::isLoadedObject($params['product'])) {
            $idProduct = (int)$params['product']->id;
        } elseif (isset($params['id_product']) && (int)$params['id_product'] > 0) {
            $idProduct = (int)$params['id_product'];
        }
        if ($idProduct !== false) {
            if (Tools::getIsset('ap5_is_edited_pack') && Tools::getValue('ap5_is_edited_pack')) {
                $this->_postProcessAdminProducts($idProduct, true);
                $this->_updatePackFields((int)$idProduct, true);
                return;
            }
            $duplicateAction = false;
            $duplicateId = null;
            if (Tools::getIsset('duplicateproduct') && Tools::getValue('id_product') != $idProduct) {
                $duplicateAction = true;
                $duplicateId = (int)Tools::getValue('id_product');
            } elseif (!Tools::getIsset('duplicateproduct') && version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                $request = $this->getSfRequest();
                if (empty($request)) {
                    return;
                }
                if ($request->get('id') && $request->get('action') == 'duplicate') {
                    $duplicateAction = true;
                    $duplicateId = (int)$request->get('id');
                }
            }
            if ($duplicateAction && !empty($duplicateId)) {
                $originalPack = new Product((int)$duplicateId);
                if (!Validate::isLoadedObject($originalPack) || !AdvancedPack::isValidPack($originalPack->id)) {
                    return;
                }
                $this->duplicatePack($originalPack, $idProduct);
            }
        }
    }
    public function hookActionProductUpdate($params)
    {
        if (self::$_preventInfiniteLoop) {
            return;
        }
        if (defined('STORE_COMMANDER')) {
            return;
        }
        $idProduct = false;
        if (isset($params['product']) && Validate::isLoadedObject($params['product'])) {
            $idProduct = (int)$params['product']->id;
        } elseif (isset($params['id_product']) && (int)$params['id_product'] > 0) {
            $idProduct = (int)$params['id_product'];
        }
        if ($idProduct !== false) {
            if (AdvancedPack::isValidPack($idProduct)) {
                if (Tools::getIsset('ap5_is_edited_pack') && Tools::getValue('ap5_is_edited_pack')) {
                    $this->_postProcessAdminProducts($idProduct, false, (Tools::getIsset('ap5_is_major_edited_pack') && Tools::getValue('ap5_is_major_edited_pack')));
                }
                $this->_updatePackFields((int)$idProduct);
            } else {
                if (version_compare(_PS_VERSION_, '1.7.0.0', '>=') && Tools::getValue('is_real_new_pack') && Tools::getIsset('ap5_is_edited_pack') && Tools::getValue('ap5_is_edited_pack')) {
                    $this->_postProcessAdminProducts($idProduct, true);
                    $this->_updatePackFields((int)$idProduct, true);
                } else {
                    $this->updateRelatedPacks((int)$idProduct);
                }
            }
        }
    }
    public function updateRelatedPacks($idProduct)
    {
        if (self::$_preventInfiniteLoop) {
            return;
        }
        if (Shop::getContext() != Shop::CONTEXT_SHOP) {
            $oldContext = Shop::getContext();
            foreach (AdvancedPack::getIdPacksByIdProduct((int)$idProduct) as $idPack) {
                Shop::setContext(Shop::CONTEXT_SHOP, AdvancedPack::getPackIdShop($idPack));
                $this->_updatePackFields((int)$idPack);
            }
            $this->_massUpdateQuantity(array((int)$idProduct));
            Shop::setContext($oldContext);
        } else {
            foreach (AdvancedPack::getIdPacksByIdProduct((int)$idProduct) as $idPack) {
                $this->_updatePackFields((int)$idPack);
            }
            $this->_massUpdateQuantity(array((int)$idProduct));
        }
    }
    public function hookActionUpdateQuantity($params)
    {
        static $alreadyDone = array();
        if (isset($params['id_product']) && is_numeric($params['id_product']) && (int)$params['id_product'] > 0 && !in_array((int)$params['id_product'], $alreadyDone)) {
            self::$_updateQuantityProcess = true;
            $alreadyDone[] = (int)$params['id_product'];
            if (AdvancedPack::isValidPack($params['id_product'])) {
                return;
            }
            if (isset($params['quantity'])) {
                self::$currentStockUpdate[(int)$params['id_product']][(int)$params['id_product_attribute']] = (int)$params['quantity'];
            }
            if (self::$_validateOrderProcess) {
                self::$_productListQuantityToUpdate[] = (int)$params['id_product'];
                return;
            }
            Cache::clean('StockAvailable::getQuantityAvailableByProduct_'.(int)$params['id_product'].'*');
            foreach (AdvancedPack::getIdPacksByIdProduct((int)$params['id_product']) as $idPack) {
                $sql = new DbQuery();
                $sql->select('GROUP_CONCAT(DISTINCT `id_product_attribute_pack`)');
                $sql->from('pm_advancedpack_cart_products', 'acp');
                $sql->where('acp.`id_pack`='.(int)$idPack);
                $sql->where('acp.`id_order` IS NULL');
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                if ($result !== false && !empty($result)) {
                    $result = array_map('intval', explode(',', $result));
                    if (self::_isFilledArray($result)) {
                        foreach ($result as $idProductAttribute) {
                            if ((int)$idProductAttribute > 0) {
                                AdvancedPack::setStockAvailableQuantity((int)$idPack, (int)$idProductAttribute, AdvancedPack::getPackAvailableQuantity((int)$idPack, AdvancedPack::getIdProductAttributeListByIdPack((int)$idPack, (int)$idProductAttribute)), false);
                            }
                        }
                    }
                }
                if (!self::$_preventUpdateQuantityCompleteHook) {
                    AdvancedPack::updatePackStock((int)$idPack);
                }
            }
        }
    }
    private function _massUpdateQuantity($productList)
    {
        if (self::_isFilledArray($productList)) {
            $productList = array_unique($productList);
            $idPackList = $idProductList = array();
            foreach ($productList as $idProduct) {
                $tmpIdPackList = AdvancedPack::getIdPacksByIdProduct((int)$idProduct);
                if (self::_isFilledArray($tmpIdPackList)) {
                    $idPackList = array_merge($tmpIdPackList, $idPackList);
                    $idProductList[] = (int)$idProduct;
                }
            }
            $idPackList = array_unique($idPackList);
            $idProductList = array_unique($idProductList);
            if (self::_isFilledArray($idPackList)) {
                foreach ($idPackList as $idPack) {
                    $sql = new DbQuery();
                    $sql->select('GROUP_CONCAT(DISTINCT `id_product_attribute_pack`)');
                    $sql->from('pm_advancedpack_cart_products', 'acp');
                    $sql->where('acp.`id_pack`='.(int)$idPack);
                    $sql->where('acp.`id_order` IS NULL');
                    $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                    if ($result !== false && !empty($result)) {
                        $result = array_map('intval', explode(',', $result));
                        if (self::_isFilledArray($result)) {
                            foreach ($result as $idProductAttribute) {
                                if ((int)$idProductAttribute > 0) {
                                    AdvancedPack::setStockAvailableQuantity((int)$idPack, (int)$idProductAttribute, AdvancedPack::getPackAvailableQuantity((int)$idPack, AdvancedPack::getIdProductAttributeListByIdPack((int)$idPack, (int)$idProductAttribute)), false);
                                }
                            }
                        }
                    }
                    AdvancedPack::updatePackStock((int)$idPack);
                }
            }
        }
    }
    public function hookActionProductDelete($params)
    {
        if (isset($params['product']) && Validate::isLoadedObject($params['product'])) {
            $clearCache = false;
            if (AdvancedPack::isValidPack($params['product']->id)) {
                Db::getInstance()->delete('pm_advancedpack', '`id_pack`='.(int)$params['product']->id);
                Db::getInstance()->delete('pm_advancedpack_products', '`id_pack`='.(int)$params['product']->id);
                Db::getInstance()->delete('pm_advancedpack_cart_products', '`id_order` IS NULL AND `id_pack`='.(int)$params['product']->id);
                Db::getInstance()->delete('pm_advancedpack_products_attributes', '`id_product_pack` NOT IN (SELECT `id_product_pack` FROM `'._DB_PREFIX_.'pm_advancedpack_products`)');
                $clearCache = true;
            } else {
                $packList = AdvancedPack::getIdPacksByIdProduct((int)$params['product']->id);
                Db::getInstance()->delete('pm_advancedpack_products', '`id_product`='.(int)$params['product']->id);
                Db::getInstance()->delete('pm_advancedpack_products_attributes', '`id_product_pack` NOT IN (SELECT `id_product_pack` FROM `'._DB_PREFIX_.'pm_advancedpack_products`)');
                Db::getInstance()->delete('pm_advancedpack_cart_products', '`id_order` IS NULL AND `id_product_pack` NOT IN (SELECT `id_product_pack` FROM `'._DB_PREFIX_.'pm_advancedpack_products`)');
                AdvancedPack::clearAP5Cache();
                foreach ($packList as $idPack) {
                    $pack = new AdvancedPack($idPack);
                    if (Validate::isLoadedObject($pack)) {
                        Db::getInstance()->delete('pm_advancedpack_cart_products', '`id_order` IS NULL AND `id_pack`='.(int)$idPack);
                        SpecificPrice::deleteByProductId((int)$idPack);
                        $pack->deleteCartProducts();
                        $pack->deleteFromCartRules();
                        $pack->deleteProductAttributes();
                        $pack->active = false;
                        $pack->update();
                        if (!$clearCache) {
                            $clearCache = true;
                        }
                    }
                }
            }
            if ($clearCache) {
                AdvancedPack::clearAP5Cache();
            }
        }
    }
    public function hookActionBeforeCartUpdateQty($params)
    {
        if (Validate::isLoadedObject($params['cart']) && Tools::getIsset('ajax') && Tools::getIsset('add') && Tools::getValue('add') && Tools::getIsset('id_product') && isset($this->context->controller) && is_object($this->context->controller) && get_class($this->context->controller) == 'CartController' && $this->context->controller->isTokenValid() && $this->context->controller->ajax) {
            $params['beforeCartUpdate'] = true;
            $this->hookActionCartSave($params);
            self::$_preventCartSaveHook = true;
        } elseif (Validate::isLoadedObject($params['cart']) && !Tools::getIsset('summary') && Tools::getIsset('add') && Tools::getValue('add') && Tools::getIsset('id_product') && isset($this->context->controller) && is_object($this->context->controller) && $this->context->controller->isTokenValid() && !$this->context->controller->ajax) {
            $params['beforeCartUpdate'] = true;
            $this->hookActionCartSave($params);
            self::$_preventCartSaveHook = true;
        }
    }
    public function hookActionCartSave($params)
    {
        if (self::$_preventInfiniteLoop || self::$_preventCartSaveHook) {
            return;
        }
        $idProduct = (int)Tools::getValue('id_product');
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=') && Tools::getIsset('group')) {
            $idProductAttribute = (int)Product::getIdProductAttributesByIdAttributes($idProduct, Tools::getValue('group'));
        } else {
            $idProductAttribute = (int)Tools::getValue('id_product_attribute');
            if (!$idProductAttribute && (int)Tools::getValue('ipa')) {
                $idProductAttribute = (int)Tools::getValue('ipa');
            }
        }
        $idAddressDelivery = (int)Tools::getValue('id_address_delivery');
        $newCartQuantityUp = abs(Tools::getValue('qty', 1));
        if (!isset($this->context->cookie->id_cart) || !$this->context->cookie->id_cart) {
            $this->context->cookie->id_cart = (int)$this->context->cart->id;
        }
        if (Validate::isLoadedObject($params['cart']) && (version_compare(_PS_VERSION_, '1.7.0.0', '<') && Tools::getIsset('ajax') || version_compare(_PS_VERSION_, '1.7.0.0', '>=') && Tools::getIsset('action')) && Tools::getIsset('add') && Tools::getValue('add') && Tools::getIsset('id_product') && isset($this->context->controller) && is_object($this->context->controller) && get_class($this->context->controller) == 'CartController' && $this->context->controller->isTokenValid() && $this->context->controller->ajax) {
            if (!Tools::getIsset('summary')) {
                if (in_array($idProduct, AdvancedPack::getExclusiveProducts())) {
                    self::$_preventInfiniteLoop = true;
                    if (empty($params['beforeCartUpdate'])) {
                        $params['cart']->deleteProduct($idProduct, $idProductAttribute);
                    }
                    self::$_preventInfiniteLoop = false;
                    if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                        http_response_code(500);
                    }
                    die(Tools::jsonEncode(array('hasError' => true, 'from_AP5' => true, 'errors' => array($this->l('This product can only be ordered via a pack')))));
                } else {
                    if (AdvancedPack::isValidPack($idProduct)) {
                        self::$_preventInfiniteLoop = true;
                        if (!$idProductAttribute) {
                            $idProductAttribute = (int)Product::getDefaultAttribute($idProduct);
                        }
                        if (AdvancedPack::isValidPack($idProduct, true)) {
                            if (!$idProductAttribute) {
                                if (empty($params['beforeCartUpdate'])) {
                                    $params['cart']->deleteProduct($idProduct, $idProductAttribute);
                                }
                                if (AdvancedPack::isInStock($idProduct, $newCartQuantityUp, array(), true, $idProductAttribute)) {
                                    AdvancedPack::addPackToCart($idProduct, $newCartQuantityUp, array(), array(), true);
                                } else {
                                    if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                                        http_response_code(500);
                                    }
                                    die(Tools::jsonEncode(array('hasError' => true, 'from_AP5' => true, 'errors' => array($this->getFrontTranslation('errorMaximumQuantity')))));
                                }
                            } else {
                                if (AdvancedPack::isInStock($idProduct, $newCartQuantityUp, array(), true, $idProductAttribute)) {
                                    if (empty($params['beforeCartUpdate'])) {
                                        $params['cart']->deleteProduct($idProduct, $idProductAttribute);
                                    }
                                    AdvancedPack::addPackToCart($idProduct, $newCartQuantityUp, array(), array(), true);
                                } else {
                                    if (empty($params['beforeCartUpdate'])) {
                                        $params['cart']->updateQty($newCartQuantityUp, $idProduct, $idProductAttribute, 0, 'down', $idAddressDelivery);
                                    }
                                    if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                                        http_response_code(500);
                                    }
                                    die(Tools::jsonEncode(array('hasError' => true, 'from_AP5' => true, 'errors' => array($this->getFrontTranslation('errorMaximumQuantity')))));
                                }
                            }
                        } else {
                            if (empty($params['beforeCartUpdate'])) {
                                $params['cart']->updateQty($newCartQuantityUp, $idProduct, $idProductAttribute, 0, 'down', $idAddressDelivery);
                            }
                            if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                                http_response_code(500);
                            }
                            die(Tools::jsonEncode(array('hasError' => true, 'from_AP5' => true, 'errors' => array($this->getFrontTranslation('errorOutOfStock')))));
                        }
                        self::$_preventInfiniteLoop = false;
                    } else {
                        if (!Product::isAvailableWhenOutOfStock(StockAvailable::outOfStock((int)$idProduct))) {
                            if (!$idProductAttribute) {
                                $idProductAttribute = (int)Product::getDefaultAttribute($idProduct);
                            }
                            $currentPackCartStock = AdvancedPack::getPackProductsCartQuantity();
                            $stockAvailable = (int)StockAvailable::getQuantityAvailableByProduct((int)$idProduct, (int)$idProductAttribute);
                            if (isset($currentPackCartStock[(int)$idProduct][(int)$idProductAttribute])) {
                                $stockAvailable -= $currentPackCartStock[(int)$idProduct][(int)$idProductAttribute];
                                $stockAvailable -= AdvancedPack::getCartQuantity((int)$idProduct, (int)$idProductAttribute);
                                if ($stockAvailable < 0) {
                                    self::$_preventInfiniteLoop = true;
                                    if (empty($params['beforeCartUpdate'])) {
                                        $params['cart']->updateQty($newCartQuantityUp, $idProduct, $idProductAttribute, 0, 'down', $idAddressDelivery);
                                    }
                                    self::$_preventInfiniteLoop = false;
                                    if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                                        http_response_code(500);
                                    }
                                    die(Tools::jsonEncode(array('hasError' => true, 'from_AP5' => true, 'errors' => array($this->getFrontTranslation('errorMaximumQuantity')))));
                                }
                            }
                        }
                    }
                }
            } elseif (Tools::getIsset('summary') && Tools::getValue('op', 'up') == 'up' && (int)Tools::getValue('ipa')) {
                if (AdvancedPack::isValidPack($idProduct)) {
                    if (AdvancedPack::isValidPack($idProduct, true)) {
                        if ($newCartQuantityUp > 0 && !AdvancedPack::isInStock($idProduct, $newCartQuantityUp, array(), true, $idProductAttribute)) {
                            self::$_preventInfiniteLoop = true;
                            if (empty($params['beforeCartUpdate'])) {
                                $params['cart']->updateQty($newCartQuantityUp, $idProduct, $idProductAttribute, 0, 'down', $idAddressDelivery);
                            }
                            self::$_preventInfiniteLoop = false;
                            if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                                http_response_code(500);
                            }
                            die(Tools::jsonEncode(array('hasError' => true, 'from_AP5' => true, 'errors' => array($this->getFrontTranslation('errorMaximumQuantity')))));
                        }
                    } else {
                        self::$_preventInfiniteLoop = true;
                        if (empty($params['beforeCartUpdate'])) {
                            $params['cart']->updateQty($newCartQuantityUp, $idProduct, $idProductAttribute, 0, 'down', $idAddressDelivery);
                        }
                        self::$_preventInfiniteLoop = false;
                        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                            http_response_code(500);
                        }
                        die(Tools::jsonEncode(array('hasError' => true, 'from_AP5' => true, 'errors' => array($this->getFrontTranslation('errorOutOfStock')))));
                    }
                } else {
                    if (!Product::isAvailableWhenOutOfStock(StockAvailable::outOfStock((int)$idProduct))) {
                        $currentPackCartStock = AdvancedPack::getPackProductsCartQuantity();
                        $stockAvailable = (int)StockAvailable::getQuantityAvailableByProduct((int)$idProduct, (int)$idProductAttribute);
                        if (isset($currentPackCartStock[(int)$idProduct][(int)$idProductAttribute])) {
                            $stockAvailable -= $currentPackCartStock[(int)$idProduct][(int)$idProductAttribute];
                            $stockAvailable -= AdvancedPack::getCartQuantity((int)$idProduct, (int)$idProductAttribute);
                            if ($stockAvailable < 0) {
                                self::$_preventInfiniteLoop = true;
                                if (empty($params['beforeCartUpdate'])) {
                                    $params['cart']->updateQty($newCartQuantityUp, $idProduct, $idProductAttribute, 0, 'down', $idAddressDelivery);
                                }
                                self::$_preventInfiniteLoop = false;
                                if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                                    http_response_code(500);
                                }
                                die(Tools::jsonEncode(array('hasError' => true, 'from_AP5' => true, 'errors' => array($this->getFrontTranslation('errorMaximumQuantity')))));
                            }
                        }
                    }
                }
            }
        } elseif (Validate::isLoadedObject($params['cart']) && !Tools::getIsset('summary') && Tools::getIsset('add') && Tools::getValue('add') && Tools::getIsset('id_product') && isset($this->context->controller) && is_object($this->context->controller) && $this->context->controller->isTokenValid() && !$this->context->controller->ajax) {
            if (in_array($idProduct, AdvancedPack::getExclusiveProducts())) {
                self::$_preventInfiniteLoop = true;
                if (empty($params['beforeCartUpdate'])) {
                    $params['cart']->deleteProduct($idProduct, $idProductAttribute);
                }
                self::$_preventInfiniteLoop = false;
                $this->context->controller->errors[] = $this->l('This product can only be ordered via a pack');
            } else {
                if (!Tools::getValue('ipa')) {
                    $idProductAttribute = (int)Product::getDefaultAttribute($idProduct);
                }
                if (AdvancedPack::isValidPack($idProduct)) {
                    self::$_preventInfiniteLoop = true;
                    if (!AdvancedPack::isValidPack($idProduct, true)) {
                        if (empty($params['beforeCartUpdate'])) {
                            $params['cart']->deleteProduct($idProduct, $idProductAttribute);
                        }
                        $this->context->controller->errors[] = $this->getFrontTranslation('errorOutOfStock');
                    } else {
                        if (!Tools::getValue('ipa')) {
                            if (empty($params['beforeCartUpdate'])) {
                                $params['cart']->deleteProduct($idProduct, $idProductAttribute);
                            }
                            if (AdvancedPack::isInStock($idProduct, $newCartQuantityUp, array(), true, $idProductAttribute)) {
                                AdvancedPack::addPackToCart($idProduct, $newCartQuantityUp, array(), array(), false);
                            } else {
                                $this->context->controller->errors[] = $this->getFrontTranslation('errorMaximumQuantity');
                            }
                        } else {
                            if (AdvancedPack::isInStock($idProduct, $newCartQuantityUp, array(), true, $idProductAttribute)) {
                                if (empty($params['beforeCartUpdate'])) {
                                    $params['cart']->deleteProduct($idProduct, $idProductAttribute);
                                }
                                AdvancedPack::addPackToCart($idProduct, $newCartQuantityUp, array(), array(), false);
                            } else {
                                if (empty($params['beforeCartUpdate'])) {
                                    $params['cart']->updateQty($newCartQuantityUp, $idProduct, (int)$idProductAttribute, 0, 'down', $idAddressDelivery);
                                }
                                $this->context->controller->errors[] = $this->getFrontTranslation('errorMaximumQuantity');
                            }
                        }
                    }
                    self::$_preventInfiniteLoop = false;
                } else {
                    if (!Product::isAvailableWhenOutOfStock(StockAvailable::outOfStock((int)$idProduct))) {
                        $currentPackCartStock = AdvancedPack::getPackProductsCartQuantity();
                        $stockAvailable = (int)StockAvailable::getQuantityAvailableByProduct((int)$idProduct, (int)$idProductAttribute);
                        if (isset($currentPackCartStock[(int)$idProduct][(int)$idProductAttribute])) {
                            $stockAvailable -= $currentPackCartStock[(int)$idProduct][(int)$idProductAttribute];
                            $stockAvailable -= AdvancedPack::getCartQuantity((int)$idProduct, (int)$idProductAttribute);
                            if ($stockAvailable < 0) {
                                self::$_preventInfiniteLoop = true;
                                if (empty($params['beforeCartUpdate'])) {
                                    $params['cart']->updateQty($newCartQuantityUp, $idProduct, $idProductAttribute, 0, 'down', $idAddressDelivery);
                                }
                                $this->context->controller->errors[] = $this->getFrontTranslation('errorMaximumQuantity');
                                self::$_preventInfiniteLoop = false;
                                return;
                            }
                        }
                    }
                }
            }
        } else {
            if (Tools::isSubmit('submitReorder') && $id_order = (int)Tools::getValue('id_order')) {
                $this->_duplicateCartWithPacks($id_order);
            }
        }
        if (Validate::isLoadedObject($params['cart'])) {
            AdvancedPack::updateCartSpecificPriceAndStock((int)$params['cart']->id);
            if (!empty($idProduct)) {
                foreach (AdvancedPack::getIdPacksByIdProduct((int)$idProduct) as $idPack) {
                    $sql = new DbQuery();
                    $sql->select('GROUP_CONCAT(DISTINCT `id_product_attribute_pack`)');
                    $sql->from('pm_advancedpack_cart_products', 'acp');
                    $sql->where('acp.`id_cart`='.(int)$params['cart']->id);
                    $sql->where('acp.`id_pack`='.(int)$idPack);
                    $sql->where('acp.`id_order` IS NULL');
                    $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                    if ($result !== false && !empty($result)) {
                        $result = array_map('intval', explode(',', $result));
                        if (self::_isFilledArray($result)) {
                            foreach ($result as $idProductAttribute) {
                                if ((int)$idProductAttribute > 0) {
                                    AdvancedPack::setStockAvailableQuantity((int)$idPack, (int)$idProductAttribute, AdvancedPack::getPackAvailableQuantity((int)$idPack, AdvancedPack::getIdProductAttributeListByIdPack((int)$idPack, (int)$idProductAttribute), array(), array(), $idProductAttribute), false);
                                }
                            }
                        }
                    }
                }
            }
        }
        $lastTimeCleaning = Configuration::get('PM_AP5_LAST_CLEAN');
        if (!$lastTimeCleaning || time() > ($lastTimeCleaning + 86400)) {
            $this->cleanModuleDatas();
            Configuration::updateValue('PM_AP5_LAST_CLEAN', time());
        }
    }
    protected function cleanModuleDatas()
    {
        AdvancedPack::removeOldPackData();
        if (AdvancedPack::getPackAttributeGroupId() !== false) {
            $sql = new DbQuery();
            $sql->select('a.`id_attribute`, pac.`id_product_attribute`');
            $sql->from('product_attribute_combination', 'pac');
            $sql->innerJoin('attribute', 'a', 'a.`id_attribute` = pac.`id_attribute` AND a.`id_attribute_group` = ' . (int)AdvancedPack::getPackAttributeGroupId());
            $sql->innerJoin('attribute_lang', 'al', 'a.`id_attribute` = al.`id_attribute` AND al.`id_lang`=' . (int)$this->context->language->id . ' AND al.`name` NOT LIKE "%-defaultCombination"');
            $sql->leftJoin('cart_product', 'cp', 'cp.`id_product_attribute` = pac.`id_product_attribute`');
            $sql->where('cp.`id_product_attribute` IS NULL');
            $idAttributeList = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            if (self::_isFilledArray($idAttributeList)) {
                foreach ($idAttributeList as $attributeRow) {
                    $attribute = new Attribute((int)$attributeRow['id_attribute'], $this->context->language->id);
                    if (Validate::isLoadedObject($attribute) && !preg_match('/[0-9]+-defaultCombination/', $attribute->name)) {
                        Db::getInstance()->delete('attribute_shop', '`id_attribute` = '.(int)$attribute->id);
                        if (!empty($attributeRow['id_product_attribute'])) {
                            Db::getInstance()->delete('product_attribute_shop', '`id_product_attribute` = '.(int)$attributeRow['id_product_attribute']);
                        }
                        $attribute->delete();
                    }
                    if (!empty($attributeRow['id_product_attribute'])) {
                        $combination = new Combination((int)$attributeRow['id_product_attribute']);
                        if (Validate::isLoadedObject($combination) && (!Validate::isLoadedObject($attribute) || (Validate::isLoadedObject($attribute) && !preg_match('/[0-9]+-defaultCombination/', $attribute->name)))) {
                            Db::getInstance()->delete('product_attribute_shop', '`id_product_attribute` = '.(int)$attributeRow['id_product_attribute']);
                            $combination->delete();
                        }
                    }
                }
            }
        }
    }
    private function _duplicateCartWithPacks($id_order)
    {
        $oldCart = new Cart(Order::getCartIdStatic($id_order, $this->context->customer->id));
        if (Validate::isLoadedObject($oldCart)) {
            self::$_preventInfiniteLoop = true;
            $id_address_delivery = Configuration::get('PS_ALLOW_MULTISHIPPING') ? $this->context->cart->id_address_delivery : 0;
            if (!$this->context->cart->id) {
                if (!$this->context->cart instanceof Cart) {
                    $this->context->cart = new Cart();
                }
                if (Context::getContext()->cookie->id_guest) {
                    $guest = new Guest(Context::getContext()->cookie->id_guest);
                    $this->context->cart->mobile_theme = $guest->mobile_theme;
                }
                $this->context->cart->add();
                if ($this->context->cart->id) {
                    $this->context->cookie->id_cart = (int)$this->context->cart->id;
                }
            }
            $productList = $oldCart->getProducts();
            foreach ($productList as $product) {
                if (AdvancedPack::isValidPack($product['id_product'], true)) {
                    AdvancedPack::addPackToCart($product['id_product'], (int)$product['cart_quantity'], AdvancedPack::getIdProductAttributeListByIdPack($product['id_product'], $product['id_product_attribute']), array(), false);
                } else {
                    $this->context->cart->updateQty(
                        (int)$product['quantity'],
                        (int)$product['id_product'],
                        (int)$product['id_product_attribute'],
                        null,
                        'up',
                        (int)$id_address_delivery,
                        new Shop((int)$this->context->cart->id_shop),
                        false
                    );
                }
            }
            self::$_preventInfiniteLoop = false;
        }
        if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 1) {
            Tools::redirect('index.php?controller=order-opc');
        }
        Tools::redirect('index.php?controller=order');
    }
    public function hookActionShopDataDuplication($params)
    {
        if (!empty($params['new_id_shop'])) {
            $packIdList = AdvancedPack::getIdsPacks(true);
            if (AdvancedPackCoreClass::_isFilledArray($packIdList)) {
                Db::getInstance()->delete('product_shop', '`id_product` IN (' . implode(',', $packIdList) . ') AND `id_shop`=' . (int)$params['new_id_shop']);
            }
        }
    }
    public function hookActionObjectOrderAddAfter($params)
    {
        self::$_validateOrderProcess = true;
        $order = $params['object'];
        if (is_object($order) && self::_isFilledArray($order->product_list)) {
            foreach ($order->product_list as $key => $product) {
                if ($product['id_product_attribute'] && AdvancedPack::isValidPack($product['id_product'])) {
                    $order->product_list[$key]['attributes'] = $this->displayPackContent($product['id_product'], $product['id_product_attribute'], self::PACK_CONTENT_ORDER_CONFIRMATION_EMAIL);
                }
            }
        }
    }
    public function hookActionObjectOrderUpdateAfter($params)
    {
        if (!self::$_addOutOfStockOrderHistory) {
            return;
        }
        $order = $params['object'];
        if (Validate::isLoadedObject($order)) {
            $orderHistoryCount = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'order_history` WHERE id_order = '.(int)$order->id);
            if ($orderHistoryCount == 1) {
                self::$_addOutOfStockOrderHistory = false;
                $history = new OrderHistory();
                $history->id_order = (int)$order->id;
                if (version_compare(_PS_VERSION_, '1.6.0.11', '>=')) {
                    $history->changeIdOrderState(Configuration::get($order->valid ? 'PS_OS_OUTOFSTOCK_PAID' : 'PS_OS_OUTOFSTOCK_UNPAID'), $order, true);
                } else {
                    $history->changeIdOrderState(Configuration::get('PS_OS_OUTOFSTOCK'), $order, true);
                }
                $history->addWithemail();
            }
        }
    }
    public function hookActionObjectSpecificPriceAddAfter($params)
    {
        if (!isset($params['object']) || !Validate::isLoadedObject($params['object']) || empty($params['object']->id_product)) {
            return;
        }
        if (!empty($params['object']->id_customer) && !empty($params['object']->id_cart)) {
            return;
        }
        $idPackList = AdvancedPack::getIdsPacks(true);
        if (in_array($params['object']->id_product, $idPackList)) {
            if (!empty($params['object']->id_specific_price_rule)) {
                $params['object']->delete();
            }
        } else {
            $this->updateRelatedPacks((int)$params['object']->id_product);
        }
    }
    public function hookActionObjectSpecificPriceDeleteAfter($params)
    {
        if (isset($params['object']) && Validate::isLoadedObject($params['object']) && !empty($params['object']->id_product)) {
            $idPackList = AdvancedPack::getIdsPacks(true);
            if (!in_array($params['object']->id_product, $idPackList)) {
                $this->updateRelatedPacks((int)$params['object']->id_product);
            }
        }
    }
    public function hookActionProductListModifier($params)
    {
        if (isset($params['cat_products']) && is_array($params['cat_products']) && sizeof($params['cat_products'])) {
            $idPackList = AdvancedPack::getIdsPacks(true);
            if (!sizeof($idPackList)) {
                return;
            }
            self::$actionProductListModifierProcessing = true;
            $useTax = (Product::getTaxCalculationMethod((int)$this->context->cookie->id_customer) != 1);
            foreach ($params['cat_products'] as &$catProduct) {
                if (empty($catProduct['is_ap5_bundle']) && in_array((int)$catProduct['id_product'], $idPackList)) {
                    $catProduct['price'] = AdvancedPack::getPackPrice((int)$catProduct['id_product'], true, true, true, 6, array(), array(), array(), true);
                    $catProduct['price_tax_exc'] = AdvancedPack::getPackPrice((int)$catProduct['id_product'], false, true, true, 6, array(), array(), array(), true);
                    $catProduct['classic_pack_price_tax_exc'] = AdvancedPack::getPackPrice((int)$catProduct['id_product'], false, false, true, 6, array(), array(), array(), true);
                    $catProduct['price_without_reduction'] = AdvancedPack::getPackPrice((int)$catProduct['id_product'], $useTax, false, true, 6, array(), array(), array(), true);
                    $catProduct['reduction'] = $catProduct['classic_pack_price_tax_exc'] - $catProduct['price_tax_exc'];
                    $catProduct['orderprice'] = $catProduct['price_tax_exc'];
                    $oosMessage = AdvancedPack::getPackOosMessage((int)$catProduct['id_product'], (int)$this->context->language->id);
                    if ($oosMessage !== false) {
                        $catProduct['quantity'] = 0;
                        $catProduct['available_later'] = $oosMessage;
                        $catProduct['out_of_stock'] = 1;
                        $catProduct['allow_oosp'] = 1;
                    } else {
                        $catProduct['quantity'] = AdvancedPack::getPackAvailableQuantity((int)$catProduct['id_product']);
                    }
                    if (empty($catProduct['allow_oosp']) && $catProduct['quantity'] <= 0 && AdvancedPack::isPackAvailableInAtLeastCombinations((int)$catProduct['id_product'])) {
                        $catProduct['quantity_all_versions'] = AdvancedPack::PACK_FAKE_STOCK;
                    } else {
                        $catProduct['quantity_all_versions'] = $catProduct['quantity'];
                    }
                    if ($catProduct['reduction'] == 0 && isset($catProduct['specific_prices']) && is_array($catProduct['specific_prices']) && isset($catProduct['specific_prices']['reduction']) && $catProduct['specific_prices']['reduction'] > 0) {
                        $catProduct['price_without_reduction'] = AdvancedPack::getPackPrice((int)$catProduct['id_product'], $useTax, false, true, 6, array(), array(), array(), false);
                    }
                }
            }
            self::$actionProductListModifierProcessing = false;
        }
    }
    public function hookActionObjectCombinationDeleteAfter($params)
    {
        if (!empty(AdvancedPack::$actionRemoveOldPackDataProcessing)) {
            return;
        }
        $combination = $params['object'];
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'pm_advancedpack_cart_products` WHERE `cleaned`=0 AND `id_product_attribute_pack`='.(int)$combination->id);
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'pm_advancedpack_products_attributes` WHERE `id_product_attribute`='.(int)$combination->id);
        $sql = new DbQuery();
        $sql->select('DISTINCT `id_product_attribute_pack`, `id_pack`');
        $sql->from('pm_advancedpack_cart_products');
        $sql->where('`id_order` IS NULL');
        $sql->where('`id_product_attribute`=' . (int)$combination->id);
        $packsInCart = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if (AdvancedPackCoreClass::_isFilledArray($packsInCart)) {
            foreach ($packsInCart as $packInCart) {
                if (empty($packInCart['id_product_attribute_pack']) || empty($packInCart['id_pack'])) {
                    continue;
                }
                AdvancedPack::setStockAvailableQuantity($packInCart['id_pack'], $packInCart['id_product_attribute_pack'], 0, false);
            }
        }
        AdvancedPack::clearAP5Cache();
    }
    private function _postProcessAdminProducts($idPack, $isNewPack = false, $isMajorUpdate = false)
    {
        $pack = new AdvancedPack($idPack);
        if (Validate::isLoadedObject($pack)) {
            if (Tools::getIsset('ap5_productList') && self::_isFilledArray(Tools::getValue('ap5_productList'))) {
                $packInformations = array();
                $packPositions = array();
                $packSettings = array('fixedPrice' => null, 'allowRemoveProduct' => false);
                $hasAlwaysUseReducEntries = true;
                if (Tools::getIsset('ap5_pack_positions') && Tools::getValue('ap5_pack_positions')) {
                    $packPositions = explode(',', Tools::getValue('ap5_pack_positions'));
                }
                if (Tools::getIsset('ap5_price_rules') && Tools::getValue('ap5_price_rules') == 3 && Tools::getIsset('ap5_fixed_pack_price')) {
                    $packSettings['fixedPrice'] = Tools::getValue('ap5_fixed_pack_price');
                    if (is_array($packSettings['fixedPrice'])) {
                        $packSettings['fixedPrice'] = array_map('floatval', $packSettings['fixedPrice']);
                        $customerGroupId = (int)Configuration::get('PS_CUSTOMER_GROUP');
                        if (isset($packSettings['fixedPrice'][$customerGroupId])) {
                            $defaultFixedPriceValue = $packSettings['fixedPrice'][$customerGroupId];
                        } else {
                            $defaultFixedPriceValue = current($packSettings['fixedPrice']);
                        }
                        foreach (Group::getGroups(Context::getContext()->language->id, true) as $group) {
                            if (!isset($packSettings['fixedPrice'][(int)$group['id_group']])) {
                                $packSettings['fixedPrice'][(int)$group['id_group']] = $defaultFixedPriceValue;
                            }
                        }
                    } else {
                        $packSettings['fixedPrice'] = array();
                    }
                }
                $combinationsInformations = array();
                foreach (Tools::getValue('ap5_productList') as $idProductPack) {
                    $customCombinations = (Tools::getIsset('ap5_customCombinations-' . $idProductPack) && Tools::getValue('ap5_customCombinations-' . $idProductPack) ? Tools::getValue('ap5_combinationInclude-' . $idProductPack) : array());
                    $combinationsInformations[$idProductPack] = array();
                    foreach ($customCombinations as $idProductAttribute) {
                        if (!Tools::getValue('ap5_combinationReductionType-' . $idProductPack . '-' . $idProductAttribute)) {
                            $combinationsInformations[$idProductPack][$idProductAttribute] = array(
                                'reduction_amount' => null,
                                'reduction_type' => null,
                            );
                            continue;
                        }
                        $combinationsInformations[$idProductPack][$idProductAttribute] = array(
                            'reduction_amount' => (Tools::getValue('ap5_combinationReductionType-' . $idProductPack . '-' . $idProductAttribute) == 'percentage' ? Tools::getValue('ap5_combinationReductionAmount-' . $idProductPack . '-' . $idProductAttribute) / 100 : Tools::getValue('ap5_combinationReductionAmount-' . $idProductPack . '-' . $idProductAttribute)),
                            'reduction_type' => Tools::getValue('ap5_combinationReductionType-' . $idProductPack . '-' . $idProductAttribute),
                        );
                    }
                    $defaultCombinationId = (int)Product::getDefaultAttribute((int)Tools::getValue('ap5_originalIdProduct-' . $idProductPack));
                    if (Tools::getIsset('ap5_customCombinations-' . $idProductPack) && Tools::getValue('ap5_customCombinations-' . $idProductPack) && Tools::getValue('ap5_defaultCombination-' . $idProductPack)) {
                        $defaultCombinationId = (int)Tools::getValue('ap5_defaultCombination-' . $idProductPack);
                    }
                    $packInformations[$idProductPack] = array(
                        'id_product_pack' => (Tools::strlen($idProductPack) != 16 && is_numeric($idProductPack) && $idProductPack ? (int)$idProductPack : null),
                        'id_pack' => $idPack,
                        'id_product' => Tools::getValue('ap5_originalIdProduct-' . $idProductPack),
                        'quantity' => Tools::getValue('ap5_quantity-' . $idProductPack),
                        'reduction_amount' => (Tools::getValue('ap5_reductionType-' . $idProductPack) == 'percentage' ? Tools::getValue('ap5_reductionAmount-' . $idProductPack) / 100 : Tools::getValue('ap5_reductionAmount-' . $idProductPack)),
                        'reduction_type' => Tools::getValue('ap5_reductionType-' . $idProductPack),
                        'exclusive' => (Tools::getIsset('ap5_exclusive-' . $idProductPack) && Tools::getValue('ap5_exclusive-' . $idProductPack) ? (int)Tools::getValue('ap5_exclusive-' . $idProductPack) : 0),
                        'use_reduc' => (Tools::getIsset('ap5_useReduc-' . $idProductPack) && Tools::getValue('ap5_useReduc-' . $idProductPack) ? (int)Tools::getValue('ap5_useReduc-' . $idProductPack) : 0),
                        'position' => array_search($idProductPack, $packPositions),
                        'default_id_product_attribute' => $defaultCombinationId,
                        'customCombinations' => $customCombinations,
                        'combinationsInformations' => (isset($combinationsInformations[$idProductPack]) ? $combinationsInformations[$idProductPack] : array()),
                        'customCustomizationField' => (Tools::getIsset('ap5_customizationFields-' . $idProductPack) && Tools::getValue('ap5_customizationFields-' . $idProductPack) ? Tools::getValue('ap5_customizationFieldInclude-' . $idProductPack) : array()),
                    );
                    $hasAlwaysUseReducEntries &= $packInformations[$idProductPack]['use_reduc'];
                }
                if ($hasAlwaysUseReducEntries && Tools::getIsset('ap5_price_rules') && Tools::getValue('ap5_price_rules') == 4 && Tools::getIsset('ap5_allow_remove_product') && Tools::getValue('ap5_allow_remove_product') == 1 && sizeof($packInformations) >= 2) {
                    $packSettings['allowRemoveProduct'] = (bool)Tools::getValue('ap5_allow_remove_product');
                }
                if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                    $validator = Symfony\Component\Validator\Validation::createValidator();
                    $constraintWithCombinationsDiscount = new Symfony\Component\Validator\Constraints\Collection(array(
                        'ap5_quantity' => array(
                            new Symfony\Component\Validator\Constraints\NotBlank(),
                            new Symfony\Component\Validator\Constraints\GreaterThanOrEqual(1),
                        ),
                        'ap5_reductionAmount' => array(),
                    ));
                    $constraintWithoutCombinationsDiscount = new Symfony\Component\Validator\Constraints\Collection(array(
                        'ap5_quantity' => array(
                            new Symfony\Component\Validator\Constraints\NotBlank(),
                            new Symfony\Component\Validator\Constraints\GreaterThanOrEqual(1),
                        ),
                        'ap5_reductionAmount' => array(
                            new Symfony\Component\Validator\Constraints\NotBlank(),
                            new Symfony\Component\Validator\Constraints\GreaterThanOrEqual(0),
                        ),
                    ));
                    $combinationConstraint = new Symfony\Component\Validator\Constraints\Collection(array(
                        'ap5_combinationReductionAmount' => array(
                            new Symfony\Component\Validator\Constraints\NotBlank(),
                            new Symfony\Component\Validator\Constraints\GreaterThanOrEqual(0),
                        ),
                    ));
                    $errorsToReport = array();
                    foreach ($packInformations as $idProductPack => $packInformationsRow) {
                        $hasCombinationsDiscounts = false;
                        foreach ($packInformationsRow['combinationsInformations'] as $idProductAttribute => $combinationsInformationsRow) {
                            if (empty($combinationsInformationsRow['reduction_type'])) {
                                continue;
                            }
                            $hasCombinationsDiscounts = true;
                            break;
                        }
                        if ($hasCombinationsDiscounts) {
                            $constraint = $constraintWithCombinationsDiscount;
                        } else {
                            $constraint = $constraintWithoutCombinationsDiscount;
                        }
                        $violations = $validator->validate(array(
                            'ap5_quantity' => $packInformationsRow['quantity'],
                            'ap5_reductionAmount' => $packInformationsRow['reduction_amount'],
                        ), $constraint);
                        foreach ($violations as $violation) {
                            $errorsToReport[str_replace(array('[', ']'), array('', ''), $violation->getPropertyPath()) . '-' . $idProductPack][] = $violation->getMessage();
                        }
                        if (empty($packInformationsRow['reduction_type']) && $hasCombinationsDiscounts) {
                            foreach ($combinationsInformations[$idProductPack] as $idProductAttribute => $combinationsInformationsRow) {
                                if (empty($combinationsInformationsRow['reduction_type'])) {
                                    continue;
                                }
                                $violations = $validator->validate(array(
                                    'ap5_combinationReductionAmount' => $combinationsInformationsRow['reduction_amount'],
                                ), $combinationConstraint);
                                foreach ($violations as $violation) {
                                    $errorsToReport[str_replace(array('[', ']'), array('', ''), $violation->getPropertyPath()) . '-' . $idProductPack . '-' . $idProductAttribute][] = $violation->getMessage();
                                }
                            }
                        }
                    }
                    if (sizeof($errorsToReport)) {
                        header("HTTP/1.0 400 Bad Request");
                        header("Content-Type: application/json");
                        die(json_encode($errorsToReport));
                    }
                }
                if (self::_isFilledArray($packInformations)) {
                    if (!$pack->updatePackContent($packInformations, $packSettings, $isNewPack, $isMajorUpdate)) {
                        throw new PrestaShopException($this->l('Unable to update pack content'));
                    }
                }
            }
        }
    }
    public function hookActionAdminProductsListingResultsModifier($params)
    {
        if (isset($params['list']) && self::_isFilledArray($params['list'])) {
            foreach ($params['list'] as &$product) {
                if (AdvancedPack::isValidPack($product['id_product'])) {
                    $product['sav_quantity'] = AdvancedPack::getPackAvailableQuantity($product['id_product']);
                }
            }
        } elseif (version_compare(_PS_VERSION_, '1.7.0.0', '>=') && isset($params['products']) && self::_isFilledArray($params['products'])) {
            foreach ($params['products'] as &$product) {
                if (AdvancedPack::isValidPack($product['id_product'])) {
                    $product['sav_quantity'] = AdvancedPack::getPackAvailableQuantity($product['id_product']);
                    $product['price'] = Tools::displayPrice(AdvancedPack::getPackPrice($product['id_product'], false, true));
                    $product['price_final'] = Tools::displayPrice(AdvancedPack::getPackPrice($product['id_product'], true, true));
                }
            }
        }
    }
    public function hookActionAdminControllerSetMedia($params)
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=') && isset($this->context->controller->controller_name) && $this->context->controller->controller_name == 'AdminProducts') {
            $id_product = (int)$this->getCurrentProductIdFromRequest();
            $product = new Product((int)$id_product, true, $this->context->language->id, $this->context->shop->id);
        } else {
            $id_product = (int)Tools::getValue('id_product');
            $product = false;
        }
        if (Tools::getIsset('id_product') && (int)$id_product > 0) {
            $product = new Product((int)$id_product, true, $this->context->language->id, $this->context->shop->id);
        }
        if (Tools::getIsset('newpack') || Tools::getIsset('is_real_new_pack') || (Validate::isLoadedObject($product) && AdvancedPack::isValidPack($product->id))) {
            $this->context->controller->addCSS($this->_path . 'views/css/admin-new-pack.css', 'all');
            $this->context->controller->addJS($this->_path . 'views/js/admin-new-pack.js');
            if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                $this->context->controller->addJqueryPlugin('tablednd');
            }
        } elseif (Validate::isLoadedObject($product) && !AdvancedPack::isValidPack($product->id)) {
            if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                $this->context->controller->addJS($this->_path . 'views/js/admin-related-pack.js');
            }
            $this->context->controller->addCSS($this->_path . 'views/css/admin-product-tab.css', 'all');
        }
    }
    public function getSfContainer()
    {
        // Note Team Validation - Requiert une globale pour obtenir l'objet Request
        global $kernel;
        if (empty($kernel)) {
            throw new Exception('Symfony kernel is not defined');
        }
        return $kernel->getContainer();
    }
    protected function getSfRequest()
    {
        try {
            $request = null;
            if (version_compare(_PS_VERSION_, '1.7.4.0', '>=')) {
                $request = $this->getSfContainer()->get('request_stack')->getCurrentRequest();
            } else {
                $request = $this->getSfContainer()->get('request');
            }
            if (!is_object($request)) {
                return null;
            }
            return $request;
        } catch (Exception $e) {
        }
        return null;
    }
    protected function getCurrentProductIdFromRequest()
    {
        $request = $this->getSfRequest();
        if (empty($request)) {
            return null;
        }
        return (int)$request->get('id');
    }
    public function hookDisplayBackOfficeHeader($params)
    {
        $currentIdProduct = null;
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=') && isset($this->context->controller->controller_name) && $this->context->controller->controller_name == 'AdminProducts') {
            $currentIdProduct = (int)$this->getCurrentProductIdFromRequest();
        } elseif (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
            $currentIdProduct = (int)Tools::getValue('id_product');
        }
        $checkProfilePermissionsPacks = Profile::getProfileAccess($this->context->employee->id_profile, Tab::getIdFromClassName('AdminPacks'));
        $checkProfilePermissionsProducts = Profile::getProfileAccess($this->context->employee->id_profile, Tab::getIdFromClassName('AdminProducts'));
        $canEditPacks = (!empty($checkProfilePermissionsPacks['edit']) || !empty($checkProfilePermissionsProducts['edit']));
        if ($canEditPacks) {
            $useCache = true;
            if (Tools::getValue('configure') == 'pm_advancedpack' || $this->context->controller instanceof AdminPacksController) {
                $useCache = false;
            }
            $packListToFix = AdvancedPack::getPackListToFix($useCache);
            if (AdvancedPackCoreClass::_isFilledArray($packListToFix)) {
                $idPackListToFix = array();
                foreach ($packListToFix as $idPack => $idProductList) {
                    if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                        $editPackLink = $this->context->link->getAdminLink('AdminProducts', true, array('id_product' => (int)$idPack)) . '#pm_advancedpack';
                    } else {
                        $editPackLink = $this->context->link->getAdminLink('AdminProducts') . '&key_tab=ModulePm_advancedpack&updateproduct&id_product=' . (int)$idPack;
                    }
                    $idPackListToFix[(int)$idPack] = array(
                        'idPack' => (int)$idPack,
                        'editPackLink' => $editPackLink,
                        'idProductList' => $idProductList,
                    );
                }
                $this->context->smarty->assign(array(
                    'idPackListToFix' => $idPackListToFix,
                ));
                $this->context->controller->warnings[] = $this->display(__FILE__, Tools::substr(_PS_VERSION_, 0, 3) . '/admin-missing-pack-combinations-alert.tpl');
            }
        }
        if (Tools::getIsset('ap5Error') && Tools::getValue('ap5Error')) {
            if (Tools::getValue('ap5Error') == 1) {
                $this->context->controller->errors[] = $this->displayName . ' - ' . $this->l('You can\'t duplicate a pack.');
            }
        } elseif (Tools::getValue('controller') == 'AdminCarts' && Tools::getIsset('viewcart') && (int)Tools::getValue('id_cart')) {
            $cart = new Cart((int)Tools::getValue('id_cart'));
            if (Validate::isLoadedObject($cart)) {
                return $this->replacePackSmallAttributes(array('cart' => $cart), 'displayBackOfficeHeader');
            }
        } elseif (isset($this->context->controller) && is_object($this->context->controller) && ($this->context->controller instanceof AdminAttributeGeneratorController) && Tools::getIsset('attributegenerator') && (int)Tools::getValue('id_product')) {
            $this->context->controller->addJquery();
            $this->context->controller->addJS($this->_path.'views/js/admin-global.js');
            Media::addJsDef(array(
                'ap5_attributePackId' => (int)AdvancedPack::getPackAttributeGroupId(),
            ));
        } elseif (Tools::getValue('controller') == 'AdminProducts' && $currentIdProduct) {
            $product = false;
            if ($currentIdProduct > 0) {
                $product = new Product((int)$currentIdProduct, true, $this->context->language->id, $this->context->shop->id);
            }
            $idPackList = AdvancedPack::getIdsPacks(true);
            if (Validate::isLoadedObject($product)) {
                if (AdvancedPack::isValidPack($product->id) && !AdvancedPack::isFromShop($product->id, Context::getContext()->shop->id) && count($this->context->controller->confirmations)) {
                    $this->context->controller->errors[] = $this->l('You must select the right shop in order to continue (where the pack has been created).');
                    return $this->display(__FILE__, Tools::substr(_PS_VERSION_, 0, 3) . '/admin-js-disable-product-edit.tpl');
                }
                if (!in_array($product->id, $idPackList) && Tools::getValue('key_tab') == 'Prices' && !count($this->context->controller->errors) && Tools::getValue('conf') == 4) {
                    $this->updateRelatedPacks((int)$product->id);
                } else {
                    $this->context->controller->addJquery();
                    $this->context->controller->addJS($this->_path.'views/js/admin-global.js');
                    Media::addJsDef(array(
                        'ap5_attributePackId' => (int)AdvancedPack::getPackAttributeGroupId(),
                    ));
                }
            }
        } elseif ($this->context->controller instanceof AdminAttributesGroupsController && method_exists($this->context->controller, 'addRowActionSkipList')) {
            $attributeGroupId = (int)AdvancedPack::getPackAttributeGroupId();
            if (!empty($attributeGroupId)) {
                $this->context->controller->addRowActionSkipList('view', $attributeGroupId);
                $this->context->controller->addRowActionSkipList('edit', $attributeGroupId);
                $this->context->controller->addRowActionSkipList('delete', $attributeGroupId);
            }
        } elseif ($this->context->controller instanceof AdminOrdersController && Tools::getIsset('ajax') && Tools::getValue('action') == 'searchProducts' && method_exists($this->context->controller, 'ajaxProcessSearchProducts')) {
            $this->context->controller->ajaxProcessSearchProducts();
            $ajaxJsonContent = json_decode($this->context->controller->content);
            if (is_object($ajaxJsonContent) && isset($ajaxJsonContent->products) && sizeof($ajaxJsonContent->products)) {
                $idPackList = AdvancedPack::getIdsPacks(true);
                foreach ($ajaxJsonContent->products as $k => $product) {
                    if (!empty($product->id_product) && in_array($product->id_product, $idPackList)) {
                        unset($ajaxJsonContent->products[$k]);
                    }
                }
                if (!sizeof($ajaxJsonContent->products)) {
                    $ajaxJsonContent->found = false;
                }
                die(Tools::jsonEncode($ajaxJsonContent));
            }
        }
        if ($currentIdProduct && version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $product = new Product((int)$currentIdProduct, true, $this->context->language->id, $this->context->shop->id);
            $config = $this->_getModuleConfiguration();
            if (AdvancedPack::isValidPack($currentIdProduct) || Tools::getValue('is_real_new_pack')) {
                Media::addJsDef(array(
                    $this->name => array(
                        'tabName' => $this->l('Pack configuration'),
                        'tabContent' => $this->getAdminNewPackOutput($product),
                        'packIsVirtual' => (bool)$product->is_virtual,
                    ),
                    'ap5_displayMode' => $config['displayMode'],
                ));
            } else {
                $packListId = AdvancedPack::getIdPacksByIdProduct((int)$product->id);
                $packList = array();
                $packObjects = array();
                if (self::_isFilledArray($packListId)) {
                    foreach ($packListId as $idPack) {
                        $packContent = AdvancedPack::getPackContent($idPack, null, true);
                        $packList[$idPack] = $packContent;
                        $packObjects[$idPack] = new AdvancedPack($idPack, false, $this->context->language->id);
                        $packObjects[$idPack]->urlAdminProduct = $this->context->link->getAdminLink('AdminProducts', true, array('id_product' => (int)$idPack)) . '#pm_advancedpack';
                    }
                }
                if (Validate::isLoadedObject($product) && !Pack::isPack($product->id)) {
                    $this->context->smarty->assign(array(
                        'currentProduct' => $product,
                        'packList' => $packList,
                        'packObjects' => $packObjects,
                        'createNewPackUrl' => Link::getUrlSmarty(array('entity' => 'sf', 'route' => 'admin_product_new')) . '&new_pack=1',
                    ));
                    Media::addJsDef(array(
                        $this->name => array(
                            'tabName' => $this->l('Related packs'),
                            'tabContent' => $this->display(__FILE__, Tools::substr(_PS_VERSION_, 0, 3) . '/admin-product-tab-packs-list.tpl'),
                        ),
                        'ap5_displayMode' => $config['displayMode'],
                    ));
                }
            }
        }
    }
    private function getAdminNewPackOutput($product)
    {
        $isTempProduct = (version_compare(_PS_VERSION_, '1.7.0.0', '>=') && Validate::isLoadedObject($product) && $product->state == Product::STATE_TEMP && Tools::getValue('is_real_new_pack'));
        if (Validate::isLoadedObject($product) && !AdvancedPack::isFromShop($product->id, Context::getContext()->shop->id) && (version_compare(_PS_VERSION_, '1.7.0.0', '<') || !$isTempProduct)) {
            $this->context->controller->errors[] = $this->l('You must select the right shop in order to continue (where the pack has been created).');
            return $this->display(__FILE__, Tools::substr(_PS_VERSION_, 0, 3) . '/admin-js-disable-product-edit.tpl');
        }
        $packPriceRules = 4;
        $packFixedPrice = array();
        $hasAlwaysUseReducEntries = true;
        $packCheckAllExclusive = true;
        $reductionAmountTable = $reductionPercentageTable = $packContent = $hasDiscountOnCombinations = array();
        if ((Validate::isLoadedObject($product) && AdvancedPack::isValidPack($product->id)) || $isTempProduct) {
            $packContent = AdvancedPack::getPackContent($product->id, null, true);
            $packFixedPrice = AdvancedPack::getPackFixedPrice($product->id);
            $warehouseFinalListId = array();
            if (version_compare(_PS_VERSION_, '1.7.0.0', '>=') && $isTempProduct) {
                $packContent = array();
            }
            foreach ($packContent as $idProductPack => $packProduct) {
                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && Product::usesAdvancedStockManagement($packProduct['productObj']->id)) {
                    $warehouseList = Warehouse::getProductWarehouseList($packProduct['productObj']->id, ($packProduct['productObj']->hasAttributes() ? Product::getDefaultAttribute($packProduct['productObj']->id) : 0));
                    if (self::_isFilledArray($warehouseList)) {
                        $warehouseListId = array();
                        foreach ($warehouseList as $warehouseRow) {
                            $warehouseListId[] = (int)$warehouseRow['id_warehouse'];
                        }
                        $warehouseListId = array_unique($warehouseListId);
                        if (sizeof($warehouseListId)) {
                            $warehouseFinalListId[] = current($warehouseListId);
                        }
                    }
                }
                if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                    $packContent[$idProductPack]['urlAdminProduct'] = $this->context->link->getAdminLink('AdminProducts', true, array('id_product' => $packProduct['productObj']->id));
                }
                $packContent[$idProductPack]['productCombinations'] = $packProduct['productObj']->getAttributesResume($this->context->language->id);
                $packContent[$idProductPack]['productCombinationsWhiteList'] = AdvancedPack::getProductAttributeWhiteList($packProduct['id_product_pack']);
                $packContent[$idProductPack]['productHasRequiredCustomizationFields'] = self::_isFilledArray($packProduct['productObj']->getRequiredCustomizableFields());
                $packContent[$idProductPack]['productCustomizationFields'] = AdvancedPack::getProductPackCustomizationFields($packProduct['productObj']->id);
                $packContent[$idProductPack]['productCustomizationFieldsWhiteList'] = AdvancedPack::getProductCustomizationFieldWhiteList($packProduct['id_product_pack']);
                if (!isset($hasDiscountOnCombinations[$idProductPack])) {
                    $hasDiscountOnCombinations[$idProductPack] = false;
                }
                if (!$hasDiscountOnCombinations[$idProductPack] && isset($packProduct['combinationsInformations'])) {
                    foreach ($packProduct['combinationsInformations'] as $combinationInformation) {
                        if ($combinationInformation['reduction_type'] != null) {
                            $hasDiscountOnCombinations[$idProductPack] = true;
                            break;
                        }
                    }
                }
                if ($packProduct['reduction_type'] == 'amount') {
                    $reductionAmountTable[] = $packProduct['reduction_amount'];
                } elseif ($packProduct['reduction_type'] == 'percentage') {
                    $reductionPercentageTable[] = $packProduct['reduction_amount'];
                }
                $hasAlwaysUseReducEntries &= $packProduct['use_reduc'];
                $packCheckAllExclusive &= $packProduct['exclusive'];
            }
            $reductionPercentageTable = array_unique($reductionPercentageTable);
            $warehouseFinalListId = array_unique($warehouseFinalListId);
            if (array_sum($hasDiscountOnCombinations) > 0) {
                $packPriceRules = 2;
            } elseif (is_array($packFixedPrice) && array_sum($packFixedPrice) > 0) {
                $packPriceRules = 3;
            } elseif (count($reductionPercentageTable) == 1 && !count($reductionAmountTable)) {
                if (current($reductionPercentageTable) == 0) {
                    $packPriceRules = 4;
                } else {
                    $packPriceRules = 1;
                }
            } elseif (count($reductionPercentageTable) || count($reductionAmountTable)) {
                $packPriceRules = 2;
            }
        }
        $config = $this->_getModuleConfiguration();
        $packIdGroupList = array();
        $defaultGroups = array();
        $defaultGroups[] = Configuration::get('PS_UNIDENTIFIED_GROUP');
        $defaultGroups[] = Configuration::get('PS_GUEST_GROUP');
        foreach (Group::getGroups(Context::getContext()->language->id, true) as $group) {
            if (in_array((int)$group['id_group'], $defaultGroups)) {
                continue;
            }
            $packIdGroupList[(int)$group['id_group']] = $group['name'];
        }
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->context->smarty->assign('link', Context::getContext()->link);
            $this->context->smarty->assign('pmlink', Context::getContext()->link);
        }
        $this->context->smarty->assign(array(
            'idTaxRulesGroup' => (Validate::isLoadedObject($product) && AdvancedPack::isValidPack($product->id) ? (int)$product->getIdTaxRulesGroup() : null),
            'idWarehouse' => (Validate::isLoadedObject($product) && AdvancedPack::isValidPack($product->id) ? (int)current($warehouseFinalListId) : null),
            'packContent' => $packContent,
            'defaultCurrency' => Currency::getDefaultCurrency(),
            'packPriceRules' => $packPriceRules,
            'packFixedPrice' => $packFixedPrice,
            'packIdGroupList' => $packIdGroupList,
            'packIdGroupDefault' => (int)Configuration::get('PS_CUSTOMER_GROUP'),
            'packFixedPercentage' => ($packPriceRules == 1 && count($reductionPercentageTable) == 1 ? current($reductionPercentageTable) * 100 : 0),
            'packAllowRemoveProduct' => (Validate::isLoadedObject($product) && AdvancedPack::isValidPack($product->id) && AdvancedPack::getPackAllowRemoveProduct($product->id) && $packPriceRules == 4 && $hasAlwaysUseReducEntries && sizeof($packContent) >= 2),
            'packCheckAllUseReduc' => $hasAlwaysUseReducEntries,
            'packCheckAllExclusive' => $packCheckAllExclusive,
            'packClassicPrice' => 0,
            'packClassicPriceWt' => 0,
            'discountPercentage' => number_format(0, 2),
            'packPrice' => 0,
            'packPriceWt' => 0,
            'totalPackEcoTax' => 0,
            'totalPackEcoTaxWt' => 0,
            'totalFinal' => 0,
            'totalFinalWt' => 0,
            'hasDiscountOnCombinations' => $hasDiscountOnCombinations,
            'packDisplayMode' => $config['displayMode'],
            'adminTaxesLink' => $this->context->link->getAdminLink('AdminTaxes'),
            'adminPreferencesLink' => $this->context->link->getAdminLink('AdminPPreferences'),
            'adminProductsLink' => $this->context->link->getAdminLink('AdminProducts'),
            'adminModulesLink' => $this->context->link->getAdminLink('AdminModules'),
        ));
        return $this->display(__FILE__, Tools::substr(_PS_VERSION_, 0, 3) . '/admin-product-tab-new-pack.tpl');
    }
    public function hookDisplayAdminProductsExtra($params)
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->unregisterHook('displayAdminProductsExtra');
            return;
        }
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->context->controller->errors[] = $this->l('You must select a specific shop in order to continue.');
            return $this->display(__FILE__, Tools::substr(_PS_VERSION_, 0, 3) . '/admin-js-disable-product-edit.tpl');
        }
        $id_product = (int)Tools::getValue('id_product');
        $packList = array();
        $packObjects = array();
        $product = false;
        if (Tools::getIsset('id_product') && (int)$id_product > 0) {
            $product = new Product((int)$id_product, true, $this->context->language->id, $this->context->shop->id);
        }
        if (Validate::isLoadedObject($product) && !AdvancedPack::isValidPack($product->id)) {
            $packListId = AdvancedPack::getIdPacksByIdProduct((int)$product->id);
            if (self::_isFilledArray($packListId)) {
                foreach ($packListId as $idPack) {
                    $packContent = AdvancedPack::getPackContent($idPack, null, true);
                    $packList[$idPack] = $packContent;
                    $packObjects[$idPack] = new AdvancedPack($idPack, false, $this->context->language->id);
                }
            }
            if (Validate::isLoadedObject($product)) {
                $this->context->smarty->assign(array(
                    'currentProduct' => $product,
                    'currentProductIsPack' => Pack::isPack($product->id),
                    'packList' => $packList,
                    'packObjects' => $packObjects,
                ));
                return $this->display(__FILE__, Tools::substr(_PS_VERSION_, 0, 3) . '/admin-product-tab-packs-list.tpl');
            }
        } elseif ((Validate::isLoadedObject($product) && AdvancedPack::isValidPack($product->id)) || (Tools::getValue('id_product') == 0 && Tools::getIsset('updateproduct') && Tools::getIsset('is_real_new_pack'))) {
            return $this->getAdminNewPackOutput($product);
        } else {
            $this->context->controller->errors[] = $this->l('This product must be saved in order to proceed.');
            return false;
        }
    }
    public function hookDisplayShoppingCartFooter($params)
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            return;
        }
        return $this->replacePackSmallAttributes($params, 'displayShoppingCartFooter');
    }
    public function hookDisplayBeforeBodyClosingTag($params)
    {
        return $this->replacePackSmallAttributes($params, 'displayBeforeBodyClosingTag');
    }
    public function hookDisplayFooter($params)
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            return;
        }
        if ((int)Group::getCurrent()->price_display_method) {
            if ((isset($this->context->controller->step) && $this->context->controller->step == 3 || Configuration::get('PS_ORDER_PROCESS_TYPE')) && get_class($this->context->controller) == 'OrderController') {
                $this->context->controller->addJqueryPlugin('typewatch');
                $this->context->controller->addJS(_THEME_JS_DIR_.'cart-summary.js');
            }
            $this->context->controller->addJS($this->_path.'views/js/shopping-cart.js');
        }
        $this->context->controller->addJS($this->_path.'views/js/shopping-cart-refresh.js');
        return $this->replacePackSmallAttributes($params, 'displayFooter');
    }
    public function hookDisplayFooterProduct($params)
    {
        $config = $this->_getModuleConfiguration();
        if (isset($config['enablePackCrossSellingBlock']) && $config['enablePackCrossSellingBlock']) {
            $currentProductObj = $this->getCurrentProduct();
            if (Validate::isLoadedObject($currentProductObj) && self::_isFilledArray(AdvancedPack::getIdPacksByIdProduct($currentProductObj->id))) {
                $packList = array();
                if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                    $assembler = new ProductAssembler($this->context);
                    $imageRetriever = new PrestaShop\PrestaShop\Adapter\Image\ImageRetriever($this->context->link);
                    $presenterFactory = new ProductPresenterFactory($this->context);
                    $presentationSettings = $presenterFactory->getPresentationSettings();
                    $presenter = new PrestaShop\PrestaShop\Core\Product\ProductPresenter(
                        $imageRetriever,
                        $this->context->link,
                        new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter(),
                        new PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever(),
                        $this->context->getTranslator()
                    );
                }
                foreach (AdvancedPack::getIdPacksByIdProduct($currentProductObj->id) as $idPack) {
                    $productPackObj = new Product($idPack, true, $this->context->language->id);
                    if (Validate::isLoadedObject($productPackObj) && $productPackObj->active && AdvancedPack::isValidPack($idPack, true)) {
                        $packList[$idPack] = array(
                            'idPack' => $idPack,
                            'packContent' => AdvancedPack::getPackContent($idPack, null, true),
                            'packObj' => $productPackObj
                        );
                        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                            $packList[$idPack]['presentation'] = $presenter->present(
                                $presentationSettings,
                                $assembler->assembleProduct(array('id_product' => (int)$idPack)),
                                $this->context->language
                            );
                        } else {
                            $packList[$idPack]['presentation'] = array();
                        }
                    }
                }
                if (self::_isFilledArray($packList)) {
                    $this->_assignSmartyImageTypeVars();
                    if (isset($config['orderByCrossSelling']) && $config['orderByCrossSelling']) {
                        ksort($packList);
                        if ($config['orderByCrossSelling'] == 'date_add_desc') {
                            krsort($packList);
                        } elseif ($config['orderByCrossSelling'] == 'price_asc' || $config['orderByCrossSelling'] == 'price_desc') {
                            foreach ($packList as $idPack => &$packRow) {
                                $packRow['packPrice'] = AdvancedPack::getPackPrice($idPack, false);
                            }
                            self::$_sortArrayByKeyColumn = 'packPrice';
                            self::$_sortArrayByKeyOrder = ($config['orderByCrossSelling'] == 'price_asc' ? 1 : 2);
                            uasort($packList, 'self::sortArrayByKey');
                        } elseif ($config['orderByCrossSelling'] == 'random') {
                            shuffle($packList);
                        }
                    }
                    if (isset($config['limitPackNbCrossSelling']) && (int)$config['limitPackNbCrossSelling'] > 0) {
                        $packList = array_slice($packList, 0, $config['limitPackNbCrossSelling']);
                    }
                    $this->context->smarty->assign(array(
                        'bootstrapTheme' => (bool)$config['bootstrapTheme'],
                        'enableViewThisPackButton' => (bool)$config['enableViewThisPackButton'],
                        'enableBuyThisPackButton' => (bool)$config['enableBuyThisPackButton'],
                        'packShowProductsPrice' => (isset($config['showProductsPrice']) ? $config['showProductsPrice'] : $this->_defaultConfiguration['showProductsPrice']),
                    ));
                    $this->context->smarty->assign(array('packList' => $packList));
                    return $this->display(__FILE__, 'views/templates/front/' . Tools::substr(_PS_VERSION_, 0, 3) . '/product-footer-pack-list.tpl');
                }
            }
        }
    }
    public function hookDisplayRightColumnProduct($params)
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            return;
        }
        $config = $this->_getModuleConfiguration();
        if ($config['displayMode'] == self::DISPLAY_ADVANCED) {
            return false;
        } else {
            $product = $this->getCurrentProduct();
            if (Validate::isLoadedObject($product) && AdvancedPack::isValidPack((int)$product->id)) {
                $this->_assignSmartyVars('pack', (int)$product->id);
                $packAttributesList = array();
                $this->context->smarty->assign(array(
                    'ap5_firstExecution' => true,
                    'ap5_buyBlockPackPriceContainer' => Tools::jsonEncode($this->displayPackPriceContainer((int)$product->id, $packAttributesList))
                ));
                $this->_assignSmartyVars('pack', (int)$product->id);
                return $this->display(__FILE__, 'views/templates/hook/' . Tools::substr(_PS_VERSION_, 0, 3) . '/pack-product-list-extra-right.tpl');
            }
        }
        return false;
    }
    public function hookDisplayProductAdditionalInfo($params)
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
            return;
        }
        if (self::$_preventInfiniteLoop) {
            return;
        }
        $config = $this->_getModuleConfiguration();
        if ($config['displayMode'] == self::DISPLAY_ADVANCED) {
            return;
        }
        self::$_preventInfiniteLoop = true;
        $product = $this->getCurrentProduct();
        if (Validate::isLoadedObject($product) && AdvancedPack::isValidPack((int)$product->id)) {
            $this->_assignSmartyVars('pack', (int)$product->id);
            $packAttributesList = array();
            $this->context->smarty->assign(array(
                'ap5_firstExecution' => true,
                'ap5_buyBlockPackPriceContainer' => Tools::jsonEncode($this->displayPackPriceContainer((int)$product->id, $packAttributesList))
            ));
            $this->_assignSmartyVars('pack', (int)$product->id);
            $return = $this->display(__FILE__, 'views/templates/hook/' . Tools::substr(_PS_VERSION_, 0, 3) . '/pack-product-list-extra-right.tpl');
            self::$_preventInfiniteLoop = false;
            return $return;
        }
    }
    public function getFormatedPackAttributes(Cart $cart)
    {
        $cartPackProducts = array();
        if (Validate::isLoadedObject($cart)) {
            if ($this->context->customer == null) {
                $this->context->customer = new Customer($cart->id_customer);
            }
            $products = $cart->getProducts();
            if (self::_isFilledArray($products)) {
                foreach ($products as $cartProduct) {
                    if ($cartProduct['id_product_attribute'] && AdvancedPack::isValidPack($cartProduct['id_product'])) {
                        if (empty($cartProduct['attributes_small'])) {
                            continue;
                        }
                        $cartPackProducts[$cartProduct['attributes_small']] = array(
                            'cart' => $this->displayPackContent($cartProduct['id_product'], $cartProduct['id_product_attribute'], self::PACK_CONTENT_SHOPPING_CART),
                            'block_cart' => $this->displayPackContent($cartProduct['id_product'], $cartProduct['id_product_attribute'], self::PACK_CONTENT_BLOCK_CART)
                        );
                    }
                }
            }
        }
        return $cartPackProducts;
    }
    protected function replacePackSmallAttributes($params, $fromHookName = false)
    {
        if ($fromHookName == 'displayFooter' && isset($this->context->controller->step) && $this->context->controller->step == 0 && get_class($this->context->controller) == 'OrderController') {
            return;
        }
        $cartPackProducts = array();
        if (isset($params['cart']) && Validate::isLoadedObject($params['cart'])) {
            $cartPackProducts = $this->getFormatedPackAttributes($params['cart']);
        }
        $this->context->smarty->assign(array('cartPackProducts' => $cartPackProducts));
        if ($fromHookName == 'displayBackOfficeHeader') {
            return $this->display(__FILE__, Tools::substr(_PS_VERSION_, 0, 3) . '/backoffice-footer.tpl');
        } else {
            return $this->display(__FILE__, Tools::substr(_PS_VERSION_, 0, 3) . '/footer.tpl');
        }
    }
    public function displayPackContent($idPack, $idProductAttribute, $contextType, $packProducts = array())
    {
        if ($idProductAttribute && AdvancedPack::isValidPack($idPack) || sizeof($packProducts)) {
            if (!sizeof($packProducts)) {
                $packProducts = AdvancedPack::getPackContent($idPack, $idProductAttribute);
            }
            if (self::_isFilledArray($packProducts)) {
                foreach ($packProducts as $key => $packProduct) {
                    $product = new Product((int)$packProduct['id_product'], false, $this->context->language->id);
                    $packProducts[$key]['product_name'] = $product->name;
                    $packProducts[$key]['quantity'] = (int)$packProducts[$key]['quantity'];
                    $attributeDatas = AdvancedPack::getProductAttributeList((isset($packProduct['id_product_attribute']) ? (int)$packProduct['id_product_attribute'] : (int)$packProduct['default_id_product_attribute']));
                    $packProducts[$key] = array_merge($packProducts[$key], $attributeDatas);
                }
                $this->context->smarty->assign(array('packProducts' => $packProducts));
                if ($contextType == self::PACK_CONTENT_SHOPPING_CART) {
                    return $this->display(__FILE__, Tools::substr(_PS_VERSION_, 0, 3) . '/pack-product-list-cart-summary.tpl');
                } elseif ($contextType == self::PACK_CONTENT_BLOCK_CART) {
                    return $this->display(__FILE__, Tools::substr(_PS_VERSION_, 0, 3) . '/pack-product-list-block-cart.tpl');
                } elseif ($contextType == self::PACK_CONTENT_ORDER_CONFIRMATION_EMAIL) {
                    return html_entity_decode(trim(strip_tags($this->display(__FILE__, Tools::substr(_PS_VERSION_, 0, 3) . '/pack-product-list-order-confirmation-email.tpl'))), ENT_QUOTES, 'UTF-8');
                }
            }
        }
    }
    public function displayPackContentTable($idPack, $packAttributesList, $packCompleteAttributesList, $packQuantityList = array(), $packExcludeList = array(), $packErrorsList = array(), $packFatalErrorsList = array(), $packForceHideInfoList = array())
    {
        $productPack = new Product((int)$idPack, true, $this->context->language->id);
        $productPack->ecotax = AdvancedPack::getPackEcoTax($idPack, $packAttributesList);
        $productPack->quantity = AdvancedPack::getPackAvailableQuantity($idPack, $packAttributesList, $packQuantityList, $packExcludeList);
        $productsPack = AdvancedPack::getPackContent($idPack, null, true, $packAttributesList, $packQuantityList);
        $config = $this->_getModuleConfiguration();
        $this->context->smarty->assign(array(
            'packDeviceIsMobile' => (method_exists($this->context, 'isMobile') ? $this->context->isMobile() : false),
            'packDeviceIsTablet' => (method_exists($this->context, 'isTablet') ? $this->context->isTablet() : false),
            'bootstrapTheme' => (bool)$config['bootstrapTheme'],
            'autoScrollBuyBlock' => (bool)$config['autoScrollBuyBlock'],
            'productsPack' => $productsPack,
            'packShowProductsThumbnails' => (isset($config['showProductsThumbnails']) ? $config['showProductsThumbnails'] : $this->_defaultConfiguration['showProductsThumbnails']),
            'packShowProductsPrice' => (isset($config['showProductsPrice']) ? $config['showProductsPrice'] : $this->_defaultConfiguration['showProductsPrice']),
            'packShowProductsAvailability' => (isset($config['showProductsAvailability']) ? $config['showProductsAvailability'] : $this->_defaultConfiguration['showProductsAvailability']),
            'packAvailableQuantity' => AdvancedPack::getPackAvailableQuantity($idPack, $packAttributesList, $packQuantityList, $packExcludeList),
            'packAvailableQuantityList' => AdvancedPack::getPackAvailableQuantityList($idPack, $packAttributesList),
            'packMaxImagesPerProduct' => AdvancedPack::getMaxImagesPerProduct($productsPack),
            'productsPackErrors' => $packErrorsList,
            'productsPackFatalErrors' => $packFatalErrorsList,
            'productsPackForceHideInfoList' => $packForceHideInfoList,
            'packAttributesList' => $packAttributesList,
            'packQuantityList' => $packQuantityList,
            'packCompleteAttributesList' => $packCompleteAttributesList,
            'packAllowRemoveProduct' => AdvancedPack::getPackAllowRemoveProduct($idPack),
            'packShowProductsQuantityWanted' => (isset($config['showProductsQuantityWanted']) ? $config['showProductsQuantityWanted'] : $this->_defaultConfiguration['showProductsQuantityWanted']),
            'packExcludeList' => $packExcludeList,
            'product' => $productPack,
            'col_img_dir' => _PS_COL_IMG_DIR_,
            'display_qties' => (int)Configuration::get('PS_DISPLAY_QTIES'),
            'allow_oosp' => $productPack->isAvailableWhenOutOfStock((int)$productPack->out_of_stock),
            'tax_enabled' => Configuration::get('PS_TAX'),
            'content_only' => false,
        ));
        $this->_assignSmartyImageTypeVars();
        $isFromQuickView = (version_compare(_PS_VERSION_, '1.7.0.0', '>=') && $this->context->controller instanceof pm_advancedpackupdate_packModuleFrontController && $this->context->controller->isFromQuickView());
        if ($isFromQuickView || $config['displayMode'] == 'advanced') {
            return $this->display(__FILE__, 'views/templates/front/' . Tools::substr(_PS_VERSION_, 0, 3) . '/pack-product-list.tpl');
        } else {
            return $this->display(__FILE__, 'views/templates/hook/' . Tools::substr(_PS_VERSION_, 0, 3) . '/pack-product-list-extra-right.tpl');
        }
    }
    public function displayPackPriceContainer($idPack, $packAttributesList, $packQuantityList = array(), $packExcludeList = array(), $packErrorsList = array(), $packFatalErrorsList = array())
    {
        $productPack = new Product((int)$idPack, true, $this->context->language->id);
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $context = Context::getContext();
            $assembler = new ProductAssembler($context);
            $imageRetriever = new PrestaShop\PrestaShop\Adapter\Image\ImageRetriever($context->link);
            $presenterFactory = new ProductPresenterFactory($context);
            $presentationSettings = $presenterFactory->getPresentationSettings();
            $presenter = new PrestaShop\PrestaShop\Core\Product\ProductPresenter(
                $imageRetriever,
                $context->link,
                new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter(),
                new PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever(),
                $context->getTranslator()
            );
            $productPresentation = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct(array('id_product' => (int)$productPack->id)),
                $context->language
            );
            $productPresentation['quantity_wanted'] = 1;
            $productPresentation['quantity'] = AdvancedPack::getPackAvailableQuantity($idPack, $packAttributesList, $packQuantityList, $packExcludeList);
            if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) {
                $productPresentation['rounded_display_price'] = Tools::ps_round(
                    $productPresentation['price'],
                    Context::getContext()->currency->precision
                );
            }
            if (version_compare(_PS_VERSION_, '1.7.3.0', '>=') && version_compare(_PS_VERSION_, '1.7.5.0', '<')) {
                $productPresentation = $presenter->addQuantityInformation(
                    $productPresentation,
                    $presentationSettings,
                    $productPresentation,
                    $this->context->language
                );
            } elseif (version_compare(_PS_VERSION_, '1.7.3.0', '<')) {
                $productPresentation = $presenter->addQuantityInformation(
                    $productPresentation,
                    $presentationSettings,
                    $productPresentation
                );
            }
        } else {
            $productPresentation = array();
        }
        $productPack->ecotax = AdvancedPack::getPackEcoTax($idPack, $packAttributesList);
        $productPack->quantity = AdvancedPack::getPackAvailableQuantity($idPack, $packAttributesList, $packQuantityList, $packExcludeList);
        $ecotax_rate = (float)Tax::getProductEcotaxRate($this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
        $ecotax_tax_amount = Tools::ps_round($productPack->ecotax, 2);
        if (Product::$_taxCalculationMethod == PS_TAX_INC && (int)Configuration::get('PS_TAX')) {
            $ecotax_tax_amount = Tools::ps_round($ecotax_tax_amount * (1 + $ecotax_rate / 100), 2);
        }
        $id_group = (int)Group::getCurrent()->id;
        $group_reduction = GroupReduction::getValueForProduct($productPack->id, $id_group);
        if ($group_reduction === false) {
            $group_reduction = Group::getReduction((int)$this->context->cookie->id_customer) / 100;
        }
        $config = $this->_getModuleConfiguration();
        $this->context->smarty->assign(array(
            'packDisplayModeAdvanced' => ($config['displayMode'] == self::DISPLAY_ADVANCED),
            'packDisplayModeSimple' => ($config['displayMode'] == self::DISPLAY_SIMPLE),
            'productsPackErrors' => $packErrorsList,
            'productsPackFatalErrors' => $packFatalErrorsList,
            'packAvailableQuantity' => AdvancedPack::getPackAvailableQuantity($idPack, $packAttributesList, $packQuantityList, $packExcludeList),
            'product' => (version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? $productPresentation : $productPack),
            'packAttributesList' => $packAttributesList,
            'packQuantityList' => $packQuantityList,
            'packAllowRemoveProduct' => AdvancedPack::getPackAllowRemoveProduct($idPack),
            'packExcludeList' => $packExcludeList,
            'priceDisplayPrecision' => _PS_PRICE_DISPLAY_PRECISION_,
            'tax_enabled' => Configuration::get('PS_TAX'),
            'ecotax_tax_inc' => $ecotax_tax_amount,
            'ecotax_tax_exc' => Tools::ps_round($productPack->ecotax, 2),
            'group_reduction' => $group_reduction,
            'content_only' => false,
            'allow_oosp' => $productPack->isAvailableWhenOutOfStock((int)$productPack->out_of_stock),
            'displayUnitPrice' => false,
            'displayPackPrice' => false,
        ));
        return $this->display(__FILE__, 'views/templates/front/' . Tools::substr(_PS_VERSION_, 0, 3) . '/pack-price-container.tpl');
    }
    public function _updatePackFields($idPack, $isNewPack = false, $isImportedFromNativePack = false)
    {
        self::$_preventInfiniteLoop = true;
        AdvancedPack::clearAP5Cache();
        $productPack = new Product((int)$idPack, false, null, AdvancedPack::getPackIdShop($idPack));
        if (AdvancedPack::getPackIdTaxRulesGroup($idPack)) {
            $productPack->price = AdvancedPack::getPackPrice($idPack, false, false, false);
        } else {
            $productPack->price = AdvancedPack::getPackPrice($idPack, true, false, false);
        }
        if ($productPack->price === false && isset(Context::getContext()->controller) && get_class(Context::getContext()->controller) == 'AdminProductsController') {
            throw new PrestaShopException($this->l('Unable to get the pack price, please check if all the products are available'));
        }
        $productPack->id_tax_rules_group = AdvancedPack::getPackIdTaxRulesGroup($idPack);
        $productPack->weight = AdvancedPack::getPackWeight($idPack);
        $productPack->wholesale_price = AdvancedPack::getPackWholesalePrice($idPack);
        $productPack->is_virtual = (int)AdvancedPack::isVirtualPack($idPack);
        $productPack->ecotax = 0;
        $productPack->customizable = (AdvancedPackCoreClass::_isFilledArray(AdvancedPack::getPackCustomizationRequiredFields($idPack)) ? 2 : 0);
        if ($productPack->customizable) {
            $productPack->text_fields = sizeof(AdvancedPack::getPackCustomizationRequiredFields($idPack));
        } else {
            $productPack->text_fields = 0;
        }
        $productPack->out_of_stock = 0;
        StockAvailable::setProductOutOfStock($idPack, 0);
        if (((Configuration::hasKey('PS_FORCE_ASM_NEW_PRODUCT') && Configuration::get('PS_FORCE_ASM_NEW_PRODUCT')) || !Configuration::hasKey('PS_FORCE_ASM_NEW_PRODUCT')) && Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
            $productPack->advanced_stock_management = AdvancedPack::getPackAsmState($idPack);
        }
        $productPack->depends_on_stock = 0;
        StockAvailable::setProductDependsOnStock($idPack, 0);
        if ($isNewPack && !$isImportedFromNativePack && !AdvancedPack::clonePackImages($productPack->id)) {
            throw new PrestaShopException($this->l('Unable to clone pack images'));
        }
        $associatedShops = $productPack->getAssociatedShops();
        if (self::_isFilledArray($associatedShops) && sizeof($associatedShops) > 1) {
            foreach ($associatedShops as $idShopToRemove) {
                if (!empty($idShopToRemove) && $idShopToRemove != AdvancedPack::getPackIdShop($idPack)) {
                    Db::getInstance()->delete('product_shop', '`id_product`='.(int)$idPack.' AND `id_shop`='.(int)$idShopToRemove);
                }
            }
        }
        if ($productPack->save()) {
            $config = $this->_getModuleConfiguration();
            if (!empty($config['postponeUpdatePackSpecificPrice'])) {
                $idPackList = $this->getPackIdToUpdate();
                $idPackList[] = (int)$idPack;
                $idPackList = array_unique($idPackList);
                Configuration::updateValue('PM_' . self::$modulePrefix . '_PRICE_UPDATE', json_encode($idPackList));
            } else {
                if (!self::$_updateQuantityProcess && !AdvancedPack::clonePackAttributes($productPack->id)) {
                    throw new PrestaShopException($this->l('Unable to generate pack attribute combinations'));
                }
                return AdvancedPack::addPackSpecificPrice($idPack, 0);
            }
        } else {
            throw new PrestaShopException($this->l('Unable to save the pack'));
        }
        return false;
    }
    public static function getPackAddCartURL($idPack)
    {
        return Context::getContext()->link->getModuleLink('pm_advancedpack', 'add_pack', array('id_pack' => (int)$idPack, 'rand' => time()));
    }
    public static function getPackUpdateURL($idPack)
    {
        return Context::getContext()->link->getModuleLink('pm_advancedpack', 'update_pack', array('id_pack' => (int)$idPack, 'id_product' => (int)$idPack, 'rand' => time()));
    }
    public function getFrontTranslation($idTranslation)
    {
        $translationTab = array(
            'errorWrongCombination' => $this->l('This combination does not exist for this product. Please select another combination.'),
            'errorMaximumQuantity' => $this->l('You already have the maximum quantity available for this product.'),
            'errorSavePackContent' => $this->l('Unable to save pack content, Please contact the webmaster.'),
            'errorGeneratingPrice' => $this->l('Error when generating price for pack. Please contact the webmaster.'),
            'errorOutOfStock' => $this->l('This pack is out of stock.'),
            'errorInvalidPack' => $this->l('This pack is not valid or is no longer available.'),
            'errorInvalidPackChoice' => $this->l('Choice on the pack aren\'t valid.'),
            'errorProductOrCombinationIsOutOfStock' => $this->l('This product or combination is out of stock.'),
            'errorProductIsOutOfStock' => $this->l('This product is out of stock.'),
            'errorProductIsDisabled' => $this->l('This product is not available at this time.'),
            'errorProductAccessDenied' => $this->l('You do not have access to this product.'),
            'errorProductIsNotAvailableForOrder' => $this->l('This product is not available for order.'),
            'errorInvalidExclude' => $this->l('You must keep at least one product.'),
            'errorInvalidCustomization' => $this->l('Please fill in all of the required fields first.'),
        );
        if (isset($translationTab[$idTranslation])) {
            return $translationTab[$idTranslation];
        }
        return false;
    }
    public static $moduleCacheId = null;
    public function getPMNativeCacheId()
    {
        if (self::$moduleCacheId === null) {
            if (empty($this->context->currency)) {
                $this->context->currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));
            }
            self::$moduleCacheId = $this->getCacheId();
            return self::$moduleCacheId;
        } else {
            return self::$moduleCacheId;
        }
    }
    protected function getCrossSellingOrderByOptions()
    {
        $values = array(
            'date_add_asc' => $this->l('Creation date (ascending, older first)'),
            'date_add_desc' => $this->l('Creation date (descending, new first)'),
            'price_asc' => $this->l('Price (ascending)'),
            'price_desc' => $this->l('Price (descending)'),
            'random' => $this->l('Random'),
        );
        $toBeReturned = array();
        foreach ($values as $key => $value) {
            $toBeReturned[] = array(
                'id' => 'crossOrderBy_' . $key,
                'value' => $key,
                'label' => $value,
            );
        }
        return $toBeReturned;
    }
    public static function getAdvancedStylesDb()
    {
        $cssRules = Configuration::get('PM_AP5_ADVANCED_STYLES');
        return self::getDataUnserialized($cssRules);
    }
    public function getImageType($includePixelsSize = false)
    {
        $result = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT it. `id_image_type`, it.`name`, it.`products`, it.`width`, it.`height`
			FROM `' . _DB_PREFIX_ . 'image_type` it
			WHERE it.`products` = 1
		');
        $image = array();
        foreach ($result as $k => $img) {
            $image[$k] = array();
            $image[$k]['value'] = $img['name'];
            $image[$k]['name'] = $img['name'].($includePixelsSize ? ' ('.$img ['width'].'px * '.$img ['height'].' px)' : '');
        }
        return $image;
    }
    public function renderModal(Cart $cart, $id_product, $id_product_attribute)
    {
        $presenter = new PrestaShop\PrestaShop\Adapter\Cart\CartPresenter();
        $data = $presenter->present($cart);
        $product = null;
        $groupPriceDisplayMethod = (int)Group::getCurrent()->price_display_method;
        $psTaxDisplay = (int)Configuration::get('PS_TAX_DISPLAY');
        if (is_array($id_product_attribute)) {
            $explodedPackData = $id_product_attribute;
            if (!isset($explodedPackData['cq']) || !is_numeric($explodedPackData['cq']) || $explodedPackData['cq'] <= 0) {
                $explodedPackData['cq'] = 1;
            }
            foreach (array('idpal', 'ql', 'pel') as $k) {
                if (!isset($explodedPackData[$k]) || !is_array($explodedPackData[$k])) {
                    $explodedPackData[$k] = array();
                }
            }
            $product = array();
            $assembler = new ProductAssembler($this->context);
            $imageRetriever = new PrestaShop\PrestaShop\Adapter\Image\ImageRetriever($this->context->link);
            $presenterFactory = new ProductPresenterFactory($this->context);
            $presentationSettings = $presenterFactory->getPresentationSettings();
            $presenter = new PrestaShop\PrestaShop\Core\Product\ProductPresenter(
                $imageRetriever,
                $this->context->link,
                new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter(),
                new PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever(),
                $this->context->getTranslator()
            );
            $productPackObj = new Product($id_product, true, $this->context->language->id);
            if (Validate::isLoadedObject($productPackObj) && $productPackObj->active && AdvancedPack::isValidPack($id_product)) {
                $product = $presenter->present(
                    $presentationSettings,
                    $assembler->assembleProduct(array('id_product' => (int)$id_product, 'cart_quantity' => (int)$explodedPackData['cq'])),
                    $this->context->language
                );
                $attributeGroupPublic = new AttributeGroup(AdvancedPack::getPackAttributeGroupId(), $this->context->language->id);
                if (version_compare(_PS_VERSION_, '1.7.5.0', '>=')) {
                    $product->offsetSet('attributes', array(), true);
                } else {
                    $product['attributes'] = array();
                }
                if (Validate::isLoadedObject($attributeGroupPublic)) {
                    $product['attributes'][$attributeGroupPublic->public_name] = 'ap5ExplodedCart';
                } else {
                    $product['attributes']['Pack'] = 'ap5ExplodedCart';
                }
                $product['price'] = Tools::displayPrice(AdvancedPack::getPackPrice((int)$product['id_product'], false, true, true, 6, $explodedPackData['idpal'], $explodedPackData['ql'], $explodedPackData['pel'], true));
                $product['price_wt'] = AdvancedPack::getPackPrice((int)$product['id_product'], true, true, true, 6, $explodedPackData['idpal'], $explodedPackData['ql'], $explodedPackData['pel'], true);
            }
        } else {
            foreach ($data['products'] as $p) {
                if ($p['id_product'] == $id_product && $p['id_product_attribute'] == $id_product_attribute) {
                    $product = $p;
                    break;
                }
            }
            if ($product !== null && !empty($product['id_product']) && !AdvancedPack::getPackIdTaxRulesGroup((int)$product['id_product'])) {
                if ($groupPriceDisplayMethod) {
                    if (!is_numeric($product['price']) && isset($product['price_wt'])) {
                        $data['totals']['total']['amount'] -= $product['price_wt'];
                    } else {
                        $data['totals']['total']['amount'] -= $product['price'];
                    }
                }
                $newPrice = AdvancedPack::getPackPrice((int)$product['id_product'], false, true, true, 6, AdvancedPack::getIdProductAttributeListByIdPack((int)$product['id_product'], (int)$product['id_product_attribute']), array(), array(), true);
                $newPriceWt = AdvancedPack::getPackPrice((int)$product['id_product'], true, true, true, 6, AdvancedPack::getIdProductAttributeListByIdPack((int)$product['id_product'], (int)$product['id_product_attribute']), array(), array(), true);
                if ($groupPriceDisplayMethod) {
                    $product['price'] = Tools::displayPrice($newPrice);
                    $product['price_wt'] = $newPriceWt;
                }
                if ($psTaxDisplay) {
                    $data['subtotals']['tax']['amount'] += (int)$product['cart_quantity'] * ($newPriceWt - $newPrice);
                    $data['subtotals']['tax']['value'] = Tools::displayPrice($data['subtotals']['tax']['amount']);
                    $data['totals']['total_excluding_tax']['amount'] -= (int)$product['cart_quantity'] * ($newPriceWt - $newPrice);
                    $data['totals']['total_excluding_tax']['value'] = Tools::displayPrice($data['totals']['total_excluding_tax']['amount']);
                    $product['price_with_reduction_without_tax'] = $newPrice;
                }
                if ($groupPriceDisplayMethod) {
                    $data['totals']['total']['amount'] += $newPrice;
                    $data['totals']['total']['value'] = Tools::displayPrice($data['totals']['total']['amount']);
                }
            }
        }
        $this->smarty->assign(array(
            'product' => $product,
            'cart' => $data,
            'cart_url' => $this->context->link->getPageLink('cart', null, $this->context->language->id, array('action' => 'show'), false, null, true),
        ));
        return $this->fetch('module:ps_shoppingcart/modal.tpl');
    }
    public function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->submit_action = 'submitModuleConfiguration';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $formValues = $this->_getModuleConfiguration();
        $formValues['displayMode'] = ($formValues['displayMode'] == "simple" ? 0 : 1);
        $cronURL = $this->context->link->getModuleLink($this->name, 'cron', array('secure_key' => Configuration::getGlobalValue('PM_AP5_SECURE_KEY')));
        $helper->tpl_vars = array(
            'fields_value' => $formValues,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'module_dir' => $this->_path,
            'module_prefix' => self::$modulePrefix,
            'id_shop' => $this->context->shop->id,
            'ps_version' => _PS_VERSION_,
            'cronURL' => $cronURL,
            'nativeIdsPacksList' => AdvancedPack::getNativeIdsPacks(),
        );
        $this->context->controller->addCSS($this->_path . 'views/css/codemirror.css');
        $this->context->controller->addJS($this->_path . 'views/js/codemirror-compressed.js');
        return $helper->generateForm(array($this->getConfigForm()));
    }
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'tabs' => array(
                    'settings' => $this->l('Configuration'),
                    'style' => $this->l('Advanced Styles'),
                    'pack_migration' => $this->l('Native pack migration'),
                ),
                'input' => array(
                    array(
                        'type' => 'html',
                        'html_content' => '<h2 class="ap-title">'. $this->l('Settings for pack page') .'</h2><hr />',
                        'name' => '',
                        'tab' => 'settings',
                        'label' => '',
                        'col' => 12,
                    ),
                    array(
                        'type' => 'switch',
                        'name' => 'displayMode',
                        'label' => $this->l('Enable Advanced mode'),
                        'hint' => $this->l('When enabled, a custom template file will be used. When disabled, your product page template will be used.'),
                        'tab' => 'settings',
                        'form_group_class' => '',
                        'col' => 8,
                        'values' => array(
                            array(
                              'id'    => 'active_on',
                              'value' => true,
                              'label' => $this->l('Simple'),
                            ),
                            array(
                              'id'    => 'active_off',
                              'value' => false,
                              'label' => $this->l('Advanced'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'html',
                        'html_content' => '<h4 class="ap-title">'. $this->l('Buy block') .'</h4><hr />',
                        'form_group_class' => 'advancedModeOption',
                        'name' => '',
                        'tab' => 'settings',
                        'label' => '',
                        'col' => 12,
                    ),
                    array(
                        'type' => 'switch',
                        'name' => 'autoScrollBuyBlock',
                        'label' => $this->l('Activate sticky mode for the buy block'),
                        'hint' => $this->l('When enabled, the buy block containing the Add to Cart button will follow the scrolling of the page'),
                        'form_group_class' => 'advancedModeOption',
                        'col' => 8,
                        'tab' => 'settings',
                        'values' => array(
                            array(
                              'id'    => 'active_on',
                              'value' => true,
                              'label' => $this->l('Yes'),
                            ),
                            array(
                              'id'    => 'active_off',
                              'value' => false,
                              'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'html',
                        'html_content' => '<h4 class="ap-title">'. $this->l('Display settings') .'</h4><hr />',
                        'name' => '',
                        'tab' => 'settings',
                        'label' => '',
                        'col' => 12,
                    ),
                    array(
                        'type' => 'switch',
                        'name' => 'showProductsQuantityWanted',
                        'label' => $this->l('Provide an option for the customer to choose the quantity of each product to be included in the pack'),
                        'hint' => $this->l('For the quantity selector to be displayed, the "Allow product removal from the pack" option must be checked in your pack configuration'),
                        'tab' => 'settings',
                        'form_group_class' => '',
                        'col' => 8,
                        'values' => array(
                            array(
                              'id'    => 'active_on',
                              'value' => true,
                              'label' => $this->l('Yes'),
                            ),
                            array(
                              'id'    => 'active_off',
                              'value' => false,
                              'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'name' => 'showProductsThumbnails',
                        'label' => $this->l('Show thumbnails for the products in the pack'),
                        'tab' => 'settings',
                        'form_group_class' => 'advancedModeOption',
                        'col' => 8,
                        'values' => array(
                            array(
                              'id'    => 'active_on',
                              'value' => true,
                              'label' => $this->l('Yes'),
                            ),
                            array(
                              'id'    => 'active_off',
                              'value' => false,
                              'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'name' => 'showProductsPrice',
                        'label' => $this->l('Display the individual price for the products in the pack'),
                        'tab' => 'settings',
                        'form_group_class' => '',
                        'col' => 8,
                        'values' => array(
                            array(
                              'id'    => 'active_on',
                              'value' => true,
                              'label' => $this->l('Yes'),
                            ),
                            array(
                              'id'    => 'active_off',
                              'value' => false,
                              'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'name' => 'showImagesOnlyForCombinations',
                        'label' => $this->l('Restrict product images to the combination selected?'),
                        'hint' => $this->l('When enabled, the image of the selected combination will be used as main image'),
                        'tab' => 'settings',
                        'form_group_class' => 'advancedModeOption',
                        'col' => 8,
                        'values' => array(
                            array(
                              'id'    => 'active_on',
                              'value' => true,
                              'label' => $this->l('Yes'),
                            ),
                            array(
                              'id'    => 'active_off',
                              'value' => false,
                              'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'name' => 'showProductsLongDescription',
                        'label' => $this->l('Display the description for the products in the pack?'),
                        'tab' => 'settings',
                        'form_group_class' => 'advancedModeOption',
                        'col' => 8,
                        'values' => array(
                            array(
                              'id'    => 'active_on',
                              'value' => true,
                              'label' => $this->l('Yes'),
                            ),
                            array(
                              'id'    => 'active_off',
                              'value' => false,
                              'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'name' => 'showProductsShortDescription',
                        'label' => $this->l('Display the short description for the products in the pack?'),
                        'tab' => 'settings',
                        'form_group_class' => 'advancedModeOption',
                        'col' => 8,
                        'values' => array(
                            array(
                              'id'    => 'active_on',
                              'value' => true,
                              'label' => $this->l('Yes'),
                            ),
                            array(
                              'id'    => 'active_off',
                              'value' => false,
                              'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'name' => 'showProductsFeatures',
                        'label' => $this->l('Display the features of the products in the pack'),
                        'tab' => 'settings',
                        'form_group_class' => 'advancedModeOption',
                        'col' => 8,
                        'values' => array(
                            array(
                              'id'    => 'active_on',
                              'value' => true,
                              'label' => $this->l('Yes'),
                            ),
                            array(
                              'id'    => 'active_off',
                              'value' => false,
                              'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'name' => 'showProductsAvailability',
                        'label' => $this->l('Display the availability information for the products in the pack'),
                        'tab' => 'settings',
                        'form_group_class' => 'advancedModeOption',
                        'col' => 8,
                        'values' => array(
                            array(
                              'id'    => 'active_on',
                              'value' => true,
                              'label' => $this->l('Yes'),
                            ),
                            array(
                              'id'    => 'active_off',
                              'value' => false,
                              'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'html',
                        'html_content' => '<h4 class="ap-title">'. $this->l('Style settings') .'</h4><hr />',
                        'form_group_class' => 'advancedModeOption',
                        'name' => '',
                        'tab' => 'settings',
                        'label' => '',
                        'col' => 12,
                    ),
                    array(
                        'type' => 'gradientcolor',
                        'name' => 'tabActiveBackgroundColor',
                        'label' => $this->l('Active tab background color'),
                        'tab' => 'settings',
                        'form_group_class' => 'advancedModeOption' . (version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? ' hide' : ''),
                        'col' => 8,
                    ),
                    array(
                        'type' => 'color',
                        'name' => 'tabActiveFontColor',
                        'label' => $this->l('Active tab text color'),
                        'tab' => 'settings',
                        'form_group_class' => 'advancedModeOption' . (version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? ' hide' : ''),
                        'col' => 8,
                    ),
                    array(
                        'type' => 'color',
                        'name' => 'tabActiveBorderColor',
                        'label' => $this->l('Active tab border color'),
                        'tab' => 'settings',
                        'form_group_class' => 'advancedModeOption' . (version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? ' hide' : ''),
                        'col' => 8,
                    ),
                    array(
                        'type' => 'gradientcolor',
                        'name' => 'ribbonBackgroundColor',
                        'label' => $this->l('Background color for the Ribbons (used to display the quantity)'),
                        'tab' => 'settings',
                        'form_group_class' => 'advancedModeOption',
                        'col' => 8,
                    ),
                    array(
                        'type' => 'color',
                        'name' => 'ribbonFontColor',
                        'label' => $this->l('Text color for the Ribbons (used to display the quantity)'),
                        'tab' => 'settings',
                        'form_group_class' => 'advancedModeOption',
                        'col' => 8,
                    ),
                    array(
                        'type' => 'color',
                        'name' => 'ribbonBorderColor',
                        'label' => $this->l('Border color for the Ribbons (used to display the quantity)'),
                        'tab' => 'settings',
                        'form_group_class' => 'advancedModeOption' . (version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? ' hide' : ''),
                        'col' => 8,
                    ),
                    array(
                        'type' => 'color',
                        'name' => 'iconPlusFontColor',
                        'label' => $this->l('Product separator color'),
                        'tab' => 'settings',
                        'form_group_class' => 'advancedModeOption',
                        'col' => 8,
                    ),
                    array(
                        'type' => 'color',
                        'name' => 'iconRemoveFontColor',
                        'label' => $this->l('Color of the "Remove Product from Pack" icon'),
                        'tab' => 'settings',
                        'form_group_class' => 'advancedModeOption',
                        'col' => 8,
                    ),
                    array(
                        'type' => 'color',
                        'name' => 'iconCheckFontColor',
                        'label' => $this->l('Color of the "Re-Insert Product into Pack" icon'),
                        'tab' => 'settings',
                        'form_group_class' => 'advancedModeOption',
                        'col' => 8,
                    ),
                    array(
                        'type' => 'html',
                        'html_content' => '<h4 class="ap-title">'. $this->l('Image settings') .'</h4><hr />',
                        'form_group_class' => 'advancedModeOption',
                        'name' => '',
                        'tab' => 'settings',
                        'label' => '',
                        'col' => 12,
                    ),
                    array(
                        'type' => 'select',
                        'name' => 'imageFormatProductCover',
                        'label' => $this->l('Main product image size'),
                        'tab' => 'settings',
                        'form_group_class' => 'advancedModeOption',
                        'col' => 8,
                        'options' => array(
                            'query' => $this->getImageType(true),
                            'id' => 'value',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'name' => 'imageFormatProductCoverMobile',
                        'label' => $this->l('Main product image size (mobile)'),
                        'tab' => 'settings',
                        'form_group_class' => 'advancedModeOption',
                        'col' => 8,
                        'options' => array(
                            'query' => $this->getImageType(true),
                            'id' => 'value',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'name' => 'imageFormatProductSlideshow',
                        'label' => $this->l('Product thumbnail size'),
                        'tab' => 'settings',
                        'form_group_class' => 'advancedModeOption',
                        'col' => 8,
                        'options' => array(
                            'query' => $this->getImageType(true),
                            'id' => 'value',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'name' => 'imageFormatProductZoom',
                        'label' => $this->l('Size of the zoom image for the products'),
                        'tab' => 'settings',
                        'form_group_class' => 'advancedModeOption',
                        'col' => 8,
                        'options' => array(
                            'query' => $this->getImageType(true),
                            'id' => 'value',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'html',
                        'html_content' => '<h2 class="ap-title">'. $this->l('Settings for the "This product is also available in pack" block') .'</h2><hr />',
                        'name' => '',
                        'tab' => 'settings',
                        'label' => '',
                        'col' => 12,
                    ),
                    array(
                        'type' => 'switch',
                        'name' => 'enablePackCrossSellingBlock',
                        'label' => $this->l('Display the "This product is also available in pack" block ?'),
                        'tab' => 'settings',
                        'form_group_class' => '',
                        'col' => 8,
                        'values' => array(
                            array(
                              'id'    => 'active_on',
                              'value' => true,
                              'label' => $this->l('Yes'),
                            ),
                            array(
                              'id'    => 'active_off',
                              'value' => false,
                              'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'limitPackNbCrossSelling',
                        'label' => $this->l('Maximum number of packs to be displayed (0 = unlimited)'),
                        'maxlength' => 2,
                        'tab' => 'settings',
                        'form_group_class' => 'CrossSellingOption',
                        'col' => 8,
                        'class' => 'fixed-width-sm',
                    ),
                    array(
                        'type' => 'radio',
                        'name' => 'orderByCrossSelling',
                        'label' => $this->l('Sort packs by'),
                        'tab' => 'settings',
                        'form_group_class' => 'CrossSellingOption',
                        'col' => 8,
                        'values' => $this->getCrossSellingOrderByOptions(),
                    ),
                    array(
                        'type' => 'switch',
                        'name' => 'enableViewThisPackButton',
                        'label' => $this->l('Display "View this Pack" button'),
                        'tab' => 'settings',
                        'form_group_class' => 'CrossSellingOption',
                        'col' => 8,
                        'values' => array(
                            array(
                              'id'    => 'active_on',
                              'value' => true,
                              'label' => $this->l('Yes'),
                            ),
                            array(
                              'id'    => 'active_off',
                              'value' => false,
                              'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'name' => 'enableBuyThisPackButton',
                        'label' => $this->l('Display "Buy this Pack" button'),
                        'tab' => 'settings',
                        'form_group_class' => 'CrossSellingOption',
                        'col' => 8,
                        'values' => array(
                            array(
                              'id'    => 'active_on',
                              'value' => true,
                              'label' => $this->l('Yes'),
                            ),
                            array(
                              'id'    => 'active_off',
                              'value' => false,
                              'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'gradientcolor',
                        'name' => 'viewThisPackButtonBackgroundColor',
                        'label' => $this->l('"View this pack" button background color'),
                        'tab' => 'settings',
                        'form_group_class' => 'CrossSellingOption' . (version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? ' hide' : ''),
                        'col' => 8,
                    ),
                    array(
                        'type' => 'color',
                        'name' => 'viewThisPackButtonFontColor',
                        'label' => $this->l('"View this pack" button text color'),
                        'tab' => 'settings',
                        'form_group_class' => 'CrossSellingOption' . (version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? ' hide' : ''),
                        'col' => 8,
                    ),
                    array(
                        'type' => 'gradientcolor',
                        'name' => 'buyThisPackButtonBackgroundColor',
                        'label' => $this->l('"Buy this pack" button background color'),
                        'tab' => 'settings',
                        'form_group_class' => 'CrossSellingOption' . (version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? ' hide' : ''),
                        'col' => 8,
                    ),
                    array(
                        'type' => 'color',
                        'name' => 'buyThisPackButtonFontColor',
                        'label' => $this->l('"Buy this pack" button text color'),
                        'tab' => 'settings',
                        'form_group_class' => 'CrossSellingOption' . (version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? ' hide' : ''),
                        'col' => 8,
                    ),
                    array(
                        'type' => 'select',
                        'name' => 'imageFormatProductFooterCover',
                        'label' => $this->l('Main product image size'),
                        'tab' => 'settings',
                        'form_group_class' => 'CrossSellingOption',
                        'col' => 8,
                        'options' => array(
                            'query' => $this->getImageType(true),
                            'id' => 'value',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'html',
                        'html_content' => '<h2 class="ap-title">'. $this->l('Specific settings') .'</h2><hr />',
                        'name' => '',
                        'tab' => 'settings',
                        'label' => '',
                        'col' => 12,
                    ),
                    array(
                        'type' => 'switch',
                        'name' => 'addPrefixToOrderDetail',
                        'label' => $this->l('Add ”Pack <ID> -” prefix to order details'),
                        'hint' => $this->l('If enabled, will add ”Pack <ID> -” to each product of the pack into order details'),
                        'tab' => 'settings',
                        'col' => 8,
                        'values' => array(
                            array(
                              'id'    => 'active_on',
                              'value' => true,
                              'label' => $this->l('Yes'),
                            ),
                            array(
                              'id'    => 'active_off',
                              'value' => false,
                              'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'name' => 'dynamicPriceCalculation',
                        'form_group_class' => (version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? 'hidden' : ''),
                        'label' => $this->l('Enable dynamic price calculation'),
                        'hint' => $this->l('If enabled, it may slow down your shop if you have a lot of packs'),
                        'tab' => 'settings',
                        'col' => 8,
                        'values' => array(
                            array(
                              'id'    => 'active_on',
                              'value' => true,
                              'label' => $this->l('Yes'),
                            ),
                            array(
                              'id'    => 'active_off',
                              'value' => false,
                              'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'name' => 'postponeUpdatePackSpecificPrice',
                        'label' => $this->l('Postpone pack price update (you must use CRON URL if so)'),
                        'hint' => $this->l('If enabled, will disable specific price update into the Back Office (will speed up product saving)'),
                        'tab' => 'settings',
                        'col' => 8,
                        'values' => array(
                            array(
                              'id'    => 'active_on',
                              'value' => true,
                              'label' => $this->l('Yes'),
                            ),
                            array(
                              'id'    => 'active_off',
                              'value' => false,
                              'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'cron',
                        'name' => '',
                        'tab' => 'settings',
                        'form_group_class' => 'postPoneOption',
                        'col' => 12,
                        'label' => '',
                    ),
                    array(
                        'type' => 'html',
                        'html_content' => '<h2 class="ap-title">' . $this->l('Advanced Styles (CSS)') . '</h2><h4>' . $this->l('Enter your CSS rules here') . '</h4>',
                        'name' => '',
                        'tab' => 'style',
                        'label' => '',
                        'col' => 12,
                    ),
                    array(
                        'type' => 'advancedstyles',
                        'label' => $this->l('Enter your CSS rules here'),
                        'name' => '',
                        'tab' => 'style',
                        'col' => 12,
                        'label' => '',
                    ),
                    array(
                        'type' => 'html',
                        'html_content' => '<h2 class="ap-title">' . $this->l('Native pack migration') . '</h2>',
                        'name' => '',
                        'tab' => 'pack_migration',
                        'label' => '',
                        'col' => 12,
                    ),
                    array(
                        'type' => 'native_pack_migration',
                        'label' => '',
                        'name' => '',
                        'tab' => 'pack_migration',
                        'col' => 12,
                        'label' => '',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }
    public function getPackIdToUpdate()
    {
        $idPackList = Configuration::get('PM_' . self::$modulePrefix . '_PRICE_UPDATE');
        if (!empty($idPackList)) {
            $idPackList = json_decode($idPackList);
            if (!is_array($idPackList)) {
                $idPackList = array();
            }
        } else {
            $idPackList = array();
        }
        return $idPackList;
    }
    public function cleanPackIdToUpdate()
    {
        Configuration::updateValue('PM_' . self::$modulePrefix . '_PRICE_UPDATE', json_encode(array()));
    }
}

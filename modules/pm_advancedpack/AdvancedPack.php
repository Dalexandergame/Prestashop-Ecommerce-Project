<?php
/**
 * Advanced Pack 5
 *
 * @author    Presta-Module.com <support@presta-module.com> - http://www.presta-module.com
 * @copyright Presta-Module 2017 - http://www.presta-module.com
 * @license   Commercial
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
class AdvancedPack extends Product
{
    const MODULE_ID = 'AP5';
    const PACK_FAKE_STOCK = 10000;
    const PACK_FAKE_CUSTOMER_ID = 999999;
    public static $forceUseOfAnotherContext = false;
    public static function getPriceStaticPack(
        $id_product,
        $usetax = true,
        $id_product_attribute = null,
        $decimals = 6,
        $divisor = null,
        $only_reduc = false,
        $usereduc = true,
        $quantity = 1,
        $id_customer = null,
        $id_cart = null,
        $id_address = null,
        &$specific_price_output = null,
        $with_ecotax = true,
        $use_group_reduction = true,
        Context $context = null,
        $use_customer_price = true
    ) {
        static $configurationKeys = null;
        static $excludeTaxOption = null;
        if ($configurationKeys === null) {
            $configurationKeys = Configuration::getMultiple(array('PS_CURRENCY_DEFAULT', 'PS_TAX_ADDRESS_TYPE', 'VATNUMBER_COUNTRY', 'VATNUMBER_MANAGEMENT'));
        }
        if ($excludeTaxOption === null) {
            $excludeTaxOption = AdvancedPack::excludeTaxeOption();
        }
        if (!$context) {
            $context = self::getContext();
        }
        $cur_cart = $context->cart;
        if ($divisor !== null) {
            Tools::displayParameterAsDeprecated('divisor');
        }
        if (!Validate::isBool($usetax) || !Validate::isUnsignedId($id_product)) {
            die(Tools::displayError());
        }
        $id_group = (int)Group::getCurrent()->id;
        if (!is_object($cur_cart) || (Validate::isUnsignedInt($id_cart) && $id_cart && $cur_cart->id != $id_cart)) {
            if (!$id_cart && !isset($context->employee)) {
                if (!Tools::getIsset('secure_key')) {
                    die(Tools::displayError());
                }
            }
            $cur_cart = new Cart($id_cart);
            if (!Validate::isLoadedObject($context->cart)) {
                $context->cart = $cur_cart;
            }
        }
        $id_currency = (int)Validate::isLoadedObject($context->currency) ? $context->currency->id : $configurationKeys['PS_CURRENCY_DEFAULT'];
        $id_country = (int)$context->country->id;
        $id_state = 0;
        $zipcode = 0;
        if (!$id_address && Validate::isLoadedObject($cur_cart)) {
            $id_address = $cur_cart->{$configurationKeys['PS_TAX_ADDRESS_TYPE']};
        }
        if (!$id_address && Validate::isLoadedObject($context->customer)) {
            $id_address = (int)Address::getFirstCustomerAddressId($context->customer->id);
        }
        if ($id_address) {
            $address_infos = Address::getCountryAndState($id_address);
            if (!self::$forceUseOfAnotherContext) {
                if ($address_infos['id_country']) {
                    $id_country = (int)$address_infos['id_country'];
                    $id_state = (int)$address_infos['id_state'];
                    $zipcode = $address_infos['postcode'];
                }
            } else {
                $fakeContext = self::getContext();
                if (!empty($address_infos['id_country']) && $fakeContext->country->id == $address_infos['id_country']) {
                    $address_infos['id_state'] = 0;
                    $address_infos['post_code'] = '';
                } else {
                    $address_infos = array();
                }
            }
        } elseif (isset($context->customer->geoloc_id_country) && !self::$forceUseOfAnotherContext) {
            $id_country = (int)$context->customer->geoloc_id_country;
            $id_state = (int)$context->customer->id_state;
            $zipcode = (int)$context->customer->postcode;
        }
        if ($excludeTaxOption) {
            $usetax = false;
        }
        if ($usetax != false
            && !empty($address_infos['vat_number'])
            && $address_infos['id_country'] != $configurationKeys['VATNUMBER_COUNTRY']
            && $configurationKeys['VATNUMBER_MANAGEMENT']) {
            $usetax = false;
        }
        if (is_null($id_customer) && Validate::isLoadedObject($context->customer)) {
            $id_customer = $context->customer->id;
        }
        return Product::priceCalculation(
            $context->shop->id,
            $id_product,
            $id_product_attribute,
            $id_country,
            $id_state,
            $zipcode,
            $id_currency,
            $id_group,
            $quantity,
            $usetax,
            $decimals,
            $only_reduc,
            $usereduc,
            $with_ecotax,
            $specific_price_output,
            $use_group_reduction,
            $id_customer,
            $use_customer_price,
            $id_cart,
            $quantity
        );
    }
    public static function getPackContent($idPack, $idProductAttribute = null, $withFrontDatas = false, $attributesList = array(), $quantityList = array())
    {
        $idLang = (int)self::getContext()->language->id;
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idPack.(int)$idProductAttribute.(int)$withFrontDatas.serialize($attributesList).serialize($quantityList), true);
        $cacheIdWithoutFront = self::getPMCacheId(__METHOD__.(int)$idPack.(int)$idProductAttribute.serialize($quantityList));
        if (!self::isInCache($cacheId, true)) {
            if (!self::isInCache($cacheIdWithoutFront, true)) {
                $sql = new DbQuery();
                $sql->select('*');
                $sql->from('pm_advancedpack_products', 'app');
                if ($idProductAttribute != null && $idProductAttribute) {
                    $sql->innerJoin('pm_advancedpack_cart_products', 'acp', 'acp.`id_pack`='.(int)$idPack.' AND acp.`id_product_pack`=app.`id_product_pack` AND acp.`id_product_attribute_pack`='.(int)$idProductAttribute);
                }
                $sql->where('app.`id_pack`='.(int)$idPack);
                $sql->orderBy('app.`position` ASC');
                $productsPack = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                if (AdvancedPackCoreClass::_isFilledArray($productsPack)) {
                    $productsAttributesPack = self::getPackProductAttributeList($idPack);
                    foreach ($productsPack as &$packProduct) {
                        if (isset($quantityList[$packProduct['id_product_pack']]) && is_numeric($quantityList[$packProduct['id_product_pack']])) {
                            $packProduct['quantity'] = (int)$quantityList[$packProduct['id_product_pack']];
                        }
                        if (isset($packProduct['customization_infos']) && !empty($packProduct['customization_infos'])) {
                            $packProduct['customizationFieldsName'] = array();
                            $packProduct['customization_infos'] = (array)Tools::jsonDecode($packProduct['customization_infos']);
                            $customizationFields = AdvancedPack::getProductPackCustomizationFields((int)$packProduct['id_product']);
                            if (AdvancedPackCoreClass::_isFilledArray($customizationFields)) {
                                foreach ($customizationFields as $customizationField) {
                                    $packProduct['customizationFieldsName'][(int)$customizationField['id_customization_field']] = $customizationField['name'];
                                }
                            }
                        }
                        if (isset($productsAttributesPack[$packProduct['id_product_pack']])) {
                            $packProduct['combinationsInformations'] = $productsAttributesPack[$packProduct['id_product_pack']];
                        }
                    }
                }
                self::storeInCache($cacheIdWithoutFront, $productsPack, true);
            } else {
                $productsPack = self::getFromCache($cacheIdWithoutFront, true);
            }
            if ($withFrontDatas && AdvancedPackCoreClass::_isFilledArray($productsPack)) {
                $config = pm_advancedpack::getModuleConfigurationStatic();
                list($address, $useTax) = self::getAddressInstance();
                $gsrModuleInstance = Module::getInstanceByName('gsnippetsreviews');
                if (version_compare(_PS_VERSION_, '1.6.0.0', '<') || !Validate::isLoadedObject($gsrModuleInstance) || !$gsrModuleInstance->active || version_compare($gsrModuleInstance->version, '4.0.0', '<')) {
                    $gsrModuleInstance = false;
                }
                $linevenReviewsModuleInstance = Module::getInstanceByName('homecomments');
                if (version_compare(_PS_VERSION_, '1.6.0.0', '<') || !Validate::isLoadedObject($linevenReviewsModuleInstance) || !$linevenReviewsModuleInstance->active || version_compare($linevenReviewsModuleInstance->version, '1.4.2', '<')) {
                    $linevenReviewsModuleInstance = false;
                }
                $PM_MultipleFeaturesModuleInstance = Module::getInstanceByName('pm_multiplefeatures');
                $productsPack = self::getPackPriceTable($productsPack, self::getPackFixedPrice($idPack), self::getPackIdTaxRulesGroup((int)$idPack), $useTax, true, true, $attributesList, $quantityList);
                foreach ($productsPack as &$packProduct) {
                    if (!isset($attributesList[$packProduct['id_product_pack']]) || !is_numeric($attributesList[$packProduct['id_product_pack']])) {
                        $idProductAttribute = (int)$packProduct['default_id_product_attribute'];
                    } else {
                        $idProductAttribute = (int)$attributesList[$packProduct['id_product_pack']];
                    }
                    $packProduct['productObj'] = new Product((int)$packProduct['id_product'], false, (int)$idLang);
                    if (Validate::isLoadedObject($packProduct['productObj'])) {
                        self::transformProductDescriptionWithImg($packProduct['productObj']);
                    }
                    $packProduct['image'] = self::_getProductCoverImage((int)$packProduct['id_product'], (int)$idProductAttribute);
                    if (isset($config['showImagesOnlyForCombinations']) && $config['showImagesOnlyForCombinations']) {
                        $packProduct['images'] = Image::getImages($idLang, (int)$packProduct['id_product'], (int)$idProductAttribute);
                        if (!is_array($packProduct['images']) || !sizeof($packProduct['images'])) {
                            $packProduct['images'] = self::_getProductImages($packProduct, $idLang);
                        }
                    } else {
                        $packProduct['images'] = self::_getProductImages($packProduct, $idLang);
                        $packProduct['imagesCombinations'] = Image::getImages($idLang, (int)$packProduct['id_product'], (int)$idProductAttribute);
                        $packProduct['imagesMobile'] = $packProduct['images'];
                        if (is_array($packProduct['imagesCombinations']) && sizeof($packProduct['imagesCombinations'])) {
                            foreach ($packProduct['imagesCombinations'] as $imgCombination) {
                                foreach ($packProduct['imagesMobile'] as $imgMobileKey => $imgMobile) {
                                    if ($imgMobile['id_image'] == $imgCombination['id_image']) {
                                        unset($packProduct['imagesMobile'][$imgMobileKey]);
                                    }
                                }
                            }
                            $packProduct['imagesMobile'] = array_merge($packProduct['imagesCombinations'], $packProduct['imagesMobile']);
                        }
                    }
                    $packProduct['reduction_amount_tax_incl'] = $packProduct['priceInfos']['reductionAmountWt'];
                    $packProduct['reduction_amount_tax_excl'] = $packProduct['priceInfos']['reductionAmount'];
                    $packProduct['productPackPrice'] = $packProduct['priceInfos']['productPackPriceWt'];
                    $packProduct['productPackPriceTaxExcl'] = $packProduct['priceInfos']['productPackPrice'];
                    $packProduct['productClassicPrice'] = $packProduct['priceInfos']['productClassicPriceWt'];
                    $packProduct['productClassicPriceTaxExcl'] = $packProduct['priceInfos']['productClassicPrice'];
                    $packProduct['attributes'] = false;
                    if ($idProductAttribute) {
                        $packProduct['attributes'] = self::_getProductAttributesGroups($packProduct['productObj'], (int)$idProductAttribute, self::getProductAttributeWhiteList($packProduct['id_product_pack']), (int)$idLang);
                    }
                    $packProduct['id_product_attribute'] = (int)$idProductAttribute;
                    if (Validate::isLoadedObject($PM_MultipleFeaturesModuleInstance) && $PM_MultipleFeaturesModuleInstance->active) {
                        $packProduct['features'] = $PM_MultipleFeaturesModuleInstance->getFrontFeatures((int)$packProduct['productObj']->id);
                    } else {
                        $packProduct['features'] = $packProduct['productObj']->getFrontFeatures((int)$idLang);
                    }
                    $packProduct['accessories'] = $packProduct['productObj']->getAccessories((int)$idLang);
                    $packProduct['attachments'] = (($packProduct['productObj']->cache_has_attachments) ? $packProduct['productObj']->getAttachments((int)$idLang) : array());
                    if ($gsrModuleInstance) {
                        $packProduct['gsrAverage'] = $gsrModuleInstance->hookProductRating(array('id' => (int)$packProduct['id_product'], 'display' => 'productRating'));
                        if (!empty($packProduct['gsrAverage'])) {
                            $packProduct['gsrReviewsList'] = $gsrModuleInstance->hookDisplayProductTabContent(array('product' => $packProduct['productObj']));
                        }
                    }
                    if ($linevenReviewsModuleInstance) {
                        $packProduct['gsrAverage'] = $linevenReviewsModuleInstance->partnerDisplayAverageRate((int)$packProduct['id_product'], 'pm_advancedpack');
                        if (!empty($packProduct['gsrAverage'])) {
                            $packProduct['gsrReviewsList'] = $linevenReviewsModuleInstance->partnerDisplayListReviews((int)$packProduct['id_product'], 'pm_advancedpack');
                        }
                    }
                    $text_fields = array();
                    if ($packProduct['productObj']->customizable) {
                        $texts = self::getContext()->cart->getProductCustomization($packProduct['productObj']->id, Product::CUSTOMIZE_TEXTFIELD, true);
                        foreach ($texts as $text_field) {
                            if (in_array((int)$text_field['index'], self::getProductCustomizationFieldWhiteList($packProduct['id_product_pack']))) {
                                $text_fields['textFields_'.$packProduct['productObj']->id.'_'.$text_field['index']] = str_replace('<br />', "\n", $text_field['value']);
                            }
                        }
                    }
                    $customization_fields = $packProduct['productObj']->customizable ? AdvancedPack::getProductPackCustomizationFields((int)$packProduct['productObj']->id) : array();
                    foreach ($customization_fields as $customizationKey => $customizationRow) {
                        if (!in_array((int)$customizationRow['id_customization_field'], self::getProductCustomizationFieldWhiteList($packProduct['id_product_pack']))) {
                            unset($customization_fields[$customizationKey]);
                        }
                    }
                    $packProduct['customization']['textFields'] = $text_fields;
                    $packProduct['customization']['customizationFields'] = $customization_fields;
                    $packProduct['productObj']->customization_required = false;
                    if (is_array($customization_fields)) {
                        foreach ($customization_fields as $customization_field) {
                            if ($packProduct['productObj']->customization_required = $customization_field['required']) {
                                break;
                            }
                        }
                    }
                }
            }
            if (AdvancedPackCoreClass::_isFilledArray($productsPack)) {
                self::storeInCache($cacheId, $productsPack, true);
                return $productsPack;
            };
        } else {
            return self::getFromCache($cacheId, true);
        }
        self::storeInCache($cacheId, false, true);
        return false;
    }
    public static function getPackContentGroupByProduct($productsPack)
    {
        $idProductList = array();
        foreach ($productsPack as $productRowKey => $packProduct) {
            if (!in_array((int)$packProduct['id_product'], $idProductList)) {
                $idProductList[] = (int)$packProduct['id_product'];
            } else {
                unset($productsPack[$productRowKey]);
                continue;
            }
        }
        return $productsPack;
    }
    public static function getPackPriceTable($packContent, $packFixedPriceList = array(), $packIdTaxRulesGroup = 0, $useTax = true, $includeEcoTax = true, $useGroupReduction = false, $attributesList = array())
    {
        $packFixedPrice = 0;
        $productCategoryReduction = null;
        $packCategoryReduction = null;
        $currentIdGroup = null;
        if ($packContent !== false && is_array($packContent)) {
            $currentIdGroup = (int)Group::getCurrent()->id;
            $packContentFirstItem = current($packContent);
            $idPack = (int)$packContentFirstItem['id_pack'];
            $packCategoryReduction = GroupReduction::getValueForProduct((int)$idPack, $currentIdGroup);
            if (is_float($packCategoryReduction + 0)) {
                $packCategoryReduction = $packCategoryReduction * 100;
            } else {
                $packCategoryReduction = null;
            }
            if (is_array($packFixedPriceList) && isset($packFixedPriceList[$currentIdGroup]) && $packFixedPriceList[$currentIdGroup] > 0) {
                $packFixedPrice = $packFixedPriceList[$currentIdGroup];
            }
        }
        if ($packCategoryReduction !== null) {
            $useGroupReduction = false;
            if ($packFixedPrice > 0) {
                $packFixedPrice -= ($packFixedPrice * $packCategoryReduction / 100);
            }
        }
        $excludeVATCase = false;
        list($address) = self::getAddressInstance();
        if (!$packIdTaxRulesGroup
        && !empty($address->vat_number)
        && $address->id_country != Configuration::get('VATNUMBER_COUNTRY')
        && Configuration::get('VATNUMBER_MANAGEMENT')) {
            $excludeVATCase = true;
        } elseif (!$packIdTaxRulesGroup && Validate::isLoadedObject(self::getContext()->customer) && self::getContext()->customer->id != self::PACK_FAKE_CUSTOMER_ID) {
            $excludeVATCase = (Product::getTaxCalculationMethod(self::getContext()->customer->id) == 0);
        }
        $cacheId = self::getPMCacheId((int)self::$usePackReductionReduction.__METHOD__.serialize($packContent).(float)$packFixedPrice.(int)$packIdTaxRulesGroup.(int)$useTax.(int)$excludeVATCase.(int)$includeEcoTax.(int)$useGroupReduction.serialize($attributesList), true);
        if (self::$forceUseOfAnotherContext || !self::isInCache($cacheId, true)) {
            $groupReduction = Group::getReductionByIdGroup($currentIdGroup);
            $totalClassicPriceWithoutTaxes = $totalClassicPriceWithTaxes = $totalEcoTax = 0;
            if ($packContent !== false) {
                foreach ($packContent as &$packProduct) {
                    $productPackIdAttribute = (isset($attributesList[(int)$packProduct['id_product_pack']]) ? $attributesList[(int)$packProduct['id_product_pack']] : (isset($packProduct['id_product_attribute']) && (int)$packProduct['id_product_attribute'] ? (int)$packProduct['id_product_attribute'] : (int)$packProduct['default_id_product_attribute']));
                    $specificPriceOutput = null;
                    $productPackPriceWt = $productClassicPriceWt = self::getPriceStaticPack($packProduct['id_product'], true, $productPackIdAttribute, 6, null, false, (bool)$packProduct['use_reduc'], (int)$packProduct['quantity'], null, null, null, $specificPriceOutput, false, $useGroupReduction);
                    $productPackPrice = $productClassicPrice = self::getPriceStaticPack($packProduct['id_product'], false, $productPackIdAttribute, 6, null, false, (bool)$packProduct['use_reduc'], (int)$packProduct['quantity'], null, null, null, $specificPriceOutput, false, $useGroupReduction);
                    if ($packFixedPrice > 0) {
                        if ($packCategoryReduction === null) {
                            $specificCategoryReduction = null;
                            $productCategoryReduction = GroupReduction::getValueForProduct((int)$packProduct['id_product'], $currentIdGroup);
                            if (is_float($productCategoryReduction + 0)) {
                                $productCategoryReduction = $productCategoryReduction * 100;
                            } else {
                                $productCategoryReduction = null;
                            }
                            $specificCategoryReduction = $productCategoryReduction;
                            if ($specificCategoryReduction === null && $groupReduction > 0) {
                                $specificCategoryReduction = (float)$groupReduction;
                            }
                            if ($specificCategoryReduction !== null && $specificCategoryReduction > 0) {
                                $productClassicPriceWtTmp = self::getPriceStaticPack($packProduct['id_product'], true, $productPackIdAttribute, 6, null, false, (bool)$packProduct['use_reduc'], (int)$packProduct['quantity'], null, null, null, $specificPriceOutput, false, false);
                                $productClassicPriceTmp = self::getPriceStaticPack($packProduct['id_product'], false, $productPackIdAttribute, 6, null, false, (bool)$packProduct['use_reduc'], (int)$packProduct['quantity'], null, null, null, $specificPriceOutput, false, false);
                                $packProduct['customPercentageDiscount'] = $specificCategoryReduction;
                                if ($packIdTaxRulesGroup) {
                                    $totalClassicPriceWithTaxes -= ($productClassicPriceTmp * $specificCategoryReduction / 100);
                                } else {
                                    $totalClassicPriceWithoutTaxes -= ($productClassicPriceWtTmp * $specificCategoryReduction / 100);
                                }
                            }
                        }
                    } else {
                        if ($packCategoryReduction === null && self::$usePackReductionReduction && !$useGroupReduction) {
                            $productCategoryReduction = GroupReduction::getValueForProduct((int)$packProduct['id_product'], $currentIdGroup);
                            if (is_float($productCategoryReduction + 0)) {
                                $productCategoryReduction = $productCategoryReduction * 100;
                            } else {
                                $productCategoryReduction = null;
                            }
                            if ($productCategoryReduction != null && $productCategoryReduction > 0) {
                                $packProduct['customPercentageDiscount'] = $productCategoryReduction;
                                $productPackPrice -= ($productPackPrice * $productCategoryReduction / 100);
                                $productPackPriceWt -= ($productPackPriceWt * $productCategoryReduction / 100);
                                $productClassicPrice -= ($productClassicPrice * $productCategoryReduction / 100);
                                $productClassicPriceWt -= ($productClassicPriceWt * $productCategoryReduction / 100);
                            }
                        } elseif ($packCategoryReduction !== null && $packCategoryReduction > 0) {
                            $productPackPrice -= ($productPackPrice * $packCategoryReduction / 100);
                            $productPackPriceWt -= ($productPackPriceWt * $packCategoryReduction / 100);
                            $productClassicPrice -= ($productClassicPrice * $packCategoryReduction / 100);
                            $productClassicPriceWt -= ($productClassicPriceWt * $packCategoryReduction / 100);
                            $packProduct['customPercentageDiscount'] = $packCategoryReduction;
                        }
                    }
                    $taxManager = TaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct((int)$packProduct['id_product']));
                    $productTaxCalculator = $taxManager->getTaxCalculator();
                    $packReductionType = $packProduct['reduction_type'];
                    $packReductionAmount = $packProduct['reduction_amount'];
                    if (isset($packProduct['combinationsInformations']) && isset($packProduct['combinationsInformations'][$productPackIdAttribute]) && $packProduct['combinationsInformations'][$productPackIdAttribute]['reduction_type'] != null) {
                        $packReductionType = $packProduct['combinationsInformations'][$productPackIdAttribute]['reduction_type'];
                        $packReductionAmount = $packProduct['combinationsInformations'][$productPackIdAttribute]['reduction_amount'];
                        $packProduct['reduction_type'] = $packReductionType;
                        $packProduct['reduction_amount'] = $packReductionAmount;
                    }
                    if ($packReductionType == 'amount') {
                        $packReductionAmount = Tools::convertPrice($packReductionAmount, self::getContext()->currency);
                        $packProduct['reduction_amount'] = $packReductionAmount;
                        $productPackPrice -= Tools::ps_round($packReductionAmount, 6);
                        $productPackPriceWt -= Tools::ps_round($useTax ? $productTaxCalculator->addTaxes($packReductionAmount) : $packReductionAmount, 6);
                    } elseif ($packReductionType == 'percentage') {
                        $productPackPrice *= (1 - $packReductionAmount);
                        $productPackPriceWt *= (1 - $packReductionAmount);
                    }
                    if (version_compare(_PS_VERSION_, '1.6.1.0', '<') && !(bool)$packProduct['use_reduc'] && $useGroupReduction && $groupReduction > 0) {
                        $productPackPrice -= ($productPackPrice * $groupReduction / 100);
                        $productPackPriceWt -= ($productPackPriceWt * $groupReduction / 100);
                    }
                    if ($productPackPrice < 0) {
                        $productPackPrice = $productPackPriceWt = 0;
                    }
                    if ($packFixedPrice > 0 && $productPackIdAttribute != (int)$packProduct['default_id_product_attribute']) {
                        $defaultCombinationPriceImpact = Combination::getPrice($packProduct['default_id_product_attribute']);
                        $combinationPriceImpact = Combination::getPrice($productPackIdAttribute);
                        if ($productPackPrice > 0 && $defaultCombinationPriceImpact > 0) {
                            $combinationPriceImpact -= $defaultCombinationPriceImpact;
                        }
                        $packFixedPrice += $combinationPriceImpact;
                    }
                    $productEcoTax = self::getProductEcoTax((int)$packProduct['id_product'], (int)$productPackIdAttribute);
                    if ($useTax) {
                        $taxManager = TaxManagerFactory::getManager($address, (int)Configuration::get('PS_ECOTAX_TAX_RULES_GROUP_ID'));
                        $ecoTaxCalculator = $taxManager->getTaxCalculator();
                        $productPackPrice += $ecoTaxCalculator->addTaxes($productEcoTax);
                        $productPackPriceWt += $ecoTaxCalculator->addTaxes($productEcoTax);
                        $productClassicPrice += $ecoTaxCalculator->addTaxes($productEcoTax);
                        $productClassicPriceWt += $ecoTaxCalculator->addTaxes($productEcoTax);
                        $totalEcoTax += $ecoTaxCalculator->addTaxes($productEcoTax);
                    } else {
                        $productPackPrice += $productEcoTax;
                        $productPackPriceWt += $productEcoTax;
                        $productClassicPrice += $productEcoTax;
                        $productClassicPriceWt += $productEcoTax;
                        $totalEcoTax += $productEcoTax;
                    }
                    if ($packFixedPrice > 0 && $excludeVATCase) {
                        $address2 = clone($address);
                        if (!empty($address2->vat_number)) {
                            $address2->vat_number = null;
                        } else {
                            $address2->id_country = (int)Configuration::get('PS_COUNTRY_DEFAULT');
                            $address2->id_state = 0;
                        }
                        $taxManager2 = TaxManagerFactory::getManager($address2, Product::getIdTaxRulesGroupByIdProduct((int)$packProduct['id_product']));
                        $productTaxCalculator2 = $taxManager2->getTaxCalculator();
                        $totalClassicPriceWithoutTaxes += $productClassicPrice * (int)$packProduct['quantity'];
                        $totalClassicPriceWithTaxes += $productTaxCalculator2->addTaxes($productClassicPrice) * (int)$packProduct['quantity'];
                    } else {
                        $totalClassicPriceWithoutTaxes += $productClassicPrice * (int)$packProduct['quantity'];
                        $totalClassicPriceWithTaxes += $productClassicPriceWt * (int)$packProduct['quantity'];
                    }
                    $packProduct['priceInfos'] = array(
                        'productPackPrice' => $productPackPrice,
                        'productPackPriceWt' => $productPackPriceWt,
                        'productClassicPrice' => $productClassicPrice,
                        'productClassicPriceWt' => $productClassicPriceWt,
                        'taxesClassic' => $productClassicPriceWt - $productClassicPrice,
                        'taxesPack' => ($productPackPriceWt - $productEcoTax) - $productTaxCalculator->removeTaxes($productPackPriceWt - $productEcoTax),
                        'productEcoTax' => $productEcoTax,
                        'quantity' =>  (int)$packProduct['quantity'],
                    );
                    if ($packReductionType == 'amount') {
                        $packProduct['priceInfos']['reductionAmountWt'] = Tools::ps_round($useTax ? $productTaxCalculator->addTaxes($packReductionAmount) : $packReductionAmount, 6);
                        $packProduct['priceInfos']['reductionAmount'] = Tools::ps_round($packReductionAmount, 6);
                    } else {
                        $packProduct['priceInfos']['reductionAmountWt'] = 0;
                        $packProduct['priceInfos']['reductionAmount'] = 0;
                    }
                }
                if ($packFixedPrice > 0) {
                    foreach ($packContent as &$packProduct) {
                        $taxManager = TaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct((int)$packProduct['id_product']));
                        $productTaxCalculator = $taxManager->getTaxCalculator();
                        if ($packIdTaxRulesGroup) {
                            $packProduct['priceInfos']['productPackPriceWt'] = Tools::ps_round((($packProduct['priceInfos']['productPackPriceWt'] * (int)$packProduct['quantity']) / $totalClassicPriceWithoutTaxes) * $packFixedPrice, 6) / (int)$packProduct['quantity'];
                            if ($packProduct['priceInfos']['productEcoTax'] > 0) {
                                $packProduct['priceInfos']['productPackPrice'] = Tools::ps_round($productTaxCalculator->removeTaxes($packProduct['priceInfos']['productPackPriceWt'] - $packProduct['priceInfos']['productEcoTax']) + $packProduct['priceInfos']['productEcoTax'], 6);
                            } else {
                                $packProduct['priceInfos']['productPackPrice'] = Tools::ps_round((($packProduct['priceInfos']['productPackPrice'] * (int)$packProduct['quantity']) / $totalClassicPriceWithoutTaxes) * $packFixedPrice, 6) / (int)$packProduct['quantity'];
                            }
                        } else {
                            $packProduct['priceInfos']['productPackPriceWt'] = Tools::ps_round((($packProduct['priceInfos']['productPackPriceWt'] * (int)$packProduct['quantity']) / $totalClassicPriceWithTaxes) * $packFixedPrice, 6) / (int)$packProduct['quantity'];
                            if ($packProduct['priceInfos']['productEcoTax'] > 0) {
                                $packProduct['priceInfos']['productPackPrice'] = Tools::ps_round($productTaxCalculator->removeTaxes($packProduct['priceInfos']['productPackPriceWt'] - $packProduct['priceInfos']['productEcoTax']) + $packProduct['priceInfos']['productEcoTax'], 6);
                            } else {
                                $packProduct['priceInfos']['productPackPrice'] = Tools::ps_round((($packProduct['priceInfos']['productPackPrice'] * (int)$packProduct['quantity']) / $totalClassicPriceWithTaxes) * $packFixedPrice, 6) / (int)$packProduct['quantity'];
                            }
                        }
                    }
                } else {
                    foreach ($packContent as &$packProduct) {
                        $packProduct['priceInfos']['productPackPrice'] = Tools::ps_round($packProduct['priceInfos']['productPackPrice'], 6);
                        $packProduct['priceInfos']['productPackPriceWt'] = Tools::ps_round($packProduct['priceInfos']['productPackPriceWt'], 6);
                        $packProduct['priceInfos']['productClassicPrice'] = Tools::ps_round($packProduct['priceInfos']['productClassicPrice'], 6);
                        $packProduct['priceInfos']['productClassicPriceWt'] = Tools::ps_round($packProduct['priceInfos']['productClassicPriceWt'], 6);
                        $packProduct['priceInfos']['taxesClassic'] = Tools::ps_round($packProduct['priceInfos']['taxesClassic'], 6);
                        $packProduct['priceInfos']['taxesPack'] = Tools::ps_round($packProduct['priceInfos']['taxesPack'], 6);
                        $packProduct['priceInfos']['productEcoTax'] = Tools::ps_round($packProduct['priceInfos']['productEcoTax'], 6);
                    }
                }
            }
        } else {
            return self::getFromCache($cacheId, true);
        }
        self::storeInCache($cacheId, $packContent, true);
        return $packContent;
    }
    public static $usePackReductionReduction = false;
    public static function getPackPrice($idPack, $useTax = true, $usePackReduction = true, $includeEcoTax = true, $priceDisplayPrecision = 6, $attributesList = array(), $quantityList = array(), $packExcludeList = array(), $useGroupReduction = false)
    {
        self::$usePackReductionReduction = $usePackReduction;
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idPack.(int)$useTax.(int)$usePackReduction.(int)$includeEcoTax.(int)$priceDisplayPrecision.serialize($attributesList).serialize($quantityList).serialize($packExcludeList).(int)$useGroupReduction, true);
        if (self::$forceUseOfAnotherContext || !self::isInCache($cacheId, true)) {
            $packContent = self::getPackContent($idPack, null, false, $attributesList, $quantityList);
            $packFixedPrice = self::getPackFixedPrice($idPack);
            $packClassicPrice = $packClassicPriceWt = $packPrice = $packPriceWt = $totalPackEcoTax = $totalPackEcoTaxWt = 0;
            list($address) = self::getAddressInstance();
            self::$usePackReductionReduction = $usePackReduction;
            $packProducts = self::getPackPriceTable($packContent, $packFixedPrice, self::getPackIdTaxRulesGroup((int)$idPack), $useTax, $includeEcoTax, $useGroupReduction, $attributesList, $quantityList);
            foreach ($packProducts as $packProduct) {
                if (in_array((int)$packProduct['id_product_pack'], $packExcludeList)) {
                    continue;
                }
                $packClassicPrice += $packProduct['priceInfos']['productClassicPrice'] * (int)$packProduct['quantity'];
                $packClassicPriceWt += $packProduct['priceInfos']['productClassicPriceWt'] * (int)$packProduct['quantity'];
                $packPriceWt += $packProduct['priceInfos']['productPackPriceWt'] * (int)$packProduct['quantity'];
                $packPrice += $packProduct['priceInfos']['productPackPrice'] * (int)$packProduct['quantity'];
                $totalPackEcoTax += $packProduct['priceInfos']['productEcoTax'] * (int)$packProduct['quantity'];
                $totalPackEcoTaxWt += $packProduct['priceInfos']['productEcoTax'] * (int)$packProduct['quantity'];
            }
            if (!$includeEcoTax) {
                $packPrice -= $totalPackEcoTax;
                $packPriceWt -= $totalPackEcoTaxWt;
                $packClassicPrice -= $totalPackEcoTax;
                $packClassicPriceWt -= $totalPackEcoTaxWt;
            }
            if ($useTax) {
                if ($usePackReduction) {
                    self::storeInCache($cacheId, (float)$packPriceWt, true);
                    return $packPriceWt;
                } else {
                    self::storeInCache($cacheId, (float)$packClassicPriceWt, true);
                    return $packClassicPriceWt;
                }
            } else {
                if ($usePackReduction) {
                    self::storeInCache($cacheId, (float)$packPrice, true);
                    return $packPrice;
                } else {
                    self::storeInCache($cacheId, (float)$packClassicPrice, true);
                    return $packClassicPrice;
                }
            }
        } else {
            return self::getFromCache($cacheId, true);
        }
        self::storeInCache($cacheId, (float)$packPrice, true);
        return (float)$packPrice;
    }
    public static function getPackFixedPrice($idPack)
    {
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idPack, true);
        if (!self::isInCache($cacheId, true)) {
            $packFixedPrice = array();
            $sql = new DbQuery();
            $sql->select('ap.`fixed_price`');
            $sql->from('pm_advancedpack', 'ap');
            $sql->where('ap.`id_pack`=' . (int)$idPack);
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            if (!empty($result)) {
                if (is_numeric($result)) {
                    $newFixedPriceData = array();
                    foreach (Group::getGroups(Context::getContext()->language->id, true) as $group) {
                        $newFixedPriceData[(int)$group['id_group']] = $result;
                    }
                    $newFixedPriceData = json_encode($newFixedPriceData);
                    Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'pm_advancedpack` CHANGE COLUMN `fixed_price` `fixed_price` text DEFAULT NULL');
                    Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'pm_advancedpack` ap SET ap.`fixed_price`="'.pSQL($newFixedPriceData).'" WHERE ap.`id_pack` = '.(int)$idPack);
                } else {
                    $jsonResult = (array)json_decode($result, true);
                    if ($jsonResult !== false && is_array($jsonResult)) {
                        foreach ($jsonResult as $k => $v) {
                            $jsonResult[$k] = Tools::convertPrice((float)$jsonResult[$k], self::getContext()->currency);
                        }
                        $packFixedPrice = $jsonResult;
                    } else {
                        $packFixedPrice = array();
                    }
                }
            } else {
                $packFixedPrice = array();
            }
        } else {
            return self::getFromCache($cacheId, true);
        }
        self::storeInCache($cacheId, (array)$packFixedPrice, true);
        return (array)$packFixedPrice;
    }
    public static function getPackAllowRemoveProduct($idPack)
    {
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idPack);
        if (!self::isInCache($cacheId, true)) {
            $sql = new DbQuery();
            $sql->select('ap.`allow_remove_product`');
            $sql->from('pm_advancedpack', 'ap');
            $sql->where('ap.`id_pack`=' . (int)$idPack);
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            $packAllowRemoveProduct = (bool)$result;
        } else {
            return self::getFromCache($cacheId, true);
        }
        self::storeInCache($cacheId, (bool)$packAllowRemoveProduct, true);
        return (bool)$packAllowRemoveProduct;
    }
    private static function _getCartProducts()
    {
        $cartContent = array();
        if (is_object(self::getContext()->controller) && isset(self::getContext()->controller->controller_type) && self::getContext()->controller->controller_type != 'front') {
            return $cartContent;
        }
        $cart = self::getContext()->cart;
        if (Validate::isLoadedObject($cart)) {
            $cacheId = self::getPMCacheId(__METHOD__.(int)$cart->id, true);
            if (!self::isInCache($cacheId, true)) {
                $sql = 'SELECT `id_product`, `id_product_attribute`, `quantity` FROM `'._DB_PREFIX_.'cart_product` WHERE `id_cart` = '.(int)$cart->id;
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                if (pm_advancedpack::_isFilledArray($result)) {
                    foreach ($result as $cartRow) {
                        $cartContent[(int)$cartRow['id_product']][(int)$cartRow['id_product_attribute']] = (int)$cartRow['quantity'];
                    }
                }
            } else {
                return self::getFromCache($cacheId, true);
            }
            self::storeInCache($cacheId, $cartContent, true);
        }
        return $cartContent;
    }
    public static function getCartQuantity($idProduct, $idProductAttribute = 0)
    {
        $cartProducts = self::_getCartProducts();
        if (isset($cartProducts[(int)$idProduct][(int)$idProductAttribute])) {
            return $cartProducts[(int)$idProduct][(int)$idProductAttribute];
        }
        return 0;
    }
    public static function getPackProductsCartQuantity($idProductAttribute = false)
    {
        $currentPackCartStock = array();
        if (is_object(self::getContext()->controller) && isset(self::getContext()->controller->controller_type) && self::getContext()->controller->controller_type != 'front' && self::getContext()->controller->controller_type != 'modulefront') {
            return $currentPackCartStock;
        }
        $cart = self::getContext()->cart;
        if (Validate::isLoadedObject($cart)) {
            $cacheId = self::getPMCacheId(__METHOD__.(int)$cart->id.(int)$idProductAttribute, true);
            if (!self::isInCache($cacheId, true)) {
                foreach ($cart->getProducts() as $cartProduct) {
                    if ($idProductAttribute !== false && (int)$idProductAttribute == (int)$cartProduct['id_product_attribute']) {
                        continue;
                    }
                    if (AdvancedPack::isValidPack((int)$cartProduct['id_product'])) {
                        $packContent = AdvancedPack::getPackContent((int)$cartProduct['id_product'], (int)$cartProduct['id_product_attribute']);
                        if ($packContent !== false) {
                            foreach ($packContent as $packProduct) {
                                if (isset($currentPackCartStock[(int)$packProduct['id_product']][(int)$packProduct['id_product_attribute']])) {
                                    $currentPackCartStock[(int)$packProduct['id_product']][(int)$packProduct['id_product_attribute']] += (int)$cartProduct['cart_quantity'] * (int)$packProduct['quantity'];
                                } else {
                                    $currentPackCartStock[(int)$packProduct['id_product']][(int)$packProduct['id_product_attribute']] = (int)$cartProduct['cart_quantity'] * (int)$packProduct['quantity'];
                                }
                            }
                        }
                    }
                }
            } else {
                return self::getFromCache($cacheId, true);
            }
            self::storeInCache($cacheId, $currentPackCartStock, true);
        }
        return $currentPackCartStock;
    }
    public static function getPackAvailableQuantity($idPack, $attributesList = array(), $quantityList = array(), $packExcludeList = array(), $idProductAttribute = false, $useCache = true)
    {
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idPack.serialize($attributesList).serialize($quantityList).serialize($packExcludeList).(int)$idProductAttribute, true);
        $packAvailableQuantity = 0;
        if (!$useCache || !self::isInCache($cacheId, true)) {
            if (!AdvancedPack::isValidPack($idPack, true, $packExcludeList)) {
                return 0;
            }
            if (!self::isVirtualPack($idPack)) {
                $currentPackCartStock = self::getPackProductsCartQuantity($idProductAttribute);
                $packContent = self::getPackContent($idPack);
                $productPackQuantityList = array();
                $stockNeededByIdProductIdProductAttribute = array();
                if ($packContent !== false) {
                    foreach ($packContent as $packProduct) {
                        if (in_array((int)$packProduct['id_product_pack'], $packExcludeList)) {
                            continue;
                        }
                        if (!Product::isAvailableWhenOutOfStock(StockAvailable::outOfStock((int)$packProduct['id_product']))) {
                            if (!isset($attributesList[$packProduct['id_product_pack']]) || !is_numeric($attributesList[$packProduct['id_product_pack']])) {
                                $idProductAttribute = (int)$packProduct['default_id_product_attribute'];
                            } else {
                                $idProductAttribute = (int)$attributesList[$packProduct['id_product_pack']];
                            }
                            if (!isset($stockNeededByIdProductIdProductAttribute[(int)$packProduct['id_product']][$idProductAttribute])) {
                                $stockNeededByIdProductIdProductAttribute[(int)$packProduct['id_product']][$idProductAttribute] = (int)$packProduct['quantity'];
                            } else {
                                $stockNeededByIdProductIdProductAttribute[(int)$packProduct['id_product']][$idProductAttribute] += (int)$packProduct['quantity'];
                            }
                        }
                    }
                    foreach ($packContent as $packProduct) {
                        if (in_array((int)$packProduct['id_product_pack'], $packExcludeList)) {
                            continue;
                        }
                        if (!Product::isAvailableWhenOutOfStock(StockAvailable::outOfStock((int)$packProduct['id_product']))) {
                            if (!isset($attributesList[$packProduct['id_product_pack']]) || !is_numeric($attributesList[$packProduct['id_product_pack']])) {
                                $idProductAttribute = (int)$packProduct['default_id_product_attribute'];
                            } else {
                                $idProductAttribute = (int)$attributesList[$packProduct['id_product_pack']];
                            }
                            $cartPackStock = 0;
                            if (isset($currentPackCartStock[(int)$packProduct['id_product']][(int)$idProductAttribute])) {
                                $cartPackStock = $currentPackCartStock[(int)$packProduct['id_product']][(int)$idProductAttribute];
                            }
                            if (isset(pm_advancedpack::$currentStockUpdate[(int)$packProduct['id_product']]) && isset(pm_advancedpack::$currentStockUpdate[(int)$packProduct['id_product']][$idProductAttribute])) {
                                $currentAvailableStock = (int)pm_advancedpack::$currentStockUpdate[(int)$packProduct['id_product']][$idProductAttribute];
                            } else {
                                $currentAvailableStock = (int)StockAvailable::getQuantityAvailableByProduct((int)$packProduct['id_product'], $idProductAttribute);
                            }
                            $productPackQuantityList[(int)$packProduct['id_product_pack']] = (int)floor(((int)$currentAvailableStock - self::getCartQuantity((int)$packProduct['id_product'], (int)$idProductAttribute) - $cartPackStock) / (int)$stockNeededByIdProductIdProductAttribute[(int)$packProduct['id_product']][$idProductAttribute]);
                        }
                    }
                }
                if (AdvancedPackCoreClass::_isFilledArray($productPackQuantityList)) {
                    $packAvailableQuantity = (int)min(array_values($productPackQuantityList));
                } else {
                    $packAvailableQuantity = self::PACK_FAKE_STOCK;
                }
            } else {
                $packAvailableQuantity = self::PACK_FAKE_STOCK;
            }
        } else {
            return self::getFromCache($cacheId, true);
        }
        self::storeInCache($cacheId, (int)$packAvailableQuantity, true);
        return (int)$packAvailableQuantity;
    }
    public static function getPackAvailableQuantityList($idPack, $attributesList = array(), $quantityList = array(), $useCache = true)
    {
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idPack.serialize($attributesList).serialize($quantityList), true);
        if (!$useCache || !self::isInCache($cacheId, true)) {
            $currentPackCartStock = self::getPackProductsCartQuantity();
            $packContent = self::getPackContent($idPack, null, false, $attributesList, $quantityList);
            $productPackQuantityList = array();
            if ($packContent !== false) {
                foreach ($packContent as $packProduct) {
                    $productAttributesList = self::getProductAttributeWhiteList($packProduct['id_product_pack']);
                    if (!pm_advancedpack::_isFilledArray($productAttributesList)) {
                        $productAttributesList = array_keys(self::getProductCombinationsByIdProductPack((int)$packProduct['id_product_pack']));
                    }
                    if (!pm_advancedpack::_isFilledArray($productAttributesList)) {
                        $productAttributesList = array(0);
                    }
                    if (!Product::isAvailableWhenOutOfStock(StockAvailable::outOfStock((int)$packProduct['id_product']))) {
                        foreach ($productAttributesList as $idProductAttribute) {
                            $cartPackStock = 0;
                            if (isset($currentPackCartStock[(int)$packProduct['id_product']][(int)$idProductAttribute])) {
                                $cartPackStock = $currentPackCartStock[(int)$packProduct['id_product']][(int)$idProductAttribute];
                            }
                            $productPackQuantityList[(int)$packProduct['id_product_pack']][$idProductAttribute] = (int)floor(((int)StockAvailable::getQuantityAvailableByProduct((int)$packProduct['id_product'], $idProductAttribute) - self::getCartQuantity((int)$packProduct['id_product'], (int)$idProductAttribute) - $cartPackStock) / (int)$packProduct['quantity']);
                        }
                    } else {
                        foreach ($productAttributesList as $idProductAttribute) {
                            $productPackQuantityList[(int)$packProduct['id_product_pack']][$idProductAttribute] = self::PACK_FAKE_STOCK;
                        }
                    }
                }
            }
        } else {
            return self::getFromCache($cacheId, true);
        }
        self::storeInCache($cacheId, $productPackQuantityList, true);
        return $productPackQuantityList;
    }
    public static function getPackWeight($idPack)
    {
        $packContent = self::getPackContent($idPack);
        $packWeight = 0;
        if ($packContent !== false) {
            foreach ($packContent as $packProduct) {
                $product = new Product((int)$packProduct['id_product']);
                $packWeight += (float)$product->weight * (int)$packProduct['quantity'];
            }
        }
        return (float)$packWeight;
    }
    public static function getPackIdTaxRulesGroup($idPack)
    {
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idPack);
        $finalIdTaxRulesGroup = 0;
        if (!self::isInCache($cacheId, true)) {
            $packContent = self::getPackContent($idPack);
            $idTaxRulesGroup = array();
            if ($packContent !== false) {
                foreach ($packContent as $packProduct) {
                    $idTaxRulesGroup[] = (int)Product::getIdTaxRulesGroupByIdProduct((int)$packProduct['id_product']);
                }
            }
            $idTaxRulesGroup = array_unique($idTaxRulesGroup);
            if (sizeof($idTaxRulesGroup) == 1) {
                $finalIdTaxRulesGroup = (int)current($idTaxRulesGroup);
            }
        } else {
            return self::getFromCache($cacheId, true);
        }
        self::storeInCache($cacheId, $finalIdTaxRulesGroup, true);
        return $finalIdTaxRulesGroup;
    }
    public static function getPackEcoTax($idPack, $idProductAttributeList = array())
    {
        $packContent = self::getPackContent($idPack);
        $packEcoTaxAmount = 0;
        if ($packContent !== false) {
            foreach ($packContent as $packProduct) {
                $productPackIdAttribute = (isset($idProductAttributeList[(int)$packProduct['id_product_pack']]) ? $idProductAttributeList[(int)$packProduct['id_product_pack']] : $packProduct['default_id_product_attribute']);
                $packEcoTaxAmount += self::getProductEcoTax((int)$packProduct['id_product'], $productPackIdAttribute) * (int)$packProduct['quantity'];
            }
        }
        return (float)$packEcoTaxAmount;
    }
    public static function getProductEcoTax($idProduct, $idProductAttribute)
    {
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idProduct.(int)$idProductAttribute);
        if (!self::isInCache($cacheId)) {
            $product = new Product((int)$idProduct);
            if (Validate::isLoadedObject($product)) {
                $combinationObj = new Combination($idProductAttribute);
                if (Validate::isLoadedObject($combinationObj) && $combinationObj->ecotax > 0) {
                    self::storeInCache($cacheId, (float)$combinationObj->ecotax);
                    return (float)$combinationObj->ecotax;
                }
                self::storeInCache($cacheId, (float)$product->ecotax);
                return (float)$product->ecotax;
            }
            self::storeInCache($cacheId, 0);
            return 0;
        } else {
            return self::getFromCache($cacheId);
        }
    }
    public static function getProductPackCustomizationFields($idProduct, $idLang = null)
    {
        if ($idLang == null) {
            $idLang = self::getContext()->language->id;
        }
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idProduct.(int)$idLang);
        if (!self::isInCache($cacheId)) {
            $productObj = new Product($idProduct, false, $idLang);
            if (Validate::isLoadedObject($productObj)) {
                $customizationFields = $productObj->getCustomizationFields($idLang);
                if (AdvancedPackCoreClass::_isFilledArray($customizationFields)) {
                    foreach ($customizationFields as $k => $customizationField) {
                        if ($customizationField['type'] != 1) {
                            unset($customizationFields[$k]);
                        }
                    }
                    self::storeInCache($cacheId, $customizationFields);
                    return $customizationFields;
                }
            }
        } else {
            return self::getFromCache($cacheId);
        }
        self::storeInCache($cacheId, array());
        return array();
    }
    public static function getPackCustomizationRequiredFields($idPack, $packExcludeList = array())
    {
        if (!$idPack || !Customization::isFeatureActive()) {
            return array();
        }
        $sql = new DbQuery();
        $sql->select('GROUP_CONCAT(cf.`id_customization_field`)');
        $sql->from('customization_field', 'cf');
        $sql->innerJoin('pm_advancedpack_products', 'app', 'app.`id_pack`='.(int)$idPack.' AND app.`id_product`=cf.`id_product`');
        $sql->where('cf.`required`=1');
        if (AdvancedPackCoreClass::_isFilledArray($packExcludeList)) {
            $sql->where('app.`id_product_pack` NOT IN ('. implode(',', $packExcludeList) .')');
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        if (!empty($result)) {
            return array_map('intval', explode(',', $result));
        }
        return array();
    }
    public static function getMaxImagesPerProduct($productsPack)
    {
        $maxImages = array();
        foreach ($productsPack as $productPack) {
            if (isset($productPack['images']) && is_array($productPack['images'])) {
                $maxImages[] = count($productPack['images']);
            }
        }
        if (count($maxImages)) {
            return max($maxImages);
        }
        return 0;
    }
    public static function getExclusiveProducts()
    {
        $cacheId = self::getPMCacheId(__METHOD__);
        if (!self::isInCache($cacheId)) {
            $idProductExclusiveList = array();
            $sql = new DbQuery();
            $sql->select('GROUP_CONCAT(app.`id_product`)');
            $sql->from('pm_advancedpack_products', 'app');
            $sql->where('app.`exclusive`=1');
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            if (!empty($result)) {
                $idProductExclusiveList = explode(',', $result);
            }
        } else {
            return self::getFromCache($cacheId);
        }
        self::storeInCache($cacheId, $idProductExclusiveList);
        return $idProductExclusiveList;
    }
    public static function getIdsPacks($fromAllShop = false)
    {
        $cacheId = self::getPMCacheId(__METHOD__.(int)$fromAllShop);
        if (!self::isInCache($cacheId)) {
            $idPackList = array();
            $sql = new DbQuery();
            $sql->select('app.`id_pack`');
            $sql->from('pm_advancedpack', 'app');
            if (!$fromAllShop) {
                $sql->where('app.`id_shop` IN ('.implode(', ', Shop::getContextListShopID()).')');
            }
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            if (AdvancedPackCoreClass::_isFilledArray($result)) {
                foreach ($result as $row) {
                    $idPackList[] = (int)$row['id_pack'];
                }
            }
        } else {
            return self::getFromCache($cacheId);
        }
        self::storeInCache($cacheId, $idPackList);
        return $idPackList;
    }
    public static function getIdPacksByIdProduct($idProduct)
    {
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idProduct);
        if (!self::isInCache($cacheId)) {
            $idPackList = array();
            $sql = new DbQuery();
            $sql->select('DISTINCT app.`id_pack`');
            $sql->from('pm_advancedpack', 'ap');
            $sql->innerJoin('pm_advancedpack_products', 'app', 'app.`id_pack` = ap.`id_pack`');
            $sql->where('ap.`id_shop` IN ('.implode(', ', Shop::getContextListShopID()).')');
            $sql->where('app.`id_product`='.(int)$idProduct);
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            if (AdvancedPackCoreClass::_isFilledArray($result)) {
                foreach ($result as $row) {
                    $idPackList[] = (int)$row['id_pack'];
                }
            }
        } else {
            return self::getFromCache($cacheId);
        }
        self::storeInCache($cacheId, $idPackList);
        return $idPackList;
    }
    public static function getIdProductAttributeListByIdPack($idPack, $idProductAttribute = null)
    {
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idPack.($idProductAttribute !== null ? (int)$idProductAttribute : ''), true);
        $productAttributeList = array();
        if (!self::isInCache($cacheId, true)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('pm_advancedpack_products', 'app');
            if ($idProductAttribute !== null) {
                $sql->innerJoin('pm_advancedpack_cart_products', 'acp', 'acp.`id_pack`='.(int)$idPack.' AND acp.`id_product_pack`=app.`id_product_pack` AND acp.`id_product_attribute_pack`='.(int)$idProductAttribute);
            }
            $sql->where('app.`id_pack`='.(int)$idPack);
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            if (AdvancedPackCoreClass::_isFilledArray($result)) {
                foreach ($result as $row) {
                    if ($idProductAttribute !== null) {
                        $productAttributeList[(int)$row['id_product_pack']] = (int)$row['id_product_attribute'];
                    } else {
                        $productAttributeList[(int)$row['id_product_pack']] = (int)$row['default_id_product_attribute'];
                    }
                }
            }
        } else {
            return self::getFromCache($cacheId, true);
        }
        self::storeInCache($cacheId, $productAttributeList, true);
        return $productAttributeList;
    }
    public static function getPackAttributeUniqueName($idPack, $idProductAttribute, $idLang = null)
    {
        if ($idLang == null) {
            $idLang = self::getContext()->language->id;
        }
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idPack.(int)$idProductAttribute.(int)$idLang);
        if (!self::isInCache($cacheId)) {
            $productCombination = new Combination($idProductAttribute);
            $productAttributesNames = $productCombination->getAttributesName($idLang);
            if (is_array($productAttributesNames) && count($productAttributesNames) == 1) {
                $attributeName = current($productAttributesNames);
                if (isset($attributeName['name']) && !empty($attributeName['name'])) {
                    self::storeInCache($cacheId, $attributeName['name']);
                    return $attributeName['name'];
                }
            }
        } else {
            return self::getFromCache($cacheId);
        }
        self::storeInCache($cacheId, false);
        return false;
    }
    public static function getProductAttributeList($idProductAttribute, $idLang = null)
    {
        if ($idLang == null) {
            $idLang = self::getContext()->language->id;
        }
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idProductAttribute.(int)$idLang);
        if (!self::isInCache($cacheId)) {
            $attributeList = array('attributes' => array(), 'attributes_small' => array());
            if ($idProductAttribute) {
                $result = Db::getInstance()->executeS('
                    SELECT pac.`id_product_attribute`, agl.`public_name` AS public_group_name, al.`name` AS attribute_name
                    FROM `'._DB_PREFIX_.'product_attribute_combination` pac
                    LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                    LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                    LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$idLang.')
                    LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$idLang.')
                    WHERE pac.`id_product_attribute`='.(int)$idProductAttribute.'
                    ORDER BY agl.`public_name` ASC');
                if (AdvancedPackCoreClass::_isFilledArray($result)) {
                    foreach ($result as $attributeRow) {
                        $attributeList['attributes'][] = $attributeRow['public_group_name'].' : '.$attributeRow['attribute_name'];
                        $attributeList['attributes_small'][] = $attributeRow['attribute_name'];
                    }
                    $attributeList['attributes'] = implode($attributeList['attributes'], ', ');
                    $attributeList['attributes_small'] = implode($attributeList['attributes_small'], ', ');
                }
            }
        } else {
            return self::getFromCache($cacheId);
        }
        self::storeInCache($cacheId, $attributeList);
        return $attributeList;
    }
    public static function getProductCombinations($idProduct, $ignoreModuleAttributeGroup = true)
    {
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idProduct.(int)$ignoreModuleAttributeGroup.self::getContext()->shop->id);
        if (!self::isInCache($cacheId)) {
            $combinationsList = array();
            $result = Db::getInstance()->executeS('
                SELECT pac.`id_product_attribute`, pac.`id_attribute`
                FROM `'._DB_PREFIX_.'product_attribute` pa
                ' . Shop::addSqlAssociation('product_attribute', 'pa') .
                'JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`'
                . ($ignoreModuleAttributeGroup ? ' JOIN `'._DB_PREFIX_.'attribute` a ON (a.`id_attribute` = pac.`id_attribute` AND a.`id_attribute_group` != ' . (int)self::getPackAttributeGroupId() . ')' : '') .
                'WHERE pa.`id_product` = ' . (int)$idProduct);
            if (AdvancedPackCoreClass::_isFilledArray($result)) {
                foreach ($result as $combinationRow) {
                    $combinationsList[(int)$combinationRow['id_product_attribute']][] = (int)$combinationRow['id_attribute'];
                }
            }
        } else {
            return self::getFromCache($cacheId);
        }
        self::storeInCache($cacheId, $combinationsList);
        return $combinationsList;
    }
    public static function getProductCombinationsByIdProductPack($idProductPack)
    {
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idProductPack.self::getContext()->shop->id);
        if (!self::isInCache($cacheId)) {
            $combinationsList = array();
            $result = Db::getInstance()->executeS('
                SELECT pac.`id_product_attribute`, pac.`id_attribute`
                FROM `'._DB_PREFIX_.'product_attribute` pa
                ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                JOIN `'._DB_PREFIX_.'pm_advancedpack_products` app ON app.`id_product` = pa.`id_product`
                JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                WHERE app.`id_product_pack` = ' . (int)$idProductPack);
            if (AdvancedPackCoreClass::_isFilledArray($result)) {
                foreach ($result as $combinationRow) {
                    $combinationsList[(int)$combinationRow['id_product_attribute']][] = (int)$combinationRow['id_attribute'];
                }
            }
        } else {
            return self::getFromCache($cacheId);
        }
        self::storeInCache($cacheId, $combinationsList);
        return $combinationsList;
    }
    public static function getProductAttributeWhiteList($idProductPack)
    {
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idProductPack);
        $whiteListFinal = array();
        if (!self::isInCache($cacheId)) {
            $sql = new DbQuery();
            $sql->select('appa.`id_product_attribute`');
            $sql->from('pm_advancedpack_products', 'app');
            $sql->innerJoin('pm_advancedpack_products_attributes', 'appa', 'appa.`id_product_pack`=app.`id_product_pack`');
            $sql->where('app.`id_product_pack`='.(int)$idProductPack);
            $whiteList = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            if (AdvancedPackCoreClass::_isFilledArray($whiteList)) {
                foreach ($whiteList as $whiteListRow) {
                    $whiteListFinal[] = (int)$whiteListRow['id_product_attribute'];
                }
                self::storeInCache($cacheId, $whiteListFinal);
                return $whiteListFinal;
            }
        } else {
            return self::getFromCache($cacheId);
        }
        self::storeInCache($cacheId, $whiteListFinal);
        return $whiteListFinal;
    }
    public static function getPackProductAttributeList($idPack)
    {
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idPack);
        $attributeReductionList = $attributeReductionListFinal = array();
        if (!self::isInCache($cacheId)) {
            $sql = new DbQuery();
            $sql->select('appa.*');
            $sql->from('pm_advancedpack_products', 'app');
            $sql->innerJoin('pm_advancedpack_products_attributes', 'appa', 'appa.`id_product_pack`=app.`id_product_pack`');
            $sql->where('app.`id_pack`='.(int)$idPack);
            $attributeReductionList = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            if (AdvancedPackCoreClass::_isFilledArray($attributeReductionList)) {
                foreach ($attributeReductionList as $attributeReductionListRow) {
                    $attributeReductionListFinal[(int)$attributeReductionListRow['id_product_pack']][(int)$attributeReductionListRow['id_product_attribute']] = array(
                        'reduction_amount' => $attributeReductionListRow['reduction_amount'],
                        'reduction_type' => $attributeReductionListRow['reduction_type'],
                    );
                }
                self::storeInCache($cacheId, $attributeReductionListFinal);
                return $attributeReductionListFinal;
            }
        } else {
            return self::getFromCache($cacheId);
        }
        self::storeInCache($cacheId, $attributeReductionListFinal);
        return $attributeReductionListFinal;
    }
    public static function getProductCustomizationFieldWhiteList($idProductPack)
    {
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idProductPack);
        $whiteListFinal = array();
        if (!self::isInCache($cacheId)) {
            $sql = new DbQuery();
            $sql->select('appc.`id_customization_field`');
            $sql->from('pm_advancedpack_products', 'app');
            $sql->innerJoin('pm_advancedpack_products_customization', 'appc', 'appc.`id_product_pack`=app.`id_product_pack`');
            $sql->where('app.`id_product_pack`='.(int)$idProductPack);
            $whiteList = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            if (AdvancedPackCoreClass::_isFilledArray($whiteList)) {
                foreach ($whiteList as $whiteListRow) {
                    $whiteListFinal[] = (int)$whiteListRow['id_customization_field'];
                }
                self::storeInCache($cacheId, $whiteListFinal);
                return $whiteListFinal;
            }
        } else {
            return self::getFromCache($cacheId);
        }
        self::storeInCache($cacheId, $whiteListFinal);
        return $whiteListFinal;
    }
    public static function getPackAttributeGroupId()
    {
        $cacheId = self::getPMCacheId(__METHOD__.self::getContext()->language->id.self::getContext()->shop->id);
        if (!self::isInCache($cacheId)) {
            $attributeGroups = AttributeGroup::getAttributesGroups(self::getContext()->language->id);
            if (AdvancedPackCoreClass::_isFilledArray($attributeGroups)) {
                foreach ($attributeGroups as $attributeGroup) {
                    if ($attributeGroup['name'] == 'AP5-Pack') {
                        self::storeInCache($cacheId, (int)$attributeGroup['id_attribute_group']);
                        return (int)$attributeGroup['id_attribute_group'];
                    }
                }
            }
        } else {
            return self::getFromCache($cacheId);
        }
        self::storeInCache($cacheId, false);
        return false;
    }
    public static function getIdCountryListByIdPack($idPack, $addAllActive = false)
    {
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idPack, true);
        $idCountryList = array(0, self::getContext()->country->id);
        list($address) = self::getAddressInstance();
        if (is_object($address) && !empty($address->id_country)) {
            $idCountryList[] = (int)$address->id_country;
        }
        if ($addAllActive) {
            $countries = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT c.`id_country` FROM `'._DB_PREFIX_.'country` c '.Shop::addSqlAssociation('country', 'c').' WHERE c.`active`=1');
            if (AdvancedPackCoreClass::_isFilledArray($countries)) {
                foreach ($countries as $country) {
                    $idCountryList[] = (int)$country['id_country'];
                }
            }
        }
        if (!self::isInCache($cacheId, true)) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT DISTINCT sp.`id_country`
                FROM `'._DB_PREFIX_.'specific_price` sp
                WHERE sp.`id_product` IN (
                    SELECT app.`id_product`
                    FROM `'._DB_PREFIX_.'pm_advancedpack_products` app
                    WHERE app.`id_pack`=' . (int)$idPack . '
                )
            ');
            if (AdvancedPackCoreClass::_isFilledArray($result)) {
                foreach ($result as $row) {
                    $idCountryList[] = (int)$row['id_country'];
                }
            }
        } else {
            return self::getFromCache($cacheId, true);
        }
        $idCountryList = array_unique($idCountryList);
        self::storeInCache($cacheId, $idCountryList, true);
        return $idCountryList;
    }
    public static function getIdGroupListByIdPack($idPack)
    {
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idPack, true);
        $idGroupList = array(0);
        if (!self::isInCache($cacheId, true)) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT DISTINCT sp.`id_group`
                FROM `'._DB_PREFIX_.'specific_price` sp
                WHERE sp.`id_product` IN (
                    SELECT app.`id_product`
                    FROM `'._DB_PREFIX_.'pm_advancedpack_products` app
                    WHERE app.`id_pack`=' . (int)$idPack . '
                )
            ');
            if (AdvancedPackCoreClass::_isFilledArray($result)) {
                foreach ($result as $row) {
                    $idGroupList[] = (int)$row['id_group'];
                }
            }
        } else {
            return self::getFromCache($cacheId, true);
        }
        $idGroupList = array_unique($idGroupList);
        self::storeInCache($cacheId, $idGroupList, true);
        return $idGroupList;
    }
    public static function getIdCurrencyListByIdPack($idPack, $addAllActive = false)
    {
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idPack, true);
        $idCurrencyList = array(0, (int)Configuration::get('PS_CURRENCY_DEFAULT'));
        if (isset(self::getContext()->currency) && Validate::isLoadedObject(self::getContext()->currency)) {
            $idCurrencyList[] = self::getContext()->currency->id;
        }
        if ($addAllActive) {
            $currencies = Currency::getCurrencies(false, true);
            if (AdvancedPackCoreClass::_isFilledArray($currencies)) {
                foreach ($currencies as $currency) {
                    $idCurrencyList[] = (int)$currency['id_currency'];
                }
            }
        }
        if (!self::isInCache($cacheId, true)) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT DISTINCT sp.`id_currency`
                FROM `'._DB_PREFIX_.'specific_price` sp
                WHERE sp.`id_product` IN (
                    SELECT app.`id_product`
                    FROM `'._DB_PREFIX_.'pm_advancedpack_products` app
                    WHERE app.`id_pack`=' . (int)$idPack . '
                )
            ');
            if (AdvancedPackCoreClass::_isFilledArray($result)) {
                foreach ($result as $row) {
                    $idCurrencyList[] = (int)$row['id_currency'];
                }
            }
        } else {
            return self::getFromCache($cacheId, true);
        }
        $idCurrencyList = array_unique($idCurrencyList);
        self::storeInCache($cacheId, $idCurrencyList, true);
        return $idCurrencyList;
    }
    public static function addCustomPackProductAttribute($idPack, $attributesList, $packUniqueHash = false, $defaultCombination = false)
    {
        $idProductAttribute = false;
        $combinationObj = null;
        if ($packUniqueHash !== false) {
            $idProductAttribute = (int)Db::getInstance()->getValue('SELECT `id_product_attribute_pack` FROM `'._DB_PREFIX_.'pm_advancedpack_cart_products` WHERE `id_order` IS NULL AND `unique_hash` = "'.pSQL($packUniqueHash).'" AND `id_pack` = '.(int)$idPack.' AND `id_cart` = ' . (int)self::getContext()->cookie->id_cart);
            if ($idProductAttribute) {
                $combinationObj = new Combination($idProductAttribute);
                if (!Validate::isLoadedObject($combinationObj)) {
                    Db::getInstance()->getValue('DELETE FROM `'._DB_PREFIX_.'pm_advancedpack_cart_products` WHERE `id_product_attribute_pack`='.(int)$idProductAttribute.' AND `id_order` IS NULL AND `unique_hash` = "'.pSQL($packUniqueHash).'" AND `id_pack` = '.(int)$idPack.' AND `id_cart` = ' . (int)self::getContext()->cookie->id_cart);
                    $idProductAttribute = false;
                }
            }
        }
        if (!$idProductAttribute && $defaultCombination) {
            $idProductAttribute = Product::getDefaultAttribute($idPack);
            if ($idProductAttribute) {
                $combinationObj = new Combination($idProductAttribute);
                if (!Validate::isLoadedObject($combinationObj)) {
                    $idProductAttribute = false;
                } else {
                    $hasRealDefaultAttribute = false;
                    $tmpAttributeList = AdvancedPack::getProductAttributeList($combinationObj->id);
                    if (isset($tmpAttributeList['attributes_small']) && $tmpAttributeList['attributes_small'] == $idPack.'-defaultCombination') {
                        $hasRealDefaultAttribute = true;
                    }
                    if (!$hasRealDefaultAttribute) {
                        $idProductAttribute = false;
                        $combinationObj->default_on = false;
                        $combinationObj->save();
                        $combinationObj = null;
                    }
                }
            }
        }
        if (!$idProductAttribute) {
            if ($defaultCombination) {
                $uniqueId = $idPack.'-defaultCombination';
            } else {
                $uniqueId = uniqid();
            }
            $attributeObj = new Attribute();
            $attributeObj->id_attribute_group = self::getPackAttributeGroupId();
            foreach (Language::getLanguages(false) as $lang) {
                $attributeObj->name[$lang['id_lang']] = $uniqueId;
            }
            if ($attributeObj->save()) {
                $idAttribute = $attributeObj->id;
                $combinationObj = new Combination();
                $combinationObj->id_product = (int)$idPack;
                $combinationObj->default_on = (bool)$defaultCombination;
                if ($defaultCombination) {
                    $combinationObj->quantity = self::getPackAvailableQuantity($idPack, $attributesList, array(), array(), false, false);
                }
                $idWarehouse = false;
                $packProducts = self::getPackContent($idPack);
                if (AdvancedPackCoreClass::_isFilledArray($packProducts)) {
                    foreach ($packProducts as $packProduct) {
                        $idProductAttributeWeight = (isset($attributesList[(int)$packProduct['id_product_pack']]) ? $attributesList[(int)$packProduct['id_product_pack']] : (int)$packProduct['default_id_product_attribute']);
                        if ($idProductAttributeWeight) {
                            $combinationWeightObj = new Combination($idProductAttributeWeight);
                            if (Validate::isLoadedObject($combinationWeightObj)) {
                                $combinationObj->weight += (float)$combinationWeightObj->weight * (int)$packProduct['quantity'];
                            }
                            unset($combinationWeightObj);
                        }
                        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && !$idWarehouse) {
                            $warehouseList = Warehouse::getProductWarehouseList((int)$packProduct['id_product'], $idProductAttributeWeight);
                            if (AdvancedPackCoreClass::_isFilledArray($warehouseList)) {
                                foreach ($warehouseList as $warehouseRow) {
                                    $idWarehouse = (int)$warehouseRow['id_warehouse'];
                                    break;
                                }
                            }
                        }
                    }
                }
                unset($packProducts);
                if (!$combinationObj->save() || !$combinationObj->setAttributes(array($idAttribute))) {
                    return false;
                }
                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $idWarehouse) {
                    $warehouseLocationEntity = new WarehouseProductLocation();
                    $warehouseLocationEntity->id_product = (int)$combinationObj->id_product;
                    $warehouseLocationEntity->id_product_attribute = (int)$combinationObj->id;
                    $warehouseLocationEntity->id_warehouse = (int)$idWarehouse;
                    $warehouseLocationEntity->location = '';
                    $warehouseLocationEntity->save();
                    StockAvailable::synchronize((int)$combinationObj->id_product);
                }
            }
        } else {
            $combinationObj = new Combination($idProductAttribute);
        }
        if (!Validate::isLoadedObject($combinationObj)) {
            return false;
        }
        if (AdvancedPack::isValidPack($idPack, true)) {
            self::setStockAvailableQuantity($idPack, $combinationObj->id, self::getPackAvailableQuantity($idPack, $attributesList, array(), array(), $combinationObj->id, false), false);
        } else {
            self::setStockAvailableQuantity($idPack, $combinationObj->id, 0, false);
        }
        if ($defaultCombination) {
            self::setDefaultPackAttribute((int)$idPack, (int)$combinationObj->id);
        }
        return (int)$combinationObj->id;
    }
    public static function setStockAvailableQuantity($idProduct, $idProductAttribute, $quantity, $runUpdateQuantityHook = true)
    {
        $combinationObj = new Combination($idProductAttribute, null, (int)AdvancedPack::getPackIdShop($idProduct));
        if (Validate::isLoadedObject($combinationObj)) {
            $currentQuantity = (int)StockAvailable::getQuantityAvailableByProduct((int)$idProduct, (int)$idProductAttribute);
            if ($combinationObj->quantity != $quantity) {
                $combinationObj->quantity = (int)$quantity;
                $combinationObj->minimal_quantity = 1;
                $combinationObj->save();
            }
            if ($currentQuantity != $quantity) {
                if ($runUpdateQuantityHook) {
                    return StockAvailable::setQuantity((int)$idProduct, (int)$idProductAttribute, (int)$quantity);
                } else {
                    $id_shop = null;
                    if (Shop::getContext() != Shop::CONTEXT_GROUP) {
                        $id_shop = (int)self::getContext()->shop->id;
                    }
                    $id_stock_available = (int)StockAvailable::getStockAvailableIdByProductId((int)$idProduct, (int)$idProductAttribute, $id_shop);
                    if ($id_stock_available) {
                        $stock_available = new StockAvailable($id_stock_available);
                        if ((int)$stock_available->quantity != (int)$quantity) {
                            $stock_available->quantity = (int)$quantity;
                            $stock_available->update();
                        }
                    } else {
                        $out_of_stock = StockAvailable::outOfStock((int)$idProduct, $id_shop);
                        $stock_available = new StockAvailable();
                        $stock_available->out_of_stock = (int)$out_of_stock;
                        $stock_available->id_product = (int)$idProduct;
                        $stock_available->id_product_attribute = (int)$idProductAttribute;
                        $stock_available->quantity = (int)$quantity;
                        if ($id_shop === null) {
                            $shop_group = Shop::getContextShopGroup();
                        } else {
                            $shop_group = new ShopGroup((int)Shop::getGroupFromShop((int)$id_shop));
                        }
                        if ($shop_group->share_stock) {
                            $stock_available->id_shop = 0;
                            $stock_available->id_shop_group = (int)$shop_group->id;
                        } else {
                            $stock_available->id_shop = (int)$id_shop;
                            $stock_available->id_shop_group = 0;
                        }
                        $stock_available->add();
                    }
                    Cache::clean('StockAvailable::getQuantityAvailableByProduct_'.(int)$idProduct.'*');
                }
            }
        }
        return false;
    }
    public static function updatePackStock($idPack)
    {
        self::updateFakePackCombinationStock($idPack);
        self::setStockAvailableQuantity((int)$idPack, (int)Product::getDefaultAttribute($idPack), self::getPackAvailableQuantity($idPack, array(), array(), array(), false, false), false);
    }
    public static function updateFakePackCombinationStock($idPack)
    {
        $packProducts = self::getPackContent($idPack);
        $minStockAvailableByIdAttribute = array();
        $minStockAvailableForProductsWithoutAttributes = null;
        if (AdvancedPackCoreClass::_isFilledArray($packProducts)) {
            foreach ($packProducts as $packProduct) {
                $product = new Product((int)$packProduct['id_product']);
                $attributesWhitelist = self::getProductAttributeWhiteList($packProduct['id_product_pack']);
                $isAvailableWhenOutOfStock = Product::isAvailableWhenOutOfStock(StockAvailable::outOfStock((int)$packProduct['id_product']));
                if (AdvancedPackCoreClass::_isFilledArray($attributesWhitelist)) {
                    foreach ($attributesWhitelist as $idProductAttribute) {
                        $combinationList = $product->getAttributeCombinationsById($idProductAttribute, self::getContext()->language->id);
                        if (AdvancedPackCoreClass::_isFilledArray($combinationList)) {
                            foreach ($combinationList as $combinationRow) {
                                if ($isAvailableWhenOutOfStock) {
                                    $stockAvailable = self::PACK_FAKE_STOCK;
                                } else {
                                    $stockAvailable =  (int)$combinationRow['quantity'];
                                }
                                if (!isset($minStockAvailableByIdAttribute[(int)$combinationRow['id_attribute']]) || $stockAvailable < $minStockAvailableByIdAttribute[(int)$combinationRow['id_attribute']]) {
                                    $minStockAvailableByIdAttribute[(int)$combinationRow['id_attribute']] = $stockAvailable;
                                }
                            }
                        }
                    }
                } else {
                    $combinationList = $product->getAttributeCombinations(self::getContext()->language->id);
                    if (AdvancedPackCoreClass::_isFilledArray($combinationList)) {
                        foreach ($combinationList as $combinationRow) {
                            if ($isAvailableWhenOutOfStock) {
                                $stockAvailable = self::PACK_FAKE_STOCK;
                            } else {
                                $stockAvailable =  (int)$combinationRow['quantity'];
                            }
                            if (!isset($minStockAvailableByIdAttribute[(int)$combinationRow['id_attribute']]) || $stockAvailable < $minStockAvailableByIdAttribute[(int)$combinationRow['id_attribute']]) {
                                $minStockAvailableByIdAttribute[(int)$combinationRow['id_attribute']] = $stockAvailable;
                            }
                        }
                    } else {
                        if ($isAvailableWhenOutOfStock) {
                            $stockAvailable = self::PACK_FAKE_STOCK;
                        } else {
                            $stockAvailable = StockAvailable::getQuantityAvailableByProduct((int)$packProduct['id_product']);
                        }
                        if ($minStockAvailableForProductsWithoutAttributes == null || $stockAvailable < $minStockAvailableForProductsWithoutAttributes) {
                            $minStockAvailableForProductsWithoutAttributes = $stockAvailable;
                        }
                    }
                }
            }
        }
        $combinationList = self::getProductCombinations($idPack, true);
        if (AdvancedPackCoreClass::_isFilledArray($combinationList)) {
            foreach ($combinationList as $packIdProductAttribute => $attributeList) {
                $idAttribute = current($attributeList);
                $availableQuantity = (isset($minStockAvailableByIdAttribute[$idAttribute]) ? $minStockAvailableByIdAttribute[$idAttribute] : 1);
                if ($minStockAvailableForProductsWithoutAttributes !== null) {
                    $availableQuantity = min(array($minStockAvailableForProductsWithoutAttributes, $availableQuantity));
                }
                if ($minStockAvailableForProductsWithoutAttributes !== null) {
                    $availableQuantity = min(array($minStockAvailableForProductsWithoutAttributes, $availableQuantity));
                }
                self::setStockAvailableQuantity((int)$idPack, (int)$packIdProductAttribute, $availableQuantity, false);
            }
        }
    }
    public static function isValidPack($idPack, $deepCheck = false, $packExcludeList = array())
    {
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idPack.(int)$deepCheck.serialize($packExcludeList).($deepCheck && isset(self::getContext()->customer) ? self::getContext()->customer->id : 0));
        if (!self::isInCache($cacheId)) {
            $packIdList = AdvancedPack::getIdsPacks(true);
            $result = in_array((int)$idPack, $packIdList);
            if ($result && $deepCheck) {
                $packContent = AdvancedPack::getPackContent($idPack);
                if ($packContent !== false) {
                    foreach ($packContent as $packProduct) {
                        if (in_array((int)$packProduct['id_product_pack'], $packExcludeList)) {
                            continue;
                        }
                        $product = new Product((int)$packProduct['id_product']);
                        $result &= Validate::isLoadedObject($product) && $product->active;
                        $result &= Validate::isLoadedObject($product) && $product->checkAccess(isset(self::getContext()->customer) ? self::getContext()->customer->id : 0);
                        $result &= Validate::isLoadedObject($product) && $product->available_for_order;
                    }
                }
            }
            self::storeInCache($cacheId, $result);
            return $result;
        } else {
            return self::getFromCache($cacheId);
        }
        self::storeInCache($cacheId, false);
        return false;
    }
    public static function isVirtualPack($idPack)
    {
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idPack);
        if (!self::isInCache($cacheId)) {
            $packContent = self::getPackContent($idPack);
            $isVirtual = true;
            if ($packContent !== false) {
                foreach ($packContent as $packProduct) {
                    $product = new Product((int)$packProduct['id_product']);
                    if ($product->getType() != Product::PTYPE_VIRTUAL) {
                        $isVirtual = false;
                        break;
                    }
                }
            }
            self::storeInCache($cacheId, $isVirtual);
            return $isVirtual;
        } else {
            return self::getFromCache($cacheId);
        }
        self::storeInCache($cacheId, false);
        return false;
    }
    public static function isInStock($idPack, $quantity = 1, $attributesList = array(), $incrementCartQuantity = false, $idProductAttribute = false)
    {
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idPack.serialize($attributesList).(int)$incrementCartQuantity.(int)$idProductAttribute, true);
        $packIsInStock = true;
        if (!self::isInCache($cacheId, true)) {
            $currentPackCartStock = self::getPackProductsCartQuantity();
            if ($incrementCartQuantity) {
                $packContent = self::getPackContent($idPack, $idProductAttribute);
            } else {
                $packContent = self::getPackContent($idPack);
            }
            if ($packContent !== false) {
                foreach ($packContent as $packProduct) {
                    if (!isset($attributesList[$packProduct['id_product_pack']]) || !is_numeric($attributesList[$packProduct['id_product_pack']])) {
                        $idProductAttribute = (int)$packProduct['default_id_product_attribute'];
                    } else {
                        $idProductAttribute = (int)$attributesList[$packProduct['id_product_pack']];
                    }
                    $cartPackStock = 0;
                    if (isset($currentPackCartStock[(int)$packProduct['id_product']][$idProductAttribute])) {
                        $cartPackStock = $currentPackCartStock[(int)$packProduct['id_product']][$idProductAttribute];
                    }
                    if (Product::isAvailableWhenOutOfStock(StockAvailable::outOfStock((int)$packProduct['id_product']))) {
                        $packIsInStock &= true;
                    } else {
                        $stockAvailable = ((int)StockAvailable::getQuantityAvailableByProduct((int)$packProduct['id_product'], $idProductAttribute) * $quantity) - self::getCartQuantity((int)$packProduct['id_product'], $idProductAttribute) - $cartPackStock;
                        if ($incrementCartQuantity) {
                            $packIsInStock &= $stockAvailable >= 0;
                        } else {
                            $packIsInStock &= $stockAvailable >= ((int)$packProduct['quantity'] * $quantity);
                        }
                    }
                }
            }
        } else {
            return self::getFromCache($cacheId, true);
        }
        self::storeInCache($cacheId, (int)$packIsInStock, true);
        return (int)$packIsInStock;
    }
    public static function getPackAsmState($idPack)
    {
        if (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
            return false;
        }
        $packProducts = self::getPackContent($idPack);
        $res = true;
        if (AdvancedPackCoreClass::_isFilledArray($packProducts)) {
            $idShop = (int)AdvancedPack::getPackIdShop($idPack);
            foreach ($packProducts as $packProduct) {
                $product = new Product((int)$packProduct['id_product'], false, null, $idShop);
                $res &= (bool)$product->advanced_stock_management;
            }
        }
        return $res;
    }
    public static function getPackIdShop($idPack)
    {
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idPack, true);
        $idShop = false;
        if (!self::isInCache($cacheId, true)) {
            $sql = new DbQuery();
            $sql->select('ap.`id_shop`');
            $sql->from('pm_advancedpack', 'ap');
            $sql->where('ap.`id_pack`='.(int)$idPack);
            $idShop = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        } else {
            return self::getFromCache($cacheId, true);
        }
        self::storeInCache($cacheId, $idShop, true);
        return $idShop;
    }
    public static function isFromShop($idPack, $idShop)
    {
        return (self::getPackIdShop($idPack) == $idShop);
    }
    public static function combinationExists($idProductPack, $attributesList)
    {
        $attributesWhitelist = self::getProductAttributeWhiteList($idProductPack);
        foreach (self::getProductCombinationsByIdProductPack($idProductPack) as $idProductAttribute => $combinationAttributesList) {
            if (AdvancedPackCoreClass::_isFilledArray($attributesWhitelist) && !in_array($idProductAttribute, $attributesWhitelist)) {
                continue;
            }
            if (!count(array_diff($combinationAttributesList, $attributesList))) {
                return (int)$idProductAttribute;
            }
        }
        return false;
    }
    public static function clonePackImages($idPack)
    {
        $packProducts = self::getPackContent($idPack);
        $res = true;
        $defaultPackImagePath = dirname(__FILE__) . '/img/default-pack-image.png';
        $coverImage = new Image();
        $coverImage->id_product = (int)$idPack;
        $coverImage->position = Image::getHighestPosition($idPack) + 1;
        if ($coverImage->add() && ($new_path = $coverImage->getPathForCreation()) && ImageManager::resize($defaultPackImagePath, $new_path.'.'.$coverImage->image_format)) {
            foreach (ImageType::getImagesTypes('products') as $imageType) {
                $res &= ImageManager::resize($defaultPackImagePath, $new_path.'-'.Tools::stripslashes($imageType['name']).'.'.$coverImage->image_format, $imageType['width'], $imageType['height'], $coverImage->image_format);
            }
        }
        if (AdvancedPackCoreClass::_isFilledArray($packProducts)) {
            foreach ($packProducts as $packProduct) {
                $res &= Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'image` i, `'._DB_PREFIX_.'image_shop` i_shop SET i.`cover` = NULL, i_shop.`cover` = NULL WHERE i.`id_image`=i_shop.`id_image` AND i.`id_product` = '.(int)$idPack);
                $res &= Image::duplicateProductImages($packProduct['id_product'], $idPack, array());
            }
        }
        if (Validate::isLoadedObject($coverImage)) {
            $res &= Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'image` i, `'._DB_PREFIX_.'image_shop` i_shop SET i.`cover` = NULL, i_shop.`cover` = NULL WHERE i.`id_image`=i_shop.`id_image` AND i.`id_product` = '.(int)$idPack);
            $i = 2;
            $result = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'image` WHERE `id_product` = '.(int)$idPack.' AND `id_image` != '.(int)$coverImage->id.' ORDER BY `position`');
            if ($result) {
                foreach ($result as $row) {
                    $res &= Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'image` SET `position` = '.(int)$i.' WHERE `id_image` = '.(int)$row['id_image']);
                    $i++;
                }
            }
            $coverImage->cover = 1;
            $coverImage->update();
        }
        return $res;
    }
    protected static function setDefaultPackAttribute($idPack, $idProductAttribute)
    {
        $result = Db::getInstance()->update('product_shop', array('cache_default_attribute' => $idProductAttribute), 'id_product = '.(int)$idPack . Shop::addSqlRestriction());
        $result &= Db::getInstance()->update('product', array('cache_default_attribute' => $idProductAttribute), 'id_product = ' . (int)$idPack);
        $result &= Db::getInstance()->update('product_attribute_shop', array('default_on' => 1), 'id_product_attribute = ' . (int)$idProductAttribute . Shop::addSqlRestriction());
        $result &= Db::getInstance()->update('product_attribute', array('default_on' => 1), 'id_product_attribute = ' . (int)$idProductAttribute);
        if (version_compare(_PS_VERSION_, '1.6.0.0', '>=') && method_exists('Tools', 'clearColorListCache')) {
            Tools::clearColorListCache($idPack);
        }
        return $result;
    }
    public static function clonePackAttributes($idPack)
    {
        $packProducts = self::getPackContent($idPack);
        $finalAttributesList = array();
        $res = true;
        if (AdvancedPackCoreClass::_isFilledArray($packProducts)) {
            foreach ($packProducts as $packProduct) {
                $product = new Product((int)$packProduct['id_product']);
                $attributesWhitelist = self::getProductAttributeWhiteList($packProduct['id_product_pack']);
                if (AdvancedPackCoreClass::_isFilledArray($attributesWhitelist)) {
                    foreach ($attributesWhitelist as $idProductAttribute) {
                        $combinationList = $product->getAttributeCombinationsById($idProductAttribute, self::getContext()->language->id);
                        if (AdvancedPackCoreClass::_isFilledArray($combinationList)) {
                            foreach ($combinationList as $combinationRow) {
                                $finalAttributesList[] = (int)$combinationRow['id_attribute'];
                            }
                        }
                    }
                } else {
                    $combinationList = $product->getAttributeCombinations(self::getContext()->language->id);
                    if (AdvancedPackCoreClass::_isFilledArray($combinationList)) {
                        foreach ($combinationList as $combinationRow) {
                            $finalAttributesList[] = (int)$combinationRow['id_attribute'];
                        }
                    }
                }
            }
        }
        $productPack = new Product((int)$idPack);
        $combinationList = $productPack->getAttributeCombinations(self::getContext()->language->id);
        if (AdvancedPackCoreClass::_isFilledArray($combinationList)) {
            $combinationToDelete = array();
            foreach ($combinationList as $combinationRow) {
                if ($combinationRow['id_attribute_group'] != self::getPackAttributeGroupId()) {
                    $combinationToDelete[] = (int)$combinationRow['id_product_attribute'];
                }
            }
            if (AdvancedPackCoreClass::_isFilledArray($combinationToDelete)) {
                $res &= Db::getInstance()->delete('product_attribute', '`id_product_attribute` IN ('. implode(',', $combinationToDelete) .')');
                $res &= Db::getInstance()->delete('product_attribute_shop', '`id_product_attribute` IN ('. implode(',', $combinationToDelete) .')');
                $res &= Db::getInstance()->delete('product_attribute_combination', '`id_product_attribute` IN ('. implode(',', $combinationToDelete) .')');
                $res &= Db::getInstance()->delete('cart_product', '`id_product_attribute` IN ('. implode(',', $combinationToDelete) .')');
                $res &= Db::getInstance()->delete('product_attribute_image', '`id_product_attribute` IN ('. implode(',', $combinationToDelete) .')');
                $res &= Db::getInstance()->delete('stock_available', '`id_product_attribute` IN ('. implode(',', $combinationToDelete) .')');
            }
        }
        if (AdvancedPackCoreClass::_isFilledArray($finalAttributesList)) {
            $finalAttributesList = array_unique($finalAttributesList);
            $packIdShop = (int)AdvancedPack::getPackIdShop($idPack);
            foreach ($finalAttributesList as $idAttribute) {
                $obj = new Combination(null, null, $packIdShop);
                $obj->id_product = (int)$idPack;
                $obj->price = 0;
                $obj->weight = 0;
                $obj->ecotax = 0;
                $obj->quantity = 0;
                $obj->reference = '';
                $obj->minimal_quantity = 1;
                if ($obj->add()) {
                    $res &= Db::getInstance()->insert('product_attribute_combination', array(
                        'id_product_attribute' => (int)$obj->id,
                        'id_attribute' => (int)$idAttribute
                    ));
                }
            }
            self::updateFakePackCombinationStock((int)$idPack);
        }
        self::addCustomPackProductAttribute($idPack, array(), false, true);
        return $res;
    }
    private static function checkCustomizationErrors($idPack, $customizationList, $moduleInstance, $fromCartController = true, $packExcludeList = array())
    {
        $customizationError = false;
        $context = self::getContext();
        $packCustomizationRequiredFields = self::getPackCustomizationRequiredFields($idPack, $packExcludeList);
        if (AdvancedPackCoreClass::_isFilledArray($packCustomizationRequiredFields) && !AdvancedPackCoreClass::_isFilledArray($customizationList)) {
            $customizationError = true;
        } else {
            if (AdvancedPackCoreClass::_isFilledArray($customizationList)) {
                foreach ($customizationList as $customization) {
                    foreach ($customization as $idCustomizationField => $value) {
                        if (in_array($idCustomizationField, $packCustomizationRequiredFields) && !Tools::strlen($value)) {
                            $customizationError = true;
                            break;
                        }
                    }
                }
            }
        }
        if ($customizationError) {
            $errors = array(Tools::displayError($moduleInstance->getFrontTranslation('errorInvalidCustomization'), false));
            if ($fromCartController) {
                die(Tools::jsonEncode(array('hasError' => true, 'errors' => $errors)));
            } else {
                $context->controller->errors = $errors;
                return $customizationError;
            }
        }
        return $customizationError;
    }
    public static function addPackToCart($idPack, $quantity = 1, $idProductAttributeList = array(), $customizationList = array(), $fromCartController = true, $fromProductController = false)
    {
        $errors = array();
        $moduleInstance = AdvancedPack::getModuleInstance();
        $context = self::getContext();
        if (self::isValidPack($idPack, true)) {
            if (!count($idProductAttributeList)) {
                $idProductAttributeList = self::getIdProductAttributeListByIdPack($idPack);
            }
            ksort($idProductAttributeList);
            $packUniqueHash = md5((int)$context->cookie->id_cart . '-' . (int)$idPack . '-' . serialize($idProductAttributeList) . (sizeof($customizationList) ? serialize($customizationList) : ''));
            if (self::isInStock($idPack, $quantity, $idProductAttributeList)) {
                $customizationHasError = self::checkCustomizationErrors($idPack, $customizationList, $moduleInstance, $fromCartController);
                $idProductAttribute = self::addCustomPackProductAttribute($idPack, $idProductAttributeList, $packUniqueHash);
                $idAddressDelivery = (int)Tools::getValue('id_address_delivery');
                if (!$customizationHasError && is_numeric($idProductAttribute) && $idProductAttribute > 0 && $idProductAttribute !== false) {
                    if (self::addPackSpecificPrice($idPack, $idProductAttribute, $idProductAttributeList)) {
                        $updateQuantity = $context->cart->updateQty($quantity, $idPack, $idProductAttribute, null, 'up', $idAddressDelivery);
                        if (!$updateQuantity) {
                            $errors[] = Tools::displayError($moduleInstance->getFrontTranslation('errorMaximumQuantity'), false);
                        } else {
                            $resPackAdd = true;
                            $packProducts = self::getPackContent($idPack);
                            if (AdvancedPackCoreClass::_isFilledArray($packProducts)) {
                                $values = array();
                                foreach ($packProducts as $packProduct) {
                                    $productPackIdAttribute = (isset($idProductAttributeList[(int)$packProduct['id_product_pack']]) ? $idProductAttributeList[(int)$packProduct['id_product_pack']] : (int)$packProduct['default_id_product_attribute']);
                                    $packCustomizationList = (isset($customizationList[(int)$packProduct['id_product_pack']]) ? $customizationList[(int)$packProduct['id_product_pack']] : null);
                                    $values[] = '('.(int)$context->cookie->id_cart.', '.(int)$context->shop->id.', '.(int)$idPack.', '.(int)$packProduct['id_product_pack'].', '.(int)$idProductAttribute.', '.(int)$productPackIdAttribute.', "'.pSQL($packUniqueHash).'", ' . (AdvancedPackCoreClass::_isFilledArray($packCustomizationList) ? '"' . pSQL(Tools::jsonEncode($packCustomizationList)) . '"' : 'NULL') . ')';
                                }
                                if (AdvancedPackCoreClass::_isFilledArray($values)) {
                                    $resPackAdd &= Db::getInstance()->execute('INSERT IGNORE INTO `'._DB_PREFIX_.'pm_advancedpack_cart_products` (`id_cart`, `id_shop`, `id_pack`, `id_product_pack`, `id_product_attribute_pack`, `id_product_attribute`, `unique_hash`, `customization_infos`) VALUES '.implode($values, ','));
                                }
                            }
                            if ($resPackAdd) {
                                if ($fromCartController) {
                                    ob_start();
                                    $cartController = new CartController();
                                    $cartController->displayAjax();
                                    $jsonCartContent = (array)Tools::jsonDecode(ob_get_contents(), true);
                                    ob_end_clean();
                                    if (is_array($jsonCartContent)) {
                                        $hasPackUsingDifferentVAT = false;
                                        foreach ($jsonCartContent['products'] as &$cartProduct) {
                                            if (!empty($cartProduct['idCombination']) && AdvancedPack::isValidPack($cartProduct['id']) && !AdvancedPack::getPackIdTaxRulesGroup($cartProduct['id'])) {
                                                $hasPackUsingDifferentVAT = true;
                                                break;
                                            }
                                        }
                                        foreach ($jsonCartContent['products'] as &$cartProduct) {
                                            if (!empty($cartProduct['idCombination']) && AdvancedPack::isValidPack($cartProduct['id'])) {
                                                $cartProduct['attributes'] = $moduleInstance->displayPackContent($cartProduct['id'], $cartProduct['idCombination'], pm_advancedpack::PACK_CONTENT_BLOCK_CART);
                                                if ($hasPackUsingDifferentVAT && (int)Group::getCurrent()->price_display_method) {
                                                    $cartProduct['price_float'] = $cartProduct['quantity'] * AdvancedPack::getPackPrice((int)$cartProduct['id'], false, true, true, 6, AdvancedPack::getIdProductAttributeListByIdPack((int)$cartProduct['id'], (int)$cartProduct['idCombination']), array(), array(), true);
                                                    $cartProduct['price'] = Tools::displayPrice($cartProduct['price_float'], $context->currency);
                                                    $cartProduct['priceByLine'] = Tools::displayPrice($cartProduct['price_float'], $context->currency);
                                                }
                                                if (!$fromProductController && $cartProduct['idCombination'] == $idProductAttribute) {
                                                    $cartProduct['idCombination'] = Product::getDefaultAttribute((int)$cartProduct['id']);
                                                }
                                            }
                                        }
                                        if ($hasPackUsingDifferentVAT && (int)Group::getCurrent()->price_display_method) {
                                            $newCartSummary = $context->cart->getSummaryDetails(null, true);
                                            if (is_array($newCartSummary)) {
                                                $summaryTotal = 0;
                                                foreach ($newCartSummary['products'] as &$cartProduct) {
                                                    if (!empty($cartProduct['id_product_attribute']) && AdvancedPack::isValidPack($cartProduct['id_product'])) {
                                                        $newProductSummaryTotal = (int)$cartProduct['cart_quantity'] * AdvancedPack::getPackPrice((int)$cartProduct['id_product'], false, true, true, 6, AdvancedPack::getIdProductAttributeListByIdPack((int)$cartProduct['id_product'], (int)$cartProduct['id_product_attribute']), array(), array(), true);
                                                        $summaryTotal += ($cartProduct['total'] - $newProductSummaryTotal);
                                                        $cartProduct['price_without_quantity_discount'] = AdvancedPack::getPackPrice((int)$cartProduct['id_product'], false, false, true, 6, AdvancedPack::getIdProductAttributeListByIdPack((int)$cartProduct['id_product'], (int)$cartProduct['id_product_attribute']), array(), array(), true);
                                                        $cartProduct['price_wt'] = AdvancedPack::getPackPrice((int)$cartProduct['id_product'], false, true, true, 6, AdvancedPack::getIdProductAttributeListByIdPack((int)$cartProduct['id_product'], (int)$cartProduct['id_product_attribute']), array(), array(), true);
                                                    }
                                                }
                                                $jsonCartContent['productTotal'] = Tools::displayPrice($newCartSummary['total_products'] - $summaryTotal, $context->currency);
                                                $jsonCartContent['total'] = Tools::displayPrice($context->cart->getOrderTotal(false) - $summaryTotal, $context->currency);
                                            }
                                        }
                                        $jsonCartContent['ap5Data'] = array('idProductAttribute' => $idProductAttribute);
                                        if (Configuration::get('PS_BLOCK_CART_AJAX') == 0) {
                                            if (Configuration::get('PS_CART_REDIRECT') == 0) {
                                                $jsonCartContent['ap5RedirectURL'] = self::getContext()->link->getProductLink($idPack);
                                            } else {
                                                $jsonCartContent['ap5RedirectURL'] = self::getContext()->link->getPageLink('cart');
                                            }
                                        }
                                        die(Tools::jsonEncode($jsonCartContent));
                                    } else {
                                        $cartController->displayAjax();
                                    }
                                }
                            } else {
                                $errors[] = Tools::displayError($moduleInstance->getFrontTranslation('errorSavePackContent'), false);
                            }
                        }
                    } else {
                        $errors[] = Tools::displayError($moduleInstance->getFrontTranslation('errorGeneratingPrice'), false);
                    }
                }
            } else {
                $errors[] = Tools::displayError($moduleInstance->getFrontTranslation('errorOutOfStock'), false);
            }
        } else {
            $errors[] = Tools::displayError($moduleInstance->getFrontTranslation('errorInvalidPack'), false);
        }
        if (count($errors)) {
            if ($fromCartController) {
                die(Tools::jsonEncode(array('hasError' => true, 'errors' => $errors)));
            } else {
                $context->controller->errors = $errors;
            }
        }
    }
    public static function addExplodedPackToCart($idPack, $quantity = 1, $idProductAttributeList = array(), $customizationList = array(), $quantityList = array(), $packExcludeList = array())
    {
        $errors = array();
        $moduleInstance = AdvancedPack::getModuleInstance();
        if (self::isValidPack($idPack, true, $packExcludeList)) {
            self::checkCustomizationErrors($idPack, $customizationList, $moduleInstance, true, $packExcludeList);
            $resPackAdd = true;
            $packProducts = self::getPackContent($idPack, null, true, array(), $quantityList);
            $totalPackPrice = 0;
            if (AdvancedPackCoreClass::_isFilledArray($packProducts)) {
                $context = Context::getContext();
                $useTax = (Product::getTaxCalculationMethod($context->customer->id) != PS_TAX_EXC);
                $idAddressDelivery = (int)Tools::getValue('id_address_delivery');
                pm_advancedpack::$_preventInfiniteLoop = true;
                foreach ($packProducts as $k => &$packProduct) {
                    if (in_array((int)$packProduct['id_product_pack'], $packExcludeList)) {
                        unset($packProducts[$k]);
                        continue;
                    }
                    $productPackIdAttribute = (isset($idProductAttributeList[(int)$packProduct['id_product_pack']]) ? $idProductAttributeList[(int)$packProduct['id_product_pack']] : (int)$packProduct['default_id_product_attribute']);
                    $packProduct['id_product_attribute'] = $productPackIdAttribute;
                    if (isset($customizationList[(int)$packProduct['id_product_pack']])) {
                        foreach ($customizationList[(int)$packProduct['id_product_pack']] as $idCustomizationField => $customizationValue) {
                            if (!Tools::strlen($customizationValue)) {
                                continue;
                            }
                            self::getContext()->cart->_addCustomization((int)$packProduct['id_product'], $productPackIdAttribute, $idCustomizationField, Product::CUSTOMIZE_TEXTFIELD, $customizationValue, (int)$packProduct['quantity'] * $quantity);
                            Db::getInstance()->execute('
                                UPDATE `'._DB_PREFIX_.'customization`
                                SET `in_cart`=1
                                WHERE `id_cart`=' . (int)self::getContext()->cart->id . '
                                AND `id_product`=' . (int)(int)$packProduct['id_product'] . '
                                AND `id_product_attribute`=' . (int)$productPackIdAttribute . '
                                AND `quantity`=' . (int)$packProduct['quantity'] * $quantity);
                        }
                    }
                    $resPackAdd &= self::getContext()->cart->updateQty((int)$packProduct['quantity'] * $quantity, (int)$packProduct['id_product'], $productPackIdAttribute, null, 'up', $idAddressDelivery);
                    $totalPackPrice += $packProduct['productObj']->getPrice($useTax, $productPackIdAttribute);
                }
                pm_advancedpack::$_preventInfiniteLoop = false;
                ob_start();
                $cartController = new CartController();
                $cartController->displayAjax();
                $jsonCartContent = (array)Tools::jsonDecode(ob_get_contents(), true);
                ob_end_clean();
                if (is_array($jsonCartContent)) {
                    $packCover = Product::getCover($idPack);
                    $packProductObject = new Product($idPack, false, $context->language->id);
                    if (Validate::isLoadedObject($packProductObject)) {
                        $jsonCartContent['fakeAp5Product'] = array(
                            'name' => $packProductObject->name,
                            'price' => Tools::displayPrice($totalPackPrice),
                            'quantity' => (int)$quantity,
                            'image' => (!empty($packCover['id_image']) ? $context->link->getImageLink($packProductObject->link_rewrite, $packCover['id_image'], implode('_', array('home', 'default'))) : ''),
                            'hasAttributes' => true,
                            'attributes' => $moduleInstance->displayPackContent($idPack, false, pm_advancedpack::PACK_CONTENT_BLOCK_CART, $packProducts),
                        );
                        die(Tools::jsonEncode($jsonCartContent));
                    }
                }
            }
            $cartController = new CartController();
            $cartController->displayAjax();
        } else {
            $errors[] = Tools::displayError($moduleInstance->getFrontTranslation('errorInvalidPack'), false);
        }
        if (count($errors)) {
            die(Tools::jsonEncode(array('hasError' => true, 'errors' => $errors)));
        }
    }
    public static function getAddressInstance()
    {
        $address_infos = array();
        $id_country = (int)self::getContext()->country->id;
        $id_state = 0;
        $zipcode = 0;
        $id_address = 0;
        if (Validate::isLoadedObject(self::getContext()->cart)) {
            $id_address = self::getContext()->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
        }
        if (!$id_address && Validate::isLoadedObject(self::getContext()->customer)) {
            $id_address = (int)Address::getFirstCustomerAddressId(self::getContext()->customer->id);
        }
        if ($id_address) {
            $address_infos = Address::getCountryAndState($id_address);
            if ($address_infos['id_country']) {
                $id_country = (int)$address_infos['id_country'];
                $id_state = (int)$address_infos['id_state'];
                $zipcode = $address_infos['postcode'];
            }
        } elseif (isset(self::getContext()->customer->geoloc_id_country)) {
            $id_country = (int)self::getContext()->customer->geoloc_id_country;
            $id_state = (int)self::getContext()->customer->id_state;
            $zipcode = (int)self::getContext()->customer->postcode;
        }
        $useTax = true;
        if (AdvancedPack::excludeTaxeOption()) {
            $useTax = false;
        }
        if ($useTax != false
            && !empty($address_infos['vat_number'])
            && $address_infos['id_country'] != Configuration::get('VATNUMBER_COUNTRY')
            && Configuration::get('VATNUMBER_MANAGEMENT')) {
            $useTax = false;
        }
        $address = new Address();
        if (!empty($id_address)) {
            $address = new Address((int)$id_address);
        }
        if (!Validate::isLoadedObject($address)) {
            $address = new Address();
            $address->id_country = $id_country;
            $address->id_state = $id_state;
            $address->postcode = $zipcode;
        }
        $useTax = true;
        if (AdvancedPack::excludeTaxeOption()) {
            $useTax = false;
        }
        if ($useTax != false
            && !empty($address_infos['vat_number'])
            && $address_infos['id_country'] != Configuration::get('VATNUMBER_COUNTRY')
            && Configuration::get('VATNUMBER_MANAGEMENT')) {
            $useTax = false;
        }
        return array($address, $useTax);
    }
    public static function updateCartSpecificPrice($idCart = null)
    {
        if (empty($idCart)) {
            $idCart = Context::getContext()->cart->id;
        }
        if (empty($idCart)) {
            return;
        }
        $sql = new DbQuery();
        $sql->select('DISTINCT `id_pack`, `id_product_attribute_pack`');
        $sql->from('pm_advancedpack_cart_products', 'acp');
        $sql->where('acp.`id_cart`='.(int)$idCart);
        $sql->where('acp.`id_order` IS NULL');
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if ($result !== false && AdvancedPackCoreClass::_isFilledArray($result)) {
            foreach ($result as $resultRow) {
                if (!isset($resultRow['id_product_attribute_pack']) || (int)$resultRow['id_product_attribute_pack'] <= 0) {
                    continue;
                }
                $idProductAttribute = (int)$resultRow['id_product_attribute_pack'];
                $idPack = (int)$resultRow['id_pack'];
                $idProductAttributeList = self::getIdProductAttributeListByIdPack((int)$idPack, $idProductAttribute);
                self::addPackSpecificPrice((int)$idPack, $idProductAttribute, $idProductAttributeList);
            }
        }
    }
    public static function addPackSpecificPrice($idPack, $idProductAttribute, &$idProductAttributeList = array())
    {
        $config = pm_advancedpack::getModuleConfigurationStatic();
        $packIdTaxRulesGroup = AdvancedPack::getPackIdTaxRulesGroup((int)$idPack);
        $packProducts = self::getPackContent($idPack);
        $packFixedPrice = self::getPackFixedPrice($idPack);
        $packHasFixedPrice = is_array($packFixedPrice) && array_sum($packFixedPrice) > 0;
        $reductionAmountTable = $reductionPercentageTable = array();
        $forceReductionByAmount = false;
        if (!$packHasFixedPrice && AdvancedPackCoreClass::_isFilledArray($packProducts)) {
            foreach ($packProducts as $packProduct) {
                $selectedIdProductAttribute = (isset($idProductAttributeList[$packProduct['id_product_pack']]) ? (int)$idProductAttributeList[$packProduct['id_product_pack']] : null);
                if (empty($selectedIdProductAttribute) && !empty($packProduct['default_id_product_attribute'])) {
                    $selectedIdProductAttribute = (int)$packProduct['default_id_product_attribute'];
                }
                if ($selectedIdProductAttribute != null && isset($packProduct['combinationsInformations']) && isset($packProduct['combinationsInformations'][$selectedIdProductAttribute])) {
                    if ((float)$packProduct['combinationsInformations'][$selectedIdProductAttribute]['reduction_amount'] > 0) {
                        $forceReductionByAmount = true;
                    }
                }
                if ($packProduct['reduction_type'] == 'amount') {
                    $reductionAmountTable[] = $packProduct['reduction_amount'];
                } elseif ($packProduct['reduction_type'] == 'percentage') {
                    $reductionPercentageTable[] = $packProduct['reduction_amount'];
                }
            }
            $reductionPercentageTable = array_unique($reductionPercentageTable);
            if (array_sum($reductionPercentageTable) == 0) {
                $reductionPercentageTable = array();
                $forceReductionByAmount = true;
            }
        }
        $packHasPercentageReduction = (!$forceReductionByAmount && count($reductionPercentageTable) == 1 && !count($reductionAmountTable));
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'specific_price` WHERE `id_product`='.(int)$idPack.' AND `id_product_attribute`='.(int)$idProductAttribute);
        $spToAdd = array();
        $sp = new SpecificPrice();
        $sp->id_product = $idPack;
        $sp->id_cart = 0;
        $sp->id_product_attribute = $idProductAttribute;
        $sp->id_shop = AdvancedPack::getPackIdShop($idPack);
        $sp->id_shop_group = 0;
        $sp->id_currency = ($idProductAttribute && Validate::isLoadedObject(self::getContext()->currency) ? self::getContext()->currency->id : 0);
        $sp->id_country = 0;
        $sp->id_group = 0;
        $sp->id_customer = 0;
        $sp->from = '0000-00-00 00:00:00';
        $sp->to = '0000-00-00 00:00:00';
        $sp->from_quantity = 1;
        $currentCustomer = self::getContext()->customer;
        $currentCustomerIsLogged = (Validate::isLoadedObject($currentCustomer) && $currentCustomer->isLogged());
        $idGroupList = array();
        $idGroupListWithoutTaxes = array();
        if ($currentCustomerIsLogged) {
            $defaultCustomerGroup = (int)Customer::getDefaultGroupId(self::getContext()->customer->id);
            $idGroupList = array($defaultCustomerGroup);
        } else {
            if ($idProductAttribute) {
                $idGroupList = self::getIdGroupListByIdPack($idPack);
                foreach (Group::getGroups(Context::getContext()->language->id, true) as $group) {
                    $groupPriceDisplayMethod = (int)Group::getPriceDisplayMethod((int)$group['id_group']);
                    if ($groupPriceDisplayMethod == 1) {
                        $idGroupListWithoutTaxes[] = (int)$group['id_group'];
                    }
                    if (!$packIdTaxRulesGroup && $groupPriceDisplayMethod == 1) {
                        $idGroupList[] = (int)$group['id_group'];
                    } elseif (!empty($group['reduction']) && $group['reduction'] > 0) {
                        $idGroupList[] = (int)$group['id_group'];
                    }
                }
                $idGroupList = array_unique($idGroupList);
            } else {
                foreach (Group::getGroups(Context::getContext()->language->id, true) as $group) {
                    $groupPriceDisplayMethod = (int)Group::getPriceDisplayMethod((int)$group['id_group']);
                    if ($groupPriceDisplayMethod == 1) {
                        $idGroupListWithoutTaxes[] = (int)$group['id_group'];
                    }
                    $idGroupList[] = (int)$group['id_group'];
                }
                $idGroupList = array_unique($idGroupList);
            }
        }
        if ($idProductAttribute) {
            $idCountryList = self::getIdCountryListByIdPack($idPack);
            $idCurrencyList = self::getIdCurrencyListByIdPack($idPack);
        } else {
            if (!empty($config['postponeUpdatePackSpecificPrice']) && !Context::getContext()->controller instanceof pm_advancedpackcronModuleFrontController) {
                $idCountryList = array(Context::getContext()->country->id);
            } else {
                $idCountryList = self::getIdCountryListByIdPack($idPack, true);
            }
            $idCurrencyList = self::getIdCurrencyListByIdPack($idPack, true);
        }
        $groupReductionList = array();
        $groupReductionList[(int)Configuration::get('PS_UNIDENTIFIED_GROUP')] = Group::getReductionByIdGroup((int)Configuration::get('PS_UNIDENTIFIED_GROUP'));
        foreach ($idGroupList as $idGroupTmp) {
            if ($idGroupTmp) {
                $groupReductionList[(int)$idGroupTmp] = (float)Group::getReductionByIdGroup($idGroupTmp);
                $packCategoryReduction = GroupReduction::getValueForProduct((int)$idPack, $idGroupTmp);
                if (is_float($packCategoryReduction + 0)) {
                    $groupReductionList[(int)$idGroupTmp] = $packCategoryReduction * 100;
                } else {
                }
            }
        }
        $specificPriceCartesian = AdvancedPackCoreClass::array_cartesian(array(
            'id_country' => $idCountryList,
            'id_group' => $idGroupList,
            'id_currency' => $idCurrencyList,
        ));
        $saveResult = true;
        $fieldsList = null;
        $currencyCache = array();
        $countryCache = array();
        $fakeCustomer = new Customer();
        $fakeCustomer->id = self::PACK_FAKE_CUSTOMER_ID;
        $idCountryDefault = Context::getContext()->country->id;
        $oldContext = clone(self::getContext());
        self::$forceUseOfAnotherContext = true;
        $spByCountry = clone($sp);
        $psUnidentifiedGroup = (int)Configuration::get('PS_UNIDENTIFIED_GROUP');
        $idDefaultCurrency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
        foreach ($specificPriceCartesian as $specificPriceCartesianRow) {
            $idCountry = $specificPriceCartesianRow[0];
            $idGroup = $specificPriceCartesianRow[1];
            $idCurrency = $specificPriceCartesianRow[2];
            $spByCountry->id = $spByCountry->id_specific_price = null;
            $newContext = $oldContext->cloneContext();
            if ($idCountry) {
                if (isset($countryCache[$idCountry])) {
                    $newContext->country = $countryCache[$idCountry];
                } else {
                    $newContext->country = new Country($idCountry, (int)$newContext->cookie->id_lang);
                    $countryCache[$idCountry] = $newContext->country;
                }
            } elseif ($idCountryDefault) {
                if (isset($countryCache[$idCountryDefault])) {
                    $newContext->country = $countryCache[$idCountryDefault];
                } else {
                    $newContext->country = new Country($idCountryDefault, (int)$newContext->cookie->id_lang);
                    $countryCache[$idCountryDefault] = $newContext->country;
                }
            }
            if (!Validate::isLoadedObject($newContext->customer)) {
                $newContext->customer = $fakeCustomer;
            }
            if (!empty($idGroup)) {
                $newContext->customer->id_default_group = $idGroup;
            } else {
                $newContext->customer->id_default_group = $psUnidentifiedGroup;
            }
            if ($idCurrency) {
                if (isset($currencyCache[$idCurrency])) {
                    $newContext->currency = $currencyCache[$idCurrency];
                } else {
                    $newContext->currency = new Currency($idCurrency);
                    $currencyCache[$idCurrency] = $newContext->currency;
                }
            } else {
                if (isset($currencyCache[$idDefaultCurrency])) {
                    $newContext->currency = $currencyCache[$idDefaultCurrency];
                } else {
                    $newContext->currency = new Currency($idDefaultCurrency);
                    $currencyCache[$idDefaultCurrency] = $newContext->currency;
                }
            }
            self::setContext($newContext);
            $spByCountry->id_group = $idGroup;
            $spByCountry->id_country = $idCountry;
            $spByCountry->id_currency = $idCurrency;
            $currentGlobalGroupDiscount = (!empty($groupReductionList[(int)$idGroup]) ? $groupReductionList[(int)$idGroup] : 0);
            if ($idGroup && $currentGlobalGroupDiscount > 0) {
                if ($packIdTaxRulesGroup) {
                    $spByCountry->price = self::getPackPrice($idPack, false, false, false, 6, $idProductAttributeList, array(), array(), true) / (1 - $currentGlobalGroupDiscount/100);
                    $groupPriceWt = self::getPackPrice($idPack, true, false, false, 6, $idProductAttributeList, array(), array(), true);
                    $realPriceWt = self::getPackPrice($idPack, true, true, false, 6, $idProductAttributeList, array(), array(), true);
                } else {
                    if (($packHasFixedPrice || $packHasPercentageReduction) && in_array($idGroup, $idGroupListWithoutTaxes)) {
                        $spByCountry->price = self::getPackPrice($idPack, false, false, false, 6, $idProductAttributeList, array(), array(), true) / (1 - $currentGlobalGroupDiscount/100);
                        $groupPriceWt = self::getPackPrice($idPack, false, false, false, 6, $idProductAttributeList, array(), array(), true);
                        $realPriceWt = self::getPackPrice($idPack, false, true, false, 6, $idProductAttributeList, array(), array(), true);
                    } else {
                        $spByCountry->price = self::getPackPrice($idPack, true, false, false, 6, $idProductAttributeList, array(), array(), true) / (1 - $currentGlobalGroupDiscount/100);
                        $groupPriceWt = self::getPackPrice($idPack, true, false, false, 6, $idProductAttributeList, array(), array(), true);
                        $realPriceWt = self::getPackPrice($idPack, true, true, false, 6, $idProductAttributeList, array(), array(), true);
                    }
                }
                if ($packHasPercentageReduction) {
                    $spByCountry->reduction = current($reductionPercentageTable);
                    $spByCountry->reduction_type = 'percentage';
                } else {
                    if ($realPriceWt > $groupPriceWt) {
                        $spByCountry->price = self::getPackPrice($idPack, false, ($packIdTaxRulesGroup > 0), false, 6, $idProductAttributeList) / (1 - $currentGlobalGroupDiscount/100);
                        $spByCountry->reduction = 0;
                    } else {
                        $spByCountry->reduction = ($groupPriceWt - $realPriceWt) / (1 - $currentGlobalGroupDiscount/100);
                    }
                    $spByCountry->reduction_type = 'amount';
                }
            } else {
                if (!($idGroup && !$packIdTaxRulesGroup && (int)Group::getPriceDisplayMethod($idGroup) == 1)) {
                    if (!$idGroup && !$idCountry && !$idCurrency && !$idProductAttribute) {
                        $spByCountry->price = -1;
                    } else {
                        if ($packIdTaxRulesGroup) {
                            $spByCountry->price = self::getPackPrice($idPack, false, false, false, 6, $idProductAttributeList);
                        } else {
                            $spByCountry->price = self::getPackPrice($idPack, true, false, false, 6, $idProductAttributeList);
                        }
                    }
                    if ($packHasFixedPrice) {
                        $spByCountry->reduction = self::getPackPrice($idPack, true, false, true, 6, $idProductAttributeList) - self::getPackPrice($idPack, true, true, true, 6, $idProductAttributeList);
                        $spByCountry->reduction_type = 'amount';
                    } elseif ($packHasPercentageReduction) {
                        $spByCountry->reduction = current($reductionPercentageTable);
                        $spByCountry->reduction_type = 'percentage';
                    } else {
                        $spByCountry->reduction_type = 'amount';
                        $spByCountry->reduction = self::getPackPrice($idPack, true, false, true, 6, $idProductAttributeList) - self::getPackPrice($idPack, true, true, true, 6, $idProductAttributeList);
                    }
                    if ($spByCountry->reduction_type == 'amount' && $currentCustomerIsLogged && $idProductAttribute) {
                        list(, $useTax) = self::getAddressInstance();
                        if (!$useTax) {
                            if (property_exists('SpecificPrice', 'reduction_tax') && isset($spByCountry->reduction_tax)) {
                                $spByCountry->reduction_tax = 0;
                            } else {
                                if ($packIdTaxRulesGroup) {
                                    $spByCountry->price = self::getPackPrice($idPack, false, true, false, 6, $idProductAttributeList);
                                } else {
                                    $spByCountry->price = self::getPackPrice($idPack, true, true, false, 6, $idProductAttributeList);
                                }
                                $spByCountry->reduction = 0;
                            }
                        }
                    }
                    if ($spByCountry->reduction < 0) {
                        $spByCountry->reduction = 0;
                        if ($packIdTaxRulesGroup) {
                            $spByCountry->price = self::getPackPrice($idPack, false, true, false, 6, $idProductAttributeList);
                        } else {
                            $spByCountry->price = self::getPackPrice($idPack, true, true, false, 6, $idProductAttributeList);
                        }
                    }
                } elseif ($idGroup && !$idProductAttribute && !$packIdTaxRulesGroup && (int)Group::getPriceDisplayMethod($idGroup) == 1) {
                    if ($packHasFixedPrice) {
                        $spByCountry->price = self::getPackPrice($idPack, false, false, false, 6, $idProductAttributeList);
                    } elseif (AdvancedPackCoreClass::_isFilledArray($idProductAttributeList)) {
                        $spByCountry->price = self::getPackPrice($idPack, false, false, false, 6, $idProductAttributeList);
                    } else {
                        $spByCountry->price = self::getPackPrice($idPack, false, false, false, 6, $idProductAttributeList);
                    }
                    if ($packHasFixedPrice) {
                        if ($packIdTaxRulesGroup) {
                            $spByCountry->reduction = self::getPackPrice($idPack, true, false, true, 6, $idProductAttributeList) - self::getPackPrice($idPack, true, true, true, 6, $idProductAttributeList);
                        } else {
                            $spByCountry->reduction = self::getPackPrice($idPack, false, false, true, 6, $idProductAttributeList) - self::getPackPrice($idPack, false, true, true, 6, $idProductAttributeList);
                        }
                        $spByCountry->reduction_type = 'amount';
                    } elseif ($packHasPercentageReduction) {
                        $spByCountry->reduction = current($reductionPercentageTable);
                        $spByCountry->reduction_type = 'percentage';
                    } else {
                        $spByCountry->reduction_type = 'amount';
                        $spByCountry->reduction = self::getPackPrice($idPack, false, false, true, 6, $idProductAttributeList) - self::getPackPrice($idPack, false, true, true, 6, $idProductAttributeList);
                    }
                    if ($spByCountry->reduction < 0) {
                        $spByCountry->reduction = 0;
                        $spByCountry->price = self::getPackPrice($idPack, false, true, false, 6, $idProductAttributeList);
                    }
                } elseif ($idGroup && $idProductAttribute && !$packIdTaxRulesGroup && (int)Group::getPriceDisplayMethod($idGroup) == 1) {
                    if ($packIdTaxRulesGroup) {
                        $spByCountry->price = self::getPackPrice($idPack, false, false, false, 6, $idProductAttributeList);
                    } else {
                        $spByCountry->price = self::getPackPrice($idPack, true, false, false, 6, $idProductAttributeList);
                    }
                    if ($packHasFixedPrice) {
                        $spByCountry->reduction = self::getPackPrice($idPack, true, false, true, 6, $idProductAttributeList) - self::getPackPrice($idPack, true, true, true, 6, $idProductAttributeList);
                        $spByCountry->reduction_type = 'amount';
                    } elseif ($packHasPercentageReduction) {
                        $spByCountry->reduction = current($reductionPercentageTable);
                        $spByCountry->reduction_type = 'percentage';
                    } else {
                        $spByCountry->reduction_type = 'amount';
                        $spByCountry->reduction = self::getPackPrice($idPack, true, false, true, 6, $idProductAttributeList) - self::getPackPrice($idPack, true, true, true, 6, $idProductAttributeList);
                    }
                    if ($spByCountry->reduction < 0) {
                        $spByCountry->reduction = 0;
                        if ($packIdTaxRulesGroup) {
                            $spByCountry->price = self::getPackPrice($idPack, false, true, false, 6, $idProductAttributeList);
                        } else {
                            $spByCountry->price = self::getPackPrice($idPack, true, true, false, 6, $idProductAttributeList);
                        }
                    }
                }
            }
            $spByCountry->price = Tools::ps_round($spByCountry->price, 6);
            $spByCountry->reduction = Tools::ps_round($spByCountry->reduction, 6);
            if ($fieldsList === null) {
                $fieldsList = array_keys($spByCountry->getFields());
                $fieldsList = array_keys(array_intersect_key(get_object_vars($spByCountry), array_flip($fieldsList)));
            }
            $spToAdd[] = '("' . implode('", "', array_map('pSQL', array_intersect_key(get_object_vars($spByCountry), array_flip($fieldsList)))) . '")';
        }
        self::setContext($oldContext);
        self::$forceUseOfAnotherContext = false;
        if (sizeof($spToAdd)) {
            $columnList = '`' . implode('`, `', $fieldsList) . '`';
            foreach (array_chunk($spToAdd, 1000) as $spChunckToAdd) {
                $saveResult &= Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'specific_price` (' . $columnList . ') VALUES ' . implode(',', $spChunckToAdd));
            }
        }
        $idSpecificPrice = (int)Db::getInstance()->getValue('SELECT `id_specific_price` FROM `'._DB_PREFIX_.'specific_price` WHERE `id_product`=' . (int)$idPack);
        if (!empty($idSpecificPrice)) {
            $spCacheReset = new SpecificPrice($idSpecificPrice);
            $spCacheReset->update();
        }
        return $saveResult;
    }
    public static function transformProductDescriptionWithImg($product)
    {
        $reg = '/\[img\-([0-9]+)\-(left|right)\-([a-zA-Z0-9-_]+)\]/';
        while (preg_match($reg, $product->description, $matches)) {
            $link_lmg = self::getContext()->link->getImageLink($product->link_rewrite, $product->id.'-'.$matches[1], $matches[3]);
            $class = $matches[2] == 'left' ? 'class="imageFloatLeft"' : 'class="imageFloatRight"';
            $html_img = '<img src="'.$link_lmg.'" alt="" '.$class.'/>';
            $product->description = str_replace($matches[0], $html_img, $product->description);
        }
        return $product->description;
    }
    private static function _getProductImages($packProduct, $idLang = null)
    {
        if ($idLang == null) {
            $idLang = self::getContext()->language->id;
        }
        $cacheId = self::getPMCacheId(__METHOD__.(int)$packProduct['id_product_pack'].(int)$idLang.self::getContext()->shop->id);
        if (!self::isInCache($cacheId)) {
            $productAttributesList = self::getProductAttributeWhiteList($packProduct['id_product_pack']);
            if (!pm_advancedpack::_isFilledArray($productAttributesList)) {
                $productObj = new Product((int)$packProduct['id_product'], false, (int)$idLang);
                $images = $productObj->getImages($idLang);
            } else {
                $sql = 'SELECT i.`id_image`, il.`legend`, ai.`id_product_attribute`
                        FROM `'._DB_PREFIX_.'image` i
                        '.Shop::addSqlAssociation('image', 'i').'
                        LEFT JOIN `'._DB_PREFIX_.'product_attribute_image` ai ON (i.`id_image` = ai.`id_image`)
                        LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$idLang.')
                        WHERE i.`id_product` = '.(int)$packProduct['id_product'].'
                        AND (ai.`id_product_attribute` IN ('. implode(',', $productAttributesList) .') OR ai.`id_product_attribute` IS NULL)
                        GROUP BY i.`id_image`
                        ORDER BY `position`';
                $images = Db::getInstance()->executeS($sql);
                if (pm_advancedpack::_isFilledArray($images)) {
                    foreach ($images as $k => $image) {
                        if ((int)$image['id_product_attribute'] && !in_array((int)$image['id_product_attribute'], $productAttributesList)) {
                            unset($images[$k]);
                        }
                    }
                } else {
                    $images = array();
                }
            }
            self::storeInCache($cacheId, $images);
        } else {
            return self::getFromCache($cacheId);
        }
        return $images;
    }
    private static function _getProductCoverImage($idProduct, $idProductAttribute = null, $idLang = null)
    {
        if ($idLang == null) {
            $idLang = self::getContext()->language->id;
        }
        $cacheId = self::getPMCacheId(__METHOD__.(int)$idProduct.(int)$idProductAttribute.(int)$idLang.self::getContext()->shop->id);
        if (!self::isInCache($cacheId)) {
            $sql = new DbQuery();
            $sql->select('i.`id_image`, il.`legend`');
            $sql->from('image', 'i');
            $sql->join(Shop::addSqlAssociation('image', 'i'));
            $sql->leftJoin('image_lang', 'il', 'i.`id_image` = il.`id_image`');
            if ($idProductAttribute != null && $idProductAttribute) {
                $sql->leftJoin('product_attribute_image', 'pai', 'i.`id_image` = pai.`id_image`');
                $sql->where('i.`id_product`='.(int)$idProduct);
                $sql->where('il.`id_lang`='.(int)$idLang);
                $sql->where('pai.`id_product_attribute`='.(int)$idProductAttribute);
            } else {
                $sql->where('i.`id_product`='.(int)$idProduct);
                $sql->where('il.`id_lang`='.(int)$idLang);
            }
            $sql->orderBy('i.`position` ASC');
            $productImage = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
            if (AdvancedPackCoreClass::_isFilledArray($productImage)) {
                self::storeInCache($cacheId, $productImage);
                return $productImage;
            } else {
                $sql = new DbQuery();
                $sql->select('i.`id_image`, il.`legend`');
                $sql->from('image', 'i');
                $sql->join(Shop::addSqlAssociation('image', 'i'));
                $sql->leftJoin('image_lang', 'il', 'i.`id_image` = il.`id_image`');
                $sql->where('i.`id_product`='.(int)$idProduct);
                $sql->where('il.`id_lang`='.(int)$idLang);
                $sql->where('image_shop.`cover`=1');
                $productImage = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
                if (AdvancedPackCoreClass::_isFilledArray($productImage)) {
                    self::storeInCache($cacheId, $productImage);
                    return $productImage;
                } else {
                    return false;
                }
            }
        } else {
            return self::getFromCache($cacheId);
        }
        self::storeInCache($cacheId, false);
        return false;
    }
    private static function _getProductAttributesGroups($productObj, $idProductAttributeDefault = null, $idProductAttributeWhiteList = array(), $idLang = null)
    {
        if ($idLang == null) {
            $idLang = self::getContext()->language->id;
        }
        $colors = $groups = $combinations = $combination_prices_set = array();
        $attributes_groups = $productObj->getAttributesGroups($idLang);
        if (is_array($attributes_groups) && $attributes_groups) {
            $combinationImages = $productObj->getCombinationImages($idLang);
            $combination_specific_price = null;
            $atLeastOneDefaultAttribute = array();
            $alternativeDefaultIdAttributeGroup = array();
            foreach ($attributes_groups as $k => $row) {
                if (count($idProductAttributeWhiteList) && !in_array((int)$row['id_product_attribute'], $idProductAttributeWhiteList)) {
                    unset($attributes_groups[$k]);
                    continue;
                }
                if ($idProductAttributeDefault != null && (int)$idProductAttributeDefault == (int)$row['id_product_attribute']) {
                    $attributes_groups[$k]['default_on'] = 1;
                } else {
                    $attributes_groups[$k]['default_on'] = 0;
                }
                if (!isset($alternativeDefaultIdAttributeGroup[$row['id_attribute_group']])) {
                    $alternativeDefaultIdAttributeGroup[$row['id_attribute_group']] = array('id_attribute_group' => $row['id_attribute_group'], 'id_attribute' => $row['id_attribute']);
                }
            }
            foreach ($attributes_groups as $k => $row) {
                if (isset($row['is_color_group']) && $row['is_color_group'] && (isset($row['attribute_color']) && $row['attribute_color']) || (version_compare(_PS_VERSION_, '1.6.0.0', '>=') && Tools::file_exists_cache(_PS_COL_IMG_DIR_.$row['id_attribute'].'.jpg'))) {
                    $colors[$row['id_attribute']]['value'] = $row['attribute_color'];
                    $colors[$row['id_attribute']]['name'] = $row['attribute_name'];
                    if (!isset($colors[$row['id_attribute']]['attributes_quantity'])) {
                        $colors[$row['id_attribute']]['attributes_quantity'] = 0;
                    }
                    $colors[$row['id_attribute']]['attributes_quantity'] += (int)$row['quantity'];
                }
                if (!isset($groups[$row['id_attribute_group']])) {
                    $groups[$row['id_attribute_group']] = array(
                        'group_name' => $row['group_name'],
                        'name' => $row['public_group_name'],
                        'group_type' => $row['group_type'],
                        'default' => -1,
                    );
                }
                $groups[$row['id_attribute_group']]['attributes'][$row['id_attribute']] = $row['attribute_name'];
                if ($row['default_on'] && $groups[$row['id_attribute_group']]['default'] == -1) {
                    $groups[$row['id_attribute_group']]['default'] = (int)$row['id_attribute'];
                    $atLeastOneDefaultAttribute[$row['id_attribute_group']] = true;
                }
                if (!isset($groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']])) {
                    $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] = 0;
                }
                $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] += (int)$row['quantity'];
                $combinations[$row['id_product_attribute']]['attributes_values'][$row['id_attribute_group']] = $row['attribute_name'];
                $combinations[$row['id_product_attribute']]['attributes'][] = (int)$row['id_attribute'];
                $combinations[$row['id_product_attribute']]['price'] = (float)$row['price'];
                if (!isset($combination_prices_set[(int)$row['id_product_attribute']])) {
                    Product::getPriceStatic((int)$productObj->id, false, $row['id_product_attribute'], 6, null, false, true, 1, false, null, null, null, $combination_specific_price);
                    $combination_prices_set[(int)$row['id_product_attribute']] = true;
                    $combinations[$row['id_product_attribute']]['specific_price'] = $combination_specific_price;
                }
                $combinations[$row['id_product_attribute']]['ecotax'] = (float)$row['ecotax'];
                $combinations[$row['id_product_attribute']]['weight'] = (float)$row['weight'];
                $combinations[$row['id_product_attribute']]['quantity'] = (int)$row['quantity'];
                $combinations[$row['id_product_attribute']]['reference'] = $row['reference'];
                $combinations[$row['id_product_attribute']]['unit_impact'] = $row['unit_price_impact'];
                $combinations[$row['id_product_attribute']]['minimal_quantity'] = $row['minimal_quantity'];
                if ($row['available_date'] != '0000-00-00') {
                    $combinations[$row['id_product_attribute']]['available_date'] = $row['available_date'];
                    if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
                        $combinations[$row['id_product_attribute']]['date_formatted'] = Tools::displayDate($row['available_date']);
                    }
                } else {
                    $combinations[$row['id_product_attribute']]['available_date'] = '';
                }
                $combinations[$row['id_product_attribute']]['id_image'] = (isset($combinationImages[$row['id_product_attribute']][0]['id_image']) ? (int)$combinationImages[$row['id_product_attribute']][0]['id_image'] : 1);
            }
            if (!Product::isAvailableWhenOutOfStock($productObj->out_of_stock) && Configuration::get('PS_DISP_UNAVAILABLE_ATTR') == 0) {
                foreach ($groups as &$group) {
                    foreach ($group['attributes_quantity'] as $key => &$quantity) {
                        if ($quantity <= 0) {
                            unset($group['attributes'][$key]);
                        }
                    }
                }
                foreach ($colors as $key => $color) {
                    if ($color['attributes_quantity'] <= 0) {
                        unset($colors[$key]);
                    }
                }
            }
            foreach ($combinations as $id_product_attribute => $comb) {
                $attribute_list = '';
                foreach ($comb['attributes'] as $id_attribute) {
                    $attribute_list .= '\''.(int)$id_attribute.'\',';
                }
                $attribute_list = rtrim($attribute_list, ',');
                $combinations[$id_product_attribute]['list'] = $attribute_list;
            }
            foreach ($groups as $id_attribute_group => &$group) {
                if (!isset($atLeastOneDefaultAttribute[$id_attribute_group])) {
                    $groups[$id_attribute_group]['default'] = (int)$alternativeDefaultIdAttributeGroup[$id_attribute_group]['id_attribute'];
                }
            }
            return array(
                'groups' => $groups,
                'colors' => (count($colors)) ? $colors : false,
                'combinations' => $combinations,
                'combinationImages' => $combinationImages
            );
        }
        return false;
    }
    public function updatePackContent($packContent, $packSettings, $isNewPack = false, $isMajorUpdate = false)
    {
        $res = true;
        if ($isNewPack) {
            $res &= Db::getInstance()->insert('pm_advancedpack', array('id_pack' => $this->id, 'id_shop' => (int)self::getContext()->shop->id, 'fixed_price' => json_encode($packSettings['fixedPrice']), 'allow_remove_product' => (int)$packSettings['allowRemoveProduct']), true);
        }
        if (!$isNewPack) {
            $sql = new DbQuery();
            $sql->select('`id_cart`, `id_pack`, `id_product_attribute_pack`');
            $sql->from('pm_advancedpack_cart_products', 'acp');
            $sql->leftJoin('product_attribute', 'ipa', 'acp.`id_product_attribute` = ipa.`id_product_attribute`');
            $sql->where('acp.`id_order` IS NULL');
            $sql->where('acp.`id_product_attribute` != 0');
            $sql->where('ipa.`id_product_attribute` IS NULL');
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
            if (AdvancedPackCoreClass::_isFilledArray($result)) {
                foreach ($result as $packToRemoveFromCart) {
                    $res &= Db::getInstance()->delete('cart_product', '`id_cart`='. (int)$packToRemoveFromCart['id_cart'] . ' AND `id_product`='. (int)$packToRemoveFromCart['id_pack'] . ' AND `id_product_attribute`='. (int)$packToRemoveFromCart['id_product_attribute_pack']);
                    $res &= Db::getInstance()->delete('pm_advancedpack_cart_products', '`id_pack`='. (int)$packToRemoveFromCart['id_pack'] . ' AND `id_product_attribute_pack`='. (int)$packToRemoveFromCart['id_product_attribute_pack']);
                }
            }
        }
        if ($isMajorUpdate) {
            $sql = new DbQuery();
            $sql->select('GROUP_CONCAT(DISTINCT `id_product_attribute_pack`)');
            $sql->from('pm_advancedpack_cart_products', 'acp');
            $sql->where('acp.`id_pack`='.(int)$this->id);
            $sql->where('acp.`id_order` IS NULL');
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            if ($result !== false && !empty($result)) {
                $result = array_map('intval', explode(',', $result));
                if (AdvancedPackCoreClass::_isFilledArray($result)) {
                    foreach ($result as $idProductAttribute) {
                        if ((int)$idProductAttribute > 0) {
                            self::setStockAvailableQuantity((int)$this->id, (int)$idProductAttribute, 0, false);
                        }
                    }
                }
            }
        } elseif (!$isMajorUpdate && !$isNewPack) {
            $sql = new DbQuery();
            $sql->select('GROUP_CONCAT(DISTINCT `id_product_attribute_pack`)');
            $sql->from('pm_advancedpack_cart_products', 'acp');
            $sql->where('acp.`id_pack`='.(int)$this->id);
            $sql->where('acp.`id_order` IS NULL');
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            if ($result !== false && !empty($result)) {
                $result = array_map('intval', explode(',', $result));
                if (AdvancedPackCoreClass::_isFilledArray($result)) {
                    foreach ($result as $idProductAttribute) {
                        if ((int)$idProductAttribute > 0) {
                            $idProductAttributeList = self::getIdProductAttributeListByIdPack((int)$this->id, $idProductAttribute);
                            self::addPackSpecificPrice((int)$this->id, $idProductAttribute, $idProductAttributeList);
                            if (self::isValidPack((int)$this->id, true)) {
                                self::setStockAvailableQuantity((int)$this->id, (int)$idProductAttribute, self::getPackAvailableQuantity((int)$this->id, $idProductAttributeList), false);
                            } else {
                                self::setStockAvailableQuantity((int)$this->id, (int)$idProductAttribute, 0, false);
                            }
                        }
                    }
                }
            }
        }
        $res &= Db::getInstance()->delete('pm_advancedpack_products', '`id_pack`='. (int)$this->id);
        $res &= Db::getInstance()->delete('pm_advancedpack_products_attributes', '`id_product_pack` NOT IN (SELECT `id_product_pack` FROM `'._DB_PREFIX_.'pm_advancedpack_products`)');
        $res &= Db::getInstance()->delete('pm_advancedpack_products_customization', '`id_product_pack` NOT IN (SELECT `id_product_pack` FROM `'._DB_PREFIX_.'pm_advancedpack_products`)');
        self::clearAP5Cache();
        foreach ($packContent as $k => $packContentRow) {
            unset($packContentRow['customCombinations']);
            unset($packContentRow['combinationsInformations']);
            unset($packContentRow['customCustomizationField']);
            $res &= Db::getInstance()->insert('pm_advancedpack_products', $packContentRow, true);
            if (is_null($packContentRow['id_product_pack'])) {
                $packContent[$k]['id_product_pack'] = (int)Db::getInstance()->Insert_ID();
            }
        }
        foreach ($packContent as $k => $packContentRow) {
            if (AdvancedPackCoreClass::_isFilledArray($packContentRow['customCombinations'])) {
                foreach ($packContentRow['customCombinations'] as $idProductAttribute) {
                    $res &= Db::getInstance()->insert('pm_advancedpack_products_attributes', array(
                        'id_product_pack' => $packContentRow['id_product_pack'],
                        'id_product_attribute' => $idProductAttribute,
                    ));
                }
            }
            if (AdvancedPackCoreClass::_isFilledArray($packContentRow['combinationsInformations'])) {
                foreach ($packContentRow['combinationsInformations'] as $idProductAttribute => $combinationRow) {
                    $res &= Db::getInstance()->update('pm_advancedpack_products_attributes', array(
                        'reduction_amount' => (float)$combinationRow['reduction_amount'],
                        'reduction_type' => (empty($combinationRow['reduction_type']) ? null : $combinationRow['reduction_type']),
                    ), '`id_product_pack`=' . (int)$packContentRow['id_product_pack'] . ' AND `id_product_attribute`=' . (int)$idProductAttribute, 0, true);
                }
            }
            if (AdvancedPackCoreClass::_isFilledArray($packContentRow['customCustomizationField'])) {
                foreach ($packContentRow['customCustomizationField'] as $idCustomizationField) {
                    $res &= Db::getInstance()->insert('pm_advancedpack_products_customization', array('id_product_pack' => $packContentRow['id_product_pack'], 'id_customization_field' => $idCustomizationField));
                }
            }
        }
        $res &= Db::getInstance()->update('pm_advancedpack', array('fixed_price' => json_encode($packSettings['fixedPrice']), 'allow_remove_product' => (int)$packSettings['allowRemoveProduct']), '`id_pack`=' . (int)$this->id . ' AND `id_shop`=' . (int)self::getContext()->shop->id, 0, true);
        return $res;
    }
    public static function removeOldPackData()
    {
        $oldCart = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT DISTINCT c.`id_cart`, acp.`id_pack`, acp.`id_product_attribute_pack`
            FROM `'._DB_PREFIX_.'pm_advancedpack_cart_products` acp
            LEFT JOIN `'._DB_PREFIX_.'cart` c ON (c.`id_cart` = acp.`id_cart`)
            WHERE acp.`id_order` IS NULL
            AND c.`date_upd` < DATE_SUB(NOW(), INTERVAL 10 DAY)
        ');
        if ($oldCart !== false && AdvancedPackCoreClass::_isFilledArray($oldCart)) {
            foreach ($oldCart as $oldCartRow) {
                if (!empty($oldCartRow['id_cart']) && !empty($oldCartRow['id_pack'])) {
                    $idAttribute = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
                        SELECT `id_attribute`
                        FROM `'._DB_PREFIX_.'product_attribute_combination`
                        WHERE `id_product_attribute` = '.(int)$oldCartRow['id_product_attribute_pack']);
                    if (!empty($idAttribute)) {
                        $attributeObj = new Attribute($idAttribute);
                        if (Validate::isLoadedObject($attributeObj) && $attributeObj->id_attribute_group == self::getPackAttributeGroupId()) {
                            $attributeObj->delete();
                            Db::getInstance()->execute('
                                DELETE FROM `'._DB_PREFIX_.'pm_advancedpack_cart_products`
                                WHERE `id_pack` = ' . (int)$oldCartRow['id_pack'] . '
                                AND `id_product_attribute_pack` = ' . (int)$oldCartRow['id_product_attribute_pack'] . '
                                AND `id_cart` = ' . (int)$oldCartRow['id_cart']);
                        }
                    }
                }
            }
        }
    }
    private static $ap5Context = null;
    public static function setContext($context)
    {
        pm_advancedpack::$moduleCacheId = null;
        self::$ap5Context = $context;
        $currentContext = Context::getContext();
        $currentContext->customer = $context->customer;
        $currentContext->currency = $context->currency;
        $currentContext->country = $context->country;
    }
    public static function getContext()
    {
        if (self::$ap5Context == null) {
            self::$ap5Context = Context::getContext();
        }
        return self::$ap5Context;
    }
    public static function excludeTaxeOption()
    {
        static $excludeTaxeOption = null;
        if ($excludeTaxeOption === null) {
            $excludeTaxeOption = Tax::excludeTaxeOption();
        }
        return $excludeTaxeOption;
    }
    public static function getModuleInstance()
    {
        static $moduleInstance = null;
        if ($moduleInstance === null) {
            $moduleInstance = Module::getInstanceByName('pm_advancedpack');
        }
        return $moduleInstance;
    }
    public static function changeProductPropertiesCache()
    {
        $context = Context::getContext();
        $idLang = $context->language->id;
        $useTax = (Product::getTaxCalculationMethod((int)$context->cookie->id_customer) != 1);
        $useTaxCacheId = Tax::excludeTaxeOption();
        $allowOosp = Product::isAvailableWhenOutOfStock(0);
        $idPackList = self::getIdsPacks();
        foreach ($idPackList as $idPack) {
            $idProductAttribute = Product::getDefaultAttribute($idPack, !$allowOosp);
            $cacheId = $idPack.'-'.$idProductAttribute.'-'.$idLang.'-'.(int)$useTaxCacheId;
            $productObj = new Product($idPack, false);
            if (!Validate::isLoadedObject($productObj)) {
                continue;
            }
            try {
                $row = $productObj->getFields();
                $rowLang = $productObj->getFieldsLang();
            } catch (Exception $e) {
                continue;
            }
            if (!isset($rowLang[$idLang]) || !is_array($rowLang)) {
                continue;
            }
            if (isset($productObj->out_of_stock)) {
                $row['out_of_stock'] = $productObj->out_of_stock;
            } else {
                $row['out_of_stock'] = StockAvailable::outOfStock((int)$idPack, $context->shop->id);
            }
            $productCover = Product::getCover((int)$idPack);
            if (is_array($productCover) && !empty($productCover['id_image'])) {
                $row['id_image'] = $productCover['id_image'];
            }
            $row = array_merge($row, $rowLang[$idLang]);
            Product::getProductProperties($idLang, $row, $context);
            if (isset(self::$producPropertiesCache[$cacheId])) {
                self::$producPropertiesCache[$cacheId]['price'] = AdvancedPack::getPackPrice((int)self::$producPropertiesCache[$cacheId]['id_product'], true, true, true, 6, array(), array(), array(), true);
                self::$producPropertiesCache[$cacheId]['price_tax_exc'] = AdvancedPack::getPackPrice((int)self::$producPropertiesCache[$cacheId]['id_product'], false, true, true, 6, array(), array(), array(), true);
                self::$producPropertiesCache[$cacheId]['classic_pack_price_tax_exc'] = AdvancedPack::getPackPrice((int)self::$producPropertiesCache[$cacheId]['id_product'], false, false, true, 6, array(), array(), array(), true);
                self::$producPropertiesCache[$cacheId]['price_without_reduction'] = AdvancedPack::getPackPrice((int)self::$producPropertiesCache[$cacheId]['id_product'], $useTax, false, true, 6, array(), array(), array(), true);
                self::$producPropertiesCache[$cacheId]['reduction'] = self::$producPropertiesCache[$cacheId]['classic_pack_price_tax_exc'] - self::$producPropertiesCache[$cacheId]['price_tax_exc'];
                self::$producPropertiesCache[$cacheId]['orderprice'] = self::$producPropertiesCache[$cacheId]['price_tax_exc'];
                self::$producPropertiesCache[$cacheId]['quantity'] = AdvancedPack::getPackAvailableQuantity((int)self::$producPropertiesCache[$cacheId]['id_product']);
                self::$producPropertiesCache[$cacheId]['quantity_all_versions'] = self::$producPropertiesCache[$cacheId]['quantity'];
                if (self::$producPropertiesCache[$cacheId]['reduction'] == 0 && isset(self::$producPropertiesCache[$cacheId]['specific_prices']) && is_array(self::$producPropertiesCache[$cacheId]['specific_prices']) && isset(self::$producPropertiesCache[$cacheId]['specific_prices']['reduction']) && self::$producPropertiesCache[$cacheId]['specific_prices']['reduction'] > 0) {
                    self::$producPropertiesCache[$cacheId]['price_without_reduction'] = AdvancedPack::getPackPrice((int)self::$producPropertiesCache[$cacheId]['id_product'], $useTax, false, true, 6, array(), array(), array(), false);
                }
                self::$producPropertiesCache[$cacheId]['is_ap5_bundle'] = true;
            }
        }
    }
    private static function getPMCacheId($key, $withNativeCacheId = false)
    {
        return self::MODULE_ID . sha1($key.($withNativeCacheId ? AdvancedPack::getModuleInstance()->getPMNativeCacheId() : ''));
    }
    private static function isInCache($key, $static = false)
    {
        if (!_PS_CACHE_ENABLED_ || $static) {
            return Cache::isStored($key);
        } else {
            return Cache::getInstance()->exists($key);
        }
    }
    private static function getFromCache($key, $static = false)
    {
        if (!_PS_CACHE_ENABLED_ || $static) {
            return Cache::retrieve($key);
        } else {
            return Cache::getInstance()->get($key);
        }
    }
    private static function storeInCache($key, $value, $static = false, $ttl = 0)
    {
        if (!_PS_CACHE_ENABLED_ || $static) {
            return Cache::store($key, $value);
        } else {
            return Cache::getInstance()->set($key, $value, $ttl);
        }
    }
    public static function clearAP5Cache()
    {
        if (!_PS_CACHE_ENABLED_) {
            Cache::clean('AP5*');
        } else {
            Cache::clean('AP5*');
            Cache::getInstance()->delete('AP5*');
        }
    }
}

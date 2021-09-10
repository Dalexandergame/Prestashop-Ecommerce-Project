<?php
/**
 * Advanced Pack 5
 *
 * @author    Presta-Module.com <support@presta-module.com> - https://www.presta-module.com
 * @copyright Presta-Module - https://www.presta-module.com
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
class pm_advancedpackajax_cartModuleFrontController extends CartController
{
    /**
     * @see CartController::initContent()
     */
    public function initContent()
    {
        parent::initContent();
        $presenter = new PrestaShop\PrestaShop\Adapter\Cart\CartPresenter();
        $presented_cart = $presenter->present($this->context->cart, true);
        if (is_array($presented_cart) && isset($presented_cart['products']) && sizeof($presented_cart['products'])) {
            $idPackList = AdvancedPack::getIdsPacks();
            $groupPriceDisplayMethod = (int)Group::getCurrent()->price_display_method;
            $psTaxDisplay = (int)Configuration::get('PS_TAX_DISPLAY');
            if (($groupPriceDisplayMethod || $psTaxDisplay) && is_array($idPackList) && sizeof($idPackList)) {
                foreach ($presented_cart['products'] as &$product) {
                    if (in_array($product['id_product'], $idPackList) && !AdvancedPack::getPackIdTaxRulesGroup((int)$product['id_product'])) {
                        if ($groupPriceDisplayMethod) {
                            $presented_cart['totals']['total']['amount'] -= ((int)$product['cart_quantity'] * $product['price_wt']);
                        }
                        $newPrice = AdvancedPack::getPackPrice((int)$product['id_product'], false, true, true, 6, AdvancedPack::getIdProductAttributeListByIdPack((int)$product['id_product'], (int)$product['id_product_attribute']), array(), array(), true);
                        $newPriceWt = AdvancedPack::getPackPrice((int)$product['id_product'], true, true, true, 6, AdvancedPack::getIdProductAttributeListByIdPack((int)$product['id_product'], (int)$product['id_product_attribute']), array(), array(), true);
                        if ($psTaxDisplay) {
                            $presented_cart['subtotals']['tax']['amount'] += (int)$product['cart_quantity'] * ($newPriceWt - $newPrice);
                            $presented_cart['subtotals']['tax']['value'] = Tools::displayPrice($presented_cart['subtotals']['tax']['amount']);
                            $presented_cart['totals']['total_excluding_tax']['amount'] -= (int)$product['cart_quantity'] * ($newPriceWt - $newPrice);
                            $presented_cart['totals']['total_excluding_tax']['value'] = Tools::displayPrice($presented_cart['totals']['total_excluding_tax']['amount']);
                            $product['price_with_reduction_without_tax'] = $newPrice;
                        }
                        if ($groupPriceDisplayMethod) {
                            $product['price'] = Tools::displayPrice($newPrice);
                            $product['price_wt'] = $newPriceWt;
                            $product['total'] = Tools::displayPrice((int)$product['cart_quantity']  * $newPrice);
                            $product['total_wt'] = ((int)$product['cart_quantity'] * $newPriceWt);
                        }
                        if ($groupPriceDisplayMethod) {
                            $presented_cart['totals']['total']['amount'] += ((int)$product['cart_quantity'] * $newPrice);
                            $presented_cart['totals']['total']['value'] = Tools::displayPrice($presented_cart['totals']['total']['amount']);
                        }
                    }
                }
            }
        }
        $this->context->smarty->assign(array(
            'cart' => $presented_cart,
        ));
    }
}

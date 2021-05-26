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
class pm_advancedpackupdate_cartModuleFrontController extends ModuleFrontController
{
    protected $jsonOutput = array();
    protected $hasPackInCart = false;
    protected $hasClassicPackInCart = false;
    public $ajax = true;
    public $display_header = false;
    public $display_footer = false;
    public $display_column_left = false;
    public $display_column_right = false;
    public function init()
    {
        parent::init();
        header('X-Robots-Tag: noindex, nofollow', true);
        $this->ajax = true;
    }
    public function postProcess()
    {
        if (!$this->isTokenValid()) {
            Tools::redirect('index.php');
        }
    }
    public function displayAjax()
    {
        ob_start();
        $cartController = new CartController();
        $cartController->displayAjax();
        $this->jsonOutput = (array)Tools::jsonDecode(ob_get_contents(), true);
        ob_end_clean();
        if (is_array($this->jsonOutput)) {
            $newCartSummary = $this->context->cart->getSummaryDetails(null, true);
            $summaryTotal = 0;
            $summaryTotalVAT = 0;
            if (is_array($newCartSummary)) {
                foreach ($newCartSummary['products'] as &$product) {
                    if (AdvancedPack::isValidPack($product['id_product']) && $product['id_product_attribute'] && !AdvancedPack::getPackIdTaxRulesGroup($product['id_product'])) {
                        if (!$this->hasPackInCart) {
                            $this->hasPackInCart = true;
                        }
                        $oldProduct = $product;
                        $packPrice = AdvancedPack::getPackPrice((int)$product['id_product'], false, true, true, 6, AdvancedPack::getIdProductAttributeListByIdPack((int)$product['id_product'], (int)$product['id_product_attribute']), array(), array(), true);
                        $packPriceWt = AdvancedPack::getPackPrice((int)$product['id_product'], true, true, true, 6, AdvancedPack::getIdProductAttributeListByIdPack((int)$product['id_product'], (int)$product['id_product_attribute']), array(), array(), true);
                        $packClassicPrice = AdvancedPack::getPackPrice((int)$product['id_product'], false, false, true, 6, AdvancedPack::getIdProductAttributeListByIdPack((int)$product['id_product'], (int)$product['id_product_attribute']), array(), array(), true);
                        $product['price'] = $packPrice;
                        $product['price_wt'] = $packPriceWt;
                        $product['total'] = (int)$product['cart_quantity'] * $product['price'];
                        $product['total_wt'] = (int)$product['cart_quantity'] * $product['price_wt'];
                        if (isset($product['price_without_quantity_discount'])) {
                            $product['price_without_quantity_discount'] = $packClassicPrice;
                        }
                        $product['attributes'] = $this->module->displayPackContent((int)$product['id_product'], (int)$product['id_product_attribute'], pm_advancedpack::PACK_CONTENT_BLOCK_CART);
                        $productVAT = ($packPriceWt - $packPrice);
                        $summaryTotalVAT += (int)$product['cart_quantity'] * $productVAT;
                        $newProductSummaryTotal = (int)$product['cart_quantity'] * $packPrice;
                        $summaryTotal += ($oldProduct['total'] - $newProductSummaryTotal);
                    } elseif (AdvancedPack::isValidPack($product['id_product']) && $product['id_product_attribute']) {
                        if (!$this->hasClassicPackInCart) {
                            $this->hasClassicPackInCart = true;
                        }
                        $product['attributes'] = $this->module->displayPackContent((int)$product['id_product'], (int)$product['id_product_attribute'], pm_advancedpack::PACK_CONTENT_BLOCK_CART);
                    }
                }
            }
            if ($this->hasPackInCart || $this->hasClassicPackInCart) {
                foreach ($this->jsonOutput['products'] as &$product) {
                    if (AdvancedPack::isValidPack($product['id']) && $product['idCombination']) {
                        $product['attributes'] = $this->module->displayPackContent((int)$product['id'], (int)$product['idCombination'], pm_advancedpack::PACK_CONTENT_BLOCK_CART);
                    }
                }
            }
            if ($this->hasPackInCart) {
                $newCartSummary['total_products'] -= $summaryTotal;
                $newCartSummary['total_price_without_tax'] -= $summaryTotal;
                $newCartSummary['total_tax'] += $summaryTotalVAT;
                if ((int)Group::getCurrent()->price_display_method) {
                    foreach ($this->jsonOutput['products'] as &$product) {
                        if (AdvancedPack::isValidPack($product['id']) && $product['idCombination'] && !AdvancedPack::getPackIdTaxRulesGroup($product['id'])) {
                            $packPrice = AdvancedPack::getPackPrice((int)$product['id'], false, true, true, 6, AdvancedPack::getIdProductAttributeListByIdPack((int)$product['id'], (int)$product['idCombination']), array(), array(), true);
                            $product['price_float'] = $product['quantity'] * $packPrice;
                            $product['price'] = Tools::displayPrice($product['price_float'], $this->context->currency);
                            $product['priceByLine'] = Tools::displayPrice($product['price_float'], $this->context->currency);
                        }
                    }
                    $this->jsonOutput['productTotal'] = Tools::displayPrice($newCartSummary['total_products'], $this->context->currency);
                    $this->jsonOutput['total'] = Tools::displayPrice($this->context->cart->getOrderTotal(false) - $summaryTotal, $this->context->currency);
                }
            }
            if ($this->hasPackInCart || $this->hasClassicPackInCart) {
                $this->jsonOutput['summary'] = $newCartSummary;
            }
            die(Tools::jsonEncode(array('hasError' => false, 'cartData' => $this->jsonOutput)));
        }
        die(Tools::jsonEncode(array('hasError' => true)));
    }
    public function initContent()
    {
    }
}

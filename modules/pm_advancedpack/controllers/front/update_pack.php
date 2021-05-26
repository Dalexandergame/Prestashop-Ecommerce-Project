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
class pm_advancedpackupdate_packModuleFrontController extends ModuleFrontController
{
    protected $idPack;
    protected $productPackChoice = array();
    protected $productPackExclude = array();
    protected $productPackQuantityList = array();
    protected $jsonOutput = array();
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
        $this->idPack = (int)Tools::getValue('id_pack');
        if (Tools::getIsset('productPackChoice')) {
            $tmp_productPackChoice = (array)Tools::getValue('productPackChoice');
            if (is_array($tmp_productPackChoice) && count($tmp_productPackChoice)) {
                foreach ($tmp_productPackChoice as $packChoiceRow) {
                    $this->productPackChoice[(int)$packChoiceRow['idProductPack']] = array_map('intval', $packChoiceRow['attributesList']);
                }
            }
            unset($tmp_productPackChoice);
            if (!count($this->productPackChoice)) {
                $this->errors[] = Tools::displayError($this->module->getFrontTranslation('errorInvalidPackChoice'), false);
            }
        }
        if (Tools::getIsset('productPackExclude')) {
            $this->productPackExclude = array_unique(array_map('intval', (array)Tools::getValue('productPackExclude')));
        }
        if (Tools::getIsset('productPackQuantityList')) {
            $tmp_productPackQuantityList = (array)Tools::getValue('productPackQuantityList');
            if (is_array($tmp_productPackQuantityList) && count($tmp_productPackQuantityList)) {
                foreach ($tmp_productPackQuantityList as $packChoiceRow) {
                    if (!is_numeric($packChoiceRow['quantity']) || (int)$packChoiceRow['quantity'] <= 0) {
                        $packChoiceRow['quantity'] = 1;
                    }
                    $this->productPackQuantityList[(int)$packChoiceRow['idProductPack']] = (int)$packChoiceRow['quantity'];
                }
            }
        }
    }
    public function postProcess()
    {
        if (!$this->isTokenValid()) {
            Tools::redirect('index.php');
        }
    }
    public function displayAjax()
    {
        if (!count($this->errors) && AdvancedPack::isValidPack($this->idPack)) {
            $packAttributesList = array();
            $packCompleteAttributesList = array();
            $packErrorsList = array();
            $packFatalErrorsList = array();
            foreach ($this->productPackChoice as $idProductPack => $attributeList) {
                if (in_array($idProductPack, $this->productPackExclude)) {
                    continue;
                }
                $idProductAttribute = AdvancedPack::combinationExists((int)$idProductPack, $attributeList);
                if ($idProductAttribute === false) {
                    $packErrorsList[(int)$idProductPack][] = $this->module->getFrontTranslation('errorWrongCombination');
                } else {
                    $packAttributesList[(int)$idProductPack] = (int)$idProductAttribute;
                }
                $packCompleteAttributesList[(int)$idProductPack] = $attributeList;
            }
            $packContent = AdvancedPack::getPackContent($this->idPack, null, false, $packAttributesList, $this->productPackQuantityList);
            if ($packContent !== false) {
                foreach ($packContent as $packProduct) {
                    if (in_array((int)$packProduct['id_product_pack'], $this->productPackExclude)) {
                        continue;
                    }
                    $product = new Product((int)$packProduct['id_product']);
                    if (Validate::isLoadedObject($product) && !$product->active) {
                        $packFatalErrorsList[(int)$packProduct['id_product_pack']][] = $this->module->getFrontTranslation('errorProductIsDisabled');
                    } elseif (Validate::isLoadedObject($product) && !$product->checkAccess(isset(Context::getContext()->customer) ? Context::getContext()->customer->id : 0)) {
                        $packFatalErrorsList[(int)$packProduct['id_product_pack']][] = $this->module->getFrontTranslation('errorProductAccessDenied');
                    } elseif (Validate::isLoadedObject($product) && !$product->available_for_order) {
                        $packFatalErrorsList[(int)$packProduct['id_product_pack']][] = $this->module->getFrontTranslation('errorProductIsNotAvailableForOrder');
                    }
                }
            }
            if (AdvancedPack::getPackAllowRemoveProduct($this->idPack) && sizeof($packContent) >= 2 && ($packContent == false || sizeof($this->productPackExclude) >= sizeof($packContent))) {
                $this->errors[] = Tools::displayError($this->module->getFrontTranslation('errorInvalidExclude'), false);
            }
            if (!count($this->errors)) {
                $packQuantityList = $packQuantityOriginalList = AdvancedPack::getPackAvailableQuantityList($this->idPack, $packAttributesList, $this->productPackQuantityList);
                if (count($this->productPackQuantityList)) {
                    $packQuantityOriginalList = AdvancedPack::getPackAvailableQuantityList($this->idPack, $packAttributesList);
                }
                foreach ($packAttributesList as $idProductPack => $idProductAttribute) {
                    if (isset($packQuantityList[(int)$idProductPack]) && array_sum($packQuantityList[(int)(int)$idProductPack]) <= 0) {
                        if (count($this->productPackQuantityList) && isset($packQuantityOriginalList[(int)$idProductPack]) && array_sum($packQuantityOriginalList[(int)(int)$idProductPack]) <= 0) {
                            $packFatalErrorsList[(int)$idProductPack][] = $this->module->getFrontTranslation('errorProductIsOutOfStock');
                        } else {
                            $packErrorsList[(int)$idProductPack][] = $this->module->getFrontTranslation('errorProductIsOutOfStock');
                        }
                    } elseif (isset($packQuantityList[(int)$idProductPack][$idProductAttribute]) && $packQuantityList[(int)(int)$idProductPack][$idProductAttribute] <= 0) {
                        $packErrorsList[(int)$idProductPack][] = $this->module->getFrontTranslation('errorProductOrCombinationIsOutOfStock');
                    }
                }
                foreach ($this->productPackExclude as $idProductPackExcluded) {
                    if (isset($packErrorsList[$idProductPackExcluded])) {
                        unset($packErrorsList[$idProductPackExcluded]);
                    }
                    if (isset($packFatalErrorsList[$idProductPackExcluded])) {
                        unset($packFatalErrorsList[$idProductPackExcluded]);
                    }
                }
                $this->jsonOutput['packAvailableQuantity'] = AdvancedPack::getPackAvailableQuantity($this->idPack, $packAttributesList, $this->productPackQuantityList, $this->productPackExclude);
                $this->jsonOutput['packContentTable'] = $this->module->displayPackContentTable($this->idPack, $packAttributesList, $packCompleteAttributesList, $this->productPackQuantityList, $this->productPackExclude, $packErrorsList, $packFatalErrorsList);
                $this->jsonOutput['packPriceContainer'] = $this->module->displayPackPriceContainer($this->idPack, $packAttributesList, $this->productPackQuantityList, $this->productPackExclude, $packErrorsList, $packFatalErrorsList);
                $this->jsonOutput['HOOK_EXTRA_RIGHT'] = Hook::exec('displayRightColumnProduct');
                $this->jsonOutput['packErrorsList'] = $packErrorsList;
                $this->jsonOutput['packFatalErrorsList'] = $packFatalErrorsList;
                $this->jsonOutput['packHasErrors'] = count($packErrorsList) ? true : false;
                $this->jsonOutput['packHasFatalErrors'] = count($packFatalErrorsList) ? true : false;
                $this->jsonOutput['packAttributesList'] = (array)Tools::jsonEncode($packAttributesList);
                $this->jsonOutput['productPackExclude'] = (array)$this->productPackExclude;
                die(Tools::jsonEncode($this->jsonOutput));
            }
        } else {
            $this->errors[] = Tools::displayError($this->module->getFrontTranslation('errorInvalidPack'), false);
        }
        if (count($this->errors)) {
            die(Tools::jsonEncode(array('hasError' => true, 'errors' => $this->errors)));
        }
    }
    public function initContent()
    {
    }
    public function getProduct()
    {
        $packObj = new Product((int)$this->idPack, false, Context::getContext()->language->id);
        return $packObj;
    }
}

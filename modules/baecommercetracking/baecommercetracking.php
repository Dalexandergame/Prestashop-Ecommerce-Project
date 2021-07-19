<?php
/**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <hatt@buy-addons.com>
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
* @since 1.6
*/

class BaEcommerceTracking extends Module
{
    private $demoMode = false;
    public function __construct()
    {
        $this->name = "baecommercetracking";
        $this->tab = "analytics_stats";
        $this->version = "1.0.6";
        $this->author = "buy-addons";
        $this->need_instance = 0;
        $this->secure_key = Tools::encrypt($this->name);
        $this->bootstrap = true;
        $this->module_key = '602cd0bdd0899986aa08946c275edcc3';
        parent::__construct();
        $this->displayName = $this->l('Google and Facebook Conversion Tracking.');
        $this->description = $this->l('Tracking Conversion from Google Analytics, Google Adword, Facebook Pixel.');
    }
    
    public function install()
    {
        if (parent::install() == false) {
            return false;
        }
        if ($this->registerHook("displayFooter") == false
            || $this->registerHook("orderConfirmation") == false
            || $this->registerHook("displayHeader") == false
            || $this->registerHook("hookHeader") == false) {
            return false;
        }
        return true;
    }
    
    public function uninstall()
    {
        if (parent::uninstall()) {
            return true;
        }
        return true;
    }
    
    public function getContent()
    {
        $token=Tools::getAdminTokenLite('AdminModules');
        $adminController=AdminController::$currentIndex;
        $buttonSubmitSaveArr = array(
            'baSaveAndStay',
            'baSave',
            'baAdwordsSaveAndStay',
            'baAdwordsSave',
            'baFacebookSave',
            'baAdwordsSaveAndStay'
        );
        if ($this->demoMode==true) {
            foreach ($buttonSubmitSaveArr as $buttonSubmitSave) {
                if (Tools::isSubmit($buttonSubmitSave)) {
                    Tools::redirectAdmin($adminController.'&token='.$token.'&configure='.$this->name.'&demoMode=1');
                }
            }
        }
        if (Tools::isSubmit('baSaveAndStay')) {
            $this->saveData();
        } elseif (Tools::isSubmit('baSave')) {
            $this->saveData();
            Tools::redirectAdmin($adminController.'&token='.$token);
        } elseif (Tools::isSubmit('baCancel')) {
            Tools::redirectAdmin($adminController.'&token='.$token);
        }
        
        if (Tools::isSubmit('baAdwordsSaveAndStay')) {
            $this->saveDataAdword();
        } elseif (Tools::isSubmit('baAdwordsSave')) {
            $this->saveDataAdword();
            Tools::redirectAdmin($adminController.'&token='.$token);
        } elseif (Tools::isSubmit('baAdwordsCancel')) {
            Tools::redirectAdmin($adminController.'&token='.$token);
        }
        
        if (Tools::isSubmit('baFacebookSaveAndStay')) {
            $this->saveDataFacebook();
        } elseif (Tools::isSubmit('baFacebookSave')) {
            $this->saveDataFacebook();
            Tools::redirectAdmin($adminController.'&token='.$token);
        } elseif (Tools::isSubmit('baFacebookCancel')) {
            Tools::redirectAdmin($adminController.'&token='.$token);
        }
        
        $this->getDataAdword();
        $this->getDataFacebook();
        $baIdAnalytics=Configuration::get('baIdAnalytics');
        $this->smarty->assign('baIdAnalytics', $baIdAnalytics);
        $this->context->controller->addCSS($this->_path.'views/css/style.css');
        $this->context->controller->addJS($this->_path.'views/js/admin.js');
        $this->smarty->assign('demoMode', Tools::getValue('demoMode'));
        return $this->display(__FILE__, 'views/templates/admin/baecommercetracking.tpl');
    }
    
    private function saveData()
    {
        $baIdAnalytics=Tools::getIsset("baIdAnalytics")?Tools::getValue("baIdAnalytics"):'';
        Configuration::updateValue('baIdAnalytics', trim($baIdAnalytics));
    }
    
    private function saveDataAdword()
    {
        $baIdAdwords=Tools::getIsset("baIdAdwords")?Tools::getValue("baIdAdwords"):'';
        Configuration::updateValue('baIdAdwords', trim($baIdAdwords));
        
        $baIdAdwordsLabel=Tools::getIsset("baIdAdwordsLabel")?Tools::getValue("baIdAdwordsLabel"):'';
        Configuration::updateValue('baIdAdwordsLabel', trim($baIdAdwordsLabel));
    }
    
    private function getDataAdword()
    {
        $baIdAdwords=Configuration::get('baIdAdwords');
        $this->smarty->assign('baIdAdwords', $baIdAdwords);
        
        $baIdAdwordsLabel=Configuration::get('baIdAdwordsLabel');
        $this->smarty->assign('baIdAdwordsLabel', $baIdAdwordsLabel);
        
        $languageDefault = $this->context->language->iso_code;
        $this->smarty->assign('languageDefault', $languageDefault);
        
        $currencyDefault = $this->context->currency->iso_code;
        $this->smarty->assign('currencyDefault', $currencyDefault);
    }
    
    private function saveDataFacebook()
    {
        $facebookId=Tools::getIsset("facebookId")?Tools::getValue("facebookId"):'';
        Configuration::updateValue('facebookId', Tools::htmlentitiesUTF8(trim($facebookId)));
    }
    
    private function getDataFacebook()
    {
        $facebookId=Configuration::get('facebookId');
        $this->smarty->assign('facebookId', $facebookId);
    }
    
    public function getSKUProduct($product)
    {
        $sku='EAN_'.$product['product_ean13'];
        if ($product['product_upc'] != "") {
            $sku='UPC_'.$product['product_upc'];
        } elseif ($product['product_reference'] != "") {
            $sku='REFERENCE_'.$product['product_reference'];
        } else {
            $sku='PRODUCT_ID_'.$product['product_id'];
        }
        return $sku;
    }
     
    public function hookorderConfirmation($params)
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>')) {
            $order = $params['order'];
        } else {
            $order = $params['objOrder'];
        }
        $this->context->smarty->assign('order', $order);
        
        $idOrder = Order::getOrderByCartId($order->id_cart);
        $this->context->smarty->assign('idOrder', $idOrder);
        
        $productList = $order->getProducts();
        foreach ($productList as $key => $product) {
            $category = new Category($product['id_category_default'], $order->id_lang, $product['id_shop']);
            $productList[$key]['category_name'] = $category->name;
        }
        //var_dump($productList);die;
        $this->context->smarty->assign('productList', $productList);

        $currency = new Currency($order->id_currency);
        $this->context->smarty->assign('currency_iso_code', $currency->iso_code);
        
        $totalTax = ($order->total_paid_tax_incl - $order->total_paid_tax_excl);
        $this->context->smarty->assign('totalTax', $totalTax);
        
        $language = new Language($order->id_lang);
        $this->context->smarty->assign('language_iso_code', $language->iso_code);
        
        $baIdAnalytics=Configuration::get('baIdAnalytics');
        $this->context->smarty->assign('baIdAnalytics', $baIdAnalytics);
        
        $PS_SHOP_NAME=Configuration::get('PS_SHOP_NAME');
        $this->context->smarty->assign('PS_SHOP_NAME', $PS_SHOP_NAME);
        
        $baIdAdwordsLabel=Configuration::get('baIdAdwordsLabel');
        $this->context->smarty->assign('baIdAdwordsLabel', $baIdAdwordsLabel);
        //Tracking google analytics
        //End tracking google analytics
        //Tracking google adword
        $baIdAdwords=Configuration::get('baIdAdwords');
        $this->context->smarty->assign('baIdAdwords', $baIdAdwords);
        //End tracking google adword
        //Tracking facebook
        $facebookId = Configuration::get('facebookId');
        $this->context->smarty->assign('facebookId', $facebookId);

        //End Tracking facebook
        return $this->display(__FILE__, 'views/templates/front/blockecommercetracking.tpl');
    }
    
    public function hookdisplayFooter($params)
    {
        $baIdAnalytics=Configuration::get('baIdAnalytics');
        $this->context->smarty->assign('baIdAnalytics', $baIdAnalytics);
    }
    public function hookdisplayHeader(& $params)
    {
        return $this->hookHeader($params);
    }
    public function hookHeader($params)
    {
        $page_type = Tools::getValue('controller');
        $stepoder = Tools::getValue("step");
        $baIdAnalytics = Configuration::get('baIdAnalytics');
        $currency_code = $this->context->currency->iso_code;
        $facebookId = Configuration::get('facebookId');
        $this->context->smarty->assign('facebookId', $facebookId);
        $this->context->smarty->assign('bastepoder', $stepoder);
        $this->context->smarty->assign('currency_code', $currency_code);
        $this->context->smarty->assign('baIdAnalytics', $baIdAnalytics);
        $this->context->smarty->assign('page_type', $page_type);
        return $this->display(__FILE__, 'views/templates/front/blockecommercetracking2.tpl');
    }
}

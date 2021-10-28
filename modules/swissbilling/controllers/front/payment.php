<?php
/**
*  NOTICE OF LICENSE
* 
*  Module for Prestashop
*  100% Swiss development
* 
*  @author    Webbax <contact@webbax.ch>
*  @copyright -
*  @license   -
*/

class SwissbillingPaymentModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $this->display_column_left = false;
        parent::initContent();

        $cart = $this->context->cart;
        if (!$this->module->checkCurrency($cart))
                Tools::redirect('index.php?controller=order');

        $this->context->smarty->assign(array(
                'nbProducts' => $cart->nbProducts(),
                'cust_currency' => $cart->id_currency,
                'currencies' => $this->module->getCurrency((int)$cart->id_currency),
                'total' => $cart->getOrderTotal(true, Cart::BOTH),
                'this_path' => $this->module->getPathUri(),
                'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/',
                'ps_version'=> $this->module->ps_version,
        ));

        $this->setTemplate('payment_execution.tpl');
    }
}

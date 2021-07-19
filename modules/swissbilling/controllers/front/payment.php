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
                $cookie = $this->context->cookie;
                $this->display_column_left = false;
		parent::initContent();

		$cart = $this->context->cart;
		if (!$this->module->checkCurrency($cart))
			Tools::redirect('index.php?controller=order');

                //BEGIN Ajout de frais de paiement 5CHF
                  $products = $cart->getProducts(false,false,null,true);
                  $frai_virement = (int) Configuration::get('TUNNELVENTE_ID_FRAI_VIREMENT');
                  $find_cost = false;

                  foreach($products as $p){
                      if($p['id_product']==$frai_virement){$find_cost=true;}
                  }

                  if(!$find_cost){
                      $Product = new Product($frai_virement,false,  $this->context->language->id);
                      $cart->updateQty(1,$Product->id); 
                  }  
                  //END
                    
		$this->context->smarty->assign(array(
			'nbProducts' => $cart->nbProducts(),
			'cust_currency' => $cart->id_currency,
			'currencies' => $this->module->getCurrency((int)$cart->id_currency),
			'total' => $cart->getOrderTotal(true, Cart::BOTH),
			'this_path' => $this->module->getPathUri(),
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/',
                        'info_prepmt' => Configuration::get('SWSBLG_INFO_PREPMT_'.$cookie->id_lang),
                        'ps_version'=> $this->module->ps_version,
		));

		$this->setTemplate('payment_execution.tpl');
	}
}

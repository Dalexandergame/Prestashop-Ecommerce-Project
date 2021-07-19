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

@ini_set('soap.wsdl_cache_enabled','0');

class SwissbillingSuccessModuleFrontController extends ModuleFrontController
{
	public $ssl = true;

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
                     
            $Context = Context::getContext();
            $cookie = $Context->cookie;
            $cookie->order_timestamp = Tools::getValue('timestamp');
            $Swissbilling = new Swissbilling();

            $trans = Tools::getValue('trans'); 
            if($trans==$cookie->id_cart){
                 
                // V1
                if(Tools::version_compare(_PS_VERSION_, '1.6.1.5','<')){
                    include_once(_PS_SWIFT_DIR_.'Swift.php');
                    include_once(_PS_SWIFT_DIR_.'Swift/Connection/SMTP.php');
                    include_once(_PS_SWIFT_DIR_.'Swift/Connection/NativeMail.php');
                    include_once(_PS_SWIFT_DIR_.'Swift/Plugin/Decorator.php');
                // V2
                }else{
                    include_once(_PS_SWIFT_DIR_.'swift_required.php');
                }
                
                $cart = new Cart($cookie->id_cart);
                $customer = new Customer($cookie->id_customer);
                $total = (float)($cart->getOrderTotal(true,Cart::BOTH));

                // mail dans la langue client            
                $subject = $Swissbilling->l('Confirmation of your request for payment by invoice','success').' '.Configuration::get('PS_SHOP_NAME');
                $msg_content = $Swissbilling->l('Hello','success').',<br/><br/>'.
                $Swissbilling->l('Your request for payment on account was properly.','success').'<br/>'.
                $Swissbilling->l('You will be notified by email when your order status will be changed.','success').'<br/><br/>';
                $mailFormatMsg = $Swissbilling->generateMailTemplate($subject,$msg_content);
                
                //todo remove comment to enable mailSend after success
                $Swissbilling->MailSend($subject,$mailFormatMsg,$cookie->email);
                
                // vérifie s'il faut ajouter des frais supplémentaires à la commande
                // --------------------------------------------------
                // paramètres généraux marchand
                $merchant = array(
                    'id'            => $Swissbilling->merchant_id,
                    'pwd'           => $Swissbilling->merchant_pw,
                    'success_url'   => $Swissbilling->success_url,
                    'cancel_url'    => $Swissbilling->cancel_url,
                    'error_url'     => $Swissbilling->error_url,
                    );
   
                // 09.04.14 - confirmation
                // assure Swissbilling que le client a été redirigé
                $SoapClient = new SoapClient($Swissbilling->url_wsdl_old,array('trace'=>0,'exceptions'=>1));
                $response = $SoapClient->EshopTransactionStatusRequest($merchant,$cookie->transaction_ref,$cookie->order_timestamp);  
                try{
                    $Confirmation = $SoapClient->EshopTransactionConfirmation($merchant,$cookie->transaction_ref,$cookie->order_timestamp);
                    $action = Tools::strtolower($Confirmation->action);
                    if($action!=='success'){
                        $msg = $Swissbilling->l('Error status confirmation','success').' : action->'.$action.', transaction_ref->'.$cookie->transaction_ref;
                        Tools::redirect($Swissbilling->error_url.'?msg='.urlencode($msg));
                    }
                }catch(SoapFault $exception){
                    $message = $exception->getMessage();
                    Tools::d($message);
                    exit;
                }

                // frais de facturation
                $invoicing_costs = $response->partial_payment_fees+$response->invoicing_costs;
                if($invoicing_costs>0){
                    
                    // id produit des frais
                    $id_product = Db::getInstance()->getValue('
                    SELECT `id_product`
                    FROM `'._DB_PREFIX_.'product` p
                    WHERE p.`reference` = "'.$Swissbilling->ref_product.'"');

                    // présence de la taxe dans le panier ?
                    $prods = $cart->getProducts();
                    $find_tax = false;
                    foreach($prods as $p){if($p['id_product']==$id_product){$find_tax=true;}}

                    if(!$find_tax){    
                        // met à jour le produit des frais
                        $price_tax_swissbilling = $invoicing_costs+Configuration::get('SWSBLG_ADMIN_FEE_AMOUNT');
                        Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'product` SET `price`="'.pSQL($price_tax_swissbilling).'" WHERE `id_product`="'.pSQL($id_product).'" AND `id_shop_default`="'.pSQL(Shop::getContextShopID()).'"');
                        Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'product_shop` SET `price`="'.pSQL($price_tax_swissbilling).'" WHERE `id_product`="'.pSQL($id_product).'" AND `id_shop`="'.pSQL(Shop::getContextShopID()).'"');
                        // met à jour le panier
                        //$Cart = new Cart($cookie->id_cart);
                        $this->context->cart->updateQty(1,$id_product);
                        // reprends le total du panier
                        $total = (float)($this->context->cart->getOrderTotal(true,Cart::BOTH));
                    }
                }
                // force le panier à refresh
                $this->context->cart->getPackageList(true);
                // --------------------------------------------------
                
                
                // validation automatique de la transaction
                //--
                if($Swissbilling->auto_validation){
                    try {
                        $SoapClient = new SoapClient($Swissbilling->url_wsdl_old,array('trace'=>0,'exceptions'=>1));
                        $SoapClient->EshopTransactionAcknowledge($merchant, $cookie->transaction_ref,Tools::getValue('timestamp'));
                    }        
                    catch(SoapFault $exception){
                        $message = $exception->getMessage();
                        Tools::d($message);
                        exit;
                    }    
                }
                //--

                $mailVars = array();
                $id_order_state = Configuration::get('SWSBLG_ID_ORDER_STATE',null,$this->context->shop->id_shop_group,$this->context->shop->id);
                $Swissbilling->validateOrder($cart->id,$id_order_state,$total,$Swissbilling->displayName, NULL, $mailVars,null, false,$cart->secure_key); 
                $order = new Order($Swissbilling->currentOrder);
                // redirection sur payment_return
                if($Swissbilling->ps_version>=1.6){
                    Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.$Swissbilling->id.'&id_order='.$Swissbilling->currentOrder.'&key='.$customer->secure_key); 
                }else{
                    Tools::redirectLink(Tools::getHttpHost(true).__PS_BASE_URI__.'order-confirmation.php?id_cart='.$cart->id.'&id_module='.$Swissbilling->id.'&id_order='.$Swissbilling->currentOrder.'&key='.$order->secure_key);
                }

            }else{
                d($Swissbilling->l('Error validation','success'));
            }
            
	}
}

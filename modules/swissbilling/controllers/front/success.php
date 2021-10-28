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
   
                $cart = new Cart($cookie->id_cart);
                $customer = new Customer($cookie->id_customer);
                $total = (float)($cart->getOrderTotal(true,Cart::BOTH));

                // mail dans la langue client            
                if(Configuration::get('SWSBLG_CONF_MAIL')){
                    $subject = $Swissbilling->l('Confirmation of your request for payment by invoice','success').' '.Configuration::get('PS_SHOP_NAME');
                    $msg_content = $Swissbilling->l('Hello','success').','."\r\n".
                    $Swissbilling->l('Your request for payment on account was properly.','success')."\r\n".
                    $Swissbilling->l('You will be notified by email when your order status will be changed.','success')."\r\n"."\r\n";
                    $mailFormatMsg = $Swissbilling->generateMailTemplate($subject,$msg_content);
                    $Swissbilling->MailSend($subject,$mailFormatMsg,$cookie->email);
                }
                
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
                if(Configuration::get('SWSBLG_REDIRECTION')==1){
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
                        Tools::dieObject($message);
                        exit;
                    }
                }

                // frais de facturation)
                $invoicing_costs = $response->partial_payment_fees+$response->invoicing_costs;
                if(Configuration::get('SWSBLG_COSTS_ORDER')){
                    
                    // id produit des frais
                    $id_product = Db::getInstance()->getValue('
                    SELECT p.`id_product`
                    FROM `'._DB_PREFIX_.'product` p
                    LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON (p.`id_product` = ps.`id_product`)
                    WHERE p.`reference` = "'.pSQL($Swissbilling->ref_product).'"
                    AND ps.`id_shop`="'.pSQL($this->context->shop->id).'"');
                    
                    if(!empty($id_product)){         
                        // présence de la taxe dans le panier ?
                        $prods = $cart->getProducts();
                        $find_tax = false;
                        foreach($prods as $p){if($p['id_product']==$id_product){$find_tax=true;}}

                        if(!$find_tax){    
                            
                            // Gestion des frais (3 choix possibles)
                            $costs_orders_mode = Configuration::get('SWSBLG_COSTS_ORDER_MODE');
                            // frais client + frais marchand
                            if(empty($costs_orders_mode)){
                                $price_tax_swissbilling = $invoicing_costs+Configuration::get('SWSBLG_ADMIN_FEE_AMOUNT');
                            // frais client uniquement
                            }elseif($costs_orders_mode==1){
                                $price_tax_swissbilling = $invoicing_costs;
                            // frais marchand uniquement
                            }elseif($costs_orders_mode==2){
                                $price_tax_swissbilling = Configuration::get('SWSBLG_ADMIN_FEE_AMOUNT');
                            }
                            
                            // met à jour le produit des frais
                            // s'il ya un bon de réduction on augmente les frais pour compenser la différence
                            $cr = $this->context->cart->getCartRules(CartRule::FILTER_ACTION_REDUCTION);
                            if(isset($cr[0]['reduction_percent']) && $cr[0]['reduction_percent']!='' && $cr[0]['reduction_percent']!=0){
                                $price_tax_swissbilling = $price_tax_swissbilling+$cr[0]['reduction_percent']*$price_tax_swissbilling/100;
                            }
                            Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'product` SET `price`="'.pSQL($price_tax_swissbilling).'" WHERE `id_product`="'.pSQL($id_product).'" AND `id_shop_default`="'.pSQL(Shop::getContextShopID()).'"');
                            Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'product_shop` SET `price`="'.pSQL($price_tax_swissbilling).'" WHERE `id_product`="'.pSQL($id_product).'" AND `id_shop`="'.pSQL(Shop::getContextShopID()).'"');
                            // met à jour le panier
                            $this->context->cart->updateQty(1,$id_product);
                            // reprends le total du panier
                            $total = (float)($this->context->cart->getOrderTotal(true,Cart::BOTH));
                        }
                    }
                    
                }
                // force le panier à refresh
                $this->context->cart->getPackageList(true);
                // --------------------------------------------------
                
                // insertion de la transaction
                $vals = array('id_cart'=>pSQL($cart->id),'timestamp'=>pSQL(Tools::getValue('timestamp')));
                Db::getInstance()->insert('swissbilling',$vals);
                
                // validation automatique de la transaction
                //--
                // assure Swissbilling que le client a été redirigé
                if(Configuration::get('SWSBLG_REDIRECTION')==1){
                    if($Swissbilling->auto_validation){
                        try {
                            $SoapClient = new SoapClient($Swissbilling->url_wsdl_old,array('trace'=>0,'exceptions'=>1));
                            $SoapClient->EshopTransactionAcknowledge($merchant, $cookie->transaction_ref,Tools::getValue('timestamp'));
                        }        
                        catch(SoapFault $exception){
                            $message = $exception->getMessage();
                            Tools::dieObject($message);
                            exit;
                        }    
                    }
                }
                //--
                
                $mailVars = array();
                $id_order_state = Configuration::get('SWSBLG_ID_ORDER_STATE',null,$this->context->shop->id_shop_group,$this->context->shop->id);
                $Swissbilling->validateOrder($cart->id,$id_order_state,$total,$Swissbilling->displayName, NULL, $mailVars,null, false,$cart->secure_key);
                Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.$Swissbilling->id.'&id_order='.$Swissbilling->currentOrder.'&key='.$customer->secure_key); 
                
            }else{
                dump($Swissbilling->l('Error validation','success'));
                exit();
            }
            
	}
}

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

/*
 V1.9.17 - 31.08.16
 - modification de la validation de commande sans reload de page
 - ajout option / validation automatique de la transaction
 V1.9.16 - 31.08.16
 - forcer "soap.wsdl_cache_enabled" à zéro
 V1.9.15 - 22.08.16
 - correction sur frais pour emballages cadeaux
 V1.9.14 - 17.06.16
 - factorisation TPL dans le back-office
 - ajout automatique d'un produit pour les frais Swissbilling
 - retrait des logs format .txt
 - retrait du détail de transaction dans le message client / commande
 - insertion OrderState sous la forme d'objet (installation)
 V1.9.13 - 02.06.16
 - rétro compat. payment_return.tpl (PS 1.5)
 V1.9.12 - 01.06.16
 - optimisations conventions PS Addons
 - textes sources en anglais
 - envoi confirmation mail / traduction dynamique
 V1.9.11 - 28.04.16
 - mise à jour de Swift - PS 1.6.1.5
 - correction icône back-office
 V1.9.10 - 08.03.16
 - correction sur récupération de SWSBLG_ID_ORDER_STATE
 V1.9.9 - 03.02.16
 - optimisation du paramètre B2C & B2B pour les transactions
 V1.9.8 - 22.01.16
 - correction lien sur /error au lieu de /error.php
 V1.9.7 - 06.01.16
 - révision de la structure du module
 - gestion des colonnes sur payment & error
 - amélioration du retour d'erreur côté client
 - redirection cancel sur le panier
 - ajustement méthodes installation / désinstallation
 V1.9.6 - 20.08.15
 - correction remontage mention BO Swissbilling (cancel.php / error.php)
 - correction formatage des adresses clients
 V1.9.5 - 30.07.15
 - ajout contrôle "failure_code" avant redirection sur Swissbilling 
 V1.9.4 - 28.07.15
 - ajout contrôle succès "PS_OS_OUTOFSTOCK_UNPAID"
 V1.9.3 - 30.06.15
 - retrait de l'arrondi sur VAT_amount
 V1.9.2 - 15.05.15
 - optimisation sur la validation & redirection
 V1.9.1 - 09.03.15
 - révision pré-screening
 - corrections affichage processus selon normes PS 1.6
 - corrections présentation email selon normes PS 1.6
 - contrôle langue interface
 - définition date anniversaire par défaut (bug serialize)
 V1.9.0 - 06.01.15
 - intégration pré-screening
 V1.8.2 - 16.12.14
 - désactivation du PDF
 - retrait controlleur obsolète
 - modification description (titre du produit)
 V1.8.1 - 14.10.14
 - correction appel .tpl dans payment (controller)
 V1.8.0 - 28.05.14 
 - compatibilité Prestashop 1.6
 - retrait option de transfert des images
 */

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class Swissbilling extends PaymentModule
{
	private $_html = '';
	private $_postErrors = array();
	public  $merchant_id;
	public  $merchant_pw;
        public  $private_key;
        public  $max_amount;
        public  $pre_screening;
        public  $b2b;
        public  $type;
        public  $auto_validation;
        public  $delivery_status;
        public  $url_wsdl;
        public  $url_wsdl_old;
        public  $success_url;
        public  $cancel_url;
        public  $error_url;
        public  $langs_interface;
        // détail transactions
        public  $total_products = 0;
        public  $phys_delivery = 1;

	public function __construct()
	{
		$this->name = 'swissbilling';
		$this->tab = 'payments_gateways';
		$this->version = '1.9.17';
                $this->module_key = '24eebf1c93245cd9514990db3d9e709d';
                $this->author = 'Webbax';
                
                // propose les colonnes dans la section du thème
                $this->controllers = array('payment','error');

		$this->currencies = true;
		$this->currencies_mode = 'checkbox';

		$config = Configuration::getMultiple(array('SWSBLG_MERCHANT_ID', 'SWSBLG_MERCHANT_PW', 'SWSBLG_PRIVATE_KEY','SWSBLG_MAX_AMOUNT', 'SWSBLG_PRE_SCREENING', 'SWSBLG_B2B', 'SWSBLG_TYPE','SWSBLG_AUTO_VALIDATION','SWSBLG_DELIVERY_STATUS'));
                $this->merchant_id = $config['SWSBLG_MERCHANT_ID'];
		$this->merchant_pw = $config['SWSBLG_MERCHANT_PW'];
                $this->private_key = $config['SWSBLG_PRIVATE_KEY'];
                $this->max_amount = $config['SWSBLG_MAX_AMOUNT'];
                $this->pre_screening = $config['SWSBLG_PRE_SCREENING'];
                $this->b2b = $config['SWSBLG_B2B'];
                $this->type = $config['SWSBLG_TYPE'];
                $this->auto_validation = $config['SWSBLG_AUTO_VALIDATION'];
                $this->delivery_status = $config['SWSBLG_DELIVERY_STATUS'];
                $this->ref_product = 'SWISSBILLING';

                $this->success_url = Tools::getHttpHost(true).__PS_BASE_URI__.'module/swissbilling/success';
                $this->cancel_url = Tools::getHttpHost(true).__PS_BASE_URI__.'module/swissbilling/cancel';
                $this->error_url = Tools::getHttpHost(true).__PS_BASE_URI__.'module/swissbilling/error';
                
                // interface de Swissbilling supportée en français & allemand uniquement
                $this->lang_interface_default = 'FR';
                $this->langs_interface = array('FR','DE'); 
                
                // Test
                if($this->type=='Test'){
                    $this->url_wsdl='http://demo-ws.swissbilling.ch/ws/EshopRequestV3.svc?WSDL'; // V3
                    $this->url_wsdl_old='http://demo-shop80.swissbilling.ch/wsdl/EShopRequestV2_80.wsdl'; // V2
                    // $this->url_wsdl='http://demo-shop.swissbilling.ch:8083/EShopRequest.wsdl'; // V1
                // Prod
                }else{
                    $this->url_wsdl='https://ws.swissbilling.ch/ws/EshopRequestV3.svc?WSDL'; // V3
                    $this->url_wsdl_old='https://secure.safebill.ch/EShopRequestV2StdSec.wsdl'; // V2
                    // $this->url_wsdl='https://secure.safebill.ch/EShopRequestStdSec.wsdl'; // V1
                }
                
                /* PS 1.6 */
                $this->bootstrap = true;
                $this->ps_version  = Tools::substr(_PS_VERSION_,0,3);

		parent::__construct();

		$this->displayName = 'Swissbilling';
		$this->description = $this->l('Accept payment by invoice (with payment guarantee)');
                
		$this->confirmUninstall = $this->l('Are you sure you ?');
		if ($this->merchant_id=='' OR $this->merchant_pw=='' OR $this->private_key=='')
			$this->warning = $this->l('Swissbilling the configuration has not been defined yet.');
	}

	public function install()
	{
                
                // ajout le produit de frais Swissbilling 
                $Product = new Product();
                $Product->name[$this->context->cookie->id_lang] = $this->l('Costs Swissbilling');
                $Product->link_rewrite[$this->context->cookie->id_lang] = 'Swissbilling';
                $Product->reference = $this->ref_product;
                $Product->active = 0;
                $Product->is_virtual = 1;
                $Product->id_tax_rules_group = 0;
                $Product->add();
                
                // installe directement sur toutes les boutiques (si multi-shop)
                $shops = Shop::getShops();
                foreach($shops as $shop){
                    
                    Configuration::updateValue('SWSBLG_MAX_AMOUNT','850',false,$shop['id_shop_group'],$shop['id_shop']);
                    Configuration::updateValue('SWSBLG_ADMIN_FEE_AMOUNT','0',false,$shop['id_shop_group'],$shop['id_shop']);
                    Configuration::updateValue('SWSBLG_PRE_SCREENING','0',false,$shop['id_shop_group'],$shop['id_shop']);
                    
                    $id_order_state = Configuration::get('SWSBLG_ID_ORDER_STATE',null,$shop['id_shop_group'],$shop['id_shop']);
                    if(empty($id_order_state)){
                        
                        // ajout d'un nouveau message de statut pour Swissbilling
                        $OrderState = new OrderState();
                        $languages = Language::getLanguages();
                        foreach($languages as $l){
                            if($l['iso_code']=='de'){
                                $state_lang = 'Zahlung per Rechnung (über Swissbilling)';
                            }elseif($l['iso_code']=='en'){
                                $state_lang = 'Payment by invoice (via Swissbilling)';
                            }elseif($l['iso_code']=='it'){    
                                $state_lang = 'Il pagamento tramite fattura (via Swissbilling)';
                            }else{
                                $state_lang = 'Paiement par facture (via Swissbilling)';
                            }
                            $OrderState->name[$l['id_lang']] = $state_lang;
                        }
                        $OrderState->invoice = 0;
                        $OrderState->send_email = 0;
                        $OrderState->color = '#32CD32';
                        $OrderState->unremovable = 0;
                        $OrderState->hidden = 0;
                        $OrderState->logable = 1;
                        $OrderState->delivery = 0;
                        $OrderState->paid = 1;
                        $OrderState->add();

                        $id_order_state_last = Db::getInstance()->getValue('SELECT max(id_order_state) AS max FROM '._DB_PREFIX_.'order_state');
                        @copy(dirname(__FILE__).'/logo.gif',_PS_IMG_DIR_.'os/'.$id_order_state_last.'.gif'); 
                        
                        Configuration::updateValue('SWSBLG_ID_ORDER_STATE',$id_order_state_last,false,$shop['id_shop_group'],$shop['id_shop']);
                    }
                }
                
                if(!parent::install() OR !$this->registerHook('payment') OR !$this->registerHook('paymentReturn')){
                    return false;
                }else{
                    return true;
                }
	}

	public function uninstall()
	{
                
                // processus exécuté uniquement sur  la boutique courante
                // désinstalle le module
		if(!parent::uninstall())
                    return false;
                
                // supprime le produit de frais Swissbilling 
                $id_product = Db::getInstance()->getValue('
                SELECT `id_product`
                FROM `'._DB_PREFIX_.'product` p
                WHERE p.`reference` = "'.$this->ref_product.'"');
                $Product = new Product($id_product);
                $Product->delete();
                
                // retire la configuration
                if(!empty($this->context->shop->id) && Shop::isFeatureActive()){
                    $id_shop = $this->context->shop->id;
                    $id_shop_group = $this->context->shop->id_shop_group;
                    $AND = 'AND `id_shop`="'.pSQL($id_shop);    
                }else{
                    $id_shop = 'NULL';
                    $id_shop_group = 'NULL';
                    $AND = '';
                }
                Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'configuration` WHERE `name` LIKE "%SWSBLG_%" '.$AND);
                
                // supprime les messages personnalisés
                $languages = Language::getLanguages(false);
                foreach($languages as $language){
                    $conf_name = 'SWSBLG_INFO_PREPMT_'.$language['id_lang'];
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'configuration` WHERE `name` LIKE "%'.pSQL($conf_name).'%" '.$AND);
                }
                      
                // supprime le logo de paiement + status commande
                $id_order_state = Configuration::get('SWSBLG_ID_ORDER_STATE',null,$id_shop_group,$id_shop);
                @unlink(_PS_IMG_DIR_.'os/'.$id_order_state.'.gif');
                Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'order_state` WHERE `id_order_state`="'.$id_order_state.'"');
                Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'order_state_lang` WHERE `id_order_state`="'.$id_order_state.'"');
                
		return true;
	}

	private function _postValidation()
	{
		if (Tools::isSubmit('btnSubmitConfig'))
		{
			if (!Tools::getValue('merchant_id'))
				$this->_postErrors[] = $this->l('The id of the merchant is required.');
			elseif (!Tools::getValue('merchant_pw'))
				$this->_postErrors[] = $this->l('The password is required.');
                        elseif (!Tools::getValue('private_key'))
				$this->_postErrors[] = $this->l('The private key is required.');
                        elseif (!Tools::getValue('max_amount'))
				$this->_postErrors[] = $this->l('The maximum amount is required.');
		}
	}

	private function _postProcess()
	{
		if(Tools::isSubmit('btnSubmitConfig')){
                    Configuration::updateValue('SWSBLG_MERCHANT_ID', Tools::getValue('merchant_id'));
                    Configuration::updateValue('SWSBLG_MERCHANT_PW', Tools::getValue('merchant_pw'));
                    Configuration::updateValue('SWSBLG_PRIVATE_KEY', Tools::getValue('private_key'));
                    Configuration::updateValue('SWSBLG_MAX_AMOUNT', Tools::getValue('max_amount'));
                    Configuration::updateValue('SWSBLG_PRE_SCREENING', Tools::getValue('pre_screening'));
                    Configuration::updateValue('SWSBLG_B2B', Tools::getValue('b2b'));
                    Configuration::updateValue('SWSBLG_TYPE', Tools::getValue('type'));
                    Configuration::updateValue('SWSBLG_AUTO_VALIDATION', Tools::getValue('auto_validation'));
                    Configuration::updateValue('SWSBLG_DELIVERY_STATUS', Tools::getValue('delivery_status'));
                    Configuration::updateValue('SWSBLG_ADMIN_FEE_AMOUNT', Tools::getValue('admin_fee_amount'));
		}

                if(Tools::isSubmit('btnSubmitParams')){
                    $languages = Language::getLanguages(false);
                    foreach($languages as $language){
                        Configuration::updateValue('SWSBLG_INFO_PREPMT_'.$language['id_lang'],Tools::getValue('body_info_prepmt_'.$language['id_lang']),true);
                    }
                }

		$this->_html .= $this->displayConfirmation($this->l('The changes were saved.'));
	}

	private function _displaySwissbilling()
	{
		$this->_html .= '<b>'.$this->l('This module allows you to accept payment by invoice (payment guaranteed by Swissbilling).').'</b><br />';
	}

	private function _displayForm()
	{       
   
                $cookie = $this->context->cookie;
            
                if(extension_loaded('soap')){$soap = true;}else{$soap = false;}
                if(extension_loaded('openssl')){$openssl = true;}else{$openssl = false;}
                if(Configuration::get('SWSBLG_PRE_SCREENING')=='0'){$selected_ps_no='selected';}else{$selected_ps_no='';}
                if(Configuration::get('SWSBLG_PRE_SCREENING')=='1'){$selected_ps_yes='selected';}else{$selected_ps_yes='';}  
                if(Configuration::get('SWSBLG_B2B')=='0'){$selected_b2b_no='selected';}else{$selected_b2b_no='';}
                if(Configuration::get('SWSBLG_B2B')=='1'){$selected_b2b_yes='selected';}else{$selected_b2b_yes='';}
                if(Configuration::get('SWSBLG_TYPE')=='Test'){$selected_tp_test='selected';}else{$selected_tp_test='';}
                if(Configuration::get('SWSBLG_TYPE')=='Real'){$selected_tp_real='selected';}else{$selected_tp_real='';}
                if(Configuration::get('SWSBLG_AUTO_VALIDATION')!=1){$selected_auto_validation_no='selected';$selected_auto_validation_yes='';}else{$selected_auto_validation_no='';$selected_auto_validation_yes='selected';}
                if(Configuration::get('SWSBLG_DELIVERY_STATUS')=='pending'){$selected_pending='selected';}else{$selected_pending='';}
                if(Configuration::get('SWSBLG_DELIVERY_STATUS')=='sent'){$selected_sent='selected';}else{$selected_sent='';}
                if(Configuration::get('SWSBLG_DELIVERY_STATUS')=='distributed'){$selected_distribued='selected';}else{$selected_distribued='';}

                $ad = dirname($_SERVER["PHP_SELF"]);
                $iso = Language::getIsoById((int)($cookie->id_lang));
		$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
                $defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages(false);            
                $info_prepmt = array();
                foreach ($languages as $language){
                    $info_prepmt[$language['id_lang']] = Configuration::get('SWSBLG_INFO_PREPMT_'.$language['id_lang']);
                }
		$divLangName = 'info_prepmt';
                
                $this->context->smarty->assign(array(
                    'soap' => $soap,
                    'openssl' => $openssl,
                    '_path' => $this->_path,
                    'merchant_id' => htmlentities(Tools::getValue('merchant_id',$this->merchant_id), ENT_COMPAT, 'UTF-8'),
                    'merchant_pw' => htmlentities(Tools::getValue('merchant_pw',$this->merchant_pw), ENT_COMPAT, 'UTF-8'),
                    'private_key' => htmlentities(Tools::getValue('private_key',$this->private_key), ENT_COMPAT, 'UTF-8'),
                    'max_amount' => htmlentities(Tools::getValue('max_amount',$this->max_amount), ENT_COMPAT, 'UTF-8'),
                    'selected_ps_yes' => $selected_ps_yes,
                    'selected_ps_no' => $selected_ps_no,
                    'selected_b2b_yes' => $selected_b2b_yes,
                    'selected_b2b_no' => $selected_b2b_no,
                    'selected_tp_test' => $selected_tp_test,
                    'selected_tp_real' => $selected_tp_real,
                    'selected_auto_validation_no' => $selected_auto_validation_no,
                    'selected_auto_validation_yes' => $selected_auto_validation_yes,
                    'selected_pending' => $selected_pending,
                    'selected_sent' => $selected_sent,
                    'selected_distribued' => $selected_distribued,
                    'admin_fee_amount' => Configuration::get('SWSBLG_ADMIN_FEE_AMOUNT'),
                    'ad' => $ad,
                    'iso' => $iso,
                    'isoTinyMCE' => $isoTinyMCE,
                    'ps_base_uri' => __PS_BASE_URI__,
                    'theme_css_dir' => _THEME_CSS_DIR_,
                    'defaultLanguage' => $defaultLanguage,
                    'displayName' => $this->displayName,
                    'languages' => $languages,
                    'divLangName' => $divLangName,
                    'info_prepmt' => $info_prepmt,
                    'displayFlags' => $this->displayFlags($languages, $defaultLanguage, $divLangName,'info_prepmt',true),
                    'link_logs'=> 'index.php?tab=AdminLogs&token='.Tools::getAdminToken('AdminLogs'.Tab::getIdFromClassName('AdminLogs').$cookie->id_employee),
                ));
            
                $this->_html .= $this->context->smarty->fetch(dirname(__FILE__).'/views/templates/admin/page.tpl');  
	}

	public function getContent()
	{
            
                if($this->ps_version=='1.6'){
                    $this->_html .= '<link rel="stylesheet" type="text/css" href="'.$this->_path.'views/css/styles_1.6.css">';
                }
            
		$this->_html .= '
                <div id="div_bo_swissbilling" class="panel">
                    <h2>'.$this->displayName.'</h2>';

                    if (Tools::isSubmit('btnSubmitConfig') || Tools::isSubmit('btnSubmitParams'))
                    {
                            $this->_postValidation();
                            if (!sizeof($this->_postErrors))
                                    $this->_postProcess();
                            else
                                    foreach ($this->_postErrors AS $err)
                                            $this->_html .= $this->displayError($err);
                    }
                    else
                            $this->_html .= '<br />';

                    $this->_displaySwissbilling();
                    $this->_displayForm();
                $this->_html.='
                </div>';
		return $this->_html;
	}

	public function hookPayment($params)
	{
		if (!$this->active)
			return ;
		if (!$this->_checkCurrency($params['cart']))
			return ;

		$smarty = $this->context->smarty;
                $cookie = $this->context->cookie;

                // on affiche la méthode uniqument si le montant total ne dépasse par là règle Swissbilling
                $Cart = new Cart($cookie->id_cart);
                $total_order = (float)($Cart->getOrderTotal(true,Cart::BOTH));
                $max_amount = Configuration::get('SWSBLG_MAX_AMOUNT');
                if($total_order<=$max_amount){
                    
                    $smarty->assign(array(
                        'this_path' => $this->_path,
                        'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/',
                        'ps_version' => $this->ps_version,
                    ));
              
                    //BEGIN Ajout de frais de paiement 5CHF
                    $products = $Cart->getProducts();
                    $frai_virement = (int) Configuration::get('TUNNELVENTE_ID_FRAI_VIREMENT');
                    $find_cost = false;

                    foreach($products as $p){
                        if($p['id_product']==$frai_virement){$find_cost=true;}
                    }

                    if(!$find_cost){
                        $Product = new Product($frai_virement,false,  $this->context->language->id);
                        $Cart->updateQty(1,$Product->id); 
                    }  
                    //END
                    
                    // avec pré-screening 
                    $swissbilling = new Swissbilling();
                   if($swissbilling->pre_screening){
                       
                        // contrôle la solvabilité du client avant la méthode de paieemtn 
                        try{

                            // paramètres généraux marchand
                            $merchant = array(
                                'id'            => $swissbilling->merchant_id,
                                'pwd'           => $swissbilling->merchant_pw,
                                'success_url'   => $swissbilling->success_url,
                                'cancel_url'    => $swissbilling->cancel_url,
                                'error_url'     => $swissbilling->error_url,
                            );

                            // 1. liste des produits
                            $items = $swissbilling->getItems();
                            // 2. transaction
                            $transaction = $swissbilling->getTransaction();
                            // 3. débiteur
                            $debtor = $swissbilling->getDebtor();

                            //p($items);
                            //p($transaction);
                            //p($debtor);

                            $SoapClient = new SoapClient($swissbilling->url_wsdl,array('trace'=>0,'exceptions'=>1));
                            $parameters = array('merchant'=>$merchant,'transaction'=>$transaction,'debtor'=>$debtor,'item_count'=>count($items),'arrayofitems'=>$items);
                            $status = $SoapClient->EshopTransactionPreScreening($parameters);

                            // en cas d'acceptation on affiche la méthode Swissbilling
                            if($status->EshopTransactionPreScreeningResult->status=='Answered'){return $this->display(__FILE__, 'payment.tpl');}

                        }catch(SoapFault $exception){
                             // conservation du Log dans le back-office
                             $message = $exception->getMessage();
                             echo $message;
                             Logger::addLog(pSQL($message,true),1,2,'Swissbilling',$cookie->id_cart);
                        }
                    
                // sans pre-screening
                }else{
                    return $this->display(__FILE__, 'payment.tpl'); 
                } 
            }
	}

	public function hookPaymentReturn($params)
	{
		if (!$this->active)
			return ;

		$smarty = $this->context->smarty;

		$state = $params['objOrder']->getCurrentState();     
                $id_order_state = Configuration::get('SWSBLG_ID_ORDER_STATE',null,$this->context->shop->id_shop_group,$this->context->shop->id);
                
                if(in_array($state,array($id_order_state,Configuration::get('PS_OS_OUTOFSTOCK'),Configuration::get('PS_OS_OUTOFSTOCK_UNPAID')))){
                    $smarty->assign(array(
                        'total_to_pay' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
                        'status' => 'ok',
                        'id_order' => $params['objOrder']->id
                    ));       
                }else{
                    $smarty->assign('status', 'failed');
                }
                
                if(version_compare(_PS_VERSION_, '1.6.0', '>=')){
                    return $this->display(__FILE__, 'payment_return.tpl');
                }else{
                    return $this->display(__FILE__, 'views/templates/front/payment_return.tpl');
                }  
	}
        
        /*
         * Liste des produits attendus par Swissbilling
         * @param -
         * @return array
         */
        public function getItems(){
            
            $Cart = new Cart($this->context->cart->id);
            $products = $Cart->getProducts();     
            $items = array();
            
            foreach($products as $p){
                
                // création d'un détail de commande
                $Product = new Product($p['id_product'],null,$this->context->cookie->id_lang);
                $Link = new Link();

                // produits
                if(isset($p['attributes'])){$attributes=$p['attributes'];}else{$attributes='';}
                if($p['rate']==NULL){$tax_rate=0;}else{$tax_rate=$p['rate'];}
                $item  = array('short_desc'    => $p['name'],//.' '.$attributes,
                               'desc'          => $p['name'],//.' '.$attributes,
                               'quantity'      => $p['cart_quantity'],
                               'unit_price'    => Tools::ps_round($p['price_wt'],2),
                               'VAT_rate'      => $tax_rate,
                               'VAT_amount'    => Tools::ps_round($p['price'],2)*$tax_rate/100,
                               'file_link'     => $Link->getProductLink($Product),
                         );
                $items[] = $item;
                // utilisé pour calculer la réduction total pour les bons de réductions
                $this->total_products = $this->total_products+$p['total'];
                // vérifie si un des produits est téléchargeable
                $p_download = Db::getInstance()->getRow('SELECT `id_product_download`
                                                         FROM `'._DB_PREFIX_.'product_download`
                                                         WHERE `id_product`="'.pSQL($p['id_product']).'"
                                                         AND `active`="1"');
                if(!empty($p_download)){$this->phys_delivery=0;}
            }
            return $items;
            
        }
        
        /*
         * Détail transaction attendue par Swissbilling
         * @param -
         * @return array
         */
        public function getTransaction(){
             
            $Currency = new Currency($this->context->cookie->id_currency);
            $Cart = new Cart($this->context->cart->id);
            $total_tax = Tools::ps_round($Cart->getOrderTotal()-$Cart->getOrderTotal(false),2);

            // total taxes transporteur
            $total_tax_carrier = Tools::ps_round(@$Cart->getOrderShippingCost($Cart->id_carrier)-@$Cart->getOrderShippingCost($Cart->id_carrier,false),2);
            $total_tax=$total_tax+$total_tax_carrier;

            // frais manutention
            $Carrier = new Carrier($Cart->id_carrier);
            if($Carrier->shipping_handling){
                $shipping_handling = Configuration::get('PS_SHIPPING_HANDLING');
            }else{
                $shipping_handling=0;
            }

            // bon de reductions
            $total_discount=0;
            $discounts = @$Cart->getDiscounts($Cart->id);
            // plusieurs bons ?
            foreach($discounts as $d){
                $Discount = new Discount($d['id_discount']);
                if($total_tax>0){$use_tax=true;}else{$use_tax=false;}
                $total_discount=Tools::ps_round($total_discount+$Discount->getValue(1,$this->total_products,$shipping_handling,$Cart->id,$use_tax),2);
            }

            // Modification de l'IP local
            if($_SERVER['REMOTE_ADDR']=='127.0.0.1'){$debitor_IP='92.106.68.184';}else{$debitor_IP=$_SERVER['REMOTE_ADDR'];}
            
            $total_order = (float)($Cart->getOrderTotal(true, Cart::BOTH));
            $order_timestamp = date('c');
            
            // conservation dans le cookie pour le retour de page
            $this->context->cookie->order_timestamp = $order_timestamp;
            $this->context->cookie->transaction_ref = $this->context->cookie->id_cart;
            
            // B2C ou B2B
            if($this->b2b == 1){
                $AddressInvoice = new Address($Cart->id_address_invoice,$this->context->cookie->id_lang);       
                if(!empty($AddressInvoice->company)){
                    $this->b2b = 1; // B2B
                }else{
                    $this->b2b = 0; // B2C
                }
            }else{
                $this->b2b = 0; // B2C
            }
            
            $transaction = array(
                                'type'                      => 'Real', // 09.04.14 - exigé par Swissbilling
                                'is_B2B'                    => $this->b2b,
                                'eshop_ID'                  => $this->merchant_id, //constant set in payment method config
                                'eshop_ref'                 => $Cart->id, //Order number
                                'order_timestamp'           => $order_timestamp,
                                'currency'                  => $Currency->iso_code, //multi currency, can be chosen from the payment method config
                                'amount'                    => $total_order+Configuration::get('SWSBLG_ADMIN_FEE_AMOUNT'), // Order amount
                                'VAT_amount'                => $total_tax,
                                'admin_fee_amount'          => Configuration::get('SWSBLG_ADMIN_FEE_AMOUNT')+($Cart->gift)?$this->round5ct($Cart->getGiftWrappingPrice()):0,
                                'delivery_fee_amount'       => @$Cart->getOrderShippingCost($Cart->id_carrier),
                                'coupon_discount_amount'    => $total_discount,
                                'vol_discount'              => 0,
                                'phys_delivery'             => $this->phys_delivery, //virtual product == false
                                'delivery_status'           => Configuration::get('SWSBLG_DELIVERY_STATUS'),
                                'debtor_IP'                 => $debitor_IP,
                                'signature'                 => sha1($this->merchant_id.$Cart->id.$total_order.$this->private_key) //public is a string set in payment method config
                                );

            return $transaction;
            
        }
        
        /*
         * Détail débiteur attendu par Swissbilling
         * @param -
         * @return array
         */
        public function getDebtor(){
            
            $Cart = new Cart($this->context->cart->id);
            $Language = new Language($this->context->cookie->id_lang);
            $Customer = new Customer($this->context->cookie->id_customer);
            
            $AddressInvoice = new Address($Cart->id_address_invoice,$this->context->cookie->id_lang);
            $CountryInvoice = new Country($AddressInvoice->id_country);
            
            $AddressDelivery = new Address($Cart->id_address_delivery,$this->context->cookie->id_lang);
            $CountryDelivery = new Country($AddressDelivery->id_country);

            if($Language->iso_code=='de'){
                $gender_h = 'Herr';
                $gender_f = 'Frau';
            }elseif($Language->iso_code=='en'){
                $gender_h = 'Mr.';
                $gender_f = 'Mrs.';
            }elseif($Language->iso_code=='it'){    
                $gender_h = 'Mr.';
                $gender_f = 'Mrs.';
            }else{
                $gender_h = 'Monsieur';
                $gender_f = 'Madame';
            }

            if($Customer->id_gender==1){$gender=$gender_h;}elseif($Customer->id_gender==2){$gender=$gender_f;}else{$gender='';}
            if(!empty($AddressInvoice->phone)){$phone=$AddressInvoice->phone;}else{$phone=$AddressInvoice->phone_mobile;}

            $debtor = array(
                            'company_name'  => Tools::ucfirst($AddressInvoice->company),
                            'title'         => Tools::ucfirst($gender),
                            'firstname'     => Tools::ucfirst($AddressInvoice->firstname),
                            'lastname'      => Tools::ucfirst($AddressInvoice->lastname),
                            'birthdate'     => (($Customer->birthday!=='0000-00-00')?$Customer->birthday:'1900-01-01'),
                            'adr1'          => Tools::ucfirst($AddressInvoice->address1),
                            'adr2'          => Tools::ucfirst($AddressInvoice->address2),
                            'city'          => Tools::ucfirst($AddressInvoice->city),
                            'zip'           => $AddressInvoice->postcode,
                            'country'       => Tools::strtoupper($CountryInvoice->iso_code),
                            'email'         => $Customer->email,
                            'phone'         => $phone,
                            'language'      => (in_array(Tools::strtoupper($Language->iso_code),$this->langs_interface)?Tools::strtoupper($Language->iso_code):$this->lang_interface_default),
                            'user_ID'       => $this->context->cookie->id_customer,
                            'is_SBMember'   => false,
                            'SBMember_ID'   => '',
                            // adresse livraison (depuis API V3)
                            'deliv_company_name'    => Tools::ucfirst($AddressDelivery->company), 
                            'deliv_title'           => (($Cart->id_address_invoice==$Cart->id_address_delivery)?Tools::ucfirst($gender):''),
                            'deliv_firstname'       => Tools::ucfirst($AddressDelivery->firstname),
                            'deliv_lastname'        => Tools::ucfirst($AddressDelivery->lastname),
                            'deliv_adr1'            => Tools::ucfirst($AddressDelivery->address1),
                            'deliv_adr2'            => Tools::ucfirst($AddressDelivery->address2),
                            'deliv_street_nr'       => '',
                            'deliv_city'            => Tools::ucfirst($AddressDelivery->city),
                            'deliv_zip'             => $AddressDelivery->postcode,
                            'deliv_country'         => Tools::strtoupper($CountryDelivery->iso_code),
                           );
            
            return $debtor;
            
        }
	
	private function _checkCurrency($cart)
	{
		$currency_order = new Currency((int)($cart->id_currency));
		$currencies_module = $this->getCurrency((int)$cart->id_currency);
		
		if (is_array($currencies_module))
			foreach ($currencies_module AS $currency_module)
				if ($currency_order->id == $currency_module['id_currency'])
					return true;
		return false;
	}
   
        /*
         * Nettoie la chaine
         * @param string (chaine)
         * @return string
         */
        public function clean($string){
            //$string = Tools::htmlentitiesDecodeUTF8($string);
            $string = html_entity_decode($string);
            $string = strip_tags($string);
            $string = str_replace(CHR(13).CHR(10),"",$string); // enlève les retours chariot
            $string = preg_replace('/<br\\s*?\/??>/i','', $string);
            $string = trim($string);
            $string = trim($string);
            $string = str_replace("\r",'',$string);
            $string = str_replace("\n",'',$string);
            return $string;
        }
        
        /* for controller - payment.php */
        public function checkCurrency($cart){
            $currency_order = new Currency($cart->id_currency);
            $currencies_module = $this->getCurrency($cart->id_currency);

            if (is_array($currencies_module))
                    foreach ($currencies_module as $currency_module)
                            if ($currency_order->id == $currency_module['id_currency'])
                                    return true;
            return false;
	}
        
        /*
         * Crée un template selon le standard Prestashop
         * @param string ($subject)
         * @param html ($message)
         * @return html
         */
        public function generateMailTemplate($subject,$message){
            
            $shop_name = Configuration::get('PS_SHOP_NAME');
            $ShopUrl = new ShopUrl($this->context->shop->id);
            $url_logo = $ShopUrl->getUrl().'img/'.Configuration::get('PS_LOGO');
            
            $html = '
            <!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>'.$subject.'</title>
            </head>
            <body style="width:650px;margin:auto;">
            <table class="table table-mail" style="width:100%;margin-top:10px;-moz-box-shadow:0 0 5px #afafaf;-webkit-box-shadow:0 0 5px #afafaf;-o-box-shadow:0 0 5px #afafaf;box-shadow:0 0 5px #afafaf;filter:progid:DXImageTransform.Microsoft.Shadow(color=#afafaf,Direction=134,Strength=5)">
                <tr>
                    <td class="space" style="width:20px;padding:7px 0">&nbsp;</td>
                    <td align="center" style="padding:7px 0">
                        <table class="table" bgcolor="#ffffff" style="width:100%">
                                <tr>
                                    <td align="center" class="logo" style="border-bottom:4px solid #333333;padding:7px 0">
                                        <a title="'.$shop_name.'" href="'.$ShopUrl->getUrl().'" style="color:#337ff1">
                                            <img src="'.$url_logo.'" alt="'.$shop_name.'" />
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" class="titleblock" style="padding:7px 0">
                                        <font size="2" face="Open-sans, sans-serif" color="#555454">
                                            <span class="title" style="font-weight:500;font-size:28px;text-transform:uppercase;line-height:33px">'.$subject.'</span><br/>
                                        </font>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="space_footer" style="padding:0!important">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">
                                        <table class="table" style="width:100%">
                                            <tr>
                                                <td width="10" style="padding:7px 0">&nbsp;</td>
                                                <td style="padding:7px 0">
                                                    <font size="2" face="Open-sans, sans-serif" color="#555454">
                                                            '.$message.'
                                                    </font>
                                                </td>
                                                <td width="10" style="padding:7px 0">&nbsp;</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="space_footer" style="padding:0!important">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="footer" style="border-top:4px solid #333333;padding:7px 0">
                                        <span><a href="'.$ShopUrl->getUrl().'" style="color:#337ff1">'.$shop_name.'</a> </span>
                                    </td>
                                </tr>
                            </table>
                    </td>
                    <td class="space" style="width:20px;padding:7px 0">&nbsp;</td>
                </tr>
            </table>
            </body>
            </html>';
            return $html;
        }
        
       /*
        * Envoie un email
        * @param string ($subject)
        * @param html ($message)
        * @param string ($to)
        * @param string ($from)
        * @param string ($from_name)
        * @return -
        */
       public function MailSend($subject,$message,$to,$from=null,$from_name=null){

           if(Validate::isEmail($to)){ 

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

               // envoi via SMTP
               try{
                   if (Configuration::get('PS_MAIL_METHOD')== 2){
                       // var
                       $smtpServer = Configuration::get('PS_MAIL_SERVER');
                       $smtpPort = Configuration::get('PS_MAIL_SMTP_PORT');
                       $smtpEncryption = Configuration::get('PS_MAIL_SMTP_ENCRYPTION');
                       $smtpLogin = Configuration::get('PS_MAIL_USER');
                       $smtpPassword = Configuration::get('PS_MAIL_PASSWD');
                       // config
                       // V1
                       if(Tools::version_compare(_PS_VERSION_, '1.6.1.5','<')){
                           $smtp = new Swift_Connection_SMTP($smtpServer, $smtpPort, ($smtpEncryption == 'off') ? 
                           Swift_Connection_SMTP::ENC_OFF : (($smtpEncryption == 'tls') ? Swift_Connection_SMTP::ENC_TLS : Swift_Connection_SMTP::ENC_SSL));
                           $smtp->setUsername($smtpLogin);
                           $smtp->setpassword($smtpPassword);
                           $smtp->setTimeout(5);
                           $swift = new Swift($smtp, Configuration::get('PS_MAIL_DOMAIN'));
                       // V2
                       }else{
                           if(Tools::strtolower($smtpEncryption)==='off'){$smtpEncryption = false;}
                           $connection = Swift_SmtpTransport::newInstance($smtpServer,$smtpPort,$smtpEncryption)
                                         ->setUsername($smtpLogin)
                                         ->setPassword($smtpPassword);
                       }
                   // envoi Mail()
                   }else{
                        // V1
                       if(Tools::version_compare(_PS_VERSION_, '1.6.1.5','<')){
                           $connection = new Swift_Connection_NativeMail();
                           $swift = new Swift($connection);
                       // V2
                       }else{
                           $connection = Swift_MailTransport::newInstance();
                       }
                   }

                   if(empty($from)){$from=Configuration::get('PS_SHOP_EMAIL');}
                   if(empty($from_name)){$from_name=Configuration::get('PS_SHOP_NAME');}

                   // V1 
                   if(Tools::version_compare(_PS_VERSION_, '1.6.1.5','<')){
                       $message = new Swift_Message($subject,$message,'text/html');
                       $swift->send($message,$to,new Swift_Address($from,$from_name));
                       $swift->disconnect();
                   // V2
                   }else{
                       $swift = Swift_Mailer::newInstance($connection);
                       $message = new Swift_Message($subject,$message,'text/html');  
                       $message->setFrom(array($from=>$from_name));
                       $message->setTo($to);                  
                       $swift->send($message);
                   }

               }catch(Swift_ConnectionException $e){
                   ob_clean();
                   Tools::d($e->getMessage());
               }catch(Swift_Message_MimeException $e){
                   ob_clean();
                   Tools::d($e->getMessage());
               }
           }       
        }
        
       /*
        * Arrondi à 5ct
        * @param float ($amount)
        * @return -
        */
        public function round5ct($amount){
            return round(20*$amount)/20;  
        }  
        
}
?>

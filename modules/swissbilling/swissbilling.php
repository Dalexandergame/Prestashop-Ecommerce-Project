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
 V17.2.03 - 17.04.20
 - optimisation du comportement Pré-Screening
 V17.2.02 - 10.10.18
 - ajustements sur traductions en allemand
 V17.2.01 - 29.08.18
 - actualisation des urls API Swissbilling
 V17.1.9 - 07.05.18
 - ajustement du HTTPS pour les urls API test
 V17.1.8 - 03.04.18
 - intégration hachage _COOKIE_IV_
 V17.1.7 - 30.01.18
 - ajustement des logos
 V17.1.6 - 20.12.17
 - cron.php / ajustement des logs + contrôle $Response->status
 V17.1.5 - 15.12.17
 - intégration du logo Swissbilling dans hookPaymentOptions
 V17.1.4 - 12.12.17
 - amélioration de la gestion des frais (administration / client)
 V17.1.3 - 07.11.17
 - traitement des transactions sur un délai de -30 jours
 - optmisation des d() et p() en dump()
 - stock le log du mail en cas d'erreur CRON
 V17.1.2 - 05.09.17
 - optimisations Prestashop Addons
 V17.1.1 - 04.09.17
 - correction d() / dump() sur success.php
 V17.1.0 - 25.07.17
 - intégration compatibilité Prestashop 1.7
 V1.12.01 - 05.06.17
 - ajout mode intégré EshopTransactionDirect()
 V1.11.01 - 28.03.17
 - ajout option impression du PDF en back-office
 V1.10.04 - 14.12.16
 - par défaut désactiver l'injection des frais
 V1.10.03 - 09.12.16
 - correction lien autres méthodes (error.tpl)
 V1.10.02 - 07.12.16
 - correction sélection SQL frais Swissbilling (multi-boutiques)
 V1.10.01 - 31.10.16
 - intégration du système de validation automatique des transactions
 - validation en différé selon nombre de jours / via tâche planifée
 - majorer la différence des frais suite à une réduction globale en %
 V1.9.18 - 12.10.16
 - correction SQL à la désinstallation
 - success.php contrôle existance produit de frais Swissbilling
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

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

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
        $this->version = '17.2.03';
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
            $this->url_wsdl='https://ws-pp.swissbilling.ch/ws/EshopRequestV3.svc?WSDL'; // V3
            $this->url_wsdl_old='https://sr-pp.swissbilling.ch/EShopRequestV2StdSec.wsdl'; // V2
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

        // crée la table des transactions
        Db::getInstance()->Execute('
        CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'swissbilling` (
            `id_swissbilling` int(11) NOT NULL AUTO_INCREMENT,
            `id_cart` int(11) NOT NULL,
            `timestamp` varchar(64) NOT NULL,
            `validate` datetime NOT NULL,
            `error` tinyint(1) NOT NULL,
            PRIMARY KEY (`id_swissbilling`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;');

        // ajout le produit de frais Swissbilling 
        $Product = new Product();
        $Product->name[Configuration::get('PS_LANG_DEFAULT')] = $this->l('Costs Swissbilling');
        $Product->link_rewrite[Configuration::get('PS_LANG_DEFAULT')] = 'Swissbilling';
        $Product->reference = $this->ref_product;
        $Product->active = 1;
        $Product->visibility = 'none';
        $Product->is_virtual = 1;
        $Product->id_tax_rules_group = 0;
        $Product->add();

        // installe directement sur toutes les boutiques (si multi-shop)
        $shops = Shop::getShops();
        foreach($shops as $shop){

            Configuration::updateValue('SWSBLG_IMPRESSION_TYPE','Post',false,$shop['id_shop_group'],$shop['id_shop']);
            Configuration::updateValue('SWSBLG_MAX_AMOUNT','850',false,$shop['id_shop_group'],$shop['id_shop']);
            Configuration::updateValue('SWSBLG_ADMIN_FEE_AMOUNT','0',false,$shop['id_shop_group'],$shop['id_shop']);
            Configuration::updateValue('SWSBLG_CONF_MAIL','1',false,$shop['id_shop_group'],$shop['id_shop']);
            Configuration::updateValue('SWSBLG_COSTS_ORDER','0');
            Configuration::updateValue('SWSBLG_REDIRECTION','1');
            Configuration::updateValue('SWSBLG_PRE_SCREENING','0',false,$shop['id_shop_group'],$shop['id_shop']);         
            Configuration::updateValue('SWSBLG_AUTO_VALIDATION','0',false,$shop['id_shop_group'],$shop['id_shop']);
            $ios = Db::getInstance()->getRow('SELECT `id_order_state`
                                       FROM `'._DB_PREFIX_.'order_state`
                                       WHERE `paid`="1"
                                       ORDER BY `id_order_state` ASC');
            Configuration::updateValue('SWSBLG_ORDER_STATE_VALIDATION',$ios['id_order_state'],false,$shop['id_shop_group'],$shop['id_shop']);
            Configuration::updateValue('SWSBLG_NB_DAYS_VALIDATION',7,false,$shop['id_shop_group'],$shop['id_shop']);

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

        if(!parent::install() || 
           !$this->registerHook('payment') || 
           !$this->registerHook('paymentReturn') || 
           !$this->registerHook('displayAdminOrderTabOrder') ||
           !$this->registerHook('paymentOptions')
        ){
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

        // supprime la table des transactions
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'swissbilling`');

        // supprime le produit de frais Swissbilling 
        $id_product = Db::getInstance()->getValue('
        SELECT `id_product`
        FROM `'._DB_PREFIX_.'product` p
        WHERE p.`reference` = "'.pSQL($this->ref_product).'"');
        $Product = new Product($id_product);
        $Product->delete();

        // retire la configuration
        if(!empty($this->context->shop->id) && Shop::isFeatureActive()){
            $id_shop = $this->context->shop->id;
            $id_shop_group = $this->context->shop->id_shop_group;
            $AND = 'AND `id_shop`="'.pSQL($id_shop).'"';    
        }else{
            $id_shop = 'NULL';
            $id_shop_group = 'NULL';
            $AND = '';
        }
        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'configuration` WHERE `name` LIKE "%SWSBLG_%" '.$AND);

        // supprime le logo de paiement + status commande
        $id_order_state = Configuration::get('SWSBLG_ID_ORDER_STATE',null,$id_shop_group,$id_shop);
        @unlink(_PS_IMG_DIR_.'os/'.$id_order_state.'.gif');
        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'order_state` WHERE `id_order_state`="'.pSQL($id_order_state).'"');
        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'order_state_lang` WHERE `id_order_state`="'.pSQL($id_order_state).'"');

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
            elseif (!Tools::getValue('max_amount') || !Validate::isInt(Tools::getValue('max_amount')))
                    $this->_postErrors[] = $this->l('The maximum amount is required.');
            elseif (!empty(Tools::getValue('admin_fee_amount') && !Validate::isInt(Tools::getValue('admin_fee_amount'))))
                    $this->_postErrors[] = $this->l('The merchant administration fee amount is invalid.');
            elseif (!Validate::isInt(Tools::getValue('nb_days_validation')))
                    $this->_postErrors[] = $this->l('The number of days field is invalid.');
        }
    }

    private function _postProcess()
    {
        if(Tools::isSubmit('btnSubmitConfig')){
            Configuration::updateValue('SWSBLG_MERCHANT_ID', Tools::getValue('merchant_id'));
            Configuration::updateValue('SWSBLG_MERCHANT_PW', Tools::getValue('merchant_pw'));
            Configuration::updateValue('SWSBLG_PRIVATE_KEY', Tools::getValue('private_key'));
            Configuration::updateValue('SWSBLG_MAX_AMOUNT', Tools::getValue('max_amount'));
            Configuration::updateValue('SWSBLG_CONF_MAIL', Tools::getValue('conf_mail'));
            Configuration::updateValue('SWSBLG_REDIRECTION', Tools::getValue('redirection'));
            Configuration::updateValue('SWSBLG_COSTS_ORDER', Tools::getValue('costs_order'));
            Configuration::updateValue('SWSBLG_COSTS_ORDER_MODE', Tools::getValue('costs_order_mode'));
            Configuration::updateValue('SWSBLG_PRE_SCREENING', Tools::getValue('pre_screening'));
            Configuration::updateValue('SWSBLG_B2B', Tools::getValue('b2b'));
            Configuration::updateValue('SWSBLG_TYPE', Tools::getValue('type'));
            Configuration::updateValue('SWSBLG_GENERATE_PDF', Tools::getValue('generate_pdf'));
            Configuration::updateValue('SWSBLG_IMPRESSION_TYPE',Tools::getValue('impression_type'));
            Configuration::updateValue('SWSBLG_AUTO_VALIDATION', Tools::getValue('auto_validation'));
            Configuration::updateValue('SWSBLG_DELIVERY_STATUS', Tools::getValue('delivery_status'));
            Configuration::updateValue('SWSBLG_ADMIN_FEE_AMOUNT', Tools::getValue('admin_fee_amount'));
            Configuration::updateValue('SWSBLG_ORDER_STATE_VALIDATION', Tools::getValue('order_state_validation'));
            Configuration::updateValue('SWSBLG_NB_DAYS_VALIDATION', Tools::getValue('nb_days_validation'));
        }

        $this->_html .= $this->displayConfirmation($this->l('The changes were saved.'));
    }

    private function _displayForm()
    {       

        $cookie = $this->context->cookie;

        if(extension_loaded('soap')){$soap = true;}else{$soap = false;}
        if(extension_loaded('openssl')){$openssl = true;}else{$openssl = false;}
        if(Configuration::get('SWSBLG_PRE_SCREENING')=='0'){$selected_ps_no='selected';}else{$selected_ps_no='';}
        if(Configuration::get('SWSBLG_PRE_SCREENING')=='1'){$selected_ps_yes='selected';}else{$selected_ps_yes='';}  
        if(Configuration::get('SWSBLG_COSTS_ORDER')=='1'){$selected_costs_order_yes='selected';$selected_costs_order_no='';}else{$selected_costs_order_yes='';$selected_costs_order_no='selected';}  
        
        $selected_costs_order_0 = '';
        $selected_costs_order_1 = '';
        $selected_costs_order_2 = '';
        $cost_order_mode = Configuration::get('SWSBLG_COSTS_ORDER_MODE');
        if($cost_order_mode=='' || $cost_order_mode==0){
            $selected_costs_order_0 = 'selected="selected"';
        }elseif($cost_order_mode==1){
            $selected_costs_order_1 = 'selected="selected"';
        }elseif($cost_order_mode==2){
            $selected_costs_order_2 = 'selected="selected"';
        }    
        
        if(Configuration::get('SWSBLG_REDIRECTION')=='1'){$selected_redirection_yes='selected';$selected_redirection_no='';}else{$selected_redirection_yes='';$selected_redirection_no='selected';}  
        if(Configuration::get('SWSBLG_B2B')=='0'){$selected_b2b_no='selected';}else{$selected_b2b_no='';}
        if(Configuration::get('SWSBLG_B2B')=='1'){$selected_b2b_yes='selected';}else{$selected_b2b_yes='';}
        if(Configuration::get('SWSBLG_TYPE')=='Test'){$selected_tp_test='selected';}else{$selected_tp_test='';}
        if(Configuration::get('SWSBLG_TYPE')=='Real'){$selected_tp_real='selected';}else{$selected_tp_real='';}
        if(Configuration::get('SWSBLG_GENERATE_PDF')=='0'){
            $selected_gen_pdf_no='selected';
            $selected_gen_pdf_yes='';
        }else{
            $selected_gen_pdf_no='';
            $selected_gen_pdf_yes='selected';
        }  
        if(Configuration::get('SWSBLG_AUTO_VALIDATION')!=1){$selected_auto_validation_no='selected';$selected_auto_validation_yes='';}else{$selected_auto_validation_no='';$selected_auto_validation_yes='selected';}
        if(Configuration::get('SWSBLG_DELIVERY_STATUS')=='pending'){$selected_pending='selected';}else{$selected_pending='';}
        if(Configuration::get('SWSBLG_DELIVERY_STATUS')=='sent'){$selected_sent='selected';}else{$selected_sent='';}
        if(Configuration::get('SWSBLG_DELIVERY_STATUS')=='distributed'){$selected_distribued='selected';}else{$selected_distribued='';}

        $ad = dirname($_SERVER["PHP_SELF"]);
        $iso = Language::getIsoById((int)($cookie->id_lang));
        $isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
        $defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages(false);            

        // transactions stockées par Swissbilling
        $orders_swissbilling = array();
        $transac_swissbilling = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'swissbilling ORDER BY `id_swissbilling` DESC');
        foreach($transac_swissbilling as $o){
            $id_order = Order::getOrderByCartId($o['id_cart']);
            $Order = new Order($id_order);
            $orders_swissbilling[] = array(
                                          'id_cart' => $o['id_cart'],
                                          'Order' => $Order,
                                          'timestamp' => $o['timestamp'],
                                          'validate' => $o['validate'],
                                          'error' => $o['error'],
                                          );
        }

        $this->context->smarty->assign(array(
            'displayName' => $this->displayName,
            'module_desc' => $this->l('This module allows you to accept payment by invoice (payment guaranteed by Swissbilling).'), 
            'soap' => $soap,
            'openssl' => $openssl,
            '_path' => $this->_path,
            'merchant_id' => htmlentities(Tools::getValue('merchant_id',$this->merchant_id), ENT_COMPAT, 'UTF-8'),
            'merchant_pw' => htmlentities(Tools::getValue('merchant_pw',$this->merchant_pw), ENT_COMPAT, 'UTF-8'),
            'private_key' => htmlentities(Tools::getValue('private_key',$this->private_key), ENT_COMPAT, 'UTF-8'),
            'max_amount' => htmlentities(Tools::getValue('max_amount',$this->max_amount), ENT_COMPAT, 'UTF-8'),
            'conf_mail' => Configuration::get('SWSBLG_CONF_MAIL'),
            'selected_costs_order_yes' => $selected_costs_order_yes,
            'selected_costs_order_no' => $selected_costs_order_no,
            'selected_costs_order_0' => $selected_costs_order_0,
            'selected_costs_order_1' => $selected_costs_order_1,
            'selected_costs_order_2' => $selected_costs_order_2,
            'selected_redirection_yes' => $selected_redirection_yes,
            'selected_redirection_no' => $selected_redirection_no,
            'selected_ps_no' => $selected_ps_no,
            'selected_ps_yes' => $selected_ps_yes,
            'selected_ps_no' => $selected_ps_no,
            'selected_b2b_yes' => $selected_b2b_yes,
            'selected_b2b_no' => $selected_b2b_no,
            'selected_tp_test' => $selected_tp_test,
            'selected_tp_real' => $selected_tp_real,
            'selected_gen_pdf_no' => $selected_gen_pdf_no,
            'selected_gen_pdf_yes' => $selected_gen_pdf_yes,
            'impression_type' => Configuration::get('SWSBLG_IMPRESSION_TYPE'),
            'selected_auto_validation_no' => $selected_auto_validation_no,
            'selected_auto_validation_yes' => $selected_auto_validation_yes,
            'selected_pending' => $selected_pending,
            'selected_sent' => $selected_sent,
            'selected_distribued' => $selected_distribued,
            'admin_fee_amount' => Configuration::get('SWSBLG_ADMIN_FEE_AMOUNT'),
            'ad' => $ad,
            'iso' => $iso,
            'isoTinyMCE' => $isoTinyMCE,
            'ps_base_url' => Tools::getHttpHost(true),
            'ps_base_uri' => __PS_BASE_URI__,
            'theme_css_dir' => _THEME_CSS_DIR_,
            'defaultLanguage' => $defaultLanguage,
            'displayName' => $this->displayName,
            'languages' => $languages,
            'link_logs'=> 'index.php?tab=AdminLogs&token='.Tools::getAdminToken('AdminLogs'.Tab::getIdFromClassName('AdminLogs').$cookie->id_employee),
            'url_cron_validation' => Tools::getHttpHost(true).__PS_BASE_URI__.'modules/'.$this->name.'/cron.php?action=EshopTransactionAcknowledge&token='.Tools::hashIV(_COOKIE_IV_),
            'order_state_validation' => Configuration::get('SWSBLG_ORDER_STATE_VALIDATION'),
            'order_states' => OrderState::getOrderStates($this->context->cookie->id_lang),
            'nb_days_validation' => Configuration::get('SWSBLG_NB_DAYS_VALIDATION'),
            'last_cron' => Configuration::get('SWSBLG_LAST_CRON'),
            'orders_swissbilling' => $orders_swissbilling, 
        ));

        $this->_html .= $this->context->smarty->fetch(dirname(__FILE__).'/views/templates/admin/page.tpl');  
    }

    public function getContent()
    {
        if (Tools::isSubmit('btnSubmitConfig') || Tools::isSubmit('btnSubmitParams')){
            $this->_postValidation();
            if (!sizeof($this->_postErrors))
                $this->_postProcess();
            else
                foreach ($this->_postErrors AS $err)
                    $this->_html .= $this->displayError($err);
        }
        $this->_displayForm();
        return $this->_html;
    }

    public function hookPaymentReturn($params)
    {
        if (!$this->active)
                return ;

        $smarty = $this->context->smarty;
        
        $state = $params['order']->getCurrentState();     
        $id_order_state = Configuration::get('SWSBLG_ID_ORDER_STATE',null,$this->context->shop->id_shop_group,$this->context->shop->id);
        if(in_array($state,array($id_order_state,Configuration::get('PS_OS_OUTOFSTOCK'),Configuration::get('PS_OS_OUTOFSTOCK_UNPAID')))){
            $smarty->assign(array(
                'shop_name' => Configuration::get('PS_SHOP_NAME'),
                'total_to_pay' => Tools::displayPrice($params['order']->total_paid),
                'status' => 'ok',
                'id_order' => $params['order']->id
            ));       
        }else{
            $smarty->assign('status', 'failed');
        }

        return $this->display(__FILE__, 'payment_return.tpl');
         
    }

    /*
     * Hook back-office
     * @params
     * @return -
     */
    public function hookdisplayAdminOrderTabOrder($params){

        if(Configuration::get('SWSBLG_GENERATE_PDF')){

            $id_order = Tools::getValue('id_order');
            $Order = new Order($id_order);

            $transac_swissbilling = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'swissbilling WHERE `id_cart`="'.pSQL($Order->id_cart).'"');
            $datetime = new DateTime(Tools::substr($transac_swissbilling['timestamp'],0,19));
            $timestamp_iso =  $datetime->format(DateTime::ATOM); // Updated ISO8601

            $Currenttimestamp = $timestamp_iso;
            $CurrentEshop_ref = $Order->id_cart;
            $merchant = array(
                    'id'            => $this->merchant_id,
                    'pwd'           => $this->merchant_pw,
                    'success_url'   => $this->success_url,
                    'cancel_url'    => $this->cancel_url,
                    'error_url'     => $this->error_url,
            );

            $SoapClient = new SoapClient($this->url_wsdl_old,array('trace'=>0,'exceptions'=>1));
            $Response = $SoapClient->EshopTransactionStatusRequest($merchant,$CurrentEshop_ref,$Currenttimestamp);  
            if($Response->status === 'Acknowledged'){   
                $generate_invoice = Tools::getValue('generate_invoice');
                if($generate_invoice){
                    try{            
                        $SoapClient = new SoapClient($this->url_wsdl,array('trace'=>0,'exceptions'=>1));
                        $parameters = array("merchant"=>$merchant,"transaction_ref"=>$CurrentEshop_ref,"timestamp"=>$Currenttimestamp,"reporttype"=>Tools::getValue('reporttype'));
                        $status = $SoapClient->EShopTransactionGetInvoice($parameters);
                        $failure_text = @$status->EShopTransactionGetInvoiceResult->failure_text;                      
                        if(!empty($failure_text) && $failure_text!=='no error'){
                            dump($failure_text);
                            exit();
                        }
                        $this->EchoPdfDownload(
                                $status->EShopTransactionGetInvoiceResult->Invoice,
                                'order_id-'.$id_order.'.pdf',
                                $status->EShopTransactionGetInvoiceResult->Length
                                );		
                    }catch(SoapFault $exception){
                        $message = $exception->getMessage();
                        echo $message;
                        exit();
                    }
                }
                $link_swissbilling_invoice = true;
            }else{
                $link_swissbilling_invoice = false;
            }               

            $this->context->smarty->assign(
                                            array(
                                                '_path' => $this->_path,
                                                'link_swissbilling_invoice' => $link_swissbilling_invoice,
                                                'link_invoice' => $_SERVER['REQUEST_URI'].'&generate_invoice=1',
                                                'status' => $Response->status,
                                                'impression_type' => Configuration::get('SWSBLG_IMPRESSION_TYPE'),
                                                )
                                           );
            return $this->display(__FILE__, 'views/templates/admin/hookdisplayAdminOrderTabOrder.tpl');  
        }
    }

    /*
     * Génère le PDF Swissbilling
     * @param $data
     * @param $fileName
     * @param $dataLength
     * @return file
     */
    public function EchoPdfDownload($data, $fileName, $dataLength) {    
        // required for IE
        if(ini_get('zlib.output_compression')) { ini_set('zlib.output_compression', 'Off');	}
        ob_clean();	
        header('Pragma: public'); 	// required
        header('Expires: 0');		// no cache
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Last-Modified: '.gmdate ('D, d M Y H:i:s', time()) .' GMT');
        header('Cache-Control: private',false);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: '.$dataLength);
        header('Connection: close');	
        echo $data;
        exit();
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
            $item  = array('short_desc'    => (!empty($p['reference'])?$p['reference'].' - ':'').$p['name'].' '.$attributes,
                           'desc'          => (!empty($p['reference'])?$p['reference'].' - ':'').$p['name'].' '.$attributes,
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

        // bons de reductions
        $total_discount = ($Cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));

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
        
        $admin_fee_amount = Configuration::get('SWSBLG_ADMIN_FEE_AMOUNT');
        if(!empty($Cart->gift)){$admin_fee_amount+$this->round5ct($Cart->getGiftWrappingPrice());}

        $transaction = array(
                            'type'                      => 'Real', // 09.04.14 - exigé par Swissbilling
                            'is_B2B'                    => $this->b2b,
                            'eshop_ID'                  => $this->merchant_id, //constant set in payment method config
                            'eshop_ref'                 => $Cart->id, //Order number
                            'order_timestamp'           => $order_timestamp,
                            'currency'                  => $Currency->iso_code, //multi currency, can be chosen from the payment method config
                            'amount'                    => $total_order+Configuration::get('SWSBLG_ADMIN_FEE_AMOUNT'), // Order amount
                            'VAT_amount'                => $total_tax,
                            'admin_fee_amount'          => 0,
                            'delivery_fee_amount'       => $Cart->getTotalShippingCost(),
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
        $logo_url = $ShopUrl->getUrl().'img/'.Configuration::get('PS_LOGO');
        $mail_html = Tools::file_get_contents(dirname(__FILE__).'/views/mails/confirmation.html');
        $mail_html = str_replace('{subject}',$subject,$mail_html);
        $mail_html = str_replace('{shop_name}',$shop_name,$mail_html);
        $mail_html = str_replace('{shop_url}',$ShopUrl->getUrl(),$mail_html);
        $mail_html = str_replace('{logo_url}',$logo_url,$mail_html);
        $mail_html = str_replace('{message}',$message,$mail_html);
        return $mail_html;
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

            include_once(_PS_VENDOR_DIR_.'swiftmailer/swiftmailer/lib/swift_required.php');

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
                   if(Tools::strtolower($smtpEncryption)==='off'){$smtpEncryption = false;}

                   $connection = (new Swift_SmtpTransport($smtpServer,$smtpPort,$smtpEncryption))
                                 ->setUsername($smtpLogin)
                                 ->setPassword($smtpPassword);
               // envoi Mail()
               }else{
                   $connection = Swift_MailTransport::newInstance();
               }

               if(empty($from)){$from=Configuration::get('PS_SHOP_EMAIL');}
               if(empty($from_name)){$from_name=Configuration::get('PS_SHOP_NAME');}

               $swift = new Swift_Mailer($connection);
               $message = new Swift_Message($subject,$message,'text/html');
               $message->setFrom(array($from=>$from_name));
               $message->setTo($to);                  
               $swift->send($message);

           }catch(Swift_ConnectionException $e){
               ob_clean();
               dump($e->getMessage());
               exit();
           }catch(Swift_Message_MimeException $e){
               ob_clean();
               dump($e->getMessage());
               exit();
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

    public function hookPaymentOptions($params){

        if (!$this->active) {
            return;
        }
        if (!$this->checkCurrency($params['cart'])) {
            return;
        }
        
        $admin_fee_amount = Configuration::get('SWSBLG_ADMIN_FEE_AMOUNT');
        $text_more = '';
        if($admin_fee_amount>0){
            $text_more = ' (+ '.sprintf('%0.2f',$admin_fee_amount).' CHF '.$this->l('additional fees').')';
        }
        
        $logo = Tools::getHttpHost(true).__PS_BASE_URI__.'modules/'.$this->name.'/views/img/logo-hookpaymentoptions.jpg';
        $externalOption = new PaymentOption();
        $externalOption->setCallToActionText($this->l('Pay by invoice with Swissbilling').$text_more)
                       ->setAction(Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'module/'.$this->name.'/validation')
                       ->setLogo($logo);
        $payments_method = array($externalOption);  

        $swissbilling = new Swissbilling();
        
        // Pré-Screening actif 
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

                $SoapClient = new SoapClient($swissbilling->url_wsdl,array('trace'=>0,'exceptions'=>1,'stream_context'=>$swissbilling->stream_context));
                $parameters = array('merchant'=>$merchant,'transaction'=>$transaction,'debtor'=>$debtor,'item_count'=>count($items),'arrayofitems'=>$items);
                $status = $SoapClient->EshopTransactionPreScreening($parameters);

                // en cas d'acceptation on affiche la méthode Swissbilling
                if($status->EshopTransactionPreScreeningResult->status=='Answered'){
                    return $payments_method;
                }

            }catch(SoapFault $exception){
                // conservation du Log dans le back-office
                $message = $exception->getMessage();
                echo $message;
                Logger::addLog(pSQL($message,true),1,2,'Swissbilling',$cookie->id_cart);
            }
            
        // Pré-Screening désactivé     
        }else{
            return $payments_method;
        }

    }
        
}
?>
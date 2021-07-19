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

include(dirname(__FILE__).'/../../config/config.inc.php');
@include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/swissbilling.php');

$swissbilling = new Swissbilling();
$Context = Context::getContext();
$cookie = $Context->cookie;
$cart = $Context->cart;

if ($cart->id_customer == 0 OR $cart->id_address_delivery == 0 OR $cart->id_address_invoice == 0 OR !$swissbilling->active)
	Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

$customer = new Customer((int)$cart->id_customer);

if (!Validate::isLoadedObject($customer))
	Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

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

// transmission via Webservice
try{
    
    $SoapClient = new SoapClient($swissbilling->url_wsdl_old,array('trace'=>0,'exceptions'=>1));
    $status = $SoapClient->EshopTransactionRequest($merchant,$transaction,$debtor,count($items),$items);
    
    // pré-autorisation ok 
    if($status->failure_code==0){
        Tools::redirect($status->url); 
    // redirection page d'erreur
    }else{
        $location = $swissbilling->error_url.'?msg='.urlencode($status->failure_text_debtor);
        Tools::redirect($location);
    }
    
}catch(SoapFault $exception){
    
    // conservation du Log dans le back-office
    $message = $exception->getMessage();
    Logger::addLog(pSQL($message,true),1,1,'Swissbilling',$cart->id);;
    // redirection page d'erreur
    $location = $swissbilling->error_url.'?msg='.urlencode($message);
    Tools::redirect($location);
    
}
exit;

?>

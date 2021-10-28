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

header('Access-Control-Allow-Origin: *');
require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/swissbilling.php');

$token = Tools::getValue('token');
if($token==Tools::hashIV(_COOKIE_IV_)){
    
    
    Configuration::updateValue('SWSBLG_LAST_CRON',date('Y-m-d H:i:s'));
    
    $nb_days = Configuration::get('SWSBLG_NB_DAYS_VALIDATION');
    $DateEnd = new Datetime();
    $DateEnd->modify('-'.$nb_days.' days');
    $date_end =  $DateEnd->format('Y-m-d H:i:s');

    $DateStart = new Datetime($date_end);
    $DateStart->modify('-30 days');
    $date_start =  $DateStart->format('Y-m-d H:i:s');

    $sql = 'SELECT `id_cart`,`id_order` FROM `'._DB_PREFIX_.'orders` WHERE DATE_ADD BETWEEN "'.pSQL($date_start).'" AND "'.pSQL($date_end).'"';
    $orders = Db::getInstance()->ExecuteS($sql);
    
    $Swissbilling = new Swissbilling();
    $merchant = array(
        'id'            => $Swissbilling->merchant_id,
        'pwd'           => $Swissbilling->merchant_pw,
        'success_url'   => $Swissbilling->success_url,
        'cancel_url'    => $Swissbilling->cancel_url,
        'error_url'     => $Swissbilling->error_url,
        );
    
    // validation de la transaction
    foreach($orders as $o){
        
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'swissbilling` WHERE `id_cart`="'.pSQL($o['id_cart']).'" AND validate="0000-00-00 00:00:00" AND `error`="0"';
        $res = Db::getInstance()->getRow($sql);
          
        if(!empty($res)){
            
            $Order = new Order($o['id_order']);
            $id_order_state_current = $Order->getCurrentState();
            $id_order_state = Configuration::get('SWSBLG_ORDER_STATE_VALIDATION');
            if($id_order_state==$id_order_state_current){
                $SoapClient = new SoapClient($Swissbilling->url_wsdl_old,array('exceptions'=>0));
                $Response = $SoapClient->EshopTransactionAcknowledge($merchant,$res['id_cart'],$res['timestamp']);

                if(@$Response->status!=='Acknowledged' && @$Response->status!==''){
                    $subject = $Swissbilling->l('Error validation Swissbilling','cron');
                    $msg_content = 'id_cart : '.$res['id_cart'].'<br/>'.
                                   'timestamp : '.$res['timestamp'].'<br/>
                                    <br/>'.
                                    @$Response;
                    
                    // Webbax - 07.11.17 - stock le log du mail
                    Logger::addLog(pSQL($msg_content,true),4,5,'Swissbilling',$res['id_cart']);
                    
                    $mailFormatMsg = $Swissbilling->generateMailTemplate($subject,$msg_content);
                    $Swissbilling->MailSend($subject,$mailFormatMsg,Configuration::get('PS_SHOP_EMAIL'));
                    $vals = array('error'=>1);
                    Db::getInstance()->update('swissbilling',$vals,'`id_cart`="'.pSQL($res['id_cart']).'"');

                // approuve la transaction en interne
                }else{
                    $vals = array('validate'=> pSQL(date('Y-m-d H:i:s')));
                    Db::getInstance()->update('swissbilling',$vals,'`id_cart`="'.pSQL($res['id_cart']).'"');
                }
            }else{
				dump('ID order can not be validated : '.$o['id_order'].' / $id_order_state!=$id_order_state_current');
			}	
            
        }
    }
    
    echo 'ok';
    
}else{
    dump('invalid token');
}

?>
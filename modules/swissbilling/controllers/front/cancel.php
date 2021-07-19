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

class SwissbillingCancelModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        
        $Swissbilling = New Swissbilling();

        // conserve le log de l'erreur
        $transaction_ref = Tools::getValue('trans');
        $order_timestamp = Tools::getValue('timestamp');
        if(!empty($transaction_ref) && !empty($order_timestamp)){
            try{
                // paramètres généraux marchand
                $merchant = array(
                    'id'            => $Swissbilling->merchant_id,
                    'pwd'           => $Swissbilling->merchant_pw,
                    'success_url'   => $Swissbilling->success_url,
                    'cancel_url'    => $Swissbilling->cancel_url,
                    'error_url'     => $Swissbilling->error_url,
                );
                // récupération du statut de la commande
                $SoapClient = new SoapClient($Swissbilling->url_wsdl_old,array('trace'=>0,'exceptions'=>1));
                $response = $SoapClient->EshopTransactionStatusRequest($merchant,$transaction_ref,$order_timestamp);

                // 18.04.14 - confirmation
                // assure Swissbilling que le client a été redirigé
                try{
                    // le paiement a été refusé  (inutile de conserver le log)
                    $SoapClient->EshopTransactionConfirmation($merchant,$transaction_ref,$order_timestamp);
                }catch(SoapFault $exception){
                    d($exception);
                }

                Logger::addLog(pSQL($response->status,true),1,3,'Swissbilling',$transaction_ref);

            }catch(SoapFault $exception){
               d($exception);
            }
        }
        
        Tools::redirect('index.php?controller=order&step=0');

    }
}

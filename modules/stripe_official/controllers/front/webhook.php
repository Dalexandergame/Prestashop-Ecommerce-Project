<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 * @author    202-ecommerce <tech@202-ecommerce.com>
 * @copyright Copyright (c) Stripe
 * @license   Commercial license
 */

use Stripe_officialClasslib\Actions\ActionsHandler;
use Stripe_officialClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;

class stripe_officialWebhookModuleFrontController extends ModuleFrontController
{

    public function postProcess()
    {
        try {
            $secret_key = $this->module->getSecretKey();

            ProcessLoggerHandler::logInfo($secret_key, null, null, 'webhook');

            \Stripe\Stripe::setApiKey($secret_key);

            // Retrieve the request's body and parse it as JSON
            ProcessLoggerHandler::logInfo(
                'setApiKey ok. Retrieve the request\'s body and parse it as JSON',
                null,
                null,
                'webhook'
            );

            $endpoint_secret = Configuration::get(Stripe_official::WEBHOOK_SIGNATURE);
            $input = @Tools::file_get_contents("php://input");
            ProcessLoggerHandler::logInfo('$input => ' . $input, null, null, 'webhook');

            $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
            $event = null;

        } catch (Exception $e) {
            print_r($e->getMessage());
            ProcessLoggerHandler::logError('setApiKey not ok: ' . $e->getMessage(), null, null, 'webhook');
            ProcessLoggerHandler::closeLogger();
            http_response_code(500);
            exit;
        }

        try {
            $event = \Stripe\Webhook::constructEvent(
                $input,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            ProcessLoggerHandler::logError('Invalid payload : '.$e->getMessage(), null, null, 'webhook');
            ProcessLoggerHandler::closeLogger();
            http_response_code(400);
            echo $e->getMessage();
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            ProcessLoggerHandler::logError('Invalid signature : '.$e->getMessage(), null, null, 'webhook');
            ProcessLoggerHandler::closeLogger();
            http_response_code(400);
            echo $e->getMessage();
            exit();
        }

        try {
            ProcessLoggerHandler::logInfo('$event => ' . $event, null, null, 'webhook');
            ProcessLoggerHandler::logInfo('event ' . $event->id . ' retrieved', null, null, 'webhook');
            ProcessLoggerHandler::logInfo('event type : ' . $event->type, null, null, 'webhook');

            if (!$event) {
                $msg = 'JSON not valid';
                ProcessLoggerHandler::logError($msg, null, null, 'webhook');
                ProcessLoggerHandler::closeLogger();
                http_response_code(500);
                echo $msg;
                exit;
            }

            if ($event->type === 'application_fee.created') {
                \Stripe\Stripe::setAccountId('acct_1JrqvHHb3AcGIZAB');
                $charge = \Stripe\Charge::retrieve($event->data->object->charge);
                $event->type = 'charge.succeeded';
                $event->data->object = $charge;
            }

            if (!in_array($event->type, Stripe_official::$webhook_events)) {
                $msg = 'webhook "'.$event->type.'" call not yet supported';
                ProcessLoggerHandler::logInfo($msg, null, null, 'webhook');
                ProcessLoggerHandler::closeLogger();
                echo $msg;
                exit;
            }

            ProcessLoggerHandler::logInfo('starting webhook actions', null, null, 'webhook');

            $events_states = array(
                'charge.expired' => Configuration::get('PS_OS_CANCELED'),
                'charge.failed' => Configuration::get('PS_OS_ERROR'),
                'charge.succeeded' => Configuration::get('PS_OS_PAYMENT'),
                'charge.captured' => Configuration::get('PS_OS_PAYMENT'),
                'charge.refunded' => Configuration::get('PS_OS_CANCELED'),
                'charge.dispute.created' => Configuration::get(Stripe_official::SEPA_DISPUTE)
            );

            if ($event->type == 'payment_intent.requires_action') {
                $paymentIntent = $event->data->object->id;
            } else {
                $paymentIntent = $event->data->object->payment_intent;
            }

            if ($event->type == 'charge.refunded' && !$event->data->object->refunded) {
                $events_states['charge.refunded'] = Configuration::get('PS_OS_PAYMENT');
            }

            // Create the handler
            $handler = new ActionsHandler();
            $handler->setConveyor(array(
                'event_json' => $event,
                'module' => $this->module,
                'context' => $this->context,
                'events_states' => $events_states,
                'paymentIntent' => $paymentIntent,
            ));

            if (($event->type == 'charge.succeeded' && $event->data->object->payment_method_details->type == 'card')
                || ($event->type == 'charge.pending' && $event->data->object->payment_method_details->type == 'sepa_debit')
                || ($event->type == 'payment_intent.requires_action' && $event->data->object->payment_method_types[0] == 'oxxo')) {
                ProcessLoggerHandler::logInfo('payment_intent : '.$paymentIntent, null, null, 'webhook');
                ProcessLoggerHandler::logInfo('$event->type : '.$event->type, null, null, 'webhook');
                $handler->addActions(
                    'prepareFlowNone',
                    'updatePaymentIntent',
                    'createOrder',
                    'sendMail',
                    'saveCard',
                    'addTentative'
                );
            } elseif (($event->type == 'charge.succeeded'
                        && Stripe_official::$paymentMethods[$event->data->object->payment_method_details->type]['flow'] == 'redirect' && $event->data->object->payment_method_details->type != 'sofort')
                    || ($event->type == 'charge.pending'
                        && $event->data->object->payment_method_details->type == 'sofort')) {
                ProcessLoggerHandler::logInfo('payment_intent : '.$paymentIntent, null, null, 'webhook');
                ProcessLoggerHandler::logInfo('$event->type : '.$event->type, null, null, 'webhook');
                $handler->addActions(
                    'prepareFlowRedirectPaymentIntent',
                    'updatePaymentIntent',
                    'createOrder',
                    'sendMail',
                    'saveCard',
                    'addTentative'
                );
            } else {
                $handler->addActions('chargeWebhook');
            }
            // Process actions chain
            if (!$handler->process('ValidationOrder')) {
                // Handle error
                ProcessLoggerHandler::logError('Order webhook process failed.', null, null, 'webhook');
                ProcessLoggerHandler::closeLogger();
            }
        } catch (Exception $e) {
            print_r($e->getMessage());
            ProcessLoggerHandler::logError($e->getMessage(), null, null, 'webhook');
            ProcessLoggerHandler::closeLogger();
            http_response_code(200);
        }
        exit;
    }
}

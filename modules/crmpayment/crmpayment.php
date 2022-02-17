<?php

if (!defined('_PS_VERSION_'))
    exit;

class CrmPayment extends PaymentModule
{
    public function __construct()
    {
        $this->name = 'crmpayment';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'Pulse.digital';
        $this->need_instance = 1;

        parent::__construct();

        $this->displayName = $this->l('Paiement par CRM');
        $this->description = $this->l('Paiement par CRM');
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerHook('payment')
            || !$this->registerHook('paymentOptions')) {
            return false;
        }
        return true;
    }
}

<?php

class AdminEcosapinNoRetourDateOrdersController extends ModuleAdminController
{

    protected function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        if ( _PS_VERSION_ >= '1.7') {
            return Context::getContext()->getTranslator()->trans($string);
        } else {
            return parent::l($string, $class, $addslashes, $htmlentities);
        }
    }


    public function __construct()
    {
        $this->bootstrap      = true;
        $this->table          = 'order';
        $this->className      = 'Order';
        $this->lang           = false;
        $this->explicitSelect = true;
        $this->allow_export   = true;
        $this->deleted        = false;
        $this->addRowAction('afficher');
        $this->list_no_link = true;
        $this->context      = Context::getContext();

        $this->_select = '
		a.id_currency,
		a.id_order AS id_pdf,
		IF(YEAR(pdc.date_retour) = 0 , "aucune" , pdc.date_retour) as "retour_date",
		CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
		osl.`name` AS `osname`,
		os.`color`,
		IF((SELECT so.id_order FROM `' . _DB_PREFIX_ . 'orders` so WHERE so.id_customer = a.id_customer AND so.id_order < a.id_order LIMIT 1) > 0, 0, 1) as new,
		country_lang.name as cname,
		IF(a.valid, 1, 0) badge_success';

        $this->_where = ' = 0 OR YEAR(pdc.date_retour) = 0 OR pdc.date_retour = "--"';

        $this->_join           = '
        LEFT JOIN `' . _DB_PREFIX_ . 'planning_delivery_carrier` pdc ON (pdc.`id_order` = a.`id_order`)
		LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`)
		LEFT JOIN `' . _DB_PREFIX_ . 'address` address ON address.id_address = a.id_address_delivery
		LEFT JOIN `' . _DB_PREFIX_ . 'country` country ON address.id_country = country.id_country
		LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` country_lang ON (country.`id_country` = country_lang.`id_country` AND country_lang.`id_lang` = ' . (int) $this->context->language->id . ')
		LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON (os.`id_order_state` = a.`current_state`)
		LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = ' . (int) $this->context->language->id . ')';
        $this->_orderBy        = 'id_order';
        $this->_group          = 'group by id_order';
        $this->_orderWay       = 'DESC';
        $this->_use_found_rows = true;

        $this->fields_list = array(
            'id_order'=> array(
                'title' => $this->l('ID Orders'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'retour_date'=> array(
                'title' => $this->l('Retour Date')
            ),
            'reference'=> array(
                'title' => $this->l('Reference')
            ),
            'customer'=> array(
                'title'        => $this->l('Customer'),
                'havingFilter' => true,
            ),
            'total_paid_tax_incl' => array(
                'title'         => $this->l('Total'),
                'align'         => 'text-right',
                'type'          => 'price',
                'currency'      => true,
                'badge_success' => true
            ),
            'date_add'=> array(
                'title'      => $this->l('Date'),
                'align'      => 'text-right',
                'type'       => 'datetime',
                'filter_key' => 'a!date_add'
            )
        );

        parent::__construct();
    }


    public function displayAfficherLink($token, $id)
    {
        $token = Tools::getAdminTokenLite('AdminOrders');
        $link  = _PS_BASE_URL_ . __PS_BASE_URI__ . "administration/index.php?controller=AdminOrders&id_order=$id&vieworder&token=$token";
        return "<a class='btn btn-info' href='$link'><i class='icon-search'></i> détails</a>";
    }
}

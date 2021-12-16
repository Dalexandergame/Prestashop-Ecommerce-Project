<?php

class AdminCustomersFilterController extends ModuleAdminController
{

    private $_html    = '';
    private $myFilter = null;

    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
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
        $this->table          = 'customer';
        $this->className      = 'Customer';
        $this->lang           = false;
        $this->explicitSelect = true;
        $this->allow_export   = true;
        $this->deleted        = false;
        $this->list_no_link   = true;
        $this->addRowAction('afficher');
        $this->addRowAction('order');
        $this->context = Context::getContext();


        $this->_join = "LEFT JOIN ps_address c on a.id_customer = c.id_customer ";
        $this->_join .= "LEFT JOIN ps_orders o on o.id_address_delivery = c.id_address";

      $this->_use_found_rows = true;

        $this->fields_list = array(
            'id_customer' => array(
                'title'      => $this->l('ID'),
                'align'      => 'text-center',
                'class'      => 'fixed-width-xs',
               // 'filter_key' => 'o.id_order'
            ),
            'name' => array(
                'title'      => $this->l('ID Order'),
                'align'      => 'text-center',
                'class'      => 'fixed-width-xs',
                'filter_key' => 'o!id_order'
            ),
            'firstname'   => array(
                'title'      => $this->l('Firstname'),
                'filter_key' => 'c!firstname'
            ),
            'lastname'    => array(
                'title'      => $this->l('Lastname'),
                'filter_key' => 'c!lastname'
            ),
            'email'       => array(
                'title'      => $this->l('Email'),
                'filter_key' => 'a!email'
            ),
            'postcode'    => array(
                'title'      => $this->l('NPA/CP'),
                'filter_key' => 'c!postcode'
            ),
            'address1'    => array(
                'title'      => $this->l('Address 1'),
                'filter_key' => 'c!address1'
            ),
            'address2'    => array(
                'title'      => $this->l('Address 2'),
                'filter_key' => 'c!address2'
            ),
            'phone'       => array(
                'title'      => $this->l('Phone'),
                'filter_key' => 'c!phone'
            )
        );

       $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Filter customers')
            ),
            'input'  => array(
                array(
                    'label'       => $this->l('Filter'),
                    'type'        => 'text',
                    'name'        => 'filter',
                    'placeholder' => $this->l('Search filter')
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'name'  => 'filters',
                'class' => 'btn btn-default pull-right'
            )
        );

        parent::__construct();
    }


    public function postProcess()
    {
        if (Tools::isSubmit('filters')) {
            $this->myFilter = '%' . Tools::getValue('filter') . '%';
            if ($this->myFilter != null) {
                $this->_where = " AND c.city like '" . $this->myFilter . "'
                OR c.lastname like '" . $this->myFilter . "'
                OR c.address1 like '" . $this->myFilter . "'
                OR c.address2 like '" . $this->myFilter . "'
                OR c.postcode like '" . $this->myFilter . "'
                OR c.phone like '" . $this->myFilter . "'
                OR c.phone_mobile like '" . $this->myFilter . "'
                OR c.company like '" . $this->myFilter . "'
                OR a.email like '" . $this->myFilter . "'
                OR c.firstname like '" . $this->myFilter . "'";
            }
        }
    }

    public function renderList()
    {
        $this->_html .= parent::renderForm();
        $this->_html .= parent::renderList();
        return $this->_html;
    }

    public function displayAfficherLink($token, $id){
        $customer = new Customer($id);
        $token = Tools::getAdminTokenLite('AdminCustomers');
        $link  = _PS_BASE_URL_ . __PS_BASE_URI__ . "administration/index.php?controller=AdminCustomers&id_customer={$customer->id}&viewcustomer&token=$token";
        return "<a class='btn btn-info' href='$link'><i class='icon-search'></i> ".$this->l("Details")."</a>";
    }

    public function displayOrderLink($token, $id, $name)
    {
        $order = new Order($name);
        $token = Tools::getAdminTokenLite('AdminOrders');
        $link  = _PS_BASE_URL_ . __PS_BASE_URI__ . "administration/index.php?controller=AdminOrders&id_order={$order->id}&vieworder&token=$token";
        return "<a class='btn btn-info' href='$link'><i class='icon-search'></i> " . $this->l("Commande") . "</a>";
    }
}

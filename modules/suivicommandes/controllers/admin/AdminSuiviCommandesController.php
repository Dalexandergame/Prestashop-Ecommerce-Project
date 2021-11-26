<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the GNU General Public License, version 3 (GPL-3.0).
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    emarketing www.emarketing.com <integrations@emarketing.com>
 * @copyright 2020 emarketing AG
 * @license   https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 */

require_once (dirname(__FILE__) . '/../../classes/SuiviOrder.php');
require_once dirname(__FILE__) . '/../../classes/pdf/HTMLTemplateFichePdf.php';

class AdminSuiviCommandesController extends ModuleAdminController
{

    public    $id_product_abies = 115; //My Sapin Abies
    public    $id_shop = 1; //Ecosapin
    public    $dateLivraison       = NULL;
    public    $warehouse_selected  = NULL;
    protected $position_identifier = 'id_suivi_orders';
    protected $alertmsg            = NULL;
    protected $osmkey              = NULL;
    protected $gkey                = NULL;
    protected $carrier_profil_id   = 3;

    public function setCookies()
    {
        $this->context->cookie->__set('datepickerDatelivraison', $this->dateLivraison);
        $this->context->cookie->__set('warehouse_selected', implode(",", $this->warehouse_selected));
    }

    protected function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        if ( _PS_VERSION_ >= '1.7') {
            return Context::getContext()->getTranslator()->trans($string);
        } else {
            return parent::l($string, $class, $addslashes, $htmlentities);
        }
    }

    /**
     * AdminSuiviCommandesController constructor.
     * @throws PrestaShopException
    */
    public function __construct()
    {

        $this->table             = 'suivi_orders';
        $this->className         = 'SuiviOrder';
        $this->lang              = false;
        $this->deleted           = false;
        $this->colorOnBackground = false;
        $this->bootstrap         = true;
        $this->list_no_link      = true;
        $this->identifier        = 'id_suivi_orders';
        $this->isRetour          = null;

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->addRowAction('afficher');

        $this->id_carrier_post = (int) Configuration::get('TUNNELVENTE_ID_CARRIER_POST');
        $this->osmkey          = trim(Configuration::get('SUIVI_COMMANDES_OSM_API_KEY'));
        $this->gkey            = trim(Configuration::get('SUIVI_COMMANDES_GOOGLE_API_KEY'));

        $this->bulk_actions = array(
            'enable'        => array(
                'text' => $this->l('marquer livrée'),
                'icon' => 'icon-check'
            ),
            'disable'       => array(
                'text' => $this->l('marquer non livrée'),
                'icon' => 'icon-remove'
            ),
            'enable2'       => array(
                'text' => $this->l('marquer récupéré'),
                'icon' => 'icon-check'
            ),
            'disable2'      => array(
                'text' => $this->l('marquer non récupéré'),
                'icon' => 'icon-remove'
            ),
            'optimiser'     => array(
                'text' => $this->l('Optimiser textes'),
                'icon' => 'icon-pencil'
            ),
            'changeCarrier' => array(
                'text' => $this->l('Modifier transporteur'),
                'icon' => 'icon-AdminParentShipping'
            )
        );

        $this->context = Context::getContext();
        $this->id_shop = $this->context->shop->id;

        parent::__construct();

        if (Tools::getIsset('fix_duplicate_orders')) {
            $db_instance = Db::getInstance();
            $query = "
                select reference
                     , group_concat(id_order)        order_ids
                     , max(id_carrier)               id_carrier
                     , sum(total_discounts)          total_discounts
                     , sum(total_discounts_tax_excl) total_discounts_tax_excl
                     , sum(total_discounts_tax_incl) total_discounts_tax_incl
                     , sum(total_paid)               total_paid
                     , sum(total_paid_real)          total_paid_real
                     , sum(total_paid_tax_excl)      total_paid_tax_excl
                     , sum(total_paid_tax_incl)      total_paid_tax_incl
                     , sum(total_products)           total_products
                     , sum(total_products_wt)        total_products_wt
                     , sum(total_shipping)           total_shipping
                     , sum(total_shipping_tax_excl)  total_shipping_tax_excl
                     , sum(total_shipping_tax_incl)  total_shipping_tax_incl
                     , sum(total_wrapping)           total_wrapping
                     , sum(total_wrapping_tax_excl)  total_wrapping_tax_excl
                     , sum(total_wrapping_tax_incl)  total_wrapping_tax_incl
                from ps_orders
                group by reference
                having count(*) > 1
            ";

            $result = $db_instance->ExecuteS($query);

            foreach ($result as $order) {
                list($main_order, $duplicate_order) = explode(',', $order['order_ids']);
                $carrier_id = $order['id_carrier'];

                $db_instance->Execute("
                    update ps_orders set 
                        id_carrier = $carrier_id,
                        total_discounts = {$order['total_discounts']},
                        total_discounts_tax_excl = {$order['total_discounts_tax_excl']},
                        total_discounts_tax_incl = {$order['total_discounts_tax_incl']},
                        total_paid = {$order['total_paid']},
                        total_paid_real = {$order['total_paid_real']},
                        total_paid_tax_excl = {$order['total_paid_tax_excl']},
                        total_paid_tax_incl = {$order['total_paid_tax_incl']},
                        total_products = {$order['total_products']},
                        total_products_wt = {$order['total_products_wt']},
                        total_shipping = {$order['total_shipping']},
                        total_shipping_tax_excl = {$order['total_shipping_tax_excl']},
                        total_shipping_tax_incl = {$order['total_shipping_tax_incl']},
                        total_wrapping = {$order['total_wrapping']},
                        total_wrapping_tax_excl = {$order['total_wrapping_tax_excl']},
                        total_wrapping_tax_incl = {$order['total_wrapping_tax_incl']}
                    where id_order = $main_order
                ");
                $db_instance->Execute("
                    update ps_order_invoice set
                        total_discounts_tax_excl = {$order['total_discounts_tax_excl']},
                        total_discounts_tax_incl = {$order['total_discounts_tax_incl']},
                        total_paid_tax_excl = {$order['total_paid_tax_excl']},
                        total_paid_tax_incl = {$order['total_paid_tax_incl']},
                        total_products = {$order['total_products']},
                        total_products_wt = {$order['total_products_wt']},
                        total_shipping_tax_excl = {$order['total_shipping_tax_excl']},
                        total_shipping_tax_incl = {$order['total_shipping_tax_incl']},
                        total_wrapping_tax_excl = {$order['total_wrapping_tax_excl']},
                        total_wrapping_tax_incl = {$order['total_wrapping_tax_incl']}
                    where id_order = $main_order
                ");
                $db_instance->Execute("update ps_orders set current_state = 6, reference = concat(substr(reference,1,6), 'XXX') where id_order = $duplicate_order");
                $db_instance->Execute("update ps_suivi_orders set order_id = $main_order where order_id = $duplicate_order");
                $db_instance->Execute("update ps_stripe_capture set order_id = $main_order where order_id = $duplicate_order");
                $db_instance->Execute("update ps_twint_for_prestashop set order_id = $main_order where order_id = $duplicate_order");
                $db_instance->Execute("update ps_planning_delivery_carrier set order_id = $main_order where order_id = $duplicate_order");
                $db_instance->Execute("update ps_order_detail set id_order = $main_order where id_order = $duplicate_order");
                $db_instance->Execute("update ps_order_carrier set id_carrier = $carrier_id where id_order = $main_order");
                $db_instance->Execute("update ps_order_history set id_order_state = 6 where id_order = $duplicate_order");
                $db_instance->Execute("update ps_order_invoice_payment set id_order = $main_order where id_order = $duplicate_order");
            }
        }

        if (!$this->osmkey || empty($this->osmkey)) {
            $this->alertmsg .= "Veuillez configurer la clé de l'API OSM! <br> ";
        }
        if (!$this->gkey || empty($this->gkey)) {
            $this->alertmsg .= "Veuillez configurer la clé de l'API GOOGLE MAP! <br> ";
        }

        $this->dateLivraison      = date("Y-m-d");
        $this->warehouse_selected = $this->getWarehouseIds();
        if (empty($this->context->cookie->datepickerDatelivraison)) $this->setCookies();

        if (Tools::getValue("datepickerDatelivraison") && Tools::getValue("warehouse_selected")) {
            $this->setParams(Tools::getValue("datepickerDatelivraison", $this->dateLivraison), Tools::getValue("warehouse_selected", $this->warehouse_selected));
            $this->setCookies();
        } else {
            //When we don't submit those values like in edit , we catch them from cookies
            $this->setParams($this->context->cookie->datepickerDatelivraison, explode(",", $this->context->cookie->warehouse_selected));
        }

        $today = new \DateTime("now");
        $year  = $today->format("Y");
        $month = $today->format("m");

        // années d'acivité actuel
        $dateRetourStart = $month > 6 ? (intval($year) + 1) . "-01-01" : "$year-01-01";

        //check if delivery or retour
        $date1          = date_create($this->dateLivraison);
        $date2          = date_create($dateRetourStart);
        $this->isRetour = date_diff($date1, $date2)->invert == 1 ? true : false;

        if (($this->dateLivraison != null) && ($this->warehouse_selected != null)) {
            if (Tools::isSubmit('submitImport')) {
                if ($this->warehouse_selected[0] != $this->id_carrier_post."_p") {
                    $this->importCommandes();
                } else {
                    $this->importCommandesPoste();
                }
            } else if (Tools::isSubmit('submitMaj')) $this->majCommandes();
            else if (Tools::isSubmit('ordonnerOSM')) $this->ordonnerOSM();
        }

        if ($this->ajax && $this->isXmlHttpRequest() && Tools::getIsset('status')) {
            if (Tools::getIsset('status' . $this->table)) {
                $this->changeStatus((int) Tools::getValue('status'), false);
            }
            if (Tools::getIsset('recovered' . $this->table)) {
                $this->changeStatus((int) Tools::getValue('status'), true);
            }
        }

        //this removes the suffix _p from the Post id, selected from the front office
        $temp_warehouse_selected = [];
        foreach ($this->warehouse_selected as $warehouse_selected) {
            $wh_selected = str_replace("_p", '', $warehouse_selected);
            if (!in_array($wh_selected, $temp_warehouse_selected))
                $temp_warehouse_selected[] = $wh_selected;
        }

        //Pour afficher le nom du transporteur a partir de l'id
        //N.B: ll faut que les alias font reference au nouveau champ ajouté au tableau ex:commande et l'element selectionné est le parametre callback
        $this->_select .= 'a.*,ca.name as \'carrier_title\', car.name as \'carrier_retour_title\', CONCAT(a.id_suivi_orders , \'-\',ca.name, \'-\',"L") AS '
            . 'carrier_name ,CONCAT(a.id_suivi_orders , \'-\',car.name, \'-\',"R") AS carrier_name_retour, '
            . 'CONCAT(a.firstname, \'  \',a.lastname, \' \',a.company) AS customer, '
            . 'IF(datediff(a.date_delivery,"' . $this->dateLivraison . '")=0 ,ca.name, car.name ) as \'orderable_carrier_name\','
            . 'IF(datediff(a.date_delivery,"' . $this->dateLivraison . '")=0, ca.color, car.color) as color,'
            . 'IF(datediff(a.date_delivery,"' . $this->dateLivraison . '")=0, "L", "R") as type,'
            . 'CONCAT(a.phone, \' - \',a.phone_mobile) AS tel,'
            . 'CONCAT(a.address1, \'  \',a.address2) AS address,'
            . "adr.open_houre, "
            . 'a.active as active, '
            . 'a.recovered as recovered, ';

        $position = $this->isRetour
            ? "CONCAT(car.name, LPAD(a.position_retour, 3, 0)) as position, " .
              "IFNULL(a.position_retour,0) as position_new, a.position_retour as `position_retour`,"
            : "CONCAT(ca.name, LPAD(a.position, 3, 0)) as position, " .
              "IFNULL(a.position,0) as position_new, a.position as `real_position`,";


        $this->_select .= $position;

        $this->_join  .= ' JOIN ' . _DB_PREFIX_ . 'carrier ca ON (ca.id_carrier = a.id_carrier)'
                         . ' LEFT JOIN ' . _DB_PREFIX_ . 'carrier car ON (car.id_carrier = a.id_carrier_retour)'
                         . ' LEFT JOIN ' . _DB_PREFIX_ . 'orders o ON (a.id_order = o.id_order) '
                         . ' LEFT JOIN ' . _DB_PREFIX_ . 'address adr ON (adr.id_address = o.id_address_delivery) ';
        $this->_where = 'AND (datediff(a.date_delivery,"' . $this->dateLivraison . '")=0 OR datediff(a.date_retour,"' . $this->dateLivraison . '")=0) AND ';
        if ($this->warehouse_selected[0] == $this->id_carrier_post . "_p") {
            $this->_where .= ' a.id_carrier = ' . $this->id_carrier_post;
        } else {
            $this->_where .= ' a.id_carrier != ' . $this->id_carrier_post . ' AND a.id_warehouse IN ' . "(" . implode(",", $temp_warehouse_selected) . ")";
        }

        $this->_defaultOrderBy = 'position';

        if ($this->isRetour) {
            $position_field = [
                'position_retour' => array(
                    'title'      => $this->l('Position retour'),
                    'filter_key' => 'a!position_retour',
                    'position'   => 'real_position',
                    'align'      => 'center'
                )
            ];
        } else {
            $position_field = [
                'real_position' => array(
                    'title'      => $this->l('Position'),
                    'filter_key' => 'a!position',
                    'position'   => 'real_position',
                    'align'      => 'center'
                )
            ];
        }

        $fields_list = array(
            'position_new'        => array(
                'title'    => $this->l('New position'),
                'callback' => 'getNewPosition'
            ),
            'type'                => array(
                'title' => $this->l('type'),
                'color' => 'color',
            ),
            'carrier_name'        => array(
                'title'    => $this->l('Transporteur L'),
                'callback' => 'getCarrierName'
            ),
            'carrier_name_retour' => array(
                'title'    => $this->l('Transporteur R'),
                'callback' => 'getCarrierName'
            ),
            'customer'            => array(
                'title' => $this->l('client')
            ),
            'address'             => array(
                'title' => $this->l('Adresse')
            ),
            'postcode'            => array(
                'title'      => $this->l('CP'),
                'filter_key' => 'a!postcode'
            ),
            'city'                => array(
                'title'      => $this->l('Ville'),
                'filter_key' => 'a!city'
            ),
            'tel'                 => array(
                'title' => $this->l('Tel')
            ),
            'message'             => array(
                'title' => $this->l('message')
            ),
            'commande'            => array(
                'title'    => $this->l('Commande'),
                'callback' => 'getOrderProducts',
            ),
            'open_houre'          => array(
                'title' => $this->l('open_houre')
            ),
            'active'              => array(
                'title'   => $this->l('Livré'),
                'class'   => 'fixed-width-xs delivered',
                'align'   => 'center',
                'active'  => 'status',
                'type'    => 'bool',
                'orderby' => FALSE,
                'ajax'    => true
            ),
            'recovered'           => array(
                'title'   => $this->l('Récupéré'),
                'class'   => 'fixed-width-xs recovered',
                'align'   => 'center',
                'active'  => 'recovered',
                'type'    => 'bool',
                'orderby' => FALSE,
                'ajax'    => true
            )
        );

        $this->fields_list = array_merge($position_field, $fields_list);
    }


    public function setParams($dateLivraison, $warehouse_selected)
    {
        if (!is_null($dateLivraison)) {
            $this->dateLivraison = $dateLivraison;
        }

        if (!is_null($warehouse_selected)) {
            $this->warehouse_selected = $warehouse_selected;
        }
        if (is_null($warehouse_selected) || in_array("0", $warehouse_selected)) {
            $this->warehouse_selected = $this->getWarehouseIds();
        }
        if (is_null($warehouse_selected) || in_array("-", $warehouse_selected)) {
            $this->warehouse_selected = array(0);
        }
    }

    public function changeStatus($status, $isRecover)
    {
        $id  = (int) Tools::getValue('id_suivi_orders');
        $obj = new SuiviOrder($id);

        if (empty($obj->id_order))
            return;

        $order = new OrderCore($obj->id_order);

        // 5 is the status of 'delivered'
        // 2 is the status of 'payment accepted'
        // 21 is the status of 'recovered'
        if ($isRecover)
            $order_state = new OrderState($status == 1 ? 21 : 5);
        else
            $order_state = new OrderState($status == 1 ? 5 : 2);


        if (!Validate::isLoadedObject($order_state))
            $this->errors[] = Tools::displayError('The new order status is invalid.');
        else {
            $current_order_state = $order->getCurrentOrderState();
            if ($current_order_state->id != $order_state->id) {
                // Create new OrderHistory
                $history              = new OrderHistory();
                $history->id_order    = $order->id;
                $history->id_employee = (int) $this->context->employee->id;

                $use_existings_payment = false;
                if (!$order->hasInvoice())
                    $use_existings_payment = true;
                $history->changeIdOrderState((int) $order_state->id, $order, $use_existings_payment);

                $carrier      = new Carrier($order->id_carrier, $order->id_lang);
                $templateVars = array();
                if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number)
                    $templateVars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));

                // Save all changes
                if ($history->addWithemail(true, $templateVars)) {
                    // synchronizes quantities if needed..
                    if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
//                        foreach ($order->getProducts() as $product) {
//                            if (StockAvailable::dependsOnStock($product['product_id']))
//                                StockAvailable::synchronize($product['product_id'], (int)$product['id_shop']);
//                        }
                    }
                }
                // $this->errors[] = Tools::displayError('An error occurred while changing order status, or we were unable to send an email to the customer.');
            } else
                $this->errors[] = Tools::displayError('The order has already been assigned this status.');
        }

        if ($isRecover)
            $obj->toggleRecoveredStatus($status);
        else
            $obj->toggleDeliveredStatus($status);

    }

    // enabled means delivered
    public function changeStatusToEnabled($suivi_order_id = null)
    {
        $id  = $suivi_order_id;
        $obj = new SuiviOrder($id);

        if (empty($obj->id_order))
            return;
        $order = new OrderCore($obj->id_order);

        if ($obj->active == 1)
            return;
        // 5 is the status of 'delivered'
        // 2 is the status of 'payment accepted'
        $order_state = new OrderState(5);

        if (!Validate::isLoadedObject($order_state))
            $this->errors[] = Tools::displayError('The new order status is invalid.');
        else {
            $current_order_state = $order->getCurrentOrderState();
            if ($current_order_state->id != $order_state->id) {
                // Create new OrderHistory
                $history              = new OrderHistory();
                $history->id_order    = $order->id;
                $history->id_employee = (int) $this->context->employee->id;

                $use_existings_payment = false;
                if (!$order->hasInvoice())
                    $use_existings_payment = true;
                $history->changeIdOrderState((int) $order_state->id, $order, $use_existings_payment);

                $carrier      = new Carrier($order->id_carrier, $order->id_lang);
                $templateVars = array();
                if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number)
                    $templateVars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));

                // Save all changes
                if ($history->addWithemail(true, $templateVars)) {
                    // synchronizes quantities if needed..
                    if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
//                        foreach ($order->getProducts() as $product) {
//                            if (StockAvailable::dependsOnStock($product['product_id']))
//                                StockAvailable::synchronize($product['product_id'], (int)$product['id_shop']);
//                        }
                    }
                }
                // $this->errors[] = Tools::displayError('An error occurred while changing order status, or we were unable to send an email to the customer.');
            } else
                $this->errors[] = Tools::displayError('The order has already been assigned this status.');
        }

        $orderCheck = new OrderCore($obj->id_order);
        if ($orderCheck->current_state == 5)
            $obj->toggleStatus();

    }


    public function changeStatusToDisabled($suivi_order_id = null)
    {
        $id  = $suivi_order_id;
        $obj = new SuiviOrder($id);

        if (empty($obj->id_order))
            return;

        $order = new OrderCore($obj->id_order);
        if ($obj->active == 0)
            return;
        // 5 is the status of 'delivered'
        // 2 is the status of 'payment accepted'
        $order_state = new OrderState(2);

        if (!Validate::isLoadedObject($order_state))
            $this->errors[] = Tools::displayError('The new order status is invalid.');
        else {
            $current_order_state = $order->getCurrentOrderState();
            if ($current_order_state->id != $order_state->id) {
                // Create new OrderHistory
                $history              = new OrderHistory();
                $history->id_order    = $order->id;
                $history->id_employee = (int) $this->context->employee->id;

                $use_existings_payment = false;
                if (!$order->hasInvoice())
                    $use_existings_payment = true;

                if (Validate::isLoadedObject($order))
                    $history->changeIdOrderState((int) $order_state->id, $order, $use_existings_payment);

                $carrier      = new Carrier($order->id_carrier, $order->id_lang);
                $templateVars = array();
                if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number)
                    $templateVars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));

                // Save all changes
                if ($history->addWithemail(true, $templateVars)) {
                    // synchronizes quantities if needed..
                    if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
//                        foreach ($order->getProducts() as $product) {
//                            if (StockAvailable::dependsOnStock($product['product_id']))
//                                StockAvailable::synchronize($product['product_id'], (int)$product['id_shop']);
//                        }
                    }
                }
                // $this->errors[] = Tools::displayError('An error occurred while changing order status, or we were unable to send an email to the customer.');
            } else
                $this->errors[] = Tools::displayError('The order has already been assigned this status.');
        }

        $orderCheck = new OrderCore($obj->id_order);
        if ($orderCheck->current_state != 5)
            $obj->toggleStatus();
    }

    // enabled means delivered
    public function changeStatusToEnabled2($suivi_order_id = null)
    {
        $id  = $suivi_order_id;
        $obj = new SuiviOrder($id);

        if (empty($obj->id_order))
            return;
        $order = new OrderCore($obj->id_order);

        if ($obj->recovered == 1)
            return;
        // 5 is the status of 'delivered'
        // 2 is the status of 'payment accepted'
        $order_state = new OrderState(21);

        if (!Validate::isLoadedObject($order_state))
            $this->errors[] = Tools::displayError('The new order status is invalid.');
        else {
            $current_order_state = $order->getCurrentOrderState();
            if ($current_order_state->id != $order_state->id) {
                // Create new OrderHistory
                $history              = new OrderHistory();
                $history->id_order    = $order->id;
                $history->id_employee = (int) $this->context->employee->id;

                $use_existings_payment = false;
                if (!$order->hasInvoice())
                    $use_existings_payment = true;
                $history->changeIdOrderState((int) $order_state->id, $order, $use_existings_payment);

                $carrier      = new Carrier($order->id_carrier, $order->id_lang);
                $templateVars = array();
                if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number)
                    $templateVars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));

                // Save all changes
                if ($history->addWithemail(true, $templateVars)) {
                    // synchronizes quantities if needed..
                    if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
//                        foreach ($order->getProducts() as $product) {
//                            if (StockAvailable::dependsOnStock($product['product_id']))
//                                StockAvailable::synchronize($product['product_id'], (int)$product['id_shop']);
//                        }
                    }
                }
                // $this->errors[] = Tools::displayError('An error occurred while changing order status, or we were unable to send an email to the customer.');
            } else
                $this->errors[] = Tools::displayError('The order has already been assigned this status.');
        }

        $orderCheck = new OrderCore($obj->id_order);
        if ($orderCheck->current_state == 21)
            $obj->toggleRecoveredStatus();

    }


    public function changeStatusToDisabled2($suivi_order_id = null)
    {
        $id  = $suivi_order_id;
        $obj = new SuiviOrder($id);

        if (empty($obj->id_order))
            return;

        $order = new OrderCore($obj->id_order);
        if ($obj->recovered == 0)
            return;
        // 5 is the status of 'delivered'
        // 2 is the status of 'payment accepted'
        $order_state = new OrderState(5);

        if (!Validate::isLoadedObject($order_state))
            $this->errors[] = Tools::displayError('The new order status is invalid.');
        else {
            $current_order_state = $order->getCurrentOrderState();
            if ($current_order_state->id != $order_state->id) {
                // Create new OrderHistory
                $history              = new OrderHistory();
                $history->id_order    = $order->id;
                $history->id_employee = (int) $this->context->employee->id;

                $use_existings_payment = false;
                if (!$order->hasInvoice())
                    $use_existings_payment = true;

                if (Validate::isLoadedObject($order))
                    $history->changeIdOrderState((int) $order_state->id, $order, $use_existings_payment);

                $carrier      = new Carrier($order->id_carrier, $order->id_lang);
                $templateVars = array();
                if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number)
                    $templateVars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));

                // Save all changes
                if ($history->addWithemail(true, $templateVars)) {
                    // synchronizes quantities if needed..
                    if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
//                        foreach ($order->getProducts() as $product) {
//                            if (StockAvailable::dependsOnStock($product['product_id']))
//                                StockAvailable::synchronize($product['product_id'], (int)$product['id_shop']);
//                        }
                    }
                }
                // $this->errors[] = Tools::displayError('An error occurred while changing order status, or we were unable to send an email to the customer.');
            } else
                $this->errors[] = Tools::displayError('The order has already been assigned this status.');
        }

        $orderCheck = new OrderCore($obj->id_order);
        if ($orderCheck->current_state != 5)
            $obj->toggleRecoveredStatus();
    }


    public function renderList()
    {
        if (Tools::getIsset('map')) {
            $smarty               = $this->context->smarty;
            $this->display_header = false;
            $this->display_footer = false;
            $this->lite_display   = false;

            $tpl = $smarty->createTemplate(_PS_MODULE_DIR_ . '\suivicommandes\views\templates\admin\gmap_multiple_routes.tpl');

            $this->setParams(Tools::getValue("date", NULL), Tools::getValue("wh", NULL));
            $list = $this->getMap();

            $tpl->assign(array(
                             "base_dir" => _PS_BASE_URL_ . __PS_BASE_URI__,
                             "lists"    => $list,
                             "gkey"     => $this->gkey
                         )
            );
        } else if (Tools::getIsset('pdf')) {

            $id_carrier = Tools::getValue("idc", NULL);
            $this->setParams(Tools::getValue("date", NULL), Tools::getValue("wh", NULL));

            $params = (object) array("id_carrier" => $id_carrier, "dateLivraison" => $this->dateLivraison, "warehouse_selected" => $this->warehouse_selected, "isRetour" => $this->isRetour);
            $pdf    = new PDF($params, 'FichePdf', Context::getContext()->smarty, 'L');
            $pdf->render();
            exit;
        } else {
            if ($this->context->cookie->profile != $this->carrier_profil_id) {
                if (!($this->fields_list && is_array($this->fields_list)))
                    return false;
                $this->getList($this->context->language->id, null, null, 0, false, false);

                $helper                          = new HelperList();
                $helper->force_show_bulk_actions = true;

                $this->setHelperDisplay($helper);
                $helper->simple_header = false;

                $list = $helper->generateList($this->_list, $this->fields_list);

                $assign = array(
                    "warehouses"    => $this->setSelectWarehouses(),
                    "dateLivraison" => $this->dateLivraison,
                    "blocks"        => $this->getBlocks(),
                    "token"         => $this->token,
                    "wh"            => "(" . implode(",", $this->warehouse_selected) . ")",
                    "lists"         => $list,
                    "carriers"      => $this->getCarriers($this->context->language->id, $this->id_shop),
                    "alert"         => $this->alertmsg,
                    "link"          => $this->context->link,
                    "restricted"    => FALSE
                );
            } else {
                $assign = array(
                    "warehouses"    => $this->setSelectWarehouses(),
                    "dateLivraison" => $this->dateLivraison,
                    "token"         => $this->token,
                    "wh"            => "(" . implode(",", $this->warehouse_selected) . ")",
                    "blocks"        => $this->getBlocks(),
                    "alert"         => $this->alertmsg,
                    "restricted"    => TRUE
                );
            }

            $smarty = $this->context->smarty;
            $tpl    = $smarty->createTemplate(_PS_MODULE_DIR_ . '\suivicommandes\views\templates\admin\suivi.tpl');

            $tpl->assign($assign);
        }
        return $tpl->fetch();
    }


    public function getOrderProducts($commande)
    {
        $list     = '';
        $commande = trim($commande, ' ,');
        if ($commande) {
            $list = "<ul><li>." . str_replace(',', '.</li><li>.', $commande) . ".</li></ul>";
        }

        return $list;
    }

    public function getCarrierName($p)
    {
        $param = explode('-', $p);
        $field = '<a href="#" class="carriers" data-type="select" data-name="' . $param[2] . '" data-pk="' . $param[0] . '" data-url="index.php?controller=AdminSuiviCommandes&action=updateCarrier&ajax=true&token=' . $this->token . '"  >' . $param[1] . '</a>';

        return $field;
    }

    public function getNewPosition($position, $row)
    {
        $url   = $this->isRetour
            ? "index.php?controller=AdminSuiviCommandes&action=updatePositionRetour&ajax=true&token=$this->token"
            : "index.php?controller=AdminSuiviCommandes&action=updatePosition&ajax=true&token=$this->token";
        $field = '<a href="#" class="newPosition" data-type="text" data-name="new_position" data-pk="' . $row['id_suivi_orders'] . '" data-url="' . $url . '"  >' . $position . '</a>';

        return $field;
    }

    public function getWarehouseIds()
    {
        $res        = array();
        $warehouses = WarehouseCore::getWarehouses();
        foreach ($warehouses as $warehouse) {
            array_push($res, $warehouse['id_warehouse']);
        }
        return $res;
    }

    public function setSelectWarehouses()
    {
        $res        = array();
        $warehouses = WarehouseCore::getWarehouses();

        $res[] = array(
            'id'       => 0,
            'name'     => ' - All Warehouses- ',
            'selected' => count($warehouses) == count($this->warehouse_selected) ? 'selected' : ''
        );

        foreach ($warehouses as $warehouse) {
            $res[] = array(
                'id'       => $warehouse['id_warehouse'],
                'name'     => $warehouse['name'],
                'selected' => count($warehouses) != count($this->warehouse_selected) && in_array($warehouse["id_warehouse"], $this->warehouse_selected) ? 'selected' : ''
            );
        }
        $res[] = array(
            'id'       => $this->id_carrier_post . "_p",
            'name'     => "La Poste",
            'selected' => count($warehouses) != count($this->warehouse_selected) && in_array($this->id_carrier_post."_p", $this->warehouse_selected) ? '' : ''
        );

        $res[] = array(
            'id'       => "-",
            'name'     => "Aucun entrepôt",
            'selected' => count($warehouses) != count($this->warehouse_selected) && in_array("-", $this->warehouse_selected) ? 'selected' : ''
        );

        return $res;
    }


    public function importCommandes()
    {
        // fix missing orders
        Db::getInstance()->Execute("update ignore ps_planning_delivery_carrier pdc join ps_orders o on pdc.id_cart = o.id_cart set pdc.id_order = o.id_order where pdc.id_order = 0 ");

        $w = "(" . implode(",", $this->warehouse_selected) . ")";

        $where = " (datediff(pd.date_delivery,'" . $this->dateLivraison . "')=0 "
                 . "OR datediff(pd.date_retour,'" . $this->dateLivraison . "')=0) "
                 . "AND d.id_warehouse IN " . $w;

        $id_lang = 2; //FR
        //Import suivi_orders
        $sql1 = "INSERT INTO " . _DB_PREFIX_ . "suivi_orders(id_order,id_warehouse,id_carrier,id_carrier_retour,firstname,lastname,company,address1,address2,postcode,city,phone,phone_mobile,message,commande,date_delivery,date_retour,date_add,to_translate)
                    SELECT distinct(o.id_order),d.id_warehouse,o.id_carrier,o.id_carrier,a.firstname,a.lastname,a.company,a.address1,a.address2,a.postcode,a.city,a.phone,a.phone_mobile,cm.message,
                    GROUP_CONCAT(distinct(d.product_name),' X',d.product_quantity),pd.date_delivery,pd.date_retour,o.date_add,IF( o.id_lang != " . $id_lang . ", 1, 0)
                    FROM " . _DB_PREFIX_ . "orders as o 
                    JOIN " . _DB_PREFIX_ . "order_detail as d ON o.id_order = d.id_order 
                    LEFT JOIN " . _DB_PREFIX_ . "address as a ON o.id_address_delivery = a.id_address
                    LEFT JOIN " . _DB_PREFIX_ . "customer_thread as ct ON o.id_order = ct.id_order 
                    LEFT JOIN " . _DB_PREFIX_ . "planning_delivery_carrier as pd ON pd.id_order in (select o2.id_order from ps_orders o2 where o2.reference = o.reference) 
                    LEFT JOIN " . _DB_PREFIX_ . "customer_message as cm ON ct.id_customer_thread = cm.id_customer_thread WHERE o.is_imported=0 AND o.id_carrier != " . $this->id_carrier_post . " AND " . $where . " GROUP BY o.id_order";

        $sql2 = "UPDATE " . _DB_PREFIX_ . "orders as o
            INNER JOIN " . _DB_PREFIX_ . "suivi_orders as so ON o.id_order = so.id_order
            SET o.is_imported=1
            WHERE o.is_imported=0";

        Db::getInstance()->Execute($sql1);
        Db::getInstance()->Execute($sql2);

        $this->translateCommandsTo($id_lang);
    }

    public function importCommandesPoste()
    {
        $where = " datediff(pd.date_delivery,'" . $this->dateLivraison . "')=0 ";

        $id_lang = 2; //FR
        //Import suivi_orders
        $sql1 = "INSERT INTO " . _DB_PREFIX_ . "suivi_orders(id_order,id_warehouse,id_carrier,id_carrier_retour,firstname,lastname,company,address1,address2,postcode,city,phone,phone_mobile,message,commande,date_delivery,date_retour,date_add,to_translate)
                    SELECT distinct(o.id_order),d.id_warehouse,o.id_carrier,o.id_carrier,a.firstname,a.lastname,a.company,a.address1,a.address2,a.postcode,a.city,a.phone,a.phone_mobile,cm.message,
                    GROUP_CONCAT(distinct(d.product_name),' X',d.product_quantity),pd.date_delivery,pd.date_retour,o.date_add,IF( o.id_lang != " . $id_lang . ", 1, 0)
                    FROM " . _DB_PREFIX_ . "orders as o 
                    JOIN " . _DB_PREFIX_ . "order_detail as d ON o.id_order = d.id_order 
                    LEFT JOIN " . _DB_PREFIX_ . "address as a ON o.id_address_delivery = a.id_address
                    LEFT JOIN " . _DB_PREFIX_ . "customer_thread as ct ON o.id_order = ct.id_order 
                    LEFT JOIN " . _DB_PREFIX_ . "planning_delivery_carrier as pd ON pd.id_order in (select o2.id_order from ps_orders o2 where o2.reference = o.reference) 
                    LEFT JOIN " . _DB_PREFIX_ . "customer_message as cm ON ct.id_customer_thread = cm.id_customer_thread WHERE o.is_imported=0 AND o.id_carrier = " . $this->id_carrier_post . " AND " . $where . " GROUP BY o.id_order";

        //Mettre le flag imported
//            $sql2 ="UPDATE "._DB_PREFIX_."orders SET is_imported=1 WHERE id_order IN "
//                    . "(SELECT d.id_order FROM "._DB_PREFIX_."order_detail d "
//                    . "JOIN "._DB_PREFIX_."planning_delivery_carrier as pd ON d.id_order = pd.id_order WHERE".$where." )";
//
        $sql2 = "UPDATE " . _DB_PREFIX_ . "orders as o
            INNER JOIN " . _DB_PREFIX_ . "suivi_orders as so ON o.id_order = so.id_order
            SET o.is_imported=1
            WHERE o.is_imported=0";

        Db::getInstance()->Execute($sql1);
        Db::getInstance()->Execute($sql2);

        $this->translateCommandsTo($id_lang);
    }

    public function translateCommandsTo($id_lang)
    {
        $sql1 = "SELECT id_suivi_orders,id_order
                   FROM " . _DB_PREFIX_ . "suivi_orders
                   WHERE to_translate=1
                   AND id_order is not null";

        $result = Db::getInstance()->ExecuteS($sql1);

        foreach ($result as $res) {

            $commande = NULL;
            $sql2     = "SELECT pl.name,pac.id_attribute,a.id_attribute_group,d.product_quantity,d.product_id
                    FROM " . _DB_PREFIX_ . "orders as o
                    JOIN " . _DB_PREFIX_ . "order_detail as d ON o.id_order = d.id_order
                    JOIN " . _DB_PREFIX_ . "product_lang as pl ON d.product_id = pl.id_product
                    LEFT JOIN " . _DB_PREFIX_ . "product_attribute_combination as pac ON d.product_attribute_id = pac.id_product_attribute 
                    LEFT JOIN " . _DB_PREFIX_ . "attribute as a ON pac.id_attribute = a.id_attribute
                    WHERE o.id_order = " . $res["id_order"] . "
                    AND pl.id_lang = " . $id_lang . "
                    AND pl.id_shop = " . $this->id_shop . "
                    ORDER BY pl.name, a.id_attribute_group";

            $attributes = Db::getInstance()->ExecuteS($sql2);

            foreach ($attributes as $att) {

                $sql3 = "SELECT agl.name as att1,al.name as att2
                    FROM " . _DB_PREFIX_ . "attribute as at 
                    JOIN " . _DB_PREFIX_ . "attribute_group_lang as agl ON at.id_attribute_group = agl.id_attribute_group
                    JOIN " . _DB_PREFIX_ . "attribute_lang as al ON at.id_attribute = al.id_attribute
                    WHERE at.id_attribute ='" . $att["id_attribute"] . "'
                    AND agl.id_lang = " . $id_lang . " 
                    AND al.id_lang = " . $id_lang;

                $translated_attributes = Db::getInstance()->ExecuteS($sql3);


                $commande .= (in_array($att["id_attribute_group"], [12, 13]) && $att["product_id"] == $this->id_product_abies? '' : $att["name"]) .
                             ((isset($translated_attributes[0]['att1']) && $translated_attributes[0]['att1'] != null) ? ' - ' . $translated_attributes[0]['att1'] : '') .
                             ((isset($translated_attributes[0]['att2']) && $translated_attributes[0]['att2'] != null) ? ' : ' . $translated_attributes[0]['att2'] : '') .
                             ' X' . $att["product_quantity"] .
                             (in_array($att["id_attribute_group"], [1, 12]) && $att["product_id"] == $this->id_product_abies ? '' : ',');

            }

            Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'suivi_orders
                                SET commande = "' . (string) pSQL(trim($commande, ",")) . '",to_translate = 0 
                                WHERE id_suivi_orders=' . (int) $res["id_suivi_orders"]
            );
        }

    }

    /**
     * Pulse
     * By: Abdelhafid El kadiri
     */
    public function getBlocks()
    {
        $w = "(" . implode(",", $this->warehouse_selected) . ")";

        $where = "WHERE (datediff(so.date_delivery,'" . $this->dateLivraison . "')=0 OR datediff(so.date_retour,'" . $this->dateLivraison . "')=0) "
                 . "AND so.id_warehouse IN " . $w . " "
                 . "AND so.id_carrier != $this->id_carrier_post ";

        $sql1 = "SELECT count(*) as ncmd,
                    IF( datediff(so.date_delivery,'" . $this->dateLivraison . "')=0, so.id_carrier, so.id_carrier_retour) as id_carrier,
                    IF( datediff(so.date_delivery,'" . $this->dateLivraison . "')=0, ca.name, car.name) AS carrier_name,
                    IF( datediff(so.date_delivery,'" . $this->dateLivraison . "')=0, ca.color, car.color) AS color,
                    IF( datediff(so.date_delivery,'" . $this->dateLivraison . "')=0, soc.text, socr.text) AS text
                    FROM " . _DB_PREFIX_ . "suivi_orders as so
                    LEFT JOIN " . _DB_PREFIX_ . "suivi_orders_carrier soc ON (soc.id_carrier = so.id_carrier and datediff(soc.date_delivery,'" . $this->dateLivraison . "')=0)
                    LEFT JOIN " . _DB_PREFIX_ . "suivi_orders_carrier socr ON (socr.id_carrier = so.id_carrier_retour and datediff(socr.date_delivery,'" . $this->dateLivraison . "')=0)
                    JOIN " . _DB_PREFIX_ . "carrier as ca ON (ca.id_carrier = so.id_carrier)
                    JOIN " . _DB_PREFIX_ . "carrier as car ON (car.id_carrier = so.id_carrier_retour) " . $where . "
                    GROUP BY IF( datediff(so.date_delivery,'" . $this->dateLivraison . "')=0, so.id_carrier, so.id_carrier_retour) ";
        return Db::getInstance()->ExecuteS($sql1);

    }

    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
        // d($this->_list);
        $orders = [];

        foreach ($this->_list as $suiviOrder) {

            $productsAndQuantities = explode(',', $suiviOrder['commande']);

            $products = [];

            foreach ($productsAndQuantities as $productsAndQuantity) {
                $pattern = '/X[0-9]+$/';
                $matches = null;
                preg_match($pattern, $productsAndQuantity, $matches);
                $products[] = [
                    'name'     => str_replace($matches[0], '', $productsAndQuantity),
                    'quantity' => str_replace('X', '', $matches[0])
                ];
            }
            $orders[] = $products;
        }
        //d($orders);
    }


    public function majCommandes()
    {
        $id_lang = 2;
        $w       = "(" . implode(",", $this->warehouse_selected) . ")";

        $where = " WHERE (datediff(pd.date_delivery,'" . $this->dateLivraison . "')=0 OR datediff(pd.date_retour,'" . $this->dateLivraison . "')=0) ";

        if ($this->warehouse_selected[0] == $this->id_carrier_post."_p") {
            $where .= 'AND so.id_carrier = ' . $this->id_carrier_post;
        } else {
            $where .= "AND d.id_warehouse IN " . $w;
        }

        //Select new data
        $sql = "
        SELECT distinct so.id_suivi_orders,
               a.firstname,
               a.lastname,
               a.company,
               a.address1,
               a.address2,
               a.postcode,
               a.city,
               a.phone,
               pd.date_delivery,
               pd.date_retour,
               (SELECT GROUP_CONCAT(distinct (d.product_name), ' X', d.product_quantity)
                FROM ps_order_detail as d
                WHERE d.id_order = so.id_order
                GROUP BY d.id_order)    as commande,
               IF(o.id_lang != 2, 1, 0) as to_translate,
               a.phone_mobile
        FROM ps_suivi_orders so
                 JOIN ps_orders as o ON o.id_order = so.id_order
                 JOIN ps_address as a ON o.id_address_delivery = a.id_address
                 JOIN ps_order_detail as d ON o.id_order = d.id_order
                 JOIN ps_planning_delivery_carrier as pd ON so.id_order = pd.id_order
        $where";

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
        foreach ($result as $item) {
            Db::getInstance()->update('suivi_orders', $item, 'id_suivi_orders=' .$item['id_suivi_orders']);
        }
        $this->translateCommandsTo($id_lang);
    }

    public function renderForm()
    {
        $id_lang = 2; // french

        $id_order = NULL;
        if (is_string(Tools::getValue('updatesuivi_orders'))) {
            $id_order = (int) Db::getInstance()->getValue('SELECT id_order FROM ' . _DB_PREFIX_ . $this->table . ' WHERE id_suivi_orders = ' . Tools::getValue('id_suivi_orders'));
        }

        $products   = Db::getInstance()->executeS("
select pa.id_product_attribute, concat(pl.name, ' - ',agl.name, ' : ',al.name ) as 'name'
from ps_product p
       inner join ps_product_attribute pa on p.id_product = pa.id_product
       inner join ps_product_attribute_combination pac on pa.id_product_attribute = pac.id_product_attribute
       inner join ps_attribute_lang al on al.id_attribute = pac.id_attribute
       inner join ps_product_lang pl on pl.id_product = p.id_product
       inner join ps_attribute a on a.id_attribute = pac.id_attribute
       inner join ps_attribute_group_lang agl on agl.id_attribute_group = a.id_attribute_group
where
      al.id_lang = " . $id_lang . "
  and pl.id_lang = " . $id_lang . "
  and agl.id_lang = " . $id_lang . "
  and a.id_attribute_group != 8 -- Ignore packs
  and pl.id_shop = " . $this->id_shop . "
order by `name` asc
"
        )
        ;
        $products[] = [
            'id_product_attribute' => 0,
            'name'                 => 'Retourner votre Ecosapin'
        ];

        // d($products);

        //Ajout                                                  //Update new created
        if (is_string(Tools::getValue('addsuivi_orders')) || (is_string(Tools::getValue('updatesuivi_orders')) && !$id_order)) {
            $this->fields_form = array(
                'input'  => array(
                    array(
                        'type'     => 'select',
                        'label'    => $this->l('Transporteur L:'),
                        'name'     => 'id_carrier',
                        'required' => true,
                        'options'  => array(
                            'query' => $this->getCarriers($this->context->language->id, $this->id_shop),
                            'id'    => 'id_carrier',
                            'name'  => 'name'
                        ),
                    ),
                    array(
                        'type'     => 'select',
                        'label'    => $this->l('Transporteur R:'),
                        'name'     => 'id_carrier_retour',
                        'required' => true,
                        'options'  => array(
                            'query' => $this->getCarriers($this->context->language->id, $this->id_shop),
                            'id'    => 'id_carrier',
                            'name'  => 'name'
                        ),
                    ),
                    array(
                        'type'     => 'select',
                        'label'    => $this->l('Entrepôt :'),
                        'name'     => 'id_warehouse',
                        'required' => true,
                        'options'  => array(
                            'query' => WarehouseCore::getWarehouses(),
                            'id'    => 'id_warehouse',
                            'name'  => 'name'
                        ),
                    ),
                    array(
                        'type'  => 'text',
                        'label' => $this->l('Prenom :'),
                        'name'  => 'firstname'
                    ),
                    array(
                        'type'  => 'text',
                        'label' => $this->l('Nom :'),
                        'name'  => 'lastname'
                    ),
                    array(
                        'type'  => 'text',
                        'label' => $this->l('Entreprise :'),
                        'name'  => 'company'
                    ),
                    array(
                        'type'  => 'textarea',
                        'label' => $this->l('Adresse1 :'),
                        'name'  => 'address1'
                    ),
                    array(
                        'type'  => 'textarea',
                        'label' => $this->l('Adresse2 :'),
                        'name'  => 'address2'
                    ),
                    array(
                        'type'  => 'text',
                        'label' => $this->l('CP :'),
                        'name'  => 'postcode'
                    ),
                    array(
                        'type'  => 'text',
                        'label' => $this->l('Ville :'),
                        'name'  => 'city'
                    ),
                    array(
                        'type'  => 'text',
                        'label' => $this->l('Tel :'),
                        'name'  => 'phone'
                    ),
                    array(
                        'type'  => 'text',
                        'label' => $this->l('Mobile :'),
                        'name'  => 'phone_mobile'
                    ),
                    array(
                        'type'  => 'textarea',
                        'label' => $this->l('Message :'),
                        'name'  => 'message',
                        'rows'  => 5,
                        'size'  => 50
                    ),
                    array(
                        'type'  => 'textarea',
                        'label' => $this->l('Commande :'),
                        'name'  => 'commande',
                        'id'    => 'order_to_construct',
                        'desc'  => 'Entrez les commandes séparées par une virgule simple ex : cmd1,cm2,... ',
                        'rows'  => 5,
                        'size'  => 50
                    ),
                    array(
                        'type'    => 'select',
                        'label'   => $this->l('Produit'),
                        'desc'    => $this->l('Produit à ajouté'),
                        'name'    => 'product_to_add',
                        'id'      => 'product_to_add',
                        'options' => array(
                            'query' => $products,
                            'id'    => 'id_product_attribute',
                            'name'  => 'name'
                        )
                    ),
                    array(
                        'type'  => 'text',
                        'label' => $this->l('Quantité'),
                        'class' => 'go-for',
                        'desc'  => $this->l('La quantité souhaitée.'),
                        'name'  => 'Quantity',
                        'id'    => 'quantity_to_add',
                        'value' => 'click me',
                    ),
                    array(
                        'type'  => 'free',
                        'label' => '',
                        'class' => 'go-for',
                        'desc'  => "
<button type='button' id='add_product' text='ajouter'><i class='process-icon-plus'></i></button> &nbsp;
<button type='button' id='clear_products'text='vider la liste'><i class='process-icon-delete'></i></button>
<script>
       $('#add_product').click(function(e) {
           e.preventDefault();
           var product = $('#product_to_add option:selected').text().trim();
           var quantity = parseInt($('#quantity_to_add').val());
           
           if(isNaN(quantity) || quantity <= 0){
               alert('Quantité invalide! veuillez spécifier la quantité souhaitée');
               return;
           }
           
           var line = product + ' X'+quantity+',';
           var oldContent = $('#order_to_construct').val();
           $('#order_to_construct').val(oldContent + line);
       });
       $('#clear_products').click(function(e) {
           e.preventDefault();
           if(!confirm('Voulez-vous vraiment vider la liste des produits?')){   
               $('#quantity_to_add').val('')
               return;
           }
           $('#order_to_construct').val('');
       });                      
</script>
                            ",
                        'name'  => 'shipping_method',
                        'value' => 'click me',
                    ),
                    array(
                        'type'  => 'date',
                        'label' => $this->l('Date livraison'),
                        'name'  => 'date_delivery',
                        'size'  => 10
                    ),
                    array(
                        'type'  => 'date',
                        'label' => $this->l('Date retour'),
                        'name'  => 'date_retour',
                        'size'  => 10
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'button'
                )
            );

        } else {

            $this->fields_form = array(
                'input'  => array(
                    array(
                        'type'     => 'select',
                        'label'    => $this->l('Transporteur L:'),
                        'name'     => 'id_carrier',
                        'required' => true,
                        'options'  => array(
                            'query' => $this->getCarriers($this->context->language->id, $this->id_shop),
                            'id'    => 'id_carrier',
                            'name'  => 'name'
                        ),
                    ),
                    array(
                        'type'     => 'select',
                        'label'    => $this->l('Transporteur R:'),
                        'name'     => 'id_carrier_retour',
                        'required' => true,
                        'options'  => array(
                            'query' => $this->getCarriers($this->context->language->id, $this->id_shop),
                            'id'    => 'id_carrier',
                            'name'  => 'name'
                        ),
                    ),
                    array(
                        'type'     => 'select',
                        'label'    => $this->l('Entrepôt :'),
                        'name'     => 'id_warehouse',
                        'required' => true,
                        'options'  => array(
                            'query' => WarehouseCore::getWarehouses(),
                            'id'    => 'id_warehouse',
                            'name'  => 'name'
                        ),
                    ),
                    array(
                        'type'     => 'textarea',
                        'label'    => $this->l('Message :'),
                        'name'     => 'message',
                        'rows'     => 5,
                        'size'     => 50,
                        'required' => true
                    ),

                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'button'
                )
            );

            //Update imported one
            if (is_string(Tools::getValue('updatesuivi_orders')) && $id_order) {
                $statusField = array(
                    'type'     => 'switch',
                    'label'    => $this->l('Livré'),
                    'name'     => 'active',
                    'required' => false,
                    'class'    => 't',
                    'is_bool'  => true,
                    'values'   => array(
                        array(
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                );

                array_push($this->fields_form['input'], $statusField);

                $statusField = array(
                    'type'     => 'switch',
                    'label'    => $this->l('Récupéré'),
                    'name'     => 'recovered',
                    'required' => false,
                    'class'    => 't',
                    'is_bool'  => true,
                    'values'   => array(
                        array(
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                );

                array_push($this->fields_form['input'], $statusField);
            }

        }

        return parent::renderForm();
    }

    public function curlExec($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function getlatlng($adr)
    {
        $wait = 1;
        while ($wait <= 2) {

            $address = str_replace(' ', '+', $adr);
            $url     = "https://maps.googleapis.com/maps/api/geocode/json?address=$address&sensor=false&key=" . $this->gkey;

            $response_a = json_decode($this->curlExec($url));
            if ($response_a->status == 'OK') {
                $lat  = $response_a && $response_a->status == 'OK' ? $response_a->results[0]->geometry->location->lat : NULL;
                $long = $response_a && $response_a->status == 'OK' ? $response_a->results[0]->geometry->location->lng : NULL;
                return array("lat" => $lat, "long" => $long);
            } else {
                sleep(2);
                $wait++;
            }
        }

        $this->alertmsg .= "Limite Google API dépassée ou l'adresse " . '"' . $adr . '"' . " est incorrecte.<br> Type Erreur Google API: " . $response_a->status . "<br>";

        return NULL;
    }

    public function getOSMRequest($idw = NULL, $order = "")
    {
        if (!$idw) {
            $idw   = "(" . implode(",", $this->warehouse_selected) . ")";
            $where = "o.id_warehouse IN " . $idw;
        } else {
            $where = "o.id_warehouse = " . $idw;
        }

        $sql = "SELECT o.id_suivi_orders,
                IF( datediff(o.date_delivery,'" . $this->dateLivraison . "')=0, o.id_carrier, o.id_carrier_retour) as id_carrier,
                IF( datediff(o.date_delivery,'" . $this->dateLivraison . "')=0, ca.name, car.name) AS carrier_name,
                CONCAT(o.address1,' ',o.postcode,' ',o.city) as address,
                CONCAT(a.address1,' ',a.postcode,' ',a.city) as addresswh,
                o.position as position,
                o.position_retour as position_retour
                FROM " . _DB_PREFIX_ . "suivi_orders as o
                JOIN " . _DB_PREFIX_ . "warehouse as w ON o.id_warehouse = w.id_warehouse
                JOIN " . _DB_PREFIX_ . "address as a ON w.id_address = a.id_address
                JOIN " . _DB_PREFIX_ . "carrier as ca ON (ca.id_carrier = o.id_carrier) 
                JOIN " . _DB_PREFIX_ . "carrier as car ON (car.id_carrier = o.id_carrier_retour)
                WHERE o.id_carrier != $this->id_carrier_post AND (datediff(o.date_delivery,'" . $this->dateLivraison . "')=0 OR datediff(o.date_retour,'" . $this->dateLivraison . "')=0) AND " . $where . $order;

        $res = Db::getInstance()->ExecuteS($sql);

        return $res;
    }

    public function getWarehouseAddressStart($idw)
    {

        $sql = "SELECT CONCAT(a.address1,' ',a.postcode,' ',a.city) as addresswh
                FROM " . _DB_PREFIX_ . "warehouse as w
                JOIN " . _DB_PREFIX_ . "address as a ON w.id_address = a.id_address
                WHERE w.id_warehouse = " . $idw;

        $res = Db::getInstance()->ExecuteS($sql);
        return $res;
    }

    public function ordonnerOSM()
    {

        foreach ($this->warehouse_selected as $w) {

            $startPoint       = $this->getWarehouseAddressStart((int) $w)[0]["addresswh"];
            $latlngStartPoint = $this->getlatlng($startPoint);
            $listPoints       = $latlngStartPoint["lat"] . ',' . $latlngStartPoint["long"] . '|';

            if (!empty($latlngStartPoint["lat"])) {

                $res  = $this->getOSMRequest((int) $w);
                $res1 = $result = array();
                $i    = 1;

                foreach ($res as $item) {
                    $thePoint = $this->getlatlng($item["address"]);
                    $lat      = $thePoint["lat"];
                    $long     = $thePoint["long"];

                    if ($thePoint != NULL) {
                        if (!empty($lat)) {
                            $res1[]     = array(
                                'index'           => $i++,
                                'id_suivi_orders' => $item["id_suivi_orders"],
                                'address'         => addslashes((string) $item["address"]),
                                'lat'             => $lat,
                                'long'            => $long
                            );
                            $listPoints .= $lat . ',' . $long . '|';
                        } else {
                            $this->alertmsg .= "L'adresse : '" . $item["address"] . "' est incorrecte. <br>";
                        }
                    }
                }
                // tour=open donc on met le point de départ comme arrivée aussi
                $listPoints .= $latlngStartPoint["lat"] . ',' . $latlngStartPoint["long"];

                if ($i >= 3) {
                    $i++;
                    $url = "http://maps.open-street.fr/api/tsp/?pts=" . $listPoints . "&nb=" . $i . "&mode=driving&unit=m&tour=open&key=" . $this->osmkey;

                    $optimizedIndex = json_decode($this->curlExec($url));

                    if (!empty($optimizedIndex->status) && $optimizedIndex->status == "LOW_CREDIT") {
                        $this->alertmsg .= "Vous n'avez pas assez de crédit OpenStreetMap pour effectuer cette requête.<br>";
                    } else {
                        $flippedOptimizedIndex = array_flip($optimizedIndex->OPTIMIZATION);
                        ksort($flippedOptimizedIndex);

                        if (sizeof($flippedOptimizedIndex) == $i) {

                            //Mettre les positions par index selon fourni par l'OSM
                            foreach ($res1 as $item) {
                                $result[] = array(
                                    'index'           => $item["index"],
                                    'id_suivi_orders' => $item["id_suivi_orders"],
                                    'position'        => $flippedOptimizedIndex[$item["index"]]
                                );
                            }

                            foreach ($result as $item) {
                                if ($this->isRetour)
                                    Db::getInstance()->execute('
                                    UPDATE `' . _DB_PREFIX_ . 'suivi_orders`
                                    SET `position_retour` = ' . (int) ($item["position"] - 1) . '
                                    WHERE `id_suivi_orders`=' . (int) $item['id_suivi_orders']
                                    )
                                    ;
                                else
                                    Db::getInstance()->execute('
                                    UPDATE `' . _DB_PREFIX_ . 'suivi_orders`
                                    SET `position` = ' . (int) ($item["position"] - 1) . '
                                    WHERE `id_suivi_orders`=' . (int) $item['id_suivi_orders']
                                    )
                                    ;
                            }
                        } else {
                            $this->alertmsg .= "Vérifiez que toutes les adresses sont correctes et se trouvent dans le même continent.<br>";
                        }
                    }
                } else {
                    $this->alertmsg .= "Il faut au moins trois points à ordonner pour les commandes de l'entrepôt n " . $w . "<br>";
                }
            } else {
                $this->alertmsg .= "L'adresse de l'entrepôt : '" . $startPoint . "' est incorrecte. <br>";
            }

        }
    }

    public function in_array_r($needle, $haystack, $strict = false)
    {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array_r($needle, $item, $strict))) {
                return true;
            }
        }
        return false;
    }

    public function getMap()
    {
        if ($this->isRetour) {
            $res = $this->getOSMRequest(NULL, " ORDER BY o.position_retour ASC");
        } else {
            $res = $this->getOSMRequest(NULL, " ORDER BY o.position ASC");
        }

        $res1 = $result = array();

        foreach ($res as $item) {
            $latlngwh = $this->getlatlng($item["addresswh"]);
            $latwh    = $latlngwh["lat"];
            $longwh   = $latlngwh["long"];
            $latlng   = $this->getlatlng($item["address"]);
            $lat      = $latlng["lat"];
            $long     = $latlng["long"];

            if ($latwh && $longwh && $lat && $long) {
                $res1[] = array(
                    'id_carrier'      => $item["id_carrier"],
                    'carrier_name'    => $item["carrier_name"],
                    'position'        => $item["position"],
                    'position_retour' => $item["position_retour"],
                    'addresswh'       => addslashes((string) $item["addresswh"]),
                    'latwh'           => $latwh,
                    'longwh'          => $longwh,
                    'address'         => addslashes((string) $item["address"]),
                    'lat'             => $lat,
                    'long'            => $long
                );
            }
        }

        foreach ($res1 as $key => $item) {
            if ($item['latwh'] && (!array_key_exists($item['carrier_name'], $result) || ($result[$item['carrier_name']] && !$this->in_array_r($item['addresswh'], $result[$item['carrier_name']])))) {
                $result[$item['carrier_name']][] = array('address' => $item['addresswh'], 'lat' => $item['latwh'], 'long' => $item['longwh'], 'marker' => 'S');
            }
            if ($item['lat']) {
                $marker                          = $this->isRetour ? $item['position_retour'] : $item['position'];
                $result[$item['carrier_name']][] = array('address' => $item['address'], 'lat' => $item['lat'], 'long' => $item['long'], 'marker' => $marker);
            }
        }
        ksort($result, SORT_NUMERIC);

        return $result;

    }

    public function optimiser($text)
    {
        if (Configuration::get('SUIVI_COMMANDES_TEXTES_OPTIMISATION')) {
            $arr = json_decode("{" . Configuration::get('SUIVI_COMMANDES_TEXTES_OPTIMISATION') . "}");

            foreach ($arr as $key => $value) {
                $text = str_replace($key, $value, $text);
            }
        }

        return pSQL($text);
    }

    protected function processBulkEnable()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id) {
                $this->changeStatusToEnabled($id);
            }
        }
    }

    protected function processBulkDisable()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id) {
                $this->changeStatusToDisabled($id);
            }
        }
    }

    protected function processBulkEnable2()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id) {
                $this->changeStatusToEnabled2($id);
            }
        }
    }

    protected function processBulkDisable2()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id) {
                $this->changeStatusToDisabled2($id);
            }
        }
    }

    protected function processBulkOptimiser()
    {

        if (is_array($this->boxes) && !empty($this->boxes)) {

            foreach ($this->boxes as $id) {

                $sql1   = "SELECT so.message,so.commande FROM " . _DB_PREFIX_ . "suivi_orders so WHERE so.id_suivi_orders = " . $id;
                $result = Db::getInstance()->ExecuteS($sql1);

                $commande = '"' . $this->optimiser($result[0]["commande"]) . '"';

                $sql2 = "UPDATE " . _DB_PREFIX_ . "suivi_orders so
                    SET so.commande = $commande
                    WHERE so.id_suivi_orders = " . $id;

                Db::getInstance()->Execute($sql2);

            }
        }
    }

    protected function processBulkChangeCarrier()
    {
        $id_carrier   = (int) Tools::getValue("carrier_selected");
        $carrier_type = Tools::getValue("carrier_type");

        if (is_array($this->boxes) && !empty($this->boxes)) {

            if ($carrier_type == "L") {
                foreach ($this->boxes as $id) {
                    $obj = new SuiviOrder($id);
                    $obj->setNewCarrier($id_carrier);
                }
            } else if ($carrier_type == "R") {
                foreach ($this->boxes as $id) {
                    $obj = new SuiviOrder($id);
                    $obj->setNewCarrierRetour($id_carrier);
                }
            }
        }
    }

    public function ajaxProcessUpdatePositions()
    {
        $id_suivi_orders = (int)Tools::getValue('id');
        $positions       = Tools::getValue('suivi_orders');
        $field           = $this->isRetour? "position_retour": "position";

        if (is_array($positions)) {
            foreach ($positions as $position => $value) {
                $pos   = explode('_', $value);

                $update_position_sql = "
                       UPDATE ps_suivi_orders set $field = $position
                       WHERE id_suivi_orders = {$pos[2]}
                    ";
                echo $update_position_sql . " => ";
                if (isset($position) && Db::getInstance()->execute($update_position_sql))
                    echo 'ok position ' . (int)$position . ' for id ' . (int)$pos[2] . '\r\n';
                else
                    echo '{"hasError" : true, "errors" : "Can not update id ' . (int)$id_suivi_orders . ' to position ' . (int)$position . ' "}';

            }
        }
    }

    public function ajaxProcessUpdateCarrier()
    {
        $id_suivi_order = (int) Tools::getValue('pk');
        $type           = Tools::getValue('name');
        $id_carrier     = (int) Tools::getValue('value');
        $this->setParams(Tools::getValue("date", NULL), Tools::getValue("wh", NULL));

        $obj = new SuiviOrder($id_suivi_order);

        if ($type == "L") {
            $obj->setNewCarrier($id_carrier);
        } else if ($type == "R") {
            $obj->setNewCarrierRetour($id_carrier);
        }
        $smarty = $this->context->smarty;
        $tpl    = $smarty->createTemplate(_PS_MODULE_DIR_ . '\suivicommandes\views\templates\admin\blockc.tpl');

        $tpl->assign(array(
                         "blocks"        => $this->getBlocks(),
                         "dateLivraison" => $this->dateLivraison,
                         "wh"            => "(" . implode(",", $this->warehouse_selected) . ")",
                         "token"         => $this->token,
                     )
        );

        die($tpl->fetch());

    }

    public function ajaxProcessUpdatePosition()
    {
        $id_suivi_order = (int) Tools::getValue('pk');
        $type           = Tools::getValue('name');
        $new_position   = (int) Tools::getValue('value');

        $obj = new SuiviOrder($id_suivi_order);

        $obj->position = $new_position;
        $obj->save();

        $response = ['success' => 'true', 'message' => 'position successfully updated'];

        die(json_encode($response));

    }

    public function ajaxProcessUpdatePositionRetour()
    {
        $id_suivi_order = (int) Tools::getValue('pk');
        $type           = Tools::getValue('name');
        $new_position   = (int) Tools::getValue('value');

        $obj = new SuiviOrder($id_suivi_order);

        $obj->position_retour = $new_position;
        $obj->save();

        $response = ['success' => 'true', 'message' => 'position successfully updated'];

        die(json_encode($response));

    }


    public function ajaxProcessUpdateTextBlockCarrier()
    {
        $id_carrier = (int) Tools::getValue('pk');
        $texte      = Tools::getValue('value');
        $this->setParams(Tools::getValue("date", NULL), Tools::getValue("wh", NULL));

        $sql1 = "SELECT so.id_suivi_orders_carrier FROM " . _DB_PREFIX_ . "suivi_orders_carrier so 
                 WHERE so.id_carrier = " . $id_carrier . " 
                 AND datediff(so.date_delivery,'" . $this->dateLivraison . "')=0";


        $existant = Db::getInstance()->ExecuteS($sql1);

        if (empty($existant)) {
            $sql2 = "INSERT INTO " . _DB_PREFIX_ . "suivi_orders_carrier 
                     VALUES ('', '" . $id_carrier . "', '" . $this->dateLivraison . "', '" . $texte . "')";
        } else {
            $sql2 = "UPDATE " . _DB_PREFIX_ . "suivi_orders_carrier 
                    SET text='" . $texte . "' WHERE id_suivi_orders_carrier = " . $existant[0]["id_suivi_orders_carrier"];
        }

        if ($sql2) Db::getInstance()->Execute($sql2);
    }

    public static function getCarriers($id_lang, $id_shop, $modules_filters = Carrier::PS_CARRIERS_ONLY)
    {
        $sql = '
		SELECT c.*
		FROM `' . _DB_PREFIX_ . 'carrier` c
		LEFT JOIN `' . _DB_PREFIX_ . 'carrier_lang` cl ON (c.`id_carrier` = cl.`id_carrier` AND cl.`id_lang` = ' . (int) $id_lang . ')
		WHERE c.`deleted` = 0
		AND cl.`id_shop` = ' . $id_shop ;

        switch ($modules_filters) {
            case 1 :
                $sql .= ' AND c.is_module = 0 ';
                break;
            case 2 :
                $sql .= ' AND c.is_module = 1 ';
                break;
            case 3 :
                $sql .= ' AND c.is_module = 1 AND c.need_range = 1 ';
                break;
            case 4 :
                $sql .= ' AND (c.is_module = 0 OR c.need_range = 1) ';
                break;
        }
        $sql .= ' GROUP BY c.`id_carrier` ORDER BY c.name ASC';

        $cache_id = '$this->getCarriers_' . md5($sql);
        if (!Cache::isStored($cache_id)) {
            $carriers = Db::getInstance()->executeS($sql);
            Cache::store($cache_id, $carriers);
        }
        $carriers = Cache::retrieve($cache_id);
        foreach ($carriers as $key => $carrier)
            if ($carrier['name'] == '0')
                $carriers[$key]['name'] = Configuration::get('PS_SHOP_NAME');
        return $carriers;
    }

    public function displayAfficherLink($token, $id)
    {
        $suiviOrder  = new SuiviOrder($id);
        $order_id    = $suiviOrder->id_order;
        $order       = new Order($order_id);
        $customer_id = $order->id_customer;
        $token       = Tools::getAdminTokenLite('AdminCustomers');
        $link        = _PS_BASE_URL_ . __PS_BASE_URI__ . "administration/index.php?controller=AdminCustomers&id_customer=$customer_id&viewcustomer&token=$token";
        return "<a href='$link'><i class='icon-search'></i> Détails Client</a>";
    }
}

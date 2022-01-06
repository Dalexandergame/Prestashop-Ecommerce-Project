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
        $this->token = Tools::getValue('token')? Tools::getValue('token') : null;


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
        }elseif (Tools::getValue('ajax') == 1 && Tools::getValue('action') == 'uploadCsvAddress') {
            $this->processUploadCsvAddress();
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

    /*
 * Pulse: Custom Functions
 * Created By Abdelhafid El Kadiri
 */

    /**
     *
     * @return void
     */
    public function ajaxProcessUploadCSVaddress()
    {
        die('test process succeed');
        if ($this->tabAccess['edit'] === '1') {
            $customer = new Customer((int)Tools::getValue('id_customer'));
            die("test" . $customer->id);
            if (!Validate::isLoadedObject($customer))
                die ('error:update');
            if (!empty($note) && !Validate::isCleanHtml($note))
                die ('error:validation');
            $customer->note = $note;
            if (!$customer->update())
                die ('error:update');
            die('ok');
        }
    }

    /*function process_csv($file, $separator) {

        $file = fopen($file, "r");
        $data = array();

        while (!feof($file)) {
            $row = fgetcsv($file,null,$separator);
            if($row != false)
                $data[] = $row;
        }

        fclose($file);
        return $data;
    }*/

    function process_csv($file, $separator)
    {
        $content = file_get_contents($file);
//        $content = iconv('macintosh', "UTF-8", $content);
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $data = explode("\n", $content);
        foreach ($data as $key => &$row) {
            if (empty($row)) {
                unset($data[$key]);
                continue;
            }
            $row = explode($separator, $row);
        }
        return $data;
    }

    function clean($string){
        return Db::getInstance()->_escape(trim($string));
    }

    public function processUploadCsvAddress()
    {
        $id_customer = (int)Tools::getValue('id_customer');
        $separator = Tools::getValue('separator');
        $file = isset($_FILES['csv-upload']) ? $_FILES['csv-upload'] : null;
        $count = 0;
        $error = "";
        $inserted = "";
        if ($file != null) {
            $tmpName = $file['tmp_name'];
            $data = $this->process_csv($tmpName, $separator);
            $header = array();
            try {
                foreach ($data as $csvRow) {
                    $row = $csvRow;
                    if (sizeof($header) == 0) {
                        foreach ($row as $key => $cell) {
                            $cell = str_replace(["é","è","ê"], "e", $cell);
                            $cell = preg_replace("/[^A-Za-z0-9;'-]/", '', utf8_encode(strtolower(trim($cell))));
                            $header[$cell] = $key;
                        }
                        continue;
                    }
                    $address = null;
                    $address->id_customer = $id_customer;
                    $address->firstname = $this->prepareValue($row,$header,"prenom");
                    $address->lastname = $this->prepareValue($row,$header,"nom");
                    $address->id_country = Country::getIdByName($this->context->language->id, $this->prepareValue($row,$header,"pays"));
                    if($address->id_country == 0) $address->id_country = 19;//todo Suisse ID
                    $address->id_state = State::getIdByIso($this->prepareValue($row,$header,"etat"), $address->id_country);
                    $address->address1 = $this->prepareValue($row,$header,"adresse");
                    $address->address2 = $this->prepareValue($row,$header,"adresse2");
                    $address->postcode = $this->prepareValue($row,$header,"npa");
                    $address->city = $this->prepareValue($row,$header,"ville");
                    $address->phone = $this->prepareValue($row,$header,"telephone") != "" ? $this->prepareValue($row,$header,"telephone") : $this->prepareValue($row,$header,"mobile");
                    $address->phone_mobile = $this->prepareValue($row,$header,"mobile");
                    $address->other = $this->prepareValue($row,$header,"autre");
                    $address->company = $this->prepareValue($row,$header,"company");
                    $address->date_add = pSQL(date('Y-m-d H:i:s'));
                    $address->date_upd = pSQL(date('Y-m-d H:i:s'));
                    $address->alias = $address->postcode . "-" . substr(str_replace(" ", "", ucwords($address->firstname)), 0, 8) . substr(str_replace(" ", "", ucwords($address->lastname)), 0, 8) . "-" . $address->id_customer;
                    if (!Address::aliasExist($address->alias, null, (int)$id_customer) && $address->postcode != "") {
                        Db::getInstance()->insert('address', $address);
                        $count++;
                    } else {
                        $inserted .= $address->firstname . " " . $address->lastname . " " . $address->postcode . "<br>";
                    }
                }
            } catch (Exception $exception) {
                die(json_encode([
                    "success" => 0,
                    "count" => $count,
                    "inserted" => $inserted,
                    "error" => $exception->getMessage()
                ]));
            }
            die(json_encode([
                "success" => 1,
                "count" => $count,
                "inserted" => $inserted,
                "error" => $error
            ]));
        }
        die(json_encode([
            "success" => 0,
            "count" => $count,
            "inserted" => $inserted,
            "error" => "Fichier invalid"
        ]));
    }

    private function prepareValue($row, $header, $column)
    {
        if (isset($header[$column]))
            if (isset($row[$header[$column]]))
                return $this->clean($row[$header[$column]]);
        return "";
    }
}

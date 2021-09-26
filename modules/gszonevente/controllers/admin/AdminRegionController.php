<?php

require_once dirname(__FILE__) . '/../../models/Region.php';

class AdminRegionController extends ModuleAdminController {

    private $opt;
    private $optCarrier;

    protected function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        if ( _PS_VERSION_ >= '1.7') {
            return Context::getContext()->getTranslator()->trans($string);
        } else {
            return parent::l($string, $class, $addslashes, $htmlentities);
        }
    }

    public function initContent()
    {
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->errors[] = $this->l('You must select a specific shop in order to continue.');
        }
        parent::initContent();
    }
    
    public function __construct() {
        $this->table = 'gszonevente_region';
        $this->className = 'Region';
        $this->list_id = 'region';

        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->shopLinkType = 'shop';
        $this->fields_list = array(
            'id_gszonevente_region' => array(
                'title' => 'Id'
            ),
            'name' => array(
                'title' => $this->l('Nom'),
            ),
            'id_carrier' => array(
                'title' => $this->l('Transporteur'),
                'callback' => 'getValueOptionCarrier'
            ),
            'id_country' => array(
                'title' => $this->l('Pays'),
                'callback' => 'getValueOption'
            ),
        );


        $this->addRowAction('edit');
        $this->addRowAction('view');
        $this->actions = array('view', 'edit', 'delete');
        parent::__construct();
    }

//    public function initPageHeaderToolbar() {
//
//
//        if ($this->display == 'view' && $id = Tools::getValue("id_gszonevente_region"))
//            $this->page_header_toolbar_btn['new_menu'] = array(
//                'href' => self::$currentIndex . '&id_gszonevente_region=' . $id . '&addnpa&token=' . $this->token,
//                'desc' => $this->l('Ajouter NPA', null, null, false),
//                'icon' => 'process-icon-new'
//            );
//
//        parent::initPageHeaderToolbar();
//    }

    public function ListeOptions() {
        if ($this->opt) {
            return $this->opt;
        }
        $sql = "SELECT id_country,name FROM " . _DB_PREFIX_ . "country_lang  WHERE id_lang = 1";
        $resultat = Db::getInstance()->executeS($sql);
        $country = array();
        foreach ($resultat as $key => $value) {
            $country[] = array(
                'id_option' => $value['id_country'],
                'name' => $value['name'],
            );
        }
        return $this->opt = $country;
    }

    public function ListeOptionsCarrier() {
        if ($this->optCarrier) {
            return $this->optCarrier;
        }
        $sql = "SELECT id_carrier,name FROM " . _DB_PREFIX_ . "carrier  WHERE deleted = 0";
        $resultat = Db::getInstance()->executeS($sql);
        $country = array();
        foreach ($resultat as $key => $value) {
            $country[] = array(
                'id_option' => $value['id_carrier'],
                'name' => $value['name'],
            );
        }
        return $this->optCarrier = $country;
    }

    public function getValueOption($id) {
        foreach ($this->ListeOptions() as $value) {
            if ((int) $value['id_option'] == (int) $id) {
                return $value['name'];
            }
        }
        return '';
    }

    public function getValueOptionCarrier($id) {
        foreach ($this->ListeOptionsCarrier() as $value) {
            if ((int) $value['id_option'] == (int) $id) {
                return $value['name'];
            }
        }
        return '';
    }

    public function renderView() {
        if (($id = Tools::getValue('id_gszonevente_region'))) {
            $url = $this->context->link->getAdminLink('AdminNpa&id_gszonevente_region='.$id.'&token='.Tools::getAdminTokenLite("AdminNpa"),FALSE);
            Tools::redirectAdmin($url);
        }
    }

    public function renderForm() {
        $options = $this->ListeOptions();
        $optionsCarrier = $this->ListeOptionsCarrier();
        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Country'),
                'icon' => 'icon-tags'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
//                    'lang' => true,
                    'required' => true,
                    'hint' => $this->l('Invalid characters:') . ' <>;=#{}',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Pays'),
                    'name' => 'id_country',
                    'required' => true,
                    'options' => array(
                        'query' => $options,
                        'id' => 'id_option',
                        'name' => 'name',
                    ),
                    'hint' => $this->l('Invalid characters:') . ' <>;=#{}',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Transporteur'),
                    'name' => 'id_carrier',
                    'required' => true,
                    'options' => array(
                        'query' => $optionsCarrier,
                        'id' => 'id_option',
                        'name' => 'name',
                    ),
                    'hint' => $this->l('Invalid characters:') . ' <>;=#{}',
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => 'submitAdd' . $this->table
            )
        );
        return parent::renderForm();
    }

}

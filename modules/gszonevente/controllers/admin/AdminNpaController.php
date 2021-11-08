<?php

require_once dirname(__FILE__) . '/../../models/Npa.php';

class AdminNpaController extends ModuleAdminController {

    private $id_gszonevente_region;
    protected function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        if ( _PS_VERSION_ >= '1.7') {
            return Context::getContext()->getTranslator()->trans($string);
        } else {
            return parent::l($string, $class, $addslashes, $htmlentities);
        }
    }
    public function __construct() {
        $this->table = 'gszonevente_npa';
        $this->className = 'Npa';
        $this->list_id = 'npa';

        $this->bootstrap = true;
        $this->context = Context::getContext();
//        $this->lang = true;
        $this->fields_list = array(
            'id_gszonevente_npa' => array(
                'title' => 'Id'
            ),
            'name' => array(
                'title' => $this->l('NPA'),
            ),
        );

        $this->id_gszonevente_region = Tools::getValue("id_gszonevente_region");
        $this->addRowAction('edit');
        $this->actions = array('edit', 'delete');

        parent::__construct();
    }

    public function renderList()
	{
		
                $this->_where = 'AND a.`id_gszonevente_region` = '.(int)$this->id_gszonevente_region;
		return parent::renderList();
	}
    
    public function initPageHeaderToolbar() {
        self::$currentIndex = self::$currentIndex . '&id_gszonevente_region=' . $this->id_gszonevente_region;
        parent::initPageHeaderToolbar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex . '&addgszonevente_npa&token=' . $this->token,
            'desc' => $this->l('Nouveau NPA', null, null, false),
            'icon' => 'process-icon-new'
        );
    }

    public function initToolbar() {
        parent::initToolbar();
        $this->toolbar_btn['new'] = array(
            'href' => self::$currentIndex . '&addgszonevente_npa&id_gszonevente_region=' . $this->id_gszonevente_region . '&token=' . $this->token,
            'desc' => $this->l('Nouveau NPA', null, null, false),
            'icon' => 'process-icon-new'
        );
    }

    public function processAdd() {
        $object = parent::processAdd();
        if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
            $this->redirect_after = self::$currentIndex . '&addgszonevente_npa&id_gszonevente_region=' . $this->id_gszonevente_region . '&token=' . $this->token;
        }  else {
            $this->redirect_after = self::$currentIndex . '&id_gszonevente_region=' . $this->id_gszonevente_region . '&token=' . $this->token;
        }
        return $object;
    }

    public function processUpdate() {
        $object = parent::processUpdate();
        if (Tools::isSubmit('submitAdd' . $this->table)) {
            $this->redirect_after = self::$currentIndex . '&id_gszonevente_region=' . $this->id_gszonevente_region . '&token=' . $this->token;
        }
        return $object;
    }
    
    public function processDelete() {
        $object = parent::processDelete();
        $this->redirect_after = self::$currentIndex . '&id_gszonevente_region=' . $this->id_gszonevente_region . '&token=' . $this->token;
        return $object;    
    }

    public function renderForm() {

        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('NPA'),
                'icon' => 'icon-tags'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('NPA'),
                    'name' => 'name',
                    'required' => true,
                    'hint' => $this->l('Invalid characters:') . ' <>;=#{}',
                ),
                array(
                    'type' => 'hidden',
                    'label' => $this->l('Name'),
                    'name' => 'id_gszonevente_region',
                    'required' => true,
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => 'submitAdd' . $this->table
            )
        );
        $this->fields_form['buttons'] = array(
                'save-and-stay' => array(
                'title' => $this->l('Enregistrer puis ajouter une autre valeur'),
                'name' => 'submitAdd'.$this->table.'AndStay',
                'type' => 'submit',
                'class' => 'btn btn-default pull-right',
                'icon' => 'process-icon-save'
                )
        );
        $this->getFieldsValue(array(
            'id_gszonevente_region' => $this->id_gszonevente_region,
        ));
        return parent::renderForm();
    }

    public function initToolbarTitle() {
        $bread_extended = $this->breadcrumbs;        
        switch ($this->display) {
            case 'edit':
                $bread_extended[] = $this->l('Modifier NPA');
                break;

            case 'add':
                $bread_extended[] = $this->l('Ajouter NPA');
                break;

            default :
                $bread_extended[] = $this->l('Liste NPA');
                break;
        }

        if (count($bread_extended) > 0)
            $this->addMetaTitle($bread_extended[count($bread_extended) - 1]);

        $this->toolbar_title = $bread_extended;
    }

}

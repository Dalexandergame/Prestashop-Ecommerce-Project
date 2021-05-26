<?php
/**
 * author: mouhcine mahfoud (mouhcinemahfoud@gmail.com)
 *
 */

require_once(dirname(__FILE__) .'/classes/Partner.php');

class EcosapinPartners extends Module {
    private $_html;
    private $table_name;
    private $updating;
    private $viewing;
    private $selected_partner_id;

    public function __construct()
    {
        $this->name = "ecosapinpartners";
        $this->displayName = "Ecosapin partenaires";
        $this->tab = 'administration';
        $this->version = '0.1';
        $this->author = 'mouhcine@pulse.digital';
        $this->description = 'manage your partners';
        $this->bootstrap = true;
        parent::__construct();
        $this->_html = '';
        $this->table_name = Partner::$definition['table'];
        $this->updating = false;
        $this->viewing = false;
    }

    public function getContent(){
        $this->_postProcess();
        if($this->updating ){
            $partner = $this->_findPartner($this->selected_partner_id);
            $this->_html .= $this->_getPartnerEditForm($partner);
        }else if($this->viewing){
            $partner = $this->_findPartner($this->selected_partner_id);
            $this->_html .= $this->_getPartnerShowView($partner);
        }else{
            $this->_html .= $this->_getPartnerForm();
            $this->_html .= $this->_getPartnerList();
        }
        return $this->_html ;
    }

    private function _postProcess(){
        $img_name = null;
        $error = "";

        // show partner details
        // activate viewing where isSubmited viewpartners
        if(Tools::isSubmit('viewpartners')){
            $this->viewing = true;
            $this->selected_partner_id = (int)Tools::getValue('partner_id');
        }else{
            $this->viewing = false;
        }

        // add partner action
        if(Tools::isSubmit('add_partner_action')){
            // handle image upload
            if($_FILES)
            {
                $helper = new HelperImageUploader('partner_img');
                $files = $helper->process();

                if($files)
                {
                    $file = $files[0];
                    if(isset($file['save_path']))
                    {
                        if(!ImageManager::checkImageMemoryLimit($file['save_path']))
                            $error = Tools::displayError('Memory Limit reached');

                        if(!$error)
                        {
                            // using the timestamp to randomize the image name for avoiding naming collisions
                            $now = new DateTime();
                            $timestamp = $now->getTimestamp();
                            $final_img_name = $timestamp.'_'.$file['name'];

                            if(!ImageManager::resize($file['save_path'], dirname(__FILE__) . '/uploads/' . $final_img_name))
                                $error = Tools::DisplayError('An error occurred during the image upload');
                            else {
                                $img_name = $final_img_name;
                            }
                        }
                        unlink($file['save_path']);
                    }
                }
            }
            
            $partner_name = pSQL(Tools::getValue('partner_name'));
            $partner_description = [];
            $languages = $this->context->controller->getLanguages();
            foreach ($languages as $language){
                $partner_description[$language['id_lang']] = pSQL(Tools::getValue('partner_description_'.$language['id_lang']));
            }
            $partner_img = $img_name ? $img_name : 'default.jpg';
            $warehouse_id = pSQL(Tools::getValue('warehouse_id'));
            if($this->_addPartner($partner_name, $partner_img,$partner_description, $warehouse_id)){
                $this->_html .= $this->displayConfirmation($this->l("Partner has been added successfully"));
            }else{
                $this->_html .= $this->displayError($this->l("oops! an error accrued while adding the partner!"));
            }
        }

        // activate editing mode
        if(Tools::isSubmit('updatepartners')){
            $this->updating = true;
            $this->selected_partner_id = (int)Tools::getValue('partner_id');
        }else{
            $this->updating = false;
        }

        // updating a record
        if(Tools::isSubmit('update_partner_action')){
            $partner = new Partner();
            $partner->partner_id = (int)Tools::getValue('partner_id');
            $partner->name = pSQL(Tools::getValue('partner_name'));
            $partner->img = pSQL(Tools::getValue('partner_img'));
            $partner_description = [];
            $languages = $this->context->controller->getLanguages();
            foreach ($languages as $language){
                $partner_description[$language['id_lang']] = pSQL(Tools::getValue('partner_description_'.$language['id_lang']));
            }
            $partner->description = $partner_description;
            $partner->warehouse_id = pSQL(Tools::getValue('warehouse_id'));
            $this->_updatePartner($partner);
            $this->_html .= $this->displayConfirmation($this->l("Partner has been updated successfully"));
        }

        // deleting a record
        if(Tools::isSubmit('deletepartners')){
            $this->selected_partner_id = (int)Tools::getValue('partner_id');
            if($this->_deletePartner($this->selected_partner_id)){
                $this->_html .= $this->displayConfirmation($this->l("Partner has been deleted successfully"));
            }else{
                $this->_html .= $this->displayError($this->l("oops! an error accrued while deleting the partner!"));
            }

        }
    }

    private function _updatePartner(Partner $partner){
        $old_partner = $this->_findPartner($partner->partner_id);
        if(!$partner || !$old_partner){
            $this->_html .= $this->displayError($this->l("oops! partner not found!"));
            return;
        }
        $isImageUpdated = (isset($_FILES['partner_img']) && $_FILES['partner_img']['size'] > 0) ? true : false ;
        $error = '';

        // handle image upload if image is submitted
        if($isImageUpdated){
            if($_FILES)
            {
                $helper = new HelperImageUploader('partner_img');
                $files = $helper->process();

                if($files)
                {
                    $file = $files[0];
                    if(isset($file['save_path']))
                    {
                        if(!ImageManager::checkImageMemoryLimit($file['save_path']))
                            $error = Tools::displayError('Memory Limit reached');

                        if(!$error)
                        {
                            // using the timestamp to randomize the image name for avoiding naming collisions
                            $now = new DateTime();
                            $timestamp = $now->getTimestamp();
                            $final_img_name = $timestamp.'_'.$file['name'];

                            if(!ImageManager::resize($file['save_path'], dirname(__FILE__) . '/uploads/' . $final_img_name))
                                $error = Tools::DisplayError('An error occurred during the image upload');
                            else {
                                $partner->img = $final_img_name;
                                // deleting the old partner image if not the default.jpg
                                if($old_partner->img != 'default.jpg'){
                                    $old_img_path = dirname(__FILE__) . '/uploads/' .$old_partner->img;
                                    unlink($old_img_path);
                                }
                            }
                        }
                        unlink($file['save_path']);
                    }
                }
            }
        }

        $this->_html .= $error;
        $update_partner_sql = "
            UPDATE " ._DB_PREFIX_.$this->table_name ."
            SET name='" .$partner->name. "',"
            . ( $isImageUpdated ? "img='" .$partner->img. "'," : '') .
            "warehouse_id='" .$partner->warehouse_id. "'
             WHERE partner_id = '" .$partner->partner_id. "';
        ";
        $update_lang_sql ="";
        foreach($partner->description as $id_lang => $description){
            $update_lang_sql .= "
            UPDATE " ._DB_PREFIX_.$this->table_name ."_lang
            SET description='" .$description.
            "' WHERE id_partner = '" .$partner->partner_id. "'
            AND id_lang = '".$id_lang."';
            ";
        }
        Db::getInstance()->execute($update_partner_sql);
        Db::getInstance()->execute($update_lang_sql);
        $this->updating = false;
    }

    private function _getPartnerShowView($partner)
    {
        $module_path = _MODULE_DIR_.'/'.$this->name;
        // http://ecosapin.local/commandes/index.php?controller=AdminModules&configure=ecosapinpartners
        $token = Tools::getAdminTokenLite('AdminModules');
        $goBackUrl = _PS_BASE_URL_.__PS_BASE_URI__.'commandes/index.php?controller=AdminModules'
        .'&configure='.$this->name .'&token='.$token;
        $partner->description = $partner->description[$this->context->language->id];
        $this->context->smarty->assign('partner' , $partner);
        $this->context->smarty->assign('modulePath' , $module_path);
        $this->context->smarty->assign('goBackUrl' , $goBackUrl);

        return $this->display(__FILE__, 'showPartner.tpl');
    }

    private function _getPartnerForm(){

        $inputs = $this->_getPartnerFields();
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('add new partner')
                ],
                'input' => $inputs,
                'submit' => [
                    'title' => $this->l('add'),
                    'class' => 'btn btn-default pull-right'
                ]
            ]
        ];

        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));

        $helper = new HelperForm();
        $helper->submit_action = 'add_partner_action';
        $helper->default_form_language = $lang->id;
        $helper->currentIndex  = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='. $this->name .'&tab_module='. $this->tab .'&module_name='. $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $languages = $this->context->controller->getLanguages();
        $partner_description = [];
        foreach ($languages as $language){
            $partner_description[$language['id_lang']] = '';
        }

        $helper->tpl_vars = [
            'fields_value' => [
                'partner_id' => -1,
                'partner_name' => '',
                'partner_img' => '',
                'partner_description' => $partner_description,
                'warehouse_id' => 0
            ],
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        ];


        return $helper->generateForm([$form]);
    }

    private function _getPartnerEditForm(Partner $partner){
        if(!$partner)
            die("no partner has been selected");

        $inputs = $this->_getPartnerFields($partner->img);
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('update partner')
                ],
                'input' => $inputs,
                'submit' => [
                    'title' => $this->l('update'),
                    'class' => 'btn btn-default pull-right'
                ]
            ]
        ];
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));

        $helper = new HelperForm();
        $helper->submit_action = 'update_partner_action';
        $helper->default_form_language = $lang->id;
        $helper->currentIndex  = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='. $this->name .'&tab_module='. $this->tab .'&module_name='. $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => [
                'partner_id' => $partner->partner_id,
                'partner_name' => $partner->name,
                'partner_img' => $partner->img,
                'partner_description' => $partner->description,
                'warehouse_id' => $partner->warehouse_id
            ],
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        ];

        return $helper->generateForm([$form]);
    }

    private function _getPartnerList(){
        $partners = $this->_getPartners();

        $fields_list = array(
            'partner_id' => array(
                'title' => $this->l('id'),
                'type' => 'text',
            ),
            'partner_name' => array(
                'title' => $this->l('partner name'),
                'type' => 'text',
            ),
            'partner_img' => array(
                'title' => $this->l('has image?'),
                'type' => 'text',
            ),
            'warehouse_name' => array(
                'title' => $this->l('warehouse name'),
                'type' => 'text',
            ),
            'warehouse_reference' => array(
                'title' => $this->l('warehouse reference'),
                'type' => 'text',
            ),
        );

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->actions = array('edit', 'delete', 'view');
        $helper->identifier = 'partner_id';
        $helper->show_toolbar = true;
        $helper->title = 'Partners';
        $helper->table = $this->table_name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        return $helper->generateList($partners, $fields_list);
    }

    private function _getPartnerFields($partner_img = null){
        $getWarehousesQuery = "SELECT wh.id_warehouse, wh.reference FROM "._DB_PREFIX_."warehouse as wh";
        $warehouses =  Db::getInstance()->executeS($getWarehousesQuery);

        $file = "../modules/" .$this->name. "/uploads/" .$partner_img;

        return [
            [
                'name' => 'partner_id',
                'type' => 'hidden',
                'required' => false
            ],
            [
                'name' => 'partner_name',
                'type' => 'text',
                'label' => $this->l('Partner name'),
                'required' => true
            ],
            [
                'name' => 'partner_img',
                'type' => 'file',
                'label' => $this->l('Partner image'),
                'required' => false,
                'thumb' => file_exists($file) ? $file.'" width="30%' : '',
                'class' => 'sm'
            ],
            [
                'name' => 'partner_description',
                'type' => 'textarea',
                'label' => $this->l('Partner description'),
                'required' => true,
                'lang' => true,
                'autoload_rte' => true
            ],
            [
                'type' => 'select',
                'label' => $this->l('warehouse'),
                'desc' => $this->l('Choose the partners\' warehouse'),
                'name' => 'warehouse_id',
                'required' => true,
                'options' => [
                    'query' => $warehouses,
                    'id' => 'id_warehouse',
                    'name' => 'reference'
                ]
            ]

        ];
    }

    private function _addPartner($partner_name, $partner_img, $partner_description, $warehouse_id){
        $partner = new Partner();
        $partner->partner_id = 0;
        $partner->name = $partner_name;
        $partner->img = $partner_img;
        $partner->description = $partner_description[$this->context->language->id];
        $partner->warehouse_id = $warehouse_id;
        if(!$partner->save())
            return false;

        $inserted_id = Db::getInstance()->Insert_ID();
        $insert_land_description = "
              insert into "._DB_PREFIX_.$this->table_name."_lang(id_partner, id_lang, description) values ";
        $i = 0;
        foreach($partner_description as $id_lang => $description){
            $i++;
            $insert_land_description .= "('$inserted_id','$id_lang', '$description')";
            if($i != count($partner_description)) $insert_land_description .= ",";
        }
        return Db::getInstance()->execute($insert_land_description);
    }

    private function _getPartners(){
        $getPartnersQuery = "
              SELECT 
              p.partner_id, 
              p.name as partner_name,
              -- if the partner has an img other the default.png
              IF (p.img like 'default.jpg' OR p.img = '', 'No', 'Yes') as partner_img,
              p.description as partner_description,
              wh.name as warehouse_name,
              wh.reference as warehouse_reference
              FROM "._DB_PREFIX_."partners as p
              LEFT JOIN "._DB_PREFIX_."warehouse as wh ON (p.warehouse_id = wh.id_warehouse)
          ";
        return Db::getInstance()->executeS($getPartnersQuery);
    }

    private function _findPartner($id){
        $find_partner_sql = "
            SELECT p.*
            FROM " ._DB_PREFIX_.$this->table_name. " p
            WHERE p.partner_id = '$id'
        ";

        $result = Db::getInstance()->getRow($find_partner_sql);

        if(!$result)
            return null;

        $languages = $this->context->controller->getLanguages();
        $partner_description = [];
        foreach ($languages as $language){
            $get_description_sql = "
                select description from "._DB_PREFIX_.$this->table_name."_lang
                where id_partner = $id
                AND id_lang = ".$language['id_lang']."
            ";
            $partner_description[$language['id_lang']] = Db::getInstance()->getValue($get_description_sql);
        }

        $partner = new Partner();
        $partner->partner_id = $result['partner_id'];
        $partner->name = $result['name'];
        $partner->img = $result['img'];
        $partner->description = $partner_description;
        $partner->warehouse_id = $result['warehouse_id'];
        return $partner;
    }

    private function _deletePartner($id)
    {
        $partner_to_delete = $this->_findPartner($id);
        if(!$partner_to_delete){
            $this->_html .= $this->displayError($this->l("oops! partner not found!"));
            return false;
        }
        if($partner_to_delete->img != 'default.jpg'){
            $img_path = dirname(__FILE__) . '/uploads/' .$partner_to_delete->img;
            unlink($img_path);
        }

        $delete_partner_sql = "
            DELETE
            FROM " ._DB_PREFIX_.$this->table_name. "
            WHERE partner_id = '" .$id. "' ;
        ";

        $delete_partner_sql .= "
            DELETE
            FROM " ._DB_PREFIX_.$this->table_name. "_lang
            WHERE id_partner = '" .$id. "' ;
        ";
        return Db::getInstance()->execute($delete_partner_sql);
    }

    private function _initDatabase(){
        $query = "
            create table if not exists ". _DB_PREFIX_.$this->table_name." (
                partner_id int not null AUTO_INCREMENT,
                name varchar(64) not null,
                img varchar(255),
                description text,
                warehouse_id int not null,
                primary key (partner_id)
            );
            create table ps_partners_lang (
                id_partner_lang int AUTO_INCREMENT not null,
                id_partner int not null,
                id_lang int not null,
                description text not null,
                primary key(id_partner_lang, id_partner, id_lang)
            );
        ";
        return Db::getInstance()->execute($query);
    }

    private function _cleanDatabase(){
        $query = "drop table if exists ". _DB_PREFIX_.$this->table_name."_lang";
        $query2 = "drop table if exists ". _DB_PREFIX_.$this->table_name;
        return Db::getInstance()->execute($query) && Db::getInstance()->execute($query2);
    }

    public function install(){
        return parent::install()
        && $this->_initDatabase();
    }

    public function uninstall(){
        return parent::uninstall()
            && $this->_cleanDatabase();
    }



}

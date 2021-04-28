<?php


class SuiviCommandes extends Module
{
    
    const INSTALL_SQL_FILE = 'install.sql';
    
    public function __construct()
    {
        $this->name = 'suivicommandes';
        $this->version = '2.0';
        $this->author = 'Pulse Digital';
        $this->displayName = 'Module suivi des commandes';
        $this->bootstrap = true;
       
        parent::__construct();
    }
   
    public function install()
    {
       
        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))	{ return (false); }
            else if (!$sql = Tools::file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) { return (false); }
            $sql1 = str_replace('PREFIX_', _DB_PREFIX_, $sql);
            $sql2 = preg_split("/;\s*[\r\n]+/", $sql1);
            
            foreach ($sql2 as $query) {
                if (!Db::getInstance()->Execute(trim($query))) { return (false); }
            }
            
        $this->addTabStockSapinsVendusRestants();
        $this->addTabStockGlobalView();
        $this->registerHook('displayBackOfficeHeader');
        $this->installTab();

        if (!parent::install())
            return false;
        
        return true;
    }           
    
    public function uninstall()
    {
        Configuration::deleteByName('SUIVI_COMMANDES_TEXTES_OPTIMISATION');
        Configuration::deleteByName('SUIVI_COMMANDES_OSM_API_KEY');
        Configuration::deleteByName('SUIVI_COMMANDES_GOOGLE_API_KEY');
        Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'orders` DROP COLUMN `is_imported`');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'suivi_orders`');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'suivi_orders_carrier`');
        //$this->deleteTab(); 
        return parent::uninstall();
    }

    public function getContent()
    {
        $this->processConfiguration();
        $this->context->smarty->assign(array(
                'textes' => Configuration::get('SUIVI_COMMANDES_TEXTES_OPTIMISATION'),
                'osmkey' => Configuration::get('SUIVI_COMMANDES_OSM_API_KEY'),
                'gkey' => Configuration::get('SUIVI_COMMANDES_GOOGLE_API_KEY')
                ));
        return $this->display(__FILE__, 'getContent.tpl');
    }
    

    public function processConfiguration()
    {
        if (Tools::isSubmit('suivi_config_form')){
            $textes = Tools::getValue('textes');
            $osmkey = Tools::getValue('osmkey');
            $gkey = Tools::getValue('gkey');
            Configuration::updateValue('SUIVI_COMMANDES_TEXTES_OPTIMISATION', $textes);
            Configuration::updateValue('SUIVI_COMMANDES_OSM_API_KEY', $osmkey);
            Configuration::updateValue('SUIVI_COMMANDES_GOOGLE_API_KEY', $gkey);
            $this->context->smarty->assign('confirmation', 'ok');
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookDisplayBackOfficeHeader()
    {
            $this->context->controller->addJS($this->_path . 'views/js/Control.Geocoder.js');
            $this->context->controller->addJS($this->_path . 'views/js/leaflet-routing-machine.js');
            $this->context->controller->addJS($this->_path . 'views/js/leaflet-routing-machine.min.js');
            $this->context->controller->addCSS($this->_path . 'views/css/gmap.css');
            $this->context->controller->addCSS($this->_path . 'views/css/index.css');
    }

    private function installTab()
    {
        $tab = new Tab();
        $tab->class_name = 'AdminSuiviCommandes';
        $tab->module = $this->name;
        $tab->id_parent = 3;
        $tab->icon = 'settings_applications';
        $languages = Language::getLanguages();
        foreach ($languages as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('Suivi commandes');
        }
        try {
            $tab->save();
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function uninstallTab()
    {
        $idTab = (int)Tab::getIdFromClassName('AdminSuiviCommandes');
        if ($idTab) {
            $tab = new Tab($idTab);
            try {
                $tab->delete();
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
        return true;
    }

    public function addTabStockSapinsVendusRestants()
    {
        $tab = new Tab();
        $tab->class_name = 'AdminStockSapinsVendusRestants';
        $tab->module = 'suivicommandes';
        $tab->id_parent = 3;
        $langs = Language::getLanguages();
        foreach ($langs as $l)
        {
            $tab->name[$l['id_lang']] = 'Stock Sapins Vendus Restants';
        }
        return $tab->add();
    }

    public function addTabStockGlobalView()
    {
        $tab = new Tab();
        $tab->class_name = 'AdminStockGlobalView';
        $tab->module = 'suivicommandes';
        $tab->id_parent = 3;
        $langs = Language::getLanguages();
        foreach ($langs as $l)
        {
            $tab->name[$l['id_lang']] = 'Vue d\'ensemble du stock';
        }
        return $tab->add();
    }
    /*
    public function getTabId($className, $module)
	{
		$row = Db::getInstance()->getRow('SELECT `id_tab` FROM '._DB_PREFIX_.'tab WHERE `class_name` = "'.$className.'" AND `module` = "'.$module.'"');
		return ($row ? $row['id_tab'] : false);
	}

    public function deleteTab()
	{
		$idTab = $this->getTabId('AdminSuiviCommandes', 'suivicommandes');
		if ($idTab)
		{
			Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'tab WHERE `id_tab` = '.(int)($idTab));
			Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'tab_lang WHERE `id_tab` = '.(int)($idTab));
		}
	}    
    */    
}

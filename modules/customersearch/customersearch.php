<?php

class CustomerSearch extends Module {

    public function __construct()
    {
        $this->name = "customersearch";
        $this->displayName = $this->l("advanced customer filters");
        $this->tab = 'administration';
        $this->version = '0.1';
        $this->author = 'mouhcine@pulse.digital';
        $this->description = $this->l('advanced customer search');
        $this->bootstrap = true;
        parent::__construct();
    }

    public function install(){
        parent::install();
        if (!$this->installTab('AdminParentCustomer', 'AdminCustomersFilter', $this->l("Filter Customers")))
            return false;

        return true;
    }

    public function uninstall(){// Uninstall admin tab
        if (!$this->uninstallTab('AdminCustomersFilter'))
            return false;
        return parent::uninstall();
    }

    /*public function getContent()
	{
		return $this->display(__FILE__, 'controllers/admin/AdminCustomersFilterController.php');
	}*/

    /* Admin tabs*/
    public function installTab($parent, $class_name, $name)
    {
        // Create new admin tab
        $tab = new Tab();
        $tab->id_parent = (int)Tab::getIdFromClassName($parent);
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab->name[$lang['id_lang']] = $name;
        $tab->class_name = $class_name;
        $tab->module = $this->name;
        $tab->active = 1;
        return $tab->add();
    }

    public function uninstallTab($class_name)
    {
        // Retrieve Tab ID
        $id_tab = (int)Tab::getIdFromClassName($class_name);

        // Load tab
        $tab = new Tab((int)$id_tab);

        // Delete it
        return $tab->delete();
    }
}

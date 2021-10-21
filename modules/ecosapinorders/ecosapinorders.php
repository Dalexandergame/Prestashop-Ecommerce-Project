<?php

class EcosapinOrders extends Module
{

    public function __construct()
    {
        $this->name        = "ecosapinorders";
        $this->displayName = "Ecosapin filter products";
        $this->tab         = 'administration';
        $this->version     = '1.0';
        $this->author      = 'm.mouhcine@pulse.digital';
        $this->description = 'Show orders with no warehouse nor delivery/retour date attached';
        $this->bootstrap   = true;
        parent::__construct();
    }

    public function install()
    {
        parent::install();
        if (!$this->installTab('AdminParentOrders', 'AdminEcosapinNoDeliveryDateOrders', 'Commandes sans date livraison'))
            return false;
        if (!$this->installTab('AdminParentOrders', 'AdminEcosapinNoRetourDateOrders', 'Commandes sans retour'))
            return false;
        if (!$this->installTab('AdminParentOrders', 'AdminEcosapinNoWarehouseOrders', 'Commandes sans entrepôt'))
            return false;

        return true;
    }

    public function uninstall()
    {// Uninstall admin tab
        if (!$this->uninstallTab('AdminEcosapinNoDeliveryDateOrders'))
            return false;
        if (!$this->uninstallTab('AdminEcosapinNoRetourDateOrders'))
            return false;
        if (!$this->uninstallTab('AdminEcosapinNoWarehouseOrders'))
            return false;
        return parent::uninstall();
    }

    /* Admin tabs*/
    public function installTab($parent, $class_name, $name)
    {
        // Create new admin tab
        $tab            = new Tab();
        $tab->id_parent = 3;
        $tab->name      = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab->name[$lang['id_lang']] = $name;
        $tab->class_name = $class_name;
        $tab->module     = $this->name;
        $tab->active     = 1;
        return $tab->add();
    }

    public function uninstallTab($class_name)
    {
        // Retrieve Tab ID
        $id_tab = (int) Tab::getIdFromClassName($class_name);

        // Load tab
        $tab = new Tab((int) $id_tab);

        // Delete it
        return $tab->delete();
    }
    

}

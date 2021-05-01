<?php
/**
 * *
 *  2007-2018 PrestaShop
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Academic Free License (AFL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/afl-3.0.php
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *   @author    PrestaShop SA <contact@prestashop.com>
 *   @copyright 2007-2018 PrestaShop SA
 *   @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *   International Registered Trademark & Property of PrestaShop SA
 * /
 */

require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Model/AdvancedStockWarehouses.php';
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Model/AdvancedStockWarehousesProducts.php';
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Model/AdvancedStockWarehousesShops.php';

class AdminWarehousesHomeController extends ModuleAdminController
{
    public $w_id;
    public $warehouse;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'bms_advancedstock_warehouse';
        $this->className = 'AdvancedStockWarehouses';
        $this->identifier = 'w_id';
        $this->context = Context::getContext();
        $this->default_form_language = $this->context->language->id;
        parent::__construct();

        $this->setFieldsList();
        $this->override_folder = 'warehouse/';
    }

    public function init()
    {
        $this->w_id = (int) Tools::getValue('w_id', null);

        if (Tools::getIsset('createWH')) {
            if ($this->w_id) {
                $this->warehouse = new AdvancedStockWarehouses($this->w_id);
                $this->object = $this->warehouse;
            }
        }
        
        parent::init();
    }

    public function initContent()
    {
        parent::initContent();
        $this->setBmsMedia();
    }
    
    protected function setFieldsList()
    {
        $this->fields_list = array(
            'w_id' => array(
                'title' => $this->l('ID'),
                'width' => 35,
                'search' => true,
                'orderby' => true
            ),
            'w_name' => array(
                'title' => $this->l('Name'),
                'search' => true,
                'orderby' => true
            ),
            'w_city' => array(
                'title' => $this->l('City'),
                'search' => true,
                'orderby' => true
            ),
            'w_state' => array(
                'title' => $this->l('State'),
                'search' => true,
                'orderby' => true
            ),
            'country_name' => array(
                'title' => $this->l('Country'),
                'search' => true,
                'filter_key' => 'cl!name',
                'orderby' => true
            ),
            'w_is_active' => array(
                'title' => $this->l('Active'),
                'type' => 'bool',
                'align' => 'center',
                'search' => true,
                'orderby' => true
            ),
            'w_is_primary' => array(
                'title' => $this->l('Primary'),
                'type' => 'bool',
                'align' => 'center',
                'search' => true,
                'orderby' => true
            ),
            'ws_warehouse_id' => array(
                'title' => $this->l('Assignment'),
                'callback' => 'getWarehouseRolesForAllShops',
                'search' => false,
                'orderby' => false
            )
        );
    }
    
    public function renderList()
    {
        $this->toolbar_btn = array();
        $this->_select = 'cl.name as country_name, ws_warehouse_id';
        $this->_join = '
            LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (cl.`id_country` = `w_country` AND cl.`id_lang` = '.(int)$this->context->language->id.')
            LEFT JOIN `'._DB_PREFIX_.'bms_advancedstock_warehouse_shop` ws ON (ws.`ws_warehouse_id` = `w_id`)
            ';
        $this->_group = 'GROUP BY a.w_id';
        $this->_orderBy = 'w_id';
        $this->_orderWay = 'DESC';

        return parent::renderList();
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        $this->page_header_toolbar_title = $this->l('Warehouses');

        $this->page_header_toolbar_btn['Create'] = array(
            'href' => $this->context->link->getAdminLink('AdminWarehousesHome') . '&action=createWH&addbms_advancedstock_warehouse',
            'desc' => $this->l('Create warehouse'),
            'icon' => 'process-icon-new',
        );

        $this->context->smarty->assign('help_link', null);
    }

    public function renderForm()
    {
        if ($this->w_id) {
            $this->context->smarty->assign('w_id', $this->w_id);
        }

        $this->context->smarty->assign('controller_template', _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/warehouse/edit/');

        $this->fields_form = array(
            'tabs' => array(
                'general' => $this->l('General'),
                'address' => $this->l('Address'),
                'shops' => $this->l('Shops')
            ),
            'input' => array(),
            'submit' => array(
                'title' => $this->l('Save'),
                'stay' => true
            )
        );

        $kpis = $this->addKpis();
        $helperKpiRow = new HelperKpiRow();
        $helperKpiRow->kpis = $kpis;

        $this->setGeneralTab();
        $this->setAddressTab();

        $shops = Shop::getShopsCollection();
        foreach ($shops as $shop) {
            $this->setShopsTab($shop);
        }

        $this->setFormsValues();

        return $helperKpiRow->generate() . parent::renderForm();
    }

    protected function setGeneralTab()
    {
        array_push(
            $this->fields_form['input'],
            array(
                'type' => 'text',
                'label' => $this->l('Name'),
                'name' => 'w_name',
                'required' => true,
                'col' => 2,
                'tab' => 'general'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Contact'),
                'name' => 'w_contact',
                'col' => 2,
                'tab' => 'general'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Email'),
                'name' => 'w_email',
                'col' => 2,
                'validation' => 'isEmail',
                'tab' => 'general'
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Status'),
                'name' => 'w_is_active',
                'options' => array(
                    'query' => AdvancedStockWarehouses::getWhStatuses(),
                    'id' => 'code',
                    'name' => 'status'
                ),
                'tab' => 'general'
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Primary'),
                'name' => 'w_is_primary',
                'options' => array(
                    'query' => array(array("id"=>1,"name"=>"Yes"), array("id"=>0,"name"=>"No")),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'tab' => 'general'
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Display on front'),
                'name' => 'w_display_on_front',
                'options' => array(
                    'query' => array(array("id"=>1,"name"=>"Yes"), array("id"=>0,"name"=>"No")),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'tab' => 'general'
            ),
            array(
                'type' => 'textarea',
                'label' => $this->l('Notes'),
                'name' => 'w_notes',
                'required' => false,
                'autoload_rte' => false,
                'tab' => 'general'
            ),
            array(
                'type' => 'textarea',
                'label' => $this->l('Open Hours'),
                'name' => 'w_open_hours',
                'autoload_rte' => false,
                'tab' => 'general'
            )
        );
    }

    protected function setAddressTab()
    {
        $countries_array = array();
        $countries = Country::getCountries($this->context->language->id);
        foreach ($countries as $country) {
            $countries_array[$country['id_country']] = $country['name'];
        }

        array_push(
            $this->fields_form['input'],
            array(
                'type' => 'text',
                'label' => $this->l('Company name'),
                'name' => 'w_company_name',
                'col' => 2,
                'tab' => 'address'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Street1'),
                'name' => 'w_street1',
                'col' => 2,
                'tab' => 'address'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Street2'),
                'name' => 'w_street2',
                'col' => 2,
                'tab' => 'address'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Postcode'),
                'name' => 'w_postcode',
                'col' => 2,
                'tab' => 'address'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('City'),
                'name' => 'w_city',
                'col' => 2,
                'tab' => 'address'
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Country'),
                'name' => 'w_country',
                'col' => '4',
                'default_value' => (int)$this->context->country->id,
                'options' => array(
                    'query' => Country::getCountries($this->context->language->id),
                    'id' => 'id_country',
                    'name' => 'name'
                ),
                'tab' => 'address'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('State/Region'),
                'name' => 'w_state',
                'col' => 2,
                'tab' => 'address'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Phone'),
                'name' => 'w_telephone',
                'col' => 2,
                'tab' => 'address'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Fax'),
                'name' => 'w_fax',
                'col' => 2,
                'tab' => 'address'
            )
        );
    }

    protected function setShopsTab($shop)
    {
        array_push(
            $this->fields_form['input'],
            array(
                'type' => 'free',
                'label' => $this->l($shop->name),
                'name' => 'shop_'.(int)$shop->id,
                'tab' => 'shops'
            ),
            array(
                'type' => 'checkbox',
                'label' => $this->l('Assignments'),
                'name' => 'assignment_'.(int)$shop->id,
                'values'  => array(
                    'query' => AdvancedStockWarehousesShops::getRoles(),
                    'id'    => 'role',
                    'name'  => 'label'
                ),
                'tab' => 'shops',
                'hint' => $this->l('Select the roles to affect for the shop '. $shop->name)
            )
        );
    }

    protected function setFormsValues()
    {
        if (!$this->w_id) {
            $this->fields_value['w_is_active'] = 1;
        }

        $assignments = AdvancedStockWarehousesShops::getWarehouseRolesForAllShops($this->w_id);
        foreach ($assignments as $assignment) {
            $this->fields_value['assignment_' . $assignment['ws_shop_id'] . '_' .$assignment['ws_role']] = 'on';
        }
    }

    /**
     * @return false|ObjectModel|void
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function processSave()
    {
        $warehouse = parent::processSave();
        if ($warehouse) { //warehouse can be empty if an error occured
            if (!$warehouse->w_id) {
                $this->associateProductsToWarehouse($warehouse->id);
            }

            $postData = Tools::getAllValues();
            $originalAssignments = $this->retrieveOriginalAssignments();
            $newAssignments = $this->getNewAssignmentsFromPostData($postData);
            $assignmentsDiff = $this->buildArrayDiff($newAssignments, $originalAssignments);

            AdvancedStockWarehousesShops::deleteWarehouseAssignments($warehouse->id);
            $this->saveAssignments($newAssignments, $warehouse->id);

            if (!empty($assignmentsDiff)) {
                $this->updatePrestashopQuantities($assignmentsDiff);
            }
        }
    }

    /**
     * @param $postData array
     * @return array
     */
    protected function getNewAssignmentsFromPostData($postData)
    {
        $assignments = array();
        foreach ($postData as $field => $value) {
            if (strpos($field, 'assignment_') !== false && $value == 'on') {
                $assignments[] =  explode('_', str_replace('assignment_', '', $field));
            }
        }
        return $assignments;
    }

    /**
     * @return array
     * @throws PrestaShopDatabaseException
     */
    protected function retrieveOriginalAssignments()
    {
        $warehouseRoles = AdvancedStockWarehousesShops::getWarehouseRolesForAllShops($this->w_id);
        $assignments = array();
        foreach ($warehouseRoles as $warehouseRole) {
            $assignments[] = array($warehouseRole['ws_shop_id'], $warehouseRole['ws_role']);
        }
        return $assignments;
    }

    protected function buildArrayDiff($newAssignments, $originalAssignments)
    {
        return $this->restoreArrayAfterDiff(array_merge(
            array_diff(
                $this->prepareArrayForDiff($newAssignments),
                $this->prepareArrayForDiff($originalAssignments)
            ),
            array_diff(
                $this->prepareArrayForDiff($originalAssignments),
                $this->prepareArrayForDiff($newAssignments)
            )
        ));
    }

    protected function prepareArrayForDiff($array)
    {
        foreach ($array as $key => $value) {
            $array[$key] = json_encode($value);
        }
        return $array;
    }

    protected function restoreArrayAfterDiff($array)
    {
        foreach ($array as $key => $value) {
            $array[$key] = json_decode($value);
        }
        return $array;
    }

    /**
     * @param $assignmentsDiff
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    protected function updatePrestashopQuantities($assignmentsDiff)
    {
        foreach ($assignmentsDiff as $assignment) {
            //if sales assignment has not been changed
            if ($assignment[1] !== '1') {
                return;
            }
            $productsData = AdvancedStockWarehousesProducts::getProductIds();
            foreach ($productsData as $productData) {
                AdvancedStockWarehouseProduct::updatePrestashopAvailableQties(
                    (int)$productData['wi_product_id'],
                    (int)$productData['wi_attribute_id'],
                    $this->w_id,
                    $assignment[0]
                );
            }
        }
    }

    protected function associateProductsToWarehouse($warehouseId)
    {
        $productsData = AdvancedStockWarehousesProducts::getProductIds();
        foreach ($productsData as $productData) {
            try {
                AdvancedStockWarehousesProducts::create(
                    (int)$productData['wi_product_id'],
                    (int)$productData['wi_attribute_id'],
                    (int)$warehouseId
                );
            } catch (PrestaShopException $e) {
                Logger::addLog($e->getMessage());
            }
        }
    }

    protected function saveAssignments($assignments, $warehouseId)
    {
        foreach ($assignments as $assignment) {
            $warehouseShopObj = new AdvancedStockWarehousesShops();
            $warehouseShopObj->ws_warehouse_id = (int)$warehouseId;
            $warehouseShopObj->ws_shop_id = (int)$assignment[0];
            $warehouseShopObj->ws_role = (int)$assignment[1];
            $warehouseShopObj->save();
        }
    }

    public function setBmsMedia()
    {
        if ($this->w_id) {
            Media::addJsDef(array(
                'AdminWarehousesProductTabLabel' => $this->l('Products'),
                'AdminWarehousesProductTabUrl' => $this->context->link->getAdminLink('AdminWarehousesProductTab') . ($this->w_id ? '&w_id=' . $this->w_id : ''),
                'AdminWarehousesImportExportTabLabel' => $this->l('Import/Export'),
                'AdminWarehousesImportExportTabUrl' => $this->context->link->getAdminLink('AdminWarehousesImportExportTab') . ($this->w_id ? '&w_id=' . $this->w_id : ''),
                'dateFormat' => Tools::displayDate(date('Y-m-d')),
                'w_id' => $this->w_id
            ));
            $this->addJquery();
            $this->addJqueryUi('ui.tabs');
            $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/tabs.js');
            $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/iframe.js');
            $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/warehouse.js');
            $this->addCSS(_PS_MODULE_DIR_ . $this->module->name . '/views/css/tab.css');
        }
    }

    protected function addKpis()
    {
        $kpis = array();
        $helperKpi = new HelperKpi();
        $helperKpi->id = 'box-total-quantity';
        $helperKpi->icon = 'icon-search';
        $helperKpi->color = 'color1';
        $helperKpi->title = $this->l('Total Quantity');
        $helperKpi->subtitle = $this->l('Sum of products quantities');
        $helperKpi->source = $this->context->link->getAdminLink('AdminWarehousesHome').'&ajax=1&action=getKpi&kpi=total-quantity&w_id='.(int)$this->w_id;
        $kpis[] = $helperKpi->generate();

        $helperKpi = new HelperKpi();
        $helperKpi->id = 'box-references';
        $helperKpi->icon = 'icon-list';
        $helperKpi->color = 'color1';
        $helperKpi->title = $this->l('References');
        $helperKpi->subtitle = $this->l('Unique references');
        $helperKpi->source = $this->context->link->getAdminLink('AdminWarehousesHome').'&ajax=1&action=getKpi&kpi=references&w_id='.(int)$this->w_id;
        $kpis[] = $helperKpi->generate();

        $helperKpi = new HelperKpi();
        $helperKpi->id = 'box-stock-value';
        $helperKpi->icon = 'icon-money';
        $helperKpi->color = 'color1';
        $helperKpi->title = $this->l('Stock Value');
        $helperKpi->subtitle = $this->l('Product cost x Quantity');
        $helperKpi->source = $this->context->link->getAdminLink('AdminWarehousesHome').'&ajax=1&action=getKpi&kpi=stock-value&w_id='.(int)$this->w_id;
        $kpis[] = $helperKpi->generate();

        return $kpis;
    }

    public function displayAjaxGetKpi()
    {
        $tooltip = null;
        $warehouseId = (int)Tools::getValue('w_id');
        switch (Tools::getValue('kpi')) {
            case 'total-quantity':
                $value = $this->getTotalQuantity($warehouseId) ? $this->getTotalQuantity($warehouseId) : '0 '; //hack to display value "0"
                break;
            case 'references':
                $value = $this->getSumOfProductReferencesInStock($warehouseId) ? $this->getSumOfProductReferencesInStock($warehouseId) : '0 ';
                break;
            case 'stock-value':
                $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
                $value = Tools::displayPrice($this->getStockValue($warehouseId), $currency);
                break;
            default:
                $value = false;
        }

        if ($value == false) {
            die(json_encode(array('has_errors' => true)));
        }

        $array = array('value' => $value, 'tooltip' => $tooltip);
        die(json_encode($array));
    }

    protected function getTotalQuantity($warehouseId)
    {
        return AdvancedStockWarehousesProducts::getTotalQuantity($warehouseId);
    }

    protected function getSumOfProductReferencesInStock($warehouseId)
    {
        return AdvancedStockWarehousesProducts::getSumOfProductReferencesInStock($warehouseId);
    }

    protected function getStockValue($warehouseId)
    {
        return AdvancedStockWarehousesProducts::getStockValue($warehouseId);
    }

    public function getWarehouseRolesForAllShops($warehouseId)
    {
        $warehouseRoles = AdvancedStockWarehousesShops::getWarehouseRolesForAllShops($warehouseId);
        $roles = AdvancedStockWarehousesShops::getRolesArray();

        $this->context->smarty->assign('warehouseRoles', $warehouseRoles);
        $this->context->smarty->assign('roles', $roles);
        return $this->createTemplate('assignment.tpl')->fetch();
    }
}

<?php
/*
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
require_once _PS_MODULE_DIR_ . 'gszonevente/models/Region.php';
class AdminWarehousesController extends AdminWarehousesControllerCore {
    /**
     * AdminController::renderForm() override
     * @see AdminController::renderForm()
     */
    /*
    * module: gszonevente
    * date: 2021-10-13 10:32:32
    * version: 1.0.0
    */
    public function renderForm() {
        if (!($obj = $this->loadObject(true)))
            return;
        $query = new DbQuery();
        $query->select('id_employee, CONCAT(lastname," ",firstname) as name');
        $query->from('employee');
        $query->where('active = 1');
        $employees_array = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        if (Tools::isSubmit('add' . $this->table))
            $this->toolbar_title = $this->l('Stock: Create a warehouse');
        else
            $this->toolbar_title = $this->l('Stock: Warehouse management');
        $tmp_addr = new Address();
        $res = $tmp_addr->getFieldsRequiredDatabase();
        $required_fields = array();
        foreach ($res as $row)
            $required_fields[(int) $row['id_required_field']] = $row['field_name'];
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Warehouse information'),
                'icon' => 'icon-pencil'
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_address',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Reference'),
                    'name' => 'reference',
                    'maxlength' => 32,
                    'required' => true,
                    'hint' => $this->l('Reference for this warehouse.'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'maxlength' => 45,
                    'required' => true,
                    'hint' => array(
                        $this->l('Name of this warehouse.'),
                        $this->l('Invalid characters:') . ' !&lt;&gt;,;?=+()@#"�{}_$%:',
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Phone'),
                    'name' => 'phone',
                    'maxlength' => 16,
                    'hint' => $this->l('Phone number for this warehouse.'),
                    'required' => in_array('phone', $required_fields)
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Mobile phone'),
                    'name' => 'phone_mobile',
                    'required' => in_array('phone_mobile', $required_fields),
                    'maxlength' => 16,
                    'hint' => $this->l('Mobile phone number for this supplier.')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Address'),
                    'name' => 'address',
                    'maxlength' => 128,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Address') . ' (2)',
                    'name' => 'address2',
                    'maxlength' => 128,
                    'hint' => $this->l('Complementary address (optional).'),
                    'required' => in_array('address2', $required_fields)
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Zip/postal code'),
                    'name' => 'postcode',
                    'maxlength' => 12,
                    'required' => in_array('postcode', $required_fields)
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('City'),
                    'name' => 'city',
                    'maxlength' => 32,
                    'required' => true,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Country'),
                    'name' => 'id_country',
                    'required' => true,
                    'default_value' => (int) $this->context->country->id,
                    'options' => array(
                        'query' => Country::getCountries($this->context->language->id, false),
                        'id' => 'id_country',
                        'name' => 'name',
                    ),
                    'hint' => $this->l('Country of location of the warehouse.')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('State'),
                    'name' => 'id_state',
                    'required' => true,
                    'options' => array(
                        'query' => array(),
                        'id' => 'id_state',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Manager'),
                    'name' => 'id_employee',
                    'required' => true,
                    'options' => array(
                        'query' => $employees_array,
                        'id' => 'id_employee',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'swap',
                    'label' => $this->l('Carriers'),
                    'name' => 'ids_carriers',
                    'required' => false,
                    'multiple' => true,
                    'options' => array(
                        'query' => Carrier::getCarriers($this->context->language->id, false, false, false, null, Carrier::ALL_CARRIERS),
                        'id' => 'id_reference',
                        'name' => 'name'
                    ),
                    'hint' => array(
                        $this->l('Associated carriers.'),
                        $this->l('If you do not select any carrier, none will be able to ship from this warehouse.'),
                        $this->l('You can specify the number of carriers available to ship orders from particular warehouses.'),
                    ),
                    'desc' => $this->l('You must select at least one carrier to enable shipping from this warehouse. Use CTRL+Click to select more than one carrier.'),
                ),
                array(
                    'type' => 'swap',
                    'label' => $this->l('Region'),
                    'name' => 'ids_regions',
                    'required' => false,
                    'multiple' => true,
                    'options' => array(
                        'query' => Region::getRegions(),
                        'id' => 'id_gszonevente_region',
                        'name' => 'name'
                    ),
                    'hint' => array(
                        $this->l('Associated carriers.'),
                        $this->l('If you do not select any carrier, none will be able to ship from this warehouse.'),
                        $this->l('You can specify the number of carriers available to ship orders from particular warehouses.'),
                    ),
                    'desc' => $this->l('You must select at least one carrier to enable shipping from this warehouse. Use CTRL+Click to select more than one carrier.'),
                ),
            ),
        );
        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
                'disable_shared' => Shop::SHARE_STOCK
            );
        }
        if (Tools::isSubmit('addwarehouse') || Tools::isSubmit('submitAddwarehouse')) {
            $this->fields_form['input'][] = array(
                'type' => 'select',
                'label' => $this->l('Management type'),
                'hint' => $this->l('Inventory valuation method. Be careful! You won\'t be able to change this value later!'),
                'name' => 'management_type',
                'required' => true,
                'options' => array(
                    'query' => array(
                        array(
                            'id' => 'WA',
                            'name' => $this->l('Weighted Average')
                        ),
                        array(
                            'id' => 'FIFO',
                            'name' => $this->l('First In, First Out')
                        ),
                        array(
                            'id' => 'LIFO',
                            'name' => $this->l('Last In, First Out')
                        ),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
            );
            $this->fields_form['input'][] = array(
                'type' => 'select',
                'label' => $this->l('Stock valuation currency'),
                'hint' => $this->l('Be careful! You won\'t be able to change this value later!'),
                'name' => 'id_currency',
                'required' => true,
                'options' => array(
                    'query' => Currency::getCurrencies(),
                    'id' => 'id_currency',
                    'name' => 'name'
                )
            );
        } else { // else hide input
            $this->fields_form['input'][] = array(
                'type' => 'hidden',
                'name' => 'management_type'
            );
            $this->fields_form['input'][] = array(
                'type' => 'hidden',
                'name' => 'id_currency'
            );
        }
        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );
        $address = null;
        if ($obj->id_address > 0)
            $address = new Address($obj->id_address);
        $shops = $obj->getShops();
        $ids_shop = array();
        foreach ($shops as $shop)
            $ids_shop[] = $shop['id_shop'];
        $carriers = $obj->getCarriers(true);
        $regions = array();
        if ($address != null)
            $this->fields_value = array(
                'id_address' => $address->id,
                'phone' => $address->phone,
                'address' => $address->address1,
                'address2' => $address->address2,
                'postcode' => $address->postcode,
                'city' => $address->city,
                'id_country' => $address->id_country,
                'id_state' => $address->id_state,
            );
        else // loads default country
            $this->fields_value = array(
                'id_address' => 0,
                'id_country' => Configuration::get('PS_COUNTRY_DEFAULT')
            );
        foreach (Region::getRegionsByWarehouse($obj->id) as $region) {
            $regions[] = $region['id_gszonevente_region'];
        }

        $this->fields_value['ids_shops[]'] = $ids_shop;
        $this->fields_value['ids_carriers'] = $carriers;
        $this->fields_value['ids_regions'] = $regions;
        if (!Validate::isLoadedObject($obj))
            $this->fields_value['id_currency'] = (int) Configuration::get('PS_CURRENCY_DEFAULT');
        return $this->myRenderForm();
    }
    /**
     * Function used to render the form for this controller
     */
    /*
    * module: gszonevente
    * date: 2021-10-13 10:32:32
    * version: 1.0.0
    */
    public function myRenderForm() {
        if (!$this->default_form_language)
            $this->getLanguages();
        if (Tools::getValue('submitFormAjax'))
            $this->content .= $this->context->smarty->fetch('form_submit_ajax.tpl');
        if ($this->fields_form && is_array($this->fields_form)) {
            if (!$this->multiple_fieldsets)
                $this->fields_form = array(array('form' => $this->fields_form));
            if (is_array($this->fields_form_override) && !empty($this->fields_form_override))
                $this->fields_form[0]['form']['input'] = array_merge($this->fields_form[0]['form']['input'], $this->fields_form_override);
            $fields_value = $this->getFieldsValue($this->object);
            Hook::exec('action' . $this->controller_name . 'FormModifier', array(
                'fields' => &$this->fields_form,
                'fields_value' => &$fields_value,
                'form_vars' => &$this->tpl_form_vars,
            ));
            $helper = new HelperForm($this);
            $this->setHelperDisplay($helper);
            $helper->fields_value = $fields_value;
            $helper->submit_action = $this->submit_action;
            $helper->tpl_vars = $this->getTemplateFormVars();
            $helper->show_cancel_button = (isset($this->show_form_cancel_button)) ? $this->show_form_cancel_button : ($this->display == 'add' || $this->display == 'edit');
            $back = Tools::safeOutput(Tools::getValue('back', ''));
            if (empty($back))
                $back = self::$currentIndex . '&token=' . $this->token;
            if (!Validate::isCleanHtml($back))
                die(Tools::displayError());
            $helper->back_url = $back;
            !is_null($this->base_tpl_form) ? $helper->base_tpl = $this->base_tpl_form : '';
            if ($this->tabAccess['view']) {
                if (Tools::getValue('back'))
                    $helper->tpl_vars['back'] = Tools::safeOutput(Tools::getValue('back'));
                else
                    $helper->tpl_vars['back'] = Tools::safeOutput(Tools::getValue(self::$currentIndex . '&token=' . $this->token));
            }
            $form = $helper->generateForm($this->fields_form);
            return $form;
        }
    }
    /**
     * @see AdminController::afterAdd()
     * Called once $object is set.
     * Used to process the associations with address/shops/carriers
     */
    /*
    * module: gszonevente
    * date: 2021-10-13 10:32:32
    * version: 1.0.0
    */
    protected function afterAdd($object) {
        $address = new Address($object->id_address);
        if (Validate::isLoadedObject($address)) {
            $address->id_warehouse = (int) $object->id;
            $address->save();
        }
        if (Tools::isSubmit('ids_carriers_selected'))
            $object->setCarriers(Tools::getValue('ids_carriers_selected'));
        if (Tools::isSubmit('ids_regions_selected'))
            $object->setRegions(Tools::getValue('ids_regions_selected'));
        return true;
    }
    /*
    * module: gszonevente
    * date: 2021-10-13 10:32:32
    * version: 1.0.0
    */
    public function processAdd() {
        if (Tools::isSubmit('submitAdd' . $this->table)) {
            if (!($obj = $this->loadObject(true)))
                return;
            $this->updateAddress();
            $this->deleted = false;
            return parent::processAdd();
        }
    }
    /**
     * @see AdminController::processDelete();
     */
    /*
    * module: gszonevente
    * date: 2021-10-13 10:32:32
    * version: 1.0.0
    */
    public function processDelete() {
        if (Tools::isSubmit('delete' . $this->table)) {
            if (!($obj = $this->loadObject(true)))
                return;
            elseif ($obj->getQuantitiesOfProducts() > 0) // not possible : products
                $this->errors[] = $this->l('It is not possible to delete a warehouse when there are products in it.');
            elseif (SupplyOrder::warehouseHasPendingOrders($obj->id)) // not possible : supply orders
                $this->errors[] = $this->l('It is not possible to delete a Warehouse if it has pending supply orders.');
            else { // else, it can be deleted
                $address = new Address($obj->id_address);
                $address->deleted = 1;
                $address->save();
                $obj->setCarriers(array());
                $obj->setRegions(array());
                $obj->resetProductsLocations();
                return parent::processDelete();
            }
        }
    }
    /**
     * @see AdminController::processUpdate();
     */
    /*
    * module: gszonevente
    * date: 2021-10-13 10:32:32
    * version: 1.0.0
    */
    public function processUpdate() {
        if (!($obj = $this->loadObject(true)))
            return;
        $this->updateAddress();
        $obj->setCarriers(Tools::getValue('ids_carriers_selected'), array());
        $obj->setRegions(Tools::getValue('ids_regions_selected'), array());

        return parent::processUpdate();
    }
}

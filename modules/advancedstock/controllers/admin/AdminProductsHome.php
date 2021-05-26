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

require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Model/AdvancedStockWarehousesProducts.php';
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Model/AdvancedStockWarehouses.php';
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Model/AdvancedStockStockMovements.php';
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Helper/AdvancedStockUtils.php';

class AdminProductsHomeController extends ModuleAdminController
{
    protected $wi_id;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'bms_advancedstock_warehouse_product';
        $this->className = 'AdvancedStockWarehousesProducts';
        $this->identifier = 'wi_id';
        $this->context = Context::getContext();
        $this->lang = false;

        parent::__construct();

        $this->setFieldsList();
        $this->override_folder = 'product/';
    }

    public function init()
    {
        if (Tools::getIsset('wi_id')) {
            $this->wi_id = (int)Tools::getValue('wi_id', null);
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
        $fields = array(
            'image' => array(
                'title' => $this->l('Image'),
                'image' => 'p',
                'image_id' => 'id_image',
                'align' => 'center',
                'search' => false,
                'orderby' => false,
                'filter' => false
            ),

            'wi_product_id' => array(
                'title' => $this->l('Product Id'),
                'align' => 'left',
                'width' => 50,
                'search' => true,
            ),

            'product_full_name' => array(
                'title' => $this->l('Product'),
                'align' => 'left',
                'filter_key' => 'pl!name',
                'search' => true,
                'orderby' => true,
                'callback' => 'getAdvancedStockProductLink'
            ),

            'reference' => array(
                'title' => $this->l('Reference'),
                'align' => 'left',
                'filter_key' => 'p!reference',
                'search' => true,
                'orderby' => true
            ),

            'barcode' => array(
                'title' => $this->l('Barcode'),
                'align' => 'left',
                'filter_key' => 'barcode',
                'havingFilter' => true,
                'search' => true,
                'orderby' => false
            ),


        );

        $warehouses = AdvancedStockWarehouses::getAllWarehouses();
        $warehouseFields = array();
        foreach ($warehouses as $wId => $wName) {
            $tmp = array(
                'w_'.$wId => array(
                    'title' => 'Stock '.$this->l($wName),
                    'align' => 'left',
                    'search' => false,
                    'orderby' => false,
                    'callback' => 'getStocksDetailsHtml'
                )
            );
            $warehouseFields = array_merge($warehouseFields, $tmp);
        }

        $this->fields_list = array_merge($fields, $warehouseFields);
    }

    public function renderList()
    {
        $this->toolbar_btn = array();

        $quantitiesPerWarehouse = '';
        $wLeftJoin = '';
        foreach (AdvancedStockWarehouses::getAllWarehouses() as $warehouseId => $warehouseName) {
            $quantitiesPerWarehouse .= $warehouseId.' AS w_'.$warehouseId.', w_'.$warehouseId.'_pq, w_'.$warehouseId.'_aq, w_'.$warehouseId.'_qts,';
            $wLeftJoin .= 'LEFT JOIN (' .
                'SELECT wp.wi_product_id as product_id,
                wp.wi_attribute_id as pa_id,
                wp.wi_physical_quantity AS w_'.$warehouseId.'_pq,
                wp.wi_available_quantity AS w_'.$warehouseId.'_aq,
                wp.wi_quantity_to_ship AS w_'.$warehouseId.'_qts ' .
                'FROM ' . _DB_PREFIX_ . 'bms_advancedstock_warehouse_product as wp ' .
                'WHERE wp.wi_warehouse_id = ' .pSQL($warehouseId) . ' ' .
                'GROUP BY wp.wi_product_id, wp.wi_attribute_id, wp.wi_warehouse_id ' .
                ') AS c'.$warehouseId.' ON c'.$warehouseId.'.product_id = a.wi_product_id AND c'.$warehouseId.'.pa_id = a.wi_attribute_id ';
        }

        $this->_select = "`wi_id`," .
            $quantitiesPerWarehouse .
            "wi_product_id," .
            "wi_attribute_id," .
            "id_image,".
            "IF(pa.ean13 != '', pa.ean13, IF(p.ean13 != '', p.ean13, IF(pa.upc != '', pa.upc, p.upc))) as barcode,".
            "IF(b.attribute_full_name IS NULL, pl.name, CONCAT(pl.name, ' ', b.attribute_full_name)) as product_full_name,".
            "IF(pa.reference != '', pa.reference, p.reference) as reference,";

        $this->_join = 'INNER JOIN ' . _DB_PREFIX_ . 'product p on wi_product_id = p.id_product '.
            'LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (wi_product_id = pl.id_product and id_shop = '. (int) $this->context->shop->id.' and id_lang = '. (int) $this->context->language->id.') '.
            'LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (image_shop.`id_product` = wi_product_id AND image_shop.`cover` = 1 AND image_shop.id_shop = ' . (int) $this->context->shop->id . ') '.
            'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON (pa.id_product = wi_product_id AND pa.id_product_attribute = wi_attribute_id)'.
            'LEFT JOIN (' .
                "SELECT wp.wi_product_id as product_id, wp.wi_attribute_id as pa_id, GROUP_CONCAT(al.name SEPARATOR ' ') as attribute_full_name ".
                'FROM ' . _DB_PREFIX_ . 'bms_advancedstock_warehouse_product as wp ' .
                'LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_combination as pac ON (pac.`id_product_attribute` = `wp`.`wi_attribute_id`) ' .
                'LEFT JOIN ' . _DB_PREFIX_ . 'attribute_lang as al ON al.`id_attribute` = pac.`id_attribute` AND id_lang = '.(int)$this->context->language->id.' ' .
                'GROUP BY wp.wi_attribute_id, wp.wi_warehouse_id ' .
                'HAVING wp.wi_attribute_id > 0' .
            ') AS b ON b.product_id = a.wi_product_id AND b.pa_id = a.wi_attribute_id '.
            $wLeftJoin;

        $this->_group = 'GROUP BY wi_product_id, wi_attribute_id';
        $this->_orderBy = 'wi_product_id';
        $this->_orderWay = 'DESC';

        //fix for JS issue with gamification module
        $html = parent::renderList();
        $tpl = $this->context->smarty->createTemplate(_PS_MODULE_DIR_. $this->module->name . '/views/templates/admin/fix_gamification.tpl');
        $html .= $tpl->fetch();

        return $html;
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        $this->page_header_toolbar_title = $this->l('Products') . (
            $this->wi_id ? (' - ' . AdvancedStockWarehousesProducts::getProductFullName($this->wi_id)) : ''
        );

        if ($this->wi_id) {
            $wp = new AdvancedStockWarehousesProducts($this->wi_id);
            $productId = $wp->wi_product_id;
            $this->page_header_toolbar_btn['prestashopProductView'] = array(
                'href' => AdvanceStockCompatibility::getProductLink($productId, $this->context),
                'desc' => $this->l('View Product'),
                'icon' => 'process-icon-export',
            );
        }

        $this->context->smarty->assign('help_link', null);
    }

    public function getStocksDetailsHtml($warehouseId, $line)
    {
        $this->context->smarty->assign('physicalQuantity', $line['w_'.$warehouseId.'_pq']);
        $this->context->smarty->assign('availableQuantity', $line['w_'.$warehouseId.'_aq']);
        $this->context->smarty->assign('quantityToShip', $line['w_'.$warehouseId.'_qts']);
        $this->context->smarty->assign('class', $line['w_'.$warehouseId.'_aq'] > 0 ? 'instock' : 'outofstock');
        return $this->createTemplate('stocks.tpl')->fetch();
    }

    public function getAdvancedStockProductLink($name, $line)
    {
        $linkUrl = AdvanceStockCompatibility::getAdvancedStockProductLink((int)$line['wi_id'], $this->context);
        return AdvancedStockUtils::getLink($name, $linkUrl);
    }

    public function renderForm()
    {
        if ($this->wi_id) {
            $this->context->smarty->assign('wi_id', $this->wi_id);
        }

        $this->context->smarty->assign('controller_template', _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/warehouse/edit/');

        $this->fields_form = array(
            'tabs' => array(
                'new_stock_movement' => $this->l('New Stock Movement')
            ),
            'input' => array(),
            'submit' => array(
                'title' => $this->l('Save'),
                'stay' => true
            )
        );

        $this->setNewStockMovementTab();

        return parent::renderForm();
    }

    public function setNewStockMovementTab()
    {
        array_push(
            $this->fields_form['input'],
            array(
                'type' => 'hidden',
                'label' => $this->l('Product ID'),
                'name' => 'wi_product_id',
                'required' => true,
                'tab' => 'new_stock_movement'
            ),
            array(
                'type' => 'hidden',
                'label' => $this->l('Product Attribute ID'),
                'name' => 'wi_attribute_id',
                'required' => true,
                'tab' => 'new_stock_movement'
            ),
            array(
                'type' => 'select',
                'label' => $this->l('From'),
                'name' => 'w_name_from',
                'options' => array(
                    'query' => AdvancedStockWarehouses::getWarehousesOptions(true),
                    'id' => 'code',
                    'name' => 'warehouse'
                ),
                'required' => true,
                'tab' => 'new_stock_movement'
            ),
            array(
                'type' => 'select',
                'label' => $this->l('To'),
                'name' => 'w_name_to',
                'options' => array(
                    'query' => AdvancedStockWarehouses::getWarehousesOptions(true),
                    'id' => 'code',
                    'name' => 'warehouse'
                ),
                'required' => true,
                'tab' => 'new_stock_movement'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Quantity'),
                'name' => 'quantity',
                'required' => true,
                'col' => 2,
                'tab' => 'new_stock_movement'
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Type'),
                'name' => 'type',
                'options' => array(
                    'query' => $this->getStockMovementTypes(),
                    'id' => 'type',
                    'name' => 'label'
                ),
                'required' => true,
                'tab' => 'new_stock_movement'
            ),
            array(
                'type' => 'textarea',
                'label' => $this->l('Comment'),
                'name' => 'comment',
                'rows' => 4,
                'cols' => 5,
                'hint' => $this->l('200 characters max'),
                'tab' => 'new_stock_movement'
            )
        );

        $product = new AdvancedStockWarehousesProducts($this->wi_id);
        $productId = $product->wi_product_id;
        $attributeId = $product->wi_attribute_id;
        $this->fields_value['wi_product_id'] = $productId;
        $this->fields_value['wi_attribute_id'] = $attributeId;
    }

    public function setBmsMedia()
    {
        $this->addCSS(_PS_MODULE_DIR_ . $this->module->name . '/views/css/stocks.css');

        if (!$this->wi_id) {
            return;
        }
        Media::addJsDef(array(
            'AdminProductWarehousesTabLabel' => $this->l('Warehouses'),
            'AdminProductWarehousesTabUrl' => $this->context->link->getAdminLink('AdminProductWarehousesTab') . ($this->wi_id ? '&wi_id=' . $this->wi_id : ''),
            'AdminProductOrdersTabLabel' => $this->l('Sales Orders'),
            'AdminProductOrdersTabUrl' => $this->context->link->getAdminLink('AdminProductOrdersTab') . ($this->wi_id ? '&wi_id=' . $this->wi_id : ''),
            'AdminProductStockMovementsTabLabel' => $this->l('Stock Movements'),
            'AdminProductStockMovementsTabUrl' => $this->context->link->getAdminLink('AdminProductStockMovementsTab') . ($this->wi_id ? '&wi_id=' . $this->wi_id : ''),
            'AdminStockMovementAlertEmptyWarehouses' => $this->l('Please set at least one warehouse'),
            'AdminStockMovementAlertSameWarehouses' => $this->l('Both warehouses cannot be the same'),
            'AdminStockMovementAlertWrongQty' => $this->l('Please set a positive quantity')

        ));

        if (AdvanceStockCompatibility::procurementIsInstalled()) {
            $warehouseProduct = new AdvancedStockWarehousesProducts($this->wi_id);
            $productId = $warehouseProduct->wi_product_id;
            $attributeId = $warehouseProduct->wi_attribute_id;

            Media::addJsDef(array(
                'AdminProductPurchaseOrdersTabLabel' => $this->l('Purchase Orders'),
                'AdminProductPurchaseOrdersTabUrl' => $this->context->link->getAdminLink('AdminProcurementPurchaseOrderHistory') . '&id_product=' . $productId,
            ));
        } else {
            Media::addJsDef(array(
                'AdminProductPurchaseOrdersTabLabel' => false,
                'AdminProductPurchaseOrdersTabUrl' => false,
            ));
        }

        $this->addJquery();
        $this->addJqueryUI('ui.tabs');
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/tabs.js');
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/iframe.js');
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/product.js');
        $this->addCSS(_PS_MODULE_DIR_ . $this->module->name . '/views/css/tab.css');
    }

    /**
     * @throws \Exception
     */
    public function processSave()
    {
        $keys = array(
            'wi_product_id',
            'wi_attribute_id',
            'w_name_from',
            'w_name_to',
            'quantity',
            'type',
            'comment'
        );

        $sm = array();
        foreach ($keys as $key) {
            $sm[$key] = Tools::getValue($key);
        }

        try {
            AdvancedStockStockMovements::create(
                $sm['wi_product_id'],
                $sm['wi_attribute_id'],
                $sm['w_name_from'],
                $sm['w_name_to'],
                $sm['quantity'],
                $sm['type'],
                $sm['comment']
            );
        } catch (PrestaShopException $e) {
            $this->errors[] = $e->getMessage();
        }

        return parent::processSave();
    }

    protected function getStockMovementTypes()
    {
        return array(
            array('type' => AdvancedStockStockMovements::TYPE_SYSTEM, 'label' => $this->l('System')),
            array('type' => AdvancedStockStockMovements::TYPE_SHIPMENT, 'label' => $this->l('Shipment')),
            array('type' => AdvancedStockStockMovements::TYPE_ADJUSTMENT, 'label' => $this->l('Adjustment')),
            array('type' => AdvancedStockStockMovements::TYPE_CREDITMEMO, 'label' => $this->l('Credit Memo')),
            array('type' => AdvancedStockStockMovements::TYPE_PURCHASEORDER, 'label' => $this->l('Purchase Order')),
            array('type' => AdvancedStockStockMovements::TYPE_RECEPTION, 'label' => $this->l('Reception'))
        );
    }
}

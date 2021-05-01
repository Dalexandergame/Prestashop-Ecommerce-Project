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

require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Model/AdvancedStockStockMovements.php';
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Model/AdvancedStockWarehouses.php';
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Helper/AdvancedStockUtils.php';

class AdminStockMovementsHomeController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'bms_advancedstock_stock_movement';
        $this->className = 'AdvancedStockStockMovements';
        $this->identifier = 'sm_id';
        $this->context = Context::getContext();
        parent::__construct();

        $this->setFieldsList();
        $this->override_folder = 'stock_movement/';
    }

    public function setFieldsList()
    {
        $this->fields_list = array(
            'sm_date' => array(
                'title' => $this->l('Date'),
                'type' => 'datetime',
                'search' => true,
                'orderby' => true
            ),
            'reference' => array(
                'title' => $this->l('Reference'),
                'filter_key' => 'p!reference',
                'search' => true,
                'orderby' => true
            ),
            'product_full_name' => array(
                'title' => $this->l('Product'),
                'filter_key' => 'pl!name',
                'search' => true,
                'orderby' => true
            ),
            'sm_source_warehouse_id' => array(
                'title' => $this->l('From'),
                'align' => 'center',
                'type' => 'select',
                'list' => AdvancedStockWarehouses::getAllWarehouses(),
                'filter_key' => 'sm_source_warehouse_id',
                'callback' => 'getWarehouseNameById',
                'search' => true,
                'orderby' => true
            ),
            'sm_target_warehouse_id' => array(
                'title' => $this->l('To'),
                'align' => 'center',
                'type' => 'select',
                'list' => AdvancedStockWarehouses::getAllWarehouses(),
                'filter_key' => 'sm_target_warehouse_id',
                'callback' => 'getWarehouseNameById',
                'search' => true,
                'orderby' => true
            ),
            'icon' => array(
                'title' => $this->l(''),
                'align' => 'center',
                'callback' => 'getStockMovementIcon',
                'orderby' => false,
                'search' => false
            ),
            'sm_qty' => array(
                'title' => $this->l('Quantity'),
                'align' => 'center',
                'search' => true,
                'orderby' => true
            ),
            'sm_type' => array(
                'title' => $this->l('Type'),
                'align' => 'center',
                'type' => 'select',
                'list' => $this->getStockMovementTypes(),
                'filter_key' => 'sm_type',
                'callback' => 'getTypeLabelById',
                'search' => true,
                'orderby' => true
            ),
            'sm_comment' => array(
                'title' => $this->l('Comment'),
                'search' => true,
                'orderby' => true
            )
        );
    }

    public function renderList()
    {
        $this->toolbar_btn = array();
        $this->list_no_link = true;

        $this->_select = "p.reference as product_reference,".
            "IF(b.attribute_full_name IS NULL, pl.name, CONCAT(pl.name, ' ', b.attribute_full_name)) as product_full_name,".
            "IF(pa.reference != '', pa.reference, p.reference) as reference,".
            "IF(sm_target_warehouse_id != 0,IF(sm_source_warehouse_id != 0, 'forward', 'increase'), 'decrease') AS icon ";

        $this->_join = 'INNER JOIN ' . _DB_PREFIX_ . 'product p on sm_product_id = p.id_product '.
            'LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl ON (sm_product_id = pl.id_product and id_shop = '. (int) $this->context->shop->id.' and id_lang = '. (int) $this->context->language->id.') '.
            'LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute pa ON (pa.id_product = sm_product_id AND pa.id_product_attribute = sm_attribute_id)'.
            'LEFT JOIN (' .
                "SELECT wp.wi_product_id as product_id, wp.wi_attribute_id as pa_id, GROUP_CONCAT(al.name SEPARATOR ' ') as attribute_full_name ".
                'FROM ' . _DB_PREFIX_ . 'bms_advancedstock_warehouse_product as wp ' .
                'LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_combination as pac ON (pac.id_product_attribute = wp.wi_attribute_id) ' .
                'LEFT JOIN ' . _DB_PREFIX_ . 'attribute_lang as al ON al.id_attribute = pac.id_attribute ' .
                'GROUP BY wp.wi_attribute_id, wp.wi_warehouse_id ' .
                'HAVING wp.wi_attribute_id > 0' .
            ') AS b ON b.product_id = a.sm_product_id AND b.pa_id = a.sm_attribute_id ';

        $this->_group = 'GROUP BY sm_id';
        $this->_orderBy = 'sm_id';
        $this->_orderWay = 'DESC';
        return parent::renderList();
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        $this->page_header_toolbar_title = $this->l('Stock Movements');
        $this->context->smarty->assign('help_link', null);
    }

    public function getWarehouseNameById($warehouseId)
    {
        return AdvancedStockWarehouses::getWarehouseNameById($warehouseId);
    }

    public function getTypeLabelById($typeId)
    {
        $types = $this->getStockMovementTypes();
        return $types[$typeId];
    }

    public function getStockMovementIcon($icon)
    {
        return AdvancedStockUtils::getStockMovementIcon($icon);
    }

    protected function getStockMovementTypes()
    {
        return array(
            AdvancedStockStockMovements::TYPE_SYSTEM => $this->l('System'),
            AdvancedStockStockMovements::TYPE_SHIPMENT => $this->l('Shipment'),
            AdvancedStockStockMovements::TYPE_ADJUSTMENT => $this->l('Adjustment'),
            AdvancedStockStockMovements::TYPE_CREDITMEMO => $this->l('Credit Memo'),
            AdvancedStockStockMovements::TYPE_PURCHASEORDER => $this->l('Purchase Order'),
            AdvancedStockStockMovements::TYPE_RECEPTION => $this->l('Reception')
        );
    }
}

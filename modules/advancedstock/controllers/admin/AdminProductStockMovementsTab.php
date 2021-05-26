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
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Model/AdvancedStockWarehousesProducts.php';
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Helper/AdvancedStockUtils.php';

class AdminProductStockMovementsTabController extends ModuleAdminController
{
    protected $wi_id;

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

    public function init()
    {
        if (Tools::getIsset('wi_id')) {
            $this->wi_id = (int)Tools::getValue('wi_id', null);
        }
        parent::init();
    }

    public function postProcess()
    {
        self::$currentIndex .= '&wi_id='. $this->wi_id;
        return parent::postProcess();
    }

    public function initProcess()
    {
        $this->display_header = false;
        $this->display_header_javascript = true;
        $this->display_footer = false;
        $this->content_only = false;
        $this->lite_display = true;

        parent::initProcess();
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
        $wi = new AdvancedStockWarehousesProducts($this->wi_id);
        $productId = $wi->wi_product_id;
        $productAttributeId = $wi->wi_attribute_id;

        $this->_select = "IF(sm_target_warehouse_id != 0,IF(sm_source_warehouse_id != 0, 'forward', 'increase'), 'decrease') AS icon ";
        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'bms_advancedstock_warehouse_product` wp ON (wi_attribute_id = sm_attribute_id AND wi_product_id = sm_product_id)';
        $this->_where = ' AND wi_product_id = ' . (int)$productId . ' AND wi_attribute_id = ' . (int)$productAttributeId;
        $this->_group = 'GROUP BY sm_id';
        $this->_orderBy = 'sm_id';
        $this->_orderWay = 'DESC';

        //fix for JS issue with gamification module
        $html = parent::renderList();
        $tpl = $this->context->smarty->createTemplate(_PS_MODULE_DIR_. $this->module->name . '/views/templates/admin/fix_gamification.tpl');
        $html .= $tpl->fetch();

        return $html;
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

    private function getStockMovementTypes()
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

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
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Helper/AdvancedStockWarehouseProduct.php';
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Model/AdvancedStockWarehousesShops.php';

class AdminProductWarehousesTabController extends ModuleAdminController
{
    protected $wi_id;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'bms_advancedstock_warehouse_product';
        $this->className = 'AdvancedStockWarehousesProducts';
        $this->identifier = 'wi_id';
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

    public function initProcess()
    {
        $this->display_header = false;
        $this->display_header_javascript = true;
        $this->display_footer = false;
        $this->content_only = false;
        $this->lite_display = true;

        parent::initProcess();
    }

    public function postProcess()
    {
        self::$currentIndex .= '&wi_id='. $this->wi_id;
        return parent::postProcess();
    }

    protected function setFieldsList()
    {
        $this->fields_list = array(
            'w_name' => array(
                'title' => $this->l('Warehouse'),
                'type' => 'select',
                'list' => AdvancedStockWarehouses::getAllWarehouses(),
                'filter_key' => 'w_id',
                'align' => 'center',
                'search' => true,
                'orderby' => true
            ),
            'wi_physical_quantity' => array(
                'title' => $this->l('Physical Quantity'),
                'align' => 'center',
                'search' => true,
                'orderby' => true,
            ),
            'wi_available_quantity' => array(
                'title' => $this->l('Available Quantity'),
                'align' => 'center',
                'search' => true,
                'orderby' => true,
            ),
            'wi_quantity_to_ship' => array(
                'title' => $this->l('Quantity To Ship'),
                'align' => 'center',
                'search' => true,
                'orderby' => true,
            ),
            'wi_reserved_quantity' => array(
                'title' => $this->l('Reserved Quantity'),
                'align' => 'center',
                'search' => true,
                'orderby' => true,
            ),
            'shelf_location' => array(
                'title' => $this->l('Shelf Location'),
                'align' => 'center',
                'filter_key' => 'wi_shelf_location',
                'search' => true,
                'callback' => 'getShelfLocation',
            ),
        );
    }

    public function renderList()
    {
        $this->list_no_link = true;
        $this->toolbar_btn = array();

        $wi = new AdvancedStockWarehousesProducts($this->wi_id);
        $productId = $wi->wi_product_id;
        $productAttributeId = $wi->wi_attribute_id;

        $this->_select = 'w_name,' .
            'wi_physical_quantity,' .
            'wi_available_quantity,' .
            'wi_quantity_to_ship,' .
            'wi_reserved_quantity,' .
            "IFNULL(wi_shelf_location, '') as shelf_location," .
            'reference';

        $this->_join = 'INNER JOIN ' . _DB_PREFIX_ . 'product p on wi_product_id = p.id_product '.
            'INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps ON p.id_product = ps.id_product '.
            'LEFT JOIN `' . _DB_PREFIX_ . 'bms_advancedstock_warehouse` wh ON (wi_warehouse_id = wh.w_id) ';

        $this->_where = ' AND wi_product_id = ' . (int)$productId . ' AND wi_attribute_id = ' . (int)$productAttributeId . ' AND ps.id_shop = '.(int)$this->context->shop->id;

        $this->_orderBy = 'wi_product_id';


        $html = parent::renderList();

        //fix for JS issue with gamification module
        $tpl = $this->context->smarty->createTemplate(_PS_MODULE_DIR_. $this->module->name . '/views/templates/admin/fix_gamification.tpl');
        $html .= $tpl->fetch();

        return $html;
    }

    public function getShelfLocation($sl, $object)
    {
        $context = Context::getContext();
        $tpl = $context->smarty->createTemplate(_PS_MODULE_DIR_. $this->module->name . '/views/templates/admin/input/wi_shelf_location.tpl');
        $tpl->assign('wi_shelf_location', $sl);
        $tpl->assign('productId', $object['wi_product_id']);
        $tpl->assign('warehouseId', $object['wi_warehouse_id']);
        $tpl->assign('wi_id', $object['wi_id']);
        $tpl->assign('productAttributeId', $object['wi_attribute_id']);
        return $tpl->fetch();
    }

    public function ajaxPreProcess()
    {
        AdvancedStockWarehouseProduct::ajaxUpdateWhProduct();
    }

    public function setBmsMedia()
    {
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/saveInput.js');

        Media::addJsDef(array(
            'ajaxUpdateWhProduct' => $this->context->link->getAdminLink('AdminProductWarehousesTab', true),
        ));
    }
}

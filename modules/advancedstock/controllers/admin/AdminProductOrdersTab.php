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
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Helper/AdvancedStockUtils.php';

class AdminProductOrdersTabController extends ModuleAdminController
{
    protected $wi_id;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'order';
        $this->className = 'Order';
        $this->identifier = 'id_order';
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

    protected function setFieldsList()
    {
        $this->fields_list = array(
            'order_id' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'search' => true,
                'orderby' => true
            ),
            'order_ref' => array(
                'title' => $this->l('Reference'),
                'align' => 'center',
                'search' => true,
                'orderby' => true,
                'callback' => 'getOrderLink'
            ),
            'customer_name' => array(
                'title' => $this->l('Customer'),
                'align' => 'center',
                'search' => true,
                'orderby' => true,
            ),
            'state' => array(
                'title' => $this->l('Status'),
                'align' => 'center',
                'search' => true,
                'orderby' => true,
            ),
            'w_name' => array(
                'title' => $this->l('Warehouse'),
                'align' => 'center',
                'search' => true,
                'orderby' => true,
            ),
            'eod_qty_to_ship' => array(
                'title' => $this->l('Quantity to Ship'),
                'align' => 'center',
                'search' => true,
                'orderby' => true,
                'width' => 50
            ),
            'eod_reserved_qty' => array(
                'title' => $this->l('Reserved quantity'),
                'align' => 'center',
                'search' => true,
                'orderby' => true
            ),
        );
    }

    /**
     * @return false|string
     * @throws \PrestaShopException
     */
    public function renderList()
    {
        $this->toolbar_btn = array();
        $wi = new AdvancedStockWarehousesProducts($this->wi_id);
        $productId = $wi->wi_product_id;
        $productAttributeId = $wi->wi_attribute_id;

        $this->_select =
            'a.id_order as order_id,' .
            'a.reference as order_ref,' .
            "CONCAT(firstname, ' ', lastname) AS customer_name," .
            'osl.name AS state,' .
            'eod_qty_to_ship,' .
            'eod_reserved_qty, ' .
            'w_name';

        $this->_join = 'INNER JOIN ' . _DB_PREFIX_ . 'order_detail od ON od.id_order = a.id_order '.
            'LEFT JOIN `' . _DB_PREFIX_ . 'bms_advancedstock_extended_order_detail` eod ON (eod.eod_order_detail_id = od.id_order_detail) '.
            'INNER JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.id_customer  = a.id_customer) '.
            'LEFT JOIN `' . _DB_PREFIX_ . 'bms_advancedstock_warehouse` wh ON (eod_warehouse_id = w_id) '.
            'LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (osl.id_order_state = a.current_state and osl.id_lang = '.(int)$this->context->language->id.') '.

        $this->_where = ' AND od.product_id = ' . (int)$productId . ' AND od.product_attribute_id = ' . (int)$productAttributeId . ' and a.id_shop = '.(int)$this->context->shop->id;

        $this->_orderBy = 'product_id';

        //fix for JS issue with gamification module
        $html = parent::renderList();
        $tpl = $this->context->smarty->createTemplate(_PS_MODULE_DIR_. $this->module->name . '/views/templates/admin/fix_gamification.tpl');
        $html .= $tpl->fetch();

        return $html;
    }

    public function getOrderLink($name, $line)
    {
        $linkUrl = AdvanceStockCompatibility::getOrderLink((int)$line['order_id'], $this->context);

        return AdvancedStockUtils::getLink($name, $linkUrl);
    }
}

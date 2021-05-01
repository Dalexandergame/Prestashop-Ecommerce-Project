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
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Model/AdvancedStockStockMovements.php';
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Model/AdvancedStockWarehouses.php';
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Helper/AdvancedStockUtils.php';
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Helper/AdvancedStockWarehouseProduct.php';

class AdminMassStockEditorHomeController extends ModuleAdminController
{
    public $w_id;
    public $wh;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'bms_advancedstock_warehouse_product';
        $this->className = 'AdvancedStockWarehousesProducts';
        $this->identifier = 'wi_id';

        parent::__construct();
        $this->setFieldsList();
        $this->override_folder = 'input/';
    }

    public function initContent()
    {
        parent::initContent();
        $this->setBmsMedia();
    }

    protected function setFieldsList()
    {
        $this->fields_list = array(
            'wi_product_id' => array(
                'title' => $this->l('Product Id'),
                'align' => 'center',
                'search' => true
            ),
            'wi_attribute_id' => array(
                'title' => $this->l('Attribute Id'),
                'align' => 'center',
                'search' => true,
            ),
            'image' => array(
                'title' => $this->l('Image'),
                'image' => 'p',
                'image_id' => 'id_image',
                'align' => 'center',
                'search' => false,
                'orderby' => false,
                'filter' => false
            ),
            'reference' => array(
                'title' => $this->l('Reference'),
                'filter_key' => 'p!reference',
                'search' => true,
                'orderby' => true
            ),
            'product_full_name' => array(
                'title' => $this->l('Product Name'),
                'filter_key' => 'pl!name',
                'search' => true,
                'orderby' => true,
                'callback' => 'getProductLink'
            ),
            'w_name' => array(
                'title' => $this->l('Warehouse'),
                'align' => 'center',
                'type' => 'select',
                'list' => AdvancedStockWarehouses::getAllWarehouses(),
                'filter_key' => 'w_id',
                'search' => true,
                'orderby' => true
            ),
            'wi_physical_quantity' => array(
                'title' => $this->l('Physical Quantity'),
                'align' => 'center',
                'search' => true,
                'callback' => 'getQtyInWh',
                'hint' => $this->l('Update the value and click outside of the text box to save it')
            ),
            'wi_available_quantity' => array(
                'title' => $this->l('Available Quantity'),
                'align' => 'center',
                'search' => true
            ),
            'shelfLocation' => array(
                'title' => $this->l('Shelf Location'),
                'align' => 'center',
                'search' => true,
                'filter_key' => 'wi_shelf_location',
                'callback' => 'getShelfLocation',
                'hint' => $this->l('Update the value and click outside of the text box to save it')
            ),
            'barcode' => array(
                'title' => $this->l('Barcode'),
                'align' => 'center',
                'filter_key' => 'barcode',
                'havingFilter' => true,
                'search' => true,
                'orderby' => false
            ),
        );
    }

    public function getQtyInWh($qty, $object)
    {
        $tpl = $this->createTemplate('wi_physical_quantity.tpl');
        $tpl->assign('wi_physical_quantity', $qty);
        $tpl->assign('productId', $object['wi_product_id']);
        $tpl->assign('warehouseId', $object['wi_warehouse_id']);
        $tpl->assign('wi_id', $object['wi_id']);
        $tpl->assign('productAttributeId', $object['wi_attribute_id']);
        return $tpl->fetch();
    }

    public function getShelfLocation($sf, $object)
    {
        $tpl = $this->createTemplate('wi_shelf_location.tpl');
        $tpl->assign('wi_shelf_location', $sf);
        $tpl->assign('productId', $object['wi_product_id']);
        $tpl->assign('warehouseId', $object['wi_warehouse_id']);
        $tpl->assign('wi_id', $object['wi_id']);
        $tpl->assign('productAttributeId', $object['wi_attribute_id']);
        return $tpl->fetch();
    }

    public function getProductLink($name, $line)
    {
        $linkUrl = AdvanceStockCompatibility::getProductLink((int)$line['wi_product_id'], $this->context);

        return AdvancedStockUtils::getLink($name, $linkUrl);
    }

    public function renderList()
    {
        $this->toolbar_btn = array();
        $this->tpl_list_vars['title'] = $this->l('Products');
        $this->list_no_link = true;

        $this->_select = "id_image, pl.name,
            w_name,
            w_id,
            IFNULL(wi_shelf_location, '') as shelfLocation,
            IF(b.attribute_full_name IS NULL, pl.name, CONCAT(pl.name, ' ', b.attribute_full_name)) as product_full_name,
            IF(pa.reference != '', pa.reference, p.reference) as reference,
            IF(pa.ean13 != '', pa.ean13, IF(p.ean13 != '', p.ean13, IF(pa.upc != '', pa.upc, p.upc))) as barcode";

        $this->_join = 'INNER JOIN ' . _DB_PREFIX_ . 'product p on wi_product_id = p.id_product '.
            'LEFT JOIN `' . _DB_PREFIX_ . 'bms_advancedstock_warehouse` wh ON (wi_warehouse_id = wh.w_id) '.
            'LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (wi_product_id = pl.id_product and id_shop = '.(int)$this->context->shop->id.' and id_lang = '.(int)$this->context->language->id.') '.
            'LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (image_shop.`id_product` = wi_product_id AND image_shop.`cover` = 1 AND image_shop.id_shop = ' . (int)$this->context->shop->id . ') '.
            'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON (pa.`id_product` = wi_product_id AND pa.`id_product_attribute` = wi_attribute_id)'.
            'LEFT JOIN (' .
                "SELECT wp.wi_product_id as product_id, wp.wi_attribute_id as pa_id, GROUP_CONCAT(al.name SEPARATOR ' ') as attribute_full_name ".
                'FROM ' . _DB_PREFIX_ . 'bms_advancedstock_warehouse_product as wp ' .
                'LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_combination as pac ON (pac.`id_product_attribute` = `wp`.`wi_attribute_id`) ' .
                'LEFT JOIN ' . _DB_PREFIX_ . 'attribute_lang as al ON al.`id_attribute` = pac.`id_attribute` AND id_lang = '.(int)$this->context->language->id.' ' .
                'GROUP BY wp.wi_attribute_id, wp.wi_warehouse_id ' .
                'HAVING wp.wi_attribute_id > 0' .
            ') AS b ON b.product_id = a.wi_product_id AND b.pa_id = a.wi_attribute_id';

        $this->_group = 'GROUP BY wi_product_id, wi_attribute_id, wi_warehouse_id';

        return parent::renderList();
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        $this->page_header_toolbar_title = $this->l('Mass Stock Editor');
        $this->context->smarty->assign('help_link', null);
    }

    public function ajaxPreProcess()
    {
        AdvancedStockWarehouseProduct::ajaxUpdateWhProduct();
    }

    public function setBmsMedia()
    {
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/saveInput.js');
        
        Media::addJsDef(array(
            'ajaxUpdateWhProduct' => $this->context->link->getAdminLink('AdminMassStockEditorHome', true),
        ));
    }
}

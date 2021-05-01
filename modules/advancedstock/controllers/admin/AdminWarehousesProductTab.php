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
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Helper/AdvanceStockCompatibility.php';
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Helper/AdvancedStockUtils.php';

class AdminWarehousesProductTabController extends ModuleAdminController
{
    protected $w_id;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'bms_advancedstock_warehouse_product';
        $this->className = 'AdvancedStockWarehousesProducts';
        $this->identifier = 'wi_id';
        $this->context = Context::getContext();
        parent::__construct();

        $this->setFieldsList();
        $this->override_folder = 'product/';
    }

    public function init()
    {
        parent::init();

        if (Tools::getIsset('w_id')) {
            $this->w_id = (int)Tools::getValue('w_id');
            $this->context->smarty->assign("w_id", $this->w_id);
        } else {
            throw new PrestaShopException($this->l('Missing parameter w_id'));
        }
    }

    public function postProcess()
    {
        self::$currentIndex .= '&w_id='. $this->w_id;
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
                'align' => 'center',
                'search' => true,
                'orderby' => true,
                'filter_key' => 'p!reference',
            ),
            'product_full_name' => array(
                'title' => $this->l('Name'),
                'filter_key' => 'pl!name',
                'search' => true,
                'orderby' => true,
                'callback' => 'getProductLink'
            ),
            'wi_physical_quantity' => array(
                'title' => $this->l('Physical Quantity'),
                'align' => 'center',
                'search' => true
            ),
            'wi_available_quantity' => array(
                'title' => $this->l('Available Quantity'),
                'align' => 'center',
                'search' => true
            ),
            'wi_quantity_to_ship' => array(
                'title' => $this->l('Quantity To Ship'),
                'align' => 'center',
                'search' => true
            ),
            'wi_shelf_location' => array(
                'title' => $this->l('Shelf Location'),
                'align' => 'center',
                'search' => true
            ),
            'wholesale_price' => array(
                'title' => $this->l('Cost'),
                'type' => 'price',
                'align' => 'center',
                'search' => false
            ),
            'total_value' => array(
                'title' => $this->l('Total Value'),
                'type' => 'price',
                'align' => 'center',
                'search' => false
            ),
        );
    }

    public function renderList()
    {
        $this->toolbar_btn = array();
        $this->tpl_list_vars['title'] = $this->l('Products');
        $this->list_no_link = true;

        $this->_select = "`wi_id`," .
            "wi_product_id," .
            "wi_attribute_id," .
            "id_image,".
            "IF(b.attribute_full_name IS NULL, pl.name, CONCAT(pl.name, ' ', b.attribute_full_name)) as product_full_name,".
            "IF(pa.reference != '', pa.reference, p.reference) as reference,".
            "IF(pa.`wholesale_price` > 0, pa.`wholesale_price`,p.`wholesale_price`) as `wholesale_price`,".
            "(`wi_physical_quantity`*IF(pa.`wholesale_price` > 0, pa.`wholesale_price`, p.`wholesale_price`)) as total_value";

        $this->_join = 'INNER JOIN ' . _DB_PREFIX_ . 'product p on wi_product_id = p.id_product '.
            'LEFT JOIN `' . _DB_PREFIX_ . 'bms_advancedstock_warehouse` wh ON (wi_warehouse_id = wh.w_id) '.
            'LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (wi_product_id = pl.id_product and id_shop = '. (int)$this->context->shop->id .' and id_lang = '. (int)$this->context->language->id .') '.
            'LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (image_shop.`id_product` = wi_product_id AND image_shop.`cover` = 1 AND image_shop.id_shop = ' . (int)$this->context->shop->id . ') '.
            'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON (pa.`id_product` = wi_product_id AND pa.`id_product_attribute` = wi_attribute_id)'.
            'LEFT JOIN (' .
                "SELECT wp.wi_product_id as product_id, wp.wi_attribute_id as pa_id, GROUP_CONCAT(al.name SEPARATOR ' ') as attribute_full_name ".
                'FROM ' . _DB_PREFIX_ . 'bms_advancedstock_warehouse_product as wp ' .
                'LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_combination as pac ON (pac.`id_product_attribute` = `wp`.`wi_attribute_id`) ' .
                'LEFT JOIN ' . _DB_PREFIX_ . 'attribute_lang as al ON al.`id_attribute` = pac.`id_attribute` AND al.id_lang = '.(int)$this->context->language->id.' ' .
                ($this->w_id ? 'WHERE wp.wi_warehouse_id = ' . pSQL($this->w_id) . ' ' : '') .
                'GROUP BY wp.wi_attribute_id ' .
                'HAVING wp.wi_attribute_id > 0' .
            ') AS b ON b.product_id = a.wi_product_id AND b.pa_id = a.wi_attribute_id';

        if ($this->w_id) {
            $this->_where = 'AND wi_warehouse_id = ' . (int)$this->w_id;
        }

        $this->_orderBy = 'wi_product_id';

        //fix for JS issue with gamification module
        $html = parent::renderList();
        $tpl = $this->context->smarty->createTemplate(_PS_MODULE_DIR_. $this->module->name . '/views/templates/admin/fix_gamification.tpl');
        $html .= $tpl->fetch();

        return $html;
    }

    public function processExport($textDelimiter = '"')
    {
        if (Tools::getIsset('w_id')) {
            $warehouseId = Tools::getValue('w_id');
        } else {
            throw new PrestaShopException($this->l('Missing parameter w_id'));
        }

        $export_dir = defined('_PS_HOST_MODE_') ? _PS_ROOT_DIR_.'/export/' : _PS_ADMIN_DIR_.'/export/';
        $file = 'export_products_warehouse_'.$warehouseId.'.csv';

        if (!$csv = @fopen($export_dir.$file, 'w')) {
            $this->errors[] = $this->l('An error occurred during the file generation. Check the permissions of the /export directory.');
            return;
        }

        $data = $this->getProductInfoByWarehouse($warehouseId);
        $this->buildCsv($csv, $data, $textDelimiter);

        if (!file_exists($export_dir.$file)) {
            $this->errors[] = $this->l('There is no export file to download.');
            return;
        }

        $fileSize = filesize($export_dir.$file);
        $uploadMaxFileSize = Tools::convertBytes(ini_get('upload_max_filesize'));

        if ($fileSize > $uploadMaxFileSize) {
            $this->errors[] = $this->l('The export file size exceeds the upload_max_filesize setting and cannot be downloaded.');
            return;
        }

        if (Configuration::get('PS_ENCODING_FILE_MANAGER_SQL')) {
            $charset = Configuration::get('PS_ENCODING_FILE_MANAGER_SQL');
        } else {
            $charset = 'utf-8';
        }

        header('Content-Type: text/csv; charset='.$charset);
        header('Cache-Control: no-store, no-cache');
        header('Content-Disposition: attachment; filename="'.$file.'"');
        header('Content-Length: '.$fileSize);
        readfile($export_dir.$file);
        die();
    }

    public function getProductLink($name, $line)
    {
        $linkUrl = AdvanceStockCompatibility::getProductLink((int)$line['wi_product_id'], $this->context);

        return AdvancedStockUtils::getLink($name, $linkUrl);
    }

    protected function getProductInfoByWarehouse($warehouseId)
    {

        $sql ="SELECT wi_product_id,
               a.wi_attribute_id,
               IF(pa.reference, pa.reference, p.reference) AS reference,
               IF(b.attribute_full_name IS NULL, pl.name, CONCAT(pl.name, ' ', b.attribute_full_name)) as product_full_name,
               a.wi_physical_quantity,
               a.wi_available_quantity,
               a.wi_reserved_quantity,
               a.wi_shelf_location,
               IF(pa.wholesale_price > 0, pa.wholesale_price, p.wholesale_price) AS wholesale_price,
               (wi_physical_quantity * IF(pa.wholesale_price > 0, pa.wholesale_price, p.wholesale_price)) AS total_value ".
            'FROM '. _DB_PREFIX_ .'bms_advancedstock_warehouse_product as a '.
            'INNER JOIN '._DB_PREFIX_.'product p ON a.wi_product_id = p.id_product '.
                'LEFT JOIN ( ' .
                "SELECT wp.wi_product_id as product_id, wp.wi_attribute_id as pa_id, GROUP_CONCAT(al.name SEPARATOR ' ') as attribute_full_name ".
                'FROM ' ._DB_PREFIX_.'bms_advancedstock_warehouse_product as wp ' .
                'LEFT JOIN ' ._DB_PREFIX_.'product_attribute_combination as pac ON (pac.`id_product_attribute` = `wp`.`wi_attribute_id`) ' .
                'LEFT JOIN ' ._DB_PREFIX_.'attribute_lang as al ON al.`id_attribute` = pac.`id_attribute` and al.id_lang = '.(int)Context::getContext()->language->id.' '.
                'GROUP BY wp.wi_attribute_id, wp.wi_warehouse_id ' .
                'HAVING wp.wi_attribute_id > 0 AND wi_warehouse_id ='.(int)$warehouseId.' '.') AS b
                ON b.product_id = a.wi_product_id AND b.pa_id = a.wi_attribute_id ' .
            'LEFT JOIN '._DB_PREFIX_.'product_lang pl ON a.wi_product_id = pl.id_product and id_shop = '.(int)Context::getContext()->shop->id.' and id_lang = '.(int)Context::getContext()->language->id.' '.
            'LEFT JOIN '._DB_PREFIX_ .'product_attribute pa ON pa.`id_product` = a.wi_product_id AND pa.`id_product_attribute` = a.wi_attribute_id '.
            'WHERE (wi_warehouse_id ='.(int)$warehouseId.')';



        return Db::getInstance()->executeS($sql);
    }

    protected function buildCsv($csv, $data, $textDelimiter)
    {
        $headers = array_keys($data[0]);
        $fields = array();
        foreach ($headers as $header) {
            $fields[] = $header;
            fputs($csv, $header . AdvanceStockCompatibility::CSV_DELIMITER);
        }

        foreach ($data as $result) {
            fputs($csv, "\n");
            foreach ($fields as $field) {
                fputs($csv, $textDelimiter . strip_tags($result[$field]) . $textDelimiter . AdvanceStockCompatibility::CSV_DELIMITER);
            }
        }
    }
}

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
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Model/AdvancedStockStockMovements.php';

class AdminWarehousesImportExportTabController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'bms_advancedstock_warehouse_product';
        $this->className = 'AdvancedStockWarehousesProducts';
        $this->identifier = 'wi_id';
        $this->context = Context::getContext();

        parent::__construct();

        $this->override_folder = 'warehouse/edit/';
        $this->setTemplate('importexport.tpl');
    }

    public function init()
    {
        parent::init();

        if (Tools::getIsset('w_id')) {
            $this->context->smarty->assign("w_id", (int) Tools::getValue('w_id'));
        } else {
            throw new PrestaShopException($this->l('Missing parameter w_id'));
        }
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

    public function processImport()
    {
        $fileData = $this->checkUploadedFile();

        if ($fileData["import_stock"]["error"]) {
            $this->errors[] = $fileData["import_stock"]["error"];
            return;
        }

        if (Tools::getIsset('w_id')) {
            $warehouseId = (int) Tools::getValue('w_id', null);
        } else {
            throw new PrestaShopException($this->l('Missing parameter w_id'));
        }

        $filename = $this->getPath($fileData['import_stock']['filename']);
        $file = fopen($filename, "r");
        $headers = fgetcsv($file, 10000, AdvanceStockCompatibility::CSV_DELIMITER);
        if (!$this->checkHeaders($headers)) {
            $this->errors[] = Tools::displayError($this->l('Some headers are missing.'));
            return;
        }

        $nbLine = 1;
        while (($line = fgetcsv($file, 10000, AdvanceStockCompatibility::CSV_DELIMITER)) !== false) {
            $nbLine++;

            if (empty($line)) {
                continue;
            }

            $data = @array_combine($headers, $line);
            if (!$data) {
                $this->errors[] = $this->l('The lines format is incorrect.');
                return;
            }

            $productId = (int)$data['wi_product_id'];
            $productAttributeId = (int)$data['wi_attribute_id'];
            $targetQty = (int)$data['wi_physical_quantity'];
            $shelfLocation = pSQL($data['wi_shelf_location']);

            try {
                $wp = AdvancedStockWarehousesProducts::updatePhysicalQuantity($productId, $productAttributeId, $warehouseId, $targetQty);
            } catch (PrestaShopException $e) {
                $this->errors[] = $e->getMessage(). $this->l(' (line: '. $nbLine .')');
                continue;
            }
            $currentShelfLocation = $wp->wi_shelf_location;
            if ($currentShelfLocation != $shelfLocation) {
                $wp->wi_shelf_location = $shelfLocation;
                $wp->save();
            }
        }

        fclose($file);
        $this->confirmations[] = "Import processed successfully";
    }

    protected function checkUploadedFile()
    {
        if (!empty($_FILES['import_stock']['error'])) {
            switch ($_FILES['import_stock']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    $_FILES['import_stock']['error'] = $this->l('The uploaded file exceeds the upload_max_filesize directive in your php.ini.');
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $_FILES['import_stock']['error'] = $this->l('The uploaded file exceeds the post_max_size directive in your php.ini.');
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $_FILES['import_stock']['error'] = $this->l('The uploaded file was only partially uploaded.');
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $_FILES['import_stock']['error'] = $this->l('No file was uploaded.');
                    break;
            }
            return $_FILES;
        }

        if (!preg_match('#([^\.]*?)\.(csv|xls[xt]?|o[dt]s)$#is', $_FILES['import_stock']['name'])) {
            $_FILES['import_stock']['error'] = $this->l('The extension of your file should be ".csv".');
            return $_FILES;
        }

        $filenamePrefix = date('YmdHis').'-';

        if (!@filemtime($_FILES['import_stock']['tmp_name']) ||
            !@move_uploaded_file($_FILES['import_stock']['tmp_name'], $this->getPath().$filenamePrefix.str_replace("\0", '', $_FILES['import_stock']['name']))) {
            $_FILES['import_stock']['error'] = $this->l('An error occurred while uploading / copying the file.');
            return $_FILES;
        }

        @chmod($this->getPath().$filenamePrefix.$_FILES['file']['name'], 0664);
        $_FILES['import_stock']['filename'] = $filenamePrefix.str_replace('\0', '', $_FILES['import_stock']['name']);

        return $_FILES;
    }

    protected function checkHeaders($headers)
    {
        return count(array_diff($this->getImportRequireFields(), $headers)) > 0 ? false : true;
    }

    protected function getImportRequireFields()
    {
        return array(
                'wi_product_id',
                'wi_attribute_id',
                'wi_physical_quantity',
                'wi_shelf_location',
        );
    }

    public static function getPath($file = '')
    {
        $mainDir = defined('_PS_HOST_MODE_') ? _PS_ROOT_DIR_ : _PS_ADMIN_DIR_;
        return $mainDir.DIRECTORY_SEPARATOR.'import'.DIRECTORY_SEPARATOR.$file;
    }
}

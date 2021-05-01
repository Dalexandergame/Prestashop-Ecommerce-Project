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

require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Helper/AdvancedStockFreeScan.php';

class AdminFreeScanHomeController extends ModuleAdminController
{
    protected $scanAction;
    protected $warehouseId;
    protected $label;
    protected $warehouseName;

    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->override_folder = 'freescan/';
        $this->setTemplate('freescan.tpl');
    }

    public function init()
    {
        parent::init();
        $this->warehouseId = (int)Tools::getValue('warehouse', null);
        $this->scanAction = Tools::getValue('action', null);
        $this->label = Tools::getValue('label', null);
        $this->warehouseName = (new AdvancedStockWarehouses($this->warehouseId))->w_name;

        $this->context->smarty->assign('title', $this->getTitle());
        $this->context->smarty->assign('warehouseId', $this->warehouseId);
        $this->context->smarty->assign('action', $this->scanAction);
        $this->context->smarty->assign('label', $this->label);
        $this->context->smarty->assign('saveUrl', Context::getContext()->link->getAdminLink('AdminFreeScanHome', true));
        $this->context->smarty->assign('nokSoundUrl', Context::getContext()->shop->getBaseURL(true) . 'modules/advancedstock/views/sound/wrong.mp3');
        $this->context->smarty->assign('okSoundUrl', Context::getContext()->shop->getBaseURL(true) . 'modules/advancedstock/views/sound/correct.mp3');
    }

    public function initContent()
    {
        parent::initContent();
        $this->setBmsMedia();
    }

    protected function getTitle()
    {
        return $this->warehouseName . ' : ' . AdvancedStockFreeScan::getActionName($this->scanAction);
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        $this->page_header_toolbar_title = $this->l('Free Scan');
        $this->context->smarty->assign('help_link', null);
    }

    public function setBmsMedia()
    {
        Media::addJsDef(array(
            'ProductInformationUrl' => $this->context->link->getAdminLink('AdminFreeScanHome', true),
            'warehouseId' => $this->warehouseId,
            'action' => $this->scanAction
        ));
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/freescan.js');
        $this->removeJS(_PS_JS_DIR_.'admin/notifications.js');
    }

    public function ajaxPreProcess()
    {
        if (Tools::getValue('action') !== 'productInformation') {
            return;
        }

        $barcode = Tools::getValue('barcode', null);
        $warehouseId = Tools::getValue('warehouse_id', null);
        $productInfo = array();

        $productInfo['error'] = '';
        try {
            $productInfo = AdvancedStockFreeScan::getProductInformation($barcode, $warehouseId);
        } catch (Exception $e) {
            $productInfo['error'] = $e->getMessage();
        }

        die(json_encode($productInfo));
    }

    public function postProcess()
    {
        parent::postProcess();
        if (Tools::isSubmit('product')) {
            $data = Tools::getValue('product');
            $warehouseId = Tools::getValue('warehouse_id');
            $action = Tools::getValue('action');
            $label = Tools::getValue('label');
            $errorFlag = 0;
            try {
                AdvancedStockFreeScan::applyChanges($data, $warehouseId, $action, $label);
                $this->confirmations[] = 'Changes successfully applied';
            } catch (Exception $e) {
                $this->errors[] = $e->getMessage();
                $errorFlag = 1;
            }
            if ($errorFlag) {
                $message = urlencode(implode(',', $this->errors));
            } else {
                $message = urlencode(implode(',', $this->confirmations));
            }

            Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminFreeScanInitHome', true) . '&error='.$errorFlag.'&message='.$message);
        }
    }
}

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

class AdminFreeScanInitHomeController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->override_folder = 'freescan/';
        $this->setTemplate('freescan_init.tpl');
        $this->context->smarty->assign("warehouses", AdvancedStockWarehouses::getWarehousesOptions());
        $this->context->smarty->assign("actions", AdvancedStockFreeScan::getActionsOptions());
        $this->context->smarty->assign("continueUrl", $this->getContinueUrl());
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        $this->page_header_toolbar_title = $this->l('Free Scan');
        $this->context->smarty->assign('help_link', null);
    }

    private function getContinueUrl()
    {
        return $this->context->link->getAdminLink('AdminFreeScanHome', true);
    }

    public function init()
    {
        parent::init();
        $message = Tools::getValue('message');
        $errorFlag = Tools::getValue('error');
        $message = urldecode($message);
        if ($message && $errorFlag) {
            $this->errors[] = $message;
        }
        if ($message && !$errorFlag) {
            $this->confirmations[] = $message;
        }
    }
}

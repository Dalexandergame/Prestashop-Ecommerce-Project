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

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Helper/AdvanceStockCompatibility.php';
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Helper/AdvancedStockReservation.php';
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Helper/AdvancedStockUtils.php';
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Model/AdvancedStockExtendedOrderDetail.php';
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Model/AdvancedStockWarehousesProducts.php';
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Model/AdvancedStockStockMovements.php';
require_once _PS_MODULE_DIR_ . 'advancedstock/install/init.php';

class AdvancedStock extends Module
{
    public function __construct()
    {
        $this->name = 'advancedstock';
        $this->tab = 'administration';
        $this->version = '1.0.14';
        $this->author = 'BoostMyShop';
        $this->need_instance = 0;
        $this->module_key = 'f44dfd92071154c12c9a08929760c542';
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        $this->displayName = $this->l('BMS AdvancedStock');
        $this->description = $this->l('Prestashop ERP Module Advanced Stock');

        parent::__construct();

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function install()
    {
        if (! parent::install()) {
            return false;
        }

        $hooksNames = $this->getModuleHooks();
        foreach ($hooksNames as $hookName) {
            if (!$this->registerHook($hookName)) {
                return false;
            }
        }

        $this->installSql('schema');
        $this->installSql('data');
        $this->initQuantities();
        $this->installMenu();
        $this->installSubMenu();

        return true;
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    protected function initQuantities()
    {
        $ordersDetails = AdvancedStockExtendedOrderDetail::getOpenedOrdersItems();
        foreach ($ordersDetails as $eod) {
            $this->initQtyToShip($eod);
            $this->initReservedAndAvailableQty($eod);
        }
    }

    /**
     * Init warehouse products quantity to ship
     * @param array $eod
     */
    protected function initQtyToShip($eod)
    {
        $qtyToShip = retrieveTotalQuantityToShip($eod['product_id'], $eod['product_attribute_id']);
        sqlUpdateWpQuantityToShip($eod['product_id'], $eod['product_attribute_id'], $eod['eod_warehouse_id'], $qtyToShip);
    }

    /**
     * Init warehouse products and extended order details reserved quantity
     * @param array $eod
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    protected function initReservedAndAvailableQty($eod)
    {
        $availableQty = AdvancedStockWarehousesProducts::getAvailableQtyForProduct($eod['product_id'], $eod['product_attribute_id'], $eod['eod_warehouse_id']);
        if ($availableQty && $availableQty > 0) {
            $qtyToReserve = $eod['eod_qty_to_ship'];
            $reservedQty = $availableQty - $qtyToReserve >= 0 ? $qtyToReserve : $availableQty;
            sqlUpdateEodReservedQty($eod['eod_id'], $reservedQty);
            AdvancedStockWarehousesProducts::updateReservedQuantity($eod['product_id'], $eod['product_attribute_id'], $eod['eod_warehouse_id'], $reservedQty);
            $newAvailableQty = $availableQty - $reservedQty;

            sqlUpdateWpAvailableQty($eod['product_id'], $eod['product_attribute_id'], $eod['eod_warehouse_id'], $newAvailableQty);
        }
    }

    protected function getModuleHooks()
    {
        return array(
            //module hooks
            'newStockMovement',
            'orderDetailWarehouseChange',
            'orderDetailQtyToShipChange',
            'orderDetailQtyReservedChange',
            'warehouseProductAfterSave',
            'warehouseProductQtyOnHandChange',
            'warehouseProductQtyToShipChange',
            'warehouseProductAvailableQtyChange',

            //prestashop hooks
            'displayBackOfficeHeader',
            'actionProductDelete',
            'actionValidateOrder',
            'actionOrderStatusUpdate',
            'actionOrderStatusPostUpdate',
            'actionProductSave',
            'actionUpdateQuantity'
        );
    }

    //module hooks
    /**
     * @param $params array
     * @throws PrestaShopException
     */
    public function hookNewStockMovement($params)
    {
        $sm = $params['object'];
        AdvancedStockWarehousesProducts::updatePhysicalQuantityFromSm($sm);
    }

    /**
     * @param $params array
     * @throws PrestaShopException
     */
    public function hookOrderDetailWarehouseChange($params)
    {
        /** @var AdvancedStockExtendedOrderDetail $eod */
        $eod = $params['object'];
        $od = $eod->getOrderDetail();
        AdvancedStockWarehousesProducts::updateQuantityToShip($od->product_id, $od->product_attribute_id, $params['previousWarehouseId']);
        AdvancedStockWarehousesProducts::updateReservedQuantity($od->product_id, $od->product_attribute_id, $params['previousWarehouseId'], -$eod->eod_reserved_qty);
        $eod->eod_reserved_qty = 0;
        $eod->save();
        AdvancedStockWarehousesProducts::updateQuantityToShip($od->product_id, $od->product_attribute_id, $eod->eod_warehouse_id);
    }

    /**
     * @param $params array
     * @throws PrestaShopException
     */
    public function hookOrderDetailQtyToShipChange($params)
    {
        /** @var AdvancedStockExtendedOrderDetail $eod */
        $eod = $params['object'];
        $od = $eod->getOrderDetail();
        $eod->updateReservedQty();
        AdvancedStockWarehousesProducts::updateQuantityToShip($od->product_id, $od->product_attribute_id, $eod->eod_warehouse_id);
    }

    /**
     * @param $params
     * @throws PrestaShopException
     */
    public function hookOrderDetailQtyReservedChange($params)
    {
        /** @var AdvancedStockExtendedOrderDetail $eod */
        $eod = $params['object'];
        $od = $eod->getOrderDetail();
        $qtyToAdd = $eod->eod_reserved_qty - $params['previousQty'];
        AdvancedStockWarehousesProducts::updateReservedQuantity($od->product_id, $od->product_attribute_id, $eod->eod_warehouse_id, $qtyToAdd);
    }

    /**
     * @param $params array
     * @throws Exception
     */
    public function hookWarehouseProductAfterSave($params)
    {
        $wp = $params['object'];
        AdvancedStockStockMovements::syncStockMovements($wp);
    }

    /**
     * @param $params array
     * @throws PrestaShopException
     * @throws PrestaShopDataBaseException
     */
    public function hookWarehouseProductQtyOnHandChange($params)
    {
        $wp = $params['object'];
        $wp->updateAvailableQuantity();
        AdvancedStockReservation::process($wp);
    }

    /**
     * @param $params array
     * @throws PrestaShopException
     * @throws PrestaShopDataBaseException
     */
    public function hookWarehouseProductQtyToShipChange($params)
    {
        /** @var AdvancedStockWarehousesProducts $wp */
        $wp = $params['object'];
        $wp->updateAvailableQuantity();
        AdvancedStockReservation::process($wp);
    }

    /**
     * @param $params array
     * @throws PrestaShopException
     * @throws PrestaShopDataBaseException
     */
    public function hookWarehouseProductAvailableQtyChange($params)
    {
        /** @var AdvancedStockWarehousesProducts $wp */
        $wp = $params['object'];
        AdvancedStockWarehouseProduct::updatePrestashopAvailableQties(
            $wp->wi_product_id,
            $wp->wi_attribute_id,
            $wp->wi_warehouse_id
        );
    }

    //prestashop hooks
    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCss($this->_path.'views/css/tab.css');
    }

    /**
     * @param $params array
     * @throws PrestaShopDataBaseException
     * @throws PrestaShopException
     */
    public function hookActionProductDelete($params)
    {
        $productId = $params['id_product'];
        AdvancedStockWarehousesProducts::removeWarehouseProductAfterDelete($productId);
    }

    /**
     * @param $params array
     * @throws PrestaShopDataBaseException
     * @throws PrestaShopException
     */
    public function hookActionValidateOrder($params)
    {
        $order = $params['order'];
        AdvancedStockExtendedOrderDetail::createDetailsForOrder($order);
    }

    public function hookActionOrderStatusUpdate($params)
    {
        $orderId = $params['id_order'];
        $status = $params['newOrderStatus'];
        AdvancedStockExtendedOrderDetail::beforeOrderStatusChange($orderId, $status);
    }

    /**
     * @param $params array
     * @throws PrestaShopDataBaseException
     * @throws PrestaShopException
     */
    public function hookActionOrderStatusPostUpdate($params)
    {
        $orderId = $params['id_order'];
        $status = $params['newOrderStatus'];
        AdvancedStockExtendedOrderDetail::onOrderStatusChange($orderId, $status);
    }


    /**
     * @param $params array
     * @throws PrestaShopException
     */
    public function hookActionProductSave($params)
    {
        $product = $params['product'];
        $productId = $params['id_product'];

        if ($product->name[1] === '') {
            return;
        }

        $productAttributes = $product->getProductAttributesIds($productId);
        if (empty($productAttributes)) {
            AdvancedStockWarehousesProducts::assignToAllWarehouses($productId);
            return;
        }

        foreach ($productAttributes as $productAttribute) {
            AdvancedStockWarehousesProducts::assignToAllWarehouses($productId, $productAttribute['id_product_attribute']);
        }
    }

    /**
     * @param $params
     */
    public function hookActionUpdateQuantity($params)
    {
        $productId = (int)$params['id_product'];
        $productAttributeId = (int)$params['id_product_attribute'];
        $qty = (int)$params['quantity'];
        $isManualAdjustment = $params['cart'] === null;

        if ($isManualAdjustment) {
            AdvancedStockWarehousesProducts::onPrestashopQuantityChange($productId, $productAttributeId, $qty);
        }
    }

    //SQL
    /**
     * @param $type string
     * @return bool
     */
    public function installSql($type)
    {
        if (! ($sql = Tools::file_get_contents(dirname(__FILE__) . '/install/install_'.$type.'.sql'))) {
            return false;
        }

        $sql = str_replace("ps_", _DB_PREFIX_, $sql);

        return Db::getInstance()->execute($sql);
    }

    public function installMenu()
    {
        $tab = new Tab();

        foreach (Language::getLanguages(true) as $lang) {
            switch ($lang['iso_code']) {
                case 'fr':
                    $tab->name[$lang['id_lang']] = 'Gestion de stock';
                    break;
                default:
                    $tab->name[$lang['id_lang']] = $this->l('AdvancedStock');
            }
        }

        $tab->class_name = 'AdminAdvancedStockHome';
        $tab->id_parent = Tab::getIdFromClassName('SELL');
        $tab->module = $this->name;
        return $tab->add();
    }

    public function installSubMenu()
    {
        $arraySubMenu = array(

            array(
                'name' => $this->l('Products'),
                'name_fr' => $this->l('Produits'),
                'class_name' => 'AdminProductsHome',
                'active' => 1
            ),
            array(
                'name' => $this->l('Warehouses'),
                'name_fr' => $this->l('Dépôts'),
                'class_name' => 'AdminWarehousesHome',
                'active' => 1
            ),
            array(
                'name' => $this->l('Mass Stock Editor'),
                'name_fr' => $this->l('Edition des stocks en masse'),
                'class_name' => 'AdminMassStockEditorHome',
                'active' => 1
            ),
            array(
                'name' => $this->l('Stock Movements'),
                'name_fr' => $this->l('Mouvements de stock'),
                'class_name' => 'AdminStockMovementsHome',
                'active' => 1
            ),
            array(
                'name' => $this->l('Free Scan'),
                'class_name' => 'AdminFreeScanInitHome',
                'active' => 1
            ),
            array(
                'name' => $this->l('Free Scan'),
                'class_name' => 'AdminFreeScanHome',
                'active' => 0
            ),
            array(
                'name' => $this->l('Warehouse Import-Export Tab'),
                'class_name' => 'AdminWarehousesImportExportTab',
                'active' => 0
            ),
            array(
                'name' => $this->l('Warehouse Product Tab'),
                'class_name' => 'AdminWarehousesProductTab',
                'active' => 0
            ),
            array(
                'name' => $this->l('Product Warehouses Tab'),
                'class_name' => 'AdminProductWarehousesTab',
                'active' => 0
            ),
            array(
                'name' => $this->l('Product Orders Tab'),
                'class_name' => 'AdminProductOrdersTab',
                'active' => 0
            ),
            array(
                'name' => $this->l('Product Stock Movements Tab'),
                'class_name' => 'AdminProductStockMovementsTab',
                'active' => 0
            )
        );

        foreach ($arraySubMenu as $subMenu) {
            $tab = new Tab();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = (isset($subMenu['name_' . $lang['iso_code']]) ? $subMenu['name_' . $lang['iso_code']] : $subMenu['name']);
            }
            $tab->class_name = $subMenu['class_name'];
            $tab->id_parent = (int) empty($subMenu['id_parent']) ? Tab::getIdFromClassName('AdminAdvancedStockHome') : $subMenu['id_parent'];
            $tab->module = $this->name;
            $tab->active = (isset($subMenu['active']) ? $subMenu['active'] : 0);
            $tab->add();
        }

        return true;
    }

    public function uninstall()
    {
        parent::uninstall();

        if (!$this->uninstallTabs()) {
            return false;
        }

        return $this->uninstallSql();
    }

    protected function uninstallTabs()
    {
        foreach (Tab::getCollectionFromModule($this->name) as $tab) {
            $tab->delete();
        }

        return true;
    }

    public function uninstallSql()
    {
        if (! ($sql = Tools::file_get_contents(dirname(__FILE__) . '/install/uninstall.sql'))) {
            return false;
        }

        $sql = str_replace('ps_', _DB_PREFIX_, $sql);

        return Db::getInstance()->execute($sql);
    }
}

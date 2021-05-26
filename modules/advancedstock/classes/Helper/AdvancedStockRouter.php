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

require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Model/AdvancedStockWarehousesShops.php';
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Model/AdvancedStockWarehouses.php';

class AdvancedStockRouter
{
    /**
     * @param $orderDetail OrderDetail
     * @return int
     * @throws \PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public static function getWarehouseForOrderDetail($orderDetail)
    {
        $order = new Order($orderDetail->id_order);
        $shopId = $order->id_shop;
        $productId = $orderDetail->product_id;
        $productAttributeId = $orderDetail->product_attribute_id;

        Logger::addLog('Routing item ' . $orderDetail->id . ' from order ' . $order->id, null, null, 'Router', $orderDetail->id);
        if (($warehouseId = self::getFirstShipWarehouseForProduct($shopId, $productId, $productAttributeId)) !== false) {
            Logger::addLog("Assigning warehouse $warehouseId", null, null, 'Router', $orderDetail->id);
            return $warehouseId;
        }
        Logger::addLog('No warehouse found with available stock', null, null, 'Router', $orderDetail->id);

        if (($warehouseId = self::getFirstShipWarehouseForProduct($shopId, $productId, $productAttributeId, false)) !== false) {
            Logger::addLog("Assigning warehouse $warehouseId, despite having no available stock", null, null, 'Router', $orderDetail->id);
            return $warehouseId;
        }
        Logger::addLog('No active warehouse found with ship role !', 2, null, 'Router', $orderDetail->id);

        if ($warehouseId = AdvancedStockWarehouses::getActivePrimaryWarehouse()) {
            Logger::addLog("Assigning primary warehouse $warehouseId", null, null, 'Router', $orderDetail->id);
            return $warehouseId;
        }

        Logger::addLog('No primary warehouse found ! Cannot assign any warehouse', 3, null, 'Router', $orderDetail->id);
        return 0;
    }

    /**
     * @param $shopId
     * @param $productId
     * @param $productAttributeId
     * @param bool $havingStock
     * @return false|null|string
     */
    private static function getFirstShipWarehouseForProduct($shopId, $productId, $productAttributeId, $havingStock = true)
    {
        $query = new DbQuery();
        $query->select('w_id')
           ->from('bms_advancedstock_warehouse', 'w')
           ->leftJoin('bms_advancedstock_warehouse_product', 'wp', 'wi_warehouse_id = w_id')
           ->leftJoin('bms_advancedstock_warehouse_shop', 'ws', 'ws.ws_warehouse_id = wp.wi_warehouse_id')
           ->where('ws.ws_shop_id = ' . (int)$shopId . ' AND ' .
               'wp.wi_product_id = ' . (int)$productId . ' AND ' .
               'wp.wi_attribute_id = ' . (int)$productAttributeId . ' AND ' .
               'ws.ws_role = ' . AdvancedStockWarehousesShops::ROLE_SHIPMENT . ' AND ' .
               'w.w_is_active = 1 ' .
               ($havingStock ? ' AND wp.wi_available_quantity > 0' : ''));

        return Db::getInstance()->getValue($query);
    }
}

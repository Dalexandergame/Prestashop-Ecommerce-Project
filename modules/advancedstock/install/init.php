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

/**
 * @param int $productId
 * @param int $productAttributeId
 * @return int
 */
function retrieveTotalQuantityToShip($productId, $productAttributeId)
{
    $sql = new DbQuery();
    $sql->select('SUM(od.product_quantity - od.product_quantity_refunded)')
        ->from(OrderDetail::$definition['table'], 'od')
        ->leftJoin(Order::$definition['table'], 'o', 'od.id_order = o.id_order')
        ->leftJoin(OrderState::$definition['table'], 'os', 'o.current_state = os.id_order_state')
        ->where('od.product_id = ' . (int)$productId)
        ->where('od.product_attribute_id = ' . (int)$productAttributeId)
        ->where('os.deleted = 0')
        ->where('os.shipped = 0')
        ->where(
            'o.current_state NOT IN (' .
            (int)Configuration::get('PS_OS_REFUND') . ',' .
            (int)Configuration::get('PS_OS_DELIVERED') . ',' .
            (int)Configuration::get('PS_OS_CANCELED') . ',' .
            (int)Configuration::get('PS_OS_ERROR') . ')'
        );

    $qtyToShip = Db::getInstance()->getValue($sql);

    if (!$qtyToShip) {
        return 0;
    }

    return $qtyToShip;
}

function sqlUpdateEodReservedQty($eodId, $reservedQty)
{
    $sql = DB::getInstance();
    $sql->update(
        AdvancedStockExtendedOrderDetail::$definition['table'],
        array('eod_reserved_qty' => (int)$reservedQty),
        'eod_id ='. (int)$eodId
    );
}

/**
 * @param int $productId
 * @param int $productAttributeId
 * @param int $warehouseId
 * @param int $qtyToShip
 */
function sqlUpdateWpQuantityToShip($productId, $productAttributeId, $warehouseId, $qtyToShip)
{
    $sql = DB::getInstance();
    $sql->update(
        AdvancedStockWarehousesProducts::$definition['table'],
        array('wi_quantity_to_ship' => (int)$qtyToShip),
        'wi_product_id = '.(int)$productId.' AND wi_attribute_id = '.(int)$productAttributeId.' AND wi_warehouse_id = '.(int)$warehouseId
    );
}

function sqlUpdateWpAvailableQty($productId, $productAttributeId, $warehouseId, $availableQty)
{
    $sql = DB::getInstance();
    $sql->update(
        AdvancedStockWarehousesProducts::$definition['table'],
        array('wi_available_quantity' => (int)$availableQty),
        'wi_product_id = '.(int)$productId.' AND wi_attribute_id = '.(int)$productAttributeId.' AND wi_warehouse_id = '.(int)$warehouseId
    );
}

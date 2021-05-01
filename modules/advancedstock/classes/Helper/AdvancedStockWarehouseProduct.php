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

class AdvancedStockWarehouseProduct
{
    public function ajaxUpdateWhProduct()
    {
        if (!Tools::getIsset('method')) {
            throw new PrestaShopException('An error occurred: method undefined.');
        }

        if (!Tools::getIsset('wi_id')) {
            throw new PrestaShopException('An error occurred: wi_id undefined.');
        }

        $warehouseId = (int) Tools::getValue('warehouseId');
        $productId = (int) Tools::getValue('productId');
        $productAttributeId = (int) Tools::getValue('productAttributeId');
        $fieldName = pSQL(Tools::getValue('fieldName'));
        $errors = array();

        switch (Tools::getValue('method')) {
            case "updateWhProduct":
                try {
                    if ($fieldName == 'wi_physical_quantity') {
                        $value = (int) Tools::getValue('value');
                        AdvancedStockWarehousesProducts::updatePhysicalQuantity($productId, $productAttributeId, $warehouseId, $value);
                        break;
                    }
                    //here $fieldName is 'wi_shelf_location'
                    $wp = AdvancedStockWarehousesProducts::findProduct($productId, $productAttributeId, $warehouseId);
                    $value = pSQL(Tools::getValue('value'));
                    if ($wp->wi_shelf_location == $value) {
                        break;
                    }
                    $wp->wi_shelf_location = $value;
                    $wp->save();
                } catch (PrestaShopException $e) {
                    $errors[] = $e->getMessage();
                }
                break;
        }

        if (count($errors) > 0) {
            header($_SERVER['SERVER_PROTOCOL']. ' 500 Internal Server');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(
                array(
                    'errors' => $errors)
            ));
        }
    }

    public static function getGlobalQuantityToShip($productId, $attributeId, $warehouseId)
    {
        $query = new DbQuery();
        $query->select('SUM(eod_qty_to_ship)')
            ->from(AdvancedStockExtendedOrderDetail::$definition['table'])
            ->leftJoin(OrderDetail::$definition['table'], null, 'id_order_detail = eod_order_detail_id')
            ->where('eod_warehouse_id = ' . (int)$warehouseId)
            ->where('product_id = ' . (int)$productId)
            ->where('product_attribute_id = ' . (int)$attributeId);

        $sum = Db::getInstance()->getValue($query);

        return $sum ? $sum : 0;
    }

    /**
     * @param $productId
     * @param $attributeId
     * @param $warehouseId
     * @param string $shopId
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function updatePrestashopAvailableQties($productId, $attributeId, $warehouseId, $shopId = null)
    {
        if ($shopId) {
            self::updateStockAvailableQty($productId, $attributeId, $shopId);
            return;
        }

        $shops = self::getSalesWarehousesShopIds($warehouseId);
        foreach ($shops as $shop) {
            self::updateStockAvailableQty($productId, $attributeId, $shop['ws_shop_id']);
        }
    }

    /**
     * @param int $productId
     * @param int $attributeId
     * @param int $shopId
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    protected static function updateStockAvailableQty($productId, $attributeId, $shopId)
    {
        $saId = StockAvailable::getStockAvailableIdByProductId($productId, $attributeId, $shopId);
        if (!$saId) {
            return;
        }
        $sa = new StockAvailable($saId);
        $sa->quantity = self::getAvailableQuantityFromSalesWarehouses($shopId, $productId, $attributeId);
        $sa->save();
        Hook::exec('shopProductQtyAvailableChanges', array('stock_available' => $sa));
    }

    /**
     * @param $shopId int
     * @param $productId int
     * @param $attributeId int
     * @return false|null|string
     */
    private static function getAvailableQuantityFromSalesWarehouses($shopId, $productId, $attributeId)
    {
        $query = new DbQuery();

        $query->select('SUM(wi_physical_quantity - wi_quantity_to_ship)')
            ->from('bms_advancedstock_warehouse_product')
            ->leftJoin(AdvancedStockWarehousesShops::$definition['table'], 'ws', 'wi_warehouse_id = ws_warehouse_id')
            ->where('ws_shop_id = ' . (int)$shopId)
            ->where('ws_role = ' . AdvancedStockWarehousesShops::ROLE_SALES)
            ->where('wi_product_id = ' . pSQL($productId))
            ->where('wi_attribute_id = ' . pSQL($attributeId));

        $availableQtyForSales = Db::getInstance()->getValue($query);
        return ($availableQtyForSales>0)?$availableQtyForSales:0;
    }

    /**
     * @param $warehouseId
     * @return array|false|mysqli_result|null|PDOStatement|resource
     * @throws \PrestaShopDatabaseException
     */
    private static function getSalesWarehousesShopIds($warehouseId)
    {
        $query = new DbQuery();
        $query->select('ws_shop_id')
            ->from(AdvancedStockWarehousesShops::$definition['table'])
            ->where('ws_warehouse_id = ' . (int)$warehouseId)
            ->where('ws_role = ' . AdvancedStockWarehousesShops::ROLE_SALES);

        $ids = Db::getInstance()->executeS($query);

        if (!$ids) {
            return array();
        }

        return $ids;
    }
}

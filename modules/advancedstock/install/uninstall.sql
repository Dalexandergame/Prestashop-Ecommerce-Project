DROP TABLE IF EXISTS `ps_bms_advancedstock_warehouse`;
DROP TABLE IF EXISTS `ps_bms_advancedstock_warehouse_product`;
DROP TABLE IF EXISTS `ps_bms_advancedstock_warehouse_shop`;
DROP TABLE IF EXISTS `ps_bms_advancedstock_stock_movement`;
DROP TABLE IF EXISTS `ps_bms_advancedstock_extended_order_detail`;

DELETE FROM `ps_hook` WHERE `name` = 'newStockMovement';
DELETE FROM `ps_hook` WHERE `name` = 'orderDetailWarehouseChange';
DELETE FROM `ps_hook` WHERE `name` = 'orderDetailQtyToShipChange';
DELETE FROM `ps_hook` WHERE `name` = 'orderDetailQtyReservedChange';
DELETE FROM `ps_hook` WHERE `name` = 'warehouseProductAfterSave';
DELETE FROM `ps_hook` WHERE `name` = 'warehouseProductQtyOnHandChange';
DELETE FROM `ps_hook` WHERE `name` = 'warehouseProductQtyToShipChange';
DELETE FROM `ps_hook` WHERE `name` = 'warehouseProductAvailableQtyChange';

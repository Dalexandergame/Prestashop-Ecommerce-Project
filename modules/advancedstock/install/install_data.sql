-- noinspection SqlNoDataSourceInspectionForFile

-- noinspection SqlDialectInspectionForFile

-- Insert the default warehouse
INSERT INTO `ps_bms_advancedstock_warehouse` (w_name, w_is_active, w_is_primary) VALUES ('Default', 1, 1);

-- Assign to the default warehouse, the sales and shipment roles for all shops
INSERT INTO `ps_bms_advancedstock_warehouse_shop`
(ws_warehouse_id, ws_shop_id, ws_role)
(SELECT 1, id_shop, 1 FROM `ps_shop`)
UNION
(SELECT 1, id_shop, 2 FROM `ps_shop`);

-- Associate all simple products to the default warehouse
INSERT INTO `ps_bms_advancedstock_warehouse_product`
(wi_warehouse_id, wi_product_id, wi_physical_quantity, wi_available_quantity, wi_reserved_quantity, wi_quantity_to_ship, wi_attribute_id)
SELECT 1,
  sa.id_product,
  IF(physical_quantity > 0, physical_quantity, 0),
  IF(physical_quantity > 0, physical_quantity, 0),
  0,
  0,
  sa.id_product_attribute
FROM `ps_stock_available` as sa
LEFT JOIN `ps_product_lang` as pl on sa.id_product = pl.id_product
WHERE sa.id_product_attribute > 0 OR sa.id_product NOT IN (
  select distinct id_product from ps_product_attribute
) AND pl.name != ''
GROUP BY sa.id_product, sa.id_product_attribute;

-- Initialize the stock movements
INSERT INTO `ps_bms_advancedstock_stock_movement`
(sm_date, sm_product_id, sm_attribute_id, sm_target_warehouse_id, sm_qty, sm_type, sm_comment)
    SELECT now(), wi_product_id, wi_attribute_id, 1, wi_physical_quantity, 1, 'Initialization'
    FROM `ps_bms_advancedstock_warehouse_product`
    WHERE wi_physical_quantity > 0;

-- Consider all unshipped orders
INSERT INTO `ps_bms_advancedstock_extended_order_detail`
(eod_order_detail_id, eod_warehouse_id, eod_qty_to_ship, eod_reserved_qty)
    SELECT
      od.id_order_detail, 1, (od.product_quantity - od.product_quantity_refunded), 0
    FROM
      `ps_order_detail` AS od
      LEFT JOIN `ps_orders` AS o ON od.id_order = o.id_order
      LEFT JOIN `ps_order_state` AS os ON o.current_state = os.id_order_state
    WHERE
      os.shipped = 0
      AND os.deleted = 0
      AND o.current_state != (SELECT value FROM `ps_configuration` WHERE name = 'PS_OS_CANCELED')
      AND o.current_state != (SELECT value FROM `ps_configuration` WHERE name = 'PS_OS_ERROR')
      AND o.current_state != (SELECT value FROM `ps_configuration` WHERE name = 'PS_OS_DELIVERED')
      AND o.current_state != (SELECT value FROM `ps_configuration` WHERE name = 'PS_OS_REFUND');

CREATE TABLE IF NOT EXISTS `ps_bms_advancedstock_warehouse` (
  `w_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `w_name` varchar(50) NOT NULL,
  `w_contact` varchar(250) DEFAULT NULL,
  `w_email` varchar(250) DEFAULT NULL,
  `w_is_active` int(11) UNSIGNED DEFAULT 1,
  `w_display_on_front` int(11) UNSIGNED DEFAULT 1,
  `w_notes` text DEFAULT NULL,
  `w_company_name` varchar(250) DEFAULT NULL,
  `w_street1` varchar(250) DEFAULT NULL,
  `w_street2` varchar(250) DEFAULT NULL,
  `w_postcode` varchar(20) DEFAULT NULL,
  `w_city` varchar(50) DEFAULT NULL,
  `w_state` varchar(50) DEFAULT NULL,
  `w_country` varchar(3) DEFAULT NULL,
  `w_telephone` varchar(250) DEFAULT NULL,
  `w_fax` varchar(250) DEFAULT NULL,
  `w_open_hours` text DEFAULT NULL,
  `w_is_primary` int(11) DEFAULT 0,
  `w_use_in_supplyneeds` int(11) DEFAULT 1,
  PRIMARY KEY (`w_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `ps_bms_advancedstock_warehouse_shop` (
  `ws_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ws_warehouse_id` int(11) DEFAULT NULL,
  `ws_shop_id` int(11) DEFAULT NULL,
  `ws_role` int(11) DEFAULT NULL,
  PRIMARY KEY (`ws_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE INDEX IDX_WS_WAREHOUSE_ID ON ps_bms_advancedstock_warehouse_shop(ws_warehouse_id);
CREATE INDEX IDX_WS_SHOP_ID ON ps_bms_advancedstock_warehouse_shop(ws_shop_id);

CREATE TABLE IF NOT EXISTS `ps_bms_advancedstock_warehouse_product` (
  `wi_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `wi_warehouse_id` int(11) DEFAULT NULL,
  `wi_product_id` int(11) DEFAULT NULL,
  `wi_attribute_id` int(11) DEFAULT NULL,
  `wi_physical_quantity` int(11) DEFAULT 0,
  `wi_available_quantity` int(11) DEFAULT 0,
  `wi_reserved_quantity` int(11) DEFAULT 0,
  `wi_shelf_location` varchar(20) DEFAULT NULL,
  `wi_quantity_to_ship` int(11) DEFAULT 0,
  `wi_warning_stock_level` int(11) DEFAULT 0,
  `wi_use_config_warning_stock_level` int(11) DEFAULT 1,
  `wi_ideal_stock_level` int(11) DEFAULT 0,
  `wi_use_config_ideal_stock_level` int(11) DEFAULT 1,
  PRIMARY KEY (`wi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `ps_bms_advancedstock_stock_movement` (
  `sm_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sm_date` datetime NOT NULL,
  `sm_product_id` int(11) NOT NULL,
  `sm_attribute_id` int(11) DEFAULT NULL,
  `sm_source_warehouse_id` int(11) DEFAULT 0,
  `sm_target_warehouse_id` int(11) DEFAULT 0,
  `sm_qty` int(11) NOT NULL,
  `sm_type` int(11) NOT NULL,
  `sm_comment` varchar(200) DEFAULT NULL,
  `sm_employee_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`sm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE INDEX IDX_SM_PRODUCT_ID ON ps_bms_advancedstock_stock_movement(sm_product_id);
CREATE INDEX IDX_SM_ATTRIBUTE_ID ON ps_bms_advancedstock_stock_movement(sm_attribute_id);
CREATE INDEX IDX_SM_SRC_WAREHOUSE_ID ON ps_bms_advancedstock_stock_movement(sm_source_warehouse_id);
CREATE INDEX IDX_SM_TGT_WAREHOUSE_ID ON ps_bms_advancedstock_stock_movement(sm_target_warehouse_id);

CREATE TABLE IF NOT EXISTS `ps_bms_advancedstock_extended_order_detail` (
  `eod_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `eod_order_detail_id` int(11) DEFAULT NULL,
  `eod_warehouse_id` int(11) DEFAULT NULL,
  `eod_qty_to_ship` int(11) DEFAULT 0,
  `eod_reserved_qty` int(11) DEFAULT 0,
  PRIMARY KEY (`eod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

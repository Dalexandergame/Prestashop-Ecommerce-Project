CREATE TABLE IF NOT EXISTS `PREFIX_pm_advancedpack` (
  `id_pack` int(10) unsigned NOT NULL,
  `id_shop` int(10) unsigned NOT NULL,
  `fixed_price` decimal(20,6) UNSIGNED NULL DEFAULT NULL,
  `allow_remove_product` tinyint(3) unsigned DEFAULT 0,
  PRIMARY KEY (`id_pack`, `id_shop`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `PREFIX_pm_advancedpack_cart_products` (
  `id_cart` int(10) unsigned NOT NULL,
  `id_shop` int(10) unsigned NOT NULL,
  `id_pack` int(10) unsigned NOT NULL,
  `id_product_pack` int(10) unsigned NOT NULL,
  `id_product_attribute_pack` int(10) unsigned NOT NULL,
  `unique_hash` char(32) NOT NULL,
  `id_product_attribute` int(10) unsigned NOT NULL,
  `id_order` int(10) unsigned NULL,
  `customization_infos` text DEFAULT NULL,
  PRIMARY KEY (`id_cart`,`id_shop`,`id_pack`,`id_product_pack`,`id_product_attribute_pack`,`id_product_attribute`),
  INDEX `order_pack` (`id_order`, `id_pack`),
  INDEX `unique_hash` (`id_order`, `unique_hash`, `id_cart`, `id_pack`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `PREFIX_pm_advancedpack_products` (
  `id_product_pack` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_pack` int(10) unsigned DEFAULT NULL,
  `id_product` int(10) unsigned DEFAULT NULL,
  `default_id_product_attribute` int(10) unsigned DEFAULT NULL,
  `quantity` int(10) unsigned DEFAULT NULL,
  `use_reduc` tinyint(4) DEFAULT '1',
  `position` tinyint(3) unsigned DEFAULT NULL,
  `reduction_amount` decimal(20,6) unsigned DEFAULT '0.000000',
  `reduction_type` enum('amount','percentage') DEFAULT NULL,
  `exclusive` tinyint(3) unsigned DEFAULT '0',
  PRIMARY KEY (`id_product_pack`),
  INDEX `id_pack` (`id_pack`),
  INDEX `id_product` (`id_product`),
  INDEX `exclusive` (`exclusive`),
  INDEX `position` (`position`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `PREFIX_pm_advancedpack_products_attributes` (
  `id_product_pack` int(10) unsigned NOT NULL,
  `id_product_attribute` int(10) unsigned NOT NULL,
  `reduction_amount` decimal(20,6) unsigned DEFAULT '0.000000',
  `reduction_type` enum('amount','percentage') DEFAULT NULL,
  PRIMARY KEY (`id_product_pack`, `id_product_attribute`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `PREFIX_pm_advancedpack_products_customization` (
  `id_product_pack` int(10) unsigned NOT NULL,
  `id_customization_field` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_product_pack`, `id_customization_field`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
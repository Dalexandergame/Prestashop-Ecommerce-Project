CREATE TABLE IF NOT EXISTS `PREFIX_suivi_orders` (
  `id_suivi_orders` int(11) NOT NULL AUTO_INCREMENT,
  `id_order` int(11) NULL,
  `id_warehouse` int(11) NOT NULL,
  `id_carrier` int(11) NOT NULL,
  `id_carrier_retour` int(11) NULL,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `company` varchar(64) DEFAULT NULL,
  `address1` varchar(128) DEFAULT NULL,
  `address2` varchar(128) DEFAULT NULL,
  `postcode` varchar(12) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `phone_mobile` varchar(32) DEFAULT NULL,
  `message` text,
  `commande` text,
  `position` int(11) NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `date_delivery` DATETIME NULL,
  `date_retour` DATETIME NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `to_translate` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_suivi_orders`)
);

CREATE TABLE IF NOT EXISTS `ps_suivi_orders_carrier` (
  `id_suivi_orders_carrier` int(11) NOT NULL AUTO_INCREMENT,
  `id_carrier` int(11) NOT NULL,
  `date_delivery` date NOT NULL,
  `text` varchar(64) NOT NULL,
  PRIMARY KEY (`id_suivi_orders_carrier`)
);

ALTER TABLE `PREFIX_orders` ADD `is_imported` BOOLEAN NULL DEFAULT FALSE ;
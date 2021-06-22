CREATE TABLE IF NOT EXISTS `PREFIX_planning_delivery_carrier` (
  `id_planning_delivery_carrier`  int(10) unsigned NOT null auto_increment PRIMARY KEY,
  `id_cart` int(10) unsigned NOT null,
  `id_order` int(10) unsigned NOT null,
  `id_planning_delivery_carrier_slot` int(10) unsigned NOT null,
  `date_delivery` DATETIME NOT null,
  `date_retour` datetime DEFAULT null,
  `date_add` DATETIME NOT null,
  `date_upd` DATETIME NOT null,
  UNIQUE (`id_cart`, `id_order`)
) ENGINE = MYISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_planning_delivery_carrier_slot` (
  `id_planning_delivery_carrier_slot` int(10) unsigned NOT null auto_increment,
  `id_lang` int(10) unsigned NOT null,
  `name` varchar(64) NOT null,
  `slot1` DATETIME NOT null,
  `slot2` DATETIME NOT null,
  `date_delivery` DATETIME NOT null,
  `customers_max` INT(10) UNSIGNED NOT null,
  PRIMARY KEY (`id_planning_delivery_carrier_slot`, `id_lang`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_planning_delivery_carrier_slot_day` (
  `id_day` int(10) unsigned NOT null,
  `id_planning_delivery_carrier_slot` int(10) unsigned NOT null,
  `id_carrier` int(10) unsigned NOT null,
  PRIMARY KEY(`id_day`, `id_planning_delivery_carrier_slot`, `id_carrier`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_planning_delivery_carrier_exception` (
  `id_planning_delivery_carrier_exception` int(10) unsigned NOT null auto_increment PRIMARY KEY,
  `date_from` DATETIME NOT null,
  `date_to` DATETIME NOT null,
  `max_places` int  null,
  `nb_commandes` int  null,
  `id_carrier` int(10) NOT null
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_planning_retour_carrier_exception` (
  `id_planning_retour_carrier_exception` int(10) unsigned NOT null auto_increment PRIMARY KEY,
  `date_from` DATETIME NOT null,
  `date_to` DATETIME NOT null,
  `max_places` int  null,
  `nb_commandes` int  null,
  `id_carrier` int(10) NOT null
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


ALTER TABLE `ps_cart`
    ADD `npa` VARCHAR(10)  NULL  DEFAULT NULL  AFTER `allow_seperated_package`;

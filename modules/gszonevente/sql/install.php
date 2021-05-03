<?php
/**
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2014 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'gszonevente_region` (
    `id_gszonevente_region` int(11) NOT NULL AUTO_INCREMENT,
     `name` varchar(255) NOT NULL,
     `id_carrier` int(10)  NOT NULL,
     `id_country` int(10) unsigned NOT NULL,
    PRIMARY KEY  (`id_gszonevente_region`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'gszonevente_npa` (
    `id_gszonevente_npa` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `id_gszonevente_region` int(11)  NOT NULL,
    PRIMARY KEY  (`id_gszonevente_npa`)
  
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[]=  'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'gszonevente_region_warehouse` (
  `id_gszonevente_region` int(11) unsigned NOT NULL,
  `id_warehouse` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_warehouse`,`id_gszonevente_region`),
  KEY `id_warehouse` (`id_warehouse`),
  KEY `id_gszonevente_region` (`id_gszonevente_region`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;';

$sql[]=  'ALTER TABLE `'._DB_PREFIX_.'gszonevente_npa`
    ADD CONSTRAINT fk_gszonevente_region_npa FOREIGN KEY (id_gszonevente_region) 
    REFERENCES `'._DB_PREFIX_.'gszonevente_region`(id_gszonevente_region)
    ON UPDATE CASCADE ON DELETE CASCADE     ;';

foreach ($sql as $query)
	if (Db::getInstance()->execute($query) == false)
		return false;

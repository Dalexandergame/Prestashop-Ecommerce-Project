/*
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

$(document).ready(function(){
	
 	$('ul.nav.nav-tabs').append('<li><a href="#product" data-href="' + AdminWarehousesProductTabUrl + '" data-toggle="tab">' + AdminWarehousesProductTabLabel + '</a></li>')	
 	$('div.tab-content.panel').append(' <div id="product" class="tab-pane" data-tab-id="product"><iframe id="productFrame" frameborder= "0" scrolling= "no" width= "100%" onload="resizeIframe(this)"  src=""></iframe></div>')

 	$('ul.nav.nav-tabs').append('<li><a href="#ImportExport" data-href="' + AdminWarehousesImportExportTabUrl + '" data-toggle="tab">' + AdminWarehousesImportExportTabLabel + '</a></li>')	
 	$('div.tab-content.panel').append(' <div id="ImportExport" class="tab-pane" data-tab-id="ImportExport"><iframe id="ImportExportFrame" frameborder= "0" scrolling= "no" width= "100%" onload="resizeIframe(this)"  src=""></iframe></div>')
 
	initFormTabs();
	checkoutIframe();
	
});
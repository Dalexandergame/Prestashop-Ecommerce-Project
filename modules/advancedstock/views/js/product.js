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

function isAdvancedStockNumeric(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}

$(document).ready(function(){


    if (AdminProductPurchaseOrdersTabUrl)
    {
        $('ul.nav.nav-tabs').prepend('<li><a href="#warehouses" data-href="' + AdminProductPurchaseOrdersTabUrl + '" data-toggle="tab">' + AdminProductPurchaseOrdersTabLabel + '</a></li>');
        $('div.tab-content.panel').prepend(' <div id="purchase_orders" class="tab-pane" data-tab-id="purchase_orders"><iframe id="purchaseOrdersFrame" frameborder= "0" scrolling= "no" width= "100%" onload="resizeIframe(this)"  src=""></iframe></div>');
    }

	$('ul.nav.nav-tabs').prepend('<li><a href="#stock_movements" data-href="' + AdminProductStockMovementsTabUrl + '" data-toggle="tab">' + AdminProductStockMovementsTabLabel + '</a></li>');
	$('div.tab-content.panel').prepend(' <div id="stock_movements" class="tab-pane" data-tab-id="stock_movements"><iframe id="stock_movementsFrame" frameborder= "0" scrolling= "no" width= "100%" onload="resizeIframe(this)"  src=""></iframe></div>');

	$('ul.nav.nav-tabs').prepend('<li><a href="#orders" data-href="' + AdminProductOrdersTabUrl + '" data-toggle="tab">' + AdminProductOrdersTabLabel + '</a></li>');
	$('div.tab-content.panel').prepend(' <div id="orders" class="tab-pane" data-tab-id="orders"><iframe id="ordersFrame" frameborder= "0" scrolling= "no" width= "100%" onload="resizeIframe(this)"  src=""></iframe></div>');

 	$('ul.nav.nav-tabs').prepend('<li><a href="#warehouses" data-href="' + AdminProductWarehousesTabUrl + '" data-toggle="tab">' + AdminProductWarehousesTabLabel + '</a></li>');
 	$('div.tab-content.panel').prepend(' <div id="warehouses" class="tab-pane" data-tab-id="warehouses"><iframe id="warehousesFrame" frameborder= "0" scrolling= "no" width= "100%" onload="resizeIframe(this)"  src=""></iframe></div>');


    addUrlAncor('warehouses');
	initFormTabs();
	checkoutIframe();

    $('#bms_advancedstock_warehouse_product_form').submit(function () {
        var whFrom = $('#w_name_from').val();
        var whTo = $('#w_name_to').val();
        var quantity = $('#quantity').val();

        if(whFrom == "" && whTo == "") {
            alert(AdminStockMovementAlertEmptyWarehouses);
            return false;
        }

        if(whFrom == whTo) {
            alert(AdminStockMovementAlertSameWarehouses);
            return false;
        }

        if(quantity == "" ||  quantity <= 0 || (!isAdvancedStockNumeric(quantity))) {
            alert(AdminStockMovementAlertWrongQty);
            return false;
        }

        return true;
    });

});
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

    $('#div_product').hide();
    $('#expected_qty_block').hide();

    $(window).keypress(function(e) {
        var focusedInput = $('input:focus');
        if(focusedInput.length < 1 || focusedInput[0].id == 'barcode') {
            barcode(e);
        }
    });
});

var knownProducts = [],
    currentProduct = null;

function barcode(event){
    reset();
    if (event.keyCode == 13) { //enter
        event.preventDefault();
        scanProduct(window.parent.$('#barcode').val());
    }else{
        window.parent.$('#barcode').val(window.parent.$('#barcode').val() + event.key);
    }
    return false;
}

function scanProduct(barcode) {

    if (knownProducts[barcode]) {
        processProduct(knownProducts[barcode]);
        return true;
    }

    jQuery.ajax(
        {
            url: ProductInformationUrl,
            type: 'POST',
            data: {
                'action': 'productInformation',
                'ajax': '1',
                'barcode': barcode,
                'warehouse_id': warehouseId,
            },
            dataType: 'json',
            success: function ($data) {
                if ($data.error) {
                    setError($data.error);
                } else {
                    $data.scanned = 0;
                    $data.barcode = barcode;
                    processProduct($data);
                }
            }
        });

}

function processProduct(productInfo) {
    $('#div_product').show();
    currentProduct = productInfo;
    setOk(currentProduct.name);

    $('#product_name').html(currentProduct.name);
    $('#product_sku').html(currentProduct.sku);
    $('#product_image').attr("src", currentProduct.image_url);
    $('#product_stock').val(parseInt(currentProduct.scanned));
    $('#expected_qty').html(parseInt(currentProduct.qty));

    inc();

    if(action == 'stock_control') {
        $('#expected_qty_block').show();
    }
    addHistory(currentProduct, parseInt(currentProduct.scanned));
}

function addHistory(product, newStockLevel) {

    if(!knownProducts[product.barcode]) {
        knownProducts[product.barcode] = product;
        var html = '<tr>';

        if(product.image_url == null) {
            html += '<td></td>';
        } else {
            html += '<td><img src="' + product.image_url + '" height="30"></td>';
        }
        html += '<td class="sbi-table-cell">' + product.name + '</td>';
        html += '<td>' + product.barcode + '</td>';
        if(action == 'stock_control') {
            html += '<td>' + product.qty + '</td>';
        }
        html += '<td class="sbi-table-cell"><input ' +
            'type="text" ' +
            'name="product[' + product.product_id + '][' + product.product_attribute_id + ']" ' +
            'value="' + newStockLevel + '" ' +
            'id="product[' + product.product_id + '][' + product.product_attribute_id + ']" ' +
            'onchange="changeQte(this.value)">' +
            '</td>';
        if(action == 'stock_control') {
            html += '<td id="status[' + product.product_id + '][' + product.product_attribute_id + ']"></td>';
        }
        html += '</tr>';

        $('#table_history tr:last').after(html);

        if(action == 'stock_control') {
            checkStatus();
        }
    }
}

function setError(message){
    window.parent.$('#barcode').css('color','red');
    window.parent.$('#barcode').val(message +  ' ' + window.parent.$('#barcode').val());

    playNokSound();
}

function setOk(message){
    if(message){
        window.parent.$('#barcode').val(message);
    }
    window.parent.$('#barcode').css('color','green');
    playOkSound();
}

function reset(){

    if( window.parent.$('#barcode').css('color') != "rgb(85, 85, 85)"){
        window.parent.$('#barcode').css('color','rgb(85, 85, 85)');
        window.parent.$('#barcode').val('');
    }
}

function changeQte(qty){

    $('#product_stock').val(qty);
    currentProduct.scanned = qty;
    if(knownProducts[currentProduct.barcode]) {
        knownProducts[currentProduct.barcode].scanned = qty;
        document.getElementById('product[' + currentProduct.product_id + '][' + currentProduct.product_attribute_id + ']').value = qty;
        if(action == 'stock_control') {
            checkStatus();
        }
    }

}

function checkStatus() {
    var qtyScanned = document.getElementById('product[' + currentProduct.product_id + '][' + currentProduct.product_attribute_id + ']').value;

    if(qtyScanned == currentProduct.qty) {
        document.getElementById('status[' + currentProduct.product_id + '][' + currentProduct.product_attribute_id + ']').innerHTML = '<span style="color:green">match</span>';
    } else {
        document.getElementById('status[' + currentProduct.product_id + '][' + currentProduct.product_attribute_id + ']').innerHTML = '<span style="color:red">discrepancy</span>';
    }
}

function dec(){
    var qty = $('#product_stock').val();
    qty--;
    changeQte(qty);

}
function inc(){
    var qty = $('#product_stock').val();
    qty++;
    changeQte(qty);
}

function playOkSound()
{
    if ($("#audio_ok").get(0))
        $("#audio_ok").get(0).play();
}

function playNokSound()
{
    if ($("#audio_nok").get(0))
        $("#audio_nok").get(0).play();
}
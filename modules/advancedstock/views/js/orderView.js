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
    $("select[data-ajax='1']").change(function(){

        if(location.hash === "#refundForm"){
            return;
        }

        var select = this;
        $('.mse_error').remove();
        $.ajax({
            type: 'POST',
            async: false,
            url: ajaxUpdatePreparationWarehouse,
            data: {
                'method': "updatePreparationWarehouse",
                'warehouseId_eodId': $(select).val()
            },
            dataType: 'json',
            success: function() {
                var msg = '<i class="material-icons" style="color:#24c924;">check</i>';
                $(select).after(msg);
                $(select).css("width","80%");
                $(select).css("float","left");
                setTimeout(function(){
                    $(select).next("i").remove();
                    $(select).css("width","100%");
                }, 3000);
            },
            error: function(jqXHR) {
                var msg = '<div class="alert alert-danger mse_error">';
                jqXHR.responseJSON.errors.forEach(function(errorMsg) {
                    msg += '<p>'+ errorMsg +'</p>';
                });
                msg += '</div>';
                $(select).before(msg);
            }
        });
    });
});
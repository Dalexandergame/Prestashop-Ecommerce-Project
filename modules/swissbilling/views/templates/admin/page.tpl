{*
* NOTICE OF LICENSE
*
* Module for Prestashop
*
* 100% Swiss development
* @author Webbax <contact@webbax.ch>
* @copyright -
* @license   -
*}

<div id="div_bo_swissbilling" class="panel">

    <h2>{$displayName|escape:'htmlall':'UTF-8'}</h2>
    <link rel="stylesheet" type="text/css" href="{$_path|escape:'htmlall':'UTF-8'}views/css/styles_bo.css">

    <strong>{$module_desc|escape:'htmlall':'UTF-8'}</strong><br/>
    <br/>
    <fieldset>
        <legend>{l s='Put into service' mod='swissbilling'}</legend>
        <div>
            1. {l s='Enable only the CHF currency for Swissbilling payment module (under the Modules tab -> Payment, currency restriction section)' mod='swissbilling'}<br/>
            2. {l s='Check that the following are installed extension' mod='swissbilling'}<br/>
                <table style="margin-left:30px">
                    <tr>
                        <td><strong>Soap</strong></td>
                        <td>
                            &nbsp;:&nbsp;
                            <i>
                                {if $soap}
                                    {l s='the extension is enabled' mod='swissbilling'}
                                {else}
                                    {l s='the extension is not enabled' mod='swissbilling'}
                                {/if}
                            </i>
                        </td></tr>
                    <tr>
                        <td>
                            <strong>Open SSL</strong>
                        </td>
                        <td>
                            &nbsp;:&nbsp;
                            <i>
                                {if $openssl}
                                    {l s='the extension is enabled' mod='swissbilling'}
                                {else}
                                    {l s='the extension is not enabled' mod='swissbilling'}
                                {/if}
                            </i>
                        </td>
                    </tr>
                </table>
            3. {l s='Configure the settings below' mod='swissbilling'}<br/>
        </div>
    </fieldset>
    <br/>
    <fieldset>
        <legend>{l s='Configuration' mod='swissbilling'}</legend>
        <form method="post">
            <table border="0" cellpadding="0" cellspacing="0" id="table_swissbilling">
                    <tr><td width="250" style="height:35px;" class="td-label">ID{l s='merchant' mod='swissbilling'}</td><td><input type="text" name="merchant_id" value="{$merchant_id|escape:'htmlall':'UTF-8'}" style="width: 150px;" /></td></tr>
                    <tr><td style="height:35px;">{l s='Password' mod='swissbilling'}</td><td><input type="password" name="merchant_pw" value="{$merchant_pw|escape:'htmlall':'UTF-8'}" style="width: 150px;" /></td></tr>
                    <tr><td style="height:35px;">{l s='Private key' mod='swissbilling'}</td><td><input type="password" name="private_key" value="{$private_key|escape:'htmlall':'UTF-8'}" style="width: 150px;" /></td></tr>
                    <tr><td style="height:35px;">{l s='maximum amount accepted by Swissbilling' mod='swissbilling'}</td><td><input type="text" name="max_amount" value="{$max_amount|escape:'htmlall':'UTF-8'}" style="width: 50px;" />&nbsp;<span class="iso_currency">CHF</span></td></tr>
                    <tr><td colspan="2"><hr/></td></tr>
                    <tr><td></td><td><input type="checkbox" name="conf_mail" value="1" {if $conf_mail}checked="checked"{/if} > {l s='Send a confirmation email' mod='swissbilling'}</td></tr>
                    <tr><td colspan="2"><hr/></td></tr>
                    <tr>
                        <td style="height:35px;">{l s='Add Swissbilling costs in order PrestaShop' mod='swissbilling'}</td>
                        <td>
                            <select id="costs_order" name="costs_order">
                                <option value="0" {$selected_costs_order_no|escape:'htmlall':'UTF-8'}>{l s='No' mod='swissbilling'}
                                <option value="1" {$selected_costs_order_yes|escape:'htmlall':'UTF-8'}>{l s='Yes' mod='swissbilling'}
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="line-costs-order-mode" style="height:35px;"></td>
                        <td class="line-costs-order-mode">
                            <select name="costs_order_mode">
                                <option value="0" {$selected_costs_order_0|escape:'htmlall':'UTF-8'}>{l s='In the Prestashop order add the Swissbilling customer fee + the Swissbilling merchant fee' mod='swissbilling'}
                                <option value="1" {$selected_costs_order_1|escape:'htmlall':'UTF-8'}>{l s='In the Prestashop order add only the Swissbilling customer fee' mod='swissbilling'}
                                <option value="2" {$selected_costs_order_2|escape:'htmlall':'UTF-8'}>{l s='In the Prestashop order add only the Swissbilling merchant fee' mod='swissbilling'}    
                            </select>
                        </td>
                        <script>
                            {literal}
                            function lineFeeTypeDisplay(){
                                if($('#costs_order').val()=='0'){
                                    $('.line-costs-order-mode').css('display','none');
                                }else{
                                    $('.line-costs-order-mode').css('display','table-cell');
                                }
                            }         
                            $(document).ready(function(){
                                lineFeeTypeDisplay();
                                $('#costs_order').change(function() {
                                   lineFeeTypeDisplay();
                                });
                             });
                            {/literal}
                        </script>
                    </tr>
                    <tr>
                        <td style="height:35px;">{l s='Redirection Swissbilling' mod='swissbilling'} <i class="icon-info-circle" title="{l s='Make the payment process redirected to Swissbilling. The "No" option allows Swissbilling to be used in integrated mode. This option is only available on request.' mod='swissbilling'}"></i>    
                        </td>
                        <td>
                            <select name="redirection">
                                <option value="0" {$selected_redirection_no|escape:'htmlall':'UTF-8'}>{l s='No' mod='swissbilling'}
                                <option value="1" {$selected_redirection_yes|escape:'htmlall':'UTF-8'}>{l s='Yes' mod='swissbilling'}
                            </select>
                        </td>
                    </tr>
                      <tr><td colspan="2"><hr/></td></tr>
                     <tr>
                        <td style="height:35px;">{l s='Merchant Administration Fee' mod='swissbilling'} 
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-lg-1">
                                    <input type="text" class="form-control" name="admin_fee_amount" value="{$admin_fee_amount|escape:'htmlall':'UTF-8'}" maxlength="3">
                                </div>
                                <div class="col-lg-11">
                                    <div class="m-top-5">
                                        <strong>CHF</strong>&nbsp;&nbsp; <span class="red"><strong>{l s='Important' mod='swissbilling'}</strong> : {l s='amount according to the contract defined with Swissbilling' mod='swissbilling'}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:35px;">Pré-Screening</td>
                        <td>
                            <select name="pre_screening">
                                <option value="0" {$selected_ps_no|escape:'htmlall':'UTF-8'}>{l s='No' mod='swissbilling'}
                                <option value="1" {$selected_ps_yes|escape:'htmlall':'UTF-8'}>{l s='Yes' mod='swissbilling'}
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:35px;">{l s='Type sales' mod='swissbilling'}</td>
                        <td>
                            <select name="b2b">
                                <option value="0" {$selected_b2b_no|escape:'htmlall':'UTF-8'}>B2C
                                <option value="1" {$selected_b2b_yes|escape:'htmlall':'UTF-8'}>B2C & B2B
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:35px;">{l s='Type of operation' mod='swissbilling'}</td>
                        <td>
                            <select name="type">
                                <option value="Test" {$selected_tp_test|escape:'htmlall':'UTF-8'}>{l s='Test' mod='swissbilling'}
                                <option value="Real" {$selected_tp_real|escape:'htmlall':'UTF-8'}>{l s='Production' mod='swissbilling'}
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:35px;">{l s='Generate PDF Swissbilling Invoice' mod='swissbilling'} <i class="icon-info-circle" title="{l s='If the invoices are sent by yourself. To use this option you must have the Swissbilling agreement.' mod='swissbilling'}"></i></td>
                        <td>
                            <select id="generate_pdf" name="generate_pdf">
                                <option value="0" {$selected_gen_pdf_no|escape:'htmlall':'UTF-8'}>{l s='No' mod='swissbilling'}
                                <option value="1" {$selected_gen_pdf_yes|escape:'htmlall':'UTF-8'}>{l s='Yes' mod='swissbilling'}
                            </select>
                             <script>    
                            {literal}
                            function lineImpressionTypeDisplay(){
                                if($('#generate_pdf').val()=='0'){
                                    $('.line-impression-type').css('display','none');
                                }else{
                                    $('.line-impression-type').css('display','table-cell');
                                }
                            }         
                            $(document).ready(function(){
                                lineImpressionTypeDisplay();
                                $('#generate_pdf').change(function() {
                                   lineImpressionTypeDisplay();
                                });
                             });
                            {/literal}
                            </script>
                        </td>
                    </tr>
                    <tr>
                        <td class="line-impression-type" style="height:35px;">{l s='Type of printing' mod='swissbilling'} <i class="icon-info-circle" title="{l s='Define whether your invoice is printed for sending by email or by post.' mod='swissbilling'}"></i></td>
                        <td class="line-impression-type">
                            <select name="impression_type">
                                <option value="post" {if $impression_type=='post'}selected="selected"{/if}>{l s='Sending by Post' mod='swissbilling'}
                                <option value="mail" {if $impression_type=='mail'}selected="selected"{/if}>{l s='Sending by mail' mod='swissbilling'}
                                <option value="all" {if $impression_type=='all'}selected="selected"{/if}>{l s='Sending by email or Post' mod='swissbilling'}
                            </select>
                        </td>
                    </tr>
                    <tr><td colspan="2"><hr/></td></tr>
                    <tr>
                        <td style="height:35px;">{l s='Immediate automatic validation' mod='swissbilling'}</td>
                        <td>
                            <select id="auto_validation" name="auto_validation">
                                <option value="0" {$selected_auto_validation_no|escape:'htmlall':'UTF-8'}>{l s='No' mod='swissbilling'}
                                <option value="1" {$selected_auto_validation_yes|escape:'htmlall':'UTF-8'}>{l s='Yes' mod='swissbilling'}
                            </select>
                            <script>
                                $(document ).ready(function(){
                                    $('#auto_validation').change(function(){
                                        var display = $('.validation_cron').css('display');
                                        if(display=='none'){
                                            $('.validation_cron').css('display','block');
                                        }else{
                                            $('.validation_cron').css('display','none');
                                        }
                                    });
                                });
                            </script>


                            <table style="{if $selected_auto_validation_yes}display:none;{/if}" class="validation_cron">
                                <tr>
                                    <td class="text-center">
                                        <hr/>
                                            <strong>{l s='massive validation' mod='swissbilling'}</strong>
                                        <hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        {l s='Validation CRON url' mod='swissbilling'} :<br/>
                                        <a href="{$url_cron_validation|escape:'htmlall':'UTF-8'}" target="_blank">{$url_cron_validation|escape:'htmlall':'UTF-8'}</a><br/>
                                        <br/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        {l s='Validation CRON orders with status' mod='swissbilling'} :<br/>
                                        <select name="order_state_validation">
                                            {foreach from=$order_states item=os}
                                                <option value="{$os.id_order_state|escape:'htmlall':'UTF-8'}" {if $order_state_validation==$os.id_order_state}selected="selected"{/if}>{$os.name|escape:'htmlall':'UTF-8'}
                                           {/foreach}
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-inline" style="margin-top:10px;">
                                            {l s='Send the bill after' mod='swissbilling'}
                                            <div class="form-group">
                                                <input type="text" name="nb_days_validation" value="{$nb_days_validation|escape:'htmlall':'UTF-8'}" style="width:50px;" maxlength="2" /> {l s='days' mod='swissbilling'}
                                            </div>
                                        </div>       
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <tr style="display:none;">
                        <td style="height:35px;">{l s='Order status in Swissbilling once the order is confirmed.' mod='swissbilling'}</td>
                        <td>
                            <select name="delivery_status">
                                <option value="pending" {$selected_pending|escape:'htmlall':'UTF-8'} >{l s='Waiting' mod='swissbilling'}
                                <option value="sent" {$selected_sent|escape:'htmlall':'UTF-8'} >{l s='Sent' mod='swissbilling'}
                                <option value="distributed" {$selected_distribued|escape:'htmlall':'UTF-8'} >{l s='Distributed' mod='swissbilling'}
                            </select>
                        </td>
                    </tr>
                    <tr><td colspan="2"><hr/></td></tr>
                    <tr><td></td><td align="left"><input class="button btn btn-default" name="btnSubmitConfig" value="{l s='Update' mod='swissbilling'}" type="submit" /></td></tr>
            </table>
        </form>
    </fieldset>

    <br/>

    <fieldset>
        <legend>{l s='Logs' mod='swissbilling'}</legend>
        <a href="{$link_logs|escape:'htmlall':'UTF-8'}" target="_blank">
            <img src="{$_path|escape:'htmlall':'UTF-8'}views/img/log.png" style="vertical-align:bottom" />
            &nbsp;{l s='Check the logs back office' mod='swissbilling'}
        </a>
    </fieldset>

    <br/> 

    <fieldset>
        <legend>{l s='Orders' mod='swissbilling'} / {l s='massive validation' mod='swissbilling'}</legend>
        <label>{l s='Last validation scheduled task' mod='swissbilling'} :</label> {if empty($last_cron)}-{else}{$last_cron|escape:'htmlall':'UTF-8'}{/if}<br/>
        <br/>
        <table class="table">
            <tr>
                <td><i>id_cart</i></td>
                <td><i>id_order</i></td>
                <td><i>timestamp</i></td>
                <td><i>validate</i></td>
                <td><i>error</i></td>
            </tr>
            {foreach from=$orders_swissbilling item=order}
                <tr>
                    <td>{$order.id_cart|escape:'htmlall':'UTF-8'}</td>
                    <td>{$order.Order->id|escape:'htmlall':'UTF-8'}</td>
                    <td>{$order.timestamp|escape:'htmlall':'UTF-8'}</td>
                    <td>
                        {if $order.validate=='0000-00-00 00:00:00'}
                            {l s='no' mod='swissbilling'}
                        {else}
                            {l s='yes' mod='swissbilling'} / {$order.validate|escape:'htmlall':'UTF-8'}
                        {/if}
                    </td>
                    <td>{$order.error|escape:'htmlall':'UTF-8'}</td>
                 </tr>
            {/foreach}
            {if empty($orders_swissbilling)}
                <tr><td colspan="5">-</td></tr>
            {/if}
        </table>
    </fieldset>
    
</div>
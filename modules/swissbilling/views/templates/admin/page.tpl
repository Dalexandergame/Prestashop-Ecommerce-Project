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
<br/>
<fieldset>
    <legend><img src="{$_path|escape:'htmlall':'UTF-8'}logo.gif" /> {l s='Put into service' mod='swissbilling'}</legend>
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
    <legend><img src="{$_path|escape:'htmlall':'UTF-8'}logo.gif" alt="" title="" />&nbsp;{l s='Configuration' mod='swissbilling'}</legend>
    <form method="post">
        <table border="0" width="500" cellpadding="0" cellspacing="0" id="table_swissbilling">
                <tr><td width="250" style="height:35px;">ID{l s='merchant' mod='swissbilling'}</td><td><input type="text" name="merchant_id" value="{$merchant_id|escape:'htmlall':'UTF-8'}" style="width: 150px;" /></td></tr>
                <tr><td style="height:35px;">{l s='Password' mod='swissbilling'}</td><td><input type="password" name="merchant_pw" value="{$merchant_pw|escape:'htmlall':'UTF-8'}" style="width: 150px;" /></td></tr>
                <tr><td style="height:35px;">{l s='Private key' mod='swissbilling'}</td><td><input type="password" name="private_key" value="{$private_key|escape:'htmlall':'UTF-8'}" style="width: 150px;" /></td></tr>
                <tr><td style="height:35px;">{l s='maximum amount accepted by Swissbilling' mod='swissbilling'}</td><td><input type="text" name="max_amount" value="{$max_amount|escape:'htmlall':'UTF-8'}" style="width: 50px;" />&nbsp;<span class="iso_currency">CHF</span></td></tr>
                <tr><td colspan="2"><hr style="height:10px;margin:0px 0px 10px 0px;border-bottom:1px solid #aaaaaa;font-size:1px; "/></td></tr>
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
                    <td style="height:35px;">{l s='Automatic validation' mod='swissbilling'}</td>
                    <td>
                        <select name="auto_validation">
                            <option value="0" {$selected_auto_validation_no|escape:'htmlall':'UTF-8'}>{l s='No' mod='swissbilling'}
                            <option value="1" {$selected_auto_validation_yes|escape:'htmlall':'UTF-8'}>{l s='Yes' mod='swissbilling'}
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="height:35px;">{l s='Order status in Swissbilling once the order is confirmed.' mod='swissbilling'}</td>
                    <td>
                        <select name="delivery_status">
                            <option value="pending" {$selected_pending|escape:'htmlall':'UTF-8'} >{l s='Waiting' mod='swissbilling'}
                            <option value="sent" {$selected_sent|escape:'htmlall':'UTF-8'} >{l s='Sent' mod='swissbilling'}
                            <option value="distributed" {$selected_distribued|escape:'htmlall':'UTF-8'} >{l s='Distributed' mod='swissbilling'}
                        </select>
                    </td>
                </tr>
                <tr><td colspan="2"><hr style="height:10px;margin:0px 0px 10px 0px;border-bottom:1px solid #aaaaaa;font-size:1px; "/></td></tr>
                 <tr>
                    <td>{l s='Management fees for the merchant (recommended to leave at zero)' mod='swissbilling'}</td>
                    <td><input type="text" name="admin_fee_amount" value="{$admin_fee_amount|escape:'htmlall':'UTF-8'}" style="width:30px"><span class="iso_currency">&nbsp;CHF</span></td>
                </tr>
                <tr><td colspan="2"><hr/></td></tr>
                <tr><td></td><td align="left"><input class="button btn btn-default" name="btnSubmitConfig" value="{l s='Update' mod='swissbilling'}" type="submit" /></td></tr>
        </table>
    </form>
</fieldset>
                                     
<script type="text/javascript">
    var iso = '{$isoTinyMCE|escape:'htmlall':'UTF-8'}';
    var pathCSS = '{$theme_css_dir|escape:'htmlall':'UTF-8'}';
    var ad = '{$ad|escape:'htmlall':'UTF-8'}';
</script>

<script type="text/javascript" src="{$ps_base_uri|escape:'htmlall':'UTF-8'}js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="{$ps_base_uri|escape:'htmlall':'UTF-8'}js/tinymce.inc.js"></script>

<script type="text/javascript">
    $(document).ready(function(){
        tinySetup({
            editor_selector :"autoload_rte",
        });
    });
</script>

<script type="text/javascript">
    id_language = Number({$defaultLanguage|escape:'htmlall':'UTF-8'});
</script> 

<form method="post" enctype="multipart/form-data">
    <br/>
    <fieldset>
        <legend><img src="{$_path|escape:'htmlall':'UTF-8'}logo.gif" alt="" title="" /> {$displayName|escape:'htmlall':'UTF-8'}</legend>
        <div class="margin-form">
            <div class="clear"></div>
        </div>
        <label>{l s='Additional information in the payment process' mod='swissbilling'}</label>
        <div class="margin-form">
        {foreach from=$languages item=l}
            <div id="info_prepmt_{$l.id_lang|escape:'htmlall':'UTF-8'}" style="display:{if $l.id_lang == $defaultLanguage}block{else}none{/if};float:left;">
                <textarea class="rte autoload_rte" cols="70" rows="10" id="body_info_prepmt_{$l.id_lang|escape:'htmlall':'UTF-8'}" name="body_info_prepmt_{$l.id_lang|escape:'htmlall':'UTF-8'}">{$info_prepmt[$l.id_lang]}</textarea>
            </div>
        {/foreach}
        {$displayFlags}
        </div>
        <div class="clear pspace clearfix"></div>
        <div class="margin-form clear"><input type="submit" name="btnSubmitParams" value="{l s='Update' mod='swissbilling'}" class="button btn btn-default" /></div>
    </fieldset>
</form>
        
<br/>

<fieldset>
    <legend><img src="{$_path|escape:'htmlall':'UTF-8'}logo.gif" />&nbsp;{l s='Logs' mod='swissbilling'}</legend>
    <a href="{$link_logs|escape:'htmlall':'UTF-8'}" target="_blank">
        <img src="{$_path|escape:'htmlall':'UTF-8'}views/img/log.png" style="vertical-align:bottom" />
        &nbsp;{l s='Check the logs back office' mod='swissbilling'}
    <a/>
</fieldset>
{*
* 2007-2012 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 9608 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<tbody>
{if count($list)}
{foreach $list AS $index => $tr}
	<tr
	{if $position_identifier}id="tr_{$id_category}_{$tr.$identifier}_{if isset($tr.position['position'])}{$tr.position['position']}{else}0{/if}"{/if}
	class="{if $index is odd}alt_row{/if} {if $row_hover}row_hover{/if}"
	{if isset($tr.color) && $color_on_bg}style="background-color: {$tr.color}"{/if}
	>
		<td class="center">
			{if {$has_bulk_actions}}
				{if isset($list_skip_actions.delete)}
					{if !in_array($tr.$identifier, $list_skip_actions.delete)}
						<input type="checkbox" name="{$table}Box[]" value="{$tr.$identifier}" class="noborder" />
					{/if}
				{else}
					<input type="checkbox" name="{$table}Box[]" value="{$tr.$identifier}" class="noborder" />
				{/if}
			{/if}
		</td>
		{foreach $fields_display AS $key => $params}
			{block name="open_td"}
				<td
					{if isset($params.position)}
						id="td_{if !empty($id_category)}{$id_category}{else}0{/if}_{$tr.$identifier}"
					{/if}
					class="{if !$no_link}pointer{/if}
					{if isset($params.position) && $order_by == 'position'  && $order_way != 'DESC'} dragHandle{/if}
					{if isset($params.align)} {$params.align}{/if}">
				
			{/block}
			{block name="td_content"}
				{if isset($params.prefix)}{$params.prefix}{/if}
				{if isset($params.color) && isset($tr[$params.color])}
					<span class="color_field" style="background-color:{$tr.color};color:{if Tools::getBrightness($tr.color) < 128}white{else}#383838{/if}">
				{/if}

				{if isset($params.active)}
					{$tr.$key}
				{elseif isset($params.activeVisu)}
					<img src="../img/admin/{if $tr.$key}enabled.gif{else}disabled.gif{/if}"
					alt="{if $tr.$key}{l s='Enabled'}{else}{l s='Disabled'}{/if}" title="{if $tr.$key}{l s='Enabled'}{else}{l s='Disabled'}{/if}" />
				{elseif isset($params.position)}
					{if $order_by == 'position' && $order_way != 'DESC'}
						<a href="{$tr.$key.position_url_down}" {if !($tr.$key.position != $positions[count($positions) - 1])}style="display: none;"{/if}>
							<img src="../img/admin/{if $order_way == 'ASC'}down{else}up{/if}.gif" alt="{l s='Down'}" title="{l s='Down'}" />
						</a>

						<a href="{$tr.$key.position_url_up}" {if !($tr.$key.position != $positions.0)}style="display: none;"{/if}>
							<img src="../img/admin/{if $order_way == 'ASC'}up{else}down{/if}.gif" alt="{l s='Up'}" title="{l s='Up'}" />
						</a>
					{else}
						{$tr.$key.position + 1}
					{/if}
				{elseif isset($params.image)}
					{$tr.$key}
				{elseif isset($params.icon)}
					<img src="../img/admin/{$tr[$key]['src']}" alt="{$tr[$key]['alt']}" title="{$tr[$key]['alt']}" />
				{elseif isset($params.price)}
					{$tr.$key}
				{elseif isset($params.float)}
					{$tr.$key}
				{elseif isset($params.type) && $params.type == 'date'}
					{$tr.$key}
				{elseif isset($params.type) && $params.type == 'datetime'}
					{$tr.$key}
				{elseif isset($params.type) && $params.type == 'decimal'}
					{$tr.$key|string_format:"%.2f"}
				{elseif isset($params.type) && $params.type == 'percent'}
					{$tr.$key} {l s='%'}
				{* If type is 'editable', an input is created *}
				{elseif isset($params.type) && $params.type == 'editable' && isset($tr.id)}
					<input type="text" name="{$key}_{$tr.id}" value="{$tr.$key|escape:'htmlall':'UTF-8'}" class="{$key}" />
				{elseif isset($params.callback)}
					{$tr.$key}
				{elseif isset($tr.$key) && $key == 'color'}
					<div style="float: left; width: 18px; height: 12px; border: 1px solid #996633; background-color: {$tr.$key}; margin-right: 4px;"></div>
				{elseif isset($tr.$key)}
					{if isset($params.maxlength) && Tools::strlen($tr.$key) > $params.maxlength}
						<span title="{$tr.$key|escape:'htmlall':'UTF-8'}">{$tr.$key|truncate:$params.maxlength:'...'|escape:'htmlall':'UTF-8'}</span>
					{else}
						{$tr.$key|escape:'htmlall':'UTF-8'}
					{/if}
				{else}
					{block name="default_field"}--{/block}
				{/if}
				{if isset($params.suffix)}{$params.suffix}{/if}
				{if isset($params.color) && isset($tr.color)}
					</span>
				{/if}
				</td>
			{/block}
		{/foreach}

	{if $shop_link_type}
		<td class="center" title="{$tr.shop_name}">
			{if isset($tr.shop_short_name)}
				{$tr.shop_short_name}
			{else}
				{$tr.shop_name}
			{/if}</td>
	{/if}
	{if $has_actions}
		<td class="center" style="white-space: nowrap;">
			{foreach $actions AS $action}
				{if isset($tr.$action)}
					{$tr.$action}
				{/if}
			{/foreach}
		</td>
	{/if}
	</tr>
{/foreach}
{else}
	<tr><td class="center" colspan="{count($fields_display) + 2}">{l s='No items found'}</td></tr>
{/if}
</tbody>
<tfoot>
    <tr class="is_not_commande">
        <td colspan="11" style="padding: 10px; color: #000;">
            
            <form action="{$link->getAdminLink('AdminPlanningRetourByCarrier')|escape:'htmlall':'UTF-8'}&action=sendMailsRetour" method="POST" id="myFormSendMail" class="result">
                <fieldset>
                    <legend>Envoyer des emails aux clients</legend>
                    <label>Sujet</label>
                    <div class="margin-form">
                        <input type="text" name="sujet" required="" class="form-control sujet text-left"  placeholder="Sujet">
                        <div class="clear"></div>
                    </div>
                    <label>Message d'email</label>
                    <div class="margin-form">
                        <textarea class="form-control msg_mail" required="" rows="6" cols="60" name="description" placeholder="Message d'email"></textarea>
                        <div class="clear"></div>
                    </div>
                    <div class="margin-form">
                        <button type="submit" name="envoimail" class="btn btn-primary" style="color: #000;">Envoyer les emails</button>
                    </div>
                </fieldset>               
            </form>
            
            <script type="text/javascript">
                $(function(){
                    
                    $('.title_box:last').append($('<input />').attr({ type: 'checkbox', 'checked': 'checked' }).addClass('check_all').css({ 'margi-left' :'4px' }));
                    $('.title_box:last .check_all').click(function(e){
                        var $me = $(this), checked = $me.is(':checked');
                        console.info(checked);
                        $.each( $('.inp_id_order'),function(){
                            $(this).prop('checked',checked);
                        });
                    });
                    $('form#myFormSendMail').submit(function(e){
                        e.preventDefault();
                        var $form = $(this), isSubmit = $form.data("isSubmit"), ids_orders = [],$input_checked = $('.inp_id_order'),
                                subject = $.trim($form.find('input.sujet').val()),msg_mail = $.trim($form.find('textarea.msg_mail').val())
                        ;
                        if(!isSubmit){
                       
                            if( subject == "" || msg_mail == ""){
                                showErrorMessage("Erreur : remplir les champs sujet et message d'email");
                                return false;
                            }
                            if($input_checked.length ){
                                $.each( $input_checked,function(k,v){
                                    if($(this).is(':checked'))
                                        ids_orders.push(parseInt($(v).val()));
                                });
                                /**/                            
                                $.ajax({
                                    url : $form.attr('action'),
                                    data : {
                                            ajax : true,
                                            action : "sendMailsRetour",
                                            subject: subject,
                                            message: msg_mail,
                                            orders_ids : ids_orders,
                                            },
                                    type : "POST",
                                    dataType: "json",
                                    success : function(data){
                                        try {
                                            if(data.success){
                                                showSuccessMessage(data.msg);
                                            }else{
                                                showErrorMessage(data.msg);
                                            }
                                        }catch(err) {
                                            showErrorMessage("Erreur");
                                        }
                                    },
                                    error : function(msg){
                                        showNoticeMessage("Erreur ");
                                    }
                                });

                                /**/
                            }else{
                                showErrorMessage("Erreur : Vous devez selectionner au moins une commande !");
                            }
                        }
                    });
                });
            </script>
        </td>
    </tr>
</tfoot>
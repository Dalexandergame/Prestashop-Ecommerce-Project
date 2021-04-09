{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{capture name=path}{l s='Contact'}{/capture}
<div class="msg_info">
    {l s='Avant de nous contacter, vérifiez que la réponse à votre question ne se trouve pas dans notre page de '}
    <a href="{$link->getCMSLink('9', 'questions-frequantes')}" title="{l s='Questions fréquentes'}">{l s='Questions fréquentes'}</a>
</div>
<h1 class="page-subheading bottom-indent">
	{l s='Customer service'} - {if isset($customerThread) && $customerThread}{l s='Your reply'}{else}{l s='Contact us'}{/if}
</h1>
{if isset($confirmation)}
	<p class="alert alert-success">{l s='Your message has been successfully sent to our team.'}</p>
	<ul class="footer_links clearfix">
		<li>
            <a class="btn btn-default link-home" href="{if $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}">
				<span>
					<i class="icon-angle-left"></i>{l s='Home'}
				</span>
			</a>
		</li>
	</ul>
{elseif isset($alreadySent)}
	<p class="alert alert-warning">{l s='Your message has already been sent.'}</p>
	<ul class="footer_links clearfix">
		<li>
            <a class="btn btn-default button button-small" href="{if $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}">
				<span>
					<i class="icon-chevron-left"></i>{l s='Home'}
				</span>
			</a>
		</li>
	</ul>
{else}
	{include file="$tpl_dir./errors.tpl"}
	<form action="{$request_uri}" method="post" class="contact-form-box" enctype="multipart/form-data">
		<fieldset>
			<!-- <h3 class="page-subheading">{l s='send a message'}</h3> -->
			<div class="clearfix">
				<div class="row">
					<div class="form-group selector1 col-md-6 col-xs-12">
{*						<label for="id_contact">{l s='Subject Heading'}</label>*}
					{if isset($customerThread.id_contact) && $customerThread.id_contact}
							{foreach from=$contacts item=contact}
								{if $contact.id_contact == $customerThread.id_contact}
									<input type="text" class="form-control" id="contact_name" name="contact_name" value="{$contact.name|escape:'html':'UTF-8'}" readonly="readonly" />
									<input type="hidden" name="id_contact" value="{$contact.id_contact}" />
								{/if}
							{/foreach}
					</div>
					{else}
						<select id="id_contact" class="form-control" name="id_contact">
							<option value="0">{l s='Subject Heading'}</option>
							{foreach from=$contacts item=contact}
								<option value="{$contact.id_contact|intval}"{if isset($smarty.request.id_contact) && $smarty.request.id_contact == $contact.id_contact} selected="selected"{/if}>{$contact.name|escape:'html':'UTF-8'}</option>
							{/foreach}
						</select>
					</div>
						<p id="desc_contact0" class="desc_contact{if isset($smarty.request.id_contact)} unvisible{/if}">&nbsp;</p>
						{foreach from=$contacts item=contact}
							<p id="desc_contact{$contact.id_contact|intval}" class="desc_contact contact-title{if !isset($smarty.request.id_contact) || $smarty.request.id_contact|intval != $contact.id_contact|intval} unvisible{/if}">
								<i class="icon-comment-alt"></i>{$contact.description|escape:'html':'UTF-8'}
							</p>
						{/foreach}
					{/if}
					<div class="form-group  col-md-6 col-xs-12">
{*						<label for="email">{l s='Email address'}</label>*}
						{if isset($customerThread.email)}
                                                    <input class="form-control grey" type="text" id="email" name="from" value="{$customerThread.email|escape:'html':'UTF-8'}" readonly="readonly" placeholder="{l s='Email address'}"/>
						{else}
                                                    <input class="form-control grey validate" type="text" id="email" name="from" data-validate="isEmail" value="{$email|escape:'html':'UTF-8'}" placeholder="{l s='Email address'}" />
						{/if}
					</div>
					{if !$PS_CATALOG_MODE}
						{if (!isset($customerThread.id_order) || $customerThread.id_order > 0)}
							<div class="form-group selector1 select11 col-md-6 col-xs-12">
{*								<label>{l s='Order reference'}</label>*}
								{if !isset($customerThread.id_order) && isset($is_logged) && $is_logged}
									<select name="id_order" class="form-control">
										<option value="0">{l s='Order reference'}</option>
										{foreach from=$orderList item=order}
											<option value="{$order.value|intval}"{if $order.selected|intval} selected="selected"{/if}>{$order.label|escape:'html':'UTF-8'}</option>
										{/foreach}
									</select>
								{elseif !isset($customerThread.id_order) && empty($is_logged)}
                                                                    <input class="form-control grey" type="text" name="id_order" id="id_order" value="{if isset($customerThread.id_order) && $customerThread.id_order|intval > 0}{$customerThread.id_order|intval}{else}{if isset($smarty.post.id_order) && !empty($smarty.post.id_order)}{$smarty.post.id_order|escape:'html':'UTF-8'}{/if}{/if}" placeholder="{l s='Order reference'}" />
								{elseif $customerThread.id_order|intval > 0}
                                                                    <input class="form-control grey" type="text" name="id_order" id="id_order" value="{if isset($customerThread.reference) && $customerThread.reference}{$customerThread.reference|escape:'html':'UTF-8'}{else}{$customerThread.id_order|intval}{/if}" readonly="readonly" placeholder="{l s='Order reference'}"/>
								{/if}
							</div>
						{/if}
						{if isset($is_logged) && $is_logged}
							<div class="form-group selector1 select22 col-md-6 col-xs-12 {if isset($is_logged) && $is_logged} hide{/if}">
{*								<label class="unvisible">{l s='Product'}</label>*}
								{if !isset($customerThread.id_product)}
									{foreach from=$orderedProductList key=id_order item=products name=products}
										<select name="id_product" id="{$id_order}_order_products" class="unvisible product_select form-control"{if !$smarty.foreach.products.first} style="display:none;"{/if}{if !$smarty.foreach.products.first} disabled="disabled"{/if}>
											<option value="0">{l s='Product'}</option>
											{foreach from=$products item=product}
												<option value="{$product.value|intval}">{$product.label|escape:'html':'UTF-8'}</option>
											{/foreach}
										</select>
									{/foreach}
								{elseif $customerThread.id_product > 0}
									<input  type="hidden" name="id_product" id="id_product" value="{$customerThread.id_product|intval}" readonly="readonly" />
								{/if}
							</div>
						{/if}
					{/if}
					{if $fileupload == 1}
						<div class="form-group col-md-6 col-xs-12">
{*							<label for="fileUpload">{l s='Attach File'}</label>*}
							<input type="hidden" name="MAX_FILE_SIZE" value="{if isset($max_upload_size) && $max_upload_size}{$max_upload_size|intval}{else}2000000{/if}" />
							<input type="file" name="fileUpload" id="fileUpload" class="form-control" />
						</div>
					{/if}
				</div>
				<div class="row">
					<div class="form-group col-md-12 col-xs-12">
{*						<label for="message">{l s='Message'}</label>*}
                                                <textarea class="form-control" id="message" name="message" placeholder="{l s='Message'}">{if isset($message)}{$message|escape:'html':'UTF-8'|stripslashes}{/if}</textarea>
					</div>
				</div>
			</div>
			<div class="submit text-center">
				<button type="submit" name="submitMessage" id="submitMessage" class=" link-home btn btn-default"><span>{l s='Send'}<!-- <i class="icon-chevron-right right"></i> --></span></button>
			</div>
		</fieldset>
	</form>
        
        <div class="row text-contact">
            <div class="col-md-6 col-xs-12 no-margin">
                <div class=" col-xs-12">
                    <strong>{l s='Si vous avez un problème de livraison'}</strong><br/>
                    {*<br/>
                    {l s='contactez directement la Poste :'}<br />*}
                    <br/>
                    {l s='Service à la clientèle'} <br/>
                    {l s='Téléphone : 0848 888 888'} <br/>
                    {l s='E-mail : serviceclientele@poste.ch'}<br/>
                    <br/>
                    <br/>
                    <br/>
                </div>
                <div class=" col-xs-12" style="margin-bottom:20px">
                    <strong>{l s='Standard téléphonique'}</strong><br/>
                    <br/>
                    {l s='Vous pouvez nous joindre par téléphone'}<br/>
                    {l s='du Lundi au Vendredi de'} <br/>
                    {l s='9h00 à 12h00 et 13h00 à 17h00'}<br />
                    <br/>
                    {l s='Tél.: +41 (0)79 460 09 92'}<br/>
                    {l s='Fermeture du 17.01.15 au 10.10.15'}<br/>
                </div>
            </div>
            <div class="col-md-6 col-xs-12">
                
                <div class="bl_right">
                	<strong>{l s='Ecosapin sàrl'}</strong><br/>
                	<span>
                	    {l s='le château'}<br/>
                	    {l s='1116 cottens'}<br/>
                	    {l s='suisse'}<br/>
                	</span>
                	<br/><br/><br/>
                	<strong>{l s='Email: contact@ecosapin.ch'}</strong><br/>
                	<span>{l s='Boîte mail lue toute l\'année'}</span>
                </div>
            </div>
        </div>
{/if}
{addJsDefL name='contact_fileDefaultHtml'}{l s='Attach File' js=1}{/addJsDefL}
{addJsDefL name='contact_fileButtonHtml'}{l s='Choose File' js=1}{/addJsDefL}

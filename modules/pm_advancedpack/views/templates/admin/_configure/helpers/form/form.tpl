{**
 * pm_advancedpack
 *
 * @author    Presta-Module.com <support@presta-module.com> - https://www.presta-module.com
 * @copyright Presta-Module - https://www.presta-module.com
 * @license   Commercial
 *
 *           ____     __  __
 *          |  _ \   |  \/  |
 *          | |_) |  | |\/| |
 *          |  __/   | |  | |
 *          |_|      |_|  |_|
 *}

{extends file="helpers/form/form.tpl"}

{block name="label"}
	{if $input.type != 'html'}
		{if isset($input.label)}
			<label class="control-label col-lg-4{if isset($input.required) && $input.required && $input.type != 'radio'} required{/if}">
				{if isset($input.hint)}
				<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{if is_array($input.hint)}
							{foreach $input.hint as $hint}
								{if is_array($hint)}
									{$hint.text|escape:'html':'UTF-8'}
								{else}
									{$hint|escape:'html':'UTF-8'}
								{/if}
							{/foreach}
						{else}
							{$input.hint|escape:'html':'UTF-8'}
						{/if}">
				{/if}
				{$input.label}
				{if isset($input.hint)}
				</span>
				{/if}
			</label>
		{/if}
	{/if}
{/block}

{block name="input"}
	{if $input.type == 'cron'}
		<div id="postponeUpdatePackSpecificPrice-container">
			<div class="alert alert-info">
				<p>
					{l s='Cron URL' mod='pm_advancedpack'}: <a href="{$cronURL}" target="_blank">{$cronURL}</a>
				</p>
			</div>
		</div>

	{elseif $input.type == 'advancedstyles'}
		<fieldset id="ap_advanced_styles_fieldset">
			<div class="dynamicTextarea" style="width:95%;">
				<textarea style="height:150px" rows="5" name="PM_{$module_prefix|escape:'html':'UTF-8'}_ADVANCED_STYLES" id="AP_css">{pm_advancedpack::getAdvancedStylesDb()}{* HTML *}</textarea>
			</div>

			<div class="clear"></div>

			<script type="text/javascript">
				var editorAP_css = CodeMirror.fromTextArea(
					document.getElementById("AP_css"),
					{ldelim}
						mode: "css",
						lineNumbers: true,
						autofocus: false
					{rdelim}
				);
			</script>
		</fieldset>

	{elseif $input.type == 'html'}
		{$input.html_content}{* HTML *}

	{elseif $input.type == 'native_pack_migration'}
			{if !sizeof($nativeIdsPacksList)}
				<div class="alert alert-success">
					<p>{l s='There is no native pack to migrate. You are all done!' mod='pm_advancedpack'}</p>
				</div>
			{else}
				<p>
					{l s='There is %d native pack to migrate, click on the button below to proceed.' mod='pm_advancedpack' sprintf=[sizeof($nativeIdsPacksList)]}
				</p>
				<div class="text-center">
					<button type="submit" name="processNativePackMigration" class="btn btn-primary">{l s='Transform and migrate all native packs to Advanced Pack' mod='pm_advancedpack'}</button>
				</div>
			{/if}
	{elseif $input.type == 'gradientcolor'}
		<div class="margin-form {$input.form_group_class|escape:'html':'UTF-8'}">
			<input size="20" type="text" name="{$input.name|escape:'html':'UTF-8'}[0]" id="{$input.name|escape:'html':'UTF-8'}_0" class="colorPickerInput ui-corner-all ui-input-pm" value="{(!$fields_value[$input.name][0]) ? '' : $fields_value[$input.name][0]}" size="20" style="width:160px;float:left;" />
			<span {(isset($fields_value[$input.name][1]) && $fields_value[$input.name][1]) ? '' : 'style="display:none"'} id="{$input.name|escape:'html':'UTF-8'}_gradient"><input size="20" type="text" class="colorPickerInput ui-corner-all ui-input-pm" name="{$input.name|escape:'html':'UTF-8'}[1]" id="{$input.name|escape:'html':'UTF-8'}_1" value="{(!isset($fields_value[$input.name][1]) || ! $fields_value[$input.name][1]) ? '' : $fields_value[$input.name][1]}" size="20" style="margin-left:10px;width:160px;float:left;" /></span>
			<span id="{$input.name|escape:'html':'UTF-8'}_gradient" style="float:left;margin-left:10px;margin-top:6px;">
				<input type="checkbox" name="{$input.name|escape:'html':'UTF-8'}_gradient" value="1" {(isset($fields_value[$input.name][1]) && $fields_value[$input.name][1]) ? 'checked=checked' : ''} class="makeGradient" /> {l s='Make a gradient' mod='pm_advancedpack'}
			</span>
		</div>

	{elseif $input.type == 'color'}
		<div class="margin-form {$input.form_group_class|escape:'html':'UTF-8'}">
			<input size="20" type="text" name="{$input.name|escape:'html':'UTF-8'}" class="colorPickerInput ui-corner-all ui-input-pm" value="{(!$fields_value[$input.name]) ? '' : $fields_value[$input.name]}" size="20" style="width:160px;float:left;" />
		</div>

	{else}
		{$smarty.block.parent}
	{/if}
{/block}

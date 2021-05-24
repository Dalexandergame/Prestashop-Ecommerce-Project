<div id="product-new-pack" class="panel product-tab">
	<h3>{l s='Pack configuration:' mod='pm_advancedpack'}</h3>

	<input type="hidden" id="ap5_is_edited_pack" name="ap5_is_edited_pack" value="0" />
	<input type="hidden" id="ap5_is_major_edited_pack" name="ap5_is_major_edited_pack" value="0" />
	<input type="hidden" id="ap5_pack_positions" name="ap5_pack_positions" value="" />

	<div class="form-group">
		<div class="col-lg-7">
			<h4>{l s='Price rules' mod='pm_advancedpack'}</h4>
			<div class="radio">
				<label for="ap5_price_rules_4">
					<input type="radio" id="ap5_price_rules_4" name="ap5_price_rules" class="ap5_price_rules" value="4"{if $packPriceRules == 4} checked="checked"{/if} />
					{l s='Do not apply any discount for this pack' mod='pm_advancedpack'}
				</label>
			</div>
			<div class="radio">
				<label for="ap5_price_rules_1">
					<input type="radio" id="ap5_price_rules_1" name="ap5_price_rules" class="ap5_price_rules" value="1"{if $packPriceRules == 1} checked="checked"{/if} />
					{l s='Apply a global percentage discount on each pack\'s products' mod='pm_advancedpack'}
				</label>
			</div>
			<div class="radio">
				<label for="ap5_price_rules_2">
					<input type="radio" id="ap5_price_rules_2" name="ap5_price_rules" class="ap5_price_rules" value="2"{if $packPriceRules == 2} checked="checked"{/if} />
					{l s='Apply a custom discount by product' mod='pm_advancedpack'}
				</label>
			</div>
			<div class="radio">
				<label for="ap5_price_rules_3">
					<input type="radio" id="ap5_price_rules_3" name="ap5_price_rules" class="ap5_price_rules" value="3"{if $packPriceRules == 3} checked="checked"{/if} />
					{l s='Apply a specific price for this pack' mod='pm_advancedpack'} <em>({l s='will disable all product\'s discounts' mod='pm_advancedpack'})</em>
				</label>
			</div>

			<div id="ap5_price_rules_1_configuration" class="ap5-admin-hide hide">
				<label class="control-label col-lg-4" for="ap5_global_percentage_discount">
					<span class="label-tooltip" data-toggle="tooltip" title="{l s='Enter a fixed percentage discount for all the products of your pack' mod='pm_advancedpack'}">
						{l s='Global percentage discount' mod='pm_advancedpack'}
					</span>
				</label>
				<div class="col-lg-4">
					<div class="input-group">
						<input min="0" max="100" type="text" onchange="this.value = this.value.replace(/,/g, '.');" value="{$packFixedPercentage|floatval}" id="ap5_global_percentage_discount" name="ap5_global_percentage_discount" class="ap5_global_percentage_discount" maxlength="6" />
						<span class="input-group-addon"> %</span>
					</div>
				</div>
			</div>

			<div id="ap5_price_rules_3_configuration" class="ap5-admin-hide hide">
				{foreach from=$packIdGroupList key=idGroup item=groupName}
				<div class="row">
					<label class="control-label col-lg-8" for="ap5_fixed_pack_price">
						<span class="label-tooltip" data-toggle="tooltip" title="{l s='Enter a fixed amount for your pack' mod='pm_advancedpack'}">
							{l s='Pack price' mod='pm_advancedpack'} - {if $idGroup == 3}{l s='Default group' mod='pm_advancedpack'}{else}{$groupName|escape:'html':'UTF-8'}{/if}<em class="ap5-fixed-pack-price-without-taxes{if $idTaxRulesGroup <= 0} ap5-admin-hide hide{/if}"> {l s='(without taxes)' mod='pm_advancedpack'}</em><em class="ap5-fixed-pack-price-with-taxes{if $idTaxRulesGroup !== 0 || is_null($idTaxRulesGroup)} ap5-admin-hide hide{/if}"> {l s='(with taxes)' mod='pm_advancedpack'}</em>
						</span>
					</label>
					<div class="col-lg-4">
						<div class="input-group">
							<input min="0" type="text" onchange="this.value = this.value.replace(/,/g, '.');" value="{if isset($packFixedPrice[$idGroup])}{$packFixedPrice[$idGroup]|floatval}{/if}" id="ap5_fixed_pack_price_{$idGroup|intval}" name="ap5_fixed_pack_price[{$idGroup|intval}]" class="ap5_fixed_pack_price" maxlength="14" />
							<span class="input-group-addon"> {$defaultCurrency->sign|escape:'html':'UTF-8'}</span>
						</div>
					</div>
				</div>
				{/foreach}
			</div>

			<div id="ap5_price_rules_4_configuration" class="ap5-admin-hide hide">
				<label class="control-label col-lg-4" for="ap5_allow_remove_product">
					<span class="label-tooltip" data-toggle="tooltip" title="{l s='Check this box if you want your customers to be able to remove a product from the pack. This option can only be enabled if you have more than one product into your pack and if inherit discounts option is enabled on each product.' mod='pm_advancedpack'}">
						{l s='Allow product removal from the pack' mod='pm_advancedpack'}
					</span>
				</label>
				<div class="col-lg-4">
					<div class="input-group">
						<input type="checkbox"{if $packAllowRemoveProduct} checked="checked"{/if} value="1" id="ap5_allow_remove_product" name="ap5_allow_remove_product" />
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-5">
			<h4>{l s='Pack price simulation' mod='pm_advancedpack'}</h4>
			{include file="./admin-product-tab-pack-price-simulation.tpl"}
		</div>
	</div>

	{if Configuration::get('PS_TAX_DISPLAY')}
	<div class="form-group ap5-tax-display-alert ap5-admin-hide hide">
		<p class="alert alert-danger">
			{l s='Your pack contains product with different taxes rate and "Display tax in the shopping cart" option is enabled.' mod='pm_advancedpack'}<br />
			{l s='In order to show consistent information to your customer, we recommand you disable this option.' mod='pm_advancedpack'}<br /><br />
			<a href="{$link->getAdminLink('AdminTaxes')}#PS_TAX_DISPLAY_on" target="_blank"><strong>{l s='Click here to edit taxes configuration' mod='pm_advancedpack'}</strong></a>
		</p>
	</div>
	{/if}

	<div class="form-group ap5-stock-management-alert ap5-admin-hide hide">
		<p class="alert alert-danger">
			{l s='Advanced stock management is enabled on your shop, but your pack contains product with this option disabled.' mod='pm_advancedpack'}<br />
			{l s='In order to avoid carrier issues, we recommand you to enable advanced stock management for all your products, or disable it if you\'re not using it' mod='pm_advancedpack'}<br /><br />
			<a href="{$link->getAdminLink('AdminPPreferences')}#PS_ADVANCED_STOCK_MANAGEMENT_on" target="_blank"><strong>{l s='Click here to edit stock configuration' mod='pm_advancedpack'}</strong></a>
		</p>
	</div>

	<div class="form-group">
		<h4>{l s='Pack content' mod='pm_advancedpack'}</h4>
		<label class="control-label col-lg-3" for="ap5_pack_content_input">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='Select here the products that you want to add to your pack' mod='pm_advancedpack'}">
				{l s='Add a new product to this pack' mod='pm_advancedpack'}
			</span>
		</label>
		<div class="col-lg-5">
			<div id="ajax_choose_product">
				<div class="input-group">
					<input type="text" id="ap5_pack_content_input" name="ap5_pack_content_input" />
					<span class="input-group-addon"><i class="icon-search"></i></span>
				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<table id="ap5-pack-content-table" class="table configuration">
			<thead>
				<tr class="nodrag nodrop">
					<th class="center">{l s='Image' mod='pm_advancedpack'}</th>
					<th class="left">{l s='Name' mod='pm_advancedpack'}</th>
					<th class="left">{l s='Default combination' mod='pm_advancedpack'}</th>
					<th class="center"><span class="label-tooltip" data-toggle="tooltip" title="{l s='If you want to only use specific combinations or customization fields, check this box' mod='pm_advancedpack'}">{l s='Custom' mod='pm_advancedpack'}</span></th>
					<th class="center">{l s='Quantity' mod='pm_advancedpack'}</th>
					<th class="left">{l s='Price' mod='pm_advancedpack'}</th>
					<th class="left ap5_discountCell">{l s='Discount' mod='pm_advancedpack'}</th>
					<th class="center"><span class="label-tooltip" data-toggle="tooltip" title="{l s='If checked, your customers will only be able to buy the product from a pack' mod='pm_advancedpack'}">{l s='Exclusive?' mod='pm_advancedpack'}</span></th>
					<th class="center ap5_useReduc-container"><span class="label-tooltip" data-toggle="tooltip" title="{l s='If you want to use product base price (without discounts), uncheck this box' mod='pm_advancedpack'}">{l s='Inherit discounts?' mod='pm_advancedpack'}</span></th>
					<th class="center">{l s='Remove' mod='pm_advancedpack'}</th>
				</tr>
			</thead>
			<tbody>
				{include file="./admin-product-tab-pack-table.tpl"}
			</tbody>
			<tfoot>
				<tr>
					<td colspan="9" class="left"><em>{l s='* All prices are without taxes' mod='pm_advancedpack'}</em></td>
				</tr>
			</tfoot>
		</table>
	</div>
	<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminProducts')}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel' mod='pm_advancedpack'}</a>
		{if version_compare($smarty.const._PS_VERSION_,'1.6.0.11','>=')}
			<button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save' mod='pm_advancedpack'}</button>
			<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save and stay' mod='pm_advancedpack'}</button>
		{else}
			<button type="submit" name="submitAddproduct" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='pm_advancedpack'}</button>
			<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save and stay' mod='pm_advancedpack'}</button>
		{/if}
	</div>
</div>
<div id="create-pack-invite" class="panel product-tab ps-15 ap5-admin-hide hide">
	<p>{l s='If you want to create a pack, please click on the link below:' mod='pm_advancedpack'}</p>
	<p><a class="btn btn-primary text-center" href="{$link->getAdminLink('AdminProducts')}&addproduct&newpack"><i class="icon-plus-sign"></i><br />{l s='Add a new pack' mod='pm_advancedpack'}</a></p>
</div>
<script>
	ap5_productListUrl = '{$link->getAdminLink('AdminModules')}&configure=pm_advancedpack&adminProductList';
	ap5_updateUrl = '{$link->getAdminLink('AdminModules')}&configure=pm_advancedpack&adminPackContentUpdate';
	ap5_deleteConfirmationMessage = '{l s='Do you really want to delete this product from the pack?' mod='pm_advancedpack' js=1}';
	ap5_packIdTaxRulesGroup = {Tools::jsonEncode($idTaxRulesGroup)};
	ap5_warehouseMessage = '{l s='You can\'t have different warehouse into the pack, please choose another product in order to continue.' mod='pm_advancedpack' js=1}';
	ap5_packIdWarehouse = {Tools::jsonEncode($idWarehouse)};
	ap5_displayMode = {Tools::jsonEncode($packDisplayMode)};
	ap5_atLeastOneCombinationMessage = '{l s='You must have at least one selected combination.' mod='pm_advancedpack' js=1}';
	ap5_atLeastTwoProductMessage = '{l s='Do you really want to create a pack with only one product inside ?' mod='pm_advancedpack' js=1}';
	ap5_atLeastOneProductMessage = '{l s='You must have at least one product into the pack.' mod='pm_advancedpack' js=1}';
	if (typeof(ap5_initNewPackFields) != 'undefined') {
		ap5_setProductTabName('{l s='Pack configuration' mod='pm_advancedpack' js=1}');
		ap5_initNewPackFields();
	} else {
		$('#product-new-pack').addClass('ap5-admin-hide hide');
		$('#create-pack-invite').removeClass('ap5-admin-hide hide');
	}
</script>
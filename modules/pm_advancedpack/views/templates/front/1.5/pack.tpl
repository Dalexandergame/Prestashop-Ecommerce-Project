{include file="$tpl_dir./errors.tpl"}
{if $errors|@count == 0}
<script type="text/javascript">
// <![CDATA[

// PrestaShop internal settings
var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
var currencyRate = '{$currencyRate|floatval}';
var currencyFormat = '{$currencyFormat|intval}';
var currencyBlank = '{$currencyBlank|intval}';
var taxRate = {$tax_rate|floatval};
var jqZoomEnabled = {if $jqZoomEnabled}true{else}false{/if};

//JS Hook
var oosHookJsCodeFunctions = new Array();

// Parameters
var id_product = '{$product->id|intval}';
var productHasAttributes = {if isset($groups)}true{else}false{/if};
var quantitiesDisplayAllowed = {if $display_qties == 1}true{else}false{/if};
var quantityAvailable = {if $display_qties == 1 && $product->quantity}{$product->quantity}{else}0{/if};
var allowBuyWhenOutOfStock = {if $allow_oosp == 1}true{else}false{/if};
var availableNowValue = '{$product->available_now|escape:'quotes':'UTF-8'}';
var availableLaterValue = '{$product->available_later|escape:'quotes':'UTF-8'}';
var productPriceTaxExcluded = {AdvancedPack::getPackPrice($product->id, false)|default:'null'} - {$product->ecotax};
var productBasePriceTaxExcluded = {AdvancedPack::getPackPrice($product->id, false, false)} - {$product->ecotax};

var reduction_percent = {if $product->specificPrice AND $product->specificPrice.reduction AND $product->specificPrice.reduction_type == 'percentage'}{$product->specificPrice.reduction*100}{else}0{/if};
var reduction_price = {if $product->specificPrice AND $product->specificPrice.reduction AND $product->specificPrice.reduction_type == 'amount'}{$product->specificPrice.reduction|floatval}{else}0{/if};
var specific_price = {if $product->specificPrice AND $product->specificPrice.price}{$product->specificPrice.price}{else}0{/if};
var product_specific_price = new Array();
{foreach from=$product->specificPrice key='key_specific_price' item='specific_price_value'}
	product_specific_price['{$key_specific_price}'] = '{$specific_price_value}';
{/foreach}
var specific_currency = {if $product->specificPrice AND $product->specificPrice.id_currency}true{else}false{/if};
var group_reduction = '{$group_reduction}';
var default_eco_tax = {$product->ecotax};
var ecotaxTax_rate = {$ecotaxTax_rate};
var currentDate = '{$smarty.now|date_format:'%Y-%m-%d %H:%M:%S'}';
var maxQuantityToAllowDisplayOfLastQuantityMessage = {$last_qties};
var noTaxForThisProduct = {if $no_tax == 1}true{else}false{/if};
var displayPrice = {$priceDisplay};
var productReference = '{$product->reference|escape:'htmlall':'UTF-8'}';
var productAvailableForOrder = {if (isset($restricted_country_mode) AND $restricted_country_mode) OR $PS_CATALOG_MODE}'0'{else}'{$product->available_for_order}'{/if};
var productShowPrice = '{if !$PS_CATALOG_MODE}{$product->show_price}{else}0{/if}';
var productUnitPriceRatio = '{$product->unit_price_ratio}';
var idDefaultImage = {if isset($cover.id_image_only)}{$cover.id_image_only}{else}0{/if};
var stock_management = {$stock_management|intval};
{if !isset($priceDisplayPrecision)}
	{assign var='priceDisplayPrecision' value=2}
{/if}
{if !$priceDisplay || $priceDisplay == 2}
	{assign var='productPrice' value=AdvancedPack::getPackPrice($product->id, true, true, true, $priceDisplayPrecision, array(), array(), array(), true)}
	{assign var='productPriceWithoutReduction' value=AdvancedPack::getPackPrice($product->id, true, false, true, $priceDisplayPrecision, array(), array(), array(), true)}
{elseif $priceDisplay == 1}
	{assign var='productPrice' value=AdvancedPack::getPackPrice($product->id, false, true, true, $priceDisplayPrecision, array(), array(), array(), true)}
	{assign var='productPriceWithoutReduction' value=AdvancedPack::getPackPrice($product->id, false, false, true, $priceDisplayPrecision, array(), array(), array(), true)}
{/if}


var productPriceWithoutReduction = '{$productPriceWithoutReduction}';
var productPrice = '{$productPrice}';

// Customizable field
var img_ps_dir = '{$img_ps_dir}';
var customizationFields = new Array();
{assign var='imgIndex' value=0}
{assign var='textFieldIndex' value=0}
{foreach from=$customizationFields item='field' name='customizationFields'}
	{assign var="key" value="pictures_`$product->id`_`$field.id_customization_field`"}
	customizationFields[{$smarty.foreach.customizationFields.index|intval}] = new Array();
	customizationFields[{$smarty.foreach.customizationFields.index|intval}][0] = '{if $field.type|intval == 0}img{$imgIndex++}{else}textField{$textFieldIndex++}{/if}';
	customizationFields[{$smarty.foreach.customizationFields.index|intval}][1] = {if $field.type|intval == 0 && isset($pictures.$key) && $pictures.$key}2{else}{$field.required|intval}{/if};
{/foreach}

// Images
var img_prod_dir = '{$img_prod_dir}';
var combinationImages = new Array();

{if isset($combinationImages)}
	{foreach from=$combinationImages item='combination' key='combinationId' name='f_combinationImages'}
		combinationImages[{$combinationId}] = new Array();
		{foreach from=$combination item='image' name='f_combinationImage'}
			combinationImages[{$combinationId}][{$smarty.foreach.f_combinationImage.index}] = {$image.id_image|intval};
		{/foreach}
	{/foreach}
{/if}

combinationImages[0] = new Array();
{if isset($images)}
	{foreach from=$images item='image' name='f_defaultImages'}
		combinationImages[0][{$smarty.foreach.f_defaultImages.index}] = {$image.id_image};
	{/foreach}
{/if}

// Translations
var doesntExist = '{l s='This combination does not exist for this product. Please select another combination.' mod='pm_advancedpack' js=1}';
var doesntExistNoMore = '{l s='This product is no longer in stock' mod='pm_advancedpack' js=1}';
var doesntExistNoMoreBut = '{l s='with those attributes but is available with others.' mod='pm_advancedpack' js=1}';
var uploading_in_progress = '{l s='Uploading in progress, please be patient.' mod='pm_advancedpack' js=1}';
var fieldRequired = '{l s='Please fill in all the required fields before saving your customization.' mod='pm_advancedpack' js=1}';

{if isset($groups)}
	// Combinations
	{foreach from=$combinations key=idCombination item=combination}
		var specific_price_combination = new Array();
		var available_date = new Array();
		specific_price_combination['reduction_percent'] = {if $combination.specific_price AND $combination.specific_price.reduction AND $combination.specific_price.reduction_type == 'percentage'}{$combination.specific_price.reduction*100}{else}0{/if};
		specific_price_combination['reduction_price'] = {if $combination.specific_price AND $combination.specific_price.reduction AND $combination.specific_price.reduction_type == 'amount'}{$combination.specific_price.reduction}{else}0{/if};
		specific_price_combination['price'] = {if $combination.specific_price AND $combination.specific_price.price}{$combination.specific_price.price}{else}0{/if};
		specific_price_combination['reduction_type'] = '{if $combination.specific_price}{$combination.specific_price.reduction_type}{/if}';
		specific_price_combination['id_product_attribute'] = {if $combination.specific_price}{$combination.specific_price.id_product_attribute|intval}{else}0{/if};
		available_date['date'] = '{$combination.available_date}';
		available_date['date_formatted'] = '{dateFormat date=$combination.available_date full=false}';
		addCombination({$idCombination|intval}, new Array({$combination.list}), {$combination.quantity}, {$combination.price}, {$combination.ecotax}, {$combination.id_image}, '{$combination.reference|addslashes}', {$combination.unit_impact}, {$combination.minimal_quantity}, available_date, specific_price_combination);
	{/foreach}
{/if}

{if isset($attributesCombinations)}
	// Combinations attributes informations
	var attributesCombinations = new Array();
	{foreach from=$attributesCombinations key=id item=aC}
		tabInfos = new Array();
		tabInfos['id_attribute'] = '{$aC.id_attribute|intval}';
		tabInfos['attribute'] = '{$aC.attribute}';
		tabInfos['group'] = '{$aC.group}';
		tabInfos['id_attribute_group'] = '{$aC.id_attribute_group|intval}';
		attributesCombinations.push(tabInfos);
	{/foreach}
{/if}
//]]>
</script>

{include file="$tpl_dir./breadcrumb.tpl"}
<div id="primary_block" class="clearfix">

	{if isset($adminActionDisplay) && $adminActionDisplay}
	<div id="admin-action">
		<p>{l s='This product is not visible to your customers.' mod='pm_advancedpack'}
		<input type="hidden" id="admin-action-product-id" value="{$product->id|intval}" />
		<input type="submit" value="{l s='Publish' mod='pm_advancedpack'}" class="exclusive" onclick="submitPublishProduct('{$base_dir}{$smarty.get.ad|escape:'htmlall':'UTF-8'}', 0, '{$smarty.get.adtoken|escape:'htmlall':'UTF-8'}')"/>
		<input type="submit" value="{l s='Back' mod='pm_advancedpack'}" class="exclusive" onclick="submitPublishProduct('{$base_dir}{$smarty.get.ad|escape:'htmlall':'UTF-8'}', 1, '{$smarty.get.adtoken|escape:'htmlall':'UTF-8'}')"/>
		</p>
		<p id="admin-action-result"></p>
		</p>
	</div>
	{/if}

	{if isset($confirmation) && $confirmation}
	<p class="confirmation">
		{$confirmation}
	</p>
	{/if}

	<div class="pb-center-column col-xs-12 col-sm-12 col-md-12">
		<h1 itemprop="name">{$product->name|escape:'html':'UTF-8'}</h1>
		{if $product->description_short}
			<div id="short_description_block">
				<div id="short_description_content" class="rte align_justify" itemprop="description">{$product->description_short}</div>
			</div> {* end short_description_block *}
		{/if}

		{* Product list of the pack *}
		{include file="./pack-product-list.tpl"}

		{* Pack description *}
		{if $packShowProductsFeatures || $packShowProductsShortDescription || $packShowProductsLongDescription || $product->description || $product->customizable}
		<div class="clear" id="more_info_block">
			<ul class="idTabs idTabsShort clearfix" id="more_info_tabs">
				{if $product->description}<li><a href="#idTab0" id="more_info_tab_more_info" class="selected">{l s='Pack description' mod='pm_advancedpack'}</a></li>{/if}
				{if $packShowProductsFeatures || $packShowProductsShortDescription || $packShowProductsLongDescription}
					{foreach from=$productsPackUnique item=productPack name=productPack_list}
					<li><a href="#idTab{$productPack.id_product_pack|intval}">{$productPack.productObj->name|escape:'html':'UTF-8'}</a></li>
					{/foreach}
				{/if}
			</ul>
			<div class="sheets align_justify" id="more_info_sheets">
				{if $product->description}<div class="rte" id="idTab0">{$product->description}</div>{/if}
				{if $packShowProductsFeatures || $packShowProductsShortDescription || $packShowProductsLongDescription || $product->customizable}
					{foreach from=$productsPackUnique item=productPack name=productPack_list}
						<div class="rte" id="idTab{$productPack.id_product_pack|intval}">

							<div class="col-xs-12 col-sm-12 {if (!$packShowProductsLongDescription || !$product->description) && ($packShowProductsFeatures && isset($productPack.features) && $productPack.features)}col-md-6{else}col-md-12{/if}">
								<div class="rte">
									{if $packShowProductsShortDescription && $productPack.productObj->description_short}
										{$productPack.productObj->description_short}
									{/if}
									{if $packShowProductsLongDescription && $productPack.productObj->description}
										{if $packShowProductsShortDescription && $productPack.productObj->description_short}<hr />{/if}
										{$productPack.productObj->description}
									{/if}
								</div>
							</div>

							{if $packShowProductsFeatures && isset($productPack.features) && $productPack.features}
								<!-- Data sheet -->
								<div class="col-xs-12 col-sm-12 {if !$product->description}col-md-6{else}col-md-12{/if}">
									<section class="page-product-box">
										<h3 class="page-product-heading">{l s='Data sheet' mod='pm_advancedpack'}</h3>
										<table class="table-data-sheet">
											{foreach from=$productPack.features item=feature}
												<tr class="{cycle values="odd,even"}">
													{if isset($feature.value)}
														<td>{$feature.name|escape:'html':'UTF-8'}</td>
														<td>{$feature.value|escape:'html':'UTF-8'}</td>
													{/if}
												</tr>
											{/foreach}
										</table>
									</section>
								</div>
								<!--end Data sheet -->
							{/if}

							<!-- Customizable products -->
							{if $productPack.productObj->customizable && sizeof($productPack.customization.customizationFields)}
								<div id="idTab10" class="bullet customization_block">
									<form method="post" action="{pm_advancedpack::getPackUpdateURL($productPack.productObj->id)|escape:'html':'UTF-8'}" enctype="multipart/form-data" id="customizationForm-{$productPack.productObj->id}" class="ap5-customization-form clearfix" data-id-product-pack="{$productPack.id_product_pack|intval}">
										{if $productPack.productObj->text_fields|intval}
										<div class="customizableProductsText">
											<h3>{l s='Text' mod='pm_advancedpack'}</h3>
											<ul id="text_fields">
											{counter start=0 assign='customizationField'}
											{foreach from=$productPack.customization.customizationFields item='field' name='customizationFields'}
												{if $field.type == 1}
												<li class="customizationUploadLine{if $field.required} required{/if}">
													<label for ="textField{$customizationField}">{assign var='key' value='textFields_'|cat:$productPack.productObj->id|cat:'_'|cat:$field.id_customization_field} {if !empty($field.name)}{$field.name}{/if}{if $field.required}<sup>*</sup>{/if}</label>
													<textarea name="textField{$field.id_customization_field}" id="textField{$customizationField}" rows="1" cols="40"{if $field.required} required="required"{/if} data-id-customization-field="{$field.id_customization_field}" class="customization_block_input ap5-customization-block-input">{if isset($productPack.customization.textFields.$key)}{$productPack.customization.textFields.$key|stripslashes}{/if}</textarea>
												</li>
												{counter}
												{/if}
											{/foreach}
											</ul>
										</div>
										{/if}
									</form>
									<p class="clear required"><sup>*</sup> {l s='required fields' mod='pm_advancedpack'}</p>
								</div>
							{/if}
						</div>
					{/foreach}
				{/if}
			</div>
		</div>
		{/if}
	</div>
</div>

{if isset($HOOK_PRODUCT_FOOTER) && $HOOK_PRODUCT_FOOTER}{$HOOK_PRODUCT_FOOTER}{/if}

{/if}
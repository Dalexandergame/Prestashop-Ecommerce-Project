{include file="$tpl_dir./errors.tpl"}
{if $errors|@count == 0}
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
	<div class="primary_block row" itemscope itemtype="http://schema.org/Product">
		{if !$content_only}
			<div class="container">
				<div class="top-hr"></div>
			</div>
		{/if}
		{if isset($adminActionDisplay) && $adminActionDisplay}
			<div id="admin-action">
				<p>{l s='This product is not visible to your customers.' mod='pm_advancedpack'}
					<input type="hidden" id="admin-action-product-id" value="{$product->id|intval}" />
					<input type="submit" value="{l s='Publish' mod='pm_advancedpack'}" name="publish_button" class="exclusive" />
					<input type="submit" value="{l s='Back' mod='pm_advancedpack'}" name="lnk_view" class="exclusive" />
				</p>
				<p id="admin-action-result"></p>
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
		</div>

		{* Product list of the pack *}
		{include file="./pack-product-list.tpl"}

		{* pb-right-column*}
		<div class="pb-right-column col-xs-12 col-sm-4 col-md-3">
		{if ($product->show_price && !isset($restricted_country_mode)) || isset($groups) || $product->reference || (isset($HOOK_PRODUCT_ACTIONS) && $HOOK_PRODUCT_ACTIONS)}
			{* add to cart form*}
			<form id="buy_block" class="ap5-buy-block{if $PS_CATALOG_MODE && !isset($groups) && $product->quantity > 0} hidden{/if}" action="{pm_advancedpack::getPackAddCartURL($product->id)|escape:'html':'UTF-8'}" method="post">
				{* hidden datas *}
				<p class="hidden">
					<input type="hidden" name="token" value="{$static_token|escape:'html':'UTF-8'}" />
					<input type="hidden" name="id_product" value="{$product->id|intval}" id="product_page_product_id" />
					<input type="hidden" name="add" value="1" />
					<input type="hidden" name="id_product_attribute" id="idCombination" value="" />
				</p>
				<div class="box-info-product">
					{include file="./pack-price-container.tpl"}
					
					{* Remove this if you want $HOOK_EXTRA_RIGHT into buy block *}
					{*
					<div class="box-cart-bottom">
						<div id="ap5-hook-product-extra-right-container">
						{if isset($HOOK_EXTRA_RIGHT) && $HOOK_EXTRA_RIGHT}{$HOOK_EXTRA_RIGHT}{/if}
						</div>
					</div> <!-- .end box-cart-bottom -->
					*}
				</div> {* end box-info-product *}
			</form>
		{/if}
		</div>{* end right infos *}
	</div> {* end primary_block *}

	{* Pack hook product footer *}
	{* {if !$content_only && isset($HOOK_PRODUCT_FOOTER) && $HOOK_PRODUCT_FOOTER}
	<div id="ap5-hook-product-footer">
		{$HOOK_PRODUCT_FOOTER}
	</div>
	{/if} *}

	{* Pack description *}
	{if $product->description}
	<div id="ap5-pack-description-block" class="col-xs-12 col-sm-12 col-md-6">
		<section id="ap5-hook-pack-description" class="page-product-box">
			<h3 class="page-product-heading">{l s='Pack description' mod='pm_advancedpack'}</h3>
			<div class="rte">{$product->description}</div>
		</section>
	</div>
	{/if}

	{* Product list into tabs *}
	{if $packShowProductsFeatures || $packShowProductsShortDescription || $packShowProductsLongDescription}
	<div id="ap5-pack-content-block" class="col-xs-12 col-sm-12 {if $product->description}col-md-6{else}col-md-12{/if}">
		<section id="ap5-pack-content-tabs" class="page-product-box">
			<h3 class="page-product-heading">{l s='Pack content' mod='pm_advancedpack'}</h3>
			{include file="./pack-product-list-tabs.tpl"}
		</section>
	</div>
	{/if}

	{* JS Definitions *}
	{strip}
		{if isset($smarty.get.ad) && $smarty.get.ad}
			{addJsDefL name=ad}{$base_dir|cat:$smarty.get.ad|escape:'html':'UTF-8'}{/addJsDefL}
		{/if}
		{if isset($smarty.get.adtoken) && $smarty.get.adtoken}
			{addJsDefL name=adtoken}{$smarty.get.adtoken|escape:'html':'UTF-8'}{/addJsDefL}
		{/if}
		{addJsDef allowBuyWhenOutOfStock=$allow_oosp|boolval}
		{addJsDef availableNowValue=$product->available_now|escape:'quotes':'UTF-8'}
		{addJsDef availableLaterValue=$product->available_later|escape:'quotes':'UTF-8'}
		{addJsDef attribute_anchor_separator=$attribute_anchor_separator|escape:'quotes':'UTF-8'}
		{addJsDef attributesCombinations=$attributesCombinations}
		{addJsDef currencySign=$currencySign|html_entity_decode:2:"UTF-8"}
		{addJsDef currencyRate=$currencyRate|floatval}
		{addJsDef currencyFormat=$currencyFormat|intval}
		{addJsDef currencyBlank=$currencyBlank|intval}
		{addJsDef currentDate=$smarty.now|date_format:'%Y-%m-%d %H:%M:%S'}
		{if isset($combinations) && $combinations}
			{addJsDef combinations=$combinations}
			{addJsDef combinationsFromController=$combinations}
			{addJsDef displayDiscountPrice=$display_discount_price}
			{addJsDefL name='upToTxt'}{l s='Up to' mod='pm_advancedpack' js=1}{/addJsDefL}
		{/if}
		{if isset($combinationImages) && $combinationImages}
			{addJsDef combinationImages=$combinationImages}
		{/if}
		{addJsDef customizationFields=$customizationFields}
		{addJsDef default_eco_tax=$product->ecotax|floatval}
		{addJsDef displayPrice=$priceDisplay|intval}
		{addJsDef ecotaxTax_rate=$ecotaxTax_rate|floatval}
		{addJsDef group_reduction=$group_reduction}
		{if isset($cover.id_image_only)}
			{addJsDef idDefaultImage=$cover.id_image_only|intval}
		{else}
			{addJsDef idDefaultImage=0}
		{/if}
		{addJsDef img_ps_dir=$img_ps_dir}
		{addJsDef img_prod_dir=$img_prod_dir}
		{addJsDef id_product=$product->id|intval}
		{addJsDef jqZoomEnabled=$jqZoomEnabled|boolval}
		{addJsDef maxQuantityToAllowDisplayOfLastQuantityMessage=$last_qties|intval}
		{addJsDef minimalQuantity=$product->minimal_quantity|intval}
		{addJsDef noTaxForThisProduct=$no_tax|boolval}
		{addJsDef customerGroupWithoutTax=$customer_group_without_tax|boolval}
		{addJsDef oosHookJsCodeFunctions=Array()}
		{addJsDef productHasAttributes=isset($groups)|boolval}
		{addJsDef productPriceTaxExcluded=(AdvancedPack::getPackPrice($product->id, false)|default:'null' - $product->ecotax)|floatval}
		{addJsDef productBasePriceTaxExcluded=(AdvancedPack::getPackPrice($product->id, false, false) - $product->ecotax)|floatval}
		{addJsDef productBasePriceTaxExcl=($product->base_price|floatval)}
		{addJsDef productReference=$product->reference|escape:'html':'UTF-8'}
		{addJsDef productAvailableForOrder=$product->available_for_order|boolval}
		{addJsDef productPriceWithoutReduction=$productPriceWithoutReduction|floatval}
		{addJsDef productPrice=$productPrice|floatval}
		{addJsDef productUnitPriceRatio=$product->unit_price_ratio|floatval}
		{addJsDef productShowPrice=(!$PS_CATALOG_MODE && $product->show_price)|boolval}
		{addJsDef PS_CATALOG_MODE=$PS_CATALOG_MODE}
		{if $product->specificPrice && $product->specificPrice|@count}
			{addJsDef product_specific_price=$product->specificPrice}
		{else}
			{addJsDef product_specific_price=array()}
		{/if}
		{if $display_qties == 1 && $product->quantity}
			{addJsDef quantityAvailable=$product->quantity}
		{else}
			{addJsDef quantityAvailable=0}
		{/if}
		{addJsDef quantitiesDisplayAllowed=$display_qties|boolval}
		{if $product->specificPrice && $product->specificPrice.reduction && $product->specificPrice.reduction_type == 'percentage'}
			{addJsDef reduction_percent=$product->specificPrice.reduction*100|floatval}
		{else}
			{addJsDef reduction_percent=0}
		{/if}
		{if $product->specificPrice && $product->specificPrice.reduction && $product->specificPrice.reduction_type == 'amount'}
			{addJsDef reduction_price=$product->specificPrice.reduction|floatval}
		{else}
			{addJsDef reduction_price=0}
		{/if}
		{if $product->specificPrice && $product->specificPrice.price}
			{addJsDef specific_price=$product->specificPrice.price|floatval}
		{else}
			{addJsDef specific_price=0}
		{/if}
		{addJsDef specific_currency=($product->specificPrice && $product->specificPrice.id_currency)|boolval}
		{addJsDef stock_management=$stock_management|intval}
		{addJsDef taxRate=$tax_rate|floatval}
		{addJsDefL name=doesntExist}{l s='This combination does not exist for this product. Please select another combination.' mod='pm_advancedpack' js=1}{/addJsDefL}
		{addJsDefL name=doesntExistNoMore}{l s='This product is no longer in stock' mod='pm_advancedpack' js=1}{/addJsDefL}
		{addJsDefL name=doesntExistNoMoreBut}{l s='with those attributes but is available with others.' mod='pm_advancedpack' js=1}{/addJsDefL}
		{addJsDefL name=fieldRequired}{l s='Please fill in all the required fields before saving your customization.' mod='pm_advancedpack' js=1}{/addJsDefL}
		{addJsDefL name=uploading_in_progress}{l s='Uploading in progress, please be patient.' mod='pm_advancedpack' js=1}{/addJsDefL}
		{addJsDefL name='product_fileDefaultHtml'}{l s='No file selected' mod='pm_advancedpack' js=1}{/addJsDefL}
		{addJsDefL name='product_fileButtonHtml'}{l s='Choose File' mod='pm_advancedpack' js=1}{/addJsDefL}
	{/strip}
	{* End JS Definitions *}
{/if}
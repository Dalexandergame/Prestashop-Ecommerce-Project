{if !$priceDisplay || $priceDisplay == 2}
	{assign var='productPrice' value=AdvancedPack::getPackPrice($product->id, true, true, true, $priceDisplayPrecision, $packAttributesList, $packQuantityList, $packExcludeList, true)}
	{assign var='productPriceWithoutReduction' value=AdvancedPack::getPackPrice($product->id, true, false, true, $priceDisplayPrecision, $packAttributesList, $packQuantityList, $packExcludeList, true)}
{elseif $priceDisplay == 1}
	{assign var='productPrice' value=AdvancedPack::getPackPrice($product->id, false, true, true, $priceDisplayPrecision, $packAttributesList, $packQuantityList, $packExcludeList, true)}
	{assign var='productPriceWithoutReduction' value=AdvancedPack::getPackPrice($product->id, false, false, true, $priceDisplayPrecision, $packAttributesList, $packQuantityList, $packExcludeList, true)}
{/if}

<div id="ap5-buy-block-container" class="block">
	<p class="title_block">{l s='Buy this pack' mod='pm_advancedpack'}</p>
	{if ($product->show_price AND !isset($restricted_country_mode)) OR isset($groups) OR $product->reference OR (isset($HOOK_PRODUCT_ACTIONS) && $HOOK_PRODUCT_ACTIONS)}
	<!-- add to cart form-->
	<form id="buy_block" class="ap5-buy-block" {if $PS_CATALOG_MODE AND !isset($groups) AND $product->quantity > 0}class="hidden"{/if} action="{pm_advancedpack::getPackAddCartURL($product->id)|escape:'html':'UTF-8'}" method="post">

		<!-- hidden datas -->
		<p class="hidden">
			<input type="hidden" name="token" value="{$static_token|escape:'html':'UTF-8'}" />
			<input type="hidden" name="id_product" value="{$product->id|intval}" id="product_page_product_id" />
			<input type="hidden" name="add" value="1" />
			<input type="hidden" name="id_product_attribute" id="idCombination" value="" />
		</p>

		<p id="product_reference" {if isset($groups) OR !$product->reference}style="display: none;"{/if}>
			<label>{l s='Reference:' mod='pm_advancedpack'} </label>
			<span class="editable">{$product->reference|escape:'htmlall':'UTF-8'}</span>
		</p>

		<div class="content_prices clearfix">
			<!-- prices -->
			{if $product->show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}

			{if $product->online_only}
			<p class="online_only">{l s='Online only' mod='pm_advancedpack'}</p>
			{/if}

			<div class="price">
				<p class="our_price_display">
				{if $priceDisplay >= 0 && $priceDisplay <= 2}
					<span id="our_price_display">{convertPrice price=$productPrice}</span>
					{* {if $tax_enabled  && ((isset($display_tax_label) && $display_tax_label == 1) OR !isset($display_tax_label))}
						{if $priceDisplay == 1}{l s='tax excl.' mod='pm_advancedpack'}{else}{l s='tax incl.' mod='pm_advancedpack'}{/if}
					{/if} *}
				{/if}
				</p>

				{if $productPriceWithoutReduction > $productPrice}
					<span class="discount">{l s='Reduced price!' mod='pm_advancedpack'}</span>
				{/if}
				{if $priceDisplay == 2}
					<br />
					<span id="pretaxe_price"><span id="pretaxe_price_display">{convertPrice price=$product->getPrice(false, $smarty.const.NULL)}</span>&nbsp;{l s='tax excl.' mod='pm_advancedpack'}</span>
				{/if}
			</div>

			{if $product->specificPrice.reduction != 0}
				<p id="reduction_percent" {if !$product->specificPrice OR $product->specificPrice.reduction_type != 'percentage'} style="display:none;"{/if}><span id="reduction_percent_display">{if $product->specificPrice AND $product->specificPrice.reduction_type == 'percentage'}-{$product->specificPrice.reduction*100}%{/if}</span></p>
				<p id="reduction_amount" {if !$product->specificPrice OR $product->specificPrice.reduction_type != 'amount' || $product->specificPrice.reduction|intval ==0} style="display:none"{/if}>
					<span id="reduction_amount_display">
					{if $product->specificPrice AND $product->specificPrice.reduction_type == 'amount' AND $product->specificPrice.reduction|intval !=0}
						-{convertPrice price=$productPriceWithoutReduction-$productPrice|floatval}
					{/if}
					</span>
				</p>
			{/if}
			
			<p id="old_price">
			{if $priceDisplay >= 0 && $priceDisplay <= 2}
					<span id="old_price_display">{if $productPriceWithoutReduction > $productPrice}{convertPrice price=$productPriceWithoutReduction}{/if}</span>
					{* {if $tax_enabled && $display_tax_label == 1}{if $priceDisplay == 1}{l s='tax excl.' mod='pm_advancedpack'}{else}{l s='tax incl.' mod='pm_advancedpack'}{/if}{/if} *}
			{/if}
			</p>
			{if $product->ecotax != 0}
				<p class="price-ecotax">{l s='Include' mod='pm_advancedpack'} <span id="ecotax_price_display">{if $priceDisplay == 2}{$ecotax_tax_exc|convertAndFormatPrice}{else}{$ecotax_tax_inc|convertAndFormatPrice}{/if}</span> {l s='For green tax' mod='pm_advancedpack'}
					{if $product->specificPrice AND $product->specificPrice.reduction}
					<br />{l s='(not impacted by the discount)' mod='pm_advancedpack'}
					{/if}
				</p>
			{/if}
			{if !empty($product->unity) && $product->unit_price_ratio > 0.000000}
				 {math equation="pprice / punit_price"  pprice=$productPrice  punit_price=$product->unit_price_ratio assign=unit_price}
				<p class="unit-price"><span id="unit_price_display">{convertPrice price=$unit_price}</span> {l s='per' mod='pm_advancedpack'} {$product->unity|escape:'htmlall':'UTF-8'}</p>
			{/if}
			{*close if for show price*}
			{/if}

			<!-- buy action and errors message -->
			<div id="ap5-buy-container" {if (!$allow_oosp && $product->quantity <= 0) || !$product->available_for_order || (isset($restricted_country_mode) && $restricted_country_mode) || $PS_CATALOG_MODE} class="unvisible"{/if}>
			{if isset($productsPackFatalErrors) && count($productsPackFatalErrors)}
				<p class="ap5-pack-unavailable animated shake alert alert-danger">
					<span>{l s='One of product is no longer available. This pack can\t be purchased' mod='pm_advancedpack'}</span>
				</p>
			{else if isset($productsPackErrors) && count($productsPackErrors)}
				<p class="ap5-combination-unavailable animated flash alert alert-warning">
					<span><a href="#ap5-pack-product-{current(array_keys($productsPackErrors))}">{l s='One of product combination is no longer available. Please select another attribute to this product' mod='pm_advancedpack'}</a></span>
				</p>
			{else}
				<!-- quantity wanted -->
				<p id="quantity_wanted_p">
					<label>{l s='Quantity:' mod='pm_advancedpack'}&nbsp;</label>
					<input type="text" name="qty" id="quantity_wanted" class="text" value="1" size="2" maxlength="3" />
				</p>
				<p id="add_to_cart_pack" {if (!$allow_oosp && $product->quantity <= 0) OR !$product->available_for_order OR (isset($restricted_country_mode) AND $restricted_country_mode) OR $PS_CATALOG_MODE}style="display:none"{/if} class="buttons_bottom_block">
					<input type="submit" name="Submit" value="{l s='Add this pack' mod='pm_advancedpack'}" class="exclusive" />
				</p>
			{/if}
			</div>
			{* Remove this if you want $HOOK_EXTRA_RIGHT into buy block *}
			{*
			<div id="ap5-hook-product-extra-right-container">
			{if isset($HOOK_EXTRA_RIGHT) && $HOOK_EXTRA_RIGHT}{$HOOK_EXTRA_RIGHT}{/if}
			</div>
			*}
			<div class="clear"></div>
		</div>
	</form>
	{/if}
</div>
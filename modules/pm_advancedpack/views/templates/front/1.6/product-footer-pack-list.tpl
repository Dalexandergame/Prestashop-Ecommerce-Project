<!-- pack list-->
<section id="ap5-page-product-box" class="page-product-box">
	<h3 class="page-product-heading" id="#ap5-product-footer-pack-list">{l s='This product is also available in pack' mod='pm_advancedpack'}</h3>
	<div id="ap5-product-footer-pack-list">
	{foreach from=$packList item=productsPack key=idPack}
		{assign var='idPack' value=$productsPack.idPack}
		{if !$priceDisplay || $priceDisplay == 2}
			{assign var='productPrice' value=AdvancedPack::getPackPrice($idPack, true, true, true, 6, array(), array(), array(), true)}
			{assign var='productPriceWithoutReduction' value=AdvancedPack::getPackPrice($idPack, true, false, true, 6, array(), array(), array(), true)}
		{elseif $priceDisplay == 1}
			{assign var='productPrice' value=AdvancedPack::getPackPrice($idPack, false, true, true, 6, array(), array(), array(), true)}
			{assign var='productPriceWithoutReduction' value=AdvancedPack::getPackPrice($idPack, false, false, true, 6, array(), array(), array(), true)}
		{/if}
		<div id="ap5-product-footer-pack-container-{$idPack|intval}" class="ap5-product-footer-pack-container">
			<div id="ap5-product-footer-pack-informations-{$idPack|intval}" class="ap5-product-footer-pack-informations clearfix">
				<h5 class="ap5-product-footer-pack-name product-name">
					<a href="{$link->getProductLink($productsPack.packObj)|escape:'html':'UTF-8'}" title="{$productsPack.packObj->name|escape:'html':'UTF-8'}" itemprop="url">
						<span class="ap5-view-pack-name">{$productsPack.packObj->name|escape:'html':'UTF-8'}</span><span class="ap5-view-pack-category"> ({$productsPack.packObj->category|escape:'html':'UTF-8'})</span>
						{if $enableViewThisPackButton}<span class="ap5-view-pack-button">{l s='View this pack' mod='pm_advancedpack'}</span>{/if}
					</a>
					{if $enableBuyThisPackButton && AdvancedPack::isValidPack($idPack, true) && AdvancedPack::isInStock($idPack) && !sizeof(AdvancedPack::getPackCustomizationRequiredFields($idPack))}
					<a class="ajax_add_to_cart_button" rel="ajax_id_product_{$idPack|intval}" data-id-product="{$idPack|intval}" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$idPack|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}" title="{l s='Buy this pack' mod='pm_advancedpack'}" itemprop="url">
						<span class="ap5-buy-pack-button">{l s='Buy this pack' mod='pm_advancedpack'}</span>
					</a>
					{/if}
				</h5>
				{if $productsPack.packObj->show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}
				<div id="ap5-price-container-{$idPack|intval}" class="ap5-price-container content_prices">
					<div class="price">
						<p class="our_price_display" {if $priceDisplay >= 0 && $priceDisplay <= 2}itemprop="offers" itemscope itemtype="https://schema.org/Offer"{/if}>
							{if $priceDisplay >= 0 && $priceDisplay <= 2}
								<meta itemprop="priceCurrency" content="{$currency->iso_code|escape:'html':'UTF-8'}" />
								<span id="our_price_display" itemprop="price" content="{$productPrice}">{convertPrice price=$productPrice}</span>
								{* {if $tax_enabled  && ((isset($display_tax_label) && $display_tax_label == 1) || !isset($display_tax_label))}
									{if $priceDisplay == 1}{l s='tax excl.' mod='pm_advancedpack'}{else}{l s='tax incl.' mod='pm_advancedpack'}{/if}
								{/if} *}
							{/if}
						</p>
						{if $productsPack.packObj->specificPrice.reduction != 0}
							<p id="reduction_percent" {if !$productsPack.packObj->specificPrice || $productsPack.packObj->specificPrice.reduction_type != 'percentage'} style="display:none;"{/if}>
								<span id="reduction_percent_display">
									{if $productsPack.packObj->specificPrice && $productsPack.packObj->specificPrice.reduction_type == 'percentage'}-{$productsPack.packObj->specificPrice.reduction*100}%{/if}
								</span>
							</p>
							<p id="reduction_amount" {if !$productsPack.packObj->specificPrice || $productsPack.packObj->specificPrice.reduction_type != 'amount' || $productsPack.packObj->specificPrice.reduction|floatval ==0} style="display:none"{/if}>
								<span id="reduction_amount_display">
								{if $productsPack.packObj->specificPrice && $productsPack.packObj->specificPrice.reduction_type == 'amount' && $productsPack.packObj->specificPrice.reduction|intval !=0}
									-{convertPrice price=$productPriceWithoutReduction-$productPrice|floatval}
								{/if}
								</span>
							</p>
							<p id="old_price"{if (!$productsPack.packObj->specificPrice || !$productsPack.packObj->specificPrice.reduction) && $group_reduction == 1} class="hidden"{/if}>
								{if $priceDisplay >= 0 && $priceDisplay <= 2}
									<span class="ap5-old-price-display">{if $productPriceWithoutReduction > $productPrice}{convertPrice price=$productPriceWithoutReduction}{/if}</span>
									{* {if $tax_enabled && $display_tax_label == 1}{if $priceDisplay == 1}{l s='tax excl.' mod='pm_advancedpack'}{else}{l s='tax incl.' mod='pm_advancedpack'}{/if}{/if} *}
								{/if}
							</p>
						{/if}
						{if $priceDisplay == 2}
							<br />
							<span id="pretaxe_price">
								<span id="pretaxe_price_display">{convertPrice price=$productsPack.packObj->getPrice(false, $smarty.const.NULL)}</span>
								{l s='tax excl.' mod='pm_advancedpack'}
							</span>
						{/if}
					</div>
				</div>
				{/if}
			</div>

			<div id="ap5-product-footer-pack-{$idPack|intval}" class="ap5-product-footer-pack pm-ap-owl-carousel">
			{foreach from=$productsPack.packContent item=productPack}
				{assign var=imageIds value="`$productPack.id_product`-`$productPack.image.id_image`"}
				{if !empty($productPack.image.legend)}
					{assign var=imageTitle value=$productPack.image.legend|escape:'html':'UTF-8'}
				{else}
					{assign var=imageTitle value=$productPack.productObj->name|escape:'html':'UTF-8'}
				{/if}
				<div id="ap5-pack-product-{$productPack.id_product_pack}" class="ap5-pack-product">
					<div class="ap5-pack-product-content">

						<!-- quantity -->
						{if $productPack.quantity > 1}
						<div class="ribbon-wrapper">
							<div class="ap5-pack-product-quantity ribbon">
								x {$productPack.quantity|intval}
							</div>
						</div>
						{/if}

						<p class="ap5-pack-product-name {if $productPack.quantity > 1}title-left{else}title-center{/if}">
							<a target="_blank" href="{$link->getProductLink($productPack.productObj, null, null, null, null, null, $productPack.id_product_attribute, Configuration::get('PS_REWRITING_SETTINGS'), false, true)|escape:'html':'UTF-8'}" title="{$productPack.productObj->name|escape:'html':'UTF-8'}" itemprop="url">
								{$productPack.productObj->name|escape:'html':'UTF-8'}
							</a>
						</p>

						<div class="ap5-pack-product-image">
							<a class="no-print fancybox" rel="ap5_packImages-{$idPack|intval}" title="{$imageTitle}" href="{$link->getImageLink($productPack.productObj->link_rewrite, $imageIds, $imageFormatProductZoom)|escape:'html':'UTF-8'}">
								<img class="img-responsive" id="thumb_{$productPack.image.id_image}" src="{$link->getImageLink($productPack.productObj->link_rewrite, $imageIds, $imageFormatProductFooterCover)|escape:'html':'UTF-8'}" alt="{$imageTitle}" title="{$imageTitle}" height="{$imageFormatProductFooterCoverHeight}" width="{$imageFormatProductFooterCoverWidth}" itemprop="image" />
							</a>
						</div>
						<hr class="ap5-pack-product-icon-plus" />

						{if $packShowProductsPrice && $productPack.productObj->show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}
						<div class="ap5-pack-product-price-table-container{if $productPack.reduction_amount <= 0} ap5-no-reduction{/if}">
							<div class="ap5-pack-product-price-table-cell">
								<p class="ap5-pack-product-price text-center our_price_display">
									{if $productPack.productObj->show_price}
										{if $productPack.productPackPrice == 0}
											{l s='Free' mod='pm_advancedpack'}
										{else}
											{if !$priceDisplay || $priceDisplay == 2}
												{convertPrice price=($productPack.productPackPrice * $productPack.quantity)}
											{elseif $priceDisplay == 1}
												{convertPrice price=($productPack.productPackPriceTaxExcl * $productPack.quantity)}
											{/if}
										{/if}
									{/if}
								</p>
								{if $productPack.reduction_amount > 0}
								<div class="ap5-old-price-container text-center">
									{if $productPack.reduction_type == 'amount'}
									<p id="reduction_amount" class="ap5-pack-product-amount-container">
										<span id="reduction_amount_display" class="ap5-pack-product-reduction-value">
										-{convertPrice price=($productPack.reduction_amount_tax_incl * $productPack.quantity)}
										</span>
									</p>
									{else}
									<p id="reduction_percent" class="ap5-pack-product-reduction-container">
										<span id="reduction_percent_display" class="ap5-pack-product-reduction-value">
										-{$productPack.reduction_amount * 100}%
										</span>
									</p>
									{/if}
									<p id="old_price" class="ap5-pack-product-original-price text-center">
										<span class="ap5-pack-product-original-price-value ap5-old-price-display">
										{if !$priceDisplay || $priceDisplay == 2}
											{convertPrice price=($productPack.productClassicPrice * $productPack.quantity)}
										{elseif $priceDisplay == 1}
											{convertPrice price=($productPack.productClassicPriceTaxExcl * $productPack.quantity)}
										{/if}
										</span>
									</p>
								</div>
								{/if}
							</div>
						</div>
						{/if}
					</div>
				</div>
			{/foreach}
			</div>
		</div>
	{/foreach}
	</div>
</section>
<!-- end pack list -->

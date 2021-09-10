<!-- pack list-->
<section id="ap5-page-product-box" class="card m-t-2">
	<div class="card-header" id="#ap5-product-footer-pack-list"><h3>{l s='This product is also available in pack' mod='pm_advancedpack'}</h3></div>
	<div class="card-block" id="ap5-product-footer-pack-list">
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
					<a href="{$productsPack.presentation.url}" title="{$productsPack.presentation.name}" itemprop="url">
						<span class="ap5-view-pack-name">{$productsPack.presentation.name}</span><span class="ap5-view-pack-category"> ({$productsPack.presentation.category})</span>
						{if $enableViewThisPackButton}
							<span class="btn btn-secondary m-l-1"><i class="material-icons">&#xE8B6;</i>{l s='View this pack' mod='pm_advancedpack'}</span>
						{/if}
						{if $enableBuyThisPackButton && AdvancedPack::isValidPack($idPack, true) && AdvancedPack::isInStock($idPack) && !sizeof(AdvancedPack::getPackCustomizationRequiredFields($idPack))}
						<form action="{pm_advancedpack::getPackAddCartURL($idPack)}" class="m-l-1 d-inline-block" method="post" id="add-to-cart-or-refresh">
							<input type="hidden" name="token" value="{$static_token}">
							<input type="hidden" name="id_product" value="{$idPack|intval}">
							<input type="hidden" name="id_product_attribute_list" value="">
							<input type="hidden" name="qty" value="1">
							<button
							class="btn btn-primary add-to-cart"
							data-button-action="add-pack-to-cart"
							type="submit"
							><i class="material-icons shopping-cart">&#xE547;</i>{l s='Buy this pack' mod='pm_advancedpack'}</button>
						</form>
						{/if}
					</a>
				</h5>
				{if $productsPack.presentation.show_price && !$configuration.is_catalog}
				<div id="ap5-price-container-{$idPack|intval}" class="ap5-price-container content_prices">
					<div class="price">
						{if $productsPack.presentation.has_discount}
							<div class="product-discount ap5-pack-product-original-price d-inline-block">
								<span class="regular-price ap5-pack-product-original-price-value">{$productsPack.presentation.regular_price}</span>
							</div>
						{/if}
						<div class="product-price h5{if $productsPack.presentation.has_discount} has-discount{/if}" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
							<meta itemprop="priceCurrency" content="{$currency.iso_code}" />
							<span itemprop="price" content="{$productsPack.presentation.price_amount}">{$productsPack.presentation.price}</span>
							{if $productsPack.presentation.has_discount}
								{if $productsPack.presentation.discount_type === 'percentage'}
									<span class="discount discount-percentage ap5-pack-product-reduction-value">{l s='Save %percentage%' d='Shop.Theme.Catalog' sprintf=['%percentage%' => $productsPack.presentation.discount_percentage_absolute]}</span>
								{else}
									<span class="discount discount-amount ap5-pack-product-reduction-value">
										{l s='Save %amount%' d='Shop.Theme.Catalog' sprintf=['%amount%' => $productsPack.presentation.discount_to_display]}
									</span>
								{/if}
							{/if}
						</div>
						{if $priceDisplay == 2}
							<p class="product-without-taxes">{l s='%price% tax excl.' d='Shop.Theme.Catalog' sprintf=['%price%' => $productsPack.presentation.price_tax_exc]}</p>
						{/if}
					</div>
				</div>
				{/if}
			</div>
			<hr class="m-t-1 m-b-0" />

			<div id="ap5-product-footer-pack-{$idPack|intval}" class="ap5-product-footer-pack pm-ap-owl-carousel">
			{foreach from=$productsPack.packContent item=productPack}
				{assign var=imageIds value="`$productPack.id_product`-`$productPack.image.id_image`"}
				{assign var=imageRewrite value=$productPack.presentation.link_rewrite}
				{if empty($imageRewrite)}
					{assign var=imageRewrite value=$productPack.id_product}
				{/if}
				{if !empty($productPack.image.legend)}
					{assign var=imageTitle value=$productPack.image.legend}
				{else}
					{assign var=imageTitle value=$productPack.presentation.name}
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
							<a target="_blank" href="{$productPack.presentation.url}" title="{$productPack.presentation.name}" itemprop="url">
								{$productPack.productObj->name|escape:'html':'UTF-8'}
							</a>
						</p>

						<div class="ap5-pack-product-image">
							<a class="no-print" title="{$imageTitle}" href="{$productPack.presentation.url}" target="_blank">
								<img class="img-fluid d-block mx-auto" id="thumb_{$productPack.image.id_image|intval}" src="{$link->getImageLink($imageRewrite, $imageIds, $imageFormatProductCover)}" alt="{$imageTitle}" title="{$imageTitle}" height="{$imageFormatProductCoverHeight}" width="{$imageFormatProductCoverWidth}" itemprop="image" />
							</a>
						</div>
						<hr class="ap5-pack-product-icon-plus" />

						{if $packShowProductsPrice && $productPack.presentation.show_price && !$configuration.is_catalog}
						<div class="ap5-pack-product-price-table-container product-prices{if $productPack.reduction_amount <= 0} ap5-no-reduction{/if}">
							<div class="current-price ap5-pack-product-price text-center">
								{if $productPack.reduction_amount > 0}
									<div class="product-discount ap5-pack-product-original-price text-center d-inline-block">
										<span class="regular-price ap5-pack-product-original-price-value m-r-0">
										{if !$priceDisplay || $priceDisplay == 2}
											{$productPack.presentation.productClassicPriceTotal}
										{elseif $priceDisplay == 1}
											{$productPack.presentation.productClassicPriceTaxExclTotal}
										{/if}
										</span>
									</div>
								{/if}
								<div class="product-price h5 has-discount d-inline-block" {if !$priceDisplay || $priceDisplay == 1 || $priceDisplay == 2}itemprop="offers" itemscope itemtype="https://schema.org/Offer"{/if}>
									{if $productPack.presentation.show_price}
										{if $productPack.productPackPrice == 0}
											{l s='Gift' d='Shop.Theme.Checkout'}
										{else}
											{if !$priceDisplay || $priceDisplay == 2}
												<meta itemprop="priceCurrency" content="{$currency.iso_code}" />
												<span itemprop="price" content="{$productPack.productPackPriceTotal|floatval}">{$productPack.presentation.productPackPriceTotal}</span>
											{elseif $priceDisplay == 1}
												<meta itemprop="priceCurrency" content="{$currency.iso_code}" />
												<span itemprop="price" content="{$productPack.productPackPriceTaxExclTotal|floatval}">{$productPack.presentation.productPackPriceTaxExclTotal}</span>
											{/if}
										{/if}
									{/if}
									{*
									{if $productPack.reduction_amount > 0}
										{if $productPack.productPackPrice > 0}
											{if $productPack.reduction_type == 'amount'}
												<span class="discount discount-amount ap5-pack-product-reduction-value">
													{l s='Save %amount%' d='Shop.Theme.Catalog' sprintf=['%amount%' => $productPack.productReductionAmountTotal]}
												</span>
											{else}
												<span class="discount discount-percentage ap5-pack-product-reduction-value">{l s='Save %percentage%' d='Shop.Theme.Catalog' sprintf=['%percentage%' => $productPack.reduction_amount * 100|cat:'%']}</span>
											{/if}
										{/if}
									{/if}
									*}
								</div>
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

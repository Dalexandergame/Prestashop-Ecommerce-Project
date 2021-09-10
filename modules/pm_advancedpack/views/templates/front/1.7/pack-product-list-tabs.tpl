{* Grid calculation *}
{assign var=nbTabs value=count($productsPackUnique)}
{if $product.description}
	{if $nbTabs % 2 == 0}{assign var=tabGridMd value="col-xs-12 col-12 col-sm-12 col-md-6"}{else}{assign var=tabGridMd value="col-xs-12 col-12 col-sm-12 col-md-4"}{/if}
{else}
	{if $nbTabs == 2}
		{assign var=tabGridMd value="col-xs-12 col-12 col-sm-12 col-md-6"}
	{else if $nbTabs is div by 3}
		{assign var=tabGridMd value="col-xs-12 col-12 col-sm-12 col-md-4"}
	{else if $nbTabs is div by 2}
		{assign var=tabGridMd value="col-xs-12 col-12 col-sm-12 col-md-3"}
	{else}
		{assign var=tabGridMd value="col-xs-12 col-12 col-sm-12 col-md-2"}
	{/if}
{/if}

<div class="tabs">
	<ul id="ap5-pack-product-tab-list" class="nav nav-tabs">
		{foreach from=$productsPackUnique item=productPack name=productPack_list}
			<li class="nav-item">
				<a href="#pack-product-tab-{$productPack.id_product_pack|intval}" data-toggle="tab" class="nav-link{if $smarty.foreach.productPack_list.first} active{/if}">
					<span class="ap5-pack-product-tab-name">{$productPack.presentation.name}</span>
					{if isset($productPack.gsrAverage) && !empty($productPack.gsrAverage)}
					<!-- Average rating from gsnippetsreviews -->
					<div id="productRating-{$productPack.presentation.id_product|intval}" class="ap5-gsnippetsreviews-average-container">{$productPack.gsrAverage nofilter}</div>
					{/if}
				</a>
			</li>
		{/foreach}
	</ul>

	<div id="ap5-pack-product-tabs-content" class="tab-content clearfix">
		{foreach from=$productsPackUnique item=productPack name=productPack_list}
			<div class="tab-pane {if $smarty.foreach.productPack_list.first} active{/if}" id="pack-product-tab-{$productPack.id_product_pack|intval}">

				{block name='ap5_products_description'}
					{if $packShowProductsShortDescription || $packShowProductsLongDescription}
						<div class="col-xs-12 col-12 col-sm-12 {if (!$packShowProductsLongDescription || !$product.description) && ($packShowProductsFeatures && isset($productPack.features) && $productPack.features)}col-md-6{else}col-md-12{/if}">
							<div class="rte">
								{if $packShowProductsShortDescription && $productPack.presentation.description_short}
									{$productPack.presentation.description_short nofilter}
								{/if}
								{if $packShowProductsLongDescription && $productPack.presentation.description}
									{if $packShowProductsShortDescription && $productPack.presentation.description_short}<hr />{/if}
									{$productPack.presentation.description nofilter}
								{/if}
							</div>
						</div>
					{/if}
				{/block}

				{block name='ap5_products_features'}
					{if $packShowProductsFeatures && isset($productPack.features) && $productPack.features}
						<!-- Data sheet -->
						<div class="col-xs-12 col-12 col-sm-12 {if !$product.description}col-md-6{else}col-md-12{/if}">
							<section class="product-features">
								<h3 class="h6">{l s='Data sheet' d='Shop.Theme.Catalog'}</h3>
								<dl class="data-sheet">
								{foreach from=$productPack.features item=feature}
									<dt class="name">{$feature.name}</dt>
									<dd class="value">{$feature.value}</dd>
								{/foreach}
								</dl>
							</section>
						</div>
						<!--end Data sheet -->
					{/if}
				{/block}

				{block name='ap5_products_reviews'}
					{if isset($productPack.gsrReviewsList) && !empty($productPack.gsrReviewsList)}
					<!-- Reviews from gsnippetsreviews -->
					<div class="col-xs-12 col-12 col-sm-12 col-md-12 clear ap5-gsnippetsreviews-reviews-container">{$productPack.gsrReviewsList nofilter}</div>
					{/if}
				{/block}

				{block name='ap5_products_customization'}
					{if $productPack.presentation.customizable && sizeof($productPack.customization.customizationFields)}
					<!--Customization -->
					<div class="col-xs-12 col-12 col-sm-12 col-md-12 product-customization ap5-product-customization-container" style="clear: left">
						<div class="card card-block">
							<h3 class="h4 card-title">{l s='Product customization' d='Shop.Theme.Catalog'}</h3>

							<!-- Customizable products -->
							<form method="post" action="{pm_advancedpack::getPackUpdateURL($productPack.presentation.id_product)|escape:'html':'UTF-8'}" enctype="multipart/form-data" id="customizationForm-{$productPack.presentation.id_product}" class="ap5-customization-form clearfix" data-id-product-pack="{$productPack.id_product_pack|intval}">
								{if $productPack.presentation.text_fields|intval}
									<ul class="clearfix">
									{counter start=0 assign='customizationField'}
									{foreach from=$productPack.customization.customizationFields item='field' name='customizationFields'}
										{if $field.type == 1}
											<li class="product-customization-item">
												<label for="textField{$customizationField}" class="{if $field.required}required{/if}">
													{assign var='key' value='textFields_'|cat:$productPack.presentation.id_product|cat:'_'|cat:$field.id_customization_field}
													{if !empty($field.name)}
														{$field.name}
													{/if}
													{if $field.required}<sup>*</sup>{/if}
												</label>
												<textarea name="textField{$field.id_customization_field}" class="product-message ap5-customization-block-input" id="textField{$customizationField}"{if $field.required} required{/if} data-id-customization-field="{$field.id_customization_field}">{strip}
													{if isset($productPack.customization.textFields.$key)}
														{$productPack.customization.textFields.$key|stripslashes}
													{/if}
												{/strip}</textarea>
												<small class="float-xs-right pull-xs-right">{l s='250 char. max' d='Shop.Forms.Help'}</small>
											</li>
											{counter}
										{/if}
									{/foreach}
									</ul>
								{/if}
							</form>
						</div>
					</div>
					<!--end Customization -->
					{/if}
				{/block}

				{*
				<section class="page-product-box">
				{$productPack.HOOK_PRODUCT_TAB}
				{if isset($productPack.HOOK_PRODUCT_TAB_CONTENT) && $productPack.HOOK_PRODUCT_TAB_CONTENT}{$productPack.HOOK_PRODUCT_TAB_CONTENT}{/if}
				</section>
				*}
			</div>
		{/foreach}
	</div>
</div>

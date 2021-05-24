{assign var=nbPackProducts value=count($productsPack)}
{if $bootstrapTheme}
	{assign var=colWidth value='colWidth33'}
	{if $nbPackProducts >= 3}{assign var=colWidth value='colWidth25'}{/if}
{else}
	{assign var=colWidth value='colWidth50'}
	{if $nbPackProducts >= 3}{assign var=colWidth value='colWidth33'}{/if}
{/if}

		<!-- pack product list-->  
		<div id="ap5-product-list" class="ap5-product-list clearfix row {$colWidth} col-sm-8 col-md-9{if $packAvailableQuantity <= 0} ap5-pack-oos{/if}">

<div class="list-deco-fleche">
        	<a href="#0" class="prev-deco"></a>
        	<a href="#0" class="next-deco"></a>        
        </div>
        
        <div class="list-pot-fleche">
        	<a href="#0" class="prev-pot"></a>
        	<a href="#0" class="next-pot"></a>        
        </div>

		{if $bootstrapTheme}
			{* pb-right-column*}
			<div class="ap5-pack-product ap5-right ap5-no-plus-icon col-xs-12 col-sm-4">
			{if ($product->show_price && !isset($restricted_country_mode)) || isset($groups) || $product->reference || (isset($HOOK_PRODUCT_ACTIONS) && $HOOK_PRODUCT_ACTIONS)}
				{* add to cart form*}
				<form id="buy_block" class="ap5-buy-block{if $PS_CATALOG_MODE && !isset($groups) && $product->quantity > 0} hidden{/if}" action="{pm_advancedpack::getPackAddCartURL($product->id)|escape:'html':'UTF-8'}" method="post">
					{* hidden datas *}
					<p class="hidden">
						<input type="hidden" name="token" value="{$static_token}" />
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
		{/if}
		
		{foreach from=$productsPack item=productPack}
			{assign var=imageIds value="`$productPack.id_product`-`$productPack.image.id_image`"}
			{if !empty($productPack.image.legend)}
				{assign var=imageTitle value=$productPack.image.legend|escape:'html':'UTF-8'}
			{else}
				{assign var=imageTitle value=$productPack.productObj->name|escape:'html':'UTF-8'}
			{/if}

			<div id="ap5-pack-product-{$productPack.id_product_pack}" class="ap5-pack-product ap5-no-plus-icon col-xs-12 col-sm-4 {if $nbPackProducts != 2} col-md-4{/if}{if isset($productsPackErrors[$productPack.id_product_pack])} ap5-product-pack-row-has-errors{/if}{if isset($productsPackFatalErrors[$productPack.id_product_pack])} ap5-product-pack-row-has-fatal-errors{/if}{if !$productPack.attributes.groups|@count} ap5-no-attributes{/if}{if in_array($productPack.id_product_pack, $packExcludeList)} ap5-is-excluded-product{/if}">

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
						<a target="_blank" href="{$link->getProductLink($productPack.productObj, null, null, null, null, null, $productPack.id_product_attribute)|escape:'html':'UTF-8'}" title="{$productPack.productObj->name|escape:'html':'UTF-8'}" itemprop="url">
							{$productPack.productObj->name|escape:'html':'UTF-8'}
						</a>
					</p>
					{if !$mobile_device}
						<div class="ap5-pack-product-image">
							<a class="no-print fancybox" title="{$imageTitle}" href="{$link->getImageLink($productPack.productObj->link_rewrite, $imageIds, $imageFormatProductZoom)|escape:'html':'UTF-8'}">
								<img class="img-responsive" id="thumb_{$productPack.image.id_image|intval}" src="{$link->getImageLink($productPack.productObj->link_rewrite, $imageIds, $imageFormatProductCover)|escape:'html':'UTF-8'}" alt="{$imageTitle}" title="{$imageTitle}" height="{$imageFormatProductCoverHeight}" width="{$imageFormatProductCoverWidth}" itemprop="image" />
							</a>
						</div>
						<hr class="ap5-pack-product-icon-plus" />
						{if $packShowProductsThumbnails && count($productPack.images) > 1}
							<div class="ap5-pack-product-slideshow clearfix{if count($productPack.images) < 3} no-carousel{/if}" style="height: {$imageFormatProductSlideshowHeight}px;{if count($productPack.images) < 3} width: {$imageFormatProductSlideshowWidth * count($productPack.images)}px{/if}">
							{if count($productPack.images) > 1}
								{foreach from=$productPack.images item=productPackImage}
									{assign var=productPackImageTitle value=$productPack.productObj->name|escape:'html':'UTF-8'}
									{assign var=productPackImageIds value="`$productPack.id_product`-`$productPackImage.id_image`"}
									<div id="ap5-pack-product-thumbnail-{$productPackImage.id_image|intval}" class="ap5-pack-product-thumbnail">
										<a data-fancybox-group="ap5-pack-product-images-{$productPack.id_product_pack}" class="fancybox" title="{$productPackImageTitle}" href="{$link->getImageLink($productPack.productObj->link_rewrite, $productPackImageIds, $imageFormatProductZoom)|escape:'html':'UTF-8'}">
											<img class="img-responsive" id="thumb_{$productPackImage.id_image|intval}" src="{$link->getImageLink($productPack.productObj->link_rewrite, $productPackImageIds, $imageFormatProductSlideshow)|escape:'html':'UTF-8'}" alt="{$productPackImageTitle}" title="{$productPackImageTitle}" height="{$imageFormatProductSlideshowHeight}" width="{$imageFormatProductSlideshowWidth}" itemprop="image" />
										</a>
									</div>
								{/foreach}
							{/if}
							</div>
						{else if $packShowProductsThumbnails && count($productPack.images) == 1 && $packMaxImagesPerProduct > 1}
							<div class="ap5-pack-product-slideshow clearfix no-carousel" style="height: {$imageFormatProductSlideshowHeight}px;"></div>
						{/if}
					{else}
						<div class="ap5-pack-product-mobile-slideshow clearfix">
							{foreach from=$productPack.imagesMobile item=productPackImage}
								{assign var=productPackImageTitle value=$productPack.productObj->name|escape:'html':'UTF-8'}
								{assign var=productPackImageIds value="`$productPack.id_product`-`$productPackImage.id_image`"}
	
								<div id="ap5-pack-product-thumbnail-{$productPackImage.id_image|intval}" class="ap5-pack-product-thumbnail">
									<img class="img-responsive" id="thumb_{$productPackImage.id_image|intval}" src="{$link->getImageLink($productPack.productObj->link_rewrite, $productPackImageIds, $imageFormatProductCoverMobile)|escape:'html':'UTF-8'}" alt="{$productPackImageTitle}" title="{$productPackImageTitle}" height="{$imageFormatProductCoverMobileHeight}" width="{$imageFormatProductCoverMobileWidth}" itemprop="image" />
								</div>
							{/foreach}
						</div>
					{/if}
					{if $productPack.productObj->show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}
					{if $packShowProductsThumbnails && $packMaxImagesPerProduct > 1}<hr />{/if}
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
									{if $productPack.productPackPrice > 0}
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
									{/if}
									<p id="old_price" class="ap5-pack-product-original-price text-center">
										<span id="old_price_display" class="ap5-pack-product-original-price-value">
										{if !$priceDisplay || $priceDisplay == 2}
											{convertPrice price=($productPack.productClassicPrice * $productPack.quantity)}
										{elseif $priceDisplay == 1}
											{convertPrice price=($productPack.productClassicPriceTaxExcl * $productPack.quantity)}
										{/if}
										</span>
									</p>
								</div>
							{/if}
							{if $packShowProductsAvailability}
							<!-- availability -->
							<p id="availability_statut" class="ap5-availability-statut">
								{if StockAvailable::getQuantityAvailableByProduct((int)$productPack.productObj->id, $productPack.id_product_attribute) <= 0}
								<span id="availability_value" class="ap5-product-availability-oos warning_inline">{if $productPack.productObj->available_later}{$productPack.productObj->available_later|escape:'html':'UTF-8'}{else}{l s='Out of stock' mod='pm_advancedpack'}{/if}</span>
								{else}
								<span id="availability_value" class="ap5-product-availability-is">{if $productPack.productObj->available_now}{$productPack.productObj->available_now|escape:'html':'UTF-8'}{else}{l s='In stock' mod='pm_advancedpack'}{/if}</span>                       
								{/if}
							</p>
							{/if}
						</div>
					</div>
					{/if}
					<hr />
					{if $packAllowRemoveProduct && $packShowProductsQuantityWanted}
					<!-- quantity wanted -->
					<fieldset id="ap5-quantity-wanted-{$productPack.id_product_pack|intval}" class="attribute_fieldset ap5-attribute-fieldset ap5-quantity-fieldset">
						<label class="attribute_label" for="quantity_wanted_{$productPack.id_product_pack|intval}">{l s='Quantity:' mod='pm_advancedpack'}</label>
						<div class="attribute_list ap5-attribute-list ap5-quantity-input-container">
							<p id="quantity_wanted_p">
								<input type="text" name="qty_{$productPack.id_product_pack|intval}" id="quantity_wanted_{$productPack.id_product_pack|intval}" value="{$productPack.quantity|intval}" class="ap5-quantity-wanted" data-id-product-pack="{$productPack.id_product_pack|intval}" />
							</p>
						</div>
					</fieldset>
					{/if}
					{if $productPack.attributes.groups|@count >= 1}
					<!-- attributes -->
					<div id="attributes" class="ap5-attributes" data-id-product-pack="{$productPack.id_product_pack|intval}">
						{foreach from=$productPack.attributes.groups key=id_attribute_group item=group}
							{if $group.attributes|@count}
								<fieldset id="ap5-attribute-fieldset-{$id_attribute_group|intval}" class="attribute_fieldset ap5-attribute-fieldset">
									<label class="attribute_label" {if $group.group_type != 'color' && $group.group_type != 'radio'}for="group_{$id_attribute_group|intval}"{/if}>{$group.name|escape:'html':'UTF-8'} :&nbsp;</label>
									{assign var="groupName" value="group_`$productPack.id_product_pack`_$id_attribute_group"}
									<div class="attribute_list ap5-attribute-list">
										{if ($group.group_type == 'select')}
											<select name="{$groupName}" id="group_{$id_attribute_group|intval}" class="form-control attribute_select ap5-attribute-select no-print">
												{foreach from=$group.attributes key=id_attribute item=group_attribute}
													{assign var=ap5_isCurrentSelectedIdAttribute value=((isset($productsPackErrors[$productPack.id_product_pack]) && isset($packCompleteAttributesList[$productPack.id_product_pack]) && in_array($id_attribute, $packCompleteAttributesList[$productPack.id_product_pack])) || !isset($productsPackErrors[$productPack.id_product_pack]) && $group.default == $id_attribute)}
													<option value="{$id_attribute|intval}"{if $ap5_isCurrentSelectedIdAttribute} selected="selected"{/if} title="{$group_attribute|escape:'html':'UTF-8'}">{$group_attribute|escape:'html':'UTF-8'}</option>
												{/foreach}
											</select>
										{elseif ($group.group_type == 'color')}
											<ul id="color_to_pick_list" class="clearfix ap5-color-to-pick-list ap5-color-to-pick-list-{$productPack.id_product_pack|intval}-{$id_attribute_group|intval}">
												{assign var="default_colorpicker" value=""}
												{foreach from=$group.attributes key=id_attribute item=group_attribute}
													{assign var=ap5_isCurrentSelectedIdAttribute value=((isset($productsPackErrors[$productPack.id_product_pack]) && isset($packCompleteAttributesList[$productPack.id_product_pack]) && in_array($id_attribute, $packCompleteAttributesList[$productPack.id_product_pack])) || $group.default == $id_attribute)}
													<li{if $ap5_isCurrentSelectedIdAttribute} class="selected"{/if}>
														<a href="{$link->getProductLink($product)|escape:'html':'UTF-8'}" data-id-product-pack="{$productPack.id_product_pack|intval}" data-id-attribute-group="{$id_attribute_group|intval}" data-id-attribute="{$id_attribute|intval}" id="color_{$id_attribute|intval}" name="{$productPack.attributes.colors.$id_attribute.name|escape:'html':'UTF-8'}" class="color_pick{if $ap5_isCurrentSelectedIdAttribute} selected{/if}" style="background: {$productPack.attributes.colors.$id_attribute.value|escape:'html':'UTF-8'};" title="{$productPack.attributes.colors.$id_attribute.name|escape:'html':'UTF-8'}">
															{if file_exists($col_img_dir|cat:$id_attribute|cat:'.jpg')}
																<img src="{$img_col_dir}{$id_attribute|intval}.jpg" alt="{$productPack.attributes.colors.$id_attribute.name|escape:'html':'UTF-8'}" width="20" height="20" />
															{/if}
														</a>
													</li>
													{if $ap5_isCurrentSelectedIdAttribute}
														{$default_colorpicker = $id_attribute}
													{/if}
												{/foreach}
											</ul>
											<input type="hidden" class="color_pick_hidden_{$productPack.id_product_pack|intval}_{$id_attribute_group|intval}" name="{$groupName|escape:'html':'UTF-8'}" value="{$default_colorpicker|intval}" />
										{elseif ($group.group_type == 'radio')}
											<ul>
												{foreach from=$group.attributes key=id_attribute item=group_attribute}
													{assign var=ap5_isCurrentSelectedIdAttribute value=((isset($productsPackErrors[$productPack.id_product_pack]) && isset($packCompleteAttributesList[$productPack.id_product_pack]) && in_array($id_attribute, $packCompleteAttributesList[$productPack.id_product_pack])) || $group.default == $id_attribute)}
													<li>
														<input type="radio" class="attribute_radio ap5-attribute-radio" name="{$groupName|escape:'html':'UTF-8'}" value="{$id_attribute}" {if $ap5_isCurrentSelectedIdAttribute} checked="checked"{/if} />
														<span>{$group_attribute|escape:'html':'UTF-8'}</span>
													</li>
												{/foreach}
											</ul>
										{/if}
									</div> <!-- end attribute_list -->
								</fieldset>
							{/if}
						{/foreach}
					</div>
					{/if}
					{* Let's display error list *}
					{if isset($productsPackErrors[$productPack.id_product_pack]) || isset($productsPackFatalErrors[$productPack.id_product_pack])}
					{if isset($productsPackFatalErrors[$productPack.id_product_pack])}<div class="ap5-overlay"></div>{/if}
					<div class="alert animated shake {if isset($productsPackFatalErrors[$productPack.id_product_pack])}alert-danger{else}alert-warning{/if}">
						<ol>
						{if isset($productsPackErrors[$productPack.id_product_pack])}
							{foreach from=$productsPackErrors[$productPack.id_product_pack] item=errorRow}
								<li>{$errorRow|escape:'html':'UTF-8'}</li>
							{/foreach}
						{/if}
						{if isset($productsPackFatalErrors[$productPack.id_product_pack])}
							{foreach from=$productsPackFatalErrors[$productPack.id_product_pack] item=errorRow}
								<li>{$errorRow|escape:'html':'UTF-8'}</li>
							{/foreach}
						{/if}
						</ol>
					</div>
					{/if}
					{if $packAllowRemoveProduct}
						{if !in_array($productPack.id_product_pack, $packExcludeList)}
						<span class="ap5-pack-product-icon-remove" data-id-product-pack="{$productPack.id_product_pack|intval}"></span>
						{else}
						<span class="ap5-pack-product-icon-check" data-id-product-pack="{$productPack.id_product_pack|intval}"></span>
						{/if}
					{/if}
				</div>
			</div>
		{/foreach}
		</div>
		<!-- end pack product list -->

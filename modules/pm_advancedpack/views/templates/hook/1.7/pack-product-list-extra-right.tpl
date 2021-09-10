		<!-- pack product list-->
		<section id="ap5-product-list" class="ap5-on-product-page ap5-product-list {if $packAvailableQuantity <= 0} ap5-pack-oos{/if}">
		{assign var=nbPackProducts value=count($productsPack)}
		{foreach from=$productsPack item=productPack name=productsPackLoop}
			<div id="ap5-pack-product-{$productPack.id_product_pack}" class="ap5-pack-product {if isset($productsPackErrors[$productPack.id_product_pack])} ap5-product-pack-row-has-errors{/if}{if isset($productsPackFatalErrors[$productPack.id_product_pack])} ap5-product-pack-row-has-fatal-errors{/if}{if empty($productPack.attributes.groups)} ap5-no-attributes{/if}{if in_array($productPack.id_product_pack, $packExcludeList)} ap5-is-excluded-product{/if}">

				<div class="ap5-pack-product-content">

					<h2 class="ap5-pack-product-title">
						<a target="_blank" href="{$productPack.presentation.url}" title="{$productPack.presentation.name}" itemprop="url">
							{$productPack.presentation.name}
						</a>

						{if $packAllowRemoveProduct}
							{if !in_array($productPack.id_product_pack, $packExcludeList)}
								<span class="ap5-pack-product-remove-label pull-right" data-id-product-pack="{$productPack.id_product_pack|intval}">[{l s='Remove' mod='pm_advancedpack'}]</span>
							{else}
								<span class="ap5-pack-product-add-label pull-right" data-id-product-pack="{$productPack.id_product_pack|intval}">[{l s='Add' mod='pm_advancedpack'}]</span>
							{/if}
						{/if}
					</h2>

					<div class="ap5-pack-product-infos clearfix">
						<div class="ap5-pack-product-quantity pull-left">
							{if $packAllowRemoveProduct && $packShowProductsQuantityWanted}
								<!-- quantity wanted -->
								<fieldset id="ap5-quantity-wanted-{$productPack.id_product_pack|intval}" class="attribute_fieldset ap5-attribute-fieldset ap5-quantity-fieldset">
									<label class="attribute_label" for="quantity_wanted_{$productPack.id_product_pack|intval}">{l s='Quantity' d='Shop.Theme.Catalog'} </label>
									<div class="attribute_list ap5-attribute-list ap5-quantity-input-container">
										<input type="text" name="qty_{$productPack.id_product_pack|intval}" id="quantity_wanted_{$productPack.id_product_pack|intval}" value="{$productPack.quantity|intval}" class="ap5-quantity-wanted" data-id-product-pack="{$productPack.id_product_pack|intval}" data-available-quantity="{$packAvailableQuantityList[$productPack.id_product_pack][$productPack.id_product_attribute]|intval}"/>
										<a href="#" rel="quantity_wanted_{$productPack.id_product_pack|intval}" class="btn btn-default button-minus ap5-product-quantity-down"><span><i class="icon-minus"></i></span></a>
										<a href="#" rel="quantity_wanted_{$productPack.id_product_pack|intval}" class="btn btn-default button-plus ap5-product-quantity-up"><span><i class="icon-plus"></i></span></a>
									</div>
								</fieldset>
							{else}
								<span class="label">{l s='Quantity' d='Shop.Theme.Catalog'}</span>
								{$productPack.quantity|intval}
							{/if}
						</div>

						{if $packShowProductsPrice && empty($productsPackForceHideInfoList[$productPack.id_product_pack])}
						<div class="ap5-pack-product-price pull-right">
							<span class="label">{l s='Product price' mod='pm_advancedpack'} : </span>
							{if $productPack.presentation.show_price}
								{if $productPack.productPackPrice == 0}
									{l s='Gift' d='Shop.Theme.Checkout'}
								{else}
									{if !$priceDisplay || $priceDisplay == 2}
										<span>{$productPack.presentation.productPackPriceTotal}</span>
									{elseif $priceDisplay == 1}
										<span>{$productPack.presentation.productPackPriceTaxExclTotal}</span>
									{/if}
								{/if}
							{/if}
						</div>
						{/if}
					</div>

					<div class="ap5-delivery-information">
						{if $productPack.presentation.additional_delivery_times == 1}
							{if $productPack.presentation.delivery_information}
								<span class="delivery-information">{$productPack.presentation.delivery_information}</span>
							{/if}
						{elseif $productPack.presentation.additional_delivery_times == 2}
							{if $productPack.presentation.quantity > 0}
								<span class="delivery-information">{$productPack.presentation.delivery_in_stock}</span>
								{* Out of stock message should not be displayed if customer can't order the product. *}
							{elseif $productPack.presentation.quantity <= 0 && $productPack.presentation.add_to_cart_url}
								<span class="delivery-information">{$productPack.presentation.delivery_out_stock}</span>
							{/if}
						{/if}
					</div>

					{if !empty($productPack.attributes.groups)}
						<!-- attributes -->
						<div class="product-variants ap5-attributes" data-id-product-pack="{$productPack.id_product_pack|intval}">
							{foreach from=$productPack.attributes.groups key=id_attribute_group item=group}
								{if $group.attributes|@count}
									{foreach from=$group.attributes key=id_attribute item=group_attribute}
										{* Force the user-selected attribute to be the default one *}
										{if isset($packCompleteAttributesList[$productPack.id_product_pack]) && in_array($id_attribute, $packCompleteAttributesList[$productPack.id_product_pack])}
											{$group['default'] = $id_attribute}
										{/if}
									{/foreach}
									<div id="ap5-product-variants-item-{$id_attribute_group|intval}" class="clearfix product-variants-item ap5-attribute-fieldset">
										<span class="control-label">{$group.name}</span>
										{assign var="groupName" value="group_`$productPack.id_product_pack`_$id_attribute_group"}
										<div class="attribute_list ap5-attribute-list">
											{if ($group.group_type == 'select')}
												<select name="{$groupName}" id="group_{$id_attribute_group|intval}" class="ap5-attribute-select no-print form-control form-control-select"{if in_array($productPack.id_product_pack, $packExcludeList)} disabled="disabled"{/if}>
													{foreach from=$group.attributes key=id_attribute item=group_attribute}
														{assign var=ap5_isCurrentSelectedIdAttribute value=((isset($productsPackErrors[$productPack.id_product_pack]) && isset($packCompleteAttributesList[$productPack.id_product_pack]) && in_array($id_attribute, $packCompleteAttributesList[$productPack.id_product_pack])) || $group.default == $id_attribute)}
														<option value="{$id_attribute|intval}"{if $ap5_isCurrentSelectedIdAttribute} selected="selected"{/if} title="{$group_attribute}">{$group_attribute}</option>
													{/foreach}
												</select>
											{elseif ($group.group_type == 'color')}
												<ul class="ap5-color-to-pick-list ap5-color-to-pick-list-{$productPack.id_product_pack|intval}-{$id_attribute_group|intval}">
													{assign var="default_colorpicker" value=""}
													{foreach from=$group.attributes key=id_attribute item=group_attribute}
														{assign var=ap5_isCurrentSelectedIdAttribute value=((isset($productsPackErrors[$productPack.id_product_pack]) && isset($packCompleteAttributesList[$productPack.id_product_pack]) && in_array($id_attribute, $packCompleteAttributesList[$productPack.id_product_pack])) || $group.default == $id_attribute)}
														<li class="float-left float-xs-left pull-xs-left input-container{if $ap5_isCurrentSelectedIdAttribute} selected{/if}">
															<a href="{$productPack.presentation.url}" data-id-product-pack="{$productPack.id_product_pack|intval}" data-id-attribute-group="{$id_attribute_group|intval}" data-id-attribute="{$id_attribute|intval}" id="color_{$id_attribute|intval}" name="{$productPack.attributes.colors.$id_attribute.name}" class="ap5-color color color_pick{if $ap5_isCurrentSelectedIdAttribute} selected{/if}{if in_array($productPack.id_product_pack, $packExcludeList)} disabled{/if}" style="background: {$productPack.attributes.colors.$id_attribute.value};" title="{$productPack.attributes.colors.$id_attribute.name}">
																{if $productPack.attributes.colors.$id_attribute.image_exists}
																	<img src="{$urls.img_col_url}{$id_attribute|intval}.jpg" alt="{$productPack.attributes.colors.$id_attribute.name}" width="20" height="20" />
																{/if}
															</a>
														</li>
														{if $ap5_isCurrentSelectedIdAttribute}
															{$default_colorpicker = $id_attribute}
														{/if}
													{/foreach}
												</ul>
												<input type="hidden" class="color_pick_hidden_{$productPack.id_product_pack|intval}_{$id_attribute_group|intval}" name="{$groupName}" value="{$default_colorpicker|intval}" />
											{elseif ($group.group_type == 'radio')}
												<ul>
													{foreach from=$group.attributes key=id_attribute item=group_attribute}
														{assign var=ap5_isCurrentSelectedIdAttribute value=((isset($productsPackErrors[$productPack.id_product_pack]) && isset($packCompleteAttributesList[$productPack.id_product_pack]) && in_array($id_attribute, $packCompleteAttributesList[$productPack.id_product_pack])) || $group.default == $id_attribute)}
														<li class="input-container float-left float-xs-left pull-xs-left">
															<input type="radio" class="input-radio ap5-attribute-radio" name="{$groupName}" value="{$id_attribute}" {if $ap5_isCurrentSelectedIdAttribute} checked="checked"{/if}{if in_array($productPack.id_product_pack, $packExcludeList)} disabled="disabled"{/if} />
															<span class="radio-label">{$group_attribute}</span>
														</li>
													{/foreach}
												</ul>
											{/if}
										</div> <!-- end attribute_list -->
									</div>
								{/if}
							{/foreach}
						</div>
					{/if}

					{* Customizable products *}
					{if $productPack.presentation.customizable && sizeof($productPack.customization.customizationFields)}
					<div class="col-xs-12 col-12 col-sm-12 col-md-12 product-customization ap5-product-customization-container" style="clear: left">
						<div class="card card-block">
							<h3 class="h4 card-title">{l s='Product customization' d='Shop.Theme.Catalog'}</h3>

							{* Create an empty form to bypass the invalid HTML hierarchy errors *}
							{if !empty($packDisplayModeSimple) && $smarty.foreach.productsPackLoop.first}<form style="display:none"></form>{/if}

							<!-- Customizable products -->
							<form method="post" action="{pm_advancedpack::getPackUpdateURL($productPack.presentation.id_product)}" enctype="multipart/form-data" id="customizationForm-{$productPack.presentation.id_product}" class="ap5-customization-form clearfix" data-id-product-pack="{$productPack.id_product_pack|intval}">
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

					{* Let's display error list *}
					{if isset($productsPackErrors[$productPack.id_product_pack]) || isset($productsPackFatalErrors[$productPack.id_product_pack])}
					{if isset($productsPackFatalErrors[$productPack.id_product_pack])}<div class="ap5-overlay"></div>{/if}
					<div class="alert animated shake {if isset($productsPackFatalErrors[$productPack.id_product_pack])}alert-danger{else}alert-warning{/if}">
						<ol>
						{if isset($productsPackErrors[$productPack.id_product_pack])}
							{foreach from=$productsPackErrors[$productPack.id_product_pack] item=errorRow}
								<li>{$errorRow}</li>
							{/foreach}
						{/if}
						{if isset($productsPackFatalErrors[$productPack.id_product_pack])}
							{foreach from=$productsPackFatalErrors[$productPack.id_product_pack] item=errorRow}
								<li>{$errorRow}</li>
							{/foreach}
						{/if}
						</ol>
					</div>
					{/if}
				</div>

			</div>

		{/foreach}
		</section>
		<!-- end pack product list -->

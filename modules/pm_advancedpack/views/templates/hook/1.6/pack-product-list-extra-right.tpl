{if isset($ap5_firstExecution) && $ap5_firstExecution}
<script type="text/javascript">
	$(document).ready(function() {
		ap5_changeBuyBlock('{pm_advancedpack::getPackAddCartURL($product->id)}', {$ap5_buyBlockPackPriceContainer});
	});
</script>
{/if}

		<!-- pack product list-->  
		<section id="ap5-product-list" class="ap5-on-product-page ap5-product-list {if $packAvailableQuantity <= 0} ap5-pack-oos{/if}">
			<h3 class="page-product-heading">{l s='Content of this pack:' mod='pm_advancedpack'}</h3>
		{assign var=nbPackProducts value=count($productsPack)}
		{foreach from=$productsPack item=productPack}
			<div id="ap5-pack-product-{$productPack.id_product_pack}" class="ap5-pack-product {if isset($productsPackErrors[$productPack.id_product_pack])} ap5-product-pack-row-has-errors{/if}{if isset($productsPackFatalErrors[$productPack.id_product_pack])} ap5-product-pack-row-has-fatal-errors{/if}{if !$productPack.attributes.groups|@count} ap5-no-attributes{/if}{if in_array($productPack.id_product_pack, $packExcludeList)} ap5-is-excluded-product{/if}">

				<div class="ap5-pack-product-content">

					<h2 class="ap5-pack-product-title">
						<a target="_blank" href="{$link->getProductLink($productPack.productObj, null, null, null, null, null, $productPack.id_product_attribute, true)|escape:'html':'UTF-8'}" title="{$productPack.productObj->name|escape:'html':'UTF-8'}" itemprop="url">
							{$productPack.productObj->name|escape:'html':'UTF-8'}
						</a>

						{if $packAllowRemoveProduct && $packShowProductsQuantityWanted}
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
									<label class="attribute_label" for="quantity_wanted_{$productPack.id_product_pack|intval}">{l s='Quantity:' mod='pm_advancedpack'} </label>
									<div class="attribute_list ap5-attribute-list ap5-quantity-input-container">
										<p id="quantity_wanted_p">
											<input type="text" name="qty_{$productPack.id_product_pack|intval}" id="quantity_wanted_{$productPack.id_product_pack|intval}" value="{$productPack.quantity|intval}" class="ap5-quantity-wanted" data-id-product-pack="{$productPack.id_product_pack|intval}" />
											<a href="#" rel="quantity_wanted_{$productPack.id_product_pack|intval}" class="btn btn-default button-minus ap5-product-quantity-down"><span><i class="icon-minus"></i></span></a>
											<a href="#" rel="quantity_wanted_{$productPack.id_product_pack|intval}" class="btn btn-default button-plus ap5-product-quantity-up"><span><i class="icon-plus"></i></span></a>
										</p>
									</div>
								</fieldset>
							{else}
								<span class="label">{l s='Quantity:' mod='pm_advancedpack'}</span>
								{$productPack.quantity|intval}
							{/if}
						</div>

						<div class="ap5-pack-product-price pull-right">
							<span class="label">{l s='Product price' mod='pm_advancedpack'} : </span>
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
						</div>
					</div>

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

					<!-- Customizable products -->
					{if $productPack.productObj->customizable && sizeof($productPack.customization.customizationFields)}
						<div id="idTab10" class="bullet customization_block">
							<form method="post" action="{pm_advancedpack::getPackUpdateURL($productPack.productObj->id)|escape:'html':'UTF-8'}" enctype="multipart/form-data" id="customizationForm-{$productPack.productObj->id}" class="ap5-customization-form clearfix" data-id-product-pack="{$productPack.id_product_pack|intval}">
								{if $productPack.productObj->text_fields|intval}
								<div class="customizableProductsText">
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
				</div>

			</div>
			
		{/foreach}
		</section>
		<!-- end pack product list -->
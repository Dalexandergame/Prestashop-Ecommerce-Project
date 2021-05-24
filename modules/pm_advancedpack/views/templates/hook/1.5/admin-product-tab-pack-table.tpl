				{foreach from=$packContent item='packProduct' key='idProductPackKey'}
					{assign var=imageCoverId value=Product::getCover($packProduct['id_product'])}
					{if isset($packProduct['id_product_pack']) && $packProduct['id_product_pack']}
						{assign var=idProductPack value=$packProduct['id_product_pack']}
					{else}
						{assign var=idProductPack value=$idProductPackKey}
					{/if}
					{if isset($packProduct['default_id_product_attribute']) && $packProduct['default_id_product_attribute']}
						{assign var=idProductAttribute value=$packProduct['default_id_product_attribute']}
					{else}
						{assign var=idProductAttribute value=null}
					{/if}
					{assign var=currentProductPrice value=AdvancedPack::getPriceStaticPack($packProduct['productObj']->id, false, $idProductAttribute, 6, null, false, true, $packProduct['quantity'])}
					{assign var=originalProductPrice value=AdvancedPack::getPriceStaticPack($packProduct['productObj']->id, false, $idProductAttribute, 6, null, false, false, $packProduct['quantity'])}
				<tr id="ap5_packRow-{$idProductPack|escape:'html':'UTF-8'}" data-id-product-pack="{$idProductPack|escape:'html':'UTF-8'}">
					<td class="center">
						{AdvancedPackCoreClass::getThumbnailImageHTML($packProduct['id_product'], $imageCoverId.id_image)}
					</td>
					<td>
						<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}&updateproduct&id_product={$packProduct['id_product']|intval}" target="_blank" title="{l s='Edit this product' mod='pm_advancedpack'}"><strong>{$packProduct['productObj']->name|escape:'html':'UTF-8'}</strong></a><br />
						<em>{l s='Ref:' mod='pm_advancedpack'} {$packProduct['productObj']->reference|escape:'html':'UTF-8'}<br />
						{l s='Stock:' mod='pm_advancedpack'} {StockAvailable::getQuantityAvailableByProduct($packProduct['productObj']->id)|intval}<br />
						{l s='Sales:' mod='pm_advancedpack'} {ProductSale::getNbrSales($packProduct['productObj']->id)|intval}</em>
					</td>
					<td>
						{if is_array($packProduct['productCombinations']) && count($packProduct['productCombinations'])}
						{foreach from=$packProduct['productCombinations'] item='productCombination'}
							{if (isset($packProduct['default_id_product_attribute']) && (int)$packProduct['default_id_product_attribute'] && (int)$packProduct['default_id_product_attribute'] == (int)$productCombination['id_product_attribute']) || (!isset($packProduct['default_id_product_attribute']) || !(int)$packProduct['default_id_product_attribute']) && (int)$productCombination['id_product_attribute'] == (int)Product::getDefaultAttribute($packProduct['productObj']->id)}
								{assign var=defaultCombinationTmp value=','|explode:$productCombination['attribute_designation']}
								{foreach from=$defaultCombinationTmp item='defaultCombinationRow'}
									<span class="clearfix">{$defaultCombinationRow|trim|escape:'html':'UTF-8'}</span>
								{/foreach}
							{/if}
						{/foreach}
						{else}
						{l s='N/A' mod='pm_advancedpack'}
						{/if}
					</td>
					<td class="center">
						{l s='Combinations:' mod='pm_advancedpack'}&nbsp;
						{if is_array($packProduct['productCombinations']) && count($packProduct['productCombinations'])}
						<input type="checkbox"{if count($packProduct['productCombinationsWhiteList'])} checked="checked"{/if} value="1" id="ap5_customCombinations-{$idProductPack|escape:'html':'UTF-8'}" name="ap5_customCombinations-{$idProductPack|escape:'html':'UTF-8'}" class="ap5_customCombinations" data-id-product-pack="{$idProductPack|escape:'html':'UTF-8'}" />
						{else}
						{l s='N/A' mod='pm_advancedpack'}
						<input type="hidden" value="0" id="ap5_customCombinations-{$idProductPack|escape:'html':'UTF-8'}" name="ap5_customCombinations-{$idProductPack|escape:'html':'UTF-8'}" />
						{/if}
						<br />
						{l s='Customization:' mod='pm_advancedpack'}&nbsp;
						{if is_array($packProduct['productCustomizationFields']) && count($packProduct['productCustomizationFields'])}
						<input type="checkbox"{if count($packProduct['productCustomizationFieldsWhiteList'])} checked="checked"{/if} value="1" id="ap5_customizationFields-{$idProductPack|escape:'html':'UTF-8'}" name="ap5_customizationFields-{$idProductPack|escape:'html':'UTF-8'}" class="ap5_customizationFields" data-id-product-pack="{$idProductPack|escape:'html':'UTF-8'}" />
						{else}
						{l s='N/A' mod='pm_advancedpack'}
						<input type="hidden" value="0" id="ap5_customizationFields-{$idProductPack|escape:'html':'UTF-8'}" name="ap5_customizationFields-{$idProductPack|escape:'html':'UTF-8'}" />
						{/if}
						<br />
					</td>
					<td class="center">
						<input type="hidden" name="ap5_productList[]" value="{$idProductPack|escape:'html':'UTF-8'}" />
						<input type="hidden" name="ap5_originalIdProduct-{$idProductPack|escape:'html':'UTF-8'}" value="{$packProduct['productObj']->id|intval}" />
						<input type="text" required="required" value="{$packProduct['quantity']|intval}" size="2" id="ap5_quantity-{$idProductPack|escape:'html':'UTF-8'}" name="ap5_quantity-{$idProductPack|escape:'html':'UTF-8'}" min="1" class="ap5_quantity" />
					</td>
					<td class="ap5_productPrice-container">
						{if $originalProductPrice != $currentProductPrice}
						<span class="ap5-pack-product-original-price">{convertPrice price=$originalProductPrice}</span><br />
						{/if}
						<span class="ap5-pack-product-current-price">{convertPrice price=$currentProductPrice}</span>
					</td>
					<td class="ap5_discountCell">
						<input min="0" class="ap5_reductionAmount" type="text" required="required" onchange="this.value = this.value.replace(/,/g, '.');" value="{if $packProduct['reduction_type'] == 'percentage'}{toolsConvertPrice price=$packProduct['reduction_amount']*100}{else}{toolsConvertPrice price=$packProduct['reduction_amount']}{/if}" size="2" id="ap5_reductionAmount-{$idProductPack|escape:'html':'UTF-8'}" name="ap5_reductionAmount-{$idProductPack|escape:'html':'UTF-8'}" maxlength="14" data-id-product-pack="{$idProductPack|escape:'html':'UTF-8'}" /><select class="ap5_reductionType" name="ap5_reductionType-{$idProductPack|escape:'html':'UTF-8'}" id="ap5_reductionType-{$idProductPack|escape:'html':'UTF-8'}" data-id-product-pack="{$idProductPack|escape:'html':'UTF-8'}">
							<option value="percentage"{if $packProduct['reduction_type'] == 'percentage'} selected="selected"{/if}>%</option>
							<option value="amount"{if $packProduct['reduction_type'] == 'amount'} selected="selected"{/if}>{$defaultCurrency->sign|escape:'html':'UTF-8'}</option>
						</select>
					</td>
					<td class="center">
						<input type="checkbox"{if $packProduct['exclusive']} checked="checked"{/if} value="1" id="ap5_exclusive-{$idProductPack|escape:'html':'UTF-8'}" name="ap5_exclusive-{$idProductPack|escape:'html':'UTF-8'}" class="ap5_exclusive" />
					</td>
					<td class="center ap5_useReduc-container">
						<input type="checkbox"{if $packProduct['use_reduc']} checked="checked"{/if} value="1" id="ap5_useReduc-{$idProductPack|escape:'html':'UTF-8'}" name="ap5_useReduc-{$idProductPack|escape:'html':'UTF-8'}" class="ap5_useReduc" />
					</td>
					<td class="center">
						<a data-id-product-pack="{$idProductPack|escape:'html':'UTF-8'}" id="ap5_removeProduct-{$idProductPack|escape:'html':'UTF-8'}" class="ap5_removeProduct" href="javascript:void(0)"><img src="../img/admin/delete.gif" /></a>
					</td>
				</tr>
				{if is_array($packProduct['productCombinations']) && count($packProduct['productCombinations'])}
				<tr id="ap5_combinationsContainer-{$idProductPack|escape:'html':'UTF-8'}" class="nodrag nodrop ap5_combinationsContainer{if !count($packProduct['productCombinationsWhiteList'])} ap5-admin-hide hidden{/if}" data-id-product-pack="{$idProductPack|escape:'html':'UTF-8'}">
					<td colspan="10">
						<table id="ap5-pack-combination-table-{$idProductPack|escape:'html':'UTF-8'}" class="table configuration ap5_combinationsTable">
							<thead>
								<tr class="nodrag nodrop">
									<th class="center"><input type="checkbox"{if !count($packProduct['productCombinationsWhiteList'])} checked="checked"{/if} id="ap5_combinationIncludeAll-{$idProductPack|escape:'html':'UTF-8'}" class="ap5_combinationIncludeAll" data-id-product-pack="{$idProductPack|escape:'html':'UTF-8'}" /></th>
									<th class="center">{l s='Default combination' mod='pm_advancedpack'}</th>
									<th class="left">{l s='Combination name' mod='pm_advancedpack'}</th>
									<th class="left">{l s='Price impact' mod='pm_advancedpack'}</th>
									<th class="left ap5_discountCell">
										<input title="{l s='Allow custom discounts on combinations' mod='pm_advancedpack'}" type="checkbox" id="ap5_combinationDiscount-{$idProductPack|escape:'html':'UTF-8'}" class="ap5_combinationDiscount" data-id-product-pack="{$idProductPack|escape:'html':'UTF-8'}"{if isset($hasDiscountOnCombinations[$idProductPackKey]) && $hasDiscountOnCombinations[$idProductPackKey]} checked="checked"{/if} />&nbsp;{l s='Discount' mod='pm_advancedpack'}
									</th>
									<th class="left">{l s='Reference / EAN13' mod='pm_advancedpack'}</th>
									<th class="center">{l s='Quantity' mod='pm_advancedpack'}</th>
								</tr>
							</thead>
							<tbody>
								{include file="./admin-product-tab-combination-table.tpl"}
							</tbody>
						</table>
					</td>
				</tr>
				{/if}
				{if is_array($packProduct['productCustomizationFields']) && count($packProduct['productCustomizationFields'])}
				<tr id="ap5_customizationFieldsContainer-{$idProductPack|escape:'html':'UTF-8'}" class="nodrag nodrop ap5_customizationFieldsContainer{if !count($packProduct['productCustomizationFieldsWhiteList'])} ap5-admin-hide hidden{/if}" data-id-product-pack="{$idProductPack|escape:'html':'UTF-8'}">
					<td colspan="10">
						<table id="ap5-pack-customization-fields-table-{$idProductPack|escape:'html':'UTF-8'}" class="table configuration ap5_customizationFieldsTable">
							<thead>
								<tr class="nodrag nodrop">
									<th class="center"><input type="checkbox"{if !count($packProduct['productCustomizationFieldsWhiteList'])} checked="checked"{/if} id="ap5_customizationFieldsIncludeAll-{$idProductPack|escape:'html':'UTF-8'}" class="ap5_customizationFieldsIncludeAll" data-id-product-pack="{$idProductPack|escape:'html':'UTF-8'}" /></th>
									<th class="left">{l s='Field name' mod='pm_advancedpack'}</th>
									<th class="left">{l s='Required' mod='pm_advancedpack'}</th>
								</tr>
							</thead>
							<tbody>
								{include file="./admin-product-tab-customization-fields-table.tpl"}
							</tbody>
						</table>
					</td>
				</tr>
				{/if}
				{/foreach}
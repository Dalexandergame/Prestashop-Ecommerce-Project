				{foreach from=$packProduct['productCombinations'] item='productCombination'}
					{assign var=ap5_isDefaultCombination value=((isset($packProduct['default_id_product_attribute']) && (int)$packProduct['default_id_product_attribute'] && (int)$packProduct['default_id_product_attribute'] == (int)$productCombination['id_product_attribute']) || (!isset($packProduct['default_id_product_attribute']) || !(int)$packProduct['default_id_product_attribute']) && (int)$productCombination['id_product_attribute'] == (int)Product::getDefaultAttribute($packProduct['productObj']->id))}
					{assign var=ap5_isSelected value=count($packProduct['productCombinationsWhiteList']) && in_array($productCombination['id_product_attribute'], $packProduct['productCombinationsWhiteList'])}
					{assign var=ap5_combinationAvailableQuantity value=StockAvailable::getQuantityAvailableByProduct($packProduct['productObj']->id, $productCombination['id_product_attribute'])|intval}
					{assign var=ap5_idProductAttribute value=$productCombination['id_product_attribute']|escape:'html':'UTF-8'}
					{if isset($packProduct['combinationsInformations']) && isset($packProduct['combinationsInformations'][$productCombination['id_product_attribute']])}
						{assign var=ap5_combinationReductionAmount value=$packProduct['combinationsInformations'][$productCombination['id_product_attribute']]['reduction_amount']}
						{assign var=ap5_combinationReductionType value=$packProduct['combinationsInformations'][$productCombination['id_product_attribute']]['reduction_type']}
					{else}
						{assign var=ap5_combinationReductionAmount value=0}
						{assign var=ap5_combinationReductionType value='percentage'}
					{/if}
				<tr id="ap5_combination-{$idProductPack}-{$ap5_idProductAttribute}" class="nodrag nodrop{if $ap5_isDefaultCombination} bg-info text-white{/if}">
					<td class="text-center" width="35">
						<input type="checkbox"{if $ap5_isSelected || !count($packProduct['productCombinationsWhiteList'])} checked="checked"{/if} value="{$ap5_idProductAttribute}" id="ap5_combinationInclude-{$idProductPack}-{$ap5_idProductAttribute}" name="ap5_combinationInclude-{$idProductPack}[]" class="ap5_combinationInclude" data-id-product-pack="{$idProductPack}" data-id-product-attribute="{$ap5_idProductAttribute}" />
					</td>
					<td class="text-center">
						<input type="radio"{if $ap5_isDefaultCombination} checked="checked"{else if !$ap5_isSelected && count($packProduct['productCombinationsWhiteList'])} disabled="disabled" {/if} value="{$ap5_idProductAttribute}" id="ap5_defaultCombination-{$idProductPack}_{$ap5_idProductAttribute}" name="ap5_defaultCombination-{$idProductPack}" class="ap5_defaultCombination" data-id-product-pack="{$idProductPack}" data-id-product-attribute="{$ap5_idProductAttribute}" />
					</td>
					<td>{$productCombination['attribute_designation']|escape:'html':'UTF-8'}</td>
					<td>{convertPrice price=$productCombination['price']}</td>
					<td class="ap5_discountCell{if !isset($hasDiscountOnCombinations[$idProductPackKey]) || !$hasDiscountOnCombinations[$idProductPackKey]} ap5_inputDisabled{/if}" data-id-product-pack="{$idProductPack}">
						<input min="0" class="ap5_reductionAmount form-control" type="text" required="required" onchange="this.value = this.value.replace(/,/g, '.');" value="{if $ap5_combinationReductionType == 'percentage'}{toolsConvertPrice price=$ap5_combinationReductionAmount*100}{else}{toolsConvertPrice price=$ap5_combinationReductionAmount}{/if}" size="2" id="form_ap5_combinationReductionAmount-{$idProductPack|escape:'html':'UTF-8'}-{$ap5_idProductAttribute}" name="ap5_combinationReductionAmount-{$idProductPack|escape:'html':'UTF-8'}-{$ap5_idProductAttribute}" maxlength="14" /><select class="ap5_combinationReductionType form-control" name="ap5_combinationReductionType-{$idProductPack|escape:'html':'UTF-8'}-{$ap5_idProductAttribute}" id="ap5_combinationReductionType-{$idProductPack|escape:'html':'UTF-8'}-{$ap5_idProductAttribute}">
							<option value="percentage"{if $ap5_combinationReductionType == 'percentage'} selected="selected"{/if}>%</option>
							<option value="amount"{if $ap5_combinationReductionType == 'amount'} selected="selected"{/if}>{$defaultCurrency->sign|escape:'html':'UTF-8'}</option>
						</select>
					</td>
					<td>
						{if !empty($productCombination['reference']) && !empty($productCombination['ean13'])}
							{$productCombination['reference']|escape:'html':'UTF-8'} - {$productCombination['ean13']|escape:'html':'UTF-8'}
						{else if !empty($productCombination['reference']) && empty($productCombination['ean13'])}
							{$productCombination['reference']|escape:'html':'UTF-8'}
						{else if empty($productCombination['reference']) && !empty($productCombination['ean13'])}
							{$productCombination['ean13']|escape:'html':'UTF-8'}
						{else}
							N/A
						{/if}
					</td>
					<td class="text-center">
						{if $ap5_combinationAvailableQuantity <= 10}<span class="badge badge-{if $ap5_combinationAvailableQuantity <= 5}danger{else}warning{/if}">{/if}
						{$ap5_combinationAvailableQuantity|intval}
						{if $ap5_combinationAvailableQuantity <= 10}</span{/if}
					</td>
				</tr>
				{/foreach}
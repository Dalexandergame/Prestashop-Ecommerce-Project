				{foreach from=$packProduct['productCustomizationFields'] item='productCustomizationField'}
					{assign var=ap5_isSelected value=count($packProduct['productCustomizationFieldsWhiteList']) && in_array($productCustomizationField['id_customization_field'], $packProduct['productCustomizationFieldsWhiteList'])}
				<tr id="ap5_combination-{$idProductPack}-{$productCustomizationField['id_customization_field']|intval}" class="nodrag nodrop">
					<td class="center" width="35">
						{if $productCustomizationField['required'] == 1}
							<input type="hidden" value="{$productCustomizationField['id_customization_field']|intval}" id="ap5_customizationFieldInclude-{$idProductPack}-{$productCustomizationField['id_customization_field']|intval}" name="ap5_customizationFieldInclude-{$idProductPack}[]" class="ap5_customizationFieldsInclude" data-id-product-pack="{$idProductPack}" data-id-customization-field="{$productCustomizationField['id_customization_field']|intval}" />
						{else}
							<input type="checkbox"{if $ap5_isSelected || !count($packProduct['productCustomizationFieldsWhiteList'])} checked="checked"{/if} value="{$productCustomizationField['id_customization_field']|intval}" id="ap5_customizationFieldInclude-{$idProductPack}-{$productCustomizationField['id_customization_field']|intval}" name="ap5_customizationFieldInclude-{$idProductPack}[]" class="ap5_customizationFieldsInclude" data-id-product-pack="{$idProductPack}" data-id-customization-field="{$productCustomizationField['id_customization_field']|intval}" />
						{/if}
					</td>
					<td>{$productCustomizationField['name']|escape:'html':'UTF-8'}</td>
					<td>{if $productCustomizationField['required'] == 1}{l s='Yes' mod='pm_advancedpack'}{else}{l s='No' mod='pm_advancedpack'}{/if}</td>
				</tr>
				{/foreach}
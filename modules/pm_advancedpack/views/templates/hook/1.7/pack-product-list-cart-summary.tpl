{*
Available fields :
$packProduct['id_pack'] 			-> id_product of the pack
$packProduct['id_product']			-> id_product of a product of the pack
$packProduct['product_name']		-> product name of a product of the pack
$packProduct['attributes']			-> list of product attributes for a product of the pack (group name + group values)
$packProduct['attributes_small']	-> list of product attributes for a product of the pack (group values)
$packProduct['quantity']			-> product quantity of a product of the pack
$packProduct['reduction_amount']	-> product reduction amount or percentage (depends of reduction_type) of a product of the pack
$packProduct['reduction_type']		-> product reduction type (amount or percentage) of a product of the pack
$packProduct['customization_infos']	-> product customization infos (list of id_customization_field => value)
*}

<ul class="ap5_pack_product_list ap5_pack_product_list_cart_summary">
{foreach from=$packProducts item='packProduct'}
	<li>
		{$packProduct['quantity']}x {$packProduct['product_name']|escape:'htmlall':'UTF-8'}
		{if isset($packProduct['attributes']) && !empty($packProduct['attributes'])}<br /><em>{$packProduct['attributes']|escape:'htmlall':'UTF-8'}</em>{/if}
		{if isset($packProduct['customization_infos']) && is_array($packProduct['customization_infos']) && sizeof($packProduct['customization_infos'])}
			<br />
			{foreach from=$packProduct['customization_infos'] item='customizationValue' key='idCustomizationField'}
				{if isset($packProduct['customizationFieldsName'][$idCustomizationField])}
					<em>{$packProduct['customizationFieldsName'][$idCustomizationField]|escape:'htmlall':'UTF-8'}: {$customizationValue|escape:'htmlall':'UTF-8'}</em><br />
				{else}
					<em>{l s='Customization:' mod='pm_advancedpack'} {$customizationValue|escape:'htmlall':'UTF-8'}</em><br />
				{/if}
			{/foreach}
		{/if}
	</li>
{/foreach}
</ul>
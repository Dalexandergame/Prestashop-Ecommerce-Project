{* Available fields :
$packProduct['id_pack'] 			-> id_product of the pack
$packProduct['id_product']			-> id_product of a product of the pack
$packProduct['product_name']		-> product name of a product of the pack
$packProduct['attributes']			-> list of product attributes for a product of the pack (group name + group values)
$packProduct['attributes_small']	-> list of product attributes for a product of the pack (group values)
$packProduct['quantity']			-> product quantity of a product of the pack
$packProduct['reduction_amount']	-> product reduction amount or percentage (depends of reduction_type) of a product of the pack
$packProduct['reduction_type']		-> product reduction type (amount or percentage) of a product of the pack *}
{* WARNING : HTML is not allowed here *}
{foreach from=$packProducts item='packProduct' name='productLoop'}{$packProduct['quantity']}x {$packProduct['product_name']|escape:'htmlall':'UTF-8'}{if isset($packProduct['attributes']) && !empty($packProduct['attributes'])} - {$packProduct['attributes']|escape:'htmlall':'UTF-8'}{/if}{if !$smarty.foreach.productLoop.last} - {/if}{/foreach}
{* WARNING : HTML is not allowed here *}
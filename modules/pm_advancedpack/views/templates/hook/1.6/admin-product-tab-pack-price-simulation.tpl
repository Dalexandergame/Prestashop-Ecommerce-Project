			<div id="ap5-admin-pack-price-simulation">
				<table class="table">
					<thead>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th class="text-right"><strong>{l s='Without taxes' mod='pm_advancedpack'}</strong></th>
						<th class="text-right"><strong>{l s='With taxes' mod='pm_advancedpack'}</strong></th>
					</thead>
					<tbody>
						<tr>
							<td>{l s='Original pack price:' mod='pm_advancedpack'}</td>
							<td>&nbsp;</td>
							<td class="text-right"><span class="badge">{convertPrice price=$packClassicPrice}</span></td>
							<td class="text-right"><span class="badge">{convertPrice price=$packClassicPriceWt}</span></td>
						</tr>
						<tr>
							<td>{l s='Discounts:' mod='pm_advancedpack'}</td>
							<td class="center">{$discountPercentage} %</td>
							<td class="text-right"><span class="badge badge-{if $packPrice <= $packClassicPrice}warning{else}danger{/if}">{if $packPrice <= $packClassicPrice}{convertPrice price=($packPrice-$packClassicPrice)}{else}{convertPrice price=0}{/if}</span></td>
							<td class="text-right"><span class="badge badge-{if $packPrice <= $packClassicPrice}warning{else}danger{/if}">{if $packPriceWt <= $packClassicPriceWt}{convertPrice price=($packPriceWt-$packClassicPriceWt)}{else}{convertPrice price=0}{/if}</span></td>
						</tr>
						{if $totalPackEcoTax > 0}
						<tr>
							<td>{l s='Included green tax:' mod='pm_advancedpack'}</td>
							<td>&nbsp;</td>
							<td class="text-right"><span class="badge badge-danger">{convertPrice price=$totalPackEcoTax}</span></td>
							<td class="text-right"><span class="badge badge-danger">{convertPrice price=$totalPackEcoTaxWt}</span></td>
						</tr>
						{/if}
						<tr>
							<td><strong>{l s='Final pack price:' mod='pm_advancedpack'}</strong></td>
							<td>&nbsp;</td>
							<td class="text-right"><span class="badge badge-{if $packPrice <= $packClassicPrice && $packPrice > 0}success{else}danger{/if}">{convertPrice price=$packPrice}</span></td>
							<td class="text-right"><span class="badge badge-{if $packPrice <= $packClassicPrice && $packPriceWt > 0}success{else}danger{/if}">{convertPrice price=$packPriceWt}</span></td>
						</tr>
					</tbody>
				</table>
			</div>
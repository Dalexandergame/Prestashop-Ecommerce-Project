{if version_compare($smarty.const._PS_VERSION_, '1.7.3.0', '>=')}
	{assign var='originalPriceSimulationCssClasses' value='badge-pill badge-info'}
	{assign var='discountsSimulationCssClasses' value='badge-pill badge-warning'}
	{assign var='finalPriceSimulationCssClasses' value='badge-pill badge-success'}
	{assign var='priceErrorCssClasses' value='badge-pill badge-danger'}
{else}
	{assign var='originalPriceSimulationCssClasses' value='text-info bg-info'}
	{assign var='discountsSimulationCssClasses' value='text-warning bg-warning'}
	{assign var='finalPriceSimulationCssClasses' value='bg-success text-success'}
	{assign var='priceErrorCssClasses' value='text-danger bg-danger'}
{/if}
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
							<td class="text-right"><span class="{$originalPriceSimulationCssClasses} img-rounded rounded p-x-1">{convertPrice price=$packClassicPrice}</span></td>
							<td class="text-right"><span class="{$originalPriceSimulationCssClasses} img-rounded rounded p-x-1">{convertPrice price=$packClassicPriceWt}</span></td>
						</tr>
						<tr>
							<td>{l s='Discounts:' mod='pm_advancedpack'}</td>
							<td class="text-center">{$discountPercentage} %</td>
							<td class="text-right"><span class="{if $packPrice <= $packClassicPrice}{$discountsSimulationCssClasses}{else}{$priceErrorCssClasses}{/if} img-rounded rounded p-x-1">{if $packPrice <= $packClassicPrice}{convertPrice price=($packPrice-$packClassicPrice)}{else}{convertPrice price=0}{/if}</span></td>
							<td class="text-right"><span class="{if $packPrice <= $packClassicPrice}{$discountsSimulationCssClasses}{else}{$priceErrorCssClasses}{/if} img-rounded rounded p-x-1">{if $packPriceWt <= $packClassicPriceWt}{convertPrice price=($packPriceWt-$packClassicPriceWt)}{else}{convertPrice price=0}{/if}</span></td>
						</tr>
						{if $totalPackEcoTax > 0}
						<tr>
							<td>{l s='Included green tax:' mod='pm_advancedpack'}</td>
							<td>&nbsp;</td>
							<td class="text-right"><span class="{$priceErrorCssClasses} img-rounded rounded p-x-1">{convertPrice price=$totalPackEcoTax}</span></td>
							<td class="text-right"><span class="{$priceErrorCssClasses} img-rounded rounded p-x-1">{convertPrice price=$totalPackEcoTaxWt}</span></td>
						</tr>
						{/if}
						<tr>
							<td><strong>{l s='Final pack price:' mod='pm_advancedpack'}</strong></td>
							<td>&nbsp;</td>
							<td class="text-right"><span class="{if $packPrice <= $packClassicPrice && $packPrice > 0}{$finalPriceSimulationCssClasses}{else}{$priceErrorCssClasses}{/if} img-rounded rounded p-x-1">{convertPrice price=$totalFinal}</span></td>
							<td class="text-right"><span class="{if $packPrice <= $packClassicPrice && $packPriceWt > 0}{$finalPriceSimulationCssClasses}{else}{$priceErrorCssClasses}{/if} img-rounded rounded p-x-1">{convertPrice price=$totalFinalWt}</span></td>
						</tr>
					</tbody>
				</table>
			</div>

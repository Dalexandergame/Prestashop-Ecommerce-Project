{if isset($displayWeekList) and isset($weekListGroupByDay)}
    <h2>{l s='Les retours pour' mod='planningdeliverybycarrier'} <span class="dateLivraison"></span></h2>

	{foreach $weekListGroupByDay AS $day => $deliveriesGroupBySlot}
    <div id="table_info_large">
		<h5><strong>{$day|escape:'htmlall'}</strong></h5>
			{foreach $deliveriesGroupBySlot as $slot => $deliveries}
            <div id="{cycle values='order_line1, '}">
            <div class="listing-date">
                {if $slot != ''}{$slot|escape:'htmlall'} ({count($deliveries)}){/if}
             </div> <div class="clear"></div>
					{foreach $deliveries as $delivery}

					<div class="livraison-listing-planning">
						<ul style="background-color:{$delivery.oscolor|escape:'htmlall'}">
								<li>
									<p><b>{$delivery.customer|escape:'htmlall'}</b></p>
                                    <span class="color_field">{$delivery.osname|escape:'htmlall'}</span> <br />
									<p>{l s='Carrier' mod='planningdeliverybycarrier'} : <b>{if $delivery.carriername != '0'}{$delivery.carriername|escape:'htmlall'}{else}{$PS_SHOP_NAME|escape:'htmlall'}{/if}</b></p>
									<a target="_blank" href="index.php?tab=adminorders&id_order={$delivery.id_order|escape:'intval'}&view{$table|escape:'htmlall'}&token={$orderToken|escape:'htmlall'}">
										<img src="../img/admin/details.gif" alt="{l s='View' mod='planningdeliverybycarrier'}" title="{l s='View' mod='planningdeliverybycarrier'}" />
									</a>
								</li>
						</ul>
					</div>

                    {/foreach}
                    <div class="clear"></div>
		</div>
			{/foreach}

    </div>
	{/foreach}

{/if}
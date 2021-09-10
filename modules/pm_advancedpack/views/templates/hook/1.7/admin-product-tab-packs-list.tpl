<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12 col-12">
			<div id="product-packs" class="product-tab">
				<h3>{l s='Pack list with:' mod='pm_advancedpack'} {$currentProduct->name|escape:'html':'UTF-8'}</h3>

				<div id="ap5-pack-list" class="row">
					<div class="col-lg-3 col-sm-4 col-xs-6 col-6">
						<div id="ap5-pack-row-new" class="ap5-pack-row text-center">
							<a class="btn btn-primary text-center" href="{$createNewPackUrl nofilter}&source_id_product={$currentProduct->id|intval}"><i class="material-icons">add_circle</i><br /><br />{l s='Create a new pack from this product' mod='pm_advancedpack'}</a>
						</div>
					</div>
			{if $packList|count}
				{foreach from=$packList item='packContent' name='packLoop' key='idPack'}
					{assign var=packPrice value=AdvancedPack::getPackPrice($idPack, false)}
					{assign var=packOldPrice value=AdvancedPack::getPackPrice($idPack, false, false)}
					<div class="col-lg-3 col-sm-4 col-xs-6 col-6">
						<div id="ap5-pack-row-{$idPack|intval}" class="ap5-pack-row card">
							<div class="ap5-pack-name card-header">
								<a target="_blank" href="{$packObjects[$idPack]->urlAdminProduct nofilter}">
									{$packObjects[$idPack]->name|escape:'html':'UTF-8'}
									&nbsp;<i class="material-icons">mode_edit</i>
								</a>
								</span>
							</div>
							<div class="card-body p-a-1">
								{assign var=imageCoverId value=Product::getCover($idPack)}
								<div class="ap5-pack-image text-center">
									<a target="_blank" href="{$packObjects[$idPack]->urlAdminProduct nofilter}">
										{AdvancedPackCoreClass::getThumbnailImageHTML($idPack, $imageCoverId.id_image)}
									</a>
								</div>
								<div class="ap5-pack-action-buttons"></div>
								<div class="ap5-pack-content">
									<p>{l s='Pack content:' mod='pm_advancedpack'}</p>
									<ul class="list-unstyled">
								{foreach from=$packContent item='packProduct' name='packProductLoop'}
									<li{if $currentProduct->id == $packProduct.presentation.id_product}class="ap5-current-product"{/if}>
										{$packProduct.quantity|intval}x <a target="_blank" href="{$packProduct.presentation.url}">{$packProduct.presentation.name}</a> ({$packProduct.presentation.reference})
									</li>
								{/foreach}
									</ul>
								</div>
								<hr />
								<div class="ap5-pack-price-container text-center">
									{if $packPrice != $packOldPrice}
										<strong class="ap5-pack-price">{convertPrice price=$packPrice}</strong> - <s class="ap5-pack-old-price">{convertPrice price=$packOldPrice}</s>
									{else}
										<strong class="ap5-pack-price">{convertPrice price=$packPrice}</strong>
									{/if}
								</div>
							</div>
						</div>
					</div>
				{/foreach}
			{/if}
				</div>
			</div>
		</div>
	</div>
</div>
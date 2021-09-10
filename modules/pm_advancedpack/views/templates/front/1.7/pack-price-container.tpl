{if !$priceDisplay || $priceDisplay == 2}
    {assign var='productPrice' value=AdvancedPack::getPackPrice($product.id, true, true, true, $priceDisplayPrecision, $packAttributesList, $packQuantityList, $packExcludeList, true)}
    {assign var='productPriceWithoutReduction' value=AdvancedPack::getPackPrice($product.id, true, false, true, $priceDisplayPrecision, $packAttributesList, $packQuantityList, $packExcludeList, true)}
{elseif $priceDisplay == 1}
    {assign var='productPrice' value=AdvancedPack::getPackPrice($product.id, false, true, true, $priceDisplayPrecision, $packAttributesList, $packQuantityList, $packExcludeList, true)}
    {assign var='productPriceWithoutReduction' value=AdvancedPack::getPackPrice($product.id, false, false, true, $priceDisplayPrecision, $packAttributesList, $packQuantityList, $packExcludeList, true)}
{/if}
{assign var='packReductionAmount' value=$productPriceWithoutReduction-$productPrice}

<div id="ap5-buy-block-container" class="{if empty($packDisplayModeSimple)}{if empty($from_quickview)}col-xs-12 col-12 col-sm-4 col-md-3{else}col-xs-12 col-12{/if}{/if}{if $packDeviceIsTablet || $packDeviceIsMobile} ap5-is-mobile{/if}{if (isset($productsPackErrors) && count($productsPackErrors)) || (isset($productsPackFatalErrors) && count($productsPackFatalErrors))} ap5-buy-block-with-errors{/if}">
    {if $product.show_price && !$configuration.is_catalog}
        <div class="product-actions">
            <!-- buy action and errors message -->
            <div id="ap5-buy-container" {if (!$product.allow_oosp && $product.quantity <= 0) || !$product.available_for_order || $configuration.is_catalog} class="unvisible"{/if}>
                {if isset($productsPackFatalErrors) && count($productsPackFatalErrors)}
                    <p class="ap5-pack-unavailable animated shake alert alert-danger">
                        <span>{l s='One of product is no longer available. This pack can\t be purchased' mod='pm_advancedpack'}</span>
                    </p>
                {else if isset($productsPackErrors) && count($productsPackErrors)}
                    <p class="ap5-combination-unavailable animated flash alert alert-warning">
                        <span><a href="#ap5-pack-product-{current(array_keys($productsPackErrors))|intval}">{l s='One of product combination is no longer available. Please select another attribute to this product' mod='pm_advancedpack'}</a></span>
                    </p>
                {else}
                    {* add to cart form *}
                    <form class="ap5-buy-block{if $configuration.is_catalog && $product.quantity > 0} hidden{/if}{if !empty($from_quickview)} ap5-from-modal{/if}" action="{pm_advancedpack::getPackAddCartURL($product.id)}" method="post">
                        <input type="hidden" name="token" value="{$static_token}" />
                        <input type="hidden" name="id_product" value="{$product.id|intval}" id="product_page_product_id" />
                        <input type="hidden" name="add" value="1" />
                        <input type="hidden" name="id_product_attribute" id="idCombination" value="" />
                        {if !empty($from_quickview)}
                            <div class="row">
                                {block name='ap5_price_container'}
                                    <div id="ap5-price-container" class="{if !empty($from_quickview)} col-xs-6 col-6{/if}">
                                        {include file='catalog/_partials/product-prices.tpl'}
                                    </div>
                                {/block}

                                {block name='ap5_add_to_cart_container'}
                                    <div class="ap5-add-to-cart-container{if !empty($from_quickview)} col-xs-6 col-6{/if}">
                                        {include file='catalog/_partials/product-add-to-cart.tpl'}
                                    </div>
                                {/block}
                            </div>
                        {else}
                            {block name='ap5_price_container'}
                                <div id="ap5-price-container">
                                    {include file='catalog/_partials/product-prices.tpl'}
                                </div>
                            {/block}

                            {block name='ap5_add_to_cart_container'}
                                <div class="ap5-add-to-cart-container">
                                    {include file='catalog/_partials/product-add-to-cart.tpl'}
                                </div>
                            {/block}
                            {include file='catalog/_partials/product-additional-info.tpl'}
                        {/if}
                    </form>
                {/if}
            </div>
        </div>
    {/if}
</div>
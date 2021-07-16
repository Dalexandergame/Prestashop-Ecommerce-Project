{*
 * This file is part of the prestahsop-adroll module.
 *
 * (c) AdRoll
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Damián Nohales <damian.nohales@adroll.com>
 * @copyright AdRoll
 * @license   https://opensource.org/licenses/MIT The MIT License
 *}
<script data-adroll="prestashop-adroll-pixel" type="text/javascript">
    var prestashopAdrollPixelGuard = "prestashop-adroll-pixel-guard";
{if $adroll_advertisable_id && $adroll_pixel_id }

    {if isset($adroll_current_page)}
        adroll_current_page = "{$adroll_current_page|escape:'htmlall':'UTF-8'}";
    {/if}

    adroll_product_group = "{$adroll_product_group|escape:'htmlall':'UTF-8'}";

    {if isset($adroll_customer->email)}
        adroll_email = "{$adroll_customer->email|md5|escape:'htmlall':'UTF-8'}";
    {/if}

    {if isset($adroll_customer->id)}
        adroll_user_id = "{$adroll_customer->id|escape:'htmlall':'UTF-8'}";
    {/if}

    {if isset($adroll_product)}
        {literal}adroll_product = {{/literal}

            {literal}"price":{/literal} "{$adroll_product->getPrice(true, $smarty.const.NULL, 2)|escape:'htmlall':'UTF-8'}",
            {literal}"product_id":{/literal} "{$adroll_product->id|escape:'htmlall':'UTF-8'}",
            {literal}"category":{/literal} "{$adroll_product->category|escape:'htmlall':'UTF-8'}",
            {literal}"product_group": adroll_product_group{/literal}

        {literal}};{/literal}
    {/if}

    {if isset($adroll_order)}
        adroll_conversion_value = "{$adroll_order->total_paid|escape:'htmlall':'UTF-8'}";
        adroll_order_id = "{$adroll_order->id|escape:'htmlall':'UTF-8'}";
        {if isset($adroll_order_currency)}
            adroll_order_currency = "{$adroll_order_currency->iso_code|escape:'htmlall':'UTF-8'}";
        {/if}
        adroll_checkout_products = [
            {foreach from=$adroll_order->getProducts() item=product name=checkout_products}
                {literal}{"product_id":{/literal} "{$product['product_id']|escape:'htmlall':'UTF-8'}",
                {literal}"quantity":{/literal} "{$product['product_quantity']|escape:'htmlall':'UTF-8'}",
                {literal}"product_group": adroll_product_group,{/literal}
                {literal}"price":{/literal} "{$product['unit_price_tax_incl']|escape:'htmlall':'UTF-8'}"{literal}}{/literal}{if not $smarty.foreach.checkout_products.last},{/if}

            {/foreach}
        ];
    {/if}

    {if isset($cart_obj) && !empty($cart_obj->getProducts())}
        adroll_cart_products = [
            {foreach from=$cart_obj->getProducts() item=product name=cart_products}
                {literal}{"product_id":{/literal} "{$product['id_product']|escape:'htmlall':'UTF-8'}",
                {literal}"quantity":{/literal} "{$product['cart_quantity']|escape:'htmlall':'UTF-8'}",
                {literal}"price":{/literal} "{$product['price_with_reduction']|escape:'htmlall':'UTF-8'}",
                {literal}"product_group": adroll_product_group,{/literal}
                {literal}"category":{/literal} "{$product['category']|escape:'htmlall':'UTF-8'}"{literal}}{/literal}{if not $smarty.foreach.cart_products.last},{/if}
            {/foreach}
        ];
    {/if}

    {if isset($search_string)}
        adroll_search_string = "{$search_string|escape:'htmlall':'UTF-8'}";
    {/if}


    {if isset($adroll_currency)}
        adroll_currency = "{$adroll_currency|escape:'htmlall':'UTF-8'}";
    {/if}


    {if isset($adroll_language_code)}
        adroll_language_code = "{$adroll_language_code|escape:'htmlall':'UTF-8'}";
    {/if}


    adroll_adv_id = "{$adroll_advertisable_id|escape:'htmlall':'UTF-8'}";
    adroll_pix_id = "{$adroll_pixel_id|escape:'htmlall':'UTF-8'}";
    {literal}
    adroll_version = "2.0";
    (function(w,d,e,o,a){
        w.__adroll_loaded=true;
        w.adroll=w.adroll||[];
        w.adroll.f=['setProperties','identify','track'];
        var roundtripUrl="https://s.adroll.com/j/" + adroll_adv_id + "/roundtrip.js";
        for(a=0;a<w.adroll.f.length;a++){
            w.adroll[w.adroll.f[a]]=w.adroll[w.adroll.f[a]]||(function(n){return function(){w.adroll.push([n,arguments])}})(w.adroll.f[a])};e=d.createElement('script');o=d.getElementsByTagName('script')[0];e.async=1;e.src=roundtripUrl;o.parentNode.insertBefore(e, o);})(window,document);
    {/literal}
{/if}
</script>

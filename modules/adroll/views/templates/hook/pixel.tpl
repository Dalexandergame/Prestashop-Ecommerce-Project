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
    adroll_adv_id = "{$adroll_advertisable_id|escape:'htmlall':'UTF-8'}";
    adroll_pix_id = "{$adroll_pixel_id|escape:'htmlall':'UTF-8'}";
    {if isset($adroll_customer->email)}
        adroll_email = "{$adroll_customer->email|md5|escape:'htmlall':'UTF-8'}";
    {/if}
    {if isset($adroll_segments)}
        adroll_segments = "{$adroll_segments|escape:'htmlall':'UTF-8'}";
    {/if}

    {if isset($adroll_product)}
        adroll_product_id = "{$adroll_product->id|escape:'htmlall':'UTF-8'}";
    {/if}
    adroll_product_group = "{$adroll_product_group|escape:'htmlall':'UTF-8'}";

    {if isset($adroll_order)}
        adroll_conversion_value = "{$adroll_order->total_paid|escape:'htmlall':'UTF-8'}";
        adroll_currency = "{$adroll_currency_iso_code|escape:'htmlall':'UTF-8'}";
    {/if}

    adroll_custom_data = {
        {if isset($adroll_order)}
            ORDER_ID: "{$adroll_order->id|escape:'htmlall':'UTF-8'}",
        {/if}
        {if isset($adroll_customer->id)}
            USER_ID: "{$adroll_customer->id|escape:'htmlall':'UTF-8'}"
        {/if}
    };

    {if isset($adroll_order)}
        adroll_checkout_product_ids = [
        {foreach from=$adroll_order->getProducts() item=product}
            {$product['product_id']|escape:'htmlall':'UTF-8'},
        {/foreach}
        ];
    {/if}

    {literal}
    (function () {
        var _onload = function(){
            if (document.readyState && !/loaded|complete/.test(document.readyState)){setTimeout(_onload, 10);return}
            if (!window.__adroll_loaded){__adroll_loaded=true;setTimeout(_onload, 50);return}
            var scr = document.createElement("script");
            var host = "//s.adroll.com";
            scr.setAttribute('async', 'true');
            scr.type = "text/javascript";
            scr.src = host + "/j/roundtrip.js";
            ((document.getElementsByTagName('head') || [null])[0] ||
                document.getElementsByTagName('script')[0].parentNode).appendChild(scr);
        };
        if (window.addEventListener) {window.addEventListener('load', _onload, false);}
        else {window.attachEvent('onload', _onload)}
    }());
    {/literal}
{/if}
</script>

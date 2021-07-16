{*
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@buy-addons.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    Buy-addons    <contact@buy-addons.com>
* @copyright 2007-2020 Buy-addons
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script>
	{literal}
	(
		function(i,s,o,g,r,a,m)
		{
				i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
						(i[r].q=i[r].q||[]).push(arguments)
					},
				i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		}
	)(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	{/literal}
	ga('create', '{$baIdAnalytics|escape:'htmlall':'UTF-8'}', 'auto');
	ga('send', 'pageview');
	ga('require', 'ecommerce');
	ga('ecommerce:addTransaction', {
		'id': '{$idOrder|escape:'htmlall':'UTF-8'}',
		'affiliation': '{$PS_SHOP_NAME|escape:'htmlall':'UTF-8'}',
		'revenue': '{$order->total_paid_tax_incl|escape:'htmlall':'UTF-8'}',
		'shipping': '{$order->total_shipping_tax_incl|escape:'htmlall':'UTF-8'}',
		'tax': '{$totalTax|escape:'htmlall':'UTF-8'}',
		'currency': '{$currency_iso_code|escape:'htmlall':'UTF-8'}'  // local currency code.
	});
	{foreach $productList as $product}
	{assign var="sku" value="EAN_"|cat:$product.product_ean13}
	{if $product.product_upc != ""}
		{assign var="sku" value="UPC_"|cat:$product.product_upc}
	{elseif $product.product_reference != ""}
		{assign var="sku" value="REFERENCE_"|cat:$product.product_reference}
	{else}
		{assign var="sku" value="PRODUCT_ID_"|cat:$product.product_id}
	{/if}
	ga('ecommerce:addItem', {
		'id': '{$idOrder|escape:'htmlall':'UTF-8'}',
		'name': '{$product.product_name|escape:'htmlall':'UTF-8'}, Reference: {$product.product_reference|escape:'htmlall':'UTF-8'}',
		'sku': '{$sku|escape:'htmlall':'UTF-8'}',
		'category': '{$product.category_name|escape:'htmlall':'UTF-8'}',
		'price': '{$product.total_price_tax_incl|escape:'htmlall':'UTF-8'}',
		'quantity': '{$product.product_quantity|escape:'htmlall':'UTF-8'}',
		'currency': '{$currency_iso_code|escape:'htmlall':'UTF-8'}'
	});
	{/foreach}
	ga('ecommerce:send');
	ga('ecommerce:clear');
</script><br/>
<!-- Google Code for code1 Conversion Page -->
<script type="text/javascript">
	/* <![CDATA[ */
	var google_conversion_id = {$baIdAdwords|escape:'htmlall':'UTF-8'};
	var google_conversion_language = "{$language_iso_code|escape:'htmlall':'UTF-8'}";
	var google_conversion_format = "3";
	var google_conversion_color = "ffffff";
	var google_conversion_label = "{$baIdAdwordsLabel|escape:'htmlall':'UTF-8'}";
	var google_conversion_value = {$order->total_paid_tax_incl|escape:'htmlall':'UTF-8'};
	var google_conversion_currency = "{$currency_iso_code|escape:'htmlall':'UTF-8'}";
	var google_remarketing_only = false;
	/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
	<div style="display:inline;">
		<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/{$baIdAdwords|escape:'htmlall':'UTF-8'}/?value={$order->total_paid_tax_incl|escape:'htmlall':'UTF-8'}&amp;currency_code={$currency_iso_code|escape:'htmlall':'UTF-8'}&amp;label={$baIdAdwordsLabel|escape:'htmlall':'UTF-8'}&amp;guid=ON&amp;script=0"/>
	</div>
</noscript>

<!-- Facebook Pixel Code -->
<script>
  {literal}
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  {/literal}
  fbq('init', '{$facebookId|escape:"htmlall":"UTF-8"}');
  fbq('track', 'Purchase');
</script>
<noscript><img height="1" width="1" style="display:none"
  src="https://www.facebook.com/tr?id={$facebookId|escape:"htmlall":"UTF-8"}&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel Code -->

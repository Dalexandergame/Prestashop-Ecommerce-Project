/**
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
*/
jQuery(document).ready(function(){
	jQuery('#btnTestYourCodeAdword').click(function(){
		console.log('aaaaa');
		var idAdword = jQuery('#idAdword').val();
		var LabelAdword = jQuery('#LabelAdword').val();
		var languageDefault = jQuery('#languageDefault').val();
		var currencyDefault = jQuery('#currencyDefault').val();
		
		var codeAword = encodeHtmlEntity('<!-- Google Code for code1 Conversion Page -->')+'<br />';
		codeAword+=encodeHtmlEntity('<script type="text/javascript">')+'<br />';
		codeAword+=encodeHtmlEntity('/* <![CDATA[ */')+'<br />';
		codeAword+='var google_conversion_id = '+idAdword+';<br />';
		codeAword+='var google_conversion_language = "'+languageDefault+'";<br />';
		codeAword+='var google_conversion_format = "3";<br />';
		codeAword+='var google_conversion_color = "ffffff";<br />';
		codeAword+='var google_conversion_label = "'+LabelAdword+'";<br />';
		codeAword+='var google_conversion_value = 1.00;<br/>';
		codeAword+='var google_conversion_currency = "'+currencyDefault+'";<br />';
		codeAword+='var google_remarketing_only = false;<br />';
		codeAword+='/* ]]> */<br/>';
		codeAword+=encodeHtmlEntity('</script>')+'<br />';
		codeAword+=encodeHtmlEntity('<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">');
		codeAword+=encodeHtmlEntity('</script>')+'<br />';
		codeAword+=encodeHtmlEntity('<noscript>')+'<br />';
		codeAword+=encodeHtmlEntity('<div style="display:inline;">')+'<br />';
		codeAword+=encodeHtmlEntity('<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/'+idAdword+'/?value=1.00&amp;currency_code='+currencyDefault+'&amp;label='+LabelAdword+'&amp;guid=ON&amp;script=0"/>')+'<br />';
		codeAword+=encodeHtmlEntity('</div>')+'<br />';
		codeAword+=encodeHtmlEntity('</noscript>');
		jQuery('#wrapperViewCodeAdword').show();
		jQuery('#viewCodeAdword').html(codeAword);
		
	});
	
	jQuery('#btnTestYourCodeFacebook').click(function(){
		var idFacebook = jQuery('#facebookId').val();
		var codeFacebook = encodeHtmlEntity("<!-- Facebook Pixel Code -->")+"<br/>";
		codeFacebook += encodeHtmlEntity("<script>")+"<br/>";
		codeFacebook += encodeHtmlEntity("  !function(f,b,e,v,n,t,s)")+"<br/>";
		codeFacebook += encodeHtmlEntity("  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?")+"<br/>";
		codeFacebook += encodeHtmlEntity("  n.callMethod.apply(n,arguments):n.queue.push(arguments)};")+"<br/>";
		codeFacebook += encodeHtmlEntity("  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';")+"<br/>";
		codeFacebook += encodeHtmlEntity("  n.queue=[];t=b.createElement(e);t.async=!0;")+"<br/>";
		codeFacebook += encodeHtmlEntity("  t.src=v;s=b.getElementsByTagName(e)[0];")+"<br/>";
		codeFacebook += encodeHtmlEntity("  s.parentNode.insertBefore(t,s)}(window, document,'script',")+"<br/>";
		codeFacebook += encodeHtmlEntity("  'https://connect.facebook.net/en_US/fbevents.js');")+"<br/>";
		codeFacebook += encodeHtmlEntity("  fbq('init', '"+idFacebook+"');")+"<br/>";
		codeFacebook += encodeHtmlEntity("  fbq('track', 'PageView');")+"<br/>";
		codeFacebook += encodeHtmlEntity("</script>")+"<br/>";
		codeFacebook += encodeHtmlEntity('<noscript><img height="1" width="1" style="display:none"  src="https://www.facebook.com/tr?id='+idFacebook+'&ev=PageView&noscript=1"/></noscript>')+"<br/>";
		codeFacebook += encodeHtmlEntity('<!-- End Facebook Pixel Code -->');

		jQuery('#wrapperViewCodeFacebook').show();
		jQuery('#viewCodeFacebook').html(codeFacebook);
	});
});

var encodeHtmlEntity = function(str) {
	var buf = [];
	for (var i=str.length-1;i>=0;i--) {
		buf.unshift(['&#', str[i].charCodeAt(), ';'].join(''));
	}
	return buf.join('');
};
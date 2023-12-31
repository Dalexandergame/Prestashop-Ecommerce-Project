/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$(document).ready(function(){
	$('#home-page-tabs li:first, #index .tab-content ul:first').addClass('active');
	//$( '#steps' ).insertBefore( '#video_eco' );	
	var srcImg = $('#eco1 img').data('int');
	$('#eco1 img').attr('src',srcImg)


	var quotes = $(".quote");
	var quoteIndex = -1;

	function showQuote(change)
	{
		quoteIndex += change;
		if (quoteIndex < 0)
		{
			quoteIndex += quotes.length;
		}
		else if (quoteIndex >= quotes.length)
		{
			quoteIndex -= quotes.length;
		}
		quotes.stop(true, true).hide().eq(quoteIndex)
			.fadeIn(2000)
			.delay(2000)
			.fadeOut(2000)
			.queue(function() { showQuote(1); });
	}
	showQuote(1);

	$('.before-text').on('click', function()
	{
		showQuote(-1);
	});

	$('.next-text').on('click', function()
	{
		showQuote(1);
	});
});

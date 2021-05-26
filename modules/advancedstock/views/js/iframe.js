/*
 * *
 *  2007-2018 PrestaShop
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Academic Free License (AFL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/afl-3.0.php
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *   @author    PrestaShop SA <contact@prestashop.com>
 *   @copyright 2007-2018 PrestaShop SA
 *   @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *   International Registered Trademark & Property of PrestaShop SA
 * /
 */

function resizeIframe(obj,addSize) {
	jQuery(document).ready(function($) {
		addSize = parseFloat((addSize ? addSize : 0));

	   $(obj).animate({height: (parseFloat(obj.contentWindow.document.scrollingElement.scrollHeight) + addSize ) + 'px'}, 500);
    });
}

function checkoutIframe(obj) {
	// pour sortir du frame
	$('.table tbody td.pointer').each(function(){
		
		if($(this).attr('onclick') && $(this).attr('onclick').indexOf('document.location') !==-1){
			$(this).attr('onclick',$(this).attr('onclick').replace('document.location','parent.document.location'));	
		}
		 
	});
	$('.table.supplier tbody td a.edit.btn').attr('target','_parent');
	
	$(".fancybox").click(function(e){
		e.preventDefault();
		
	 	parent.$.fancybox({width:'100%',href: this.href,type: 'iframe',scrolling:'auto',
	 	afterLoad : function() {

	 		 resizeIframe(this.content[0],100);
		    
 		}});
	 	
	});
}

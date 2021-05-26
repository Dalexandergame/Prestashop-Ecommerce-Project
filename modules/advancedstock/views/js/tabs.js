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

function initFormTabs(){
	/**
	 * Lorsque l'on click sur un tabPanel, le frame dont l'id est spécifié dans href  + 'Frame' et charger avec la page spécifié dans la propriete data-href (target)
	 * @exemple : <a href="#supply" data-href="http://google.fr"></a> : l'iframe id="#supplyFrame" -> scr = 'http://google.fr'
	 * 
	 */
	$('ul.nav.nav-tabs a').on('click', function(e) {
		try{
			
			var id = $(this).attr('href')+"Frame",
			 	target = $(this).attr('data-href');
			 	noRefresh = $(this).attr('data-noRefresh'),
			 	params = $(this).attr('href').substr(1)
			 ;
			addUrlAncor(params);
			
			if( $(this).closest('li').hasClass('active') || ($(id).attr('src') ==  target && noRefresh=='true')){						
				return;
			}
		    $(id).attr('src', target);
		    
		}catch(e){
			return e;
		}
	});
	var pos = location.href.indexOf("#tabActive");
	
	if(pos !=-1){
		var param=location.href.substr(pos+11);
		if(param) $("a[href='#" + param + "']").click();
	}
}
function initTabs(){
	
$( "#tabs").tabs();
	
	/**
	 * Lorsque l'on click sur un tabPanel, le frame dont l'id est spécifié dans href  + 'Frame' et charger avec la page spécifié dans la propriete data-href (target)
	 * @exemple : <a href="#supply" data-href="http://google.fr"></a> : l'iframe id="#supplyFrame" -> scr = 'http://google.fr'
	 * 
	 */
	$('.tabs a').on('click', function(e) {
		try{
			
			var id = $(this).attr('href')+"Frame",
			 	target = $(this).attr('data-href');
			 	noRefresh = $(this).attr('data-noRefresh'),
			 	params = $(this).attr('href').substr(1)
			 ;
			addUrlAncor(params);
			
			if($(id).attr('src') ==  target && noRefresh=='true'){
				
				return;
			}
		  	
		    $(id).attr('src', target);
		    
		}catch(e){
			return e;
		}
	});
	var pos = location.href.indexOf("#tabActive");
	
	if(pos !=-1){
		var param=location.href.substr(pos+11);
		if(param) $("a[href='#" + param + "']").click();
	}
}
function addUrlAncor(params){
	var href=  location.href,
		pos = href.indexOf("#tabActive")
		
	;
	if( pos != -1){
		location.href = href.substring(-1,(pos+1));	
	}else{
		location.href +="#";
	}
	location.href += "tabActive=" + params;
}

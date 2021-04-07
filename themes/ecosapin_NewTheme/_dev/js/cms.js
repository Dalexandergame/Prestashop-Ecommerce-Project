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
	if (typeof ad !== 'undefined' && ad && typeof adtoken !== 'undefined' && adtoken)
	{
		$(document).on('click', 'input[name=publish_button]', function(e){
			e.preventDefault();
			submitPublishCMS(ad, 0, adtoken);
		});
		$(document).on('click', 'input[name=lnk_view]', function(e){
			e.preventDefault();
			submitPublishCMS(ad, 1, adtoken);
		});
	}
        if($( "#answers" ).length)
            $( "#answers" ).accordion({active: false, collapsible: true, header: "> div > h3", heightStyle: "content"});
        $('.close-accord').bind('click', function(){
              $(this).closest('div').prev('h3').trigger('click');
              return false;
        });
		
	$('.item-partners:even').addClass('backFull');
	
	$('.links-partners[href^="#"]').click(function(){  
		var id = $(this).attr("href");
		var offset = $(id).offset().top - 100
		$('html, body').animate({scrollTop: offset}, 'slow'); 
		return false;  
	}); 
	
	$('.menu-page a[href^="#"]').click(function(){  
		var height1 = $('.header-container').height();
		var height2 = $('.menu-pager').height();
		var heightF = height1 + height2;
		var id = $(this).attr("href");
		$('.menu-page a').removeClass('active');
		$(this).addClass('active');
		if( $('#header').hasClass('header-fixed') ) {
			var offset = $(id).offset().top - 140;
		}else {
			var offset = $(id).offset().top - 350;
		}
		$('html, body').animate({scrollTop: offset}, 'slow'); 
		
		return false;  
	}); 
	
	$(window).scroll(function(){
      if ($(this).scrollTop() > 180) {
          $('.menu-page').addClass('menu-page-fixed');
      } else {
          $('.menu-page').removeClass('menu-page-fixed');
      }
  });


});

function submitPublishCMS(url, redirect, token)
{
	var id_cms = $('#admin-action-cms-id').val();

	$.ajaxSetup({async: false});
	$.post(url+'/index.php', { 
			action: 'PublishCMS',
			id_cms: id_cms, 
			status: 1, 
			redirect: redirect,
			ajax: 1,
			tab: 'AdminCmsContent',
			token: token
		},
		function(data)
		{
			if (data.indexOf('error') === -1)
				document.location.href = data;
		}
	);
	return true;
}

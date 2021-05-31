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
	
	// $(window).scroll(function(){
  //     if ($(this).scrollTop() > 180) {
  //         $('.menu-page').addClass('menu-page-fixed');
  //     } else {
  //         $('.menu-page').removeClass('menu-page-fixed');
  //     }
  // });


	$('page-cms-les-producteurs .respo-mobile.dots').html('<span class="dot-p active" id="dot1"></span>\n' +
		'        <span class="dot-p" id="dot2"></span>\n' +
		'        <span class="dot-p" id="dot3"></span>\n' +
		'        <span class="dot-p" id="dot4"></span>');

	$('page-cms-les-producteurs .row.equal').on('touchend',function () {
		var scroll_left_width = $('page-cms-les-producteurs .row.equal').scrollLeft();
		var div_width =$('page-cms-les-producteurs .row.equal').width();
		if (scroll_left_width < div_width - 10) {
			$('#dot1').addClass('active')
			$('#dot2').removeClass('active');
			$('#dot3').removeClass('active');
			$('#dot4').removeClass('active');
		} else if (scroll_left_width >= div_width -10 && scroll_left_width < (div_width*2 -10) ) {
			$('#dot2').addClass('active')
			$('#dot1').removeClass('active');
			$('#dot3').removeClass('active');
			$('#dot4').removeClass('active');
		} else if (  scroll_left_width >= (div_width * 2 - 10) && scroll_left_width < (div_width * 3 - 10)) {
			$('#dot3').addClass('active')
			$('#dot2').removeClass('active');
			$('#dot1').removeClass('active');
			$('#dot4').removeClass('active');
		}
		else if (  scroll_left_width >= (div_width * 3 - 10) ) {
			$('#dot4').addClass('active')
			$('#dot2').removeClass('active');
			$('#dot3').removeClass('active');
			$('#dot1').removeClass('active');
		}
	});


	$('#dot1').on('click', function (e) {
		e.preventDefault();
		$('page-cms-les-producteurs .row.equal').scrollLeft(0);
		$('#dot1').addClass('active')
		$('#dot2').removeClass('active');
		$('#dot3').removeClass('active');
		$('#dot4').removeClass('active');
	});
	$('#dot2').on('click', function (e) {
		e.preventDefault();
		$('page-cms-les-producteurs .row.equal').scrollLeft($('page-cms-les-producteurs .row.equal').width());
		$('#dot2').addClass('active')
		$('#dot1').removeClass('active');
		$('#dot3').removeClass('active');
		$('#dot4').removeClass('active');
	});
	$('#dot3').on('click', function (e) {
		e.preventDefault();
		$('page-cms-les-producteurs .row.equal').scrollLeft($('page-cms-les-producteurs .row.equal').width()*2);
		$('#dot3').addClass('active')
		$('#dot2').removeClass('active');
		$('#dot1').removeClass('active');
		$('#dot4').removeClass('active');
	});
	$('#dot4').on('click', function (e) {
		e.preventDefault();
		$('page-cms-les-producteurs .row.equal').scrollLeft($('page-cms-les-producteurs .row.equal').width()*3);
		$('#dot4').addClass('active')
		$('#dot2').removeClass('active');
		$('#dot1').removeClass('active');
		$('#dot3').removeClass('active');
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


$(document).ready(function(){

	$("#qte1").click(function(){
		$("#collapsedesc1").collapse('toggle');
	});
	$("#qte2").click(function(){
		$("#collapsedesc2").collapse('toggle');
	});
	$("#qte3").click(function(){
		$("#collapsedesc3").collapse('toggle');
	});
	$("#qte4").click(function(){
		$("#collapsedesc4").collapse('toggle');
	});
	$("#qte5").click(function(){
		$("#collapsedesc5").collapse('toggle');
	});
	// Toggle plus minus icon on show hide of collapse element
	$(".collapse").on('show.bs.collapse', function(){
		$('.collapsedesc.in').each(function(){
			$(this).collapse('hide');
		});
	});

	$(".close-modal-p").click(function () {
		$('#popup-prix').hide();
		$('#popup-prix').removeClass('open');
		$('#typesapain').removeClass('openpopup');
	});
	$('.btn-mrg.btn-centers-mr').click(function () {
		$('#popup-prix').show();
		$('#popup-prix').addClass('open');
		$('#typesapain').addClass('openpopup');
	});

	$('.sapin-nom').attr('placeholder', 'Nom');
	$('.sapin-email').attr('placeholder', 'Email');
	$('.sapin-message').attr('placeholder', 'Message');


	$(".link-size").click(function () {
		$('.link-size').addClass('active');
		$('.link-essence').removeClass('active');
		$('.link-quality').removeClass('active');
		$('.link-pied').removeClass('active');
	});
	$(".link-essence").click(function () {
		$('.link-size').removeClass('active');
		$('.link-essence').addClass('active');
		$('.link-quality').removeClass('active');
		$('.link-pied').removeClass('active');
	});
	$(".link-quality").click(function () {
		$('.link-size').removeClass('active');
		$('.link-essence').removeClass('active');
		$('.link-quality').addClass('active');
		$('.link-pied').removeClass('active');
	});
	$(".link-pied").click(function () {
		$('.link-size').removeClass('active');
		$('.link-essence').removeClass('active');
		$('.link-quality').removeClass('active');
		$('.link-pied').addClass('active');
	});
	$('.slick-initialized .slick-slide').css("width", $( window ).width());


	$('.gallery-responsive').slick({
		dots: false,
		infinite: false,
		speed: 300,
		slidesToShow: 3,
		slidesToScroll: 1,
		initialSlide:1,
		responsive: [
			{
				breakpoint: 1024,
				settings: {
					slidesToShow: 2,
					slidesToScroll: 1,
					infinite: false,
					dots: false
				}
			},
			{
				breakpoint: 600,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1
				}
			},
			{
				breakpoint: 480,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1
				}
			}
		]
	});
	$('.gallery-responsive-2').slick({
		dots: false,
		infinite: false,
		speed: 300,
		slidesToShow: 3,
		slidesToScroll: 1,
		initialSlide:0,
		responsive: [
			{
				breakpoint: 1024,
				settings: {
					slidesToShow: 2,
					slidesToScroll: 1,
					infinite: false,
					dots: false
				}
			},
			{
				breakpoint: 600,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1
				}
			},
			{
				breakpoint: 480,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1
				}
			}
		]
	});
	$('.gallery-responsive-3').slick({
		dots: false,
		infinite: false,
		speed: 300,
		slidesToShow: 3,
		slidesToScroll: 1,
		initialSlide:0,
		responsive: [
			{
				breakpoint: 1024,
				settings: {
					slidesToShow: 2,
					slidesToScroll: 1,
					infinite: false,
					dots: false
				}
			},
			{
				breakpoint: 600,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1
				}
			},
			{
				breakpoint: 480,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1
				}
			}
		]
	});
	$('.gallery-responsive-4').slick({
		dots: false,
		infinite: false,
		speed: 300,
		slidesToShow: 3,
		slidesToScroll: 1,
		initialSlide:0,
		responsive: [
			{
				breakpoint: 1024,
				settings: {
					slidesToShow: 2,
					slidesToScroll: 1,
					infinite: false,
					dots: false
				}
			},
			{
				breakpoint: 600,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1
				}
			},
			{
				breakpoint: 480,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1
				}
			}
		]
	});
});
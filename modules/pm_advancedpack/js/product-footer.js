$(document).ready(function() {
	$('div.ap5-pack-product-image a.fancybox').fancybox();
	$("div.ap5-product-footer-pack").owlCarousel({
		autoPlay: true,
		stopOnHover: true,
		responsiveBaseWidth: (ap5_bootstrapTheme ? window : $('#ap5-page-product-box')),
		pagination: true
	});
});

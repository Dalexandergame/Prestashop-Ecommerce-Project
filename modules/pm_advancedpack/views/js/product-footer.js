$(document).ready(function() {
	$('div.ap5-pack-product-image a.fancybox').fancybox();
	$("div.ap5-product-footer-pack").pmAPOwlCarousel({
		autoplay: true,
		autoplayHoverPause: true,
		nav: false,
		dots: true
	});
});

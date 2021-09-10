function ap5_updateRefreshCartData() {
	$.ajax({
		type: 'POST',
		headers: { "cache-control": "no-cache" },
		url: baseUri + '?rand=' + new Date().getTime(),
		async: true,
		cache: false,
		dataType : "json",
		data: 'controller=update_cart&ajax=1&fc=module&module=pm_advancedpack&ajax=true&token=' + static_token,
		success: function(jsonData) {
			if (typeof(jsonData.hasError) !== 'undefined' && !jsonData.hasError) {
				if (typeof(ajaxCart) != 'undefined') {
					if (typeof(ajaxCart.updateCart) != 'undefined') {
						ajaxCart.updateCart(jsonData.cartData);
					}
					setTimeout(function() {
						if (typeof(ajaxCart.updateCartEverywhere) != 'undefined') {
							ajaxCart.updateCartEverywhere(jsonData.cartData);
						}
						if (typeof(ajaxCart.updateCartInformation) != 'undefined') {
							ajaxCart.updateCartInformation(jsonData.cartData);
						}
					}, 1000);
				}
			}
		}
	});
}

$(document).ajaxSuccess(function(e, ajaxOptions, ajaxData) {
	if (typeof(ajaxData) !== 'undefined' && typeof(ajaxData.data) !== 'undefined' && ajaxData.data != null && ajaxData.data.indexOf('controller=update_cart') == -1 && ajaxData.data.indexOf('controller=cart') > -1 && ajaxData.data.indexOf('add=1') > -1) {
		ap5_updateRefreshCartData();
	}
});
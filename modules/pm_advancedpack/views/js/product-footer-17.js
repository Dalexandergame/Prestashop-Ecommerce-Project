var ap5ProductFooterPlugin = {
	debug: false,

	log: function(txt) {
		if (ap5ProductFooterPlugin.debug) {
			console.log(new Date().toUTCString() + ' - ' + txt);
		}
	},

	displayErrors: function(jsonData) {
		// User errors display
		if (jsonData.hasError) {
			var errors = '';
			for (error in jsonData.errors) {
				errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
			}
			if (!!$.prototype.modal) {
				$('<div class="modal fade"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title text-danger">' + ap5_modalErrorTitle + '</h5></div><div class="modal-body"><p>' + errors + '</p></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button></div></div></div></div>').modal('show');
			} else {
				alert(errors);
			}
		}
	},

	addPackToCart: function(idPack, callerElement, callBack) {
		ap5ProductFooterPlugin.log('[ap5ProductFooterPlugin.addPackToCart] Call');
		if (idPack > 0) {
			var ap5_submitButton = $('[type="submit"]', callerElement);
			var ap5_quantityWanted = parseInt($('input[name=qty]', callerElement).val());
			if (isNaN(ap5_quantityWanted) || ap5_quantityWanted <= 0) {
				ap5_quantityWanted = 1;
			}
			$(ap5_submitButton).prop('disabled', true);
			$.ajax({
				type: 'POST',
				url: $(callerElement).attr('action'),
				data: {
					qty: ap5_quantityWanted,
					token: prestashop.static_token
				},
				dataType: 'json',
				cache: false,
				success: function(jsonData, textStatus, jqXHR) {
					ap5ProductFooterPlugin.log('[ap5ProductFooterPlugin.addPackToCart] Success');

					// Redirect if AJAX cart is disabled
					if (!jsonData.hasError && typeof(jsonData.ap5RedirectURL) !== 'undefined' && jsonData.ap5RedirectURL != null && jsonData.ap5RedirectURL.length > 0) {
						window.location = jsonData.ap5RedirectURL;
						return;
					}
					// Something went wrong, display error
					if (jsonData.hasError) {
						ap5ProductFooterPlugin.displayErrors(jsonData);
						return;
					}

					// Update ap5_cartPackProducts var from ajax call
					if (typeof(jsonData.ap5Data) == 'object' && typeof(jsonData.ap5Data.cartPackProducts) == 'object') {
						ap5_cartPackProducts = jsonData.ap5Data.cartPackProducts;
					}

					// We must define custom url here
					if (typeof(ap5_modalAjaxUrl) == 'string') {
						$('.blockcart').data('refresh-url', ap5_modalAjaxUrl);
					}

					// Display add to cart modal
					prestashop.emit('updateCart', {
						reason: {
							idProduct: idPack,
							idProductAttribute: jsonData.ap5Data.idProductAttribute,
							linkAction: 'add-to-cart'
						},
						resp: jsonData
					});
					$(document).trigger('ap5-After-AddPackToCart-ProductFooter', [idPack, callerElement]);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					alert("Impossible to add the product to the cart.\n\ntextStatus: '" + textStatus + "'\nerrorThrown: '" + errorThrown + "'\nresponseText:\n" + XMLHttpRequest.responseText);
				},
				complete: function(jqXHR, textStatus) {
					$(ap5_submitButton).prop('disabled', false);
				}
			});
		}
	}
}

$(document).ready(function() {
	$("div.ap5-product-footer-pack").pmAPOwlCarousel({
		autoplay: true,
		autoplayHoverPause: true,
		nav: false,
		dots: true
	});

	// Add pack to cart (product footer)
	$(document).on('submit', '.ap5-product-footer-pack-container form', function(e){
		e.preventDefault();
		e.stopImmediatePropagation();
		var pm_ap5_id_pack = parseInt($('input[name=id_product]', this).val());

		ap5ProductFooterPlugin.addPackToCart(pm_ap5_id_pack, $(this));
		return false;
	});
});

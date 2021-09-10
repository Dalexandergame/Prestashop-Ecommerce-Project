var ap5Plugin = {
	debug: false,
	fromQuickView: false,
	productPackExcludeBackup: [],
	productPackExclude: [],
	autoScrollBuyBlockEnabled: (typeof(ap5_autoScrollBuyBlock) !== 'undefined' && ap5_autoScrollBuyBlock),
	topLimit: 0,

	log: function(txt) {
		if (ap5Plugin.debug) {
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

	addPackToCart: function(idPack, idProductAttributeList, callerElement, callBack) {
		ap5Plugin.log('[ap5Plugin.addPackToCart] Call');
		if (idPack > 0) {
			var ap5_submitButton = $('[type="submit"]', callerElement);
			var ap5_quantityWanted = parseInt($('input[name=qty]').val());
			if (isNaN(ap5_quantityWanted) || ap5_quantityWanted <= 0) {
				ap5_quantityWanted = 1;
			}
			$(ap5_submitButton).prop('disabled', true);
			// Get quantity for each product
			var productPackQuantityList = [];
			$('.ap5-quantity-wanted').each(function (index, element) {
				id_product_pack = $(this).attr('data-id-product-pack');
				productPackQuantity = { idProductPack: id_product_pack, quantity: $(this).val() };
				productPackQuantityList.push(productPackQuantity);
			});
			// Get customization data for each product
			var productPackCustomizationList = [];
			$('.ap5-customization-form').each(function () {
				id_product_pack = $(this).attr('data-id-product-pack');
				$('.ap5-customization-block-input', $(this)).each(function (index, element) {
					id_customization_field = $(this).attr('data-id-customization-field');
					productPackCustomization = {
						idProductPack: id_product_pack,
						idCustomizationField: id_customization_field,
						value: $(this).val()
					};
					productPackCustomizationList.push(productPackCustomization);
				});
			});
			$.ajax({
				type: 'POST',
				url: $(callerElement).attr('action'),
				data: {
					id_product_attribute_list: idProductAttributeList,
					productPackExclude: ap5Plugin.productPackExclude,
					productPackQuantityList: productPackQuantityList,
					productPackCustomizationList: productPackCustomizationList,
					qty: ap5_quantityWanted,
					token: prestashop.static_token
				},
				dataType: 'json',
				cache: false,
				success: function(jsonData, textStatus, jqXHR) {
					ap5Plugin.log('[ap5Plugin.addPackToCart] Success');

					// Redirect if AJAX cart is disabled
					if (!jsonData.hasError && typeof(jsonData.ap5RedirectURL) !== 'undefined' && jsonData.ap5RedirectURL != null && jsonData.ap5RedirectURL.length > 0) {
						window.location = jsonData.ap5RedirectURL;
						return;
					}
					// Something went wrong, display error
					if (jsonData.hasError) {
						ap5Plugin.displayErrors(jsonData);
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
					if (typeof(callBack) === 'function') {
						// Trigger callback
						callBack(idPack, (typeof(jsonData.ap5Data.explodedProductsData) != 'object' ? jsonData.ap5Data.idProductAttribute : null), jsonData.ap5Data);
					} else {
						// Display add to cart modal
						prestashop.emit('updateCart', {
							reason: {
								idProduct: idPack,
								idProductAttribute: (typeof(jsonData.ap5Data.explodedProductsData) == 'object' ? jsonData.ap5Data.explodedProductsData : jsonData.ap5Data.idProductAttribute),
								linkAction: 'add-to-cart'
							},
							resp: jsonData
						});
					}
					$(document).trigger('ap5-After-AddPackToCart', [idPack, idProductAttributeList, callerElement]);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					try {
						jsonData = JSON.parse(XMLHttpRequest.responseText);
					} catch(e) {
						jsonData = false;
					}

					// Something went wrong, display error
					if (typeof(jsonData) == 'object' && jsonData.hasError) {
						ap5Plugin.displayErrors(jsonData);
						return;
					} else {
						alert("Impossible to add the product to the cart.\n\ntextStatus: '" + textStatus + "'\nerrorThrown: '" + errorThrown + "'\nresponseText:\n" + XMLHttpRequest.responseText);
					}
				},
				complete: function(jqXHR, textStatus) {
					$(ap5_submitButton).prop('disabled', false);
				}
			});
		}
	},

	initNewContent: function(jsonData) {
		$(document).trigger('ap5-Before-InitNewContent');

		// Remove events associated to updateProduct
		prestashop.removeAllListeners('updateProduct');

		$('#quantity_wanted').TouchSpin({
			verticalbuttons: true,
			verticalupclass: 'material-icons touchspin-up',
			verticaldownclass: 'material-icons touchspin-down',
			buttondown_class: 'btn btn-touchspin js-touchspin',
			buttonup_class: 'btn btn-touchspin js-touchspin',
			min: 1,
			max: (typeof(jsonData) == 'object' && typeof(jsonData.packAvailableQuantity) != 'undefined' && jsonData.packAvailableQuantity > 0 ? jsonData.packAvailableQuantity : 1000000)
		});
		$('.ap5-quantity-wanted').each(function (index, element) {
			$(element).TouchSpin({
				verticalbuttons: true,
				verticalupclass: 'material-icons touchspin-up',
				verticaldownclass: 'material-icons touchspin-down',
				buttondown_class: 'btn btn-touchspin js-touchspin',
				buttonup_class: 'btn btn-touchspin js-touchspin',
				min: 1,
				max: $(element).data('available-quantity') || 10000
			});
		});
		$('[data-button-action="add-to-cart"]').attr('data-button-action', 'add-pack-to-cart');
		$('.product-add-to-cart .js-add-to-cart').removeClass('js-add-to-cart');

		// $('div.ap5-pack-product-image a.fancybox, div.ap5-pack-product-slideshow a.fancybox').fancybox();
		$("div.ap5-pack-product-slideshow:not(.no-carousel)").pmAPOwlCarousel({
			responsive:{
				0:{
					items: 1
				},
				576:{
					items: 2
				},
				992:{
					items: 3
				}
			},
			mergeFit: false,
			dots: false,
			autoplay: true,
			autoplayHoverPause: true
		});
		$("div.ap5-pack-product-mobile-slideshow").pmAPOwlCarousel({
			items: 1,
			autoplay: false,
			autoplayHoverPause: true
		});

		if (ap5_displayMode == 'advanced') {
			ap5Plugin.applyProductListMinHeight($('.ap5-pack-product-name'), true, 'min-height');
			ap5Plugin.applyProductListMinHeight($('div.ap5-pack-product-price-table-container'), true, 'height');
			ap5Plugin.applyProductListMinHeight($('div.ap5-pack-images-container'), true, 'height');
			ap5Plugin.applyProductListMinHeight($('div.ap5-pack-product-content'), true, 'height');
			ap5Plugin.applyProductListMinHeight($('#ap5-pack-product-tab-list li'), true, 'height');
			ap5Plugin.applyProductListMinHeight($('div.ap5-right'), true, 'min-height', $('div.ap5-pack-product'));
		}

		ap5Plugin.addCSSClasses();

		$(document).trigger('ap5-After-InitNewContent');
	},

	addCSSClasses: function() {
		if (ap5_displayMode == 'simple') {
			return false;
		}

		ap5_packProductOffset = $('div.ap5-pack-product:not(.ap5-right):eq(0)').offset();
		if (typeof(ap5_packProductOffset) == 'object') {
			var minLeft = ap5_packProductOffset.left;
			var sameMinLeft = true;
			$('div.ap5-pack-product:not(.ap5-right)').each(function() {
				var offsetLeft = $(this).offset().left;
				sameMinLeft &= (offsetLeft == minLeft);
				if (offsetLeft > minLeft) {
					$(this).removeClass('ap5-no-plus-icon');
				}
			});
			if (sameMinLeft) {
				$('div.ap5-pack-product:not(.ap5-right)').each(function(index, value) {
					if (index > 0) {
						$(this).removeClass('ap5-no-plus-icon');
					}
				});
			}
		}
	},

	applyProductListMinHeight: function(items, includePadding, property, reference) {
		var minHeight = 0;
		var sourcesItem = (typeof(reference) != 'undefined' ? reference : items);
		$(items).css(property, '');
		$(sourcesItem).each(function() {
			if ((includePadding === true ? $(this).outerHeight() : $(this).height())  > minHeight) {
				minHeight = (includePadding === true ? $(this).outerHeight() : $(this).height());
			}
		});
		if (minHeight > 0) {
			$(items).css(property, minHeight);
		}
	},

	// Color Picker click
	colorPickerClick: function(elt) {
		id_attribute = $(elt).attr('data-id-attribute');
		id_attribute_group = $(elt).attr('data-id-attribute-group');
		id_product_pack = $(elt).attr('data-id-product-pack');
		ap5Plugin.log('[ap5_Event] Color picker click - ' + id_product_pack + ' - ' + id_attribute + ' - ' + id_attribute_group);
		$('ul.ap5-color-to-pick-list-' + id_product_pack + '-' + id_attribute_group).children().removeClass('selected');
		$('.color_pick_hidden_' + id_product_pack + '_' + id_attribute_group).val(id_attribute);
	},

	// Add layer and spinner
	addLayerLoading: function(pmAjaxSpinnerTarget) {
		// Remove previous spinner first
		ap5Plugin.removeLayerLoading(pmAjaxSpinnerTarget);
		// Create the spinner here
		$(pmAjaxSpinnerTarget).addClass('ap5-loader-blur').append('<div class="ap5-loader"></div>');
		$(pmAjaxSpinnerTarget).find('.ap5-loader').each(function() {
			$(this).css('top', $(pmAjaxSpinnerTarget).outerHeight()/2 - $(this).outerHeight()*1.4);
		});
		return pmAjaxSpinnerTarget;
	},

	// Remove layer and spinner
	removeLayerLoading: function(pmAjaxSpinnerTarget) {
		// Remove layer and spinner
		$(pmAjaxSpinnerTarget).removeClass('ap5-loader-blur');
		$('.ap5-loader', pmAjaxSpinnerTarget).remove();
	},

	// Send ajax query in order to update pack table
	updatePackTable: function() {
		ap5Plugin.log('[ap5Plugin.updatePackTable] Call');
		var productPackChoice = [];
		var productPackQuantityList = [];
		$('.ap5-attributes').each(function (index, element) {
			id_product_pack = $(this).attr('data-id-product-pack');
			productChoice = { idProductPack: id_product_pack, attributesList: []};
			$('select, input[type=hidden], input[type=radio]:checked', $(element)).each(function(){
				productChoice.attributesList.push(parseInt($(this).val()));
			});
			productPackChoice.push(productChoice);
		});
		// Get quantity for each product
		$('.ap5-quantity-wanted').each(function (index, element) {
			id_product_pack = $(this).attr('data-id-product-pack');
			productPackQuantity = { idProductPack: id_product_pack, quantity: $(this).val() };
			productPackQuantityList.push(productPackQuantity);
		});

		var pmAjaxSpinnerInstance = ap5Plugin.addLayerLoading($('#ap5-product-list'));
		$.ajax({
			type: 'POST',
			url: ap5_updatePackURL,
			data: {
				productPackChoice: productPackChoice,
				productPackExclude: ap5Plugin.productPackExclude,
				productPackQuantityList: productPackQuantityList,
				fromQuickView: (typeof(ap5Plugin.fromQuickView) != 'undefined' && ap5Plugin.fromQuickView ? 1 : 0),
				token: prestashop.static_token
			},
			dataType: 'json',
			cache: false,
			success: function(jsonData, textStatus, jqXHR) {
				$(document).trigger('ap5-Before-UpdatePackContent');
				if (typeof(jsonData.hasError) !== 'undefined' && jsonData.hasError) {
					ap5Plugin.displayErrors(jsonData);
					// Restore exclusion
					ap5Plugin.productPackExclude = ap5Plugin.productPackExcludeBackup.slice(0);
				} else {
					if (typeof(jsonData.packContentTable) !== 'undefined') {
						$('#ap5-product-list').replaceWith(jsonData.packContentTable);
					}
					if (typeof(jsonData.packPriceContainer) !== 'undefined') {
						$('#ap5-buy-block-container').replaceWith(jsonData.packPriceContainer);
					}
					if (typeof(jsonData.HOOK_EXTRA_RIGHT) !== 'undefined') {
						$('#ap5-hook-product-extra-right-container').html(jsonData.HOOK_EXTRA_RIGHT);
					}
					if (typeof(jsonData.productPackExclude) !== 'undefined') {
						ap5Plugin.productPackExclude = jsonData.productPackExclude;
					}
					if ((typeof(jsonData.packHasFatalErrors) !== 'undefined' && jsonData.packHasFatalErrors === true) ||
						(typeof(jsonData.packHasErrors) !== 'undefined' && jsonData.packHasErrors === true) ||
						(typeof(jsonData.packAvailableQuantity) !== 'undefined' && jsonData.packAvailableQuantity <= 0)
					) {
						$('#ap5-add-to-cart').hide();
					} else {
						$('#idCombination').val(jsonData.packAttributesList);
						$('#ap5-add-to-cart').show();
					}
				}
				setTimeout(function(){
					ap5Plugin.initNewContent(jsonData);
					ap5Plugin.removeLayerLoading(pmAjaxSpinnerInstance);
					$(document).trigger('ap5-After-UpdatePackContent');
				}, 100);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				$('#ap5-add-to-cart').hide();
				alert("Impossible to update pack attribute choice.\n\ntextStatus: '" + textStatus + "'\nerrorThrown: '" + errorThrown + "'\nresponseText:\n" + XMLHttpRequest.responseText);
			},
			complete: function(jqXHR, textStatus) {
				ap5Plugin.removeLayerLoading(pmAjaxSpinnerInstance);
			}
		});
	},

	changeBuyBlock: function(ap5_buyBlockURL, ap5_buyBlockPackPriceContainer) {
		ap5Plugin.log('[ap5Plugin.changeBuyBlock] Call');

		var pmAjaxSpinnerInstance = ap5Plugin.addLayerLoading($('#buy_block'));

		$(document).trigger('ap5-Before-UpdateBuyBlock');

		// Remove unused div
		$('.product-prices').remove();
		$('#ap5-product-list').detach().insertBefore('.product-actions');
		$('.product-actions').html(ap5_buyBlockPackPriceContainer);

		setTimeout(function(){
			ap5Plugin.removeLayerLoading(pmAjaxSpinnerInstance);
			$(document).trigger('ap5-After-UpdateBuyBlock');
		}, 100);
	},

}

$(window).off('hashchange');
// End - Remove default behavior of product.js

// Add pack to cart
$(document).on('submit', 'form.ap5-buy-block', function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var pm_ap5_id_pack = parseInt($('input[name=id_product]').val());
	var pm_ap5_id_product_attribute_list = $('#idCombination').val();

	ap5Plugin.addPackToCart(pm_ap5_id_pack, pm_ap5_id_product_attribute_list, $(this));
	return false;
});

// Attribute choice
$(document).on('click', '.ap5-attributes .ap5-color', function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	if (!$(this).hasClass('disabled')) {
		ap5Plugin.colorPickerClick($(this));
		ap5Plugin.updatePackTable();
	}
});

$(document).on('change', '.ap5-attributes .ap5-attribute-select', function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	ap5Plugin.log('[ap5_Event] Attribute select click');
	ap5Plugin.updatePackTable();
});

$(document).on('click', '.ap5-attributes .ap5-attribute-radio', function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	ap5Plugin.log('[ap5_Event] Attribute radio click');
	ap5Plugin.updatePackTable();
});

$(document).on('ap5-CombinationUpdate', function(e){
	ap5Plugin.log('[ap5_Event] Combination update');
	ap5Plugin.updatePackTable();
});

// Quantity increment
$(document).on('click', '.product_quantity_up', function(e){
	e.preventDefault();
	if (typeof($(this).attr('rel')) == 'undefined') {
		qty_input_selector = 'input[name=qty]';
	} else {
		qty_input_selector = '#' + $(this).attr('rel');
	}
	var currentVal = parseInt($(qty_input_selector).val());
	if (quantityAvailable > 0) {
		quantityAvailableT = quantityAvailable;
	} else {
		quantityAvailableT = 100000000;
	}
	if (!isNaN(currentVal) && currentVal < quantityAvailableT) {
		$(qty_input_selector).val(currentVal + 1).trigger('keyup');
	} else {
		$(qty_input_selector).val(quantityAvailableT);
	}
});
// Quantity decrement
$(document).on('click', '.product_quantity_down', function(e){
	e.preventDefault();
	if (typeof($(this).attr('rel')) == 'undefined') {
		qty_input_selector = 'input[name=qty]';
	} else {
		qty_input_selector = '#' + $(this).attr('rel');
	}
	var currentVal = parseInt($(qty_input_selector).val());
	if (!isNaN(currentVal) && currentVal > 1) {
		$(qty_input_selector).val(currentVal - 1).trigger('keyup');
	} else {
		$(qty_input_selector).val(1);
	}
});
// Quantity check
$(document).on('keyup', 'input[name=qty], input.ap5-quantity-wanted', function(e){
	var currentVal = parseInt($(this).val());
	if (isNaN(currentVal) || currentVal <= 0) {
		$(this).val(1);
	}
});
// Quantity change trigger for pack product
$(document).on('change', 'input.ap5-quantity-wanted', function(e){
	// Prevent wanted quantity to be > available quantity
	if ($(this).data('available-quantity') && $(this).val() > $(this).data('available-quantity')) {
		e.preventDefault();
		$(this).val(parseInt($(this).data('available-quantity')));
	}
	ap5Plugin.updatePackTable();
});

// Exclude product
$(document).on('click', '.ap5-pack-product-icon-remove, .ap5-pack-product-remove-label', function(e){
	ap5Plugin.log('[ap5_Event] Product exclude');
	e.preventDefault();
	ap5Plugin.productPackExcludeBackup = ap5Plugin.productPackExclude.slice(0);
	var idProductPack = parseInt($(this).attr('data-id-product-pack'));
	if (ap5Plugin.productPackExclude.indexOf(idProductPack) == -1) {
		ap5Plugin.productPackExclude.push(idProductPack);
	}
	ap5Plugin.updatePackTable();
});

// Include product
$(document).on('click', '.ap5-pack-product-icon-check, .ap5-pack-product-add-label', function(e){
	ap5Plugin.log('[ap5_Event] Product include');
	e.preventDefault();
	ap5Plugin.productPackExcludeBackup = ap5Plugin.productPackExclude.slice(0);
	var idProductPack = parseInt($(this).attr('data-id-product-pack'));
	if (ap5Plugin.productPackExclude.indexOf(idProductPack) > -1) {
		ap5Plugin.productPackExclude.splice(ap5Plugin.productPackExclude.indexOf(idProductPack), 1);
	}
	ap5Plugin.updatePackTable();
});

// Add event on customization form
$(document).on('submit', '.ap5-customization-form', function(e){
	ap5Plugin.log('[ap5_Event] Customization form submit');
	// Prevent default, but fire validity check
	e.preventDefault();
});

// Image for modal on click
$(document).on('click', '#ap5-product-list a[data-toggle="modal"]', function(e) {
	target = $(this).data('target');
	if (typeof(target) != 'undefined') {
		$(target).find('li.thumb-container:eq(0) img').trigger('click');
	}
});

$(document).ready(function() {
	// Add classes to body
	$('body').addClass('ap5-pack-page');
	$('[data-button-action="add-to-cart"]').attr('data-button-action', 'add-pack-to-cart');
	$('.product-add-to-cart .js-add-to-cart').removeClass('js-add-to-cart');
	// Remove events associated to updateProduct
	prestashop.removeAllListeners('updateProduct');
});

$(window).on('load', function () {
	ap5Plugin.initNewContent();

	buyBlock = $('form.ap5-buy-block');

	if (ap5Plugin.autoScrollBuyBlockEnabled) {
		/* PS 1.6 CODE */
		if (buyBlock.length == 0) {
			return;
		}
		if (buyBlock.hasClass('ap5-from-modal')) {
			ap5Plugin.autoScrollBuyBlockEnabled = false;
			return;
		}
		ap5Plugin.topLimit = buyBlock.offset().top + parseFloat(buyBlock.css('marginTop').replace(/auto/, 0));
		$(window).on('scroll', function () {
			ap5_windowWidth = (navigator.userAgent.indexOf('Macintosh') > -1 && navigator.userAgent.indexOf('Safari/') > -1 ? $(window).width() : window.innerWidth);
			var ap5_scrollTop = $(window).scrollTop();
			var ap5_originalTop = parseFloat(buyBlock.css('top'));
			var ap5_buyBlockHeight = buyBlock.height();

			ap5_maxScrollAdd = 0;
			if ($('#ap5-pack-description-block').length > 0) {
				ap5_maxScrollAdd += $('#ap5-pack-description-block').offset().top;
			} else if ($('#ap5-pack-content-block').length > 0) {
				ap5_maxScrollAdd += $('#ap5-pack-content-block').offset().top;
			} else {
				ap5_maxScrollAdd += $('#ap5-product-list').offset().top + $('#ap5-product-list').height();
			}
			var ap5_maxScroll = -10 + ap5_maxScrollAdd;

			if (ap5_windowWidth >= 768 && ap5_scrollTop >= ap5Plugin.topLimit) {
				buyBlock.addClass('ap5-fixed');
				marginLeftBuyBlock = buyBlock.css('marginLeft');
				if (typeof(marginLeftBuyBlock) == 'undefined') {
					marginLeftBuyBlock = 0;
				} else {
					marginLeftBuyBlock = parseFloat(marginLeftBuyBlock.replace(/auto/, 0));
				}
	 			buyBlock.css('width', buyBlock.parent().width() - marginLeftBuyBlock);
				if ((ap5_scrollTop + ap5_buyBlockHeight) >= ap5_maxScroll) {
					if (ap5_scrollTop > (ap5_maxScroll - ap5_buyBlockHeight)) {
						if (ap5_scrollTop < ap5_maxScroll) {
							toTop = (ap5_scrollTop - ap5_maxScroll + ap5_buyBlockHeight) * -1;
							buyBlock.css('top', toTop);
						} else {
							buyBlock.css('top', -ap5_buyBlockHeight);
						}
					}
				} else {
					buyBlock.css('top', '');
				}
			} else {
				buyBlock.css('top', '');
	 			buyBlock.css('width', '');
				buyBlock.removeClass('ap5-fixed');
			}
		});

		$(window).trigger('scroll');
	}

	$(window).on('resize', function () {
		if (ap5Plugin.autoScrollBuyBlockEnabled) {
			ap5_formOffset = $('form.ap5-buy-block').offset();
			if (typeof(ap5_formOffset) == 'object') {
				ap5Plugin.topLimit = ap5_formOffset.top + parseFloat($('form.ap5-buy-block').css('marginTop').replace(/auto/, 0));
			}
		}
		ap5Plugin.applyProductListMinHeight($('#ap5-pack-product-tab-list li'), true, 'height');
		ap5Plugin.applyProductListMinHeight($('.ap5-pack-product-name'), true, 'min-height');
		ap5Plugin.addCSSClasses();
	});
});

var ap5Debug = false;
var ap5_topLimit = 0;
var ap5_autoScrollBuyBlockEnabled = (typeof(ap5_autoScrollBuyBlock) !== 'undefined' && ap5_autoScrollBuyBlock);
var ap5_productPackExclude = [];
var ap5_productPackExcludeBackup = [];

function ap5_log(txt) {
	if (ap5Debug)
		console.log(new Date().toUTCString() + ' - ' + txt);
}

function ap5_displayErrors(jsonData) {
	// User errors display
	if (jsonData.hasError) {
		var errors = '';
		for (error in jsonData.errors)
			// IE6 bug fix
			if (error != 'indexOf')
				errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
		if (!!$.prototype.fancybox)
			$.fancybox.open([
				{
					type: 'inline',
					autoScale: true,
					minHeight: 30,
					content: '<p class="fancybox-error">' + errors + '</p>'
				}
			], {
				padding: 0
			});
		else
			alert(errors);
	}
}

function ap5_addPackToCart(idPack, idProductAttributeList, callerElement, callBack) {
	ap5_log('[ap5_addPackToCart] Call');
	if (idPack > 0) {
		var ap5_submitButton = $('input[type="submit"]', callerElement);
		var ap5_quantityWanted = parseInt($('input[name=qty]').val());
		if (isNaN(ap5_quantityWanted) || ap5_quantityWanted <= 0)
			ap5_quantityWanted = 1;
		$(ap5_submitButton).attr('disabled', true).removeClass('exclusive').addClass('exclusive_disabled');
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
				productPackExclude: ap5_productPackExclude,
				productPackQuantityList: productPackQuantityList,
				productPackCustomizationList: productPackCustomizationList,
				qty: ap5_quantityWanted,
				token: static_token
			},
			dataType: 'json',
			cache: false,
			success: function(jsonData,textStatus,jqXHR) {
				ap5_log('[ap5_addPackToCart] Success');

				// Redirect if AJAX cart is disabled
				if (!jsonData.hasError && typeof(jsonData.ap5RedirectURL) !== 'undefined' && jsonData.ap5RedirectURL != null && jsonData.ap5RedirectURL.length > 0) {
					window.location = jsonData.ap5RedirectURL;
					return;
				}

				$('#ap5-add-to-cart button').prop('disabled', 'disabled').addClass('disabled');
				$('.filled').removeClass('filled');
				if ($('.cart_block_list').hasClass('collapsed'))
					this.expand();
				if (!jsonData.hasError) {
					if (typeof(callBack) == 'function') {
						// Trigger callback
						callBack(idPack, jsonData.ap5Data.idProductAttribute, jsonData.ap5Data);
					} else {
						// Modal Cart 3
						if (typeof(modalAjaxCart) !== 'undefined' && typeof(jsonData.ap5Data) !== 'undefined' && typeof(jsonData.ap5Data.idProductAttribute) !== 'undefined') {
							modalAjaxCart.showModal("pack_add", idPack, jsonData.ap5Data.idProductAttribute);
						} else if (typeof(ajaxCart) !== 'undefined' && typeof(ajaxCart.updateLayer) !== 'undefined') {
							if (typeof(jsonData.ap5Data) !== 'undefined') {
								$(jsonData.products).each(function() {
									if (this.id != undefined && this.id == parseInt(idPack) && this.idCombination == parseInt(jsonData.ap5Data.idProductAttribute))
										if (typeof(contentOnly) !== 'undefined' && contentOnly && typeof(window.parent.ajaxCart) !== 'undefined') {
											window.parent.ajaxCart.updateLayer(this);
										} else {
											ajaxCart.updateLayer(this);
										}
								});
							} else if (typeof(jsonData.fakeAp5Product) !== 'undefined') {
								if (typeof(contentOnly) !== 'undefined' && contentOnly && typeof(window.parent.ajaxCart) !== 'undefined') {
									window.parent.updateLayer(jsonData.fakeAp5Product);
								} else {
									ajaxCart.updateLayer(jsonData.fakeAp5Product);
								}
							}
						}
					}
					if (typeof(contentOnly) !== 'undefined' && contentOnly && typeof(window.parent.ajaxCart) !== 'undefined') {
						window.parent.ajaxCart.updateCartInformation(jsonData, true);
					} else if (typeof(window.parent.ajaxCart) !== 'undefined') {
						window.parent.ajaxCart.updateCartInformation(jsonData, true);
					} else if (typeof(ajaxCart) !== 'undefined') {
						ajaxCart.updateCartInformation(jsonData, true);
					}
					$('#ap5-add-to-cart button').removeProp('disabled').removeClass('disabled');
					if (!jsonData.hasError || jsonData.hasError == false)
						$('#ap5-add-to-cart button').addClass('added');
					else
						$('#ap5-add-to-cart button').removeClass('added');

					// Close quick view
					if (typeof(contentOnly) !== 'undefined' && contentOnly) {
						parent.$.fancybox.close();
					}
				} else {
					$('#ap5-add-to-cart button').removeProp('disabled').removeClass('disabled');
					ap5_displayErrors(jsonData);
				}
				$(document).trigger('ap5-After-AddPackToCart', [idPack, idProductAttributeList, callerElement]);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				alert("Impossible to add the product to the cart.\n\ntextStatus: '" + textStatus + "'\nerrorThrown: '" + errorThrown + "'\nresponseText:\n" + XMLHttpRequest.responseText);
				$('#add_to_cart button').removeProp('disabled').removeClass('disabled');
			},
			complete: function(jqXHR, textStatus) {
				$('#add_to_cart button').removeProp('disabled').removeClass('disabled');
			}
		});
	}
}

// Remove default behavior of product.js
$(document).off('click', '.color_pick');
$(document).off('change', '.attribute_select');
$(document).off('click', '.attribute_radio');
$(window).off('hashchange');
// End - Remove default behavior of product.js

// Add pack to cart
$(document).on('submit', 'form.ap5-buy-block', function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var pm_ap5_id_pack = parseInt($('input[name=id_product]').val());
	var pm_ap5_id_product_attribute_list = $('#idCombination').val();

	ap5_addPackToCart(pm_ap5_id_pack, pm_ap5_id_product_attribute_list, $(this));
	return false;
});

// Attribute choice
$(document).on('click', '.ap5-attributes .color_pick', function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	if (!$(this).hasClass('disabled')) {
		ap5_colorPickerClick($(this));
		ap5_updatePackTable();
	}
});

$(document).on('change', '.ap5-attributes .attribute_select', function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	ap5_log('[ap5_Event] Attribute select click');
	ap5_updatePackTable();
});

$(document).on('click', '.ap5-attributes .attribute_radio', function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	ap5_log('[ap5_Event] Attribute radio click');
	ap5_updatePackTable();
});

$(document).on('ap5-CombinationUpdate', function(e){
	ap5_log('[ap5_Event] Combination update');
	ap5_updatePackTable();
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
	ap5_updatePackTable();
});
// Quantity increment for pack product
$(document).on('click', '.ap5-product-quantity-up', function(e){
	e.preventDefault();
	qty_input_selector = '#' + $(this).attr('rel');
	var currentVal = parseInt($(qty_input_selector).val());
	if (!isNaN(currentVal)) {
		$(qty_input_selector).val(currentVal + 1).trigger('keyup');
	} else {
		$(qty_input_selector).val(quantityAvailableT);
	}
	ap5_updatePackTable();
});
// Quantity decrement for pack product
$(document).on('click', '.ap5-product-quantity-down', function(e){
	e.preventDefault();
	qty_input_selector = '#' + $(this).attr('rel');
	var currentVal = parseInt($(qty_input_selector).val());
	if (!isNaN(currentVal) && currentVal > 1) {
		$(qty_input_selector).val(currentVal - 1).trigger('keyup');
	} else {
		$(qty_input_selector).val(1);
	}
	ap5_updatePackTable();
});

// Exclude product
$(document).on('click', '.ap5-pack-product-icon-remove, .ap5-pack-product-remove-label', function(e){
	ap5_log('[ap5_Event] Product exclude');
	e.preventDefault();
	ap5_productPackExcludeBackup = ap5_productPackExclude.slice(0);
	var idProductPack = parseInt($(this).attr('data-id-product-pack'));
	if (ap5_productPackExclude.indexOf(idProductPack) == -1)
		ap5_productPackExclude.push(idProductPack);
	ap5_updatePackTable();
});

// Include product
$(document).on('click', '.ap5-pack-product-icon-check, .ap5-pack-product-add-label', function(e){
	ap5_log('[ap5_Event] Product include');
	e.preventDefault();
	ap5_productPackExcludeBackup = ap5_productPackExclude.slice(0);
	var idProductPack = parseInt($(this).attr('data-id-product-pack'));
	if (ap5_productPackExclude.indexOf(idProductPack) > -1)
		ap5_productPackExclude.splice(ap5_productPackExclude.indexOf(idProductPack), 1);
	ap5_updatePackTable();
});

// Add event on customization form
$(document).on('submit', '.ap5-customization-form', function(e){
	ap5_log('[ap5_Event] Customization form submit');
	// Prevent default, but fire validity check
	e.preventDefault();
});

$(document).ready(function() {	// Add classes to body
	$('body').addClass('ap5-pack-page');
});

$(window).on('load', function () {
	ap5_initNewContent();

	if (ap5_autoScrollBuyBlockEnabled) {
		ap5_topLimit = $('form.ap5-buy-block').offset().top + parseFloat($('form.ap5-buy-block').css('marginTop').replace(/auto/, 0));
		$(window).on('scroll', function () {
			ap5_windowWidth = (navigator.userAgent.indexOf('Macintosh') > -1 && navigator.userAgent.indexOf('Safari/') > -1 ? $(window).width() : window.innerWidth);
			var ap5_scrollTop = $(window).scrollTop();
			var ap5_originalTop = parseFloat($('form.ap5-buy-block').css('top'));
			var ap5_buyBlockHeight = $('form.ap5-buy-block').height();

			ap5_maxScrollAdd = 0;
			if ($('#ap5-pack-description-block').size() > 0) {
				ap5_maxScrollAdd += $('#ap5-pack-description-block').offset().top;
			} else if ($('#ap5-pack-content-block').size() > 0) {
				ap5_maxScrollAdd += $('#ap5-pack-content-block').offset().top;
			} else {
				ap5_maxScrollAdd += $('#ap5-product-list').offset().top + $('#ap5-product-list').height();
			}
			var ap5_maxScroll = -10 + ap5_maxScrollAdd;

			if (ap5_windowWidth >= 768 && ap5_scrollTop >= ap5_topLimit) {
				$('form.ap5-buy-block').addClass('ap5-fixed');
	 			$('form.ap5-buy-block').css('width', $('form.ap5-buy-block').parent().width() - parseFloat($('form.ap5-buy-block').css('marginLeft').replace(/auto/, 0)) );
				if ((ap5_scrollTop + ap5_buyBlockHeight) >= ap5_maxScroll) {
					if (ap5_scrollTop > (ap5_maxScroll - ap5_buyBlockHeight)) {
						if (ap5_scrollTop < ap5_maxScroll) {
							toTop = (ap5_scrollTop - ap5_maxScroll + ap5_buyBlockHeight) * -1;
							$('form.ap5-buy-block').css('top', toTop);
						} else {
							$('form.ap5-buy-block').css('top', -ap5_buyBlockHeight);
						}
					}
				} else {
					$('form.ap5-buy-block').css('top', '');
				}
			} else {
				$('form.ap5-buy-block').css('top', '');
	 			$('form.ap5-buy-block').css('width', '');
				$('form.ap5-buy-block').removeClass('ap5-fixed');
			}
		});

		$(window).trigger('scroll');
	}

	$(window).on('resize', function () {
		if (ap5_autoScrollBuyBlockEnabled)
			ap5_topLimit = $('form.ap5-buy-block').offset().top + parseFloat($('form.ap5-buy-block').css('marginTop').replace(/auto/, 0));
		ap5_applyProductListMinHeight($('#ap5-pack-product-tab-list li'), true, 'height');
		ap5_applyProductListMinHeight($('.ap5-pack-product-name'), true, 'min-height');
		ap5_addCSSClasses();
	});
});

function ap5_initNewContent() {
	$(document).trigger('ap5-Before-InitNewContent');

	$('div.ap5-pack-product-image a.fancybox, div.ap5-pack-product-slideshow a.fancybox').fancybox();
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
	if (typeof($.uniform) != 'undefined') {
		// Init PS 1.6 theme default behaviour
		$("select.form-control,input[type='checkbox']:not(.comparator),input[type='radio']").uniform();
		// /Init PS 1.6 theme default behaviour
	}

	if (ap5_displayMode == 'advanced') {
		ap5_applyProductListMinHeight($('.ap5-pack-product-name'), true, 'min-height');
		ap5_applyProductListMinHeight($('div.ap5-pack-product-price-table-container'), true, 'height');
		ap5_applyProductListMinHeight($('div.ap5-pack-images-container'), true, 'height');
		ap5_applyProductListMinHeight($('div.ap5-pack-product-content'), true, 'min-height');
		ap5_applyProductListMinHeight($('#ap5-pack-product-tab-list li'), true, 'height');
		ap5_applyProductListMinHeight($('div.ap5-right'), true, 'min-height', $('div.ap5-pack-product'));
	}

	ap5_addCSSClasses();

	$(document).trigger('ap5-After-InitNewContent');
}

function ap5_addCSSClasses() {
	if (ap5_displayMode == 'simple') {
		return false;
	}

	var minLeft = $('div.ap5-pack-product:not(.ap5-right):eq(0)').offset().left;
	var sameMinLeft = true;
	$('div.ap5-pack-product:not(.ap5-right)').each(function() {
		var offsetLeft = $(this).offset().left;
		sameMinLeft &= (offsetLeft == minLeft);
		if (offsetLeft > minLeft)
			$(this).removeClass('ap5-no-plus-icon');
	});
	if (sameMinLeft) {
		$('div.ap5-pack-product:not(.ap5-right)').each(function(index, value) {
			if (index > 0)
				$(this).removeClass('ap5-no-plus-icon');
		});
	}
}

function ap5_applyProductListMinHeight(items, includePadding, property, reference) {
	var minHeight = 0;
	var sourcesItem = (typeof(reference) != 'undefined' ? reference : items);
	$(items).css(property, '');
	$(sourcesItem).each(function() {
		if ((includePadding === true ? $(this).outerHeight() : $(this).height())  > minHeight)
			minHeight = (includePadding === true ? $(this).outerHeight() : $(this).height());
	});
	if (minHeight > 0)
		$(items).css(property, minHeight);
}

// Color Picker click
function ap5_colorPickerClick(elt) {
	id_attribute = $(elt).attr('data-id-attribute');
	id_attribute_group = $(elt).attr('data-id-attribute-group');
	id_product_pack = $(elt).attr('data-id-product-pack');
	ap5_log('[ap5_Event] Color picker click - ' + id_product_pack + ' - ' + id_attribute + ' - ' + id_attribute_group);
	$('ul.ap5-color-to-pick-list-' + id_product_pack + '-' + id_attribute_group).children().removeClass('selected');
	$('.color_pick_hidden_' + id_product_pack + '_' + id_attribute_group).val(id_attribute);
}

// Add layer and spinner
function ap5_addLayerLoading(pmAjaxSpinnerTarget) {
	// Remove previous spinner first
	ap5_removeLayerLoading(pmAjaxSpinnerTarget);
	// Create the spinner here
	$(pmAjaxSpinnerTarget).addClass('ap5-loader-blur').append('<div class="ap5-loader"></div>');
	$(pmAjaxSpinnerTarget).find('.ap5-loader').each(function() {
		$(this).css('top', $(pmAjaxSpinnerTarget).outerHeight()/2 - $(this).outerHeight()*1.4);
	});
	return pmAjaxSpinnerTarget;
}

// Remove layer and spinner
function ap5_removeLayerLoading(pmAjaxSpinnerTarget) {
	// Remove layer and spinner
	$(pmAjaxSpinnerTarget).removeClass('ap5-loader-blur');
	$('.ap5-loader', pmAjaxSpinnerTarget).remove();
}

// Send ajax query in order to update pack table
function ap5_updatePackTable() {
	ap5_log('[ap5_updatePackTable] Call');
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

	var pmAjaxSpinnerInstance = ap5_addLayerLoading($('#ap5-product-list'));
	$.ajax({
		type: 'POST',
		url: ap5_updatePackURL,
		data: {
			productPackChoice: productPackChoice,
			productPackExclude: ap5_productPackExclude,
			productPackQuantityList: productPackQuantityList,
			token: static_token
		},
		dataType: 'json',
		cache: false,
		success: function(jsonData, textStatus, jqXHR) {
			$(document).trigger('ap5-Before-UpdatePackContent');
			if (typeof(jsonData.hasError) !== 'undefined' && jsonData.hasError) {
				ap5_displayErrors(jsonData);
				// Restore exclusion
				ap5_productPackExclude = ap5_productPackExcludeBackup.slice(0);
			} else {
				if (typeof(jsonData.packContentTable) !== 'undefined')
					$('#ap5-product-list').replaceWith(jsonData.packContentTable);
				if (typeof(jsonData.packPriceContainer) !== 'undefined')
					$('#ap5-buy-block-container').replaceWith(jsonData.packPriceContainer);
				if (typeof(jsonData.HOOK_EXTRA_RIGHT) !== 'undefined')
					$('#ap5-hook-product-extra-right-container').html(jsonData.HOOK_EXTRA_RIGHT);
				if (typeof(jsonData.productPackExclude) !== 'undefined')
					ap5_productPackExclude = jsonData.productPackExclude;
				if ((typeof(jsonData.packHasFatalErrors) !== 'undefined' && jsonData.packHasFatalErrors === true) ||
					(typeof(jsonData.packHasErrors) !== 'undefined' && jsonData.packHasErrors === true) ||
					(typeof(jsonData.packAvailableQuantity) !== 'undefined' && jsonData.packAvailableQuantity <= 0)
				)
					$('#ap5-add-to-cart').hide();
				else {
					$('#idCombination').val(jsonData.packAttributesList);
					$('#ap5-add-to-cart').show();
				}
			}
			setTimeout(function(){
				ap5_initNewContent();
				ap5_removeLayerLoading(pmAjaxSpinnerInstance);
				$(document).trigger('ap5-After-UpdatePackContent');
			}, 100);
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			$('#ap5-add-to-cart').hide();
			alert("Impossible to update pack attribute choice.\n\ntextStatus: '" + textStatus + "'\nerrorThrown: '" + errorThrown + "'\nresponseText:\n" + XMLHttpRequest.responseText);
		},
		complete: function(jqXHR, textStatus) {
			ap5_removeLayerLoading(pmAjaxSpinnerInstance);
		}
	});
}

function ap5_changeBuyBlock(ap5_buyBlockURL, ap5_buyBlockPackPriceContainer) {
	ap5_log('[ap5_changeBuyBlock] Call');

	var pmAjaxSpinnerInstance = ap5_addLayerLoading($('#buy_block'));

	$(document).trigger('ap5-Before-UpdateBuyBlock');

	$('.box-info-product').html(ap5_buyBlockPackPriceContainer);

	$('#buy_block').addClass('ap5-buy-block');
	$('#buy_block').attr('action', ap5_buyBlockURL);

	setTimeout(function(){
		ap5_removeLayerLoading(pmAjaxSpinnerInstance);
		$(document).trigger('ap5-After-UpdateBuyBlock');
	}, 100);
}

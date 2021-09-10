var ap5_packIdTaxRulesGroup = null;
var ap5_packIdWarehouse = null;
var ap5_saveElement = null;
var ap5_saveAndStayElement = null;
var ap5_Bloodhound = null;
function ap5_hideUnusedFields() {
	// PS 1.6
	$('#product-pack-container').next('hr').addClass('ap5-admin-hide hide');
	$('input[name=type_product]').parents('div.form-group').addClass('ap5-admin-hide hide');
	$('a#page-header-desc-product-stats').parents('li').addClass('ap5-admin-hide hide');
	$('#product-suppliers').next('.panel').addClass('ap5-admin-hide hide');
	if (typeof(ap5_displayMode) != 'undefined' && ap5_displayMode == 'advanced') {
		// PS 1.6
		$('input[name=inputAccessories]').parents('div.form-group').addClass('ap5-admin-hide hide');
		// PS 1.7
		// $('#form_content #step1 #related-product').addClass('ap5-admin-hide hide');
	}
	// PS 1.7
	if (typeof (pm_advancedpack) !== 'undefined') {
		$('div.form_step1_type_product').addClass('ap5-admin-hide hide');
		$('li#tab_step3, li#tab_step2').addClass('ap5-admin-hide hide');
		$('#form_content #step1 #show_variations_selector').addClass('ap5-admin-hide hide');
		$('#form_content #step1 #form_step1_price_shortcut').parents('.form-group').addClass('ap5-admin-hide hide');
		$('#form_content #step6 #custom_fields').parent(':not(#supplier_collection)').parent().addClass('ap5-admin-hide hide');
		// Starting from PrestaShop 1.7, there is a check to see if a virtual product has combinations, which is forbidden.
		// As we use combinations to manage the pack, we have to prevent this check in order to avoid accidental removal
		// of our internal combinations, which would break the pack.
		// If the pack has virtual tag, then prevent checkAccessVariations() to be run
		if (pm_advancedpack.packIsVirtual) {
			displayFieldsManager.checkAccessVariations = function () { };
		}
	}
}

function ap5_packUpdated(firstCall, majorUpdate) {
	if (majorUpdate === true) {
		$('#ap5_is_major_edited_pack').val(1);
		// Update pack price simulation table
		ap5_updatePackPriceSimulation();
	}
	if (firstCall !== true) {
		$('#ap5_is_edited_pack').val(1);
		// Update pack price simulation table
		ap5_updatePackPriceSimulation();
	}
	// Update fields state
	if ($('#ap5-pack-content-table input.ap5_useReduc:not(:checked)').length > 0 || ap5_getNbProducts() < 2) {
		$('input#ap5_allow_remove_product').attr('checked', false);
		$('input#ap5_allow_remove_product').attr('disabled', true);
		if ($('#ap5-pack-content-table input#ap5_useReduc-all').is(':checked')) {
			$('#ap5-pack-content-table input#ap5_useReduc-all').prop('checked', false);
		}
	} else {
		$('input#ap5_allow_remove_product').attr('disabled', false);
		if (!$('#ap5-pack-content-table input#ap5_useReduc-all').is(':checked')) {
			$('#ap5-pack-content-table input#ap5_useReduc-all').prop('checked', true);
		}
	}
	if ($('#ap5-pack-content-table input.ap5_exclusive:not(:checked)').length > 0) {
		if ($('#ap5-pack-content-table input#ap5_exclusive-all').is(':checked')) {
			$('#ap5-pack-content-table input#ap5_exclusive-all').prop('checked', false);
		}
	} else {
		if (!$('#ap5-pack-content-table input#ap5_exclusive-all').is(':checked')) {
			$('#ap5-pack-content-table input#ap5_exclusive-all').prop('checked', true);
		}
	}

	$('#ap5-pack-content-table input.ap5_useReduc').each(function() {
		priceTd = $(this).parents('tr').find('td.ap5_productPrice-container');
		originalPrice = priceTd.find('.ap5-pack-product-original-price');
		if (originalPrice.length > 0) {
			currentPrice = priceTd.find('.ap5-pack-product-current-price');
			if ($(this).is(':checked')) {
				currentPrice.addClass('font-bold').removeClass('font-line-through');
				originalPrice.removeClass('font-bold').addClass('font-line-through');
			} else {
				currentPrice.removeClass('font-bold').addClass('font-line-through');
				originalPrice.addClass('font-bold').removeClass('font-line-through');
			}
		}
	});
}

function ap5_initBloodhound() {
	ap5_Bloodhound = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.whitespace,
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		identify: function(obj) {
			return obj.id;
		},
		remote: {
			url: ap5_productListUrl + '&q=%QUERY',
			cache: false,
			wildcard: '%QUERY',
			transform: function(response){
				var newResponse = [];

				if(!response){
					return newResponse;
				}

				$.each(response, function(key, item){
				   newResponse.push(item);
				});

				return newResponse;
			}
		}
	});
}

function ap5_initNewPackFields() {
	if (typeof(pm_advancedpack) != 'undefined') {
		if (ap5_Bloodhound == null) {
			ap5_initBloodhound();
		}
		$('#ap5_pack_content_input').typeahead({
			minLength: 2,
			highlight: true,
			hint: false
		}, {
			display: 'name',
			source: ap5_Bloodhound,
			limit: 100,
			templates: {
				suggestion: function(item){
					return '<div>' +
						   '<table><tr>' +
						   '<td style="padding-right: 10px">'+ item.image +'</td>' +
						   '<td>' + item.name + '<br />REF: ' + item.ref + '</td>' +
						   '</tr></table></div>'
				}
			}
		}).bind('typeahead:select', function(ev, suggestion) {
			ap5_addNewProductToPack(ev, suggestion, null)
			$(ev.target).val('');
		}).bind('typeahead:close', function(ev) {
			$(ev.target).val('');
		});
	} else {
		$('#ap5_pack_content_input').autocomplete(ap5_productListUrl, {
			minChars: 1,
			autoFill: true,
			max: 20,
			matchContains: true,
			mustMatch: true,
			scroll: false,
			cacheLength: 0,
			formatItem: function(item) {
				return item[0]+' - '+item[1];
			}
		}).result(ap5_addNewProductToPack);
	}

	$(document).on('click', '#ap5-pack-content-table input#ap5_exclusive-all', function() {
		$('#ap5-pack-content-table input.ap5_exclusive').prop('checked', $(this).is(':checked'));
	});
	$(document).on('click', '#ap5-pack-content-table input#ap5_useReduc-all', function() {
		$('#ap5-pack-content-table input.ap5_useReduc').prop('checked', $(this).is(':checked'));
	});

	$(document).on('click', 'button[name=submitAddproductAndStay], button[name=submitAddproduct]', function() {
		if ($('#ap5-pack-content-table>tbody>tr:not(.ap5_combinationsContainer):not(.ap5_customizationFieldsContainer)').length == 0) {
			alert(ap5_atLeastOneProductMessage);
			return false;
		} else if ($('#ap5-pack-content-table>tbody>tr:not(.ap5_combinationsContainer):not(.ap5_customizationFieldsContainer)').length < 2) {
			if (!confirm(ap5_atLeastTwoProductMessage)) {
				$('a#link-ModulePm_advancedpack').trigger('click');
				return false;
			}
		}
	});

	$(document).on('click', '.ap5_removeProduct', function() {
		if (confirm(ap5_deleteConfirmationMessage)) {
			$('#ap5-pack-content-table>tbody tr#ap5_packRow-' + $(this).attr('data-id-product-pack')).remove();
			$('#ap5-pack-content-table>tbody tr#ap5_combinationsContainer-' + $(this).attr('data-id-product-pack')).remove();
			$('#ap5-pack-content-table>tbody tr#ap5_customizationFieldsContainer-' + $(this).attr('data-id-product-pack')).remove();
			// Drag & Drop for ordering pack content
			ap5_makeTableDnD();
			if ($('#ap5-pack-content-table>tbody tr').length == 0) {
				// Reset some vars
				ap5_packIdTaxRulesGroup = null;
				ap5_packIdWarehouse = null;
			}
			// Update pack trigger (major)
			ap5_packUpdated(false, true);
		}

		return false;
	});

	$(document).on('change', '#ap5-pack-content-table input[type=text], #ap5-pack-content-table select, #ap5-pack-content-table input.ap5_useReduc, #ap5-pack-content-table input.ap5_exclusive, input.ap5_price_rules, input#ap5_allow_remove_product', function() {
		// Update pack trigger
		ap5_packUpdated();
	});

	$(document).on('change', 'input.ap5_price_rules, input#ap5_global_percentage_discount, input.ap5_fixed_pack_price', function() {
		ap5_updatePriceRulesForm();
		// Update pack trigger
		ap5_packUpdated();
	});
	ap5_updatePriceRulesForm();

	$(document).on('change', 'input.ap5_defaultCombination', function() {
		if (typeof(pm_advancedpack) != 'undefined') {
			// PS 1.7
			$('table#ap5-pack-combination-table-' + $(this).attr('data-id-product-pack') + ' tr.bg-info.text-white').removeClass('bg-info text-white');
			$(this).closest('tr').addClass('bg-info text-white');
		} else {
			// PS 1.6
			$('table#ap5-pack-combination-table-' + $(this).attr('data-id-product-pack') + ' tr.highlighted').removeClass('highlighted');
			$(this).closest('tr').addClass('highlighted');
		}
		// Update pack trigger (major)
		ap5_packUpdated(false, true);
	});

	$(document).on('click', 'input.ap5_combinationInclude', function(event) {
		if ($('table#ap5-pack-combination-table-' + $(this).attr('data-id-product-pack') + ' input.ap5_combinationInclude:checked').length == 0) {
			alert(ap5_atLeastOneCombinationMessage);
			event.preventDefault();
			return false;
		} else {
			if (!$(this).is(':checked')) {
				$('table#ap5-pack-combination-table-' + $(this).attr('data-id-product-pack') + ' input.ap5_defaultCombination[data-id-product-attribute='+ $(this).attr('data-id-product-attribute') +']').attr('disabled', true);
				if ($('input[name=ap5_defaultCombination-' + $(this).attr('data-id-product-pack') + ']:checked').val() == $(this).attr('data-id-product-attribute')) {
					ap5_nextIdProductAttribute = $('table#ap5-pack-combination-table-' + $(this).attr('data-id-product-pack') + ' input.ap5_combinationInclude:checked:first-child').attr('data-id-product-attribute');
					$('table#ap5-pack-combination-table-' + $(this).attr('data-id-product-pack') + ' input.ap5_defaultCombination[data-id-product-attribute='+ ap5_nextIdProductAttribute +']').click().trigger('change');
				}
			} else {
				$('table#ap5-pack-combination-table-' + $(this).attr('data-id-product-pack') + ' input.ap5_defaultCombination[data-id-product-attribute='+ $(this).attr('data-id-product-attribute') +']').attr('disabled', false);
			}
			// Update pack trigger (major)
			ap5_packUpdated(false, true);
		}
	});

	$(document).on('click', 'input.ap5_combinationIncludeAll', function() {
		if ($(this).is(':checked')) {
			$('table#ap5-pack-combination-table-' + $(this).attr('data-id-product-pack') + ' input.ap5_combinationInclude').each(function() {
				$(this).attr('checked', true);
				$('table#ap5-pack-combination-table-' + $(this).attr('data-id-product-pack') + ' input.ap5_defaultCombination[data-id-product-attribute='+ $(this).attr('data-id-product-attribute') +']').attr('disabled', false);
			});
		} else {
			ap5_currentIdProductAttribute = $('input[name=ap5_defaultCombination-' + $(this).attr('data-id-product-pack') + ']:checked').attr('data-id-product-attribute');
			$('table#ap5-pack-combination-table-' + $(this).attr('data-id-product-pack') + ' input.ap5_combinationInclude[data-id-product-attribute!='+ ap5_currentIdProductAttribute + ']').each(function() {
				$(this).attr('checked', false);
				$('table#ap5-pack-combination-table-' + $(this).attr('data-id-product-pack') + ' input.ap5_defaultCombination[data-id-product-attribute='+ $(this).attr('data-id-product-attribute') +']').attr('disabled', true);
			});
		}
		// Update pack trigger (major)
		ap5_packUpdated(false, true);
	});

	$(document).on('click', 'input.ap5_customCombinations', function() {
		if ($(this).is(':checked')) {
			$('#ap5-pack-content-table>tbody tr#ap5_combinationsContainer-' + $(this).attr('data-id-product-pack')).removeClass('hidden').removeClass('ap5-admin-hide');
			$(this).val(1);
		} else {
			$('#ap5-pack-content-table>tbody tr#ap5_combinationsContainer-' + $(this).attr('data-id-product-pack')).addClass('hidden').addClass('ap5-admin-hide');
			$(this).val(0);
			// Uncheck discount per combination option
			$('#ap5-pack-content-table>tbody tr#ap5_combinationsContainer-' + $(this).attr('data-id-product-pack') + ' input.ap5_combinationDiscount').prop('checked', false);
		}
		// Update pack trigger (major)
		ap5_packUpdated(false, true);
	});

	$(document).on('click', 'input.ap5_customizationFieldsInclude', function(event) {
		// Update pack trigger (major)
		ap5_packUpdated(false, true);
	});

	$(document).on('click', 'input.ap5_customizationFields', function() {
		if ($(this).is(':checked')) {
			$('#ap5-pack-content-table>tbody tr#ap5_customizationFieldsContainer-' + $(this).attr('data-id-product-pack')).removeClass('hidden').removeClass('ap5-admin-hide');
			$(this).val(1);
		} else {
			$('#ap5-pack-content-table>tbody tr#ap5_customizationFieldsContainer-' + $(this).attr('data-id-product-pack')).addClass('hidden').addClass('ap5-admin-hide');
			$(this).val(0);
		}
		// Update pack trigger (major)
		ap5_packUpdated(false, true);
	});

	$(document).on('click', 'input.ap5_customizationFieldsIncludeAll', function() {
		$('table#ap5-pack-customization-fields-table-' + $(this).attr('data-id-product-pack') + ' input.ap5_customizationFieldsInclude:not([disabled])').attr('checked', $(this).is(':checked'));
		// Update pack trigger (major)
		ap5_packUpdated(false, true);
	});

	$(document).on('change keypress keydown keyup', 'input.ap5_reductionAmount', function() {
		if (!$('#ap5_combinationDiscount-' + $(this).attr('data-id-product-pack')).is(':checked')) {
			$('input[name^="ap5_combinationReductionAmount-' + $(this).attr('data-id-product-pack') + '-"]').val($(this).val());
		}
	});
	$(document).on('change', 'select.ap5_reductionType', function() {
		if (!$('#ap5_combinationDiscount-' + $(this).attr('data-id-product-pack')).is(':checked')) {
			$('select[name^="ap5_combinationReductionType-' + $(this).attr('data-id-product-pack') + '-"] option[value="' + $(this).val() + '"]').attr('selected', true).trigger('change');
		}
	});
	$(document).on('change', 'input.ap5_combinationDiscount', function() {
		// Update pack trigger
		ap5_packUpdated(false);

		$('select[name="ap5_reductionType-' + $(this).attr('data-id-product-pack') + '"]').attr('disabled', $(this).is(':checked'));
		$('input[name="ap5_reductionAmount-' + $(this).attr('data-id-product-pack') + '"]').attr('disabled', $(this).is(':checked'));
		$('select[name^="ap5_combinationReductionType-' + $(this).attr('data-id-product-pack') + '-"]').attr('disabled', !$(this).is(':checked'));
		$('input[name^="ap5_combinationReductionAmount-' + $(this).attr('data-id-product-pack') + '-"]').attr('disabled', !$(this).is(':checked'));
		if ($(this).is(':checked')) {
			$('select[name^="ap5_combinationReductionType-' + $(this).attr('data-id-product-pack') + '-"]').parent().removeClass('ap5_inputDisabled');
		} else {
			$('select[name^="ap5_combinationReductionType-' + $(this).attr('data-id-product-pack') + '-"]').parent().addClass('ap5_inputDisabled');
		}
	});
	$('input.ap5_combinationDiscount').trigger('change');
	$(document).on('click', 'tr.ap5_combinationsContainer td.ap5_discountCell', function() {
		if ($(this).attr('data-id-product-pack')) {
			if (!$('#ap5_combinationDiscount-' + $(this).attr('data-id-product-pack')).is(':checked')) {
				$('#ap5_combinationDiscount-' + $(this).attr('data-id-product-pack')).parent().toggleClass('ap5_pulse');
			}
		}
	});

	// Update pack trigger
	ap5_packUpdated(true);

	// Update pack price simulation table
	ap5_updatePackPriceSimulation();

	// Drag & Drop for ordering pack content
	ap5_makeTableDnD();

	// Auto-add source product into the pack
	if (ap5_getIdProductSource() !== false)
		ap5_addNewPackLine(ap5_getIdProductSource());

	// Make Images tab availables, only when pack has already been created
	if ($('#ap5_pack_positions').val() != '')
		$('a#link-Images').css('display', 'block');
}

function ap5_getIdProductSource() {
	if (typeof(window.location.href) != 'undefined') {
		var source_id_productRegexp = new RegExp('[\\?&]source_id_product=([^&#]*)').exec(window.location.href);
		if (source_id_productRegexp && source_id_productRegexp.length == 2 && !isNaN(source_id_productRegexp[1]) && source_id_productRegexp[1] > 0)
			return parseInt(source_id_productRegexp[1]);
	}
	return false;
}

function ap5_setProductPositions() {
	tmpPositionsArray = [];
	$("#ap5-pack-content-table>tbody>tr:not(.ap5_combinationsContainer):not(.ap5_customizationFieldsContainer)").each(function() {
		tmpPositionsArray.push($(this).attr('data-id-product-pack'));
	});
	$('#ap5_pack_positions').val(tmpPositionsArray.join(','));
}

function ap5_makeTableDnD() {
	if ($('#ap5-pack-content-table>tbody tr').length == 0) {
		$('#ap5-pack-content-table').addClass('ap5-admin-hide hide');
	} else {
		$('#ap5-pack-content-table').removeClass('ap5-admin-hide hide');
	}
	$("#ap5-pack-content-table").tableDnD({
		onDragStart: function(table, row) {
			ap5_packContentOriginalOrder = $.tableDnD.serialize();
			$('#ap5_combinationsContainer-' + $(row).attr('id') + ', ' + '#ap5_customizationFieldsContainer-' + $(row).attr('id')).hide(200);
			$('.ap5_combinationsContainer, .ap5_customizationFieldsContainer').animate({opacity: 0.2}, 200);
			ap5_setProductPositions();
		},
		onDrop: function(table, row) {
			pid = $(row).attr('id');
			if (ap5_packContentOriginalOrder != $.tableDnD.serialize()) {
				$('#ap5_is_edited_pack').val(1);
				$('#ap5-pack-content-table>tbody tr.ap5_customizationFieldsContainer').each(function() {
					$(this).insertAfter('#ap5-pack-content-table>tbody tr#ap5_packRow-' + $(this).attr('data-id-product-pack'));
				});
				$('#ap5-pack-content-table>tbody tr.ap5_combinationsContainer').each(function() {
					$(this).insertAfter('#ap5-pack-content-table>tbody tr#ap5_packRow-' + $(this).attr('data-id-product-pack'));
				});
			}
			$('#ap5_combinationsContainer-' + pid + ', #ap5_customizationFieldsContainer-' + pid).show(200);
			$('.ap5_combinationsContainer, .ap5_customizationFieldsContainer').animate({opacity: 1}, 200);
			ap5_setProductPositions();
		}
	});
	ap5_setProductPositions();
}

function ap5_addNewProductToPack(event, data, formatted) {
	if (data == null) {
		return false;
	}
	if (typeof(data.id) != 'undefined') {
		// PS 1.7
		var productId = data.id;
		var productName = data.name;
	} else {
		// PS 1.6
		var productId = parseInt(data[1]);
		var productName = data[0];
	}
	var getWarehouseIdResult = false;
	ap5_getProductInformations(productId).done(function(jsonData, textStatus, jqXHR) {
		if (jsonData != undefined) {
			if (jsonData.warehouseListId != undefined) {
				if (jsonData.idWarehouse == null || jsonData.idWarehouse != undefined) {
					if (ap5_packIdWarehouse == null) {
						ap5_packIdWarehouse = jsonData.idWarehouse;
						getWarehouseIdResult = true;
					} else if (ap5_packIdWarehouse == jsonData.idWarehouse || jsonData.idWarehouse == null) {
						getWarehouseIdResult = true;
					}
				}
			}
		}
		if (!getWarehouseIdResult) {
			alert(ap5_warehouseMessage);
		}
		if (getWarehouseIdResult) {
			$('#ap5_pack_content_input').val('');
			ap5_addNewPackLine(productId);
		} else {
			return false;
		}
	});
}

function ap5_addNewPackLine(productId) {
	$.ajax({
		type: "POST",
		dataType: "json",
		url: ap5_updateUrl,
		data: {
			addPackLine: 1,
			productId: productId
		},
		cache: false,
		success: function(jsonData, textStatus, jqXHR) {
			if (jsonData != undefined && jsonData.html != undefined) {
				$('#ap5-pack-content-table>tbody').append(jsonData.html);
				// Drag & Drop for ordering pack content
				ap5_makeTableDnD();
				// Set new layout settings & fields values
				ap5_updatePriceRulesForm();
				// Update pack trigger (major)
				ap5_packUpdated(false, true);
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
		},
		complete: function(jqXHR, textStatus) {
		}
	});
}

function ap5_setProductTabName(productTabName) {
	$('a#link-ModulePm_advancedpack').html(productTabName);
}

$(document).ready(function() {
	$('a#link-ModulePm_advancedpack').attr('href', $('a#link-ModulePm_advancedpack').attr('href') + '&is_real_new_pack=1');
	ap5_hideUnusedFields();

	$(document).ajaxComplete(function() {
		ap5_hideUnusedFields();
	});

	// PS 1.7
	if (typeof(pm_advancedpack) != 'undefined') {
		$('#form_content .form-contenttab').removeClass('active');
		newTabContent = $('#form_content .form-contenttab:eq(0)').clone().attr('id', 'pm_advancedpack').html(pm_advancedpack.tabContent);
		newTabContent.insertBefore('#form_content .form-contenttab:eq(0)');
		if (typeof(jQuery.fn.pstooltip) != 'undefined') {
			jQuery('[data-toggle="pstooltip"]', newTabContent).pstooltip();
		}

		$('#form-loading ul.js-nav-tabs li a').removeClass('active');
		newTabItem = $('#form-loading ul.js-nav-tabs li:eq(0)').clone().attr('id', 'tab_pm_advancedpack');
		$('a', newTabItem).html(pm_advancedpack.tabName).attr('href', '#pm_advancedpack');
		newTabItem.insertBefore('#form-loading ul.js-nav-tabs li:eq(0)');
		$('#tab_pm_advancedpack a').trigger('click');
		// Recalculate width of nav tabs container
		var navWidth = 50;
		$('#form-loading ul.js-nav-tabs li').each((index, item) => {
			navWidth += $(item).width();
		});
		$('#form-loading ul.js-nav-tabs').width(navWidth);
		// Since PrestaShop 1.7.4.4, the min-height is removed and calculated in an automatic way
		$('.product-page #product-images-dropzone').css('min-height', '175px');
	}
});

function ap5_getProductInformations(productId) {
	return $.ajax({
		type: "POST",
		dataType: "json",
		url: ap5_updateUrl,
		data: {
			getProductExtraInformations: 1,
			productId: productId,
		},
		cache: false
	});
}

ap5_updatePackPriceSimulationAjaxCallDelay = null;
function ap5_updatePackPriceSimulation() {
	if (ap5_updatePackPriceSimulationAjaxCallDelay != null) {
		clearInterval(ap5_updatePackPriceSimulationAjaxCallDelay);
	}
	ap5_updatePackPriceSimulationAjaxCallDelay = setTimeout(ap5_updatePackPriceSimulationCallback, 100);
}

function ap5_updatePackPriceSimulationCallback() {
	if (typeof(pm_advancedpack) != 'undefined') {
		var productFormValuesSerialized = $('#form.product-page').serialize();
	} else {
		var productFormValuesSerialized = $('#product_form').serialize();
	}
	$.ajax({
		type: "POST",
		dataType: "json",
		url: ap5_updateUrl,
		data: {
			updatePackPriceSimulation: 1,
			productFormValues: productFormValuesSerialized
		},
		cache: false,
		success: function(jsonData, textStatus, jqXHR) {
			if (jsonData != undefined) {
				if (jsonData.idTaxRulesGroup != undefined) {
					ap5_packIdTaxRulesGroup = jsonData.idTaxRulesGroup;
					$('.ap5-fixed-pack-price-with-taxes, .ap5-fixed-pack-price-without-taxes, .ap5-tax-display-alert').removeClass('ap5-admin-hide hide');
					if (ap5_packIdTaxRulesGroup == null || ap5_packIdTaxRulesGroup !== 0)
						$('.ap5-fixed-pack-price-with-taxes, .ap5-tax-display-alert').addClass('ap5-admin-hide hide');
					if (ap5_packIdTaxRulesGroup <= 0)
						$('.ap5-fixed-pack-price-without-taxes').addClass('ap5-admin-hide hide');
				}
				if (jsonData.advancedStockManagementAlert != undefined) {
					if (jsonData.advancedStockManagementAlert)
						$('.ap5-stock-management-alert').removeClass('ap5-admin-hide hide');
					else
						$('.ap5-stock-management-alert').addClass('ap5-admin-hide hide');
				}
				if (jsonData.html != undefined)
					$('#ap5-admin-pack-price-simulation').replaceWith(jsonData.html);
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
		},
		complete: function(jqXHR, textStatus) {
		}
	});
}

function ap5_updatePriceRulesForm() {
	$('.ap5_fixed_pack_price').prop('required', false).attr('min', '0');
	if ($('input.ap5_price_rules:checked').val() == 1) {
		$('#ap5_price_rules_1_configuration').removeClass('ap5-admin-hide hide');
		$('#ap5_price_rules_3_configuration, #ap5_price_rules_4_configuration').addClass('ap5-admin-hide hide');
		$('.ap5_discountCell').addClass('ap5-admin-hide hide');
		$('.ap5_useReduc-container').removeClass('ap5-admin-hide hide');
	} else if ($('input.ap5_price_rules:checked').val() == 2) {
		$('#ap5_price_rules_1_configuration, #ap5_price_rules_3_configuration, #ap5_price_rules_4_configuration').addClass('hide ap5-admin-hide');
		$('.ap5_discountCell').removeClass('ap5-admin-hide hide');
		$('.ap5_useReduc-container').removeClass('ap5-admin-hide hide');
	} else if ($('input.ap5_price_rules:checked').val() == 3) {
		$('#ap5_price_rules_1_configuration, #ap5_price_rules_4_configuration').addClass('ap5-admin-hide hide');
		$('#ap5_price_rules_3_configuration').removeClass('ap5-admin-hide hide');
		$('.ap5_discountCell').addClass('ap5-admin-hide hide');
		$('.ap5_useReduc').attr('checked', false);
		if ($('#ap5-pack-content-table input#ap5_useReduc-all').is(':checked')) {
			$('#ap5-pack-content-table input#ap5_useReduc-all').prop('checked', false);
		}
		$('.ap5_useReduc-container').addClass('ap5-admin-hide hide');
		$('.ap5_fixed_pack_price').prop('required', true).attr('min', '0.000001');
	} else if ($('input.ap5_price_rules:checked').val() == 4) {
		$('#ap5_price_rules_1_configuration, #ap5_price_rules_3_configuration').addClass('hide ap5-admin-hide');
		$('#ap5_price_rules_4_configuration').removeClass('ap5-admin-hide hide');
		$('.ap5_discountCell').addClass('ap5-admin-hide hide');
		$('.ap5_useReduc-container').removeClass('ap5-admin-hide hide');
	}
	ap5_updatePackFields($('input.ap5_price_rules:checked').val());
}

function ap5_updatePackFields(priceRule) {
	if (priceRule == 1) {
		$('.ap5_reductionAmount').val($('input#ap5_global_percentage_discount').val());
		$('.ap5_reductionType option:selected').attr("selected", false);
		$('.ap5_reductionType option[value=percentage]').attr('selected', true);
		$('.ap5_combinationDiscount:checked').prop('checked', false).trigger('change');
	} else if (priceRule == 3) {
		$('.ap5_reductionAmount').val(0);
		$('.ap5_reductionType option:selected').attr("selected", false);
		$('.ap5_reductionType option[value=percentage]').attr('selected', true);
		$('.ap5_combinationDiscount:checked').prop('checked', false).trigger('change');
	} else if (priceRule == 4) {
		$('.ap5_reductionAmount').val(0);
		$('.ap5_reductionType option:selected').attr("selected", false);
		$('.ap5_reductionType option[value=percentage]').attr('selected', true);
		$('.ap5_combinationDiscount:checked').prop('checked', false).trigger('change');
	}
}

function ap5_getNbProducts() {
	return $("#ap5-pack-content-table>tbody>tr:not(.ap5_combinationsContainer):not(.ap5_customizationFieldsContainer)").length;
}

function ap5_disableProductEdit() {
	// PS 1.5 & 1.6
	$('div.productTabs').parent().remove();
}
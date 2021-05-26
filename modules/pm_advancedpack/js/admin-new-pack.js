var ap5_packIdTaxRulesGroup = null;
var ap5_packIdWarehouse = null;
var ap5_saveElement = null;
var ap5_saveAndStayElement = null;
function ap5_hideUnusedFields() {
	// PS 1.6
	$('#product-pack-container').next('hr').addClass('ap5-admin-hide hide');
	$('input[name=type_product]').parents('div.form-group').addClass('ap5-admin-hide hide');	
	$('a#page-header-desc-product-duplicate, a#page-header-desc-product-stats').parents('li').addClass('ap5-admin-hide hide');
	$('#product-suppliers').next('.panel').addClass('ap5-admin-hide hide');
	// PS 1.5
	$('input[name=type_product]').parent().next('div.separation').addClass('ap5-admin-hide hide');
	$('input[name=type_product]').parent().addClass('ap5-admin-hide hide');	
	$('a#desc-product-duplicate, a#desc-product-stats').parents('li').addClass('ap5-admin-hide hide');
	$('#suppliers_accordion').removeAttr('style');
	$('#product-tab-content-Suppliers > table:eq(0)').nextAll().addClass('ap5-admin-hide hide');
	if (typeof(ap5_displayMode) != 'undefined' && ap5_displayMode == 'advanced') {
		// PS 1.6
		$('input[name=inputAccessories]').parents('div.form-group').addClass('ap5-admin-hide hide');
		// PS 1.5
		$('input[name=inputAccessories]').parents('tr').addClass('ap5-admin-hide hide');
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
	if ($('#ap5-pack-content-table input.ap5_useReduc:not(:checked)').size() > 0 || ap5_getNbProducts() < 2) {
		$('input#ap5_allow_remove_product').attr('checked', false);
		$('input#ap5_allow_remove_product').attr('disabled', true);
	} else {
		$('input#ap5_allow_remove_product').attr('disabled', false);
	}
}

function ap5_initNewPackFields() {
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

	// PS 1.5
	ap5_saveElement = $('a#desc-product-save').clone(true);
	ap5_saveAndStayElement = $('a#desc-product-save-and-stay').clone(true);
	$('a#desc-product-save, a#desc-product-save-and-stay').unbind('click');

	$('a#desc-product-save').bind('click', function(event) {
		if ($('#ap5-pack-content-table>tbody>tr:not(.ap5_combinationsContainer):not(.ap5_customizationFieldsContainer)').size() == 0) {
			alert(ap5_atLeastOneProductMessage);
			return false;
		} else if ($('#ap5-pack-content-table>tbody>tr:not(.ap5_combinationsContainer):not(.ap5_customizationFieldsContainer)').size() < 2) {
			if (!confirm(ap5_atLeastTwoProductMessage)) {
				$('a#link-ModulePm_advancedpack').trigger('click');
				return false;
			}
		}
		$(ap5_saveElement).trigger('click');
	});
	$('a#desc-product-save-and-stay').bind('click', function(event) {
		if ($('#ap5-pack-content-table>tbody>tr:not(.ap5_combinationsContainer):not(.ap5_customizationFieldsContainer)').size() == 0) {
			alert(ap5_atLeastOneProductMessage);
			return false;
		} else if ($('#ap5-pack-content-table>tbody>tr:not(.ap5_combinationsContainer):not(.ap5_customizationFieldsContainer)').size() < 2) {
			if (!confirm(ap5_atLeastTwoProductMessage)) {
				$('a#link-ModulePm_advancedpack').trigger('click');
				return false;
			}
		}
		$(ap5_saveAndStayElement).trigger('click');
	});
	// /PS 1.5

	$(document).on('click', 'button[name=submitAddproductAndStay], button[name=submitAddproduct]', function() {
		if ($('#ap5-pack-content-table>tbody>tr:not(.ap5_combinationsContainer):not(.ap5_customizationFieldsContainer)').size() == 0) {
			alert(ap5_atLeastOneProductMessage);
			return false;
		} else if ($('#ap5-pack-content-table>tbody>tr:not(.ap5_combinationsContainer):not(.ap5_customizationFieldsContainer)').size() < 2) {
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
			if ($('#ap5-pack-content-table>tbody tr').size() == 0) {
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
		$('table#ap5-pack-combination-table-' + $(this).attr('data-id-product-pack') + ' tr.highlighted').removeClass('highlighted');
		$(this).closest('tr').addClass('highlighted');
		// Update pack trigger (major)
		ap5_packUpdated(false, true);
	});

	$(document).on('click', 'input.ap5_combinationInclude', function(event) {
		if ($('table#ap5-pack-combination-table-' + $(this).attr('data-id-product-pack') + ' input.ap5_combinationInclude:checked').size() == 0) {
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
			$('input[id^="ap5_combinationReductionAmount-' + $(this).attr('data-id-product-pack') + '-"]').val($(this).val());
		}
	});
	$(document).on('change', 'select.ap5_reductionType', function() {
		if (!$('#ap5_combinationDiscount-' + $(this).attr('data-id-product-pack')).is(':checked')) {
			$('select[id^="ap5_combinationReductionType-' + $(this).attr('data-id-product-pack') + '-"] option[value="' + $(this).val() + '"]').attr('selected', true).trigger('change');
		}
	});
	$(document).on('change', 'input.ap5_combinationDiscount', function() {
		// Update pack trigger (major)
		ap5_packUpdated(false, true);

		$('select[id="ap5_reductionType-' + $(this).attr('data-id-product-pack') + '"]').attr('disabled', $(this).is(':checked'));
		$('input[id="ap5_reductionAmount-' + $(this).attr('data-id-product-pack') + '"]').attr('disabled', $(this).is(':checked'));
		$('select[id^="ap5_combinationReductionType-' + $(this).attr('data-id-product-pack') + '-"]').attr('disabled', !$(this).is(':checked'));
		$('input[id^="ap5_combinationReductionAmount-' + $(this).attr('data-id-product-pack') + '-"]').attr('disabled', !$(this).is(':checked'));		
		if ($(this).is(':checked')) {
			$('select[id^="ap5_combinationReductionType-' + $(this).attr('data-id-product-pack') + '-"]').parent().removeClass('ap5_inputDisabled');
		} else {
			$('select[id^="ap5_combinationReductionType-' + $(this).attr('data-id-product-pack') + '-"]').parent().addClass('ap5_inputDisabled');
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
	if ($('div#product-new-pack').hasClass('ps-15'))
		$('.label-tooltip, .help-tooltip').tooltip();
	ap5_setProductPositions();
}

function ap5_addNewProductToPack(event, data, formatted) {
	if (data == null)
		return false;
	var productId = parseInt(data[1]);
	var productName = data[0];
	var getWarehouseIdResult = false;
	ap5_getProductInformations(productId).success(function(jsonData, textStatus, jqXHR) {
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

function ap5_updatePackPriceSimulation() {
	$.ajax({
		type: "POST",
		dataType: "json",
		url: ap5_updateUrl,
		data: {
			updatePackPriceSimulation: 1,
			productFormValues: $('#product_form').serialize()
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
	return $("#ap5-pack-content-table>tbody>tr:not(.ap5_combinationsContainer):not(.ap5_customizationFieldsContainer)").size();
}

function ap5_disableProductEdit() {
	// PS 1.5
	$('div#product_toolbar').remove();
	$('div#content > form').remove();
	// PS 1.5 & 1.6
	$('div.productTabs').parent().remove();
}

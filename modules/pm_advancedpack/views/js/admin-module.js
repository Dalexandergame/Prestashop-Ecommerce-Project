var currentColorPicker = false;
$(document).ready(function() {
	$('div#addons-rating-container p.dismiss a').click(function() {
		$('div#addons-rating-container').hide(500);
		$.ajax({type : "GET", url : window.location+'&dismissRating=1' });
		return false;
	});
	// Gradient
	$('.makeGradient').unbind('click').click(function() {
		var e = $(this).parent('span').prev('span');
		if($(e).css('display') == 'inline') {
			$(this).parent('span').prev('span').hide();
			$('input', $(this).parent('span').prev('span')).val('');
		}
		else
			$(this).parent('span').prev('span').show();
	});
	// Color picker
	$("input.colorPickerInput").each(function() {
		if ($(this).val() != '') {
			$(this).css('borderLeft', '5px solid ' + $(this).val());
		}
	});
	$("input.colorPickerInput").colpick({
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val('#' + hex);
			$(el).css('borderLeft', '5px solid #' + hex);
			$(el).colpickHide();
		},
		onBeforeShow: function () {
			currentColorPicker = $(this);
			$(this).colpickSetColor(this.value);
		},
		onChange: function (hsb, hex, rgb) {
			$(currentColorPicker).val('#' + hex);
			$(currentColorPicker).css('borderLeft', '5px solid #' + hex);
			if ($(currentColorPicker).parent("div").find("span input.colorPickerInput").length && $(currentColorPicker).parent("div").find("span input.colorPickerInput").val() == '') {
				$(currentColorPicker).parent("div").find("span input.colorPickerInput").val('#' + hex);
				$(currentColorPicker).parent("div").find("span input.colorPickerInput").css('borderLeft', '5px solid #' + hex);
			}
		}
	}).bind("keyup", function() {
		$(this).colpickSetColor(this.value);
	});

	if ($('.ap-info').length > 0) {
		$('.close').on('click', function() {
			$('.ap-info').addClass('hide');
		});
	}

	showHideElementsOnLoading();
	showRelatedOptions();

	// Add "Add a new pack" item into header toolbar
	if (!$('#page-header-desc-product-new_pack').size() && $('a#desc-module-back').size() == 1 && $('a#desc-module-back').parents('ul').size() == 1 && $('#add-pack-hint').size() == 1) {
		$('a#desc-module-back').parents('ul').append('<li><a id="page-header-desc-product-new_pack" class="toolbar_btn pointer" href="' + $('#add-pack-hint').attr('href') + '" title="' + $('#add-pack-hint').data('header-label') + '"><i class="process-icon-new"></i><div>' + $('#add-pack-hint').data('header-label') + '</div></a></li>');
	}
});

function initTips(e) {
	$(document).ready(function() { $(e+"-tips").tipTip(); });
}

function showRelatedOptions() {
	$(document).on('change', 'input[name="displayMode"]', function() {
    	if ($(this).val() == 1) {
 			$(".advancedModeOption").show();
 		} else {
 			$(".advancedModeOption").hide();
 		}
    });
	$(document).on('change', 'input[name="enablePackCrossSellingBlock"]', function() {
    	if ($(this).val() == 1) {
 			$(".CrossSellingOption").show();
 		} else {
 			$(".CrossSellingOption").hide();
 		}
    });
    $(document).on('change', 'input[name="postponeUpdatePackSpecificPrice"]', function() {
    	if ($(this).val() == 1) {
			$('.postPoneOption').show();
		} else {
			$('.postPoneOption').hide();
		}
	});
}

function showHideElementsOnLoading() {
	if ($('#enablePackCrossSellingBlock_off').is(':checked')) {
		$('.CrossSellingOption').hide();
	}

	if ($('#displayMode_off').is(':checked')) {
		$('.advancedModeOption').hide();
	}

	if ($('#postponeUpdatePackSpecificPrice_off').is(':checked')) {
		$('.postPoneOption').hide();
	}
}
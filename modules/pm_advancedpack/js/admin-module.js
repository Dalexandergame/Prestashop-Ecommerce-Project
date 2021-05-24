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
		if ($(this).val() != '')
			$(this).css('backgroundColor', $(this).val());
	});
	$("input.colorPickerInput").colpick({
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val('#' + hex);
			$(el).css('backgroundColor', '#' + hex);
			$(el).colpickHide();
		},
		onBeforeShow: function () {
			currentColorPicker = $(this);
			$(this).colpickSetColor(this.value);
		},
		onChange: function (hsb, hex, rgb) {
			$(currentColorPicker).val('#' + hex);
			$(currentColorPicker).css('backgroundColor', '#' + hex);
			if ($(currentColorPicker).parent("div").find("span input.colorPickerInput").length && $(currentColorPicker).parent("div").find("span input.colorPickerInput").val() == '') {
				$(currentColorPicker).parent("div").find("span input.colorPickerInput").val('#' + hex);
				$(currentColorPicker).parent("div").find("span input.colorPickerInput").css('backgroundColor', '#' + hex);
			}
		}
	}).bind("keyup", function() {
		$(this).colpickSetColor(this.value);
	});

	showRelatedOptions($('#displayMode'));

	if ($('#enablePackCrossSellingBlock_on:checked').val()) {
		showRelatedOptions($('#enablePackCrossSellingBlock_on'));
	} else {
		showRelatedOptions($('#enablePackCrossSellingBlock_off'));
	}

	if ($('#postponeUpdatePackSpecificPrice_on:checked').val()) {
		showRelatedOptions($('#postponeUpdatePackSpecificPrice_on'));
	} else {
		showRelatedOptions($('#postponeUpdatePackSpecificPrice_off'));
	}
});
function initTips(e) {
	$(document).ready(function() { $(e+"-tips").tipTip(); });
}
function showRelatedOptions(e) {
	if ($(e).val() == 1 || ($(e).attr('id') == 'displayMode' && $(e).val() == 'advanced')) {
		$('#' + $(e).attr('name') + '-container').show();
	} else {
		$('#' + $(e).attr('name') + '-container').hide();
	}
}

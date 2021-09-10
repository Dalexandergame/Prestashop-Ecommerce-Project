if (typeof($) != 'undefined') {
	$(document).ready(function() {
		if (typeof(ap5_attributePackId) != 'undefined') {
			$('#attribute_group optgroup#' + ap5_attributePackId).remove();
			$('#attribute-group-' + ap5_attributePackId).remove();
			$('a.attribute-group-name[href="#attribute-group-' + ap5_attributePackId + '"]').parents('.attribute-group').remove();
		}
	});
	if (typeof(ap5_attributePackId) != 'undefined') {
		$(document).ajaxSuccess(function() {
			if ($('#add_new_combination #attribute_group>option[value=' + ap5_attributePackId + ']').length > 0) {
				$('#add_new_combination #attribute_group>option[value=' + ap5_attributePackId + ']').remove();
				$('#add_new_combination #attribute_group').trigger('change');
			}
		});
	}
}
/**
 *  * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2018 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 */
(function($) {
	var twintcwBuildHiddenFormFields = function(fields) {
		var output = '';
		for ( var key in fields) {
			if (fields.hasOwnProperty(key)) {
				output += '<input type="hidden" value="'
						+ fields[key].replace('"', '&quot;') + '" name="' + key
						+ '" />';
			}
		}
		return output;
	};

	var aliasSubmissionHandler = function(event) {
		var completeForm = $(event.target)
				.parents('.twintcw-payment-form');
		var completeFormId = completeForm.attr('id');

		$("#" + completeFormId).animate({
			opacity : 0.3,
			duration : 30,
		});

		$.ajax({
			type : 'POST',
			url : $('.twintcw-alias-pane').attr('data-ajax-action'),
			data : $('.twintcw-alias-pane').find(':input').serialize()
					+ '&ajaxAliasForm=true',
			success : function(response) {
				var newPane = $("#" + completeFormId, $(response));
				if (newPane.length > 0) {
					var newContent = newPane.html();
					$("#" + completeFormId).html(newContent);

					// Execute the JS to make sure any JS inside newContent is
					// executed
					$(response).each(function(k, e) {
						if (typeof e === 'object' && e.nodeName == 'SCRIPT') {
							jQuery.globalEval(e.innerHTML);
						}
					});
					attachEventHandlers();
				}

				$("#" + completeFormId).animate({
					opacity : 1,
					duration : 100,
				});
			},
		});

		return false;
	};

	var aliasChangedHandler = function(event) {
		aliasSubmissionHandler(event);
	};

	var aliasSubmitClickedHandler = function(event) {
		$(this).parents('.twintcw-alias-pane').append(
				'<input type="hidden" name="' + $(this).attr('name')
						+ '" value="' + $(this).val() + '" />');
		event.preventDefault();
		aliasSubmissionHandler(event);
	};

	var ajaxSubmissionHandler = function(event) {
		$(this).hide();
		var methodName = ajaxForm.attr('data-method-name');
		var callback = window['twintcw_ajax_submit_callback_'
				+ methodName];

		var validationCallback = window['cwValidateFields' + 'twintcw_'
				+ methodName + '_'];
		if (typeof validationCallback != 'undefined') {
			validationCallback(function(valid) {
				ajaxFormTwintCw_ValidationSuccess(ajaxForm);
			}, ajaxFormTwintCw_ValidationFailure);
			return;
		}
		ajaxFormTwintCw_ValidationSuccess($(this));
		return;
	};

	var createTransactionSubmissionHandler = function(event) {
		if (window.twintcwAjaxRequestInProgress === true) {
			return false;
		}
		window.twintcwAjaxRequestInProgress = true;

		var validationCallback = window['cwValidateFields' + 'twintcw_'
				+ event.data.methodName + '_'];
		if (typeof validationCallback != 'undefined') {
			validationCallback(function(valid) {
				createTransactionTwintCw_ValidationSuccess(
						event.data.form, event.data.ajaxUrl);
			}, createTransactionTwintCw_ValidationFailure);
			return false;
		}
		createTransactionTwintCw_ValidationSuccess(event.data.form,
				event.data.ajaxUrl);
		return false;
	};

	var attachEventHandlers = function() {
		$('*').off('.twintcw');

		// normal form submit
		// check if alias is selected
		// then call function

		$('.twintcw-alias-form').find("input[type='checkbox']").on(
				'change.twintcw', aliasChangedHandler);
		$('.twintcw-alias-form').find("select").on(
				'change.twintcw', aliasChangedHandler);
		$('.twintcw-alias-form').find("input[type='submit']").on(
				'click.twintcw', aliasSubmitClickedHandler);

		$('.twintcw-ajax-authorization-form').each(
				function() {
					var ajaxForm = $(this);
					ajaxForm.parents('.twintcw-payment-form form').on(
							'twintcw.send', ajaxSubmissionHandler);
				});

		$('.twintcw-create-transaction')
				.each(
						function() {
							var ajaxUrl = $(this).attr('data-ajax-url');
							var sendFormDataBack = $(this).attr(
									'data-send-form-back') == 'true' ? true
									: false;
							var form = $(this).children('form');
							var methodName = form.attr('data-method-name');

							var params = {
								'ajaxUrl' : ajaxUrl,
								'sendFormDataBack' : sendFormDataBack,
								'form' : form,
								'methodName' : methodName
							};

							form.on('send.twintcw', params,
									createTransactionSubmissionHandler);
						});

		$('.twintcw-standard-redirection').on('send.twintcw',
				function(e) {
					$(this)[0].originalSubmit();
				});

		$('.twintcw-error-message-inline').on(
				'load.twintcw',
				function(event) {
					forceCurrentPaymentMethod();
				});

		if ($('.twintcw-error-message-inline')[0]) {
			forceCurrentPaymentMethod();
		}

		TwintCwRegisterSubmissionHandling();
	};
    
    var forceCurrentPaymentMethod = function() {
        var btnId = $('.twintcw-error-message-inline').first().parents('.twintcw-payment-form').find('button').last().attr('id')
				.substring(9);
        $('#' + btnId).click();
    }

	var ajaxFormTwintCw_ValidationSuccess = function(ajaxForm) {
		var methodName = ajaxForm.attr('data-method-name');
		var callback = window['twintcw_ajax_submit_callback_'
				+ methodName];
		if (typeof callback == 'undefined') {
			alert("No Ajax callback found.");
		} else {
			callback(ajaxForm.serialize());
		}
		window.TwintCwIsSubmissionRunning = false;
	}

	var ajaxFormTwintCw_ValidationFailure = function(errors, valid) {
		alert(errors[Object.keys(errors)[0]]);
		window.TwintCwIsSubmissionRunning = false;
	}

	var createTransactionTwintCw_ValidationSuccess = function(form,
			ajaxUrl) {
		var data = $(form).find(':input').serializeArray();
		var fields = {}; // must be var, is used later.
		$(data).each(function(index, value) {
			fields[value.name] = value.value;
		});

		form.animate({
			opacity : 0.3,
			duration : 30,
		});
		$
				.ajax({
					type : 'POST',
					url : ajaxUrl,
					data : $(form).serialize(),
					success : function(response) {
						var error = response;
						try {
							var data = $.parseJSON(response);

							if (data.status == 'success') {
								var func = eval('[' + data.callback + ']')[0];
								func();
								return;
							} else {
								error = data.message;
							}
						} catch (e) {
							console.log(e);
						}

						form.animate({
							opacity : 1,
							duration : 100,
						});
						if ($('.twintcw-error-message-inline')[0]) {
							$('.twintcw-error-message-inline').html(
									error);
						} else {
							form
									.prepend("<div class='twintcw-error-message-inline alert alert-danger'>"
											+ error + "</div>");
						}
						window.twintcwAjaxRequestInProgress = false;
					},
				});

	}

	var createTransactionTwintCw_ValidationFailure = function(
			errors, valid) {
		alert(errors[Object.keys(errors)[0]]);
		window.twintcwAjaxRequestInProgress = false;
		window.TwintCwIsSubmissionRunning = false;
	}

	var TwintCwRegisterSubmissionHandling = function() {
		$('.twintcw-payment-form form').each(function() {
			this.originalSubmit = this.submit;

			this.submit = function(evt) {
				if (window.TwintCwIsSubmissionRunning) {
					return;
				}
				window.TwintCwIsSubmissionRunning = true;
				$(this).trigger('send.twintcw');
			}
		});
	}

	$(document).ready(function() {
		// Make JS required section visible
		$('.twintcw-javascript-required').show();

		attachEventHandlers();
		
		prestashop.on('steco_event_updated', function(){
		    attachEventHandlers();
		})
	});

}(jQuery));
(function(root, factory) {
	if (typeof define === 'function' && define.amd) {
		define("twintRedirect", factory(root));
	} else if (typeof exports === 'object') {
		module.exports = factory(root);
	} else {
		root["twintRedirect"] = factory(root);
	}
})
		(
				window || this,
				function(root) {

					/**
					 * Twint payment base url.
					 */
					var baseUrl = undefined;

					/**
					 * App link object.
					 */
					var appLink = undefined;

					/**
					 * The redirect timeout.
					 */
					var redirectTimeout = 3000;

					/**
					 * App urls.
					 */
					var appUrls = {
						// iOS universal app link
						ios : 'applinks/?al_applink_data={"app_action_type": "TWINT_PAYMENT","extras": {"code": "{token}",},"referer_app_link": {"target_url": "", "url": "", "app_name": "EXTERNAL_WEB_BROWSER"}, "version": "6.0" }',
						// Android intent link
						android : 'intent://payment#Intent;action=ch.twint.action.TWINT_PAYMENT;scheme=twint;S.code={token};S.startingOrigin=EXTERNAL_WEB_BROWSER;S.browser_fallback_url=;end'
					}

					var init = function(tokenValue) {
						var payWithTwintBtn = document
								.getElementById("pay-with-twint");
						if (isIOS()) {
							var appChooserContainer = document
									.getElementById("app-chooser-container");
							appChooserContainer.style.display = '';

							payWithTwintBtn.disabled = true;

							var appChooser = document
									.getElementById("app-chooser");
							appChooser
									.addEventListener(
											"change",
											function() {
												payWithTwintBtn.disabled = appChooser.value === undefined
														|| appChooser.value == '';
											});
						}

						payWithTwintBtn.addEventListener('click', function() {
							parent.location = getAppUrl(tokenValue);
						}, false);

						document.getElementById("copy-token").addEventListener(
								'click', function() {
									copyTokenToClipboard(tokenValue);
								}, false);
					}

					// -------------------------------------------------------------------------------
					// HELPERS
					// -------------------------------------------------------------------------------
					/**
					 * Console log wrapper
					 * 
					 * @private
					 */
					var log = function(msg, args) {
						if (args) {
							console.log(msg, args);
						} else {
							console.log(msg);
						}
					}

					/**
					 * Check if the user-agent is Android
					 * 
					 * @private
					 * @returns {Boolean} true/false
					 */
					var isAndroid = function() {
						return navigator.userAgent.match('Android');
					}

					var copyTokenToClipboard = function(token) {
						var el = document.getElementById("tokenHolder");

						el.contentEditable = true;
						el.readOnly = true;

						var range = document.createRange();
						range.selectNodeContents(el);

						var sel = window.getSelection();
						sel.removeAllRanges();
						sel.addRange(range);

						el.setSelectionRange(0, 30);

						document.execCommand("copy");

						sel.removeAllRanges();
						el.blur();

						el.contentEditable = false;
					}

					/**
					 * Check if the user-agent is Chrome
					 * 
					 * @private
					 * @returns {Boolean} true/false
					 */
					var isChrome = function() {
						return navigator.userAgent.match('Chrome')
								&& !navigator.userAgent.match('SamsungBrowser');
					}

					/**
					 * Check if the user-agent is iPad/iPhone/iPod
					 * 
					 * @private
					 * @returns {Boolean} true/false
					 */
					var isIOS = function() {
						return navigator.userAgent.match('iPad')
								|| navigator.userAgent.match('iPhone')
								|| navigator.userAgent.match('iPod');
					}

					/**
					 * Get app url.
					 * 
					 * @private
					 * @returns {String}
					 */
					var getAppUrl = function(token) {
						var url;
						if (isIOS()) {
							var appChooser = document
									.getElementById("app-chooser");
							var issuer = appChooser.options[appChooser.selectedIndex].value;
							url = issuer + appUrls.ios;
						} else {
							url = appUrls.android;
						}
						return url.replace("{token}", token);
					}

					return {
						init : init
					};
				});
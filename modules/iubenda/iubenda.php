<?php
/**
* iubenda Cookie Solution
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author iubenda https://www.iubenda.com
* @copyright 2018-2020, iubenda s.r.l
* @license http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

if ( ! defined( '_PS_VERSION_' ) )
	exit;

/**
 * iubenda class.
 *
 * @class iubenda
 */
class iubenda extends Module {

	/**
	 * Class constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		// plugin slug
		$this->name = 'iubenda';

		// plugin type
		$this->tab = 'front_office_features';

		// plugin version
		$this->version = '1.1.16';

		// plugin author
		$this->author = 'iubenda';

		// plugin instance
		$this->need_instance = 1;

		// plugin compatible versions
		$this->ps_versions_compliancy = array( 'min' => '1.6', 'max' => '1.7.99.99' );

		// plugin bootstrap
		$this->bootstrap = true;

		parent::__construct();

		// plugin full name
		$this->displayName = $this->l( 'iubenda Cookie Solution' );

		// plugin description
		$this->description = $this->l( 'iubenda Cookie Solution allows you to manage all aspects of cookie law on Prestashop.' );

		// plugin uninstall string
		$this->confirmUninstall = $this->l( 'Are you sure you want to delete these details?' );

		// iubenda tag types
		$this->iubenda_tag_types = array(
			0 => $this->l( 'Not set' ),
			1 => $this->l( 'Strictly necessary' ),
			2 => $this->l( 'Basic interactions & functionalities' ),
			3 => $this->l( 'Experience enhancement' ),
			4 => $this->l( 'Analytics' ),
			5 => $this->l( 'Targeting & Advertising' )
		);
	}

	/**
	 * Install hook.
	 *
	 * @return object
	 */
	public function install() {
		return parent::install() && $this->registerHook( 'displayHeader' );
	}

	/**
	 * Uninstall hook.
	 *
	 * @return bool
	 */
	public function uninstall() {
		if ( ! parent::uninstall() )
			return false;

		return true;
	}

	/**
	 * Save module options.
	 *
	 * @return mixed
	 */
	public function getContent() {
		$output = '';

		// save data?
		if ( Tools::isSubmit( 'submit' . $this->name ) ) {
			// get languages
			$languages = Language::getLanguages( true );

			// get current HTML purifier setting
			$use_html_purifier = (bool) Configuration::get( 'PS_USE_HTMLPURIFIER' );

			// is it turned on? then turn it off
			if ( $use_html_purifier )
				Configuration::updateValue( 'PS_USE_HTMLPURIFIER', false );

			// run through all languages
			foreach ( $languages as $lang ) {
				// get input name
				$name = 'IUBENDA_CODE_' . Tools::strtoupper( $lang['iso_code'] );

				// get input value
				$code = ! empty( Tools::getValue( $name ) ) ? Tools::getValue( $name ) : '';

				// update code for specified language
				Configuration::updateValue( $name, $code, true );
			}

			// turn on purifier if it was turned off
			if ( $use_html_purifier )
				Configuration::updateValue( 'PS_USE_HTMLPURIFIER', true );

			// get settings
			$block_scripts = strval( Tools::getValue( 'IUBENDA_BLOCK_SCRIPTS' ) );
			$experimental_engine = strval( Tools::getValue( 'IUBENDA_EXPERIMENTAL_ENGINE' ) );

			// get custom scripts
			$custom_scripts = Tools::getValue( 'IUBENDA_CUSTOM_SCRIPTS' );

			// check scripts
			if ( ! empty( $custom_scripts ) && ! empty( $custom_scripts['script'] ) && ! empty( $custom_scripts['type'] ) ) {
				$scripts = array();

				// first field is template
				if ( count( $custom_scripts['script'] ) > 1 ) {
					// run through all scripts
					foreach ( $custom_scripts['script'] as $number => $script ) {
						// trim value to skip hidden template one
						$trimmed = trim( $script );

						// add casted type
						if ( $trimmed !== '' )
							$scripts[$trimmed] = (int) $custom_scripts['type'][$number];
					}
				}

				$custom_scripts = $scripts;
			} else
				$custom_scripts = array();

			// get custom iframes
			$custom_iframes = Tools::getValue( 'IUBENDA_CUSTOM_IFRAMES' );

			// check iframes
			if ( ! empty( $custom_iframes ) && ! empty( $custom_iframes['iframe'] ) && ! empty( $custom_iframes['type'] ) ) {
				$iframes = array();

				// first field is template
				if ( count( $custom_iframes['iframe'] ) > 1 ) {
					// run through all iframes
					foreach ( $custom_iframes['iframe'] as $number => $iframe ) {
						// trim value to skip hidden template one
						$trimmed = trim( $iframe );

						// add casted type
						if ( $trimmed !== '' )
							$iframes[$trimmed] = (int) $custom_iframes['type'][$number];
					}
				}

				$custom_iframes = $iframes;
			} else
				$custom_iframes = array();

			// update settings
			Configuration::updateValue( 'IUBENDA_BLOCK_SCRIPTS', ! empty( $block_scripts ) );
			Configuration::updateValue( 'IUBENDA_EXPERIMENTAL_ENGINE', ! empty( $experimental_engine ) );
			Configuration::updateValue( 'IUBENDA_CUSTOM_SCRIPTS', json_encode( $custom_scripts ) );
			Configuration::updateValue( 'IUBENDA_CUSTOM_IFRAMES', json_encode( $custom_iframes ) );

			// display message
			$output .= $this->displayConfirmation( $this->l( 'Settings updated' ) );
		}

		// enqueue JavaScript file
        $this->context->controller->addJS( $this->_path . 'js/admin.js' );

		return $output . $this->displayForm();
	}

	/**
	 * Render list of tag types.
	 *
	 * @param string $type IFRAMES or SCRIPTS
	 * @param int $selected Selected script or iframe
	 * @return string
	 */
	function renderTagTypes( $type, $selected ) {
		$html = '<select name="IUBENDA_CUSTOM_' . $type . '[type][]">';

		foreach ( $this->iubenda_tag_types as $tag_id => $tag_name ) {
			$html .= '<option value="' . $tag_id . '"' . ( $selected === $tag_id ? ' selected="selected"' : '' ) . '>' . $tag_name . '</option>';
		}

		return $html . '</select>';
	}

	/**
	 * Render custom scripts content.
	 *
	 * @return string
	 */
	function generateCustomScriptsContent() {
		// scripts template
		$html = '
		<div id="custom-script-field-template" class="template-field" style="display: none;">
			<div class="col-lg-8">
				<input type="text" name="IUBENDA_CUSTOM_SCRIPTS[script][]" value="" placeholder="' . $this->l( 'Enter custom script' ) . '">
			</div>
			<div class="col-lg-3">
				' . $this->renderTagTypes( 'SCRIPTS', 0 ) . '
			</div>
			<div class="col-lg-1">
				<a href="#" class="remove-custom-script-field btn btn-default" title="' . $this->l( 'Remove' ) . '">-</a>
			</div>
			<p class="clearfix"></p>
		</div>';

		// get scripts
		$scripts = trim( Configuration::get( 'IUBENDA_CUSTOM_SCRIPTS' ) );

		// empty scripts?
		if ( empty( $scripts ) ) {
			$scripts = array();
		} else {
			// get scripts
			$data = json_decode( $scripts, true );

			// valid scripts? new format
			if ( json_last_error() == JSON_ERROR_NONE ) {
				if ( ! empty( $data ) && is_array( $data ) )
					$scripts = $data;
				else
					$scripts = array();
			// old scripts format
			} else {
				// filter scripts
				$new_scripts = array_filter( array_map( 'trim', explode( "\n", str_replace( "\r", '', $scripts ) ) ) );
				$scripts = array();

				// set all old scripts to 0 type
				foreach ( $new_scripts as $script ) {
					$scripts[$script] = 0;
				}
			}
		}

		// any scripts?
		if ( ! empty( $scripts ) ) {
			foreach ( $scripts as $script => $type ) {
				// add script field
				$html .= '
				<div class="custom-script-field">
					<div class="col-lg-8">
						<input type="text" name="IUBENDA_CUSTOM_SCRIPTS[script][]" value="' . $script . '" placeholder="' . $this->l( 'Enter custom script' ) . '">
					</div>
					<div class="col-lg-3">
						' . $this->renderTagTypes( 'SCRIPTS', $type ) . '
					</div>
					<div class="col-lg-1">
						<a href="#" class="remove-custom-script-field btn btn-default" title="' . $this->l( 'Remove' ) . '">-</a>
					</div>
					<p class="clearfix"></p>
				</div>';
			}
		}

		// add new script button
		$html .= '<a href="#" class="add-custom-script-field btn btn-default">Add New Script</a>';

		return $html;
	}

	/**
	 * Render custom iframes content.
	 *
	 * @return string
	 */
	function generateCustomIframesContent() {
		// iframes template
		$html = '
		<div id="custom-iframe-field-template" class="template-field" style="display: none;">
			<div class="col-lg-8">
				<input type="text" name="IUBENDA_CUSTOM_IFRAMES[iframe][]" value="" placeholder="' . $this->l( 'Enter custom iframe' ) . '">
			</div>
			<div class="col-lg-3">
				' . $this->renderTagTypes( 'IFRAMES', 0 ) . '
			</div>
			<div class="col-lg-1">
				<a href="#" class="remove-custom-iframe-field btn btn-default" title="' . $this->l( 'Remove' ) . '">-</a>
			</div>
			<p class="clearfix"></p>
		</div>';

		// get iframes
		$iframes = trim( Configuration::get( 'IUBENDA_CUSTOM_IFRAMES' ) );

		// empty iframes?
		if ( empty( $iframes ) ) {
			$iframes = array();
		} else {
			// get iframes
			$data = json_decode( $iframes, true );

			// valid iframes? new format
			if ( json_last_error() == JSON_ERROR_NONE ) {
				if ( ! empty( $data ) && is_array( $data ) )
					$iframes = $data;
				else
					$iframes = array();
			// old iframes format
			} else {
				// filter iframes
				$new_iframes = array_filter( array_map( 'trim', explode( "\n", str_replace( "\r", '', $iframes ) ) ) );
				$iframes = array();

				// set all old iframes to 0 type
				foreach ( $new_iframes as $iframe ) {
					$iframes[$iframe] = 0;
				}
			}
		}

		// any iframes?
		if ( ! empty( $iframes ) ) {
			foreach ( $iframes as $iframe => $type ) {
				// add iframe field
				$html .= '
				<div class="custom-iframe-field">
					<div class="col-lg-8">
						<input type="text" name="IUBENDA_CUSTOM_IFRAMES[iframe][]" value="' . $iframe . '" placeholder="' . $this->l( 'Enter custom iframe' ) . '">
					</div>
					<div class="col-lg-3">
						' . $this->renderTagTypes( 'IFRAMES', $type ) . '
					</div>
					<div class="col-lg-1">
						<a href="#" class="remove-custom-iframe-field btn btn-default" title="' . $this->l( 'Remove' ) . '">-</a>
					</div>
					<p class="clearfix"></p>
				</div>';
			}
		}

		// add new iframe button
		$html .= '<a href="#" class="add-custom-iframe-field btn btn-default">Add New Iframe</a>';

		return $html;
	}

	/**
	 * Display module options.
	 *
	 * @return string
	 */
	public function displayForm() {
		// get languages
		$languages = Language::getLanguages( true );
		$langs = array();

		// run through all languages
		foreach ( $languages as $lang ) {
			// get language code
			$code = Tools::strtoupper( $lang['iso_code'] );

			// add language
			$langs[] = array(
				'type' => 'textarea',
				'label' => $this->l( 'Code' ) . ' ' . $code,
				'name' => 'IUBENDA_CODE_' . $code,
				'desc' => $this->l( 'Enter the iubenda code.' ),
				'required' => false
			);
		}

		// init array with all plugin fields
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l( 'Settings' )
			),
			'input' => array_merge(
				$langs,
				array(
					array(
						'type' => 'checkbox',
						'label' => $this->l( 'Scripts blocking' ),
						'name' => 'IUBENDA_BLOCK',
						'desc' => '(' . sprintf( $this->l( 'see %s for the list of detected scripts' ), '<a href="https://www.iubenda.com/en/help/4338" target="_blank">' . $this->l( 'our documentation' ) . '</a>' ) . ')',
						'required' => false,
						'values' => array(
							'query' => array(
								array(
									'id_option' => 'SCRIPTS',
									'name' => $this->l( 'Automatically block scripts detected by the plugin.' )
								)
							),
							'id' => 'id_option',
							'name' => 'name'
						)
					),
					array(
						'type' => 'radio',
						'label' => $this->l( 'Parsing engine' ),
						'name' => 'IUBENDA_EXPERIMENTAL_ENGINE',
						'desc' => $this->l( 'Select parsing engine.' ),
						'required'  => true,
						'values' => array(
							array(
								'id' => 'iubenda-primary-engine',
								'value' => 1,
								'label' => $this->l( 'Primary' )
							),
							array(
								'id' => 'iubenda-secondary-engine',
								'value' => 0,
								'label' => $this->l( 'Secondary' )
							)
						)
					),
					array(
						'type' => 'html',
						'name' => 'custom_scripts',
						'label' => $this->l( 'Custom scripts' ),
						'desc' => $this->l( 'Provide a list of custom scripts you\'d like to block and assign their purpose.' ),
						'html_content' => $this->generateCustomScriptsContent(),
						'required' => false
					),
					array(
						'type' => 'html',
						'name' => 'custom_iframes',
						'label' => $this->l( 'Custom iframes' ),
						'desc' => $this->l( 'Provide a list of custom iframes you\'d like to block and assign their purpose.' ),
						'html_content' => $this->generateCustomIframesContent(),
						'required' => false
					)
				)
			),
			'submit' => array(
				'title' => $this->l( 'Save' ),
				'class' => 'btn btn-default pull-right'
			)
		);

		// init HelperForm
		$helper = new HelperForm();

		// set module
		$helper->module = $this;

		// set name
		$helper->name_controller = $this->name;

		// set token
		$helper->token = Tools::getAdminTokenLite( 'AdminModules' );

		// get default language
		$default_lang = (int) Configuration::get( 'PS_LANG_DEFAULT' );

		// languages
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;

		// title and toolbar settings
		$helper->title = $this->displayName;
		$helper->show_toolbar = true;
		$helper->toolbar_scroll = true;
		$helper->submit_action = 'submit' . $this->name;
		$helper->toolbar_btn = array(
			'save' =>
			array(
				'desc' => $this->l( 'Save' ),
				'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name . '&token=' . Tools::getAdminTokenLite( 'AdminModules' )
			),
			'back' => array(
				'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite( 'AdminModules' ),
				'desc' => $this->l( 'Back to list' )
			)
		);

		// load language values
		foreach ( $languages as $lang ) {
			$name = 'IUBENDA_CODE_' . Tools::strtoupper( $lang['iso_code'] );
			$helper->fields_value[$name] = Configuration::get( $name );
		}

		// load other values
		$helper->fields_value['IUBENDA_BLOCK_SCRIPTS'] = Configuration::get( 'IUBENDA_BLOCK_SCRIPTS' );
		$helper->fields_value['IUBENDA_EXPERIMENTAL_ENGINE'] = Configuration::get( 'IUBENDA_EXPERIMENTAL_ENGINE' );

		// return new fields form using HelperForm's generateForm function
		return '
		<div class="panel">
			<div class="panel-heading">' . $this->l( 'Information' ) . '</div>
			<div class="form-wrapper">
				<p>
					<img id="iubenda-logo" width="150" height="auto" src="data:image/png;base64,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" />
				</p>
				<p>' . $this->l( "This plugin is the easiest and most comprehensive way to adapt your Prestashop site to the" ) . ' <a href="https://www.iubenda.com/en/help/4338-cookie-solution-prestashop-plugin-installation-guide" target="_blank">' . $this->l( "European Cookie Law" ) . '</a>. ' . $this->l( "Upon your user's first visit, the plugin will display a banner informing users that your site uses cookies, take care of collecting their consent, of blocking the most popular the cookie-installing scripts and subsequently reactivate these scripts as soon as consent is provided. The basic settings include obtaining consent by a simple scroll action" ) . ' (' . '<a href="https://www.iubenda.com/en/help/5525-cookies-and-eu-data-law-gdpr-requirements#activeconsent" target="_blank">' . $this->l( "the most convenient method" ) . '</a>' . ') ' . $this->l( "and script reactivation without the need to refresh the page." ) . '</p>
				<p><a href="https://www.iubenda.com/en/help/4338-cookie-solution-prestashop-plugin-installation-guide" target="_blank">' . $this->l( "Full installation guide" ) . '</a></p>
				<h4>' . $this->l( "FAQ's" ) . '</h4>
				<p><strong>' . $this->l( "I'd like to know more about the Cookie Law & the GDPR?" ) . '</strong><br/>' . $this->l( 'Read the' ) . ' <a href="https://www.iubenda.com/en/help/5525-cookies-and-eu-data-law-gdpr-requirements" target="_blank">' . $this->l( "complete guide here" ) . '</a>.</p>
				<p><strong>' . $this->l( 'What is the full functionality of the plugin?' ) . '</strong><br/>' . $this->l( 'You can read all about the functionality of the plugin in the dedicated PrestaShop guide.' ) . '</p>
					<p><strong>' . $this->l( 'How do I use this plugin?' ) . '</strong><br/>' . $this->l( 'In order to run the plugin, you need to enter the iubenda code that activates the cookie law banner and the cookie policy in the form below. You can' ) . ' <a href="https://www.iubenda.com/en/cookie-solution" target="_blank">' . $this->l( "generate your cookie banner and code here" ) . '</a> ' . $this->l( "and find detailed info on the Cookie Law and the Cookie Solution in the" ) . ' <a href="https://www.iubenda.com/en/help/1177-iubenda-cookie-law-solution-introduction-and-getting-started" target="_blank">' . $this->l( "dedicated guide here" ) . '</a>.</p>
				<p><strong>' . $this->l( 'Need support for this plugin?' ) . '</strong><br/>' .  $this->l( 'Visit our' ) . ' <a href="https://support.iubenda.com/support/home" target="_blank">' . $this->l( "support forum" ) . '</a>.</p>
			</div>
		</div>' . $helper->generateForm( $fields_form );
	}

	/**
	 * Add JavaScript to site header.
	 *
	 * @return string
	 */
	public function hookDisplayHeader() {
		// get iubenda code
		$code = trim( Configuration::get( 'IUBENDA_CODE_' . Tools::strtoupper( $this->context->language->iso_code ) ) );

		// parse it
		$code = $this->parse_code( $code );

		// valid code?
		if ( $code !== '' ) {
			// inhclude JavaScript code
			return $code . "
			<script type='text/javascript'>
				var iCallback = function(){};

				if('callback' in _iub.csConfiguration) {
					if('onConsentGiven' in _iub.csConfiguration.callback) iCallback = _iub.csConfiguration.callback.onConsentGiven;

					_iub.csConfiguration.callback.onConsentGiven = function() {
						iCallback();

						jQuery('noscript._no_script_iub').each(function (a, b) { var el = jQuery(b); el.after(el.html()); });
					};
				};
			</script>";
		} else
			return '';
	}

	/**
	 * Fix iubenda code to display it correctly.
	 *
	 * @param string $source JavaScript code
	 * @return string
	 */
	public function parse_code( $source ) {
		$source = trim( $source );

		// check HTML content
		preg_match_all( '/(\"(?:html|content)\"(?:\s+)?\:(?:\s+)?)\"((?:.*?)(?:[^\\\\]))\"/s', $source, $matches );

		// found subgroup?
		if ( ! empty( $matches[1] ) && ! empty( $matches[2] ) ) {
			foreach ( $matches[2] as $no => $match ) {
				// replace content with special tags
				$source = str_replace( $matches[0][$no], $matches[1][$no] . '[[IUBENDA_TAG_START]]' . $match . '[[IUBENDA_TAG_END]]', $source );
			}

			// check special tags
			preg_match_all( '/\[\[IUBENDA_TAG_START\]\](.*?)\[\[IUBENDA_TAG_END\]\]/s', $source, $matches_tags );

			// found any matches?
			if ( ! empty( $matches_tags[1] ) ) {
				foreach ( $matches_tags[1] as $no => $match ) {
					// fix end tags
					$source = str_replace( $matches_tags[0][$no], '"' . str_replace( '</', '<\/', $matches[2][$no] ) . '"', $source );
				}
			}
		}

		return $source;
	}


	/**
	 * Integrate with PageCache
	 *
	 * @return string
	 */
	/**
	 * Integrate with PageCache
	 *
	 * @return string
	 */
	public function getJPrestaCacheKey() {
		$signature = md5(json_encode($this->getIubConfig()));

		foreach ( $_COOKIE as $key => $value ) {
			if ( $this->startsWith( $key, '_iub_cs-s' ) || $this->startsWith( $key, '_iub_cs' ) ) {
				$data = json_decode( stripslashes( $value ), true );
				if ( ! isset( $data['timestamp'] ) ) {
					continue;
				}
				$consentData = (int) ( isset( $data['consent'] ) && $data['consent'] == true );
				$purposes['purposes'] = isset( $data['purposes'] ) ? (array) $data['purposes'] : array();
				$purposes['consent'] = $consentData;

				$data = array_merge( $this->getIubConfig(), $purposes );
				$signature = md5(json_encode($data));
			}
		}

		return $signature;
	}

	/**
	 * Get all iubenda configuration in array
	 *
	 * @return array
	 */
	private function getIubConfig() {
		return [
			'iub_block_scripts'  => Configuration::get( 'IUBENDA_BLOCK_SCRIPTS' ),
			'iub_exp_engine'     => Configuration::get( 'IUBENDA_EXPERIMENTAL_ENGINE' ),
			'iub_custom_scripts' => Configuration::get( 'IUBENDA_CUSTOM_SCRIPTS' ),
			'iub_custom_iframes' => Configuration::get( 'IUBENDA_CUSTOM_IFRAMES' ),
			'iub_script' => trim( Configuration::get( 'IUBENDA_CODE_' . Tools::strtoupper( $this->context->language->iso_code ) ) ),
		];
	}

	/**
	 * Check string is start with
	 *
	 * @param $haystack
	 * @param $needles
	 *
	 * @return bool
	 */
	private function startsWith($haystack, $needles)
	{
        foreach ((array) $needles as $needle) {
            if ('' !== $needle && strncmp( $haystack, $needle, strlen( $needle ) ) === 0) {
                return true;
            }
        }

		return false;
	}

}
<?php
/**
* 2018-2020 iubenda s.r.l
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
* @author iubenda s.r.l https://www.iubenda.com
* @copyright 2018-2020 iubenda s.r.l
* @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

/**
 * FrontController class.
 *
 * @class FrontController
 */
class FrontController extends FrontControllerCore
{


	/**
	 * @param $content
	 */
	protected function smartyOutputContent($content)
	{
		$this->context->cookie->write();

		$html = '';

		$theme = $this->context->shop->theme->getName();

		if (is_array($content)) {
			foreach ($content as $tpl) {
				$html .= $this->context->smarty->fetch($tpl, null, $theme . $this->getLayout());
			}
		} else {
			$html = $this->context->smarty->fetch($content, null, $theme . $this->getLayout());
		}

		$html = $this->parse($html);

		Hook::exec('actionOutputHTMLBefore', ['html' => &$html]);
		echo trim($html);
	}

	/**
	 * Parse HTML
	 *
	 * @return string
	 */
	private function parse($html) {
		// iubenda loaded?
		if ( file_exists( _PS_MODULE_DIR_ . 'iubenda/iubenda-cookie-class/iubenda.class.php' ) && Configuration::get( 'IUBENDA_BLOCK_SCRIPTS' ) ) {
			// empty HTML? exit
			if ( $html === '' )
				return $html;

			// include iubenda class
			include_once( _PS_MODULE_DIR_ . 'iubenda/iubenda-cookie-class/iubenda.class.php' );

			// iubenda parser exists?
			if ( class_exists( 'iubendaParser' ) ) {
				// check whether consent was given or bot was detected
				if ( iubendaParser::consent_given() || iubendaParser::bot_detected() ) {
					return $html;
				}

				// get scripts
				$custom_scripts = Configuration::get( 'IUBENDA_CUSTOM_SCRIPTS' );

				// empty scripts?
				if ( empty( $custom_scripts ) ) {
					$custom_scripts = array();
				} else {
					// get scripts
					$data = json_decode( $custom_scripts, true );

					// valid scripts? new format
					if ( json_last_error() == JSON_ERROR_NONE ) {
						if ( ! empty( $data ) && is_array( $data ) ) {
							$custom_iframes = $data;
						} else
							$custom_iframes = array();
					// old scripts format
					} else {
						// filter scripts
						$new_scripts = array_filter( array_map( 'trim', explode( "\n", str_replace( "\r", '', $custom_scripts ) ) ) );
						$custom_scripts = array();

						// set all old scripts to 0 type
						foreach ( $new_scripts as $script ) {
							$custom_scripts[$script] = 0;
						}
					}
				}

				// get iframes
				$custom_iframes = Configuration::get( 'IUBENDA_CUSTOM_IFRAMES' );

				// empty iframes?
				if ( empty( $custom_iframes ) ) {
					$custom_iframes = array();
				} else {
					// get iframes
					$data = json_decode( $custom_iframes, true );

					// valid iframes? new format
					if ( json_last_error() == JSON_ERROR_NONE ) {
						if ( ! empty( $data ) && is_array( $data ) ) {
							$custom_iframes = $data;
						} else
							$custom_iframes = array();
					// old iframes format
					} else {
						// filter iframes
						$new_iframes = array_filter( array_map( 'trim', explode( "\n", str_replace( "\r", '', $custom_iframes ) ) ) );
						$custom_iframes = array();

						// set all old iframes to 0 type
						foreach ( $new_iframes as $iframe ) {
							$custom_iframes[$iframe] = 0;
						}
					}
				}

				// prepare scripts and iframes
				$custom_scripts = $this->iubendaPrepareCustomData( $custom_scripts );
				$custom_iframes = $this->iubendaPrepareCustomData( $custom_iframes );

				// primary parser?
				if ( (int) Configuration::get( 'IUBENDA_EXPERIMENTAL_ENGINE' ) === 1 )
					$iubenda = new iubendaParser( mb_convert_encoding( $html, 'HTML-ENTITIES', 'UTF-8' ), array( 'type' => 'faster', 'scripts' => $custom_scripts, 'iframes' => $custom_iframes ) );
				// secondary parser
				else
					$iubenda = new iubendaParser( $html, array( 'type' => 'page', 'scripts' => $custom_scripts, 'iframes' => $custom_iframes ) );

				// parse HTML
				$html = $iubenda->parse();
			}
		}

		// return new content
		return $html;
	}

	/**
	 * Prepare scripts and iframes to new format.
	 *
	 * @param array $data All scripts or iframes
	 * @return array
	 */
	public function iubendaPrepareCustomData( $data ) {

		// decode data if it string [JSON]
		$data = is_string( $data ) ? json_decode( $data, true ) : $data;

		// Escape if the data is not arrayable
		if ( ! is_array( $data ) ) {
			return array();
		}

		$newdata = array();

		foreach ( $data as $script => $type ) {
			// no such type?
			if ( ! array_key_exists( $type, $newdata ) )
				$newdata[$type] = array();

			// add script/iframe
			$newdata[$type][] = $script;
		}

		return $newdata;
	}

}

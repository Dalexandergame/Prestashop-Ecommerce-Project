<?php
/**
 * Advanced Pack 5
 *
 * @author    Presta-Module.com <support@presta-module.com> - http://www.presta-module.com
 * @copyright Presta-Module 2017 - http://www.presta-module.com
 * @license   Commercial
 *
 *           ____     __  __
 *          |  _ \   |  \/  |
 *          | |_) |  | |\/| |
 *          |  __/   | |  | |
 *          |_|      |_|  |_|
 */

if (!defined('_PS_VERSION_')) {
    exit;
}
class AdvancedPackCoreClass extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
    const DYN_CSS_FILE = 'css/dynamic-{id_shop}.css';
    public static $_module_prefix = 'AP5';
    protected $_coreClassName;
    protected $_html = '';
    protected $_base_config_url = '';
    protected $_file_to_check = array();
    protected $_support_link = false;
    protected $_getting_started = false;
    protected $_copyright_link = array(
        'link'    => '',
        'img'    => '//www.presta-module.com/img/logo-module.JPG'
    );
    public function __construct()
    {
        parent::__construct();
        $this->_coreClassName = Tools::strtolower(get_class());
        $this->defaultLanguage = (int)Configuration::get('PS_LANG_DEFAULT');
        $this->_iso_lang = Language::getIsoById($this->context->cookie->id_lang);
        $this->languages = Language::getLanguages(false);
        $forum_url_tab = array(
            'fr' => 'http://www.prestashop.com/forums/topic/372622-module-pm-advanced-pack-5/',
            'en' => 'http://www.prestashop.com/forums/topic/372623-module-pm-advanced-pack-5/'
        );
        $forum_url = $forum_url_tab['en'];
        if ($this->context->language->iso_code == 'fr') {
            $forum_url = $forum_url_tab['fr'];
        }
        $this->_support_link = array(
            array('link' => $forum_url, 'target' => '_blank', 'label' => $this->l('Forum topic', $this->_coreClassName)),
            array('link' => 'http://addons.prestashop.com/contact-community.php?id_product=1015', 'target' => '_blank', 'label' => $this->l('Support contact', $this->_coreClassName))
        );
    }
    public static function _isFilledArray($array)
    {
        return ($array && is_array($array) && count($array));
    }
    protected static function getDataSerialized($data, $type = 'base64')
    {
        if (is_array($data)) {
            return array_map($type . '_encode', array($data));
        } else {
            return current(array_map($type . '_encode', array($data)));
        }
    }
    protected static function getDataUnserialized($data, $type = 'base64')
    {
        if (is_array($data)) {
            return array_map($type . '_decode', array($data));
        } else {
            return current(array_map($type . '_decode', array($data)));
        }
    }
    public static function array_cartesian($pA)
    {
        if (count($pA) == 0) {
            return array(array());
        }
        $a = array_shift($pA);
        $c = self::array_cartesian($pA);
        $r = array();
        foreach ($a as $v) {
            foreach ($c as $p) {
                $r[] = array_merge(array($v), $p);
            }
        }
        return $r;
    }
    protected function installDatabase()
    {
        if (!Tools::file_exists_cache(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE)) {
            return false;
        } elseif (!$sqlFile = Tools::file_get_contents(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE)) {
            return false;
        }
        $sqlFile = preg_split("/;\s*[\r\n]+/", str_replace(array('PREFIX_', 'MYSQL_ENGINE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sqlFile));
        foreach ($sqlFile as $sqlQuery) {
            if (!Db::getInstance()->Execute(trim($sqlQuery))) {
                return false;
            }
        }
        return true;
    }
    public function _checkIfModuleIsUpdate($updateDb = false, $displayConfirm = true, $firstInstall = false)
    {
        if (!$updateDb && $this->version != Configuration::get('PM_' . self::$_module_prefix . '_LAST_VERSION', false)) {
            return false;
        }
        if ($firstInstall) {
        }
        if ($updateDb) {
            if (!$firstInstall) {
                try {
                    $this->installOverrides();
                } catch (Exception $e) {
                    $this->context->controller->errors[] = sprintf(Tools::displayError('Unable to install override: %s'), $e->getMessage());
                    $this->uninstallOverrides();
                }
            }
            if (method_exists($this, 'registerNewHooks')) {
                $this->registerNewHooks(Configuration::get('PM_' . self::$_module_prefix . '_LAST_VERSION', false), $this->version);
            }
            Configuration::updateValue('PM_' . self::$_module_prefix . '_LAST_VERSION', $this->version);
            if (!Configuration::getGlobalValue('PM_AP5_SECURE_KEY')) {
                Configuration::updateGlobalValue('PM_AP5_SECURE_KEY', Tools::strtoupper(Tools::passwdGen(16)));
            }
            $this->_updateDb();
            $config = $this->_getModuleConfiguration();
            foreach ($this->_defaultConfiguration as $configKey => $configValue) {
                if (!isset($config[$configKey])) {
                    $config[$configKey] = $configValue;
                }
            }
            $this->_setModuleConfiguration($config);
            AdvancedPack::clearAP5Cache();
            $this->_generateCSS();
            if ($displayConfirm) {
                $this->context->controller->confirmations[] = $this->l('Module updated successfully', $this->_coreClassName);
            }
        }
        return true;
    }
    protected function _columnExists($table, $column, $createIfNotExist = false, $type = false, $insertAfter = false)
    {
        $columnsList = Db::getInstance()->ExecuteS("SHOW COLUMNS FROM `" . _DB_PREFIX_ . $table . "`", true, false);
        foreach ($columnsList as $columnRow) {
            if ($columnRow['Field'] == $column) {
                return true;
            }
        }
        if ($createIfNotExist && Db::getInstance()->Execute('ALTER TABLE `' . _DB_PREFIX_ . $table . '` ADD `' . $column . '` ' . $type . ' ' . ($insertAfter ? ' AFTER `' . $insertAfter . '`' : '') . '')) {
            return true;
        }
        return false;
    }
    protected function _checkPermissions()
    {
        if (self::_isFilledArray($this->_file_to_check)) {
            $errors = array();
            foreach ($this->_file_to_check as $fileOrDir) {
                if (!is_writable(dirname(__FILE__) . '/' . $fileOrDir)) {
                    $errors[] = dirname(__FILE__) . '/' . $fileOrDir;
                }
            }
            if (!count($errors)) {
                return true;
            } else {
                $errorContent = '';
                $errorContent .= $this->l('Before being able to configure the module, make sure to set write permissions to files and folders listed below:', $this->_coreClassName) . '<br /><br />';
                foreach ($errors as $error) {
                    $errorContent .= '&bull; ' . str_replace(dirname(__FILE__) . '/', dirname(__FILE__) . '/<strong>', $error) . '</strong><br />';
                }
                $errorContent .= '<br /><a class="button" href="'. $this->_base_config_url .'">' . $this->l('Click here to check again', $this->_coreClassName) . '</a>';
                $this->context->controller->errors[] = $errorContent;
                return false;
            }
        }
        return true;
    }
    public function _showRating($show = false)
    {
        $dismiss = (int)(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? Configuration::getGlobalValue('PM_'.self::$_module_prefix.'_DISMISS_RATING') : Configuration::get('PM_'.self::$_module_prefix.'_DISMISS_RATING'));
        if ($show && $dismiss != 1 && self::_getNbDaysModuleUsage() >= 3) {
            $this->_html .= '
			<div id="addons-rating-container" class="ui-widget note">
				<div style="margin-top: 20px; margin-bottom: 20px; padding: 0 .7em; text-align: center;" class="ui-state-highlight ui-corner-all">
					<p class="invite">'
                        . $this->l('You are satisfied with our module and want to encourage us to add new features ?', $this->_coreClassName)
                        . '<br/>'
                        . '<a href="http://addons.prestashop.com/ratings.php" target="_blank"><strong>'
                        . $this->l('Please rate it on Prestashop Addons, and give us 5 stars !', $this->_coreClassName)
                        . '</strong></a>
					</p>
					<p class="dismiss">'
                        . '[<a href="javascript:void(0);">'
                        . $this->l('No thanks, I don\'t want to help you. Close this dialog.', $this->_coreClassName)
                        . '</a>]
					 </p>
				</div>
			</div>';
        }
    }
    
    private function _getPMdata()
    {
        $param = array();
        $param[] = 'ver-'._PS_VERSION_;
        $param[] = 'current-'.$this->name;
        
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT DISTINCT name FROM '._DB_PREFIX_.'module WHERE name LIKE "pm_%"');
        if ($result && self::_isFilledArray($result)) {
            foreach ($result as $module) {
                $instance = Module::getInstanceByName($module['name']);
                if ($instance && isset($instance->version)) {
                    $param[] = $module['name'].'-'.$instance->version;
                }
            }
        }
        return urlencode(self::getDataSerialized(implode('|', $param)));
    }
    private function getPMAddons()
    {
        $pmAddons = array();
        $result = Db::getInstance()->ExecuteS('SELECT DISTINCT name FROM '._DB_PREFIX_.'module WHERE name LIKE "pm_%"');
        if ($result && self::_isFilledArray($result)) {
            foreach ($result as $module) {
                $instance = Module::getInstanceByName($module['name']);
                if ($instance && isset($instance->version)) {
                    $pmAddons[$module['name']] = $instance->version;
                }
            }
        }
        return $pmAddons;
    }
    private function doHttpRequest($data = array(), $c = 'prestashop', $s = 'api.addons')
    {
        $data = array_merge(array(
            'version' => _PS_VERSION_,
            'iso_lang' => Tools::strtolower($this->_iso_lang),
            'iso_code' => Tools::strtolower(Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT'))),
            'module_key' => $this->module_key,
            'method' => 'contributor',
            'action' => 'all_products',
        ), $data);
        $postData = http_build_query($data);
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'content' => $postData,
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'timeout' => 15,
            )
        ));
        $response = Tools::file_get_contents('https://' . $s . '.' . $c . '.com', false, $context);
        if (empty($response)) {
            return false;
        }
        $responseToJson = Tools::jsonDecode($response);
        if (empty($responseToJson)) {
            return false;
        }
        return $responseToJson;
    }
    private function getAddonsModulesFromApi()
    {
        $modules = Configuration::get('PM_' . self::$_module_prefix . '_AM');
        $modules_date = Configuration::get('PM_' . self::$_module_prefix . '_AMD');
        if ($modules && strtotime('+2 day', $modules_date) > time()) {
            return Tools::jsonDecode($modules, true);
        }
        $jsonResponse = $this->doHttpRequest();
        if (empty($jsonResponse->products)) {
            return array();
        }
        $dataToStore = array();
        foreach ($jsonResponse->products as $addonsEntry) {
            $dataToStore[(int)$addonsEntry->id] = array(
                'name' => $addonsEntry->name,
                'displayName' => $addonsEntry->displayName,
                'url' => $addonsEntry->url,
                'compatibility' => $addonsEntry->compatibility,
                'version' => $addonsEntry->version,
                'description' => $addonsEntry->description,
            );
        }
        Configuration::updateValue('PM_' . self::$_module_prefix . '_AM', Tools::jsonEncode($dataToStore));
        Configuration::updateValue('PM_' . self::$_module_prefix . '_AMD', time());
        return Tools::jsonDecode(Configuration::get('PM_' . self::$_module_prefix . '_AM'), true);
    }
    private function getPMModulesFromApi()
    {
        $modules = Configuration::get('PM_' . self::$_module_prefix . '_PMM');
        $modules_date = Configuration::get('PM_' . self::$_module_prefix . '_PMMD');
        if ($modules && strtotime('+2 day', $modules_date) > time()) {
            return Tools::jsonDecode($modules, true);
        }
        $jsonResponse = $this->doHttpRequest(array('list' => $this->getPMAddons()), 'presta-module', 'api-addons');
        if (empty($jsonResponse)) {
            return array();
        }
        Configuration::updateValue('PM_' . self::$_module_prefix . '_PMM', Tools::jsonEncode($jsonResponse));
        Configuration::updateValue('PM_' . self::$_module_prefix . '_PMMD', time());
        return Tools::jsonDecode(Configuration::get('PM_' . self::$_module_prefix . '_PMM'), true);
    }
    public function _displaySupport()
    {
        $get_started_image_list = array();
        if (isset($this->_getting_started) && self::_isFilledArray($this->_getting_started)) {
            foreach ($this->_getting_started as $get_started_image) {
                $get_started_image_list[] = "{ 'href': '".$get_started_image['href']."', 'title': '".htmlentities($get_started_image['title'], ENT_QUOTES, 'UTF-8')."' }";
            }
        }
        $pm_addons_products = $this->getAddonsModulesFromApi();
        $pm_products = $this->getPMModulesFromApi();
        if (!is_array($pm_addons_products)) {
            $pm_addons_products = array();
        }
        if (!is_array($pm_products)) {
            $pm_products = array();
        }
        self::shuffleArray($pm_addons_products);
        if (self::_isFilledArray($pm_addons_products)) {
            if (!empty($pm_products['ignoreList']) && self::_isFilledArray($pm_products['ignoreList'])) {
                foreach ($pm_products['ignoreList'] as $ignoreId) {
                    if (isset($pm_addons_products[$ignoreId])) {
                        unset($pm_addons_products[$ignoreId]);
                    }
                }
            }
            $addonsList = $this->getPMAddons();
            if ($addonsList && self::_isFilledArray($addonsList)) {
                foreach (array_keys($addonsList) as $moduleName) {
                    foreach ($pm_addons_products as $k => $pm_addons_product) {
                        if ($pm_addons_product['name'] == $moduleName) {
                            unset($pm_addons_products[$k]);
                            break;
                        }
                    }
                }
            }
        }
        $vars = array(
            'support_links' => (self::_isFilledArray($this->_support_link) ? $this->_support_link : array()),
            'copyright_link' => (self::_isFilledArray($this->_copyright_link) ? $this->_copyright_link : false),
            'get_started_image_list' => (isset($this->_getting_started) && self::_isFilledArray($this->_getting_started) ? $this->_getting_started : array()),
            'pm_module_version' => $this->version,
            'pm_data' => $this->_getPMdata(),
            'pm_products' => $pm_products,
            'pm_addons_products' => $pm_addons_products,
            'html_at_end' => (method_exists($this, '_includeHTMLAtEnd') ? $this->_includeHTMLAtEnd() : ''),
        );
        return $this->fetchTemplate('core/support.tpl', $vars);
    }
    public function smartyNoFilterModifier($s)
    {
        return $s;
    }
    protected function registerFrontSmartyObjects()
    {
        static $registeredFO = false;
        if (!$registeredFO && !empty($this->context->smarty)) {
            $this->context->smarty->unregisterPlugin('modifier', Tools::strtolower(self::$_module_prefix) . '_nofilter');
            $this->context->smarty->registerPlugin('modifier', Tools::strtolower(self::$_module_prefix) . '_nofilter', array($this, 'smartyNoFilterModifier'));
            $registeredFO = true;
        }
    }
    protected function fetchTemplate($tpl, $customVars = array(), $configOptions = array())
    {
        $this->registerFrontSmartyObjects();
        $this->context->smarty->assign(array(
            'ps_major_version' => Tools::substr(str_replace('.', '', _PS_VERSION_), 0, 2),
            'module_name' => $this->name,
            'module_path' => $this->_path,
            'base_config_url' => $this->_base_config_url,
            'current_iso_lang' => $this->_iso_lang,
            'current_id_lang' => (int)$this->context->language->id,
            'default_language' => $this->defaultLanguage,
            'languages' => $this->languages,
            'options' => $configOptions,
            'shopFeatureActive' => Shop::isFeatureActive(),
        ));
        if (sizeof($customVars)) {
            $this->context->smarty->assign($customVars);
        }
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/admin/' . $tpl);
    }
    public static function shuffleArray(&$a)
    {
        if (is_array($a) && sizeof($a)) {
            $ks = array_keys($a);
            shuffle($ks);
            $new = array();
            foreach ($ks as $k) {
                $new[$k] = $a[$k];
            }
            $a = $new;
            return true;
        }
        return false;
    }
    private static function _getNbDaysModuleUsage()
    {
        $sql = 'SELECT DATEDIFF(NOW(),date_add)
				FROM '._DB_PREFIX_.'configuration
				WHERE name = \''.pSQL('PM_'.self::$_module_prefix.'_LAST_VERSION').'\'
				ORDER BY date_add ASC';
        return (int)Db::getInstance()->getValue($sql);
    }
    protected function _getModuleConfiguration()
    {
        $conf = Configuration::get('PM_' . self::$_module_prefix . '_CONF');
        if (!empty($conf)) {
            $config = Tools::jsonDecode($conf, true);
            foreach ($this->_defaultConfiguration as $configKey => $configValue) {
                if (!isset($config[$configKey])) {
                    $config[$configKey] = $configValue;
                }
            }
            return $config;
        } else {
            return $this->_defaultConfiguration;
        }
    }
    public static function getModuleConfigurationStatic()
    {
        $conf = Configuration::get('PM_' . self::$_module_prefix . '_CONF');
        if (!empty($conf)) {
            return Tools::jsonDecode($conf, true);
        } else {
            return array();
        }
    }
    protected function _setModuleConfiguration($newConf)
    {
        Configuration::updateValue('PM_' . self::$_module_prefix . '_CONF', Tools::jsonEncode($newConf));
    }
    protected function _setDefaultConfiguration()
    {
        if (!is_array($this->_getModuleConfiguration()) || !sizeof($this->_getModuleConfiguration())) {
            Configuration::updateValue('PM_' . self::$_module_prefix . '_CONF', Tools::jsonEncode($this->_defaultConfiguration));
        }
        return true;
    }
    public function getContent()
    {
        $this->context->controller->addJqueryUI('ui.tabs');
        $this->context->controller->addJqueryPlugin('chosen');
        $this->context->controller->addCSS($this->_path . 'css/admin-module.css', 'all');
        $this->context->controller->addCSS($this->_path . 'css/colpick.css', 'all');
        $this->context->controller->addJS($this->_path . 'js/jquery.tiptip.min.js');
        $this->context->controller->addJS($this->_path . 'js/colpick.min.js');
        $this->context->controller->addJS($this->_path . 'js/admin-module.js');
        $this->_base_config_url = $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name;
    }
    protected function _pmClear()
    {
        $this->_html .= '<div class="clear"></div>';
    }
    public static function _getCssRule($selector, $rule, $value, $is_important = false, $params = false, &$css_rules = array())
    {
        $css_rule = '';
        if ((is_array($value) && count($value)) || (Tools::strlen($value) > 0 && $value != '')) {
            switch ($rule) {
                case 'keyframes_spin':
                case 'bg_gradient':
                    if (!is_array($value)) {
                        $val = explode(self::$_gradient_separator, $value);
                    } else {
                        $val = $value;
                    }
                    if (isset($val [1]) && $val [1]) {
                        $color1 = htmlentities($val [0], ENT_COMPAT, 'UTF-8');
                        $color2 = htmlentities($val [1], ENT_COMPAT, 'UTF-8');
                    } elseif (isset($val [0]) && $val [0]) {
                        $color1 = htmlentities($val [0], ENT_COMPAT, 'UTF-8');
                    }
                    if (! isset($color1)) {
                        return '';
                    }
                    if ($rule == 'bg_gradient') {
                        $css_rule .= 'background:' . $color1 . ($is_important ? '!important' : '') . ';';
                        if (isset($color2)) {
                            $css_rule .= 'background: -webkit-gradient(linear, 0 0, 0 bottom, from(' . $color1 . '), to(' . $color2 . '))' . ($is_important ? '!important' : '') . ';';
                            $css_rule .= 'background: -webkit-linear-gradient(' . $color1 . ', ' . $color2 . ')' . ($is_important ? '!important' : '') . ';';
                            $css_rule .= 'background: -moz-linear-gradient(' . $color1 . ', ' . $color2 . ')' . ($is_important ? '!important' : '') . ';';
                            $css_rule .= 'background: -ms-linear-gradient(' . $color1 . ', ' . $color2 . ')' . ($is_important ? '!important' : '') . ';';
                            $css_rule .= 'background: -o-linear-gradient(' . $color1 . ', ' . $color2 . ')' . ($is_important ? '!important' : '') . ';';
                            $css_rule .= 'background: linear-gradient(' . $color1 . ', ' . $color2 . ')' . ($is_important ? '!important' : '') . ';';
                            $css_rule .= '-pie-background: linear-gradient(' . $color1 . ', ' . $color2 . ')' . ($is_important ? '!important' : '') . ';';
                        }
                    } elseif ($rule == 'keyframes_spin') {
                        if (!isset($color2)) {
                            $color2 = $color1;
                        }
                        $css_rule .= '@keyframes ap5loader { 0%, 80%, 100% { box-shadow: 0 2.5em 0 -1.3em '. $color2 .'; } 40% { box-shadow: 0 2.5em 0 0 '. $color1 .'; } }';
                        $css_rule .= '@-webkit-keyframes ap5loader { 0%, 80%, 100% { box-shadow: 0 2.5em 0 -1.3em '. $color2 .'; } 40% { box-shadow: 0 2.5em 0 0 '. $color1 .'; } } ';
                    }
                    break;
                case 'color':
                    $css_rule .= 'color:' . $value . ($is_important ? '!important' : '') . ';';
                    break;
                case 'border_color':
                    $css_rule .= 'border-color:' . $value . ($is_important ? '!important' : '') . ';';
                    break;
                case 'border_top_color':
                    if (is_array($value)) {
                        $value = current($value);
                    }
                    $css_rule .= 'border-top-color:' . $value . ($is_important ? '!important' : '') . ';';
                    break;
                case 'border':
                    if ($value == 'none') {
                        $css_rule .= 'border:none!important;';
                    } else {
                        if (!is_array($value)) {
                            $val = explode(self::$_border_separator, $value);
                        } else {
                            $val = $value;
                        }
                        if (isset($val [5]) && $val [5]) {
                            $top    = htmlentities(str_replace('px', '', $val [0]), ENT_COMPAT, 'UTF-8');
                            $right    = htmlentities(str_replace('px', '', $val [1]), ENT_COMPAT, 'UTF-8');
                            $bottom    = htmlentities(str_replace('px', '', $val [2]), ENT_COMPAT, 'UTF-8');
                            $left    = htmlentities(str_replace('px', '', $val [3]), ENT_COMPAT, 'UTF-8');
                            $style    = htmlentities(str_replace('px', '', $val [4]), ENT_COMPAT, 'UTF-8');
                            $color    = htmlentities(str_replace('px', '', $val [5]), ENT_COMPAT, 'UTF-8');
                        } else {
                            return '';
                        }
                        $css_rule .= 'border-top:'   . $top . ($top ? ($params && isset($params ['suffix']) ? $params ['suffix'] : 'px'):'') . ($is_important ? '!important' : '') . ';';
                        $css_rule .= 'border-right:'  . $right . ($right ? ($params && isset($params ['suffix']) ? $params ['suffix'] : 'px'):'') . ($is_important ? '!important' : '') . ';';
                        $css_rule .= 'border-bottom:' . $bottom . ($bottom ? ($params && isset($params ['suffix']) ? $params ['suffix'] : 'px'):'') . ($is_important ? '!important' : '') . ';';
                        $css_rule .= 'border-left:'  . $left .  ($left ? ($params && isset($params ['suffix']) ? $params ['suffix'] : 'px'):'') . ($is_important ? '!important' : '') . ';';
                        $css_rule .= 'border-style:' . $style . ($is_important ? '!important' : '') . ';';
                        $css_rule .= 'border-color:' . $color . ($is_important ? '!important' : '') . ';';
                    }
                    break;
                case 'box_shadow':
                    if (!is_array($value)) {
                        $val = explode(self::$_shadow_separator, $value);
                    } else {
                        $val = $value;
                    }
                    if ($value == 'none' || (is_array($val) && sizeof($val) == 6 && !$val[0] && !$val[1] && !$val[2] && !$val[3])) {
                        $css_rule .= '-webkit-box-shadow:none!important;';
                        $css_rule .= '-moz-box-shadow:none!important;';
                        $css_rule .= 'box-shadow:none!important;';
                    } else {
                        $css_rule .= '-webkit-box-shadow:' . (isset($val[4]) && $val[4] != 'outset' ? $val[4].' ' : '') . $val[0].($val[0] ? ($params && isset($params ['suffix']) ? $params ['suffix'] : 'px'):'').' '. $val[1] .($val[1] ? ($params && isset($params ['suffix']) ? $params ['suffix'] : 'px'):'').' '. $val[2] .($val[2] ? ($params && isset($params ['suffix']) ? $params ['suffix'] : 'px'):'').' '. $val[3].($val[3] ? ($params && isset($params ['suffix']) ? $params ['suffix'] : 'px'):''). (isset($val[5]) ? ' '.$val[5] : '') . ($is_important ? '!important' : '') . ';';
                        $css_rule .= '-moz-box-shadow:' . (isset($val[4]) && $val[4] != 'outset' ? $val[4].' ' : '') . $val[0].($val[0] ? ($params && isset($params ['suffix']) ? $params ['suffix'] : 'px'):'').' '. $val[1] .($val[1] ? ($params && isset($params ['suffix']) ? $params ['suffix'] : 'px'):'').' '. $val[2] .($val[2] ? ($params && isset($params ['suffix']) ? $params ['suffix'] : 'px'):'').' '. $val[3].($val[3] ? ($params && isset($params ['suffix']) ? $params ['suffix'] : 'px'):''). (isset($val[5]) ? ' '.$val[5] : '') . ($is_important ? '!important' : '') . ';';
                        $css_rule .= 'box-shadow:' . (isset($val[4]) && $val[4] != 'outset' ? $val[4].' ' : '') . $val[0] .($val[0] ? ($params && isset($params ['suffix']) ? $params ['suffix'] : 'px'):'').' '. $val[1] .($val[1] ? ($params && isset($params ['suffix']) ? $params ['suffix'] : 'px'):'').' '. $val[2] .($val[2] ? ($params && isset($params ['suffix']) ? $params ['suffix'] : 'px'):'').' '. $val[3].($val[3] ? ($params && isset($params ['suffix']) ? $params ['suffix'] : 'px'):''). (isset($val[5]) ? ' '.$val[5] : '') . ($is_important ? '!important' : '') . ';';
                    }
                    break;
                case 'text_shadow':
                    if (!is_array($value)) {
                        $val = explode(self::$_shadow_separator, $value);
                    } else {
                        $val = $value;
                    }
                    if ($value == 'none' || (is_array($val) && sizeof($val) == 4 && !$val[0] && !$val[1] && !$val[2])) {
                        $css_rule .= 'text-shadow:none!important;';
                    } else {
                        $css_rule .= 'text-shadow:' . $val[0] .($val[0] ? ($params && isset($params ['suffix']) ? $params ['suffix'] : 'px'):'').' '. $val[1] .($val[1] ? ($params && isset($params ['suffix']) ? $params ['suffix'] : 'px'):'').' '. $val[2] .($val[2] ? ($params && isset($params ['suffix']) ? $params ['suffix'] : 'px'):''). ' ' . $val[3] . ($is_important ? '!important' : '') . ';';
                        $css_rule .= 'filter: dropshadow(color='.$val[3].', offx='.$val[0].', offy='.$val[1].')' . ($is_important ? '!important' : '') . ';';
                    }
                    break;
            }
        }
        if (!isset($css_rules[$selector])) {
            $css_rules[$selector] = array();
        }
        $css_rules[$selector][] = $css_rule;
        return $css_rules;
    }
    protected function _generateCSS()
    {
        $advanced_styles = '';
        $css_rules_array = array();
        $config = $this->_getModuleConfiguration();
        foreach ($this->_cssMapTable as $var => $cssRules) {
            foreach ($cssRules as $cssRule) {
                self::_getCssRule($cssRule['selector'], $cssRule['type'], $config[$var], true, false, $css_rules_array);
            }
        }
        if (self::_isFilledArray($css_rules_array)) {
            foreach ($css_rules_array as $selector => $rules) {
                if (self::_isFilledArray($rules)) {
                    if (preg_match('/keyframes_/i', $selector)) {
                        $advanced_styles .= implode('', $rules) . "\n";
                    } else {
                        $advanced_styles .= $selector.' {'.implode('', $rules).'}'."\n";
                    }
                }
            }
        }
        $dynamic_css_file = str_replace('{id_shop}', $this->context->shop->id, self::DYN_CSS_FILE);
        $advanced_styles .= "\n" . $this->_getAdvancedStylesDb();
        if (is_writable(dirname(__FILE__) . '/css/')) {
            file_put_contents(dirname(__FILE__) . '/' . $dynamic_css_file, $advanced_styles);
        } else {
            if (!is_writable(dirname(__FILE__) . '/css/')) {
                $this->context->controller->errors[] = $this->l('Please set write permision to folder:', $this->_coreClassName). ' '.dirname(__FILE__) . '/css/';
            } elseif (!is_writable(dirname(__FILE__) . '/' . $dynamic_css_file)) {
                $this->context->controller->errors[] = $this->l('Please set write permision to file:', $this->_coreClassName). ' '.dirname(__FILE__) . '/' . $dynamic_css_file;
            }
        }
    }
    protected function _updateAdvancedStyles($css_styles)
    {
        Configuration::updateValue('PM_'.self::$_module_prefix.'_ADVANCED_STYLES', self::getDataSerialized(trim($css_styles)));
        $this->_generateCSS();
    }
    protected function _getAdvancedStylesDb()
    {
        $advanced_css_file_db = Configuration::get('PM_'.self::$_module_prefix.'_ADVANCED_STYLES');
        if ($advanced_css_file_db !== false) {
            return self::getDataUnserialized($advanced_css_file_db);
        }
        return false;
    }
    protected function _displayAdvancedStyles()
    {
        $this->context->controller->addCSS($this->_path . 'css/codemirror.css', 'all');
        $this->context->controller->addJS($this->_path . 'js/codemirror-compressed.js');
        if ($this->_getAdvancedStylesDb() == false) {
            $this->_updateAdvancedStyles('/* '. self::$_module_prefix . ' - Advanced Styles Content */' . "\n");
        }
        $this->_html .= '
		<form action="' . $this->_base_config_url . '#ap5-advanced-styles" id="formAdvancedStyles_' . $this->name . '" name="formAdvancedStyles_' . $this->name . '" method="post">
			<div class="dynamicCSSTextArea">
				<textarea name="advancedCSSStyles" id="advancedCSSStyles" cols="120" rows="30">' . $this->_getAdvancedStylesDb() . '</textarea>
			</div>';
        $this->_pmClear();
        $this->_html .= '
			<br />
			<center>
				<input type="submit" value="' . $this->l('   Save   ', $this->_coreClassName) . '" name="submitAdvancedStyles" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" />
			</center>
		</form>
		<script>var editor = CodeMirror.fromTextArea(document.getElementById("advancedCSSStyles"), {});</script>
		';
        $this->_pmClear();
    }
    public function _retrieveFormValue($type, $fieldName, $fieldDbName = false, $obj, $defaultValue = '', $compareValue = false, $key = false)
    {
        if (! $fieldDbName) {
            $fieldDbName = $fieldName;
        }
        switch ($type) {
            case 'text':
                if ($key) {
                    return htmlentities(Tools::stripslashes(Tools::getValue($fieldName, (self::_isFilledArray($obj) && isset($obj[$fieldDbName] [$key]) ? $obj[$fieldDbName] [$key] : $defaultValue))), ENT_COMPAT, 'UTF-8');
                } else {
                    return htmlentities(Tools::stripslashes(Tools::getValue($fieldName, (self::_isFilledArray($obj) && isset($obj[$fieldDbName]) ? $obj[$fieldDbName] : $defaultValue))), ENT_COMPAT, 'UTF-8');
                }
            case 'textpx':
                if ($key) {
                    return (int)preg_replace('#px#', '', Tools::getValue($fieldName, (self::_isFilledArray($obj) && isset($obj[$fieldDbName]) ? $obj[$fieldDbName] [$key] : $defaultValue)));
                } else {
                    return (int)preg_replace('#px#', '', Tools::getValue($fieldName, (self::_isFilledArray($obj) && isset($obj[$fieldDbName]) ? $obj[$fieldDbName] : $defaultValue)));
                }
            case 'select':
                return ((Tools::getValue($fieldName, (self::_isFilledArray($obj) && isset($obj[$fieldDbName]) ? $obj[$fieldDbName] : $defaultValue)) == $compareValue) ? ' selected="selected"' : '');
            case 'radio':
            case 'checkbox':
                if (isset($obj[$fieldName]) && is_array($obj[$fieldName]) && sizeof($obj[$fieldName]) && isset($obj[$fieldDbName])) {
                    return ((in_array($compareValue, $obj[$fieldName])) ? ' checked="checked"' : '');
                }
                return ((Tools::getValue($fieldName, (self::_isFilledArray($obj) && isset($obj[$fieldDbName]) ? $obj[$fieldDbName] : $defaultValue)) == $compareValue) ? ' checked="checked"' : '');
        }
    }
    private function _parseOptions($defaultOptions = array(), $options = array())
    {
        if (self::_isFilledArray($options)) {
            $options = array_change_key_case($options, CASE_LOWER);
        }
        if (isset($options['tips']) && !empty($options['tips'])) {
            $options['tips'] = htmlentities($options['tips'], ENT_QUOTES, 'UTF-8');
        }
        if (self::_isFilledArray($defaultOptions)) {
            $defaultOptions = array_change_key_case($defaultOptions, CASE_LOWER);
            foreach (array_keys($defaultOptions) as $option_name) {
                if (!isset($options[$option_name])) {
                    $options[$option_name] = $defaultOptions[$option_name];
                }
            }
        }
        return $options;
    }
    protected function _displaySubmit($value, $name)
    {
        $this->_pmClear();
        $this->_html .= '<center><input type="submit" value="' . $value . '" name="' . $name . '" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" /></center><br />';
    }
    protected function _startForm($configOptions)
    {
        $defaultOptions = array(
            'action' => false,
            'target' => 'dialogIframePostForm'
        );
        $configOptions = $this->_parseOptions($defaultOptions, $configOptions);
        $this->_html .= '<form action="' . ($configOptions['action'] ? $configOptions['action'] : $this->_base_config_url) . '" method="post" id="' . $configOptions['id'] . '" target="' . $configOptions['target'] . '">';
    }
    protected function _endForm($configOptions)
    {
        $defaultOptions = array('id' => null);
        $configOptions = $this->_parseOptions($defaultOptions, $configOptions);
        $this->_html .= '</form>';
    }
    protected function _displayInputText($configOptions)
    {
        $defaultOptions = array(
            'type' => 'text',
            'size' => '150px',
            'defaultvalue' => false,
            'min' => false,
            'max' => false,
            'maxlength' => false,
            'onkeyup' => false,
            'onchange' => false,
            'required' => false,
            'tips' => false
        );
        $configOptions = $this->_parseOptions($defaultOptions, $configOptions);
        $this->_html .= '<label>' . $configOptions['label'] . '</label>
		    <div class="margin-form">
		      <input style="width:' . $configOptions['size'] . '" type="'. $configOptions['type'] .'" name="' . $configOptions['key'] . '" id="' . $configOptions['key'] . '" value="' . $this->_retrieveFormValue('text', $configOptions['key'], false, $configOptions['obj'], $configOptions['defaultvalue'], false, false) . '" class="ui-corner-all ui-input-pm" '.(($configOptions['required'] == true) ? 'required="required" ' : '') . ($configOptions['onkeyup'] ? ' onkeyup="' . $configOptions['onkeyup'] . '"' : '') . ($configOptions['onchange'] ? ' onchange="' . $configOptions['onchange'] . '"' : '') . (($configOptions['min'] !== false) ? 'min="'.(int)$configOptions['min'].'" ' : '').(($configOptions['max'] !== false) ? 'max="'.(int)$configOptions['max'].'" ' : '').(($configOptions['maxlength'] != false) ? 'maxlength="'.(int)$configOptions['maxlength'].'" ' : '').'/>'.((isset($configOptions['suffix']) && !empty($configOptions['suffix'])) ? '<span class="input-suffix">&nbsp;&nbsp;'.$configOptions['suffix'].'</span>' : '');
        if (isset($configOptions['tips']) && $configOptions['tips']) {
            $this->_html .= '<img title="'.$configOptions['tips'].'" id="' . $configOptions['key'] . '-tips" class="pm_tips" src="' . $this->_path . 'img/question.png" width="16px" height="16px" />';
            $this->_html .= '<script type="text/javascript">initTips("#' . $configOptions['key'] . '")</script>';
        }
        $this->_pmClear();
        $this->_html .= '</div>';
    }
    protected function _displayInputActive($configOptions)
    {
        $defaultOptions = array(
            'defaultvalue' => false,
            'tips' => false,
            'onclick' => false
        );
        $configOptions = $this->_parseOptions($defaultOptions, $configOptions);
        $this->_html .= '<label>' . $configOptions['label'] . '</label>
	    <div class="margin-form"><label class="t" for="' . $configOptions['key_active'] . '_on" style="float:left;"><img src="'.$this->_path.'img/yes.png" alt="' . $this->l('Yes', $this->_coreClassName) . '" title="' . $this->l('Yes', $this->_coreClassName) . '" /></label>
	      <input type="radio" name="' . $configOptions['key_active'] . '" id="' . $configOptions['key_active'] . '_on" ' . ($configOptions['onclick'] ? 'onclick="' . $configOptions['onclick'] . '"' : '') . ' value="1" ' . $this->_retrieveFormValue('radio', $configOptions['key_active'], $configOptions['key_db'], $configOptions['obj'], $configOptions['defaultvalue'], 1, false) . '  style="float:left;margin-top:5px;" />
	      <label class="t" for="' . $configOptions['key_active'] . '_on" style="float:left; margin-top:5px;"> ' . $this->l('Yes', $this->_coreClassName) . '</label>
	      <label class="t" for="' . $configOptions['key_active'] . '_off" style="float:left;"><img src="'.$this->_path.'img/no.png" alt="' . $this->l('No', $this->_coreClassName) . '" title="' . $this->l('No', $this->_coreClassName) . '" style="margin-left: 10px;" /></label>
	      <input type="radio" name="' . $configOptions['key_active'] . '" id="' . $configOptions['key_active'] . '_off" ' . ($configOptions['onclick'] ? 'onclick="' . $configOptions['onclick'] . '"' : '') . ' value="0" ' . $this->_retrieveFormValue('radio', $configOptions['key_active'], $configOptions['key_db'], $configOptions['obj'], $configOptions['defaultvalue'], 0, false) . '  style="float:left;margin-top:5px;"/>
	      <label class="t" for="' . $configOptions['key_active'] . '_off" style="float:left;margin-top:5px;"> ' . $this->l('No', $this->_coreClassName) . '</label>';
        if (isset($configOptions['tips']) && $configOptions['tips']) {
            $this->_html .= '<img title="'.$configOptions['tips'].'" id="' . $configOptions['key_active'] . '-tips" class="pm_tips" src="' . $this->_path . 'img/question.png" width="16px" height="16px" />';
            $this->_html .= '<script>initTips("#' . $configOptions['key_active'] . '")</script>';
        }
        $this->_pmClear();
        $this->_html .= '</div>';
    }
    protected function _displayInputColor($configOptions)
    {
        $defaultOptions = array(
            'size' => '60px',
            'defaultvalue' => false,
            'tips' => false
        );
        $configOptions = $this->_parseOptions($defaultOptions, $configOptions);
        $this->_html .= '<label>' . $configOptions['label'] . '</label>
		    <div class="margin-form">
		      <input size="20" type="text" name="' . $configOptions['key'] . '" id="' . $configOptions['key'] . '" class="colorPickerInput ui-corner-all ui-input-pm" value="' . $this->_retrieveFormValue('text', $configOptions['key'], false, $configOptions['obj'], $configOptions['defaultvalue'], false, false) . '" style="width:' . $configOptions['size'] . '" />
		    ';
        if (isset($configOptions['tips']) && $configOptions['tips']) {
            $this->_html .= '<img title="'.$configOptions['tips'].'" id="' . $configOptions['key'] . '-tips" class="pm_tips" src="' . $this->_path . 'img/question.png" width="16px" height="16px" />';
            $this->_html .= '<script type="text/javascript">initTips("#' . $configOptions['key'] . '")</script>';
        }
        $this->_pmClear();
        $this->_html .= '</div>';
    }
    protected function _displayInputGradient($configOptions)
    {
        $defaultOptions = array(
            'defaultvalue' => false,
            'tips' => false
        );
        $configOptions = $this->_parseOptions($defaultOptions, $configOptions);
        $color1 = false;
        $color2 = false;
        $val = false;
        $postValue = Tools::getValue($configOptions['key']);
        if (isset($postValue[0])) {
            if (is_array($postValue)) {
                if (isset($postValue[1])) {
                    $color1 = htmlentities($postValue[0], ENT_COMPAT, 'UTF-8');
                    $color2 = htmlentities($postValue[1], ENT_COMPAT, 'UTF-8');
                } else {
                    $color1 = htmlentities($postValue[0], ENT_COMPAT, 'UTF-8');
                }
            } else {
                $val = explode(self::$_gradient_separator, $postValue);
                if (isset($val[1])) {
                    $color1 = htmlentities($val[0], ENT_COMPAT, 'UTF-8');
                    $color2 = htmlentities($val[1], ENT_COMPAT, 'UTF-8');
                } else {
                    $color1 = htmlentities($val[0], ENT_COMPAT, 'UTF-8');
                }
            }
        } elseif ($configOptions['obj']) {
            $val = $configOptions['obj'][$configOptions['key']];
            if (isset($val[1]) && $val[1] != $val[0]) {
                $color1 = htmlentities($val[0], ENT_COMPAT, 'UTF-8');
                $color2 = htmlentities($val[1], ENT_COMPAT, 'UTF-8');
            } else {
                $color1 = htmlentities($val[0], ENT_COMPAT, 'UTF-8');
            }
        } elseif (!$configOptions['obj'] && $configOptions['defaultvalue']) {
            $val = explode(self::$_gradient_separator, $configOptions['defaultvalue']);
            if (isset($val[1])) {
                $color1 = htmlentities($val[0], ENT_COMPAT, 'UTF-8');
                $color2 = htmlentities($val[1], ENT_COMPAT, 'UTF-8');
            } else {
                $color1 = htmlentities($val[0], ENT_COMPAT, 'UTF-8');
            }
        }
        $this->_html .= '<label>' . $configOptions['label'] . '</label>
    <div class="margin-form">
      <input size="20" type="text" name="' . $configOptions['key'] . '[0]" id="' . $configOptions['key'] . '_0" class="colorPickerInput ui-corner-all ui-input-pm" value="' . (! $color1 ? '' : $color1) . '" size="20" style="width:60px" />
      &nbsp; <span ' . (isset($color2) && $color2 ? '' : 'style="display:none"') . ' id="' . $configOptions['key'] . '_gradient"><input size="20" type="text" class="colorPickerInput ui-corner-all ui-input-pm" name="' . $configOptions['key'] . '[1]" id="' . $configOptions['key'] . '_1" value="' . (! isset($color2) || ! $color2 ? '' : $color2) . '" size="20" style="margin-left:10px;" /></span>
      &nbsp; <span id="' . $configOptions['key'] . '_gradient" style="float:left;margin-left:10px;">
      <input type="checkbox" name="' . $configOptions['key'] . '_gradient" value="1" ' . (isset($color2) && $color2 ? 'checked=checked' : '') . ' class="makeGradient" /> &nbsp; ' . $this->l('Make a gradient', $this->_coreClassName) . '</span>';
        if (isset($configOptions['tips']) && $configOptions['tips']) {
            $this->_html .= '<img title="'.$configOptions['tips'].'" id="' . $configOptions['key'] . '-tips" class="pm_tips" src="' . $this->_path . 'img/question.png" width="16px" height="16px" />';
            $this->_html .= '<script type="text/javascript">initTips("#' . $configOptions['key'] . '")</script>';
        }
        $this->_pmClear();
        $this->_html .= '</div>';
    }
    protected function _displaySelect($configOptions)
    {
        $defaultOptions = array(
            'size' => '280px',
            'defaultvalue' => false,
            'options' => array(),
            'onchange' => false,
            'tips' => false,
            'isarray' => false,
            'disable_search_threshold' => 3
        );
        $configOptions = $this->_parseOptions($defaultOptions, $configOptions);
        $this->_html .= '<label>' . $configOptions['label'] . '</label>
		    <div class="margin-form pm_displaySelect">
		      <select id="' . $configOptions['key'] . '" name="' . $configOptions['key'] . '" style="width:' . $configOptions['size'] . '">';
        foreach ($configOptions['options'] as $value => $text_value) {
            $this->_html .= '<option value="' . ($value) . '" ' . $this->_retrieveFormValue('select', $configOptions['key'], false, $configOptions['obj'], $configOptions['defaultvalue'], $value, false) . '>' . $text_value . '</option>';
        }
        $this->_html .= '</select>';
        $this->_html .= '<script type="text/javascript">';
        $this->_html .= '$("#' . $configOptions['key'] . '").chosen({ disable_search: true, max_selected_options: 1, inherit_select_classes: true });';
        if ($configOptions['onchange']) {
            $this->_html .= '$("#' . $configOptions['key'] . '").unbind("change").bind("change",function() { ' . $configOptions['onchange'] . ' });';
        }
        $this->_html .= '</script>';
        if (isset($configOptions['tips']) && $configOptions['tips']) {
            $this->_html .= '<img title="'.$configOptions['tips'].'" id="' . $configOptions['key'] . '-tips" class="pm_tips" src="' . $this->_path . 'img/question.png" width="16px" height="16px" />';
            $this->_html .= '<script type="text/javascript">initTips("#' . $configOptions['key'] . '")</script>';
        }
        $this->_pmClear();
        $this->_html .= '</div>';
    }
    protected static function _getProductsImagesTypes()
    {
        $a = array();
        foreach (ImageType::getImagesTypes('products') as $imageType) {
            $a[$imageType['name']] = $imageType['name'] . ' (' . $imageType['width'] .' x ' . $imageType['height'] .' pixels)';
        }
        return $a;
    }
    public static function getThumbnailImageHTML($idProduct, $idImage)
    {
        $image = new Image((int)$idImage);
        $imageType = Context::getContext()->controller->imageType;
        $imagePath = _PS_IMG_DIR_.'p/'.$image->getExistingImgPath().'.'.$imageType;
        return ImageManager::thumbnail($imagePath, 'product_mini_'.(int)$idProduct.'.'.$imageType, 45, $imageType);
    }
    protected function removeJSFromController($jsFile)
    {
        if (method_exists($this->context->controller, 'removeJS')) {
            $this->context->controller->removeJS($jsFile);
        } else {
            $jsPath = Media::getJSPath($jsFile);
            if ($jsPath && array_search($jsPath, $this->context->controller->js_files) !== false) {
                unset($this->context->controller->js_files[array_search($jsPath, $this->context->controller->js_files)]);
            }
        }
    }
    protected static $_sortArrayByKeyColumn = null;
    protected static $_sortArrayByKeyOrder = null;
    protected function sortArrayByKey($a, $b)
    {
        if ($a[self::$_sortArrayByKeyColumn] > $b[self::$_sortArrayByKeyColumn]) {
            return (self::$_sortArrayByKeyOrder == 1 ? 1 : -1);
        } elseif ($a[self::$_sortArrayByKeyColumn] < $b[self::$_sortArrayByKeyColumn]) {
            return (self::$_sortArrayByKeyOrder == 1 ? -1 : 1);
        }
        return 0;
    }
}

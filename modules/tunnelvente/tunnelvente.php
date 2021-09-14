<?php
/**
 * 2007-2015 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_'))
    exit;

require_once dirname(__FILE__) . "/controllers/front/Front.php";

//require_once dirname(__FILE__) ."/classes/Functions.php";

class TunnelVente extends Module
{
//    use Functions;

    protected $config_form = false;

    public function __construct()
    {
        $this->name          = 'tunnelvente';
        $this->tab           = 'checkout';
        $this->version       = '1.0.0';
        $this->author        = 'Pulse digital';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Tunnel de vente');
        $this->description = $this->l('Module Tunnel de vente ');
        $module            = $this;
        $controllers       = array(
            1 => 'type',
            2 => 'taille',
            3 => 'sapin',
            4 => 'retour',
            5 => 'decoration',
            6 => 'pot',
            7 => 'autres',
            8 => 'accessoire',
        );
        //etape 1
        $step1 = new Step($module->l('Configuration du sapin'));
        $step1->addStepDetail(new StepDetail($module->l('NPA'), '#'));
        $step1->addStepDetail(new StepDetail($module->l('Choisissez le type'), "{$controllers[1]}"));
        $step1->addStepDetail(new StepDetail($module->l('Petit ou grand'), "{$controllers[2]}"));
        $step1->addStepDetail(new StepDetail($module->l('Choisissez le type de recyclage'), "{$controllers[4]}"));

        //etape 2
        $step2 = new Step($module->l('Choix de la décoration'));
        $step2->addStepDetail(new StepDetail($module->l('Choisissez les boules'), "{$controllers[5]}"));
        $step2->addStepDetail(new StepDetail($module->l('Choisissez les pots'), "{$controllers[6]}"));

        //etape 3
        $step3 = new Step($module->l('Confirmation et accessoires'));
        $step3->addStepDetail(new StepDetail($module->l('Confirmation de commande'), "{$controllers[6]}"));
        $step3->addStepDetail(new StepDetail($module->l('Choisissez les accessoires'), "{$controllers[7]}"));

        $steps = Steps::getInstance();
        $steps->addStep($step1)
              ->addStep($step2)
              ->addStep($step3)
        ;

        Front::$steps = $steps;
    }


    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
//		Configuration::updateValue('TUNNELVENTE_LIVE_MODE', false);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayHome') &&
            $this->registerHook('DisplayFooterProduct') &&
            $this->registerHook('displayMyCMSPage') &&
            $this->registerHook('backOfficeHeader');
    }

    public function uninstall()
    {
//		Configuration::deleteByName('TUNNELVENTE_LIVE_MODE');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool) Tools::isSubmit('submitTunnelventeModule')) == true)
            $this->postProcess();

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return /*$output.*/ $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar             = false;
        $helper->table                    = $this->table;
        $helper->module                   = $this;
        $helper->default_form_language    = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier    = $this->identifier;
        $helper->submit_action = 'submitTunnelventeModule';
        $helper->currentIndex  = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token         = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages'    => $this->context->controller->getLanguages(),
            'id_language'  => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon'  => 'icon-cogs',
                ),
                'input'  => array(
                    array(
                        'type'    => 'switch',
                        'label'   => $this->l('Tunnel activer'),
                        'name'    => 'TUNNELVENTE_ENABLED',
                        'is_bool' => true,
                        'desc'    => $this->l('Activer le tunnel de vente'),
                        'values'  => array(
                            array(
                                'id'    => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id'    => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col'    => 3,
                        'type'   => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc'   => $this->l('Id de produit de retour Ecosapin gratuit ex: 37'),
                        'name'   => 'TUNNELVENTE_ID_PRODUCT_RECYCLAGE_ECOSAPIN_GRATUIT',
                        'label'  => $this->l('Id produit de retour Ecosapin gratuit'),
                    ),
                    array(
                        'col'    => 3,
                        'type'   => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc'   => $this->l('Id de produit de retour Sapin Suisse payant ex: 67'),
                        'name'   => 'TUNNELVENTE_ID_PRODUCT_RECYCLAGE_SAPIN_SUISSE_PAYANT',
                        'label'  => $this->l('Id produit de retour Sapin Suisse payant'),
                    ),
                    array(
                        'col'    => 3,
                        'type'   => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc'   => $this->l('Id de produit de retour Sapin Suisse gratuit ex: 68'),
                        'name'   => 'TUNNELVENTE_ID_PRODUCT_RECYCLAGE_SAPIN_SUISSE_GRATUIT',
                        'label'  => $this->l('Id produit de retour Sapin Suisse gratuit'),
                    ),
                    array(
                        'col'    => 3,
                        'type'   => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc'   => $this->l('Id de produit des boules ex: 52'),
                        'name'   => 'TUNNELVENTE_ID_PRODUCT_BOULE',
                        'label'  => $this->l('Id produit des boules'),
                    ),
                    array(
                        'col'    => 3,
                        'type'   => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc'   => $this->l('Id de produit des pots ex: 53'),
                        'name'   => 'TUNNELVENTE_ID_PRODUCT_POT',
                        'label'  => $this->l('Id produit des pots'),
                    ),
                    array(
                        'col'    => 3,
                        'type'   => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc'   => $this->l('Id de produit des pieds ex: 53'),
                        'name'   => 'TUNNELVENTE_ID_PRODUCT_PIED',
                        'label'  => $this->l('Id produit des pieds'),
                    ),
                    array(
                        'col'    => 3,
                        'type'   => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc'   => $this->l('Id de produit de MyLittel ecosapin ex: 50'),
                        'name'   => 'TUNNELVENTE_ID_PRODUCT_MYLITTELECOSAPIN',
                        'label'  => $this->l('Id produit de MyLittel ecosapin '),
                    ),
                    array(
                        'col'    => 3,
                        'type'   => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc'   => $this->l('Id d\'entropot par defaut ex: 1'),
                        'name'   => 'TUNNELVENTE_DEFAULT_ENTROPOT_STOCK_DISPO',
                        'label'  => $this->l('Entropot par defaut quand il y a pas de stock dispo pour le NPA'),
                    ),
                    array(
                        'col'    => 3,
                        'type'   => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc'   => $this->l('Id de transporteur de post: 7'),
                        'name'   => 'TUNNELVENTE_ID_CARRIER_POST',
                        'label'  => $this->l('Id de transporteur de post'),
                    ),
                    array(
                        'col'    => 3,
                        'type'   => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc'   => $this->l('Id de catégorie accessoire ex: 12'),
                        'name'   => 'TUNNELVENTE_ID_CATEGORIE_ACCESSOIRE',
                        'label'  => $this->l('Id de catégorie accessoire'),
                    ),
                    array(
                        'col'    => 3,
                        'type'   => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc'   => $this->l('Id de Frais de virement ex: 51'),
                        'name'   => 'TUNNELVENTE_ID_FRAI_VIREMENT',
                        'label'  => $this->l('Id de Frais de virement'),
                    ),
                    array(
                        'col'    => 3,
                        'type'   => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc'   => $this->l('Id catégorie Little Ecosapin ex: 9'),
                        'name'   => 'TUNNELVENTE_ID_LITTLE_ECOSAPIN',
                        'label'  => $this->l('Id catégorie Little Ecosapin'),
                    ),
                    array(
                        'col'    => 3,
                        'type'   => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc'   => $this->l('Id catégorie Ecosapin ex: 2'),
                        'name'   => 'TUNNELVENTE_ID_ECOSAPIN',
                        'label'  => $this->l('Id catégorie Ecosapin'),
                    ),
                    array(
                        'col'    => 3,
                        'type'   => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc'   => $this->l('Id catégorie Sapin Suisse ex: 13'),
                        'name'   => 'TUNNELVENTE_ID_SAPIN_SUISSE',
                        'label'  => $this->l('Id catégorie Sapin Suisse'),
                    ),
                    array(
                        'col'    => 3,
                        'type'   => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc'   => $this->l('Id attribut Petit Sapin Suisse 90/110 cm coupé avec pied ex: 21'),
                        'name'   => 'TUNNELVENTE_ID_ATTRIBUTE_PETIT_SAPIN_SUISSE',
                        'label'  => $this->l('Id attribut Petit Sapin Suisse 90/110 cm coupé avec pied'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'TUNNELVENTE_ENABLED'                                   => Configuration::get('TUNNELVENTE_ENABLED'),
            'TUNNELVENTE_ID_PRODUCT_RECYCLAGE_ECOSAPIN_GRATUIT'     => Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_ECOSAPIN_GRATUIT'),
            'TUNNELVENTE_ID_PRODUCT_RECYCLAGE_SAPIN_SUISSE_PAYANT'  => Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_SAPIN_SUISSE_PAYANT'),
            'TUNNELVENTE_ID_PRODUCT_RECYCLAGE_SAPIN_SUISSE_GRATUIT' => Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_SAPIN_SUISSE_GRATUIT'),
            'TUNNELVENTE_ID_PRODUCT_BOULE'                          => Configuration::get('TUNNELVENTE_ID_PRODUCT_BOULE'),
            'TUNNELVENTE_ID_PRODUCT_POT'                            => Configuration::get('TUNNELVENTE_ID_PRODUCT_POT'),
            'TUNNELVENTE_ID_PRODUCT_PIED'                           => Configuration::get('TUNNELVENTE_ID_PRODUCT_PIED'),
            'TUNNELVENTE_ID_PRODUCT_MYLITTELECOSAPIN'               => Configuration::get('TUNNELVENTE_ID_PRODUCT_MYLITTELECOSAPIN'),
            'TUNNELVENTE_DEFAULT_ENTROPOT_STOCK_DISPO'              => Configuration::get('TUNNELVENTE_DEFAULT_ENTROPOT_STOCK_DISPO'),
            'TUNNELVENTE_ID_CARRIER_POST'                           => Configuration::get('TUNNELVENTE_ID_CARRIER_POST'),
            'TUNNELVENTE_ID_CATEGORIE_ACCESSOIRE'                   => Configuration::get('TUNNELVENTE_ID_CATEGORIE_ACCESSOIRE'),
            'TUNNELVENTE_ID_FRAI_VIREMENT'                          => Configuration::get('TUNNELVENTE_ID_FRAI_VIREMENT'),
            'TUNNELVENTE_ID_LITTLE_ECOSAPIN'                        => Configuration::get('TUNNELVENTE_ID_LITTLE_ECOSAPIN'),
            'TUNNELVENTE_ID_ECOSAPIN'                               => Configuration::get('TUNNELVENTE_ID_ECOSAPIN'),
            'TUNNELVENTE_ID_SAPIN_SUISSE'                           => Configuration::get('TUNNELVENTE_ID_SAPIN_SUISSE'),
            'TUNNELVENTE_ID_ATTRIBUTE_PETIT_SAPIN_SUISSE'           => Configuration::get('TUNNELVENTE_ID_ATTRIBUTE_PETIT_SAPIN_SUISSE')
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key)
            Configuration::updateValue($key, Tools::getValue($key));
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
     //   $this->context->controller->addJS(_THEME_JS_DIR_ . 'jquery.jqtransform.js');
        $this->context->controller->registerJavascript('magnific-js',_THEME_JS_DIR_ . 'dist/jquery.magnific-popup.js',['position' =>  'bottom', 'priority' => 100]);
        $this->context->controller->registerJavascript('tunnelvente-js',_PS_JS_DIR_ . 'jquery/jquery-1.11.0.min.js',['position' =>  'head', 'priority' => 50]);
        $this->context->controller->registerJavascript('tunnelvente-jqJs',_PS_JS_DIR_ . 'jquery/jquery-migrate-1.2.1.min.js',['position' =>  'head', 'priority' => 60]);
        $this->context->controller->registerJavascript('product-js',_THEME_JS_DIR_ . 'product.js',['position' =>  'bottom', 'priority' => 200]);
        $this->context->controller->registerJavascript('global-js',_THEME_JS_DIR_ . 'global.js',['position' =>  'bottom', 'priority' => 110]);


        $this->context->controller->registerJavascript("tunnelvente-jq",_PS_JS_DIR_ .'jquery/jqtransformplugin/jquery.jqtransform.js',['position' =>  'bottom', 'priority' => 150]);
        $this->context->controller->registerJavascript('front-js',$this->_path . 'views/js/front.js',['position' =>  'bottom', 'priority' => 100]);

        //$this->context->controller->addCSS(_THEME_CSS_DIR_ . 'jqtransformplugin/jqtransform.css');
        $this->context->controller->registerStylesheet('tunnelvente-global-style',$this->_path . 'views/css/global.css',['media' => 'all', 'priority' => 20]);
        $this->context->controller->registerStylesheet('tunnelvente-front-style',$this->_path . 'views/css/front.css',['media'=>'all', 'priority' => 50]);
        $this->context->controller->registerStylesheet("tunnelvente-jqCss",_PS_JS_DIR_ .'jquery/jqtransformplugin/jqtransform.css',['media'=>'all', 'priority' => 100]);
    }

    function getIdProductSapins($cat)
    {

        $sql    = "SELECT id_product FROM " . _DB_PREFIX_ . "product
                       WHERE id_category_default IN (" . implode(",", $cat) . ")";
        $result = Db::getInstance()->executeS($sql);

        $idP = array();
        foreach ($result as $res) {
            $idP[] = $res["id_product"];
        }
        return $idP;
    }

    public function hookDisplayHome($params)
    {
        $steps = Front::getSteps();

        //activer NPA
        $steps->getStepByPosition(1)->setActive(true)
              ->getStepDetailByPosition(1)->setActive(true)
        ;

        $productMyLitte    = new Product((int) Configuration::get('TUNNELVENTE_ID_PRODUCT_MYLITTELECOSAPIN'), false, $this->context->language->id);
        $cart              = $this->context->cart;
        $hasSapin          = false;
        $id_product_sapins = $this->getIdProductSapins(array(Configuration::get('TUNNELVENTE_ID_ECOSAPIN'), Configuration::get('TUNNELVENTE_ID_SAPIN_SUISSE')));
        $npa               = '';
        if ($cart && $products = $cart->getProducts()) {
            $npa = $cart->npa;
            foreach ($products as $product) {
                if (in_array($product['id_product'], $id_product_sapins)) {
                    $hasSapin = true;
                    break;
                }
            }
        }
        $this->context->smarty->assign(array(
                                           'steps'                 => $steps,
                                           'prod_mylittelecosapin' => $productMyLitte,
                                           "npa"                   => $npa,
                                           "hasSapin"              => $hasSapin,
                                       )
        );

        return $this->display(_THEME_DIR_, 'template/index.tpl');
    }

    public function hookDisplayFooterProduct($params)
    {
        return $this->hookDisplayHome($params);
    }
}

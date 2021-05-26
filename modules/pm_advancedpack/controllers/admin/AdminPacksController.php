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
class AdminPacksController extends AdminController
{
    protected $moduleInstance;
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'product';
        $this->className = 'Product';
        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->context = Context::getContext();
        $this->moduleInstance = (class_exists('AdvancedPack') ? AdvancedPack::getModuleInstance() : Module::getInstanceByName('pm_advancedpack'));
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );
        $this->fields_list = array(
            'id_product' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'type' => 'int'
            ),
            'image' => array(
                'title' => $this->l('Image'),
                'align' => 'center',
                'image' => 'p',
                'orderby' => false,
                'filter' => false,
                'search' => false,
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Pack name'),
                'filter_key' => 'b!name',
            ),
            'reference' => array(
                'title' => $this->l('Reference'),
                'align' => 'left',
            ),
            'name_category' => array(
                'title' => $this->l('Category'),
                'filter_key' => 'cl!name',
            ),
            'nb_products' => array(
                'title' => $this->l('Nb. products'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'type' => 'int'
            ),
            'classic_price' => array(
                'title' => $this->l('Classic price*'),
                'type' => 'price',
                'align' => 'text-right',
                'color' => 'red',
                'havingFilter' => false,
                'orderby' => false,
                'search' => false
            ),
            'price_final' => array(
                'title' => $this->l('Pack price*'),
                'type' => 'price',
                'align' => 'text-right',
                'havingFilter' => false,
                'orderby' => false,
                'search' => false
            ),
            'pack_quantity' => array(
                'title' => $this->l('Available Qty*'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'type' => 'int',
                'havingFilter' => false,
                'orderby' => false,
                'search' => false
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'active' => 'status',
                'filter_key' => 'sa!active',
                'align' => 'text-center',
                'type' => 'bool',
                'class' => 'fixed-width-sm',
                'orderby' => false
            )
        );
        if (!class_exists('AdvancedPack')) {
            include_once _PS_ROOT_DIR_ . '/modules/pm_advancedpack/AdvancedPack.php';
            include_once _PS_ROOT_DIR_ . '/modules/pm_advancedpack/AdvancedPackCoreClass.php';
        }
        parent::__construct();
        $id_shop = (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP ? (int)$this->context->shop->id : 'a.id_shop_default');
        $this->_select .= ' app.`nb_products`, MAX(image_shop.`id_image`) AS id_image, cl.name AS `name_category`, 0 AS pack_quantity, 0 AS classic_price, 0 AS price_final ';
        $this->_join .= ' JOIN (SELECT app.id_pack, COUNT(app.id_pack) as nb_products FROM `'._DB_PREFIX_.'pm_advancedpack_products` app GROUP BY app.id_pack) app ON app.`id_pack`=a.`id_product` ';
        $this->_join .= ' JOIN `'._DB_PREFIX_.'pm_advancedpack` ap_shop ON (a.`id_product` = ap_shop.`id_pack` AND ap_shop.`id_shop` = '.$id_shop.') ';
        $this->_join .= ' JOIN `'._DB_PREFIX_.'product_shop` sa ON (a.`id_product` = sa.`id_product` AND sa.id_shop = '.$id_shop.') ';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (sa.`id_category_default` = cl.`id_category` AND b.`id_lang` = cl.`id_lang` AND cl.id_shop = '.$id_shop.') ';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = a.`id_product`) ';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop ON (image_shop.`id_image` = i.`id_image` AND image_shop.`cover` = 1 AND image_shop.`id_shop` = '.$id_shop.') ';
        $this->_group = ' GROUP BY a.`id_product` ';
    }
    public function l($string, $class = 'AdminPacksController', $addslashes = false, $htmlentities = true)
    {
        return $this->moduleInstance->l($string, $class, $addslashes, $htmlentities);
    }
    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_pack'] = array(
                'href' => $this->context->link->getAdminLink('AdminProducts') . '&addproduct&newpack',
                'desc' => $this->l('Add a new pack', 'AdminPacksController', null, false),
                'icon' => 'process-icon-new'
            );
            $this->page_header_toolbar_btn['configure_module'] = array(
                'href' => $this->context->link->getAdminLink('AdminModules') . '&configure=pm_advancedpack',
                'desc' => $this->l('Configure module', 'AdminPacksController', null, false),
                'icon' => 'process-icon-modules-list'
            );
        }
        parent::initPageHeaderToolbar();
    }
    public function initToolbar()
    {
        parent::initToolbar();
        if (empty($this->display)) {
            $this->toolbar_btn['new'] = array(
                'href' => $this->context->link->getAdminLink('AdminProducts') . '&addproduct&newpack',
                'desc' => $this->l('Add a new pack', 'AdminPacksController', null, false),
                'icon' => 'process-icon-new'
            );
            $this->toolbar_btn['modules-list'] = array(
                'href' => $this->context->link->getAdminLink('AdminModules') . '&configure=pm_advancedpack',
                'desc' => $this->l('Configure module', 'AdminPacksController', null, false),
                'icon' => 'process-icon-modules-list'
            );
        }
    }
    public function initContent()
    {
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->errors[] = $this->l('You must select a specific shop in order to continue.');
        }
        parent::initContent();
    }
    public function initProcess()
    {
        if (Tools::getIsset('updateproduct') && Tools::getIsset('id_product') && (int)Tools::getValue('id_product')) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminProducts') . '&key_tab=ModulePm_advancedpack&updateproduct&id_product=' . (int)Tools::getValue('id_product'));
        }
        parent::initProcess();
    }
    public function getList($id_lang, $orderBy = null, $orderWay = null, $start = 0, $limit = null, $id_lang_shop = null)
    {
        parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, ($id_lang_shop == null ? $this->context->shop->id : $id_lang_shop));
        if (AdvancedPackCoreClass::_isFilledArray($this->_list)) {
            for ($i = 0; $i < count($this->_list); $i++) {
                $this->_list[$i]['classic_price'] = Tools::convertPrice(AdvancedPack::getPackPrice($this->_list[$i]['id_product'], true, false));
                $this->_list[$i]['price_final'] = Tools::convertPrice(AdvancedPack::getPackPrice($this->_list[$i]['id_product']));
                $this->_list[$i]['pack_quantity'] = AdvancedPack::getPackAvailableQuantity($this->_list[$i]['id_product']);
            }
        }
    }
    public function renderList()
    {
        $r = '';
        if (!count($this->errors)) {
            $r .= parent::renderList();
            $r .= '<p><i>*'.$this->l('Columns with wildcards only reflect data for default combinations').'</i></p>';
        }
        return $r;
    }
}

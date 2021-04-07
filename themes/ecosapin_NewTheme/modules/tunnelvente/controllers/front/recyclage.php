<?php

if (!defined('_PS_VERSION_'))
    exit;

require_once dirname(__FILE__) . "/Front.php";
require_once dirname(__FILE__) . "/boule.php";

class tunnelventerecyclageModuleFrontController extends tunnelventebouleModuleFrontController {

    protected static $TEMPLATE = "recyclage.tpl";
    protected $id_product_recyclage;

    function setId_product_recyclage() {
        
        // le retour est payant pour Sapin Suisse
        // Si une personne a pris un ecosapin + un sapin suisse (le retour ecosapin étant gratuit) le retour pour les deux sapin est gratuit.
	// Si il prend 2 sapin suisse, ou plus, il ne paie qu’une seul fois 
        
        $type = $this->getValueTunnelVent("type");
        
        $cart = $this->context->cart;
        
        $this->id_product_recyclage = Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_ECOSAPIN_GRATUIT');
        
        $sapin_suisse=0;
        if($cart && $products = $cart->getProducts()){
            foreach ($products as $product) {
                 if($product['id_category_default'] == Configuration::get('TUNNELVENTE_ID_SAPIN_SUISSE')){
                    $sapin_suisse++;
                }
            }
        }
        
        if( !$cart->getProducts() && $type == Configuration::get('TUNNELVENTE_ID_SAPIN_SUISSE')){ // un seul sapin suisse => retour payant
            $this->id_product_recyclage = Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_SAPIN_SUISSE_PAYANT');
        }
        else if( $cart->getProducts() && $type == Configuration::get('TUNNELVENTE_ID_SAPIN_SUISSE') && $sapin_suisse>0){
            $this->id_product_recyclage = Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_SAPIN_SUISSE_GRATUIT');
        }
        
    }
    
    public function __construct() {
        parent::__construct();
    }

    public function init() {
        $this->page_name = 'taillespain';
        front::init();
        $this->display_column_left = false;
        $this->display_column_right = false;
        
        $this->setId_product_recyclage();
        
        if ($this->ajax && $this->isXmlHttpRequest() /* && Tools::isSubmit('type')/* */) {
              $last_id_recyclage_checked = ($this->getValueTunnelVent("id_product_recyclage") === false )? $this->id_product_recyclage:$this->getValueTunnelVent("id_product_recyclage");//null;
            if (Tools::isSubmit("sapin")) {
                $sapin = (int) Tools::getValue("sapin", 0);
                
                if (!is_numeric($sapin) || $sapin <= 0) {
                    $this->errors[] = Tools::displayError("erreur : Choisissez l'essence de votre sapin !");
                }  else {
                    $this->addValueTunnelVent('id_product_sapin', $sapin);//$this->context->cookie->__set('id_product_sapin', $sapin);
                }
            }else if (Tools::isSubmit("back")) {
                  $last_id_recyclage_checked = $this->getValueTunnelVent("id_product_recyclage");//$this->context->cookie->id_product_recyclage;
            }else{
                 $this->errors[] = Tools::displayError("erreur : Choisissez l'essence de votre sapin !");
            }
            
            if($this->getValueTunnelVent("id_attribute_taille") == Configuration::get('TUNNELVENTE_ID_ATTRIBUTE_PETIT_SAPIN_SUISSE')){
                parent::init();
            }else{
            $return = array(
                'html' => $this->getHtml($last_id_recyclage_checked),
                'hasError' => !empty($this->errors),
                'errors' => $this->errors,
                'numStep' => 5,
                'supp' => $this->getValuesTunnelVent(),
            );
            die(Tools::jsonEncode($return));
            }
        }
    }

    public function initContent() {
        parent::initContent();
        $steps = $this->getSteps();
        $sapin = null;
        $last_id_recyclage_checked = $this->id_product_recyclage;
        if (Tools::isSubmit("taille")) {
            $sapin = (int) Tools::getValue("sapin", 0);
        }else{
            $sapin = $this->getValueTunnelVent("id_product_sapin");//$this->context->cookie->id_product_sapin;
            $last_id_recyclage_checked =  $this->getValueTunnelVent("id_product_recyclage");//$this->context->cookie->id_product_recyclage;
        }
        
        if (!is_numeric($sapin) || $sapin <= 0) {
            $this->errors[] = Tools::displayError("erreur : Choisissez l'essence de votre sapin !");
        } else {
            $this->addValueTunnelVent('id_product_sapin', $sapin);//$this->context->cookie->__set('id_product_sapin', $sapin);
            //activer recyclage
            $steps->getStepByPosition(1)->setActive(true)
                    ->getStepDetailByPosition(5)->setActive(true);
        }
        
        $this->context->smarty->assign(array(
            'steps' => $steps,
            'errors' => $this->errors,
            "result" =>  $this->getProductRecyclage(),
            'last_id_recyclage_checked' => $last_id_recyclage_checked,
        ));

        $this->setTemplate('index.tpl');
    }

    private function getHtml($last_id_recyclage_checked) {
        $smarty = $this->context->smarty;
        $sql = 'SELECT id_product FROM ' . _DB_PREFIX_ . 'product_attribute WHERE id_product_attribute = ' . $this->getValueTunnelVent('id_product_sapin');
        $id_product_spain = (int) Db::getInstance()->getValue($sql);
        $image = "";
        if($id_product_spain >0){
            $image = "{$id_product_spain}.png";
        }
        $smarty->assign(array(
            "product" => $this->getProductRecyclage(),
            'last_id_recyclage_checked' => $last_id_recyclage_checked,
            'image_recyclage' => $image,
        ));
        $html = stripslashes($smarty->fetch(dirname(__FILE__) . "/../../views/templates/front/" . self::$TEMPLATE))
                
        ;
        return $html;
    }

    
    private function getProductRecyclage() {
        $product = new Product($this->id_product_recyclage,false,$this->context->language->id);

        
        return array(
            "id" => $product->id,
            "description_short" => $product->description_short,
            "description" => $product->description,
            "price" => $product->getPrice(),
        );
    }

}

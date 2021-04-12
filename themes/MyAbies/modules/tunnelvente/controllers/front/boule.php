<?php

if (!defined('_PS_VERSION_'))
    exit;

require_once dirname(__FILE__) . "/Front.php";

class tunnelventebouleModuleFrontController extends Front {

    protected static $TEMPLATE = "boule.tpl";
    protected $id_product_boule;
    
    public function __construct() {
        parent::__construct();
        $this->id_product_boule =  Configuration::get('TUNNELVENTE_ID_PRODUCT_BOULE');//52
    }
    
    public function init() {
        $this->page_name = 'taillespain';
        parent::init();
        $this->display_column_left = false;
        $this->display_column_right = false;

        if ($this->ajax && $this->isXmlHttpRequest()) {

            if (Tools::isSubmit('recyclage')) {
                $recyclage = Tools::getValue("recyclage");
                if(!is_numeric($recyclage) && $recyclage < 0){
                    $this->errors[] = Tools::displayError("erreur : Choisissez le type de recyclage !");
                }  else {
                    $this->addValueTunnelVent('id_product_recyclage', $recyclage);// $this->context->cookie->__set('id_product_recyclage', $recyclage); 
                }               
            }else if (Tools::isSubmit("back")) {
                
            }else if($this->getValueTunnelVent("id_attribute_taille") != Configuration::get('TUNNELVENTE_ID_ATTRIBUTE_PETIT_SAPIN_SUISSE')){
                $this->errors[] = Tools::displayError("erreur : Choisissez le type de recyclage !");
            }
            
            $return = array(
                'hasError' => !empty($this->errors),
                'errors' => $this->errors,
                'html' => $this->getHtmlBoules(),
                'numStep' => 6,
            );
            die(Tools::jsonEncode($return));
        }
    }

    private function getHtmlBoules() {

        $product = new Product($this->id_product_boule,null,$this->context->language->id);        
        $smarty = $this->context->smarty;
        $smarty->assign(array(
            "petit_sapin_suisse" => ($this->getValueTunnelVent("id_attribute_taille") == Configuration::get('TUNNELVENTE_ID_ATTRIBUTE_PETIT_SAPIN_SUISSE')),
            "result" => $this->getListAttributeProductBoule(),
            "product" => $product,
            "id_product_boule" => ($this->getValueTunnelVent('id_product_boule')/*$this->context->cookie->id_product_boule*/)?$this->getValueTunnelVent('id_product_boule')/*$this->context->cookie->id_product_boule*/:0,
        ));
        $html = stripslashes($smarty->fetch(dirname(__FILE__) . "/../../views/templates/front/" . self::$TEMPLATE))

        ;
        return $html;        
    }

    private function getListAttributeProductBoule() {
        
        $id_lang = $this->context->language->id;
        
        $sql = SqlRequete::getSqlProductAttributBoule($this->id_product_boule,$this->getValueTunnelVent('npa') ,$id_lang);
        $result = Db::getInstance()->executeS($sql);        
        $products = array();
        foreach ($result as $row) {            
            $row['price_ttc'] = number_format(Product::getPriceStatic($row["id_product"],true,$row['id_product_attribute']),2);
            $products[] = $row;
        }
        return $products ;        
    }

}

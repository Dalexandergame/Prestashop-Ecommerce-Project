<?php

if (!defined('_PS_VERSION_'))
    exit;

require_once dirname(__FILE__) . "/Front.php";

class tunnelventetypeModuleFrontController extends front {

    protected static $TEMPLATE = "type.tpl";

    public function init() {
        $this->page_name = 'typesapain';
        parent::init();
        $this->display_column_left = false;
        $this->display_column_right = false;

        if ($this->ajax && $this->isXmlHttpRequest()  ) {
            if(Tools::isSubmit('npa')){
                $npa =  Tools::getValue("npa");
                $this->context->cookie->__set('npa', $npa);
                $this->addValueTunnelVent('npa', $npa);
                $return = array(
                    'hasError' => !empty($this->errors),
                    'errors' => $this->errors,
                    'html' => $this->getHtmlType($npa),
                    'numStep' => 2,
                );
                die(Tools::jsonEncode($return));
            }else{
                $npa = $this->getValueTunnelVent("npa"); //$this->context->cookie->npa;
                $type = $this->getValueTunnelVent("type");
                if(Tools::isSubmit('back')){
                    $back = (int) Tools::getValue('back');
                    if($back == 2){ // 2 type
                        $return = array(
                            'hasError' => !empty($this->errors),
                            'errors' => $this->errors,
                            'html' => $this->getHtmlType($npa),
                            'numStep' => 2,
                        );
                    }  else { // 1 npa
                        $return = array(
                            'hasError' => !empty($this->errors),
                            'errors' => $this->errors,
                            'html' => $this->getHtml($npa),
                            'numStep' => 1,
                        );
                    }
                }
                
                die(Tools::jsonEncode($return));
            }
           
        }
        
    }
    

    public function initContent() {
        parent::initContent();

        $steps = $this->getSteps();
        $typeSapin = array();
        $taille = ''; 
        if(Tools::isSubmit("npa")){
            $npa = Tools::getValue("npa");
            
            if (!is_numeric($npa) || strlen($npa) != 4 || $npa < 1000 || $npa > 9999 ) {
                $this->errors[] = Tools::displayError('erreur de saisie de NPA !');
                //activer npa
                $steps->getStepByPosition(1)->setActive(true)
                        ->getStepDetailByPosition(1)->setActive(true);
            }else {
                
                $npa = Tools::getValue("npa");
                $this->addValueTunnelVent('npa', $npa);
                $dateDispo = PlanningDeliveryByCarrierExceptionOver::getDateDisponibleByNPA();

                if(!count($dateDispo)){
                    $this->errors[] = Tools::displayError('Tous nos jours de livraison de ce district sont complets pour cette année. Rendez-vous en 2016!');
                     //activer npa
                    $steps->getStepByPosition(1)->setActive(true)
                        ->getStepDetailByPosition(1)->setActive(true);
                }else{
                //activer taille
                $steps->getStepByPosition(1)->setActive(true)
                        ->getStepDetailByPosition(2)->setActive(true);
                $typeSapin = $this->getTypeDisponible($npa);
                }
            }
            
        }else{
            $npa =  $this->getValueTunnelVent("npa");// $this->context->cookie->npa;
            if(!Tools::isSubmit("back")){                
                //activer npa
                $steps->getStepByPosition(1)->setActive(true)
                    ->getStepDetailByPosition(1)->setActive(true);
            }else{
                $taille = $this->getValueTunnelVent("id_attribute_taille");//$this->context->cookie->id_attribute_taille;
                $steps->getStepByPosition(1)->setActive(true)
                        ->getStepDetailByPosition(2)->setActive(true);
                $typeSapin = $this->getTypeDisponible($npa); 
            }
            
        }
        
        $cart = $this->context->cart ;
        $hasSapin = false;
        if($cart && $products = $cart->getProducts()){
            $npa = $cart->npa;
            foreach ($products as $product) {
                if(in_array($product['id_product'], $this->id_product_sapins)){
                    $hasSapin = true;
                    break;
                }
            }
        }

        $this->context->smarty->assign(array(
            'steps' => $steps,
            'errors' => $this->errors,
            "result" => $typeSapin,
            "npa" => $npa,
            "hasSapin" => $hasSapin,
        ));

        $this->setTemplate('index.tpl');
    }

    private function getHtml($npa) {
        $smarty = $this->context->smarty;
        $cart = $this->context->cart ;
        $hasSapin = false;
        if($cart && $products = $cart->getProducts()){
            $npa = $cart->npa;
            foreach ($products as $product) {
                if(in_array($product['id_product'], $this->id_product_sapins)){
                    $hasSapin = true;
                    break;
                }
            }
        }
        $smarty->assign(array(
            "npa" => ($npa) ? $npa : '',
            "hasSapin" => $hasSapin,
        ));
        $html = $smarty->fetch(dirname(__FILE__) . "/../../views/templates/front/npa.tpl")
        //."<br>$sql<br>"
        ;
        return $html;
    }
    private function getHtmlType($npa) {
        $smarty = $this->context->smarty;
        $cart = $this->context->cart;
        $hasSapin = false;
        if($cart && $products = $cart->getProducts()){
            $npa = $cart->npa;
            foreach ($products as $product) {
                if(in_array($product['id_product'], $this->id_product_sapins)){
                    $hasSapin = true;
                    break;
                }
            }
        }
        
        $smarty->assign(array(
            "types" => $this->getTypeDisponible($npa),
            "npa" => $npa,
            "hasSapin" => $hasSapin,
            "id_type" => $this->getValueTunnelVent('type')
        ));
        $html = $smarty->fetch(dirname(__FILE__) . "/../../views/templates/front/".self::$TEMPLATE);
        return $html;
    }



}

<?php

if (!defined('_PS_VERSION_'))
    exit;

require_once dirname(__FILE__).'/FrontAbies.php';
require_once dirname(__FILE__).'/../../../planningdeliverybycarrier/classes/PlanningDeliveryByCarrierException.php';
require_once dirname(__FILE__).'/../../../planningdeliverybycarrier/classes/PlanningRetourByCarrierException.php';

class TunnelVenteAbiesTypeModuleFrontController extends FrontAbies {

    protected static $TEMPLATE = "typeEdited.tpl";

    public function init() {
        $this->page_name = 'typesapain';
        parent::init();
        $this->display_column_left = false;
        $this->display_column_right = false;

        if ($this->ajax && $this->isXmlHttpRequest()  ) {

            if(Tools::isSubmit('npa')) {
                $type = 13;
                $npa =  Tools::getValue("npa");
                $this->context->cookie->__set('npa', $npa);
                $this->addValueTunnelVent('npa', $npa);
                $this->context->cookie->__set('type', $type);
                $this->addValueTunnelVent('type', $type);

                $dateDispo = PlanningDeliveryByCarrierException::getDateDisponibleByNPA();
                $dateDispoR = PlanningRetourByCarrierException::getDateDisponibleByNPA();

                if(!count($dateDispo) || !count($dateDispoR)){
                    $this->errors[] = Tools::displayError($this->module->l('Malheureusement notre service de livraison n\'est pas disponible dans votre région. Nous espérons pouvoir trouver un partenaire dans votre région pour les prochaines années'));
                }

                $return = array(
                    'hasError' => !empty($this->errors),
                    'errors' => $this->errors,
                    'html' => $this->getHtmlType($npa, $type),
                    'numStep' => 2,
                );
                die(Tools::jsonEncode($return));
            }else{
                $npa = $this->getValueTunnelVent("npa"); //$this->context->cookie->npa;
                $type = 12;
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
            }

            die(Tools::jsonEncode($return));
        } else {
            $products = $this->context->cart->getProducts();
            foreach ($products as $product) {
                $this->context->cart->deleteProduct($product["id_product"]);
            }
            
            $this->removeValueTunnelVent('id_product_sapin');
            $this->removeValueTunnelVent('id_product_recyclage');
            $this->removeValueTunnelVent('id_product_pied');
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
                $dateDispo = PlanningDeliveryByCarrierException::getDateDisponibleByNPA();

                if(!count($dateDispo)){
                    $this->errors[] = Tools::displayError('Tous nos jours de livraison de ce district sont complets pour cette année. Rendez-vous en 2018!');
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

        $cart = $this->context->cart;
        $cookie = $this->context->cookie;
        $hasSapin = false;
        if(isset($cart) && $products = $cart->getProducts()){
            $npa = isset($cookie->npa) ? $cookie->npa : '';
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
            "isTunnelEnabled" => Configuration::get('TUNNELVENTE_ENABLED')
        ));

        $this->setTemplate('module:tunnelventeabies/views/templates/front/index.tpl');
    }

    private function getHtml($npa) {
        $smarty = $this->context->smarty;

        $cart = $this->context->cart;
        $cookie = $this->context->cookie;
        $hasSapin = false;
        if(isset($cart) && $products = $cart->getProducts()){
            $npa = isset($cookie->npa) ? $cookie->npa : '';
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
            "isTunnelEnabled"   =>  true
        ));
        $html = $smarty->fetch(dirname(__FILE__) . "/../../views/templates/front/npa.tpl");
        return $html;
    }
    private function getHtmlType($npa, $type) {
        $smarty = $this->context->smarty;

        $cart = $this->context->cart;
        $cookie = $this->context->cookie;
        $hasSapin = false;
        if(isset($cart) && $products = $cart->getProducts()){
            $npa = isset($cookie->npa) ? $cookie->npa : '';
            foreach ($products as $product) {
                if(in_array($product['id_product'], $this->id_product_sapins)){
                    $hasSapin = true;
                    break;
                }
            }
        }

        $id_shop = Context::getContext()->shop->id;
        $get_partner_sql = "select part.partner_id, part.name , part.img, part.description 
                            from ps_partners part
                            join ps_warehouse_carrier wc on wc.id_warehouse = part.warehouse_id
                            join ps_gszonevente_region r on r.id_carrier = wc.id_carrier
                            join ps_gszonevente_npa npa on npa.id_gszonevente_region = r.id_gszonevente_region
                            where npa.name = $npa AND part.shop_id = $id_shop AND r.id_shop = $id_shop";
        $partner         = Db::getInstance()->getRow($get_partner_sql);
        if(!$partner){
            $partner['name'] = 'Poste';
            $partner['description'] = 'Votre sapin sera livré par Poste';
            $partner['img'] = 'post.png';
        }else{
            $active_lang = $this->context->language->id;

            $get_partner_lang_sql = "
                select description from "._DB_PREFIX_."partners_lang
                where id_partner = ".$partner['partner_id']."
                and id_lang = '$active_lang'
            ";
            $partner['description'] = Db::getInstance()->getValue($get_partner_lang_sql);

        }


        $AllCombinations = $this->getAllCombinations();
        $DefaultCombination  = [];
        foreach ($AllCombinations as $Combination) {
            if ($Combination['default'] == 1)  {
                $DefaultCombination = $Combination;
                break;
            }
        }

        if (empty($DefaultCombination) && !empty($AllCombinations)) {
            $DefaultCombination = $AllCombinations[0];
        }

        $tailles = $this->getTailleDisponible($npa, false);

        $smarty->assign(array(
            "types" => $this->getTypeDisponible($npa),
            "npa" => $npa,
            "hasSapin" => $hasSapin,
            "id_type" => $this->getValueTunnelVent('type'),
            "partner" => $partner,
            "tailles"             => $tailles[0],
            "attributs"         => $tailles[1],
            "choix"             => $this->getChoixDisponible($npa),
            "essence"             => $this->getEssenceDisponible($npa),
            "allCombinations"     =>    $AllCombinations,
            "defaultCombination"    =>  $DefaultCombination,
            "id_attribute_taille" => $this->getValueTunnelVent("id_attribute_taille") ? $this->getValueTunnelVent("id_attribute_taille") : '_',
            "isSapinSwiss"        => false,
            "selectedTaille"       => [],
            "typetpl"             => "ecosapin"
        ));

        $html = $smarty->fetch(dirname(__FILE__) . "/../../views/templates/front/".self::$TEMPLATE);
        return $html;
    }



}

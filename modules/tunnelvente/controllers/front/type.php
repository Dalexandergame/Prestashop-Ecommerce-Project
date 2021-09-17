<?php

if (!defined('_PS_VERSION_'))
    exit;

require_once dirname(__FILE__).'/Front.php';
require_once dirname(__FILE__).'/../../../planningdeliverybycarrier/classes/PlanningDeliveryByCarrierException.php';
require_once dirname(__FILE__).'/../../../planningdeliverybycarrier/classes/PlanningRetourByCarrierException.php';

class TunnelVenteTypeModuleFrontController extends Front {

    protected static $TEMPLATE = "type.tpl";

    public function init() {

        $this->page_name = 'typesapain';

        parent::init();

        $this->display_column_left  = false;
        $this->display_column_right = false;

        if ($this->ajax && $this->isXmlHttpRequest()) {

            if (Tools::isSubmit('npa')) {

                $npa =  Tools::getValue("npa");
                $this->context->cookie->__set('npa', $npa);
                $this->addValueTunnelVent('npa', $npa);

                $dateDispo = PlanningDeliveryByCarrierException::getDateDisponibleByNPA();
                $dateDispoR = PlanningRetourByCarrierException::getDateDisponibleByNPA();

                if(!count($dateDispo) || !count($dateDispoR)){
                    $this->errors[] = Tools::displayError('Tous nos jours de livraison de ce district sont complets pour cette année. Rendez-vous en 2018!');
                }


                $return = [
                    'hasError' => !empty($this->errors),
                    'errors'   => $this->errors,
                    'html'     => $this->getHtmlType($npa),
                    'numStep'  => 2,
                ];

                die(json_encode($return));

            }else{

                $npa  = $this->getValueTunnelVent("npa"); //$this->context->cookie->npa;
                $type = $this->getValueTunnelVent("type");

                if (Tools::isSubmit('back')) {

                    $back = (int) Tools::getValue('back');

                    if ($back == 2) { // 2 type

                        $return = [
                            'hasError' => !empty($this->errors),
                            'errors'   => $this->errors,
                            'html'     => $this->getHtmlType($npa),
                            'numStep'  => 2,
                        ];

                    }  else { // 1 npa

                        $return = [
                            'hasError' => !empty($this->errors),
                            'errors'   => $this->errors,
                            'html'     => $this->getHtml($npa),
                            'numStep'  => 1,
                        ];
                    }
                }

            }

            die(json_encode($return));
        }
    }


    public function initContent() {

        parent::initContent();

        $steps     = $this->getSteps();
        $typeSapin = [];
        $taille    = '';

        if (Tools::isSubmit("npa")) {

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

                if (!count($dateDispo)) {

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

            if (!Tools::isSubmit("back")) {

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

        $cart     = $this->context->cart;
        $cookie   = $this->context->cookie;
        $hasSapin = false;

        if (isset($cart) && $products = $cart->getProducts()) {

            $npa = isset($cookie->npa) ? $cookie->npa : '';

            foreach ($products as $product) {
                if (in_array($product['id_product'], $this->id_product_sapins)) {
                    $hasSapin = true;
                    break;
                }
            }
        }

        $this->context->smarty->assign(
            [
                'steps'           => $steps,
                'errors'          => $this->errors,
                "result"          => $typeSapin,
                "npa"             => $npa,
                "hasSapin"        => $hasSapin,
                "isTunnelEnabled" => Configuration::get('TUNNELVENTE_ENABLED')
            ]
        );

        $this->setTemplate('module:tunnelvente/views/templates/front/index.tpl');
    }

    private function getHtml($npa) {
        $smarty   = $this->context->smarty;

        $cart     = $this->context->cart;
        $cookie   = $this->context->cookie;
        $hasSapin = false;

        if (isset($cart) && $products = $cart->getProducts()) {

            $npa = isset($cookie->npa) ? $cookie->npa : '';
            foreach ($products as $product) {
                if (in_array($product['id_product'], $this->id_product_sapins)) {
                    $hasSapin = true;
                    break;
                }
            }
        }

        $smarty->assign(
            [
                "npa"      => ($npa) ? $npa : '',
                "hasSapin" => $hasSapin,
            ]
        );

        return $smarty->fetch(dirname(__FILE__) . "/../../views/templates/front/npa.tpl");
    }
    private function getHtmlType($npa) {
        $smarty   = $this->context->smarty;

        $cart     = $this->context->cart;
        $cookie   = $this->context->cookie;
        $hasSapin = false;

        if (isset($cart) && $products = $cart->getProducts()) {

            $npa = isset($cookie->npa) ? $cookie->npa : '';
            foreach ($products as $product) {
                if (in_array($product['id_product'], $this->id_product_sapins)) {
                    $hasSapin = true;
                    break;
                }
            }
        }

        $get_partner_sql = "select part.partner_id, part.name , part.img, part.description
                            from ps_partners part
                            join ps_warehouse_carrier wc on wc.id_warehouse = part.warehouse_id
                            join ps_gszonevente_region r on r.id_carrier = wc.id_carrier
                            join ps_gszonevente_npa npa on npa.id_gszonevente_region = r.id_gszonevente_region
                            where npa.`name` = $npa AND part.shop_id = '". Context::getContext()->shop->id ."'";
        $partner         = Db::getInstance()->getRow($get_partner_sql);

        if (!$partner) {
            $partner['name'] = 'Poste';
            // $partner['description'] = $this->module->l('Votre sapin sera livré par Poste');
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

        $smarty->assign(
            [
                "types"    => $this->getTypeDisponible($npa),
                "npa"      => $npa,
                "hasSapin" => $hasSapin,
                "id_type"  => $this->getValueTunnelVent('type'),
                "partner"  => $partner,
            ]
        );

        return $smarty->fetch(dirname(__FILE__) . "/../../views/templates/front/".self::$TEMPLATE);
    }

}

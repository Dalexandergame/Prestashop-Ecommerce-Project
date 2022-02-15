<?php

if (!defined('_PS_VERSION_'))
    exit;

require_once dirname(__FILE__) . "/FrontAbies.php";
require_once dirname(__FILE__) . "/little.php";
require_once dirname(__FILE__).'/../../../planningdeliverybycarrier/classes/PlanningDeliveryByCarrierException.php';

class TunnelVenteAbiesTailleModuleFrontController extends TunnelVenteLittleAbiesModuleFrontController
{

    protected static $TEMPLATE = "taille.tpl";

    public function init()
    {
        $this->page_name = 'taillespain';
        frontAbies::init();
        $this->display_column_left  = false;
        $this->display_column_right = false;

        if ($this->ajax && $this->isXmlHttpRequest()) {

            if (Tools::isSubmit("type")) {
                $type = Tools::getValue("type");

                $this->context->cookie->__set('type', $type);
                $this->addValueTunnelVent('type', $type);

                if ($type == Configuration::get('TUNNELVENTE_ID_LITTLE_ECOSAPIN')) {
                    parent::init();
                }
            } else if (Tools::isSubmit("back")) {
                $type = 13;
                $npa = $this->getValueTunnelVent("npa");

                if (Tools::isSubmit("pied")) {
                    $this->addValueTunnelVent('id_product_pied', Tools::getValue("pied"));
                }

                $return = array(
                    'hasError' => !empty($this->errors),
                    'errors' => $this->errors,
                    'html' => $this->getHtmlType($npa, $type),
                    'numStep' => 2,
                );
                die(Tools::jsonEncode($return));
            }

            $return = array(
                'hasError' => !empty($this->errors),
                'errors'   => $this->errors,
                'html'     => $this->getHtmlTaille($type),
                'numStep'  => 2,
            );
            die(Tools::jsonEncode($return));
        }
    }

    public function initContent()
    {
        parent::initContent();

        $steps      = $this->getSteps();
        $taileSapin = array();
        $taille     = '';
        $npa        = $this->getValueTunnelVent("npa");

        if (Tools::isSubmit("type")) {

            $type = Tools::getValue("type");
            $this->addValueTunnelVent("type", $type);
            $dateDispo = PlanningDeliveryByCarrierException::getDateDisponibleByNPA();

            if (!count($dateDispo)) {
                $this->errors[] = Tools::displayError($this->trans('Tous nos jours de livraison de ce district sont complets pour cette année. Rendez-vous en 2022!', [], 'Modules.Tunnelventeabies.Taille', $this->context->language->locale));
                //activer npa
                $steps->getStepByPosition(1)->setActive(true)
                      ->getStepDetailByPosition(1)->setActive(true)
                ;
            } else {
                //activer taille
                $steps->getStepByPosition(1)->setActive(true)
                      ->getStepDetailByPosition(3)->setActive(true)
                ;
                $taileSapin = $this->getTailleDisponible($npa, $type);

            }

        } else {
            $type = $this->getValueTunnelVent("type");
            if (!Tools::isSubmit("back")) {
                //activer npa
                $steps->getStepByPosition(1)->setActive(true)
                      ->getStepDetailByPosition(2)->setActive(true)
                ;
            } else {
                $taille = $this->getValueTunnelVent("id_attribute_taille");//$this->context->cookie->id_attribute_taille;
                $steps->getStepByPosition(1)->setActive(true)
                      ->getStepDetailByPosition(3)->setActive(true)
                ;
                $taileSapin = $this->getTailleDisponible($npa, $type);
            }
        }

        $this->context->smarty->assign(
            array(
                'steps'               => $steps,
                'errors'              => $this->errors,
                "result"              => $taileSapin,
                "id_attribute_taille" => $taille,
            )
        );

        $this->setTemplate('index.tpl');
    }


    private function getHtmlTaille($type)
    {
        $npa    = $this->getValueTunnelVent("npa");
        $smarty = $this->context->smarty;

        $typetpl = "ecosapin";
        if ($type == Configuration::get('TUNNELVENTE_ID_ECOSAPIN')) {
            $typetpl = "ecosapin";
        } else if ($type == Configuration::get('TUNNELVENTE_ID_SAPIN_SUISSE')) {
            $typetpl = "sapinsuisse";
        }

        $smarty->assign(
            array(
                "tailles"             => $this->getTailleDisponible($npa, $type),
                "id_attribute_taille" => $this->getValueTunnelVent("id_attribute_taille") ? $this->getValueTunnelVent("id_attribute_taille") : '_',
                "isSapinSwiss"        => $typetpl == "sapinsuisse",
                "typetpl"             => $typetpl
            )
        );

        return $smarty->fetch(dirname(__FILE__) . "/../../views/templates/front/" . self::$TEMPLATE);
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

        $get_partner_sql = "select part.partner_id, part.name , part.img, part.description 
                            from ps_partners part
                            join ps_warehouse_carrier wc on wc.id_warehouse = part.warehouse_id
                            join ps_gszonevente_region r on r.id_carrier = wc.id_carrier
                            join ps_gszonevente_npa npa on npa.id_gszonevente_region = r.id_gszonevente_region
                            where npa.`name` = $npa AND part.shop_id = '". Context::getContext()->shop->id ."'";
        $partner         = Db::getInstance()->getRow($get_partner_sql);
        if(!$partner){
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


        $id_product_sapin          = (int) $this->getValueTunnelVent("id_product_sapin");
        if (!empty($id_product_sapin)) {
            $AllCombinations = $this->getAllCombinations($id_product_sapin);
        } else {
            $AllCombinations = $this->getAllCombinations();
        }

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

        $html = $smarty->fetch(dirname(__FILE__) . "/../../views/templates/front/typeEdited.tpl");
        return $html;
    }
}

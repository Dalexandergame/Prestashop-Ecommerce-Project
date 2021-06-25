<?php

if (!defined('_PS_VERSION_'))
    exit;

require_once dirname(__FILE__) . "/Front.php";
require_once dirname(__FILE__) . "/little.php";

class TunnelVenteTailleModuleFrontController extends TunnelVenteLittleModuleFrontController
{

    protected static $TEMPLATE = "taille.tpl";

    public function init()
    {
        $this->page_name = 'taillespain';

        Front::init();

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
                $type = $this->getValueTunnelVent("type");
            }

            $return = [
                'hasError' => !empty($this->errors),
                'errors'   => $this->errors,
                'html'     => $this->getHtmlTaille($type),
                'numStep'  => 3,
            ];

            die(json_encode($return));
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
            $dateDispo = PlanningDeliveryByCarrierExceptionOver::getDateDisponibleByNPA();

            if (!count($dateDispo)) {
                $this->errors[] = Tools::displayError('Tous nos jours de livraison de ce district sont complets pour cette année. Rendez-vous en 2018!');
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
            [
                'steps'               => $steps,
                'errors'              => $this->errors,
                "result"              => $taileSapin,
                "id_attribute_taille" => $taille,
            ]
        );

        $this->setTemplate('index.tpl');
    }


    private function getHtmlTaille($type)
    {
        $npa     = $this->getValueTunnelVent("npa");
        $smarty  = $this->context->smarty;
        $typetpl = "ecosapin";

        if ($type == Configuration::get('TUNNELVENTE_ID_ECOSAPIN')) {
            $typetpl = "ecosapin";
        } else if ($type == Configuration::get('TUNNELVENTE_ID_SAPIN_SUISSE')) {
            $typetpl = "sapinsuisse";
        }

        $smarty->assign(
            [
                "tailles"             => $this->getTailleDisponible($npa, $type),
                "id_attribute_taille" => $this->getValueTunnelVent("id_attribute_taille") ? $this->getValueTunnelVent("id_attribute_taille") : '_',
                "isSapinSwiss"        => $typetpl == "sapinsuisse",
                "typetpl"             => $typetpl,
                "base_url"            => Tools::usingSecureMode() ? _PS_BASE_URL_SSL_ : _PS_BASE_URL_
            ]
        );

        return $smarty->fetch(dirname(__FILE__) . "/../../views/templates/front/" . self::$TEMPLATE);
    }
}

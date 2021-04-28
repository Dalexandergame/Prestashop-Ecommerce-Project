<?php

if (!defined('_PS_VERSION_'))
    exit;

require_once dirname(__FILE__) . "/Front.php";

class tunnelventeautresapinModuleFrontController extends Front
{
    protected static $TEMPLATE = "autresapin.tpl";

    public function init()
    {
        $this->page_name = 'autresapin';
        parent::init();

        $this->display_column_left  = false;
        $this->display_column_right = false;

        if ($this->ajax && $this->isXmlHttpRequest()) {
            if (Tools::isSubmit('pot')) {
                $pot = Tools::getValue("pot");

                if (!is_numeric($pot) && $pot <= 0) {
                    $this->errors[] = Tools::displayError("erreur : Choisissez un pot !");
                } else {
                    $this->addValueTunnelVent('id_product_pot', $pot);//$this->context->cookie->__set('id_product_pot', $pot);
                }
            } else if (Tools::isSubmit("back")) {
                //DO nothing
            } else {
                $this->errors[] = Tools::displayError("erreur : Choisissez un pot !");
            }
        }

        $return = array(
            'hasError' => !empty($this->errors),
            'errors'   => $this->errors,
            'html'     => $this->getHtmlAutreSapin(),
            'numStep'  => 7,
        );
        die(Tools::jsonEncode($return));
    }

    private function getHtmlAutreSapin()
    {
        $little = false;
        $smarty = $this->context->smarty;

        if ($this->getValueTunnelVent("type") == Configuration::get('TUNNELVENTE_ID_LITTLE_ECOSAPIN')) {
            $little = true;
        }

        $smarty->assign(
            array(
                'little' => $little
            )
        );

        return $smarty->fetch(dirname(__FILE__) . "/../../views/templates/front/" . self::$TEMPLATE);
    }


}

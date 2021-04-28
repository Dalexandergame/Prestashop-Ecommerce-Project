<?php

if (!defined('_PS_VERSION_'))
    exit;

require_once dirname(__FILE__) . "/Front.php";

class tunnelventesapinModuleFrontController extends Front
{

    protected static $TEMPLATE = "sapin.tpl";

    public function init()
    {
        $this->page_name = 'taillespain';
        parent::init();
        $this->display_column_left  = false;
        $this->display_column_right = false;

        if ($this->ajax && $this->isXmlHttpRequest() /* && Tools::isSubmit('type')/* */) {
            $npa          = (int) $this->getValueTunnelVent("npa");//$this->context->cookie->npa;
            $id_attribute = 0;
            if (Tools::isSubmit("taille")) {
                $id_attribute = (int) Tools::getValue("taille", 0);
                $this->addValueTunnelVent("id_attribute_taille", $id_attribute);//$this->context->cookie->__set('id_attribute_taille', $id_attribute);

            }
            if (Tools::isSubmit("back")) {
                $id_attribute = $this->getValueTunnelVent("id_attribute_taille");//$this->context->cookie->id_attribute_taille;
            }
            $return = array(
                'html'     => $this->getHtml($id_attribute, $npa),
                'hasError' => !empty($this->errors),
                'errors'   => $this->errors,
                'numStep'  => 4,
            );
            // this line is to skip this step
            //  parent::init();
            die(Tools::jsonEncode($return));
        }
    }

    public function initContent()
    {
        parent::initContent();

        $steps = $this->getSteps();
        $sapin = array();

        if (Tools::isSubmit("taille")) {
            $taille = Tools::getValue("taille");
            if (!is_numeric($taille)) {

                $this->errors[] = Tools::displayError('erreur : choisi la taille de sapin !');
                //activer taille
                $steps->getStepByPosition(1)->setActive(true)
                      ->getStepDetailByPosition(3)->setActive(true)
                ;
                $sapin = $this->getTailleDisponible((int) $this->getValueTunnelVent("npa"), (int) $this->getValueTunnelVent("type"));
            } else {
                $npa          = (int) $this->getValueTunnelVent("npa");//$this->context->cookie->npa;
                $id_attribute = (int) $taille;
                $this->addValueTunnelVent("id_attribute_taille", $id_attribute);//$this->context->cookie->__set('id_attribute_taille', $id_attribute);
                //activer choix de l'essence
                $steps->getStepByPosition(1)->setActive(true)
                      ->getStepDetailByPosition(4)->setActive(true)
                ;
                $sapin = $this->getSapinDisponible($id_attribute, $npa, $this->getValueTunnelVent("type"));
            }
        } else {
            $taille = $this->getValueTunnelVent("id_attribute_taille");//$this->context->cookie->id_attribute_taille;
            //activer taille
            $steps->getStepByPosition(1)->setActive(true)
                  ->getStepDetailByPosition(4)->setActive(true)
            ;
            $sapin = $this->getSapinDisponible($taille, (int) $this->getValueTunnelVent("npa"), $this->getValueTunnelVent("type"));
            // TODO:
        }


        $this->context->smarty->assign(array(
                                           'steps'               => $steps,
                                           'errors'              => $this->errors,
                                           "result"              => $sapin,
                                           "id_attribute_taille" => $taille,
                                           "npa"                 => $this->getValueTunnelVent("npa"),//$this->context->cookie->npa,
                                           "id_product_sapin"    => ($this->getValueTunnelVent("id_product_sapin")/*$this->context->cookie->id_product_sapin*/) ? $this->getValueTunnelVent("id_product_sapin")/*$this->context->cookie->id_product_sapin*/ : null,
                                       )
        );

        $this->setTemplate('index.tpl');
    }

    private function getHtml($id_attribute_taille, $npa)
    {
        $smarty = $this->context->smarty;

        $smarty->assign(array(
                            "result"           => $this->getSapinDisponible($id_attribute_taille, $npa, $this->getValueTunnelVent("type")),
                            "id_product_sapin" => $this->getValueTunnelVent("id_product_sapin") ?
                                $this->getValueTunnelVent("id_product_sapin") : null,
                            "isSapinSwiss"     => $this->getValueTunnelVent('type') == 13,
                        )
        );

        return $smarty->fetch(dirname(__FILE__) . "/../../views/templates/front/" . self::$TEMPLATE);
    }

    public function getSapinDisponible($id_attribute = 0, $npa = 0, $type = 0)
    {
        $id_lang = $this->context->language->id;
        if (!$npa) {
            $this->errors[] = Tools::displayError('erreur : saisir NPA !');
            return null;
        }
        if (!$id_attribute) {
            $this->errors[] = Tools::displayError('erreur : choisi la taille de sapin !');
            return null;
        }
        if (!$type) {
            $this->errors[] = Tools::displayError('erreur : choisi le type de sapin !');
            return null;
        }
        //systeme de stock est active
        $sqlEntrepotByNPA = SqlRequete::getSqlEntrepotByNPA($npa);

        $DefaultEntrepotByNPA = Configuration::get('TUNNELVENTE_DEFAULT_ENTROPOT_STOCK_DISPO');// Entrepot par defaut quand il y a pas de NPA dans la BDD
        //test stock dispo pour cette NPA ou non
        $countEntrop = Db::getInstance()->getValue("SELECT COUNT(*) FROM ($sqlEntrepotByNPA) tEntropot");
        if ($countEntrop <= 0) {
            $sqlEntrepotByNPA = $DefaultEntrepotByNPA;
        }
        //product_attribute
        $sql = SqlRequete::getSqlProductAttributAndImage($id_lang) . " WHERE id_attribute  = $id_attribute AND p.id_category_default = $type AND st.`usable_quantity` > 0 AND p.active = 1";


        //Parceque le petit sapin suisse est accessible pour toutes les NPA , on enleve la condition de l'entrepot
        if ($id_attribute != Configuration::get('TUNNELVENTE_ID_ATTRIBUTE_PETIT_SAPIN_SUISSE')) {
            $sql .= " AND st.id_warehouse IN($sqlEntrepotByNPA)";
        }
        $result   = Db::getInstance()->executeS($sql);
        $products = array();
        foreach ($result as $row) {
            if ($row['id_product_attribute'] == 1402 && $row['id_product'] == 54 && $row['id_warehouse'] != 1) continue;
            if ($row['id_product_attribute'] == 1529 && $row['id_product'] == 3 && $row['id_warehouse'] == 1) continue;
            $row['price_ttc'] = number_format(Product::getPriceStatic($row["id_product"], true, $row['id_product_attribute']), 2);
            $products[]       = $row;
        }

        return $products;
    }

}

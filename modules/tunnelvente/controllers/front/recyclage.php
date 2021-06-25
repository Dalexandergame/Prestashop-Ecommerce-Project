<?php

if (!defined('_PS_VERSION_'))
    exit;

require_once dirname(__FILE__) . "/Front.php";
require_once dirname(__FILE__) . "/boule.php";

class TunnelVenteRecyclageModuleFrontController extends TunnelVenteBouleModuleFrontController
{

    protected static $TEMPLATE = "recyclage.tpl";
    protected        $id_product_recyclage;

    function setId_product_recyclage()
    {

        // le retour est payant pour Sapin Suisse
        // Si une personne a pris un ecosapin + un sapin suisse (le retour ecosapin étant gratuit) le retour pour les deux sapin est gratuit.
        // Si il prend 2 sapin suisse, ou plus, il ne paie qu’une seul fois

        $type = $this->getValueTunnelVent("type");

        $cart = $this->context->cart;

        $this->id_product_recyclage = Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_ECOSAPIN_GRATUIT');

        $sapin_suisse = 0;
        if ($cart && $products = $cart->getProducts()) {
            foreach ($products as $product) {
                if ($product['id_category_default'] == Configuration::get('TUNNELVENTE_ID_SAPIN_SUISSE')) {
                    $sapin_suisse++;
                }
            }
        }

        if (!$cart->getProducts() && $type == Configuration::get('TUNNELVENTE_ID_SAPIN_SUISSE')) { // un seul sapin suisse => retour payant
            $this->id_product_recyclage = Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_SAPIN_SUISSE_PAYANT');
        } else if ($cart->getProducts() && $type == Configuration::get('TUNNELVENTE_ID_SAPIN_SUISSE') && $sapin_suisse > 0) {
            $this->id_product_recyclage = Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_SAPIN_SUISSE_GRATUIT');
        }

    }

    public function __construct()
    {
        parent::__construct();
    }

    public function init()
    {
        $this->page_name = 'taillespain';
        Front::init();

        $this->display_column_left  = false;
        $this->display_column_right = false;
        $this->setId_product_recyclage();

        if ($this->ajax && $this->isXmlHttpRequest() /* && Tools::isSubmit('type')/* */) {
            $last_id_recyclage_checked = ($this->getValueTunnelVent("id_product_recyclage") === false) ? $this->id_product_recyclage : $this->getValueTunnelVent("id_product_recyclage");//null;

            if (Tools::isSubmit("taille")) {
                $npa      = (int) $this->getValueTunnelVent("npa");
                $taille   = (int) Tools::getValue("taille");
                $products = $this->getSapinDisponible($taille, $npa, $this->getValueTunnelVent("type"));

                if (count($products)) {
                    $sapin = $products[0]['id_product_attribute'];

                    $this->addValueTunnelVent("id_attribute_taille", $taille);
                    $this->addValueTunnelVent('id_product_sapin', $sapin);
                }
            } else if (Tools::isSubmit("pied")) {
                $pied = Tools::getValue("pied");

                if (!is_numeric($pied) && $pied <= 0) {
                    $this->errors[] = Tools::displayError("erreur : Choisissez un type de pied !");
                } else {
                    $this->addValueTunnelVent('id_product_pied', $pied);
                }

            } else if (Tools::isSubmit("back")) {
                $last_id_recyclage_checked = $this->getValueTunnelVent("id_product_recyclage");
            } else {
                $this->errors[] = Tools::displayError("erreur : Choisissez l'essence de votre sapin !");
            }

            if (Tools::isSubmit("back")) {
                $last_id_recyclage_checked = $this->getValueTunnelVent("id_product_recyclage");
            }

            $isSapinSwiss = $this->getValueTunnelVent('type') == 13;

            $return = [
                'html'          => $this->getHtml($last_id_recyclage_checked),
                'hasError'      => !empty($this->errors),
                'errors'        => $this->errors,
                'numStep'       => $isSapinSwiss ? 5 : 4,
                'supp'          => $this->getValuesTunnelVent(),
                'order_process' => Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order',
            ];

            die(json_encode($return));
        }
    }

    public function initContent()
    {
        parent::initContent();

        $steps                     = $this->getSteps();
        $sapin                     = null;
        $last_id_recyclage_checked = $this->id_product_recyclage;
        $isSapinSwiss              = $this->getValueTunnelVent('type') == 13;

        if ($isSapinSwiss) {
            if (Tools::isSubmit('pied')) {
                $pied = Tools::getValue("pied");

                if (!is_numeric($pied) && $pied <= 0) {
                    $this->errors[] = Tools::displayError("erreur : Choisissez un type de pied !");
                } else {
                    $this->addValueTunnelVent('id_product_pied', $pied);
                }
            } else {
                $this->errors[] = Tools::displayError("erreur : Choisissez un pied !");
            }
        } else {
            $sapin                     = $this->getValueTunnelVent("id_product_sapin");
            $last_id_recyclage_checked = $this->getValueTunnelVent("id_product_recyclage");

            if (!is_numeric($sapin) || $sapin <= 0) {
                $this->errors[] = Tools::displayError("erreur : Choisissez l'essence de votre sapin !");
            } else {
                $this->addValueTunnelVent('id_product_sapin', $sapin);

                //activer recyclage
                $steps->getStepByPosition(1)->setActive(true)
                      ->getStepDetailByPosition(5)->setActive(true)
                ;
            }
        }

        $this->context->smarty->assign(
            [
                'steps'                     => $steps,
                'errors'                    => $this->errors,
                "result"                    => $this->getProductRecyclage(),
                'last_id_recyclage_checked' => $last_id_recyclage_checked,
                'order_process'             => Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order',
            ]
        );

        $this->setTemplate('index.tpl');
    }

    private function getHtml($last_id_recyclage_checked)
    {
        $smarty = $this->context->smarty;

        $image = $this->getValueTunnelVent('id_product_sapin') . ".png";

        $product_attribute_id = $this->getValueTunnelVent('id_product_sapin');
        $npa                  = $this->getValueTunnelVent('npa');

        // get price and type
        $get_product_info_sql = "SELECT price, id_product FROM ps_product_attribute where id_product_attribute = $product_attribute_id;";
        $get_partner_sql      = "select part.partner_id, part.name , part.img, part.description 
                            from ps_partners part
                            join ps_warehouse_carrier wc on wc.id_warehouse = part.warehouse_id
                            join ps_gszonevente_region r on r.id_carrier = wc.id_carrier
                            join ps_gszonevente_npa npa on npa.id_gszonevente_region = r.id_gszonevente_region
                            where npa.`name` = $npa";

        $product_info         = Db::getInstance()->getRow($get_product_info_sql);
        $partner              = Db::getInstance()->getRow($get_partner_sql);

        if (!$partner) {
            $partner['name'] = 'Poste';
            $partner['img']  = 'post.png';
        }

        $lang = $this->context->language->id;

        $get_taille_sql = "select al.name from ps_product_attribute pa
            join ps_product_attribute_combination pac on pac.id_product_attribute = pa.id_product_attribute
            join ps_attribute a on a.id_attribute = pac.id_attribute
            join ps_attribute_lang al on a.id_attribute = al.id_attribute
            where pac.id_product_attribute = $product_attribute_id and al.id_lang = $lang";

        $taille = Db::getInstance()->getValue($get_taille_sql);

        $get_type_sql = "SELECT cl.name FROM ps_category_lang cl
        join ps_product p on cl.id_category = p.id_category_default
        where id_product = '" . $product_info['id_product'] . "' and cl.id_lang = $lang";

        $type = Db::getInstance()->getValue($get_type_sql);

        $resume = [
            "transporteur"     => $partner['name'],
            "transporteur_img" => $partner['img'],
            "type"             => $type,
            "taille"           => substr($taille, 0, strpos(strtolower($taille), "cm") + 2),
            "prix"             => $product_info['price']
        ];

        $smarty->assign(
            [
                "product"                   => $this->getProductRecyclage(),
                "isSapinSwiss"              => $this->getValueTunnelVent('type') == 13,
                'last_id_recyclage_checked' => $last_id_recyclage_checked,
                'order_process'             => Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order',
                'image_recyclage'           => $image,
                'resume'                    => $resume,
                "base_url"                  => Tools::usingSecureMode() ? _PS_BASE_URL_SSL_ : _PS_BASE_URL_
            ]
        );

        return stripslashes($smarty->fetch(dirname(__FILE__) . "/../../views/templates/front/" . self::$TEMPLATE));

    }

    private function getProductRecyclage()
    {
        $product = new Product($this->id_product_recyclage, false, $this->context->language->id);

        return [
            "id"                => $product->id,
            "description_short" => $product->description_short,
            "description"       => $product->description,
            "price"             => $product->getPrice(),
        ];
    }

}

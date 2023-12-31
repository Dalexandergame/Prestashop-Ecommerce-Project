<?php

error_reporting(0);
ini_set('display_errors', false);
require_once dirname(__FILE__) . '/../../config/config.inc.php';
include("wsbc-init.php");
include("wsbc-utils.php");
include("PDFMerger.php");
error_reporting(0);
ini_set('display_errors', false);
function wd_remove_accents($text, $charset = 'utf-8')
{
    $text = /*utf8_encode*/
        ($text);
    $text = str_replace("Mylittleecosapin", 'Little', $text);

    $text    = str_replace("cm", '', $text);
    $text    = str_replace("en pot", '', $text);
    $search  = explode(",", "ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u");
    $replace = explode(",", "c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u");
    $Title   = str_replace($search, $replace, $text); //coupé avec pied
    $Title   = str_replace("coupe avec pied", '', $Title);
    $Title   = trim($Title);
    return mb_substr($Title, 0, 23);
}

function getIdProductSapins($cat)
{
    $sql    = "SELECT id_product FROM " . _DB_PREFIX_ . "product
               WHERE id_category_default IN (" . implode(",", $cat) . ")";
    $result = Db::getInstance()->executeS($sql);

    $idP = array();
    foreach ($result as $res) {
        $idP[] = $res["id_product"];
    }
    return $idP;
}

set_time_limit(0);
//**

$id_lang = 2;
//$id_products_sapin = array(2, 3, 4, /* 52, 53, */ 54);
$id_products_sapin                = getIdProductSapins(array(Configuration::get('TUNNELVENTE_ID_SAPIN_SUISSE')));
$id_product_mylittel              = array(92, 94);
$id_attribute_group_TailleCouleur = array(1, 7);
$id_attribute_group_Decoration    = array(94);
$id_attribute_group_Couleurpot    = array(92);
//$id_product_retour = 37;
$id_product_retour = array(Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_ECOSAPIN_GRATUIT'), Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_SAPIN_SUISSE_PAYANT'), Configuration::get('TUNNELVENTE_ID_PRODUCT_RECYCLAGE_SAPIN_SUISSE_GRATUIT'));
//$id_produts_genereEtiqueete = $id_product_mylittel;//array(2, 3, 4, 50, 54);
$id_produts_genereEtiqueete = array_merge($id_products_sapin, $id_product_mylittel);
$id_carrier                 = (int) Configuration::get('TUNNELVENTE_ID_CARRIER_POST');
$date_depart                = "IF(o.`id_carrier` = $id_carrier,IF( dayofweek(pd.date_delivery - INTERVAL 2 DAY) in (1,7) ,DATE(pd.date_delivery - INTERVAL 4 DAY) , DATE(pd.date_delivery - INTERVAL 2 DAY)) ,DATE(pd.date_delivery))";
$varSql                     = "(SELECT GROUP_CONCAT(pal.name SEPARATOR ', ') AS `name`
                                   FROM `ps_order_detail` pod
                                     JOIN `ps_product_attribute_combination` ppac ON pod.`product_attribute_id` = ppac.`id_product_attribute`
                                     JOIN `ps_attribute` pa ON pa.`id_attribute` = ppac.`id_attribute`
                                     JOIN `ps_attribute_lang` pal ON (pal.`id_attribute` = pa.`id_attribute` AND pal.`id_lang` = 2)
                                   WHERE pod.product_id IN (%s) and pod.id_order = o.id_order)";

$_sql = "
SELECT 
IF(od.`product_id` IN(" . implode(",", $id_products_sapin) . ")," . sprintf($varSql, implode(",", $id_attribute_group_TailleCouleur)) . ",
 IF(app.`id_pack` IN (93), 
    CONCAT(" . sprintf($varSql, implode(",", $id_attribute_group_Couleurpot)) . ",' - '," . sprintf($varSql, implode(",", $id_attribute_group_Decoration)) . "
)

 , od.`product_reference`)
 ) as name_prod 



,{$date_depart} as date_depart,date_delivery,od.`id_order_detail`,od.`id_order`,
od.`product_id`,od.`product_attribute_id`,
GROUP_CONCAT(SUBSTRING_INDEX(od.`product_name`, ': ', -1) SEPARATOR ', ') AS `product_name`,
od.`product_quantity`,
GROUP_CONCAT(od.`product_reference` SEPARATOR ' - ') product_reference,
ad.`company`,ad.`firstname`,ad.`lastname`,ad.`address1`,ad.`address2`,ad.`postcode`,ad.`city`,ad.`other`,cu.`email`

FROM `" . _DB_PREFIX_ . "orders` o 
JOIN `" . _DB_PREFIX_ . "order_detail` od ON (od.`id_order` = o.`id_order`) 
left join ps_pm_advancedpack_products app on app.id_product = od.product_id
LEFT JOIN `ps_product_attribute_combination` pac ON od.product_attribute_id = pac.id_product_attribute
JOIN `" . _DB_PREFIX_ . "address` ad ON (ad.`id_address` = o.`id_address_delivery`)
JOIN `" . _DB_PREFIX_ . "customer` cu ON (cu.`id_customer` = o.`id_customer`)
JOIN `" . _DB_PREFIX_ . "planning_delivery_carrier` pd ON pd.id_order in (select o2.id_order from ps_orders o2 where o2.reference = o.reference)

 
WHERE o.`id_order` IN(%s) AND (app.id_pack = 93 OR pac.id_attribute IN (" . Configuration::get('TUNNELVENTE_ID_ATTRIBUTE_PETIT_SAPIN_SUISSE') . ")) GROUP BY od.id_order";

//*/
$order_ids = Tools::getValue('order_ids');
if (empty($order_ids) || $order_ids == "") {
    die("Aucune commande séléctionnée");
} else {

    $_sql = sprintf($_sql, implode(",", $order_ids));
//    printf($_sql);exit;
    $Db        = Db::getInstance();
    $generated = array();
    $results   = $Db->executeS($_sql);

    $orders = array();
    $_GET['pdf'] = 1;
    foreach ($results as $row) {
        if (in_array($row['product_id'], $id_produts_genereEtiqueete)) {
            $orders[$row['id_order']]['products'] [] = $row;
        } elseif ($row['product_id'] == $id_product_retour) {
            $orders[$row['id_order']]['hasRetour'] = true;
        }
        $order = new Order($row['id_order']);
        changeOrderStatus($order);
    }

    $productsToDeliver = array();
    $date_d            = "";
    $product_attribute_list = Tools::getValue('product_attribute');
    foreach ($orders as $id_order => $order) {
        foreach ($product_attribute_list as $product_attribute) {
            $product_order_detail = Db::getInstance()->getRow('SELECT * FROM `ps_order_detail` WHERE `product_attribute_id`="'.$product_attribute.'" AND `ps_order_detail`.`id_order`='.$id_order);
            foreach ($order['products'] as $product) {
                $name1 = mb_substr($product['company'], 0, 35);
                $name2 = mb_substr($product['firstname'] . " " . $product['lastname'], 0, 35);
                if (strlen($name1) == 0) {
                    $name1 = $name2;
                    $name2 = '';
                }

                $product_attribute_name = isset($product_order_detail) ? trim(ucfirst($product_order_detail['product_reference'])) : mb_substr($product['name_prod'], 0, 5);
                $productName = ucfirst($product['product_reference']);
                $productName = mb_substr("Little " . $product['product_name'], 0, 21) . " " . $product_attribute_name;
                $productName = $productName . " " . (isset($order['hasRetour']) ? " R" : "");
                $productName = trim($productName);

                for ($i = 0; $i < $product['product_quantity']; $i++) {
                    $itemId = isset($product_order_detail) ? $product_order_detail['product_attribute_id'] : $i;
                    $item = array(// 1.Item ...
                                  'ItemID'     => $product['id_order_detail'] ."-". $itemId,
                                  'Recipient'  => array(
                                      'Title'   => mb_substr($productName, 0, 31),
                                      'Vorname' => mb_substr($product['firstname'], 0, 35),
                                      'Name1'   => $name1,
                                      'Name2'   => $name2,
                                      'Street'  => mb_substr($product['address1'], 0, 35),
                                      'POBox'   => mb_substr($product['address2'], 0, 35),
                                      'ZIP'     => $product['postcode'],
                                      'City'    => mb_substr($product['city'], 0, 35),
                                      'EMail'   => $product['email'],
                                  ),
                                  'Attributes' => array(
                                      'PRZL'     => array("ECO"),
                                      //                        'DeliveryDate' => $product['date_depart'],
                                      'ProClima' => true
                                  )
                    );
                    if (($key = array_search('ZAW3217', $item['Attributes']['PRZL'])) !== false) {
                        unset($item['Attributes']['PRZL'][$key]);
                    }
                    if (!in_array($product['product_id'], $id_products_sapin)) {
                        if (($key = array_search('SP', $item['Attributes']['PRZL'])) !== false) {
                            unset($item['Attributes']['PRZL'][$key]);
                        }
                    } else {
                        if (strpos($product['name_prod'], "avec pied")) {
                            $item['Attributes']['FreeText'] = "coupé";
                        }
                    }
                    $przl = array();
                    foreach ($item['Attributes']['PRZL'] as $_val) {
                        $przl[] = $_val;
                    }
                    $item['Attributes']['PRZL'] = $przl;
                    $productsToDeliver[]        = $item;
                    if ($date_d == "")
                        $date_d = $product['date_depart'];
                }
            }
        }
    }
//    var_dump($productsToDeliver);exit;
    generateIfNotExist($productsToDeliver);

    foreach ($productsToDeliver as $i) {

        array_push($generated, $i['ItemID']);
    }

    $pdf = new PDFMerger;
    foreach ($generated as $etiquette) {
        $pdf->addPDF("labels/" . $etiquette . ".pdf", 'all');
        $pdf->addPDF("labels/customer_message.pdf", 'all');
    }
    $pdf->merge('download', 'commandes_' . $date_d . '.pdf');
}

function generateIfNotExist($items)
{
    global $SOAP_Client;
    $allExists = true;

    foreach ($items as $i) {
        //  print_r($i);
        if (!file_exists("labels/" . $i['ItemID'] . ".pdf"))
            $allExists = false;
    }

    if ($allExists)
        return;


    //$frankinglicense = '60044529';
    $frankinglicense = '60039277';
    //$frankinglicense = '024675';
    //$frankinglicense = '40323999';

    $imgfile          = 'logos-etiquettes.gif';
    $logo_binary_data = fread(fopen($imgfile, "r"), filesize($imgfile));

    $generateLabelRequest = array(
        'Language' => 'fr',
        'Envelope' => array(
            'LabelDefinition' => array(
                'LabelLayout'     => 'A6',
                'PrintAddresses'  => 'RecipientAndCustomer',
                'ImageFileType'   => 'PDF',
                'ImageResolution' => 300,
                'PrintPreview'    => false
            ),
            'FileInfos'       => array(
                'FrankingLicense' => $frankinglicense,
                'PpFranking'      => false,
                'Customer'        => array(
                    'Name1'      => 'Ecosapin',
                    'Street'     => 'Le Château',
                    'ZIP'        => '1116',
                    'City'       => 'Cotens',
                    'Logo'       => $logo_binary_data,
                    'LogoFormat' => 'GIF',
                ),
                'CustomerSystem'  => 'PHP Client System'
            ),
            'Data'            => array(
                'Provider' => array(
                    'Sending' => array(
                        'Item' => $items
                    )
                )
            )
        )
    );

    // 2. Web service call
    $response = null;
    try {
        $response = $SOAP_Client->GenerateLabel($generateLabelRequest);

    } catch (SoapFault $fault) {
//        echo('Error in GenerateLabel: ' . $fault->__toString() . '<br />');
    }

    foreach (getElements($response->Envelope->Data->Provider->Sending->Item) as $item) {
        if (@$item->Errors != null) {

            $errorMessages = "";
            $delimiter     = "";
            foreach (getElements($item->Errors->Error) as $error) {
                $errorMessages .= $delimiter . $error->Message;
                $delimiter     = ",";
            }
//                  echo '<p>ERROR for item with itemID='.$item->ItemID.": ".$errorMessages.'.<br/></p>';
        } else {
            $identCode       = $item->IdentCode;
            $labelBinaryData = $item->Label;

            $filename = 'labels/' . $item->ItemID . '.pdf';
            file_put_contents($filename, $labelBinaryData);

//              echo '<p>Label generated successfully for identCode='.$item->ItemID.': <br/>';
            if (@$item->Warnings != null) {
                $warningMessages = "";
                foreach (getElements($item->Warnings->Warning) as $warning) {
                    $warningMessages .= $warning->Message . ",";
                }
//                echo 'with WARNINGS: '.$warningMessages.'.<br/>';
            }

            //echo '</p>';
        }
    }
}

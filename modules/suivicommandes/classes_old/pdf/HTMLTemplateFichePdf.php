<?php

class HTMLTemplateFichePdf extends HTMLTemplate
{
	public $params;
        public $carrier_name;
        public $gkey;
        
	public function __construct($params, $smarty)
	{
            
                $this->gkey = trim(Configuration::get('SUIVI_COMMANDES_GOOGLE_API_KEY'));
		$this->params = $params;
		$this->smarty = $smarty;
                
		// header informations
		$this->title = HTMLTemplateFichePdf::l('Custom Title');
                $carrier = new Carrier((int)$this->params->id_carrier);
                $this->carrier_name = $carrier->name;
		
	}
 
	/**
	 * Returns the template's HTML content
	 * @return string HTML content
	 */
	public function getContent()
	{
                $infos = $this->getFicheInfos();
                
		$this->smarty->assign(array(
		    'infos' => $infos[0],
                    'total' => $infos[1],
                    'markers' => $this->getStaticMap(),
                    'gkey' => $this->gkey
		));
 
		return $this->smarty->fetch(_PS_MODULE_DIR_ . 'suivicommandes/views/templates/pdf/content.tpl');
	}
 
 
	public function getHeader()
	{
		$path_logo = $this->getLogo();

		$width = 0;
		$height = 0;
		if (!empty($path_logo))
			list($width, $height) = getimagesize($path_logo);
 
            $sql = "SELECT soc.text 
                    FROM "._DB_PREFIX_."suivi_orders_carrier soc 
                    WHERE soc.id_carrier = ".(int)$this->params->id_carrier." 
                    AND datediff(soc.date_delivery,'".$this->params->dateLivraison."')=0";
           
            $title = Db::getInstance()->ExecuteS($sql);
            
                $this->smarty->assign(array(
			'logo_path' => $path_logo,
                        'title_text' => $title[0]["text"],
                        'date' => $this->params->dateLivraison,
                        ));
                
		return $this->smarty->fetch(_PS_MODULE_DIR_ . 'suivicommandes/views/templates/pdf/header.tpl');
	}
 
 
	/**
	 * Returns the template filename
	 * @return string filename
	 */
	public function getFilename()
	{
                $cname = str_replace(" ", "_", $this->carrier_name);
		return $this->params->dateLivraison."_".$cname.'.pdf';
	}
 
	/**
	 * Returns the template filename when using bulk rendering
	 * @return string filename
	 */
	public function getBulkFilename()
	{
		return 'fiche_pdf.pdf';
	}
        
        
    public function getFicheInfos()
    {
        $sql_no_returns = "SELECT so.*, 
                IF(datediff(so.date_delivery,'".$this->params->dateLivraison."')=0, 'L', 'R') as type,
                CONCAT(so.firstname, ' ',so.lastname, ' ',so.company) AS customer,
                CONCAT(so.phone, ' - ',so.phone_mobile) AS tel,
                CONCAT(so.address1,' ',so.address2,' ',so.postcode,' ',so.city) AS address 
                FROM "._DB_PREFIX_."suivi_orders so 
                JOIN "._DB_PREFIX_."warehouse as w ON w.id_warehouse = so.id_warehouse
                WHERE so.id_carrier = ".(int)$this->params->id_carrier."
                AND datediff(so.date_delivery,'".$this->params->dateLivraison."')=0
                AND so.id_warehouse IN ".$this->params->warehouse_selected." ORDER BY so.position";


        $sql1 = "SELECT so.*,
                IF(datediff(so.date_delivery,'".$this->params->dateLivraison."')=0, 'L', 'R') as type,
                CONCAT(so.firstname, ' ',so.lastname, ' ',so.company) AS customer,
                CONCAT(so.phone, ' - ',so.phone_mobile) AS tel,
                CONCAT(so.address1,' ',so.address2,' ',so.postcode,' ',so.city) AS address
                FROM "._DB_PREFIX_."suivi_orders so
                JOIN "._DB_PREFIX_."warehouse as w ON w.id_warehouse = so.id_warehouse
                WHERE IF(datediff(so.date_delivery,'".$this->params->dateLivraison."')=0, so.id_carrier = ".(int)$this->params->id_carrier.", so.id_carrier_retour = ".(int)$this->params->id_carrier.")
                AND (datediff(so.date_delivery,'".$this->params->dateLivraison."')=0 OR datediff(so.date_retour,'".$this->params->dateLivraison."')=0)
                AND so.id_warehouse IN ".$this->params->warehouse_selected." ORDER BY so.position";

        $result = Db::getInstance()->ExecuteS($sql1);
        $result_no_returns = Db::getInstance()->ExecuteS($sql_no_returns);
        $commandes_no_returns = '';
        $commandes = '';

        // counting returns
        foreach($result as &$res){
            $commandes .= $res["commande"].',';
            $cmd = explode(',', rtrim($res["commande"], ","));
            
            //Manip pour dupliquer les commandes dont la quantité >1 question de compter le récurence dans le total des quantités
            foreach($cmd as $c){
                $quantity =  substr($c, strrpos($c, 'X') + 1);
                if($quantity>1) { 
                    for($i=0;$i<$quantity-1;$i++){
                        $commandes .= $c.','; 
                    }
                }
            }
            //END
            $res["commande"] = $this->getOrderProducts($res["commande"]);
        }

        // ignoring returns
        foreach($result_no_returns as &$res){
            $commandes_no_returns .= $res["commande"].',';
            $cmd = explode(',', rtrim($res["commande"], ","));

            //Manip pour dupliquer les commandes dont la quantité >1 question de compter le récurence dans le total des quantités
            foreach($cmd as $c){
                $quantity =  substr($c, strrpos($c, 'X') + 1);
                if($quantity>1) {
                    for($i=0;$i<$quantity-1;$i++){
                        $commandes_no_returns .= $c.',';
                    }
                }
            }
            //END
            $res["commande"] = $this->getOrderProducts($res["commande"]);
        }
        
        $recap = explode(',', rtrim($commandes_no_returns, ","));
        $recap = array_map('trim',$recap);
        //Enlever les quantité après le X
        foreach($recap as &$r){
            $r = substr($r, 0, strpos($r, ' X'));
        }
        $total = array_count_values($recap);
        uksort($total,"strnatcasecmp");
        // die("<pre>".print_r($total)."</pre>");
        return array($result,$total);
        
    }
    
    public function getOrderProducts($commande)
    {
        $list='';
        if($commande){
        $list = "* ".str_replace(',', '<br>* ', $commande)."<br>";
        }

        return $list;
    }
    
    
    public function curlExec($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function getlatlng($adr)
    {
        $wait=1;
        while ($wait<=2) {
            
        $address = str_replace(' ', '+', $adr);
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=$address&sensor=false&key=".$this->gkey;

        $response_a = json_decode($this->curlExec($url));
        
        if($response_a->status=='OK'){
            $lat = $response_a && $response_a->status=='OK' ? $response_a->results[0]->geometry->location->lat : NULL ;
            $long = $response_a && $response_a->status=='OK' ? $response_a->results[0]->geometry->location->lng : NULL;
            return array("lat"=> $lat,"long"=> $long);
        }
        else {
            sleep(2);
            $wait++;
        }
        }
        
        $this->alertmsg .="Limite Google API dépassée ou l'adresse ".'"'.$adr.'"'." est incorrecte.<br> Type Erreur Google API: ".$response_a->status."<br>";
        
        return NULL;
    }

    
    public function getStaticMap()
    {
        $sql1 = "SELECT so.*,CONCAT(a.address1,' ',a.address2,' ',a.postcode,' ',a.city) as addresswh,
            CONCAT(so.address1,' ',so.address2,' ',so.postcode,' ',so.city) AS address 
            FROM "._DB_PREFIX_."suivi_orders so 
            JOIN "._DB_PREFIX_."warehouse as w ON w.id_warehouse = so.id_warehouse
            JOIN "._DB_PREFIX_."address as a ON w.id_address = a.id_address
            WHERE IF(datediff(so.date_delivery,'".$this->params->dateLivraison."')=0, so.id_carrier = ".(int)$this->params->id_carrier.", so.id_carrier_retour = ".(int)$this->params->id_carrier.") 
            AND (datediff(so.date_delivery,'".$this->params->dateLivraison."')=0 OR datediff(so.date_retour,'".$this->params->dateLivraison."')=0) 
            AND so.id_warehouse IN ".$this->params->warehouse_selected." ORDER BY so.position";
        
        $result = Db::getInstance()->ExecuteS($sql1);
        
        $listPoints='';
        $whs = array();
        
        foreach($result as $res){
            
            if(!in_array($res["addresswh"], $whs)){
                $Point = $this->getlatlng($res["addresswh"]);
                if(!empty($Point["lat"])){
                    $listPoints .= "&markers=size:mid|color:0xff0000|label:S|".$Point["lat"].",".$Point["long"].'|';
                }
                array_push($whs, $res["addresswh"]); //Stocker l'adress wh pour checker dans la prochaine boucle si elle existe
            }
            
            $Point2 = $this->getlatlng($res["address"]);
            
            if(!empty($Point2["lat"])){
                $listPoints .= "&markers=size:mid|color:0xff0000|label:".$res["position"]."|".$Point2["lat"].",".$Point2["long"].'|';
            }
            
        }
        
        
        return $listPoints;
    }

    public function getFooter()
    {
        return "";
    }
        
}

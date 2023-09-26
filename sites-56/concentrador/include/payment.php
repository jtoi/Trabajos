<?php


class Payment
{
	public $idTransaction	= "";
	public $idCommande 		= "";
	public $lang			= "fr";
	public $vad_number 		= "";
	public $secret_key 		= "";
	public $urlRetourOK 	= "http:///";
	public $urlRetourNOK 	= "http:///";
	public $urlIPN			= "http:///";
	public $typeTr          = "D";
    public $data			= "";
    public $panier			= "";
    
    // http://docs.comnpay.com/psp.html#integration-manuelle-du-formulaire-de-paiement
    public $porteur			= "";
    
    // http://docs.comnpay.com/psp.html#paiement-par-abonnement
    public $abonnement		= "";
    
    // http://docs.comnpay.com/psp.html#integration-manuelle-du-formulaire-de-paiement
    public $codeTemplate	= "";



	function __construct($vad_number = "", $secret_key = "", $urlRetourOK = "", $urlRetourNOK = "", $urlIPN = "", $typeTr = "D")
	{
		$this->vad_number 	= $vad_number;
		$this->secret_key 	= $secret_key;

		$this->urlRetourOK 	= $urlRetourOK;
		$this->urlRetourNOK = $urlRetourNOK;

		$this->urlIPN 		= $urlIPN;
		$this->typeTr		= strtoupper($typeTr);
	}


	function buildSecretHTML($produit="Produit", $montant="0.00", $idTransaction="")
	{
		if($idTransaction == ""){
			$this->idTransaction = time().$this->vad_number.sprintf("%03d",rand(0,999));
		}
		else{
			$this->idTransaction = $idTransaction;	
		}

		$array_tpe = array(
							'montant' 		=> (String)$montant,
							'idTPE' 		=> $this->vad_number,
							'idTransaction' => $this->idTransaction,
							'idCommande' 	=> $this->idCommande,
							'devise' 		=> "EUR",
							'lang' 			=> $this->lang,
							'nom_produit' 	=> $produit,
							'source' 		=> $_SERVER['SERVER_NAME'],
							'urlRetourOK' 	=> $this->urlRetourOK,
							'urlRetourNOK' 	=> $this->urlRetourNOK,
							'typeTr'		=> $this->typeTr,
							'data'			=> $this->data,
							'porteur'		=> base64_encode(json_encode($this->porteur)),
							'abonnement'	=> base64_encode(json_encode($this->abonnement)),
							'codeTemplate'	=> $this->codeTemplate,
							'panier'		=> $this->panier
						);

		if($this->urlIPN != ""){
			$array_tpe['urlIPN'] = $this->urlIPN;
		}


		$array_tpe['key'] = $this->secret_key;
		$strWithKey = base64_encode(implode("|", $array_tpe));
		unset($array_tpe['key']);
		$array_tpe['sec'] = hash("sha512",$strWithKey);


		$return = "";
		foreach ($array_tpe as $key => $value) {
			$return .= '<input type="hidden" name="'.$key.'" value="'.$value.'"/>';
		}

		return $return;
	}
}

function validSec($values, $secret_key){
	if (isset($values['sec']) && $values['sec'] != ""){
		$sec = $values['sec'];
		unset($values['sec']);
		return strtoupper(hash("sha512", base64_encode(implode("|",$values)."|".$secret_key))) == strtoupper($sec);
	}else{
		return false;
	}
}


?>
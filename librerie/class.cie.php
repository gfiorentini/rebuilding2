<?php

class cie {

	public $wsdl_url;
	public $soapClient;
	public $SoapClientTimeout;
	
	//Costruttore vuoto -> utilizzo CURL, provare -> SOAP e impostare wsdl nel costruttore
	public function __construct() {

		$tipo_servizio_cie=get_db_value("SELECT tipo_servizio_cie FROM ".DBNAME_A.".enti WHERE idente=1");
		if($tipo_servizio_cie==1)
			$fldwsdl_url="https://cie.comune-online.it/AuthServiceCIE/services/AuthService?wsdl";
		else
			$fldwsdl_url="https://cie-oidc.comune-online.it/AuthServiceOIDC/services/AuthService?wsdl";

		$this->wsdl_url=$fldwsdl_url;
		$this->soapClient = new soapClient($this->wsdl_url,array('trace' => 1, 'stream_context' => stream_context_create(
            array(
              'ssl' => array(
                'verify_peer' => false, 
                'verify_peer_name' => false
              )
          	)
        )));
	}

	//DEBUG - Elenca le funzioni disponibili
	public function show_functions(){
		echo '<br>$soapClient->__getFunctions()';
		echo "<pre>";
		print_r($this->soapClient->__getFunctions()); 
		echo "</pre>";

		echo '<br>$soapClient->__getTypes()';
		echo "<pre>";
		print_r($this->soapClient->__getTypes()); 
		echo "</pre>";
	}


	public function getAuthId(){
		$error=0;

		try {
			$response=$this->soapClient->getAuthId();
			$array = json_decode(json_encode($response), True);

			$response=$array;

		} catch (SoapFault $fault) {
			$error=2;
			$error_desc='Errore getAuthId: '.$fault->faultcode.' - '.$fault->faultstring;
		}

		$return=array();
		$return["error_code"]=$error;
		$return["error_desc"]=$error_desc;
		$return["response"]=$response;

		return $return;
	}

	public function retrieveUserData($authId){
		$error=0;

		$ap_param = array(
			"authId" => $authId,
		);

		try {
			$response=$this->soapClient->retrieveUserData($ap_param);
			$array = json_decode(json_encode($response), True);

			$response=$array;

		} catch (SoapFault $fault) {
			$error=2;
			$error_desc='Errore retrieveUserData: '.$fault->faultcode.' - '.$fault->faultstring;
		}
		
		
		//echo $this->soapClient->__getLastResponse();
	
		
		//print_r_formatted($error_desc);
		//die;
		$return=array();
		$return["error_code"]=$error;
		$return["error_desc"]=$error_desc;
		$return["response"]=$response;

		return $return;
	}

	public function singleSignOut($authId){
		$error=0;

		$ap_param = array(
			"authId" => $authId,
		);

		try {
			$response=$this->soapClient->singleSignOut($ap_param);
			$array = json_decode(json_encode($response), True);

			$response=$array;

		} catch (SoapFault $fault) {
			$error=2;
			$error_desc='Errore singleSignOut: '.$fault->faultcode.' - '.$fault->faultstring;
		}

		//echo $this->soapClient->__getLastResponse();
		
		//print_r_formatted($error_desc);
		//die;
		$return=array();
		$return["error_code"]=$error;
		$return["error_desc"]=$error_desc;
		$return["response"]=$response;

		return $return;
	}
}

?>
<?php

class CARDNews {

	public $idcard_notizia;
	public $iddara_cliente;
	public $notizia_titolo;
	public $notizia_testo;
	public $notizia_datainizio;
	public $idcard_tbl_notiziastato;
	public $idcard_tbl_notiziacategoria;
	public $notizia_orainizio;
	public $notizia_datafine;
	public $notizia_orafine;
	public $notizia_note;
	public $notizia_profilo;
	public $notizia_datacreazione;
	public $notizia_oracreazione;
	public $iddara_operatore;
	public $notizia_approfondimento;
	public $notizia_collegata;

	public function __construct($idcard_notizia=null) 
	{
		global $db;
		if(!empty($idcard_notizia))
		{
			$this->idcard_notizia=$idcard_notizia;
			
			$sSQL="SELECT * FROM card_notizia WHERE idcard_notizia='$idcard_notizia'";
			$aNEWS=$db->select($sSQL);

			foreach ($aNEWS as $key => $rows) 
			{
				$this->idcard_notizia=$rows["idcard_notizia"];
				$this->iddara_cliente=$rows["iddara_cliente"];
				$this->iddara_operatore=$rows["iddara_operatore"];
				$this->notizia_titolo=$rows["notizia_titolo"];
				$this->notizia_testo=$rows["notizia_testo"];
				$this->notizia_datainizio=$rows["notizia_datainizio"];
				$this->notizia_orainizio=$rows["notizia_orainizio"];			
				$this->idcard_tbl_notiziastato=$rows["idcard_tbl_notiziastato"];							
				$this->idcard_tbl_notiziacategoria=$rows["idcard_tbl_notiziacategoria"];							
				$this->notizia_datafine=$rows["notizia_datafine"];
				$this->notizia_orafine=$rows["notizia_orafine"];
				$this->notizia_note=$rows["notizia_note"];
				$this->notizia_profilo=$rows["notizia_profilo"];
				$this->notizia_datacreazione=$rows["notizia_datacreazione"];
				$this->notizia_oracreazione=$rows["notizia_oracreazione"];			
				$this->notizia_approfondimento=$rows["notizia_approfondimento"];
				$this->notizia_collegata=$rows["notizia_collegata"];			
				

			}	
		}
	}

	function getNEWS($condizione=NULL)
	{
		global $db;
		$sSQL="select * from card_notizia ";
		$sOrder=" order by idcard_notizia desc ";
		if (!empty($condizione))
			$sWhere=" where ".$condizione;

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aNEWS=$db->select($sSQL);
		return $aNEWS;
	}

			 
	function insertNEWS($iddara_cliente,$notizia_titolo,$notizia_testo,$idcard_tbl_notiziastato=0,$notizia_datafine="",$notizia_orafine="",$idcard_tbl_notiziacategoria=1,$notizia_note="",$notizia_profilo="",$notizia_approfondimento="",$notizia_datainizio="",$notizia_orainizio="",$notizia_collegata="")
	{
		global $db;
		$notizia_datacreazione=date("Y-m-d");
		$notizia_oracreazione=date("H:i");
		$notizia_datainizio=datausa($notizia_datainizio);
		$notizia_datafine=datausa($notizia_datafine);
		$sSQL="insert into card_notizia (iddara_cliente,iddara_operatore,notizia_datacreazione,notizia_oracreazione,notizia_titolo,notizia_testo,idcard_tbl_notiziastato,notizia_datafine,notizia_orafine,idcard_tbl_notiziacategoria,notizia_note,notizia_profilo,notizia_approfondimento,notizia_collegata) values('".$iddara_cliente."','".$user."','".$notizia_datacreazione."','".$notizia_oracreazione."','".$notizia_titolo."','".$notizia_testo."','".$idcard_tbl_notiziastato."','".$notizia_datafine."','".$notizia_orafine."','".$idcard_tbl_notiziacategoria."','".$notizia_note."','".$notizia_profilo."','".$notizia_approfondimento."','".$notizia_collegata."')";

		$db->query($sSQL);
		return $db->insert_id();

	}


	function updateNEWS($iddara_cliente,$notizia_titolo,$notizia_testo,$idcard_tbl_notiziastato=0,$notizia_datafine="",$notizia_orafine="",$idcard_tbl_notiziacategoria=1,$notizia_note="",$notizia_profilo="",$notizia_approfondimento="",$notizia_datainizio="",$notizia_orainizio="",$notizia_collegata="")
	{
		global $db;

		$notizia_datainizio=datausa($notizia_datainizio);
		$notizia_datafine=datausa($notizia_datafine);
		$sSQL="update card_notizia set notizia_titolo='".$notizia_titolo."',notizia_testo='".$notizia_testo."',idcard_tbl_notiziastato='".$idcard_tbl_notiziastato."',notizia_datafine='".$notizia_datafine."',notizia_orafine='".$notizia_orafine."',idcard_tbl_notiziacategoria='".$idcard_tbl_notiziacategoria." ',notizia_note='".$notizia_note."',notizia_profilo='".$notizia_profilo."',notizia_approfondimento='".$notizia_approfondimento."',notizia_datainizio='".$notizia_datainizio."',notizia_orainizio='".$notizia_orainizio."',notizia_collegata='".$notizia_collegata."' where idcard_notizia='".$this->idcard_notizia."'";

		$db->query($sSQL);
		

	}

	function deleteNEWS($idcard_notizia)
	{
		global $db;
		$sSQL="delete from card_notizia where idcard_notizia='".$idcard_notizia."'";
		$db->query($sSQL);
	}	


}	

?>
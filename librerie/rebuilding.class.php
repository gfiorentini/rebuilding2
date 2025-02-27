<?php

class rebuildingSPORTELLO {

	public $idrebuilding_sportello;
	public $sportello_titolo;
	public $sportello_testo;
	public $sportello_esperto;

	public function __construct($idrebuilding_sportello=null) 
	{
		global $db;
		if(!empty($idrebuilding_sportello))
		{
			$this->idrebuilding_sportello=$idrebuilding_sportello;
			
			$sSQL="SELECT * FROM rebuilding_sportello WHERE idrebuilding_sportello='$idrebuilding_sportello'";
			$aNEWS=$db->select($sSQL);

			foreach ($aNEWS as $key => $rows) 
			{
				$this->idrebuilding_sportello=$rows["idrebuilding_sportello"];
				$this->sportello_titolo=$rows["sportello_titolo"];
				$this->sportello_testo=$rows["sportello_testo"];
				$this->sportello_esperto=$rows["sportello_esperto"];											

			}	
		}
	}

	public function getDOMANDE()
	{
		global $db;
		$sSQL="select * from rebuilding_sportello_domanda where idrebuilding_sportello='".$this->idrebuilding_sportello."'";
	    $sOrder=" order by idrebuilding_sportello ";
		$sWhere='';
	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aDOMANDE=$db->select($sSQL);
	    return $aDOMANDE;    		
	}


	public function getRISPOSTE($idrebuilding_sportello_domanda=0)
	{
		global $db;
		if(!empty($idrebuilding_sportello_domanda))
			$sWhere=" and idrebuilding_sportello_domanda='".$idrebuilding_sportello_domanda."'";

		$sSQL="select * from rebuilding_sportello_risposta where idrebuilding_sportello='".$this->idrebuilding_sportello."'".$sWhere;
	    $sOrder=" order by idrebuilding_sportello ";

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aRISPOSTE=$db->select($sSQL);
	    return $aRISPOSTE;    		
	}

	public function insertDOMANDA($domanda_testo)
	{
		global $db;
		$user=verificaUSER();

		$data=date("Y-m-d");
		$orario=date("H:i");
		$sSQL="insert into rebuilding_sportello_domanda (idrebuilding_sportello,domanda_data,domanda_orario,domanda_operatore,domanda_testo) values('".$this->idrebuilding_sportello."','".$data."','".$orario."','".$user."','".$domanda_testo."')";		
		$db->query($sSQL);

	}

	public function insertRISPOSTA($idrebuilding_sportello_domanda,$risposta_testo)
	{
		global $db;
		$user=verificaUSER();

		$data=date("Y-m-d");
		$orario=date("H:i");
		$sSQL="insert into rebuilding_sportello_risposta (idrebuilding_sportello,idrebuilding_sportello_domanda,risposta_data,risposta_orario,risposta_operatore,risposta_testo) values('".$this->idrebuilding_sportello."','".$idrebuilding_sportello_domanda."','".$data."','".$orario."','".$user."','".$risposta_testo."')";		
		$db->query($sSQL);

	}	

	public function getFORMAZIONI()
	{
		global $db;
		$sSQL="select * from rebuilding_sportello_corso where idrebuilding_sportello='".$this->idrebuilding_sportello."'";
	    $sOrder=" order by ordine, idrebuilding_sportello ";
		$sWhere='';
	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aCORSI=$db->select($sSQL);
	    return $aCORSI;    		
	}	

}	

class rebuildingFLUSSOFINANZIARIO {

	public $idrebuilding_flussofinanziario;
	public $flussofinanziario_anno;
	public $flussofinanziario_ente;
	public $flussofinanziario_tipofondo;
	public $flussofinanziario_areaintervento;
	public $flussofinanziario_codicesirps;
	public $flussofinanziario_leggeriferimento;
	public $flussofinanziario_titolo;
	public $flussofinanziario_testo;
	public $flussofinanziario_rup;
	public $flussofinanziario_contatti_rup;
	public $flussofinanziario_documento1;
	public $flussofinanziario_documento2;
	public $flussofinanziario_documento3;
	public $flussofinanziario_documento4;
	public $flussofinanziario_documento5;
	public $flussofinanziario_documento6;
	public $flussofinanziario_tipodocumento1;
	public $flussofinanziario_tipodocumento2;
	public $flussofinanziario_tipodocumento3;
	public $flussofinanziario_tipodocumento4;
	public $flussofinanziario_tipodocumento5;
	public $flussofinanziario_tipodocumento6;
	public $flussofinanziario_operatore;
	public $flussofinanziario_datainserimento;
	public $flussofinanziario_ultimamodifica;
	public $flussofinanziario_stato;

	public function __construct($idrebuilding_flussofinanziario=null) 
	{
		global $db;
		if(!empty($idrebuilding_flussofinanziario))
		{
			$this->idrebuilding_flussofinanziario=$idrebuilding_flussofinanziario;
			
			$sSQL="SELECT * FROM rebuilding_flussofinanziario WHERE idrebuilding_flussofinanziario='$idrebuilding_flussofinanziario'";
			$aNEWS=$db->select($sSQL);

			foreach ($aNEWS as $key => $rows) 
			{
				$this->idrebuilding_flussofinanziario=$rows["idrebuilding_flussofinanziario"];
				$this->flussofinanziario_anno=stripslashes($rows["flussofinanziario_anno"]);
				$this->flussofinanziario_ente=stripslashes($rows["flussofinanziario_ente"]);
				$this->flussofinanziario_tipofondo=stripslashes($rows["flussofinanziario_tipofondo"]);											

				$this->flussofinanziario_areaintervento=stripslashes($rows["flussofinanziario_areaintervento"]);
				$this->flussofinanziario_codicesirps=stripslashes($rows["flussofinanziario_codicesirps"]);
				$this->flussofinanziario_leggeriferimento=stripslashes($rows["flussofinanziario_leggeriferimento"]);
				$this->flussofinanziario_titolo=stripslashes($rows["flussofinanziario_titolo"]);
				$this->flussofinanziario_testo=stripslashes($rows["flussofinanziario_testo"]);
				$this->flussofinanziario_contatti_rup=stripslashes($rows["flussofinanziario_contatti_rup"]);
				$this->flussofinanziario_rup=stripslashes($rows["flussofinanziario_rup"]);
				$this->flussofinanziario_operatore=stripslashes($rows["flussofinanziario_operatore"]);
				$this->flussofinanziario_datainserimento=stripslashes($rows["flussofinanziario_datainserimento"]);
				$this->flussofinanziario_ultimamodifica=stripslashes($rows["flussofinanziario_ultimamodifica"]);
				$this->flussofinanziario_stato=stripslashes($rows["flussofinanziario_stato"]);
				
				$this->flussofinanziario_documento1=$rows["flussofinanziario_documento1"];
				$this->flussofinanziario_documento2=$rows["flussofinanziario_documento2"];
				$this->flussofinanziario_documento3=$rows["flussofinanziario_documento3"];
				$this->flussofinanziario_documento4=$rows["flussofinanziario_documento4"];
				$this->flussofinanziario_documento5=$rows["flussofinanziario_documento5"];
				$this->flussofinanziario_documento6=$rows["flussofinanziario_documento6"];
				$this->flussofinanziario_tipodocumento1=$rows["flussofinanziario_tipodocumento1"];
				$this->flussofinanziario_tipodocumento2=$rows["flussofinanziario_tipodocumento2"];
				$this->flussofinanziario_tipodocumento3=$rows["flussofinanziario_tipodocumento3"];
				$this->flussofinanziario_tipodocumento4=$rows["flussofinanziario_tipodocumento4"];
				$this->flussofinanziario_tipodocumento5=$rows["flussofinanziario_tipodocumento5"];
				$this->flussofinanziario_tipodocumento6=$rows["flussofinanziario_tipodocumento6"];

			}	
		}
	}

	public function getFLUSSO($sWhere=null)
	{
		global $db;
		$sSQL="select * from rebuilding_flussofinanziario where idrebuilding_flussofinanziario='".$this->idrebuilding_flussofinanziario."'";
	    $sOrder=" order by idrebuilding_flussofinanziario ";

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aFLUSSO=$db->select($sSQL);
	    return $aFLUSSO;    		
	}

	public function getFLUSSI($sWhere=null)
	{
		global $db;

		$sSQL="select * from rebuilding_flussofinanziario ";		
	    $sOrder=" order by idrebuilding_flussofinanziario ";

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aFLUSSI=$db->select($sSQL);
	    return $aFLUSSI;    		
	}

	public function getFLUSSIENTE($sWhere=null)
	{
		global $db;

		$sSQL="select * from rebuilding_flussofinanziario inner join rebuilding_flussofinanziario_ente on rebuilding_flussofinanziario.idrebuilding_flussofinanziario=rebuilding_flussofinanziario_ente.idrebuilding_flussofinanziario ";		
	    $sOrder=" order by rebuilding_flussofinanziario.idrebuilding_flussofinanziario ";

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aFLUSSI=$db->select($sSQL);
	    return $aFLUSSI;    		
	}	

	public function insertFLUSSO($flussofinanziario_anno,$flussofinanziario_ente,$flussofinanziario_tipofondo,$flussofinanziario_areaintervento,$flussofinanziario_leggeriferimento,$flussofinanziario_titolo,$flussofinanziario_testo,$flussofinanziario_documento1,$flussofinanziario_documento2,$flussofinanziario_documento3,$flussofinanziario_documento4)
	{
		global $db;
	
	    $pflussofinanziario_datainserimento=date("Y-m-d");
	    $pflussofinanziario_orainserimento=date("H:i");
	    $sSQL="insert into rebuilding_flussofinanziario  (flussofinanziario_ente,flussofinanziario_tipofondo,flussofinanziario_anno,flussofinanziario_datainserimento,flussofinanziario_orainserimento,flussofinanziario_titolo,flussofinanziario_testo,flussofinanziario_leggeriferimento,flussofinanziario_areaintervento) values('$flussofinanziario_ente','$flussofinanziario_tipofondo','$flussofinanziario_anno','$pflussofinanziario_datainserimento','$pflussofinanziario_orainserimento','$flussofinanziario_titolo','$flussofinanziario_testo','$flussofinanziario_leggeriferimento','$flussofinanziario_areaintervento')";		
		$db->query($sSQL);

	}

	public function getALLEGATI($sWhere=null)
	{
		global $db;
		$sSQL="select * from rebuilding_flussofinanziario_documento where idrebuilding_flussofinanziario='".$this->idrebuilding_flussofinanziario."'";
	    $sOrder=" order by idrebuilding_flussofinanziario ";

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aDOCUMENTI=$db->select($sSQL);
	    return $aDOCUMENTI;    		
	}

	public function getRISORSE($sWhere=null)
	{
		global $db;
		$sSQL="select * from rebuilding_flussofinanziario_risorsa where idrebuilding_flussofinanziario='".$this->idrebuilding_flussofinanziario."'";
	    $sOrder=" order by idrebuilding_flussofinanziario_risorsa ";

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aRISORSE=$db->select($sSQL);
	    return $aRISORSE;    		
	}

	public function getINTERVENTI($sWhere=null)
	{
		global $db;
		$sSQL="select * from rebuilding_flussofinanziario_intervento where idrebuilding_flussofinanziario='".$this->idrebuilding_flussofinanziario."' and intervento_flagelimina=0 ";
	    $sOrder=" order by idrebuilding_flussofinanziario_intervento ";

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aINTERVENTI=$db->select($sSQL);
	    return $aINTERVENTI;    		
	}

	public function getLIQUIDAZIONI($sWhere=null)
	{
		global $db;
		$sSQL="select * from rebuilding_flussofinanziario_liquidazione where idrebuilding_flussofinanziario='".$this->idrebuilding_flussofinanziario."' AND liquidazione_flagelimina=0";
	    $sOrder=" order by idrebuilding_flussofinanziario_intervento,idrebuilding_tipologiaspesa ";

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aINTERVENTI=$db->select($sSQL);
	    return $aINTERVENTI;    		
	}

	public function getTOTALERISORSE($sWhere=null)
	{
		global $db;
		$sSQL="select sum(risorsa_assegnata) as risorse from rebuilding_flussofinanziario_risorsa where idrebuilding_flussofinanziario='".$this->idrebuilding_flussofinanziario."'";	    

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aRISORSE=$db->select($sSQL);
	    
	    return $aRISORSE[0]['risorse'];    		
	}

	public function getTOTALESPESA($sWhere=null)
	{
		global $db;
		$sSQL="select sum(liquidazione_importo) as spesa from rebuilding_flussofinanziario_liquidazione where idrebuilding_flussofinanziario='".$this->idrebuilding_flussofinanziario."'";	    

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aSPESA=$db->select($sSQL);
	    
	    return $aSPESA[0]['spesa'];    		
	}

	public function getTOTALEBENEFICIARI($sWhere=null)
	{
		global $db;
		$sSQL="select sum(liquidazione_beneficiari) as beneficiari from rebuilding_flussofinanziario_liquidazione where idrebuilding_flussofinanziario='".$this->idrebuilding_flussofinanziario."'";	    

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aBENEFICIARI=$db->select($sSQL);
	    
	    return $aBENEFICIARI[0]['beneficiari'];    		
	}


}	

class rebuildingNOTIFICA {

	public $idrebuilding_notifica;
	public $notifica_oggetto;
	public $notifica_testo;
	public $notifica_destinatario;
	public $notifica_mittente;
	public $notifica_stato;
	public $notifica_data;	
	public $notifica_datainserimento;
	public $notifica_ultimamodifica;
	public $notifica_operatore;
	public $idrebuilding_flussofinanziario;
	public $notifica_destinatario_regione;

	public function __construct($idrebuilding_notifica=null) 
	{
		global $db;
		if(!empty($idrebuilding_notifica))
		{
			$this->idrebuilding_notifica=$idrebuilding_notifica;
			
			$sSQL="SELECT * FROM rebuilding_notifica WHERE idrebuilding_notifica='$idrebuilding_notifica'";
			$aNEWS=$db->select($sSQL);

			foreach ($aNEWS as $key => $rows) 
			{
				$this->idrebuilding_notifica=$rows["idrebuilding_notifica"];
				$this->notifica_oggetto=stripslashes($rows["notifica_oggetto"]);
				$this->notifica_testo=stripslashes($rows["notifica_testo"]);
				$this->notifica_destinatario=stripslashes($rows["notifica_destinatario"]);
				$this->idrebuilding_flussofinanziario=$rows["idrebuilding_flussofinanziario"];												
				$this->notifica_mittente=stripslashes($rows["notifica_mittente"]);											
				$this->notifica_data=$rows["notifica_data"];											
				$this->notifica_stato=$rows["notifica_stato"];											
				$this->notifica_datainserimento=$rows["notifica_datainserimento"];											
				$this->notifica_ultimamodifica=$rows["notifica_ultimamodifica"];											
				$this->notifica_operatore=$rows["notifica_operatore"];	
				$this->notifica_destinatario_regione=$rows["notifica_destinatario_regione"];														

			}	
		}
	}

	public function getNOTIFICHE($sWhere=null)
	{
		global $db;

		$sSQL="select * from rebuilding_notifica ";		
	    $sOrder=" order by idrebuilding_notifica ";

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aNOTIFICHE=$db->select($sSQL);
	    return $aNOTIFICHE;    		
	}

	public function getNOTIFICHEENTE($sWhere=null)
	{
		global $db;

		$sSQL="select rebuilding_notifica.* from rebuilding_notifica inner join rebuilding_notifica_ente on rebuilding_notifica.idrebuilding_notifica=rebuilding_notifica_ente.idrebuilding_notifica ";		
	    $sOrder=" order by idrebuilding_notifica ";

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aNOTIFICHE=$db->select($sSQL);
	    return $aNOTIFICHE;    		
	}

	public function getEMAIL()
	{
		global $db;
		$sSQL="select * from rebuilding_notifica_email where idrebuilding_notifica='".$this->idrebuilding_notifica."'";
	    $sOrder=" order by idrebuilding_notifica_email ";
		$sWhere='';
	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aEMAIL=$db->select($sSQL);
	    return $aEMAIL;    		
	}

	public function getALLEGATI($sWhere=null)
	{
		global $db;

		$sSQL="select * from rebuilding_notifica_documento where idrebuilding_notifica='".$this->idrebuilding_notifica."' ";		
	    $sOrder=" order by idrebuilding_notifica_documento ";

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aDOCUMENTI=$db->select($sSQL);
	    return $aDOCUMENTI;    		
	}

	public function getALLEGATINAME($sWhere=null)
	{
		global $db;

		$sSQL="select notifica_documentonome from rebuilding_notifica_documento where idrebuilding_notifica='".$this->idrebuilding_notifica."' ";		
	    $sOrder=" order by idrebuilding_notifica_documento ";

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aDOCUMENTI=$db->select($sSQL);
		
		$str='';
		foreach ($aDOCUMENTI as $documento => $value) 
		{
			
			$str.='../documenti/rebuilding/toolkit/'.$value["notifica_documentonome"].'|';
		}

		$str=rtrim($str,'|');

	    return $str;    		
	}

}	

class rebuildingSCADENZARIO {

	public $idrebuilding_scadenzario;
	public $scadenza_testo;
	public $scadenza_destinatario;
	public $scadenza_stato;
	public $scadenza_data;	
	public $scadenza_datainserimento;
	public $scadenza_ultimamodifica;
	public $scadenza_operatore;
	public $idrebuilding_flussofinanziario;

	public function __construct($idrebuilding_scadenzario=null) 
	{
		global $db;
		if(!empty($idrebuilding_scadenzario))
		{
			$this->idrebuilding_scadenzario=$idrebuilding_scadenzario;
			
			$sSQL="SELECT * FROM rebuilding_scadenzario WHERE idrebuilding_scadenzario='$idrebuilding_scadenzario'";
			$aNEWS=$db->select($sSQL);

			foreach ($aNEWS as $key => $rows) 
			{
				$this->idrebuilding_scadenzario=$rows["idrebuilding_scadenzario"];
				$this->scadenza_testo=stripslashes($rows["scadenza_testo"]);
				$this->scadenza_destinatario=stripslashes($rows["scadenza_destinatario"]);
				$this->idrebuilding_flussofinanziario=$rows["idrebuilding_flussofinanziario"];												
				$this->scadenza_data=$rows["scadenza_data"];											
				$this->scadenza_stato=$rows["scadenza_stato"];											
				$this->scadenza_datainserimento=$rows["scadenza_datainserimento"];											
				$this->scadenza_ultimamodifica=$rows["scadenza_ultimamodifica"];											
				$this->scadenza_operatore=$rows["scadenza_operatore"];											

			}	
		}
	}

	public function getSCADENZE($sWhere=null)
	{
		global $db;

		$sSQL="select * from rebuilding_scadenzario ";		
	    $sOrder=" order by scadenza_data ";

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aSCADENZE=$db->select($sSQL);
	    return $aSCADENZE;    		
	}

	public function getSCADENZEENTE($sWhere=null)
	{
		global $db;

		$sSQL="select rebuilding_scadenzario.* from rebuilding_scadenzario inner join rebuilding_scadenzario_ente on rebuilding_scadenzario.idrebuilding_scadenzario=rebuilding_scadenzario_ente.idrebuilding_scadenzario ";		
	    $sOrder=" order by rebuilding_scadenzario.scadenza_data ";

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aSCADENZE=$db->select($sSQL);
	    return $aSCADENZE;    		
	}

}

class rebuildingRENDICONTAZIONE {

	public $idrebuilding_rendicontazione;
	public $idrebuilding_flussofinanziario;
	public $rendicontazione_ente;
	public $rendicontazione_titolo;
	public $rendicontazione_inizio;	
	public $rendicontazione_fine;	
	public $rendicontazione_datainserimento;
	public $rendicontazione_ultimamodifica;
	public $rendicontazione_operatore;
	public $rendicontazione_assegnata;

	public function __construct($idrebuilding_rendicontazione=null) 
	{
		global $db;
		if(!empty($idrebuilding_rendicontazione))
		{
			$this->idrebuilding_rendicontazione=$idrebuilding_rendicontazione;
			
			$sSQL="SELECT * FROM rebuilding_rendicontazione WHERE idrebuilding_rendicontazione='$idrebuilding_rendicontazione'";
			$aRENDICONTAZIONE=$db->select($sSQL);

			foreach ($aRENDICONTAZIONE as $key => $rows) 
			{
				
				$this->idrebuilding_rendicontazione=$rows["idrebuilding_rendicontazione"];
				$this->idrebuilding_flussofinanziario=$rows["idrebuilding_flussofinanziario"];
				$this->rendicontazione_ente=stripslashes($rows["rendicontazione_ente"]);
				$this->rendicontazione_titolo=stripslashes($rows["rendicontazione_titolo"]);												
				$this->rendicontazione_inizio=$rows["rendicontazione_inizio"];											
				$this->rendicontazione_fine=$rows["rendicontazione_fine"];		
				$this->rendicontazione_assegnata=$rows["rendicontazione_assegnata"];		
													
				$this->rendicontazione_datainserimento=$rows["rendicontazione_datainserimento"];											
				$this->rendicontazione_ultimamodifica=$rows["rendicontazione_ultimamodifica"];											
				$this->rendicontazione_operatore=$rows["rendicontazione_operatore"];											

			}	
		}
	}

	public function getRENDICONTAZIONI($sWhere=null)
	{
		global $db;

		$sSQL="select * from rebuilding_rendicontazione ";		
	    $sOrder=" order by idrebuilding_rendicontazione ";

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aRENDICONTAZIONI=$db->select($sSQL);
	    return $aRENDICONTAZIONI;    		
	}

	public function getINTERVENTI($sWhere=null)
	{
		global $db;

		$sSQL="select rebuilding_rendicontazione_intervento.* from rebuilding_rendicontazione_intervento where idrebuilding_rendicontazione='".$this->idrebuilding_rendicontazione."' ";		
	    $sOrder=" order by rebuilding_rendicontazione_intervento.idrebuilding_rendicontazione_intervento ";

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aSCADENZE=$db->select($sSQL);
	    return $aSCADENZE;    		
	}

	public function getSOMMASPESA($sWhere=null)
	{
		global $db;

		$sSQL="select sum(intervento_spesa) as spesaINTERVENTI from rebuilding_rendicontazione_intervento  where idrebuilding_rendicontazione='".$this->idrebuilding_rendicontazione."' ";		
	    $sOrder="  ";

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $sommaspesa=$db->getVALUE($sSQL,'spesaINTERVENTI');
	    return $sommaspesa;    		
	}	

	public function getUTENTI($sWhere=null)
	{
		global $db;

		$sSQL="select sum(intervento_utenti) as utentiINTERVENTI from rebuilding_rendicontazione_intervento  where idrebuilding_rendicontazione='".$this->idrebuilding_rendicontazione."' ";		
	    $sOrder="  ";

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $utenti=$db->getVALUE($sSQL,'utentiINTERVENTI');
	    return $utenti;    		
	}		

}

class rebuildingLIQUIDAZIONE {

	public $idrebuilding_flussofinanziario_liquidazione;
	public $idrebuilding_flussofinanziario;
	public $liquidazione_ente;
	public $idrebuilding_flussofinanziario_intervento;
	public $idrebuilding_tipologiaspesa;	
	public $liquidazione_importo;	
	public $liquidazione_beneficiari;	
	public $liquidazione_attonumero;	
	public $liquidazione_attodata;	
	public $liquidazione_quietanzanumero;	
	public $liquidazione_quietanzadata;	
	public $liquidazione_datainserimento;
	public $liquidazione_ultimamodifica;
	public $liquidazione_operatore;
	

	public function __construct($idrebuilding_flussofinanziario_liquidazione=null) 
	{
		global $db;
		if(!empty($idrebuilding_flussofinanziario_liquidazione))
		{
			$this->idrebuilding_flussofinanziario_liquidazione=$idrebuilding_rendicontazione;
			
			$sSQL="SELECT * FROM rebuilding_flussofinanziario_liquidazione WHERE idrebuilding_flussofinanziario_liquidazione='$idrebuilding_flussofinanziario_liquidazione'";
			$aLIQUIDAZIONE=$db->select($sSQL);

			foreach ($aLIQUIDAZIONE as $key => $rows) 
			{
				
				$this->idrebuilding_flussofinanziario_liquidazione=$rows["idrebuilding_flussofinanziario_liquidazione"];
				$this->idrebuilding_flussofinanziario=$rows["idrebuilding_flussofinanziario"];
				$this->liquidazione_ente=$rows["liquidazione_ente"];
				$this->idrebuilding_flussofinanziario_intervento=$rows["idrebuilding_flussofinanziario_intervento"];												
				$this->idrebuilding_tipologiaspesa=stripslashes($rows["idrebuilding_tipologiaspesa"]);											
				$this->liquidazione_importo=$rows["liquidazione_importo"];		
				$this->liquidazione_beneficiari=$rows["liquidazione_beneficiari"];		
				$this->liquidazione_attonumero=$rows["liquidazione_attonumero"];		
				$this->liquidazione_attodata=$rows["liquidazione_attodata"];		
				$this->liquidazione_quietanzanumero=$rows["liquidazione_quietanzanumero"];		
				$this->liquidazione_quietanzadata=$rows["liquidazione_quietanzadata"];		
													
				$this->liquidazione_datainserimento=$rows["liquidazione_datainserimento"];											
				$this->liquidazione_ultimamodifica=$rows["liquidazione_ultimamodifica"];											
				$this->liquidazione_operatore=$rows["liquidazione_operatore"];											

			}	
		}
	}

	public function getLIQUIDAZIONI($sWhere=null)
	{
		global $db;

		if(!empty($sWhere))
			$sWhere.=" and liquidazione_flagelimina=0 ";
		else
			$sWhere=" where liquidazione_flagelimina=0 ";

		$sSQL="select * from rebuilding_flussofinanziario_liquidazione ";		
	    $sOrder=" order by idrebuilding_flussofinanziario_liquidazione ";

	     $sSQL=$sSQL.$sWhere.$sOrder;
	    $aLIQUIDAZIONI=$db->select($sSQL);
	    return $aLIQUIDAZIONI;    		
	}
	
	public function getALLEGATI($sWhere=null)
	{
		global $db;
		$sSQL="select * from rebuilding_flussofinanziario_liquidazione_documento where idrebuilding_flussofinanziario_liquidazione='".$this->idrebuilding_flussofinanziario_liquidazione."'";
	    $sOrder=" order by idrebuilding_flussofinanziario_liquidazione_documento ";

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aDOCUMENTI=$db->select($sSQL);
	    return $aDOCUMENTI;    		
	}	

}


?>
<?php

class DARAAvviso {

	public $iddara_avviso;
	public $iddara_cliente;
	public $avviso_titolo;
	public $avviso_descrizione;
	public $avviso_datainizio;
	public $avviso_orainizio;
	public $avviso_datascadenza;
	public $avviso_orascadenza;	
	
	public $avviso_flagpubblica;
	public $avviso_richiedente_datainizio;
	public $avviso_richiedente_datafine;
	public $avviso_modellodomanda;
	public $avviso_modelloricevuta;
	public $avviso_modelloattestato;

	public function __construct($iddara_avviso=null) 
	{
		global $db;
		if(!empty($iddara_avviso))
		{
			$this->iddara_avviso=$iddara_avviso;
			
			$sSQL="SELECT * FROM dara_avviso WHERE iddara_avviso='$iddara_avviso'";
			$aAVVISO=$db->select($sSQL);

			foreach ($aAVVISO as $key => $rows) 
			{
				$this->iddara_cliente=$rows["iddara_cliente"];
				$this->avviso_titolo=$rows["avviso_titolo"];
				$this->avviso_descrizione=$rows["avviso_descrizione"];
				$this->avviso_flagpubblica=$rows["avviso_flagpubblica"];
				$this->avviso_datainizio=$rows["avviso_datainizio"];			
				$this->avviso_orainizio=$rows["avviso_orainizio"];
				$this->avviso_datascadenza=$rows["avviso_datascadenza"];
				$this->avviso_orascadenza=$rows["avviso_orascadenza"];					
				
				$this->avviso_richiedente_datainizio=$rows["avviso_richiedente_datainizio"];
				$this->avviso_richiedente_datafine=$rows["avviso_richiedente_datafine"];
				$this->avviso_modellodomanda=$rows["avviso_modellodomanda"];
				$this->avviso_modelloricevuta=$rows["avviso_modelloricevuta"];
				$this->avviso_modelloattestato=$rows["avviso_modelloattestato"];
			}	
		}
		
	}

	function isSCADUTO() 
	{
		$oggi=date("Y-m-d");
		$adesso=date("H:i");

		if ($adesso<=$this->avviso_datascadenza && $adesso<=$this->avviso_orascadenza)
			return false;
		else
			return true;
	}

	function getAVVISI($condizione=NULL)
	{
		global $db;
		$sSQL="select * from dara_avviso ";
		$sOrder=" order by iddara_avviso ";
		$sWhere="";
		if (!empty($condizione))
			$sWhere=" where ".$condizione;

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aAVVISI=$db->select($sSQL);
		return $aAVVISI;

	}

	function getDOMANDE($condizione=NULL)
	{
		global $db;
		$sSQL="select * from dara_domanda ";
		$sOrder=" order by iddara_domanda ";
		if (!empty($condizione))
			$sWhere=" where ".$condizione;

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aDOMANDE=$db->select($sSQL);
		return $aDOMANDE;
	}

	function getAUTOCERTIFICAZIONI($front=false,$tipo=null)
	{
		global $db;
		$sSQL="select * from dara_avviso_autocertificazione ";
		$sWhere=" where iddara_avviso='".$this->iddara_avviso."'";
		if($front)
			$sWhere.=" and autocertificazione_flagfront='1' ";

		if(!empty($tipo))
			$sWhere.=" and iddara_tbl_tipocampo='$tipo' ";

		$sOrder=" order by iddara_avviso_autocertificazione ";

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aAUTOCERTIFICAZIONI=$db->select($sSQL);
		return $aAUTOCERTIFICAZIONI;		
	}

	function getDIPENDENZE($front=false)
	{
		global $db;
		$sSQL="select * from dara_avviso_dipendenza ";
		$sWhere=" where iddara_avviso='".$this->iddara_avviso."'";
		$sOrder=" order by iddara_avviso_dipendenza ";

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aDIPENDENZE=$db->select($sSQL);
		return $aDIPENDENZE;		
	}

	function getPARAMETRICAPO($iddara_avviso_autocertificazione)
	{
		global $db;
		$sSQL="select iddara_avviso_autocertificazione_capo,iddara_avviso_autocertificazione_capo_risposta from dara_avviso_dipendenza ";
		$sWhere=" where iddara_avviso_autocertificazione='".$iddara_avviso_autocertificazione."'";
		$sOrder=" order by iddara_avviso_dipendenza ";

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aPARAMETRICAPO=$db->select($sSQL);
		return $aPARAMETRICAPO;		
	}

	function getDICHIARAZIONI()
	{
		global $db;
		$sSQL="select * from dara_avviso_dichiarazione ";
		$sWhere=" where iddara_avviso='".$this->iddara_avviso."'";
		$sOrder=" order by iddara_avviso_dichiarazione ";

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aDICHIARAZIONI=$db->select($sSQL);
		return $aDICHIARAZIONI;		
	}	

	function getDOCUMENTI($front=false)
	{
		global $db;
		$sSQL="select * from dara_avviso_documento ";
		$sWhere=" where iddara_avviso='".$this->iddara_avviso."'";
		if($front)
			$sWhere.=" and documento_flagfront='1'";
		$sOrder=" order by iddara_avviso_documento ";

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aDOCUMENTI=$db->select($sSQL);
		return $aDOCUMENTI;		
	}	

	function insertDOCUMENTO($documento_descrizione,$documento_flagfront=0,$documento_flagobbligatorio=0,$iddara_avviso_autocertificazione=0,$documento_modulo="")
	{
		global $db;

		if (!empty($documento_modulo))
		{

		}

		$sSQL="insert into dara_avviso_documento (iddara_avviso,documento_descrizione,documento_flagfront,documento_flagobbligatorio,iddara_avviso_autocertificazione,documento_modulo) values('".$this->iddara_avviso."','".$documento_descrizione."',".$documento_flagfront.",".$documento_flagobbligatorio.",".$iddara_avviso_autocertificazione.",'".$documento_modulo."')";

		$db->query($sSQL);
		return $db->insert_id();

	}

	function updateDOCUMENTO($iddara_avviso_documento,$documento_descrizione,$documento_flagfront=0,$documento_flagobbligatorio=0,$iddara_avviso_autocertificazione=0,$documento_modulo="")
	{
		global $db;

		if (!empty($documento_modulo))
		{

		}

		$sSQL="update dara_avviso_documento set documento_descrizione='".$documento_descrizione."',documento_flagfront='".$documento_flagfront."',documento_flagobbligatorio='".$documento_flagobbligatorio."',iddara_avviso_autocertificazione='".$iddara_avviso_autocertificazione."',documento_modulo='".$documento_modulo."' where iddara_avviso_documento='".$iddara_avviso_documento."'";

		$db->query($sSQL);
		

	}


	function deleteDOCUMENTO($iddara_avviso_documento)
	{
		global $db;
		$sSQL="delete from dara_avviso_documento where iddara_avviso_documento='$iddara_avviso_documento'";
		$db->query($sSQL);
	}


	function insertDICHIARAZIONE($dichiarazione_descrizione,$dichiarazione_flagobbligatorio=0,$iddara_avviso_autocertificazione=0,$dichiarazione_modulo="")
	{
		global $db;

		if (!empty($dichiarazione_modulo))
		{

		}

		$sSQL="insert into dara_avviso_dichiarazione (iddara_avviso,dichiarazione_descrizione,dichiarazione_flagobbligatorio,iddara_avviso_autocertificazione,dichiarazione_modulo) values('".$this->iddara_avviso."','".$dichiarazione_descrizione."',".$dichiarazione_flagobbligatorio.",".$iddara_avviso_autocertificazione.",'".$dichiarazione_modulo."')";

		$db->query($sSQL);
		return $db->insert_id();

	}

	function updateDICHIARAZIONE($iddara_avviso_dichiarazione,$dichiarazione_descrizione,$dichiarazione_flagobbligatorio=0,$iddara_avviso_autocertificazione=0,$dichiarazione_modulo="")
	{
		global $db;

		if (!empty($dichiarazione_modulo))
		{

		}

		$sSQL="update dara_avviso_dichiarazione set dichiarazione_descrizione='".$dichiarazione_descrizione."',dichiarazione_flagobbligatorio='".$dichiarazione_flagobbligatorio."',iddara_avviso_autocertificazione='".$iddara_avviso_autocertificazione."',dichiarazione_modulo='".$dichiarazione_modulo."' where iddara_avviso_dichiarazione='".$iddara_avviso_dichiarazione."'";

		$db->query($sSQL);
		

	}


	function deleteDICHIARAZIONE($iddara_avviso_dichiarazione)
	{
		global $db;
		$sSQL="delete from dara_avviso_dichiarazione where iddara_avviso_dichiarazione='$iddara_avviso_dichiarazione'";
		$db->query($sSQL);
	}


	function insertAUTOCERTIFICAZIONE($autocertificazione_descrizione,$autocertificazione_flagfront=0,$autocertificazione_flagobbligatorio=0,$iddara_tbl_tipocampo=0,$autocertificazione_paragrafo="",$iddara_tbl_tipocampo_controlli=0)
	{
		global $db;
		$sSQL="insert into dara_avviso_autocertificazione (iddara_avviso,autocertificazione_descrizione,autocertificazione_flagfront,autocertificazione_flagobbligatorio,iddara_tbl_tipocampo,autocertificazione_paragrafo,iddara_tbl_tipocampo_controlli) values('".$this->iddara_avviso."','".$autocertificazione_descrizione."',".$autocertificazione_flagfront.",".$autocertificazione_flagobbligatorio.",".$iddara_tbl_tipocampo.",'".$autocertificazione_paragrafo."','".$iddara_tbl_tipocampo_controlli."')";
		$db->query($sSQL);
		return $db->insert_id();
	}

	function updateAUTOCERTIFICAZIONE($iddara_avviso_autocertificazione,$autocertificazione_descrizione,$autocertificazione_flagfront=0,$autocertificazione_flagobbligatorio=0,$iddara_tbl_tipocampo=0,$autocertificazione_paragrafo="",$iddara_tbl_tipocampo_controlli=0)
	{
		global $db;
		$sSQL="update dara_avviso_autocertificazione set autocertificazione_descrizione='".$autocertificazione_descrizione."',autocertificazione_flagfront='".$autocertificazione_flagfront."',autocertificazione_flagobbligatorio='".$autocertificazione_flagobbligatorio."',iddara_tbl_tipocampo='".$iddara_tbl_tipocampo."',autocertificazione_paragrafo='".$autocertificazione_paragrafo."', iddara_tbl_tipocampo_controlli=".$iddara_tbl_tipocampo_controlli." where iddara_avviso_autocertificazione='".$iddara_avviso_autocertificazione."'";
		$db->query($sSQL);
	}

	function deleteAUTOCERTIFICAZIONE($iddara_avviso_autocertificazione)
	{
		global $db;
		$sSQL="delete from dara_avviso_autocertificazione where iddara_avviso_autocertificazione='$iddara_avviso_autocertificazione'";
		$db->query($sSQL);
	}

	function getRISPOSTALIST($iddara_avviso_autocertificazione)
	{
		global $db;
		$sSQL="select iddara_avviso_autocertificazione_risposta,risposta_descrizione,risposta_valore from dara_avviso_autocertificazione_risposta where iddara_avviso_autocertificazione='$iddara_avviso_autocertificazione' order by iddara_avviso_autocertificazione_risposta ";
		$aRISPOSTE=$db->select($sSQL);
		
		return $aRISPOSTE;
	}

	function getETICHETTA($iddara_avviso_autocertificazione)
	{
		global $db;
		$sSQL="select autocertificazione_paragrafo from dara_avviso_autocertificazione where iddara_avviso_autocertificazione='$iddara_avviso_autocertificazione'";
		$autocertificazione_paragrafo=$db->getVALUE($sSQL,'autocertificazione_paragrafo');
		
		return $autocertificazione_paragrafo;
	}

	function getTIPOCAMPO($iddara_avviso_autocertificazione,$tipo,$value='',$tipo_controlli=0)
	{
		global $db;
		$sSQL="select autocertificazione_flagobbligatorio from dara_avviso_autocertificazione where iddara_avviso_autocertificazione='$iddara_avviso_autocertificazione'";
		$autocertificazione_flagobbligatorio=$db->getVALUE($sSQL,'autocertificazione_flagobbligatorio');
		$required="";
		if(!empty($autocertificazione_flagobbligatorio))
			$required="required";

		switch($tipo)
		{
			case '1':
				$type="text";
				switch($tipo_controlli)
				{
					case 1:
						$type="text";
						break;

					case 2:
						$type="number";
						break;

					case 3:
						$type="text";
						break;

					case 4:
						$type="date";
						break;

					case 5:
						$type="time";
						break;

					default:
						$type="text";
						break;
				}

				$campo='<input type="'.$type.'" class="form-control form-control-xs class_autocertificazione" style="border-color: silver " id="autocertificazione'.$iddara_avviso_autocertificazione.'" name="autocertificazione'.$iddara_avviso_autocertificazione.'" placeholder="" value="'.$value.'" '.$required.'>';
				break;
			case '3':
				$campo='<select class="form-select form-select-xs class_autocertificazione" style="border-color: silver " aria-label="" id="autocertificazione'.$iddara_avviso_autocertificazione.'" name="autocertificazione'.$iddara_avviso_autocertificazione.'" '.$required.'>';
				$aOPTIONS=$this->getRISPOSTALIST($iddara_avviso_autocertificazione);
				$campo.="<option value='0'></option>";
				if (is_array($aOPTIONS))
				{
					foreach ($aOPTIONS as $key => $aDATI) 
					{
						if ($aDATI["iddara_avviso_autocertificazione_risposta"]==$value)
							$campo.="<option value='".$aDATI["iddara_avviso_autocertificazione_risposta"]."' selected>".$aDATI["risposta_descrizione"]."</option>";
						else	
							$campo.="<option value='".$aDATI["iddara_avviso_autocertificazione_risposta"]."'>".$aDATI["risposta_descrizione"]."</option>";
					}
				}
				$campo.='</select>';
				break;	
			case '5':
				if($value==1)
					$checked="checked";
				$campo='<div class="form-check form-switch">
                  <input class="form-check-input class_autocertificazione" type="checkbox" '.$checked.' id="autocertificazione'.$iddara_avviso_autocertificazione.'" name="autocertificazione'.$iddara_avviso_autocertificazione.'" '.$required.'>
                </div>';
				break;
			case '7':
				$campo='<textarea class="form-control form-control-xs class_autocertificazione" id="autocertificazione'.$iddara_avviso_autocertificazione.'" name="autocertificazione'.$iddara_avviso_autocertificazione.'" style="height: 120px;" '.$required.'>'.$value.'</textarea>';
				break;
		}

		return $campo;
	}


}

class DARAOperatore {
	
	public $iddara_operatore;
	public $operatore_ente;
	public $operatore_cognome;
	public $operatore_nome;
	public $operatore_codicefiscale;
	public $operatore_cellulare;
	public $operatore_email;
	public $operatore_username;
	public $operatore_password;
	public $operatore_flagabilitato;
	public $operatore_flagamministratore;
	public $operatore_flagrup;
	public $operatore_flagdirigente;
	
	public function __construct($iddara_operatore) {
		global $db;

		if(!empty($iddara_operatore))
		{

			$sSQL="select * from dara_operatore where iddara_operatore='$iddara_operatore' ";
			$aOPERATORE=$db->select($sSQL);

			foreach ($aOPERATORE as $key => $rows) 
			{
				$this->iddara_operatore=$iddara_operatore;					
				$this->operatore_ente=$rows["operatore_ente"];
				$this->operatore_cognome=$rows["operatore_cognome"];
				$this->operatore_nome=$rows["operatore_nome"];
				$this->operatore_codicefiscale=$rows["operatore_codicefiscale"];
				$this->operatore_cellulare=$rows["operatore_cellulare"];
				$this->operatore_email=$rows["operatore_email"];
				$this->operatore_username=$rows["operatore_username"];
				$this->operatore_password=$rows["operatore_password"];
				$this->operatore_flagabilitato=$rows["operatore_flagabilitato"];
				$this->operatore_flagamministratore=$rows["operatore_flagamministratore"];
				$this->operatore_flagrup=$rows["operatore_flagrup"];
				$this->operatore_flagdirigente=$rows["operatore_flagdirigente"];
			}

		}

	}

	function getAUTORIZZAZIONE($iddara_modulo) 
	{
		global $db;
		$sSQL="select iddara_autorizzazione from dara_operatore_autorizzazione where iddara_operatore='".$this->iddara_operatore."' and iddara_modulo='$iddara_modulo'";
		$iddara_autorizzazione=$db->getVALUE($sSQL,'iddara_autorizzazione');
		return $iddara_autorizzazione;

	}

	function getMENU($iddara_autorizzazione)
	{
		global $db;
		$sSQL="select iddara_menu,iddara_tbl_autorizzazione,menu_titolo,menu_codice from dara_autorizzazione_menu inner join dara_menu on dara_autorizzazione_menu.iddara_menu=dara_menu.iddara_menu where iddara_operatore='".$this->iddara_operatore."' and iddara_modulo='$iddara_modulo'";
		$aMENU=$db->select($sSQL);
		return $aMENU;

	}

	function getOPERATORI($condizione=NULL)
	{
		global $db;
		$sSQL="select * from dara_operatore ";
		$sOrder=" order by operatore_cognome,operatore_nome ";
		if (!empty($condizione))
			$sWhere=" where ".$condizione;

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aOPERATORI=$db->select($sSQL);
		return $aOPERATORI;

	}

	function getIDCLIENTIOPERATORE()
	{
		global $db;
		$sSQL="select iddara_cliente from dara_operatore_cliente where iddara_operatore='".$this->iddara_operatore."'";
		$aCLIENTI=$db->select($sSQL);

		$aOPERATORECLIENTI=array();

		if(!empty($aCLIENTI))
		{
			foreach($aCLIENTI as $cliente)
			{
				array_push($aOPERATORECLIENTI,$cliente["iddara_cliente"]);
			}
		}

		return $aOPERATORECLIENTI;
	}

	function getCLIENTIOPERATORE()
	{
		global $db;
		$sSQL="select distinct dara_cliente.* from dara_operatore_cliente inner join dara_cliente on dara_operatore_cliente.iddara_cliente=dara_cliente.iddara_cliente where dara_operatore_cliente.iddara_operatore='".$this->iddara_operatore."' ORDER BY dara_cliente.cliente_nominativo ASC";
		$aOPERATORECLIENTI=$db->select($sSQL);

		return $aOPERATORECLIENTI;
	}

	function getTARIFFEOPERATORE($idcliente=null)
	{
		global $db;

		$query_filter='';
		if(!empty($idcliente))
			$query_filter=" and dara_tbl_tariffa.iddara_cliente='$idcliente' ";

		$sSQL="SELECT DISTINCT dara_tbl_tariffa.* FROM dara_operatore_cliente INNER JOIN dara_cliente ON dara_operatore_cliente.iddara_cliente=dara_cliente.iddara_cliente INNER JOIN dara_tbl_tariffa ON dara_tbl_tariffa.iddara_cliente=dara_cliente.iddara_cliente WHERE dara_operatore_cliente.iddara_operatore='".$this->iddara_operatore."' ".$query_filter." ORDER BY dara_tbl_tariffa.iddara_tbl_tariffa ASC";
		$aTARIFFE=$db->select($sSQL);
		return $aTARIFFE;

	}

	function getRIDUZIONIOPERATORE($idcliente=null)
	{
		global $db;

		$query_filter='';
		if(!empty($idcliente))
			$query_filter=" and dara_tbl_riduzione.iddara_cliente='$idcliente' ";


		$sSQL="SELECT DISTINCT dara_tbl_riduzione.* FROM dara_operatore_cliente INNER JOIN dara_cliente ON dara_operatore_cliente.iddara_cliente=dara_cliente.iddara_cliente INNER JOIN dara_tbl_riduzione ON dara_tbl_riduzione.iddara_cliente=dara_cliente.iddara_cliente WHERE dara_operatore_cliente.iddara_operatore='".$this->iddara_operatore."' ".$query_filter." ORDER BY dara_tbl_riduzione.iddara_tbl_riduzione ASC";
		$aRIDUZIONI=$db->select($sSQL);
		return $aRIDUZIONI;

	}

	function getAVVISIOPERATORE($idcliente=null)
	{
		global $db;

		$query_filter='';
		if(!empty($idcliente))
			$query_filter=" and dara_avviso.iddara_cliente='$idcliente' ";


		$sSQL="SELECT DISTINCT dara_avviso.* FROM dara_operatore_cliente INNER JOIN dara_cliente ON dara_operatore_cliente.iddara_cliente=dara_cliente.iddara_cliente INNER JOIN dara_avviso ON dara_avviso.iddara_cliente=dara_cliente.iddara_cliente WHERE dara_operatore_cliente.iddara_operatore='".$this->iddara_operatore."' ".$query_filter." ORDER BY dara_avviso.avviso_titolo ASC";
		$aOPERATOREAVVISI=$db->select($sSQL);

		return $aOPERATOREAVVISI;
	}

	function getCATEGORIEOPERATORE($idcliente=null)
	{
		global $db;

		$query_filter='';
		if(!empty($idcliente))
			$query_filter=" and dara_tbl_categoria.iddara_cliente='$idcliente' ";


		$sSQL="SELECT DISTINCT dara_tbl_categoria.* FROM dara_operatore_cliente INNER JOIN dara_cliente ON dara_operatore_cliente.iddara_cliente=dara_cliente.iddara_cliente INNER JOIN dara_tbl_categoria ON dara_tbl_categoria.iddara_cliente=dara_cliente.iddara_cliente WHERE dara_operatore_cliente.iddara_operatore='".$this->iddara_operatore."' ".$query_filter." ORDER BY dara_tbl_categoria.categoria_descrizione ASC";
		$aOPERATORECATEGORIE=$db->select($sSQL);

		return $aOPERATORECATEGORIE;
	}

	function getSQLFilterClienti($filter)
	{
		if(empty($filter))
			$filter="iddara_cliente";

		$query_filter="";

		$aIDCLIENTIOPERATORE=$this->getIDCLIENTIOPERATORE();
		if(!empty($aIDCLIENTIOPERATORE))
		{
			$query_filter="(";

			$loop=0;
			foreach($aIDCLIENTIOPERATORE as $idcliente)
			{
				if(!empty($loop))
				{
					$query_filter.=" OR ";
				}

				$query_filter.=$filter."=".$idcliente;

				$loop++;
			}

			$query_filter.=")";
		}

		return $query_filter;
	}
}

/***********/

class DARAModulistica {
	
	public $iddara_modulistica;
	public $modulistica_titolo;
	public $modulistica_filename;
	
	public function __construct($iddara_modulistica) {
		global $db;

		if(!empty($iddara_modulistica))
		{

			$sSQL="select * from dara_modulistica where iddara_modulistica='$iddara_modulistica' ";
			$aOPERATORE=$db->select($sSQL);

			foreach ($aOPERATORE as $key => $rows) 
			{
				$this->iddara_modulistica=$iddara_modulistica;					
				$this->modulistica_titolo=$rows["modulistica_titolo"];
				$this->modulistica_filename=$rows["modulistica_filename"];
			}

		}

	}

	function getMODULI($condizione="")
	{
		global $db;
		$sSQL="select * from dara_modulistica ";
		$sOrder=" order by modulistica_titolo ";
		if (!empty($condizione))
			$sWhere=" where ".$condizione;

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aMODULI=$db->select($sSQL);
		return $aMODULI;

	}

}


class DARADomanda {

	public $iddara_domanda;
	public $iddara_cliente;
	public $iddara_avviso;
	public $domanda_anno;
	public $iddara_presentetada;
	public $iddara_richiedente;
	public $domanda_datainvio;
	public $domanda_orainvio;
	public $domanda_flaginviata;
	public $iddara_tbl_domandastato;	
	
	public $domanda_note;
	public $domanda_punteggio;

	public $domanda_categoria;
	public $domanda_tariffa;
	public $domanda_riduzione;
	public $domanda_sconto;
	//public $domanda_dovuto;	


	public function __construct($iddara_domanda=null) 
	{
		global $db;
		if(!empty($iddara_domanda))
		{
			$this->iddara_domanda=$iddara_domanda;
			
			$sSQL="SELECT * FROM dara_domanda WHERE iddara_domanda='$iddara_domanda'";
			$aAVVISO=$db->select($sSQL);

			foreach ($aAVVISO as $key => $rows) 
			{
				$this->iddara_cliente=$rows["iddara_cliente"];
				$this->iddara_avviso=$rows["iddara_avviso"];
				$this->iddara_presentetada=$rows["iddara_presentetada"];
				$this->iddara_richiedente=$rows["iddara_richiedente"];
				$this->domanda_anno=$rows["domanda_anno"];
				$this->domanda_datainvio=$rows["domanda_datainvio"];
				$this->domanda_orainvio=$rows["domanda_orainvio"];
				$this->domanda_flaginviata=$rows["domanda_flaginviata"];					
				
				$this->iddara_tbl_domandastato=$rows["iddara_tbl_domandastato"];
				$this->domanda_note=$rows["domanda_note"];
				$this->domanda_punteggio=$rows["domanda_punteggio"];

				$this->domanda_categoria=$rows["domanda_categoria"];

				$this->domanda_categoria_descrizione=$db->getVALUE("SELECT categoria_descrizione FROM dara_tbl_categoria WHERE iddara_tbl_categoria='".$this->domanda_categoria."'",'categoria_descrizione');

				$this->domanda_tariffa=$rows["domanda_tariffa"];
				$this->domanda_riduzione=$rows["domanda_riduzione"];
				$this->domanda_sconto=$rows["domanda_sconto"];
				
				/*
				$this->domanda_dovuto=$rows["domanda_dovuto"];
				if(empty($this->domanda_dovuto))
					$this->domanda_dovuto=0;
				*/
			}	
		}
		
	}

	function getDOMANDE($condizione=NULL)
	{
		global $db;

		$sSelect="select dara_domanda.* ";

		$sFrom=" from dara_domanda 
		inner join dara_anagrafica presentante on dara_domanda.iddara_presentetada=presentante.iddara_anagrafica 
		inner join dara_anagrafica richiedente on dara_domanda.iddara_richiedente=richiedente.iddara_anagrafica";
		
		if (!empty($condizione))
			$sWhere=" where ".$condizione;

		$sOrder=" order by iddara_domanda ";

		$sSQL=$sSelect.$sFrom.$sWhere.$sOrder;
		$aDOMANDE=$db->select($sSQL);
		return $aDOMANDE;
	}

	function getPRESENZA($presenza_data)
	{
		global $db;
		
		$sSQL="select iddara_domanda_presenza from dara_domanda_presenza where iddara_domanda='".$this->iddara_domanda."' and presenza_data='".$presenza_data."'";

		$iddara_domanda_presenza=$db->getVALUE($sSQL,"iddara_domanda_presenza");

		if (!empty($iddara_domanda_presenza))
			return true;
		else
			return false;
	}

	function savePRESENZA($presenza_data,$value)
	{
		global $db;
		if (!$this->getPRESENZA($presenza_data) && $value=='1')
			$sSQL="insert into dara_domanda_presenza (iddara_domanda,presenza_data) values(".$this->iddara_domanda.",'".$presenza_data."')";
		else
			$sSQL="delete from dara_domanda_presenza where iddara_domanda='".$this->iddara_domanda."' and presenza_data='".$presenza_data."' ";		
		$result=$db->query($sSQL);

	}

	function getDICHIARAZIONE($iddara_avviso_dichiarazione)
	{
		global $db;
		$sSQL="select iddara_domanda_dichiarazione from dara_domanda_dichiarazione where iddara_domanda='".$this->iddara_domanda."' and iddara_avviso_dichiarazione='".$iddara_avviso_dichiarazione."'";
		$iddara_domanda_dichiarazione=$db->getVALUE($sSQL,"iddara_domanda_dichiarazione");
		if (!empty($iddara_domanda_dichiarazione))
			return true;
		else
			return false;
	}

	function saveDICHIARAZIONE($iddara_avviso_dichiarazione,$value)
	{
		global $db;
		if (!$this->getDICHIARAZIONE($iddara_avviso_dichiarazione) && $value=='1')
			$sSQL="insert into dara_domanda_dichiarazione (iddara_domanda,iddara_avviso_dichiarazione) values(".$this->iddara_domanda.",".$iddara_avviso_dichiarazione.")";
		else
			$sSQL="delete from dara_domanda_dichiarazione where iddara_domanda='".$this->iddara_domanda."' and iddara_avviso_dichiarazione='".$iddara_avviso_dichiarazione."' ";

		$result=$db->query($sSQL);

	}

	function getAUTOCERTIFICAZIONE($iddara_avviso_autocertificazione)
	{
		global $db;
		$sSQL="select iddara_domanda_autocertificazione from dara_domanda_autocertificazione where iddara_domanda='".$this->iddara_domanda."' and iddara_avviso_autocertificazione='".$iddara_avviso_autocertificazione."'";
		$iddara_domanda_autocertificazione=$db->getVALUE($sSQL,"iddara_domanda_autocertificazione");
		if (!empty($iddara_domanda_autocertificazione))
			return true;
		else
			return false;
	}

	function getAUTOCERTIFICAZIONEVALUE($iddara_avviso_autocertificazione)
	{
		global $db;
		$sSQL="select autocertificazione_risposta from dara_domanda_autocertificazione where iddara_domanda='".$this->iddara_domanda."' and iddara_avviso_autocertificazione='".$iddara_avviso_autocertificazione."'";
		$autocertificazione_risposta=$db->getVALUE($sSQL,"autocertificazione_risposta");

		return $autocertificazione_risposta;
	}

	function saveAUTOCERTIFICAZIONE($iddara_avviso_autocertificazione,$iddara_tbl_tipocampo,$autocertificazionevalue)
	{
		global $db;

		$iddara_avviso_autocertificazione_risposta=null;

		if ($iddara_tbl_tipocampo==5 && $autocertificazionevalue=='on')
			$autocertificazionevalue=1;
		elseif ($iddara_tbl_tipocampo==5)
			$autocertificazionevalue=0;
		elseif($iddara_tbl_tipocampo==3)
			$iddara_avviso_autocertificazione_risposta=$autocertificazionevalue;

		if (!$this->getAUTOCERTIFICAZIONE($iddara_avviso_autocertificazione))
		{
			$sSQL="insert into dara_domanda_autocertificazione (iddara_domanda,iddara_avviso_autocertificazione,autocertificazione_risposta,iddara_avviso_autocertificazione_risposta) values(".$this->iddara_domanda.",".$iddara_avviso_autocertificazione.",'".$autocertificazionevalue."','".$iddara_avviso_autocertificazione_risposta."')";
			$result=$db->query($sSQL);
		}
		elseif($this->getAUTOCERTIFICAZIONE($iddara_avviso_autocertificazione))
		{
			$sSQL="update dara_domanda_autocertificazione set autocertificazione_risposta='".$autocertificazionevalue."', iddara_avviso_autocertificazione_risposta='".$iddara_avviso_autocertificazione_risposta."' where iddara_domanda='".$this->iddara_domanda."' and iddara_avviso_autocertificazione='".$iddara_avviso_autocertificazione."'";
			$result=$db->query($sSQL);
		}	

	}

	function getDOCUMENTO($iddara_avviso_documento)
	{
		global $db;
		$sSQL="select iddara_domanda_documento from dara_domanda_documento where iddara_domanda='".$this->iddara_domanda."' and iddara_avviso_documento='".$iddara_avviso_documento."'";
		$iddara_domanda_documento=$db->getVALUE($sSQL,"iddara_domanda_documento");
		if (!empty($iddara_domanda_documento))
			return true;
		else
			return false;
	}

	function getDOCUMENTOVALUE($iddara_avviso_documento)
	{
		global $db;
		$sSQL="select documento_filename from dara_domanda_documento where iddara_domanda='".$this->iddara_domanda."' and iddara_avviso_documento='".$iddara_avviso_documento."'";
		$documento_filename=$db->getVALUE($sSQL,"documento_filename");
		
		return $documento_filename;
	}

	function saveDOCUMENTO($iddara_avviso_documento,$filename)
	{
		global $db;
		if (!$this->getDOCUMENTO($iddara_avviso_documento))
			$sSQL="insert into dara_domanda_documento (iddara_domanda,iddara_avviso_documento,documento_filename) values(".$this->iddara_domanda.",".$iddara_avviso_documento.",'".$filename."')";
		else
			$sSQL="update dara_domanda_documento set documento_filename='".$filename."' where iddara_domanda='".$this->iddara_domanda."' and iddara_avviso_documento='".$iddara_avviso_documento."' ";
		$result=$db->query($sSQL);

	}	

	function getSTEPSTATUS($front=false)
	{
		global $db;
		
		$aRESPONSE_STATUS=array();

		//controllo step1

		$status_step1=true;
		$errors_step1=0;
		$errors_step1_presenante=0;

		$errors_step1_atleta=0;
		$errors_step1_message="";

		$aAVVISO=new DARAAvviso($this->iddara_avviso);

		$presentante=new DARAAnagrafica($this->iddara_presentetada);

		if(empty($presentante->anagrafica_cognome))
		{
			$errors_step1++;
			$errors_step1_presenante++;
			$errors_step1_message.="Cognome, ";
		}

		if(empty($presentante->anagrafica_nome))
		{
			$errors_step1++;
			$errors_step1_presenante++;
			$errors_step1_message.="Nome, ";

		}

		if(empty($presentante->anagrafica_datanascita))
		{
			$errors_step1++;
			$errors_step1_presenante++;
			$errors_step1_message.="Data di nascita, ";

		}

		if(empty($presentante->anagrafica_genere))
		{
			$errors_step1++;
			$errors_step1_presenante++;
			$errors_step1_message.="Genere, ";

		}

		if(empty($presentante->idnazione_nascita))
		{
			$errors_step1++;
			$errors_step1_presenante++;
			$errors_step1_message.="Nazione di nascita, ";
		}

		if(empty($presentante->idnazionalita))
		{
			$errors_step1++;
			$errors_step1_presenante++;
			$errors_step1_message.="Nazionalità, ";
		}

		if($presentante->idnazione_nascita==122)
		{
			if(empty($presentante->idcomune_nascita))
			{
				$errors_step1++;
				$errors_step1_presenante++;
				$errors_step1_message.="Comune di nascita, ";
			}
		}
		else
		{
			if(empty($presentante->anagrafica_comunenascita))
			{
				$errors_step1++;
				$errors_step1_presenante++;
				$errors_step1_message.="Comune di nascita, ";
			}
		}

		if(empty($presentante->anagrafica_provnascita))
		{
			$errors_step1++;
			$errors_step1_presenante++;
			$errors_step1_message.="Provincia di nascita, ";
		}

		if(empty($presentante->idcomune_residenza))
		{
			$errors_step1++;
			$errors_step1_presenante++;
			$errors_step1_message.="Comune di residenza, ";
		}

		if(empty($presentante->anagrafica_provresidenza))
		{
			$errors_step1++;
			$errors_step1_presenante++;
			$errors_step1_message.="Provincia di residenza, ";
		}

		if(empty($presentante->anagrafica_indirizzo))
		{
			$errors_step1++;
			$errors_step1_presenante++;
			$errors_step1_message.="Indirizzo di residenza, ";
		}

		if(empty($presentante->anagrafica_civico))
		{
			$errors_step1++;
			$errors_step1_presenante++;
			$errors_step1_message.="Civico di residenza, ";
		}

		if(empty($presentante->anagrafica_cellulare))
		{
			$errors_step1++;
			$errors_step1_presenante++;
			$errors_step1_message.="Cellulare, ";
		}

		if($errors_step1_presenante>0)
		{
			$errors_step1_message.="Dati richiedente<br>";
		}

		if($this->iddara_presentetada!=$this->iddara_richiedente)
		{
			$richiedente=new DARAAnagrafica($this->iddara_richiedente);

			if(empty($richiedente->anagrafica_cognome))
			{
				$errors_step1++;
				$errors_step1_atleta++;
				$errors_step1_message.="Cognome, ";
			}

			if(empty($richiedente->anagrafica_nome))
			{
				$errors_step1++;
				$errors_step1_atleta++;
				$errors_step1_message.="Nome, ";

			}

			if(empty($richiedente->anagrafica_datanascita))
			{
				$errors_step1++;
				$errors_step1_atleta++;
				$errors_step1_message.="Data di nascita, ";

			}

			if(empty($richiedente->anagrafica_genere))
			{
				$errors_step1++;
				$errors_step1_atleta++;
				$errors_step1_message.="Genere, ";

			}

			if(empty($richiedente->idnazione_nascita))
			{
				$errors_step1++;
				$errors_step1_atleta++;
				$errors_step1_message.="Nazione di nascita, ";
			}

			if(empty($richiedente->idnazionalita))
			{
				$errors_step1++;
				$errors_step1_atleta++;
				$errors_step1_message.="Nazionalità, ";
			}

			if($richiedente->idnazione_nascita==122)
			{
				if(empty($richiedente->idcomune_nascita))
				{
					$errors_step1++;
					$errors_step1_atleta++;
					$errors_step1_message.="Comune di nascita, ";
				}
			}
			else
			{
				if(empty($richiedente->anagrafica_comunenascita))
				{
					$errors_step1++;
					$errors_step1_atleta++;
					$errors_step1_message.="Comune di nascita, ";
				}
			}

			if(empty($richiedente->anagrafica_provnascita))
			{
				$errors_step1++;
				$errors_step1_atleta++;
				$errors_step1_message.="Provincia di nascita, ";
			}

			if(empty($richiedente->idcomune_residenza))
			{
				$errors_step1++;
				$errors_step1_atleta++;
				$errors_step1_message.="Comune di residenza, ";
			}

			if(empty($richiedente->anagrafica_provresidenza))
			{
				$errors_step1++;
				$errors_step1_atleta++;
				$errors_step1_message.="Provincia di residenza, ";
			}

			if(empty($richiedente->anagrafica_indirizzo))
			{
				$errors_step1++;
				$errors_step1_atleta++;
				$errors_step1_message.="Indirizzo di residenza, ";
			}

			if(empty($richiedente->anagrafica_civico))
			{
				$errors_step1++;
				$errors_step1_atleta++;
				$errors_step1_message.="Civico di residenza, ";
			}

			if(empty($richiedente->anagrafica_cellulare))
			{
				$errors_step1++;
				$errors_step1_atleta++;
				$errors_step1_message.="Cellulare, ";
			}

			if($errors_step1_atleta>0)
			{
				$errors_step1_message.="Dati atleta<br>";
			}
		}

		if($errors_step1>0)
		{
			$status_step1=false;
			$errors_step1_message=rtrim($errors_step1_message, ",");
		}

		$aRESPONSE_STATUS["step1"]=$status_step1;
		$aRESPONSE_STATUS["step1_error_message"]=$errors_step1_message;

		//controllo step2

		$status_step2=true;
		$errors_step2=0;
		$errors_step2_message="";

		$aAUTOCERTIFICAZIONI_CHECK=array();
		$aDIPENDENZE=$aAVVISO->getDIPENDENZE();
		if (@is_array($aDIPENDENZE) && !empty($aDIPENDENZE))
		{
			$cDipendenze=1;
			$aDIPENDENZE_REQUIRED=array();

			foreach($aDIPENDENZE as $key=>$aDATI)
			{
				$iddara_avviso_autocertificazione=$aDATI["iddara_avviso_autocertificazione"];

				$sSQL="select autocertificazione_flagobbligatorio from dara_avviso_autocertificazione where iddara_avviso_autocertificazione='$iddara_avviso_autocertificazione'";
				$autocertificazione_flagobbligatorio=$db->getVALUE($sSQL,'autocertificazione_flagobbligatorio');

				if(!empty($autocertificazione_flagobbligatorio))
				{
					$aDIPENDENZE_REQUIRED[$iddara_avviso_autocertificazione][$cDipendenze]["domanda"]=$aDATI["iddara_avviso_autocertificazione_capo"];
					$aDIPENDENZE_REQUIRED[$iddara_avviso_autocertificazione][$cDipendenze]["risposta"]=$aDATI["iddara_avviso_autocertificazione_capo_risposta"];

					$cDipendenze++;
				}
			}

			if(!empty($aDIPENDENZE_REQUIRED))
			{
				foreach($aDIPENDENZE_REQUIRED as $idautocertificazione=>$aDIPENDENZA_DETTAGLIO)
				{
					$sSQL="select autocertificazione_descrizione from dara_avviso_autocertificazione where iddara_avviso_autocertificazione='$idautocertificazione'";
					$autocertificazione_descrizione=$db->getVALUE($sSQL,'autocertificazione_descrizione');

					$aAUTOCERTIFICAZIONI_CHECK[]=$idautocertificazione;

					$autocertificazioneVALUE=$this->getAUTOCERTIFICAZIONEVALUE($idautocertificazione);
					if(empty($autocertificazioneVALUE))
					{
						foreach($aDIPENDENZA_DETTAGLIO as $dipendenza)
						{
							$domanda=$dipendenza["domanda"];
							$risposta=$dipendenza["risposta"];

							$risposta_temp=$this->getAUTOCERTIFICAZIONEVALUE($domanda);
							if($risposta_temp==$risposta)
							{
								$errors_step2++;
								$errors_step2_message=stripslashes($autocertificazione_descrizione).", ";
							}
						}
					}
				}
			}
		}

		$aAUTOCERTIFICAZIONI=$aAVVISO->getAUTOCERTIFICAZIONI($front);
		if (@is_array($aAUTOCERTIFICAZIONI) && !empty($aAUTOCERTIFICAZIONI))
		{
			$iCounter=1;
			foreach ($aAUTOCERTIFICAZIONI as $key => $aDATI) 
			{
				$iddara_avviso_autocertificazione=$aDATI["iddara_avviso_autocertificazione"];

				if(!in_array($iddara_avviso_autocertificazione,$aAUTOCERTIFICAZIONI_CHECK))
				{
					$autocertificazioneVALUE=$this->getAUTOCERTIFICAZIONEVALUE($iddara_avviso_autocertificazione);

					if(!empty($aDATI["autocertificazione_flagobbligatorio"]) && empty($autocertificazioneVALUE))
					{
						$errors_step2++;
						$errors_step2_message.=stripslashes($aDATI["autocertificazione_descrizione"]).", ";
					}
				}
			}
		}

		if($errors_step2>0)
		{
			$status_step2=false;
			$errors_step2_message=rtrim($errors_step2_message, ", ");
		}

		$aRESPONSE_STATUS["step2"]=$status_step2;
		$aRESPONSE_STATUS["step2_error_message"]=$errors_step2_message;

		//controllo step3

		$status_step3=true;
		$errors_step3=0;
		$errors_step3_message="";

		$aDICHIARAZIONI=$aAVVISO->getDICHIARAZIONI($front);
		if (@is_array($aDICHIARAZIONI))
		{
			$iCounter=1;
			foreach ($aDICHIARAZIONI as $key => $aDATI) 
			{
				$iddara_avviso_dichiarazione=$aDATI["iddara_avviso_dichiarazione"];
				//Verifico se è stata selezionata
				$ischecked=$this->getDICHIARAZIONE($iddara_avviso_dichiarazione);
				if(!empty($aDATI["dichiarazione_flagobbligatorio"]) && empty($ischecked))
				{
					$errors_step3++;
					$errors_step3_message.=stripslashes($aDATI["dichiarazione_descrizione"]).", ";
				}
			}
		}

		if($errors_step3>0)
		{
			$status_step3=false;
			$errors_step3_message=rtrim($errors_step3_message, ", ");
		}

		$aRESPONSE_STATUS["step3"]=$status_step3;
		$aRESPONSE_STATUS["step3_error_message"]=$errors_step3_message;

		//controllo step4

		$status_step4=true;
		$errors_step4=0;
		$errors_step4_message="";

		$domandepath="../documenti/dara/domande/";

		$aDOCUMENTI=$aAVVISO->getDOCUMENTI($front);
		if (@is_array($aDOCUMENTI))
		{
			$iCounter=1;
			foreach ($aDOCUMENTI as $key => $aDATI) 
			{
				$iddara_avviso_documento=$aDATI["iddara_avviso_documento"];

				$documento_filename=$this->getDOCUMENTOVALUE($iddara_avviso_documento);

				if(!empty($aDATI["documento_flagobbligatorio"]) && (empty($documento_filename) || !file_exists($domandepath.$documento_filename)))
				{
					$errors_step4++;
					$errors_step4_message.=stripslashes($aDATI["documento_descrizione"]).", ";
				}
			}
		}

		if($errors_step4>0)
		{
			$status_step4=false;
			$errors_step4_message=rtrim($errors_step4_message, ", ");
		}

		$aRESPONSE_STATUS["step4"]=$status_step4;
		$aRESPONSE_STATUS["step4_error_message"]=$errors_step4_message;


		return $aRESPONSE_STATUS;
	}

}	

class DARAAnagrafica {

	public $iddara_anagrafica;
	public $iddara_cliente;
	public $anagrafica_cognome;
	public $anagrafica_nome;
	public $anagrafica_codicefiscale;
	public $anagrafica_genere;
	public $anagrafica_datanascita;
	public $anagrafica_nazionenascita;
	public $anagrafica_comunenascita;	
	public $anagrafica_provnascita;
	public $anagrafica_comuneresidenza;

	public $anagrafica_provresidenza;
	public $anagrafica_capresidenza;
	public $anagrafica_indirizzo;
	public $anagrafica_civico;
	public $anagrafica_telefono;
	public $anagrafica_cellulare;
	public $anagrafica_recapiti;
	public $anagrafica_email;
	public $anagrafica_pec;
	public $anagrafica_abilita_accesso;
	public $anagrafica_username;
	public $anagrafica_password;

	public function __construct($iddara_anagrafica=null) 
	{
		global $db;
		if(!empty($iddara_anagrafica))
		{
			$this->iddara_anagrafica=$iddara_anagrafica;
			
			$sSQL="SELECT * FROM dara_anagrafica WHERE iddara_anagrafica='$iddara_anagrafica'";
			$aAVVISO=$db->select($sSQL);

			foreach ($aAVVISO as $key => $rows) 
			{
				$this->iddara_cliente=$rows["iddara_cliente"];
				$this->anagrafica_cognome=$rows["anagrafica_cognome"];
				$this->anagrafica_nome=$rows["anagrafica_nome"];
				$this->anagrafica_codicefiscale=$rows["anagrafica_codicefiscale"];
				$this->anagrafica_genere=$rows["anagrafica_genere"];			
				$this->anagrafica_datanascita=$rows["anagrafica_datanascita"];

				$this->idnazionalita=$rows["idnazionalita"];
				if(!empty($this->idnazionalita))
				{
					$sSQL="select nazione from nazione where idnazione='$this->idnazionalita'";
      				$this->anagrafica_nazionalita=$db->getVALUE($sSQL,'nazione');
				}
				else
				{
					$this->anagrafica_nazionalita=$rows["anagrafica_nazionalita"];	
				}

				$this->idnazione_nascita=$rows["idnazione_nascita"];
				$this->anagrafica_nazionenascita=$rows["anagrafica_nazionenascita"];
								
				$this->idcomune_nascita=$rows["idcomune_nascita"];

				if(!empty($this->idcomune_nascita))
				{
					$sSQL="select comune from comune where idcomune='$this->idcomune_nascita'";
      				$this->anagrafica_comunenascita=$db->getVALUE($sSQL,'comune');

					$sSQL="select provincia from comune where idcomune='$this->idcomune_nascita'";
      				$this->anagrafica_provnascita=$db->getVALUE($sSQL,'provincia');
				}
				else
				{
					$this->anagrafica_comunenascita=$rows["anagrafica_comunenascita"];	
					$this->anagrafica_provnascita=$rows["anagrafica_provnascita"];
				}
				
				$this->idcomune_residenza=$rows["idcomune_residenza"];
				if(!empty($this->idcomune_residenza))
				{
					$sSQL="select comune from comune where idcomune='$this->idcomune_residenza'";
      				$this->anagrafica_comuneresidenza=$db->getVALUE($sSQL,'comune');

					$sSQL="select provincia from comune where idcomune='$this->idcomune_residenza'";
      				$this->anagrafica_provresidenza=$db->getVALUE($sSQL,'provincia');

					$sSQL="select cap from comune where idcomune='$this->idcomune_residenza'";
      				$this->anagrafica_capresidenza=$db->getVALUE($sSQL,'cap');
				}
				else
				{
					$this->anagrafica_comuneresidenza=$rows["anagrafica_comuneresidenza"];	
					$this->anagrafica_provresidenza=$rows["anagrafica_provresidenza"];
					$this->anagrafica_capresidenza=$rows["anagrafica_capresidenza"];
				}

				$this->anagrafica_indirizzo=$rows["anagrafica_indirizzo"];
				$this->anagrafica_civico=$rows["anagrafica_civico"];

				$this->anagrafica_telefono=$rows["anagrafica_telefono"];
				$this->anagrafica_cellulare=$rows["anagrafica_cellulare"];

				$this->anagrafica_recapiti="";
				if(!empty($this->anagrafica_telefono))
				{
					if(!empty($this->anagrafica_recapiti))
						$this->anagrafica_recapiti.="/";

					$this->anagrafica_recapiti.=$this->anagrafica_telefono;
				}

				if(!empty($this->anagrafica_cellulare))
				{
					if(!empty($this->anagrafica_recapiti))
						$this->anagrafica_recapiti.="/";

					$this->anagrafica_recapiti.=$this->anagrafica_cellulare;
				}
				
				$this->anagrafica_email=$rows["anagrafica_email"];
				$this->anagrafica_pec=$rows["anagrafica_pec"];
				$this->anagrafica_abilita_accesso=$rows["anagrafica_abilita_accesso"];
				$this->anagrafica_username=$rows["anagrafica_username"];
				$this->anagrafica_password=$rows["anagrafica_password"];
			}	
		}
	}

	function getANAGRAFICHE($condizione=NULL)
	{
		global $db;
		$sSQL="select * from dara_anagrafica ";
		$sOrder=" order by anagrafica_cognome,anagrafica_nome ";
		if (!empty($condizione))
			$sWhere=" where ".$condizione;

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aANAGRAFICHE=$db->select($sSQL);
		return $aANAGRAFICHE;
	}

}	

class DARAModulo {

	public $iddara_modulo;
	public $modulo_titolo;
	public $modulo_descrizione;
	public $modulo_immagine;
	public $modulo_sigla;

	public function __construct($iddara_modulo=null) 
	{
		global $db;
		if(!empty($iddara_modulo))
		{
			$this->iddara_modulo=$iddara_modulo;
			
			$sSQL="SELECT * FROM dara_modulo WHERE iddara_modulo='$iddara_modulo'";
			$aMODULO=$db->select($sSQL);

			foreach ($aMODULO as $key => $rows) 
			{
				$this->modulo_titolo=$rows["modulo_titolo"];
				$this->modulo_descrizione=$rows["modulo_descrizione"];
				$this->modulo_immagine=$rows["modulo_immagine"];
				$this->modulo_sigla=$rows["modulo_sigla"];
			}	
		}
		
	}

	function getMODULI($condizione=NULL)
	{
		global $db;
		$sSQL="select * from dara_modulo ";
		$sOrder=" order by iddara_modulo ";
		if (!empty($condizione))
			$sWhere=" where ".$condizione;

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aMODULI=$db->select($sSQL);
		return $aMODULI
		;

	}

	function getAUTORIZZAZIONI()
	{
		global $db;
		$sSQL="select * from dara_autorizzazione where iddara_modulo='".$this->iddara_modulo."' ";
		$sOrder=" order by iddara_autorizzazione ";

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aAUTORIZZAZIONI=$db->select($sSQL);
		return $aAUTORIZZAZIONI
		;

	}	

}

class DARACliente {

	public $iddara_cliente;
	public $cliente_nominativo;
	public $idcomune;
	public $cliente_citta;
	public $cliente_cap;
	public $cliente_provincia;
	public $cliente_indirizzo;
	public $cliente_civico;
	public $cliente_email;
	public $cliente_pec;
	public $cliente_telefono;
	public $cliente_partitaiva;
	public $cliente_codicefiscale;
	public $cliente_flagabilitato;

	public function __construct($iddara_cliente=null) 
	{
		global $db;
		if(!empty($iddara_cliente))
		{
			$this->iddara_cliente=$iddara_cliente;
			
			$sSQL="SELECT * FROM dara_cliente WHERE iddara_cliente='$iddara_cliente'";
			$aCLIENTE=$db->select($sSQL);
			if (is_array($aCLIENTE))
			{
				foreach ($aCLIENTE as $key => $rows) 
				{
					$this->cliente_nominativo=$rows["cliente_nominativo"];
					$this->idcomune=$rows["idcomune"];
					$this->cliente_citta=$rows["cliente_citta"];
					$this->cliente_cap=$rows["cliente_cap"];
					$this->cliente_provincia=$rows["cliente_provincia"];
					$this->cliente_indirizzo=$rows["cliente_indirizzo"];
					$this->cliente_civico=$rows["cliente_civico"];
					$this->cliente_email=$rows["cliente_email"];
					$this->cliente_pec=$rows["cliente_pec"];
					$this->cliente_telefono=$rows["cliente_telefono"];
					$this->cliente_partitaiva=$rows["cliente_partitaiva"];
					$this->cliente_codicefiscale=$rows["cliente_codicefiscale"];
					$this->cliente_flagabilitato=$rows["cliente_flagabilitato"];

				}
			}
	
		}
		
	}


	function getCLIENTI($sWhere=null)
	{
	    global $db;

	    $sSQL="select * from dara_cliente ";
		
	    $sOrder=" order by cliente_nominativo ";

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aCLIENTI=$db->select($sSQL);
	    return $aCLIENTI;    
	}

	function getCOMUNI()
	{
	    global $db;

	    $sSQL="select * from dara_cliente_comune ";
		$sWhere=" where iddara_cliente='".$this->iddara_cliente."'";
	    $sOrder=" order by iddara_cliente_comune ";

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aCOMUNI=$db->select($sSQL);
	    return $aCOMUNI;    
	}

	function getAVVISI()
	{
		global $db;

		$sSQL="select * from dara_avviso where iddara_cliente='".$this->iddara_cliente."' ";
		$sWhere="";
		$sOrder=" order by avviso_titolo asc ";

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aAVVISI=$db->select($sSQL);
		return $aAVVISI;

	}

	function getCATEGORIE()
	{
		global $db;

		$sSQL="select * from dara_tbl_categoria where iddara_cliente='".$this->iddara_cliente."' ";
		$sWhere="";
		$sOrder=" order by iddara_tbl_categoria ";

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aCATEGORIE=$db->select($sSQL);
		return $aCATEGORIE
		;

	}

	function getTARIFFE($idavviso=null)
	{
		global $db;

		$sWhere="";
		if(!empty($idavviso))
		{
			$sWhere=" and iddara_avviso='$idavviso'";
		}

		$sSQL="select * from dara_tbl_tariffa where iddara_cliente='".$this->iddara_cliente."' ".$sWhere;
		$sWhere="";
		$sOrder=" order by iddara_tbl_tariffa ";

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aTARIFFE=$db->select($sSQL);
		return $aTARIFFE;

	}	

	function getRIDUZIONI($idavviso=null)
	{
		global $db;

		$sWhere="";
		if(!empty($idavviso))
		{
			$sWhere=" and iddara_avviso='$idavviso'";
		}
		
		$sSQL="select * from dara_tbl_riduzione where iddara_cliente='".$this->iddara_cliente."' ".$sWhere;
		$sOrder=" order by iddara_tbl_riduzione ";

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aRIDUZIONI=$db->select($sSQL);
		return $aRIDUZIONI
		;

	}	

	function getARTICOLI()
	{
	    global $db;

	    $sSQL="select * from dara_articolo ";
	    $sWhere=" where iddara_cliente='".$this->iddara_cliente."'";
	    $sOrder=" order by iddara_articolo ";

	    $sSQL=$sSQL.$sWhere.$sOrder;
	    $aARTICOLI=$db->select($sSQL);
	    return $aARTICOLI;    
	}	

}

class DARACategoria {

	public $iddara_tbl_categoria;
	public $iddara_cliente;
	public $categoria_descrizione;

	public function __construct($iddara_tbl_categoria=null) 
	{
		global $db;
		if(!empty($iddara_tbl_categoria))
		{
			$this->iddara_tbl_categoria=$iddara_tbl_categoria;
			
			$sSQL="SELECT * FROM dara_tbl_categoria WHERE iddara_tbl_categoria='$iddara_tbl_categoria'";
			$aMODULO=$db->select($sSQL);
			if (is_array($aMODULO))
			{
				foreach ($aMODULO as $key => $rows) 
				{
					$this->iddara_cliente=$rows["iddara_cliente"];
					$this->categoria_descrizione=$rows["categoria_descrizione"];
				}				
			}
	
		}
		
	}

	function getCATEGORIE($idCLIENTE)
	{
		global $db;
		$sSQL="select * from dara_tbl_categoria where iddara_cliente='".$idCLIENTE."' ";
		$sOrder=" order by iddara_tbl_categoria ";

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aCATEGORIE=$db->select($sSQL);
		return $aCATEGORIE
		;

	}


	function insertCATEGORIA($categoria_descrizione,$iddara_cliente)
	{
		global $db;

		$sSQL="insert into dara_tbl_categoria (iddara_cliente,categoria_descrizione) values('".$iddara_cliente."','".$categoria_descrizione."')";
		$db->query($sSQL);
		return $db->insert_id();

	}

	function updateCATEGORIA($iddara_tbl_categoria,$categoria_descrizione,$iddara_cliente)
	{
		global $db;

		$sSQL="update dara_tbl_categoria set categoria_descrizione='".$categoria_descrizione."',iddara_cliente='".$iddara_cliente."' where iddara_tbl_categoria='".$iddara_tbl_categoria."'";
		$db->query($sSQL);
	}

	function deleteCATEGORIA($iddara_tbl_categoria)
	{
		global $db;
		$sSQL="delete from dara_tbl_categoria where iddara_tbl_categoria='$iddara_tbl_categoria'";
		$db->query($sSQL);
	}	

}

class DARATariffa {

	public $iddara_tbl_tariffa;
	public $iddara_cliente;
	public $iddara_avviso;
	public $tariffa_anno;
	public $tariffa_descrizione;
	public $tariffa_quotafissa;
	public $tariffa_quotavariabile;
	public $tariffa_quotaminima;

	public function __construct($iddara_tbl_tariffa=null) 
	{
		global $db;
		if(!empty($iddara_tbl_tariffa))
		{
			$this->iddara_tbl_tariffa=$iddara_tbl_tariffa;
			
			$sSQL="SELECT * FROM dara_tbl_tariffa WHERE iddara_tbl_tariffa='$iddara_tbl_tariffa'";
			$aMODULO=$db->select($sSQL);
			if (is_array($aMODULO))
			{
				foreach ($aMODULO as $key => $rows) 
				{
					$this->iddara_cliente=$rows["iddara_cliente"];
					$this->tariffa_descrizione=$rows["tariffa_descrizione"];
					$this->iddara_avviso=$rows["iddara_avviso"];
					$this->tariffa_anno=$rows["tariffa_anno"];
					$this->tariffa_quotafissa=$rows["tariffa_quotafissa"];
					$this->tariffa_quotavariabile=$rows["tariffa_quotavariabile"];
					$this->tariffa_quotaminima=$rows["tariffa_quotaminima"];
				}				
			}
	
		}
		
	}

	function getTARIFFE($idCLIENTE)
	{
		global $db;
		$sSQL="select * from dara_tbl_tariffa where iddara_cliente='".$idCLIENTE."' ";
		$sOrder=" order by iddara_tbl_tariffa ";

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aTARIFFE=$db->select($sSQL);
		return $aTARIFFE;
	}


	function insertTARIFFA($tariffa_descrizione,$iddara_cliente,$iddara_avviso=0,$tariffa_quotafissa=0,$tariffa_quotavariabile=0,$tariffa_quotaminima=0)
	{
		global $db;

		$sSQL="insert into dara_tbl_tariffa (iddara_cliente,iddara_avviso,tariffa_descrizione,tariffa_quotafissa) values('".$iddara_cliente."','".$iddara_avviso."','".$tariffa_descrizione."','".$tariffa_quotafissa."')";
		$db->query($sSQL);
		return $db->insert_id();
	}


	function updateTARIFFA($iddara_avviso,$tariffa_descrizione,$iddara_cliente,$tariffa_quotafissa=0,$tariffa_quotavariabile=0,$tariffa_quotaminima=0)
	{
		global $db;

		$sSQL="update dara_tbl_tariffa set tariffa_descrizione='".$tariffa_descrizione."',iddara_cliente='".$iddara_cliente."',iddara_avviso='".$iddara_avviso."',tariffa_quotafissa='".$tariffa_quotafissa."' where iddara_tbl_tariffa='".$this->iddara_tbl_tariffa."'";
		$db->query($sSQL);
	}

	function deleteTARIFFA()
	{
		global $db;

		$sSQL="delete from dara_tbl_tariffa where iddara_tbl_tariffa='".$this->$iddara_tbl_tariffa."'";
		$db->query($sSQL);
	}	

}

class DARARiduzione {

	public $iddara_tbl_riduzione;
	public $iddara_cliente;
	public $iddara_avviso;
	public $riduzione_descrizione;
	public $riduzione_percentuale;

	public function __construct($iddara_tbl_riduzione=null) 
	{
		global $db;
		if(!empty($iddara_tbl_riduzione))
		{
			$this->iddara_tbl_riduzione=$iddara_tbl_riduzione;
			
			$sSQL="SELECT * FROM dara_tbl_riduzione WHERE iddara_tbl_riduzione='$iddara_tbl_riduzione'";
			$aMODULO=$db->select($sSQL);
			if (is_array($aMODULO))
			{
				foreach ($aMODULO as $key => $rows) 
				{
					$this->iddara_cliente=$rows["iddara_cliente"];
					$this->riduzione_descrizione=$rows["riduzione_descrizione"];
					$this->iddara_avviso=$rows["iddara_avviso"];
					$this->riduzione_percentuale=$rows["riduzione_percentuale"];
				}				
			}
	
		}
		
	}

	function getRIDUZIONI($idCLIENTE)
	{
		global $db;
		$sSQL="select * from dara_tbl_riduzione where iddara_cliente='".$idCLIENTE."' ";
		$sOrder=" order by iddara_tbl_riduzione ";

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aRIDUZIONI=$db->select($sSQL);
		return $aRIDUZIONI;

	}


	function insertRIDUZIONE($riduzione_descrizione,$iddara_cliente,$iddara_avviso=0,$riduzione_percentuale=0)
	{
		global $db;

		$sSQL="insert into dara_tbl_riduzione (iddara_cliente,iddara_avviso,riduzione_descrizione,riduzione_percentuale) values('".$iddara_cliente."','".$iddara_avviso."','".$riduzione_descrizione."','".$riduzione_percentuale."')";

		$db->query($sSQL);
		return $db->insert_id();

	}

	function updateRIDUZIONE($iddara_avviso,$riduzione_descrizione,$iddara_cliente,$riduzione_percentuale=0)
	{
		global $db;

		$sSQL="update dara_tbl_riduzione set riduzione_descrizione='".$riduzione_descrizione."',iddara_cliente='".$iddara_cliente."',iddara_avviso='".$iddara_avviso."',riduzione_percentuale='".$riduzione_percentuale."' where iddara_tbl_riduzione='".$this->iddara_tbl_riduzione."'";

		$db->query($sSQL);
		

	}

	function deleteRIDUZIONE()
	{
		global $db;
		$sSQL="delete from dara_tbl_riduzione where iddara_tbl_riduzione='".$this->$iddara_tbl_riduzione."'";
		$db->query($sSQL);
	}	

}

class DARANazione {

	public $idnazione;
	public $nazione;

	public function __construct($idnazione=null) 
	{
		global $db;
		if(!empty($idnazione))
		{
			$this->idnazione=$idnazione;
			
			$sSQL="SELECT * FROM nazione WHERE idnazione='$idnazione'";
			$aNAZIONI=$db->select($sSQL);

			foreach ($aNAZIONI as $key => $rows) 
			{
				$this->idnazione=$rows["idnazione"];
				$this->nazione=$rows["nazione"];
			}	
		}
		
	}

	function getNAZIONI($condizione=NULL)
	{
		global $db;
		$sSQL="select * from nazione ";
		$sOrder=" order by nazione ";
		if (!empty($condizione))
			$sWhere=" where ".$condizione;

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aNAZIONI=$db->select($sSQL);
		return $aNAZIONI;
	}
}


class DARAOrganigramma {

	public $iddara_organigramma;
	public $iddara_cliente;
	public $organigramma_cognome;
	public $organigramma_nome;
	public $organigramma_codicefiscale;
	public $iddara_tbl_organigrammaruolo;
	public $organigramma_ruolo;
	public $organigramma_cellulare;
	public $organigramma_email;

	public function __construct($iddara_organigramma=null) 
	{
		global $db;
		if(!empty($iddara_organigramma))
		{
			$this->iddara_organigramma=$iddara_organigramma;
			
			$sSQL="SELECT * FROM dara_organigramma WHERE iddara_organigramma='$iddara_organigramma'";
			$aAVVISO=$db->select($sSQL);

			foreach ($aAVVISO as $key => $rows) 
			{
				$this->iddara_cliente=$rows["iddara_cliente"];
				$this->organigramma_cognome=$rows["organigramma_cognome"];
				$this->organigramma_nome=$rows["organigramma_nome"];
				$this->organigramma_codicefiscale=$rows["organigramma_codicefiscale"];
				$this->iddara_tbl_organigrammaruolo=$rows["iddara_tbl_organigrammaruolo"];			
				$this->organigramma_ruolo=getORGANIGRAMMARUOLO("iddara_tbl_organigrammaruolo='".$this->iddara_tbl_organigrammaruolo."'");			
				$this->organigramma_cellulare=$rows["organigramma_cellulare"];
				$this->organigramma_email=$rows["organigramma_email"];

			}	
		}
	}

	function getORGANIGRAMMA($condizione=NULL)
	{
		global $db;
		$sSQL="select * from dara_organigramma ";
		$sOrder=" order by iddara_cliente,organigramma_cognome,organigramma_nome ";

		$sWhere="";
		if (!empty($condizione))
			$sWhere=" where ".$condizione;

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aORGANIGRAMMA=$db->select($sSQL);
		return $aORGANIGRAMMA;
	}

	function insertORGANIGRAMMA($iddara_cliente,$organigramma_cognome,$organigramma_nome,$organigramma_codicefiscale,$iddara_tbl_organigrammaruolo=0,$organigramma_cellulare="",$organigramma_email="")
	{
		global $db;

		$sSQL="insert into dara_organigramma (iddara_cliente,organigramma_cognome,organigramma_nome,organigramma_codicefiscale,iddara_tbl_organigrammaruolo,organigramma_cellulare,organigramma_email) values('".$iddara_cliente."','".$organigramma_cognome."','".$organigramma_nome."','".$organigramma_codicefiscale."','".$iddara_tbl_organigrammaruolo."','".$organigramma_cellulare."','".$organigramma_email."')";

		$db->query($sSQL);
		return $db->insert_id();

	}

	function updateORGANIGRAMMA($iddara_cliente,$organigramma_cognome,$organigramma_nome,$organigramma_codicefiscale,$iddara_tbl_organigrammaruolo=0,$organigramma_cellulare="",$organigramma_email="")
	{
		global $db;

		$sSQL="update dara_organigramma set organigramma_cognome='".$organigramma_cognome."',iddara_cliente='".$iddara_cliente."',organigramma_nome='".$organigramma_nome."',organigramma_codicefiscale='".$organigramma_codicefiscale."',iddara_tbl_organigrammaruolo='".$iddara_tbl_organigrammaruolo."',organigramma_cellulare='".$organigramma_cellulare."',organigramma_email='".$organigramma_email."' where iddara_organigramma='".$this->iddara_organigramma."'";

		$db->query($sSQL);
	}

	function deleteORGANIGRAMMA()
	{
		global $db;
		$sSQL="delete from dara_organigramma where iddara_organigramma='".$this->$dara_organigramma."'";
		$db->query($sSQL);
	}	

}	

class DARAArticolo {

	public $iddara_articolo;
	public $iddara_cliente;
	public $iddara_tbl_merceologica;
	public $articolo_codice;
	public $articolo_descrizione;
	public $articolo_prezzo;
	public $articolo_note;

	public function __construct($iddara_articolo=null) 
	{
		global $db;
		if(!empty($iddara_articolo))
		{
			$this->iddara_articolo=$iddara_articolo;
			
			$sSQL="SELECT * FROM dara_articolo WHERE iddara_articolo='$iddara_articolo'";
			$aARTICOLO=$db->select($sSQL);

			foreach ($aARTICOLO as $key => $rows) 
			{
				$this->iddara_cliente=$rows["iddara_cliente"];
				$this->iddara_tbl_merceologica=$rows["iddara_tbl_merceologica"];
				$this->articolo_codice=$rows["articolo_codice"];
				$this->articolo_descrizione=$rows["articolo_descrizione"];
				$this->articolo_prezzo=$rows["articolo_prezzo"];
				$this->articolo_note=$rows["articolo_note"];
			}	
		}
	}

	function getARTICOLO($condizione=NULL)
	{
		global $db;
		$sSQL="select * from dara_articolo ";
		$sOrder=" order by iddara_cliente,articolo_descrizione ";
		if (!empty($condizione))
			$sWhere=" where ".$condizione;

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aARTICOLO=$db->select($sSQL);
		return $aARTICOLO;
	}

	function getARTICOLI($condizione=NULL)
	{
		global $db;
		$sSQL="select * from dara_articolo ";
		$sOrder=" order by iddara_articolo ";
		$sWhere="";
		if (!empty($condizione))
			$sWhere=" where ".$condizione;

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aARTICOLI=$db->select($sSQL);
		return $aARTICOLI;

	}

	function insertARTICOLO($iddara_cliente,$iddara_tbl_merceologica,$articolo_codice,$articolo_descrizione,$articolo_prezzo,$articolo_note="")
	{
		global $db;

		$sSQL="insert into dara_articolo (iddara_cliente,iddara_tbl_merceologica,articolo_codice,articolo_descrizione,articolo_prezzo,articolo_note) values('".$iddara_cliente."','".$iddara_tbl_merceologica."','".$articolo_codice."','".$articolo_descrizione."','".$articolo_prezzo."','".$articolo_note."')";

		$db->query($sSQL);
		return $db->insert_id();

	}

	function updateARTICOLO($iddara_cliente,$iddara_tbl_merceologica,$articolo_codice,$articolo_descrizione,$articolo_prezzo,$articolo_note="")
	{
		global $db;

		$sSQL="update dara_articolo set articolo_codice='".$articolo_codice."',iddara_cliente='".$iddara_cliente."',iddara_tbl_merceologica='".$iddara_tbl_merceologica."',articolo_descrizione='".$articolo_descrizione."',articolo_prezzo='".$articolo_prezzo."',articolo_note='".$articolo_note."' where iddara_articolo='".$this->iddara_articolo."'";

		$db->query($sSQL);
	}

	function deleteARTICOLO()
	{
		global $db;
		$sSQL="delete from dara_articolo where iddara_articolo='".$this->iddara_articolo."'";
		$db->query($sSQL);
	}	

}

class DARAMagazzino {

	public $iddara_magazzino;
	public $iddara_cliente;
	public $iddara_articolo;
	public $magazzino_eu;
	public $magazzino_quantita;
	public $magazzino_taglia;
	public $magazzino_note;
	public $iddara_anagrafica;
	public $magazzino_data;
	public $magazzino_ora;
	
	public $magazzino_operatore;

	public function __construct($iddara_magazzino=null) 
	{
		global $db;
		if(!empty($iddara_magazzino))
		{
			$this->iddara_magazzino=$iddara_magazzino;
			
			$sSQL="SELECT * FROM dara_magazzino WHERE iddara_magazzino='$iddara_magazzino'";
			$aMAGAZZINO=$db->select($sSQL);

			foreach ($aMAGAZZINO as $key => $rows) 
			{
				$this->iddara_cliente=$rows["iddara_cliente"];
				$this->iddara_articolo=$rows["iddara_articolo"];
				$this->magazzino_eu=$rows["magazzino_eu"];
				$this->magazzino_quantita=$rows["magazzino_quantita"];
				$this->magazzino_taglia=$rows["magazzino_taglia"];
				$this->magazzino_note=$rows["magazzino_note"];			

				$this->iddara_anagrafica=$rows["iddara_anagrafica"];
				$this->magazzino_data=$rows["magazzino_data"];
				$this->magazzino_ora=$rows["magazzino_ora"];
				$this->magazzino_operatore=$rows["magazzino_operatore"];			


			}	
		}
	}

	function getCARICO($condizione=NULL)
	{
		global $db;
		$sSQL="select * from dara_magazzino ";
		$sOrder=" order by iddara_magazzino ";
		if (!empty($condizione))
			$sWhere=" where ".$condizione." and magazzino_eu='E' ";
		else
			$sWhere=" where magazzino_eu='E'";

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aMAGAZZINO=$db->select($sSQL);
		return $aMAGAZZINO;
	}



	function insertCARICO($iddara_cliente,$iddara_articolo,$magazzino_quantita,$magazzino_taglia,$magazzino_note="")
	{
		global $db;
		$data=date("Y-m-d");
		$ora=date("H:i");
		$sSQL="insert into dara_magazzino (iddara_cliente,iddara_articolo,magazzino_quantita,magazzino_taglia,magazzino_note,magazzino_data,magazzino_ora,magazzino_operatore,magazzino_eu) values('".$iddara_cliente."','".$iddara_articolo."','".$magazzino_quantita."','".$magazzino_taglia."','".$magazzino_note."','".$data."','".$ora."','".$user."','E')";

		$db->query($sSQL);
		return $db->insert_id();

	}

	function updateCARICO($iddara_cliente,$iddara_articolo,$magazzino_quantita,$magazzino_taglia,$magazzino_note="")
	{
		global $db;

		// E' necessario verificare nel caso la quantità sia inferiore alla precedente
		$sSQL="update dara_magazzino set iddara_articolo='".$iddara_articolo."',iddara_cliente='".$iddara_cliente."',magazzino_quantita='".$magazzino_quantita."',magazzino_taglia='".$magazzino_taglia."',magazzino_note='".$magazzino_note."' where iddara_magazzino='".$this->iddara_magazzino."'";

		$db->query($sSQL);
	}

	function deleteCARICO()
	{
		global $db;
		// E' necessario verificare nel caso ci siano più scarichi dei carichi
		$sSQL="delete from dara_magazzino where iddara_magazzino='".$this->iddara_magazzino."'";
		$db->query($sSQL);
	}	

	function getSCARICO($condizione=NULL)
	{
		global $db;
		$sSQL="select * from dara_magazzino ";
		$sOrder=" order by iddara_magazzino ";
		$sWhere="";
		if (!empty($condizione))
			$sWhere=" where ".$condizione." and magazzino_eu='U' ";
		else
			$sWhere=" where magazzino_eu='U' ";


		$sSQL=$sSQL.$sWhere.$sOrder;
		$aMAGAZZINO=$db->select($sSQL);
		return $aMAGAZZINO;

	}

	function insertSCARICO($iddara_cliente,$iddara_articolo,$iddara_anagrafica,$magazzino_quantita,$magazzino_taglia,$magazzino_note="")
	{
		global $db;
		$data=date("Y-m-d");
		$ora=date("H:i");
		$sSQL="insert into dara_magazzino (iddara_cliente,iddara_articolo,iddara_anagrafica,magazzino_quantita,magazzino_taglia,magazzino_note,magazzino_data,magazzino_ora,magazzino_operatore,magazzino_eu) values('".$iddara_cliente."','".$iddara_articolo."','".$iddara_anagrafica."','".$magazzino_quantita."','".$magazzino_taglia."','".$magazzino_note."','".$data."','".$ora."','".$user."','U')";

		$db->query($sSQL);
		return $db->insert_id();

	}

	function updateSCARICO($iddara_cliente,$iddara_articolo,$iddara_anagrafica,$magazzino_quantita,$magazzino_taglia,$magazzino_note="")
	{
		global $db;

		// E' necessario verificare nel caso la quantità sia inferiore alla precedente
		$sSQL="update dara_magazzino set iddara_articolo='".$iddara_articolo."',iddara_cliente='".$iddara_cliente."',iddara_anagrafica='".$iddara_anagrafica."',magazzino_quantita='".$magazzino_quantita."',magazzino_taglia='".$magazzino_taglia."',magazzino_note='".$magazzino_note."' where iddara_magazzino='".$this->iddara_magazzino."'";

		$db->query($sSQL);
	}

	function deleteSCARICO()
	{
		global $db;
		
		$sSQL="delete from dara_magazzino where iddara_magazzino='".$this->iddara_magazzino."'";
		$db->query($sSQL);
	}	

	function getRIEPILOGO($condizione="")
	{
		global $db;
		$sSQL="select iddara_articolo,sum(magazzino_quantita) as carico from dara_magazzino where magazzino_eu='E' ";
		$sGroup=" group by iddara_articolo ";
		$sWhere="";
		if (!empty($condizione))
			$sWhere=" and ".$condizione;	

		$sSQL=$sSQL.$sWhere.$sOrder.$sGroup;
		$aCARICO=$db->select($sSQL);	

		$sSQL="select iddara_articolo,sum(magazzino_quantita) as scarico from dara_magazzino where magazzino_eu='U' ";
		$sWhere="";
		$sSQL=$sSQL.$sWhere.$sOrder.$sGroup;
		$aSCARICO=$db->select($sSQL);		

		$aRIEPILOGO=array();
		
		foreach ($aCARICO as $key => $aDATI) 
		{
			$aRIEPILOGO[$aDATI['iddara_articolo']]['E']=$aDATI['carico'];
			$aRIEPILOGO[$aDATI['iddara_articolo']]['SALDO']=$aDATI['carico'];
		}
		foreach ($aSCARICO as $key => $aDATI) 
		{
			$aRIEPILOGO[$aDATI['iddara_articolo']]['U']=$aDATI['scarico'];
			$aRIEPILOGO[$aDATI['iddara_articolo']]['SALDO']=$aRIEPILOGO[$aDATI['iddara_articolo']]['E']-$aDATI['scarico'];
		}	

		return $aRIEPILOGO;

	}

}

class DARAPagamento {

	public $iddara_pagamento;
	public $iddara_cliente;
	public $iddara_avviso;
	public $iddara_anagrafica;
	public $iddara_referente;
	public $pagamento_data;
	public $pagamento_importo;
	public $iddara_documentocontabile;
	public $iddara_tbl_pagamentomodalita;
	public $pagamento_note;

	public function __construct($iddara_pagamento=null) 
	{
		global $db;
		if(!empty($iddara_pagamento))
		{
			$this->iddara_pagamento=$iddara_pagamento;
			
			$sSQL="SELECT * FROM dara_pagamento WHERE iddara_pagamento='$iddara_pagamento'";
			$aPAGAMENTO=$db->select($sSQL);

			foreach ($aPAGAMENTO as $key => $rows) 
			{
				$this->iddara_cliente=$rows["iddara_cliente"];
				$this->iddara_avviso=$rows["iddara_avviso"];
				$this->iddara_anagrafica=$rows["iddara_anagrafica"];
				$this->iddara_referente=$rows["iddara_referente"];
				$this->pagamento_data=$rows["pagamento_data"];			
				$this->pagamento_importo=$rows["pagamento_importo"];
				$this->iddara_documentocontabile=$rows["iddara_documentocontabile"];
				$this->iddara_tbl_pagamentomodalita=$rows["iddara_tbl_pagamentomodalita"];
				$this->pagamento_note=$rows["pagamento_note"];
			}	
		}
	}

	function getPAGAMENTI($condizione=NULL)
	{
		global $db;
		$sSQL="select dara_pagamento.* from dara_pagamento inner join dara_anagrafica on dara_pagamento.iddara_anagrafica=dara_anagrafica.iddara_anagrafica ";
		$sOrder=" order by iddara_pagamento ";
		if (!empty($condizione))
			$sWhere=" where ".$condizione;
		$sOrder=" order by iddara_pagamento ";
		$sSQL=$sSQL.$sWhere.$sOrder;
		$aPAGAMENTI=$db->select($sSQL);
		return $aPAGAMENTI;
	}

	function insertPAGAMENTO($iddara_cliente,$iddara_avviso,$iddara_anagrafica,$iddara_referente,$pagamento_data,$pagamento_importo,$iddara_documentocontabile,$piddara_tbl_pagamentomodalita,$pagamento_note=null)
	{
		global $db;
		$data=date("Y-m-d");
		$ora=date("H:i");
		$sSQL="insert into dara_pagamento (iddara_cliente,iddara_avviso,iddara_anagrafica,iddara_referente,pagamento_data,pagamento_importo,iddara_documentocontabile,iddara_tbl_pagamentomodalita,pagamento_note) values('".$iddara_cliente."','".$iddara_avviso."','".$iddara_anagrafica."','".$iddara_referente."','".$pagamento_data."','".$pagamento_importo."','".$iddara_documentocontabile."','".$piddara_tbl_pagamentomodalita."','".$pagamento_note."')";

		$db->query($sSQL);
		return $db->insert_id();

	}

	function updatePAGAMENTO($iddara_cliente,$iddara_avviso,$iddara_anagrafica,$iddara_referente,$pagamento_data,$pagamento_importo,$iddara_documentocontabile,$piddara_tbl_pagamentomodalita,$pagamento_note=null)
	{
		global $db;

		
		$sSQL="update dara_pagamento set iddara_avviso='".$iddara_avviso."',iddara_cliente='".$iddara_cliente."',iddara_anagrafica='".$iddara_anagrafica."',iddara_referente='".$iddara_referente."',pagamento_importo='".$pagamento_importo."',pagamento_data='".$pagamento_data."',iddara_tbl_pagamentomodalita='".$piddara_tbl_pagamentomodalita."', pagamento_note='".$pagamento_note."' where iddara_pagamento='".$this->iddara_pagamento."'";

		$db->query($sSQL);
	}

	function deletePAGAMENTO()
	{
		global $db;
		
		$sSQL="delete from dara_pagamento where iddara_pagamento='".$this->iddara_pagamento."'";
		$db->query($sSQL);
	}	



}

class DARAComune {

	public $idcomune;
	public $comune;
	public $provincia;
	public $cap;
	public $belfiore;
	public $regione;
	public $istat;

	public function __construct($idcomune=null) 
	{
		global $db;
		if(!empty($idcomune))
		{
			$this->idcomune=$idcomune;
			
			$sSQL="SELECT * FROM comune WHERE idcomune='$idcomune'";
			$aCOMUNI=$db->select($sSQL);

			foreach ($aCOMUNI as $key => $rows) 
			{
				$this->idcomune=$rows["idcomune"];
				$this->comune=$rows["comune"];
				$this->provincia=$rows["provincia"];
				$this->cap=$rows["cap"];
				$this->belfiore=$rows["belfiore"];			

				$this->regione=$rows["regione"];
				$this->istat=$rows["istat"];				
							


			}	
		}
	}

	function getCOMUNI($condizione=NULL)
	{
		global $db;
		$sSQL="select * from comune ";
		$sOrder=" order by comune ";
		if (!empty($condizione))
			$sWhere=" where ".$condizione;

		$sSQL=$sSQL.$sWhere.$sOrder;
		$aCOMUNI=$db->select($sSQL);
		return $aCOMUNI;
	}
}	
	
?>
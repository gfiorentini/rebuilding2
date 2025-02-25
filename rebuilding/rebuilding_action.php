<?php
/*
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
*/
//error_reporting(-1);


//error_reporting(0);

require_once("./rebuilding_connect.php");
require_once("../librerie/dara.class.servizi.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/class.pagination.php");

global $db;

$paction=getPARAMETRO("_action");

switch ($paction) 
{

	case 'rlogin':

		$pusername=getPARAMETRO("_u");
		$ppassword=getPARAMETRO("_p");

  		$pusername=stripslashes($pusername); 
      	$ppassword=$db->escape_text($ppassword);

		$sSQL="select iddara_operatore from dara_operatore where operatore_username='$pusername' and operatore_password='$ppassword'";

		
		$iddara_operatore=$db->getVALUE($sSQL,'iddara_operatore');
		if ($iddara_operatore>0)
		{		
			if(!empty($_SERVER['HTTP_CLIENT_IP']))
			    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
			elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
			else
			    $ipaddress = $_SERVER['REMOTE_ADDR'];
			
			$chiaveRSA=generaCHIAVE($pusername,$ipaddress,$iddara_operatore);

			insertLOG($chiaveRSA,$ipaddress,$iddara_operatore,"login");

			insertSESSION($chiaveRSA);

			echo "1";
		}

		break;			
	case "inseriscianagrafica":
		$panagrafica_codicefiscale=getPARAMETRO("anagrafica_codicefiscale");
		$panagrafica_cognome=getPARAMETRO("anagrafica_cognome");
		$panagrafica_nome=getPARAMETRO("anagrafica_nome");
		$panagrafica_datanascita=getPARAMETRO("anagrafica_datanascita");
		$panagrafica_comunenascita=getPARAMETRO("anagrafica_comunenascita");
		$panagrafica_indirizzo=getPARAMETRO("anagrafica_indirizzo");
		$panagrafica_comuneresidenza=getPARAMETRO("anagrafica_comuneresidenza");
		$panagrafica_cellulare=getPARAMETRO("anagrafica_cellulare");
		$panagrafica_email=getPARAMETRO("anagrafica_email");

		$aRESPONSE=array();

		$sSQL="select iddara_anagrafica from dara_anagrafica where anagrafica_codicefiscale='$panagrafica_codicefiscale'";
		$iddara_anagrafica=$db->getVALUE($sSQL,'iddara_anagrafica');
		if(empty($iddara_anagrafica))
		{
			$insert="insert into dara_anagrafica (
				anagrafica_codicefiscale,
				anagrafica_cognome,
				anagrafica_nome,
				anagrafica_datanascita,
				anagrafica_comunenascita,
				anagrafica_indirizzo,
				anagrafica_comuneresidenza,
				anagrafica_cellulare,
				anagrafica_email
				) 
			values (
				'$panagrafica_codicefiscale',
				'$panagrafica_cognome',
				'$panagrafica_nome',
				'$panagrafica_datanascita',
				'$panagrafica_comunenascita',
				'$panagrafica_indirizzo',
				'$panagrafica_comuneresidenza',
				'$panagrafica_cellulare',
				'$panagrafica_email'
			)";

			$result=$db->query($insert);

			$aRESPONSE["status"]=1;
		}
		else
		{
			$aRESPONSE["status"]=0;
			$aRESPONSE["message"]="Il Codice Fiscale inserito è già presente in anagrafe.";
		}

		echo json_encode($aRESPONSE);

		break;

	case "loadanagrafica":
  		$anagrafica=new DARAAnagrafica(0);
		$aANAGRAFICHE=$anagrafica->getANAGRAFICHE();

		$loadoptions.="<option value=\"0\"></option>";

		foreach($aANAGRAFICHE as $anagrafica)
		{
			$loadoptions.="<option value=\"".$anagrafica["iddara_anagrafica"]."\">".stringXMLClean($anagrafica["anagrafica_cognome"]." ".$anagrafica["anagrafica_nome"])."</option>";
		}
		
		echo $loadoptions;
		break;

	case "loadnazioni":
		$pvalue=getPARAMETRO("value");
    	$pvalue=$db->escape_text($pvalue);

    	$query_value="";
    	if(!empty($pvalue))
    		$query_value=" WHERE nazione LIKE '%$pvalue%' ";

		$sSQL="SELECT * FROM nazione ".$query_value." ORDER BY nazione ASC";
		$aRESULT=$db->select($sSQL);

		foreach ($aRESULT as $key => $rows) 
		{
		    $nazione=str_replace("'","",$rows["nazione"]);

		    $aNAZIONI[$nazione]=(int) $rows["idnazione"];
		}	

		//array_walk_recursive($aNAZIONI, 'encode_items'); // http://stackoverflow.com/questions/3912930/applying-a-function-all-values-in-an-array
		$jNAZIONI=json_encode($aNAZIONI);

		echo $jNAZIONI;
		break;

	case "loadcomuni":
		$pvalue=getPARAMETRO("value");
    	$pvalue=$db->escape_text($pvalue);

    	$query_value="";
    	if(!empty($pvalue))
    		$query_value=" WHERE comune LIKE '%$pvalue%' ";

		$sSQL="SELECT * FROM comune ".$query_value." ORDER BY comune ASC";
		$aRESULT=$db->select($sSQL);

		foreach ($aRESULT as $key => $rows) 
		{
		    $comune=str_replace("'","",$rows["comune"]);

		    $aCOMUNI[$comune." (".$rows["provincia"].")"]=(int) $rows["idcomune"];
		}	

		array_walk_recursive($aCOMUNI, 'encode_items'); // http://stackoverflow.com/questions/3912930/applying-a-function-all-values-in-an-array
		$jCOMUNI=json_encode($aCOMUNI);

		echo $jCOMUNI;
		break;

	case "loadanagrafiche":
		$page=getPARAMETRO("page");
		$pquery=getPARAMETRO("query");

		$sWhere="";
		if(!empty($pquery))
		{
			$pquery=stripslashes($pquery);

			$aQUERY=json_decode($pquery,true);
			if(!empty($aQUERY["anagrafica_cognome"]))
			{
				$param=$aQUERY["anagrafica_cognome"];
    			$param=$db->escape_text($param);

				if(!empty($sWhere))
					$sWhere.=" AND ";

				$sWhere.=" dara_anagrafica.anagrafica_cognome LIKE '%$param%' ";
			}

			if(!empty($aQUERY["anagrafica_nome"]))
			{
				$param=$aQUERY["anagrafica_nome"];
    			$param=$db->escape_text($param);

				if(!empty($sWhere))
					$sWhere.=" AND ";
				
				$sWhere.=" dara_anagrafica.anagrafica_nome LIKE '%$param%' ";
			}
		}

		if(!empty($sWhere))
			$sWhere=" WHERE ".$sWhere;

		$sSQL="SELECT COUNT(*) AS numero FROM dara_anagrafica ".$sWhere;
		$records_count=$db->getVALUE($sSQL,'numero');

		$record_each_page=10;

		if(ceil($records_count/$record_each_page)<$page)	//Se la nuova ricerca restituisce meno pagine di risultati rispetto alla pagina in cui si è attualmente
			$page=1;
			
		$paging=new Pagination($records_count, $record_each_page, $page, 10);

		$sLimit=$paging->get_sql();
		$counter=$paging->get_counter();

		$sSQL="SELECT * FROM dara_anagrafica ".$sWhere." ORDER BY dara_anagrafica.anagrafica_cognome ASC, dara_anagrafica.anagrafica_nome ASC,dara_anagrafica.anagrafica_codicefiscale ASC ".$sLimit;
    	$aANAGRAFICHE=$db->select($sSQL);

    	if (@is_array($aANAGRAFICHE))
		{
        	foreach ($aANAGRAFICHE as $key => $aDATI) 
        	{
                $iddara_anagrafica=$aDATI["iddara_anagrafica"];
                $anagrafica=new DARAAnagrafica($iddara_anagrafica);

                $data[] = array(
					'iddara_anagrafica'=>$iddara_anagrafica,
					'counter'=>$counter,
					'anagrafica_cognome'=>$anagrafica->anagrafica_cognome,
					'anagrafica_nome'=>	$anagrafica->anagrafica_nome,
					'anagrafica_codicefiscale'=>$anagrafica->anagrafica_codicefiscale,
					'anagrafica_comuneresidenza'=>$anagrafica->anagrafica_comuneresidenza,
					'anagrafica_indirizzo'=>$anagrafica->anagrafica_indirizzo,
					'anagrafica_civico'=>$anagrafica->anagrafica_civico,
					'anagrafica_cellulare'=>$anagrafica->anagrafica_cellulare,
					'anagrafica_email'=>$anagrafica->anagrafica_email
				);

                $counter++;
    		}
		}

		$pagination_html=$paging->get_pagination();

		$output = array(
			'data'				=>	$data,
			'pagination'		=>	$pagination_html,
			'total_data'		=>	$records_count
		);

		echo json_encode($output);

		break;

	case "loadanagrafica_data":
		$piddara_anagrafica=getPARAMETRO("_id");

		$anagrafica=new DARAAnagrafica($piddara_anagrafica);

		$output = array(
			'anagrafica_cognome'=>$anagrafica->anagrafica_cognome,
	        'anagrafica_nome'=>$anagrafica->anagrafica_nome,
	        'anagrafica_datanascita'=>$anagrafica->anagrafica_datanascita,
	        'anagrafica_genere'=>$anagrafica->anagrafica_genere,
	        'anagrafica_nazionenascita'=>$anagrafica->anagrafica_nazionenascita,
	        'idnazione_nascita'=>$anagrafica->idnazione_nascita,
	        'anagrafica_nazionalita'=>$anagrafica->anagrafica_nazionalita,
	        'idnazionalita'=>$anagrafica->idnazionalita,
	        'anagrafica_comunenascita'=>$anagrafica->anagrafica_comunenascita,
	        'idcomune_nascita'=>$anagrafica->idcomune_nascita,
	        'anagrafica_provnascita'=>$anagrafica->anagrafica_provnascita,
	        'anagrafica_codicefiscale'=>$anagrafica->anagrafica_codicefiscale,
	        'anagrafica_comuneresidenza'=>$anagrafica->anagrafica_comuneresidenza,
	        'idcomune_residenza'=>$anagrafica->idcomune_residenza,
	        'anagrafica_provresidenza'=>$anagrafica->anagrafica_provresidenza,
	        'anagrafica_indirizzo'=>$anagrafica->anagrafica_indirizzo,
	        'anagrafica_civico'=>$anagrafica->anagrafica_civico,
	        'anagrafica_cellulare'=>$anagrafica->anagrafica_cellulare,
	        'anagrafica_email'=>$anagrafica->anagrafica_email
	    );

		echo json_encode($output);

		break;
	case "accessoAUTHSERVICE":
		$ptype=getPARAMETRO("_type");
		$pidgen_procedura=getPARAMETRO("_idprocedura");
		
		$link="";
		switch($ptype)
		{
			case "SPID":
				$pidp=getPARAMETRO("_idp");
				
				if(!empty($pidp))
				{
					
					$urlSpid="https://spid.comune-online.it/AuthServiceSPID/auth.jsp";
					
					if($_SERVER["HTTP_HOST"]=="rebuilding.regione.marche.it")
						$backUrl="https://rebuilding.regione.marche.it/rebuilding/spid_auth_rebuilding.php?params={authId}";
					else
						$backUrl="http://localhost:8082/rebuilding/spid_auth_rebuilding.php?params={authId}";
						// $backUrl="https://rebuilding.sicare.it/rebuilding/spid_auth_rebuilding.php?params={authId}";

					// echo $backUrl ;

					$serviceProvider="regionemarche";
					$authLevel='https://www.spid.gov.it/SpidL2';
					$serviceIndex='1';

					if(!empty($urlSpid) && !empty($backUrl) && !empty($serviceProvider) && $authLevel!=null && $serviceIndex!=null)
					{
						include_once("../librerie/class.spid.php");
					
						$spid=new spid();
						$res=$spid->getAuthId();

						$authid=$res["response"]["getAuthIdReturn"];
						
						if(!empty($authid))
						{

							$backUrl=str_replace("{authId}",$authid,$backUrl);
							$backUrl=str_replace("{procedura}",$pidgen_procedura,$backUrl);
							
							$backUrl=urlencode($backUrl);

							$link=$urlSpid.'?backUrl='.$backUrl.'&authSystem=spid&authId='.$authid.'&serviceProvider='.$serviceProvider.'&authLevel='.$authLevel.'&idp='.$pidp.'&serviceIndex='.$serviceIndex;
							$link=base64_encode($link);
						}
					}
				}
			break;

			default:
			break;
		}
		if(!empty($link))
			echo $link;
		else
			echo "0";
		break;
	case "loadoperatori":
		$page=getPARAMETRO("page");
		$pquery=getPARAMETRO("query");

		$sWhere="";
		if(!empty($pquery))
		{
			$pquery=stripslashes($pquery);

			$aQUERY=json_decode($pquery,true);
			if(!empty($aQUERY["operatore_nominativo"]))
			{
				$param=$aQUERY["operatore_nominativo"];
    			$param=$db->escape_text($param);

				if(!empty($sWhere))
					$sWhere.=" AND ";

				$sWhere.=" (CONCAT_WS(' ',dara_operatore.operatore_cognome,dara_operatore.operatore_nome) like '%$param%') ";
			}
		}

		if(!empty($sWhere))
			$sWhere=" WHERE ".$sWhere;

		$sSQL="SELECT COUNT(*) AS numero FROM dara_operatore ".$sWhere;
		$records_count=$db->getVALUE($sSQL,'numero');
		if(empty($records_count))
			$records_count=0;
		
		$record_each_page=10;

		if(ceil($records_count/$record_each_page)<$page)	//Se la nuova ricerca restituisce meno pagine di risultati rispetto alla pagina in cui si è attualmente
			$page=1;
			
		$paging=new Pagination($records_count, $record_each_page, $page, 10);

		$sLimit=$paging->get_sql();
		$counter=$paging->get_counter();

		$sSQL="SELECT * FROM dara_operatore ".$sWhere." ORDER BY operatore_cognome ASC, operatore_nome ASC ".$sLimit;
    	$aOPERATORI=$db->select($sSQL);

    	$data=array();
    	
    	if (@is_array($aOPERATORI))
		{
        	foreach ($aOPERATORI as $key => $aDATI) 
        	{
                $iddara_operatore=$aDATI["iddara_operatore"];
                $operatore_cognome=$aDATI["operatore_cognome"];
                $operatore_nome=$aDATI["operatore_nome"];
                $operatore_codicefiscale=$aDATI["operatore_codicefiscale"];
                $operatore_email=$aDATI["operatore_email"];
                $operatore_flagabilitato=$aDATI["operatore_flagabilitato"];

                $data[] = array(
					'iddara_operatore'=>$iddara_operatore,
					'counter'=>$counter,
					'operatore_cognome'=>$operatore_cognome,
					'operatore_nome'=>$operatore_nome,
					'operatore_codicefiscale'=>$operatore_codicefiscale,
					'operatore_email'=>$operatore_email,
					'operatore_flagabilitato'=>$operatore_flagabilitato
				);

                $counter++;
    		}
		}

		$pagination_html=$paging->get_pagination();

		$output = array(
			'data'				=>	$data,
			'pagination'		=>	$pagination_html,
			'total_data'		=>	$records_count
		);

		echo json_encode($output);

		break;
	case "deletedocumentoflusso":
		$pidrebuilding_flussofinanziario_documento=getPARAMETRO("_documento");
		$pidrebuilding_flussofinanziario_documento=$db->escape_text($pidrebuilding_flussofinanziario_documento);

		$sSQL="delete from rebuilding_flussofinanziario_documento where idrebuilding_flussofinanziario_documento='$pidrebuilding_flussofinanziario_documento'";
		$db->query($sSQL);

		break;
	case "deleteintervento":
		$pidrebuilding_rendicontazione_intervento=getPARAMETRO("_intervento");
		$pidrebuilding_rendicontazione_intervento=$db->escape_text($pidrebuilding_rendicontazione_intervento);

		echo $sSQL="delete from rebuilding_rendicontazione_intervento where idrebuilding_rendicontazione_intervento='$pidrebuilding_rendicontazione_intervento'";
		$db->query($sSQL);

		break;		
	case "getSCADENZE":
		$pidrebuilding_flussofinanziario=getPARAMETRO("_RENDICONTAZIONE");
		$pidrebuilding_flussofinanziario=$db->escape_text($pidrebuilding_flussofinanziario);
		$sWhere=" where idrebuilding_flussofinanziario='$pidrebuilding_flussofinanziario'";

		$sSQL="SELECT * FROM rebuilding_scadenzario ".$sWhere." order by idrebuilding_scadenzario ";
		$aSCADENZE=$db->select($sSQL);      
		echo $jSCADENZE=json_encode($aSCADENZE);

		/*
        foreach ($aSCADENZE as $key => $aDATI) 
        {
          $idrebuilding_scadenzario=$aDATI["idrebuilding_scadenzario"];
          $scadenza_data=$aDATI["scadenza_data"];
          $scadenza_ora=$aDATI["scadenza_ora"];
          $scadenza_testo=$aDATI["scadenza_testo"];
          $scadenza_stato=$aDATI["scadenza_stato"];
        }  
        */

		break;	
	case "saveTIPOLOGIASPESA":
		$pidrebuilding_tipologiaspesa=getPARAMETRO("_k");
		$pidrebuilding_tipologiaspesa=$db->escape_text($pidrebuilding_tipologiaspesa);
		$ptipologiaspesa_descrizione=getPARAMETRO("_value");
		$ptipologiaspesa_descrizione=$db->escape_text($ptipologiaspesa_descrizione);

		if(empty($pidrebuilding_tipologiaspesa))
			$sSQL="insert into rebuilding_tipologiaspesa (tipologiaspesa_descrizione) values('".$ptipologiaspesa_descrizione."') ";		
		else
			$sSQL="update rebuilding_tipologiaspesa set tipologiaspesa_descrizione='".$ptipologiaspesa_descrizione."' where idrebuilding_tipologiaspesa='".$pidrebuilding_tipologiaspesa."' ";		

		;
		$db->query($sSQL);

		break;		
	case "saveINTERVENTO":
		$pidrebuilding_flussofinanziario_intervento=getPARAMETRO("_k");
		$pidrebuilding_flussofinanziario_intervento=$db->escape_text($pidrebuilding_flussofinanziario_intervento);
		$pintervento_codice=stripslashes(getPARAMETRO("_codice"));
		$pintervento_codice=$db->escape_text($pintervento_codice);
		$pintervento_titolo=stripslashes(getPARAMETRO("_titolo"));
		$pintervento_titolo=$db->escape_text($pintervento_titolo);
		$pintervento_descrizione=stripslashes(getPARAMETRO("_descrizione"));
		$pintervento_descrizione=$db->escape_text($pintervento_descrizione);
		$pintervento_areaintervento=stripslashes(getPARAMETRO("_area"));
		$pintervento_areaintervento=$db->escape_text($pintervento_areaintervento);
		$pidrebuilding_tipologiaspesa=getPARAMETRO("_spesa");
		$pidrebuilding_tipologiaspesa=$db->escape_text($pidrebuilding_tipologiaspesa);

		$pidrebuilding_flussofinanziario=getPARAMETRO("_flusso");
		$pidrebuilding_flussofinanziario=$db->escape_text($pidrebuilding_flussofinanziario);

		$sSQL="SELECT COUNT(*) AS numero FROM rebuilding_flussofinanziario_intervento WHERE intervento_codice='$pintervento_codice' AND idrebuilding_tipologiaspesa='$pidrebuilding_tipologiaspesa' AND idrebuilding_flussofinanziario='$pidrebuilding_flussofinanziario' AND intervento_flagelimina=0";
		$records_count=$db->getVALUE($sSQL,'numero');
		if($records_count>1)
			echo "0";
		else
		{
			if(empty($pidrebuilding_flussofinanziario_intervento))
				$sSQL="INSERT INTO rebuilding_flussofinanziario_intervento (idrebuilding_flussofinanziario,intervento_codice,intervento_titolo,intervento_descrizione,idrebuilding_tipologiaspesa) VALUES('".$pidrebuilding_flussofinanziario."','".$pintervento_codice."','".$pintervento_titolo."','".$pintervento_descrizione."','".$pidrebuilding_tipologiaspesa."') ";		
			else
				$sSQL="update rebuilding_flussofinanziario_intervento set intervento_codice='".$pintervento_codice."',intervento_titolo='".$pintervento_titolo."',intervento_descrizione='".$pintervento_descrizione."',idrebuilding_tipologiaspesa='".$pidrebuilding_tipologiaspesa."' where idrebuilding_flussofinanziario_intervento='".$pidrebuilding_flussofinanziario_intervento."' ";		

		
			$db->query($sSQL);
			echo "1";
		}
		

		break;	
	case "deletedocumentoliquidazione":
		$pidrebuilding_flussofinanziario_liquidazione_documento=getPARAMETRO("_documento");
		$pidrebuilding_flussofinanziario_liquidazione_documento=$db->escape_text($pidrebuilding_flussofinanziario_liquidazione_documento);

		$sSQL="DELETE from rebuilding_flussofinanziario_liquidazione_documento where idrebuilding_flussofinanziario_liquidazione_documento='$pidrebuilding_flussofinanziario_liquidazione_documento'";
		$db->query($sSQL);

		break;
	case "unset_user_front":
		/*
		ini_set('display_errors', '1');
		ini_set('display_startup_errors', '1');
		error_reporting(E_ALL);
		*/
		$plibrerie=getPARAMETRO("librerie");

		// $backUrl="https://rebuilding.regione.marche.it/rebuilding/login";
		$backUrl="http://localhost:8082/rebuilding/login";

		$data=date("Y-m-d");
		$ora=date("H:i:s");
		$ip=$_SERVER['REMOTE_ADDR'];
		//$ip = tosql($ip,'Text');
			
		//$db->query("INSERT INTO log_utente (ip,data,ora,chiave_front,idutente,attivita) VALUES ($ip,$data,$ora,'$chiave_front','$fldidgen_utente','logout')");

		// setcookie('iccsuser_front', null , time()+18000 , "/", $_SERVER['HTTP_HOST'], false, true);
		setcookie('iccsuser_front', null , time()+18000 , "/",false, true);
		session_destroy();
		session_unset();
		
		
		if(isset($_COOKIE['authservice_authidSPID']) || isset($_COOKIE['authservice_authidCNS']) || isset($_COOKIE['authservice_authidCIE']))
		{

			$urlRedirect="";
			
			if(isset($_COOKIE['authservice_authidSPID']))
			{
				$authid=$_COOKIE['authservice_authidSPID'];

				if(!empty($authid))
				{
					require_once("../librerie/class.spid.php");

					$spid=new spid();
					$result=$spid->singleSignOut($authid);

					//print_r_formatted($result);
					if(!empty($result["response"]["singleSignOutReturn"]))
					{
						// setcookie('authservice_authidSPID', null , time()+18000 , "/", domain: $_SERVER['HTTP_HOST'], secure: false, httponly: true);
						setcookie('authservice_authidSPID', null , time()+18000 , "/",  secure: false, httponly: true);
						$urlRedirect=$result["response"]["singleSignOutReturn"];
					}
				}
			}
			elseif(isset($_COOKIE['authservice_authidCNS']))
			{
				$authid=$_COOKIE['authservice_authidCNS'];


				if(!empty($authid))
				{
					require_once("../librerie/class.cns.php");

					$cns=new CNS();
					$result=$cns->singleSignOut($authid);

					//print_r_formatted($result);
					if(!empty($result["response"]["singleSignOutReturn"]))
					{
						// setcookie('authservice_authidCNS', null , time()+18000 , "/", $_SERVER['HTTP_HOST'], false, true);
						setcookie('authservice_authidCNS', null , time()+18000 
						, "/", false, true);
						$urlRedirect=$result["response"]["singleSignOutReturn"];
					}
				}
			}
			elseif(isset($_COOKIE['authservice_authidCIE']))
			{
				$authid=$_COOKIE['authservice_authidCIE'];


				if(!empty($authid))
				{
					require_once("../librerie/class.cie.php");

					$cie=new cie();
					$result=$cie->singleSignOut($authid);

					//print_r_formatted($result);
					if(!empty($result["response"]["singleSignOutReturn"]))
					{
						// setcookie('authservice_authidCIE', null , time()+18000 , "/", $_SERVER['HTTP_HOST'], false, true);
						setcookie('authservice_authidCIE', null , time()+18000 , "/",  false, true);
						$urlRedirect=$result["response"]["singleSignOutReturn"];
					}
				}
			}
			
			if(!empty($urlRedirect))
				echo "1|".$urlRedirect."&backUrl=".$backUrl;
			else
				echo "1|".$backUrl;	
		}
		else
			echo "1|".$backUrl;

	break;
	default:
		break;
}

?>
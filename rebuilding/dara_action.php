<?php
/*
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
*/

error_reporting(0);

require_once("./rebuilding_connect.php");
require_once("../librerie/dara.class.servizi.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/class.pagination.php");

global $db;

$paction=getPARAMETRO("_action");

switch ($paction) 
{
	case 'daralogin':

		$pusername=getPARAMETRO("_u");
		$ppassword=getPARAMETRO("_p");

  		$pusername=stripslashes($pusername); 
      	$ppassword=$db->escape_text($ppassword);

		$sSQL="select iddara_operatore from dara_operatore where operatore_username='$pusername' and operatore_password='$ppassword'";
		$iddara_operatore=$db->getVALUE($sSQL,'iddara_operatore');
		if ($iddara_operatore>0 && !empty($pusername) && !empty($ppassword))
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
		elseif(!empty($pusername) && !empty($ppassword))
		{
			// verifico se è un cittadino
			$sSQL="select iddara_anagrafica from dara_anagrafica where anagrafica_username='$pusername' and anagrafica_password='$ppassword' and anagrafica_abilita_accesso=1";
			$iddara_anagrafica=$db->getVALUE($sSQL,'iddara_anagrafica');
			if ($iddara_anagrafica>0)
			{		
				if(!empty($_SERVER['HTTP_CLIENT_IP']))
				    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
				elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
				    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
				else
				    $ipaddress = $_SERVER['REMOTE_ADDR'];
				
				$chiaveRSA=generaCHIAVE($pusername,$ipaddress,$iddara_anagrafica);

				insertLOG($chiaveRSA,$ipaddress,$iddara_anagrafica,"login");

				insertESESSION($chiaveRSA);

				echo "2";
			}
		}
		break;
	case 'wlogin':

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
	case 'rlogin':

		$pusername=getPARAMETRO("_u");
		$ppassword=getPARAMETRO("_p");

  		$pusername=stripslashes($pusername); 
      	$ppassword=$db->escape_text($ppassword);

		echo $sSQL="select iddara_operatore from dara_operatore where operatore_username='$pusername' and operatore_password='$ppassword'";
		die;
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
	case "domandadichiarazione":
		$piddara_domanda=getPARAMETRO("_k");
		$piddara_avviso_dichiarazione=getPARAMETRO("_dichiarazione");
		$pvalue=getPARAMETRO("_value");

		$domanda = new DARADomanda($piddara_domanda);
		$domanda->saveDICHIARAZIONE($piddara_avviso_dichiarazione,$pvalue);
		break;
	case "domandapresenza":
		$piddara_domanda=getPARAMETRO("_domanda");
		$ppresenza_data=getPARAMETRO("_data");
		$pvalue=getPARAMETRO("_value");
		$domanda=new DARADomanda($piddara_domanda);
		$domanda->savePRESENZA($ppresenza_data,$pvalue);
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

	case "loaddomande":
		$page=getPARAMETRO("page");
		$pquery=getPARAMETRO("query");

		$sWhere="";
		if(!empty($pquery))
		{
			$pquery=stripslashes($pquery);

			$aQUERY=json_decode($pquery,true);
			if(!empty($aQUERY["domanda_richiedente"]))
			{
				$param=$aQUERY["domanda_richiedente"];
    			$param=$db->escape_text($param);

				if(!empty($sWhere))
					$sWhere.=" AND ";

				$sWhere.=" (CONCAT_WS(' ',richiedente.anagrafica_cognome,richiedente.anagrafica_nome) like '%$param%') ";
			}

			if(!empty($aQUERY["domanda_presentante"]))
			{
				$param=$aQUERY["domanda_presentante"];
    			$param=$db->escape_text($param);

				if(!empty($sWhere))
					$sWhere.=" AND ";
				
				$sWhere.=" (CONCAT_WS(' ',presentante.anagrafica_cognome,presentante.anagrafica_nome) like '%$param%') ";
			}
		}

		if(!empty($sWhere))
			$sWhere=" WHERE ".$sWhere;

		$sSQL="SELECT COUNT(*) AS numero 
		FROM dara_domanda 
		INNER JOIN dara_anagrafica presentante ON dara_domanda.iddara_presentetada=presentante.iddara_anagrafica 
		INNER JOIN dara_anagrafica richiedente ON dara_domanda.iddara_richiedente=richiedente.iddara_anagrafica ".$sWhere;
		$records_count=$db->getVALUE($sSQL,'numero');

		$record_each_page=10;

		if(ceil($records_count/$record_each_page)<$page)	//Se la nuova ricerca restituisce meno pagine di risultati rispetto alla pagina in cui si è attualmente
			$page=1;
			
		$paging=new Pagination($records_count, $record_each_page, $page, 10);

		$sLimit=$paging->get_sql();
		$counter=$paging->get_counter();

		$sSQL="SELECT * 
		FROM dara_domanda 
		INNER JOIN dara_anagrafica presentante ON dara_domanda.iddara_presentetada=presentante.iddara_anagrafica 
		INNER JOIN dara_anagrafica richiedente ON dara_domanda.iddara_richiedente=richiedente.iddara_anagrafica 
		".$sWhere." 
		ORDER BY richiedente.anagrafica_cognome ASC, richiedente.anagrafica_nome ASC ".$sLimit;
    	$aDOMANDE=$db->select($sSQL);

    	if (@is_array($aDOMANDE))
		{
        	foreach ($aDOMANDE as $key => $aDATI) 
        	{
        		$iddara_avviso=$aDATI["iddara_avviso"];
	            $avviso=new DARAAvviso($iddara_avviso);   

	            $iddara_richiedente=$aDATI["iddara_richiedente"];
	            $richiedente=new DARAAnagrafica($iddara_richiedente);

	            $iddara_presentetada=$aDATI["iddara_presentetada"];
	            $presentante=new DARAAnagrafica($iddara_presentetada);

	            $data[] = array(
					'iddara_domanda'=>$aDATI["iddara_domanda"],
					'counter'=>$counter,
					'avviso_titolo'=>$avviso->avviso_titolo,
					'datainvio'=>	dataitaliana($aDATI["domanda_datainvio"]),
					'presentante_cognome'=>$presentante->anagrafica_cognome,
					'presentante_nome'=>$presentante->anagrafica_nome,
					'richiedente_cognome'=>$richiedente->anagrafica_cognome,
					'richiedente_nome'=>$richiedente->anagrafica_nome,
					'anagrafica_cellulare'=>$richiedente->anagrafica_cellulare
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

	
	case "loadorganigrammi":
		$page=getPARAMETRO("page");
		$pquery=getPARAMETRO("query");

		$sWhere="";
		if(!empty($pquery))
		{
			$pquery=stripslashes($pquery);

			$aQUERY=json_decode($pquery,true);
			if(!empty($aQUERY["organigramma_nominativo"]))
			{
				$param=$aQUERY["organigramma_nominativo"];
    			$param=$db->escape_text($param);

				if(!empty($sWhere))
					$sWhere.=" AND ";

				$sWhere.=" (CONCAT_WS(' ',organigramma_cognome,organigramma_nome) like '%$param%') ";
			}

			if(!empty($aQUERY["organigramma_societa"]))
			{
				$param=$aQUERY["organigramma_societa"];
    			$param=$db->escape_text($param);

				if(!empty($sWhere))
					$sWhere.=" AND ";
				
				$sWhere.=" iddara_cliente='$param'";
			}
		}

		if(!empty($sWhere))
			$sWhere=" WHERE ".$sWhere;

		$sSQL="SELECT COUNT(*) AS numero 
		FROM dara_organigramma ".$sWhere;
		$records_count=$db->getVALUE($sSQL,'numero');

		$record_each_page=10;

		if(ceil($records_count/$record_each_page)<$page)	//Se la nuova ricerca restituisce meno pagine di risultati rispetto alla pagina in cui si è attualmente
			$page=1;
			
		$paging=new Pagination($records_count, $record_each_page, $page, 10);

		$sLimit=$paging->get_sql();
		$counter=$paging->get_counter();

		$sSQL="SELECT * 
		FROM dara_organigramma 
		".$sWhere." 
		ORDER BY organigramma_cognome ASC, organigramma_nome ASC ".$sLimit;
    	$aORGANIGRAMMI=$db->select($sSQL);

    	if (@is_array($aORGANIGRAMMI))
		{
        	foreach ($aORGANIGRAMMI as $key => $aDATI) 
        	{
        		$iddara_organigramma=$aDATI["iddara_organigramma"];
                $organigramma=new DARAOrganigramma($iddara_organigramma);
                $idsocieta=$organigramma->iddara_cliente;
                $societa=new DARACliente($idsocieta);

	            $data[] = array(
					'iddara_organigramma'=>$aDATI["iddara_organigramma"],
					'counter'=>$counter,
					'iddara_cliente'=>$organigramma->iddara_cliente,
					'iddara_tbl_organigrammaruolo'=>$organigramma->organigramma_ruolo[0]['iddara_tbl_organigrammaruolo'],
					'organigramma_societa'=>$societa->cliente_nominativo,
					'organigramma_cognome'=>$organigramma->organigramma_cognome,
					'organigramma_nome'=>$organigramma->organigramma_nome,
					'organigramma_codicefiscale'=>$organigramma->organigramma_codicefiscale,
					'organigramma_ruolo'=>$organigramma->organigramma_ruolo[0]["ruolo_descrizione"],
					'organigramma_cellulare'=>$organigramma->organigramma_cellulare,
					'organigramma_email'=>$organigramma->organigramma_email
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

	case "savestep1":

        $piddara_domanda=getPARAMETRO("iddara_domanda");
        $piddara_avviso=getPARAMETRO("iddara_avviso");
        $piddara_presentante=getPARAMETRO("iddara_presentante");

        //dati richiedente
        $ppresentante_cognome=getPARAMETRO("presentante_cognome");
    	$ppresentante_cognome=$db->escape_text($ppresentante_cognome);

        $ppresentante_nome=getPARAMETRO("presentante_nome");
    	$ppresentante_nome=$db->escape_text($ppresentante_nome);

        $ppresentante_datanascita=getPARAMETRO("presentante_datanascita");
    	$ppresentante_datanascita=$db->escape_text($ppresentante_datanascita);

        $ppresentante_genere=getPARAMETRO("presentante_genere");
    	$ppresentante_genere=$db->escape_text($ppresentante_genere);

        $ppresentante_nazionenascita=getPARAMETRO("presentante_nazionenascita");
    	$ppresentante_nazionenascita=$db->escape_text($ppresentante_nazionenascita);

        $pidnazione_nascita_presentante=getPARAMETRO("idnazione_nascita_presentante");
    	$pidnazione_nascita_presentante=$db->escape_text($pidnazione_nascita_presentante);

        $ppresentante_nazionalita=getPARAMETRO("presentante_nazionalita");
    	$ppresentante_nazionalita=$db->escape_text($ppresentante_nazionalita);

        $pidnazionalita_presentante=getPARAMETRO("idnazionalita_presentante");
    	$pidnazionalita_presentante=$db->escape_text($pidnazionalita_presentante);

        $ppresentante_comunenascita=getPARAMETRO("presentante_comunenascita");
    	$ppresentante_comunenascita=$db->escape_text($ppresentante_comunenascita);

        $pidcomune_nascita_presentante=getPARAMETRO("idcomune_nascita_presentante");
    	$pidcomune_nascita_presentante=$db->escape_text($pidcomune_nascita_presentante);

        $ppresentante_provnascita=getPARAMETRO("presentante_provnascita");
    	$ppresentante_provnascita=$db->escape_text($ppresentante_provnascita);

        $ppresentante_codicefiscale=getPARAMETRO("presentante_codicefiscale");
    	$ppresentante_codicefiscale=$db->escape_text($ppresentante_codicefiscale);

        $ppresentante_comuneresidenza=getPARAMETRO("presentante_comuneresidenza");
    	$ppresentante_comuneresidenza=$db->escape_text($ppresentante_comuneresidenza);

        $pidcomune_residenza_presentante=getPARAMETRO("idcomune_residenza_presentante");
    	$pidcomune_residenza_presentante=$db->escape_text($pidcomune_residenza_presentante);
        
        $ppresentante_provresidenza=getPARAMETRO("presentante_provresidenza");
    	$ppresentante_provresidenza=$db->escape_text($ppresentante_provresidenza);
        
        $ppresentante_indirizzo=getPARAMETRO("presentante_indirizzo");
    	$ppresentante_indirizzo=$db->escape_text($ppresentante_indirizzo);
        
        $ppresentante_civico=getPARAMETRO("presentante_civico");
    	$ppresentante_civico=$db->escape_text($ppresentante_civico);
        
        $ppresentante_cellulare=getPARAMETRO("presentante_cellulare");
    	$ppresentante_cellulare=$db->escape_text($ppresentante_cellulare);
       
        $ppresentante_email=getPARAMETRO("presentante_email");
    	$ppresentante_email=$db->escape_text($ppresentante_email);

    	if(!empty($piddara_presentante))
    	{
	        $sSQL="UPDATE dara_anagrafica SET 
	            anagrafica_codicefiscale='$ppresentante_codicefiscale',
	            anagrafica_cognome='$ppresentante_cognome',
	            anagrafica_nome='$ppresentante_nome',
	            anagrafica_genere='$ppresentante_genere',
	            anagrafica_datanascita='$ppresentante_datanascita',
	            idnazionalita='$pidnazionalita_presentante',
	            idnazione_nascita='$pidnazione_nascita_presentante',
	            anagrafica_nazionenascita='$ppresentante_nazionenascita',
	            idcomune_nascita='$pidcomune_nascita_presentante',
	            anagrafica_comunenascita='$ppresentante_comunenascita',
	            anagrafica_provnascita='$ppresentante_provnascita',
	            anagrafica_indirizzo='$ppresentante_indirizzo',
	            anagrafica_civico='$ppresentante_civico',
	            idcomune_residenza='$pidcomune_residenza_presentante',
	            anagrafica_comuneresidenza='$ppresentante_comuneresidenza',
	            anagrafica_provresidenza='$ppresentante_provresidenza',
	            anagrafica_cellulare='$ppresentante_cellulare',
	            anagrafica_email='$ppresentante_email' 
	        WHERE iddara_anagrafica='$piddara_presentante'";
	        $db->query($sSQL);
	    }
	    
        $pflag_tipo_richiedente=getPARAMETRO("flag_tipo_richiedente");

        if($pflag_tipo_richiedente==2)
        {
            //dati iscritto
            $piscritto_cognome=getPARAMETRO("iscritto_cognome");
    		$piscritto_cognome=$db->escape_text($piscritto_cognome);

            $piscritto_nome=getPARAMETRO("iscritto_nome");
    		$piscritto_nome=$db->escape_text($piscritto_nome);
    		
            $piscritto_datanascita=getPARAMETRO("iscritto_datanascita");
    		$piscritto_datanascita=$db->escape_text($piscritto_datanascita);
    		
            $piscritto_genere=getPARAMETRO("iscritto_genere");
    		$piscritto_genere=$db->escape_text($piscritto_genere);
    		
            $piscritto_nazionenascita=getPARAMETRO("iscritto_nazionenascita");
    		$piscritto_nazionenascita=$db->escape_text($piscritto_nazionenascita);
    		
            $pidnazione_nascita_iscritto=getPARAMETRO("idnazione_nascita_iscritto");
    		$pidnazione_nascita_iscritto=$db->escape_text($pidnazione_nascita_iscritto);
    		
            $piscritto_nazionalita=getPARAMETRO("iscritto_nazionalita");
    		$piscritto_nazionalita=$db->escape_text($piscritto_nazionalita);
    		
            $pidnazionalita_iscritto=getPARAMETRO("idnazionalita_iscritto");
    		$pidnazionalita_iscritto=$db->escape_text($pidnazionalita_iscritto);
    		
            $piscritto_comunenascita=getPARAMETRO("iscritto_comunenascita");
    		$piscritto_comunenascita=$db->escape_text($piscritto_comunenascita);
    		
            $pidcomune_nascita_iscritto=getPARAMETRO("idcomune_nascita_iscritto");
    		$pidcomune_nascita_iscritto=$db->escape_text($pidcomune_nascita_iscritto);
    		
            $piscritto_provnascita=getPARAMETRO("iscritto_provnascita");
    		$piscritto_provnascita=$db->escape_text($piscritto_provnascita);
    		
            $piscritto_codicefiscale=getPARAMETRO("iscritto_codicefiscale");
    		$piscritto_codicefiscale=$db->escape_text($piscritto_codicefiscale);
    		
            $piscritto_comuneresidenza=getPARAMETRO("iscritto_comuneresidenza");
    		$piscritto_comuneresidenza=$db->escape_text($piscritto_comuneresidenza);
    		
            $pidcomune_residenza_iscritto=getPARAMETRO("idcomune_residenza_iscritto");
    		$pidcomune_residenza_iscritto=$db->escape_text($pidcomune_residenza_iscritto);
    		
            $piscritto_provresidenza=getPARAMETRO("iscritto_provresidenza");
    		$piscritto_provresidenza=$db->escape_text($piscritto_provresidenza);
    		
            $piscritto_indirizzo=getPARAMETRO("iscritto_indirizzo");
    		$piscritto_indirizzo=$db->escape_text($piscritto_indirizzo);
    		
            $piscritto_civico=getPARAMETRO("iscritto_civico");
    		$piscritto_civico=$db->escape_text($piscritto_civico);
    		
            $piscritto_cellulare=getPARAMETRO("iscritto_cellulare");
    		$piscritto_cellulare=$db->escape_text($piscritto_cellulare);
    		
            $piscritto_email=getPARAMETRO("iscritto_email");
    		$piscritto_email=$db->escape_text($piscritto_email);
    		
            $sSQL="select iddara_anagrafica from dara_anagrafica where anagrafica_codicefiscale='$piscritto_codicefiscale'";
			$piddara_richiedente=$db->getVALUE($sSQL,'iddara_anagrafica');
			if(!empty($piddara_richiedente))
			{
				$sSQL="UPDATE dara_anagrafica SET 
		            anagrafica_codicefiscale='$piscritto_codicefiscale',
		            anagrafica_cognome='$piscritto_cognome',
		            anagrafica_nome='$piscritto_nome',
		            anagrafica_genere='$piscritto_genere',
		            anagrafica_datanascita='$piscritto_datanascita',
		            idnazionalita='$pidnazionalita_iscritto',
		            idnazione_nascita='$pidnazione_nascita_iscritto',
		            anagrafica_nazionenascita='$piscritto_nazionenascita',
		            idcomune_nascita='$pidcomune_nascita_iscritto',
		            anagrafica_comunenascita='$piscritto_comunenascita',
		            anagrafica_provnascita='$piscritto_provnascita',
		            anagrafica_indirizzo='$piscritto_indirizzo',
		            anagrafica_civico='$piscritto_civico',
		            idcomune_residenza='$pidcomune_residenza_iscritto',
		            anagrafica_comuneresidenza='$piscritto_comuneresidenza',
		            anagrafica_provresidenza='$piscritto_provresidenza',
		            anagrafica_cellulare='$piscritto_cellulare',
		            anagrafica_email='$piscritto_email' 
		        WHERE iddara_anagrafica='$piddara_richiedente'";
			}
			else
			{
				//prendo l'idcliente dal presentante
				$sSQL="SELECT iddara_cliente FROM dara_anagrafica WHERE iddara_anagrafica='$piddara_presentante'";
				$piddara_cliente=$db->getVALUE($sSQL,'iddara_cliente');

				$insert="INSERT INTO dara_anagrafica (
	                iddara_cliente,
	        	    anagrafica_codicefiscale,
	                anagrafica_cognome,
	                anagrafica_nome,
	                anagrafica_genere,
	                anagrafica_datanascita,
	                idnazionalita,
	                idnazione_nascita,
	                anagrafica_nazionenascita,
	                idcomune_nascita,
	                anagrafica_comunenascita,
	                anagrafica_provnascita,
	                anagrafica_indirizzo,
	                anagrafica_civico,
	                idcomune_residenza,
	                anagrafica_comuneresidenza,
	                anagrafica_provresidenza,
	                anagrafica_cellulare,
	                anagrafica_email
	            ) 
	            VALUES (
	                '$piddara_cliente',
	                '$piscritto_codicefiscale',
	                '$piscritto_cognome',
	                '$piscritto_nome',
	                '$piscritto_genere',
	                '$piscritto_datanascita',
	                '$pidnazionalita_iscritto',
	                '$pidnazione_nascita_iscritto',
	                '$piscritto_nazionenascita',
	                '$pidcomune_nascita_iscritto',
	                '$piscritto_comunenascita',
	                '$piscritto_provnascita',
	                '$piscritto_indirizzo',
	                '$piscritto_civico',
	                '$pidcomune_residenza_iscritto',
	                '$piscritto_comuneresidenza',
	                '$piscritto_provresidenza',
	                '$piscritto_cellulare',
	                '$piscritto_email'
	            )";
	            $db->query($insert);
	            $piddara_richiedente=$db->insert_id();
			}
        }
        else
        	$piddara_richiedente=$piddara_presentante;

        if(!empty($piddara_domanda))
        {
        	$sSQL="UPDATE dara_domanda SET
			iddara_presentetada='$piddara_presentante',
			iddara_richiedente='$piddara_richiedente',
			iddara_avviso='$piddara_avviso'
			WHERE iddara_domanda='$piddara_domanda'"; 
			$db->query($sSQL);
        }
        else
        {
	        //Controllo se esiste già una domanda
			$sSQL="SELECT iddara_domanda FROM dara_domanda WHERE iddara_avviso='$piddara_avviso' AND iddara_richiedente=''";
			$esiste_domanda=$db->getVALUE($sSQL,'iddara_domanda');

			if(empty($esiste_domanda))
			{
	        	$sSQL="INSERT INTO dara_domanda (iddara_presentetada,iddara_richiedente,iddara_avviso) VALUES('$piddara_presentante','$piddara_richiedente','$piddara_avviso')";
				$db->query($sSQL);
				$piddara_domanda=$db->insert_id();				
			}
			else
			{
				$output = array("error"=>"2","iddara_domanda"=>0);
				echo json_encode($output);
				die;
			}
        }

		$output = array("error"=>"0","iddara_domanda"=>$piddara_domanda);
		echo json_encode($output);

		break;

	case "savestep2":
        $piddara_domanda=getPARAMETRO("iddara_domanda");
        if(!empty($piddara_domanda))
        {
	        $piddara_avviso=getPARAMETRO("iddara_avviso");

			$domanda=new DARADomanda($piddara_domanda);
			$avviso=new DARAAvviso($piddara_avviso);

			$aAUTOCERTIFICAZIONI=$avviso->getAUTOCERTIFICAZIONI(true);
			//print_r($aAUTOCERTIFICAZIONI);

			foreach ($aAUTOCERTIFICAZIONI as $key => $aDATI) 
			{
				$iddara_avviso_autocertificazione=$aDATI["iddara_avviso_autocertificazione"];
				$iddara_tbl_tipocampo=$aDATI["iddara_tbl_tipocampo"];
				$pvalue=getPARAMETRO("autocertificazione".$iddara_avviso_autocertificazione);

				switch($iddara_tbl_tipocampo)
				{
					case '6':
					  break;
					default:
					  $domanda->saveAUTOCERTIFICAZIONE($iddara_avviso_autocertificazione,$iddara_tbl_tipocampo,$pvalue);
					  break;  
				}
			}

			$output = array("error"=>"0");
		}
		else
			$output = array("error"=>"1","message"=>"Domanda non rilevata");

		echo json_encode($output);
		break;

	case "saveAUTOCERTIFICAZIONI":
        $piddara_domanda=getPARAMETRO("iddara_domanda");
        if(!empty($piddara_domanda))
        {
	        $piddara_avviso=getPARAMETRO("iddara_avviso");

			$domanda=new DARADomanda($piddara_domanda);
			$avviso=new DARAAvviso($piddara_avviso);

			$aAUTOCERTIFICAZIONI=$avviso->getAUTOCERTIFICAZIONI();
			//print_r($aAUTOCERTIFICAZIONI);

			foreach ($aAUTOCERTIFICAZIONI as $key => $aDATI) 
			{
				$iddara_avviso_autocertificazione=$aDATI["iddara_avviso_autocertificazione"];
				$iddara_tbl_tipocampo=$aDATI["iddara_tbl_tipocampo"];
				$pvalue=getPARAMETRO("autocertificazione".$iddara_avviso_autocertificazione);

				switch($iddara_tbl_tipocampo)
				{
					case '6':
					  break;
					default:
					  $domanda->saveAUTOCERTIFICAZIONE($iddara_avviso_autocertificazione,$iddara_tbl_tipocampo,$pvalue);
					  break;  
				}
			}

			$output = array("error"=>"0");
		}
		else
			$output = array("error"=>"1","message"=>"Domanda non rilevata");

		echo json_encode($output);
		break;

	case "saveautocertificazionerisposta":
		$piddara_avviso_autocertificazione=getPARAMETRO("_k");
		$piddara_avviso=getPARAMETRO("_idavviso");
		$pvalue=getPARAMETRO("_value");
      	$pvalue=$db->escape_text($pvalue);
		$prisposta=getPARAMETRO("_risposta");
      	$prisposta=$db->escape_text($prisposta);

      	if(!empty($piddara_avviso_autocertificazione) && !empty($piddara_avviso) && !empty($pvalue) && !empty($prisposta))
      	{
			$sSQL="SELECT iddara_avviso_autocertificazione_risposta FROM dara_avviso_autocertificazione_risposta WHERE iddara_avviso='$piddara_avviso' AND iddara_avviso_autocertificazione='$piddara_avviso_autocertificazione' AND risposta_valore='$pvalue'";
			$iddara_avviso_autocertificazione_risposta=$db->getVALUE($sSQL,'iddara_avviso_autocertificazione_risposta');
			if(empty($iddara_avviso_autocertificazione_risposta))
			{
				$sSQL="INSERT INTO dara_avviso_autocertificazione_risposta (iddara_avviso_autocertificazione,iddara_avviso,risposta_descrizione,risposta_valore) VALUES('$piddara_avviso_autocertificazione','$piddara_avviso','$prisposta','$pvalue')";
				$db->query($sSQL);
				$iddara_avviso_autocertificazione_risposta=$db->insert_id();

				$output = array("error"=>"0","iddara_avviso_autocertificazione_risposta"=>$iddara_avviso_autocertificazione_risposta,"risposta_descrizione"=>$prisposta,"risposta_valore"=>$pvalue);
			}
			else
			{
				$output = array("error"=>"1", "message"=>"il valore definito nel campo value è già stato utilizzato per un'altra risposta.");
			}
		}
		else
		{
			$output = array("error"=>"1", "message"=>"dati non rilevati.");
		}

		echo json_encode($output);

		break;

	case "loadautocertificazionerisposte":
		$piddara_avviso_autocertificazione=getPARAMETRO("_k");
		$piddara_avviso=getPARAMETRO("_idavviso");

		$avviso=new DARAAvviso($piddara_avviso);
		$aRISPOSTE=$avviso->getRISPOSTALIST($piddara_avviso_autocertificazione);
		
		$data=array();

		if (@is_array($aRISPOSTE))
		{
        	foreach ($aRISPOSTE as $key => $aDATI) 
        	{
                $data[] = array(
					'iddara_avviso_autocertificazione_risposta'=>$aDATI["iddara_avviso_autocertificazione_risposta"],
					'risposta_descrizione'=>$aDATI["risposta_descrizione"],
					'risposta_valore'=>$aDATI["risposta_valore"]
				);
    		}
		}

		$output = array(
			'error'				=>	"0",
			'data'				=>	$data,
		);

		echo json_encode($output);

		break;

	case "savedipendenza":
		$piddara_avviso=getPARAMETRO("_idavviso");
		$dipendenza_autocertificazione=getPARAMETRO("dipendenza_autocertificazione");
		$dipendenza_autocertificazione_capo=getPARAMETRO("dipendenza_autocertificazione_capo");
		$dipendenza_autocertificazione_risposta_capo=getPARAMETRO("dipendenza_autocertificazione_risposta_capo");

      	if(!empty($dipendenza_autocertificazione) && !empty($dipendenza_autocertificazione_capo) && !empty($dipendenza_autocertificazione_risposta_capo) && !empty($piddara_avviso))
      	{
      		if($dipendenza_autocertificazione!=$dipendenza_autocertificazione_capo)
      		{
				$sSQL="SELECT iddara_avviso_dipendenza FROM dara_avviso_dipendenza WHERE iddara_avviso='$piddara_avviso' AND iddara_avviso_autocertificazione='$dipendenza_autocertificazione' AND iddara_avviso_autocertificazione_capo='$dipendenza_autocertificazione_capo'";
				$iddara_avviso_dipendenza=$db->getVALUE($sSQL,'iddara_avviso_dipendenza');
				if(empty($iddara_avviso_dipendenza))
				{
					$sSQL="INSERT INTO dara_avviso_dipendenza (iddara_avviso,iddara_avviso_autocertificazione,iddara_avviso_autocertificazione_capo,iddara_avviso_autocertificazione_capo_risposta) VALUES('$piddara_avviso','$dipendenza_autocertificazione','$dipendenza_autocertificazione_capo','$dipendenza_autocertificazione_risposta_capo')";
					$db->query($sSQL);
					$iddara_avviso_dipendenza=$db->insert_id();

					$sSQL="SELECT autocertificazione_descrizione FROM dara_avviso_autocertificazione WHERE iddara_avviso_autocertificazione='$dipendenza_autocertificazione'";
					$autocertificazione_descrizione=$db->getVALUE($sSQL,'autocertificazione_descrizione');
					$autocertificazione_descrizione=stripslashes($autocertificazione_descrizione);

					$sSQL="SELECT autocertificazione_descrizione FROM dara_avviso_autocertificazione WHERE iddara_avviso_autocertificazione='$dipendenza_autocertificazione_capo'";
					$autocertificazione_capo_descrizione=$db->getVALUE($sSQL,'autocertificazione_descrizione');
					$autocertificazione_capo_descrizione=stripslashes($autocertificazione_capo_descrizione);

					$sSQL="SELECT risposta_descrizione FROM dara_avviso_autocertificazione_risposta WHERE iddara_avviso_autocertificazione_risposta='$dipendenza_autocertificazione_risposta_capo'";
					$autocertificazione_risposta_capo_descrizione=$db->getVALUE($sSQL,'risposta_descrizione');
					$autocertificazione_risposta_capo_descrizione=stripslashes($autocertificazione_risposta_capo_descrizione);

					$output = array("error"=>"0","iddara_avviso_dipendenza"=>$iddara_avviso_dipendenza,"autocertificazione_descrizione"=>$autocertificazione_descrizione,"autocertificazione_capo_descrizione"=>$autocertificazione_capo_descrizione, "autocertificazione_risposta_capo_descrizione"=>$autocertificazione_risposta_capo_descrizione);
				}
				else
				{
					$output = array("error"=>"1", "message"=>"dipendenza già presente.");
				}
			}
			else
			{
				$output = array("error"=>"1", "message"=>"un'autocertificazione non può dipendere da se stessa.");
			}
		}
		else
		{
			$output = array("error"=>"1", "message"=>"dati non rilevati.");
		}

		echo json_encode($output);

		break;

	case "deletedipendenza":
		$piddara_avviso_dipendenza=getPARAMETRO("_k");

		$delete="DELETE FROM dara_avviso_dipendenza WHERE iddara_avviso_dipendenza='$piddara_avviso_dipendenza'";
		$db->query($delete);

		$output = array("error"=>"0");
		
		echo json_encode($output);

		break;

	case "deleterisposta":
		$piddara_avviso_autocertificazione_risposta=getPARAMETRO("_k");

		$delete="DELETE FROM dara_avviso_autocertificazione_risposta WHERE iddara_avviso_autocertificazione_risposta='$piddara_avviso_autocertificazione_risposta'";
		$db->query($delete);

		$output = array("error"=>"0");
		
		echo json_encode($output);

		break;

	case "deletedocumento":
		$piddara_domanda_documento=getPARAMETRO("_k");

		$delete="DELETE FROM dara_domanda_documento WHERE iddara_domanda_documento='$piddara_domanda_documento'";
		$db->query($delete);

		$output = array("error"=>"0");
		
		echo json_encode($output);
		break;

	case "saveorganigramma":
		$piddara_organigramma=getPARAMETRO("_k");

		$piddara_cliente=getPARAMETRO("dara_societa");
		$piddara_cliente=$db->escape_text($piddara_cliente);

		$porganigramma_cognome=getPARAMETRO("organigramma_cognome");
		$porganigramma_cognome=$db->escape_text($porganigramma_cognome);

		$porganigramma_nome=getPARAMETRO("organigramma_nome");
		$porganigramma_nome=$db->escape_text($porganigramma_nome);

		$porganigramma_codicefiscale=getPARAMETRO("organigramma_codicefiscale");
		$porganigramma_codicefiscale=$db->escape_text($porganigramma_codicefiscale);

		$piddara_tbl_organigrammaruolo=getPARAMETRO("iddara_tbl_organigrammaruolo");
		$piddara_tbl_organigrammaruolo=$db->escape_text($piddara_tbl_organigrammaruolo);

		$porganigramma_cellulare=getPARAMETRO("organigramma_cellulare");
		$porganigramma_cellulare=$db->escape_text($porganigramma_cellulare);

		$porganigramma_email=getPARAMETRO("organigramma_email");
		$porganigramma_email=$db->escape_text($porganigramma_email);

		$organigramma=new DARAOrganigramma($piddara_organigramma);  

		if (empty($piddara_organigramma))  
			$piddara_organigramma=$organigramma->insertORGANIGRAMMA($piddara_cliente,$porganigramma_cognome,$porganigramma_nome,$porganigramma_codicefiscale,$piddara_tbl_organigrammaruolo,$porganigramma_cellulare,$porganigramma_email);
		else  
			$organigramma->updateORGANIGRAMMA($piddara_cliente,$porganigramma_cognome,$porganigramma_nome,$porganigramma_codicefiscale,$piddara_tbl_organigrammaruolo,$porganigramma_cellulare,$porganigramma_email);

		$output = array("error"=>"0");
		
		echo json_encode($output);

		break;

	case "deleteorganigramma":
		$piddara_organigramma=getPARAMETRO("_k");

		if(!empty($piddara_organigramma))
		{
			$sSQL="delete from dara_organigramma where iddara_organigramma='".$piddara_organigramma."'";
			$db->query($sSQL);

			$output = array("error"=>"0");
  		}
  		else
  		{
			$output = array("error"=>"1");
		}

		echo json_encode($output);
		break;
	

	default:
		break;
}

?>
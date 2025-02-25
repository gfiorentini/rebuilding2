<?php
require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
include("../librerie/class.spid.php");

global $db;
/*
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
/*
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
*/
$pparams=getPARAMETRO("params");

$params_explode=explode("|",$pparams);

$authid=$params_explode[0];
//$pprocedura=$params_explode[1];

if(!empty($authid))
{
    $spid=new spid();
    $res=$spid->retrieveUserData($authid);

    
    
    if($res["error_code"]==2)
        die("Attenzione! impossibile accedere");

    $datiLetti=print_r($res,true);
    $inptofile=date("Y-m-d ----- H:i:s")." --- ACCESSO SPID ---- ".$datiLetti;
    
    //saveLOG("../logs/log_spid.log",$inptofile);
   
    $authId=$res["response"]["retrieveUserDataReturn"]["authId"];
    $authId=db_string($authId);

    $aziendaDenominazione=$res["response"]["retrieveUserDataReturn"]["aziendaDenominazione"];
    $aziendaDenominazione=db_string($aziendaDenominazione);

    $aziendaPIVA=$res["response"]["retrieveUserDataReturn"]["aziendaPIVA"];
    $aziendaPIVA=db_string($aziendaPIVA);

    $aziendaSedelegale=$res["response"]["retrieveUserDataReturn"]["aziendaSedelegale"];
    $aziendaSedelegale=db_string($aziendaSedelegale);

    $aziendaSedelegaleCitta=$res["response"]["retrieveUserDataReturn"]["aziendaSedelegaleCitta"];
    $aziendaSedelegaleCitta=db_string($aziendaSedelegaleCitta);

    $cellulare=$res["response"]["retrieveUserDataReturn"]["cellulare"];
    $cellulare=db_string($cellulare);

    $codiceFiscale=$res["response"]["retrieveUserDataReturn"]["codiceFiscale"];
    $codiceFiscale=db_string($codiceFiscale);
    //$codiceFiscale="RMTSFO94S66I608X";

    $cognome=$res["response"]["retrieveUserDataReturn"]["cognome"];
    $cognome=strtoupper($cognome);
    $cognome=db_string($cognome);

    $dataInserimento=$res["response"]["retrieveUserDataReturn"]["dataInserimento"];
    $dataInserimento=db_string($dataInserimento);

    $demo=$res["response"]["retrieveUserDataReturn"]["demo"];
    $demo=db_string($demo);

    $domicilioCap=$res["response"]["retrieveUserDataReturn"]["domicilioCap"];
    $domicilioCap=db_string($domicilioCap);

    $domicilioCitta=$res["response"]["retrieveUserDataReturn"]["domicilioCitta"];
    $domicilioCitta=db_string($domicilioCitta);

    $domicilioIndirizzo=$res["response"]["retrieveUserDataReturn"]["domicilioIndirizzo"];
    $domicilioIndirizzo=db_string($domicilioIndirizzo);

    $domicilioProvincia=$res["response"]["retrieveUserDataReturn"]["domicilioProvincia"];
    $domicilioProvincia=db_string($domicilioProvincia);

    $domicilioStato=$res["response"]["retrieveUserDataReturn"]["domicilioStato"];
    $domicilioStato=db_string($domicilioStato);

    $fax=$res["response"]["retrieveUserDataReturn"]["fax"]; 
    $fax=db_string($fax);

    $lavoro=$res["response"]["retrieveUserDataReturn"]["lavoro"];
    $lavoro=db_string($lavoro);

    $livelloAutenticazione=$res["response"]["retrieveUserDataReturn"]["livelloAutenticazione"];
    $livelloAutenticazione=db_string($livelloAutenticazione);

    $livelloPasswordPolicy=$res["response"]["retrieveUserDataReturn"]["livelloPasswordPolicy"];
    $livelloPasswordPolicy=db_string($livelloPasswordPolicy);

    $mailAddress=$res["response"]["retrieveUserDataReturn"]["mailAddress"];
    $mailAddress=db_string($mailAddress);

    $aDATI=get_data_from_cf($codiceFiscale);


    $nascitaData=$aDATI["data_nascita"];
    if(empty_data($nascitaData))
    {
        $nascitaData=$res["response"]["retrieveUserDataReturn"]["nascitaData"];
        $nascitaData=db_string($nascitaData);
    }

    $nascitaLuogo=$res["response"]["retrieveUserDataReturn"]["nascitaLuogo"];
    $nascitaLuogo=db_string($nascitaLuogo);

    if(!empty($nascitaLuogo))
    {
        $sSQL="SELECT * FROM nazione WHERE belfiore='$nascitaLuogo'";
        $fldidgen_nazione_nascita=$db->getVALUE($sSQL,"idnazione");
        if($fldidgen_nazione_nascita!=122)
        {
            $sSQL="SELECT nazione FROM nazione WHERE idnazione='$fldidgen_nazione_nascita'";
            $fldcomune_nascita=$db->getVALUE($sSQL,"nazione");
            $fldcomune_nascita=db_string($fldcomune_nascita);
        }
        else
        {
            $sSQL="SELECT idcomune FROM comune WHERE belfiore='$nascitaLuogo'";
            $fldidgen_comune_nascita=$db->getVALUE($sSQL,"idcomune");

            $sSQL="SELECT comune FROM comune WHERE idcomune='$fldidgen_comune_nascita'";
            $fldcomune_nascita=$db->getVALUE($sSQL,"comune");
            $fldcomune_nascita=db_string($fldcomune_nascita);
        }

    }
    else
    {
        $fldidgen_nazione_nascita=$aDATI["idamb_nazione"];
        $fldidgen_comune_nascita=$aDATI["idgen_comune_nascita"];
        $fldcomune_nascita=db_string($aDATI["comune_nascita"]);
    }

    $nascitaProvincia=$res["response"]["retrieveUserDataReturn"]["nascitaProvincia"];
    $nascitaProvincia=db_string($nascitaProvincia);
    if(empty($nascitaProvincia))
        $nascitaProvincia=$aDATI["provincia_nascita"];

    $nome=$res["response"]["retrieveUserDataReturn"]["nome"];
    $nome=strtoupper($nome);
    $nome=db_string($nome);

    $pec=$res["response"]["retrieveUserDataReturn"]["pec"];
    $pec=db_string($pec);

    $professionistaAlbo=$res["response"]["retrieveUserDataReturn"]["professionistaAlbo"];
    $professionistaAlbo=db_string($professionistaAlbo);

    $professionistaEstremiAlbo=$res["response"]["retrieveUserDataReturn"]["professionistaEstremiAlbo"];
    $professionistaEstremiAlbo=db_string($professionistaEstremiAlbo);

    $residenzaCap=$res["response"]["retrieveUserDataReturn"]["residenzaCap"];
    $residenzaCap=db_string($residenzaCap);

    $residenzaCitta=$res["response"]["retrieveUserDataReturn"]["residenzaCitta"];
    $residenzaCitta=db_string($residenzaCitta);

    $residenzaIndirizzo=$res["response"]["retrieveUserDataReturn"]["residenzaIndirizzo"];
    $residenzaIndirizzo=db_string($residenzaIndirizzo);

    $residenzaProvincia=$res["response"]["retrieveUserDataReturn"]["residenzaProvincia"];
    $residenzaProvincia=db_string($residenzaProvincia);

    $residenzaStato=$res["response"]["retrieveUserDataReturn"]["residenzaStato"];
    $residenzaStato=db_string($residenzaStato);

    $sesso=$res["response"]["retrieveUserDataReturn"]["sesso"];
    $sesso=db_string($sesso);
    if(empty($sesso))
        $sesso=$aDATI["sesso"];

    $telefono=$res["response"]["retrieveUserDataReturn"]["telefono"];
    $telefono=db_string($telefono);

    $tipoSoggetto=$res["response"]["retrieveUserDataReturn"]["tipoSoggetto"];
    $tipoSoggetto=db_string($tipoSoggetto);

    $titolo=$res["response"]["retrieveUserDataReturn"]["titolo"];
    $titolo=db_string($titolo);
    
    $sSQL="SELECT iddara_operatore FROM dara_operatore WHERE operatore_codicefiscale='$codiceFiscale'";
    $pidutente=$db->getVALUE($sSQL,"iddara_operatore");
    $sPage='';



    if ($pidutente>0)
    {
        if(!empty($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else
            $ipaddress = $_SERVER['REMOTE_ADDR'];
            
        $chiaveRSA=generaCHIAVE($codiceFiscale,$ipaddress,$pidutente);
        
        insertLOG($chiaveRSA,$ipaddress,$pidutente,"login");

        insertSESSION($chiaveRSA);

        if(!empty($authid))
            setcookieAuthServiceAuhtId($authid,'SPID');
    
        //$sPage="https://rebuilding.regione.marche.it/rebuilding/home?_spid=true";
        $sPage="http://localhost:8082/rebuilding/home?_spid=true";
    }

    if(!empty($sPage))
        header("location: ".$sPage);   
    else
        header("location: ./login");   

}
else
    die("authId non rilevato");


function get_data_from_cf($fldcodice_fiscale)
{
    global $db;
    if(strlen($fldcodice_fiscale)==16)
    {
        $fldmese_nascita=substr($fldcodice_fiscale, 8, 1);
        switch($fldmese_nascita)
        {
            case "A":
                $fldmese_nascita="01";
            break;

            case "B":
                $fldmese_nascita="02";
            break;

            case "C":
                $fldmese_nascita="03";
            break;

            case "D":
                $fldmese_nascita="04";
            break;

            case "E":
                $fldmese_nascita="05";
            break;

            case "H":
                $fldmese_nascita="06";
            break;

            case "L":
                $fldmese_nascita="07";
            break;

            case "M":
                $fldmese_nascita="08";
            break;

            case "P":
                $fldmese_nascita="09";
            break;

            case "R":
                $fldmese_nascita="10";
            break;

            case "S":
                $fldmese_nascita="11";
            break;

            case "T":
                $fldmese_nascita="12";
            break;
        }

        $fldgiorno_nascita=substr($fldcodice_fiscale, 9, 2);
        $fldgiorno_nascita=intval($fldgiorno_nascita);
        if($fldgiorno_nascita>=41)
        {
            $fldsesso="F";
            $fldgiorno_nascita=$fldgiorno_nascita-40;

            if($fldgiorno_nascita<=9)
                $fldgiorno_nascita="0".$fldgiorno_nascita;
        }
        else
        {
            $fldsesso="M";
            if($fldgiorno_nascita<=9)
                $fldgiorno_nascita="0".$fldgiorno_nascita;
        }

        $fldanno_nascita=substr($fldcodice_fiscale, 6, 2);
        $fldanno_nascita_int=intval($fldanno_nascita);
        if($fldanno_nascita_int>22)
            $fldanno_nascita="19".$fldanno_nascita;
        else
            $fldanno_nascita="20".$fldanno_nascita;

        $flddata_nascita=$fldanno_nascita."-".$fldmese_nascita."-".$fldgiorno_nascita;
        $fldbelfiore=substr($fldcodice_fiscale, 11, 4);

        $array_dati_anagrafici["sesso"]=$fldsesso;
        $array_dati_anagrafici["data_nascita"]=$flddata_nascita;

        //controllo prima la nazione di nascita perchè nella tabella dei comuni ci sono anche comuni esteri
        $sSQL="SELECT idnazione from nazione where belfiore='$fldbelfiore'";
        $fldidgen_nazione_nascita=$db->getVALUE($sSQL,"idnazione");
        if(!empty($fldidgen_nazione_nascita))
        {
            $sSQL="SELECT nazione from nazione where idnazione='$fldidgen_nazione_nascita'";
            $fldnazione=$db->getVALUE($sSQL,"nazione");
            $sSQL="SELECT provincia from comune where idnazione='$fldidgen_nazione_nascita'";
            $fldprov_nascita=$db->getVALUE($sSQL,"provincia");

            $array_dati_anagrafici["idgen_nazione_nascita"]=$fldidgen_nazione_nascita;
            $array_dati_anagrafici["nazione_nascita"]=$fldnazione;
        }
        else
        {
            $sSQL="SELECT idcomune from comune where belfiore='$fldbelfiore'";
            $fldidgen_comune_nascita=$db->getVALUE($sSQL,"idcomune");

            if(!empty($fldidgen_comune_nascita))
            {
                $sSQL="SELECT comune from comune where idcomune='$fldidgen_comune_nascita'";
                $fldcomune_nascita=$db->getVALUE($sSQL,"comune");

                $sSQL="SELECT provincia from comune where idcomune='$fldidgen_comune_nascita'";
                $fldprov_nascita=$db->getVALUE($sSQL,"provincia");

                $array_dati_anagrafici["idgen_nazione_nascita"]=122;
                $array_dati_anagrafici["nazione_nascita"]="ITALIA";
                $array_dati_anagrafici["idgen_comune_nascita"]=$fldidgen_comune_nascita;
                $array_dati_anagrafici["comune_nascita"]=$fldcomune_nascita;
                $array_dati_anagrafici["provincia_nascita"]=$fldprov_nascita;
            }
        }

        return $array_dati_anagrafici;
    }
}

?>
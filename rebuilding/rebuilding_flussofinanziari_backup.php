<?php
//phpinfo();
/*
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
*/
require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");
require_once("../librerie/dara.class.servizi.php");


//error_reporting(0);
//ini_set("display_errors",1);
date_default_timezone_set('Europe/Rome');
$idoperatore=verificaUSER();

$operatore=new DARAOperatore($idoperatore);
$operatore_ente=$operatore->operatore_ente;
if(empty($operatore_ente))
  $operatore_ente=9999;

$operatore_flagamministratore=$db->getVALUE("select operatore_flagamministratore from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagamministratore");
$operatore_flagrup=$db->getVALUE("select operatore_flagrup from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagrup");
$operatore_flagdirigente=$db->getVALUE("select operatore_flagdirigente from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagdirigente");

//$autorizzazioni_domanda=$operatore->getAUORIZZAZIONEdomanda();

$aENTI=array(1=>"ATS1 - Pesaro",3=>"ATS3 - C.M. Catria e Nerone",4=>"ATS4 - Urbino",5=>"ATS5 - C.M. Montefeltro",6=>"ATS6 - Fano",7=>"ATS7 - Fossombrone",8=>"ATS8 - Senigallia",9=>"ATS9 - ASP Ambito 9 Jesi",10=>"ATS10 - Fabriano",11=>"ATS11 - Ancona",12=>"ATS12 - Falconara Marittima",13=>"ATS13 - Osimo",14=>"ATS14 - Civitanova Marche",15=>"ATS15 - Macerata",16=>"ATS16 - C.M. Monti Azzurri",17=>"ATS17 - Unione montanta alta valle del potenza e dell'Esino",18=>"ATS18 - C.M. Camerino",19=>"ATS19 - Fermo",20=>"ATS20 - Porto Sant'Elpidio",21=>"ATS 21 - San Benedetto del Tronto",22=>"ATS22 - Ascoli Piceno",23=>"ATS23 - U.C. Vallata del Tronto",24=>"ATS24 - C.M. dei Sibillini");

$centroterritorialeOPERATORE=$aENTI[$operatore_ente];

$aANNI=array(2017=>"2017",2018=>"2018",2019=>"2019",2020=>"2020",2021=>"2021",2022=>"2022",2023=>"2023");

$aTIPOFONDO=array(1=>"Regionale",2=>"Statale",3=>"FSE",4=>"Misto","Sanitario");
$aTIPODOCUMENTO=array(1=>"RIPARTO",2=>"CRONOPROGRAMMA",3=>"MODULISTICA",4=>"ALTRO");
$aTIPOAREA=array(1=>"Famiglia e Minori",2=>"Anziani",3=>"Immigrati e nomadi",4=>"Dipendenze",5=>"Disabili",6=>"Povertà, disagio adulti e senza fissa dimora",7=>"Multiutenza");

$operatori=new DARAOperatore(0);
$aRUP=$operatori->getOPERATORI('operatore_flagamministratore=1 and operatore_flagabilitato=1 and operatore_flagrup=1');

$pidrebuilding_flussofinanziario=getPARAMETRO("_RENDICONTAZIONE");
$pidrebuilding_flussofinanziario=$db->escape_text($pidrebuilding_flussofinanziario);

$prendicontazione_leggeriferimento=getPARAMETRO("rendicontazione_leggeriferimento");
$prendicontazione_leggeriferimento=$db->escape_text($prendicontazione_leggeriferimento);

$prendicontazione_areaintervento=getPARAMETRO("rendicontazione_areaintervento");
$prendicontazione_areaintervento=$db->escape_text($prendicontazione_areaintervento);

$prendicontazione_testo=getPARAMETRO("rendicontazione_testo");
$prendicontazione_testo=$db->escape_text($prendicontazione_testo);

$prendicontazione_tipofondo=getPARAMETRO("rendicontazione_tipofondo");
$prendicontazione_tipofondo=$db->escape_text($prendicontazione_tipofondo);

$prendicontazione_anno=getPARAMETRO("rendicontazione_anno");
$prendicontazione_anno=$db->escape_text($prendicontazione_anno);

$prendicontazione_ente=getPARAMETRO("rendicontazione_ente");
$prendicontazione_ente=$db->escape_text($prendicontazione_ente);

$target_dir = "../documenti/rebuilding/toolkit/";

if(getPARAMETRO("_salva") && $operatore_flagamministratore==1 && empty($operatore_flagdirigente))
{

  $pflussofinanziario_ente=getPARAMETRO("formENTE");
  $pflussofinanziario_ente=$db->escape_text($pflussofinanziario_ente);
  $aentiselezionati=explode(",",$pflussofinanziario_ente);
  
  $pflussofinanziario_anno=getPARAMETRO("formANNO");
  $pflussofinanziario_anno=$db->escape_text($pflussofinanziario_anno);

  $pflussofinanziario_tipofondo=getPARAMETRO("formCLASSIFICAZIONE");
  $pflussofinanziario_tipofondo=$db->escape_text($pflussofinanziario_tipofondo);

  $pflussofinanziario_codicesirps=getPARAMETRO("formCODICESIRPS");
  $pflussofinanziario_codicesirps=$db->escape_text($pflussofinanziario_codicesirps);

  $pflussofinanziario_leggeriferimento=getPARAMETRO("formLEGGERIFERIMENTO");
  $pflussofinanziario_leggeriferimento=$db->escape_text($pflussofinanziario_leggeriferimento);

  $pflussofinanziario_titolo=getPARAMETRO("formTITOLO");
  $pflussofinanziario_titolo=$db->escape_text($pflussofinanziario_titolo);

  $pflussofinanziario_testo=getPARAMETRO("formTESTO");
  $pflussofinanziario_testo=$db->escape_text($pflussofinanziario_testo);

  $pflussofinanziario_rup=getPARAMETRO("formRUP");
  $pflussofinanziario_rup=$db->escape_text($pflussofinanziario_rup);

  $pflussofinanziario_contatti_rup=getPARAMETRO("formCONTATTI");
  $pflussofinanziario_contatti_rup=$db->escape_text($pflussofinanziario_contatti_rup);

  $pflussofinanziario_areaintervento=getPARAMETRO("formAREAINTERVENTO");
  $pflussofinanziario_areaintervento=$db->escape_text($pflussofinanziario_areaintervento);

  $pflussofinanziario_stato=1;

  $pflussofinanziario_datainserimento=date("Y-m-d");
  if(!empty($pidrebuilding_flussofinanziario))
  {   
    $pflussofinanziario_ultimamodifica=date("Y-m-d");

    $sSQL="UPDATE rebuilding_flussofinanziario  SET
    flussofinanziario_ente='$pflussofinanziario_ente',
    flussofinanziario_tipofondo='$pflussofinanziario_tipofondo',
    flussofinanziario_stato='$pflussofinanziario_stato',
    flussofinanziario_anno='$pflussofinanziario_anno',
    flussofinanziario_titolo='$pflussofinanziario_titolo',
    flussofinanziario_testo='$pflussofinanziario_testo',
    flussofinanziario_rup='$pflussofinanziario_rup',
    flussofinanziario_contatti_rup='$pflussofinanziario_contatti_rup',
    flussofinanziario_codicesirps='$pflussofinanziario_codicesirps',
    flussofinanziario_leggeriferimento='$pflussofinanziario_leggeriferimento',
    flussofinanziario_areaintervento='$pflussofinanziario_areaintervento', 
    flussofinanziario_ultimamodifica='$pflussofinanziario_ultimamodifica' 
    WHERE idrebuilding_flussofinanziario='$pidrebuilding_flussofinanziario'"; 
    $db->query($sSQL);

    $alert_update_success=true;
  }
  else
  {
    
    $pflussofinanziario_orainserimento=date("H:i");
    $sSQL="insert into rebuilding_flussofinanziario  (
    flussofinanziario_ente,
    flussofinanziario_stato,
    flussofinanziario_tipofondo,
    flussofinanziario_anno,
    flussofinanziario_datainserimento,
    flussofinanziario_orainserimento,
    flussofinanziario_titolo,
    flussofinanziario_testo,
    flussofinanziario_rup,
    flussofinanziario_contatti_rup,
    flussofinanziario_codicesirps,
    flussofinanziario_leggeriferimento,
    flussofinanziario_areaintervento,
    flussofinanziario_operatore,
    flussofinanziario_ultimamodifica) 
    values(
    '$pflussofinanziario_ente',
    '$pflussofinanziario_stato',
    '$pflussofinanziario_tipofondo',
    '$pflussofinanziario_anno',
    '$pflussofinanziario_datainserimento',
    '$pflussofinanziario_orainserimento',
    '$pflussofinanziario_titolo',
    '$pflussofinanziario_testo',
    '$pflussofinanziario_rup',
    '$pflussofinanziario_contatti_rup',
    '$pflussofinanziario_codicesirps',
    '$pflussofinanziario_leggeriferimento',
    '$pflussofinanziario_areaintervento',
    '$idoperatore',
    '$pflussofinanziario_datainserimento')";
    $db->query($sSQL);
    $pidrebuilding_flussofinanziario=$db->insert_id();

    $alert_insert_success=true;
  }


  $sSQL="delete from rebuilding_flussofinanziario_ente where idrebuilding_flussofinanziario='$pidrebuilding_flussofinanziario'";
  $db->query($sSQL);

  foreach ($aentiselezionati as $key => $value) 
  {
      $sSQL="insert into rebuilding_flussofinanziario_ente (idrebuilding_flussofinanziario,flussofinanziario_ente) values ('$pidrebuilding_flussofinanziario','$value')";
      $db->query($sSQL);
  }


  //$sSQL="delete from rebuilding_flussofinanziario_documento where idrebuilding_flussofinanziario='$pidrebuilding_flussofinanziario'";
  //$db->query($sSQL);


  $flussofinanziario=new rebuildingFLUSSOFINANZIARIO($pidrebuilding_flussofinanziario);
  $aDOCUMENTIDB=$flussofinanziario->getALLEGATI();


  $nALLEGATI=getPARAMETRO("counter_allegati");
  $nALLEGATI=$db->escape_text($nALLEGATI);
 
  //$flussofinanziario_documentotime=mktime(date("H"),date("m"),date("i"),date("m"),date("d"),date("Y"));
  $flussofinanziario_documentotime=date("Ymdhis");
  $str_error_upload='';
  for($i=1;$i<=$nALLEGATI;$i++)
  {    

    $allegatoTITOLO=getPARAMETRO("allegatoTITOLO".$i);
    $allegatoTITOLO=$db->escape_text($allegatoTITOLO);
    
    $allegatoTIPO=getPARAMETRO("allegatoTIPO".$i);
    $allegatoTIPO=$db->escape_text($allegatoTIPO);

    $allegatoENTE=getPARAMETRO("allegatoENTE".$i);
    $allegatoENTE=$db->escape_text($allegatoENTE);

    $idrebuilding_flussofinanziario_documento=getPARAMETRO("_flussofinanziario_documento".$i);
    $idrebuilding_flussofinanziario_documento=$db->escape_text($idrebuilding_flussofinanziario_documento);

    $documentoNAME = basename($_FILES["nome_file".$i]["name"]); 
    if(!empty($documentoNAME) && empty($idrebuilding_flussofinanziario_documento))
    {
      $path_parts = pathinfo($_FILES["nome_file".$i]["name"]);
      $filename=md5(date("Ymdhis").$documentoNAME).".".$path_parts['extension'];
      if($_FILES["nome_file".$i]["error"]!=1)
      {
        // echo $_FILES["nome_file".$i]["tmp_name"]."<br>";
        // echo $target_dir.$filename."<br>";
         copy($_FILES["nome_file".$i]["tmp_name"],$target_dir.$filename);    
   
         $sSQL="INSERT INTO rebuilding_flussofinanziario_documento (idrebuilding_flussofinanziario,flussofinanziario_documentonome,flussofinanziario_documentotitolo,flussofinanziario_documentotipo,flussofinanziario_documentoente,flussofinanziario_documentodata,flussofinanziario_documentooperatore,flussofinanziario_documentoultimamodifica,flussofinanziario_documentotime) values('$pidrebuilding_flussofinanziario','$filename','$allegatoTITOLO','$allegatoTIPO','$allegatoENTE','$pflussofinanziario_datainserimento','$idoperatore','$pflussofinanziario_datainserimento','$flussofinanziario_documentotime') ";
         $db->query($sSQL); 
      }
      else
        $str_error_upload.="FILE ".$_FILES["nome_file".$i]["name"]." non caricato<br>";

         
    }
    elseif(!empty($documentoNAME) && !empty($idrebuilding_flussofinanziario_documento))
    {
      $path_parts = pathinfo($_FILES["nome_file".$i]["name"]);
      $filename=md5(date("Ymdhis").$documentoNAME).".".$path_parts['extension'];
      if($_FILES["nome_file".$i]["error"]!=1)
      {
        copy($_FILES["nome_file".$i]["tmp_name"],$target_dir.$filename);    
        $sSQL="UPDATE rebuilding_flussofinanziario_documento set flussofinanziario_documentonome='$filename',flussofinanziario_documentotitolo='$allegatoTITOLO',flussofinanziario_documentotipo='$allegatoTIPO',flussofinanziario_documentoente='$allegatoENTE',flussofinanziario_documentooperatore='$idoperatore',flussofinanziario_documentoultimamodifica='$ultimamodifica',flussofinanziario_documentotime='$flussofinanziario_documentotime' where idrebuilding_flussofinanziario_documento='$idrebuilding_flussofinanziario_documento'";
        $db->query($sSQL);
      }
      else
        $str_error_upload.="FILE ".$_FILES["nome_file".$i]["name"]." non caricato<br>";
    }
    else
    {
      $ultimamodifica=date("Y-m-d");
      $sSQL="update rebuilding_flussofinanziario_documento set flussofinanziario_documentotitolo='$allegatoTITOLO',flussofinanziario_documentotipo='$allegatoTIPO',flussofinanziario_documentoente='$allegatoENTE',flussofinanziario_documentooperatore='$idoperatore',flussofinanziario_documentoultimamodifica='$ultimamodifica',flussofinanziario_documentotime='$flussofinanziario_documentotime' where idrebuilding_flussofinanziario_documento='$idrebuilding_flussofinanziario_documento'";
      
      $db->query($sSQL);
      

    }
    
  }

  //Esiste il file di riparto?
  $esiste_riparto=$db->getVALUE("select idrebuilding_flussofinanziario_documento from rebuilding_flussofinanziario_documento where idrebuilding_flussofinanziario='$pidrebuilding_flussofinanziario' and flussofinanziario_documentotipo=1","idrebuilding_flussofinanziario_documento");


  if(!empty($pflussofinanziario_ente) && !empty($pflussofinanziario_anno) && !empty($pflussofinanziario_areaintervento) && !empty($pflussofinanziario_tipofondo) && !empty($pflussofinanziario_leggeriferimento) && !empty($esiste_riparto))
  {    
    $flussofinanziario_stato=2;
  }
  else
  {
    $flussofinanziario_stato=1;
  }

  // Aggiorno lo stato della domanda
  $sSQL="UPDATE rebuilding_flussofinanziario  SET
  flussofinanziario_stato='$flussofinanziario_stato' 
  WHERE idrebuilding_flussofinanziario='$pidrebuilding_flussofinanziario'"; 
  $db->query($sSQL);

  //$sSQL="delete from rebuilding_flussofinanziario_documento where idrebuilding_flussofinanziario='$pidrebuilding_flussofinanziario' and flussofinanziario_documentotime!='$flussofinanziario_documentotime'";
  //$db->query($sSQL);


}
elseif(getPARAMETRO("_elimina") && $operatore_flagamministratore==1 && empty($operatore_flagdirigente))
{
  $sSQL="delete from rebuilding_flussofinanziario where idrebuilding_flussofinanziario='$pidrebuilding_flussofinanziario'";
  $db->query($sSQL);
}
elseif(getPARAMETRO("_eliminaallegato") && $operatore_flagamministratore==1 && empty($operatore_flagdirigente))
{

  $pidrebuilding_flussofinanziario_documento=getPARAMETRO("_allegato");
  $pidrebuilding_flussofinanziario_documento=$db->escape_text($pidrebuilding_flussofinanziario_documento);

  $sSQL="delete from rebuilding_flussofinanziario_documento where idrebuilding_flussofinanziario_documento='$pidrebuilding_flussofinanziario_documento'";
  $db->query($sSQL);
}

$aSTATI=array(1=>"NO",2=>"SI");


if(!empty($pidrebuilding_flussofinanziario))
{
  $flussofinanziario=new rebuildingFLUSSOFINANZIARIO($pidrebuilding_flussofinanziario);

  $flussofinanziario_datainserimento=dataitaliana($flussofinanziario->flussofinanziario_datainserimento);
  $flussofinanziario_ultimamodifica=dataitaliana($flussofinanziario->flussofinanziario_ultimamodifica);

  $operatore=new DARAOperatore($flussofinanziario->flussofinanziario_operatore);
  $operatore_nominativo=$operatore->operatore_cognome.' '.$operatore->operatore_nome;
  $operatore_nominativo=addslashes($operatore_nominativo);

}

$disabled_scheda="";
if(empty($operatore_flagamministratore) || !empty($operatore_flagdirigente) || (!empty($pidrebuilding_flussofinanziario) && $flussofinanziario->flussofinanziario_rup>0 && $flussofinanziario->flussofinanziario_rup!=$idoperatore))
  $disabled_scheda="disabled";


if(!$operatore_flagamministratore)
{
  $label_liquidazioni="Liquidazioni";
}
else  
{
  $label_liquidazioni="Liquidazioni degli ATS";
}

?>
<!doctype html>
<html lang="it">
  <head>
  	 <?php echo getREBUILDINGHEAD(true); ?>

     <link rel="stylesheet" href="../librerie/css/bootstrap-select.css">

  </head>
  <body class="bg-light">

  	<?php echo getREBUILDINGNAVBAR(); ?>
    
    <!-- BREADCRUMB -->
    <?php 
		$aBREADCUMB=array();

		$aBREADCUMB[0]["titolo_pagina"]="Home";
		$aBREADCUMB[0]["url"]="home";

		$aBREADCUMB[1]["titolo_pagina"]="Toolkit";
		$aBREADCUMB[1]["url"]="toolkit_menu";

    $aBREADCUMB[2]["titolo_pagina"]="Flussi di finanziamento";
    $aBREADCUMB[2]["url"]="toolkit";

    $aBREADCUMB[3]["titolo_pagina"]="Flussi finanziari";
    $aBREADCUMB[3]["url"]="rendicontazione";

    $aBREADCUMB[4]["titolo_pagina"]="Scheda";
    $aBREADCUMB[4]["url"]="";

		generaBREADCUMB($aBREADCUMB);

	  ?>

    <section class="bg-light">
      <div class="container-fluid">

        <div class="toast-container d-flex justify-content-center align-items-center w-100">
          <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
              
              <strong class="me-auto">Flusso finanziario</strong>              
              <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close" onclick="closeMSG()"></button>
            </div>
            <div class="toast-body ">
              <span class="text-danger">E' obbligatorio selezionare:<br> - almeno un ATS<br> - Area/target<br> - Tipo fondo<br> - Titolo<br> - DGR di riferimento</span>
            </div>
          </div>
        </div>

      

        <div class="row">
          <div class="col-12 col-lg-6 col-xl-8 offset-xl-2 py-lg-2 bg-light">            
              <div class="card-body">

              <ul class="nav nav-pills" style="padding-bottom: 50px">
                <li class="nav-item">
                  <a class="nav-link active" aria-current="page" href="flussofinanziario?_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario;?>">Flusso finanziario</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?php if(empty($pidrebuilding_flussofinanziario)) echo 'disabled';?>" aria-current="page" href="flussofinanziario-risorse?_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario;?>">Risorse assegnate</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?php if(empty($pidrebuilding_flussofinanziario)) echo 'disabled';?>" aria-current="page" href="flussofinanziario-interventi?_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario;?>">Interventi</a>
                </li>

                <li class="nav-item">
                  <a class="nav-link <?php if(empty($pidrebuilding_flussofinanziario)) echo 'disabled';?>" aria-current="page" href="flussofinanziario-liquidazioni?_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario;?>"><?php echo $label_liquidazioni;?></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?php if(empty($pidrebuilding_flussofinanziario)) echo 'disabled';?>" aria-current="page" href="flussofinanziario-monitoraggio?_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario;?>">Monitoraggio/rendiconto</a>
                </li>
              </ul>



                <form id="RENDICONTAZIONEFORM" name="RENDICONTAZIONEFORM" method="post" role="form" enctype="multipart/form-data" onsubmit="return doOnSubmit()">

                  <?php
                  if($str_error_upload!='')
                  {
                    echo "<div class='alert alert-danger alert-dismissible' role='alert' style='width: 100%;'>
                          <button type='button' class='btn btn-danger btn-xs' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
                          $str_error_upload
                      </div>";  
                      
                  }
                  ?>
                  
                  <div class="form-group row">
                    <div class="col-12 col-md-6">
                        
                        <select class="selectpicker" multiple id="formENTEselect" name="formENTEselect" title="Centro territoriale/ATS*" <?php echo $disabled_scheda;?> data-actions-box="true" >
                          <option value='0'></option>
                          <?php
                            
                            $aentiselezionati=explode(",",$flussofinanziario->flussofinanziario_ente);

                            foreach ($aENTI as $key => $descrizione) 
                            {
                              if(in_array($key,$aentiselezionati))
                                echo '<option value="'.$key.'" selected>'.$descrizione.'</option>';
                              else
                                echo '<option value="'.$key.'">'.$descrizione.'</option>';
                              
                            }
                          ?>                                  
                        </select>
                        <input type="hidden" id="formENTE" name="formENTE" value="">
                      
                    </div>
                    <div class="col-12 col-md-6">

                      <div class="form-floating">
                        <select id="formAREAINTERVENTO" name="formAREAINTERVENTO" class="form-control form-control-flush form-select" required <?php echo $disabled_scheda;?>>
                          <option value='0'></option>
                          <?php                                  
                            foreach ($aTIPOAREA as $key => $descrizione) 
                            {
                              if($key==$flussofinanziario->flussofinanziario_areaintervento)
                                echo '<option value="'.$key.'" selected>'.$descrizione.'</option>';
                              else
                                echo '<option value="'.$key.'">'.$descrizione.'</option>';
                              
                            }
                          ?>                                  
                        </select>
                        <label for="formAREAINTERVENTO">Area/target*</label>
                      </div> 
                    </div>          
                  </div>    

                  <div class="form-group row">

                    <div class="col-12 col-md-2">

                      <div class="form-floating">
                        <select id="formANNO" name="formANNO" class="form-control form-control-flush form-select" required <?php echo $disabled_scheda;?>>
                          <option value='0'></option>
                          <?php
                            
                            
                            foreach ($aANNI as $key => $descrizione) 
                            {
                              if($key==$flussofinanziario->flussofinanziario_anno)
                                echo '<option value="'.$key.'" selected>'.$descrizione.'</option>';                    
                              else  
                                echo '<option value="'.$key.'">'.$descrizione.'</option>';                    
                            }
                          ?>                                  
                        </select>
                        <label for="formANNO">Anno*</label>
                      </div> 
                    </div>

                    <div class="col-12 col-md-10">

                      <div class="form-floating">
                        <select id="formCLASSIFICAZIONE" name="formCLASSIFICAZIONE" class="form-control form-control-flush form-select" required <?php echo $disabled_scheda;?>>
                          <option value='0'></option>
                          <?php
                                            
                            foreach ($aTIPOFONDO as $key => $descrizione) 
                            {
                              if($key==$flussofinanziario->flussofinanziario_tipofondo)
                                echo '<option value="'.$key.'" selected>'.$descrizione.'</option>';                    
                              else
                                echo '<option value="'.$key.'">'.$descrizione.'</option>';
                            }
                          ?>                                  
                        </select>
                        <label for="formCLASSIFICAZIONE">Tipo di fondo*</label>
                      </div> 
                    </div>
                  </div>            

                  <div class="form-group row">

                    <div class="col-12 col-md-2">
                      <div class="form-floating">
                        <input type="text" id="formCODICESIRPS" name="formCODICESIRPS" class="form-control form-control-flush" required placeholder="Inserire il codice SIRPS" value="<?php echo $flussofinanziario->flussofinanziario_codicesirps;?>" <?php echo $disabled_scheda;?> >
                        <label for="formCODICESIRPS">Codice SIRPS</label>
                      </div> 
                    </div>  

                    <div class="col-12 col-md-10">
                      <div class="form-floating">
                        <input type="text" id="formLEGGERIFERIMENTO" name="formLEGGERIFERIMENTO" class="form-control form-control-flush" required placeholder="Inserire il DGR di  riferimento" value="<?php echo $flussofinanziario->flussofinanziario_leggeriferimento;?>" <?php echo $disabled_scheda;?> >
                        <label for="formLEGGERIFERIMENTO">DGR di riferimento*</label>
                      </div> 
                    </div>              
                  </div>      

                  <div class="form-group row">
                    <div class="col-12 col-md-12">
                      <div class="form-floating">
                        <select id="formRUP" name="formRUP" class="form-control form-control-flush form-select" required <?php echo $disabled_scheda;?>>
                          <option value='0'></option>
                          <?php
                                            
                            foreach ($aRUP as $key => $aDATI) 
                            {
                              if(($aDATI['iddara_operatore']==$flussofinanziario->flussofinanziario_rup) || (!$pidrebuilding_flussofinanziario && $aDATI['iddara_operatore']==$idoperatore))
                                echo '<option value="'.$aDATI['iddara_operatore'].'" selected>'.$aDATI['operatore_cognome'].' '.$aDATI['operatore_nome'].'</option>';                    
                              else
                                echo '<option value="'.$aDATI['iddara_operatore'].'">'.$aDATI['operatore_cognome'].' '.$aDATI['operatore_nome'].'</option>';                    
                            }
                          ?>                                  
                        </select>              
                        <label for="formRUP">RUP</label>
                      </div>
                    </div>

                  </div>

                  <div class="form-group row">
                    <div class="col-12 col-md-12">
                      <div class="form-floating">
                        <textarea id="formCONTATTI" name="formCONTATTI" class="form-control form-control-flush" rows="2" <?php echo $disabled_scheda;?>><?php echo $flussofinanziario->flussofinanziario_contatti_rup; ?></textarea>            
                        <label for="formCONTATTI">Contatti RUP</label>
                      </div> 
                    </div>

                  </div>

                  <div class="form-group row">
                    <div class="col-12 col-md-12">
                      <div class="form-floating">
                        <input type="text" id="formTITOLO" name="formTITOLO" class="form-control form-control-flush"  placeholder="" required value="<?php echo $flussofinanziario->flussofinanziario_titolo; ?>" <?php echo $disabled_scheda;?>>
                        <label for="formTITOLO">Titolo*</label>
                      </div> 
                    </div>

                  </div>

                  <div class="form-group row">
                    <div class="col-12 col-md-12">
                      <div class="form-text">
                        <textarea id="formTESTO" name="formTESTO" class="form-control form-control-flush" rows="5" <?php echo $disabled_scheda;?>><?php echo removeslashes($flussofinanziario->flussofinanziario_testo); ?></textarea>            
                        <label for="formTESTO">Testo*</label>
                      </div> 
                    </div>

                  </div>

                  <br>

                  <div class="form-group row">
                    <div class="col-12">
                      <p align="right">
                        <button type="button" class="btn btn-primary btn-xs" onclick="addALLEGATO()" <?php echo $disabled_scheda;?> <?php if(empty($pidrebuilding_flussofinanziario)) echo "style='display:none';"?> ><i class="fe fe-plus"></i>&nbsp; Allegato</button>             
                      </p> 
                    </div>
                  </div>

                  <div id="div_allegati">
                    
                      <?php

                        if(!empty($pidrebuilding_flussofinanziario))
                        {


                          $aDOCUMENTI=$flussofinanziario->getALLEGATI();
                          $counter_allegati=0;
                          $rigadocumento="";
                          foreach ($aDOCUMENTI as $key => $aDATI) 
                          {
                            $idrebuilding_flussofinanziario_documento=$aDATI["idrebuilding_flussofinanziario_documento"];
                            $flussofinanziario_documentotitolo=$aDATI["flussofinanziario_documentotitolo"];
                            $flussofinanziario_documentotipo=$aDATI["flussofinanziario_documentotipo"];
                            $flussofinanziario_documentoente=$aDATI["flussofinanziario_documentoente"];
                            $flussofinanziario_documentonome=$aDATI["flussofinanziario_documentonome"];
                            $counter_allegati++;
                            
                            $entiselezionati=explode(",",$flussofinanziario_documentoente);
                            if((empty($operatore_flagamministratore) && in_array($operatore_ente, $entiselezionati)) || !empty($operatore_flagamministratore))
                            {
                              $rigadocumento.=' <p><div class="row" id="div_allegato'.$counter_allegati.'">
                                    <div class="col-12 col-md-2" >
                                      <input id="nome_file'.$counter_allegati.'" name="nome_file'.$counter_allegati.'" type="file" class="form-control form-control-xs">
                                    </div>
                                    <div class="col-12 col-md-3" >
                                      <input type="text" id="allegatoTITOLO'.$counter_allegati.'" name="allegatoTITOLO'.$counter_allegati.'" class="form-control form-control-text form-control-xs"  placeholder="titolo/descrizione" value="'.$flussofinanziario_documentotitolo.'" >
                                    </div>
                                    <div class="col-12 col-md-3" >
                                      <select id="allegatoTIPO'.$counter_allegati.'" name="allegatoTIPO'.$counter_allegati.'" class="form-control form-control-xs form-select ">
                                        <option value=0>Tipo</option>';
                              foreach ($aTIPODOCUMENTO as $key => $descrizione)
                              { 
                                if($key==$flussofinanziario_documentotipo)
                                  $rigadocumento.='<option value="'.$key.'" selected>'.$descrizione.'</option>';
                                else  
                                  $rigadocumento.='<option value="'.$key.'">'.$descrizione.'</option>';
                              }
                              $rigadocumento.='</select>
                                    </div>
                                    <div class="col-12 col-md-3" >
                                      <select  class="selectpicker" multiple id="allegatoENTEselect'.$counter_allegati.'" name="allegatoENTEselect'.$counter_allegati.'" title="Visibilità" >
                                        <option value=0></option>';
                              foreach ($aENTI as $key => $descrizione)
                              {  
                                if(in_array($key,$entiselezionati))
                                  $rigadocumento.='<option value="'.$key.'" selected>'.addslashes($descrizione).'</option>';
                                else
                                  $rigadocumento.='<option value="'.$key.'">'.addslashes($descrizione).'</option>';
                              }


                              $confirm='<a href=\'#\'><span class=\'badge bg-primary-soft\'>NO</span></a>&nbsp;<a href=\'rebuilding_flussofinanziario.php?_RENDICONTAZIONE='.$aDATI["idrebuilding_flussofinanziario"].'&_allegato='.$idrebuilding_flussofinanziario_documento.'&_eliminaallegato=true\'><span class=\'badge bg-primary-soft\' >SI</span></a>';

                              $rigadocumento.='</select>
                                      <input type="hidden" id="allegatoENTE'.$counter_allegati.'" name="allegatoENTE'.$counter_allegati.'" value="">
                                    </div>
                                    <div class="col-12 col-md-1" >
                                      <button id="consultafile" name="consultafile" type="button" class="btn btn-xs btn-rounded-circle btn-primary" onclick="viewALLEGATO(\''.$target_dir.$flussofinanziario_documentonome.'\')" ><i class="fe fe-file"></i></button>';

                              $rigadocumento.='<button type="button" id="deletefile'.$counter_allegati.'" name="filesassegnazione'.$counter_allegati.'" class="btn btn-xs btn-rounded-circle btn-danger" data-bs-container="body" data-bs-toggle="popover" data-bs-placement="left" title="Confermi l\'eliminazione?" data-bs-content="'.$confirm.'" data-bs-html=true data-bs-trigger="focus"><i class="fe fe-x"></i></button>';
                              
                              $rigadocumento.='</div>
                                  </div>
                                  <input type="hidden" id="_flussofinanziario_documento'.$counter_allegati.'" name="_flussofinanziario_documento'.$counter_allegati.'" value="'.$idrebuilding_flussofinanziario_documento.'">
                                  </p>';                            
                            }

                                
                          }  
                          echo $rigadocumento;
                       }   
                        
                      ?>
                  </div>
                  <br>
                  <div class="form-group row">
                    <div class="col-12 col-md-2">    
                      <div class="form-floating">
                          <select id="formSTATO" name="formSTATO" class="form-control form-control-flush form-select" disabled>                  
                            <?php
                                                  
                              foreach ($aSTATI as $key => $descrizione) 
                              {
                                if($key==$flussofinanziario->flussofinanziario_stato)
                                  echo '<option value="'.$key.'" selected >'.$descrizione.'</option>';                    
                                else  
                                  echo '<option value="'.$key.'">'.$descrizione.'</option>';                    
                              }
                            ?>                                  
                        </select>
                        <label for="formSTATO">VALIDATA</label>
                      </div>               
                    </div>
                    <div class="col-12 col-md-3">    
                      <div class="form-floating">
                        <input type="text" id="formDATACREAZIONE" name="formDATACREAZIONE" class="form-control form-control-flush"  placeholder="" value="<?php echo $flussofinanziario_datainserimento;?>" readonly>
                        <label for="formDATACREAZIONE">Data creazione</label>
                      </div>               
                    </div>
                    <div class="col-12 col-md-4">    
                      <div class="form-floating">
                        <input type="text" id="formOPERATORE" name="formOPERATORE" class="form-control form-control-flush"  placeholder="" value="<?php echo $operatore_nominativo;?>" readonly>
                        <label for="formOPERATORE">Operatore</label>
                      </div>               
                    </div> 
                    <div class="col-12 col-md-3">    
                      <div class="form-floating">
                        <input type="text" id="formULTIMAMODIFICA" name="formULTIMAMODIFICA" class="form-control form-control-flush"  placeholder="" value="<?php echo $flussofinanziario_ultimamodifica;?>" readonly>
                        <label for="formULTIMAMODIFICA">Data ultima modifica</label>
                      </div>               
                    </div>
                  </div>                 

                  <div class="row">
                  <div class="col-12">
                    <button type="button" class="btn w-100 btn-primary-soft mt-3 lift" id="salva" name="salva" onclick="saveRENDICONTAZIONE();" <?php echo $disabled_scheda;?> >Salva</button>

                  </div>
                  </div>
                  <input type="hidden" name="_salva" id="_salva" value="true">
                  <input type="hidden" name="_RENDICONTAZIONE" id="_RENDICONTAZIONE" value="<?php echo $pidrebuilding_flussofinanziario; ?>" >
                  <input type="hidden" id="counter_allegati" name="counter_allegati" value="<?php echo $counter_allegati;?>">
                </form>
              </div>
          </div>
      </div>        
    </div>




    </section>  





    <!-- JAVASCRIPT -->
    <!-- Map JS -->
    <script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
    
    <!-- Vendor JS -->
    <script src="../librerie/assets/js/vendor.bundle.js"></script>
    
    <!-- Theme JS -->
    <script src="../librerie/assets/js/theme.bundle.js"></script>


    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.bundle.min.js"></script>
    <script src="../librerie/js/bootstrap-select.js"></script>

  </body>
</html>



<script>

function apriRENDICONTAZIONE()
{


}


function saveRENDICONTAZIONE()
{

    if($("#formENTEselect").val()=='' || !$("#formTITOLO").val() || !$("#formRUP").val() || !$("#formLEGGERIFERIMENTO").val() || !$("#formAREAINTERVENTO").val() || !$("#formANNO").val())
    {
      $("#liveToast").show()
    }
    else  
      $("#RENDICONTAZIONEFORM").submit() 
 
}

function closeMSG()
{
  $("#liveToast").hide()
}


function addALLEGATO()
{
    var counter_allegati=parseInt($("#counter_allegati").val());

    counter_allegati=counter_allegati+1;
    

    var allegato=' <p><div class="row" id="div_allegato'+counter_allegati+'">\
      <div class="col-12 col-md-2" >\
        <input id="nome_file'+counter_allegati+'" name="nome_file'+counter_allegati+'" type="file" class="form-control form-control-xs">\
      </div>\
      <div class="col-12 col-md-3" >\
        <input type="text" id="allegatoTITOLO'+counter_allegati+'" name="allegatoTITOLO'+counter_allegati+'" class="form-control form-control-text form-control-xs"  placeholder="titolo/descrizione" value="" >\
      </div>\
      <div class="col-12 col-md-3" >\
        <select id="allegatoTIPO'+counter_allegati+'" name="allegatoTIPO'+counter_allegati+'" class="form-control form-control-xs form-select ">\
          <option value=0>Tipo</option><?php foreach ($aTIPODOCUMENTO as $key => $descrizione){ echo '<option value="'.$key.'">'.$descrizione.'</option>';}?>
        </select>\
      </div>\
      <div class="col-12 col-md-3" >\
        <select  class="selectpicker" multiple id="allegatoENTEselect'+counter_allegati+'" name="allegatoENTEselect'+counter_allegati+'" title="Visibilità" >\
          <option value=0></option><?php foreach ($aENTI as $key => $descrizione){ echo '<option value="'.$key.'">'.addslashes($descrizione).'</option>';}?>
        </select>\
        <input type="hidden" id="allegatoENTE'+counter_allegati+'" name="allegatoENTE'+counter_allegati+'" value="">\
        <input type="hidden" id="_flussofinanziario_documento'+counter_allegati+'" name="_flussofinanziario_documento'+counter_allegati+'" value="">\
      </div>\
      <div class="col-12 col-md-1" >\
        <button type="button" id="deletefile'+counter_allegati+'" name="filesassegnazione'+counter_allegati+'"  class="btn btn-xs btn-rounded-circle btn-danger" onclick="deleteALLEGATO('+counter_allegati+')"><i class="fe fe-x"></i></button>\
      </div>\
    </div></p>';

    $("#div_allegati").append(allegato);
    
    $('#allegatoENTEselect'+counter_allegati).selectpicker({ actionsBox:true });

    $("#counter_allegati").val(counter_allegati);
     
}

function deleteALLEGATO(idallegato)
{

  
  



  idDOCUMENTO=$("#_flussofinanziario_documento"+idallegato).val()

  if(idDOCUMENTO)
  {

    var page="rebuilding_action.php";
    var params="_action=deletedocumentoflusso&_documento="+idDOCUMENTO;
    $.ajax({
      type: "POST",
      url: page,
      data: params, 
      dataType: "html",
      success: function(result)
      {
        
        $("#div_allegato"+idallegato).remove();
      },
      error: function()
      {
        console.log("Chiamata fallita, si prega di riprovare...");
      }
    });

  }
  else
  {
    if(confirm("Confermi l'eliminazione?"))
      $("#div_allegato"+idallegato).remove();
  }

  
}

function doOnSubmit()
{
  $("#formENTE").val($("#formENTEselect").val())
  var counter_allegati=parseInt($("#counter_allegati").val());
  for(i=1;i<=counter_allegati;i++)
  {
    $("#allegatoENTE"+i).val($("#allegatoENTEselect"+i).val())
  }

}


function viewALLEGATO(myFILENAME)
{
  window.open(myFILENAME,'_blank')
}


function selezionatutti()
{
  
  $('#formENTEselect').selectpicker('selectAll');

}
</script>

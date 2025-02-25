<?php

require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");
require_once("../librerie/dara.class.servizi.php");

include_once ("../librerie/mail/lib.mail.php");

//error_reporting(0);
//ini_set("display_errors",1);

$idoperatore=verificaUSER();

//$operatore=new DARAOperatore($idoperatore);
//$autorizzazioni_domanda=$operatore->getAUORIZZAZIONEdomanda();

$disabled_notifica='';
$aENTI=array(1=>"ATS1 - Pesaro",3=>"ATS3 - C.M. Catria e Nerone",4=>"ATS4 - Urbino",5=>"ATS5 - C.M. Montefeltro",6=>"ATS6 - Fano",7=>"ATS7 - Fossombrone",8=>"ATS8 - Senigallia",9=>"ATS9 - ASP Ambito 9 Jesi",10=>"ATS10 - Fabriano",11=>"ATS11 - Ancona",12=>"ATS12 - Falconara Marittima",13=>"ATS13 - Osimo",14=>"ATS14 - Civitanova Marche",15=>"ATS15 - Macerata",16=>"ATS16 - C.M. Monti Azzurri",17=>"ATS17 - Unione montanta alta valle del potenza e dell'Esino",18=>"ATS18 - C.M. Camerino",19=>"ATS19 - Fermo",20=>"ATS20 - Porto Sant'Elpidio",21=>"ATS 21 - San Benedetto del Tronto",22=>"ATS22 - Ascoli Piceno",23=>"ATS23 - U.C. Vallata del Tronto",24=>"ATS24 - C.M. dei Sibillini");

$aANNI=array(2017=>"2017",2018=>"2018",2019=>"2019",2020=>"2020",2021=>"2021",2022=>"2022",2023=>"2023",2024=>"2024",2025=>"2025",2026=>"2026", 2027=>"2027");

$aTIPOFONDO=array(1=>"Regionale",2=>"Statale",3=>"FSE",4=>"Misto","Sanitario");
$aTIPODOCUMENTO=array(1=>"RIPARTO",2=>"CRONOPROGRAMMA",3=>"MODULISTICA",4=>"ALTRO");
$aTIPOAREA=array(1=>"Famiglia",2=>"Anziani",3=>"Minori",4=>"Dipendenze",5=>"Disabili",6=>"Adulti e minori sottoposti a provvedimenti Autorità Giudiziaria");

$operatore_flagamministratore=$db->getVALUE("select operatore_flagamministratore from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagamministratore");

$operatori=new DARAOperatore(0);
$aRUP=$operatori->getOPERATORI('operatore_flagamministratore=1 and operatore_flagabilitato=1');

$pidrebuilding_scadenzario=getPARAMETRO("_SCADENZA");
$pidrebuilding_scadenzario=$db->escape_text($pidrebuilding_scadenzario);

$target_dir = "../documenti/rebuilding/toolkit/";
$aentiselezionati=array();
if(getPARAMETRO("_salva"))
{
  
  $pscadenza_destinatario=getPARAMETRO("formENTE");
  $pscadenza_destinatario=$db->escape_text($pscadenza_destinatario);
  $aentiselezionati=explode(",",$pscadenza_destinatario);
  
  $pscadenza_testo=getPARAMETRO("scadenza_testo");
  $pscadenza_testo=$db->escape_text($pscadenza_testo);

  $pscadenza_data=getPARAMETRO("scadenza_data");
  $pscadenza_data=$db->escape_text($pscadenza_data);

  if(empty_data($pscadenza_data))
    $pscadenza_data='0000-00-00';

  $pidrebuilding_flussofinanziario=getPARAMETRO("idrebuilding_flussofinanziario");
  $pidrebuilding_flussofinanziario=$db->escape_text($pidrebuilding_flussofinanziario);

  $pscadenza_stato=1;

  $pscadenza_datainserimento=date("Y-m-d");
  if(!empty($pidrebuilding_scadenzario))
  {   
    $pscadenza_ultimamodifica=date("Y-m-d");

    $sSQL="UPDATE rebuilding_scadenzario  SET
    scadenza_destinatario='$pscadenza_destinatario',
    scadenza_stato='$pscadenza_stato',
    scadenza_testo='$pscadenza_testo',
    scadenza_data='$pscadenza_data',
    idrebuilding_flussofinanziario='$pidrebuilding_flussofinanziario',
    scadenza_ultimamodifica='$pscadenza_ultimamodifica' 
    WHERE idrebuilding_scadenzario='$pidrebuilding_scadenzario'"; 
    $db->query($sSQL);

    $alert_update_success=true;
  }
  else
  {
    
    $pflussofinanziario_orainserimento=date("H:i");
    $sSQL="insert into rebuilding_scadenzario  (scadenza_destinatario,scadenza_stato,scadenza_data,scadenza_testo,idrebuilding_flussofinanziario,scadenza_operatore,scadenza_ultimamodifica) values('$pscadenza_destinatario','$pscadenza_stato','$pscadenza_data','$pscadenza_testo','$pidrebuilding_flussofinanziario','$idoperatore','$pscadenza_datainserimento')";
    $db->query($sSQL);
    $pidrebuilding_scadenzario=$db->insert_id();

    $alert_insert_success=true;
  }


  $sSQL="delete from rebuilding_scadenzario_ente where idrebuilding_scadenzario='$pidrebuilding_scadenzario'";
  $db->query($sSQL);

  foreach ($aentiselezionati as $key => $value) 
  {
      $sSQL="insert into rebuilding_scadenzario_ente (idrebuilding_scadenzario,scadenza_ente) values ('$pidrebuilding_scadenzario','$value')";
      $db->query($sSQL);
  }

  //$sSQL="delete from rebuilding_scadenzario_documento where idrebuilding_scadenzario='$pidrebuilding_scadenzario' and flussofinanziario_documentotime!='$scadenzario_documentotime'";
  //$db->query($sSQL);


}
elseif(getPARAMETRO("_elimina"))
{
  $sSQL="delete from rebuilding_scadenzario where idrebuilding_scadenzario='$pidrebuilding_scadenzario'";
  $db->query($sSQL);
  $sSQL="delete from rebuilding_scadenzario_destinatario where idrebuilding_scadenzario='$pidrebuilding_scadenzario'";
  $db->query($sSQL);
}

$aSTATI=array(1=>"NON SCADUTA",2=>"SCADUTA");


if(!empty($pidrebuilding_scadenzario))
{


  $scadenzario=new rebuildingSCADENZARIO($pidrebuilding_scadenzario);

  $scadenza_datainserimento=dataitaliana($scadenzario->scadenza_datainserimento);
  $scadenza_ultimamodifica=dataitaliana($scadenzario->scadenza_ultimamodifica);

  $operatore=new DARAOperatore($scadenzario->scadenza_operatore);
  $operatore_nominativo=$operatore->operatore_cognome.' '.$operatore->operatore_nome;
  $operatore_nominativo=addslashes($operatore_nominativo);

  $aentiselezionati=explode(",",$scadenzario->scadenza_destinatario);
  if($scadenzario->scadenza_stato==2)
    $disabled_notifica="disabled";

}

$flussofinanziario=new rebuildingFLUSSOFINANZIARIO();
$aFLUSSI=$flussofinanziario->getFLUSSI(' where flussofinanziario_stato=2 ');

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

    $aBREADCUMB[3]["titolo_pagina"]="Scadenzario";
    $aBREADCUMB[3]["url"]="scadenzario";

    $aBREADCUMB[4]["titolo_pagina"]="Scadenza";
    $aBREADCUMB[4]["url"]="";

		generaBREADCUMB($aBREADCUMB);

	  ?>
    
    <section class="bg-light">
      <div class="container-fluid">

        <div class="toast-container d-flex justify-content-center align-items-center w-100">
          <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
              
              <strong class="me-auto">Scadenza</strong>              
              <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close" onclick="closeMSG()"></button>
            </div>
            <div class="toast-body ">
              <span class="text-danger">E' obbligatorio selezionare almeno un ATS</span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12 col-lg-6 col-xl-8 offset-xl-2 py-lg-2 bg-light">            
              <div class="card-body">
                <form id="NOTIFICAFORM" name="NOTIFICAFORM" method="post" role="form" enctype="multipart/form-data" onsubmit="return doOnSubmit()">

                  <div class="form-group row">

                    <div class="col-12 col-md-2">    
                      <div class="form-floating">
                        <input type="date" id="scadenza_data" name="scadenza_data" class="form-control form-control-flush"  placeholder="" value="<?php echo $scadenzario->scadenza_data;?>">
                        <label for="scadenza_data">Data scadenza*</label>
                      </div>               
                    </div>

                    <div class="col-12 col-md-10">
                        
                        <select class="selectpicker" multiple id="formENTEselect" name="formENTEselect" title="Centro territoriale/ATS*" <?php echo $disabled_notifica;?> data-actions-box="true">
                          <option value='0'></option>
                          <?php
                            
                            

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
        
                  </div>    


                  <div class="form-group row">
                    <div class="col-12 col-md-12">
                      <div class="form-text">
                        <textarea id="scadenza_testo" name="scadenza_testo" class="form-control form-control-flush" rows="5" <?php echo $disabled_notifica;?> ><?php echo $scadenzario->scadenza_testo; ?></textarea>            
                        <label for="scadenza_testo">Testo*</label>
                      </div> 
                    </div>

                  </div>

                  <div class="form-group row">
                    <div class="col-12 col-md-12">

                      <div class="form-floating">
                        <select id="idrebuilding_flussofinanziario" name="idrebuilding_flussofinanziario" class="form-control form-control-flush form-select" <?php echo $disabled_notifica;?>>
                          <option value='0'></option>
                          <?php                                  
                            foreach ($aFLUSSI as $key => $aDATI) 
                            {
                              $idrebuilding_flussofinanziario=$aDATI['idrebuilding_flussofinanziario'];
                              $descrizione=$aDATI['flussofinanziario_titolo'];
                              if($idrebuilding_flussofinanziario==$scadenzario->idrebuilding_flussofinanziario)
                                echo '<option value="'.$idrebuilding_flussofinanziario.'" selected>'.$descrizione.'</option>';
                              else
                                echo '<option value="'.$idrebuilding_flussofinanziario.'">'.$descrizione.'</option>';
                              
                            }
                          ?>                                  
                        </select>
                        <label for="idrebuilding_flussofinanziario">Flusso finanziario</label>
                      </div> 
                    </div> 
                  </div>    

                  <br>
                  <div class="form-group row">

                    <div class="col-12 col-md-3">    
                      <div class="form-floating">
                        <input type="text" id="formDATACREAZIONE" name="formDATACREAZIONE" class="form-control form-control-flush"  placeholder="" value="<?php echo $scadenza_ultimamodifica;?>" readonly>
                        <label for="formDATACREAZIONE">Data creazione</label>
                      </div>               
                    </div>
                    <div class="col-12 col-md-6">    
                      <div class="form-floating">
                        <input type="text" id="formOPERATORE" name="formOPERATORE" class="form-control form-control-flush"  placeholder="" value="<?php echo $operatore_nominativo;?>" readonly>
                        <label for="formOPERATORE">Operatore</label>
                      </div>               
                    </div> 
                    <div class="col-12 col-md-3">    
                      <div class="form-floating">
                        <input type="text" id="formULTIMAMODIFICA" name="formULTIMAMODIFICA" class="form-control form-control-flush"  placeholder="" value="<?php echo $scadenza_ultimamodifica;?>" readonly>
                        <label for="formULTIMAMODIFICA">Data ultima modifica</label>
                      </div>               
                    </div>
                  </div>    

                  <div class="row">
                  <div class="col-12">
                    <?php 
                      if(empty($pidrebuilding_scadenzario))
                      {
                          echo '<center><button type="button" class="btn w-100 btn-primary-soft mt-3 lift" id="salva" name="salva" onclick="saveNOTIFICA();">Salva</button></center>';
                      }
                      elseif(!empty($pidrebuilding_scadenzario))
                      {
                          echo '<button type="button" class="btn w-100 btn-primary-soft mt-3 lift" id="salva" name="salva" onclick="saveNOTIFICA();" '.$disabled_notifica.'>Salva</button>';
                      }

                    ?>  
                  </div>
                  </div>
                  <input type="hidden" name="_salva" id="_salva" value="true">
                  <input type="hidden" name="_SCADENZA" id="_SCADENZA" value="<?php echo $pidrebuilding_scadenzario; ?>" >
                  
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

function saveNOTIFICA()
{

    if($("#formENTEselect").val()=='')
    {
      //alert("E' obbligatorio selezionare almeno un destinatario ATS.");
      $("#liveToast").show()
    }
    else  
      $("#NOTIFICAFORM").submit() 
}


function doOnSubmit()
{
  $("#formENTE").val($("#formENTEselect").val())


}



function closeMSG()
{
  $("#liveToast").hide()
}
</script>
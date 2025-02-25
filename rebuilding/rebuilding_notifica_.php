<?php

require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");
require_once("../librerie/dara.class.servizi.php");

include_once ("../librerie/mail/lib.mail.php");

//error_reporting(-1);
//ini_set("display_errors",1);

$idoperatore=verificaUSER();

//$operatore=new DARAOperatore($idoperatore);
//$autorizzazioni_domanda=$operatore->getAUORIZZAZIONEdomanda();

$aENTI=array(1=>"ATS1 - Pesaro",3=>"ATS3 - C.M. Catria e Nerone",4=>"ATS4 - Urbino",5=>"ATS5 - C.M. Montefeltro",6=>"ATS6 - Fano",7=>"ATS7 - Fossombrone",8=>"ATS8 - Senigallia",9=>"ATS9 - ASP Ambito 9 Jesi",10=>"ATS10 - Fabriano",11=>"ATS11 - Ancona",12=>"ATS12 - Falconara Marittima",13=>"ATS13 - Osimo",14=>"ATS14 - Civitanova Marche",15=>"ATS15 - Macerata",16=>"ATS16 - C.M. Monti Azzurri",17=>"ATS17 - Unione montanta alta valle del potenza e dell'Esino",18=>"ATS18 - C.M. Camerino",19=>"ATS19 - Fermo",20=>"ATS20 - Porto Sant'Elpidio",21=>"ATS 21 - San Benedetto del Tronto",22=>"ATS22 - Ascoli Piceno",23=>"ATS23 - U.C. Vallata del Tronto",24=>"ATS24 - C.M. dei Sibillini");

$aANNI=array(2017=>"2017",2018=>"2018",2019=>"2019",2020=>"2020",2021=>"2021",2022=>"2022",2023=>"2023");

$aTIPOFONDO=array(1=>"Regionale",2=>"Statale",3=>"FSE",4=>"Misto","Sanitario");
$aTIPODOCUMENTO=array(1=>"RIPARTO",2=>"CRONOPROGRAMMA",3=>"MODULISTICA",4=>"ALTRO");
$aTIPOAREA=array(1=>"Famiglia",2=>"Anziani",3=>"Minori",4=>"Dipendenze",5=>"Disabili",6=>"Adulti e minori sottoposti a provvedimenti Autorità Giudiziaria");

$operatore_flagamministratore=$db->getVALUE("select operatore_flagamministratore from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagamministratore");

$operatori=new DARAOperatore(0);
$aRUP=$operatori->getOPERATORI('operatore_flagamministratore=1 and operatore_flagabilitato=1');

$pidrebuilding_notifica=getPARAMETRO("_NOTIFICA");
$pidrebuilding_notifica=$db->escape_text($pidrebuilding_notifica);

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
$aentiselezionati=array();
if(getPARAMETRO("_salva"))
{

  $pnotifica_destinatario=getPARAMETRO("formENTE");
  $pnotifica_destinatario=$db->escape_text($pnotifica_destinatario);
  $aentiselezionati=explode(",",$pnotifica_destinatario);
  
  $pnotifica_oggetto=getPARAMETRO("notifica_oggetto");
  $pnotifica_oggetto=$db->escape_text($pnotifica_oggetto);

  $pnotifica_testo=getPARAMETRO("notifica_testo");
  $pnotifica_testo=$db->escape_text($pnotifica_testo);

  $pidrebuilding_flussofinanziario=getPARAMETRO("idrebuilding_flussofinanziario");
  $pidrebuilding_flussofinanziario=$db->escape_text($pidrebuilding_flussofinanziario);

  $pnotifica_stato=1;

  $pnotifica_datainserimento=date("Y-m-d");
  if(!empty($pidrebuilding_notifica))
  {   
    $pnotifica_ultimamodifica=date("Y-m-d");
    //notifica_enti='$pnotifica_enti',
    $sSQL="UPDATE rebuilding_notifica  SET
    notifica_destinatario='$pnotifica_destinatario',
    notifica_stato='$pnotifica_stato',
    notifica_oggetto='$pnotifica_oggetto',
    notifica_testo='$pnotifica_testo',
    idrebuilding_flussofinanziario='$pidrebuilding_flussofinanziario',
    notifica_ultimamodifica='$pnotifica_ultimamodifica' 
    WHERE idrebuilding_notifica='$pidrebuilding_notifica'"; 
    $db->query($sSQL);

    $alert_update_success=true;
  }
  else
  {
    
    $pflussofinanziario_orainserimento=date("H:i");
    $sSQL="insert into rebuilding_notifica  (notifica_destinatario,notifica_stato,notifica_datainserimento,notifica_oggetto,notifica_testo,idrebuilding_flussofinanziario,notifica_operatore,notifica_ultimamodifica) values('$pnotifica_destinatario','$pnotifica_stato','$pnotifica_datainserimento','$pnotifica_oggetto','$pnotifica_testo','$pidrebuilding_flussofinanziario','$idoperatore','$pnotifica_datainserimento')";
    $db->query($sSQL);
    $pidrebuilding_notifica=$db->insert_id();

    $alert_insert_success=true;
  }


  $sSQL="delete from rebuilding_notifica_ente where idrebuilding_notifica='$pidrebuilding_notifica'";
  $db->query($sSQL);

  foreach ($aentiselezionati as $key => $value) 
  {
      $sSQL="insert into rebuilding_notifica_ente (idrebuilding_notifica,notifica_ente) values ('$pidrebuilding_notifica','$value')";
      $db->query($sSQL);
  }

  //$sSQL="delete from rebuilding_notifica_documento where idrebuilding_notifica='$pidrebuilding_notifica' and flussofinanziario_documentotime!='$notifica_documentotime'";
  //$db->query($sSQL);


}
elseif(getPARAMETRO("_invia"))
{

  $notifica=new rebuildingNOTIFICA($pidrebuilding_notifica);
  $aentiselezionati=explode(",",$notifica->notifica_destinatario);
  if(!empty($notifica->idrebuilding_flussofinanziario))
  {

  }


  foreach ($aentiselezionati as $key => $value) 
  {
    $emailATS=$db->getVALUE("SELECT ats_email from rebuilding_ats where idrebuilding_ats='$value'",'ats_email');

    //echo "inviata notifica a ".$value." ".$emailATS." ".$notifica->notifica_oggetto." ".$notifica->notifica_testo."<br>";
    $aEMAILATS=explode(";",$emailATS);

    foreach ($aEMAILATS as $key => $emailATS) 
    {

      $aEMAIL=array();
      $aEMAIL[0]=$emailATS;
      //$aEMAIL[0]="claudio.milani@iccs.it";
      $aEMAIL[1]=$notifica->notifica_oggetto;
      $aEMAIL[2]=$notifica->notifica_testo;
      $aEMAIL[3]="";
      $aEMAIL[4]="";

      $fldmail_result=sendMAIL($aEMAIL);

      if($fldmail_result===true)
      {
            $sSQL="INSERT INTO rebuilding_notifica_email (idrebuilding_notifica,notifica_ente,notifica_email,notifica_esito) value('$pidrebuilding_notifica','$value','$emailATS','2')";
            $db->query($sSQL);
      }
      else
      {
            $sSQL="INSERT INTO rebuilding_notifica_email (idrebuilding_notifica,notifica_ente,notifica_email,notifica_esito) value('$pidrebuilding_notifica','$value','$emailATS','1')";
            $db->query($sSQL);      
      }      

      $data=date("Y-m-d"); 
      $ora=date("H:i:s");
      $oggetto=db_string($notifica->notifica_oggetto);

      $testo=db_string($notifica->notifica_testo);

      $sSQL="INSERT INTO gen_mail_temp 
      (destinatario_mail,mittente_nominativo, mittente_mail, subject, 
      body,data, ora, result,domain)
      VALUES 
      ('$emailATS','Regione Marche - Rebuilding','noreply.rebuilding@regione.marche.it','$oggetto',
      '$testo','$data','$ora','$fldmail_result','iccs.it')";
      $db->query($sSQL);


    }



  }  
  $orario_attuale=date("H:i");
  $pnotifica_ultimamodifica=date("Y-m-d");
  $sSQL="UPDATE rebuilding_notifica  SET
  notifica_stato='2',
  notifica_data='$pnotifica_ultimamodifica' ,
  notifica_ora='$orario_attuale' ,
  notifica_ultimamodifica='$pnotifica_ultimamodifica' 
  WHERE idrebuilding_notifica='$pidrebuilding_notifica'"; 
  $db->query($sSQL);

  $alert_update_success=true;

}
elseif(getPARAMETRO("_elimina"))
{
  $sSQL="delete from rebuilding_notifica where idrebuilding_notifica='$pidrebuilding_notifica'";
  $db->query($sSQL);
  $sSQL="delete from rebuilding_notifica_destinatario where idrebuilding_notifica='$pidrebuilding_notifica'";
  $db->query($sSQL);
}

$aSTATI=array(1=>"NON INVIATA",2=>"INVIATA");


if(!empty($pidrebuilding_notifica))
{

  $notifica=new rebuildingNOTIFICA($pidrebuilding_notifica);

  $notifica_datainserimento=dataitaliana($notifica->notifica_datainserimento);
  $notifica_ultimamodifica=dataitaliana($notifica->notifica_ultimamodifica);

  $operatore=new DARAOperatore($notifica->notifica_operatore);
  $operatore_nominativo=$operatore->operatore_cognome.' '.$operatore->operatore_nome;
  $operatore_nominativo=addslashes($operatore_nominativo);

  $aentiselezionati=explode(",",$notifica->notifica_destinatario);
  if($notifica->notifica_stato==2)
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

    $aBREADCUMB[3]["titolo_pagina"]="Notifiche";
    $aBREADCUMB[3]["url"]="notifiche";

    $aBREADCUMB[4]["titolo_pagina"]="Notifica";
    $aBREADCUMB[4]["url"]="";

		generaBREADCUMB($aBREADCUMB);

	  ?>


    
    <section class="bg-light">
      <div class="container-fluid">

        <div class="toast-container d-flex justify-content-center align-items-center w-100">
          <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
              
              <strong class="me-auto">Notifica</strong>              
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
                    <div class="col-12 col-md-12">
                        
                        <select class="selectpicker" multiple id="formENTEselect" name="formENTEselect" title="Centro territoriale/ATS*" <?php echo $disabled_notifica;?>  data-actions-box="true">
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
                      <div class="form-floating">
                        <input type="text" id="notifica_oggetto" name="notifica_oggetto" class="form-control form-control-flush"  placeholder="" value="<?php echo $notifica->notifica_oggetto; ?>" <?php echo $disabled_notifica; ?>>
                        <label for="notifica_oggetto">Oggetto*</label>
                      </div> 
                    </div>

                  </div>

                  <div class="form-group row">
                    <div class="col-12 col-md-12">
                      <div class="form-text">
                        <textarea id="notifica_testo" name="notifica_testo" class="form-control form-control-flush" rows="5" <?php echo $disabled_notifica;?> ><?php echo $notifica->notifica_testo; ?></textarea>            
                        <label for="notifica_testo">Testo*</label>
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
                              if($idrebuilding_flussofinanziario==$notifica->idrebuilding_flussofinanziario)
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
                    <div class="col-12 col-md-2">    
                      <div class="form-floating">
                          <select id="formSTATO" name="formSTATO" class="form-control form-control-flush form-select" disabled>                  
                            <?php
                                                  
                              foreach ($aSTATI as $key => $descrizione) 
                              {
                                if($key==$notifica->notifica_stato)
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
                        <input type="text" id="formDATACREAZIONE" name="formDATACREAZIONE" class="form-control form-control-flush"  placeholder="" value="<?php echo $notifica_datainserimento;?>" readonly>
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
                        <input type="text" id="formULTIMAMODIFICA" name="formULTIMAMODIFICA" class="form-control form-control-flush"  placeholder="" value="<?php echo $notifica_ultimamodifica;?>" readonly>
                        <label for="formULTIMAMODIFICA">Data ultima modifica</label>
                      </div>               
                    </div>
                  </div>    
                  <?php         

                      if($notifica->notifica_stato==2)
                      {
                          echo '<div class="row">
                                  <div class="col-12 ">


                                      <table class="table table-striped">
                                        <thead class="fs-6">
                                          <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Centro territoriale</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Esito</th>
                                          </tr>
                                        </thead>
                                        <tbody id="post_data" class="fs-6">';                  

                          $sWhere="";

                          $aNOTIFICHEEMAIL=$notifica->getEMAIL();
                          $iCounter=1;
                          foreach ($aNOTIFICHEEMAIL as $key => $aDATI) 
                          {
                            $idrebuilding_notifica_email=$aDATI["idrebuilding_notifica_email"];
                            $notifica_ente=$aDATI["notifica_ente"];
                            $notifica_email=$aDATI["notifica_email"];
                            $notifica_esito=$aDATI["notifica_esito"];
                            $statonotifica=$aSTATI[$notifica_esito];
                           
                            //if($notifica_esito==1 || $notifica_esito=="1")
                            //  $color="#ff0000";
                            //else
                            //  $color="#77dd77";

                            echo '<tr style="color:'.$color.'">';
                            echo '<th>'.$iCounter.'</th>';
                            echo '<th>'.$aENTI[$notifica_ente].'</th>';
                            echo '<th>'.$notifica_email.'</th>';
                            echo '<th>'.$statonotifica.'</th>';
                            echo '</tr>';
                            $iCounter++;
                          }

                          echo '      </tbody>
                              </table>
                            
                          </div>
                        </div>';                         
                    }    

                
                  ?>
                  <div class="row">
                  <div class="col-12">
                    <?php 
                      if(empty($pidrebuilding_notifica))
                      {
                          echo '<center><button type="button" class="btn w-100 btn-primary-soft mt-3 lift" id="salva" name="salva" onclick="saveNOTIFICA();">Salva</button></center>';
                      }
                      elseif(!empty($pidrebuilding_notifica))
                      {
                          $confirm='<a href=\'#\'><span class=\'badge bg-primary-soft\'>NO</span></a>&nbsp;<a href=\'rebuilding_notifica.php?_NOTIFICA='.$pidrebuilding_notifica.'&_invia=true\'><span class=\'badge bg-primary-soft\' >SI</span></a>';

                          echo '<button type="button" class="btn w-50 btn-primary-soft mt-3 lift" id="salva" name="salva" onclick="saveNOTIFICA();" '.$disabled_notifica.'>Salva</button>';
                          echo '<button type="button" class="btn w-50 btn-success-soft mt-3 lift" id="invia" name="invia" data-bs-container="body" data-bs-toggle="popover" data-bs-placement="top" title="Confermi l\'invio della notifica?" data-bs-content="'.$confirm.'" data-bs-html=true data-bs-trigger="focus" '.$disabled_notifica.'>Invia</button>';
                      }

                    ?>  
                  </div>
                  </div>
                  <input type="hidden" name="_salva" id="_salva" value="true">
                  <input type="hidden" name="_NOTIFICA" id="_NOTIFICA" value="<?php echo $pidrebuilding_notifica; ?>" >
                  
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

function viewALLEGATO(myFILENAME)
{
  window.open(myFILENAME,'_blank')
}

function inviaNOTIFICA()
{

}

function closeMSG()
{
  $("#liveToast").hide()
}
</script>
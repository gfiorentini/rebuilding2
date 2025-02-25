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


$aENTI=array(1=>"ATS1 - Pesaro",3=>"ATS3 - C.M. Catria e Nerone",4=>"ATS4 - Urbino",5=>"ATS5 - C.M. Montefeltro",6=>"ATS6 - Fano",7=>"ATS7 - Fossombrone",8=>"ATS8 - Senigallia",9=>"ATS9 - ASP Ambito 9 Jesi",10=>"ATS10 - Fabriano",11=>"ATS11 - Ancona",12=>"ATS12 - Falconara Marittima",13=>"ATS13 - Osimo",14=>"ATS14 - Civitanova Marche",15=>"ATS15 - Macerata",16=>"ATS16 - C.M. Monti Azzurri",17=>"ATS17 - Unione montanta alta valle del potenza e dell'Esino",18=>"ATS18 - C.M. Camerino",19=>"ATS19 - Fermo",20=>"ATS20 - Porto Sant'Elpidio",21=>"ATS 21 - San Benedetto del Tronto",22=>"ATS22 - Ascoli Piceno",23=>"ATS23 - U.C. Vallata del Tronto",24=>"ATS24 - C.M. dei Sibillini");

$aANNI=array(2017=>"2017",2018=>"2018",2019=>"2019",2020=>"2020",2021=>"2021",2022=>"2022",2023=>"2023",2024=>"2024",2025=>"2025",2026=>"2026", 2027=>"2027");

$aTIPOFONDO=array(1=>"Regionale",2=>"Statale",3=>"FSE",4=>"Misto","Sanitario");
$aTIPODOCUMENTO=array(1=>"RIPARTO",2=>"CRONOPROGRAMMA",3=>"MODULISTICA",4=>"ALTRO");
$aTIPOAREA=array(1=>"Famiglia e Minori",2=>"Anziani",3=>"Immigrati e nomadi",4=>"Dipendenze",5=>"Disabili",6=>"Povertà, disagio adulti e senza fissa dimora",7=>"Multiutenza");

$operatore_flagamministratore=$db->getVALUE("select operatore_flagamministratore from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagamministratore");

$operatori=new DARAOperatore(0);
$aRUP=$operatori->getOPERATORI('operatore_flagamministratore=1 and operatore_flagabilitato=1');

$pidrebuilding_rendicontazione=getPARAMETRO("_k");
$pidrebuilding_rendicontazione=$db->escape_text($pidrebuilding_rendicontazione);

$prendicontazione_ente=getPARAMETRO("idrebuilding_ente");
$prendicontazione_ente=$db->escape_text($prendicontazione_ente);

$prendicontazione_inizio=getPARAMETRO("rendicontazione_inizio");
$prendicontazione_inizio=$db->escape_text($prendicontazione_inizio);
if(empty_data($prendicontazione_inizio))
  $prendicontazione_inizio='0000-00-00';

$prendicontazione_fine=getPARAMETRO("rendicontazione_fine");
$prendicontazione_fine=$db->escape_text($prendicontazione_fine);
if(empty_data($prendicontazione_fine))
  $prendicontazione_fine='0000-00-00';

$pidrebuilding_flussofinanziario=getPARAMETRO("idrebuilding_flussofinanziario");
$pidrebuilding_flussofinanziario=$db->escape_text($pidrebuilding_flussofinanziario);
$disabled_scheda='';
if(getPARAMETRO("_salva"))
{

  $pnotifica_stato=1;

  $prendicontazione_datainserimento=date("Y-m-d");
  if(!empty($pidrebuilding_rendicontazione))
  {   
    $rendicontazione_ultimamodifica=date("Y-m-d");

    $sSQL="UPDATE rebuilding_rendicontazione  SET
    rendicontazione_ente='$prendicontazione_ente',
    idrebuilding_flussofinanziario='$pidrebuilding_flussofinanziario',
    rendicontazione_inizio='$prendicontazione_inizio',
    rendicontazione_fine='$prendicontazione_fine',
    rendicontazione_ultimamodifica='$rendicontazione_ultimamodifica' 
    WHERE idrebuilding_rendicontazione='$pidrebuilding_rendicontazione'"; 
    $db->query($sSQL);

    $alert_update_success=true;
  }
  else
  {
    

    $pflussofinanziario_orainserimento=date("H:i");
    $sSQL="insert into rebuilding_rendicontazione  (rendicontazione_ente,rendicontazione_datainserimento,idrebuilding_flussofinanziario,rendicontazione_inizio,rendicontazione_fine,rendicontazione_operatore,rendicontazione_ultimamodifica) values('$prendicontazione_ente','$prendicontazione_datainserimento','$pidrebuilding_flussofinanziario','$prendicontazione_inizio','$prendicontazione_fine','$idoperatore','$prendicontazione_datainserimento')";
    
    $db->query($sSQL);
    $pidrebuilding_rendicontazione=$db->insert_id();

    $alert_insert_success=true;
  }


  $sSQL="delete from rebuilding_rendicontazione_intervento where idrebuilding_rendicontazione='$pidrebuilding_rendicontazione'";
  $db->query($sSQL);

  $counter_interventi=getPARAMETRO("counter_interventi");
  $counter_interventi=$db->escape_text($counter_interventi);
  for($i=1;$i<=$counter_interventi;$i++)
  {
    $titolo=getPARAMETRO("intervento_titolo".$i);
    $titolo=$db->escape_text($titolo);
    $utenti=getPARAMETRO("intervento_utenti".$i);
    $utenti=$db->escape_text($utenti);
    $spesa=getPARAMETRO("intervento_spesa".$i);
    $spesa=$db->escape_text($spesa);
    $intervento_inizio=getPARAMETRO("intervento_inizio".$i);
    $intervento_inizio=$db->escape_text($intervento_inizio);
    $intervento_fine=getPARAMETRO("intervento_fine".$i);
    $intervento_fine=$db->escape_text($intervento_fine);

    $sSQL="insert into rebuilding_rendicontazione_intervento (idrebuilding_rendicontazione,intervento_titolo,intervento_utenti,intervento_spesa,intervento_inizio,intervento_fine) values('$pidrebuilding_rendicontazione','$titolo','$utenti','$spesa','$intervento_inizio','$intervento_fine')";
    $db->query($sSQL);
  }


}
elseif(getPARAMETRO("_elimina"))
{
  $sSQL="delete from rebuilding_rendicontazione where idrebuilding_rendicontazione='$pidrebuilding_rendicontazione'";
  $db->query($sSQL);
  $sSQL="delete from rebuilding_rendicontazione_intervento where idrebuilding_rendicontazione='$pidrebuilding_rendicontazione'";
  $db->query($sSQL);
}

$aSTATI=array(1=>"NO",2=>"SI");


if(!empty($pidrebuilding_rendicontazione))
{


  $rendicontazione=new rebuildingRENDICONTAZIONE($pidrebuilding_rendicontazione);

  $rendicontazione_datainserimento=dataitaliana($rendicontazione->rendicontazione_datainserimento);
  $rendicontazione_ultimamodifica=dataitaliana($rendicontazione->rendicontazione_ultimamodifica);

  $operatore=new DARAOperatore($rendicontazione->rendicontazione_operatore);
  $operatore_nominativo=$operatore->operatore_cognome.' '.$operatore->operatore_nome;
  $operatore_nominativo=addslashes($operatore_nominativo);

  

}

$flussofinanziario=new rebuildingFLUSSOFINANZIARIO();
$aFLUSSI=$flussofinanziario->getFLUSSI(' where flussofinanziario_stato=2 ');
$aTIPOLOGIAINTERVENTO=array();
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
    $aBREADCUMB[1]["url"]="toolkit";

    $aBREADCUMB[2]["titolo_pagina"]="Monitoraggio/rendiconto";
    $aBREADCUMB[2]["url"]="monitoraggio";

    $aBREADCUMB[3]["titolo_pagina"]="Rendiconto";
    $aBREADCUMB[3]["url"]="";

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
                <form id="rendicontoFORM" name="rendicontoFORM" method="post" role="form" enctype="multipart/form-data" onsubmit="return doOnSubmit()">


                  <div class="form-group row">
                    <div class="col-12 col-md-12">

                      <div class="form-floating">
                        <select id="idrebuilding_ente" name="idrebuilding_ente" class="form-control form-control-flush form-select" <?php echo $disabled_rendiconto;?>>
                          <option value='0'></option>
                          <?php                                  
                            foreach ($aENTI as $key => $descrizioneENTE) 
                            {
                              if($key==$rendicontazione->rendicontazione_ente)
                                echo '<option value="'.$key.'" selected>'.$descrizioneENTE.'</option>';
                              else
                                echo '<option value="'.$key.'">'.$descrizioneENTE.'</option>';
                              
                            }
                          ?>                                  
                        </select>
                        <label for="idrebuilding_ente">Centro territoriale</label>
                      </div> 
                    </div> 
                  </div>  

                  <div class="form-group row">
                    <div class="col-12 col-md-12">

                      <div class="form-floating">
                        <select id="idrebuilding_flussofinanziario" name="idrebuilding_flussofinanziario" class="form-control form-control-flush form-select" <?php echo $disabled_rendiconto;?>>
                          <option value='0'></option>
                          <?php                                  
                            foreach ($aFLUSSI as $key => $aDATI) 
                            {
                              $idrebuilding_flussofinanziario=$aDATI['idrebuilding_flussofinanziario'];
                              $descrizione=$aDATI['flussofinanziario_titolo'];
                              if($idrebuilding_flussofinanziario==$rendicontazione->idrebuilding_flussofinanziario)
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

                  <div class="form-group row">
                    <div class="col-12 col-md-12">

                      <div class="form-floating">
                        <select id="idrebuilding_tipologiaintervento" name="idrebuilding_tipologiaintervento" class="form-control form-control-flush form-select" <?php echo $disabled_rendiconto;?>>
                          <option value='0'></option>
                          <?php     

                            foreach ($aTIPOLOGIAINTERVENTO as $key => $aDATI) 
                            {
                              $idrebuilding_flussofinanziario=$aDATI['idrebuilding_flussofinanziario'];
                              $descrizione=$aDATI['flussofinanziario_titolo'];
                              if($idrebuilding_flussofinanziario==$rendicontazione->idrebuilding_flussofinanziario)
                                echo '<option value="'.$idrebuilding_flussofinanziario.'" selected>'.$descrizione.'</option>';
                              else
                                echo '<option value="'.$idrebuilding_flussofinanziario.'">'.$descrizione.'</option>';
                              
                            }
                          ?>                                  
                        </select>
                        <label for="idrebuilding_tipologiaintervento">Tipologia intervento</label>
                      </div> 
                    </div> 
                  </div>   

                  <!--div class="form-group row">
                    <div class="col-12 col-md-3">
                      <div class="form-floating">
                        <input type="date" id="rendicontazione_inizio" name="rendicontazione_inizio" class="form-control form-control-flush"  placeholder="" value="<?php echo $rendicontazione->rendicontazione_inizio; ?>" <?php echo $disabled_rendiconto; ?>>
                        <label for="rendicontazione_inizio">Data inizio*</label>
                      </div> 
                    </div>

                    <div class="col-12 col-md-3">
                      <div class="form-floating">
                        <input type="date" id="rendicontazione_fine" name="rendicontazione_fine" class="form-control form-control-flush"  placeholder="" value="<?php echo $rendicontazione->rendicontazione_fine; ?>" <?php echo $disabled_rendiconto; ?>>
                        <label for="rendicontazione_fine">Data fine*</label>
                      </div> 
                    </div>
                  </div-->
 
                  <br>

                  <div class="form-group row">
                    <div class="col-12">
                      <p align="right">
                        <button type="button" class="btn btn-primary btn-xs" onclick="addINTERVENTO()" <?php echo $disabled_scheda;?>><i class="fe fe-plus"></i>&nbsp; Intervento</button>             
                      </p> 
                    </div>
                  </div>
                  <div id="div_interventi">
                    
                      <?php
                        $counter_interventi=0;
                        if(!empty($pidrebuilding_rendicontazione))
                        {


                          $aINTERVENTI=$rendicontazione->getINTERVENTI();
                          
                          $rigadocumento="";
                          foreach ($aINTERVENTI as $key => $aDATI) 
                          {
                            $idrebuilding_rendicontazione_intervento=$aDATI["idrebuilding_rendicontazione_intervento"];
                            $intervento_titolo=$aDATI["intervento_titolo"];
                            $intervento_utenti=$aDATI["intervento_utenti"];
                            $intervento_spesa=$aDATI["intervento_spesa"];
                            $intervento_inizio=$aDATI["intervento_inizio"];
                            $intervento_fine=$aDATI["intervento_fine"];
                            $counter_interventi++;
                            
                            $rigadocumento.=' <p><div class="row" id="div_intervento'.$counter_interventi.'">
                                  <div class="col-12 col-md-3" >
                                    <input type="text" id="intervento_titolo'.$counter_interventi.'" name="intervento_titolo'.$counter_interventi.'" class="form-control form-control-text form-control-xs"  placeholder="titolo/descrizione" value="'.$intervento_titolo.'" >
                                  </div>
                                  <div class="col-12 col-md-2" >
                                    <input type="text" id="intervento_utenti'.$counter_interventi.'" name="intervento_utenti'.$counter_interventi.'" class="form-control form-control-text form-control-xs"  placeholder="Numero utenti" value="'.$intervento_utenti.'" >
                                  </div>
                                  <div class="col-12 col-md-2" >
                                    <input type="text" id="intervento_spesa'.$counter_interventi.'" name="intervento_spesa'.$counter_interventi.'" class="form-control form-control-text form-control-xs"  placeholder="Spesa" value="'.$intervento_spesa.'" >
                                  </div>
                                  <div class="col-12 col-md-2" >
                                    <input type="date" id="intervento_inizio'.$counter_interventi.'" name="intervento_inizio'.$counter_interventi.'" class="form-control form-control-text form-control-xs"  placeholder="Inizio attività" value="'.$intervento_inizio.'" >
                                  </div>
                                  <div class="col-12 col-md-2" >
                                    <input type="date" id="intervento_fine'.$counter_interventi.'" name="intervento_fine'.$counter_interventi.'" class="form-control form-control-text form-control-xs"  placeholder="Fine attività" value="'.$intervento_fine.'" >
                                  </div>                                
                                  <div class="col-12 col-md-1" >
                                    <button id="deletefile'.$counter_interventi.'" name="filesassegnazione'.$counter_interventi.'"  class="btn btn-xs btn-rounded-circle btn-danger" onclick="deleteINTERVENTO('.$counter_interventi.')" '.$disabled_scheda.'><i class="fe fe-x"></i></button>
                                  </div>
                                </div>
                                <input type="hidden" id="_intervento'.$counter_interventi.'" name="_intervento'.$counter_interventi.'" value="'.$idrebuilding_rendicontazione_intervento.'">
                                </p>';                            
                            

                                
                          }  
                          echo $rigadocumento;
                       }   
                        
                      ?>
                  </div>                  


                  <br>
                  <div class="form-group row">
                    <div class="col-12 col-md-3">    
                      <div class="form-floating">
                        <input type="text" id="formDATACREAZIONE" name="formDATACREAZIONE" class="form-control form-control-flush"  placeholder="" value="<?php echo $rendicontazione_datainserimento;?>" readonly>
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
                        <input type="text" id="formULTIMAMODIFICA" name="formULTIMAMODIFICA" class="form-control form-control-flush"  placeholder="" value="<?php echo $rendicontazione_ultimamodifica;?>" readonly>
                        <label for="formULTIMAMODIFICA">Data ultima modifica</label>
                      </div>               
                    </div>
                  </div>    

                  <div class="row">
                  <div class="col-12">
                    <button type="button" class="btn w-100 btn-primary-soft mt-3 lift" id="salva" name="salva" onclick="saveRENDICONTAZIONE();" <?php echo $disabled_scheda;?> >Salva</button>
                  </div>
                  </div>
                  <input type="hidden" name="_salva" id="_salva" value="">
                  <input type="hidden" name="_k" id="_k" value="<?php echo $pidrebuilding_rendicontazione; ?>" >
                  <input type="hidden" id="counter_interventi" name="counter_interventi" value="<?php echo $counter_interventi;?>">
                  
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

function addINTERVENTO()
{
    var counter_interventi=parseInt($("#counter_interventi").val());

    counter_interventi=counter_interventi+1;
    
    var intervento=' <p><div class="row" id="div_intervento'+counter_interventi+'">\
      <div class="col-12 col-md-3" >\
        <input type="text" id="intervento_titolo'+counter_interventi+'" name="intervento_titolo'+counter_interventi+'" class="form-control form-control-text form-control-xs"  placeholder="Tipologia spesa" value="" >\
      </div>\
      <div class="col-12 col-md-2" >\
        <input type="text" id="intervento_utenti'+counter_interventi+'" name="intervento_utenti'+counter_interventi+'" class="form-control form-control-text form-control-xs"  placeholder="Numero utenti" value="" >\
      </div>\
      <div class="col-12 col-md-2" >\
        <input type="text" id="intervento_spesa'+counter_interventi+'" name="intervento_spesa'+counter_interventi+'" class="form-control form-control-text form-control-xs"  placeholder="Spesa" value="" >\
      </div>\
      <div class="col-12 col-md-2" >\
        <input type="date" id="intervento_inizio'+counter_interventi+'" name="intervento_inizio'+counter_interventi+'" class="form-control form-control-text form-control-xs"  placeholder="Inizio attività" value="" >\
      </div>\
      <div class="col-12 col-md-2" >\
        <input type="date" id="intervento_fine'+counter_interventi+'" name="intervento_fine'+counter_interventi+'" class="form-control form-control-text form-control-xs"  placeholder="Fine attività" value="" >\
      </div>\
      <div class="col-12 col-md-1" >\
        <button type="button" id="deleteintervento'+counter_interventi+'" name="deleteintervento'+counter_interventi+'"  class="btn btn-xs btn-rounded-circle btn-danger" onclick="deleteINTERVENTO('+counter_interventi+')"><i class="fe fe-x"></i></button>\
      </div>\
    </div></p>';

    $("#div_interventi").append(intervento);
    
    $("#counter_interventi").val(counter_interventi);
     
}

function saveRENDICONTAZIONE()
{
  $("#_salva").val('true')
  $("#rendicontoFORM").submit() 
}

function deleteINTERVENTO(idintervento)
{
  idINTERVENTO=$("#_intervento"+idintervento).val()

  if(idINTERVENTO)
  {

    var page="rebuilding_action.php";
    var params="_action=deleteintervento&_intervento="+idINTERVENTO;
    $.ajax({
      type: "POST",
      url: page,
      data: params, 
      dataType: "html",
      success: function(result)
      {
        
        $("#div_intervento"+idintervento).remove();
      },
      error: function()
      {
        console.log("Chiamata fallita, si prega di riprovare...");
      }
    });

  }
  else
    $("#div_intervento"+idintervento).remove();
}

function doOnSubmit()
{
  var counter_interventi=parseInt($("#counter_interventi").val());

}
</script>
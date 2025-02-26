<?php

require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");
require_once("../librerie/dara.class.servizi.php");

use EXCEL\SimpleXLSX;
//require_once("../librerie/lib.excell.php");
//require_once ("../librerie/PHPExcel/IOFactory.php");
//require_once ("../librerie/simplexlsx.class.php");
//require_once ("../librerie/easyODS.php");
require_once("../librerie/SimpleXLSX.php");

//error_reporting(-1);
//ini_set("display_errors",1);

$idoperatore=verificaUSER();

$operatore=new DARAOperatore($idoperatore);
$operatore_ente=$operatore->operatore_ente;
if(empty($operatore_ente))
  $operatore_ente=9999;
$operatore_flagamministratore=$db->getVALUE("select operatore_flagamministratore from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagamministratore");
$operatore_flagrup=$db->getVALUE("select operatore_flagrup from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagrup");
$operatore_flagdirigente=$db->getVALUE("select operatore_flagdirigente from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagdirigente");


$aENTI=array(1=>"ATS1 - Pesaro",3=>"ATS3 - C.M. Catria e Nerone",4=>"ATS4 - Urbino",5=>"ATS5 - C.M. Montefeltro",6=>"ATS6 - Fano",7=>"ATS7 - Fossombrone",8=>"ATS8 - Senigallia",9=>"ATS9 - ASP Ambito 9 Jesi",10=>"ATS10 - Fabriano",11=>"ATS11 - Ancona",12=>"ATS12 - Falconara Marittima",13=>"ATS13 - Osimo",14=>"ATS14 - Civitanova Marche",15=>"ATS15 - Macerata",16=>"ATS16 - C.M. Monti Azzurri",17=>"ATS17 - Unione montanta alta valle del potenza e dell'Esino",17=>"ATS 17 - UNIONE MONTANA ALTE VALLI DEL POTENZA E DELL'ESINO",18=>"ATS18 - C.M. Camerino",19=>"ATS19 - Fermo",20=>"ATS20 - Porto Sant'Elpidio",21=>"ATS 21 - San Benedetto del Tronto",22=>"ATS22 - Ascoli Piceno",23=>"ATS23 - U.C. Vallata del Tronto",24=>"ATS24 - C.M. dei Sibillini");


$aENTI_import=array(1=>"ATS 1",3=>"ATS 3",4=>"ATS 4",5=>"ATS 5",6=>"ATS 6",7=>"ATS 7",8=>"ATS 8",9=>"ATS 9",10=>"ATS 10",11=>"ATS 11",12=>"ATS 12",13=>"ATS 13",14=>"ATS 14",15=>"ATS 15",16=>"ATS 16",17=>"ATS 17",18=>"ATS 18",19=>"ATS 19",20=>"ATS 20",21=>"ATS 21",22=>"ATS 22",23=>"ATS 23",24=>"ATS 24");

$centroterritorialeOPERATORE=$aENTI[$operatore_ente];

$operatori=new DARAOperatore(0);
$aRUP=$operatori->getOPERATORI('operatore_flagamministratore=1 and operatore_flagabilitato=1 and operatore_flagrup=1');


$pidrebuilding_flussofinanziario=getPARAMETRO("_RENDICONTAZIONE");
$pidrebuilding_flussofinanziario=$db->escape_text($pidrebuilding_flussofinanziario);

$flussofinanziario=new rebuildingFLUSSOFINANZIARIO($pidrebuilding_flussofinanziario);
$flussofinanziario_ente=$flussofinanziario->flussofinanziario_ente;
$aENTISELEZIONATI=explode(",",$flussofinanziario_ente);

$disable_importaxls="";
if(empty($operatore_flagamministratore))
{
  $aENTISELEZIONATI=array();
  $aENTISELEZIONATI[$operatore_ente]=$operatore_ente;

  $disable_importaxls="disabled";


}

$risorseassegnate=$flussofinanziario->getRISORSE();
$aRISORSEASSEGNATE=array();
foreach ($risorseassegnate as $key => $aRISORSE) 
{
  $aRISORSEASSEGNATE[$aRISORSE['risorsa_ente']]=$aRISORSE['risorsa_assegnata'];
}

if(getPARAMETRO("_salva") && $operatore_flagamministratore==1 && empty($operatore_flagdirigente))
{
  $data=date("Y-m-d");
  foreach ($aENTISELEZIONATI as $key => $idente) 
  {
    $risorsaassegnata=getPARAMETRO("risorse".$idente);
    $risorsaassegnata=$db->escape_text($risorsaassegnata);
    $risorsaassegnata = str_replace(",", ".", $risorsaassegnata);

    if(empty($risorsaassegnata))
      $risorsaassegnata=0;

    // Verifico se esiste il record
    if(array_key_exists($idente,$aRISORSEASSEGNATE))
    {
      $sSQL="UPDATE rebuilding_flussofinanziario_risorsa set risorsa_assegnata='$risorsaassegnata',risorsa_ultimamodifica='$data',risorsa_operatore='$idoperatore' where idrebuilding_flussofinanziario='$pidrebuilding_flussofinanziario' and risorsa_ente='$idente'";
      $db->query($sSQL);
    }
    else  
    {
      $sSQL="insert into rebuilding_flussofinanziario_risorsa (idrebuilding_flussofinanziario,risorsa_ente,risorsa_assegnata,risorsa_datainserimento,risorsa_ultimamodifica,risorsa_operatore) values('$pidrebuilding_flussofinanziario','$idente','$risorsaassegnata','$data','$data','$idoperatore')";
      $db->query($sSQL);
    }  

  }

}

if(getPARAMETRO("_import_file"))
{
  if(!is_dir('../documenti/import'))
    mkdir("../documenti/import", 0777);

  $alert_file = false;
  $pfile = $_FILES["nome_file"]["tmp_name"];

  if ($pfile) {
    $pfile_name = $_FILES["nome_file"]["name"];
    $flddata = date("Y-m-d");

    if (!is_uploaded_file($_FILES["nome_file"]["tmp_name"]) || $_FILES["nome_file"]["error"] > 0)
        $alert_file = true;
    
    if ($_FILES["nome_file"]['size'] > 10000000)    // 10 Mb
        $alert_file = true;

    $ext = pathinfo($pfile_name, PATHINFO_EXTENSION);
    $fldfilename = '_import_risorse_fonti_' . date("YmdHis") . '.' . $ext;
    $upload_dir="../documenti/import/";
    copy($_FILES["nome_file"]["tmp_name"], $upload_dir . $fldfilename);

    //if (!file_exists($upload_dir . $fldfilename))
      //echo "FILE NON COPIATO";
    $alert_formato=false;
    switch ($ext) {
        case "xlsx":
          $alert_success=false;
          if ($xlsx = SimpleXLSX::parse($pfile)) 
          {
            $righe_salto=2;
            foreach ($xlsx->rows() as $row) 
            {
              if ($counter > $righe_salto) 
              {
                $ats_completo=strtoupper($row[4]);
                $partita_iva=$row[5];
                $risorsaassegnata=$row[7];
      
                list($ats_codice,$ats_descrizione)=explode("-",$ats_completo);
                $ats_codice=trim($ats_codice);
                $idente=array_search($ats_codice,$aENTI_import);
                
                if(!empty($idente) && in_array($idente,$aENTISELEZIONATI))
                {
                    if(empty($risorsaassegnata))
                      $risorsaassegnata=0;
      
                    // Verifico se esiste il record
                    if(array_key_exists($idente,$aRISORSEASSEGNATE))
                    {
                      $sSQL="UPDATE rebuilding_flussofinanziario_risorsa set risorsa_assegnata='$risorsaassegnata',risorsa_ultimamodifica='$flddata',risorsa_operatore='$idoperatore' where idrebuilding_flussofinanziario='$pidrebuilding_flussofinanziario' and risorsa_ente='$idente'";
                      $db->query($sSQL);
                      //echo "<br>";
                    }
                    else  
                    {
                      $sSQL="INSERT INTO rebuilding_flussofinanziario_risorsa (idrebuilding_flussofinanziario,risorsa_ente,risorsa_assegnata,risorsa_datainserimento,risorsa_ultimamodifica,risorsa_operatore) values('$pidrebuilding_flussofinanziario','$idente','$risorsaassegnata','$flddata','$flddata','$idoperatore')";
                      $db->query($sSQL);
                      
                    }  
                    
                    $alert_success=true;
                }    
                
              }
              $counter++;
              
            }
            
          } 
          else 
          {
              echo SimpleXLSX::parseError();
          }

        break;
        case "xls":
        
          $alert_formato=true;
          /*
          $data = new Spreadsheet_Excel_Reader($pfile);
          
          $array = getDataInArray($data);
          print_r($array);
          die;
          $counter = 0;
          $counter_insert = 0;
          $righe_salto = 1;
          foreach ($array as $row) {
            print_r_formatted($row);
            die;
            if ($counter > $righe_salto) {

            }
          }
          */
        break;
    }
  }

}

$aRISORSEASSEGNATE=array();
// GF
$aRISORSE_LIQUIDATE=array();
$aRISORSE_RESTITUITE=array();
$aRISORSE_ECONOMIA=array();
//


if(!empty($pidrebuilding_flussofinanziario))
{
  $flussofinanziario=new rebuildingFLUSSOFINANZIARIO($pidrebuilding_flussofinanziario);
  if(!empty($operatore_flagamministratore))
  {
    $risorseassegnate=$flussofinanziario->getRISORSE();
  }
  else
  {
   $risorseassegnate=$flussofinanziario->getRISORSE(" and risorsa_ente='$operatore_ente'"); 
  }
  
  foreach ($risorseassegnate as $key => $aRISORSE) 
  {
    $aRISORSEASSEGNATE[$aRISORSE['risorsa_ente']]=$aRISORSE['risorsa_assegnata'];
    // GF
    $aRISORSE_LIQUIDATE[$aRISORSE['risorsa_ente']]=$aRISORSE['risorsa_liquidata'];
    $aRISORSE_RESTITUITE[$aRISORSE['risorsa_ente']]=$aRISORSE['risorsa_rimborsata'];
    $aRISORSE_ECONOMIA[$aRISORSE['risorsa_ente']]=$aRISORSE['risorsa_economia'];
    //
    //$disabled_salva="disabled";
  }

}
$disabled_salva='';
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

function getDataInArray($data)
{
    for ($row = 1; $row <= $data->rowcount(); $row++) {
        for ($col = 1; $col <= $data->colcount(); $col++) {
            $out[$row][$col] = $data->val($row, $col);
        }
    }
    return $out;
}
?>
<!doctype html>
<html lang="it">
  <head>
  	 <?php echo getREBUILDINGHEAD(true); ?>

     <link rel="stylesheet" href="../librerie/css/bootstrap-select.css">
     <link rel="stylesheet" type="text/css" href="../librerie/css/bootstrap-dialog.min.css">

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
        <form action="rebuilding_flussofinanziario_risorse.php" method="post" name="flusso_finanziario" id="flusso_finanziario" class="form-horizontal" enctype="multipart/form-data">
        <!-- Modal -->
        <?php
        
          if($alert_success) echo(get_alert(4,'Flusso di finanziamento importato correttamente'));	
          if($alert_formato) echo(get_alert(0,'ATTENZIONE - Il formato deve essere .xlsx - Flusso non importato'));


        ?>
          <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal_uploadLabel" aria-hidden="true" id="modal_upload" name="modal_upload" >
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header bg-primary text-light">
                  <h3 class="modal-title " id="modal_uploadLabel">Risorse fonte di finanziamento</h3>
                </div>
                <div class="modal-body">
                  <div class="row border-bottom">
                    <label class="form-label secondary" for="nome_file">Seleziona tipo di file</label>
                    <select class=" form-select" id="tipo_file" name="tipo_file" >
                      <option value="impegnato">Impegni</option>
                      <option value="liquidato">Liquidazioni</option>
                      <option value="restituito">Restituito</option>
                      <option value="economia">Economie</option>
                    </select>
                  </div>
                  <div class="row border-bottom" style="margin-top: 1rem;">
                    <label class="form-label" for="nome_file">Seleziona il file da elaborare</label>
                    <input type="file" class="form-control" id="nome_file" name="nome_file" value=""/>                  
                  </div>
                </div>
                <div class="modal-footer">
                  <hr>
                  <button type="button" class="btn btn-secondary" id="chiud_modal" name="chiud_modal" data-dismiss="modal">Chiudi</button>
                  <button type="button" class="btn btn-primary" value="true" id="importa" name="importa">Elabora</button>
                </div>
              </div>
            </div>
            <input type="hidden" class="form-control" id="_import_file" name="_import_file"  value=""/>                  

          </div>
      
          <section class="bg-light">
            <div class="container-fluid">
              <div class="row">
                <div class="col-12 col-lg-6 col-xl-8 offset-xl-2 py-lg-2 bg-light">            
                    <div class="card-body">

                      <ul class="nav nav-pills" style="padding-bottom: 50px">
                        <li class="nav-item">
                          <a class="nav-link " aria-current="page" href="flussofinanziario?_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario;?>">Flusso finanziario</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link active" aria-current="page" href="flussofinanziario-risorse?_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario;?>">Risorse assegnate</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" aria-current="page" href="flussofinanziario-interventi?_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario;?>">Interventi</a>
                        </li>


                        <!-- <li class="nav-item">
                          <a class="nav-link " aria-current="page" href="flussofinanziario-liquidazioni?_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario;?>"><?php echo $label_liquidazioni;?></a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link " aria-current="page" href="flussofinanziario-monitoraggio?_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario;?>">Monitoraggio/rendiconto</a>
                        </li> -->


                      </ul>

                        <!-- ELENCO -->
                      <form id="formRISORSE" name="formRISORSE" method="post">
                        <div class="row">
                          <div class="col-12">    
                              <p align="right">
                                  <button type="button" class="btn btn-sm btn-primary-soft  btn-xs" data-toggle="modal" data-target="#modal_upload" <?php echo $disable_importaxls; ?>><i class="fe fe-list"></i>&nbsp;Importa da excel</button>
                              </p>
                          </div>  
                        </div> 

                      <div class="table-responsive mb-7 mb-md-3">

                          <table class="table table-bordered " id="tblREPORT" name="tblREPORT">
                            <thead class="fs-4">
                              <tr>
                                <th scope="col" style="width: 5%">#</th>
                                <th scope="col" style="width: 25%">Centro territoriale</th>
                                <th scope="col" style="width: 15%">Somme impegnate</th>
                                <th scope="col" style="width: 15%">Somme liquidate</th>
                                <th scope="col" style="width: 15%">Somme restituite</th>
                                <th scope="col" style="width: 15%">Somme economia</th>
                              </tr>
                            </thead>
                            <tbody id="post_data" class="fs-5">
                              <?php

                                $iCounter=1;

                                foreach ($aENTISELEZIONATI as $key => $idente) 
                                {

                                  //if($disabled_scheda)
                                    $inputRISORSE='<input type="text" id="risorse'.$idente.'" name="risorse'.$idente.'" class="form-control" placeholder="0.00" value="'.number_format($aRISORSEASSEGNATE[$idente],2,",",".").'" readonly >';
                                    $inputLIQUIDATO='<input type="text" id="liquidato'.$idente.'" name="liquidato'.$idente.'" class="form-control" placeholder="0.00" value="'.number_format($aRISORSE_LIQUIDATE[$idente],2,",",".").'" readonly >';
                                    $inputRESTITUITO='<input type="text" id="restituito'.$idente.'" name="restituito'.$idente.'" class="form-control" placeholder="0.00" value="'.number_format($aRISORSE_RESTITUITE[$idente],2,",",".").'" readonly >';
                                    $inputECONOMIA='<input type="text" id="economia'.$idente.'" name="economia'.$idente.'" class="form-control" placeholder="0.00" value="'.number_format($aRISORSE_RESTITUITE[$idente],2,",",".").'" readonly >';
                                  //else
                                  //  $inputRISORSE='<input type="text" id="risorse'.$idente.'" name="risorse'.$idente.'" class="form-control" placeholder="0.00" value="'.$aRISORSEASSEGNATE[$idente].'" '.$disabled_scheda.' >';


                                  echo '<tr>';
                                  echo '<td>'.$iCounter.'</td>';
                                  echo '<td>'.$aENTI[$idente].'</td>';
                                  echo '<td>'.$inputRISORSE.'</td>';
                                  echo '<td>'.$inputLIQUIDATO.'</td>';
                                  echo '<td>'.$inputRESTITUITO.'</td>';
                                  echo '<td>'.$inputECONOMIA.'</td>';
                                  
                                  echo '</tr>';
                                  $iCounter++;
                                }


                              ?>
                            </tbody>
                        </table>
                      </div>

                        <div class="col-12">
                          <!--button type="submit" class="btn w-100 btn-primary-soft mt-3 lift" id="_salva" name="_salva" value="true"  <?php echo $disabled_scheda;?> >Salva</button-->
                        </div>

                        <input type="hidden" name="_RENDICONTAZIONE" id="_RENDICONTAZIONE" value="<?php echo $pidrebuilding_flussofinanziario; ?>" >

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
          <script language="javascript" type="text/javascript" src="../librerie/js/bootstrap-dialog.min.js"></script>   
          </form>
  </body>
</html>



<script>


$(document).ready(function() {


});

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
          <option value=0>Tipo</option><?php foreach ($aTIPODOCUMENTO as $key => $descrizione){ echo '<option value="'.$key.'">'.$descrizione.'</option>';}?>\
        </select>\
      </div>\
      <div class="col-12 col-md-3" >\
        <select  class="selectpicker" multiple id="allegatoENTEselect'+counter_allegati+'" name="allegatoENTEselect'+counter_allegati+'" title="Visibilità" >\
          <option value=0></option><?php foreach ($aENTI as $key => $descrizione){ echo '<option value="'.$key.'">'.addslashes($descrizione).'</option>';}?>\
        </select>\
        <input type="hidden" id="allegatoENTE'+counter_allegati+'" name="allegatoENTE'+counter_allegati+'" value="">\
        <input type="hidden" id="_flussofinanziario_documento'+counter_allegati+'" name="_flussofinanziario_documento'+counter_allegati+'" value="">\
      </div>\
      <div class="col-12 col-md-1" >\
        <button id="deletefile'+counter_allegati+'" name="filesassegnazione'+counter_allegati+'"  class="btn btn-xs btn-rounded-circle btn-danger" onclick="deleteALLEGATO('+counter_allegati+')"><i class="fe fe-x"></i></button>\
      </div>\
    </div></p>';

    $("#div_allegati").append(allegato);
    
    $('#allegatoENTEselect'+counter_allegati).selectpicker({ actionsBox:false });

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
    $("#div_allegato"+idallegato).remove();
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

$("#importa").click(function() {
  
  var file = $("#nome_file").val();
  
  if(file=='' || file== null || file==undefined)
  {
    //$("#chiud_modal").hide()
    $("#chiud_modal").trigger('click');
    BootstrapDialog.show({
				title: 'Attenzione',
				closable: false,
				size: BootstrapDialog.SIZE_NORMAL,
				type: BootstrapDialog.TYPE_WARNING, 
				message: "Selezionare il file da caricare",
				buttons: [
				{
					label: 'OK',
            action: function(dialogItself){
                dialogItself.close();
					}
				}]
			});
  }
  else
  {
    $("#_import_file").val("true");
    $("#flusso_finanziario").submit();
  }
   

  return false;
  var idrebuilding_flussofinanziario = $("#_RENDICONTAZIONE").val();
  
  
   
    //var page="rebuilding_flussofinanziario_risorse.php";
    //var params="?_import_file=true&_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario ?>&nome_file="+file;
    //window.location=(page+params); 
   
  
  /*
  BootstrapDialog.show({
    title: 'Elaborazione in corso',
    size: BootstrapDialog.SIZE_NORMAL,
    type: BootstrapDialog.TYPE_PRIMARY, 
    closable: false,
    message: '<br><center><table><tr><td><i class=\"fa fa-spinner fa-spin\" style=\"font-size:24px;\"></i></td><td>&nbsp;&nbsp;&nbsp;</td><td><b>Elaborazione in corso<br>Si prega di attendere...</b></td></tr></table></center><br><br><br>'
  });
  */







  /*
  if(!isEmpty(file))
  {
    var page="rebuilding_flussofinanziario_importaxls.php";
    var params="_RENDICONTAZIONE="+idrebuilding_flussofinanziario+"&_documento="+file;
    alert(page+params)
    $.ajax({
      type: "POST",
      url: page,
      data: params, 
      dataType: "html",
      success: function(result)
      {
        
      
      },
      error: function()
      {
        console.log("Chiamata fallita, si prega di riprovare...");
      }
    });
  }
  else
  {
    $("#modal_upload").hide();

    modal_upload
    BootstrapDialog.show({
				title: 'Attenzione',
				closable: false,
				size: BootstrapDialog.SIZE_NORMAL,
				type: BootstrapDialog.TYPE_INFO, 
				message: "Selezionare il file da caricare",
				buttons: [
				{
					label: 'OK',
            action: function(dialogItself){
                dialogItself.close();
					}
				}]
			});
  }
  */




  
})


function importaXLS()
{

}


</script>

<?php

require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");
require_once("../librerie/dara.class.servizi.php");
/*
error_reporting(0);
*/
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

$centroterritorialeOPERATORE=$aENTI[$operatore_ente];

$operatori=new DARAOperatore(0);
$aRUP=$operatori->getOPERATORI('operatore_flagamministratore=1 and operatore_flagabilitato=1 and operatore_flagrup=1');

$pidrebuilding_flussofinanziario=getPARAMETRO("_RENDICONTAZIONE");
$pidrebuilding_flussofinanziario=$db->escape_text($pidrebuilding_flussofinanziario);

$pidrebuilding_flussofinanziario_intervento=getPARAMETRO("_k");
$pidrebuilding_flussofinanziario_intervento=$db->escape_text($pidrebuilding_flussofinanziario_intervento);

$flussofinanziario=new rebuildingFLUSSOFINANZIARIO($pidrebuilding_flussofinanziario);
$flussofinanziario_ente=$flussofinanziario->flussofinanziario_ente;
$aENTISELEZIONATI=explode(",",$flussofinanziario_ente);
$showaccordion="";
if(empty($operatore_flagamministratore))
{
  $aENTISELEZIONATI=array();
  $aENTISELEZIONATI[$operatore_ente]=$operatore_ente;
  $showaccordion="show";
}

if(getPARAMETRO("_salva") && $operatore_flagamministratore==1 && empty($operatore_flagdirigente))
{
  $data=date("Y-m-d");
}

$disabled_salva='';
$disabled_scheda="";

if(empty($operatore_flagamministratore))
{
  $aLIQUIDAZIONI=$flussofinanziario->getLIQUIDAZIONI(" and liquidazione_ente='".$operatore_ente."' ");
  if(!is_array($aLIQUIDAZIONI))
    $aLIQUIDAZIONI=array();
  $disabled_scheda="disabled";
}
else
{

  $aLIQUIDAZIONI=$flussofinanziario->getLIQUIDAZIONI();
}
//print_r_formatted($aLIQUIDAZIONI);
$aINTERVENTIFLUSSO=$flussofinanziario->getINTERVENTI();
//print_r_formatted($aINTERVENTIFLUSSO);

$aTIPOAREA=array(1=>"Famiglia",2=>"Anziani",3=>"Minori",4=>"Dipendenze",5=>"Disabili",6=>"Adulti e minori sottoposti a provvedimenti Autorità Giudiziaria");
$aTIPOSPESA=$db->select("select idrebuilding_tipologiaspesa,tipologiaspesa_descrizione from rebuilding_tipologiaspesa order by idrebuilding_tipologiaspesa ");
$aINTERVENTI=$db->select("select idrebuilding_flussofinanziario_intervento,intervento_titolo,intervento_codice from rebuilding_flussofinanziario_intervento order by idrebuilding_flussofinanziario_intervento ");
$aSPESA=$db->select("select idrebuilding_tipologiaspesa,tipologiaspesa_descrizione from rebuilding_tipologiaspesa order by idrebuilding_tipologiaspesa");

$aDESCRIZIONEINTERVENTO=array();
foreach ($aINTERVENTI as $key => $aDATI) 
{
  $aDESCRIZIONEINTERVENTO[$aDATI['idrebuilding_flussofinanziario_intervento']]=$aDATI['intervento_codice'].' '.$aDATI['intervento_titolo'];
}

$aDESCRIZIONESPESA=array();
foreach ($aSPESA as $key => $aDATI) 
{
  $aDESCRIZIONESPESA[$aDATI['idrebuilding_tipologiaspesa']]=$aDATI['tipologiaspesa_descrizione'];
}

$aMONITORAGGIO=array();
$aRIEPILOGO=array();
foreach ($aLIQUIDAZIONI as $key => $aDATI) 
{
  $aMONITORAGGIO[$aDATI['liquidazione_ente']][$aDATI['idrebuilding_flussofinanziario_intervento'].'|'.$aDATI['idrebuilding_tipologiaspesa']]['beneficiari']+=$aDATI['liquidazione_beneficiari'];
  $aMONITORAGGIO[$aDATI['liquidazione_ente']][$aDATI['idrebuilding_flussofinanziario_intervento'].'|'.$aDATI['idrebuilding_tipologiaspesa']]['importo']+=$aDATI['liquidazione_importo'];

  $aRIEPILOGO[$aDATI['idrebuilding_flussofinanziario_intervento'].'|'.$aDATI['idrebuilding_tipologiaspesa']]['beneficiari']+=$aDATI['liquidazione_beneficiari'];
  $aRIEPILOGO[$aDATI['idrebuilding_flussofinanziario_intervento'].'|'.$aDATI['idrebuilding_tipologiaspesa']]['importo']+=$aDATI['liquidazione_importo'];

  $aRIEPILOGOATS[$aDATI['liquidazione_ente']]['beneficiari']+=$aDATI['liquidazione_beneficiari'];
  $aRIEPILOGOATS[$aDATI['liquidazione_ente']]['importo']+=$aDATI['liquidazione_importo'];  
}

$risorseassegnate=$flussofinanziario->getRISORSE();
$aRISORSEASSEGNATE=array();
foreach ($risorseassegnate as $key => $aRISORSE) 
{
  $aRISORSEASSEGNATE[$aRISORSE['risorsa_ente']]=$aRISORSE['risorsa_assegnata'];
}

// ordinare per intervento e tipologia spesa - aggiungere codice intervento
// nel caso di ats aprire subito l'accordion

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

    <script language="javascript" type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script language="javascript" type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script language="javascript" type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script language="javascript" type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script language="javascript" type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script language="javascript" type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>


  
      <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    
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

    $aBREADCUMB[4]["titolo_pagina"]="Interventi";
    $aBREADCUMB[4]["url"]="";

		generaBREADCUMB($aBREADCUMB);

	  ?>
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
                    <a class="nav-link " aria-current="page" href="flussofinanziario-risorse?_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario;?>">Risorse assegnate</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link " aria-current="page" href="flussofinanziario-interventi?_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario;?>">Interventi</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link " aria-current="page" href="flussofinanziario-liquidazioni?_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario;?>"><?php echo $label_liquidazioni;?></a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link active " aria-current="page" href="flussofinanziario-monitoraggio?_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario;?>">Monitoraggio/rendiconto</a>
                  </li>

                </ul>
                <div class="table-responsive mb-7 mb-md-3">
                  <div class="accordion accordion-flush" id="accordionENTI">
                      <?php 

                          $totalebeneficiariFLUSSO=0;
                          $totaleimportoFLUSSO=0;
                          $totalemediaFLUSSO=0;
                          
                          foreach ($aENTISELEZIONATI as $key => $valueENTE) 
                          {
                              echo '<div class="accordion-item">
                                <h2 class="accordion-header">
                                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse'.$key.'" aria-expanded="false" aria-controls="flush-collapse'.$key.'">
                                    '.$aENTI[$valueENTE].'
                                  </button>
                                </h2>
                                <div id="flush-collapse'.$key.'" class="accordion-collapse collapse '.$showaccordion.' " data-bs-parent="#accordionENTI">
                                  <div class="accordion-body">
                                    <div class="table-responsive mb-7 mb-md-3">

                                        <table class="table table-bordered " id="tblMONITORAGGIO" name="tblMONITORAGGIO">
                                          <thead class="fs-6">
                                            <tr>
                                              <th scope="col" style="width: 5%">#</th>
                                              <th scope="col" style="width: 25%">Intervento</th>
                                              <th scope="col" style="width: 25%">Tipologia spesa</th>
                                              <th scope="col" style="width: 5%">Beneficiari</th>
                                              <th scope="col" style="width: 5%">Spesa media</th>
                                              <th scope="col" style="width: 5%">Importo</th>
                                            </tr>
                                          </thead>
                                          <tbody id="post_data" class="fs-6">';

                              $totalebeneficiariENTE=0;
                              $totaleimportoENTE=0;
                              $totalemediaENTE=0;
                              $iCounter=1;
                              
                              foreach ($aINTERVENTIFLUSSO as $key => $aDATIINTERVENTO) 
                              {
                                $idrebuilding_flussofinanziario_intervento=$aDATIINTERVENTO['idrebuilding_flussofinanziario_intervento'];
                                $intervento_codice=$aDATIINTERVENTO['intervento_codice'];
                                $intervento_titolo=$aDATIINTERVENTO['intervento_titolo'];
                                $idrebuilding_tipologiaspesa=$aDATIINTERVENTO['idrebuilding_tipologiaspesa'];
                                
                                $beneficiari=$aMONITORAGGIO[$valueENTE][$idrebuilding_flussofinanziario_intervento.'|'.$idrebuilding_tipologiaspesa]['beneficiari'];
                                $importo=$aMONITORAGGIO[$valueENTE][$idrebuilding_flussofinanziario_intervento.'|'.$idrebuilding_tipologiaspesa]['importo'];
                                if($beneficiari>0)
                                  $spesamedia=@($importo/$beneficiari);
                                else
                                  $spesamedia=0;

                                echo '<tr>';
                                echo '<td>'.$iCounter.'</td>';
                                echo '<td>'.stripslashes($intervento_codice.' '.$intervento_titolo).'</td>';
                                echo '<td>'.$aDESCRIZIONESPESA[$idrebuilding_tipologiaspesa].'</td>';
                                echo '<td>'.number_format($beneficiari).'</td>';
                                echo '<td>'.number_format($spesamedia,2,",",".").'</td>';
                                echo '<td>'.number_format($importo,2,",",".").'</td>';
                                echo '</tr>';      

                                $totalebeneficiariENTE+=$beneficiari;
                                $totaleimportoENTE+=$importo;

                                $totalebeneficiariFLUSSO+=$beneficiari;
                                $totaleimportoFLUSSO+=$importo;
                                $iCounter++;


                              }
                              
                              /*
                              foreach ($aMONITORAGGIO as $keyENTE => $aDATIMONITORAGGIO) 
                              {
                                if($keyENTE==$valueENTE)
                                {
                                  
                                  foreach ($aDATIMONITORAGGIO as $keyINTERVENTOSPESA => $aDATITIPOLOGIA) 
                                  {
                                    $spesamedia=$aDATITIPOLOGIA['importo']/$aDATITIPOLOGIA['beneficiari'];
                                    list($keyINTERVENTO,$keyTIPOLOGIA)=explode("|",$keyINTERVENTOSPESA);
                                    echo '<tr>';
                                    echo '<td>'.$iCounter.'</td>';
                                    echo '<td>'.stripslashes($aDESCRIZIONEINTERVENTO[$keyINTERVENTO]).'</td>';
                                    echo '<td>'.$aDESCRIZIONESPESA[$keyTIPOLOGIA].'</td>';
                                    echo '<td>'.$aDATITIPOLOGIA['beneficiari'].'</td>';
                                    echo '<td>'.number_format($spesamedia,2,".","").'</td>';
                                    echo '<td>'.number_format($aDATITIPOLOGIA['importo'],2,".","").'</td>';
                                    echo '</tr>';
                                    $totalebeneficiariENTE+=$aDATITIPOLOGIA['beneficiari'];
                                    $totaleimportoENTE+=$aDATITIPOLOGIA['importo'];

                                    $totalebeneficiariFLUSSO+=$aDATITIPOLOGIA['beneficiari'];
                                    $totaleimportoFLUSSO+=$aDATITIPOLOGIA['importo'];
                                    $iCounter++;
                                  }                                  
                                }


                              }
                                     
                              */
                              
                              if($totaleimportoENTE>0)
                              {
                                $totalemediaENTE=round($totaleimportoENTE/$totalebeneficiariENTE,2);
                                echo '<tr>';
                                echo '<td></td>';
                                echo '<td></td>';
                                echo '<td></td>';
                                echo '<td>'.$totalebeneficiariENTE.'</td>';
                                echo '<td>'.number_format($totalemediaENTE,2,",",".").'</td>';
                                echo '<td>'.number_format($totaleimportoENTE,2,",",".").'</td>';
                                echo '</tr>';                                  
                              }                              

                              echo '      </tbody>
                                     </table>
                                   </div>                                      
                                  </div>
                                </div>
                              </div>';                       
                          }

                          if(!empty($operatore_flagamministratore))
                          {
                              echo '<div class="accordion-item">
                            <h2 class="accordion-header">
                              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseR" aria-expanded="false" aria-controls="flush-collapseR">
                                RIEPILOGO PER INTERVENTO/TIPOLOGIA SPESA
                              </button>
                            </h2>
                            <div id="flush-collapseR" class="accordion-collapse collapse show" data-bs-parent="#accordionENTI">
                              <div class="accordion-body">
                                <div class="table-responsive mb-7 mb-md-3">

                                    <table class="table table-bordered " id="tblMONITORAGGIOR" name="tblMONITORAGGIOR">
                                      <thead class="fs-6">
                                        <tr>
                                          <th scope="col" style="width: 5%">#</th>
                                          <th scope="col" style="width: 25%">Intervento</th>
                                          <th scope="col" style="width: 25%">Tipologia spesa</th>
                                          <th scope="col" style="width: 5%">Beneficiari</th>
                                          <th scope="col" style="width: 5%">Spesa media</th>
                                          <th scope="col" style="width: 5%">Importo</th>
                                        </tr>
                                      </thead>
                                      <tbody id="post_data" class="fs-6">';

                                  $iCounter=1;
                                  foreach ($aINTERVENTIFLUSSO as $key => $aDATIINTERVENTO) 
                                  {
                                    $idrebuilding_flussofinanziario_intervento=$aDATIINTERVENTO['idrebuilding_flussofinanziario_intervento'];
                                    $intervento_codice=$aDATIINTERVENTO['intervento_codice'];
                                    $intervento_titolo=$aDATIINTERVENTO['intervento_titolo'];
                                    $idrebuilding_tipologiaspesa=$aDATIINTERVENTO['idrebuilding_tipologiaspesa'];

                                    $beneficiari=$aRIEPILOGO[$idrebuilding_flussofinanziario_intervento.'|'.$idrebuilding_tipologiaspesa]['beneficiari'];
                                    $importo=$aRIEPILOGO[$idrebuilding_flussofinanziario_intervento.'|'.$idrebuilding_tipologiaspesa]['importo'];
                                    if($beneficiari>0)                   
                                      $spesamedia=@($importo/$beneficiari);
                                    else
                                      $spesamedia=0;
                                    echo '<tr>';
                                    echo '<td>'.$iCounter.'</td>';
                                    echo '<td>'.stripslashes($intervento_codice.' '.$intervento_titolo).'</td>';
                                    echo '<td>'.$aDESCRIZIONESPESA[$idrebuilding_tipologiaspesa].'</td>';
                                    echo '<td>'.number_format($beneficiari).'</td>';
                                    echo '<td>'.number_format($spesamedia,2,",",".").'</td>';
                                    //echo '<td>'.number_format($spesamedia,2,".","").'</td>';
                                    echo '<td>'.number_format($importo,2,",",".").'</td>';
                                    echo '</tr>';


                                  }  
                                                      
                                                       
                                $totalemediaRIEPILOGO=round($totaleimportoFLUSSO/$totalebeneficiariFLUSSO,2);
                                echo '<tr>';
                                echo '<td></td>';
                                echo '<td></td>';
                                echo '<td></td>';
                                echo '<td>'.$totalebeneficiariFLUSSO.'</td>';
                                echo '<td>'.number_format($totalemediaRIEPILOGO,2,",",".").'</td>';
                                //echo '<td>'.number_format($totalemediaRIEPILOGO,2,".","").'</td>';
                                echo '<td>'.number_format($totaleimportoFLUSSO,2,",",".").'</td>';
                                echo '</tr>';                                  
                                                       

                              echo '      </tbody>
                                     </table>
                                   </div>                                      
                                  </div>
                                </div>
                              </div>'; 

                              echo '<div class="accordion-item">
                            <h2 class="accordion-header">
                              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseATS" aria-expanded="false" aria-controls="flush-collapseATS">
                                RIEPILOGO PER ATS
                              </button>
                            </h2>
                            <div id="flush-collapseATS" class="accordion-collapse collapse show" data-bs-parent="#accordionENTI">
                              <div class="accordion-body">
                                <div class="table-responsive mb-7 mb-md-3">

                                    <table class="table table-bordered " id="tblMONITORAGGIOATS" name="tblMONITORAGGIOATS">
                                      <thead class="fs-6">
                                        <tr>
                                          <th scope="col" style="width: 5%">#</th>
                                          <th scope="col" style="width: 30%">ATS</th>
                                          <th scope="col" style="width: 10%">Risorse</th>
                                          <th scope="col" style="width: 10%">Spesa</th>
                                          <th scope="col" style="width: 10%">% Spesa</th>
                                          <th scope="col" style="width: 10%">Beneficiari</th>
                                          <th scope="col" style="width: 10%">Spesa media</th>
                                          
                                        </tr>
                                      </thead>
                                      <tbody id="post_data" class="fs-6">';


                                  $iCounter=1;
                                  foreach ($aENTISELEZIONATI as $key => $keyENTE)
                                  {
                                    $importo=$aRIEPILOGOATS[$keyENTE]['importo'];
                                    $beneficiari=$aRIEPILOGOATS[$keyENTE]['beneficiari'];
                                    if($beneficiari>0)
                                      $spesamedia=@($importo/$beneficiari);
                                    else
                                      $spesamedia=0;
                                    $percentuale=@round(100-(($aRISORSEASSEGNATE[$keyENTE]-$aDATIATS['importo'])/$aRISORSEASSEGNATE[$keyENTE]*100),2);
                                    echo '<tr>';
                                    echo '<td>'.$iCounter.'</td>';
                                    echo '<td>'.$aENTI[$keyENTE].'</td>';
                                    echo '<td>'.number_format($aRISORSEASSEGNATE[$keyENTE],2,".","").'</td>';
                                    echo '<td>'.number_format($importo,2,",",".").'</td>';
                                    echo '<td>'.number_format($percentuale,2,",",".").'</td>';
                                    echo '<td>'.number_format($beneficiari,2,",",".").'</td>';
                                    echo '<td>'.number_format($spesamedia,2,",",".").'</td>';
                                    echo '</tr>';
                                    $totalebeneficiariATS+=$beneficiari;
                                    $totaleimportoATS+=$importo;
                                    $totalerisorseATS+=$aRISORSEASSEGNATE[$keyENTE];

                                    $iCounter++;
                                  }
                                                   
                                  if($totalebeneficiariATS>0)
                                    $totalemediaATS=@round($totaleimportoATS/$totalebeneficiariATS,2);
                                  else
                                    $totalemediaATS=0;

                                  if($totalerisorseATS>0)
                                    $totalepercentualeATS=@round(100-(($totalerisorseATS-$totaleimportoATS)/$totalerisorseATS*100),2);
                                  else
                                    $totalepercentualeATS=0;

                                echo '<tr>';
                                echo '<td></td>';
                                echo '<td></td>';
                                echo '<td>'.number_format($totalerisorseATS,2,",",".").'</td>';
                                echo '<td>'.number_format($totaleimportoATS,2,",",".").'</td>';
                                echo '<td>'.number_format($totalepercentualeATS,2,",",".").'</td>';
                                echo '<td>'.number_format($totalebeneficiariATS,2,",",".").'</td>';
                                echo '<td>'.number_format($totalemediaATS,2,",",".").'</td>';
                                echo '</tr>';                                  
                                                       

                              echo '      </tbody>
                                     </table>
                                   </div>                                      
                                  </div>
                                </div>
                              </div>'; 

                          }
                      ?>
                      


                      </div>
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

  </body>
</html>



<script>
$(document).ready(function(){

  $('#tblMONITORAGGIOATS').DataTable({
    "searching": false,
    "info":     false,
    "paging":   false,
    "ordering": false,
    "language": {      
			"infoEmpty": "Nessun record trovato"},
    dom: 'Bfrtip',
        buttons: [
            
            'excelHtml5'
        ]
    
  })

  $('#tblMONITORAGGIOR').DataTable({
    "searching": false,
    "info":     false,
    "paging":   false,
    "ordering": false,
    "language": {
			"infoEmpty": "Nessun record trovato"			
    },
    dom: 'Bfrtip',
        buttons: [
           
            'excelHtml5'
        ]
    
  })

  
})

</script>

<?php

require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");
require_once("../librerie/dara.class.servizi.php");

//error_reporting(0);
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

$pidrebuilding_flussofinanziario_liquidazione=getPARAMETRO("_k");
$pidrebuilding_flussofinanziario_liquidazione=$db->escape_text($pidrebuilding_flussofinanziario_liquidazione);

$flussofinanziario=new rebuildingFLUSSOFINANZIARIO($pidrebuilding_flussofinanziario);
$flussofinanziario_ente=$flussofinanziario->flussofinanziario_ente;
$aENTISELEZIONATI=explode(",",$flussofinanziario_ente);

if(empty($operatore_flagamministratore))
{
  $aENTISELEZIONATI=array();
  $aENTISELEZIONATI[$operatore_ente]=$operatore_ente;
}

if(getPARAMETRO("_elimina"))
{
  $sSQL="UPDATE rebuilding_flussofinanziario_liquidazione set liquidazione_flagelimina=1 where idrebuilding_flussofinanziario_liquidazione='$pidrebuilding_flussofinanziario_liquidazione'";
  $db->query($sSQL);
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

$aTIPOAREA=array(1=>"Famiglia",2=>"Anziani",3=>"Minori",4=>"Dipendenze",5=>"Disabili",6=>"Adulti e minori sottoposti a provvedimenti Autorità Giudiziaria");
$aTIPOSPESA=$db->select("select idrebuilding_tipologiaspesa,tipologiaspesa_descrizione from rebuilding_tipologiaspesa order by idrebuilding_tipologiaspesa ");
$aINTERVENTI=$db->select("select idrebuilding_flussofinanziario_intervento,intervento_titolo from rebuilding_flussofinanziario_intervento order by idrebuilding_flussofinanziario_intervento ");
$aSPESA=$db->select("select idrebuilding_tipologiaspesa,tipologiaspesa_descrizione from rebuilding_tipologiaspesa order by idrebuilding_tipologiaspesa");

$aDESCRIZIONEINTERVENTO=array();
foreach ($aINTERVENTI as $key => $aDATI) 
{
  $aDESCRIZIONEINTERVENTO[$aDATI['idrebuilding_flussofinanziario_intervento']]=$aDATI['intervento_titolo'];
}

$aDESCRIZIONESPESA=array();
foreach ($aSPESA as $key => $aDATI) 
{
  $aDESCRIZIONESPESA[$aDATI['idrebuilding_tipologiaspesa']]=$aDATI['tipologiaspesa_descrizione'];
}

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

    $aBREADCUMB[4]["titolo_pagina"]="Liquidazioni";
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
                    <a class="nav-link active " aria-current="page" href="flussofinanziario-liquidazioni?_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario;?>"><?php echo $label_liquidazioni;?></a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link " aria-current="page" href="flussofinanziario-monitoraggio?_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario;?>">Monitoraggio/rendiconto</a>
                  </li>

                </ul>

                  <!-- ELENCO -->
                <form id="formINTERVENTI" name="formINTERVENTI" method="post">
                  <div class="row">
                    <div class="col-12">    
                        <p align="right">                          
                            <button type="button" class="btn btn-sm btn-primary-soft  btn-xs" data-bs-toggle="modal" data-bs-target="#modalINTERVENTO" onclick="openLIQUIDAZIONE('0')" <?php if($operatore_flagamministratore=='1') echo 'style="display:none"'; ?>><i class="fe fe-plus"></i>&nbsp;Nuova liquidazione/spesa</button>
                        </p>
                    </div>  
                  </div> 

                <div class="table-responsive mb-7 mb-md-3">

                    <table class="table table-bordered " id="tblINTERVENTI" name="tblINTERVENTI">
                      <thead class="fs-6">
                        <tr>
                          <th scope="col" style="width: 5%">#</th>
                          <th scope="col" style="width: 20%">ATS</th>
                          <th scope="col" style="width: 30%">Intervento/Tipologia spesa</th>
                          <th scope="col" style="width: 10%">Importo</th>
                          <th scope="col" style="width: 20%">Atto liquidazione</th>
                          <!--th scope="col" style="width: 15%">Quiestanza</th-->
                          <th scope="col" style="width: 15%"></th>
                        </tr>
                      </thead>
                      <tbody id="post_data" class="fs-6">
                        <?php
                          
                          foreach ($aLIQUIDAZIONI as $key => $aDATI) 
                          {

                            $confirm='<a href=\'#\'><span class=\'badge bg-primary-soft\'>NO</span></a>&nbsp;<a href=\'rebuilding_flussofinanziario_liquidazioni.php?_k='.$aDATI["idrebuilding_flussofinanziario_liquidazione"].'&_elimina=true&_RENDICONTAZIONE='.$pidrebuilding_flussofinanziario.'\'><span class=\'badge bg-primary-soft\' >SI</span></a>';

                            echo "<tr id='".$aDATI["idrebuilding_flussofinanziario_liquidazione"]."'>";
                            echo "<td>".$aDATI["idrebuilding_flussofinanziario_liquidazione"]."</td>";
                            echo "<td>".$aENTI[$aDATI["liquidazione_ente"]]."</td>";
                            echo "<td>".$aDESCRIZIONEINTERVENTO[$aDATI["idrebuilding_flussofinanziario_intervento"]].'/'.$aDESCRIZIONESPESA[$aDATI["idrebuilding_tipologiaspesa"]]."</td>";
                            echo "<td>".number_format($aDATI["liquidazione_importo"],2,",",".")."</td>";
                            echo "<td>".$aDATI["liquidazione_attonumero"].' '.dataitaliana($aDATI["liquidazione_attodata"])."</td>";
                            //echo "<td>".$aDATI["liquidazione_quietanzanumero"].' '.dataitaliana($aDATI["liquidazione_quietanzadata"])."</td>";
                            echo "<td>";
                            if(empty($operatore_flagamministratore))
                              echo '<button type="button" id="edittipo" name="edittipo" class="btn btn-xs btn-rounded-circle btn-primary" data-bs-toggle="modal" data-bs-target="#modalINTERVENTO"  onclick="openLIQUIDAZIONE(\''.$aDATI["idrebuilding_flussofinanziario_liquidazione"].'\')" ><i class="fe fe-check"></i></button>
                                  <button type="button" id="deletetipo'.$aDATI["idrebuilding_flussofinanziario_liquidazione"].'" name="deletetipo'.$aDATI["idrebuilding_flussofinanziario_liquidazione"].'" class="btn btn-xs btn-rounded-circle btn-danger" data-bs-container="body" data-bs-toggle="popover" data-bs-placement="left" title="Confermi l\'eliminazione?" data-bs-content="'.$confirm.'" data-bs-html=true data-bs-trigger="focus"><i class="fe fe-x"></i></button>';
                            echo "</td>";
                            echo "</tr>";                            
                          }

                        ?>
                      </tbody>
                   </table>
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

  $('#tblINTERVENTI').DataTable({
    "searching": false,
    "info":     false,
    "paging":   false,
    "ordering": false,
    "language": {      
      "infoEmpty": "Nessun record trovato"},
    dom: 'Bfrtip',
        buttons: [
          {
                extend: 'excelHtml5',
                exportOptions: {
                  columns: [ 0, 1, 2, 3, 4 ]
                }
            }
          ]
    
  })

  
})

function openLIQUIDAZIONE(myKEY)
{
  

  window.location='flussofinanziario-liquidazione?_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario;?>&_k='+myKEY

}

function saveINTERVENTO()
{

 
}

function closeINTERVENTO()
{


  
}
</script>

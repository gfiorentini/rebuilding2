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

$pidrebuilding_flussofinanziario_intervento=getPARAMETRO("_k");
$pidrebuilding_flussofinanziario_intervento=$db->escape_text($pidrebuilding_flussofinanziario_intervento);

$flussofinanziario=new rebuildingFLUSSOFINANZIARIO($pidrebuilding_flussofinanziario);
$flussofinanziario_ente=$flussofinanziario->flussofinanziario_ente;
$aENTISELEZIONATI=explode(",",$flussofinanziario_ente);

$aINTERVENTI=$flussofinanziario->getINTERVENTI();

if(getPARAMETRO("_salva") && $operatore_flagamministratore==1 && empty($operatore_flagdirigente))
{
  $data=date("Y-m-d");
}

if(getPARAMETRO("_elimina"))
{
  $sSQL="UPDATE rebuilding_flussofinanziario_intervento set intervento_flagelimina=1 where idrebuilding_flussofinanziario_intervento='$pidrebuilding_flussofinanziario_intervento'";
  $db->query($sSQL);
  header("location:flussofinanziario-interventi?_RENDICONTAZIONE=".$pidrebuilding_flussofinanziario);
  exit;
}

$disabled_salva='';
$disabled_scheda="";
if(empty($operatore_flagamministratore) || !empty($operatore_flagdirigente) || (!empty($pidrebuilding_flussofinanziario) && $flussofinanziario->flussofinanziario_rup>0 && $flussofinanziario->flussofinanziario_rup!=$idoperatore))
{
  if($flussofinanziario->flussofinanziario_operatore==$idoperatore)
  {

  }
  else
    $disabled_scheda="disabled";  
}

if(empty($operatore_flagamministratore))
  $disabled_scheda="disabled";  

$aTIPOAREA=array(1=>"Famiglia",2=>"Anziani",3=>"Minori",4=>"Dipendenze",5=>"Disabili",6=>"Adulti e minori sottoposti a provvedimenti Autorità Giudiziaria");
$aTIPOSPESA=$db->select("SELECT idrebuilding_tipologiaspesa,tipologiaspesa_descrizione from rebuilding_tipologiaspesa order by idrebuilding_tipologiaspesa ");

$aDESCRIZIONISPESA=array();

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
    <script src="js/bootstrap.min.js"></script>

    <link rel="stylesheet" type="text/css" href="../librerie/css/bootstrap-dialog.min.css">
    <script language="javascript" type="text/javascript" src="../librerie/js/bootstrap-dialog.min.js"></script>   
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

    $aBREADCUMB[2]["titolo_pagina"]="Flussi di finanziamento";
    $aBREADCUMB[2]["url"]="toolkit";

    $aBREADCUMB[3]["titolo_pagina"]="Flussi finanziari";
    $aBREADCUMB[3]["url"]="rendicontazione";

    $aBREADCUMB[4]["titolo_pagina"]="Interventi";
    $aBREADCUMB[4]["url"]="";

		generaBREADCUMB($aBREADCUMB);

	  ?>        
    <section class="bg-light">
      <!-- Modal -->
      <div class="modal fade" id="modalINTERVENTO" tabindex="-1" role="dialog" aria-labelledby="modalINTERVENTOLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalINTERVENTOLabel"><b>Flusso di finanziamento - Intervento</b></h5>
            </div>
            <div class="modal-body">
                  <div class="form-group row">
                    <div class="col-12 col-md-4">
                      <div class="form-floating">
                        <input type="text" id="formCODICE" name="formCODICE" class="form-control form-control-flush"  placeholder="" required value="" <?php echo $disabled_scheda;?>>
                        <label for="formCODICE">Codice*</label>
                      </div> 
                    </div>

                    <div class="col-12 col-md-8">
                      <div class="form-floating">
                        <input type="text" id="formTITOLO" name="formTITOLO" class="form-control form-control-flush"  placeholder="" required value="" <?php echo $disabled_scheda;?>>
                        <label for="formTITOLO">Titolo*</label>
                      </div> 
                    </div>

                  </div>     

                  <div class="form-group row">
                    <div class="col-12 col-md-12">
                      <div class="form-text">
                        <textarea id="formDESCRIZIONE" name="formDESCRIZIONE" class="form-control form-control-flush" rows="2" ></textarea>            
                        <label for="formDESCRIZIONE">Descrizione</label>
                      </div>  
                    </div>
                  </div>    


                  <div class="form-group row">
                    <div class="col-12 col-md-6">

                      <div class="form-floating">
                        <select id="formTIPOSPESA" name="formTIPOSPESA" class="form-control form-control-flush form-select" required <?php echo $disabled_scheda;?>>
                          <option value='0'></option>
                          <?php           
                                            
                            foreach ($aTIPOSPESA as $key => $aDATI) 
                            {

                                echo '<option value="'.$aDATI['idrebuilding_tipologiaspesa'].'">'.$aDATI['tipologiaspesa_descrizione'].'</option>';

                                $aDESCRIZIONISPESA[$aDATI['idrebuilding_tipologiaspesa']]=$aDATI['tipologiaspesa_descrizione'];
                            }
                          ?>                                  
                        </select>
                        <label for="formTIPOSPESA">Tipologia spesa*</label>
                      </div> 
                    </div>    
         
                  </div>    


              </div>
            
            <div class="modal-footer">
              <input type="hidden" id="modalKEY" name="modalKEY" value="">
              <button type="button" class="btn btn-xs btn-primary" onclick="saveINTERVENTO()" <?php echo $disabled_scheda; ?>>Salva</button>
              <button type="button" class="btn btn-xs btn-secondary" onclick="closeINTERVENTO()">Chiudi</button>
            </div>
          </div>
        </div>
      </div>

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
                    <a class="nav-link active" aria-current="page" href="flussofinanziario-interventi?_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario;?>">Interventi</a>
                  </li>
                  <!-- <li class="nav-item">
                    <a class="nav-link " aria-current="page" href="flussofinanziario-liquidazioni?_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario;?>"><?php echo $label_liquidazioni;?></a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link " aria-current="page" href="flussofinanziario-monitoraggio?_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario;?>">Monitoraggio/rendiconto</a>
                  </li> -->

                </ul>

                  <!-- ELENCO -->
                <form id="formINTERVENTI" name="formINTERVENTI" method="post">
                  <div class="row">
                    <div class="col-12">    
                        <p align="right">                          
                            <button type="button" class="btn btn-sm btn-primary-soft  btn-xs" data-bs-toggle="modal" data-bs-target="#modalINTERVENTO" onclick="openINTERVENTO('0','','','','0','0')" <?php echo $disabled_scheda; ?>><i class="fe fe-plus"></i>&nbsp;Nuovo intervento</button>
                        </p>
                    </div>  
                  </div> 

                <div class="table-responsive mb-7 mb-md-3">

                    <table class="table table-bordered " id="tblINTERVENTI" name="tblINTERVENTI">
                      <thead class="fs-6">
                        <tr>
                          <th scope="col" style="width: 5%">#</th>
                          <th scope="col" style="width: 10%">Codice</th>
                          <th scope="col" style="width: 20%">Titolo</th>
                          <th scope="col" style="width: 40%">Descrizione</th>
                          <th scope="col" style="width: 15%">Tipo spesa</th>
                          <th scope="col" style="width: 10%"></th>
                        </tr>
                      </thead>
                      <tbody id="post_data" class="fs-6">
                        <?php

                          foreach ($aINTERVENTI as $key => $aDATI) 
                          {

                            $confirm='<a href=\'#\'><span class=\'badge bg-primary-soft\'>NO</span></a>&nbsp;<a href=\'rebuilding_flussofinanziario_interventi.php?_k='.$aDATI["idrebuilding_flussofinanziario_intervento"].'&_elimina=true&_RENDICONTAZIONE='.$pidrebuilding_flussofinanziario.'\'><span class=\'badge bg-primary-soft\' >SI</span></a>';

                            echo "<tr id='".$aDATI["idrebuilding_flussofinanziario_intervento"]."'>";
                            echo "<td>".$aDATI["idrebuilding_flussofinanziario_intervento"]."</td>";
                            echo "<td>".stripslashes($aDATI["intervento_codice"])."</td>";
                            echo "<td>".stripslashes($aDATI["intervento_titolo"])."</td>";
                            echo "<td>".stripslashes($aDATI["intervento_descrizione"])."</td>";
                            echo "<td>".$aDESCRIZIONISPESA[$aDATI["idrebuilding_tipologiaspesa"]]."</td>";
                            echo "<td>";
                            echo '<button type="button" id="edittipo" name="edittipo" '.$disabled_scheda.' class="btn btn-xs btn-rounded-circle btn-primary" data-bs-toggle="modal" data-bs-target="#modalINTERVENTO"  onclick="openINTERVENTO(\''.$aDATI["idrebuilding_flussofinanziario_intervento"].'\',\''.addslashes($aDATI["intervento_codice"]).'\',\''.addslashes($aDATI["intervento_titolo"]).'\',\''.addslashes($aDATI["intervento_descrizione"]).'\',\''.$aDATI["intervento_areaintervento"].'\',\''.$aDATI["idrebuilding_tipologiaspesa"].'\')" ><i class="fe fe-check"></i></button>
                                  <button type="button" id="deletetipo'.$aDATI["idrebuilding_flussofinanziario_intervento"].'" name="deletetipo'.$aDATI["idrebuilding_flussofinanziario_intervento"].'" '.$disabled_scheda.' class="btn btn-xs btn-rounded-circle btn-danger" data-bs-container="body" data-bs-toggle="popover" data-bs-placement="left" title="Confermi l\'eliminazione?" data-bs-content="'.$confirm.'" data-bs-html=true data-bs-trigger="focus"><i class="fe fe-x"></i></button>';
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


function openINTERVENTO(myKEY,myCODICE,myTITOLO,myDESCRIZIONE,myAREA,mySPESA)
{
  $("#modalKEY").val(myKEY)

  $("#formCODICE").val(myCODICE)
  $("#formTITOLO").val(myTITOLO)
  $("#formDESCRIZIONE").val(myDESCRIZIONE)
  

  $("#formTIPOSPESA").val(mySPESA)
  //$("#formAREAINTERVENTO").val(myAREA)

  //$("#modalINTERVENTO").modal('show');

}

function saveINTERVENTO()
{

    myKEY=$("#modalKEY").val()
    myCODICE=$("#formCODICE").val()
    myTITOLO=$("#formTITOLO").val()
    myDESCRIZIONE=$("#formDESCRIZIONE").val()
    myTIPOSPESA=$("#formTIPOSPESA").val()
    //myAREAINTERVENTO=$("#formAREAINTERVENTO").val()
    
    var page="rebuilding_action.php";
    var params="_action=saveINTERVENTO&_flusso=<?php echo $pidrebuilding_flussofinanziario;?>&_descrizione="+myDESCRIZIONE+"&_codice="+myCODICE+"&_titolo="+myTITOLO+"&_spesa="+myTIPOSPESA+"&_k="+myKEY;
    //alert(page+params)
    $.ajax({
      type: "POST",
      url: page,
      data: params, 
      dataType: "html",
      success: function(result)
      {
        
        if(result=="1" || result==1)
          location.reload()
        else
        {
          $("#modalINTERVENTO").modal('hide');

          BootstrapDialog.show({
		            title: 'Attenzione',
					      type: BootstrapDialog.TYPE_DANGER, 
		            message: 'Flusso con codice e tipo spesa già presente.',
		            buttons: [{
		                label: 'Chiudi',
		                action: function(dialog) {
		                    dialog.close();
		                }
		            }]
		        });	
        }     

      },
      error: function()
      {
        alert("ERRORE")
        console.log("Chiamata fallita, si prega di riprovare...");
      }
    });    
}

function closeINTERVENTO()
{
  $("#modalINTERVENTO").modal('hide');

}
</script>

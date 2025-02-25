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


$target_dir = "../documenti/rebuilding/toolkit/";


if(getPARAMETRO("_salva"))
{

  $data=date("Y-m-d");
  $pidrebuilding_ente=getPARAMETRO("idrebuilding_ente");
  $pidrebuilding_ente=$db->escape_text($pidrebuilding_ente);
  $pidrebuilding_flussofinanziario=getPARAMETRO("idrebuilding_flussofinanziario");
  $pidrebuilding_flussofinanziario=$db->escape_text($pidrebuilding_flussofinanziario);
  
  $pidrebuilding_flussofinanziario_interventospesa=getPARAMETRO("idrebuilding_flussofinanziario_intervento");
  $pidrebuilding_flussofinanziario_interventospesa=$db->escape_text($pidrebuilding_flussofinanziario_interventospesa);

  list($pidrebuilding_flussofinanziario_intervento,$pidrebuilding_tipologiaspesa)=explode("|",$pidrebuilding_flussofinanziario_interventospesa);

  if(empty($pidrebuilding_tipologiaspesa))
    $pidrebuilding_tipologiaspesa=0;
  //$pidrebuilding_tipologiaspesa=getPARAMETRO("idrebuilding_tipologiaspesa");
  //$pidrebuilding_tipologiaspesa=$db->escape_text($pidrebuilding_tipologiaspesa);
  $pliquidazione_attonumero=getPARAMETRO("liquidazione_attonumero");
  $pliquidazione_attonumero=$db->escape_text($pliquidazione_attonumero);
  $pliquidazione_attodata=getPARAMETRO("liquidazione_attodata");
  $pliquidazione_attodata=$db->escape_text($pliquidazione_attodata);
  $pliquidazione_quietanzanumero=getPARAMETRO("liquidazione_quietanzanumero");
  $pliquidazione_quietanzanumero=$db->escape_text($pliquidazione_quietanzanumero);
  $pliquidazione_quietanzadata=getPARAMETRO("liquidazione_quietanzadata");
  $pliquidazione_quietanzadata=$db->escape_text($pliquidazione_quietanzadata);

  $pliquidazione_importo=getPARAMETRO("liquidazione_importo");
  $pliquidazione_importo=$db->escape_text($pliquidazione_importo);
  $pliquidazione_beneficiari=getPARAMETRO("liquidazione_beneficiari");
  $pliquidazione_beneficiari=$db->escape_text($pliquidazione_beneficiari);

  if(empty($pidrebuilding_flussofinanziario_liquidazione))
  {
    $sSQL="insert into rebuilding_flussofinanziario_liquidazione (
    liquidazione_ente,
    idrebuilding_flussofinanziario,
    idrebuilding_flussofinanziario_intervento,    
    idrebuilding_tipologiaspesa,
    liquidazione_attonumero,
    liquidazione_attodata,
    liquidazione_importo,
    liquidazione_beneficiari) 
    values(
    '$pidrebuilding_ente',
    '$pidrebuilding_flussofinanziario',
    '$pidrebuilding_flussofinanziario_intervento',
    '$pidrebuilding_tipologiaspesa',
    '$pliquidazione_attonumero',
    '$pliquidazione_attodata',
    '$pliquidazione_importo',
    '$pliquidazione_beneficiari')";
    $db->query($sSQL);

    $pidrebuilding_flussofinanziario_liquidazione=$db->insert_id();
  }
  else
  {
   $sSQL="update rebuilding_flussofinanziario_liquidazione set 
    liquidazione_ente='$pidrebuilding_ente',
    idrebuilding_flussofinanziario_intervento='$pidrebuilding_flussofinanziario_intervento',    
    idrebuilding_tipologiaspesa='$pidrebuilding_tipologiaspesa',
    liquidazione_attonumero='$pliquidazione_attonumero',
    liquidazione_attodata='$pliquidazione_attodata',
    liquidazione_importo='$pliquidazione_importo',
    liquidazione_beneficiari='$pliquidazione_beneficiari' 
    where idrebuilding_flussofinanziario_liquidazione='$pidrebuilding_flussofinanziario_liquidazione'";
    $db->query($sSQL);
  }

  $nALLEGATI=getPARAMETRO("counter_allegati");
  $nALLEGATI=$db->escape_text($nALLEGATI);
  
  $liquidazione_documentotime=mktime(date("H"),date("m"),date("i"),date("m"),date("d"),date("Y"));
  $pflussofinanziario_datainserimento=date("Y-m-d");

  for($i=1;$i<=$nALLEGATI;$i++)
  {    

    $idrebuilding_flussofinanziario_liquidazione_documento=getPARAMETRO("_flussofinanziario_documento".$i);
    $idrebuilding_flussofinanziario_liquidazione_documento=$db->escape_text($idrebuilding_flussofinanziario_liquidazione_documento);
    
    $documentoNAME = basename($_FILES["nome_file".$i]["name"]);    
    if(!empty($documentoNAME) && empty($idrebuilding_flussofinanziario_liquidazione_documento))
    {
      $path_parts = pathinfo($_FILES["nome_file".$i]["name"]);
      $allegatoTITOLO=$_FILES["nome_file".$i]["name"];
      $filename='L'.md5(date("Ymdhis").$documentoNAME).".".$path_parts['extension'];
      copy($_FILES["nome_file".$i]["tmp_name"],$target_dir.$filename);    
      $sSQL="insert into rebuilding_flussofinanziario_liquidazione_documento (idrebuilding_flussofinanziario,idrebuilding_flussofinanziario_liquidazione,liquidazione_documentonome,liquidazione_documentotitolo,liquidazione_documentodata,liquidazione_documentooperatore,liquidazione_documentoultimamodifica) values('$pidrebuilding_flussofinanziario','$pidrebuilding_flussofinanziario_liquidazione','$filename','$allegatoTITOLO','$pflussofinanziario_datainserimento','$idoperatore','$pflussofinanziario_datainserimento') ";
      $db->query($sSQL);      
    }
    elseif(!empty($documentoNAME) && !empty($idrebuilding_flussofinanziario_liquidazione_documento))
    {
      $ultimamodifica=date("Y-m-d");
      $path_parts = pathinfo($_FILES["nome_file".$i]["name"]);
      $allegatoTITOLO=$_FILES["nome_file".$i]["name"];
      $filename='L'.md5(date("Ymdhis").$documentoNAME).".".$path_parts['extension'];
      copy($_FILES["nome_file".$i]["tmp_name"],$target_dir.$filename);    
      $sSQL="update rebuilding_flussofinanziario_liquidazione_documento set liquidazione_documentonome='$filename',liquidazione_documentotitolo='$allegatoTITOLO',liquidazione_documentooperatore='$idoperatore',liquidazione_documentoultimamodifica='$ultimamodifica' where idrebuilding_flussofinanziario_liquidazione_documento='$idrebuilding_flussofinanziario_liquidazione_documento'";

    }
    else
    {
      
      //$sSQL="update rebuilding_flussofinanziario_liquidazione_documento set liquidazione_documentotitolo='$allegatoTITOLO',liquidazione_documentooperatore='$idoperatore',liquidazione_documentoultimamodifica='$ultimamodifica',liquidazione_documentotime='$liquidazione_documentotime' where idrebuilding_flussofinanziario_liquidazione_documento='$idrebuilding_flussofinanziario_liquidazione_documento'";
      
      //$db->query($sSQL);
      

    }
    
  }


}

if(getPARAMETRO("_elimina"))
{
  $sSQL="update from rebuilding_flussofinanziario_liquidazione set liquidazione_flagelimina=1 where idrebuilding_flussofinanziario_liquidazione='$pidrebuilding_flussofinanziario_liquidazione'";
  $db->query($sSQL);
}

$disabled_salva='';
$disabled_scheda="";
if(empty($operatore_flagamministratore) || !empty($operatore_flagdirigente) || (!empty($pidrebuilding_flussofinanziario) && $flussofinanziario->flussofinanziario_rup>0 && $flussofinanziario->flussofinanziario_rup!=$idoperatore))
{
  $aENTISELEZIONATI=array();
  $aENTISELEZIONATI[$operatore_ente]=$operatore_ente;
  $disabled_scheda="disabled";
}
else
{
  
}

$aTIPOAREA=array(1=>"Famiglia",2=>"Anziani",3=>"Minori",4=>"Dipendenze",5=>"Disabili",6=>"Adulti e minori sottoposti a provvedimenti Autorità Giudiziaria");
$aTIPOSPESA=$db->select("select idrebuilding_tipologiaspesa,tipologiaspesa_descrizione from rebuilding_tipologiaspesa order by idrebuilding_tipologiaspesa ");
$aINTERVENTI=$flussofinanziario->getINTERVENTI();

$aFLUSSI=$flussofinanziario->getFLUSSI(" where idrebuilding_flussofinanziario='$pidrebuilding_flussofinanziario' ");


if(!empty($pidrebuilding_flussofinanziario_liquidazione))
{
  $liquidazione=new rebuildingLIQUIDAZIONE($pidrebuilding_flussofinanziario_liquidazione);

}

$aSPESA=$db->select("select idrebuilding_tipologiaspesa,tipologiaspesa_descrizione from rebuilding_tipologiaspesa order by idrebuilding_tipologiaspesa");
$aDESCRIZIONESPESA=array();
foreach ($aSPESA as $key => $aDATI) 
{
  $aDESCRIZIONESPESA[$aDATI['idrebuilding_tipologiaspesa']]=$aDATI['tipologiaspesa_descrizione'];
}

$disabled_rendiconto='';
?>
<!doctype html>
<html lang="it">
  <head>
  	 <?php echo getREBUILDINGHEAD(true); ?>

     <link rel="stylesheet" href="../librerie/css/bootstrap-select.css">
     <!--link rel="stylesheet" type="text/css" href="../librerie/css/bootstrap-dialog.min.css">
      <script language="javascript" type="text/javascript" src="../librerie/js/bootstrap-dialog.min.js"></script-->
      
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

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
                    <a class="nav-link active " aria-current="page" href="flussofinanziario-liquidazioni?_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario;?>">Liquidazioni</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link " aria-current="page" href="flussofinanziario-monitoraggio?_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario;?>">Monitoraggio/rendiconto</a>
                  </li>

                </ul>
                
                
                  <div class="row">
                    <div class="col-12">    
                        <p align="right">                          
                            <button type="button" class="btn btn-sm btn-primary-soft  btn-xs" data-bs-toggle="modal" data-bs-target="#modalINTERVENTO" onclick="openELENCO()"><i class="fe fe-list"></i>&nbsp;Torna all'elenco</button>
                        </p>
                    </div>  
                  </div> 

                  <div class="table-responsive mb-7 mb-md-3">

                      <form id="rendicontoFORM" name="rendicontoFORM" method="post" role="form" enctype="multipart/form-data" onsubmit="return doOnSubmit()">


                        <div class="form-group row">
                          <div class="col-12 col-md-12">

                            <div class="form-floating">
                              <select id="idrebuilding_ente" name="idrebuilding_ente" required class="form-control form-control-flush form-select" <?php echo $disabled_rendiconto;?>>
                               
                                <?php          

                                  if(!empty($operatore_flagamministratore))                        
                                    echo "<option value='0'></option>";
                                  foreach ($aENTI as $key => $descrizioneENTE) 
                                  {
                                    if($key==$liquidazione->liquidazione_ente && in_array($key,$aENTISELEZIONATI))
                                      echo '<option value="'.$key.'" selected>'.$descrizioneENTE.'</option>';
                                    elseif(in_array($key,$aENTISELEZIONATI))
                                      echo '<option value="'.$key.'">'.$descrizioneENTE.'</option>';
                                    
                                  }
                                ?>                                  
                              </select>
                              <label for="idrebuilding_ente">ATS*</label>
                            </div> 
                          </div> 
                        </div>  

                        <div class="form-group row">
                          <div class="col-12 col-md-12">

                            <div class="form-floating">
                              <select id="idrebuilding_flussofinanziario" name="idrebuilding_flussofinanziario" required class="form-control form-control-flush form-select" <?php echo $disabled_rendiconto;?>>
                                
                                <?php          

                                  //if(!empty($operatore_flagamministratore))                        
                                  //  echo "<option value='0'></option>";

                                  foreach ($aFLUSSI as $key => $aDATI) 
                                  {
                                    $idrebuilding_flussofinanziario=$aDATI['idrebuilding_flussofinanziario'];
                                    $descrizione=$aDATI['flussofinanziario_titolo'];
                                    if($idrebuilding_flussofinanziario==$liquidazione->idrebuilding_flussofinanziario)
                                      echo '<option value="'.$idrebuilding_flussofinanziario.'" selected>'.$descrizione.'</option>';
                                    else
                                      echo '<option value="'.$idrebuilding_flussofinanziario.'">'.$descrizione.'</option>';
                                    
                                  }
                                ?>                                  
                              </select>
                              <label for="idrebuilding_flussofinanziario">Flusso di finanziamento*</label>
                            </div> 
                          </div> 
                        </div>                     

                        <div class="form-group row">
                          <div class="col-12 col-md-12">

                            <div class="form-floating">
                              <select id="idrebuilding_flussofinanziario_intervento" name="idrebuilding_flussofinanziario_intervento" required class="form-control form-control-flush form-select" <?php echo $disabled_rendiconto;?> onchange="changeTIPOLOGIASPESA(this.value)">
                                <option value='0'></option>
                                <?php                                  
                                  foreach ($aINTERVENTI as $key => $aDATI) 
                                  {
                                    $idrebuilding_flussofinanziario_intervento=$aDATI['idrebuilding_flussofinanziario_intervento'];
                                    $intervento_codice=$aDATI['intervento_codice'];
                                    $intervento_titolo=stripslashes($aDATI['intervento_titolo']);
                                    $intervento_tipologiaspesa=$aDATI['idrebuilding_tipologiaspesa'];
                                    if($idrebuilding_flussofinanziario_intervento==$liquidazione->idrebuilding_flussofinanziario_intervento)
                                      echo '<option value="'.$idrebuilding_flussofinanziario_intervento.'|'.$intervento_tipologiaspesa.'" selected>'.$intervento_titolo.'/'.$aDESCRIZIONESPESA[$intervento_tipologiaspesa].'</option>';
                                    else
                                      echo '<option value="'.$idrebuilding_flussofinanziario_intervento.'|'.$intervento_tipologiaspesa.'">'.$intervento_titolo.'/'.$aDESCRIZIONESPESA[$intervento_tipologiaspesa].'</option>';
                                    
                                  }
                                ?>                                  
                              </select>
                              <label for="idrebuilding_flussofinanziario_intervento">Intervento*</label>
                            </div> 
                          </div> 
                        </div>   

                        <div class="form-group row">
                          <div class="col-12 col-md-12">

                            <div class="form-floating">
                              <select id="idrebuilding_tipologiaspesa" name="idrebuilding_tipologiaspesa" class="form-control form-control-flush form-select" disabled >
                                <option value='0'></option>
                                <?php                                  
                                  foreach ($aTIPOSPESA as $key => $aDATI) 
                                  {
                                    $idrebuilding_tipologiaspesa=$aDATI['idrebuilding_tipologiaspesa'];
                                    $tipologiaspesa_descrizione=$aDATI['tipologiaspesa_descrizione'];
                                    if($idrebuilding_tipologiaspesa==$liquidazione->idrebuilding_tipologiaspesa)
                                      echo '<option value="'.$idrebuilding_tipologiaspesa.'" selected>'.$tipologiaspesa_descrizione.'</option>';
                                    else
                                      echo '<option value="'.$idrebuilding_tipologiaspesa.'">'.$tipologiaspesa_descrizione.'</option>';
                                    
                                  }
                                ?>                                  
                              </select>
                              <label for="idrebuilding_tipologiaspesa">Tipologia spesa*</label>
                            </div> 
                          </div> 
                        </div>   

                        <div class="form-group row">

                          <div class="col-12 col-md-3">
                            <div class="form-floating">
                              <input type="text" id="liquidazione_attonumero" name="liquidazione_attonumero" class="form-control form-control-flush"  placeholder="" value="<?php echo $liquidazione->liquidazione_attonumero; ?>" <?php echo $disabled_rendiconto; ?> required >
                              <label for="liquidazione_attonumero">Numero atto liquidazione*</label>
                            </div> 
                          </div>

                          <div class="col-12 col-md-3">
                            <div class="form-floating">
                              <input type="date" id="liquidazione_attodata" name="liquidazione_attodata" class="form-control form-control-flush"  placeholder="" value="<?php echo $liquidazione->liquidazione_attodata; ?>" <?php echo $disabled_rendiconto; ?> required>
                              <label for="liquidazione_attodata">Data atto liquidazione*</label>
                            </div> 
                          </div>

                          <!--div class="col-12 col-md-3">
                            <div class="form-floating">
                              <input type="text" id="liquidazione_quietanzanumero" name="liquidazione_quietanzanumero" class="form-control form-control-flush"  placeholder="" value="<?php echo $liquidazione->liquidazione_quietanzanumero; ?>" <?php echo $disabled_rendiconto; ?>>
                              <label for="liquidazione_quietanzanumero">Numero quietanza</label>
                            </div> 
                          </div>

                          <div class="col-12 col-md-3">
                            <div class="form-floating">
                              <input type="date" id="liquidazione_quietanzadata" name="liquidazione_quietanzadata" class="form-control form-control-flush"  placeholder="" value="<?php echo $liquidazione->liquidazione_quietanzadata; ?>" <?php echo $disabled_rendiconto; ?>>
                              <label for="liquidazione_quietanzadata">Data quietanza</label>
                            </div> 
                          </div-->

                        </div>
       
                        <div class="form-group row">

                          <div class="col-12 col-md-3">
                            <div class="form-floating">
                              <input type="number" id="liquidazione_importo" step="0.01" name="liquidazione_importo" required class="form-control form-control-flush"  placeholder="" value="<?php echo $liquidazione->liquidazione_importo; ?>" <?php echo $disabled_rendiconto; ?>>
                              <label for="liquidazione_importo">Importo*</label>
                            </div> 
                          </div>


                          <div class="col-12 col-md-3">
                            <div class="form-floating">
                              <input type="number" id="liquidazione_beneficiari"  name="liquidazione_beneficiari" required class="form-control form-control-flush"  placeholder="" value="<?php echo $liquidazione->liquidazione_beneficiari; ?>" <?php echo $disabled_rendiconto; ?>>
                              <label for="liquidazione_beneficiari">Numero beneficiari*</label>
                            </div> 
                          </div>


                        </div>

                        <br>            


                        <div class="form-group row">
                          <div class="col-12">
                            <p align="right">
                              <button type="button" class="btn btn-primary btn-xs" onclick="addALLEGATO()" <?php if(empty($pidrebuilding_flussofinanziario_liquidazione)) echo 'style=display:none';?> ><i class="fe fe-plus"></i>&nbsp; Allegato</button>             
                            </p> 
                          </div>
                        </div>

                        <div id="div_allegati">
                          
                            <?php

                              if(!empty($pidrebuilding_flussofinanziario_liquidazione))
                              {
                                $aDOCUMENTI=$liquidazione->getALLEGATI();
                                $counter_allegati=0;
                                $rigadocumento="";
                                foreach ($aDOCUMENTI as $key => $aDATI) 
                                {
                                  $idrebuilding_flussofinanziario_liquidazione_documento=$aDATI["idrebuilding_flussofinanziario_liquidazione_documento"];
                                  $liquidazione_documentotitolo=$aDATI["liquidazione_documentotitolo"];
                                  $flussofinanziario_documentotipo=$aDATI["flussofinanziario_documentotipo"];
                                  $flussofinanziario_documentoente=$aDATI["flussofinanziario_documentoente"];
                                  $liquidazione_documentonome=$aDATI["liquidazione_documentonome"];
                                  $counter_allegati++;
                                  
                                  $entiselezionati=explode(",",$flussofinanziario_documentoente);                          

                                  $rigadocumento.=' <p><div class="row" id="div_allegato'.$counter_allegati.'">
                                        <div class="col-12 col-md-5" >
                                          <input id="nome_file'.$counter_allegati.'" name="nome_file'.$counter_allegati.'" type="file" class="form-control form-control-xs">
                                        </div>
                                        <div class="col-12 col-md-6" >
                                          <input type="text" id="allegatoTITOLO'.$counter_allegati.'" name="allegatoTITOLO'.$counter_allegati.'" readonly class="form-control form-control-text form-control-xs"  placeholder="titolo/descrizione" value="'.$liquidazione_documentotitolo.'" >
                                        </div>
                                        <div class="col-12 col-md-1" >
                                          <button id="consultafile" name="consultafile" class="btn btn-xs btn-rounded-circle btn-primary" onclick="viewALLEGATO(\''.$target_dir.$liquidazione_documentonome.'\')" ><i class="fe fe-file"></i></button>
                                          <button type="button" id="deletefile'.$counter_allegati.'" name="filesassegnazione'.$counter_allegati.'"  class="btn btn-xs btn-rounded-circle btn-danger" onclick="deleteALLEGATO('.$counter_allegati.')"><i class="fe fe-x"></i></button>
                                        </div>
                                      </div>
                                      <input type="hidden" id="_flussofinanziario_documento'.$counter_allegati.'" name="_flussofinanziario_documento'.$counter_allegati.'" value="'.$idrebuilding_flussofinanziario_liquidazione_documento.'">
                                      </p>';
                                      
                                }  
                                echo $rigadocumento;                                
                              }

                              
                            ?>
                        </div>

                        <br>
                        <!--div class="form-group row">
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
                        </div-->    

                        <div class="row">
                        <div class="col-12">
                          <button type="submit" class="btn w-100 btn-primary-soft mt-3 lift" id="salva" name="salva"  >Salva</button>
                        </div>
                        </div>
                        <input type="hidden" name="_salva" id="_salva" value="true">
                        <input type="hidden" name="_k" id="_k" value="<?php echo $pidrebuilding_flussofinanziario_liquidazione; ?>" >
                        <input type="hidden" name="_RENDICONTAZIONE" id="_RENDICONTAZIONE" value="<?php echo $pidrebuilding_flussofinanziario; ?>" >
                        <input type="hidden" id="counter_allegati" name="counter_allegati" value="<?php echo $counter_allegati;?>">
                        
                      </form>


                  </div>                  

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

function openELENCO()
{
  

  window.location='flussofinanziario-liquidazioni?_RENDICONTAZIONE=<?php echo $pidrebuilding_flussofinanziario;?>'

}

function addALLEGATO()
{
    var counter_allegati=parseInt($("#counter_allegati").val());

    counter_allegati=counter_allegati+1;

    var allegato=' <p><div class="row" id="div_allegato'+counter_allegati+'">\
      <div class="col-12 col-md-11" >\
        <input id="nome_file'+counter_allegati+'" name="nome_file'+counter_allegati+'" type="file" class="form-control form-control-xs">\
      </div>\
      <div class="col-12 col-md-1" >\
        <button type="button" id="deletefile'+counter_allegati+'" name="filesassegnazione'+counter_allegati+'"  class="btn btn-xs btn-rounded-circle btn-danger" onclick="deleteALLEGATO('+counter_allegati+')"><i class="fe fe-x"></i></button>\
      </div>\
    </div><input type="hidden" id="_flussofinanziario_documento'+counter_allegati+'" name="_flussofinanziario_documento'+counter_allegati+'" value=""></p>';

    $("#div_allegati").append(allegato);
    
    $("#counter_allegati").val(counter_allegati);
     
}

function deleteALLEGATO(idallegato)
{
  idDOCUMENTO=$("#_flussofinanziario_documento"+idallegato).val()
  //alert(idDOCUMENTO)
  if(idDOCUMENTO)
  {
    $.confirm({
        title: 'Attenzione!',
        content: 'Sicuro di voler eliminare l\'allegato selezionato?',
        buttons: {
            Elimina: function () {
              var page="rebuilding_action.php";
              var params="_action=deletedocumentoliquidazione&_documento="+idDOCUMENTO;
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
            },
            Chiudi: function () {
                //$.alert('Canceled!');
            }
        }
    });
    /*
    BootstrapDialog.show({
        title: 'Attenzione',
        type: BootstrapDialog.TYPE_WARNING, 
        message: 'Sicuro di voler eliminare l\'allegato selezionato?',
        buttons: [{
            label: 'Annulla',
            action: function(dialog) {
                dialog.close();
            },
            label: 'Elimina',
            action: function(dialog) {
             
             
            }
        }]
    });	
    */
  }
  else
  {
    /*
    BootstrapDialog.show({
        title: 'Attenzione',
        type: BootstrapDialog.TYPE_WARNING, 
        message: 'Anomalia nell\'eliminazione, contattare l\'assistenza',
        buttons: [{
            label: 'Chiudi',
            action: function(dialog) {
                dialog.close();
            }
        }]
    });	
    */
  }
}

function viewALLEGATO(myFILENAME)
{
  window.open(myFILENAME,'_blank')
}

function changeTIPOLOGIASPESA(myVALUE)
{
  aVALUE=myVALUE.split('|')
  $("#idrebuilding_tipologiaspesa").val(aVALUE[1])
}

function doOnSubmit()
{
  var counter_allegati=parseInt($("#counter_allegati").val());

}
</script>

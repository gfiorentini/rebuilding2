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

//$operatore=new DARAOperatore($idoperatore);
//$autorizzazioni_domanda=$operatore->getAUORIZZAZIONEdomanda();

$aENTI=array(1=>"ATS1 - Pesaro",3=>"ATS3 - C.M. Catria e Nerone",4=>"ATS4 - Urbino",5=>"ATS5 - C.M. Montefeltro",6=>"ATS6 - Fano",7=>"ATS7 - Fossombrone",8=>"ATS8 - Senigallia",9=>"ATS9 - ASP Ambito 9 Jesi",10=>"ATS10 - Fabriano",11=>"ATS11 - Ancona",12=>"ATS12 - Falconara Marittima",13=>"ATS13 - Osimo",14=>"ATS14 - Civitanova Marche",15=>"ATS15 - Macerata",16=>"ATS16 - C.M. Monti Azzurri",17=>"ATS17 - Unione montanta alta valle del potenza e dell'Esino",18=>"ATS18 - C.M. Camerino",19=>"ATS19 - Fermo",20=>"ATS20 - Porto Sant'Elpidio",21=>"ATS 21 - San Benedetto del Tronto",22=>"ATS22 - Ascoli Piceno",23=>"ATS23 - U.C. Vallata del Tronto",24=>"ATS24 - C.M. dei Sibillini");

$aTIPODOCUMENTO=array(1=>"RIPARTO",2=>"CRONOPROGRAMMA",3=>"MODULISTICA",4=>"ALTRO");

$operatore_flagamministratore=$db->getVALUE("select operatore_flagamministratore from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagamministratore");


$pidrebuilding_scadenzario=getPARAMETRO("_SCADENZA");
$pidrebuilding_scadenzario=$db->escape_text($pidrebuilding_scadenzario);

$pscadenza_testo=getPARAMETRO("scadenza_testo");
$pscadenza_testo=$db->escape_text($pscadenza_testo);

$pscadenza_destinatario=getPARAMETRO("scadenza_destinatario");
$pscadenza_destinatario=$db->escape_text($pscadenza_destinatario);


$pscadenza_datainizio=getPARAMETRO("scadenza_datainizio");
$pscadenza_datainizio=$db->escape_text($pscadenza_datainizio);

$pscadenza_datafine=getPARAMETRO("scadenza_datafine");
$pscadenza_datafine=$db->escape_text($pscadenza_datafine);

if(getPARAMETRO("_elimina"))
{
  $sSQL="delete from rebuilding_scadenzario where idrebuilding_scadenzario='$pidrebuilding_scadenzario'";
  $db->query($sSQL);
  $sSQL="delete from rebuilding_scadenzario_ente where idrebuilding_scadenzario='$pidrebuilding_scadenzario'";
  $db->query($sSQL);
}

if(empty($operatore_flagamministratore))
{
  $aENTISELEZIONATI=array();
  $aENTISELEZIONATI[$operatore_ente]=$aENTI[$operatore_ente];
  $aENTI=$aENTISELEZIONATI;
  $disable_importaxls="disabled";

}

$aSTATI=array(1=>"NON SCADUTA",2=>"SCADUTA");

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
    $aBREADCUMB[3]["url"]="";

		generaBREADCUMB($aBREADCUMB);

	  ?>

    <!-- RICERCA -->
    <section class="py-6 bg-light">
      <div class="col-10 offset-1">
        <div class="row">
          <div class="col-12">
              <p align="right">
                  <button type="button" class="btn btn-primary-soft btn-xs" onclick="apriSCADENZA('0')" <?php if($operatore_flagamministratore!='1') echo 'style="display:none"'; ?>><i class="fe fe-plus"  ></i>&nbsp;Scadenza</button>
                  <button type="button" class="btn btn-sm btn-primary-soft  btn-xs" onclick="loadDATA();"><i class="fe fe-search"></i>&nbsp;Ricerca</button>
              </p>
          </div>  
        </div> 
        <div class="row">
          <div class="col-12">

            <form class="rounded shadow" method="post" action="scadenzario" id="formSCADENZE" name="formSCADENZE" >

              <div class="input-group input-group-lg" style="background-color: #FFFFFF;">

                <div class="form-floating col-sm-4 p-2">
                  <select id="scadenza_destinatario" name="scadenza_destinatario" class="form-select" required>
                    
                    <?php
                      
                      if(!empty($operatore_flagamministratore))
                      {
                        echo "<option value='0'>Tutti</option>";
                      }
                      foreach ($aENTI as $key => $descrizione) 
                      {

                        if($key==$pscadenza_destinatario)
                          echo '<option value="'.$key.'" selected>'.$descrizione.'</option>';
                        else  
                          echo '<option value="'.$key.'">'.$descrizione.'</option>';
                        
                      }
                    ?>                                               
                  </select>
                  <label for="centroterritoriale">Centro territoriale/ATS</label>
                </div>   
  
                <div class="form-floating col-sm-3 p-2">
                  <input type="text" oninput="let p=this.selectionStart;" class="form-control" id="scadenza_testo" name="scadenza_testo" placeholder="Testo" value="<?php echo $pscadenza_testo; ?>">
                  <label for="scadenza_testo">Testo</label>
                </div>
                          
                <div class="form-floating col-sm-2 p-2">
                  <input type="date" oninput="let p=this.selectionStart;" class="form-control" id="scadenza_datainizio" name="scadenza_datainizio" placeholder="Testo" value="<?php echo $pscadenza_datainizio; ?>">
                  <label for="scadenza_datainizio">Dal</label>
                </div>

                <div class="form-floating col-sm-2 p-2">
                  <input type="date" oninput="let p=this.selectionStart;" class="form-control" id="scadenza_datafine" name="scadenza_datafine" placeholder="Testo" value="<?php echo $pscadenza_datafine; ?>">
                  <label for="scadenza_datafine">Al</label>
                </div>                

             </div>


              </div>
                <input type="hidden" name="_ricerca" id="_ricerca" value="true">
            </form>

          </div>
        </div>
      </div>
    </section>
   
	<!-- ELENCO -->
    <section class="pt-6 pt-md-4 bg-light">
      <div class="col-10 offset-1 pb-10 pb-md-11 ">

        <div class="row">
          <div class="col-12">

            <div class="table-responsive mb-7 mb-md-9">

              <table class="table table-striped">
                <thead class="fs-6">
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Descrizione</th>
                    <th scope="col">Destinatari</th>
                    <th scope="col">Flusso finanziario</th>
                    <th scope="col">Scade il</th>
                    <th scope="col">Stato</th>
                    <th scope="col"></th>
                  </tr>
                </thead>
                <tbody id="post_data" class="fs-6">
                  <?php

                    $sWhere="";
                    $oggi=date("Y-m-d");
                    if(!empty($pscadenza_testo))
                    {
                      if(!empty($sWhere))
                        $sWhere.=" and ";
                      $sWhere.=" scadenza_testo like '%".$pscadenza_testo."%' ";
                    }


                    if(!empty($pscadenza_datainizio))
                    {
                      if(!empty($sWhere))
                        $sWhere.=" and ";
                      $sWhere.=" scadenza_data>='".$pscadenza_datainizio."' ";
                    }
                    /*
                      if(!empty($pscadenza_destinatario))
                      {
                        if(!empty($sWhere))
                          $sWhere.=" and ";
                        $sWhere.=" rebuilding_scadenzario_ente.notifica_ente='".$pscadenza_destinatario."' ";
                      }
                    */
                    if(!empty($pscadenza_datafine))
                    {
                      if(!empty($sWhere))
                        $sWhere.=" and ";
                      $sWhere.=" scadenza_data<='".$pscadenza_datafine."' ";
                    }                                                            

                    if(!empty($sWhere))
                      $sWhere=" WHERE ".$sWhere;

                    $notifiche=new rebuildingSCADENZARIO();
                    if(empty($pscadenza_destinatario))
                      $aSCADENZE=$notifiche->getSCADENZE($sWhere);
                    else
                      $aSCADENZE=$notifiche->getSCADENZEENTE($sWhere);
                    
                    $aSCADENZESTAMPATE=array();
                    foreach ($aSCADENZE as $key => $aDATI) 
                    {
                      $idrebuilding_scadenzario=$aDATI["idrebuilding_scadenzario"];
                      if(!in_array($idrebuilding_scadenzario,$aSCADENZESTAMPATE))
                      {
                        $aSCADENZESTAMPATE[]=$idrebuilding_scadenzario;
                        $scadenza_datainserimento=$aDATI["scadenza_datainserimento"];
                        $scadenza_data=$aDATI["scadenza_data"];
                        $scadenza_ora=$aDATI["scadenza_ora"];
                        $scadenza_destinatario=$aDATI["scadenza_destinatario"];
                        $scadenza_testo=$aDATI["scadenza_testo"];
                        //$scadenza_stato=$aDATI["scadenza_stato"];
                        //$statoscadenza=$aSTATI[$scadenza_stato];

                        if($oggi>$scadenza_data)
                          $statoscadenza="SCADUTA";
                        else
                          $statoscadenza="NON SCADUTA";

                        $idrebuilding_flussofinanziario=$aDATI["idrebuilding_flussofinanziario"];

                        $flussofinanziario=new rebuildingFLUSSOFINANZIARIO($idrebuilding_flussofinanziario); 

                        $scadenza_datainserimento=dataitaliana($scadenza_datainserimento);
                        $scadenza_data=dataitaliana($scadenza_data);
                        $aentiselezionati=explode(",",$aDATI["scadenza_destinatario"]);
                        $destinatari="";
                        foreach ($aentiselezionati as $key => $value) 
                        {

                          if($destinatari)
                            $destinatari.=", ";
                          $destinatari.=$aENTI[$value];
                        }

                        $visibile=true;
                        if(!empty($pscadenza_destinatario) && !in_array($pscadenza_destinatario,$aentiselezionati) )
                        {
                          $visibile=false;
                        }

                        if($visibile)
                        {
                          $confirm='<a href=\'#\'><span class=\'badge bg-primary-soft\'>NO</span></a>&nbsp;<a href=\'rebuilding_scadenzario.php?_SCADENZA='.$aDATI["idrebuilding_scadenzario"].'&_elimina=true\'><span class=\'badge bg-primary-soft\' >SI</span></a>';

                          echo '<tr>';
                          echo '<th>'.$idrebuilding_scadenzario.'</th>';
                          echo '<th>'.$scadenza_testo.'</th>';
                          echo '<th>'.$destinatari.'</th>';
                          echo '<th>'.$flussofinanziario->flussofinanziario_titolo.'</th>';
                          echo '<th>'.$scadenza_data.'</th>';
                          echo '<th>'.$statoscadenza.'</th>';
                          echo '<th class="text-right">';
                          if($operatore_flagamministratore==1)
                          {

                            if($scadenza_stato==2)
                                echo '<button id="editnotifica" name="editnotifica" class="btn btn-xs btn-rounded-circle btn-success" onclick="apriSCADENZA(\''.$idrebuilding_scadenzario.'\')" ><i class="fe fe-check"></i></button>';
                            else      
                                echo '<button id="editnotifica" name="editnotifica" class="btn btn-xs btn-rounded-circle btn-primary" onclick="apriSCADENZA(\''.$idrebuilding_scadenzario.'\')" ><i class="fe fe-check"></i></button>
                                  <button id="deleteassegnazione'.$aDATI["idrebuilding_scadenzario"].'" name="deleteassegnazione'.$aDATI["idrebuilding_scadenzario"].'" class="btn btn-xs btn-rounded-circle btn-danger" data-bs-container="body" data-bs-toggle="popover" data-bs-placement="left" title="Confermi l\'eliminazione?" data-bs-content="'.$confirm.'" data-bs-html=true data-bs-trigger="focus"><i class="fe fe-x"></i></button>';                          

                          }      
                          echo '</th>';                      
                          echo '</tr>';                        
                        }                        
                      }



                    }
                  ?>
                </tbody>
              </table>

              <div style="margin-top: 5%;" id="pagination_link"></div>

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

function apriSCADENZA(mySCADENZA)
{
  
  window.location=('scadenza?_SCADENZA='+mySCADENZA)

}

function loadDATA()
{
  $("#formSCADENZE").submit()
}


function doOnSubmit()
{


}

</script>

<?php

require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");
require_once("../librerie/dara.class.servizi.php");

//error_reporting(0);
//ini_set("display_errors",1);

$idoperatore=verificaUSER();

//$operatore=new DARAOperatore($idoperatore);
//$autorizzazioni_domanda=$operatore->getAUORIZZAZIONEdomanda();

$aENTI=array(1=>"ATS1 - Pesaro",3=>"ATS3 - C.M. Catria e Nerone",4=>"ATS4 - Urbino",5=>"ATS5 - C.M. Montefeltro",6=>"ATS6 - Fano",7=>"ATS7 - Fossombrone",8=>"ATS8 - Senigallia",9=>"ATS9 - ASP Ambito 9 Jesi",10=>"ATS10 - Fabriano",11=>"ATS11 - Ancona",12=>"ATS12 - Falconara Marittima",13=>"ATS13 - Osimo",14=>"ATS14 - Civitanova Marche",15=>"ATS15 - Macerata",16=>"ATS16 - C.M. Monti Azzurri",18=>"ATS18 - C.M. Camerino",19=>"ATS19 - Fermo",20=>"ATS20 - Porto Sant'Elpidio",21=>"ATS21 - San Benedetto del Trotto",22=>"ATS22 - Ascoli Piceno",23=>"ATS23 - U.C. Vallata del Tronto",24=>"ATS24 - C.M. dei Sibillini");


$aTIPODOCUMENTO=array(1=>"RIPARTO",2=>"CRONOPROGRAMMA",3=>"MODULISTICA",4=>"ALTRO");

$operatore_flagamministratore=$db->getVALUE("select operatore_flagamministratore from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagamministratore");


$pidrebuilding_notifica=getPARAMETRO("_NOTIFICA");
$pidrebuilding_notifica=$db->escape_text($pidrebuilding_notifica);

$pnotifica_testo=getPARAMETRO("notifica_testo");
$pnotifica_testo=$db->escape_text($pnotifica_testo);

$pnotifica_destinatario=getPARAMETRO("notifica_destinatario");
$pnotifica_destinatario=$db->escape_text($pnotifica_destinatario);


$pnotifica_datainizio=getPARAMETRO("notifica_datainizio");
$pnotifica_datainizio=$db->escape_text($pnotifica_datainizio);

$pnotifica_datafine=getPARAMETRO("notifica_datafine");
$pnotifica_datafine=$db->escape_text($pnotifica_datafine);

if(getPARAMETRO("_elimina"))
{
  $sSQL="delete from rebuilding_notifica where idrebuilding_notifica='$pidrebuilding_notifica'";
  $db->query($sSQL);
  $sSQL="delete from rebuilding_notifica_email where idrebuilding_notifica='$pidrebuilding_notifica'";
  $db->query($sSQL);
  $sSQL="delete from rebuilding_notifica_ente where idrebuilding_notifica='$pidrebuilding_notifica'";
  $db->query($sSQL);
}

$aSTATI=array(1=>"NON INVIATA",2=>"INVIATA");


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
    $aBREADCUMB[3]["url"]="";

		generaBREADCUMB($aBREADCUMB);

	  ?>

    <!-- RICERCA -->
    <section class="py-6 bg-light">
      <div class="col-10 offset-1">
        <div class="row">
          <div class="col-12">
              <p align="right">
                  <button type="button" class="btn btn-primary-soft btn-xs" onclick="apriNOTIFICA('0')"><i class="fe fe-plus"></i>&nbsp;Notifica</button>
                  <button type="button" class="btn btn-sm btn-primary-soft  btn-xs" onclick="loadDATA();"><i class="fe fe-search"></i>&nbsp;Ricerca</button>
              </p>
          </div>  
        </div> 
        <div class="row">
          <div class="col-12">

            <form class="rounded shadow" method="post" action="notifiche" id="formNOTIFICHE" name="formNOTIFICHE" >

              <div class="input-group input-group-lg" style="background-color: #FFFFFF;">

                <div class="form-floating col-sm-4 p-2">
                  <select id="notifica_destinatario" name="notifica_destinatario" class="form-select" required>
                    <option value='0'>Tutti</option>
                    <?php
                      
                      
                      foreach ($aENTI as $key => $descrizione) 
                      {

                        if($key==$pnotifica_destinatario)
                          echo '<option value="'.$key.'" selected>'.$descrizione.'</option>';
                        else  
                          echo '<option value="'.$key.'">'.$descrizione.'</option>';
                        
                      }
                    ?>                                               
                  </select>
                  <label for="centroterritoriale">Centro territoriale/ATS</label>
                </div>   
  
                <div class="form-floating col-sm-3 p-2">
                  <input type="text" oninput="let p=this.selectionStart;" class="form-control" id="notifica_testo" name="notifica_testo" placeholder="Testo" value="<?php echo $pnotifica_testo; ?>">
                  <label for="notifica_testo">Testo</label>
                </div>
                          
                <div class="form-floating col-sm-2 p-2">
                  <input type="date" oninput="let p=this.selectionStart;" class="form-control" id="notifica_datainizio" name="notifica_datainizio" placeholder="Testo" value="<?php echo $pnotifica_datainizio; ?>">
                  <label for="notifica_datainizio">Dal</label>
                </div>

                <div class="form-floating col-sm-2 p-2">
                  <input type="date" oninput="let p=this.selectionStart;" class="form-control" id="notifica_datafine" name="notifica_datafine" placeholder="Testo" value="<?php echo $pnotifica_datafine; ?>">
                  <label for="notifica_datafine">Al</label>
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
                    <th scope="col">Data</th>
                    <th scope="col">Inviata il</th>
                    <th scope="col">Destinatari</th>
                    <th scope="col">Testo</th>
                    <th scope="col">Stato</th>
                    <th scope="col"></th>
                  </tr>
                </thead>
                <tbody id="post_data" class="fs-6">
                  <?php

                    $sWhere="";

                    if(!empty($pnotifica_testo))
                    {
                      if(!empty($sWhere))
                        $sWhere.=" and ";
                      $sWhere.=" notifica_testo like '%".$pnotifica_testo."%' ";
                    }


                    if(!empty($pnotifica_datainizio))
                    {
                      if(!empty($sWhere))
                        $sWhere.=" and ";
                      $sWhere.=" notifica_data>='".$pnotifica_datainizio."' ";
                    }

                    if(!empty($pnotifica_destinatario))
                    {
                      if(!empty($sWhere))
                        $sWhere.=" and ";
                      $sWhere.=" rebuilding_notifica_ente.notifica_ente='".$pnotifica_destinatario."' ";
                    }

                    if(!empty($pnotifica_datafine))
                    {
                      if(!empty($sWhere))
                        $sWhere.=" and ";
                      $sWhere.=" notifica_data<='".$pnotifica_datafine."' ";
                    }                                                            

                    if(!empty($sWhere))
                      $sWhere=" WHERE ".$sWhere;

                    $notifiche=new rebuildingNOTIFICA();
                    if(empty($pnotifica_destinatario))
                      $aNOTIFICHE=$notifiche->getNOTIFICHE($sWhere);
                    else
                      $aNOTIFICHE=$notifiche->getNOTIFICHEENTE($sWhere);
                    
                    foreach ($aNOTIFICHE as $key => $aDATI) 
                    {
                      $idrebuilding_notifica=$aDATI["idrebuilding_notifica"];
                      $notifica_datainserimento=$aDATI["notifica_datainserimento"];
                      $notifica_data=$aDATI["notifica_data"];
                      $notifica_ora=$aDATI["notifica_ora"];
                      $notifica_destinatario=$aDATI["notifica_destinatario"];
                      $notifica_oggetto=$aDATI["notifica_oggetto"];
                      $notifica_testo=$aDATI["notifica_testo"];
                      $notifica_stato=$aDATI["notifica_stato"];
                      $statonotifica=$aSTATI[$notifica_stato];

                      $notifica_datainserimento=dataitaliana($notifica_datainserimento);
                      $notifica_data=dataitaliana($notifica_data);
                      $aentiselezionati=explode(",",$aDATI["notifica_destinatario"]);
                      $destinatari="";
                      foreach ($aentiselezionati as $key => $value) 
                      {

                        if($destinatari)
                          $destinatari.=", ";
                        $destinatari.=$aENTI[$value];
                      }

                      $confirm='<a href=\'#\'><span class=\'badge bg-primary-soft\'>NO</span></a>&nbsp;<a href=\'rebuilding_notifiche.php?_NOTIFICA='.$aDATI["idrebuilding_notifica"].'&_elimina=true\'><span class=\'badge bg-primary-soft\' >SI</span></a>';

                      echo '<tr>';
                      echo '<th>'.$idrebuilding_notifica.'</th>';
                      echo '<th>'.$notifica_datainserimento.'</th>';
                      echo '<th>'.$notifica_data." ".substr($notifica_ora,0,5).'</th>';
                      echo '<th>'.$destinatari.'</th>';
                      echo '<th>'.$notifica_testo.'</th>';
                      echo '<th>'.$statonotifica.'</th>';
                      echo '<th class="text-right">';
                      if($operatore_flagamministratore==1)
                      {

                        if($notifica_stato==2)
                            echo '<button id="editnotifica" name="editnotifica" class="btn btn-xs btn-rounded-circle btn-success" onclick="apriNOTIFICA(\''.$idrebuilding_notifica.'\')" ><i class="fe fe-check"></i></button>';
                        else      
                            echo '<button id="editnotifica" name="editnotifica" class="btn btn-xs btn-rounded-circle btn-primary" onclick="apriNOTIFICA(\''.$idrebuilding_notifica.'\')" ><i class="fe fe-check"></i></button>
                              <button id="deleteassegnazione'.$aDATI["idrebuilding_notifica"].'" name="deleteassegnazione'.$aDATI["idrebuilding_notifica"].'" class="btn btn-xs btn-rounded-circle btn-danger" data-bs-container="body" data-bs-toggle="popover" data-bs-placement="left" title="Confermi l\'eliminazione?" data-bs-content="'.$confirm.'" data-bs-html=true data-bs-trigger="focus"><i class="fe fe-x"></i></button>';                          

                      }      
                      echo '</th>';                      
                      echo '</tr>';

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

function apriNOTIFICA(myNOTIFICA)
{

/*
  $("#formANNO").val('')
  $("#formENTE").val('')
  $("#formAREAINTERVENTO").val('')
  $("#formLEGGERIFERIMENTO").val('')
  $("#formTESTO").val('')
  $("#formTITOLO").val('')
  $("#formCONTATTI").val('')
  $("#formCLASSIFICAZIONE").val('')
  $("#formRUP").val('')
  $("#formSTATO").val('')
  $("#formDATACREAZIONE").val('')
  $("#formOPERATORE").val('')
  $("#formULTIMAMODIFICA").val('')


  $("#formDOCUMENTO1").val('')
  $("#formDOCUMENTO2").val('')
  $("#formDOCUMENTO3").val('')
  $("#formDOCUMENTO4").val('')
  $("#formDOCUMENTO5").val('')
  $("#formDOCUMENTO6").val('')

  $("#formTIPODOCUMENTO1").val('')
  $("#formTIPODOCUMENTO2").val('')
  $("#formTIPODOCUMENTO3").val('')
  $("#formTIPODOCUMENTO4").val('')
  $("#formTIPODOCUMENTO5").val('')
  $("#formTIPODOCUMENTO6").val('')

  $("#_RENDICONTAZIONE").val('')
  $("#modalNOTIFICHE").modal('show');
*/
  
  window.location=('notifica?_NOTIFICA='+myNOTIFICA)

}

function loadDATA()
{
  $("#formNOTIFICHE").submit()
}


function doOnSubmit()
{


}

</script>

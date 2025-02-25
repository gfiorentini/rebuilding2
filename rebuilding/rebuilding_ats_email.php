<?php

require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");
require_once("../librerie/dara.class.servizi.php");

//error_reporting(0);
//ini_set("display_errors",1);

$idoperatore=verificaUSER();


$aENTI=array(1=>"ATS1 - Pesaro",3=>"ATS3 - C.M. Catria e Nerone",4=>"ATS4 - Urbino",5=>"ATS5 - C.M. Montefeltro",6=>"ATS6 - Fano",7=>"ATS7 - Fossombrone",8=>"ATS8 - Senigallia",9=>"ATS9 - ASP Ambito 9 Jesi",10=>"ATS10 - Fabriano",11=>"ATS11 - Ancona",12=>"ATS12 - Falconara Marittima",13=>"ATS13 - Osimo",14=>"ATS14 - Civitanova Marche",15=>"ATS15 - Macerata",16=>"ATS16 - C.M. Monti Azzurri",17=>"ATS17 - Unione montanta alta valle del potenza e dell'Esino",18=>"ATS18 - C.M. Camerino",19=>"ATS19 - Fermo",20=>"ATS20 - Porto Sant'Elpidio",21=>"ATS 21 - San Benedetto del Tronto",22=>"ATS22 - Ascoli Piceno",23=>"ATS23 - U.C. Vallata del Tronto",24=>"ATS24 - C.M. dei Sibillini");

$operatore_flagamministratore=$db->getVALUE("select operatore_flagamministratore from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagamministratore");

$pats_email=getPARAMETRO("emailATS");
$pats_email=$db->escape_text($pats_email);


if(getPARAMETRO("_salva"))
{


  $patsID=getPARAMETRO("_ats");
  $patsID=$db->escape_text($patsID);


  $pnotifica_datainserimento=date("Y-m-d");
    $pnotifica_ultimamodifica=date("Y-m-d");

    $sSQL="UPDATE rebuilding_ats  SET
    ats_email='$pats_email'
    WHERE idrebuilding_ats='$patsID'"; 
    $db->query($sSQL);

    $alert_update_success=true;


}

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

    $aBREADCUMB[1]["titolo_pagina"]="ATS";
    $aBREADCUMB[1]["url"]="";

		generaBREADCUMB($aBREADCUMB);

	  ?>


    <!-- MODALS -->
    <div class="modal fade" id="modalEMAIL" tabindex="-1" role="dialog" aria-labelledby="modalEMAILTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body">
    
            <!-- Close -->
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
    
            <!-- Heading -->
            <h2 class="fw-bold text-center mb-1" id="modalEMAILTitle">
              Email ATS
            </h2>
    
            <!-- Text -->
            <p class="fs-md text-center text-muted mb-6 mb-md-8">
              Indicare di seguito uno o più indirizzi email separati da ;
            </p>
    
            <!-- Form -->
            <form id="formATSEMAIL" name="formATSEMAIL" method="post" action="ats" >
              <div class="row">
                <div class="col-12 col-md-12">
    
                  <!-- Email -->
                  <div class="form-floating">
                    <input type="text" class="form-control form-control-flush" id="emailATS" name="emailATS" placeholder="Email dell'ATS" value="">
                    <label for="registrationEmailModal">Email</label>
                  </div>
    
                </div>

              </div>
              <div class="row">
                <div class="col-12">
    
                  <!-- Submit -->
                  <button class="btn w-100 btn-primary mt-3 lift" id="_salva" name="_salva" value="true">
                    Salva
                  </button>
    
                </div>
              </div>

                <input type="hidden" name="_ats" id="_ats" value="">
            </form>
    
          </div>
        </div>
      </div>
    </div>
   
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
                    <th scope="col">Ambito territoriale sociale</th>
                    <th scope="col">Email</th>
                    <th scope="col"></th>
                  </tr>
                </thead>
                <tbody id="post_data" class="fs-6">
                  <?php

                    $sWhere="";

                    
                    foreach ($aENTI as $key => $nomeente) 
                    {

                      $email=$db->getVALUE("select ats_email from rebuilding_ats where idrebuilding_ats='$key'",'ats_email');
                      echo '<tr>';
                      echo '<th>'.$key.'</th>';
                      echo '<th>'.$nomeente.'</th>';
                      echo '<th>'.$email.'</th>';
                      echo '<th class="text-right">';
                      if($operatore_flagamministratore==1)
                      {

                          echo '<button id="editassegnazione" name="editassegnazione" class="btn btn-xs btn-rounded-circle btn-primary" onclick="apriEMAIL(\''.$email.'\',\''.$key.'\')" ><i class="fe fe-check"></i></button>';

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

function apriEMAIL(myEMAIL,myATS)
{


  $("#emailATS").val(myEMAIL)
  
  $("#_ats").val(myATS)
  $("#modalEMAIL").modal('show');  
 

}


function doOnSubmit()
{



}

</script>

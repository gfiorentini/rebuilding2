<?php

require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");

global $db;

$idoperatore=verificaUSER();

//$operatore=new DARAOperatore($idoperatore);
//$autorizzazioni=$operatore->getAUORIZZAZIONI();

$operatore_flagamministratore=$db->getVALUE("select operatore_flagamministratore from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagamministratore");


$pidrebuilding_sportello=getPARAMETRO("_k");
//$aSPORTELLI=array(1=>"Gare/accreditamento/coprogettazione",2=>"",3=>"",4=>"",5=>"",6=>"");

$sportello=new rebuildingSPORTELLO($pidrebuilding_sportello);

$flag_domandasuccess=false;
$flag_rispostasuccess=false;
$aCORSI=$sportello->getFORMAZIONI();

?>
<!doctype html>
<html lang="it">
  <head>

  	<?php echo getREBUILDINGHEAD(true); ?>

  </head>
  <body>

  	<?php echo getREBUILDINGNAVBAR(); ?>

    <!-- BREADCRUMB -->
    <nav class="bg-gray-200">
      <div class="container">
        <div class="row">
          <div class="col-12">

            <!-- Breadcrumb -->
            <ol class="breadcrumb breadcrumb-scroll">
              <li class="breadcrumb-item">
                <a href="home" class="text-gray-700">
                  Home page
                </a>
              </li>
              <li class="breadcrumb-item">
                <a href="toolkit_menu" class="text-gray-700">
                  Toolkit
                </a>
              </li>              
              <li class="breadcrumb-item" aria-current="page">
                <a href="biblioteca" class="text-gray-700">
                Biblioteca
                </a>
              </li>
              <li class="breadcrumb-item active" aria-current="page">
                <?php echo $sportello->sportello_titolo;?>
              </li>
              
            </ol>

          </div>
        </div> <!-- / .row -->
      </div> <!-- / .container -->
    </nav>
    

    <!-- MODALS -->    
    <div class="modal fade" id="modalRISPOSTA" name="modalRISPOSTA" tabindex="-1" role="dialog" aria-labelledby="modalRISPOSTATITOLO" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg" >
        <div class="modal-content">
          <div class="modal-body">
    
            <!-- Close -->
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
    
            <!-- Heading -->
            <h3 class="fw-bold text-center mb-1" id="modalRISPOSTATITOLO">
              Risposta
            </h3>
      
            <div id="alert_config_autocertificazione"></div>

            <!-- Form -->
            <form id="autocertificazioneFORM" name="autocertificazioneFORM" method="post" role="form">
              <div class="row">
                <div class="col-12 col-md-12">
    
                    <div class="form-floating">
                      <textarea id="risposta_descrizione" name="risposta_descrizione" class="form-control form-control-flush" style="height:250px" placeholder="Inserire il testo della risposta" <?php echo $isENABLED; ?> required ></textarea>
                      <label for="risposta_descrizione">Riposta*</label>
                    </div> 
                </div>
              </div>


              <div class="row">
                <div class="col-12">
                    <button type="submit" id="saveRISPOSTA" name="saveRISPOSTA" value="true" class="btn w-100 btn-primary-soft mt-3 lift">
                    Salva
                  </button>
                </div>
              </div>

              <input type="hidden" name="_k" id="_k" value="<?php echo $pidrebuilding_sportello; ?>" >
              <input type="hidden" name="_domanda" id="_domanda" value="" >
            </form>
    
          </div>
        </div>
      </div>
    </div>


     <!-- CONTENT -->
    <section class="pt-6 pt-md-8 pb-8 mb-md-8">
      <div class="container">
        <?php
          if ($flag_domandasuccess) echo '<div class="alert alert-success" role="alert">La domanda è stata inserita con successo.</div>';
          if ($flag_rispostasuccess) echo '<div class="alert alert-success" role="alert">La risposta è stata inserita con successo.</div>';
        ?>
        <div class="row">
          <div class="col-12">

            <div class="row mb-6 mb-md-8">
              <div class="col-auto">

                <!-- Icon -->
                <div class="icon-circle bg-primary text-white">
                  <i class="fe fe-users"></i>
                </div>

              </div>
              <div class="col ms-n4">

               
                <h2 class="fw-bold mb-0">
                   <?php echo $sportello->sportello_titolo;?>
                </h2>

              
                <!--p class="fs-lg text-gray-700 mb-0">
                   <?php echo $sportello->sportello_testo;?>
                </p-->

              </div>
            </div> <!-- / .row -->

            <!-- Card -->
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                    <tr>
                        <th >#</th>
                        <th >Titolo</th>
                        <th >Link</th>
                    </tr> 
                </thead>
                <tbody>
            <?php

              $iCounter=1;
              foreach ($aCORSI as $key => $aDATI) 
              {
                $idrebuilding_sportello_corso=$aDATI['idrebuilding_sportello_corso'];
                $corso_data=$aDATI['corso_data'];
                $corso_orario=$aDATI['corso_orario'];
                $corso_operatore=$aDATI['corso_operatore'];
                $corso_testo=$aDATI['corso_testo'];
                $corso_video=$aDATI['corso_video'];
                if($corso_video)
                {
                  $corso_video='<a href="'.$corso_video.'" target="_blank">'.$corso_video.'</a>';
                }

                echo '<tr>';
                echo '<td>'.$iCounter.'</td>';
                echo '<td>'.$corso_testo.'</td>';
                echo '<td>'.$corso_video.'</td>';
                echo '</tr>';

                $iCounter++;
              }  

            ?>
                </tbody>
              </table>
            </div>
          </div>
        </div> <!-- / .row -->
      </div> <!-- / .container -->
    </section> 
	  


    <!-- JAVASCRIPT -->
    <!-- Map JS -->
    <script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
    
    <!-- Vendor JS -->
    <script src="../librerie/assets/js/vendor.bundle.js"></script>
    
    <!-- Theme JS -->
    <script src="../librerie/assets/js/theme.bundle.js"></script>

  </body>
</html>
<script>
function insertRISPOSTA(kDOMANDA)
{

}
</script>

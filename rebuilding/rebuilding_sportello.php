<?php

require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");
require_once("../librerie/mail/lib.mail.php");


global $db;
/*
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
*/
$idoperatore=verificaUSER();

//$operatore=new DARAOperatore($idoperatore);
//$autorizzazioni=$operatore->getAUORIZZAZIONI();

$operatore_flagamministratore=$db->getVALUE("select operatore_flagamministratore from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagamministratore");


$pidrebuilding_sportello=getPARAMETRO("_k");
//$aSPORTELLI=array(1=>"Gare/accreditamento/coprogettazione",2=>"",3=>"",4=>"",5=>"",6=>"");

$sportello=new rebuildingSPORTELLO($pidrebuilding_sportello);
$flag_domandasuccess=false;
$flag_rispostasuccess=false;

if(getPARAMETRO("saveDOMANDA"))
{
  $ptestoDOMANDA=getPARAMETRO("testoDOMANDA");
  $ptestoDOMANDA=$db->escape_text($ptestoDOMANDA);

  $sportello->insertDOMANDA($ptestoDOMANDA);
  $flag_domandasuccess=true;
  //
  /** Invia mail al referente dello sportello */
  //
  $id_utente_esperto_sportello = $sportello->sportello_esperto;
  $email_referente_sportello=$db->getVALUE("select operatore_email from dara_operatore where iddara_operatore='$id_utente_esperto_sportello' ","operatore_email");
  // $titolo_sportello = $sportello.$sportello_titolo;
  //
  $aEMAIL=array();
  $aEMAIL[0]=$email_referente_sportello;  // destinatario
  // $aEMAIL[0]="giacomo.fiorentini@regione.marche.it";
  $aEMAIL[1]="Nuova domanda per il progetto Rebuilding - Regione Marche " ; // oggetto
  $aEMAIL[2]="Buongiorno,<br>La presente per comunicarLe che e' stata inoltrata una nuova domanda per lo sportello tematico di Sua competenza.<br> {$ptestoDOMANDA}"; // corpo
  $aEMAIL[3]=''; // attachment
  //
  //
  $mail_result=sendMAIL($aEMAIL);
  //
}
elseif(getPARAMETRO("saveRISPOSTA"))
{
  $pidrebuilding_sportello_domanda=getPARAMETRO("_domanda");
  $pidrebuilding_sportello_domanda=$db->escape_text($pidrebuilding_sportello_domanda);
  $prisposta_descrizione=getPARAMETRO("risposta_descrizione");
  $prisposta_descrizione=$db->escape_text($prisposta_descrizione);

  $sportello->insertRISPOSTA($pidrebuilding_sportello_domanda,$prisposta_descrizione);

  $esperto_=
  $aEMAIL=array();
  $aEMAIL[0]=$email_esperto;
  $aEMAIL[1]="Nuova domanda per il progetto Rebuilding - Regone Marche";
  $aEMAIL[2]="Buongiorno,<br>La presente per comunicarLe che e' stata inoltrata una nuova domanda per lo sportello tematico di Sua competenza.";
  $aEMAIL[3]='';

  $mail_result=sendMAIL($aEMAIL);

  $flag_rispostasuccess=true;
}

$aDOMANDE=$sportello->getDOMANDE();

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
                <a href="sportelli" class="text-gray-700">
                  Sportelli telematici
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
              Domanda
            </h3>
      
            <div id="alert_config_autocertificazione"></div>

            <!-- Form -->
            <form id="autocertificazioneFORM" name="autocertificazioneFORM" method="post" role="form">
              <div class="row">
                <div class="col-12 col-md-12">
    
                    <div class="form-floating">
                      <textarea id="domanda_descrizione" name="domanda_descrizione" class="form-control form-control-flush" style="height:250px; font-size: 14px" placeholder="" disabled></textarea>
                      
                    </div> 
                </div>
              </div>

              <div class="row">
                <div class="col-12 col-md-12">
    
                    <div class="form-floating">
                      <textarea id="risposta_descrizione" name="risposta_descrizione" class="form-control form-control-flush" style="height:250px" placeholder="Inserire il testo della risposta" <?php echo $isENABLED; ?> required ></textarea>
                      <label for="risposta_descrizione">Risposta*</label>
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

                <!-- Heading -->
                <h2 class="fw-bold mb-0">
                   <?php echo $sportello->sportello_titolo;?>
                </h2>

                <!-- Text -->
                <p class="fs-lg text-gray-700 mb-0">
                   <?php echo $sportello->sportello_testo;?>
                </p>

              </div>
            </div> <!-- / .row -->

            <!-- Card -->

            <?php

              foreach ($aDOMANDE as $key => $aDATI) 
              {
                $idrebuilding_sportello_domanda=$aDATI['idrebuilding_sportello_domanda'];
                $domanda_data=$aDATI['domanda_data'];
                $domanda_orario=$aDATI['domanda_orario'];
                $domanda_operatore=$aDATI['domanda_operatore'];
                $domanda_testo=$aDATI['domanda_testo'];
                $aRISPOSTE=$sportello->getRISPOSTE($idrebuilding_sportello_domanda);
                $nRISPOSTE=count($aRISPOSTE);


                echo '<div class="accordion shadow-light-lg mb-5 mb-md-6" id="helpAccordion'.$idrebuilding_sportello_domanda.'">
                      <div class="accordion-item">

                        <!-- Button -->
                          <div class="accordion-button collapsed" role="button" data-bs-toggle="collapse" data-bs-target="#help'.$idrebuilding_sportello_domanda.'" aria-expanded="false" aria-controls="help'.$idrebuilding_sportello_domanda.'">

                            <!-- Title -->
                            <span class="me-4" id="help'.$idrebuilding_sportello_domanda.'Heading"><p><span class="breadcrumb-item text-info"><i><b>Domanda:</b></i></span></p>
                              '.stripslashes($domanda_testo).'
                            </span>

                            <!-- Metadata -->
                            <div class="text-muted ms-auto">

                              <!-- Badge -->
                              <span class="badge rounded-pill bg-success-soft ms-4">
                                <span class="h6 text-uppercase">
                                  '.$nRISPOSTE.' risposte
                                </span>
                              </span>

                            </div>

                          </div>

                          <!-- Collapse -->
                          <div class="accordion-collapse collapse" id="help'.$idrebuilding_sportello_domanda.'" aria-labelledby="help'.$idrebuilding_sportello_domanda.'Heading" data-bs-parent="#helpAccordion'.$idrebuilding_sportello_domanda.'">
                            <div class="accordion-body">';                              

                              foreach ($aRISPOSTE as $keyRISPOSTA => $aDATIRISPOSTA) 
                              {

                                echo '<div>
                                    <!-- Text -->
                                    <p class="text-gray-700"><p><span class="breadcrumb-item text-success"><i><b>Risposta:</b></i></span></p>
                                        '.stripslashes($aDATIRISPOSTA['risposta_testo']).'
                                    </p>

                                    <div class="d-flex align-items-center">

                                      <!-- Vote -->
                                      <div class="btn-group me-4">

                                        <!-- Thumb down -->
                                        <input type="radio" class="btn-check" name="help'.$idrebuilding_sportello_domanda.'Vote" id="help'.$idrebuilding_sportello_domanda.'Down" checked>
                                        <label class="btn btn-sm btn-white" for="help'.$idrebuilding_sportello_domanda.'Down">
                                          <i class="fe fe-thumbs-down"></i>
                                        </label>

                                        <!-- Thumb up -->
                                        <input type="radio" class="btn-check" name="help'.$idrebuilding_sportello_domanda.'Vote" id="help'.$idrebuilding_sportello_domanda.'Up">
                                        <label class="btn btn-sm btn-white" for="help'.$idrebuilding_sportello_domanda.'Up">
                                          <i class="fe fe-thumbs-up"></i>
                                        </label>

                                      </div>

                                      <!-- Text -->
                                      <span class="fs-sm text-muted">
                                        Hai risolto il tuo problema?
                                      </span>                                 

                                    </div>
                                  </div>  

                                  <hr class="bg-gray-300 my-6">
                                ';
                            }    
                  echo '<div>';


                  if($operatore_flagamministratore==1 || $operatore_flagamministratore==2)
                  {
                    echo '<div class="row">';
                    echo '<div class="d-flex align-items-center">';
                    echo '<button type="button" name="saveRISPOSTA" id="saveRISPOSTA" onclick="insertRISPOSTA(\''.$idrebuilding_sportello_domanda.'\',\''.base64_encode(stripslashes($domanda_testo)).'\')" value="true" class="btn btn-primary-soft lift btn-xs">Rispondi</button>';
                    echo '</div>';
                    echo '</div>';
                    
                  }

                  echo '</div>

                      </div>
                    </div> 
                  </div>';




              }

            ?>




          </div>
        </div> <!-- / .row -->
      </div> <!-- / .container -->
    </section> 
	  

    <!-- FORM -->
    <section class="pt-8 pt-md-1 pb-8 pb-md-14">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12 col-md-10 col-lg-8 text-center">

            <!-- Heading -->
            <h2 class="fw-bold">
              Fai la tua richiesta di consulenza
            </h2>

            <!-- Text -->
            <p class="fs-lg text-muted mb-7 mb-md-9">
              In questa sezione puoi fai una richiesta all'esperto ...
            </p>

          </div>
        </div> <!-- / .row -->
        <div class="row justify-content-center">
          <div class="col-12 col-md-12 col-lg-10">

            <!-- Form -->
            <form>

              <div class="row">
                <div class="col-12">
                  <div class="form-group mb-7 mb-md-9">

                    <!-- Input -->
                    <textarea class="form-control" id="testoDOMANDA" name="testoDOMANDA" rows="10" placeholder="Inserisci il testo della richiesta" required></textarea>

                  </div>
                </div>
              </div> <!-- / .row -->
              <div class="row justify-content-center">
                <div class="col-auto">

                  <!-- Submit -->
                  <button type="submit" name="saveDOMANDA" id="saveDOMANDA" value="true" class="btn btn-primary lift">
                    Invia la richiesta
                  </button>

                </div>
              </div> <!-- / .row -->
              <input type="hidden" name="_k" id="_k" value="<?php echo $pidrebuilding_sportello;?>">
            </form>

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
function insertRISPOSTA(kDOMANDA,testoDOMANDA)
{
  
  $("#_domanda").val(kDOMANDA)
  $("#domanda_descrizione").val(atob(testoDOMANDA))
  $("#risposta_descrizione").val('')
  $("#modalRISPOSTA").modal('show');
}
</script>

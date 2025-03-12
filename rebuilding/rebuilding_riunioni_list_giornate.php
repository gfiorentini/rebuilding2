<?php

require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");

$idoperatore=verificaUSER();

//$operatore=new DARAOperatore($idoperatore);
//$autorizzazioni=$operatore->getAUORIZZAZIONI();
function checkAndCreateDirectory($directory) {
  // Controlla se la directory esiste
  if (!is_dir($directory)) {
      // Se non esiste, prova a crearla
      if (mkdir($directory, 0777, true)) {
          // echo "Directory creata con successo: $directory\n";
      } else {
          // echo "Errore nella creazione della directory: $directory\n";
      }
  } else {
      //echo "La directory già esiste: $directory\n";
  }
}




//GF
$rm_profilo_utente = $db->select("select * from dara_operatore where iddara_operatore='$idoperatore' ") ;
$rm_operatore_codicefiscale=$rm_profilo_utente[0]["operatore_codicefiscale"];
//GF

$operatore_flagamministratore=$db->getVALUE("select operatore_flagamministratore from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagamministratore");

// Indica l'ID del gruppo di lavoro
$gidr = -1;

// Controlla se ci sono parametri nella query string
if (isset($_GET['idr']) ) {
  $gidr = intval( $_GET['idr'] );
} else {
  die("Errore: gruppi id gruppo tematico errato errati.");
}

$gcurrentgruppo =  $db->select("select * from rebuilding_gruppi_di_lavoro where idrebuilding_gld=$gidr ") ;

$gcurrentgruppo_auth = $db->select("select * from rebuilding_gruppi_di_lavoro_auth rgdla
	where  rgdla.fk_idrebuilding_gld=$gidr AND codice_fiscale='$rm_operatore_codicefiscale' ");

$gcurrentgruppo_auth_canView = $gcurrentgruppo_auth[0]["canView"];
$gcurrentgruppo_auth_canEdit = $gcurrentgruppo_auth[0]["canEdit"];

// TODO:   check if current user is authorized to accesso the gdl

// VERIFICA ED EVENTUALMENTE CREA LA CARTELLA PER LE RIUNIONI  DEL GRUPPO DI LAVORO
$riunioniRoot = $_SERVER["DOCUMENT_ROOT"] . '/riunioni/';
$riunioniRootGruppo = $riunioniRoot. $gcurrentgruppo[0]["gdl_path"] . '/';
checkAndCreateDirectory( $riunioniRootGruppo );



$gelenco_incontri = $db->select("select * from rebuilding_gruppi_di_lavoro_incontri where fk_idrebuilding_gld=$gidr ") ;


// $rm_gruppi_di_lavoro =  $db->select("select * from rebuilding_gruppi_di_lavoro gl
// where 
// gl.gdl_tipo='$gdlclass' AND
// exists ( 
// 	select * from rebuilding_gruppi_di_lavoro_auth rgdla
// 	where  rgdla.fk_idrebuilding_gld=gl.idrebuilding_gld AND codice_fiscale='$rm_operatore_codicefiscale' and (canView=1  or canEdit=1)
// )") ;


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
              <li  class="breadcrumb-item active" aria-current="page">
              <a href="toolkit_menu" class="text-gray-700"> Toolkit</a>
              </li>              
              <li class="breadcrumb-item active" aria-current="page">
                Riunioni - Giornate e materiale incontri effettuati. *** sistemare *** 
              </li>
            </ol>

          </div>
        </div> <!-- / .row -->
      </div> <!-- / .container -->
    </nav>
    
    <header>

    <?php if ($gcurrentgruppo_auth_canEdit == 1): ?>
            <!-- HTML code to display if condition is true -->
                <a href="rebuilding_riunione_create" class="btn btn-primary" >AGGIUNGI NUOVA RIUNIONE</a>
            <?php else: ?>
            <!-- HTML code to display if condition is false -->
            <a href="#" class="btn btn-primary" >aaa</a>
            <?php endif; ?>



    </header>
    
    <section class="pt-8 pt-md-11 pb-md-11">
      <div class="container">

        <div class="row">

        <div class="list-group">


        <?php foreach ($gelenco_incontri as $incontro) { ?>

          <!-- PER OGNI GRUPPO DI LAVORO DELLA CLASSE gdlclass  -->

          <a href="#" class="list-group-item list-group-item-action " aria-current="true">
            <div class="d-flex w-100 justify-content-between">
              <h4 class="mb-1"><?php echo $incontro["incontro_titolo"]  ; ?></h4>
              <small><?php echo ( new DateTime( $incontro["incontro_giorno"]))->format("d-m-Y"); ?></small>
            </div>
            <p class="mb-1"><?php echo $incontro["incontro_abstract"]  ; ?></p>
            <small>............</small>
          </a>          



        <?php } ?>


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

  </body>
</html>

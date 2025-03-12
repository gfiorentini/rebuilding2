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
    
    
    <section class="pt-8 pt-md-11 pb-md-11">
      <div class="container">

        <div class="row">


        <?php foreach ($gelenco_incontri as $incontro) { ?>

          <!-- PER OGNI GRUPPO DI LAVORO DELLA CLASSE gdlclass  -->


          <div class="col-12 col-md-6 col-lg-4 text-center aos-init aos-animate" data-aos="fade-up">
              <!-- Icon -->
              <div class="icon icon-lg mb-4">
                <img src="../librerie/assets/img/analytics.png">    <!-- icona del gruppo di lavoro --> 
              </div>

              <!-- Heading -->
              <h3 class="fw-bold">
                <a href="riunione_dettaglio?idr=<?php echo $incontro["idincontro"]; ?>" class="dropdown-item fw-bold text-decoration-none"><?php echo $incontro["incontro_titolo"] . '--' . $incontro["incontro_giorno"] ; ?></a>
              </h3>

              <!-- Text -->
              <p class="text-muted mb-8">
              <?php echo $incontro["incontro_abstract"]; ?>
              </p>

          </div>

        <?php } ?>


             

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

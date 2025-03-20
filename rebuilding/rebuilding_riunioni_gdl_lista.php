<?php

require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");

$idoperatore=verificaUSER();

//$operatore=new DARAOperatore($idoperatore);
//$autorizzazioni=$operatore->getAUORIZZAZIONI();

//GF
$rm_profilo_utente = $db->select("select * from dara_operatore where iddara_operatore='$idoperatore' ") ;
$rm_operatore_codicefiscale=$rm_profilo_utente[0]["operatore_codicefiscale"];
//GF

$operatore_flagamministratore=$db->getVALUE("select operatore_flagamministratore from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagamministratore");

// Indica la classe di gruppi di lavoro 
$gdlclass = "non specificata";

// Controlla se ci sono parametri nella query string
if (isset($_GET['gdlclass']) ) {
  $gdlclass = $_GET['gdlclass'];
  
} else {
  die("Errore: gruppi tematici errati.");
}

$rm_gruppi_di_lavoro=[];
// $rm_gruppi_di_lavoro =  $db->select("select * from rebuilding_gruppi_di_lavoro where gdl_tipo='$gdlclass' ") ;


// $rm_gruppi_di_lavoro =  $db->select("select * from rebuilding_gruppi_di_lavoro gl
// where 
// gl.gdl_tipo='$gdlclass' AND
// exists ( 
// 	select * from rebuilding_gruppi_di_lavoro_auth rgdla
// 	where  rgdla.fk_idrebuilding_gld=gl.idrebuilding_gld AND codice_fiscale='$rm_operatore_codicefiscale' and (canView=1  or canEdit=1)
// )") ;





$rm_gruppi_di_lavoro =  $db->select("select * , COALESCE(canView, 0 ) AS userCanView from rebuilding_gruppi_di_lavoro gl 
left join
( select * from rebuilding_gruppi_di_lavoro_auth rgdla  
	where  rgdla.codice_fiscale='$rm_operatore_codicefiscale' 
)  bb
on gl.idrebuilding_gld = bb.fk_idrebuilding_gld
where 
gl.gdl_tipo='$gdlclass' 
order by gl.idrebuilding_gld") ;


?>
<!doctype html>
<html lang="it">
  <head>

  	<?php echo getREBUILDINGHEAD(true); ?>

<style>

button:disabled, input:disabled, select:disabled, a:disabled {
  opacity: 0.5; /* Reduce opacity to indicate a isabled state */
  cursor: not-allowed; /* Change cursor to indicate non-interactivity */
  background-color: #f0f0f0; /* Light background for visual distinction */
  color: #a0a0a0; /* Muted text color */
}

.dropdown-item.disabled, .dropdown-item:disabled {
    color: #dadee5;
}


.text-muted {
    color: #dadee5 !important;
}


</style>

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
                <a href="rebuilding_riunioni_menu" class="text-gray-700"> Riunioni</a>
              </li>
            </ol>

          </div>
        </div> <!-- / .row -->
      </div> <!-- / .container -->
    </nav>
    
    
    <section class="pt-8 pt-md-11 pb-md-11">
      <div class="container">

        <div class="row">


        <?php foreach ($rm_gruppi_di_lavoro as $rm_gruppo) { ?>

          <!-- PER OGNI GRUPPO DI LAVORO DELLA CLASSE gdlclass  -->
          <?php if (!getPARAMETRO("_nuovo")):   ?>
            <?php endif; ?>

          <div class="col-12 col-md-6 col-lg-4 text-center aos-init aos-animate" data-aos="fade-up">
              <!-- Icon -->
              <div class="icon icon-lg mb-4">
                <img src="../librerie/assets/img/analytics.png">    <!-- icona del gruppo di lavoro --> 
              </div>

<?php if ($rm_gruppo["userCanView"] == 0):  ?>
              <!-- Heading -->
              <h3 class="fw-bold">
                <a href="rebuilding_riunioni_list_giornate?igl=<?php echo $rm_gruppo["idrebuilding_gld"]; ?>" class="disabled dropdown-item fw-bold text-decoration-none"><?php echo $rm_gruppo["gdl_titolo"]; ?></a>
              </h3>
              <p class="text-muted mb-8 fs-6">
              <?php echo $rm_gruppo["gdl_testo"]; ?>
              </p>
<?php endif; ?>

<?php if ($rm_gruppo["userCanView"] == 1):  ?>
              <!-- Heading -->
              <h3 class="fw-bold">
                <a href="rebuilding_riunioni_list_giornate?igl=<?php echo $rm_gruppo["idrebuilding_gld"]; ?>" class="dropdown-item fw-bold text-decoration-none"><?php echo $rm_gruppo["gdl_titolo"]; ?></a>
              </h3>
              <!-- Text -->
              <p class="mb-8 fs-6">
              <?php echo $rm_gruppo["gdl_testo"]; ?>
              </p>
<?php endif; ?>

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

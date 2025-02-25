<?php

require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");

$idoperatore=verificaUSER();

//$operatore=new DARAOperatore($idoperatore);
//$autorizzazioni=$operatore->getAUORIZZAZIONI();
$operatore_flagamministratore=$db->getVALUE("select operatore_flagamministratore from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagamministratore");

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
              <li class="breadcrumb-item active" aria-current="page">
                Toolkit
              </li>
            </ol>

          </div>
        </div> <!-- / .row -->
      </div> <!-- / .container -->
    </nav>
    
    
    <section class="pt-8 pt-md-11 pb-md-11">
      <div class="container">

        <div class="row">

          <div class="col-12 col-md-6 col-lg-4 text-center" data-aos="fade-up">
              <!-- Icon -->
              <div class="icon icon-lg mb-3">
                <img src="../librerie/assets/img/analytics.png">  
              </div>

              <!-- Heading -->
              <h3 class="fw-bold">
                <a href="toolkit" class="dropdown-item fw-bold text-decoration-none">Flussi di finanziamento</a>
              </h3>

              <!-- Text -->
              <p class="text-muted mb-8 mb-lg-0">
                Modelli e documentazione dei flussi di finanziamento
              </p>

          </div>

          <div class="col-12 col-md-6 col-lg-4 text-center" data-aos="fade-up">
              <!-- Icon -->
              <div class="icon icon-lg mb-3">
                <img src="../librerie/assets/img/biblioteca.png" style="width: 20%">  
              </div>

              <!-- Heading -->
              <h3 class="fw-bold">
                <a href="biblioteca" class="dropdown-item fw-bold text-decoration-none">Biblioteca</a>
              </h3>

              <!-- Text -->
              <p class="text-muted mb-8 mb-lg-0">
                Modelli e documentazione dei flussi di finanziamento
              </p>

          </div>

          <div class="col-12 col-md-6 col-lg-4 text-center" data-aos="fade-up">
              <!-- Icon -->
              <div class="icon icon-lg mb-3">
                <img src="../librerie/assets/img/biblioteca.png" style="width: 20%">  
              </div>

              <!-- Heading -->
              <h3 class="fw-bold">
                <a href="riunioni" class="dropdown-item fw-bold text-decoration-none">Riunioni</a>
              </h3>

              <!-- Text -->
              <p class="text-muted mb-8 mb-lg-0">
                Riunioni dei Gruppi di Lavoro
              </p>

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

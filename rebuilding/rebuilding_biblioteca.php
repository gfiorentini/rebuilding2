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
              <li class="breadcrumb-item" aria-current="page">
                <a href="toolkit_menu" class="text-gray-700">
                  Toolkit
                </a>
              </li>
              <li class="breadcrumb-item active" aria-current="page">
                Biblioteca
              </li>
            </ol>

          </div>
        </div> <!-- / .row -->
      </div> <!-- / .container -->
    </nav>
    
    
    <section class="pt-8 pt-md-11 pb-md-11">
      <div class="container">

        <div class="row" style="padding-top: 50px">
       
          <div class="col-12 col-md-6 col-lg-4 text-center" data-aos="fade-up" >
              <!-- Icon -->
              <div class="icon icon-lg mb-3">
                <img src="../librerie/assets/img/folders.png" style="width: 20%">  
              </div>

              <!-- Heading -->
              <h3 class="fw-bold">
                <a href="avvisi?_k=8" class="dropdown-item fw-bold text-decoration-none">Avvisi ex D.Lgs. D.Lgs. 36/23e s.m.i.</a>
              </h3>

              <!-- Text -->
              <p class="text-muted mb-8 mb-lg-0">
               
              </p>

          </div>  
         
          <div class="col-12 col-md-6 col-lg-4 text-center" data-aos="fade-up" >
              <!-- Icon -->
              <div class="icon icon-lg mb-3">
                <img src="../librerie/assets/img/folders.png" style="width: 20%">  
              </div>

              <!-- Heading -->
              <h3 class="fw-bold">
                <a href="avvisi?_k=10" class="dropdown-item fw-bold text-decoration-none">Format Avviso Pubblico<br> Co-progettazione</a>
              </h3>

              <!-- Text -->
              <p class="text-muted mb-8 mb-lg-0">
               
              </p>

          </div>  
          <div class="col-12 col-md-6 col-lg-4 text-center" data-aos="fade-up" >
              <!-- Icon -->
              <div class="icon icon-lg mb-3">
                <img src="../librerie/assets/img/folders.png" style="width: 20%">  
              </div>

              <!-- Heading -->
              <h3 class="fw-bold">
                <a href="avvisi?_k=11" class="dropdown-item fw-bold text-decoration-none">Format accordo ATS PNRR</a>
              </h3>

              <!-- Text -->
              <p class="text-muted mb-8 mb-lg-0">
               
              </p>

          </div>  
         
        </div>
        <div class="row" style="padding-top: 50px">
        <div class="col-12 col-md-6 col-lg-4 text-center" data-aos="fade-up" >
              <!-- Icon -->
              <div class="icon icon-lg mb-3">
                <img src="../librerie/assets/img/folders.png" style="width: 20%">  
              </div>

              <!-- Heading -->
              <h3 class="fw-bold">
                <a href="avvisi?_k=9" class="dropdown-item fw-bold text-decoration-none">Format PNRR Convenzione ATS <br>per Costituzione Consorzio</a>
              </h3>

              <!-- Text -->
              <p class="text-muted mb-8 mb-lg-0">
               
              </p>

          </div>  

          

          <div class="col-12 col-md-6 col-lg-4 text-center" data-aos="fade-up" >
              <!-- Icon -->
              <div class="icon icon-lg mb-3">
                <img src="../librerie/assets/img/folders.png" style="width: 20%">  
              </div>

              <!-- Heading -->
              <h3 class="fw-bold">
                <a href="avvisi?_k=12" class="dropdown-item fw-bold text-decoration-none">Bando Concorsi per procedure<br> di assunzione</a>
              </h3>

              <!-- Text -->
              <p class="text-muted mb-8 mb-lg-0">
               
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

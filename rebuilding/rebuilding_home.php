<?php

require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");

global $db;

$idoperatore=verificaUSER();
// print_r_formatted($idoperatore);

//$operatore=new DARAOperatore($idoperatore);
//$autorizzazioni=$operatore->getAUORIZZAZIONI();

//Verifico il profilo

$operatore_flagamministratore=$db->getVALUE("select operatore_flagamministratore from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagamministratore");



?>
<!doctype html>
<html lang="it">
  <head>

  	<?php echo getREBUILDINGHEAD(true); ?>

  </head>
  <body>

  	<?php echo getREBUILDINGNAVBAR(); ?>
    
    <div class="container">
	    <section class="pt-8 pt-md-11 pb-md-11">
	      <div class="container">
	        <div class="row">


	        	
		          <div class="col-12 col-md-6 col-lg-4 text-center" data-aos="fade-up">
		            <!-- Icon -->
		            <div class="icon icon-lg mb-3">
		            	<img src="../librerie/assets/img/computer.png">  
		            </div>

		            <!-- Heading -->
		            <h3 class="fw-bold">
		              <a href="sportelli" class="dropdown-item fw-bold text-decoration-none">Sportelli tematici</a>
		            </h3>

		            <!-- Text -->
		            <p class="text-muted mb-8 mb-lg-0">
		             	Sportello per ciascuna singola tematica
		            </p>

			      </div>

		          <div class="col-12 col-md-6 col-lg-4 text-center" data-aos="fade-up">
		            <!-- Icon -->
		            <div class="icon icon-lg mb-3">
		            	<img src="../librerie/assets/img/online-course.png">  
		            </div>

		            <!-- Heading -->
		            <h3 class="fw-bold">
		              <a href="corsi" class="dropdown-item fw-bold text-decoration-none">Corso di formazione</a>
		            </h3>

		            <!-- Text -->
		            <p class="text-muted mb-8 mb-lg-0">
		              Il corso prevede lezioni in FAD asincrone
		            </p>

			      </div>			      
			      
		          <div class="col-12 col-md-6 col-lg-4 text-center" data-aos="fade-up">

		            <!-- Icon -->
		            <div class="icon icon-lg mb-4">
		              <img src="../librerie/assets/img/package-box.png">
		          	</div>

		            <!-- Heading -->
		            <h3 class="fw-bold">
		              <a href="toolkit_menu" class="dropdown-item fw-bold text-decoration-none">Tool kit</a>
		            </h3>

		            <!-- Text -->
		            <p class="text-muted mb-8">
		              Strumenti e materiali normativo e di letteratura
		            </p>


		          </div>
	                    


		          <div class="col-12 col-md-6 col-lg-4 text-center" data-aos="fade-up" <?php if($operatore_flagamministratore!='1') echo 'style="display:none"'; ?>>

		            <!-- Icon -->
		            <div class="icon icon-lg mb-4">
		              <img src="../librerie/assets/img/security.png">
		            </div>

		            <!-- Heading -->
		            <h3 class="fw-bold">
		              <a href="autorizzazione-operatori" class="dropdown-item fw-bold text-decoration-none">Autorizzazioni</a>
		            </h3>

		            <!-- Text -->
		            <p class="text-muted mb-0">
		              Gestione degli operatori
		            </p>

		          </div>

		          <div class="col-12 col-md-6 col-lg-4 text-center" data-aos="fade-up" <?php if($operatore_flagamministratore!='1') echo 'style="display:none"'; ?>>

		            <!-- Icon -->
		            <div class="icon icon-lg mb-4">
		              <img src="../librerie/assets/img/security.png">
		            </div>

		            <!-- Heading -->
		            <h3 class="fw-bold">
		              <a href="ats" class="dropdown-item fw-bold text-decoration-none">Ambiti sociali</a>
		            </h3>

		            <!-- Text -->
		            <p class="text-muted mb-0">
		              Gestione email degli ATS
		            </p>

		          </div>		          

			
	        </div> <!-- / .row -->
	      </div> <!-- / .container -->
	    </section>
	</div>    

	<div style="margin-top: 15%;" ></div>

	<?php echo getWFOOTERNOLOGIN(); ?>

    <!-- JAVASCRIPT -->
    <!-- Map JS -->
    <script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
    
    <!-- Vendor JS -->
    <script src="../librerie/assets/js/vendor.bundle.js"></script>
    
    <!-- Theme JS -->
    <script src="../librerie/assets/js/theme.bundle.js"></script>

  </body>
</html>

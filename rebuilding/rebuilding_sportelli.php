<?php

require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");

$idoperatore=verificaUSER();

//$operatore=new DARAOperatore($idoperatore);
//$autorizzazioni=$operatore->getAUORIZZAZIONI();


$sportello1=new rebuildingSPORTELLO(1);
$sportello2=new rebuildingSPORTELLO(2);
$sportello3=new rebuildingSPORTELLO(3);
$sportello4=new rebuildingSPORTELLO(4);
$sportello5=new rebuildingSPORTELLO(5);
$sportello6=new rebuildingSPORTELLO(6);
$sportello7=new rebuildingSPORTELLO(7);
$sportello13=new rebuildingSPORTELLO(13);

$nDOMANDA1=count($sportello1->getDOMANDE());
$nDOMANDA2=count($sportello2->getDOMANDE());
$nDOMANDA3=count($sportello3->getDOMANDE());
$nDOMANDA4=count($sportello4->getDOMANDE());
$nDOMANDA5=count($sportello5->getDOMANDE());
$nDOMANDA6=count($sportello6->getDOMANDE());
$nDOMANDA13=count($sportello13->getDOMANDE());

$nRISPOSTE1=count($sportello1->getRISPOSTE());
$nRISPOSTE2=count($sportello2->getRISPOSTE());
$nRISPOSTE3=count($sportello3->getRISPOSTE());
$nRISPOSTE4=count($sportello4->getRISPOSTE());
$nRISPOSTE5=count($sportello5->getRISPOSTE());
$nRISPOSTE6=count($sportello6->getRISPOSTE());
$nRISPOSTE7=count($sportello7->getRISPOSTE());
$nRISPOSTE13=count($sportello13->getRISPOSTE());


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
                Sportelli tematici
              </li>
            </ol>

          </div>
        </div> <!-- / .row -->
      </div> <!-- / .container -->
    </nav>
    
    
    <section>
      <div class="container">

        <div class="row" style="padding-top: 30px">
          <div class="col-12 col-md-6 col-lg-4">

            <!-- Card -->
            <div class="card card-border border-warning shadow-lg mb-6 mb-md-8 lift lift-lg">
              <div class="card-body text-center">

                <!-- Icon -->
                <div class="icon-circle bg-warning text-white mb-5">
                  <i class="fe fe-users"></i>
                </div>

                <!-- Heading -->
                <h4 class="fw-bold">
                  <a href="sportello?_k=3">Le Fonti di Finanziamento</a>
                </h4>

                <!-- Text -->
                <p class="text-gray-700 mb-5">
                  Programmazione delle fonti di finanziamento comunitarie, nazionali e regionali.<br>&nbsp;
                </p>

                <!-- Badge -->
                <span class="badge rounded-pill bg-dark-soft">
                  <span class="h6 text-uppercase">
                    <?php echo $nDOMANDA3;?> Domande - <?php echo $nRISPOSTE3;?> Risposte 
                  </span>
                </span>

              </div>
            </div>

          </div>    
          
          <div class="col-12 col-md-6 col-lg-4">

            <!-- Card -->
            <div class="card card-border border-dark shadow-lg mb-6 mb-md-8 mb-lg-0 lift lift-lg">
              <div class="card-body text-center">

                <!-- Icon -->
                <div class="icon-circle bg-dark text-white mb-5">
                  <i class="fe fe-users"></i>
                </div>

                <!-- Heading -->
                <h4 class="fw-bold">
                  <a href="sportello?_k=4">Bilancio e contabilità</a>
                </h4>

                <!-- Text -->
                <p class="text-gray-700 mb-5">
                  Bilanci annuali e pluriennali, accertamento e impegno fonti di finanziamento. Piano dei conti
                </p>

                <!-- Badge -->
                <span class="badge rounded-pill bg-dark-soft">
                  <span class="h6 text-uppercase">
                    <?php echo $nDOMANDA4;?> Domande - <?php echo $nRISPOSTE4;?> Risposte 
                  </span>
                </span>

              </div>
            </div>

          </div>

          <div class="col-12 col-md-6 col-lg-4">

            <!-- Card -->
            <div class="card card-border border-primary shadow-lg mb-6 mb-md-8 lift lift-lg">
              <div class="card-body text-center">

                <!-- Icon -->
                <div class="icon-circle bg-primary text-white mb-5">
                  <i class="fe fe-users"></i>
                </div>

                <!-- Heading -->
                <h4 class="fw-bold">
                  <a href="sportello?_k=1">Procedure di affidamento</a>
                </h4>

                <!-- Text -->
                <p class="text-gray-700 mb-5">
                  Gare, accreditamenti, coprogettazioni.<br>&nbsp;<br>&nbsp;
                </p>

                <!-- Badge -->
                <span class="badge rounded-pill bg-dark-soft">
                  <span class="h6 text-uppercase">
                    <?php echo $nDOMANDA1;?> Domande - <?php echo $nRISPOSTE1;?> Risposte 
                  </span>
                </span>

              </div>
            </div>

          </div>

          <div class="col-12 col-md-6 col-lg-4">

            <!-- Card -->
            <div class="card card-border border-primary-desat shadow-lg mb-6 mb-md-0 lift lift-lg">
              <div class="card-body text-center">

                <!-- Icon -->
                <div class="icon-circle bg-primary-desat text-white mb-5">
                  <i class="fe fe-users"></i>
                </div>

                <!-- Heading -->
                <h4 class="fw-bold">
                  <a href="sportello?_k=5">Il personale della PA</a>
                </h4>

                <!-- Text -->
                <p class="text-gray-700 mb-5">
                  Concorsi, procedure selettive, limiti di spesa e assunzionali<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;
                </p>

                <!-- Badge -->
                <span class="badge rounded-pill bg-dark-soft">
                  <span class="h6 text-uppercase">
                    <?php echo $nDOMANDA5;?> Domande - <?php echo $nRISPOSTE5;?> Risposte 
                  </span>
                </span>

              </div>
            </div>

          </div>

          <div class="col-12 col-md-6 col-lg-4">

            <!-- Card -->
            <div class="card card-border border-success shadow-lg mb-6 mb-md-8 lift lift-lg">
              <div class="card-body text-center">

                <!-- Icon -->
                <div class="icon-circle bg-success text-white mb-5">
                  <i class="fe fe-users"></i>
                </div>

                <!-- Heading -->
                <h4 class="fw-bold">
                  <a href="sportello?_k=2">Monitoraggio e rendicontazione fonti di finanziamento</a>
                </h4>

                <!-- Text -->
                <p class="text-gray-700 mb-5">
                  Procedure amministrative e gestionali per il monitoraggio e la rendicontazione delle fonti di finanziamento.
                </p>

                <!-- Badge -->
                <span class="badge rounded-pill bg-dark-soft">
                  <span class="h6 text-uppercase">
                    <?php echo $nDOMANDA2;?> Domande - <?php echo $nRISPOSTE2;?> Risposte 
                  </span>
                </span>

              </div>
            </div>

          </div>



          <div class="col-12 col-md-6 col-lg-4">

            <!-- Card -->
            <div class="card card-border border-danger shadow-lg lift lift-lg">
              <div class="card-body text-center">

                <!-- Icon -->
                <div class="icon-circle bg-danger text-white mb-5">
                  <i class="fe fe-users"></i>
                </div>

                <!-- Heading -->
                <h4 class="fw-bold">
                  <a href="sportello?_k=6">Piattaforme</a>
                </h4>

                <!-- Text -->
                <p class="text-gray-700 mb-5">
                  Supporto per la gestione delle piattaforme ministeriali e regionali (SIUSS, SIOSS, Gepi, Multifondo, ecc...)<br>&nbsp;<br>&nbsp;
                </p>

                <!-- Badge -->
                <span class="badge rounded-pill bg-dark-soft">
                  <span class="h6 text-uppercase">
                    <?php echo $nDOMANDA6;?> Domande - <?php echo $nRISPOSTE6;?> Risposte 
                  </span>
                </span>

              </div>
            </div>

          </div>

          <div class="col-12 col-md-6 col-lg-4">

            <!-- Card -->
            <div class="card card-border border-warning shadow-lg mb-6 mb-md-8 lift lift-lg">
              <div class="card-body text-center">

                <!-- Icon -->
                <div class="icon-circle bg-warning text-white mb-5">
                  <i class="fe fe-users"></i>
                </div>

                <!-- Heading -->
                <h4 class="fw-bold">
                  <a href="sportello?_k=7">Flussi di Finanziamento</a>
                </h4>

                <!-- Text -->
                <p class="text-gray-700 mb-5">
                  <br><br><br><br><br>
                </p>

                <!-- Badge -->
                <span class="badge rounded-pill bg-dark-soft">
                  <span class="h6 text-uppercase">
                    <?php echo $nDOMANDA7;?> Domande - <?php echo $nRISPOSTE7;?> Risposte 
                  </span>
                </span>

              </div>
            </div>

          </div>    

          <!-- area sportello strutture sociali start   id_sportello==13 -->
          <div class="col-12 col-md-6 col-lg-4">

            <!-- Card -->
            <div class="card card-border border-success shadow-lg mb-6 mb-md-8 lift lift-lg">
              <div class="card-body text-center">

                <!-- Icon -->
                <div class="icon-circle bg-success text-white mb-5">
                  <i class="fe fe-users"></i>
                </div>

                <!-- Heading -->
                <h4 class="fw-bold">  
                  <a href="sportello?_k=13">Autorizzazzioni Strutture Sociali</a>
                </h4>

                <!-- Text -->
                <p class="text-gray-700 mb-5">
                  <br><br><br><br><br>
                </p>

                <!-- Badge -->
                <span class="badge rounded-pill bg-dark-soft">
                  <span class="h6 text-uppercase">
                    <?php echo $nDOMANDA13;?> Domande - <?php echo $nRISPOSTE13;?> Risposte 
                  </span>
                </span>

              </div>
            </div>

          </div>    

          <!-- area sportello strutture sociali end -->




        </div> <!-- / .row -->
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

<?php
include_once("../librerie/constants.php");

function getPARAMETRO($paramatroNAME)
{
	$result="";
	if (!empty($paramatroNAME))
	{
	  if(isset($_POST[$paramatroNAME]))
	    $result = $_POST[$paramatroNAME];
	  else if(isset($_GET[$paramatroNAME]))
	    $result = $_GET[$paramatroNAME];
	}

  $result = @addslashes($result);

	return $result;
}

function dataodierna()
{
	return date("Y-m-d");
}

function oraodierna()
{
	return date("H:i:s");
}

function generaCHIAVE($username,$ipaddress,$user)
{
	include "../librerie/class.rsa.php";

	$crypt = new RsaCrypt;	

	//$crypt->genKeys(2048);

	$crypt->setPublicKey('../librerie/publicDARA.pem');
	$crypt->setPrivateKey('../librerie/privateDARA.pem');
	$dataRSA = $crypt->encrypt("DARAAccesso".date("YmdHis")."|".$user."#".$ipaddress."@".$username."+393400008848");	
	$dataRSA=base64_encode($dataRSA);

	return $dataRSA;
}

function generaPASSWORD($pwdlen=8,$pwdupper=true)
{
  $password = "";
  /*
  $caratteri_disponibili ="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  $lunghezza=3;
  for($i=0; $i<$lunghezza; $i++)
    $password.=substr($caratteri_disponibili,rand(0,strlen($caratteri_disponibili)-1),1);
  */
  $lenpwd1=floor($pwdlen/2);
  $lenpwd2=$pwdlen-$lenpwd1;

  $caratteri_disponibili ="abcdefghijklmnopqrstuvwxyz";
  $lunghezza=$lenpwd1;
  for($i=0; $i<$lunghezza; $i++)
    $password.=substr($caratteri_disponibili,rand(0,strlen($caratteri_disponibili)-1),1);

  if ($pwdupper)
  {
    $password=strtoupper(substr($password,0,1)).substr($password,1);
  }

  $caratteri_disponibili ="1234567890!";
  $lunghezza=$lenpwd2;
  for($i=0; $i<$lunghezza; $i++)
    $password.=substr($caratteri_disponibili,rand(0,strlen($caratteri_disponibili)-1),1);

  //$password=criptaPASSWORD($password);
  return $password;
}

function criptaPASSWORD($password)
{
  include "../librerie/class.rsa.php";

  $crypt = new RsaCrypt;  

  //$crypt->genKeys(2048);

  $crypt->setPublicKey('../librerie/publicDARA.pem');
  $crypt->setPrivateKey('../librerie/privateDARA.pem');
  $dataRSA = $crypt->encrypt("DARA@cc3ss0"."|utente!".$password."#SPORT2050@".$password."+393400008848");  
  $dataRSA=base64_encode($dataRSA);

  return $dataRSA;
}

function insertLOG($chiave,$ipaddress,$user,$log_attivita=null)
{
	global $db;

	$data=dataodierna();
	$orario=oraodierna();

	$log_pagina = basename($_SERVER['PHP_SELF']);

	$sSQL="INSERT INTO dara_logs (logs_ip,logs_data,logs_ora,logs_chiave,iddara_operatore,logs_pagina,logs_attivita) values('$ipaddress','$data','$orario','$chiave','$user','$log_pagina','$log_attivita')";
 
	//$sSQL=$db->escape_text($sSQL);
	$db->query($sSQL);
}


function insertSESSION($chiave)
{
	global $db;

	$data=dataodierna();
	$orario=oraodierna();

	$timestamp_expiration = time() + 6 * 3600;
	$timestamp_expiration = date('Y-m-d H:i:s',$timestamp_expiration);
  //$timestamp_expiration='';

	$sSQL="INSERT INTO dara_sessions (sessions_chiave,sessions_data,sessions_ora,sessions_expire) values('$chiave','$data','$orario','$timestamp_expiration')";
	$db->query($sSQL);

	session_start();
	$_SESSION["dara_userkey"]=$chiave;
}


function dataitaliana($data)
{
  $nuovadata="";
	if ($data!="")
	{
		$separatoreOut="/";
		list($anno,$mese,$giorno)=explode("-",$data);
		$nuovadata=$giorno.$separatoreOut.$mese.$separatoreOut.$anno;
	}

	return $nuovadata;
}

function datausa($data)
{
    if ($data!="")
    {
        $separatoreOut="-";
        list($giorno,$mese,$anno)=explode("/",$data);
        $nuovadata=$anno.$separatoreOut.$mese.$separatoreOut.$giorno;
    }

    return $nuovadata;
}


function getDARAHEAD()
{
    $head='<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
	
    <!-- Favicon -->
    <link rel="shortcut icon" href="../librerie/assets/favicon/favicon.ico" type="image/x-icon" />
    
    <!-- Map CSS -->
    <link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.css" />
    
    <!-- Libs CSS -->
    <link rel="stylesheet" href="../librerie/assets/css/libs.bundle.css" />
    
    <!-- Theme CSS -->
    <link rel="stylesheet" href="../librerie/assets/css/theme.bundle.css" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script src="../librerie/lib.dara.js?v='.JS_LIB_VERSION.'"></script>

    <script src="../librerie/dependsOn-1.0.0.min.js"></script>
    
    <link rel="stylesheet" href="../librerie/dara.css" />

    <!-- https://icon-sets.iconify.design/ -->
    <script src="https://code.iconify.design/iconify-icon/1.0.0-beta.3/iconify-icon.min.js"></script>
    
    <!-- Title -->
    <title>Sport</title>';

    return $head;
}

function getDARANAVBAR($flagfixed=false)
{
	global $db;

	$idoperatore=verificaUSER();
	$sSQL="select concat_ws(' ',operatore_cognome,operatore_nome) as nominativo from dara_operatore where iddara_operatore='$idoperatore'";
	$operatore=strtoupper($db->getVALUE($sSQL,'nominativo'));

	$fixed="";
	if ($flagfixed)
		$fixed="fixed-top";

	$navbar='<!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-light '.$fixed.' bg-dark border-bottom">        
      <div class="container">
      <!--div class="container-fluid"-->
    
        <!-- Brand -->
        <a class="navbar-brand" href="home">
          <img src="../librerie/assets/img/brand.svg" class="navbar-brand-img" alt="...">
        </a>
    
        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
    
        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="navbarCollapse">
    
          <!-- Toggler -->
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fe fe-x"></i>
          </button>

          <!-- Navigation -->          
          <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" style="color:white" id="navbarPages" data-bs-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                Domande
              </a>
              <div class="dropdown-menu dropdown-menu-md" aria-labelledby="navbarDocumentation">
                <div class="list-group list-group-flush">
                  <a class="list-group-item" href="domanda">
    
                    <!-- Icon -->
                    <div class="icon icon-sm text-primary">
                      <span class="fe fe-file"></span>
                  	</div>
    
                    <!-- Content -->
                    <div class="ms-4">
    
                      <!-- Heading -->
                      <h6 class="fw-bold text-uppercase text-primary mb-0">
                        Nuova domanda
                      </h6>
    
    
                    </div>
    
                  </a>                	
                  <a class="list-group-item" href="domande">
    
                    <!-- Icon -->
                    <div class="icon icon-sm text-primary">
                      <span class="fe fe-file"></span>
                  	</div>
    
                    <!-- Content -->
                    <div class="ms-4">
    
                      <!-- Heading -->
                      <h6 class="fw-bold text-uppercase text-primary mb-0">
                        Elenco domande
                      </h6>
    
    
                    </div>
    
                  </a>                 
                </div>
              </div>
            </li>
            <li class="nav-item dropdown">

              <a class="nav-link" style="color:white" id="navbarDocumentation" href="presenze" aria-haspopup="true" aria-expanded="false">
                Presenze
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link" style="color:white" id="navbarDocumentation" href="pagamenti" aria-haspopup="true" aria-expanded="false">
                Pagamenti
              </a>
            </li>  
            <li class="nav-item dropdown">
              <a class="nav-link" style="color:white" id="navbarDocumentation" href="../sport/dara_magazzino_articoli.php" aria-haspopup="true" aria-expanded="false">
                Magazzino
              </a>
            </li>                        
          </ul>
    
          <!-- Button -->
            <div class="dropdown">
              <button class="btn btn-xs btn-primary dropdown-toggle" type="button" id="dropdownUSER" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fe fe-user"></i> '.$operatore.'
              </button>
              <div class="dropdown-menu dropdown-menu-xs" aria-labelledby="dropdownUSER">
                <!--a class="dropdown-item" href="#!">Cambia password</a-->
                <a class="dropdown-item" onclick="location.href = \'login\'">Esci&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
              </div>
            </div>
        </div>
    
      </div>
    </nav>';

    return $navbar;

}

function getDARANAVBARNOLOGIN()
{

    $navbar='<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
      <div class="container">
    
        <!-- Brand -->
        <a class="navbar-brand" href="./dara_index.php">
          <img src="../librerie/assets/img/brand.svg" class="navbar-brand-img" alt="...">
        </a>
    
        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
    
        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="navbarCollapse">
    
          <!-- Toggler -->
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fe fe-x"></i>
          </button>
    
          <!-- Navigation -->
          <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" id="navbarHOME"  href="./dara_index.php" >
                Home page
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" id="navbarSOCIETA" data-bs-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                Società
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" id="navbarPRODOTTI" data-bs-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                Prodotti
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" id="navbarSERVIZI" data-bs-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                Servizi
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" id="navbarCONTATTI" data-bs-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                Contatti
              </a>
            </li>            

          </ul>
    
          <!-- Button -->
          <a class="navbar-btn btn btn-sm btn-primary lift ms-auto" href="./dara_login.php" >
            Accedi al sistema
          </a>
    
        </div>
    
      </div>
    </nav> ';

    return $navbar;
}

function getDARAFOOTERNOLOGIN()
{

  $footer='<footer class="py-8 py-md-11 bg-dark border-top border-gray-800-50">
      <div class="container">
        <div class="row">
          <div class="col-12 col-md-4 col-lg-3">
    
            <!-- Brand -->
            <img src="../librerie/assets/img/brand.svg" alt="..." class="footer-brand img-fluid mb-2">
    
            <!-- Text -->
            <p class="text-gray-700 mb-2">
              A better way to build.
            </p>
    
            <!-- Social -->
            <ul class="list-unstyled list-inline list-social mb-6 mb-md-0">
              <li class="list-inline-item list-social-item me-3">
                <a href="#!" class="text-decoration-none">
                  <img src="../librerie/assets/img/icons/social/instagram.svg" class="list-social-icon" alt="...">
                </a>
              </li>
              <li class="list-inline-item list-social-item me-3">
                <a href="#!" class="text-decoration-none">
                  <img src="../librerie/assets/img/icons/social/facebook.svg" class="list-social-icon" alt="...">
                </a>
              </li>
              <li class="list-inline-item list-social-item me-3">
                <a href="#!" class="text-decoration-none">
                  <img src="../librerie/assets/img/icons/social/twitter.svg" class="list-social-icon" alt="...">
                </a>
              </li>
              <li class="list-inline-item list-social-item">
                <a href="#!" class="text-decoration-none">
                  <img src="../librerie/assets/img/icons/social/pinterest.svg" class="list-social-icon" alt="...">
                </a>
              </li>
            </ul>
    
          </div>
          <div class="col-6 col-md-4 col-lg-2">
    
            <!-- Heading -->
            <h6 class="fw-bold text-uppercase text-gray-700">
              Prodotti
            </h6>
    

    
          </div>
          <div class="col-6 col-md-4 col-lg-2">
    
            <!-- Heading -->
            <h6 class="fw-bold text-uppercase text-gray-700">
              Servizi
            </h6>
    
            <!-- List -->

    
          </div>
          <div class="col-6 col-md-4 offset-md-4 col-lg-2 offset-lg-0">
    
            <!-- Heading -->
            <h6 class="fw-bold text-uppercase text-gray-700">
              Contatti
            </h6>
    
    
          </div>
          <div class="col-6 col-md-4 col-lg-2">
    
            <!-- Heading -->
            <h6 class="fw-bold text-uppercase text-gray-700">
              Legal
            </h6>
    
            <!-- List -->
            <ul class="list-unstyled text-muted mb-0">
              <li class="mb-3" style="font-size: 12px">
                <a href="#!" class="text-reset">
                  Informativa sulla privacy
                </a>
              </li>

            </ul>
    
          </div>
        </div> <!-- / .row -->
      </div> <!-- / .container -->
    </footer>';

    return $footer;
}

function getWHEAD()
{
    $head='<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
  
    <!-- Favicon -->
    <link rel="shortcut icon" href="../librerie/assets/favicon/favicon.ico" type="image/x-icon" />
    
    <!-- Map CSS -->
    <link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.css" />
    
    <!-- Libs CSS -->
    <link rel="stylesheet" href="../librerie/assets/css/libs.bundle.css" />
    
    <!-- Theme CSS -->
    <link rel="stylesheet" href="../librerie/assets/css/theme.bundle.css" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <script src="../librerie/lib.dara.js"></script>

    <!-- Title -->
    <title>Dabliu</title>';

    return $head;
}

function getWNAVBAR($flagfixed=false)
{
  global $db;

  $idoperatore=verificaUSER();
  $sSQL="select concat_ws(' ',operatore_cognome,operatore_nome) as nominativo from dara_operatore where iddara_operatore='$idoperatore'";
  $operatore=strtoupper($db->getVALUE($sSQL,'nominativo'));

  $fixed="";
  if ($flagfixed)
    $fixed="fixed-top";

  $navbar='<!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-light '.$fixed.' bg-dark border-bottom">        
      <div class="container">
      <!--div class="container-fluid"-->
    
        <!-- Brand -->
        <a class="navbar-brand" href="home">
          <img src="../librerie/assets/img/brand.svg" class="navbar-brand-img" alt="...">
        </a>
    
        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
    
        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="navbarCollapse">
    
          <!-- Toggler -->
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fe fe-x"></i>
          </button>

          <!-- Navigation -->          
          <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" style="color:white" id="navbarPages" data-bs-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                News
              </a>
              <div class="dropdown-menu dropdown-menu-md" aria-labelledby="navbarDocumentation">
                <div class="list-group list-group-flush">
                  <a class="list-group-item" href="domanda">
    
                    <!-- Icon -->
                    <div class="icon icon-sm text-primary">
                      <span class="fe fe-file"></span>
                    </div>
    
                    <!-- Content -->
                    <div class="ms-4">
    
                      <!-- Heading -->
                      <h6 class="fw-bold text-uppercase text-primary mb-0">
                        Nuova news
                      </h6>
    
    
                    </div>
    
                  </a>                  
                  <a class="list-group-item" href="domande">
    
                    <!-- Icon -->
                    <div class="icon icon-sm text-primary">
                      <span class="fe fe-file"></span>
                    </div>
    
                    <!-- Content -->
                    <div class="ms-4">
    
                      <!-- Heading -->
                      <h6 class="fw-bold text-uppercase text-primary mb-0">
                        Elenco newss
                      </h6>
    
    
                    </div>
    
                  </a>                 
                </div>
              </div>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" style="color:white" id="navbarDocumentation" data-bs-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                Comunicazioni
              </a>
              <div class="dropdown-menu dropdown-menu-md" aria-labelledby="navbarDocumentation">
                <div class="list-group list-group-flush">
                  <a class="list-group-item" href="./docs/index.html">
    
                    <!-- Icon -->
                    <div class="icon icon-sm text-primary">
                      <span class="fe fe-mail"></span>
                    </div>
    
                    <!-- Content -->
                    <div class="ms-4">
    
                      <!-- Heading -->
                      <h6 class="fw-bold text-uppercase text-primary mb-0">
                        Nuova comunicazione singola
                      </h6>
    
    
                    </div>
    
                  </a>
                  <a class="list-group-item" href="./docs/alerts.html">
    
                    <!-- Icon -->
                    <div class="icon icon-sm text-primary">
                      <span class="fe fe-mail"></span>
                    </div>
    
                    <!-- Content -->
                    <div class="ms-4">
    
                      <!-- Heading -->
                      <h6 class="fw-bold text-uppercase text-primary mb-0">
                        Nuova comunicazione massiva
                      </h6>
    
    
                    </div>
    
                  </a>
                  <a class="list-group-item" href="./docs/changelog.html">
    
                    <!-- Icon -->
                    <div class="icon icon-sm text-primary">
                      <span class="fe fe-mail"></span>
                    </div>
    
                    <!-- Content -->
                    <div class="ms-4">
    
                      <!-- Heading -->
                      <h6 class="fw-bold text-uppercase text-primary mb-0">
                        Elenco comunicazioni
                      </h6>
                    </div>
                  </a>
                </div>
              </div>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" style="color:white" id="navbarDocumentation" data-bs-toggle="dropdown" href="./dara_configurazione.php" aria-haspopup="true" aria-expanded="false">
                Tabelle
              </a>

            </li>              
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" style="color:white" id="navbarDocumentation" data-bs-toggle="dropdown" href="./dara_configurazione.php" aria-haspopup="true" aria-expanded="false">
                Configurazione
              </a>

            </li>                    
          </ul>
    
          <!-- Button -->
            <div class="dropdown">
              <button class="btn btn-xs btn-primary dropdown-toggle" type="button" id="dropdownMenuExtraSmall" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fe fe-user"></i> '.$operatore.'
              </button>
              <div class="dropdown-menu dropdown-menu-xs" aria-labelledby="dropdownMenuExtraSmall">
                <a class="dropdown-item" href="#!">Cambia password</a>
                <a class="dropdown-item" onclick="location.href = \'login\'">Esci</a>
              </div>
            </div>
        </div>
    
      </div>
    </nav>';

    return $navbar;

}

function getWNAVBARNOLOGIN()
{

    $navbar='<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
      <div class="container">
    
        <!-- Brand -->
        <a class="navbar-brand" href="./dabliu_index.php">
          <img src="../librerie/assets/img/logoregionemarche.png" class="navbar-brand-img" alt="...">&nbsp;&nbsp;
          <img src="../librerie/assets/img/logosos.jpg" class="navbar-brand-img" alt="...">
        </a>
    
        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
    
        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="navbarCollapse">
    
          <!-- Toggler -->
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fe fe-x"></i>
          </button>
    
          <!-- Navigation -->
          <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" id="navbarHOME"  href="./dabliu_login.php" >
                Accedi
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" id="navbarCONTATTI" data-bs-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                Contatti
              </a>
            </li>            

          </ul>
    
        </div>
    
      </div>
    </nav> ';

    return $navbar;
}

function getWFOOTERNOLOGIN()
{

  $footer='<footer class="py-8 py-md-8  border-top border-gray-800-50" style="background-color:#22335c; z-index:-1;">
      <div class="container" style="background-color:#22335c; margin-left:45%">
        <div class="row">
          <div class="col-8 col-md-10 col-lg-10" >
    
            <!-- Brand -->
            <img src="../librerie/assets/img/logopon.jpg" alt="..." class="img-fluid " >

    
          </div>

          <div class="col-2 col-md-2 col-lg-2">
    
            <!-- Heading -->
            <h6 class="fw-bold text-uppercase text-gray-700">
              Legal
            </h6>
    
            <!-- List -->
            <ul class="list-unstyled text-muted mb-0">
              <li class="mb-3" style="font-size: 12px">
                <a href="#!" class="text-reset">
                  Informativa sulla privacy
                </a>
              </li>

            </ul>
    
          </div>
        </div> <!-- / .row -->
      </div> <!-- / .container -->
    </footer>';

    return $footer;
}


function getSUPERVISIONEHEAD()
{
    $head='<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
  
    <!-- Favicon -->
    <link rel="shortcut icon" href="../librerie/assets/favicon/favicon.ico" type="image/x-icon" />
    
    <!-- Map CSS -->
    <link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.css" />
    
    <!-- Libs CSS -->
    <link rel="stylesheet" href="../librerie/assets/css/libs.bundle.css" />
    
    <!-- Theme CSS -->
    <link rel="stylesheet" href="../librerie/assets/css/theme.bundle.css" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script src="../librerie/lib.dara.js?v='.JS_LIB_VERSION.'"></script>

    <script src="../librerie/dependsOn-1.0.0.min.js"></script>
    
    <link rel="stylesheet" href="../librerie/dara.css" />

    <!-- https://icon-sets.iconify.design/ -->
    <script src="https://code.iconify.design/iconify-icon/1.0.0-beta.3/iconify-icon.min.js"></script>
    
    <!-- Title -->
    <title>Supervisione</title>';

    return $head;
}

function getSUPERVISIONENAVBAR($flagfixed=false)
{
  global $db;

  $idoperatore=verificaUSER();
  $sSQL="select concat_ws(' ',operatore_cognome,operatore_nome) as nominativo from dara_operatore where iddara_operatore='$idoperatore'";
  $operatore=strtoupper($db->getVALUE($sSQL,'nominativo'));

  $fixed="";
  if ($flagfixed)
    $fixed="fixed-top";

  $navbar='<!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-light '.$fixed.' bg-dark border-bottom">        
      <div class="container">
      <!--div class="container-fluid"-->
    
        <!-- Brand -->
        <a class="navbar-brand" href="home">
          <img src="../librerie/assets/img/brand.svg" class="navbar-brand-img" alt="...">
        </a>
    
        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
    
        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="navbarCollapse">
    
          <!-- Toggler -->
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fe fe-x"></i>
          </button>

          <!-- Navigation -->          
          <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" style="color:white" id="navbarPages" data-bs-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                Pianificazione corsi
              </a>
              <div class="dropdown-menu dropdown-menu-md" aria-labelledby="navbarDocumentation">
                <div class="list-group list-group-flush">
                  <a class="list-group-item" href="domanda">
    
                    <!-- Icon -->
                    <div class="icon icon-sm text-primary">
                      <span class="fe fe-file"></span>
                    </div>
    
                    <!-- Content -->
                    <div class="ms-4">
    
                      <!-- Heading -->
                      <h6 class="fw-bold text-uppercase text-primary mb-0">
                        Nuovo corso
                      </h6>
    
    
                    </div>
    
                  </a>                  
                  <a class="list-group-item" href="domande">
    
                    <!-- Icon -->
                    <div class="icon icon-sm text-primary">
                      <span class="fe fe-file"></span>
                    </div>
    
                    <!-- Content -->
                    <div class="ms-4">
    
                      <!-- Heading -->
                      <h6 class="fw-bold text-uppercase text-primary mb-0">
                        Elenco corsi
                      </h6>
    
    
                    </div>
    
                  </a>                 
                </div>
              </div>
            </li>
            <li class="nav-item dropdown">

              <a class="nav-link" style="color:white" id="navbarDocumentation" href="utenti" aria-haspopup="true" aria-expanded="false">
                Utenti
              </a>
            </li>
                       
          </ul>
    
          <!-- Button -->
            <div class="dropdown">
              <button class="btn btn-xs btn-primary dropdown-toggle" type="button" id="dropdownUSER" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fe fe-user"></i> '.$operatore.'
              </button>
              <div class="dropdown-menu dropdown-menu-xs" aria-labelledby="dropdownUSER">
                <!--a class="dropdown-item" href="#!">Cambia password</a-->
                <a class="dropdown-item" onclick="location.href = \'login\'">Esci&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
              </div>
            </div>
        </div>
    
      </div>
    </nav>';

    return $navbar;

}

function getSUPERVISIONENAVBARNOLOGIN()
{

    $navbar='<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
      <div class="container">
    
        <!-- Brand -->
        <a class="navbar-brand" href="./supervisione_index.php">          
          <img src="../librerie/assets/img/brand.svg" class="navbar-brand-img" alt="...">

        </a>
    
        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
    
        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="navbarCollapse">
    
          <!-- Toggler -->
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fe fe-x"></i>
          </button>
    
          <!-- Navigation -->
          <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" id="navbarHOME"  href="./dabliu_login.php" >
                Accedi
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" id="navbarCONTATTI" data-bs-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                Contatti
              </a>
            </li>            

          </ul>
    
        </div>
    
      </div>
    </nav> ';

    return $navbar;
}

function getSUPERVISIONEFOOTERNOLOGIN()
{

  $footer='<footer class="py-8 py-md-11 bg-dark border-top border-gray-800-50">
      <div class="container">
        <div class="row">
          <div class="col-12 col-md-4 col-lg-3">
    
            <!-- Brand -->
            <img src="../librerie/assets/img/brand.svg" alt="..." class="footer-brand img-fluid mb-2">
    
            <!-- Text -->
            <p class="text-gray-700 mb-2">
              A better way to build.
            </p>
    
            <!-- Social -->
            <ul class="list-unstyled list-inline list-social mb-6 mb-md-0">
              <li class="list-inline-item list-social-item me-3">
                <a href="#!" class="text-decoration-none">
                  <img src="../librerie/assets/img/icons/social/instagram.svg" class="list-social-icon" alt="...">
                </a>
              </li>
              <li class="list-inline-item list-social-item me-3">
                <a href="#!" class="text-decoration-none">
                  <img src="../librerie/assets/img/icons/social/facebook.svg" class="list-social-icon" alt="...">
                </a>
              </li>
              <li class="list-inline-item list-social-item me-3">
                <a href="#!" class="text-decoration-none">
                  <img src="../librerie/assets/img/icons/social/twitter.svg" class="list-social-icon" alt="...">
                </a>
              </li>
              <li class="list-inline-item list-social-item">
                <a href="#!" class="text-decoration-none">
                  <img src="../librerie/assets/img/icons/social/pinterest.svg" class="list-social-icon" alt="...">
                </a>
              </li>
            </ul>
    
          </div>

          <div class="col-6 col-md-4 col-lg-2">
    
            <!-- Heading -->
            <h6 class="fw-bold text-uppercase text-gray-700">
              Legal
            </h6>
    
            <!-- List -->
            <ul class="list-unstyled text-muted mb-0">
              <li class="mb-3" style="font-size: 12px">
                <a href="#!" class="text-reset">
                  Informativa sulla privacy
                </a>
              </li>

            </ul>
    
          </div>
        </div> <!-- / .row -->
      </div> <!-- / .container -->
    </footer>';

    

    return $footer;
}

function getREBUILDINGHEAD()
{
    $head='<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
  
    <!-- Favicon -->
    <link rel="shortcut icon" href="../librerie/assets/favicon/favicon.ico" type="image/x-icon" />
    
    <!-- Map CSS -->
    <link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.css" />
    
    <!-- Libs CSS -->
    <link rel="stylesheet" href="../librerie/assets/css/libs.bundle.css" />
    
    <!-- Theme CSS -->
    <link rel="stylesheet" href="../librerie/assets/css/theme.bundle.css" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <script src="../librerie/lib.dara.js"></script>
    <script src="../rebuilding/js/lib.rebuilding.js"></script>

    <!-- Title -->
    <title>Rebuilding</title>';

    return $head;
}

function getREBUILDINGNAVBAR($flagfixed=false)
{
  global $db;

  $idoperatore=verificaUSER();
  $sSQL="select concat_ws(' ',operatore_cognome,operatore_nome) as nominativo from dara_operatore where iddara_operatore='$idoperatore'";
  $operatore=strtoupper($db->getVALUE($sSQL,'nominativo'));

  $fixed="";
  if ($flagfixed)
    $fixed="fixed-top";

  $navbar='<!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-light '.$fixed.' bg-dark border-bottom">        
      <div class="container">
      <!--div class="container-fluid"-->
    
        <!-- Brand -->
        <!--a class="navbar-brand" href="home">
          <img src="../librerie/assets/img/logosos.jpg" class="navbar-brand-img" alt="...">
        </a-->
    
        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
    
        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="navbarCollapse">
    
          <!-- Toggler -->
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fe fe-x"></i>
          </button>

          <!-- Navigation -->          
          <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
              <a class="nav-link " style="color:white" id="navbarSPORTELLI" href="sportelli" aria-haspopup="true" aria-expanded="false">
                Sportelli tematici
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link " style="color:white" id="navbarFORMAZIONE"  href="corsi" aria-haspopup="true" aria-expanded="false">
                Corso formazione
              </a>
            </li>            
            <li class="nav-item dropdown">
              <a class="nav-link " style="color:white" id="navbarTOOLKIT"  href="toolkit_menu" aria-haspopup="true" aria-expanded="false">
                Tool kit
              </a>
            </li>
            <!--li class="nav-item dropdown">
              <a class="nav-link " style="color:white" id="navbarTABELLE" " href="#" aria-haspopup="true" aria-expanded="false">
                Tabelle
              </a>

            </li-->              
            <!--li class="nav-item dropdown">
              <a class="nav-link " style="color:white" id="navbarAUTORIZZAZIONI"  href="#" aria-haspopup="true" aria-expanded="false">
                Autorizzazioni
              </a>

            </li-->                    
          </ul>
    
          <!-- Button -->
            <div class="dropdown">
              <button class="btn btn-xs btn-primary dropdown-toggle" type="button" id="dropdownMenuExtraSmall" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fe fe-user"></i> '.$operatore.'
              </button>
              <div class="dropdown-menu dropdown-menu-xs" aria-labelledby="dropdownMenuExtraSmall">
                <a class="dropdown-item" href="#!">Cambia password</a>
                <a class="dropdown-item" onclick="logout()">Esci</a>
              </div>
            </div>
        </div>
    
      </div>
    </nav>';
    //<a class="dropdown-item" onclick="location.href = \'login\'">Esci</a>
   
    return $navbar;

}

function verificaUSER()
{
	global $db;
  date_default_timezone_set('Europe/Rome');

	$session_valid=false;

	@session_start();

	$chiaveRSA=$_SESSION["dara_userkey"];
 
  if(isset($_COOKIE['authservice_authidSPID']))
	{
		$sSQL="SELECT iddara_sessions from dara_sessions where sessions_chiave='$chiaveRSA'";
		$iddara_sessions=$db->getVALUE($sSQL,"iddara_sessions");
   
		if(!empty($iddara_sessions))
		{
      $sSQL="SELECT sessions_expire from dara_sessions where iddara_sessions='$iddara_sessions'";
      $sessions_expire=$db->getVALUE($sSQL,"sessions_expire");
     
      $ora=date("Y-m-d H:i:s",time());
      $today_dt = new DateTime($ora);
      $expire_dt = new DateTime($sessions_expire);

      if($today_dt<=$expire_dt)
      {
  			include_once("../librerie/class.rsa.php");
       
  			$crypt = new RsaCrypt;  

  			$crypt->setPrivateKey('../librerie/privateDARA.pem');

  			$chiaveRSA=base64_decode($chiaveRSA);
  			$chiaveDECRYPT=$crypt->decrypt($chiaveRSA);

  			if(!empty($chiaveDECRYPT))
  			{
  				$aCHIAVE=explode("|",$chiaveDECRYPT);
  				$aCHIAVE=explode("#",$aCHIAVE[1]);
  				$idoperatore=$aCHIAVE[0];
  				if(!empty($idoperatore))
  				{
  					$session_valid=true;
  					return $idoperatore;
  				}
  			}
      }
		}
	}

	if(!$session_valid)
	{
    //echo $sSQL;
    //die("<br>sessione scaduta");
		@header("Location: ../rebuilding/login?_s=false");
	}
}

function getFRONTDARANAVBAR($flagfixed=false)
{
  global $db;

  $idoperatore=verificaEUSER();
  $sSQL="select concat_ws(' ',anagrafica_cognome,anagrafica_nome) as nominativo from dara_anagrafica where iddara_anagrafica='$idoperatore'";
  $operatore=strtoupper($db->getVALUE($sSQL,'nominativo'));

  $fixed="";
  if ($flagfixed)
    $fixed="fixed-top";

  $navbar='<!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-light '.$fixed.' bg-dark border-bottom">        
      <div class="container">
      <!--div class="container-fluid"-->
    
        <!-- Brand -->
        <a class="navbar-brand" href="home-f">
          <img src="../librerie/assets/img/brand.svg" class="navbar-brand-img" alt="...">
        </a>
    
        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
    
        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="navbarCollapse">
    
          <!-- Toggler -->
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fe fe-x"></i>
          </button>

          <!-- Navigation -->          
          <ul class="navbar-nav ms-auto">

            <li class="nav-item dropdown">
              <a class="nav-link" style="color:white" id="navbarPages" href="domande-f" aria-haspopup="true" aria-expanded="false">
                Iscrizioni
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" style="color:white" id="navbarAccount" href="#" aria-haspopup="true" aria-expanded="false">
                Presenze
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link " style="color:white" id="navbarDocumentation" data-bs-toggle="dropdown" href="./dara_configurazione.php" aria-haspopup="true" aria-expanded="false">
                Pagamenti
              </a>
            </li>              
            <li class="nav-item dropdown">
              <a class="nav-link " style="color:white" id="navbarDocumentation" href="#" aria-haspopup="true" aria-expanded="false">
                Comunicazioni
              </a>
            </li>   
                  
          </ul>
    
          <!-- Button -->
            <div class="dropdown">
              <button class="btn btn-xs btn-primary dropdown-toggle" type="button" id="dropdownMenuExtraSmall" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fe fe-user"></i> '.$operatore.'
              </button>
              <div class="dropdown-menu dropdown-menu-xs" aria-labelledby="dropdownMenuExtraSmall">
                <a class="dropdown-item" href="#!">Cambia password</a>
                <a class="dropdown-item" onclick="location.href = \'login\'">Esci</a>
              </div>
            </div>
        </div>
    
      </div>
    </nav>';

    return $navbar;

}


function insertESESSION($chiave)
{
  global $db;

  $data=dataodierna();
  $orario=oraodierna();

  $timestamp_expiration = time() + 6 * 3600;
  $timestamp_expiration = date('Y-m-d H:i:s',$timestamp_expiration);

  $sSQL="insert into dara_sessions (sessions_chiave,sessions_data,sessions_ora,sessions_expire) values('$chiave','$data','$orario','$timestamp_expiration')";
  $db->query($sSQL);

  session_start();
  $_SESSION["dara_euserkey"]=$chiave;


}

function verificaEUSER()
{
  global $db;

  $session_valid=false;

  @session_start();

  $chiaveRSA=$_SESSION["dara_euserkey"];
  if(!empty($chiaveRSA))
  {
    $sSQL="select iddara_sessions from dara_sessions where sessions_chiave='$chiaveRSA' and CURRENT_TIMESTAMP()<=sessions_expire";
    $iddara_sessions=$db->getVALUE($sSQL,"iddara_sessions");
    if(!empty($iddara_sessions))
    {
      include_once("../librerie/class.rsa.php");

      $crypt = new RsaCrypt;  

      $crypt->setPrivateKey('../librerie/privateDARA.pem');

      $chiaveRSA=base64_decode($chiaveRSA);
      $chiaveDECRYPT=$crypt->decrypt($chiaveRSA);

      if(!empty($chiaveDECRYPT))
      {
        $aCHIAVE=explode("|",$chiaveDECRYPT);
        $aCHIAVE=explode("#",$aCHIAVE[1]);
        $idoperatore=$aCHIAVE[0];
        if(!empty($idoperatore))
        {
          $session_valid=true;
          return $idoperatore;
        }
      }
    }
  }

  if(!$session_valid)
  {
    @header("Location: ../sport/dara_login.php?_s=false");
  }
}

function printALERT($tipologia=null,$testo=null,$dismissibile=false)
{
	if(!empty($tipologia) && !empty($testo))
	{
		switch($tipologia)
		{
			case "ALERT_PRIMARY":
				$class="primary";
				break;

			case "ALERT_SECONDARY":
				$class="secondary";
				break;

			case "ALERT_SUCCESS":
				$class="success";
				break;

			case "ALERT_DANGER":
				$class="danger";
				break;

			case "ALERT_WARNING":
				$class="warning";
				break;

			case "ALERT_INFO":
				$class="info";
				break;

			case "ALERT_LIGHT":
				$class="light";
				break;

			case "ALERT_DARK":
				$class="dark";
				break;
		}

		if($dismissibile)
		{
			$class_dismissibile='alert-dismissible fade show';
			$button_close='<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
		}

		return '<div class="alert alert-'.$class.' '.$class_dismissibile.'" role="alert">'.$testo.' '.$button_close.'</div>';

	}
}

function stringXMLClean($strin) {
  $strout = null;

  for ($i = 0; $i < @strlen($strin); $i++) {
    $ord = ord($strin[$i]);

    if (($ord > 0 && $ord < 32) || ($ord >= 127)) {
      $strout .= "&amp;#{$ord};";
    }
    else {
      switch ($strin[$i]) {
        case '<':
        $strout .= '&lt;';
        break;
        case '>':
        $strout .= '&gt;';
        break;
        case '&':
        $strout .= '&amp;';
        break;
        case '"':
        $strout .= '&quot;';
        break;
        default:
        $strout .= $strin[$i];
      }
    }
  }

  return $strout;
}

function generaBREADCUMB($aPAGES)
{
	$breadcumb='<nav class="bg-gray-200">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <ol class="breadcrumb breadcrumb-scroll">';

      			$lastElement = end($aPAGES);

      			foreach($aPAGES as $index=>$dett_page)
      			{
              $url=$dett_page["url"];
              if(empty($url))
                $url="javascript: void(0);";

      				$element="";
      				if($lastElement==$dett_page)
      				{
      					$element='<li class="breadcrumb-item active" aria-current="page"><a href="'.$url.'">'.$dett_page["titolo_pagina"].'</a></li>';
      				}
      				else
              {
      					$element='<li class="breadcrumb-item"><a href="'.$url.'">'.$dett_page["titolo_pagina"].'</a></li>';
              }

      				$breadcumb.=$element;
      			}

            $breadcumb.='</ol>
          </div>
        </div> 
      </div> 
	</nav>';

	echo $breadcumb;
}

function getORGANIGRAMMARUOLO($condizione="")
{
    global $db;
    $sSQL="select * from dara_tbl_organigrammaruolo ";
    $sOrder=" order by ruolo_descrizione ";
    if (!empty($condizione))
      $sWhere=" where ".$condizione;

    $sSQL=$sSQL.$sWhere.$sOrder;
    $aRUOLI=$db->select($sSQL);
    
    return $aRUOLI; 
}

function getNOTIZIACATEGORIA($condizione="")
{
    global $db;
    $sSQL="select * from card_tbl_notiziacategoria ";
    $sOrder=" order by idcard_tbl_notiziacategoria ";
    if (!empty($condizione))
      $sWhere=" where ".$condizione;

    $sSQL=$sSQL.$sWhere.$sOrder;
    $aCATEGORIE=$db->select($sSQL);
    
    return $aCATEGORIE; 
}

function encode_items(&$item, $key)
{
	$item = utf8_encode($item);
}

function get_alert($id_alert, $testo='errore', $align="center")
{
  $style="width: 100%; padding-right:10%; padding_left:10%;";

  $alert="";

  switch($id_alert)
  {
    case 0: //ERRORE
      $alert= "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\" style=\"".$style."\">
            $testo
            <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>
          </div>";
      break;
    
    case 1: //SALVATAGGIO
      $alert= "<div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\" style=\"".$style."\">
            $testo
            <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>
          </div>";
      break;  
      
    case 2: //WARNING
      $alert= "<div class=\"alert alert-warning alert-dismissible fade show\" role=\"alert\" style=\"".$style."\">
            $testo
            <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>
          </div>";
      break;

    case 3:  //ALERT INFO
      $alert= "<div class=\"alert alert-info alert-dismissible fade show\" role=\"alert\" style=\"".$style."\">
            $testo
          <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>
        </div>";
      break;
    case 4: //SALVATAGGIO CUSTOM
      $alert= "<div class='alert alert-success' role='alert' style='".$style."'>
                  $testo
              </div>";
      break;  
  }

  return '<span style="text-align: '.$align.'">'.$alert.'</span><br>';

}

function getNEWSPROFILO($profilo)
{
  $aPROFILO=array(1=>"Sostegno al reddito",2=>"Famiglia e figli",3=>"Assistenza e cura");
  return $aPROFILO[$profilo];
}

function generalENCRYPT($key)
{
	include "../librerie/class.rsa.php";

	$crypt = new RsaCrypt;	

	//$crypt->genKeys(2048);

	$crypt->setPublicKey('../librerie/publicDARA.pem');
	$crypt->setPrivateKey('../librerie/privateDARA.pem');
	$dataRSA = $crypt->encrypt("DARA|".$key);	
	$dataRSA=base64_encode($dataRSA);

	return $dataRSA;
}

function generalDECRYPT($chiaveRSA)
{
  include_once("../librerie/class.rsa.php");

  $crypt = new RsaCrypt;  

  $crypt->setPrivateKey('../librerie/privateDARA.pem');

  $chiaveRSA=base64_decode($chiaveRSA);
  $chiaveDECRYPT=$crypt->decrypt($chiaveRSA);

	return $chiaveDECRYPT;
}

function getCOMUNI($condizione="")
{
    global $db;
    $sSQL="select * from comune ";
    $sOrder=" order by comune.comune ";
    if (!empty($condizione))
      $sWhere=" where ".$condizione;

    $sSQL=$sSQL.$sWhere.$sOrder;
    $aCONUNI=$db->select($sSQL);
    
    return $aCONUNI; 
}

function getTAGLIE($condizione="")
{
    global $db;
    $sSQL="select * from dara_taglia ";
    $sOrder=" order by iddara_taglia ";
    if (!empty($condizione))
      $sWhere=" where ".$condizione;

    $sSQL=$sSQL.$sWhere.$sOrder;
    $aTAGLIE=$db->select($sSQL);
    
    return $aTAGLIE; 
}

function getMERCEOLOGICA($condizione="")
{
    global $db;
    $sSQL="select * from dara_tbl_merceologica ";
    $sOrder=" order by merceologica_descrizione ";
    if (!empty($condizione))
      $sWhere=" where ".$condizione;

    $sSQL=$sSQL.$sWhere.$sOrder;
    $aTIPOLOGIA=$db->select($sSQL);
    
    return $aTIPOLOGIA; 
}

function descrizioneMERCEOLOGICA($iddara_tbl_merceologica)
{
    global $db;
    $sSQL="select merceologica_descrizione from dara_tbl_merceologica ";
        
    $sWhere=" where iddara_tbl_merceologica='$iddara_tbl_merceologica'";

    $sSQL=$sSQL.$sWhere;
    $merceologica_descrizione=$db->getVALUE($sSQL,"merceologica_descrizione");
    
    return $merceologica_descrizione; 
}

function getANNI($condizione="")
{
    global $db;
    $sSQL="select * from dara_tbl_anno ";
    $sOrder=" order by anno_codice desc ";
    if (!empty($condizione))
      $sWhere=" where ".$condizione;

    $sSQL=$sSQL.$sWhere.$sOrder;
    $aANNI=$db->select($sSQL);
    
    return $aANNI; 
}

function descrizioneANNO($iddara_tbl_anno)
{
    global $db;
    $sSQL="select anno_descrizione from dara_tbl_anno ";
        
    $sWhere=" where iddara_tbl_anno='$iddara_tbl_anno'";

    $sSQL=$sSQL.$sWhere;
    $anno_descrizione=$db->getVALUE($sSQL,"merceologica_descrizione");
    
    return $anno_descrizione; 
}


function db_string($string_value)
{
	$string_value = stripslashes($string_value);
	$string_value = str_replace('"', "'", $string_value);
	$string_value = str_replace("'", "''", $string_value);
	/*$string_value = str_replace ("à", "&agrave;", $string_value);
	$string_value = str_replace ("è", "&egrave;", $string_value);
	$string_value = str_replace ("ì", "&igrave;", $string_value);
	$string_value = str_replace ("ò", "&ograve;", $string_value);
	$string_value = str_replace ("ù", "&ugrave;", $string_value);*/
	return $string_value;
}

function empty_data($data)
{
	if($data=='0000-00-00' || $data== '' || $data==null)
		return true;
	else
		return false;
}


function print_r_formatted($array)
{
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}

function removeslashes($string)
{
    $string=implode("",explode("\\",$string));
    return stripslashes(trim($string));
}

function setcookieAuthServiceAuhtId($authid,$tipologia)
{
	
	// GF $host=$_SERVER['HTTP_HOST'];
	$host="localhost";
  

	if(isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on')
		setcookie('authservice_authid'.$tipologia, $authid,0, "/", $host, true, true);
	else
		setcookie('authservice_authid'.$tipologia, $authid,0, "/", $host, false, true);
}

function clean($string) {
  $string = str_replace("“","\"",$string);
  $string = str_replace("”","\"",$string);
  $string = str_replace("–","-",$string);
  $string = str_replace("€","E.",$string);
  $string = str_replace("à","a'",$string);
  $string = str_replace("è","e'",$string);
  $string = str_replace("é","e'",$string);
  $string = str_replace("ù","u'",$string);
  $string = str_replace("ò","o'",$string);
  $string = str_replace("ì","i'",$string);
  $string = str_replace("’","'",$string);

  return $string; // Replaces multiple hyphens with single one.
}

?>
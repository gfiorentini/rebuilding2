<?php
//phpinfo();
/*
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
*/
require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");
require_once("../librerie/dara.class.servizi.php");


//error_reporting(0);
//ini_set("display_errors",1);
date_default_timezone_set('Europe/Rome');

$str_error_upload='';

$idoperatore=verificaUSER();
$pidincontro = NULL;

$operatore=new DARAOperatore($idoperatore);
$operatore_ente=$operatore->operatore_ente;
if(empty($operatore_ente))
  $operatore_ente=9999;

$operatore_flagamministratore=$db->getVALUE("select operatore_flagamministratore from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagamministratore");
$operatore_flagrup=$db->getVALUE("select operatore_flagrup from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagrup");
$operatore_flagdirigente=$db->getVALUE("select operatore_flagdirigente from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagdirigente");

$aENTI=array(1=>"ATS1 - Pesaro",3=>"ATS3 - C.M. Catria e Nerone",4=>"ATS4 - Urbino",5=>"ATS5 - C.M. Montefeltro",6=>"ATS6 - Fano",7=>"ATS7 - Fossombrone",8=>"ATS8 - Senigallia",9=>"ATS9 - ASP Ambito 9 Jesi",10=>"ATS10 - Fabriano",11=>"ATS11 - Ancona",12=>"ATS12 - Falconara Marittima",13=>"ATS13 - Osimo",14=>"ATS14 - Civitanova Marche",15=>"ATS15 - Macerata",16=>"ATS16 - C.M. Monti Azzurri",17=>"ATS17 - Unione montanta alta valle del potenza e dell'Esino",18=>"ATS18 - C.M. Camerino",19=>"ATS19 - Fermo",20=>"ATS20 - Porto Sant'Elpidio",21=>"ATS 21 - San Benedetto del Tronto",22=>"ATS22 - Ascoli Piceno",23=>"ATS23 - U.C. Vallata del Tronto",24=>"ATS24 - C.M. dei Sibillini");

if ($operatore_ente != 9999) {
  $centroterritorialeOPERATORE=$aENTI[$operatore_ente];
} else {
  if ($operatore->operatore_flagamministratore == 1) {
    $centroterritorialeOPERATORE = "REGIONE MARCHE";
  }
}

$aANNI=array(2017=>"2017",2018=>"2018",2019=>"2019",2020=>"2020",2021=>"2021",2022=>"2022",2023=>"2023",2024=>"2024",2025=>"2025",2026=>"2026", 2027=>"2027");

$operatori=new DARAOperatore(0);
$aRUP=$operatori->getOPERATORI('operatore_flagamministratore=1 and operatore_flagabilitato=1 and operatore_flagrup=1');

$pfk_idrebuilding_gld = getPARAMETRO("igl");   // id gruppo di lavoro
$pidincontro = getPARAMETRO("idr"); // id riunione
//
//
$rm_profilo_utente = $db->select("select * from dara_operatore where iddara_operatore='$idoperatore' ") ;
$rm_operatore_codicefiscale=$rm_profilo_utente[0]["operatore_codicefiscale"];
$gcurrentgruppo =  $db->select("select * from rebuilding_gruppi_di_lavoro where idrebuilding_gld=$pfk_idrebuilding_gld ") ;
$gcurrentgruppo_auth = $db->select("select * from rebuilding_gruppi_di_lavoro_auth rgdla
	where  rgdla.fk_idrebuilding_gld=$pfk_idrebuilding_gld AND codice_fiscale='$rm_operatore_codicefiscale' ");
$gcurrentgruppo_auth_canView = $gcurrentgruppo_auth[0]["canView"];
$gcurrentgruppo_auth_canEdit = $gcurrentgruppo_auth[0]["canEdit"];
// VERIFICA ED EVENTUALMENTE CREA LA CARTELLA PER LE RIUNIONI  DEL GRUPPO DI LAVORO
$riunioniRoot = $_SERVER["DOCUMENT_ROOT"] . '/riunioni/';
$riunioniRootGruppo = $riunioniRoot. $gcurrentgruppo[0]["gdl_path"] . '/';
//

$incontroDAO=new rebuildingGruppiDiLavoroIncontri($pidincontro);

if (empty($incontroDAO->incontro_file_agenda)) {
  $url_incontro_file_agenda = "#";
} else {
  $url_incontro_file_agenda = '/riunioni/' . $gcurrentgruppo[0]["gdl_path"] . '/incontro' . $pidincontro . '/' . $incontroDAO->incontro_file_agenda ; 
}
if (empty($incontroDAO->incontro_file_verbale)) {
  $url_incontro_file_verbale = "#";
} else {
  $url_incontro_file_verbale = '/riunioni/' . $gcurrentgruppo[0]["gdl_path"] . '/incontro' . $pidincontro . '/' . $incontroDAO->incontro_file_verbale ; 
}
if (empty($incontroDAO->incontro_file_video)) {
  $url_incontro_file_video = "#";
} else {
  $url_incontro_file_video = '/riunioni/' . $gcurrentgruppo[0]["gdl_path"] . '/incontro' . $pidincontro . '/' . $incontroDAO->incontro_file_video ; 
}
if (empty($incontroDAO->incontro_file_trascrizione)) {
  $url_incontro_file_trascrizione = "#";
} else {
  $url_incontro_file_trascrizione = '/riunioni/' . $gcurrentgruppo[0]["gdl_path"] . '/incontro' . $pidincontro . '/' . $incontroDAO->incontro_file_trascrizione ; 
}
if (empty($incontroDAO->incontro_file_altro_materiale)) {
  $url_incontro_file_altro_materiale = "#";
} else {
  $url_incontro_file_altro_materiale = '/riunioni/' . $gcurrentgruppo[0]["gdl_path"] . '/incontro' . $pidincontro . '/' . $incontroDAO->incontro_file_altro_materiale ; 
}

if (empty($incontroDAO->incontro_file_partecipanti)) {
  $url_incontro_file_partecipanti = "#";
} else {
  $url_incontro_file_partecipanti = '/riunioni/' . $gcurrentgruppo[0]["gdl_path"] . '/incontro' . $pidincontro . '/' . $incontroDAO->incontro_file_partecipanti ; 
}

$disabled_scheda="";


?>
<!doctype html>
<html lang="it">
  <head>
  	 <?php echo getREBUILDINGHEAD(true); ?>

     <link rel="stylesheet" href="../librerie/css/bootstrap-select.css">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">


     <style>
    textarea {
        height: 8em !important; /* 5 rows with additional space for padding */
        line-height: 1.2em; /* Adjust line height if needed */
    }


     </style>

  </head>
  <body class="bg-light">

  	<?php echo getREBUILDINGNAVBAR(); ?>
    
    <!-- BREADCRUMB -->
    <?php 
		$aBREADCUMB=array();

		$aBREADCUMB[0]["titolo_pagina"]="Home";
		$aBREADCUMB[0]["url"]="home";

		$aBREADCUMB[1]["titolo_pagina"]="Toolkit";
		$aBREADCUMB[1]["url"]="toolkit_menu";

		$aBREADCUMB[2]["titolo_pagina"]="Riunioni";
		$aBREADCUMB[2]["url"]="rebuilding_riunioni_menu";

    $aBREADCUMB[3]["titolo_pagina"]= $gcurrentgruppo[0]["gdl_titolo"];
    $aBREADCUMB[3]["url"]="rebuilding_riunioni_list_giornate?igl=$pfk_idrebuilding_gld" ;

    $aBREADCUMB[4]["titolo_pagina"]="Riunione";
    $aBREADCUMB[4]["url"]="#";

		generaBREADCUMB($aBREADCUMB);

	  ?>

    <section class="bg-light">
      <div class="container-fluid">
      

        <div class="row">
          <div class="col-12 col-lg-6 col-xl-8 offset-xl-2 py-lg-2 bg-light">            
              <div class="card-body">

                <form id="INCONTRO_FORM" name="INCONTRO_FORM" action="rebuilding_riunione_edit" method="post" role="form" enctype="multipart/form-data" >

                  <?php
                  if($str_error_upload!='')
                  {
                    echo "<div class='alert alert-danger alert-dismissible' role='alert' style='width: 100%;'>
                          <button type='button' class='btn btn-danger btn-xs' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
                          $str_error_upload
                      </div>";  
                      
                  }
                  ?>
                  
                  <div class="form-group row">

                    <div class="col-12 col-md-10">
                      <div class="form-floating">
                        <input type="text" readonly aria-readonly="true" id="form_incontro_titolo" name="form_incontro_titolo" class="form-control form-control-flush" required placeholder="Inserire titolo riunione" value="<?php echo htmlspecialchars( $incontroDAO->incontro_titolo );?>" <?php echo $disabled_scheda;?> >
                        <label for="form_incontro_titolo">Titolo</label>
                      </div> 
                    </div>  
                    <hr/>
                    <div class="col-12 col-md-10">
                      <div class="form-floating">
                      <textarea id="form_incontro_abstract" rows="5" readonly aria-readonly="true"  name="form_incontro_abstract" class="form-control form-control-flush"  placeholder="Inserire abstract riunione" > <?php echo htmlspecialchars(  $incontroDAO->incontro_abstract );?></textarea> 
                        <label for="form_incontro_abstract">Ordine del giorno</label>
                      </div> 
                    </div>  
                    <hr/>
                    <div class="col-12 col-md-10">
                      <div class="form-floating">
                        <input type="date"  readonly aria-readonly="true" id="form_incontro_giorno" name="form_incontro_giorno" class="form-control form-control-flush" required placeholder="Data riunione" value="<?php echo $incontroDAO->incontro_giorno;?>" <?php echo $disabled_scheda;?> >
                        <label for="form_incontro_giorno">Data riunione</label>
                      </div> 
                    </div>              
                  </div>      
                  <hr/>
                  <fieldset>
                      <legend>Documenti</legend>
                  <div class="row">
                    <div class="col-4"> <label class="form-label" for="fileConvocazione">Convocazione</label>
                    </div>
                    <div class="col-6">
                      <?php if (empty($incontroDAO->incontro_file_agenda)):   ?>     
                        ** non disponibile **  
                      <?php else: ?>               
                        <a class="btn btn-primary-soft btn-xs small w-60" href="<?php echo  $url_incontro_file_agenda ; ?>"   ><i class="bi bi-download"></i> <?php echo  $incontroDAO->incontro_file_agenda ; ?></a>
                        
                      <?php endif; ?> 
                    </div>
                  </div>
                  <hr/>
                  <div class="row">
                    <div class="col-4"> <label class="form-label" for="filePartecipanti">Partecipanti</label>
                    </div>
                    <div class="col-6">
                      <?php if (empty($incontroDAO->incontro_file_partecipanti)):   ?>     
                        ** non disponibile **  
                      <?php else: ?>               
                        <a class="btn btn-primary-soft btn-xs small w-60" href="<?php echo  $url_incontro_file_partecipanti ; ?>"   ><i class="bi bi-download"></i> <?php echo  $incontroDAO->incontro_file_partecipanti ; ?></a>
                        
                      <?php endif; ?> 
                    </div>
                  </div>
                  <hr/>                  
                  <div class="row mt-3">

                      <?php if (empty($incontroDAO->incontro_file_video)):   ?>    
                        <div class="col-4"><label class="form-label" for="fileVideoRiunione">Registrazione</label>
                        </div>
                        <div class="col-6">                         
                            ** non disponibile **  
                        </div>
                      <?php else: ?>    
                        <div class="col-4"><label class="form-label" for="fileVideoRiunione">Registrazione &nbsp;</label>
                        <video controls width="640" style="border: 2px solid blue;padding: 5px;">
                              <source src="/stream.php?video=<?php echo $url_incontro_file_video ; ?>" type="video/mp4" />
                              Your browser does not support the video tag.
                        </video>                      
                        </div>
                      <?php endif; ?> 
                  </div>
                  <hr/>

                  <div class="row mt-3">
                    <div class="col-4"> <label class="form-label" for="fileVerbale">Resoconto</label>
                    </div>
                    <div class="col-6">
                      <?php if (empty($incontroDAO->incontro_file_verbale)):   ?>     
                         ** non disponibile **  
                      <?php else: ?>  
                        <a class="btn btn-primary-soft btn-xs small w-60" href="<?php echo $url_incontro_file_verbale; ?>"   ><i class="bi bi-download"></i> <?php echo $incontroDAO->incontro_file_verbale; ?></a>
                      <?php endif; ?>   
                    </div>
                  </div>
                  <hr/>

                  <div class="row">
                    <div class="col-4"> <label class="form-label" for="fileVerbale">Trascrizione</label>
                    </div>
                    <div class="col-6">
                      <?php if (empty($incontroDAO->incontro_file_trascrizione)):   ?>     
                         ** non disponibile **  
                      <?php else: ?>  
                        <a class="btn btn-primary-soft btn-xs small w-60" href="<?php echo $url_incontro_file_trascrizione; ?>"   ><i class="bi bi-download"></i> <?php echo $incontroDAO->incontro_file_trascrizione; ?></a>
                      <?php endif; ?>  
                    </div>
                  </div>
                  <hr/>

                  <div class="row">
                    <div class="col-4"> <label class="form-label" for="fileVerbale">Ulteriore Documentazione</label>
                    </div>
                    <div class="col-6">
                      <?php if (empty($incontroDAO->incontro_file_altro_materiale)):   ?>     
                         ** non disponibile **  
                      <?php else: ?>  
                        <a class="btn btn-primary-soft btn-xs small w-60" href="<?php echo $url_incontro_file_altro_materiale; ?>"   ><i class="bi bi-download"></i> <?php echo $incontroDAO->incontro_file_altro_materiale; ?></a>
                      <?php endif; ?>  
                    </div>
                  </div>                  

                  </fieldset>
                  <hr/>

                  <?php if (!getPARAMETRO("_nuovo")):   ?>
                  <?php endif; ?>

                </form>
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


    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.bundle.min.js"></script>
    <script src="../librerie/js/bootstrap-select.js"></script>

  </body>

  <script>


function closeMSG()
{
  $("#liveToast").hide()
}

</script>

</html>





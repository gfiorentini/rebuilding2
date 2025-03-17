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


function checkAndCreateDirectory($directory) {
  // Controlla se la directory esiste
  if (!is_dir($directory)) {
      if (mkdir($directory, 0777, true)) {
      } else {
      }
  } else {
  }
}

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
checkAndCreateDirectory( $riunioniRootGruppo );
//
if(getPARAMETRO("_salva") && $operatore_flagamministratore==1 && empty($operatore_flagdirigente))
{
  $riunioneRoot = $riunioniRootGruppo . "incontro$pidincontro/";
  checkAndCreateDirectory( $riunioneRoot );

  $pform_incontro_titolo=getPARAMETRO("form_incontro_titolo");
  $pform_incontro_titolo=$db->escape_text($pform_incontro_titolo);
  // $aentiselezionati=explode(",",$pflussofinanziario_ente);
  
  $pincontro_abstract=getPARAMETRO("form_incontro_abstract");
  $pincontro_abstract=$db->escape_text($pincontro_abstract);

  $pincontro_giorno=getPARAMETRO("form_incontro_giorno");
  $pincontro_giorno=$db->escape_text($pincontro_giorno);

  $prebuilding_gruppi_di_lavoro_incontri_stato=1;

  $pflussofinanziario_datainserimento=date("Y-m-d");
  if(!empty($pidincontro))
  {   
    $pincontro_dt_last_modified=date("Y-m-d");

    $sSQL="   UPDATE rebuilding_gruppi_di_lavoro_incontri 
    SET incontro_titolo='$pform_incontro_titolo'
    , incontro_abstract='$pincontro_abstract'
    , incontro_giorno='$pincontro_giorno'
    , incontro_dt_created=current_timestamp()
    , incontro_op_created=NULL
    , incontro_dt_last_modified=current_timestamp()
    , incontro_op_last_modified=NULL 
    WHERE idincontro=$pidincontro;"; 
    $db->query($sSQL);

    $alert_update_success=true;
  }
  else
  {
    
    $pform_incontro_titolo=getPARAMETRO("form_incontro_titolo");
    $pform_incontro_titolo=$db->escape_text($pform_incontro_titolo);
    // $aentiselezionati=explode(",",$pflussofinanziario_ente);
    
    $pincontro_abstract=getPARAMETRO("form_incontro_abstract");
    $pincontro_abstract=$db->escape_text($pincontro_abstract);
  
    $pincontro_giorno=getPARAMETRO("form_incontro_giorno");
    $pincontro_giorno=$db->escape_text($pincontro_giorno);

    $sSQL="INSERT INTO rebuilding_gruppi_di_lavoro_incontri 
    (fk_idrebuilding_gld
    , incontro_titolo
    , incontro_abstract
    , incontro_giorno
    , incontro_file_agenda
    , incontro_file_verbale
    , incontro_file_video
    , incontro_file_trascrizione
    , incontro_file_altro_materiale
    , incontro_dt_created
    , incontro_op_created
    , incontro_dt_last_modified
    , incontro_op_last_modified
    ) VALUES ($pfk_idrebuilding_gld
    , '$pform_incontro_titolo'
    , '$pincontro_abstract'
    , '$pincontro_giorno', NULL, NULL, NULL, NULL , NULL, current_timestamp(), NULL, current_timestamp(), NULL);";
    $db->query($sSQL);
    // legge nuovo ID
    $pidincontro=$db->insert_id();

    $alert_insert_success=true;

    header("Location: rebuilding_riunione_edit?igl=$pfk_idrebuilding_gld&idr=$pidincontro");
    exit; // Ensure no further code is executed after the redirect

  }


  /** Manage file uploads */
  if (isset($_FILES['fileConvocazione']) && $_FILES['fileConvocazione']['error'] !== UPLOAD_ERR_NO_FILE) {
    $targetFile = $riunioneRoot . basename($_FILES["fileConvocazione"]["name"]);
    if (move_uploaded_file($_FILES["fileConvocazione"]["tmp_name"], $targetFile)) {
      // ok. 
      $filename = $_FILES["fileConvocazione"]["name"];
      $sSQL="   UPDATE rebuilding_gruppi_di_lavoro_incontri SET incontro_file_agenda='$filename' WHERE idincontro=$pidincontro;"; 
      $db->query($sSQL);
    }
  }

  if (isset($_FILES['fileVerbale']) && $_FILES['fileVerbale']['error'] !== UPLOAD_ERR_NO_FILE) {
    $targetFile = $riunioneRoot . basename($_FILES["fileVerbale"]["name"]);
    if (move_uploaded_file($_FILES["fileVerbale"]["tmp_name"], $targetFile)) {
      // ok. 
      $filename = $_FILES["fileVerbale"]["name"];
      $sSQL="   UPDATE rebuilding_gruppi_di_lavoro_incontri SET incontro_file_verbale='$filename' WHERE idincontro=$pidincontro;"; 
      $db->query($sSQL);
    }
  }

  if (isset($_FILES['fileVideoRiunione']) && $_FILES['fileVideoRiunione']['error'] !== UPLOAD_ERR_NO_FILE) {
    $targetFile = $riunioneRoot . basename($_FILES["fileVideoRiunione"]["name"]);
    if (move_uploaded_file($_FILES["fileVideoRiunione"]["tmp_name"], $targetFile)) {
      // ok. 
      $filename = $_FILES["fileVideoRiunione"]["name"];
      $sSQL="   UPDATE rebuilding_gruppi_di_lavoro_incontri SET incontro_file_video='$filename' WHERE idincontro=$pidincontro;"; 
      $db->query($sSQL);
    }
  }

  if (isset($_FILES['fileTrascrizione']) && $_FILES['fileTrascrizione']['error'] !== UPLOAD_ERR_NO_FILE) {
    $targetFile = $riunioneRoot . basename($_FILES["fileTrascrizione"]["name"]);
    if (move_uploaded_file($_FILES["fileTrascrizione"]["tmp_name"], $targetFile)) {
      // ok. 
      $filename = $_FILES["fileTrascrizione"]["name"];
      $sSQL="   UPDATE rebuilding_gruppi_di_lavoro_incontri SET incontro_file_trascrizione='$filename' WHERE idincontro=$pidincontro;"; 
      $db->query($sSQL);
    }
  }

  if (isset($_FILES['fileAltraDocumentazione']) && $_FILES['fileAltraDocumentazione']['error'] !== UPLOAD_ERR_NO_FILE) {
    $targetFile = $riunioneRoot . basename($_FILES["fileAltraDocumentazione"]["name"]);
    if (move_uploaded_file($_FILES["fileAltraDocumentazione"]["tmp_name"], $targetFile)) {
      // ok. 
      $filename = $_FILES["fileAltraDocumentazione"]["name"];
      $sSQL="   UPDATE rebuilding_gruppi_di_lavoro_incontri SET incontro_file_altro_materiale='$filename' WHERE idincontro=$pidincontro;"; 
      $db->query($sSQL);
    }
  }

   


}
elseif(getPARAMETRO("_elimina") && $operatore_flagamministratore==1 && empty($operatore_flagdirigente))
{
  // $sSQL="delete from rebuilding_flussofinanziario where idrebuilding_flussofinanziario='$pidrebuilding_flussofinanziario'";
  // $db->query($sSQL);
}
elseif(getPARAMETRO("_eliminaallegato") && $operatore_flagamministratore==1 && empty($operatore_flagdirigente))
{

  // $pidrebuilding_flussofinanziario_documento=getPARAMETRO("_allegato");
  // $pidrebuilding_flussofinanziario_documento=$db->escape_text($pidrebuilding_flussofinanziario_documento);

  // $sSQL="delete from rebuilding_flussofinanziario_documento where idrebuilding_flussofinanziario_documento='$pidrebuilding_flussofinanziario_documento'";
  // $db->query($sSQL);
} elseif(getPARAMETRO("_nuovo")) {

}


$incontroDAO=new rebuildingGruppiDiLavoroIncontri($pidincontro);

$disabled_scheda="";


?>
<!doctype html>
<html lang="it">
  <head>
  	 <?php echo getREBUILDINGHEAD(true); ?>

     <link rel="stylesheet" href="../librerie/css/bootstrap-select.css">


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

        <div class="toast-container d-flex justify-content-center align-items-center w-100">
          <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
              
              <strong class="me-auto">Riunione</strong>              
              <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close" onclick="closeMSG()"></button>
            </div>
            <div class="toast-body ">
              <span class="text-danger">E' obbligatorio indicare:<br> - Titolo<br> - Abstract<br> - Data<br></span>
            </div>
          </div>
        </div>

      

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
                        <input type="text" id="form_incontro_titolo" name="form_incontro_titolo" class="form-control form-control-flush" required placeholder="Inserire titolo riunione" value="<?php echo $incontroDAO->incontro_titolo;?>" <?php echo $disabled_scheda;?> >
                        <label for="form_incontro_titolo">Titolo</label>
                      </div> 
                    </div>  

                    <div class="col-12 col-md-10">
                      <div class=" form-floating fix-floating-label">
                      <textarea id="form_incontro_abstract" rows="5"  name="form_incontro_abstract" class="form-control form-control-flush" required placeholder="Inserire abstract riunione" <?php echo $disabled_scheda;?>   ><?php echo $incontroDAO->incontro_abstract;?></textarea> 
                      <label for="form_incontro_abstract">Ordine del giorno</label>
                      </div> 
                    </div>  

                    <div class="col-12 col-md-10">
                      <div class="form-floating">
                        <input type="date" id="form_incontro_giorno" name="form_incontro_giorno" class="form-control form-control-flush" required placeholder="Data riunione" value="<?php echo $incontroDAO->incontro_giorno;?>" <?php echo $disabled_scheda;?> >
                        <label for="form_incontro_giorno">Data riunione</label>
                      </div> 
                    </div>              
                  </div>      

    <?php
    $nuovariuione = true; // Variabile per simulare lo stato dell'utente
    ?>

<?php if (!getPARAMETRO("_nuovo")):   ?>



                  <div class="form-group row">
                    <div class="col-3 col-md-3">
                      <label class="form-label form-control-xs" for="fileConvocazione">Convocazione</label>
                    </div>
                    <div class="col-4 col-md-4">
                      
                      <input type="file" class="form-control form-control-text  form-control-xs" name="fileConvocazione" id="fileConvocazione" />
                    </div>    
                    <div class="col-4 col-md-4">
                      <input type="text" 
                          id="fileConvocazione_text" 
                          name="fileConvocazione_text" 
                          class="form-control form-control-text form-control-xs " 
                          disabled aria-disabled="true"
                          value="<?php echo $incontroDAO->incontro_file_agenda ?>">          
                    </div>
                  </div>      


                  <div class="form-group row">
                    <div class="col-3 col-md-3">
                      <label class="form-label  form-control-xs" for="fileVideoRiunione">Registrazione</label>
                    </div>
                    <div class="col-4 col-md-4">
                          <input type="file" class="form-control  form-control-text  form-control-xs" name="fileVideoRiunione" id="fileVideoRiunione">
                      </div> 
                    <div class="col-4 col-md-4">
                      <input type="text" 
                            id="fileVideoRiunione_text" 
                            name="fileVideoRiunione_text" 
                            class="form-control form-control-text form-control-xs " 
                            disabled aria-disabled="true"
                            value="<?php echo $incontroDAO->incontro_file_video ?>">          
                    </div>                                   
                  </div>   

                  <div class="form-group row">
                    <div class="col-3 col-md-3">
                      <label class="form-label  form-control-xs" for="fileVerbale">Resoconto</label>
                    </div>
                    <div class="col-4 col-md-4">
                        <input type="file" class="form-control  form-control-text  form-control-xs" name="fileVerbale" id="fileVerbale">
                    </div>   
                    <div class="col-4 col-md-4">
                      <input type="text" 
                            id="fileVerbale_text" 
                            name="fileVerbale_text" 
                            class="form-control form-control-text form-control-xs " 
                            disabled aria-disabled="true"
                            value="<?php echo $incontroDAO->incontro_file_verbale ?>">          
                    </div>                                
                  </div>      

                  <div class="form-group row">
                    <div class="col-3 col-md-3">
                      <label class="form-label form-control-xs" for="fileTrascrizione">Trascrizione</label>
                    </div>
                    <div class="col-4 col-md-4">
                        <input type="file" class="form-control  form-control-text  form-control-xs" name="fileTrascrizione" id="fileTrascrizione">
                    </div>       
                    <div class="col-4 col-md-4">
                      <input type="text" 
                            id="fileTrascrizione_text" 
                            name="fileTrascrizione_text" 
                            class="form-control form-control-text form-control-xs " 
                            disabled aria-disabled="true"
                            value="<?php echo $incontroDAO->incontro_file_trascrizione ?>">          
                    </div>                               
                  </div>  

                  <div class="form-group row">
                    <div class="col-3 col-md-3">
                      <label class="form-label form-control-xs" for="fileAltraDocumentazione">Altra documentazione (ZIP)</label>
                    </div>                    
                    <div class="col-4 col-md-4">
                        <input type="file" class="form-control  form-control-text  form-control-xs" name="fileAltraDocumentazione" id="fileAltraDocumentazione">
                    </div>     
                    <div class="col-4 col-md-4">
                      <input type="text" 
                            id="fileAltraDocumentazione_text" 
                            name="fileAltraDocumentazione_text" 
                            class="form-control form-control-text form-control-xs " 
                            disabled aria-disabled="true"
                            value="<?php echo $incontroDAO->incontro_file_altro_materiale ?>">          
                    </div>                                  
                  </div>                    


<?php endif; ?>



                  <div class="row">
                    <div class="col-10">
                      <button type="button" class="btn w-100 btn-primary-soft mt-3 lift" id="salva" name="salva" onclick="saveINCONTRO();"  <?php echo $disabled_scheda;?> >Salva</button>

                    </div>
                  </div>
                  <input type="hidden" name="_salva" id="_salva" value="true">
                  <input type="hidden" name="igl" id="igl" value="<?php echo $pfk_idrebuilding_gld ?>" >
                  <input type="hidden" name="idr" id="idr" value="<?php echo $pidincontro ?>" >
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

function saveINCONTRO()
{

    if($("#form_incontro_titolo").val()=='' || !$("#form_incontro_abstract").val() || !$("#form_incontro_giorno").val() )
    {
      $("#liveToast").show()
    }
    else  
      $("#INCONTRO_FORM").submit() 
 
}

function closeMSG()
{
  $("#liveToast").hide()
}




function doOnSubmit()
{
  $("#formENTE").val($("#formENTEselect").val())
  var counter_allegati=parseInt($("#counter_allegati").val());
  for(i=1;i<=counter_allegati;i++)
  {
    $("#allegatoENTE"+i).val($("#allegatoENTEselect"+i).val())
  }

}


</script>

</html>





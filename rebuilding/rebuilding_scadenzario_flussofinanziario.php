<?php

require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");
require_once("../librerie/dara.class.servizi.php");

//error_reporting(0);
//ini_set("display_errors",1);

$idoperatore=verificaUSER();

//$operatore=new DARAOperatore($idoperatore);
//$autorizzazioni_domanda=$operatore->getAUORIZZAZIONEdomanda();

$aENTI=array(1=>"ATS1 - Pesaro",3=>"ATS3 - C.M. Catria e Nerone",4=>"ATS4 - Urbino",5=>"ATS5 - C.M. Montefeltro",6=>"ATS6 - Fano",7=>"ATS7 - Fossombrone",8=>"ATS8 - Senigallia",9=>"ATS9 - ASP Ambito 9 Jesi",10=>"ATS10 - Fabriano",11=>"ATS11 - Ancona",12=>"ATS12 - Falconara Marittima",13=>"ATS13 - Osimo",14=>"ATS14 - Civitanova Marche",15=>"ATS15 - Macerata",16=>"ATS16 - C.M. Monti Azzurri",17=>"ATS17 - Unione montanta alta valle del potenza e dell'Esino",18=>"ATS18 - C.M. Camerino",19=>"ATS19 - Fermo",20=>"ATS20 - Porto Sant'Elpidio",21=>"ATS 21 - San Benedetto del Tronto",22=>"ATS22 - Ascoli Piceno",23=>"ATS23 - U.C. Vallata del Tronto",24=>"ATS24 - C.M. dei Sibillini");


$aTIPODOCUMENTO=array(1=>"RIPARTO",2=>"CRONOPROGRAMMA",3=>"MODULISTICA",4=>"ALTRO");

$operatore_flagamministratore=$db->getVALUE("select operatore_flagamministratore from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagamministratore");


$pidrebuilding_flussofinanziario=getPARAMETRO("_RENDICONTAZIONE");
$pidrebuilding_flussofinanziario=$db->escape_text($pidrebuilding_flussofinanziario);


$aSTATI=array(1=>"NON SCADUTA",2=>"SCADUTA");


?>
<!doctype html>
<html lang="it">
  <head>
  	 <?php echo getREBUILDINGHEAD(true); ?>

     <link rel="stylesheet" href="../librerie/css/bootstrap-select.css">

  </head>
  <body class="bg-light">

   
	<!-- ELENCO -->
    <section class="pt-6 pt-md-4 bg-light">
      <div class="col-10 offset-1 pb-10 pb-md-11 ">

        <div class="row">
          <div class="col-12">

            <div class="table-responsive mb-7 mb-md-9">

              <table class="table table-striped">
                <thead class="fs-6">
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Descrizione</th>
                    <th scope="col">Destinatari</th>                    
                    <th scope="col">Scade il</th>
                    <th scope="col">Stato</th>                    
                  </tr>
                </thead>
                <tbody id="post_data" class="fs-6">
                  <?php

                    $sWhere.=" rebuilding_scadenzario.idrebuilding_flussofinanziario='".$pidrebuilding_flussofinanziario."' ";


                    if(!empty($sWhere))
                      $sWhere=" WHERE ".$sWhere;

                    $scadenze=new rebuildingSCADENZARIO();
                    $aSCADENZE=$scadenze->getSCADENZE($sWhere);
                    
                    foreach ($aSCADENZE as $key => $aDATI) 
                    {
                      $idrebuilding_scadenzario=$aDATI["idrebuilding_scadenzario"];
                      $scadenza_datainserimento=$aDATI["scadenza_datainserimento"];
                      $scadenza_data=$aDATI["scadenza_data"];
                      $scadenza_ora=$aDATI["scadenza_ora"];
                      $scadenza_destinatario=$aDATI["scadenza_destinatario"];
                      $scadenza_testo=$aDATI["scadenza_testo"];
                      $scadenza_stato=$aDATI["scadenza_stato"];
                      $statoscadenza=$aSTATI[$scadenza_stato];
                      $idrebuilding_flussofinanziario=$aDATI["idrebuilding_flussofinanziario"];

                      $flussofinanziario=new rebuildingFLUSSOFINANZIARIO($idrebuilding_flussofinanziario); 

                      $scadenza_datainserimento=dataitaliana($scadenza_datainserimento);
                      $scadenza_data=dataitaliana($scadenza_data);
                      $aentiselezionati=explode(",",$aDATI["scadenza_destinatario"]);
                      $destinatari="";
                      foreach ($aentiselezionati as $key => $value) 
                      {

                        if($destinatari)
                          $destinatari.=", ";
                        $destinatari.=$aENTI[$value];
                      }

                      echo '<tr>';
                      echo '<th>'.$idrebuilding_scadenzario.'</th>';
                      echo '<th>'.$scadenza_testo.'</th>';
                      echo '<th>'.$destinatari.'</th>';
                      echo '<th>'.$scadenza_data.'</th>';
                      echo '<th>'.$statoscadenza.'</th>';
                      echo '</tr>';

                    }
                  ?>
                </tbody>
              </table>

              <div style="margin-top: 5%;" id="pagination_link"></div>

            </div>

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
</html>





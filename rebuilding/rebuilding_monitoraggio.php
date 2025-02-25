<?php

require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");
require_once("../librerie/dara.class.servizi.php");

//error_reporting(0);
//ini_set("display_errors",1);

$aENTI=array(1=>"ATS1 - Pesaro",3=>"ATS3 - C.M. Catria e Nerone",4=>"ATS4 - Urbino",5=>"ATS5 - C.M. Montefeltro",6=>"ATS6 - Fano",7=>"ATS7 - Fossombrone",8=>"ATS8 - Senigallia",9=>"ATS9 - ASP Ambito 9 Jesi",10=>"ATS10 - Fabriano",11=>"ATS11 - Ancona",12=>"ATS12 - Falconara Marittima",13=>"ATS13 - Osimo",14=>"ATS14 - Civitanova Marche",15=>"ATS15 - Macerata",16=>"ATS16 - C.M. Monti Azzurri",17=>"ATS17 - Unione montanta alta valle del potenza e dell'Esino",18=>"ATS18 - C.M. Camerino",19=>"ATS19 - Fermo",20=>"ATS20 - Porto Sant'Elpidio",21=>"ATS 21 - San Benedetto del Tronto",22=>"ATS22 - Ascoli Piceno",23=>"ATS23 - U.C. Vallata del Tronto",24=>"ATS24 - C.M. dei Sibillini");


$idoperatore=verificaUSER();

$operatore=new DARAOperatore($idoperatore);
$operatore_ente=$operatore->operatore_ente;
$centroterritorialeOPERATORE=$aENTI[$operatore_ente];

//$centroterritorialeOPERATORE=$db->getVALUE("select ")
//$autorizzazioni_domanda=$operatore->getAUORIZZAZIONEdomanda();

$aANNI=array(2017=>"2017",2018=>"2018",2019=>"2019",2020=>"2020",2021=>"2021",2022=>"2022",2023=>"2023",2024=>"2024",2025=>"2025",2026=>"2026", 2027=>"2027");

$aTIPOFONDO=array(1=>"Regionale",2=>"Statale",3=>"FSE",4=>"Misto","Sanitario");
$aTIPODOCUMENTO=array(1=>"RIPARTO",2=>"CRONOPROGRAMMA",3=>"MODULISTICA",4=>"ALTRO");
$aTIPOAREA=array(1=>"Famiglia e Minori",2=>"Anziani",3=>"Immigrati e nomadi",4=>"Dipendenze",5=>"Disabili",6=>"Povertà, disagio adulti e senza fissa dimora",7=>"Multiutenza");

$operatore_flagamministratore=$db->getVALUE("select operatore_flagamministratore from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagamministratore");
if(empty($operatore_ente) && !$operatore_flagamministratore)
  $operatore_ente=9999;

$operatore_flagrup=$db->getVALUE("select operatore_flagrup from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagrup");
$operatore_flagdirigente=$db->getVALUE("select operatore_flagdirigente from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagdirigente");

$operatori=new DARAOperatore(0);
$aRUP=$operatori->getOPERATORI('operatore_flagamministratore=1 and operatore_flagabilitato=1 and operatore_flagrup=1');

$pidrebuilding_flussofinanziario=getPARAMETRO("_RENDICONTAZIONE");
$pidrebuilding_flussofinanziario=$db->escape_text($pidrebuilding_flussofinanziario);


$prendicontazione_areaintervento=getPARAMETRO("rendicontazione_areaintervento");
$prendicontazione_areaintervento=$db->escape_text($prendicontazione_areaintervento);

$prendicontazione_tipofondo=getPARAMETRO("rendicontazione_tipofondo");
$prendicontazione_tipofondo=$db->escape_text($prendicontazione_tipofondo);

$prendicontazione_anno=getPARAMETRO("rendicontazione_anno");
$prendicontazione_anno=$db->escape_text($prendicontazione_anno);

$prendicontazione_ente=getPARAMETRO("rendicontazione_ente");
$prendicontazione_ente=$db->escape_text($prendicontazione_ente);

if(empty($operatore_flagamministratore))
{
  $prendicontazione_ente=$operatore_ente;
}

$pflussofinanziario_rup=getPARAMETRO("flussofinanziario_rup");
$pflussofinanziario_rup=$db->escape_text($pflussofinanziario_rup);



$aSTATI=array(1=>"NO",2=>"SI");

?>
<!doctype html>
<html lang="it">
  <head>
     <?php echo getREBUILDINGHEAD(true); ?>

     <link rel="stylesheet" href="../librerie/css/bootstrap-select.css">

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

    $aBREADCUMB[2]["titolo_pagina"]="Flussi di finanziamento";
    $aBREADCUMB[2]["url"]="toolkit";

    $aBREADCUMB[3]["titolo_pagina"]="Analisi/monitoraggio";
    $aBREADCUMB[3]["url"]="";

    generaBREADCUMB($aBREADCUMB);

    ?>

    <!-- Modal -->
    <div class="modal fade" id="modalSCADENZE" tabindex="-1" role="dialog" aria-labelledby="modalSCADENZELabel" aria-hidden="true">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalSCADENZELabel">Scadenze flusso di finanziamento</h5>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-12">

                          <div class="table-responsive mb-7 mb-md-9">

                            <table class="table table-striped" id="tblSCADENZE" name="tblSCADENZE">
                              <thead class="fs-6">
                                <tr>
                                  <th scope="col" style="width: 80%">Descrizione</th>
                                  <th scope="col" style="width: 20%">Data scadenza</th>
                                </tr>
                              </thead>
                              <tbody id="datiSCADENZE" class="fs-6">     
                              </tbody>
                          </table>
                         </div>
                </div>                       
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-xs btn-secondary" onclick="closeSCADENZE()">Chiudi</button>
            
          </div>
        </div>
      </div>
    </div>


    <!-- RICERCA -->
    <section class="py-6 bg-light">
      <div class="col-10 offset-1">
        <div class="row">
          <div class="col-6">
              <p align="left"><?php if(!$operatore_flagamministratore) echo '<div class="badge bg-secondary-soft">PROFILO ATS '.$centroterritorialeOPERATORE.'</div>'; else echo '<div class="badge bg-secondary-soft">PROFILO REGIONALE</div>';?></p>  

          </div>    
          <div class="col-6">    
              <p align="right">
                  <button type="button" class="btn btn-sm btn-primary-soft  btn-xs" onclick="loadDATA();"><i class="fe fe-search"></i>&nbsp;Ricerca</button>
              </p>
          </div>  
        </div> 
        <div class="row">
          <div class="col-12">

            <form class="rounded shadow" method="post" action="monitoraggio" id="formRENDICONTAZIONE" name="formRENDICONTAZIONE" >

              <div class="input-group input-group-lg" style="background-color: #FFFFFF;">

                <div class="form-floating col-sm-4 p-2">
                  <select id="rendicontazione_areaintervento" name="rendicontazione_areaintervento" class="form-select" required >
                    <option value='0'>Tutti</option>
                    <?php
                      
                      
                      foreach ($aTIPOAREA as $key => $descrizione) 
                      {
                        if($key==$prendicontazione_areaintervento)
                          echo '<option value="'.$key.'" selected>'.$descrizione.'</option>';
                        else
                          echo '<option value="'.$key.'">'.$descrizione.'</option>';                        
                      }
                    ?>  
                  </select>  
                  <label for="dara_avviso">Area/target</label>
                </div>   

                <div class="form-floating col-sm-3 p-2">
                  <select id="rendicontazione_anno" name="rendicontazione_anno" class="form-select" required>
                    <option value='0'>Tutti</option>
                    <?php
                      
                      
                      foreach ($aANNI as $key => $descrizione) 
                      {
                        if($key==$prendicontazione_anno)
                          echo '<option value="'.$key.'" selected>'.$descrizione.'</option>';
                        else
                          echo '<option value="'.$key.'">'.$descrizione.'</option>';
                        
                      }
                    ?>                                 
                  </select>
                  <label for="rendicontazione_anno">Anno</label>
                </div>

                <div class="form-floating col-sm-5 p-2">
                  <select id="rendicontazione_tipofondo" name="rendicontazione_tipofondo" class="form-select" required >
                    <option value='0'>Tutti</option>
                    <?php
                      
                      
                      foreach ($aTIPOFONDO as $key => $descrizione) 
                      {
                        if($key==$prendicontazione_tipofondo)
                          echo '<option value="'.$key.'" selected>'.$descrizione.'</option>';
                        else
                          echo '<option value="'.$key.'">'.$descrizione.'</option>';                        
                      }
                    ?>  
                  </select>  
                  <label for="dara_avviso">Tipo di fondo</label>
                </div>      
                               
             </div>


              <div class="input-group input-group-lg p-2" style="background-color: #FFFFFF; <?php if(!$operatore_flagamministratore) echo 'display:none'; ?> ">


                <div class="form-floating col-sm-4 p-2">
                  
                        <select id="flussofinanziario_rup" name="flussofinanziario_rup" class="form-select">
                          <option value='0'>Tutti</option>
                          <?php
                                            
                            foreach ($aRUP as $key => $aDATI) 
                            {
                              if($aDATI['iddara_operatore']==$pflussofinanziario_rup)
                                echo '<option value="'.$aDATI['iddara_operatore'].'" selected>'.$aDATI['operatore_cognome'].' '.$aDATI['operatore_nome'].'</option>';                    
                              else
                                echo '<option value="'.$aDATI['iddara_operatore'].'">'.$aDATI['operatore_cognome'].' '.$aDATI['operatore_nome'].'</option>';                    
                            }
                          ?>                                  
                        </select>    

                        <label for="flussofinanziario_rup">RUP</label>
                  
                </div>

                <div class="form-floating col-sm-4 p-2" style="display: none">
                  <input type="text" oninput="let p=this.selectionStart;this.value=this.value.toUpperCase();this.setSelectionRange(p, p);" class="form-control" id="rendicontazione_testo" name="rendicontazione_testo" placeholder="Testo" value="<?php echo $prendicontazione_testo; ?>">
                  <label for="rendicontazione_testo">Testo</label>
                </div>


                <div class="form-floating col-sm-1 p-1">
                  <span id="n_result" class="h6 text-uppercase text-muted d-none d-md-block mb-0 me-5" style="text-align: right;"></span>
            </div>


             </div>
              </div>
                <input type="hidden" name="_ricerca" id="_ricerca" value="true">
            </form>

          </div>
        </div>
      </div>
    </section>
   
  <!-- ELENCO -->
    <section class="pt-6 pt-md-4 bg-light">
      <div class="col-10 offset-1 pb-10 pb-md-11 ">

        <div class="row">
          <div class="col-12">

            <div class="table-responsive mb-7 mb-md-9">

              <table class="table table-striped" id="tblREPORT" name="tblREPORT">
                <thead class="fs-6">
                  <tr>
                    <th scope="col" style="width: 5%">#</th>
                    <th scope="col" style="width: 5%">Anno</th>
                    <th scope="col" style="width: 10%">Tipo fondo</th>
                    <th scope="col" style="width: 20%">DGR riferimento</th>
                    <th scope="col" style="width: 20%">Titolo</th>                    
                    <th scope="col" style="width: 10%">Risorse</th>
                    <th scope="col" style="width: 10%">Spesa</th>
                    <th scope="col" style="width: 10%">% Spesa</th>
                    <th scope="col" style="width: 5%">Beneficiari</th>
                    <th scope="col" style="width: 10%">Spesa media</th>
                  </tr>
                </thead>
                <tbody id="post_data" class="fs-6">
                  <?php

                    $sWhere="";


                    if(!empty($prendicontazione_tipofondo))
                    {
                      if(!empty($sWhere))
                        $sWhere.=" and ";
                      $sWhere.=" flussofinanziario_tipofondo='".$prendicontazione_tipofondo."' ";
                    }

                    if(!empty($prendicontazione_anno))
                    {
                      if(!empty($sWhere))
                        $sWhere.=" and ";
                      $sWhere.=" flussofinanziario_anno='".$prendicontazione_anno."' ";
                    }

                    if(!empty($pflussofinanziario_rup))
                    {
                      if(!empty($sWhere))
                        $sWhere.=" and ";
                      $sWhere.=" flussofinanziario_rup='".$pflussofinanziario_rup."' ";
                    }

                    if(!empty($prendicontazione_areaintervento))
                    {
                      if(!empty($sWhere))
                        $sWhere.=" and ";
                      $sWhere.=" flussofinanziario_areaintervento='".$prendicontazione_areaintervento."' ";
                    }

                    if(!empty($sWhere))
                      $sWhere=" WHERE ".$sWhere;

                    $flussi=new rebuildingFLUSSOFINANZIARIO();
                    if(empty($operatore_flagamministratore))
                    {
                      if(!empty($sWhere))
                        $sWhere.=" and ";
                      else
                        $sWhere=" where ";

                      $sWhere.=" flussofinanziario_stato='2' ";

                      if(!empty($sWhere))
                        $sWhere.=" and ";
                      else
                        $sWhere=" where ";
                      $sWhere.=" rebuilding_flussofinanziario_ente.flussofinanziario_ente='".$operatore->operatore_ente."'";
                      $aFLUSSI=$flussi->getFLUSSIENTE($sWhere);
                    }
                    else
                    {

                      if(!empty($sWhere))
                        $sWhere.=" and ";
                      else
                        $sWhere=" where ";

                      $sWhere.=" flussofinanziario_stato='2' ";

                      $aFLUSSI=$flussi->getFLUSSI($sWhere);
                    }
                    

                    foreach ($aFLUSSI as $key => $aDATI) 
                    {
                      $idrebuilding_flussofinanziario=$aDATI["idrebuilding_flussofinanziario"];
                      $flussofinanziario_anno=$aDATI["flussofinanziario_anno"];
                      $flussofinanziario_ente=$aDATI["flussofinanziario_ente"];
                      $flussofinanziario_tipofondo=$aDATI["flussofinanziario_tipofondo"];
                      $flussofinanziario_areaintervento=$aDATI["flussofinanziario_areaintervento"];
                      $flussofinanziario_leggeriferimento=$aDATI["flussofinanziario_leggeriferimento"];
                      $flussofinanziario_titolo=$aDATI["flussofinanziario_titolo"];
                      $flussofinanziario_testo=$aDATI["flussofinanziario_testo"];
                      $flussofinanziario_rup=$aDATI["flussofinanziario_rup"];
                      $flussofinanziario_operatore=$aDATI["flussofinanziario_operatore"];
                      $flussofinanziario_stato=$aDATI["flussofinanziario_stato"];

                      $operatore=new DARAOperatore($flussofinanziario_operatore);
                      $operatore_nominativo=$operatore->operatore_cognome.' '.$operatore->operatore_nome;
                      $operatore_nominativo=addslashes($operatore_nominativo);

                      $flussofinanziario=new rebuildingFLUSSOFINANZIARIO($idrebuilding_flussofinanziario);
                      $flussofinanziario_risorse=$flussofinanziario->getTOTALERISORSE();
                      $flussofinanziario_spesa=$flussofinanziario->getTOTALESPESA();
                      $flussofinanziario_beneficiari=$flussofinanziario->getTOTALEBENEFICIARI();
                      $flussofinanziario_percentualespesa=@round(100-(($flussofinanziario_risorse-$flussofinanziario_spesa)/$flussofinanziario_risorse*100),2);

                      if($flussofinanziario_beneficiari>0)
                        $flussofinanziario_spesamedia=@($flussofinanziario_spesa/$flussofinanziario_beneficiari);
                      else
                        $flussofinanziario_spesamedia=0;

                      echo '<tr>';
                      echo '<th>'.$idrebuilding_flussofinanziario.'</th>';
                      echo '<th>'.$flussofinanziario_anno.'</th>';
                      echo '<th>'.$aTIPOFONDO[$flussofinanziario_tipofondo].'</th>';
                      echo '<th>'.$flussofinanziario_leggeriferimento.'</th>';
                      echo '<th>'.$flussofinanziario_titolo.'</th>';  
                      echo '<th>'.number_format($flussofinanziario_risorse,2,",",".").'</th>';                                         
                      echo '<th>'.number_format($flussofinanziario_spesa,2,",",".").'</th>';                                         
                      echo '<th>'.number_format($flussofinanziario_percentualespesa,2,",",".").'</th>';                                         
                      echo '<th>'.number_format($flussofinanziario_beneficiari).'</th>';                                         
                      echo '<th>'.number_format($flussofinanziario_spesamedia,2,",",".").'</th>';                                         
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



<script>

function loadDATA()
{
  $("#formRENDICONTAZIONE").submit()
}

</script>

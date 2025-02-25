<?php


require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");
require_once("../librerie/dara.class.servizi.php");


$idoperatore=verificaUSER();

$pidrebuilding_tipologiaspesa=getPARAMETRO("_k");
$pidrebuilding_tipologiaspesa=$db->escape_text($pidrebuilding_tipologiaspesa);


$ptipologiaspesa_descrizione=getPARAMETRO("tipologiaspesa_descrizione");
$ptipologiaspesa_descrizione=$db->escape_text($ptipologiaspesa_descrizione);


if(getPARAMETRO("_elimina"))
{
  //$sSQL="update from rebuilding_tipologiaspesa set tipologiaspesa_flagelimina=1 where idrebuilding_tipologiaspesa='$pidrebuilding_tipologiaspesa'";
  $sSQL="delete from rebuilding_tipologiaspesa where idrebuilding_tipologiaspesa='$pidrebuilding_tipologiaspesa'";
  $db->query($sSQL);
}

?>
<!doctype html>
<html lang="it">
  <head>
  	<?php echo getREBUILDINGHEAD(true); ?>
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

      $aBREADCUMB[3]["titolo_pagina"]="Tipologia spesa";
      $aBREADCUMB[3]["url"]="";

      generaBREADCUMB($aBREADCUMB);
    ?> 

    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body">
            <div class="form-group">
                <label for="modalDESCRIZIONE">Descrizione tipologia spesa*</label>
                <input type="text" class="form-control  border-2 px-2" id="modalDESCRIZIONE" name="modalDESCRIZIONE" maxlength="200" value="" >
            </div>  
          </div>
          <div class="modal-footer">
            <input type="hidden" id="modalKEY" name="modalKEY" value="">
            <button type="button" class="btn btn-success" onclick="saveTIPOLOGIA()">Salva</button>
            <button type="button" class="btn btn-secondary" onclick="closeTIPOLOGIA()">Chiudi</button>
          </div>
        </div>
      </div>
    </div>

    <!-- SEARCH -->
    <section class="py-6 bg-light">
      <div class="col-10 offset-1">

        <div class="row">
          <div class="col-12">
              <p align="right">
                  <button type="button" class="btn btn-primary-soft btn-xs" onclick="apriTIPOLOGIA('0','')"><i class="fe fe-plus"></i>&nbsp;Tipologia spesa</button>                  
              </p>
          </div>  
        </div> 


        <div class="row">

          <div class="col-12">

            <!-- Form -->
            <form class="rounded shadow">
              <div class="input-group input-group-lg">

                <span class="input-group-text border-0 pe-1">
                  <i class="fe fe-search"></i>
                </span>

                <input type="text" class="form-control border-0 px-1" id="tipologiaspesa_descrizione" name="tipologiaspesa_descrizione" aria-label="Ricerca le tipologie ..." placeholder="Inserisci la descrizione" value="<?php echo $ptipologiaspesa_descrizione;?>">

                <span class="input-group-text border-0 py-0 ps-1 pe-3">

                  <span id="n_result" class="h6 text-uppercase text-muted d-none d-md-block mb-0 me-5">
                  </span>

                  <button type="submit" class="btn btn-sm btn-primary" ><i class="fe fe-search"></i>&nbsp;Ricerca</button>

                </span>
              </div>
            </form>

          </div>
        </div> <!-- / .row -->
      </div>
    </section>


    <!-- APPLYING -->
    <section class="pt-6 pt-md-4 bg-light">
      <div class="col-10 offset-1 pb-8 pb-md-11 ">
        <div class="row">
          <div class="col-12">

          	<div class="table-responsive mb-7 mb-md-9">

              <table class="table table-striped">
                <thead class="fs-6">
                  <tr>
                    <th scope="col" style="width: 5%">#</th>
                    <th scope="col" style="width: 85%">Tipologia di spesa</th>
                    <th scope="col" style="width: 10%"></th>
                  </tr>
                </thead>
                <tbody id="post_data" class="fs-6">
                    <?php
                      $sWhere=" where tipologiaspesa_flagelimina=0 ";
                      if(!empty($ptipologiaspesa_descrizione))
                      {
                        $sWhere.=" and tipologiaspesa_descrizione like '%".$ptipologiaspesa_descrizione."%'";
                      }

                      $sSQL="select * from rebuilding_tipologiaspesa ".$sWhere." order by tipologiaspesa_descrizione";
                      $aTIPOLOGIE=$db->select($sSQL);
                      //print_r($aTIPOLOGIE);
                      foreach ($aTIPOLOGIE as $key => $aDATA) 
                      {

                        $confirm='<a href=\'#\'><span class=\'badge bg-primary-soft\'>NO</span></a>&nbsp;<a href=\'rebuilding_toolkit_tipologiaspesa.php?_k='.$aDATA["idrebuilding_tipologiaspesa"].'&_elimina=true\'><span class=\'badge bg-primary-soft\' >SI</span></a>';

                        echo "<tr id='".$aDATA["idrebuilding_tipologiaspesa"]."'>";
                        echo "<td>".$aDATA["idrebuilding_tipologiaspesa"]."</td>";
                        echo "<td>".$aDATA["tipologiaspesa_descrizione"]."</td>";
                        echo "<td>";
                        echo '<button id="edittipo" name="edittipo" class="btn btn-xs btn-rounded-circle btn-primary" onclick="apriTIPOLOGIA(\''.$aDATA["idrebuilding_tipologiaspesa"].'\',\''.addslashes($aDATA["tipologiaspesa_descrizione"]).'\')" ><i class="fe fe-check"></i></button>
                              <button id="deletetipo'.$aDATA["idrebuilding_tipologiaspesa"].'" name="deletetipo'.$aDATA["idrebuilding_tipologiaspesa"].'" class="btn btn-xs btn-rounded-circle btn-danger" data-bs-container="body" data-bs-toggle="popover" data-bs-placement="left" title="Confermi l\'eliminazione?" data-bs-content="'.$confirm.'" data-bs-html=true data-bs-trigger="focus"><i class="fe fe-x"></i></button>';
                        echo "</td>";
                        echo "</tr>";
                        
                      }
                    ?>
                </tbody>
              </table>

              <div style="margin-top: 5%;" id="pagination_link"></div>

            </div>

          </div>
        </div> <!-- / .row -->

      </div> <!-- / .container -->
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
<script>

function apriTIPOLOGIA(myKEY,myDESCRIZIONE)
{

//  var params = {};
//  params['_k'] = key;
  $("#modalKEY").val(myKEY)
  $("#modalDESCRIZIONE").val(myDESCRIZIONE)
  $("#exampleModalCenter").modal('show');
  //postForm('autorizzazione-operatore', params);
}

function closeTIPOLOGIA()
{
  $("#exampleModalCenter").modal('hide');
}

function saveTIPOLOGIA()
{
    myDESCRIZIONE=$("#modalDESCRIZIONE").val()
    myKEY=$("#modalKEY").val()
    var page="rebuilding_action.php";
    var params="_action=saveTIPOLOGIASPESA&_value="+myDESCRIZIONE+"&_k="+myKEY;
    $.ajax({
      type: "POST",
      url: page,
      data: params, 
      dataType: "html",
      success: function(result)
      {
                
        window.location.reload();
        
      },
      error: function()
      {
        alert("ERRORE")
        console.log("Chiamata fallita, si prega di riprovare...");
      }
    });  
}
</script>

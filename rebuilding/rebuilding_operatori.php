<?php


require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");
require_once("../librerie/dara.class.servizi.php");


$idoperatore=verificaUSER();


/*
if ($validuser)
{
	$operatore=new DARAOperatore($user);
	$autorizzazioni_avviso=$operatore->getAUORIZZAZIONEAVVISO();
	
}
*/

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

      $aBREADCUMB[1]["titolo_pagina"]="Autorizzazioni";
      $aBREADCUMB[1]["url"]="";

      generaBREADCUMB($aBREADCUMB);
    ?> 

    <!-- SEARCH -->
    <section class="py-6 bg-light">
      <div class="col-10 offset-1">

        <div class="row">
          <div class="col-12">
              <p align="right">
                  <button type="button" class="btn btn-primary-soft btn-xs" onclick="apriOPERATORE('0')"><i class="fe fe-plus"></i>&nbsp;Operatore</button>                  
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

                <input type="text" class="form-control border-0 px-1" id="operatore_nominativo" name="operatore_nominativo" aria-label="Ricerca gli operatori ..." placeholder="Inserisci il nominativo">

                <span class="input-group-text border-0 py-0 ps-1 pe-3">

                  <span id="n_result" class="h6 text-uppercase text-muted d-none d-md-block mb-0 me-5">
                  </span>

                  <button type="button" class="btn btn-sm btn-primary" onclick="load_data();"><i class="fe fe-search"></i>&nbsp;Ricerca</button>

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
                    <th scope="col" style="width: 60%">Cognome e Nome</th>
                    <th scope="col" style="width: 15%">Codice Fiscale</th>
                    <th scope="col" style="width: 15%">e-mail</th>
                    <th scope="col" style="width: 5%">Abilitato</th>
                  </tr>
                </thead>
                <tbody id="post_data" class="fs-6">

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

load_data();

function load_data(page = 1)
{
  //$("#div_spinner").show();
  //$("#page_anagrafica").hide();


  $('#post_data').html('');
  $('#pagination_link').html('');

  var myObj = {};
  myObj["operatore_nominativo"] = $("#operatore_nominativo").val();

  var query = JSON.stringify(myObj);

  //var query='<?php echo $jsonQUERY; ?>';

  $.ajax({
    type: "POST",
    url: "rebuilding_action.php",
    data: "_action=loadoperatori&query="+query+"&page="+page, 
    dataType: "json",
    success: function(response)
    {
      //var response = JSON.parse(result);

      var html = '';

      var serial_no = 1;

      if(response.data.length > 0)
      {
        for(var count = 0; count < response.data.length; count++)
        {
	        html += '<tr id="tr_'+response.data[count].iddara_operatore+'" onclick="apriOPERATORE('+response.data[count].iddara_operatore+')">';
	            html += '<td>'+response.data[count].counter+'</td>';
	            html += '<td>'+response.data[count].operatore_cognome+' '+response.data[count].operatore_nome+'</td>';
	            html += '<td>'+response.data[count].operatore_codicefiscale+'</td>';
	            html += '<td>'+response.data[count].operatore_email+'</td>';

	            if (!isEmpty(response.data[count].operatore_flagabilitato))
					var abilitato = '<span class="badge bg-success">SI</span>';
				else
					var abilitato = '<span class="badge bg-danger">NO</span>';
	           
	            html += '<td>'+abilitato+'</td>';

          html += '</tr>';

          serial_no++;
        }
      }
      else
      {
        html += '<tr><td colspan="5" class="text-center">Nessun dato trovato</td></tr>';
      }

      $('#post_data').html(html);
      $('#total_data').html(response.total_data);
      $('#pagination_link').html(response.pagination);

      $('#n_result').html(response.total_data+" risultati");

        window.scrollTo({ top: 0, behavior: 'auto' });

      //$("#div_spinner").hide();
      //$("#page_anagrafica").show();

    },
    error: function()
    {
      console.log("Chiamata fallita, si prega di riprovare...");

      //$("#div_spinner").hide();
      //$("#page_anagrafica").show();
    }
  });
}

function apriOPERATORE(key)
{
  var params = {};
  params['_k'] = key;

  postForm('autorizzazione-operatore', params);
}
</script>

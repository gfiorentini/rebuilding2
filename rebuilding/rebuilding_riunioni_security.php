<?php


require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");
require_once("../librerie/dara.class.servizi.php");


$idoperatore=verificaUSER();


// Indica l'ID del gruppo di lavoro
$igl= -1;

// Controlla se ci sono parametri nella query string
if (isset($_GET['igl']) ) {
  $igl = intval( $_GET['igl'] );
} else {
  die("Errore: gruppi id gruppo tematico errato errati.");
}


$gcurrentgruppo =  $db->select("select * from rebuilding_gruppi_di_lavoro where idrebuilding_gld=$igl ") ;


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


    <style>
    /* Change background color when checked */
    .form-switch .form-check-input:checked {
      background-color: #28a745 !important; /* Green */
      border-color: #28a745 !important;   /* Green border */
    }

    /* Change knob color */
    .form-switch .form-check-input:checked::before {
      background-color: #ffffff !important; /* White knob */
    }

    /* Add hover effect */
    .form-switch .form-check-input:hover {
      background-color: #218838 !important; /* Darker green on hover */
    }

    .form-check {
      display: flex;
      justify-content: center;
      align-items: center;
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

      $aBREADCUMB[1]["titolo_pagina"]=$gcurrentgruppo[0]["gdl_titolo"];
      $aBREADCUMB[1]["url"]="rebuilding_riunioni_list_giornate?igl=$igl ";

      $aBREADCUMB[2]["titolo_pagina"]="Autorizzazioni";
      $aBREADCUMB[2]["url"]="";

      generaBREADCUMB($aBREADCUMB);
    ?> 

    <!-- SEARCH -->
    <section class="py-6 bg-light">
      <div class="col-10 offset-1">

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

              <table class="table  table-hover">
                <thead class="fs-4">
                  <tr>
                    <th scope="col" style="width: 5%">#</th>
                    <th scope="col" style="width: 50%">Cognome e Nome</th>
                    <th scope="col" style="width: 15%">Codice Fiscale</th>
                    <th scope="col" style="width: 15%">e-mail</th>
                    <th scope="col" class="text-center" style="width: 5%;">Abilitato</th>
                    <th scope="col" class="text-center" style="width: 5%">Visualizza</th>
                    <th scope="col" class="text-center" style="width: 5%">Gestione</th>
                  </tr>
                </thead>
                <tbody id="post_data" class="fs-4">

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

// SET global 
var igl = <?php echo $igl ?>; 

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

  

  $.ajax({
    type: "POST",
    url: "rebuilding_action.php",
    data: "_action=loadoperatoriPerGruppoDiLavoro&query="+query+"&page="+page+"&igl="+igl, 
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
	        html += '<tr id="tr_'+response.data[count].iddara_operatore+'" ">';
	            html += '<td>'+response.data[count].counter+'</td>';
	            html += '<td>'+response.data[count].operatore_cognome+' '+response.data[count].operatore_nome+'</td>';
	            html += '<td>'+response.data[count].operatore_codicefiscale+'</td>';
	            html += '<td>'+response.data[count].operatore_email+'</td>';

              let viewSelected = '';
              let editSelected = '';
              
              if (response.data[count].userCanView=="1") {
                viewSelected = 'checked';
              }
              if (response.data[count].userCanEdit=="1") {
                editSelected = 'checked';
              }

	            if (!isEmpty(response.data[count].operatore_flagabilitato))
					      var abilitato = '<span class="badge bg-success">SI</span>';
				      else
					      var abilitato = '<span class="badge bg-danger">NO</span>';
	           
	            html += '<td class="text-center" >'+abilitato+'</td>';

              html += `<td  ><div class="form-check form-switch"><input class="checkboxView form-check-input" type="checkbox" id='canView_${count}' name='canView_${count}' ${viewSelected} value="${response.data[count].operatore_codicefiscale}" ></div></td>`;

              html += `<td  ><div class="form-check form-switch "><input class="checkboxEdit form-check-input" type="checkbox" id='canEdit_${count}' name='canEdit_${count}' ${editSelected} value="${response.data[count].operatore_codicefiscale}" ></div></td>`;

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



// Initialize jQuery and register event handlers
$(document).ready(function () {

    console.log ( ' igl= ' , igl );

    function notify_server ( pigl, cf, canView, canEdit ) {
      $.ajax({
          type: "POST",
          url: "rebuilding_action.php",
          data: `_action=gruppiDiLavoroSetSecurity&igl=${pigl}&cf=${cf}&canView=${canView}&canEdit=${canEdit}`, 
          dataType: "json",
          success: function(response)
          {
            console.log(response);
          },
          error: function()
          {
            console.log("Chiamata gruppiDiLavoroSetSecurity fallita, si prega di riprovare...");

            //$("#div_spinner").hide();
            //$("#page_anagrafica").show();
          }
        });
    }

    // Attach a change event to all elements with the class 'checkboxView'
    $(document).on('change', '.checkboxView', function() {
      // Check if the element is a checkbox and whether it is checked
      let cf=$(this).val();
      if ($(this).is(':checkbox')) {
        console.log(`Checkbox with value "${$(this).val()}" is ${$(this).is(':checked') ? 'checked' : 'unchecked'}.`);
        let checked = $(this).is(':checked') ? 1 : 0 ;
        notify_server( igl, cf, checked, '' );
      } else {
        console.log(`Value changed to: ${$(this).val()}`);
      }
    });

    // Attach a change event to all elements with the class 'checkboxEdit'
    $(document).on('change', '.checkboxEdit', function() {
      // Check if the element is a checkbox and whether it is checked
      let cf=$(this).val();
      if ($(this).is(':checkbox')) {
        console.log(`Checkbox with value "${$(this).val()}" is ${$(this).is(':checked') ? 'checked' : 'unchecked'}.`);
        let checked = $(this).is(':checked') ? 1 : 0 ;
        notify_server( igl, cf, '', checked );
      } else {
        console.log(`Value changed to: ${$(this).val()}`);
      }
    });

      // // Example: Click event handler for a button
      // $("#myButton").click(function () {
      //   alert("Button clicked!");
      // });

      // // Example: Change event handler for an input field
      // $(".myInput").on("change", function () {
      //   console.log("Input value changed to:", $(this).val());
      // });
});



</script>

<?php 

/*
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
*/
date_default_timezone_set('Europe/Rome');

require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");

session_destroy();
session_unset();

$psession=getPARAMETRO("_s");
$pattivo=getPARAMETRO("_attivo");

$pidgen_procedura=9;
?>
<!doctype html>
<html lang="it">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
        
    <link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.css" />
    <link rel="stylesheet" href="../librerie/assets/css/libs.bundle.css" />
    <link rel="stylesheet" href="../librerie/assets/css/theme.bundle.css" />

	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

    <!--link type="text/css" rel="stylesheet" href="../librerie/css/spid/spid-sp-access-button.min.css" />
    <script type="text/javascript" src="../librerie/js/spid/spid-sp-access-button.min.js"></script-->

	<link type="text/css" rel="stylesheet" href="../librerie/css/spid/spid-sp-access-button.min.css" />
	<script type="text/javascript" src="../librerie/js/spid/spid-idps.js"></script>
	<script type="text/javascript" src="../librerie/js/spid/spid-sp-access-button.min.js"></script>

	<link rel="stylesheet" type="text/css" href="../librerie/css/bootstrap-dialog.min.css">
    <script language="javascript" type="text/javascript" src="../librerie/js/bootstrap-dialog.min.js"></script>   

    <title>Rebuilding</title>
  </head>
  <body>

    <!-- NAVBAR -->
	<?php echo getWNAVBARNOLOGIN(); ?>

	<form id="formLOGIN" method="post" class="row g-3 needs-validation" novalidate>
		<section>
			<div class="container d-flex flex-column">
				<div class="row align-items-center justify-content-center gx-0 min-vh-100">
					<div class="col-12 col-md-6 col-lg-4 py-8 py-md-11">

						<?php 
						
						if(!empty($psession))
							echo printALERT("ALERT_WARNING",'La sessione è scaduta! Ripetere il login.',true);
							
						if(!empty($pattivo))
							echo printALERT("ALERT_WARNING",'Operatore non attivo',true);
						?>

						<p>La piattaforma SOS, Sistema <strong>Sapiens</strong> per gli Operatori dei Servizi <strong>Sociali</strong>, <strong>è una iniziativa della Regione Marche</strong>, Direzione Politiche Sociali, <strong>di affiancamento, di aggiornamento professionale e di sviluppo di strumenti operativi (Toolkit) rivolto a tutti gli Ambiti Territoriali Sociali regionali
						<br>Progetto: Interventi di Capacity Building finanziato con i fondi PON “Inclusione” Fondi Sociali Europei - FSE 2014/2020</strong></p>
						
						<div class="col-md-10">
							<a href="#" class="italia-it-button italia-it-button-size-m button-spid" spid-idp-button="#spid-idp-button-medium-get" aria-haspopup="true" aria-expanded="false">
								<span class="italia-it-button-icon"><img src="../images/spid/spid-ico-circle-bb.svg" onerror='this.src="../images/spid/spid-ico-circle-bb.png"; this.onerror=null;' alt="" /></span>
								<span class="italia-it-button-text">Entra con SPID</span>
							</a>
							<div id="spid-idp-button-medium-get" class="spid-idp-button spid-idp-button-tip spid-idp-button-relative" >
								<ul id="spid-idp-list-medium-root-get" class="spid-idp-button-menu" data-spid-remote aria-labelledby="spid-idp">
									<li style="padding-right:5%; padding-left:5%;"><a class="" href="https://www.spid.gov.it">Maggiori informazioni</a></li>
									<li style="padding-right:5%; padding-left:5%;"><a class="" href="https://www.spid.gov.it/richiedi-spid">Non hai SPID?</a></li>
									<li style="padding-right:5%; padding-left:5%;"><a class="" href="https://www.spid.gov.it/serve-aiuto">Serve aiuto?</a></li>
								</ul>
							</div>
							<br>
							<br>
							<ul style="color: #97989b; text-transform: uppercase; font-size: 12px">
								SPID è il sistema di accesso che consente di utilizzare, con un\'identità digitale unica, i servizi online della Pubblica Amministrazione e dei privati accreditati. Se sei già in possesso di un\'identità digitale, accedi con le credenziali del tuo gestore. Se non hai ancora un\'indentità digitale, richiedila ad uno dei gestori. PER LE INFORMAZIONI SUL SISTEMA SPID <a href="https://www.spid.gov.it"><b>CLICCA QUI</b></a>
							</ul>	
							<br><br>
						</div>
						
					</div>

					<div class="col-lg-7 offset-lg-1 align-self-stretch d-none d-lg-block">
						<div class="h-100 w-cover bg-cover" style="background-image: url(../librerie/assets/img/rebuilding.jpg);"></div>
						<div class="shape shape-start shape-fluid-y text-white">
						<svg viewBox="0 0 100 1544" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h100v386l-50 772v386H0V0z" fill="currentColor"/></svg>
					</div>

				</div>
				</div>
			</div>
		</section>

        <!-- Text -->


	</form>

	<?php echo getWFOOTERNOLOGIN(); ?>

    <script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
    <script src="../librerie/assets/js/vendor.bundle.js"></script>
    <script src="../librerie/assets/js/theme.bundle.js"></script>

  </body>
</html>
<script>

(function () {
  'use strict'

  var forms = document.querySelectorAll('.needs-validation')

  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        else
        {
          	event.preventDefault()
        	//checkLOGIN();
        }

        form.classList.add('was-validated')
      }, false)
    })
})()


function checkLOGIN()
{
	$(".alert").hide();

	myUSER=$("#username").val()
	myPWD=$("#password").val()

	var sPAGE="./rebuilding_action.php";
	var sPARAMS="_action=rlogin&_u="+myUSER+"&_p="+myPWD;
	$.ajax({
		type: "POST",
		url: sPAGE,
		data: sPARAMS, 
		dataType: "html",
		success: function(result)
		{
			if(result==1)
				window.location.href="home";
			else
				$("#alert_error").show();
		},
		error: function()
		{

			$("#alert_error").show();

			console.log("Chiamata fallita, si prega di riprovare...");
		}
	});
}

function accessoAUTHSERVICE(type, idp, idgen_procedura)
{
	idp = idp || '';

	var accesso_type="";
	switch(type)
	{
		case "SPID":
			accesso_type="SPID"
			break;

		case "CNS":
			accesso_type="CNS / TS-CNS / CIE"
			break;

		case "CIE":
			accesso_type="CIE"
			break;

		case "LINEACOMUNE":
			accesso_type="LineaComune"
			break;
	}

	var page="./rebuilding_action.php";
	var params="_action=accessoAUTHSERVICE&_type="+type+"&_idp="+idp+"&_idprocedura="+idgen_procedura;
	//alert(page+params)
	$.ajax({
		type: "POST",
		url: page,
		data: params, 
		dataType: "html",
		success: function(result)
		{
			if(result=="0")
			{
				BootstrapDialog.show({
		            title: 'Attenzione',
					type: BootstrapDialog.TYPE_WARNING, 
		            message: 'Autenticazione '+accesso_type+' momentaneamente non disponibile.',
		            buttons: [{
		                label: 'CONTINUA',
		                action: function(dialog) {
		                    dialog.close();
		                }
		            }]
		        });	
			}
			else
			{
				//alert(result)
				var url=atob(result);
				
				window.location.href = url;
			}
		},
		error: function()
		{
			console.log("Chiamata fallita, si prega di riprovare...");

			BootstrapDialog.show({
	            title: 'Attenzione',
				type: BootstrapDialog.TYPE_DANGER, 
		        message: 'Autenticazione '+accesso_type+' momentaneamente non disponibile.',
	            buttons: [{
	                label: 'CONTINUA',
	                action: function(dialog) {
	                    dialog.close();
	                }
	            }]
	        });	
		}
	});
}

</script>

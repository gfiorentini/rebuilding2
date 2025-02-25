<?php

require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");
require_once("../librerie/dara.class.servizi.php");

$idoperatore=verificaUSER();

/*
$user=getUSER();
$validuser=verificaUSER($user);

if ($validuser)
{
	$operatore=new DARAOperatore($user);
	$autorizzazioni_domanda=$operatore->getAUORIZZAZIONEdomanda();
	
}
*/

global $db;

$piddara_operatore=getPARAMETRO("_k");


$aENTI=array(1=>"ATS1 - Pesaro",3=>"ATS3 - C.M. Catria e Nerone",4=>"ATS4 - Urbino",5=>"ATS5 - C.M. Montefeltro",6=>"ATS6 - Fano",7=>"ATS7 - Fossombrone",8=>"ATS8 - Senigallia",9=>"ATS9 - ASP Ambito 9 Jesi",10=>"ATS10 - Fabriano",11=>"ATS11 - Ancona",12=>"ATS12 - Falconara Marittima",13=>"ATS13 - Osimo",14=>"ATS14 - Civitanova Marche",15=>"ATS15 - Macerata",16=>"ATS16 - C.M. Monti Azzurri",17=>"ATS17 - Unione montanta alta valle del potenza e dell'Esino",18=>"ATS18 - C.M. Camerino",19=>"ATS19 - Fermo",20=>"ATS20 - Porto Sant'Elpidio",21=>"ATS 21 - San Benedetto del Tronto",22=>"ATS22 - Ascoli Piceno",23=>"ATS23 - U.C. Vallata del Tronto",24=>"ATS24 - C.M. dei Sibillini");

if (!empty($piddara_operatore))
{
    $operatore=new DARAOperatore($piddara_operatore);
    $descrizione_breadcumb="Dettaglio operatore ".$operatore->operatore_cognome." ".$operatore->operatore_nome; 
}
else
{
    $descrizione_breadcumb="Nuovo operatore";
}

if(getPARAMETRO("salvaOPERATORE"))
{
  $poperatore_ente=getPARAMETRO("operatore_ente");
  $poperatore_ente=$db->escape_text($poperatore_ente);


  $poperatore_cognome=getPARAMETRO("operatore_cognome");
  $poperatore_cognome=$db->escape_text($poperatore_cognome);
  
  $poperatore_nome=getPARAMETRO("operatore_nome");
  $poperatore_nome=$db->escape_text($poperatore_nome);

  $poperatore_email=getPARAMETRO("operatore_email");
  $poperatore_email=$db->escape_text($poperatore_email);  

  $poperatore_codicefiscale=getPARAMETRO("operatore_codicefiscale");
  $poperatore_codicefiscale=$db->escape_text($poperatore_codicefiscale);

  $poperatore_flagamministratore=getPARAMETRO("operatore_flagamministratore");
  $poperatore_flagamministratore=$db->escape_text($poperatore_flagamministratore);
  if ($poperatore_flagamministratore=='on')
    $poperatore_flagamministratore=1;
  else
    $poperatore_flagamministratore=0;

  $poperatore_flagabilitato=getPARAMETRO("operatore_flagabilitato");
  $poperatore_flagabilitato=$db->escape_text($poperatore_flagabilitato);
  if ($poperatore_flagabilitato=='on')
    $poperatore_flagabilitato=1;
  else
    $poperatore_flagabilitato=0;

  $poperatore_flagrup=getPARAMETRO("operatore_flagrup");
  $poperatore_flagrup=$db->escape_text($poperatore_flagrup);
  if ($poperatore_flagrup=='on')
    $poperatore_flagrup=1;
  else
    $poperatore_flagrup=0;

  $poperatore_flagdirigente=getPARAMETRO("operatore_flagdirigente");
  $poperatore_flagdirigente=$db->escape_text($poperatore_flagdirigente);
  if ($poperatore_flagdirigente=='on')
    $poperatore_flagdirigente=1;
  else
    $poperatore_flagdirigente=0;

  if(!empty($piddara_operatore))
  {   
    $sSQL="UPDATE dara_operatore SET
    operatore_ente='$poperatore_ente',
    operatore_cognome='$poperatore_cognome',
    operatore_nome='$poperatore_nome',
    operatore_email='$poperatore_email',
    operatore_codicefiscale='$poperatore_codicefiscale',
    operatore_flagabilitato='$poperatore_flagabilitato',
    operatore_flagamministratore='$poperatore_flagamministratore',
    operatore_flagrup='$poperatore_flagrup',
    operatore_flagdirigente='$poperatore_flagdirigente' 
    WHERE iddara_operatore='$piddara_operatore'"; 
    $db->query($sSQL);

    $operatore=new DARAOperatore($piddara_operatore);
  }
  else
  {
    $sSQL="insert into dara_operatore (operatore_ente,operatore_cognome,operatore_nome,operatore_email,operatore_codicefiscale,operatore_flagabilitato,operatore_flagamministratore,operatore_flagrup,operatore_flagdirigente) values('$poperatore_ente','$poperatore_cognome','$poperatore_nome','$poperatore_email','$poperatore_codicefiscale','$poperatore_flagabilitato','$poperatore_flagamministratore','$poperatore_flagrup','$poperatore_flagdirigente') ";
    $db->query($sSQL);
    $piddara_operatore=$db->insert_id();

    $operatore=new DARAOperatore($piddara_operatore);
  }
}

if ($operatore->operatore_flagabilitato==1)
  $checked_abilitato="checked";

if ($operatore->operatore_flagamministratore==1)
  $checked_amministratore="checked";

if ($operatore->operatore_flagamministratore==1)
  $checked_amministratore="checked";

if ($operatore->operatore_flagrup==1)
  $checked_rup="checked";

if ($operatore->operatore_flagdirigente==1)
  $checked_dirigente="checked";


?>
<!doctype html>
<html lang="it">
  <head>
  	<?php echo getREBUILDINGHEAD(true); ?>

    <style>
    .row {
      margin-bottom: 1%;
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

      $aBREADCUMB[1]["titolo_pagina"]="Autorizzazioni";
      $aBREADCUMB[1]["url"]="autorizzazione-operatori";

      $aBREADCUMB[3]["titolo_pagina"]=$descrizione_breadcumb;
      $aBREADCUMB[3]["url"]="";

      generaBREADCUMB($aBREADCUMB);
    ?>

    <!-- CONTENT -->
    <section class="bg-light">
      <div class="container-fluid">
        <div class="row">

          <div class="col-12 col-lg-6 col-xl-10 offset-xl-1 py-lg-2 bg-light">
              <div class="card-body">

                <!-- Form -->
                <form id="formOperatore" name="formOperatore" method="post" action="autorizzazione-operatore" role="form">

                  <div class="row">
                    <div class="col-12 col-md-12">
                      <div class="form-floating">
                        <select id="operatore_ente" name="operatore_ente" class="form-control form-control-flush form-select">
                            <option value='0'></option>
                            <?php
                                              
                              foreach ($aENTI as $key => $descrizione) 
                              {
                                if($key==$operatore->operatore_ente)
                                  echo '<option value="'.$key.'" selected>'.$descrizione.'</option>';                    
                                else
                                  echo '<option value="'.$key.'">'.$descrizione.'</option>';                    
                              }
                            ?>                                  
                          </select>              
                          <label for="formRUP">Centro territoriale</label>
                      </div>
                    </div>
                  </div>    
                  <div class="row">
                    <div class="col-12 col-md-12">
        
                        <div class="form-floating">
                          <input type="text" class="form-control" id="operatore_cognome" name="operatore_cognome" placeholder="Cognome" value="<?php echo $operatore->operatore_cognome;?>" <?php echo $isENABLED; ?> required>  
                          <label for="operatore_cognome">Cognome*</label>
                        </div> 
                    </div>
                  </div>                  

                  <div class="row">
                    <div class="col-12 col-md-12">
        
                        <div class="form-floating">
                          <input type="text" class="form-control" id="operatore_nome" name="operatore_nome" placeholder="Nome" value="<?php echo $operatore->operatore_nome;?>" <?php echo $isENABLED; ?> required>  
 
                          <label for="operatore_nome">Nome*</label>
                        </div> 
                    </div>
                  </div>  
               
                  <div class="row">
                    <div class="col-12 col-md-12">
        
                        <div class="form-floating">
                          <input type="text" class="form-control id="operatore_email" name="operatore_email" placeholder="Indirizzo email" value="<?php echo $operatore->operatore_email;?>" <?php echo $isENABLED; ?> required>  
 
                          <label for="operatore_email">e-mail*</label>
                        </div> 
                    </div>
                  </div>        

                  <div class="row">
                    <div class="col-12 col-md-12">
        
                        <div class="form-floating">
                          <input type="text" class="form-control" id="operatore_codicefiscale" name="operatore_codicefiscale" placeholder="Codice fiscale" value="<?php echo $operatore->operatore_codicefiscale;?>" <?php echo $isENABLED; ?> >  
 
                          <label for="operatore_email">Codice Fiscale</label>
                        </div> 
                    </div>
                  </div>                            

	              <div class="row">
	                <div class="col-12 col-md-6">
	    
	                  <div class="form-floating">
	                    <div class="card">
	                      <div class="card-body">

	                        <div class="form-check form-switch">
	                          <input class="form-check-input" type="checkbox" id="operatore_flagabilitato" name="operatore_flagabilitato" <?php echo $checked_abilitato; ?>>
	                          <label class="form-switch-label" for="operatore_flagabilitato">Abilitato</label>
	                        </div>

	                      </div>
	                    </div>
	                  </div>
	    
	                </div>
	                <div class="col-12 col-md-6">
	                  <div class="form-floating">
	                    <div class="card">
	                      <div class="card-body">
	                        <div class="form-check form-switch">
	                          <input class="form-check-input" type="checkbox" id="operatore_flagamministratore" name="operatore_flagamministratore"<?php echo $checked_amministratore; ?>>
	                          <label class="form-switch-label" for="operatore_flagamministratore">Amministratore</label>
	                        </div>

	                      </div>
	                    </div>
	                  </div>
	    
	                </div>
	              </div>

                <div class="row">
                  <div class="col-12 col-md-6">
      
                    <div class="form-floating">
                      <div class="card">
                        <div class="card-body">

                          <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="operatore_flagrup" name="operatore_flagrup" <?php echo $checked_rup; ?>>
                            <label class="form-switch-label" for="operatore_flagrup">RUP</label>
                          </div>

                        </div>
                      </div>
                    </div>
      
                  </div>
                  <div class="col-12 col-md-6">
                    <div class="form-floating">
                      <div class="card">
                        <div class="card-body">
                          <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="operatore_flagdirigente" name="operatore_flagdirigente"<?php echo $checked_dirigente; ?>>
                            <label class="form-switch-label" for="operatore_flagdirigente">Dirigente</label>
                          </div>

                        </div>
                      </div>
                    </div>
      
                  </div>
                </div>                

                    <div class="mt-6">
                      <button class="btn w-100 btn-primary-soft lift" type="submit" name="salvaOPERATORE" id="salvaOPERATORE" value="true">
                        Salva
                      </button>
                    </div>

                    <input type="hidden" name="_k" id="_k" value="<?php echo $piddara_operatore; ?>">
                </form>

              </div>
          </div>    

          
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
<script>

$(document).ready(function(){


});



</script> 
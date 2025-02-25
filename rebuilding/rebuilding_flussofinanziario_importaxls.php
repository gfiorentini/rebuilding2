<?php

require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");
require_once("../librerie/dara.class.servizi.php");


include("../librerie/lib.excell.php");
include("../librerie/PHPExcel/IOFactory.php");
include ("../librerie/simplexlsx.class.php");
include ("../librerie/easyODS.php");




global $db;


//$idoperatore=verificaUSER();
//$operatore=new DARAOperatore($idoperatore);

//$pidrebuilding_flussofinanziario=getPARAMETRO("_RENDICONTAZIONE");
//$pidrebuilding_flussofinanziario=$db->escape_text($pidrebuilding_flussofinanziario);

$pidrebuilding_flussofinanziario=1;
$flussofinanziario=new rebuildingFLUSSOFINANZIARIO($pidrebuilding_flussofinanziario);
$flussofinanziario_ente=$flussofinanziario->flussofinanziario_ente;
$aENTISELEZIONATI=explode(",",$flussofinanziario_ente);

$risorseassegnate=$flussofinanziario->getRISORSE();
$aRISORSEASSEGNATE=array();
foreach ($risorseassegnate as $key => $aRISORSE) 
{
  $aRISORSEASSEGNATE[$aRISORSE['risorsa_ente']]=$aRISORSE['risorsa_assegnata'];
}
die;



$righe_salto=2;
$action="importa";

if($action=="importa")
{



  $data=date("Y-m-d");

  $aENTI=array(1=>"ATS 1",3=>"ATS 3",4=>"ATS 4",5=>"ATS 5",6=>"ATS 6",7=>"ATS 7",8=>"ATS 8",9=>"ATS 9",10=>"ATS 10",11=>"ATS 11",12=>"ATS 12",13=>"ATS 13",14=>"ATS 14",15=>"ATS 15",16=>"ATS 16",17=>"ATS 17",18=>"ATS 18",19=>"ATS 19",20=>"ATS 20",21=>"ATS 21",22=>"ATS 22",23=>"ATS 23",24=>"ATS 24");

  $fldnome_allegato_name="rebuilding_risorseassegnate.xlsx";
  $fileName="./".$fldpath.$fldnome_allegato_name;

  if (!is_readable ($fileName)) die ('Cannot read ' . $fileName);

  $counter=1;
  $totale_anagrafiche=0;

      $array = new SimpleXLSX($fileName);
      
      foreach ($array->rows() as $row)
      {
        if($counter>$righe_salto)
        { 
          $ats_completo=strtoupper($row[4]);
          $partita_iva=$row[5];
          $risorsaassegnata=$row[7];

          list($ats_codice,$ats_descrizione)=explode("-",$ats_completo);
          $ats_codice=trim($ats_codice);
          $idente=array_search($ats_codice,$aENTI);
          if(!empty($idente) && in_array($idente,$aENTISELEZIONATI))
          {
              if(empty($risorsaassegnata))
                $risorsaassegnata=0;

              // Verifico se esiste il record
              if(array_key_exists($idente,$aRISORSEASSEGNATE))
              {
                $sSQL="UPDATE rebuilding_flussofinanziario_risorsa set risorsa_assegnata='$risorsaassegnata',risorsa_ultimamodifica='$data',risorsa_operatore='$idoperatore' where idrebuilding_flussofinanziario='$pidrebuilding_flussofinanziario' and risorsa_ente='$idente'";
                
                $db->query($sSQL);
              }
              else  
              {
                $sSQL="insert into rebuilding_flussofinanziario_risorsa (idrebuilding_flussofinanziario,risorsa_ente,risorsa_assegnata,risorsa_datainserimento,risorsa_ultimamodifica,risorsa_operatore) values('$pidrebuilding_flussofinanziario','$idente','$risorsaassegnata','$data','$data','$idoperatore')";
                $db->query($sSQL);
                
              }  
          }    

          
        }

        $counter++;
      }


      die("fine elaborazione");

}



?>
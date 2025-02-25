<?php 
require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");
require_once("../librerie/dara.class.servizi.php");

require('../librerie/fpdf/fpdf.php');

global $db;
//ini_set('display_errors', 1);

$idoperatore=verificaUSER();

$operatore=new DARAOperatore($idoperatore);
$operatore_ente=$operatore->operatore_ente;
if(empty($operatore_ente))
  $operatore_ente=9999;

$operatore_flagamministratore=$db->getVALUE("select operatore_flagamministratore from dara_operatore where iddara_operatore='$idoperatore' ","operatore_flagamministratore");

$aENTI=array(1=>"ATS1 - Pesaro",3=>"ATS3 - C.M. Catria e Nerone",4=>"ATS4 - Urbino",5=>"ATS5 - C.M. Montefeltro",6=>"ATS6 - Fano",7=>"ATS7 - Fossombrone",8=>"ATS8 - Senigallia",9=>"ATS9 - ASP Ambito 9 Jesi",10=>"ATS10 - Fabriano",11=>"ATS11 - Ancona",12=>"ATS12 - Falconara Marittima",13=>"ATS13 - Osimo",14=>"ATS14 - Civitanova Marche",15=>"ATS15 - Macerata",16=>"ATS16 - C.M. Monti Azzurri",18=>"ATS18 - C.M. Camerino",19=>"ATS19 - Fermo",20=>"ATS20 - Porto Sant'Elpidio",21=>"San Benedetto del Trotto",22=>"ATS22 - Ascoli Piceno",23=>"ATS23 - U.C. Vallata del Tronto",24=>"ATS24 - C.M. dei Sibillini");

$centroterritorialeOPERATORE=$aENTI[$operatore_ente];

$aANNI=array(2017=>"2017",2018=>"2018",2019=>"2019",2020=>"2020",2021=>"2021",2022=>"2022",2023=>"2023",2024=>"2024",2025=>"2025",2026=>"2026", 2027=>"2027");

$aTIPOFONDO=array(1=>"Regionale",2=>"Statale",3=>"FSE",4=>"Misto","Sanitario");
$aTIPODOCUMENTO=array(1=>"RIPARTO",2=>"CRONOPROGRAMMA",3=>"MODULISTICA",4=>"ALTRO");
$aTIPOAREA=array(1=>"Famiglia",2=>"Anziani",3=>"Minori",4=>"Dipendenze",5=>"Disabili",6=>"Adulti e minori sottoposti a provvedimenti Autorità Giudiziaria");


$pidrebuilding_flussofinanziario=getPARAMETRO("_RENDICONTAZIONE");
$pidrebuilding_flussofinanziario=$db->escape_text($pidrebuilding_flussofinanziario);

$flussofinanziario=new rebuildingFLUSSOFINANZIARIO($pidrebuilding_flussofinanziario);

$aentiselezionati=explode(",",$flussofinanziario->flussofinanziario_ente);
$enti='';
foreach ($aentiselezionati as $key => $value) 
{
	if($enti)
		$enti.=", ";
	$enti.=$aENTI[$value];
}

$areaintervento=$aTIPOAREA[$flussofinanziario->flussofinanziario_areaintervento];

$flussofinanziario_datainserimento=dataitaliana($flussofinanziario->flussofinanziario_datainserimento);
$flussofinanziario_ultimamodifica=dataitaliana($flussofinanziario->flussofinanziario_ultimamodifica);

$operatore=new DARAOperatore($flussofinanziario->flussofinanziario_operatore);
$operatore_nominativo=$operatore->operatore_cognome.' '.$operatore->operatore_nome;
$operatore_nominativo=addslashes($operatore_nominativo);

$operatorerup=new DARAOperatore($flussofinanziario->flussofinanziario_rup);
$operatorerup_nominativo=$operatorerup->operatore_cognome.' '.$operatorerup->operatore_nome;
$operatorerup_nominativo=addslashes($operatorerup_nominativo);

$aDOCUMENTI=$flussofinanziario->getALLEGATI();
$counter_allegati=0;
$rigadocumento="";
$sDOCUMENTI="";

foreach ($aDOCUMENTI as $key => $aDATI) 
{
	$idrebuilding_flussofinanziario_documento=$aDATI["idrebuilding_flussofinanziario_documento"];
	$flussofinanziario_documentotitolo=$aDATI["flussofinanziario_documentotitolo"];
	$flussofinanziario_documentotipo=$aDATI["flussofinanziario_documentotipo"];
	$flussofinanziario_documentoente=$aDATI["flussofinanziario_documentoente"];
	$flussofinanziario_documentonome=$aDATI["flussofinanziario_documentonome"];
	$counter_allegati++;

	$entiselezionati=explode(",",$flussofinanziario_documentoente);
	if((empty($operatore_flagamministratore) && in_array($operatore_ente, $entiselezionati)) || !empty($operatore_flagamministratore))
	{
		if($sDOCUMENTI)
			$sDOCUMENTI.=", ";
		$sDOCUMENTI.=$flussofinanziario_documentotitolo;
	}

    
}  


class PDF extends FPDF
{
	// Page header
	function Header()
	{
		// Logo
		//$this->Image('logo.png',10,6,30);
		// Arial bold 15

		$this->SetFont('Arial','B',15);
		// Move to the right
		$this->Cell(50);

		// Title
		$this->Cell(80,10,'Flusso di finanziamento',1,0,'C');
		// Line break
		$this->Ln(20);
	}

	// Page footer
	function Footer()
	{
		// Position at 1.5 cm from bottom
		$this->SetY(-15);
		// Arial italic 8
		$this->SetFont('Arial','I',8);
		// Page number
		$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}
}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',10);

$pdf->Cell(0,10,"Titolo: ".$flussofinanziario->flussofinanziario_titolo,0,1);
$pdf->MultiCell(0,10,"Centri territoriali: ".$enti,0,1);
$pdf->Cell(0,10,"Area intervento: ".$areaintervento,0,1);
$pdf->Cell(0,10,"Anno: ".$flussofinanziario->flussofinanziario_anno,0,1);
$pdf->Cell(0,10,"Tipo fondo: ".$aTIPOFONDO[$flussofinanziario->flussofinanziario_tipofondo],0,1);
$pdf->MultiCell(0,10,"Legge di riferimento: ".$flussofinanziario->flussofinanziario_leggeriferimento,0,1);
$pdf->Cell(0,10,"RUP: ".$operatorerup_nominativo,0,1);
$pdf->Cell(0,10,"Contatti RUP: ".$flussofinanziario->flussofinanziario_contatti_rup,0,1);
$pdf->MultiCell(0,10,"Testo: ".$flussofinanziario->flussofinanziario_testo,0,1);
$pdf->Cell(0,10,"Documenti: ".$sDOCUMENTI,0,1);
$pdf->Cell(0,10,$flussofinanziario_datainserimento." ".$operatore_nominativo,0,1);
/*
for($i=1;$i<=40;$i++)
{
	
	$pdf->Cell(0,10,"Centri territoriali: ".$enti);
	$pdf->Cell(0,10,"Area intervento: ".$areaintervento);
	$pdf->Cell(0,10,"Anno: ".$flussofinanziario->flussofinanziario_anno);
	$pdf->Cell(0,10,"Tipo fondo: ".$aTIPOFONDO[$flussofinanziario->flussofinanziario_tipofondo]);
	$pdf->Cell(0,10,"Legge di riferimento: ".$flussofinanziario->flussofinanziario_leggeriferimento);
	$pdf->Cell(0,10,"RUP: ".$operatorerup_nominativo);
	$pdf->Cell(0,10,"Contatti RUP: ".$flussofinanziario->flussofinanziario_contatti_rup);
	$pdf->Cell(0,10,"Titolo: ".$flussofinanziario->flussofinanziario_titolo);
	$pdf->Cell(0,10,"Testo: ".$flussofinanziario->flussofinanziario_testo);
	$pdf->Cell(0,10,"Documenti: ".$sDOCUMENTI);
	$pdf->Cell(0,10,$flussofinanziario_datainserimento." ".$operatore_nominativo);
	
	$pdf->Cell(0,10,'Printing line number '.$i,0,1);

}
*/
$pdf->Output();
/*

$pdf = new FPDF();
$pdf->AddFont('CevicheOne','','CevicheOne-Regular.php','.');
$pdf->AddPage();
$pdf->ln();
$pdf->ln();
$pdf->SetFont('CevicheOne','B',22);
$pdf->Cell(0,10,'Flusso finanziario - scheda',0,0,'C');

$pdf->ln();
$pdf->ln();
$pdf->SetFont('CevicheOne','',13);
$pdf->MultiCell(0,5,"Centri territoriali: ".$enti);
$pdf->ln();
$pdf->MultiCell(0,5,"Area intervento: ".$areaintervento);
$pdf->ln();
$pdf->MultiCell(0,5,"Anno: ".$flussofinanziario->flussofinanziario_anno);
$pdf->ln();
$pdf->MultiCell(0,5,"Tipo fondo: ".$aTIPOFONDO[$flussofinanziario->flussofinanziario_tipofondo]);
$pdf->ln();
$pdf->MultiCell(0,5,"Legge di riferimento: ".$flussofinanziario->flussofinanziario_leggeriferimento);
$pdf->ln();
$pdf->MultiCell(0,5,"RUP: ".$operatorerup_nominativo);
$pdf->ln();
$pdf->MultiCell(0,5,"Contatti RUP: ".$flussofinanziario->flussofinanziario_contatti_rup);
$pdf->ln();
$pdf->MultiCell(0,5,"Titolo: ".$flussofinanziario->flussofinanziario_titolo);
$pdf->ln();
$pdf->MultiCell(0,5,"Testo: ".$flussofinanziario->flussofinanziario_testo);
$pdf->ln();
$pdf->MultiCell(0,5,"Documenti: ".$sDOCUMENTI);

$pdf->SetFont('CevicheOne','',6);
$pdf->ln();
$pdf->ln();
$pdf->ln();
//$pdf->MultiCell(0,5,$flussofinanziario_datainserimento." ".$operatore_nominativo." ".$flussofinanziario_ultimamodifica);
$pdf->MultiCell(0,5,$flussofinanziario_datainserimento." ".$operatore_nominativo);

$pdf->Output();

*/
?>
<?php

require_once("./rebuilding_connect.php");
require_once("../librerie/funzionigenerali.php");
require_once("../librerie/rebuilding.class.php");
require_once("../librerie/dara.class.servizi.php");
//require_once('../librerie/class.XLSXWriter.php');

$aENTI = array(1 => "ATS1 - Pesaro", 3 => "ATS3 - C.M. Catria e Nerone", 4 => "ATS4 - Urbino", 5 => "ATS5 - C.M. Montefeltro", 6 => "ATS6 - Fano", 7 => "ATS7 - Fossombrone", 8 => "ATS8 - Senigallia", 9 => "ATS9 - ASP Ambito 9 Jesi", 10 => "ATS10 - Fabriano", 11 => "ATS11 - Ancona", 12 => "ATS12 - Falconara Marittima", 13 => "ATS13 - Osimo", 14 => "ATS14 - Civitanova Marche", 15 => "ATS15 - Macerata", 16 => "ATS16 - C.M. Monti Azzurri", 18 => "ATS18 - C.M. Camerino", 19 => "ATS19 - Fermo", 20 => "ATS20 - Porto Sant'Elpidio", 21 => "San Benedetto del Trotto", 22 => "ATS22 - Ascoli Piceno", 23 => "ATS23 - U.C. Vallata del Tronto", 24 => "ATS24 - C.M. dei Sibillini");

$idoperatore = verificaUSER();

$operatore = new DARAOperatore($idoperatore);
$operatore_ente = $operatore->operatore_ente;
$centroterritorialeOPERATORE = $aENTI[$operatore_ente];

$aANNI = array(2017 => "2017", 2018 => "2018", 2019 => "2019", 2020 => "2020", 2021 => "2021", 2022 => "2022", 2023 => "2023",2024=>"2024",2025=>"2025",2026=>"2026", 2027=>"2027");

$aTIPOFONDO = array(1 => "Regionale", 2 => "Statale", 3 => "FSE", 4 => "Misto", "Sanitario");
$aTIPODOCUMENTO = array(1 => "RIPARTO", 2 => "CRONOPROGRAMMA", 3 => "MODULISTICA", 4 => "ALTRO");
$aTIPOAREA=array(1=>"Famiglia e Minori",2=>"Anziani",3=>"Immigrati e nomadi",4=>"Dipendenze",5=>"Disabili",6=>"Povertà, disagio adulti e senza fissa dimora",7=>"Multiutenza");

$operatore_flagamministratore = $db->getVALUE("select operatore_flagamministratore from dara_operatore where iddara_operatore='$idoperatore' ", "operatore_flagamministratore");
if (empty($operatore_ente) && !$operatore_flagamministratore)
  $operatore_ente = 9999;


$prendicontazione_areaintervento = getPARAMETRO("_target");
$prendicontazione_areaintervento = $db->escape_text($prendicontazione_areaintervento);


$prendicontazione_tipofondo = getPARAMETRO("_fondo");
$prendicontazione_tipofondo = $db->escape_text($prendicontazione_tipofondo);

$prendicontazione_anno = getPARAMETRO("_anno");
$prendicontazione_anno = $db->escape_text($prendicontazione_anno);

$prendicontazione_ente = getPARAMETRO("_ente");
$prendicontazione_ente = $db->escape_text($prendicontazione_ente);

$pflussofinanziario_rup = getPARAMETRO("_rup");
$pflussofinanziario_rup = $db->escape_text($pflussofinanziario_rup);


if (empty($operatore_flagamministratore)) {
  $prendicontazione_ente = $operatore_ente;
}


$aSTATI = array(1 => "NO", 2 => "SI");

$sWhere = "";
if (!empty($prendicontazione_leggeriferimento)) {
  if (!empty($sWhere))
    $sWhere .= " and ";
  $sWhere .= " flussofinanziario_leggeriferimento like '%" . $prendicontazione_leggeriferimento . "%' ";
}

if (!empty($prendicontazione_testo)) {
  if (!empty($sWhere))
    $sWhere .= " and ";
  $sWhere .= " flussofinanziario_testo like '%" . $prendicontazione_testo . "%' ";
}

if (!empty($prendicontazione_tipofondo)) {
  if (!empty($sWhere))
    $sWhere .= " and ";
  $sWhere .= " flussofinanziario_tipofondo='" . $prendicontazione_tipofondo . "' ";
}

if (!empty($prendicontazione_anno)) {
  if (!empty($sWhere))
    $sWhere .= " and ";
  $sWhere .= " flussofinanziario_anno='" . $prendicontazione_anno . "' ";
}

if (!empty($pflussofinanziario_rup)) {
  if (!empty($sWhere))
    $sWhere .= " and ";
  $sWhere .= " flussofinanziario_rup='" . $pflussofinanziario_rup . "' ";
}

if (!empty($prendicontazione_areaintervento)) {
  if (!empty($sWhere))
    $sWhere .= " and ";
  $sWhere .= " flussofinanziario_areaintervento='" . $prendicontazione_areaintervento . "' ";
}

if (!empty($sWhere))
  $sWhere = " WHERE " . $sWhere;

$flussi = new rebuildingFLUSSOFINANZIARIO();
if (empty($operatore_flagamministratore)) {
  if (!empty($sWhere))
    $sWhere .= " and ";
  else
    $sWhere = " where ";

  $sWhere .= " flussofinanziario_stato='2' ";

  if (!empty($sWhere))
    $sWhere .= " and ";
  else
    $sWhere = " where ";
  $sWhere .= " rebuilding_flussofinanziario_ente.flussofinanziario_ente='" . $operatore->operatore_ente . "'";
  $aFLUSSI = $flussi->getFLUSSIENTE($sWhere);
} else
  $aFLUSSI = $flussi->getFLUSSI($sWhere);


$aINTESTAZIONE = array("Progressivo", "Area/Target", "Anno", "Enti", "Tipo fondo", "Codice SIRPS", "DGR di riferimento", "Titolo", "Testo", "RUP", "Contatti RUP", "Validato");
$iCounter = 1;
$aRECORD = array();

$tableINTESTAZIONE="<thead>";
$tableINTESTAZIONE.="<tr>";
$tableINTESTAZIONE.="<th>ID Flusso</th>";
$tableINTESTAZIONE.="<th>Area/Target</th>";
$tableINTESTAZIONE.="<th>Anno</th>";
$tableINTESTAZIONE.="<th>Enti</th>";
$tableINTESTAZIONE.="<th>Tipo fondo</th>";
$tableINTESTAZIONE.="<th>Codice SIRPS</th>";
$tableINTESTAZIONE.="<th>DGR di riferimento</th>";
$tableINTESTAZIONE.="<th>RUP</th>";
$tableINTESTAZIONE.="<th>Contatti RUP</th>";
$tableINTESTAZIONE.="<th>Titolo</th>";
$tableINTESTAZIONE.="<th>Testo</th>";
$tableINTESTAZIONE.="<th>Validato</th>";
$tableINTESTAZIONE.="</tr>";
$tableINTESTAZIONE.="</thead>";

$tableRECORD="<tbody>";
foreach ($aFLUSSI as $key => $aDATI) 
{
  $idrebuilding_flussofinanziario = $aDATI["idrebuilding_flussofinanziario"];
  $flussofinanziario_anno = $aDATI["flussofinanziario_anno"];
  $flussofinanziario_ente = $aDATI["flussofinanziario_ente"];
  $flussofinanziario_tipofondo = $aDATI["flussofinanziario_tipofondo"];
  $flussofinanziario_areaintervento = $aDATI["flussofinanziario_areaintervento"];
  $flussofinanziario_codicesirps = $aDATI["flussofinanziario_codicesirps"];

  $flussofinanziario_leggeriferimento = clean($aDATI["flussofinanziario_leggeriferimento"]);

  $flussofinanziario_titolo = clean($aDATI["flussofinanziario_titolo"]);

  $flussofinanziario_testo = clean($aDATI["flussofinanziario_testo"]);
  
  $flussofinanziario_rup = $aDATI["flussofinanziario_rup"];
  $flussofinanziario_operatore = $aDATI["flussofinanziario_operatore"];
  $flussofinanziario_stato = $aDATI["flussofinanziario_stato"];

  $operatore = new DARAOperatore($flussofinanziario_operatore);
  $operatore_nominativo = $operatore->operatore_cognome . ' ' . $operatore->operatore_nome;
  $operatore_nominativo = addslashes($operatore_nominativo);

  $operatorerup = new DARAOperatore($flussofinanziario_rup);
  $operatorerup_nominativo = $operatorerup->operatore_cognome . ' ' . $operatorerup->operatore_nome;
  $operatorerup_nominativo = addslashes($operatorerup_nominativo);

  $flussofinanziario_datainserimento = $aDATI["flussofinanziario_datainserimento"];
  $flussofinanziario_ultimamodifica = $aDATI["flussofinanziario_ultimamodifica"];
  $flussofinanziario_contatti_rup = $aDATI["flussofinanziario_contatti_rup"];

  $flussofinanziario_datainserimento = dataitaliana($flussofinanziario_datainserimento);
  $flussofinanziario_ultimamodifica = dataitaliana($flussofinanziario_ultimamodifica);

  $rowDETTAGLIO = array($idrebuilding_flussofinanziario, $aTIPOAREA[$flussofinanziario_areaintervento], $flussofinanziario_anno, $flussofinanziario_ente, $aTIPOFONDO[$flussofinanziario_tipofondo], $flussofinanziario_leggeriferimento, $flussofinanziario_titolo, $flussofinanziario_testo, $operatorerup_nominativo, $flussofinanziario_contatti_rup, $aSTATI[$flussofinanziario_stato]);

  $aentiselezionati=explode(",",$flussofinanziario_ente);
  foreach ($aentiselezionati as $key => $value) 
  {
    if($enti)
      $enti.=", ";
    $enti.=$aENTI[$value];
  }

  $tableRECORD.="<tr>";
  $tableRECORD.="<td>".$idrebuilding_flussofinanziario."</td>";
  $tableRECORD.="<td>".$aTIPOAREA[$flussofinanziario_areaintervento]."</td>";
  $tableRECORD.="<td>".$flussofinanziario_anno."</td>";
  $tableRECORD.="<td>".$enti."</td>";
  $tableRECORD.="<td>".$aTIPOFONDO[$flussofinanziario_tipofondo]."</td>";
  $tableRECORD.="<td>".$flussofinanziario_codicesirps."</td>";
  $tableRECORD.="<td>".$flussofinanziario_leggeriferimento."</td>";
  $tableRECORD.="<td>".$operatorerup_nominativo."</td>";
  $tableRECORD.="<td>".$flussofinanziario_contatti_rup."</td>";
  $tableRECORD.="<td>".$flussofinanziario_titolo."</td>";
  $tableRECORD.="<td>".$flussofinanziario_testo."</td>";
  $tableRECORD.="<td>".$aSTATI[$flussofinanziario_stato]."</td>";
  $tableRECORD.="</tr>";
  array_push($aRECORD, $rowDETTAGLIO);

  $iCounter++;
}
$tableRECORD.="</tbody>";

$filename = "ReportFlussifinanziari_" . date("Ymd") . ".xls";

header ("Content-Type: application/vnd.ms-excel");
header ("Content-Disposition: inline; filename=$filename");            

echo "<html lang=it><table border=1>".$tableINTESTAZIONE.$tableRECORD."</table>";

/*
header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');

$writer = new XLSXWriter();
$writer->setAuthor('ICCS');

$writer->writeSheetRow('Sheet1', $aINTESTAZIONE);

foreach ($aRECORD as $key => $value) {
  $writer->writeSheetRow('Sheet1', $value);
}

$writer->writeToStdOut();
*/



<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/12/21 17:48:19 $
 		File Versie					: $Revision: 1.6 $

 		$Log: tijdelijkerekeningFondsmutatiesXLS.php,v $
 		Revision 1.6  2018/12/21 17:48:19  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2015/10/07 19:34:27  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2015/10/04 11:49:47  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2011/09/08 07:17:48  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2009/04/01 14:57:36  rvv
 		*** empty log message ***

 		Revision 1.1  2009/03/14 11:42:06  rvv
 		*** empty log message ***

 		Revision 1.3  2009/01/20 17:46:01  rvv
 		*** empty log message ***

 		Revision 1.2  2008/05/16 08:04:51  rvv
 		*** empty log message ***

 		Revision 1.1  2007/11/27 13:19:18  cvs
 		CRM
 		- verjaardaglijst
 		- velden omzetten van extra velden naar naw
 		- excel van tijdelijke rekening mutaties


*/

include_once("wwwvars.php");
include_once('../classes/excel/Writer.php');



function FillRow($data,$row=0)
{
  global $worksheet,$veldArray;
  $col=0;
  foreach ($veldArray as $value)
  {
    $worksheet->write($row, $col, $data[$value]);
    $col++;
  }
}

$veldArray = array("Regel","Client","Rekening","Transactietype","Aantal","Fonds","Fondskoers","Totaal","Valuta","Valutakoers");

$query = "
SELECT
  '1' as Regel,
  Portefeuilles.Client as Client,
  TijdelijkeRekeningmutaties.Rekening as Rekening,
  TijdelijkeRekeningmutaties.Transactietype as Transactietype,
  TijdelijkeRekeningmutaties.Aantal as Aantal,
  TijdelijkeRekeningmutaties.Fonds as Fonds,
  TijdelijkeRekeningmutaties.Fondskoers as Fondskoers,
  TijdelijkeRekeningmutaties.Bedrag as Totaal,
  TijdelijkeRekeningmutaties.Valuta as Valuta,
  TijdelijkeRekeningmutaties.Omschrijving as FondsOmschrijving,
  TijdelijkeRekeningmutaties.Valutakoers  as Valutakoers,
  TijdelijkeRekeningmutaties.bankTransactieId
FROM
  (TijdelijkeRekeningmutaties, Portefeuilles)
JOIN
  Rekeningen ON Rekeningen.Rekening = TijdelijkeRekeningmutaties.Rekening AND Rekeningen.consolidatie=0
WHERE
  Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND TijdelijkeRekeningmutaties.change_user = '$USR' AND Portefeuilles.consolidatie=0 AND
  TijdelijkeRekeningmutaties.Grootboekrekening = 'FONDS'
ORDER BY
  Client,Fonds,Transactietype ASC, Bedrag desc";

$db = new DB();
$db->SQL($query);
$db->Query();

$db2= new DB();

$workbook = new Spreadsheet_Excel_Writer();
$worksheet =& $workbook->addWorksheet();
$row=0;
$col=0;
foreach ($veldArray as $value)
{
  $worksheet->write($row, $col, $value);
  $col++;
}

$row = 1;
$records=array();
while ($record = $db->nextRecord())
  $records[]=$record;

$query = "SELECT TijdelijkeRekeningmutaties.Rekening,TijdelijkeRekeningmutaties.Bedrag,TijdelijkeRekeningmutaties.Grootboekrekening,
TijdelijkeRekeningmutaties.Omschrijving
FROM
  (TijdelijkeRekeningmutaties, Portefeuilles)
JOIN
  Rekeningen ON Rekeningen.Rekening = TijdelijkeRekeningmutaties.Rekening AND Rekeningen.consolidatie=0
   WHERE Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie=0
  AND TijdelijkeRekeningmutaties.Grootboekrekening IN('KOST','KOBU') AND
TijdelijkeRekeningmutaties.change_user = '$USR' ORDER BY Bedrag desc ";
$db->SQL($query);
$db->Query();
while ($record = $db->nextRecord())
  $kostenTotaal[$record['Rekening']][$record['Omschrijving']][$record['Grootboekrekening']][]=$record['Bedrag'];

foreach ($records as $record)
{
  $query = "SELECT SUM(Bedrag) as kosten FROM TijdelijkeRekeningmutaties WHERE Omschrijving = '".mysql_real_escape_string($record['FondsOmschrijving'])."' AND 
                   TijdelijkeRekeningmutaties.Grootboekrekening IN('KOST','KOBU','TOB') 
                   AND TijdelijkeRekeningmutaties.Rekening = '".mysql_real_escape_string($record['Rekening'])."' 
                   AND TijdelijkeRekeningmutaties.bankTransactieId = '".$record['bankTransactieId']."'
                   AND TijdelijkeRekeningmutaties.change_user = '$USR'";
  $db2->SQL($query);
  $kosten=$db2->lookupRecord();
  $record['Totaal'] += $kosten['kosten'];
  //$record['Totaal'] += $kosten['kosten'];
  $record['Regel']=$row;
  fillRow($record,$row);
  $row++;
}
$worksheet->setColumn(0,9,15);//$firstcol, $lastcol, $width, $format = null, $hidden = 0, $level = 0

$workbook->send('export.xls');
 // exit;
$workbook->close();
?>
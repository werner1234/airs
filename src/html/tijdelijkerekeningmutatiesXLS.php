<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/12/21 17:48:19 $
 		File Versie					: $Revision: 1.6 $
 		
 		$Log: tijdelijkerekeningmutatiesXLS.php,v $
 		Revision 1.6  2018/12/21 17:48:19  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/07/28 14:47:20  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2009/03/14 11:42:06  rvv
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
  global $worksheet;
  for($col = 0; $col < count($data); $col++)
  {
    $worksheet->write($row, $col, $data[$col]);
  }
}
   

$veldArray = array("Client"=>"Portefeuilles.Client as Client",
                   "Rekening"=>"TijdelijkeRekeningmutaties.Rekening as Rekening", 
                   "Omschrijving"=>"TijdelijkeRekeningmutaties.Omschrijving as Omschrijving",
                   "Boekdatum"=>"TijdelijkeRekeningmutaties.Boekdatum as Boekdatum", 
                   "Grootboekrekening"=>"TijdelijkeRekeningmutaties.Grootboekrekening as Grootboekrekening", 
                   "Valuta"=>"TijdelijkeRekeningmutaties.Valuta as Valuta",
                   "ValutaKoers"=>"TijdelijkeRekeningmutaties.Valutakoers as ValutaKoers", 
                   "Aantal"=>"TijdelijkeRekeningmutaties.Aantal as Aantal", 
                   "Fondskoers"=>"TijdelijkeRekeningmutaties.Fondskoers as Fondskoers", 
                   "Debet"=>"TijdelijkeRekeningmutaties.Debet as Debet", 
                   "Credit"=>"TijdelijkeRekeningmutaties.Credit as Credit",
                   "Bedrag"=>"TijdelijkeRekeningmutaties.Bedrag as Bedrag", 
                   "Transactietype"=>"TijdelijkeRekeningmutaties.Transactietype as Transactietype",
                   "InternDepot"=>"Portefeuilles.InternDepot as InternDepot",);

$query = "
SELECT 
  ".implode(", ",$veldArray)."
FROM 
  (TijdelijkeRekeningmutaties, Portefeuilles)
JOIN 
  Rekeningen ON Rekeningen.Rekening = TijdelijkeRekeningmutaties.Rekening AND Rekeningen.consolidatie=0
WHERE
  Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie=0 AND TijdelijkeRekeningmutaties.change_user = '$USR'
ORDER BY 
  Rekening ASC";

$db = new DB();
$db->SQL($query);
$db->Query();
$filename = "test.xls";
$workbook = new Spreadsheet_Excel_Writer();
$worksheet =& $workbook->addWorksheet();


FillRow(array_keys($veldArray));
$row = 1;
while ($record = $db->nextRecord("num"))
{
  fillRow($record,$row);
  $row++;
}
//listarray($data);


$workbook->send('export.xls');
 // exit;
$workbook->close();
?>
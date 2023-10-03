<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/08/31 12:18:23 $
 		File Versie					: $Revision: 1.15 $
 		
 		$Log: tijdelijkerekeningFondsmutatiesPDF2.php,v $
 		Revision 1.15  2019/08/31 12:18:23  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2018/12/21 17:48:19  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2018/11/23 08:02:45  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2018/11/21 16:46:17  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2018/11/16 14:55:43  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.9  2015/10/07 19:34:27  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2015/10/04 11:49:47  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2015/01/28 20:03:38  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/10/08 15:35:30  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2014/08/02 15:22:50  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/02/28 16:39:28  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2013/11/06 14:42:34  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/11/06 14:21:51  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/11/06 13:06:45  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2013/10/29 08:23:21  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2013/10/26 15:40:52  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2013/10/01 14:48:38  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2013/09/01 13:31:16  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2013/08/14 15:57:30  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2013/06/01 16:14:14  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2009/10/21 16:06:28  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2009/07/12 09:29:31  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2009/07/06 07:15:25  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2009/06/10 12:29:49  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2009/06/07 10:26:28  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/05/30 15:02:22  rvv
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
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once("../classes/AE_cls_fpdf.php");
include_once("rapport/rapportVertaal.php");
include_once("rapport/rapportRekenClass.php");
include_once("rapport/PDFOverzicht.php");


	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}



$pdf = new PDFOverzicht('L','mm');
		
$pdf->SetAutoPageBreak(true,10);
//$pdf->pagebreak = 200;
$pdf->__appvar = $__appvar;

$db=new DB();
if(checkAccess())
  $query="SELECT Portefeuilles.Portefeuille FROM Portefeuilles ";
else
  $query="SELECT Portefeuilles.Portefeuille, VermogensbeheerdersPerGebruiker.Gebruiker 
  FROM VermogensbeheerdersPerGebruiker Join Portefeuilles ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
  WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR' ";

$db->SQL($query);
$portefeuille = $db->lookupRecord();

loadLayoutSettings($pdf, $portefeuille['Portefeuille']);	

$pdf->rapport_type = "Fondsmutaties2";
$pdf->rapport_voettext='';
if($__appvar["bedrijf"] == 'RCN')
{
  $pdf->SetFont($pdf->rapport_font, 'B', 9);
}
else
{
  $pdf->SetFont($pdf->rapport_font, 'B', 8);
}
$pdf->AddPage();
$pdf->rowHeight=6;


$query = "
SELECT 
  Portefeuilles.Client as Client,
  Portefeuilles.Portefeuille as Portefeuille,
  TijdelijkeRekeningmutaties.Rekening as Rekening,
  TijdelijkeRekeningmutaties.Transactietype as Transactietype,
  TijdelijkeRekeningmutaties.Aantal as Aantal,
  TijdelijkeRekeningmutaties.Fonds as Fonds,
  TijdelijkeRekeningmutaties.Fondskoers as Fondskoers,
  TijdelijkeRekeningmutaties.Bedrag as Totaal,
  TijdelijkeRekeningmutaties.Valuta as Valuta,
  TijdelijkeRekeningmutaties.Omschrijving as FondsOmschrijving,
  TijdelijkeRekeningmutaties.Valutakoers  as Valutakoers,
  TijdelijkeRekeningmutaties.Boekdatum  as Boekdatum,
  TijdelijkeRekeningmutaties.bankTransactieId,
  Fondsen.OptieBovenliggendFonds,
  Fondsen.Fondseenheid,
  Fondsen.Valuta as FondsValuta,
  IF(Fondsen.OptieBovenliggendFonds='',TijdelijkeRekeningmutaties.Fonds,CONCAT(Fondsen.OptieBovenliggendFonds,'Optie')) as sortering
FROM 
  (TijdelijkeRekeningmutaties)
  JOIN Rekeningen ON Rekeningen.Rekening = TijdelijkeRekeningmutaties.Rekening AND Rekeningen.consolidatie=0
  JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille  AND Portefeuilles.consolidatie=0
  LEFT JOIN Fondsen ON TijdelijkeRekeningmutaties.Fonds = Fondsen.Fonds
  LEFT JOIN BeleggingscategoriePerFonds ON TijdelijkeRekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND Portefeuilles.Vermogensbeheerder=BeleggingscategoriePerFonds.Vermogensbeheerder 
  LEFT JOIN Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
  LEFT JOIN BeleggingssectorPerFonds ON TijdelijkeRekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds AND Portefeuilles.Vermogensbeheerder=BeleggingssectorPerFonds.Vermogensbeheerder
  LEFT JOIN Regios ON BeleggingssectorPerFonds.Regio = Regios.Regio
WHERE
  TijdelijkeRekeningmutaties.change_user = '$USR' AND
  TijdelijkeRekeningmutaties.Grootboekrekening = 'FONDS'
ORDER BY 
  Client,Rekening,Boekdatum,sortering,Beleggingscategorien.Afdrukvolgorde,Regios.Afdrukvolgorde,Fondsen.OptieBovenliggendFonds,Fondsen.OptieExpDatum,Transactietype ASC";

$db = new DB();
$db2= new DB();
$db->SQL($query);
$db->Query();

$trans=array('V/O'=>'VO','V/S'=>'VS','A/O'=>'AO','A/S'=>'AS');
$pdf->SetFillColor(230,230,230);
$fill=false; 
$row = 1;
while ($record = $db->nextRecord())
{
  $date=db2jul($record['Boekdatum']);
  $query = "SELECT SUM(Bedrag) as kosten FROM TijdelijkeRekeningmutaties WHERE Omschrijving = '".mysql_real_escape_string($record['FondsOmschrijving'])."' AND 
                   TijdelijkeRekeningmutaties.Grootboekrekening IN('KOST','KOBU','TOB') 
                   AND TijdelijkeRekeningmutaties.Rekening = '".mysql_real_escape_string($record['Rekening'])."' 
                   AND TijdelijkeRekeningmutaties.bankTransactieId = '".$record['bankTransactieId']."'
                   AND TijdelijkeRekeningmutaties.change_user = '$USR'";
  $db2->SQL($query);
  $kosten=$db2->lookupRecord();
  $record['Totaal'] += $kosten['kosten'];
  
  $query = "SELECT SUM(Bedrag) as rente FROM TijdelijkeRekeningmutaties WHERE Omschrijving = '".mysql_real_escape_string($record['FondsOmschrijving'])."' AND 
                   TijdelijkeRekeningmutaties.Grootboekrekening IN('RENOB','RENME') AND TijdelijkeRekeningmutaties.Rekening = '".mysql_real_escape_string($record['Rekening'])."' AND TijdelijkeRekeningmutaties.change_user = '$USR'";
  $db2->SQL($query);
  $rente=$db2->lookupRecord();
  
  $query='SELECT Portefeuille FROM Rekeningen WHERE Rekening=\''.mysql_real_escape_string($record['Rekening']).'\' AND consolidatie=0';
  $db2->SQL($query);
  $portfeuille=$db2->lookupRecord(); 

  $query='SELECT ifnull(SUM(Aantal),0) as aantal FROM Rekeningmutaties WHERE
Rekening IN (SELECT Rekening FROM Rekeningen WHERE Portefeuille =\''.mysql_real_escape_string($portfeuille['Portefeuille']).'\' AND consolidatie=0 )
AND Fonds=\''.mysql_real_escape_string($record['Fonds']).'\' AND Boekdatum >= \''.date('Y',$date).'-01-01\' AND Boekdatum < \''.date('Y-m-d',$date).'\'';
  $db2->SQL($query);
  $aantal=$db2->lookupRecord();
  $aantalVoorTrans=$aantal['aantal'];
  
  $record['Totaal'] += $rente['rente'];
  $record['Regel']=$row;
  
  $record['Rekening']=substr($record['Rekening'],0,strlen($record['Rekening'])-3);

  if($pdf->rapport_layout == 13)
  {
    if(key_exists($record['Transactietype'],$trans))
    {
      $record['Transactietype']=$trans[$record['Transactietype']];
    }
    $aantalTxt=formatGetal($record['Aantal'],0);
    $aantalNaTrans=$aantalVoorTrans+$record['Aantal'];
    $aantalNaTransTxt=formatGetal($aantalNaTrans);
  }
  else 
  {
    $aantalTxt = formatGetal($record['Aantal'],2);
    $aantalNaTrans=$aantalVoorTrans+$record['Aantal'];
    $aantalNaTransTxt=formatGetal($aantalNaTrans,2);
  }
  
 

  if(strlen($record['Client'])>13)
    $client=substr($record['Client'],0,11)."..";
  else 
    $client=$record['Client'];  
    
    if($fill==true)
		{
		  $pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
      $fill=false;
    }
    else
    {
      $pdf->fillCell=array();
      $fill=true;
    }
  
  $pdf->Row(array($record['Regel'],
                  $client,
                  $record['Portefeuille'],
                  date('dmy',$date),
                  $record['Transactietype'],
                  $aantalTxt,$record['Fonds'],
                  $aantalNaTransTxt,
                  formatGetal($record['Fondskoers'],2),
                  formatGetal($record['Valutakoers'],4),
                  formatGetal($record['Totaal'],2),
                  formatGetal($rente['rente'],2),
                  $record['Valuta'])
                  );
                  


  if($record['OptieBovenliggendFonds'])
  {
    
//    $pdf->Row(array('','','','','',$record['OptieBovenliggendFonds']));
  }
  
  $row++;
}

		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	//	$pdf->Output("fondsmutaties.pdf","D");
  $pdf->Output();

?>
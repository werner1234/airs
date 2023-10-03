<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/04/11 15:19:54 $
File Versie					: $Revision: 1.7 $

$Log: Obligatieoverzicht.php,v $
Revision 1.7  2018/04/11 15:19:54  rvv
*** empty log message ***

Revision 1.6  2017/05/26 16:44:29  rvv
*** empty log message ***

Revision 1.5  2015/09/26 15:57:19  rvv
*** empty log message ***

Revision 1.4  2014/11/15 19:05:41  rvv
*** empty log message ***

Revision 1.3  2014/11/12 16:41:04  rvv
*** empty log message ***

Revision 1.2  2014/11/08 18:36:24  rvv
*** empty log message ***

Revision 1.1  2014/11/01 22:06:25  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");

class Obligatieoverzicht
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function Obligatieoverzicht( $selectData ) {

	  global $USR;
		$this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->orderby = "Clienten.Client";

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "geaggregeerd";
		$this->pdf->rapport_titel = "Optie overzicht.";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);

		$this->pdf->vandatum = $this->selectData['datumVan'];
		$this->pdf->tmdatum  = $this->selectData['datumTm'];








	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		global $__appvar;
		$rapportJaar = date("Y",$this->selectData['datumTm']);

		$selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();
    $portefeuilleList=array_keys($portefeuilles);
		$extraquery=" Rekeningen.Portefeuille IN('".implode("','",$portefeuilleList)."') AND ";
		$this->extraquery=$extraquery;
/*

  */

$dbdatum=jul2db($this->selectData['datumTm']);	
$db=new DB();
$query="SELECT
sum(Rekeningmutaties.Aantal) as Positie,
Fondsen.Fonds,
Fondsen.FondsImportCode,
Fondsen.ISINCode,
Fondsen.fondssoort,
Fondsen.Valuta,
Fondsen.Fondseenheid,
Fondsen.inflatieGekoppeld,
Fondsen.Renteperiode,
Fondsen.Rente30_360,
Fondsen.variabeleCoupon,
Fondsen.Lossingsdatum,
Fondsen.EindDatum,
Fondsen.Rentedatum,
Fondsen.Rentepercentage,
Fondsen.EersteRentedatum,
Fondsen.rating
FROM
Fondsen 
JOIN Rekeningmutaties ON Fondsen.Fonds=Rekeningmutaties.Fonds
JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
WHERE 
YEAR(Rekeningmutaties.Boekdatum) = '".$rapportJaar."' AND
Rekeningmutaties.Verwerkt = '1' AND 
Rekeningmutaties.Boekdatum <= '$dbdatum' AND 
$extraquery
Fondsen.fondssoort IN('OBL','OVERIG') AND 
(Fondsen.EindDatum>'$dbdatum' OR Fondsen.EindDatum='0000-00-00' OR Fondsen.EindDatum IS NULL) AND 
(Fondsen.Lossingsdatum > '$dbdatum' OR Fondsen.Lossingsdatum='0000-00-00' OR Fondsen.Lossingsdatum IS NULL)
GROUP BY Rekeningmutaties.Fonds
Having Positie > 0
ORDER BY Fonds";
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord())
{
  $fondsen[$data['Fonds']]=$data;
}

$headerVelden=array('Fonds'=>'Fonds',
'ISINCode'=>'ISINCode',
'fondssoort'=>'Fondssoort',
'rating'=>'Rating',
'Valuta'=>'Valuta',
'AantalPtf'=>'Aantal Ptf',
'Positie'=>'Positie',
'Koers'=>'Koers',
'Waarde'=>'Waarde',
'OpgelopenRente'=>'Opgelopen rente VV',
'WaardeIncl'=>'Waarde EUR incl Opg Rente',
'Rentepercentage'=>'Actuele rentepercentage',
'VolgendeRentedatum'=>'Volgende Coupondatum',
'Lossingsdatum'=>'Lossingsdatum',
'Renteperiode'=>'Renteperiode');
/*
- Velden:
   - Fonds
   - ISIN-code
   - Valuta
   - Aantal Ptf (aantal portefeuilles met positie)
   - Positie (totale positie selectie)
   - Koers
   - Waarde
   - Opgelopen rente
   - Waarde EUR incl Opg Rente
   - Actueel rente% (zie vervalkalender obligaties)
   - Volgende coupondatum (zie vervalkalender obligaties)
   - Lossingsdatum (zie vervalkalender obligaties)
   */


$header=array();
foreach($headerVelden as $veld)
{
  $header[]=array($veld,'header');
}
$this->pdf->excelData[]=$header;

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / count($fondsen);
		}

foreach($fondsen as $fonds=>$fondsData)
{
  
  			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
			}
      
  $query="SELECT
ROUND(SUM(Rekeningmutaties.Aantal),0) AS Positie,
Rekeningen.Portefeuille 
FROM Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
WHERE 
YEAR(Rekeningmutaties.Boekdatum) = '".$rapportJaar."' AND $extraquery
Rekeningmutaties.Verwerkt = '1' AND 
Rekeningmutaties.Boekdatum <= '$dbdatum' AND 
Rekeningmutaties.Fonds='".mysql_real_escape_string($fonds)."'  
GROUP BY Rekeningen.Portefeuille
HAVING Positie <> 0
ORDER BY Fonds";
  $db->SQL($query); 
  $db->Query();
  $aantal=$db->records();
  $fondsen[$fonds]['AantalPtf']=$aantal;
  //echo "$aantal $query <br>\n";

  $renteBerekenen=0;
  $rente=getRenteParameters($fonds, $dbdatum);

  if($rente['Rentepercentage'])
  {
    $fondsen[$fonds]['Rentepercentage']=$rente['Rentepercentage'];
    $renteBerekenen=$rente['rentemethodiek'];
  }

  $koers=getRentePercentage($fonds,$dbdatum);
  if($koers['Rentepercentage'])
    $fondsen[$fonds]['Rentepercentage']=$koers['Rentepercentage'];


  $fondsen[$fonds]['Frequentie']=$fondsData['Renteperiode']/12;
  $rentedatumJul=db2jul($fondsData['Rentedatum']);
  $renteDag=date('d',$rentedatumJul);
  $renteMaand=date('m',$rentedatumJul);
  $fondsen[$fonds]['Rentedatum']=$renteDag.'-'.$renteMaand;
  $jaar=substr($fondsData['EersteRentedatum'],0,4);

  $timer=0;
  $start= db2jul($fondsData['EersteRentedatum']);
  if($fondsData['Renteperiode']>0 && $start > 1)
  {
    $eind=$this->selectData['datumTm'];//mktime(0,0,0,$renteMaand,$renteDag,$jaar);
    $timer=$start;
    $maanden=0;
    while($timer<=$eind)
    {
      $maanden+=$fondsData['Renteperiode'];
      $timer=mktime(0,0,0,$renteMaand+$maanden,$renteDag,$jaar);
    }
  }
  $fondsen[$fonds]['VolgendeRentedatum']=adodb_date('d-m-Y',$timer);
  if($fondsen[$fonds]['VolgendeRentedatum']=='01-01-1970')
    $fondsen[$fonds]['VolgendeRentedatum']='';
  //$fondsen[$fonds]['EindDatum']=adodb_date('d-m-Y',adodb_db2jul($fondsen[$fonds]['EindDatum']));
  $fondsen[$fonds]['Lossingsdatum']=adodb_date('d-m-Y',adodb_db2jul($fondsen[$fonds]['Lossingsdatum']));
  if($fondsen[$fonds]['Lossingsdatum']=='01-01-1970')
    $fondsen[$fonds]['Lossingsdatum']='';
  $valutaKoers=getValutaKoers($fondsData['Valuta'],$dbdatum);
  
  $fondsen[$fonds]['Koers']=$this->getFondsKoers($fonds,$dbdatum);
  $fondsen[$fonds]['Waarde']=round($fondsData['Positie']*$fondsen[$fonds]['Koers']*$fondsData['Fondseenheid'],2);
  if(substr($dbdatum,5,5)=='01-01')
    $min1dag=1;
  else
    $min1dag=0;  
    
	$fondsData['fonds']						= $fondsData['Fonds'];
	$fondsData['totaalAantal'] 					= $fondsData['Positie'];
	$fondsData['rentedatum'] 			= $fondsData['Rentedatum'];
	$fondsData['eersteRentedatum'] = $fondsData['EersteRentedatum'];
	$fondsData['renteperiode'] 		= $fondsData['Renteperiode'];    
  $rentebedrag = renteOverPeriode($fondsData, $dbdatum, $min1dag, $renteBerekenen);
  $fondsen[$fonds]['OpgelopenRente']=round($rentebedrag,2);
  $fondsen[$fonds]['WaardeIncl']=round(($fondsen[$fonds]['Waarde']+$rentebedrag)*$valutaKoers,2);
  
}

foreach($fondsen as $fonds=>$fondsData)
{
  $row=array();
  foreach($headerVelden as $key=>$value)
  {
    $row[]=$fondsData[$key];

  }
  $this->pdf->excelData[]=$row;
}

		if($this->progressbar)
			$this->progressbar->hide();
	}

	function getFondsKoers($fonds,$datum)
	{
	  $db=new DB();
	  $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
	  $db->SQL($query);
	  $koers=$db->lookupRecord();
	  return $koers['Koers'];
	}

}
?>
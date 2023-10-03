<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.2 $

$Log: RapportOIB_L31.php,v $
Revision 1.2  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.1  2014/01/26 15:08:14  rvv
*** empty log message ***

Revision 1.3  2012/06/23 15:20:24  rvv
*** empty log message ***

Revision 1.2  2012/06/20 18:11:09  rvv
*** empty log message ***

Revision 1.1  2012/06/17 13:04:11  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIB_L31
{
	function RapportOIB_L31($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		if($this->pdf->rapport_OIB_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_OIB_titel;
		else
			$this->pdf->rapport_titel = "Onderverdeling in beleggingscategorie";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
		$this->pdf->underlinePercentage=0.8;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }


	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}


	function writeRapport()
	{
		global $__appvar;
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();

		// voor data
		$this->pdf->widthB = array(40,35,25,25,25,15,115);
		$this->pdf->alignB = array('L','L','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = array(40,35,25,25,25,15,115);
		$this->pdf->alignA = array('L','L','R','R','R','R','R');


		$this->pdf->AddPage();

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		//getTypeGrafiekData($object,$type,$extraWhere='',$items=array())
		getTypeGrafiekData($this,'Beleggingscategorie');
		getTypeGrafiekData($this,'Valuta');
		//listarray($this->pdf->grafiekData);
		//listarray($this->pdf->veldOmschrijvingen);
		$this->pdf->setY(40);
		$this->pdf->setWidths(array(160,35,35,25,25));
		$this->pdf->setAligns(array('L','L','L','R','R'));
  	$this->pdf->CellBorders = array('','U','U','U','U');
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Valuta','Beleggingscategorie','in EUR','in %'));
    $this->pdf->CellBorders = array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $totaal=0;
    $totaalProcent=0;
  	foreach ($this->pdf->grafiekData['Valuta']['port']['waarde'] as $valuta=>$waarde)
		{
		  $valData=array();
		  $query="SELECT Beleggingscategorie,BeleggingscategorieOmschrijving,SUM(actuelePortefeuilleWaardeEuro) as  actuelePortefeuilleWaardeEuro
		          FROM TijdelijkeRapportage
		          WHERE valuta='$valuta' AND rapportageDatum ='".$this->rapportageDatum."' AND portefeuille = '".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
		          GROUP BY Beleggingscategorie
		          ORDER BY BeleggingscategorieVolgorde";
		  $DB->SQL($query);
		  $DB->Query();
		  while($data=$DB->nextRecord())
		  {
		    if($data['Beleggingscategorie']=='')
		      $data['Beleggingscategorie']="Overige";
		    if($data['BeleggingscategorieOmschrijving']=='')
		      $data['BeleggingscategorieOmschrijving']="Overige";
         $valData[]=$data;
		  }


 		  foreach ($valData as $row=>$data)
		  {
		    if($row==0)
		    {
		      $y=$this->pdf->getY();
		      $this->pdf->SetFont($this->pdf->rapport_font,'BI',$this->pdf->rapport_fontsize);
		      $this->pdf->row(array('',$this->pdf->veldOmschrijvingen['Valuta'][$valuta]));
		      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		      $this->pdf->setY($y);
		    }

		    $this->pdf->row(array('','',
		                        $data['BeleggingscategorieOmschrijving'],
		                        $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),
		                        $this->formatGetal($data['actuelePortefeuilleWaardeEuro']/$totaalWaarde*100,1)));

         if($row==(count($valData)-1))
         {
           $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
           $this->pdf->CellBorders = array('','','','TS');
		  		 $this->pdf->row(array('','','',$this->formatGetal($waarde,0),$this->formatGetal($this->pdf->grafiekData['Valuta']['port']['procent'][$valuta]*100,1)));
		  		 $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		  		 $this->pdf->CellBorders = array();
		  		 $this->pdf->ln(2);
         }
		  }
		  $totaal+=$waarde;
		  $totaalProcent+=$this->pdf->grafiekData['Valuta']['port']['procent'][$valuta];
		}

    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array('','','',array('TS','UU'));
    $this->pdf->row(array('','Totaal','',$this->formatGetal($totaal,0),$this->formatGetal($totaalProcent*100,1)));
    $this->pdf->CellBorders = array();


		$this->pdf->setXY(23,130);
	  //PieChart($this->pdf,50, 45, $this->pdf->grafiekData['Valuta']['grafiek'], '%l (%p)',$this->pdf->grafiekData['Valuta']['grafiekKleur']);


	  $this->pdf->setY(40);
		$this->pdf->setWidths(array(5,35,35,25,25));
		$this->pdf->setAligns(array('L','L','L','R','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array('','U','U','U','U');
    $this->pdf->row(array('','Beleggingscategorie','Valutasoort','in EUR','in %'));
    $this->pdf->CellBorders = array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $totaal=0;
    $totaalProcent=0;

    //$this->pdf->veldOmschrijvingen['Beleggingscategorie']['geenWaarden']="Liquiditeiten";
   	foreach ($this->pdf->grafiekData['Beleggingscategorie']['port']['waarde'] as $categorie=>$waarde)
		{
		 // if($categorie=='geenWaarden')
		//    $realCat='';
		//  else
		//    $realCat=$categorie;

		  $catData=array();
		  $query="SELECT valuta,valutaOmschrijving,SUM(actuelePortefeuilleWaardeEuro) as  actuelePortefeuilleWaardeEuro
		          FROM TijdelijkeRapportage
		          WHERE Beleggingscategorie='$categorie' AND rapportageDatum ='".$this->rapportageDatum."' AND portefeuille = '".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
		          GROUP BY valuta
		          ORDER BY valutaVolgorde";
		  $DB->SQL($query);
		  $DB->Query();
		  while($data=$DB->nextRecord())
         $catData[]=$data;

      if(count($catData)==0)
        $catData[]=array($categorie);

		  foreach ($catData as $row=>$data)
		  {
		    if($row==0)
		    {
		      $y=$this->pdf->getY();
		      $this->pdf->SetFont($this->pdf->rapport_font,'BI',$this->pdf->rapport_fontsize);
		      $this->pdf->row(array('',$this->pdf->veldOmschrijvingen['Beleggingscategorie'][$categorie]));
		      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		      $this->pdf->setY($y);
		    }

		    $this->pdf->row(array('','',
		                        $data['valutaOmschrijving'],
		                        $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),
		                        $this->formatGetal($data['actuelePortefeuilleWaardeEuro']/$totaalWaarde*100,1)));

         if($row==(count($catData)-1))
         {
           $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
           $this->pdf->CellBorders = array('','','','TS');
		  		 $this->pdf->row(array('','','',$this->formatGetal($waarde,0),$this->formatGetal($this->pdf->grafiekData['Beleggingscategorie']['port']['procent'][$categorie]*100,1)));
		  		 $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		  		 $this->pdf->CellBorders = array();
		  		 $this->pdf->ln(2);
         }
		  }
		  $totaal+=$waarde;
		  $totaalProcent+=$this->pdf->grafiekData['Beleggingscategorie']['port']['procent'][$categorie];
		}
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array('','','',array('TS','UU'));
    $this->pdf->row(array('','Totaal','',$this->formatGetal($totaal,0),$this->formatGetal($totaalProcent*100,1)));
    $this->pdf->CellBorders = array();

    $this->pdf->setXY(170,130);
	  //PieChart($this->pdf,50, 45, $this->pdf->grafiekData['Beleggingscategorie']['grafiek'], '%l (%p)',$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur']);
    $this->pdf->ln(8);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


/*
if(isset($this->pdf->grafiekData['Beleggingscategorie']['grafiek']['Overige']))
{
  $this->pdf->grafiekData['Beleggingscategorie']['grafiek']['Liquiditeiten']=$this->pdf->grafiekData['Beleggingscategorie']['grafiek']['Overige'];
  unset($this->pdf->grafiekData['Beleggingscategorie']['grafiek']['Overige']);
}
*/


$diameter = 35;
$hoek = 30;
$dikte = 10;
$Xas= 75;
$yas= 140;
//print_r($grafiekData);exit;
foreach ($this->pdf->grafiekData['Beleggingscategorie']['grafiek'] as $omschrijving=>$waarde)
{
  $grafiekData['OIB']['Omschrijving'][]=$omschrijving;
  $grafiekData['OIB']['Percentage'][]=$waarde;
}

foreach ($this->pdf->grafiekData['Valuta']['grafiek'] as $omschrijving=>$waarde)
{
  $grafiekData['OIV']['Omschrijving'][]=$omschrijving;
  $grafiekData['OIV']['Percentage'][]=$waarde;
}
//listarray($this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur']);

$this->pdf->set3dLabels($grafiekData['OIB']['Omschrijving'],$Xas,$yas,$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur']);
$this->pdf->Pie3D($grafiekData['OIB']['Percentage'],$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur'],$Xas,$yas,$diameter,$hoek,$dikte,"Beleggingscategorie");

$this->pdf->set3dLabels($grafiekData['OIV']['Omschrijving'],$Xas+155,$yas,$this->pdf->grafiekData['Valuta']['grafiekKleur']);
$this->pdf->Pie3D($grafiekData['OIV']['Percentage'],$this->pdf->grafiekData['Valuta']['grafiekKleur'],$Xas+155,$yas,$diameter,$hoek,$dikte,"Valuta");

  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}
}
?>
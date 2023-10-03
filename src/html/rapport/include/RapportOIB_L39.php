<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.13 $

$Log: RapportOIB_L39.php,v $
Revision 1.13  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.12  2014/01/18 17:27:23  rvv
*** empty log message ***

Revision 1.11  2013/07/10 16:01:24  rvv
*** empty log message ***

Revision 1.10  2013/04/10 15:58:01  rvv
*** empty log message ***

Revision 1.9  2013/03/31 12:35:14  rvv
*** empty log message ***

Revision 1.8  2013/01/27 14:14:24  rvv
*** empty log message ***

Revision 1.7  2013/01/23 16:45:37  rvv
*** empty log message ***

Revision 1.6  2012/10/21 12:44:08  rvv
*** empty log message ***

Revision 1.5  2012/10/17 09:16:53  rvv
*** empty log message ***

Revision 1.4  2012/09/23 08:51:44  rvv
*** empty log message ***

Revision 1.3  2012/09/19 16:53:18  rvv
*** empty log message ***

Revision 1.2  2012/06/20 18:11:09  rvv
*** empty log message ***

Revision 1.1  2012/06/17 13:04:11  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/ATTberekening_L39.php");

class RapportOIB_L39
{
	function RapportOIB_L39($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Onderverdeling in beleggingscategorie en sector";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
		$this->pdf->underlinePercentage=0.8;
    
    $this->eerste='Beleggingscategorie';
    $this->eersteWhere='';
    $this->eersteWhereATT='';
    $this->tweede='Beleggingssector';
    $this->tweedeWhere="AND Beleggingscategorie='AAND' ";
    $this->tweedeWhereATT="AND Beleggingscategorien.Beleggingscategorie='AAND' ";
    $this->tweedeTitel='Sectorverdeling aandelen';
    
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
    $this->pdf->widthB = array(40, 35, 25, 25, 25, 15, 115);
    $this->pdf->alignB = array('L', 'L', 'R', 'R', 'R', 'R', 'R');
		// voor kopjes
		$this->pdf->widthA = $this->pdf->widthB;
		$this->pdf->alignA = $this->pdf->alignB;


		$this->pdf->AddPage();
    
    $att = new ATTberekening_L39($this);
    
    if($this->eerste=='Beleggingscategorie')
      $this->eerstePerfData=$att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'EUR','categorie',$this->eersteWhereATT);
    if($this->tweede=='Beleggingssector')
      $this->tweedePerfData=$att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'EUR','sector',$this->tweedeWhereATT);   

    if($this->eerste=='Regio')
      $this->eerstePerfData=$att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'EUR','regio',$this->eersteWhereATT);
    if($this->tweede=='Valuta')
      $this->tweedePerfData=$att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'EUR','valuta',$this->tweedeWhereATT);  
      
  
      
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
    

    
		getTypeGrafiekData($this,$this->eerste,$this->eersteWhere);
		getTypeGrafiekData($this,$this->tweede,$this->tweedeWhere);
		//listarray($this->pdf->grafiekData);
		//listarray($this->pdf->veldOmschrijvingen);
		$this->pdf->setY(45);
    if($__appvar['bedrijf']=='TEST')
    {
      $this->pdf->setWidths(array(165,55,20,12,23));
    }
    else
    {
      $this->pdf->setWidths(array(165,55,20,10,25));
    }
    
		$this->pdf->setAligns(array('L','L','R','R','R'));
  	$this->pdf->CellBorders = array('','U','U','U','U');
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',vertaalTekst($this->tweedeTitel,$this->pdf->rapport_taal),vertaalTekst('in euro',$this->pdf->rapport_taal),
                             vertaalTekst('in %',$this->pdf->rapport_taal),vertaalTekst('rendement',$this->pdf->rapport_taal).' %'));
    $this->pdf->CellBorders = array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $totaal=0;
    $totaalProcent=0;
    $skipSecondPie=false;
    $alleFondsen=array();
    $alleRekeningen=array();
  	foreach ($this->pdf->grafiekData[$this->tweede]['port']['waarde'] as $valuta=>$waarde)
		{
		  /*
		  $fondsen=array_values($this->pdf->grafiekData[$this->tweede]['port']['fondsen'][$valuta]);
      $rekeningen=array_values($this->pdf->grafiekData[$this->tweede]['port']['rekeningen'][$valuta]);
      foreach($fondsen as $fonds)
        $alleFondsen[]=$fonds;
      foreach($rekeningen as $rekening)
        $alleRekeningen[]=$rekening;   
			$perfInput=array('fondsen'=>$fondsen,'rekeningen'=>$rekeningen);
    
      $perf=$att->fondsPerformance($perfInput,$this->rapportageDatumVanaf,$this->rapportageDatum,false,true);
      */

		  //$perfTxt=$this->formatGetal($this->tweedePerfData[$valuta]['procent'],1);

      $perfTxt=$this->formatGetal($this->tweedePerfData[$valuta]['procent'],2);
      
      if($waarde < 0)
        $skipSecondPie=true;
		  $this->pdf->row(array('',vertaalTekst($this->pdf->veldOmschrijvingen[$this->tweede][$valuta],$this->pdf->rapport_taal),
		                        $this->formatGetal($waarde,0),
		                        $this->formatGetal($waarde/$totaalWaarde*100,1),
                            $perfTxt));
		  $totaal+=$waarde;
		  $totaalProcent+=($waarde/$totaalWaarde);//$this->pdf->grafiekData[$this->tweede]['port']['procent'][$valuta];
		}
    //$perfInput=array('fondsen'=>$alleFondsen,'rekeningen'=>$alleRekeningen);
   // $perf=$att->fondsPerformance($perfInput,$this->rapportageDatumVanaf,$this->rapportageDatum,false,true);
		$perfTxt=$this->formatGetal($this->tweedePerfData['totaal']['procent'],2);
    $perfTxt='';

    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array('','',array('TS'),array('TS'));
    $this->pdf->row(array('',vertaalTekst('Totaal',$this->pdf->rapport_taal),$this->formatGetal($totaal,0),$this->formatGetal($totaalProcent*100,1),$perfTxt));
    $this->pdf->CellBorders = array();
		$this->pdf->setXY(23,130);
	 
	  $this->pdf->setY(45);
    if($__appvar['bedrijf']=='TEST')
    {
      $this->pdf->setWidths(array(10, 55, 20, 12, 23));
    }
    else
    {
      $this->pdf->setWidths(array(10, 55, 20, 10, 25));
    }
		$this->pdf->setAligns(array('L','L','R','R','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array('','U','U','U','U');
    $this->pdf->row(array('',vertaalTekst($this->eerste,$this->pdf->rapport_taal),vertaalTekst('in euro',$this->pdf->rapport_taal),
                             vertaalTekst('in %',$this->pdf->rapport_taal),vertaalTekst('rendement',$this->pdf->rapport_taal).' %'));
    $this->pdf->CellBorders = array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $totaal=0;
    $totaalProcent=0;
    $skipFirstPie=false;
    $alleFondsen=array();
    $alleRekeningen=array();
   
  	foreach ($this->pdf->grafiekData[$this->eerste]['port']['waarde'] as $categorie=>$waarde)
		{
		  /*
		  $fondsen=array_values($this->pdf->grafiekData[$this->eerste]['port']['fondsen'][$categorie]);
      $rekeningen=array_values($this->pdf->grafiekData[$this->eerste]['port']['rekeningen'][$categorie]);
      foreach($fondsen as $fonds)
        $alleFondsen[]=$fonds;
      foreach($rekeningen as $rekening)
        $alleRekeningen[]=$rekening;   
			$perfInput=array('fondsen'=>$fondsen,'rekeningen'=>$rekeningen);
     
      $perf=$att->fondsPerformance($perfInput,$this->rapportageDatumVanaf,$this->rapportageDatum,false,true);
      */
		  $perfTxt=$this->formatGetal($this->eerstePerfData[$categorie]['procent'],2);
      if($categorie=='Liquiditeiten')
        $perfTxt='';
		  //$perf=$this->formatGetal($this->eerstePerfData[$categorie]['procent'],1);
		  if($waarde < 0)
        $skipFirstPie=true;
		  $this->pdf->row(array('',vertaalTekst($this->pdf->veldOmschrijvingen[$this->eerste][$categorie],$this->pdf->rapport_taal),
		                        $this->formatGetal($waarde,0),
		                        $this->formatGetal($waarde/$totaalWaarde*100,1),
                            $perfTxt));
		  $totaal+=$waarde;
		  $totaalProcent+=$this->pdf->grafiekData[$this->eerste]['port']['procent'][$categorie];
		}
    //$perfInput=array('fondsen'=>$alleFondsen,'rekeningen'=>$alleRekeningen);
   //$perf=$att->fondsPerformance($perfInput,$this->rapportageDatumVanaf,$this->rapportageDatum,false,true);
		$perfTxt=$this->formatGetal($this->eerstePerfData['totaal']['procent'],2);
    
    //if($this->eerste=='Beleggingscategorie')
      $perfTxt='';

		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array('','',array('TS'),array('TS'));//,array('TS')
    $this->pdf->row(array('',vertaalTekst('Totaal',$this->pdf->rapport_taal),$this->formatGetal($totaal,0),$this->formatGetal($totaalProcent*100,1),$perfTxt));
    $this->pdf->CellBorders = array();
    $this->pdf->setXY(170,130);
	  $this->pdf->ln(8);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);



$diameter = 35;
$hoek = 30;
$dikte = 10;
$Xas= 75;
$yas= 140;
//print_r($grafiekData);exit;
foreach ($this->pdf->grafiekData[$this->eerste]['grafiek'] as $omschrijving=>$waarde)
{
  $grafiekData['eerste']['Omschrijving'][]=$omschrijving;
  $grafiekData['eerste']['Percentage'][]=$waarde;
}

foreach ($this->pdf->grafiekData[$this->tweede]['grafiek'] as $omschrijving=>$waarde)
{
  $grafiekData['tweede']['Omschrijving'][]=$omschrijving;
  $grafiekData['tweede']['Percentage'][]=$waarde;
}
//listarray($this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur']);

if($skipFirstPie == false)
{
  $this->pdf->set3dLabels($grafiekData['eerste']['Omschrijving'],$Xas,$yas,$this->pdf->grafiekData[$this->eerste]['grafiekKleur']);
  $this->pdf->Pie3D($grafiekData['eerste']['Percentage'],$this->pdf->grafiekData[$this->eerste]['grafiekKleur'],$Xas,$yas,$diameter,$hoek,$dikte,vertaalTekst($this->eerste,$this->pdf->rapport_taal));
}
if($skipSecondPie == false)
{
  $this->pdf->set3dLabels($grafiekData['tweede']['Omschrijving'],$Xas+155,$yas,$this->pdf->grafiekData[$this->tweede]['grafiekKleur']);
  $this->pdf->Pie3D($grafiekData['tweede']['Percentage'],$this->pdf->grafiekData[$this->tweede]['grafiekKleur'],$Xas+155,$yas,$diameter,$hoek,$dikte,vertaalTekst($this->tweedeTitel,$this->pdf->rapport_taal));
}
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}
}
?>
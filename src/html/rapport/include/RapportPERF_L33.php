<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2015/12/16 17:07:25 $
File Versie					: $Revision: 1.15 $

$Log: RapportPERF_L33.php,v $
Revision 1.15  2015/12/16 17:07:25  rvv
*** empty log message ***

Revision 1.14  2014/01/09 17:28:55  rvv
*** empty log message ***

Revision 1.13  2012/12/30 14:27:12  rvv
*** empty log message ***

Revision 1.12  2012/08/22 15:46:00  rvv
*** empty log message ***

Revision 1.11  2012/04/21 15:38:14  rvv
*** empty log message ***

Revision 1.10  2011/07/27 16:27:15  rvv
*** empty log message ***

Revision 1.9  2011/04/09 14:35:27  rvv
*** empty log message ***

Revision 1.8  2011/04/03 17:15:40  rvv
*** empty log message ***

Revision 1.7  2011/04/03 08:35:46  rvv
*** empty log message ***

Revision 1.6  2011/03/30 20:18:43  rvv
*** empty log message ***

Revision 1.5  2011/03/26 16:52:07  rvv
*** empty log message ***

Revision 1.4  2011/03/23 17:01:48  rvv
*** empty log message ***

Revision 1.3  2011/03/18 15:02:38  rvv
*** empty log message ***

Revision 1.2  2011/03/10 07:10:09  rvv
*** empty log message ***

Revision 1.1  2011/02/13 17:50:29  rvv
*** empty log message ***

Revision 1.1  2011/02/06 14:36:59  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/rapportATTberekening.php");

class RapportPERF_L33
{
	function RapportPERF_L33($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Ontwikkeling vermogen";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
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



	function writeRapport()
	{
		global $__appvar;
		$query = "SELECT Portefeuilles.startDatum, Portefeuilles.startdatumMeerjarenrendement, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
		$this->pdf->underlinePercentage=0.5;

		if($this->pdf->rapport_datum > db2jul($portefeuilledata['startDatum']))
	   	$rapportageStartJaar= date("Y-01-01",$this->pdf->rapport_datum);
	  else
	   	$rapportageStartJaar=substr($portefeuilledata['startDatum'],0,10);
      
      
		$startDatumTekst=date("j",$this->pdf->rapport_datumvanaf)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datumvanaf);
    $rapDatumTekst=date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum);
    $startJaarDatumTekst=date("j",db2jul($rapportageStartJaar))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($rapportageStartJaar))],$this->pdf->rapport_taal)." ".date("Y",db2jul($rapportageStartJaar));

		$this->pdf->AddPage();
		$this->pdf->templateVars['PERFPaginas'] = $this->pdf->customPageNo;
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->ln();
		$this->pdf->SetWidths(array(15,40,35,35, 35, 35,35,35,15));
		$this->pdf->SetAligns(array('L','L','R','R','R','R','R','R','R'));
		$this->pdf->CellBorders = array('U','U','U','U','U','U','U','U','U','U');
		//$this->pdf->row(array('','Maand',"Beginwaarde\n(in ".$this->pdf->rapportageValuta.")","Storting/\nonttrekking","Resultaat\n(in ".$this->pdf->rapportageValuta.")","Eindwaarde\n(in ".$this->pdf->rapportageValuta.")","Performance\nmaand","Cumulatieve\nperformance",''));


		$this->pdf->row(array('',vertaalTekst("Maand",$this->pdf->rapport_taal),
		vertaalTekst("Beginwaarde",$this->pdf->rapport_taal)."\n(".vertaalTekst("in",$this->pdf->rapport_taal)." ".$this->pdf->rapportageValuta.")",
		vertaalTekst("Storting",$this->pdf->rapport_taal)."/\n".vertaalTekst("onttrekking",$this->pdf->rapport_taal),
		vertaalTekst("Resultaat",$this->pdf->rapport_taal)."\n(".vertaalTekst("in",$this->pdf->rapport_taal)." ".$this->pdf->rapportageValuta.")",
		vertaalTekst("Eindwaarde",$this->pdf->rapport_taal)."\n(".vertaalTekst("in",$this->pdf->rapport_taal)." ".$this->pdf->rapportageValuta.")",
		vertaalTekst("Performance",$this->pdf->rapport_taal)."\n".vertaalTekst("maand",$this->pdf->rapport_taal),
		vertaalTekst("Cumulatieve",$this->pdf->rapport_taal)."\n".vertaalTekst("Performance",$this->pdf->rapport_taal),''));


    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    $startDatum=$portefeuilledata['startDatum'];
    if(db2jul($portefeuilledata['startdatumMeerjarenrendement']) > db2jul($portefeuilledata['startDatum']))
      $startDatum=$portefeuilledata['startdatumMeerjarenrendement'];

    $index = new indexHerberekening();
    $indexWaarden = $index->getWaarden($startDatum,$this->rapportageDatum,array($this->portefeuille,$this->pdf->portefeuilles),$this->pdf->portefeuilledata['SpecifiekeIndex'],'maanden',$this->pdf->rapportageValuta);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);

    $rapportageJaar=date("Y",$this->pdf->rapport_datum);
    $jaarPerf=100;
    $jaarReset=false;
    $totalen=array();
    foreach ($indexWaarden as $id=>$waarden)
    {
      //listarray($waarden);
      $periodeJaar=substr($waarden['periodeForm'],-4);
      if($rapportageJaar > $periodeJaar)
      {
        if($periodeJaar <> $lastPeriodeJaar)
          $jaarPerf=100;

    	  $jaarPerf =$jaarPerf*($waarden['performance']+100)/100;

        if(!isset($jaren[$periodeJaar]['waardeBegin']))
          $jaren[$periodeJaar]['waardeBegin']=$waarden['waardeBegin'];

        $jaren[$periodeJaar]['waardeHuidige']=($waarden['waardeHuidige']);
        $jaren[$periodeJaar]['stortingen']+=($waarden['stortingen']-$waarden['onttrekkingen']);
        $jaren[$periodeJaar]['resultaatVerslagperiode']+=$waarden['resultaatVerslagperiode'];
        $jaren[$periodeJaar]['performance']=$jaarPerf-100;
        $jaren[$periodeJaar]['index']=$waarden['index'];
        $lastPeriodeJaar=$periodeJaar;
      }
      else
      {
        if($jaarReset==false)
        {
          $jaarReset=true;
          $jaarPerf=100;
        }

        $jaarPerf =$jaarPerf*($waarden['performance']+100)/100;
        //echo "$jaarPerf =$jaarPerf*(".$waarden['performance']."+100)/100; <br>\n";
        $julMaand=db2jul(substr($waarden['periode'],12,10));
        $maand=vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$julMaand)],$this->pdf->rapport_taal)." ".date("Y",$julMaand);
        $this->pdf->row(array('',$maand,
        $this->formatGetal($waarden['waardeBegin'],0),
        $this->formatGetal($waarden['stortingen']-$waarden['onttrekkingen'],0),
        $this->formatGetal($waarden['resultaatVerslagperiode'],0),
        $this->formatGetal($waarden['waardeHuidige'],0),
        $this->formatGetal($waarden['performance'],1),
        $this->formatGetal($jaarPerf-100,1),''));
        $totalen['stortingen']+=$waarden['stortingen']-$waarden['onttrekkingen'];
        $totalen['resultaatVerslagperiode']+=$waarden['resultaatVerslagperiode'];
      }
      $lastPeriodeJaar=$periodeJaar;
    }
    

    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('','','','TS','TS',);
    $this->pdf->row(array('',vertaalTekst('Totaal',$this->pdf->rapport_taal),'',$this->formatGetal($totalen['stortingen'],0),$this->formatGetal($totalen['resultaatVerslagperiode'],0)));
    $totalen=array();
    $this->pdf->ln(5);
    
    if(count($jaren) > 9)
    {
      unset($this->pdf->CellBorders);
      $this->pdf->AddPage();
      $this->pdf->Ln();
    }
    $this->pdf->SetWidths(array(15,40,35,35, 35, 35,35,35,15));  
    $this->pdf->SetAligns(array('L','L','R','R','R','R','R','R','R'));
		$this->pdf->CellBorders = array('U','U','U','U','U','U','U','U','U','U');
//		$this->pdf->row(array('','Jaar',"Beginwaarde\n(in ".$this->pdf->rapportageValuta.")","Storting/\nonttrekking","Resultaat\n(in ".$this->pdf->rapportageValuta.")","Eindwaarde\n(in ".$this->pdf->rapportageValuta.")","Performance\nJaar","Cumulatieve\nperformance",''));
		$this->pdf->row(array('',vertaalTekst("Jaar",$this->pdf->rapport_taal),
		vertaalTekst("Beginwaarde",$this->pdf->rapport_taal)."\n(".vertaalTekst("in",$this->pdf->rapport_taal)." ".$this->pdf->rapportageValuta.")",
		vertaalTekst("Storting",$this->pdf->rapport_taal)."/\n".vertaalTekst("onttrekking",$this->pdf->rapport_taal),
		vertaalTekst("Resultaat",$this->pdf->rapport_taal)."\n(".vertaalTekst("in",$this->pdf->rapport_taal)." ".$this->pdf->rapportageValuta.")",
		vertaalTekst("Eindwaarde",$this->pdf->rapport_taal)."\n(".vertaalTekst("in",$this->pdf->rapport_taal)." ".$this->pdf->rapportageValuta.")",
		vertaalTekst("Performance",$this->pdf->rapport_taal)."\n".vertaalTekst("Jaar",$this->pdf->rapport_taal),
		vertaalTekst("Cumulatieve",$this->pdf->rapport_taal)."\n".vertaalTekst("Performance",$this->pdf->rapport_taal),''));

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
    $jaarPerf=100;
    foreach ($jaren as $jaar=>$waarden)
    {
      $jaarPerf =$jaarPerf*($waarden['performance']+100)/100;
      $this->pdf->row(array('',$jaar,
        $this->formatGetal($waarden['waardeBegin'],0),
        $this->formatGetal($waarden['stortingen'],0),
        $this->formatGetal($waarden['resultaatVerslagperiode'],0),
        $this->formatGetal($waarden['waardeHuidige'],0),
        $this->formatGetal($waarden['performance'],1),
        $this->formatGetal($jaarPerf-100,1),''));
        $totalen['stortingen']+=$waarden['stortingen'];
        $totalen['resultaatVerslagperiode']+=$waarden['resultaatVerslagperiode'];
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('','','','TS','TS',);
    $this->pdf->row(array('',vertaalTekst('Totaal',$this->pdf->rapport_taal),'',$this->formatGetal($totalen['stortingen'],0),$this->formatGetal($totalen['resultaatVerslagperiode'],0)));
    unset($this->pdf->CellBorders);

    $this->pdf->SetWidths(array(280));
//    $this->pdf->setY(180);
//		$this->pdf->row(array('In geval gedurende het jaar wordt aangevangen met de beleggingen, kan de cumulatieve performance aanzienlijk afwijken van het totaal van de afzonderlijke maandresultaten. Dit is het gevolg van het feit dat bij de cumulatieve performance wordt gerekend met het gemiddelde gedurende het gehele jaar geïnvesteerde vermogen (een deel van het jaar bedroeg het geïnvesteerde vermogen dus 0). Als u vanaf begin van het jaar bent belegd, zal dit effect zich niet voordoen.'));
	}
}
?>
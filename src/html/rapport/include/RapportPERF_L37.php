<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.7 $

$Log: RapportPERF_L37.php,v $
Revision 1.7  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.6  2012/07/04 16:05:11  rvv
*** empty log message ***

Revision 1.5  2012/06/06 18:18:25  rvv
*** empty log message ***

Revision 1.4  2012/05/30 16:02:38  rvv
*** empty log message ***

Revision 1.3  2012/05/27 08:33:10  rvv
*** empty log message ***

Revision 1.2  2012/05/12 15:11:00  rvv
*** empty log message ***

Revision 1.1  2012/05/02 15:53:13  rvv
*** empty log message ***

Revision 1.7  2012/04/15 16:01:34  rvv
*** empty log message ***

Revision 1.6  2012/04/14 16:51:17  rvv
*** empty log message ***

Revision 1.5  2012/03/25 13:27:46  rvv
*** empty log message ***

Revision 1.4  2012/03/21 19:08:58  rvv
*** empty log message ***

Revision 1.3  2012/03/18 16:08:24  rvv
*** empty log message ***

Revision 1.2  2012/03/08 07:58:38  rvv
*** empty log message ***

Revision 1.1  2012/02/26 15:17:43  rvv
*** empty log message ***



*/
include_once("rapport/include/RapportOIB_L37.php");
include_once("rapport/include/RapportPERFG_L37.php");
include_once("rapport/include/ATTberekening_L37.php");


class RapportPERF_L37
{

	function RapportPERF_L37($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  $this->pdf = &$pdf;
	// 	$this->oib = new RapportOIB_L37($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
	//	$this->perfg = new RapportPERFG_L37($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = vertaalTekst("Kerngegevens",$this->pdf->rapport_taal);
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
	  // OIB grafiek rechts boven.
	  // Perfg grafiek eerste pagina (totaal)

		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		if($this->pdf->rapport_layout == 1)
		{
			$kopStyle = "";
		}
		else
		{
			$kopStyle = "u";
		}

		$DB = new DB();

		// voor data
		$this->pdf->widthA = array(0,85,30,10,30,120);
		$this->pdf->alignA = array('L','L','R','L','R');

		// voor kopjes
		$this->pdf->widthB = array(1,95,30,10,30,120);
		$this->pdf->alignB = array('L','L','R','L','R');

		$this->pdf->AddPage();
		$this->pdf->templateVars['PERFPaginas']=$this->pdf->customPageNo;

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
    $x=$this->pdf->setX();
    $y=$this->pdf->getY();

		//grafiek rechts boven
		$DB = new DB();
		$q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$kleuren = unserialize($kleuren['grafiek_kleur']);

		//getOIBdata($this);
    foreach ($this->catogorieData as $categorie=>$data)
	  {
	    if($data['port']['procent'] > 0)
	    {
	      $portefeuilleOibGrafiekData[$this->categorien[$categorie]]=round($data['port']['procent']*100,1);
	      $portefeuilleOibGrafiekKleur[]=array($kleuren['OIB'][$categorie]['R']['value'],$kleuren['OIB'][$categorie]['G']['value'],$kleuren['OIB'][$categorie]['B']['value']);
	    }
	  }
	  //getOIVdata($this);
    // einde grafiek rechtsboven
    foreach ($this->valutaData as $categorie=>$data)
	  {
	    if($data['port']['procent'] > 0)
	    {
	      $portefeuilleOivGrafiekData[$this->valutas[$categorie]]=round($data['port']['procent']*100,1);
	      $portefeuilleOivGrafiekKleur[]=array($kleuren['OIV'][$categorie]['R']['value'],$kleuren['OIV'][$categorie]['G']['value'],$kleuren['OIV'][$categorie]['B']['value']);
	    }
	  }

	  getTypeGrafiekData($this,'Valuta','',array('EUR','USD'));
	  getTypeGrafiekData($this,'Beleggingscategorie');


    //begin grafiek rechts onder
$indexLookup['totaal']=$this->pdf->portefeuilledata['SpecifiekeIndex'];
$start = $this->rapportageDatumVanaf;
$eind = $this->rapportageDatum;
$datumStop  = db2jul($eind);
$att=new ATTberekening_L37($this);
$hcatData=$att->bereken($start,$eind,$this->pdf->rapportageValuta,'hoofdcategorie');
$this->pdf->hcatData['rapPeriode']=$hcatData;
$maandPeriode=mktime(0,0,0,1,1,date("Y",$datumStop));//-1
$categorien=array('ZAK','RISM','totaal');
foreach ($categorien as $cat)
{
  $perfIndexCum=1;
  foreach ($att->waarden[$cat] as $datum=>$data)
  {
    $juldate=db2jul($datum);
    if($juldate > $maandPeriode)
    {
    //   $perfIndex=$this->pdf->fondsPerf($indexLookup[$cat],date("Y-m-d",mktime(0,0,0,substr($datum,5,2),0,substr($datum,0,4))),$datum);
      // $perfIndexCum= ($perfIndexCum  * (1+$perfIndex)) ;
       $data['specifiekeIndex']=($perfIndexCum-1)*100;
       $hcatWaarden['maanden'][$cat]['portefeuille'][]=$data['index']-100;
      // $hcatWaarden['maanden'][$cat]['specifiekeIndex'][]=$data['specifiekeIndex'];
       $hcatWaarden['maanden'][$cat]['datum'][]= date("M",$juldate);
       $hcatWaarden['maanden'][$cat]['waarde'][]=$data;


    }
  }
}

     $cat='totaal';
    $typen=array('procent','indexPerf'); //,'bijdrage'

    $this->pdf->setXY(190,50);
    $this->pdf->MultiCell(70,5,vertaalTekst("Cumulatief rendement",$this->pdf->rapport_taal),0,'C');
    $this->pdf->setXY(190,55);
    LineDiagram($this->pdf,70, 45, $hcatWaarden['maanden'][$cat],array(array(87,165,25)),0,0,6,5,1);//50

/*
    $this->pdf->setXY($this->pdf->marge ,115);
    $this->pdf->setWidths(array(135,135));
    $this->pdf->setAligns(array('C','C'));
   	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst("Verdeling over categorieën",$this->pdf->rapport_taal),vertaalTekst('Verdeling over valuta\'s',$this->pdf->rapport_taal)));
*/

    $this->pdf->setXY(50,120);
	  PieChartOnder($this->pdf,45, 45, $this->pdf->grafiekData['Beleggingscategorie']['grafiek'], '%l (%p)',$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur'],vertaalTekst("Verdeling over categorieën",$this->pdf->rapport_taal));

    $this->pdf->setXY(190,120);
	  PieChartOnder($this->pdf,45, 45, $this->pdf->grafiekData['Valuta']['grafiek'], '%l (%p)',$this->pdf->grafiekData['Valuta']['grafiekKleur'],vertaalTekst('Verdeling over valuta\'s',$this->pdf->rapport_taal));


    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
    $this->pdf->setXY($x,$y);

		// ***************************** ophalen data voor afdruk ************************ //

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersBegin." ) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();
		$totaalWaardeVanaf = $DB->nextRecord();

		$waardeEind				= $totaalWaarde['totaal'];
		$waardeBegin 			 	= $totaalWaardeVanaf['totaal'];
		$waardeMutatie 	   	= $waardeEind - $waardeBegin;
		$stortingen 			 	= getStortingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$onttrekkingen 		 	= getOnttrekkingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
    $rendementProcent=$hcatData['totaal']['procent'];

		if($this->pdf->rapport_PERF_jaarRendement)
		{
			$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
		  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul( "$RapStartJaar-01-01"))
		  {
		    $startDatum =  $this->pdf->PortefeuilleStartdatum;
		    $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,$this->pdf->PortefeuilleStartdatum,true);
        vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,$this->pdf->PortefeuilleStartdatum);
		  }
		  else
		    $startDatum = "$RapStartJaar-01-01";

		  if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01")
	    {
	      $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,$startDatum,true);
        vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,$startDatum);
	    }
	    $rendementProcentJaar = performanceMeting($this->portefeuille,$startDatum,$this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
		}


			$ypos = $this->pdf->GetY();
			$this->pdf->ln(0);
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);
			$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);

			$this->pdf->row(array("",vertaalTekst("Resultaat rapportageperiode",$this->pdf->rapport_taal),"",""));
      $this->pdf->ln(2);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)),$this->formatGetal($waardeBegin,2,true),""));
      $this->pdf->ln(2);
			$this->pdf->row(array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($resultaatVerslagperiode,2),""));
      $this->pdf->ln(2);
			$this->pdf->row(array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($stortingen,2),""));
      $this->pdf->ln(2);
			$this->pdf->row(array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($onttrekkingen,2),""));
			$this->pdf->Line($posSubtotaal+$extraLengte ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
      $this->pdf->ln(2);
			$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatum)),$this->formatGetal($waardeEind,2),""));
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->ln(2);
			$this->pdf->row(array("",vertaalTekst("Rendement rapportageperiode",$this->pdf->rapport_taal),$this->formatGetal($rendementProcent,2),"%"));
			if($this->pdf->rapport_PERF_jaarRendement)
			  $this->pdf->row(array("",vertaalTekst("Rendement lopende kalenderjaar",$this->pdf->rapport_taal),$this->formatGetal($rendementProcentJaar,2),"%",""));

			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY(),$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY()+1 ,$posSubtotaalEnd ,$this->pdf->GetY()+1);

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

			$this->pdf->widthA = array(130,70,30,5,30,120);
			$this->pdf->alignA = array('L','L','R','R','R');

			$this->pdf->widthB = array(130,70,30,5,30,120);
			$this->pdf->alignB = array('L','L','R','R','R');

			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
			$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];

		$this->pdf->SetY($ypos);



		$actueleWaardePortefeuille = 0;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);



	//	$his->pdf->rowHeight=4;



	}


}

?>
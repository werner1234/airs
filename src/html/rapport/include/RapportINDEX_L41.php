<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2013/04/07 17:35:04 $
File Versie					: $Revision: 1.2 $

$Log: RapportINDEX_L41.php,v $
Revision 1.2  2013/04/07 17:35:04  rvv
*** empty log message ***

Revision 1.1  2013/04/06 16:16:31  rvv
*** empty log message ***

Revision 1.2  2012/07/25 16:01:56  rvv
*** empty log message ***

*/


include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportIndex_L41
{
	function RapportIndex_L41($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "INDEX";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_FRONT_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_FRONT_titel;
		else
			$this->pdf->rapport_titel = "Indices";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatumVanafJul=db2jul($this->rapportageDatumVanaf);
		$this->rapportageDatum = $rapportageDatum;
		$this->rapportageDatumJul=db2jul($this->rapportageDatum);
		$this->pdf->extraPage =0;
		$this->DB = new DB();


		$this->rapportJaar 		= date("Y",$this->rapportageDatumJul);

		$this->pdf->brief_font = $this->pdf->rapport_font;

	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}
	function kopEnVoet()
	{
	  if(is_file($this->pdf->rapport_factuurHeader))
		{
			$this->pdf->Image($this->pdf->rapport_factuurHeader, 0, 10, 210, 34);
		}
		if(is_file($this->pdf->rapport_factuurFooter))
		{
			$this->pdf->Image($this->pdf->rapport_factuurFooter, 5, 255, 200, 37);
		}
	}

	function getFondsKoers($fonds,$datum)
	{
	  $db=new DB();
	  $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
	  $db->SQL($query);
	  $koers=$db->lookupRecord();
	  return $koers['Koers'];
	}


	function writeRapport()
	{
	  global $__appvar;
	  $this->pdf->addPage();
	  $this->pdf->templateVars['INDEXPaginas'] = $this->pdf->page;

	  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  else
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";


	  $DB=new DB();
	  $perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);

	  $query="SELECT
IndexPerBeleggingscategorie.Fonds,
Beleggingscategorien.Omschrijving as categorieOmschrijving,
Fondsen.Omschrijving as fondsOmschrijving,
Fondsen.Valuta,
KeuzePerVermogensbeheerder.Afdrukvolgorde
FROM
IndexPerBeleggingscategorie
INNER JOIN Beleggingscategorien ON IndexPerBeleggingscategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
INNER JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds = Fondsen.Fonds
INNER JOIN KeuzePerVermogensbeheerder ON IndexPerBeleggingscategorie.Beleggingscategorie = KeuzePerVermogensbeheerder.waarde AND KeuzePerVermogensbeheerder.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
WHERE IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY KeuzePerVermogensbeheerder.Afdrukvolgorde";


		$DB->SQL($query);
		$DB->Query();
		$benchmarkCategorie=array();
	  while($index = $DB->nextRecord())
		{
		 	$indexData[$index['Fonds']]=$index;
      foreach ($perioden as $periode=>$datum)
      {
        $indexData[$index['Fonds']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Fonds'],$datum);
        $indexData[$index['Fonds']]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
      }
     	$indexData[$index['Fonds']]['performanceJaar'] = ($indexData[$index['Fonds']]['fondsKoers_eind'] - $indexData[$index['Fonds']]['fondsKoers_jan'])    / ($indexData[$index['Fonds']]['fondsKoers_jan']/100 );
			$indexData[$index['Fonds']]['performance'] =     ($indexData[$index['Fonds']]['fondsKoers_eind'] - $indexData[$index['Fonds']]['fondsKoers_begin']) / ($indexData[$index['Fonds']]['fondsKoers_begin']/100 );
  		$indexData[$index['Fonds']]['performanceEurJaar'] = ($indexData[$index['Fonds']]['fondsKoers_eind']*$indexData[$index['Fonds']]['valutaKoers_eind'] - $indexData[$index['Fonds']]['fondsKoers_jan']  *$indexData[$index['Fonds']]['valutaKoers_jan'])/(  $indexData[$index['Fonds']]['fondsKoers_jan']*  $indexData[$index['Fonds']]['valutaKoers_jan']/100 );
			$indexData[$index['Fonds']]['performanceEur'] =     ($indexData[$index['Fonds']]['fondsKoers_eind']*$indexData[$index['Fonds']]['valutaKoers_eind'] - $indexData[$index['Fonds']]['fondsKoers_begin']*$indexData[$index['Fonds']]['valutaKoers_begin'])/($indexData[$index['Fonds']]['fondsKoers_begin']*$indexData[$index['Fonds']]['valutaKoers_begin']/100 );
		}

	

    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->SetY(40);
  	$this->pdf->SetWidths(array(60,60,33,33,33,33,33));
  	$this->pdf->SetAligns(array('L','L','R','R','R','R','R','R','R','R'));
 	  $this->pdf->ln();
  	$this->pdf->CellBorders = array('U','U','U','U','U','U','U');
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);

    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->fillCell=array(1,1,1,1,1,1);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetTextColor(255,255,255);
  	  $this->pdf->row(array("\nCategorie","\nIndex","Koers\n".date("d-m-Y",db2jul($perioden['begin'])),"Koers\n".date("d-m-Y",db2jul($perioden['eind'])),'Rendement verslagperiode in %'));

    $this->pdf->CellBorders = array();
    unset($this->pdf->fillCell);
    $this->pdf->SetFillColor(0,0,0);
    $this->pdf->SetTextColor(0,0,0);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

  	foreach ($indexData as $fonds=>$data)
  	{

  	      $this->pdf->row(array($data['categorieOmschrijving'],$data['fondsOmschrijving'],
     	    $this->formatGetal($data['fondsKoers_begin'],2),
  	      $this->formatGetal($data['fondsKoers_eind'],2),
  	      $this->formatGetal($data['performance'],1)));
  	  
  	}

   // foreach ($indexData as $fonds=>$fondsData)
  //    $this->pdf->row(array($fondsData['toelichting'],$fondsData['Omschrijving'],$this->formatGetal($fondsData['performance'],1),$this->formatGetal($fondsData['performanceJaar'],1)));
/*
    $this->pdf->ln();
    $this->pdf->SetWidths(array(110,30,30));
  	$this->pdf->SetAligns(array('L','R','R'));
  	$this->pdf->CellBorders = array('U','U','U');
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	$this->pdf->row(array("\nValutarendementen",'Rendement verslagperiode in %','Rendement vanaf '.date("d-m-Y",db2jul($this->tweedePerformanceStart)).' in %'));
  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
  	foreach ($indexValuta as $valuta=>$valutaData)
     $this->pdf->row(array($valutaData['Omschrijving'],$this->formatGetal($valutaData['performance'],1),$this->formatGetal($valutaData['performanceJaar'],1)));
   $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
*/
//$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
	}
}
?>
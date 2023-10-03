<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.20 $

$Log: RapportATT_L33.php,v $
Revision 1.20  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.19  2015/06/27 15:52:41  rvv
*** empty log message ***

Revision 1.18  2014/02/12 15:55:51  rvv
*** empty log message ***

Revision 1.17  2014/02/05 16:02:14  rvv
*** empty log message ***

Revision 1.16  2012/09/30 11:18:17  rvv
*** empty log message ***

Revision 1.15  2012/07/29 10:24:33  rvv
*** empty log message ***

Revision 1.14  2012/04/21 15:38:14  rvv
*** empty log message ***

Revision 1.13  2012/02/19 16:13:11  rvv
*** empty log message ***

Revision 1.12  2011/06/08 18:19:04  rvv
*** empty log message ***

Revision 1.11  2011/05/04 16:32:00  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/rapportATTberekening.php");
include_once("rapport/include/ATTberekening_L33.php");

class RapportATT_L33
{
	function RapportATT_L33($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ATT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Beleggingsresultaat";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->tweedePerformanceStart=$rapportageDatumVanaf;
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
		$query = "SELECT Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();

		if(db2jul(date("Y",$this->pdf->rapport_datum)."-01-01") > db2jul($portefeuilledata['startDatum']))
	   	$rapportageStartJaar= date("Y-01-01",$this->pdf->rapport_datum);
	  else
	   	$rapportageStartJaar=substr($portefeuilledata['startDatum'],0,10);

	  $this->tweedePerformanceStart=$rapportageStartJaar;

		$startDatumTekst=date("j",$this->pdf->rapport_datumvanaf)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datumvanaf);
    $rapDatumTekst=date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum);
    $startJaarDatumTekst=date("j",db2jul($rapportageStartJaar))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($rapportageStartJaar))],$this->pdf->rapport_taal)." ".date("Y",db2jul($rapportageStartJaar));

		$this->pdf->AddPage();

		$this->pdf->templateVars['ATTPaginas'] = $this->pdf->customPageNo;//+$this->pdf->extraPage
		$this->pdf->ln();
		$this->pdf->SetWidths(array(55,65, 40, 55,65));
		$this->pdf->SetAligns(array('L','C','L','L','C'));
    //$this->pdf->CellBorders = array('T','T','T','T','T');
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		//$this->pdf->row(array('','Beleggingsresultaat','','','Beleggingsresultaat'));
		$this->pdf->CellBorders = array('U','U','U','U','U','U','U','U','U','U');
		$this->pdf->row(array('',"   $startDatumTekst - $rapDatumTekst",'','',"   $startJaarDatumTekst - $rapDatumTekst"));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetWidths(array(50,23,23,24, 40, 50,23,23,24));
		$this->pdf->SetAligns(array('L','R','R','R','C','L','R','R','R'));
    $y=$this->pdf->getY();
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);

		$this->pdf->row(array(vertaalTekst('Beleggingscategorie',$this->pdf->rapport_taal),'',"","",'',vertaalTekst('Beleggingscategorie',$this->pdf->rapport_taal)));
		$this->pdf->setY($y);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->CellBorders = array('U','U','U','U','U','U','U','U','U','U');
		$this->pdf->row(array('',vertaalTekst('Resultaat',$this->pdf->rapport_taal),
                             vertaalTekst('Performance',$this->pdf->rapport_taal)."\n".vertaalTekst('categorie',$this->pdf->rapport_taal)."",
                             vertaalTekst('Performance',$this->pdf->rapport_taal)."\n".vertaalTekst('contributie',$this->pdf->rapport_taal)."",'','',
		                         vertaalTekst('Resultaat',$this->pdf->rapport_taal),
                             vertaalTekst('Performance',$this->pdf->rapport_taal)."\n".vertaalTekst('categorie',$this->pdf->rapport_taal)."",
                             vertaalTekst('Performance',$this->pdf->rapport_taal)."\n".vertaalTekst('contributie',$this->pdf->rapport_taal).""));

    unset($this->pdf->CellBorders);

    if($this->pdf->portefeuilledata['PerformanceBerekening'] == 6)
	    $periodeBlok = 'kwartaal';
	  else
	    $periodeBlok = 'maand';
	  $this->berekening = new rapportATTberekening($this->portefeuille);
	  $this->berekening->getAttributieCategorien();
    $this->berekening->pdata['pdf']=true;
    //$this->berekening->attributiePerformance($this->portefeuille,$this->rapportageDatumVanaf,  $this->rapportageDatum,'rapportagePeriode',$this->pdf->rapportageValuta,$periodeBlok);
    //$this->waarden['rapportagePeriode']=$this->berekening->performance['rapportagePeriode'];

    //unset($this->berekening->performance);
    //$this->berekening->attributiePerformance($this->portefeuille,$this->tweedePerformanceStart,$this->rapportageDatum,'lopendeJaar',      $this->pdf->rapportageValuta,$periodeBlok);
    //$this->waarden['lopendeJaar']=$this->berekening->performance['lopendeJaar'];

	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

        $query="SELECT
Rekeningen.Portefeuille,
Rekeningen.Rekening,
Rekeningmutaties.Grootboekrekening,
SUM((Rekeningmutaties.Credit*Rekeningmutaties.Valutakoers $koersQuery)-(Rekeningmutaties.Debet*Rekeningmutaties.Valutakoers $koersQuery)) as waarde,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Fonds,
Grootboekrekeningen.Kosten,
Grootboekrekeningen.Opbrengst,
Grootboekrekeningen.Omschrijving
FROM
Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
Inner Join Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
WHERE
Rekeningen.Portefeuille='".$this->portefeuille."' AND Grootboekrekeningen.Kosten=1 AND Rekeningmutaties.Fonds='' AND
Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."'  AND  Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."'
GROUP BY Rekeningmutaties.Grootboekrekening";

    $DB=new DB();
 		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->NextRecord())
		{
      $totalen['rapportagePeriode']['kosten'] += $data['waarde'];
		}

		        $query="SELECT
Rekeningen.Portefeuille,
Rekeningen.Rekening,
Rekeningmutaties.Grootboekrekening,
SUM((Rekeningmutaties.Credit*Rekeningmutaties.Valutakoers $koersQuery)-(Rekeningmutaties.Debet*Rekeningmutaties.Valutakoers $koersQuery)) as waarde,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Fonds,
Grootboekrekeningen.Kosten,
Grootboekrekeningen.Opbrengst,
Grootboekrekeningen.Omschrijving
FROM
Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
Inner Join Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
WHERE
Rekeningen.Portefeuille='".$this->portefeuille."' AND Grootboekrekeningen.Kosten=1 AND Rekeningmutaties.Fonds='' AND
Rekeningmutaties.Boekdatum > '".$this->tweedePerformanceStart."'  AND  Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."'
GROUP BY Rekeningmutaties.Grootboekrekening";

    $DB=new DB();
 		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->NextRecord())
		{
      $totalen['lopendeJaar']['kosten'] += $data['waarde'];
		}

    unset($this->pdf->CellBorders);

    $categorieVolgorde=array('Risicodragend'=>array('AAND'=>'Aandelen','ALTERN'=>'Alternatieven'),
                             'Risicomijdend'=>array('OBL-ST'=>'Staatsobligaties','OBL-FI'=>'Bedrijfsobligaties','Liquiditeiten'=>'Liquiditeiten'));
$att=new ATTberekening_L33($this);
$this->waarden['rapportagePeriode']=$att->bereken($this->rapportageDatumVanaf,  $this->rapportageDatum,$this->pdf->rapportageValuta);
$this->waarden['lopendeJaar']=$att->bereken($this->tweedePerformanceStart,  $this->rapportageDatum,$this->pdf->rapportageValuta);

$totalen['rapportagePeriode']['perf'] =performanceMeting($this->portefeuille,$this->rapportageDatumVanaf,  $this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
$totalen['lopendeJaar']['perf']     =performanceMeting($this->portefeuille,$this->tweedePerformanceStart,  $this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);

    foreach ($categorieVolgorde as $hoofdCategorie=>$categorien)
    {
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->row(array(vertaalTekst($hoofdCategorie,$this->pdf->rapport_taal),'','','','',vertaalTekst($hoofdCategorie,$this->pdf->rapport_taal)));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->ln(3);
      foreach ($categorien as $categorie=>$categorieOmschrijving)
      {
          $this->pdf->row(array('    '.vertaalTekst($categorieOmschrijving,$this->pdf->rapport_taal),
      		$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['resultaat'],0),
      		$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['procent'],1).'%',
      		$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['bijdrage'],1).'%',
      		'',
      		'    '.vertaalTekst($categorieOmschrijving,$this->pdf->rapport_taal),
      		$this->formatGetal($this->waarden['lopendeJaar'][$categorie]['resultaat'],0),
      		$this->formatGetal($this->waarden['lopendeJaar'][$categorie]['procent'],1).'%',
      		$this->formatGetal($this->waarden['lopendeJaar'][$categorie]['bijdrage'],1).'%'));

      		$weging[$categorie]=$this->waarden['lopendeJaar']['bijdrage'][$categorie]/$this->waarden['lopendeJaar']['performance'][$categorie];

      		$totalen['rapportagePeriode']['resultaat'] += $this->waarden['rapportagePeriode'][$categorie]['resultaat'];
      		$totalen['rapportagePeriode']['opbrengsten'] += $this->waarden['rapportagePeriode'][$categorie]['opbrengsten'];
      		$totalen['rapportagePeriode']['bijdrage'] += $this->waarden['rapportagePeriode'][$categorie]['bijdrage'];
//echo "$categorie ". $this->waarden['rapportagePeriode'][$categorie]['bijdrage']."<br>\n";
      		$totalen['lopendeJaar']['resultaat'] += $this->waarden['lopendeJaar'][$categorie]['resultaat'];
      		$totalen['lopendeJaar']['opbrengsten'] += $this->waarden['lopendeJaar'][$categorie]['opbrengsten'];
      		$totalen['lopendeJaar']['bijdrage'] += $this->waarden['lopendeJaar'][$categorie]['bijdrage'];
          $this->pdf->ln(3);
      }
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    //$this->pdf->CellBorders = array('T','T','T','T','T','T','T','T','T','T');
    $this->pdf->Line($this->pdf->marge,$this->pdf->getY(),288,$this->pdf->getY());
    $this->pdf->ln(3);

    $this->pdf->row(array(vertaalTekst("Bruto resultaat",$this->pdf->rapport_taal),
      		$this->formatGetal($totalen['rapportagePeriode']['resultaat'],0),
      		'',
      		$this->formatGetal($totalen['rapportagePeriode']['bijdrage'],1).'%',
      		'',
      		vertaalTekst("Bruto resultaat",$this->pdf->rapport_taal),
      		$this->formatGetal($totalen['lopendeJaar']['resultaat'],0),
      		'',
      		$this->formatGetal($totalen['lopendeJaar']['bijdrage'],1).'%'));
    $this->pdf->ln(3);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
 unset($this->pdf->CellBorders);

    $this->pdf->row(array( vertaalTekst("Overige kosten",$this->pdf->rapport_taal),
      		$this->formatGetal($totalen['rapportagePeriode']['kosten'],0),
      		'',
      		$this->formatGetal(round($totalen['rapportagePeriode']['perf'],1)-round($totalen['rapportagePeriode']['bijdrage'],1),1).'%',
      		'',
      		vertaalTekst("Overige kosten",$this->pdf->rapport_taal),
      		$this->formatGetal($totalen['lopendeJaar']['kosten'],0),
      		'',
      		$this->formatGetal(round($totalen['lopendeJaar']['perf'],1)-round($totalen['lopendeJaar']['bijdrage'],1),1).'%'));
      		$this->pdf->ln(3);
//          echo $totalen['lopendeJaar']['perf']." ".$totalen['lopendeJaar']['bijdrage']."<br>\n";
/*
    $this->pdf->row(array("Opbrengsten",
      		$this->formatGetal($totalen['rapportagePeriode']['opbrengsten'],2),
      		'',
      		$this->formatGetal($totalen['rapportagePeriode']['opbrengsten']/$this->waarden['rapportagePeriode']['gemiddelde']['Totaal']*100,2),
      		'',
      		"Opbrengsten",
      		$this->formatGetal($totalen['lopendeJaar']['opbrengsten'],2),
      		'',
      		$this->formatGetal($totalen['lopendeJaar']['opbrengsten']/$this->waarden['lopendeJaar']['gemiddelde']['Totaal']*100,2)));
*/

		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->Line($this->pdf->marge,$this->pdf->getY(),288,$this->pdf->getY());
		$this->pdf->ln(3);
		$this->pdf->row(array(vertaalTekst("Netto resultaat",$this->pdf->rapport_taal),
      		$this->formatGetal($totalen['rapportagePeriode']['resultaat']+$totalen['rapportagePeriode']['kosten'],0),
      		'',//$this->formatGetal($this->waarden['rapportagePeriode']['performance']['Totaal'],2)
      		$this->formatGetal($totalen['rapportagePeriode']['perf'],1).'%',
      		'',
      		vertaalTekst("Netto resultaat",$this->pdf->rapport_taal),
      		$this->formatGetal($totalen['lopendeJaar']['resultaat']+$totalen['lopendeJaar']['kosten'],0),
         	'',//$this->formatGetal($this->waarden['lopendeJaar']['performance']['Totaal'],2)
      		$this->formatGetal($totalen['lopendeJaar']['perf'],1).'%'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);

    	$this->pdf->setY(170);
		$this->pdf->SetWidths(array(280));
    		$this->pdf->ln();
    if($this->pdf->rapport_taal==1)
		  $this->pdf->row(array('This overview shows a breakdown of the performance of the portfolio over the current reporting period and for the year to date. 
Above the performance for each investment category is shown. This performance has been calculated on the basis of the time weighted invested amounts in this category (Modified Dietz method). The performance contribution shows the contribution of the investment category to the overall investment result.'));
    else        
		  $this->pdf->row(array('In het bovenstaande overzicht treft u een uitsplitsing aan van de op uw portefeuille behaalde performance over afgelopen verslagperiode en over het gehele jaar tot en met de afgelopen verslagperiode. Wij hebben hierbij een onderverdeling gemaakt naar beleggingscategorie. Bij performance categorie treft u het rendement aan per beleggingscategorie. Dit wordt berekend als het resultaat gedeeld door het gemiddeld tijdgewogen geinvesteerde vermogen in de betreffende beleggingscategorie (de zogenaamde Modified Dietz methode). Bij performance contributie wordt de bijdrage van de betreffende beleggingscategorie aan het beleggingsresultaat weergegeven.'));

  }
}
?>
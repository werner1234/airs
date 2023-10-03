<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/02/26 10:04:26 $
File Versie					: $Revision: 1.1 $

$Log: RapportINDEX_L62.php,v $
Revision 1.1  2017/02/26 10:04:26  rvv
*** empty log message ***

Revision 1.1  2013/06/30 15:07:33  rvv
*** empty log message ***

Revision 1.2  2012/07/25 16:01:56  rvv
*** empty log message ***

Revision 1.1  2012/03/28 15:55:19  rvv
*** empty log message ***

Revision 1.5  2011/12/24 16:35:21  rvv
*** empty log message ***

Revision 1.4  2011/10/10 16:44:51  rvv
*** empty log message ***

Revision 1.3  2011/10/05 18:00:14  rvv
*** empty log message ***

Revision 1.2  2011/10/02 08:37:20  rvv
*** empty log message ***

Revision 1.1  2011/09/28 18:46:41  rvv
*** empty log message ***

Revision 1.6  2011/09/25 16:23:28  rvv
*** empty log message ***

Revision 1.5  2011/04/12 09:05:54  cvs
telefoonnr en BTW nr aanpassen

Revision 1.4  2011/01/11 08:23:38  cvs
*** empty log message ***

Revision 1.3  2011/01/08 14:27:56  rvv
*** empty log message ***

Revision 1.2  2011/01/05 18:53:09  rvv
*** empty log message ***

Revision 1.1  2010/12/05 09:54:08  rvv
*** empty log message ***

Revision 1.4  2010/07/04 15:24:39  rvv
*** empty log message ***

*/


include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportIndex_L62
{
	function RapportIndex_L62($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "INDEX";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datum_vanaf = db2jul($rapportageDatumVanaf);

		if($this->pdf->rapport_FRONT_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_FRONT_titel;
		else
			$this->pdf->rapport_titel = "Benchmarks";

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

	  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  else
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";

		$this->pdf->tweedePerformanceStart=db2jul($this->tweedePerformanceStart);

		$rowHeightBackup=$this->pdf->rowHeight;
		$this->pdf->rowHeight=5;
		$this->pdf->addPage();
		$this->pdf->templateVars['INDEXPaginas'] = $this->pdf->customPageNo;

	  $DB=new DB();
	  $perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);

	  $query="SELECT
IndexPerBeleggingscategorie.Fonds,
Fondsen.Omschrijving,
Fondsen.Valuta,
IndexPerBeleggingscategorie.Beleggingscategorie,
IndexPerBeleggingscategorie.Vermogensbeheerder,
IndexPerBeleggingscategorie.Beleggingscategorie,
Beleggingscategorien.Omschrijving as catOmschrijving
FROM
IndexPerBeleggingscategorie
Inner Join Fondsen ON IndexPerBeleggingscategorie.Fonds = Fondsen.Fonds
Left Join Beleggingscategorien ON IndexPerBeleggingscategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
WHERE IndexPerBeleggingscategorie.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND 
(IndexPerBeleggingscategorie.vanaf='0000-00-00' OR IndexPerBeleggingscategorie.vanaf > '".$this->rapportageDatum."') AND
(IndexPerBeleggingscategorie.portefeuille='' OR  IndexPerBeleggingscategorie.portefeuille='".$this->portefeuille."')
ORDER BY Beleggingscategorien.Afdrukvolgorde,Fondsen.Omschrijving";


		$DB->SQL($query);
		$DB->Query();
		$benchmarkCategorie=array();
	  while($index = $DB->nextRecord())
		{
      if($index['catOmschrijving'] == '')
        $index['catOmschrijving']='Overige';

		  $benchmarkCategorie[$index['catOmschrijving']][]=$index['Fonds'];

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



  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


  	foreach ($benchmarkCategorie as $categorie=>$fondsen)
  	{
  	  //$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	 // $this->pdf->row(array("",$categorie));
  	//  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	  foreach ($fondsen as $fonds)
  	  {
  	     $fondsData=$indexData[$fonds];
  	      $this->pdf->row(array($categorie,$fondsData['Omschrijving'],
														$this->formatGetal($indexData[$fonds]['fondsKoers_eind'],2),
														$this->formatGetal($indexData[$fonds]['fondsKoers_begin'],2),
														$this->formatGetal($fondsData['performance'],1),
   	                        $this->formatGetal($indexData[$fonds]['fondsKoers_jan'],2),
  	                        $this->formatGetal($indexData[$fonds]['fondsKoers_eind'],2),
														$this->formatGetal($fondsData['performanceJaar'],1)));
  	  }
  	}
		unset($this->pdf->CellBorders);
		$this->pdf->rowHeight=$rowHeightBackup;

	}
}
?>
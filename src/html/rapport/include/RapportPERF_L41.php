<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.14 $

$Log: RapportPERF_L41.php,v $
Revision 1.14  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.13  2013/04/24 16:01:30  rvv
*** empty log message ***

Revision 1.12  2013/02/27 17:04:41  rvv
*** empty log message ***

Revision 1.11  2013/01/06 10:09:57  rvv
*** empty log message ***

Revision 1.10  2012/12/30 14:27:12  rvv
*** empty log message ***

Revision 1.9  2012/12/02 11:05:56  rvv
*** empty log message ***

Revision 1.8  2012/11/21 16:29:06  rvv
*** empty log message ***

Revision 1.7  2012/11/18 18:05:39  rvv
*** empty log message ***

Revision 1.6  2012/11/17 16:02:20  rvv
*** empty log message ***

Revision 1.5  2012/11/04 13:32:33  rvv
*** empty log message ***

Revision 1.4  2012/11/04 13:15:03  rvv
*** empty log message ***

Revision 1.3  2012/10/24 18:06:07  rvv
*** empty log message ***

Revision 1.1  2012/08/01 16:57:55  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportPERF_L41
{

	function RapportPERF_L41($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		if($this->pdf->rapport_PERF_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_PERF_titel;
		else
			$this->pdf->rapport_titel = "Resultaat en rendementsberekening";


		//$this->pdf->rapport_PERF_displayType

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
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		if(true)
		{
			$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
		  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul( "$RapStartJaar-01-01"))
		  {
		    $startDatum =  substr($this->pdf->PortefeuilleStartdatum,0,10);
		    $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,$this->pdf->PortefeuilleStartdatum,true);
        vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,$this->pdf->PortefeuilleStartdatum);
		  }
		  else
		    $startDatum = "$RapStartJaar-01-01";
      $this->janStart=$startDatum;

		  if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01")
	    {
	      $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,$startDatum,true);
        vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,$startDatum);
	    }
		}



		$DB = new DB();

		// voor data
		$this->pdf->widthA = array(5,80,30,5,30,5,30,120);
		$this->pdf->alignA = array('L','L','R','L','R');

		// voor kopjes
		$this->pdf->widthB = array(0,85,30,5,30,5,30,120);
		$this->pdf->alignB = array('L','L','R','L','R');


		$this->pdf->AddPage();
    $this->pdf->templateVars['PERFPaginas']=$this->pdf->page;
    

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);

	  $data['periode']=$this->getWaarden($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
	  $data['ytm']=$this->getWaarden($this->portefeuille,$startDatum,$this->rapportageDatum);

		// ***************************** einde ophalen data voor afdruk ************************ //

		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];

		$extraLengte = $this->pdf->rapport_PERF_lijnenKorter;

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

    $YpageStart=$this->pdf->getY(); 

    $startPeriodeTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf));
    $startJaarTxt=date("j",db2jul($startDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($startDatum))],$this->pdf->taal)." ".date("Y",db2jul($startDatum));
    $eindPeriodeTxt=date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum));
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->fillCell=array(0,1,1,1,1,1);
    $this->pdf->SetTextColor(255,255,255);
    $kwartaal=ceil(date("n",db2jul($this->rapportageDatum))/3);
		$this->pdf->row(array("",vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal),"Kwartaal $kwartaal","",'Lopend jaar',''));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->fillCell=array();
		//$this->pdf->SetWidths($this->pdf->widthA);
		//$this->pdf->SetAligns($this->pdf->alignA);

		$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per begin rapportageperiode",$this->pdf->rapport_taal)
		,$this->formatGetal($data['periode']['waardeBegin'],0,true),"",$this->formatGetal($data['ytm']['waardeBegin'],0,true),""));
    $this->pdf->CellBorders = array('','','U','','U');
		$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per eind rapportageperiode",$this->pdf->rapport_taal),
    $this->formatGetal($data['periode']['waardeEind'],0),"",$this->formatGetal($data['ytm']['waardeEind'],0)));
    
    //." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum))." ",
    $this->pdf->CellBorders = array();
			// subtotaal
		$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->ln();
		$this->pdf->row(array("",vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal),$this->formatGetal($data['periode']['waardeMutatie'],0),"",$this->formatGetal($data['ytm']['waardeMutatie'],0),""));
		$this->pdf->row(array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($data['periode']['stortingen'],0),"",$this->formatGetal($data['ytm']['stortingen'],0),""));
		$this->pdf->CellBorders = array('','','U','','U');
    $this->pdf->row(array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($data['periode']['onttrekkingen'],0),"",$this->formatGetal($data['ytm']['onttrekkingen'],0),""));
    $this->pdf->ln();
		$this->pdf->row(array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($data['periode']['resultaatVerslagperiode'],0),"",$this->formatGetal($data['ytm']['resultaatVerslagperiode'],0),""));
		$this->pdf->ln();

    $this->pdf->CellBorders = array('U','U','U','U','U','U');
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($data['periode']['rendementProcent'],2),"%",$this->formatGetal($data['ytm']['rendementProcent'],2),"%"));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  $this->pdf->CellBorders = array();
		$ypos = $this->pdf->GetY();


		$this->pdf->SetY($ypos);
		$this->pdf->ln();

		//$this->pdf->SetWidths($this->pdf->widthB);
		//$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->fillCell=array(0,1,1,1,1,1);
    $this->pdf->SetTextColor(255,255,255);
    $YSamenstelling=$this->pdf->GetY();
		$this->pdf->row(array("",vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal),"","","",""));
		$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->fillCell=array();
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		//$this->pdf->SetWidths($this->pdf->widthA);
		//$this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->row(array("",vertaalTekst("Ongerealiseerde koersresultaten",$this->pdf->rapport_taal),$this->formatGetal($data['periode']['ongerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['ongerealiseerdeKoersResultaat'],0),""));
		$this->pdf->row(array("",vertaalTekst("Gerealiseerde koersresultaten",$this->pdf->rapport_taal),$this->formatGetal($data['periode']['gerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['gerealiseerdeKoersResultaat'],0),""));
		if(round($data['periode']['koersResulaatValutas'],2) != 0.00 || round($data['ytm']['koersResulaatValutas'],2) != 0.00)
		  $this->pdf->row(array("",vertaalTekst("Koersresultaten valuta's",$this->pdf->rapport_taal),$this->formatGetal($data['periode']['koersResulaatValutas'],0),"",$this->formatGetal($data['ytm']['koersResulaatValutas'],0),""));
		$this->pdf->row(array("",vertaalTekst("Resultaat opgelopen rente",$this->pdf->rapport_taal),$this->formatGetal($data['periode']['opgelopenRente'],0),"",$this->formatGetal($data['ytm']['opgelopenRente'],0),""));

		$keys=array();
		foreach ($data['periode']['opbrengstenPerGrootboek'] as $key=>$val)
		  $keys[]=$key;

		foreach ($keys as $key)
		{
		  if(round($data['periode']['opbrengstenPerGrootboek'][$key],2) != 0.00 || round($data['ytm']['opbrengstenPerGrootboek'][$key],2) != 0.00)
			  $this->pdf->row(array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($data['periode']['opbrengstenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['opbrengstenPerGrootboek'][$key],0),""));
		}

    $this->pdf->CellBorders = array('','','T','','T');
		$this->pdf->row(array("","",$this->formatGetal($data['periode']['totaalOpbrengst'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst'],0)));
		$this->pdf->ln();
    $this->pdf->CellBorders = array();

		//$this->pdf->SetWidths($this->pdf->widthB);
		//$this->pdf->SetAligns($this->pdf->alignB);

		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Kosten",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		//$this->pdf->SetWidths($this->pdf->widthA);
		//$this->pdf->SetAligns($this->pdf->alignA);

		$keys=array();
		foreach ($data['periode']['kostenPerGrootboek'] as $key=>$val)
		  $keys[]=$key;
		foreach ($keys as $key)
		{
		  if(round($data['periode']['kostenPerGrootboek'][$key],2) != 0.00 || round($data['ytm']['kostenPerGrootboek'][$key],2) != 0.00)
			  $this->pdf->row(array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($data['periode']['kostenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['kostenPerGrootboek'][$key],0),""));
		}



    $this->pdf->CellBorders = array('','','T','','T');
		$this->pdf->row(array("","",$this->formatGetal($data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalKosten'],0)));

		$posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3];

		//$this->pdf->Line($posTotaal +2+$extraLengte  ,$this->pdf->GetY() ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY());
		
$this->pdf->CellBorders = array('U','U','U','U','U','U');
$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
	  $this->pdf->row(array("","",$this->formatGetal($data['periode']['totaalOpbrengst']-$data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst']-$data['ytm']['totaalKosten'],0),''));

		$actueleWaardePortefeuille = 0;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
$this->pdf->CellBorders = array();
    $this->pdf->SetY($YpageStart);
		$this->getIndexWaarden();
    $this->pdf->SetY($YSamenstelling);
		$this->portefeuilleVerdeling();
  
    if(is_array($this->pdf->__appvar['consolidatie']))
      $this->addconsolidatie();
	}

	function getWaarden($portefeuille,$vanafDatum,$totDatum)
	{
	 global $__appvar;
  	// ***************************** ophalen data voor afdruk ************************ //

  	$waarden=array();
	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	  {
	    $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	    $totRapKoers=getValutaKoers($this->pdf->rapportageValuta,$vanafDatum);
	    $vanRapKoers=getValutaKoers($this->pdf->rapportageValuta,$totDatum);
	  }
	  else
	  {
	    $koersQuery = "";
	    $totRapKoers=1;
	    $vanRapKoers=1;
	  }
    
    if(substr($vanafDatum,5,5)=='01-01')
      $beginJaar=true;
    else
      $beginJaar=false;  
 
    $fondsen=berekenPortefeuilleWaarde($portefeuille,$vanafDatum,$beginJaar,$this->pdf->rapportageValuta,$vanafDatum);
    $totaal=array();
    $totaalWaardeVanaf['totaal']=0;
    foreach($fondsen as $id=>$regel)
    {
      $totaalWaardeVanaf['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
      if($regel['type']=='rente')
      {
        $totaalB['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
      } 
    }
 
    $totaalWaarde['totaal']=0;
    $fondsen=berekenPortefeuilleWaarde($portefeuille,$totDatum,false,$this->pdf->rapportageValuta,$vanafDatum);
    $totaal=array();
    foreach($fondsen as $id=>$regel)
    {
      $totaalWaarde['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
      if($regel['type']=='rente')
      {
        $totaalA['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
      }
      if($regel['type']=='fondsen')
      {
        $totaal['totaalB']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
        $totaal['totaalA']+=($regel['beginPortefeuilleWaardeEuro']/$totRapKoers);
      }
    }

    $ongerealiseerdeKoersResultaat = $totaal['totaalB'] - $totaal['totaalA'];
    $waarden['ongerealiseerdeKoersResultaat']=$ongerealiseerdeKoersResultaat;


    $DB=new DB();

		$waardeEind				  = $totaalWaarde['totaal'];
		$waardeBegin 			 	= $totaalWaardeVanaf['totaal'];
		$waardeMutatie 	   	= $waardeEind - $waardeBegin;
		$stortingen 			 	= getStortingen($portefeuille,$vanafDatum,$totDatum,$this->pdf->rapportageValuta);
		$onttrekkingen 		 	= getOnttrekkingen($portefeuille,$vanafDatum,$totDatum,$this->pdf->rapportageValuta);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
		$rendementProcent  	= performanceMeting($portefeuille, $vanafDatum, $totDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
//echo $this->pdf->portefeuilledata['PerformanceBerekening'];exit;
		$waarden['waardeEind']=$waardeEind;
		$waarden['waardeBegin']=$waardeBegin;
		$waarden['waardeMutatie']=$waardeMutatie;
		$waarden['stortingen']=$stortingen;
		$waarden['onttrekkingen']=$onttrekkingen;
		$waarden['resultaatVerslagperiode']=$resultaatVerslagperiode;
		$waarden['rendementProcent']=$rendementProcent;

    $RapJaar = date("Y", db2jul($totDatum));
    $RapStartJaar = date("Y", db2jul($vanafDatum));
		$totaalOpbrengst += $ongerealiseerdeKoersResultaat;
		$gerealiseerdeKoersResultaat = gerealiseerdKoersresultaat($portefeuille, $vanafDatum, $totDatum,$this->pdf->rapportageValuta,true);
		$totaalOpbrengst += $gerealiseerdeKoersResultaat;
		$waarden['gerealiseerdeKoersResultaat']=$gerealiseerdeKoersResultaat;

		$opgelopenRente = ($totaalA['totaal'] - $totaalB['totaal']) / $totRapKoers;
		$totaalOpbrengst += $opgelopenRente;
		$waarden['opgelopenRente']=$opgelopenRente;

		if($this->pdf->GrootboekPerVermogensbeheerder)
		  $query = "SELECT DISTINCT(GrootboekPerVermogensbeheerder.Grootboekrekening), GrootboekPerVermogensbeheerder.Omschrijving FROM GrootboekPerVermogensbeheerder
                WHERE GrootboekPerVermogensbeheerder.Opbrengst = '1' AND GrootboekPerVermogensbeheerder.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
                ORDER BY GrootboekPerVermogensbeheerder.Afdrukvolgorde";
		else
      $query = "SELECT DISTINCT(Grootboekrekeningen.Grootboekrekening), Grootboekrekeningen.Omschrijving FROM Grootboekrekeningen WHERE Grootboekrekeningen.Opbrengst = '1' ORDER BY Grootboekrekeningen.Afdrukvolgorde";


		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		while($gb = $DB->nextRecord())
		{
			$query = "SELECT  ".
		  	"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
		  	"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
		  	"FROM Rekeningmutaties, Rekeningen, Portefeuilles ".
		  	"WHERE ".
		  	"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		  	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
		  	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  	"Rekeningmutaties.Verwerkt = '1' AND ".
		  	"Rekeningmutaties.Boekdatum > '".$vanafDatum."' AND ".
		  	"Rekeningmutaties.Boekdatum <= '".$totDatum."' AND ".
			  "Rekeningmutaties.Grootboekrekening = '".$gb['Grootboekrekening']."' ";

			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();

			while($opbrengst = $DB2->nextRecord())
			{
				$opbrengstenPerGrootboek[$gb['Omschrijving']] =  ($opbrengst['totaalcredit']-$opbrengst['totaaldebet']);
				$totaalOpbrengst += ($opbrengst['totaalcredit'] - $opbrengst['totaaldebet']);
			}
		}
		$waarden['opbrengstenPerGrootboek']=$opbrengstenPerGrootboek;
		$waarden['totaalOpbrengst']=$totaalOpbrengst;

		// loopje over Grootboekrekeningen Kosten = 1
		if($this->pdf->GrootboekPerVermogensbeheerder)
		{
		  $query = "SELECT GrootboekPerVermogensbeheerder.Omschrijving,GrootboekPerVermogensbeheerder.Grootboekrekening, ".
		  "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
		  "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
		  "FROM Rekeningmutaties, Rekeningen, Portefeuilles, GrootboekPerVermogensbeheerder ".
	   	"WHERE ".
		  "Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		  "Rekeningen.Portefeuille = '".$portefeuille."' AND ".
		  "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  "Rekeningmutaties.Verwerkt = '1' AND ".
		  "Rekeningmutaties.Boekdatum > '".$vanafDatum."' AND ".
		  "Rekeningmutaties.Boekdatum <= '".$totDatum."' AND ".
		  "GrootboekPerVermogensbeheerder.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND ".
		  "Rekeningmutaties.Grootboekrekening = GrootboekPerVermogensbeheerder.GrootboekRekening AND ".
		  "GrootboekPerVermogensbeheerder.Kosten = '1' ".
		  "GROUP BY Rekeningmutaties.Grootboekrekening ".
		  "ORDER BY GrootboekPerVermogensbeheerder.Afdrukvolgorde ";
		}
		else
		{
		  $query = "SELECT Grootboekrekeningen.Omschrijving,Grootboekrekeningen.Grootboekrekening, ".
		  "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
		  "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
		  "FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen ".
		  "WHERE ".
		  "Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		  "Rekeningen.Portefeuille = '".$portefeuille."' AND ".
		  "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  "Rekeningmutaties.Verwerkt = '1' AND ".
		  "Rekeningmutaties.Boekdatum > '".$vanafDatum."' AND ".
		  "Rekeningmutaties.Boekdatum <= '".$totDatum."' AND ".
		  "Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND ".
		  "Grootboekrekeningen.Kosten = '1' ".
		  "GROUP BY Rekeningmutaties.Grootboekrekening ".
		  "ORDER BY Grootboekrekeningen.Afdrukvolgorde ";
		}

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		$kostenPerGrootboek = array();

		while($kosten = $DB->nextRecord())
		{
			if($kosten['Grootboekrekening'] == "KNBA")
			{
			  $kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = "Bankkosten en provisie";
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}
			elseif($kosten['Grootboekrekening'] == "KOBU")
			{
				$kostenPerGrootboek['KOST']['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}
			else
			{
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = $kosten['Omschrijving'];
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}


			$totaalKosten += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
		}
					foreach ($kostenPerGrootboek as $data)
			{
			  $tmp[$data['Omschrijving']]=$data['Bedrag'];
			}

		$waarden['kostenPerGrootboek']=$tmp;
		$waarden['totaalKosten']=$totaalKosten;

		$kostenProcent = ($totaalKosten / $waardeEind) * 100;
		$koersResulaatValutas = $resultaatVerslagperiode - ($totaalOpbrengst  -  $totaalKosten);
		$totaalOpbrengst += $koersResulaatValutas;
		$waarden['kostenProcent']=$kostenProcent;
		$waarden['koersResulaatValutas']=$koersResulaatValutas;
		$waarden['totaalOpbrengst']=$totaalOpbrengst;

		return $waarden;
	}

	function getIndexWaarden()
	{
	  $DB = new DB();
$query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE Portefeuille = '".$this->portefeuille."' AND Categorie = 'Totaal' ORDER BY Datum ASC LIMIT 1 ";
$DB->SQL($query);
$DB->Query();
$datum = $DB->nextRecord();


if($datum['id'] > 0 && $this->pdf->lastPOST['perfPstart'] == 1)
{
  if($datum['month'] <10)
    $datum['month'] = "0".$datum['month'];
  $start = $datum['year'].'-'.$datum['month'].'-01';
}
else
  $start = $this->rapportageDatumVanaf;
  $eind = $this->rapportageDatum;

$datumStart = db2jul($start);
$datumStop  = db2jul($eind);

$index = new indexHerberekening();
$indexWaarden = $index->getWaarden($start,$eind,$this->portefeuille);


if($this->pdf->portefeuilledata['SpecifiekeIndex'] != '')
{
  $lookupDB = new DB();
  $lookupQuery = "SELECT Fondsen.Omschrijving FROM Fondsen WHERE Fondsen.Fonds = '".$this->pdf->portefeuilledata['SpecifiekeIndex']."'";
  $lookupDB->SQL($lookupQuery);
  $lookupRec = $lookupDB->lookupRecord();
  $indexFondsen[]=$this->pdf->portefeuilledata['SpecifiekeIndex'];
  $indexNaam[$this->pdf->portefeuilledata['SpecifiekeIndex']] = $lookupRec['Omschrijving'];
}

$query = "SELECT Indices.Beursindex ,Indices.grafiekKleur
          FROM Indices
          WHERE Indices.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'  ORDER BY Indices.Afdrukvolgorde  ";
$DB->SQL($query);
$DB->Query();
while ($data = $DB->nextRecord())
{
	$indexFondsen[] = $data['Beursindex'];
	$indexKleuren[$data['Beursindex']] = unserialize($data['grafiekKleur']);
}

$query = "SELECT BeleggingscategoriePerFonds.grafiekKleur, BeleggingscategoriePerFonds.Fonds
          FROM  BeleggingscategoriePerFonds
          WHERE BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND BeleggingscategoriePerFonds.Fonds IN('".implode("','",$indexFondsen)."') ";
$DB->SQL($query);
$DB->Query();
while ($data = $DB->nextRecord())
{
  if($data['grafiekKleur'] !='')
	  $indexKleuren[$data['Fonds']] = unserialize($data['grafiekKleur']);
}


//listarray($indexKleuren);exit;
//listarray($indexFondsen);

$aantalWaarden = count($indexWaarden);
foreach ($indexWaarden as $id=>$waarden)
{
  $start = jul2sql(form2jul(substr($waarden['periodeForm'],0,10)));
  $eind = jul2sql(form2jul(substr($waarden['periodeForm'],13)));
  foreach ($indexFondsen as $fonds)
  {
 	  $q0 = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$eind."' AND Fonds = '$fonds'  ORDER BY Datum DESC LIMIT 1" ;
 	  $q1 = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$start."' AND Fonds = '$fonds'  ORDER BY Datum DESC LIMIT 1";
	  $DB->SQL($q0);
	  $DB->Query();
	  $koersEind = $DB->LookupRecord();
	  $DB->SQL($q1);
	  $DB->Query();
	  $koersStart = $DB->LookupRecord();
	  $perf = $koersEind['Koers'] /$koersStart['Koers']  ;
	  if($perf==0)
      $perf =1;
    $indexWaarden[$id]['fondsPerf'][$fonds] = $perf  ;

  //  echo "$eind $fonds $perf <br>";

    if(empty($indexWaarden[$id-1]['fondsIndex'][$fonds]))
	    $indexWaarden[$id]['fondsIndex'][$fonds] = $indexWaarden[$id]['fondsPerf'][$fonds];
	  else
  	  $indexWaarden[$id]['fondsIndex'][$fonds]  =($indexWaarden[$id]['fondsPerf'][$fonds]*$indexWaarden[$id-1]['fondsIndex'][$fonds]);

    $jaar=substr($eind,0,4);

   	if(empty($indexTabel['cumulatief'][$fonds]['jaren']))
   	  $indexTabel['cumulatief'][$fonds]['jaren']=100;

   	if(empty($indexTabel['cumulatief'][$fonds]['cumulatief']))
   	   $indexTabel['cumulatief'][$fonds]['cumulatief']=100;

    $indexTabel['cumulatief'][$fonds]['jaren']      = ($indexTabel['cumulatief'][$fonds]['jaren']*($perf*100))/100;
    $indexTabel['cumulatief'][$fonds]['cumulatief'] = ($indexTabel['cumulatief'][$fonds]['cumulatief']*($perf*100))/100;
    $indexTabel[$jaar][$fonds]['jaar'] = $indexTabel['cumulatief'][$fonds]['jaren'];

    if(substr($eind,5,5) == '12-31' || $aantalWaarden == $id)
    {
      $indexTabel['cumulatief'][$fonds]['jaren'] = 100;
      $indexTabel[$jaar][$fonds]['cumulatief'] = $indexTabel['cumulatief'][$fonds]['cumulatief'];
    }
  }
}

$n=0;
$minVal = 99;
$maxVal = 101;
foreach ($indexWaarden as $id=>$data)
{
  $grafiekData['portefeuille'][$n]=$data['index'];
  $datumArray[$n] = $data['datum'];
  $jaar=substr($data['datum'],0,4);

  if(empty($indexTabel['cumulatief']['portefeuille']['jaren']))
    $indexTabel['cumulatief']['portefeuille']['jaren']=100;
  $indexTabel['cumulatief']['portefeuille']['jaren'] =($indexTabel['cumulatief']['portefeuille']['jaren']*(100+$data['performance'])/100);
  $indexTabel[$jaar]['portefeuille']['jaar'] = $indexTabel['cumulatief']['portefeuille']['jaren'];
  if(substr($data['datum'],5,5) == '12-31' || $aantalWaarden == $id)
  {
    $indexTabel['cumulatief']['portefeuille']['jaren'] = 100;
    $indexTabel[$jaar]['portefeuille']['cumulatief'] = $data['index'];
  }

  if($data['index'] != 0)
  {
    $maxVal=max($maxVal,$data['index']);
    $minVal=min($minVal,$data['index']);
  }

  foreach ($data['fondsIndex'] as $fonds=>$waarde)
  {
    $grafiekData[$fonds][$n]=$waarde *100;
    if($waarde != 0)
    {
      $maxVal=max($maxVal,$waarde *100);
      $minVal=min($minVal,$waarde *100);
    }
  }
  $n++;
}


$indexTabelFondsen = array('Portefeuille'=>'portefeuille',$this->pdf->portefeuilledata['SpecifiekeIndex']=>$this->pdf->portefeuilledata['SpecifiekeIndex']);

$tmpArray0 = array('','');
$tmpArray1 = array('','Jaar');

foreach ($indexTabelFondsen as $fondsOmschrijving=>$fonds)
{

  array_push($tmpArray0,($fonds <> "portefeuille"?"Benchmark":$fondsOmschrijving));
  array_push($tmpArray1,"per jaar");
  array_push($tmpArray1,"cumu.");
}

    $this->pdf->fillCell=array(0,1,1,1,1);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetTextColor(255,255,255);

$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
$this->pdf->setAligns(array('L','L','C','C'));
$this->pdf->CellBorders = array();
$left=297-(2*$this->pdf->marge)-100;
$w=100/5;
$this->pdf->setWidths(array($left,$w,$w+$w,$w+$w));
$this->pdf->Row($tmpArray0);
$this->pdf->fillCell=array();
$this->pdf->SetTextColor(0,0,0);

$this->pdf->setWidths(array($left,$w,$w,$w,$w,$w));

//$this->pdf->SetWidths(array(297-(2*$this->pdf->marge)-100,55,23,22));
$this->pdf->setAligns(array('L','L','R','R','R','R'));
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
$this->pdf->Row($tmpArray1);


//listarray($indexTabel);
foreach ($indexTabel as $datum=>$fondsen)
{
  if(is_numeric($datum))
  {
    $tmpArray = array('');
    array_push($tmpArray,$datum);

    foreach ($indexTabelFondsen as $fonds)
    {
      $waarden = $indexTabel[$datum][$fonds];
   //  echo $fonds." "; listarray($waarden);
      if(in_array($fonds,$indexTabelFondsen))
      {
        if(!empty($waarden['jaar']))
          array_push($tmpArray,$this->formatGetal(($waarden['jaar']-100),2)."%");
        else
          array_push($tmpArray,"0,00%");

        if(!empty($waarden['cumulatief']))
          array_push($tmpArray,$this->formatGetal(($waarden['cumulatief']-100),2)."%");
        elseif(!empty($indexTabel['cumulatief'][$fonds]['cumulatief']))
          array_push($tmpArray,$this->formatGetal(($indexTabel['cumulatief'][$fonds]['cumulatief']-100),2)."%");
        else
          array_push($tmpArray,"");

      }
    }
    $this->pdf->Row($tmpArray);
  }
}
$this->pdf->CellBorders=array();

	}













function addconsolidatie()
{
  
  if(!isset($this->pdf->__appvar['consolidatie']))
  {
   $this->pdf->__appvar['consolidatie']=1;
   $this->pdf->portefeuilles=array($this->portefeuille);
  }
$this->pdf->addPage();

  $startPeriodeTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf));
    $startJaarTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($startDatum));
    $eindPeriodeTxt=date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum));

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
 // listarray($this->pdf->portefeuilles);
  $fillArray=array(0,1);
  $subOnder=array('','');
  $volOnder=array('U','U');
  $subBoven=array('','');
  $header=array("",vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal));
  $samenstelling=array("",vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal));
  
  foreach($this->pdf->portefeuilles as $portefeuille)
  {
    $volOnder[]='U';
    $volOnder[]='U';
    $subOnder[]='U';
    $subOnder[]='';
    $subBoven[]='T';
    $subBoven[]='';    
    $fillArray[]=1;
    $fillArray[]=1;
    $header[]=$portefeuille;
    $header[]='';
    $samenstelling[]='';
    $samenstelling[]='';
    $perfWaarden[$portefeuille]=$this->getWaarden($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
  }

  foreach($perfWaarden as $port=>$waarden)
  {
    foreach($waarden['opbrengstenPerGrootboek'] as $categorie=>$waarde)
      if(round($waarde,2)!=0.00)
       $opbrengstCategorien[$categorie]=$categorie;
    foreach($waarden['kostenPerGrootboek'] as $categorie=>$waarde)
      if(round($waarde,2)!=0.00)
        $kostenCategorien[$categorie]=$categorie;   
  }
  
  $perbegin=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)));
  $waardeRapdatum=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum)));
  $mutwaarde=array("",vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal));
  $stortingen=array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal));
  $onttrekking=array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal));
  $resultaat=array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));
  $rendement=array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal));
  $ongerealiseerd=array("",vertaalTekst("Ongerealiseerde koersresultaten",$this->pdf->rapport_taal)); //
$gerealiseerd=array("",vertaalTekst("Gerealiseerde koersresultaten",$this->pdf->rapport_taal)); //
$valutaResultaat=array("",vertaalTekst("Koersresultaten valuta's",$this->pdf->rapport_taal)); //
$rente=array("",vertaalTekst("Resultaat opgelopen rente",$this->pdf->rapport_taal));//
$totaalOpbrengst=array("","");//totaalOpbrengst

    $totaalKosten=array("","");   //totaalKosten 
    $totaal=array("","");   //totaalOpbrengst-totaalKosten 

  foreach($perfWaarden as $portefeuille=>$waarden)
  { 
    $perbegin[]=$this->formatGetal($perfWaarden[$portefeuille]['waardeBegin'],0,true);
    $perbegin[]='';
    $waardeRapdatum[]=$this->formatGetal($perfWaarden[$portefeuille]['waardeEind'],0,true);
    $waardeRapdatum[]='';
    $mutwaarde[]=$this->formatGetal($perfWaarden[$portefeuille]['waardeMutatie'],0,true);
    $mutwaarde[]='';
    $stortingen[]=$this->formatGetal($perfWaarden[$portefeuille]['stortingen'],0);
    $stortingen[]='';
    $onttrekking[]=$this->formatGetal($perfWaarden[$portefeuille]['onttrekkingen'],0);
    $onttrekking[]='';
    $resultaat[]=$this->formatGetal($perfWaarden[$portefeuille]['resultaatVerslagperiode'],0);
    $resultaat[]='';
    $rendement[]=$this->formatGetal($perfWaarden[$portefeuille]['rendementProcent'],2);
    $rendement[]='%';
    $ongerealiseerd[]=$this->formatGetal($perfWaarden[$portefeuille]['ongerealiseerdeKoersResultaat'],0);
    $ongerealiseerd[]='';
    $gerealiseerd[]=$this->formatGetal($perfWaarden[$portefeuille]['gerealiseerdeKoersResultaat'],0);
    $gerealiseerd[]='';
    $valutaResultaat[]=$this->formatGetal($perfWaarden[$portefeuille]['koersResulaatValutas'],0);
    $valutaResultaat[]='';
    $rente[]=$this->formatGetal($perfWaarden[$portefeuille]['opgelopenRente'],0);
    $rente[]='';
    $totaalOpbrengst[]=$this->formatGetal($perfWaarden[$portefeuille]['totaalOpbrengst'],0);
    $totaalOpbrengst[]='';
    $totaalKosten[]=$this->formatGetal($perfWaarden[$portefeuille]['totaalKosten'],0);
    $totaalKosten[]='';
    $totaal[]=$this->formatGetal($perfWaarden[$portefeuille]['totaalOpbrengst']-$perfWaarden[$portefeuille]['totaalKosten'],0);
    $totaal[]='';
    
  }     


  	$this->pdf->widthB = array(0,85,30,5,30,5,30,5,30,5,30,5,30,5);
		$this->pdf->alignB = array('L','L','R','L','R','L','R','L','R','L','R');
    $this->pdf->widthA = array(0,85,30,5,30,5,30,5,30,5,30,5,30,5);
		$this->pdf->alignA = array('L','L','R','L','R','L','R','L','R','L','R');
  
  $this->pdf->ln();
//listarray($perfWaarden);

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
  		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->fillCell=$fillArray;
    $this->pdf->SetTextColor(255,255,255);
		$this->pdf->row($header);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->fillCell=array();
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		$this->pdf->row($perbegin);
	//,$this->formatGetal($data['periode']['waardeBegin'],2,true),"",$this->formatGetal($data['ytm']['waardeBegin'],2,true),""));
    $this->pdf->CellBorders = $subOnder;
		$this->pdf->row($waardeRapdatum);//$this->formatGetal($data['periode']['waardeEind'],0),"",$this->formatGetal($data['ytm']['waardeEind'],0),""));
    $this->pdf->CellBorders = array();
			// subtotaal
		$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->ln();
		$this->pdf->row($mutwaarde);//,$this->formatGetal($data['periode']['waardeMutatie'],0),"",$this->formatGetal($data['ytm']['waardeMutatie'],0),""));
		$this->pdf->row($stortingen);////,$this->formatGetal($data['periode']['stortingen'],0),"",$this->formatGetal($data['ytm']['stortingen'],0),""));
		$this->pdf->CellBorders = $subOnder;
    $this->pdf->row($onttrekking);//,$this->formatGetal($data['periode']['onttrekkingen'],0),"",$this->formatGetal($data['ytm']['onttrekkingen'],0),""));
    $this->pdf->ln();
		$this->pdf->row($resultaat);//,$this->formatGetal($data['periode']['resultaatVerslagperiode'],0),"",$this->formatGetal($data['ytm']['resultaatVerslagperiode'],0),""));
		$this->pdf->ln();

    $this->pdf->CellBorders = $volOnder;
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row($rendement);//,$this->formatGetal($data['periode']['rendementProcent'],0),"%",$this->formatGetal($data['ytm']['rendementProcent'],0),"%"));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  $this->pdf->CellBorders = array();
		$ypos = $this->pdf->GetY();


		$this->pdf->SetY($ypos);
		$this->pdf->ln();

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->fillCell=$fillArray;
    $this->pdf->SetTextColor(255,255,255);
    $YSamenstelling=$this->pdf->GetY();
		$this->pdf->row($samenstelling);//,"","","",""));
		//$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->fillCell=array();
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->row($ongerealiseerd);//,$this->formatGetal($data['periode']['ongerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['ongerealiseerdeKoersResultaat'],0),""));
		$this->pdf->row($gerealiseerd);//,$this->formatGetal($data['periode']['gerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['gerealiseerdeKoersResultaat'],0),""));
	//	if(round($data['periode']['koersResulaatValutas'],0) != 0.00 || round($data['ytm']['koersResulaatValutas'],0) != 0.00)
		  $this->pdf->row($valutaResultaat);//,$this->formatGetal($data['periode']['koersResulaatValutas'],0),"",$this->formatGetal($data['ytm']['koersResulaatValutas'],0),""));
		$this->pdf->row($rente);//,$this->formatGetal($data['periode']['opgelopenRente'],0),"",$this->formatGetal($data['ytm']['opgelopenRente'],0),""));

		$keys=array();
		foreach ($data['periode']['opbrengstenPerGrootboek'] as $key=>$val)
		  $keys[]=$key;

		foreach ($opbrengstCategorien as $categorie)
		{
		  $tmp=array("",vertaalTekst($categorie,$this->pdf->rapport_taal));
      foreach($perfWaarden as $port=>$waarden)
      {
        $tmp[]=$this->formatGetal($waarden['opbrengstenPerGrootboek'][$categorie],0);
        $tmp[]='';
      }
		  //if(round($data['periode']['opbrengstenPerGrootboek'][$key],0) != 0.00 || round($data['ytm']['opbrengstenPerGrootboek'][$key],0) != 0.00)
			  $this->pdf->row($tmp);//;array(,$this->formatGetal($data['periode']['opbrengstenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['opbrengstenPerGrootboek'][$key],0),""));
		}

    $this->pdf->CellBorders = $subBoven;
		$this->pdf->row($totaalOpbrengst);//array("","",$this->formatGetal($data['periode']['totaalOpbrengst'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst'],0)));
		$this->pdf->ln();
    $this->pdf->CellBorders = array();

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Kosten",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		foreach ($kostenCategorien as $categorie)
		{
		  
      $tmp=array("",vertaalTekst($categorie,$this->pdf->rapport_taal));
      foreach($perfWaarden as $port=>$waarden)
      {
        $tmp[]=$this->formatGetal($waarden['kostenPerGrootboek'][$categorie],0);
        $tmp[]='';
      }
      //		  if(round($data['periode']['kostenPerGrootboek'][$key],0) != 0.00 || round($data['ytm']['kostenPerGrootboek'][$key],0) != 0.00)
			$this->pdf->row($tmp);//array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($data['periode']['kostenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['kostenPerGrootboek'][$key],0),""));
		}
    $this->pdf->CellBorders = $subBoven;
  	$this->pdf->row($totaalKosten);//$this->formatGetal($data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalKosten'],0)));
		$posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3];
    $this->pdf->CellBorders = $volOnder;
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
	  $this->pdf->row($totaal);//"","",$this->formatGetal($data['periode']['totaalOpbrengst']-$data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst']-$data['ytm']['totaalKosten'],0),''));
		$actueleWaardePortefeuille = 0;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();

}


	function portefeuilleVerdeling()
	{
    global $__appvar;
		$DB=new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) /".$this->pdf->ValutaKoersEind."  AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];
if(!isset($this->pdf->__appvar['consolidatie']))
{
$this->pdf->__appvar['consolidatie']=1;
$this->pdf->portefeuilles=array($this->portefeuille);
}
		if(isset($this->pdf->__appvar['consolidatie']))
		{

		  		    $query = "SELECT
	            	if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam,Clienten.Naam) as Naam,
                if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam1,Clienten.Naam1) as Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Portefeuilles.Depotbank,
                Portefeuilles.PortefeuilleVoorzet,
                Portefeuilles.kleurcode,
                Accountmanagers.Naam as accountManager,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email,
                Depotbanken.Omschrijving as depotbankOmschrijving
		          FROM
		            Portefeuilles
		            LEFT JOIN Clienten ON Portefeuilles.Client = Clienten.Client
		            LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
		            LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
		            LEFT Join CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille
		            Join Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
		          WHERE
		            Portefeuilles.Portefeuille IN('".implode("','",$this->pdf->portefeuilles)."')
		            ORDER BY Naam,Depotbank";
		    $DB->SQL($query);
	    	$DB->Query();
	      while($tmp = $DB->nextRecord())
	        $portefeuilledata[$tmp['Portefeuille']]=$tmp;


      foreach ($portefeuilledata as $portefeuille=>$pdata)
      {
        if(substr($this->rapportageDatum,5,5)=='01-01')
          $startjaar=true;
        else
          $startjaar=false;

        $waarden=berekenPortefeuilleWaarde($portefeuille,$this->rapportageDatum,$startjaar,$this->rapportageDatumVanaf);
        foreach ($waarden as $waarde)
        {
          $portefeuilleWaarden[$portefeuille]+=$waarde['actuelePortefeuilleWaardeEuro']/$this->pdf->ValutaKoersEind;
          $valutaWaarden[$waarde['valuta']]+=$waarde['actuelePortefeuilleWaardeEuro']/$this->pdf->ValutaKoersEind;
        }
      }


      if(!is_array($this->pdf->grafiekKleuren))
	    {
	      $q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
	    	$DB->SQL($q);
  	  	$DB->Query();
    		$kleuren = $DB->LookupRecord();
    		$kleuren = unserialize($kleuren['grafiek_kleur']);
    		$this->pdf->grafiekKleuren=$kleuren;
	    }

      $portefeuilleKleur=array();
      foreach ($portefeuilleWaarden as $portefeuille=>$waarde)
      {
        if(unserialize($portefeuilledata[$portefeuille]['kleurcode']))
          $kleur=unserialize($portefeuilledata[$portefeuille]['kleurcode']);
        else
          $kleur=array(rand(0,255),rand(0,255),rand(0,255));

        $portefeuilleAandeel[$portefeuilledata[$portefeuille]['depotbankOmschrijving']." ".$portefeuille]=$waarde/$totaalWaarde*100;
        $portefeuilleKleur[]=$kleur;
      }
      
      $tinten=array(1,0.5,0.7);
foreach($tinten as $tint)
{
$colors[]=array($this->pdf->blue[0]*$tint,$this->pdf->blue[1]*$tint,$this->pdf->blue[2]*$tint);
$colors[]=array($this->pdf->midblue[0]*$tint,$this->pdf->midblue[1]*$tint,$this->pdf->midblue[2]*$tint);//$this->pdf->midblue;
$colors[]=array($this->pdf->lightblue[0]*$tint,$this->pdf->lightblue[1]*$tint,$this->pdf->lightblue[2]*$tint);//$this->pdf->lightblue;
$colors[]=array($this->pdf->green[0]*$tint,$this->pdf->green[1]*$tint,$this->pdf->green[2]*$tint);//$this->pdf->green;
$colors[]=array($this->pdf->kopkleur[0]*$tint,$this->pdf->kopkleur[1]*$tint,$this->pdf->kopkleur[2]*$tint);//$this->pdf->kopkleur;
$colors[]=array($this->pdf->lightgreen[0]*$tint,$this->pdf->lightgreen[1]*$tint,$this->pdf->lightgreen[2]*$tint);//$this->pdf->lightgreen;
}



      
      $this->pdf->SetAligns(array('L','L','R','R'));
      $this->pdf->SetWidths(array(297-(2*$this->pdf->marge)-100,55,23,22));

      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->fillCell=array(0,1,1,1);
      $this->pdf->SetTextColor(255,255,255);
		  $this->pdf->row(array('','Portefeuilles','Waarde','in %'));
         $this->pdf->fillCell=array();
      $this->pdf->SetTextColor(0,0,0);
	
		  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		  $totalen=array();
		  foreach ($portefeuilleWaarden as $portefeuille=>$waarde)
		  {
		     $grafieknaam=$portefeuilledata[$portefeuille]['depotbankOmschrijving']." ".$portefeuille;
		     $this->pdf->row(array('',
         $portefeuilledata[$portefeuille]['Naam']." - ".$portefeuille,
         $this->formatGetal($waarde,0),
         $this->formatGetal($portefeuilleAandeel[$grafieknaam],1)));
		     $totalen['waarde']+=$waarde;
		     $totalen['aandeel']+=$portefeuilleAandeel[$grafieknaam];
		 	}
      
		 	$this->pdf->underlinePercentage=0.8;
		 	$this->pdf->CellBorders=array('','U',array('U'),array('U'));
		 	$this->pdf->row(array('','Totaal',$this->formatGetal($totalen['waarde'],0),$this->formatGetal($totalen['aandeel'],1)));
      $this->pdf->CellBorders=array();


		 	//$this->pdf->setXY(180+75/2-45/2,120);
      
      // $this->pdf->Line(297-(1*$this->pdf->marge)-100,10,297-(1*$this->pdf->marge)-100,180);//links
      // $this->pdf->Line(297-(1*$this->pdf->marge),10,297-(1*$this->pdf->marge),180);//links
      $this->pdf->setXY(297-(1*$this->pdf->marge)-50-55/2,111);
      PieChart_L41($this->pdf,55, 65, $portefeuilleAandeel, '%l',$colors,"Verdeling over depot's"); // (%p)
		}

	}
}
?>
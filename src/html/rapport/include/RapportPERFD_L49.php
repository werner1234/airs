<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/06/22 16:31:44 $
File Versie					: $Revision: 1.2 $

$Log: RapportPERFD_L49.php,v $
Revision 1.2  2019/06/22 16:31:44  rvv
*** empty log message ***

Revision 1.1  2019/06/16 09:50:08  rvv
*** empty log message ***

Revision 1.51  2014/11/30 13:18:31  rvv
*** empty log message ***

Revision 1.50  2014/11/19 16:41:56  rvv
*** empty log message ***

Revision 1.49  2014/02/08 17:42:52  rvv
*** empty log message ***

Revision 1.48  2013/10/26 15:42:06  rvv
*** empty log message ***

Revision 1.47  2013/07/17 15:52:10  rvv
*** empty log message ***

Revision 1.46  2012/10/10 13:36:56  cvs
update 10-10-2012

Revision 1.45  2012/10/07 14:56:44  rvv
*** empty log message ***

Revision 1.44  2011/09/14 09:26:56  rvv
*** empty log message ***

Revision 1.43  2010/07/31 16:07:05  rvv
*** empty log message ***

Revision 1.42  2010/06/30 16:10:10  rvv
*** empty log message ***

Revision 1.41  2010/06/09 16:38:21  rvv
*** empty log message ***

Revision 1.40  2009/07/18 14:11:49  rvv
*** empty log message ***

Revision 1.39  2009/03/14 13:24:27  rvv
*** empty log message ***

Revision 1.38  2009/01/20 17:44:09  rvv
*** empty log message ***

Revision 1.37  2008/05/16 08:12:57  rvv
*** empty log message ***

Revision 1.36  2008/03/18 09:30:24  rvv
*** empty log message ***

Revision 1.35  2007/11/16 11:22:27  rvv
*** empty log message ***

Revision 1.34  2007/11/02 12:53:13  rvv
met liquiditeiten

Revision 1.33  2007/10/12 10:06:33  rvv
*** empty log message ***

Revision 1.32  2007/10/10 08:18:40  rvv
*** empty log message ***

Revision 1.31  2007/10/04 12:01:30  rvv
*** empty log message ***

Revision 1.30  2007/08/09 08:58:31  rvv
*** empty log message ***

Revision 1.29  2007/07/10 15:54:49  rvv
AFS update

Revision 1.28  2007/07/05 12:28:39  rvv
*** empty log message ***

Revision 1.27  2007/06/29 11:38:56  rvv
L14 aanpassingen

Revision 1.26  2007/06/05 11:38:25  rvv
*** empty log message ***

Revision 1.25  2007/03/29 10:37:11  rvv
fix performance met jaarovergang

Revision 1.24  2007/03/27 14:58:20  rvv
VreemdeValutaRapportage

Revision 1.23  2006/11/03 11:24:04  rvv
Na user update

Revision 1.22  2006/10/31 12:11:04  rvv
Voor user update

Revision 1.21  2006/08/18 07:33:20  rvv
Toevoeging om het ongerealiseerde koersresultaat uit het vorige jaar mee te nemen.

Revision 1.20  2006/08/10 15:15:42  cvs
*** empty log message ***

Revision 1.19  2006/07/13 18:31:24  cvs
*** empty log message ***

Revision 1.18  2006/03/21 10:13:26  jwellner
*** empty log message ***

Revision 1.17  2006/03/01 07:56:20  jwellner
*** empty log message ***

Revision 1.16  2006/01/13 15:46:51  jwellner
diverse aanpassingen

Revision 1.15  2005/12/19 13:23:27  jwellner
no message

Revision 1.14  2005/11/30 08:37:39  jwellner
layout stuff

Revision 1.13  2005/11/25 09:30:08  jwellner
- verdiept overzicht
- layout

Revision 1.12  2005/11/21 08:39:26  jwellner
layout

Revision 1.11  2005/11/18 15:15:01  jwellner
no message

Revision 1.10  2005/11/11 16:13:50  jwellner
bufix in MUT2 , PERF en Rekenclass

Revision 1.9  2005/11/09 10:21:05  jwellner
no message

Revision 1.8  2005/11/07 10:29:17  jwellner
no message

Revision 1.7  2005/10/04 12:34:41  jwellner
no message

Revision 1.6  2005/09/30 14:05:13  jwellner
- rapport OIH
- rapport MUT2
- Layout 5
- selectieschermen

Revision 1.5  2005/09/29 15:00:18  jwellner
no message

Revision 1.4  2005/09/16 07:32:55  jwellner
aanpassingen rapportage.

Revision 1.3  2005/09/09 11:31:46  jwellner
diverse aanpassingen zie e-mails Theo

Revision 1.2  2005/07/28 15:12:37  jwellner
no message

Revision 1.1  2005/07/15 11:21:00  jwellner
Layout verwijderd, alles samengevoegd in PDFRapport

Revision 1.4  2005/07/12 15:04:20  jwellner
diverse aanpassingen

Revision 1.3  2005/07/12 07:09:50  jwellner
no message

Revision 1.2  2005/07/08 13:52:01  jwellner
no message

Revision 1.1  2005/06/30 08:22:56  jwellner
Rapportage toegevoegd

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportPERFD_L49
{

	function RapportPERFD_L49($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFD";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		//$this->pdf->rapport_PERF_displayType

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

   
    if($rapportageDatumVanaf==$rapportageDatum && substr($rapportageDatumVanaf,5,5)=='01-01')
      $this->rapportageDatumVanaf=(substr($rapportageDatumVanaf,0,4)-1).'-12-31';
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

	function printTotaal($title, $totaalA, $totaalB, $procent)
	{
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		$extra = $this->pdf->rapport_PERF_lijnenKorter;

		$actueel = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2];

		$actueeleind = $actueel + $this->pdf->widthA[3] +$this->pdf->widthA[4]+ $this->pdf->widthA[5]+ $this->pdf->widthA[6]+ $this->pdf->widthA[7];

		if(!empty($totaalA))
		{
			$this->pdf->Line($actueel+2+$extra,$this->pdf->GetY(),$actueel + $this->pdf->widthA[3],$this->pdf->GetY());
			$totaalAtxt = $this->formatGetal($totaalA,2);
		}

		if(!empty($totaalB))
		{
			$totaalBtxt = $this->formatGetal($totaalB,2);
		}

		if(!empty($procent))
			$totaalprtxt = $this->formatGetal($procent,1);

		$this->pdf->SetX($actueel);

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthA[3],4,$title, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[5],4,$totaalBtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[4],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[6],4,$totaalprtxt, 0,0, "R");

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();

		return $totaalA;
	}

	function printKop($title, $type="default")
	{
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor[r],$this->pdf->rapport_default_fontcolor[g],$this->pdf->rapport_default_fontcolor[b]);
	}
  
  function switchColor($n)
  {
    $col1=$this->pdf->achtergrondLicht;
    $col2=$this->pdf->achtergrondDonker;
    
    if($n%2==0)
      $this->pdf->SetFillColor($col1[0],$col1[1],$col1[2]);
    else
      $this->pdf->SetFillColor($col2[0],$col2[1],$col2[2]);
    
    $n++;
    return $n;
  }

	function writeRapport()
	{
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
		$this->pdf->widthA = array(80,$this->pdf->witCell,30,$this->pdf->witCell,10,$this->pdf->witCell,30,$this->pdf->witCell,120);
		$this->pdf->alignA = array('L','L','R','R','R');

		// voor kopjes
    
    $this->pdf->widthB = array(130,65,$this->pdf->witCell,30,$this->pdf->witCell,30,$this->pdf->witCell,30,$this->pdf->witCell,30);
    $this->pdf->alignB = array('L','L','R','R','R','R','R');


		$this->pdf->AddPage();
    checkPage($this->pdf);
    
    
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize+2);
    $this->pdf->setY($this->pdf->rapportYstart+2);
    $this->pdf->SetX($this->pdf->marge);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->Cell(297-$this->pdf->marge*2,4,'Performancemeting', 0, "L");
    $this->pdf->fillCell = array(1,0,1,0,1,0,1,0,1,0,1,0,1);
    $this->pdf->SetWidths(array(75,$this->pdf->witCell,15,$this->pdf->witCell,20,$this->pdf->witCell,15,$this->pdf->witCell,15,$this->pdf->witCell,15));
    $this->pdf->SetAligns(array('L','C','R','C','R','C','R','C','R','C','R'));
    $tmp=297-$this->pdf->marge*2;
    $this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array($this->pdf->koplijn[0],$this->pdf->koplijn[1],$this->pdf->koplijn[2]),'dash'=>0));
    $this->pdf->Line($this->pdf->marge,$this->pdf->rapportYstart+$this->pdf->rowHeight+3,$tmp+$this->pdf->marge,$this->pdf->rapportYstart+$this->pdf->rowHeight+3);
    $this->pdf->Ln(7);

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);

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
    
    $totaalOpbrengst=0;
		$waardeEind				= $totaalWaarde['totaal'];
		$waardeBegin 			 	= $totaalWaardeVanaf['totaal'];
		$waardeMutatie 	   	= $waardeEind - $waardeBegin;
		$stortingen 			 	= getStortingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$onttrekkingen 		 	= getOnttrekkingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
		$rendementProcent  	= performanceMeting($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
    //echo "$rendementProcent  	= performanceMeting(".$this->portefeuille.", ".$this->rapportageDatumVanaf.", ".$this->rapportageDatum.", ".$this->pdf->portefeuilledata['PerformanceBerekening'].",".$this->pdf->rapportageValuta." ";exit;
		if($this->pdf->rapport_PERF_jaarRendement)
		{
		  $RapStartJaar = date("Y", db2jul($this->rapportageDatum));
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
    
    $query="SELECT Portefeuilles.SpecifiekeIndex, Fondsen.Omschrijving,Fondsen.Valuta
    FROM Portefeuilles
    INNER JOIN Fondsen ON Portefeuilles.SpecifiekeIndex = Fondsen.Fonds
    WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";
    $DB->SQL($query);
    $DB->Query();
    $perioden=array('jan'=>$startDatum,'begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);
    $indexData=$DB->nextRecord();
    $hoofdIndex=$indexData['SpecifiekeIndex'];
    if($hoofdIndex<>'')
    {
      foreach ($perioden as $periode => $datum)
      {
        $indexData[$hoofdIndex]['fondsKoers_' . $periode] = getFondsKoers($hoofdIndex, $datum);
        //  $indexData[$hoofdIndex]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
      }
      $indexData[$hoofdIndex]['performanceJaar'] = ($indexData[$hoofdIndex]['fondsKoers_eind'] - $indexData[$hoofdIndex]['fondsKoers_jan']) / ($indexData[$hoofdIndex]['fondsKoers_jan'] / 100);
      $indexData[$hoofdIndex]['performance'] = ($indexData[$hoofdIndex]['fondsKoers_eind'] - $indexData[$hoofdIndex]['fondsKoers_begin']) / ($indexData[$hoofdIndex]['fondsKoers_begin'] / 100);
    }


		// ophalen van het totaal beginwaare en actuele waarde voor ongerealiseerde koersresultaat
 		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind."  AS totaalB, ".
 						 "SUM(beginPortefeuilleWaardeEuro)/ ".$this->pdf->ValutaKoersStart."  AS totaalA ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND "
						 ." type = 'fondsen' ".$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaal = $DB->nextRecord();
		$ongerealiseerdeKoersResultaat = $totaal['totaalB'] - $totaal['totaalA']; //huidigeJaarRapdatum - 01-01-HuidigeJaar = OngerealiseerdHuidigeJaar.



		$totaalOpbrengst += $ongerealiseerdeKoersResultaat;

		$gerealiseerdeKoersResultaat = gerealiseerdKoersresultaat($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum,$this->pdf->rapportageValuta,true);
		$totaalOpbrengst += $gerealiseerdeKoersResultaat;

		// ophalen van rente totaal A en rentetotaal B
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND ".
						 " type = 'rente' ".$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalA = $DB->nextRecord();

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND ".
						 " type = 'rente' ". $__appvar['TijdelijkeRapportageMaakUniek'] ;
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalB = $DB->nextRecord();

		$opgelopenRente = ($totaalA[totaal] - $totaalB[totaal]) / $this->pdf->ValutaKoersEind;
		$totaalOpbrengst += $opgelopenRente;


		$query = "SELECT DISTINCT(Grootboekrekeningen.Grootboekrekening), Grootboekrekeningen.Omschrijving".
		" FROM Grootboekrekeningen ".
		" WHERE Grootboekrekeningen.Opbrengst = '1'  ".
		" ORDER BY Grootboekrekeningen.Afdrukvolgorde";


		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
    $opbrengstenPerGrootboek=array();
		while($gb = $DB->nextRecord())
		{
			$query = "SELECT  ".
		  	"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
		  	"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
		  	"FROM Rekeningmutaties, Rekeningen, Portefeuilles ".
		  	"WHERE ".
		  	"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		  	"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		  	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  	"Rekeningmutaties.Verwerkt = '1' AND ".
		  	"Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
		  	"Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND ".
			  "Rekeningmutaties.Grootboekrekening = '".$gb[Grootboekrekening]."' ";

			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();

			if($this->pdf->rapport_layout == 7)
			{
				switch($gb['Omschrijving'])
				{
					case "Creditrente" :
						$gb['Omschrijving'] = "Rente Bankrekeningen";
					break;
					case "Rente obligaties" :
						$gb['Omschrijving'] = "Ontvangen rente obligaties";
					break;
					case "Meegekochte rente" :
						$gb['Omschrijving'] = "Gekochte en verkochte couponrente";
					break;
				}
			}

			while($opbrengst = $DB2->nextRecord())
			{
				$opbrengstenPerGrootboek[$gb['Omschrijving']] =  ($opbrengst['totaalcredit']-$opbrengst['totaaldebet']);
				$totaalOpbrengst += ($opbrengst['totaalcredit'] - $opbrengst['totaaldebet']);
			}
		}

		// loopje over Grootboekrekeningen Kosten = 1
		if($this->pdf->GrootboekPerVermogensbeheerder)
		{
		  $query = "SELECT GrootboekPerVermogensbeheerder.Omschrijving,GrootboekPerVermogensbeheerder.Grootboekrekening, ".
		  "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
		  "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
		  "FROM Rekeningmutaties, Rekeningen, Portefeuilles, GrootboekPerVermogensbeheerder ".
	   	"WHERE ".
		  "Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		  "Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		  "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  "Rekeningmutaties.Verwerkt = '1' AND ".
		  "Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
		  "Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND ".
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
		  "Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		  "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  "Rekeningmutaties.Verwerkt = '1' AND ".
		  "Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
		  "Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND ".
		  "Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND ".
		  "Grootboekrekeningen.Kosten = '1' ".
		  "GROUP BY Rekeningmutaties.Grootboekrekening ".
		  "ORDER BY Grootboekrekeningen.Afdrukvolgorde ";
		}

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		$kostenPerGrootboek = array();
    $totaalKosten=0;
		while($kosten = $DB->nextRecord())
		{
			if($kosten[Grootboekrekening] == "KNBA")
			{
			  $kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = "Bankkosten en provisie";
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}
			else if($kosten['Grootboekrekening'] == "KOBU")
			{
				//$kostenPerGrootboek['KOST'][Omschrijving] = "Bankkosten en provisie";
				$kostenPerGrootboek['KOST']['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
        $kostenPerGrootboek['KOST']['omschrijving'] = "Transactiekosten";
			}
			else
			{
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = $kosten['Omschrijving'];
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}

			$totaalKosten += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
		}

		$kostenProcent = ($totaalKosten / $waardeEind) * 100;


		// het overgebleven is de koers resultaat op valutas (om de getalletjes te laten kloppen).
		$koersResulaatValutas = $resultaatVerslagperiode - ($totaalOpbrengst  -  $totaalKosten);
		$totaalOpbrengst += $koersResulaatValutas;
		// ***************************** einde ophalen data voor afdruk ************************ //

		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];

		$extraLengte = $this->pdf->rapport_PERF_lijnenKorter;

  
		$this->pdf->ln();
    $n=0;
    $n=$this->switchColor($n);


    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
			$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
    
    $this->pdf->SetFillColor($this->pdf->achtergrondKop[0], $this->pdf->achtergrondKop[1], $this->pdf->achtergrondKop[2]);
    $ypos = $this->pdf->GetY();
			$this->pdf->row(array(vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal),"","","",''));
    $n=$this->switchColor($n);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);



			$this->pdf->row(array(vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",$this->pdf->rapport_datumvanaf)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datumvanaf),'',$this->formatGetal($waardeBegin,2,true),"",""));
			
    $n=$this->switchColor($n);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->row(array(vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal),'',$this->formatGetal($resultaatVerslagperiode,2),"",""));
    $n=$this->switchColor($n);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->row(array(vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal),'',$this->formatGetal($stortingen,2),"",""));
    $n=$this->switchColor($n);
			$this->pdf->row(array(vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal),'',$this->formatGetal($onttrekkingen,2),"",""));
    $n=$this->switchColor($n);
		//	$this->pdf->Line($posSubtotaal+$extraLengte ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->row(array(vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatum)),'',$this->formatGetal($waardeEind,2),"",""));
	//		$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
    $n=$this->switchColor($n);

			$this->pdf->ln();
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);

			$this->pdf->row(array(vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal),'',$this->formatGetal($rendementProcent,2),'',"%"));
    $n=$this->switchColor($n);
			if($this->pdf->rapport_PERF_jaarRendement)
			  $this->pdf->row(array(vertaalTekst("Rendement lopende kalenderjaar",$this->pdf->rapport_taal),'',$this->formatGetal($rendementProcentJaar,2),'',"%",""));
    $n=$this->switchColor($n);
    
    
    if($hoofdIndex<>'')
    {
      $this->pdf->ln();
      $this->pdf->row(array(vertaalTekst("Benchmark over verslagperiode",$this->pdf->rapport_taal),'',$this->formatGetal($indexData[$hoofdIndex]['performance'],2),'',"%"));
      $n=$this->switchColor($n);
      $this->pdf->row(array(vertaalTekst("Benchmark lopende kalenderjaar",$this->pdf->rapport_taal),'',$this->formatGetal($indexData[$hoofdIndex]['performanceJaar'],2),'',"%",""));
      $n=$this->switchColor($n);
    }

	
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->fillCell = array(0,1,0,1,0,1,0,1,0,1,0,1,0,1);




		$this->pdf->SetY($ypos);


		$this->pdf->SetWidths(array($this->pdf->widthB[0],$this->pdf->widthB[1]+$this->pdf->widthB[2]+$this->pdf->widthB[3],$this->pdf->widthB[4],$this->pdf->widthB[5]));
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($this->pdf->achtergrondKop[0], $this->pdf->achtergrondKop[1], $this->pdf->achtergrondKop[2]);
		$this->pdf->row(array("",vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal),"",""));
    $this->pdf->SetWidths($this->pdf->widthB);
    $n=$this->switchColor($n);
		$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"","","",""));
    $n=$this->switchColor($n);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


			$this->pdf->row(array("",vertaalTekst("Ongerealiseerde koersresultaten",$this->pdf->rapport_taal),'',$this->formatGetal($ongerealiseerdeKoersResultaat,2),"",'',""));
    $n=$this->switchColor($n);
			$this->pdf->row(array("",vertaalTekst("Gerealiseerde koersresultaten",$this->pdf->rapport_taal),'',$this->formatGetal($gerealiseerdeKoersResultaat,2),"",'',""));
    $n=$this->switchColor($n);
			if(round($koersResulaatValutas,2) != 0.00)
      {
        $this->pdf->row(array("", vertaalTekst("Koersresultaten valuta's", $this->pdf->rapport_taal), '', $this->formatGetal($koersResulaatValutas, 2), "", '', ""));
        $n=$this->switchColor($n);
      }
	  $this->pdf->row(array("",vertaalTekst("Resultaat opgelopen rente",$this->pdf->rapport_taal),'',$this->formatGetal($opgelopenRente,2),'','',""));
    $n=$this->switchColor($n);

		
		foreach($opbrengstenPerGrootboek as $key=>$value)
    {
		  if(round($value,2) != 0.00)
      {
        $this->pdf->row(array("", vertaalTekst($key, $this->pdf->rapport_taal), '', $this->formatGetal($value, 2), "",''));
        $n=$this->switchColor($n);
  
      }
		}

		$this->pdf->row(array("","","","",'',$this->formatGetal($totaalOpbrengst,2)));
		$this->pdf->ln();

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Kosten",$this->pdf->rapport_taal),"","",'',''));
    $n=$this->switchColor($n);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		
    foreach($kostenPerGrootboek as $key=>$value)
		{
		  if(round($kostenPerGrootboek[$key]['Bedrag'],2) != 0.00)
      {
        $this->pdf->row(array("", vertaalTekst($kostenPerGrootboek[$key]['Omschrijving'], $this->pdf->rapport_taal), '', $this->formatGetal($kostenPerGrootboek[$key]['Bedrag'], 2), "",''));
        $n=$this->switchColor($n);
      }
		}

		$this->pdf->row(array("","","","",'',$this->formatGetal($totaalKosten,2)));
    $n=$this->switchColor($n);
    $this->pdf->row(array("","","","",'',$this->formatGetal($totaalOpbrengst - $totaalKosten,2)));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


    paginaVoet($this->pdf);
	}
}
?>
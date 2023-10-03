<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/05/30 16:10:58 $
File Versie					: $Revision: 1.5 $

$Log: RapportPERF_L22.php,v $
Revision 1.5  2018/05/30 16:10:58  rvv
*** empty log message ***

Revision 1.4  2013/07/17 15:53:14  rvv
*** empty log message ***

Revision 1.3  2012/09/30 11:18:17  rvv
*** empty log message ***

Revision 1.2  2011/02/26 16:02:23  rvv
*** empty log message ***

Revision 1.1  2010/11/27 16:16:59  rvv
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

class RapportPERF_L22
{

	function RapportPERF_L22($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		if($this->pdf->rapport_PERF_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_PERF_titel;
		else
			$this->pdf->rapport_titel = "Performancemeting (in ".$this->pdf->rapportageValuta.")";


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
		switch($type)
		{
			case "b" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'b';
			break;
			case "bi" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'bi';
			break;
			case "i" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'i';
			break;
			default :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = '';
			break;
		}


		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor[r],$this->pdf->rapport_default_fontcolor[g],$this->pdf->rapport_default_fontcolor[b]);
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
		$this->pdf->widthA = array(5,80,30,10,30,120);
		$this->pdf->alignA = array('L','L','R','R','R');

		// voor kopjes
		$this->pdf->widthB = array(1,95,30,10,30,120);
		$this->pdf->alignB = array('L','L','R','R','R');

		$this->pdf->AddPage();

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

		$waardeEind				= $totaalWaarde[totaal];
		$waardeBegin 			 	= $totaalWaardeVanaf[totaal];
		$waardeMutatie 	   	= $waardeEind - $waardeBegin;
		$stortingen 			 	= getStortingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$onttrekkingen 		 	= getOnttrekkingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
		$rendementProcent  	= performanceMeting($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
    //echo "$rendementProcent  	= performanceMeting(".$this->portefeuille.", ".$this->rapportageDatumVanaf.", ".$this->rapportageDatum.", ".$this->pdf->portefeuilledata['PerformanceBerekening'].",".$this->pdf->rapportageValuta." ";exit;

    if(substr($this->rapportageDatumVanaf,5,5)=='01-01')
      $this->pdf->rapport_PERF_jaarRendement=0;
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
		$ongerealiseerdeKoersResultaat = $totaal[totaalB] - $totaal[totaalA]; //huidigeJaarRapdatum - 01-01-HuidigeJaar = OngerealiseerdHuidigeJaar.
	//echo " $query ".$ongerealiseerdeKoersResultaat." = ".$totaal[totaalB]." - ".$totaal[totaalA].";<br>\n";
//rvv 	Extra query die het mogelijk maakt om een startdatum na 1-1-jaar te kiezen. Het resultaat binnen het lopende jaar tot de start
//		datum wordt van het totaal afgehaald.
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin." AS totaalB, ".
 						 "SUM(beginPortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersStart." ) AS totaalA ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND "
						 . " type = 'fondsen' ".$__appvar['TijdelijkeRapportageMaakUniek'];
//		debugSpecial($query,__FILE__,__LINE__);
//		$DB->SQL($query);
//		$DB->Query();
//		$totaalWaardeVanaf = $DB->nextRecord();
//		$ongerealiseerdeKoersResultaatTotStart = $totaalWaardeVanaf[totaalB] - $totaalWaardeVanaf[totaalA];
//		$ongerealiseerdeKoersResultaat -= $ongerealiseerdeKoersResultaatTotStart;

$RapJaar = date("Y", db2jul($this->rapportageDatum));
$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));

/*
if ($RapJaar != $RapStartJaar) //Wanneer we startdatum in het afgelopen jaar kiezen moeten we de resultaten van dat jaar ook ophalen.
{
    	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".getValutaKoers($this->pdf->rapportageValuta,$RapStartJaar."-12-31")."  AS totaalB, ".
 						 "SUM(beginPortefeuilleWaardeEuro) / ".getValutaKoers($this->pdf->rapportageValuta,$RapStartJaar."-01-01")." AS totaalA ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$RapStartJaar."-12-31' AND ".
						 " portefeuille = '".$this->portefeuille."' AND ".
						 " type = 'fondsen' ".$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalVorigeJaar = $DB->nextRecord();
		$ongerealiseerdeKoersResultaatVorigJaar = ($totaalVorigeJaar[totaalB] - $totaalVorigeJaar[totaalA]);
		$ongerealiseerdeKoersResultaat += $ongerealiseerdeKoersResultaatVorigJaar  ;
}
*/
//rvv end
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

		if($this->pdf->GrootboekPerVermogensbeheerder)
		{
		  $query = "SELECT DISTINCT(GrootboekPerVermogensbeheerder.Grootboekrekening), GrootboekPerVermogensbeheerder.Omschrijving FROM GrootboekPerVermogensbeheerder
                WHERE GrootboekPerVermogensbeheerder.Opbrengst = '1' AND GrootboekPerVermogensbeheerder.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
                ORDER BY GrootboekPerVermogensbeheerder.Afdrukvolgorde";
		}
		else
		{
		$query = "SELECT DISTINCT(Grootboekrekeningen.Grootboekrekening), Grootboekrekeningen.Omschrijving".
		" FROM Grootboekrekeningen ".
		" WHERE Grootboekrekeningen.Opbrengst = '1'  ".
		" ORDER BY Grootboekrekeningen.Afdrukvolgorde";
		}

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

		while($kosten = $DB->nextRecord())
		{
			if($kosten[Grootboekrekening] == "KNBA")
			{
			  if($this->pdf->rapport_layout == 17)
			   $kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = $kosten['Omschrijving'];
			  else
				  $kostenPerGrootboek[$kosten[Grootboekrekening]][Omschrijving] = "Bankkosten en provisie";
				$kostenPerGrootboek[$kosten[Grootboekrekening]][Bedrag] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}
			else if($kosten[Grootboekrekening] == "KOBU" && $this->pdf->rapport_layout != 14 )
			{
				$kostenPerGrootboek['KOST']['Omschrijving'] = "Transactiekosten";
				$kostenPerGrootboek['KOST'][Bedrag] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}
			else
			{
				$kostenPerGrootboek[$kosten[Grootboekrekening]][Omschrijving] = $kosten['Omschrijving'];
				$kostenPerGrootboek[$kosten[Grootboekrekening]][Bedrag] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
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

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->ln();

		if($this->pdf->rapport_PERF_displayType == 1 || $this->pdf->lastPOST['perfBm'])
		{
			$ypos = $this->pdf->GetY();

			$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
			$this->pdf->row(array("",vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal),"",""));
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)),$this->formatGetal($waardeBegin,2,true),""));
			$this->pdf->ln(2);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->row(array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($resultaatVerslagperiode,2),""));
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln(2);
			$this->pdf->row(array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($stortingen,2),""));
			$this->pdf->ln(2);
			$this->pdf->row(array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($onttrekkingen,2),""));
			$this->pdf->Line($posSubtotaal+$extraLengte ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->ln(2);
			$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum)),$this->formatGetal($waardeEind,2),""));
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());

			$this->pdf->ln();
			$this->pdf->ln();
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);

			$this->pdf->row(array("",vertaalTekst("Netto rendement over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($rendementProcent,2),"%"));
			if($this->pdf->rapport_PERF_jaarRendement)
			{
	 			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY(),$posSubtotaalEnd ,$this->pdf->GetY());
  			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY()+1 ,$posSubtotaalEnd ,$this->pdf->GetY()+1);
			  $this->pdf->ln();
			  $this->pdf->row(array("",vertaalTekst("Netto rendement lopende kalenderjaar",$this->pdf->rapport_taal),$this->formatGetal($rendementProcentJaar,2),"%",""));
			}

			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY(),$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY()+1 ,$posSubtotaalEnd ,$this->pdf->GetY()+1);

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

			$this->pdf->widthA = array(130,80,30,5,30,120);
			$this->pdf->alignA = array('L','L','R','R','R');

			$this->pdf->widthB = array(125,95,30,5,30,120);
			$this->pdf->alignB = array('L','L','R','R','R');

			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
			$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];
		}
		else
		{
			$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
			$this->pdf->row(array("",vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal),"",""));
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)),$this->formatGetal($waardeBegin,2,true),""));
			$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum)),$this->formatGetal($waardeEind,2),""));

			// subtotaal
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->ln();
			$this->pdf->row(array("",vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal),$this->formatGetal($waardeMutatie,2),""));
			$this->pdf->row(array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($stortingen,2),""));
			$this->pdf->row(array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($onttrekkingen,2),""));
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->ln();
			$this->pdf->row(array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($resultaatVerslagperiode,2),""));
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY()+1 ,$posSubtotaalEnd ,$this->pdf->GetY()+1);
			$this->pdf->ln();



			$this->pdf->row(array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($rendementProcent,2),"%"));
			if($this->pdf->rapport_PERF_jaarRendement)
			{
			  $this->pdf->ln();
			  $this->pdf->row(array("",vertaalTekst("Rendement lopende kalenderjaar",$this->pdf->rapport_taal),$this->formatGetal($rendementProcentJaar,2),"%",""));
			}

			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY()+1 ,$posSubtotaalEnd ,$this->pdf->GetY()+1);

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

			$ypos = $this->pdf->GetY();
		}

		$this->pdf->SetY($ypos);
		$this->pdf->ln();

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		if($this->pdf->rapport_layout == 7)
		{
			$this->pdf->row(array("",vertaalTekst("Ongerealiseerde koers- en valutaresultaten",$this->pdf->rapport_taal),$this->formatGetal($ongerealiseerdeKoersResultaat,2),""));
			$this->pdf->row(array("",vertaalTekst("Gerealiseerde koers- en valutaresultaten",$this->pdf->rapport_taal),$this->formatGetal($gerealiseerdeKoersResultaat,2),""));
			$this->pdf->row(array("",vertaalTekst("Resultaat op V.V.-rekeningen",$this->pdf->rapport_taal),$this->formatGetal($koersResulaatValutas,2),""));
			$this->pdf->row(array("",vertaalTekst("Resultaat opgelopen rente obligaties",$this->pdf->rapport_taal),$this->formatGetal($opgelopenRente,2),""));
		}
		else
		{
			$this->pdf->row(array("",vertaalTekst("Ongerealiseerde koersresultaten",$this->pdf->rapport_taal),$this->formatGetal($ongerealiseerdeKoersResultaat,2),""));
			$this->pdf->row(array("",vertaalTekst("Gerealiseerde koersresultaten",$this->pdf->rapport_taal),$this->formatGetal($gerealiseerdeKoersResultaat,2),""));
			if(round($koersResulaatValutas,2) != 0.00)
			  $this->pdf->row(array("",vertaalTekst("Koersresultaten valuta's",$this->pdf->rapport_taal),$this->formatGetal($koersResulaatValutas,2),""));
      if($this->pdf->rapport_layout == 5)
			  $this->pdf->row(array("",vertaalTekst("Mutatie opgelopen rente",$this->pdf->rapport_taal),$this->formatGetal($opgelopenRente,2),""));
			else
			  $this->pdf->row(array("",vertaalTekst("Resultaat opgelopen rente",$this->pdf->rapport_taal),$this->formatGetal($opgelopenRente,2),""));
		}

		if($this->pdf->rapport_layout == 5 )
		{
     // $opbrengstenLayout5['Rente obligaties']     =  $opbrengstenPerGrootboek['Rente obligaties'];
     // $opbrengstenLayout5['Meegekochte rente']    =  $opbrengstenPerGrootboek['Meegekochte rente'];
    //  $opbrengstenLayout5['Dividend']             =  $opbrengstenPerGrootboek['Dividend'];
    //  $opbrengstenLayout5['Dividendbelasting']    =  $opbrengstenPerGrootboek['Dividendbelasting'];
    //  $opbrengstenLayout5['Creditrente']          =  $opbrengstenPerGrootboek['Creditrente'];
      $opbrengstenPerGrootboek['Fractieverrekeningen'] =  $opbrengstenPerGrootboek['Stockdividend'];
      unset($opbrengstenPerGrootboek['Stockdividend']);
		 // $opbrengstenPerGrootboek = $opbrengstenLayout5;
		}

		while (list($key, $value) = each($opbrengstenPerGrootboek))
		{
		  if(round($value,2) != 0.00)
			  $this->pdf->row(array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($value,2),""));
		}

		$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->row(array("","","","",$this->formatGetal($totaalOpbrengst,2)));
		$this->pdf->ln();

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Kosten",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		while (list($key, $value) = each($kostenPerGrootboek))
		{
		  if(round($kostenPerGrootboek[$key]['Bedrag'],2) != 0.00)
			  $this->pdf->row(array("",vertaalTekst($kostenPerGrootboek[$key]['Omschrijving'],$this->pdf->rapport_taal),$this->formatGetal($kostenPerGrootboek[$key]['Bedrag'],2),""));
		}

		$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->row(array("","","","",$this->formatGetal($totaalKosten,2)));

		$posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3];

		$this->pdf->Line($posTotaal +2+$extraLengte  ,$this->pdf->GetY() ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY());

		if($this->pdf->rapport_layout == 17)
		{


    $this->pdf->SetWidths(array(130,50,60,5,30,120));
	//	  $this->pdf->row(array("","","","Resultaat over verslagperiode",$this->formatGetal($totaalOpbrengst - $totaalKosten,2)));
		  $this->pdf->row(array("","","Resultaat over verslagperiode","",$this->formatGetal($totaalOpbrengst - $totaalKosten,2)));
		  $this->pdf->SetWidths($this->pdf->widthA);
		}
		else
		  $this->pdf->row(array("","","","",$this->formatGetal($totaalOpbrengst - $totaalKosten,2)));

		$this->pdf->Line($posTotaal +2+$extraLengte  ,$this->pdf->GetY() ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY());
		$this->pdf->Line($posTotaal +2+$extraLengte  ,$this->pdf->GetY()+1 ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY()+1);

		$actueleWaardePortefeuille = 0;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		if($this->pdf->rapport_PERF_rendement == 1)
		{

		  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
		  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul( "$RapStartJaar-01-01"))
		    $startDatum =  $this->pdf->PortefeuilleStartdatum;
		  else
		    $startDatum = "$RapStartJaar-01-01";

	    if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01")
	    {
	    $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,$startDatum,true);
      vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,$startDatum);
	    }

	    $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,$this->pdf->PortefeuilleStartdatum,true);
      vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,$this->pdf->PortefeuilleStartdatum);

	    $performanceJaar = performanceMeting($this->portefeuille,$startDatum,$this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
		  $performancePeriode = performanceMeting($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
//		  $performanceBegin  = performanceMeting($this->portefeuille,$this->pdf->PortefeuilleStartdatum,$this->rapportageDatum,1,$this->pdf->rapportageValuta);

		  $this->pdf->SetY($this->pdf->GetY()+30);
		  $extraMarge = 140;
		  $this->pdf->SetX($this->pdf->marge );
		  $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor[r],$this->pdf->rapport_kop_bgcolor[g],$this->pdf->rapport_kop_bgcolor[b]);
      $min = 6;
		  $this->pdf->Rect($this->pdf->marge + $extraMarge,$this->pdf->getY(),110,(20-$min),'F');
		  $this->pdf->SetFillColor(0);
		  $this->pdf->Rect($this->pdf->marge + $extraMarge,$this->pdf->getY(),110,(20-$min));
		  $this->pdf->ln(2);

      $this->pdf->SetX($this->pdf->marge  +$extraMarge +10);
			$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[r],$this->pdf->rapport_kop_fontcolor[g],$this->pdf->rapport_kop_fontcolor[b]);
			$this->pdf->Cell(60,4, vertaalTekst("Resultaat over verslagperiode",$this->rapport_taal), 0,0, "L");
			$this->pdf->Cell(30,4, $this->formatGetal($performancePeriode,2)."%", 0,1, "R");
			$this->pdf->ln(2);

			$this->pdf->SetX($this->pdf->marge  +$extraMarge +10);
	    $this->pdf->Cell(60,4, vertaalTekst("Resultaat lopende kalenderjaar",$this->rapport_taal), 0,0, "L");
		  $this->pdf->Cell(30,4, $this->formatGetal($performanceJaar,2)."%", 0,1, "R");
			$this->pdf->ln(2);

//			$this->pdf->SetX($this->pdf->marge  +$extraMarge +10);
//	    $this->pdf->Cell(60,4, vertaalTekst("Resultaat vanaf begin beheer / ".jul2form(db2jul($this->pdf->PortefeuilleStartdatum)),$this->rapport_taal), 0,0, "L");
//		  $this->pdf->Cell(30,4, $this->formatGetal($performanceBegin,2)."%", 0,1, "R");
//		  $this->pdf->ln(2);
		}



$query ="SELECT Portefeuilles.SpecifiekeIndex , Fondsen.Omschrijving
 FROM Portefeuilles JOIN Fondsen on Portefeuilles.SpecifiekeIndex = Fondsen.Fonds
  WHERE Portefeuilles.Portefeuille = '".$this->portefeuille."' ";
$DB->SQL($query);
$data = $DB->lookupRecord();
$specifiekeIndex = $data['SpecifiekeIndex'];
$specifiekeIndexOmschrijving = $data['Omschrijving'];

 	  if($this->pdf->rapport_PERF_portefeuilleIndex == 1)
		{
		  $grafiekData = array();
		  $query = "SELECT indexWaarde, Datum ,
		      (SELECT Koers  FROM Fondskoersen WHERE fonds = '".$specifiekeIndex."' AND MONTH(Datum) = MONTH(HistorischePortefeuilleIndex.Datum) ORDER BY Datum DESC limit 1) as specifiekeIndexWaarde

		           FROM HistorischePortefeuilleIndex WHERE periode='m' AND portefeuille = '".$this->portefeuille."' AND Datum < '".$this->rapportageDatum."'";
		  $DB->SQL($query);
		  $DB->Query();
			  $n=0;
		  while ($data = $DB->nextRecord())
		  {
        $grafiekData['Datum'][] = $data['Datum'];
        $grafiekData['Index'][] = ($data['indexWaarde']);
        $specifiekeIndexWaarde[$n]=$data['specifiekeIndexWaarde'];
		    if($n==0)
		    {

		      $db2=new DB();
		      $query = "SELECT Koers FROM Fondskoersen WHERE fonds = '".$specifiekeIndex."' AND Datum > '".$grafiekData['Datum'][0]."' LIMIT 1";
		      $db2->sql($query);
		      $db2->Query();
		      $indexStart = $db2->lookupRecord();
         $grafiekData['Index1'][$n] = ($data['specifiekeIndexWaarde']/$indexStart['Koers']*100);
		    }
		    else
		    {
 		      $grafiekData['Index1'][$n] = ($data['specifiekeIndexWaarde']/$indexStart['Koers']*100);
 		    }
		    $n++;
		  }
//		  listarray($indexStart);
//listarray($specifiekeIndexWaarde);
//listarray($grafiekData);
		  if (count($grafiekData) > 1)
		  {
		  $color= array(30,23,96);
		  $color1 = array(167,26,32);
	    $this->pdf->SetXY(10,108)		;
		  $this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
		  $this->pdf->Cell(0, 5, 'Vermogensontwikkeling', 0, 1);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->SetX(15,$this->pdf->GetY()+2);
      $valX = $this->pdf->GetX();
      $valY = $this->pdf->GetY();
      $this->pdf->LineDiagram(108, 60, $grafiekData,array($color,$color1),0,0,4,4);

      $this->pdf->Rect($valX, $valY+70, 3, 3 ,'F','',$color);
      $this->pdf->SetXY($valX+4, $valY + 70);
      $this->pdf->Cell(0, 4, 'Portefeuille', 0, 0);

      $this->pdf->Rect($valX+30, $valY+70, 3, 3 ,'F','',$color1);
      $this->pdf->SetXY($valX+4+30, $valY + 70);
      $this->pdf->Cell(0, 4, $specifiekeIndexOmschrijving, 0, 0);

      $this->pdf->SetXY($valX, $valY + 80);
      }
		}

		if($this->pdf->rapport_PERF_liquiditeiten == 1)
		{
		  $this->pdf->ln();

		  $this->pdf->SetWidths($this->pdf->widthB);
		  $this->pdf->SetAligns($this->pdf->alignB);
		  $this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
		  $this->pdf->row(array("",vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),"",""));
		  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
 		  $this->pdf->SetWidths($this->pdf->widthA);
	 	  $this->pdf->SetAligns($this->pdf->alignA);

		  $query = "SELECT SUM(actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersBegin." ) AS totaal ".
						   "FROM TijdelijkeRapportage WHERE ".
						   " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
						   " portefeuille = '".$this->portefeuille."' AND
						     type <> 'fondsen' AND type = 'rekening' "
						   .$__appvar['TijdelijkeRapportageMaakUniek'];
		           debugSpecial($query,__FILE__,__LINE__);

		  $DB->SQL($query);
		  $DB->Query();
		  $totaalWaardeLiquiditeitenVanaf = $DB->nextRecord();
		 	$this->pdf->row(array("",vertaalTekst("Saldo liquiditeiten per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)),$this->formatGetal($totaalWaardeLiquiditeitenVanaf['totaal'],2),""));


		  $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS totaal ".
			    		 "FROM TijdelijkeRapportage WHERE ".
					  	 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						   " portefeuille = '".$this->portefeuille."' AND
						     type <> 'fondsen' AND type = 'rekening' "
						   .$__appvar['TijdelijkeRapportageMaakUniek'];
		  debugSpecial($query,__FILE__,__LINE__);
      $DB->SQL($query);
     	$DB->Query();
     	$totaalWaardeLiquiditeiten = $DB->nextRecord();
		  $this->pdf->row(array("",vertaalTekst("Saldo liquiditeiten per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum)),$this->formatGetal($totaalWaardeLiquiditeiten['totaal'],2),""));

		  if($this->pdf->lastPOST['perfBm'])
		  {
		    $this->pdf->ln();
		    $this->pdf->printAEXVergelijking($this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);
		  }
		}

	}
}
?>
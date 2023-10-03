<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/18 14:58:47 $
File Versie					: $Revision: 1.19 $

$Log: RapportPERF_L7.php,v $
Revision 1.19  2020/07/18 14:58:47  rvv
*** empty log message ***

Revision 1.18  2019/08/11 07:06:35  rvv
*** empty log message ***

Revision 1.17  2019/08/10 17:27:40  rvv
*** empty log message ***

Revision 1.16  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.15  2018/05/12 15:46:42  rvv
*** empty log message ***

Revision 1.14  2016/07/17 19:23:14  rvv
*** empty log message ***

Revision 1.13  2016/07/16 15:16:49  rvv
*** empty log message ***

Revision 1.12  2016/06/29 16:04:07  rvv
*** empty log message ***

Revision 1.11  2016/06/25 16:57:02  rvv
*** empty log message ***

Revision 1.10  2016/05/29 10:19:26  rvv
*** empty log message ***

Revision 1.9  2016/05/28 14:21:20  rvv
*** empty log message ***

Revision 1.8  2016/05/15 17:15:00  rvv
*** empty log message ***

Revision 1.7  2016/04/13 16:30:05  rvv
*** empty log message ***

Revision 1.6  2016/04/10 15:48:34  rvv
*** empty log message ***

Revision 1.5  2016/04/06 15:30:51  rvv
*** empty log message ***

Revision 1.4  2016/03/30 10:35:05  rvv
*** empty log message ***

Revision 1.3  2016/03/28 15:53:33  rvv
*** empty log message ***

Revision 1.2  2016/03/27 18:15:30  rvv
*** empty log message ***

Revision 1.1  2016/03/27 17:35:07  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportPERF_L7
{

	function RapportPERF_L7($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Vermogensontwikkeling in ".$this->pdf->rapportageValuta."";


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
		$this->pdf->widthA = array(0,85,30,10,30,120);
		$this->pdf->alignA = array('L','L','R','L','R');

		// voor kopjes
		$this->pdf->widthB = array(0,95,30,10,30,120);
		$this->pdf->alignB = array('L','L','R','L','R');

		$this->pdf->AddPage();
    $this->pdf->templateVars['PERFPaginas']=$this->pdf->page;
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
    $kruispost=true;
		$waardeEind				= $totaalWaarde[totaal];
		$waardeBegin 			 	= $totaalWaardeVanaf[totaal];
		$waardeMutatie 	   	= $waardeEind - $waardeBegin;
		$stortingen 			 	= getStortingenKruis($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta,$kruispost);
		$onttrekkingen 		 	= getOnttrekkingenKruis($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta,$kruispost);
		$interneboeking     = $stortingen['kruispost']-$onttrekkingen['kruispost'];
		$stortingen         = $stortingen['storting'];
		$onttrekkingen      = $onttrekkingen['onttrekking'];

		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen -$interneboeking;
  //echo "perf: $resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen <br>\n";
    if(substr($this->rapportageDatum,0,4) < 2016)
      $perfBerekening=2;
    else 
      $perfBerekening=$this->pdf->portefeuilledata['PerformanceBerekening'];

    //$rendementProcent  	= performanceMeting_L7($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $perfBerekening,$this->pdf->rapportageValuta,$kruispost);
    $rendementProcent  	= performanceMeting($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $perfBerekening,$this->pdf->rapportageValuta);
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
	    //$rendementProcentJaar = performanceMeting_L7($this->portefeuille,$startDatum,$this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta,$kruispost);
      $rendementProcentJaar = performanceMeting($this->portefeuille,$startDatum,$this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
		}

		// ophalen van het totaal beginwaare en actuele waarde voor ongerealiseerde koersresultaat
		/*
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
		*/

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind." - beginPortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersStart." ) AS resultaatEUR,
  SUM(totaalAantal*fondsEenheid*(actueleFonds-beginwaardeLopendeJaar)*actueleValuta) / ".$this->pdf->ValutaKoersEind." as fondsresultaatEUR".
			" FROM TijdelijkeRapportage WHERE ".
			" rapportageDatum ='".$this->rapportageDatum."'  AND".
			" portefeuille = '".$this->portefeuille."' AND "
			." type = 'fondsen' ".$__appvar['TijdelijkeRapportageMaakUniek'];
		$DB->SQL($query); //echo $query;exit;
		$DB->Query();
		$totaal = $DB->nextRecord();
		$ongerealiseerdFondsResultaat = $totaal['fondsresultaatEUR'] ;
		$ongerealiseerdValutaResultaat = $totaal['resultaatEUR']-$totaal['fondsresultaatEUR'] ;
		$ongerealiseerdeKoersResultaat=$ongerealiseerdFondsResultaat+$ongerealiseerdValutaResultaat;


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



    
 $grootboeken=array('DIV'=>'','VKSTO'=>'','RENOB'=>'','RENME'=>'',"RENTEMUTATIE"=>'','RENTE'=>'','DIVBE'=>'');	
$query = "SELECT DISTINCT(Grootboekrekeningen.Grootboekrekening) as Grootboekrekening, Grootboekrekeningen.Omschrijving".
		" FROM Grootboekrekeningen ".
		" WHERE Grootboekrekeningen.Opbrengst = '1' OR Grootboekrekeningen.Grootboekrekening IN('ROER') ".
		" ORDER BY Grootboekrekeningen.Afdrukvolgorde";//,if(Grootboekrekeningen.Grootboekrekening='VKSTO',1.1,round(Grootboekrekeningen.Afdrukvolgorde,1)) as volgorde

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		while($gb = $DB->nextRecord())
		{
		  $grootboeken[$gb['Grootboekrekening']]=$gb;
    }
    
    foreach($grootboeken as $gbVeld=>$gb)
    {
      if($gbVeld=='RENTEMUTATIE')
      {
     		$opbrengstenPerGrootboek[vertaalTekst("Mutatie opgelopen obligatierente",$this->pdf->rapport_taal)] = $opgelopenRente;
				$totaalOpbrengst += $opgelopenRente; 
      }
      else
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
			  "Rekeningmutaties.Grootboekrekening = '".$gb['Grootboekrekening']."' ";

		  	$DB2 = new DB();
		  	$DB2->SQL($query);
			  $DB2->Query();

				switch($gb['Grootboekrekening'])
				{
					case "RENOB" :
						$gb['Omschrijving'] = "Ontvangen obligatierente";
					break;
					case "DIV" :
						$gb['Omschrijving'] = "Dividenden";
					break;
					case "RENME" :
						$gb['Omschrijving'] = "Meegekochte- en verkochte obligatierente";
					break;
					case "DIVBE" :
          case "ROER" :
						$gb['Omschrijving'] = "Ingehouden belastingen op rente en dividenden";
					break;          
          
				}

		  	while($opbrengst = $DB2->nextRecord())
		  	{
			  	$opbrengstenPerGrootboek[$gb['Omschrijving']] +=  ($opbrengst['totaalcredit']-$opbrengst['totaaldebet']);
			  	$totaalOpbrengst += ($opbrengst['totaalcredit'] - $opbrengst['totaaldebet']);
		  	}
      }
		}

		// loopje over Grootboekrekeningen Kosten = 1

		  $query = "SELECT if (Rekeningmutaties.Fonds<>'',1,if(Rekeningmutaties.Omschrijving like 'Dividend%',1,if(Rekeningmutaties.Omschrijving like 'Coupon%',1,0))) AS fondsgekoppeld,
		  Grootboekrekeningen.Omschrijving,Grootboekrekeningen.Grootboekrekening, ".
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
		  "Grootboekrekeningen.Kosten = '1' AND Grootboekrekeningen.Grootboekrekening NOT IN('ROER')  ".
		  "GROUP BY Rekeningmutaties.Grootboekrekening, fondsgekoppeld ".
		  "ORDER BY Grootboekrekeningen.Afdrukvolgorde, fondsgekoppeld desc ";
	

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		$kostenPerGrootboek = array();
		$totaalKosten=0;
		while($kosten = $DB->nextRecord())
		{
			if($kosten['Grootboekrekening'] == "KNBA")
			{
				if($kosten['fondsgekoppeld']==1)
				{
					$kostenPerGrootboek['KNBAF']['Omschrijving'] = "Bankkosten op rente en dividenden";
					$kostenPerGrootboek['KNBAF']['Bedrag'] += ($kosten['totaalcredit'] - $kosten['totaaldebet']);
				}
				else
				{
					$kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = "Bankkosten overige";
					$kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaalcredit'] - $kosten['totaaldebet']);
				}
			}
			else if($kosten['Grootboekrekening'] == "KOBU")
			{
				$kostenPerGrootboek['KOST']['Bedrag'] += ($kosten['totaalcredit']-$kosten['totaaldebet']);
        $kostenPerGrootboek['KOST']['Omschrijving'] = "Transactiekosten";
			}
			else
			{
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = $kosten['Omschrijving'];
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaalcredit']-$kosten['totaaldebet']);
			}

			$totaalKosten += ($kosten['totaalcredit']-$kosten['totaaldebet']);
		}
		// het overgebleven is de koers resultaat op valutas (om de getalletjes te laten kloppen).
		$koersResulaatValutas = $resultaatVerslagperiode - ($totaalOpbrengst  +  $totaalKosten);
		$totaalOpbrengst += $koersResulaatValutas;
		// ***************************** einde ophalen data voor afdruk ************************ //

		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];

		$extraLengte = 0;

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->ln();


			$ypos = $this->pdf->GetY();

			$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
			$this->pdf->row(array("",vertaalTekst("Verloop vermogen",$this->pdf->rapport_taal),"",""));
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",$this->pdf->rapport_datumvanaf)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datumvanaf)));
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->ln(-4);
      $this->pdf->row(array("",'',$this->formatGetal($waardeBegin,2,true)));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      
			$this->pdf->ln(2);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->row(array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($resultaatVerslagperiode,2),""));
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln(2);
			$this->pdf->row(array("",vertaalTekst("Stortingen",$this->pdf->rapport_taal),$this->formatGetal($stortingen,2),""));
			$this->pdf->ln(2);
			$this->pdf->row(array("",vertaalTekst("Onttrekkingen",$this->pdf->rapport_taal),$this->formatGetal($onttrekkingen*-1,2),""));
		  $this->pdf->ln(2);
		  $this->pdf->row(array("",vertaalTekst("Interne overboekingen",$this->pdf->rapport_taal),$this->formatGetal($interneboeking,2),""));


			$this->pdf->Line($posSubtotaal+$extraLengte ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->ln(2);
			$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatum))));
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->ln(-4);
      $this->pdf->row(array("",'',$this->formatGetal($waardeEind,2)));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      
      $this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());

			$this->pdf->ln();
			$this->pdf->ln();
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);

			$this->pdf->row(array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($rendementProcent,2),"%"));
			if($this->pdf->rapport_PERF_jaarRendement)
			  $this->pdf->row(array("",vertaalTekst("Rendement lopende kalenderjaar",$this->pdf->rapport_taal),$this->formatGetal($rendementProcentJaar,2),"%",""));

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


		$this->pdf->SetY($ypos);
	
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Opbrengsten",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

    $this->pdf->SetFont($this->pdf->rapport_font,'u',$this->pdf->rapport_fontsize);
    $this->pdf->row(array("",vertaalTekst("Rente en dividenden",$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$renteDivedendenTotaal=0;
    while (list($key, $value) = each($opbrengstenPerGrootboek))
		{
		  $renteDivedendenTotaal+=$value;
		  if(round($value,2) != 0.00)
			  $this->pdf->row(array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($value,2),""));
		}
    $this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
   // $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
     $this->pdf->row(array("",'','','',$this->formatGetal($renteDivedendenTotaal,2),""));//vertaalTekst('Totaal rente en dividenden',$this->pdf->rapport_taal)
    //$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'u',$this->pdf->rapport_fontsize);
    $this->pdf->row(array("",vertaalTekst("Koers- en valutaresultaten",$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    //$this->pdf->row(array("",vertaalTekst("Ongerealiseerde koers- en valutaresultaten",$this->pdf->rapport_taal),$this->formatGetal($ongerealiseerdeKoersResultaat,2),""));
		$this->pdf->row(array("",vertaalTekst("Ongerealiseerde koersresultaten",$this->pdf->rapport_taal),$this->formatGetal($ongerealiseerdFondsResultaat,2),""));
		$this->pdf->row(array("",vertaalTekst("Ongerealiseerde valutaresultaten",$this->pdf->rapport_taal),$this->formatGetal($ongerealiseerdValutaResultaat,2),""));


		$this->pdf->row(array("",vertaalTekst("Gerealiseerde koers- en valutaresultaten",$this->pdf->rapport_taal),$this->formatGetal($gerealiseerdeKoersResultaat,2),""));
		$this->pdf->row(array("",vertaalTekst("Resultaat op vreemde valuta- rekeningen",$this->pdf->rapport_taal),$this->formatGetal($koersResulaatValutas,2),""));
		$koersTotaal=$ongerealiseerdeKoersResultaat+$gerealiseerdeKoersResultaat+$koersResulaatValutas;
   $this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
   // $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
     $this->pdf->row(array("",'','','',$this->formatGetal($koersTotaal,2),""));//vertaalTekst('Totaal Koers- en valutaresultaten',$this->pdf->rapport_taal)
    //$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);

		//$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3];
    $this->pdf->Line($posTotaal +2+$extraLengte  ,$this->pdf->GetY() ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY());
		$this->pdf->row(array("","Totaal opbrengsten","","",$this->formatGetal($totaalOpbrengst,2)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
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
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row(array("","Totaal kosten","","",$this->formatGetal($totaalKosten,2)));
    
    $this->pdf->Ln();
		
    $this->pdf->Line($posTotaal +2+$extraLengte  ,$this->pdf->GetY() ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY());

	  $this->pdf->row(array("","Netto resultaat over verslagperiode","","",$this->formatGetal($totaalOpbrengst + $totaalKosten,2)));

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
        
 // if($rapportageDatum['a']==$rapportageDatum['b'] && substr($rapportageDatum['a'],5,5)=='01-01')
 //   vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,substr($rapportageDatum['a'],0,4).'-12-31');
    
	    if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01")
	    {
	      $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,$startDatum,true);
        vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,$startDatum);
	    }

//	    $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,$this->pdf->PortefeuilleStartdatum,true);
//      vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,$this->pdf->PortefeuilleStartdatum);

	    $performanceJaar = performanceMeting($this->portefeuille,$startDatum,$this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
		  $performancePeriode = performanceMeting($this->portefeuille,date('Y-m-d',$this->pdf->rapport_datumvanaf),$this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
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
    
    

      $this->pdf->SetXY(10,100);
			$this->pdf->SetFont($this->pdf->pdf->rapport_font,'bu',10);
			$this->pdf->Cell(80,4,vertaalTekst('Verdeling vermogen per', $this->pdf->rapport_taal).' '.date('d-m-Y',db2jul($this->rapportageDatum)).' '.vertaalTekst('naar vermogenscategorie', $this->pdf->rapport_taal),0,1,"L");
			$this->pdf->SetFont($this->pdf->pdf->rapport_font,'',$this->pdf->pdf->rapport_fontsize);
			$this->pdf->SetX(30);
      
          getTypeGrafiekData($this,'Beleggingscategorie');
    $pieChart=true;
    $pieData=array();
    $grafiekData=array();

    $n=0;
    foreach ($this->pdf->grafiekData['Beleggingscategorie']['grafiek'] as $omschrijving=>$waarde)
    {
      if($waarde<0)
        $pieChart=false;
      $pieData[$omschrijving] = $waarde;
  
      $grafiekData[$omschrijving]['percentage']=$waarde;
      $grafiekData[$omschrijving]['kleur']=$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur'][$n];
      $n++;
    }
    //listarray($this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur']);
   // listarray($this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur']);

    if($pieChart==true)
    {
      $this->PieChart(100, 50, $pieData, $this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur']);
    }
    else
    {
      $this->pdf->setXY($this->pdf->marge, 90);
      $this->VBarDiagram2(77, 50, $grafiekData, '',true);
    }
    
		
      
//listarray($this->pdf->grafiekData['Beleggingscategorie']);
      $i=0;
            $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

      $x1 = 20;//$this->pdf->GetX();
      $y1=160;
      $totalen=array();
      foreach ($this->pdf->grafiekData['Beleggingscategorie']['port']['waarde'] as $cat=>$waarde)
      {
        $tableData=$this->pdf->grafiekData['Beleggingscategorie']['port'];
        $kleur=$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur'][$i];
        $this->pdf->SetFillColor($kleur[0],$kleur[1],$kleur[2]);
        $this->pdf->Rect($x1-6, $y1, 2, 2, 'DF');
        $this->pdf->SetXY($x1,$y1);
        $this->pdf->Cell(40,2,$tableData['omschrijving'][$cat]);
        $this->pdf->Cell(20,2,$this->formatGetal($waarde),0,0,'R');
        $this->pdf->Cell(20,2,$this->formatGetal($tableData['procent'][$cat]*100,1)."%",0,0,'R');
        $y1+=4;
        $i++;
        $totalen['waarde']+=$waarde;
        $totalen['procent']+=$tableData['procent'][$cat]*100;
      }
      $this->pdf->Line($x1+40,$y1-1,$x1+40+20,$y1-1);
      $this->pdf->SetXY($x1,$y1+1);
      $this->pdf->Cell(40,2,'Totaal');
      $this->pdf->Cell(20,2,$this->formatGetal($totalen['waarde']),0,0,'R');
      $this->pdf->Cell(20,2,$this->formatGetal($totalen['procent'],1)."%",0,0,'R');
      $this->pdf->Line($x1+40,$y1+4,$x1+40+20,$y1+4);
      $this->pdf->Line($x1+40,$y1+5,$x1+40+20,$y1+5);

	}
  
  
  
  function VBarDiagram2($w, $h, $data,$titel,$procent=true,$legendaLocatie='U')
  {
    
    if($legendaLocatie=='R')
      $legendaWidth = 45;
    elseif($legendaLocatie=='U')
      $legendaWidth = 0;
    else
      $legendaHeight = 30;
    
    $h=$h-$legendaHeight;
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    // listarray($data);
    
    // $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->setXY($XPage,$YPage+2);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Cell($w,4,$titel,0,1,'C');
    // $this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array($this->pdf->koplijn[0],$this->pdf->koplijn[1],$this->pdf->koplijn[2]),'dash'=>0));
    //$this->pdf->line($XPage,$YPage+$this->pdf->rowHeight+3,$XPage+$w,$YPage+$this->pdf->rowHeight+3);
    
    $YPage=$YPage+$h+15;
    
    $maxVal=1;
    $minVal=-1;
    foreach($data as $categorie=>$waarden)
    {
      
      if($waarden['percentage'] > $maxVal)
        $maxVal=ceil($waarden['percentage'] );
      if($waarden['percentage']  < $minVal)
        $minVal=floor($waarden['percentage'] );
    }
    
    if($procent==false)
      $maxVal=ceil($maxVal/pow(10,strlen($maxVal)-1))*pow(10,strlen($maxVal)-1);
    else
      $maxVal=ceil($maxVal/5)*5;
//echo $max;exit;
//echo "$minVal <br>\n";
    $minVal=floor($minVal/.5)*.5;
//
//echo "$minVal <br>\n<br>\n";
    $numBars = 1;//count($legenda);
    $color=array(155,155,155);
    
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
//      $XPage = $this->pdf->GetX();
//      $YPage = $this->pdf->GetY()+$h+15;
    $margin = 0;
    $margeLinks=10;
    $XPage+=$margeLinks;
    $w-=$margeLinks;
    
    $YstartGrafiek = $YPage - floor($margin * 1);
    $hGrafiek = ($h - $margin * 1);
    $XstartGrafiek = $XPage + $margin * 1 ;
    $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda
    
    if($minVal < 0)
    {
      $unit = $hGrafiek / (-1 * $minVal + $maxVal) * -1;
      $nulYpos =  $unit * (-1 * $minVal);
    }
    else
    {
      $unit = $hGrafiek / $maxVal * -1;
      $nulYpos =0;
    }
    
    
    $horDiv = 4;
    $horInterval = $hGrafiek / $horDiv;
    $bereik = $hGrafiek/$unit;
    
    
    
    /*
    $n=0;
    if($legendaLocatie=='U')
    {
      $xcorrectie=$w;
      $ycorrectie=$h+10;
    }
    
    foreach($data as $categorie=>$gegevens)
    {
      $this->pdf->Rect($XstartGrafiek+$bGrafiek+3-$xcorrectie , $YstartGrafiek-$hGrafiek+$n*7+2+$ycorrectie, 2, 2, 'F',null,$gegevens['kleur']);
      $this->pdf->SetXY($XstartGrafiek+$bGrafiek+6-$xcorrectie ,$YstartGrafiek-$hGrafiek+$n*7+1.5+$ycorrectie );
      $this->pdf->MultiCell(40, 4,$categorie,0,'L');
      $n++;
    }
    */
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    
    $stapgrootte = round(abs($bereik)/$horDiv);
    $top = $YstartGrafiek-$h;
    $bodem = $YstartGrafiek;
    $absUnit =abs($unit);
    
    $nulpunt = $YstartGrafiek + $nulYpos;
    $n=0;
    
    if($procent==true)
      $legendaEnd=' %';
    else
      $legendaEnd='';
    
    
    
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
      $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1).$legendaEnd,0,0,'R');
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    for($i=$nulpunt; round($i) >= $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
      {
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte).$legendaEnd,0,0,'R');
      }
      $n++;
      if($n >20)
        break;
    }
    
    
    
    if($numBars > 0)
      $this->pdf->NbVal=$numBars;
    
    $vBar = ($bGrafiek);// / ($this->pdf->NbVal + 1));
    
    $eBaton = ($vBar * .8);
    
    
    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $aantalCategorien=count($data);
    $catCount=0;
    
    foreach($data as $categorie=>$gegevens)
    {
      
      $val=$gegevens['percentage'];
      $lval = $eBaton/$aantalCategorien;
      $xval = $XstartGrafiek + ($catCount * $lval)+ $vBar *.1 ;
      $yval = $YstartGrafiek + $nulYpos ;
      $hval = ($val * $unit);
      
      $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$gegevens['kleur']);
      
      
      $catCount++;
      
    }
    
    
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
 
   function PieChart($w, $h, $data, $colors=null)
  {

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $hLegend = 2;
      $radius = min($w - $margin * 4 - $hLegend , $h - $margin * 2); //
      $radius = floor($radius / 2);
      $XDiag = $XPage + $margin + $radius;
      $YDiag = $YPage + $margin + $radius;
      if($colors == null) {
          for($i = 0;$i < $this->pdf->NbVal; $i++) {
              $gray = $i * intval(255 / $this->pdf->NbVal);
              $colors[$i] = array($gray,$gray,$gray);
          }
      }

      //Sectors
      $sum=array_sum($data);
      $this->pdf->SetLineWidth(0.2);
      $angleStart = 0;
      $angleEnd = 0;
      $i = 0;
      foreach($data as $val) {
          $angle = floor(($val * 360) / doubleval($sum));
          if ($angle != 0) {
              $angleEnd = $angleStart + $angle;
              $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
              $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
              $angleStart += $angle;
          }
          $i++;
      }
      if ($angleEnd != 360) {
          $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
      }

      //Legends




  }
}
?>

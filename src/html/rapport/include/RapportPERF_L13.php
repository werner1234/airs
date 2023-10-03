<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/06/13 15:10:01 $
 		File Versie					: $Revision: 1.24 $

 		$Log: RapportPERF_L13.php,v $
 		Revision 1.24  2020/06/13 15:10:01  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2020/06/10 15:25:29  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2020/06/06 15:48:23  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2018/12/19 17:00:47  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2018/11/16 10:18:07  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2018/11/07 17:08:06  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2018/11/07 10:25:22  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2018/10/03 13:20:24  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2017/12/27 18:29:09  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2017/01/07 16:23:16  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2016/12/17 16:33:26  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2015/09/23 15:05:33  rvv
 		*** empty log message ***

 		Revision 1.11  2015/04/29 15:28:24  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2015/03/29 07:43:24  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2014/05/25 14:38:33  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2012/04/04 12:20:06  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2011/04/13 14:17:39  rvv
 		*** empty log message ***

 		Revision 1.6  2010/12/08 18:29:07  rvv
 		*** empty log message ***

 		Revision 1.5  2010/06/09 18:48:09  rvv
 		*** empty log message ***

 		Revision 1.4  2010/03/10 10:55:21  rvv
 		*** empty log message ***

 		Revision 1.3  2009/01/31 16:42:38  rvv
 		*** empty log message ***

 		Revision 1.2  2009/01/20 17:44:09  rvv
 		*** empty log message ***

 		Revision 1.1  2008/10/01 10:22:35  rvv
 		*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportPERF_L13
{

	function RapportPERF_L13($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
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
    if($this->pdf->rapportToonRente == false)
      $renteFilter=" AND Type <> 'rente' ";
    else
      $renteFilter='';

		$DB = new DB();

		// voor data
		$this->pdf->widthA = array(12,80,30,30,20,3);
		$this->pdf->alignA = array('L','L','L','R','R','R');

		// voor kopjes
		$this->pdf->widthB = array(12,60,10,30,20,3);
    $this->pdf->widthC = array(12,70,10,10,20,3);
		$this->pdf->alignB = array('L','L','R','R','R','R');

		$this->pdf->AddPage("P");
	  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

		// ***************************** ophalen data voor afdruk ************************ //

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT
		          TijdelijkeRapportage.type,
		          SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' $renteFilter"
						 .$__appvar['TijdelijkeRapportageMaakUniek']." GROUP BY TijdelijkeRapportage.type ";

		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->nextRecord())
		{
		  $totaalWaarde[$data['type']] = $data['totaal'];
		  $totaalWaarde['totaal'] += $data['totaal'];
		}



		// haal totaalwaarde op om % te berekenen
		$query = "SELECT TijdelijkeRapportage.type , SUM(actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersBegin." ) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
						 " portefeuille = '".$this->portefeuille."' $renteFilter"
						 .$__appvar['TijdelijkeRapportageMaakUniek']." GROUP BY TijdelijkeRapportage.type ";
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->nextRecord())
		{
		  $totaalWaardeVanaf[$data['type']] = $data['totaal'];
		  $totaalWaardeVanaf['totaal'] += $data['totaal'];
		}



		$waardeEind				  = $totaalWaarde['totaal'];
		$waardeBegin 			 	= $totaalWaardeVanaf['totaal'];
		$waardeMutatie 	   	= $waardeEind - $waardeBegin;
		$stortingen 			 	= getStortingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$onttrekkingen 		 	= getOnttrekkingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
		$rendementProcent  	= performanceMeting($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);


		if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
	    $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

  	$query = "SELECT ".
  	"SUM(((TO_DAYS('".$this->rapportageDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
  	"  / (TO_DAYS('".$this->rapportageDatum."') - TO_DAYS('".$this->rapportageDatumVanaf."')) ".
  	"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
   	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
    "FROM  (Rekeningen, Portefeuilles)
	  Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
    "WHERE ".
	  "Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
	  "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	  "Rekeningmutaties.Verwerkt = '1' AND ".
	  "Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
	  "Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND ".
	  "Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
	  $DB->SQL($query);
	  $DB->Query();
	  $weging = $DB->NextRecord();
  	$gemiddelde = $waardeBegin + $weging['totaal1'];
  	if($gemiddelde <> 0)
  	{
  		$rendementProcent = ((($waardeEind - $waardeBegin) - $weging['totaal2']) / $gemiddelde) * 100;
  	}



$RapJaar = date("Y", db2jul($this->rapportageDatum));
$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));



		$query = "SELECT DISTINCT(Grootboekrekeningen.Grootboekrekening), Grootboekrekeningen.Omschrijving".
		" FROM Grootboekrekeningen ".
		" WHERE Grootboekrekeningen.Opbrengst = '1'  ".
		" ORDER BY Grootboekrekeningen.Afdrukvolgorde";

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
    $totaalOpbrengst=0;
    $totaalKosten=0;
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
			  "Rekeningmutaties.Grootboekrekening = '".$gb['Grootboekrekening']."'  ";

			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();


			while($opbrengst = $DB2->nextRecord())
			{
				$opbrengstenPerGrootboek[$gb['Omschrijving']] =  ($opbrengst['totaalcredit']-$opbrengst['totaaldebet']);
				$totaalOpbrengst += ($opbrengst['totaalcredit'] - $opbrengst['totaaldebet']);
			}
		}


		  $query = "SELECT Grootboekrekeningen.Omschrijving,Grootboekrekeningen.Grootboekrekening, ".
		  "(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
		  "(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet,
		   (Rekeningmutaties.Boekdatum) as laatsteBoekdatum ".
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
		//  "GROUP BY Rekeningmutaties.Grootboekrekening ".
		  "ORDER BY Grootboekrekeningen.Afdrukvolgorde ";

//echo $query;exit;
		$DB = new DB();
		$DB2 = new DB();
		$DB->SQL($query);
		$DB->Query();

		$kostenPerGrootboek = array();
		$factuurHistorieUsedIds=array();
		while($kosten = $DB->nextRecord())
		{
			if($kosten['Grootboekrekening'] == "KNBA")
			{
			  $kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = "Bankkosten en provisie";
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}
			else if($kosten['Grootboekrekening'] == "KOBU")
			{
				//$kostenPerGrootboek['KOST'][Omschrijving] = "Bankkosten en provisie";
				$kostenPerGrootboek['KOST']['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}
			else
			{
				$btw=array('btw'=>0);
        $btwTotaal=0;
				if($kosten['Grootboekrekening']=='BEH')
				{
			
				  if(is_array($this->pdf->portefeuilles) && count($this->pdf->portefeuilles)>0)
				    $portefeuilles=$this->pdf->portefeuilles;
				  else
				    $portefeuilles=array($this->portefeuille);

				  foreach ($portefeuilles as $portefeuille)
          {
            $query = "SELECT id,btw FROM FactuurHistorie WHERE factuurDatum < '" . $kosten['laatsteBoekdatum'] . "' AND portefeuille='" . $portefeuille . "' ORDER BY factuurDatum desc limit 1";
            $DB2->SQL($query);
            $btw = $DB2->lookupRecord();
            if (in_array($btw['id'], $factuurHistorieUsedIds))
            {
              $btw['btw'] = 0;
            }
            else
            {
              $kosten['totaaldebet'] -= $btw['btw'];
              $factuurHistorieUsedIds[] = $btw['id'];
              //	listarray($factuurHistorieUsedIds);listarray($btw);
            }
            $btwTotaal+=$btw['btw'];
          }

				}

				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = $kosten['Omschrijving'];
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
				if($btwTotaal <> 0)
				{
					$kostenPerGrootboek['BTW']['Omschrijving'] = 'BTW';
					$kostenPerGrootboek['BTW']['Bedrag'] += ($btwTotaal);
					$totaalKosten += ($btwTotaal);

				}
			}

			$totaalKosten += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
		}

		$kostenProcent = ($totaalKosten / $waardeEind) * 100;


		// ***************************** einde ophalen data voor afdruk ************************ //
$stippelStart =$this->pdf->marge+$this->pdf->widthA[0]+$this->pdf->widthA[1] ;
$stippelEind = $this->pdf->marge+$this->pdf->widthA[0]+$this->pdf->widthA[1]+$this->pdf->widthA[2];

		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
	//	$this->pdf->setDash(1,1);
if($this->pdf->lastPOST['perc'] == 1)
{
  	$rendement = $this->formatGetal($rendementProcent,2);
  	$percentage = "%";

	//	$this->pdf->row(array(vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal),'',$this->formatGetal($rendementProcent,2),"%"));
  //  $this->pdf->ln(8);
}

		$this->pdf->ln();
if($this->pdf->lastPOST['vvgl'] == 1 || $this->pdf->lastPOST['crmAfdruk'] == true)
{
		$this->pdf->row(array('',date("d/m/Y",db2jul($this->rapportageDatumVanaf)),vertaalTekst('effecten',$this->pdf->rapport_taal),$this->formatGetal($totaalWaardeVanaf['fondsen'],2,true),""));
	  $this->pdf->row(array('','',vertaalTekst('liquiditeiten',$this->pdf->rapport_taal),$this->formatGetal($totaalWaardeVanaf['rente']+$totaalWaardeVanaf['rekening'],2,true),""));
	  //$this->pdf->ln(2);
	  //$this->pdf->Line($stippelStart ,$this->pdf->GetY() ,$stippelEind,$this->pdf->GetY());
		//$this->pdf->ln(2);
    $this->pdf->row(array('','','','--------------------',""));
		$this->pdf->SetAligns($this->pdf->alignB);
	  $this->pdf->row(array('','',$this->pdf->rapportageValuta,$this->formatGetal($totaalWaardeVanaf['totaal'],2,true),""));
	  $this->pdf->SetAligns($this->pdf->alignA);
	  $this->pdf->ln(18);

	   $this->pdf->row(array('',vertaalTekst('Opnamen/Stortingen',$this->pdf->rapport_taal)));
     $this->pdf->row(array('',date("d/m/Y",db2jul($this->rapportageDatumVanaf)).' '.vertaalTekst('t/m',$this->pdf->rapport_taal).' '.date("d/m/Y",db2jul($this->rapportageDatum)),'',$this->formatGetal($stortingen-$onttrekkingen,2,true),""));

	  //$this->pdf->ln(2);
	  //$this->pdf->Line($stippelStart ,$this->pdf->GetY() ,$stippelEind,$this->pdf->GetY());
		//$this->pdf->ln(2);
    $this->pdf->row(array('','','','--------------------',""));
		$this->pdf->SetAligns($this->pdf->alignB);
	  $this->pdf->row(array('','',$this->pdf->rapportageValuta,$this->formatGetal($totaalWaardeVanaf['totaal']+($stortingen-$onttrekkingen),2,true),""));
	  $this->pdf->SetAligns($this->pdf->alignA);
	  $this->pdf->ln(18);
	   //;

		$this->pdf->row(array('',date("d/m/Y",db2jul($this->rapportageDatum)),vertaalTekst('effecten',$this->pdf->rapport_taal),$this->formatGetal($totaalWaarde['fondsen'],2,true),""));
	  $this->pdf->row(array('','',vertaalTekst('liquiditeiten',$this->pdf->rapport_taal),$this->formatGetal($totaalWaarde['rente']+$totaalWaarde['rekening'],2,true),""));
	  //$this->pdf->ln(2);
	  //$this->pdf->Line($stippelStart ,$this->pdf->GetY() ,$stippelEind,$this->pdf->GetY());
		//$this->pdf->ln(2);
    $this->pdf->row(array('','','','--------------------',""));
		$this->pdf->SetAligns($this->pdf->alignB);
	  $this->pdf->row(array('','',$this->pdf->rapportageValuta,$this->formatGetal($totaalWaarde['totaal'],2,true),""));
	  $this->pdf->ln(18);

	    $this->pdf->row(array('',vertaalTekst('Toename/afname',$this->pdf->rapport_taal) ,$this->pdf->rapportageValuta,$this->formatGetal($resultaatVerslagperiode,2,true),$rendement,$percentage));
	    $this->pdf->ln(22);
	    $this->pdf->SetAligns($this->pdf->alignA);
}
else
{
  $this->pdf->SetWidths(array(150));
  $this->pdf->row(array('',vertaalTekst("Rapportageperiode vanaf" ,$this->pdf->rapport_taal)." ".date("d/m/Y",db2jul($this->rapportageDatumVanaf))." t/m ".date("d/m/Y",db2jul($this->rapportageDatum))));
  $this->pdf->ln(8);
  $this->pdf->SetWidths($this->pdf->widthA);
}


  $this->pdf->SetWidths($this->pdf->widthB);

if($this->pdf->lastPOST['opbr'] == 1)
{
	$this->pdf->ln(8);
  $this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
  $this->pdf->row(array('',vertaalTekst("Opbrengsten",$this->pdf->rapport_taal),"",""));
  $this->pdf->ln(2);
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  while (list($key, $value) = each($opbrengstenPerGrootboek))
	{
	  if(round($value,2) != 0.00)
      {
				if($this->pdf->lastPOST['kostPerc'] == 1)
        {
          $this->pdf->row(array('', vertaalTekst($key, $this->pdf->rapport_taal), ':', $this->formatGetal($value, 2), $this->formatGetal($value / $gemiddelde * 100, 2) . '%'));
          $kostenLijn='----------';
        }
        else
        {
          $this->pdf->row(array('', vertaalTekst($key, $this->pdf->rapport_taal), ':', $this->formatGetal($value, 2), ""));
          $kostenLijn='';
        }
      }
	}
	//$this->pdf->ln(2);
  //$this->pdf->Line($this->pdf->marge+$this->pdf->widthB[0]+$this->pdf->widthB[1] ,$this->pdf->GetY() ,$this->pdf->marge+$this->pdf->widthB[0]+$this->pdf->widthB[1]+$this->pdf->widthB[2],$this->pdf->GetY());
  //$this->pdf->ln(2);
  $this->pdf->row(array('','','','--------------------',$kostenLijn));
	if($this->pdf->lastPOST['kostPerc'] == 1)
    $this->pdf->row(array('',vertaalTekst("Totaal" ,$this->pdf->rapport_taal),"",$this->formatGetal($totaalOpbrengst,2),$this->formatGetal($totaalOpbrengst/$gemiddelde*100,2).'%'));
  else
     $this->pdf->row(array('',vertaalTekst("Totaal" ,$this->pdf->rapport_taal),"",$this->formatGetal($totaalOpbrengst,2)));
	$this->pdf->ln(14);
}

if($this->pdf->lastPOST['kost'] == 1)
{
	//$this->pdf->SetWidths(array(100));
	$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
	$this->pdf->row(array('',vertaalTekst("Kosten",$this->pdf->rapport_taal),"",""));
  $this->pdf->ln(2);
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	$this->pdf->SetWidths($this->pdf->widthB);
	while (list($key, $value) = each($kostenPerGrootboek))
	{
	  if(round($kostenPerGrootboek[$key]['Bedrag'],2) != 0.00)
    {
      $omschrijving = $kostenPerGrootboek[$key]['Omschrijving'];
      if ( $omschrijving === 'Transactiekosten' ) {
        $omschrijving = $omschrijving . ' (incl. valutakosten)';
      }
	  	if($this->pdf->lastPOST['kostPerc'] == 1)
      {
        $this->pdf->row(array('', vertaalTekst($omschrijving, $this->pdf->rapport_taal), ':', $this->formatGetal($kostenPerGrootboek[$key]['Bedrag'], 2), $this->formatGetal($kostenPerGrootboek[$key]['Bedrag'] / $gemiddelde * 100, 2) . '%'));
        $kostenLijn='----------';
      }
      else
      {
        $this->pdf->row(array('', vertaalTekst($omschrijving, $this->pdf->rapport_taal), ':', $this->formatGetal($kostenPerGrootboek[$key]['Bedrag'], 2), ""));
        $kostenLijn='';
      }
    }
	}
	//$this->pdf->ln(2);
  //$this->pdf->Line($this->pdf->marge+$this->pdf->widthB[0]+$this->pdf->widthB[1] ,$this->pdf->GetY() ,$this->pdf->marge+$this->pdf->widthB[0]+$this->pdf->widthB[1]+$this->pdf->widthB[2],$this->pdf->GetY());
  //$this->pdf->ln(2);
  $this->pdf->row(array('','','','--------------------',$kostenLijn));
	if($this->pdf->lastPOST['kostPerc'] == 1)
  	$this->pdf->row(array('',vertaalTekst("Totaal directe kosten",$this->pdf->rapport_taal),"",$this->formatGetal($totaalKosten,2),$this->formatGetal($totaalKosten/$gemiddelde*100,2).'%'));
  else
	  $this->pdf->row(array('',vertaalTekst("Totaal directe kosten",$this->pdf->rapport_taal),"",$this->formatGetal($totaalKosten,2)));
  $this->pdf->ln();
  
//  if($this->pdf->lastPOST['kostPerc'] == 1)
//  {
//    $this->pdf->SetWidths($this->pdf->widthC);
//    $this->pdf->row(array('', vertaalTekst("Indicatie verschil bied-laat prijzen", $this->pdf->rapport_taal), '', "", $this->formatGetal(0.04, 2) . '%'));
//    $this->pdf->row(array('','','','',$kostenLijn));
//    $this->pdf->row(array('',vertaalTekst("Totaal directe en indirecte kosten",$this->pdf->rapport_taal),"",'',$this->formatGetal($totaalKosten/$gemiddelde*100+0.04,2).'%'));
//  }
  
}
	}
}
?>
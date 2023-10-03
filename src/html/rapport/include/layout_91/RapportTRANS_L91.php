<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/15 16:39:40 $
File Versie					: $Revision: 1.2 $

$Log: RapportTRANS_L91.php,v $
Revision 1.2  2020/07/15 16:39:40  rvv
*** empty log message ***

Revision 1.1  2020/07/01 16:22:28  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTRANS_L91
{
	function RapportTRANS_L91($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "TRANS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Transactie-overzicht";

		if ($this->pdf->rapportageValuta != 'EUR' && $this->pdf->rapportageValuta != '')
		  $this->pdf->rapport_titel .= " in ".$this->pdf->rapportageValuta;

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{

	  if ($VierDecimalenZonderNullen)
	  {

	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '0000' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];
	       if ($decimaal != '0' && !isset($newDec))
	       {
	       //  echo $this->portefeuille." $waarde <br>";exit;
	         $newDec = $i;
	       }
	     }
	     return number_format($waarde,$newDec,",",".");
	   }
	  else
	   return number_format($waarde,$dec,",",".");
	  }
	  else
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

		$actueel = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2];

		$actueeleind = $actueel + $this->pdf->widthA[3] +$this->pdf->widthA[4]+ $this->pdf->widthA[5]+ $this->pdf->widthA[6]+ $this->pdf->widthA[7];

		if(!empty($totaalA))
		{
			$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthA[3],$this->pdf->GetY());
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

	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		// voor data
		$this->pdf->widthA = array(15,15,63,18,22,22,30,22,30,22,22);
		$this->pdf->alignA = array('L','L','L','R','R','R','R','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthB = $this->pdf->widthA ;//array(15,12,18,42,20,22,22,16,22,22,22,22,22,1);
		$this->pdf->alignB = $this->pdf->alignA ;//array('L','L','R','L','R','R','R','R','R','R','R','R','R','R');


		if($this->pdf->rapport_MUT_kwartaal == 1 && ($this->pdf->selectData['backoffice'] == true) )
		{
			$maand = date("n",db2jul($this->rapportageDatum));
			$kwartaal = floor(($maand / 4)+1);
			switch($kwartaal)
			{
				case 1 :
					$this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-01-01";
					$this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
				break;
				case 2 :
					$this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-03-31";
					$this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
				break;
				case 3 :
					$this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-06-31";
					$this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
				break;
				case 4 :
					$this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-09-30";
					$this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
				break;
			}
		}

		$this->pdf->AddPage();
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

		// loopje over Grootboekrekeningen Opbrengsten = 1
		$query = "SELECT Rekeningmutaties.id, Fondsen.id as fondsId, Fondsen.Omschrijving, ".
		"Fondsen.Fondseenheid, ".
		"Rekeningmutaties.Boekdatum, ".
		"Rekeningmutaties.Transactietype,
		Rekeningmutaties.Valuta,
		 Rekeningmutaties.Afschriftnummer,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, Rekeningmutaties.Fonds,  ".
		"Rekeningmutaties.Fondskoers, ".
		"Rekeningmutaties.Debet as Debet, ".
		"Rekeningmutaties.Credit as Credit, ".
		"Rekeningmutaties.Valutakoers,
		 1 $koersQuery as Rapportagekoers ".
		"FROM Rekeningmutaties, Fondsen, Rekeningen, Portefeuilles, Grootboekrekeningen ".
		"WHERE ".
		"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		"Rekeningmutaties.Fonds = Fondsen.Fonds AND ".
		"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		"Rekeningmutaties.Verwerkt = '1' AND ".
		"Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND ".
		"Rekeningmutaties.Transactietype <> 'B' AND ".
		"Grootboekrekeningen.FondsAanVerkoop = '1' AND ".
		"Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
		"Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
		"ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		// haal koersresultaat op om % te berekenen

		$rapjaar = date('Y',db2jul($this->rapportageDatumVanaf));
		$koersresultaat = gerealiseerdKoersresultaat($this->portefeuille,$this->rapportageDatumVanaf, $this->rapportageDatum,$this->pdf->rapportageValuta);
		$transactietypen = array();

		$buffer = array();
		$cashFondsIds = array(51817, 52094, 53392, 53378, 53490, 55852, 56313, 55853, 55854, 60745);
    
    $cashFondsTotaal=array();
		while($mutaties = $DB->nextRecord())
		{
				$buffer[] = $mutaties;
		}

		$totaal_resultaat_waarde=0;
		$totaal_aankoop_waarde=0;
		$totaal_verkoop_waarde=0;
		$t_verkoop_waarde=0;
		$t_aankoop_waarde=0;
		$t_aankoopCorrectie=0;
		$t_verkoopCorrectie=0;
		
		foreach ($buffer as $mutaties)
		{

			//if($mutaties[Transactietype] != "A/S")
			$mutaties['Aantal'] = abs($mutaties['Aantal']);

			$aankoop_koers = "";
			$aankoop_waardeinValuta = "";
			$aankoop_waarde = "";
			$verkoop_koers = "";
			$verkoop_waardeinValuta = "";
			$verkoop_waarde = "";
			$historisch_kostprijs = "";
			$resultaat_voorgaande = "";
			$resultaat_lopendeProcent = "";
			$resultaatlopende = 0;
			$resultaatvoorgaande =0;


			switch ($mutaties['Transactietype'])
			{
				case "A" :
					// Aankoop
					$t_aankoop_waarde = abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
					$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
					$t_aankoop_koers = $mutaties['Fondskoers'];

					$totaal_aankoop_waarde += $t_aankoop_waarde;

					if ($t_aankoop_waarde > 0)
					{
						$aankoop_koers = $this->formatGetal($t_aankoop_koers, 2);
					}
					if ($t_aankoop_waardeinValuta > 0)
					{
						$aankoop_waardeinValuta = $this->formatGetal($t_aankoop_waardeinValuta, $this->pdf->rapport_TRANS_decimaal);
					}
					if ($t_aankoop_koers > 0)
					{
						$aankoop_waarde = $this->formatGetal($t_aankoop_waarde, $this->pdf->rapport_TRANS_decimaal);
					}
					break;
				case "A/O" :
					// Aankoop / openen
					$t_aankoop_waarde = abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
					$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
					$t_aankoop_koers = $mutaties['Fondskoers'];

					$totaal_aankoop_waarde += $t_aankoop_waarde;

					if ($t_aankoop_waarde > 0)
					{
						$aankoop_koers = $this->formatGetal($t_aankoop_koers, 2);
					}
					if ($t_aankoop_waardeinValuta > 0)
					{
						$aankoop_waardeinValuta = $this->formatGetal($t_aankoop_waardeinValuta, $this->pdf->rapport_TRANS_decimaall);
					}
					if ($t_aankoop_koers > 0)
					{
						$aankoop_waarde = $this->formatGetal($t_aankoop_waarde, $this->pdf->rapport_TRANS_decimaal);
					}
					break;
				case "A/S" :
					// Aankoop / sluiten
					$t_aankoop_waarde = abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
					$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
					$t_aankoop_koers = $mutaties['Fondskoers'];

					$totaal_aankoop_waarde += $t_aankoop_waarde;

					if ($t_aankoop_waarde > 0)
					{
						$aankoop_koers = $this->formatGetal($t_aankoop_koers, 2);
					}
					if ($t_aankoop_waardeinValuta > 0)
					{
						$aankoop_waardeinValuta = $this->formatGetal($t_aankoop_waardeinValuta, $this->pdf->rapport_TRANS_decimaal);
					}
					if ($t_aankoop_koers > 0)
					{
						$aankoop_waarde = $this->formatGetal($t_aankoop_waarde, $this->pdf->rapport_TRANS_decimaal);
					}
					break;
				case "B" :
					// Beginstorting
					break;
				case "D" :
				case "S" :
					// Deponering
					$t_aankoop_waarde = abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
					$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
					$t_aankoop_koers = $mutaties['Fondskoers'];

					$totaal_aankoop_waarde += $t_aankoop_waarde;

					if ($t_aankoop_waarde > 0)
					{
						$aankoop_koers = $this->formatGetal($t_aankoop_koers, 2);
					}
					if ($t_aankoop_waardeinValuta > 0)
					{
						$aankoop_waardeinValuta = $this->formatGetal($t_aankoop_waardeinValuta, $this->pdf->rapport_TRANS_decimaal);
					}
					if ($t_aankoop_waarde > 0)
					{
						$aankoop_waarde = $this->formatGetal($t_aankoop_waarde, $this->pdf->rapport_TRANS_decimaal);
					}
					break;
				case "L" :
					// Lichting
					$t_verkoop_waarde = abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
					$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
					$t_verkoop_koers = $mutaties['Fondskoers'];

					$totaal_verkoop_waarde += $t_verkoop_waarde;

					if ($t_verkoop_koers > 0)
					{
						$verkoop_koers = $this->formatGetal($t_verkoop_koers, 2);
					}
					if ($t_verkoop_waardeinValuta > 0)
					{
						$verkoop_waardeinValuta = $this->formatGetal($t_verkoop_waardeinValuta, $this->pdf->rapport_TRANS_decimaal);
					}
					if ($t_verkoop_waarde > 0)
					{
						$verkoop_waarde = $this->formatGetal($t_verkoop_waarde, $this->pdf->rapport_TRANS_decimaal);
					}
					break;
				case "V" :
					// Verkopen
					$t_verkoop_waarde = abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
					//		echo " $t_verkoop_waarde 				= abs(".$mutaties['Credit'].") * ".$mutaties['Valutakoers']."  * ".$mutaties['Rapportagekoers']."  ";
					$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
					$t_verkoop_koers = $mutaties['Fondskoers'];

					$totaal_verkoop_waarde += $t_verkoop_waarde;

					if ($t_verkoop_koers > 0)
					{
						$verkoop_koers = $this->formatGetal($t_verkoop_koers, 2);
					}
					if ($t_verkoop_waardeinValuta > 0)
					{
						$verkoop_waardeinValuta = $this->formatGetal($t_verkoop_waardeinValuta, $this->pdf->rapport_TRANS_decimaal);
					}
					if ($t_verkoop_waarde > 0)
					{
						$verkoop_waarde = $this->formatGetal($t_verkoop_waarde, $this->pdf->rapport_TRANS_decimaal);
					}
					break;
				case "V/O" :
					// Verkopen / openen
					$t_verkoop_waarde = abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
					$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
					$t_verkoop_koers = $mutaties['Fondskoers'];

					$totaal_verkoop_waarde += $t_verkoop_waarde;

					if ($t_verkoop_koers > 0)
					{
						$verkoop_koers = $this->formatGetal($t_verkoop_koers, 2);
					}
					if ($t_verkoop_waardeinValuta > 0)
					{
						$verkoop_waardeinValuta = $this->formatGetal($t_verkoop_waardeinValuta, $this->pdf->rapport_TRANS_decimaal);
					}
					if ($t_verkoop_waarde > 0)
					{
						$verkoop_waarde = $this->formatGetal($t_verkoop_waarde, $this->pdf->rapport_TRANS_decimaal);
					}
					break;
				case "V/S" :
					// Verkopen / sluiten
					$t_verkoop_waarde = abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
					$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
					$t_verkoop_koers = $mutaties['Fondskoers'];

					$totaal_verkoop_waarde += $t_verkoop_waarde;

					if ($t_verkoop_koers > 0)
					{
						$verkoop_koers = $this->formatGetal($t_verkoop_koers, 2);
					}
					if ($t_verkoop_waardeinValuta > 0)
					{
						$verkoop_waardeinValuta = $this->formatGetal($t_verkoop_waardeinValuta, $this->pdf->rapport_TRANS_decimaal);
					}
					if ($t_verkoop_waarde > 0)
					{
						$verkoop_waarde = $this->formatGetal($t_verkoop_waarde, $this->pdf->rapport_TRANS_decimaal);
					}
					break;
				default :
					$_error = "Fout ongeldig tranactietype!!";
					break;
			}

			/*
				Alleen resultaat berekenen bij "Sluiten", niet bij "Openen".
			*/

			if ($mutaties['Transactietype'] == "L" ||
				$mutaties['Transactietype'] == "V" ||
				$mutaties['Transactietype'] == "V/S" ||
				$mutaties['Transactietype'] == "A/S"
			)
			{

//			if((!empty($verkoop_waarde) || $mutaties['Transactietype'] == "A/S") && $mutaties['Transactietype'] <> "V/O")
//			{

				$historie = berekenHistorischKostprijs($this->portefeuille, $mutaties['Fonds'], $mutaties['Boekdatum'], $this->pdf->rapportageValuta,'',$mutaties['id']);

				//		listarray($historie);
				//echo $mutaties['Fonds'];
//listarray($mutaties);

				if ($mutaties['Transactietype'] == "A/S")
				{
					$historischekostprijs = ($mutaties['Aantal'] * -1) * $historie['historischeWaarde'] * $historie['historischeValutakoers'] * $mutaties['Fondseenheid'];
					$beginditjaar = ($mutaties['Aantal'] * -1) * $historie['beginwaardeLopendeJaar'] * $historie['beginwaardeValutaLopendeJaar'] * $mutaties['Fondseenheid'];
				}
				else
				{
					$historischekostprijs = $mutaties['Aantal'] * $historie['historischeWaarde'] * $historie['historischeValutakoers'] * $mutaties['Fondseenheid'];
					$beginditjaar = $mutaties['Aantal'] * $historie['beginwaardeLopendeJaar'] * $historie['beginwaardeValutaLopendeJaar'] * $mutaties['Fondseenheid'];

					//			  echo "$historischekostprijs = ".$mutaties['Aantal']."        * ".$historie['historischeWaarde']."       * ".$historie['historischeValutakoers']."        * ".$mutaties['Fondseenheid']."<br>";
					//echo "$beginditjaar         = ".$mutaties['Aantal']."        * ".$historie['beginwaardeLopendeJaar']."  * ".$historie['beginwaardeValutaLopendeJaar']."  * ".$mutaties['Fondseenheid']."<br>";

				}
//listarray($mutaties);
				if ($this->pdf->rapportageValuta != 'EUR' && $mutaties['Valuta'] == $this->pdf->rapportageValuta)
				{
					$historischekostprijs = $historischekostprijs / $historie['historischeValutakoers'];
					//   echo "historischekostprijs eur $historischekostprijs = ".$historischekostprijs." / ".$historie['historischeRapportageValutakoers']."<br>";
					$beginditjaar = $beginditjaar / getValutaKoers($this->pdf->rapportageValuta, date("Y", db2jul($this->rapportageDatum) . '-01-01'));
				}
				elseif ($this->pdf->rapportageValuta != 'EUR')
				{
					$historischekostprijs = $historischekostprijs / $historie['historischeRapportageValutakoers'];
					//   echo "historischekostprijs eur $historischekostprijs = ".$historischekostprijs." / ".$historie['historischeRapportageValutakoers']."<br>";
					$beginditjaar = $beginditjaar / getValutaKoers($this->pdf->rapportageValuta, date("Y", db2jul($this->rapportageDatum) . '-01-01'));
					//    echo "beginditjaar eur $beginditjaar  = $beginditjaar         / ".getValutaKoers($this->pdf->rapportageValuta ,date("Y",db2jul($this->rapportageDatum).'-01-01'))."<br>";
				}

				if ($historie['voorgaandejarenActief'] == 0)
				{
					$resultaatvoorgaande = 0;
					$resultaatlopende = $t_verkoop_waarde - $historischekostprijs;
					if ($mutaties['Transactietype'] == "A/S")
					{
						$resultaatvoorgaande = 0;
						$resultaatlopende = $t_aankoop_waarde - $historischekostprijs;
					}
				}
				else
				{
					$resultaatvoorgaande = $beginditjaar - $historischekostprijs;
					$resultaatlopende = $t_verkoop_waarde - $beginditjaar;
//echo "Ttotaal=$t_verkoop_waarde" ;
					if ($mutaties['Transactietype'] == "A/S")
					{
						$resultaatvoorgaande = $beginditjaar - $historischekostprijs;
						$resultaatlopende = ($t_aankoop_waarde * -1) - $beginditjaar;
					}
				}

//	echo "lopende -> ".$resultaatlopende." <-  voorgaande ".$resultaatvoorgaande. " -  <br>" ;

				$result_historischkostprijs = $this->formatGetal($historischekostprijs, $this->pdf->rapport_TRANS_decimaal);
				$result_voorgaandejaren = $this->formatGetal($resultaatvoorgaande, $this->pdf->rapport_TRANS_decimaal2);
				$result_lopendejaar = $this->formatGetal($resultaatlopende, $this->pdf->rapport_TRANS_decimaal2);

				$totaal_resultaat_waarde += $resultaatlopende;

			}
			else
			{
				$result_historischkostprijs = "";
				$result_voorgaandejaren = "";
				$result_lopendejaar = "";
			}

			// print fondsomschrijving appart ivm met apparte fontkleur
			$this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
			$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'], $this->pdf->rapport_fonds_fontcolor['g'], $this->pdf->rapport_fonds_fontcolor['b']);
			$this->pdf->setX($this->pdf->marge);



			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'], $this->pdf->rapport_fontcolor['g'], $this->pdf->rapport_fontcolor['b']);

			// % van totaal
			$percentageTotaalTekst = "";
			if ($this->pdf->rapport_TRANS_procent == 1)
			{
				$percentageTotaal = ABS(($resultaatlopende / ($resultaatvoorgaande + $historischekostprijs)) * 100);

				if ($resultaatlopende < 0)
				{
					$percentageTotaal = (-1 * $percentageTotaal);
				}

				if ($percentageTotaal <> 0)
				{
					if ($percentageTotaal > 1000 || $percentageTotaal < -1000)
					{
						$percentageTotaalTekst = "p.m.";
					}
					else
					{
						$percentageTotaalTekst = $this->formatGetal($percentageTotaal, 1);
					}
				}

			}


			if(in_array($mutaties['fondsId'],$cashFondsIds))
			{

        $transType=substr($mutaties['Transactietype'],0,1) ;
				
        if($transType=='L'||$transType=='D')
        	$groep='LD';
        else
        	$groep='AV';
				
				if (substr($mutaties['Transactietype'],0,1) == "A" || substr($mutaties['Transactietype'],0,1) == "D")
					$cashFondsTotaal[$groep][$mutaties['Omschrijving']]['Aantal']+=$mutaties['Aantal'];
				else
					$cashFondsTotaal[$groep][$mutaties['Omschrijving']]['Aantal']-=$mutaties['Aantal'];

				$cashFondsTotaal[$groep][$mutaties['Omschrijving']]['Debet']+=$mutaties['Debet'];
				$cashFondsTotaal[$groep][$mutaties['Omschrijving']]['Credit']+=$mutaties['Credit'];

				$cashFondsTotaal[$groep][$mutaties['Omschrijving']]['DebetEUR']+=$mutaties['Debet']*$mutaties['Valutakoers'];
				$cashFondsTotaal[$groep][$mutaties['Omschrijving']]['CreditEUR']+=$mutaties['Credit']*$mutaties['Valutakoers'];

				$cashFondsTotaal[$groep][$mutaties['Omschrijving']]['result_voorgaandejaren']+=$resultaatvoorgaande;
				$cashFondsTotaal[$groep][$mutaties['Omschrijving']]['result_lopendejaar']+=$resultaatlopende;

			}
			else
			{

				$this->pdf->Cell($this->pdf->widthB[0] + $this->pdf->widthB[1] , 4, "");
				$this->pdf->Cell($this->pdf->widthB[2], 4, $mutaties['Omschrijving']);//, 27));
				$this->pdf->setX($this->pdf->marge);
				$this->pdf->row(array(date("d-m", db2jul($mutaties['Boekdatum'])),
													$mutaties['Transactietype'],
                          "",
													$this->formatGetal($mutaties['Aantal'], 0),
													$aankoop_koers,
												//	$aankoop_waardeinValuta,
													$aankoop_waarde,
													$verkoop_koers,
											//		$verkoop_waardeinValuta,
													$verkoop_waarde,
													$result_historischkostprijs,
													$result_voorgaandejaren,
													$result_lopendejaar,
													$percentageTotaalTekst));
			}

			$transactietypen[$mutaties['Transactietype']] = $mutaties['Transactietype'];


		}
    if(count($cashFondsTotaal)>0)
		{
			$this->pdf->ln();
		//	$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			$this->pdf->cell(100,4,'Transacties in DeGiro Cash-fondsen');
		//	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();
		}
		//listarray($cashFondsTotaal);
		//echo " $t_aankoopCorrectie $t_verkoopCorrectie <br>\n";
    foreach($cashFondsTotaal as $groep=>$groepData)
		{
		  foreach($groepData as $cashRegelFonds=>$cashData)
		  {
		  	if($groep=='AV')
		{
			if($cashData['Aantal']<0)
          {
				$transactieType='V';
          }
			else
          {
				$transactieType='A';
          }
        }
        else
        {
          if ($cashData['Aantal'] < 0)
          {
            $transactieType = 'L';
          }
          else
          {
            $transactieType = 'D';
          }
        }

			$this->pdf->Cell($this->pdf->widthB[0]+$this->pdf->widthB[1]+$this->pdf->widthB[2], 4, "");
			$this->pdf->Cell($this->pdf->widthB[3], 4, rclip($cashRegelFonds, 27));
			$this->pdf->setX($this->pdf->marge);
      if($cashData['Credit']-$cashData['Debet'] < 0)
			{
				$cashData['DebetTxt']=$this->formatGetal(abs($cashData['Credit']-$cashData['Debet']), $this->pdf->rapport_TRANS_decimaal);
				$cashData['CreditTxt']='';
			}
			else
			{
				$cashData['DebetTxt']='';
				$cashData['CreditTxt']=$this->formatGetal(abs($cashData['Credit']-$cashData['Debet']), $this->pdf->rapport_TRANS_decimaal);
			}


			if($cashData['CreditEUR']-$cashData['DebetEUR'] < 0)
			{
				$cashData['DebetEURTxt']=$this->formatGetal(abs($cashData['CreditEUR']-$cashData['DebetEUR']), $this->pdf->rapport_TRANS_decimaal);
				$cashData['CreditEURTxt']='';
				$totaal_aankoop_waarde-=abs($cashData['CreditEUR']);
				$totaal_verkoop_waarde-=abs($cashData['CreditEUR']);
     	}
			else
			{
				$cashData['DebetEURTxt']='';
				$cashData['CreditEURTxt']=$this->formatGetal(abs($cashData['CreditEUR']-$cashData['DebetEUR']), $this->pdf->rapport_TRANS_decimaal);
				$totaal_aankoop_waarde-=abs($cashData['DebetEUR']);
				$totaal_verkoop_waarde-=abs($cashData['DebetEUR']);
			}

			$this->pdf->row(array('',
												$transactieType,
												$this->formatGetal($cashData['Aantal'], 0),
												'',
												'',
												$cashData['DebetTxt'],
												$cashData['DebetEURTxt'],
												'',
												$cashData['CreditTxt'],
												$cashData['CreditEURTxt'],
												'',
												$this->formatGetal($cashData['result_voorgaandejaren'], $this->pdf->rapport_TRANS_decimaal),
												$this->formatGetal($cashData['result_lopendejaar'], $this->pdf->rapport_TRANS_decimaal),
												''));
		}
    }



		$totaal1 = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3] + $this->pdf->widthA[4]  ;
		$totaal2 = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3] + $this->pdf->widthA[4] + $this->pdf->widthA[5] + $this->pdf->widthA[6]  ;
		$totaal3 = $totaal2 + $this->pdf->widthA[7] + $this->pdf->widthA[8] + $this->pdf->widthA[9] ;

		//$actueeleind = $actueel + $this->pdf->widthA[3] +$this->pdf->widthA[4]+ $this->pdf->widthA[5]+ $this->pdf->widthA[6]+ $this->pdf->widthA[7];

		//$this->pdf->SetTextColor($this->pdf->rapport_subtotaal_omschr_fontcolor['r'],$this->pdf->rapport_subtotaal_omschr_fontcolor['g'],$this->pdf->rapport_subtotaal_omschr_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_kop2_font,$this->pdf->rapport_kop2_fontstyle,$this->pdf->rapport_kop2_fontsize);


		$this->pdf->ln();
    
    $this->pdf->setDrawColor($this->pdf->rapportLineColor[0],$this->pdf->rapportLineColor[1],$this->pdf->rapportLineColor[2]);



		$this->pdf->line($totaal1 + 2, $this->pdf->GetY(), $totaal1 + $this->pdf->widthA[5], $this->pdf->GetY());
		$this->pdf->line($totaal2 + 2, $this->pdf->GetY(), $totaal2 + $this->pdf->widthA[7], $this->pdf->GetY());
		$this->pdf->line($totaal3 + 2, $this->pdf->GetY(), $totaal3 + $this->pdf->widthA[10], $this->pdf->GetY());

		$this->pdf->row(array("",
								"",
								vertaalTekst("Totalen",$this->pdf->rapport_taal),
								"",
                "",
  							$this->formatGetal($totaal_aankoop_waarde,$this->pdf->rapport_TRANS_decimaal),
								"",
								$this->formatGetal($totaal_verkoop_waarde,$this->pdf->rapport_TRANS_decimaal),
                      "",
								"",
								$this->formatGetal($koersresultaat,$this->pdf->rapport_TRANS_decimaal)));
								//$totaal_resultaat_waarde
		$this->pdf->line($totaal1+2,$this->pdf->GetY(),$totaal1 + $this->pdf->widthA[5],$this->pdf->GetY());
		$this->pdf->line($totaal2+2,$this->pdf->GetY(),$totaal2 + $this->pdf->widthA[7],$this->pdf->GetY());
		$this->pdf->line($totaal3+2,$this->pdf->GetY(),$totaal3 + $this->pdf->widthA[10],$this->pdf->GetY());

		$this->pdf->line($totaal1+2,$this->pdf->GetY()+1,$totaal1 + $this->pdf->widthA[5],$this->pdf->GetY()+1);
		$this->pdf->line($totaal2+2,$this->pdf->GetY()+1,$totaal2 + $this->pdf->widthA[7],$this->pdf->GetY()+1);
		$this->pdf->line($totaal3+2,$this->pdf->GetY()+1,$totaal3 + $this->pdf->widthA[10],$this->pdf->GetY()+1);


		if($this->pdf->rapport_TRANS_legenda == 1 && count($transactietypen)>0)
		{
			$this->pdf->ln();
			sort($transactietypen);
			$hoogte = (count($transactietypen) * 4) ;
			if(($this->pdf->GetY() + $hoogte + 8) >= $this->pdf->pagebreak) {
				$this->pdf->AddPage();
				$this->pdf->ln();
			}
   
			$this->pdf->setWidths(array(40,8));
      $this->pdf->row(array('Legenda'));
      $this->pdf->line($this->pdf->marge,$this->pdf->GetY(),$this->pdf->marge+48,$this->pdf->GetY());
			$this->pdf->SetX($this->pdf->marge);

			$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
			$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
			$transactietypeOmschrijving=array('A'=>'Aankoop','A/O'=>'Aankoop / openen','A/S'=>'Aankoop / sluiten','D'=>'Deponering','L'=>'Lichting','V'=>'Verkoop','V/O'=>'Verkoop / openen','V/S'=>'Verkoop / sluiten');
      foreach($transactietypen as $val)
   		{
        $this->pdf->row(array(vertaalTekst($transactietypeOmschrijving[$val],$this->pdf->rapport_taal),vertaalTekst($val,$this->pdf->rapport_taal)));
		
			}
			$this->pdf->ln();
		}

	}
}
?>
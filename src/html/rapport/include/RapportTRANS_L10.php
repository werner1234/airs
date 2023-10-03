<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/06/02 10:03:42 $
File Versie					: $Revision: 1.5 $

$Log: RapportTRANS_L10.php,v $
Revision 1.5  2019/06/02 10:03:42  rvv
*** empty log message ***

Revision 1.4  2019/03/06 16:13:44  rvv
*** empty log message ***

Revision 1.3  2019/01/23 08:49:58  rvv
*** empty log message ***

Revision 1.2  2019/01/19 13:54:10  rvv
*** empty log message ***

Revision 1.1  2018/10/07 08:29:03  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportTransactieoverzichtLayout.php");

class RapportTRANS_L10
{
	function RapportTRANS_L10($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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


	  $this->pdf->excelData[]=array("Datum",'Aan/verkoop','Aantal','Fonds','Aankoopkoers in valuta','Aankoopwaarde in valuta','Aankoopwaarde in EUR',
	  'Verkoopkoers in valuta','Verkoopwaarde in valuta','Verkoopwaarde in valuta','Historsiche kostprijs in EUR','Resultaat voorgaande jaren','Resultaat lopende jaar','%');

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
	       if ($decimaal != '0' && !$newDec)
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

	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		$DB = new DB();
		$db2 = new DB();
    
    // voor data				0  1  2  3     4  5  6  7 8  9 10 11 12 13
    $this->pdf->widthA = array(15,10,15,78,    25,3,25,25,3,25,7,7,25,15);
    $this->pdf->alignA = array('L','L','R','L','R','R','R','R','R','R','R','R','R','R');
    
    // voor kopjes
    $this->pdf->widthB = $this->pdf->widthA;
    $this->pdf->alignB = $this->pdf->alignA;

		if($this->pdf->rapport_MUT_kwartaal == 1 && ($this->pdf->selectData[backoffice] == true) )
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
    $this->pdf->templateVars['TRANSPaginas']=$this->pdf->page;
		$this->pdf->setWidths($this->pdf->widthB);
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

		// loopje over Grootboekrekeningen Opbrengsten = 1
		$query = "SELECT Fondsen.Omschrijving, ".
		"Fondsen.Fondseenheid, ".
		"Rekeningmutaties.Boekdatum, ".
		"Rekeningmutaties.id,
		Rekeningmutaties.Transactietype,
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
		"ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		// haal koersresultaat op om % te berekenen

		$rapjaar = date('Y',db2jul($this->rapportageDatumVanaf));
		$koersresultaat = gerealiseerdKoersresultaat($this->portefeuille,$this->rapportageDatumVanaf, $this->rapportageDatum,$this->pdf->rapportageValuta);
		$transactietypen = array();

		$buffer = array();
		$sortBuffer = array();

		while($mutaties = $DB->nextRecord())
		{
			$buffer[] = $mutaties;
			if($this->pdf->rapport_layout == 14)
			{
				$sortBuffer[$mutaties['Afschriftnummer']][$mutaties['rekeningOmschrijving']]['transacties'][] = ABS($mutaties['Debet'])+ABS($mutaties['Credit']);
				$sortBuffer[$mutaties['Afschriftnummer']][$mutaties['rekeningOmschrijving']]['boekdatum']=$mutaties['Boekdatum'];
			}
		}


    
    $this->pdf->SetFillColor($this->pdf->rapport_background_fill[0],$this->pdf->rapport_background_fill[1],$this->pdf->rapport_background_fill[2]);
    $i=0;
    foreach ($buffer as $mutaties)
		{

			//if($mutaties[Transactietype] != "A/S")
			$mutaties[Aantal] = abs($mutaties[Aantal]);

			$aankoop_koers = "";
			$aankoop_waardeinValuta = "";
			$aankoop_waarde = "";
			$verkoop_koers = "";
			$verkoop_waardeinValuta = "";
			$verkoop_waarde = "";
			$historisch_kostprijs = "";
			$resultaat_voorgaande = "";
			$resultaat_lopendeProcent = "";
			$resultaatlopende = 0 ;

			$t_aankoop_koers=0;
      $t_aankoop_waardeinValuta=0;
			$t_aankoop_waarde=0;
			$t_verkoop_koers=0;
			$t_verkoop_waardeinValuta=0;
			$t_verkoop_waarde=0;



			switch($mutaties['Transactietype'])
			{
					case "A" :
						// Aankoop
						$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties[Debet]);
						$t_aankoop_koers					= $mutaties[Fondskoers];

						$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $this->formatGetal($t_aankoop_koers, 2);
						if($t_aankoop_waardeinValuta > 0)
							$aankoop_waardeinValuta = $this->formatGetal($t_aankoop_waardeinValuta,$this->pdf->rapport_TRANS_decimaal);
						if($t_aankoop_koers > 0)
							$aankoop_waarde 				= $this->formatGetal($t_aankoop_waarde,$this->pdf->rapport_TRANS_decimaal);
					break;
					case "A/O" :
						// Aankoop / openen
						$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties[Debet]);
						$t_aankoop_koers					= $mutaties[Fondskoers];

						$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $this->formatGetal($t_aankoop_koers,2);
						if($t_aankoop_waardeinValuta > 0)
							$aankoop_waardeinValuta = $this->formatGetal($t_aankoop_waardeinValuta,$this->pdf->rapport_TRANS_decimaall);
						if($t_aankoop_koers > 0)
							$aankoop_waarde 				= $this->formatGetal($t_aankoop_waarde,$this->pdf->rapport_TRANS_decimaal);
					break;
					case "A/S" :
						// Aankoop / sluiten
						$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties[Debet]);
						$t_aankoop_koers					= $mutaties[Fondskoers];

						$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $this->formatGetal($t_aankoop_koers,2);
						if($t_aankoop_waardeinValuta > 0)
							$aankoop_waardeinValuta = $this->formatGetal($t_aankoop_waardeinValuta,$this->pdf->rapport_TRANS_decimaal);
						if($t_aankoop_koers > 0)
							$aankoop_waarde 				= $this->formatGetal($t_aankoop_waarde,$this->pdf->rapport_TRANS_decimaal);

					break;
					case "B" :
						// Beginstorting
					break;
					case "D" :
					case "S" :
							// Deponering
						$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties[Debet]);
						$t_aankoop_koers					= $mutaties[Fondskoers];

						$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $this->formatGetal($t_aankoop_koers,2);
						if($t_aankoop_waardeinValuta > 0)
							$aankoop_waardeinValuta = $this->formatGetal($t_aankoop_waardeinValuta,$this->pdf->rapport_TRANS_decimaal);
						if($t_aankoop_waarde > 0)
							$aankoop_waarde 				= $this->formatGetal($t_aankoop_waarde,$this->pdf->rapport_TRANS_decimaal);
					break;
					case "L" :
							// Lichting
						$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties[Credit]);
						$t_verkoop_koers					= $mutaties[Fondskoers];

						$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $this->formatGetal($t_verkoop_koers,2);
						if($t_verkoop_waardeinValuta > 0)
							$verkoop_waardeinValuta = $this->formatGetal($t_verkoop_waardeinValuta,$this->pdf->rapport_TRANS_decimaal);
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $this->formatGetal($t_verkoop_waarde,$this->pdf->rapport_TRANS_decimaal);
					break;
					case "V" :
							// Verkopen
						$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
				//		echo " $t_verkoop_waarde 				= abs(".$mutaties[Credit].") * ".$mutaties[Valutakoers]."  * ".$mutaties['Rapportagekoers']."  ";
						$t_verkoop_waardeinValuta = abs($mutaties[Credit]);
						$t_verkoop_koers					= $mutaties[Fondskoers];

						$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $this->formatGetal($t_verkoop_koers,2);
						if($t_verkoop_waardeinValuta > 0)
							$verkoop_waardeinValuta = $this->formatGetal($t_verkoop_waardeinValuta,$this->pdf->rapport_TRANS_decimaal);
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $this->formatGetal($t_verkoop_waarde,$this->pdf->rapport_TRANS_decimaal);
					break;
					case "V/O" :
							// Verkopen / openen
						$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties[Credit]);
						$t_verkoop_koers					= $mutaties[Fondskoers];

						$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $this->formatGetal($t_verkoop_koers,2);
						if($t_verkoop_waardeinValuta > 0)
							$verkoop_waardeinValuta = $this->formatGetal($t_verkoop_waardeinValuta,$this->pdf->rapport_TRANS_decimaal);
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $this->formatGetal($t_verkoop_waarde,$this->pdf->rapport_TRANS_decimaal);
					break;
					case "V/S" :
					 		// Verkopen / sluiten
						$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties[Credit]);
						$t_verkoop_koers					= $mutaties[Fondskoers];

						$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $this->formatGetal($t_verkoop_koers,2);
						if($t_verkoop_waardeinValuta > 0)
							$verkoop_waardeinValuta = $this->formatGetal($t_verkoop_waardeinValuta,$this->pdf->rapport_TRANS_decimaal);
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $this->formatGetal($t_verkoop_waarde,$this->pdf->rapport_TRANS_decimaal);
					break;
					default :
								$_error = "Fout ongeldig tranactietype!!";
					break;
			}

			/*
				Alleen resultaat berekenen bij "Sluiten", niet bij "Openen".
			*/

			if(	$mutaties['Transactietype'] == "L" ||
					$mutaties['Transactietype'] == "V" ||
					$mutaties['Transactietype'] == "V/S" ||
					$mutaties['Transactietype'] == "A/S")
			{

//			if((!empty($verkoop_waarde) || $mutaties['Transactietype'] == "A/S") && $mutaties['Transactietype'] <> "V/O")
//			{

				$historie = berekenHistorischKostprijs($this->portefeuille, $mutaties[Fonds], $mutaties[Boekdatum],$this->pdf->rapportageValuta,$this->rapportageDatumVanaf,$mutaties['id']);
				//echo $mutaties[Fonds];
//listarray($mutaties);

				if($mutaties['Transactietype'] == "A/S")
				{
					$historischekostprijs  = ($mutaties[Aantal] * -1) * $historie[historischeWaarde]      * $historie[historischeValutakoers]        * $mutaties[Fondseenheid];
					$beginditjaar          = ($mutaties[Aantal] * -1) * $historie[beginwaardeLopendeJaar] * $historie[beginwaardeValutaLopendeJaar]  * $mutaties[Fondseenheid];
				}
				else
				{
					$historischekostprijs = $mutaties[Aantal]        * $historie[historischeWaarde]       * $historie[historischeValutakoers]        * $mutaties[Fondseenheid];
				  $beginditjaar         = $mutaties[Aantal]        * $historie[beginwaardeLopendeJaar]  * $historie[beginwaardeValutaLopendeJaar]  * $mutaties[Fondseenheid];

	//			  echo "$historischekostprijs = ".$mutaties[Aantal]."        * ".$historie[historischeWaarde]."       * ".$historie[historischeValutakoers]."        * ".$mutaties[Fondseenheid]."<br>";
 //echo "$beginditjaar         = ".$mutaties[Aantal]."        * ".$historie[beginwaardeLopendeJaar]."  * ".$historie[beginwaardeValutaLopendeJaar]."  * ".$mutaties[Fondseenheid]."<br>";

				}
//listarray($mutaties);
        if($this->pdf->rapportageValuta != 'EUR' && $mutaties['Valuta'] == $this->pdf->rapportageValuta)
        {
  		    $historischekostprijs = $historischekostprijs / $historie['historischeValutakoers'];
		 //   echo "historischekostprijs eur $historischekostprijs = ".$historischekostprijs." / ".$historie['historischeRapportageValutakoers']."<br>";
		      $beginditjaar         = $beginditjaar         / getValutaKoers($this->pdf->rapportageValuta ,date("Y",db2jul($this->rapportageDatum).'-01-01'));
        }
        elseif ($this->pdf->rapportageValuta != 'EUR')
		    {
		    $historischekostprijs = $historischekostprijs / $historie['historischeRapportageValutakoers'];
		 //   echo "historischekostprijs eur $historischekostprijs = ".$historischekostprijs." / ".$historie['historischeRapportageValutakoers']."<br>";
		    $beginditjaar         = $beginditjaar         / getValutaKoers($this->pdf->rapportageValuta ,date("Y",db2jul($this->rapportageDatum).'-01-01'));
		//    echo "beginditjaar eur $beginditjaar  = $beginditjaar         / ".getValutaKoers($this->pdf->rapportageValuta ,date("Y",db2jul($this->rapportageDatum).'-01-01'))."<br>";
		    }

				if($historie[voorgaandejarenActief] == 0)
				{
					$resultaatvoorgaande = 0;
					$resultaatlopende = $t_verkoop_waarde - $historischekostprijs;
					if($mutaties['Transactietype'] == "A/S")
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
					if($mutaties['Transactietype'] == "A/S")
					{
						$resultaatvoorgaande = $beginditjaar - $historischekostprijs;
						$resultaatlopende = ($t_aankoop_waarde * -1) - $beginditjaar;
					}
				}

//	echo "lopende -> ".$resultaatlopende." <-  voorgaande ".$resultaatvoorgaande. " -  <br>" ;

				$result_historischkostprijs = $this->formatGetal($historischekostprijs,$this->pdf->rapport_TRANS_decimaal);
				$result_voorgaandejaren = $this->formatGetal($resultaatvoorgaande,$this->pdf->rapport_TRANS_decimaal2);
				$result_lopendejaar = $this->formatGetal($resultaatlopende,$this->pdf->rapport_TRANS_decimaal2);

				$totaal_resultaat_waarde += $resultaatlopende;

			}
			else
			{
				$result_historischkostprijs = "";
				$result_voorgaandejaren = "";
				$result_lopendejaar = "";
				$historischekostprijs=0;
		  	$resultaatvoorgaande=0;
		  	$percentageTotaal=0;
			}

			// print fondsomschrijving appart ivm met apparte fontkleur
			$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);


			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);

			// % van totaal
			if($this->pdf->rapport_TRANS_procent == 1)
			{
				$percentageTotaal = ABS(($resultaatlopende / ($resultaatvoorgaande + $historischekostprijs)) *100);

				if($resultaatlopende < 0)
					$percentageTotaal = (-1*$percentageTotaal);

				if($percentageTotaal <>0)
				{
					if($percentageTotaal > 1000 || $percentageTotaal < -1000)
						$percentageTotaalTekst = "p.m.";
					else
						$percentageTotaalTekst = $this->formatGetal($percentageTotaal,1);

				}
				else
					$percentageTotaalTekst = "";
			}

			if($this->pdf->rapport_layout == 10)
			{
        
        if($i%2==0)
        {
          $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
        }
        else
        {
          $this->pdf->fillCell=array();
        }
        $i++;
				$this->pdf->row(array(date("d-m",db2jul($mutaties['Boekdatum'])),
											$mutaties['Transactietype'],
											$this->formatGetal($mutaties['Aantal'],0),
                          $mutaties[Omschrijving],
											$aankoop_koers,
											"",
											$aankoop_waarde,
											$verkoop_koers,
											"",
											$verkoop_waarde,
											"",
											"",
											$result_lopendejaar,
											$percentageTotaalTekst));
			}
			

			$transactietypen[] = $mutaties[Transactietype];
		}


		$totaal1 = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3] + $this->pdf->widthA[4] + $this->pdf->widthA[5] ;
		$totaal2 = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3] + $this->pdf->widthA[4] + $this->pdf->widthA[5] + $this->pdf->widthA[6] + $this->pdf->widthA[7] + $this->pdf->widthA[8] ;
		$totaal3 = $totaal2 + $this->pdf->widthA[9] + $this->pdf->widthA[10] + $this->pdf->widthA[11];

		//$actueeleind = $actueel + $this->pdf->widthA[3] +$this->pdf->widthA[4]+ $this->pdf->widthA[5]+ $this->pdf->widthA[6]+ $this->pdf->widthA[7];

		$this->pdf->SetTextColor($this->pdf->rapport_subtotaal_omschr_fontcolor[r],$this->pdf->rapport_subtotaal_omschr_fontcolor[g],$this->pdf->rapport_subtotaal_omschr_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_kop2_font,$this->pdf->rapport_kop2_fontstyle,$this->pdf->rapport_kop2_fontsize);


		$this->pdf->ln();



		//$koersresultaat = gerealiseerdKoersresultaat($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum);
		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}
    $this->pdf->fillCell=array();
	
		$this->pdf->line($totaal1 + 2, $this->pdf->GetY(), $totaal1 + $this->pdf->widthA[6],  $this->pdf->GetY());
		$this->pdf->line($totaal2 + 2, $this->pdf->GetY(), $totaal2 + $this->pdf->widthA[9],  $this->pdf->GetY());
		$this->pdf->line($totaal3 + 2, $this->pdf->GetY(), $totaal3 + $this->pdf->widthA[12], $this->pdf->GetY());

		$this->pdf->row(array("",
								"",
								"",
								"",
								vertaalTekst("Totalen",$this->pdf->rapport_taal),
								"",
								$this->formatGetal($totaal_aankoop_waarde,$this->pdf->rapport_TRANS_decimaal),
								"",
								"",
								$this->formatGetal($totaal_verkoop_waarde,$this->pdf->rapport_TRANS_decimaal),
								"",
								"",
								$this->formatGetal($koersresultaat,$this->pdf->rapport_TRANS_decimaal)));
								//$totaal_resultaat_waarde
		$this->pdf->line($totaal1+2,$this->pdf->GetY(),$totaal1 + $this->pdf->widthA[6],$this->pdf->GetY());
		$this->pdf->line($totaal2+2,$this->pdf->GetY(),$totaal2 + $this->pdf->widthA[9],$this->pdf->GetY());
		$this->pdf->line($totaal3+2,$this->pdf->GetY(),$totaal3 + $this->pdf->widthA[12],$this->pdf->GetY());

		$this->pdf->line($totaal1+2,$this->pdf->GetY()+1,$totaal1 + $this->pdf->widthA[6],$this->pdf->GetY()+1);
		$this->pdf->line($totaal2+2,$this->pdf->GetY()+1,$totaal2 + $this->pdf->widthA[9],$this->pdf->GetY()+1);
		$this->pdf->line($totaal3+2,$this->pdf->GetY()+1,$totaal3 + $this->pdf->widthA[12],$this->pdf->GetY()+1);
	



		if($this->pdf->rapport_TRANS_legenda == 1)
		{
			$this->pdf->ln();

			$transactietypen = array_unique($transactietypen);
			sort($transactietypen);

			$hoogte = (count($transactietypen) * 4) ;
			if(($this->pdf->GetY() + $hoogte + 8) >= $this->pdf->pagebreak) {
				$this->pdf->AddPage();
				$this->pdf->ln();
			}

			$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor[r],$this->pdf->rapport_kop_bgcolor[g],$this->pdf->rapport_kop_bgcolor[b]);
			//$this->pdf->SetX($this->pdf->marge + $this->pdf->widthB[0]);
			$this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),110,$hoogte,'F');
			$this->pdf->SetFillColor(0);
			$this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),110,$hoogte);
			//$this->pdf->SetX($this->pdf->marge);
			$this->pdf->SetX($this->pdf->marge);

			// kopfontcolor

			$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[r],$this->pdf->rapport_kop_fontcolor[g],$this->pdf->rapport_kop_fontcolor[b]);
			$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

			reset($transactietypen);

   		while (list($key, $val) = each($transactietypen))
   		{
				switch($val)
				{
					case "A" :
						$this->pdf->Cell(30,4, "A", 0,0, "L");
						$this->pdf->Cell(80,4, vertaalTekst("Aankoop",$this->pdf->rapport_taal), 0,1, "L");
					break;
					case "A/O" :
						$this->pdf->Cell(30,4, "A/O", 0,0, "L");
						$this->pdf->Cell(80,4, vertaalTekst("Aankoop / openen",$this->pdf->rapport_taal), 0,1, "L");
					break;
					case "A/S" :
						$this->pdf->Cell(30,4, "A/S", 0,0, "L");
						$this->pdf->Cell(80,4, vertaalTekst("Aankoop / sluiten",$this->pdf->rapport_taal), 0,1, "L");
					break;
					case "D" :
						$this->pdf->Cell(30,4, "D", 0,0, "L");
						$this->pdf->Cell(80,4, vertaalTekst("Deponering",$this->pdf->rapport_taal), 0,1, "L");
					break;
					case "L" :
						$this->pdf->Cell(30,4, "L", 0,0, "L");
						$this->pdf->Cell(80,4, vertaalTekst("Lichting",$this->pdf->rapport_taal), 0,1, "L");
					break;
					case "V" :
						$this->pdf->Cell(30,4, "V", 0,0, "L");
						$this->pdf->Cell(80,4, vertaalTekst("Verkoop",$this->pdf->rapport_taal), 0,1, "L");
					break;
					case "V/O" :
						$this->pdf->Cell(30,4, "V/O", 0,0, "L");
						$this->pdf->Cell(80,4, vertaalTekst("Verkoop / openen",$this->pdf->rapport_taal), 0,1, "L");
					break;
					case "V/S" :
						$this->pdf->Cell(30,4, "V/S", 0,0, "L");
						$this->pdf->Cell(80,4, vertaalTekst("Verkoop / sluiten",$this->pdf->rapport_taal), 0,1, "L");
					break;
				}
			}
		}
    
    $this->pdf->ln();
      $this->pdf->SetTextColor(0);
		  $this->pdf->SetFont($this->pdf->rapport_font,'BU',$this->pdf->rapport_fontsize);
      $this->pdf->MultiCell(280,4, 'Geschiktheidsverklaring:', 0, "L");
      $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
		  $this->pdf->MultiCell(280,4, 'In het kader van wet- en regelgeving (MIFID 2) worden effectentransacties getoetst of deze geschikt zijn voor uw beheerportefeuille. De bovenstaande transacties hebben wij zodoende geschikt geacht in het kader van het bij ons bekende beleggersprofiel, alsmede de risicobereidheid en doelstellingen met betrekking tot deze portefeuille. Daarnaast evalueren wij of de wijze waarop het vermogensbeheer is uitgevoerd tijdens de opgesomde periode geschikt en passend is geweest. Dit achten wij op basis van het bij ons bekende beleggersprofiel tevens het geval en wij zijn voornemens om voor de komende periode deze wijze van beheer uitgaande van het beleggersprofiel te handhaven. Het beleggersprofiel treft u aan op de eerste pagina van dit overzicht.', 0, "L");
		  $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);



	}
}
?>
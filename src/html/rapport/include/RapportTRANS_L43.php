<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/08/07 15:30:49 $
File Versie					: $Revision: 1.4 $

$Log: RapportTRANS_L43.php,v $
Revision 1.4  2019/08/07 15:30:49  rvv
*** empty log message ***

Revision 1.3  2015/12/19 08:29:17  rvv
*** empty log message ***

Revision 1.2  2014/11/26 16:48:55  rvv
*** empty log message ***

Revision 1.1  2013/02/03 09:04:21  rvv
*** empty log message ***

Revision 1.44  2013/01/16 16:59:39  rvv
*** empty log message ***

Revision 1.43  2012/12/22 15:33:14  rvv
*** empty log message ***

Revision 1.42  2011/09/14 09:26:56  rvv
*** empty log message ***

Revision 1.41  2011/01/12 08:45:50  rvv
*** empty log message ***

Revision 1.40  2010/10/17 09:22:47  rvv
Disclamer tekst toegevoegd

Revision 1.39  2010/03/31 16:27:02  rvv
*** empty log message ***

Revision 1.38  2010/01/17 11:00:49  rvv
*** empty log message ***

Revision 1.37  2009/03/25 12:45:14  rvv
*** empty log message ***

Revision 1.36  2009/03/14 13:24:27  rvv
*** empty log message ***

Revision 1.35  2009/02/12 11:29:23  cvs
decimale aanpassen layout 14 bij aantallen

Revision 1.34  2009/02/11 15:20:51  cvs
layout 14 AFS
bij van totaal, provise aftrekken

Revision 1.33  2009/01/20 17:44:09  rvv
*** empty log message ***

Revision 1.32  2008/08/12 10:14:33  rvv
AFS aanpassing

Revision 1.31  2008/05/16 08:12:57  rvv
*** empty log message ***

Revision 1.30  2008/02/14 07:37:25  rvv
*** empty log message ***

Revision 1.29  2007/11/02 12:54:00  rvv
L14 aanpassing

Revision 1.28  2007/10/04 11:57:04  rvv
*** empty log message ***

Revision 1.27  2007/07/10 15:54:49  rvv
AFS update

Revision 1.26  2007/06/29 11:38:56  rvv
L14 aanpassingen

Revision 1.25  2007/03/27 14:58:20  rvv
VreemdeValutaRapportage

Revision 1.24  2006/10/02 13:37:45  rvv
include nu .php

Revision 1.23  2006/10/02 12:46:13  rvv
Aanpassing kolommen layout 10

Revision 1.22  2006/05/09 07:48:11  jwellner
- afronding fondsaantal
- afronding controle bij afdrukken rapporten
- sorteren frontoffice selectie

Revision 1.21  2006/04/12 07:54:47  jwellner
*** empty log message ***

Revision 1.20  2006/02/07 11:06:28  jwellner
- bugfix valuta in mutatievoorstel fondsen
- bugfix in MUT / TRANS layout 8

Revision 1.19  2006/01/23 14:13:43  jwellner
no message

Revision 1.18  2006/01/02 11:28:55  cvs
kolombreedtes aanpassen

Revision 1.17  2005/11/30 08:37:39  jwellner
layout stuff

Revision 1.16  2005/11/18 15:15:01  jwellner
no message

Revision 1.15  2005/11/17 07:25:02  jwellner
no message

Revision 1.14  2005/11/07 10:29:17  jwellner
no message

Revision 1.13  2005/10/19 11:18:23  jwellner
focus op 1e veld in formulier bij editForm

Revision 1.12  2005/10/05 11:50:08  jwellner
transactieoverzicht

Revision 1.11  2005/10/05 09:02:18  jwellner
placeholder

Revision 1.10  2005/10/05 08:14:40  jwellner
transactie overzicht

Revision 1.9  2005/10/04 12:34:41  jwellner
no message

Revision 1.8  2005/09/30 14:05:13  jwellner
- rapport OIH
- rapport MUT2
- Layout 5
- selectieschermen

Revision 1.7  2005/09/29 15:00:18  jwellner
no message

Revision 1.6  2005/09/16 07:32:55  jwellner
aanpassingen rapportage.

Revision 1.5  2005/09/13 14:49:18  jwellner
rapportage toevoegingen

Revision 1.4  2005/09/12 12:04:16  jwellner
bugs en features

Revision 1.3  2005/08/24 10:42:55  jwellner
no message

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
//include_once($__appvar["basedir"]."/html/rapport/RapportTransactieoverzichtLayout.php");

class RapportTRANS_L43
{
	function RapportTRANS_L43($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
	  $transactietypen�mschrijving= array('A'=>'Aankoop',
	                                      'A/O'=>'Aankoop / openen',
	                                      'A'=>'Aankoop',
	                                      'A/S'=>'Aankoop / sluiten',
	                                      'D'=>'Deponering',
	                                      'L'=>'Lichting',
	                                      'V'=>'Verkoop',
	                                      'V/O'=>'Verkoop / openen',
	                                      'V/S'=>'Verkoop / sluiten',);


	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		$DB = new DB();
		$db2 = new DB();


		// voor data				0  1  2  3     4  5  6  7 8  9 10 11 12 13
		$this->pdf->widthA = array(15,30,75,18,    25,25,1,25,25,25,7,7,1,1);
		$this->pdf->alignA = array('L','L','L','R','R','R','R','R','R','R','R','R','R','R');

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
		$this->pdf->setWidths($this->pdf->widthB);
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

		// loopje over Grootboekrekeningen Opbrengsten = 1
		$query = "SELECT Rekeningmutaties.id, Fondsen.Omschrijving, ".
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
		$sortBuffer = array();

		while($mutaties = $DB->nextRecord())
		{
			$buffer[] = $mutaties;
			if($this->pdf->rapport_layout == 43)
			{
				$sortBuffer[$mutaties['Afschriftnummer']][$mutaties['Boekdatum']][$mutaties['rekeningOmschrijving']]['transacties'][] = ABS($mutaties['Debet'])+ABS($mutaties['Credit']);
				$sortBuffer[$mutaties['Afschriftnummer']][$mutaties['Boekdatum']][$mutaties['rekeningOmschrijving']]['boekdatum']=$mutaties['Boekdatum'];
			}
		}

	if($this->pdf->rapport_layout == 43)
	{
		if(count($sortBuffer) > 0)
		{
      foreach ($sortBuffer as $afschrift=>$boekdatumArray)
      {
        foreach($boekdatumArray as $boekdatum=>$fondsomschrijvingArray)
        {
          foreach ($fondsomschrijvingArray as $rekeningOmschrijving=>$transactie)
          {
            $rekeningOmschrijving = addslashes($rekeningOmschrijving);
            $query = "SELECT SUM(Debet * Valutakoers) as Debet
			                FROM
			                Rekeningmutaties,Rekeningen
			                WHERE
			                Afschriftnummer =  '".$afschrift."'  AND
			                Omschrijving = '".$rekeningOmschrijving."' AND
                      Rekeningmutaties.Rekening = Rekeningen.Rekening AND
                      Grootboekrekening IN('KOST','KOBU') AND
                      Rekeningmutaties.Boekdatum = '".$boekdatum."' AND
                      Rekeningen.Portefeuille = '".$this->portefeuille."' ";
            $db2->SQL($query);
			      $db2->Query();
			      while($kosten = $db2->nextRecord())
			      {
		  	      $sortBuffer[$afschrift][$boekdatum][$rekeningOmschrijving]['kosten'][]   = $kosten['Debet'];
	  		    }
          }
        }
      }
		}

		if(count($sortBuffer) > 0)
		{
		  foreach ($buffer as $i=>$waarden)
		  {
		    if (count($sortBuffer[$waarden['Afschriftnummer']][$waarden['Boekdatum']][$waarden['rekeningOmschrijving']]['transacties']) >1)
		    {
		      foreach ($sortBuffer[$waarden['Afschriftnummer']][$waarden['Boekdatum']][$waarden['rekeningOmschrijving']]['transacties'] as $key=>$value)
		      {
		        if (($waarden ['Debet'] + $waarden['Credit']) == $value)
		        {
		          $buffer[$i]['kosten']=$sortBuffer[$waarden['Afschriftnummer']][$waarden['Boekdatum']][$waarden['rekeningOmschrijving']]['kosten'][$key];
		        }
		      }
		    }
		    else
		    {
		       $buffer[$i]['kosten']=$sortBuffer[$waarden['Afschriftnummer']][$waarden['Boekdatum']][$waarden['rekeningOmschrijving']]['kosten'][0];
		    }
		  }
		}
	}

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

		//		listarray($historie);
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
			$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
			$this->pdf->setX($this->pdf->marge);



$this->pdf->Cell($this->pdf->widthB[0],4,"");
$this->pdf->Cell($this->pdf->widthB[1],4,"");
$this->pdf->Cell($this->pdf->widthA[3],4,$mutaties[Omschrijving]); 	

			$this->pdf->setX($this->pdf->marge);

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


 		    $query = "SELECT fondsEenheid FROM Fondsen WHERE Fonds='".$mutaties['Fonds']."'";
			  $db2->SQL($query);
			  $fondsEenheid=$db2->lookupRecord();

			  $koers = ($t_aankoop_waarde +  $t_verkoop_waarde) / $mutaties['Aantal'] / $fondsEenheid['fondsEenheid']; // Nog delen door Fondseenheid.

        if ($mutaties[Valutakoers] <> 1)
        {
        $koersValuta = ( $t_aankoop_waardeinValuta + $t_verkoop_waardeinValuta) / $mutaties['Aantal'];

        if (round($koersValuta,2) != 0)
	        $koersValuta = $this->formatGetal($koersValuta,2);
        else
          $koersValuta = '';
        }
        else
        {
          $koersValuta = '';
        }


			  if (round($koers,2 >0))
			    $koers = $this->formatGetal($koers,2);
			  else
			    $koers = '';

			    if (round($mutaties['kosten'],2) >0)
			    {
			       $provisieTxt = $this->formatGetal($mutaties['kosten'],2);
			       $provisie += $mutaties['kosten'];
			    }
			    else
			      $provisieTxt = '';

			    $datum = date("d-m",db2jul($mutaties['Boekdatum']));

			    if ($datum == $oldDatum)
			      $datum = '';

			      $totaalTransactie =0;
			      if ($t_aankoop_waarde > 0)
			      {
			        $totaalTransactie = ($t_aankoop_waarde + $kosten['Debet']) * -1;
			      }
			      if ($t_verkoop_waarde > 0)
			      {
			        $totaalTransactie = $t_verkoop_waarde - $kosten['Debet'];
			      }
			    if (round($totaalTransactie,2) != 0)
			    {
			       $totaalTransactieTxt = $this->formatGetal($totaalTransactie-$mutaties['kosten'],2);
			    }
			    else
			    $totaalTransactieTxt = '';

$t_aankoop_waarde =0;
$t_verkoop_waarde =0;
$t_aankoop_waardeinValuta =0;
$t_verkoop_waardeinValuta =0;


				$this->pdf->row(array($datum,
											$transactietypen�mschrijving[$mutaties['Transactietype']],
											'',
											$this->formatAantal($mutaties['Aantal'],4,true),
											$koersValuta,
											$koers,
											'',
											$provisieTxt,
											$totaalTransactieTxt,
											"",
											'',
											"",
											"",
											'',
											''));
			$oldDatum = date("d-m",db2jul($mutaties['Boekdatum']));


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

		if($this->pdf->rapport_layout == 43)
		{//echo "$totaal_verkoop_waarde - $totaal_aankoop_waarde - $provisie";
		$this->pdf->row(array("",
								"",
								"",
								"",
								'',
								vertaalTekst("Totaal",$this->pdf->rapport_taal),
								'',
								$this->formatGetal($provisie,$this->pdf->rapport_TRANS_decimaal),
								$this->formatGetal($totaal_verkoop_waarde-$totaal_aankoop_waarde-$provisie,$this->pdf->rapport_TRANS_decimaal),
								"",
								'',
								"",
								"",
								''));

		$this->pdf->line($totaal1+10, $this->pdf->GetY(), $totaal1+ $this->pdf->widthA[6]+ $this->pdf->widthA[7],  $this->pdf->GetY());
		$this->pdf->line($totaal1+10, $this->pdf->GetY()+1, $totaal1+ $this->pdf->widthA[6]+ $this->pdf->widthA[7],  $this->pdf->GetY()+1);
		$this->pdf->line($totaal1+10, $this->pdf->GetY()-4, $totaal1+ $this->pdf->widthA[6]+ $this->pdf->widthA[7],  $this->pdf->GetY()-4);

		$this->pdf->line($totaal2-16, $this->pdf->GetY(), $totaal2,  $this->pdf->GetY());
		$this->pdf->line($totaal2-16, $this->pdf->GetY()+1, $totaal2,  $this->pdf->GetY()+1);
		$this->pdf->line($totaal2-16, $this->pdf->GetY()-4, $totaal2,  $this->pdf->GetY()-4);


		}
		else
		{
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
		}




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

		if(isset($this->pdf->rapport_TRANS_disclaimerText))
		{
		  $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize-2);
		  $this->pdf->MultiCell(280,4, $this->pdf->rapport_TRANS_disclaimerText, 0, "L");
		  $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
		}


//	if($this->pdf->rapport_layout == 16)
//	{
//	  $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
//    if($this->pdf->GetY()< $this->pdf->pagebreak-4)
//      $this->pdf->SetY($this->pdf->pagebreak);
//	  $this->pdf->Cell(80,4, vertaalTekst("Hoewel deze informatie met de meeste zorg is samengesteld, aanvaardt Keijser Capital N.V. geen aansprakelijkheid voor de volledigheid en/of juistheid van bovenstaande opgave.",$this->pdf->rapport_taal), 0,1, "L");
//	}



	}
}
?>
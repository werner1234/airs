<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/08/07 15:30:49 $
File Versie					: $Revision: 1.23 $

$Log: RapportTRANS_L63.php,v $
Revision 1.23  2019/08/07 15:30:49  rvv
*** empty log message ***

Revision 1.22  2018/03/25 10:16:55  rvv
*** empty log message ***

Revision 1.21  2018/02/08 07:40:41  rvv
*** empty log message ***

Revision 1.20  2018/02/07 17:22:29  rvv
*** empty log message ***

Revision 1.19  2017/11/15 17:03:35  rvv
*** empty log message ***

Revision 1.18  2016/12/14 15:11:00  rvv
*** empty log message ***

Revision 1.17  2016/11/30 16:48:42  rvv
*** empty log message ***

Revision 1.16  2016/11/27 11:09:29  rvv
*** empty log message ***

Revision 1.15  2016/10/05 16:19:00  rvv
*** empty log message ***

Revision 1.14  2016/09/04 14:42:06  rvv
*** empty log message ***

Revision 1.13  2016/05/04 16:01:30  rvv
*** empty log message ***

Revision 1.12  2016/04/30 15:33:27  rvv
*** empty log message ***

Revision 1.11  2016/04/07 05:49:24  rvv
*** empty log message ***

Revision 1.10  2016/04/06 15:30:51  rvv
*** empty log message ***

Revision 1.9  2016/03/19 16:51:09  rvv
*** empty log message ***

Revision 1.8  2016/02/13 14:02:39  rvv
*** empty log message ***

Revision 1.7  2016/01/27 17:08:53  rvv
*** empty log message ***

Revision 1.6  2016/01/09 18:58:30  rvv
*** empty log message ***

Revision 1.5  2016/01/06 16:29:17  rvv
*** empty log message ***

Revision 1.4  2015/12/02 16:16:29  rvv
*** empty log message ***

Revision 1.3  2015/12/02 08:26:21  rvv
*** empty log message ***

Revision 1.2  2015/11/29 13:13:22  rvv
*** empty log message ***

Revision 1.1  2015/09/20 17:32:28  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTRANS_L63
{
	function RapportTRANS_L63($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "TRANS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Transacties";
    $this->rapport_xls_titel = "Transacties";

		if ($this->pdf->rapportageValuta != 'EUR' && $this->pdf->rapportageValuta != '')
		  $this->pdf->rapport_titel .= " in ".$this->pdf->rapportageValuta;

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

    $this->pdf->excelData[]=array('Portefeuille','PAS-code','Soort','ISIN','Bedrag','Valuta','Datum');
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
	  $transactietypenOmschrijving= array('A'=>'Aankoop',
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

	
		$this->pdf->widthB = array(20, 18, 66, 15, 20, 16, 22, 20, 20, 25, 25, 15);
		$this->pdf->alignB = array('L','R','L','L','R','R','R','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = $this->pdf->widthB;
		$this->pdf->alignA = $this->pdf->alignB;

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
		$query = "SELECT Rekeningmutaties.id, Fondsen.Omschrijving, Fondsen.FondsImportCode,Rekeningen.Rekening,Rekeningen.Valuta as rekeningValuta, Rekeningen.Termijnrekening, ".
		"Fondsen.Fondseenheid,Fondsen.fondssoort, ".
		"Rekeningmutaties.Boekdatum, ".
		"Rekeningmutaties.Transactietype,
		Rekeningmutaties.Valuta,
		 Rekeningmutaties.Afschriftnummer,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, Rekeningmutaties.Fonds,  ".
		"Rekeningmutaties.Fondskoers, ".
		"Rekeningmutaties.Debet as Debet, ".
		"Rekeningmutaties.Credit as Credit, ".
    "ABS((Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers)-(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers)) as Bedrag, ".
    "((Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers)-(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers)) as BedragEur, ".
		"Rekeningmutaties.Valutakoers,
     grootboeknummers.rekeningnummer as pasCode, 
     Rekeningmutaties.Grootboekrekening,
		 1 $koersQuery as Rapportagekoers ,
     Grootboekrekeningen.FondsAanVerkoop ".
		"FROM Rekeningmutaties
     LEFT JOIN Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds 
     JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening 
     JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille  
     JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening 
     LEFT JOIN grootboeknummers ON Rekeningmutaties.Grootboekrekening=grootboeknummers.Grootboekrekening AND grootboeknummers.vermogensbeheerder=Portefeuilles.Vermogensbeheerder
     ".
		"WHERE ".
		"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		"Rekeningmutaties.Verwerkt = '1' AND Rekeningen.Memoriaal = '0' AND ".
		"Rekeningmutaties.Transactietype <> 'B' AND ".

		"Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
		"Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
		"ORDER BY Rekeningmutaties.Transactietype,Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
    //		"Grootboekrekeningen.FondsAanVerkoop = '1' AND ".
//    BeleggingssectorPerFonds.Beleggingssector,
//LEFT JOIN BeleggingssectorPerFonds ON Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder=Portefeuilles.Vermogensbeheerder 

		// haal koersresultaat op om % te berekenen

		$rapjaar = date('Y',db2jul($this->rapportageDatumVanaf));
		$koersresultaat = gerealiseerdKoersresultaat($this->portefeuille,$this->rapportageDatumVanaf, $this->rapportageDatum,$this->pdf->rapportageValuta);
		$transactietypen = array();

		$buffer = array();
		$sortBuffer = array();

		while($mutaties = $DB->nextRecord())
		{
			$buffer[] = $mutaties;
		}
    $aantalRegels=count($buffer);
    $n=0;

		foreach ($buffer as $mutaties)
		{
		  $n++;
      if($mutaties['FondsAanVerkoop']==1)
      {
      
       $this->pdf->SetFillColor($this->pdf->rapport_kop2_bgcolor['r'],$this->pdf->rapport_kop2_bgcolor['g'],$this->pdf->rapport_kop2_bgcolor['b']);
       $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);




			if($mutaties['Transactietype'] != $lastTransactietype)
      {
        if($lastTransactietype <> '')
        {
          $this->preRow();
          $this->pdf->row(array('','','','','','','','','','','',''));
        }
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->preRow();
        $this->pdf->row(array($transactietypenOmschrijving[$mutaties['Transactietype']],'','','','','','','','','','',''));
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      }
      
      $lastTransactietype=$mutaties['Transactietype'];
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

				$historie = berekenHistorischKostprijs($this->portefeuille, $mutaties['Fonds'], $mutaties['Boekdatum'],$this->pdf->rapportageValuta,$this->rapportageDatumVanaf,$mutaties['id']);

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



	

			$this->pdf->setX($this->pdf->marge);

			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);


				$percentageTotaal = ABS(($resultaatlopende / ($resultaatvoorgaande + $historischekostprijs)) *100);

				if($resultaatlopende < 0)
					$percentageTotaal = (-1*$percentageTotaal);

				if($percentageTotaal <>0)
				{
					if($percentageTotaal > 1000 || $percentageTotaal < -1000)
						$percentageTotaalTekst = "p.m.";
					else
						$percentageTotaalTekst = $this->formatGetal($percentageTotaal,1).'%';

				}
				else
					$percentageTotaalTekst = "";
	

			    $datum=date("d-m-y",db2jul($mutaties['Boekdatum']));
          
        if($n==$aantalRegels)  
          $extra='E';
        else
          $extra='';
            
        $this->preRow($extra);

				$omschrijvingWidth=$this->pdf->GetStringWidth($mutaties['Omschrijving']);

				$cellWidth=$this->pdf->widths[2]-2;

				if($omschrijvingWidth > $cellWidth)
				{
					$dotWidth=$this->pdf->GetStringWidth('...');
					$chars=strlen($mutaties['Omschrijving']);
					$newOmschrijving=$mutaties['Omschrijving'];
					for($i=3;$i<$chars;$i++)
					{
						$omschrijvingWidth=$this->pdf->GetStringWidth(substr($newOmschrijving,0,$chars-$i));
						if($cellWidth>($omschrijvingWidth+$dotWidth))
						{
							$omschrijving=substr($newOmschrijving,0,$chars-$i).'...';
							break;
						}
					}
				}
				else
					$omschrijving=$mutaties['Omschrijving'];


				$this->pdf->row(array($datum,
											$this->formatGetal($mutaties['Aantal'],0),
											$omschrijving,
                      $mutaties['Valuta'],
											$this->formatGetal($mutaties['Fondskoers'],2),
                      $this->formatGetal($mutaties['Valutakoers']* $mutaties['Rapportagekoers'],2),
                      $this->formatGetal($mutaties['Bedrag']* $mutaties['Rapportagekoers'],2),
											'',
											$result_historischkostprijs,
											$result_voorgaandejaren,
											$result_lopendejaar,
											$percentageTotaalTekst));


       $this->AddRegel($mutaties);

			}
    else
    {
      $this->AddRegel($mutaties);
    }
  }

                                        
 

	



		//$koersresultaat = gerealiseerdKoersresultaat($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum);
		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}


    unset($this->pdf->fillCell);

		if(isset($this->pdf->rapport_TRANS_disclaimerText))
		{
		  $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize-2);
		  $this->pdf->MultiCell(280,4, $this->pdf->rapport_TRANS_disclaimerText, 0, "L");
		  $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
		}



	}
  
  function AddRegel($mutaties)
  {
    $datum=date("d-m-y",db2jul($mutaties['Boekdatum']));
    $pasCode=$mutaties['pasCode'];
  	if($mutaties['Grootboekrekening']=='FONDS')
		{
		  if(substr($mutaties['Transactietype'],0,1)=='A' || $mutaties['Transactietype']=='D')
        $pasCode='E01';
 		  elseif(substr($mutaties['Transactietype'],0,1)=='V' || $mutaties['Transactietype']=='L')
        $pasCode='E02';        
		}
    else
    {
      if($mutaties['Grootboekrekening']=='RENME')
      {
		    if($mutaties['BedragEur'] < 0 )
          $pasCode='E03';
 		    elseif($mutaties['BedragEur'] > 0 )
          $pasCode='E04';   
      }
      elseif($mutaties['Grootboekrekening']=='DIVBE')
      {
		    if($mutaties['fondssoort']=='OBL')
          $pasCode='E13';
 		    else
          $pasCode='E10';   
      }
      elseif($mutaties['Grootboekrekening']=='ROER')
      {
		    if($mutaties['fondssoort']=='OBL')
          $pasCode='E12';
 		    else
          $pasCode='E09';
          
        if($mutaties['Fonds']=='')
          $pasCode='D04';
      }
      elseif($mutaties['Grootboekrekening']=='BTLBR')
      {
		    if($mutaties['fondssoort']=='OBL')
          $pasCode='E13';
 		    else
          $pasCode='E10';
      }
      elseif($mutaties['Grootboekrekening']=='KNBA')
      {
        if($mutaties['Fonds']=='')
          $pasCode='D08';
        else
          $pasCode='E14'; 
          
        if($mutaties['BedragEur'] > 0 ) 
          $pasCode='D09'; 
      }
      elseif($mutaties['Grootboekrekening']=='KOST')
      {
        if($mutaties['Fonds']=='')
          $pasCode='D05';
        else
          $pasCode='E14'; 
      }
      elseif($mutaties['Grootboekrekening']=='BTWAU')
      {
        $pasCode='D12'; 
      }
			elseif($mutaties['Grootboekrekening']=='MWBEL')
			{
				$pasCode='E06';
			}
			elseif($mutaties['Grootboekrekening']=='BTW')
			{
				if($mutaties['Fonds']=='')
					$pasCode='D10';
				else
			  	$pasCode='E07';
			}
      //$importCode=$mutaties['FondsImportCode'];
    }

		if($mutaties['rekeningValuta']=="EUR" && $mutaties['Valuta'] <> "EUR")
		{
			$valuta="EUR";
		}
		else
		{
			$valuta=$mutaties['Valuta'];
		}

    if($mutaties['Fonds']!='')
    {
      $this->pdf->excelData[]=array($this->portefeuille,$pasCode,'FONDS',$mutaties['FondsImportCode'],
                                      $mutaties['BedragEur']*-1* $mutaties['Rapportagekoers'],$valuta,date("d-m-Y",db2jul($mutaties['Boekdatum'])));//,$mutaties['Grootboekrekening'],$mutaties['rekeningOmschrijving']);
                                        
	    $this->pdf->excelData[]=array($this->portefeuille,'D11','REKENING',$mutaties['rekeningValuta'],
                                        $mutaties['BedragEur']* $mutaties['Rapportagekoers'],$valuta,date("d-m-Y",db2jul($mutaties['Boekdatum'])));
    }
    else
    {
      $importCode=$mutaties['Valuta'];
			if($mutaties['Termijnrekening']==1)
			{
				$importCode=$importCode."T";
			}
      $this->pdf->excelData[]=array($this->portefeuille,$pasCode,'REKENING',$importCode,
                                        $mutaties['BedragEur']* $mutaties['Rapportagekoers'],$valuta,date("d-m-Y",db2jul($mutaties['Boekdatum'])));//,$mutaties['Grootboekrekening'],$mutaties['rekeningOmschrijving']);
    }
  }
  
  function preRow($extra)
  {
    $this->pdf->CheckPageBreak($this->pdf->rowHeight);
    if($this->pdf->GetY()< 51)
    {
      $this->pdf->CellBorders = array(array('L','T'),'T','T','T','T','T','T','T','T','T','T',array('R','T'));
    }
    elseif($this->pdf->GetY()> 188 || $extra=='E')
    {
      $this->pdf->CellBorders = array(array('L','U'),'U','U','U','U','U','U','U','U','U','U',array('R','U'));
    }
    else
      $this->pdf->CellBorders = array('L','','','','','','','','','','','R');
  }
}
?>

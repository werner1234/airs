<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/06/16 09:50:08 $
File Versie					: $Revision: 1.17 $

$Log: RapportTRANS_L26.php,v $
Revision 1.17  2019/06/16 09:50:08  rvv
*** empty log message ***

Revision 1.16  2019/06/15 20:53:26  rvv
*** empty log message ***

Revision 1.15  2018/04/21 17:55:51  rvv
*** empty log message ***

Revision 1.14  2017/05/25 14:35:58  rvv
*** empty log message ***

Revision 1.13  2011/07/17 14:52:22  rvv
*** empty log message ***

Revision 1.12  2010/12/12 15:35:55  rvv
*** empty log message ***

Revision 1.11  2010/11/12 07:35:25  rvv
kosten optellen en via grootboek.kosten=1

Revision 1.10  2010/09/15 16:29:09  rvv
*** empty log message ***

Revision 1.9  2010/07/27 04:19:13  rvv
*** empty log message ***

Revision 1.8  2010/07/26 18:10:40  rvv
*** empty log message ***

Revision 1.7  2010/07/26 12:31:39  cvs
*** empty log message ***

Revision 1.6  2010/07/24 12:02:53  rvv
*** empty log message ***

Revision 1.5  2010/07/21 17:36:35  rvv
*** empty log message ***

Revision 1.4  2010/07/18 17:04:44  rvv
*** empty log message ***

Revision 1.3  2010/07/14 17:33:49  rvv
*** empty log message ***

Revision 1.2  2010/07/11 16:00:05  rvv
*** empty log message ***

Revision 1.1  2010/07/07 16:10:24  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportTransactieoverzichtLayout.php");

class RapportTRANS_L26
{
	function RapportTRANS_L26($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "TRANS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Mutatie overzicht ".date('d-m-Y',$this->pdf->rapport_datumvanaf)." tot ".date('d-m-Y',$this->pdf->rapport_datum);

		if ($this->pdf->rapportageValuta != 'EUR' && $this->pdf->rapportageValuta != '')
		  $this->pdf->rapport_titel .= " in ".$this->pdf->rapportageValuta;

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->underlinePercentage=0.8;
	}

	function formatGetal($waarde, $dec)
	{
	  if(round($waarde,$dec) <> 0)
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


	function writeRapport()
	{
	  $transactietypenÓmschrijving= array('A'=>'Aankoop',
	                                      'A/O'=>'Aankoop / openen',
	                                      'A'=>'Aankoop',
	                                      'A/S'=>'Aankoop / sluiten',
	                                      'D'=>'Deponering',
	                                      'L'=>'Lichting',
	                                      'V'=>'Verkoop',
	                                      'V/O'=>'Verkoop / openen',
	                                      'V/S'=>'Verkoop / sluiten',
	                                      'Kruispost'=>'Overboeking');


	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		$DB = new DB();
		$db2 = new DB();



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
				$this->pdf->SetDrawColor($this->pdf->rapport_lijn_rood['r'],$this->pdf->rapport_lijn_rood['g'],$this->pdf->rapport_lijn_rood['b']);
		$this->pdf->SetLineWidth(0.1);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$query="SELECT Rekeningmutaties.id,
Rekeningmutaties.Grootboekrekening,
Fondsen.Omschrijving,
Fondsen.Fondseenheid,
Rekeningmutaties.Boekdatum,
 if(  Rekeningmutaties.Grootboekrekening='RENME','D',  Rekeningmutaties.Transactietype )  as Transactietype    ,
Rekeningmutaties.Valuta,
Rekeningmutaties.Afschriftnummer,
Rekeningmutaties.Omschrijving AS rekeningOmschrijving,
Rekeningmutaties.Aantal AS Aantal,
Rekeningmutaties.Fonds,
Rekeningmutaties.Fondskoers,
Rekeningmutaties.Debet AS Debet,
Rekeningmutaties.Credit AS Credit,
Grootboekrekeningen.Omschrijving as grootboekOmschrijving,
Grootboekrekeningen.Kosten,
Rekeningmutaties.Valutakoers,
1 $koersQuery as Rapportagekoers,
 if(Rekeningmutaties.Grootboekrekening IN( 'STORT','ONTR') ,1,
 if( Transactietype IN('D','L'),2,
 if( Rekeningmutaties.Grootboekrekening='RENME' ,2,
 if(Transactietype like 'A%' ,3,
 if(Transactietype like 'V%' ,4,
if(Rekeningmutaties.Grootboekrekening IN( 'KOST') ,5,
 if(Rekeningmutaties.Grootboekrekening IN( 'DIV','DIVBE','RENOB') ,6,
 if(Rekeningmutaties.Grootboekrekening IN( 'RENTE') ,7,
 if(Rekeningmutaties.Grootboekrekening NOT IN( 'RENTE','DIV','DIVB','RENOB','FONDS','RENME','STORT','ONTR','KRUIS') ,8,
10 )) )) ))) )) as transactieVolgorde,

CategorienPerHoofdcategorie.Hoofdcategorie,
BeleggingscategoriePerFonds.Beleggingscategorie,

IFNULL(HoofdBeleggingscategorien.Omschrijving,'Liquiditeiten') as Homschrijving

FROM
Rekeningmutaties
LEFT Join Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
 JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening

LEFT Join BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien as HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie

WHERE Rekeningen.Portefeuille = '".$this->portefeuille."'  AND
Rekeningmutaties.Verwerkt = '1' AND
Rekeningmutaties.Transactietype <> 'B' AND
Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."'
AND  ((Rekeningen.Memoriaal = 0 OR Rekeningmutaties.Transactietype <> '' ) OR (Rekeningen.Memoriaal =1 AND  Rekeningmutaties.Grootboekrekening='RENME' ) )
AND Rekeningmutaties.Grootboekrekening NOT IN('VERM')
ORDER BY
transactieVolgorde,
Transactietype,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.id,
Fondsen.Omschrijving,
rekeningOmschrijving,
Rekeningmutaties.Fonds
";
//echo $query;exit;
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
//echo $query;
		// haal koersresultaat op om % te berekenen

		$rapjaar = date('Y',db2jul($this->rapportageDatumVanaf));
		//$koersresultaat = gerealiseerdKoersresultaat($this->portefeuille,$this->rapportageDatumVanaf, $this->rapportageDatum,$this->pdf->rapportageValuta);
		$transactietypen = array();

		$buffer = array();
		$sortBuffer = array();

		while($mutaties = $DB->nextRecord())
		{
  		if($mutaties['Fonds'] <> '' && $mutaties['Kosten'] == '1')
	    	$extraBuffer['KOST'][$mutaties['Fonds']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['rekeningOmschrijving']][]=(ABS($mutaties['Debet'])*-1+ABS($mutaties['Credit']))*$mutaties['Valutakoers'];
  	  else
	      $buffer[] = $mutaties;
	 	}

//listarray($extraBuffer);
		foreach ($buffer as $mutaties)
		{

		  $historie=array();
			//if($mutaties[Transactietype] != "A/S")
			$mutaties['Aantal'] = abs($mutaties['Aantal']);
			$resultaat=0;

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
			$historischekostprijs=0;
			$beginditjaar=0;


			switch($mutaties['Transactietype'])
			{
					case "A" :
						// Aankoop
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
						$t_aankoop_koers					= $mutaties['Fondskoers'];

						$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $t_aankoop_koers;
						if($t_aankoop_waardeinValuta > 0)
							$aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
						if($t_aankoop_koers > 0)
							$aankoop_waarde 				= $t_aankoop_waarde;
					break;
					case "A/O" :
						// Aankoop / openen
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
						$t_aankoop_koers					= $mutaties['Fondskoers'];

						$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $t_aankoop_koers;
						if($t_aankoop_waardeinValuta > 0)
							$aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
						if($t_aankoop_koers > 0)
							$aankoop_waarde 				= $t_aankoop_waarde;
					break;
					case "A/S" :
						// Aankoop / sluiten
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
						$t_aankoop_koers					= $mutaties['Fondskoers'];

						$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $t_aankoop_koers;
						if($t_aankoop_waardeinValuta > 0)
							$aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
						if($t_aankoop_koers > 0)
							$aankoop_waarde 				= $t_aankoop_waarde;

					break;
					case "B" :
						// Beginstorting
					break;
					case "D" :
					case "S" :
							// Deponering
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
						$t_aankoop_koers					= $mutaties['Fondskoers'];

						$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $t_aankoop_koers;
						if($t_aankoop_waardeinValuta > 0)
							$aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
						if($t_aankoop_waarde > 0)
							$aankoop_waarde 				= $t_aankoop_waarde;
					break;
					case "L" :
							// Lichting
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
						$t_verkoop_koers					= $mutaties['Fondskoers'];

						$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $t_verkoop_koers;
						if($t_verkoop_waardeinValuta > 0)
							$verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $t_verkoop_waarde;
					break;
					case "V" :
							// Verkopen
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
				//		echo " $t_verkoop_waarde 				= abs(".$mutaties['Credit'].") * ".$mutaties['Valutakoers']."  * ".$mutaties['Rapportagekoers']."  ";
						$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
						$t_verkoop_koers					= $mutaties['Fondskoers'];

						$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $t_verkoop_koers;
						if($t_verkoop_waardeinValuta > 0)
							$verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $t_verkoop_waarde;
					break;
					case "V/O" :
							// Verkopen / openen
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
						$t_verkoop_koers					= $mutaties['Fondskoers'];

						$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $t_verkoop_koers;
						if($t_verkoop_waardeinValuta > 0)
							$verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $t_verkoop_waarde;
					break;
					case "V/S" :
					 		// Verkopen / sluiten
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
						$t_verkoop_koers					= $mutaties['Fondskoers'];

						$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $t_verkoop_koers;
						if($t_verkoop_waardeinValuta > 0)
							$verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $t_verkoop_waarde;
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

				$historie = berekenHistorischKostprijs($this->portefeuille, $mutaties['Fonds'], $mutaties['Boekdatum'],$this->pdf->rapportageValuta,$this->rapportageDatumVanaf,$mutaties['id']);

				if($mutaties['Transactietype'] == "A/S")
				{
					$historischekostprijs  = ($mutaties['Aantal'] * -1) * $historie['historischeWaarde']      * $historie['historischeValutakoers']        * $mutaties['Fondseenheid'];
					$beginditjaar          = ($mutaties['Aantal'] * -1) * $historie['beginwaardeLopendeJaar'] * $historie['beginwaardeValutaLopendeJaar']  * $mutaties['Fondseenheid'];
				}
				else
				{
					$historischekostprijs = $mutaties['Aantal']        * $historie['historischeWaarde']       * $historie['historischeValutakoers']        * $mutaties['Fondseenheid'];
				  $beginditjaar         = $mutaties['Aantal']        * $historie['beginwaardeLopendeJaar']  * $historie['beginwaardeValutaLopendeJaar']  * $mutaties['Fondseenheid'];
				}

        if($this->pdf->rapportageValuta != 'EUR' && $mutaties['Valuta'] == $this->pdf->rapportageValuta)
        {
  		    $historischekostprijs = $historischekostprijs / $historie['historischeValutakoers'];
		      $beginditjaar         = $beginditjaar         / getValutaKoers($this->pdf->rapportageValuta ,date("Y",db2jul($this->rapportageDatum).'-01-01'));
        }
        elseif ($this->pdf->rapportageValuta != 'EUR')
		    {
		    $historischekostprijs = $historischekostprijs / $historie['historischeRapportageValutakoers'];
		    $beginditjaar         = $beginditjaar         / getValutaKoers($this->pdf->rapportageValuta ,date("Y",db2jul($this->rapportageDatum).'-01-01'));

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
					if($mutaties['Transactietype'] == "A/S")
					{
						$resultaatvoorgaande = $beginditjaar - $historischekostprijs;
						$resultaatlopende = ($t_aankoop_waarde * -1) - $beginditjaar;
					}
				}

				$result_historischkostprijs = $historischekostprijs;
				$result_voorgaandejaren = $resultaatvoorgaande;
				$result_lopendejaar = $resultaatlopende;

				$totaal_resultaat_waarde += $resultaatlopende;

			}
			else
			{
				$result_historischkostprijs = "";
				$result_voorgaandejaren = "";
				$result_lopendejaar = "";
				$aankoop_waarde=($mutaties['Valutakoers']*$mutaties['Credit']-$mutaties['Valutakoers']*$mutaties['Debet']);
			  if($mutaties['Transactietype'] =='')
		  		$mutaties['Transactietype']=$mutaties['Grootboekrekening'];
			}


			if($aankoop_koers)
			  $fondsKoersVV=$aankoop_koers;
			else
			  $fondsKoersVV=$verkoop_koers;

			if($aankoop_waarde)
			  $waardeEur=$aankoop_waarde;
			else
			  $waardeEur=$verkoop_waarde;


			if( $mutaties['Aantal'] <> 0 && $historie['historischeWaarde'] <> 0)
			{
		//  $koersresultaat=($mutaties['Aantal']*$fondsKoersVV* $mutaties['Fondseenheid'] - $mutaties['Aantal']*$historie['historischeWaarde']* $mutaties['Fondseenheid'] ) * $mutaties['Valutakoers'];
		   $koersresultaat=($mutaties['Aantal']*$fondsKoersVV* $mutaties['Fondseenheid'] - $mutaties['Aantal']*$historie['beginwaardeLopendeJaar']* $mutaties['Fondseenheid'] ) * $mutaties['Valutakoers'];

			// echo $mutaties[Omschrijving]." $koersresultaat=( ". $mutaties['Aantal']."*$fondsKoersVV * ".$mutaties['Fondseenheid']." - ".$mutaties['Aantal']."*".$historie['historischeWaarde']."* ".$mutaties['Fondseenheid']." ) * ".$mutaties['Valutakoers']."; <br>\n";
			}
			else
			  $koersresultaat=0;



     if($mutaties['Omschrijving'] == '')
       $mutaties['Omschrijving']=$mutaties['rekeningOmschrijving'];

      if($transactietypenÓmschrijving[$mutaties['Transactietype']])
       $categorieOmschrijving=$transactietypenÓmschrijving[$mutaties['Transactietype']];
      elseif($transactietypenÓmschrijving[$mutaties['grootboekOmschrijving']])
       $categorieOmschrijving=$transactietypenÓmschrijving[$mutaties['grootboekOmschrijving']];
      else
       $categorieOmschrijving=$mutaties['grootboekOmschrijving'];



if($categorieOmschrijving=='Dividend')
  $type='DIVBE';
else
  $type='KOST';

$kosten=0;
if(is_array($extraBuffer[$type][$mutaties['Fonds']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['rekeningOmschrijving']]))
{
  $unset=array();
  foreach ($extraBuffer[$type][$mutaties['Fonds']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['rekeningOmschrijving']] as $index=>$waarde)
  {
    $kosten +=$waarde;
    unset($extraBuffer[$type][$mutaties['Fonds']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['rekeningOmschrijving']][$index]);
    //break; //rvv
  }
}



if($categorieOmschrijving=='Dividend'||$categorieOmschrijving=='Dividendbelasting'||$categorieOmschrijving=='Rente obligaties'||$categorieOmschrijving=='Rente')
{
  $resultaat=$waardeEur+$kosten;
  $valutaResultaat=0;
  $waardeEur=0;
}
elseif($mutaties['Kosten']==1)
{
  $kosten=$waardeEur;
 $waardeEur=0;
	$valutaResultaat=0;

}
else
{
  $resultaat=$result_lopendejaar;
  $valutaResultaat=$resultaat-$koersresultaat;
}

if($mutaties['Transactietype']=='')
 $mutaties['Transactietype']='geen';

switch($mutaties['Transactietype'])
			{
			  	case "D" :
					case "L" :
					  $waardeEur=$waardeEur*-1;
//					  $totalen[$mutaties['transactieVolgorde']]['waardeEur'] +=$waardeEur;
					  $totaal['waardeEur'] +=$waardeEur;
					  $totalen[$mutaties['Homschrijving']]['waardeEur'] +=$waardeEur;
					break;

					case "A" :
					case "A/O" :
					case "A/S" :
					case "B" :
					case "S" :
					case "V" :
					case "V/O" :
					case "V/S" :
					  $waardeEur=$waardeEur*-1;
					 // $totalen[$mutaties['transactieVolgorde']]['waardeEur'] +=0;
					//  $totaal['waardeEur'] +=0;
					break;
					case "STORT" :
					case "ONTTR" :
					  $totaal['waardeEur'] +=$waardeEur;
					  $totalen[$mutaties['Homschrijving']]['waardeEur'] +=$waardeEur;
					break;
					default :
//								$totalen[$mutaties['transactieVolgorde']]['waardeEur'] +=$waardeEur;


					break;
			}




    if(!(isset($catTotalen[$mutaties['transactieVolgorde']]['omschrijving'])))
      $catTotalen[$mutaties['transactieVolgorde']]['omschrijving']=$categorieOmschrijving;
    $catTotalen[$mutaties['transactieVolgorde']]['waardeEur'] +=$waardeEur;
    $catTotalen[$mutaties['transactieVolgorde']]['koersresultaat'] +=$koersresultaat;
    $catTotalen[$mutaties['transactieVolgorde']]['valutaresultaat'] +=$valutaResultaat;
    $catTotalen[$mutaties['transactieVolgorde']]['totaalresultaat'] +=$resultaat;
    $catTotalen[$mutaties['transactieVolgorde']]['kosten'] +=$kosten;


    if(!(isset($totalen[$mutaties['Homschrijving']]['omschrijving'])))
      $totalen[$mutaties['Homschrijving']]['omschrijving']=$categorieOmschrijving;

    $totalen[$mutaties['Homschrijving']]['koersresultaat'] +=$koersresultaat;
    $totalen[$mutaties['Homschrijving']]['valutaresultaat'] +=$valutaResultaat;
    $totalen[$mutaties['Homschrijving']]['totaalresultaat'] +=$resultaat;
    $totalen[$mutaties['Homschrijving']]['kosten'] +=$kosten;

    $totaal['koersresultaat'] +=$koersresultaat;
    $totaal['valutaresultaat'] +=$valutaResultaat;
    $totaal['totaalresultaat'] +=$resultaat;
    $totaal['kosten'] +=$kosten;

      $regels[]=array('Boekdatum'=>$mutaties['Boekdatum'],
											'categorieOmschrijving'=>$categorieOmschrijving,
											'Omschrijving'=>rclip($mutaties['Omschrijving'],30),
											'valuta'=>$mutaties['Valuta'],
											'Aantal'=>$mutaties['Aantal'],
											'historischeWaarde'=>$historie['beginwaardeLopendeJaar'], //'historischeWaarde'=>$historie['historischeWaarde'],
											'historischeValutakoers'=>$historie['historischeValutakoers'],
                      'historischekostprijs'=>$beginditjaar, //$historischekostprijs
                      'fondsKoersVV'=>$fondsKoersVV,
                      'Valutakoers'=>$mutaties['Valutakoers'],
                      'waardeEur'=>$waardeEur,
                      'koersresultaat'=>$koersresultaat,
                      'valutaresultaat'=>$valutaResultaat,
                      'resultaat'=>$resultaat,
                      'kosten'=>$kosten,
                      'transactieVolgorde'=>$mutaties['transactieVolgorde']);

      $transactietypen[] = $mutaties['Transactietype'];

		}

    $dataWidth=$this->pdf->widths;
		$this->pdf->SetWidths(array(17+16,45,11,18,17,23,17,17,18,18,18,15,18,15));


		foreach ($totalen as $lastType=>$data)
		{
		  	if(round($data['waardeEur'])!=0 || round($data['koersresultaat'])!=0 || round($data['valutaresultaat'])!=0 || round($data['totaalresultaat'])!=0 || round($data['kosten'])!=0)
		  	{
		  	  if($lastType > 2)
		  	    $data['waardeEur']=0;

       		$this->pdf->row(array($lastType,'','','','','','','','',$this->formatGetal($data['waardeEur'],0),$this->formatGetal($data['koersresultaat'],0),
 		                      $this->formatGetal($data['valutaresultaat'],0),$this->formatGetal($data['totaalresultaat'],0),$this->formatGetal($data['kosten'],0)));
		  	}
		}
		$this->pdf->SetWidths($dataWidth);
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array('T','T','T','T','T','T','T','T','T','T','T','T','T','T','T');
  	$this->pdf->row(array('Totaal','','','','','','','','','',$this->formatGetal($totaal['waardeEur'],0),$this->formatGetal($totaal['koersresultaat'],0),
                        	$this->formatGetal($totaal['valutaresultaat'],0),$this->formatGetal($totaal['totaalresultaat'],0),$this->formatGetal($totaal['kosten'],0)));
    $this->pdf->CellBorders = array();
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    $this->pdf->ln();

		$lastType='';
		foreach ($regels as $regel)
		{
		  if($lastType=='')
      {

        $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
        $this->pdf->MultiCell(100,$this->pdf->rowHeight,$regel['categorieOmschrijving'],false,'L');
      }
      elseif($lastType!=$regel['transactieVolgorde'])
      {
               $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
        $this->pdf->CellBorders = array('','','','','','','','','','','TS','TS','TS','TS','TS');
        $this->pdf->row(array('','','','','','','','','','',$this->formatGetal($catTotalen[$lastType]['waardeEur'],0),$this->formatGetal($catTotalen[$lastType]['koersresultaat'],0),
        $this->formatGetal($catTotalen[$lastType]['valutaresultaat'],0),$this->formatGetal($catTotalen[$lastType]['totaalresultaat'],0),
        $this->formatGetal($catTotalen[$lastType]['kosten'],0)));
        $this->pdf->CellBorders = array();
        $this->pdf->ln();

        $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
        if($this->pdf->getY() > 180 )
          $this->pdf->addPage();
        if($this->pdf->getY() > 60 )
        {
          $this->pdf->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U','U','U','U','U');
          $this->pdf->Row(array("\nBoekdatum","Transactie\ntype","\nFonds","\nValuta","\nAantal","Referentie\nkoers VV","Referentie koers\nValuta","referentie\nwaarde","Koers\nin VV","\nValutakoers","\nStortingen","Gerealiseerd\nkoers","Resultaat valuta","Totaal\ngerealiseerd","\nKosten"));
          $this->pdf->CellBorders=array();
        }
				if($regel['transactieVolgorde']>7)
					$this->pdf->MultiCell(100,$this->pdf->rowHeight,'Overige',false,'L');
				else
          $this->pdf->MultiCell(100,$this->pdf->rowHeight,$regel['categorieOmschrijving'],false,'L');
      }
     	$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
			$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
			$this->pdf->setX($this->pdf->marge);
			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);

			$this->pdf->row(array(date("d-m-Y",db2jul($regel['Boekdatum'])),
		  			         	rclip($regel['categorieOmschrijving'],9),
											$regel['Omschrijving'],
											$regel['valuta'],
                      $this->formatGetal($regel['Aantal'],0),
                      $this->formatGetal($regel['historischeWaarde'],2),
                      $this->formatGetal($regel['historischeValutakoers'],3),
                      $this->formatGetal($regel['historischekostprijs'],0),
                      $this->formatGetal($regel['fondsKoersVV'],3),
                      $this->formatGetal($regel['Valutakoers'],3),
                      $this->formatGetal($regel['waardeEur'],0),
                      $this->formatGetal($regel['koersresultaat'],0),
                      $this->formatGetal($regel['valutaresultaat'],0),
                      $this->formatGetal($regel['resultaat'],0),
                      $this->formatGetal($regel['kosten'],0)));

		   $lastType=$regel['transactieVolgorde'];
		}
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('','','','','','','','','','','TS','TS','TS','TS','TS');
    $this->pdf->row(array('','','','','','','','','','',$this->formatGetal($catTotalen[$lastType]['waardeEur'],0),$this->formatGetal($catTotalen[$lastType]['koersresultaat'],0),
        				$this->formatGetal($catTotalen[$lastType]['valutaresultaat'],0),$this->formatGetal($catTotalen[$lastType]['totaalresultaat'],0),
        				$this->formatGetal($catTotalen[$lastType]['kosten'],0)));
        				$this->pdf->CellBorders = array();


	}
}
?>
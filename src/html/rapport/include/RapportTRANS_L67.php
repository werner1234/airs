<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/06/24 13:02:42 $
File Versie					: $Revision: 1.10 $

$Log: RapportTRANS_L67.php,v $
Revision 1.10  2020/06/24 13:02:42  rvv
*** empty log message ***

Revision 1.9  2019/12/15 13:46:41  rvv
*** empty log message ***

Revision 1.8  2019/12/14 17:46:24  rvv
*** empty log message ***

Revision 1.7  2019/11/06 16:11:20  rvv
*** empty log message ***

Revision 1.6  2019/08/07 15:30:49  rvv
*** empty log message ***

Revision 1.5  2019/01/23 07:45:51  rvv
*** empty log message ***

Revision 1.4  2017/11/15 17:03:35  rvv
*** empty log message ***

Revision 1.3  2016/06/01 19:48:58  rvv
*** empty log message ***

Revision 1.2  2016/04/03 10:58:02  rvv
*** empty log message ***

Revision 1.1  2016/03/06 18:17:00  rvv
*** empty log message ***

Revision 1.7  2015/08/08 11:32:14  rvv
*** empty log message ***

Revision 1.6  2015/08/05 15:59:20  rvv
*** empty log message ***

Revision 1.5  2012/05/02 15:53:13  rvv
*** empty log message ***

Revision 1.4  2012/04/14 16:51:17  rvv
*** empty log message ***

Revision 1.3  2012/03/25 13:27:46  rvv
*** empty log message ***

Revision 1.2  2012/03/17 11:58:16  rvv
*** empty log message ***

Revision 1.1  2012/03/11 17:19:57  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportTransactieoverzichtLayout.php");

class RapportTRANS_L67
{
	function RapportTRANS_L67($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "TRANS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Specificatie effectentransacties";

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

	  if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		// voor kopjes
		$this->pdf->widthA = array(80,20,20,20,25,27,27,30,30);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R');

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
		$this->pdf->templateVars['TRANSPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['TRANSPaginas']=$this->pdf->rapport_titel;



	$query="SELECT
Fondsen.Omschrijving,
Fondsen.Fondseenheid,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Transactietype,
Rekeningmutaties.Valuta,
Rekeningmutaties.Afschriftnummer,
Rekeningmutaties.Omschrijving AS rekeningOmschrijving,
Rekeningmutaties.Aantal AS Aantal,
Rekeningmutaties.Fonds,
Rekeningmutaties.Fondskoers,
Rekeningmutaties.Debet AS Debet,
Rekeningmutaties.Credit AS Credit,
Rekeningmutaties.Valutakoers,
1 $koersQuery AS Rapportagekoers,
Rekeningmutaties.id,
BeleggingscategoriePerFonds.Beleggingscategorie,
CategorienPerHoofdcategorie.Hoofdcategorie,
hcat.Omschrijving as hoofdcategorieOmschrijving,
hcat.Afdrukvolgorde as hoofcategorieVolgorde
FROM
Rekeningmutaties
Inner Join Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
Inner Join Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
Inner Join Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
Inner Join Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
LEFT Join BeleggingscategoriePerFonds ON Fondsen.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder  = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien as hcat ON CategorienPerHoofdcategorie.Hoofdcategorie = hcat.Beleggingscategorie
WHERE
 Rekeningen.Portefeuille = '".$this->portefeuille."' AND
 Rekeningmutaties.Verwerkt = '1' AND
Rekeningmutaties.Transactietype <> 'B' AND
 Grootboekrekeningen.FondsAanVerkoop = '1' AND
 Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."'  AND
Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."'
ORDER BY hoofcategorieVolgorde,Rekeningmutaties.Transactietype, Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds
";
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
		}
    
    $this->pdf->excelData[]=array("Categorie","Omschrijving",'datum',
											'aantal',
											'valuta',
											'wisselkoers',
											'transactiekoers','aankoopbedrag',
											'verkoopbedrag',
											'gerealiseerd resultaat voorgaande jaren',
											'gerealiseerd resultaat huidige jaar');

		$totalen=array();
    $totaal_aankoop_waarde=0;
    $totaal_verkoop_waarde=0;
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
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
						$t_aankoop_koers					= $mutaties['Fondskoers'];

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
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
						$t_aankoop_koers					= $mutaties['Fondskoers'];

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
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
						$t_aankoop_koers					= $mutaties['Fondskoers'];

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
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
						$t_aankoop_koers					= $mutaties['Fondskoers'];

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
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
						$t_verkoop_koers					= $mutaties['Fondskoers'];

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
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
				//		echo " $t_verkoop_waarde 				= abs(".$mutaties['Credit'].") * ".$mutaties['Valutakoers']."  * ".$mutaties['Rapportagekoers']."  ";
						$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
						$t_verkoop_koers					= $mutaties['Fondskoers'];

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
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
						$t_verkoop_koers					= $mutaties['Fondskoers'];

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
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
						$t_verkoop_koers					= $mutaties['Fondskoers'];

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

	//			  echo "$historischekostprijs = ".$mutaties['Aantal']."        * ".$historie['historischeWaarde']."       * ".$historie['historischeValutakoers']."        * ".$mutaties['Fondseenheid']."<br>";
 //echo "$beginditjaar         = ".$mutaties['Aantal']."        * ".$historie['beginwaardeLopendeJaar']."  * ".$historie['beginwaardeValutaLopendeJaar']."  * ".$mutaties['Fondseenheid']."<br>";

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
			$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
			$this->pdf->setX($this->pdf->marge);


      $transactietype=substr($mutaties['Transactietype'],0,1);
			$this->pdf->setX($this->pdf->marge);
			if(($mutaties['hoofdcategorieOmschrijving']!=$lastHcat || $transactietype!=$lastTransactietype ))
			{
			  if(count($totalen) > 0 )
				{
					$aankoop='';
					$resultaatvoorgaande='';
					$resultaatlopende='';
					if($lastTransactietype=='A')
						$type='aankopen';
					else
						$type='verkopen';

					if($totalen['aankoop_waarde']+$totalen['verkoop_waarde'] <> 0)
					{
						$aankoop=$this->formatGetal($totalen['aankoop_waarde']+$totalen['verkoop_waarde'],2);
					}
					if($totalen['resultaatvoorgaande'] <> 0)
					{
						$resultaatvoorgaande=$this->formatGetal($totalen['resultaatvoorgaande'],2);
					}
					if($totalen['resultaatlopende'] <> 0)
					{
						$resultaatlopende=$this->formatGetal($totalen['resultaatlopende'],2);
					}
					$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
					$this->pdf->row(array(vertaalTekst("Totaal" ,$this->pdf->rapport_taal)." ".vertaalTekst($lastHcat ,$this->pdf->rapport_taal)." ".vertaalTekst($type ,$this->pdf->rapport_taal),'',
														'',
														'',
														'',
														'',
														$aankoop,
														$resultaatvoorgaande,
														$resultaatlopende));
					$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
 // listarray($totalen);
					$totalen=array();

				}
			  $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
			  
			  
        if($transactietype=='A')
        {
          $this->pdf->ln();
          $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        	$this->pdf->row(array(vertaalTekst($mutaties['hoofdcategorieOmschrijving'],$this->pdf->rapport_taal)." ". vertaalTekst("aankopen",$this->pdf->rapport_taal),vertaalTekst('datum',$this->pdf->rapport_taal),
											vertaalTekst('aantal',$this->pdf->rapport_taal),
											vertaalTekst('valuta',$this->pdf->rapport_taal),
											vertaalTekst('wisselkoers',$this->pdf->rapport_taal),
											vertaalTekst('transactiekoers',$this->pdf->rapport_taal),
											vertaalTekst('aankoopbedrag',$this->pdf->rapport_taal)));
	      }
        else
        {
           $this->pdf->ln();
           $this->pdf->row(array(vertaalTekst($mutaties['hoofdcategorieOmschrijving'],$this->pdf->rapport_taal)." ". vertaalTekst("verkopen",$this->pdf->rapport_taal),vertaalTekst('datum',$this->pdf->rapport_taal),
											vertaalTekst('aantal',$this->pdf->rapport_taal),
											vertaalTekst('valuta',$this->pdf->rapport_taal),
											vertaalTekst('wisselkoers',$this->pdf->rapport_taal),
											vertaalTekst('transactiekoers',$this->pdf->rapport_taal),
											vertaalTekst('verkoopbedrag',$this->pdf->rapport_taal),
											vertaalTekst('gerealiseerd resultaat voorgaande jaren',$this->pdf->rapport_taal),
											vertaalTekst('gerealiseerd resultaat huidige jaar',$this->pdf->rapport_taal)));
        }
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

			}
      
      $omschrijvingParts=explode(" ",$mutaties['rekeningOmschrijving']);
      $omschrijvingParts[0]=vertaalTekst($omschrijvingParts[0] ,$this->pdf->rapport_taal);
      $omschrijving=implode(" ",$omschrijvingParts);
      
			if($transactietype=='A')
      {
			   $this->pdf->row(array($omschrijving,date("d-m",db2jul($mutaties['Boekdatum'])),
											$this->formatGetal($mutaties['Aantal'],0),
											$mutaties[Valuta],
											$this->formatGetal($mutaties['Valutakoers'],4),
											$this->formatGetal($mutaties['Fondskoers'],4),
											$aankoop_waarde,
											''));
         $this->pdf->excelData[]=array($mutaties['hoofdcategorieOmschrijving']." "."aankopen",
                      $mutaties['rekeningOmschrijving'],date("d-m-Y",db2jul($mutaties['Boekdatum'])),
											round($mutaties['Aantal'],0),
											$mutaties[Valuta],
											round($mutaties['Valutakoers'],4),
											round($mutaties['Fondskoers'],4),
											round($t_aankoop_waarde,2),
											'');

				$totalen['aankoop_waarde']+=$t_aankoop_waarde;
			}
      else
			{
			   $this->pdf->row(array($omschrijving,date("d-m",db2jul($mutaties['Boekdatum'])),
											$this->formatGetal($mutaties['Aantal'],0),
											$mutaties['Valuta'],
											$this->formatGetal($mutaties['Valutakoers'],4),
											$this->formatGetal($mutaties['Fondskoers'],4),
											$verkoop_waarde,
											$this->formatGetal($resultaatvoorgaande,0),
											$this->formatGetal($resultaatlopende,0)));
                      
         $this->pdf->excelData[]=array($mutaties['hoofdcategorieOmschrijving']." "."verkopen",
                      $mutaties['rekeningOmschrijving'],date("d-m-Y",db2jul($mutaties['Boekdatum'])),
											round($mutaties['Aantal'],0),
											$mutaties['Valuta'],
											round($mutaties['Valutakoers'],4),
											round($mutaties['Fondskoers'],4),'',
											round($t_verkoop_waarde,2),
											round($resultaatvoorgaande,0),
											round($resultaatlopende,0));

				$totalen['verkoop_waarde']+=$t_verkoop_waarde;
				$totalen['resultaatvoorgaande']+=$resultaatvoorgaande;
				$totalen['resultaatlopende']+=$resultaatlopende;
			}

			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
			/*
			$this->pdf->row(array(date("d-m",db2jul($mutaties['Boekdatum'])),
											$mutaties['Transactietype'],
											$this->formatGetal($mutaties['Aantal'],0),
											"",
											$aankoop_koers,
											$aankoop_waardeinValuta,
											$aankoop_waarde,
											$verkoop_koers,
											$verkoop_waardeinValuta,
											$verkoop_waarde,
											$result_historischkostprijs,
											$result_voorgaandejaren,
											$result_lopendejaar,
											$percentageTotaalTekst));

					$this->pdf->excelData[]=array(date("d-m",db2jul($mutaties['Boekdatum'])),
											$mutaties['Transactietype'],
											round($mutaties['Aantal'],0),
											$mutaties['Omschrijving'],
                      $t_aankoop_koers,
                      $t_aankoop_waardeinValuta,
											$t_aankoop_waarde,
											$t_verkoop_koers,
											$t_verkoop_waardeinValuta,
											$t_verkoop_waarde,
											$historischekostprijs,
											$resultaatvoorgaande,
											$resultaatlopende,
											$percentageTotaal);
*/
      $lastHcat=$mutaties['hoofdcategorieOmschrijving'];
      $lastTransactietype=$transactietype;
			$transactietypen[] = $mutaties['Transactietype'];
		}
		if(count($totalen) > 0 )
		{
			$aankoop='';
			$resultaatvoorgaande='';
			$resultaatlopende='';
			if($lastTransactietype=='A')
				$type='aankopen';
			else
				$type='verkopen';

			if($totalen['aankoop_waarde']+$totalen['verkoop_waarde'] <> 0)
			{
				$aankoop=$this->formatGetal($totalen['aankoop_waarde']+$totalen['verkoop_waarde'],2);
			}
			if($totalen['resultaatvoorgaande'] <> 0)
			{
				$resultaatvoorgaande=$this->formatGetal($totalen['resultaatvoorgaande'],2);
			}
			if($totalen['resultaatlopende'] <> 0)
			{
				$resultaatlopende=$this->formatGetal($totalen['resultaatlopende'],2);
			}
			$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
			$this->pdf->row(array(vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastHcat ,$this->pdf->rapport_taal)." ".vertaalTekst( $type ,$this->pdf->rapport_taal),'',
												'',
												'',
												'',
												'',
												$aankoop,
												$resultaatvoorgaande,
												$resultaatlopende));
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			// listarray($totalen);
			$totalen=array();

		}
    
		if($this->pdf->getY()+7*4>$this->pdf->PageBreakTrigger)
		  $this->pdf->addPage();
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    
    if($this->pdf->rapport_taal==1)
    {
      $this->pdf->MultiCell(280, 4, "We have assessed the investments Fair Capital Partners asset managers has managed on your behalf in the past year based on your profile (as known to us*). We have determined that the investments in your portfolio, and about which we report in this portfolio statement, are appropriate for you and in line with your goals and risk appetite.

* Please get in touch with your asset manager if there have been any changes to your circumstances that could affect the management of your assets, if you have not already done so.");
    }
    else
    {
      $this->pdf->MultiCell(280,4, 'Wij hebben de beleggingen zoals Fair Capital Partners vermogensbeheer deze het afgelopen jaar voor u heeft gedaan, beoordeeld in het licht van uw profiel zoals die bij ons bekend is*. We hebben vastgesteld dat de beleggingen in uw portefeuille – waarover we u in deze rapportage informeren – passend zijn voor u en in overeenstemming zijn met uw doelstellingen en risicobereidheid.’

* neemt u contact op met uw vermogensbeheerder indien er zich wel wijzigingen hebben voorgedaan in uw situatie die van belang zijn voor het beheer van uw vermogen en u uw vermogensbeheerder hiervan nog niet in kennis hebt gesteld.', 0, "L");
    }
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);



	}
}
?>
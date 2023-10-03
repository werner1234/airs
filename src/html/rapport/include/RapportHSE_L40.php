<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2013/07/15 17:06:38 $
File Versie					: $Revision: 1.3 $

$Log: RapportHSE_L40.php,v $
Revision 1.3  2013/07/15 17:06:38  rvv
*** empty log message ***

Revision 1.2  2012/10/17 15:55:14  rvv
*** empty log message ***

Revision 1.1  2012/07/29 10:24:33  rvv
*** empty log message ***

Revision 1.40  2012/03/14 17:29:35  rvv
*** empty log message ***

Revision 1.39  2012/01/15 11:03:37  rvv
*** empty log message ***

Revision 1.38  2011/12/24 16:36:57  rvv
*** empty log message ***

Revision 1.37  2011/12/24 16:34:55  rvv
*** empty log message ***

Revision 1.36  2011/06/25 16:51:45  rvv
*** empty log message ***

Revision 1.35  2011/05/18 16:51:08  rvv
*** empty log message ***

Revision 1.34  2010/09/15 16:27:45  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportHuidigeSamenstellingLayout.php");

class RapportHSE_L40
{
	function RapportHSE_L40($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum, $valuta = 'EUR')
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "HSE";
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_HSE_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_HSE_titel;
		else
			$this->pdf->rapport_titel = "Huidige samenstelling effectenportefeuille";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->verdeling='beleggingssector';
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
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

	function printTotaal($title, $type,$fontStyle='')
	{
	  if($type=='hoofdcategorie')
	  {
	    $space='';
	  }
	  if($type=='beleggingscategorie')
	  {
	    $space='  ';
	  }
	  if($type=='verdeling')
	  {
	    $space='    ';
	  }


	  $this->pdf->SetFont($this->pdf->rapport_font,$fontStyle,$this->pdf->rapport_fontsize);
  	$this->pdf->SetX($this->pdf->marge);
		$this->pdf->Cell(150,4, $space.'Totaal '.$title, 0, "L");
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->setX($this->pdf->marge);
		$this->pdf->row(array("","",'','',
													$this->formatGetal($this->totalen[$type]['beginPortefeuilleWaardeInValuta'],$this->pdf->rapport_decimaal),
													$this->formatGetalKoers($this->totalen[$type]['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_decimaal,true),
													"",'',
													$this->formatGetal($this->totalen[$type]['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_decimaal),
													$this->formatGetalKoers($this->totalen[$type]['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_decimaal) ));
		$this->totalen[$type]=array();
	}

	function printKop($title, $type, $fontStyle="")
	{
	  if($type=='hoofdcategorie')
	  {
	    $space='';
	  }
	  if($type=='beleggingscategorie')
	  {
	    $space='  ';
	  }
	  if($type=='verdeling')
	  {
	    $space='    ';
	  }
		$this->pdf->SetFont($this->pdf->rapport_font,$fontStyle,$this->pdf->rapport_fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $space.$title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

	}

	function writeRapport()
	{

	  # LOOP over H-CAT/CAT/(regio of secotr)
	  # eerst fonds dan optie tonen.
	  # rapportagedatum +365 dagen is kortlopende
	  # P 229002
		global $__appvar;
		// rapport settings
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();

	  $query="SELECT Vermogensbeheerders.VerouderdeKoersDagen
    FROM Vermogensbeheerders Inner Join Portefeuilles ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
    WHERE portefeuille = '".$this->portefeuille."' ";
		$DB->SQL($query);
		$DB->Query();
		$dagen = $DB->nextRecord();
    $maxDagenOud=$dagen['VerouderdeKoersDagen'];

	  $this->pdf->widthB = array(10,55,20,20,30,30,15,20,30,30,20);
		$this->pdf->alignB = array('L','L','R','R','R','R','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = array(65,20,20,30,30,15,20,30,30,20);
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R');

		$this->pdf->AddPage();

		// haal totaalwaarde op om % te berekenen
		$DB = new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."'"
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		$actueleWaardePortefeuille = 0;

    if($categorien['Valuta'] == $this->pdf->rapportageValuta)
		  $beginQuery = 'beginwaardeValutaLopendeJaar';
		else
		  $beginQuery = $this->pdf->ValutaKoersBegin;

		$DB2 = new DB();

		// print categorie headers
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		// print totaal op hele categorie.
		if($lastCategorie <> $categorien['BeleggingscategorieOmschrijving'] && !empty($lastCategorie) )
		{
			$percentageVanTotaal = $totaalactueel / ($totaalWaarde/100);
			if($this->pdf->rapport_layout == 4 )
				$totaalbegin = 0;
			$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal);
			$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel, $percentageVanTotaal);
			$totaalbegin = 0;
			$totaalactueel = 0;
		}

//		$verdeling='regio';
		$verdeling=$this->verdeling;



				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);



			$query = "SELECT
			 TijdelijkeRapportage.beleggingscategorie,
			 TijdelijkeRapportage.valuta,
			 TijdelijkeRapportage.fondsOmschrijving,
			 TijdelijkeRapportage.actueleValuta,
			 TijdelijkeRapportage.totaalAantal,
			 TijdelijkeRapportage.beginwaardeLopendeJaar,
			 TijdelijkeRapportage.beginPortefeuilleWaardeInValuta,
			 TijdelijkeRapportage.beginPortefeuilleWaardeEuro / $beginQuery as beginPortefeuilleWaardeEuro,
			 TijdelijkeRapportage.actueleFonds, TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,
			 round((UNIX_TIMESTAMP(TijdelijkeRapportage.rapportageDatum) - UNIX_TIMESTAMP(TijdelijkeRapportage.koersDatum))/86400) as koersLeeftijd,
			 TijdelijkeRapportage.hoofdsector,
       TijdelijkeRapportage.hoofdcategorieOmschrijving,
       TijdelijkeRapportage.beleggingscategorie,
       TijdelijkeRapportage.beleggingscategorieOmschrijving,
       TijdelijkeRapportage.beleggingscategorieVolgorde,
       $verdeling as verdeling,
       ".$verdeling."Omschrijving as verdelingOmschrijving,
       ".$verdeling."Volgorde,
       Fondsen.OptieBovenliggendFonds,
       if(Fondsen.OptieBovenliggendFonds='',TijdelijkeRapportage.Fonds ,Fondsen.OptieBovenliggendFonds) as onderliggendFonds
			 FROM TijdelijkeRapportage
			 LEFT Join Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
			 WHERE
			 TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
			 TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".
			$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde,TijdelijkeRapportage.beleggingscategorieVolgorde, ".$verdeling."Volgorde, onderliggendFonds,Fondsen.OptieBovenliggendFonds, TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
			debugSpecial($subquery,__FILE__,__LINE__);

			$DB2->SQL($query);
			$DB2->Query();
			$somVelden=array('beginPortefeuilleWaardeInValuta','beginPortefeuilleWaardeEuro','actuelePortefeuilleWaardeInValuta','actuelePortefeuilleWaardeEuro');
			$vedelingen=array('hoofdcategorie'=>'bi','beleggingscategorie'=>'i','verdeling'=>'','alles'=>'');
			$omschrijvingVelden=array('hoofdcategorieOmschrijving'=>'hoofdcategorie','beleggingscategorieOmschrijving'=>'beleggingscategorie','verdelingOmschrijving'=>$verdeling);
			while($subdata = $DB2->NextRecord())
			{
			  foreach ($omschrijvingVelden as $veldNaam=>$omschrijving)
			    if($subdata[$veldNaam]=='')
			      $subdata[$veldNaam] ="Geen $omschrijving";

			  foreach (array_reverse($vedelingen,true) as $type=>$weergave)
			  {
			    if($lastVerdeling[$type] <> $subdata[$type.'Omschrijving'] && isset($this->totalen[$type]))
			      $this->printTotaal($lastVerdeling[$type],$type,$weergave);
			  }

			  foreach ($vedelingen as $type=>$weergave)
  			  if($lastVerdeling[$type] <> $subdata[$type.'Omschrijving'])
	  		  	$this->printKop(vertaalTekst($subdata[$type.'Omschrijving'],$this->pdf->rapport_taal),$type, $weergave);



				$this->pdf->setX($this->pdf->marge);

				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,$subdata['fondsOmschrijving']);
				$this->pdf->setX($this->pdf->marge);
				$this->pdf->row(array("",
													"",
													$this->formatAantal($subdata['totaalAantal'],0,$this->pdf->rapport_HSE_aantalVierDecimaal),
													$this->formatGetal($subdata['beginwaardeLopendeJaar'],2),
													$this->formatGetal($subdata['beginPortefeuilleWaardeInValuta'],$this->pdf->rapport_decimaal),
													$this->formatGetalKoers($subdata['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_decimaal,true),
													"",
													$this->formatGetal($subdata['actueleFonds'],2).$markering,
													$this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_decimaal),
													$this->formatGetalKoers($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_decimaal),
													($this->pdf->rapport_inprocent)?$this->formatGetal($percentageVanTotaal,2)." %":""));



      foreach ($vedelingen as $type=>$weergave)
      {
        $lastVerdeling[$type]=$subdata[$type.'Omschrijving'];
        foreach ($somVelden as $veld)
          $this->totalen[$type][$veld]+=$subdata[$veld];
      }

		}

	  foreach (array_reverse($vedelingen,true) as $type=>$weergave)
		{
		  if(isset($this->totalen[$type]) && $type <> 'alles')
		     $this->printTotaal($lastVerdeling[$type],$type,$weergave);
		}



  	// print grandtotaal
		//$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,100,true);
		$this->pdf->ln();
		$this->printTotaal('','alles','B');


	}
}
?>
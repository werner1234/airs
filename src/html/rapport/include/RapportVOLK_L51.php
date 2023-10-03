<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/include/RapportEND_L51.php");

class RapportVOLK_L51
{
	function RapportVOLK_L51($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
    //$this->end=new RapportEND_L51($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VOLK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		if($this->pdf->rapport_VOLK_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_VOLK_titel;
		else
			$this->pdf->rapport_titel = "Vergelijkend overzicht lopend kalenderjaar";

		if(substr(jul2form($this->pdf->rapport_datumvanaf),0,5) != '01-01')
			$this->pdf->rapport_titel = "Vergelijkend overzicht rapportage periode";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

		$this->pdf->underlinePercentage=0.8;
    $this->extraVoetPages=array();
    $this->extraVoet='';
    $this->extraVoet2='';
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	  {
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	    return number_format($this->pdf->ValutaKoersEind,2,",",".") ." - ".number_format($waarde,$dec,",",".");
	  }
	  else
	  {
	    $waarde = $waarde / $this->pdf->ValutaKoersBegin;
	    return number_format($this->pdf->ValutaKoersBegin,2,",",".") ." - ".number_format($waarde,$dec,",",".");
	  }
	  //return number_format($waarde,$dec,",",".");
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
	       if (!isset($newDec) && $decimaal != '0')
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



	function printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde,$resultaat=true,$rente=false)
	{
	  $this->pdf->fillCell=array();
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    
	  if($rente==true)
    {
    //  listarray($categorieTotaal);
      unset($categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro']);
      $categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']=$categorieTotaal[$lastCategorieOmschrijving]['renteWaardeEuro'];
    }
   // else
   // {
		$fondsResultaat='';
		$valutaResultaat='';
		$dividend='';

          if(!isset($categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro']))
          {
            $this->pdf->CellBorders = array('','','','','','','','SUB','SUB','SUB');
            if($rente==true)
              $omschrijving='Opgelopen rente';
            else
              $omschrijving=$lastCategorieOmschrijving;
            $this->pdf->row(array(vertaalTekst("Subtotaal",$this->pdf->rapport_taal).' '.vertaalTekst($omschrijving,$this->pdf->rapport_taal),
            '','','','','','',
            $this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
            $this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']/ ($totaalWaarde/100),$this->pdf->rapport_VOLK_decimaal_proc)));
          }
          else
          {
          $this->pdf->CellBorders = array('','','','','SUB','','','SUB','SUB','','SUB','SUB','SUB','SUB','SUB');
          if($resultaat)
          {
           // $resultaatWaarde=$categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']-
          //                   $categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro']; //- $categorieTotaal[$lastCategorieOmschrijving]['renteWaardeEuro']
           // $resultaatProcent=$this->formatGetal(($resultaatWaarde/($categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'] /100)),$this->pdf->rapport_VOLK_decimaal_proc);
            //$resultaatWaarde=$this->formatGetal($resultaatWaarde,$this->pdf->rapport_VOLK_decimaal);

						$resultaatProcent=$this->formatGetal((($categorieTotaal[$lastCategorieOmschrijving]['fondsResultaat']+$categorieTotaal[$lastCategorieOmschrijving]['valutaResultaat']+$categorieTotaal[$lastCategorieOmschrijving]['dividendc'])/($categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'] /100)),$this->pdf->rapport_VOLK_decimaal_proc);
						$fondsResultaat=$this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['fondsResultaat'],$this->pdf->rapport_VOLK_decimaal);
						$valutaResultaat=$this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['valutaResultaat'],$this->pdf->rapport_VOLK_decimaal);
						$ongerealiseerdResultaat=$this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['fondsResultaat']+$categorieTotaal[$lastCategorieOmschrijving]['valutaResultaat'],$this->pdf->rapport_VOLK_decimaal);
						$dividend=$this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['dividend'],$this->pdf->rapport_VOLK_decimaal);

          }
          else
          {
            $this->pdf->CellBorders = array('','','','','SUB','','','SUB','SUB','','','');
            $resultaatProcent='';
           // $resultaatWaarde='';
          }

          $this->pdf->row(array(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorieOmschrijving,$this->pdf->rapport_taal),
          '','','',
          $this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
          '','',
          $this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
          $this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']/ ($totaalWaarde/100),$this->pdf->rapport_VOLK_decimaal_proc),
          '',
          $fondsResultaat,
					$valutaResultaat,
				  $ongerealiseerdResultaat,
					$dividend,
          $resultaatProcent));
          }


   //  }
          $this->pdf->CellBorders = array();
          $this->pdf->ln();
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
         
	}

	function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF = 0, $grandtotaal=false, $totaalG = 0, $totaalH = 0 )
	{
		return $totaalB;
	}

	function printKop($title, $type='',$ln=false)
	{
	  $this->pdf->fillCell=array();
		if($ln)
	    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,$type,$this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst($title,$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}


	function getDividend($fonds)
	{
		global $__appvar;

		if($fonds=='')
			return 0;
    
    if ($this->pdf->lastPOST['doorkijk'] == 1)
		  $portefeuille=$this->portBackup;
    else
      $portefeuille=$this->portefeuille;

		$query="SELECT rapportageDatum,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro as actuelePortefeuilleWaardeEuro,
         TijdelijkeRapportage.type,
         TijdelijkeRapportage.totaalAantal
     FROM TijdelijkeRapportage
     WHERE
       TijdelijkeRapportage.fonds='$fonds' AND
       portefeuille = '".$portefeuille."'".$__appvar['TijdelijkeRapportageMaakUniek']. "
     GROUP BY rapportageDatum,TijdelijkeRapportage.type";

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$totaal=0;
		while($data = $DB->nextRecord())
		{
			if($data['type']=='rente')
				$rente[$data['rapportageDatum']]=$data['actuelePortefeuilleWaardeEuro'];
			elseif($data['type']=='fondsen')
				$aantal[$data['rapportageDatum']]=$data['totaalAantal'];
		}

		$totaal+=($rente[$this->rapportageDatum]-$rente[$this->rapportageDatumVanaf]);
		$totaalCorrected=$totaal;

		$query="SELECT Boekdatum,(Debet*Valutakoers) as Debet,(Credit*valutakoers) as Credit,Bedrag,Rekeningmutaties.Omschrijving
     FROM Rekeningmutaties
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
     JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
     WHERE Rekeningen.Portefeuille='".$portefeuille."' AND
     Rekeningmutaties.Boekdatum >= '".	$this->rapportageDatumVanaf."' AND
     Rekeningmutaties.Boekdatum <= '".	$this->rapportageDatum."' AND
     Rekeningmutaties.Fonds='$fonds' AND
     Grootboekrekeningen.Opbrengst=1";
		$DB->SQL($query);
		$DB->Query();
		//echo "$query <br>\n";
		while($data = $DB->nextRecord())
		{
			$boekdatum=substr($data['Boekdatum'],0,10);
			if(!isset($aantal[$data['Boekdatum']]))
			{
				$fondsAantal=fondsAantalOpdatum($portefeuille,$fonds,$data['Boekdatum']);
				$aantal[$boekdatum]=$fondsAantal['totaalAantal'];
			}
			$aandeel=1;

			if($aantal[$boekdatum] > $aantal[$this->rapportageDatum])
			{
				$aandeel=$aantal[$this->rapportageDatum]/$aantal[$boekdatum];
			}
			// echo "$fonds $aandeel  $boekdatum ".$this->rapportageDatum." ".($data['Credit']-$data['Debet'])."<br>\n";
			$totaal+=($data['Credit']-$data['Debet']);
			$totaalCorrected+=(($data['Credit']-$data['Debet'])*$aandeel);
		}


		return array('totaal'=>$totaal,'corrected'=>$totaalCorrected);
	}

	function writeRapport()
	{
		global $__appvar;
    //$brightness=1.55;
		$this->pdf->SetFillColor($this->pdf->rapport_regelKleur[0],$this->pdf->rapport_regelKleur[1],$this->pdf->rapport_regelKleur[2]);



		$query = "SELECT Vermogensbeheerders.VerouderdeKoersDagen , Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank,
    Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder,
    Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client
    FROM Portefeuilles
    JOIN Clienten ON Portefeuilles.Client = Clienten.Client
    Join Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
    WHERE Portefeuille = '".$this->portefeuille."'";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
    $maxDagenOud=$this->portefeuilledata ['VerouderdeKoersDagen'];

		$this->pdf->AddPage();
    $this->pdf->templateVars['VOLKPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['VOLKPaginas']=$this->pdf->rapport_titel;
    
    if ($this->pdf->lastPOST['doorkijk'] == 1)
    {
      $gegevens = bepaaldFondsWaardenVerdiept_L51($this->portefeuille, $this->rapportageDatumVanaf, $this->pdf,$this->rapportageDatumVanaf);
      vulTijdelijkeTabel($gegevens,'d'.$this->portefeuille,$this->rapportageDatumVanaf);
      $gegevens = bepaaldFondsWaardenVerdiept_L51($this->portefeuille, $this->rapportageDatum, $this->pdf,$this->rapportageDatumVanaf);
      vulTijdelijkeTabel($gegevens,'d'.$this->portefeuille,$this->rapportageDatum);
      $this->portBackup=$this->portefeuille;
      $this->portefeuille='d'.$this->portefeuille;
      
    }
    
		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						  .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		$query = "SELECT TijdelijkeRapportage.hoofdcategorieVolgorde,TijdelijkeRapportage.hoofdcategorieOmschrijving as hoofdcategorieOmschrijving,
TijdelijkeRapportage.Hoofdcategorie,
TijdelijkeRapportage.beleggingscategorieVolgorde as categorieAfdrukVolgorde, TijdelijkeRapportage.beleggingscategorieOmschrijving as categorieOmschrijving,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.fondsOmschrijving, ".
				" TijdelijkeRapportage.fonds, ".
				" TijdelijkeRapportage.actueleValuta, ".
				" TijdelijkeRapportage.Valuta, ".
				" TijdelijkeRapportage.totaalAantal, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
				"IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. ") as beginPortefeuilleWaardeEuro,".
				" TijdelijkeRapportage.actueleFonds,
				TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				  TijdelijkeRapportage.beleggingscategorie,
				  TijdelijkeRapportage.valuta,
          TijdelijkeRapportage.type,
				   TijdelijkeRapportage.portefeuille,
				   TijdelijkeRapportage.historischeWaarde,
           round((UNIX_TIMESTAMP(TijdelijkeRapportage.rapportageDatum) - UNIX_TIMESTAMP(TijdelijkeRapportage.koersDatum))/86400) as koersLeeftijd,
				   Valutas.Valutateken ".
				" FROM TijdelijkeRapportage
LEFT Join Valutas ON TijdelijkeRapportage.valuta = Valutas.Valuta
WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.type IN('fondsen','rente') AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde,
TijdelijkeRapportage.Hoofdcategorie,
TijdelijkeRapportage.beleggingscategorieVolgorde,
TijdelijkeRapportage.beleggingscategorie ,TijdelijkeRapportage.type,
TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";

		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		$fill=false;
    $categorieTotaal=array();
    $hoofdcategorieTotaal=array();
    $lastCategorieOmschrijving='';
    $lastHoofdcategorieOmschrijving='';
    $renteKop=false;
		while($data = $DB->NextRecord())
		{
		    
      if($data['categorieOmschrijving']=='Liquiditeiten' && $data['type']=='fondsen')
        $data['categorieOmschrijving']='Liquiditeiten (fondsen)';
  

      
    //  echo $data['categorieOmschrijving']." ".$data['type']." ".$data['fondsOmschrijving']. " | ".round($data['actuelePortefeuilleWaardeEuro'],0)." => ".round($categorieTotaal[$data['categorieOmschrijving']]['actuelePortefeuilleWaardeEuro'],0)."<br>\n";
    
		  //categorietotalen
		  $this->pdf->rowHeight=5;
     
	    if($data['categorieOmschrijving'] != $lastCategorieOmschrijving && $lastCategorieOmschrijving !='' && is_array($categorieTotaal[$lastCategorieOmschrijving]))
      {
        if($renteKop==true)
        {
          $this->printSubTotaal($lastCategorieOmschrijving, $categorieTotaal, $totaalWaarde, false, true);
          $this->printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde);
        }
        else
          $this->printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde);
      }
      if($data['hoofdcategorieOmschrijving'] != $lastHoofdcategorieOmschrijving)
        $this->printKop($data['hoofdcategorieOmschrijving'],'BI',true);
      if($data['categorieOmschrijving'] != $lastCategorieOmschrijving )
      {
        $this->printKop($data['categorieOmschrijving'],'B',false);
        $renteKop=false;
      }
      
			$fondsResultaatTxt = "";
			$valutaResultaatTxt = "";
			$dividendTxt = "";
			$procentResultaattxt='';
			$ongerealiseerdResultaatTxt='';
			
      if($data['type']=='fondsen')
      {

	$dividend=$this->getDividend($data['fonds']);

	$fondsResultaat = ($data['actuelePortefeuilleWaardeInValuta'] - $data['beginPortefeuilleWaardeInValuta']) * $data['actueleValuta'] / $this->pdf->ValutaKoersEind;
	//$fondsResultaatprocent = ($fondsResultaat / $data['beginPortefeuilleWaardeEuro']) * 100;
	$valutaResultaat = $data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro'] - $fondsResultaat;

	$procentResultaat = (($data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro'] + $dividend['corrected']) / ($data['beginPortefeuilleWaardeEuro'] /100));
	if($data['beginPortefeuilleWaardeEuro'] < 0)
		$procentResultaat = -1 * $procentResultaat;
	///

	$categorieTotaal[$data['categorieOmschrijving']]['fondsResultaat'] +=$fondsResultaat;
	$categorieTotaal[$data['categorieOmschrijving']]['valutaResultaat'] +=$valutaResultaat;
	$categorieTotaal[$data['categorieOmschrijving']]['dividend'] +=$dividend['totaal'];
	$categorieTotaal[$data['categorieOmschrijving']]['dividendc'] +=$dividend['corrected'];

	$hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['fondsResultaat;'] +=$fondsResultaat;
	$hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['valutaResultaat'] +=$valutaResultaat;
	$hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['dividend'] +=$dividend['totaal'];
	$hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['dividendc'] +=$dividend['corrected'];



	if($procentResultaat > 1000 || $procentResultaat < -1000)
		$procentResultaattxt = "p.m.";
	else
		$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VOLK_decimaal_proc);

	if($fondsResultaat <> 0)
		$fondsResultaatTxt = $this->formatGetal($fondsResultaat,$this->pdf->rapport_VOLK_decimaal);

	if($valutaResultaat <> 0)
		$valutaResultaatTxt = $this->formatGetal($valutaResultaat,$this->pdf->rapport_VOLK_decimaal);

	if($valutaResultaat <> 0 || $fondsResultaat <> 0)
	  $ongerealiseerdResultaatTxt= $this->formatGetal($fondsResultaat+$valutaResultaat,$this->pdf->rapport_VOLK_decimaal);

	if($dividend['totaal'] <> 0)
		$dividendTxt = $this->formatGetal($dividend['totaal'],$this->pdf->rapport_VOLK_decimaal);

      if($data['koersLeeftijd'] > $maxDagenOud && $data['actueleFonds'] <> 0)
      {
			  $markering="*";
        $this->extraVoet=vertaalTekst('Koersen met een * zijn meer dan',$this->pdf->rapport_taal).' '.$maxDagenOud.' '.vertaalTekst('dagen oud.',$this->pdf->rapport_taal);
      }
      elseif($data['koersLeeftijd'] > 365 && $data['actueleFonds'] <> 0)
      {
			  $markering="**";
        $this->extraVoet2=vertaalTekst('Koersen met een ** zijn meer dan 365 dagen oud.',$this->pdf->rapport_taal);
			}
      else
			  $markering="";
}

					if($fill==true)
		      {
		        $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
		        $fill=false;
		      }
		      else
		      {
		        $this->pdf->fillCell=array();
		        $fill=true;
		      }

      $this->extraVoet();
      

    $lastHoofdcategorieOmschrijving=$data['hoofdcategorieOmschrijving'];
    $lastCategorieOmschrijving=$data['categorieOmschrijving'];
    
    
if($data['type']=='rente')
{
  if($renteKop==false)
  {
  	$this->printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde);
    $this->printKop('Opgelopen rente','B',false);
    $renteKop=true;
  }
	if($fill==false)
	$this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);

  		$percentageVanTotaal = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
			$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";
			$this->pdf->row(array("  ".$data['fondsOmschrijving'],'', //.' '.$rentePeriodetxt
					              $data['valuta'],'','','','',
												$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc),'','','','','',''));
}
else
{

	$percentageVanTotaal = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
			$this->pdf->row(array("  ".$data['fondsOmschrijving'],
				                $this->formatAantal($data['totaalAantal'],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
												$data['Valuta'],
												$this->formatGetal($data['beginwaardeLopendeJaar'],2),
												$this->formatGetal($data['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												"",
												$this->formatGetal($data['actueleFonds'],2).$markering,
												$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc),
												"",
												$fondsResultaatTxt,
												$valutaResultaatTxt,
				                $ongerealiseerdResultaatTxt,
												$dividendTxt,
												$procentResultaattxt
												 )	);//,$this->formatGetal($data['historischeWaarde'],2)
}
      
      
      $categorieTotaal[$data['categorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
      $categorieTotaal[$data['categorieOmschrijving']]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];
      $hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
      $hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];
      
      if($data['type']=='rente')
      {
        $categorieTotaal[$data['categorieOmschrijving']]['renteWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
        $hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['renteWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
      }


		}
    $this->extraVoet();
    if($renteKop==true)
    	$this->printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde,false,true);

    
	  	$this->printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde);


	  $query="SELECT TijdelijkeRapportage.rekening FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type = 'rekening'
	  AND TijdelijkeRapportage.rapportageDatum IN('".$this->rapportageDatumVanaf."','".$this->rapportageDatum."') AND TijdelijkeRapportage.rekening <> ''" .$__appvar['TijdelijkeRapportageMaakUniek'];
    $DB1 = new DB();
    $DB1->SQL($query);
    $DB1->Query();
    $aanwezigeRekeningen=array();
    while($data = $DB1->NextRecord())
    {
      $aanwezigeRekeningen[]=$data['rekening'];
    
    }

	  	
		// Liquiditeiten
		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.rekening as zoekRekening,
      Depotbanken.Omschrijving as depotbankOmschrijving, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro, ".
			" (SELECT actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage WHERE portefeuille = '".$this->portefeuille."' AND rapportageDatum = '".$this->rapportageDatumVanaf."' AND rekening = zoekRekening AND type='rekening'  LIMIT 1)  / ".$this->pdf->ValutaKoersBegin. " as beginPortefeuilleWaardeEuro,".
      " (SELECT actuelePortefeuilleWaardeInValuta FROM TijdelijkeRapportage WHERE portefeuille = '".$this->portefeuille."' AND rapportageDatum = '".$this->rapportageDatumVanaf."' AND rekening = zoekRekening AND type='rekening'  LIMIT 1) as beginPortefeuilleWaardeValuta,".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,
			Rekeningen.Deposito, Rekeningen.Termijnrekening, Rekeningen.Memoriaal".
			" FROM TijdelijkeRapportage
      JOIN Rekeningen on Rekeningen.rekening = TijdelijkeRapportage.rekening AND Rekeningen.consolidatie=0 AND TijdelijkeRapportage.portefeuille = '".$this->portefeuille."'
      LEFT JOIN Depotbanken on Rekeningen.Depotbank=Depotbanken.Depotbank
			WHERE ".
			" TijdelijkeRapportage.rekening IN('".implode("','",$aanwezigeRekeningen)."') AND ".
			" TijdelijkeRapportage.type = 'rekening'  ".
			" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY Depotbanken.Depotbank, TijdelijkeRapportage.rekening, TijdelijkeRapportage.valutaVolgorde";
		debugSpecial($query,__FILE__,__LINE__);


		$DB1->SQL($query);
		$DB1->Query();
    $rekeningen=array();
		if($DB1->records() > 0)
		{
			$totaalLiquiditeitenInValuta = 0;
			$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),"B");
			while($data = $DB1->NextRecord())
			{
			  $liqiteitenBuffer[] = $data;
			  $rekeningen[]=$data['zoekRekening'];
			}
			$query="SELECT TijdelijkeRapportage.fondsOmschrijving,
			TijdelijkeRapportage.rekening as zoekRekening,
			Depotbanken.Omschrijving as depotbankOmschrijving,
			TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta as beginPortefeuilleWaardeValuta,
			 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as beginPortefeuilleWaardeEuro
			 FROM TijdelijkeRapportage
			 JOIN Rekeningen on Rekeningen.rekening = TijdelijkeRapportage.rekening  AND Rekeningen.Portefeuille = TijdelijkeRapportage.portefeuille
			 LEFT JOIN Depotbanken on Rekeningen.Depotbank=Depotbanken.Depotbank
			 WHERE
			 TijdelijkeRapportage.rekening IN('".implode("','",$aanwezigeRekeningen)."') AND
			 TijdelijkeRapportage.type = 'rekening'
			 AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatumVanaf."' AND TijdelijkeRapportage.rekening NOT IN('".implode("','",$rekeningen)."')	 "
        .$__appvar['TijdelijkeRapportageMaakUniek'];
      $DB1->SQL($query);
      $DB1->Query();
      while($data = $DB1->NextRecord())
      {
        $liqiteitenBuffer[] = $data;
        $rekeningen[]=$data['zoekRekening'];
      }

			foreach($liqiteitenBuffer as $data)
			{
			  	if($fill==true)
		      {
		        $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
		        $fill=false;
		      }
		      else
		      {
		        $this->pdf->fillCell=array();
		         $fill=true;
		      }

					$percentageVanTotaal = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
          $this->extraVoet();
          $this->pdf->row(array('',
                        '',$data['Valutateken'],
                        $this->formatGetal($data['beginPortefeuilleWaardeValuta'],$this->pdf->rapport_VOLK_decimaal),
                        $this->formatGetal($data['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												'',
                        $this->formatGetal($data['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_VOLK_decimaal),
											  $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)
												));
          $this->pdf->SetXY($this->pdf->marge,$this->pdf->GetY()-$this->pdf->rowHeight);
			    $this->pdf->Cell('150',$this->pdf->rowHeight,"  ".vertaalTekst($data['fondsOmschrijving'],$this->pdf->rapport_taal).' '.$data['zoekRekening']." ".$data['depotbankOmschrijving'],0,1,'L');
					
      		$categorieTotaal["Liquiditeiten"]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
					$categorieTotaal["Liquiditeiten"]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];
			}
      $this->extraVoet();
			$this->printSubTotaal("Liquiditeiten",$categorieTotaal,$totaalWaarde,false);
		} // einde liquide
    $this->extraVoet();
		// check op totaalwaarde!
		$actueleWaardePortefeuille=0;
		foreach ($categorieTotaal as $categorie=>$waardes)
		{
		  $actueleWaardePortefeuille+=$waardes['actuelePortefeuilleWaardeEuro'];
		}
		global $__debug;
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0 )
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();

		}

		$this->pdf->CellBorders = array('','','','','','','','SUB','SUB');
    $this->extraVoet();
		$this->pdf->row(array(vertaalTekst("Totale actuele waarde vermogen",$this->pdf->rapport_taal),'','','','','','',$this->formatGetal($totaalWaarde,$this->pdf->rapport_VOLK_decimaal),
		$this->formatGetal(($actueleWaardePortefeuille/$totaalWaarde*100),$this->pdf->rapport_VOLK_decimaal_proc)));
    $this->pdf->CellBorders = array();
		$this->pdf->ln();
		$this->pdf->rowHeight=4;
    
    //$this->end->AddRestricties();
  
		if($this->pdf->rapport_VOLK_valutaoverzicht == 1)
		{
			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		}
		elseif($this->pdf->rapport_VOLK_valutaoverzicht == 2)
		{
			$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}

		if($this->pdf->rapport_VOLK_rendement == 1)
		{
			$this->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}
    $this->extraVoet(true);
    
    
    if ($this->pdf->lastPOST['doorkijk'] == 1)
    {
      $this->portefeuille=$this->portBackup;
    }
    
    
/*
$this->pdf->ln(8);
if($this->pdf->getY() > 160)
{
  $this->pdf->addPage();
  $this->pdf->ln(8);
}
		$this->pdf->MultiCell(145,4,"Om in grote lijnen te kunnen bepalen hoe de relatieve prestatie van uw beleggingsportefeuille is, kunt u deze vergelijken met bepaalde indices. Omdat uw portefeuille geen exacte afspiegeling vormt van één bepaalde index, geven wij u er een paar die daar zo veel mogelijk bij in de buurt komen. Het aandelengedeelte van uw portefeuille kunt u afzetten tegen de MSCI World Euro. Dit is een wereldwijde aandelenindex vertaald naar euro. Deze index bestaat uit 1728 aandelen uit 23 ontwikkelde landen. Voor het obligatiegedeelte kunt u de Citigroup Euro BIG Bond Index gebruiken. Dit is een wereldwijde index van (bedrijfs)obligaties vertaald naar euro. De AEX Index tenslotte is de Nederlandse index van 25 hoofdfondsen.", 1, "L");
*/

	}
  
  function extraVoet($force=false)
  {
    if(trim($this->extraVoet.$this->extraVoet2)<>'')
    {
    
      if($this->pdf->GetY()+$this->pdf->rowHeight*3>$this->pdf->PageBreakTrigger || $force)
      {
        if(!in_array($this->pdf->page,$this->extraVoetPages))
        {
        $x=$this->pdf->getX();
        $y=$this->pdf->GetY();
        $this->pdf->AutoPageBreak=false;
        $this->pdf->SetXY(0,197);
        $this->pdf->MultiCell(297,$this->pdf->rowHeight,trim($this->extraVoet.$this->extraVoet2),0,'C');
        $this->pdf->SetXY($x,$y);
        $this->pdf->AutoPageBreak=true;
        $this->extraVoetPages[]=$this->pdf->page;
        }
      }
    }    
  }

	function printRendement($portefeuille, $rapportageDatum, $rapportageDatumVanaf, $kort=false)
  {
  		global $__appvar;
		// vergelijk met begin Periode rapport.

		$DB= new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$rapportageDatumVanaf."' AND ".
						 " portefeuille = '".$portefeuille."' ".
						 $__appvar['TijdelijkeRapportageMaakUniek'];

		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$vergelijkWaarde = $DB->nextRecord();
		$vergelijkWaarde = $vergelijkWaarde[totaal] /  getValutaKoers($this->pdf->rapportageValuta,$rapportageDatumVanaf);

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$rapportageDatum."' AND ".
						 " portefeuille = '".$portefeuille."' ".
						 $__appvar['TijdelijkeRapportageMaakUniek'];
    	debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$actueleWaardePortefeuille = $DB->nextRecord();
		$actueleWaardePortefeuille = $actueleWaardePortefeuille[totaal]  / $this->pdf->ValutaKoersEind;

		$resultaat = ($actueleWaardePortefeuille -
									$vergelijkWaarde -
									getStortingen($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$this->pdf->rapportageValuta) +
									getOnttrekkingen($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$this->pdf->rapportageValuta)
									);

		$performance = performanceMeting($portefeuille, $rapportageDatumVanaf, $rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);

		$this->pdf->ln(2);

		if($kort)
			$min = 8;

		if(($this->pdf->GetY() + 22 - $min) >= $this->pdf->pagebreak) {
			$this->pdf->AddPage();
			$this->pdf->ln();
		}

		$this->pdf->SetFillColor(255,255,255);
		//$this->pdf->SetX($this->pdf->marge + $this->pdf->widthB[0]);
		$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),110,(16-$min),'F');
		$this->pdf->SetFillColor(0);
		$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),110,(16-$min));
		$this->pdf->ln(2);
		//$this->pdf->SetX($this->pdf->marge);
		$this->pdf->SetX($this->pdf->marge);

		// kopfontcolor
		if(!$kort)
		{
			$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[r],$this->pdf->rapport_kop_fontcolor[g],$this->pdf->rapport_kop_fontcolor[b]);
			$this->pdf->Cell(80,4, vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal), 0,0, "L");
			$this->pdf->Cell(30,4, $this->pdf->formatGetal($resultaat,2), 0,1, "R");
			$this->pdf->ln();
		}
		$this->pdf->SetX($this->pdf->marge);
		if ($this->pdf->rapport_rendementText)
		  $this->pdf->Cell(80,4, vertaalTekst($this->pdf->rapport_rendementText,$this->pdf->rapport_taal), 0,0, "L");
		else
		  $this->pdf->Cell(80,4, vertaalTekst("Rendement lopende kalenderjaar",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,4, $this->pdf->formatGetal($performance,2)."%", 0,1, "R");
		$this->pdf->ln(2);
  }

  function printAEXVergelijking($vermogensbeheerder, $rapportageDatumVanaf, $rapportageDatum)
	{
	  $query = "SELECT Indices.Beursindex, Fondsen.Omschrijving, Fondsen.Valuta FROM Indices, Fondsen WHERE Indices.Beursindex = Fondsen.Fonds AND Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' ORDER BY Afdrukvolgorde";
    $border=0;
		$DB  = new DB();
		$DB2 = new DB();

		$DB->SQL($query);
		$DB->Query();
		$regels = $DB->records();
		$hoogte = ($regels * 4) + 8;
		if(($this->pdf->GetY() + $hoogte) > $this->pdf->pagebreak)
		{
			$this->pdf->AddPage();
			$this->pdf->ln();
		}

		$perfEur = 0;
		$perfVal = 1;
		$perfJan = 0;

		if($this->pdf->rapport_perfIndexJanuari == true)
	  {
	    $julRapDatumVanaf = db2jul($rapportageDatumVanaf);
	    $rapJaar = date('Y',$julRapDatumVanaf);
	    $dagMaand = date('d-m',$julRapDatumVanaf);
	    $januariDatum = $rapJaar.'-01-01';
	    	    if($dagMaand =='01-01')
        $this->pdf->rapport_perfIndexJanuari = false;
	  }
		if($this->pdf->rapport_printAEXVergelijkingEur == 1)
		{
		  $extraX = 26;
		  $perfEur = 1;
		  $perfVal = 0;
		  $perfJan = 0;
		}
		if($this->pdf->rapport_perfIndexJanuari == true)
	  {
		  $perfEur = 0;
		  $perfVal = 0;
		  $perfJan = 1;
	  }

	  if($this->pdf->printAEXVergelijkingProcentTeken)
	    $teken = '%';
	  else
	    $teken = '';


		if($this->pdf->rapport_perfIndexJanuari == true)
		  $extraX += 51;

		$this->pdf->ln();
		$this->pdf->SetFillColor(255,255,255);
		$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),110+9+$extraX,$hoogte,'F');
		$this->pdf->SetFillColor(0);
		$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),110+9+$extraX,$hoogte);
		$this->pdf->SetX($this->pdf->marge);

		// kopfontcolor
		//$this->pdf->SetTextColor($this->pdf->rapport_kop4_fontcolor[r],$this->pdf->rapport_kop4_fontcolor[g],$this->pdf->rapport_kop4_fontcolor[b]);
		$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[r],$this->pdf->rapport_kop_fontcolor[g],$this->pdf->rapport_kop_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_kop4_font,$this->pdf->rapport_kop4_fontstyle,$this->pdf->rapport_kop4_fontsize);
		$this->pdf->Cell(40,4, vertaalTekst("Index-vergelijking",$this->pdf->rapport_taal), 0,0, "L");

		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
		//$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
		$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[r],$this->pdf->rapport_kop_fontcolor[g],$this->pdf->rapport_kop_fontcolor[b]);
		if($this->pdf->rapport_perfIndexJanuari == true)
			$this->pdf->Cell(26,4, date("d-m-Y",db2jul($januariDatum)), $border,0, "R");
		$this->pdf->Cell(26,4, date("d-m-Y",db2jul($rapportageDatumVanaf)), $border,0, "R");
		$this->pdf->Cell(26,4, date("d-m-Y",db2jul($rapportageDatum)), $border,0, "R");
		$this->pdf->Cell(26,4, vertaalTekst("Performance in %",$this->pdf->rapport_taal), $border,$perfVal, "R");
		if($this->pdf->rapport_printAEXVergelijkingEur == 1)
		  $this->pdf->Cell(26,4, vertaalTekst("Perf in % in EUR",$this->pdf->rapport_taal), $border,$perfEur, "R");
		if($this->pdf->rapport_perfIndexJanuari == true)
			$this->pdf->Cell(26,4, vertaalTekst("Jaar Perf.",$this->pdf->rapport_taal), $border,$perfJan, "R");

		while($perf = $DB->nextRecord())
		{
		  if($perf['Valuta'] != 'EUR')
		  {
		    if($this->pdf->rapport_perfIndexJanuari == true)
		    {
		      $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$januariDatum."' ORDER BY Datum DESC LIMIT 1 ";
		      $DB2->SQL($q);
			    $DB2->Query();
			    $valutaKoersJan = $DB2->LookupRecord();
			  }

		    $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$rapportageDatumVanaf."' ORDER BY Datum DESC LIMIT 1 ";
		    $DB2->SQL($q);
			  $DB2->Query();
			  $valutaKoersStart = $DB2->LookupRecord();

		    $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$rapportageDatum."' ORDER BY Datum DESC LIMIT 1 ";
		    $DB2->SQL($q);
			  $DB2->Query();
			  $valutaKoersStop = $DB2->LookupRecord();

		  }
		  else
		  {
		    $valutaKoersJan['Koers'] = 1;
		    $valutaKoersStart['Koers'] = 1;
		    $valutaKoersStop['Koers'] = 1;
		  }

		  if($this->pdf->rapport_perfIndexJanuari == true)
		  {
		    $q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$januariDatum."' AND Fonds = '".$perf[Beursindex]."'  ORDER BY Datum DESC LIMIT 1";
		  	$DB2->SQL($q);
		  	$DB2->Query();
		  	$koers0 = $DB2->LookupRecord();
		  }

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatumVanaf."' AND Fonds = '".$perf[Beursindex]."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers1 = $DB2->LookupRecord();

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatum."' AND Fonds = '".$perf[Beursindex]."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers2 = $DB2->LookupRecord();

			$performanceJaar = ($koers2['Koers'] - $koers0['Koers']) / ($koers0['Koers']/100 );
			$performance = ($koers2['Koers'] - $koers1['Koers']) / ($koers1['Koers']/100 );
			$performanceEur = ($koers2['Koers']*$valutaKoersStop['Koers'] - $koers1['Koers']*$valutaKoersStart['Koers']) / ($koers1['Koers']*$valutaKoersStart['Koers']/100 );
      //echo $perf[Omschrijving]." $performanceEur = (.".$koers2['Koers']."*".$valutaKoersStop['Koers']." - ".$koers1['Koers']."*".$valutaKoersStart['Koers'].") / (".$koers1['Koers']."*".$valutaKoersStart['Koers']."/100 );<br>";
			$this->pdf->Cell(40,4, $perf[Omschrijving], $border,0, "L");
		  if($this->pdf->rapport_perfIndexJanuari == true)
		     $this->pdf->Cell(26,4, $this->pdf->formatGetal($koers0[Koers],2), $border,0, "R");
			$this->pdf->Cell(26,4, $this->pdf->formatGetal($koers1[Koers],2), $border,0, "R");
			$this->pdf->Cell(26,4, $this->pdf->formatGetal($koers2[Koers],2), $border,0, "R");
		  $this->pdf->Cell(26,4, $this->pdf->formatGetal($performance,2).$teken, $border,$perfVal, "R");
		  if($this->pdf->rapport_printAEXVergelijkingEur == 1)
		    $this->pdf->Cell(26,4, $this->pdf->formatGetal($performanceEur,2).$teken, $border,$perfEur, "R");
		  if($this->pdf->rapport_perfIndexJanuari == true)
		    $this->pdf->Cell(26,4, $this->pdf->formatGetal($performanceJaar,2).$teken, $border,$perfJan, "R");
		}

		$query2 = "SELECT Portefeuilles.SpecifiekeIndex, Fondsen.Omschrijving, Fondsen.Valuta FROM Portefeuilles, Fondsen WHERE Portefeuilles.SpecifiekeIndex = Fondsen.Fonds AND Portefeuilles.Portefeuille = '". $this->pdf->rapport_portefeuille."' ";
		$DB->SQL($query2);
		$DB->Query();

		while($perf = $DB->nextRecord())
		{

		  if($perf['Valuta'] != 'EUR')
		  {

		    if($this->pdf->rapport_perfIndexJanuari == true)
		    {
		      $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$januariDatum."' ORDER BY Datum DESC LIMIT 1 ";
		      $DB2->SQL($q);
			    $DB2->Query();
			    $valutaKoersJan = $DB2->LookupRecord();
		    }

		    $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$rapportageDatumVanaf."' ORDER BY Datum DESC LIMIT 1 ";
		    $DB2->SQL($q);
			  $DB2->Query();
			  $valutaKoersStart = $DB2->LookupRecord();

		    $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$rapportageDatum."' ORDER BY Datum DESC LIMIT 1 ";
		    $DB2->SQL($q);
			  $DB2->Query();
			  $valutaKoersStop = $DB2->LookupRecord();

		  }
		  else
		  {
		    $valutaKoersJan['Koers'] = 1;
		    $valutaKoersStart['Koers'] = 1;
		    $valutaKoersStop['Koers'] = 1;
		  }

		  	if($this->pdf->rapport_perfIndexJanuari == true)
		    {
		  	  $q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$januariDatum."' AND Fonds = '".$perf[SpecifiekeIndex]."'  ORDER BY Datum DESC LIMIT 1";
			    $DB2->SQL($q);
			    $DB2->Query();
			    $koers0 = $DB2->LookupRecord();
		    }

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatumVanaf."' AND Fonds = '".$perf[SpecifiekeIndex]."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers1 = $DB2->LookupRecord();

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatum."' AND Fonds = '".$perf[SpecifiekeIndex]."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers2 = $DB2->LookupRecord();

			$performanceJaar = ($koers2['Koers'] - $koers0['Koers']) / ($koers0['Koers']/100 );
			$performance = ($koers2['Koers'] - $koers1['Koers']) / ($koers1['Koers']/100 );
			$performanceEur = ($koers2['Koers']*$valutaKoersStop['Koers'] - $koers1['Koers']*$valutaKoersStart['Koers']) / ($koers1['Koers']*$valutaKoersStart['Koers']/100 );
      //echo $perf[Omschrijving]." $performanceEur = (.".$koers2['Koers']."*".$valutaKoersStop['Koers']." - ".$koers1['Koers']."*".$valutaKoersStart['Koers'].") / (".$koers1['Koers']."*".$valutaKoersStart['Koers']."/100 );<br>";


			$this->pdf->Cell(40,4, $perf[Omschrijving], 0,0, "L");
			if($this->pdf->rapport_perfIndexJanuari == true)
		     $this->pdf->Cell(26,4, $this->pdf->formatGetal($koers0[Koers],2), $border,0, "R");
			$this->pdf->Cell(26,4, $this->pdf->formatGetal($koers1[Koers],2), $border,0, "R");
			$this->pdf->Cell(26,4, $this->pdf->formatGetal($koers2[Koers],2), $border,0, "R");
		  $this->pdf->Cell(26,4, $this->pdf->formatGetal($performance,2).$teken, $border,$perfVal, "R");
		  if($this->pdf->rapport_printAEXVergelijkingEur == 1)
		    $this->pdf->Cell(26,4, $this->pdf->formatGetal($performanceEur,2).$teken, $border,$perfEur, "R");
		  if($this->pdf->rapport_perfIndexJanuari == true)
		    $this->pdf->Cell(26,4, $this->pdf->formatGetal($performanceJaar,2).$teken, $border,$perfJan, "R");
		}
	}
}
?>

<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2015/12/19 08:29:17 $
File Versie					: $Revision: 1.2 $

$Log: RapportVOLK_L26_.php,v $
Revision 1.2  2015/12/19 08:29:17  rvv
*** empty log message ***

Revision 1.1  2010/05/30 12:46:25  rvv
*** empty log message ***

Revision 1.2  2010/05/23 14:01:46  rvv
*** empty log message ***

Revision 1.1  2010/05/22 12:56:24  rvv
*** empty log message ***

*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVOLK_L26
{
	function RapportVOLK_L26($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
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
	}

	function formatGetal($waarde, $dec,$procent=false)
	{
	  if($waarde==0)
	    return;
		$data=number_format($waarde,$dec,",",".");
		if($procent==true)
		  $data.="%";
		return $data;
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
	  return number_format($waarde,$dec,",",".");
  }

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	  if($waarde==0)
	    return;
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



	function printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde,$resultaat=true,$style='B')
	{
    $this->pdf->SetFont($this->pdf->rapport_font,$style,$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('','','','','','SUB','','','SUB','SUB','','SUB','SUB','SUB','SUB');
    $this->pdf->Cell(40,4,"Subtotaal ".$lastCategorieOmschrijving,0,'L');
    $this->pdf->setX($this->pdf->marge);

    $merged=$this->arrayMerge($categorieTotaal['begin'],$categorieTotaal['eind']);
    $waardeVeranderingEuro  = $categorieTotaal['eind']['actuelePortefeuilleWaardeEuro'] - $categorieTotaal['begin']['actuelePortefeuilleWaardeEuro'];
    $waardeVeranderingInValuta  = $categorieTotaal['eind']['actuelePortefeuilleWaardeInValuta'] - $categorieTotaal['begin']['actuelePortefeuilleWaardeInValuta'];
    $gewogenResultaat = $this->fondsPerformance2($merged['fonds'],$this->rapportageDatumVanaf,$this->rapportageDatum);
    $fondsResultaat=  ($waardeVeranderingInValuta * $merged['actueleValuta']) + $gewogenResultaat['stort'];
    $valutaResultaat= $waardeVeranderingEuro + $gewogenResultaat['stort'] - $fondsResultaat;

     $fondsResultaat   =    $categorieTotaal['eind']['fondsResultaat'];
      $valutaResultaat =  $categorieTotaal['eind']['valutaResultaat'];


 	  $this->pdf->row(array('','','','','',
												$this->formatGetal($categorieTotaal['begin']['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												'','',
												$this->formatGetal($categorieTotaal['eind']['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($categorieTotaal['eind']['aandeelInTotaal'],2)."%",
												"",$this->formatGetal($fondsResultaat,0),
												$this->formatGetal($valutaResultaat,0),
												$this->formatGetal($fondsResultaat+$valutaResultaat,0),
                        $this->formatGetal($gewogenResultaat['procent'],2)."%"));

    $this->pdf->CellBorders = array();
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}

	function printKop($title, $type='',$ln=false)
	{
		if($ln)
	    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,$type,$this->pdf->rapport_fontsize);
    $this->pdf->Cell(40,4,$title,0,1,'L');

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}

	function arraySum($a,$b,$skipFields=false)
	{
	  if(is_array($a))
	    $c=$a;
	  else
	    return $b;
	  foreach ($b as $key=>$value)
	  {
	    if($skipFields)
	    {
	      if(in_array($key,$skipFields))
	        continue;
	    }
	    if(isset($a[$key]))
	    {
	      if(is_numeric($value) && is_numeric($a[$key]))
	        $c[$key]=$a[$key] + $value;
	      else
	      {
	          if(!is_array($c[$key]))
	            $c[$key]=array($c[$key]);
	          $c[$key][]=$value;
	      }
	    }
	  }
	  return $c;
	}
	function arrayMerge($a,$b)
	{
	  $c=$a;
	  foreach ($b as $key=>$value)
	    if($value!='')
	      $c[$key]=$value;
    return $c;
	}

	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();

		$this->pdf->AddPage();

		// haal totaalwaarde op om % te berekenen
				$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						  .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde['begin'] = $totaalWaarde['totaal'];

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						  .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaal = $DB->nextRecord();
		$totaalWaarde['eind'] = $totaal['totaal'];

		$DB = new DB();
		$perioden=array('begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);
		foreach ($perioden as $periode=>$datum)
		{
			$query="SELECT TijdelijkeRapportage.fondsOmschrijving,
				 TijdelijkeRapportage.actueleValuta, ".
				" TijdelijkeRapportage.Valuta, ".
				" TijdelijkeRapportage.totaalAantal, ".
				" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				 TijdelijkeRapportage.actueleFonds,
         Beleggingscategorien.Omschrijving as categorieOmschrijving,
         Valutas.Omschrijving AS ValutaOmschrijving,
         TijdelijkeRapportage.actueleValuta,
         if(TijdelijkeRapportage.`type`='rente','Rente',TijdelijkeRapportage.beleggingscategorie ) AS beleggingscategorie,
         CategorienPerHoofdcategorie.Hoofdcategorie,
         HoofdBeleggingscategorien.Omschrijving AS hoofdcategorieOmschrijving,
         TijdelijkeRapportage.fonds,
         TijdelijkeRapportage.rekening,
         TijdelijkeRapportage.Type,

         Regios.Omschrijving as regioOmschrijving,
         Beleggingscategorien.Omschrijving as beleggingscategorieOmschrijving
         FROM TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta)
         LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie)
         LEFT Join CategorienPerHoofdcategorie ON TijdelijkeRapportage.beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
         LEFT Join Beleggingscategorien as HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie
         LEFT Join Regios ON TijdelijkeRapportage.Regio = Regios.Regio
         WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum = '".$datum."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
         ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde,Regios.Afdrukvolgorde , Beleggingscategorien.Afdrukvolgorde asc, TijdelijkeRapportage.fonds, TijdelijkeRapportage.Type";
		  $DB->SQL($query);
		  $DB->Query();
		  while($data = $DB->NextRecord())
		  {
		    foreach ($data as $key=>$value)
		      if($value=='')
				    if($data['Type']=='rekening' && $key=='fonds')
		          $data[$key]=$data['rekening'];
		        else
		          $data[$key]='Geen';
 		    if($data['fondsOmschrijving']=='Effectenrekening ')
          $data['fondsOmschrijving'] .= $data['rekening'];

		    $data['aandeelInTotaal'] =	$data['actuelePortefeuilleWaardeEuro'] / $totaalWaarde[$periode] *100;

        if(is_array($waarden[$data['hoofdcategorieOmschrijving']][$data['regioOmschrijving']][$data['beleggingscategorieOmschrijving']][$data['fondsOmschrijving']][$periode]))
          $waarden[$data['hoofdcategorieOmschrijving']][$data['regioOmschrijving']][$data['beleggingscategorieOmschrijving']][$data['fondsOmschrijving']][$periode]=$this->arraySum($waarden[$data['hoofdcategorieOmschrijving']][$data['regioOmschrijving']][$data['beleggingscategorieOmschrijving']][$data['fondsOmschrijving']][$periode],$data,array('fondsOmschrijving','totaalAantal','actueleValuta','categorieOmschrijving','Valuta'));
        else
		      $waarden[$data['hoofdcategorieOmschrijving']][$data['regioOmschrijving']][$data['beleggingscategorieOmschrijving']][$data['fondsOmschrijving']][$periode]=$data;

  	    $totalen[$periode]=$this->arraySum($totalen[$periode],$data);
		    $hoofdcategorieTotalen[$data['hoofdcategorieOmschrijving']][$periode]=$this->arraySum($hoofdcategorieTotalen[$data['hoofdcategorieOmschrijving']][$periode],$data);
		    $regioTotalen[$data['hoofdcategorieOmschrijving']][$data['regioOmschrijving']][$periode]=$this->arraySum($regioTotalen[$data['hoofdcategorieOmschrijving']][$data['regioOmschrijving']][$periode],$data);
		    $categorieTotalen[$data['hoofdcategorieOmschrijving']][$data['regioOmschrijving']][$data['beleggingscategorieOmschrijving']][$periode]=$this->arraySum($categorieTotalen[$data['hoofdcategorieOmschrijving']][$data['regioOmschrijving']][$data['beleggingscategorieOmschrijving']][$periode],$data);
		  }
		}


foreach ($waarden as $hoofdcategorie=>$regioData)
{
  foreach ($regioData as $regio=>$categorieData)
  {
    foreach ($categorieData as $categorie=>$regelData)
    {
      foreach ($regelData as $omschrijving=>$data)
      {

        $merged=$this->arrayMerge($data['begin'],$data['eind']);
        $waardeVeranderingEuro  = $data['eind']['actuelePortefeuilleWaardeEuro'] - $data['begin']['actuelePortefeuilleWaardeEuro'];
        $waardeVeranderingInValuta  = $data['eind']['actuelePortefeuilleWaardeInValuta'] - $data['begin']['actuelePortefeuilleWaardeInValuta'];
        $gewogenResultaat = $this->fondsPerformance2($merged['fonds'],$perioden['begin'],$perioden['eind']);
        $fondsResultaat=  ($waardeVeranderingInValuta * $merged['actueleValuta']) + $gewogenResultaat['stort'];
        $valutaResultaat= $waardeVeranderingEuro + $gewogenResultaat['stort'] - $fondsResultaat;

        $waarden[$hoofdcategorie][$regio][$categorie][$omschrijving]['eind']['fondsResultaat']=$fondsResultaat;
        $waarden[$hoofdcategorie][$regio][$categorie][$omschrijving]['eind']['valutaResultaat']=$valutaResultaat;
        $waarden[$hoofdcategorie][$regio][$categorie][$omschrijving]['eind']['gewogenResultaat']=$gewogenResultaat;

        $totalen['eind']['fondsResultaat']+=$fondsResultaat;
        $totalen['eind']['valutaResultaat']+=$valutaResultaat;
        $hoofdcategorieTotalen[$hoofdcategorie]['eind']['fondsResultaat']+=$fondsResultaat;
        $hoofdcategorieTotalen[$hoofdcategorie]['eind']['valutaResultaat']+=$valutaResultaat;
        $regioTotalen[$hoofdcategorie][$regio]['eind']['fondsResultaat']+=$fondsResultaat;
        $regioTotalen[$hoofdcategorie][$regio]['eind']['valutaResultaat']+=$valutaResultaat;
      }
    }
  }
}

		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->Cell(40,4,'Totale portefeuille',0,'L');
		$this->pdf->setX($this->pdf->marge);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$merged=$this->arrayMerge($totalen['begin'],$totalen['eind']);
		$gewogenResultaat = $this->fondsPerformance2($merged['fonds'],$this->rapportageDatumVanaf,$this->rapportageDatum);
		$this->pdf->row(array('','','','','',$this->formatGetal($totaalWaarde['begin'],0),'','',$this->formatGetal($totaalWaarde['eind'],0),$this->formatGetal($totalen['eind']['aandeelInTotaal'],2)."%",'',
		$this->formatGetal($totalen['eind']['fondsResultaat'],0),$this->formatGetal($totalen['eind']['valutaResultaat'],0),$this->formatGetal($totalen['eind']['fondsResultaat']+$totalen['eind']['valutaResultaat'],0),
		$this->formatGetal($gewogenResultaat['procent'],2)."%"));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		foreach ($hoofdcategorieTotalen as $hoofCategorie=>$data)
		{
	    $merged=$this->arrayMerge($data['begin'],$data['eind']);
      $gewogenResultaat = $this->fondsPerformance2($merged['fonds'],$this->rapportageDatumVanaf,$this->rapportageDatum);

		  $this->pdf->row(array('','waarvan',substr($hoofCategorie,0,15),'','',$this->formatGetal($data['begin']['actuelePortefeuilleWaardeEuro'],0),'','',$this->formatGetal($data['eind']['actuelePortefeuilleWaardeEuro'],0),$this->formatGetal($data['eind']['aandeelInTotaal'],2)."%",'',
		  $this->formatGetal($data['eind']['fondsResultaat'],0),$this->formatGetal($data['eind']['valutaResultaat'],0),$this->formatGetal($data['eind']['fondsResultaat']+$data['eind']['valutaResultaat'],0),
		  $this->formatGetal($gewogenResultaat['procent'],2)."%"));
		}

	//	listarray($waarden);
foreach ($waarden as $hoofdcategorie=>$regioData)
{
  $this->printKop($hoofdcategorie,'BI',true);
  foreach ($regioData as $regio=>$categorieData)
  {
    $this->printKop($regio,'B',true);
    foreach ($categorieData as $categorie=>$regelData)
    {
      foreach ($regelData as $omschrijving=>$data)
      {
        $merged=$this->arrayMerge($data['begin'],$data['eind']);

       	$this->pdf->row(array($this->formatAantal($data['eind']['totaalAantal'],$this->pdf->rapport_VOLK_aantal_decimaal,false),
												substr($merged['beleggingscategorieOmschrijving'],0,12),
												substr($merged['fondsOmschrijving'],0,15),
												$merged['Valuta'],
												$this->formatGetal($data['begin']['actueleFonds'],2),
												$this->formatGetal($data['begin']['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												'',
												$this->formatGetal($data['eind']['actueleFonds'],2),
												$this->formatGetal($data['eind']['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($data['eind']['aandeelInTotaal'],2,true),
												"",$this->formatGetal($data['eind']['fondsResultaat'],0),
												$this->formatGetal($data['eind']['valutaResultaat'],0),
												$this->formatGetal($data['eind']['valutaResultaat']+$data['eind']['fondsResultaat'],0),
                        $this->formatGetal($data['eind']['gewogenResultaat']['procent'],2)."%"	));


      }
    }
    $this->printSubTotaal($regio,$regioTotalen[$hoofdcategorie][$regio],$totaalWaarde);
  }
  $this->printSubTotaal($hoofdcategorie,$hoofdcategorieTotalen[$hoofdcategorie],$totaalWaarde,true,'BI');
}




		// check op totaalwaarde!
		$actueleWaardePortefeuille=0;
		foreach ($categorieTotaal as $categorie=>$waardes)
		{
		  $actueleWaardePortefeuille+=$waardes['actuelePortefeuilleWaardeEuro'];
		}
		/*
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}
		*/
/*
		$this->pdf->CellBorders = array('','','','','SUB','SUB');
		$this->pdf->row(array("Totale actuele waarde portefeuille",'','','',$this->formatGetal($totaalWaarde,$this->pdf->rapport_VOLK_decimaal),
		$this->formatGetal(($actueleWaardePortefeuille/$totaalWaarde*100),$this->pdf->rapport_VOLK_decimaal_proc)));
    $this->pdf->CellBorders = array();
		$this->pdf->ln();
*/
		if($this->pdf->rapport_VOLK_valutaoverzicht == 1)
		{
			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		}
		elseif($this->pdf->rapport_VOLK_valutaoverzicht == 2)
		{
			$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}

		if($this->pdf->rapport_VOLK_rendement == 1)
			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		elseif ($this->pdf->rapport_VOLK_rendement == 2)
		  $this->pdf->printSamenstellingResultaat($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf) ;

		if($this->pdf->portefeuilledata[AEXVergelijking] > 0 ) //|| $this->pdf->rapport_layout == 8 voor L8 de index er weer uitgehaald.
		{
		  if(!$this->pdf->rapport_VOLK_geenIndex)
			  $this->pdf->printAEXVergelijking($this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);
		}
	}


	function genereerMutatieLijst($rapportageDatumVanaf,$rapportageDatum)
	{
	  	// loopje over Grootboekrekeningen Opbrengsten = 1
		$query = "SELECT Fondsen.Omschrijving, ".
		"Fondsen.Fondseenheid, ".
		"Rekeningmutaties.Boekdatum, ".
		"Rekeningmutaties.Transactietype,
		Rekeningmutaties.Valuta,
		Rekeningmutaties.Fonds,
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
		"Rekeningmutaties.Boekdatum > '$rapportageDatumVanaf' AND ".
		"Rekeningmutaties.Boekdatum <= '$rapportageDatum' ".
		"ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		// haal koersresultaat op om % te berekenen


		$buffer = array();
		$sortBuffer = array();

		while($mutaties = $DB->nextRecord())
		{
			$buffer[] = $mutaties;
		}

	  foreach ($buffer as $mutaties)
		{
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


			switch($mutaties[Transactietype])
			{
					case "A" :
						// Aankoop
						$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties[Debet]);
						$t_aankoop_koers					= $mutaties[Fondskoers];

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
						$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties[Debet]);
						$t_aankoop_koers					= $mutaties[Fondskoers];

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
						$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties[Debet]);
						$t_aankoop_koers					= $mutaties[Fondskoers];

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
						$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties[Debet]);
						$t_aankoop_koers					= $mutaties[Fondskoers];

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
						$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties[Credit]);
						$t_verkoop_koers					= $mutaties[Fondskoers];

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
						$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties[Credit]);
						$t_verkoop_koers					= $mutaties[Fondskoers];

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
						$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties[Credit]);
						$t_verkoop_koers					= $mutaties[Fondskoers];

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
						$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties[Credit]);
						$t_verkoop_koers					= $mutaties[Fondskoers];

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

				$historie = berekenHistorischKostprijs($this->portefeuille, $mutaties[Fonds], $mutaties[Boekdatum],$this->pdf->rapportageValuta);

				if($mutaties['Transactietype'] == "A/S")
				{
					$historischekostprijs  = ($mutaties[Aantal] * -1) * $historie[historischeWaarde]      * $historie[historischeValutakoers]        * $mutaties[Fondseenheid];
					$beginditjaar          = ($mutaties[Aantal] * -1) * $historie[beginwaardeLopendeJaar] * $historie[beginwaardeValutaLopendeJaar]  * $mutaties[Fondseenheid];
				}
				else
				{
					$historischekostprijs = $mutaties[Aantal]        * $historie[historischeWaarde]       * $historie[historischeValutakoers]        * $mutaties[Fondseenheid];
				  $beginditjaar         = $mutaties[Aantal]        * $historie[beginwaardeLopendeJaar]  * $historie[beginwaardeValutaLopendeJaar]  * $mutaties[Fondseenheid];
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
			}

	//	listarray($mutaties);
				$data[$mutaties['Fonds']][0]+=$aankoop_waarde-$verkoop_waarde;
				$data[$mutaties['Fonds']][1].=' '.$mutaties['Transactietype'];
				if($mutaties['Credit'])
				  $data[$mutaties['Fonds']][2]+=$mutaties['Aantal'];
				else
			  	$data[$mutaties['Fonds']][2]+=$mutaties['Aantal'];
				$data[$mutaties['Fonds']][3]+=$aankoop_waarde;
				$data[$mutaties['Fonds']][4]+=$verkoop_waarde;


		}
		return $data;
	}

	function getRekeningMutaties($rekening,$van,$tot)
	{
	  $db= new DB();
	  $query = "
	  SELECT
  SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)  as totaal
 	FROM
	Rekeningmutaties ,  Rekeningen

	WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	Rekeningen.Rekening =  '$rekening'  AND
 	Rekeningmutaties.Verwerkt = '1' AND
	Rekeningmutaties.Boekdatum > '$van' AND
	Rekeningmutaties.Boekdatum <= '$tot'";

	  $db->SQL($query);
	  $db->Query();
	  $data = $db->nextRecord();
return $data['totaal'];
	}



		function fondsKostenOpbrengsten($fonds,$datumBegin,$datumEind)
		{
		  $DB=new DB();
		  $query = "SELECT
      Sum((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaalWaarde
      FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
      JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
      WHERE
      (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
      Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
      Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
      Rekeningmutaties.Boekdatum <= '$datumEind' AND
      Rekeningmutaties.Fonds = '$fonds'";
      $DB->SQL($query); //echo "$fonds $query  <br>\n";
      $DB->Query();
      $totaalWaarde = $DB->NextRecord();

		  return $totaalWaarde['totaalWaarde'];
		}


	function fondsPerformance2($fonds,$datumBegin,$datumEind,$debug=false)
  {
    global $__appvar;
	  $DB=new DB();
	  $datum=$this->getKwartalen(db2jul($datumBegin),db2jul($datumEind));
    $totaalPerf = 100;
    foreach ($datum as $periode)
    {
	    $datumBegin = $periode['start'];
	    $datumEind = $periode['stop'];

      $fondsQuery = 'Fonds';

      if(is_array($fonds))
	      $fondsenWhere = " IN('".implode('\',\'',$fonds)."') ";
      else
        $fondsenWhere = " IN('$fonds') ";

      $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro
               FROM TijdelijkeRapportage
               WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumBegin' AND
               (TijdelijkeRapportage.rekening $fondsenWhere OR TijdelijkeRapportage.fonds $fondsenWhere )".$__appvar['TijdelijkeRapportageMaakUniek'];
	     $DB->SQL($query);
	     $DB->Query();
	     $start = $DB->NextRecord();
	     $beginwaarde = $start['actuelePortefeuilleWaardeEuro'];


       $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro
                FROM TijdelijkeRapportage
                WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumEind' AND
               (TijdelijkeRapportage.rekening $fondsenWhere OR TijdelijkeRapportage.fonds $fondsenWhere ) ".$__appvar['TijdelijkeRapportageMaakUniek'] ;
	     $DB->SQL($query);
	     $DB->Query();
	     $eind = $DB->NextRecord();
	     $eindwaarde = $eind['actuelePortefeuilleWaardeEuro'];

	    if($beginwaarde == 0)
	    {
	      $query = "SELECT Rekeningmutaties.Boekdatum - INTERVAL 1 DAY as Boekdatum FROM  (Rekeningen, Portefeuilles)
	                LEFT JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening  WHERE Rekeningmutaties.$fondsQuery $fondsenWhere AND
	                Rekeningen.Portefeuille = '".$this->portefeuille."' AND	Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
	                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' ORDER BY Rekeningmutaties.Boekdatum asc LIMIT 1 ";
	      $DB->SQL($query);
	      $DB->Query();
	      $start = $DB->NextRecord();
	      if($start['Boekdatum'] != '')
	        $datumBegin = $start['Boekdatum'];
	    }

 	    if($eindwaarde == 0)
 	    {
 	      $query = "SELECT Rekeningmutaties.Boekdatum + INTERVAL 1 DAY as Boekdatum FROM  (Rekeningen, Portefeuilles)
	                LEFT JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening  WHERE Rekeningmutaties.$fondsQuery $fondsenWhere AND
	                Rekeningen.Portefeuille = '".$this->portefeuille."' AND	Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
	                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' ORDER BY Rekeningmutaties.Boekdatum desc LIMIT 1 ";
	      $DB->SQL($query);
	      $DB->Query();
	      $eind = $DB->NextRecord();
	      if($eind['Boekdatum'] != '')
	        $datumEind = $eind['Boekdatum'];
 	    }


	     $queryAttributieStortingenOntrekkingenRekening = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$datumBegin."'))  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) )))*-1 AS gewogen, ".
	              "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))*-1  AS totaal ".
	              "FROM  Rekeningmutaties JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	               WHERE (Grootboekrekeningen.Opbrengst=0 AND Grootboekrekeningen.Kosten =0) AND ".
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               Rekeningmutaties.Rekening $fondsenWhere ";
	     $DB->SQL($queryAttributieStortingenOntrekkingenRekening);
	     $DB->Query();
	     $AttributieStortingenOntrekkingenRekening = $DB->NextRecord();


       $queryRekeningDirecteKostenOpbrengsten = "SELECT
                 SUM(((TO_DAYS('$datumEind') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('$datumEind') - TO_DAYS('$datumBegin')) * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) AS gewogen,
                 SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaal
                 FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                 JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
                 WHERE
                 (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND   Rekeningmutaties.Fonds = '' AND
                 Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                 Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
                 Rekeningmutaties.Boekdatum <= '$datumEind' AND
                 Rekeningmutaties.Rekening $fondsenWhere  ";
       $DB->SQL($queryRekeningDirecteKostenOpbrengsten);
       $DB->Query();
       $RekeningDirecteKostenOpbrengsten = $DB->NextRecord();

      $queryFondsDirecteKostenOpbrengsten = "SELECT SUM(((TO_DAYS('$datumEind') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('$datumEind') - TO_DAYS('$datumBegin')) * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) AS gewogen,
                       SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaal
                FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
                WHERE
                (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
                Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
                Rekeningmutaties.Boekdatum <= '$datumEind' AND
                Rekeningmutaties.Fonds $fondsenWhere";
       $DB->SQL($queryFondsDirecteKostenOpbrengsten); //echo "$fonds $query  <br>\n";
       $DB->Query();
       $FondsDirecteKostenOpbrengsten = $DB->NextRecord();


	     $queryAttributieStortingenOntrekkingen = "SELECT ".
	              "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$datumBegin."')) ".
	              "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) AS gewogen, ".
	              "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))  AS totaal ".
	              "FROM  (Rekeningen, Portefeuilles)
	               Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	              "WHERE ".
	              "Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
	              "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
	              "Rekeningmutaties.Grootboekrekening = 'FONDS' AND Rekeningmutaties.Fonds $fondsenWhere ";
	     $DB->SQL($queryAttributieStortingenOntrekkingen); //echo "$query <br>\n";
	     $DB->Query();
	     $AttributieStortingenOntrekkingen = $DB->NextRecord();

       $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];
   //    $AttributieStortingenOntrekkingen['totaal'] +=$AttributieStortingenOntrekkingenRekening['totaal'];
   	  $query = "SELECT SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)  as totaal
 	              FROM Rekeningmutaties,Rekeningen
	              WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	              Rekeningen.Rekening $fondsenWhere  AND
 	              Rekeningmutaties.Verwerkt = '1' AND
	              Rekeningmutaties.Boekdatum > '$datumBegin' AND
	               Rekeningmutaties.Boekdatum <= '$datumEind'";

	     $DB->SQL($query);
	     $DB->Query();
	     $data = $DB->nextRecord();
	     $AttributieStortingenOntrekkingen['totaal'] -=$data['totaal'];

       $directeKostenOpbrengsten['totaal'] = $RekeningDirecteKostenOpbrengsten['totaal'] + $FondsDirecteKostenOpbrengsten['totaal'];
       $directeKostenOpbrengsten['gewogen'] = $RekeningDirecteKostenOpbrengsten['gewogen'] + $FondsDirecteKostenOpbrengsten['gewogen'];

 	    $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'] - $directeKostenOpbrengsten['gewogen'] ;
      $performance = ((($eindwaarde - $beginwaarde) + $AttributieStortingenOntrekkingen['totaal'] + $directeKostenOpbrengsten['totaal'] ) / $gemiddelde) * 100;

		$debug=true;
	  $debug=false;

	  $totaalStort += ($AttributieStortingenOntrekkingen['totaal'] );

      if($debug)
      {
   //    echo "$queryAttributieStortingenOntrekkingenRekening <br>\n $queryRekeningDirecteKostenOpbrengsten <br>\n " ;
    //   echo "$queryRekeningDirecteKostenOpbrengsten <br>\n $queryFondsDirecteKostenOpbrengsten <br>\n";
     //  echo "$queryAttributieStortingenOntrekkingenRekening <br>\n $queryAttributieStortingenOntrekkingen <br>\n";
     //  listarray($directeKostenOpbrengsten);
     //  listarray($AttributieStortingenOntrekkingen);
       echo "    <br>\n" ;
       echo "$fondsenWhere $datumBegin -> $datumEind <br>\n";
       echo "gemiddelde= 	 $gemiddelde = begin $beginwaarde -  gewogenSo ".$AttributieStortingenOntrekkingen['gewogen']." - gewogenDko ".$directeKostenOpbrengsten['gewogen']."<br>\n " ;
       echo "   $performance = ((($eindwaarde - $beginwaarde) + ".$AttributieStortingenOntrekkingen['totaal']." + ".$directeKostenOpbrengsten['totaal']." ) / $gemiddelde) * 100;	<br>\n";
       ob_flush();


      echo ($totaalPerf  * (100+$performance)/100)." = ($totaalPerf  * (100+$performance)/100) <br>\n";
      }
      $totaalPerf = ($totaalPerf  * (100+$performance)/100) ;

    }
   if($debug)
     echo " perftotaal ".($totaalPerf-100) ."<br>\n ";

  return array('procent'=>($totaalPerf-100),'stort'=>$totaalStort);
	}

function getKwartalen($julBegin, $julEind)
{
	  $eindjaar = date("Y",$julEind);
	  $eindmaand = floor(date("m",$julEind)/3)*3;
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = floor(date("m",$julBegin)/3)*3;

   $i=0;
   $stop=mktime (0,0,0,$eindmaand-3,0,$eindjaar);
    while ($counterStart <= $stop)
	  {

	    $counterStart = mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar);
	    $counterEnd   = mktime (0,0,0,$beginmaand+$i+4,0,$beginjaar);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;
      if($i == 0)
      {
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
      }
	    else
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
       $i=$i+3;
	  }

	  if($julEind > db2jul($datum[$i-3]['stop']))
	  {
	    $datum[$i]['start'] = $datum[$i-3]['stop'];
	    $datum[$i]['stop'] = jul2sql($julEind);
	  }
	  return $datum;
}
}
?>
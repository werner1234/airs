<?php
/*
Author  						: $Author: cvs $
Laatste aanpassing	: $Date: 2011/08/02 11:12:47 $
File Versie					: $Revision: 1.20 $

$Log: RapportVOLK_L26.php,v $
Revision 1.20  2011/08/02 11:12:47  cvs
ISIN vervangen door fondscode

Revision 1.19  2011/04/15 11:38:01  cvs
*** empty log message ***

Revision 1.18  2011/03/29 09:19:29  cvs
*** empty log message ***

Revision 1.17  2011/01/27 13:56:53  cvs
*** empty log message ***

Revision 1.16  2011/01/20 13:55:58  cvs
*** empty log message ***

Revision 1.15  2011/01/16 11:19:30  rvv
*** empty log message ***

Revision 1.14  2010/10/27 13:06:54  rvv
*** empty log message ***

Revision 1.13  2010/10/27 12:42:16  rvv
*** empty log message ***

Revision 1.12  2010/09/15 16:29:09  rvv
*** empty log message ***

Revision 1.11  2010/07/24 12:02:53  rvv
*** empty log message ***

Revision 1.10  2010/07/21 17:36:35  rvv
*** empty log message ***

Revision 1.9  2010/07/18 17:04:44  rvv
*** empty log message ***

Revision 1.7  2010/06/09 16:39:37  rvv
*** empty log message ***

Revision 1.6  2010/06/03 17:38:48  rvv
*** empty log message ***

Revision 1.5  2010/06/02 16:57:23  rvv
*** empty log message ***

Revision 1.4  2010/05/30 12:46:25  rvv
*** empty log message ***

Revision 1.3  2010/05/26 17:12:37  rvv
*** empty log message ***

Revision 1.1  2010/05/22 12:56:24  rvv
*** empty log message ***

Revision 1.3  2010/05/19 16:24:10  rvv

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
		$this->pdf->excelData 	= array();

		$this->pdf->rapport_titel = "Portefeuilleoverzicht per ".date("d-m-Y",$this->pdf->rapport_datum);

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

		$this->pdf->underlinePercentage=0.8;
	}

	function formatGetal($waarde, $dec)
	{
	  if(round($waarde,4)==0)
	    return;
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



	function printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde,$style='',$extraSettings=array())
	{
    $this->pdf->SetFont($this->pdf->rapport_font,$style,$this->pdf->rapport_fontsize);

    $resultaatProcent=$this->formatGetal((($categorieTotaal['actuelePortefeuilleWaardeEuro'] - $categorieTotaal['beginPortefeuilleWaardeEuro']) / ($categorieTotaal['beginPortefeuilleWaardeEuro'] /100)),$this->pdf->rapport_VOLK_decimaal_proc);
    $resultaatWaarde=$this->formatGetal($categorieTotaal['actuelePortefeuilleWaardeEuro']-$categorieTotaal['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal);

    if($lastCategorieOmschrijving=='Subtotaal Liquiditeiten')
      $this->pdf->CellBorders = array('','','','','','','','','TS','TS','','','','','');
    else
      $this->pdf->CellBorders = array('','','','','','TS','','','TS','TS','','TS','TS','TS','');

    $this->pdf->Cell(40,4,$lastCategorieOmschrijving,0,'L');
    $this->pdf->setX($this->pdf->marge);

    $resultaatWaarde=$categorieTotaal['fondsResultaat']+$categorieTotaal['valutaResultaat'];
 	  $this->pdf->row(array('','','','','',
												$this->formatGetal($categorieTotaal['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												'','',
												$this->formatGetal($categorieTotaal['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($categorieTotaal['aandeelInTotaal'],$this->pdf->rapport_VOLK_decimaal_proc)."%",
												"",$this->formatGetal($categorieTotaal['fondsResultaat'],0),
												$this->formatGetal($categorieTotaal['valutaResultaat'],0),
												$this->formatGetal($resultaatWaarde,0),
                        ''));
    if(substr($lastCategorieOmschrijving,0,9)=='Subtotaal')
      $type='sub'; //regio
    elseif(substr($lastCategorieOmschrijving,0,6)=='Totaal')
      $type='totaal'; //hoofdcategorie
    else
      $type=$lastCategorieOmschrijving;


   if ($extraSettings['regio'] == "Liquiditeiten")
   {
    $_regio = "";
    $_categorie = "Liquiditeiten";
   }
   else
   {
    $_regio = $extraSettings['regio'];
    $_categorie = $extraSettings['cat'];
   }
    $this->pdf->excelData[]=array($this->pdf->portefeuilledata['ClientVermogensbeheerder'],
                                 $this->portefeuille,
                                 $type,
                                 $_categorie,
                                 $_regio,'','','','','','',
                                 round($data['beginPortefeuilleWaardeEuro'],0),'',
                                 round($categorieTotaal['actuelePortefeuilleWaardeEuro'],0),
                                 round($categorieTotaal['fondsResultaat'],0),
                                 round($categorieTotaal['valutaResultaat'],0),
                                 round($resultaatWaarde,2),'',
                                 round($categorieTotaal['aandeelInTotaal'],2));


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
		if($ln)
	    $this->pdf->ln();

	  if($type=='BI' && $title=='Liquiditeiten')
	    $title=$title."***";
    $this->pdf->SetFont($this->pdf->rapport_font,$type,$this->pdf->rapport_fontsize);
    $this->pdf->Cell(40,4,$title,0,1,'L');

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}

	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder,
		                 Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client
		                 FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();

		$this->pdf->AddPage();
				$this->pdf->SetDrawColor($this->pdf->rapport_lijn_rood['r'],$this->pdf->rapport_lijn_rood['g'],$this->pdf->rapport_lijn_rood['b']);
		$this->pdf->SetLineWidth(0.1);

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
		$totaalWaardeBegin = $totaalWaarde['totaal'];

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

		$query="SELECT TijdelijkeRapportage.fonds, TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, TijdelijkeRapportage.actuelePortefeuilleWaardeEuro
    FROM TijdelijkeRapportage
		WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatumVanaf."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
		AND TijdelijkeRapportage.`type`='RENTE'";
		$DB->SQL($query);
		$DB->Query();
		$startRente=array();
		while($data = $DB->NextRecord())
		{
		  $startRente[$data['fonds']] += $data['actuelePortefeuilleWaardeEuro'];
		}

		$query="SELECT
    Rekeningmutaties.Boekdatum,Rekeningmutaties.Grootboekrekening,
    (Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers) -(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) as Bedrag,
    Rekeningmutaties.Fonds
    FROM
    Rekeningmutaties
    Inner Join Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
    WHERE
    Rekeningen.Portefeuille='".$this->portefeuille."' AND
    Rekeningmutaties.Grootboekrekening='RENME' AND
    Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."'";
		$DB->SQL($query);
		$DB->Query();
		//$startRente=array();
		while($data = $DB->NextRecord())
		{
		  $startRente[$data['Fonds']] -= $data['Bedrag'];
		}


			$query="SELECT TijdelijkeRapportage.fondsOmschrijving,TijdelijkeRapportage.fonds,
				 TijdelijkeRapportage.actueleValuta,
				 TijdelijkeRapportage.rekening, ".
				" TijdelijkeRapportage.Valuta, ".
				" TijdelijkeRapportage.totaalAantal, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar,
				SUM(TijdelijkeRapportage.beginPortefeuilleWaardeInValuta) AS beginPortefeuilleWaardeInValuta,
        SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro)  AS beginPortefeuilleWaardeEuro,
        if(TijdelijkeRapportage.`type`='RENTE',(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) ,0 ) as opgelopenente,
        TijdelijkeRapportage.actueleFonds,
				sum(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) as actuelePortefeuilleWaardeInValuta,
				sum(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
Beleggingscategorien.Omschrijving as echteCategorieOmschrijving,
Valutas.Omschrijving AS ValutaOmschrijving,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.actueleValuta,
LOWER(TijdelijkeRapportage.beleggingscategorie) AS categorieOmschrijving,
if(TijdelijkeRapportage.`type`='rekening',TijdelijkeRapportage.rekening,TijdelijkeRapportage.fonds ) AS fondsVeld,
(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta,
(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel,
CategorienPerHoofdcategorie.Hoofdcategorie,
HoofdBeleggingscategorien.Omschrijving AS hoofdcategorieOmschrijving,
TijdelijkeRapportage.Type,
Regios.Omschrijving as regioOmschrijving,
Fondsen.FondsImportCode,
Fondsen.ISINCode
FROM TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta)
LEFT JOIN Fondsen on (TijdelijkeRapportage.fonds = Fondsen.Fonds)
LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie)
LEFT Join CategorienPerHoofdcategorie ON TijdelijkeRapportage.beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien as HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie
LEFT Join Regios ON TijdelijkeRapportage.Regio = Regios.Regio
WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
GROUP BY fondsVeld
ORDER BY HoofdBeleggingscategorien.afdrukvolgorde,Regios.Afdrukvolgorde , Beleggingscategorien.Afdrukvolgorde asc, TijdelijkeRapportage.fondsOmschrijving";

// if(TijdelijkeRapportage.`type`='RENTE',(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) ,0 ) as opgelopenente,
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		$fondsData=array();
		while($data = $DB->NextRecord())
		{
		  $data['beginPortefeuilleWaardeEuro'] += $startRente[$data['fonds']];  //$data['opgelopenente'];

		  if($data['Type']=='rekening')
		    $data['fondsOmschrijving']=$data['fondsOmschrijving'].' '.$data['rekening'];

		  if($data['regioOmschrijving']=='')
		  {
		    if($data['Type']=='rekening')
		      $data['regioOmschrijving']='Liquiditeiten';
		    else
		      $data['regioOmschrijving']='Geen';
		  }

		  if($data['Type'] != 'rekening')
			{
		    $data['fondsResultaat'] = (($data['actuelePortefeuilleWaardeInValuta'] - $data['beginPortefeuilleWaardeInValuta']) * $data['actueleValuta'] - $startRente[$data['fonds']]) / $this->pdf->ValutaKoersEind;
		    // $data['opgelopenente']
		  	$data['fondsResultaatprocent'] = ($data['fondsResultaat'] / $data['beginPortefeuilleWaardeEuro']) * 100;
		  	$data['valutaResultaat'] = $data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro'] - $data['fondsResultaat'];
		  	$data['procentResultaat'] = (($data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro']) / ($data['beginPortefeuilleWaardeEuro'] /100));
		  	if($data['beginPortefeuilleWaardeEuro'] < 0)
				  $data['procentResultaat'] = -1 * $data['procentResultaat'];
			}

			$data['aandeelInTotaal'] =	$data['actuelePortefeuilleWaardeEuro'] / $totaalWaarde *100;
		  $hoofdcategorieTotalen[$data['hoofdcategorieOmschrijving']]['actuelePortefeuilleWaardeEuro']+=$data['actuelePortefeuilleWaardeEuro'];
		  $hoofdcategorieTotalen[$data['hoofdcategorieOmschrijving']]['beginPortefeuilleWaardeEuro']+=$data['beginPortefeuilleWaardeEuro'];
		  $hoofdcategorieTotalen[$data['hoofdcategorieOmschrijving']]['aandeelInTotaal']+=$data['aandeelInTotaal'];
		  $hoofdcategorieTotalen[$data['hoofdcategorieOmschrijving']]['fondsResultaat']+=$data['fondsResultaat'];
		  $hoofdcategorieTotalen[$data['hoofdcategorieOmschrijving']]['valutaResultaat']+=$data['valutaResultaat'];
      $regioRegelCount[$data['hoofdcategorieOmschrijving']][$data['regioOmschrijving']]+=1;
		  $totalen['aandeelInTotaal']+=$data['aandeelInTotaal'];
		  $totalen['fondsResultaat']+=$data['fondsResultaat'];
		  $totalen['valutaResultaat']+=$data['valutaResultaat'];
		  $fondsData[]=$data;
		}

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		foreach ($hoofdcategorieTotalen as $hoofCategorie=>$waarden)
		{
		  $this->pdf->Cell(40,4,$hoofCategorie,0,'L');
		  $this->pdf->setX($this->pdf->marge);
		  $this->pdf->row(array('','','','','','','','',$this->formatGetal($waarden['actuelePortefeuilleWaardeEuro'],0),$this->formatGetal($waarden['aandeelInTotaal'],2)."%",'',
		                  $this->formatGetal($waarden['fondsResultaat'],0),$this->formatGetal($waarden['valutaResultaat'],0),$this->formatGetal($waarden['fondsResultaat']+$waarden['valutaResultaat'],0),''));
		}
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array('T','T','T','T','T','T','T','T','T','T','T','T','T','T','T');
		$this->pdf->Cell(40,4,'Totale portefeuille',0,'L');
		$this->pdf->setX($this->pdf->marge);
			$this->pdf->SetLineWidth($this->pdf->lineWidth);
		$this->pdf->row(array('','','','','','','','',$this->formatGetal($totaalWaarde,0),$this->formatGetal($totalen['aandeelInTotaal'],2)."%",'',
		$this->formatGetal($totalen['fondsResultaat'],0),$this->formatGetal($totalen['valutaResultaat'],0),$this->formatGetal($totalen['fondsResultaat']+$totalen['valutaResultaat'],0),''));
    unset($this->pdf->CellBorders);

		foreach ($fondsData as $line=>$data)
		{
		  //categorietotalen
	    if($data['regioOmschrijving'] != $lastRegioOmschrijving && $lastRegioOmschrijving !='' && isset($lastRegioOmschrijving,$regioTotaal[$lastHoofdcategorieOmschrijving][$lastRegioOmschrijving]))
	    {
        $this->printSubTotaal('Subtotaal '.$lastRegioOmschrijving,$regioTotaal[$lastHoofdcategorieOmschrijving][$lastRegioOmschrijving],$totaalWaarde,'',array('regio'=>$lastRegioOmschrijving,'cat'=>$lastHoofdcategorieOmschrijving));
        if(($this->pdf->getY() + ($regioRegelCount[$data['hoofdcategorieOmschrijving']][$data['regioOmschrijving']]*$this->pdf->rowHeight)+3*$this->pdf->rowHeight) > 200)
          $this->pdf->addPage();
	    }


      if($data['hoofdcategorieOmschrijving'] != $lastHoofdcategorieOmschrijving && $lastHoofdcategorieOmschrijving !='' && isset($hoofdcategorieTotaal[$lastHoofdcategorieOmschrijving]))
        $this->printSubTotaal('Totaal '.$lastHoofdcategorieOmschrijving,$hoofdcategorieTotaal[$lastHoofdcategorieOmschrijving],$totaalWaarde,'BI',array('regio'=>$lastRegioOmschrijving,'cat'=>$lastHoofdcategorieOmschrijving));
      if($data['hoofdcategorieOmschrijving'] != $lastHoofdcategorieOmschrijving)
        $this->printKop($data['hoofdcategorieOmschrijving'],'BI',true);
      if($data['regioOmschrijving'] != $lastRegioOmschrijving )
        $this->printKop($data['regioOmschrijving'],'B',false);

			$resultaat = $data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro'];
			$procentResultaat = (($data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro']) / ($data['beginPortefeuilleWaardeEuro'] /100));
			if($data['beginPortefeuilleWaardeEuro'] < 0)
				$procentResultaat = -1 * $procentResultaat;
			$percentageVanTotaal = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
			$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)."%";

			if(round($data['procentResultaat'],4)==0)
			  $procentResultaattxt='';
			elseif($data['procentResultaat'] > 1000 || $data['procentResultaat'] < -1000)
				$procentResultaattxt = "p.m.";
			else
				$procentResultaattxt = $this->formatGetal($procentResultaat,2)."%";

			$resultaattxt = "";
 			if($resultaat <> 0)
				$resultaattxt = $this->formatGetal($resultaat,$this->pdf->rapport_VOLK_decimaal);

		if($procentResultaat < 0)
      $this->pdf->CellFontColor = array('','','','','','','','','','','','','','',$this->pdf->rapport_font_rood);
    else
      $this->pdf->CellFontColor = array('','','','','','','','','','','','','','',$this->pdf->rapport_font_groen);


			$this->pdf->row(array($this->formatAantal($data['totaalAantal'],$this->pdf->rapport_VOLK_aantal_decimaal,false),
												substr($data['categorieOmschrijving'],0,13),
												$data['fondsOmschrijving'],
												$data['valuta'],
												$this->formatGetal($data['beginwaardeLopendeJaar'],2),
												$this->formatGetal($data['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												'',
												$this->formatGetal($data['actueleFonds'],2),
												$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$percentageVanTotaaltxt,
												"",$this->formatGetal($data['fondsResultaat'],0),
												$this->formatGetal($data['valutaResultaat'],0),
												$this->formatGetal($data['fondsResultaat']+$data['valutaResultaat'],0),
                        $procentResultaattxt	));

   if ($data['regioOmschrijving'] == "Liquiditeiten")
   {
    $_regio = "";
    $_categorie = "Liquiditeiten";
   }
   else
   {
    $_regio = $data['regioOmschrijving'];
    $_categorie = $data['hoofdcategorieOmschrijving'];
   }

      $this->pdf->excelData[]=array($this->pdf->portefeuilledata['ClientVermogensbeheerder'],
                                 $this->portefeuille,
                                 'regel',
                                 $_categorie,
                                 $_regio,
                                 $data['totaalAantal'],
                                 $data['categorieOmschrijving'],
                                 $data['FondsImportCode'],
                                 $data['fondsOmschrijving'],
                                 $data['valuta'],
                                 round($data['beginwaardeLopendeJaar'],2),
                                 round($data['beginPortefeuilleWaardeEuro'],0),
                                 round($data['actueleFonds'],2),
                                 round($data['actuelePortefeuilleWaardeEuro'],0),
                                 round($data['fondsResultaat'],0),
                                 round($data['valutaResultaat'],0),
                                 round($data['fondsResultaat']+$data['valutaResultaat'],0),
                                 round($procentResultaat,2),
                                 round($percentageVanTotaal,2));

    $regioTotaal[$data['hoofdcategorieOmschrijving']][$data['regioOmschrijving']]['valutaResultaat'] +=$data['valutaResultaat'];
    $regioTotaal[$data['hoofdcategorieOmschrijving']][$data['regioOmschrijving']]['fondsResultaat'] +=$data['fondsResultaat'];
    $regioTotaal[$data['hoofdcategorieOmschrijving']][$data['regioOmschrijving']]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
		$regioTotaal[$data['hoofdcategorieOmschrijving']][$data['regioOmschrijving']]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];
		$regioTotaal[$data['hoofdcategorieOmschrijving']][$data['regioOmschrijving']]['aandeelInTotaal'] +=$percentageVanTotaal;


    $hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['valutaResultaat'] +=$data['valutaResultaat'];
    $hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['fondsResultaat'] +=$data['fondsResultaat'];
		$hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
		$hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];
		$hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['aandeelInTotaal'] +=$percentageVanTotaal;

    $lastHoofdcategorieOmschrijving=$data['hoofdcategorieOmschrijving'];
    $lastCategorieOmschrijving=$data['categorieOmschrijving'];
    $lastRegioOmschrijving=$data['regioOmschrijving'];
		}
//listarray($regioTotaal);exit;

	//	$this->printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde);
	$this->printSubTotaal('Subtotaal '.$lastRegioOmschrijving,$regioTotaal[$lastHoofdcategorieOmschrijving][$lastRegioOmschrijving],$totaalWaarde,'',array('regio'=>$lastRegioOmschrijving,'cat'=>$lastHoofdcategorieOmschrijving));
	$this->printSubTotaal('Totaal '.$lastHoofdcategorieOmschrijving,$hoofdcategorieTotaal[$lastHoofdcategorieOmschrijving],$totaalWaarde,'BI',array('regio'=>$lastRegioOmschrijving,'cat'=>$lastHoofdcategorieOmschrijving));




		// check op totaalwaarde!
		$actueleWaardePortefeuille=0;
		foreach ($regioTotaal as $categorie=>$waardes)
		{
		  $actueleWaardePortefeuille+=$waardes['actuelePortefeuilleWaardeEuro'];
		}

		      $this->pdf->excelData[]=array($this->pdf->portefeuilledata['ClientVermogensbeheerder'],
                                 $this->portefeuille,
                                 'totaalPort',
                                 'totaal',
                                 'totaal',
                                 '',
                                 '',
                                 '',
                                 '',
                                 '',
                                 '',
                                 round($totaalWaardeBegin,2),
                                 round($data['actueleFonds'],2),
                                 round($totaalWaarde,2),
                                 round($totalen['fondsResultaat'],2),
                                 round($totalen['valutaResultaat'],2),
                                 round($totalen['fondsResultaat']+$totalen['valutaResultaat'],2),
                                 '',
                                 round($totalen['aandeelInTotaal'],2));

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

			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
      $this->pdf->ln();
	    $this->pdf->Cell(75,4,"* Koers voor verrekening transactiekosten en eventuele belastingen.",'0',1,'L');
	    $this->pdf->Cell(75,4,"** Waarde in Euro inclusief lopende rente en voor verrekening transactie kosten.",'0',1,'L');
	    $this->pdf->Cell(75,4,"*** Liquiditeiten exclusief lopende rente.",'0',1,'L');
unset($this->pdf->CellFontColor);



	}
}
?>

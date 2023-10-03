<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/25 15:36:13 $
File Versie					: $Revision: 1.11 $

$Log: RapportVOLK_L27.php,v $
Revision 1.11  2020/07/25 15:36:13  rvv
*** empty log message ***

Revision 1.10  2017/08/09 16:10:49  rvv
*** empty log message ***

Revision 1.9  2015/02/15 10:36:34  rvv
*** empty log message ***

Revision 1.8  2015/02/07 20:37:51  rvv
*** empty log message ***

Revision 1.7  2013/11/13 15:48:56  rvv
*** empty log message ***

Revision 1.6  2013/10/19 15:57:25  rvv
*** empty log message ***

Revision 1.5  2012/05/08 07:44:04  rvv
*** empty log message ***

Revision 1.4  2012/04/21 15:38:14  rvv
*** empty log message ***

Revision 1.3  2010/12/22 18:45:30  rvv
*** empty log message ***

Revision 1.2  2010/12/19 13:05:15  rvv
*** empty log message ***

Revision 1.1  2010/07/04 15:24:39  rvv
*** empty log message ***

Revision 1.3  2010/05/19 16:24:10  rvv
*** empty log message ***

Revision 1.2  2010/05/09 19:21:43  rvv
*** empty log message ***

Revision 1.1  2010/05/05 18:37:43  rvv
*** empty log message ***

Revision 1.52  2010/04/21 06:37:18  rvv
*** empty log message ***

*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVOLK_L27
{
	function RapportVOLK_L27($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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



	function printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde,$resultaat=true)
	{
	  $this->pdf->fillCell=array();
          $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
          if(!isset($categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro']))
          {
            $this->pdf->CellBorders = array('','','','','','','','SUB','SUB');
            $this->pdf->row(array("Subtotaal ".$lastCategorieOmschrijving,
            '','','','','','',
            $this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
            $this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']/ ($totaalWaarde/100),$this->pdf->rapport_VOLK_decimaal_proc)));
          }
          else
          {
          $this->pdf->CellBorders = array('','','','','SUB','','','SUB','SUB','','SUB','SUB');
          if($resultaat)
          {
            $resultaatProcent=$this->formatGetal((($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro'] - $categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro']) / ($categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'] /100)),$this->pdf->rapport_VOLK_decimaal_proc);
            $resultaatWaarde=$this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']-$categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal);
          }
          else
          {
            $this->pdf->CellBorders = array('','','','','SUB','','','SUB','SUB','','','');
            $resultaatProcent='';
            $resultaatWaarde='';
          }

          $this->pdf->row(array("Subtotaal ".$lastCategorieOmschrijving,
          '','','',
          $this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
          '','',
          $this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
          $this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']/ ($totaalWaarde/100),$this->pdf->rapport_VOLK_decimaal_proc),
          '',
          $resultaatWaarde,
          $resultaatProcent));
          }
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
    $this->pdf->row(array($title));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}

	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetFillColor(200,240,255);


		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();

		$this->pdf->AddPage();

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

		$query = "SELECT hoofdscategorieVolgorde.Afdrukvolgorde as hoofdcategorieAfdrukVolgorde,hoofdscategorieVolgorde.Omschrijving as hoofdcategorieOmschrijving,
CategorienPerHoofdcategorie.Hoofdcategorie,
categorieVolgorde.Afdrukvolgorde as categorieAfdrukVolgorde,categorieVolgorde.Omschrijving as categorieOmschrijving,
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
				   TijdelijkeRapportage.portefeuille,
				   TijdelijkeRapportage.historischeWaarde,
				   Valutas.Valutateken ".
				" FROM TijdelijkeRapportage
				LEFT Join CategorienPerHoofdcategorie ON TijdelijkeRapportage.beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien as hoofdscategorieVolgorde ON CategorienPerHoofdcategorie.Hoofdcategorie = hoofdscategorieVolgorde.Beleggingscategorie
LEFT Join Beleggingscategorien as categorieVolgorde ON TijdelijkeRapportage.beleggingscategorie = categorieVolgorde.Beleggingscategorie
LEFT Join Valutas ON TijdelijkeRapportage.valuta = Valutas.Valuta
WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.type =  'fondsen' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" ORDER BY hoofdcategorieAfdrukVolgorde,
CategorienPerHoofdcategorie.Hoofdcategorie,
categorieAfdrukVolgorde,
TijdelijkeRapportage.beleggingscategorie ,
TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";

		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();


		while($data = $DB->NextRecord())
		{
		  //categorietotalen
		  $this->pdf->rowHeight=5;
	    if($data['categorieOmschrijving'] != $lastCategorieOmschrijving && $lastCategorieOmschrijving !='' && is_array($categorieTotaal[$lastCategorieOmschrijving]))
          $this->printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde);
      if($data['hoofdcategorieOmschrijving'] != $lastHoofdcategorieOmschrijving)
        $this->printKop($data['hoofdcategorieOmschrijving'],'BI',true);
      if($data['categorieOmschrijving'] != $lastCategorieOmschrijving )
        $this->printKop($data['categorieOmschrijving'],'B',false);

			$resultaat = $data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro'];
			$procentResultaat = (($data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro']) / ($data['beginPortefeuilleWaardeEuro'] /100));
			if($data['beginPortefeuilleWaardeEuro'] < 0)
				$procentResultaat = -1 * $procentResultaat;
			$percentageVanTotaal = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
			$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";

			if($procentResultaat > 1000 || $procentResultaat < -1000)
				$procentResultaattxt = "p.m.";
			else
				$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VOLK_decimaal_proc);

			$resultaattxt = "";
 			if($resultaat <> 0)
				$resultaattxt = $this->formatGetal($resultaat,$this->pdf->rapport_VOLK_decimaal);


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

			$this->pdf->row(array("  ".$data['fondsOmschrijving'],
				                $this->formatAantal($data['totaalAantal'],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
												$data['Valuta'],
												$this->formatGetal($data['beginwaardeLopendeJaar'],2),
												$this->formatGetal($data['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												"",
												$this->formatGetal($data['actueleFonds'],2),
												$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc),
												"",
												$resultaattxt,
												$procentResultaattxt
												 )	);//,$this->formatGetal($data['historischeWaarde'],2)
		$categorieTotaal[$data['categorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
		$categorieTotaal[$data['categorieOmschrijving']]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];
		$hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
		$hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];

    $lastHoofdcategorieOmschrijving=$data['hoofdcategorieOmschrijving'];
    $lastCategorieOmschrijving=$data['categorieOmschrijving'];
		}
		$this->printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde);


		// selecteer rente
		$query = "SELECT Valutas.Valutateken,TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.fondsOmschrijving, ".
		" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
		" TijdelijkeRapportage.rentedatum, ".
		" TijdelijkeRapportage.renteperiode, ".
		" TijdelijkeRapportage.beginPortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersBegin. " as beginPortefeuilleWaardeEuro, ".
		" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro FROM ".
		" TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta) ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.type = 'rente'  AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		"  ".
		" ORDER BY Valutas.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		if($DB->records() > 0)
		{
		  $this->printKop(vertaalTekst("Opgelopen rente en coupondatum",$this->pdf->rapport_taal), "B");
			$totaalRenteInValuta = 0 ;
			while($data = $DB->NextRecord())
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
			  	$rentePeriodetxt = "  ".date("d-m",db2jul($data['rentedatum']));
					if($subdata['renteperiode'] <> 12 && $data['renteperiode'] <> 0)
						$rentePeriodetxt .= " / ".$data['renteperiode'];

					$percentageVanTotaal = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);

					$this->pdf->row(array("  ".$data['fondsOmschrijving'].' '.$rentePeriodetxt,'',
					              $data['valuta'],'','','','',
												$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)));
					$categorieTotaal["Opgelopen Rente"]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
			}
			$this->printSubTotaal("Opgelopen Rente",$categorieTotaal,$totaalWaarde);
		}

		// Liquiditeiten
		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.rekening, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.valuta as zoekValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro, ".
			" (SELECT actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage WHERE portefeuille = '".$this->portefeuille."' AND rapportageDatum = '".$this->rapportageDatumVanaf."' AND TijdelijkeRapportage.valuta = zoekValuta AND type='rekening'  LIMIT 1)  / ".$this->pdf->ValutaKoersBegin. " as beginPortefeuilleWaardeEuro,".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,
			Rekeningen.Deposito, Rekeningen.Termijnrekening, Rekeningen.Memoriaal".
			" FROM TijdelijkeRapportage JOIN Rekeningen on Rekeningen.rekening = TijdelijkeRapportage.rekening  AND Rekeningen.Portefeuille = TijdelijkeRapportage.portefeuille
			WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekening'  ".
			" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.valuta asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();

		if($DB1->records() > 0)
		{
			$totaalLiquiditeitenInValuta = 0;
			$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),"B");
			while($data = $DB1->NextRecord())
			{
			  $liqiteitenBuffer[] = $data;
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
					$this->pdf->row(array("  ".$data['fondsOmschrijving'].' '.$data['rekening'],'',$data['Valutateken'],'',
                        $this->formatGetal($data['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												'','',
											  $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)
												));
					$categorieTotaal["Liquiditeiten"]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
					$categorieTotaal["Liquiditeiten"]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];
			}
			$this->printSubTotaal("Liquiditeiten",$categorieTotaal,$totaalWaarde,false);
		} // einde liquide

		// check op totaalwaarde!
		$actueleWaardePortefeuille=0;
		foreach ($categorieTotaal as $categorie=>$waardes)
		{
		  $actueleWaardePortefeuille+=$waardes['actuelePortefeuilleWaardeEuro'];
		}
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();

		}

		$this->pdf->CellBorders = array('','','','','','','','SUB','SUB');
		$this->pdf->row(array("Totale actuele waarde portefeuille",'','','','','','',$this->formatGetal($totaalWaarde,$this->pdf->rapport_VOLK_decimaal),
		$this->formatGetal(($actueleWaardePortefeuille/$totaalWaarde*100),$this->pdf->rapport_VOLK_decimaal_proc)));
    $this->pdf->CellBorders = array();
		$this->pdf->ln();
		      $this->pdf->rowHeight=4;
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

		if($this->pdf->portefeuilledata['AEXVergelijking'] > 0 ) //|| $this->pdf->rapport_layout == 8 voor L8 de index er weer uitgehaald.
		{
			  $this->printAEXVergelijking($this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);
		}

$this->pdf->ln(8);
if($this->pdf->getY() > 160)
{
  $this->pdf->addPage();
  $this->pdf->ln(8);
}
		$this->pdf->MultiCell(145,4,"Om in grote lijnen te kunnen bepalen hoe de relatieve prestatie van uw beleggingsportefeuille is, kunt u deze vergelijken met bepaalde indices. Omdat uw portefeuille geen exacte afspiegeling vormt van één bepaalde index, geven wij u er een paar die daar zo veel mogelijk bij in de buurt komen. Het aandelengedeelte van uw portefeuille kunt u afzetten tegen de MSCI World Euro. Dit is een wereldwijde aandelenindex vertaald naar euro. Deze index bestaat uit 1728 aandelen uit 23 ontwikkelde landen. Voor het obligatiegedeelte kunt u de Barclays Euro Aggr. Corp. TR Unhdg gebruiken. Dit is een wereldwijde index van (bedrijfs)obligaties vertaald naar euro. De AEX Index tenslotte is de Nederlandse index van 25 hoofdfondsen.", 1, "L");


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
			$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
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

    $widthDesc=50;
		$this->pdf->ln();
		$this->pdf->SetFillColor(255,255,255);
		$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),70+9+$extraX+$widthDesc,$hoogte,'F');
		$this->pdf->SetFillColor(0);
		$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),70+9+$extraX+$widthDesc,$hoogte);
		$this->pdf->SetX($this->pdf->marge);
    
		// kopfontcolor
		//$this->pdf->SetTextColor($this->pdf->rapport_kop4_fontcolor['r'],$this->pdf->rapport_kop4_fontcolor['g'],$this->pdf->rapport_kop4_fontcolor['b']);
		$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_kop4_font,$this->pdf->rapport_kop4_fontstyle,$this->pdf->rapport_kop4_fontsize);
		$this->pdf->Cell($widthDesc,4, vertaalTekst("Index-vergelijking",$this->pdf->rapport_taal), 0,0, "L");

		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
		//$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
		$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
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
		    $q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$januariDatum."' AND Fonds = '".$perf['Beursindex']."'  ORDER BY Datum DESC LIMIT 1";
		  	$DB2->SQL($q);
		  	$DB2->Query();
		  	$koers0 = $DB2->LookupRecord();
		  }

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatumVanaf."' AND Fonds = '".$perf['Beursindex']."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers1 = $DB2->LookupRecord();

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatum."' AND Fonds = '".$perf['Beursindex']."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers2 = $DB2->LookupRecord();

			$performanceJaar = ($koers2['Koers'] - $koers0['Koers']) / ($koers0['Koers']/100 );
			$performance = ($koers2['Koers'] - $koers1['Koers']) / ($koers1['Koers']/100 );
			$performanceEur = ($koers2['Koers']*$valutaKoersStop['Koers'] - $koers1['Koers']*$valutaKoersStart['Koers']) / ($koers1['Koers']*$valutaKoersStart['Koers']/100 );
      //echo $perf['Omschrijving']." $performanceEur = (.".$koers2['Koers']."*".$valutaKoersStop['Koers']." - ".$koers1['Koers']."*".$valutaKoersStart['Koers'].") / (".$koers1['Koers']."*".$valutaKoersStart['Koers']."/100 );<br>";
			$this->pdf->Cell($widthDesc,4, $perf['Omschrijving'], $border,0, "L");
		  if($this->pdf->rapport_perfIndexJanuari == true)
		     $this->pdf->Cell(26,4, $this->pdf->formatGetal($koers0['Koers'],2), $border,0, "R");
			$this->pdf->Cell(26,4, $this->pdf->formatGetal($koers1['Koers'],2), $border,0, "R");
			$this->pdf->Cell(26,4, $this->pdf->formatGetal($koers2['Koers'],2), $border,0, "R");
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
		  	  $q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$januariDatum."' AND Fonds = '".$perf['SpecifiekeIndex']."'  ORDER BY Datum DESC LIMIT 1";
			    $DB2->SQL($q);
			    $DB2->Query();
			    $koers0 = $DB2->LookupRecord();
		    }

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatumVanaf."' AND Fonds = '".$perf['SpecifiekeIndex']."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers1 = $DB2->LookupRecord();

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatum."' AND Fonds = '".$perf['SpecifiekeIndex']."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers2 = $DB2->LookupRecord();

			$performanceJaar = ($koers2['Koers'] - $koers0['Koers']) / ($koers0['Koers']/100 );
			$performance = ($koers2['Koers'] - $koers1['Koers']) / ($koers1['Koers']/100 );
			$performanceEur = ($koers2['Koers']*$valutaKoersStop['Koers'] - $koers1['Koers']*$valutaKoersStart['Koers']) / ($koers1['Koers']*$valutaKoersStart['Koers']/100 );
      //echo $perf['Omschrijving']." $performanceEur = (.".$koers2['Koers']."*".$valutaKoersStop['Koers']." - ".$koers1['Koers']."*".$valutaKoersStart['Koers'].") / (".$koers1['Koers']."*".$valutaKoersStart['Koers']."/100 );<br>";


			$this->pdf->Cell($widthDesc,4, $perf['Omschrijving'], 0,0, "L");
			if($this->pdf->rapport_perfIndexJanuari == true)
		     $this->pdf->Cell(26,4, $this->pdf->formatGetal($koers0['Koers'],2), $border,0, "R");
			$this->pdf->Cell(26,4, $this->pdf->formatGetal($koers1['Koers'],2), $border,0, "R");
			$this->pdf->Cell(26,4, $this->pdf->formatGetal($koers2['Koers'],2), $border,0, "R");
		  $this->pdf->Cell(26,4, $this->pdf->formatGetal($performance,2).$teken, $border,$perfVal, "R");
		  if($this->pdf->rapport_printAEXVergelijkingEur == 1)
		    $this->pdf->Cell(26,4, $this->pdf->formatGetal($performanceEur,2).$teken, $border,$perfEur, "R");
		  if($this->pdf->rapport_perfIndexJanuari == true)
		    $this->pdf->Cell(26,4, $this->pdf->formatGetal($performanceJaar,2).$teken, $border,$perfJan, "R");
		}
	}
}
?>
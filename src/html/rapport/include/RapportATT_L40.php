<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2015/06/06 10:13:06 $
File Versie					: $Revision: 1.1 $

$Log: RapportATT_L40.php,v $
Revision 1.1  2015/06/06 10:13:06  rvv
*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/rapportATTberekening.php");

class RapportATT_L40
{

	function RapportATT_L40($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		global $__appvar;
		$this->__appvar = $__appvar;
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ATT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->underlinePercentage=1;

		if($this->pdf->rapport_PERF_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_PERF_titel;
		else
			$this->pdf->rapport_titel = "Performancemeting (in ".$this->pdf->rapportageValuta.")";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	  if(strval($pdf->rapport_ATT_decimaal) != '')
	    $this->bedragDecimalen=$pdf->rapport_ATT_decimaal;
	  else
	    $this->bedragDecimalen=2;

	  $this->periodeId = substr(jul2db(db2jul($this->rapportageDatumVanaf)),0,10)."-".substr(jul2db(db2jul($this->rapportageDatum)),0,10);
	  $this->db = new DB();

	 if ($RapStartJaar != $RapStopJaar)
	 {
     echo "Attributie start- en einddatum moeten in hetzelfde jaar liggen.";
     flush();
     exit;
	 }
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersBegin;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec)
	{
		  return number_format($waarde,$dec,",",".");
	}

	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}

	function tweedeStart()
	{
	  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  else
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";
	}

	function getAttributieCategorien()
	{
	  $query = "SELECT  BeleggingssectorPerFonds.AttributieCategorie,  AttributieCategorien.Omschrijving
              FROM BeleggingssectorPerFonds  ,AttributieCategorien
              WHERE BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND
              BeleggingssectorPerFonds.AttributieCategorie =  AttributieCategorien.AttributieCategorie
              GROUP BY BeleggingssectorPerFonds.AttributieCategorie";
		$this->db->SQL($query);
		$this->db->Query();
		$this->categorien[] = 'Totaal';
		$categorieKop[] = 'Totaal';
		while($categorie = $this->db->nextRecord())
		{
		  $categorieKop[]=$categorie['Omschrijving'];
		  $this->categorien[]=$categorie['AttributieCategorie'];
		}
		$categorieKop[]='Liquiditeiten';
		$this->categorien[]='Liquiditeiten';

		$this->pdf->row(array(""));
		$kopRegel = array();
	  array_push($kopRegel,"");
	  array_push($kopRegel,"");
		foreach ($categorieKop as $omschrijving)
		{
		  array_push($kopRegel,$omschrijving);
		  array_push($kopRegel,"");
		}
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
		$this->pdf->row($kopRegel);
		return $this->categorien;
	}

  function bepaalCategorieWaarden()
  {
    foreach ($this->categorien as $categorie)
    {
      if ($categorie == 'Totaal')
        $attributieQuery = '';
      elseif ($categorie == 'Liquiditeiten')
        $attributieQuery = " TijdelijkeRapportage.AttributieCategorie = '' AND ";
      else
        $attributieQuery = " TijdelijkeRapportage.AttributieCategorie = '".$categorie."' AND";

		  if ($categorie == 'Totaal' || $this->pdf->debug)
		  {

		    $gerealiseerdKoersresultaat[$categorie] = gerealiseerdKoersresultaat($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta,true,$categorie);

	 		  $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS totaalB, ".
 						 "SUM(beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin." AS totaalA ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND ".
						 $attributieQuery
						 ." type = 'fondsen' ".$this->__appvar['TijdelijkeRapportageMaakUniek'];
		    debugSpecial($query,__FILE__,__LINE__);
		    $this->db->SQL($query);
		    $this->db->Query();
		    $totaal = $this->db->nextRecord();
        $ongerealiseerdeKoersResultaaten[$categorie] = ($totaal[totaalB] - $totaal[totaalA]) ;

        $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						     "FROM TijdelijkeRapportage WHERE ".
						     " rapportageDatum ='".$this->rapportageDatum."' AND ".
						     " portefeuille = '".$this->portefeuille."' AND ".$attributieQuery.
						     " type = 'rente' ".$this->__appvar['TijdelijkeRapportageMaakUniek'];
		    debugSpecial($query,__FILE__,__LINE__);
		    $this->db->SQL($query);
		    $this->db->Query();
		    $totaalA = $this->db->nextRecord();
    		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
				    		 "FROM TijdelijkeRapportage WHERE ".
						     " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
						     " portefeuille = '".$this->portefeuille."' AND ". $attributieQuery.
						     " type = 'rente' ". $this->__appvar['TijdelijkeRapportageMaakUniek'] ;
		    debugSpecial($query,__FILE__,__LINE__);
		    $this->db->SQL($query);
		    $this->db->Query();
		    $totaalB = $this->db->nextRecord();
    		$opgelopenRentes[$categorie] = ($totaalA[totaal] - $totaalB[totaal]) / $this->pdf->ValutaKoersEind;
      }
    }
    $waarden=array('gerealiseerdKoersresultaat'=>$gerealiseerdKoersresultaat,
                   'ongerealiseerdeKoersResultaaten'=>$ongerealiseerdeKoersResultaaten,
                   'opgelopenRentes'=>$opgelopenRentes);
    $this->waarde = $waarden;
  return $waarden;
  }


  function createRows()
  {
    $row['waardeVanaf'] = array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)));
    $row['waardeTot'] = array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum)));
    $row['mutatiewaarde'] = array("",vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal));
    $row['totaalStortingen'] = array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal));
    $row['totaalOnttrekkingen'] = array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal));
    $row['directeOpbrengsten'] = array("",vertaalTekst("Directe opbrengsten",$this->pdf->rapport_taal));
    $row['toegerekendeKosten'] = array("",vertaalTekst("Toegerekende kosten",$this->pdf->rapport_taal));
    $row['resultaatVerslagperiode'] = array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));
    $row['rendementProcent'] = array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal));
    $row['rendementProcentJaar'] = array("",vertaalTekst("Rendement over lopende jaar",$this->pdf->rapport_taal));
    $row['gerealiseerdKoersresultaat'] = array("",vertaalTekst("gerealiseerdKoersresultaat",$this->pdf->rapport_taal));
    $row['ongerealiseerdeKoersResultaaten'] = array("",vertaalTekst("ongerealiseerdeKoersResultaaten",$this->pdf->rapport_taal));
    $row['opgelopenRentes'] = array("",vertaalTekst("opgelopenRentes",$this->pdf->rapport_taal));
    $row['totaal'] = array("",vertaalTekst("Totaal Performance",$this->pdf->rapport_taal));

    foreach ($this->categorien as $categorie)
    {
      $resultaatVerslagperiode[$categorie] = $this->waarden['rapportagePeriode']['mutatie'][$categorie] - $this->waarden['rapportagePeriode']['stortingen'][$categorie] + $this->waarden['rapportagePeriode']['onttrekkingen'][$categorie] + $this->waarden['rapportagePeriode']['opbrengsten'][$categorie] - $this->waarden['rapportagePeriode']['kosten'][$categorie];
      if ($categorie == 'Totaal')
      {
        $resultaatCorrectie = $resultaatVerslagperiode['Totaal'] - $this->waarde['opgelopenRentes'][$categorie] - $this->waarde['ongerealiseerdeKoersResultaaten'][$categorie] -
                                                               ($this->waardenPerGrootboek['totaalOpbrengst'] - $this->waardenPerGrootboek['totaalKosten']);
        if(round($resultaatCorrectie,1) != round($this->waarde['gerealiseerdKoersresultaat'][$categorie],1))//correctie vreemde valuta
        {
          $this->waarde['gerealiseerdKoersresultaat'][$categorie]  = $resultaatCorrectie ;
        }
      }

      array_push($row['waardeVanaf'],$this->formatGetal($this->waarden['rapportagePeriode']['beginWaarde'][$categorie],$this->bedragDecimalen,true));
      array_push($row['waardeVanaf'],"");

      array_push($row['waardeTot'],$this->formatGetal($this->waarden['rapportagePeriode']['eindWaarde'][$categorie],$this->bedragDecimalen));
      array_push($row['waardeTot'],"");

      array_push($row['mutatiewaarde'],$this->formatGetal($this->waarden['rapportagePeriode']['mutatie'][$categorie],$this->bedragDecimalen));
      array_push($row['mutatiewaarde'],"");

      if ($categorie == 'Liquiditeiten')
      {
        array_push($row['totaalStortingen'],$this->formatGetal(-1*$this->waarden['rapportagePeriode']['stortingen'][$categorie],$this->bedragDecimalen));
        array_push($row['totaalOnttrekkingen'],$this->formatGetal(-1*$this->waarden['rapportagePeriode']['onttrekkingen'][$categorie],$this->bedragDecimalen));
        array_push($row['rendementProcent'],' ');
        array_push($row['rendementProcent'],' ');
        array_push($row['rendementProcentJaar'],' ');
        array_push($row['rendementProcentJaar'],' ');
      }
      else
      {
        array_push($row['totaalStortingen'],$this->formatGetal($this->waarden['rapportagePeriode']['stortingen'][$categorie],$this->bedragDecimalen));
        array_push($row['totaalOnttrekkingen'],$this->formatGetal($this->waarden['rapportagePeriode']['onttrekkingen'][$categorie],$this->bedragDecimalen));
        array_push($row['rendementProcent'],$this->formatGetal($this->waarden['rapportagePeriode']['performance'][$categorie],2));
        array_push($row['rendementProcent'],'%');
        array_push($row['rendementProcentJaar'],$this->formatGetal($this->waarden['lopendeJaar']['performance'][$categorie],2));
        array_push($row['rendementProcentJaar'],'%');
      }

      array_push($row['totaalStortingen'],"");
      array_push($row['totaalOnttrekkingen'],"");

      if ($categorie == 'Totaal')
      {
        array_push($row['directeOpbrengsten'],'-');
        array_push($row['toegerekendeKosten'],'-');
      }
      else
      {
        array_push($row['directeOpbrengsten'],$this->formatGetal($this->waarden['rapportagePeriode']['opbrengsten'][$categorie],$this->bedragDecimalen));
        array_push($row['toegerekendeKosten'],$this->formatGetal($this->waarden['rapportagePeriode']['kosten'][$categorie],$this->bedragDecimalen));
      }
      array_push($row['directeOpbrengsten'],"");
      array_push($row['toegerekendeKosten'],"");

      array_push($row['resultaatVerslagperiode'],$this->formatGetal($resultaatVerslagperiode[$categorie],$this->bedragDecimalen));
      array_push($row['resultaatVerslagperiode'],"");
   }
  return $row;
  }


  function waardenPerGrootboek()
  {

	  if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		$query = "SELECT Grootboekrekeningen.Omschrijving,Grootboekrekeningen.Grootboekrekening,
		Grootboekrekeningen.Kosten ,Grootboekrekeningen.Opbrengst,".
		"SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery ) -  ".
		"SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery )AS waarde ".
		"FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen ".
		"WHERE ".
		"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		"Rekeningmutaties.Verwerkt = '1' AND ".
		"Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
		"Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND ".
		"Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND ".
		"  (Grootboekrekeningen.Kosten = '1' || Grootboekrekeningen.Opbrengst ='1') ".
		"GROUP BY Rekeningmutaties.Grootboekrekening ".
		"ORDER BY Grootboekrekeningen.Afdrukvolgorde ";

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$waardenPerGrootboek = array();
		while($grootboek = $DB->nextRecord())
		{
		  if($grootboek['Opbrengst']=='1')
		  {
		    $waardenPerGrootboek['opbrengst'][$grootboek['Grootboekrekening']]['omschrijving'] = $grootboek['Omschrijving'];
		    $waardenPerGrootboek['opbrengst'][$grootboek['Grootboekrekening']]['bedrag'] += $grootboek['waarde'];
		    $waardenPerGrootboek['totaalOpbrengst'] += $grootboek['waarde'];
		  }
		  else
		  {
		  	if($grootboek[Grootboekrekening] == "KNBA")
		  	{
		  	  $waardenPerGrootboek['kosten'][$grootboek['Grootboekrekening']]['omschrijving'] = "Bankkosten en provisie";
		  	  $waardenPerGrootboek['kosten'][$grootboek['Grootboekrekening']]['bedrag'] -= $grootboek['waarde'];
			  }
			  else if($grootboek[Grootboekrekening] == "KOBU")
		  	{
				  $waardenPerGrootboek['kosten']['KOST']['bedrag'] -= $grootboek['waarde'];

		  	}
		  	else
			  {
		  		$waardenPerGrootboek['kosten'][$grootboek['Grootboekrekening']]['omschrijving'] = $grootboek['Omschrijving'];
			  	$waardenPerGrootboek['kosten'][$grootboek['Grootboekrekening']]['bedrag'] -= $grootboek['waarde'];
		  	}
        $waardenPerGrootboek['totaalKosten'] -= $grootboek['waarde'];
		  }
		}

		return $waardenPerGrootboek;
  }


	function writeRapport()
	{
	  $this->tweedeStart();
	  $DB = new DB();
		$this->pdf->SetLineWidth($this->pdf->lineWidth);
		$kopStyle = "u";

	  if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

	  if($this->pdf->portefeuilledata['PerformanceBerekening'] == 2)
	    $periodeBlok = 'periode';
	  elseif($this->pdf->portefeuilledata['PerformanceBerekening'] == 6)
	    $periodeBlok = 'kwartaal';
	  else
	    $periodeBlok = 'maand';

	  $query =  "SELECT Portefeuilles.Vermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Portefeuille, Portefeuilles.Startdatum, ".
		" Portefeuilles.Einddatum, Portefeuilles.Client, Portefeuilles.Depotbank, Portefeuilles.RapportageValuta, Vermogensbeheerders.attributieInPerformance, ".
		" Clienten.Naam, Portefeuilles.ClientVermogensbeheerder FROM (Portefeuilles, Clienten ,Vermogensbeheerders)  WHERE ".
		" Portefeuilles.Client = Clienten.Client AND Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder".
		" AND Portefeuilles.Portefeuille = '$this->portefeuille' ";
		$DB->SQL($query);
		$pdata = $DB->lookupRecord();

	  $this->berekening = new rapportATTberekening($pdata);
	  $this->berekening->getAttributieCategorien();
    $this->berekening->pdata['pdf']=true;
    $this->berekening->attributiePerformance($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,'rapportagePeriode',$this->pdf->rapportageValuta,$periodeBlok);
    $this->berekening->attributiePerformance($this->portefeuille,$this->tweedePerformanceStart,$this->rapportageDatum,'lopendeJaar',$this->pdf->rapportageValuta,$periodeBlok);

    $this->waarden['rapportagePeriode']=$this->berekening->performance['rapportagePeriode'];
    $this->waarden['lopendeJaar']=$this->berekening->performance['lopendeJaar'];

    $this->pdf->widthA = array(1,95,25,5,25,5,25,5,25,5,25,5,25,5,25,5);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R');
    $this->pdf->widthB = array(1,95,30,10,30,115);
		$this->pdf->alignB = array('L','L','R','R','R');
		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
    $posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->AddPage();

    //$this->pdf->row(array("",vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal),"",""));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);

	  $this->categorien = $this->getAttributieCategorien();
 		$waardenPerGrootboek = $this->waardenPerGrootboek();
		$this->waardenPerGrootboek = $waardenPerGrootboek;

    $attributieCategorieGrootboek['Opbrengst'] = $tmp['opbrengst'];
    $attributieCategorieGrootboek['Kosten'] = $tmp['kosten'];
    $this->attributieGrootboekPeriode = $attributieCategorieGrootboek;
    $waarde = $this->bepaalCategorieWaarden();
    $row = $this->createRows();
		$this->pdf->row($row['waardeVanaf']);
		$this->pdf->CellBorders = array('','','U','','U','','U','','U','','U','','U');
		$this->pdf->row($row['waardeTot']);
		$this->pdf->CellBorders = array();
		$this->pdf->ln();

		$this->pdf->row($row['mutatiewaarde']);
		$this->pdf->row($row['totaalStortingen']);
 		$this->pdf->row($row['totaalOnttrekkingen']);
 		$this->pdf->row($row['directeOpbrengsten']);
		$this->pdf->CellBorders = array('','','U','','U','','U','','U','','U','','U');
 		$this->pdf->row($row['toegerekendeKosten']);
		$this->pdf->CellBorders = array();
		$this->pdf->ln();

		$this->pdf->CellBorders = array('','','UU','','UU','','UU','','UU','','UU','','UU');
		$this->pdf->row($row['resultaatVerslagperiode']);
	  $this->pdf->CellBorders = array();
		$this->pdf->ln();

		$this->pdf->row($row['rendementProcent']);
		$this->pdf->CellBorders = array('','','UU','','UU','','UU','','UU');
		$this->pdf->row($row['rendementProcentJaar']);
		$this->pdf->CellBorders = array();
	 if($this->pdf->debug)
	 {
		$this->pdf->row(array(''));
			$this->pdf->row($row['directeOpbrengsten']);
			$this->pdf->row($row['toegerekendeKosten']);
			$this->pdf->row($row['gerealiseerdKoersresultaat']);
			$this->pdf->row($row['ongerealiseerdeKoersResultaaten']);
			$this->pdf->row($row['opgelopenRentes']);
			$this->pdf->row($row['totaal']);
	 }
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$ypos = $this->pdf->GetY();
		$this->pdf->SetY($ypos);
		$this->pdf->ln();
		$totaalOpbrengst += $this->waarde['opgelopenRentes']['Totaal'];
		$totaalOpbrengst += $this->waarde['ongerealiseerdeKoersResultaaten']['Totaal'];
		$totaalOpbrengst += $this->waarde['gerealiseerdKoersresultaat']['Totaal'];

		$koersResulaatValutas = 0;
		$totaalOpbrengst += $koersResulaatValutas;
		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];
    $posSubtotaal2 = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3]+ $this->pdf->widthA[4]+ $this->pdf->widthA[5];
		$posSubtotaal2End = $posSubtotaal2 + $this->pdf->widthA[4];
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->ln();
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);


    $gemiddelde=($this->waarden['rapportagePeriode']['resultaat']['Totaal']/$this->waarden['rapportagePeriode']['performance']['Totaal']*100);
 	  if(round($this->waarde['ongerealiseerdeKoersResultaaten']['Totaal'],2) != 0.00)
 	   	$this->pdf->row(array("",vertaalTekst("Ongerealiseerde koersresultaten",$this->pdf->rapport_taal),
                               $this->formatGetal($this->waarde['ongerealiseerdeKoersResultaaten']['Totaal'],$this->bedragDecimalen),
                               "","","",
                               $this->formatGetal($this->waarde['ongerealiseerdeKoersResultaaten']['Totaal']/$gemiddelde*100,$this->bedragDecimalen),'%'));
 	 	if(round($this->waarde['gerealiseerdKoersresultaat']['Totaal'],2) != 0.00)
	    $this->pdf->row(array("",vertaalTekst("Gerealiseerde koersresultaten",$this->pdf->rapport_taal),
                               $this->formatGetal($this->waarde['gerealiseerdKoersresultaat']['Totaal'],$this->bedragDecimalen),
                               "","","",
                               $this->formatGetal($this->waarde['gerealiseerdKoersresultaat']['Totaal']/$gemiddelde*100,$this->bedragDecimalen),'%'));
	  if(round($koersResulaatValutas,2) != 0.00)
	    $this->pdf->row(array("",vertaalTekst("Koersresultaten valuta's",$this->pdf->rapport_taal),
                               $this->formatGetal($koersResulaatValutas,$this->bedragDecimalen),
                               "","","",
                               $this->formatGetal($koersResulaatValutas/$gemiddelde*100,$this->bedragDecimalen),'%'));

  	if(round($this->waarde['opgelopenRentes']['Totaal'],2) != 0.00)
			 $this->pdf->row(array("",vertaalTekst("Resultaat opgelopen rente",$this->pdf->rapport_taal),
                                $this->formatGetal($this->waarde['opgelopenRentes']['Totaal'],$this->bedragDecimalen),
                                "","","",
                                $this->formatGetal($this->waarde['opgelopenRentes']['Totaal']/$gemiddelde*100,$this->bedragDecimalen),'%'));

		foreach ($waardenPerGrootboek['opbrengst'] as $grootboek=>$grootboekWaarden)
		{
		  if(round($grootboekWaarden['bedrag'],2) != 0.00)
			  $this->pdf->row(array("",vertaalTekst($grootboekWaarden['omschrijving'],$this->pdf->rapport_taal),
                                 $this->formatGetal($grootboekWaarden['bedrag'],$this->bedragDecimalen),
                                 "","","",
                                 $this->formatGetal($grootboekWaarden['bedrag']/$gemiddelde*100,$this->bedragDecimalen),'%'));
			  $totaalOpbrengst += $grootboekWaarden['bedrag'];
		}

		$this->pdf->Line($posSubtotaal ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
    $this->pdf->Line($posSubtotaal2 ,$this->pdf->GetY() ,$posSubtotaal2End ,$this->pdf->GetY());
		$this->pdf->row(array("","","","",$this->formatGetal($totaalOpbrengst,$this->bedragDecimalen),
                                 "","","",
                                 $this->formatGetal($totaalOpbrengst/$gemiddelde*100,$this->bedragDecimalen),'%'));
		$this->pdf->ln();
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Kosten",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		foreach ($waardenPerGrootboek['kosten'] as $grootboek=>$grootboekWaarden)
		{
		  if(round($grootboekWaarden['bedrag'],2) != 0.00)
			$this->pdf->row(array("",vertaalTekst($grootboekWaarden['omschrijving'],$this->pdf->rapport_taal),
                                            $this->formatGetal($grootboekWaarden['bedrag'],$this->bedragDecimalen),
                                            "","","",
                                            $this->formatGetal($grootboekWaarden['bedrag']/$gemiddelde*-100,$this->bedragDecimalen),'%'));
		}

		$this->pdf->Line($posSubtotaal ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
    $this->pdf->Line($posSubtotaal2 ,$this->pdf->GetY() ,$posSubtotaal2End ,$this->pdf->GetY());
		$this->pdf->row(array("","","","",$this->formatGetal($waardenPerGrootboek['totaalKosten'],$this->bedragDecimalen),
                                      "","","",
                                       $this->formatGetal($waardenPerGrootboek['totaalKosten']/$gemiddelde*-100,$this->bedragDecimalen),'%'));

		$posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3];
		$this->pdf->Line($posTotaal +2 ,$this->pdf->GetY() ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY());
		$this->pdf->row(array("","","","",$this->formatGetal($totaalOpbrengst - $waardenPerGrootboek['totaalKosten'],$this->bedragDecimalen)));
		$this->pdf->Line($posTotaal +2 ,$this->pdf->GetY() ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY());
		$this->pdf->Line($posTotaal +2 ,$this->pdf->GetY()+1 ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY()+1);

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$RapJaar = date("Y", db2jul($this->rapportageDatum));
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));

	 if($this->pdf->debug)
	 {
	   listarray($this->berekening->performance);flush();
	   exit;
   }
	}

}

?>
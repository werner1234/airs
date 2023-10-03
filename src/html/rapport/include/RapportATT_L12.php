<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/02/22 18:46:19 $
File Versie					: $Revision: 1.15 $

$Log: RapportATT_L12.php,v $
Revision 1.15  2020/02/22 18:46:19  rvv
*** empty log message ***

Revision 1.14  2018/01/13 19:10:28  rvv
*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once("rapport/rapportATTberekening.php");
include_once("rapport/include/rapportATTberekening_L12.php");
//include_once("rapport/include/ATTberekening_L4.php");

class RapportATT_L12
{

	function RapportATT_L12($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
              GROUP BY BeleggingssectorPerFonds.AttributieCategorie
              ORDER By AttributieCategorien.Afdrukvolgorde";
		$this->db->SQL($query);
		$this->db->Query();
		$this->categorien[] = 'Totaal';
		$categorieKop[] = 'Totaal';
		while($categorie = $this->db->nextRecord())
		{
		  $categorieKop[]=$categorie['Omschrijving'];
		  $this->categorien[]=$categorie['AttributieCategorie'];
		}
		if(!in_array('Liquiditeiten',$this->categorien))
		{
		  $categorieKop[]='Liquiditeiten';
		  $this->categorien[]='Liquiditeiten';
		}


		return $this->categorien;
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
  
    $tmpLiq=array();
    foreach ($this->categorien as $categorie=>$categorieOmschrijving)
    {
      if($categorie=='Liquiditeiten')
        continue;
    //  echo $categorie;
    //  listarray($this->waarden['rapportagePeriode'][$categorie]);
      if($categorie=='totaal')
      {
        $tmpLiq['opbrengst'] += $this->waarden['rapportagePeriode'][$categorie]['opbrengst'];
        $tmpLiq['kosten'] += $this->waarden['rapportagePeriode'][$categorie]['kosten'];
      }
      else
      {
        $tmpLiq['opbrengst'] -= $this->waarden['rapportagePeriode'][$categorie]['opbrengst'];
        $tmpLiq['kosten'] -= $this->waarden['rapportagePeriode'][$categorie]['kosten'];
      }
    }
    
    
    foreach ($this->categorien as $categorie=>$categorieOmschrijving)
    {


      array_push($row['waardeVanaf'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['beginwaarde'],$this->bedragDecimalen,true));
      array_push($row['waardeVanaf'],"");

      array_push($row['waardeTot'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['eindwaarde'],$this->bedragDecimalen));
      array_push($row['waardeTot'],"");

      array_push($row['mutatiewaarde'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['eindwaarde']-$this->waarden['rapportagePeriode'][$categorie]['beginwaarde'],$this->bedragDecimalen));
      array_push($row['mutatiewaarde'],"");
  
      if ($categorie == 'Liquiditeiten')
      {
        array_push($row['totaalStortingen'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['storting']+$tmpLiq['opbrengst'],$this->bedragDecimalen));
        array_push($row['totaalOnttrekkingen'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['onttrekking']+$tmpLiq['kosten'],$this->bedragDecimalen));
        array_push($row['rendementProcent'],' ');
        array_push($row['rendementProcent'],' ');
        array_push($row['rendementProcentJaar'],' ');
        array_push($row['rendementProcentJaar'],' ');
        array_push($row['directeOpbrengsten'],$this->formatGetal($tmpLiq['opbrengst'],$this->bedragDecimalen));
        array_push($row['toegerekendeKosten'],$this->formatGetal($tmpLiq['kosten'],$this->bedragDecimalen));
      }
      else
      {
        array_push($row['totaalStortingen'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['storting'],$this->bedragDecimalen));
        array_push($row['totaalOnttrekkingen'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['onttrekking'],$this->bedragDecimalen));
        array_push($row['rendementProcent'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['procent'],2));
        array_push($row['rendementProcent'],'%');
        array_push($row['rendementProcentJaar'],$this->formatGetal($this->waarden['lopendeJaar'][$categorie]['procent'],2));
        array_push($row['rendementProcentJaar'],'%');
        if ($categorie == 'totaal')
        {
          array_push($row['directeOpbrengsten'],'-');
          array_push($row['toegerekendeKosten'],'-');
        }
        else
        {
          array_push($row['directeOpbrengsten'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['opbrengst'],$this->bedragDecimalen));
          array_push($row['toegerekendeKosten'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['kosten'],$this->bedragDecimalen));
        }
      }

      array_push($row['totaalStortingen'],"");
      array_push($row['totaalOnttrekkingen'],"");


      array_push($row['directeOpbrengsten'],"");
      array_push($row['toegerekendeKosten'],"");

      array_push($row['resultaatVerslagperiode'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['resultaat'],$this->bedragDecimalen));
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
				  $waardenPerGrootboek['kosten']['KOST']['omschrijving'] = "Transactiekosten";
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
    $query="SELECT Grootboekrekening,Omschrijving FROM Grootboekrekeningen";
    $DB->SQL($query);
    $DB->query();
    $grootboekOmschrijvingen=array();
    while($gb=$DB->nextRecord())
    {
      $grootboekOmschrijvingen[$gb['Grootboekrekening']]=$gb['Omschrijving'];
    }


    
    $this->berekening = new rapportATTberekening_L12($this);//rapportATTberekening_L12($this->pdata);
   // $this->berekening=new ATTberekening_L4($this);
    $this->waarden=array();
    $this->waarden['rapportagePeriode']=$this->berekening->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'attributie',$this->pdf->rapportageValuta);
    $this->waarden['lopendeJaar']=$this->berekening->bereken($this->tweedePerformanceStart ,$this->rapportageDatum,'attributie',$this->pdf->rapportageValuta);
    $this->categorien=$categorien=$this->berekening->categorien;
    if($_POST['debug']==1)
    {
      listarray($this->waarden);
    }
    $w=30;
    $this->pdf->widthA = array(0,95,$w,5,$w,5,$w,5,$w,5,$w,5,$w,5,$w,6);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R');
    $this->pdf->widthB = array(0,95,30,10,30,116);
		$this->pdf->alignB = array('L','L','R','R','R');
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->AddPage();

    //$this->pdf->row(array("",vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal),"",""));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);


    //$this->pdf->row(array(""));
    $kopRegel = array();
    array_push($kopRegel,"");
    array_push($kopRegel,"");
    foreach ($categorien as $omschrijving)
    {
      array_push($kopRegel,$omschrijving);
      array_push($kopRegel,"");
    }
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
    $this->pdf->row($kopRegel);
    

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

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$ypos = $this->pdf->GetY();
		$this->pdf->SetY($ypos);
		$this->pdf->ln();

		$koersResulaatValutas = 0;
		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
	//	$this->pdf->ln();
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		
		//listarray($this->waarden['rapportagePeriode']['totaal']);
		$resultaatVelden=array('gerealiseerdFondsResultaat'=>'Gerealiseerde fonds resultaten','gerealiseerdValutaResultaat'=>'Gerealiseerde valuta resultaten',
      'ongerealiseerdFondsResultaat'=>'Ongerealiseerde fonds resultaten','ongerealiseerdValutaResultaat'=>'Ongerealiseerde valuta resultaten','opgelopenrente'=>'Resultaat opgelopen rente');
		$totaalOpbrengst=0;
		foreach($resultaatVelden as $veld=>$omschrijving)
    {
      if (round($this->waarden['rapportagePeriode']['totaal'][$veld], 2) != 0.00)
      {
        $this->pdf->row(array("", vertaalTekst($omschrijving, $this->pdf->rapport_taal), $this->formatGetal($this->waarden['rapportagePeriode']['totaal'][$veld], 2), ""));
        $totaalOpbrengst+=$this->waarden['rapportagePeriode']['totaal'][$veld];
      }
    }
    $koersResultaatValuta=$this->waarden['rapportagePeriode']['totaal']['resultaat']
      -($totaalOpbrengst+($this->waarden['rapportagePeriode']['totaal']['opbrengst'])
      +$this->waarden['rapportagePeriode']['totaal']['kosten']);
		
    $this->pdf->row(array("", vertaalTekst('Koersresultaten valuta\'s', $this->pdf->rapport_taal), $this->formatGetal($koersResultaatValuta, 2), ""));
    $totaalOpbrengst+=$koersResultaatValuta;
		foreach ($this->waarden['rapportagePeriode']['totaal']['grootboekOpbrengsten'] as $grootboek=>$bedrag)
		{
		  if(round($bedrag) != 0.00)
			  $this->pdf->row(array("",vertaalTekst($grootboekOmschrijvingen[$grootboek],$this->pdf->rapport_taal),$this->formatGetal($bedrag,$this->bedragDecimalen),""));
      $totaalOpbrengst+=$bedrag;
		}



		$this->pdf->Line($posSubtotaal ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->row(array("","","","",$this->formatGetal($totaalOpbrengst,$this->bedragDecimalen)));
//		$this->pdf->ln();
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Kosten",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
  
		$kostenTotaal=0;
    foreach ($this->waarden['rapportagePeriode']['totaal']['grootboekKosten'] as $grootboek=>$bedrag)
		{
		  if(round($bedrag,2) != 0.00)
			$this->pdf->row(array("",vertaalTekst($grootboekOmschrijvingen[$grootboek],$this->pdf->rapport_taal),$this->formatGetal($bedrag,$this->bedragDecimalen),""));
      $kostenTotaal+=$bedrag;
		}

		$this->pdf->Line($posSubtotaal ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->row(array("","","","",$this->formatGetal($kostenTotaal,$this->bedragDecimalen)));

		$posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3];
		$this->pdf->Line($posTotaal +2 ,$this->pdf->GetY() ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY());
		$this->pdf->row(array("","","","",$this->formatGetal($totaalOpbrengst + $kostenTotaal,$this->bedragDecimalen)));
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
<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/11/27 19:18:01 $
File Versie					: $Revision: 1.7 $

$Log: RapportATT_L36.php,v $
Revision 1.7  2019/11/27 19:18:01  rvv
*** empty log message ***

Revision 1.5  2015/12/30 19:01:23  rvv
*** empty log message ***

Revision 1.4  2015/12/09 19:46:07  rvv
*** empty log message ***

Revision 1.3  2013/07/13 15:19:44  rvv
*** empty log message ***

Revision 1.2  2012/06/13 14:35:39  rvv
*** empty log message ***

Revision 1.1  2012/06/09 13:43:40  rvv
*** empty log message ***

Revision 1.4  2012/05/30 16:02:38  rvv
*** empty log message ***

Revision 1.3  2012/05/27 08:33:10  rvv
*** empty log message ***

Revision 1.2  2012/05/20 06:44:07  rvv
*** empty log message ***

Revision 1.1  2012/05/12 15:11:00  rvv
*** empty log message ***

Revision 1.24  2012/04/01 07:39:25  rvv
*** empty log message ***

*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L36.php");

class RapportATT_L36
{

	function RapportATT_L36($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

	  $this->db = new DB();


	  $this->categorien=array('totaal'=>'Totaal');
	  $query="SELECT
Beleggingscategorien.Afdrukvolgorde,
Beleggingscategorien.Beleggingscategorie,
Beleggingscategorien.Omschrijving
FROM
Beleggingscategorien
Inner Join CategorienPerHoofdcategorie ON CategorienPerHoofdcategorie.Hoofdcategorie = Beleggingscategorien.Beleggingscategorie
WHERE CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
GROUP BY Beleggingscategorien.Beleggingscategorie
ORDER BY Beleggingscategorien.Afdrukvolgorde";
	  $this->db->SQL($query);
	  $this->db->Query();
	  while($data=$this->db->nextRecord())
	  {
	    $this->categorien[$data['Beleggingscategorie']]=$data['Omschrijving'];
	  }







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


    $this->pdf->row(array(""));
    $kopRegel = array();
	  array_push($kopRegel,"");
	  array_push($kopRegel,"");
    foreach ($this->categorien as $categorie=>$omschrijving)
    {
		  array_push($kopRegel,vertaalTekst($omschrijving,$this->pdf->rapport_taal));
		  array_push($kopRegel,"");
    }
		$this->pdf->row($kopRegel);

		$this->waarden=$this->pdf->hcatData;

    foreach ($this->categorien as $categorie=>$omschrijving)
    {
      $this->waarden['rapportagePeriode'][$categorie]['mutatie']=$this->waarden['rapportagePeriode'][$categorie]['eindwaarde']-$this->waarden['rapportagePeriode'][$categorie]['beginwaarde'];

      if($categorie=='totaal')
      {
        $this->waarden['rapportagePeriode'][$categorie]['kosten']=0;
        $this->waarden['rapportagePeriode'][$categorie]['opbrengst']=0;
        $this->waarden['rapportagePeriode'][$categorie]['storting']=getStortingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
        $this->waarden['rapportagePeriode'][$categorie]['onttrekking']=getOnttrekkingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
        $resultaatVerslagperiode[$categorie] = $this->waarden['rapportagePeriode'][$categorie]['mutatie'] -
        $this->waarden['rapportagePeriode'][$categorie]['storting'] +
        $this->waarden['rapportagePeriode'][$categorie]['onttrekking'] +
        $this->waarden['rapportagePeriode'][$categorie]['kosten'] +
        $this->waarden['rapportagePeriode'][$categorie]['opbrengst'];

      }
      else
      {
        $this->waarden['rapportagePeriode'][$categorie]['storting']=$this->waarden['rapportagePeriode'][$categorie]['storting']*-1;
        $this->waarden['rapportagePeriode'][$categorie]['onttrekking']=$this->waarden['rapportagePeriode'][$categorie]['onttrekking']*-1;
        $resultaatVerslagperiode[$categorie] = $this->waarden['rapportagePeriode'][$categorie]['mutatie'] +
        $this->waarden['rapportagePeriode'][$categorie]['storting'] +
        $this->waarden['rapportagePeriode'][$categorie]['onttrekking'] +
        $this->waarden['rapportagePeriode'][$categorie]['kosten'] +
        $this->waarden['rapportagePeriode'][$categorie]['opbrengst'];
      }

      array_push($row['waardeVanaf'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['beginwaarde'],$this->bedragDecimalen,true));
      array_push($row['waardeVanaf'],"");

      array_push($row['waardeTot'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['eindwaarde'],$this->bedragDecimalen));
      array_push($row['waardeTot'],"");

      array_push($row['mutatiewaarde'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['mutatie'],$this->bedragDecimalen));
      array_push($row['mutatiewaarde'],"");

      array_push($row['totaalStortingen'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['storting'],$this->bedragDecimalen));
      array_push($row['totaalOnttrekkingen'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['onttrekking'],$this->bedragDecimalen));
      array_push($row['rendementProcent'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['procent'],2));
      array_push($row['rendementProcent'],'%');
      array_push($row['rendementProcentJaar'],$this->formatGetal($this->waarden['lopendeJaar'][$categorie]['procent'],2));
      array_push($row['rendementProcentJaar'],'%');

      array_push($row['totaalStortingen'],"");
      array_push($row['totaalOnttrekkingen'],"");

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
      array_push($row['directeOpbrengsten'],"");
      array_push($row['toegerekendeKosten'],"");

      array_push($row['resultaatVerslagperiode'],$this->formatGetal($resultaatVerslagperiode[$categorie],$this->bedragDecimalen));
      array_push($row['resultaatVerslagperiode'],"");
   }
  return $row;
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


$att=new ATTberekening_L36($this);
$hcatData=$att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta,'hoofdcategorie');
$this->pdf->hcatData['rapportagePeriode']=$hcatData;
$hcatData=$att->bereken($this->tweedePerformanceStart,$this->rapportageDatum,$this->pdf->rapportageValuta,'hoofdcategorie');
$this->pdf->hcatData['lopendeJaar']=$hcatData;

 		$waardenPerGrootboek = $this->waardenPerGrootboek();
		$this->waardenPerGrootboek = $waardenPerGrootboek;
    $attributieCategorieGrootboek['Opbrengst'] = $tmp['opbrengst'];
    $attributieCategorieGrootboek['Kosten'] = $tmp['kosten'];
    $this->attributieGrootboekPeriode = $attributieCategorieGrootboek;
    $waarde = $this->bepaalCategorieWaarden();



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
    $this->pdf->templateVars['ATTPaginas']=$this->pdf->customPageNo;

    //$this->pdf->row(array("",vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal),"",""));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);


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
//		$this->pdf->ln();
		$totaalOpbrengst += $this->waarde['opgelopenRentes']['Totaal'];
		$totaalOpbrengst += $this->waarde['ongerealiseerdeKoersResultaaten']['Totaal'];
		$totaalOpbrengst += $this->waarde['gerealiseerdKoersresultaat']['Totaal'];

		$koersResulaatValutas = 0;
		$totaalOpbrengst += $koersResulaatValutas;
		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->ln();
		$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);

		$this->pdf->row(array("",vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal),"",""));
				$y=$this->pdf->getY();
		$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

  	  if(round($this->waarde['ongerealiseerdeKoersResultaaten']['Totaal'],2) != 0.00)
  	   	$this->pdf->row(array("",vertaalTekst("Ongerealiseerde koersresultaten",$this->pdf->rapport_taal),$this->formatGetal($this->waarde['ongerealiseerdeKoersResultaaten']['Totaal'],$this->bedragDecimalen),""));
  	 	if(round($this->waarde['gerealiseerdKoersresultaat']['Totaal'],2) != 0.00)
		    $this->pdf->row(array("",vertaalTekst("Gerealiseerde koersresultaten",$this->pdf->rapport_taal),$this->formatGetal($this->waarde['gerealiseerdKoersresultaat']['Totaal'],$this->bedragDecimalen),""));
		  if(round($koersResulaatValutas,2) != 0.00)
		    $this->pdf->row(array("",vertaalTekst("Koersresultaten valuta's",$this->pdf->rapport_taal),$this->formatGetal($koersResulaatValutas,$this->bedragDecimalen),""));

  	if(round($this->waarde['opgelopenRentes']['Totaal'],2) != 0.00)
			 $this->pdf->row(array("",vertaalTekst("Resultaat opgelopen rente",$this->pdf->rapport_taal),$this->formatGetal($this->waarde['opgelopenRentes']['Totaal'],$this->bedragDecimalen),""));

		foreach ($waardenPerGrootboek['opbrengst'] as $grootboek=>$grootboekWaarden)
		{
		  if(round($grootboekWaarden['bedrag'],2) != 0.00)
			  $this->pdf->row(array("",vertaalTekst($grootboekWaarden['omschrijving'],$this->pdf->rapport_taal),$this->formatGetal($grootboekWaarden['bedrag'],$this->bedragDecimalen),""));
		  $totaalOpbrengst += $grootboekWaarden['bedrag'];
		}
		$this->pdf->Line($posSubtotaal ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->row(array("","",$this->formatGetal($totaalOpbrengst,$this->bedragDecimalen)));
		$this->pdf->ln();
		//listarray($this->pdf->widthB);exit;
		$this->pdf->SetWidths(array(129,80,30,10,30,100));
		$this->pdf->SetAligns($this->pdf->alignB);

		$this->pdf->setY($y);
		$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Kosten",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		//$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		foreach ($waardenPerGrootboek['kosten'] as $grootboek=>$grootboekWaarden)
		{
		  if(round($grootboekWaarden['bedrag'],2) != 0.00)
			$this->pdf->row(array("",vertaalTekst($grootboekWaarden['omschrijving'],$this->pdf->rapport_taal),$this->formatGetal($grootboekWaarden['bedrag'],$this->bedragDecimalen),""));
		}

		//$this->pdf->Line($posSubtotaal ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$posTotaal = 129+80+$this->pdf->marge+5;
		$this->pdf->Line($posTotaal +2 ,$this->pdf->GetY() ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY());
		$this->pdf->row(array("","",$this->formatGetal($waardenPerGrootboek['totaalKosten'],$this->bedragDecimalen)));


		$this->pdf->ln();
		$this->pdf->ln();
		$this->pdf->row(array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($totaalOpbrengst - $waardenPerGrootboek['totaalKosten'],$this->bedragDecimalen)));
		//$posTotaal+=40;
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



  function bepaalCategorieWaarden()
  {
       $categorie = 'Totaal';
  	    $gerealiseerdKoersresultaat[$categorie] = gerealiseerdKoersresultaat($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta,true,$categorie);

	 		  $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS totaalB, ".
 						 "SUM(beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin." AS totaalA ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND ".
						 " type = 'fondsen' ".$this->__appvar['TijdelijkeRapportageMaakUniek'];
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


    $waarden=array('gerealiseerdKoersresultaat'=>$gerealiseerdKoersresultaat,
                   'ongerealiseerdeKoersResultaaten'=>$ongerealiseerdeKoersResultaaten,
                   'opgelopenRentes'=>$opgelopenRentes);
    $this->waarde = $waarden;
  return $waarden;
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

}

?>
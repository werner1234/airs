<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/08 09:28:24 $
File Versie					: $Revision: 1.20 $

$Log: RapportPERF_L40.php,v $
Revision 1.20  2020/03/08 09:28:24  rvv
*** empty log message ***

Revision 1.19  2020/03/07 16:58:38  rvv
*** empty log message ***

Revision 1.18  2016/02/06 16:42:56  rvv
*** empty log message ***

Revision 1.17  2016/01/10 08:52:29  rvv
*** empty log message ***

Revision 1.16  2016/01/09 18:58:30  rvv
*** empty log message ***

Revision 1.15  2015/01/12 11:20:53  rvv
*** empty log message ***

Revision 1.14  2015/01/11 12:48:50  rvv
*** empty log message ***

Revision 1.13  2015/01/03 16:09:20  rvv
*** empty log message ***

Revision 1.12  2013/07/13 15:19:44  rvv
*** empty log message ***

Revision 1.11  2013/06/12 18:46:36  rvv
*** empty log message ***

Revision 1.10  2012/11/01 10:09:29  rvv
*** empty log message ***

Revision 1.9  2012/10/17 15:55:14  rvv
*** empty log message ***

Revision 1.8  2012/10/07 14:57:18  rvv
*** empty log message ***

Revision 1.7  2012/10/04 08:26:42  rvv
*** empty log message ***

Revision 1.6  2012/10/02 16:17:32  rvv
*** empty log message ***

Revision 1.5  2012/09/30 11:18:17  rvv
*** empty log message ***

Revision 1.4  2012/09/23 08:51:44  rvv
*** empty log message ***

Revision 1.3  2012/09/19 16:53:18  rvv
*** empty log message ***

Revision 1.2  2012/09/16 12:45:46  rvv
*** empty log message ***

Revision 1.1  2012/09/01 14:27:48  rvv
*** empty log message ***

Revision 1.1  2012/08/01 16:57:55  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportPERF_L40
{

	function RapportPERF_L40($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		if($this->pdf->rapport_PERF_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_PERF_titel;
		else
			$this->pdf->rapport_titel = "Performancemeting (in euro)";


		//$this->pdf->rapport_PERF_displayType

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }

  /*
function getTransformedData($begin,$eind)
{
	global $__appvar;
  $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,$eind,(substr($eind,5,5)=='01-01')?true:false,$this->pdf->portefeuilledata['RapportageValuta'],$begin);
  vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,$eind);
  $processor = new PreProcessor_L40($this->portefeuille, $eind);
  $select="SELECT * FROM TijdelijkeRapportage WHERE Portefeuille='".$this->portefeuille."' AND rapportageDatum='$eind' ".$__appvar['TijdelijkeRapportageMaakUniek'];
  
  $db=new DB();
  $db->SQL($select);
  $db->query();
  $regels=array();
  while($data=$db->nextRecord())
	{
		$regels[]=$data;
	}
	return $regels;
}
  */
  
	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		if(true)
		{
		  $perfVanafJul=db2jul($this->pdf->portefeuilledata['startdatumMeerjarenrendement']);
      $portefeuilleStartJul=db2jul($this->pdf->PortefeuilleStartdatum);
			$rapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
      $janJul=db2jul("$rapStartJaar-01-01");
      
      $janStartJul=$janJul;
      if($portefeuilleStartJul > $janJul)
        $janStartJul=$portefeuilleStartJul;
      if($perfVanafJul > $janStartJul)
        $janStartJul=$perfVanafJul;

      $this->janStart=date("Y-m-d",$janStartJul);

      $startDatum =  $this->janStart;//substr($this->pdf->PortefeuilleStartdatum,0,10);

      if($this->janStart <> $this->pdf->rapport_datumvanaf)
      {
        if(substr($this->janStart,5,5)=='01-01')
         $minDag=true;
        else
         $minDag=false; 
		    $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,$this->janStart,$minDag);
        vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,$this->janStart);
        $processor= new PreProcessor_L40($this->portefeuille,$this->janStart,$this->pdf);
      }
     
      if($perfVanafJul > $this->pdf->rapport_datumvanaf)
        $this->rapportageDatumVanaf=date('Y-m-d',$perfVanafJul);


		}





		$kopStyle = "u";

		$DB = new DB();

		// voor data
		$this->pdf->widthA = array(5,70,35,5,35,10,30,92);
		$this->pdf->alignA = array('L','L','R','R','R');

		// voor kopjes
		$this->pdf->widthB = array(1,75,35,5,35,10,30,92);
		$this->pdf->alignB = array('L','L','R','R','R');

    $this->pdf->widthC = array(160,70,25,2,25,2,25,1);
		$this->pdf->AddPage();

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

	  $data['periode']=$this->getWaarden($this->rapportageDatumVanaf,$this->rapportageDatum);
	  $data['ytm']=$this->getWaarden($startDatum,$this->rapportageDatum);

   $perioden=array($startDatum,$this->rapportageDatumVanaf,$this->rapportageDatum);
		// ***************************** einde ophalen data voor afdruk ************************ //

		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];

		$extraLengte = $this->pdf->rapport_PERF_lijnenKorter;

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->ln();
    
    $startPeriodeTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf));
    $startJaarTxt=date("j",db2jul($startDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($startDatum))],$this->pdf->taal)." ".date("Y",db2jul($startDatum));
    $eindPeriodeTxt=date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum));

    $ypos = $this->pdf->GetY();
		//$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
		//$this->pdf->row(array("",vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal),$startPeriodeTxt,'',$startJaarTxt));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $beginY=$this->pdf->GetY();
    $maxY=$beginY;
    
    $categorien=array();
    foreach($perioden as $periode)
    {
      foreach($data['periode']['verdeling'][$periode] as $categorie=>$waarde)
        $categorien[$categorie]=$categorie;
      foreach($data['ytm']['verdeling'][$periode] as $categorie=>$waarde)
        $categorien[$categorie]=$categorie; 
    }        

    foreach($categorien as $categorie)
      $this->pdf->row(array("",$categorie,$this->formatGetal($data['periode']['verdeling'][$this->rapportageDatumVanaf][$categorie],2),'',$this->formatGetal($data['ytm']['verdeling'][$startDatum][$categorie],2)));
      
    $this->pdf->CellBorders=array('','','T','','T');
		$this->pdf->row(array("","",$this->formatGetal($data['periode']['waardeBegin'],2,true),"",$this->formatGetal($data['ytm']['waardeBegin'],2,true),""));
		unset($this->pdf->CellBorders);
    
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal),$eindPeriodeTxt,'',$eindPeriodeTxt));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach($categorien as $categorie)
    {

      $this->pdf->row(array("",$categorie,$this->formatGetal($data['periode']['verdeling'][$this->rapportageDatum][$categorie],2),'',$this->formatGetal($data['ytm']['verdeling'][$this->rapportageDatum][$categorie],2)));
    }
    $this->pdf->CellBorders=array('','','T','','T');
    
    $this->pdf->row(array("","",$this->formatGetal($data['periode']['waardeEind'],2),"",$this->formatGetal($data['ytm']['waardeEind'],2),""));
    unset($this->pdf->CellBorders);
			// subtotaal
		//$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->ln();
		$this->pdf->row(array("",vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal),$this->formatGetal($data['periode']['waardeMutatie'],2),"",$this->formatGetal($data['ytm']['waardeMutatie'],2),""));
		$this->pdf->row(array("",vertaalTekst("Stortingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($data['periode']['stortingen'],2),"",$this->formatGetal($data['ytm']['stortingen'],2),""));
		$this->pdf->CellBorders=array('','','U','','U');
    $this->pdf->row(array("",vertaalTekst("Onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($data['periode']['onttrekkingen'],2),"",$this->formatGetal($data['ytm']['onttrekkingen'],2),""));
		
    $this->pdf->ln();
    $this->pdf->CellBorders=array('','','UU','','UU');
		$this->pdf->row(array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($data['periode']['resultaatVerslagperiode'],2),"",$this->formatGetal($data['ytm']['resultaatVerslagperiode'],2),""));
		 $this->pdf->ln();
     $this->pdf->row(array("",vertaalTekst("Rendement",$this->pdf->rapport_taal),$this->formatGetal($data['periode']['rendementProcent'],1)."%","",$this->formatGetal($data['ytm']['rendementProcent'],1)."%"));
   
    unset($this->pdf->CellBorders);
 		$this->pdf->ln();

		//$this->pdf->row(array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($data['periode']['rendementProcent'],2),"%",$this->formatGetal($data['ytm']['rendementProcent'],2),"%"));

		//$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		//$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY()+1 ,$posSubtotaalEnd ,$this->pdf->GetY()+1);

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		


		$this->pdf->SetY($ypos);

		$this->pdf->SetWidths($this->pdf->widthC);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"Verslagperiode",'',"Lopend jaar"));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->SetWidths($this->pdf->widthC);
		$this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->row(array("",vertaalTekst("Ongerealiseerde koersresultaten",$this->pdf->rapport_taal),
                            $this->formatGetal($data['periode']['ongerealiseerdeKoersResultaat'],2),"",
                            $this->formatGetal($data['ytm']['ongerealiseerdeKoersResultaat'],2),""));
		$this->pdf->row(array("",vertaalTekst("Gerealiseerde koersresultaten",$this->pdf->rapport_taal),$this->formatGetal($data['periode']['gerealiseerdeKoersResultaat'],2),"",$this->formatGetal($data['ytm']['gerealiseerdeKoersResultaat'],2),""));
		if(round($data['periode']['koersResulaatValutas'],2) != 0.00 || round($data['ytm']['koersResulaatValutas'],2) != 0.00)
		  $this->pdf->row(array("",vertaalTekst("Koersresultaten valuta's",$this->pdf->rapport_taal),$this->formatGetal($data['periode']['koersResulaatValutas'],2),"",$this->formatGetal($data['ytm']['koersResulaatValutas'],2),""));
		$this->pdf->row(array("",vertaalTekst("Resultaat opgelopen rente",$this->pdf->rapport_taal),$this->formatGetal($data['periode']['opgelopenRente'],2),"",$this->formatGetal($data['ytm']['opgelopenRente'],2),""));

		$keys=array();
		foreach ($data['ytm']['opbrengstenPerGrootboek'] as $key=>$val)
		  $keys[$key]=$key;      

		foreach ($keys as $key)
		{
		  if(round($data['periode']['opbrengstenPerGrootboek'][$key],2) != 0.00 || round($data['ytm']['opbrengstenPerGrootboek'][$key],2) != 0.00)
			  $this->pdf->row(array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($data['periode']['opbrengstenPerGrootboek'][$key],2),"",$this->formatGetal($data['ytm']['opbrengstenPerGrootboek'][$key],2),""));
		}

    $this->pdf->CellBorders=array('','','T','','T');
		$this->pdf->row(array("","",$this->formatGetal($data['periode']['totaalOpbrengst'],2),"",$this->formatGetal($data['ytm']['totaalOpbrengst'],2)));
		unset($this->pdf->CellBorders);
    //$this->pdf->ln();

		$this->pdf->SetWidths($this->pdf->widthC);
		$this->pdf->SetAligns($this->pdf->alignB);

		$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Kosten",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetWidths($this->pdf->widthC);
		$this->pdf->SetAligns($this->pdf->alignA);

		$keys=array();
 		foreach ($data['ytm']['kostenPerGrootboek'] as $key=>$val)
		  $keys[$key]=$key;         
		foreach ($keys as $key)
		{
		  if(round($data['periode']['kostenPerGrootboek'][$key],2) != 0.00 || round($data['ytm']['kostenPerGrootboek'][$key],2) != 0.00)
			  $this->pdf->row(array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($data['periode']['kostenPerGrootboek'][$key],2),"",$this->formatGetal($data['ytm']['kostenPerGrootboek'][$key],2),""));
		}


    $this->pdf->CellBorders=array('','','T','','T');
		$this->pdf->row(array("","",$this->formatGetal($data['periode']['totaalKosten'],2),"",$this->formatGetal($data['ytm']['totaalKosten'],2)));
    $this->pdf->ln();
    $this->pdf->CellBorders=array('','','UU','','UU');
	  $this->pdf->row(array("","Resultaat over verslagperiode",$this->formatGetal($data['periode']['totaalOpbrengst']-$data['periode']['totaalKosten'],2),"",$this->formatGetal($data['ytm']['totaalOpbrengst']-$data['ytm']['totaalKosten'],2)));
	  unset($this->pdf->CellBorders);
  
		$actueleWaardePortefeuille = 0;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->getIndexWaarden();

	}

	function getWaarden($vanafDatum,$totDatum)
	{
	  global $__appvar;
  	// ***************************** ophalen data voor afdruk ************************ //

  	$waarden=array();
	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	  {
	    $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	    $totRapKoers=getValutaKoers($this->pdf->rapportageValuta,$vanafDatum);
	    $vanRapKoers=getValutaKoers($this->pdf->rapportageValuta,$totDatum);
	  }
	  else
	  {
	    $koersQuery = "";
	    $totRapKoers=1;
	    $vanRapKoers=1;
	  }


    $DB=new DB();

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$totRapKoers." AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$totDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
  
    		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$totRapKoers." AS totaal, hoofdCategorieOmschrijving ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$totDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek']." GROUP BY hoofdCategorie ORDER BY hoofdCategorieVolgorde";
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();
    while($waarde = $DB->nextRecord())
    {
      if($waarde['hoofdCategorieOmschrijving']=='')
        $waarde['hoofdCategorieOmschrijving']='Geen categorie';
      $waarden['verdeling'][$totDatum][$waarde['hoofdCategorieOmschrijving']]=$waarde['totaal'];
    }

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro / ".$vanRapKoers." ) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$vanafDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();
		$totaalWaardeVanaf = $DB->nextRecord();
    
    
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$vanRapKoers." AS totaal, hoofdCategorieOmschrijving ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$vanafDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek']." GROUP BY hoofdCategorie ORDER BY hoofdCategorieVolgorde";
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();
    while($waarde = $DB->nextRecord())
    {
      if($waarde['hoofdCategorieOmschrijving']=='')
        $waarde['hoofdCategorieOmschrijving']='Geen categorie';
      $waarden['verdeling'][$vanafDatum][$waarde['hoofdCategorieOmschrijving']]=$waarde['totaal'];
    }
    
    


		$waardeEind				  = $totaalWaarde['totaal'];
		$waardeBegin 			 	= $totaalWaardeVanaf['totaal'];
		$waardeMutatie 	   	= $waardeEind - $waardeBegin;
		$stortingen 			 	= getStortingen($this->portefeuille,$vanafDatum,$totDatum,$this->pdf->rapportageValuta);
		$onttrekkingen 		 	= getOnttrekkingen($this->portefeuille,$vanafDatum,$totDatum,$this->pdf->rapportageValuta);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
		$rendementProcent  	= performanceMeting($this->portefeuille, $vanafDatum, $totDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
//echo "PERF $vanafDatum, $totDatum $rendementProcent <br>\n";
		$waarden['waardeEind']=$waardeEind;
		$waarden['waardeBegin']=$waardeBegin;
		$waarden['waardeMutatie']=$waardeMutatie;
		$waarden['stortingen']=$stortingen;
		$waarden['onttrekkingen']=$onttrekkingen;
		$waarden['resultaatVerslagperiode']=$resultaatVerslagperiode;
		$waarden['rendementProcent']=$rendementProcent;


		// ophalen van het totaal beginwaare en actuele waarde voor ongerealiseerde koersresultaat
    /*
 		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$totRapKoers."  AS totaalB, ".
 						 "SUM(beginPortefeuilleWaardeEuro)/ ".$vanRapKoers."  AS totaalA ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$totDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND "
						 ." type = 'fondsen' ".$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaal = $DB->nextRecord();
    */
    $fondsen=berekenPortefeuilleWaarde($this->portefeuille,$totDatum,false,$this->pdf->rapportageValuta,$vanafDatum);
    $totaal=array();
    foreach($fondsen as $id=>$regel)
    {
      if($regel['type']=='fondsen')
      {
        $totaal['totaalB']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
        $totaal['totaalA']+=($regel['beginPortefeuilleWaardeEuro']/$totRapKoers);
      }
    }

    //($portefeuille, $rapportageDatum, $min1dag = false, $rapportageValuta = 'EUR',$rapportageBeginDatum)
		$ongerealiseerdeKoersResultaat = $totaal['totaalB'] - $totaal['totaalA']; //huidigeJaarRapdatum - 01-01-HuidigeJaar = OngerealiseerdHuidigeJaar.
		$waarden['ongerealiseerdeKoersResultaat']=$ongerealiseerdeKoersResultaat;

    $RapJaar = date("Y", db2jul($totDatum));
    $RapStartJaar = date("Y", db2jul($vanafDatum));
		$totaalOpbrengst += $ongerealiseerdeKoersResultaat;
		$gerealiseerdeKoersResultaat = gerealiseerdKoersresultaat($this->portefeuille, $vanafDatum, $totDatum,$this->pdf->rapportageValuta,true);
		$totaalOpbrengst += $gerealiseerdeKoersResultaat;
		$waarden['gerealiseerdeKoersResultaat']=$gerealiseerdeKoersResultaat;

		// ophalen van rente totaal A en rentetotaal B
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$totDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND ".
						 " type = 'rente' ".$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalA = $DB->nextRecord();

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$vanafDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND ".
						 " type = 'rente' ". $__appvar['TijdelijkeRapportageMaakUniek'] ;
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalB = $DB->nextRecord();

		$opgelopenRente = ($totaalA['totaal'] - $totaalB['totaal']) / $totRapKoers;
		$totaalOpbrengst += $opgelopenRente;
		$waarden['opgelopenRente']=$opgelopenRente;

		if($this->pdf->GrootboekPerVermogensbeheerder)
		  $query = "SELECT DISTINCT(GrootboekPerVermogensbeheerder.Grootboekrekening), GrootboekPerVermogensbeheerder.Omschrijving FROM GrootboekPerVermogensbeheerder
                WHERE GrootboekPerVermogensbeheerder.Opbrengst = '1' AND GrootboekPerVermogensbeheerder.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
                ORDER BY GrootboekPerVermogensbeheerder.Afdrukvolgorde";
		else
      $query = "SELECT DISTINCT(Grootboekrekeningen.Grootboekrekening), Grootboekrekeningen.Omschrijving FROM Grootboekrekeningen WHERE Grootboekrekeningen.Opbrengst = '1' ORDER BY Grootboekrekeningen.Afdrukvolgorde";


		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		while($gb = $DB->nextRecord())
		{
			$query = "SELECT  ".
		  	"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
		  	"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
		  	"FROM Rekeningmutaties, Rekeningen, Portefeuilles ".
		  	"WHERE ".
		  	"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		  	"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		  	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  	"Rekeningmutaties.Verwerkt = '1' AND ".
		  	"Rekeningmutaties.Boekdatum > '".$vanafDatum."' AND ".
		  	"Rekeningmutaties.Boekdatum <= '".$totDatum."' AND ".
			  "Rekeningmutaties.Grootboekrekening = '".$gb['Grootboekrekening']."' ";

			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();

			while($opbrengst = $DB2->nextRecord())
			{
				$opbrengstenPerGrootboek[$gb['Omschrijving']] =  ($opbrengst['totaalcredit']-$opbrengst['totaaldebet']);
				$totaalOpbrengst += ($opbrengst['totaalcredit'] - $opbrengst['totaaldebet']);
			}
		}
		$waarden['opbrengstenPerGrootboek']=$opbrengstenPerGrootboek;
		$waarden['totaalOpbrengst']=$totaalOpbrengst;

		// loopje over Grootboekrekeningen Kosten = 1
		if($this->pdf->GrootboekPerVermogensbeheerder)
		{
		  $query = "SELECT GrootboekPerVermogensbeheerder.Omschrijving,GrootboekPerVermogensbeheerder.Grootboekrekening, ".
		  "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
		  "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
		  "FROM Rekeningmutaties, Rekeningen, Portefeuilles, GrootboekPerVermogensbeheerder ".
	   	"WHERE ".
		  "Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		  "Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		  "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  "Rekeningmutaties.Verwerkt = '1' AND ".
		  "Rekeningmutaties.Boekdatum > '".$vanafDatum."' AND ".
		  "Rekeningmutaties.Boekdatum <= '".$totDatum."' AND ".
		  "GrootboekPerVermogensbeheerder.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND ".
		  "Rekeningmutaties.Grootboekrekening = GrootboekPerVermogensbeheerder.GrootboekRekening AND ".
		  "GrootboekPerVermogensbeheerder.Kosten = '1' ".
		  "GROUP BY Rekeningmutaties.Grootboekrekening ".
		  "ORDER BY GrootboekPerVermogensbeheerder.Afdrukvolgorde ";
		}
		else
		{
		  $query = "SELECT Grootboekrekeningen.Omschrijving,Grootboekrekeningen.Grootboekrekening, ".
		  "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
		  "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
		  "FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen ".
		  "WHERE ".
		  "Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		  "Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		  "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  "Rekeningmutaties.Verwerkt = '1' AND ".
		  "Rekeningmutaties.Boekdatum > '".$vanafDatum."' AND ".
		  "Rekeningmutaties.Boekdatum <= '".$totDatum."' AND ".
		  "Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND ".
		  "Grootboekrekeningen.Kosten = '1' ".
		  "GROUP BY Rekeningmutaties.Grootboekrekening ".
		  "ORDER BY Grootboekrekeningen.Afdrukvolgorde ";
		}

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		$kostenPerGrootboek = array();

		while($kosten = $DB->nextRecord())
		{
			if($kosten['Grootboekrekening'] == "KNBA")
			{
			  $kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = "Bankkosten en provisie";
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}
			elseif($kosten['Grootboekrekening'] == "KOBU")
			{
			  $kostenPerGrootboek['KOST']['Omschrijving'] = 'Transactiekosten';
				$kostenPerGrootboek['KOST']['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}
			else
			{
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = $kosten['Omschrijving'];
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}


			$totaalKosten += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
		}
					foreach ($kostenPerGrootboek as $data)
			{
			  $tmp[$data['Omschrijving']]=$data['Bedrag'];
			}

		$waarden['kostenPerGrootboek']=$tmp;
		$waarden['totaalKosten']=$totaalKosten;

		$kostenProcent = ($totaalKosten / $waardeEind) * 100;
		$koersResulaatValutas = $resultaatVerslagperiode - ($totaalOpbrengst  -  $totaalKosten);
		$totaalOpbrengst += $koersResulaatValutas;
		$waarden['kostenProcent']=$kostenProcent;
		$waarden['koersResulaatValutas']=$koersResulaatValutas;
		$waarden['totaalOpbrengst']=$totaalOpbrengst;

		return $waarden;
	}

	function getIndexWaarden()
	{
	  $DB = new DB();
$query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE Portefeuille = '".$this->portefeuille."' AND Categorie = 'Totaal' ORDER BY Datum ASC LIMIT 1 ";
$DB->SQL($query);
$DB->Query();
$datum = $DB->nextRecord();


if($datum['id'] > 0 && $this->pdf->lastPOST['perfPstart'] == 1)
{
  if($datum['month'] <10)
    $datum['month'] = "0".$datum['month'];
  $start = $datum['year'].'-'.$datum['month'].'-01';
}
else
{
  $start = $this->rapportageDatumVanaf;

	  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));

	  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
	    $start = $this->pdf->PortefeuilleStartdatum;
	  elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	    $start = $this->pdf->PortefeuilleStartdatum;
	  else
	    $start = "$RapStartJaar-01-01";

    if(db2jul($this->pdf->portefeuilledata['startdatumMeerjarenrendement'])>= db2jul($start))
	    $start = $this->pdf->portefeuilledata['startdatumMeerjarenrendement'];
}
  $eind = $this->rapportageDatum;

$datumStart = db2jul($start);
$datumStop  = db2jul($eind);

$index = new indexHerberekening();
$indexWaarden = $index->getWaarden($start,$eind,$this->portefeuille);


if($this->pdf->portefeuilledata['SpecifiekeIndex'] != '')
{
  $lookupDB = new DB();
  $lookupQuery = "SELECT Fondsen.Omschrijving FROM Fondsen WHERE Fondsen.Fonds = '".$this->pdf->portefeuilledata['SpecifiekeIndex']."'";
  $lookupDB->SQL($lookupQuery);
  $lookupRec = $lookupDB->lookupRecord();
  $indexFondsen[]=$this->pdf->portefeuilledata['SpecifiekeIndex'];
  $indexNaam[$this->pdf->portefeuilledata['SpecifiekeIndex']] = $lookupRec['Omschrijving'];
}

/*$query = "SELECT Indices.Beursindex ,Indices.grafiekKleur
          FROM Indices
          WHERE Indices.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'  ORDER BY Indices.Afdrukvolgorde  ";
$DB->SQL($query);
$DB->Query();
while ($data = $DB->nextRecord())
{
	$indexFondsen[] = $data['Beursindex'];
	$indexKleuren[$data['Beursindex']] = unserialize($data['grafiekKleur']);
}
*/
$query = "SELECT BeleggingscategoriePerFonds.grafiekKleur, BeleggingscategoriePerFonds.Fonds
          FROM  BeleggingscategoriePerFonds
          WHERE BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND BeleggingscategoriePerFonds.Fonds IN('".implode("','",$indexFondsen)."') ";
$DB->SQL($query);
$DB->Query();
while ($data = $DB->nextRecord())
{
  if($data['grafiekKleur'] !='')
	  $indexKleuren[$data['Fonds']] = unserialize($data['grafiekKleur']);
}


//listarray($indexKleuren);exit;
//listarray($indexFondsen);


$aantalWaarden = count($indexWaarden);
foreach ($indexWaarden as $id=>$waarden)
{
  $start = jul2sql(form2jul(substr($waarden['periodeForm'],0,10)));
  $eind = jul2sql(form2jul(substr($waarden['periodeForm'],13)));
  foreach ($indexFondsen as $fonds)
  {
 	  $q0 = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$eind."' AND Fonds = '$fonds'  ORDER BY Datum DESC LIMIT 1" ;
 	  $q1 = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$start."' AND Fonds = '$fonds'  ORDER BY Datum DESC LIMIT 1";
	  $DB->SQL($q0);
	  $DB->Query();
	  $koersEind = $DB->LookupRecord();
	  $DB->SQL($q1);
	  $DB->Query();
	  $koersStart = $DB->LookupRecord();
	  $perf = $koersEind['Koers'] /$koersStart['Koers']  ;
	  if($perf==0)
      $perf =1;
    $indexWaarden[$id]['fondsPerf'][$fonds] = $perf  ;
    
    $jaar=substr($eind,0,4);
   	$kwartaal=ceil(substr($eind,5,2)/3);

    if(empty($indexWaarden[$id-1]['fondsIndex'][$fonds]))
	    $indexWaarden[$id]['fondsIndex'][$fonds] = $indexWaarden[$id]['fondsPerf'][$fonds];
	  else
  	  $indexWaarden[$id]['fondsIndex'][$fonds]  =($indexWaarden[$id]['fondsPerf'][$fonds]*$indexWaarden[$id-1]['fondsIndex'][$fonds]);


   	if(empty($indexTabel['cumulatief'][$fonds]['kwartaal']))
   	  $indexTabel['cumulatief'][$fonds]['kwartaal']=100;

   	if(empty($indexTabel['cumulatief'][$fonds]['cumulatief']))
   	   $indexTabel['cumulatief'][$fonds]['cumulatief']=100;

    $indexTabel['cumulatief'][$fonds]['kwartaal']   = ($indexTabel['cumulatief'][$fonds]['kwartaal']*($perf*100))/100;
    $indexTabel['cumulatief'][$fonds]['cumulatief'] = $indexWaarden[$id]['fondsIndex'][$fonds]*100;//($indexTabel['cumulatief'][$fonds]['cumulatief']*($perf*100))/100;
    
    $indexTabel["$jaar Q$kwartaal"][$fonds]['kwartaal'] = $indexTabel['cumulatief'][$fonds]['kwartaal'];

    if(substr($eind,5,5) == '03-31' || substr($eind,5,5) == '06-30' || substr($eind,5,5) == '09-30' || substr($eind,5,5) == '12-31' || $aantalWaarden == $id)
    {
      $indexTabel['cumulatief'][$fonds]['kwartaal'] = 100;
      $indexTabel["$jaar Q$kwartaal"][$fonds]['cumulatief'] = $indexTabel['cumulatief'][$fonds]['cumulatief'];
    }
    
   
  }
} 
//listarray($indexWaarden);
//listarray($indexTabel);






$n=0;
$minVal = 99;
$maxVal = 101;
foreach ($indexWaarden as $id=>$data)
{
  $grafiekData['portefeuille'][$n]=$data['index'];
  $datumArray[$n] = $data['datum'];
 
  $eind=$data['datum'];
  $jaar=substr($eind,0,4);
  $kwartaal=ceil(substr($eind,5,2)/3); 

  if(empty($indexTabel['cumulatief']['portefeuille']['kwartaal']))
    $indexTabel['cumulatief']['portefeuille']['kwartaal']=100;
    
  $indexTabel['cumulatief']['portefeuille']['kwartaal'] =($indexTabel['cumulatief']['portefeuille']['kwartaal']*(100+$data['performance'])/100);
  $indexTabel["$jaar Q$kwartaal"]['portefeuille']['kwartaal'] = $indexTabel['cumulatief']['portefeuille']['kwartaal'];


  if(substr($eind,5,5) == '03-31' || substr($eind,5,5) == '06-30' || substr($eind,5,5) == '09-30' || substr($eind,5,5) == '12-31' || $aantalWaarden == $id)
  {
    $indexTabel['cumulatief']['portefeuille']['kwartaal'] = 100;
    $indexTabel["$jaar Q$kwartaal"]['portefeuille']['cumulatief'] = $data['index'];
  }

  if($data['index'] != 0)
  {
    $maxVal=max($maxVal,$data['index']);
    $minVal=min($minVal,$data['index']);
  }

  foreach ($data['fondsIndex'] as $fonds=>$waarde)
  {
    $grafiekData[$fonds][$n]=$waarde *100;
    if($waarde != 0)
    {
      $maxVal=max($maxVal,$waarde *100);
      $minVal=min($minVal,$waarde *100);
    }
  }
  $n++;
}


$indexTabelFondsen = array('portefeuille',$this->pdf->portefeuilledata['SpecifiekeIndex']);
$indexTabelFondsen = array('portefeuille');
$tmpArray0 = array('','','Portefeuille',"Benchmark");
$tmpArray0 = array('','','Portefeuille');
$tmpArray1 = array('','Periode');
foreach ($indexTabelFondsen as $fonds)
{
  array_push($tmpArray1,"per kwartaal");
  array_push($tmpArray1,"cumulatief");
}

$startJaar=date("Y",$datumStart);
$stopJaar=date("Y",$datumStop);
$rendementY=145;
if($stopJaar<>$startJaar)
{
  if(($stopJaar-$startJaar)>1)
  {
    $nieuwePagina=true;
    $this->pdf->AddPage();
    $rendementY=40;
    $this->pdf->setY($rendementY);
  }
  else
  {
    $rendementY=125;
    $this->pdf->setY($rendementY);
  }  
}
else
  $this->pdf->setY($rendementY);

//listarray($this->pdf->portefeuilledata['Risicoklasse']);exit;
$this->pdf->setWidths(array(5,100));
$this->pdf->SetFont($this->pdf->rapport_font,'bu',$this->pdf->rapport_fontsize);
$this->pdf->Row(array('','Rendement portefeuille '.($this->pdf->portefeuilledata['Risicoklasse']<>''?' / '.$this->pdf->portefeuilledata['Risicoklasse']:'')));
$this->pdf->Ln();
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
$this->pdf->setAligns(array('L','L','C','C'));
#$this->pdf->CellBorders = array('',array('T','L'),array('T'),array('T','R'));
$this->pdf->CellBorders = array('',array('T','L'),array('T','R'));
$this->pdf->setWidths(array(5,22,22+22,22+22));
$this->pdf->Row($tmpArray0);
#$this->pdf->CellBorders = array('',array('U','L'),'U','U','U',array('U','R'));
$this->pdf->CellBorders = array('',array('U','L'),'U',array('U','R'));
$this->pdf->setWidths(array(5,22,22,22,22,22));
$this->pdf->setAligns(array('L','L','R','R','R','R'));
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
$this->pdf->Row($tmpArray1);

$this->pdf->CellBorders = array('','L','','R');
foreach ($indexTabel as $datum=>$fondsen)
{
  if(is_numeric(substr($datum,0,4)))
  {
    $tmpArray = array('');
    array_push($tmpArray,$datum);

    foreach ($indexTabelFondsen as $fonds)
    {
      $waarden = $indexTabel[$datum][$fonds];
    // echo $fonds." "; listarray($waarden);
      if(in_array($fonds,$indexTabelFondsen))
      {
        if(!empty($waarden['kwartaal']))
          array_push($tmpArray,$this->formatGetal(($waarden['kwartaal']-100),1)."%");
        else
          array_push($tmpArray,"0,0%");

        if(!empty($waarden['cumulatief']))
          array_push($tmpArray,$this->formatGetal(($waarden['cumulatief']-100),1)."%");
        elseif(!empty($indexTabel['cumulatief'][$fonds]['cumulatief']))
          array_push($tmpArray,$this->formatGetal(($indexTabel['cumulatief'][$fonds]['cumulatief']-100),1)."%");
        else
          array_push($tmpArray,"");

      }
    }
    $this->pdf->Row($tmpArray);
  }
}
$this->pdf->CellBorders = array('','T','T','T','T','T');
$this->pdf->Row(array('','','',''));
$this->pdf->CellBorders=array();

$this->pdf->setWidths(array(5,100));
$this->pdf->Row(array('','Het cumulatieve rendement wordt berekend door vermenigvuldiging van de kwartaalrendementen.'));

if($nieuwePagina==true)
  $this->pdf->setY($rendementY);
else
  $this->pdf->SetY($rendementY);
  
$this->pdf->setWidths(array(160,100));
$this->pdf->SetFont($this->pdf->rapport_font,'bu',$this->pdf->rapport_fontsize);
$this->pdf->Row(array('','Indexvergelijking'));
$this->pdf->SetY($rendementY+8);
$this->toonIndexvergelijking();

	}
  
  function toonIndexvergelijking()
  {
    
    $this->pdf->SetWidths(array(160,40,35,35));
		$this->pdf->SetAligns(array('L','L','R','R'));
    
	  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));

	  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  else
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";

    if(db2jul($this->pdf->portefeuilledata['startdatumMeerjarenrendement']) >= db2jul($this->tweedePerformanceStart))
	    $this->tweedePerformanceStart = $this->pdf->portefeuilledata['startdatumMeerjarenrendement'];

	  $DB=new DB();
	  $perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);
    
    	  $query = "SELECT Indices.Beursindex, Fondsen.Omschrijving, Fondsen.Valuta,Indices.toelichting
	  FROM Indices
	  JOIN Fondsen ON Indices.Beursindex = Fondsen.Fonds
	  WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' ORDER BY Afdrukvolgorde";
		$DB->SQL($query);
		$DB->Query();
	  while($index = $DB->nextRecord())
		{
		 	$indexData[$index['Beursindex']]=$index;
      foreach ($perioden as $periode=>$datum)
      {
        $indexData[$index['Beursindex']]['fondsKoers_'.$periode]=getFondsKoers($index['Beursindex'],$datum);
        $indexData[$index['Beursindex']]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
      }
     	$indexData[$index['Beursindex']]['performanceJaar'] = ($indexData[$index['Beursindex']]['fondsKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_jan'])    / ($indexData[$index['Beursindex']]['fondsKoers_jan']/100 );
			$indexData[$index['Beursindex']]['performance'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']) / ($indexData[$index['Beursindex']]['fondsKoers_begin']/100 );
  		$indexData[$index['Beursindex']]['performanceEurJaar'] = ($indexData[$index['Beursindex']]['fondsKoers_eind']*$indexData[$index['Beursindex']]['valutaKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_jan']  *$indexData[$index['Beursindex']]['valutaKoers_jan'])/(  $indexData[$index['Beursindex']]['fondsKoers_jan']*  $indexData[$index['Beursindex']]['valutaKoers_jan']/100 );
			$indexData[$index['Beursindex']]['performanceEur'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind']*$indexData[$index['Beursindex']]['valutaKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin'])/($indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin']/100 );
		}
    
       
  	$this->pdf->CellBorders = array('',array('U','T','L'),array('U','T'),array('U','T','R'));
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	//$this->pdf->row(array("\nOverige indices","\nIndex (in €)",'Rendement verslagperiode in %','Rendement vanaf '.date("d-m-Y",db2jul($this->tweedePerformanceStart)).' in %'));
  	$this->pdf->row(array('',vertaalTekst("\nIndex (in euro)",$this->pdf->rapport_taal),
  	vertaalTekst("Rendement verslagperiode",$this->pdf->rapport_taal),vertaalTekst("Rendement vanaf",$this->pdf->rapport_taal).' '.date("d-m-Y",db2jul($this->tweedePerformanceStart))));

  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	
    $this->pdf->CellBorders = array('',array('L'),'',array('R'));
    $this->pdf->SetWidths(array(160,60,15,35));
    foreach ($indexData as $fonds=>$fondsData)
      $this->pdf->row(array('',$fondsData['Omschrijving'],$this->formatGetal($fondsData['performance'],1)."%",$this->formatGetal($fondsData['performanceJaar'],1)."%"));
    $this->pdf->CellBorders = array('','T','T','T');
    $this->pdf->row(array('','','',''));
    
    unset($this->pdf->CellBorders);
    
  }


}
?>

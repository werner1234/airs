<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/05/26 16:45:07 $
 		File Versie					: $Revision: 1.4 $

 		$Log: RapportVAR_L30.php,v $
 		Revision 1.4  2017/05/26 16:45:07  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2016/10/02 12:38:58  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/01/06 10:09:57  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/09/05 18:19:11  rvv
 		*** empty log message ***
 		
 	
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
include_once($__appvar["basedir"]."/html/rapport/PDFOverzicht.php");

//ini_set('max_execution_time',60);
class RapportVAR_L30
{
	function RapportVAR_L30($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "RISK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

    $this->pdf->rapport_titel = "\n \nRendement & Risicokenmerken 2\nKenmerken van het vastrentende deel van uw portefeuille";

		$this->pdf->rapport_titel2='';

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->excelData=array();

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  else
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";

		$this->perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);

	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		global $__appvar;

		$DB=new DB();
		$query="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS actuelePortefeuilleWaardeEuro
			  FROM TijdelijkeRapportage
			  Left Join Fondsen ON Fondsen.Fonds = TijdelijkeRapportage.Fonds
			  WHERE TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND
			   TijdelijkeRapportage.portefeuille='".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek']."  AND ( TijdelijkeRapportage.hoofdcategorie='G-RISM' OR Fondsen.Lossingsdatum <> '0000-00-00')";
		$DB->SQL($query);
		$waarde=$DB->lookupRecord();
		$waarde=$waarde['actuelePortefeuilleWaardeEuro'];

		if($waarde==0)
			return 0;


    $this->pdf->addPage();
    $this->vastrentendeDeel();


	}


	function printRendement($portefeuille, $rapportageDatum, $rapportageDatumVanaf)
  {
 		global $__appvar;
		$DB= new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$rapportageDatumVanaf."' AND ".
						 " portefeuille = '".$portefeuille."' ".
						 $__appvar['TijdelijkeRapportageMaakUniek'];

		$DB->SQL($query);
		$DB->Query();
		$vergelijkWaarde = $DB->nextRecord();
		$vergelijkWaarde = $vergelijkWaarde['totaal'] /  getValutaKoers($this->pdf->rapportageValuta,$rapportageDatumVanaf);

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$rapportageDatum."' AND ".
						 " portefeuille = '".$portefeuille."' ".
						 $__appvar['TijdelijkeRapportageMaakUniek'];
    $DB->SQL($query);
		$DB->Query();
		$actueleWaardePortefeuille = $DB->nextRecord();
		$this->actueleWaardePortefeuille=$actueleWaardePortefeuille['totaal'];
		$actueleWaardePortefeuille = $actueleWaardePortefeuille['totaal']  / $this->pdf->ValutaKoersEind;
		$resultaat = ($actueleWaardePortefeuille -$vergelijkWaarde - getStortingen($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$this->pdf->rapportageValuta) + getOnttrekkingen($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$this->pdf->rapportageValuta));
		$performance = performanceMeting($portefeuille, $rapportageDatumVanaf, $rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
		$this->pdf->ln(2);

		if(($this->pdf->GetY() + 22) >= $this->pdf->pagebreak)
		{
			$this->pdf->AddPage();
			$this->pdf->ln();
		}

		$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),120,14);
		$this->pdf->ln(2);
		$this->pdf->SetWidths(array(80,40));
  	$this->pdf->SetAligns(array('L','R'));
  	$this->pdf->row(array(vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal),"€ ".$this->pdf->formatGetal($resultaat,2)));
  	$this->pdf->ln(2);
  	$this->pdf->row(array(vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal),$this->pdf->formatGetal($performance,2)." %"));
  	$this->pdf->ln();




  }

  function getFondsKoers($fonds,$datum)
	{
	  $db=new DB();
	  $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
	  $db->SQL($query);
	  $koers=$db->lookupRecord();
	  return $koers['Koers'];
	}

  function printAEXvergelijking()
  {
    $DB = new DB();
    $query = "SELECT Indices.Beursindex, Fondsen.Omschrijving, Fondsen.Valuta
	  FROM Indices JOIN Fondsen ON Indices.Beursindex = Fondsen.Fonds
	  WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' ORDER BY Afdrukvolgorde";
		$DB->SQL($query);
		$DB->Query();
	  while($index = $DB->nextRecord())
		{
		 	$indexData[$index['Beursindex']]=$index;
      foreach ($this->perioden as $periode=>$datum)
      {
        $indexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Beursindex'],$datum);
        $indexData[$index['Beursindex']]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
      }
     	$indexData[$index['Beursindex']]['performanceJaar'] = ($indexData[$index['Beursindex']]['fondsKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_jan'])    / ($indexData[$index['Beursindex']]['fondsKoers_jan']/100 );
			$indexData[$index['Beursindex']]['performance'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']) / ($indexData[$index['Beursindex']]['fondsKoers_begin']/100 );
  		$indexData[$index['Beursindex']]['performanceEurJaar'] = ($indexData[$index['Beursindex']]['fondsKoers_eind']*$indexData[$index['Beursindex']]['valutaKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_jan']  *$indexData[$index['Beursindex']]['valutaKoers_jan'])/(  $indexData[$index['Beursindex']]['fondsKoers_jan']*  $indexData[$index['Beursindex']]['valutaKoers_jan']/100 );
			$indexData[$index['Beursindex']]['performanceEur'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind']*$indexData[$index['Beursindex']]['valutaKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin'])/($indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin']/100 );
  	}

  	$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),120,((count($indexData)+1)*4));
   // $this->pdf->ln(2);
		$this->pdf->SetWidths(array(50,25,25,20));
  	$this->pdf->SetAligns(array('L','R','R','R'));
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	$this->pdf->row(array('Index-vergelijking',dbdate2form($this->perioden['begin']),dbdate2form($this->perioden['eind']),'Perf in %'));
  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	foreach ($indexData as $indexData)
  	  $this->pdf->row(array($indexData['Omschrijving'],$this->formatGetal($indexData['fondsKoers_begin'],2),$this->formatGetal($indexData['fondsKoers_eind'],2),$this->formatGetal($indexData['performance'],2)));
   // $this->pdf->ln(2);
  }

  function printBenchmarkvergelijking()
  {

    $DB = new DB();
    $query = "SELECT Portefeuilles.SpecifiekeIndex,Fondsen.Omschrijving,Fondsen.Valuta
              FROM Portefeuilles Join Fondsen ON Portefeuilles.SpecifiekeIndex = Fondsen.Fonds
              WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";
		$DB->SQL($query);
		$DB->Query();
	  while($index = $DB->nextRecord())
		{
		 	$indexData[$index['SpecifiekeIndex']]=$index;
      foreach ($this->perioden as $periode=>$datum)
      {
        $indexData[$index['SpecifiekeIndex']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['SpecifiekeIndex'],$datum);
        $indexData[$index['SpecifiekeIndex']]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
      }
     	$indexData[$index['SpecifiekeIndex']]['performanceJaar'] = ($indexData[$index['SpecifiekeIndex']]['fondsKoers_eind'] - $indexData[$index['SpecifiekeIndex']]['fondsKoers_jan'])    / ($indexData[$index['SpecifiekeIndex']]['fondsKoers_jan']/100 );
			$indexData[$index['SpecifiekeIndex']]['performance'] =     ($indexData[$index['SpecifiekeIndex']]['fondsKoers_eind'] - $indexData[$index['SpecifiekeIndex']]['fondsKoers_begin']) / ($indexData[$index['SpecifiekeIndex']]['fondsKoers_begin']/100 );
  		$indexData[$index['SpecifiekeIndex']]['performanceEurJaar'] = ($indexData[$index['SpecifiekeIndex']]['fondsKoers_eind']*$indexData[$index['SpecifiekeIndex']]['valutaKoers_eind'] - $indexData[$index['SpecifiekeIndex']]['fondsKoers_jan']  *$indexData[$index['SpecifiekeIndex']]['valutaKoers_jan'])/(  $indexData[$index['SpecifiekeIndex']]['fondsKoers_jan']*  $indexData[$index['SpecifiekeIndex']]['valutaKoers_jan']/100 );
			$indexData[$index['SpecifiekeIndex']]['performanceEur'] =     ($indexData[$index['SpecifiekeIndex']]['fondsKoers_eind']*$indexData[$index['SpecifiekeIndex']]['valutaKoers_eind'] - $indexData[$index['SpecifiekeIndex']]['fondsKoers_begin']*$indexData[$index['SpecifiekeIndex']]['valutaKoers_begin'])/($indexData[$index['SpecifiekeIndex']]['fondsKoers_begin']*$indexData[$index['SpecifiekeIndex']]['valutaKoers_begin']/100 );
  	}

  	$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),120,((count($indexData)+1)*4));
   // $this->pdf->ln(2);
		$this->pdf->SetWidths(array(50,25,25,20));
  	$this->pdf->SetAligns(array('L','R','R','R'));
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	$this->pdf->row(array('Benchmark-vergelijking','','','Perf in %'));
  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	foreach ($indexData as $indexData)
  	  $this->pdf->row(array($indexData['Omschrijving'],$this->formatGetal($indexData['fondsKoers_begin'],2),$this->formatGetal($indexData['fondsKoers_eind'],2),$this->formatGetal($indexData['performance'],2)));
   // $this->pdf->ln(2);
  }

  function printValutaVergelijking()
  {
    $DB= new DB();
  		$query = "SELECT TijdelijkeRapportage.valuta,Valutas.Omschrijving, Valutas.Afdrukvolgorde FROM TijdelijkeRapportage
                Join Valutas ON TijdelijkeRapportage.valuta = Valutas.Valuta WHERE Portefeuille='".$this->portefeuille."' AND TijdelijkeRapportage.valuta <> '".$this->pdf->rapportageValuta."' GROUP BY Valuta
                ORDER BY Valutas.Afdrukvolgorde";
		$DB->SQL($query);
		$DB->Query();
	  while($valuta = $DB->nextRecord())
		{
		  $valutas[]=$valuta['Valuta'];
		  $indexValuta[$valuta['valuta']]=$valuta;
		  foreach ($this->perioden as $periode=>$datum)
      {
        $indexValuta[$valuta['valuta']]['valutaKoers_'.$periode]=getValutaKoers($valuta['valuta'],$datum);
      }
      $indexValuta[$valuta['valuta']]['performanceJaar'] = ($indexValuta[$valuta['valuta']]['valutaKoers_eind'] - $indexValuta[$valuta['valuta']]['valutaKoers_jan'])    / ($indexValuta[$valuta['valuta']]['valutaKoers_jan']/100 );
			$indexValuta[$valuta['valuta']]['performance'] =     ($indexValuta[$valuta['valuta']]['valutaKoers_eind'] - $indexValuta[$valuta['valuta']]['valutaKoers_begin']) / ($indexValuta[$valuta['valuta']]['valutaKoers_begin']/100 );
		}

		if(count($indexValuta)>1)
		{
	  	$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),120,((count($indexValuta)+1)*4));
      // $this->pdf->ln(2);
  		$this->pdf->SetWidths(array(50,25,25,20));
    	$this->pdf->SetAligns(array('L','R','R','R'));
     	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    	$this->pdf->row(array('Valuta','','','Perf in %'));
    	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    	foreach ($indexValuta as $indexData)
    	  $this->pdf->row(array($indexData['Omschrijving'],$this->formatGetal($indexData['valutaKoers_begin'],4),$this->formatGetal($indexData['valutaKoers_eind'],4),$this->formatGetal($indexData['performance'],2)));
      // $this->pdf->ln(2);
		}
  }

  function printRisico()
  {

        $DB= new DB();
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

		$query="SELECT Zorgplicht,Omschrijving FROM Zorgplichtcategorien WHERE Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
		$DB->SQL($query);
		$DB->Query();
		while($zorgp = $DB->nextRecord())
		{
		  $zorgplichtcategorien[$zorgp['Zorgplicht']]=$zorgp['Omschrijving'];
		}
		$zorgplichtcategorien['Overige']='Vastrentende waarden';

		$this->totaalWaarde=$totaalWaarde;
		if(!$this->totaalWaarde)
      $this->totaalWaarde=1;
    $query= "SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->totaalWaarde." as percentage,
    TijdelijkeRapportage.hoofdcategorie
    FROM TijdelijkeRapportage
    WHERE TijdelijkeRapportage.Portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'
    GROUP By TijdelijkeRapportage.hoofdcategorie";
    		$DB->SQL($query);
		$DB->Query();
		while($data= $DB->nextRecord())
		{
		  if($data['hoofdcategorie']=='')
		    $data['hoofdcategorie']='Overige';
		  elseif ($data['hoofdcategorie']=='ZAK')
		    $data['hoofdcategorie']='Zakelijk';

		  $categorieWaarden[$data['hoofdcategorie']]=$data['percentage']*100;
		}


  	$zorgplicht = new Zorgplichtcontrole();
  	$zpwaarde=$zorgplicht->zorgplichtMeting($this->pdf->portefeuilledata,$this->rapportageDatum);

    $tmp=array();
    foreach ($zpwaarde['conclusie'] as $index=>$regelData)
      $tmp[$regelData[0]]=$regelData;

    krsort($tmp);

    $this->pdf->SetAligns(array('L','R','R','R','R'));
   	$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),120,51.3);
  	$this->pdf->SetWidths(array(40,20,20,20,20));
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('Risicoprofiel'."\n".$this->pdf->portefeuilledata['Risicoklasse'],'Minimaal','Maximaal',"Werkelijke\nverdeling","Risico\ngewogen"));
    	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetAligns(array('L','R','R','R','R'));
  	foreach ($tmp as $index=>$regelData)
  	  $this->pdf->row(array($zorgplichtcategorien[$regelData[0]],$zpwaarde['categorien'][$regelData[0]]['Minimum']."%",$zpwaarde['categorien'][$regelData[0]]['Maximum']."%",$this->formatGetal($categorieWaarden[$regelData[0]],1)."%",$regelData[2]."%"));
    $this->pdf->ln();

    $db=new DB();
    $query="SELECT Portefeuilles.OptieToestaan,Portefeuilles.Memo FROM Portefeuilles WHERE Portefeuille='".$this->portefeuille."'";
    $db->SQL($query);
    $optie=$db->lookupRecord();
    if($optie['OptieToestaan']==1)
      $optie['OptieToestaan']='toegestaan';
    else
      $optie['OptieToestaan']='niet toegestaan';

    $query="SELECT profielVastgoed,Memo FROM CRM_naw WHERE Portefeuille='".$this->portefeuille."'";
    $db->SQL($query);
    $crm=$db->lookupRecord();
    if($crm['profielVastgoed']=='J')
      $crm['profielVastgoed']='toegestaan';
    else
      $crm['profielVastgoed']='niet toegestaan';

    //listarray($this->pdf->portefeuilledata);
  	  //ZorgplichtPerRisicoklasse
  	// SELECT ZorgplichtPerRisicoklasse.id, ZorgplichtPerRisicoklasse.Vermogensbeheerder, ZorgplichtPerRisicoklasse.Zorgplicht, ZorgplichtPerRisicoklasse.Risicoklasse, ZorgplichtPerRisicoklasse.Minimum, ZorgplichtPerRisicoklasse.Maximum FROM (ZorgplichtPerRisicoklasse) WHERE
  	$this->pdf->SetWidths(array(40,80));
  	$this->pdf->SetAligns(array('L','L','L','L'));
  	//$this->pdf->row(array('Risicoklasse',$this->pdf->portefeuilledata['Risicoklasse']));
  	$this->pdf->row(array('Opties',$optie['OptieToestaan']));
  	$this->pdf->row(array('Vastgoed',$crm['profielVastgoed']));
  	$this->pdf->row(array('Bijzonderheden',$optie['Memo']));
  }

  function printGrafieken()
  {
    global $__appvar;
    $DB= new DB();
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

		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$kleuren = unserialize($kleuren['grafiek_kleur']);
		$kleurenSec = $kleuren['OIS'];
		$kleurenVal = $kleuren['OIV'];
		$q = "SELECT Valuta, omschrijving FROM Valutas";
		$DB->SQL($q);
		$DB->Query();

		$dbValutacategorien = array();
		while($valta = $DB->NextRecord())
			$dbValutacategorien[$valta['Valuta']] = $valta['omschrijving'];

		$query = "SELECT  SUM((TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. ") AS actuelePortefeuilleWaardeEuro, TijdelijkeRapportage.valuta, Valutas.Afdrukvolgorde
			    FROM TijdelijkeRapportage Join Valutas ON TijdelijkeRapportage.valuta = Valutas.Valuta
				  WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
		    	.$__appvar['TijdelijkeRapportageMaakUniek'].
		    	"GROUP BY valuta ORDER BY Afdrukvolgorde ";

		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		while($regel = $DB->NextRecord())
		  $valutaWaarde[$regel['valuta']]=$regel['actuelePortefeuilleWaardeEuro'];

		$percentage=array();
		$kleur=array();
		$omschrijvingen=array();
		$rest=100;
		foreach ($valutaWaarde as $valuta=>$waarde)
		{
		  $valutaPercentage=$waarde/$totaalWaarde*100;
		  $rest -= $valutaPercentage;
		  $percentage[]=$valutaPercentage;
		  $kleur[]=array($kleurenVal[$valuta]['R']['value'],$kleurenVal[$valuta]['G']['value'],$kleurenVal[$valuta]['B']['value']);
      $omschrijvingen[]=$dbValutacategorien[$valuta]." ".$this->formatGetal($valutaPercentage,1)."%" ;
		}
		if(round($rest,1) <> 0.0)
		{
		  $percentage[]=$rest;
		  $kleur[]=array(200,100,100);
		  $omschrijvingen[]="Restpercentage"." ".$this->formatGetal($rest,1)."%" ;
		}


    $y=125;
		$this->pdf->set3dLabels($omschrijvingen,220,$y-5,$kleur,-55,-5);
    $this->pdf->Pie3D($percentage,$kleur,210,$y+5,28,20,6,"Valutaverdeling");


			$query="SELECT TijdelijkeRapportage.Beleggingssector, TijdelijkeRapportage.beleggingssectorOmschrijving as Omschrijving, sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro ,TijdelijkeRapportage.beleggingscategorie
 FROM TijdelijkeRapportage
WHERE  TijdelijkeRapportage.Portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
." AND TijdelijkeRapportage.beleggingscategorie = 'AAND'"
.$__appvar['TijdelijkeRapportageMaakUniek']."
GROUP BY TijdelijkeRapportage.Beleggingssector ORDER BY beleggingssectorVolgorde desc ";


	$DB->SQL($query);
	$DB->Query();

	while($sec = $DB->nextRecord())
	{
	  if ($sec['Beleggingssector']== "")
	    $sec['Beleggingssector']='Geen sector';
	  if ($sec['Omschrijving']== "")
	    $sec['Omschrijving']='Geen sector';
	  $data['sectoren'][$sec['Beleggingssector']]['waardeEur']=$sec['WaardeEuro'];
	  $data['sectoren'][$sec['Beleggingssector']]['Omschrijving']=$sec['Omschrijving'];
	  $totalen['sectoren']+=$sec['WaardeEuro'];
	}

		$percentage=array();
		$kleur=array();
		$omschrijvingen=array();
		$rest=100;
		foreach ($data['sectoren'] as $sector=>$waardeData)
		{
		  $sectorPercentage=$waardeData['waardeEur']/$totalen['sectoren']*100;
		  $rest -= $sectorPercentage;
		  $percentage[]=$sectorPercentage;
		  $tmpKleur=array($kleurenSec[$sector]['R']['value'],$kleurenSec[$sector]['G']['value'],$kleurenSec[$sector]['B']['value']);
		  if ($tmpKleur[0]==0 && $tmpKleur[1]==0 && $tmpKleur[2]==0)
		    $tmpKleur=array(rand(1,255),rand(1,255),rand(1,255));
		  $kleur[]=$tmpKleur;
      $omschrijvingen[]=$waardeData['Omschrijving']." ".$this->formatGetal($sectorPercentage,1)."%" ;
		}
		if(round($rest,1) <> 0.0)
		{
		  $percentage[]=$rest;
		  $kleur[]=array(200,100,100);
		  $omschrijvingen[]="Restpercentage"." ".$this->formatGetal($rest,1)."%" ;
		}

		    $y=61;
		$this->pdf->set3dLabels($omschrijvingen,220,$y-5,$kleur,-55,-5);
    $this->pdf->Pie3D($percentage,$kleur,210,$y+5,28,20,6,"Sectorverdeling aandelen");//
  }

  function vastrentendeDeel()
  {
    global $__appvar;
  	$this->pdf->templateVars['VOLKVPaginas'] = $this->pdf->customPageNo;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		unset($this->pdf->CellBorders);

		$this->cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		$this->cashfow->genereerTransacties();
		$this->cashfow->genereerRows();
		$this->pdf->SetWidths(array(30,80,23,23,23,23,23,23,23));
		$this->pdf->SetAligns(array("R",'L','R','L','L','R','R','R','R'));

    $this->pdf->CellBorders = array('US','US','US','US','US','US','US','US','US');

		$this->pdf->ln();
		 $this->pdf->row(array(vertaalTekst("Nominale\nWaarde",$this->pdf->rapport_taal),
     vertaalTekst("Instrument",$this->pdf->rapport_taal),
		 vertaalTekst("Actuele\nWaarde",$this->pdf->rapport_taal),
		 vertaalTekst("Rating instrument",$this->pdf->rapport_taal),
		 vertaalTekst("Rating debiteur",$this->pdf->rapport_taal),
		 vertaalTekst("Coupon Rendement",$this->pdf->rapport_taal),
		 vertaalTekst("Markt Rendement",$this->pdf->rapport_taal),
		 vertaalTekst("Modified duration",$this->pdf->rapport_taal),
		 vertaalTekst("Resterende looptijd",$this->pdf->rapport_taal)));
   $this->pdf->SetAligns(array("R",'L','R','L','L','R','R','R','R'));
       unset($this->pdf->CellBorders);
       $this->pdf->ln();

		$DB = new DB();
		$this->db = new DB();
		$this->vastWhere=" AND ( TijdelijkeRapportage.hoofdcategorie='G-RISM' OR Fondsen.Lossingsdatum <> '0000-00-00')";


			  $query="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS actuelePortefeuilleWaardeEuro
			  FROM TijdelijkeRapportage
			  Left Join Fondsen ON Fondsen.Fonds = TijdelijkeRapportage.Fonds
			  WHERE TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND
			   TijdelijkeRapportage.portefeuille='".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek'].$this->vastWhere."";
    $DB->SQL($query);
    $waarde=$DB->lookupRecord();
    $waarde=$waarde['actuelePortefeuilleWaardeEuro'];

    $this->actueleWaardePortefeuille=$waarde;

 $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
	  $query="SELECT
TijdelijkeRapportage.hoofdcategorieOmschrijving AS HcategorieOmschrijving,
TijdelijkeRapportage.historischeWaarde,
TijdelijkeRapportage.historischeValutakoers,
 SUM(IF(TijdelijkeRapportage.type = 'fondsen',(beginPortefeuilleWaardeEuro),0 )) / ".$this->pdf->ValutaKoersStart." AS beginPortefeuilleWaardeEuro,
SUM(IF(TijdelijkeRapportage.type = 'fondsen',TijdelijkeRapportage.beginwaardeLopendeJaar,0))  as beginwaardeLopendeJaar,
SUM(IF(TijdelijkeRapportage.type = 'fondsen',TijdelijkeRapportage.historischeWaarde,0)) as historischeWaarde,
SUM(IF(TijdelijkeRapportage.type = 'rente' , (actuelePortefeuilleWaardeEuro),0)) / ".$this->pdf->ValutaKoersEind." AS rente,
SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS actuelePortefeuilleWaardeEuro ,
 SUM(IF(TijdelijkeRapportage.type = 'fondsen',(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid * TijdelijkeRapportage.actueleValuta),0
 )) AS historischeWaardeEuro,
IF(TijdelijkeRapportage.type = 'rekening' ,actuelePortefeuilleWaardeInValuta, totaalAantal) as totaalAantal,
TijdelijkeRapportage.actueleFonds,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.actueleValuta,
TijdelijkeRapportage.fondsOmschrijving,
TijdelijkeRapportage.rekening,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.beleggingscategorieVolgorde as Afdrukvolgorde,
TijdelijkeRapportage.type,
TijdelijkeRapportage.beleggingscategorieOmschrijving as categorieOmschrijving,
Fondsen.rating as fondsRating,
Fondsen.Lossingsdatum,
Fondsen.variabeleCoupon,
Fondsen.Renteperiode,
Fondsen.Rentedatum,
emittentPerFonds.emittent,
TijdelijkeRapportage.fonds,
emittenten.rating as emittentRating,
TijdelijkeRapportage.fondsEenheid
FROM
TijdelijkeRapportage
Left Join Fondsen ON Fondsen.Fonds = TijdelijkeRapportage.Fonds
Left Join emittentPerFonds ON emittentPerFonds.Fonds = TijdelijkeRapportage.Fonds  AND emittentPerFonds.vermogensbeheerder='$beheerder'
LEFT Join emittenten ON emittentPerFonds.emittent = emittenten.emittent AND emittentPerFonds.vermogensbeheerder = '$beheerder'
WHERE
TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille='".$this->portefeuille."'
".$__appvar['TijdelijkeRapportageMaakUniek'].$this->vastWhere."
GROUP BY
TijdelijkeRapportage.fonds,TijdelijkeRapportage.rekening
ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde,TijdelijkeRapportage.fondsOmschrijving,TijdelijkeRapportage.rekening";
		$DB->SQL($query);
		$DB->Query();

		while ($data=$DB->nextRecord())
		{
			$rente=getRenteParameters($data['fonds'], $this->rapportageDatum);
			foreach($rente as $key=>$value)
				$data[$key]=$value;
      if($_POST['anoniem'] !=1 && $data['rekening'] <> '')
        $data['fondsOmschrijving'].=' '.substr($data['rekening'],0,strlen($data['rekening'])-3);

      $Hcategorie=$data['HcategorieOmschrijving'];
      if($Hcategorie=='')
        $Hcategorie='Hcat';

      //$data['actuelePortefeuilleWaardeEuro']=$data['actuelePortefeuilleWaardeEuro']-$data['rente'];
      if($data['type']=='rekening')
        $ongerealiseerdResultaat=0;
      else
        $ongerealiseerdResultaat=$data['actuelePortefeuilleWaardeEuro']-$data['beginPortefeuilleWaardeEuro'];

      $aandeel=$data['actuelePortefeuilleWaardeEuro']/$this->actueleWaardePortefeuille;

      $totalenCat[$data['categorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
      $totalenCat[$data['categorieOmschrijving']]['beginPortefeuilleWaardeEuro'] += $data['beginPortefeuilleWaardeEuro'];
      $totalenCat[$data['categorieOmschrijving']]['ongerealiseerdResultaat'] += $ongerealiseerdResultaat;
      $totalenCat[$data['categorieOmschrijving']]['aandeel'] += $aandeel;

      $totalenHcat[$Hcategorie]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
      $totalenHcat[$Hcategorie]['historischeWaardeEuro'] += $data['beginPortefeuilleWaardeEuro'];
      $totalenHcat[$Hcategorie]['ongerealiseerdResultaat'] += $ongerealiseerdResultaat;
      $totalenHcat[$Hcategorie]['aandeel'] += $aandeel;

      $totalen['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
      $totalen['beginPortefeuilleWaardeEuro'] += $data['beginPortefeuilleWaardeEuro'];
      $totalen['ongerealiseerdResultaat'] += $ongerealiseerdResultaat;
      $totalen['aandeel'] += $aandeel;
/*
      if($data['categorieOmschrijving'] <> $lastcategorieOmschrijving)
      {
        if(!empty($lastcategorieOmschrijving))
        {
          $this->pdf->CellBorders = array('','','','','','','T','','T','T','T','T','T');
          $this->pdf->row(array('','','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],0),'',
          '','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['aandeel']*100,1),''));
          unset($this->pdf->CellBorders);
          $totalenC=array();
        }

        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $lastHcategorie=$Hcategorie;
        $this->pdf->row(array($data['categorieOmschrijving']));
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      }
     */

      $totalen['rente'] += $data['rente'];
     // listarray($waarden);
     if($data['Lossingsdatum'] <> '')
        $lossingsJul = adodb_db2jul($data['Lossingsdatum']);
     else
        $lossingsJul=0;
        $rentedatumJul = adodb_db2jul($data['Rentedatum']);
        $renteVanafJul = adodb_db2jul(jul2sql($this->pdf->rapport_datum));

			$koers=getRentePercentage($data['fonds'],$this->rapportageDatum);

			  $renteDag=0;
			  if($data['variabeleCoupon'] == 1)
			  {
			    $rapportJul=adodb_db2jul($this->rapportageDatum);
			    $renteJul=adodb_db2jul($data['Rentedatum']);
          $renteStap=($data['Renteperiode']/12)*31556925.96;
          $renteDag=$renteJul;
          if($renteStap > 100000)
            while($renteDag<$rapportJul)
            {
              $renteDag+=$renteStap;
            }
			  }

$ytm=0;
$duration=0;
$modifiedDuration=0;

        if($lossingsJul > 0)
	      {

	        //$this->huidigeWaardeTotaal += $fonds['actuelePortefeuilleWaardeEuro'];
	        //$this->lossingsWaardeTotaal += $fonds['totaalAantal'] * 100 * $fonds['fondsEenheid'] * $fonds['actueleValuta'];
		  	  $jaar = ($lossingsJul-$renteVanafJul)/31556925.96;

		  	  $p = $data['actueleFonds'];
	        $r = $koers['Rentepercentage']/100;
	        $b = $this->cashfow->fondsDataKeyed[$data['fonds']]['lossingskoers'];
	        $y = $jaar;

	        $ytm=  $this->cashfow->bondYTM($p,$r,$b,$y)*100;
	        $restLooptijd=($lossingsJul-$this->pdf->rapport_datum)/31556925.96;

	         $duration=$this->cashfow->waardePerFonds[$data['fonds']]['ActueelWaardeJaar']/$this->cashfow->waardePerFonds[$data['fonds']]['ActueelWaarde'];
	         if($data['variabeleCoupon'] == 1 && $renteDag <> 0)
	           $modifiedDuration=($renteDag-db2jul($this->rapportageDatum))/86400/365;
	         else
	           $modifiedDuration=$duration/(1+$ytm/100);
	         $aandeel=$data['actuelePortefeuilleWaardeEuro']/$this->actueleWaardePortefeuille;

           $totalen['yield']+=$koers['Rentepercentage']*$data['totaalAantal']/$data['actuelePortefeuilleWaardeEuro']*$data['actueleValuta']*$aandeel;
	         $totalen['ytm']+=$ytm*$aandeel;
	         $totalen['duration']+=$duration*$aandeel;
	         $totalen['modifiedDuration']+=$modifiedDuration*$aandeel;
	         $totalen['restLooptijd']+=$restLooptijd*$aandeel;
	      }
	      else
	      {
	        $ytm=0;
	        $restLooptijd=0;
	        $duration=0;
	        $modifiedDuration=0;
	      }

      $this->pdf->row(array($this->formatGetal(
      $data['totaalAantal'],0),
      $data['fondsOmschrijving'],
      $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),
      $data['fondsRating'],$data['emittentRating'],
      $this->formatGetal($koers['Rentepercentage']*$data['totaalAantal']/$data['actuelePortefeuilleWaardeEuro']*$data['actueleValuta'],2)."%",
      $this->formatGetal($ytm,2)."%",
      $this->formatGetal($modifiedDuration,2),
      $this->formatGetal($restLooptijd,2) ));
      $lastcategorieOmschrijving=$data['categorieOmschrijving'];
    }
		 $this->pdf->underlinePercentage=0.8;
    $this->pdf->CellBorders = array('','','','','','TS','TS','TS','TS');
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->ln(2);


    $this->pdf->row(array('',vertaalTekst("Totale portefeuile gemiddeld",$this->pdf->rapport_taal),
    '','','',
    $this->formatGetal($totalen['yield'],2)."%",
    $this->formatGetal($totalen['ytm'],2)."%",
    $this->formatGetal($totalen['modifiedDuration'],2),
    $this->formatGetal($totalen['restLooptijd'],2)));
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->underlinePercentage);

  }

}
?>
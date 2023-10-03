<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/01/02 16:18:56 $
 		File Versie					: $Revision: 1.21 $

 		$Log: RapportRISK_L30.php,v $
 		Revision 1.21  2019/01/02 16:18:56  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2018/08/18 12:40:15  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.19  2016/05/28 14:21:20  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2015/01/31 20:02:46  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2014/11/30 13:19:33  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2014/11/23 14:13:22  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2014/10/22 15:50:27  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2013/03/20 11:07:25  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2012/12/22 15:34:10  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2012/11/25 13:16:31  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2012/09/05 18:19:11  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2012/08/11 13:17:53  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2012/08/08 15:37:42  rvv
 		*** empty log message ***

 		Revision 1.8  2012/04/08 08:13:18  rvv
 		*** empty log message ***

 		Revision 1.7  2012/03/08 08:15:34  rvv
 		*** empty log message ***

 		Revision 1.6  2012/03/08 07:58:38  rvv
 		*** empty log message ***

 		Revision 1.5  2012/01/04 16:28:38  rvv
 		*** empty log message ***

 		Revision 1.4  2011/12/21 19:19:33  rvv
 		*** empty log message ***

 		Revision 1.3  2011/12/14 18:59:12  rvv
 		*** empty log message ***

 		Revision 1.2  2011/10/26 17:33:24  rvv
 		*** empty log message ***

 		Revision 1.1  2011/10/23 13:34:45  rvv
 		*** empty log message ***

 		Revision 1.7  2011/09/14 09:26:56  rvv
 		*** empty log message ***

 		Revision 1.6  2011/09/03 14:30:20  rvv
 		*** empty log message ***

 		Revision 1.5  2011/07/03 06:42:47  rvv
 		*** empty log message ***

 		Revision 1.4  2011/06/15 16:14:39  rvv
 		*** empty log message ***

 		Revision 1.3  2011/06/13 14:41:56  rvv
 		*** empty log message ***

 		Revision 1.2  2011/06/02 15:05:05  rvv
 		*** empty log message ***

 		Revision 1.1  2011/05/29 06:38:42  rvv
 		*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
include_once($__appvar["basedir"]."/html/rapport/PDFOverzicht.php");

//ini_set('max_execution_time',60);
class RapportRISK_L30
{
	function RapportRISK_L30($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "RISK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		if($this->pdf->rapport_RISK_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_RISK_titel;
		else
			$this->pdf->rapport_titel = "\n \n \nRendement & Risicokenmerken";

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

		$this->pdf->addPage();
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();
		$this->printRendement($this->portefeuille,$this->rapportageDatum,$this->rapportageDatumVanaf);
    $this->printAEXvergelijking();
    $this->pdf->ln(2);
    $this->printBenchmarkvergelijking();
    $this->pdf->ln(2);
    $this->printValutaVergelijking();
    $this->pdf->ln(2);
    $this->printZorg();
    $this->printGrafieken();
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

		$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),130,14);
		$this->pdf->ln(2);
		$this->pdf->SetWidths(array(80,50));
  	$this->pdf->SetAligns(array('L','R'));
  	$this->pdf->row(array(vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal),"€ ".$this->pdf->formatGetal($resultaat,2)));
  	$this->pdf->ln(2);
  	$this->pdf->row(array(vertaalTekst("Rendement over verslagperiode (na kosten)",$this->pdf->rapport_taal),$this->pdf->formatGetal($performance,2)." %"));
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

  	$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),130,((count($indexData)+1)*4));
   // $this->pdf->ln(2);
		$this->pdf->SetWidths(array(60,25,25,20));
  	$this->pdf->SetAligns(array('L','R','R','R'));
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	$this->pdf->row(array('Index-vergelijking',dbdate2form($this->perioden['begin']),dbdate2form($this->perioden['eind']),'Perf in %'));
  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	foreach ($indexData as $index)
  	  $this->pdf->row(array($index['Omschrijving'],$this->formatGetal($index['fondsKoers_begin'],2),$this->formatGetal($index['fondsKoers_eind'],2),$this->formatGetal($index['performance'],2)));
   // $this->pdf->ln(2);
  }

  function printBenchmarkvergelijking()
  {



		if(db2jul($this->perioden['eind']) > mktime(0,0,0,3,31,2016))
		{
			$data=getBenchmarkvergelijking($this);
			//	listarray($data);
			$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),130,2*4);
			$this->pdf->SetWidths(array(60,25,25,20));
			$this->pdf->SetAligns(array('L','R','R','R'));
			$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
			$this->pdf->row(array('Index-vergelijking','','','Perf in %'));
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->row(array($data['totaal']['Omschrijving'] . ' (voor kosten)','','',$this->formatGetal($data['totaal']['rendement'],2)));
		}
		else
		{
			$DB = new DB();
			$query = "SELECT Portefeuilles.SpecifiekeIndex,Fondsen.Omschrijving,Fondsen.Valuta
              FROM Portefeuilles JOIN Fondsen ON Portefeuilles.SpecifiekeIndex = Fondsen.Fonds
              WHERE Portefeuilles.Portefeuille='" . $this->portefeuille . "'";
			$DB->SQL($query);
			$DB->Query();
			while ($index = $DB->nextRecord())
			{
				$indexData[$index['SpecifiekeIndex']] = $index;
				foreach ($this->perioden as $periode => $datum)
				{
					$indexData[$index['SpecifiekeIndex']]['fondsKoers_' . $periode] = $this->getFondsKoers($index['SpecifiekeIndex'], $datum);
					$indexData[$index['SpecifiekeIndex']]['valutaKoers_' . $periode] = getValutaKoers($index['Valuta'], $datum);
				}
				$indexData[$index['SpecifiekeIndex']]['performanceJaar'] = ($indexData[$index['SpecifiekeIndex']]['fondsKoers_eind'] - $indexData[$index['SpecifiekeIndex']]['fondsKoers_jan']) / ($indexData[$index['SpecifiekeIndex']]['fondsKoers_jan'] / 100);
				$indexData[$index['SpecifiekeIndex']]['performance'] = ($indexData[$index['SpecifiekeIndex']]['fondsKoers_eind'] - $indexData[$index['SpecifiekeIndex']]['fondsKoers_begin']) / ($indexData[$index['SpecifiekeIndex']]['fondsKoers_begin'] / 100);
				$indexData[$index['SpecifiekeIndex']]['performanceEurJaar'] = ($indexData[$index['SpecifiekeIndex']]['fondsKoers_eind'] * $indexData[$index['SpecifiekeIndex']]['valutaKoers_eind'] - $indexData[$index['SpecifiekeIndex']]['fondsKoers_jan'] * $indexData[$index['SpecifiekeIndex']]['valutaKoers_jan']) / ($indexData[$index['SpecifiekeIndex']]['fondsKoers_jan'] * $indexData[$index['SpecifiekeIndex']]['valutaKoers_jan'] / 100);
				$indexData[$index['SpecifiekeIndex']]['performanceEur'] = ($indexData[$index['SpecifiekeIndex']]['fondsKoers_eind'] * $indexData[$index['SpecifiekeIndex']]['valutaKoers_eind'] - $indexData[$index['SpecifiekeIndex']]['fondsKoers_begin'] * $indexData[$index['SpecifiekeIndex']]['valutaKoers_begin']) / ($indexData[$index['SpecifiekeIndex']]['fondsKoers_begin'] * $indexData[$index['SpecifiekeIndex']]['valutaKoers_begin'] / 100);
			}

			$this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 130, ((count($indexData) + 1) * 4));
			// $this->pdf->ln(2);
			$this->pdf->SetWidths(array(60, 25, 25, 20));
			$this->pdf->SetAligns(array('L', 'R', 'R', 'R'));
			$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
			$this->pdf->row(array('Benchmark-vergelijking', '', '', 'Perf in %'));
			$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
			foreach ($indexData as $indexData)
			{
				$this->pdf->row(array($indexData['Omschrijving'], $this->formatGetal($indexData['fondsKoers_begin'], 2), $this->formatGetal($indexData['fondsKoers_eind'], 2), $this->formatGetal($indexData['performance'], 2)));
			}
			// $this->pdf->ln(2);
		}
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
	  	$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),130,((count($indexValuta)+1)*4));
      // $this->pdf->ln(2);
  		$this->pdf->SetWidths(array(60,25,25,20));
    	$this->pdf->SetAligns(array('L','R','R','R'));
     	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    	$this->pdf->row(array('Valuta','','','Perf in %'));
    	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    	foreach ($indexValuta as $indexData)
    	  $this->pdf->row(array($indexData['Omschrijving'],$this->formatGetal($indexData['valutaKoers_begin'],4),$this->formatGetal($indexData['valutaKoers_eind'],4),$this->formatGetal($indexData['performance'],2)));
      // $this->pdf->ln(2);
		}
  }

  function printZorg()
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

#listarray($categorieWaarden);
  
    $query= "SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->totaalWaarde." as percentage,ZorgplichtPerBeleggingscategorie.Zorgplicht,
    TijdelijkeRapportage.beleggingscategorie,TijdelijkeRapportage.beleggingscategorie
    FROM TijdelijkeRapportage
    INNER JOIN ZorgplichtPerBeleggingscategorie ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='CAS'
    WHERE TijdelijkeRapportage.Portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
      .$__appvar['TijdelijkeRapportageMaakUniek']."
    GROUP By ZorgplichtPerBeleggingscategorie.Zorgplicht";
    $DB->SQL($query);
    $DB->Query();
    $categorieWaarden=array();
    while($data= $DB->nextRecord())
    {
    
      $categorieWaarden[$data['Zorgplicht']]+=$data['percentage']*100;
    }
    

  	$zorgplicht = new Zorgplichtcontrole();
    $tmp=$this->pdf->portefeuilledata;
    $tmp['Portefeuille']=$this->portefeuille;
  	$zpwaarde=$zorgplicht->zorgplichtMeting($tmp,$this->rapportageDatum);

    $tmp=array();
    foreach ($zpwaarde['conclusie'] as $index=>$regelData)
      $tmp[$regelData[0]]=$regelData;

    krsort($tmp);

    $this->pdf->SetAligns(array('L','R','R','R','R'));
   	$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),130,51.3);
  	$this->pdf->SetWidths(array(50,20,20,20,20));
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('Risicoprofiel'."\n".$this->pdf->portefeuilledata['Risicoklasse'],'Minimaal','Maximaal',"Werkelijke\nverdeling","Risico\ngewogen"));
    	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetAligns(array('L','R','R','R','R'));
  	foreach ($tmp as $index=>$regelData)
        {
          //echo $regelData[0]." ";
  	  $this->pdf->row(array($zorgplichtcategorien[$regelData[0]],$zpwaarde['categorien'][$regelData[0]]['Minimum']."%",$zpwaarde['categorien'][$regelData[0]]['Maximum']."%",$this->formatGetal($categorieWaarden[$regelData[0]],1)."%",$regelData[2]."%"));
        }
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
  	$this->pdf->SetWidths(array(50,80));
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


}
?>

<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/06/25 16:57:02 $
 		File Versie					: $Revision: 1.1 $

 		$Log: RapportRISK_L18.php,v $
 		Revision 1.1  2016/06/25 16:57:02  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2015/03/14 17:01:49  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2015/03/04 16:30:29  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2015/03/01 14:08:16  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2014/11/05 16:52:22  rvv
 		*** empty log message ***
 		
 	

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
include_once($__appvar["basedir"]."/html/rapport/PDFOverzicht.php");
include_once($__appvar["basedir"]."/html/rapport/include/ATTberekening_L22.php");

//ini_set('max_execution_time',60);
class RapportRISK_L18
{
	function RapportRISK_L18($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
    $this->pdf->switchFont('fondsLaag');
		$this->pdf->ln(10);
		$this->printRendement($this->portefeuille,$this->rapportageDatum,$this->rapportageDatumVanaf);
    $this->printAEXvergelijking();
    $this->pdf->ln(2);
    $this->printValutaVergelijking();
    $this->pdf->ln(2);
    $this->printRisico();
    $this->pdf->addPage();

    $this->printSTDDEV();
    $this->printGrafieken();


     $this->extraText();

	}
  
  function extraText()
  { 
    
   	$this->pdf->SetWidths(array(270));
  	$this->pdf->SetAligns(array('L'));
    
    $this->pdf->addPage();
    $this->pdf->ln(20);
    $body="Standaarddeviatie wordt gebruikt als een maatstaf voor de risicograad van beleggingen. Het geeft de mate van afwijking van een gemiddelde weer. Risico bij beleggen is te omschrijven als de kans dat het werkelijke rendement afwijkt van het verwachte rendement. Dit kan dus zowel een lager als een hoger rendement betekenen. Een hogere standaarddeviatie geeft aan dat sprake is van een hoger risico, aangezien de afwijkingen van het gemiddelde in het verleden groter waren. Ander woorden voor standaarddeviatie zijn volatiliteit en beweeglijkheid. De rendementen van aandelen schommelen meer dan die van obligaties. Dit komt tot uitdrukking in het verschil in standaarddeviatie. De standaarddeviatie van obligaties is doorgaans lager dan die van aandelen. Naarmate de rendementen in het verleden meer schommelden, is de standaarddeviatie hoger en dat geldt daarmee ook voor het risico. De standaarddeviatie wordt berekend met behulp van historische rendementen."; 
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	$this->pdf->row(array('Standaarddeviatie'));
	  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	$this->pdf->row(array($body));
    $this->pdf->ln();
    $kop="Verschil AFM standaarddeviatie en de standaarddeviatie van uw portefeuille.";
    $body="De AFM standaarddeviatie: Hierbij is de standaarddeviatie niet berekend op basis van eigen historische cijfers, maar wordt er gebruik gemaakt van voorgeschreven gegevens die voor de gehele markt dezelfde zijn. De portefeuille standaarddeviatie is door Sequoia berekend op basis van de historische rendementen van de beleggingen binnen uw portefeuille.";
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	$this->pdf->row(array($kop));
	  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	$this->pdf->row(array($body));

  }
  
  function standard_deviation($aValues)
  {
    $fMean = array_sum($aValues) / count($aValues);
    $fVariance = 0.0;
    foreach ($aValues as $i)
    {
        $fVariance += pow($i - $fMean, 2);
    }
    $fVariance /= count($aValues)-1;
    return (float) sqrt($fVariance);
  }
  
  function printSTDDEV()
  {

    $att=new ATTberekening_L22($this);
    $data=$att->bereken($this->pdf->PortefeuilleStartdatum,$this->rapportageDatum,'categorien');
    $this->attData=$data;
    $stddevInput=array();
    $startX=$this->pdf->GetX();
    $startY=$this->pdf->GetY()+3+30;
    
    $head=array_keys($data);

$this->pdf->excelData[0]=array('Periode');
foreach($head as $cat)
  $this->pdf->excelData[0][]=$cat;
  
    $ncat=0;
    foreach($data as $categorie=>$categorieData)
    {
      $ncat++;
      $row=1;
      foreach($categorieData['perfWaarden'] as $datum=>$perfData)
      {
        $stddevInput[$categorie][]=$perfData['procent']*100;
        $this->pdf->excelData[$row][0]=$datum;
        $this->pdf->excelData[$row][$ncat]=$perfData['procent']*100;
        $row++;
      }
    }
    if($this->pdf->debug)
    {
      listarray($stddevInput);
    }
   
   
    $stddev=array();
    foreach($catVerdeling as $categorie=>$tmp)
      $stddev[$categorie]=0;
    foreach($stddevInput as $categorie=>$input)
    {
      if(!in_array($categorie,array('Geen H-cat','Geen cat')))
        $stddev[$categorie]=$this->standard_deviation($input)*sqrt(12); //*12^0.5 maanden/jaar
    }
    
    $row=count($this->pdf->excelData);
    $this->pdf->excelData[$row]=array("stdev*sqrt(12)");
    foreach($head as $cat)
      $this->pdf->excelData[$row][]=$stddev[$cat];


    $this->pdf->setXY($startX,$startY-30);
    $this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),130,((count($stddev)+1)*6));
		$this->pdf->SetWidths(array(85,25));
  	$this->pdf->SetAligns(array('L','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('Standaarddeviatie portefeuille'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	foreach ($stddev as $cat=>$stddevWaarde)
    {
      if($att->categorien[$cat] <> '')
        $categorieOmschrijving=$att->categorien[$cat];
      else
       $categorieOmschrijving=$cat;
  	  $this->pdf->row(array('Standaarddeviatie '.$categorieOmschrijving,$this->formatGetal($stddevWaarde,1)));
    }

    $startY=165;
   	$this->pdf->Rect(157,$startY,106,10);
    $this->pdf->setXY($this->pdf->marge,$startY);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(155,60,20));
    $this->pdf->SetAligns(array('L','L','R'));
    $this->pdf->ln(2);
    $this->pdf->Row(array('','Standaarddeviatie portefeuille',$this->formatGetal($stddev['totaal'],1)." %"));
    unset($stddev['totaal']);
    unset($stddev['Liquiditeiten']);
    $startY-=30;
      // $this->pdf->ln(2);

    
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
		$actueleWaardePortefeuille = $actueleWaardePortefeuille['totaal']  / $this->pdf->ValutaKoersEind;
		$resultaat = ($actueleWaardePortefeuille -$vergelijkWaarde - getStortingen($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$this->pdf->rapportageValuta) + getOnttrekkingen($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$this->pdf->rapportageValuta));
		$performance = performanceMeting($portefeuille, $rapportageDatumVanaf, $rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
		$this->pdf->ln(2);

		if(($this->pdf->GetY() + 22) >= $this->pdf->pagebreak)
		{
			$this->pdf->AddPage();
			$this->pdf->ln();
		}

		$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),145,17);
		$this->pdf->ln(2);
		$this->pdf->SetWidths(array(80,65));
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

  	$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),145,((count($indexData)+1)*6));
   // $this->pdf->ln(2);
		$this->pdf->SetWidths(array(75,25,25,20));
  	$this->pdf->SetAligns(array('L','R','R','R'));
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	$this->pdf->row(array('Index-vergelijking',dbdate2form($this->perioden['begin']),dbdate2form($this->perioden['eind']),'Perf in %'));
  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	foreach ($indexData as $index)
  	  $this->pdf->row(array($index['Omschrijving'],$this->formatGetal($index['fondsKoers_begin'],2),$this->formatGetal($index['fondsKoers_eind'],2),$this->formatGetal($index['performance'],2)));
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

		if(count($indexValuta)>0)
		{
	  	$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),145,((count($indexValuta)+1)*6));
      // $this->pdf->ln(2);
  		$this->pdf->SetWidths(array(75,25,25,20));
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
    $query="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->totaalWaarde." as percentage,
TijdelijkeRapportage.beleggingscategorieOmschrijving,
TijdelijkeRapportage.beleggingscategorie
FROM TijdelijkeRapportage
WHERE TijdelijkeRapportage.Portefeuille =  '".$this->portefeuille."' AND
 TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'
GROUP BY TijdelijkeRapportage.beleggingscategorie
ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde";
    $DB->SQL($query);
		$DB->Query();
		while($data= $DB->nextRecord())
		{
		 
		  if($data['beleggingscategorie']=='')
		    $data['beleggingscategorie']='Overige';
		  elseif ($data['beleggingscategorie']=='ZAK')
		    $data['beleggingscategorie']='Zakelijk';
		  elseif ($data['beleggingscategorie']=='VAR')
		    $data['beleggingscategorie']='Vastrentend';

		  $categorieWaarden[$data['beleggingscategorie']]=$data['percentage']*100;
      $categorieOmschrijving[$data['beleggingscategorie']]=$data['beleggingscategorieOmschrijving'];
		}
 
  	$zorgplicht = new Zorgplichtcontrole();
  	$zpwaarde=$zorgplicht->zorgplichtMeting($this->pdf->portefeuilledata,$this->rapportageDatum);

    $tmp=array();
    foreach ($zpwaarde['conclusie'] as $index=>$regelData)
      $tmp[$regelData[0]]=$regelData;

    krsort($tmp);
   //listarray($tmp);
    //listarray($categorieWaarden);exit;

    $this->pdf->SetAligns(array('L','R','R','R','R'));
   	$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),145,count($categorieWaarden)*6+12);
  	$this->pdf->SetWidths(array(55,20,25,25,20));
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('Gekozen risicoprofiel'."\n".$this->pdf->portefeuilledata['Risicoklasse'],'Minimaal','Maximaal',"Werkelijke\nverdeling","Risico\ngewogen"));
    	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetAligns(array('L','R','R','R','R'));
  	//foreach ($tmp as $index=>$regelData)
    foreach ($categorieWaarden as $cat=>$percentage)
    {
      if($tmp[$cat][2])
        $risicogewogen=$tmp[$cat][2]."%";
      else
        $risicogewogen=''; 
      if($zpwaarde['categorien'][$cat]['Minimum'])   
        $min=$zpwaarde['categorien'][$cat]['Minimum']."%";
      else
        $min='';   
      if($zpwaarde['categorien'][$cat]['Maximum'])  
        $max=$zpwaarde['categorien'][$cat]['Maximum']."%";
      else
        $max='';  
  	  $this->pdf->row(array($categorieOmschrijving[$cat],$min,$max,$this->formatGetal($categorieWaarden[$cat],1)."%",$risicogewogen));
    }
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
		$kleurenAfm = $kleuren['AFM'];

			$query="SELECT TijdelijkeRapportage.Beleggingssector, TijdelijkeRapportage.beleggingssectorOmschrijving as Omschrijving, sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro ,TijdelijkeRapportage.beleggingscategorie
 FROM TijdelijkeRapportage
WHERE  TijdelijkeRapportage.Portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
." AND TijdelijkeRapportage.beleggingscategorie = 'AAND'"
.$__appvar['TijdelijkeRapportageMaakUniek']."
GROUP BY TijdelijkeRapportage.Beleggingssector ORDER BY beleggingssectorVolgorde desc ";


		$query = "SELECT TijdelijkeRapportage.afmCategorieOmschrijving as Omschrijving, ".
			" TijdelijkeRapportage.afmCategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS WaardeEuro ".
			" FROM TijdelijkeRapportage ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.afmCategorie ".
			" ORDER BY TijdelijkeRapportage.afmCategorie asc,  TijdelijkeRapportage.valutaVolgorde asc";


	$DB->SQL($query);
	$DB->Query();

	while($sec = $DB->nextRecord())
	{
	  if ($sec['afmCategorie']== "")
	    $sec['afmCategorie']='Geen categorie';
	  if ($sec['Omschrijving']== "")
	    $sec['Omschrijving']='Geen categorie';
	  $data['afmCategorien'][$sec['afmCategorie']]['waardeEur']=$sec['WaardeEuro'];
	  $data['afmCategorien'][$sec['afmCategorie']]['Omschrijving']=$sec['Omschrijving'];
	  $totalen['afmCategorien']+=$sec['WaardeEuro'];
	}

		$percentage=array();
		$kleur=array();
		$omschrijvingen=array();
		$rest=100;
    //listarray($kleurenAfm);
		foreach ($data['afmCategorien'] as $categorie=>$waardeData)
		{
		  $categoriePercentage=$waardeData['waardeEur']/$totaalWaarde*100;
		  $rest -= $categoriePercentage;
		  $percentage[]=$categoriePercentage;
		  $tmpKleur=array($kleurenAfm[$categorie]['R']['value'],$kleurenAfm[$categorie]['G']['value'],$kleurenAfm[$categorie]['B']['value']);
		  if ($tmpKleur[0]==0 && $tmpKleur[1]==0 && $tmpKleur[2]==0)
		    $tmpKleur=array(rand(1,255),rand(1,255),rand(1,255));
		  $kleur[]=$tmpKleur;
      $omschrijvingen[]=$waardeData['Omschrijving']." ".$this->formatGetal($categoriePercentage,1)."%" ;
		}
		if(round($rest,1) <> 0.0)
		{
		  $percentage[]=$rest;
		  $kleur[]=array(200,100,100);
		  $omschrijvingen[]="Restpercentage"." ".$this->formatGetal($rest,1)."%" ;
		}

		//$y=73;
		$y=87;
		$this->pdf->set3dLabels($omschrijvingen,220,$y-5,$kleur,-55,-5);
    $this->pdf->Pie3D($percentage,$kleur,210,$y+5,28,20,6,"AFM-verdeling");//


$perfdata=$this->attData;
  $categorien=array('totaal');
  $startPeriode=db2jul($this->rapportageDatumVanaf);
  foreach ($categorien as $cat)
  { 
    $perfIndex=1;
    foreach ($perfdata[$cat]['perfWaarden'] as $datum=>$data)
    {
      $juldate=db2jul($datum);
      if($juldate > $startPeriode)
      { //echo date("Y-m-d",mktime(0,0,0,1,1,substr($datum,0,4)))." $datum <br>\n";

         $perfIndex=(1+$data['procent'])*$perfIndex;
         $hcatWaarden['periode'][$cat]['portefeuille'][]=($perfIndex-1)*100;
         $hcatWaarden['periode'][$cat]['datum'][]= date("M",$juldate);
         $hcatWaarden['periode'][$cat]['waarde'][]=$data;
      }
    }
  }
  //listarray($hcatWaarden);
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(35-$this->pdf->marge,70,20));
    $this->pdf->SetAligns(array('L','C','R'));
    $this->pdf->setXY($this->pdf->marge,128);
    $this->pdf->Row(array('','Portefeuille rendement'));
  
$this->pdf->setXY(35,135);
$portKleur=array($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
$this->LineDiagram(70, 35, $hcatWaarden['periode']['totaal'],array($portKleur),0,0,6,5,1);//50

    $afm=AFMstd($this->portefeuille,$this->rapportageDatum,$this->pdf->debug);
   	$this->pdf->Rect(157,128+20,106,10);
    $this->pdf->setY(130+20);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(155,60,20));
    $this->pdf->SetAligns(array('L','L','R'));
    $this->pdf->Row(array('','AFM-standaarddeviatie',$this->formatGetal($afm['std'],1)." %"));
  }
  
  
function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$periode='maand')
  {
    global $__appvar;

    $legendDatum= $data['datum'];
    $data1 = $data['specifiekeIndex'];
    $data = $data['portefeuille'];
    $legendaItems= $data['legenda'];




    if(count($data1)>0)
      $bereikdata = array_merge($data,$data1);
    else
      $bereikdata =   $data;

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );

    $this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.2);

    $this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

    if ($maxVal == 0)
    {
      $maxVal = ceil(max($bereikdata));
      if ($maxVal < 0)
        $maxVal = 1;
    }
    if ($minVal == 0)
    {
      $minVal = floor(min($bereikdata));
      if ($minVal > 0)
        $minVal =-1;
    }

    $minVal = floor(($minVal-1) * 1.1);
    $maxVal = ceil(($maxVal+1) * 1.1);
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / count($data);

    if($periode=='maand')
      $unit = $lDiag / 12;

    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
      $xpos = $XDiag + $verInterval * $i;

    $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);

    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);

    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);

    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $n=0;
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
       break;
    }

    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");

      $n++;
      if($n >20)
         break;
    }
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    $jaren=ceil(count($data1)/12);
    for ($i=0; $i<count($data); $i++)
    {
      if($i%$jaren==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$i],25);
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
      if ($i>0)
        $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color);
      if ($i==count($data1)-1)
          $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color);
      $yval = $yval2;
    }

    
    if(is_array($data1))
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color1);

      for ($i=0; $i<count($data1); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        if ($i>0)
          $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color1);
        if ($i==count($data1)-1)
          $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color1);

         $yval = $yval2;
      }
    }
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));

    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
  }
  
  function VBarDiagram2($w, $h, $data, $format, $color=null,$nbDiv=4,$numBars=0)
  {
      global $__appvar;
      $legendDatum = $data['datum'];
      //$data = $data['portefeuille'];
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      //$this->pdf->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1);

      $this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $w- $margin, $hGrafiek,'D',''); //,array(245,245,245)
      if($color == null)
          $color=array(155,155,155);
      
      $maxVal=0;
      $minVal=0;
      $maanden=array();
      foreach($data as $maand=>$maandData)
      {
        $maanden[$maand]=$maand;
        foreach($maandData as $type=>$waarde)
        {
          if($waarde > $maxVal)
            $maxVal = $waarde;
          if($waarde < $minVal)  
            $minVal = $waarde;
        }
      }
      if($maxVal > 1)
        $maxVal=ceil($maxVal);
      if($minVal < -1)  
        $minVal=floor($minVal);
      $minVal = $minVal * 1.1;
      $maxVal = $maxVal * 1.1;      
      if ($maxVal <0)
       $maxVal=0;

      if($minVal < 0)
      {
        $unit = $hGrafiek / (-1 * $minVal + $maxVal) * -1;
        $nulYpos =  $unit * (-1 * $minVal);
      }
      else
      {
        $unit = $hGrafiek / $maxVal * -1;
        $nulYpos =0;
      }

      $horDiv = 10;
      $horInterval = $hGrafiek / $horDiv;
      $bereik = $hGrafiek/$unit;

      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      $this->pdf->SetTextColor(0,0,0);

      $stapgrootte = ceil(abs($bereik)/$horDiv*10)/10;
      $top = $YstartGrafiek-$h;
      $bodem = $YstartGrafiek;
      $absUnit =abs($unit);

      $nulpunt = $YstartGrafiek + $nulYpos;
      $n=0;

      for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
      {
        $skipNull = true;
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");
        $n++;
        if($n >20)
         break;
      }

      $n=0;
      for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        if($skipNull == true)
          $skipNull = false;
        else
        $this->pdf->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");
        $n++;
        if($n >20)
          break;
      }
      
      $numBars=count($data);
      if($numBars > 0)
        $this->pdf->NbVal=$numBars;

         $colors=array('allocateEffect'=>array(239,208,102),'selectieEffect'=>array(190,130,76),'attributieEffect'=>array(222,181,93)); //


      $vBar = ($bGrafiek / ($this->pdf->NbVal ))/3; //4
      $bGrafiek = $vBar * ($this->pdf->NbVal );
      $eBaton = ($vBar * 80 / 100);
      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;
      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      foreach($data as $maand=>$maandData)
      {
        
        foreach($maandData as $type=>$val)
        {
          $color=$colors[$type];
          //Bar
          $xval = $XstartGrafiek + ($i + 1) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiek + $nulYpos;
          $hval = ($val * $unit);
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$color);
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3 && $eBaton > 4)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);
          $i++;
          }
          $i++;
          

          $this->pdf->Text($XstartGrafiek + ($i -2) * $vBar - $eBaton / 2,$YstartGrafiek +3 ,date('M',db2jul($maand)));
          
      }



     // $color=array(155,155,155);
     // $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
  }

}
?>
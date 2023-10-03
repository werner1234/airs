<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/11/23 12:59:28 $
 		File Versie					: $Revision: 1.7 $

 		$Log: RapportVAR_L51.php,v $
 		Revision 1.7  2019/11/23 12:59:28  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2019/11/20 16:19:15  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2019/11/16 17:12:28  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2018/10/17 07:48:11  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2018/08/18 12:40:15  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.2  2017/05/26 16:45:07  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/09/18 15:23:07  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2013/01/06 10:09:57  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2012/12/30 14:27:12  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2012/12/12 16:54:24  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/12/08 14:48:08  rvv
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
class RapportVAR_L51
{
	function RapportVAR_L51($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VAR";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

    $this->pdf->rapport_titel = "Kenmerken vastrentende instrumenten";

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


    
    $data=$this->bepaalWaarden();
   
    if($data=='geenRapport')
      return '';
  // $this->printRendement($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
   
   // $this->vastrentendeDeel();
		$y=$this->pdf->getY()+10;
    $this->rating(100,$y);
		$this->toonDuration(120+30,$y);


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
		$actueleWaardePortefeuille = $this->actueleWaardePortefeuille / $this->pdf->ValutaKoersEind;
		$resultaat = ($actueleWaardePortefeuille -$vergelijkWaarde - getStortingen($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$this->pdf->rapportageValuta) + getOnttrekkingen($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$this->pdf->rapportageValuta));
		$performance = performanceMeting($portefeuille, $rapportageDatumVanaf, $rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
		$this->pdf->ln(2);

		if(($this->pdf->GetY() + 22) >= $this->pdf->pagebreak)
		{
			$this->pdf->AddPage();
			$this->pdf->ln();
		}

		//$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),90,14);
		$this->pdf->ln(2);
		$this->pdf->SetWidths(array(40,30));
  	$this->pdf->SetAligns(array('L','R'));
    $this->pdf->row(array(vertaalTekst("Omvang in (€)",$this->pdf->rapport_taal),''));
    $this->pdf->row(array(vertaalTekst("%- van Portefeuille",$this->pdf->rapport_taal),'%'));
  	$this->pdf->row(array(vertaalTekst("Rendement in",$this->pdf->rapport_taal).' '.date('Y',$this->pdf->rapport_datum),$this->pdf->formatGetal($performance,2)." %"));
  	$this->pdf->row(array(vertaalTekst("Gemiddelde Yield",$this->pdf->rapport_taal),'%'));
    $this->pdf->row(array(vertaalTekst("Duration",$this->pdf->rapport_taal)));
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

  

  function printGrafieken()
  {
    global $__appvar;


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
    $kleurenSec=array();
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

  function bepaalWaarden()
  {
    global $__appvar;
    $totalen=array();
    $totalenCat=array();
    $totalenHcat=array();
  	//$this->pdf->templateVars['VARPaginas'] = $this->pdf->customPageNo;
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		unset($this->pdf->CellBorders);

		$this->cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		$this->cashfow->genereerTransacties();
		$this->cashfow->genereerRows();


		$DB = new DB();
		$this->db = new DB();
		$this->vastWhere=" AND ( TijdelijkeRapportage.hoofdcategorie='G-RISM' OR Fondsen.Lossingsdatum <> '0000-00-00')";


			  $query="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS actuelePortefeuilleWaardeEuro
			  FROM TijdelijkeRapportage
			  Left Join Fondsen ON Fondsen.Fonds = TijdelijkeRapportage.Fonds
			  WHERE TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND
			   TijdelijkeRapportage.portefeuille='".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek'];//.$this->vastWhere."";
    $DB->SQL($query);
    $waarde=$DB->lookupRecord();
    $waarde=$waarde['actuelePortefeuilleWaardeEuro'];

    $this->actueleWaardePortefeuille=$waarde;

		$query="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS actuelePortefeuilleWaardeEuro
			  FROM TijdelijkeRapportage
			  Left Join Fondsen ON Fondsen.Fonds = TijdelijkeRapportage.Fonds
			  WHERE TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND
			   TijdelijkeRapportage.portefeuille='".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek'].$this->vastWhere."";
		$DB->SQL($query);
		$waarde=$DB->lookupRecord();
		$waarde=$waarde['actuelePortefeuilleWaardeEuro'];

		$this->actueleWaardePortefeuilleVast=$waarde;

		$DB = new DB();
		$q = "SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'";
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$kleuren = unserialize($kleuren['grafiek_kleur']);
		$this->kleuren=$kleuren;

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
Left Join emittenten ON emittentPerFonds.emittent = emittenten.emittent AND emittentPerFonds.vermogensbeheerder = '$beheerder'
WHERE
TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille='".$this->portefeuille."'
".$__appvar['TijdelijkeRapportageMaakUniek'].$this->vastWhere."
GROUP BY
TijdelijkeRapportage.fonds,TijdelijkeRapportage.rekening
ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde,TijdelijkeRapportage.fondsOmschrijving,TijdelijkeRapportage.rekening";
		$DB->SQL($query);
		$DB->Query();
		if($DB->records()==0)
    {
      return 'geenRapport';
    }
  
    $this->pdf->addPage();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->templateVars['VARPaginas']=$this->pdf->page;

		while ($data=$DB->nextRecord())
		{

			$rente=getRenteParameters($data['fonds'], $this->rapportageDatum);
			foreach($rente as $key=>$value)
				$data[$key]=$value;

      if($data['fondsRating']=='')
        $fondsRating='NR';
      else
        $fondsRating=$data['fondsRating']; 
 
      $this->ratingVerdeling[$fondsRating]+=$data['actuelePortefeuilleWaardeEuro']/$this->actueleWaardePortefeuilleVast;


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
	         $aandeel=$data['actuelePortefeuilleWaardeEuro']/$this->actueleWaardePortefeuilleVast;

         //  $totalen['yield']+=$koers['Rentepercentage']*$data['totaalAantal']/$data['actuelePortefeuilleWaardeEuro']*$data['actueleValuta']*$aandeel;
           $totalen['yield']+=$koers['Rentepercentage']*$aandeel;
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
/*
      $this->pdf->row(array($this->formatGetal(
      $data['totaalAantal'],0),
      $data['fondsOmschrijving'],
      $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),
      $data['fondsRating'],
      $this->formatGetal($koers['Rentepercentage']*$data['totaalAantal']/$data['actuelePortefeuilleWaardeEuro']*$data['actueleValuta'],2)."%",
      $this->formatGetal($ytm,2)."%",
      $this->formatGetal($modifiedDuration,2),
      $this->formatGetal($restLooptijd,2) ));
*/      
      $lastcategorieOmschrijving=$data['categorieOmschrijving'];
    }
		 $this->pdf->underlinePercentage=0.8;
    $this->pdf->CellBorders = array(array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->ln(2);
    $this->pdf->SetWidths(array(30+60,20,20,20,20,20,20,20));
    $this->pdf->SetAligns(array('L','R','R','R','R','R','R','R'));
    $this->pdf->row(array(vertaalTekst("Waarde obligaties",$this->pdf->rapport_taal),
											$this->formatGetal($totalen['actuelePortefeuilleWaardeEuro'],0),
    $this->formatGetal($totalen['yield'],3),//."%",
    $this->formatGetal($totalen['ytm'],2),//."%",
    $this->formatGetal($totalen['modifiedDuration'],2),
    $this->formatGetal($totalen['restLooptijd'],2),
		$this->formatGetal($totalen['aandeel']*100,2)));
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->underlinePercentage);

  }
  
  function correctLegentHeight($regels)
  {
    return array($this->pdf->GetX()+60,$this->pdf->GetY()+ 35 -($regels*4)/2);
    
  }
  
  function rating($x,$y)
  {
		global $__appvar;
    $db=new DB();
    $query="SELECT rating,Omschrijving FROM Rating order by afdrukvolgorde";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      $ratingVolgorde[$data['rating']]=$data['Omschrijving'];
    }


    $extraX=20;
		$this->pdf->SetXY($this->pdf->marge+$extraX,$y);
   // $y=$this->pdf->GetY();
    /*
  	$this->pdf->SetWidths(array(30,30));
		$this->pdf->SetAligns(array("L",'R'));
    $this->pdf->fillCell=array(1,1);
		$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
		 $this->pdf->row(array(vertaalTekst("Rating",$this->pdf->rapport_taal),
		 vertaalTekst("Aandeel %",$this->pdf->rapport_taal)));
    $this->pdf->CellBorders = array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->fillCell);
    $this->pdf->SetFillColor(0,0,0);
    $this->pdf->SetTextColor(0,0,0);
    $totaal=0;
    $ratingGrafiek=array();
    $colors=array();
		//listarray($this->kleuren);
    */
    $ratingGrafiek=array();
    foreach($ratingVolgorde as $rating=>$omschrijving)
    {
      if($this->ratingVerdeling[$rating] <> 0)
      {
        //$this->pdf->Row(array($omschrijving,$this->formatGetal($this->ratingVerdeling[$rating]*100,2)));
			
        //$totaal+=$this->ratingVerdeling[$rating]*100;
        $omschrijving=$omschrijving." (".$this->formatGetal($this->ratingVerdeling[$rating]*100,1).")";
        $colors[]=array($this->kleuren['Rating'][$rating]['R']['value'],$this->kleuren['Rating'][$rating]['G']['value'],$this->kleuren['Rating'][$rating]['B']['value']);
        $ratingGrafiek[$omschrijving]=$this->ratingVerdeling[$rating]*100;
      }
    }
    
    /*
    $this->pdf->CellBorders = array(array('T'),array('T'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('Totaal',$this->formatGetal($totaal,2)));
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    $this->pdf->SetXY($x,$y);
    */
    /*
    $tinten=array(1,0.5,0.7);
foreach($tinten as $tint)
{
$colors[]=array($this->pdf->blue[0]*$tint,$this->pdf->blue[1]*$tint,$this->pdf->blue[2]*$tint);
$colors[]=array($this->pdf->midblue[0]*$tint,$this->pdf->midblue[1]*$tint,$this->pdf->midblue[2]*$tint);//$this->pdf->midblue;
$colors[]=array($this->pdf->lightblue[0]*$tint,$this->pdf->lightblue[1]*$tint,$this->pdf->lightblue[2]*$tint);//$this->pdf->lightblue;
$colors[]=array($this->pdf->green[0]*$tint,$this->pdf->green[1]*$tint,$this->pdf->green[2]*$tint);//$this->pdf->green;
$colors[]=array($this->pdf->lightgreen[0]*$tint,$this->pdf->lightgreen[1]*$tint,$this->pdf->lightgreen[2]*$tint);//$this->pdf->lightgreen;
}
    */
    $chartsize=55;
  //  $headerHeight=65;
    //$lwb=(297/2)-$this->pdf->marge; //133.5
    //$vwh=((210-$headerHeight-$this->pdf->marge)/2+$headerHeight)-$headerHeight;
   // $legendaStart=array($this->pdf->marge+$chartsize+$extraX+10,$headerHeight+10);
    //$legendaStart=array(160,$y+10);
    $legendaStart=$this->correctLegentHeight(count($ratingGrafiek));
    PieChart_L51($this->pdf,$chartsize,$chartsize,$ratingGrafiek,'%l',$colors,'Rating',$legendaStart);//'%l (%p)'
  
  
    
    
  }

	function toonDuration($x,$y)
	{
		$this->pdf->setXY($x,$y);
		global $__appvar;
		$DB=new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
			"FROM TijdelijkeRapportage Left Join Fondsen ON Fondsen.Fonds = TijdelijkeRapportage.Fonds WHERE ".
			" rapportageDatum ='".$this->rapportageDatum."' AND ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' "
			.$__appvar['TijdelijkeRapportageMaakUniek']. $this->vastWhere;
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();

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
".$__appvar['TijdelijkeRapportageMaakUniek']. $this->vastWhere."
GROUP BY
TijdelijkeRapportage.fonds,TijdelijkeRapportage.rekening
ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde,TijdelijkeRapportage.fondsOmschrijving,TijdelijkeRapportage.rekening";

		$this->db=new DB();
		$DB->SQL($query);
		$DB->Query();
		$durationChart=array('0-1'=>0,'1-3'=>0,'3-7'=>0,'7-12'=>0,'>12'=>0,'overig'=>0);


		$actueleWaardePortefeuille=0;
		while ($data=$DB->nextRecord())
		{
			$rente=getRenteParameters($data['fonds'], $this->rapportageDatum);
			foreach($rente as $key=>$value)
				$data[$key]=$value;
			$actueleWaardePortefeuille+=$data['actuelePortefeuilleWaardeEuro'];



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

			$aandeel=$data['actuelePortefeuilleWaardeEuro']/$totaalWaarde['totaal']*100;

			if($lossingsJul > 0)
			{

				//$this->huidigeWaardeTotaal += $fonds['actuelePortefeuilleWaardeEuro'];
				//$this->lossingsWaardeTotaal += $fonds['totaalAantal'] * 100 * $fonds['fondsEenheid'] * $fonds['actueleValuta'];
				$jaar = ($lossingsJul-$renteVanafJul)/31556925.96;

				$p = $data['actueleFonds'];
				$r = $koers['Rentepercentage']/100;
				$b = $this->cashfow->fondsDataKeyed[$data['fonds']]['lossingskoers'];
				$year = $jaar;

				$ytm=  $this->cashfow->bondYTM($p,$r,$b,$year)*100;

				$restLooptijd=($lossingsJul-$this->pdf->rapport_datum)/31556925.96;

				$duration=$this->cashfow->waardePerFonds[$data['fonds']]['ActueelWaardeJaar']/$this->cashfow->waardePerFonds[$data['fonds']]['ActueelWaarde'];
				if($data['variabeleCoupon'] == 1 && $renteDag <> 0)
					$modifiedDuration=($renteDag-db2jul($this->rapportageDatum))/86400/365;
				else
					$modifiedDuration=$duration/(1+$ytm/100);


				$totalen['yield']+=$koers['Rentepercentage']*$data['totaalAantal']/$data['actuelePortefeuilleWaardeEuro']*$data['actueleValuta']*$aandeel;
				$totalen['ytm']+=$ytm*$aandeel;
				$totalen['duration']+=$duration*$aandeel;
				$totalen['modifiedDuration']+=$modifiedDuration*$aandeel;
				$totalen['restLooptijd']+=$restLooptijd*$aandeel;


				if($duration<1)
					$durationChart['0-1']+=$aandeel;
				elseif($duration<3)
					$durationChart['1-3']+=$aandeel;
				elseif($duration<7)
					$durationChart['3-7']+=$aandeel;
				elseif($duration<12)
					$durationChart['7-12']+=$aandeel;
				else
					$durationChart['>12']+=$aandeel;


			}
			else
			{
				$durationChart['overig']+=$aandeel;
			}
		}

		//$durationChartKleuren=array();
		//$kleuren=array('0-1'=>array(0,200,250),'1-3'=>array(10,190,240),'3-7'=>array(20,160,230),'7-12'=>array(30,140,220),'>12'=>array(40,110,220),'overig'=>array(40,80,210));
		//  arsort ($durationChart);
		/*
		if(count($durationChart)<7)
			$kleuren=$this->standaardKleurenKort;
		else
			$kleuren=$this->standaardKleurenLang;
		*/
		$kleuren=array();
		foreach($this->kleuren['Rating'] as $rating=>$kleurData)
		{
			$kleuren[] = array($this->kleuren['Rating'][$rating]['R']['value'], $this->kleuren['Rating'][$rating]['G']['value'], $this->kleuren['Rating'][$rating]['B']['value']);
		}
		//listarray($kleuren);
		//listarray($durationChart);
		$durationChartFiltered=array();
		$i=0;
		foreach($durationChart as $key=>$value)
		{
			if($value<>0)
			{
				$durationChartKleuren[] = $kleuren[$i];
				$durationChartFiltered[$key."  (".$this->formatGetal($value,1).' %)']=$value;
				$i++;
			}
			else
			{
				unset($durationChart[$key]);
			}
		}
/*
		$this->pdf->SetXY($this->pdf->marge,$y);
		// $y=$this->pdf->GetY();
		$this->pdf->SetWidths(array(30,30));
		$this->pdf->SetAligns(array("L",'R'));
		$this->pdf->fillCell=array(1,1);
		$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
		$this->pdf->row(array(vertaalTekst("Duration",$this->pdf->rapport_taal),
											vertaalTekst("Aandeel %",$this->pdf->rapport_taal)));
		$this->pdf->CellBorders = array();
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		unset($this->pdf->fillCell);
		$this->pdf->SetFillColor(0,0,0);
		$this->pdf->SetTextColor(0,0,0);
		$totaal=0;
		$ratingGrafiek=array();
		//listarray($this->kleuren);
		foreach($durationChartFiltered as $omschrijving=>$percentage)
		{
			if($percentage <> 0)
			{
				$this->pdf->Row(array($omschrijving,$this->formatGetal($percentage,2)));
			//	$colors[]=array($this->kleuren['Rating'][$rating]['R']['value'],$this->kleuren['Rating'][$rating]['G']['value'],$this->kleuren['Rating'][$rating]['B']['value']);
				$totaal+=$percentage;

			}
		}

		$this->pdf->CellBorders = array(array('T'),array('T'));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->Row(array('Totaal',$this->formatGetal($totaal,2)));
		unset($this->pdf->CellBorders);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
*/
		$this->pdf->SetXY($x,$y);


		//$legendaStart=array(160,$y+10);
    $chartsize=55;

    $headerHeight=65;
    //$legendaStart=array($this->pdf->getX()+$chartsize+10,$headerHeight+10);
    $legendaStart=$this->correctLegentHeight(count($durationChartFiltered));
		PieChart_L51($this->pdf,55,55,$durationChartFiltered,'%l',$durationChartKleuren,vertaalTekst('Duration',$this->pdf->rapport_taal),$legendaStart);//'%l (%p)'


	}

}
?>
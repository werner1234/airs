<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 		File Versie					: $Revision: 1.4 $
 		
 		$Log: RapportOIB_C_L16.php,v $
 		Revision 1.4  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.3  2009/03/14 13:25:06  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2007/11/16 11:25:30  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2007/09/26 15:31:29  rvv
 		*** empty log message ***
 		
 	
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

include_once($__appvar["basedir"]."/classes/barGraph.php");

class RapportOIB_L16
{
	function RapportOIB_L16($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		if($this->pdf->rapport_OIB_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_OIB_titel;
		else 
			$this->pdf->rapport_titel = "Onderverdeling in beleggingscategorie";
		
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
		
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = array();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);		
		$this->kleuren= array();
		$this->kleuren = $allekleuren['OIB'];

		//OpgelopenRente
		$this->kleuren['rente']=	$this->kleuren['Opgelopen Rente'];
	
	}
	
	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}
	
	function getCategorieData($datum)
	{
	  $data = array();
	  $DB = new DB();
	  

  
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$datum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde[totaal]; 
		
		$query = "SELECT
		Beleggingscategorien.Afdrukvolgorde,
    Beleggingscategorien.Omschrijving,
    TijdelijkeRapportage.beleggingscategorie, 
    SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS totaal 
    FROM TijdelijkeRapportage 
    LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie) 
    WHERE 
    TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND 
    TijdelijkeRapportage.type = 'fondsen' AND 
    TijdelijkeRapportage.rapportageDatum = '".$datum."'  
    ".$__appvar['TijdelijkeRapportageMaakUniek']."
    GROUP BY 
    TijdelijkeRapportage.beleggingscategorie
    ORDER BY Beleggingscategorien.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);	

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		
		while($categorien = $DB->NextRecord())
		{
		  if(round($categorien['totaal'],2) != 0.00)
      $data[$categorien['beleggingscategorie']]=array('omschrijving'=>$categorien['Omschrijving'],
                                                      'waarde'=>$categorien['totaal'],
                                                      'percentage'=>$categorien['totaal']/$totaalWaarde*100,
                                                      'kleur'=>$this->kleuren[$categorien['beleggingscategorie']],
                                                      'volgorde'=>$categorien['Afdrukvolgorde']);

		}
		
		// selecteer rente
    $query = "SELECT 
    TijdelijkeRapportage.beleggingscategorie,
    SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) totaal 
    FROM TijdelijkeRapportage 
    WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND 
    TijdelijkeRapportage.type = 'rente' AND 
    TijdelijkeRapportage.rapportageDatum = '".$datum."' 
    ".$__appvar['TijdelijkeRapportageMaakUniek']." 
    GROUP BY TijdelijkeRapportage.beleggingscategorie ";
    debugSpecial($query,__FILE__,__LINE__);			

		$DB->SQL($query);
		$DB->Query();
	
		if($DB->records() > 0)
		{

			
			while($categorien = $DB->NextRecord())
			{
			  $data['rente']['omschrijving'] = 'Rente';
        $data['rente']['waarde'] += $categorien['totaal'];
			}
			$data['rente']['percentage'] = $data['rente']['waarde']/$totaalWaarde*100;
			$data['rente']['kleur'] = $this->kleuren['rente'];
			$data['rente']['volgorde'] = 100;
		}
		
		$query= "SELECT TijdelijkeRapportage.fondsOmschrijving, 
    SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS totaal
    FROM TijdelijkeRapportage
    WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND 
    TijdelijkeRapportage.type = 'rekening' AND 
    TijdelijkeRapportage.rapportageDatum = '".$datum."'   
    ".$__appvar['TijdelijkeRapportageMaakUniek']." 
    GROUP BY TijdelijkeRapportage.fondsOmschrijving";

		$DB->SQL($query);
		$DB->Query();
		
		if($DB->records() > 0)
		{
		 
			while($categorien = $DB->NextRecord())
			{
			  $data['liquiditeiten']['omschrijving'] = 'Liquiditeiten';
        $data['liquiditeiten']['waarde'] += $categorien['totaal'];
			}
			$data['liquiditeiten']['percentage'] = $data['liquiditeiten']['waarde']/$totaalWaarde*100;
			$data['liquiditeiten']['kleur'] = $this->kleuren['Liquiditeiten'];
			$data['liquiditeiten']['volgorde'] = 200;
		}
	  
		$totaal =0;
		foreach ($data as $categorie)
		{
		  $totaal += $categorie['waarde'];
		}
		if(round($totaal,1)!=round($totaalWaarde,1))
		  echo $this->portefeuille.": Bepaalde waarde $totaal komt niet overeen met $totaalWaarde <br>\n";

		
		return $data;
	}
	
	function sortWaarden($dataStart,$dataStop)
	{
	  foreach ($dataStop as $cat=>$waarden)
    {
      if(!$dataStart[$cat])
      {
      $dataStart[$cat]['omschrijving'] = $dataStop[$cat]['omschrijving'];
      $dataStart[$cat]['waarde'] = 0;
      $dataStart[$cat]['percentage'] = 0;
      $dataStart[$cat]['kleur']  = $dataStop[$cat]['kleur'] ;      
      $dataStart[$cat]['volgorde']  = $dataStop[$cat]['volgorde'] ;        
      }
    }
    reset($dataStop);

    foreach ($dataStart as $cat=>$waarden)
    {
      if(!$dataStop[$cat])
      {
      $dataStop[$cat]['omschrijving'] = $dataStart[$cat]['omschrijving'];
      $dataStop[$cat]['waarde'] = 0;
      $dataStop[$cat]['percentage'] = 0;
      $dataStop[$cat]['kleur']  = $dataStart[$cat]['kleur'] ;     
      $dataStop[$cat]['volgorde']  = $dataStart[$cat]['volgorde'] ;          
      }
    }
    reset($dataStart);
    
    $tmpStart = array();
    foreach ($dataStart as $cat=>$waarden)
    {
      $tmpStart[$waarden['volgorde']][$cat] = $waarden;
    }
    ksort($tmpStart);
    $dataStart = array();
    foreach ($tmpStart as $volgorde=>$waarden)
    {
      $cat = key($waarden);
      $dataStart[$cat] = $waarden[$cat];
    }   

    
    $tmpStop = array();
    foreach ($dataStop as $cat=>$waarden)
    {
      $tmpStop[$waarden['volgorde']][$cat] = $waarden;
    }
    ksort($tmpStop);
    $dataStop = array();
    foreach ($tmpStop as $volgorde=>$waarden)
    {
      $cat = key($waarden);
      $dataStop[$cat] = $waarden[$cat];
    }   
    

    
  $data['start']=$dataStart;
  $data['stop']=$dataStop;
    
	  
	return $data;  
	  
	}
	
	function toonCategorieData($data,$datum)
	{
	  
	  $this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		
		$this->pdf->ln();
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
	  $this->pdf->row(array(jul2form(db2jul($datum)),'Waarde','in %'));	
	  $this->pdf->ln(2);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_omschr_fontstyle,$this->pdf->rapport_fontsize);
		
	  foreach ($data as $categorie)
		{
		  
		  $this->pdf->row(array($categorie['omschrijving'],
		                        $this->formatGetal($categorie['waarde'],2),
		                        $this->formatGetal($categorie['percentage'],2)));
		  $totaalWaarde += $categorie['waarde'];
		  $totaalPercentage += $categorie['percentage'];
		}
		
	  $this->pdf->ln();
	  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
	  $this->pdf->row(array('Totaal',$this->formatGetal($totaalWaarde,2),$this->formatGetal($totaalPercentage,2),));	
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_omschr_fontstyle,$this->pdf->rapport_fontsize);
		
	}
	
	function writeRapport()
	{
		global $__appvar;
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
		
		// voor kopjes
		$this->pdf->widthA = array(70,25,25);
		$this->pdf->alignA = array('L','R','R');
		
		$this->pdf->AddPage();
		
		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde[totaal];

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$beginWaarde = $DB->nextRecord();
		$beginWaarde = $beginWaarde[totaal];		

		$actueleWaardePortefeuille = 0;

//

$dataStart = $this->getCategorieData($this->rapportageDatumVanaf);
$dataStop = $this->getCategorieData($this->rapportageDatum);

$data = $this->sortWaarden($dataStart,$dataStop);

$dataStart = $data['start'];
$dataStop = $data['stop'];


$this->pdf->setXY($this->pdf->marge,45);  
$this->toonCategorieData($dataStart,$this->rapportageDatumVanaf);
$plot = new barGraph($this->pdf);
$plot->setXY(140,105);    
$plot->VBarDiagram(80,50,$dataStart);

$this->pdf->setY(110);   

$this->toonCategorieData($dataStop,$this->rapportageDatum);

$plot->setXY(140,170);    
$plot->VBarDiagram(80,50,$dataStop);

$plot->setXY(10,170); 
$this->pdf->ln(8);



		  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));

	    if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01")
	    {
	      if($this->pdf->engineII == false)
	      {
	        $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,"$RapStartJaar-01-01",true);
          vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,"$RapStartJaar-01-01"); 
	      }
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='$RapStartJaar-01-01' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		  debugSpecial($query,__FILE__,__LINE__);
		  $DB->SQL($query);
		  $DB->Query();
		  $janWaarde = $DB->nextRecord();
		  $janWaarde = $janWaarde[totaal];	
		  
	    }
	    else 
	      $janWaarde = $beginWaarde;

	    $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,$this->rapportageDatumVanaf,true);
      vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,$this->rapportageDatumVanaf); 
		  
	    $performanceJaar = performanceMeting($this->portefeuille,"$RapStartJaar-01-01",$this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
		  $performancePeriode = performanceMeting($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
		  
	  $this->pdf->row(array('Gewogen beleggingsresultaat over deze periode',$this->formatGetal($totaalWaarde - $beginWaarde,2),$this->formatGetal($performancePeriode,2).'%'));
	  $this->pdf->row(array('Vanaf 1 januari '.$RapStartJaar,$this->formatGetal($totaalWaarde - $janWaarde,2),$this->formatGetal($performanceJaar,2).'%'));
		  
		  
	}
}
?>
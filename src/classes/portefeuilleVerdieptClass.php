<?php

class portefeuilleVerdiept
{
  function portefeuilleVerdiept($pdf,$portefeuille,$rapportageDatum)
  {
    global $__appvar;
    $this->pdf = &$pdf;
    $this->rapportageDatum = $rapportageDatum;
    $this->portefeuille=$portefeuille;
    
    if(!isset($this->pdf->sector))
	  	$this->getSectoren();	

		if(!isset($this->pdf->regios))
		  $this->getRegios();	
    
    $DB = new DB();
    $query = "SELECT 
                  TijdelijkeRapportage.fonds,TijdelijkeRapportage.totaalAantal,Fondsen.portefeuille, TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind." as aandeelWaarde 
                FROM 
                  TijdelijkeRapportage 
                JOIN Fondsen on (TijdelijkeRapportage.fonds = Fondsen.Fonds)
                INNER JOIN Portefeuilles AS FondsPortefeuille ON Fondsen.Portefeuille = FondsPortefeuille.Portefeuille
                INNER JOIN Portefeuilles ON TijdelijkeRapportage.portefeuille = Portefeuilles.Portefeuille
                INNER JOIN VermogensbeheerdersPerBedrijf AS FondsBedrijf ON FondsPortefeuille.Vermogensbeheerder = FondsBedrijf.Vermogensbeheerder
                INNER JOIN VermogensbeheerdersPerBedrijf AS PortefueilleBedrijf ON Portefeuilles.Vermogensbeheerder = PortefueilleBedrijf.Vermogensbeheerder
                WHERE
                  FondsBedrijf.Bedrijf=PortefueilleBedrijf.Bedrijf AND
                  TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatum."' AND 
						      TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
                  Fondsen.Portefeuille <> ''  ".$__appvar['TijdelijkeRapportageMaakUniek']; //

    $DB->SQL($query);
		$DB->Query();
  
    $this->verdiepteData=array();
				 
		while($data = $DB->nextRecord())
		{
		  $this->verdiepteData[] = $data;
		  $this->FondsPortefeuilleData[$data['fonds']] = $data['portefeuille'];
		}
		foreach($this->verdiepteData as $huisFonds)
		{
			$query="SELECT
Rekeningen.Portefeuille,
Rekeningmutaties.Fonds,
Rekeningmutaties.id,
Rekeningmutaties.Rekening,
Rekeningmutaties.Afschriftnummer,
Rekeningmutaties.Volgnummer,
Rekeningmutaties.Omschrijving,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Aantal,
Rekeningmutaties.Transactietype
FROM
Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
WHERE Rekeningmutaties.Boekdatum <='".$this->rapportageDatum."'  AND Rekeningen.Portefeuille='".$this->portefeuille."' AND 
Rekeningmutaties.Fonds = '".mysql_real_escape_string($huisFonds['fonds'])."' AND Rekeningmutaties.Grootboekrekening='FONDS' AND Rekeningmutaties.Transactietype IN('A') ";//,'B'
			$DB->SQL($query);
			$DB->Query();
			$totaalAantal=0;
			while($data = $DB->nextRecord())
			{
				$meetPunten[$huisFonds['fonds']][$data['Boekdatum']]=array('aantal'=>$data['Aantal'],'aandeel'=>$data['Aantal']/$huisFonds['totaalAantal']);
				$totaalAantal+=$data['Aantal'];
			}
			foreach($meetPunten[$huisFonds['fonds']]as $boekdatum=>$gegevens)
			{
				$newAandeel=$gegevens['aantal']/$totaalAantal;
				if($newAandeel <> $gegevens['aandeel'])
				{
					//echo "Aandeel ".$huisFonds['fonds']." opnieuw bepaald. ($newAandeel <> ".$gegevens['aandeel'].")";
					$meetPunten[$huisFonds['fonds']][$data['Boekdatum']]['aandeel'] = $newAandeel;
				}
			}

		}
    $this->meetPunten=$meetPunten;
  }
  
  function getVerdieptePortefeuilles()
  {
    return $this->verdiepteData;
  }
  
  function getFondsen()
  {
    $tmparray = array();
    foreach ($this->verdiepteData as $data)
    {
      $tmparray[] = $data['fonds'];
    }
    return $tmparray;
    
  }
  
  
  
  
  function bepaalVerdeling($fonds,$portefeuille,$typen,$datum,$order='',$vanafdatum='',$clean=true)
	{
		global $__appvar;
		$this->order=$order;
		$this->fondsPortefeuille=$portefeuille;
	  if($portefeuille != $this->portefeuille)
	  {
			if($vanafdatum=='')
				$vanafdatum=$datum;

	   	$fondswaarden =  berekenPortefeuilleWaarde($portefeuille, $datum,0,$this->pdf->rapportageValuta,$vanafdatum);
	    verwijderTijdelijkeTabel($portefeuille,$datum);
	    vulTijdelijkeTabel($fondswaarden ,$portefeuille,$datum);
	  }
	  $DB=new DB();
	  
	  $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
					 "FROM TijdelijkeRapportage WHERE ".
					 " rapportageDatum ='".$datum."' AND ".$extraquery.
					 " portefeuille = '".$portefeuille."' "
					 .$__appvar['TijdelijkeRapportageMaakUniek'];
	  debugSpecial($query,__FILE__,__LINE__);				 
	  $DB->SQL($query); 
	  $DB->Query();
	  $totaalWaarde = $DB->nextRecord();
	  $totaalWaarde = $totaalWaarde['totaal'];	
	  
	  $this->pdf->fondsPortefeuille[$fonds]['totaalWaarde']=$totaalWaarde;

	  $query = "SELECT
			SUM(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro
			FROM 
			TijdelijkeRapportage 
			WHERE 
			TijdelijkeRapportage.Portefeuille = '".$portefeuille."' AND 
			TijdelijkeRapportage.rapportageDatum = '".$datum."' AND
 			TijdelijkeRapportage.Type = 'rekening' 
			" .$__appvar['TijdelijkeRapportageMaakUniek'];
	  debugSpecial($query,__FILE__,__LINE__);				 
	  $DB->SQL($query); 
	  $DB->Query();
	  $totaalLiquiditeiten = $DB->nextRecord();
	  $totaalLiquiditeiten = $totaalLiquiditeiten['WaardeEuro'];

	  $this->pdf->fondsPortefeuille[$fonds]['totaalLiquiditeiten']=$totaalLiquiditeiten;
		$this->pdf->fondsPortefeuille[$fonds]['totaalFondsen']=$this->pdf->fondsPortefeuille[$fonds]['totaalWaarde']-$this->pdf->fondsPortefeuille[$fonds]['totaalLiquiditeiten'];

	  $data=array();
	  
	  foreach ($typen as $type)
	  {
	    switch ($type)
	    {
	      case 'sector':
	        $this->setSectorVerdeling($portefeuille,$fonds,$datum);
	      break;  
	      case 'regio':
	        $this->setRegioVerdeling($portefeuille,$fonds,$datum);
	      break;  
	      case 'fonds':
	        $this->setFondsVerdeling($portefeuille,$fonds,$datum);
	      break;  
	    
	    }
	  }

	  if($clean==true && $portefeuille != $this->portefeuille)
	    verwijderTijdelijkeTabel($portefeuille,$datum);
	}  
  
  
  function getSectoren()
	{
	  $db=new DB();
	  $query = "SELECT	Beleggingssectoren.Omschrijving, Beleggingssectoren.Beleggingssector FROM Beleggingssectoren ";
	  $db->SQL($query);
	  $db->Query();
	  While ($data = $db->nextRecord())
	    $this->pdf->sector[$data['Beleggingssector']]=$data['Omschrijving'];
	    
	  $this->pdf->sector['Geen sector']='Geen sector';
	  $this->pdf->sector['Liquiditeiten']='liquiditeiten';
  }
  
	function getRegios()
	{
	  $db=new DB();
	  $query = "SELECT	Regios.Regio,	Regios.Omschrijving FROM Regios ";
	  $db->SQL($query);
	  $db->Query();
	  While ($data = $db->nextRecord())
	    $this->pdf->regio[$data['Beleggingssector']]=$data['Omschrijving'];
	    
	  $this->pdf->regio['Geen regio']='Geen regio';
	  $this->pdf->regio['Liquiditeiten']='liquiditeiten';
	}
	
	function getFondsVerdeling($fonds,$num=10)
	{
		global $__appvar;
	  $n=1;
	  $db=new DB();
	  $data = $this->pdf->fondsPortefeuille[$fonds]['verdeling']['fondsen'];
	  foreach ($data as $fonds=>$gegevens)
	  {

	    
	    $query="SELECT Fondsen.Fonds,Fondsen.Valuta as valuta,Fondseenheid,
              (SELECT Koers FROM Fondskoersen WHERE  Fondskoersen.Fonds=Fondsen.Fonds AND Datum <= '".$this->rapportageDatum."' ORDER BY Datum DESC limit 1) as fondskoers,
              (SELECT Koers FROM Valutakoersen WHERE Valutakoersen.Valuta=Fondsen.Valuta AND  Datum <= '".$this->rapportageDatum."' ORDER BY Datum DESC limit 1) as valutakoers
              FROM Fondsen WHERE Fondsen.Fonds='$fonds'";
	    $db->SQL($query);
	    $extraFondsData=$db->lookupRecord();
	    $gegevens=array_merge($gegevens,$extraFondsData);
	    $tmp[$fonds]=$gegevens;
	    if($n >= $num)
	      break;
	    $n++;   
	  }
	  return $tmp;
	}
	
  
	function setSectorVerdeling($portefeuille,$fonds,$rapportageDatum)
	{
	  global $__appvar;
	//echo "bepalen SectorVerdeling($portefeuille,$fonds,$rapportageDatum) <br>\n";

	$totaalLiquiditeiten =  $this->pdf->fondsPortefeuille[$fonds]['totaalLiquiditeiten'];
  $totaalWaarde = $this->pdf->fondsPortefeuille[$fonds]['totaalWaarde'];
	
  if ($this->pdf->rapport_GRAFIEK_sortering == 1)
    $order = 'Beleggingssectoren.Afdrukvolgorde ASC';
  else 
    $order = 'WaardeEuro desc';		
	
	$query = "SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as WaardeEuro, 
			SectorenPerHoofdsector.Hoofdsector,
 			Beleggingssectoren.Omschrijving,
 			Beleggingssectoren.Beleggingssector 
			FROM 
			TijdelijkeRapportage
			LEFT JOIN SectorenPerHoofdsector on TijdelijkeRapportage.Beleggingssector = SectorenPerHoofdsector.Beleggingssector 
			LEFT JOIN Beleggingssectoren on SectorenPerHoofdsector.Hoofdsector = Beleggingssectoren.Beleggingssector
			WHERE  
			rapportageDatum = '".$rapportageDatum."' 
 			AND Portefeuille = '".$portefeuille."' "
			.$__appvar['TijdelijkeRapportageMaakUniek']."
			GROUP BY SectorenPerHoofdsector.Hoofdsector ORDER BY $order;"; 
	debugSpecial($query,__FILE__,__LINE__);	
	$DB=new DB();
	$DB->SQL($query); 
	$DB->Query();
	
	while($sec = $DB->nextRecord())
	{
	  if ($sec['Beleggingssector']== "")
	  {
	  	if (round($sec['WaardeEuro'] - $totaalLiquiditeiten,1) != 0)
	  	{
	  		if(round($totaalLiquiditeiten,2) != 0)
	  		{
			  $data['hoofdsectoren']['Liquiditeiten']['percentage']=$totaalLiquiditeiten/$totaalWaarde;
			  $data['hoofdsectoren']['Liquiditeiten']['Omschrijving']='Liquiditeiten';	  		
			  $sec['WaardeEuro'] = $sec['WaardeEuro'] - $totaalLiquiditeiten;
	  		}
			$sec['Omschrijving']= 'Geen sector';
			$sec['Beleggingssector']= 'Geen sector';
	  	}
	  	else
	  	{ 
		    $sec['Omschrijving']= 'Liquiditeiten';
		    $sec['Beleggingssector']= 'Liquiditeiten';
	  	}
	  }
	  
	  if ($this->pdf->rapport_GRAFIEK_sortering == 1 && $sec['Omschrijving'] == "Liquiditeiten" ) //liquiditeiten later toevoegen
    {
     $liquididiteiten['waardeEur'] = $sec['WaardeEuro'];
     $liquididiteiten['Omschrijving'] = "Liquiditeiten";
    }
    else 
    {	  
	    $data['hoofdsectoren'][$sec['Beleggingssector']]['percentage']=$sec['WaardeEuro']/$totaalWaarde;
	    $data['hoofdsectoren'][$sec['Beleggingssector']]['Omschrijving']=$sec['Omschrijving'];		
    }
	}	
	
	if ($this->pdf->rapport_GRAFIEK_sortering == 1 && round($liquididiteiten['waardeEur'],2) != 0 ) // liquiditeiten toevoegen
	{
	  $data['hoofdsectoren']['Liquiditeiten']['percentage']     = $liquididiteiten['waardeEur']/$totaalWaarde;
	  $data['hoofdsectoren']['Liquiditeiten']['Omschrijving']  = $liquididiteiten['Omschrijving'];
	}
	  
	$this->pdf->fondsPortefeuille[$fonds]['verdeling']['hoofdsectoren']=$data['hoofdsectoren'];

	}
	
	
	
	function setRegioVerdeling($portefeuille,$fonds,$rapportageDatum)
	{
		global $__appvar;
	//  echo "bepalen RegioVerdeling($portefeuille,$rapportageDatum) <br>\n";
	  
	
    $totaalWaarde = $this->pdf->fondsPortefeuille[$fonds]['totaalWaarde'];
	  
	  if ($this->pdf->rapport_GRAFIEK_sortering == 1)
      $order = 'Regios.Afdrukvolgorde ASC';
    else 
      $order = 'WaardeEuro desc';	
		
	  $query="SELECT 
			TijdelijkeRapportage.Regio,
			Regios.Omschrijving,
			sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro 
			FROM TijdelijkeRapportage
			LEFT JOIN Regios ON TijdelijkeRapportage.Regio = Regios.Regio
			WHERE TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."' 
			AND TijdelijkeRapportage.Portefeuille = '".$portefeuille."' "
			.$__appvar['TijdelijkeRapportageMaakUniek']."
			GROUP BY TijdelijkeRapportage.Regio 
			ORDER BY $order"; 
	  debugSpecial($query,__FILE__,__LINE__);			
	  $DB = new DB();
	  $DB->SQL($query);
	  $DB->Query();
	  while($reg = $DB->nextRecord())
	  { 
		  if ($reg['Regio']== "")
		  {
		  $reg['Omschrijving']="Geen regio";
	  	$reg['Regio'] = "Geen regio";
		  }
		  
	  $data['regio'][$reg['Regio']]['waardeEur']=$reg['WaardeEuro'];
	  $data['regio'][$reg['Regio']]['Omschrijving']=$reg['Omschrijving'];
	  }	

//Ophalen regio liquiditeiten.

	$query = "SELECT 
			sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
			TijdelijkeRapportage.valuta,
 			ValutaPerRegio.Regio
			FROM TijdelijkeRapportage
			LEFT JOIN  ValutaPerRegio on  ValutaPerRegio.Valuta = TijdelijkeRapportage.valuta
			WHERE TijdelijkeRapportage.Portefeuille =  '".$portefeuille."' AND 
			TijdelijkeRapportage.type = 'rekening'
			AND TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."' ".
		  $__appvar['TijdelijkeRapportageMaakUniek']." 
			GROUP BY TijdelijkeRapportage.valuta";
	$DB->SQL($query); 
	$DB->Query();	  
	while($valuta = $DB->nextRecord())
	{
	if ($valuta['Regio'] == '')
	  $valuta['Regio'] = 'Geen regio';
	$data['regio'][$valuta['Regio']]['waardeEur'] = $data['regio'][$valuta['Regio']]['waardeEur'] + $valuta['WaardeEuro'];
	$data['regio']['Geen regio']['waardeEur'] = $data['regio']['Geen regio']['waardeEur'] - $valuta['WaardeEuro'];
	}
	foreach ($data['regio'] as $regio=>$waarden)
	{
	  $data['regio'][$regio]['percentage']=$waarden['waardeEur']/$totaalWaarde;
	}

	$this->pdf->fondsPortefeuille[$fonds]['verdeling']['regio']=$data['regio'];

	}


	function setFondsVerdeling($portefeuille,$fonds,$rapportageDatum)
	{
		global $__appvar;
	//  echo "bepalen RegioVerdeling($portefeuille,$rapportageDatum) <br>\n";
		if($this->order<>'')
			$order=$this->order;
		else	
	    $order="WaardeEuro desc";
	
    $totaalWaarde = $this->pdf->fondsPortefeuille[$fonds]['totaalWaarde'];
		//$totaalWaarde = $this->pdf->fondsPortefeuille[$fonds]['totaalFondsen'];
	  $query="SELECT TijdelijkeRapportage.Fonds, 
	                 TijdelijkeRapportage.FondsOmschrijving, 
                   TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro AS WaardeEuro,
                   TijdelijkeRapportage.historischeWaarde,
TijdelijkeRapportage.beginwaardeLopendeJaar,
TijdelijkeRapportage.historischeValutakoers,
TijdelijkeRapportage.beginwaardeValutaLopendeJaar,
TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro,
TijdelijkeRapportage.totaalAantal,
TijdelijkeRapportage.actueleFonds,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.fonds,
TijdelijkeRapportage.fondsEenheid,
TijdelijkeRapportage.actueleValuta,
TijdelijkeRapportage.fondsOmschrijving,
TijdelijkeRapportage.rekening,
TijdelijkeRapportage.beleggingssector,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.hoofdcategorie,
TijdelijkeRapportage.AttributieCategorie,
TijdelijkeRapportage.regio,
TijdelijkeRapportage.beleggingscategorieOmschrijving,
TijdelijkeRapportage.beleggingssectorOmschrijving,
TijdelijkeRapportage.hoofdcategorieOmschrijving,
TijdelijkeRapportage.AttributieCategorieOmschrijving,
TijdelijkeRapportage.regioOmschrijving,
TijdelijkeRapportage.beginPortefeuilleWaardeEuro,
TijdelijkeRapportage.beginPortefeuilleWaardeInValuta,
TijdelijkeRapportage.hoofdcategorieVolgorde,
TijdelijkeRapportage.AttributieCategorieVolgorde,
TijdelijkeRapportage.beleggingscategorieVolgorde,
TijdelijkeRapportage.koersDatum,
TijdelijkeRapportage.lossingsdatum,
actuelePortefeuilleWaardeInValuta,
TijdelijkeRapportage.type,
round((UNIX_TIMESTAMP(TijdelijkeRapportage.rapportageDatum) - UNIX_TIMESTAMP(TijdelijkeRapportage.koersDatum))/86400) as koersLeeftijd
            FROM TijdelijkeRapportage
            WHERE TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."' AND 
                  TijdelijkeRapportage.Portefeuille = '".$portefeuille."' AND 
                  TijdelijkeRapportage.Fonds <> '' ".$__appvar['TijdelijkeRapportageMaakUniek']."
                  ORDER BY $order";
	  
	  $DB = new DB();
	  $DB->SQL($query);
	  $DB->Query();
	  while($fon = $DB->nextRecord())
	  {
			$kostprijs=0;
			foreach($this->meetPunten[$fonds] as $datum=>$aandeelData)
			{
				$koers= $this->getFondsKoers($fon['fonds'], $datum,true);
				if($koers==0)
				{
					echo "Geen koers voor ".$fon['fonds']." gevonden.";
					exit;
				}
				$kostprijs+=$koers*$aandeelData['aandeel'];
				//echo  "$fonds $datum $kostprijs+=$koers*".$aandeelData['aandeel']."<br>\n";
			}
			if($kostprijs<>0)
  			$fon['historischeWaarde']=$kostprijs;//

      if($fon['type']=='fondsen')
				$type='fonds';
			else
				$type=$fon['type'];
//echo $rapportageDatum;listarray($fon);
  	  $data[$type][$fon['Fonds']]['waardeEur']=$fon['WaardeEuro'];
	    $data[$type][$fon['Fonds']]['Omschrijving']=$fon['FondsOmschrijving'];
			$data[$type][$fon['Fonds']]['overige']=$fon;
	  }

	foreach ($data['fonds'] as $fondsNaam=>$waarden)
	{
	  $data['fonds'][$fondsNaam]['percentage']=$waarden['waardeEur']/$totaalWaarde;
	}

	$this->pdf->fondsPortefeuille[$fonds]['verdeling']['fondsen']=$data['fonds'];
	if(isset($data['rente']))
		$this->pdf->fondsPortefeuille[$fonds]['verdeling']['rente']=$data['rente'];

	$data['liquiditeiten']['liquiditeiten']['waardeEur']=$this->pdf->fondsPortefeuille[$fonds]['totaalLiquiditeiten'];
  $data['liquiditeiten']['liquiditeiten']['Omschrijving']='Liquiditeiten en reserveringen';
	$data['liquiditeiten']['liquiditeiten']['percentage']=$this->pdf->fondsPortefeuille[$fonds]['totaalLiquiditeiten']/$totaalWaarde;

		$query="SELECT TijdelijkeRapportage.Fonds, 
	                 TijdelijkeRapportage.FondsOmschrijving, 
                   TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro AS WaardeEuro,
                   TijdelijkeRapportage.historischeWaarde,
TijdelijkeRapportage.beginwaardeLopendeJaar,
TijdelijkeRapportage.historischeValutakoers,
TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro,
TijdelijkeRapportage.totaalAantal,
TijdelijkeRapportage.actueleFonds,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.fonds,
TijdelijkeRapportage.fondsEenheid,
TijdelijkeRapportage.actueleValuta,
TijdelijkeRapportage.fondsOmschrijving,
TijdelijkeRapportage.rekening,
TijdelijkeRapportage.beleggingssector,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.hoofdcategorie,
TijdelijkeRapportage.AttributieCategorie,
TijdelijkeRapportage.beleggingscategorieOmschrijving,
TijdelijkeRapportage.beleggingssectorOmschrijving,
TijdelijkeRapportage.hoofdcategorieOmschrijving,
TijdelijkeRapportage.AttributieCategorieOmschrijving,
TijdelijkeRapportage.beginPortefeuilleWaardeEuro,
TijdelijkeRapportage.beginPortefeuilleWaardeInValuta,
TijdelijkeRapportage.hoofdcategorieVolgorde,
TijdelijkeRapportage.beleggingscategorieVolgorde,
TijdelijkeRapportage.AttributieCategorieVolgorde,
actuelePortefeuilleWaardeInValuta,
TijdelijkeRapportage.type
            FROM TijdelijkeRapportage
            WHERE TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."' AND 
                  TijdelijkeRapportage.Portefeuille = '".$portefeuille."' AND 
                  TijdelijkeRapportage.type = 'rekening' ".$__appvar['TijdelijkeRapportageMaakUniek']."
                  ORDER BY $order";
		$DB->SQL($query);
		$DB->Query();
		while($fon = $DB->nextRecord())
		{
			$data['liquiditeiten'][$fon['rekening']]['overige']=$fon;
		}

	$this->pdf->fondsPortefeuille[$fonds]['verdeling']['liquiditeiten']=$data['liquiditeiten'];



	}

	function getFondsKoers($fonds,$datum,$any=false)
	{
		$db=new DB();
		$query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc";
		$koers=$db->lookupRecordByQuery($query);
		if($koers['Koers']==0 && $any==true)
		{
			$query = "SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' order by Datum asc";
			$koers = $db->lookupRecordByQuery($query);
		}
		return $koers['Koers'];
	}
	
}

?>
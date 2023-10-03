<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2013/03/06 16:58:51 $
File Versie					: $Revision: 1.1 $

$Log: MutatievoorstelOpties.php,v $
Revision 1.1  2013/03/06 16:58:51  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");

class MutatievoorstelOpties
{
	function MutatievoorstelOpties($selectieData)
	{
		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;
    $this->pdf->marge = 10;

		$this->pdf->rapport_type = "mutatievoorstel"; 
    $this->pdf->rapport_titel = "Mutatievoorstel opties";
		$this->pdf->selectieData  = $selectieData;
		$this->pdf->rapport_datum = $selectieData['datumTm'];
		$this->rapportageDatum    = date('Y-m-d',$selectieData['datumTm']);
		//fondsOptie
		if(in_array($this->pdf->selectieData['transactiesoort'],array('openVerkoop','sluitenVerkoop')))
      $this->pdf->selectieData['percentage']=$this->pdf->selectieData['percentage']*-1;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		global $__appvar,$USR;
    if($this->pdf->selectieData['modelportefeuille'] == "Allemaal" && $this->pdf->selectieData['type'] == "Model")
		{
			$DB = new DB();
			$query = "SELECT DISTINCT(Modelportefeuille) AS Portefeuille FROM Portefeuilles WHERE Portefeuilles.Modelportefeuille <> '' ORDER BY Modelportefeuille ";
			$DB->SQL($query);
			$DB->Query();
			while($gb = $DB->NextRecord())
				$modelloop[] = $gb['Portefeuille'];
		}
		else
		{
			$modelloop[] = $this->pdf->selectieData['modelportefeuille'];
		}


		for($a=0; $a < count($modelloop); $a++)
		{
		  $this->pdf->selectieData['modelportefeuille'] = $modelloop[$a];

			if($this->pdf->selectieData['type'] == "Model")
			{
				// haal percentage van Fonds uit model!
				$this->pdf->selectieData['percentage'] = round($this->berekenModelPercentage($this->pdf->selectieData['modelportefeuille'], $this->pdf->selectieData['optie'], $this->rapportageDatum),2);
			}
			$DB2 = new DB();
			if ($this->pdf->selectieData['transactieType']=='switch')
			{
			  $this->pdf->selectieData['verkoopFonds']=$this->pdf->selectieData['optie'];
			  $query="SELECT * FROM Fondsen WHERE Fonds='".$this->pdf->selectieData['verkoopFonds']."'";
			  $DB2->SQL($query);
			  $DB2->Query();
			  $verkoopFonds = $DB2->nextRecord();
			  $this->pdf->selectieData['fondsOmschrijving'] = $this->pdf->selectieData['optie'];
			  $this->pdf->selectieData['berekeningswijzeOpties']='switch';
			  if($this->pdf->selectieData['newFonds']!='')
			  {
			    $this->pdf->selectieData['optie'] = $this->pdf->selectieData['newFonds'];
			    $this->pdf->selectieData['fondsOmschrijving'] = $this->pdf->selectieData['newFonds'];
			  }
			  else
			  {
			    $this->pdf->selectieData['optie'] = $this->pdf->selectieData['aankoopFonds'];
			    $this->pdf->selectieData['fondsOmschrijving'] = $this->pdf->selectieData['aankoopFonds'];
			  }
			}
					// selecteer koers van fonds op datum uit fonds tabel.
			$query = "SELECT Valutakoersen.Koers FROM Valutakoersen, Fondsen WHERE ".
							 " Fondsen.Fonds  			= '".$this->pdf->selectieData['optie']."' AND ".
							 " Valutakoersen.Valuta = Fondsen.Valuta AND ".
							 " Valutakoersen.Datum <= '".$this->rapportageDatum."' ORDER BY Valutakoersen.Datum DESC LIMIT 1 ";
			$DB2->SQL($query);
			$DB2->Query();
			$kdata 	= $DB2->nextRecord();
			$valutakoers = $kdata['Koers'];


     	if ($this->pdf->selectieData['newFondsValutaKoers'] && $this->pdf->selectieData['percentage'] <> 0.0)
			  $valutakoers = $this->pdf->selectieData['newFondsValutaKoers'];

			// selecteer koers van fonds op datum uit fonds tabel.
			$query = "SELECT Fondskoersen.Koers , Fondsen.Fondseenheid, Fondsen.ISINCode, Fondsen.Omschrijving, Fondsen.OptieUitoefenPrijs FROM Fondskoersen, Fondsen WHERE ".
							 " Fondskoersen.Fonds = Fondsen.Fonds AND ".
							 " Fondskoersen.Fonds = '".$this->pdf->selectieData['optie']."' AND ".
							 " Fondskoersen.Datum <='".$this->rapportageDatum."' ORDER BY Datum desc limit 1";
			$DB2->SQL($query);
			$DB2->Query();
			$koersWaarde 	= $DB2->nextRecord();

			$query = "SELECT Fondskoersen.Koers , Fondsen.Fondseenheid, Fondsen.ISINCode, Fondsen.Omschrijving FROM Fondskoersen, Fondsen WHERE ".
							 " Fondskoersen.Fonds = Fondsen.Fonds AND ".
							 " Fondskoersen.Fonds = '".$this->pdf->selectieData['fondsOptie']."' AND ".  ///   verkoopFonds  fondsOptie
							 " Fondskoersen.Datum <='".$this->rapportageDatum."' ORDER BY Datum desc limit 1";
			$DB2->SQL($query);
			$DB2->Query();
			$koersWaardeVerkoop 	= $DB2->nextRecord();

			$this->pdf->selectieData['fondsOmschrijving'] = $koersWaarde['Omschrijving'];
			$fondseenheid = $koersWaarde['Fondseenheid'];
			$fondsisin = $koersWaarde['ISINCode'];
			$OptieUitoefenPrijs 	= $koersWaarde['OptieUitoefenPrijs'];
			$koersWaarde 	= $koersWaarde['Koers'];

			$this->pdf->selectieData['koersWaarde'] = $koersWaardeVerkoop['Koers']." / ".$koersWaarde;

			if ($this->pdf->selectieData['newFondsValutaCode'])
			  $fondsValutaCode = ' '.$this->pdf->selectieData['newFondsValutaCode'];

			if ($this->pdf->selectieData['newFondsKoers'] && $this->pdf->selectieData['percentage'] <> 0.0)
			{
			  $this->selectData['koersWaarde'] = $this->pdf->selectieData['newFondsKoers'];
			  $koersWaarde = $this->pdf->selectieData['newFondsKoers'];
			}


			if ($this->pdf->selectieData['newFonds'])
			{
			  $this->pdf->selectieData['optie'] = $this->pdf->selectieData['newFonds'];
			  $this->pdf->selectieData['fondsOmschrijving'] = $this->pdf->selectieData['newFonds'];
			}
			if ($this->pdf->selectieData['newFondsEenheid'])
			  $fondseenheid = $this->pdf->selectieData['newFondsEenheid'];

			if ($this->pdf->selectieData['newFondsISIN'])
			  $fondsisin = $this->pdf->selectieData['newFondsISIN'];

		//	if ($this->pdf->selectieData['newFondsValutaKoers'] && $this->pdf->selectieData['percentage'] <> 0.0)
		//	  $valutakoers = $this->pdf->selectieData['newFondsValutaKoers'];

			if($this->pdf->selectieData['type'] == "Model")
				$extraquery .= " AND Portefeuilles.ModelPortefeuille = '".$this->pdf->selectieData['modelportefeuille']."' ";

      //$portefeuilles=getPortefeuilles($this->pdf->selectieData);
   	  $selectie = new portefeuilleSelectie($this->pdf->selectieData,$this->orderby);
      $records = $selectie->getRecords();
      $portefeuilles = $selectie->getSelectie();

      foreach ($portefeuilles as $ddata)
      {
        if($ddata['Depotbank'] <> '')
          $depotbanken[$ddata['Depotbank']]=$ddata['Depotbank'];
      }
      if(count($depotbanken) < 1)
      {
        echo "Geen portefeuilles met een depotbank gevonden.";exit;
      }

							// Maak header voor CSV bestand
			$this->pdf->excelData[] = array("Inleesdatum",
												"Portefeuille",
												"Accountmanager",
												"Risicoprofiel",
												"Soort overeenkomst",
												"Depotbank",
												"Naam",
												"Totale waarde portefeuille",
												"Totale waarde geselecteerd",
												"Totale waarde liquide middelen",
												"Voldoet aan zorgplicht",
												"Aan te kopen waarde",
												"Aan te kopen aantal",
												"Waarde liq. middelen indien actief","Fonds1 ISIN","Fonds2 ISIN",'Restricties');

			//while($ddata = $DBloop->nextRecord())
			foreach ($depotbanken as $ddata['Depotbank'])
			{
				// set 0 waarden
				$totaalAantal = 0;
				$this->pdf->selectieData['depotbank'] = $ddata['Depotbank'];
		//		$portefeuilles=getPortefeuilles($this->pdf->selectieData);
    $selectie = new portefeuilleSelectie($this->pdf->selectieData,$this->orderby); 
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();
        $this->pdf->addPage("L");

	    	if($this->progressbar)
		    {
			    $this->progressbar->moveStep(0);
			    $pro_step = 0;
			    $pro_multiplier = 100 / count($portefeuilles);
		    }

		    foreach ($portefeuilles as $portefeuille=>$data)
	    	{
         	$portefeuilleData = berekenPortefeuilleWaarde($portefeuille,$this->rapportageDatum);
          vulTijdelijkeTabel($portefeuilleData,$portefeuille,$this->rapportageDatum);
      
		      if($this->progressbar)
		    	{
			    	$pro_step += $pro_multiplier;
				    $this->progressbar->moveStep($pro_step);
			    }

		    	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE rapportageDatum ='".$this->rapportageDatum."' AND portefeuille = '".$data['Portefeuille']."' ".$__appvar['TijdelijkeRapportageMaakUniek']."";
					$DB2->SQL($query);
					$DB2->Query();
					$totaalWaarde = $DB2->nextRecord();
					$totaalWaarde = $totaalWaarde['totaal'];
					//echo $query." ".$totaalWaarde;exit;


				  if($this->pdf->selectieData['transactiesoort']=='openVerkoopPutMax')
				  {
				     $fondsenSelectie=" AND Fonds IN('".$this->pdf->selectieData['fondsOptie']."') ";
             $query = "SELECT Koers FROM Fondskoersen WHERE   Datum ='".$this->rapportageDatum."' $fondsenSelectie ";
					   $DB2->SQL($query);
				     $DB2->Query();
				     $fondsWaarde = $DB2->nextRecord();
				     $fondsKoers = $fondsWaarde['Koers'];

				  }
				  else
				    $fondsenSelectie=" AND Fonds = '".$this->pdf->selectieData['optie']."' ";

					$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE type = 'FONDS' $fondsenSelectie AND rapportageDatum ='".$this->rapportageDatum."' AND  portefeuille = '".$data['Portefeuille']."' ".$__appvar['TijdelijkeRapportageMaakUniek']."";
					$DB2->SQL($query);
					$DB2->Query();
					$fondsWaarde = $DB2->nextRecord();
					$fondsWaarde = $fondsWaarde['totaal'];

					if($this->pdf->selectieData['transactiesoort']=='openVerkoopPutMax')
					{
					  $query="SELECT TijdelijkeRapportage.totaalAantal as aantal, TijdelijkeRapportage.fondsEenheid FROM TijdelijkeRapportage WHERE  Fonds = '".$this->pdf->selectieData['optie']."' AND rapportageDatum ='".$this->rapportageDatum."' AND  portefeuille = '".$data['Portefeuille']."' ".$__appvar['TijdelijkeRapportageMaakUniek']."";
  					$DB2->SQL($query);
	  				$DB2->Query();
		  			$optieAantal = $DB2->nextRecord();
            $fondsWaarde+=$fondsKoers*$optieAantal['fondsEenheid']*$optieAantal['aantal']*-1;
					}


					$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE type IN('rekening') AND rapportageDatum ='".$this->rapportageDatum."' AND  portefeuille = '".$data['Portefeuille']."' ".$__appvar['TijdelijkeRapportageMaakUniek']."";
					$DB2->SQL($query);
					$DB2->Query();
					$liqWaarde = $DB2->nextRecord();
					$liqWaarde = $liqWaarde['totaal'];

		      if($this->pdf->selectieData['categorie'] <> '')
		        $categorieQuery=" '".$this->pdf->selectieData['categorie']."'";
		      else
		        $categorieQuery=" (SELECT beleggingscategorie FROM BeleggingssectorPerFonds WHERE Fonds = '".$this->pdf->selectieData['optie']."') ";

					$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE beleggingscategorie = $categorieQuery AND
					          type = 'fondsen' AND rapportageDatum ='".$this->rapportageDatum."' AND  portefeuille = '".$data['Portefeuille']."' ".$__appvar['TijdelijkeRapportageMaakUniek']."";
					$DB2->SQL($query);
					$DB2->Query();
					$categorieWaarde = $DB2->nextRecord();
					$categorieWaarde = $categorieWaarde['totaal'];


					$query = "SELECT actuelePortefeuilleWaardeEuro AS totaal, totaalAantal as aantal FROM TijdelijkeRapportage WHERE Fonds = '".$this->pdf->selectieData['fondsOptie']."' AND type = 'fondsen' AND rapportageDatum ='".$this->rapportageDatum."' AND  portefeuille = '".$data['Portefeuille']."' ".$__appvar['TijdelijkeRapportageMaakUniek']."";
					$DB2->SQL($query);
					$DB2->Query();
					$bovenliggendFonds = $DB2->nextRecord();
					$bovenliggendFondsWaarde = $bovenliggendFonds['totaal'];

					$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal, sum(totaalAantal) as aantal,max(actueleFonds) as actueleFonds FROM TijdelijkeRapportage WHERE type = 'FONDS' AND rapportageDatum ='".$this->rapportageDatum."' AND portefeuille = '".$data['Portefeuille']."' AND Fonds='".$this->pdf->selectieData['verkoopFonds']."' ".$__appvar['TijdelijkeRapportageMaakUniek']."";
					$DB2->SQL($query);
					$DB2->Query();
					$verkoopFondsWaarde = $DB2->nextRecord();
					$verkoopKoers= $verkoopFondsWaarde['actueleFonds'];
					$verkoopStuks = $verkoopFondsWaarde['aantal']*-1;
					$verkoopFondsWaarde = $verkoopFondsWaarde['totaal'];

					switch($this->pdf->selectieData['berekeningswijzeOpties'])
					{
						case "Totaal vermogen" :
							$totaalRekenwaarde = $totaalWaarde;
						break;
						case "Totaal belegd vermogen" :
							$totaalRekenwaarde = $totaalWaarde - $liqWaarde;
						break;
						case "Belegd vermogen per beleggingscategorie" :
							$totaalRekenwaarde = $categorieWaarde;
						break;
						case "switch" :
						  $totaalRekenwaarde=$verkoopFondsWaarde;
						break;
						case "Bovenliggend fonds" :
						  $totaalRekenwaarde=$bovenliggendFondsWaarde;
						break;

				  }

				  $norm['norm']=1;

				  if($this->pdf->selectieData['norm'] == 1)
				  {
            $query="SELECT
            ZorgplichtPerRisicoprofiel.Zorgplicht,
            Portefeuilles.Portefeuille,
            ZorgplichtPerRisicoprofiel.norm/100 as norm,
            BeleggingssectorPerFonds.Fonds
            FROM
            Portefeuilles
            Inner Join ZorgplichtPerRisicoprofiel ON Portefeuilles.Risicoprofiel = ZorgplichtPerRisicoprofiel.Risicoprofiel
            Inner Join ZorgplichtPerBeleggingscategorie ON ZorgplichtPerRisicoprofiel.Zorgplicht = ZorgplichtPerBeleggingscategorie.Zorgplicht
            Inner Join BeleggingssectorPerFonds ON ZorgplichtPerBeleggingscategorie.Beleggingscategorie = BeleggingssectorPerFonds.Beleggingscategorie
            WHERE Portefeuilles.Portefeuille='".$data['Portefeuille']."' AND BeleggingssectorPerFonds.Fonds='".$this->pdf->selectieData['fondsOptie']."'";
            $DB2->SQL($query);
            $DB2->Query();
            $norm = $DB2->nextRecord();
          }
          $query="SELECT norm/100 as norm FROM ZorgplichtPerPortefeuille WHERE Portefeuille='".$norm['Portefeuille']."' AND Zorgplicht='".$norm['Zorgplicht']."'";
          $DB2->SQL($query);
          $DB2->Query();
          $normPortefeuille = $DB2->nextRecord();
          if($normPortefeuille['norm'] <> '')
            $norm['norm']=$normPortefeuille['norm'];



				  if($this->pdf->selectieData['berekeningswijzeOpties'] == 'Bovenliggend fonds')
					{

					 // listarray($bovenliggendFonds);

	          $aankoopWaarde=$verkoopFondsWaarde*$norm['norm'];
	          $aankoopStuks 	= round(($aankoopWaarde / ($koersWaarde * $valutakoers)) /$fondseenheid);
	          if($this->pdf->selectieData['afronding'] <>1)
			  			$aankoopStuks  = round(($aankoopStuks / $this->pdf->selectieData['afronding']),0)  * $this->pdf->selectieData['afronding'];


			  		$query="UPDATE TijdelijkeRapportage SET TijdelijkeRapportage.Fonds='".$this->pdf->selectieData['optie']."', TijdelijkeRapportage.beleggingscategorie=(SELECT beleggingscategorie FROM BeleggingssectorPerFonds WHERE Fonds='".$this->pdf->selectieData['optie']."' limit 1)
					            WHERE TijdelijkeRapportage.type = 'fondsen' AND ".
						  			 " TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatum."' AND ".
						 	 	  	 " TijdelijkeRapportage.portefeuille = '".$data['Portefeuille']."'  AND ".
							  		 " TijdelijkeRapportage.Fonds = '".$this->pdf->selectieData['verkoopFonds']."' ".$__appvar['TijdelijkeRapportageMaakUniek']."";
						$DB2->SQL($query);
				  	$DB2->Query();

				  	$zorgplicht = new Zorgplichtcontrole();
				  	$zpwaarde=$zorgplicht->zorgplichtMeting($data,$this->rapportageDatum);

				  	if($aankoopStuks <> 0 && $verkoopStuks <> 0)
				  	{
						  $this->orderData[] = array($this->pdf->selectieData['optie'],$fondsisin,$data['Portefeuille'],round($aankoopStuks,2),round($koersWaarde,2),$data['Naam'],$this->pdf->selectieData['fondsOmschrijving'],$ddata['Depotbank'],$zpwaarde['voldoet']);
						  $this->orderData[] = array($verkoopFonds['optie'],$verkoopFonds['ISINCode'],$data['Portefeuille'],round($verkoopStuks,2),round($verkoopKoers,2),$data['Naam'],$verkoopFonds['Omschrijving'],$ddata['Depotbank'],$zpwaarde['voldoet']);
				  	}
					}
   				if($this->pdf->selectieData['berekeningswijzeOpties'] == 'switch')
					{
	          $aankoopWaarde=$verkoopFondsWaarde*$norm['norm'];
	          $aankoopStuks 	= round(($aankoopWaarde / ($koersWaarde * $valutakoers)) /$fondseenheid);
	          if($this->pdf->selectieData['afronding'] <>1)
			  			$aankoopStuks  = round(($aankoopStuks / $this->pdf->selectieData['afronding']),0)  * $this->pdf->selectieData['afronding'];
			  		$query="UPDATE TijdelijkeRapportage SET TijdelijkeRapportage.Fonds='".$this->pdf->selectieData['optie']."', TijdelijkeRapportage.beleggingscategorie=(SELECT beleggingscategorie FROM BeleggingssectorPerFonds WHERE Fonds='".$this->pdf->selectieData['optie']."' limit 1)
					            WHERE TijdelijkeRapportage.type = 'fondsen' AND ".
						  			 " TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatum."' AND ".
						 	 	  	 " TijdelijkeRapportage.portefeuille = '".$data['Portefeuille']."'  AND ".
							  		 " TijdelijkeRapportage.Fonds = '".$this->pdf->selectieData['verkoopFonds']."' ".$__appvar['TijdelijkeRapportageMaakUniek']."";
						$DB2->SQL($query);
				  	$DB2->Query();

				  	$zorgplicht = new Zorgplichtcontrole();
				  	$zpwaarde=$zorgplicht->zorgplichtMeting($data,$this->rapportageDatum);

				  	if($aankoopStuks <> 0 && $verkoopStuks <> 0)
				  	{
						  $this->orderData[] = array($this->pdf->selectieData['optie'],$fondsisin,$data['Portefeuille'],round($aankoopStuks,2),round($koersWaarde,2),$data['Naam'],$this->pdf->selectieData['fondsOmschrijving'],$ddata['Depotbank'],$zpwaarde['voldoet']);
						  $this->orderData[] = array($verkoopFonds['optie'],$verkoopFonds['ISINCode'],$data['Portefeuille'],round($verkoopStuks,2),round($verkoopKoers,2),$data['Naam'],$verkoopFonds['Omschrijving'],$ddata['Depotbank'],$zpwaarde['voldoet']);
				  	}
					}
					else
					{
					  //echo 			$OptieUitoefenPrijs;exit;
  					if($this->pdf->selectieData['transactiesoort']=='openVerkoopPutMax')
  					  $aankoopKoers=$OptieUitoefenPrijs;//$fondsKoers;
  					else
						  $aankoopKoers=$koersWaarde;

						$aankoopWaarde 	= round(((($totaalRekenwaarde*$norm['norm']) / 100) * $this->pdf->selectieData['percentage']) - $fondsWaarde,2);

  					$aankoopStuks 	= round(($aankoopWaarde / ($aankoopKoers * $valutakoers)) /$fondseenheid);
    				if($this->pdf->selectieData['afronding'] <>1)
			  			$aankoopStuks  = round(($aankoopStuks / $this->pdf->selectieData['afronding']),0)  * $this->pdf->selectieData['afronding'];

			  		if($this->pdf->selectieData['transactiesoort']=='openVerkoopPutMax')
			  		  $aankoopStuks=$aankoopStuks*-1;
            // voer fictieve transactie uit op de tijdelijke rapportage ivm zorgplicht berekening.
				  	$query="SELECT id FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.type = 'FONDS' AND ".
									 " TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatum."' AND ".
									 " TijdelijkeRapportage.portefeuille = '".$data['Portefeuille']."'  AND ".
									 " TijdelijkeRapportage.fonds = '".$this->pdf->selectieData['optie']."' ".$__appvar['TijdelijkeRapportageMaakUniek']."";
				  	$DB2->SQL($query);
				  	$DB2->Query();
            if($DB2->records() > 0)
              $query="UPDATE TijdelijkeRapportage SET actuelePortefeuilleWaardeEuro=actuelePortefeuilleWaardeEuro+$aankoopWaarde
					            WHERE TijdelijkeRapportage.type = 'fondsen' AND ".
						  			 " TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatum."' AND ".
						 	 	  	 " TijdelijkeRapportage.portefeuille = '".$data['Portefeuille']."'  AND ".
							  		 " TijdelijkeRapportage.Fonds = '".$this->pdf->selectieData['optie']."' ".$__appvar['TijdelijkeRapportageMaakUniek']."";
            else
            {
            
              $query="SELECT Fonds,Beleggingscategorie FROM BeleggingscategoriePerFonds WHERE Vermogensbeheerder='".$portefeuille['Vermogensbeheerder']."' AND Fonds = '".addslashes($fonds)."' ";
              $DB2->SQL($query);
    		      $Beleggingscategorie=$DB2->lookupRecord();
    		      $Beleggingscategorie=$Beleggingscategorie['Beleggingscategorie'];

      		    $query="INSERT INTO TijdelijkeRapportage SET actuelePortefeuilleWaardeEuro='$aankoopWaarde', add_user='$USR', TijdelijkeRapportage.sessionId = '".$_SESSION['usersession']['sessionId']."',
                    TijdelijkeRapportage.type = 'fondsen', Beleggingscategorie='$Beleggingscategorie',
                    rapportageDatum ='".$this->rapportageDatum."',
                    portefeuille = '".$pdata['Portefeuille']."',
                    Fonds='".addslashes($this->pdf->selectieData['optie'])."' ";
                    
            }
					  $DB2->SQL($query);
					  $DB2->Query();

					$query = "SELECT actuelePortefeuilleWaardeEuro AS totaal, totaalAantal as aantal FROM TijdelijkeRapportage WHERE type = 'fondsen' AND Fonds = '".$this->pdf->selectieData['optie']."' AND rapportageDatum ='".$this->rapportageDatum."' AND  portefeuille = '".$data['Portefeuille']."' ".$__appvar['TijdelijkeRapportageMaakUniek']."";
					$DB2->SQL($query);
					$DB2->Query();
					$optieAanwezig = $DB2->nextRecord();
					$optieAanwezig = $optieAanwezig['aantal'];

            $query="UPDATE TijdelijkeRapportage SET actuelePortefeuilleWaardeEuro=actuelePortefeuilleWaardeEuro-$aankoopWaarde
					            WHERE TijdelijkeRapportage.type = 'rekening' AND ".
						  			 " TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatum."' AND ".
						  			 " TijdelijkeRapportage.portefeuille = '".$data['Portefeuille']."' ".$__appvar['TijdelijkeRapportageMaakUniek']." LIMIT 1 ";
					  $DB2->SQL($query);
					  $DB2->Query();

					  if(($negatief = ($liqWaarde - (($aankoopStuks*$fondseenheid) * ($koersWaarde * $valutakoers)))) > 0)
				    {
					   	$negatief = "";
					 	  $negatief_csv = "";
					  }
					  else
					  {
					    $negatief_csv = round($negatief,2);
					    $negatief = $this->formatGetal($negatief,2);
					  }

					  $zorgplicht = new Zorgplichtcontrole();
				  	$zpwaarde=$zorgplicht->zorgplichtMeting($data,$this->rapportageDatum);
				  //	listarray($zpwaarde);
				  	if($aankoopStuks <> 0)
					  {
						  $this->orderData[] = array($this->pdf->selectieData['optie'],
									    	$fondsisin,
										   	$data['Portefeuille'],
												round($aankoopStuks,2),
												round($koersWaarde,2),
												$data['Naam'],
												$this->pdf->selectieData['fondsOmschrijving'],
												$ddata['Depotbank'],
												$zpwaarde['voldoet'],
												$negatief_csv
												);
					  }
					}
					if($data['Memo'] <> '')
            $restricties="X";
					else
					  $restricties='';
//echo $data['Portefeuille']." $fondsWaarde $aankoopWaarde <br>\n";
          if($this->pdf->selectieData['transactieType']=='switch')
          {
            $fondsWaarde=$verkoopStuks;
            $waardeAantalRound=0;
          }
          else
            $waardeAantalRound=2;

          if(round($fondsWaarde,2) <> 0 || round($aankoopWaarde,2) <> 0)
          {
					  $pdfdata = array(substr($data['Client'],0,20),
                        $data['Portefeuille'],
                        substr($data['Naam'],0,40),
												$this->formatGetal($totaalRekenwaarde,2),
												$this->formatGetal($fondsWaarde,$waardeAantalRound),
												$this->formatGetal($optieAanwezig,0),
												$this->formatGetal($liqWaarde,2),
												$zpwaarde['voldoet'],
												$this->formatGetal($aankoopWaarde,2),
												$this->formatGetal($aankoopStuks,0),
												$negatief,
												$restricties );
					  $this->pdf->Row($pdfdata);

					$this->pdf->excelData[] = array($data[Portefeuille],
												$data[Accountmanager],
												$data['Risicoprofiel'],
												$data['SoortOvereenkomst'],
												$data[Depotbank],
												$data[Naam],
												round($totaalRekenwaarde,2),
												round($fondsWaarde,$waardeAantalRound),
												round($liqWaarde,2),
												$zpwaarde['voldoet'],
												round($aankoopWaarde,2),
												round($aankoopStuks,2),
												$negatief_csv,
												$fondsisin,
												$verkoopFonds['ISINCode'],
												$data['Memo']);
          }

					$totaalAantal += $aankoopStuks;

				verwijderTijdelijkeTabel($portefeuille,$this->rapportageDatum);
	    	}

			}

				if($this->pdf->GetY() > 140)
					$this->pdf->AddPage('L');

				// druk totaal af.
				$this->pdf->ln();

				$this->pdf->Cell(202, 4 , "", 0, 0, "L");
				$this->pdf->SetFont("Times","b",10);
				$this->pdf->Cell(22 , 4 , "Totaal aantal:" , 0, 0, "R");
				$this->pdf->SetFont("Times","",10);
				$this->pdf->Cell(18 , 4 , $this->formatGetal($totaalAantal,0) , 0, 0, "R");
				$this->pdf->Cell(25 , 4 , "" , 0, 1, "R");

				$this->pdf->ln();

				$this->pdf->Cell(202 , 4 , "" , 0, 0, "L");
				$this->pdf->Cell(22 , 4 , "Koers:" , 0, 0, "R");
				$this->pdf->Cell(18 , 4 , $this->formatGetal($koersWaarde,2) , 0, 0, "R");
				$this->pdf->Cell(25 , 4 , "" , 0, 1, "R");

				$this->pdf->Cell(202 , 4 , "" , 0, 0, "L");
				$this->pdf->Cell(22 , 4 , "Valutakoers$fondsValutaCode:" , 0, 0, "R");
				$this->pdf->Cell(18 , 4 , $this->formatGetal($valutakoers,2) , 0, 0, "R");
				$this->pdf->Cell(25 , 4 , "" , 0, 1, "R");

				$this->pdf->Cell(202 , 4 , "" , 0, 0, "L");
				$this->pdf->Cell(22 , 4 , "Factor:" , 0, 0, "R");
				$this->pdf->Cell(18 , 4 , $this->formatGetal($fondseenheid,2) , 0, 0, "R");
				$this->pdf->Cell(25 , 4 , "" , 0, 1, "R");

				$this->pdf->ln();
				$this->pdf->Cell(202 , 4 , "" , 0, 0, "L");
				$this->pdf->SetFont("Times","b",10);
				$this->pdf->Cell(22 , 4 , "Totale in te kopen waarde:" , 0, 0, "R");
				$this->pdf->SetFont("Times","",10);
				$this->pdf->Cell(18 , 4 , $this->formatGetal(($totaalAantal * ($koersWaarde * $fondseenheid) * $valutakoers),0) , 0, 0, "R");
				$this->pdf->Cell(25 , 4 , "" , 0, 1, "R");

			$totaalAantal = 0;
			$koersWaarde = 0;
			$valutakoers = 0;
			$fondseenheid = 0;

		}
		if($this->progressbar)
			$this->progressbar->hide();
	}



	function berekenModelPercentage($model, $fonds, $datum)
	{
		global $__appvar;
    vulTijdelijkeTabel($model,$datum,'m');
		// selecteer totaalwaarde portefeuille
		$DB = new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE rapportageDatum ='".$datum."' AND  portefeuille = 'm".$model."' ".$__appvar['TijdelijkeRapportageMaakUniek']."";
		$DB->SQL($query);
		$DB->Query();
		$tdata = $DB->nextRecord();
		$totaalWaarde = $tdata['totaal'];


		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE type IN('LIQ','FWD') AND  rapportageDatum ='".$datum."' AND  portefeuille = 'm".$model."' ".$__appvar['TijdelijkeRapportageMaakUniek']."";
		$DB->SQL($query);
		$DB->Query();
		$liqWaarde = $DB->nextRecord();
		$liqWaarde = $liqWaarde['totaal'];



		// selecteer belegingscategorie
		$query  = " SELECT TijdelijkeRapportage.Beleggingscategorie FROM TijdelijkeRapportage WHERE Portefeuille = 'm".$model."' AND Fonds = '".$fonds."' AND rapportageDatum ='".$datum."' ".$__appvar['TijdelijkeRapportageMaakUniek']."";
		$DB->SQL($query);
		$DB->Query();
		$categorie = $DB->nextRecord();
		$categorie = $categorie['Beleggingscategorie'];

		// selecteer beleggingscategorie waarde
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " TijdelijkeRapportage.beleggingscategorie  = '".$categorie."' AND ".
						 " TijdelijkeRapportage.type = 'fonds' AND ".
						 " TijdelijkeRapportage.rapportageDatum ='".$datum."' AND ".
						 " TijdelijkeRapportage.portefeuille = 'm".$model."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];

		$DB2 = new DB();
		$DB2->SQL($query);
		$DB2->Query();
		$categorieWaarde = $DB2->nextRecord();
		$categorieWaarde = $categorieWaarde[totaal];

		// selecteer fondswaarde portefeuille
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " type = 'fonds' AND ".
						 " Fonds = '".$fonds."' AND ".
						 " rapportageDatum ='".$datum."' AND ".
						 " portefeuille = 'm".$model."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);

		$DB2 = new DB();
		$DB2->SQL($query);
		$DB2->Query();
		$fondsWaarde = $DB2->nextRecord();
		$fondsWaarde = $fondsWaarde[totaal];

		// selecteer waarde van fonds in portefeuille.

		switch($this->pdf->selectieData['berekeningswijzeOpties'])
		{
			case "Totaal vermogen" :
				$totaalRekenwaarde = $totaalWaarde;
			break;
			case "Totaal belegd vermogen" :
				$totaalRekenwaarde = $totaalWaarde - $liqWaarde;
			break;
			case "Belegd vermogen per beleggingscategorie" :
				$totaalRekenwaarde = $categorieWaarde;
			break;
		}

		$percentage = $fondsWaarde / (($totaalRekenwaarde) / 100);
		verwijderTijdelijkeTabel('m'.$model,$datum);
		return $percentage;
	}

	function OutputOrder()
	{
    $db=new DB();
		$t=0;
		for($t=0; $t < count($this->orderData); $t++)
		{
			$insert = "INSERT INTO OrderRegels SET ".
			            " Status = '1',".
									" Fonds = '".mysql_escape_string($this->orderData[$t][0])."', ".
									" FondsOmschrijving = '".mysql_escape_string($this->orderData[$t][6])."', ".
									" ISINCode = '".$this->orderData[$t][1]."', ".
									" Portefeuille = '".$this->orderData[$t][2]."', ".
									" ClientNaam = '".mysql_escape_string($this->orderData[$t][5])."', ".
									" Aantal = '".$this->orderData[$t][3]."', ".
									" Depotbank = '".$this->orderData[$t][7]."', ".
									" zorgplicht = '".$this->orderData[$t][8]."', ".
									" liqWaarde = '".$this->orderData[$t][9]."', ".
									" change_date = NOW(), ".
									" change_user = '".$this->USR."', ".
									" add_date = NOW(), ".
									" add_user = '".$this->USR."' ";

				$db->SQL($insert);
				$db->Query();

		}
		if($t>0)
			return true;
		else
			return false;
	}

}
?>
<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2013/03/06 16:58:51 $
File Versie					: $Revision: 1.1 $

$Log: MutatievoorstelOptiesAantal.php,v $
Revision 1.1  2013/03/06 16:58:51  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");

class MutatievoorstelOptiesAantal
{
	function MutatievoorstelOptiesAantal($selectieData)
	{
 		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;
    $this->pdf->marge = 10;
		$this->pdf->rapport_type = "mutatievoorstelOptiesAantal";
    $this->pdf->rapport_titel = "Mutatievoorstel opties";
		$this->pdf->selectieData  = $selectieData;
		$this->pdf->rapport_datum = $selectieData['datumTm'];
		$this->rapportageDatum    = date('Y-m-d',$selectieData['datumTm']);
	}


	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


	function writeRapport()
	{
		global $__appvar,$USR;
	  $this->pdf->selectieData['modelportefeuille'] = $modelloop[$a];

			$DB2 = new DB();

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
			$query = "SELECT Fondskoersen.Koers , Fondsen.Fondseenheid, Fondsen.ISINCode, Fondsen.Omschrijving FROM Fondskoersen, Fondsen WHERE ".
							 " Fondskoersen.Fonds = Fondsen.Fonds AND ".
							 " Fondskoersen.Fonds = '".$this->pdf->selectieData['optie']."' AND ".
							 " Fondskoersen.Datum <='".$this->rapportageDatum."' ORDER BY Datum desc limit 1";
			$DB2->SQL($query);
			$DB2->Query();
			$koersWaarde 	= $DB2->nextRecord();
			$optieGegevens=$koersWaarde;

			$this->pdf->selectieData['fondsOmschrijving'] = $koersWaarde['Omschrijving'];
			$fondseenheid = $koersWaarde['Fondseenheid'];
			$fondsisin = $koersWaarde['ISINCode'];
			$koersWaarde 	= $koersWaarde['Koers'];
			$this->pdf->selectieData['koersWaarde'] = $koersWaardeVerkoop['Koers']." / ".$koersWaarde;

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
				//$portefeuilles=getPortefeuilles($this->pdf->selectieData);
        
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
					//echo $totaalWaarde;exit;
					$query = "SELECT actuelePortefeuilleWaardeEuro AS totaal, totaalAantal as aantal FROM TijdelijkeRapportage WHERE type = 'fondsen' AND Fonds = '".$this->pdf->selectieData['optie']."' AND rapportageDatum ='".$this->rapportageDatum."' AND  portefeuille = '".$data['Portefeuille']."' ".$__appvar['TijdelijkeRapportageMaakUniek']."";
					$DB2->SQL($query);
					$DB2->Query();
					$fondsWaarde = $DB2->nextRecord();
					$optieAanwezig = $fondsWaarde['aantal'];
					$fondsWaarde = $fondsWaarde['totaal'];

					$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE type IN('rekening') AND rapportageDatum ='".$this->rapportageDatum."' AND  portefeuille = '".$data['Portefeuille']."' ".$__appvar['TijdelijkeRapportageMaakUniek']."";
					$DB2->SQL($query);
					$DB2->Query();
					$liqWaarde = $DB2->nextRecord();
					$liqWaarde = $liqWaarde['totaal'];


					$query = "SELECT actuelePortefeuilleWaardeEuro AS totaal, totaalAantal as aantal FROM TijdelijkeRapportage WHERE Fonds = '".$this->pdf->selectieData['fondsOptie']."' AND type = 'fondsen' AND rapportageDatum ='".$this->rapportageDatum."' AND  portefeuille = '".$data['Portefeuille']."' ".$__appvar['TijdelijkeRapportageMaakUniek']."";
					$DB2->SQL($query);
					$DB2->Query();
					$bovenliggendFonds = $DB2->nextRecord();
					$bovenliggendFondsWaarde = $bovenliggendFonds['totaal'];

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
				  if($this->pdf->selectieData['berekeningswijzeOpties'] = 'Bovenliggend fonds')
					{
   					$fondsAantal 	= (((($bovenliggendFonds['aantal']) / $optieGegevens['Fondseenheid']) * ($this->pdf->selectieData['percentage']/100))) + $optieAanwezig;
   					//echo $data['Portefeuille']." $fondsAantal 	= round((((".$bovenliggendFonds['aantal'].") / ".$optieGegevens['Fondseenheid'].") * (".$this->pdf->selectieData['percentage']."/100))) - $optieAanwezig <br>\n";
  					$optieAantal=intval($fondsAantal);
  					$aankoopWaarde=$optieAantal*$koersWaarde * $valutakoers *$optieGegevens['Fondseenheid'];
  					$aankoopStuks =$optieAantal;
    				if($this->pdf->selectieData['afronding'] <>1)
			  			$aankoopStuks  = round(($aankoopStuks / $this->pdf->selectieData['afronding']),0)  * $this->pdf->selectieData['afronding'];

			  	  if(in_array($this->pdf->selectieData['transactiesoort'],array('openVerkoopCall')))
              $aankoopStuks=$aankoopStuks*-1;


            // voer fictieve transactie uit op de tijdelijke rapportage ivm zorgplicht berekening.
				  	$query="SELECT id FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.type = 'fondsen' AND ".
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


            $waardeAantalRound=2;

          if(round($fondsWaarde,2) <> 0 || round($aankoopWaarde,2) <> 0)
          {
					  $pdfdata = array(jul2form(db2jul($data['rapportageDatum'])),
												$data[Portefeuille],
												substr($data[Naam],0,40),
												$this->formatGetal($bovenliggendFonds['aantal'],0),
												$this->formatGetal($optieAanwezig,0),
												$this->formatGetal($liqWaarde,2),
												$zpwaarde['voldoet'],
												'',
												$this->formatGetal($aankoopStuks,0),
												$negatief,
												$restricties );
					  $this->pdf->Row($pdfdata);

					  $this->pdf->excelData[] = array(jul2form(db2jul($data['rapportageDatum'])),
												$data[Portefeuille],
												$data[Accountmanager],
												$data['Risicoprofiel'],
												$data['SoortOvereenkomst'],
												$data[Depotbank],
												$data[Naam],
												round($bovenliggendFonds['aantal']),
												round($optieAanwezig),
												round($liqWaarde,2),
												$zpwaarde['voldoet'],
												'',
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

		if($this->progressbar)
			$this->progressbar->hide();
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
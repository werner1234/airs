<?
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.2 $

$Log: MutatievoorstelFondsenMeervoudig.php,v $
Revision 1.2  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.1  2013/03/06 16:58:51  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once("rapport/Zorgplichtcontrole.php");

class MutatievoorstelFondsenMeervoudig
{
	function MutatievoorstelFondsenMeervoudig($selectieData)
	{
		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;
    $this->pdf->marge = 10;
		$this->pdf->rapport_type = "MutatievoorstelMeervoudig";
    $this->pdf->rapport_titel = "Mutatievoorstel fondsen";
		$this->selectData  = $selectieData;
		$this->pdf->rapport_datum = $selectieData['datumTm'];
		$this->rapportageDatum    = date('Y-m-d',$selectieData['datumTm']);
	  $this->pdf->excelData[] = array("Client","Portefeuille","Accountmanager","Risicoprofiel","Soort overeenkomst","Modelportefeuille","Depotbank","Risicoprofiel","Fonds","Gewenste Percentage","Werkelijk Percentage","Afwijking","Kopen","Verkopen","Overschrijding waarde EUR","Gewenste waarde","Koers in locale valuta","Fonds ISIN","Restricties");
	}


	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


	function writeRapport()
	{
	  global $USR,$__appvar;
 	  $selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();

    if($this->progressbar)
    {
      $this->progressbar->moveStep(0);
      $pro_step = 0;
      $pro_multiplier = 100 / count($portefeuilles);
    }

    foreach ($this->selectData['selectedFondsen'] as $fondsData)
    {
      $fondsData=explode("|",$fondsData);
      $fondsPercentages[$fondsData[1]]=$fondsData[0];
    }

    $DB2 = new DB();
    foreach ($portefeuilles as $pdata)
    {
      $newOrder = array();
      $this->pdf->pdata=$pdata;
      $totaalOrder=0;
      $this->pdf->addPage("L");
      //vulTijdelijkeTabel($pdata['Portefeuille'],$this->rapportageDatum);
      
     	$portefeuilleData = berekenPortefeuilleWaarde($pdata['Portefeuille'],$this->rapportageDatum);
      vulTijdelijkeTabel($portefeuilleData,$pdata['Portefeuille'],$this->rapportageDatum);
          
      if($this->progressbar)
      {
         $pro_step += $pro_multiplier;
		     $this->progressbar->moveStep($pro_step);
	    }

	    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE rapportageDatum ='".$this->rapportageDatum."' AND portefeuille = '".$pdata['Portefeuille']."' ".$__appvar['TijdelijkeRapportageMaakUniek']." ";
	    $DB2->SQL($query);
	    $DB2->Query();

	    $totaalWaarde = $DB2->nextRecord();
	    $totaalWaarde = $totaalWaarde['totaal'];
	    foreach ($fondsPercentages as $fonds=>$gewenstPercentage)
	    {
       	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE type = 'fondsen' AND Fonds = '$fonds' AND rapportageDatum ='".$this->rapportageDatum."' AND  portefeuille = '".$pdata['Portefeuille']."' ".$__appvar['TijdelijkeRapportageMaakUniek']."";
				$DB2->SQL($query);
				$DB2->Query();
				$fondsWaarde = $DB2->nextRecord();
				$fondsWaarde = $fondsWaarde['totaal'];

	  		$query = "SELECT Fondskoersen.Koers , Fondsen.Fondseenheid, Fondsen.ISINCode, Fondsen.Omschrijving, Valutakoersen.Koers as valutaKoers
	  		       FROM Fondsen
	  		       LEFT JOIN Fondskoersen ON Fondsen.Fonds = Fondskoersen.Fonds
	  		       LEFT JOIN Valutakoersen ON Fondsen.Valuta = Valutakoersen.Valuta
	  		       WHERE Fondskoersen.Fonds = '$fonds' AND Fondskoersen.Datum <='".$this->rapportageDatum."' ORDER BY Fondskoersen.Datum  desc limit 1";
			  $DB2->SQL($query);// Fondsen.AfrondingsAantal
			  $DB2->Query();
		  	$koersWaarde 	= $DB2->nextRecord();


		  	$norm['norm']=1;
				if($this->selectData['norm'] == 1)
				{
				  /*
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
					WHERE Portefeuilles.Portefeuille='".$pdata['Portefeuille']."' AND BeleggingssectorPerFonds.Fonds='".$fonds."'";
					$DB2->SQL($query);
					$DB2->Query();
					$norm = $DB2->nextRecord();
          */

					$query="SELECT norm/100 as norm  FROM ZorgplichtPerPortefeuille WHERE Portefeuille='".$norm['Portefeuille']."' AND Zorgplicht='".$norm['Zorgplicht']."'";
					$DB2->SQL($query);
					$DB2->Query();
					$normPortefeuille = $DB2->nextRecord();
					if($normPortefeuille['norm'] <> '')
					  $norm['norm']=$normPortefeuille['norm'];
				}


				$werkelijkPercentage=$fondsWaarde/$totaalWaarde*100;
				$afwijking=($norm['norm']*$gewenstPercentage)-$werkelijkPercentage;
				$transactieWaarde=$totaalWaarde*$afwijking/100;
				$gewensteWaarde=$totaalWaarde*$gewenstPercentage/100;
				$stuks 	= round(($transactieWaarde / ($koersWaarde['Koers'] * $koersWaarde['valutaKoers'])) /$koersWaarde['Fondseenheid']);

        if($koersWaarde['AfrondingsAantal'] > 0)
		  		$stuks  = round(($stuks / $koersWaarde['AfrondingsAantal']),0)  * $koersWaarde['AfrondingsAantal'];

		  	$orderWaarde=	$stuks * $koersWaarde['Fondseenheid']*($koersWaarde['Koers'] * $koersWaarde['valutaKoers']);
				if($stuks < 0)
				{
				  $verkoop = abs($stuks);
				  $aankoop =0;
				}
				else
				{
				  $aankoop  = $stuks;
				  $verkoop  =0;
				}
				//
        /*
				 	$query="SELECT id FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.type = 'FONDS' AND ".
									 " TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatum."' AND ".
									 " TijdelijkeRapportage.portefeuille = '".$pdata['Portefeuille']."'  AND ".
									 " TijdelijkeRapportage.fonds = '".$fonds."' ".$__appvar['TijdelijkeRapportageMaakUniek']."";
				  	$DB2->SQL($query);
				  	$DB2->Query();
            if($DB2->records() > 0)
              $query="UPDATE TijdelijkeRapportage SET actuelePortefeuilleWaardeEuro=actuelePortefeuilleWaardeEuro+$orderWaarde
					            WHERE TijdelijkeRapportage.type = 'FONDS' AND ".
						  			 " TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatum."' AND ".
						 	 	  	 " TijdelijkeRapportage.portefeuille = '".$pdata['Portefeuille']."'  AND ".
							  		 " TijdelijkeRapportage.Fonds = '".$fonds."' ".$__appvar['TijdelijkeRapportageMaakUniek']."";
            else
            {
              $query="SELECT Beleggingssector,BeleggingssectorPerFonds.Beleggingscategorie,Regio,CategorienPerHoofdcategorie.Hoofdcategorie
  	          FROM BeleggingssectorPerFonds
  	          LEFT Join CategorienPerHoofdcategorie ON BeleggingssectorPerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie
  	          WHERE Fonds='".addslashes($fonds)."'";
  	          $DB2->SQL($query);
  	          $sectorData=$DB2->lookupRecord();
              $query="INSERT INTO TijdelijkeRapportage SET actuelePortefeuilleWaardeEuro='$orderWaarde',
                      TijdelijkeRapportage.type = 'FONDS',
                      Beleggingssector='".$sectorData['Beleggingssector']."',
                      Beleggingscategorie='".$sectorData['Beleggingscategorie']."',
                      Hoofdcategorie='".$sectorData['Hoofdcategorie']."',
                      rapportageDatum ='".$this->rapportageDatum."',
                      portefeuille = '".$pdata['Portefeuille']."',
                      Fonds='".addslashes($fonds)."',
									    add_date = NOW(),
									    add_user = '$USR'";

            }
					  $DB2->SQL($query);
					  $DB2->Query();
            $query="UPDATE TijdelijkeRapportage SET actuelePortefeuilleWaardeEuro=actuelePortefeuilleWaardeEuro-$orderWaarde
					            WHERE TijdelijkeRapportage.type = 'LIQ' AND ".
						  			 " TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatum."' AND ".
						  			 " TijdelijkeRapportage.portefeuille = '".$pdata['Portefeuille']."' ".$__appvar['TijdelijkeRapportageMaakUniek']." LIMIT 1 ";
					  $DB2->SQL($query);
					  $DB2->Query();
          */  
					// voer fictieve transactie uit op de tijdelijke rapportage ivm zorgplicht berekening.
					$query="SELECT id FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.type = 'fondsen' AND ".
									 " TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatum."' AND ".
									 " TijdelijkeRapportage.portefeuille = '".$pdata['Portefeuille']."'  AND ".
									 " TijdelijkeRapportage.Fonds = '".addslashes($fonds)."' ".$__appvar['TijdelijkeRapportageMaakUniek'];
					$DB2->SQL($query);
					$DB2->Query();
          if($DB2->records() > 0)
            $query="UPDATE TijdelijkeRapportage SET actuelePortefeuilleWaardeEuro=actuelePortefeuilleWaardeEuro+$orderWaarde
					          WHERE TijdelijkeRapportage.type = 'fondsen' AND ".
									 " TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatum."' AND ".
									 " TijdelijkeRapportage.portefeuille = '".$pdata['Portefeuille']."'  AND ".
									 " TijdelijkeRapportage.Fonds = '".addslashes($fonds)."' ".$__appvar['TijdelijkeRapportageMaakUniek'];
          else
          {
            $query="SELECT Fonds,Beleggingscategorie FROM BeleggingscategoriePerFonds WHERE Vermogensbeheerder='".$portefeuille['Vermogensbeheerder']."' AND Fonds = '".addslashes($fonds)."' ";
            $DB2->SQL($query);
    		    $Beleggingscategorie=$DB2->lookupRecord();
    		    $Beleggingscategorie=$Beleggingscategorie['Beleggingscategorie'];

    		    $query="INSERT INTO TijdelijkeRapportage SET actuelePortefeuilleWaardeEuro='$orderWaarde', add_user='$USR', TijdelijkeRapportage.sessionId = '".$_SESSION['usersession']['sessionId']."',
                    TijdelijkeRapportage.type = 'fondsen', Beleggingscategorie='$Beleggingscategorie',
                    rapportageDatum ='".$this->rapportageDatum."',
                    portefeuille = '".$pdata['Portefeuille']."',
                    Fonds='".addslashes($fonds)."' ";

          }
					$DB2->SQL($query);
					$DB2->Query();
          $query="UPDATE TijdelijkeRapportage SET actuelePortefeuilleWaardeEuro=actuelePortefeuilleWaardeEuro-$orderWaarde
					          WHERE TijdelijkeRapportage.type = 'Rekening' AND ".
									 " TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatum."' AND ".
									 " TijdelijkeRapportage.portefeuille = '".$pdata['Portefeuille']."' ".$__appvar['TijdelijkeRapportageMaakUniek']." LIMIT 1 ";
					$DB2->SQL($query);
					$DB2->Query();            
            
            
				//
        $totaalOrder+=$orderWaarde;


	      $pdfdata = array($fonds,
												$this->formatGetal($gewenstPercentage,1),
												$this->formatGetal($werkelijkPercentage,1),
												$this->formatGetal($afwijking,2),
												$this->formatGetal($aankoop,0),
												$this->formatGetal($verkoop,0),
												$this->formatGetal($transactieWaarde,2),
												$this->formatGetal($gewensteWaarde,2),
												$this->formatGetal($koersWaarde['Koers'],2)
												 );
				 $this->pdf->Row($pdfdata);
//array("Client","Portefeuille","Accountmanager","Modelportefeuille","Depotbank"
				 $this->pdf->excelData[] =array($pdata['Client'],$pdata['Portefeuille'],$pdata['Accountmanager'],$pdata['Risicoprofiel'],$pdata['SoortOvereenkomst'], $pdata['Modelportefeuille'],$pdata['Depotbank'],$pdata['Risicoprofiel'],
				                $fonds,
												round($gewenstPercentage,8),
												round($werkelijkPercentage,8),
												round($afwijking,8),
												round($aankoop,0),
												round($verkoop,0),
												round($transactieWaarde,2),
												round($gewensteWaarde,2),
												round($koersWaarde['Koers'],2),
												$koersWaarde['ISINCode'],
												$pdata['Memo']);

				if($aankoop <> 0 || $verkoop <> 0)
				{
				  if($aankoop <> 0)
				    $aantal=$aankoop;
				  else
				    $aantal=$verkoop*-1;

				    $newOrder[]	= array($fonds,$koersWaarde['ISINCode'],$pdata['Portefeuille'],
												round($aantal,2),
												round($transactieWaarde,2),
												$pdata['Client'],
												$koersWaarde['Omschrijving'],
												$pdata['Depotbank'],
												'',
												''
												);
				}
	    }

	    $query="SELECT sum(actuelePortefeuilleWaardeEuro) as waardeEur  FROM TijdelijkeRapportage WHERE type = 'rekening' AND rapportageDatum ='".$this->rapportageDatum."' AND portefeuille = '".$pdata['Portefeuille']."' ".$__appvar['TijdelijkeRapportageMaakUniek']."";
  	  $DB2->SQL($query);
  	  $liqData=$DB2->lookupRecord();
  		$zorgplicht = new Zorgplichtcontrole();
			$zpwaarde=$zorgplicht->zorgplichtMeting($pdata,$this->rapportageDatum);

	    $this->pdf->ln();
      $this->pdf->Row(array('Saldo liq. na orders',$this->formatGetal($liqData['waardeEur'],2)));
			$this->pdf->Row(array('Ordergrootte',$this->formatGetal($totaalOrder*-1,2)));
			$this->pdf->Row(array('Zorgplicht',$zpwaarde['zorgMeting']));

			foreach ($newOrder as $order)
			{
			  $order[8]=$zpwaarde['voldoet'];
			  if($liqData['waardeEur'] < 0)
			    $order[9]=round($liqData['waardeEur'],2);
			  $this->orderData[]=$order;
			}

			if($zpwaarde['zorgMeting']=='Voldoet niet ')
		  	$this->pdf->Row(array($zpwaarde['zorgMetingReden']));

			verwijderTijdelijkeTabel($pdata['Portefeuille'],$this->rapportageDatum);

		}
		//listarray($this->orderData);
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
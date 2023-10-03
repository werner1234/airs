<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/02 15:57:50 $
File Versie					: $Revision: 1.20 $

$Log: RapportMODEL_L33.php,v $
Revision 1.20  2020/05/02 15:57:50  rvv
*** empty log message ***

Revision 1.19  2020/03/11 16:21:41  rvv
*** empty log message ***

Revision 1.18  2019/04/27 18:30:34  rvv
*** empty log message ***

Revision 1.17  2018/04/21 17:56:04  rvv
*** empty log message ***

Revision 1.16  2017/09/10 14:31:29  rvv
*** empty log message ***

Revision 1.15  2017/08/27 13:56:27  rvv
*** empty log message ***

Revision 1.14  2017/03/11 20:27:43  rvv
*** empty log message ***

Revision 1.13  2017/03/01 17:17:08  rvv
*** empty log message ***

Revision 1.12  2016/10/02 12:38:58  rvv
*** empty log message ***

Revision 1.11  2016/09/11 08:30:02  rvv
*** empty log message ***

Revision 1.10  2016/06/22 16:15:05  rvv
*** empty log message ***

Revision 1.9  2016/03/19 17:22:57  rvv
*** empty log message ***

Revision 1.8  2014/12/20 16:32:36  rvv
*** empty log message ***

Revision 1.7  2013/05/29 15:49:59  rvv
*** empty log message ***

Revision 1.6  2013/03/31 12:35:14  rvv
*** empty log message ***

Revision 1.5  2013/02/27 17:04:41  rvv
*** empty log message ***

Revision 1.4  2011/10/02 08:37:20  rvv
*** empty log message ***

Revision 1.3  2011/07/30 16:39:50  rvv
*** empty log message ***

Revision 1.2  2011/07/27 16:27:15  rvv
*** empty log message ***

Revision 1.1  2011/07/03 06:42:47  rvv
*** empty log message ***

Revision 1.8  2010/03/03 20:04:39  rvv
*** empty log message ***


*/

class RapportModel_L33
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function RapportModel_L33($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  $this->pdf = &$pdf;
		$this->selectData =array('percentage' => 0.0,'modelcontrole_percentage' => 0.0,'modelcontrole_rapport' => 'percentage','modelcontrole_uitvoer' => 'alles','modelcontrole_filter' => 'gekoppeld');
		$this->portefeuille = $portefeuille;
		if(!is_array($this->pdf->excelData))
		  $this->pdf->excelData 	= array();
		$this->pdf->rapport_type = "MODEL";
		$this->pdf->rapport_titel = "Modelcontrole in ".$this->pdf->rapportageValuta;
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;
		$this->pdf->fondsRapport = true;
		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->selectData['datumTm'] = db2jul($rapportageDatum);
		$this->pdf->tmdatum = $this->selectData['datumTm'];
		$this->pdf->rapport_datum = $this->selectData['datumTm'];
		// selectdata ook aan PDF geven
		$this->pdf->selectData = $this->selectData;
		$this->orderData=array();
		$this->omrekenKoers=$this->pdf->ValutaKoersEind;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
		if ($VierDecimalenZonderNullen)
		{
			$getal = explode('.',$waarde);
			$decimaalDeel = substr($getal[1],0,6);
			if ($decimaalDeel != 0)
			{
				for ($i = strlen($decimaalDeel); $i >=0; $i--)
				{
					$decimaal = $decimaalDeel[$i-1];
					if ($decimaal != '0' && !$newDec)
					{
						$newDec = $i;
					}
				}
				return number_format($waarde,$newDec,",",".");
			}
			else
				return number_format($waarde,$dec,",",".");
		}
		else
			return number_format($waarde,$dec,",",".");
	}
	function printKop($title)
	{
		$this->pdf->SetFont($this->pdf->rapport_font,'bi',$this->pdf->rapport_fontsize);
		$this->pdf->Cell(100 , 4 , $title , 0, 1, "L");
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}

	function writeRapport()
	{
		global $__appvar,$USR;

		$DB = new DB();

		if($this->pdf->ModelSettings['modelcontrole_level'])
		 $this->selectData['modelcontrole_level']=$this->pdf->ModelSettings['modelcontrole_level'];
		else
		  $this->selectData['modelcontrole_level']='fonds';

		if($this->selectData['modelcontrole_level'] != 'fonds')
		{
		  if($this->selectData['modelcontrole_level'] == 'beleggingscategorie')
  	    $query = "SELECT Beleggingscategorie as id,Omschrijving as value FROM Beleggingscategorien";
		  elseif($this->selectData['modelcontrole_level'] == 'beleggingssector')
		    $query = "SELECT Beleggingssector as id,Omschrijving as value FROM Beleggingssectoren";
		  else
		    $query = "SELECT Regio as id,Omschrijving as value FROM Regios";
	    $DB->SQL($query);
      $DB->Query();
	    while($data=$DB->nextRecord())
	      $omschrijving[$data['id']]=$data['value'];
		}


		$einddatum = jul2sql($this->selectData['datumTm']);

		$jaar = date("Y",$this->selectData['datumTm']);


		$q = " SELECT ".
						 " Portefeuilles.Portefeuille, ".
						 " Portefeuilles.Startdatum, ".
						 " Portefeuilles.Client, ".
						 " Portefeuilles.Depotbank, ".
						 " Portefeuilles.ModelPortefeuille, ".
						 " Clienten.Naam,
				       CRM_naw.profielOverigeBeperkingen   ".
					 " FROM Portefeuilles
					 JOIN Clienten ON Portefeuilles.Client = Clienten.Client
					 LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille=CRM_naw.portefeuille WHERE ".
					 " Portefeuilles.Portefeuille = '".$this->portefeuille."'";
    
   
		$DB->SQL($q);
		$DB->Query();
		$records = $DB->records();

		while($portefeuille = $DB->NextRecord())
		{
      $this->pdf->overigeBeperkingen=$portefeuille['profielOverigeBeperkingen'];
      
		  $this->pdf->excelData[]=array($portefeuille['Portefeuille'],$portefeuille['Client']);
		  $this->pdf->excelData[]=array("ISIN code","Fonds",'Werkelijke waarde','in %',"Model Percentage","Afwijking",
												 "Aantal kopen","Waarde kopen","Aantal verkopen","Waarde verkopen",'soortOvereenkomst');
		  $this->pdf->selectData['modelcontrole_portefeuille']=$portefeuille['ModelPortefeuille'];
	  	$this->selectData['modelcontrole_portefeuille']=$portefeuille['ModelPortefeuille'];

  		//verwijderTijdelijkeTabel("m".$this->selectData['modelcontrole_portefeuille'],$einddatum);
	  	//$portefeuilleData = berekenPortefeuilleWaarde($this->selectData['modelcontrole_portefeuille'], $einddatum);

			$DB3 = new DB();
			$query="SELECT Fixed, Beleggingscategorie FROM ModelPortefeuilles WHERE Portefeuille='".$this->selectData['modelcontrole_portefeuille']."'";
			$DB3->SQL($query);
			$DB3->Query();
			$modelType = $DB3->nextRecord();
			if($modelType['Fixed']==1)
				$portefeuilleData = berekenFixedModelPortefeuille($this->selectData['modelcontrole_portefeuille'],$einddatum);
			elseif($modelType['Fixed']==3)
				$portefeuilleData = berekenMeervoudigeModelPortefeuille($portefeuille['Portefeuille'],$einddatum,$this->selectData['modelcontrole_portefeuille']);
			else
				$portefeuilleData = berekenPortefeuilleWaarde($this->selectData['modelcontrole_portefeuille'], $einddatum);

		  vulTijdelijkeTabel($portefeuilleData,"m".$this->selectData['modelcontrole_portefeuille'],$einddatum);

		  if($portefeuille['ModelPortefeuille']=='')
		  {
		    echo "Er is geen modelportefeuille aan deze portefeuille gekoppeld. Rapportage afgebroken.";
			exit;			
		  }
		  	  // bereken totaal waarde model
  		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$einddatum."' AND ".
							 " portefeuille = '"."m".$portefeuille['ModelPortefeuille']."' AND type <> 'rente' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);

		$DB3 = new DB();
		$DB3->SQL($query);
		$DB3->Query();
		$modelwaarde = $DB3->nextRecord(); 
		$modelTotaal = $modelwaarde['totaal'];
		if($this->selectData["modelcontrole_rapport"] == "vastbedrag")
		{
			$portefeuille = array();
		}
		// set pdf vars
		$this->pdf->naamOmschrijving = $portefeuille['Naam'];
		$this->pdf->clientOmschrijving = $portefeuille['Client']." / ".$portefeuille['Portefeuille']." / ".$portefeuille['Depotbank'];

			$this->pdf->AddPage();
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$uitsluitingen=bepaalModelUitsluitingen($this->portefeuille,$einddatum);
      foreach($uitsluitingen['portefeuilleRegels'] as $row)
        $this->pdf->Row(array('',$row[0]));

			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
								 " rapportageDatum = '".$einddatum."' AND ".
								 " portefeuille = '".$portefeuille['Portefeuille']."' AND type <> 'rente' "
								 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query,__FILE__,__LINE__);
			$DB3 = new DB();
			$DB3->SQL($query);
			$DB3->Query();
			$portefwaarde = $DB3->nextRecord();
			$portefTotaal = $portefwaarde['totaal'];

			if($this->selectData["modelcontrole_rapport"] == "vastbedrag")
				$portefTotaal = $this->selectData["modelcontrole_vastbedrag"];
			if($this->selectData['modelcontrole_percentage'] > 0)
				$afwijking = " HAVING ABS(afwijking) > ".$this->selectData['modelcontrole_percentage']." ";
			if($this->selectData['modelcontrole_uitvoer'] == "afwijkingen")
				$afwijking = " HAVING afwijking <> 0 ";




      $modelUniek=" AND model.add_user = '".$USR."' AND model.sessionId = '".$_SESSION['usersession']['sessionId']."' ";
      $portefUniek=" AND portef.add_user = '".$USR."' AND portef.sessionId = '".$_SESSION['usersession']['sessionId']."' ";
    
      //$modelUniek='';$portefUniek='';
			$query = "SELECT
			SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->selectData['modelcontrole_portefeuille']."' ,model.actuelePortefeuilleWaardeEuro,0))  AS modelWaarde,
		  SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->selectData['modelcontrole_portefeuille']."' ,model.actuelePortefeuilleWaardeEuro,0)) / $modelTotaal * 100 AS percentageModel,
  		SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,portef.actuelePortefeuilleWaardeEuro,0)) AS portefeuilleWaarde,
	  	SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,portef.actuelePortefeuilleWaardeEuro,0)) / $portefTotaal * 100 AS percentagePortefeuille,
			(
			  SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->selectData['modelcontrole_portefeuille']."' ,model.actuelePortefeuilleWaardeEuro,0)) / $modelTotaal * 100 -
		  	SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,portef.actuelePortefeuilleWaardeEuro,0)) / $portefTotaal * 100
			) AS afwijking,
      TijdelijkeRapportage.".$this->selectData['modelcontrole_level']." as RegelOmschrijving,
			TijdelijkeRapportage.fonds,
			Fondsen.ISINCode,
      TijdelijkeRapportage.beleggingscategorieOmschrijving,
      TijdelijkeRapportage.hoofdcategorieOmschrijving,
			TijdelijkeRapportage.fondsOmschrijving,
			TijdelijkeRapportage.actueleFonds,
			TijdelijkeRapportage.actueleValuta,
			TijdelijkeRapportage.fondsEenheid,
      TijdelijkeRapportage.totaalAantal,
      TijdelijkeRapportage.actuelePortefeuilleWaardeEuro
			FROM TijdelijkeRapportage
			LEFT JOIN TijdelijkeRapportage AS model ON model.fonds = TijdelijkeRapportage.fonds AND model.portefeuille = \""."m".$this->selectData['modelcontrole_portefeuille']."\" AND model.type = 'fondsen'  AND model.rapportageDatum = '".$einddatum."' $modelUniek
			LEFT JOIN TijdelijkeRapportage AS portef ON portef.fonds = TijdelijkeRapportage.fonds AND portef.portefeuille = \"".$portefeuille['Portefeuille']."\" AND portef.type = 'fondsen'  AND portef.rapportageDatum = '".$einddatum."'  $portefUniek 
      LEFT JOIN Fondsen on Fondsen.Fonds=model.fonds  or Fondsen.Fonds=portef.fonds
			WHERE
			TijdelijkeRapportage.type = 'fondsen' AND
			TijdelijkeRapportage.rapportageDatum = '".$einddatum."' AND
			(TijdelijkeRapportage.portefeuille = \"".$portefeuille['Portefeuille']."\" OR TijdelijkeRapportage.portefeuille = \""."m".$this->selectData['modelcontrole_portefeuille']."\")  "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.".$this->selectData['modelcontrole_level']." ".$afwijking."
			ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde, TijdelijkeRapportage.beleggingscategorieVolgorde, afwijking DESC, TijdelijkeRapportage.fonds ";
			debugSpecial($query,__FILE__,__LINE__);

			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();

			while($fdata = $DB2->nextRecord())
			{
				if(!isset($lastCat) || $lastCat != $fdata['beleggingscategorieOmschrijving'])
			  {
			    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
			    if(isset($totalen[$lastCat]))
			    {
			      $this->pdf->Row(array('Totaal',$lastCat,
															$this->formatGetal($totalen[$lastCat]['portefeuilleWaarde']/$this->omrekenKoers,0),
															$this->formatGetal($totalen[$lastCat]['percentagePortefeuille'],1),
															$this->formatGetal($totalen[$lastCat]['percentageModel'],1),
															$this->formatGetal($totalen[$lastCat]['afwijking'],1),'',
															$this->formatGetal($totalen[$lastCat]['aankoop']/$this->omrekenKoers,0),'',
															$this->formatGetal($totalen[$lastCat]['verkoop']/$this->omrekenKoers,0)));
						$this->pdf->excelData[] = array('Totaal',$lastCat,round($totalen[$lastCat]['portefeuilleWaarde'],2),round($totalen[$lastCat]['percentagePortefeuille'],1),round($totalen[$lastCat]['percentageModel'],1),
											round($totalen[$lastCat]['afwijking'],1),'',round($totalen[$lastCat]['aankoop'],2),'',round($totalen[$lastCat]['verkoop'],2),$this->pdf->portefeuilledata['SoortOvereenkomst']);
			    }
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

			  }

			  if(!isset($lastHCat) || $lastHCat != $fdata['hoofdcategorieOmschrijving'])
			  {
			    $this->pdf->SetFont($this->pdf->rapport_font,'BI',$this->pdf->rapport_fontsize);
			    if(isset($Htotalen[$lastHCat]))
			    {
			      $this->pdf->Row(array('Totaal',$lastHCat,
															$this->formatGetal($Htotalen[$lastHCat]['portefeuilleWaarde']/$this->omrekenKoers,0),
															$this->formatGetal($Htotalen[$lastHCat]['percentagePortefeuille'],1),
															$this->formatGetal($Htotalen[$lastHCat]['percentageModel'],1),
										          $this->formatGetal($Htotalen[$lastHCat]['afwijking'],1),'',
															$this->formatGetal($Htotalen[$lastHCat]['aankoop']/$this->omrekenKoers,0),'',
															$this->formatGetal($Htotalen[$lastHCat]['verkoop']/$this->omrekenKoers,0)));
						$this->pdf->excelData[] = array('Totaal',$lastHCat,round($Htotalen[$lastHCat]['portefeuilleWaarde'],2),round($Htotalen[$lastHCat]['percentagePortefeuille'],1),round($Htotalen[$lastHCat]['percentageModel'],1),
											round($Htotalen[$lastHCat]['afwijking'],1),
											'',round($Htotalen[$lastHCat]['aankoop'],2),'',round($Htotalen[$lastHCat]['verkoop'],2),$this->pdf->portefeuilledata['SoortOvereenkomst']);
			    }
	  			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			  }


			  if(!isset($lastHCat) || $lastHCat != $fdata['hoofdcategorieOmschrijving'])
			  {
          $this->pdf->SetFont($this->pdf->rapport_font,'BI',$this->pdf->rapport_fontsize);
			    $this->pdf->Row(array($fdata['hoofdcategorieOmschrijving']));
			    $this->pdf->excelData[] = array($fdata['hoofdcategorieOmschrijving']);
			    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			  }

			  if(!isset($lastCat) || $lastCat != $fdata['beleggingscategorieOmschrijving'])
			  {
			    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
			    $this->pdf->Row(array($fdata['beleggingscategorieOmschrijving']));
			    $this->pdf->excelData[] = array($fdata['beleggingscategorieOmschrijving']);
			    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			  }
			  $aankoopStuks=0;
			  $verkoopStuks=0;

				$aankoopWaarde 	= ((($portefTotaal) / 100) * $fdata['percentageModel']) - $fdata['portefeuilleWaarde'];
				$aankoopStuks 	= round(($aankoopWaarde / ($fdata['actueleFonds'] * $fdata['actueleValuta']))  / $fdata['fondsEenheid'],4);
				if($fdata['fondsEenheid'] == '0.01')
		    {
          if($aankoopStuks > 0)
		        $aankoopStuks=floor($aankoopStuks/100)*100;
          else
            $aankoopStuks=ceil($aankoopStuks/100)*100;  
		      $aankoopWaarde 	= ($aankoopStuks * ($fdata['actueleFonds'] * $fdata['actueleValuta'])) * $fdata['fondsEenheid'];
		    }
				
        if($fdata['percentageModel']==0)
        {
          $aankoopStuks=$fdata['totaalAantal']*-1;
          $aankoopWaarde=$fdata['actuelePortefeuilleWaardeEuro']*-1;
        }
		    if($aankoopWaarde > 0)
		     $verkoopWaarde=0;
		    else
		    {
		      $verkoopWaarde=$aankoopWaarde;
		      $aankoopWaarde=0;
		    }

				if($aankoopStuks < 0)
				{
				  $verkoopStuks = $aankoopStuks * -1;
				  $aankoopStuks = 0;
				}

				if($aankoopStuks > 0)
			    $aankoopStuks=round($aankoopStuks);

	 			if($verkoopStuks > 0)
		    {
		      if(intval($verkoopStuks) == $verkoopStuks )
		        $verkoopStuks = round($verkoopStuks);
	    	}

				$waardeVolgensModel = (($portefTotaal) / 100) * $fdata['percentageModel'];

				if($this->selectData['modelcontrole_level'] != 'fonds')
				{
				  $fdata['fondsOmschrijving']=$omschrijving[$fdata['RegelOmschrijving']];
				  $fdata['actueleFonds']=0;
				  $aankoopStuks=0;
				  $verkoopStuks=0;
				}


			//	$geschatOrderbedrag 	= (($verkoopStuks-$aankoopStuks) * ($fdata['actueleFonds'] * $fdata['actueleValuta'])) * $fdata['fondsEenheid'];
				

				$aankoopWaarde=$aankoopStuks* $fdata['actueleFonds'] * $fdata['actueleValuta'] * $fdata['fondsEenheid'];
	    	if($fdata['percentageModel']==0)
				{
					$verkoopStuksTxt = $this->formatAantal($fdata['totaalAantal'], 0, true);
					$verkoopWaarde=$fdata['totaalAantal']* $fdata['actueleFonds'] * $fdata['actueleValuta'] * $fdata['fondsEenheid'];
				}
				else
				{
					$verkoopStuksTxt = $this->formatGetal($verkoopStuks, 0);
					$verkoopWaarde=round($verkoopStuks)* $fdata['actueleFonds'] * $fdata['actueleValuta'] * $fdata['fondsEenheid'];
				}

				$data = array($fdata['ISINCode'],$fdata['fondsOmschrijving'],
					$this->formatGetal($fdata['portefeuilleWaarde']/$this->omrekenKoers,0),
					$this->formatGetal($fdata['percentagePortefeuille'],1),
					$this->formatGetal($fdata['percentageModel'],1),
					$this->formatGetal($fdata['afwijking'],1),
					$this->formatGetal($aankoopStuks,0),
					$this->formatGetal($aankoopWaarde/$this->omrekenKoers,0),
					$verkoopStuksTxt,
					$this->formatGetal($verkoopWaarde/$this->omrekenKoers,0));
				$this->pdf->Row($data);


				$data = array($fdata['ISINCode'],$fdata['fondsOmschrijving'],round($fdata['portefeuilleWaarde'],2),round($fdata['percentagePortefeuille'],1),round($fdata['percentageModel'],1),
											round($fdata['afwijking'],1),round($aankoopStuks,0),round($aankoopWaarde,2),round($verkoopStuks,0),round($verkoopWaarde,2),$this->pdf->portefeuilledata['SoortOvereenkomst']);
				$this->pdf->excelData[] = $data;

				$totalen[$fdata['beleggingscategorieOmschrijving']]['portefeuilleWaarde']+=$fdata['portefeuilleWaarde'];
				$totalen[$fdata['beleggingscategorieOmschrijving']]['percentagePortefeuille']+=$fdata['percentagePortefeuille'];
				$totalen[$fdata['beleggingscategorieOmschrijving']]['percentageModel']+=$fdata['percentageModel'];
				$totalen[$fdata['beleggingscategorieOmschrijving']]['afwijking']+=$fdata['afwijking'];
				$totalen[$fdata['beleggingscategorieOmschrijving']]['aankoop']+=$aankoopWaarde;
				$totalen[$fdata['beleggingscategorieOmschrijving']]['verkoop']+=$verkoopWaarde;

				$Htotalen[$fdata['hoofdcategorieOmschrijving']]['portefeuilleWaarde']+=$fdata['portefeuilleWaarde'];
				$Htotalen[$fdata['hoofdcategorieOmschrijving']]['percentagePortefeuille']+=$fdata['percentagePortefeuille'];
				$Htotalen[$fdata['hoofdcategorieOmschrijving']]['percentageModel']+=$fdata['percentageModel'];
				$Htotalen[$fdata['hoofdcategorieOmschrijving']]['afwijking']+=$fdata['afwijking'];
				$Htotalen[$fdata['hoofdcategorieOmschrijving']]['aankoop']+=$aankoopWaarde;
				$Htotalen[$fdata['hoofdcategorieOmschrijving']]['verkoop']+=$verkoopWaarde;

			  if(round($fdata['afwijking'],2) <> 0.00 && ($aankoopStuks <> 0 || $verkoopStuks <> 0))
			  {
			    $this->orderData[]=array('fonds'=>$fdata['fonds'],'modelPercentage'=>$fdata['percentageModel'],'portefeuillePercentage'=>$fdata['percentagePortefeuille'],
			                             'afwijking'=>$fdata['afwijking'],'kopen'=>$aankoopStuks,'verkopen'=>$verkoopStuks,'overschrijding'=>$aankoopWaarde,'valuta'=>$fdata['valuta'],
			                             'modelWaarde'=>$waardeVolgensModel,'koers'=>$fdata['actueleFonds'],'portefeuille'=>$portefeuille['Portefeuille']);
			  }
			  $lastCat=$fdata['beleggingscategorieOmschrijving'];
			  $lastHCat = $fdata['hoofdcategorieOmschrijving'];
			}

		  $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
			if(isset($totalen[$lastCat]))
			{
			    $this->pdf->Row(array('Totaal',$lastCat,
														$this->formatGetal($totalen[$lastCat]['portefeuilleWaarde']/$this->omrekenKoers,0),
														$this->formatGetal($totalen[$lastCat]['percentagePortefeuille'],1),
														$this->formatGetal($totalen[$lastCat]['percentageModel'],1),
														$this->formatGetal($totalen[$lastCat]['afwijking'],1),'',
														$this->formatGetal($totalen[$lastCat]['aankoop']/$this->omrekenKoers,0),'',
														$this->formatGetal($totalen[$lastCat]['verkoop']/$this->omrekenKoers,0)));
					$this->pdf->excelData[] =	array('Totaal',$lastCat,round($totalen[$lastCat]['portefeuilleWaarde'],2),round($totalen[$lastCat]['percentagePortefeuille'],1),round($totalen[$lastCat]['percentageModel'],1),
											round($totalen[$lastCat]['afwijking'],1),
											'',round($totalen[$lastCat]['aankoop'],2),'',round($totalen[$lastCat]['verkoop'],2));
			}
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


	    $this->pdf->SetFont($this->pdf->rapport_font,'BI',$this->pdf->rapport_fontsize);
	    if(isset($Htotalen[$lastHCat]))
	    {
	      $this->pdf->Row(array('Totaal',$lastHCat,
													$this->formatGetal($Htotalen[$lastHCat]['portefeuilleWaarde']/$this->omrekenKoers,0),
													$this->formatGetal($Htotalen[$lastHCat]['percentagePortefeuille'],1),
													$this->formatGetal($Htotalen[$lastHCat]['percentageModel'],1),
													$this->formatGetal($Htotalen[$lastHCat]['afwijking'],1),'',
													$this->formatGetal($Htotalen[$lastHCat]['aankoop']/$this->omrekenKoers,0),'',
													$this->formatGetal($Htotalen[$lastHCat]['verkoop']/$this->omrekenKoers,0)));
				$this->pdf->excelData[] =	array('Totaal',$lastHCat,round($Htotalen[$lastHCat]['portefeuilleWaarde'],2),round($Htotalen[$lastHCat]['percentagePortefeuille'],1),round($Htotalen[$lastHCat]['percentageModel'],1),
									round($Htotalen[$lastHCat]['afwijking'],1),'',round($Htotalen[$lastHCat]['aankoop'],2),'',round($Htotalen[$lastHCat]['verkoop'],2));
	    }
 			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);




			$query = "SELECT
			SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->selectData['modelcontrole_portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0))  AS modelWaarde,
		  SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->selectData['modelcontrole_portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $modelTotaal * 100 AS percentageModel,
   		SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) AS portefeuilleWaarde,
		  SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $portefTotaal * 100 AS percentagePortefeuille,
			(
			 SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->selectData['modelcontrole_portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $modelTotaal * 100 -
			 SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $portefTotaal * 100
			) AS afwijking,
			TijdelijkeRapportage.fonds,
			TijdelijkeRapportage.fondsOmschrijving,
			TijdelijkeRapportage.actueleFonds,
			TijdelijkeRapportage.actueleValuta,
			TijdelijkeRapportage.fondsEenheid
			FROM TijdelijkeRapportage
			WHERE
			TijdelijkeRapportage.type = 'rekening'  AND
			TijdelijkeRapportage.rapportageDatum = '".$einddatum."' AND
			(TijdelijkeRapportage.portefeuille = \"".$portefeuille['Portefeuille']."\" OR TijdelijkeRapportage.portefeuille = \""."m".$this->selectData['modelcontrole_portefeuille']."\")  "
			.$__appvar['TijdelijkeRapportageMaakUniek']."
			GROUP BY TijdelijkeRapportage.type ".$afwijking."
			ORDER BY afwijking DESC ";
			debugSpecial($query,__FILE__,__LINE__);

			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();

			$totaalRekeningen = array();

      if(count($uitsluitingen['gecorigeerdeRekeningen'])>0)
        array_push($totaalRekeningen,array("Liquiditeiten","Model waarde","Herziene waarde"));
      else
			array_push($totaalRekeningen,array("Liquiditeiten","Model waarde","Huidige waarde"));
			$this->pdf->Row(array(""));
			while($fdata = $DB2->nextRecord())
			{
				$aankoopWaarde 	= ((($portefTotaal) / 100) * $fdata['percentageModel']) - $fdata['portefeuilleWaarde'];
				$verkoopWaarde  = 0;
				$aankoopStuks 	= ($aankoopWaarde / ($fdata['actueleFonds'] * $fdata['actueleValuta']))  / $fdata['fondsEenheid'];
				$verkoopStuks = 0;
				$waardeVolgensModel = (($portefTotaal) / 100) * $fdata['percentageModel'];

				if ($fdata['portefeuilleWaarde'] != 0)
				{
				   $data = array($fdata['ISINCode'],$fdata['fondsOmschrijving'],round($fdata['portefeuilleWaarde'],2),round($fdata['percentagePortefeuille'],1),round($fdata['percentageModel'],1),
											round($fdata['afwijking'],1),round($aankoopStuks,0),round($aankoopWaarde,2),round($verkoopStuks,0),round($verkoopWaarde,2));
				   $this->pdf->excelData[] = $data;
				}
				$totalen['liq']['portefeuilleWaarde']+=$fdata['portefeuilleWaarde'];
				$totalen['liq']['percentagePortefeuille']+=$fdata['percentagePortefeuille'];
				$totalen['liq']['percentageModel']+=$fdata['percentageModel'];
				$totalen['liq']['afwijking']+=$fdata['afwijking'];
				$totalen['liq']['aankoop']+=$aankoopWaarde;
				$totalen['liq']['verkoop']+=$verkoopWaarde;
			}
      
      
     			$query=" SELECT TijdelijkeRapportage.fonds,
			TijdelijkeRapportage.fondsOmschrijving,
			TijdelijkeRapportage.actueleFonds,
			TijdelijkeRapportage.actueleValuta,
			TijdelijkeRapportage.fondsEenheid,
      TijdelijkeRapportage.rekening,
      TijdelijkeRapportage.actuelePortefeuilleWaardeEuro
			FROM TijdelijkeRapportage
			WHERE
			TijdelijkeRapportage.type = 'rekening'  AND
			TijdelijkeRapportage.rapportageDatum = '".$einddatum."' AND
			TijdelijkeRapportage.portefeuille = '".$portefeuille['Portefeuille']."' ".$__appvar['TijdelijkeRapportageMaakUniek']; 
      			$DB2->SQL($query);
			$DB2->Query();
      			while($fdata = $DB2->nextRecord())
			{
		//	 listarray($fdata);
       $this->pdf->Row(array($fdata['fondsOmschrijving'],$fdata['rekening'],$this->formatGetal($fdata['actuelePortefeuilleWaardeEuro']/$this->omrekenKoers,0)));
       }
      
      $lastCat='liq';
			$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
			if(isset($totalen[$lastCat]))
			{
			    $this->pdf->Row(array('Totaal','Liquiditeiten',
														$this->formatGetal($totalen[$lastCat]['portefeuilleWaarde']/$this->omrekenKoers,0),
														$this->formatGetal($totalen[$lastCat]['percentagePortefeuille'],1),
														$this->formatGetal($totalen[$lastCat]['percentageModel'],1),
														$this->formatGetal($totalen[$lastCat]['afwijking'],1),'',
														$this->formatGetal($totalen[$lastCat]['aankoop']/$this->omrekenKoers,0),'',
														$this->formatGetal($totalen[$lastCat]['verkoop']/$this->omrekenKoers,0)));

					$this->pdf->excelData[] =	array('Totaal','Liquiditeiten',round($totalen[$lastCat]['portefeuilleWaarde'],2),round($totalen[$lastCat]['percentagePortefeuille'],1),round($totalen[$lastCat]['percentageModel'],1),
											round($totalen[$lastCat]['afwijking'],1),'',round($totalen[$lastCat]['aankoop'],2),'',round($totalen[$lastCat]['verkoop'],2));
			}


      foreach ($totalen as $cat=>$waarden)
      {
				$alles['portefeuilleWaarde']+=$waarden['portefeuilleWaarde'];
				$alles['percentagePortefeuille']+=$waarden['percentagePortefeuille'];
				$alles['percentageModel']+=$waarden['percentageModel'];
				$alles['afwijking']+=$waarden['afwijking'];
				$alles['aankoop']+=$waarden['aankoop'];
				$alles['verkoop']+=$waarden['verkoop'];
      }
      $this->pdf->ln();
      
      if(isset($alles))
      {
			    $this->pdf->Row(array('Totaal','',
														$this->formatGetal($alles['portefeuilleWaarde']/$this->omrekenKoers,0),
														$this->formatGetal($alles['percentagePortefeuille'],1),
														$this->formatGetal($alles['percentageModel'],1),
														$this->formatGetal($alles['afwijking'],1),'',
														$this->formatGetal($alles['aankoop']/$this->omrekenKoers,0),'',
														$this->formatGetal($alles['verkoop']/$this->omrekenKoers,0)));
					$this->pdf->excelData[] =	array('Totaal','',round($alles['portefeuilleWaarde'],2),round($alles['percentagePortefeuille'],1),round($alles['percentageModel'],1),
											round($alles['afwijking'],1),'',round($alles['aankoop'],2),'',round($alles['verkoop'],2));
      }
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
		}


		if($this->pdf->extra == 'order')
		{
	      global $USR;
		  $db=new DB();
    	$query = "show tables like 'TijdelijkeOrderRegels'";
		  $db->SQL($query);
		  if (!$db->lookupRecord())
		  {
        $table = "CREATE TABLE `TijdelijkeOrderRegels` (
  			`id` int(11) NOT NULL auto_increment,
 			  `fonds` varchar(25) NOT NULL default '',
 			  `portefeuille` varchar(24) NOT NULL default '',
  		  `modelPercentage` double(8,4) NOT NULL,
  		  `portefeuillePercentage` double(8,4) NOT NULL,
  			`afwijking` double(8,4) NOT NULL,
  			`valuta` varchar(6) NOT NULL default '',
  		  `kopen` double(12,4) NOT NULL default '0.0000',
  			`verkopen` double(12,4) NOT NULL default '0.0000',
  			`overschrijding` double(12,4) NOT NULL default '0.0000',
  			`modelWaarde` double(12,4) NOT NULL default '0.0000',
 			  `koers` double(12,4) NOT NULL default '0.0000',
 			  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
 			  `add_user` varchar(10) NOT NULL default '',
  		  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  			`change_user` varchar(10) NOT NULL default '',
 			  PRIMARY KEY  (`id`))";
			  $db->SQL($table);
			  $db->Query();
		  }


		  $query="DELETE FROM TijdelijkeOrderRegels WHERE add_user='$USR'";
		  $db->SQL($query);
		  $db->Query();
		  foreach ($this->orderData as $orderregel)
		  {
		    $query="INSERT INTO TijdelijkeOrderRegels SET add_user='$USR',change_user='$USR',add_date=NOW(),change_date=NOW() ";
		    foreach ($orderregel as $veld=>$waarde)
		      $query.=" ,$veld='".addslashes($waarde)."'";
		    $db->SQL($query);
		    $db->Query();
		  }

		  header('Location: tijdelijkeorderregelsList.php');
	    exit;
		}

		$this->pdf->fondsRapport = false;
    verwijderTijdelijkeTabel("m".$this->selectData['modelcontrole_portefeuille'],$einddatum);
    
    if(count($uitsluitingen['portefeuilleRegels'])>0)
    {
      $portefeuilleData = berekenPortefeuilleWaarde($this->portefeuille, $einddatum, (substr($einddatum, 5, 5) == '01-01')?true:false, 'EUR', $einddatum);
      vulTijdelijkeTabel($portefeuilleData, $this->portefeuille, $einddatum);
    }
	}
}
?>
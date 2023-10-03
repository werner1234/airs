<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/02 15:57:50 $
File Versie					: $Revision: 1.5 $

$Log: RapportMODEL_L76.php,v $
Revision 1.5  2020/05/02 15:57:50  rvv
*** empty log message ***

Revision 1.4  2020/03/11 16:21:41  rvv
*** empty log message ***

Revision 1.3  2018/04/28 18:36:15  rvv
*** empty log message ***

Revision 1.2  2018/04/22 09:30:29  rvv
*** empty log message ***

Revision 1.1  2018/04/18 16:18:39  rvv
*** empty log message ***

Revision 1.5  2017/02/01 16:44:57  rvv
*** empty log message ***

Revision 1.4  2015/11/22 14:31:46  rvv
*** empty log message ***

Revision 1.3  2015/10/07 19:38:52  rvv
*** empty log message ***

Revision 1.2  2014/12/20 16:32:36  rvv
*** empty log message ***

Revision 1.1  2014/08/02 15:25:09  rvv
*** empty log message ***

Revision 1.12  2013/10/30 09:31:10  rvv
*** empty log message ***

Revision 1.11  2013/08/24 15:48:07  rvv
*** empty log message ***

Revision 1.10  2013/08/18 12:22:32  rvv
*** empty log message ***

Revision 1.9  2011/09/14 09:26:56  rvv
*** empty log message ***

*/
include_once("rapportRekenClass.php");

class RapportModel_L76
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function RapportModel_L76($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  $this->pdf = &$pdf;
		$this->selectData =
		array('percentage' => 0.0,
    'modelcontrole_percentage' => 0.0,
    'modelcontrole_rapport' => 'percentage',
    'modelcontrole_uitvoer' => 'alles',
    'modelcontrole_filter' => 'gekoppeld'
		);
		$this->portefeuille = $portefeuille;
		$this->pdf->excelData 	= array();
    	$this->pdf->rapport_titel =	"Overzicht portefeuilleModelcontrole";

		$this->pdf->rapport_type = "MODEL";
		$this->pdf->fondsRapport = true;
		$this->selectData['datumTm'] = db2jul($rapportageDatum);

		$this->pdf->tmdatum = $this->selectData['datumTm'];
		$this->pdf->rapport_datum = $this->selectData['datumTm'];
		// selectdata ook aan PDF geven
		$this->pdf->selectData = $this->selectData;
		$this->orderData=array();

	}

	function formatGetal($waarde, $dec)
	{
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
		global $__appvar;

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

      $this->pdf->SetFillColor(230,230,230);
      $fill=true;
		$einddatum = jul2sql($this->selectData['datumTm']);

		$jaar = date("Y",$this->selectData['datumTm']);

		$extraquery = " AND Portefeuilles.Portefeuille = '".$this->portefeuille."'";
		$q = " SELECT ".
						 " Portefeuilles.Portefeuille, ".
						 " Portefeuilles.Startdatum, ".
						 " Portefeuilles.Client, ".
						 " Portefeuilles.Depotbank, ".
             " Portefeuilles.Risicoklasse, ".
             " Portefeuilles.Vermogensbeheerder, ".
						 " Portefeuilles.ModelPortefeuille, ".
						 " Clienten.Naam  ".
					 " FROM (Portefeuilles, Clienten) ".$join." WHERE ".
					 " Portefeuilles.Client = Clienten.Client ".$extraquery;

		$DB->SQL($q);
		$DB->Query();
		$records = $DB->records();

		while($portefeuille = $DB->NextRecord())
		{
		  $this->pdf->selectData['modelcontrole_portefeuille']=$portefeuille['ModelPortefeuille'];
	  	$this->selectData['modelcontrole_portefeuille']=$portefeuille['ModelPortefeuille'];

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

	    if($modelType['Beleggingscategorie'] <> '')
 	    {
 	      $extraCategorieFilter=" AND TijdelijkeRapportage.Beleggingscategorie='".$modelType['Beleggingscategorie']."' ";
 	    }
		  	  // bereken totaal waarde model
  		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$einddatum."' AND ".
							 " portefeuille = '"."m".$portefeuille['ModelPortefeuille']."' AND type <> 'rente' $extraCategorieFilter "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
		  debugSpecial($query,__FILE__,__LINE__);
		
		  $DB3->SQL($query);
	  	$DB3->Query();
		  $modelwaarde = $DB3->nextRecord();
	  	$modelTotaal = $modelwaarde['totaal'];
      if($modelTotaal==0)
        $modelTotaal=0.01;

      $query = "SELECT norm FROM NormPerRisicoprofiel WHERE Risicoklasse='".$portefeuille['Risicoklasse']."'
 	                                                     AND Vermogensbeheerder='".$portefeuille['Vermogensbeheerder']."'
 	                                                     AND Beleggingscategorie='".$modelType['Beleggingscategorie']."'";
 	    $DB3->SQL($query); 
 	    $DB3->Query();
 	    $norm = $DB3->nextRecord();
 	 
			if($this->selectData["modelcontrole_rapport"] == "vastbedrag")
			{
				$portefeuille = array();
			}
			// set pdf vars
			$this->pdf->naamOmschrijving = $portefeuille['Naam'];
			$this->pdf->clientOmschrijving = $portefeuille['Client']." / ".$portefeuille['Portefeuille']." / ".$portefeuille['Depotbank'];

			$this->pdf->AddPage();
			$this->pdf->templateVars[$this->pdf->rapport_type.'Paginas'] = $this->pdf->customPageNo;
			$this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas'] = $this->pdf->rapport_titel;
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$uitsluitingen=bepaalModelUitsluitingen($this->portefeuille,$einddatum);
      foreach($uitsluitingen['portefeuilleRegels'] as $row)
        $this->pdf->Row($row);

			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
								 " rapportageDatum = '".$einddatum."' AND ".
								 " portefeuille = '".$portefeuille['Portefeuille']."' AND type <> 'rente' "
								 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query,__FILE__,__LINE__);
			$DB3 = new DB();
			$DB3->SQL($query);
			$DB3->Query();
			$portefwaarde = $DB3->nextRecord();
   
      if($norm['norm'] <> '')
 	      $portefwaarde['totaal']=$portefwaarde['totaal']*($norm['norm']/100);

			$portefTotaal = $portefwaarde['totaal'];
      
      if($this->selectData["modelcontrole_rapport"] == "vastbedrag")
			{
				$portefTotaal = $this->selectData["modelcontrole_vastbedrag"];
			}

			if($this->selectData['modelcontrole_percentage'] > 0)
			{
				$afwijking = " HAVING ABS(afwijking) > ".$this->selectData['modelcontrole_percentage']." ";
			}

			if($this->selectData['modelcontrole_uitvoer'] == "afwijkingen")
			{
				$afwijking = " HAVING afwijking <> 0 ";
			}

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
			Fondsen.ISINCode as ISIN,
			TijdelijkeRapportage.Valuta,
			TijdelijkeRapportage.fondsOmschrijving,
			TijdelijkeRapportage.actueleFonds,
			TijdelijkeRapportage.actueleValuta,
			TijdelijkeRapportage.fondsEenheid
			FROM TijdelijkeRapportage
			LEFT JOIN Fondsen ON TijdelijkeRapportage.Fonds=Fondsen.Fonds
			LEFT JOIN TijdelijkeRapportage AS model ON model.fonds = TijdelijkeRapportage.fonds AND model.portefeuille = \""."m".$this->selectData['modelcontrole_portefeuille']."\" 
           AND model.type = 'fondsen'  AND model.rapportageDatum = '".$einddatum."'"
          .str_replace("TijdelijkeRapportage",'model',$__appvar['TijdelijkeRapportageMaakUniek'])."
			LEFT JOIN TijdelijkeRapportage AS portef ON portef.fonds = TijdelijkeRapportage.fonds AND portef.portefeuille = \"".$portefeuille['Portefeuille']."\" 
           AND portef.type = 'fondsen'  AND portef.rapportageDatum = '".$einddatum."'"
          .str_replace("TijdelijkeRapportage",'portef',$__appvar['TijdelijkeRapportageMaakUniek'])."
			WHERE
			TijdelijkeRapportage.type = 'fondsen' AND
			TijdelijkeRapportage.rapportageDatum = '".$einddatum."' AND
			(TijdelijkeRapportage.portefeuille = \"".$portefeuille['Portefeuille']."\" OR TijdelijkeRapportage.portefeuille = \""."m".$this->selectData['modelcontrole_portefeuille']."\")  "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.".$this->selectData['modelcontrole_level']." ".$afwijking."
			ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde, afwijking DESC ";
			debugSpecial($query,__FILE__,__LINE__);

			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();

			while($fdata = $DB2->nextRecord())
			{
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

	
				$data = array($fdata['fondsOmschrijving'],$fdata['ISIN'],$fdata['Valuta'],
											$this->formatGetal($fdata['percentageModel'],1),
											$this->formatGetal($fdata['percentagePortefeuille'],1),
											$this->formatGetal($fdata['afwijking'],1),
											$this->formatGetal($aankoopStuks,0),
											$this->formatGetal($verkoopStuks,0),
											$this->formatGetal($aankoopWaarde,2),
											"",
											$this->formatGetal($waardeVolgensModel,2),
											$this->formatGetal($fdata['actueleFonds'],2));

        if($fill==true)
	      {
		      $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
		      $fill=false;
		    }
		    else
		    {
		      $this->pdf->fillCell=array();
		      $fill=true;
		    }
				$this->pdf->Row($data);

			  if(round($fdata['afwijking'],2) <> 0.00 && ($aankoopStuks <> 0 || $verkoopStuks <> 0))
			  {
			    $this->orderData[]=array('fonds'=>$fdata['fonds'],'modelPercentage'=>$fdata['percentageModel'],'portefeuillePercentage'=>$fdata['percentagePortefeuille'],
			                             'afwijking'=>$fdata['afwijking'],'kopen'=>$aankoopStuks,'verkopen'=>$verkoopStuks,'overschrijding'=>$aankoopWaarde,'valuta'=>$fdata['valuta'],
			                             'modelWaarde'=>$waardeVolgensModel,'koers'=>$fdata['actueleFonds'],'portefeuille'=>$portefeuille['Portefeuille']);
			  }
			}



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
			TijdelijkeRapportage.Valuta,
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
			GROUP BY TijdelijkeRapportage.fondsOmschrijving ".$afwijking."
			ORDER BY afwijking DESC ";
			debugSpecial($query,__FILE__,__LINE__);

			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();

			$totaalRekeningen = array();
      if(count($uitsluitingen['gecorigeerdeRekeningen'])>0)
        array_push($totaalRekeningen,array("Liquiditeiten",'','',"Model","Herziene waarde"));
      else
			array_push($totaalRekeningen,array("Liquiditeiten",'','',"Model","Huidige"));
			$this->pdf->Row(array(""));
			while($fdata = $DB2->nextRecord())
			{
				$aankoopWaarde 	= ((($portefTotaal) / 100) * $fdata['percentageModel']) - $fdata['portefeuilleWaarde'];
				$aankoopStuks 	= ($aankoopWaarde / ($fdata['actueleFonds'] * $fdata['actueleValuta']))  / $fdata['fondsEenheid'];
				$verkoopStuks = 0;
				$waardeVolgensModel = (($portefTotaal) / 100) * $fdata['percentageModel'];

				if ($fdata['portefeuilleWaarde'] != 0)
				{
				   $data = array("Effectenrekening ".$fdata['fondsOmschrijving'],$fdata['ISIN'],$fdata['Valuta'],
											$this->formatGetal($fdata['percentageModel'],1),
											$this->formatGetal($fdata['percentagePortefeuille'],1),
											$this->formatGetal($fdata['afwijking'],1),
											$this->formatGetal($aankoopStuks,0),
											$this->formatGetal($verkoopStuks,0),
											$this->formatGetal($aankoopWaarde,2),
											"",
											$this->formatGetal($waardeVolgensModel,2),
											$this->formatGetal($fdata['actueleValuta'],2));
           if($fill==true)
	         {
		         $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
		         $fill=false;
		       }
		       else
		       {
		         $this->pdf->fillCell=array();
		         $fill=true;
		       }
				   $this->pdf->Row($data);

				   $this->pdf->excelData[] = array("Effectenrekening ".$fdata['fondsOmschrijving'],$fdata['ISIN'],$fdata['Valuta'],
											round($fdata['percentageModel'],1),
											round($fdata['percentagePortefeuille'],1),
											round($fdata['afwijking'],1),
											round($aankoopStuks,0),
											round($verkoopStuks,0),
											round($aankoopWaarde,2),
											"",
											round($waardeVolgensModel,2),
											round($fdata['actueleValuta'],2),
											round($fdata['portefeuilleWaarde'],2)); //huidige waarde collom toegevoegd.

				   array_push($totaalRekeningen,array("Effectenrekening ".$fdata['fondsOmschrijving'],'','',
				    								  $this->formatGetal($waardeVolgensModel,2),
				   									  $this->formatGetal($fdata['portefeuilleWaarde'],2)));
				}
			}
			$this->pdf->ln();
			unset($this->pdf->fillCell);
 			foreach($totaalRekeningen as $rekeningRow)
 			{
 			   $this->pdf->Row($rekeningRow);
 			}

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
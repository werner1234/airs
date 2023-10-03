<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/07 07:18:10 $
File Versie					: $Revision: 1.14 $

$Log: RapportMODEL_L40.php,v $
Revision 1.14  2020/05/07 07:18:10  rvv
*** empty log message ***

Revision 1.13  2020/05/06 17:10:29  rvv
*** empty log message ***

Revision 1.12  2020/03/11 16:21:41  rvv
*** empty log message ***

Revision 1.11  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.10  2018/04/21 17:56:04  rvv
*** empty log message ***

Revision 1.9  2017/10/02 14:34:49  rvv
*** empty log message ***

Revision 1.8  2015/10/07 19:38:52  rvv
*** empty log message ***

Revision 1.7  2015/04/04 15:15:15  rvv
*** empty log message ***

Revision 1.6  2013/12/21 18:31:54  rvv
*** empty log message ***

Revision 1.5  2013/12/18 17:10:42  rvv
*** empty log message ***

Revision 1.4  2013/12/14 17:16:30  rvv
*** empty log message ***

Revision 1.3  2013/12/11 17:13:16  rvv
*** empty log message ***

Revision 1.2  2013/12/07 17:51:24  rvv
*** empty log message ***

Revision 1.1  2013/11/02 17:04:05  rvv
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
include_once($__appvar["basedir"]."/html/rapport/include/RapportOIS_L40.php");

class RapportModel_L40
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function RapportModel_L40($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
    $this->rapportageDatumVanaf=$rapportageDatumVanaf;
    $this->rapportageDatum=$rapportageDatum;
		$this->pdf->excelData 	= array();

		$this->pdf->rapport_type = "MODEL";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;
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


	function writeRapport()
	{
		global $__appvar;

		$DB = new DB();
	  $this->selectData['modelcontrole_level']='Fonds';
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
						 " Clienten.Naam,
               ModelPortefeuilles.AfdrukNiveau,
               ModelPortefeuilles.Beleggingscategorie as modelCategorie ".
					 " FROM (Portefeuilles, Clienten)
           LEFT JOIN ModelPortefeuilles ON Portefeuilles.ModelPortefeuille = ModelPortefeuilles.Portefeuille
              WHERE ".
					 " Portefeuilles.Client = Clienten.Client ".$extraquery;

		$DB->SQL($q);
		$DB->Query();
		$records = $DB->records();

		while($portefeuille = $DB->NextRecord())
		{
		  if($portefeuille['AfdrukNiveau'] <> '')
        $this->selectData['modelcontrole_level']=$portefeuille['AfdrukNiveau'];
      
      $this->modelCategorie=$portefeuille['modelCategorie'];
      $portefeuilleWaardeFilter="";
   		if($this->selectData['modelcontrole_level'] != 'Fonds')
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
      {
        echo "Modelwaarde is 0, rapport afgebroken.";
        exit;
      }


      if($this->selectData['modelcontrole_level'] == 'beleggingssector')
      {
        $query="SELECT beleggingssector FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$einddatum."' AND ".
							 " portefeuille = '"."m".$portefeuille['ModelPortefeuille']."' AND type <> 'rente' $extraCategorieFilter";
        $DB3->SQL($query);
	  	  $DB3->Query();
        $modelSectoren=array();
		    while($tmp = $DB3->nextRecord())
        {
          $modelSectoren[$tmp['beleggingssector']]=$tmp['beleggingssector'];
        }
        $restFilter="AND (TijdelijkeRapportage.Beleggingssector NOT IN('".implode("','",$modelSectoren) ."')) ";
      }
      elseif($this->selectData['modelcontrole_level'] == 'Regio')
      {
         $query="SELECT regio FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$einddatum."' AND ".
							 " portefeuille = '"."m".$portefeuille['ModelPortefeuille']."' AND type <> 'rente' $extraCategorieFilter";
        $DB3->SQL($query);
	  	  $DB3->Query();
        $modelRegios=array();
		    while($tmp = $DB3->nextRecord())
        {
          $modelRegios[$tmp['regio']]=$tmp['regio'];
        }
        $restFilter="AND (TijdelijkeRapportage.Regio NOT IN('".implode("','",$modelRegios) ."')) ";
      }
      
      if($modelType['Beleggingscategorie'] <> '')
      {
       // $restFilter=" AND TijdelijkeRapportage.Beleggingscategorie <> '".$modelType['Beleggingscategorie']."' ";

      }
      $query = "SELECT norm FROM NormPerRisicoprofiel WHERE Risicoklasse='".$portefeuille['Risicoklasse']."'
 	                                                     AND Vermogensbeheerder='".$portefeuille['Vermogensbeheerder']."'
 	                                                     AND Beleggingscategorie='".$modelType['Beleggingscategorie']."'";
 	    $DB3->SQL($query);
 	    $DB3->Query();
 	    $norm = $DB3->nextRecord();
      
      $query = "SELECT Omschrijving FROM Beleggingscategorien WHERE Beleggingscategorie='".$modelType['Beleggingscategorie']."'";
     $DB->SQL($query);
      $DB->Query();
     $tmp=$DB->nextRecord(); 
     $tmp[$modelType['Beleggingscategorie']]=$tmp['Omschrijving'];
     $modelTotaalWaarden=array();  
     $modelTotaalWaarden[$tmp[$modelType['Beleggingscategorie']]]['percentageModel']=$norm['norm'];

 	 
			if($this->selectData["modelcontrole_rapport"] == "vastbedrag")
			{
				$portefeuille = array();
			}
			// set pdf vars
			$this->pdf->naamOmschrijving = $portefeuille['Naam'];
			$this->pdf->clientOmschrijving = $portefeuille['Client']." / ".$portefeuille['Portefeuille']." / ".$portefeuille['Depotbank'];
      
      
      $uitsluitingen=bepaalModelUitsluitingen($this->portefeuille,$einddatum);

      $DB3 = new DB();
			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
								 " rapportageDatum = '".$einddatum."' AND ".
								 " portefeuille = '".$portefeuille['Portefeuille']."' AND type <> 'rente'  "
								 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query,__FILE__,__LINE__);
			$DB3->SQL($query);// echo $query;exit;
			$DB3->Query();
			$tmp = $DB3->nextRecord();
      $portefwaarde['helePortefeuille']=$tmp['totaal'];
  
  		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
								 " rapportageDatum = '".$einddatum."' AND ".
								 " portefeuille = '".$portefeuille['Portefeuille']."' AND type <> 'rente' $extraCategorieFilter "
								 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query,__FILE__,__LINE__);
			$DB3->SQL($query);
			$DB3->Query();
			$tmp = $DB3->nextRecord();
      $portefwaarde['categorie']=$tmp['totaal'];
      
      
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
								 " rapportageDatum = '".$einddatum."' AND ".
								 " portefeuille = '".$portefeuille['Portefeuille']."' AND type <> 'rente' $extraCategorieFilter $restFilter "
								 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query,__FILE__,__LINE__);
			$DB3 = new DB();
			$DB3->SQL($query);
			$DB3->Query();
			$restwaarde = $DB3->nextRecord();
      $portefwaarde['restwaarde']=$restwaarde['totaal'];
 
      if($norm['norm'] <> '')
 	      $portefwaarde['totaalNorm']=($portefwaarde['helePortefeuille'])*($norm['norm']/100)-$restwaarde['totaal'];
      else
        $portefwaarde['totaalNorm']=0;
    
   
    $portefwaarde['categorieCorrected']=$portefwaarde['categorie']-$restwaarde['totaal'];
   // listarray($portefwaarde);
      //echo  $portefwaarde['totaal']."=(".$portefwaarde['totaal'].")*(".$norm['norm']."/100)-".$restwaarde['totaal']."<br>\n";
      //

			$portefTotaal = $portefwaarde['totaalNorm'];
       
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
  		SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,portef.actuelePortefeuilleWaardeEuro,0)) AS portefeuilleWaarde,
      TijdelijkeRapportage.".$this->selectData['modelcontrole_level']." as RegelOmschrijving,
			TijdelijkeRapportage.fonds,
      max(TijdelijkeRapportage.BeleggingscategorieOmschrijving) as BeleggingscategorieOmschrijving,
			TijdelijkeRapportage.fondsOmschrijving,
			TijdelijkeRapportage.actueleFonds,
			TijdelijkeRapportage.actueleValuta,
			TijdelijkeRapportage.fondsEenheid
			FROM TijdelijkeRapportage
			LEFT JOIN TijdelijkeRapportage AS model ON model.fonds = TijdelijkeRapportage.fonds AND model.portefeuille = \""."m".$this->selectData['modelcontrole_portefeuille']."\" 
           AND model.type = 'fondsen'  AND model.rapportageDatum = '".$einddatum."'"
           .str_replace("TijdelijkeRapportage",'model',$extraCategorieFilter)." "
           .str_replace("TijdelijkeRapportage",'model',$__appvar['TijdelijkeRapportageMaakUniek'])."
			LEFT JOIN TijdelijkeRapportage AS portef ON portef.fonds = TijdelijkeRapportage.fonds AND portef.portefeuille = \"".$portefeuille['Portefeuille']."\" 
           AND portef.type = 'fondsen'  AND portef.rapportageDatum = '".$einddatum."'"
          .str_replace("TijdelijkeRapportage",'portef',$extraCategorieFilter)." "
          .str_replace("TijdelijkeRapportage",'portef',$__appvar['TijdelijkeRapportageMaakUniek'])."
			WHERE
			TijdelijkeRapportage.type = 'fondsen' AND
			TijdelijkeRapportage.rapportageDatum = '".$einddatum."' AND
			(TijdelijkeRapportage.portefeuille = \"".$portefeuille['Portefeuille']."\" OR TijdelijkeRapportage.portefeuille = \""."m".$this->selectData['modelcontrole_portefeuille']."\")  
      $extraCategorieFilter "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.".$this->selectData['modelcontrole_level']." ".$afwijking."
			 ";
      //echo $query;exit;
			debugSpecial($query,__FILE__,__LINE__);

			$DB2 = new DB();
			$DB2->SQL($query);//echo $query;exit;
			$DB2->Query();

			while($fdata = $DB2->nextRecord())
			{
			  $aankoopStuks=0;
			  $verkoopStuks=0;
        
        $fdata['percentageModel']=$fdata['modelWaarde']/$modelTotaal*100;

        $fdata['percentagePortefeuilleNorm']=$fdata['portefeuilleWaarde']/$portefwaarde['totaalNorm']*100;
        $fdata['afwijkingNorm']=$fdata['percentageModel']-$fdata['percentagePortefeuilleNorm'];
        
        
       // echo $fdata['RegelOmschrijving']." ". $fdata['percentagePortefeuilleNorm']."= ".$fdata['portefeuilleWaarde']."/".$portefwaarde['totaalNorm']."*100<br>\n";
        
        $fdata['percentagePortefeuilleExclNorm']=$fdata['portefeuilleWaarde']/$portefwaarde['categorieCorrected']*100;
        $fdata['afwijkingExclNorm']=$fdata['percentageModel']-$fdata['percentagePortefeuilleExclNorm'];

				$aankoopWaardeNorm 	= ((($portefwaarde['totaalNorm']) / 100) * $fdata['percentageModel']) - $fdata['portefeuilleWaarde'];
        $aankoopWaardeExclNorm 	= ((($portefwaarde['categorieCorrected']) / 100) * $fdata['percentageModel']) - $fdata['portefeuilleWaarde'];
      	$aankoopStuksNorm 	= round(($aankoopWaardeNorm / ($fdata['actueleFonds'] * $fdata['actueleValuta']))  / $fdata['fondsEenheid'],4);
				if($fdata['fondsEenheid'] == '0.01')
		    {
		      $aankoopStuks=floor($aankoopStuks/100)*100;
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

			//	$waardeVolgensModel = (($portefTotaal) / 100) * $fdata['percentageModel'];

				if($this->selectData['modelcontrole_level'] != 'Fonds')
				{
				  $fdata['fondsOmschrijving']=$omschrijving[$fdata['RegelOmschrijving']];
          if($fdata['fondsOmschrijving']=='')
            $fdata['fondsOmschrijving']='Geen '.$this->selectData['modelcontrole_level'];
				  $fdata['actueleFonds']=0;
				  $aankoopStuks=0;
				  $verkoopStuks=0;
				}

          if($aankoopWaarde > 0)
            $aankoopStuks=$aankoopWaarde;
          else
            $verkoopStuks=$aankoopWaarde*-1;
            
    if($fdata['percentageModel']==0)
    {
      $fdata['percentagePortefeuille']=0;
      $fdata['afwijking']=0;
      $aankoopStuks=0;
      $verkoopStuks=0;
      $aankoopWaarde=0;
    }

    $modelData[$fdata['fondsOmschrijving']]=array('percentageModel'=>$fdata['percentageModel'],
                                                  'percentagePortefeuilleExclNorm'=>$fdata['percentagePortefeuilleExclNorm'],
                                                  'afwijkingExclNorm'=>$fdata['afwijkingExclNorm'],
                                                  'mutatieExclNorm'=>$aankoopWaardeExclNorm,
                                                  'percentagePortefeuilleNorm'=>$fdata['percentagePortefeuilleNorm'],
                                                  'afwijkingNorm'=>$fdata['afwijkingNorm'],
                                                  'mutatieNorm'=>$aankoopWaardeNorm,
                                                  'aankoop'=>$aankoopStuks,
                                                  'verkoop'=>$verkoopStuks,
                                                  'aankoopWaarde'=>$aankoopWaarde,
                                                  'waardeVolgensModel'=>$waardeVolgensModel,
                                                  'actueleFonds'=>$fdata['actueleFonds']);

      $modelTotaalWaarden[$fdata['BeleggingscategorieOmschrijving']]['percentagePortefeuille']+=$fdata['portefeuilleWaarde']/$portefwaarde['helePortefeuille']*100 ;

			}



			$query = "SELECT
			SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->selectData['modelcontrole_portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0))  AS modelWaarde,
   		SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) AS portefeuilleWaarde,
      max(TijdelijkeRapportage.BeleggingscategorieOmschrijving) AS BeleggingscategorieOmschrijving,
			TijdelijkeRapportage.fonds,
      TijdelijkeRapportage.rekening,
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
			 ";
			debugSpecial($query,__FILE__,__LINE__);

			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();

			$totaalRekeningen = array();
			array_push($totaalRekeningen,array("Liquiditeiten","Model waarde","Huidige waarde"));
			$this->pdf->Row(array(""));
			while($fdata = $DB2->nextRecord())
			{
			  $fdata['percentageModel']=$fdata['modelWaarde']/$modelTotaal*100;
        $fdata['percentagePortefeuille']=$fdata['portefeuilleWaarde']/$portefwaarde['totaalNorm']*100;
        $fdata['afwijking']=$fdata['percentageModel']-$fdata['percentagePortefeuille'];
        if($_POST['anoniem']!=1)
	        $fdata['fondsOmschrijving'].=" ".ereg_replace("[^0-9]","",$fdata['rekening']);
          
				$aankoopWaarde 	= ((($portefTotaal) / 100) * $fdata['percentageModel']) - $fdata['portefeuilleWaarde'];
				$aankoopStuks 	= ($aankoopWaarde / ($fdata['actueleFonds'] * $fdata['actueleValuta']))  / $fdata['fondsEenheid'];
				$verkoopStuks = 0;
				$waardeVolgensModel = (($portefTotaal) / 100) * $fdata['percentageModel'];

				if ($fdata['portefeuilleWaarde'] != 0)
				{
          if($aankoopWaarde > 0)
            $aankoopStuks=$aankoopWaarde;
          else
            $verkoopStuks=$aankoopWaarde*-1;
            
            
        if($fdata['percentageModel']==0)
        {
          $fdata['percentagePortefeuille']=0;
          $fdata['afwijking']=0;
          $aankoopStuks=0;
          $verkoopStuks=0;
          $aankoopWaarde=0;
        }
         
          $modelData[$fdata['fondsOmschrijving']]=array('percentageModel'=>$fdata['percentageModel'],
                                                  'percentagePortefeuille'=>$fdata['percentagePortefeuille'],
                                                  'afwijking'=>$fdata['afwijking'],
                                                  'aankoop'=>$aankoopStuks,
                                                  'verkoop'=>$verkoopStuks,
                                                  'aankoopWaarde'=>$aankoopWaarde,
                                                  'waardeVolgensModel'=>$waardeVolgensModel,
                                                  'actueleFonds'=>$fdata['actueleFonds']);
                                                  

     $modelTotaalWaarden[$fdata['BeleggingscategorieOmschrijving']]['percentagePortefeuille']+=$fdata['portefeuilleWaarde']/$portefwaarde['helePortefeuille']*100 ;
                          
                                                  

				}
			}
		}
    // listarray($modelTotaalWaarden);

    $this->rapport = new rapportOIS_L40($this->pdf, $this->portefeuille, $rapportageDatumVanaf, $this->rapportageDatum);
    $this->rapport->verdeling=$this->selectData['modelcontrole_level'];
    $this->rapport->pdf->modelLayout=true;
    $this->rapport->modelData=$modelData;
    $this->rapport->modelUitsluitingen=$uitsluitingen;
    $this->rapport->modelTotaal=$modelTotaalWaarden;
    $this->rapport->modelCategorie=$this->modelCategorie;
    $this->rapport->writeRapport();
    $this->rapport->pdf->modelLayout=false;

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
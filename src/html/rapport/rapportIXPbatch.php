<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/11/16 17:36:34 $
 		File Versie					: $Revision: 1.7 $

 		$Log: rapportIXPbatch.php,v $
 		Revision 1.7  2019/11/16 17:36:34  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2016/10/29 15:38:06  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2015/12/16 17:04:53  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2015/11/24 11:57:27  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2015/11/22 14:30:47  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2015/11/18 17:06:10  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/11/14 13:25:54  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2015/10/28 16:41:18  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2015/10/25 13:25:49  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2015/09/13 11:30:48  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2014/11/15 19:05:41  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2014/07/12 15:29:41  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2014/04/05 15:33:11  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2014/03/29 16:22:08  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2014/02/28 16:39:56  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2013/10/13 13:16:46  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2013/10/12 15:50:29  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2013/08/28 16:02:00  rvv
 		*** empty log message ***
 		
 

*/

include_once("rapportRekenClass.php");

class rapportIXPbatch
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function rapportIXPbatch( $selectData )
	{
	  $this->pdf = new PDFRapport('L','mm');
	  $this->selectData = $selectData;
		$this->pdf->excelData = array();
		$this->orderby  = " Client ";
		$this->pdf->excelData = array();
    $this->pdf->excelOpmaak['getal']=array('setNumFormat'=>'2');

	}


	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		global $__appvar;
		$einddatum = jul2sql($this->selectData['datumTm']);
		$this->einddatum = $einddatum;
    $rapportageDatumVanaf = jul2sql($this->selectData['datumVan']);

		$fondswaardenClean = array();
		$fondswaardenRente = array();
		$rekeningwaarden 	 = array();

		$jaar = date("Y",$this->selectData['datumTm']);

    $selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();

		if($records <= 0)
		{
			echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
			$this->progressbar->hide();
			exit;
		}

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / $records;
		}

		$a=0;
		$lossePortefeuilles=array();
		foreach($portefeuilles as $portData) //Portefeuille data in array laden
		{
	   	$pdata[$a]=$portData;
    	$lossePortefeuilles[]=$portData['Portefeuille'];
 	 	  $a++;
		}
$DB = new DB();

if($this->selectData['invoer'] == 'alles')
{
  $this->liquiditeitenPercentage = true;
  $this->Rente=true;
}
if($this->selectData['percentages']=='aantal')
{
  $this->Rente=false;
  $this->liquiditeitenPercentage=false;
}

if($this->selectData['uitvoer'] == 'valuta' || $this->selectData['uitvoer'] == 'afm')
{
  $this->liquiditeitenPercentage = false;
  $this->Rente=false;
}

$kopItems = array();

if ($this->selectData['uitvoer'] == 'alles')
{
  $kopItems['hoofdCategorien']= array();
  $kopItems['categorien']= array();
  $kopItems['regios']= array();
  $kopItems['hoofdSectoren']= array();
  $kopItems['sectoren']= array();
  $kopItems['valutas']= array();
  $kopItems['instrumenten']= array();
}
else
{
  $kopItems[$this->selectData['uitvoer']] = array();
}


$queries['hoofdCategorien']= "SELECT
                            Beleggingscategorien.Beleggingscategorie as type,
                            Beleggingscategorien.Omschrijving
                            FROM Beleggingscategorien, BeleggingscategoriePerFonds,CategorienPerHoofdcategorie
                            WHERE
                            Beleggingscategorien.Beleggingscategorie =  CategorienPerHoofdcategorie.Hoofdcategorie
                            GROUP BY Beleggingscategorien.Beleggingscategorie";

$queries['categorien']     = "SELECT 	Beleggingscategorien.Beleggingscategorie as type,
						                Beleggingscategorien.Omschrijving
				                    FROM 	Beleggingscategorien,
						                BeleggingscategoriePerFonds
				                    WHERE 	Beleggingscategorien.Beleggingscategorie = BeleggingscategoriePerFonds.Beleggingscategorie
 				                    GROUP BY Beleggingscategorien.Beleggingscategorie";

$queries['regios']         = "SELECT 	Regios.Regio as type,
						                Regios.Omschrijving
				                    FROM 	Regios,
						                BeleggingssectorPerFonds
				                    WHERE 	Regios.Regio = BeleggingssectorPerFonds.Regio
 				                    GROUP BY Regios.Regio";

$queries['hoofdSectoren']  = "SELECT 		SectorenPerHoofdsector.Hoofdsector as type,
 			                      Beleggingssectoren.Omschrijving
			                      FROM
			                      SectorenPerHoofdsector,
			                      Beleggingssectoren
			                      WHERE SectorenPerHoofdsector.Hoofdsector = Beleggingssectoren.Beleggingssector
			                      GROUP BY Hoofdsector";
/*
$queries['sectoren']       = "SELECT Beleggingssectoren.Beleggingssector as type ,
						                Beleggingssectoren.Omschrijving
		 		                    FROM 	Beleggingssectoren 
                            JOIN BeleggingssectorPerFonds ON Beleggingssectoren.Beleggingssector=BeleggingssectorPerFonds.Beleggingssector
                            JOIN Portefeuilles ON BeleggingssectorPerFonds.Vermogensbeheerder=Portefeuilles.Vermogensbeheerder AND Portefeuilles.Portefeuille IN('".implode("','",$lossePortefeuilles)."')
						                WHERE 1
						                GROUP BY Beleggingssectoren.Beleggingssector ";
*/                            
$queries['sectoren']       = "SELECT sectoren.type,sectoren.Omschrijving FROM (
(
SELECT Beleggingssectoren.Beleggingssector as type , Beleggingssectoren.Omschrijving 
FROM Beleggingssectoren 
JOIN BeleggingssectorPerFonds ON Beleggingssectoren.Beleggingssector=BeleggingssectorPerFonds.Beleggingssector 
JOIN Portefeuilles ON BeleggingssectorPerFonds.Vermogensbeheerder=Portefeuilles.Vermogensbeheerder
 AND Portefeuilles.Portefeuille IN('".implode("','",$lossePortefeuilles)."')
WHERE 1 GROUP BY Beleggingssectoren.Beleggingssector
)
UNION 
(
SELECT Fondsen.standaardSector,Beleggingssectoren.Omschrijving 
FROM Fondsen 
JOIN Beleggingssectoren ON Fondsen.standaardSector=Beleggingssectoren.Beleggingssector
GROUP BY Fondsen.standaardSector
) ) as sectoren ORDER BY sectoren.type ";


$queries['valuta']        = "SELECT Fondsen.Valuta type ,
                            Valutas.Omschrijving
                            FROM Fondsen
                            JOIN Valutas ON Fondsen.Valuta = Valutas.Valuta
                            WHERE Fondsen.Valuta <> ''
                            GROUP BY Fondsen.Valuta
                            ORDER BY Omschrijving";

$queries['instrumenten']   = "SELECT Rekeningmutaties.Fonds as type,Fondsen.Omschrijving
                              FROM Rekeningmutaties
                              JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
                              JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.Portefeuille IN('".implode("','",$lossePortefeuilles)."')
                              JOIN Fondsen ON Rekeningmutaties.Fonds=Fondsen.Fonds
                              WHERE YEAR(Rekeningmutaties.Boekdatum) = '".$jaar."' AND Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum <= '".jul2db($this->selectData['datumTm'])."' AND
	                          	Rekeningmutaties.Grootboekrekening = 'FONDS'
		                          GROUP BY Rekeningmutaties.Fonds ORDER BY Rekeningmutaties.Fonds ";
                              
$queries['afm'] = "SELECT afmCategorien.afmCategorie as type ,
						                afmCategorien.Omschrijving
		 		                    FROM 	afmCategorien 
                            JOIN BeleggingscategoriePerFonds ON afmCategorien.afmCategorie=BeleggingscategoriePerFonds.afmCategorie
						                GROUP BY afmCategorien.afmCategorie";

$aantalkopjes =  14;

$this->pdf->excelData[] = array("Portefeuille",'SoortOvereenkomst',
								 "Einddatum",
								 "Risicoprofiel",
								 "Depotbank",
                 "Beginvermogen","Stortingen","Onttrekkingen","Resultaat","Eindvermogen",
                 'Gem vermogen',
                 'PercBegin',
								 'Performance',
								 'Kosten EUR',
								 'KostenPerc'
								 );



while (list($item, $data) = each($kopItems))
{
  $kopItems[$item] = $this->getKopLabels($queries[$item],$item);
  $aantalkopjes += count($kopItems[$item]);
	while (list($groep, $omschrijving) = each($kopItems[$item])) //Voeg beleggingscategorien toe aan header csv.
	{
  	array_push($this->pdf->excelData['0'], $omschrijving['Omschrijving']);
    //logScherm($omschrijving['Omschrijving']);
	}
}



	 	$j=1;  //regelnummer
		for($i=0; $i < count($pdata);$i++)
		{
		$totaalWaarde = 0;
		$percentageHoofdcategorien=array();
		$percentageBeleggingscategorien=array();
		$percentageRegio=array();
		$percentageHoofdSector=array();
		$percentageSector=array();
		$percentageInstrumenten=array();

		$portefeuille = $pdata[$i]['Portefeuille'];
			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
				logScherm("Portefeuille: ".$portefeuille);
			}

if($this->selectData['invoer'] == 'alles')
{
  $invoerWhere = '';
  $invoerTables = '';
}
elseif ($this->selectData['typeInvoer'] == 'H-cat')
{
  $invoerWhere  = " AND TijdelijkeRapportage.Beleggingscategorie =  CategorienPerHoofdcategorie.Beleggingscategorie
                    AND CategorienPerHoofdcategorie.Hoofdcategorie = '".$this->selectData['invoer']."'
                    AND CategorienPerHoofdcategorie.Vermogensbeheerder = '$vermogensbeheerder' ";
  $invoerTables = ', CategorienPerHoofdcategorie';

  if($this->selectData['uitvoer'] == 'hoofdCategorien' )
    $resetInvoerTables = true;

}
elseif ($this->selectData['typeInvoer'] == 'cat')
{
 $invoerWhere   = " AND TijdelijkeRapportage.beleggingscategorie = '".$this->selectData['invoer']."' ";
 $invoerTables  = '';
}
elseif ($this->selectData['typeInvoer'] == 'H-sec')
{
  $invoerWhere  = " AND TijdelijkeRapportage.Beleggingssector = SectorenPerHoofdsector.Beleggingssector
                    AND SectorenPerHoofdsector.Hoofdsector = '".$this->selectData['invoer']."'
                    AND SectorenPerHoofdsector.Vermogensbeheerder = '$vermogensbeheerder' ";
  $invoerTables = ', SectorenPerHoofdsector';

  if($this->selectData['uitvoer'] == 'hoofdSectoren' || $this->selectData['uitvoer'] == 'Beleggingssector')
    $resetInvoerTables = true;


}
elseif ($this->selectData['typeInvoer'] == 'sec')
{
 $invoerWhere   = " AND TijdelijkeRapportage.beleggingssector = '".$this->selectData['invoer']."' ";
 $invoerTables  = '';
}
elseif ($this->selectData['typeInvoer'] == 'regio')
{
 $invoerWhere   = " AND TijdelijkeRapportage.Regio = '".$this->selectData['invoer']."' ";
 $invoerTables  = '';
}
elseif ($this->selectData['typeInvoer'] == 'valuta')
{
 $invoerWhere   = " AND TijdelijkeRapportage.Valuta = '".$this->selectData['invoer']."' ";
 $invoerTables  = '';
}
elseif ($this->selectData['typeInvoer'] == 'afm')
{
 $invoerWhere   = " AND TijdelijkeRapportage.afmCategorie = '".$this->selectData['invoer']."' ";
 $invoerTables  = '';
}
else // ($this->selectData['typeInvoer'] == 'alles')
{
  $invoerWhere = '';
  $invoerTables = '';
}


		$this->portefeuille = $portefeuille;
		$vermogensbeheerder =  $pdata[$i]['Vermogensbeheerder'];
    if(db2jul($rapportageDatumVanaf) < db2jul($pdata[$i]['Startdatum']))
		{
			$startdatum = substr($pdata[$i]['Startdatum'],0,10);
		}
		else
		{
			$startdatum = $rapportageDatumVanaf;
		}



    $julrapport 		= db2jul($startdatum);
		$rapportMaand 	= date("m",$julrapport);
		$rapportDag 		= date("d",$julrapport);

		if($rapportMaand == 1 && $rapportDag == 1)
			$startjaar = true;
		else
			$startjaar = false;

//vul tijdelijke tabel.
	

		$fondswaarden =  berekenPortefeuilleWaarde($portefeuille, $einddatum);
		vulTijdelijkeTabel($fondswaarden,$portefeuille,$einddatum);

		$fondswaarden =  berekenPortefeuilleWaarde($portefeuille, $startdatum, $startjaar);
    vulTijdelijkeTabel($fondswaarden,$portefeuille,$startdatum);
    
    
    $waarden=array();
		foreach($fondswaarden as $regel)
        $waarden['begin']+=$regel['actuelePortefeuilleWaardeEuro'];
        
// Bereken totaalwaarde portefeuille
    $rapportageDatum = $startdatum;
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage $invoerTables WHERE ".
						 " rapportageDatum ='".$einddatum."' AND ".
						 " portefeuille = '".$portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'] . $invoerWhere;
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde[totaal];
    $waarden['eind'] = $totaalWaarde;

		if ($resetInvoerTables == true)
  		$invoerTables ='';
		if ($addtable != '')
		  $invoerTables .= $addtable;

//perf
      if($pdata[$i]['RapportageValuta'] =='')
        $pdata[$i]['RapportageValuta']='EUR';
			$performance  = performanceMeting($pdata[$i]['Portefeuille'], $startdatum, $einddatum, $pdata[$i]['PerformanceBerekening'], $pdata[$i]['RapportageValuta']);
/// eind perf

//verloop

      //if(db2jul($pdata[$i]['Einddatum'])< time() && db2jul($pdata[$i]['Einddatum']) > db2jul($startdatum))
      //  $einddatum=substr($pdata[$i]['Einddatum'],0,10);
      
   
      if(round($waarden['begin'],4) <> 0.00 || round($waarden['eind'],4) <> 0.00)
      {
        $waarden['storting']=getStortingen($portefeuille,$startdatum,$einddatum);
        $waarden['onttrekking']=getOnttrekkingen($portefeuille,$startdatum,$einddatum);
  
        if(db2jul($pdata[$i]['Einddatum'])< db2jul($einddatum))
        {
          if($waarden['eind'] <> 0)
          {
            $waarden['onttrekking']=+$waarden['eind'];
            $waarden['eind']=0;
            $pdata[$i]['naam']=$pdata[$i]['naam']."*";
          }
        } 
        $waarden['resultaat']=$waarden['eind']-$waarden['begin']+$waarden['onttrekking']-$waarden['storting'];

        foreach($waarden as $key=>$value)
          $totalen[$key]+=$value;
      



       }
// eind verloop

      
      $query="SELECT
Sum(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers) AS totaalcredit,
Sum(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) AS totaaldebet
FROM 
Rekeningmutaties
JOIN Rekeningen ON  Rekeningmutaties.Rekening = Rekeningen.Rekening   
WHERE Rekeningen.Portefeuille = '".$pdata[$i]['Portefeuille']."' AND Rekeningmutaties.Verwerkt = '1' AND 
Rekeningmutaties.Boekdatum > '".$startdatum."'  AND Rekeningmutaties.Boekdatum <= '".$einddatum."'
AND Rekeningmutaties.Grootboekrekening IN ('BEH', 'BEW', 'KNBA', 'KOBU', 'KOST')";
		$DB->SQL($query);
		$DB->Query();
		$kosten = $DB->nextRecord();
    $kostenEUR=$kosten['totaalcredit']-$kosten['totaaldebet'];

    $gemVermogen=($waarden['begin']+$waarden['eind'])/2;
	  $this->pdf->excelData[] = array($pdata[$i]['Portefeuille'],$pdata[$i]['SoortOvereenkomst'],
									 adodb_date("d-m-Y",adodb_db2jul($pdata[$i]['Einddatum'])), 
                   $pdata[$i]['Risicoklasse'],
                   $pdata[$i]['Depotbank'],
									 round($waarden['begin'],2),
									 round($waarden['storting'],2),
                   round($waarden['onttrekking'],2),
									 round($waarden['resultaat'],2),
                   round($waarden['eind'],2),
                   round($gemVermogen,2),
                   round($gemVermogen/$waarden['begin']*100,2),
                   round($performance,2),
                   round($kostenEUR,2),
                   round($kostenEUR/$gemVermogen*-100,4)
									 );
$rapportageDatum=$einddatum;
$percentageQuery['hoofdCategorien']		="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as WaardeEuro,
			                                  CategorienPerHoofdcategorie.Hoofdcategorie as type,
 			                                  Beleggingscategorien.Omschrijving
			                                  FROM
			                                  CategorienPerHoofdcategorie,
			                                  TijdelijkeRapportage,
			                                  Beleggingscategorien $invoerTables
			                                  WHERE TijdelijkeRapportage.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie
			                                  AND  Beleggingscategorien.Beleggingscategorie = TijdelijkeRapportage.Beleggingscategorie
			                                  AND rapportageDatum = '".$rapportageDatum."'
 			                                  AND Portefeuille = '".$portefeuille."'
 			                                  AND TijdelijkeRapportage.Type = 'Fondsen' "
			                                  .$__appvar['TijdelijkeRapportageMaakUniek']. $invoerWhere .
			                                  " AND CategorienPerHoofdcategorie.Vermogensbeheerder = '$vermogensbeheerder'".
			                                  " GROUP BY CategorienPerHoofdcategorie.Hoofdcategorie ORDER BY WaardeEuro desc;";

$percentageQuery['categorien']        ="SELECT TijdelijkeRapportage.beleggingscategorie as type ,sum(ActuelePortefeuilleWaardeEuro) AS WaardeEuro
	                                     FROM TijdelijkeRapportage $invoerTables
	                                     WHERE Portefeuille = '".$portefeuille."'
	                                     AND rapportageDatum ='".$rapportageDatum."' "
	                                     .$__appvar['TijdelijkeRapportageMaakUniek']. $invoerWhere .
	                                     "AND TijdelijkeRapportage.Type = 'Fondsen'
	                                     GROUP BY TijdelijkeRapportage.beleggingscategorie;";

$percentageQuery['regios']            ="SELECT Regio as type ,sum(ActuelePortefeuilleWaardeEuro) AS WaardeEuro
		                                    FROM TijdelijkeRapportage $invoerTables
		                                    WHERE rapportageDatum ='".$rapportageDatum."'
		                                    AND Portefeuille = '".$portefeuille."'
		                                    AND TijdelijkeRapportage.Type = 'Fondsen' "
		                                    .$__appvar['TijdelijkeRapportageMaakUniek']. $invoerWhere .
	                                     	" GROUP BY Regio";

$percentageQuery['hoofdSectoren']     ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as WaardeEuro,
			                                  SectorenPerHoofdsector.Hoofdsector as type,
 			                                  Beleggingssectoren.Omschrijving
			                                  FROM
			                                  SectorenPerHoofdsector,
			                                  TijdelijkeRapportage,
			                                  Beleggingssectoren $invoerTables
			                                  WHERE TijdelijkeRapportage.Beleggingssector = SectorenPerHoofdsector.Beleggingssector
			                                  AND  Beleggingssectoren.Beleggingssector = TijdelijkeRapportage.Beleggingssector
			                                  AND rapportageDatum = '".$rapportageDatum."'
 			                                  AND Portefeuille = '".$portefeuille."'
 			                                  AND TijdelijkeRapportage.Type = 'Fondsen' "
			                                  .$__appvar['TijdelijkeRapportageMaakUniek']. $invoerWhere .
			                                  " GROUP BY SectorenPerHoofdsector.Hoofdsector ORDER BY WaardeEuro desc;";

$percentageQuery['sectoren']          = "SELECT TijdelijkeRapportage.Beleggingssector as type ,sum(ActuelePortefeuilleWaardeEuro) AS WaardeEuro
	                                       FROM TijdelijkeRapportage $invoerTables
	                                       WHERE Portefeuille = '".$portefeuille."'
	                                       AND rapportageDatum ='".$rapportageDatum."'
	                                       AND TijdelijkeRapportage.Type = 'Fondsen' "
	                                       .$__appvar['TijdelijkeRapportageMaakUniek']. $invoerWhere .
	                                       " GROUP BY  TijdelijkeRapportage.Beleggingssector";

$percentageQuery['valuta']          = "SELECT TijdelijkeRapportage.Valuta as type ,sum(ActuelePortefeuilleWaardeEuro) AS WaardeEuro
	                                       FROM TijdelijkeRapportage $invoerTables
	                                       WHERE Portefeuille = '".$portefeuille."'
	                                       AND rapportageDatum ='".$rapportageDatum."' "
	                                       .$__appvar['TijdelijkeRapportageMaakUniek']. $invoerWhere .
	                                       " GROUP BY  TijdelijkeRapportage.Valuta";

$percentageQuery['afm']             = "SELECT TijdelijkeRapportage.afmCategorie as type ,sum(ActuelePortefeuilleWaardeEuro) AS WaardeEuro
	                                       FROM TijdelijkeRapportage $invoerTables
	                                       WHERE Portefeuille = '".$portefeuille."'
	                                       AND rapportageDatum ='".$rapportageDatum."' "
	                                       .$__appvar['TijdelijkeRapportageMaakUniek']. $invoerWhere .
	                                       " GROUP BY  TijdelijkeRapportage.afmCategorie";
                                                                                  
if($this->selectData['percentages']=='aantal')
  $waardeSelect="totaalAantal AS WaardeEuro";
else
  $waardeSelect="ActuelePortefeuilleWaardeEuro AS WaardeEuro";
  
$percentageQuery['instrumenten']		  ="SELECT fonds as type, $waardeSelect
	                                      FROM TijdelijkeRapportage $invoerTables
	                                      WHERE Portefeuille = '".$portefeuille."'
	                                      AND rapportageDatum ='".$rapportageDatum."'
	                                      AND TijdelijkeRapportage.Type = 'Fondsen' "
	                                      .$__appvar['TijdelijkeRapportageMaakUniek']. $invoerWhere ;
reset ($kopItems);
while (list($item, $data) = each($kopItems))
{
//  echo 'item ->'.$item;
  $percentageItems=$this->getWaarde($percentageQuery[$item],$item,$totaalWaarde);
  //$percentageItems=$this->getPercentages($percentageQuery[$item],$item,$totaalWaarde);
  while (list($type, $gegevens) = each($data))
  {
    $kopItems[$item][$type]['waarde']=round($percentageItems[$type]['waarde'],3);
    array_push($this->pdf->excelData[$j], $kopItems[$item][$type]['waarde']);
  }
}

	verwijderTijdelijkeTabel($portefeuille,$rapportageDatum);

	$j++; //regelnummer verhogen.
		}

$validCol = array();//Loop over array om nullen te bepalen.
$validRow = array(0=>1);
  
for($i=0;$i<=9;$i++)
  $validCol[$i]=1;
//Voordat de CSV gevuld wordt eerst alle colommen met alleen 0 waarden verwijderen.

for($regel = 1; $regel < count($this->pdf->excelData); $regel++ )
{
  for($col = 10; $col < count($this->pdf->excelData[$regel]); $col++)
  {
    if($this->selectData['filterType']=='geen')
    {
      $validCol[$col]=1;
      $validRow[$regel]=1;
    }
    elseif($this->selectData['filterType']=='groter')
    {
      if ($this->pdf->excelData[$regel][$col] > $this->selectData['filterWaarde'])
      {
       $validCol[$col]=1;
         $validRow[$regel]=1;
      }
    }
    elseif($this->selectData['filterType']=='kleiner')
    {
      if ($this->pdf->excelData[$regel][$col] < $this->selectData['filterWaarde'])
      {
        $validCol[$col]=1;
        $validRow[$regel]=1; 
      }
    }
    elseif($this->selectData['filterType']=='groterGelijk')
    {
      if ($this->pdf->excelData[$regel][$col] >= $this->selectData['filterWaarde'])
      {
        $validCol[$col]=1;
        $validRow[$regel]=1;
      }  
    }
    elseif($this->selectData['filterType']=='kleinerGelijk')
    {
      if ($this->pdf->excelData[$regel][$col] <= $this->selectData['filterWaarde'])
      {
        $validCol[$col]=1;
        $validRow[$regel]=1;
      }
    }
    elseif($this->selectData['filterType']=='gelijk')
    {
      if ($this->pdf->excelData[$regel][$col] == $this->selectData['filterWaarde'])
      {
        $validCol[$col]=1;
        $validRow[$regel]=1;
      }
    }
    elseif($this->selectData['filterType']=='nietGelijk')
    {
      if ($this->pdf->excelData[$regel][$col] <> $this->selectData['filterWaarde'])
      {
        $validCol[$col]=1;
        $validRow[$regel]=1;
      }
    }    
  }
}

$dataZonderNul = array();//Kopie van array maken zonder de nullen
$regelNew=0;
for($regel = 0; $regel < count($this->pdf->excelData); $regel++ )
{
  if($validRow[$regel] == 1)
  {
    for($col = 0; $col < count($this->pdf->excelData[$regel]); $col++)
    {
      if($col <14)
      {
        $dataZonderNul[$regelNew][]=$this->pdf->excelData[$regel][$col];
      }
      elseif ($validCol[$col] == 1)
      {
        if($this->selectData['filterType']=='geen')
        {
          if(isNumeric($this->pdf->excelData[$regel][$col]))
            $dataZonderNul[$regelNew][]=array($this->pdf->excelData[$regel][$col],'getal');
          else
            $dataZonderNul[$regelNew][]=$this->pdf->excelData[$regel][$col];
        } 
        elseif($this->selectData['filterType']=='groter')
        {
          if ($this->pdf->excelData[$regel][$col] > $this->selectData['filterWaarde'] || $regel==0)
          {
            if(isNumeric($this->pdf->excelData[$regel][$col]))
               $dataZonderNul[$regelNew][]=array($this->pdf->excelData[$regel][$col],'getal');
             else
               $dataZonderNul[$regelNew][]=$this->pdf->excelData[$regel][$col];
          }
          else
            $dataZonderNul[$regelNew][]='';  
        }
        elseif($this->selectData['filterType']=='kleiner')
        {
          if ($this->pdf->excelData[$regel][$col] < $this->selectData['filterWaarde'] || $regel==0)
          {
            if(isNumeric($this->pdf->excelData[$regel][$col]))
               $dataZonderNul[$regelNew][]=array($this->pdf->excelData[$regel][$col],'getal');
             else
               $dataZonderNul[$regelNew][]=$this->pdf->excelData[$regel][$col];
          }
          else
             $dataZonderNul[$regelNew][]='';  
        }
        elseif($this->selectData['filterType']=='groterGelijk')
        {
          if ($this->pdf->excelData[$regel][$col] >= $this->selectData['filterWaarde'] || $regel==0)
          {
            if(isNumeric($this->pdf->excelData[$regel][$col]))
               $dataZonderNul[$regelNew][]=array($this->pdf->excelData[$regel][$col],'getal');
             else
               $dataZonderNul[$regelNew][]=$this->pdf->excelData[$regel][$col];
          }
          else
            $dataZonderNul[$regelNew][]='';    
        }
        elseif($this->selectData['filterType']=='kleinerGelijk')
        {
          if ($this->pdf->excelData[$regel][$col] <= $this->selectData['filterWaarde'] || $regel==0)
          {
            if(isNumeric($this->pdf->excelData[$regel][$col]))
               $dataZonderNul[$regelNew][]=array($this->pdf->excelData[$regel][$col],'getal');
             else
               $dataZonderNul[$regelNew][]=$this->pdf->excelData[$regel][$col];
          }
          else
            $dataZonderNul[$regelNew][]='';    
        }
        elseif($this->selectData['filterType']=='gelijk')
        {
          if ($this->pdf->excelData[$regel][$col] == $this->selectData['filterWaarde'] || $regel==0)
          {
            if(isNumeric($this->pdf->excelData[$regel][$col]))
               $dataZonderNul[$regelNew][]=array($this->pdf->excelData[$regel][$col],'getal');
             else
               $dataZonderNul[$regelNew][]=$this->pdf->excelData[$regel][$col];
          }
          else
            $dataZonderNul[$regelNew][]='';  
        }
        elseif($this->selectData['filterType']=='nietGelijk')
        {
          if ($this->pdf->excelData[$regel][$col] <> $this->selectData['filterWaarde'] || $regel==0)
          {
            if(isNumeric($this->pdf->excelData[$regel][$col]))
               $dataZonderNul[$regelNew][]=array($this->pdf->excelData[$regel][$col],'getal');
             else
               $dataZonderNul[$regelNew][]=$this->pdf->excelData[$regel][$col];
          }
          else
            $dataZonderNul[$regelNew][]='';    		  
        }
      }
    }
    $regelNew++;
  }
}

$this->pdf->excelData = $dataZonderNul;
 
/*  
if(count($this->pdf->excelData['0']) > 255)
{
  logScherm("De uitvoer geeft meer kolomen dan in excel beschikbaar.");
  if($this->progressbar)
          $this->progressbar->hide();
  exit;
}
*/


		if($this->progressbar)
			$this->progressbar->hide();
	}




	function getKopLabels($query,$type)
{
  $DB = new DB();
  $DB->SQL($query);
	$DB->Query();
	$tmparray = array();
	while($categorie = $DB->nextRecord())
	{
	  if($type=='instrumenten' && $this->selectData['filterFonds'] <> '')
	  {
	    if($this->selectData['filterFonds'] == $categorie['type'])
  	    $tmparray[$categorie['type']]['Omschrijving']=$categorie['Omschrijving'];
	  }
    else
	    $tmparray[$categorie['type']]['Omschrijving']=$categorie['Omschrijving'];
	}
	$tmparray['']['Omschrijving']="Geen $type";

  if($this->Rente)
	  $tmparray['Rente']['Omschrijving']="Rente";

	if($this->liquiditeitenPercentage)
    $tmparray['liquiditeiten']['Omschrijving']="Liquiditeiten";

return $tmparray;
}

function getWaarde($query,$type,$totaalWaarde)
{
  global $__appvar;
  if($this->selectData['percentages'] == "true")
  {
    $percentages=true;
    $restWaarde = 100;
  }
  else
    $restWaarde = $totaalWaarde;

  $DB = new DB();
	$DB->SQL($query);  //echo $query." \n <br>"; exit;
	$DB->Query();
	while($item = $DB->nextRecord())
	{
	  if($percentages)
		  $returnWaarde=($item['WaardeEuro']/$totaalWaarde)*100;
		else
		  $returnWaarde=$item['WaardeEuro'];

		$restWaarde -= $returnWaarde;
		$waardeArray[$item['type']]['waarde']=$returnWaarde;
	}
	if($this->Rente)
	{
  	$DB->SQL(str_replace('Fondsen','rente',$query));// echo $query." \n <br>"; exit;
  	$DB->Query();
  	while($item = $DB->nextRecord())
  	{
  	  if($percentages)
  		  $returnWaarde=($item['WaardeEuro']/$totaalWaarde)*100;
  		else
  		  $returnWaarde=$item['WaardeEuro'];

  		$restWaarde -= $returnWaarde;
  		$waardeArray['Rente']['waarde']+=$returnWaarde;
  	}
  }

	if($this->liquiditeitenPercentage)
	{
	  $query = "SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as WaardeEuro FROM TijdelijkeRapportage
	  WHERE Type = 'rekening' AND Portefeuille = '".$this->portefeuille."' AND 
          rapportageDatum = '".$this->einddatum."' ".$__appvar['TijdelijkeRapportageMaakUniek'];
 	  $DB->SQL($query); //echo $query;
	  $DB->Query();
	  $waarde = $DB->lookupRecord();
	  if($percentages)
	    $returnWaarde = ($waarde['WaardeEuro']/$totaalWaarde)*100;
	  else
	    $returnWaarde=$waarde['WaardeEuro'];
	  $restPercentage = $restWaarde - $returnWaarde;
	  $waardeArray['liquiditeiten']['waarde']=$returnWaarde;

	}
	if($percentages)
	  $waardeArray['']['waarde']=$restPercentage;

	return $waardeArray;
}

function getPercentages($query,$type,$totaalWaarde)
{
  $restPercentage = 100;
  $DB = new DB();
	$DB->SQL($query);  //echo $query." \n <br>"; exit;
	$DB->Query();

	while($item = $DB->nextRecord())
	{
		$percentage=($item['WaardeEuro']/$totaalWaarde)*100;
		$percentageArray[$item['type']]['waarde']=$percentage;
		$restPercentage = $restPercentage - $percentage;
	}
	if($this->liquiditeitenPercentage)
	{
	  $query = "SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as WaardeEuro FROM TijdelijkeRapportage
	  WHERE Type = 'rekening' AND Portefeuille = '".$this->portefeuille."' AND rapportageDatum = '".$this->einddatum."' ";
 	  $DB->SQL($query); //echo $query;
	  $DB->Query();
	  $waarde = $DB->lookupRecord();
	  $percentage = ($waarde['WaardeEuro']/$totaalWaarde)*100;
	  $percentageArray['liquiditeiten']['waarde']=$percentage;
	  $restPercentage = $restPercentage - $percentage;
	}
	$percentageArray['']['waarde']=$restPercentage;

	return $percentageArray;
}


}
?>

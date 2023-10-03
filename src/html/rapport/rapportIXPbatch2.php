<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/12/14 17:45:29 $
 		File Versie					: $Revision: 1.11 $

 		$Log: rapportIXPbatch2.php,v $
 		Revision 1.11  2019/12/14 17:45:29  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2019/11/16 17:36:34  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2019/02/27 13:50:09  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2018/12/01 19:49:52  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2018/05/24 05:18:50  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2018/05/23 13:50:34  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/01/31 17:21:59  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2018/01/15 10:51:40  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2018/01/14 12:38:40  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2017/12/03 12:43:47  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2017/12/02 19:13:04  rvv
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

class rapportIXPbatch2
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function rapportIXPbatch2( $selectData )
	{
	  $this->pdf = new PDFRapport('L','mm');
	  $this->selectData = $selectData;
		$this->pdf->excelData = array();
		$this->orderby  = " Client ";
		$this->pdf->excelData2 = array();
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
    
    
//$portefeuilles=array(630241=>$portefeuilles[630241]);
 

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
		$totalen=array();
		$vermGroepen=array(25000=>'Kleiner dan 25.000',
										  100000=>'25.000 tot 100.000',
											250000=>'100.000 tot 250.000',
											500000=>'250.000 tot 500.000',
									   1000000=>'500.000 tot 1 milj.',
										 2500000=>'1 milj. tot 2,5 milj.',
		  	          1000000000=>'Groter dan 2,5 miljoen');
    $db=new DB();
		foreach($portefeuilles as  $portefeuille=>$portData) //Portefeuille data in array laden
		{
      $query="SELECT
Portefeuilles.id,
ModelPortefeuilles.Portefeuille as ModelPortefeuille,
KeuzePerVermogensbeheerder.categorieIXP as IXP_OVK,
VermogensbeheerdersPerBedrijf.Bedrijf
FROM
Portefeuilles
LEFT JOIN KeuzePerVermogensbeheerder ON Portefeuilles.Vermogensbeheerder = KeuzePerVermogensbeheerder.vermogensbeheerder AND 
Portefeuilles.SoortOvereenkomst = KeuzePerVermogensbeheerder.waarde AND KeuzePerVermogensbeheerder.categorie='SoortOvereenkomsten' 
LEFT JOIN VermogensbeheerdersPerBedrijf ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
LEFT JOIN ModelPortefeuilles ON Portefeuilles.Portefeuille = ModelPortefeuilles.Portefeuille
WHERE Portefeuilles.portefeuille='$portefeuille'";
      $db->SQL($query);
      $pId=$db->lookupRecord();
      $portData['portefeuilleId']=$pId['id'];
      $portData['Bedrijf']=$pId['Bedrijf'];
      
      if($pId['ModelPortefeuille']=='')
        $portData['IXP_OVK']=$pId['IXP_OVK'];
      else
        $portData['IXP_OVK']='MODEL';
			

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
  $kopItems['categorien']= array();
}
else
{
  $kopItems[$this->selectData['uitvoer']] = array();
}



$queries['categorien']     = "SELECT 	Beleggingscategorien.Beleggingscategorie as type,
						                Beleggingscategorien.Omschrijving
				                    FROM 	Beleggingscategorien,
						                BeleggingscategoriePerFonds
				                    WHERE 	Beleggingscategorien.Beleggingscategorie = BeleggingscategoriePerFonds.Beleggingscategorie
 				                    GROUP BY Beleggingscategorien.Beleggingscategorie";


$this->pdf->excelData[] = array("Bedrijf","Vermogensbeheerder","Einddatum","Portefeuille",'PTF',
                 'SoortOvereenkomst','IXP_OVK',
								 "Einddatum",
								 "Risicoprofiel",
								 "Depotbank",
                 "Beginvermogen","Stortingen","Onttrekkingen","Resultaat","Eindvermogen",
                 'Gem vermogen',
                 'PercBegin',
								 'Performance',
								 'Kosten EUR',
								 'KostenPerc',
	               'VermGroep',
	               'Aandelen','Obligaties','Liquiditeiten'
								 );

		$this->pdf->excelData2[] = array("Maand","Jaar","Vermogensbeheerder","IXPCode",'Aandelen','Obligaties','Liquiditeiten','Performance','KostenPerc','PTF',
			'IXP_OVK',
			"Bedrijf",
			"Risicoprofiel",
			"Depotbank",
			'VermGroep','nietGekoppeld','Accountmanagercode');



    $IXP_Beleggingscategorie=$__appvar["IXP_Beleggingscategorie"];
    $IXP_Beleggingscategorie[]='nietGekoppeld';

	 	$j=1;  //regelnummer
		for($i=0; $i < count($pdata);$i++)
		{

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



//vul tijdelijke tabel.

    if(substr($einddatum,5,5)=='01-01')
			$startjaar=true;
		else
			$startjaar=false;

		$fondswaarden =  berekenPortefeuilleWaarde($portefeuille, $einddatum,$startjaar,'EUR',$einddatum);
		vulTijdelijkeTabel($fondswaarden,$portefeuille,$einddatum);

			if(substr($startdatum,5,5)=='01-01')
				$startjaar=true;
			else
				$startjaar=false;

		$fondswaarden =  berekenPortefeuilleWaarde($portefeuille, $startdatum, $startjaar,'EUR',$startdatum);
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

    foreach($vermGroepen as $groepWaarde=>$groepnaam)
		{
			if($totaalWaarde<=$groepWaarde)
			{
				$vermGroep = $groepnaam;
				break;
			}
		}
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

$rapportageDatum=$einddatum;

      $query="SELECT
KeuzePerVermogensbeheerder.waarde,
KeuzePerVermogensbeheerder.categorieIXP AS categorie
FROM
KeuzePerVermogensbeheerder
INNER JOIN Portefeuilles ON KeuzePerVermogensbeheerder.vermogensbeheerder = Portefeuilles.Vermogensbeheerder
WHERE Portefeuilles.Portefeuille='".$pdata[$i]['Portefeuille']."'  AND KeuzePerVermogensbeheerder.categorie='Beleggingscategorien'";
      $DB->SQL($query);
      $DB->Query();
      $conversie=array();
     while($data = $DB->nextRecord())
     {
       $conversie[$data['waarde']]=$data['categorie'];
     }


$percentageQuery['categorien']        ="SELECT TijdelijkeRapportage.beleggingscategorie as type ,sum(ActuelePortefeuilleWaardeEuro) AS WaardeEuro
	                                     FROM TijdelijkeRapportage $invoerTables
	                                     WHERE Portefeuille = '".$portefeuille."'
	                                     AND rapportageDatum ='".$rapportageDatum."' "
	                                     .$__appvar['TijdelijkeRapportageMaakUniek']. $invoerWhere .
	                                     "AND TijdelijkeRapportage.Type IN ('Fondsen','Rente')
	                                     GROUP BY TijdelijkeRapportage.beleggingscategorie;";
      
      $nieuweVerdeling=array();
      $percentageItems=$this->getWaarde($percentageQuery['categorien'],'categorien',$totaalWaarde);

      foreach($percentageItems as $cat=>$verdeling)
      {
        if(isset($conversie[$cat]) && $conversie[$cat]!='')
          $nieuweVerdeling[$conversie[$cat]]+=$verdeling['waarde'];
        elseif(round($totaalWaarde,2)==0)
          $nieuweVerdeling['Liquiditeiten']+=$verdeling['waarde'];
        else
          $nieuweVerdeling['nietGekoppeld']+=$verdeling['waarde'];
      }



			$this->pdf->excelData[] = array($pdata[$i]['Bedrijf'],$pdata[$i]['Vermogensbeheerder'],$einddatum,$pdata[$i]['Portefeuille'],
				'PTF'.sprintf("%08d",$pdata[$i]['portefeuilleId']),
				$pdata[$i]['SoortOvereenkomst'],
				$pdata[$i]['IXP_OVK'],
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
				round($kostenEUR/$gemVermogen*-100,4),
				$vermGroep,
				round($nieuweVerdeling['Aandelen'],3),
				round($nieuweVerdeling['Obligaties'],3),
				round($nieuweVerdeling['Liquiditeiten'],3),
				round($nieuweVerdeling['nietGekoppeld'],3)
			);


			$this->pdf->excelData2[] = array(date("m",db2jul($rapportageDatum)),
				date("Y",db2jul($rapportageDatum)),
				$pdata[$i]['Vermogensbeheerder'],
        $pdata[$i]['portefeuilleId'],
				round($nieuweVerdeling['Aandelen'],3),
				round($nieuweVerdeling['Obligaties'],3),
				round($nieuweVerdeling['Liquiditeiten'],3),
				round($performance,2),
				round($kostenEUR/$gemVermogen*-100,4),
				'PTF'.sprintf("%08d",$pdata[$i]['portefeuilleId']),
				$pdata[$i]['IXP_OVK'],
				$pdata[$i]['Bedrijf'],
				$pdata[$i]['Risicoklasse'],
				$pdata[$i]['Depotbank'],
				$vermGroep,
				round($nieuweVerdeling['nietGekoppeld'],3),
        $pdata[$i]['Accountmanager']
			);


	verwijderTijdelijkeTabel($portefeuille,$rapportageDatum);

	$j++; //regelnummer verhogen.
		}


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
  $percentages=false;
  if($this->selectData['percentages'] == "true")
  {
    $percentages=true;
    $restWaarde = 100;
  }
  else
    $restWaarde = $totaalWaarde;

  if(round($totaalWaarde)==0 && $percentages==true)
  {
    return array('Liquiditeiten' => array('waarde' => 100));
  }
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
  /*
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
  */

	if($this->liquiditeitenPercentage)
	{
	  $query = "SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as WaardeEuro,  beleggingsCategorie as categorie  FROM TijdelijkeRapportage
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
    if($waarde['categorie']<> '')
      $waardeArray[$waarde['categorie']]['waarde']+=$returnWaarde;
    else
	    $waardeArray['Liquiditeiten']['waarde']+=$returnWaarde;

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
	  $query = "SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as WaardeEuro , beleggingsCategorie as categorie FROM TijdelijkeRapportage
	  WHERE Type = 'rekening' AND Portefeuille = '".$this->portefeuille."' AND rapportageDatum = '".$this->einddatum."' ";
 	  $DB->SQL($query); //echo $query;
	  $DB->Query();
	  $waarde = $DB->lookupRecord();
	  $percentage = ($waarde['WaardeEuro']/$totaalWaarde)*100;
    if($waarde['categorie']<> '')
      $percentageArray[$waarde['categorie']]['waarde']=$percentage;
    else
	    $percentageArray['liquiditeiten']['waarde']=$percentage;
	  $restPercentage = $restPercentage - $percentage;
	}
	$percentageArray['']['waarde']=$restPercentage;

	return $percentageArray;
}


}
?>

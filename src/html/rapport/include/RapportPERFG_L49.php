<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/06/30 17:43:55 $
File Versie					: $Revision: 1.27 $

$Log: RapportPERFG_L49.php,v $
Revision 1.27  2018/06/30 17:43:55  rvv
*** empty log message ***

Revision 1.26  2018/06/16 17:42:56  rvv
*** empty log message ***

Revision 1.25  2017/06/25 14:49:37  rvv
*** empty log message ***

Revision 1.24  2017/05/28 09:57:56  rvv
*** empty log message ***

Revision 1.23  2017/05/21 09:55:30  rvv
*** empty log message ***

Revision 1.22  2016/10/09 14:45:08  rvv
*** empty log message ***

Revision 1.21  2015/08/06 05:14:47  rvv
*** empty log message ***

Revision 1.20  2015/08/05 15:53:37  rvv
*** empty log message ***

Revision 1.19  2014/12/31 18:09:06  rvv
*** empty log message ***

Revision 1.18  2014/12/29 13:55:30  rvv
*** empty log message ***

Revision 1.17  2014/12/28 14:29:08  rvv
*** empty log message ***

Revision 1.16  2014/12/24 16:00:30  rvv
*** empty log message ***

Revision 1.15  2014/12/14 15:38:41  rvv
*** empty log message ***

Revision 1.14  2014/12/13 19:24:44  rvv
*** empty log message ***

Revision 1.13  2014/12/10 16:58:25  rvv
*** empty log message ***

Revision 1.12  2014/08/16 15:31:50  rvv
*** empty log message ***

Revision 1.11  2014/04/26 16:43:08  rvv
*** empty log message ***

Revision 1.10  2014/04/23 19:02:09  rvv
*** empty log message ***

Revision 1.9  2014/04/23 16:18:44  rvv
*** empty log message ***

Revision 1.8  2014/04/05 15:33:48  rvv
*** empty log message ***

Revision 1.7  2014/04/02 15:53:15  rvv
*** empty log message ***

Revision 1.6  2014/03/27 14:59:18  rvv
*** empty log message ***

Revision 1.5  2014/02/09 11:12:46  rvv
*** empty log message ***

Revision 1.4  2014/02/09 10:59:42  rvv
*** empty log message ***

Revision 1.3  2014/02/08 17:42:08  rvv
*** empty log message ***

Revision 1.2  2013/12/19 17:03:03  rvv
*** empty log message ***

Revision 1.1  2013/12/18 17:10:42  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once('../indexBerekening.php');


class RapportPERFG_L49
{
	function RapportPERFG_L49($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	  $this->tweedeStart();


	  $this->rapportageDatumVanaf = "$RapStartJaar-01-01";

	 if ($RapStartJaar != $RapStopJaar)
	 {
     echo "Attributie start- en einddatum moeten in hetzelfde jaar liggen.";
     exit;
	 }
	}

	function tweedeStart()
	{
	  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) == db2jul($this->rapportageDatumVanaf))
	  {
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  }
	  else
	  {
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";
	   if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01" && $this->pdf->engineII == false)
	   {
	    $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,"$RapStartJaar-01-01",true);
      vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,"$RapStartJaar-01-01");
      $this->extraVulling = true;
	   }
	  }
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersBegin;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}



	function writeRapport()
	{
	  global $__appvar;

	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

	 $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));


	 	//Kleuren instellen
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);
    $this->categorieKleuren=$allekleuren['OIB'];
	 // $this->categorieOmschrijving=array('LIQ'=>'Liquiditeiten','ZAK'=>'Zakelijke waarden','VAR'=>'Vastrentende waarden','Liquiditeiten'=>'Liquiditeiten');

	  $q="SELECT beleggingscategorie ,omschrijving FROM Beleggingscategorien";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
    while($data=$DB->nextRecord())
      $this->categorieOmschrijving[$data['beleggingscategorie']]=$data['omschrijving'];

//listarray($this->categorieVolgorde);
		// voor data
		$this->pdf->widthA = array(1,95,25,5,25,5,25,5,25,5,25,5,25,5,25,5);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R');


  	$this->pdf->widthB = array(1,95,30,10,30,115);
		$this->pdf->alignB = array('L','L','R','R','R');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    $this->pdf->AddPage();
    checkPage($this->pdf);

		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];


$DB = new DB();
$query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE Portefeuille = '".$this->portefeuille."' AND Categorie = 'Totaal' ORDER BY Datum ASC LIMIT 1 ";
$DB->SQL($query);
$DB->Query();
$datum = $DB->nextRecord();

if($this->pdf->lastPOST['perfPstart'] == 1 || 1)
{
  if($datum['id'] > 0)
  {
    if($datum['month'] <10)
      $datum['month'] = "0".$datum['month'];
    $start = $datum['year'].'-'.$datum['month'].'-01';
  }
  else
  {
    $start=substr($this->pdf->PortefeuilleStartdatum,0,10);
  }
}
else
  $start = $this->rapportageDatumVanaf;
  
$eind = $this->rapportageDatum;

$datumStart = db2jul($start);
$datumStop  = db2jul($eind);


$index = new indexHerberekening();
$indexWaarden = $index->getWaarden($start,$eind,$this->portefeuille,$this->pdf->portefeuilledata['SpecifiekeIndex'],'kwartaal');
/*
$query="SELECT verwachtRendement FROM Risicoklassen 
        WHERE Risicoklasse='".$this->pdf->portefeuilledata['Risicoklasse']."' AND
              Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
$DB->SQL($query); 
$DB->Query();
$verwachtRendement = $DB->nextRecord();
*/


$query="SELECT Fonds FROM IndexPerBeleggingscategorie WHERE Portefeuille='".$this->portefeuille."'";
$DB->SQL($query);  
$DB->Query();
$index = $DB->nextRecord();
$extraIndex=$index['Fonds'];  

if(substr($start,5,5)=='12-31')
  $rapportageJaarStart=substr($start,0,4)+1;
else
  $rapportageJaarStart=substr($start,0,4);
$rapportageJaar=substr($eind,0,4);
$dataPerJaar=array();

for($i=$rapportageJaarStart;$i<$rapportageJaarStart+10;$i++)
{
  $waardePerJaar[$i]=array();
  $waardePerJaarLijn[$i]=array();
}

for($i=1;$i<=4;$i++)
{
 $kwartaalPerf["Q".$i.'-'.$rapportageJaar]=array();
 $jaarPerf[$rapportageJaarStart+$i-1]=array();
 $jaarPerfVerwacht[$rapportageJaarStart+$i-1]=array();
}


foreach($indexWaarden as $perfData)
{
  $start=substr($perfData['periode'],0,10);
  $stop=substr($perfData['periode'],12,10);
  
  $indexData['fondsKoers_start']=$this->getFondsKoers($extraIndex,$start);
  $indexData['fondsKoers_stop']=$this->getFondsKoers($extraIndex,$stop);
  $indexData['performance'] = ($indexData['fondsKoers_stop'] - $indexData['fondsKoers_start']) / ($indexData['fondsKoers_start']/100 );

  $jaar=substr($perfData['periode'],12,4);
  $maand=substr($perfData['periode'],17,2);
  $kwartaal="Q".ceil($maand/3);
  
  $waardePerJaar[$jaar]['stortingen']+=$perfData['stortingen'];
  $waardePerJaar[$jaar]['onttrekkingen']+=$perfData['onttrekkingen'];
  $waardePerJaarLijn[$jaar]['waardeHuidige']=$perfData['waardeHuidige'];
/*
  if(!isset($waardePerJaarLijn[$jaar]['doelvermogen']))
  {
    $doelvermogenGetal=0;
    if(isset($this->pdf->portefeuilles) && is_array($this->pdf->portefeuilles))
    {
      foreach($this->pdf->portefeuilles as $portefeuille)
      {
        $query="SELECT waarde as doelvermogen FROM Beleggingsplan WHERE portefeuille='".$portefeuille."' AND Datum <= '".substr($stop,0,4)."-12-31' AND Datum <= '".$this->rapportageDatum."' order BY Datum desc limit 1";
        $DB->SQL($query);
        $DB->Query();
        $doelvermogen = $DB->nextRecord();
        $doelvermogenGetal+=$doelvermogen['doelvermogen'];
        //echo "$query <br>\n $portefeuille $doelvermogenGetal<br>\n ";
      }
      //$portefeuilles=implode("','",$this->pdf->portefeuilles);
      //$query="SELECT sum(waarde) as doelvermogen FROM Beleggingsplan WHERE portefeuille IN('".$portefeuilles."')";
    }
    else
    {
      $query="SELECT waarde as doelvermogen FROM Beleggingsplan WHERE portefeuille='".$this->portefeuille."' AND Datum <= '".substr($stop,0,4)."-12-31' AND Datum <= '".$this->rapportageDatum."' order BY Datum desc limit 1";
      $DB->SQL($query);
      $DB->Query();
      $doelvermogen = $DB->nextRecord();
      $doelvermogenGetal+=$doelvermogen['doelvermogen'];
    }
    $waardePerJaarLijn[$jaar]['doelvermogen']=$doelvermogenGetal;
  }
*/




  if(1||$jaar<>$rapportageJaar)
  {
    if(!isset($jaarPerf[$jaar]['specifiekeIndexPerformance']))
      $jaarPerf[$jaar]['specifiekeIndexPerformance']=0;
    if(!isset($jaarPerf[$jaar]['performance']))
      $jaarPerf[$jaar]['performance']=0;   
    if(!isset($jaarPerf[$jaar]['benchmarkBeheerder']))
      $jaarPerf[$jaar]['benchmarkBeheerder']=0;   
  
    if($this->pdf->portefeuilledata['SpecifiekeIndex'] <> '')
      $jaarPerf[$jaar]['specifiekeIndexPerformance']=((1+$jaarPerf[$jaar]['specifiekeIndexPerformance']/100)*
                                                      (1+$perfData['specifiekeIndexPerformance']/100)-1)*100;
    $jaarPerf[$jaar]['performance']=((1+$jaarPerf[$jaar]['performance']/100)*
                                                      (1+$perfData['performance']/100)-1)*100;
  //  $jaarPerf[$jaar]['benchmarkBeheerder']=((1+$jaarPerf[$jaar]['benchmarkBeheerder']/100)*
  //                                                    (1+$indexData['performance']/100)-1)*100;
     //$jaarPerf[$jaar]['performance']*1+($perfData['performance']/100);
//    $jaarPerfVerwacht[$jaar]['verwachtRendementY']=$verwachtRendement['verwachtRendement'];

  }
  if($jaar==$rapportageJaar)
  {
      if($kwartaal <> $lastKwartaal)
      {
        $kwartaalPerf[$kwartaal.'-'.$jaar]['performance']=0;
        $kwartaalPerf[$kwartaal.'-'.$jaar]['specifiekeIndexPerformance']=0;
      }
      if($this->pdf->portefeuilledata['SpecifiekeIndex'] <> '')
        $kwartaalPerf[$kwartaal.'-'.$jaar]['specifiekeIndexPerformance']=((1+$kwartaalPerf[$kwartaal.'-'.$jaar]['specifiekeIndexPerformance']/100)*
                                                      (1+$perfData['specifiekeIndexPerformance']/100)-1)*100;
      $kwartaalPerf[$kwartaal.'-'.$jaar]['performance']=((1+$kwartaalPerf[$kwartaal.'-'.$jaar]['performance']/100)*
                                                      (1+$perfData['performance']/100)-1)*100;
   //   $kwartaalPerf[$kwartaal.'-'.$jaar]['benchmarkBeheerder']=((1+$kwartaalPerf[$kwartaal.'-'.$jaar]['benchmarkBeheerder']/100)*
   //                                                   (1+$indexData['performance']/100)-1)*100;

//      $kwartaalPerfVerwacht[$kwartaal.'-'.$jaar]['verwachtRendementQ']=$verwachtRendement['verwachtRendement']/4;
  }
  $lastKwartaal=$kwartaal;

}
$this->categorieOmschrijving['performance']='Rendement beheerder';
$this->categorieOmschrijving['specifiekeIndexPerformance']='Benchmark FCT';
//$this->categorieOmschrijving['verwachtRendementY']='Doelrendement gem. jaar';
//$this->categorieOmschrijving['verwachtRendementQ']='Doelrendement gem. kw.';
$this->categorieOmschrijving['stortingen']='Stortingen';
$this->categorieOmschrijving['onttrekkingen']='Onttrekkingen';
$this->categorieOmschrijving['doelvermogen']='Doelvermogen';
$this->categorieOmschrijving['waardeHuidige']='Waarde';
$this->categorieOmschrijving['benchmarkBeheerder']='Benchmark beheerder';




//listarray($jaarPerf);
//listarray($jaarPerfVerwacht);

//listarray($kwartaalPerf);
//listarray($waardePerJaar);
//listarray($waardePerJaarLijn);

//$this->pdf->SetXY(20,$this->pdf->rapportYstart);
//$this->VBarDiagram2(130,130,$waardePerJaar,$waardePerJaarLijn,'Vermogensontwikkeling per jaar',$procent=false,'U');

$this->pdf->SetXY(20,$this->pdf->rapportYstart);
$this->VBarDiagram2(120,50,$kwartaalPerf,$kwartaalPerfVerwacht,'Rendementsanalyse per kwartaal');

$this->pdf->SetXY(160,$this->pdf->rapportYstart);
$this->VBarDiagram2(120,50,$jaarPerf,$jaarPerfVerwacht,'Rendementsanalyse per jaar');

    $this->perfBlok();

paginaVoet($this->pdf);
}

  function perfBlok()
  {
    global $__appvar;
    include_once($__appvar["basedir"]."/html/rapport/include/ATTberekening_L49.php");
    $DB = new DB();
    $query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE Portefeuille = '".$this->portefeuille."' AND Categorie = 'Totaal' ORDER BY Datum ASC LIMIT 1 ";
    $DB->SQL($query);
    $DB->Query();
    $datum = $DB->nextRecord();

    if($this->pdf->lastPOST['perfPstart'] == 1 || 1)
    {
      if($datum['id'] > 0)
      {
        if($datum['month'] <10)
          $datum['month'] = "0".$datum['month'];
        $start = $datum['year'].'-'.$datum['month'].'-01';
      }
      else
      {
        $start=$this->pdf->PortefeuilleStartdatum;
      }
    }
    else
      $start = $this->rapportageDatumVanaf;

    $tweedeStart=substr($this->rapportageDatum,0,4)."-01-01";

    if(db2jul($tweedeStart) < db2jul($this->pdf->PortefeuilleStartdatum))
      $tweedeStart=$this->pdf->PortefeuilleStartdatum;

    $att=new ATTberekening_L49($this);
    $indexDataBegin = $att->bereken($start,$this->rapportageDatum);
    $indexDataJaar = $att->bereken($tweedeStart,$this->rapportageDatum);


//exit;
    $rapportageJaar=substr($this->rapportageDatum,0,4);
    $dataPerDatum=array();

    foreach($indexDataBegin as $categorie=>$categorieData)
    {
      if($categorie <> 'totaal')
      {
        foreach($categorieData['waarden'] as $datum=>$perfData)
        {
          $dataPerDatum[$datum][$categorie]['eindwaarde']=$perfData['eindwaarde'];
          $dataPerDatum[$datum][$categorie]['perf']=1+$perfData['procent'];
          $dataPerDatum[$datum][$categorie]['aandeel']=$perfData['eindwaarde']/$indexDataBegin['totaal']['waarden'][$datum]['eindwaarde'];
        }
      }
    }

    foreach($indexDataJaar as $categorie=>$categorieData)
    {
      if($categorie <> 'totaal')
      {
        foreach($categorieData['waarden'] as $datum=>$perfData)
        {
          $dataPerDatumJaar[$datum][$categorie]['eindwaarde']=$perfData['eindwaarde'];
          $dataPerDatumJaar[$datum][$categorie]['perf']=1+$perfData['procent'];
          $dataPerDatumJaar[$datum][$categorie]['aandeel']=$perfData['eindwaarde']/$indexDataJaar['totaal']['waarden'][$datum]['eindwaarde'];
        }
      }
    }

    $jaarPerf=array();
    $kwartaalPerf=array();
    $totaalPerf=array();

    $tabel=array();
    $totaalAantal=count($dataPerDatum);
    $n=0;
    foreach($dataPerDatum as $datum=>$categorien)
    {
      $jaar=substr($datum,0,4);
      $maand=substr($datum,5,2);
      $kwartaal="Q".ceil($maand/3);

      foreach($categorien as $categorie=>$perfData)
      {
        if(!isset($totaalPerf[$categorie]))
          $totaalPerf[$categorie]=1;

        $totaalPerf[$categorie]=$totaalPerf[$categorie]*$perfData['perf'];
        if($jaar<>$rapportageJaar)
        {
          if($jaar <> $lastJaar)
            $jaarPerf[$jaar][$categorie]['perf']=1;

          $jaarPerf[$jaar][$categorie]['perf']=$jaarPerf[$jaar][$categorie]['perf']*$perfData['perf'];
          $jaarPerf[$jaar][$categorie]['eindwaarde']=$perfData['eindwaarde'];
          $jaarPerf[$jaar][$categorie]['aandeel']=$perfData['aandeel'];
        }
        if($jaar==$rapportageJaar)
        {
          if($kwartaal <> $lastKwartaal)
            $kwartaalPerf[$kwartaal.'-'.$jaar][$categorie]['perf']=1;

          $kwartaalPerf[$kwartaal.'-'.$jaar][$categorie]['perf']=$kwartaalPerf[$kwartaal.'-'.$jaar][$categorie]['perf']*$perfData['perf'];
          $kwartaalPerf[$kwartaal.'-'.$jaar][$categorie]['eindwaarde']=$perfData['eindwaarde'];
          $kwartaalPerf[$kwartaal.'-'.$jaar][$categorie]['aandeel']=$perfData['aandeel'];
        }
        if($n>$totaalAantal-5)
        {
          $tabel[$categorie][$kwartaal. "-" .$jaar] = $perfData['aandeel'];
        }

      }

     $n++;
      $lastKwartaal=$kwartaal;
    }
    $lastKwartaal='';

    foreach($dataPerDatumJaar as $datum=>$categorien)
    {
      $jaar=substr($datum,0,4);
      $maand=substr($datum,5,2);
      $kwartaal=ceil($maand/3);

      foreach($categorien as $categorie=>$perfData)
      {
        if(!isset($totaalPerfJaar[$categorie]))
          $totaalPerfJaar[$categorie]=1;

        $totaalPerfJaar[$categorie]=$totaalPerfJaar[$categorie]*$perfData['perf'];
        if($kwartaal <> $lastKwartaal)
          $kwartaalPerfJaar[$kwartaal.'-'.$jaar][$categorie]['perf']=1;

        $kwartaalPerfJaar[$kwartaal.'-'.$jaar][$categorie]['perf']=$kwartaalPerfJaar[$kwartaal.'-'.$jaar][$categorie]['perf']*$perfData['perf'];
        $kwartaalPerfJaar[$kwartaal.'-'.$jaar][$categorie]['eindwaarde']=$perfData['eindwaarde'];
        $kwartaalPerfJaar[$kwartaal.'-'.$jaar][$categorie]['aandeel']=$perfData['aandeel'];
      }
      $lastKwartaal=$kwartaal;
    }

    foreach($jaarPerf as $jaar=>$jaarData)
    {
      $grafiekData[$jaar]=$jaarData;
    }

    for($i=1;$i<=4;$i++)
      $grafiekData["Q".$i.'-'.$rapportageJaar]=array();

    foreach($kwartaalPerf as $kwartaal=>$kwartaalData)
    {
      $grafiekData[$kwartaal]=$kwartaalData;
      $laatste=$kwartaalData;
    }

    foreach($kwartaalPerfJaar as $kwartaal=>$kwartaalData)
    {
      $laatsteJaar=$kwartaalData;
    }

    $n=0;
    if (count($grafiekData) > 0)
    {
      $witMarge=$this->pdf->witCell;
      $this->pdf->SetXY($this->pdf->marge,100)		;//112
      $this->VBarDiagram(120, 50, $grafiekData, 'Ontwikkeling assetverdeling portefeuille');
      $w=(80/4)-$witMarge;
      $this->pdf->SetY(112);
      $this->pdf->setWidths(array(140,40-$witMarge,$witMarge,$w,$witMarge,$w,$witMarge,$w,$witMarge,$w,$witMarge));
      $this->pdf->SetAligns(array('L','L','C','R','C','R','C','R','C','R','C','R'));
      $this->pdf->fillCell = array(0,1,0,1,0,1,0,1,0,1,0,1);

      $this->pdf->SetFillColor($this->pdf->achtergrondKop[0],$this->pdf->achtergrondKop[1],$this->pdf->achtergrondKop[2]);
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $row=array('','Assetverdeling');
     // listarray($tabel);
      foreach($tabel as $categorie=>$categorieData)
      {
        foreach($categorieData as $periode=>$percentage)
        {
          $row[] = '';
          $row[] = $periode;
        }
        break;
      }
      $this->pdf->row($row);

      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
     // listarray($tabel);exit;

      foreach($tabel as $categorie=>$categorieData)
      {
        $n=$this->switchColor($n);
        $row=array();
        $row[]='';
        $row[]=$this->categorieOmschrijving[$categorie];
        foreach($categorieData as $periode=>$percentage)
        {
          $row[] = '';
          $row[] = $this->formatGetal($percentage*100,0).'%';
        }
        $this->pdf->row($row);
      }


    }


  }


  function switchColor($n)
  {
    $col1=$this->pdf->achtergrondLicht;
    $col2=$this->pdf->achtergrondDonker;

    if($n%2==0)
      $this->pdf->SetFillColor($col1[0],$col1[1],$col1[2]);
    else
      $this->pdf->SetFillColor($col2[0],$col2[1],$col2[2]);

    $n++;
    return $n;
  }

	function getFondsKoers($fonds,$datum)
	{
	  $db=new DB();
	  $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
	  $db->SQL($query);
	  $koers=$db->lookupRecord();
	  return $koers['Koers'];
	}
  

  function VBarDiagram2($w, $h, $data,$datalijn,$titel,$procent=true,$legendaLocatie='R')
  {
      global $__appvar;
      if($legendaLocatie=='R')
        $legendaWidth = 45;
      else
        $legendaHeight = 30; 
      $grafiekPunt = array();
      $verwijder=array();

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      
      $h=$h-$legendaHeight;

      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
      $this->pdf->setXY($XPage,$YPage+2);
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
      $this->pdf->Cell($w,4,$titel,0,1,'L');
      $this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array($this->pdf->koplijn[0],$this->pdf->koplijn[1],$this->pdf->koplijn[2]),'dash'=>0));
      $this->pdf->line($XPage,$YPage+$this->pdf->rowHeight+3,$XPage+$w,$YPage+$this->pdf->rowHeight+3);
      
      $YPage=$YPage+$h+15;
     
      $colors=array('stortingen'=>array(120,170,0),
                    'onttrekkingen'=>array(220,57,18),
                    'waardeHuidige'=>array(255,153,0),
                    'doelvermogen'=>array(51,102,204),
                    'performance'=>array(51,102,204),
                    'benchmarkBeheerder'=>array(220,57,18),
                    'specifiekeIndexPerformance'=>array(255,153,0),
                    'verwachtRendementY'=>array(120,170,0),
                    'verwachtRendementQ'=>array(120,170,0));
           //    listarray($data);    
           //    listarray($datalijn);    

      $geldigeCategorien=array();             
      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = $datum;
        $n=0;
        $minVal=-1;
        $maxVal=1;
        foreach ($waarden as $categorie=>$waarde)
        {
          if($categorie=='LIQ')
            $categorie='Liquiditeiten';
          if($waarde <> 0)
            $geldigeCategorien[$categorie]=true;
          $grafiek[$datum][$categorie]=$waarde;
          $grafiekCategorie[$categorie][$datum]=$waarde;
          $categorien[$categorie] = $categorie;
          $categorieId[$n]=$categorie ;


        if(!isset($colors[$categorie]))
            $colors[$categorie]=array(rand(0,255),rand(0,255),rand(0,255));

//            $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          $n++;
        }
      }

      foreach ($datalijn as $datum=>$waarden)
      {
        $legenda[$datum] = $datum;
        
        $n=0;
        foreach ($waarden as $categorie=>$waarde)
        {
          $categorien[$categorie] = $categorie;
          if($waarde > $maxVal)
            $maxVal=ceil($waarde);
          if($waarde < $minVal)
            $minVal=ceil($waarde); 

          if($waarde <> 0)
            $geldigeCategorien[$categorie]=true;
            
          if(!isset($colors[$categorie]))
            $colors[$categorie]=array(rand(0,255),rand(0,255),rand(0,255));

//            $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          $n++;
        }
      }     

      foreach($grafiek as $datum=>$datumData)
      {
      
        foreach($datumData as $categorie=>$waarde)
        {
          
          if($waarde > $maxVal)
            $maxVal=ceil($waarde);
          if($waarde < $minVal)
            $minVal=floor($waarde);  
        }
      }

if($procent==false)
  $maxVal=ceil($maxVal/pow(10,strlen($maxVal)-1))*pow(10,strlen($maxVal)-1);
else
  $maxVal=ceil($maxVal/5)*5;  
//echo $max;exit;
//echo "$minVal <br>\n";
$minVal=floor($minVal/.5)*.5;
//
//echo "$minVal <br>\n<br>\n";
      $numBars = count($legenda);

      if($color == null)
      {
        $color=array(155,155,155);
      }


      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
//      $XPage = $this->pdf->GetX();
//      $YPage = $this->pdf->GetY()+$h+15;
      $margin = 0;
      $margeLinks=10;
      $XPage+=$margeLinks;
      $w-=$margeLinks;
      
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

      $n=0;
      if($legendaLocatie=='U')
      {
        $xcorrectie=$w;
        $ycorrectie=$h+10;
      }
      
      foreach($colors as $categorie=>$kleur)
      {
        if(isset($geldigeCategorien[$categorie]))
        {
          $this->pdf->Rect($XstartGrafiek+$bGrafiek+3-$xcorrectie , $YstartGrafiek-$hGrafiek+$n*7+2+$ycorrectie, 2, 2, 'F',null,$colors[$categorie]);
          $this->pdf->SetXY($XstartGrafiek+$bGrafiek+6-$xcorrectie ,$YstartGrafiek-$hGrafiek+$n*7+1.5+$ycorrectie );
          $this->pdf->MultiCell(40, 4,$this->categorieOmschrijving[$categorie],0,'L');
          $n++;
        }
      }

      if($minVal < 0)
      {
        $unit = $hGrafiek / (-1 * $minVal + $maxVal) * -1;
        $nulYpos =  $unit * (-1 * $minVal);
      }
      else
      {
        $unit = $hGrafiek / $maxVal * -1;
        $nulYpos =0;
      }


      $horDiv = 4;
      $horInterval = $hGrafiek / $horDiv;
      $bereik = $hGrafiek/$unit;

      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      $this->pdf->SetTextColor(0,0,0);

      $stapgrootte = round(abs($bereik)/$horDiv);
      $top = $YstartGrafiek-$h;
      $bodem = $YstartGrafiek;
      $absUnit =abs($unit);

      $nulpunt = $YstartGrafiek + $nulYpos;
      $n=0;

      if($procent==true)
        $legendaEnd=' %';
      else
        $legendaEnd='';
         
      for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
      {
        $skipNull = true;
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1).$legendaEnd,0,0,'R');
        $n++;
        if($n >20)
         break;
      }

      $n=0;
      for($i=$nulpunt; round($i) >= $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        if($skipNull == true)
          $skipNull = false;
        else
        {
          $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
          $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte).$legendaEnd,0,0,'R');
        }
        $n++;
        if($n >20)
          break;
      }



    if($numBars > 0)
      $this->pdf->NbVal=$numBars;

        $vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
        $bGrafiek = $vBar * ($this->pdf->NbVal + 1);
        $eBaton = ($vBar * 50 / 100);


      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);

      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;
 
      if($minVal < 0)
        $extraY=abs($minVal * $unit); 
      else
        $extraY=0;
         

            
   foreach ($grafiek as $datum=>$data)
   {
      $aantalCategorien=count($data);
      $catCount=0;
      
      foreach($colors as $categorie=>$kleur)
      {
        if(isset($data[$categorie]) && isset($geldigeCategorien[$categorie]))
        {

          $val=$data[$categorie];
          $lval = $eBaton/$aantalCategorien;
          $xval = $XstartGrafiek + (1 + $i ) * $vBar + ($catCount * $lval) - $eBaton / 2;
          $yval = $YstartGrafiek + $nulYpos ;
          $hval = ($val * $unit);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);

         $this->pdf->SetTextColor(0,0,0);

         if($legendaPrinted[$datum] != 1)
         {
           $part=explode("-",$legenda[$datum]);
           $this->pdf->SetXY($xval,$yval+$extraY+2);
           $this->pdf->Cell($eBaton,0,$part[0],0,0,'C');
         }

         if($grafiekPunt[$categorie][$datum])
         {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
         $legendaPrinted[$datum] = 1;
         $catCount++;
        }
      }
      $i++;
     
   }

   $i=0;
   $YstartGrafiekLast=array();
   foreach ($grafiekNegatief as $datum=>$data)
   {
    
      foreach($data as $categorie=>$val)
      {
        if(isset($data[$categorie]) && isset($geldigeCategorien[$categorie]))
        {
          if(!isset($YstartGrafiekLast[$datum]))
            $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $lval = $eBaton/$aantalCategorien;
          $xval = $XstartGrafiek + (1 + $i ) * $vBar + ($catCount * $lval) - $eBaton / 2;
          $yval = $YstartGrafiek + $nulYpos ;
          $hval = $hval = ($val * $unit);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;

          if($grafiekPunt[$categorie][$datum])
          {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
          }
         }
       }
       $i++;
   }
   
  
   $i=0;

  
   $YDiag=$YstartGrafiek;
   $XDiag=$XstartGrafiek;
   foreach($datalijn as $datum=>$data)
   {
      foreach($data as $categorie=>$val)
      {
        if(isset($geldigeCategorien[$categorie]))
        {
        $extrax=($unit*0.5*-1);
        if($i <> 0)
          $extrax1=($unit*0.5*-1);

        $xval1[$categorie]  = $XstartGrafiek + (0 + $i ) * $vBar ;
        $xval2[$categorie] = $XstartGrafiek + (1 + $i ) * $vBar ;
        $yval2[$categorie] = $YstartGrafiek + ($unit*$val) + $nulYpos;
        

        if($i <>0 && isset($yval1[$categorie]))
          $this->pdf->line($xval1[$categorie], $yval1[$categorie], $xval2[$categorie], $yval2[$categorie],array('color'=>$colors[$categorie] ));
        $this->pdf->Rect($xval2[$categorie], $yval2[$categorie]-0.5, 1, 1 ,'F','',$colors[$categorie]);

        $yval1[$categorie] = $yval2[$categorie];
        }
      }
      $i++;
   }
  
   
   
   
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
  



  function VBarDiagram($w, $h, $data,$titel)
  {
      global $__appvar;
      $legendaWidth = 45;
      $grafiekPunt = array();
      $verwijder=array();
      
      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();

      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
      $this->pdf->setXY($XPage,$YPage+2);
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
      $this->pdf->Cell($w,4,$titel,0,1,'L');
      $this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array($this->pdf->koplijn[0],$this->pdf->koplijn[1],$this->pdf->koplijn[2]),'dash'=>0));
      $this->pdf->line($XPage,$YPage+$this->pdf->rowHeight+3,$XPage+$w,$YPage+$this->pdf->rowHeight+3);
      
      $YPage=$YPage+$h+15;
 
      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = $datum;
        $n=0;
        $minVal=0;
        $maxVal=100;
        
        foreach ($waarden as $categorie=>$categorieData)
        {
          $grafiek[$datum][$categorie]=$categorieData['aandeel']*100;
          if($categorieData['aandeel'] < 0)
          {
             unset($grafiek[$datum][$categorie]);
             $grafiekNegatief[$datum][$categorie]=$categorieData['aandeel']*100;
          }
          else
             $grafiekNegatief[$datum][$categorie]=0;

          if(!isset($colors[$categorie]))
            //$colors[$categorie]=array(rand(0,255),rand(0,255),rand(0,255));
            $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          $n++;
        }
      }



      $numBars = count($legenda);

      if($maxVal <= 100)
        $maxVal=100;
      elseif($maxVal < 125)
        $maxVal=125;

      if($minVal >= 0)
        $minVal = 0;
      elseif($minVal > -25)
        $minVal=-25;

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

      $margin = 0;
      $margeLinks=10;
      $XPage+=$margeLinks;
      $w-=$margeLinks;
      $w-=$legendaWidth;
      
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;

      $bGrafiek = ($w  ) ; // - legenda

      $n=0;
      foreach ($colors as $categorie=>$kleur)//array_reverse
      {
          $this->pdf->Rect($XstartGrafiek+$w+3 , $YstartGrafiek-$hGrafiek+$n*7+2, 2, 2, 'F',null,$colors[$categorie]);
          $this->pdf->SetXY($XstartGrafiek+$w+6 ,$YstartGrafiek-$hGrafiek+$n*7+1.5 );
          $this->pdf->MultiCell(45, 4,$this->categorieOmschrijving[$categorie],0,'L');
          $n++;
      }

      if($minVal < 0)
      {
        $unit = $hGrafiek / ($minVal + $maxVal);
        $nulYpos =  $unit * (-1 * $minVal);
      }
      else
      {
        $unit = $hGrafiek / $maxVal * -1;
        $nulYpos =0;
      }


      $horDiv = 4;
      $horInterval = $hGrafiek / $horDiv;
      $bereik = $hGrafiek/$unit;

      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      $this->pdf->SetTextColor(0,0,0);

      $stapgrootte = ceil(abs($bereik)/$horDiv);
      $top = $YstartGrafiek-$h;
      $bodem = $YstartGrafiek;
      $absUnit =abs($unit);

      $nulpunt = $YstartGrafiek + $nulYpos;
      $n=0;

      for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
      {
        $skipNull = true;
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1)." %",0,0,'R');
        $n++;
        if($n >20)
         break;
      }

      $n=0;
      for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        if($skipNull == true)
          $skipNull = false;
        else
        {
          $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
          $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte)." %",0,0,'R');
        }
        $n++;
        if($n >20)
          break;
      }



    if($numBars > 0)
      $this->pdf->NbVal=$numBars;

        $vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
        $bGrafiek = $vBar * ($this->pdf->NbVal + 1);
        $eBaton = ($vBar * 50 / 100);


      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);

      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;

   foreach ($grafiek as $datum=>$data)
   {
      foreach($data as $categorie=>$val)
      {
        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
          $hval = ($val * $unit);
//$this->pdf->SetDrawColor(255,255,255);
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
          
          /*
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);
         */
         if($legendaPrinted[$datum] != 1)
         {
           $part=explode("-",$legenda[$datum]);
          $this->pdf->SetXY($xval, $YstartGrafiek);
           $this->pdf->Cell($eBaton, 4, $part[0],0,0,'C');
           //$this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+4,$legenda[$datum],0);
         }
         if($grafiekPunt[$categorie][$datum])
         {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(194,179,157));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
         $legendaPrinted[$datum] = 1;
      }
      $i++;
   }

   $i=0;
   $YstartGrafiekLast=array();
   foreach ($grafiekNegatief as $datum=>$data)
   {
      foreach($data as $categorie=>$val)
      {
          if(!isset($YstartGrafiekLast[$datum]))
            $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
          $hval = ($val * $unit);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);

         if($grafiekPunt[$categorie][$datum])
         {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(194,179,157));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
      }
      $i++;
   }
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
 //   $this->pdf->SetDrawColor(0,0,0);
  }
}
?>
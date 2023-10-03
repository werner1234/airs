<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.13 $

$Log: RapportOIB_L49.php,v $
Revision 1.13  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.12  2016/07/13 16:06:39  rvv
*** empty log message ***

Revision 1.11  2014/12/29 13:55:30  rvv
*** empty log message ***

Revision 1.10  2014/12/28 14:29:08  rvv
*** empty log message ***

Revision 1.9  2014/12/24 16:00:30  rvv
*** empty log message ***

Revision 1.8  2014/12/13 19:24:44  rvv
*** empty log message ***

Revision 1.7  2014/12/10 16:58:25  rvv
*** empty log message ***

Revision 1.6  2014/12/06 18:13:44  rvv
*** empty log message ***

Revision 1.5  2014/03/27 14:59:18  rvv
*** empty log message ***

Revision 1.4  2014/03/22 15:47:14  rvv
*** empty log message ***

Revision 1.3  2013/12/18 17:10:42  rvv
*** empty log message ***

Revision 1.2  2013/12/14 17:16:30  rvv
*** empty log message ***

Revision 1.1  2013/06/05 15:56:07  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L42.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");

class RapportOIB_L49
{

	function RapportOIB_L49($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		$this->pdf->rapport_titel = "";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->jaarGeleden=date("Y-m-d",mktime(0,0,0,date('m',$this->pdf->rapport_datum),date('d',$this->pdf->rapport_datum),date('Y',$this->pdf->rapport_datum)-1));

    $this->att=new ATTberekening_L42($this);
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }


	function writeRapport()
	{
    $this->pdf->AddPage();
    checkPage($this->pdf);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $maakNotities=$this->maakIndex();
    if($maakNotities)
      $this->maakNotities();
    getTypeGrafiekData($this,'Beleggingscategorie');
    $this->Categorieverdeling($this->pdf->grafiekData['Beleggingscategorie'],$this->pdf->veldOmschrijvingen['Beleggingscategorie']);//,$modelData);
    paginaVoet($this->pdf);
  }
  
  function maakNotities()
  {
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize+2);
    $this->pdf->setY($this->pdf->rapportYstart+2); 
  	$this->pdf->SetX($this->pdf->marge);
		$this->pdf->Cell(150,4,'Notities', 0, "L");
    $this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array($this->pdf->achtergrondlijn[0],$this->pdf->achtergrondlijn[1],$this->pdf->achtergrondlijn[2]),'dash'=>0));
    $stappen=25;
    $yStart=$this->pdf->rapportYstart+9;
    $yStop=210-$this->pdf->margeOnder;
    $w=140;
    $stap=($yStop-$yStart)/$stappen;
    for($i=0;$i<=$stappen;$i++)
    {
      $this->pdf->Line($this->pdf->marge,$yStart+$i*$stap,$w,$yStart+$i*$stap);
    }
  }
  
  function maakIndex()
  {
    $db=new DB();
    
    $indexFondSize=$this->pdf->rapport_fontsize;
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  else
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";


	  $DB=new DB();
	  $perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);
    
    $query="SELECT Vermogensbeheerder FROM Portefeuilles WHERE Portefeuille='".$this->portefeuille."'";
    $db->SQL($query);
    $verm=$db->lookupRecord();
    
    $indices=array();
    $query="SELECT Fondsen.Omschrijving,Fondsen.Omschrijving,Fondsen.Valuta, IndexPerBeleggingscategorie.Fonds 
    FROM IndexPerBeleggingscategorie
    INNER JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds = Fondsen.Fonds
    WHERE 
    IndexPerBeleggingscategorie.Vermogensbeheerder='".$verm['Vermogensbeheerder']."' AND IndexPerBeleggingscategorie.Portefeuille='".$this->portefeuille."' 
    AND IndexPerBeleggingscategorie.vanaf < '".$this->rapportageDatum."'
    ORDER BY IndexPerBeleggingscategorie.vanaf desc limit 1 ";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      $data['type']='Benchmark Beheerder';
      $indices[$data['Fonds']]=$data;
    }
        
    $query="SELECT Portefeuilles.SpecifiekeIndex, Fondsen.Omschrijving,Fondsen.Valuta
    FROM Portefeuilles
    INNER JOIN Fondsen ON Portefeuilles.SpecifiekeIndex = Fondsen.Fonds
    WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      $data['type']='Benchmark Family Capital Trust';
      if($data['SpecifiekeIndex'] <> '')
        $indices[$data['SpecifiekeIndex']]=$data;
    }
    
    //echo $this->pdf->witCell;exit;
    if(count($indices)<>0)
    {
      $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize+2);
      $this->pdf->setY($this->pdf->rapportYstart+2); 
  	  $this->pdf->SetX($this->pdf->marge);
		  $this->pdf->Cell(150,4,'Weging en ontwikkeling benchmarks', 0, "L");
      $this->pdf->fillCell = array(1,0,1,0,1,0,1,0,1);
      $this->pdf->SetWidths(array(75,$this->pdf->witCell,15,$this->pdf->witCell,20,$this->pdf->witCell,15));
      $this->pdf->SetAligns(array('L','C','R','C','R','C','R'));
      $tmp=array_sum($this->pdf->widths);
      $this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array($this->pdf->koplijn[0],$this->pdf->koplijn[1],$this->pdf->koplijn[2]),'dash'=>0));
      $this->pdf->Line($this->pdf->marge,$this->pdf->rapportYstart+$this->pdf->rowHeight+3,$tmp+$this->pdf->marge,$this->pdf->rapportYstart+$this->pdf->rowHeight+3);
      $this->pdf->Ln(16);
    }

    
    $indexData=array();
    foreach($indices as $hoofdIndex=>$hoofdIndexData)
    {

      foreach ($perioden as $periode=>$datum)
      {
        $indexData[$hoofdIndex]['fondsKoers_'.$periode]=getFondsKoers($hoofdIndex,$datum);
      //  $indexData[$hoofdIndex]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
      }
     	$indexData[$hoofdIndex]['performanceJaar'] = ($indexData[$hoofdIndex]['fondsKoers_eind'] - $indexData[$hoofdIndex]['fondsKoers_jan'])    / ($indexData[$hoofdIndex]['fondsKoers_jan']/100 );
			$indexData[$hoofdIndex]['performance'] =     ($indexData[$hoofdIndex]['fondsKoers_eind'] - $indexData[$hoofdIndex]['fondsKoers_begin']) / ($indexData[$hoofdIndex]['fondsKoers_begin']/100 );
  		//$indexData[$hoofdIndex]['performanceEurJaar'] = ($indexData[$hoofdIndex]['fondsKoers_eind']*$indexData[$hoofdIndex]['valutaKoers_eind'] - $indexData[$hoofdIndex]['fondsKoers_jan']  *$indexData[$hoofdIndex]['valutaKoers_jan'])/(  $indexData[$hoofdIndex]['fondsKoers_jan']*  $indexData[$hoofdIndex]['valutaKoers_jan']/100 );
			//$indexData[$hoofdIndex]['performanceEur'] =     ($indexData[$hoofdIndex]['fondsKoers_eind']*$indexData[$hoofdIndex]['valutaKoers_eind'] - $indexData[$hoofdIndex]['fondsKoers_begin']*$indexData[$hoofdIndex]['valutaKoers_begin'])/($indexData[$hoofdIndex]['fondsKoers_begin']*$indexData[$hoofdIndex]['valutaKoers_begin']/100 );
      $this->pdf->SetFont($this->pdf->rapport_font,"B",$indexFondSize);
      $this->pdf->SetFillColor($this->pdf->achtergrondKop[0],$this->pdf->achtergrondKop[1],$this->pdf->achtergrondKop[2]);
      $this->pdf->row(array($hoofdIndexData['type'],'','','','','',''));
      $n=$this->switchColor($n); 
      $this->pdf->SetFont($this->pdf->rapport_font,"BI",$indexFondSize);
      $this->pdf->row(array('Benchmark','','','','% Periode','','% YtD'));
      $n=$this->switchColor($n); 
      $this->pdf->SetFont($this->pdf->rapport_font,"",$indexFondSize);
      $this->pdf->row(array($hoofdIndexData['Omschrijving'].',','','','',$this->formatGetal($indexData[$hoofdIndex]['performance'],1).'%','',$this->formatGetal($indexData[$hoofdIndex]['performanceJaar'],1).'%' ));
      $n=$this->switchColor($n); 
      $this->pdf->row(array('    bestaande uit:','','Weging','','','',''));
      $n=$this->switchColor($n); 
      //$this->pdf->row(array('','','Weging','','','',''));
      //$n=$this->switchColor($n); 
      $query="SELECT benchmarkverdeling.fonds, benchmarkverdeling.percentage, Fondsen.Omschrijving,Fondsen.Valuta
      FROM benchmarkverdeling 
      INNER JOIN Fondsen ON benchmarkverdeling.fonds = Fondsen.Fonds
      WHERE benchmarkverdeling.benchmark='".$hoofdIndex."'";
      $db->SQL($query);
      $db->Query();
      while($data=$db->nextRecord())
      {
        foreach ($perioden as $periode=>$datum)
        {
         $indexData[$data['fonds']]['fondsKoers_'.$periode]=getFondsKoers($data['fonds'],$datum);
         //$indexData[$data['fonds']]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
        }
     	  $indexData[$data['fonds']]['performanceJaar'] = ($indexData[$data['fonds']]['fondsKoers_eind'] - $indexData[$data['fonds']]['fondsKoers_jan'])    / ($indexData[$data['fonds']]['fondsKoers_jan']/100 );
			  $indexData[$data['fonds']]['performance'] =     ($indexData[$data['fonds']]['fondsKoers_eind'] - $indexData[$data['fonds']]['fondsKoers_begin']) / ($indexData[$data['fonds']]['fondsKoers_begin']/100 );
      //  listarray($data);
           $this->pdf->row(array('    '.$data['Omschrijving'],'',
             $this->formatGetal($data['percentage'],1),'',
             $this->formatGetal($indexData[$data['fonds']]['performance'],1).'%','',
             $this->formatGetal($indexData[$data['fonds']]['performanceJaar'],1).'%' ));
        $n=$this->switchColor($n);      

      }
    }
    
    
        
    if(count($indices)==0)
      return 1;

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
  
  
  
  function Categorieverdeling($data,$omschrijvingen)
	{
		global $__appvar;
    $startY=$this->pdf->GetY();
    $this->pdf->setXY(160,$this->pdf->rapportYstart);
    //$this->pdf->debug=true;
    
    PieChart($this->pdf,120, 70, $data['grafiek'], '%l', $data['grafiekKleur'],'Assetverdeling portefeuille '.getKwartaal($this->pdf->rapport_datum).' kwartaal '.date('Y',$this->pdf->rapport_datum),'R');
    $totalen=array();
    $this->pdf->setWidths(array(140,100-$this->pdf->witCell-4,$this->pdf->witCell,20));
    $this->pdf->SetAligns(array('L','L','','R'));
    $this->pdf->Ln(8);

	  $this->pdf->fillCell = array(0,1,0,1);
    $n=0;
	  foreach($data['port']['procent'] as $categorie=>$percentage)
    {
      $this->switchColor($n);
      $n++;
      $this->pdf->row(array('',$omschrijvingen[$categorie],'',$this->formatGetal($percentage*100,0).'%'));
      $totalen['percentage']+=$percentage;

    }
    $this->switchColor($n);
    $this->pdf->row(array('','Totaal','',$this->formatGetal($totalen['percentage']*100,0).'%'));
    unset($this->pdf->fillCell);
    $eindY=$this->pdf->GetY();
  }
  
  
  function Vermogensverdeling()
  {
    
    
      $index=new indexHerberekening();
  $maanden=$index->getMaanden(db2jul($this->rapportageDatumVanaf),db2jul($this->rapportageDatum));
  $indexData=array();
  foreach ($maanden as $periode)
  {
    $indexData[]=array('datum'=>$periode['stop'],'index'=>100,'waardeHuidige'=>0,'specifiekeIndex'=>100,'extra'=>array('cat'=>array('LIQ'=>0,'VAR'=>0,'ZAK'=>0)));
  }
  
  
  $indexDataReal = $index->getWaarden($indexDatum ,$this->rapportageDatum ,$this->portefeuille);
  foreach ($indexData as $index=>$maanden)
  {
    foreach ($indexDataReal as $realData)
    {
      if($realData['datum'] == $maanden['datum'])
        $indexData[$index]=$realData;
    }
  }
  
  
  
  
  //  listarray($indexData);
//  $indexData = $this->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);
//listarray($indexData);
//exit;

foreach ($indexData as $index=>$data)
{
  if($data['datum'] != '0000-00-00')
  {
    $rendamentWaarden[] = $data;
    $grafiekData['Datum'][] = $data['datum'];
    $grafiekData['Index'][] = $data['index']-100;
    $grafiekData['benchmarkIndex'][] = $data['specifiekeIndex']-100;
  //  foreach ($data['categorieVerdeling'] as $categorie=>$waarde)
    foreach ($data['extra']['cat'] as $categorie=>$waarde)
    {
      $barGraph['Index'][$data['datum']][$categorie] = $waarde/$data['waardeHuidige']*100;
    }
  }
}

  		  if (count($barGraph) > 0)
		  {
		      $this->pdf->SetXY($this->pdf->marge,40)		;//112
		    	$this->pdf->Cell(0, 5, 'Vermogensverdeling', 0, 1);
  		    $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+180,$this->pdf->GetY());
		      $this->pdf->SetXY(10,150)		;//112
		      $this->VBarDiagram(150, 100, $barGraph['Index']);
		  }
  }
  

  
  


   

   

function addCategoriePie($grafiekData,$grafiekKleurData)
{

}

function BerekenMutaties($beginDatum,$eindDatum,$portefeuille)
	{
		$totaalWaarde =array();
		$db = new DB();

    if(db2jul($beginDatum) < db2jul($this->pdf->PortefeuilleStartdatum))
      $wegingsDatum=$this->pdf->PortefeuilleStartdatum;
    else
      $wegingsDatum=$beginDatum;

		$startjaar=substr($beginDatum,0,4);
		if(db2jul($beginDatum) == mktime (0,0,0,1,1,$startjaar))
		 $beginjaar = true;
		else
		 $beginjaar = false;

		$koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,'EUR',true);

		$fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($portefeuille,$beginDatum,$beginjaar,'EUR',$beginDatum);

	  foreach ($fondswaarden['beginmaand'] as $regel)
	  {
      $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
      if($regel['type']=='rente' && $regel['fonds'] != '')
        $totaalWaarde['renteBegin'] += $regel['actuelePortefeuilleWaardeEuro'];
	  }

	  $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$eindDatum,false,'EUR',$beginDatum);
    $categorieVerdeling=$this->categorieVolgorde;

   // listarray($categorieVerdeling);
	  foreach ($fondswaarden['eindmaand'] as $regel)
	  {
      $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];

      if($regel['type']=='fondsen')
      {
        $totaalWaarde['beginResultaat'] += $regel['beginPortefeuilleWaardeEuro'];
        $totaalWaarde['eindResultaat'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling[$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rente' && $regel['fonds'] != '')
      {
        $totaalWaarde['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling['VAR'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rekening')
      {
        $categorieVerdeling['LIQ'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
	  }

	  $ongerealiseerd=($totaalWaarde['eindResultaat']-$totaalWaarde['beginResultaat']);
	  $DB=new DB();

	$query = "SELECT ".
	"SUM(((TO_DAYS('".$eindDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
	"  / (TO_DAYS('".$eindDatum."') - TO_DAYS('".$wegingsDatum."')) ".
	"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
	"FROM  (Rekeningen, Portefeuilles )
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	"WHERE ".
	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Boekdatum > '".$beginDatum."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$eindDatum."' AND ".
	"Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
	$DB->SQL($query);
	$DB->Query();
	$weging = $DB->NextRecord();

  $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
	$performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging[totaal2]) / $gemiddelde) * 100;


	  $waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
		$stortingen = getStortingen($portefeuille,$beginDatum, $eindDatum);
		$onttrekkingen = getOnttrekkingen($portefeuille,$beginDatum, $eindDatum);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;

		$query = "SELECT SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers)  AS totaalkosten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Kosten = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $kosten = $db->lookupRecord();

    $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) AS totaalOpbrengsten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Opbrengst = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $opbrengsten = $db->lookupRecord();

    $opgelopenRente=$totaalWaarde['renteEind']-$totaalWaarde['renteBegin'];
    $valutaResultaat=$resultaatVerslagperiode-($koersResultaat+$ongerealiseerd+$opbrengsten['totaalOpbrengsten']+$kosten['totaalkosten']+$opgelopenRente);
    $ongerealiseerd+=$valutaResultaat;

    $data['periode']= $beginDatum."->".$eindDatum;
    $data['periodeForm']= date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum));
    $data['waardeBegin']=round($totaalWaarde['begin'],2);
    $data['waardeHuidige']=round($totaalWaarde['eind'],2);
    $data['waardeMutatie']=round($waardeMutatie,2);
    $data['stortingen']=round($stortingen,2);
    $data['onttrekkingen']=round($onttrekkingen,2);
    $data['resultaatVerslagperiode'] = round($resultaatVerslagperiode,2);
    $data['kosten'] = round($kosten['totaalkosten'],2);
    $data['opbrengsten'] = round($opbrengsten['totaalOpbrengsten'],2);
    $data['performance'] =$performance;
    $data['ongerealiseerd'] =$ongerealiseerd;
    $data['rente'] = $opgelopenRente;
    $data['gerealiseerd'] =$koersResultaat;
    $data['categorieVerdeling']=$categorieVerdeling;
    return $data;

	}


  function VBarDiagram($w, $h, $data)
  {
      global $__appvar;
      $legendaWidth = 0;
      $grafiekPunt = array();
      $verwijder=array();

      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = $datum;
        $n=0;
        $minVal=0;
        $maxVal=100;
        foreach ($waarden as $categorie=>$waarde)
        {
          if($categorie=='LIQ')
            $categorie='Liquiditeiten';
          $grafiek[$datum][$categorie]=$waarde;
          $grafiekCategorie[$categorie][$datum]=$waarde;
          $categorien[$categorie] = $n;
          $categorieId[$n]=$categorie ;

          $maxVal=max(array($maxVal,$waarde));
          $minVal=min(array($minVal,$waarde));

          if($waarde < 0)
          {
             unset($grafiek[$datum][$categorie]);
             $grafiekNegatief[$datum][$categorie]=$waarde;
          }
          else
             $grafiekNegatief[$datum][$categorie]=0;


          if(!isset($colors[$categorie]))
            $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          $n++;
        }
      }



      $numBars = count($legenda);
      //$numBars=12;

      if($color == null)
      {
        $color=array(155,155,155);
      }

      if($maxVal <= 100)
        $maxVal=100;
      elseif($maxVal < 125)
        $maxVal=125;


      if($minVal >= 0)
        $minVal = 0;
      elseif($minVal > -25)
        $minVal=-25;



      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

      $n=0;
      foreach (array_reverse($categorieId) as $categorie)
      {
        if(is_array($grafiekCategorie[$categorie]))
        {
          $this->pdf->Rect($XstartGrafiek+$bGrafiek+3 , $YstartGrafiek-$hGrafiek+$n*10+2, 2, 2, 'DF',null,$colors[$categorie]);
          $this->pdf->SetXY($XstartGrafiek+$bGrafiek+6 ,$YstartGrafiek-$hGrafiek+$n*10+1.5 );
          $this->pdf->Cell(20, 3,$categorie,0,0,'L');
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


      $horDiv = 5;
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
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1)." %",0,0,'R');
        $n++;
        if($n >20)
         break;
      }

      $n=0;
      for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
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

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);

         if($legendaPrinted[$datum] != 1)
         {
          $this->pdf->SetXY($xval,$YstartGrafiek+1);
          //echo $legenda[$datum]." $xval,$YstartGrafiek+1 <br>\n";
          $this->pdf->Cell($eBaton,4,$legenda[$datum],0,0,'C');//$this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+4,$legenda[$datum],0);
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
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
      }
      $i++;
   }
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
}



?>
<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/01/04 18:57:42 $
File Versie					: $Revision: 1.1 $

$Log: RapportPERFG_L86.php,v $
Revision 1.1  2020/01/04 18:57:42  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_86/RapportHUIS_L86.php");


class RapportPERFG_L86
{
	function RapportPERFG_L86($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->huis = new RapportHUIS_L86($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->pdf->rapport_type = "PERFG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Historische performanceverloop";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	  $this->rapportageDatumVanaf = "$RapStartJaar-01-01";


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




//listarray($this->categorieVolgorde);
		// voor data
		$this->pdf->widthA = array(1,95,25,5,25,5,25,5,25,5,25,5,25,5,25,5);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R');


  	$this->pdf->widthB = array(1,95,30,10,30,115);
		$this->pdf->alignB = array('L','L','R','R','R');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->AddPage();
    $this->pdf->templateVars['PERFGPaginas']=$this->pdf->page;

		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];


$DB = new DB();
$query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE periode IN('j','m') AND Portefeuille = '".$this->portefeuille."' AND Categorie = 'Totaal' ORDER BY Datum ASC LIMIT 1 ";
$DB->SQL($query);
$DB->Query();
$datum = $DB->nextRecord();


if($datum['id'] > 0 && $this->pdf->lastPOST['perfPstart'] == 1)
{
  if($datum['month'] <10)
    $datum['month'] = "0".$datum['month'];
  $start = $datum['year'].'-'.$datum['month'].'-01';
}
else
  $start = substr($this->pdf->PortefeuilleStartdatum,0,10);
$eind = $this->rapportageDatum;

$datumStart = db2jul($start);
$datumStop  = db2jul($eind);

$this->index = new indexHerberekening();
$indexData = $this->getWaarden($start,$eind,$this->portefeuille,$this->pdf->portefeuilledata['SpecifiekeIndex'],'jaar');

$perioden=array();
foreach($indexData as $periode)
{
  $perioden[]=array('start'=>substr($periode['periode'],0,10),'stop'=>substr($periode['periode'],12,10));
}
    
    $consolidatiePerf='';
    if(is_array($this->pdf->portefeuilles) && count($this->pdf->portefeuilles)>0)
    {
      $consolidatiePerf = $this->huis->benchmarkBerekening($perioden);
    }
foreach ($indexData as $index=>$data)
{
  if($data['datum'] != '0000-00-00')
  {
  
    
    $rendamentWaarden[] = $data;
    $grafiekData['Datum'][] = $data['datum'];
    $grafiekData['Index'][] = $data['index']-100;
    if(is_array($consolidatiePerf))
    {
      $grafiekData['benchmarkIndex'][] = $consolidatiePerf[$data['datum']];
    }
    else
    {
      $grafiekData['benchmarkIndex'][] = $data['specifiekeIndex']-100;
    }

  }
}
   // listarray($grafiekData);

  if(count($rendamentWaarden) > 0)
   {
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $this->pdf->underlinePercentage=0.8;
        //$this->pdf->SetFillColor(137,188,255);
     $this->pdf->SetFillColor($this->pdf->rapport_background_fill[0],$this->pdf->rapport_background_fill[1],$this->pdf->rapport_background_fill[2]);


     $totaal=array();
        $perioden=array('jaar','totaal');
        $jaarRendement=array();
        foreach($perioden as $periode)
          $jaarRendement[$periode]=100;
        
        $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
        $fill=false;
		    foreach ($rendamentWaarden as $row)
		    {
		      $resultaat = $row['Opbrengsten']-$row['Kosten'];
		      $datum = db2jul($row['datum']);
          $jaar = date("Y",$datum);
          if(isset($lastJaar) && $lastJaar!=$jaar)
          {
            $this->printTotaal($totaal,$lastJaar);
            $totaal['jaar']=array();
            $jaarRendement['jaar']=100;
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
          }
                                       
          foreach($perioden as $periode)
          {
            $jaarRendement[$periode] = ($jaarRendement[$periode]  * (100+$row['performance'])/100);
            //echo $row['datum']." ".round($row['performance'],6). " ".round($row['index'],6)." ".round($jaarRendement[$periode],6)." <br>\n";
		                           if(!isset($totaal[$periode]['waardeBegin']))
		                             $totaal[$periode]['waardeBegin']=$row['waardeBegin'];
		                           $totaal[$periode]['Waarde'] = $row['waardeHuidige'];
		                           $totaal[$periode]['Resultaat'] += $row['resultaatVerslagperiode'];
		                           $totaal[$periode]['Gerealiseerd'] += $row['gerealiseerd'];
		                           $totaal[$periode]['Ongerealiseerd'] += $row['ongerealiseerd'];
		                           $totaal[$periode]['Opbrengsten'] += $row['opbrengsten'];
		                           $totaal[$periode]['Kosten'] += $row['kosten'];
		                           $totaal[$periode]['Rente'] += $row['rente'];
		                           $totaal[$periode]['StortingenOntrekkingen'] += $row['stortingen']-$row['onttrekkingen'];
		                           $totaal[$periode]['Rendament'] = $row['index'];
                               $totaal[$periode]['JaarRendament'] = $jaarRendement[$periode];
          }
  	      $lastJaar=$jaar;
		    }
        $this->printTotaal($totaal,$lastJaar);

            
            $this->pdf->CellBorders = array('','TS','TS','TS','TS','TS','TS','TS','TS','','TS'); 
            $this->pdf->fillCell=array();
            $this->pdf->row(array('','','','','','','','','','','','')); 
            $this->pdf->SetY($this->pdf->GetY()-4);


        $this->pdf->ln(3);
        
        //$this->pdf->CellBorders = array('','UU','UU','UU','UU','UU','UU','UU','UU','UU','','UU');
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->CellBorders = array();
		    $this->pdf->row(array(vertaalTekst('Totaal',$this->pdf->rapport_taal),
		                           $this->formatGetal($totaal['totaal']['waardeBegin'],2),
		                           $this->formatGetal($totaal['totaal']['StortingenOntrekkingen'],2),
		                           $this->formatGetal($totaal['totaal']['Gerealiseerd']+$totaal['totaal']['Ongerealiseerd'],2),
		                           $this->formatGetal($totaal['totaal']['Opbrengsten'],2),
		                           $this->formatGetal($totaal['totaal']['Kosten'],2),
		                           $this->formatGetal($totaal['totaal']['Rente'],2),
		                           $this->formatGetal($totaal['totaal']['Resultaat'],2),
		                           $this->formatGetal($totaal['totaal']['Waarde'],2),
		                           '',
		                           $this->formatGetal($totaal['totaal']['Rendament']-100,2)
		                           ));//$this->formatGetal($totaalRendamentIndex-100,2)
		    $this->pdf->CellBorders = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		  }


		  $query="SELECT Omschrijving FROM Fondsen WHERE Fonds='".mysql_real_escape_string($this->pdf->portefeuilledata['SpecifiekeIndex'])."'";
    $DB->SQL($query);
    $DB->Query();
    $benchmark = $DB->nextRecord();

		  if (count($grafiekData) > 1)
		  {
        $yShift=-3;
        $this->pdf->SetXY(8,111+$yShift);//104
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  		  $this->pdf->Cell(0, 5, vertaalTekst('Rendement',$this->pdf->rapport_taal).' ('.
                               vertaalTekst('cumulatief',$this->pdf->rapport_taal).' '.
                               vertaalTekst('in',$this->pdf->rapport_taal).' %)', 0, 1);
  		  $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
  		  $this->pdf->SetXY(15,117+$yShift)		;//112
        $valX = $this->pdf->GetX();
        $valY = $this->pdf->GetY();
        //function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
        $kleuren=array(array(0,56,90),array(61,59,56));
        $this->LineDiagram(270, 60, $grafiekData,$kleuren,0,0,6,5,1);//50
        $this->pdf->SetXY($valX, $valY + 75+$yShift);
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        foreach($kleuren as $index=>$kleur)
        {
          $this->pdf->rect($this->pdf->getX()-2,$this->pdf->getY()+1,2,2,'F','',$kleur);
          if($index==0)
            $this->pdf->Cell(50, 4, 'Portefeuille', 0, 0, "L");
          if($index==1)
            $this->pdf->Cell(50, 4,$benchmark['Omschrijving'], 0, 0, "L");
        }

		  }

	   $this->pdf->fillCell = array();



	}


  function printTotaal($totaal,$jaar)
{
   	    $this->pdf->row(array(vertaalTekst('Totaal '.$jaar,$this->pdf->rapport_taal),
		                           $this->formatGetal($totaal['jaar']['waardeBegin'],2),
		                           $this->formatGetal($totaal['jaar']['StortingenOntrekkingen'],2),
		                           $this->formatGetal($totaal['jaar']['Gerealiseerd']+$totaal['jaar']['Ongerealiseerd'],2),
		                           $this->formatGetal($totaal['jaar']['Opbrengsten'],2),
		                           $this->formatGetal($totaal['jaar']['Kosten'],2),
		                           $this->formatGetal($totaal['jaar']['Rente'],2),
		                           $this->formatGetal($totaal['jaar']['Resultaat'],2),
		                           $this->formatGetal($totaal['jaar']['Waarde'],2),
		                           $this->formatGetal($totaal['jaar']['JaarRendament']-100,2),
                               $this->formatGetal($totaal['totaal']['Rendament']-100,2)
		                           ));
 
}

function formatGetalLength ($getal,$decimaal,$gewensteLengte)
{
 $lengte = strlen(round($getal));
 if($getal < 0)
  $lengte --;
 $mogelijkeDecimalen = $gewensteLengte - $lengte;
 if($lengte >$gewensteLengte)
   $decimaal = 0;
 elseif ($decimaal > $mogelijkeDecimalen)
   $decimaal = $mogelijkeDecimalen;
 return number_format($getal,$decimaal,',','');
}
  
  
  
  function getWaarden($datumBegin,$datumEind,$portefeuille,$specifiekeIndex='',$methode='maanden',$valuta='EUR')
  {
    if(is_array($portefeuille))
    {
      $portefeuilles=$portefeuille[1];
      $portefeuille=$portefeuille[0];
    }
    $db=new DB();
    $db2=new DB();
    $julBegin = db2jul($datumBegin);
    $beginDatum=date("Y-m-d",$julBegin);
    $julEind = db2jul($datumEind);
    
    $eindjaar = date("Y",$julEind);
    $eindmaand = date("m",$julEind);
    $beginjaar = date("Y",$julBegin);
    $startjaar = date("Y",$julBegin);
    $beginmaand = date("m",$julBegin);
    $begindag = date("d",$julBegin);
    
    $vorigeIndex = 100;
    $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
    $datum=array();
    
    if($methode=='maanden')
    {
      $datum=$this->index->getMaanden($julBegin,$julEind);
      $type='m';
    }
    elseif($methode=='dagKwartaal')
    {
      $datum=$this->index->getDagen($julBegin,$julEind);
      $type='dk';
    }
    elseif($methode=='kwartaal')
    {
      $datum=$this->index->getKwartalen($julBegin,$julEind);
      $type='k';
    }
    elseif($methode=='jaar')
    {
      $datum=$this->index->getJaren($julBegin,$julEind);
      $type='j';
    }
    elseif($methode=='TWR')
    {
      $datum=$this->index->getTWRstortingsdagen($portefeuille,$julBegin,$julEind,true);
      $type='t';
    }
    elseif($methode=='dagYTD')
    {
      //$datum=$this->getDagen($julBegin,$julEind,'jaar');
      $datum=array();
      $newJul=$julBegin;
      while($newJul < $julEind)
      {
        $newJul=$newJul+86400;
        $datum[]=array('start'=>date('Y-m-d',$julBegin),'stop'=>date('Y-m-d',$newJul));
      }
      $type='dy';
    }
    elseif ($methode=='halveMaanden')
    {
      $datum=$this->index->getHalveMaanden($julBegin,$julEind);
      $type='2w';
    }
    elseif($methode=='weken')
    {
      $datum=$this->index->getWeken($julBegin,$julEind);
      $type='w';
    }
    elseif($methode=='dagen')
    {
      $datum=$this->index->getDagen2($julBegin,$julEind);
      $type='d';
    }
    /*
      if($i==0)
        $datum[$i]['start']=$datumBegin;
      else
        $datum[$i]['start']=jul2db(mktime (0,0,0,$beginmaand+$i,0,$startjaar));
      $datum[$i]['stop']=$datumEind;
    */
    
    $i=1;
    $indexData=array();
    $indexData['index']=100;
    $indexData['specifiekeIndex']=100;
    $kwartaalBegin=100;
    
    $huidigeIndex=$specifiekeIndex;
    $jsonOutput=array('label'=>$portefeuille,'data'=>array());
    foreach ($datum as $periode)
    {
      if($specifiekeIndex != '')
      {
        //if($specifiekeIndex )
        /*
//        $query="SELECT specifiekeIndex FROM HistorischeSpecifiekeIndex WHERE portefeuille='$portefeuille' AND tot > '".$periode['stop']."' ORDER BY tot desc limit 1";
        $db->SQL($query);
        $oldIndex=$db->lookupRecord();
        if($oldIndex['specifiekeIndex'] <> '')
        {
          $specifiekeIndex=$oldIndex['specifiekeIndex'];
          unset($startSpecifiekeIndexKoers);
        }
        else
        {
          if($huidigeIndex <> $specifiekeIndex)
            unset($startSpecifiekeIndexKoers);
          $specifiekeIndex=$huidigeIndex;
        }
        */
        if(empty($startSpecifiekeIndexKoers))
        {
          $query = "SELECT Koers FROM Fondskoersen WHERE fonds = '".$specifiekeIndex."' AND Datum <= '".$periode['start']."' ORDER BY Datum DESC limit 1 ";
          $db->SQL($query);
          $specifiekeIndexData = $db->lookupRecord();
          $startSpecifiekeIndexKoers=$specifiekeIndexData['Koers'];
        }
        $query = "SELECT Koers FROM Fondskoersen WHERE fonds = '".$specifiekeIndex."' AND Datum <= '".$periode['stop']."' ORDER BY Datum DESC limit 1 ";
        $db->SQL($query);
        $specifiekeIndexData = $db->lookupRecord();
        $specifiekeIndexKoers = $specifiekeIndexData['Koers'];
      }
      $specifiekeIndexWaarden[$i] =($specifiekeIndexKoers/$startSpecifiekeIndexKoers)*100;
      
      
      $query = "SELECT indexWaarde, Datum, PortefeuilleWaarde, PortefeuilleBeginWaarde, Stortingen, Onttrekkingen, Opbrengsten, Kosten ,Categorie, gerealiseerd,ongerealiseerd,rente,extra
		            FROM HistorischePortefeuilleIndex
		            WHERE
		            Categorie = 'Totaal' AND periode='$type' AND
		            portefeuille = '".$portefeuille."' AND
		            Datum = '".substr($periode['stop'],0,10)."' ";
  
      $gecombineerdeMaanden=array();
      if($db->QRecords($query) == 0 && $type=='j')
      {
        $somWaarden=array('Stortingen','Opbrengsten','Kosten','gerealiseerd','ongerealiseerd','rente');
        $query = "SELECT indexWaarde, Datum, PortefeuilleWaarde, PortefeuilleBeginWaarde, Stortingen, Onttrekkingen, Opbrengsten, Kosten ,Categorie, gerealiseerd,ongerealiseerd,rente,extra
		            FROM HistorischePortefeuilleIndex
		            WHERE
		            Categorie = 'Totaal' AND periode='m' AND
		            portefeuille = '".$portefeuille."' AND
		            year(Datum) = '".substr($periode['stop'],0,4)."' ";
        $db2->SQL($query);
        $db2->Query();
        while($dbData = $db2->nextRecord())
        { //
          if(!isset($gecombineerdeMaanden['PortefeuilleBeginWaarde']))
            $gecombineerdeMaanden=$dbData;
          else
          {
       
            $gecombineerdeMaanden['PortefeuilleWaarde'] = $dbData['PortefeuilleWaarde'];
            $gecombineerdeMaanden['extra'] = $dbData['extra'];
            foreach ($somWaarden as $veld)
            {
              $gecombineerdeMaanden[$veld] += $dbData[$veld];
            }
            $gecombineerdeMaanden['indexWaarde'] = ((1 + $gecombineerdeMaanden['indexWaarde']) * (1 + $indexData['indexWaarde'])) - 1;
          }
        }
        
      }
      
      if(db2jul($periode['start']) == db2jul($periode['stop']))
      {
      
      }
      elseif($db->QRecords($query) > 0 && ($valuta == 'EUR' || $valuta == '' || $this->forceDbLoad==true) )
      {
        $dbData = $db->nextRecord();
        $indexData['database']=1;
        $indexData['periodeForm'] = jul2form(db2jul($periode['start']))." - ".jul2form(db2jul($periode['stop']));
        $indexData['periode']= $periode['start']."->".$periode['stop'];
        $indexData['waardeMutatie'] = $dbData['PortefeuilleWaarde']-$dbData['PortefeuilleBeginWaarde'];
        $indexData['waardeBegin'] = $dbData['PortefeuilleWaarde']-$indexData['waardeMutatie'];
        $indexData['waardeHuidige'] = $dbData['PortefeuilleWaarde'];
        $indexData['stortingen'] = $dbData['Stortingen'];
        $indexData['onttrekkingen'] = $dbData['Onttrekkingen'];
        $indexData['resultaatVerslagperiode'] =  $indexData['waardeMutatie'] - $indexData['stortingen'] + $indexData['onttrekkingen'];
        $indexData['kosten'] = $dbData['Kosten'];
        $indexData['opbrengsten'] = $dbData['Opbrengsten'];
        $indexData['performance'] = $dbData['indexWaarde'];
        //$indexData['resultaatVerslagperiode'] = $dbData['Opbrengsten']-$dbData['Kosten'];
        $indexData['gerealiseerd'] = $dbData['gerealiseerd'];
        $indexData['ongerealiseerd'] = $dbData['ongerealiseerd'];
        $indexData['rente'] = $dbData['rente'];
        $indexData['extra'] = unserialize($dbData['extra']);
      }
      else
      {
        if(isset($portefeuilles) && ($valuta == 'EUR' || $valuta == ''  || $this->forceDbLoad==true ))
        {
          $query = "SELECT  Datum, sum(PortefeuilleWaarde) as PortefeuilleWaarde, sum(PortefeuilleBeginWaarde) as PortefeuilleBeginWaarde,
	  	    sum(Stortingen) as Stortingen, sum(Onttrekkingen) as Onttrekkingen, sum(Opbrengsten) as Opbrengsten, sum(Kosten) as Kosten ,Categorie, SUM(gerealiseerd) as gerealiseerd,
	  	    sum(ongerealiseerd) as ongerealiseerd, sum(rente) as rente, sum(gemiddelde) as gemiddelde,extra
		            FROM HistorischePortefeuilleIndex
		            WHERE
		            Categorie = 'Totaal' AND periode='$type' AND
		            portefeuille IN ('".implode("','",$portefeuilles)."') AND
		            Datum = '".substr($periode['stop'],0,10)."' GROUP BY Datum";
          
          if($db->QRecords($query) > 0)
          {
            $dbData = $db->nextRecord();
            $indexData['database']=1;
            $indexData['periodeForm'] = jul2form(db2jul($periode['start']))." - ".jul2form(db2jul($periode['stop']));
            $indexData['periode']= $periode['start']."->".$periode['stop'];
            $indexData['waardeMutatie'] = $dbData['PortefeuilleWaarde']-$dbData['PortefeuilleBeginWaarde'];
            $indexData['waardeBegin'] = $dbData['PortefeuilleWaarde']-$indexData['waardeMutatie'];
            $indexData['waardeHuidige'] = $dbData['PortefeuilleWaarde'];
            $indexData['stortingen'] = $dbData['Stortingen'];
            $indexData['onttrekkingen'] = $dbData['Onttrekkingen'];
            $indexData['resultaatVerslagperiode'] =  $indexData['waardeMutatie'] - $indexData['stortingen'] + $indexData['onttrekkingen'];
            $indexData['kosten'] = $dbData['Kosten'];
            $indexData['opbrengsten'] = $dbData['Opbrengsten'];
            $indexData['performance'] = $indexData['resultaatVerslagperiode']/$dbData['gemiddelde']*100;
            //$indexData['resultaatVerslagperiode'] = $dbData['Opbrengsten']-$dbData['Kosten'];
            $indexData['gerealiseerd'] = $dbData['gerealiseerd'];
            $indexData['ongerealiseerd'] = $dbData['ongerealiseerd'];
            $indexData['rente'] = $dbData['rente'];
            $indexData['extra'] = unserialize($dbData['extra']);
            //listarray($indexData);
          }
          else
            $indexData = array_merge($indexData,$this->BerekenMutaties($periode['start'],$periode['stop'],$portefeuille));
        }
        else
          $indexData = array_merge($indexData,$this->BerekenMutaties($periode['start'],$periode['stop'],$portefeuille,$valuta));
      }
      
      $indexData['datum'] = jul2sql(form2jul(substr($indexData['periodeForm'],-10,10)));
//          echo $indexData['periode']." ".$indexData['performance']."<br>\n";
      if($methode=='dagKwartaal')
      {
        if($periode['blok'] <> $lastBlok)
          $kwartaalBegin=$indexData['index'];
        $indexData['index'] = ($kwartaalBegin  * (100+$indexData['performance'])/100);
        $lastBlok=$periode['blok'];
        $data[$i] = array('index'=>$indexData['index'],'performance'=>$indexData['performance'],'datum'=>$indexData['datum'],'performance'=>$indexData['performance'],'periodeForm'=>$indexData['periodeForm']);
      }
      if($methode=='dagYTD')
      {
        $indexData['index']=$indexData['performance']+100;
        $data[$i] = array('index'=>$indexData['index'],'performance'=>$indexData['performance'],'datum'=>$indexData['datum'],'periodeForm'=>$indexData['periodeForm']);
      }
      else
      {
        
        if(empty($specifiekeIndexWaarden[$i-1]))
          $indexData['specifiekeIndexPerformance'] = $specifiekeIndexWaarden[$i]-100;
        else
          $indexData['specifiekeIndexPerformance'] =($specifiekeIndexWaarden[$i]/$specifiekeIndexWaarden[$i-1])*100 -100;
        $indexData['specifiekeIndex'] = ($indexData['specifiekeIndex']  * (100+$indexData['specifiekeIndexPerformance'])/100) ;
        if(empty($indexData['index']))
          $indexData['index']=100;
        $indexData['index'] = ($indexData['index']  * (100+$indexData['performance'])/100);
        $data[$i] = $indexData;
        
      }

      
      $i++;
    }
    
    return $data;
  }



	function BerekenMutaties($beginDatum,$eindDatum,$portefeuille)
	{
		$totaalWaarde =array();
		$koersQuery='';
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
        $categorieVerdeling['OBL'] += $regel['actuelePortefeuilleWaardeEuro'];
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
    $data['extra']=array('cat'=>$categorieVerdeling);
    return $data;

	}

function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;

    $legendDatum= $data['Datum'];
    $data1 = $data['benchmarkIndex'];
    $data = $data['Index'];
    if(count($data1)>0)
      $bereikdata = array_merge($data,$data1);
    else
      $bereikdata =   $data;

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $w/12 );

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(0,38,84);
    $this->pdf->SetLineWidth(0.2);

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

    if ($maxVal == 0)
    {
      $maxVal = ceil(max($bereikdata));
      if ($maxVal < 0)
        $maxVal = 1;
    }
    if ($minVal == 0)
    {
      $minVal = floor(min($bereikdata));
      if ($minVal > 0)
        $minVal =-1;
    }


    $minVal = floor(($minVal-1) * 1.1);
    $maxVal = ceil(($maxVal+1) * 1.1);
  
    if($minVal>=0 && $maxVal <100)
    {
      $minVal=0;
      $maxVal=100;
      $horDiv=5;
    }
    elseif($minVal>=-20 && $maxVal <80)
    {
      $minVal=-20;
      $maxVal=80;
      $horDiv=5;
    }
    elseif($minVal>=-40 && $maxVal <60)
    {
      $minVal=-40;
      $maxVal=60;
      $horDiv=5;
    }
    elseif($minVal>=-40 && $maxVal <60)
    {
      $minVal=-40;
      $maxVal=60;
      $horDiv=5;
    }
    elseif($minVal>=-60 && $maxVal <100)
    {
      $minVal=-60;
      $maxVal=100;
      $horDiv=8;
    }
    elseif($minVal>=-115 && $maxVal <100)
    {
      $minVal=-120;
      $maxVal=100;
      $horDiv=11;
    }
    //echo "$minVal $maxVal <br>\n";exit;
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $aantalPunten=count($data);
    $unit = $lDiag / $aantalPunten;

    if($jaar && $aantalPunten < 12)
      $unit = $lDiag / 12;

    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
    {
      $xpos = $XDiag + $verInterval * $i;
    }

    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);

    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);

    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);

    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $n=0;
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
       break;
    }

    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");

      $n++;
      if($n >20)
         break;
    }

    //datum onder grafiek
    /*
    $datumStart = db2jul($legendDatum[0]);
    $datumStart = vertaalTekst($__appvar["Maanden"][date("n",$datumStart)],$pdf->rapport_taal).' '.date("Y",$datumStart);
    $datumStop  =  db2jul($legendDatum[count($legendDatum)-1])+86400;
    $datumStop  = vertaalTekst($__appvar["Maanden"][date("n",$datumStop)],$pdf->rapport_taal).' '.date("Y",$datumStop);
    $ypos = $YDiag + $hDiag + $margin*2;
    $xpos = $XDiag;
    $this->pdf->Text($xpos, $ypos,$datumStart);
    $xpos = $XPage+$w - $this->pdf->GetStringWidth($datumStop);
    $this->pdf->Text($xpos, $ypos,$datumStop);
*/
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    //listarray($data);
   // $color=array(200,0,0);
   
    if($aantalPunten>36)
      $kwartalen=true;
    else
      $kwartalen=false;
    for ($i=0; $i<count($data); $i++)
    {
      $extrax=($unit*0.1*-1);
      if($i <> 0)
        $extrax1=($unit*0.1*-1);
        
      $julDatum=db2jul($legendDatum[$i]);
      
      if($kwartalen==false || in_array(date('m',$julDatum),array('03','06','09','12') ) || $i==$aantalPunten-1 )
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-10+$unit,$YDiag+$hDiag+8,jul2form($julDatum),25);

      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit+$extrax1, $yval, $XDiag+($i+1)*$unit+$extrax, $yval2,$lineStyle );
      $this->pdf->Rect($XDiag+($i+1)*$unit-0.5+$extrax, $yval2-0.5, 1, 1 ,'F','',$color);
      
      if($data[$i] <> 0)
        $this->pdf->Text($XDiag+($i+1)*$unit-1+$extrax,$yval2-2.5,$this->formatGetal($data[$i],1));
     
      
      $yval = $yval2;
    }

    if(is_array($data1))
    {
     // listarray($data1);
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color1);
      for ($i=0; $i<count($data1); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color1);
        
        $this->pdf->Text($XDiag+($i+1)*$unit-1,$yval2-2.5,$this->formatGetal($data1[$i],1));
         
        $yval = $yval2;
      }
    }
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }


  function VBarDiagram($w, $h, $data)
  {
      global $__appvar;
      $legendaWidth = 00;
      $grafiekPunt = array();
      $verwijder=array();

      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = jul2form(db2jul($datum));
        $n=0;
        $minVal=0;
        $maxVal=100;
        foreach (array_reverse($this->categorieVolgorde) as $categorie)
        {
        //foreach ($waarden as $categorie=>$waarde)
        //{
          if($categorie=='LIQ')
            $categorie='Liquiditeiten';
          $grafiek[$datum][$categorie]=$waarden[$categorie];
          $grafiekCategorie[$categorie][$datum]=$waarden[$categorie];
          $categorien[$categorie] = $n;
          $categorieId[$n]=$categorie ;

          $maxVal=max(array($maxVal,$waarden[$categorie]));
          $minVal=min(array($minVal,$waarden[$categorie]));

          if($waarden[$categorie] < 0)
          {
             unset($grafiek[$datum][$categorie]);
             $grafiekNegatief[$datum][$categorie]=$waarden[$categorie];
          }
          else
             $grafiekNegatief[$datum][$categorie]=0;


          if(!isset($colors[$categorie]))
            $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          $n++;
        }
      }



      $numBars = count($legenda);
      $numBars=10;

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
      $bGrafiek = ($w - $margin * 1) - ($w/12)*2; // - legenda

      $n=0;
      foreach (($this->categorieVolgorde) as $categorie)//array_reverse
      {
        if(is_array($grafiekCategorie[$categorie]))
        {
          $this->pdf->Rect($XstartGrafiek+$w+3 , $YstartGrafiek-$hGrafiek+$n*7+2, 2, 2, 'DF',null,$colors[$categorie]);
          $this->pdf->SetXY($XstartGrafiek+$w+6 ,$YstartGrafiek-$hGrafiek+$n*7+1.5 );
          $this->pdf->MultiCell(45, 4,$this->categorieOmschrijving[$categorie],0,'L');
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
           $this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+4,$legenda[$datum],0);

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
  }
}
?>
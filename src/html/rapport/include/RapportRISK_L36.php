<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:15 $
File Versie					: $Revision: 1.13 $

$Log: RapportRISK_L36.php,v $
Revision 1.13  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.12  2016/08/17 16:01:13  rvv
*** empty log message ***

Revision 1.11  2016/04/30 15:33:27  rvv
*** empty log message ***

Revision 1.10  2016/04/10 15:48:34  rvv
*** empty log message ***

Revision 1.9  2016/02/10 13:00:58  rvv
*** empty log message ***

Revision 1.8  2016/02/06 16:42:56  rvv
*** empty log message ***

Revision 1.7  2015/12/31 09:20:18  rvv
*** empty log message ***

Revision 1.6  2015/12/30 19:01:23  rvv
*** empty log message ***

Revision 1.5  2015/09/30 15:53:11  rvv
*** empty log message ***

Revision 1.4  2015/09/23 15:05:33  rvv
*** empty log message ***

Revision 1.3  2015/09/20 17:32:28  rvv
*** empty log message ***

Revision 1.2  2015/09/13 11:32:29  rvv
*** empty log message ***

Revision 1.1  2015/08/30 11:44:35  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L36.php");
include_once("rapport/include/RapportOIB_L36.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");

//include_once("rapport/ATTberekening2.php");

class RapportRISK_L36
{
	function RapportRISK_L36($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ATT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->excelData 	= array();

		$this->pdf->rapport_titel = "Rendement per beleggingscategorie afgezet tegen benchmark";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
		$this->oib = new RapportOIB_L36($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    
	}

  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		$DB = new DB();
		global $__appvar;
		//$this->pdf->AddPage();
    $query = "SELECT Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();

		$this->oib->getOIBdata($this->rapportageDatum);
		$this->oib->hoofdcategorien['geen-Hcat']='geen-Hcat';
		$oibData=$this->oib->hoofdCatogorieData;
		$oibData['totaal']['port']['procent']=1;

    if(db2jul($this->rapportageDatumVanaf) > db2jul($portefeuilledata['startDatum']))
	   	$rapportageStartJaar= date("Y-01-01",$this->pdf->rapport_datum);
	  else
	   	$rapportageStartJaar=substr($portefeuilledata['startDatum'],0,10);
	  $this->tweedePerformanceStart=$rapportageStartJaar;
	//  $att=new ATTberekening2($this);
  //  $waarden=$att->bereken($this->rapportageDatumVanaf,  $this->rapportageDatum,$this->pdf->rapportageValuta,'hoofdcategorie');
  //  listarray($waarden);

    $att=new ATTberekening_L36($this);
    $att->indexPerformance=true;
    $this->waarden['Historie']=$att->bereken($this->rapportageDatumVanaf,  $this->rapportageDatum,'EUR','hoofdcategorie',false);//substr($this->pdf->PortefeuilleStartdatum,0,10)
    //$this->waarden['Historie']=$att->bereken($rapportageStartJaar,  $this->rapportageDatum,'EUR','hoofdcategorie',false);
    if($this->pdf->debug==true)
    {
      //listarray($this->waarden['Historie']['totaal']);
       $this->pdf->excelData[]=array('Totaal categorie'); 
      $this->pdf->excelData[]=array('Datum','PortefeuillePerf','IndexPerf'); 
      foreach($this->waarden['Historie'] as $categorie=>$categorieData)
      {
        if($categorie <> 'totaal')
        {
          //$this->pdf->excelData[]=array($categorie);
          //foreach($categorieData['perfWaarden'] as $datum=>$perfData)
          //  $this->pdf->excelData[]=array($datum,$perfData['procent']+1,$perfData['indexPerf']); 
        
          //$this->pdf->excelData[]=array('');
         }
      } 
    } 
    $stapelTypen=array('procent'); //,'bijdrage'
    $somTypen=array('indexPerf');
    $gemiddeldeTypen=array('weging','eindWeging');

    foreach ($this->waarden['Historie'] as $categorie=>$categorieData)
     $this->jaarTotalen[$categorie]=array();


    foreach ($this->waarden['Historie'] as $categorie=>$categorieData)
    { 
      $laatste=array();
      
      if($lastCategorie <> '')
      {
        $this->pdf->excelData[]=array('Totaal',
           $this->jaarTotalen[$lastCategorie][$jaar]['procent'],'','',
           $this->jaarTotalen[$lastCategorie][$jaar]['indexPerf'],
           $this->jaarTotalen[$lastCategorie][$jaar]['procent']- $this->jaarTotalen[$lastCategorie][$jaar]['indexPerf'],
          $this->jaarTotalen[$lastCategorie][$jaar]['allocateEffect'],
          ( $this->jaarTotalen[$lastCategorie][$jaar]['procent']- $this->jaarTotalen[$lastCategorie][$jaar]['indexPerf'])-$this->jaarTotalen[$lastCategorie][$jaar]['allocateEffect']
          );
          
        
      }

       $this->pdf->excelData[]=array($categorie);
       $this->pdf->excelData[]=array('datum','Performance','weging','indexBijdrageWaarde','indexPerf','Attributie',
       'allocateEffect (weging-indexBijdrageWaarde)*indexPerf','SellectieEffect Totaal(Performance-indexPerf)-allocateEffect');
       foreach ($categorieData['perfWaarden'] as $datum=>$waarden)
      {
        $jaar=substr($datum,0,4);
        
        if(!isset($this->jaarTotalen[$categorie][$jaar]['aantal']))
          $this->jaarTotalen[$categorie][$jaar]['aantal']=1;
        else  
          $this->jaarTotalen[$categorie][$jaar]['aantal']++;
          
        $this->jaarTotalen[$categorie][$jaar]['resultaat']+=$waarden['resultaat'];
        foreach ($stapelTypen as $type)
        {
          $this->jaarTotalen[$categorie][$jaar][$type]=((1+$waarden[$type])*(1+$laatste[$jaar][$type])-1);
          $laatste[$jaar][$type]=$this->jaarTotalen[$categorie][$jaar][$type];
        }
        foreach ($somTypen as $type)
        {
          $this->jaarTotalen[$categorie][$jaar][$type]+=$waarden[$type];
        }
        foreach ($gemiddeldeTypen as $type)
          $this->jaarTotalen[$categorie][$jaar][$type]+=$waarden[$type];
        
        if($categorie!='totaal')
        {
        // listarray($waarden); 
          //$this->maandTotalen[$datum]['attributieEffect']+=(($waarden['weging']*$waarden['procent'])-($waarden['indexPerf']*$waarden['indexBijdrageWaarde']))*100;
          $this->maandTotalen[$datum]['allocateEffect']+=($waarden['eindWeging']-$waarden['indexBijdrageWaarde'])*$waarden['indexPerf']*100;
          //$this->maandTotalen[$datum]['allocateEffect']+=($waarden['eindWeging']-$waarden['indexBijdrageWaarde'])*$waarden['indexPerf'];//wordt gebruikt
          //$this->maandTotalen[$datum]['selectieEffect']+=($waarden['procent']-$waarden['indexPerf'])*$waarden['weging']*100;


       //   $this->maandTotalen[$datum]['selectieEffect']+=(($waarden['procent']-$waarden['indexPerf'])-($waarden['eindWeging']-$waarden['indexBijdrageWaarde'])*$waarden['indexPerf'])*100;



          $this->jaarTotalen[$categorie][$jaar]['allocateEffect']+=($waarden['eindWeging']-$waarden['indexBijdrageWaarde'])*$waarden['indexPerf'];
          
          $this->maandCumulatief[$datum]['allocateEffect']+=$this->jaarTotalen[$categorie][$jaar]['allocateEffect'];
          
         // echo "$datum $jaar $categorie ".$this->jaarTotalen[$categorie][$jaar]['allocateEffect']." <br>\n";
          $this->jaarTotalen['totaal'][$jaar]['allocateEffect']+=($waarden['eindWeging']-$waarden['indexBijdrageWaarde'])*$waarden['indexPerf'];//wordt gebruikt
     
          $this->pdf->excelData[]=array($datum,
            $waarden['procent'],
            $waarden['eindWeging'],
            $waarden['indexBijdrageWaarde'],
            $waarden['indexPerf'],
            $waarden['procent']-$waarden['indexPerf'],
            ($waarden['eindWeging']-$waarden['indexBijdrageWaarde'])*$waarden['indexPerf']); 
          
        }
        else
        {
           $this->maandTotalen[$datum]['attributieEffect']= ($this->jaarTotalen[$categorie][$jaar]['procent']-$this->jaarTotalen[$categorie][$jaar]['indexPerf'])*100;


          // echo "attributieEffect $datum ".$this->maandTotalen[$datum]['attributieEffect']."=(".$this->jaarTotalen[$categorie][$jaar]['procent']."-".$this->jaarTotalen[$categorie][$jaar]['indexPerf'].")*100<br>\n";
           $this->maandTotalen[$datum]['selectieEffect']=($waarden['procent']-$waarden['indexPerf'])*100-$this->maandTotalen[$datum]['allocateEffect'];

           $this->maandCumulatief[$datum]['selectieEffect']  =(($this->jaarTotalen[$categorie][$jaar]['procent']-$this->jaarTotalen[$categorie][$jaar]['indexPerf'])-($this->maandCumulatief[$datum]['allocateEffect']))*100;
          // echo  "selectieEffect $datum ".$this->maandCumulatief[$datum]['selectieEffect']." =((".($this->jaarTotalen[$categorie][$jaar]['procent']-$this->jaarTotalen[$categorie][$jaar]['indexPerf']).")-(".$this->maandCumulatief[$datum]['allocateEffect']."))*100<br>\n";  
  
  
        }
        $this->jaarTotalen[$categorie][$jaar]['portBijdrage']+=$waarden['bijdrage'];
         //$this->jaarTotalen[$categorie][$jaar]['indexBijdrageWaarde']+=$waarden['bijdrage'];    
         
        if($lastJaar <> '' && $jaar <> $lastJaar)
        {
             
          foreach ($gemiddeldeTypen as $type)
          {
            $this->jaarTotalen[$categorie][$lastJaar][$type]=$this->jaarTotalen[$categorie][$lastJaar][$type]/$this->jaarTotalen[$categorie][$lastJaar]['aantal'];
          }
        }
        $lastCategorie=$categorie;
        $lastJaar=$jaar;
      }
      
      $this->jaarTotalen[$categorie][$lastJaar][$type]=$this->jaarTotalen[$categorie][$lastJaar][$type]/$this->jaarTotalen[$categorie][$lastJaar]['aantal'];

//echo count($categorieData);

    }
//listarray( $this->maandTotalen);

    $startJaar=date("Y",$this->pdf->rapport_datum);
    $this->oib->hoofdcategorien['totaal']="Totaal";
    $this->pdf->rapport_titel = "Performance en attributie-overzicht per beleggingscategorie en totaal";
    $this->pdf->AddPage();
    $this->pdf->templateVars['ATTPaginas']=$this->pdf->page;
    $this->pdf->SetWidths(array(40,30,30,30,30,30,30,30));
   	$this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R'));
   	$this->pdf->ln(5);
   	$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->row(array("","Tactische\nWeging","Strategische\nWeging","Rendement\nPortefeuille","Ontwikkeling\nbenchmark",'Attributie',"Allocatie\neffect","Selectie\neffect"));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->ln();

   $categorieAandeel=0;
   foreach ($this->jaarTotalen as $categorie=>$jaarWaarden)
   {
     $waarden=$jaarWaarden[$startJaar];
     
     if($categorie=='totaal')
     {
       $query="SELECT
SUM((Rekeningmutaties.Credit-Rekeningmutaties.Debet)*Rekeningmutaties.Valutakoers) as kosten
FROM
Rekeningmutaties
INNER JOIN Grootboekrekeningen ON Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
JOIN Rekeningen ON Rekeningen.Rekening = Rekeningmutaties.Rekening
WHERE
Rekeningen.Portefeuille='".$this->portefeuille."' AND Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."'AND
 Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND
Grootboekrekeningen.Kosten=1";
       $DB=new DB();
       $DB->SQL($query);
       $kosten=$DB->lookupRecord();
       $totaleWaarde=0;
       foreach($this->oib->tabelDataHcat[$this->rapportageDatum] as $cat=>$waarde)
         $totaleWaarde+=$waarde['waarde'];
       $kostenAandeel=$kosten['kosten']/$totaleWaarde;
       //$kostenAandeel=($waarden['procent']*$waarden['weging'])-$categorieAandeel;
        //  echo "$kostenAandeel =".$waarden['procent']."- $categorieAandeel <br>\n";
      
        $this->pdf->row(array('Kosten','','',$this->formatGetal($kostenAandeel*100,2)));

        //$this->pdf->ln(5);
     }


      //listarray($waarden);      
      $this->pdf->row(array($this->oib->hoofdcategorien[$categorie],
      $this->formatGetal($waarden['eindWeging']*100,1), // . ' '.$this->formatGetal(($oibData[$categorie]['port']['procent'])*100,1)
      $this->formatGetal($att->normData[$categorie],1),
      $this->formatGetal($waarden['procent']*100,2),
      $this->formatGetal($waarden['indexPerf']*100,2),
      $this->formatGetal(($waarden['procent']-$waarden['indexPerf'])*100,2),//$this->formatGetal((($waarden['weging']*$waarden['procent'])-($waarden['indexPerf']*$waarden['indexBijdrageWaarde']))*100,2),
      $this->formatGetal($waarden['allocateEffect']*100,2),
      $this->formatGetal((($waarden['procent']-$waarden['indexPerf'])-$waarden['allocateEffect'])*100,2)));
     // $this->pdf->ln(5);

    }

      //$this->pdf->rapport_titel = "Maandelijkse attributie-effecten";
     // $this->pdf->AddPage();
      $this->pdf->setXY(15,182);
      $barData=array();
      foreach($this->maandTotalen as $maand=>$waarden)
      {
        unset($waarden['attributieEffect']);
        $barData[$maand]=$waarden;
      }
      $this->VBarDiagram2(270,137-50,$barData,'');
      $colors=array('allocate effect'=>array(0,52,121),'selectie effect'=>array(87,165,25)); //'attributie effect'=>,array(87,165,25)
      $xval=25;$yval=186;
      foreach($colors as $effect=>$color)
      {
         $this->pdf->Rect($xval, $yval, 3, 3, 'DF',null,$color);
         $this->pdf->SetTextColor(0);
         $this->pdf->SetXY($xval+5, $yval);
         $this->pdf->Cell(50, 3, $effect,0,0,'L');
         $xval+=40;
      }
  
      
      
  


    $this->pdf->SetFillColor(255,255,255);
    $this->pdf->rapport_titel = "Risicokenmerken";
    $this->pdf->AddPage();
    $this->pdf->templateVars['RISK2Paginas']=$this->pdf->page;
  	$this->pdf->SetWidths(array(10,297-$this->pdf->marge*2-20));
  	$this->pdf->SetAligns(array('L','L'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->ln();
  	$this->pdf->row(array('',""));
 
    $stdev=new rapportSDberekening($this->portefeuille,$this->rapportageDatum);
    $stdev->addReeks('afm');
    $stdev->addReeks('benchmark',$this->pdf->portefeuilledata['SpecifiekeIndex']);
    $stdev->berekenWaarden(true);
    $riskData=$stdev->riskAnalyze('totaal','benchmark',true);
    $stdevReeksen=array();
    
    $this->pdf->excelData[]=array();
    $this->pdf->excelData[]=array('risk data');
    $this->pdf->excelData[]=array('eersteMeting','laatsteMeting','stdev','stdevIndex','perf','perfIndex');
    foreach($riskData as $id=>$reeks)
    {
         $tmp=array('eersteMeting'=>$reeks['eersteMeting'],
                    'laatsteMeting'=>$reeks['laatsteMeting'],
                    'stdev'=>$reeks['standaarddeviatie'],
                    'stdevIndex'=>$reeks['standaarddeviatieBenchmark'],
                    'perf'=>$reeks['jaarPerf'],
                    'perfIndex'=>$reeks['jaarPerfBenchmark']);
         $this->pdf->excelData[]=array($reeks['eersteMeting'],$reeks['laatsteMeting'],$reeks['standaarddeviatie'],$reeks['standaarddeviatieBenchmark'],
         $reeks['jaarPerf'],$reeks['jaarPerfBenchmark']);
         $stdevReeksen['totaal'][]=$tmp;
    }
    $laatsteRisk=$riskData[count($riskData)-1];
    $this->pdf->excelData[]=array();
    $this->pdf->excelData[]=array('Performance');
    $kop=array('datum','totaal','benchmark');
    $stdevdata=array();
    foreach($stdev->reeksen['totaal'] as $datum=>$perf)
      $stdevdata[$datum]['totaal']=$perf['perf'];
    foreach($stdev->reeksen['benchmark'] as $datum=>$perf)
      $stdevdata[$datum]['benchmark']=$perf['perf'];      
    $this->pdf->excelData[]=$kop;
    foreach($stdevdata as $datum=>$categorien)
    {
      $tmp=array($datum,$categorien['totaal'],$categorien['benchmark']);
      $this->pdf->excelData[]=$tmp;
    }
      
    //listarray($stdev);
 /*
    $afm=AFMstd($this->portefeuille, $this->rapportageDatum);
   
    $query="SELECT verwachtRendement FROM Risicoklassen 
    WHERE Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND Risicoklasse='".$this->pdf->portefeuilledata['Risicoklasse']."'";
    $db=new DB();
    $db->SQL($query);
    $verwachtRendement=$db->lookupRecord();
    $index = new indexHerberekening();


    $perioden='maanden';
    $yearCount=12;
    if(db2jul($this->pdf->PortefeuilleStartdatum) < db2jul('2012-01-01'))
      $start='2012-01-01';
    else
      $start=$this->pdf->PortefeuilleStartdatum; 
    

    $indexWaarden = $index->getWaarden($start,$this->rapportageDatum,$this->portefeuille,$this->pdf->portefeuilledata['SpecifiekeIndex'],$perioden);
    $this->indexWaarden=$indexWaarden;
    $stdevReeksen=$this->createStdev();
    $this->pdf->excelData[]=array('Periode','performance','cumulatief','performanceIndex','overperfSquare');
    $perfCumArray=array();
    foreach ($indexWaarden as $id=>$waarden)
    {
      $overPerfSquareArray[$waarden['datum']]=pow(($waarden['performance']-$waarden['specifiekeIndexPerformance']),2);
      $indexModelArray[$waarden['datum']]=100+$waarden['specifiekeIndexPerformance'];
      
      $perfCumArray[]=$waarden['index'];
      $overPerfArray[$waarden['datum']]=$waarden['performance']-$waarden['specifiekeIndexPerformance'];
      $overPerfFixedArray[$waarden['datum']]=$waarden['performance']-(1/12);
      $indexArray[$waarden['datum']]=100+$waarden['performance'];
      $this->pdf->excelData[]=array($waarden['periode'],100+$waarden['performance'],$waarden['index'],$indexModelArray[$waarden['datum']],$overPerfSquareArray[$waarden['datum']]);
    }

    $maxDrawdownArray=array();

    //$perfCumArray=array(101,102.515,99.43955,100.4339455,101.639152846,103.0621009858,102.5467904809,103.5722583857,102.9508248354,102.1272182367,101.1059460544,99.5893568635,101.5811440008,101.8858874328,102.4972027574,103.522174785,105.7996626303,105.4822636424,104.8493700605,103.5911776198,104.0055423303,103.1734979916,100.0782930519,100.278449638,100.3787280876,102.3863026494,103.6149382812,104.2366279108,105.174757562,102.8609128957,103.2723565473,103.5821736169,103.6857557905,104.4115560811,104.8292023054,105.03886071);
    $aantal=count($perfCumArray)-1;
    foreach($perfCumArray as $index=>$waarde)
    {
      $min=1000;
      $max=0;
      for($i=0;$i<=$index;$i++)
      {
        if($perfCumArray[$i] > $max)
          $max=$perfCumArray[$i];
      }
      for($i=$index;$i<=$aantal;$i++)
      {
       
        if($perfCumArray[$i] < $min)
          $min=$perfCumArray[$i];
      }
      $maxDrawdownArray[$index]=($max-$min)/($max/100);
    }
    $this->pdf->excelData[]=array('maxDrawdownArray');
    foreach($maxDrawdownArray as $regel)
       $this->pdf->excelData[]=array($regel);
    $maxDrawdown=max($maxDrawdownArray);
    $this->pdf->excelData[]=array('maxDrawdown',$maxDrawdown);   
    
    $portPerfAvg=$portPerfAvg/count($indexWaarden);
    $modelPerfAvg=$modelPerfAvg/count($indexWaarden);
    $overPerfAvg=$overPerf/count($indexWaarden);
    $trackingError=pow(array_sum($overPerfSquareArray)/count($overPerfSquareArray),0.5);
    $this->pdf->excelData[]=array('trackingError','pow(array_sum($overPerfSquareArray)/count($overPerfSquareArray),0.5)','pow('.array_sum($overPerfSquareArray).'/'.count($overPerfSquareArray).',0.5)',pow(array_sum($overPerfSquareArray)/count($overPerfSquareArray),0.5));  

    $overPerfStd=standard_deviation($overPerfArray)*sqrt($yearCount);
    $overPerfFixedStd=standard_deviation($overPerfFixedArray)*sqrt($yearCount);
    $standaarddeviatiePeriode=standard_deviation($indexArray);
    $standaarddeviatie= $standaarddeviatiePeriode*sqrt($yearCount); 
    $standaarddeviatieBenchmarkPeriode=standard_deviation($indexModelArray);
    $standaarddeviatieBenchmark= $standaarddeviatieBenchmarkPeriode*sqrt($yearCount);     
    
    //listarray($stdevReeksen);
   

$sharpeRatio=array_sum($overPerfFixedArray)/$overPerfFixedStd;
$informatieratio=array_sum($overPerfArray)/$overPerfStd;
    $VaR=100+$verwachtRendement['verwachtRendement']-(2*$standaarddeviatie);
    $this->pdf->excelData[]=array('VaR','100+$verwachtRendement-(2*$standaarddeviatie)','100+'.$verwachtRendement['verwachtRendement'].'-(2*'.$standaarddeviatie.')',$VaR);


    $this->pdf->Ln(2);
 		$this->pdf->SetWidths(array(10,60,25,190));
  	$this->pdf->SetAligns(array('L','L','R','L'));
  	$this->pdf->row(array('','Standaarddeviatie',$this->formatGetal($standaarddeviatie,1).'%',$body));
    $this->pdf->ln(2);
    $this->pdf->row(array('','AFM-Standaarddeviatie',$this->formatGetal($afm['std'],1).'%',$body));
    $this->pdf->ln(2);
    $this->pdf->row(array('','Standaarddeviatie benchmark',$this->formatGetal($standaarddeviatieBenchmark,1).'%',$body));
    $this->pdf->ln(2);   
    
    
  	$this->pdf->row(array('','Value at Risk','€ '.$this->formatGetal($VaR*$this->waarden['Historie']['totaal']['eindwaarde']/100,0),''));//'Value at Risk geeft het verwachte maximale verlies aan met een waarschijnlijkheid van 95%. De historische VaR is bepaald aan de hand van de werkelijke jaarlijkse rendementsverdeling over de afgelopen tien jaar.'
    $this->pdf->ln(2);
  	$this->pdf->row(array('','Maximum Draw Down',$this->formatGetal($maxDrawdown,1).'%',''));//'Maximum Drawdown geeft de maximale daling weer vanaf de hoogste waarde in een specifieke periode. Deze periode betreft in de overzichten een periode van tien jaar.'
    $this->pdf->ln(2);
    $this->pdf->row(array('','Tracking Error',$this->formatGetal($trackingError,1).'%',''));//'De Tracking-error geeft een indicatie weer van de mate van afwijking van het rendement van de portefeuille ten opzichte van de benchmark.'
    $this->pdf->ln(2);
    $this->pdf->row(array('','Sharpe ratio',$this->formatGetal($sharpeRatio,1).'',''));
    $this->pdf->ln(2);
    $this->pdf->row(array('','Informatieratio',$this->formatGetal($informatieratio,1).'',''));
*/

    $this->pdf->Ln(2);
 		$this->pdf->SetWidths(array(10,60,25,190));
  	$this->pdf->SetAligns(array('L','L','R','L'));
  	$this->pdf->row(array('','Standaarddeviatie',$this->formatGetal($laatsteRisk['standaarddeviatie'],1).'%',$body));
    $this->pdf->ln(2);
    $this->pdf->row(array('','AFM-Standaarddeviatie',$this->formatGetal($laatsteRisk['standaarddeviatieAFM'],1).'%',$body));
    $this->pdf->ln(2);
    $this->pdf->row(array('','Standaarddeviatie benchmark',$this->formatGetal($laatsteRisk['standaarddeviatieBenchmark'],1).'%',$body));
    $this->pdf->ln(2);   
  	$this->pdf->row(array('','Value at Risk','€ '.$this->formatGetal($this->waarden['Historie']['totaal']['eindwaarde']-($laatsteRisk['valueAtRisk']*$this->waarden['Historie']['totaal']['eindwaarde']/100),0),''));//'Value at Risk geeft het verwachte maximale verlies aan met een waarschijnlijkheid van 95%. De historische VaR is bepaald aan de hand van de werkelijke jaarlijkse rendementsverdeling over de afgelopen tien jaar.'
    $this->pdf->ln(2);
  	$this->pdf->row(array('','Maximum Draw Down',$this->formatGetal($laatsteRisk['maxDrawdown'],1).'%',''));//'Maximum Drawdown geeft de maximale daling weer vanaf de hoogste waarde in een specifieke periode. Deze periode betreft in de overzichten een periode van tien jaar.'
    $this->pdf->ln(2);
    $this->pdf->row(array('','Tracking Error',$this->formatGetal($laatsteRisk['trackingError'],1).'%',''));//'De Tracking-error geeft een indicatie weer van de mate van afwijking van het rendement van de portefeuille ten opzichte van de benchmark.'
    $this->pdf->ln(2);
    $this->pdf->row(array('','Sharpe ratio',$this->formatGetal($laatsteRisk['sharpeRatio'],1).'',''));
    $this->pdf->ln(2);
    $this->pdf->row(array('','Informatieratio',$this->formatGetal($laatsteRisk['informatieratio'],1).'',''));
    
    
    $this->pdf->ln();
 		$this->pdf->SetWidths(array(10,120));
  	$this->pdf->SetAligns(array('L','L'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Toelichting'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','De "Standaarddeviatie" geeft de volatiliteit van de effectenportefeuille weer. Deze wordt gebruikt als een maatstaf voor de risicograad van de beleggingen.'));
    $this->pdf->ln(2);
    $this->pdf->row(array('','Bij de "AFM-standaarddeviatie" wordt geen berekening gemaakt van het werkelijke risico maar wordt gebruik gemaakt van lange termijn standaarden.'));
    $this->pdf->ln(2);
    $this->pdf->row(array('','De "Standaarddeviatie Benchmark" geeft de volatiliteit van de benchmark weer.'));
    $this->pdf->ln(2);
    $this->pdf->row(array('','De "Value at Risk" geeft het verwachte maximale verlies aan met een waarschijnlijkheid van 95% over een periode van 1 jaar.'));
    $this->pdf->ln(2);
    $this->pdf->row(array('','De "Maximum Drawdown" geeft de maximale daling weer vanaf de hoogste waarde van de effectenportefeuille vanaf de start.'));
    $this->pdf->ln(2);
    $this->pdf->row(array('','De "Tracking-error" geeft een indicatie weer van de mate van afwijking van het rendement van de portefeuille ten opzichte van de benchmark.'));
    $this->pdf->ln(2);
    $this->pdf->row(array('','De "Sharpe-ratio" is een meting van het voor risico gecorrigeerde rendement van de effectenportefeuille.'));
    $this->pdf->ln(2);
    $this->pdf->row(array('','De "Informatieratio" is een meting van het risico van de portefeuille gecorrigeerd voor het risico van de benchmark. Het deelt het actieve rendement door de tracking error.'));

//    $this->pdf->row(array('','Active Share',$this->formatGetal($activeShare,1).'%',''));//'De Active Share geeft een indicatie van de afwijking van de opbouw van de portefeuille ten opzichte van de opbouw van de benchmark.'
//echo $standaarddeviatie;
      $this->plotStdev($stdevReeksen);
	}
  
  function plotStdev($standaardDeviatieReeksen)
  {
    foreach($standaardDeviatieReeksen as $cat=>$reeksData)
    {
      array_push($xlsHeader,'perf '.$cat);
      array_push($xlsHeader,'index '.$cat);
      $catId=$catId+2;
      $counter=$counterBegin;
      foreach($reeksData as $n=>$waarden)
      {
    
        $grafiekData[$cat]['portefeuille'][]=$waarden['stdev'];
        if($cat<>'afm')
          $grafiekData[$cat]['specifiekeIndex'][]=$waarden['stdevIndex'];
        $grafiekData[$cat]['datum'][]=date('Y-M',db2jul($waarden['laatsteMeting']));
        
        $grafiekData2[$cat]['portefeuille']['x']=$waarden['stdev'];
        $grafiekData2[$cat]['portefeuille']['y']=$waarden['perf'];
        $grafiekData2[$cat]['portefeuille']['kleur']=array(87,165,25);
        $grafiekData2[$cat]['index']['x']=$waarden['stdevIndex'];
        $grafiekData2[$cat]['index']['y']=$waarden['perfIndex'];
        $grafiekData2[$cat]['index']['kleur']=array(0,52,121);

/*        
        if($cat=='totaal')
        {
          $grafiekData2[$cat]['portefeuilleAvg']['x']+=$waarden['stdev'];
          $grafiekData2[$cat]['portefeuilleAvg']['y']+=$waarden['perf'];
          
          $grafiekData2[$cat]['indexAvg']['x']+=$waarden['stdevIndex'];
          $grafiekData2[$cat]['indexAvg']['y']+=$waarden['perfIndex'];
        }
        $grafiekData2[$cat]['portefeuille'.$n]['x']=$waarden['stdev'];
        $grafiekData2[$cat]['portefeuille'.$n]['y']=$waarden['perf'];
        $grafiekData2[$cat]['portefeuille'.$n]['kleur']=array(87,165,25);
        $grafiekData2[$cat]['index'.$n]['x']=$waarden['stdevIndex'];
        $grafiekData2[$cat]['index'.$n]['y']=$waarden['perfIndex'];
        $grafiekData2[$cat]['index'.$n]['kleur']=array(0,52,121);
*/
        $this->pdf->excelData[$counter+1][0] = $waarden['laatsteMeting'];  
        $this->pdf->excelData[$counter+1][$catId] = $waarden['stdev'];     
        $this->pdf->excelData[$counter+1][$catId+1] = $waarden['stdevIndex'];  
        $counter++;
      }
    }

/*    
    $grafiekData2['totaal']['portefeuilleAvg']['x']=$grafiekData2['totaal']['portefeuilleAvg']['x']/count($reeksData);
    $grafiekData2['totaal']['portefeuilleAvg']['y']=$grafiekData2['totaal']['portefeuilleAvg']['y']/count($reeksData);
    $grafiekData2['totaal']['portefeuilleAvg']['kleur']=array(87,165,25);
    $grafiekData2['totaal']['indexAvg']['x']=$grafiekData2['totaal']['indexAvg']['x']/count($reeksData);
    $grafiekData2['totaal']['indexAvg']['y']=$grafiekData2['totaal']['indexAvg']['y']/count($reeksData);
    $grafiekData2['totaal']['indexAvg']['kleur']=array(0,52,121);
*/    
    $this->pdf->excelData[$counterBegin]=$xlsHeader;
    $grafiekData['totaal']['titel']='Verloop standaarddeviatie portefeuille';
    //$grafiekData['afm']['titel']='Verloop AFM-standaarddeviatie maandelijks gemeten';
    $grafiekData['totaal']['legenda']=array('portefeuille','benchmark');
    //$grafiekData['afm']['legenda']=array('portefeuille');
   // $this->pdf->setXY(20,110);
   //listarray($grafiekData2['totaal']);
    $this->pdf->setXY(160,40);
    $this->LineDiagram_L35(120, 55, $grafiekData['totaal'],array(array(87,165,25),array(0,52,121)),0,0,6,5,1);//50
    //$this->pdf->setXY(160,110);
    //$this->LineDiagram_L35(120, 55, $grafiekData['afm'],array(array(87,165,25),array(0,52,121)),0,0,6,5,1);//50    
  
    $this->pdf->setXY(160,120);
    $this->scatterplot(120,50,$grafiekData2['totaal']);
  }
  
  function createStdev()
  {
 
    foreach($this->indexWaarden as $index=>$perfData)
    { 
      $beginJaar=substr($perfData['periode'],0,4);
      $eindJaar= substr($perfData['periode'],12,4);
      if($beginJaar==$eindJaar)
      {
        $perf=($perfData['performance']);
        $indexPerf=($perfData['specifiekeIndexPerformance']);
        $perfReeks['totaal'][]=array('perf'=>$perf,'index'=>$indexPerf,'datum'=>$perfData['datum']);
      }
    }
    $standaardDeviatieReeksen=array();
    $correctie=sqrt(12);
    $catId=-1;
    $xlsHeader=array('datum');
    foreach($perfReeks as $cat=>$catData)
    {
      $buffer=array();
      $bufferIndex=array();
      $bufferStartIndex=0;
      $indexCounter=0;
      $catId=$catId+2;
      array_push($xlsHeader,'perf '.$cat);
      array_push($xlsHeader,'index '.$cat);
      foreach($catData as $index=>$waarden)
      { 
        $buffer[$index]=$waarden['perf'];
        $bufferIndex[$index]=$waarden['index'];
   
        $this->pdf->excelData[$indexCounter+1][0] = $waarden['datum'];  
        $this->pdf->excelData[$indexCounter+1][$catId] = $waarden['perf'];     
        $this->pdf->excelData[$indexCounter+1][$catId+1] = $waarden['index'];    
        $indexCounter++;
        if(count($buffer)==36)
        {
         //listarray($buffer);
         $tmp=array('eersteMeting'=>$catData[$bufferStartIndex]['datum'],
                    'laatsteMeting'=>$catData[$bufferStartIndex+35]['datum'],
                    'stdev'=>standard_deviation($buffer)*$correctie,
                    'stdevIndex'=>standard_deviation($bufferIndex)*$correctie,
                    'perf'=>$this->perfPeriode($buffer)/3,//$waarden['perf'],
                    'perfIndex'=>$this->perfPeriode($bufferIndex)/3);// ,$waarden['index']);
         $standaardDeviatieReeksen[$cat][]=$tmp;
         unset($buffer[$bufferStartIndex]);
         unset($bufferIndex[$bufferStartIndex]);
         $bufferStartIndex++;
        }
      } 
    }
    $this->pdf->excelData[0]=$xlsHeader;
    //listarray($standaardDeviatieReeksen);
   /* 
    foreach($standaardDeviatieReeksen['totaal'] as $waarden)
    {
      $datum=$waarden['laatsteMeting'];
      if(substr($datum,5,5)=='01-01')
        $beginJaar=true;
      else
        $beginJaar=false;
      vulTijdelijkeTabel(berekenPortefeuilleWaarde($this->portefeuille,$datum,$beginJaar,'EUR',$datum));
      $afmStev=AFMstd($this->portefeuille,$datum);
      $standaardDeviatieReeksen['afm'][]=array('laatsteMeting'=>$datum,'stdev'=>$afmStev['std']);//*100
    }
    */
    return $standaardDeviatieReeksen;
  }
  
  function perfPeriode($reeks)
  {
    $perfCumu=1;
    foreach($reeks as $perfWaarde)
    {
      $perfCumu=$perfCumu*(1+($perfWaarde/100));
    }
    $perfCumu=($perfCumu-1)*100;
    //echo "<br>\nperf: $perfCumu <br><br>\n";
    return $perfCumu;
  }
  
function LineDiagram($x,$y,$w, $h, $data, $title,$color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;
    
    $this->pdf->Rect($x-10,$y-5,$w+15,$h+15);
    $this->pdf->setXY($x,$y);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Multicell($w,4,$title,'','C');
    $this->pdf->setXY($x,$y+8);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    


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
      $color=array(0,0,0);
    $this->pdf->SetLineWidth(0.2);

    $this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

     $maanden=array();
      $maxVal=0;
      $minVal=0;
      foreach($data as $type=>$maandData)
      {
        
        $tmp=count($maandData);
        if($tmp > $aantalMaanden)
          $aantalMaanden=$tmp;
        foreach($maandData as $maand=>$waarde)
        {
          $maanden[$maand]=$maand;
          if($waarde > $maxVal)
            $maxVal = $waarde;
          if($waarde < $minVal)  
            $minVal = $waarde;
        }
      }

    $minVal = floor(($minVal-1) * 1.1);
    $maxVal = ceil(($maxVal+1) * 1.1);
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / $aantalMaanden;

    if($jaar)
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
    for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
       break;
    }

    $n=0;
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
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

    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
  
   // $color=array(200,0,0);

       $colors=array('allocateEffect'=>array(0,52,121),'selectieEffect'=>array(87,165,25),'attributieEffect'=>array(108,31,128)); //

    //for ($i=0; $i<count($data); $i++)
    $maandPrinted=array();
    foreach($data as $type=>$maandData)
    {
      $i=0;
      $color=$colors[$type];
      $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
      foreach($maandData as $maand=>$waarde)
      {
        //foreach($maandData as $line)
       // $extrax=($unit*0.1*-1);
        
       //   $extrax1=($unit*0.1*-1);
        

        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$i],0);

        $yval2 = $YDiag + (($maxVal-$waarde) * $waardeCorrectie) ;
        
        if($i <> -1)
        {
          $this->pdf->line($XDiag+$i*$unit+$extrax1, $yval, $XDiag+($i+1)*$unit+$extrax, $yval2,$lineStyle );
        }
        $this->pdf->Rect($XDiag+($i+1)*$unit-0.5+$extrax, $yval2-0.5, 1, 1 ,'F','',$color);
        
        if($waarde <> 0)
          $this->pdf->Text($XDiag+($i+1)*$unit+$extrax,$yval2-2.5,$this->formatGetal($waarde,1));
          $yval = $yval2;
        
      
        if(!isset($maandPrinted[$maand]))
        {
          $maandPrinted[$maand]=1;
          $this->pdf->Text($XDiag+($i+1)*$unit+$extrax,$bodem+5,date('M',db2jul($maand)));
          
        }
        
        $i++;
        
        
      }
    }
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }


function LineDiagram_L35($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4)
  {
    global $__appvar;

    $legendDatum= $data['datum'];
    $legendaItems= $data['legenda'];
    $titel=$data['titel'];
    $data1 = $data['specifiekeIndex'];
    $data = $data['portefeuille'];
    

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
    $lDiag = floor($w - $margin * 1 );
    
    $this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->Cell($w,0,$titel,0,0,'C');

    $this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.2);

    
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

    if ($maxVal == 0)
    {
      $maxVal = ceil(max($bereikdata));
    }
    if ($minVal == 0)
    {
      $minVal = floor(min($bereikdata));
    }

    $minVal = floor(($minVal-1) * 1.1);
    if($minVal > 0)
      $minVal=0;
    $maxVal = ceil(($maxVal+1) * 1.1);
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / count($data);

     

    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
      $xpos = $XDiag + $verInterval * $i;

    $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
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
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    $jaren=ceil(count($data)/12);
    for ($i=0; $i<count($data); $i++)
    {
      if($i%$jaren==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+9,$legendDatum[$i],25);
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      
      if ($i>0)
      {
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
//        $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color);
      }
//      if ($i==count($data)-1)
//          $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color);
        

      $yval = $yval2;
    }
    
    if(is_array($data1))
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color1);

      for ($i=0; $i<count($data1); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
        
        if ($i>0)
        {
          $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
//          $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color1);
        }
//        if ($i==count($data1)-1)
//          $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color1);

         $yval = $yval2;
      }
    }


    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.2,'cap' => 'butt'));


  //   $XPage
   // $YPage


    $step=5;
    foreach ($legendaItems as $index=>$item)
    {
      if($index==0)
        $kleur=$color;
      else
        $kleur=$color1;
    $this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
    $this->pdf->Rect($XPage+$step, $YPage+$h+10, 3, 3, 'DF','',$kleur);
    $this->pdf->SetXY($XPage+3+$step,$YPage+$h+10);
    $this->pdf->Cell(0,3,$item);
    $step+=($w/2);
    }
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
  }
  function VBarDiagram2($w, $h, $data, $format, $color=null,$nbDiv=4,$numBars=0)
  {
      global $__appvar;
      $legendDatum = $data['datum'];
      //$data = $data['portefeuille'];
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      //$this->pdf->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1);

      $this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $w- $margin, $hGrafiek,'D',''); //,array(245,245,245)
      if($color == null)
          $color=array(155,155,155);
      
      $maxVal=0;
      $minVal=0;
      $maanden=array();
      foreach($data as $maand=>$maandData)
      {
        $maanden[$maand]=$maand;
        foreach($maandData as $type=>$waarde)
        {
          if($waarde > $maxVal)
            $maxVal = $waarde;
          if($waarde < $minVal)  
            $minVal = $waarde;
        }
      }
      if($maxVal > 1)
        $maxVal=ceil($maxVal);
      if($minVal < -1)  
        $minVal=floor($minVal);
      $minVal = $minVal * 1.1;
      $maxVal = $maxVal * 1.1;      
      if ($maxVal <0)
       $maxVal=0;

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

      $horDiv = 10;
      $horInterval = $hGrafiek / $horDiv;
      $bereik = $hGrafiek/$unit;

      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      $this->pdf->SetTextColor(0,0,0);

      $stapgrootte = ceil(abs($bereik)/$horDiv*10)/10;
      $top = $YstartGrafiek-$h;
      $bodem = $YstartGrafiek;
      $absUnit =abs($unit);

      $nulpunt = $YstartGrafiek + $nulYpos;
      $n=0;

      for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
      {
        $skipNull = true;
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");
        $n++;
        if($n >20)
         break;
      }

      $n=0;
      for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        if($skipNull == true)
          $skipNull = false;
        else
        $this->pdf->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");
        $n++;
        if($n >20)
          break;
      }
      
      $numBars=count($data);
      if($numBars > 0)
        $this->pdf->NbVal=$numBars;

         $colors=array('allocateEffect'=>array(0,52,121),'selectieEffect'=>array(87,165,25),'attributieEffect'=>array(108,31,128)); //


      $vBar = ($bGrafiek / ($this->pdf->NbVal ))/3; //4
      $bGrafiek = $vBar * ($this->pdf->NbVal );
      $eBaton = ($vBar * 80 / 100);
      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;
      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      foreach($data as $maand=>$maandData)
      {
        
        foreach($maandData as $type=>$val)
        {
          $color=$colors[$type];
          //Bar
          $xval = $XstartGrafiek + ($i + 1) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiek + $nulYpos;
          $hval = ($val * $unit);
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$color);
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3 && $eBaton > 4)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);
          $i++;
          }
          $i++;
          

         // $this->pdf->Text($XstartGrafiek + ($i -2) * $vBar - $eBaton / 2,$YstartGrafiek +3 ,date('M',db2jul($maand)));
         $this->pdf->TextWithRotation($XstartGrafiek + ($i -2) * $vBar - $eBaton / 2,$YstartGrafiek +5 ,date('M-Y',db2jul($maand)),20);
          
          
      }



     // $color=array(155,155,155);
     // $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
  }


  function VBarDiagram($w, $h, $data, $format, $color=null, $maxVal=0, $nbDiv=4,$numBars=0)
  {
      global $__appvar;
      $legendDatum = $data['datum'];
      //$data = $data['portefeuille'];
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1);

$this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $w- $margin, $hGrafiek,'FD','',array(245,245,245));

      if($color == null)
          $color=array(155,155,155);
      if ($maxVal == 0)
        $maxVal = ceil(max($data));
      $minVal = floor(min($data));

      $minVal = $minVal * 1.1;
      $maxVal = $maxVal * 1.2;

      if ($maxVal <0)
       $maxVal=0;

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

      $horDiv = 10;
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

      for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
      {
        $skipNull = true;
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");
        $n++;
        if($n >20)
         break;
      }

      $n=0;
      for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        if($skipNull == true)
          $skipNull = false;
        else
        $this->pdf->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");
        $n++;
        if($n >20)
          break;
      }

      if($numBars > 0)
        $this->pdf->NbVal=$numBars;

        $colors=array(array(87,165,25),array(255,0,59),array(0,52,121));

      $vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
      $bGrafiek = $vBar * ($this->pdf->NbVal + 1);
      $eBaton = ($vBar * 80 / 100);
      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;
      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      foreach($data as $index=>$val)
      {

        $color=$colors[$index];
          //Bar
          $xval = $XstartGrafiek + ($i + 1) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiek + $nulYpos;
          $hval = ($val * $unit);
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$color);
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);
          $i++;
      }



     // $color=array(155,155,155);
     // $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
  }
  


function scatterplot($w, $h, $data, $xTitel,$yTitel, $xEenheid,$yEenheid,$maxXVal=20,$maxYVal=10)
  {
    global $__appvar;
    $color=null;$horDiv=8; $verDiv=8;$jaar=0;
    $xCorLegendaY=-7;

    $minXVal=-1; $maxXVal=1; 
    $minYVal=-1; $maxYVal=1; 
    
    foreach($data as $reeks=>$waarden)
    {
      if($waarden['x'] > $maxXVal)
        $maxXVal=$waarden['x'];

      if($waarden['x'] < $minXVal)
        $minXVal=$waarden['x'];
              
      if($waarden['y'] > $maxYVal)
        $maxYVal=$waarden['y'];  
        
      if($waarden['y'] < $minYVal)
        $minYVal=$waarden['y'];
    }
    
    $minXVal=floor($minXVal);
    $minYVal=floor($minYVal);
    $maxXVal=ceil($maxXVal);
    $maxYVal=ceil($maxYVal);
  
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = $h;//floor($h - $margin * 1);
    $XDiag = $XPage;// + $margin * 1 ;
    $lDiag = $w;//floor($w);

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(0,0,0);
    $this->pdf->SetLineWidth(0.2);

    $this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $this->pdf->Rect($XPage+$xCorLegendaY-0.5,$YDiag-5,$w-$xCorLegendaY*2,$h+11,'F','',array(240,240,240));
    $this->pdf->Rect($XPage,$YDiag,$w,$h,'F','',array(220,220,220));
    $this->pdf->setXY($XPage, $YDiag-5);
    $this->pdf->Cell($w,4, $i."Risico/Rendementsverhouding", 0,0, "C");
    $this->pdf->setXY($XPage, $YDiag+$h+1);
    $this->pdf->Cell($w,4, $i."Standaarddeviatie", 0,0, "C");
    $this->pdf->TextWithRotation($XPage+$xCorLegendaY+5,$YDiag+$h-20,"Rendement",90);
    $this->pdf->setXY($XPage, $YDiag+$h+6);
    $this->pdf->MultiCell($w,4, $i."Het rendement is het gemiddelde jaarrendement over de afgelopen drie jaar.
De standaarddeviatie wordt berekend over een periode van drie jaar", 0,"L");

    
    $procentWhiteSpace = 0.10;
    $xband=($maxXVal - $minXVal);
    $yband=($maxYVal - $minYVal);
    $stepSize=round($band / $horDiv);
    $stepSize=ceil($stepSize/(pow(10,strlen($stepSize))))*pow(10,strlen($stepSize));
    $maxVal = ceil($maxVal * (1 + ($procentWhiteSpace))/$stepSize)*$stepSize;
    $minVal = floor($minVal * (1 - ($procentWhiteSpace))/$stepSize)*$stepSize;
    
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / $yband;
    $waardeCorrectieX = $lDiag / $xband;
    $nulpuntY = $YDiag + ($maxYVal * $waardeCorrectie);
    $nulpuntX = $XDiag - ($minXVal * $waardeCorrectieX);
    //echo "X: $minXVal $maxXVal \n Y: $minYVal  $maxYVal <br>\n";
    //echo "$yband=($maxYVal - $minYVal); <br>\n";
   // echo "$waardeCorrectie = $hDiag / $yband;  $nulpuntY <br>\n";exit;
   //echo "$nulpuntY = $YDiag + ($maxYVal * $waardeCorrectie) ";exit;
    $Xunit = $lDiag / $xband;
    $Yunit = $hDiag / $yband *-1;

    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);

    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);

    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);

    $lineStyle=array('dash' => 1,'color'=>array(100,100,100));
    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $n=0;
    $step=1;
    if($maxYVal > 10)
      $step=2;
      
    for($i=0; $i<= $maxYVal; $i+= $step)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $nulpuntY+$i*$Yunit, $XPage+$w ,$nulpuntY+$i*$Yunit,$lineStyle);

      $this->pdf->setXY($nulpuntX-10, $nulpuntY+$i*$Yunit);
      $this->pdf->Cell(10,0, $i."".$xEenheid, 0,0, "R");
      //$this->pdf->Text($XDiag-7, $bodem+$i*$Yunit, $i." %");
      $n++;
      if($n >20)
       break;
    }
   
    for($i=$minYVal; $i<= 0; $i+= $step)
    {
      if($i==0 && $skipNull==true)
        continue;
      $skipNull = true;
      
      $this->pdf->Line($XDiag, $nulpuntY+$i*$Yunit, $XPage+$w ,$nulpuntY+$i*$Yunit,$lineStyle);

      $this->pdf->setXY($nulpuntX-10, $nulpuntY+$i*$Yunit);
      $this->pdf->Cell(10,0, $i."".$xEenheid, 0,0, "R");
      //$this->pdf->Text($XDiag-7, $bodem+$i*$Yunit, $i." %");
      $n++;
      if($n >20)
       break;
    }
    
    if($maxYVal%$step)
      $this->pdf->Line($XDiag, $bodem+$maxYVal*$Yunit, $XPage+$w ,$bodem+$maxYVal*$Yunit,$lineStyle);
    
    $this->pdf->Text($XDiag+$xCorLegendaY, $bodem+$maxYVal*$Yunit-3, $yTitel); 
    $n=0;
    $step=1;
    if($maxXVal > 10)
      $step=2;
      
    //X-as
    $skipNull=false;
    for($i=0; $i<= $maxXVal; $i+= $step)
    {
      $xplot=$nulpuntX+$i*$Xunit;
       
      $skipNull = true;
      $this->pdf->Line($xplot, $YDiag, $xplot,$bodem,$lineStyle);
      //$this->pdf->Text($xplot-2, $bodem+3, $i."".$xEenheid);
      $this->pdf->setXY($xplot-2, $nulpuntY+2);
      $this->pdf->Cell(4,0, $i."".$xEenheid, 0,0, "C");
      $n++;
      if($n >20)
       break;
    }
     for($i=$minXVal; $i<= 0; $i+= $step)
    {
      if($i==0 && $skipNull==true)
        continue;
      $xplot=$nulpuntX+$i*$Xunit;
      $this->pdf->Line($xplot, $YDiag, $xplot,$bodem,$lineStyle);
      //$this->pdf->Text($xplot-2, $bodem+3, $i."".$xEenheid);
      $this->pdf->setXY($xplot-2, $nulpuntY+2);
      $this->pdf->Cell(4,0, $i."".$xEenheid, 0,0, "C");
      $n++;
      if($n >20)
       break;
    }
    if($maxXVal%$step)
    {
      $xplot=$XDiag+$maxXVal*$Xunit;
      $this->pdf->Line($xplot, $YDiag, $xplot,$bodem,$lineStyle);
    }
    //$this->pdf->Text($XDiag+$maxXVal/2*$Xunit-8, $bodem+6, $xTitel);
    
    $this->pdf->setXY($XDiag+$maxXVal/2*$Xunit-2, $bodem+4.5);
    $this->pdf->Cell(4,0,$xTitel, 0,0, "C");
    
   $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
   foreach($data as $reeks=>$waarden)
   {
     $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
     //$this->pdf->Rect($XDiag+$waarden['x']*$Xunit-0.5,$bodem+$waarden['y']*$Yunit-0.5, 1, 1 ,'F','',$waarden['kleur']);

     if($reeks=='index'||$reeks=='portefeuille')
     {
       $r=1;
       $this->pdf->Circle($nulpuntX+$waarden['x']*$Xunit,$nulpuntY+$waarden['y']*$Yunit, $r,0,360,'F','',$waarden['kleur']);
       $this->pdf->setXY($nulpuntX+$waarden['x']*$Xunit-5.5,$nulpuntY+$waarden['y']*$Yunit-2.5);
       //$this->pdf->Cell(10,0,substr($reeks,0,-3), 0,0, "C");
       $this->pdf->Cell(10,0,$reeks, 0,0, "C");
     }
     else
     {
      $r=0.75;
      $this->pdf->Circle($nulpuntX+$waarden['x']*$Xunit,$nulpuntY+$waarden['y']*$Yunit, $r,0,360,'F','',$waarden['kleur']);
     }
   }


    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }

  
  
}
?>
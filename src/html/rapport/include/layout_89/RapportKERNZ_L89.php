<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/05/13 15:37:13 $
 		File Versie					: $Revision: 1.2 $

 		$Log: RapportKERNZ_L89.php,v $
 		Revision 1.2  2020/05/13 15:37:13  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2020/04/08 15:45:20  rvv
 		*** empty log message ***
 		

 

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");
if(file_exists($__appvar["basedir"]."/html/rapport/RapportMODEL.php"))
  include_once($__appvar["basedir"]."/html/rapport/RapportMODEL.php");
else
  include_once($__appvar["basedir"]."/html/rapport/RapportModel.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
include_once($__appvar["basedir"]."/html/rapport/PDFOverzicht.php");

//ini_set('max_execution_time',60);
class RapportKERNZ_L89
{
	function RapportKERNZ_L89($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "KERNZ";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->pdf->rapport_titel = "\n \n \nRendement & Risicokenmerken 3";
		$this->pdf->rapport_titel2='';

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->excelData=array();


	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		global $__appvar;
    //$this->pdf->rapport_titel = "\n \nRendement & Risicokenmerken 2\nKenmerken van het vastrentende deel van uw portefeuille";
    
    $this->pdf->addPage();
			$this->pdf->templateVars[$this->pdf->rapport_type .'Paginas']=$this->pdf->page;
        $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=trim($this->pdf->rapport_titel);
    $this->printRisco($this->portefeuille,$this->rapportageDatum);


	}

  function printRisco($portefeuille, $rapportageDatum)
  {
 		$this->pdf->SetWidths(array(10,297-$this->pdf->marge*2-20));
  	$this->pdf->SetAligns(array('L','L'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->ln();
  	$this->pdf->row(array('',"Castanje Vermogensbeheer streeft er in haar beleggingsbeleid om per profiel de benchmark van het profiel met 2% te verslaan. Maar zeker zo belangrijk is de gedachte om hierbij niet een te groot risico te lopen. \"Risico\" is een begrip met vaak voor iedereen een andere inhoud.
Hieronder geven we een aantal risicomaatstaven weer voor uw portefeuille met een korte uitleg."));
 
    $afm=AFMstd($portefeuille, $rapportageDatum);
    //listarray($afm);
    
    
    $query="SELECT verwachtRendement FROM Risicoklassen 
    WHERE Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND Risicoklasse='".$this->pdf->portefeuilledata['Risicoklasse']."'";
    $db=new DB();
    $db->SQL($query);
    $verwachtRendement=$db->lookupRecord();
    $index = new indexHerberekening();

/*
    $dagen=(db2jul($rapportageDatum)-db2jul($this->pdf->PortefeuilleStartdatum))/86400;
    if($dagen < 365)
    {
      $perioden='geenData';
      $yearCount=0;
    }
    elseif($dagen > (3*365))
    {
      $perioden='maanden';
      $yearCount=12;
    }
    else
    {
      $perioden='halveMaanden';
      $yearCount=24;
    }
*/   
    $perioden='maanden';
    $yearCount=12;
//echo $this->pdf->PortefeuilleStartdatum;exit;
    $indexWaarden = $index->getWaarden($this->pdf->PortefeuilleStartdatum,$this->rapportageDatum,$portefeuille,$this->pdf->portefeuilledata['SpecifiekeIndex'],$perioden);
    $this->pdf->excelData[]=array('Periode','performance','cumulatief','performanceIndex','overperfSquare');
    $perfCumArray=array();
    foreach ($indexWaarden as $id=>$waarden)
    {
      //listarray($waarden);
      //$portPerfAvg+=$waarden['performance'];
      //$modelPerfAvg+=$waarden['specifiekeIndexPerformance'];
      //$overPerf+=($waarden['performance']-$waarden['specifiekeIndexPerformance']);

      //$overPerfArray[$waarden['datum']]=($waarden['performance']-$waarden['specifiekeIndexPerformance'])/100;
      if(db2jul($waarden['datum']) >= db2jul('2012-01-01'))
      {
        $overPerfSquareArray[$waarden['datum']]=pow(($waarden['performance']-$waarden['specifiekeIndexPerformance']),2);
        $indexModelArray[$waarden['datum']]=100+$waarden['specifiekeIndexPerformance'];
      }
      $perfCumArray[]=$waarden['index'];
      //echo "".$overPerfArray[$waarden['datum']]."=".$waarden['performance']-$waarden['specifiekeIndexPerformance'];
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
    $standaarddeviatiePeriode=standard_deviation($indexArray);
    $standaarddeviatie= $standaarddeviatiePeriode*sqrt($yearCount); 
    
    $pdf=new PDFRapport();
    $pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
	  $pdf->rapport_datum = db2jul($rapportageDatum);
    loadLayoutSettings($pdf, $portefeuille);
    $mod=new RapportMODEL($pdf,$portefeuille, $this->rapportageDatumVanaf,$rapportageDatum);
    $mod->writeRapport();

    $aantalRegels=count($pdf->excelData);
    $activeShare=0;
    $this->pdf->excelData[]=array('activeShare','','','ABS($pdf->excelData[$i][2]-$pdf->excelData[$i][1])');
    for($i=1;$i<$aantalRegels;$i++)
    {
      $activeShare+=ABS($pdf->excelData[$i][2]-$pdf->excelData[$i][1]);
      $this->pdf->excelData[]=array($activeShare,$pdf->excelData[$i][2],$pdf->excelData[$i][1],ABS($pdf->excelData[$i][2]-$pdf->excelData[$i][1]));
    }
    $this->pdf->excelData[]=array('activeShare','$activeShare/2',$activeShare/2);
    $activeShare=$activeShare/2;
    

    $VaR=100+$verwachtRendement['verwachtRendement']-(2*$standaarddeviatie);
    $this->pdf->excelData[]=array('VaR','100+$verwachtRendement-(2*$standaarddeviatie)','100+'.$verwachtRendement['verwachtRendement'].'-(2*'.$standaarddeviatie.')',$VaR);
    $this->pdf->Ln();
 		$this->pdf->SetWidths(array(10,40,15,200));
  	$this->pdf->SetAligns(array('L','L','L','L'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	$this->pdf->row(array('','','','Standaarddeviatie'));
	  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $body="Standaarddeviatie wordt gebruikt als een maatstaf voor de risicograad van beleggingen. Het geeft de mate van afwijking van een gemiddelde weer. Risico bij beleggen is te omschrijven als de kans dat het werkelijke rendement afwijkt van het verwachte rendement. Dit kan dus zowel een lager als een hoger rendement betekenen. Een hogere standaarddeviatie geeft aan dat sprake is van een hoger risico. Ander woorden voor standaarddeviatie zijn volatiliteit en beweeglijkheid. De rendementen van aandelen schommelen meer dan die van obligaties. Dit komt tot uitdrukking in het verschil in standaarddeviatie,die bij obligaties doorgaans lager is. Naarmate de rendementen in het verleden meer schommelden, is de standaarddeviatie hoger en dat geldt daarmee ook voor het risico. N.B. Dit zijn cijfers gebaseerd op rendementen in het verleden. Toekomstige marktontwikkelingen kunnen voor een heel andere uitkomst zorgen."; 
  	$this->pdf->row(array('','Standaarddeviatie',$this->formatGetal($standaarddeviatie,1).'%',$body));
    $kop="Verschil AFM standaarddeviatie en de standaarddeviatie van uw portefeuille. Castanje presenteert twee verschillende standaarddeviaties.";
    $body="De AFM standaarddeviatie: Hierbij is de standaarddeviatie niet berekend op basis van eigen historische cijfers, maar wordt er gebruik gemaakt van voorgeschreven gegevens die voor de gehele markt dezelfde zijn. De portefeuille standaarddeviatie is door Castanje berekend op basis van de historische rendementen van de beleggingen binnen uw portefeuille.";
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->ln();
  	$this->pdf->row(array('','','',$kop));
	  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','AFM-Standaarddeviatie',$this->formatGetal($afm['std'],1).'%',$body));
    $this->pdf->ln();
  	$this->pdf->row(array('','VaR',$this->formatGetal($VaR,1).'%','Value at Risk geeft het verwachte maximale verlies aan met een waarschijnlijkheid van 95%. De historische VaR is bepaald aan de hand van de werkelijke jaarlijkse rendementsverdeling over de afgelopen tien jaar.'));
    $this->pdf->ln();
  	$this->pdf->row(array('','MDD',$this->formatGetal($maxDrawdown,1).'%','Maximum Drawdown geeft de maximale daling weer vanaf de hoogste waarde in een specifieke periode. Deze periode betreft in de overzichten een periode van tien jaar.'));
    $this->pdf->ln();
    $this->pdf->row(array('','TE',$this->formatGetal($trackingError,1).'%','De Tracking-error geeft een indicatie weer van de mate van afwijking van het rendement van de portefeuille ten opzichte van de benchmark.'));
    $this->pdf->ln();
    $this->pdf->row(array('','Active Share',$this->formatGetal($activeShare,1).'%','De Active Share geeft een indicatie van de afwijking van de opbouw van de portefeuille ten opzichte van de opbouw van de benchmark.'));
//echo $standaarddeviatie;
  }



}
?>

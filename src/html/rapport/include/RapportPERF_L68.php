<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/04/01 16:54:10 $
File Versie					: $Revision: 1.61 $

$Log: RapportPERF_L68.php,v $
Revision 1.61  2020/04/01 16:54:10  rvv
*** empty log message ***

Revision 1.60  2019/11/02 15:20:30  rvv
*** empty log message ***

Revision 1.59  2019/09/28 17:20:17  rvv
*** empty log message ***

Revision 1.58  2019/08/17 18:24:00  rvv
*** empty log message ***

Revision 1.57  2019/06/26 15:11:21  rvv
*** empty log message ***

Revision 1.56  2019/05/29 15:45:16  rvv
*** empty log message ***

Revision 1.55  2019/05/15 15:32:37  rvv
*** empty log message ***

Revision 1.54  2019/05/11 16:48:39  rvv
*** empty log message ***

Revision 1.53  2019/03/02 18:21:47  rvv
*** empty log message ***

Revision 1.52  2019/02/16 19:23:35  rvv
*** empty log message ***

Revision 1.51  2019/01/26 19:33:28  rvv
*** empty log message ***

Revision 1.50  2018/12/15 17:49:14  rvv
*** empty log message ***

Revision 1.49  2018/12/12 16:19:08  rvv
*** empty log message ***

Revision 1.48  2018/12/08 18:28:30  rvv
*** empty log message ***

Revision 1.47  2018/11/03 18:45:31  rvv
*** empty log message ***

Revision 1.46  2018/10/06 17:20:57  rvv
*** empty log message ***

Revision 1.45  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.44  2018/06/27 16:13:50  rvv
*** empty log message ***

Revision 1.43  2018/06/20 16:40:16  rvv
*** empty log message ***

Revision 1.42  2018/05/26 17:23:51  rvv
*** empty log message ***

Revision 1.41  2018/05/16 15:32:27  rvv
*** empty log message ***

Revision 1.40  2018/04/22 09:29:52  rvv
*** empty log message ***

Revision 1.39  2018/04/21 17:55:51  rvv
*** empty log message ***

Revision 1.38  2018/03/25 10:16:55  rvv
*** empty log message ***

Revision 1.37  2018/03/14 17:17:41  rvv
*** empty log message ***

Revision 1.36  2018/02/14 16:53:20  rvv
*** empty log message ***

Revision 1.35  2018/02/04 15:47:35  rvv
*** empty log message ***

Revision 1.34  2018/01/25 07:18:49  rvv
*** empty log message ***

Revision 1.33  2018/01/24 17:06:58  rvv
*** empty log message ***

Revision 1.32  2017/11/22 17:03:24  rvv
*** empty log message ***

Revision 1.31  2017/11/08 17:12:56  rvv
*** empty log message ***

Revision 1.30  2017/10/11 14:57:30  rvv
*** empty log message ***

Revision 1.29  2017/09/13 15:45:00  rvv
*** empty log message ***

Revision 1.28  2017/09/09 18:01:12  rvv
*** empty log message ***

Revision 1.27  2017/09/06 16:31:45  rvv
*** empty log message ***

Revision 1.26  2017/09/02 17:15:13  rvv
*** empty log message ***

Revision 1.25  2017/08/30 15:03:56  rvv
*** empty log message ***

Revision 1.24  2017/07/23 13:36:28  rvv
*** empty log message ***

Revision 1.23  2017/07/05 16:06:40  rvv
*** empty log message ***

Revision 1.22  2017/07/03 11:27:20  rvv
*** empty log message ***

Revision 1.21  2017/07/01 13:24:28  rvv
*** empty log message ***

Revision 1.20  2017/07/01 11:16:18  rvv
*** empty log message ***

Revision 1.19  2017/06/18 09:18:24  rvv
*** empty log message ***

Revision 1.18  2017/05/25 14:35:58  rvv
*** empty log message ***

Revision 1.17  2017/04/12 15:38:15  rvv
*** empty log message ***

Revision 1.16  2017/03/23 11:44:51  rvv
*** empty log message ***

Revision 1.15  2017/03/22 16:53:22  rvv
*** empty log message ***

Revision 1.14  2017/03/18 20:30:12  rvv
*** empty log message ***

Revision 1.13  2017/03/15 16:36:10  rvv
*** empty log message ***

Revision 1.12  2017/02/05 16:22:39  rvv
*** empty log message ***

Revision 1.11  2017/01/15 08:01:57  rvv
*** empty log message ***

Revision 1.10  2017/01/04 16:22:50  rvv
*** empty log message ***

Revision 1.9  2016/12/17 16:33:26  rvv
*** empty log message ***

Revision 1.8  2016/10/02 12:38:58  rvv
*** empty log message ***

Revision 1.7  2016/06/19 15:22:08  rvv
*** empty log message ***

Revision 1.6  2016/06/12 10:27:20  rvv
*** empty log message ***

Revision 1.5  2016/05/29 13:47:41  rvv
*** empty log message ***

Revision 1.4  2016/05/29 13:26:30  rvv
*** empty log message ***

Revision 1.3  2016/05/21 19:00:02  rvv
*** empty log message ***

Revision 1.2  2016/05/08 19:24:24  rvv
*** empty log message ***

Revision 1.1  2016/05/04 16:08:25  rvv
*** empty log message ***

Revision 1.3  2016/03/19 16:51:09  rvv
*** empty log message ***

Revision 1.3  2015/12/16 17:06:48  rvv
*** empty log message ***

Revision 1.2  2015/10/04 11:52:21  rvv
*** empty log message ***

Revision 1.1  2015/09/05 16:48:04  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L68.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");

class RapportPERF_L68
{

	function RapportPERF_L68($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
    $this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Performancemeting over de categorieën";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->doorkijkfondsen=array();
    $this->benchmarkVerdeling=array();
    $this->att=new ATTberekening_L68($this);
    $this->wegingInCategorie=array();
    $this->indicesPerHoofdcategorie=array();
    $this->planVerdelingNotSet=array();
    $this->debug=$_POST['debug'];
    if($this->debug)
      $this->beginTijd=getmicrotime();

   // $this->stdev=new rapportSDberekening($this->portefeuille,$this->rapportageDatum,0);
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
	 
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$DB = new DB();

		// voor data
		$this->pdf->widthA = array(5,80,30,5,30,5,30,120);
		$this->pdf->alignA = array('L','L','R','L','R');

		// voor kopjes
		$this->pdf->widthB = array(0,85,30,5,30,5,30,120);
		$this->pdf->alignB = array('L','L','R','L','R');

		$this->pdf->AddPage();
    $this->pdf->templateVars['PERFPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['PERFPaginas']=$this->pdf->rapport_titel;

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);

    $query="SELECT date(startdatumMeerjarenrendement) as startdatum FROM Portefeuilles WHERE Portefeuille='".$this->portefeuille."'";
    $DB->SQL($query);
    $DB->Query();
    $startdatum=$DB->lookupRecord();
    if($startdatum['startdatum'] <> '0000-00-00')
      $startDatum=$startdatum['startdatum'];
    else
      $startDatum=substr($this->pdf->PortefeuilleStartdatum,0,10);

    //$this->getBenchmarkVerdeling();
    $this->getKleuren();
    if($this->debug)
      echo (getmicrotime()-$this->beginTijd)." run tijd getKleuren.<br>\n";
    
    if ($this->pdf->lastPOST['doorkijk'] == 1)
      $hpiGebruik=true;
    else
      $hpiGebruik=false;

    $this->waarden['historie'] = $this->att->bereken($startDatum, $this->rapportageDatum, 'Hoofdcategorie',$hpiGebruik);

    $this->att=new ATTberekening_L68($this);
    $this->att->perioden='weken';
    $this->waarden['historieWeken'] = $this->att->bereken($startDatum, $this->rapportageDatum, 'Hoofdcategorie',$hpiGebruik);


    if($this->debug)
      echo (getmicrotime()-$this->beginTijd)." run tijd historie.<br>\n";
    $this->toonData($this->waarden['historie'], 'alles');
    if($this->debug)
      echo (getmicrotime()-$this->beginTijd)." run tijd toonData klaar.<br>\n";

    $this->addResultaat();
    if($this->debug)
      echo (getmicrotime()-$this->beginTijd)." run tijd addResultaat.<br>\n";
 
    //$this->indexVergelijking();
	}

  function getBeleggingsplan($portefeuille,$datum)
  {
    $DB=new DB();
    $query="SELECT Beleggingsplan.ProcentRisicoDragend/100 as ZAK,
Beleggingsplan.ProcentRisicoMijdend/100 as VAR,
(100-Beleggingsplan.ProcentRisicoDragend-Beleggingsplan.ProcentRisicoMijdend)/100 as Liquiditeiten
FROM
Beleggingsplan 
WHERE  Beleggingsplan.Portefeuille='$portefeuille' AND (datum <= '".$datum."' OR datum='0000-00-00') ORDER by datum desc limit 1";
    $DB->SQL($query);
    $DB->Query();
    $data=$DB->nextRecord();
    return $data;
  }

  function getDoorkijkfondsen()
  {
    $DB=new DB();
    $query="SELECT
Fondsen.Fonds,
Fondsen.Portefeuille,
Portefeuilles.Vermogensbeheerder
FROM
Fondsen
INNER JOIN Portefeuilles ON Fondsen.Portefeuille = Portefeuilles.Portefeuille
LEFT JOIN Beleggingsplan ON Portefeuilles.Portefeuille = Beleggingsplan.Portefeuille
WHERE Fondsen.Portefeuille<>'' AND Portefeuilles.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $DB->SQL($query);
    $DB->Query();
    while($data=$DB->nextRecord())
    {
      $huisfondsen[$data['Fonds']]=$data['Portefeuille'];
    }
    $this->doorkijkfondsen=$huisfondsen;
  }




  function getIndexPerBeeleggingscategorie($portefeuille,$datum,$categorie)
  {
    $DB=new DB();
    $query="SELECT IndexPerBeleggingscategorie.Beleggingscategorie,IndexPerBeleggingscategorie.Fonds FROM IndexPerBeleggingscategorie 
      WHERE IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
            AND (IndexPerBeleggingscategorie.Portefeuille='".$portefeuille."' or IndexPerBeleggingscategorie.Portefeuille='') AND (vanaf < '$datum' OR vanaf ='0000-00-00') AND Beleggingscategorie='$categorie'
            ORDER BY IndexPerBeleggingscategorie.Portefeuille, vanaf desc limit 1";
    $DB->SQL($query); //echo $query."<br>\n";
    $DB->Query();
    $index=$DB->nextRecord();
   // echo "$datum $categorie ".$index['Fonds']."<br>\n";
    return $index['Fonds'];

  }


function toonData($perfdata,$periode='maand',$hcatDataJarenShort=array())
{

  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

  $DB = new DB();
  $query="SELECT IndexPerBeleggingscategorie.Beleggingscategorie,IndexPerBeleggingscategorie.Fonds FROM IndexPerBeleggingscategorie WHERE IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' 
      AND (IndexPerBeleggingscategorie.Portefeuille='".$this->portefeuille."' or IndexPerBeleggingscategorie.Portefeuille='')
      ORDER BY IndexPerBeleggingscategorie.Portefeuille";
$DB->SQL($query);
$DB->Query();
while($index=$DB->nextRecord())
  $indexLookup[$index['Beleggingscategorie']]=$index['Fonds'];
$indexLookup['totaal']=$this->portefeuilledata['SpecifiekeIndex'];



$query="SELECT
Beleggingscategorien.Omschrijving,
Beleggingscategorien.Beleggingscategorie,
IndexPerBeleggingscategorie.Fonds
FROM
CategorienPerHoofdcategorie
Inner Join Beleggingscategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = Beleggingscategorien.Beleggingscategorie
INNER JOIN IndexPerBeleggingscategorie ON CategorienPerHoofdcategorie.Beleggingscategorie = IndexPerBeleggingscategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = IndexPerBeleggingscategorie.Vermogensbeheerder
WHERE CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
";
    $DB->SQL($query);
    $DB->Query();

while($cat=$DB->nextRecord())
{
  $hCategorieOmschrijvingen[$cat['Beleggingscategorie']] = $cat['Omschrijving'];
  $this->indicesPerHoofdcategorie[$cat['Beleggingscategorie']][]=$cat['Fonds'];
}
  $datumStop  = db2jul($this->rapportageDatum);
 // if($periode=='maand')
  //  $maandPeriode=mktime(0,0,0,1,1,date("Y",$datumStop));//-1


  $jaar=date("Y",db2jul($this->rapportageDatum));
  if(db2jul("$jaar-01-01")<db2jul($this->pdf->PortefeuilleStartdatum))
    $beginJaar=$this->pdf->PortefeuilleStartdatum;
  else
    $beginJaar="$jaar-01-01";



  $this->pdf->CellBorders=array();
  $this->pdf->setY(45);
  $this->pdf->ln();
  $this->pdf->CellBorders = array();
  $YendIndex = $this->pdf->GetY();
  $categorien=array('ZAK','VAR');//,'totaal');
  $maanden=array(0,'jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
  if($this->debug)
    echo (getmicrotime()-$this->beginTijd)." run toonData begin loop.<br>\n";
  foreach ($categorien as $cat)
  { 
    $perfIndexCum=1;
    $perfCum=1;

    foreach ($perfdata[$cat]['perfWaarden'] as $datum=>$data)
    {

      $juldate=db2jul($datum);
      if(1)//$juldate > $maandPeriode)
      {
        /*
           $fondsen=$this->benchmarkVerdelingOpDatum($datum,$cat);
           $beginDatumJul=mktime(0, 0, 0, substr($datum, 5, 2), 0, substr($datum, 0, 4));
           $beginDatum=date("Y-m-d",$beginDatumJul);

           if($beginDatumJul<db2jul($this->pdf->PortefeuilleStartdatum))
             $beginDatum=substr($this->pdf->PortefeuilleStartdatum,0,10);

           $perfIndex=$this->getFondsPerformance($fondsen,$beginDatum, $datum)/100;
  //         listarray($fondsen); listarray($perfdata['totaal']['perfWaarden'][$datum]['indexVerdeling']);
  //      echo "$datum $cat | $perfIndex | ".$perfdata['totaal']['perfWaarden'][$datum]['indexPerf']." <br>\n";
        */
        $perfIndex=$perfdata[$cat]['perfWaarden'][$datum]['indexPerf'];
        

           $this->wegingInCategorie['historie'][$datum][$cat]['aandeel']=$data['eindwaarde']/$perfdata['totaal']['perfWaarden'][$datum]['eindwaarde'];
           $this->wegingInCategorie['historie'][$datum][$cat]['benchmarkPerf']=$perfIndex;

         $perfIndexCum=($perfIndexCum  * (1+$perfIndex)) ;

         if($this->pdf->debug==true)
         {
           echo "<b> cululatief ".round($perfIndexCum*100,4)."</b><br>\n";
         }
         $data['specifiekeIndex']=($perfIndexCum-1)*100;
         $perfCum=$perfCum*(1+$data['procent']);
         $data['index']=($perfCum-1)*100;
         $hcatWaarden['periode'][$cat]['portefeuille'][]=$data['index'];

        // $maand=$maanden[date("n",$juldate)];
         $maand=date("M",$juldate);
         $hcatWaarden['periode'][$cat]['specifiekeIndex'][]=$data['specifiekeIndex'];
         if($periode=='jaar')
           $hcatWaarden['periode'][$cat]['datum'][]= $maand.date(" y",$juldate);
         else  
           $hcatWaarden['periode'][$cat]['datum'][]= $maand.date(" y",$juldate);
         $hcatWaarden['periode'][$cat]['waarde'][]=$data;
     //     listarray($data);
      }
    }
  }
  if($this->debug)
    logscherm( (getmicrotime()-$this->beginTijd)." run toonData benchmarkVerdelingOpDatum getFondsPerformance klaar.<br>\n");

 

  $stdev=new rapportSDberekening($this->portefeuille,$this->rapportageDatum,1);

  $stdev->filterJaarovergang=false;
  $stdev->noTotaal=true;
  //$stdev->addReeks('hoofdCategorie');

  foreach($this->waarden['historieWeken'] as $categorie=>$categorieData)
  {

    $nieuwePortReeks=array();
    $nieuweBenchReeks=array();
    if($categorie=='VAR' || $categorie=='ZAK' || $categorie == 'totaal')
    {
      foreach ($categorieData['perfWaarden'] as $datum => $perfWaarden)
      {
        //$perfIndex=$this->waarden['historieWeken'][$categorie]['perfWaarden'][$datum]['procent'];
        $nieuwePortReeks[$datum]['perf'] = $perfWaarden['procent'] * 100;//$tmp['perf']*100;
        $nieuwePortReeks[$datum]['datum'] = $datum;

        //$perfIndex=$this->waarden['historieWeken'][$categorie]['perfWaarden'][$datum]['indexPerf'];
        $nieuweBenchReeks[$datum]['perf'] = $perfWaarden['indexPerf'] * 100;//$tmp['perf']*100;
        $nieuweBenchReeks[$datum]['datum'] = $datum;
      }
      $stdev->reeksen[$categorie] = $nieuwePortReeks;
      $stdev->reeksen[$categorie . 'Index'] = $nieuweBenchReeks;
    }
  }
/*
  listarray($this->waarden['historieWeken']['VAR']);
  foreach($this->waarden['historieWeken']['VAR']['perfWaarden'] as $datum=>$waarden)
  {
    echo $datum ." | ".round($waarden['procent'],5)."<br>\n";
  }
  listarray($stdev);exit;

    foreach($stdev->reeksen as $reeks=>$waarden)
    {
      $nieuwePortReeks=array();
      $nieuweBenchReeks=array();
      if($reeks=='VAR' || $reeks=='ZAK' || $reeks == 'totaal')
      {
        foreach($waarden as $datum=>$perfWaarden)
        {
          $perfIndex=$this->waarden['historieWeken'][$reeks]['perfWaarden'][$datum]['procent'];
          $nieuwePortReeks[$datum]['perf']=$perfIndex*100;//$tmp['perf']*100;
          $nieuwePortReeks[$datum]['datum']=$datum;

          $perfIndex=$this->waarden['historieWeken'][$reeks]['perfWaarden'][$datum]['indexPerf'];
          $nieuweBenchReeks[$datum]['perf']=$perfIndex*100;//$tmp['perf']*100;
          $nieuweBenchReeks[$datum]['datum']=$datum;
        }
        $stdev->reeksen[$reeks]=$nieuwePortReeks;
        $stdev->reeksen[$reeks.'Index']=$nieuweBenchReeks;
                
      } 
    }
    */

    $this->grafiekWaarden=$stdev->reeksen;
  if($this->debug)
    logscherm( (getmicrotime()-$this->beginTijd)." run toonData indexPerformance .<br>\n");
  //  $stdev->addReeks('afm');
    $stdev->berekenWaarden(true);
  if($this->debug)
    logscherm( (getmicrotime()-$this->beginTijd)." run toonData berekenWaarden .<br>\n");

  $this->pdf->setY(40);
  $this->pdf->SetAligns('L','L','R');

    foreach($stdev->standaardDeviatieReeksen as $reeks=>$reeksData)
    {
      foreach($reeksData as $datum=>$meting)
      {
        if($reeks=='VAR' || $reeks=='ZAK' || $reeks == 'totaal')
        {
          $tmp=array('laatsteMeting'=>$datum,'stdev'=>$meting['stdev']);
          $tmp['stdevIndex']=$stdev->standaardDeviatieReeksen[$reeks.'Index'][$datum]['stdev'];
          $standaardDeviatieReeksen[$reeks][]=$tmp;
        }
        if($reeks=='afm')
          $standaardDeviatieReeksen[$reeks][]=array('laatsteMeting'=>$datum,'stdev'=>$meting['stdev']);
      }
    }

//procent
$grafiekData=array();
$counterBegin=count($this->pdf->excelData);
$catId=-1;
$xlsHeader=array('datum');
$skipIndex=array('afm','totaalInformatieratio','totaalTrackingError');
$maanden=array(0,'jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');

  foreach($standaardDeviatieReeksen as $cat=>$reeksData)
{
  array_push($xlsHeader,'perf '.$cat);
  array_push($xlsHeader,'index '.$cat);
  $catId=$catId+2;
  $counter=$counterBegin;
  foreach($reeksData as $waarden)
  {
    
    $grafiekData[$cat]['portefeuille'][]=$waarden['stdev'];
    if(!in_array($cat,$skipIndex))
      $grafiekData[$cat]['specifiekeIndex'][]=$waarden['stdevIndex'];
   // $grafiekData[$cat]['datum'][]=$maanden[date('n',db2jul($waarden['laatsteMeting']))]."-".date('y',db2jul($waarden['laatsteMeting']));
    $grafiekData[$cat]['datum'][]=date('M',db2jul($waarden['laatsteMeting']))." ".date('y',db2jul($waarden['laatsteMeting']));


    $this->pdf->excelData[$counter+1][0] = $waarden['laatsteMeting'];  
    $this->pdf->excelData[$counter+1][$catId] = $waarden['stdev'];     
    $this->pdf->excelData[$counter+1][$catId+1] = $waarden['stdevIndex'];  
    

    $counter++;
  }
}
$this->pdf->excelData[$counterBegin]=$xlsHeader;

$grafiekData['ZAK']['titel']='Verloop standaarddeviatie zakelijke waarden';
$grafiekData['VAR']['titel']='Verloop standaarddeviatie vastrentende waarden';
$grafiekData['totaal']['titel']='Verloop standaarddeviatie portefeuille';
$grafiekData['afm']['titel']='Verloop AFM-standaarddeviatie wekelijks gemeten';
$grafiekData['totaalTrackingError']['titel']='Tracking error';
$grafiekData['totaalInformatieratio']['titel']='Informatieratio';

$grafiekData['ZAK']['legenda']=array('portefeuille','benchmark');
$grafiekData['VAR']['legenda']=array('portefeuille','benchmark');
$grafiekData['totaal']['legenda']=array('portefeuille','benchmark');
$grafiekData['afm']['legenda']=array('portefeuille');
$grafiekData['totaalTrackingError']['legenda']=array('portefeuille');
$grafiekData['totaalInformatieratio']['legenda']=array('portefeuille');
  if($this->debug)
    echo (getmicrotime()-$this->beginTijd)." run toonData grafieken .<br>\n";
  $this->pdf->setY(105);
  $this->pdf->SetWidths(array(15,60,7.5,60,7.5,60,7.5,60,7.5,60));
  $this->pdf->SetAligns('L','C','L','C','L','C','L','C','L','C');
  $this->pdf->row(array('','Rendement zakelijke waarden','','Rendement vastrentende waarden','','Standaarddeviatie zakelijke waarden','','Standaarddeviatie vastrentende waarden'));
  $chartData=$hcatWaarden;
  
  $chartData['periode']['ZAK']['legenda']=array('portefeuille','benchmark');
  $chartData['periode']['VAR']['legenda']=array('portefeuille','benchmark');
  if(count($this->planVerdelingNotSet['totaal'])>0)
  {

    $grafiekData['ZAK']['legenda']=array('portefeuille');
    $grafiekData['VAR']['legenda']=array('portefeuille');
     unset( $grafiekData['ZAK']['specifiekeIndex']);
    unset( $grafiekData['VAR']['specifiekeIndex']);
  
  
    $chartData['periode']['ZAK']['legenda']=array('portefeuille');
    $chartData['periode']['VAR']['legenda']=array('portefeuille');
    unset( $chartData['periode']['ZAK']['specifiekeIndex']);
    unset( $chartData['periode']['VAR']['specifiekeIndex']);
  }
  

  
  if(isset($chartData['periode']['VAR']))
  {
    $this->pdf->setXY(15, 110);
    $this->LineDiagram(60, 40, $chartData['periode']['ZAK'], array($this->pdf->rapport_grafiek_pcolor, $this->pdf->rapport_grafiek_icolor), 0, 0, 6, 5, $periode);//50
  }
  if(isset($chartData['periode']['VAR']))
  {
    $this->pdf->setXY(85, 110);
    $this->LineDiagram(60, 40, $chartData['periode']['VAR'], array($this->pdf->rapport_grafiek_pcolor, $this->pdf->rapport_grafiek_icolor), 0, 0, 6, 5, $periode);//50
  }
  $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  

  $this->pdf->setXY(85 + 70, 110);
  $this->LineDiagram(60, 40, $grafiekData['ZAK'], array($this->pdf->rapport_grafiek_pcolor, $this->pdf->rapport_grafiek_icolor), 0, 0, 6, 5, 1, true);//50
  $this->pdf->setXY(85 + 140, 110);
  $this->LineDiagram(60, 40, $grafiekData['VAR'], array($this->pdf->rapport_grafiek_pcolor, $this->pdf->rapport_grafiek_icolor), 0, 0, 6, 5, 1, true);//50
  if($this->debug)
    logscherm( (getmicrotime()-$this->beginTijd)." run toonData grafieken klaar .<br>\n");
  
}


  function indexPerformance($categorie,$van,$tot)
    {
      global $__appvar;

      $tmp=$this->benchmarkVerdelingOpDatum($tot,$categorie);
      if(count($tmp)==0)
        $this->planVerdelingNotSet[$categorie][]=$tot;
      $perf=$this->getFondsPerformance($tmp,$van,$tot);
      //echo  "$categorie $van,$tot $perf <br>\n";exit;
      //$perf=getFondsPerformanceGestappeld($fonds,$this->portefeuille,$van,$tot,'maanden');
      return array('perf'=>($perf/100),'verdeling'=>$tmp);


    }

  function getFondsPerformance($fonds,$beginDatum,$eindDatum)
  {
    if(is_array($fonds))
    {
      $perf=0;
      foreach($fonds as $fondsDetail=>$percentage)
      {
        $beginKoers = globalGetFondsKoers($fondsDetail, $beginDatum);
        $eindKoers = globalGetFondsKoers($fondsDetail, $eindDatum);

        $perf += ($eindKoers - $beginKoers) / ($beginKoers) *$percentage*100;
        // echo "$beginDatum->$eindDatum  $fondsDetail ".(($eindKoers - $beginKoers) / ($beginKoers) )."  | $percentage;<br>\n";
        // echo "$eindDatum $fondsDetail |  som=$perf |  ".(($eindKoers - $beginKoers) / ($beginKoers) *$percentage)." = ($eindKoers - $beginKoers) / ($beginKoers) *$percentage;<br>\n";
      }
    }
    else
    {
      $beginKoers = globalGetFondsKoers($fonds, $beginDatum);
      $eindKoers = globalGetFondsKoers($fonds, $eindDatum);
      $perf = ($eindKoers - $beginKoers) / ($beginKoers / 100);
    }
    //echo $perf."<br>\n";
    return $perf;
  }

  function fondsPerf($fonds,$van,$tot)
  {
    $DB=new DB();
    $query="SELECT fonds,percentage FROM benchmarkverdeling WHERE benchmark='$fonds'";
    $DB->SQL($query);
    $DB->Query();
    $verdeling=array();
    while($data=$DB->nextRecord())
      $verdeling[$data['fonds']]=$data['percentage'];

    if(count($verdeling)==0)
      $verdeling[$fonds]=100;

    $totalPerf=0;
    foreach($verdeling as $fonds=>$percentage)
    {
      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '".substr($tot,0,4)."-01-01' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
    	$DB->SQL($query);
      $janKoers=$DB->lookupRecord();
      
      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$van' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
    	$DB->SQL($query);
      $startKoers=$DB->lookupRecord();

      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$tot' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
	    $DB->SQL($query);
      $eindKoers=$DB->lookupRecord();
      $perfVoorPeriode=($startKoers['Koers'] - $janKoers['Koers']) / ($janKoers['Koers']);
      $perfJaar=($eindKoers['Koers'] - $janKoers['Koers']) / ($janKoers['Koers']);
      $perf=$perfJaar-$perfVoorPeriode;
      
      if($this->pdf->debug==true)
      {
        echo "koers $fonds ".substr($tot,0,4)."-01-01 ".$janKoers['Koers']."<br>\n";
        echo "koers $fonds $van ".$startKoers['Koers']."<br>\n";
        echo "koers $fonds $tot ".$eindKoers['Koers']."<br>\n";
        echo "perf voor begin $perfVoorPeriode = (".$startKoers['Koers']." - ".$janKoers['Koers'].") / (".$janKoers['Koers'].")<br>\n";
        echo "Perf tot einddatum $perfJaar =(".$eindKoers['Koers']." - ".$janKoers['Koers'].") / ".($janKoers['Koers'])."<br>\n";
        echo "m<b> $fonds $van,$tot  $perf </b>= ( $perfJaar - $perfVoorPeriode ) <br>\n";
      }
      $totalPerf+=($perf*$percentage/100);
    }  
    //echo "t $fonds $totalPerf $van,$tot<br>\n";

    return $totalPerf;
  }

function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$periode='maand',$skipFirst=false)
  {
    global $__appvar;

    $legendDatum= $data['datum'];
    $legendaItems= $data['legenda'];
    if(isset($data['specifiekeIndex']))
      $data1 = $data['specifiekeIndex'];
    $data = $data['portefeuille'];





    if(count($data1)>0)
      $bereikdata = array_merge($data,$data1);
    else
      $bereikdata =   $data;

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $YDiag = $YPage ;
    $hDiag = floor($h );
    $XDiag = $XPage ;
    $lDiag = floor($w );

    $this->pdf->Rect($XDiag, $YDiag, $w, $h,'FD','',array(245,245,245));

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(155,155,155);
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
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / count($data);

    if($periode=='maand')
      $unit = $lDiag / 12;

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

    $nulpunt = $YDiag + ($maxVal * $waardeCorrectie);
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
    $lineStyle = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    $jaren=ceil(count($data)/6);
    for ($i=0; $i<count($data); $i++)
    {
      if($i%$jaren==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+7,$legendDatum[$i],25);
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      if($skipFirst==true && $i==0)
      {
        // skip first line
      }
      else
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
    //  if ($i>0)
    //    $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color);
   //   if ($i==count($data1)-1)
   //       $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color);
      $yval = $yval2;
    }

    
    if(is_array($data1))
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color1);

      for ($i=0; $i<count($data1); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
        if($skipFirst==true && $i==0)
        {
          // skip first line
        }
        else
          $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
   //     if ($i>0)
   //       $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color1);
   //     if ($i==count($data1)-1)
   //       $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color1);

         $yval = $yval2;
      }
    }
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));


  //   $XPage
   // $YPage

  //  $legendaItems=array('portefeuille','benchmark');
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
  function getKleuren()
  {
    $db=new DB();
    $query="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $db->SQL($query);
    $data=$db->lookupRecord();
    $this->kleuren=unserialize($data['grafiek_kleur']);
    if($this->kleuren['OIS']['Liquiditeiten']['G']['value']==0)
      $this->kleuren['OIS']['Liquiditeiten']=$this->kleuren['OIB']['Liquiditeiten'];
    foreach($this->kleuren as $groep=>$kleuren)
    {
      foreach($kleuren as $cat=>$kleurdata)
        $this->kleuren['alle'][$cat]=$kleurdata;
    }
  }

function getGrootboeken()
{
  $vertaling=array();
  $db=new DB();
  $query="SELECT Grootboekrekening,Omschrijving FROM Grootboekrekeningen";
  $db->SQL($query);
  $db->Query();
  while($data=$db->nextRecord())
  {
    //if($data['Grootboekrekening']=='BEH')
   //   $data['Omschrijving']="Beheervergoeding Trustpartners";
    //if($data['Grootboekrekening']=='BEW')
    //  $data['Omschrijving']="Administratiekosten bank";      
    //if($data['Grootboekrekening']=='KOST')
    //  $data['Omschrijving']="Transactiekosten bank";      
      
    $vertaling[$data['Grootboekrekening']]=$data['Omschrijving'];
  }
  return $vertaling;
}




 function addResultaat()
 {

  if(!isset($this->pdf->__appvar['consolidatie']))
  {
  // $this->pdf->__appvar['consolidatie']=1;
   $this->pdf->portefeuilles=array($this->portefeuille);
  }
  $rapParts=explode("-",$this->rapportageDatum);
  
  $kwartaal = ceil(date("n",db2jul($this->rapportageDatum))/3);
  if($kwartaal==1)
    $beginKwartaal=$rapParts[0]."-01-01";
  elseif($kwartaal==2)
    $beginKwartaal=$rapParts[0]."-03-31";
  elseif($kwartaal==3)
    $beginKwartaal=$rapParts[0]."-06-30";
  elseif($kwartaal==4)
    $beginKwartaal=$rapParts[0]."-09-30";
  if(db2jul($beginKwartaal)<db2jul($this->pdf->PortefeuilleStartdatum))
    $beginKwartaal=$this->pdf->PortefeuilleStartdatum;

  $jaar=date("Y",db2jul($this->rapportageDatum));
   if(db2jul("$jaar-01-01")<db2jul($this->pdf->PortefeuilleStartdatum))
     $beginJaar=$this->pdf->PortefeuilleStartdatum;
   else
     $beginJaar="$jaar-01-01";

  $vetralingGrootboek=$this->getGrootboeken();
  
   if ($this->pdf->lastPOST['doorkijk'] == 1)
     $hpiGebruik=true;
   else
     $hpiGebruik=false;
   
    $this->att=new ATTberekening_L68($this);
    $this->att->indexPerformance=false;
    $this->waarden['Periode']=$this->att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'Hoofdcategorie',$hpiGebruik);


//listarray($this->waarden['Periode']);exit;
    $this->waarden['Kwartaal']=$this->att->bereken($beginKwartaal,$this->rapportageDatum,'Hoofdcategorie',$hpiGebruik);
    $this->waarden['Jaar']=$this->att->bereken($beginJaar,$this->rapportageDatum,'Hoofdcategorie',$hpiGebruik);

   $categorien=array();
   foreach(array_keys($this->att->categorien) as $categorie)
   {
     if($this->waarden['Periode'][$categorie]['procent'] <> 0 ||
       $this->waarden['Periode'][$categorie]['beginwaarde'] <> 0 ||  $this->waarden['Periode'][$categorie]['storting'] <> 0 || $this->waarden['Periode'][$categorie]['onttrekking'] <> 0 ||
       $this->waarden['Periode'][$categorie]['eindwaarde'] <> 0)
     {
       $categorien[]=$categorie;
     }
   }
  
   $stdev=new rapportSDberekening($this->portefeuille,$this->rapportageDatum,1);
   $stdev->settings['SdFrequentie']='m';
   $stdev->setStartdatum($beginKwartaal);
   $stdev->settings['gebruikHistorischePortefeuilleIndex']=false;
   if(count($this->pdf->portefeuilles) > 1)
     $stdev->consolidatiePortefeuilles=$this->pdf->portefeuilles;
   $stdev->addReeks('benchmarkTot', '',true);//$this->index['SpecifiekeIndex']
   //$stdev->berekenWaarden();
  // $benchmarkKwartaalPercentage['totaal']=$stdev->getReeksRendement('benchmarkTot');
   $benchmarkKwartaalPercentage=array();
   foreach($categorien as $categorie)
   {
     //if($categorie<>'totaal')
    // {
       $startDatum=$beginKwartaal;
       foreach ($stdev->reeksen['benchmarkTot'] as $einddatum=>$waarden)
       {
        // $perf=$this->indexPerformance($categorie, $startDatum, $einddatum);
        // listarray($this->waarden['Kwartaal'][$categorie]['perfWaarden'][$einddatum]);exit;
         $perf=$this->waarden['Kwartaal'][$categorie]['perfWaarden'][$einddatum]['indexPerf'];
         $this->wegingInCategorie['Kwartaal'][$einddatum][$categorie]['aandeel'] = $this->waarden['Kwartaal'][$categorie]['perfWaarden'][$einddatum]['aandeelOpTotaal'];// $this->waarden['Kwartaal'][$categorie]['perfWaarden'][$einddatum]['eindwaarde']/ $this->waarden['Kwartaal']['totaal']['perfWaarden'][$einddatum]['eindwaarde'];
         $this->wegingInCategorie['Kwartaal'][$einddatum][$categorie]['benchmarkPerf'] = $perf;
         $this->wegingInCategorie['Kwartaal'][$einddatum]['totaal']['benchmarkPerf']+= $this->wegingInCategorie['Kwartaal'][$einddatum][$categorie]['aandeel'] * $perf;
         $startDatum=$einddatum;
         //logscherm("$categorie $einddatum $perf");
         $benchmarkKwartaalPercentage[$categorie] = (((1 + $benchmarkKwartaalPercentage[$categorie] / 100) * (1 + $perf )) - 1) * 100;
       }
    // }
   }


   $stdev=new rapportSDberekening($this->portefeuille,$this->rapportageDatum,1);
   $stdev->settings['SdFrequentie']='m';
   $stdev->setStartdatum($this->rapportageDatumVanaf);
   $stdev->settings['gebruikHistorischePortefeuilleIndex']=false;
   if(count($this->pdf->portefeuilles) > 1)
     $stdev->consolidatiePortefeuilles=$this->pdf->portefeuilles;
   $stdev->addReeks('benchmarkTot', '',true);//$this->index['SpecifiekeIndex']
   //$stdev->berekenWaarden();
   // $benchmarkKwartaalPercentage['totaal']=$stdev->getReeksRendement('benchmarkTot');
   $benchmarkPeriodePercentage=array();
   foreach($categorien as $categorie)
   {
     //if($categorie<>'totaal')
     // {
     $startDatum=$this->rapportageDatumVanaf;
     foreach ($stdev->reeksen['benchmarkTot'] as $einddatum=>$waarden)
     {
      // $perf=$this->indexPerformance($categorie, $startDatum, $einddatum);
       $perf=$this->waarden['Periode'][$categorie]['perfWaarden'][$einddatum]['indexPerf'];
       $this->wegingInCategorie['Periode'][$einddatum][$categorie]['aandeel'] =  $this->waarden['Periode'][$categorie]['perfWaarden'][$einddatum]['eindwaarde']/ $this->waarden['Periode']['totaal']['perfWaarden'][$einddatum]['eindwaarde'];
       $this->wegingInCategorie['Periode'][$einddatum][$categorie]['benchmarkPerf'] = $perf;
       $this->wegingInCategorie['Periode'][$einddatum]['totaal']['benchmarkPerf']+= $this->wegingInCategorie['Periode'][$einddatum][$categorie]['aandeel'] * $perf;
       $startDatum=$einddatum;
       $benchmarkPeriodePercentage[$categorie] = (((1 + $benchmarkPeriodePercentage[$categorie] / 100) * (1 + $perf )) - 1) * 100;
     }
     // }
   }



   if($this->pdf->lastPOST['doorkijk']==1)
   {
    // $benchmarkKwartaalPercentage['totaal']=0;
    // foreach ($this->wegingInCategorie['Kwartaal'] as $datum => $categorieData)
   //  {
     //  $benchmarkKwartaalPercentage['totaal'] = (((1 + $benchmarkKwartaalPercentage['totaal'] / 100) * (1 + $categorieData['totaal']['benchmarkPerf'])) - 1) * 100;
    // }
   }
  // listarray($this->wegingInCategorie['Kwartaal']);
  // listarray($benchmarkKwartaalPercentage);

   $stdev=new rapportSDberekening($this->portefeuille,$this->rapportageDatum,1);
   $stdev->settings['SdFrequentie']='m';
   $stdev->setStartdatum($beginJaar);
   $stdev->settings['gebruikHistorischePortefeuilleIndex']=false;
   if(count($this->pdf->portefeuilles) > 1)
     $stdev->consolidatiePortefeuilles=$this->pdf->portefeuilles;
   $stdev->addReeks('benchmarkTot', 'SpecifiekeIndex',true);//$this->index['SpecifiekeIndex']
 //  $stdev->berekenWaarden();
  // $benchmarkJaarPercentage['totaal']=$stdev->getReeksRendement('benchmarkTot');
   $benchmarkJaarPercentage=array();
   foreach($categorien as $categorie)
   {
    // if($categorie<>'totaal')
    // {
       $startDatum=$beginJaar;
       foreach ($stdev->reeksen['benchmarkTot'] as $einddatum=>$waarden)
       {
        // listarray($waarden);
         //$perf=$this->indexPerformance($categorie, $startDatum, $einddatum);
         $perf=$this->waarden['Jaar'][$categorie]['perfWaarden'][$einddatum]['indexPerf'];
         $this->wegingInCategorie['Jaar'][$einddatum][$categorie]['aandeel'] = $this->waarden['Jaar'][$categorie]['perfWaarden'][$einddatum]['eindwaarde']/ $this->waarden['Jaar']['totaal']['perfWaarden'][$einddatum]['eindwaarde'];
         $this->wegingInCategorie['Jaar'][$einddatum][$categorie]['benchmarkPerf'] = $perf;
         $this->wegingInCategorie['Jaar'][$einddatum]['totaal']['benchmarkPerf']+= $this->wegingInCategorie['Jaar'][$einddatum][$categorie]['aandeel'] * $perf;
         // echo "$categorie, $startDatum, $einddatum ". ($perf['perf']) ."<br>\n";ob_flush();
         $startDatum=$einddatum;
         $benchmarkJaarPercentage[$categorie] = (((1 + $benchmarkJaarPercentage[$categorie] / 100) * (1 + $perf)) - 1) * 100;
       }

//     }
   }


   $this->pdf->setY(40);
   $this->pdf->SetAligns('L','L','R');
   $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
   $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

   //
   // $perf=$stdev->fondsPerf($this->benchmarkVerdeling,$startDatum,$this->rapportageDatum);
  // echo $this->rapportageDatum;
   //$laatsteBenchmarkVerdeling=$this->benchmarkVerdelingOpDatum($this->rapportageDatum, 'totaal');

   //listarray($laatsteBenchmarkVerdeling);exit;
   if(count($this->waarden['Periode']['totaal']['perfWaarden'][$this->rapportageDatum]['indexVerdeling']) > 0)
   {
     $this->pdf->SetWidths(array(165, 100));
     $this->pdf->row(array('', 'De in deze rapportage gebruikte benchmark is opgebouwd uit:'));
     $this->pdf->SetWidths(array(165, 75, 15));
     $DB=new DB();
     foreach ($this->waarden['Periode']['totaal']['perfWaarden'][$this->rapportageDatum]['indexVerdeling'] as $fonds => $percentage)
     {
       if ($fonds <> '')
       {
         $query = "SELECT Omschrijving FROM Fondsen WHERE fonds='" . mysql_real_escape_string($fonds) . "'";
         $DB->SQL($query);
         $DB->Query();
         $fondsDetails = $DB->nextRecord();
         if($percentage<>0)
           $this->pdf->row(array('', $fondsDetails['Omschrijving'], $this->formatGetal($percentage*100, 0) . "%"));
       }
     }
     $bechmarkOnderdrukken=false;
   }
   else
   {
     $bechmarkOnderdrukken=true;
   }

   $this->pdf->setXY(8,30);



  $startPeriodeTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf));
    $startJaarTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($startDatum));
    $eindPeriodeTxt=date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum));

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
 // listarray($this->pdf->portefeuilles);
  $fillArray=array(0,1);
  $subOnder=array('','');
  $volOnder=array('U','U');
  $subBoven=array('','');
  $header=array("","");
  $samenstelling=array("",vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal));
  
  foreach($categorien as $categorie)
  {
    $volOnder[]='U';
    $volOnder[]='U';
    $subOnder[]='U';
    $subOnder[]='';
    $subBoven[]='T';
    $subBoven[]='';    
    $fillArray[]=1;
    $fillArray[]=1;
    $header[]=$this->att->categorien[$categorie];
    $header[]='';
    $samenstelling[]='';
    $samenstelling[]='';
   // $perfWaarden[$portefeuille]=$this->getWaarden($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
  }

  $perbegin=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)));
  $waardeRapdatum=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum)));
  $mutwaarde=array("",vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal));
  $stortingen=array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal));
  $onttrekking=array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal));
  $effectenmutaties=array("",vertaalTekst("Effectenmutaties gedurende verslagperiode",$this->pdf->rapport_taal));
  
  
  $resultaat=array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));

  $rendementJaar=array("",vertaalTekst("Rendement lopend jaar",$this->pdf->rapport_taal));
  $rendementBenchmarkJaar=array("",vertaalTekst("Rendement benchmark lopend jaar",$this->pdf->rapport_taal));
  $rendementKwartaal=array("",vertaalTekst("Rendement lopend kwartaal",$this->pdf->rapport_taal));
  $rendementPeriode=array("",vertaalTekst("Rendement verslagperiode",$this->pdf->rapport_taal));

  $rendementBenchmarkKwartaal=array("",vertaalTekst("Rendement benchmark lopend kwartaal",$this->pdf->rapport_taal));
   $rendementBenchmarkPeriode=array("",vertaalTekst("Rendement benchmark verslagperiode",$this->pdf->rapport_taal));
  $ongerealiseerd=array("",vertaalTekst("Ongerealiseerde resultaten",$this->pdf->rapport_taal)); //
  //$ongerealiseerdValuta=array("",vertaalTekst("Ongerealiseerde valutaresultaten",$this->pdf->rapport_taal)); //
  
$gerealiseerd=array("",vertaalTekst("Gerealiseerde resultaten",$this->pdf->rapport_taal)); //
//$gerealiseerdValuta=array("",vertaalTekst("Gerealiseerde valutaresultaten",$this->pdf->rapport_taal)); //
$valutaResultaat=array("",vertaalTekst("Koersresultaten vreemde valuta rekeningen",$this->pdf->rapport_taal)); //
$rente=array("",vertaalTekst("Mutatie opgelopen rente",$this->pdf->rapport_taal));//
$totaalOpbrengst=array("","");//totaalOpbrengst

    $totaalKosten=array("","");   //totaalKosten 
    $totaal=array("","");   //totaalOpbrengst-totaalKosten 


foreach($categorien as $categorie)
{
 // unset($this->waarden['Periode'][$categorie]['perfWaarden']);
}

  //listarray($this->waarden['Periode']);exit;
  foreach($categorien as $categorie)
  {
    $perfWaarden=$this->waarden['Periode'][$categorie];
    $perbegin[]=$this->formatGetal($perfWaarden['beginwaarde'],0,true);
    $perbegin[]='';
    $waardeRapdatum[]=$this->formatGetal($perfWaarden['eindwaarde'],0,true);
    $waardeRapdatum[]='';
    $mutwaarde[]=$this->formatGetal($perfWaarden['eindwaarde']-$perfWaarden['beginwaarde'],0,true);
    $mutwaarde[]='';
    
    if($categorie=='totaal')
    {
      if($this->pdf->lastPOST['doorkijk']==1)
        $effectenmutaties[]=$this->formatGetal($perfWaarden['fondsMutaties'], 0);
      else
        $effectenmutaties[]='';
      $effectenmutaties[]=''; 
     //$stort=getStortingen($this->rapport->portefeuille, $datumBegin, $datumEind)
     //$onttr=getOnttrekkingen($this->rapport->portefeuille, $datumBegin, $datumEind)
      $stortingen[]=$this->formatGetal($perfWaarden['storting'],0);
      $stortingen[]='';
      $onttrekking[]=$this->formatGetal($perfWaarden['onttrekking']*-1,0);
      $onttrekking[]='';
    }
    else
    {
      if($categorie=='VAR')
      {
        $effectenmutaties[] = $this->formatGetal($perfWaarden['stort']-($this->waarden['Periode']['totaal']['storting']+$this->waarden['Periode']['totaal']['onttrekking']*-1), 0);
        $effectenmutaties[] = '';
        $stortingen[]=$this->formatGetal($this->waarden['Periode']['totaal']['storting'],0);
        $stortingen[]='';
        $onttrekking[]=$this->formatGetal($this->waarden['Periode']['totaal']['onttrekking']*-1,0);
      }
      else
      {
        $effectenmutaties[] = $this->formatGetal($perfWaarden['stort'], 0);
        $effectenmutaties[] = '';
        $stortingen[] = '';//'$this->formatGetal($perfWaarden['kosten'],0);
        $stortingen[] = '';
        $onttrekking[] = '';//$this->formatGetal($perfWaarden['opbrengst'],0);
        $onttrekking[] = '';
      }
    }
    
    $totaalOpbrengstEUR=$perfWaarden['opbrengst']+
                        $perfWaarden['ongerealiseerdFondsResultaat']+
                        $perfWaarden['ongerealiseerdValutaResultaat']+
                        $perfWaarden['gerealiseerdFondsResultaat']+
                        $perfWaarden['gerealiseerdValutaResultaat']+
                        $perfWaarden['opgelopenrente'];
                  
    $perfWaarden['resultaatValuta']=$perfWaarden['resultaat']-($totaalOpbrengstEUR+$perfWaarden['kosten']);
    $totaalOpbrengstEUR+=$perfWaarden['resultaatValuta'];
    
    $resultaat[]=$this->formatGetal($perfWaarden['resultaat'],0);
    $resultaat[]='';

    if($categorie=='G-LIQ')
    {
      $rendementJaar[]='';
      $rendementJaar[]='';
      $rendementKwartaal[]='';
      $rendementKwartaal[]='';
      $rendementPeriode[]='';
      $rendementPeriode[]='';
    }
    else
    {
      $rendementJaar[]=$this->formatGetal($this->waarden['Jaar'][$categorie]['procent'],2);
      $rendementJaar[]='%';
      $rendementKwartaal[]=$this->formatGetal($this->waarden['Kwartaal'][$categorie]['procent'],2);
      $rendementKwartaal[]='%';
      $rendementPeriode[]=$this->formatGetal($this->waarden['Periode'][$categorie]['procent'],2);
      $rendementPeriode[]='%';
    }
    if($categorie=='totaal')
    {
    $ongerealiseerd[]=$this->formatGetal($perfWaarden['ongerealiseerdFondsResultaat']+$perfWaarden['ongerealiseerdValutaResultaat'],0);
    $ongerealiseerd[]='';
    //$ongerealiseerdValuta[]=$this->formatGetal($perfWaarden['ongerealiseerdValutaResultaat'],0);
    //$ongerealiseerdValuta[]='';
    $gerealiseerd[]=$this->formatGetal($perfWaarden['gerealiseerdFondsResultaat']+$perfWaarden['gerealiseerdValutaResultaat'],0);
    $gerealiseerd[]='';
    //$gerealiseerdValuta[]=$this->formatGetal($perfWaarden['gerealiseerdValutaResultaat'],0);
    //$gerealiseerdValuta[]='';
    $valutaResultaat[]=$this->formatGetal($perfWaarden['resultaatValuta'],0);
    $valutaResultaat[]='';
    $rente[]=$this->formatGetal($perfWaarden['opgelopenrente'],0);
    $rente[]='';
    $totaalOpbrengst[]='';
    $totaalOpbrengst[]='';
    $totaalOpbrengst[]=$this->formatGetal($totaalOpbrengstEUR,0);
    $totaalOpbrengst[]='';
    $totaalKosten[]='';
    $totaalKosten[]='';
    $totaalKosten[]=$this->formatGetal($perfWaarden['kosten'],0);
    $totaalKosten[]='';
    $totaal[]='';
    $totaal[]='';
    $totaal[]=$this->formatGetal($perfWaarden['resultaat'],0);
    $totaal[]='';
//
      if($bechmarkOnderdrukken==true)
      {
        $rendementBenchmarkJaar[] = '';
        $rendementBenchmarkKwartaal[]='';
        $rendementBenchmarkPeriode[]='';
        $rendementBenchmarkJaar[] = '';
        $rendementBenchmarkKwartaal[]='';
        $rendementBenchmarkPeriode[]='';

      }
      else
      {
        $rendementBenchmarkJaar[]=$this->formatGetal($benchmarkJaarPercentage[$categorie],2);
        $rendementBenchmarkJaar[]='%';
        $rendementBenchmarkKwartaal[]=$this->formatGetal($benchmarkKwartaalPercentage[$categorie],2);
        $rendementBenchmarkKwartaal[]='%';
        $rendementBenchmarkPeriode[]=$this->formatGetal($benchmarkPeriodePercentage[$categorie],2);
        $rendementBenchmarkPeriode[]='%';
      }

    
    foreach($perfWaarden['grootboekOpbrengsten'] as $categorie=>$waarde)
      if(round($waarde,2)!=0.00)
       $opbrengstCategorien[$categorie]=$categorie;
    foreach($perfWaarden['grootboekKosten'] as $categorie=>$waarde)
      if(round($waarde,2)!=0.00)
        $kostenCategorien[$categorie]=$categorie;  
    }
    else
    {

     // $rendementBenchmarkJaar[]=$this->formatGetal($this->waarden['Jaar'][$categorie]['indexPerf'],2);
     // $rendementBenchmarkJaar[]='%';
     // $rendementBenchmarkKwartaal[]=$this->formatGetal($this->waarden['Keartaal'][$categorie]['indexPerf'],2);
     // $rendementBenchmarkKwartaal[]='%';
      $rendementBenchmarkJaar[] = $this->formatGetal($benchmarkJaarPercentage[$categorie], 2);
      $rendementBenchmarkKwartaal[]=$this->formatGetal($benchmarkKwartaalPercentage[$categorie],2);
      $rendementBenchmarkPeriode[]=$this->formatGetal($benchmarkPeriodePercentage[$categorie],2);
      $rendementBenchmarkJaar[]='%';
      $rendementBenchmarkKwartaal[]='%';
      $rendementBenchmarkPeriode[]='%';

    }
  //  $rendementBenchmarkJaar[]=$this->formatGetal($benchmarkJaarPercentage[$categorie],2);
  //  $rendementBenchmarkJaar[]='%';
  //  $rendementBenchmarkKwartaal[]=$this->formatGetal($benchmarkKwartaalPercentage[$categorie],2);
  //  $rendementBenchmarkKwartaal[]='%';
    
  } 


  	$this->pdf->widthB = array(0,70,24,6,24,6,24,6,24,6,24,6,24,6,24,6,24,6);
		$this->pdf->alignB = array('L','L','R','L','R','L','R','L','R','L','R','L','R','L','R');
    $this->pdf->widthA = $this->pdf->widthB;//array(0,65,30,6,30,6,30,6,30,6,30,6,30,6);
		$this->pdf->alignA = array('L','L','R','L','R','L','R','L','R','L','R','L','R','L','R');
  

//listarray($perfWaarden);

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
  	
//    $this->pdf->fillCell=$fillArray;
//    $this->pdf->SetTextColor(255,245,245);
    $this->headerTop=$this->pdf->GetY();

    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
//    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
//		$this->pdf->Rect($this->pdf->marge+70, $this->pdf->getY(), (count($header)-2)*15, 8 , 'F');
	   $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row($header);
  //  unset($this->pdf->fillCell);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
//    $this->pdf->fillCell=array();
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		$this->pdf->row($perbegin);
	  //,$this->formatGetal($data['periode']['waardeBegin'],2,true),"",$this->formatGetal($data['ytm']['waardeBegin'],2,true),""));
    $this->pdf->CellBorders = $subOnder;
		$this->pdf->row($waardeRapdatum);//$this->formatGetal($data['periode']['waardeEind'],0),"",$this->formatGetal($data['ytm']['waardeEind'],0),""));
    $this->pdf->CellBorders = array();
			// subtotaal
		//$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->ln();
		$this->pdf->row($mutwaarde);//,$this->formatGetal($data['periode']['waardeMutatie'],0),"",$this->formatGetal($data['ytm']['waardeMutatie'],0),""));
		$this->pdf->row($stortingen);////,$this->formatGetal($data['periode']['stortingen'],0),"",$this->formatGetal($data['ytm']['stortingen'],0),""));
    $this->pdf->row($onttrekking);//,$this->formatGetal($data['periode']['onttrekkingen'],0),"",$this->formatGetal($data['ytm']['onttrekkingen'],0),""));
    $this->pdf->CellBorders = $subOnder;
    $this->pdf->row($effectenmutaties);
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row($resultaat);//,$this->formatGetal($data['periode']['resultaatVerslagperiode'],0),"",$this->formatGetal($data['ytm']['resultaatVerslagperiode'],0),""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->ln();

    $this->pdf->CellBorders = array();
    //$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		//$this->pdf->CellBorders = $volOnder;
    $this->pdf->row($rendementPeriode);
    if(count($this->planVerdelingNotSet['totaal'])<1)
      $this->pdf->row($rendementBenchmarkPeriode);
   // $this->pdf->row($rendementKwartaal);
   // $this->pdf->row($rendementBenchmarkKwartaal);
    $this->pdf->ln();
		$this->pdf->row($rendementJaar);//,$this->formatGetal($data['periode']['rendementProcent'],0),"%",$this->formatGetal($data['ytm']['rendementProcent'],0),"%"));
    if(count($this->planVerdelingNotSet['totaal'])<1)
      $this->pdf->row($rendementBenchmarkJaar);
    //$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();
		$ypos = $this->pdf->GetY();


		$this->pdf->SetY($ypos);
		$this->pdf->ln();


		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();

 }


 
  function indexVergelijking()
  {
    $DB=new DB();
    
	  $perioden=array('begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);
	  $query="SELECT
Indices.Beursindex,
Indices.specialeIndex,
Fondsen.Omschrijving,
Fondsen.Valuta,
Indices.toelichting
FROM
Indices
Inner Join Fondsen ON Indices.Beursindex = Fondsen.Fonds
WHERE Indices.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY Indices.Afdrukvolgorde";
   	$DB->SQL($query);
		$DB->Query();
    $indices=array();
	  while($index = $DB->nextRecord())
      $indices[]=$index;

$query="SELECT Portefeuilles.specifiekeIndex as Beursindex,
Fondsen.Omschrijving,
Fondsen.Valuta 
FROM Portefeuilles 
Inner Join Fondsen ON Portefeuilles.specifiekeIndex = Fondsen.Fonds  
WHERE Portefeuilles.Portefeuille = '$this->portefeuille'";
   	$DB->SQL($query);
		$DB->Query();
	  while($index = $DB->nextRecord())
    {
      $indices[]=array();
      $indices[]=$index;
    }

	  foreach($indices as $index)
		{
		  if($index['specialeIndex']==1)
      {
   	    $specialeBenchmarks[]=$index['Beursindex'];
		   	$specialeIndexData[$index['Beursindex']]=$index;
        foreach ($perioden as $periode=>$datum)
          $specialeIndexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Beursindex'],$datum);
  	  	$specialeIndexData[$index['Beursindex']]['performance'] =     ($specialeIndexData[$index['Beursindex']]['fondsKoers_eind'] - $specialeIndexData[$index['Beursindex']]['fondsKoers_begin']) / ($specialeIndexData[$index['Beursindex']]['fondsKoers_begin']/100 );
      }
      else
      {  
		    $benchmarks[]=$index['Beursindex'];
		   	$indexData[$index['Beursindex']]=$index;
        foreach ($perioden as $periode=>$datum)
        {
          $indexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Beursindex'],$datum);
          //$indexData[$index['Beursindex']]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
        }
  	  	$indexData[$index['Beursindex']]['performance'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']) / ($indexData[$index['Beursindex']]['fondsKoers_begin']/100 );
  		}
      //$indexData[$index['Beursindex']]['performanceEur'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind']*$indexData[$index['Beursindex']]['valutaKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin'])/($indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin']/100 );
		}
  
    /*
		$this->pdf->SetY(120);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	$this->pdf->SetWidths(array(150,60,20,20,20));
  	$this->pdf->SetAligns(array('L','L','R','R','R'));
    $this->pdf->Rect($this->pdf->marge+150,120,120,count($benchmarks)*4+4);
 	  $this->pdf->row(array("","Vergelijkingsmaatstaven","".date("d-m-Y",db2jul($perioden['begin'])),"".date("d-m-Y",db2jul($perioden['eind'])),"Rendement"));
  	unset($this->pdf->CellBorders);   
  	  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
 
  	foreach ($benchmarks as $fonds)
  	{  
        $fondsData=$indexData[$fonds];
        if($fondsData['Omschrijving']=='')
          $this->pdf->row(array(''));
        else
          $this->pdf->row(array('',$fondsData['Omschrijving'],
            $this->formatGetal($fondsData['fondsKoers_begin'],2),
            $this->formatGetal($fondsData['fondsKoers_eind'],2),
            $this->formatGetal($fondsData['performance'],2)."%"));
    }
    
    
    if(count($specialeBenchmarks) > 0)
    {
   	 	$this->pdf->SetY(150);
    	 $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	 	$this->pdf->SetWidths(array(150,60,20,20,20));
  	 	$this->pdf->SetAligns(array('L','L','R','R','R'));
     	$this->pdf->Rect($this->pdf->marge+150,150,120,count($specialeBenchmarks)*4+4);
 	   	$this->pdf->row(array("","Overige marktindices ter informatie","".date("d-m-Y",db2jul($perioden['begin'])),"".date("d-m-Y",db2jul($perioden['eind'])),"Rendement"));
  	 	unset($this->pdf->CellBorders);   
  	  	 $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
 
  	 	foreach ($specialeBenchmarks as $fonds)
  	 	{  
        $fondsData=$specialeIndexData[$fonds];
        if($fondsData['Omschrijving']=='')
          $this->pdf->row(array(''));
        else
          $this->pdf->row(array('',$fondsData['Omschrijving'],
            $this->formatGetal($fondsData['fondsKoers_begin'],2),
            $this->formatGetal($fondsData['fondsKoers_eind'],2),
            $this->formatGetal($fondsData['performance'],2)."%"));
     	}
    }
    */
    
  }
  function getFondsKoers($fonds,$datum)
  {
    $db=new DB();
    $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
    $db->SQL($query);
    $koers=$db->lookupRecord();
    return $koers['Koers'];
  }


}
?>
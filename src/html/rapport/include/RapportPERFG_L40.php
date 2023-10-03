<?
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/12/22 16:15:52 $
 		File Versie					: $Revision: 1.15 $

 		$Log: RapportPERFG_L40.php,v $
 		Revision 1.15  2018/12/22 16:15:52  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2018/12/21 17:49:27  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2017/03/25 16:01:09  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2017/03/22 16:53:22  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2017/02/04 19:11:39  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2017/02/01 16:44:57  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2017/01/22 10:59:19  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2017/01/21 17:48:13  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2017/01/19 08:27:29  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2017/01/19 08:05:11  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2017/01/19 07:11:26  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2017/01/18 17:02:28  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2017/01/16 17:56:42  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2017/01/16 07:03:15  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2017/01/15 11:14:43  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2016/07/02 09:36:03  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2013/07/17 15:52:10  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2011/09/03 14:29:38  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2011/07/03 06:41:41  rvv
 		*** empty log message ***

 		Revision 1.10  2009/11/08 14:11:31  rvv
 		*** empty log message ***

 		Revision 1.9  2009/08/05 11:32:08  rvv
 		*** empty log message ***

 		Revision 1.8  2009/07/18 14:11:49  rvv
 		*** empty log message ***

 		Revision 1.7  2009/01/17 12:40:55  rvv
 		*** empty log message ***

 		Revision 1.6  2009/01/06 13:44:06  cvs
 		NEUOFF percentages aanpassen

 		Revision 1.5  2009/01/06 12:38:10  cvs
 		procentWhiteSpace naar 20%

 		Revision 1.4  2009/01/06 12:05:39  cvs
 		*** empty log message ***

 		Revision 1.3  2009/01/06 10:26:04  cvs
 		Layout aanapssing tbv ATT

 		Revision 1.2  2008/12/18 10:28:03  rvv
 		*** empty log message ***

 		Revision 1.1  2008/12/17 13:42:41  rvv
 		*** empty log message ***


*/

include_once($__appvar["basedir"].'/html/indexBerekening.php');


class RapportPERFG_L40
{

  function RapportPERFG_L40($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		if($this->pdf->rapport_PERFGRAFIEK_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_PERFG_titel;
		else
			$this->pdf->rapport_titel = "Historisch rendement vanaf aanvang portefeuille";


		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


  function writeRapport()
	{

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank,Portefeuilles.Startdatum, Portefeuilles.AEXVergelijking, 
  Portefeuilles.kleurcode, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, 
  Portefeuilles.Risicoklasse, Portefeuilles.Client ,ModelPortefeuilles.Portefeuille as model,ModelPortefeuilles.Omschrijving as modelOmschrijving, Portefeuilles.Portefeuille
  FROM Portefeuilles 
  JOIN Clienten ON Portefeuilles.Client = Clienten.Client
  LEFT JOIN ModelPortefeuilles ON Portefeuilles.Portefeuille = ModelPortefeuilles.Portefeuille
  WHERE Portefeuilles.Portefeuille = '".$this->portefeuille."'  ";

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
    $this->pdf->portefeuilledata['model']=$this->portefeuilledata['model'];
    $this->pdf->portefeuilledata['modelOmschrijving']=$this->portefeuilledata['modelOmschrijving'];
    
$this->pdf->AddPage();
$this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0));


$DB = new DB();
$query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE periode='m' AND Portefeuille = '".$this->portefeuille."' AND Categorie = 'Totaal' ORDER BY Datum ASC LIMIT 1 ";
$DB->SQL($query);
$DB->Query();
$datum = $DB->nextRecord();


if(true)//$this->pdf->lastPOST['perfPstart'] == 1
{
  if($datum['id'] > 0)
  {
    if ($datum['month'] < 10)
    {
      $datum['month'] = "0" . $datum['month'];
    }
    $start = $datum['year'] . '-' . $datum['month'] . '-01';
  }
  else
  {
    $start=substr($this->portefeuilledata['Startdatum'],0,10);
  }
}
else
  $start = $this->rapportageDatumVanaf;
$eind = $this->rapportageDatum;

$datumStart = db2jul($start);
$datumStop  = db2jul($eind);

$index = new indexHerberekening();
$indexWaarden = $index->getWaarden($start,$eind,$this->portefeuille);


if($this->portefeuilledata['SpecifiekeIndex'] != '')
{
  $lookupDB = new DB();
  $lookupQuery = "SELECT Fondsen.Omschrijving FROM Fondsen WHERE Fondsen.Fonds = '".$this->portefeuilledata['SpecifiekeIndex']."'";
  $lookupDB->SQL($lookupQuery);
  $lookupRec = $lookupDB->lookupRecord();
  $indexFondsen[]=$this->portefeuilledata['SpecifiekeIndex'];
  $indexNaam[$this->portefeuilledata['SpecifiekeIndex']] = $lookupRec['Omschrijving'];
  $query="SELECT
benchmarkverdeling.fonds,
benchmarkverdeling.percentage,
Fondsen.Omschrijving
FROM benchmarkverdeling
JOIN Fondsen ON benchmarkverdeling.fonds=Fondsen.Fonds
WHERE
benchmarkverdeling.benchmark='". mysql_real_escape_string($this->portefeuilledata['SpecifiekeIndex'])."' ";
  $DB->SQL($query);
  $DB->Query();
  $benchmarkVoettekst='* Benchmark bestaande uit: ';//<weging1>% <fondsomschrijving1> / <weging2>% <fondsomschrijving2>
  $i=0;
  while ($data = $DB->nextRecord())
  {
    if($i>0)
      $benchmarkVoettekst.=' / ';
    $benchmarkVoettekst.=$data['percentage']."% ".$data['Omschrijving'];
    $i++;
  }
}

/*
$query = "SELECT Indices.Beursindex ,Indices.grafiekKleur
          FROM Indices
          WHERE Indices.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'  ORDER BY Indices.Afdrukvolgorde  ";
$DB->SQL($query);
$DB->Query();
while ($data = $DB->nextRecord())
{
	$indexFondsen[] = $data['Beursindex'];
	$indexKleuren[$data['Beursindex']] = unserialize($data['grafiekKleur']);
}
*/
$query = "SELECT BeleggingscategoriePerFonds.grafiekKleur, BeleggingscategoriePerFonds.Fonds
          FROM  BeleggingscategoriePerFonds
          WHERE BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND BeleggingscategoriePerFonds.Fonds IN('".implode("','",$indexFondsen)."') ";
$DB->SQL($query);
$DB->Query();
while ($data = $DB->nextRecord())
{
  if($data['grafiekKleur'] !='')
	  $indexKleuren[$data['Fonds']] = unserialize($data['grafiekKleur']);
}
    
   $portefeuilleKleur=array(125,201,125);//listarray(unserialize($this->portefeuilledata['kleurcode']));exit;
    $indexKleuren['portefeuille']=array('R'=>array('value'=>$portefeuilleKleur[0]),'G'=>array('value'=>$portefeuilleKleur[1]),'B'=>array('value'=>$portefeuilleKleur[2]));


$aantalWaarden = count($indexWaarden);
foreach ($indexWaarden as $id=>$waarden)
{
  $start = jul2sql(form2jul(substr($waarden['periodeForm'],0,10)));
  $eind = jul2sql(form2jul(substr($waarden['periodeForm'],13)));
  foreach ($indexFondsen as $fonds)
  {
 	  $q0 = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$eind."' AND Fonds = '$fonds'  ORDER BY Datum DESC LIMIT 1" ;
 	  $q1 = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$start."' AND Fonds = '$fonds'  ORDER BY Datum DESC LIMIT 1";
	  $DB->SQL($q0);
	  $DB->Query();
	  $koersEind = $DB->LookupRecord();
	  $DB->SQL($q1);
	  $DB->Query();
	  $koersStart = $DB->LookupRecord();
	  $perf = $koersEind['Koers'] /$koersStart['Koers']  ;
	  if($perf==0)
      $perf =1;
    $indexWaarden[$id]['fondsPerf'][$fonds] = $perf  ;

  //  echo "$eind $fonds $perf <br>";

    if(empty($indexWaarden[$id-1]['fondsIndex'][$fonds]))
	    $indexWaarden[$id]['fondsIndex'][$fonds] = $indexWaarden[$id]['fondsPerf'][$fonds];
	  else
  	  $indexWaarden[$id]['fondsIndex'][$fonds]  =($indexWaarden[$id]['fondsPerf'][$fonds]*$indexWaarden[$id-1]['fondsIndex'][$fonds]);

    $kwartaal=ceil(substr($eind,5,2)/3);
    $jaar=substr($eind,0,4);
    $periode=$jaar;//.'Q'.$kwartaal;

   	if(empty($indexTabel['cumulatief'][$fonds]['jaren']))
   	  $indexTabel['cumulatief'][$fonds]['jaren']=100;

   	if(empty($indexTabel['cumulatief'][$fonds]['cumulatief']))
   	   $indexTabel['cumulatief'][$fonds]['cumulatief']=100;

    $indexTabel['cumulatief'][$fonds]['jaren']      = ($indexTabel['cumulatief'][$fonds]['jaren']*($perf*100))/100;
    $indexTabel['cumulatief'][$fonds]['cumulatief'] = ($indexTabel['cumulatief'][$fonds]['cumulatief']*($perf*100))/100;
    $indexTabel[$periode][$fonds]['jaar'] = $indexTabel['cumulatief'][$fonds]['jaren'];

    if($lastPeriode != $periode || $aantalWaarden == $id)
    {
      $indexTabel['cumulatief'][$fonds]['jaren'] = 100;

    }
    $indexTabel[$periode][$fonds]['cumulatief'] = $indexTabel['cumulatief'][$fonds]['cumulatief'];
    $lastPeriode=$periode;
  }
}



$n=0;
$minVal = 99;
$maxVal = 101;

foreach ($indexWaarden as $id=>$data)
{
  $grafiekData['portefeuille'][$n]=$data['index'];
  $datumArray[$n] = $data['datum'];
  $kwartaal=ceil(substr($data['datum'],5,2)/3);
  $jaar=substr($data['datum'],0,4);
  $periode=$jaar;//.'Q'.$kwartaal;

  if(empty($indexTabel['cumulatief']['portefeuille']['jaren']))
    $indexTabel['cumulatief']['portefeuille']['jaren']=100;
  $indexTabel['cumulatief']['portefeuille']['jaren'] =($indexTabel['cumulatief']['portefeuille']['jaren']*(100+$data['performance'])/100);
  $indexTabel[$periode]['portefeuille']['jaar'] = $indexTabel['cumulatief']['portefeuille']['jaren'];
//echo $data['datum']."|". $data['performance']."<br>\n";
  //echo $data['datum']." $jaar.'Q'.$kwartaal ".$indexTabel['cumulatief']['portefeuille']['jaren']."<br>\n";
  if($lastPeriode!=$periode || $aantalWaarden == $id)
  {
    $indexTabel['cumulatief']['portefeuille']['jaren'] = 100;

  }
  $indexTabel[$periode]['portefeuille']['cumulatief'] = $data['index'];

  if($data['index'] != 0)
  {
    $maxVal=max($maxVal,$data['index']);
    $minVal=min($minVal,$data['index']);
  }

  foreach ($data['fondsIndex'] as $fonds=>$waarde)
  {
    $grafiekData[$fonds][$n]=$waarde *100;
    if($waarde != 0)
    {
      $maxVal=max($maxVal,$waarde *100);
      $minVal=min($minVal,$waarde *100);
    }
  }
  $lastPeriode=$periode;
  $n++;
}
  //  listarray($indexTabel);exit;

$this->pdf->setY(50);
$this->pdf->Row(array(''));
$this->pdf->SetFont($this->pdf->rapport_font, 'B', 8);
$this->pdf->setAligns(array('L','L','C','C'));
//$this->pdf->CellBorders = array('',array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'));
$this->pdf->setWidths(array(1,30,20+20,20));
    $this->pdf->Row(array('','','Cumulatief resultaat','+/- t.o.v.'));

//$this->pdf->CellBorders = array('',array('U','L','R'),'U',array('U','R'),'U',array('U','R'));
$this->pdf->setWidths(array(1,10,20,20,20,20));
$this->pdf->setAligns(array('L','L','R','R','R','R'));
$this->pdf->SetFont($this->pdf->rapport_font, '', 8);

$this->pdf->Row(array('','','Jaar','Groenstate','Benchmark','benchmark'));

//listarray($indexTabel);
foreach ($indexTabel as $datum=>$fondsen)
{
  if($datum<>'cumulatief')
  {
    $portefeuilleIndex=$indexTabel[$datum]['portefeuille']['cumulatief']-100;
    $benchmarkIndex=$indexTabel[$datum][$this->portefeuilledata['SpecifiekeIndex']]['cumulatief']-100;
    $verschil=$portefeuilleIndex-$benchmarkIndex;
    $jaar=substr($datum,0,4);
    if($jaar<> $laatsteJaar)
       $toonJaar=$jaar;
    else
       $toonJaar='';
    $kwartaal=substr($datum,5,1);
    $tmpArray=array('','',
                    $toonJaar,//'kwartaal '.$kwartaal,
                    $this->formatGetal($portefeuilleIndex,2).'%',
                    $this->formatGetal($benchmarkIndex,2).'%',
                    $this->formatGetal($verschil,2).'%');

    $this->pdf->Row($tmpArray);
    $laatsteJaar=$jaar;
  }
}
$this->pdf->CellBorders=array();
$YendIndex = $this->pdf->GetY();

$w=163;
$h=90;
$horDiv = 10;


//$this->pdf->setXY(120,40);
//$this->pdf->SetFont($this->pdf->rapport_font, 'B', 16);
// $this->pdf->Multicell($w,4,$this->portefeuille." versus benchmark",'','C');
  $this->pdf->setXY(125,60);

    $legendDatum= $data['Datum'];
    $bereikdata =   $data;

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );

    $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.3);



  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    if($this->pdf->rapport_layout == 8)
      $procentWhiteSpace = 20;
    else
      $procentWhiteSpace = 5;
    $maxVal = $maxVal * (1 + ($procentWhiteSpace/100));
    $minVal = $minVal * (1 - ($procentWhiteSpace/100));
    $legendYstep = ($maxVal - $minVal) / $horDiv;

     $verInterval = ($lDiag / $verDiv);
     $horInterval = ($hDiag / $horDiv);

     $waardeCorrectie = $hDiag / ($maxVal - $minVal);

     $unit = $lDiag / count($grafiekData['portefeuille']);
     $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
     $this->pdf->SetTextColor(0,0,0);

     $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag,'D','',array(245,245,245));
    $this->pdf->SetDrawColor(0,78,58);
   // $this->pdf->SetLineWidth(1);
   // $this->pdf->Rect($XDiag-15, $YDiag-15, $lDiag+50, $hDiag+40,'D','',array(50,150,50));
  //  $this->pdf->SetLineWidth(0.1);
     $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
     $unith = $hDiag / (-1 * $minVal + $maxVal);

  $top = $YPage;
  $bodem = $YDiag+$hDiag;
  $absUnit =abs($unith);

$nulpunt = $YDiag + (($maxVal-100) * $waardeCorrectie);
$n=0;

  for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
  {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(128,128,128)));
      if($n==0)
        $this->pdf->Text($XDiag-7, $i, round($n*$stapgrootte,1) ." %");
      else
        $this->pdf->Text($XDiag-7, $i, round(-1*$n*$stapgrootte,1) ." %");
      $n++;
      if($n >20)
       break;
  }
  $n=0;
  for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
  {
    $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(128,128,128)));
    if($skipNull == true)
      $skipNull = false;
    else
      $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte) ." %");

    $n++;
    if($n >20)
       break;
  }

$n=0;
$laatsteI = count($datumArray)-1;
$lijnenAantal = count($grafiekData);

$this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0));
//$this->pdf->Rect($this->pdf->marge+200, $YendIndex+6, 40, 6 * $lijnenAantal ,'FD','',array(240,240,240));
if($this->pdf->debug==1)
  $cubic=true;
else
  $cubic=false;
    foreach ($grafiekData as $fonds=>$data)
    {
      $oldData=$data;
      $data=array(100);
      foreach ($oldData as $value)
        $data[]=$value;

      $kleur = array($indexKleuren[$fonds]['R']['value'],$indexKleuren[$fonds]['G']['value'],$indexKleuren[$fonds]['B']['value']);
      $yval=$YDiag + (($maxVal-100) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $kleur);

      if($cubic==true)
      {
         $Index = 1;
         $XLast = -1;
         foreach ( $data as $Key => $Value )
         {
            $XIn[$Key] = $Index;
            $YIn[$Key] = $Value;
            $Index++;
         }

         $Index--;
//         $Index=count($data);
         $Yt[0] = 0;
         $Yt[1] = 0;
         $U[1]  = 0;
         for($i=1;$i<=$Index-1;$i++)
          {
           $Sig    = ($XIn[$i] - $XIn[$i-1]) / ($XIn[$i+1] - $XIn[$i-1]);
           $p      = $Sig * $Yt[$i-1] + 2;
           $Yt[$i] = ($Sig - 1) / $p;
           $U[$i]  = ($YIn[$i+1] - $YIn[$i]) / ($XIn[$i+1] - $XIn[$i]) - ($YIn[$i] - $YIn[$i-1]) / ($XIn[$i] - $XIn[$i-1]);
           $U[$i]  = (6 * $U[$i] / ($XIn[$i+1] - $XIn[$i-1]) - $Sig * $U[$i-1]) / $p;
          }
         $qn = 0;
         $un = 0;
         $Yt[$Index] = ($un - $qn * $U[$Index-1]) / ($qn * $Yt[$Index-1] + 1);

         for($k=$Index-1;$k>=1;$k--)
          $Yt[$k] = $Yt[$k] * $Yt[$k+1] + $U[$k];


          $Accuracy=0.1;
          for($X=1;$X<=$Index;$X=$X+$Accuracy)
          {
           $klo = 1;
           $khi = $Index;
           $k   = $khi - $klo;
           while($k > 1)
            {
             $k = $khi - $klo;
             If ( $XIn[$k] >= $X )
              $khi = $k;
             else
              $klo = $k;
            }
           $klo = $khi - 1;

           $h     = $XIn[$khi] - $XIn[$klo];
           $a     = ($XIn[$khi] - $X) / $h;
           $b     = ($X - $XIn[$klo]) / $h;
           $Value = $a * $YIn[$klo] + $b * $YIn[$khi] + (($a*$a*$a - $a) * $Yt[$klo] + ($b*$b*$b - $b) * $Yt[$khi]) * ($h*$h) / 6;

          // echo "$Value <br>\n";

           //$YPos = $this->GArea_Y2 - (($Value-$this->VMin) * $this->DivisionRatio);
           $YPos = $YDiag + (($maxVal-$Value) * $waardeCorrectie) ;
           $XPos = $XDiag+($X-1)*$unit;


           if($X==1)
           {
             $XLast=$XPos;
             $YLast=$YPos;
           }

           $this->pdf->Line($XLast,$YLast,$XPos,$YPos,$lineStyle);
           $XLast = $XPos;
           $YLast = $YPos;

          }


      }


     //  listarray($Yt);

      for ($i=0; $i<count($data); $i++)
      {
        if(!isset($datumPrinted[$i]) && $datumArray[$i] <> '')
        {
          $datumPrinted[$i] = 1;

          $kwartaal=ceil(substr($datumArray[$i],5,2)/3);
          $jaar=substr($datumArray[$i],0,4);
          $periode=$jaar.'Q'.$kwartaal;

          if(substr($datumArray[$i],5,5)=='12-31' )//|| $i == $laatsteI || $i==0
          {
            $this->pdf->line($XDiag+($i+1)*$unit,$YDiag+$hDiag,$XDiag+($i+1)*$unit,$YDiag+$hDiag+2,array('color'=>array(0,0,0),'width'=>0.1));
            $this->pdf->TextWithRotation($XDiag+($i+1)*$unit-8,$YDiag+$hDiag+12,date('d-m-Y',db2jul($datumArray[$i])),45);
          }
          if($lastjaar!=$jaar)
          {
          //  $this->pdf->TextWithRotation($XDiag+($i+1)*$unit-6,$YDiag+$hDiag+15,$jaar,0);
          }
          $laatstePeriode=$periode;
          $lastjaar=$jaar;
        }

        if($data[$i] != 0)
        {
          $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
          $xval=$XDiag+($i)*$unit;

          if($i==0)
           $XvalLast=$XDiag;
          if($cubic == false)
            $this->pdf->line($XvalLast, $yval, $xval, $yval2,$lineStyle );
          $yval = $yval2;
          $XvalLast=$xval;
        }

      }
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $fondsNaam = ($indexNaam[$fonds] <> "")?$indexNaam[$fonds].'*':$fonds;
      $this->pdf->Text($XPage+18-5+$n*40 , $YPage-4 ,$fondsNaam);
      $this->pdf->Rect($XPage+14-5+$n*40 , $YPage-6 , 2, 2 ,'F','',$kleur);
      $n++;
  


 }

 //$this->pdf->drawCubicCurve($grafiekData);
  
    //$this->pdf->Text($XPage , $YPage+$h+20 ,$benchmarkVoettekst);
    $this->pdf->setXY($XPage-5 , $YPage+$h+20 );
    $this->pdf->MultiCell($w+5,5,$benchmarkVoettekst,0,'L',0);
    
      $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
      $this->pdf->SetFillColor(0,0,0);
      $this->pdf->CellBorders = array();
	}

//listarray($indexWaarden);
//listarray($tmp);
}
?>
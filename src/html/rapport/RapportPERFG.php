<?
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/03/21 16:32:17 $
 		File Versie					: $Revision: 1.16 $

 		$Log: RapportPERFG.php,v $
 		Revision 1.16  2020/03/21 16:32:17  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2017/05/06 17:28:05  rvv
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


class RapportPERFG
{

  function RapportPERFG($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();

$this->pdf->AddPage();
$this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0));


$DB = new DB();
$query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE periode='m' AND Portefeuille = '".$this->portefeuille."' AND Categorie = 'Totaal' ORDER BY Datum ASC LIMIT 1 ";
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
}

$query = "SELECT Indices.Beursindex ,Indices.grafiekKleur
          FROM Indices
          WHERE Indices.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'  ORDER BY Indices.Afdrukvolgorde  ";
$DB->SQL($query);
$DB->Query();
$indexKleuren=array();
while ($data = $DB->nextRecord())
{
	$indexFondsen[] = $data['Beursindex'];
	$indexKleuren[$data['Beursindex']] = unserialize($data['grafiekKleur']);
}

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

if(isset($this->pdf->rapport_PERFG_portefeuilleKleur) && is_array($this->pdf->rapport_PERFG_portefeuilleKleur))
    $indexKleuren['portefeuille']=array('R'=>array('value'=>$this->pdf->rapport_PERFG_portefeuilleKleur[0]),'G'=>array('value'=>$this->pdf->rapport_PERFG_portefeuilleKleur[1]),'B'=>array('value'=>$this->pdf->rapport_PERFG_portefeuilleKleur[2]));
    //listarray($indexKleuren);exit;
//listarray($indexKleuren);exit;
//listarray($indexFondsen);

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

    $jaar=substr($eind,0,4);

   	if(empty($indexTabel['cumulatief'][$fonds]['jaren']))
   	  $indexTabel['cumulatief'][$fonds]['jaren']=100;

   	if(empty($indexTabel['cumulatief'][$fonds]['cumulatief']))
   	   $indexTabel['cumulatief'][$fonds]['cumulatief']=100;

    $indexTabel['cumulatief'][$fonds]['jaren']      = ($indexTabel['cumulatief'][$fonds]['jaren']*($perf*100))/100;
    $indexTabel['cumulatief'][$fonds]['cumulatief'] = ($indexTabel['cumulatief'][$fonds]['cumulatief']*($perf*100))/100;
    $indexTabel[$jaar][$fonds]['jaar'] = $indexTabel['cumulatief'][$fonds]['jaren'];

    if(substr($eind,5,5) == '12-31' || $aantalWaarden == $id)
    {
      $indexTabel['cumulatief'][$fonds]['jaren'] = 100;
      $indexTabel[$jaar][$fonds]['cumulatief'] = $indexTabel['cumulatief'][$fonds]['cumulatief'];
    }
  }
}
//listarray($indexTabel);exit;


$n=0;
$minVal = 99;
$maxVal = 101;
foreach ($indexWaarden as $id=>$data)
{
  $grafiekData['portefeuille'][$n]=$data['index'];
  $datumArray[$n] = $data['datum'];
  $jaar=substr($data['datum'],0,4);

  if(empty($indexTabel['cumulatief']['portefeuille']['jaren']))
    $indexTabel['cumulatief']['portefeuille']['jaren']=100;
  $indexTabel['cumulatief']['portefeuille']['jaren'] =($indexTabel['cumulatief']['portefeuille']['jaren']*(100+$data['performance'])/100);
  $indexTabel[$jaar]['portefeuille']['jaar'] = $indexTabel['cumulatief']['portefeuille']['jaren'];
  if(substr($data['datum'],5,5) == '12-31' || $aantalWaarden == $id)
  {
    $indexTabel['cumulatief']['portefeuille']['jaren'] = 100;
    $indexTabel[$jaar]['portefeuille']['cumulatief'] = $data['index'];
  }

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
  $n++;
}




$indexTabelFondsen = array('portefeuille',$this->portefeuilledata['SpecifiekeIndex']);

$tmpArray0 = array('','');
$tmpArray1 = array('','Jaar');

foreach ($indexTabelFondsen as $fonds)
{

  array_push($tmpArray0,($indexNaam[$fonds] <> ""?"benchmark":$fonds));
  array_push($tmpArray1,"per jaar");
  array_push($tmpArray1,"cumu.");
}
$this->pdf->setY(35);
$this->pdf->Row(array(''));
$this->pdf->SetFont($this->pdf->rapport_font, 'B', 8);
$this->pdf->setAligns(array('L','L','C','C'));
$this->pdf->CellBorders = array('',array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'));
$this->pdf->setWidths(array(200,15,15+15,15+15));
$this->pdf->Row($tmpArray0);
$this->pdf->CellBorders = array('',array('U','L','R'),'U',array('U','R'),'U',array('U','R'));
$this->pdf->setWidths(array(200,15,15,15,15,15));
$this->pdf->setAligns(array('L','L','R','R','R','R'));
$this->pdf->SetFont($this->pdf->rapport_font, '', 8);
$this->pdf->Row($tmpArray1);

//listarray($indexTabel);
foreach ($indexTabel as $datum=>$fondsen)
{
  if(is_numeric($datum))
  {
    $tmpArray = array('');
    array_push($tmpArray,$datum);

    foreach ($indexTabelFondsen as $fonds)
    {
      $waarden = $indexTabel[$datum][$fonds];
   //  echo $fonds." "; listarray($waarden);
      if(in_array($fonds,$indexTabelFondsen))
      {
        if(!empty($waarden['jaar']))
          array_push($tmpArray,$this->formatGetal(($waarden['jaar']-100),1)."%");
        else
          array_push($tmpArray,"0,0%");

        if(!empty($waarden['cumulatief']))
          array_push($tmpArray,$this->formatGetal(($waarden['cumulatief']-100),1)."%");
        elseif(!empty($indexTabel['cumulatief'][$fonds]['cumulatief']))
          array_push($tmpArray,$this->formatGetal(($indexTabel['cumulatief'][$fonds]['cumulatief']-100),1)."%");
        else
          array_push($tmpArray,"");

      }
    }
    $this->pdf->Row($tmpArray);
  }
}
$this->pdf->CellBorders=array();

$YendIndex = $this->pdf->GetY();

$w=150;
$h=100;
$horDiv = 10;


//$this->pdf->setXY(15,30);
 //$this->pdf->SetFont($this->pdf->rapport_font, 'B', 16);
 // $this->pdf->Multicell($w,4,"Index Grafiek",'','C');
  $this->pdf->setXY(35,40);

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
     $this->pdf->SetDrawColor(0,0,0);
     $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag,'FD','',array(245,245,245));
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
      $this->pdf->Text($XDiag-7, $i, 100-($n*$stapgrootte) ." %");
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
      $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+100 ." %");

    $n++;
    if($n >20)
       break;
  }

$n=0;
$laatsteI = count($datumArray)-1;
$lijnenAantal = count($grafiekData);

$this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0));
$this->pdf->Rect($this->pdf->marge+200, $YendIndex+6, 40, 6 * $lijnenAantal ,'FD','',array(240,240,240));
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
        if(!isset($datumPrinted[$i]))
        {
          $datumPrinted[$i] = 1;
          if(substr($datumArray[$i],5,5)=='12-31' || $i == $laatsteI || $i==0)
          {
            $this->pdf->TextWithRotation($XDiag+($i+1)*$unit-6,$YDiag+$hDiag+10,date("M-Y",db2jul($datumArray[$i])),45);
          }
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

      $fondsNaam = ($indexNaam[$fonds] <> "")?$indexNaam[$fonds]:$fonds;
      $this->pdf->Text($XPage+$w+28 , $YendIndex+10 +$n*6,$fondsNaam);
      $this->pdf->Rect($XPage+$w+25 , $YendIndex+9 +$n*6, 1, 1 ,'F','',$kleur);
      $n++;

 }

 //$this->pdf->drawCubicCurve($grafiekData);


if($this->pdf->rapport_layout == 8)
{
 // $this->pdf->setY($YDiag + 6 * $lijnenAantal +55);
  $this->pdf->setY(124);
  $samenstelling['OFFENSIEF'] = array('AAND'=>90,'OBL'=>10);
  $samenstelling['NEUOFF'] = array('AAND'=>77.5,'OBL'=>22.5);
  $samenstelling['NEUTRAAL'] = array('AAND'=>65,'OBL'=>35);
  $samenstelling['DEFNEU'] = array('AAND'=>52.5,'OBL'=>47.5);
  $samenstelling['DEFENSIEF'] = array('AAND'=>40,'OBL'=>60);



  $this->pdf->Row(array(''));
  $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.1));
  $this->pdf->SetFont($this->pdf->rapport_font, 'B', 8);
  $this->pdf->setAligns(array('L','L','C','C'));
  $this->pdf->CellBorders = array('',array('U','T','L','R'));
  $this->pdf->setWidths(array(200,60));

  $this->pdf->Row(array('','Samenstelling benchmark'));

  $this->pdf->CellBorders = array('',array('U','L','R'),array('U','R'));
  $this->pdf->setWidths(array(200,30,30));
  $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
  $this->pdf->Row(array('','Wereld aandelen',$samenstelling[$this->portefeuilledata['Risicoklasse']]['AAND']."%"));
  $this->pdf->Row(array('','Europese obligaties',$samenstelling[$this->portefeuilledata['Risicoklasse']]['OBL']."%"));

 // $this->portefeuilledata[Risicoklasse];

}




      $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
      $this->pdf->SetFillColor(0,0,0);
      $this->pdf->CellBorders = array();
	}

//listarray($indexWaarden);
//listarray($tmp);
}
?>
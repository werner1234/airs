<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/11/10 18:20:33 $
File Versie					: $Revision: 1.8 $

$Log: RapportPERFG_L78.php,v $
Revision 1.8  2018/11/10 18:20:33  rvv
*** empty log message ***

Revision 1.7  2018/10/20 18:05:20  rvv
*** empty log message ***

Revision 1.6  2018/10/14 12:36:15  rvv
*** empty log message ***

Revision 1.5  2018/10/14 11:12:15  rvv
*** empty log message ***

Revision 1.4  2018/10/14 10:08:32  rvv
*** empty log message ***

Revision 1.3  2018/10/13 17:18:13  rvv
*** empty log message ***

Revision 1.2  2018/06/17 07:31:10  rvv
*** empty log message ***

Revision 1.1  2018/06/16 17:42:56  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");


class RapportPERFG_L78
{
	function RapportPERFG_L78($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));
    
    $this->pdf->rapport_titel = "Historische performanceverloop";
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


	 	//Kleuren instellen
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);
    $this->categorieKleuren=$allekleuren['OIB'];

		// voor data
		$this->pdf->widthA = array(1,95,25,5,25,5,25,5,25,5,25,5,25,5,25,5);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R');


  	$this->pdf->widthB = array(1,95,30,10,30,115);
		$this->pdf->alignB = array('L','L','R','R','R');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->AddPage();
    $this->pdf->templateVars['PERFGPaginas']=$this->pdf->page;


$DB = new DB();
$query = "SELECT id, min(Datum) as datum FROM HistorischePortefeuilleIndex WHERE periode='j' AND Portefeuille = '".$this->portefeuille."' AND Categorie = 'Totaal' ORDER BY Datum ASC LIMIT 1 ";
$DB->SQL($query);
$DB->Query();
$datum = $DB->nextRecord();
if($datum['datum']=='')
  $startJaren=$this->pdf->PortefeuilleStartdatum;
else
  $startJaren=substr($datum['datum'],0,4).'-01-01';

    $beginDatum=$startJaren;//substr($this->pdf->PortefeuilleStartdatum,0,10);
    $index = new indexHerberekening();
    $indexWaarden=array();
    if(db2jul($beginDatum)<db2jul('2017-12-31'))
    {
      if(db2jul($this->rapportageDatum) < db2jul('2017-12-31'))
        $eindDatum=$this->rapportageDatum;
      else
        $eindDatum='2017-12-31';
      $indexWaarden = $index->getWaarden($beginDatum,$eindDatum,$this->portefeuille,$this->pdf->portefeuilledata['SpecifiekeIndex'],'jaar',$this->pdf->rapportageValuta);
    }
    //listarray($indexWaarden);
    
    if (db2jul($this->rapportageDatum) > db2jul('2018-01-01'))
    {
      if(db2jul($beginDatum)<db2jul('2018-01-01'))
      {
        $beginDatum = '2018-01-01';
      }
      $indexWaardenMaanden = $index->getWaarden($beginDatum, $this->rapportageDatum, $this->portefeuille, $this->pdf->portefeuilledata['SpecifiekeIndex'], 'maanden', $this->pdf->rapportageValuta);
      foreach($indexWaardenMaanden as $periodeData)
        $indexWaarden[]=$periodeData;
    }
    
    $jaarWaarden=array();
    $jaarWaardenGrafiek=array();
    $huidigeJaarGrafiek=array();
    $huidigeJaarJul=db2jul(substr($this->rapportageDatum,0,4).'-01-01');
    $laatsteDatum='';
    
    
    $totaal=array();
    $somVars=array('resultaatVerslagperiode','opbrengsten','stortingen','onttrekkingen','kosten','gerealiseerd','ongerealiseerd','waardeMutatie','rente');
    foreach($indexWaarden as $maandwaarden)
    {
      $jaar=substr($maandwaarden['datum'],0,4);
      if(!isset($jaarWaarden[$jaar]['waardeBegin']))
      {
        $jaarWaarden[$jaar]['waardeBegin'] = $maandwaarden['waardeBegin'];
        $jaarWaarden[$jaar]['beginDatum'] = substr($maandwaarden['periode'],0,10);
      }
  
      if(!isset($totaal['waardeBegin']))
      {
        $totaal['waardeBegin'] = $maandwaarden['waardeBegin'];
        $totaal['beginDatum'] = substr($maandwaarden['periode'], 0, 10);
      }
      
      $jaarWaarden[$jaar]['waardeHuidige']=$maandwaarden['waardeHuidige'];
     
      $totaal['waardeHuidige']=$maandwaarden['waardeHuidige'];
      //$totaal['index']=$maandwaarden['index']-100;
      foreach($somVars as $var)
      {
        $jaarWaarden[$jaar][$var] += $maandwaarden[$var];
        $totaal[$var] += $maandwaarden[$var];
      }
      $jaarWaarden[$jaar]['performance']=((1+$jaarWaarden[$jaar]['performance']/100) * (1+$maandwaarden['performance']/100)-1) * 100;
      $jaarWaarden[$jaar]['benchmark']=((1+$jaarWaarden[$jaar]['benchmark']/100) * (1+$maandwaarden['specifiekeIndexPerformance']/100)-1) * 100;
      $totaal['index']=((1+$totaal['index']/100) * (1+$maandwaarden['performance']/100)-1) * 100;
      $jaarWaarden[$jaar]['index']=$totaal['index'];
      
      $totaal['benchmark']=((1+$totaal['benchmark']/100) * (1+$maandwaarden['specifiekeIndexPerformance']/100)-1) * 100;
      
      if(db2jul($maandwaarden['datum']) >= $huidigeJaarJul)
      {
        $huidigeJaarGrafiek[$maandwaarden['datum']]['performance']=$maandwaarden['performance'];
        $huidigeJaarGrafiek[$maandwaarden['datum']]['performanceCumu']=((1+$huidigeJaarGrafiek[$laatsteDatum]['performanceCumu']/100) * (1+$maandwaarden['performance']/100)-1) * 100;
        $huidigeJaarGrafiek[$maandwaarden['datum']]['benchmark']=$maandwaarden['specifiekeIndexPerformance'];
        $huidigeJaarGrafiek[$maandwaarden['datum']]['benchmarkCumu']=((1+$huidigeJaarGrafiek[$laatsteDatum]['benchmarkCumu']/100) * (1+$maandwaarden['specifiekeIndexPerformance']/100)-1) * 100;
        $laatsteDatum=$maandwaarden['datum'];
      }
      $jaarWaardenGrafiek[$jaar]['performance']=$jaarWaarden[$jaar]['performance'];
      $totaal['performance']=$jaarWaarden[$jaar]['performance'];
    }

    //rvv
    
foreach ($jaarWaarden as $jaar=>$data)
{
  if($data['datum'] != '0000-00-00')
  {
    $rendamentWaarden[] = $data;
    $grafiekData['Datum'][] = $jaar;
    $grafiekData['Index'][] = $data['index'];
    if($this->pdf->portefeuilledata['SpecifiekeIndex']<>'')
      $grafiekData['benchmarkIndex'][] = $data['specifiekeIndex']-100;

  }
}
  //  listarray($grafiekData);

  if(count($jaarWaarden) > 0)
   {
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->underlinePercentage=0.8;
      //$this->pdf->SetFillColor(137,188,255);
      $this->pdf->SetFillColor($this->pdf->rapport_background_fill[0],$this->pdf->rapport_background_fill[1],$this->pdf->rapport_background_fill[2]);

      foreach ($jaarWaarden as $jaar=>$row)
      {
        if($jaar%2==0)
          $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
        else
          $this->pdf->fillCell=array();
      
        $this->printTotaal($row, $jaar);
      }
      //$this->pdf->CellBorders = array('','TS','TS','TS','TS','TS','TS','TS','','TS');
      $this->pdf->fillCell=array();
      $this->pdf->row(array('','','','','','','','','','',''));
      $this->pdf->SetY($this->pdf->GetY()-4);
      $this->pdf->ln(3);
        
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->CellBorders = array();
      $this->printTotaal($totaal,'');
		  $this->pdf->CellBorders = array();
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

	 }

   if (count($huidigeJaarGrafiek) > 1)
   {
       $yShift=-3;
       $this->pdf->SetXY(8,111+$yShift);//104
       $RapStopJaar = date("Y", db2jul($this->rapportageDatum));
       $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  		  $this->pdf->Cell(0, 5, vertaalTekst('Rendement',$this->pdf->rapport_taal).' '.$RapStopJaar.' ('.
                               vertaalTekst('cumulatief',$this->pdf->rapport_taal).' '.
                               vertaalTekst('in',$this->pdf->rapport_taal).' %)', 0, 1);
  		  $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
  		  $this->pdf->SetXY(15,117+$yShift)		;//112
        $valX = $this->pdf->GetX();
        $valY = $this->pdf->GetY();
        //function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
        $kleuren=array(array(74,166,77),array(61,59,56));
        $this->LineDiagram(270, 60, $huidigeJaarGrafiek,$kleuren,0,0,6,5,1);//50
        $this->pdf->SetXY($valX, $valY + 75+$yShift);
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        foreach($kleuren as $index=>$kleur)
        {
          if($index==0)
          {
            $this->pdf->rect($this->pdf->getX()-2,$this->pdf->getY()+1,2,2,'F','',$kleur);
            $this->pdf->Cell(50, 4, 'Portefeuille', 0, 0, "L");
          }
          if($index==1 && $this->pdf->portefeuilledata['SpecifiekeIndex']<>'')
          {
            $this->pdf->rect($this->pdf->getX()-2,$this->pdf->getY()+1,2,2,'F','',$kleur);
            $this->pdf->Cell(50, 4, $this->pdf->portefeuilledata['SpecifiekeIndex'], 0, 0, "L");
          }
        }

		  }

	   $this->pdf->fillCell = array();

	}


  function printTotaal($totaal,$jaar)
{
     	    $this->pdf->row(array(vertaalTekst('Totaal '.$jaar,$this->pdf->rapport_taal),
		                           $this->formatGetal($totaal['waardeBegin'],2),
		                           $this->formatGetal($totaal['stortingen']-$totaal['onttrekkingen'],2),
		                           $this->formatGetal($totaal['gerealiseerd']+$totaal['ongerealiseerd'],2),
		                           $this->formatGetal($totaal['opbrengsten']+$totaal['rente'],2),
		                           $this->formatGetal($totaal['kosten'],2),
		                           $this->formatGetal($totaal['resultaatVerslagperiode'],2),
		                           $this->formatGetal($totaal['waardeHuidige'],2),
		                           $this->formatGetal($totaal['performance'],2),
                               $this->formatGetal($totaal['index'],2)
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




function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;

    //listarray($data);
    $legendDatum=array();
    $data1=array();
    $data0=array();
    foreach($data as $datum=>$maandData)
    {
      $legendDatum[]= $datum;
      if($maandData['benchmarkCumu'] <> -100)
        $data1[] = $maandData['benchmarkCumu'];
      $data0[] = $maandData['performanceCumu'];
    }
    $data=$data0;

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

    $this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);
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

    if($jaar && count($data) < 12)
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
    $lineStyle = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    //listarray($data);
   // $color=array(200,0,0);
   
   
    for ($i=0; $i<count($data); $i++)
    {
      $extrax=($unit*0.1*-1);
      if($i <> 0)
        $extrax1=($unit*0.1*-1);
        
        
      $this->pdf->TextWithRotation($XDiag+($i)*$unit-10+$unit,$YDiag+$hDiag+8,date('d-m-Y',db2jul($legendDatum[$i])),25);

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


}
?>
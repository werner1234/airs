<?
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/04/23 15:33:07 $
 		File Versie					: $Revision: 1.1 $

 		$Log: RapportPERFG_L69.php,v $
 		Revision 1.1  2016/04/23 15:33:07  rvv
 		*** empty log message ***
 		
 
*/

include_once('../indexBerekening.php');


class RapportPERFG_L69
{

  function RapportPERFG_L69($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  global $__appvar;
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    
    $this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->pdf->underlinePercentage=0.8;
    
    $DB = new DB();
	  $query =  "SELECT Portefeuilles.Vermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Portefeuille, Portefeuilles.Startdatum, ".
		" Portefeuilles.Einddatum, Portefeuilles.Client, Portefeuilles.Depotbank, Portefeuilles.RapportageValuta, Vermogensbeheerders.attributieInPerformance, ".
		" Clienten.Naam, Portefeuilles.ClientVermogensbeheerder FROM (Portefeuilles, Clienten ,Vermogensbeheerders)  WHERE ".
		" Portefeuilles.Client = Clienten.Client AND Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder".
		" AND Portefeuilles.Portefeuille = '$this->portefeuille' ";
		$DB->SQL($query);
		$this->pdata = $DB->lookupRecord();

    $startJul=db2jul($this->pdata['Startdatum']);
		$this->pdf->rapport_titel = "Ontwikkeling vermogen en beleggingsresultaat vanaf ".date("d",$startJul)." ".vertaalTekst($__appvar["Maanden"][date("n",$startJul)],$this->pdf->rapport_taal)." ".date("Y",$startJul);



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

    	//Kleuren instellen
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);
    $this->attKleuren=$allekleuren['ATT'];


    $DB = new DB();
    $query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE Portefeuille = '".$this->portefeuille."' AND Categorie = 'Totaal' ORDER BY Datum ASC LIMIT 1 ";
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
  $start = $this->PortefeuilleStartdatum;

$eind = $this->rapportageDatum;
$datumStart = db2jul($start);
$datumStop  = db2jul($eind);



	  $this->berekening = new rapportATTberekening($this->pdata);
	  $categorien=$this->berekening->getAttributieCategorien();
    $this->berekening->pdata['pdf']=true;
    $this->berekening->attributiePerformance($this->portefeuille,$start,$this->rapportageDatum,'rapportagePeriode',$this->pdf->rapportageValuta);
    $this->tmp['rapportagePeriode']=$this->berekening->performance['rapportagePeriode'];
    unset($this->berekening->performance['rapportagePeriode']);



foreach ($this->berekening->performance as $periode=>$data)
{
  $datum=substr($periode,11,10);
  foreach ($categorien as $categorie)
  {
    $this->waarden[$categorie][$datum]['waarde']=$data['totaalWaarde'][$categorie]['eind'];
    $this->waarden[$categorie][$datum]['perf']=$data['totaal']['performance'][$categorie];
    $this->waarden[$categorie][$datum]['stortingen']=$data['totaal']['stortingen'][$categorie]-$data['totaal']['onttrekkingen'][$categorie];
    $this->waarden[$categorie][$datum]['resultaat']=($data['totaalWaarde'][$categorie]['eind']-$data['totaalWaarde'][$categorie]['begin'])-$this->waarden[$categorie][$datum]['stortingen'];
  }

}

$huidigeJaar=date('Y');
foreach ($this->waarden as $categorie=>$datumData)
{
  $laatstePerf=0;
  $laatstePerfCumu=0;
  foreach ($datumData as $datum=>$waarden)
  {
    $jaar=substr($datum,0,4);
    if($jaar<>$laatsteJaar)
    {
      $laatstePerf=0;
      $this->jaarWaarden[$categorie][$jaar]['stortingenCumu']=$this->jaarWaarden[$categorie][$laatsteJaar]['stortingenCumu'];
      $this->jaarWaarden[$categorie][$jaar]['resultaatCumu']=$this->jaarWaarden[$categorie][$laatsteJaar]['resultaatCumu'];
    }

    $this->jaarWaarden[$categorie][$jaar]['waarde']=$waarden['waarde'];
    $this->jaarWaarden[$categorie][$jaar]['stortingen']+=$waarden['stortingen'];
    $this->jaarWaarden[$categorie][$jaar]['resultaat']+=$waarden['resultaat'];
    $this->jaarWaarden[$categorie][$jaar]['perf']=((1+$waarden['perf']/100)*(1+$laatstePerf/100)-1)*100;
    $this->jaarWaarden[$categorie][$jaar]['perfCumu']=((1+$waarden['perf']/100)*(1+$laatstePerfCumu/100)-1)*100;
    $this->jaarWaarden[$categorie][$jaar]['stortingenCumu']+=$waarden['stortingen'];
    $this->jaarWaarden[$categorie][$jaar]['resultaatCumu']+=$waarden['resultaat'];
    $this->jaarWaarden[$categorie][$jaar]['datum']=$datum;


    $laatstePerf=$this->jaarWaarden[$categorie][$jaar]['perf'];
    $laatstePerfCumu=$this->jaarWaarden[$categorie][$jaar]['perfCumu'];
    $laatsteJaar=$jaar;
  }
}

$this->pdf->CellBorders=array();
$this->pdf->setY(34);
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
$this->pdf->setWidths(array(120));
$this->pdf->setAligns(array('C'));
$this->pdf->Row(array("Ontwikkeling totale vermogen"));
$this->pdf->ln(8);
//$this->pdf->CellBorders = array(array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'));
$this->pdf->setWidths(array(20,25,28,25,27));
$this->pdf->setAligns(array('L','R','R','R','R','R'));
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
$this->pdf->Row(array('Jaar', 'Totaal vermogen', 'Resultaat', '%', 'Stortingen en onttrekkingen'));
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
foreach($this->jaarWaarden['Totaal'] as $jaar=>$waarden)
{
   $this->pdf->Row(array($jaar,
   $this->formatGetal($waarden['waarde'],0),
   $this->formatGetal($waarden['resultaat'],0),
   $this->formatGetal($waarden['perf'],2),
   $this->formatGetal($waarden['stortingen'],0)));

   $indexWaardenGrafiek[]=array('jaar'=>$jaar,'waarde'=>$waarden['waarde'],'perfCumu'=>$waarden['perfCumu'],'datum'=>$waarden['datum']);
}

$cumulatief=$this->jaarWaarden['Totaal'][$laatsteJaar];
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
$this->pdf->CellBorders = array('','',array('TS'),'',array('TS'));
$this->pdf->Row(array('','','','','',));
$this->pdf->CellBorders = array('','',array('UU'),array('UU'),array('UU',));
$this->pdf->Row(array('Cumulatief','',$this->formatGetal($cumulatief['resultaatCumu'],0),$this->formatGetal($cumulatief['perfCumu'],2),$this->formatGetal($cumulatief['stortingenCumu'],0)));

$this->pdf->CellBorders = array();
$this->pdf->setY(34);
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
$this->pdf->setWidths(array(140,152));
$this->pdf->setAligns(array('C','C'));
$this->pdf->Row(array('',"Ontwikkeling vermogen per beleggingscategorie"));
$this->pdf->ln(8);
$this->pdf->setWidths(array(140,20,21,12,21,12,21,12,21,12));
$this->pdf->setAligns(array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
$header=array('',"Jaar\n ");
$headerCat=$categorien;
unset($headerCat[0]);
foreach ($headerCat as $categorie)
{
 array_push($header,$this->berekening->categorieOmschrijving[$categorie]);
 if($categorie!='Liquiditeiten')
   array_push($header,'%');
}
$this->pdf->Row($header);
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
$regel=array();

$col=0;
foreach ($headerCat as $categorie)
{
  foreach($this->jaarWaarden[$categorie] as $jaar=>$waarden)
    $tmp[$jaar][$categorie]=array('waarde'=>$waarden['waarde'],'perf'=>$waarden['perf'],'perfCumu'=>$waarden['perfCumu']);
}
$this->pdf->CellBorders = array();
foreach ($tmp as $jaar=>$data)
{
  $row=array('',$jaar);
  foreach ($headerCat as $categorie)
  {
    $waarden=$data[$categorie];
    array_push($row,$this->formatGetal($waarden['waarde'],0));
    if($categorie!='Liquiditeiten')
      array_push($row,$this->formatGetal($waarden['perf'],2));

      $barData[$jaar][$categorie]=($waarden['waarde']/$this->jaarWaarden['Totaal'][$jaar]['waarde']*100);
  }
  $this->pdf->Row($row);
}

$this->pdf->CellBorders = array('','','',array('UU'),'',array('UU'),'',array('UU'),'',array('UU'));
$row=array('','Cumulatief');
foreach ($headerCat as $categorie)
{
  $cumulatief=$this->jaarWaarden[$categorie][$laatsteJaar];
  array_push($row,'');
  if($categorie!='Liquiditeiten')
    array_push($row,$this->formatGetal($cumulatief['perfCumu'],2));
}
$this->pdf->ln();
$this->pdf->Row($row);

$this->pdf->CellBorders = array();





//$this->perfG(20,110,100,70,'Totaal resultaat',$indexWaardenGrafiek);

//listarray($indexWaardenGrafiek);



$this->LineDiagram(18,120,110,50,$indexWaardenGrafiek,'Totaal resultaat');


$this->VBarDiagram(160,170,123,50,$barData,'Ontwikkeling vermogen');


}


function LineDiagram($x,$y,$w, $h, $data, $title,$color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;
    
    $this->pdf->Rect($x-10,$y-5,$w+15,$h+30);
    $this->pdf->setXY($x,$y);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Multicell($w,4,$title,'','C');
    $this->pdf->setXY($x,$y+8);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    

    foreach($data as $index=>$waarden)
    {
      $legendDatum[]=$waarden['jaar'];
      $newData[]= $waarden['perfCumu'];
    }
    $data=$newData;
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
   
   
    for ($i=0; $i<count($data); $i++)
    {
      $extrax=($unit*0.1*-1);
      if($i <> 0)
        $extrax1=($unit*0.1*-1);
        
        
      $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$i],0);

      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit+$extrax1, $yval, $XDiag+($i+1)*$unit+$extrax, $yval2,$lineStyle );
      $this->pdf->Rect($XDiag+($i+1)*$unit-0.5+$extrax, $yval2-0.5, 1, 1 ,'F','',$color);
      
      if($data[$i] <> 0)
        $this->pdf->Text($XDiag+($i+1)*$unit+$extrax,$yval2-2.5,$this->formatGetal($data[$i],1));
     
      
      $yval = $yval2;
    }
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }


  function VBarDiagram($x,$y,$w, $h, $data,$title='')
  {
      global $__appvar;
    //  $this->pdf->SetXY($x,$y)		;//112
$legendaWidth = 0;

$this->pdf->Rect($x-12,$y-$h-5,$w+17, $h+30);

    $this->pdf->setXY($x,$y-$h);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Multicell($w,4,$title,'','C');
    $this->pdf->setXY($x,$y+8);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);



      $grafiekPunt = array();
      $verwijder=array();
$this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0,'width'=>0.01));

      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = $datum;
        $n=0;
        foreach ($waarden as $categorie=>$waarde)
        {
          if($categorie=='LIQ')
            $categorie='Liquiditeiten';
          $grafiek[$datum][$categorie]=$waarde;
          $grafiekCategorie[$categorie][$datum]=$waarde;
          $categorien[$categorie] = $n;
          $categorieId[$n]=$categorie ;

          if($waarde < 0)
          {
            $verwijder[$datum]=$datum;
            $grafiek[$datum][$categorie]=0;
            $grafiekCategorie[$categorie][$datum]=0;
          }


          if(!isset($colors[$categorie]))
          {
            if($this->attKleuren[$categorie])
              $colors[$categorie]=array($this->attKleuren[$categorie]['R']['value'],$this->attKleuren[$categorie]['G']['value'],$this->attKleuren[$categorie]['B']['value']);
            else
              $colors[$categorie]=array(rand(20,80),rand(20,80),rand(20,250));//array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          }
          $n++;
        }
      }

      foreach ($verwijder as $datum)
      {
        foreach ($data[$datum] as $categorie=>$waarde)
        {
          if($categorie=='LIQ')
            $categorie='Liquiditeiten';
          $grafiek[$datum][$categorie]=0;
          $grafiekCategorie[$categorie][$datum]=0;
        }
      }

      $numBars = count($grafiek);
     // $numBars=12;

      if($color == null)
      {
        $color=array(155,155,155);
      }
      
      $maxVal=100;
      foreach ($this->jaarWaarden['Totaal'] as $jaar=>$waarden)
        $maxVal=max($maxVal,$waarden['waarde']);
        
        $maxVal=round(ceil($maxVal/5000))*5000;
        
     // listarray();
     
     
      
      $minVal = 0;

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

      $n=0;
      foreach (($this->categorieVolgorde) as $categorie)
      {
        if(is_array($grafiekCategorie[$categorie]))
        {
          $this->pdf->Rect($XstartGrafiek+$bGrafiek+3 , $YstartGrafiek-$hGrafiek+$n*10+2, 2, 2, 'DF',null,$colors[$categorie]);
          $this->pdf->SetXY($XstartGrafiek+$bGrafiek+6 ,$YstartGrafiek-$hGrafiek+$n*10+1.5 );
          $this->pdf->Cell(20, 3,$this->categorieOmschrijving[$categorie],0,0,'L');
          $n++;
        }
      }

      $unit = $hGrafiek / $maxVal * -1;
      $nulYpos =0;

      $horDiv = 5;
      $horInterval = $hGrafiek / $horDiv;
      $bereik = $hGrafiek/$unit;

      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      $this->pdf->SetTextColor(0,0,0);

      $stapgrootte = (abs($bereik)/$horDiv);
      $top = $YstartGrafiek-$h;
      $bodem = $YstartGrafiek;
      $absUnit =abs($unit);

      $nulpunt = $YstartGrafiek + $nulYpos;

      $n=0;

      for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek+$bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte)."",0,0,'R');
        $n++;
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
      $data=array_reverse($data,true);
      foreach($data as $categorie=>$val)
      {
        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
          $hval = ($val * $unit * $this->jaarWaarden['Totaal'][$datum]['waarde']/100);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,0,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);

         if($legendaPrinted[$datum] != 1)
           $this->pdf->TextWithRotation($xval+1,$YstartGrafiek+4,$legenda[$datum],0);

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
   $xval=$x+10;
   $yval=$y+15;
   $colors=array_reverse($colors,true);
   foreach ($colors as $cat=>$color)
   {
     $this->pdf->Rect($xval, $yval, 5, 5, 'DF',null,$colors[$cat]);
     $this->pdf->TextWithRotation($xval+7,$yval+2.5,$cat,0);
     $xval=$xval+22;
   }

    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
}
?>
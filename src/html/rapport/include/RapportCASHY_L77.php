<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/02/20 05:56:30 $
 		File Versie					: $Revision: 1.7 $

 		$Log: RapportCASHY_L77.php,v $
 		Revision 1.7  2020/02/20 05:56:30  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2020/02/12 16:40:35  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/11/24 19:11:26  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2018/10/20 18:05:20  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2018/10/07 10:19:56  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2018/10/06 17:20:57  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2018/05/20 10:39:24  rvv
 		*** empty log message ***
 		
 	

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");

//ini_set('max_execution_time',60);
class RapportCASHY_L77
{
	function RapportCASHY_L77($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "CASHY";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Cashflow overzicht";
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
		$this->pdf->AddPage();
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

	  $this->pdf->SetWidths(array(10,25,25,25,40,20,20));
		$this->pdf->SetAligns(array('L','L','R','R','R','R','R'));

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $cashflowJaar=array();
		$cashflowTotaal=0;
	  $cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		$cashfow->genereerTransacties();
		$regels = $cashfow->genereerRows();
    $maanden=array(0,'jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
    $cashflowHuidigjaar=array();
		for($i=1;$i<13;$i++)
		{
      $maand=$maanden[$i];
		  $cashflowHuidigjaar[$maand]['lossing'] +=0;
		  $cashflowHuidigjaar[$maand]['rente'] +=0;
      $cashflowHuidigjaar[$maand]['renob'] +=0;
		}
    
    $DB=new DB();
		$query="SELECT
sum(Rekeningmutaties.Valutakoers * (Rekeningmutaties.Credit-Rekeningmutaties.Debet)) as bedrag,
Rekeningen.Portefeuille,
Rekeningmutaties.Grootboekrekening,
MONTH(Rekeningmutaties.Boekdatum) as maand
FROM
Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND
Rekeningmutaties.Boekdatum > '".substr($this->rapportageDatum,0,4)."-01-01' AND
Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND
Rekeningmutaties.Grootboekrekening IN('RENOB','HUUR')
GROUP BY Grootboekrekening,maand";
   
    $DB->SQL($query);
    $DB->Query();
    while($data=$DB->nextRecord())
    {
      $maand=$maanden[$data['maand']];
      $cashflowHuidigjaar[$maand][strtolower($data['Grootboekrekening'])] +=$data['bedrag'];
    }

    $query = "desc FondsExtraInformatie";
    $DB->SQL($query);
    $DB->query();
    $extraVeld='';
    while($data=$DB->nextRecord('num'))
    {
      if($data[0]=='MaandelijkseHuur')
        $extraVeld='FondsExtraInformatie.MaandelijkseHuur,';
    }
    
    if($extraVeld<>'')
    {
      $query = "SELECT
TijdelijkeRapportage.fonds,
$extraVeld
TijdelijkeRapportage.rapportageDatum
FROM
TijdelijkeRapportage
JOIN FondsExtraInformatie ON TijdelijkeRapportage.fonds = FondsExtraInformatie.fonds
WHERE
TijdelijkeRapportage.Portefeuille='" . $this->portefeuille . "' AND
TijdelijkeRapportage.rapportageDatum='" . $this->rapportageDatum . "'
 " . $__appvar['TijdelijkeRapportageMaakUniek'];
      $DB->SQL($query);
      $DB->Query();
      $eindMaand=intval(substr($this->rapportageDatum,5,2));

      while ($data = $DB->nextRecord())
      {
        for($i=$eindMaand+1;$i<13;$i++)
        {
          $maand = $maanden[$i];
          $cashflowHuidigjaar[$maand]['huur'] += $data['MaandelijkseHuur'];
        }
      }
    }


		$rapJaar=substr($this->rapportageDatum,0,4);
		foreach ($cashfow->regelsRaw as $regel)
		{
		  $jaar=substr($regel['0'],6,4);
 		  if($jaar > ($rapJaar+13))
	      $jaar='> '.($rapJaar+13).' '.vertaalTekst('cumulatief' ,$this->pdf->rapport_taal);
		  $maand=$maanden[intval(substr($regel['0'],3,2))];
		  $cashflowJaar[$jaar]['lossing'] +=0;
		  $cashflowJaar[$jaar]['rente'] +=0;
		  if($jaar==$rapJaar)
		    $cashflowHuidigjaar[$maand][$regel[2]] +=$regel[3];



		  $cashflowJaar[$jaar][$regel[2]] +=$regel[3];
		  $cashflowTotaal +=$regel[3];
		}


		 $this->pdf->setY(120);
		 $this->pdf->underlinePercentage=0.8;
    $this->pdf->SetWidths(array(20,15,25,25,25,20,20));

		$this->pdf->SetAligns(array('L','L','R','R','R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('',vertaalTekst('maand',$this->pdf->rapport_taal),vertaalTekst('lossing',$this->pdf->rapport_taal),vertaalTekst('coupon',$this->pdf->rapport_taal),vertaalTekst('totaal',$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $totalen=array();
		foreach ($cashflowHuidigjaar as $maand=>$waarden)
		{
       $this->pdf->Row(array('',vertaalTekst($maand,$this->pdf->rapport_taal),$this->formatGetal($waarden['lossing'],0),$this->formatGetal($waarden['renob']+$waarden['rente'],0),$this->formatGetal($waarden['renob']+$waarden['lossing']+$waarden['rente'],0)));
       $totalen['lossing'] +=$waarden['lossing'];
       $totalen['rente'] +=$waarden['rente'];
      $totalen['renob'] +=$waarden['renob'];
      $totalen['huur'] +=$waarden['huur'];
		}
		$this->pdf->ln(2);
		$this->pdf->CellBorders = array('','',array('TS','UU'),array('TS','UU'),array('TS','UU'));
		$this->pdf->Row(array('',vertaalTekst('Totaal',$this->pdf->rapport_taal),$this->formatGetal($totalen['lossing'],0),$this->formatGetal($totalen['renob']+$totalen['rente'],0),$this->formatGetal($totalen['renob']+$totalen['lossing']+$totalen['rente'],0)));
$this->pdf->CellBorders = array();

		$this->pdf->setY(120);
		$this->pdf->SetWidths(array(160,30,25,25,25,20,20));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->Row(array('',vertaalTekst('jaar',$this->pdf->rapport_taal),vertaalTekst('lossing',$this->pdf->rapport_taal),vertaalTekst('coupon',$this->pdf->rapport_taal),vertaalTekst('totaal',$this->pdf->rapport_taal)));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $totalen=array();
		foreach ($cashflowJaar as $jaar=>$waarden)
		{
       $this->pdf->Row(array('',$jaar,$this->formatGetal($waarden['lossing'],0),$this->formatGetal($waarden['rente'],0),$this->formatGetal($waarden['lossing']+$waarden['rente'],0)));
       $totalen['lossing'] +=$waarden['lossing'];
       $totalen['rente'] +=$waarden['rente'];
		}
		$this->pdf->CellBorders = array('','',array('TS','UU'),array('TS','UU'),array('TS','UU'));
		$this->pdf->ln(2);
		$this->pdf->Row(array('',vertaalTekst('Totaal',$this->pdf->rapport_taal),$this->formatGetal($totalen['lossing'],0),$this->formatGetal($totalen['rente'],0),$this->formatGetal($totalen['lossing']+$totalen['rente'],0)));
$this->pdf->CellBorders = array();

    $this->pdf->setXY(20,100);
    $this->VBarDiagram(160,60,$cashflowHuidigjaar,vertaalTekst("Lopend jaar",$this->pdf->rapport_taal));
		$this->pdf->setXY(160,100);
    $this->VBarDiagram(160,60,$cashflowJaar,vertaalTekst("Langere termijn",$this->pdf->rapport_taal));
	}


	function VBarDiagram($w, $h, $data,$titel)
  {
      global $__appvar;
      $legendaWidth = 50;
      $grafiekPunt = array();
      $verwijder=array();



      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = $datum;
        $n=0;
        foreach ($waarden as $categorie=>$waarde)
        {
          $datumTotalen[$datum]+=$waarde;
          $grafiek[$datum][$categorie]=$waarde;
          $grafiekCategorie[$categorie][$datum]=$waarde;
          $categorien[$categorie] = $n;
          $categorieId[$n]=$categorie ;


          if(!isset($colors[$categorie]))
            $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          $n++;


        }
      }

      $colors=array('rente'=>$this->pdf->rapport_lichtblauw,'renob'=>array(100,100,100),//$this->pdf->rapport_lichtblauw,
      'lossing'=>$this->pdf->rapport_blauw);

      foreach ($verwijder as $datum)
      {
        foreach ($data[$datum] as $categorie=>$waarde)
        {
          $grafiek[$datum][$categorie]=0;
          $grafiekCategorie[$categorie][$datum]=0;
        }
      }

      $numBars = count($legenda);


      if($color == null)
      {
        $color=array(155,155,155);
      }
      $maxVal=max($datumTotalen);
      $minVal = min($datumTotalen);
      if($minVal>0)
      $minVal = 0;
      
      if($maxVal<-5*$minVal)
        $maxVal=abs($minVal)/5;
  
      if($maxVal>1000)
        $yWaardeStap=1000;
      elseif($maxVal>100)
        $yWaardeStap=100;
      elseif($maxVal>10)
        $yWaardeStap=10;
      else
        $yWaardeStap=1;
  
    $xPositie=$this->pdf->getX();
    $yPositie=$this->pdf->getY();
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->setXY($xPositie-20,$yPositie-$h-8);
    $this->pdf->Multicell($w,5,$titel,'','C');
    $this->pdf->setXY($xPositie+110,$yPositie-$h-8);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
    $this->pdf->Multicell(20,5,'X '.$this->formatGetal($yWaardeStap,0),'','L');
    $this->pdf->setXY($xPositie,$yPositie);


      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

      $n=0;
      foreach (array_reverse($this->categorieVolgorde) as $categorie)
      {
        if(is_array($grafiekCategorie[$categorie]))
        {
          $this->pdf->Rect($XstartGrafiek+$bGrafiek+3 , $YstartGrafiek-$hGrafiek+$n*10+2, 2, 2, 'DF',null,$colors[$categorie]);
          $this->pdf->SetXY($XstartGrafiek+$bGrafiek+6 ,$YstartGrafiek-$hGrafiek+$n*10+1.5 );
          $this->pdf->Cell(20, 3,$this->categorieOmschrijving[$categorie],0,0,'L');
          $n++;
        }
      }
      //echo ($maxVal-$minVal)."<br>\n"; ob_flush();
      $maxmaxVal=ceil(($maxVal)/(pow(10,strlen(round($maxVal)))))*pow(10,strlen(round($maxVal)));

      if($maxmaxVal/8 > $maxVal)
        $maxVal=$maxmaxVal/8;
      elseif($maxmaxVal/4 > $maxVal)
        $maxVal=$maxmaxVal/4;
      elseif($maxmaxVal/2 > $maxVal)
        $maxVal=$maxmaxVal/2;
      else
        $maxVal=$maxmaxVal;

    $minminVal=floor(($minVal)/(pow(10,strlen(round($minVal))-1)))*pow(10,strlen(round($minVal))-1);
    
    if($minminVal/8 < $minVal)
      $minVal=$minminVal/8;
    elseif($minminVal/4 < $minVal)
      $minVal=$minminVal/4;
    elseif($minminVal/2 < $minVal)
      $minVal=$minminVal/2;
    else
      $minVal=$minminVal;
  

      $unit = $hGrafiek / ($maxVal-$minVal) * -1;
      


      $nulYpos =$unit*$minVal*-1;

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

      $this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $bGrafiek, $hGrafiek,'FD','',array(245,245,245));

      $n=0;

      if($absUnit > 0 && $stapgrootte >0)
      for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek+$bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->SetXY($XstartGrafiek+$bGrafiek+1, $i-1.5);
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte/$yWaardeStap)."",0,0,'L');
        $n++;
        if($n>100)
          break;
      }
    $n=0;
    for($i=$nulpunt; $i < $bodem; $i+= $absUnit*$stapgrootte)
    {
      if($i!=$nulpunt&& $minVal<0)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek, $i, array('dash' => 1, 'color' => array(0, 0, 0)));
     
      $this->pdf->SetXY($XstartGrafiek+$bGrafiek+1, $i-1.5);
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
      $this->pdf->Cell(10, 3, $this->formatGetal(-1*$n*$stapgrootte/$yWaardeStap)."",0,0,'L');
      }
        $n++;
      if($n>100)
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

      $categorieTotalen=array();
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
          $categorieTotalen[$categorie]+=$val;
          if(abs($hval) > 3)
          {
         //   $this->pdf->SetXY($xval, $yval+($hval/2)-2);
         //   $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);

         if($legendaPrinted[$datum] != 1)
         {
           if(strlen($legenda[$datum])>4)
             $legendaTxt='>'.substr($legenda[$datum], 2, 4);
           else
             $legendaTxt=$legenda[$datum];
           $this->pdf->TextWithRotation($xval - 0.75, $YstartGrafiek + 5.25,vertaalTekst($legendaTxt,$this->pdf->rapport_taal), 45);
         }
           //$this->pdf->TextWithRotation($XDiag+($i+1)*$unitw-6,$YDiag+$hDiag+10,vertaalTekst($maanden[date("n",$julDatum)],$pdf->rapport_taal).'-'.date("Y",$julDatum),45);

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

    $xval=$xPositie+$w/2;


   $x1=$xval-50;
   $y1=$bodem+8;
   $hLegend=3;
   $legendaMarge=2;
   $step=40;
   

   $vertaling['rente']=vertaalTekst('Rente',$this->pdf->rapport_taal);
   $vertaling['lossing']=vertaalTekst('Lossingen',$this->pdf->rapport_taal);
   if(isset($categorieTotalen['renob']))
   {
     $vertaling['renob'] = vertaalTekst('Rente', $this->pdf->rapport_taal);
     $step = 25;
   }
   else
   {
     unset($colors['renob']);
   }

         foreach ($colors as $categorie=>$color)
      {
      		$this->pdf->SetFont($this->rapport_font, '', 6);
		      $this->pdf->SetTextColor($this->rapport_fonds_fontcolor['R'],$this->rapport_fonds_fontcolor['G'],$this->rapport_fonds_fontcolor['B']);
		      $this->pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));

          $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
          $this->pdf->Rect($x1-5, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x1  ,$y1);
          $this->pdf->Cell(0,4,$vertaling[$categorie]);
         // $y1+= $hLegend + $legendaMarge;
          $x1+=$step;
         $i++;

      }

    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
}
?>
<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/01/06 10:09:57 $
 		File Versie					: $Revision: 1.3 $

 		$Log: RapportCASHY_L41.php,v $
 		Revision 1.3  2013/01/06 10:09:57  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2012/12/30 14:27:11  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/12/08 14:48:08  rvv
 		*** empty log message ***
 		
 	
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");

//ini_set('max_execution_time',60);
class RapportCASHY_L41
{
	function RapportCASHY_L41($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "CASHY";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Cashflow overzicht lopend jaar en langere termijn";
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
		$this->pdf->templateVars['CASHYPaginas']=$this->pdf->page;
	  $this->pdf->SetWidths(array(10,25,25,25,40,20,20));
		$this->pdf->SetAligns(array('L','L','R','R','R','R','R'));


		// print categorie headers

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	$cashflowJaar=array();
		$cashflowTotaal=0;
	  $cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datumvanaf,$this->pdf->debug);
		$cashfow->genereerTransacties();
		$regels = $cashfow->genereerRows();
    $maanden=array(0,'jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
    $maandenLang=$__appvar["Maanden"];//[date("n",$datum)]
    
		for($i=1;$i<13;$i++)
		{
		  $cashflowHuidigjaar[$maanden[$i]]['lossing'] +=0;
		  $cashflowHuidigjaar[$maanden[$i]]['rente'] +=0;
      $cashflowHuidigjaarLang[$maandenLang[$i]]['lossing'] +=0;
		  $cashflowHuidigjaarLang[$maandenLang[$i]]['rente'] +=0;
		}

		$rapJaar=substr($this->rapportageDatum,0,4);
		foreach ($cashfow->regelsRaw as $regel)
		{
		  $jaar=substr($regel['0'],6,4);
 		  if($jaar > ($rapJaar+13))
	      $jaar='Overig';
      $maandInt=intval(substr($regel['0'],3,2));  
		  $cashflowJaar[$jaar]['lossing'] +=0;
		  $cashflowJaar[$jaar]['rente'] +=0;
		  if($jaar==$rapJaar)
      {
		    $cashflowHuidigjaar[$maanden[$maandInt]][$regel[2]] +=$regel[3];
        $cashflowHuidigjaarLang[$maandenLang[$maandInt]][$regel[2]] +=$regel[3];
      }



		  $cashflowJaar[$jaar][$regel[2]] +=$regel[3];
     // $cashflowJaar[$jaar]['totaal'] +=$regel[3];
		  $cashflowTotaal +=$regel[3];
		}


	  $this->pdf->setY(120);
		$this->pdf->underlinePercentage=0.8;
    $chartWidth=120;
    $cols=5;
    $colWidth=$chartWidth/$cols;
    
    $this->pdf->SetWidths(array(0,$colWidth,$colWidth,$colWidth,$colWidth,$colWidth,20));
    
    $this->pdf->SetAligns(array('L','L','R','R','R','R','R'));
    $this->pdf->fillCell=array(0,1,1,1,1,1);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetTextColor(255,255,255);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('','Maand','Lossing','Coupon','Totaal','Cumulatief'));
    $this->pdf->CellBorders = array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $totaal=0;
    $totaalProcent=0;
    unset($this->pdf->fillCell);
    $this->pdf->SetFillColor(0,0,0);
    $this->pdf->SetTextColor(0,0,0);

		foreach ($cashflowHuidigjaarLang as $maand=>$waarden)
		{
		   $totalen['lossing'] +=$waarden['lossing'];
       $totalen['rente'] +=$waarden['rente'];
       $this->pdf->Row(array('',$maand,$this->formatGetal($waarden['lossing'],0),$this->formatGetal($waarden['rente'],0),$this->formatGetal($waarden['lossing']+$waarden['rente'],0),$this->formatGetal($totalen['lossing']+$totalen['rente'],0)));
		}
		$this->pdf->ln(1);
		$this->pdf->CellBorders = array('','T',array('T'),array('T'),array('T'),array('T'));
		$this->pdf->Row(array('','Totaal',$this->formatGetal($totalen['lossing'],0),$this->formatGetal($totalen['rente'],0),$this->formatGetal($totalen['lossing']+$totalen['rente'],0),$this->formatGetal($totalen['lossing']+$totalen['rente'],0)));
    $this->pdf->CellBorders = array();

		$this->pdf->setY(120);
		$this->pdf->SetWidths(array(140,$colWidth,$colWidth,$colWidth,$colWidth,$colWidth,20));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		
    $this->pdf->SetAligns(array('L','L','R','R','R','R','R'));
    $this->pdf->fillCell=array(0,1,1,1,1,1);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetTextColor(255,255,255);
    $this->pdf->Row(array('','Jaar','Lossing','Coupon','Totaal','Cumulatief'));
    $this->pdf->CellBorders = array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $totaal=0;
    $totaalProcent=0;
    unset($this->pdf->fillCell);
    $this->pdf->SetFillColor(0,0,0);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $totalen=array();
   
		foreach ($cashflowJaar as $jaar=>$waarden)
		{
		   $totalen['lossing'] +=$waarden['lossing'];
       $totalen['rente'] +=$waarden['rente'];
       $this->pdf->Row(array('',$jaar,$this->formatGetal($waarden['lossing'],0),$this->formatGetal($waarden['rente'],0),$this->formatGetal($waarden['lossing']+$waarden['rente'],0),$this->formatGetal($totalen['lossing']+$totalen['rente'],0)));

		}
		$this->pdf->CellBorders = array('','T',array('T'),array('T'),array('T'),array('T'));
		$this->pdf->ln(1);
		$this->pdf->Row(array('','Totaal',$this->formatGetal($totalen['lossing'],0),$this->formatGetal($totalen['rente'],0),$this->formatGetal($totalen['lossing']+$totalen['rente'],0),$this->formatGetal($totalen['lossing']+$totalen['rente'],0)));
    $this->pdf->CellBorders = array();

    $this->pdf->setXY(15,95);
    $this->VBarDiagram($chartWidth,60,$cashflowHuidigjaar,"Lopend jaar");
		$this->pdf->setXY(155,95);
    $this->VBarDiagram($chartWidth,60,$cashflowJaar,"Langere termijn");
	}

  function VBarDiagram($w, $h, $data,$title)
  {
      global $__appvar;
      $XPage=$this->pdf->GetX();
      $YPage=$this->pdf->GetY();
      $this->pdf->SetXY($XPage,$YPage-$h);
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', 8.5);
		  $this->pdf->Cell(0, 5, $title, 0, 1);
  		//$this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),282,$this->pdf->GetY());
      $h=$h-10;
      
          
          
      $legendaWidth = 0;//20;
      $grafiekPunt = array();
      $verwijder=array();
      
      
            $tinten=array(1,0.5,0.7);
foreach($tinten as $tint)
{
$col[]=array($this->pdf->blue[0]*$tint,$this->pdf->blue[1]*$tint,$this->pdf->blue[2]*$tint);
$col[]=array($this->pdf->midblue[0]*$tint,$this->pdf->midblue[1]*$tint,$this->pdf->midblue[2]*$tint);//$this->pdf->midblue;
$col[]=array($this->pdf->lightblue[0]*$tint,$this->pdf->lightblue[1]*$tint,$this->pdf->lightblue[2]*$tint);//$this->pdf->lightblue;
$col[]=array($this->pdf->green[0]*$tint,$this->pdf->green[1]*$tint,$this->pdf->green[2]*$tint);//$this->pdf->green;
$col[]=array($this->pdf->kopkleur[0]*$tint,$this->pdf->kopkleur[1]*$tint,$this->pdf->kopkleur[2]*$tint);//$this->pdf->kopkleur;
$col[]=array($this->pdf->lightgreen[0]*$tint,$this->pdf->lightgreen[1]*$tint,$this->pdf->lightgreen[2]*$tint);//$this->pdf->lightgreen;
}



      foreach ($data as $datum=>$waardenn)
      {
        $legenda[$datum] = $datum;
        $n=0;
        $minVal=0;
        $maxVal=25;
        foreach ($waardenn as $categorie=>$waarde)
        {
          if($categorie=='LIQ')
            $categorie='Liquiditeiten';
          $grafiek[$datum][$categorie]=$waarde;
          $grafiekCategorie[$categorie][$datum]=$waarde;
          $categorien[$categorie] = $n;
          $categorieId[$n]=$categorie ;
          
          $datumTotaal[$datum]+=$waarde;
          
          if(!isset($this->categorieVolgorde[$categorie]))
          {
            $this->categorieVolgorde[$categorie]=$categorie;
            $this->categorieOmschrijving[$categorie]=$categorie;
          } 
          if(!isset($colors[$categorie])) 
            $colors[$categorie]=$col[$n];//array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          $n++;
        }
      }
      
      $maxVal=max($datumTotaal);
      $minVal=min($datumTotaal);
      if($minVal > 0)
        $minVal=0;

      $maxVal=ceil($maxVal/1000)*1000;
      $numBars = count($legenda);
      //$numBars=10;

      if($color == null)
      {
        $color=array(155,155,155);
      }

      if($maxVal <> 0)
        $maxStringWidth=$this->pdf->GetStringWidth($maxVal);
      $this->pdf->SetXY($XPage+$maxStringWidth+1,$YPage);
      
      $this->pdf->SetFont($this->pdf->rapport_font, '', 7);
      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1) - $maxStringWidth-1; // - legenda

      $n=0;


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

      $this->pdf->SetFont($this->pdf->rapport_font, '', 7);
      $this->pdf->SetTextColor(0,0,0);

      $stapgrootte = ceil(abs($bereik)/$horDiv);
      $top = $YstartGrafiek-$h;
      $bodem = $YstartGrafiek;
      $absUnit =abs($unit);

      $nulpunt = $YstartGrafiek + $nulYpos;
      $n=0;

      $lineW=1;
      if($stapgrootte <> 0)
      {
        for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
        {
          $skipNull=true;
          if($i != $nulpunt)
            $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $lineW ,$i,array('dash' => 0,'color'=>array(0,0,0)));
          $this->pdf->SetXY($XstartGrafiek-$maxStringWidth, $i-1.5);
          $this->pdf->Cell($maxStringWidth, 3, $this->formatGetal($n*$stapgrootte*-1)."",0,0,'R');
          $n++;
          if($n >20)
           break;
        }

        $n=0;
        for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
        {
          if($i != $nulpunt)
            $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $lineW ,$i,array('dash' => 0,'color'=>array(0,0,0)));
          if($skipNull == true)
            $skipNull = false;
          else
          {
            $this->pdf->SetXY($XstartGrafiek-$maxStringWidth, $i-1.5);
            $this->pdf->Cell($maxStringWidth, 3, $this->formatGetal($n*$stapgrootte)."",0,0,'R');
          }
          $n++;
          if($n >20)
            break;
        }
      }

      if($numBars > 0)
        $this->pdf->NbVal=$numBars;

      $vBar = ($bGrafiek / ($this->pdf->NbVal + 0.5));
      $bGrafiek = $vBar * ($this->pdf->NbVal + 0.5);
      $eBaton = ($vBar * 50 / 100);

      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(255,255,255)));
      $this->pdf->SetLineWidth(0.3527);

      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;

   
   foreach ($grafiek as $datum=>$data)
   {
      foreach (($this->categorieVolgorde) as $categorie)
      {
        if(isset($data[$categorie]))
        {
          $val=$data[$categorie];
        //foreach($data as $categorie=>$val)
        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
          $hval = ($val * $unit);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
          //$this->pdf->Line($xval, $yval+$hval, $xval + $lval ,$yval+$hval,array('dash' => 0,'color'=>array(255,255,255)));
          
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3 && $val < 100)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);

         if($legendaPrinted[$datum] != 1)
           $this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+4,$legenda[$datum],0);


         $legendaPrinted[$datum] = 1;
         }
      }
      $i++;
   }

   $i=0;
   $YstartGrafiekLast=array();
   foreach ($grafiekNegatief as $datum=>$data)
   {
      foreach (($this->categorieVolgorde) as $categorie)
      {
        if(isset($data[$categorie]))
        {
          $val=$data[$categorie];
          if(!isset($YstartGrafiekLast[$datum]))
            $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
          $hval = ($val * $unit);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'D',null,$colors[$categorie]);
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."",0,0,'C');
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
      }
      $i++;
   }
   $this->pdf->SetLineWidth(0.1);
   $this->pdf->Line($XstartGrafiek, $nulpunt, $XstartGrafiek + $bGrafiek ,$nulpunt,array('dash' => 0,'color'=>array(0,0,0)));
   $this->pdf->Line($XstartGrafiek, $bodem, $XstartGrafiek ,$top,array('dash' => 0,'color'=>array(0,0,0)));
   $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
   
   
   $hLegend=2;
   //$this->pdf->SetXY($XstartGrafiek + $bGrafiek,$top);
   $this->pdf->SetXY($XstartGrafiek,$YstartGrafiek+9);
   
   $x1 = $XstartGrafiek  ;
   //$x1 = $XstartGrafiek + $bGrafiek + 2 ;
   $x2 = $x1 + $hLegend + 2 ;
   //$y1 = $top;
   $y1=$YstartGrafiek+9;
      
   foreach($colors as $categorie=>$kleur)
   {
    $categorie=strtoupper(substr($categorie,0,1)).substr($categorie,1);
    $this->pdf->SetFillColor($kleur[0],$kleur[1],$kleur[2]);
    $this->pdf->Rect($x1, $y1, $hLegend, $hLegend, 'F');
    $this->pdf->SetXY($x2,$y1);
    $this->pdf->Cell(0,$hLegend,$categorie);
    $y1+=$hLegend*2;
   }
  
   //$legendaWidth
  }
}
?>
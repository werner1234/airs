<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/05/25 14:38:33 $
 		File Versie					: $Revision: 1.1 $

 		$Log: RapportCASHY_L42.php,v $
 		Revision 1.1  2014/05/25 14:38:33  rvv
 		*** empty log message ***
 		
 	
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");

//ini_set('max_execution_time',60);
class RapportCASHY_L42
{
	function RapportCASHY_L42($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "CASHY";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Cashflow overzicht lopende jaar en op langere termijn.";
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
    $this->pdf->templateVarsOmschrijving['CASHYPaginas']=$this->pdf->rapport_titel;
	  $this->pdf->SetWidths(array(10,25,25,25,40,20,20));
		$this->pdf->SetAligns(array('L','L','R','R','R','R','R'));


		// print categorie headers

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	$cashflowJaar=array();
		$cashflowTotaal=0;
	  $cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		$cashfow->genereerTransacties();
		$regels = $cashfow->genereerRows();
$maanden=array(0,'jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
		for($i=1;$i<13;$i++)
		{
		  		  $cashflowHuidigjaar[$maanden[$i]]['lossing'] +=0;
		  $cashflowHuidigjaar[$maanden[$i]]['rente'] +=0;

		}


		$rapJaar=substr($this->rapportageDatum,0,4);
		foreach ($cashfow->regelsRaw as $regel)
		{
		  $jaar=substr($regel['0'],6,4);
 		  if($jaar > ($rapJaar+13))
	      $jaar='Overig';
		  $maand=$maanden[intval(substr($regel['0'],3,2))];
		  $cashflowJaar[$jaar]['lossing'] +=0;
		  $cashflowJaar[$jaar]['rente'] +=0;
		  if($jaar==$rapJaar)
		    $cashflowHuidigjaar[$maand][$regel[2]] +=$regel[3];



		  $cashflowJaar[$jaar][$regel[2]] +=$regel[3];
		  $cashflowTotaal +=$regel[3];
		}


		 $this->pdf->setY(125);
		 $this->pdf->underlinePercentage=0.8;
    $this->pdf->SetWidths(array(20,15,25,25,25,20,20));

		$this->pdf->SetAligns(array('L','L','R','R','R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('','maand','lossing','coupon','totaal'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		foreach ($cashflowHuidigjaar as $maand=>$waarden)
		{
       $this->pdf->Row(array('',$maand,$this->formatGetal($waarden['lossing'],0),$this->formatGetal($waarden['rente'],0),$this->formatGetal($waarden['lossing']+$waarden['rente'],0)));
       $totalen['lossing'] +=$waarden['lossing'];
       $totalen['rente'] +=$waarden['rente'];
		}
		$this->pdf->ln(2);
		$this->pdf->CellBorders = array('','',array('TS','UU'),array('TS','UU'),array('TS','UU'));
		$this->pdf->Row(array('','Totaal',$this->formatGetal($totalen['lossing'],0),$this->formatGetal($totalen['rente'],0),$this->formatGetal($totalen['lossing']+$totalen['rente'],0)));
$this->pdf->CellBorders = array();

		$this->pdf->setY(125);
		$this->pdf->SetWidths(array(160,15,25,25,25,20,20));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->Row(array('','jaar','lossing','coupon','totaal'));
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
		$this->pdf->Row(array('','Totaal',$this->formatGetal($totalen['lossing'],0),$this->formatGetal($totalen['rente'],0),$this->formatGetal($totalen['lossing']+$totalen['rente'],0)));
$this->pdf->CellBorders = array();

    $this->pdf->setXY(20,110);
    $this->VBarDiagram(160,60,$cashflowHuidigjaar,"Lopend jaar");
		$this->pdf->setXY(160,110);
    $this->VBarDiagram(160,60,$cashflowJaar,"Langere termijn");
	}


	function VBarDiagram($w, $h, $data,$titel)
  {
      global $__appvar;
      $legendaWidth = 50;
      $grafiekPunt = array();
      $verwijder=array();

      $xPositie=$this->pdf->getX();
      $yPositie=$this->pdf->getY();
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
      $this->pdf->setXY($xPositie-20,$yPositie-$h-8);
      $this->pdf->Multicell($w,5,$titel,'','C');
      $this->pdf->setXY($xPositie+110,$yPositie-$h-8);
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
      $this->pdf->Multicell(20,5,'X 1.000','','L');
      $this->pdf->setXY($xPositie,$yPositie);


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
          if($waarde < 0)
          {
            $verwijder[$datum]=$datum;
            $grafiek[$datum][$categorie]=0;
            $grafiekCategorie[$categorie][$datum]=0;
          }


          if(!isset($colors[$categorie]))
            $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          $n++;


        }
      }
      $factor=1.5;
      $colors=array('rente'=>array(117,139,153),
      'lossing'=>array(117*$factor,139*$factor,153*$factor));

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
      $maxmaxVal=ceil($maxVal/(pow(10,strlen(round($maxVal)))))*pow(10,strlen(round($maxVal)));

      if($maxmaxVal/8 > $maxVal)
        $maxVal=$maxmaxVal/8;
      elseif($maxmaxVal/4 > $maxVal)
        $maxVal=$maxmaxVal/4;
      elseif($maxmaxVal/2 > $maxVal)
        $maxVal=$maxmaxVal/2;
      else
        $maxVal=$maxmaxVal;

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

      $this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $bGrafiek, $hGrafiek,'FD','',array(245,245,245));

      $n=0;

      if($absUnit > 0 && $stapgrootte >0)
      for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek+$bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->SetXY($XstartGrafiek+$bGrafiek+1, $i-1.5);
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte/1000)."",0,0,'L');
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
         //   $this->pdf->SetXY($xval, $yval+($hval/2)-2);
         //   $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);

         if($legendaPrinted[$datum] != 1)
           $this->pdf->TextWithRotation($xval-0.75,$YstartGrafiek+5.25,$legenda[$datum],45);

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


   $x1=$xval-50;
   $y1=$nulpunt+8;
   $hLegend=3;
   $legendaMarge=2;
   $vertaling['rente']='Rente';
   $vertaling['lossing']='Lossingen';

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
          $x1+=40;
         $i++;

      }

    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
}
?>
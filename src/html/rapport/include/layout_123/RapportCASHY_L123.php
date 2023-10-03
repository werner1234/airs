<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");

//ini_set('max_execution_time',60);
class RapportCASHY_L123
{
	function RapportCASHY_L123($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "CASHY";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Overzicht toekomstige kasstroom";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->excelData=array();
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}
  
  function header($data,$titel,$xOffset=0)
  {
    $this->pdf->rowHeight=$this->pdf->rapport_lowRow;
    $this->pdf->setAligns(array("L",'L','R','R','R'));
    unset($this->pdf->fillCell);
    $w=34;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array($xOffset,$w*4));
    $this->pdf->Row(array("",$titel));
    $this->pdf->ln(2);
    $this->pdf->fillCell=array(0,1,1,1,1);
    $this->pdf->CellBorders = array('','U','U','U','U');
    $this->pdf->SetFillColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0],$this->pdf->rapport_kop_fontcolor[1],$this->pdf->rapport_kop_fontcolor[2]);
    $this->pdf->SetWidths(array($xOffset,$w,$w,$w,$w));
    $this->pdf->Row($data);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    unset($this->pdf->fillCell);
    $this->pdf->rowHeight=$this->pdf->rapport_highRow;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);
    
  }
  
  function addRegel($waarden)
  {
    $this->pdf->SetFillColor($this->pdf->rapport_grijs[0],$this->pdf->rapport_grijs[1],$this->pdf->rapport_grijs[2]);
    $this->pdf->fillCell=array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('','U','U','U','U');
    $this->pdf->setDrawColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $this->pdf->CellFontColor=array(array(),array('r'=>$this->pdf->rapport_donkergroen[0],'g'=>$this->pdf->rapport_donkergroen[1],'b'=>$this->pdf->rapport_donkergroen[2]));
    $this->pdf->CellFontStyle=array(array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize),array($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize),array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize),array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize),array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize));
    $this->pdf->Row(array('',vertaalTekst($waarden['periode'],$this->pdf->rapport_taal),$this->formatGetal($waarden['lossing'],0),$this->formatGetal($waarden['rente'],0),$this->formatGetal($waarden['lossing']+$waarden['rente'],0)));
    unset($this->pdf->CellFontColor);
    unset($this->pdf->CellFontStyle);
  }

	function writeRapport()
	{
		global $__appvar;
		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    
    $poly=array($this->pdf->marge,25,
      $this->pdf->w-$this->pdf->marge,25,
      $this->pdf->w-$this->pdf->marge,30,
      $this->pdf->w-$this->pdf->marge,35,
      $this->pdf->marge,35);
    
    $this->pdf->Polygon($poly,'F',null,$this->pdf->rapport_lichtgrijs);
    $this->pdf->setAligns(array('L'));
    $this->pdf->SetWidths(array(250));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->sety(28);
    $this->pdf->Row(array(vertaalTekst('Dit toont een overzicht van de toekomstige couponbetalingen en de aflossingen van individuele obligaties. Er is een overzicht voor de komende 12 maanden en één voor de komende 10 jaar', $this->pdf->rapport_taal)));
    
	  $this->pdf->SetWidths(array(10,25,25,25,40,20,20));
		$this->pdf->SetAligns(array('L','L','R','R','R','R','R'));


		// print categorie headers

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	$cashflowJaar=array();
		$cashflowTotaal=0;
	  $cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		$cashfow->genereerTransacties();
		$regels = $cashfow->genereerRows();
    $maanden=array(0,"januari","februari","maart","april","mei","juni","juli","augustus","september","oktober","november","december");
    $cashflowHuidigjaar=array();
    $cashflowHuidigjaarGrafiek=array();
		for($i=1;$i<13;$i++)
		{
      $maand=$maanden[$i];
      $cashflowHuidigjaar[$maand]['rente'] +=0;
		  $cashflowHuidigjaar[$maand]['lossing'] +=0;
		  if($i==3)
		    $maand='mrt';
      else
        $maand=substr($maand,0,3);
      $cashflowHuidigjaarGrafiek[$maand]['rente'] +=0;
      $cashflowHuidigjaarGrafiek[$maand]['lossing'] +=0;
		}
    
    $rapJaar=substr($this->rapportageDatum,0,4);
    for($i=0;$i<11;$i++)
    {
      $cashflowJaar[$rapJaar+$i]['rente'] +=0;
      $cashflowJaar[$rapJaar+$i]['lossing'] +=0;
    }
    $cashflowJaar['> 10 Jaar']['rente']=0;
    
		foreach ($cashfow->regelsRaw as $regel)
		{
		  $jaar=substr($regel['0'],6,4);
 		  if($jaar > ($rapJaar+9))
	      $jaar='> 10 Jaar';
		  $maand=$maanden[intval(substr($regel['0'],3,2))];


		  if($jaar==$rapJaar)
      {
        $cashflowHuidigjaar[$maand][$regel[2]] += $regel[3];
        if($i==3)
          $maand='mrt';
        else
          $maand=substr($maand,0,3);
        $cashflowHuidigjaarGrafiek[$maand][$regel[2]] += $regel[3];
      }
		  $cashflowJaar[$jaar][$regel[2]] +=$regel[3];
		  $cashflowTotaal +=$regel[3];
		}


		$this->pdf->setY(40);
		$headerData=array('',vertaalTekst('Maandoverzicht',$this->pdf->rapport_taal)."\n ",vertaalTekst('Aflossingen',$this->pdf->rapport_taal)."\n ",
      vertaalTekst('Coupon',$this->pdf->rapport_taal)."\n ",vertaalTekst('Totaal per maand',$this->pdf->rapport_taal)."\n ");
    $this->header($headerData,vertaalTekst('Overzicht kasstroom in maanden', $this->pdf->rapport_taal),0);
    foreach ($cashflowHuidigjaar as $maand=>$waarden)
		{
      $waarden['periode']=$maand;
		  $this->addRegel($waarden);
    
		}

		$this->pdf->setY(40);
    $this->pdf->CellBorders = array();
    $headerData=array('',vertaalTekst('Jaaroverzicht',$this->pdf->rapport_taal)."\n ",vertaalTekst('Aflossingen',$this->pdf->rapport_taal)."\n ",
      vertaalTekst('Coupon',$this->pdf->rapport_taal)."\n ",vertaalTekst('Totaal per jaar',$this->pdf->rapport_taal)."\n ");
    $this->header($headerData,vertaalTekst('Overzicht kasstroom in jaren', $this->pdf->rapport_taal),145);
		foreach ($cashflowJaar as $jaar=>$waarden)
		{
      $waarden['periode']=$jaar;
      $this->addRegel($waarden);
		}
	  $this->pdf->CellBorders = array();


    $this->pdf->setXY(18,175);
    $this->VBarDiagram(125,40,$cashflowHuidigjaarGrafiek,'');
		$this->pdf->setXY(160,175);
    $this->VBarDiagram(125,40,$cashflowJaar,'');

	}


	function VBarDiagram($w, $h, $data,$titel)
  {
      global $__appvar;
      $legendaWidth = 0;
      $grafiekPunt = array();
      $verwijder=array();

      $xPositie=$this->pdf->getX();
      $yPositie=$this->pdf->getY();
      $this->pdf->setXY($xPositie,$yPositie);
  
      $datumTotalen=array();
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

      $colors=array(
        'rente'     =>array(255,147,123),
        'lossing'   =>array(0,0,107)
      );

      foreach ($verwijder as $datum)
      {
        foreach ($data[$datum] as $categorie=>$waarde)
        {
          $grafiek[$datum][$categorie]=0;
          $grafiekCategorie[$categorie][$datum]=0;
        }
      }

      $numBars = count($legenda);


      $maxVal=max($datumTotalen);


      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

      $n=0;

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

      $this->pdf->SetFont($this->pdf->rapport_font, '',$this->pdf->rapport_fontsize);
      $this->pdf->SetTextColor(0,0,0);

      $stapgrootte = (abs($bereik)/$horDiv);
      $top = $YstartGrafiek-$h;
      $bodem = $YstartGrafiek;
      $absUnit =abs($unit);

      $nulpunt = $YstartGrafiek + $nulYpos;

     // $this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $bGrafiek, $hGrafiek,'D');
  
    $this->pdf->Line($XstartGrafiek, $YstartGrafiek-$hGrafiek, $XstartGrafiek ,$YstartGrafiek,array('dash' =>0,'color'=>array(0,0,0)));
  //  $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek+$bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));

      $n=0;

      if($absUnit > 0 && $stapgrootte >0)
      for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek-0.5, $i, $XstartGrafiek+$bGrafiek ,$i,array('dash' => 0,'color'=>array(0,0,0)));
        $this->pdf->SetXY($XstartGrafiek-11, $i-1.5);
        $this->pdf->SetFont($this->pdf->rapport_font, '', 5.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte,0)."",0,0,'R');
        $n++;
      }
    
    
    if($numBars > 0)
      $this->pdf->NbVal=$numBars;

        $vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
 
        $eBaton = ($vBar * 50 / 100);


      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);

      $i=0;
    $legendaPrinted=array();
    $lastX=0;
    $lastY=0;
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

         $this->pdf->SetTextColor(0,0,0);

         if($legendaPrinted[$datum] != 1)
         {
          // $this->pdf->TextWithRotation($xval - 0.75, $YstartGrafiek + 5.25, $legenda[$datum], 0);
           $this->pdf->setXY($xval+.5*$eBaton-5,$YstartGrafiek+1);
           $this->pdf->Cell(10, 4,$legenda[$datum],0,0,'C');
         }

           //$this->pdf->TextWithRotation($XDiag+($i+1)*$unitw-6,$YDiag+$hDiag+10,vertaalTekst($maanden[date("n",$julDatum)],$pdf->rapport_taal).'-'.date("Y",$julDatum),45);

         if($grafiekPunt[$categorie][$datum])
         {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
            if($lastX>0)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
         $legendaPrinted[$datum] = 1;
      }
      $i++;
   }


   $x1=$xPositie+30;
   $y1=$nulpunt+8;
   $hLegend=3;
   $legendaMarge=2;
   $vertaling['rente']=vertaalTekst('Totaal coupon',$this->pdf->rapport_taal);
   $vertaling['lossing']=vertaalTekst('Totaal aflossing',$this->pdf->rapport_taal);

      foreach ($colors as $categorie=>$color)
      {
          $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
		      $this->pdf->SetTextColor($this->rapport_fonds_fontcolor['R'],$this->rapport_fonds_fontcolor['G'],$this->rapport_fonds_fontcolor['B']);
		      $this->pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));

          $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
          $this->pdf->Rect($x1-5, $y1+.25, $hLegend, $hLegend, 'DF');
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
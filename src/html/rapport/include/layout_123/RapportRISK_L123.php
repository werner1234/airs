<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/layout_123/ATTberekening_L123.php");
include_once("rapport/include/layout_123/RapportOIB_L123.php");


class RapportRISK_L123
{
	function RapportRISK_L123($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "RISK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->excelData 	= array();

		$this->pdf->rapport_titel = "Attributie overzicht op categorieën";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
		$this->oib = new RapportOIB_L123($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    
	}

  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		$DB = new DB();
		global $__appvar;
		//$this->pdf->AddPage();
    $query = "SELECT Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
    $this->pdf->rowHeight = 4;

		$this->oib->getOIBdata();
		$this->oib->hoofdcategorien['geen-Hcat']='geen-Hcat';
		$oibData=$this->oib->hoofdCatogorieData;
		$oibData['totaal']['port']['procent']=1;

    if(db2jul($this->rapportageDatumVanaf) > db2jul($portefeuilledata['startDatum']))
	   	$rapportageStartJaar= date("Y-01-01",$this->pdf->rapport_datum);
	  else
	   	$rapportageStartJaar=substr($portefeuilledata['startDatum'],0,10);
	  $this->tweedePerformanceStart=$rapportageStartJaar;
	//  $att=new ATTberekening2($this);
  //  $waarden=$att->bereken($this->rapportageDatumVanaf,  $this->rapportageDatum,$this->pdf->rapportageValuta,'hoofdcategorie');
  //  listarray($waarden);

    $att=new ATTberekening_L123($this);
    $att->indexPerformance=true;
    //$this->waarden['Historie']=$att->bereken(substr($this->pdf->PortefeuilleStartdatum,0,10),  $this->rapportageDatum,'EUR','hoofdcategorie');
    $this->waarden['Historie']=$att->bereken($rapportageStartJaar,  $this->rapportageDatum,$this->pdf->rapportageValuta,'hoofdcategorie');

    $stapelTypen=array('procent','procentBruto'); //,'bijdrage'
    $somTypen=array('indexPerf');
    $gemiddeldeTypen=array('weging');

    foreach ($this->waarden['Historie'] as $categorie=>$categorieData)
     $this->jaarTotalen[$categorie]=array();


    foreach ($this->waarden['Historie'] as $categorie=>$categorieData)
    { 
      $laatste=array();
       foreach ($categorieData['perfWaarden'] as $datum=>$waarden)
      { //listarray($waarden);
        $jaar=substr($datum,0,4);
        $this->jaarTotalen[$categorie][$jaar]['resultaat']+=$waarden['resultaat'];
        foreach ($stapelTypen as $type)
        {
          $this->jaarTotalen[$categorie][$jaar][$type]=((1+$waarden[$type])*(1+$laatste[$jaar][$type])-1);
          $laatste[$jaar][$type]=$this->jaarTotalen[$categorie][$jaar][$type];
        }
        foreach ($somTypen as $type)
        {
          $this->jaarTotalen[$categorie][$jaar][$type]+=$waarden[$type];
        }
        foreach ($gemiddeldeTypen as $type)
          $this->jaarTotalen[$categorie][$jaar][$type]+=$waarden[$type];
        
        if($categorie!='totaal')
        {
          //$this->maandTotalen[$datum]['attributieEffect']+=(($waarden['weging']*$waarden['procent'])-($waarden['indexPerf']*$waarden['indexBijdrageWaarde']))*100;
          $this->maandTotalen[$datum]['allocateEffect']+=($waarden['weging']-$waarden['indexBijdrageWaarde'])*$waarden['indexPerf']*100;
          //$this->maandTotalen[$datum]['selectieEffect']+=($waarden['procent']-$waarden['indexPerf'])*$waarden['weging']*100;
        
          $this->jaarTotalen[$categorie][$jaar]['allocateEffect']+=($waarden['weging']-$waarden['indexBijdrageWaarde'])*$waarden['indexPerf'];
          
          $this->maandCumulatief[$datum]['allocateEffect']+=$this->jaarTotalen[$categorie][$jaar]['allocateEffect'];
          
         // echo "$datum $jaar $categorie ".$this->jaarTotalen[$categorie][$jaar]['allocateEffect']." <br>\n";
          $this->jaarTotalen['totaal'][$jaar]['allocateEffect']+=($waarden['weging']-$waarden['indexBijdrageWaarde'])*$waarden['indexPerf'];//wordt gebruikt
        }
        else
        {
           $this->maandTotalen[$datum]['attributieEffect']= ($this->jaarTotalen[$categorie][$jaar]['procent']-$this->jaarTotalen[$categorie][$jaar]['indexPerf'])*100;
           $this->maandTotalen[$datum]['selectieEffect']+=  (($waarden['procentBruto']-$waarden['indexPerf'])-$this->maandTotalen[$datum]['allocateEffect']/100)*100;
           
           
           $this->maandCumulatief[$datum]['selectieEffect']  =(($this->jaarTotalen[$categorie][$jaar]['procent']-$this->jaarTotalen[$categorie][$jaar]['indexPerf'])-($this->maandCumulatief[$datum]['allocateEffect']))*100;  

        }

         $this->jaarTotalen[$categorie][$jaar]['portBijdrage']+=$waarden['bijdrage'];
         //$this->jaarTotalen[$categorie][$jaar]['indexBijdrageWaarde']+=$waarden['bijdrage'];         
                  

        $lastCategorie=$categorie;
           // $this->formatGetal($this->waarden['Periode'][$categorie]['bijdrage'],2),
      }

      foreach ($gemiddeldeTypen as $type)
        $this->jaarTotalen[$categorie][$jaar][$type]=$this->jaarTotalen[$categorie][$jaar][$type]/count($categorieData['perfWaarden']);
    }
//listarray($this->jaarTotalen);
    $startJaar=date("Y",$this->pdf->rapport_datum);
    $this->oib->hoofdcategorien['totaal']="Totaal";
    $this->pdf->rapport_titel = "Attributie overzicht op categorieën";
    $this->pdf->AddPage();
    $this->pdf->templateVars['RISKPaginas']=$this->pdf->page;
    $this->pdf->SetWidths(array(40,30,30,30,30,30,30,30));
   	$this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R'));
   	$this->pdf->ln(5);

    $this->pdf->SetFillColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0],$this->pdf->rapport_kop_fontcolor[1],$this->pdf->rapport_kop_fontcolor[2]);

    $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 281, 8 , 'F');

    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $header=array("", vertaalTekst("Tactische\nWeging", $this->pdf->rapport_taal), vertaalTekst("Strategische\nWeging", $this->pdf->rapport_taal), vertaalTekst("Rendement\nPortefeuille", $this->pdf->rapport_taal),
      vertaalTekst("Ontwikkeling\nbenchmark", $this->pdf->rapport_taal), vertaalTekst('Attributie', $this->pdf->rapport_taal), vertaalTekst("Allocatie\neffect", $this->pdf->rapport_taal),
      vertaalTekst("Selectie\neffect", $this->pdf->rapport_taal));
    $this->pdf->row($header);
    $this->pdf->excelData[]=$header;
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->ln();

   foreach ($this->jaarTotalen as $categorie=>$jaarWaarden)
    {

      $waarden=$jaarWaarden[$startJaar];

      if ( $categorie === 'totaal' ) {
        $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0], $this->pdf->rapport_kop_fontcolor[1], $this->pdf->rapport_kop_fontcolor[2]);
        $this->pdf->Rect($this->pdf->marge, $this->pdf->getY()-1, 297-($this->pdf->marge*2), 5.5, 'F',null,$this->pdf->rapport_donkergrijs);
      }

      //listarray($waarden);      
      $this->pdf->row(array(vertaalTekst($this->oib->hoofdcategorien[$categorie], $this->pdf->rapport_taal),
      $this->formatGetal($waarden['weging']*100,1), // . ' '.$this->formatGetal(($oibData[$categorie]['port']['procent'])*100,1)
      $this->formatGetal($att->normData[$categorie],1),
      $this->formatGetal($waarden['procentBruto']*100,2),
      $this->formatGetal($waarden['indexPerf']*100,2),
      $this->formatGetal(($waarden['procentBruto']-$waarden['indexPerf'])*100,2),//$this->formatGetal((($waarden['weging']*$waarden['procent'])-($waarden['indexPerf']*$waarden['indexBijdrageWaarde']))*100,2),
      $this->formatGetal($waarden['allocateEffect']*100,2),
      $this->formatGetal((($waarden['procentBruto']-$waarden['indexPerf'])-$waarden['allocateEffect'])*100,2)));
      $this->pdf->ln(5);
      $this->pdf->excelData[]=array(vertaalTekst($this->oib->hoofdcategorien[$categorie], $this->pdf->rapport_taal),
        round($waarden['weging']*100,1), // . ' '.round(($oibData[$categorie]['port']['procent'])*100,1)
        round($att->normData[$categorie],1),
        round($waarden['procentBruto']*100,2),
        round($waarden['indexPerf']*100,2),
        round(($waarden['procentBruto']-$waarden['indexPerf'])*100,2),//round((($waarden['weging']*$waarden['procent'])-($waarden['indexPerf']*$waarden['indexBijdrageWaarde']))*100,2),
        round($waarden['allocateEffect']*100,2),
        round((($waarden['procentBruto']-$waarden['indexPerf'])-$waarden['allocateEffect'])*100,2));

      $this->pdf->SetTextColor(0,0,0);
    }
    
      //$this->pdf->rapport_titel = "Maandelijkse attributie-effecten";
     // $this->pdf->AddPage();
      $this->pdf->setXY(15,176);
      $barData=array();
     // listarray($this->maandTotalen);
      foreach($this->maandTotalen as $maand=>$waarden)
      {
        unset($waarden['attributieEffect']);
        $barData[$maand]=$waarden;
      }
      $this->VBarDiagram2(130,137-50,$barData,'');
      $colors=array('allocate effect'=>array(0,0,107),'selectie effect'=>array(255,147,123));//,'Totaal'=>array(0, 52, 121)); //'attributie effect'=>,array(87,165,25)

      $xval=25;$yval=185-5;
      foreach($colors as $effect=>$color)
      {
         $this->pdf->Rect($xval, $yval, 3, 3, 'DF',null,$color);
         $this->pdf->SetTextColor(0);
         $this->pdf->SetXY($xval+5, $yval);
         $this->pdf->Cell(50, 3,  vertaalTekst($effect ,$this->pdf->rapport_taal),0,0,'L');
         $xval+=40;
      }
      
      foreach($this->maandTotalen as $maand=>$maandWaarden)
        foreach($maandWaarden as $type=>$waarde)
        {
          if($type=='attributieEffect') //||)
            $tmp[$type]=$waarde;
          elseif($type=='selectieEffect')
            $tmp[$type]=$this->maandCumulatief[$maand][$type];
          else
            $tmp[$type]+=$waarde;  
          $this->maandTotalenCumulatief[$type][$maand]=$tmp[$type];
        }
        
    //  $colors=array('allocate effect'=>array(0,52,121),'selectie effect'=>array(87,165,25),'attributie effect'=>array(108,31,128)); //
     $colors=array('allocate effect'=>array(0,0,107),'selectie effect'=>array(255,147,123),'attributie effect'=>array(1,150,255)); //

    $this->LineDiagram(160,50+44,120,120-50,$this->maandTotalenCumulatief,'');
      $xval=165;$yval=185-5;
      foreach($colors as $effect=>$color)
      {
         $this->pdf->Rect($xval, $yval, 3, 3, 'DF',null,$color);
         $this->pdf->SetTextColor(0);
         $this->pdf->SetXY($xval+5, $yval);
         $this->pdf->Cell(50, 3,  vertaalTekst($effect , $this->pdf->rapport_taal),0,0,'L');
         $xval+=40;
      }
      
      
  



      

	}
  
  
function LineDiagram($x,$y,$w, $h, $data, $title,$color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;
    
    $this->pdf->Rect($x-10,$y-5,$w+15,$h+15);
    $this->pdf->setXY($x,$y);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Multicell($w,4, vertaalTekst($title, $this->pdf->rapport_taal),'','C');
    $this->pdf->setXY($x,$y+8);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

    //$bereikdata =   $data;

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

     $maanden=array();
      $maxVal=0;
      $minVal=0;
      foreach($data as $type=>$maandData)
      {
        
        $tmp=count($maandData);
        if($tmp > $aantalMaanden)
          $aantalMaanden=$tmp;
        foreach($maandData as $maand=>$waarde)
        {
          $maanden[$maand]=$maand;
          if($waarde > $maxVal)
            $maxVal = $waarde;
          if($waarde < $minVal)  
            $minVal = $waarde;
        }
      }

    $minVal = floor(($minVal-1) * 1.1);
    $maxVal = ceil(($maxVal+1) * 1.1);
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / $aantalMaanden;

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

       $colors=array(
         'allocateEffect'     => array(0,0,107),
         'selectieEffect'     => array(255,147,123),
         'attributieEffect'   => array(1,150,255)
       );

  


    //for ($i=0; $i<count($data); $i++)
    $maandPrinted=array();
    foreach($data as $type=>$maandData)
    {
      $i=0;
      $color=$colors[$type];
      $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
      foreach($maandData as $maand=>$waarde)
      {
        //foreach($maandData as $line)
       // $extrax=($unit*0.1*-1);
        
       //   $extrax1=($unit*0.1*-1);
        

       // $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8, vertaalTekst($legendDatum[$i], $this->pdf->rapport_taal),0);

        $yval2 = $YDiag + (($maxVal-$waarde) * $waardeCorrectie) ;
        
        if($i <> -1)
        {
          $this->pdf->line($XDiag+$i*$unit+$extrax1, $yval, $XDiag+($i+1)*$unit+$extrax, $yval2,$lineStyle );
        }
        $this->pdf->Rect($XDiag+($i+1)*$unit-0.5+$extrax, $yval2-0.5, 1, 1 ,'F','',$color);
        
        if($waarde <> 0)
          $this->pdf->Text($XDiag+($i+1)*$unit+$extrax,$yval2-2.5,$this->formatGetal($waarde,1));
          $yval = $yval2;
        
      
        if(!isset($maandPrinted[$maand]))
        {
          $maandPrinted[$maand]=1;
          $this->pdf->Text($XDiag+($i+1)*$unit+$extrax,$bodem+5,date('M',db2jul($maand)));
          
        }
        
        $i++;
        
        
      }
    }
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }

  function VBarDiagram2($w, $h, $data, $format, $color=null,$nbDiv=4,$numBars=0)
  {
      global $__appvar;
      $legendDatum = $data['datum'];
      //$data = $data['portefeuille'];
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      //$this->pdf->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1);

      $this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $w- $margin, $hGrafiek,'D',''); //,array(245,245,245)
      if($color == null)
          $color=array(155,155,155);
      
      $maxVal=0;
      $minVal=0;
      $maanden=array();
      foreach($data as $maand=>$maandData)
      {
        $maanden[$maand]=$maand;
        foreach($maandData as $type=>$waarde)
        {
          if($waarde > $maxVal)
            $maxVal = $waarde;
          if($waarde < $minVal)  
            $minVal = $waarde;
        }
      }
      if($maxVal > 1)
        $maxVal=ceil($maxVal);
      if($minVal < -1)  
        $minVal=floor($minVal);
      $minVal = $minVal * 1.1;
      $maxVal = $maxVal * 1.1;      
      if ($maxVal <0)
       $maxVal=0;

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

      $horDiv = 10;
      $horInterval = $hGrafiek / $horDiv;
      $bereik = $hGrafiek/$unit;

      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      $this->pdf->SetTextColor(0,0,0);

      $stapgrootte = ceil(abs($bereik)/$horDiv*10)/10;
      $top = $YstartGrafiek-$h;
      $bodem = $YstartGrafiek;
      $absUnit =abs($unit);

      $nulpunt = $YstartGrafiek + $nulYpos;
      $n=0;

      for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
      {
        $skipNull = true;
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");
        $n++;
        if($n >20)
         break;
      }

      $n=0;
      for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        if($skipNull == true)
          $skipNull = false;
        else
        $this->pdf->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");
        $n++;
        if($n >20)
          break;
      }
      
      $numBars=count($data);
      if($numBars > 0)
        $this->pdf->NbVal=$numBars;

         $colors=array('allocateEffect'=>array(0,0,107),'selectieEffect'=>array(255,147,123));//,'totaalEffect'=>array(0, 52, 121)); //

    $vBar = ($bGrafiek / ($this->pdf->NbVal ))/3; //4
      $bGrafiek = $vBar * ($this->pdf->NbVal );
      $eBaton = ($vBar * 80 / 100);
      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;
      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      foreach($data as $maand=>$maandData)
      {
        
        foreach($maandData as $type=>$val)
        {
          $color=$colors[$type];
          //Bar
          $xval = $XstartGrafiek + ($i + 1) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiek + $nulYpos;
          $hval = ($val * $unit);
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$color);
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3 && $eBaton > 4)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);
          $i++;
          }
          $i++;
          

          $this->pdf->Text($XstartGrafiek + ($i -2) * $vBar - $eBaton / 2,$YstartGrafiek +3 ,date('M',db2jul($maand)));
          
      }



     // $color=array(155,155,155);
     // $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
  }


  function VBarDiagram($w, $h, $data, $format, $color=null, $maxVal=0, $nbDiv=4,$numBars=0)
  {
      global $__appvar;
      $legendDatum = $data['datum'];
      //$data = $data['portefeuille'];
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1);

$this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $w- $margin, $hGrafiek,'FD','',array(245,245,245));

      if($color == null)
          $color=array(155,155,155);
      if ($maxVal == 0)
        $maxVal = ceil(max($data));
      $minVal = floor(min($data));

      $minVal = $minVal * 1.1;
      $maxVal = $maxVal * 1.2;

      if ($maxVal <0)
       $maxVal=0;

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

      $horDiv = 10;
      $horInterval = $hGrafiek / $horDiv;
      $bereik = $hGrafiek/$unit;

      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      $this->pdf->SetTextColor(0,0,0);

      $stapgrootte = ceil(abs($bereik)/$horDiv);
      $top = $YstartGrafiek-$h;
      $bodem = $YstartGrafiek;
      $absUnit =abs($unit);

      $nulpunt = $YstartGrafiek + $nulYpos;
      $n=0;

      for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
      {
        $skipNull = true;
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");
        $n++;
        if($n >20)
         break;
      }

      $n=0;
      for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        if($skipNull == true)
          $skipNull = false;
        else
        $this->pdf->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");
        $n++;
        if($n >20)
          break;
      }

      if($numBars > 0)
        $this->pdf->NbVal=$numBars;

        $colors=array(array(0,0,107),array(255,147,123),array(1,150,255));

      $vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
      $bGrafiek = $vBar * ($this->pdf->NbVal + 1);
      $eBaton = ($vBar * 80 / 100);
      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;
      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      foreach($data as $index=>$val)
      {

        $color=$colors[$index];
          //Bar
          $xval = $XstartGrafiek + ($i + 1) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiek + $nulYpos;
          $hval = ($val * $unit);
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$color);
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);
          $i++;
      }



     // $color=array(155,155,155);
     // $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
  }
}
?>
<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 		File Versie					: $Revision: 1.5 $

 		$Log: RapportPERFG_L19.php,v $
 		Revision 1.5  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.4  2016/06/22 16:15:05  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2016/06/05 12:37:50  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/05/22 19:07:47  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/05/15 17:15:00  rvv
 		*** empty log message ***
 		

*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L19.php");

class RapportPERFG_L19
{
	function RapportPERFG_L19($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Portefeuille Overzicht";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


  function tweedeStart()
  {
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    if(db2jul($this->pdf->PortefeuilleStartdatum) == db2jul($this->rapportageDatumVanaf))
    {
      $this->tweedePerformanceStart = substr($this->pdf->PortefeuilleStartdatum,0,10);
    }
    else
    {
      $this->tweedePerformanceStart = "$RapStartJaar-01-01";
      if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01")
      {
        $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,"$RapStartJaar-01-01",true);
        vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,"$RapStartJaar-01-01");
      }
    }
  }

  function row($row)
  {
    $this->pdf->CellFontColor=array();
   // listarray($row);
    foreach($row as $index=>$value)
    {
      if(substr($value,0,1)=='-')
        $this->pdf->CellFontColor[$index]=array('r'=>200,'g'=>0,'b'=>0);
      else
        $this->pdf->CellFontColor[$index]=array('r'=>0,'g'=>0,'b'=>0);
    }
//listarray($this->pdf->CellFontColor);
    $this->pdf->Row($row);
    $this->pdf->CellFontColor=array();
  }


  function writeRapport()
	{
		global $__appvar;
    $this->tweedeStart();
    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);

		$this->pdf->AddPage();

    $DB=new DB();
    if(!is_array($this->pdf->grafiekKleuren[$this->pdf->portefeuilledata['Vermogensbeheerder']]))
	  {
	      $q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
	    	$DB->SQL($q);
  	  	$DB->Query();
    		$kleuren = $DB->LookupRecord();
    		$kleuren = unserialize($kleuren['grafiek_kleur']);
    		$this->pdf->grafiekKleuren[$this->pdf->portefeuilledata['Vermogensbeheerder']]=$kleuren;
	  }
    $kleuren=$this->pdf->grafiekKleuren[$this->pdf->portefeuilledata['Vermogensbeheerder']]['OIB'];
		// haal totaalwaarde op om % te berekenen

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						  .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

   	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal,
              beleggingscategorie,beleggingscategorieOmschrijving ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						  .$__appvar['TijdelijkeRapportageMaakUniek']." 
              GROUP BY beleggingscategorie 
              ORDER BY beleggingscategorieVolgorde";
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();

    while($data=$DB->nextRecord())
    {
      $verdeling[$data['beleggingscategorie']]=$data;
		  $verdeling[$data['beleggingscategorie']]['percentage'] = $data['totaal']/$totaalWaarde*100;
      $verdeling[$data['beleggingscategorie']]['kleur']=array($kleuren[$data['beleggingscategorie']]['R']['value'],$kleuren[$data['beleggingscategorie']]['G']['value'],$kleuren[$data['beleggingscategorie']]['B']['value']);
      $pieData['data'][$data['beleggingscategorieOmschrijving']]=round($verdeling[$data['beleggingscategorie']]['percentage'],2);
      $pieData['kleur'][]=$verdeling[$data['beleggingscategorie']]['kleur'];
    }
    
    $att=new ATTberekening_L19($this);
    $att->indexPerformance=true;
    $this->waarden['Jaar']=$att->bereken($this->tweedePerformanceStart,  $this->rapportageDatum);
    $this->waarden['Periode']=$att->bereken($this->rapportageDatumVanaf,  $this->rapportageDatum);
    //listarray($this->waarden['Periode']);
    
    
    $this->pdf->SetWidths(array(10,50,20,20,22,20));
    $this->pdf->SetAligns(array('L','L','R','R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders=array('','U','U','U','U','U');
    $this->pdf->SetY(35);
    $this->row(array('','Asset Class','Waarde','Portfolio%','Benchmark%','Verschil'));
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
    $totalen=array();
    foreach($verdeling as $categorie=>$categorieData)
    {
      $this->row(array('',$categorieData['beleggingscategorieOmschrijving'],
                            $this->formatGetal($categorieData['totaal'],0),
                            $this->formatGetal($categorieData['percentage'],2),
                            $this->formatGetal($this->waarden['Periode']['totaal']['benchmarkCategorieVerdeling'][$categorie]*100,2),
                            $this->formatGetal($categorieData['percentage']-($this->waarden['Periode']['totaal']['benchmarkCategorieVerdeling'][$categorie])*100,2)
                 
                 ));
      $totalen['totaal']+=$categorieData['totaal'];
      $totalen['percentage']+=$categorieData['percentage'];
    }
    $this->pdf->CellBorders=array('','T','T','T','T','T');
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
    $this->Row(array('','Totaal',$this->formatGetal($totalen['totaal'],0),$this->formatGetal($totalen['percentage'],2),'',''));
    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
    $this->pdf->SetXY(25,120);
    PieChart($this->pdf,60, 60, $pieData['data'], '%l (%p)', $pieData['kleur']);




    $this->pdf->SetWidths(array(160,25,25,25,25,25));
    $this->pdf->SetAligns(array('L','L','R','R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders=array('','U','U','U','U');
    $this->pdf->SetY(35);
    $this->row(array('','Maand','Portfolio','Benchmark','Verschil'));
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
    $totalen=array();
    $maand=0;
    $jaar=substr($this->rapportageDatum,0,4);
    foreach($this->waarden['Jaar']['totaal']['perfWaarden'] as $maand=>$maandData)
    {
      $julMaand=db2jul($maand);
        $jaar=date('Y',$julMaand);
        $maand=date('m',$julMaand);
        $this->row(array('',$maand.'-'.$jaar,
                            $this->formatGetal($maandData['procent']*100,2),
                            $this->formatGetal($maandData['indexPerf']*100,2),
                            $this->formatGetal(($maandData['procent']-$maandData['indexPerf'])*100,2)));
    }
    if($maand<12)
      for($i=$maand+1;$i<=12;$i++)
        $this->row(array('',sprintf('%02d', $i)."-".$jaar));

    $this->pdf->CellBorders=array('','T','T','T','T');
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
    $this->row(array('','',$this->formatGetal($this->waarden['Periode']['totaal']['procent'],2),$this->formatGetal($this->waarden['Periode']['totaal']['indexPerf'],2),$this->formatGetal($this->waarden['Periode']['totaal']['procent']-$this->waarden['Periode']['totaal']['indexPerf'],2)));
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
   // listarray($this->waarden['Periode']['totaal']['procent']);
   $this->addPerfLine($this->waarden['Jaar']['totaal']);
    //echo 'rvv';
//listarray($this->waarden['Periode']['totaal']);
   $this->pdf->AddPage();

    $bovencat=$att->categorien;
    $this->pdf->SetY(35);
    $this->pdf->SetWidths(array(5,50,60, 15, 60, 15, 60));
    $this->pdf->SetAligns(array('L','L','C','R','C','R','C'));
    $this->pdf->CellBorders=array('','','U','','U','','U');
    $this->row(array('','','Gewichten %','','Rendementen %','','Attributie'));

    $this->pdf->SetWidths(array(5,50,20,20,20, 15, 20,20,20, 15, 20,20,20));
    $this->pdf->SetAligns(array('L','L','R','R','R','R','R','R','R','R','R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders=array('','U','U','U','U','','U','U','U','','U','U','U');

    $this->row(array('','Asset Class','Portfolio','Benchmark','Verschil','','Portfolio','Benchmark','Verschil','','Portfolio','Benchmark','Verschil'));
    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
    $totalen=array();
    foreach ($bovencat as $categorie=>$categorieOmschrijving)
    {
      $attributiePortefeuille=$this->waarden['Periode'][$categorie]['weging']*$this->waarden['Periode'][$categorie]['procent']/100;
      $attributieBenchmark=$this->waarden['Periode'][$categorie]['indexBijdrageWaarde']*$this->waarden['Periode'][$categorie]['indexPerf']/100;
      $attributieVerschil=$attributiePortefeuille-$attributieBenchmark;
	    $this->row(array('',$categorieOmschrijving,
                        $this->formatGetal($this->waarden['Periode'][$categorie]['weging'],2,true),
                        $this->formatGetal($this->waarden['Periode'][$categorie]['indexBijdrageWaarde'],2,true),
                        $this->formatGetal($this->waarden['Periode'][$categorie]['weging']-$this->waarden['Periode'][$categorie]['indexBijdrageWaarde'],2,true),
	                      '',
                        $this->formatGetal($this->waarden['Periode'][$categorie]['procent'],2,true),
	                      $this->formatGetal($this->waarden['Periode'][$categorie]['indexPerf'],2,true),
	                      $this->formatGetal($this->waarden['Periode'][$categorie]['overPerf'],2,true),
                        '',
                        $this->formatGetal($attributiePortefeuille,2,true),
	                      $this->formatGetal($attributieBenchmark,2,true),
                        $this->formatGetal($attributieVerschil,2,true)

          ));
      $totalen['wegingPortefeuille']+=$this->waarden['Periode'][$categorie]['weging'];
      $totalen['wegingBenchmark']+=$this->waarden['Periode'][$categorie]['indexBijdrageWaarde'];
      $totalen['attributiePortefeuille']+=$attributiePortefeuille;
      $totalen['attributieBenchmark']+=$attributieBenchmark;
      $barData[$categorie]['Portefeuille']=$this->waarden['Periode'][$categorie]['weging'];
      $barData[$categorie]['Benchmark']=$this->waarden['Periode'][$categorie]['indexBijdrageWaarde'];
    }
     $this->pdf->CellBorders=array('','T','T','T','T','','T','T','T','','T','T','T');
     $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
     $this->row(array('','Totaal',
                        $this->formatGetal($totalen['wegingPortefeuille'],2,true),
                        $this->formatGetal($totalen['wegingBenchmark'],2,true),
                        '',
	                      '',
	                      '',
	                      '',
	                      '',
                        '',
                        $this->formatGetal($totalen['attributiePortefeuille'],2,true),
	                      $this->formatGetal($totalen['attributieBenchmark'],2,true),
                        $this->formatGetal($totalen['attributiePortefeuille']-$totalen['attributieBenchmark'],2,true)));
     $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
     unset($this->pdf->CellBorders);


     $this->pdf->setXY(22,182);
     $this->pdf->Rect(10,110,175,85);
      $this->VBarDiagramPerf(160,60,$barData,'');
      $colors=array('Portefeuille'=>array(75,179,35),'Benchmark'=>array(113,176,222)); //
      $xval=45;$yval=115;
      foreach($colors as $effect=>$color)
      {
         $this->pdf->Rect($xval, $yval, 3, 3, 'DF',null,$color);
         $this->pdf->SetTextColor(0);
         $this->pdf->SetXY($xval+5, $yval);
         $this->pdf->Cell(50, 3, $effect,0,0,'L');
         $xval+=40;
      }

    $this->pdf->setXY($this->pdf->marge,110);
    $this->pdf->SetWidths(array(205,20,20,20));
    $this->pdf->SetAligns(array('L','L','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders=array('','U','U','U');

   // listarray($this->waarden['Periode']['totaal']['perfWaarden']);

    $this->row(array('','Maand','Portfolio','Benchmark'));
    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
    $stapelItems=array('procent','indexPerf');
    foreach ($stapelItems as $item)
      $totaalData[$item]=0;
    $maand=0;
    foreach($this->waarden['Jaar']['totaal']['perfWaarden'] as $maand=>$maandWaarden)
    {
      $julMaand=db2jul($maand);
      $kwartaal=ceil(date('n',$julMaand)/3);
      $jaar=date('Y',$julMaand);
      $maand=date('m',$julMaand);
      $this->row(array('',$maand."-".$jaar,$this->formatGetal($maandWaarden['procent']*100,2),$this->formatGetal($maandWaarden['indexPerf']*100,2)));

      $kwartaalOmschrijving='Q'.$kwartaal.' '.$jaar;
      foreach ($stapelItems as $item)
      {
          $totaalData[$item]=(($maandWaarden[$item]+1)  * ($totaalData[$item]+1))-1;
          $kwartalen['perfWaarden'][$kwartaal][$item] = (($kwartalen['perfWaarden'][$kwartaal][$item]+1)  * ($maandWaarden[$item]+1))-1;
          $kwartalen['perfWaarden'][$kwartaal]['omschrijving']=$kwartaalOmschrijving;
      }

    }
    if($maand<12)
      for($i=$maand+1;$i<=12;$i++)
       $this->row(array('',sprintf('%02d', $i)."-".$jaar));
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders=array('','T','T','T');
    $this->row(array('','',$this->formatGetal($totaalData['procent']*100,2),$this->formatGetal($totaalData['indexPerf']*100,2)));
    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
    $this->pdf->ln();

    $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders=array('','U','U','U');
    $this->row(array('','Kwartaal','Portfolio','Benchmark'));
    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
    foreach($kwartalen['perfWaarden'] as $kwartaal=>$kwartaalWaarden)
    {
      $this->row(array('',$kwartaalWaarden['omschrijving'],$this->formatGetal($kwartaalWaarden['procent']*100,2),$this->formatGetal($kwartaalWaarden['indexPerf']*100,2)));
    }
    if($kwartaal<4)
      for($i=$kwartaal+1;$i<=4;$i++)
       $this->row(array('','Q'.$i.' '.$jaar));
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders=array('','T','T','T');
    $this->row(array('','',$this->formatGetal($totaalData['procent']*100,2),$this->formatGetal($totaalData['indexPerf']*100,2)));
    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);       
	}
  
  
  function addPerfLine($data)
  {
    $portIndex=1;
    $indexIndex=1;
    foreach($data['perfWaarden'] as $datum=>$perfData)
    {
      $juldate=db2jul($datum);
      $portIndex=(1+$perfData['procent'])*$portIndex;
      $indexIndex=(1+$perfData['indexPerf'])*$indexIndex;
      $perfGrafiek['portefeuille'][]=($portIndex-1)*100;
      $perfGrafiek['$perfData'][]=($indexIndex-1)*100;
      $perfGrafiek['datum'][]= date("M-y",$juldate);
    }
   
    $perfGrafiek['legenda']=array('Portefeuille','Benchmark');
    $this->pdf->setXY(160,120);
    $portKleur=array(120,198,90);
    $indexKleur=array(113,176,222);
    $perfGrafiek['titel']='Rendement';
    $this->LineDiagram(120, 55, $perfGrafiek,array($portKleur,$indexKleur),0,0,6,5);//50

  }
  
  
  function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4)
  {
    global $__appvar;

    $legendDatum= $data['datum'];
    $legendaItems= $data['legenda'];
    $titel=$data['titel'];
    $data1 = $data['specifiekeIndex'];
    $data = $data['portefeuille'];
    

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
    $lDiag = floor($w - $margin * 1 );
    
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Cell($w,0,$titel,0,0,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));

    $this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.2);

    
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

    $bereikdata[]=0;
    if ($maxVal == 0)
    {
      $maxVal = ceil(max($bereikdata));
    }
    if ($minVal == 0)
    {
      $minVal = floor(min($bereikdata));
    }

    $minVal = floor(($minVal-1) * 1.1);
    if($minVal > 0)
      $minVal=0;
    $maxVal = ceil(($maxVal+1) * 1.1);
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / 12;//count($data);

    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
      $xpos = $XDiag + $verInterval * $i;

    $this->pdf->SetFont($this->pdf->rapport_font, '', 7);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);

    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);

    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);

    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $n=0;
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->setXY($XDiag-10, $i);
      $this->pdf->cell(10,4,$this->formatgetal(100-($n*$stapgrootte),2) ."%",0,0,'R');
      //$this->pdf->Text($XDiag-10, $i, 100-($n*$stapgrootte) ." %");
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
      {
        $this->pdf->setXY($XDiag-10, $i);
        $this->pdf->cell(10,4,$this->formatgetal(100-($n*$stapgrootte),2) ."%",0,0,'R');
      }

      $n++;
      if($n >20)
         break;
    }
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    $jaren=ceil(count($data)/12);
    for ($i=0; $i<count($data); $i++)
    {
      if($i%$jaren==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$i],25);
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      
     // if ($i>0)
     // {
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
    //  }

      $yval = $yval2;
    }
    
    if(is_array($data1))
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color1);

      for ($i=0; $i<count($data1); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
        
        //if ($i>0)
        //{
          $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        //}
        //$yval = $yval2;
      }
    }


    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.2,'cap' => 'butt'));
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
  
  function VBarDiagramPerf($w, $h, $data, $format, $color=null,$nbDiv=4,$numBars=0)
  {
      global $__appvar;
      $legendDatum = $data['datum'];
      //$data = $data['portefeuille'];
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      //$this->pdf->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
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
        $this->pdf->setXY($XstartGrafiek-10, $i);
        $this->pdf->cell(10,4,$this->formatGetal($n*$stapgrootte,2)." %",0,0,'R');

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
        {
         // $this->pdf->Text($XstartGrafiek - 10, $i, $this->formatGetal($n * $stapgrootte, 2) . " %");
          $this->pdf->setXY($XstartGrafiek-10, $i);
          $this->pdf->cell(10,4,$this->formatGetal($n*$stapgrootte,2)." %",0,0,'R');
        }
        $n++;
        if($n >20)
          break;
      }
      
      $numBars=count($data);
      if($numBars > 0)
        $this->pdf->NbVal=$numBars;

         $colors=array('Portefeuille'=>array(75,179,35),'Benchmark'=>array(113,176,222)); //


      $vBar = ($bGrafiek / ($this->pdf->NbVal ))/3; //4
      $bGrafiek = $vBar * ($this->pdf->NbVal );
      $eBaton = ($vBar * 80 / 100);
      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;
      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      foreach($data as $xBeschrijving=>$maandData)
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
          $this->pdf->SetTextColor(0,0,0);
          if($eBaton > 4)//abs($hval) > 3 &&
          {
            $this->pdf->SetXY($xval, $yval+$hval-4);//($hval/2)
            $this->pdf->Cell($eBaton, 4, number_format($val,2,',','.'),0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);
          $i++;
          }
          $i++;
       $this->pdf->TextWithRotation($XstartGrafiek + ($i -2) * $vBar - $eBaton / 2,$YstartGrafiek +8 ,$xBeschrijving,15);
          
          
      }
  }
}
?>
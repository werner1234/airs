<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/04/12 11:49:05 $
File Versie					: $Revision: 1.6 $

$Log: RapportKERNZ_L35.php,v $
Revision 1.6  2020/04/12 11:49:05  rvv
*** empty log message ***

Revision 1.4  2017/11/18 18:58:17  rvv
*** empty log message ***

Revision 1.3  2017/11/05 13:37:27  rvv
*** empty log message ***

Revision 1.2  2017/11/04 17:40:21  rvv
*** empty log message ***

Revision 1.1  2017/10/21 17:33:13  rvv
*** empty log message ***

Revision 1.3  2017/04/09 10:13:59  rvv
*** empty log message ***

Revision 1.2  2016/06/08 15:40:53  rvv
*** empty log message ***

Revision 1.1  2016/05/22 18:49:26  rvv
*** empty log message ***

Revision 1.6  2016/02/20 15:18:29  rvv
*** empty log message ***

Revision 1.5  2015/12/02 16:16:29  rvv
*** empty log message ***

Revision 1.4  2015/11/29 13:14:46  rvv
*** empty log message ***

Revision 1.3  2015/11/25 16:56:13  rvv
*** empty log message ***

Revision 1.2  2015/03/04 16:30:29  rvv
*** empty log message ***

Revision 1.22  2014/12/31 18:09:06  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");


class RapportKERNZ_L35
{

	function RapportKERNZ_L35($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "KERNZ";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Performancemeting over de categorieën";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->titelOmschrijving=array();
    /*
    if(substr($this->rapportageDatumVanaf,0,4) <> substr($this->rapportageDatum,0,4))
    {
      echo "Begin en eindatum liggen niet in hetzelfde jaar";
      exit;
    }
*/
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }



	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$DB = new DB();

		// voor data
		$this->pdf->widthA = array(5,80,30,5,30,5,30,120);
		$this->pdf->alignA = array('L','L','R','L','R');

		// voor kopjes
		$this->pdf->widthB = array(0,85,30,5,30,5,30,120);
		$this->pdf->alignB = array('L','L','R','L','R');


		$this->pdf->AddPage();
    $this->pdf->templateVars['KERNZPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['KERNZPaginas']=$this->pdf->rapport_titel;

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);


    $portefeuilles[]=$this->portefeuille;

    if (is_array($this->pdf->portefeuilles) && count($this->pdf->portefeuilles) > 0)
    {
       foreach($this->pdf->portefeuilles as $portefeuille)
         $portefeuilles[]=$portefeuille;
    }


    if(substr($this->portefeuille,0,2)=='C_')
      $this->titelOmschrijving[$this->portefeuille]='Geconsolideerd';

    if(is_array($this->pdf->portefeuilles))
    {
      $query="SELECT Portefeuille,Depotbanken.omschrijving,Portefeuilles.ClientVermogensbeheerder FROM Depotbanken JOIN Portefeuilles ON Portefeuilles.Depotbank=Depotbanken.Depotbank WHERE Portefeuilles.Portefeuille IN('".implode("','",$this->pdf->portefeuilles)."')";
      $DB->SQL($query);
      $DB->Query();
      while($portefeuille = $DB->NextRecord())
      {
        $this->titelOmschrijving[$portefeuille['Portefeuille']]=$portefeuille['omschrijving'];//$this->getCRMnaam($portefeuille['Portefeuille']);
      }
    }
    
    $standaardMaanden=array();
    $jaar=substr($this->rapportageDatum,0,4);
    for($i=2;$i<14;$i++)
    {
      $time=mktime(0,0,0,$i,0,$jaar);
      if($time<$this->pdf->rapport_datum)
      {
        $standaardMaanden[date('Y-m-d',$time)]=100;
      }
    }
    $startJaar=mktime(0,0,0,1,1,$jaar);
    $rendementenTabel=array();
    $rendementenGrafiek=array();
    foreach($portefeuilles as $portefeuille)
    {
      $categorien=array();
      $stdev = new rapportSDberekening($portefeuille, $this->rapportageDatum);
      $stdev->settings['SdFrequentie'] = 'm';
      $stdev->setStartdatum($this->rapportageDatumVanaf);
      unset($stdev->noTotaal);
      $stdev->addReeks('totaal');

      $stdev->addReeks('hoofdCategorie');
      foreach($stdev->reeksen as $categorie=>$categorieWaarden)
      {
        $rendementenTabel[$categorie]['portefeuilles'][$portefeuille]=$standaardMaanden;
        $indexTotaal=100;
        $indexTabel=100;
        foreach($categorieWaarden as $datum=>$totaalWaarden)
        {
          $datumJul=db2jul($datum);

          if($datumJul >=$startJaar)
          {
            $indexTabel = ($indexTabel / 100) * (1 + $totaalWaarden['perf'] / 100) * 100;
            $rendementenTabel[$categorie]['portefeuilles'][$portefeuille][$datum] = $indexTabel;
          }
          $indexTotaal = ($indexTotaal / 100) * (1 + $totaalWaarden['perf'] / 100) * 100;
          $rendementenGrafiek[$categorie]['portefeuilles'][$portefeuille][$datum] = $indexTotaal;
        }
      $categorien[$categorie]=$categorie;
      }

      $DB = new DB();
      foreach($categorien as $categorie)
      {
        if($categorie=='totaal')
        {
      $query = "SELECT SpecifiekeIndex,Omschrijving,Kleurcode FROM Portefeuilles LEFT JOIN Fondsen ON Portefeuilles.SpecifiekeIndex=Fondsen.Fonds 
            WHERE Portefeuilles.Portefeuille='" . $portefeuille . "'";
      $DB->SQL($query);
      $benchmark = $DB->lookupRecord();
 
      $this->kleurcodes[$portefeuille]=unserialize($benchmark['Kleurcode']);
        }
        else
        {

          $query = "SELECT
IndexPerBeleggingscategorie.Fonds as SpecifiekeIndex,
Fondsen.Omschrijving,
IndexPerBeleggingscategorie.Beleggingscategorie,
BeleggingscategoriePerFonds.grafiekKleur
FROM
IndexPerBeleggingscategorie
JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds=Fondsen.Fonds
INNER JOIN BeleggingscategoriePerFonds ON IndexPerBeleggingscategorie.Fonds = BeleggingscategoriePerFonds.Fonds AND IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
WHERE IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND 
(IndexPerBeleggingscategorie.Portefeuille='' OR IndexPerBeleggingscategorie.Portefeuille='" . $portefeuille . "') AND 
IndexPerBeleggingscategorie.Beleggingscategorie='$categorie'
ORDER BY IndexPerBeleggingscategorie.Portefeuille desc limit 1";
          $DB->SQL($query);
          $benchmark = $DB->lookupRecord();

          $tmp=unserialize($benchmark['Kleurcode']);
          $this->kleurcodes[$benchmark['SpecifiekeIndex']] = array($tmp['R']['value'],$tmp['G']['value'],$tmp['B']['value']) ;

        }

      if(isset($benchmark['SpecifiekeIndex']) && $benchmark['SpecifiekeIndex']<>'')
      {
          unset($stdev->reeksen['benchmark']);
          $rendementenTabel[$categorie]['rendementen'][$benchmark['Omschrijving']] = $standaardMaanden;
        $stdev->addReeks('benchmark', $benchmark['SpecifiekeIndex']);
        $indexTabel=100;
        $indexTotaal=100;
      }
      foreach($stdev->reeksen['benchmark'] as $datum=>$totaalWaarden)
      {
        $datumJul=db2jul($datum);
        if($datumJul >=$startJaar)
        {
          $indexTabel = ($indexTabel / 100) * (1 + $totaalWaarden['perf'] / 100) * 100;
          $rendementenTabel[$categorie]['benchmarks'][$benchmark['Omschrijving']][$datum] = $indexTabel;
        }
        $indexTotaal = ($indexTotaal / 100) * (1 + $totaalWaarden['perf'] / 100) * 100;
        $rendementenGrafiek[$categorie]['benchmarks'][$benchmark['Omschrijving']][$datum] = $indexTotaal;
      }

    }
}
    $this->toonTabel($rendementenTabel);
    $this->toonGrafieken($rendementenGrafiek);


		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();


	}

  function toonGrafieken($rendementenGrafiek)
  {


$portKleur=array($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
$indexKleur=array(87,165,25);


    $categorien=array('totaal'=>'Portefeuille rendementen','ZAK'=>'Rendement zakelijke waarden','VAR'=>'Rendement vastrentendewaarden');

    $perfGrafiek=array();
    foreach($categorien as $categorie=>$titel)
    {
      $perfGrafiek[$categorie]['titel']=$titel;
      $perfGrafiek[$categorie]['legenda']=array();
      foreach($rendementenGrafiek[$categorie]['portefeuilles'][$this->portefeuille] as $datum=>$perf)
        $perfGrafiek[$categorie]['datum'][]= date("M",db2jul($datum));

      foreach($rendementenGrafiek[$categorie]['portefeuilles'] as $portefeuille=>$maandWaarden)
      {
        $perfGrafiek[$categorie]['legenda'][$portefeuille]=$portefeuille;
        $perfGrafiek[$categorie]['data'][$portefeuille]=array_values($maandWaarden);
      }

      foreach($rendementenGrafiek[$categorie]['benchmarks'] as $benchmark=>$maandWaarden)
      {
        $perfGrafiek[$categorie]['legenda'][$benchmark]=$benchmark;
        $perfGrafiek[$categorie]['data'][$benchmark]=array_values($maandWaarden);
      }

    }
    $this->pdf->addPage();
    $x=40;//215
    $w=200;//75

    $this->pdf->setXY($x,45);
    $this->LineDiagram($w, 30, $perfGrafiek['totaal'],array($portKleur,$indexKleur),0,0,6,5);//50
    $this->pdf->setXY($x,95);
    $this->LineDiagram($w, 30, $perfGrafiek['ZAK'],array($portKleur,$indexKleur),0,0,6,5);//50
    $this->pdf->setXY($x,145);
    $this->LineDiagram($w, 30, $perfGrafiek['VAR'],array($portKleur,$indexKleur),0,0,6,5);//50

  }

  function buildTableRow($titel,$maandWaarden)
  {
    if(isset($this->titelOmschrijving[$titel]))
      $titel=$this->titelOmschrijving[$titel];
    $tmpRow=array($titel,'100,0');
    $tmpRowXls=$tmpRow;
    foreach($maandWaarden as $index)
    {
      $tmpRow[] = $this->formatGetal($index, 1);
      $tmpRowXls[] = round($index,1);
    }
    while(count($tmpRow)<14)
    {
      $tmpRow[] = '';
      $tmpRowXls[] = '';
    }
    $this->pdf->excelData[]=$tmpRowXls;
    return $tmpRow;
  }

  function toonTabel($rendementenTabel)
  {
    $this->pdf->setXY($this->pdf->marge,50);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $mw=14;
    $this->pdf->setWidths(array(50,$mw,$mw,$mw,$mw,$mw,$mw,$mw,$mw,$mw,$mw,$mw,$mw,$mw));
    $this->pdf->setAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R','R'));
    $this->pdf->CellBorders=array(array('L','T','R','U'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'));
    $this->pdf->multicell(80,4,'Portefeuille rendementen','','L',false);
    $this->pdf->excelData[]=array('Portefeuille rendementen');
    $this->pdf->row(array('Portefeuille','1-jan','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec'));
    $this->pdf->excelData[]=array('Portefeuille','1-jan','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
   // listarray($rendementenTabel);
    foreach($rendementenTabel['totaal']['portefeuilles'] as $portefeuille=>$maandWaarden)
    {
      if($portefeuille==$this->portefeuille)
        $this->pdf->row($this->buildTableRow($portefeuille,$maandWaarden));
    }
    foreach($rendementenTabel['totaal']['benchmarks'] as $benchmark=>$maandWaarden)
    {
      $this->pdf->row($this->buildTableRow($benchmark,$maandWaarden));
    }
    $this->pdf->ln();
    foreach($rendementenTabel['totaal']['portefeuilles'] as $portefeuille=>$maandWaarden)
    {
      if($portefeuille<>$this->portefeuille)
        $this->pdf->row($this->buildTableRow($portefeuille,$maandWaarden));
    }



    $this->pdf->ln();
    $this->pdf->excelData[]=array();
    $this->pdf->multicell(80,4,'Rendementen zakelijke waarden','','L',false);
    $this->pdf->excelData[]=array('Rendementen zakelijke waarden');
    $this->pdf->row(array('Portefeuille','1-jan','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec'));
    $this->pdf->excelData[]=array('Portefeuille','1-jan','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
    foreach($rendementenTabel['ZAK']['portefeuilles'] as $portefeuille=>$maandWaarden)
    {
      if($portefeuille==$this->portefeuille)
        $this->pdf->row($this->buildTableRow($portefeuille,$maandWaarden));
    }
    foreach($rendementenTabel['ZAK']['benchmarks'] as $benchmark=>$maandWaarden)
    {
      $this->pdf->row($this->buildTableRow($benchmark,$maandWaarden));
    }
    $this->pdf->ln();
    foreach($rendementenTabel['ZAK']['portefeuilles'] as $portefeuille=>$maandWaarden)
    {
      if($portefeuille<>$this->portefeuille)
        $this->pdf->row($this->buildTableRow($portefeuille,$maandWaarden));
    }

    $this->pdf->ln();
    $this->pdf->excelData[]=array();
    $this->pdf->multicell(80,4,'Rendementen vastrentende waarden','','L',false);
    $this->pdf->excelData[]=array('Rendementen vastrentende waarden');
    $this->pdf->row(array('Portefeuille','1-jan','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec'));
    $this->pdf->excelData[]=array('Portefeuille','1-jan','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
    foreach($rendementenTabel['VAR']['portefeuilles'] as $portefeuille=>$maandWaarden)
    {
      if($portefeuille==$this->portefeuille)
        $this->pdf->row($this->buildTableRow($portefeuille,$maandWaarden));
    }
    foreach($rendementenTabel['VAR']['benchmarks'] as $benchmark=>$maandWaarden)
    {
      $this->pdf->row($this->buildTableRow($benchmark,$maandWaarden));
    }
    $this->pdf->ln();
    foreach($rendementenTabel['VAR']['portefeuilles'] as $portefeuille=>$maandWaarden)
    {
      if($portefeuille<>$this->portefeuille)
        $this->pdf->row($this->buildTableRow($portefeuille,$maandWaarden));
    }
  }
  


  function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4)
  {
    global $__appvar;

    $legendDatum= $data['datum'];
    $legendaItems= $data['legenda'];
    $titel=$data['titel'];

    $bereikdata=array();
    foreach($data['data'] as $grafiek=>$grafiekData)
    {
      if (count($grafiekData) > 0)
      {
        $bereikdata = array_merge($bereikdata, $grafiekData);
      }
    }


    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );
    
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->setXY($XPage,$YPage-3);
    $this->pdf->Cell($w,0,"  ".$titel,0,0,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));

    $this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));

    //if($color == null)
      $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.2);

    
    //$this->pdf->SetFillColor($color[0],$color[1],$color[2]);

    if ($maxVal == 0)
    {
      $maxVal = ceil(max($bereikdata));
    }
    if ($minVal == 0)
    {
      $minVal = floor(min($bereikdata));
    }

    $minVal = floor(($minVal-1) *1);
    $maxVal = ceil(($maxVal+1) * 1);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / count($legendDatum);
    $bereik=$maxVal-$minVal;

    $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);

    $stapgrootte = ceil(abs($bereik)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);

    $top = $YPage;

    $absUnit =abs($unith);
    $nulpunt = $YDiag + (($bereik) * $waardeCorrectie);
    $n=0;

    for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-8, $i, ($n*$stapgrootte)+$minVal ." %");

      $n++;
      if($n >20)
         break;
    }



    foreach($data['data'] as $grafiek=>$grafiekData)
    {
      if(isset($this->kleurcodes[$grafiek]) && ($this->kleurcodes[$grafiek][0] <> 0 || $this->kleurcodes[$grafiek][1] <> 0 || $this->kleurcodes[$grafiek][2] <> 0))
      {
        $color = $this->kleurcodes[$grafiek];
      }
      else
      {
        $color=array(rand(0,255),rand(0,255),rand(0,255));
        $this->kleurcodes[$grafiek]=$color;
      }

      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);


      $yval = $YDiag + (($maxVal-100) * $waardeCorrectie) ;
      for ($i=0; $i<count($grafiekData); $i++)
      {
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$i],25);
        $yval2 = $YDiag + (($maxVal-$grafiekData[$i]) * $waardeCorrectie) ;
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        $yval = $yval2;
      }
    }



    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.2,'cap' => 'butt'));
    $items=count($data['data']);
    $nStep=$w/$items;
    $minWidth=20;
    $itemsPerRow=$items;
   // echo " if($nStep < $minWidth) | <br>\n";
    if($nStep < $minWidth)
    {
      $rows=ceil($items/($w/$minWidth));
  //    echo "|  $rows=ceil($items/($w/$minWidth)); |<br>\n ";
      $itemsPerRow=ceil($items/$rows);
      $nStep=$w/$itemsPerRow;
//        echo "  $itemsPerRow=$items/$rows <br>\n";
    }
    $n=0;
    $extraH=10;
   // echo "$w | $n%$itemsPerRow";exit;
    foreach ($legendaItems as $index=>$item)
    {
      if(isset($this->kleurcodes[$index]))
      {
        $kleur = $this->kleurcodes[$index];
      }
      if(isset($this->titelOmschrijving[$item]))
        $titel=$this->titelOmschrijving[$item];
      else
        $titel=$item;

      if($n>0 && $n%$itemsPerRow==0)
      {
        $extraH += 4;
        $step=0;
      }
    $this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
    $this->pdf->Rect($XPage+$step, $YPage+$h+$extraH, 3, 3, 'DF','',$kleur);
    $this->pdf->SetXY($XPage+3+$step,$YPage+$h+$extraH);
    $this->pdf->Cell(0,3,$titel);
    $step+=$nStep;
      $n++;
    }
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
  }
 
  
}
?>
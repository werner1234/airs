<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/11/18 18:58:17 $
File Versie					: $Revision: 1.7 $

$Log: RapportKERNV_L35.php,v $
Revision 1.7  2017/11/18 18:58:17  rvv
*** empty log message ***

Revision 1.6  2017/11/08 17:12:56  rvv
*** empty log message ***

Revision 1.5  2017/11/05 13:49:37  rvv
*** empty log message ***

Revision 1.4  2017/11/05 13:37:27  rvv
*** empty log message ***

Revision 1.3  2017/11/04 17:40:21  rvv
*** empty log message ***

Revision 1.2  2017/10/22 14:14:46  rvv
*** empty log message ***

Revision 1.1  2017/10/22 11:11:15  rvv
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


class RapportKERNV_L35
{

	function RapportKERNV_L35($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "KERNV";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Performancemeting over de categorieën";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->titelOmschrijving=array();
    $this->benchmarks=array();
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
    $this->pdf->templateVars['KERNVPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['KERNVPaginas']=$this->pdf->rapport_titel;

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
    $startJaar=mktime(0,0,0,1,1,$jaar);
    $volgendJaar=mktime(0,0,0,1,1,$jaar+1);

    for($i=2;$i<14;$i++)
    {
      $time=mktime(0,0,0,$i,0,$jaar);
      if($time<=$this->pdf->rapport_datum)
      {
        if(date("m-Y",$this->pdf->rapport_datum)==date("m-Y",$time))
          $standaardMaanden[date('Y-m-d',$this->pdf->rapport_datum)]=0;
        else
          $standaardMaanden[date('Y-m-d',$time)]=0;
      }
      elseif($time<$volgendJaar)
      {
        if(date("m-Y",$this->pdf->rapport_datum)==date("m-Y",$time))
          $standaardMaanden[date('Y-m-d',$this->pdf->rapport_datum)]='';
        else
          $standaardMaanden[date('Y-m-d',$time)]='';
      }
    }
    $standaardMaanden['rankingMaand']='';

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
        $rendementenTabel[$categorie]['rendementen'][$portefeuille]=$standaardMaanden;
        $indexTotaal=100;
        $indexTabel=100;
        foreach($categorieWaarden as $datum=>$totaalWaarden)
        {
          $datumJul=db2jul($datum);

          if($datumJul >=$startJaar)
          {
            $indexTabel = ($indexTabel / 100) * (1 + $totaalWaarden['perf'] / 100) * 100;
            $rendementenTabel[$categorie]['rendementen'][$portefeuille][$datum] = $totaalWaarden['perf'];
            if($datum==$this->rapportageDatum)
              $rendementenTabel[$categorie]['rendementen'][$portefeuille]['rankingMaand'] = $totaalWaarden['perf'];

          }

          $rendementenGrafiek[$categorie][$datum][$portefeuille] = $totaalWaarden['perf'];
        }
        $rendementenTabel[$categorie]['rendementen'][$portefeuille]['cumulatief'] = $indexTabel-100;
        $rendementenTabel[$categorie]['rendementen'][$portefeuille]['rankingCumulatief'] = $indexTabel-100;
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
          $this->kleurcodes[$portefeuille] = unserialize($benchmark['Kleurcode']);
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

        if (isset($benchmark['SpecifiekeIndex']) && $benchmark['SpecifiekeIndex'] <> '')
        {
          $this->benchmarks[$benchmark['SpecifiekeIndex']]=$benchmark['Omschrijving'];
          unset($stdev->reeksen['benchmark']);
          $rendementenTabel[$categorie]['rendementen'][$benchmark['Omschrijving']] = $standaardMaanden;
          $stdev->addReeks('benchmark', $benchmark['SpecifiekeIndex']);
          $indexTabel = 100;
          $indexTotaal = 100;
        }
        foreach ($stdev->reeksen['benchmark'] as $datum => $totaalWaarden)
        {
          $datumJul = db2jul($datum);
          if ($datumJul >= $startJaar)
          {
            $indexTabel = ($indexTabel / 100) * (1 + $totaalWaarden['perf'] / 100) * 100;
            $rendementenTabel[$categorie]['rendementen'][$benchmark['Omschrijving']][$datum] = $totaalWaarden['perf'];
            if ($datum == $this->rapportageDatum)
            {
              $rendementenTabel[$categorie]['rendementen'][$benchmark['Omschrijving']]['rankingMaand'] = $totaalWaarden['perf'];
            }
          }
          $indexTotaal = ($indexTotaal / 100) * (1 + $totaalWaarden['perf'] / 100) * 100;
          $rendementenGrafiek[$categorie][$datum][$benchmark['Omschrijving']] = $totaalWaarden['perf'];
        }
        $rendementenTabel[$categorie]['rendementen'][$benchmark['Omschrijving']]['cumulatief'] = $indexTabel - 100;
        $rendementenTabel[$categorie]['rendementen'][$benchmark['Omschrijving']]['rankingCumulatief'] = $indexTabel - 100;
      }
    }
//listarray($rendementenTabel);
   //
    foreach($rendementenTabel as $categorie=>$categorieWaarden)
    {
      foreach($categorieWaarden as $rendementKey=>$portefeuilleData)
      {
        foreach($portefeuilleData as $portefeuille=>$rendementsWaarden)
        {
          foreach($rendementsWaarden as $datum=>$rendement)
          {
            $rendementenTabel[$categorie]['ranking'][$portefeuille][$datum] = '';
            if($rendement!=='')
              $ranking[$categorie][$datum][$portefeuille]=$rendement;
          }
        }
      }
    }
    foreach($ranking as $categorie=>$datumData)
    {
      foreach ($datumData as $datum => $portefeuilleData)
      {
        arsort($portefeuilleData, SORT_NUMERIC);
        $i=1;
        foreach ($portefeuilleData as $portefeuille => $rendement)
        {
          $rendementenTabel[$categorie]['ranking'][$portefeuille][$datum] = $i;
          if($datum=='rankingMaand'||$datum=='rankingCumulatief')
          {
            unset($rendementenTabel[$categorie]['ranking'][$portefeuille][$datum]);
            $rendementenTabel[$categorie]['rendementen'][$portefeuille][$datum] = $i;

          }
          $i++;
          unset($rendementenTabel[$categorie]['ranking'][$portefeuille]['cumulatief']);
          unset($rendementenTabel[$categorie]['ranking'][$portefeuille]['rankingMaand']);

        }



      }
    }
    //listarray( $this->kleurcodes);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();


    $this->toonTabelen($rendementenTabel);
    $this->toonGrafieken($rendementenGrafiek);
    $this->pdf->CellBorders = array();
	}

  function toonGrafieken($rendementenGrafiek)
  {

//listarray($rendementenGrafiek);
    $this->pdf->addPage();
    $x=40;//215
    $w=200;//75
    $this->pdf->setXY($x,75);
    $this->VBarDiagram2($w,30,$rendementenGrafiek['totaal'],'');
    $this->pdf->setXY($x,125);
    $this->VBarDiagram2($w,30,$rendementenGrafiek['ZAK'],'');
    $this->pdf->setXY($x,175);
    $this->VBarDiagram2($w,30,$rendementenGrafiek['VAR'],'');

  }

  function buildRendementRow($titel,$maandWaarden)
  {
    if(isset($this->titelOmschrijving[$titel]))
      $titel=$this->titelOmschrijving[$titel];
    $tmpRow=array($titel);
 //   listarray($maandWaarden);
    $tmpRowXls=$tmpRow;
    foreach($maandWaarden as $maand=>$waarde)
    {
      if($waarde!=='')
      {
        if(substr($maand,0,7)=='ranking')
        {
          $number = $this->formatGetal($waarde, 0);
          $numberXls = round($waarde);
        }
        else
        {
          $number = $this->formatGetal($waarde, 2);
          $numberXls = round($waarde,2);
        }
      }
      else
      {
        $number = '';
        $numberXls = '';
      }
      $tmpRow[] = $number;
      $tmpRowXls[]=$numberXls;
    }
    while(count($tmpRow)<13)
    {
      $tmpRow[] = '';
      $tmpRowXls[]='';
    }
    $this->pdf->excelData[]=$tmpRowXls;

     return $tmpRow;
  }

  function buildRatingRow($titel,$maandWaarden)
  {
    if(isset($this->titelOmschrijving[$titel]))
      $titel=$this->titelOmschrijving[$titel];
    $tmpRow=array($titel);
    $rowTotal=0;
   // listarray($maandWaarden);
    $tmpRowXls=$tmpRow;
    foreach($maandWaarden as $index)
    {
      if($index!=='')
      {
        $number = $this->formatGetal($index, 0);
        $numberXls = round($index);
      }
      else
      {
        $number = '';
        $numberXls ='';
      }
      $tmpRow[] = $number;
      $tmpRowXls[] = $numberXls;
      $rowTotal+=$index;
    }
    while(count($tmpRow)<13)
    {
      $tmpRow[] = '';
      $tmpRowXls[]='';
    }
    $tmpRow[]=$this->formatGetal($rowTotal,0);
    $tmpRowXls[]=round($rowTotal);

    $this->pdf->excelData[]=$tmpRowXls;

    return $tmpRow;
  }

  function printRendementHeader($titel)
  {
    $mw=12;
    $this->pdf->setWidths(array(50,$mw,$mw,$mw,$mw,$mw,$mw,$mw,$mw,$mw,$mw,$mw,$mw,15,15,21));
    $this->pdf->setAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
    $this->pdf->CellBorders=array(array('L','T','R','U'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'));
    $this->pdf->multicell(80,4,$titel,'','L',false);
    $this->pdf->excelData[]=array($titel);
    $this->pdf->row(array('Portefeuille','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec',"Maand\nRanking","2017","Cumulatieve\nRanking"));
    $this->pdf->excelData[]=array('Portefeuille','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec',"Maand Ranking","2017","Cumulatieve Ranking");
  }
  function printRatingHeader($titel)
  {
    $mw=12;
    $this->pdf->setWidths(array(50,$mw,$mw,$mw,$mw,$mw,$mw,$mw,$mw,$mw,$mw,$mw,$mw,15));
    $this->pdf->setAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R','R'));
    $this->pdf->CellBorders=array(array('L','T','R','U'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'),array('T','U','R'));
    $this->pdf->multicell(80,4,$titel,'','L',false);
    $this->pdf->excelData[]=array($titel);
    $this->pdf->row(array('Portefeuille','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec',"Totaal"));
    $this->pdf->excelData[]=array('Portefeuille','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec',"Totaal");
  }

  function toonTabelen($rendementenTabel)
  {
    $this->pdf->setXY($this->pdf->marge,40);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    //$rowHeightBackup=$this->pdf->rowHeight;
   // $this->pdf->rowHeight=3.4;

   // listarray($rendementenTabel);
    $categorien=array('totaal'=>'portefeuilles','ZAK'=>'zakelijke waarden','VAR'=>'vastrentende waarden');

    foreach($categorien as $categorie=>$omschrijving)
    {
      if((count($rendementenTabel[$categorie]['rendementen'])+count($rendementenTabel[$categorie]['ranking'])+9)*$this->pdf->rowHeight+$this->pdf->getY()>$this->pdf->PageBreakTrigger)
        $this->pdf->addPage();
      $this->pdf->ln();
      $this->pdf->excelData[]=array();
      $this->printRendementHeader('Rendementen '.$omschrijving);
      foreach($rendementenTabel[$categorie]['rendementen'] as $portefeuille=>$maandWaarden)
      {
        if($portefeuille==$this->portefeuille || in_array($portefeuille,$this->benchmarks))
          $this->pdf->row($this->buildRendementRow($portefeuille, $maandWaarden));
      }
      $this->pdf->ln();
      $this->pdf->excelData[]=array();

      foreach($rendementenTabel[$categorie]['rendementen'] as $portefeuille=>$maandWaarden)
        if($portefeuille!=$this->portefeuille && !in_array($portefeuille,$this->benchmarks))
          $this->pdf->row($this->buildRendementRow($portefeuille,$maandWaarden));


      $this->pdf->ln(1);
      $this->printRatingHeader('Ranking '.$omschrijving);
      foreach($rendementenTabel[$categorie]['ranking'] as $portefeuille=>$maandWaarden)
      {
        if($portefeuille==$this->portefeuille || in_array($portefeuille,$this->benchmarks))
          $this->pdf->row($this->buildRatingRow($portefeuille, $maandWaarden));
      }
      $this->pdf->ln();
      $this->pdf->excelData[]=array();
      foreach($rendementenTabel[$categorie]['ranking'] as $portefeuille=>$maandWaarden)
        if($portefeuille!=$this->portefeuille && !in_array($portefeuille,$this->benchmarks))
          $this->pdf->row($this->buildRatingRow($portefeuille,$maandWaarden));
    }


//   $this->pdf->rowHeight=$rowHeightBackup;
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
    $legendaItems=array();
    $maxCols=1;
    foreach($data as $maand=>$maandData)
    {
      $maanden[$maand]=$maand;
      $maxCols=max($maxCols,count($maandData));
      foreach($maandData as $type=>$waarde)
      {
        //if($waarde!=="")
       // {
          $legendaItems[$type]=$type;
          if($waarde > $maxVal)
            $maxVal = $waarde;
          if($waarde < $minVal)
            $minVal = $waarde;
       // }

      }
     // echo "$maand|$waarde|$minVal| <br>\n ";
    }
   // echo "<br>\n| $minVal | $maxVal | <br>\n";exit;
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
    $top = $YstartGrafiek-$hGrafiek;
    $bodem = $YstartGrafiek;
    $absUnit =abs($unit);

    $nulpunt = $YstartGrafiek + $nulYpos;
    $n=0;

    for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XstartGrafiek-7, $i, ($n*$stapgrootte*-1)." %");
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
        $this->pdf->Text($XstartGrafiek-7, $i, ($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
        break;
    }

    $numBars=count($data);
    if($numBars > 0)
      $this->pdf->NbVal=$numBars;


    $vBar = ($bGrafiek / ($this->pdf->NbVal ))/($maxCols+1); //4
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

        if(isset($this->kleurcodes[$type]) && ($this->kleurcodes[$type][0] <> 0 || $this->kleurcodes[$type][1] <> 0 || $this->kleurcodes[$type][2] <> 0))
        {
          $color = $this->kleurcodes[$type];
        }
        else
        {
          $color=array(rand(0,255),rand(0,255),rand(0,255));
          $this->kleurcodes[$type]=$color;
        }

       // $color=$colors[$type];
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



    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.2,'cap' => 'butt'));
    $nStep=$w/$maxCols;
    $minWidth=20;
    $itemsPerRow=$maxCols;
    if($nStep < $minWidth)
    {
      $rows=ceil($maxCols/($w/$minWidth));
      $itemsPerRow=ceil($maxCols/$rows);
      $nStep=$w/$itemsPerRow;
    }
    $n=0;
    $extraH=5;
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
      $this->pdf->Rect($XPage+$step, $YPage+$extraH, 3, 3, 'DF','',$kleur);
      $this->pdf->SetXY($XPage+3+$step,$YPage+$extraH);
      $this->pdf->Cell(0,3,$titel);
      $step+=$nStep;
      $n++;
    }
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);



    // $color=array(155,155,155);
    // $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
  }
 
  
}
?>
<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/06/03 15:41:21 $
 		File Versie					: $Revision: 1.19 $

 		$Log: RapportCASHY_L75.php,v $
 		Revision 1.19  2020/06/03 15:41:21  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2020/04/15 07:19:46  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2020/04/08 15:42:42  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2020/03/11 15:18:12  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2020/02/15 18:29:05  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2019/11/23 18:37:04  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2019/10/26 16:07:18  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2019/09/25 15:31:35  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2019/09/21 16:31:25  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2019/07/24 15:48:45  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2019/07/20 16:28:44  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2019/05/25 16:22:07  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2018/10/31 17:23:34  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2018/10/06 17:20:57  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/06/16 17:42:56  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2018/06/09 15:58:54  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2018/05/19 16:24:53  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2018/03/31 18:06:01  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2018/02/28 16:48:45  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2018/01/21 09:00:44  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/01/13 19:10:28  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2012/09/23 08:51:44  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2012/04/14 16:51:17  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2012/03/25 13:27:46  rvv
 		*** empty log message ***

 		Revision 1.1  2012/03/11 17:19:57  rvv
 		*** empty log message ***

 		Revision 1.2  2012/03/04 11:39:58  rvv
 		*** empty log message ***

 		Revision 1.1  2012/02/29 16:52:49  rvv
 		*** empty log message ***

 		Revision 1.10  2012/02/26 15:17:43  rvv
 		*** empty log message ***

 		Revision 1.9  2012/01/04 16:28:38  rvv
 		*** empty log message ***

 		Revision 1.8  2011/12/07 19:14:53  rvv
 		*** empty log message ***

 		Revision 1.7  2011/09/14 09:26:56  rvv
 		*** empty log message ***

 		Revision 1.6  2011/09/03 14:30:20  rvv
 		*** empty log message ***

 		Revision 1.5  2011/07/03 06:42:47  rvv
 		*** empty log message ***

 		Revision 1.4  2011/06/15 16:14:39  rvv
 		*** empty log message ***

 		Revision 1.3  2011/06/13 14:41:56  rvv
 		*** empty log message ***

 		Revision 1.2  2011/06/02 15:05:05  rvv
 		*** empty log message ***

 		Revision 1.1  2011/05/29 06:38:42  rvv
 		*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");

//ini_set('max_execution_time',60);
class RapportCASHY_L75
{
	function RapportCASHY_L75($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "CASHY";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = vertaalTekst("Cashflow prognose per" ,$this->pdf->rapport_taal).' '.date('d-m-Y',$this->pdf->rapport_datum);
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
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
	  $this->pdf->SetWidths(array(10,25,25,25,40,20,20));
		$this->pdf->SetAligns(array('L','L','R','R','R','R','R'));
		// print categorie headers

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	$cashflowJaar=array();
		$cashflowTotaal=0;
	  $cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		$cashfow->genereerTransacties();
		$cashfow->genereerRows();
    $maanden=array(0,'jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
    $cashflowHuidigjaar=array();
		for($i=1;$i<13;$i++)
		{
      $maand=$maanden[$i];
		  $cashflowHuidigjaar[$maand]['lossing'] +=0;
		  $cashflowHuidigjaar[$maand]['rente'] +=0;
     // $cashflowHuidigjaar[$maand]['renob'] +=0;
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
    $huidigeJaar=substr($this->rapportageDatum,0,4);
    
    while($data=$DB->nextRecord())
    {
      $maand=$maanden[$data['maand']];
      $soort=strtolower($data['Grootboekrekening']);
      if($soort=='renob')
        $soort='rente';
      $cashflowHuidigjaar[$maand][$soort] +=$data['bedrag'];
      $cashflowJaar[$huidigeJaar][$soort] += $data['bedrag'];
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
    
    $jaarHuur=0;
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
          $cashflowJaar[$huidigeJaar]['huur'] += $data['MaandelijkseHuur'];
        }
        $jaarHuur+=$data['MaandelijkseHuur']*12;
      }
    }

    
    $rapJaar=substr($this->rapportageDatum,0,4);
    $lastJaar='';
		foreach ($cashfow->regelsRaw as $regel)
		{
		  $jaar=substr($regel['0'],6,4);
		  $realJaar=$jaar;
 		  if($jaar > ($rapJaar+13))
	      $jaar='Overig';
		  $maand=$maanden[intval(substr($regel['0'],3,2))];
		  if($jaar==$rapJaar)
      {
        $cashflowHuidigjaar[$maand][$regel[2]] += $regel[3];
      }
      else
      {
        if($realJaar<>$lastJaar)
        {
          $cashflowJaar[$jaar]['huur'] +=$jaarHuur;
        }
      }
		  $cashflowJaar[$jaar][$regel[2]] +=$regel[3];
		  $cashflowTotaal +=$regel[3];
		  $lastJaar=$realJaar;
		}
//listarray($cashflowJaar);exit;
		
	  $this->pdf->setY(125);
	  $this->pdf->underlinePercentage=0.8;
    $this->pdf->SetWidths(array(15,15,25,25,25,20,20));

		$this->pdf->SetAligns(array('L','L','R','R','R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('',vertaalTekst('Maand',$this->pdf->rapport_taal),vertaalTekst('Lossing',$this->pdf->rapport_taal),vertaalTekst('Rente',$this->pdf->rapport_taal),vertaalTekst('Huur',$this->pdf->rapport_taal),vertaalTekst('Totaal',$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $totalen=array();
		foreach ($cashflowHuidigjaar as $maand=>$waarden)
		{
       $this->pdf->Row(array('',vertaalTekst($maand,$this->pdf->rapport_taal),$this->formatGetal($waarden['lossing'],0),$this->formatGetal($waarden['renob']+$waarden['rente'],0),$this->formatGetal($waarden['huur'],0),
                         $this->formatGetal($waarden['lossing']+$waarden['rente']+$waarden['renob']+$waarden['huur'],0)));
       $totalen['lossing'] +=$waarden['lossing'];
       $totalen['rente'] +=$waarden['rente'];
       $totalen['renob'] +=$waarden['renob'];
       $totalen['huur'] +=$waarden['huur'];
		}
		$this->pdf->ln(2);
		$this->pdf->CellBorders = array('','',array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'));
		$this->pdf->Row(array('',vertaalTekst('Totaal',$this->pdf->rapport_taal),$this->formatGetal($totalen['lossing'],0),$this->formatGetal($totalen['renob']+$totalen['rente'],0),$this->formatGetal($totalen['huur'],0),
                      $this->formatGetal($totalen['lossing']+$totalen['rente']+$totalen['renob']+$totalen['huur'],0)));
    $this->pdf->CellBorders = array();

		$this->pdf->setY(125);
		$this->pdf->SetWidths(array(160,15,25,25,25,20,20));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->Row(array('',vertaalTekst('Jaar',$this->pdf->rapport_taal),vertaalTekst('Lossing',$this->pdf->rapport_taal),vertaalTekst('Rente',$this->pdf->rapport_taal),vertaalTekst('Huur',$this->pdf->rapport_taal),vertaalTekst('Totaal',$this->pdf->rapport_taal)));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $totalen=array();
		foreach ($cashflowJaar as $jaar=>$waarden)
		{
       $this->pdf->Row(array('',$jaar,$this->formatGetal($waarden['lossing'],0),
                         $this->formatGetal($totalen['renob']+$waarden['rente'],0),
                         $this->formatGetal($waarden['huur'],0),
                         $this->formatGetal($waarden['lossing']+$totalen['renob']+$waarden['rente']+$waarden['huur'],0)));
       $totalen['lossing'] +=$waarden['lossing'];
       $totalen['rente'] += ($totalen['renob']+$waarden['rente']);
       $totalen['huur'] +=$waarden['huur'];
		}
		$this->pdf->CellBorders = array('','',array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'));
		$this->pdf->ln(2);
		$this->pdf->Row(array('',vertaalTekst('Totaal',$this->pdf->rapport_taal),$this->formatGetal($totalen['lossing'],0),
                      $this->formatGetal($totalen['rente'],0),$this->formatGetal($totalen['huur'],0),
                      $this->formatGetal($totalen['lossing']+$totalen['rente']+$totalen['huur'],0)));
    $this->pdf->CellBorders = array();

    $this->pdf->setXY(20,110);
    $this->VBarDiagram(160,60,$cashflowHuidigjaar,vertaalTekst("Lopend jaar",$this->pdf->rapport_taal));
		$this->pdf->setXY(160,110);
    $this->VBarDiagram(160,60,$cashflowJaar,vertaalTekst("Langere termijn",$this->pdf->rapport_taal));
	}


	function VBarDiagram($w, $h, $data,$titel)
  {
      global $__appvar;
      $legendaWidth = 50;
      $grafiekPunt = array();
    $datumTotalen=array();
    $datumTotalenNegatief=array();
  
    $fixedColors=array('rente'=>$this->pdf->rapport_grafiek_drie,
                  'lossing'=>$this->pdf->rapport_grafiek_pcolor,
                  'renob'=>array($this->pdf->rapport_grafiek_drie[0]*1.3,$this->pdf->rapport_grafiek_drie[1]*1.3,$this->pdf->rapport_grafiek_drie[2]*1.3),
                  'huur'=>array(228,204,157));
    
      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = $datum;
        $n=0;
        foreach ($waarden as $categorie=>$waarde)
        {
          if($waarde<0)
            $datumTotalenNegatief[$datum]+=$waarde;
          else
            $datumTotalen[$datum]+=$waarde;
          $grafiek[$datum][$categorie]=$waarde;
          $grafiekCategorie[$categorie][$datum]=$waarde;
          $categorien[$categorie] = $n;
          $categorieId[$n]=$categorie ;
          
          if(!isset($colors[$categorie]))
            $colors[$categorie]=$fixedColors[$categorie];
          $n++;


        }
      }

      $numBars = count($legenda);

      if($color == null)
      {
        $color=array(155,155,155);
      }
      $maxVal=max($datumTotalen);
      $minVal = min($datumTotalen);
      $minValNegatief=min($datumTotalenNegatief);
      if($minValNegatief<$minVal)
        $minVal=$minValNegatief;
//echo "$minVal $maxVal <br>\n";exit;
      
      if($minVal>0)
        $minVal=0;
      
      if($maxVal<-5*$minVal)
      {
        $tmp=abs($minVal) / 5;
        if($tmp>$maxVal)
          $maxVal = $tmp;
      }
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

    //  $this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $bGrafiek, $hGrafiek,'FD','',array(245,245,245));

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

   foreach ($grafiek as $datum=>$data)
   {
     asort($data);
     
     $n=0;
     $negatieveStart=false;
      foreach($data as $categorie=>$val)
      {
        if($n==0 && $val<0)
          $negatieveStart=true;
        
        if(!isset($YstartGrafiekLast[$datum]) || ($negatieveStart==true && $val>=0))
        {
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
         }
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
           $this->pdf->TextWithRotation($xval-0.75,$YstartGrafiek+6,$legenda[$datum],45);

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
        $n++;
      }
      $i++;
   }

    $xval=$xPositie+$w/2;
   $x1=$xval-70;
   $y1=$bodem+8;
   $hLegend=3;
   $legendaMarge=2;
   $vertaling['rente']=vertaalTekst('Rente',$this->pdf->rapport_taal);
   $vertaling['lossing']=vertaalTekst('Lossing',$this->pdf->rapport_taal);
    $vertaling['renob']=vertaalTekst('Rente',$this->pdf->rapport_taal);
    $vertaling['huur']=vertaalTekst('Huur',$this->pdf->rapport_taal);

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
          $x1+=28;
         $i++;

      }

    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
}
?>
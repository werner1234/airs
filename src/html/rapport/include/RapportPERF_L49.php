<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/09/28 17:20:17 $
File Versie					: $Revision: 1.20 $

$Log: RapportPERF_L49.php,v $
Revision 1.20  2019/09/28 17:20:17  rvv
*** empty log message ***

Revision 1.19  2018/07/18 15:45:00  rvv
*** empty log message ***

Revision 1.18  2018/07/02 18:10:14  rvv
*** empty log message ***

Revision 1.17  2018/07/01 13:47:10  rvv
*** empty log message ***

Revision 1.16  2018/06/30 17:43:55  rvv
*** empty log message ***

Revision 1.15  2018/06/24 11:13:16  rvv
*** empty log message ***

Revision 1.14  2018/06/23 14:57:46  rvv
*** empty log message ***

Revision 1.13  2018/06/16 17:42:56  rvv
*** empty log message ***

Revision 1.12  2018/06/13 15:27:48  rvv
*** empty log message ***

Revision 1.11  2017/06/25 14:49:37  rvv
*** empty log message ***

Revision 1.10  2017/05/31 16:09:43  rvv
*** empty log message ***

Revision 1.9  2017/05/28 09:57:56  rvv
*** empty log message ***

Revision 1.8  2017/05/21 09:55:30  rvv
*** empty log message ***

Revision 1.7  2014/12/28 14:29:08  rvv
*** empty log message ***

Revision 1.6  2014/08/20 15:30:10  rvv
*** empty log message ***

Revision 1.5  2014/08/16 15:31:50  rvv
*** empty log message ***

Revision 1.4  2014/04/26 16:43:08  rvv
*** empty log message ***

Revision 1.3  2014/03/27 14:59:18  rvv
*** empty log message ***

Revision 1.2  2014/03/22 15:47:14  rvv
*** empty log message ***

Revision 1.1  2014/02/08 17:42:08  rvv
*** empty log message ***

Revision 1.2  2013/12/19 17:03:03  rvv
*** empty log message ***

Revision 1.1  2013/12/18 17:10:42  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/ATTberekening_L49.php");


class RapportPERF_L49
{
	function RapportPERF_L49($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	  $this->tweedeStart();
    $this->categorieKleuren=array();

	  //$this->rapportageDatumVanaf = "$RapStartJaar-01-01";

	 if ($RapStartJaar != $RapStopJaar)
	 {
     echo "Attributie start- en einddatum moeten in hetzelfde jaar liggen.";
     exit;
	 }
	}

	function tweedeStart()
	{
	  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) == db2jul($this->rapportageDatumVanaf))
	  {
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  }
	  else
	  {
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";
	   if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01" && $this->pdf->engineII == false)
	   {
	    $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,"$RapStartJaar-01-01",true);
      vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,"$RapStartJaar-01-01");
      $this->extraVulling = true;
	   }
	  }
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersBegin;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}



	function writeRapport()
	{
	  global $__appvar;

	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

	  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));


	 	//Kleuren instellen
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);
    $this->categorieKleuren=$allekleuren['OIB'];
	 // $this->categorieOmschrijving=array('LIQ'=>'Liquiditeiten','ZAK'=>'Zakelijke waarden','VAR'=>'Vastrentende waarden','Liquiditeiten'=>'Liquiditeiten');

	  $q="SELECT beleggingscategorie ,omschrijving FROM Beleggingscategorien";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
    while($data=$DB->nextRecord())
      $this->categorieOmschrijving[$data['beleggingscategorie']]=$data['omschrijving'];

//listarray($this->categorieVolgorde);
		// voor data
		$this->pdf->widthA = array(1,95,25,5,25,5,25,5,25,5,25,5,25,5,25,5);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R');


  	$this->pdf->widthB = array(1,95,30,10,30,115);
		$this->pdf->alignB = array('L','L','R','R','R');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    $this->pdf->AddPage();
    checkPage($this->pdf);

		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];


$DB = new DB();
$query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE Portefeuille = '".$this->portefeuille."' AND Categorie = 'Totaal' ORDER BY Datum ASC LIMIT 1 ";
$DB->SQL($query);
$DB->Query();
$datum = $DB->nextRecord();

if($this->pdf->lastPOST['perfPstart'] == 1 || 1)
{
  if($datum['id'] > 0)
  {
    if($datum['month'] <10)
      $datum['month'] = "0".$datum['month'];
    $start = $datum['year'].'-'.$datum['month'].'-01';
  }
  else
  {
    $start=$this->pdf->PortefeuilleStartdatum;
  }
}
else
  $start = $this->rapportageDatumVanaf;
  
  $tweedeStart=substr($this->rapportageDatum,0,4)."-01-01";
  
  if(db2jul($tweedeStart) < db2jul($this->pdf->PortefeuilleStartdatum))
    $tweedeStart=$this->pdf->PortefeuilleStartdatum;

  $att=new ATTberekening_L49($this);
  $indexDataBegin = $att->bereken($start,$this->rapportageDatum);
  $indexDataJaar = $att->bereken($tweedeStart,$this->rapportageDatum);


//exit;
$rapportageJaar=substr($this->rapportageDatum,0,4);
$dataPerDatum=array();

foreach($indexDataBegin as $categorie=>$categorieData)
{
  if($categorie=='Liquiditeiten')
    continue;

  if($categorie <> 'totaal')
  {
    foreach($categorieData['waarden'] as $datum=>$perfData)
    {
      $dataPerDatum[$datum][$categorie]['eindwaarde']=$perfData['eindwaarde'];
      $dataPerDatum[$datum][$categorie]['perf']=1+$perfData['procent'];
      $dataPerDatum[$datum][$categorie]['aandeel']=$perfData['eindwaarde']/$indexDataBegin['totaal']['waarden'][$datum]['eindwaarde'];
    }
  }
}

foreach($indexDataJaar as $categorie=>$categorieData)
{
  if($categorie=='Liquiditeiten')
    continue;
  if($categorie <> 'totaal')
  {
    foreach($categorieData['waarden'] as $datum=>$perfData)
    {
      $dataPerDatumJaar[$datum][$categorie]['eindwaarde']=$perfData['eindwaarde'];
      $dataPerDatumJaar[$datum][$categorie]['perf']=1+$perfData['procent'];
      $dataPerDatumJaar[$datum][$categorie]['aandeel']=$perfData['eindwaarde']/$indexDataJaar['totaal']['waarden'][$datum]['eindwaarde'];
    }
  }
}

$jaarPerf=array();
$kwartaalPerf=array();
$totaalPerf=array();
foreach($dataPerDatum as $datum=>$categorien)
{
   $jaar=substr($datum,0,4);
   $maand=substr($datum,5,2);
   $kwartaal="Q".ceil($maand/3);
      
   foreach($categorien as $categorie=>$perfData)
   {
      if(!isset($totaalPerf[$categorie]))
        $totaalPerf[$categorie]=1;
        
      $totaalPerf[$categorie]=$totaalPerf[$categorie]*$perfData['perf'];
      if($jaar<>$rapportageJaar)
      {
        if($jaar <> $lastJaar)
          $jaarPerf[$jaar][$categorie]['perf']=1;
          
        $jaarPerf[$jaar][$categorie]['perf']=$jaarPerf[$jaar][$categorie]['perf']*$perfData['perf'];
        $jaarPerf[$jaar][$categorie]['eindwaarde']=$perfData['eindwaarde'];
        $jaarPerf[$jaar][$categorie]['aandeel']=$perfData['aandeel'];
      }
      if($jaar==$rapportageJaar)
      {
        if($kwartaal <> $lastKwartaal)
          $kwartaalPerf[$kwartaal.'-'.$jaar][$categorie]['perf']=1;
       
        $kwartaalPerf[$kwartaal.'-'.$jaar][$categorie]['perf']=$kwartaalPerf[$kwartaal.'-'.$jaar][$categorie]['perf']*$perfData['perf'];
        $kwartaalPerf[$kwartaal.'-'.$jaar][$categorie]['eindwaarde']=$perfData['eindwaarde'];
        $kwartaalPerf[$kwartaal.'-'.$jaar][$categorie]['aandeel']=$perfData['aandeel'];
      }
   } 
   

   $lastKwartaal=$kwartaal;
}

$lastKwartaal='';

foreach($dataPerDatumJaar as $datum=>$categorien)
{
   $jaar=substr($datum,0,4);
   $maand=substr($datum,5,2);
   $kwartaal="Q".ceil($maand/3);
     
   foreach($categorien as $categorie=>$perfData)
   {
      if(!isset($totaalPerfJaar[$categorie]))
        $totaalPerfJaar[$categorie]=1;
        
      $totaalPerfJaar[$categorie]=$totaalPerfJaar[$categorie]*$perfData['perf'];
        if($kwartaal <> $lastKwartaal)
          $kwartaalPerfJaar[$kwartaal.'-'.$jaar][$categorie]['perf']=1;
       
      $kwartaalPerfJaar[$kwartaal.'-'.$jaar][$categorie]['perf']=$kwartaalPerfJaar[$kwartaal.'-'.$jaar][$categorie]['perf']*$perfData['perf'];
      $kwartaalPerfJaar[$kwartaal.'-'.$jaar][$categorie]['eindwaarde']=$perfData['eindwaarde'];
      $kwartaalPerfJaar[$kwartaal.'-'.$jaar][$categorie]['aandeel']=$perfData['aandeel'];
   } 
   $lastKwartaal=$kwartaal;
}

foreach($jaarPerf as $jaar=>$jaarData)
{
  $grafiekData[$jaar]=$jaarData;
}

for($i=1;$i<=4;$i++)
 $grafiekData['Q'.$i.'-'.$rapportageJaar]=array();
  
foreach($kwartaalPerf as $kwartaal=>$kwartaalData)
{
  $grafiekData[$kwartaal]=$kwartaalData;
  $laatste=$kwartaalData;
} 

foreach($kwartaalPerfJaar as $kwartaal=>$kwartaalData)
{
  $laatsteJaar=$kwartaalData;
} 


		  if (count($grafiekData) > 0)
		  {
		     $witMarge=$this->pdf->witCell;
         $grafiekX=190;
          $this->pdf->SetXY($grafiekX,$this->pdf->rapportYstart)	;
           $this->VBarDiagram2(90, 50, $grafiekData,'Rendement per asset groep');
           
          $this->pdf->SetY(90);
          $this->pdf->setWidths(array($grafiekX-20,40-$witMarge,$witMarge,25-$witMarge,$witMarge,25));
          $this->pdf->SetAligns(array('L','L','C','R','C','R'));
          $this->pdf->fillCell = array(0,1,0,1,0,1);
           
          $this->pdf->SetFillColor($this->pdf->achtergrondKop[0],$this->pdf->achtergrondKop[1],$this->pdf->achtergrondKop[2]);
          $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
          $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
          $this->pdf->row(array('',"Rendement\nper asset groep ",'','Rapportage kwartaal','','Cumulatief lopend jaar'));
          
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
          $n=0;
          foreach($laatsteJaar as $categorie=>$categorieData)
          {
            $kleur=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
            $n=$this->switchColor($n);

            $this->pdf->row(array('','    '.$this->categorieOmschrijving[$categorie],'',
                                     $this->formatGetal(($categorieData['perf']-1)*100,1).'%','',
                                     $this->formatGetal(($totaalPerfJaar[$categorie]-1)*100,1).'%')
                                     );

            $this->pdf->Rect($grafiekX+1 , $this->pdf->getY()-4, 2, 2, 'F',null,$kleur);


          }
		  }
      
      


    $this->maakIndex();

    $this->pdf->fillCell = array();

    paginaVoet($this->pdf);
	}
  
    function switchColor($n)
  {
     $col1=$this->pdf->achtergrondLicht;
     $col2=$this->pdf->achtergrondDonker;

    if($n%2==0)
      $this->pdf->SetFillColor($col1[0],$col1[1],$col1[2]);
    else
      $this->pdf->SetFillColor($col2[0],$col2[1],$col2[2]);
      
      $n++;
      return $n;
  }


function formatGetalLength ($getal,$decimaal,$gewensteLengte)
{
 $lengte = strlen(round($getal));
 if($getal < 0)
  $lengte --;
 $mogelijkeDecimalen = $gewensteLengte - $lengte;
 if($lengte >$gewensteLengte)
   $decimaal = 0;
 elseif ($decimaal > $mogelijkeDecimalen)
   $decimaal = $mogelijkeDecimalen;
 return number_format($getal,$decimaal,',','');
}



  function VBarDiagram2($w, $h, $data,$titel)
  {
      global $__appvar;
      $legendaWidth = 0;//45;
      $grafiekPunt = array();
      $verwijder=array();

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();

      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
      $this->pdf->setXY($XPage,$YPage+2);
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
      $this->pdf->Cell($w,4,$titel,0,1,'L');
      $this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array($this->pdf->koplijn[0],$this->pdf->koplijn[1],$this->pdf->koplijn[2]),'dash'=>0));
      $this->pdf->line($XPage,$YPage+$this->pdf->rowHeight+3,$XPage+$w,$YPage+$this->pdf->rowHeight+3);
      
      $YPage=$YPage+$h+15;

      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = $datum;
        $n=0;
        $minVal=-1;
        $maxVal=1;
        foreach ($waarden as $categorie=>$waarde)
        {
          if($categorie=='LIQ')
            $categorie='Liquiditeiten';
          $grafiek[$datum][$categorie]=($waarde['perf']-1)*100;
          $grafiekCategorie[$categorie][$datum]=$waarde['perf'];
          $categorien[$categorie] = $categorie;
          $categorieId[$n]=$categorie ;

          $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          $n++;
        }
      }

   
      foreach($grafiek as $datum=>$datumData)
      {
      
        foreach($datumData as $categorie=>$waarde)
        {
          
          if($waarde > $maxVal)
            $maxVal=ceil($waarde);
          if($waarde < $minVal)
            $minVal=floor($waarde);  
        }
      }


$minVal=floor($minVal/5)*5;
$maxVal=ceil($maxVal/5)*5;

      $numBars = count($legenda);

      if($color == null)
      {
        $color=array(155,155,155);
      }


      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $margin = 0;
      $margeLinks=10;
      $XPage+=$margeLinks;
      $w-=$margeLinks;
      
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

      $n=0;
    /*
      foreach ($categorien as $categorie)
      {
          $this->pdf->Rect($XstartGrafiek+$bGrafiek+3 , $YstartGrafiek-$hGrafiek+$n*7+2, 2, 2, 'F',null,$colors[$categorie]);
          $this->pdf->SetXY($XstartGrafiek+$bGrafiek+6 ,$YstartGrafiek-$hGrafiek+$n*7+1.5 );
          $this->pdf->MultiCell(40, 4,$this->categorieOmschrijving[$categorie],0,'L');
          $n++;
      }
*/
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


      $horDiv = 4;
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

      for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
      {
        $skipNull = true;
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1)." %",0,0,'R');
        $n++;
        if($n >20)
         break;
      }

      $n=0;
      for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        if($skipNull == true)
          $skipNull = false;
        else
        {
          $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
          $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte)." %",0,0,'R');
        }
        $n++;
        if($n >20)
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
      $aantalCategorien=count($data);
      $catCount=0;
      foreach($data as $categorie=>$val)
      {

          $lval = $eBaton/$aantalCategorien;
          $xval = $XstartGrafiek + (1 + $i ) * $vBar + ($catCount * $lval) - $eBaton / 2;
          $yval = $YstartGrafiek + $nulYpos ;
          $hval = ($val * $unit);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);

         $this->pdf->SetTextColor(0,0,0);

         if($legendaPrinted[$datum] != 1)
         {
           $part=explode("-",$legenda[$datum]);
            $extraY=0;
            $this->pdf->SetXY($xval,$YstartGrafiek+$extraY+2);
            $this->pdf->Cell($eBaton,0,$part[0],0,0,'C');
         }

         if($grafiekPunt[$categorie][$datum])
         {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
         $legendaPrinted[$datum] = 1;
         $catCount++;
      }
      $i++;
   }

   $i=0;
   $YstartGrafiekLast=array();
   foreach ($grafiekNegatief as $datum=>$data)
   {
      foreach($data as $categorie=>$val)
      {
          if(!isset($YstartGrafiekLast[$datum]))
            $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $lval = $eBaton/$aantalCategorien;
          $xval = $XstartGrafiek + (1 + $i ) * $vBar + ($catCount * $lval) - $eBaton / 2;
          $yval = $YstartGrafiek + $nulYpos ;
          $hval = $hval = ($val * $unit);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;

         if($grafiekPunt[$categorie][$datum])
         {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
      }
      $i++;
   }
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
  

  function maakIndex()
  {
    $db=new DB();

    $indexFondSize=$this->pdf->rapport_fontsize;
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
      $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
    elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
      $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
    else
      $this->tweedePerformanceStart = "$RapStartJaar-01-01";


    if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul(($RapStartJaar-1)."-01-01"))
      $vorigJaar = $this->pdf->PortefeuilleStartdatum;
    else
      $vorigJaar= ($RapStartJaar-1)."-01-01";


    $DB=new DB();
    $perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum,'vorig'=>$vorigJaar,'pstart'=>substr($this->pdf->PortefeuilleStartdatum,0,10));

    $query="SELECT Vermogensbeheerder FROM Portefeuilles WHERE Portefeuille='".$this->portefeuille."'";
    $db->SQL($query);
    $verm=$db->lookupRecord();

    $indices=array();
    /*
    $query="SELECT Fondsen.Omschrijving,Fondsen.Omschrijving,Fondsen.Valuta, IndexPerBeleggingscategorie.Fonds 
    FROM IndexPerBeleggingscategorie
    INNER JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds = Fondsen.Fonds
    WHERE 
    IndexPerBeleggingscategorie.Vermogensbeheerder='".$verm['Vermogensbeheerder']."' AND IndexPerBeleggingscategorie.Portefeuille='".$this->portefeuille."' 
    AND IndexPerBeleggingscategorie.vanaf < '".$this->rapportageDatum."'
    ORDER BY IndexPerBeleggingscategorie.vanaf desc limit 1 ";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      $data['type']='Benchmark Beheerder';
      $indices[$data['Fonds']]=$data;
    }
*/
    $query="SELECT Portefeuilles.SpecifiekeIndex, Fondsen.Omschrijving,Fondsen.Valuta
    FROM Portefeuilles
    INNER JOIN Fondsen ON Portefeuilles.SpecifiekeIndex = Fondsen.Fonds
    WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      $data['type']='Benchmark Family Capital Trust';
      if($data['SpecifiekeIndex'] <> '')
        $indices[$data['SpecifiekeIndex']]=$data;
    }

    $query="SELECT Fondsen.Omschrijving,Fondsen.Omschrijving,Fondsen.Valuta, IndexPerBeleggingscategorie.Fonds ,IndexPerBeleggingscategorie.Beleggingscategorie,
Beleggingscategorien.Omschrijving as `type`
    FROM IndexPerBeleggingscategorie
    INNER JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds = Fondsen.Fonds
    JOIN Beleggingscategorien ON IndexPerBeleggingscategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
    WHERE 
    IndexPerBeleggingscategorie.Vermogensbeheerder='".$verm['Vermogensbeheerder']."' AND IndexPerBeleggingscategorie.Portefeuille='' 
    AND IndexPerBeleggingscategorie.vanaf < '".$this->rapportageDatum."'
    ORDER BY IndexPerBeleggingscategorie.Beleggingscategorie ";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
    //  $data['type']=$data['Beleggingscategorie'];
      $indices[$data['Fonds']]=$data;
    }

    //echo $this->pdf->witCell;exit;

      $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize+2);
      $this->pdf->setY($this->pdf->rapportYstart+2);
      $this->pdf->SetX($this->pdf->marge);
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
      $this->pdf->Cell(150,4,'Weging en ontwikkeling benchmarks', 0, "L");
      $this->pdf->fillCell = array(1,0,1,0,1,0,1,0,1,0,1,0,1);
      $this->pdf->SetWidths(array(75,$this->pdf->witCell,15,$this->pdf->witCell,20,$this->pdf->witCell,15,$this->pdf->witCell,15,$this->pdf->witCell,15));
      $this->pdf->SetAligns(array('L','C','R','C','R','C','R','C','R','C','R'));
      $tmp=array_sum($this->pdf->widths);
      $this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array($this->pdf->koplijn[0],$this->pdf->koplijn[1],$this->pdf->koplijn[2]),'dash'=>0));
      $this->pdf->Line($this->pdf->marge,$this->pdf->rapportYstart+$this->pdf->rowHeight+3,$tmp+$this->pdf->marge,$this->pdf->rapportYstart+$this->pdf->rowHeight+3);
      $this->pdf->Ln(11);

   


    $indexData=array();
    $headerPrinted=false;
    foreach($indices as $hoofdIndex=>$hoofdIndexData)
    {

      foreach ($perioden as $periode=>$datum)
      {
        $indexData[$hoofdIndex]['fondsKoers_'.$periode]=getFondsKoers($hoofdIndex,$datum);
        //  $indexData[$hoofdIndex]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
      }
      $indexData[$hoofdIndex]['performanceJaar'] = ($indexData[$hoofdIndex]['fondsKoers_eind'] - $indexData[$hoofdIndex]['fondsKoers_jan'])    / ($indexData[$hoofdIndex]['fondsKoers_jan']/100 );
      $indexData[$hoofdIndex]['performance'] =     ($indexData[$hoofdIndex]['fondsKoers_eind'] - $indexData[$hoofdIndex]['fondsKoers_begin']) / ($indexData[$hoofdIndex]['fondsKoers_begin']/100 );
      $indexData[$hoofdIndex]['performanceVorig'] =     ($indexData[$hoofdIndex]['fondsKoers_jan'] - $indexData[$hoofdIndex]['fondsKoers_vorig']) / ($indexData[$hoofdIndex]['fondsKoers_vorig']/100 );
      $indexData[$hoofdIndex]['performancePstart'] =     ($indexData[$hoofdIndex]['fondsKoers_eind'] - $indexData[$hoofdIndex]['fondsKoers_pstart']) / ($indexData[$hoofdIndex]['fondsKoers_pstart']/100 );
      //$indexData[$hoofdIndex]['performanceEurJaar'] = ($indexData[$hoofdIndex]['fondsKoers_eind']*$indexData[$hoofdIndex]['valutaKoers_eind'] - $indexData[$hoofdIndex]['fondsKoers_jan']  *$indexData[$hoofdIndex]['valutaKoers_jan'])/(  $indexData[$hoofdIndex]['fondsKoers_jan']*  $indexData[$hoofdIndex]['valutaKoers_jan']/100 );
      //$indexData[$hoofdIndex]['performanceEur'] =     ($indexData[$hoofdIndex]['fondsKoers_eind']*$indexData[$hoofdIndex]['valutaKoers_eind'] - $indexData[$hoofdIndex]['fondsKoers_begin']*$indexData[$hoofdIndex]['valutaKoers_begin'])/($indexData[$hoofdIndex]['fondsKoers_begin']*$indexData[$hoofdIndex]['valutaKoers_begin']/100 );
      $this->pdf->SetFont($this->pdf->rapport_font,"B",$indexFondSize);
      $this->pdf->SetFillColor($this->pdf->achtergrondKop[0],$this->pdf->achtergrondKop[1],$this->pdf->achtergrondKop[2]);
      $this->pdf->row(array($hoofdIndexData['type'],'','','','% Periode','','% YtD','','% '.substr($perioden['vorig'],0,4),'','% LtD'));
      $headerPrinted=true;
      $n=$this->switchColor($n);
      $this->pdf->SetFont($this->pdf->rapport_font,"",$indexFondSize);
      $this->pdf->row(array($hoofdIndexData['Omschrijving'].',','','','',$this->formatGetal($indexData[$hoofdIndex]['performance'],1).'%',
                                                                      '',$this->formatGetal($indexData[$hoofdIndex]['performanceJaar'],1).'%',
                                                                      '',$this->formatGetal($indexData[$hoofdIndex]['performanceVorig'],1).'%',
                                                                      '',$this->formatGetal($indexData[$hoofdIndex]['performancePstart'],1).'%'));
      $n=$this->switchColor($n);
      $this->pdf->row(array('    bestaande uit:','','Weging','','','','','','','',''));
      $n=$this->switchColor($n);
      //$this->pdf->row(array('','','Weging','','','',''));
      //$n=$this->switchColor($n);
      $query="SELECT benchmarkverdeling.fonds, benchmarkverdeling.percentage, Fondsen.Omschrijving,Fondsen.Valuta
      FROM benchmarkverdeling 
      INNER JOIN Fondsen ON benchmarkverdeling.fonds = Fondsen.Fonds
      WHERE benchmarkverdeling.benchmark='".$hoofdIndex."'";
      $db->SQL($query);
      $db->Query();
      while($data=$db->nextRecord())
      {
        foreach ($perioden as $periode=>$datum)
        {
          $indexData[$data['fonds']]['fondsKoers_'.$periode]=getFondsKoers($data['fonds'],$datum);
          //$indexData[$data['fonds']]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
        }
        $indexData[$data['fonds']]['performanceJaar'] = ($indexData[$data['fonds']]['fondsKoers_eind'] - $indexData[$data['fonds']]['fondsKoers_jan'])    / ($indexData[$data['fonds']]['fondsKoers_jan']/100 );
        $indexData[$data['fonds']]['performance'] =     ($indexData[$data['fonds']]['fondsKoers_eind'] - $indexData[$data['fonds']]['fondsKoers_begin']) / ($indexData[$data['fonds']]['fondsKoers_begin']/100 );
        $indexData[$data['fonds']]['performanceVorig'] =     ($indexData[$data['fonds']]['fondsKoers_jan'] - $indexData[$data['fonds']]['fondsKoers_vorig']) / ($indexData[$data['fonds']]['fondsKoers_vorig']/100 );
        $indexData[$data['fonds']]['performancePstart'] =     ($indexData[$data['fonds']]['fondsKoers_eind'] - $indexData[$data['fonds']]['fondsKoers_pstart']) / ($indexData[$data['fonds']]['fondsKoers_pstart']/100 );

        //  listarray($data);
        $this->pdf->row(array('    '.$data['Omschrijving'],'',
                          $this->formatGetal($data['percentage'],1),'',
                          $this->formatGetal($indexData[$data['fonds']]['performance'],1).'%','',
                          $this->formatGetal($indexData[$data['fonds']]['performanceJaar'],1).'%',
                          '',$this->formatGetal($indexData[$data['fonds']]['performanceVorig'],1).'%',
                          '',$this->formatGetal($indexData[$data['fonds']]['performancePstart'],1).'%'));
        $n=$this->switchColor($n);

      }
    }



$query="SELECT
IndexPerBeleggingscategorie.Fonds as fonds,
IndexPerBeleggingscategorie.Categoriesoort,
IndexPerBeleggingscategorie.Categorie,
Fondsen.Omschrijving,
Beleggingscategorien.Omschrijving  as categorieOmschrijving
FROM
IndexPerBeleggingscategorie
INNER JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds = Fondsen.Fonds
INNER JOIN Beleggingscategorien ON IndexPerBeleggingscategorie.Categorie = Beleggingscategorien.Beleggingscategorie
WHERE 
IndexPerBeleggingscategorie.Vermogensbeheerder='".$verm['Vermogensbeheerder']."' AND IndexPerBeleggingscategorie.Categoriesoort='Beleggingscategorien'
ORDER BY Categorie";
    $db->SQL($query);
    $db->Query();
    $lastCategorie='';
    while($data=$db->nextRecord())
    {

      if($data['Categorie'] <> $lastCategorie)
      {
        $this->pdf->SetFont($this->pdf->rapport_font, "B", $indexFondSize);
        $this->pdf->SetFillColor($this->pdf->achtergrondKop[0], $this->pdf->achtergrondKop[1], $this->pdf->achtergrondKop[2]);
        if($headerPrinted==false)
        {
          $headerPrinted=true;
          $this->pdf->row(array($data['categorieOmschrijving'],'','','','% Periode','','% YtD','','% '.substr($perioden['vorig'],0,4),'','% LtD'));
        }
        else
        {
          $this->pdf->row(array($data['categorieOmschrijving'], '', '', '', '', '', '', '', '', '', ''));
        }
        $n = $this->switchColor($n);
        $this->pdf->SetFont($this->pdf->rapport_font, "BI", $indexFondSize);
    //    $this->pdf->row(array('Benchmark', '', '', '', '% Periode', '', '% YtD', '', 'vorig J', '', 'va start'));
        $n = $this->switchColor($n);
        $this->pdf->SetFont($this->pdf->rapport_font, "", $indexFondSize);
      }
      foreach ($perioden as $periode=>$datum)
      {
        $indexData[$data['fonds']]['fondsKoers_'.$periode]=getFondsKoers($data['fonds'],$datum);
      }
      $indexData[$data['fonds']]['performanceJaar'] = ($indexData[$data['fonds']]['fondsKoers_eind'] - $indexData[$data['fonds']]['fondsKoers_jan'])    / ($indexData[$data['fonds']]['fondsKoers_jan']/100 );
      $indexData[$data['fonds']]['performance'] =     ($indexData[$data['fonds']]['fondsKoers_eind'] - $indexData[$data['fonds']]['fondsKoers_begin']) / ($indexData[$data['fonds']]['fondsKoers_begin']/100 );
      $indexData[$data['fonds']]['performanceVorig'] =     ($indexData[$data['fonds']]['fondsKoers_jan'] - $indexData[$data['fonds']]['fondsKoers_vorig']) / ($indexData[$data['fonds']]['fondsKoers_vorig']/100 );
      $indexData[$data['fonds']]['performancePstart'] =     ($indexData[$data['fonds']]['fondsKoers_eind'] - $indexData[$data['fonds']]['fondsKoers_pstart']) / ($indexData[$data['fonds']]['fondsKoers_pstart']/100 );

      //  listarray($data);
      $this->pdf->row(array('    '.$data['Omschrijving'],'',
                        '','',
                        $this->formatGetal($indexData[$data['fonds']]['performance'],1).'%','',
                        $this->formatGetal($indexData[$data['fonds']]['performanceJaar'],1).'%',
                        '',$this->formatGetal($indexData[$data['fonds']]['performanceVorig'],1).'%',
                        '',$this->formatGetal($indexData[$data['fonds']]['performancePstart'],1).'%'));
      $n=$this->switchColor($n);
      $lastCategorie=$data['Categorie'];
    }



    $extraX=170;
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize+2);
    $this->pdf->setXY($extraX+$this->pdf->marge,135);


    $width=array_sum(array(22,$this->pdf->witCell,20,$this->pdf->witCell,15,$this->pdf->witCell,15,$this->pdf->witCell,15));
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Cell($width,4,'Valutas',0,1,'L');
    $this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array($this->pdf->koplijn[0],$this->pdf->koplijn[1],$this->pdf->koplijn[2]),'dash'=>0));
    $this->pdf->line($this->pdf->marge+$extraX,$this->pdf->getY()+2,$this->pdf->marge+$extraX+$width,$this->pdf->getY()+2);
    $this->pdf->ln(6);



    $this->pdf->SetX($this->pdf->marge);
    $this->pdf->fillCell = array(0,1,0,1,0,1,0,1,0,1,0,1,0,1);
    $this->pdf->SetWidths(array($extraX,22,$this->pdf->witCell,20,$this->pdf->witCell,15,$this->pdf->witCell,15,$this->pdf->witCell,15));
    $this->pdf->SetAligns(array('L','L','C','R','C','R','C','R','C','R','C','R'));


    $valutas=array('USD','CHF','GBP');
    $this->pdf->SetFont($this->pdf->rapport_font, "B", $indexFondSize);
    $this->pdf->SetFillColor($this->pdf->achtergrondKop[0], $this->pdf->achtergrondKop[1], $this->pdf->achtergrondKop[2]);
    $this->pdf->row(array('','Valuta','','% Periode','','% YtD','',substr($perioden['vorig'],0,4),'','% LtD'));
    $n = $this->switchColor($n);
    $this->pdf->SetFont($this->pdf->rapport_font, "", $indexFondSize);
    foreach($valutas as $valuta)
    {
      foreach ($perioden as $periode=>$datum)
      {
        $indexData[$valuta]['fondsKoers_'.$periode]=getValutaKoers($valuta,$datum);
      }
      $indexData[$valuta]['performanceJaar'] = ($indexData[$valuta]['fondsKoers_eind'] - $indexData[$valuta]['fondsKoers_jan'])    / ($indexData[$valuta]['fondsKoers_jan']/100 );
      $indexData[$valuta]['performance'] =     ($indexData[$valuta]['fondsKoers_eind'] - $indexData[$valuta]['fondsKoers_begin']) / ($indexData[$valuta]['fondsKoers_begin']/100 );
      $indexData[$valuta]['performanceVorig'] =     ($indexData[$valuta]['fondsKoers_jan'] - $indexData[$valuta]['fondsKoers_vorig']) / ($indexData[$valuta]['fondsKoers_vorig']/100 );
      $indexData[$valuta]['performancePstart'] =     ($indexData[$valuta]['fondsKoers_eind'] - $indexData[$valuta]['fondsKoers_pstart']) / ($indexData[$valuta]['fondsKoers_pstart']/100 );

      //  listarray($data);
      $this->pdf->row(array('','    '.$valuta."/EUR",'',
                        $this->formatGetal($indexData[$valuta]['performance'],1).'%','',
                        $this->formatGetal($indexData[$valuta]['performanceJaar'],1).'%',
                        '',$this->formatGetal($indexData[$valuta]['performanceVorig'],1).'%',
                        '',$this->formatGetal($indexData[$valuta]['performancePstart'],1).'%'));
      $n=$this->switchColor($n);

    }



    if(count($indices)==0)
      return 1;

  }

}
?>
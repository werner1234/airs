<?
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/09/23 17:14:23 $
 		File Versie					: $Revision: 1.13 $

 		$Log: RapportPERFG_L50.php,v $
 		Revision 1.13  2018/09/23 17:14:23  cvs
 		call 7175
 		
 		Revision 1.12  2017/11/08 10:10:28  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2017/11/04 17:40:21  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2017/05/20 18:16:29  rvv
 		*** empty log message ***

 		Revision 1.9  2017/04/22 16:44:09  rvv
 		*** empty log message ***

 		Revision 1.8  2015/07/29 16:09:29  rvv
 		*** empty log message ***

 		Revision 1.7  2014/10/29 16:47:20  rvv
 		*** empty log message ***

 		Revision 1.6  2014/05/07 15:40:17  rvv
 		*** empty log message ***

 		Revision 1.5  2014/04/05 15:33:48  rvv
 		*** empty log message ***


*/

include_once('../indexBerekening.php');
include_once($__appvar["basedir"]."/html/rapport/include/ATTberekening_L35.php");


class RapportPERFG_L50
{

  function RapportPERFG_L50($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "PERFG";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    if($this->pdf->rapport_PERFGRAFIEK_titel)
      $this->pdf->rapport_titel = $this->pdf->rapport_PERFG_titel;
    else
      $this->pdf->rapport_titel = "Rendement op het belegd vermogen versus gewogen benchmark";


    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
  }

  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }


  function writeRapport()
  {

    $query = "SELECT Portefeuilles.Startdatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $this->portefeuilledata = $DB->nextRecord();
    $this->pdf->AddPage();
    $this->pdf->templateVars['PERFGPaginas']=$this->pdf->page;
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0));

    $DB = new DB();
    $query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE Portefeuille = '".$this->portefeuille."' AND Categorie = 'Totaal' ORDER BY Datum ASC LIMIT 1 ";
    $DB->SQL($query);
    $DB->Query();
    $datum = $DB->nextRecord();

    if($datum['id'] > 0 && $this->pdf->lastPOST['perfPstart'] == 1)
    {
      if($datum['month'] <10)
        $datum['month'] = "0".$datum['month'];
      $start = $datum['year'].'-'.$datum['month'].'-01';
    }
    elseif($this->pdf->lastPOST['perfPstart'] == 1)
    {
      $start=substr($this->portefeuilledata['Startdatum'],0,10);
    }
    else
      $start = $this->rapportageDatumVanaf;

    $eind = $this->rapportageDatum;
    $datumStart = db2jul($start);
    $datumStop  = db2jul($eind);




//$index = new indexHerberekening();
//$index->geenCacheGebruik=true;
//$indexWaarden = $index->getWaarden($start,$eind,$this->portefeuille,$this->portefeuilledata['SpecifiekeIndex'],'periode');

    $att=new ATTberekening_L35($this);
    $this->meerjarenStart=$start;

    if($this->pdf->lastPOST['perfPstart'] == 0)
    {
//echo "Norm ".$this->rapportageDatumVanaf.",$eind,".$this->pdf->rapportageValuta.",hoofdcategorie <br>\n";
      $hcatData = $att->bereken($this->rapportageDatumVanaf, $eind, $this->pdf->rapportageValuta, 'hoofdcategorie');
      if(isset($hcatData['Liquiditeiten']))
        unset($hcatData['Liquiditeiten']);

      $this->toonData($hcatData);
      $this->pdf->addpage();
     // $hcatData = $att->bereken($this->rapportageDatumVanaf, $eind, $this->pdf->rapportageValuta, 'hoofdcategorie'); //categorie
      $this->toonData($hcatData, 'maand', array(), true);
    }

    if($this->pdf->lastPOST['perfPstart'] == 1)
    {
      $this->pdf->rapport_titel = "Historisch rendement op het belegd vermogen versus gewogen benchmark";
      //$this->pdf->AddPage();
      $hcatDataJaren=$att->bereken($start,$eind,$this->pdf->rapportageValuta,'hoofdcategorie');
      if(isset($hcatDataJaren['Liquiditeiten']))
        unset($hcatDataJaren['Liquiditeiten']);
      $hcatDataJarenShort=$this->maandenNaarJaren($hcatDataJaren);


      $this->toonData($hcatDataJaren,'jaar',$hcatDataJarenShort);

     // $this->pdf->addpage();
     // $hcatDataJaren = $att->bereken($start, $eind, $this->pdf->rapportageValuta, 'categorie');
     // $hcatDataJarenShort=$this->maandenNaarJaren($hcatDataJaren);
    //  $this->toonData($hcatDataJaren, 'jaar', $hcatDataJarenShort, true);

    }

  }

  function maandenNaarJaren($maandDataIn)
  {
//listarray($maandData);
    $tmp=array();
    $somVelden=array('indexPerf','stort','stortEnOnttrekking','storting','onttrekking','kosten','opbrengst','kostenNietGekoppeld','resultaat','ongerealiseerd','gerealiseerd');
    $stapelItems=array('indexBijdrageWaarde','overPerf','relContrib','procent');
    $gemiddeldeVelden=array('gemWaarde');
    foreach($maandDataIn as $categorie=>$maandData)
    {
      foreach($maandData['perfWaarden'] as $maand=>$totaalData)
      {
        $julDatum=db2jul($maand);
        $jaar=date("Y",$julDatum);

        if($jaar<>$laatsteJaar)
        {
          $laatsteDag[$laatsteJaar]=date("-m-d",$laasteJulDatum);
        }

        $laasteJulDatum=$julDatum;
        $laatsteJaar=$jaar;
      }
      $laatsteDag[$laatsteJaar]=date("-m-d",$laasteJulDatum);

      $aantalWaarden=0;
      foreach($maandData['perfWaarden'] as $maand=>$totaalData)
      {
        $julDatum=db2jul($maand);
        $jaar=date("Y",$julDatum);
        //$dateEnd='-12-31';
        $dateEnd=$laatsteDag[$jaar];
        if($jaar <> '')
        {

          if($jaar <> $lastJaar)
          {
            $lastJaar='';
            foreach ($stapelItems as $item)
            {
              //      if(!isset($tmp[$categorie]['perfWaarden'][$jaar.$dateEnd][$item]))
              //      $tmp[$categorie]['perfWaarden'][$jaar.$dateEnd][$item]=1;
            }
            if($lastJaar <> '')
            {
              foreach ($gemiddeldeVelden as $item)
                $tmp[$categorie]['perfWaarden'][$jaar.$dateEnd][$item]=$tmp[$categorie]['perfWaarden'][$jaar.$dateEnd][$item] /($aantalWaarden+1);
            }
            $aantalWaarden=0;

          }

          if(!isset($tmp[$categorie]['perfWaarden'][$jaar.$dateEnd]['beginwaarde']))
            $tmp[$categorie]['perfWaarden'][$jaar.$dateEnd]['beginwaarde']=$totaalData['beginwaarde'];
          $tmp[$categorie]['perfWaarden'][$jaar.$dateEnd]['eindwaarde']=$totaalData['eindwaarde'];
          $tmp[$categorie]['perfWaarden'][$jaar.$dateEnd]['index']=$totaalData['index'];

          foreach($somVelden as $veld)
            $tmp[$categorie]['perfWaarden'][$jaar.$dateEnd][$veld]+=$totaalData[$veld];

          foreach ($stapelItems as $item)
            $tmp[$categorie]['perfWaarden'][$jaar.$dateEnd][$item] = (($tmp[$categorie]['perfWaarden'][$jaar.$dateEnd][$item]+1)  * ($totaalData[$item]+1))-1;

          foreach ($gemiddeldeVelden as $item)
            $tmp[$categorie]['perfWaarden'][$jaar.$dateEnd][$item] += $totaalData[$item];

          $lastJaar=$jaar;
          $aantalWaarden++;
        }
      }
      foreach ($gemiddeldeVelden as $item)
        $tmp[$categorie]['perfWaarden'][$jaar.$dateEnd][$item] =$tmp[$categorie]['perfWaarden'][$jaar.$dateEnd][$item]/($aantalWaarden+1);
      //foreach ($stapelItems as $item)
      //   $tmp[$categorie]['perfWaarden'][$jaar.$dateEnd][$item] =$tmp[$categorie]['perfWaarden'][$jaar.$dateEnd][$item]-1;
    }

    //listarray($tmp);
    return $tmp;
  }

  function toonData($perfdata,$periode='maand',$hcatDataJarenShort=array(),$skipTotaal=false)
  {

    $DB = new DB();
    $query="SELECT IndexPerBeleggingscategorie.Beleggingscategorie,IndexPerBeleggingscategorie.Fonds FROM IndexPerBeleggingscategorie WHERE IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' 
      AND (IndexPerBeleggingscategorie.Portefeuille='".$this->portefeuille."' or IndexPerBeleggingscategorie.Portefeuille='')
      ORDER BY IndexPerBeleggingscategorie.Portefeuille";
    $DB->SQL($query);
    $DB->Query();
    while($index=$DB->nextRecord())
      $indexLookup[$index['Beleggingscategorie']]=$index['Fonds'];
    $indexLookup['totaal']=$this->portefeuilledata['SpecifiekeIndex'];


    $hCategorieOmschrijvingen=array();
    $query="SELECT
Beleggingscategorien.Omschrijving,
Beleggingscategorien.Beleggingscategorie
FROM
CategorienPerHoofdcategorie
Inner Join Beleggingscategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = Beleggingscategorien.Beleggingscategorie
WHERE CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
GROUP BY Beleggingscategorien.Beleggingscategorie
";
    $DB->SQL($query);
    $DB->Query();
    while($cat=$DB->nextRecord())
      $hCategorieOmschrijvingen[$cat['Beleggingscategorie']]=$cat['Omschrijving'];

    $datumStop  = db2jul($this->rapportageDatum);
    if($periode=='maand')
      $maandPeriode=mktime(0,0,0,1,1,date("Y",$datumStop));//-1

    $this->pdf->CellBorders=array();
    $this->pdf->setY(45);
    $this->pdf->ln();
    //$this->pdf->CellBorders = array(array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'));
    $listWidthsFirst=array(14,26,25,25,16,26,22,22,22,26,22,22);
    $listWidths=array(14,26,25,25,26,25,15,15,15,15);

    $this->pdf->CellBorders = array();
    $YendIndex = $this->pdf->GetY();
    $categorien=array_keys($perfdata); //array('ZAK','VAR','totaal');

    foreach ($categorien as $cat)
    {
      $indexStart=false;
      if($skipTotaal==true && $cat=='totaal')
        continue;
      $perfIndexCum=0;
      foreach ($perfdata[$cat]['perfWaarden'] as $datum=>$data)
      {
        $juldate=db2jul($datum);
        if($juldate > $maandPeriode)
        { //echo date("Y-m-d",mktime(0,0,0,1,1,substr($datum,0,4)))." $datum <br>\n";

          /*
           if($periode=='jaar2')
           {
              if(date("Y",db2jul($this->meerjarenStart))==substr($datum,0,4))
                $start=substr($this->meerjarenStart,0,10);
              else
                $start=date("Y-m-d",mktime(0,0,0,1,1,substr($datum,0,4)));
              $perfIndex=$this->fondsPerf($indexLookup[$cat],$start,$datum);
           }
           else
          */

          if($data['eindwaarde'] <> 0)
            $indexStart=true;

          if($indexStart==true)
            $perfIndex=$this->fondsPerf($indexLookup[$cat],date("Y-m-d",mktime(0,0,0,substr($datum,5,2),0,substr($datum,0,4))),$datum);
          else
            $perfIndex = 0;
          //$perfIndexCum+=$perfIndex;// ($perfIndexCum  * (1+$perfIndex)) ;
          $perfIndexCum= ((1+$perfIndexCum)  * (1+$perfIndex))-1;
          //echo '('.(1+$perfIndexCum).' * '.(1+$perfIndex).')-1 ='. $perfIndexCum.' '.$perfIndex."<br>\n";
          if($this->pdf->debug==true)
          {
            echo "<b> cululatief ".round($perfIndexCum*100,4)."</b><br>\n";
          }
          $data['specifiekeIndex']=$perfIndexCum*100;//($perfIndexCum-1)*100;
          //$data['specifiekeIndex']=($perfIndexCum)*100;
          $hcatWaarden['periode'][$cat]['portefeuille'][]=$data['index']-100;
          $hcatWaarden['periode'][$cat]['specifiekeIndex'][]=$data['specifiekeIndex'];
          if($periode=='jaar')
            $hcatWaarden['periode'][$cat]['datum'][]= date("M y",$juldate);
          else
            $hcatWaarden['periode'][$cat]['datum'][]= date("M",$juldate);
          $hcatWaarden['periode'][$cat]['waarde'][]=$data;
        }
      }
    }
    $chartData=$hcatWaarden;

    if(count($hcatDataJarenShort) > 0)
      $hcatWaarden=$hcatDataJarenShort;
    foreach ($categorien as $cat)
    {
      $perfIndexCum=1;
      foreach ($hcatWaarden[$cat]['perfWaarden'] as $datum=>$data)
      {
        $juldate=db2jul($datum);
        if($juldate > $maandPeriode)
        { //echo date("Y-m-d",mktime(0,0,0,1,1,substr($datum,0,4)))." $datum <br>\n";
          if($periode=='jaar')
          {
            if(date("Y",db2jul($this->meerjarenStart))==substr($datum,0,4))
              $start=substr($this->meerjarenStart,0,10);
            else
              $start=date("Y-m-d",mktime(0,0,0,1,1,substr($datum,0,4)));
            $perfIndex=$this->fondsPerf($indexLookup[$cat],$start,$datum);
          }
          else
            $perfIndex=$this->fondsPerf($indexLookup[$cat],date("Y-m-d",mktime(0,0,0,substr($datum,5,2),0,substr($datum,0,4))),$datum);
          $perfIndexCum= ($perfIndexCum  * (1+$perfIndex)) ;
          $data['specifiekeIndex']=($perfIndexCum-1)*100;
          $hcatWaarden['periode'][$cat]['portefeuille'][]=$data['index']-100;
          $hcatWaarden['periode'][$cat]['specifiekeIndex'][]=$data['specifiekeIndex'];
          if($periode=='jaar')
            $hcatWaarden['periode'][$cat]['datum'][]= date("Y",$juldate);
          else
            $hcatWaarden['periode'][$cat]['datum'][]= date("M",$juldate);
          $hcatWaarden['periode'][$cat]['waarde'][]=$data;
        }
      }

    }
//listarray($hcatWaarden);
    if($skipTotaal==false)
    {
      $cat='totaal';
      $this->pdf->setWidths($listWidthsFirst);
      $this->pdf->setAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R'));
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $this->pdf->Row(array('periode', 'beginvermogen', 'mutaties','bruto resultaat',' in %','eindvermogen',"cumulatief\nin %",'cumulatief benchmark','verschil','niet toe te rekenen kosten','netto resultaat','in %'));
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      //,'opbrengsten'   $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['gerealiseerd'],2),
      foreach($hcatWaarden['periode'][$cat]['datum'] as $i=>$datum)
      {
        $nettoResultaat=$hcatWaarden['periode'][$cat]['waarde'][$i]['resultaat']+$hcatWaarden['periode'][$cat]['waarde'][$i]['kostenNietGekoppeld'];
        $nettoRendement=$nettoResultaat/$hcatWaarden['periode'][$cat]['waarde'][$i]['gemWaarde']*100;

        $this->pdf->Row(array($datum,
                          $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['beginwaarde'],0),
                          $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['stortEnOnttrekking'],0),
                          $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['resultaat'],0),
                          $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['procent']*100,2),
                          $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['eindwaarde'],0),
                          $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['index']-100,2),
                          $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['specifiekeIndex'],2),
                          $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['index']-100-$hcatWaarden['periode'][$cat]['waarde'][$i]['specifiekeIndex'],2),
                          $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['kostenNietGekoppeld'],0),
                          $this->formatGetal($nettoResultaat,0),
                          $this->formatGetal($nettoRendement,2)
                        ));
      }
      $this->pdf->setXY(60,110);
      $this->LineDiagram(150, 60, $chartData['periode'][$cat],array(array(87,165,25),array(0,52,121)),0,0,6,5,$periode);//50

      return ''; //Klaar
      if($periode=='jaar')
        $this->pdf->rapport_titel = "Historisch rendement per beleggingscategorie versus bijbehorende benchmark";
      else
        $this->pdf->rapport_titel = "Rendement per beleggingscategorie over lopende jaar versus bijbehorende benchmark";

      $this->pdf->addPage();
    }
    $this->pdf->templateVars['PERFG2Paginas']=$this->pdf->page;
    $this->pdf->ln();
    $this->pdf->setWidths($listWidths);

    // $categorien=array('ZAK','VAR');
    $tmp=array();
    foreach($categorien as $cat)
      if($cat <> 'totaal')
        $tmp[]=$cat;
    $categorien=$tmp;
    $n=0;
    foreach ($categorien as $cat)
    {
      if($n>1)
      {
        $this->pdf->addPage();
        $n=0;
      }
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $this->pdf->setWidths(array(150));
      $this->pdf->Row(array($hCategorieOmschrijvingen[$cat]));
      $this->pdf->setWidths($listWidths);
      $this->pdf->setAligns(array('L','R','R','R','R','R','R','R','R','R','R'));
      $this->pdf->Row(array('periode', 'beginvermogen', 'mutaties','inkomsten uit beleggingen','eindvermogen','rendement',' in %','cumu. in %','cumu. bench','verschil'));
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      foreach($hcatWaarden['periode'][$cat]['datum'] as $i=>$datum)
      {
        $this->pdf->Row(array($datum,
                          $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['beginwaarde'],0),
                          $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['stortEnOnttrekking'],0),
                          $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['gerealiseerd'],0),
                          $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['eindwaarde'],0),
                          $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['resultaat'],0),
                          $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['procent']*100,2),
                          $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['index']-100,2),
                          $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['specifiekeIndex'],2),
                          $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['index']-100-$hcatWaarden['periode'][$cat]['waarde'][$i]['specifiekeIndex'],2)
                        ));
      }
      if($this->pdf->getY()<120)
        $this->pdf->setXY(220,40);
      else
        $this->pdf->setXY(220,120);
      $this->LineDiagram(70, 60, $chartData['periode'][$cat],array(array(87,165,25),array(0,52,121)),0,0,6,5,$periode);//50
      $this->pdf->ln();
      $this->pdf->setY(120);
      $n++;
    }


//  $this->pdf->setXY(220,120);
//  $this->LineDiagram(70, 60, $chartData['periode']['VAR'],array(array(87,165,25),array(0,52,121)),0,0,6,5,$periode);//50
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }


  function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$periode='maand')
  {
    global $__appvar;

    $legendDatum= $data['datum'];
    $data1 = $data['specifiekeIndex'];
    $data = $data['portefeuille'];
    $legendaItems= $data['legenda'];




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

    $this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.2);

    $this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

    if ($maxVal == 0)
    {
      $maxVal = ceil(max($bereikdata));
      if ($maxVal < 0)
        $maxVal = 1;
    }
    if ($minVal == 0)
    {
      $minVal = floor(min($bereikdata));
      if ($minVal > 0)
        $minVal =-1;
    }

    $minVal = floor(($minVal-1) * 1.1);
    $maxVal = ceil(($maxVal+1) * 1.1);
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / count($data);

    if($periode=='maand')
      $unit = $lDiag / 12;

    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
      $xpos = $XDiag + $verInterval * $i;

    $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
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
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
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
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");

      $n++;
      if($n >20)
        break;
    }
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    $jaren=ceil(count($data1)/12);
    for ($i=0; $i<count($data); $i++)
    {
      if($i%$jaren==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$i],25);
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
      if ($i>0)
        $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color);
      if ($i==count($data1)-1)
        $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color);
      $yval = $yval2;
    }

    
    if(is_array($data1))
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color1);

      for ($i=0; $i<count($data1); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        if ($i>0)
          $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color1);
        if ($i==count($data1)-1)
          $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color1);

        $yval = $yval2;
      }
    }
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));


    //   $XPage
    // $YPage

    $legendaItems=array('portefeuille','benchmark');
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

  function fondsPerf($fonds,$van,$tot)
  {
    $DB=new DB();
    $query="SELECT fonds,percentage FROM benchmarkverdeling WHERE benchmark='$fonds'";
    $DB->SQL($query);
    $DB->Query();
    $verdeling=array();
    while($data=$DB->nextRecord())
      $verdeling[$data['fonds']]=$data['percentage'];

    if(count($verdeling)==0)
      $verdeling[$fonds]=100;

    $totalPerf=0;
    foreach($verdeling as $fonds=>$percentage)
    {
      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '".substr($tot,0,4)."-01-01' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
      $DB->SQL($query);
      $janKoers=$DB->lookupRecord();
      
      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$van' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
      $DB->SQL($query);
      $startKoers=$DB->lookupRecord();

      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$tot' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
      $DB->SQL($query);
      $eindKoers=$DB->lookupRecord();
      //$perfVoorPeriode=($startKoers['Koers'] - $janKoers['Koers']) / ($janKoers['Koers']);
      //$perfJaar=($eindKoers['Koers'] - $janKoers['Koers']) / ($janKoers['Koers']);
      //$perf=$perfJaar-$perfVoorPeriode;
      $perf=($eindKoers['Koers'] - $startKoers['Koers']) / ($startKoers['Koers']);
      
      if($this->pdf->debug==true)
      {
        echo "koers $fonds ".substr($tot,0,4)."-01-01 ".$janKoers['Koers']."<br>\n";
        echo "koers $fonds $van ".$startKoers['Koers']."<br>\n";
        echo "koers $fonds $tot ".$eindKoers['Koers']."<br>\n";
        echo "perf voor begin $perfVoorPeriode = (".$startKoers['Koers']." - ".$janKoers['Koers'].") / (".$janKoers['Koers'].")<br>\n";
        echo "Perf tot einddatum $perfJaar =(".$eindKoers['Koers']." - ".$janKoers['Koers'].") / ".($janKoers['Koers'])."<br>\n";
        echo "m<b> $fonds $van,$tot  $perf </b>= ( $perfJaar - $perfVoorPeriode ) <br>\n";
      }
      $totalPerf+=($perf*$percentage/100);
    }
    //echo "t $fonds $totalPerf $van,$tot<br>\n";

    return $totalPerf;
  }

}
?>
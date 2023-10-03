<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/04/01 16:54:10 $
 		File Versie					: $Revision: 1.17 $

 		$Log: RapportOIS_L51.php,v $
 		Revision 1.17  2020/04/01 16:54:10  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2020/02/05 17:12:48  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2019/10/30 16:45:39  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2019/10/19 08:15:15  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2019/10/18 17:40:37  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2019/03/23 17:05:54  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2019/01/23 16:27:16  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2019/01/20 12:14:00  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2018/11/04 11:45:31  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2018/11/04 11:15:32  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2018/11/01 07:15:15  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2018/10/31 17:23:34  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/02/10 18:09:12  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2018/01/28 11:45:33  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2018/01/28 09:22:18  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2018/01/27 17:31:22  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2017/12/30 16:38:17  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2017/11/05 13:37:27  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2017/11/04 17:40:21  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2017/10/14 17:27:54  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2012/11/10 15:42:19  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2012/10/31 16:59:18  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2012/05/12 15:11:00  rvv
 		*** empty log message ***
 		
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");

//ini_set('max_execution_time',60);
class RapportOIS_L51
{
	function RapportOIS_L51($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Vermogensoverzicht per maandultimo";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->excelData=array();
    $this->index=new indexHerberekening();

    $vanaf=(substr($rapportageDatum,0,4)-1).substr($rapportageDatum,4,6);
    $vanafJul=db2jul($vanaf);
    $pstartJul=db2jul($this->pdf->PortefeuilleStartdatum);
    if($pstartJul>$vanafJul)
      $vanafJul=$pstartJul;

    $this->perioden=$this->index->getMaanden($vanafJul,$this->pdf->rapport_datum);
    $this->portefeuilles=array();
	}

  function formatGetal($waarde, $dec,$procent=false,$toonNul=false)
  {
    if ($waarde===null)
      return '';
    if($waarde==0 && $toonNul==false)
      return '';
    $data=number_format($waarde,$dec,",",".");
    if($procent==true)
      $data.="%";
    return $data;
  }



	function writeRapport()
  {
    global $__appvar;
    $this->pdf->AddPage();
    $this->pdf->templateVars['OISPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['OISPaginas']=$this->pdf->rapport_titel;
    // print categorie headers
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

    if(is_array($this->pdf->portefeuilles))
      $this->portefeuilles=$this->pdf->portefeuilles;
    else
      $this->portefeuilles=array($this->portefeuille);

    $portefeuilleWaarden=array();
    $totaalOpDatum=array();
    $meetPunten=array();
    $waardenPerDepotbank=array();
    $depotbankPerPortefeuille=array();
    $hoofdcategorieOmschrijving=array('Geen'=>'Geen');
    $depotbankOmschrijving=array('Totaal'=>'Totaal');
    $db=new DB();
  
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $db->SQL($q);
    $db->Query();
    $kleuren = $db->LookupRecord();
    $allekleuren = unserialize($kleuren['grafiek_kleur']);
    $grafiekData=array();
    
    foreach($this->portefeuilles as $portefeuille)
    {
      $query="SELECT Portefeuilles.Depotbank,Depotbanken.Omschrijving FROM Portefeuilles JOIN Depotbanken ON Portefeuilles.Depotbank=Depotbanken.Depotbank WHERE portefeuille='$portefeuille' limit 1";
      $db->SQL($query);
      $depot=$db->lookupRecord();
      $waardenPerDepotbank[$depot['Depotbank']]=array();
      $totaalPerDepotbank[$depot['Depotbank']]=0;
      $depotbankPerPortefeuille[$portefeuille]=$depot['Depotbank'];
      if($depot['Omschrijving']=='')
        $depot['Omschrijving']=$depot['Depotbank'];
      $depotbankOmschrijving[$depot['Depotbank']]=$depot['Omschrijving'];
    }
    foreach($this->perioden as $periode)
    {
      $meetPunten[$periode['start']]=$periode['start'];
      $meetPunten[$periode['stop']]=$periode['stop'];

      $totaalOpDatum[$periode['start']]=0;
      $totaalOpDatum[$periode['stop']]=0;
    }
  //listarray($depotbankPerPortefeuille);listarray($depotbankOmschrijving);
    $grafiekVolgorde=array();
    $portefeuillesBehouden=array();
    $portefeuillesVerwijderen=array();
    foreach($this->portefeuilles as $portefeuille)
    {

      
      foreach($meetPunten as $meetDatum)
      {
          if(substr($meetDatum,5,5)=='01-01')
            $beginJaar=true;
          else
            $beginJaar=false;

          $portefeuilleWaarden[$portefeuille][$meetDatum]['beleggingen']=0;
          $portefeuilleWaarden[$portefeuille][$meetDatum]['liquiditeiten']=0;
          $gegevens = berekenPortefeuilleWaarde($portefeuille, $meetDatum,$beginJaar);
          foreach ($gegevens as $waarde)
          { 
            if($meetDatum==$this->rapportageDatum)
            {
              if ($waarde['beleggingscategorie'] == '')
                $waarde['beleggingscategorie'] = 'Geen';
              else
              {
                $hoofdcategorieOmschrijving[$waarde['beleggingscategorie']]=$waarde['beleggingscategorieOmschrijving'];
              }

              $grafiekVolgorde[$waarde['beleggingscategorie']]=$waarde['beleggingscategorieVolgorde'];
              $waardenPerDepotbank[$depotbankPerPortefeuille[$portefeuille]][$waarde['beleggingscategorie']] += $waarde['actuelePortefeuilleWaardeEuro'];
              $totaalPerDepotbank[$depotbankPerPortefeuille[$portefeuille]] += $waarde['actuelePortefeuilleWaardeEuro'];
              $waardenPerDepotbank['Totaal'][$waarde['beleggingscategorie']] += $waarde['actuelePortefeuilleWaardeEuro'];
              $totaalPerDepotbank['Totaal'] += $waarde['actuelePortefeuilleWaardeEuro'];
            }
            if($waarde['fonds'] <> '')
              $portefeuilleWaarden[$portefeuille][$meetDatum]['beleggingen'] += $waarde['actuelePortefeuilleWaardeEuro'];
            else
              $portefeuilleWaarden[$portefeuille][$meetDatum]['liquiditeiten'] += $waarde['actuelePortefeuilleWaardeEuro'];

            $totaalOpDatum[$meetDatum]+=$waarde['actuelePortefeuilleWaardeEuro'];
          }
  
        if($portefeuilleWaarden[$portefeuille][$meetDatum]['beleggingen'] <> 0 || $portefeuilleWaarden[$portefeuille][$meetDatum]['liquiditeiten']<>0)
        {
          $portefeuillesBehouden[$portefeuille]=$portefeuille;
        }
      }
    }
    
    foreach($portefeuilleWaarden as $portefeuille=>$pdata)
    {
      if(!in_array($portefeuille,$portefeuillesBehouden))
      {
        $portefeuillesVerwijderen[]=$portefeuille;
      }
    }
    foreach($portefeuillesVerwijderen as $portefeuille)
    {
      unset($portefeuilleWaarden[$portefeuille]);
    }
    
    asort($grafiekVolgorde);

    if(!isset($allekleuren['OIB']['Geen']))
      $allekleuren['OIB']['Geen']=array('R'=>array('value'=>10),'G'=>array('value'=>10),'B'=>array('value'=>110));
    foreach($waardenPerDepotbank as $depotbank=>$categorieData)
    {
      foreach($grafiekVolgorde as $categorie=>$volgordeInt)
      {
        if(isset($categorieData[$categorie]))
        {
           $waarde=$categorieData[$categorie];
            $kleur = $allekleuren['OIB'][$categorie];

            $percentage = $waarde / $totaalPerDepotbank[$depotbank] * 100;
            $grafiekData[$depotbank]['Percentage'][vertaalTekst($hoofdcategorieOmschrijving[$categorie],$this->pdf->rapport_taal). ' (' . $this->formatGetal($percentage, 1) . '%)'] = $percentage;
            $grafiekData[$depotbank]['Kleur'][] = array($kleur['R']['value'], $kleur['G']['value'], $kleur['B']['value']);

        }
      }
    }

    //listarray($waardenPerDepotbank);
   // listarray($this->portefeuilles);
   // listarray($meetPunten);
   // listarray($portefeuilleWaarden);
    
    $portw=25;
    $this->pdf->widthB = array(70,$portw,$portw,$portw,$portw,$portw,$portw,$portw,$portw,$portw,$portw);
    $this->pdf->alignB = array('L','R','R','R','R','R','R','R','R','R','R','R');

    $this->pdf->setAligns($this->pdf->alignB);
    $this->pdf->setWidths($this->pdf->widthB);

    $this->pdf->templateVars['OISPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['OISPaginas']=$this->pdf->rapport_titel;

    $maxMeetpunten=7;
    $portefeuilleWaardenRegels=array();
    foreach($portefeuilleWaarden as $portefeuille=>$meetpuntData)
    {
      $i=1;
      $block=0;
      $oldBlock=0;
      foreach($meetpuntData as $meetpunt=>$waarden)
      {
        if($oldBlock<>$block)
        {
          $portefeuilleWaardenRegels[$block][$portefeuille]['beleggingen'][0]=null;
          $portefeuilleWaardenRegels[$block][$portefeuille]['liquiditeiten'][0]=null;
          $i++;
        }
        $oldBlock=$block;

        $portefeuilleWaardenRegels[$block][$portefeuille]['beleggingen'][$meetpunt]=$waarden['beleggingen'];
        $portefeuilleWaardenRegels[$block][$portefeuille]['liquiditeiten'][$meetpunt]=$waarden['liquiditeiten'];

        if($i%$maxMeetpunten==0)
          $block++;
        $i++;
      }
    }
    $vermogenspositieTotaal=array();
    foreach($portefeuilleWaarden as $portefeuille=>$meetpuntData)
    {
      $i=1;
      $block=0;
      $vorigeMaand=0;
      $oldBlock=0;
      foreach($meetpuntData as $meetpunt=>$waarden)
      {
        if($oldBlock<>$block)
        {
          $portefeuilleWaardenRegels[$block]['totaal']['totaal'][0]=null;
          $portefeuilleWaardenRegels[$block]['totaal']['vermogenspositie'][0]=null;
          $i++;
        }
        $oldBlock=$block;
        $portefeuilleWaardenRegels[$block]['totaal']['totaal'][$meetpunt]=$totaalOpDatum[$meetpunt];
        $vermogensPositieDelta=round(($totaalOpDatum[$meetpunt] - $vorigeMaand) / $vorigeMaand * 100, 6);
        $portefeuilleWaardenRegels[$block]['totaal']['vermogenspositie'][$meetpunt]=$vermogensPositieDelta;

        if(!isset($vermogenspositieTotaal['meetpunten'][$meetpunt]))
        {
          $vermogenspositieTotaal['meetpunten'][$meetpunt]=$vermogensPositieDelta;
          $vermogenspositieTotaal['som'] +=$vermogensPositieDelta;
        }
        $vorigeMaand=$totaalOpDatum[$meetpunt];
        if($i%$maxMeetpunten==0)
          $block++;
        $i++;
      }
    }
//listarray($portefeuilleWaardenRegels);
    $blockHeader=array();
    foreach($portefeuilleWaardenRegels as $block=>$portefeuilleData)
    { //echo $this->pdf->getY();ob_flush();
      if($this->pdf->getY()>120)
        $this->pdf->addPage();
       $rows=array();
       $aantalPortefeuilles=count($portefeuilleData);
       $i=1;
       foreach($portefeuilleData as $portefeuille=>$categorieData)
       {
  
         
         $query="SELECT Portefeuilles.Clientvermogensbeheerder FROM Portefeuilles WHERE portefeuille='$portefeuille' limit 1";
         $db->SQL($query);
         $client=$db->lookupRecord();
         if($client['Clientvermogensbeheerder']<>'')
         {
           $portefeuilleNaam = $client['Clientvermogensbeheerder'];
         }
         else
         {
           $portefeuilleNaam = $portefeuille;
         }
         foreach($categorieData as $categorie=>$meetpuntData)
         {
           $row=array('');
           $style='';
           if($portefeuille=='totaal')
           {
             $style='T';
             if($categorie=='vermogenspositie')
             {
               $row = array(vertaalTekst("Vermogenspositie t.o.v. laatst gerapporteerde maand",$this->pdf->rapport_taal));
             }
           }
           else
           {
             if($categorie=='liquiditeiten')
             {
               $query="SELECT Tenaamstelling FROM Rekeningen WHERE portefeuille='$portefeuille' AND Inactief=0 AND Tenaamstelling <> '' ORDER BY Afdrukvolgorde limit 1";
               $db->SQL($query);
               $tenaamstelling=$db->lookupRecord();
               if($tenaamstelling['Tenaamstelling'] == '')
                 $liqTxt=' '.$categorie;
               else
                 $liqTxt = ' '.$tenaamstelling['Tenaamstelling'];
             }
             else
               $liqTxt=' '.$categorie;
             $row = array($portefeuilleNaam . $liqTxt);
           }
           $blockHeader=array('');
           foreach($meetpuntData as $meetDatum=>$waarde)
           {
             if($meetDatum==0)
               $blockHeader[]='';
             else
               $blockHeader[]=date("d-M-Y",db2jul($meetDatum));
             if($categorie=='vermogenspositie')
             {
               $row[] = $this->formatGetal($waarde, 2, true, true);
               $style='UT';
             }
             else
               $row[]=$this->formatGetal($waarde,2,false,true);
           }
           $rows[]=array($row,$style);
           if($portefeuille=='totaal' && $categorie=='vermogenspositie')
             $rows[]=array(array(''),'');
         }
         if($i<$aantalPortefeuilles)
         {
           $fillBlock=array();
           foreach($blockHeader as $head)
             $fillBlock[]='';
           $rows[] = array($fillBlock, 'BB');
         }
         $i++;
       }
       $this->pdf->CellBorders=array('U','U','U','U','U','U','U','U','U');
       $this->pdf->fillCell=array(1,1,1,1,1,1,1,1);
       $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
       $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
       $this->pdf->SetDrawColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
       $this->pdf->row($blockHeader);
       $this->pdf->SetTextColor(0);
       $this->pdf->SetDrawColor(0);
       $this->pdf->fillCell=array();
       unset($this->pdf->CellBorders);

       foreach($rows as $row)
       {
         if($row[1]=='BB')
         {
           $this->pdf->fillCell=array(1,1,1,1,1,1,1,1);
           $this->pdf->SetFillColor($this->pdf->rapport_regelKleur[0],$this->pdf->rapport_regelKleur[1],$this->pdf->rapport_regelKleur[2]);
         }
         if($row[1]=='T')
         {
           $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
           $this->pdf->CellBorders = array('', 'T', 'T', 'T', 'T', 'T', 'T', 'T', 'T');
         }
         elseif($row[1]=='UT')
         {
           $this->pdf->CellBorders = array(array('U', 'T'), array('U', 'T'), array('U', 'T'), array('U', 'T'), array('U', 'T'), array('U', 'T'), array('U', 'T'), array('U', 'T'));
         }
         else
           unset($this->pdf->CellBorders);
         $this->pdf->row($row[0]);
         $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
         if($row[1]=='BB')
         {
           $this->pdf->fillCell=array();
           $this->pdf->SetFillColor(0);
         }
       }

    }
    
    
    $cellWidth=80;
    $randWidth=(297-($this->pdf->marge*2)-$cellWidth)/2;
    $this->pdf->setWidths(array($randWidth,$cellWidth,$randWidth));
    $this->pdf->setAligns(array('L','C','R'));
    //listarray(array($randWidth,$cellWidth,$randWidth));exit;
    $this->pdf->ln();
    $txt=vertaalTekst('Huidig kapitaal ten op zicht van 12 maanden terug',$this->pdf->rapport_taal).' '.$this->formatGetal($vermogenspositieTotaal['som'],2,false,true).'%';
    $this->pdf->fillCell=array(0,1,0);
    $this->pdf->SetFillColor($this->pdf->rapport_regelKleur[0],$this->pdf->rapport_regelKleur[1],$this->pdf->rapport_regelKleur[2]);
    $this->pdf->CellBorders=array('',array('L','T','U','R'),'');
    $this->pdf->row(array('',$txt,''));
    unset($this->pdf->fillCell);
    $this->pdf->SetFillColor(0);
    
//Graag onderaan het eerste deel van de rapportage een klein kader in het blauw neerzetten met daarin de tekst "Huidig kapitaal ten op zicht van 12 maanden terug ...%".
// Dit aantal procenten is dan de som van de weergegeven percentages als laatste regel van de staatjes.
    
    unset($this->pdf->CellBorders);

   // if($this->pdf->getY()+60 > $this->pdf->PageBreakTrigger)
      $this->pdf->addPage();

    $this->pdf->setXY(20,$this->pdf->getY()+60);
     $trendData= $this->createTrend($totaalOpDatum);
    $this->VBarDiagram(240,50,$totaalOpDatum,$trendData);
    
   // listarray($totaalOpDatum); listarray($trendData);exit;
    
    
    $headerHeight=30;
    $lwb=(297/2)-$this->pdf->marge; //133.5
    $vwh=((210-$headerHeight-$this->pdf->marge)/2+$headerHeight)-$headerHeight;
    $chartsize=55;
    
    //listarray($grafiekData);
    $i=0;
    $ystart=95;
    foreach($grafiekData as $depotbank=>$depotbankData)
    {
      if($i>0 && $i%3==0)
      {
        if($this->pdf->getY()>100)
        {
          $this->pdf->addPage();
          $ystart=30;
        }
        else
        {
          $ystart=95;
        }
        $i=0;
      }


      $this->pdf->setXY($this->pdf->marge+5+90*$i , $ystart);
    //  $legendaStart = $this->correctLegentHeight(count($depotbankData['Percentage']));
      $legendaStart=array($this->pdf->marge+5+90*$i,$ystart+$chartsize+10);
      PieChart_L51($this->pdf, $chartsize, $vwh, $depotbankData['Percentage'], '%l', $depotbankData['Kleur'], vertaalTekst($depotbankOmschrijving[$depotbank], $this->pdf->rapport_taal), $legendaStart);
      $i++;
    }

  }
  
  function correctLegentHeight($regels)
  {
    return array($this->pdf->GetX()+60,$this->pdf->GetY()+ 35 -($regels*4)/2);
    
  }

  function createTrend($totaalOpDatum)
  {
  
    $step=round(max($totaalOpDatum))/10;
    $n=$step;
    $datumMap=array();
    $XArray=array();
    $YArray=array();
    
    
    foreach($totaalOpDatum as $datum=>$waardeEur)
    {
      $XArray[]=$n;
      $YArray[]=$waardeEur;
      $datumMap[]=$datum;
      $n+=$step;
    }
    
    // Now convert to log-scale for X
    $logX = array_map('log', $XArray);
  
    // Now estimate $a and $b using equations from Math World
    $n = count($XArray);
    $square = create_function('$x', 'return pow($x,2);');
    $x_squared = array_sum(array_map($square, $logX));
    $xy = array_sum(array_map(create_function('$x,$y', 'return $x*$y;'), $logX, $YArray));
  
    $bFit = ($n * $xy - array_sum($YArray) * array_sum($logX)) /
      ($n * $x_squared - pow(array_sum($logX), 2));
  
    $aFit = (array_sum($YArray) - $bFit * array_sum($logX)) / $n;
  
    $Yfit = array();
    foreach($XArray as $i=>$x)
    {
      $Yfit[$datumMap[$i]] = $aFit + $bFit * log($x);
    }
    foreach($Yfit as $datum=>$value)
    {
      if(is_nan($value))
        $Yfit[$datum]=0;
    }

    return $Yfit;
  }


  function VBarDiagram($w, $h, $data,$trendData)
  {
    global $__appvar;
    $grafiekPunt = array();

    $minVal=min($data);
    if($minVal<0)
      $minVal=0;
    $maxVal=max($data);
    $maxTrend=max($trendData);
    if($maxTrend>$maxVal)
      $maxVal=$maxTrend;

    $tests=array(0.1,0.11,0.15,0.2,0.3,0.4,0.5,0.6,0.75,0.8,1.0);
    $volgendeTiental=pow(10,ceil(log($maxVal,10)));
    foreach($tests as $factor)
    {
      if($maxVal/$volgendeTiental< $factor)
      {
        $maxVal = $volgendeTiental * $factor;
        break;
      }
    }
    //echo $factor;exit;
    $volgendeTiental=pow(10,ceil(log($minVal,10)));
    foreach(array_reverse($tests) as $factor)
    {
      if(($minVal/$volgendeTiental)*0.8> $factor)
      {
        $minVal = $volgendeTiental * $factor;
        break;
      }
    }

    $minTotal=array();
    $maxTotal=array();
    $legendaPrinted=array();
    foreach ($data as $datum=>$waarde)
    {
      if(substr($datum,4,1)=='-')
        $legenda[$datum] = jul2form(db2jul($datum));
      else
        $legenda[$datum] = '';
    }

    $numBars=14;
    $color=array($this->pdf->rapport_regelKleur[0],$this->pdf->rapport_regelKleur[1],$this->pdf->rapport_regelKleur[2]);
    $offset=$minVal;
  //  echo "$offset=$maxVal-$minVal; <br>\n";exit;

   // echo "$maxVal $minVal ";exit;
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YstartGrafiek = $YPage - floor($margin * 1);
    $hGrafiek = ($h - $margin * 1);
    $XstartGrafiek = $XPage + $margin * 1 ;
    $bGrafiek = ($w - $margin * 1) ; // - legenda

    if($minVal < 0)
    {
      $unit = $hGrafiek / (-1 * $minVal + $maxVal) * -1;
      $nulYpos =  $unit * (-1 * $minVal);
    }
    else
    {
      $unit = $hGrafiek / ($maxVal-$minVal) * -1;
    //  $nulYpos =0;
      $nulYpos =  $unit * (-1 * $minVal);
    }


    $horDiv = 5;
    $bereik = $hGrafiek/$unit;

    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);

    $stapgrootte = ceil(abs($bereik)/$horDiv);
    $top = $YstartGrafiek-$h;
    $bodem = $YstartGrafiek;
    $absUnit =abs($unit);

    $nulpunt = $YstartGrafiek ;//+ $nulYpos;



    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
     // if($i>$bodem)
     //   continue;

      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
      {
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte + $offset,0),0,0,'R');
      }
      $n++;
      if($n >20)
        break;
    }



    if($numBars > 0)
      $this->pdf->NbVal=$numBars;

    $vBar = ($bGrafiek / ($this->pdf->NbVal));
    $bGrafiek = $vBar * ($this->pdf->NbVal);
    $eBaton = ($vBar * 50 / 100);


    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.2);

    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $i=0;
    $lastLijnY=0;
    $lastLijnX=0;
    foreach ($data as $datum=>$waarde)
    {
      //echo $datum.' '. count($data)."<br>\n";
      // listarray($data);
      $aantal=count($data);
      $n=1;

        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
        //Bar
        $xval = $XstartGrafiek + (0.5 + $i ) * $vBar - $eBaton / 2;
        $lval = $eBaton;
        $yval = $YstartGrafiekLast[$datum] ;//+ $nulYpos
        $hval = ($waarde * $unit) + $nulYpos ;

        $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,array($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']));
  
        $lijnX = $XstartGrafiek + (0.5 + $i) * $vBar;
        $lijnY = ($trendData[$datum] * $unit) + $YstartGrafiek +$nulYpos; //listarray($lijnY);
        if($i>0)
        {
          $this->pdf->Line($lastLijnX, $lastLijnY, $lijnX, $lijnY,array('dash' => "2,2",'width' => 0.5,'color'=>array($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b'])));
        }
        $lastLijnX = $lijnX;
        $lastLijnY = $lijnY;
      //  $this->pdf->Line($xval+$lval,$yval,$xval+$lval,$yval+$hval);
  
      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);
      
        if($aantal==$n)
          $this->pdf->Line($xval,$yval+$hval,$xval+$lval,$yval+$hval);

        $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
        $this->pdf->SetTextColor(255,255,255);
        if(abs($hval) > 3)
        {
          $this->pdf->SetXY($xval, $yval+($hval/2)-2);
          $this->pdf->Cell($eBaton, 4, number_format($waarde/1000,0,',','.')."k",0,0,'C');
        }
        $this->pdf->SetTextColor(0,0,0);

        if($legendaPrinted[$datum] != 1)
          $this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+4,$legenda[$datum],0);

        if($grafiekPunt[$categorie][$datum])
        {
          $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']));
          if($lastX)
            $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
          $lastX = $xval+.5*$eBaton;
          $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
        }
        $legendaPrinted[$datum] = 1;

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
        $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
        $lval = $eBaton;
        $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
        $hval = ($val * $unit);

        $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
        $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
        $this->pdf->SetTextColor(255,255,255);
        if(abs($hval) > 3)
        {
          $this->pdf->SetXY($xval, $yval+($hval/2)-2);
          $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
        }
        $this->pdf->SetTextColor(0,0,0);

        if($grafiekPunt[$categorie][$datum])
        {
          $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(194,179,157));
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

}
?>
<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/08/21 15:33:29 $
 		File Versie					: $Revision: 1.3 $

 		$Log: RapportHUIS_L84.php,v $
 		Revision 1.3  2019/08/21 15:33:29  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2019/07/17 15:36:13  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2019/07/06 15:40:47  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2019/07/05 16:47:00  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2017/02/11 17:30:10  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/10/23 11:32:33  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2016/09/18 12:07:30  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2016/04/10 15:48:34  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2016/02/15 06:56:41  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2016/02/13 14:02:39  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2016/01/09 18:58:30  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2015/05/27 11:57:58  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/09/17 15:16:31  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/05/03 15:47:40  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/01/11 15:46:35  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/01/08 16:55:22  rvv
 		*** empty log message ***
 		

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L56.php");
include_once("rapport/include/ATTberekening_L55.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");



class RapportHUIS_L84
{
	function RapportHUIS_L84($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "HUIS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->jaarData=array();
    $this->periodeData=array();
    $this->grafiekDataYtdPerf=array();
    $this->att=new ATTberekening_L56($this);
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	
	function writeRapport()
	{

    $this->pdf->AddPage('P');
    $this->toonAlgemeen();
    $this->getRendement();
    $this->toonVerdeling();
    $this->toonRendement();
    $this->PerformanceYtdGrafiek();
    $this->addRendementPerCategorie();
    $this->ToonAttributieBlok();
    $this->toonRiskDeel();

  }
  
  
  function toonAlgemeen()
  {
    global $__appvar;
    
    $DB=new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    
    $this->pdf->setAligns(array('L','L'));
    $this->pdf->setWidths(array(25,25));
    $this->pdf->setXY($this->pdf->marge,20);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('Algemeen'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('Datum',date('d-m-Y',$this->pdf->rapport_datum)));
    $this->pdf->row(array('Portefeuille',$this->portefeuille));
    $this->pdf->row(array('Valuta',$this->pdf->portefeuilledata['RapportageValuta']));
    $this->pdf->row(array('AUM',$this->formatGetal($totaalWaarde['totaal'],0)));
    $this->pdf->row(array('Beheer',$this->pdf->portefeuilledata['DepotbankOmschrijving']));
  }
  
  function getRendement()
  {
    $index=new indexHerberekening();
    $index->voorStartdatumNegeren=true;
    
    $rapportageJaar=substr($this->rapportageDatum,0,4);
    /*
    $rapportageMaand=substr($this->rapportageDatum,5,2);
    $rapportageDag=substr($this->rapportageDatum,8,2);
  
    $perioden=array('3 maanden'=>mktime(0,0,0,$rapportageMaand-3,$rapportageDag,$rapportageJaar),
                    'ytd'=>mktime(0,0,0,1,1,$rapportageJaar),
                    '1 jaar'=>mktime(0,0,0,$rapportageMaand,$rapportageDag,$rapportageJaar-1),
                    '3 jaar'=>mktime(0,0,0,$rapportageMaand,$rapportageDag,$rapportageJaar-3),
                    '5 jaar'=>mktime(0,0,0,$rapportageMaand,$rapportageDag,$rapportageJaar-5));

    $periodeData=array();
      */
    $jaarData=array();
    $grafiekData=array();
  /*
    foreach($perioden as $periodeNaam=>$periodeJul)
    {
      $periodeData[$periodeNaam]=array();
    }
*/
    $this->indexData = $index->getWaarden($this->pdf->PortefeuilleStartdatum ,$this->rapportageDatum ,$this->portefeuille,$this->pdf->portefeuilledata['SpecifiekeIndex']);

   
    foreach($this->indexData as $maandData)
    {
     
      $jaar=substr($maandData['datum'],0,4);
      $datumJul=db2jul($maandData['datum']);
      /*
      foreach($perioden as $periodeNaam=>$periodeJul)
      {
        if ($datumJul > $periodeJul)
        {
          $periodeData[$periodeNaam]['performance'] = ((1 + $periodeData[$periodeNaam]['performance'] / 100) * (1 + $maandData['performance'] / 100) - 1) * 100;
          $periodeData[$periodeNaam]['specifiekeIndexPerformance'] = ((1 + $periodeData[$periodeNaam]['specifiekeIndexPerformance'] / 100) * (1 + $maandData['specifiekeIndexPerformance'] / 100) - 1) * 100;
        }
      }
      */
      $jaarData[$jaar]['performance']=((1+$jaarData[$jaar]['performance']/100)*(1+$maandData['performance']/100)-1)*100;
      $jaarData[$jaar]['specifiekeIndexPerformance']=((1+$jaarData[$jaar]['specifiekeIndexPerformance']/100)*(1+$maandData['specifiekeIndexPerformance']/100)-1)*100;
  
      if($jaar==$rapportageJaar)
      {
        $grafiekData['Datum'][] = $maandData['datum'];
        $grafiekData['Index'][] = $jaarData[$jaar]['performance'];
        $grafiekData['benchmarkIndex'][] = $jaarData[$jaar]['specifiekeIndexPerformance'];
      }
      
    }
    $this->jaarData=$jaarData;
   // $this->periodeData=$periodeData;
    $this->grafiekDataYtdPerf=$grafiekData;

  }
  
  function toonVerdeling()
  {
    global $__appvar;
  
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $allekleuren = unserialize($kleuren['grafiek_kleur']);
    
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal,
     TijdelijkeRapportage.hoofdcategorieOmschrijving,
TijdelijkeRapportage.valutaOmschrijving,
 TijdelijkeRapportage.hoofdcategorie,
 TijdelijkeRapportage.valuta ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."'"
      .$__appvar['TijdelijkeRapportageMaakUniek']."
GROUP BY
TijdelijkeRapportage.hoofdcategorie,TijdelijkeRapportage.valuta
ORDER BY
TijdelijkeRapportage.hoofdcategorieVolgorde,TijdelijkeRapportage.valutaVolgorde";
    debugSpecial($query,__FILE__,__LINE__);

    $DB->SQL($query);
    $DB->Query();
    $hoofdcategorieTotalen=array();
    $valutaTotalen=array();
    $grafiek=array();
    $totaleWaarde=0;
    while($data = $DB->nextRecord())
    {
      $hoofdcategorieTotalen[$data['hoofdcategorieOmschrijving']]+=$data['totaal'];
      $valutaTotalen[$data['valutaOmschrijving']]+=$data['totaal'];
      $totaleWaarde+=$data['totaal'];
  
      $grafiek['hoofdcategorie']['pieData'][$data['hoofdcategorieOmschrijving']]+= $data['totaal']/$totaalWaarde['totaal'];
      if(!isset( $grafiek['hoofdcategorie']['kleurData'][$data['hoofdcategorieOmschrijving']]))
        $grafiek['hoofdcategorie']['kleurData'][$data['hoofdcategorieOmschrijving']]=$allekleuren['OIB'][$data['hoofdcategorie']];
      $grafiek['hoofdcategorie']['kleurData'][$data['hoofdcategorieOmschrijving']]['percentage']+=($data['totaal']/$totaalWaarde['totaal']*100);
  
      $grafiek['valuta']['pieData'][$data['valutaOmschrijving']]+= $data['totaal']/$totaalWaarde['totaal'];
      if(!isset( $grafiek['valuta']['kleurData'][$data['valutaOmschrijving']]))
        $grafiek['valuta']['kleurData'][$data['valutaOmschrijving']]=$allekleuren['OIV'][$data['valuta']];
      $grafiek['valuta']['kleurData'][$data['valutaOmschrijving']]['percentage']+=($data['totaal']/$totaalWaarde['totaal']*100);
    }
    

    $this->pdf->setXY($this->pdf->marge,50);
    $this->pdf->setWidths(array(60));
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('Verdeling per hoofdcategorie'));
    $this->pdf->setAligns(array('L','R'));
    $this->pdf->setWidths(array(50,20));
    $this->pdf->ln();
    $this->pdf->row(array('Hoofdcategorie','Waarde'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach($hoofdcategorieTotalen as $omschrijving=>$waarde)
    {
      $this->pdf->row(array($omschrijving,$this->formatGetal($waarde,0)));
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('Totaal',$this->formatGetal($totaleWaarde,0)));
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
  
    $this->pdf->setXY(95, 25);
    $this->printPie($grafiek['hoofdcategorie']['pieData'], $grafiek['hoofdcategorie']['kleurData'], 'Hoofdcategorie verdeling', 20, 20);
  
    $this->pdf->setXY(145, 25);
    $this->printPie($grafiek['valuta']['pieData'], $grafiek['valuta']['kleurData'], 'Valuta verdeling', 20, 20);
    
  }
  function toonRendement()
  {
/*
    $this->pdf->setAligns(array('L','R','R'));
    $this->pdf->setWidths(array(25,20,20));
   
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('Rendement'));
    $this->pdf->ln();
    $this->pdf->row(array('Jaar','Portefeuille','Index'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  

    foreach($this->periodeData as $periode=>$perf)
    {
      $this->pdf->row(array($periode,$this->formatGetal($perf['performance'],1)."%",$this->formatGetal($perf['specifiekeIndexPerformance'],1)."%"));
    }
    $this->pdf->ln();
  */
  
    $this->pdf->setXY($this->pdf->marge,150);
    $this->pdf->setWidths(array(60));
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('Rendement per kalenderjaar'));
    $this->pdf->setAligns(array('L','R','R'));
    $this->pdf->setWidths(array(25,20,20));
    $this->pdf->ln();
    $this->pdf->row(array('Jaar','Portefeuille','Index'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach($this->jaarData as $jaar=>$perf)
    {
      $this->pdf->row(array($jaar,$this->formatGetal($perf['performance'],1)."%",$this->formatGetal($perf['specifiekeIndexPerformance'],1)."%"));
    }
    

  }
  
  function PerformanceYtdGrafiek()
  {
    $yStart=90;
    if (count($this->grafiekDataYtdPerf) > 1)
    {
      $this->pdf->SetXY($this->pdf->marge+8, $yStart);//104
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $this->pdf->Cell(0, 5, vertaalTekst('Rendement', $this->pdf->rapport_taal) . ' (' .
                        vertaalTekst('cumulatief', $this->pdf->rapport_taal) . ' ' .
                        vertaalTekst('in', $this->pdf->rapport_taal) . ' %)', 0, 1);
      $this->pdf->Line($this->pdf->marge+8, $this->pdf->GetY(),  76, $this->pdf->GetY());
      $this->pdf->SetXY($this->pdf->marge+8, $yStart + 10);//112
      //function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
      $this->LineDiagram(60, 35, $this->grafiekDataYtdPerf, $this->pdf->rapport_grafiek_color, 0, 0, 6, 5, 1);//50
    }
  }
  
  
  
  function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;
    
    $legendDatum= $data['Datum'];
    $data1 = $data['Index1'];
    $data = $data['Index'];
    if(count($data1)>0)
      $bereikdata = array_merge($data,$data1);
    else
      $bereikdata =   $data;
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $w/12 );
    
    
    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }
    
    //$this->pdf->Rect($XPage, $YPage, $w, $h,'D','',$this->pdf->grafiekAchtergrondKleur);
    
    if($color == null)
      $color=array(140,178,209);
    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
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
    $aantalData=count($data);
    $unit = $lDiag / $aantalData;
    
    if($jaar && count($data)<12)
      $unit = $lDiag / 12;
    
    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
    {
      $xpos = $XDiag + $verInterval * $i;
    }
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);
    
    $stapgrootte = round(abs($maxVal - $minVal)/$horDiv);
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
    $lineStyle = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    //listarray($data);
    // $color=array(200,0,0);
    
    $printLabel=array();
    for ($i=0; $i<count($data); $i++)
    {
      $extrax=($unit*0.1*-1);
      if($i <> 0)
        $extrax1=($unit*0.1*-1);
      
      $maand=date("n",db2jul($legendDatum[$i]));
      if($aantalData <= 13 || $maand==3 || $maand==6 || $maand==9 || $maand==12)
      {
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-3+$unit,$YDiag+$hDiag+8,vertaalTekst($__appvar["Maanden"][$maand],$this->pdf->rapport_taal) ,25);
        $printLabel[$i]=1;
      }
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit+$extrax1, $yval, $XDiag+($i+1)*$unit+$extrax, $yval2,$lineStyle );
      $this->pdf->Rect($XDiag+($i+1)*$unit-0.5+$extrax, $yval2-0.5, 1, 1 ,'F','',$color);
      $this->pdf->Circle($XDiag+($i+1)*$unit+$extrax, $yval2, 1,0,360,'F','',$color);
      $yval = $yval2;
    }
    
    $this->pdf->setTextColor($color[0],$color[1],$color[2]);
    $yTekstStap=2.5;
    /*
    for ($i=0; $i<count($data); $i++)
    {
      if($data[$i]>$data1[$i])
        $yOffset=$yTekstStap*-1;
      else
        $yOffset=3+$yTekstStap;
      
      $extrax=($unit*0.1*-1);
      if($i <> 0)
        $extrax1=($unit*0.1*-1);
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->SetFont($this->pdf->rapport_font, '', 9);
      if($data[$i] <> 0 && $printLabel[$i])
        $this->pdf->Text($XDiag+($i+1)*$unit-1+$extrax,$yval2+$yOffset,$this->formatGetal($data[$i],1));
      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      
    }
    */
    $this->pdf->setTextColor(0);
    
    
    if(is_array($data1))
    {
      $this->pdf->setTextColor($color1[0],$color1[1],$color1[2]);
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 1.0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color1);
      for ($i=0; $i<count($data1); $i++)
      {
        if($data1[$i]>$data[$i])
          $yOffset=$yTekstStap*-1;
        else
          $yOffset=3+$yTekstStap;
        
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color1);
        
        $this->pdf->SetFont($this->pdf->rapport_font, '', 9);
        
        if($data1[$i] <> 0 && $printLabel[$i])
          $this->pdf->Text($XDiag+($i+1)*$unit-1+$extrax,$yval2+$yOffset,$this->formatGetal($data1[$i],1));
        $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
        
        $yval = $yval2;
      }
      $this->pdf->setTextColor(0);
    }
    
    
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.1, ));
    $this->pdf->SetFillColor(0,0,0);
  }
  
  
  
  function addRendementPerCategorie()
  {
    
    $this->pdf->setY(80);
    if(!isset($this->pdf->__appvar['consolidatie']))
    {
      $this->pdf->__appvar['consolidatie']=1;
      $this->pdf->portefeuilles=array($this->portefeuille);
    }
    $rapParts=explode("-",$this->rapportageDatum);
    
    $kwartaal = ceil(date("n",db2jul($this->rapportageDatum))/3);
    if($kwartaal==1)
      $beginKwartaal=$rapParts[0]."-01-01";
    elseif($kwartaal==2)
      $beginKwartaal=$rapParts[0]."-03-31";
    elseif($kwartaal==3)
      $beginKwartaal=$rapParts[0]."-06-30";
    elseif($kwartaal==4)
      $beginKwartaal=$rapParts[0]."-09-30";
    if(db2jul($beginKwartaal)<db2jul($this->pdf->PortefeuilleStartdatum))
      $beginKwartaal=$this->pdf->PortefeuilleStartdatum;
    
    $vetralingGrootboek=$this->getGrootboeken();
    
    //$att=new ATTberekening_L56($this);
    $this->att->indexPerformance=false;
    $this->waarden['Periode']=$this->att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'Hoofdcategorie',$this->pdf->rapportageValuta );
    $this->waarden['Kwartaal']=$this->att->bereken($beginKwartaal,$this->rapportageDatum,'Hoofdcategorie',$this->pdf->rapportageValuta );
    
    // $categorien=array_keys($this->waarden['Periode']);
    $categorien=array();
    foreach(array_keys($this->att->categorien) as $categorie)
    {
      if($categorie=='totaal')
        continue;
      if($this->waarden['Periode'][$categorie]['procent'] <> 0 || $this->waarden['Periode'][$categorie]['beginwaarde'] <> 0 || $this->waarden['Periode'][$categorie]['eindwaarde'] <> 0)
      {
        $categorien[]=$categorie;
      }
    }
    

    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    // listarray($this->pdf->portefeuilles);
    $fillArray=array(0,1);
    $subOnder=array('','');
    $volOnder=array('U','U');
    $subBoven=array('','');
    $header=array("","");
    $samenstelling=array("",vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal));
    
    foreach($categorien as $categorie)
    {

      $volOnder[]='U';
     // $volOnder[]='U';
      $subOnder[]='U';
    //  $subOnder[]='';
      $subBoven[]='T';
    //  $subBoven[]='';
      $fillArray[]=1;
     // $fillArray[]=1;
      $header[]=substr($this->att->categorien[$categorie],0,16);
     // $header[]='';

      $samenstelling[]='';
      //$samenstelling[]='';
      // $perfWaarden[$portefeuille]=$this->getWaarden($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
    }
    
    /*
    $perbegin=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)));
    $waardeRapdatum=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum)));
    $mutwaarde=array("",vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal));
    $stortingen=array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal));
    $onttrekking=array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal));
    $effectenmutaties=array("",vertaalTekst("Effectenmutaties gedurende verslagperiode",$this->pdf->rapport_taal));
    
    
    $resultaat=array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));
    $rendement=array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal));
    $rendementKwartaal=array("",vertaalTekst("Rendement lopend kwartaal",$this->pdf->rapport_taal));
    
    $ongerealiseerd=array("",vertaalTekst("Ongerealiseerde resultaten",$this->pdf->rapport_taal)); //
    //$ongerealiseerdValuta=array("",vertaalTekst("Ongerealiseerde valutaresultaten",$this->pdf->rapport_taal)); //
    
    $gerealiseerd=array("",vertaalTekst("Gerealiseerde resultaten",$this->pdf->rapport_taal)); //
//$gerealiseerdValuta=array("",vertaalTekst("Gerealiseerde valutaresultaten",$this->pdf->rapport_taal)); //
    $valutaResultaat=array("",vertaalTekst("Koersresultaten vreemde valuta rekeningen",$this->pdf->rapport_taal)); //
    $rente=array("",vertaalTekst("Mutatie opgelopen rente",$this->pdf->rapport_taal));//
    */
    
    ///////
    ///
    $perbegin=array("","");
    $waardeRapdatum=array("","");
    $mutwaarde=array("","");
    $stortingen=array("","");
    $onttrekking=array("","");
    $effectenmutaties=array("","");
  
  
    $resultaat=array("","");
    $rendement=array("","");
    $rendementKwartaal=array("","");
  
    $ongerealiseerd=array("","");
    //$ongerealiseerdValuta=array("",vertaalTekst("Ongerealiseerde valutaresultaten",$this->pdf->rapport_taal)); //
  
    $gerealiseerd=array("","");
//$gerealiseerdValuta=array("",vertaalTekst("Gerealiseerde valutaresultaten",$this->pdf->rapport_taal)); //
    $valutaResultaat=array("","");
    $rente=array("","");
    ///
    ///
    
    
    $totaalOpbrengst=array("","");//totaalOpbrengst
    
    $totaalKosten=array("","");   //totaalKosten
    $totaal=array("","");   //totaalOpbrengst-totaalKosten
    
    
    foreach($categorien as $categorie)
    {
      unset($this->waarden['Periode'][$categorie]['perfWaarden']);
    }
    
    //listarray($this->waarden['Periode']);exit;
    foreach($categorien as $categorie)
    {
      $perfWaarden=$this->waarden['Periode'][$categorie];
      $perbegin[]=$this->formatGetal($perfWaarden['beginwaarde'],0,true);
     // $perbegin[]='';
      $waardeRapdatum[]=$this->formatGetal($perfWaarden['eindwaarde'],0,true);
     // $waardeRapdatum[]='';
      $mutwaarde[]=$this->formatGetal($perfWaarden['eindwaarde']-$perfWaarden['beginwaarde'],0,true);
     // $mutwaarde[]='';
      
      if($categorie=='totaal')
      {
        $perbegin[]='';
        $waardeRapdatum[]='';
        $mutwaarde[]='';
        $effectenmutaties[]='';
      //  $effectenmutaties[]='';
      //  $effectenmutaties[]='';
        //$stort=getStortingen($this->rapport->portefeuille, $datumBegin, $datumEind)
        //$onttr=getOnttrekkingen($this->rapport->portefeuille, $datumBegin, $datumEind)
        $stortingen[]=$this->formatGetal($perfWaarden['storting'],0);
        $stortingen[]='';
      //  $stortingen[]='';
        $onttrekking[]=$this->formatGetal($perfWaarden['onttrekking'],0);
        $onttrekking[]='';
       // $onttrekking[]='';
      }
      else
      {
        $effectenmutaties[]=$this->formatGetal($perfWaarden['stort'],0);
       // $effectenmutaties[]='';
        $stortingen[]='';//'$this->formatGetal($perfWaarden['kosten'],0);
       // $stortingen[]='';
        $onttrekking[]='';//$this->formatGetal($perfWaarden['opbrengst'],0);
       // $onttrekking[]='';
      }
      
      $totaalOpbrengstEUR=$perfWaarden['opbrengst']+
        $perfWaarden['ongerealiseerdFondsResultaat']+
        $perfWaarden['ongerealiseerdValutaResultaat']+
        $perfWaarden['gerealiseerdFondsResultaat']+
        $perfWaarden['gerealiseerdValutaResultaat']+
        $perfWaarden['opgelopenrente'];
      
      $perfWaarden['resultaatValuta']=$perfWaarden['resultaat']-($totaalOpbrengstEUR+$perfWaarden['kosten']);
      $totaalOpbrengstEUR+=$perfWaarden['resultaatValuta'];
      
      $resultaat[]=$this->formatGetal($perfWaarden['resultaat'],0);
     // $resultaat[]='';
      
      if($categorie=='Liquiditeiten')
      {
        $rendement[]='';
      //  $rendement[]='';
        $rendementKwartaal[]='';
       // $rendementKwartaal[]='';
      }
      else
      {
        $rendement[]=$this->formatGetal($perfWaarden['procent'],2).'%';
       // $rendement[]='%';
        $rendementKwartaal[]=$this->formatGetal($this->waarden['Kwartaal'][$categorie]['procent'],2).'%';
       // $rendementKwartaal[]='%';
      }
      
      if($categorie=='totaal')
      {
        $resultaat[]='';
        $rendement[]='';
        $rendementKwartaal[]='';
        $ongerealiseerd[]=$this->formatGetal($perfWaarden['ongerealiseerdFondsResultaat']+$perfWaarden['ongerealiseerdValutaResultaat'],0);
        $ongerealiseerd[]='';
        $ongerealiseerd[]='';
        //$ongerealiseerdValuta[]=$this->formatGetal($perfWaarden['ongerealiseerdValutaResultaat'],0);
        //$ongerealiseerdValuta[]='';
        $gerealiseerd[]=$this->formatGetal($perfWaarden['gerealiseerdFondsResultaat']+$perfWaarden['gerealiseerdValutaResultaat'],0);
        $gerealiseerd[]='';
        $gerealiseerd[]='';
        //$gerealiseerdValuta[]=$this->formatGetal($perfWaarden['gerealiseerdValutaResultaat'],0);
        //$gerealiseerdValuta[]='';
        $valutaResultaat[]=$this->formatGetal($perfWaarden['resultaatValuta'],0);
        $valutaResultaat[]='';
        $valutaResultaat[]='';
        $rente[]=$this->formatGetal($perfWaarden['opgelopenrente'],0);
        $rente[]='';
        $rente[]='';
        //$totaalOpbrengst[]='';
        //$totaalOpbrengst[]='';
        $totaalOpbrengst[]=$this->formatGetal($totaalOpbrengstEUR,0);
        $totaalOpbrengst[]='';
        $totaalOpbrengst[]='';
        //$totaalKosten[]='';
        // $totaalKosten[]='';
        $totaalKosten[]=$this->formatGetal($perfWaarden['kosten'],0);
        $totaalKosten[]='';
        $totaalKosten[]='';
        // $totaal[]='';
        //$totaal[]='';
        $totaal[]=$this->formatGetal($perfWaarden['resultaat'],0);
        $totaal[]='';
        $totaal[]='';
        
        foreach($perfWaarden['grootboekOpbrengsten'] as $categorie=>$waarde)
          if(round($waarde,2)!=0.00)
            $opbrengstCategorien[$categorie]=$categorie;
        foreach($perfWaarden['grootboekKosten'] as $categorie=>$waarde)
          if(round($waarde,2)!=0.00)
            $kostenCategorien[$categorie]=$categorie;
      }
      
    }
    
    $celWidth=25;
    $this->pdf->widthB = array(40,40,$celWidth,$celWidth,$celWidth,$celWidth,$celWidth,$celWidth,$celWidth,$celWidth);
    $this->pdf->alignB = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R');
    $this->pdf->widthA = $this->pdf->widthB;//array(0,65,30,6,30,6,30,6,30,6,30,6,30,6);
    $this->pdf->alignA = $this->pdf->alignB;//array('L','L','R','L','R','L','R','L','R','L','R','L','R','L','R');


//listarray($perfWaarden);
    
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);

//    $this->pdf->fillCell=$fillArray;
//    $this->pdf->SetTextColor(255,245,245);


//    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
//    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
//		$this->pdf->Rect($this->pdf->marge+70, $this->pdf->getY(), (count($header)-2)*15, 8 , 'F');
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row($header);
    //  unset($this->pdf->fillCell);
//    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
//    $this->pdf->fillCell=array();
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    
    $this->pdf->row($perbegin);
    //,$this->formatGetal($data['periode']['waardeBegin'],2,true),"",$this->formatGetal($data['ytm']['waardeBegin'],2,true),""));
    $this->pdf->CellBorders = $subOnder;
    $this->pdf->row($waardeRapdatum);//$this->formatGetal($data['periode']['waardeEind'],0),"",$this->formatGetal($data['ytm']['waardeEind'],0),""));
    $this->pdf->CellBorders = array();
    // subtotaal

    $this->pdf->ln();
    $this->pdf->row($mutwaarde);//,$this->formatGetal($data['periode']['waardeMutatie'],0),"",$this->formatGetal($data['ytm']['waardeMutatie'],0),""));
    $this->pdf->row($stortingen);////,$this->formatGetal($data['periode']['stortingen'],0),"",$this->formatGetal($data['ytm']['stortingen'],0),""));
    $this->pdf->row($onttrekking);//,$this->formatGetal($data['periode']['onttrekkingen'],0),"",$this->formatGetal($data['ytm']['onttrekkingen'],0),""));
    $this->pdf->CellBorders = $subOnder;
    $this->pdf->row($effectenmutaties);
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row($resultaat);//,$this->formatGetal($data['periode']['resultaatVerslagperiode'],0),"",$this->formatGetal($data['ytm']['resultaatVerslagperiode'],0),""));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->ln();
    
    $this->pdf->CellBorders = array();
    //$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    //$this->pdf->CellBorders = $volOnder;
    $this->pdf->row($rendementKwartaal);
    $this->pdf->row($rendement);//,$this->formatGetal($data['periode']['rendementProcent'],0),"%",$this->formatGetal($data['ytm']['rendementProcent'],0),"%"));
    //$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();

    
    
    
  }
  
  function getGrootboeken()
  {
    $vertaling=array();
    $db=new DB();
    $query="SELECT Grootboekrekening,Omschrijving FROM Grootboekrekeningen";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      if($data['Grootboekrekening']=='BEW')
        $data['Omschrijving']="Administratiekosten bank";
      if($data['Grootboekrekening']=='KOST')
        $data['Omschrijving']="Transactiekosten bank";
      
      $vertaling[$data['Grootboekrekening']]=$data['Omschrijving'];
    }
    return $vertaling;
  }
  
  function ToonAttributieBlok()
  {
  
    $att=new ATTberekening_L55($this);
    // $hcatData=$att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum);
    $att->indexPerformance=true;
    $this->waarden['Periode']=$att->bereken($this->rapportageDatumVanaf,  $this->rapportageDatum);
    $this->waarden['Jaar']=$att->bereken(substr($this->rapportageDatum,0,4).'-01-01',  $this->rapportageDatum);
    // $this->tweedePerformanceStart.' '.$this->rapportageDatumVanaf.' '. $this->rapportageDatum."<br>\n";exit;
    //listarray($this->waarden['Periode']);
    //listarray($this->waarden['Jaar']);
  
    //Benchmark performance stapelen
    $indexBijdrage=array();
    $indexTotaal=0;
    foreach ($this->waarden['Jaar'] as $categorie=>$categorieData)
    {
      if($categorie<>'totaal')
      {
        foreach ($categorieData['perfWaarden'] as $maand=>$maandWaarden)
        {
          if($maand <> '')
          {
            $indexBijdrage[$maand]+=$maandWaarden['indexBijdrage']*100;
          
          }
        }
      }
    }
    unset($laatste);
    foreach ($indexBijdrage as $maand=>$indexBijdrage)
    {
      if(!isset($laatste))
        $laatste=0;
      $indexTotaal=((1+$indexBijdrage/100)*(1+$laatste/100)-1)*100;
      $laatste=$indexTotaal;
    }
    unset($laatste);
  
    $this->waarden['Jaar']['totaal']['indexPerf']=$indexTotaal;



//rvv
  
    foreach ($this->waarden['Periode'] as $categorie=>$categorieData)
    {
      if($categorie <> 'totaal')
      {
        foreach ($categorieData['perfWaarden'] as $maand=>$maandWaarden)
        {
          if($maand <> '')
          {
            $totalen[$maand]['allocateEffect']+=($maandWaarden['weging']-$maandWaarden['indexBijdrageWaarde'])*$maandWaarden['indexPerf']*100;
            $totalen[$maand]['selectieEffect']+=($maandWaarden['procent']-$maandWaarden['indexPerf'])*$maandWaarden['weging']*100;
            $totalen[$maand]['portBijdrage']+=$maandWaarden['bijdrage']*100;
            $totalen[$maand]['indexBijdrage']+=$maandWaarden['indexBijdrage']*100;
            $totalen[$maand]['overperfBijdrage']+=$maandWaarden['relContrib']*100;
            // echo "$maand $categorie | ".round($maandWaarden['bijdrage']*100,3)."| -> |".round($totalen[$maand]['portBijdrage'],2)."<br>\n";
            //echo "$categorie  $maand ".$totalen[$maand]['selectieEffect']."= (".$maandWaarden['procent']."-".$maandWaarden['indexPerf'].")*".$maandWaarden['weging']."*100 <br>\n";
          
          }
        }
      }
    }
  
    foreach ($totalen as $maand=>$maandWaarden)
    {
      foreach ($maandWaarden as $veld=>$waarde)
      {
        if(!isset($laatste[$veld]))
          $laatste[$veld]=0;
        $jaarTotalen[$veld]=((1+$maandWaarden[$veld]/100)*(1+$laatste[$veld]/100)-1)*100;
        $laatste[$veld]=$jaarTotalen[$veld];
      }
    }
    unset($laatste);
  
    $this->waarden['Periode']['totaal']['indexPerf']=$jaarTotalen['indexBijdrage'];
    $this->waarden['Periode']['totaal']['procent']=$jaarTotalen['portBijdrage'];
  

    $n=0;
    $this->categorieOmschrijving['totaal']='Totaal';



//rvv uit L35
  
    $stapelTypen=array('procent'); //,'bijdrage'
    $somTypen=array('indexPerf');
    $gemiddeldeTypen=array('weging');
  
    foreach ($this->waarden['Jaar'] as $categorie=>$categorieData)
      $this->jaarTotalen[$categorie]=array();
    foreach ($this->waarden['Jaar'] as $categorie=>$categorieData)
    {
      $laatste=array();
      foreach ($categorieData['perfWaarden'] as $datum=>$waarden)
      {
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
          $this->jaarTotalen[$categorie][$jaar]['allocateEffect']+=($waarden['weging']-$waarden['indexBijdrageWaarde'])*$waarden['indexPerf'];
          $this->jaarTotalen['totaal'][$jaar]['allocateEffect']+=($waarden['weging']-$waarden['indexBijdrageWaarde'])*$waarden['indexPerf'];//wordt gebruikt
        }
        $this->jaarTotalen[$categorie][$jaar]['portBijdrage']+=$waarden['bijdrage'];
        $lastCategorie=$categorie;
      }
    
      foreach ($gemiddeldeTypen as $type)
        $this->jaarTotalen[$categorie][$jaar][$type]=$this->jaarTotalen[$categorie][$jaar][$type]/count($categorieData['perfWaarden']);
    }
//rvv eind uit L35
  
    $totalen=array();
    $totalenCategorie=array();
    unset($this->waarden['Jaar']['totaal']);
    foreach ($this->waarden['Jaar'] as $categorie=>$categorieData)
    {
//      $categorieStapeling=array();
      foreach ($categorieData['perfWaarden'] as $maand=>$maandWaarden)
      {
        if($maand <> '')
        {
          $totalen[$maand]['allocateEffect']+=($maandWaarden['weging']-$maandWaarden['indexBijdrageWaarde'])*$maandWaarden['indexPerf']*100;
          //$totalen[$maand]['selectieEffect']+=($maandWaarden['procent']-$maandWaarden['indexPerf'])*$maandWaarden['weging']*100;
          $totalen[$maand]['selectieEffect']+=($maandWaarden['procent']-$maandWaarden['indexPerf'])*$maandWaarden['indexBijdrageWaarde']*100;
          $totalen[$maand]['interactieEffect']+=($maandWaarden['weging']-$maandWaarden['indexBijdrageWaarde'])*($maandWaarden['procent']-$maandWaarden['indexPerf'])*100;
          $totalen[$maand]['portBijdrage']+=$maandWaarden['bijdrage']*100;
          $totalen[$maand]['indexBijdrage']+=$maandWaarden['indexBijdrage']*100;
          $totalen[$maand]['overperfBijdrage']+=$maandWaarden['relContrib']*100;
          //echo "$categorie $maand ".($maandWaarden['bijdrage']*100)."<br>\n";
        
          $totalenCategorie[$categorie]['allocateEffect']+=($maandWaarden['weging']-$maandWaarden['indexBijdrageWaarde'])*$maandWaarden['indexPerf']*100;
          $totalenCategorie[$categorie]['selectieEffect']+=($maandWaarden['procent']-$maandWaarden['indexPerf'])*$maandWaarden['indexBijdrageWaarde']*100;
          $totalenCategorie[$categorie]['portBijdrage']+=$maandWaarden['bijdrage']*100;
          $totalenCategorie[$categorie]['indexBijdrage']+=$maandWaarden['indexBijdrage']*100;
          $totalenCategorie[$categorie]['overperfBijdrage']+=$maandWaarden['relContrib']*100;
        
        }
      }
    }
   

    unset($this->pdf->fillCell);
  
    unset($laatste);
    unset($this->jaarTotalen);
    foreach ($totalen as $maand=>$maandWaarden)
    {
      foreach ($maandWaarden as $veld=>$waarde)
      {
        if(!isset($laatste[$veld]))
          $laatste[$veld]=0;
        $this->jaarTotalen[$veld]=((1+$maandWaarden[$veld]/100)*(1+$laatste[$veld]/100)-1)*100;
        $laatste[$veld]=$this->jaarTotalen[$veld];
      }
    }
  
    $extraX=75;
    $this->pdf->setXY($this->pdf->marge+$extraX,140);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(70, 4, "Performance attributie",0,1,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array($extraX,15,18,17,20,17,17,17));
    $this->pdf->CellBorders=array('','U','U','U','U','U','U','U');
    //$this->pdf->Rect($this->pdf->marge,$this->pdf->GetY(),array_sum($this->pdf->widths), 8, 'F');
    $this->pdf->row(array('',"\nMaand","\nPortefeuille","\nBenchmark","\nOverperf.","Allocatie\nEffect","Selectie\nEffect","Interactie\nEffect"));
    unset($this->pdf->CellBorders);
  
    $barData=array();
    $n=0;
    foreach ($totalen as $maand=>$maandWaarden)
    {
      // $barData[$maand]=array('allocateEffect'=>$maandWaarden['allocateEffect'],
      //                        'selectieEffect'=>$maandWaarden['selectieEffect'],
      //                        'interactieEffect'=>$maandWaarden['overperfBijdrage']-($maandWaarden['allocateEffect']+$maandWaarden['selectieEffect']));
      $barData[$maand]=array('portefeuille'=>$maandWaarden['portBijdrage'],
                             'benchmark'=>$maandWaarden['indexBijdrage']);
      $n=fillLine($this->pdf,$n,array(0,1,1,1,1,1,1,1));
      $this->pdf->row(array('',date("m-Y",db2jul($maand)),
                        $this->formatGetal($maandWaarden['portBijdrage'],2),
                        $this->formatGetal($maandWaarden['indexBijdrage'],2),
                        $this->formatGetal($maandWaarden['overperfBijdrage'],2),
                        $this->formatGetal($maandWaarden['allocateEffect'],2),
                        $this->formatGetal($maandWaarden['selectieEffect'],2),
                        $this->formatGetal($maandWaarden['overperfBijdrage']-($maandWaarden['allocateEffect']+$maandWaarden['selectieEffect']),2)));
    
      $this->pdf->excelData[]=array(date("m-Y",db2jul($maand)),$maandWaarden['portBijdrage'],$maandWaarden['indexBijdrage'],$maandWaarden['overperfBijdrage'],
        $maandWaarden['allocateEffect'],$maandWaarden['selectieEffect'],($maandWaarden['overperfBijdrage']-($maandWaarden['allocateEffect']+$maandWaarden['selectieEffect'])));
    }
    unset($this->pdf->fillCell);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Totaal',$this->formatGetal($this->jaarTotalen['portBijdrage'],2),$this->formatGetal($this->jaarTotalen['indexBijdrage'],2),
                      $this->formatGetal($this->jaarTotalen['overperfBijdrage'],2),$this->formatGetal($this->jaarTotalen['allocateEffect'],2),$this->formatGetal($this->jaarTotalen['selectieEffect'],2),
                      $this->formatGetal($this->jaarTotalen['overperfBijdrage']-($this->jaarTotalen['allocateEffect']+$this->jaarTotalen['selectieEffect']),2)));
    $this->pdf->excelData[]=array('Totaal',$this->jaarTotalen['portBijdrage'],$this->jaarTotalen['indexBijdrage'],$this->jaarTotalen['overperfBijdrage'],
      $this->jaarTotalen['allocateEffect'],$this->jaarTotalen['selectieEffect'],($this->jaarTotalen['overperfBijdrage']-($this->jaarTotalen['allocateEffect']+$this->jaarTotalen['selectieEffect'])));
  
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


// ---------------- l35
  }
  
  function toonRiskDeel()
{

$stdev=new rapportSDberekening($this->portefeuille,$this->rapportageDatum);
$stdev->addReeks('totaal');
$stdev->addReeks('benchmark',$this->pdf->portefeuilledata['SpecifiekeIndex']);
$stdev->addReeks('afm');
$stdev->berekenWaarden();

$riskData=$stdev->riskAnalyze('totaal','benchmark',true);
$riskDataLast=$riskData[count($riskData)-1];
  $this->addStdevGrafieken($stdev);
  $this->toonRiskTabel($riskDataLast);
}
  
  
  function addStdevGrafieken($stdev)
  {
    foreach($stdev->standaardDeviatieReeksen['totaal'] as $datum=>$devData)
    {
      $benchmarkData=$stdev->standaardDeviatieReeksen['benchmark'][$datum];
      $afmData=$stdev->standaardDeviatieReeksen['afm'][$datum];
      
      $grafiekData['totaal']['datum'][]= date("M y",db2jul($datum));
      $grafiekData['totaal']['portefeuille'][]= $devData['stdev'];
      $grafiekData['totaal']['specifiekeIndex'][]= $benchmarkData['stdev'];
      if($afmData['stdev']==0)
        $grafiekData['totaal']['extra'][]='F';
      else
        $grafiekData['totaal']['extra'][]= $afmData['stdev'];
      
      // $grafiekData['afm']['datum'][]= date("M y",db2jul($datum));
      // $grafiekData['afm']['portefeuille'][]= $afmData['stdev'];
    }
    $grafiekData['totaal']['titel']='Standaarddeviatie';
    // $grafiekData['afm']['titel']='AFM Standaarddeviatie portefeuille';
    
    $grafiekData['totaal']['legenda']=array('Portefeuille',$this->pdf->portefeuilledata['SpecifiekeIndex'],'AFM');
    
    $this->pdf->setXY(20,225);
    

    $kleuren=array(array(74,166,77),array(61,59,56),array(130,130,215));
    // $indexKleur=array($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->LineDiagramAFM(80, 35, $grafiekData['totaal'],$kleuren,0,0,6,5,1);//50
    // $this->pdf->setXY(160,40);
    // $this->LineDiagram(120, 55, $grafiekData['afm'],array($portKleur,$indexKleur),0,0,6,5,1);//50
    
    
    
  }
  
  function toonRiskTabel($riskData)
  {
  

    
    global $__appvar;
    $DB=new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    
    $this->pdf->Ln(2);
    $this->pdf->setXY($this->pdf->marge,230);
    $this->pdf->SetWidths(array(110,55,20));
    $this->pdf->SetAligns(array('L','L','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Standaarddeviatie',$this->formatGetal($riskData['standaarddeviatie'],1).'%',$body));
    $this->pdf->row(array('','AFM-Standaarddeviatie',$this->formatGetal($riskData['standaarddeviatieAFM'],1).'%',$body));
    $this->pdf->row(array('','Standaarddeviatie benchmark',$this->formatGetal($riskData['standaarddeviatieBenchmark'],1).'%',$body));
    $this->pdf->row(array('','Value at Risk',$this->formatGetal((100-$riskData['valueAtRisk'])/100*$totaalWaarde['totaal'],0).'',''));//'Value at Risk geeft het verwachte maximale verlies aan met een waarschijnlijkheid van 95%. De historische VaR is bepaald aan de hand van de werkelijke jaarlijkse rendementsverdeling over de afgelopen tien jaar.'
    $this->pdf->row(array('','Maximum Draw Down',$this->formatGetal($riskData['maxDrawdown'],1).'%',''));//'Maximum Drawdown geeft de maximale daling weer vanaf de hoogste waarde in een specifieke periode. Deze periode betreft in de overzichten een periode van tien jaar.'
    $this->pdf->row(array('','Tracking Error',$this->formatGetal($riskData['trackingError'],1).'%',''));//'De Tracking-error geeft een indicatie weer van de mate van afwijking van het rendement van de portefeuille ten opzichte van de benchmark.'
    $this->pdf->row(array('','Sharpe ratio',$this->formatGetal($riskData['sharpeRatio'],1).'',''));
    $this->pdf->row(array('','Informatieratio',$this->formatGetal($riskData['informatieratio'],1).'',''));
    
  }
  
  
  
  function LineDiagramAFM($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4)
  {
    global $__appvar;
    
    $legendDatum= $data['datum'];
    $legendaItems= $data['legenda'];
    $titel=$data['titel'];
    $data1 = $data['specifiekeIndex'];
    $data2 = $data['extra'];
    $data = $data['portefeuille'];
    
    
    if(count($data1)>0)
      $bereikdata = array_merge($data,$data1);
    else
      $bereikdata =   $data;
    if(count($data2)>0)
      $bereikdata = array_merge($bereikdata,$data2);
    
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
      $color2= $color[2];
      $color1= $color[1];
      $color = $color[0];
    }
    
    if($color == null)
      $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.2);
    
    
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    
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
    $unit = $lDiag / count($data);
    
    
    
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
    if($titel=='Sharpe-ratio')
      $yAs='';
    else
      $yAs=' %';
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) .$yAs);
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
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 .$yAs);
      
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
      
      if ($i>0)
      {
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
      }
      
      $yval = $yval2;
    }
    
    if(is_array($data1))
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color1);
      
      for ($i=0; $i<count($data1); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
        
        if ($i>0)
        {
          $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        }
        $yval = $yval2;
      }
    }
    if(is_array($data2))
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color2);
      
      $lastValue='';
      for ($i=0; $i<count($data2); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data2[$i]) * $waardeCorrectie) ;
        
        if ($i>0 && $data2[$i] <> 'F' && $lastValue <> 'F')
        {
          $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        }
        $yval = $yval2;
        $lastValue=$data2[$i];
      }
    }
    
    
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.2,'cap' => 'butt'));
    $step=5;
    foreach ($legendaItems as $index=>$item)
    {
      if($index==0)
        $kleur=$color;
      elseif($index==1)
        $kleur=$color1;
      else
        $kleur=$color2;
      
      $this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
      $this->pdf->Rect($XPage+$step, $YPage+$h+10, 3, 3, 'DF','',$kleur);
      $this->pdf->SetXY($XPage+3+$step,$YPage+$h+10);
      $this->pdf->Cell(0,3,$item);
      $step+=($w/3);
    }
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
  }
  
  
  
  function printPie($pieData,$kleurdata,$title='',$width=100,$height=100)
  {
    
    $col1=array(255,0,0); // rood
    $col2=array(0,255,0); // groen
    $col3=array(255,128,0); // oranje
    $col4=array(0,0,255); // blauw
    $col5=array(255,255,0); // geel
    $col6=array(255,0,255); // paars
    $col7=array(128,128,128); // grijs
    $col8=array(128,64,64); // bruin
    $col9=array(255,255,255); // wit
    $col0=array(0,0,0); //zwart
    $standaardKleuren=array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col0);
    // standaardkleuren vervangen voor eigen kleuren.
    $startX=$this->pdf->GetX();
    
    if(isset($kleurdata))
    {
      $grafiekKleuren = array();
      $a=0;
      while (list($key, $value) = each($kleurdata))
      {
        if ($value['R']['value'] == 0 && $value['G']['value'] == 0 && $value['B']['value'] == 0)
          $grafiekKleuren[]=$standaardKleuren[$a];
        else
          $grafiekKleuren[] = array($value['R']['value'],$value['G']['value'],$value['B']['value']);
        $pieData[$key] = $value['percentage'];
        $a++;
      }
    }
    else
      $grafiekKleuren = $standaardKleuren;
    
    while (list($key, $value) = each($pieData))
      if ($value < 0)
        $pieData[$key] = -1 * $value;
    
    //$this->pdf->SetXY(210, $this->pdf->headerStart);
    $y = $this->pdf->getY();
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->setXY($startX,$y-4);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    
    $this->pdf->Cell(50,4,vertaalTekst($title, $this->pdf->rapport_taal),0,0,"C");
    $this->pdf->setXY($startX,$y);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    
    $this->pdf->setX($startX);
    $this->PieChart($width, $height, $pieData, '%l (%p)', $grafiekKleuren);
    $hoogte = ($this->pdf->getY() - $y) + 8;
    $this->pdf->setY($y);
    
    $this->pdf->SetLineWidth($this->pdf->lineWidth);
    $this->pdf->setX($startX);
    
    //	$this->pdf->Rect($startX,$this->pdf->getY(),$width,$hoogte);
    
  }
  
  function PieChart($w, $h, $data, $format, $colors=null)
  {
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->SetLegends($data,$format);
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 4;
    $hLegend = 2;
    $radius = min($w - $margin * 4 - $hLegend - $this->pdf->wLegend, $h - $margin * 2);
    $radius=min($w,$h);
    
    $radius = floor($radius / 2);
    $XDiag = $XPage + $margin + $radius;
    $YDiag = $YPage + $margin + $radius;
    if($colors == null) {
      for($i = 0;$i < $this->pdf->NbVal; $i++) {
        $gray = $i * intval(255 / $this->pdf->NbVal);
        $colors[$i] = array($gray,$gray,$gray);
      }
    }
    
    //Sectors
    $this->pdf->SetLineWidth(0.2);
    $angleStart = 0;
    $angleEnd = 0;
    $i = 0;
    $aantal=count($data);
    foreach($data as $val)
    {
      $angle = floor(($val * 360) / doubleval($this->pdf->sum));
      
      if ($angle != 0)
      {
        $angleEnd = $angleStart + $angle;
        
        $avgAngle=($angleStart+$angleEnd)/360*M_PI;
        $factor=0;
        
        if($i==($aantal-1))
          $angleEnd=360;
        
        //  echo " $angle $angleStart + $angleEnd = ".(($angleStart+$angleEnd)/2)." ".$this->pdf->legends[$i]." | cos:".cos($avgAngle)." | sin:".sin($avgAngle)."  <br>\n";
        $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
        $this->pdf->Sector($XDiag+(sin($avgAngle)*$factor), $YDiag-(cos($avgAngle)*$factor), $radius, $angleStart, $angleEnd);
        $angleStart += $angle;
      }
      $i++;
    }
    //   if ($angleEnd != 360) {
    //      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    //  }
    
    //Legends
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
    $x1 = $XPage ;
    $x2 = $x1 + $hLegend + $margin;
    $y1 = $YDiag + ($radius) + $margin;
    
    for($i=0; $i<$this->pdf->NbVal; $i++) {
      $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
      $this->pdf->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
      $this->pdf->SetXY($x2,$y1);
      $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
      $y1+=$hLegend + 2;
    }
    
  }
  
  function SetLegends($data, $format)
  {
    $this->pdf->legends=array();
    $this->pdf->wLegend=0;
    
    $this->pdf->sum=array_sum($data);
    
    $this->pdf->NbVal=count($data);
    foreach($data as $l=>$val)
    {
      //$p=sprintf('%.1f',$val/$this->sum*100).'%';
      $p=sprintf('%.1f',$val).'%';
      $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
      $this->pdf->legends[]=$legend;
      $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->pdf->wLegend);
    }
  }
  
}
?>
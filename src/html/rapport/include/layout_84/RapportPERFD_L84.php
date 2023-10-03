<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/07/27 18:01:41 $
File Versie					: $Revision: 1.5 $

$Log: RapportPERFD_L84.php,v $
Revision 1.5  2019/07/27 18:01:41  rvv
*** empty log message ***

Revision 1.4  2019/07/24 15:48:02  rvv
*** empty log message ***

Revision 1.3  2019/07/17 15:36:13  rvv
*** empty log message ***

Revision 1.2  2019/07/13 17:51:11  rvv
*** empty log message ***

Revision 1.1  2019/06/05 16:40:11  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/include/ATTberekening_L55.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_84/ATTberekening_L84.php");

class RapportPERFD_L84
{
	function RapportPERFD_L84($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFD";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Beleggingsresultaat lopend jaar";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	  
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
      if(db2jul($this->pdf->PortefeuilleStartdatum) >  db2jul("$RapStartJaar-01-01"))
      {
       $this->tweedePerformanceStart=substr($this->pdf->PortefeuilleStartdatum,0,10);
      }
      else
      {
	     $this->tweedePerformanceStart = "$RapStartJaar-01-01";
	    }
      if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01")
	    {
	     $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,"$RapStartJaar-01-01",true);
       vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,"$RapStartJaar-01-01");
       $this->extraVulling = true;
	    }
	  }

	}

  function derdeStart()
  {
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    $RapJaar = date("Y", db2jul($this->rapportageDatum));
    if(db2jul($this->pdf->PortefeuilleStartdatum) == db2jul($this->rapportageDatumVanaf))
    {
      $this->derdePerformanceStart = substr($this->pdf->PortefeuilleStartdatum,0,10);
    }
    else
    {
      if(db2jul($this->rapportageDatumVanaf) <  db2jul(($RapJaar-1).'-'.substr($this->rapportageDatum,5,5)))
      {
        $this->derdePerformanceStart=$this->rapportageDatumVanaf;
      }
      elseif(db2jul($this->pdf->PortefeuilleStartdatum) <  db2jul(($RapJaar-1).'-'.substr($this->rapportageDatum,5,5)))
      {
        $dagMaand=substr($this->rapportageDatumVanaf,5,5);
        if($dagMaand=='12-31')
          $this->derdePerformanceStart=date('Y-m-d',db2jul(($RapJaar-1).'-'.substr($this->rapportageDatum,5,5))+3600*24);
        else
          $this->derdePerformanceStart=($RapJaar-1).'-'.substr($this->rapportageDatum,5,5);
      }
      elseif(db2jul($this->pdf->PortefeuilleStartdatum) >  db2jul("$RapStartJaar-01-01"))
      {
        $this->derdePerformanceStart=substr($this->pdf->PortefeuilleStartdatum,0,10);
      }
      else
      {
        $this->derdePerformanceStart = "$RapStartJaar-01-01";
      }
    }

//echo $this->derdePerformanceStart ;exit;
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
    
    $this->tweedeStart();
    $this->derdeStart();

	 
    
    // Tweede ATT pagina
    $this->pdf->page2att=true;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    $w=(297-$this->pdf->marge*2)/8;

    $this->pdf->widthA = array($w,$w,$w,$w,$w,$w,$w,$w);
	  	$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');
  		$this->pdf->SetWidths($this->pdf->widthA);
	  	$this->pdf->SetAligns($this->pdf->alignA);

    
    $att=new ATTberekening_L84($this);
   // $hcatData=$att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum);
    $att->indexPerformance=true;
    $this->waarden['Periode']=$att->bereken($this->rapportageDatumVanaf,  $this->rapportageDatum);
    $this->waarden['Jaar']=$att->bereken($this->tweedePerformanceStart,  $this->rapportageDatum);
    

    $start=(substr($this->rapportageDatum,0,4)-1).'-'.substr($this->rapportageDatum,5,2).'-01';
    if(db2jul($this->tweedePerformanceStart)>db2jul($start))
      $start=$this->tweedePerformanceStart;

    
    $this->waarden['12Maanden']=$att->bereken($start,  $this->rapportageDatum);

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
    foreach ($indexBijdrage as $maand=>$bijdrage)
   	{
 	    if(!isset($laatste))
 	      $laatste=0;
 	    $indexTotaal=((1+$bijdrage/100)*(1+$laatste/100)-1)*100;
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
    
    foreach($att->categorien as $categorie=>$omschrijving)
    {
      if($categorie<>'totaal')
        $bovencat[$categorie] = $omschrijving;
    }
    $header=array('Categorie');
    $rows=array('verdelingPort'=>array('Portefeuille verdeling'),
                'verdelingNorm'=>array('Benchmark verdeling'),
                'verdelingVerschil'=>array('Verschil'),
                'rendementPort'=>array('Portefeuille rendement'),
                'rendementNorm'=>array('Benchmark rendement'),
                'rendementVerschil'=>array('Verschil'),
                'bijdragePort'=>array('Bijdrage aan port. rendement'),
                'bijdrageNorm'=>array('Bijdr. benchmark rendement'),
                'bijdrageVerschil'=>array('Verschil'),
                'effectAllocatie'=>array('Allocatie Effect'),
                'effectSelectie'=>array('Selectie Effect'),
                'effectInteractie'=>array('Interactie Effect'));
                
    $startJaar=date("Y",$this->pdf->rapport_datum);
    $totalenEffect=array();
    foreach ($bovencat as $categorie=>$categorieOmschrijving)
    {
      array_push($header,$categorieOmschrijving);
      array_push($rows['verdelingPort'],$this->formatGetal($this->waarden['Jaar'][$categorie]['weging'],2));
      array_push($rows['verdelingNorm'],$this->formatGetal($this->waarden['Jaar'][$categorie]['indexBijdrageWaarde'],2));
      array_push($rows['verdelingVerschil'],$this->formatGetal($this->waarden['Jaar'][$categorie]['weging']-$this->waarden['Jaar'][$categorie]['indexBijdrageWaarde'],2));

      array_push($rows['rendementPort'],$this->formatGetal($this->waarden['Jaar'][$categorie]['procent'],2));
      array_push($rows['rendementNorm'],$this->formatGetal($this->waarden['Jaar'][$categorie]['indexPerf'],2));
      array_push($rows['rendementVerschil'],$this->formatGetal($this->waarden['Jaar'][$categorie]['procent']-$this->waarden['Jaar'][$categorie]['indexPerf'],2));

      array_push($rows['bijdragePort'],$this->formatGetal($this->waarden['Jaar'][$categorie]['bijdrage'],2));
      $totalenEffect['bijdragePort']+=$this->waarden['Jaar'][$categorie]['bijdrage'];
      array_push($rows['bijdrageNorm'],$this->formatGetal($this->waarden['Jaar'][$categorie]['indexBijdrage'],2));
      $totalenEffect['bijdrageNorm']+=$this->waarden['Jaar'][$categorie]['indexBijdrage'];
      $overPerfBijdrage=$this->waarden['Jaar'][$categorie]['bijdrage']-$this->waarden['Jaar'][$categorie]['indexBijdrage'];
      array_push($rows['bijdrageVerschil'],$this->formatGetal($overPerfBijdrage,2));
      $totalenEffect['bijdrageVerschil']+=$overPerfBijdrage;
 
      array_push($rows['effectAllocatie'],$this->formatGetal($totalenCategorie[$categorie]['allocateEffect'],2));
      $totalenEffect['effectAllocatie']+=$totalenCategorie[$categorie]['allocateEffect'];
      array_push($rows['effectSelectie'],$this->formatGetal($totalenCategorie[$categorie]['selectieEffect'],2));
      $totalenEffect['effectSelectie']+=$totalenCategorie[$categorie]['selectieEffect'];

      array_push($rows['effectInteractie'],$this->formatGetal($overPerfBijdrage-($totalenCategorie[$categorie]['allocateEffect']+$totalenCategorie[$categorie]['selectieEffect']),2));   
      $totalenEffect['effectInteractie']+=$overPerfBijdrage-($totalenCategorie[$categorie]['allocateEffect']+$totalenCategorie[$categorie]['selectieEffect']);

    }


   // listarray($barData);
   // listarray($grafiekData);

    unset($this->pdf->fillCell);
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    //array_push($header,'Totaal');


    $this->pdf->rapport_titel = "Performance attributie lopend jaar";
    $this->pdf->AddPage();
    $this->pdf->templateVars['PERFDPaginas']=$this->pdf->page;
    $this->pdf->Ln();


    // ------------------ L34

    $w=(297-2*$this->pdf->marge-50)/8;
    $w=27;
    $this->pdf->SetWidths(array(50,$w,$w,$w,$w,$w,$w,$w,$w,297-(2*$this->pdf->marge)-50-(8*$w)));
    $xStart=$this->pdf->marge;
    $yStart=$this->pdf->getY();
    $this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R'));
    $this->pdf->Rect($this->pdf->marge,$this->pdf->GetY(),array_sum($this->pdf->widths), 8, 'F');
    $this->pdf->Line($this->pdf->marge,$this->pdf->GetY()+8,$this->pdf->marge+array_sum($this->pdf->widths),$this->pdf->GetY()+8);
    $this->pdf->setX($this->pdf->marge);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    
    $this->pdf->SetTextColor(255,255,255);
    $this->pdf->row($header);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->Ln();
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		unset($this->pdf->CellBorders);


foreach($rows as $index=>$rowData)
{
  if($rowData[0]=='Verschil')
  {
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $n=0;
  }
  else
  {
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);  
  }
  if(substr($rowData[0],0,8)=='Bijdrage')
    $this->pdf->Ln();
  
  //$n=fillLine($this->pdf,$n);
  $this->pdf->row($rowData);
  if($rowData[0]=='Verschil')
    $this->pdf->ln();
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

    $this->pdf->setXY($this->pdf->marge,118);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(70, 4, "Performance over totaal gemiddeld belegd vermogen",0,1,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(22,22,22,26,22,22,22));
    $this->pdf->CellBorders=array('U','U','U','U','U','U','U');
    $this->pdf->Rect($this->pdf->marge,$this->pdf->GetY(),array_sum($this->pdf->widths), 8, 'F');
    $this->pdf->SetTextColor(255,255,255);
    $this->pdf->row(array("\nMaand","\nPortefeuille","\nBenchmark","\nOverperf.","Allocatie\nEffect","Selectie\nEffect","Interactie\nEffect"));
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->excelData[]=array();
    $this->pdf->excelData[]=array("Maand","Portefeuille","Benchmark","Overperf.","Allocatie Effect","Selectie Effect","Interactie Effect");
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
      $n=fillLine($this->pdf,$n);                         
   	  $this->pdf->row(array(date("m-Y",db2jul($maand)),
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
    $this->pdf->row(array('Totaal',$this->formatGetal($this->jaarTotalen['portBijdrage'],2),$this->formatGetal($this->jaarTotalen['indexBijdrage'],2),
   	  $this->formatGetal($this->jaarTotalen['overperfBijdrage'],2),$this->formatGetal($this->jaarTotalen['allocateEffect'],2),$this->formatGetal($this->jaarTotalen['selectieEffect'],2),
   	  $this->formatGetal($this->jaarTotalen['overperfBijdrage']-($this->jaarTotalen['allocateEffect']+$this->jaarTotalen['selectieEffect']),2)));
   	$this->pdf->excelData[]=array('Totaal',$this->jaarTotalen['portBijdrage'],$this->jaarTotalen['indexBijdrage'],$this->jaarTotalen['overperfBijdrage'],
   	            $this->jaarTotalen['allocateEffect'],$this->jaarTotalen['selectieEffect'],($this->jaarTotalen['overperfBijdrage']-($this->jaarTotalen['allocateEffect']+$this->jaarTotalen['selectieEffect'])));

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    $lijnGrafiek=array();
    $lijnGrafiekKleuren=array();
    $lastPerf=array();
    
    $DB = new DB();
    $q = "SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'";
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $kleuren = unserialize($kleuren['grafiek_kleur']);
    
    foreach($this->waarden['12Maanden'] as $categorie=>$categorieData)
    {
      if($categorie<>'H-Liq'&& $categorie<>'totaal')
      {
        if(isset($att->categorien[$categorie]))
          $omschrijving=$att->categorien[$categorie];
        else
          $omschrijving=$categorie;
        foreach ($categorieData['perfWaarden'] as $maand => $perfData)
        {
          $lastPerf[$categorie]['portefeuille'] = ((1 + $lastPerf[$categorie]['portefeuille']) * (1 + $perfData['procent'])) - 1;
          $lijnGrafiek[$categorie][$maand][$omschrijving]['portefeuille'] = $lastPerf[$categorie]['portefeuille'] * 100;
    
          $lastPerf[$categorie]['benchmark'] = ((1 + $lastPerf[$categorie]['benchmark']) * (1 + $perfData['indexPerf'])) - 1;
          $lijnGrafiek[$categorie][$maand][$omschrijving]['benchmark'] = $lastPerf[$categorie]['benchmark'] * 100;
  
          if(isset($kleuren['OIB'][$categorie]))
            $lijnGrafiekKleuren[$omschrijving]=array($kleuren['OIB'][$categorie]['R']['value'],$kleuren['OIB'][$categorie]['G']['value'],$kleuren['OIB'][$categorie]['B']['value']);
          elseif(!isset($lijnGrafiekKleuren[$omschrijving]))
            $lijnGrafiekKleuren[$omschrijving]=array(rand(30,200),rand(30,200),rand(30,200));
    
        }
      }
    }
    $w=100;
    $h=35;
    $this->pdf->setXY(188,40);
    foreach($lijnGrafiek as $categorie=>$data)
    {
      $this->LineDiagram($w, $h, $data, $lijnGrafiekKleuren,$att->categorien[$categorie]);
      $this->pdf->setXY(188, $this->pdf->getY()+50);
    }

// ---------------- l35   
    /*
      $yCor=6;
      $this->pdf->setXY(155,182+$yCor);
      $this->VBarDiagram2(130,137-60,$barData,'');
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
     // $colors=array('allocatie effect'=>array(140,178,209),'selectie effect'=>array(133,140,140),'interactie effect'=>array(217,217,217)); //
      $colors=array('Portefeuille'=>array(140,178,209),'Benchmark'=>array(133,140,140)); //
      $xval=170;$yval=185+$yCor;
      foreach($colors as $effect=>$color)
      {
         $this->pdf->Rect($xval, $yval, 3, 3, 'DF',null,$color);
         $this->pdf->SetTextColor(0);
         $this->pdf->SetXY($xval+5, $yval);
         $this->pdf->Cell(50, 3, $effect,0,0,'L');
         $xval+=40;
      }
    
*/
  
  //--------------------------

  
}

  
  
  	function getFondsKoers($fonds,$datum)
	{
	  $db=new DB();
	  $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
	  $db->SQL($query);
	  $koers=$db->lookupRecord();
	  return $koers['Koers'];
	}

function fondsPerformance($fonds,$vanaf,$tot,$startdatumCheck=false)
{
  $januari=substr($tot,0,4)."-01-01";
  if($startdatumCheck==true && db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($januari))
    $januari=substr($this->pdf->PortefeuilleStartdatum,0,10);
    
  $totalPerf=0;
  $indexData=array('fondsKoers_eind'=>$this->getFondsKoers($fonds,$tot),
                    'fondsKoers_begin'=>$this->getFondsKoers($fonds,$vanaf),
                    'fondsKoers_jan'=>$this->getFondsKoers($fonds,$januari));
                    
   $jaarPerf=($indexData['fondsKoers_eind'] - $indexData['fondsKoers_jan']) / ($indexData['fondsKoers_jan']/100 );   
   $periodePerf=($indexData['fondsKoers_eind'] - $indexData['fondsKoers_begin']) / ($indexData['fondsKoers_begin']/100 );                

  return array('periode'=>$periodePerf,'jaar'=>$jaarPerf);
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



	function getWaarden($datumBegin,$datumEind,$portefeuille,$specifiekeIndex='')
	{
  $julBegin = db2jul($datumBegin);
  $julEind = db2jul($datumEind);

 	$eindjaar = date("Y",$julEind);
	$eindmaand = date("m",$julEind);
	$beginjaar = date("Y",$julBegin);
	$startjaar = date("Y",$julBegin);
	$beginmaand = date("m",$julBegin);

	$ready = false;
	$i=0;
	$vorigeIndex = 100;
	$stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
	$datum == array();

	while ($ready == false)
	{
	  if (mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar) > $stop)
	  {
	    $ready = true;
		}
		else
		{
		  if($i==0)
        $datum[$i]['start']=$datumBegin;
	    else
	    {
		    $datum[$i]['start']=jul2db(mktime (0,0,0,$beginmaand+$i,0,$startjaar));
	    }
	    $datum[$i]['stop']=jul2db(mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar));
	    $i++;
		}
	}
	if($i==0)
    $datum[$i]['start']=$datumBegin;
	else
	  $datum[$i]['start']=jul2db(mktime (0,0,0,$beginmaand+$i,0,$startjaar));
	$datum[$i]['stop']=$datumEind;

	$i=1;
	$indexData['index']=100;
	$db=new DB();
	foreach ($datum as $periode)
	{
	  /*
    if(db2jul($periode['start'])<db2jul($this->pdf->PortefeuilleStartdatum) && db2jul($periode['stop'])>db2jul($this->pdf->PortefeuilleStartdatum))
      $periode['start']=date('Y-m-d',db2jul($this->pdf->PortefeuilleStartdatum)+86400);
    
    if(db2jul($periode['start'])==db2jul($this->pdf->PortefeuilleStartdatum))
      $periode['start']=date('Y-m-d',db2jul($this->pdf->PortefeuilleStartdatum)+86400);
    listarray($periode);
	  */
	 	$indexData = array_merge($indexData,$this->BerekenMutaties($periode['start'],$periode['stop'],$portefeuille));
	 	$indexData['datum'] = jul2sql(form2jul(substr($indexData['periodeForm'],-10,10)));
 	  $indexData['index'] = ($indexData['index']  * (100+$indexData['performance'])/100);
	  $data[$i] = $indexData;
    $i++;
	}
	return $data;
	}



	function BerekenMutaties($beginDatum,$eindDatum,$portefeuille)
	{
		$totaalWaarde =array();
		$db = new DB();
    $koersQuery='';

    if(db2jul($beginDatum) < db2jul($this->pdf->PortefeuilleStartdatum))
      $wegingsDatum=$this->pdf->PortefeuilleStartdatum;
    else
      $wegingsDatum=$beginDatum;

		$startjaar=substr($beginDatum,0,4);
		if(db2jul($beginDatum) == mktime (0,0,0,1,1,$startjaar))
		 $beginjaar = true;
		else
		 $beginjaar = false;

		$koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,'EUR',true);

		$fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($portefeuille,$beginDatum,$beginjaar,'EUR',$beginDatum);

	  foreach ($fondswaarden['beginmaand'] as $regel)
	  {
      $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
      if($regel['type']=='rente' && $regel['fonds'] != '')
        $totaalWaarde['renteBegin'] += $regel['actuelePortefeuilleWaardeEuro'];
	  }
    


	  $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$eindDatum,false,'EUR',$beginDatum);
    $categorieVerdeling=$this->categorieVolgorde;

	  foreach ($fondswaarden['eindmaand'] as $regel)
	  {
      $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];

      if($regel['type']=='fondsen')
      {
        $totaalWaarde['beginResultaat'] += $regel['beginPortefeuilleWaardeEuro'];
        $totaalWaarde['eindResultaat'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling[$regel['hoofdcategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rente' && $regel['fonds'] != '')
      {
        $totaalWaarde['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling['EFI_OBL'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rekening')
      {
        $categorieVerdeling['EFI_KAS'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
	  }

	  $ongerealiseerd=($totaalWaarde['eindResultaat']-$totaalWaarde['beginResultaat']);
	  $DB=new DB();

	$query = "SELECT ".
	"SUM(((TO_DAYS('".$eindDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
	"  / (TO_DAYS('".$eindDatum."') - TO_DAYS('".$wegingsDatum."')) ".
	"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
	"FROM  (Rekeningen, Portefeuilles )
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	"WHERE ".
	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Boekdatum > '".$beginDatum."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$eindDatum."' AND ".
	"Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
	$DB->SQL($query); 
	$DB->Query();
	$weging = $DB->NextRecord();

  if($totaalWaarde['begin']==0)
    $gemiddelde = $totaalWaarde['begin'] + $weging['totaal2'];
  else
    $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
	$performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging['totaal2']) / $gemiddelde) * 100;
	//echo "ATT $performance = (((".$totaalWaarde['eind']." - ".$totaalWaarde['begin'].") - ".$weging['totaal2'].") / $gemiddelde) * 100; <br>\n";

	  $waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
		$stortingen = getStortingen($portefeuille,$beginDatum, $eindDatum);
		$onttrekkingen = getOnttrekkingen($portefeuille,$beginDatum, $eindDatum);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;

		$query = "SELECT SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers)  AS totaalkosten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Kosten = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $kosten = $db->lookupRecord();

    $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) AS totaalOpbrengsten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Opbrengst = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $opbrengsten = $db->lookupRecord();

    $opgelopenRente=$totaalWaarde['renteEind']-$totaalWaarde['renteBegin'];
    $valutaResultaat=$resultaatVerslagperiode-($koersResultaat+$ongerealiseerd+$opbrengsten['totaalOpbrengsten']+$kosten['totaalkosten']+$opgelopenRente);
    $ongerealiseerd+=$valutaResultaat;

    $data['periode']= $beginDatum."->".$eindDatum;
    $data['periodeForm']= date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum));
    $data['waardeBegin']=round($totaalWaarde['begin'],2);
    $data['waardeHuidige']=round($totaalWaarde['eind'],2);
    $data['waardeMutatie']=round($waardeMutatie,2);
    $data['stortingen']=round($stortingen,2);
    $data['onttrekkingen']=round($onttrekkingen,2);
    $data['resultaatVerslagperiode'] = round($resultaatVerslagperiode,2);
    $data['kosten'] = round($kosten['totaalkosten'],2);
    $data['opbrengsten'] = round($opbrengsten['totaalOpbrengsten'],2);
    $data['performance'] =$performance;
    $data['ongerealiseerd'] =$ongerealiseerd;
    $data['rente'] = $opgelopenRente;
    $data['gerealiseerd'] =$koersResultaat;
    $data['extra']=array('cat'=>$categorieVerdeling);
    return $data;

	}
  
  
  function LineDiagram($w, $h, $data, $color=null,$titel='', $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;
    

    $legendDatum=array();
    $maxVal=0;
    $minVal=0;
    $lijnen=array();
    $categorieen=array();
    foreach($data as $datum=>$categorieData)
    {
      foreach($categorieData as $categorie=>$waarden)
      {
        $maxVal=max(array($maxVal,$waarden['portefeuille'],$waarden['benchmark']));
        $minVal=min(array($minVal,$waarden['portefeuille'],$waarden['benchmark']));
  
        $lijnen[$categorie.'||p'][$datum]=$waarden['portefeuille'];
        $lijnen[$categorie.'||b'][$datum]=$waarden['benchmark'];
        $categorieen[$categorie]=$categorie;
      }
      $legendDatum[]=$datum;
    }
  


    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $w/12 );
  
  
    
    
    $this->pdf->Rect($XPage, $YPage, $w, $h,'FD','',$this->pdf->grafiekAchtergrondKleur);

    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    
    $this->pdf->Text($XDiag, $YDiag-2,$titel);
    
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    
    if ($maxVal == 0)
    {
      if ($maxVal < 0)
        $maxVal = 1;
    }
    if ($minVal == 0)
    {
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
    
    //datum onder grafiek
    /*
    $datumStart = db2jul($legendDatum[0]);
    $datumStart = vertaalTekst($__appvar["Maanden"][date("n",$datumStart)],$pdf->rapport_taal).' '.date("Y",$datumStart);
    $datumStop  =  db2jul($legendDatum[count($legendDatum)-1])+86400;
    $datumStop  = vertaalTekst($__appvar["Maanden"][date("n",$datumStop)],$pdf->rapport_taal).' '.date("Y",$datumStop);
    $ypos = $YDiag + $hDiag + $margin*2;
    $xpos = $XDiag;
    $this->pdf->Text($xpos, $ypos,$datumStart);
    $xpos = $XPage+$w - $this->pdf->GetStringWidth($datumStop);
    $this->pdf->Text($xpos, $ypos,$datumStop);
*/
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
  //  $lineStyle = array('width' => 1.0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    //listarray($data);
    // $color=array(200,0,0);
    
    $printLabel=array();
    $nulY=$YDiag + (($maxVal) * $waardeCorrectie);

    foreach($lijnen as $categorie=>$maandData)
    {
      $catParts=explode("||",$categorie);
      $kleur=$color[$catParts[0]];
      if($catParts[1]=='b')
      {
        //$kleur=array($kleur[0]*1.2,$kleur[1]*1.2,$kleur[2]*1.2);
        $kleur = array(90,90,90);
      }
      $lineStyle = array('width' => 1.0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $kleur);
      $i=0;
      $extrax1=0;
      $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
      foreach ($maandData as $maand => $waarde)
      {
        $extrax=($unit*0.1*-1);
        if($i <> 0)
          $extrax1=($unit*0.1*-1);
  
  
        //x-as marker
        $this->pdf->line($XDiag+($i+1)*$unit+$extrax, $nulY-1, $XDiag+($i+1)*$unit+$extrax, $nulY+1,array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 1, 'color' => array(0,0,0)) );
  
        $yval2 = $YDiag + (($maxVal-$waarde) * $waardeCorrectie) ;
        $this->pdf->line($XDiag+$i*$unit+$extrax1, $yval, $XDiag+($i+1)*$unit+$extrax, $yval2,$lineStyle );
        $this->pdf->Rect($XDiag+($i+1)*$unit-0.5+$extrax, $yval2-0.5, 1, 1 ,'F','',$kleur);
        $this->pdf->Circle($XDiag+($i+1)*$unit+$extrax, $yval2, 1,0,360,'F','',$kleur);
        

        $yval = $yval2;
        $i++;
      }
  
    }
  
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $legendaY=$YDiag+$h+2;
    $xPos=$XDiag+5;
    foreach($lijnen as $categorie=>$lijnData)
    {
      $lijnParts=explode("||",$categorie);
      $kleur=$color[$lijnParts['0']];
      if($lijnParts['1']=='b')
      {
        $omschrijving = 'Benchmark';
        //$kleur = array($kleur[0] * 1.2, $kleur[1] * 1.2, $kleur[2] * 1.2);
        $kleur = array(90,90,90);
      }
      else
      {
        $omschrijving = 'Portefeuille';
      }
      $this->pdf->Rect($xPos-4,$legendaY, 2, 2 ,'F','',$kleur);
      $this->pdf->Text($xPos,$legendaY+2,$omschrijving);
      $xPos+=35;
      
    }

    /*
    $this->pdf->setTextColor($color[0],$color[1],$color[2]);
    $yTekstStap=2.5;
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
    
    
    
    
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.1, ));
    $this->pdf->SetFillColor(0,0,0);
  }

}
?>
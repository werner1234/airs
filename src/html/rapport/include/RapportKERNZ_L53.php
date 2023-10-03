<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2016/06/27 06:12:23 $
File Versie					: $Revision: 1.5 $

$Log: RapportKERNZ_L53.php,v $
Revision 1.5  2016/06/27 06:12:23  rvv
*** empty log message ***

Revision 1.4  2016/06/25 16:57:02  rvv
*** empty log message ***

Revision 1.3  2016/06/19 15:22:08  rvv
*** empty log message ***

Revision 1.2  2016/06/15 15:58:41  rvv
*** empty log message ***

Revision 1.1  2016/06/11 14:24:49  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/ATTberekening_L53.php");

class RapportKERNZ_L53
{
	function RapportKERNZ_L53($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "KERNZ";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Beleggingsresultaat lopend jaar";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	  $this->tweedeStart();


	 // $this->rapportageDatumVanaf = "$RapStartJaar-01-01";
/*
	 if ($RapStartJaar != $RapStopJaar)
	 {
     echo "Attributie start- en einddatum moeten in hetzelfde jaar liggen.";
     exit;
	 }
*/   
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
    
    $q="SELECT beleggingscategorie,omschrijving FROM Beleggingscategorien";
		$DB->SQL($q);
		$DB->Query();
		while($cat=$DB->nextRecord())
      $this->categorieOmschrijving[$cat['beleggingscategorie']]=$cat['omschrijving'];

	 // $this->categorieOmschrijving=array('LIQ'=>'Liquiditeiten','ZAK'=>'Zakelijke waarden','VAR'=>'Vastrentende waarden','Liquiditeiten'=>'Liquiditeiten');



//listarray($this->categorieOmschrijving);
//listarray($this->categorieVolgorde);
		// voor data
	

  	$this->pdf->widthB = array(1,95,30,10,30,115);
		$this->pdf->alignB = array('L','L','R','R','R');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->widthA = array(26,25,30,30,23,23,23,24,28,24,26);
	  $this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');
  	$this->pdf->SetWidths($this->pdf->widthA);
	  $this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    
    $att=new ATTberekening_L53($this);
   // $hcatData=$att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum);
    $att->indexPerformance=true;
    $this->waarden['Periode']=$att->bereken($this->rapportageDatumVanaf,  $this->rapportageDatum);
    $this->waarden['Jaar']=$att->bereken($this->tweedePerformanceStart,  $this->rapportageDatum);
    // $this->tweedePerformanceStart.' '.$this->rapportageDatumVanaf.' '. $this->rapportageDatum."<br>\n";exit;
    //listarray($this->waarden['Periode']);
   // listarray($this->waarden['Jaar']);
    if($_POST['debug']==1)
    {
      $this->pdf->excelData[]=array('debug jaar data');
      foreach($this->waarden['Jaar'] as $categorie=>$data)
      {
        $this->pdf->excelData[]=array($categorie);
        $header=false;
        foreach($data['perfWaarden'] as $maand=>$maandData)
        {
          $tmp=array($maand);
          $headerData=array('maand');
          foreach($maandData as $key=>$value)
          {
            $tmp[]=$value;
            $headerData[]=$key;
          }
          if($header==false)
          {
            $this->pdf->excelData[] = $headerData;
            $header=true;
          }
          $this->pdf->excelData[] = $tmp;
        }
      }
    }

 //
    
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

 //rvv
    
    

  //$this->toonBenchmark();
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  
  $this->pdf->rapport_titel = "Performance attributie lopend jaar";
  $this->pdf->AddPage();
  $this->pdf->templateVars['KERNZPaginas']=$this->pdf->page;
  $this->pdf->Ln();

  
  // ------------------ L34
  
    //$w=228/12;
    $w=(297-2*$this->pdf->marge-50)/8;
    //$this->pdf->SetWidths(array(50,$w,$w,$w,$w,$w,$w,$w,$w,$w,$w,$w,$w));
    $this->pdf->SetWidths(array(50,$w,$w,$w,$w,$w,$w,$w,$w));
    $xStart=$this->pdf->marge;
    //for($i=0;$i<9;$i++){$xStart+=$this->pdf->widths[$i];}
    $yStart=$this->pdf->getY();
   	$this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R'));

    $this->pdf->Rect($this->pdf->marge,$this->pdf->GetY(),array_sum($this->pdf->widths), 8, 'F');
    $this->pdf->Line($this->pdf->marge,$this->pdf->GetY()+8,$this->pdf->marge+array_sum($this->pdf->widths),$this->pdf->GetY()+8);
    $this->pdf->setX($this->pdf->marge);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);


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

    $totalen=array();
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
   
          $totalenCategorie[$categorie]['allocateEffect']+=($maandWaarden['weging']-$maandWaarden['indexBijdrageWaarde'])*$maandWaarden['indexPerf']*100;
          //$totalenCategorie[$categorie]['selectieEffect']+=($maandWaarden['procent']-$maandWaarden['indexPerf'])*$maandWaarden['weging']*100;
          $totalenCategorie[$categorie]['selectieEffect']+=($maandWaarden['procent']-$maandWaarden['indexPerf'])*$maandWaarden['indexBijdrageWaarde']*100;
          //$totalenCategorie[$categorie]['interactieEffect']+=($maandWaarden['weging']-$maandWaarden['indexBijdrageWaarde'])*($maandWaarden['procent']-$maandWaarden['indexPerf'])*100;

          $totalenCategorie[$categorie]['portBijdrage']+=$maandWaarden['bijdrage']*100;
          $totalenCategorie[$categorie]['indexBijdrage']+=$maandWaarden['indexBijdrage']*100;
          $totalenCategorie[$categorie]['overperfBijdrage']+=$maandWaarden['relContrib']*100;

        }
      }
  
    }
	 // listarray($this->waarden['Jaar']);
    $bovencat=$att->categorien;
   // $bovencat['totaal']='Totaal';

    $header=array('Categorie');
    $rows=array('verdelingPort'=>array('Portefeuille verdeling (Gem.)'),
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
      if($categorie<>'totaal')
      {
        array_push($header, $categorieOmschrijving);
        array_push($rows['verdelingPort'], $this->formatGetal($this->waarden['Jaar'][$categorie]['weging'], 2));
        array_push($rows['verdelingNorm'], $this->formatGetal($this->waarden['Jaar'][$categorie]['indexBijdrageWaarde'], 2));
        array_push($rows['verdelingVerschil'], $this->formatGetal($this->waarden['Jaar'][$categorie]['weging'] - $this->waarden['Jaar'][$categorie]['indexBijdrageWaarde'], 2));

        array_push($rows['rendementPort'], $this->formatGetal($this->waarden['Jaar'][$categorie]['procent'], 2));
        array_push($rows['rendementNorm'], $this->formatGetal($this->waarden['Jaar'][$categorie]['indexPerf'], 2));
        array_push($rows['rendementVerschil'], $this->formatGetal($this->waarden['Jaar'][$categorie]['procent'] - $this->waarden['Jaar'][$categorie]['indexPerf'], 2));

        array_push($rows['bijdragePort'], $this->formatGetal($this->waarden['Jaar'][$categorie]['bijdrage'], 2));
        $totalenEffect['bijdragePort'] += $this->waarden['Jaar'][$categorie]['bijdrage'];
        array_push($rows['bijdrageNorm'], $this->formatGetal($this->waarden['Jaar'][$categorie]['indexBijdrage'], 2));
        $totalenEffect['bijdrageNorm'] += $this->waarden['Jaar'][$categorie]['indexBijdrage'];
        $overPerfBijdrage = $this->waarden['Jaar'][$categorie]['bijdrage'] - $this->waarden['Jaar'][$categorie]['indexBijdrage'];
        array_push($rows['bijdrageVerschil'], $this->formatGetal($overPerfBijdrage, 2));
        $totalenEffect['bijdrageVerschil'] += $overPerfBijdrage;

        array_push($rows['effectAllocatie'], $this->formatGetal($totalenCategorie[$categorie]['allocateEffect'], 2));
        $totalenEffect['effectAllocatie'] += $totalenCategorie[$categorie]['allocateEffect'];
        array_push($rows['effectSelectie'], $this->formatGetal($totalenCategorie[$categorie]['selectieEffect'], 2));
        $totalenEffect['effectSelectie'] += $totalenCategorie[$categorie]['selectieEffect'];

        array_push($rows['effectInteractie'], $this->formatGetal($overPerfBijdrage - ($totalenCategorie[$categorie]['allocateEffect'] + $totalenCategorie[$categorie]['selectieEffect']), 2));
        $totalenEffect['effectInteractie'] += $overPerfBijdrage - ($totalenCategorie[$categorie]['allocateEffect'] + $totalenCategorie[$categorie]['selectieEffect']);
//      array_push($rows['effectInteractie'],$this->formatGetal($totalenCategorie[$categorie]['interactieEffect'],2));
//       $totalenEffect['effectInteractie']+=$totalenCategorie[$categorie]['interactieEffect'];
      }

    }
    //array_push($header,'Totaal');

$this->pdf->row($header);
$this->pdf->Ln();
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		unset($this->pdf->CellBorders);


/*
foreach($totalenEffect as $index=>$effectTotaal)
{
  array_push($rows[$index],$this->formatGetal($effectTotaal,2));
}
*/
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
}
unset($this->pdf->fillCell);
    //$this->pdf->line($xStart,$yStart,$xStart,$this->pdf->getY());
    
    

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

   	//$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    //$this->pdf->row(array("Totaal",'','','',$this->formatGetal($this->jaarTotalen['portBijdrage'],2),'',$this->formatGetal($this->jaarTotalen['indexBijdrage'],2),'',$this->formatGetal($this->jaarTotalen['overperfBijdrage'],2)));


    $this->pdf->setXY($this->pdf->marge,116);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(70, 4, "Performance over totaal gemiddeld belegd vermogen",0,1,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(15,20,20,19,20,20,20));
    $this->pdf->CellBorders=array('U','U','U','U','U','U','U');
    $this->pdf->Rect($this->pdf->marge,$this->pdf->GetY(),array_sum($this->pdf->widths), 8, 'F');
    $this->pdf->row(array("\nMaand","\nPortefeuille","\nBenchmark","\nVerschil","Allocatie\nEffect","Selectie\nEffect","Interactie\nEffect"));
    $this->pdf->excelData[]=array();
    $this->pdf->excelData[]=array("Maand","Portefeuille","Benchmark","Verschil","Allocatie Effect","Selectie Effect","Interactie Effect");
   	unset($this->pdf->CellBorders);

    $barData=array();
    $n=0;
   	foreach ($totalen as $maand=>$maandWaarden)
   	{
   	  $barData[$maand]=array('allocateEffect'=>$maandWaarden['allocateEffect'],
                             'selectieEffect'=>$maandWaarden['selectieEffect'],
                             'interactieEffect'=>$maandWaarden['overperfBijdrage']-($maandWaarden['allocateEffect']+$maandWaarden['selectieEffect']));
    //  $n=fillLine($this->pdf,$n);                         
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


// ---------------- l35   
      $yCor=6;
      $this->pdf->setXY(155,182+$yCor);
      $this->VBarDiagram2(130,137-50,$barData,'');
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $colors=array('allocatie effect'=>array(230,230,230),'selectie effect'=>array(255,204,0),'interactie effect'=>array(51,51,51)); //
      $xval=170;$yval=185+$yCor;
      foreach($colors as $effect=>$color)
      {
         $this->pdf->Rect($xval, $yval, 3, 3, 'DF',null,$color);
         $this->pdf->SetTextColor(0);
         $this->pdf->SetXY($xval+5, $yval);
         $this->pdf->Cell(50, 3, $effect,0,0,'L');
         $xval+=40;
      }
    

  
  //--------------------------

  
}

  function toonBenchmark()
  {
    $db=new DB();
    $nieuweKop=true;
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $query="SELECT
IndexPerBeleggingscategorie.Beleggingscategorie,
IndexPerBeleggingscategorie.Fonds,
IndexPerBeleggingscategorie.Vermogensbeheerder,
Beleggingscategorien.Afdrukvolgorde,
Beleggingscategorien.Omschrijving as hCatOmschrijving,
Fondsen.Omschrijving as fondsOmschrijving
FROM
IndexPerBeleggingscategorie
INNER JOIN Beleggingscategorien ON IndexPerBeleggingscategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
INNER JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds = Fondsen.Fonds
WHERE IndexPerBeleggingscategorie.Vermogensbeheerder='$beheerder'
ORDER BY Beleggingscategorien.Afdrukvolgorde";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      $hoofdcategorien[]=$data;
    }
    $widths=array(80,35,25,105,35);
    $this->pdf->SetWidths($widths);
    $this->pdf->SetAligns(array('L','L','R','R','R'));
    $this->pdf->SetY(100);
    
    $beginJaar=false;
    if(substr($this->rapportageDatumVanaf,5,5)=='01-01')
      $beginJaar=true;
    

    
    foreach($hoofdcategorien as $categorie)
    {
      if($nieuweKop==true)
      {
       	$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
        $this->pdf->Rect($this->pdf->marge,$this->pdf->GetY(),array_sum($widths), 8, 'F');
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        if($beginJaar==true)
          $this->pdf->row(array('Benchmark','Hoofcategorie','','','%Ytd'));
        else
          $this->pdf->row(array('Benchmark','Hoofcategorie','','%Periode','%Ytd'));  
        $this->pdf->ln();
        $this->pdf->Line($this->pdf->marge,$this->pdf->GetY(),array_sum($widths)+$this->pdf->marge,$this->pdf->GetY());
        $nieuweKop=false;
      }
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $perf=$this->fondsPerformance($categorie['Fonds'],$this->rapportageDatumVanaf,$this->rapportageDatum);
      if($beginJaar==true)
        $this->pdf->row(array($categorie['fondsOmschrijving'],$categorie['hCatOmschrijving'],'',
                       '',$this->formatGetal($perf['jaar'],2)));
      else
        $this->pdf->row(array($categorie['fondsOmschrijving'],$categorie['hCatOmschrijving'],'',
                       $this->formatGetal($perf['periode'],2),$this->formatGetal($perf['jaar'],2)));
                       
      $query="SELECT
benchmarkverdeling.fonds,
benchmarkverdeling.percentage,
benchmarkverdeling.benchmark,
Fondsen.Omschrijving as fondsOmschrijving,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving as BeleggingscategorieOmschrijving
FROM
benchmarkverdeling
INNER JOIN Fondsen ON benchmarkverdeling.fonds = Fondsen.Fonds
LEFT JOIN BeleggingscategoriePerFonds ON Fondsen.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder='$beheerder'
LEFT JOIN Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie=Beleggingscategorien.Beleggingscategorie
WHERE 
benchmarkverdeling.benchmark='".$categorie['Fonds']."'
ORDER BY benchmarkverdeling.fonds ";
     $db->SQL($query);
     $db->Query();
     if($db->records()>0)
     {
       $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
       //$this->pdf->Rect($this->pdf->marge,$this->pdf->GetY(),array_sum($widths), 8, 'F');
       $this->pdf->row(array($categorie['fondsOmschrijving'].' bestaande uit'));
       if($beginJaar==true)
         $this->pdf->row(array('        Index/Fonds','Sub-categorie','Weging','','%Ytd'));
       else
         $this->pdf->row(array('        Index/Fonds','Sub-categorie','Weging','%Periode','%Ytd'));  
       $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
       while($data=$db->nextRecord())
       {
          $perf=$this->fondsPerformance($data['fonds'],$this->rapportageDatumVanaf,$this->rapportageDatum);
          if($beginJaar==true)
            $this->pdf->row(array('        '.$data['fondsOmschrijving'],$data['BeleggingscategorieOmschrijving'],$this->formatGetal($data['percentage'],2).'%',
                       '',$this->formatGetal($perf['jaar'],2)));
          else
            $this->pdf->row(array('        '.$data['fondsOmschrijving'],$data['BeleggingscategorieOmschrijving'],$this->formatGetal($data['percentage'],2).'%',
                       $this->formatGetal($perf['periode'],2),$this->formatGetal($perf['jaar'],2)));
       }

       $nieuweKop=true;
     }
     
          
    // listarray($perf);

    }


    
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
      $hGrafiek = ($h);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1);

      $this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $w- $margin, $hGrafiek,'FD','',$this->pdf->grafiekAchtergrondKleur); //

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

      for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
      {
        $skipNull = true;
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");
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
        $this->pdf->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");
        $n++;
        if($n >20)
          break;
      }
      
      $numBars=count($data);
      if($numBars > 0)
        $this->pdf->NbVal=$numBars;


      $colors=array('allocateEffect'=>array(230,230,230),'selectieEffect'=>array(255,204,0),'interactieEffect'=>array(51,51,51)); //

      $vBar = ($bGrafiek / ($this->pdf->NbVal ))/4; //4
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
          $this->pdf->SetTextColor(0,0,0);
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
	$performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging[totaal2]) / $gemiddelde) * 100;

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
    
    $this->pdf->Rect($XPage, $YPage, $w, $h,'FD','',$this->pdf->grafiekAchtergrondKleur);

    if($color == null)
      $color=array(140,178,209);
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
    $lineStyle = array('width' => 1.0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    //listarray($data);
   // $color=array(200,0,0);
   
    $printLabel=array();
    for ($i=0; $i<count($data); $i++)
    {
      $extrax=($unit*0.1*-1);
      if($i <> 0)
        $extrax1=($unit*0.1*-1);
        
      $maand=date("n",db2jul($legendDatum[$i]));  
      if($aantalData < 12 || $maand==3 || $maand==6 || $maand==9 || $maand==12)
      {    
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-3+$unit,$YDiag+$hDiag+8,vertaalTekst($__appvar["Maanden"][$maand],$pdf->rapport_taal) ,25);
        $printLabel[$i]=1;
      }
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit+$extrax1, $yval, $XDiag+($i+1)*$unit+$extrax, $yval2,$lineStyle );
      $this->pdf->Rect($XDiag+($i+1)*$unit-0.5+$extrax, $yval2-0.5, 1, 1 ,'F','',$color);
      $this->pdf->Circle($XDiag+($i+1)*$unit+$extrax, $yval2, 1,0,360,'F','',$color);
      $yval = $yval2;
    }
    

    for ($i=0; $i<count($data); $i++)
    {
      $extrax=($unit*0.1*-1);
      if($i <> 0)
        $extrax1=($unit*0.1*-1);
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->SetFont($this->pdf->rapport_font, '', 9);
      if($data[$i] <> 0 && $printLabel[$i])
        $this->pdf->Text($XDiag+($i+1)*$unit-1+$extrax,$yval2-2.5,$this->formatGetal($data[$i],1));
      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);

    }

    if(is_array($data1))
    {
     // listarray($data1);
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 1.0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color1);
      for ($i=0; $i<count($data1); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color);
        
        $this->pdf->Text($XDiag+($i+1)*$unit-0.5,$yval2-4.5,$data1[$i]);
         
        $yval = $yval2;
      }
    }
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }


  function areaDiagram($w, $h, $data)
  {
      global $__appvar;
      $grafiekPunt = array();
      $verwijder=array();
      
      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      
      $this->pdf->Rect($XPage, $YPage-$h, $w, $h,'FD','',$this->pdf->grafiekAchtergrondKleur);

      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = vertaalTekst($__appvar["Maanden"][date("n",db2jul($datum))],$pdf->rapport_taal);// date('m-Y',(db2jul($datum)));
        $n=0;
        $minVal=0;
        $maxVal=100;
        foreach (array_reverse($this->categorieVolgorde) as $categorie)
        {
          $grafiek[$datum][$categorie]=$waarden[$categorie];
          $grafiekCategorie[$categorie][$datum]=$waarden[$categorie];
          $categorien[$categorie] = $n;
          $categorieId[$n]=$categorie ;
          $maxVal=max(array($maxVal,$waarden[$categorie]));
          $minVal=min(array($minVal,$waarden[$categorie]));
          if($waarden[$categorie] < 0)
          {
             unset($grafiek[$datum][$categorie]);
             $grafiekNegatief[$datum][$categorie]=$waarden[$categorie];
          }
          else
             $grafiekNegatief[$datum][$categorie]=0;

          if(!isset($colors[$categorie]))
            $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          $n++;
        }
      }


      if(count($data) > 11)
        $numBars=count($data)-1;
      else  
        $numBars=11;

      if($color == null)
      {
        $color=array(155,155,155);
      }


      if($maxVal <= 100)
        $maxVal=100;
      elseif($maxVal < 125)
        $maxVal=125;

      if($minVal >= 0)
        $minVal = 0;
      elseif($minVal > -25)
        $minVal=-25;

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $margin = 0;
      $YstartGrafiek = $YPage;
      $hGrafiek = $h;
      $XstartGrafiek = $XPage;
      $bGrafiek = ($w ) - ($w/6); // - legenda

      $n=0;
      foreach (($this->categorieVolgorde) as $categorie)//array_reverse
      {
        if(is_array($grafiekCategorie[$categorie]))
        {
          $this->pdf->Rect($XstartGrafiek+$w+3 , $YstartGrafiek-$hGrafiek+$n*7+2, 2, 2, 'DF',null,$colors[$categorie]);
          $this->pdf->SetXY($XstartGrafiek+$w+6 ,$YstartGrafiek-$hGrafiek+$n*7+1.5 );
          $this->pdf->MultiCell(45, 4,$this->categorieOmschrijving[$categorie],0,'L');
          $n++;
        }
      }

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


      $horDiv = 5;
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
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1)." %",0,0,'R');
        $n++;
        if($n >20)
         break;
      }

      $n=0;
      for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
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





        $vBar = ($bGrafiek/($numBars-1));

       
    $XstartGrafiek+=$vBar*0.625;

      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);

      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;
   
   $aantalData=count($grafiek)-1;
   foreach ($grafiek as $datum=>$data)
   {

        
      foreach($data as $categorie=>$val)
      {

          
         if($i == 0)
        {
         $polly[$categorie][]=$XstartGrafiek+ ($i ) * $vBar;
         $polly[$categorie][]=$YstartGrafiek;
        }
        
           
         if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $xval = $XstartGrafiek + ($i ) * $vBar ;
          $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
          $hval = ($val * $unit);

          $yval2=$YstartGrafiekLast[$datum] + $nulYpos +($val * $unit);

          //$this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
         $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;


        $lines[$categorie][]=array($XstartGrafiek + ($i* $vBar), $yval,$XstartGrafiek + (1 + $i ) * $vBar , $yval2);
        $marks[$categorie][]=array($XDiag+($i)*$unit-0.5-$xcorrectie, $yval2-0.5);
        //$this->pdf->Rect($XDiag+($i+1)*$unit-0.5-$xcorrectie, $yval2-0.5, 1, 1 ,'F','',$color);
    
          $returnPolly[$categorie][]=$XstartGrafiek +  $i* $vBar;
          $returnPolly[$categorie][]=$yval;
          $polly[$categorie][]=$XstartGrafiek + ($i) * $vBar;
          $polly[$categorie][]=$yval2;
       
        
      
        if($i == $aantalData)
        {
         $polly[$categorie][]=$XstartGrafiek + ($i) * $vBar;
         $polly[$categorie][]=$YstartGrafiek;
        }
        
                 $this->pdf->SetTextColor(0,0,0);
         if($legendaPrinted[$datum] != 1)
         {
           $maand = date('n',db2jul($datum));
           //if( $i==0 || $i == $aantalData || (($maand==3 || $maand==6 || $maand==9 || $maand==12) && $i <> 1 && $i <> $aantalData-1 ) )
            if(($maand==3 || $maand==6 || $maand==9 || $maand==12) || $numBars==11 )
              $this->pdf->TextWithRotation($xval,$YstartGrafiek+7,$legenda[$datum],25);
            $this->pdf->line($xval,$YstartGrafiek, $xval,$YstartGrafiek+1);
         }
         $legendaPrinted[$datum] = 1;
          

/*
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
  
           


         if($grafiekPunt[$categorie][$datum])
         {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(194,179,157));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
         */
         $lastCategorie=$categorie;
      }
      $i++;
     $lastDatum=$datum;
   }
   
   
   /*
   $pollyReverse=array_reverse($pollyReverse);
   // listarray($polly);
   foreach($pollyReverse as $value)
      $polly[]=$value;
   */
  // listarray($polly);

   foreach(array_reverse($polly) as $categorie=>$pol)
     $this->pdf->Polygon($pol, 'F', null, $colors[$categorie]) ;
   
   
  //  $this->pdf->Polygon($polly['EFI_OVERIG'], 'F', null, array(200,200,200)) ;

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
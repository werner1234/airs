<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/04/11 11:20:06 $
File Versie					: $Revision: 1.41 $

$Log: RapportATT_L55.php,v $
Revision 1.41  2018/04/11 11:20:06  rvv
*** empty log message ***

Revision 1.40  2018/04/11 09:14:19  rvv
*** empty log message ***

Revision 1.39  2018/04/07 15:21:44  rvv
*** empty log message ***

Revision 1.38  2018/03/17 18:48:55  rvv
*** empty log message ***

Revision 1.37  2018/03/03 17:13:43  rvv
*** empty log message ***

Revision 1.36  2018/02/21 17:15:09  rvv
*** empty log message ***

Revision 1.35  2016/12/17 18:57:35  rvv
*** empty log message ***

Revision 1.34  2016/12/03 19:22:25  rvv
*** empty log message ***

Revision 1.33  2016/07/27 15:50:38  rvv
*** empty log message ***

Revision 1.32  2016/03/19 16:53:33  rvv
*** empty log message ***

Revision 1.31  2016/03/06 14:37:43  rvv
*** empty log message ***

Revision 1.30  2016/03/02 16:59:05  rvv
*** empty log message ***

Revision 1.29  2015/10/14 16:12:05  rvv
*** empty log message ***

Revision 1.28  2015/08/23 11:52:00  rvv
*** empty log message ***

Revision 1.27  2015/05/27 11:57:58  rvv
*** empty log message ***

Revision 1.26  2015/05/23 12:54:40  rvv
*** empty log message ***

Revision 1.25  2014/11/01 22:05:56  rvv
*** empty log message ***

Revision 1.24  2014/10/30 10:02:58  rvv
*** empty log message ***

Revision 1.23  2014/10/29 16:47:19  rvv
*** empty log message ***

Revision 1.22  2014/10/15 16:05:25  rvv
*** empty log message ***

Revision 1.21  2014/09/03 15:56:32  rvv
*** empty log message ***

Revision 1.20  2014/08/30 16:31:49  rvv
*** empty log message ***

Revision 1.19  2014/08/09 15:06:36  rvv
*** empty log message ***

Revision 1.18  2014/08/06 15:41:01  rvv
*** empty log message ***

Revision 1.17  2014/07/19 14:27:59  rvv
*** empty log message ***

Revision 1.16  2014/07/09 16:12:34  rvv
*** empty log message ***

Revision 1.15  2014/07/06 12:34:34  rvv
*** empty log message ***

Revision 1.14  2014/07/02 15:56:02  rvv
*** empty log message ***

Revision 1.13  2014/06/30 16:33:09  rvv
*** empty log message ***

Revision 1.12  2014/06/29 15:38:56  rvv
*** empty log message ***

Revision 1.11  2014/06/14 16:40:37  rvv
*** empty log message ***

Revision 1.10  2014/06/11 15:35:21  rvv
*** empty log message ***

Revision 1.9  2014/06/08 15:27:58  rvv
*** empty log message ***

Revision 1.8  2014/05/21 14:01:45  rvv
*** empty log message ***

Revision 1.7  2014/05/21 09:32:51  rvv
*** empty log message ***

Revision 1.6  2014/05/17 16:35:44  rvv
*** empty log message ***

Revision 1.5  2014/05/07 08:40:26  rvv
*** empty log message ***

Revision 1.4  2014/05/05 15:52:25  rvv
*** empty log message ***

Revision 1.3  2014/04/30 16:03:17  rvv
*** empty log message ***

Revision 1.2  2014/04/19 16:16:18  rvv
*** empty log message ***

Revision 1.1  2014/04/12 16:28:12  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/ATTberekening_L55.php");

class RapportATT_L55
{
	function RapportATT_L55($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ATT";
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

		$this->pdf->AddPage();

    $this->pdf->widthA = array(26,25,30,30,23,23,23,24,28,24,26);
	  $this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');
  	$this->pdf->SetWidths($this->pdf->widthA);
	  $this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Rect($this->pdf->marge,$this->pdf->GetY(),array_sum($this->pdf->widthA), 8, 'F');
	  $this->pdf->row(array(vertaalTekst("Maand",$this->pdf->rapport_taal)."\n ",
		                      vertaalTekst("Begin-\nvermogen",$this->pdf->rapport_taal),
		                      vertaalTekst("Stortingen en \nonttrekkingen",$this->pdf->rapport_taal),
		                      vertaalTekst("Koersresultaat",$this->pdf->rapport_taal)."\n ",
		                      vertaalTekst("Inkomsten",$this->pdf->rapport_taal)."\n ",
		                      vertaalTekst("Kosten",$this->pdf->rapport_taal)."\n ",
		                      vertaalTekst("Opgelopen\nrente",$this->pdf->rapport_taal),
		                      vertaalTekst("Beleggings-\nresultaat",$this->pdf->rapport_taal),
		                     	vertaalTekst("Eind-\nvermogen",$this->pdf->rapport_taal),
		                      vertaalTekst("Rendement",$this->pdf->rapport_taal)." %\n(".vertaalTekst("maand",$this->pdf->rapport_taal).")",
		                      vertaalTekst("Rendement",$this->pdf->rapport_taal)." %\n(".vertaalTekst("cumulatief",$this->pdf->rapport_taal).")"));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);                      
    $sumWidth = array_sum($this->pdf->widthA);
	  $this->pdf->Line($this->pdf->marge+$this->pdf->widthB[0],$this->pdf->GetY(),$this->pdf->marge+$sumWidth,$this->pdf->GetY());

      
    $this->pdf->templateVars['ATTPaginas']=$this->pdf->page;

		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];
    //$this->tweedePerformanceStart='2017-01-31';

  $indexData = $this->getWaarden($this->tweedePerformanceStart ,$this->rapportageDatum ,$this->portefeuille);
  $indexDataGrafiek = $this->getWaarden($this->derdePerformanceStart ,$this->rapportageDatum ,$this->portefeuille);


//exit;

foreach ($indexData as $index=>$data)
{
  if($data['datum'] != '0000-00-00')
  {
    $rendamentWaarden[] = $data;
    $grafiekData['Datum'][] = $data['datum'];
    $grafiekData['Index'][] = $data['index']-100;
    $grafiekData['benchmarkIndex'][] = $data['specifiekeIndex']-100;
  //  foreach ($data['categorieVerdeling'] as $categorie=>$waarde)
    foreach ($data['extra']['cat'] as $categorie=>$waarde)
    {
      if($categorie=='LIQ')
        $categorie='Liquiditeiten';

      if($waarde <> 0)
        $categorien[$categorie]=$categorie;
    }
  }
}


    foreach ($indexDataGrafiek as $index=>$data)
    {
      if($data['datum'] != '0000-00-00')
      {
        $barGraph['Index'][$data['datum']]['leeg']=0;
        foreach ($data['extra']['cat'] as $categorie=>$waarde)
        {
          if($categorie=='LIQ')
            $categorie='Liquiditeiten';

          $barGraph['Index'][$data['datum']][$categorie] = $waarde/$data['waardeHuidige']*100;
        }
      }
    }

    $i=0;
		$q="SELECT
Beleggingscategorien.Beleggingscategorie,
Beleggingscategorien.Omschrijving,
Beleggingscategorien.Afdrukvolgorde
FROM
CategorienPerHoofdcategorie
INNER JOIN Beleggingscategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = Beleggingscategorien.Beleggingscategorie
WHERE 
CategorienPerHoofdcategorie.Vermogensbeheerder='$beheerder' 
GROUP BY Beleggingscategorien.Omschrijving
ORDER BY Beleggingscategorien.Afdrukvolgorde"; //WHERE Beleggingscategorie IN('LIQ','ZAK','VAR','Liquiditeiten')

		$DB->SQL($q);
		$DB->Query();
		while($data=$DB->nextRecord())
		{
		  $this->categorieVolgorde[$data['Beleggingscategorie']]=$data['Beleggingscategorie'];
		  $this->categorieOmschrijving[$data['Beleggingscategorie']]=vertaalTekst($data['Omschrijving'],$this->pdf->rapport_taal);
		}


$grafiekData['Datum'][]="$RapStartJaar-12-01";
   
   if(count($rendamentWaarden) > 0)
   {
        $n=0;
        $this->pdf->fillCell = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $this->pdf->underlinePercentage=0.8;
        $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r']*1.2,$this->pdf->rapport_kop_bgcolor['g']*1.2,$this->pdf->rapport_kop_bgcolor['b']*1.2);

        $totaalRendament=100;
        $totaalRendamentIndex=100;
		    foreach ($rendamentWaarden as $row)
		    {
		      $resultaat = $row['Opbrengsten']-$row['Kosten'];
		      $datum = db2jul($row['datum']);
          
          $this->pdf->CellBorders = array();
          $n=fillLine($this->pdf,$n);
		      $this->pdf->row(array(ucfirst(vertaalTekst($__appvar["Maanden"][date("n",$datum)],$this->pdf->rapport_taal)).date(" Y",$datum),
		                           $this->formatGetal($row['waardeBegin'],2),
		                           $this->formatGetal($row['stortingen']-$row['onttrekkingen'],2),
		                           $this->formatGetal($row['gerealiseerd']+$row['ongerealiseerd'],2),
		                           $this->formatGetal($row['opbrengsten'],2),
		                           $this->formatGetal($row['kosten'],2),
		                           $this->formatGetal($row['rente'],2),
		                           $this->formatGetal($row['resultaatVerslagperiode'],2),
		                           $this->formatGetal($row['waardeHuidige'],2),
		                           $this->formatGetal($row['performance'],2),
		                           $this->formatGetal($row['index']-100,2)));

		                           if(!isset($waardeBegin))
		                             $waardeBegin=$row['waardeBegin'];
		                           $totaalWaarde = $row['waardeHuidige'];
		                           $totaalResultaat += $row['resultaatVerslagperiode'];
		                           $totaalGerealiseerd += $row['gerealiseerd'];
		                           $totaalOngerealiseerd += $row['ongerealiseerd'];
		                           $totaalOpbrengsten += $row['opbrengsten'];
		                           $totaalKosten += $row['kosten'];
		                           $totaalRente += $row['rente'];
		                           $totaalStortingenOntrekkingen += $row['stortingen']-$row['onttrekkingen'];
		                           $totaalRendament = $row['index'];

        $i++;
		    }
		    $this->pdf->fillCell=array();
        $this->pdf->CellBorders = array('','TS','TS','TS','TS','TS','TS','TS','TS','','TS'); 
        $this->pdf->row(array('','','','','','','','','','','','')); 
        $this->pdf->SetY($this->pdf->GetY()-4);
        $this->pdf->ln(3);
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->CellBorders = array();
		    $this->pdf->row(array(vertaalTekst('Totaal',$this->pdf->rapport_taal),
		                           $this->formatGetal($waardeBegin,2),
		                           $this->formatGetal($totaalStortingenOntrekkingen,2),
		                           $this->formatGetal($totaalGerealiseerd+$totaalOngerealiseerd,2),
		                           $this->formatGetal($totaalOpbrengsten,2),
		                           $this->formatGetal($totaalKosten,2),
		                           $this->formatGetal($totaalRente,2),
		                           $this->formatGetal($totaalResultaat,2),
		                           $this->formatGetal($totaalWaarde,2),
		                           '',
		                           $this->formatGetal($totaalRendament-100,2)
		                           ));//$this->formatGetal($totaalRendamentIndex-100,2)
		    $this->pdf->CellBorders = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		  }

      $yBegin=100;
      if($this->pdf->GetY() > 102)
      {
        $this->pdf->AddPage();
     //   $yBegin=$this->pdf->GetY()+10;
      }

		  if (count($barGraph) > 0)
		  {
		    $this->pdf->SetXY(160,$yBegin+2)		;//112
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		    	$this->pdf->Cell(0, 5, vertaalTekst('Vermogensverdeling per maandultimo',$this->pdf->rapport_taal), 0, 1);
  		    $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
		      $this->pdf->SetXY(160,$yBegin+80)		;//112
		      $this->areaDiagram(100, 70, $barGraph['Index']);
		  }

		  if (count($grafiekData) > 1)
		  {
        $this->pdf->SetXY(10,$yBegin+2);//104
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  		  $this->pdf->Cell(0, 5, vertaalTekst('Rendement',$this->pdf->rapport_taal).' ('.
                               vertaalTekst('cumulatief',$this->pdf->rapport_taal).' '.
                               vertaalTekst('in',$this->pdf->rapport_taal).' %)', 0, 1);
  		  $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
  		  $this->pdf->SetXY(14,$yBegin+10)		;//112
        //function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
        $this->LineDiagram(125, 70, $grafiekData,$this->pdf->rapport_grafiek_color,0,0,6,5,1);//50
		  }
		  $this->pdf->SetXY(8, 155);//165

		$this->pdf->ln(10);
		$this->pdf->SetX(108);
	  $this->pdf->MultiCell(170,4,$titel,0,'L');
	  $this->pdf->SetX(108);
    $this->pdf->fillCell = array();
    
    
    // Tweede ATT pagina
    $this->pdf->page2att=true;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->AddPage();
    $w=(297-$this->pdf->marge*2)/8;

    $this->pdf->widthA = array($w,$w,$w,$w,$w,$w,$w,$w);
	  	$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');
  		$this->pdf->SetWidths($this->pdf->widthA);
	  	$this->pdf->SetAligns($this->pdf->alignA);

  		$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
      $this->pdf->ln();
  
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->Rect($this->pdf->marge,$this->pdf->GetY(),array_sum($this->pdf->widthA), 8, 'F');
	  	$this->pdf->row(array(vertaalTekst("Categorie",$this->pdf->rapport_taal),
                          vertaalTekst("Begin-\nvermogen",$this->pdf->rapport_taal),
		                      vertaalTekst("Aankoop/\nverkoop",$this->pdf->rapport_taal),
		                      vertaalTekst("Opbrengsten",$this->pdf->rapport_taal)."\n ",
		                      vertaalTekst("Resultaten",$this->pdf->rapport_taal)."\n ",
		                      vertaalTekst("Eindvermogen",$this->pdf->rapport_taal)."\n ",
		                      vertaalTekst("Rendement %\nport. periode",$this->pdf->rapport_taal),
		                      vertaalTekst("Rendement %\nbenchmark",$this->pdf->rapport_taal)));

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize); 
    $sumWidth = array_sum( $this->pdf->widthA);
    $this->pdf->Line($this->pdf->marge,$this->pdf->GetY(),$this->pdf->marge+$sumWidth,$this->pdf->GetY());   
      
    $att=new ATTberekening_L55($this);
   // $hcatData=$att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum);
    $att->indexPerformance=true;
    $this->waarden['Periode']=$att->bereken($this->rapportageDatumVanaf,  $this->rapportageDatum);
    $this->waarden['Jaar']=$att->bereken($this->tweedePerformanceStart,  $this->rapportageDatum);
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

 //rvv
    
    
    
    
//listarray($this->waarden['Periode']);
$n=0;
    $this->categorieOmschrijving['totaal']='Totaal';
    foreach($this->waarden['Periode'] as $categorie=>$data)
    {

      if($categorie=='totaal')
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $n=fillLine($this->pdf,$n);
      	  	$this->pdf->row(array($this->categorieOmschrijving[$categorie],
                          $this->formatGetal($data['beginwaarde'],0),
		                      $this->formatGetal($data['stortEnOnttrekking'],0),
                          $this->formatGetal($data['opbrengsten']+$data['RENMETotaal'],0),
                          $this->formatGetal($data['resultaat'],0),
                          $this->formatGetal($data['eindwaarde'],0),
                          $this->formatGetal($data['procent'],2),
                          $this->formatGetal($data['indexPerf'],2)));
    }
    unset($this->pdf->fillCell);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  
  $this->toonBenchmark();
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);





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
    $bovencat=$att->categorien;

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

    $grafiekData2=array();
    $n=0;
    foreach ($totalen as $maand=>$maandWaarden)
    {
      // $barData[$maand]=array('allocateEffect'=>$maandWaarden['allocateEffect'],
      //                        'selectieEffect'=>$maandWaarden['selectieEffect'],
      //                        'interactieEffect'=>$maandWaarden['overperfBijdrage']-($maandWaarden['allocateEffect']+$maandWaarden['selectieEffect']));
      $barData[$maand] = array('portefeuille' => $maandWaarden['portBijdrage'],
                               'benchmark'    => $maandWaarden['indexBijdrage']);

      $grafiekData2['Datum'][$n]=$maand;
      $grafiekData2['Index'][$n]=((1+$grafiekData2['Index'][$n-1]/100)*(1+$maandWaarden['portBijdrage']/100)-1)*100;
      $grafiekData2['Index1'][$n]=((1+$grafiekData2['Index1'][$n-1]/100)*(1+$maandWaarden['indexBijdrage']/100)-1)*100;
      $n++;
    }
   // listarray($barData);
   // listarray($grafiekData);

    if (count($grafiekData2) > 1)
    {
      //$this->pdf->SetXY(130,$yBegin+2);//104
     // $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    //  $this->pdf->Cell(0, 5, vertaalTekst('Rendement',$this->pdf->rapport_taal).' ('.vertaalTekst('cumulatief',$this->pdf->rapport_taal).' '.vertaalTekst('in',$this->pdf->rapport_taal).' %)', 0, 1);
      //$this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
      $this->pdf->SetXY(160,$yBegin)		;//112
      //function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
      $this->LineDiagram(125, 70, $grafiekData2,array(array(140,178,209),array(133,140,140)),0,0,6,5,1);//50

      $colors=array('Portefeuille'=>array(140,178,209),'Benchmark'=>array(133,140,140)); //
      $xval=170;$yval=185;
      foreach($colors as $effect=>$color)
      {
        $this->pdf->Rect($xval, $yval, 3, 3, 'DF',null,$color);
        $this->pdf->SetTextColor(0);
        $this->pdf->SetXY($xval+5, $yval);
        $this->pdf->Cell(50, 3, $effect,0,0,'L');
        $xval+=40;
      }
    }

    unset($this->pdf->fillCell);
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    //array_push($header,'Totaal');


    $this->pdf->rapport_titel = "Performance attributie lopend jaar";
    $this->pdf->AddPage();
    $this->pdf->templateVars['ATT2Paginas']=$this->pdf->page;
    $this->pdf->Ln();


    // ------------------ L34

    $w=(297-2*$this->pdf->marge-50)/8;
    $this->pdf->SetWidths(array(50,$w,$w,$w,$w,$w,$w,$w,$w));
    $xStart=$this->pdf->marge;
    $yStart=$this->pdf->getY();
    $this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R'));
    $this->pdf->Rect($this->pdf->marge,$this->pdf->GetY(),array_sum($this->pdf->widths), 8, 'F');
    $this->pdf->Line($this->pdf->marge,$this->pdf->GetY()+8,$this->pdf->marge+array_sum($this->pdf->widths),$this->pdf->GetY()+8);
    $this->pdf->setX($this->pdf->marge);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);


    $this->pdf->row($header);
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

    $this->pdf->setXY($this->pdf->marge,108);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(70, 4, "Performance over totaal gemiddeld belegd vermogen",0,1,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(15,18,18,24,19,19,19));
    $this->pdf->CellBorders=array('U','U','U','U','U','U','U');
    $this->pdf->Rect($this->pdf->marge,$this->pdf->GetY(),array_sum($this->pdf->widths), 8, 'F');
    $this->pdf->row(array("\nMaand","\nPortefeuille","\nBenchmark","\nOverperf.","Allocatie\nEffect","Selectie\nEffect","Interactie\nEffect"));
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
WHERE IndexPerBeleggingscategorie.Vermogensbeheerder='$beheerder' AND IndexPerBeleggingscategorie.vanaf < '".$this->rapportageDatum."'
ORDER BY Beleggingscategorien.Afdrukvolgorde ,IndexPerBeleggingscategorie.vanaf desc ";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      $hoofdcategorien[]=$data;
    }
    $widths=array(75,25,1,20,20);
    $this->pdf->SetWidths($widths);
    $this->pdf->SetAligns(array('L','L','R','R','R'));
    $this->pdf->SetY(100);
    
    $beginJaar=false;
    if(substr($this->rapportageDatumVanaf,5,5)=='01-01')
      $beginJaar=true;
    

    $getoondeCategorien=array();
    foreach($hoofdcategorien as $categorie)
    {
      if(!in_array($categorie['Beleggingscategorie'],$getoondeCategorien))
      {
      $getoondeCategorien[]=$categorie['Beleggingscategorie'];
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
       $widths=array(75,25,20,20,20);
       $this->pdf->SetWidths($widths);

       $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
       //$this->pdf->Rect($this->pdf->marge,$this->pdf->GetY(),array_sum($widths), 8, 'F');
       $this->pdf->row(array($categorie['fondsOmschrijving'].' bestaande uit'));
       $this->pdf->ln();
       $this->pdf->ln();
       if($beginJaar==true)
         $this->pdf->row(array('        Index/Fonds','Sub-categorie','Weging','','%Ytd'));
       else
         $this->pdf->row(array('        Index/Fonds','Sub-categorie','Weging','%Periode','%Ytd'));
       $this->pdf->ln();
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

      $colors=array('portefeuille'=>array(140,178,209),'benchmark'=>array(133,140,140),'interactieEffect'=>array(217,217,217));

      $vBar = ($bGrafiek / ($this->pdf->NbVal ))/3; //4
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
        $legenda[$datum] = vertaalTekst($__appvar["Maanden"][date("n",db2jul($datum))],$this->pdf->rapport_taal);// date('m-Y',(db2jul($datum)));
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
      $bGrafiek = ($w ) ;//- ($w/6); // - legenda

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





        $vBar = ($bGrafiek/($numBars));

       
    //$XstartGrafiek+=$vBar*0.625;

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
            //if(($maand==3 || $maand==6 || $maand==9 || $maand==12) || $numBars==11 )
              $this->pdf->TextWithRotation($xval-2,$YstartGrafiek+7,$legenda[$datum],25);
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
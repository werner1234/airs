<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/05 06:02:37 $
File Versie					: $Revision: 1.3 $

$Log: RapportATT_L89.php,v $
Revision 1.3  2020/07/05 06:02:37  rvv
*** empty log message ***

Revision 1.2  2020/06/10 15:35:05  rvv
*** empty log message ***

Revision 1.1  2020/05/13 15:37:13  rvv
*** empty log message ***

Revision 1.15  2020/05/06 17:10:29  rvv
*** empty log message ***

Revision 1.14  2020/03/29 08:07:03  rvv
*** empty log message ***

Revision 1.13  2020/03/14 18:42:03  rvv
*** empty log message ***

Revision 1.12  2020/03/04 16:40:47  rvv
*** empty log message ***

Revision 1.11  2020/03/01 09:53:26  rvv
*** empty log message ***

Revision 1.10  2020/02/29 16:24:09  rvv
*** empty log message ***

Revision 1.9  2020/02/26 16:12:54  rvv
*** empty log message ***

Revision 1.8  2019/10/30 16:47:58  rvv
*** empty log message ***

Revision 1.7  2017/07/06 05:18:11  rvv
*** empty log message ***

Revision 1.6  2017/06/18 14:18:22  rvv
*** empty log message ***

Revision 1.5  2017/06/18 09:18:24  rvv
*** empty log message ***

Revision 1.4  2016/02/06 16:42:56  rvv
*** empty log message ***

Revision 1.3  2015/01/07 17:25:26  rvv
*** empty log message ***

Revision 1.2  2014/12/31 18:09:06  rvv
*** empty log message ***

Revision 1.1  2014/10/08 15:42:52  rvv
*** empty log message ***

Revision 1.9  2014/01/18 17:27:23  rvv
*** empty log message ***

Revision 1.8  2013/11/23 17:23:24  rvv
*** empty log message ***

Revision 1.7  2013/01/27 14:14:24  rvv
*** empty log message ***

Revision 1.6  2012/10/21 12:44:08  rvv
*** empty log message ***

Revision 1.5  2012/10/17 09:16:53  rvv
*** empty log message ***

Revision 1.4  2012/09/23 08:51:44  rvv
*** empty log message ***

Revision 1.3  2012/09/19 16:53:18  rvv
*** empty log message ***

Revision 1.2  2012/09/13 15:58:37  rvv
*** empty log message ***

Revision 1.5  2012/08/11 13:17:53  rvv
*** empty log message ***

Revision 1.4  2012/07/11 11:33:23  rvv
*** empty log message ***

Revision 1.3  2012/06/09 13:43:40  rvv
*** empty log message ***

Revision 1.2  2012/05/30 16:02:38  rvv
*** empty log message ***

Revision 1.1  2012/05/27 08:33:11  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");


class RapportATT_L89
{
	function RapportATT_L89($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ATT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Beleggingsresultaat lopend jaar";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->realRapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));



    if($RapStartJaar==$RapStopJaar)
    {
      $this->tweedeStart();
      $this->rapportageDatumVanaf = "$RapStartJaar-01-01";
    }
    else
      $this->tweedePerformanceStart= $this->rapportageDatumVanaf ;
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
	  if(db2jul($this->pdf->PortefeuilleStartdatum) >= db2jul($this->rapportageDatumVanaf))
	  {
	    $this->tweedePerformanceStart = substr($this->pdf->PortefeuilleStartdatum,0,10);
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
		//echo $this->pdf->PortefeuilleStartdatum." ".$this->rapportageDatumVanaf." ".$this->tweedePerformanceStart;exit;
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
    $this->valutaKleuren=$allekleuren['OIV'];
	 // $this->categorieOmschrijving=array('LIQ'=>'Liquiditeiten','ZAK'=>'Zakelijke waarden','VAR'=>'Vastrentende waarden','Liquiditeiten'=>'Liquiditeiten');




//listarray($this->categorieVolgorde);
		// voor data
		$this->pdf->widthA = array(1,95,25,5,25,5,25,5,25,5,25,5,25,5,25,5);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R');


  	$this->pdf->widthB = array(1,95,30,10,30,115);
		$this->pdf->alignB = array('L','L','R','R','R');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];


  $index=new indexHerberekening();
  $indexData = $index->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);

//  $indexData = $this->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);
//listarray($indexData);
//exit;
$i=0;
    $lastPerf='';
foreach ($indexData as $index=>$data)
{
  if($data['datum'] != '0000-00-00')
  {
/*
		if(db2jul($this->pdf->PortefeuilleStartdatum)>=db2jul($data['datum']))
			$perf=0;
		else
		{
			if(db2jul($this->tweedePerformanceStart) < db2jul($this->pdf->PortefeuilleStartdatum))
				$startDatum=substr($this->pdf->PortefeuilleStartdatum,0,10);
			else
				$startDatum=$this->tweedePerformanceStart;
			//echo $startDatum." <br>\n"; ob_flush();
			$perf = $this->performance($this->portefeuille, $startDatum, $data['datum']);
		}
		$indexData[$index]['index']=$perf;
    $data['index']=$perf;
    if($lastPerf<>'')
    {
      $data['performance']=((1+$perf/100)/(1+$lastPerf/100)-1)*100;
    }
    else
    {
      $data['performance']=$perf;
    }
    $lastPerf=$perf;
*/
    $rendamentWaarden[] = $data;
    $grafiekData['Datum'][] = $data['datum'];
    $grafiekData['Index'][] = $data['index']-100;
    $grafiekData['benchmarkIndex'][] = $data['specifiekeIndex']-100;
  //  foreach ($data['categorieVerdeling'] as $categorie=>$waarde)

  $barGraph['Index'][$data['datum']]['leeg']=0;
    foreach ($data['extra']['cat'] as $categorie=>$waarde)
    {
      if($categorie=='LIQ')
        $categorie='Liquiditeiten';

      //if(!isset($barGraph['Index'][$data['datum']][$categorie]) || $barGraph['Index'][$data['datum']][$categorie]==0)
      $barGraph['Index'][$data['datum']][$categorie] += ($waarde/$data['waardeHuidige']*100);
      if($waarde <> 0)
        $categorien[$categorie]=$categorie;
    }
  }
}

		$q="SELECT Beleggingscategorie,BeleggingscategorieOmschrijving as Omschrijving,beleggingscategorieVolgorde FROM TijdelijkeRapportage WHERE Portefeuille='".$this->portefeuille."' AND Beleggingscategorie <>'' GROUP BY Beleggingscategorie  ORDER BY beleggingscategorieVolgorde asc"; //WHERE Beleggingscategorie IN('LIQ','ZAK','VAR','Liquiditeiten')

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
        $n=1;
        $this->pdf->fillCell = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
     //   $this->pdf->CellBorders = array('','US','US','US','US','US','US','US','US','US','US','US');
        $this->pdf->underlinePercentage=0.8;

       //$this->pdf->SetFillColor(230,230,230);
        //$this->pdf->SetFillColor(200,240,255);

     //   $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r']*1.2,$this->pdf->rapport_kop_bgcolor['g']*1.2,$this->pdf->rapport_kop_bgcolor['b']*1.2);
		 $this->pdf->SetFillColor($this->pdf->rapport_background_fill[0],$this->pdf->rapport_background_fill[1],$this->pdf->rapport_background_fill[2]);

        $totaalRendament=100;
        $totaalRendamentIndex=100;
        $fill=false;
		    foreach ($rendamentWaarden as $row)
		    {
		      //listarray($row);
		      $resultaat = $row['Opbrengsten']-$row['Kosten'];
		      $datum = db2jul($row['datum']);

		      if($fill==true)
		      {
		        $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
		        $fill=false;
		      }
		      else
		      {
		        $this->pdf->fillCell=array();
		         $fill=true;
		      }

					//echo "$perf ".$this->portefeuille.",".$this->realRapportageDatumVanaf." ,".$row['datum']."<br>\n";

          $this->pdf->CellBorders = array();
		      $this->pdf->row(array(date("Y",$datum).' '.vertaalTekst($__appvar["Maanden"][date("n",$datum)],$this->pdf->rapport_taal) ,
		                           $this->formatGetal($row['waardeBegin'],0),
		                           $this->formatGetal($row['stortingen']-$row['onttrekkingen'],0),
		                           $this->formatGetal($row['gerealiseerd']+$row['ongerealiseerd'],0),
		                           $this->formatGetal($row['opbrengsten'],0),
		                           $this->formatGetal($row['kosten'],0),
		                           $this->formatGetal($row['rente'],0),
		                           $this->formatGetal($row['resultaatVerslagperiode'],0),
		                           $this->formatGetal($row['waardeHuidige'],0),
                               $this->formatGetal($row['performance'],1),
		                           $this->formatGetal($row['index']-100,1)));
                               
                               

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
		                           $totaalRendament = $row['index']-100;

		    $n++;
        $i++;
		    }
		    $this->pdf->fillCell=array();


            
            $this->pdf->CellBorders = array('','TS','TS','TS','TS','TS','TS','TS','TS','','TS'); 
            $this->pdf->row(array('','','','','','','','','','','','')); 
            $this->pdf->SetY($this->pdf->GetY()-4);


        $this->pdf->ln(3);
        
        //$this->pdf->CellBorders = array('','UU','UU','UU','UU','UU','UU','UU','UU','UU','','UU');
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->CellBorders = array();
		    $this->pdf->row(array(vertaalTekst('Totaal',$this->pdf->rapport_taal),
		                           $this->formatGetal($waardeBegin,0),
		                           $this->formatGetal($totaalStortingenOntrekkingen,0),
		                           $this->formatGetal($totaalGerealiseerd+$totaalOngerealiseerd,0),
		                           $this->formatGetal($totaalOpbrengsten,0),
		                           $this->formatGetal($totaalKosten,0),
		                           $this->formatGetal($totaalRente,0),
		                           $this->formatGetal($totaalResultaat,0),
		                           $this->formatGetal($totaalWaarde,0),
		                           '',
		                           $this->formatGetal($totaalRendament,1)
		                           ));//$this->formatGetal($totaalRendamentIndex-100,2)
		    $this->pdf->CellBorders = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		  }
    
  
		  $this->addDoorkijk();

/*
		  if (count($barGraph) > 0)
		  {
		    $this->pdf->SetXY($this->pdf->marge,102)		;//112
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		    	$this->pdf->Cell(0, 5, vertaalTekst('Vermogensverdeling',$this->pdf->rapport_taal), 0, 1);
  		    $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
		      $this->pdf->SetXY(15,140)		;//112
		      $this->VBarDiagram(220, 30, $barGraph['Index']);
		  }
*/

		  if (count($grafiekData) > 1)
		  {
        $this->pdf->SetXY(8,109+37);//104
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  		  $this->pdf->Cell(0, 5, vertaalTekst('Rendement',$this->pdf->rapport_taal).' ('.
                               vertaalTekst('cumulatief',$this->pdf->rapport_taal).' '.
                               vertaalTekst('in',$this->pdf->rapport_taal).' %)', 0, 1);
  		  $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+127,$this->pdf->GetY());
  		  $this->pdf->SetXY(15,117+35)		;//112
        $valX = $this->pdf->GetX();
        $valY = $this->pdf->GetY();
        //function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
        $this->LineDiagram(120, 30, $grafiekData,$this->pdf->rapport_grafiek_color,0,0,6,5,1);//50
        $this->pdf->SetXY($valX, $valY + 80);
		  }

		  $this->printBenchmarkvergelijking();
    /*
          $this->pdf->SetXY(8, 155);//165
    
        if(round($totaalResultaat) != round($resultaatVerslagperiode['Totaal']))
        {
        // echo "<script  type=\"text/JavaScript\">alert('Beleggingsresultaat totaal (".(round($resultaatVerslagperiode['Totaal'],2)).") komt niet overeen met perioden (".round($totaalResultaat,2).") verschil (".(round($resultaatVerslagperiode['Totaal'],2)-round($totaalResultaat,2)).") voor portefeuille ".$this->portefeuille."'); </script>";
        //  ob_flush();
        }
    
        $this->pdf->ln(10);
        $this->pdf->SetX(108);
    
    
        $this->pdf->MultiCell(170,4,$titel,0,'L');
        $this->pdf->SetX(108);
    */

	   $this->pdf->fillCell = array();


		if($this->extraVulling)
		{
	   // verwijderTijdelijkeTabel($this->portefeuille,"$RapStartJaar-01-01");
		}

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

	function performance($portefeuille,$datumBegin,$datumEind)
	{
		$beginwaarde=0;
		$eindwaarde=0;
		if(substr($datumBegin,5,5)=='01-01')
			$startJaar=true;
		else
			$startJaar=false;
		$gegevens=berekenPortefeuilleWaarde($portefeuille,$datumBegin,$startJaar);
		foreach($gegevens as $waarde)
			$beginwaarde+=$waarde['actuelePortefeuilleWaardeEuro'];
		$gegevens=berekenPortefeuilleWaarde($portefeuille,$datumEind,false);
		foreach($gegevens as $waarde)
			$eindwaarde+=$waarde['actuelePortefeuilleWaardeEuro'];

		$DB=new DB();
		$query = "SELECT ".
			"SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$datumBegin."')) ".
			"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) AS totaal1, ".
			"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))  AS totaal2 ".
			"FROM  (Rekeningen, Portefeuilles)
	     Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
			"WHERE ".
			"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
			"Rekeningmutaties.Verwerkt = '1' AND ".
			"Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
			"Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
		$DB->SQL($query);
		$DB->Query();
		$weging = $DB->NextRecord();

		$gemiddelde = $beginwaarde + $weging['totaal1'];
		if($gemiddelde <> 0)
			$performance = ((($eindwaarde - $beginwaarde) - $weging['totaal2']) / $gemiddelde) * 100;

		return $performance;
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
        $categorieVerdeling[$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rente' && $regel['fonds'] != '')
      {
        $totaalWaarde['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling['OBL'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rekening')
      {
        $categorieVerdeling['LIQ'] += $regel['actuelePortefeuilleWaardeEuro'];
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
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $w/12 );

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(27,92,124);
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
    $unit = $lDiag / count($data);

    if($jaar)
      $unit = $lDiag / 12;

    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
    {
      $xpos = $XDiag + $verInterval * $i;
    }

    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);

    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);

    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);

    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $n=0;
    for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
       break;
    }

    $n=0;
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
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
    $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    //listarray($data);
   // $color=array(200,0,0);
   
   
    for ($i=0; $i<count($data); $i++)
    {
      $extrax=($unit*0.1*-1);
      if($i <> 0)
        $extrax1=($unit*0.1*-1);
        
        
      $this->pdf->TextWithRotation($XDiag+($i)*$unit-10+$unit,$YDiag+$hDiag+8,jul2form(db2jul($legendDatum[$i])),25);

      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit+$extrax1, $yval, $XDiag+($i+1)*$unit+$extrax, $yval2,$lineStyle );
      $this->pdf->Rect($XDiag+($i+1)*$unit-0.5+$extrax, $yval2-0.5, 1, 1 ,'F','',$color);
      
      if($data[$i] <> 0)
        $this->pdf->Text($XDiag+($i+1)*$unit-1+$extrax,$yval2-2.5,$this->formatGetal($data[$i],1));
     
      
      $yval = $yval2;
    }

    if(is_array($data1))
    {
     // listarray($data1);
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color1);
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


  function VBarDiagram($w, $h, $data)
  {
      global $__appvar;
      $legendaWidth = 00;
      $grafiekPunt = array();
      $verwijder=array();

      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = jul2form(db2jul($datum));
        $n=0;
        $minVal=0;
        $maxVal=100;
        foreach (array_reverse($this->categorieVolgorde) as $categorie)
        {
        //foreach ($waarden as $categorie=>$waarde)
        //{
          if($categorie=='LIQ')
            $categorie='Liquiditeiten';
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



      $numBars = count($legenda);
      $numBars=10;

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
      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1) - ($w/12)*2; // - legenda

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

         if($legendaPrinted[$datum] != 1)
           $this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+4,$legenda[$datum],0);

         if($grafiekPunt[$categorie][$datum])
         {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(194,179,157));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
         $legendaPrinted[$datum] = 1;
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
  
  //doorkijk
  
  function bepaalWegingPerFonds($fonds,$doorkijkSoort,$airsCategorie,$waarde,$rekening)
  {
    $db=new DB();
    $query="SELECT MAX(datumVanaf) as vanafDatum FROM doorkijk_categorieWegingenPerFonds
WHERE fonds='".mysql_real_escape_string($fonds)."' AND msCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND  datumVanaf <= '" . $this->rapportageDatum . "' ";
    $db->executeQuery($query);
    $vanafDatum=$db->nextRecord();//listarray($query);
    
    $query="SELECT msCategorie,weging FROM doorkijk_categorieWegingenPerFonds
WHERE fonds='".mysql_real_escape_string($fonds)."' AND msCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND datumVanaf = '" . $vanafDatum['vanafDatum']. "'  ";
    $db->executeQuery($query);
    $wegingPerMsCategorie=array();
    while ($row = $db->nextRecord())
    {
      $wegingPerMsCategorie[$row['msCategorie']] = $row['weging'];
      if($this->debug)
      {
        $this->debugData['MSfondsWeging'][$doorkijkSoort][$fonds][$row['msCategorie']]['weging'] = $row['weging'];
        $this->debugData['MSfondsWeging'][$doorkijkSoort][$fonds][$row['msCategorie']]['waarde'] = $waarde*$row['weging']/100;
      }
    }
//echo $query;
    $wegingDoorkijkCategorie=array();
    $airsKoppelingen=array('REGION_ZOTHERND','ZSECTOR_OTHERND');
    if(count($wegingPerMsCategorie)>0)
    {
      foreach($airsKoppelingen as $categorie)
      {
        if (isset($wegingPerMsCategorie[$categorie]))
        {
          $query = "SELECT doorkijkCategorie FROM doorkijk_koppelingPerVermogensbeheerder WHERE bronKoppeling='$airsCategorie' AND doorkijkCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND systeem='AIRS' AND vermogensbeheerder='". $this->pdf->portefeuilledata['Vermogensbeheerder']."'";
          $db->executeQuery($query);
          //		listarray($wegingPerMsCategorie);
          //		echo $wegingPerMsCategorie[$categorie]."| $fonds | $airsCategorie | $doorkijkSoort | $query<br>\n";
          
          if($db->records()>0)
          {
            
            while ($row = $db->nextRecord())
            {
              $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['weging'] = $wegingPerMsCategorie[$categorie];
              $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['waarde'] = $waarde * $wegingPerMsCategorie[$categorie] / 100;
              
              if ($this->debug)
              {
                
                $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['weging'] = $wegingPerMsCategorie[$categorie];
                $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['waarde'] = $waarde * $wegingPerMsCategorie[$categorie] / 100;
              }
            }
            unset($wegingPerMsCategorie[$categorie]);
          }
        }
      }
      
      $msCategorienWhere=" bronKoppeling IN ('".implode("','",array_keys($wegingPerMsCategorie))."')";
      $query = "SELECT doorkijkCategorie,bronKoppeling as msCategorie FROM doorkijk_koppelingPerVermogensbeheerder
WHERE $msCategorienWhere AND doorkijkCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND systeem='MS' AND vermogensbeheerder='". $this->pdf->portefeuilledata['Vermogensbeheerder']."'";
      $db->executeQuery($query);
      
      while ($row = $db->nextRecord())
      {
        $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['weging'] += $wegingPerMsCategorie[$row['msCategorie']];
        $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['waarde'] += $waarde*$wegingPerMsCategorie[$row['msCategorie']]/100;
        
        if($this->debug)
        {
          $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['weging'] += $wegingPerMsCategorie[$row['msCategorie']];
          $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['waarde'] += $waarde*$wegingPerMsCategorie[$row['msCategorie']]/100;
        }
      }
    }
    else
    {
      $query = "SELECT doorkijkCategorie FROM doorkijk_koppelingPerVermogensbeheerder
WHERE bronKoppeling='$airsCategorie' AND doorkijkCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND systeem='AIRS' AND vermogensbeheerder='". $this->pdf->portefeuilledata['Vermogensbeheerder']."'";
      $db->executeQuery($query);
      
      while ($row = $db->nextRecord())
      {
        $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['weging'] = 100;
        $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['waarde'] = $waarde;
        
        if($this->debug)
        {
          $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['weging'] = 100;
          $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['waarde'] = $waarde;
        }
      }
    }
    
    
    return $wegingDoorkijkCategorie;
  }
  
  function bepaalWeging($doorkijkSoort,$belCategorien=array())
  {
    global $__appvar;
    if(is_array($belCategorien) && count($belCategorien)>0)
      $fondsFilter="AND Beleggingscategorie IN('".implode("','",$belCategorien)."')";
    else
      $fondsFilter='';
  
    $db = new DB();
  
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal
                  FROM TijdelijkeRapportage
                  WHERE rapportageDatum ='" . $this->rapportageDatum . "' $fondsFilter AND portefeuille = '" . $this->portefeuille . "'" .	$__appvar['TijdelijkeRapportageMaakUniek'];
    $db->SQL($query);
    $db->Query();
    $totaalWaarde = $db->nextRecord();
    
    if($doorkijkSoort=='Beleggingscategorien')
    {
  
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal , Beleggingscategorie, BeleggingscategorieOmschrijving
                  FROM TijdelijkeRapportage
                  WHERE rapportageDatum ='" . $this->rapportageDatum . "' $fondsFilter AND portefeuille = '" . $this->portefeuille . "'" .	$__appvar['TijdelijkeRapportageMaakUniek'].
        " GROUP BY Beleggingscategorie ORDER BY BeleggingscategorieVolgorde";
      $db->SQL($query);
      $db->Query();
      $doorkijkVerdeling=array();
      while($data=$db->nextRecord())
      {
        $categorie = $data['BeleggingscategorieOmschrijving'];
        $totaalPercentage= $data['totaal'];
        $doorkijkVerdeling['categorien'][$categorie] += $totaalPercentage;
        $doorkijkVerdeling['details'][$categorie]['percentage'] += $totaalPercentage/$totaalWaarde['totaal']*100;
        $doorkijkVerdeling['details'][$categorie]['waardeEUR'] += $data['totaal'];
      }
   
      return $doorkijkVerdeling;
    }
    
    
   

    
    $vertaling=array('Beleggingscategorien'=>'Beleggingscategorie','Beleggingssectoren'=>'Beleggingssector','Regios'=>'Regio','Valutas'=>'ValutaOmschrijving');
    $query = "SELECT fonds,rekening, actuelePortefeuilleWaardeEuro as waardeEUR, ".$vertaling[$doorkijkSoort]." as airsSoort
					FROM TijdelijkeRapportage	WHERE rapportageDatum ='".$this->rapportageDatum."'  $fondsFilter AND portefeuille = '" . $this->portefeuille . "'" .	$__appvar['TijdelijkeRapportageMaakUniek']." Order by fonds";
    
    $db=new DB();
    $db->SQL($query);// echo $query."<br>\n";exit;
    $db->Query();
    
    $doorkijkVerdeling=array();
    while($row = $db->nextRecord())
    {
      if($doorkijkSoort=='Valutas')
      {
        $verdeling = array($row['airsSoort'] => array('weging' => 100, 'waarde' => $row['waardeEUR']));
      }
      elseif($row['fonds']=='' && $doorkijkSoort <> 'Regios' && $doorkijkSoort <> 'Beleggingscategorien' )
      {
        $row['fonds'] = $row['rekening'];
        $verdeling=array('Geldrekeningen'=>array('weging'=>100,'waarde'=>$row['waardeEUR']));
      }
      else
      {
        if($row['fonds']=='')
          $row['fonds'] = $row['rekening'];
        //	listarray($row);
        $verdeling = $this->bepaalWegingPerFonds($row['fonds'], $doorkijkSoort, $row['airsSoort'], $row['waardeEUR'],$row['rekening']);
      }
     // listarray($verdeling);
      $totaalPercentage=0;
      if(is_array($verdeling))
      {
        $overige=false;
        $check=0;
        foreach($verdeling as $categorie=>$percentage)
        {
          $check+=$percentage['weging'];
          $totaalPercentage=($percentage['weging'] * ($row['waardeEUR']/$totaalWaarde['totaal']) );
          $doorkijkVerdeling['categorien'][$categorie]+=$totaalPercentage;
          $doorkijkVerdeling['details'][$categorie]['percentage']+=$totaalPercentage;
          $doorkijkVerdeling['details'][$categorie]['waardeEUR']+=$percentage['waarde'];
        }
        if($check==0)
          $overige=true;
        elseif(round($check,5) <> 100)
        {
          if($this->debug)
            $this->debugData['afwijkingWeging'][$doorkijkSoort][$row['fonds']]['afwijking']['weging'] = $check-100;
        }
      }
      else
        $overige=true;
      
      if($overige==true)
      {
        $totaalPercentage=($row['waardeEUR'] / $totaalWaarde['totaal']) * 100;
        $doorkijkVerdeling['categorien']['Overige'] += $totaalPercentage;
        $doorkijkVerdeling['details']['Overige']['percentage']+=$totaalPercentage;
        $doorkijkVerdeling['details']['Overige']['waardeEUR']+=$row['waardeEUR'];
        
        if($this->debug)
        {
          $this->debugData['NietGekoppeld'][$doorkijkSoort][$row['fonds']]['Overige']['weging'] = 100;
          $this->debugData['NietGekoppeld'][$doorkijkSoort][$row['fonds']]['Overige']['waarde']=$row['waardeEUR'];
        }
      }
      if($this->debug)
      {
        $row['percentage']=$totaalPercentage;
        $this->debugData['portefeuilleData'][] = $row;
      }
      
    }
    return $doorkijkVerdeling;
  }
  
  function toonTabel($regels,$xOffset,$titel,$kleuren)
  {
    $this->pdf->setWidths(array($xOffset+3,55,5));
    $this->pdf->setAligns(array('L','L','R','R'));
  
    $maxRegels=13;
    $aantalRegels=count($regels);
    if($aantalRegels<$maxRegels)
    {
      $extraY=floor(($maxRegels-$aantalRegels)/2)*4;
    }
    else
      $extraY=0;
    
    $this->pdf->setXY($this->pdf->marge,95+$extraY);
  //  $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  //  $this->pdf->row(array('',$titel,'in %'));
    $this->pdf->excelData[]=array($titel,'in %');
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    $this->pdf->ln(1);
    $totalen=array();
    //	listarray($kleuren);
    foreach($regels as $categorie=>$data)
      if(!isset($kleuren[$categorie]))
        $kleuren[$categorie]=array(128,128,128);
    
    foreach($kleuren as $categorie=>$kleur)
    {
      //foreach($regels as $categorie=>$data)
      if(isset($regels[$categorie]))
      {
        $data =$regels[$categorie];
        $this->pdf->rect($this->pdf->getX() + $xOffset, $this->pdf->getY() + 1, 2, 2, 'DF', '', $kleuren[$categorie]);
        $this->pdf->row(array('', $categorie.' ('.$this->formatGetal($data['percentage'], 2).'%)'));
        $this->pdf->excelData[] = array($categorie,  round($data['percentage'], 2));
        $totalen['waardeEUR'] += $data['waardeEUR'];
        $totalen['percentage'] += $data['percentage'];
      }
    }
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('','','SUB');
  //  $this->pdf->row(array('','Totaal',  $this->formatGetal($totalen['percentage'], 2)));
  //  $this->pdf->excelData[]=array('Totaal',  round($totalen['percentage'], 2));
    $this->pdf->excelData[]=array();
    $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
  }
  
  function printPie($kleurdata,$xstart,$ystart,$titel)
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
    if($kleurdata)
    {
      $sorted 		= array();
      $percentages 	= array();
      $kleur			= array();
      $valuta 		= array();
      
      //$kleurdata=	array_reverse($kleurdata);
      //	listarray($kleurdata);
      foreach($kleurdata as $key=>$data)
      {
        $percentages[] 	= $data['percentage'];
        $kleur[] 			= $data['kleur'];
        $valuta[] 		= $key;
      }
      //arsort($percentages);
      
      foreach($percentages as $key=>$percentage)
      {
        $sorted[$valuta[$key]]['kleur']=$kleur[$key];
        $sorted[$valuta[$key]]['percentage']=$percentage;
      }
      $kleurdata = $sorted; //columnSort($kleurdata, 'pecentage');
      
      $pieData=array();
      $grafiekKleuren = array();
      
      $a=0;
      foreach($kleurdata as $key=>$value)
      {
        if ($value['kleur'][0] == 0 && $value['kleur'][1] == 0 && $value['kleur'][2] == 0)
          $grafiekKleuren[]=$standaardKleuren[$a];
        else
          $grafiekKleuren[] = array($value['kleur'][0],$value['kleur'][1],$value['kleur'][2]);
        $pieData[$key] = $value[percentage];
        $a++;
      }
    }
    else
      $grafiekKleuren = $standaardKleuren;
    
    $this->pdf->SetTextColor($this->pdf->pdf->rapport_fontcolor[r],$this->pdf->pdf->rapport_fontcolor[g],$this->pdf->pdf->rapport_fontcolor[b]);
    
    $this->pdf->rapport_printpie = true;
    foreach($pieData as $key=>$value)
    {
      if ($value < 0)
        $this->pdf->rapport_printpie = false;
    }
    
    if($this->pdf->rapport_printpie)
    {
      $this->pdf->SetXY($xstart, $ystart);
      $y = $this->pdf->getY();
      $this->pdf->SetFont($this->pdf->pdf->rapport_font,'b',10);
      $this->pdf->Cell(35,4,vertaalTekst($titel, $this->pdf->rapport_taal),0,1,"C");
      $this->pdf->SetFont($this->pdf->pdf->rapport_font,'',$this->pdf->pdf->rapport_fontsize);
      $this->pdf->SetX($xstart);
      $this->PieChart(40, 40, $pieData, '%l (%p)', $grafiekKleuren);
      $this->pdf->setY($y);
      $this->pdf->SetLineWidth($this->pdf->lineWidth);
    }
  }
  
  function PieChart($w, $h, $data, $format, $colors=null)
  {
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    //	$this->pdf->SetLegends($data,$format);
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $hLegend = 2;
    $radius = min($w - $margin * 4 - $hLegend , $h - $margin * 2); //
    $radius = floor($radius / 2);
    $XDiag = $XPage + $margin + $radius;
    $YDiag = $YPage + $margin + $radius;
    if($colors == null) {
      for($i = 0;$i < count($data); $i++) {
        $gray = $i * intval(255 / count($data));
        $colors[$i] = array($gray,$gray,$gray);
      }
    }
    
    //Sectors
    $sum=array_sum($data);
    $this->pdf->SetLineWidth(0.2);
    $angleStart = 0;
    $angleEnd = 0;
    $i = 0;
    foreach($data as $val) {
      $angle = floor(($val * 360) / doubleval($sum));
      if ($angle != 0) {
        $angleEnd = $angleStart + $angle;
        $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
        $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
        $angleStart += $angle;
      }
      $i++;
    }
    if ($angleEnd != 360) {
      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    }
    /*
        //Legends
        $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
        $x1 = $XPage ;
        $x2 = $x1 + $hLegend + $margin - 12;
        $y1 = $YDiag + ($radius) + $margin;
    
        for($i=0; $i<$this->pdf->NbVal; $i++) {
          $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->pdf->Rect($x1-12, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1);
          $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
          $y1+=$hLegend + $margin;
        }
    */
  }
  
  function addDoorkijk()//$belCategorien='')
  {
    global $__appvar;
    $pieTeller = 0;
    $db=new DB();
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $query = "SELECT doorkijkCategorie,doorkijkCategorieSoort,grafiekKleur, afdrukVolgorde
                   FROM doorkijk_categoriePerVermogensbeheerder
                   WHERE Vermogensbeheerder='$beheerder'
                   ORDER BY doorkijkCategorieSoort,afdrukVolgorde
                  ";
  
    $db->SQL($query);
    $db->Query();
    $this->kleuren=array();
    while($data = $db->nextRecord())
    {
      $this->kleuren[$data['doorkijkCategorieSoort']][$data['doorkijkCategorie']]=unserialize($data['grafiekKleur']);
    }
  
    unset($this->kleuren['Beleggingscategorien']);
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal , Beleggingscategorie, BeleggingscategorieOmschrijving
                  FROM TijdelijkeRapportage
                  WHERE rapportageDatum ='" . $this->rapportageDatum . "' AND portefeuille = '" . $this->portefeuille . "'" .	$__appvar['TijdelijkeRapportageMaakUniek'].
      " GROUP BY Beleggingscategorie ORDER BY BeleggingscategorieVolgorde";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      $kleurdata=$this->categorieKleuren[$data['Beleggingscategorie']];
      $this->kleuren['Beleggingscategorien'][$data['BeleggingscategorieOmschrijving']]=array($kleurdata['R']['value'],$kleurdata['G']['value'],$kleurdata['B']['value']);
    }

    foreach($this->categorieKleuren as $oibCat=>$kleurdata)
    {
      $this->kleuren['Beleggingscategorien'][$oibCat]=array($kleurdata['R']['value'],$kleurdata['G']['value'],$kleurdata['B']['value']);
    }
 
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal , Valuta, ValutaOmschrijving
                  FROM TijdelijkeRapportage
                  WHERE rapportageDatum ='" . $this->rapportageDatum . "' AND portefeuille = '" . $this->portefeuille . "'" .	$__appvar['TijdelijkeRapportageMaakUniek'].
      " GROUP BY Valuta ORDER BY ValutaVolgorde";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      $kleurdata=$this->valutaKleuren[$data['Valuta']];
      $this->kleuren['Valutas'][$data['ValutaOmschrijving']]=array($kleurdata['R']['value'],$kleurdata['G']['value'],$kleurdata['B']['value']);
    }
  
    foreach($this->valutaKleuren as $valuta=>$kleurdata)
    {
      $this->kleuren['Valutas'][$valuta]=array($kleurdata['R']['value'],$kleurdata['G']['value'],$kleurdata['B']['value']);
    }
    
    $doorkijkCategorieSoorten=array('Beleggingscategorien','Valutas','Beleggingssectoren');
    
    $doorkijkTitels=array('Beleggingscategorien'=>'Beleggingscategorie','Valutas'=>'Valuta','Beleggingssectoren'=>'Sector - Aandelen');//array();Beleggingscategorie
    foreach($doorkijkCategorieSoorten as $index=>$doorkijkCategorieSoort)
    {
      $xOffset =  $index * (98-5);
      if($doorkijkCategorieSoort=='Beleggingssectoren')
        $belCategorien=array('AAND');
      else
        $belCategorien='';
      
      $doorKijk= $this->bepaalWeging($doorkijkCategorieSoort,$belCategorien);
   //   listarray($this->kleuren);exit;
      
      $this->toonTabel($doorKijk['details'],$xOffset+45,$doorkijkTitels[$doorkijkCategorieSoort],$this->kleuren[$doorkijkCategorieSoort]);
      $grafiekdata=array();
      $grafiekTonen=true;
      foreach($this->kleuren[$doorkijkCategorieSoort] as $categorie=>$kleurdata) //foreach($doorKijk['categorien'] as $categorie=>$percentage)
      {
        if(isset($doorKijk['categorien'][$categorie]))
        {
          $percentage=$doorKijk['categorien'][$categorie];
          $grafiekdata[$categorie]['kleur'] = $this->kleuren[$doorkijkCategorieSoort][$categorie];//array('R' => array('value' => $kleuren[$categorie][0]),'G' => array('value' => $kleuren[$categorie][1]),'B' => array('value' => $kleuren[$categorie][2]));
          $grafiekdata[$categorie]['percentage'] = $percentage;
          if ($percentage < 0)
          {
            $grafiekTonen = false;
          }
        }
      }
   
      if($grafiekTonen==true)
        $this->printPie( $grafiekdata,15+$xOffset,100,$doorkijkTitels[$doorkijkCategorieSoort]); //+$yOffset);
      $pieTeller++;
    }
    
    if($this->debug)
    {
      $wegingSoorten=array('DoorkijkfondsWeging','MSfondsWeging','NietGekoppeld','afwijkingWeging');
      $vertaling=array('afwijkingWeging'=>'afwijkingWeging som<>100%');
      foreach($wegingSoorten as $wegingSoort)
      {
        $this->pdf->addPage();
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
        $this->pdf->setWidths(array(1,50,50,30));
        if(isset($vertaling[$wegingSoort]))
        {
          $this->pdf->row(array('', $vertaling[$wegingSoort]));
          $this->pdf->excelData[]=array($vertaling[$wegingSoort]);
        }
        else
        {
          $this->pdf->row(array('', $wegingSoort));
          $this->pdf->excelData[]=array($wegingSoort);
        }
        $startPage=$this->pdf->page;
        foreach($doorkijkCategorieSoorten as $index=>$doorkijkCategorieSoort)
        {
          $this->pdf->setWidths(array(1,45,45,15,30));
          $this->pdf->setAligns(array('L','L','L','R','R'));
          $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
          $this->pdf->row(array('',$doorkijkCategorieSoort));
          
          $categorieTotalen=array();
          $this->pdf->row(array('','fonds','categorie', 'perc','waarde'));
          $this->pdf->excelData[]=array('fonds','categorie', 'perc','waarde');
          $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
          foreach($this->debugData[$wegingSoort][$doorkijkCategorieSoort] as $fonds=>$verdelingData)
          {
            $n=0;
            foreach($verdelingData as $categorie=>$percentage)
            {
              if($wegingSoort=='afwijkingWeging')
              {
                $weging = $this->formatGetal($percentage['weging'], 5);
                $waarde = '';
              }
              else
              {
                $weging = $this->formatGetal($percentage['weging'], 2);
                $waarde = $this->formatGetal($percentage['waarde'], 2);
              }
              if($n==0)
              {
                $this->pdf->row(array('', $fonds, $categorie, $weging, $waarde));
                $this->pdf->excelData[]=array($fonds, $categorie, round($percentage['weging'],5), round($percentage['waarde'],2));
              }
              else
              {
                $this->pdf->row(array('', '', $categorie, $weging, $waarde));
                $this->pdf->excelData[]=array('', $categorie, round($percentage['weging'],5), round($percentage['waarde'],2));
              }
              $categorieTotalen[$categorie]['waarde']+=$percentage['waarde'];
              $n++;
            }
          }
          $this->pdf->ln();
          $this->pdf->excelData[]=array();
          foreach($categorieTotalen as $categorie=>$waarden)
          {
            $this->pdf->row(array('', 'Totaal', $categorie, '', $this->formatGetal($waarden['waarde'], 2)));
            $this->pdf->excelData[]=array('Totaal', $categorie, '', round($waarden['waarde'], 2));
          }
          $this->pdf->ln();
          $this->pdf->excelData[]=array();
          
        }
      }
      //	listarray($this->debugData);
    }
  }
  
  
  function printBenchmarkvergelijking()
  {
    global $__appvar;
    
    $this->checkImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkY3QkI0NkM0OTU5MTExRThCNUQ4QTdFOTc2OENEOEExIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkY3QkI0NkM1OTU5MTExRThCNUQ4QTdFOTc2OENEOEExIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6RjdCQjQ2QzI5NTkxMTFFOEI1RDhBN0U5NzY4Q0Q4QTEiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6RjdCQjQ2QzM5NTkxMTFFOEI1RDhBN0U5NzY4Q0Q4QTEiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5IXOqgAAADAFBMVEUzsRn29vZFtjIAjQIAmACl1aJ7e3tNiE3d3d35+fkAbSz+/v5Zd1lyy1MAbDP7+/tsxVwXkBfC4cHH1cc6sij09PS7u7sAlAANkgdZwEOd05Ty8vIUogqIiIgAcytKqUqEk4TV1dVhyTG65KZXtFZGd0aR2m08ripvum/w8PAAkQDg4OCoqKgwmzBra2suph9jxEvt8u1dv01zy1uo2ZlTp1OD02UkqhEqmirt7e1KvSRFuiEXdBd5tnkKnQSb3XoqrRQAcSQAeiNUwinr7OuKxYfm5uZeaF5SwSgfgB+9071CuSA6oTqN2GlkaWR6pHoxdjF60k7k5OSL1mxyz0dcxi1ZxSwAawUVbhWazJo+tx4Abxt80F06tRwAghVcxDeam5ojqRECfQICcwMEhgQEmgFMviXq7urT3dNtyVTa2tro6OjAwMCwy7AAghourxaWlpZkyzJUvTxpmmlpyUUMngbq6uri4uKA01wppRsAchVkyzSS2XKUzI1KujEkoxmU23Edpw54zlwAfRvN5crD6bANlwcAbQ1vzj+JoYkapQ0AeRMAhg7Ly8tgw0QAdAyJ1mYAigl4zWAXmg2Y3HYHnAMCihMGkhDx9PB+0GMAdhdrx1ZmxVIBkAoSgBLE6rHn6uaW3HPd4d0LkwrG7LSG1GwCZwJ2hXaV3HIAkwfv8+8AbzAPoAdowVppzThWwyoaog1IvCPI7LUAfAhAuB83tBtexy4IkQYKfgoAdycnqxIOjA78/PxmzDP9/f1QwCdHvCNYxCtPwCc/uB8XpAsorBMwsBcgqA8vsBcRoQhgyC8fqBAInAO4uLid2IUIlgOlxKX4+/genRc+gz7b7ts1rxpEszDj5uMFjwNkcGQxqyEPhg8NkAdopmgfoxPe8N53zlWqsqomiiYnqhxozDZHvCQ3sh1Ctynx+PHA5q+AxICXtpfg4+CGzXmNsI2ZrJnT49PT69Pa59bj7t4mqxVxm3Fjul+2u7YAgCEDihkhcCE/tDMeoA/p6elRvEH///9r2XbkAAABAHRSTlP///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////8AU/cHJQAABaxJREFUeNqUlnlUE1cUhzMEAQMhiEQKSNBIgmHYFCHEKIaAwRbBFAQiCkIWXEAW27iACwiCoIIWRHA5Wqt4MCBalJh0CIIsIlpb2lJEamMXba3dW2u1St+bCQHEpf39mfN99907mffekAb/Z0hjfvl03HSXhZ6eC12mL/kF0b1K+PJeQF+Zt1uvSa+bd9mAPGDuyvEvE351VW9dq9+wYdk2PXWVj4+DycYBud9E5EXCd9P7+vXh4Xv2+PtXLfOl+pQ4ODzd0j8g/+LY+OcK+QFnqGw8Ufq3l7deDIuLP0+6Y3fnlvyrNRzdWGGcujecnZPzbvmexY0twa3JhJAu0P7RPXDhpxDsWWGJ2oEN6PLwxZlXoluCm4FwiRCScosLpItEtNGCo9oH8PPK32xqymTiwlk+FC7jQluBtEiEjRRs1SWg/Dz2jqbnC13qCz+v0w0Lp3p62YAP/6YJCmNbapuslL/+F2dYcO1h54D6TTiPC8NDc7UCQWeXcr/UfCoyJNiqqezyeTmnCZ7ZGFO/oHU2nxC0M23zHbek1xZUyIoidQbBpT8cLLBjiDeMAARSh7buKI/H+60k/erfDZ+k0gkhv48atal8QyYe5pVouIBhhPa6FRyY/OauigrZFFSHCw+2+kdt2sTEc6UR8GACoiPIO8Fw9Nz9aQ3mqQgUPp6wFgiLoxthomNa6kFDgL8Ud15QN8OJjsdJrz38MGFOYgoUbPuo/lFRMS0xMC319QsAf5F/DvA2M+h0BM+0aGXNwzTZydU0INwr2+Z/Qh8cXA8THAz6BzxoKN3mFGLI90yuouZhXoMV6Ik06Oe9rerE8tbmBRc/u9Pa3Jps5GcgyHg8gN9nWiMUlrJu84AQ4OZbVZWcfPYfd88eJT95NnigOF+J4zQatp3ZflVhelgo3CnOEAFhQq/vO1R+WIHnG/ePudZe4vPDzg3xNAzTWR5htgfuA4JGs3OOMwqED0xW+ZaElS1ciaLr77u2xZ2Li4u/bFMJqgPa0vJIpkAJFjCt0GgOSMwsoNB70Hdt+nsTLdD1kaLPH3TGx5/vgDyN4LMFSriAfRoDCqthS26HDm4542eBQiHlaF17Rwfsh4ZZEnytEvCm9kKjEODtc+juGZdjKBopEj0JCRn3oyPsn+AzAQ8bsndnMBhZYlxw6Znpc1egmI8vsC6Ex3Giw3lhP+/D+oH4AhUqBqOUhQ89d+DpTLt0bu18QoC8oaERvLtQpWLEWmWsB8Ia+WMHO4GA27YZdsQzCLpRvD0Z8EGxNzzgH7fi5sandlptOzdpc8qToY7AAsdH8O55KhUlSxIBdgRpkOY3YGKXxAUROBpH0I3mySpriiqWZTYVvnyDa6T9j3O7uzs7uSRHHiHQdMez25VG3l1obU0Jqo5IFOEbiOP5ml13cXFubi43bhYQ4AhHsrXKwKsKnLcn7wI8pVDsBfcoEHQfSW993YYn6dtZuLA9m0uUx+vvVQFhaXWos2GLDlaay7dOxtOV1Pzh7/TKadlcgBt4MhnylFiW1yQnw6mBTbypbqslkjubueN0S6cRJ+pbUwoloRkoNnSQIT9IryuJBAZ2FU8mcJwn78J50JDZbmT4bOUtkl7fR0ShIGiiPDnPmuCtvK7xRhzGWORbUneFkcWfDcQTGBDHeQ+RbuRxT0P/lJHtjbARpwCcUlhtFeGB0kZfKDS0aI507+GaIXhvAnjdVICmLI2VhHoZ+eEriya6bS5rSEhLSEhLyxNqwOvPoAQBvLCaFWp2zXgBjbgUsZDdU8Sy0p15eUKhRqNhBIEAXBzqlbGbgz33nkbQSVPMJTJZaemBA1lZWYWx1RJWhJfzJBR50cWOOVmkJp60YokfSSSPxCyrGxFmiakoHXvJp4MOEU1NvZ3hbAbinOGRujrl2c+NsR8nGMIToRYgqIiHYP/ha+YV+VeAAQBsubMyVT2e/wAAAABJRU5ErkJggg==');
    $this->deleteImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjA0RjcxMTA2OTU5MjExRThBRDAyRkYzMjNGRThERUQxIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjA0RjcxMTA3OTU5MjExRThBRDAyRkYzMjNGRThERUQxIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MDRGNzExMDQ5NTkyMTFFOEFEMDJGRjMyM0ZFOERFRDEiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MDRGNzExMDU5NTkyMTFFOEFEMDJGRjMyM0ZFOERFRDEiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz6gm9BkAAADAFBMVEX1+/3l6eqcnJy8EQDt7e3o2Nb/qYplX1+yMShoZmbnNhHd3d31fWXcxcL/fFL4TiTUHQDaIQK0DQCaJyb5//+td3P+4dr7LQCsVE2qqqrRvbz+jWv9a0B6enr4+Pjs8vTm5ub/ekz6w7PBFAD7+/vKu7ruHADjo5X/YTHzWTP/Xi7/gVn/gFLZ5Ob/aTn9MQD38e+NjY3/lHH/pIXacWH/NAH6+vr/ZjaoCAH/OAWXZ2SFhYXz8/P/bj7/kG3qKAD1LgD/nn3n6Ojk8vX/WSnWjoXuRib/uaT/ckb5MAD5Yz3yLACCW1vr7e3tKgDoxLrnl4f9flybDw/BNif/ViXj4+P/Th3U5Or19fXqjnb/UiHgIwD/PQrtMRD/imbNGgD/QA3oJgD/SRbg4OD/aTzr6+vkJQDrQCP4YTr/w6zbPijJycn/PgyXPTxua2va2trNzc3/NwT/d0nwKwD/RBL29vbjUz3i5OT96eXrdl//UR7iHwD/cUL/QhD59/b6VSj/dkb/mXj/hWDe1NLq5uX/SxjinJL4n4bFVUf/l3XYTz7/ooTw9ff6QRH3q5b/poj/Ogi2HRH0UCT/ooL/RxXzOQzkKQTh7vHb6e38o5LvRRzq0s///f3/XCv+WS//dUy3DwCVU1GMTk7/TRv/nXv9dEv/YC+xpaTphW32dE7p9vmjmZj7YTHvPxXIFQD6PQ763NfxUiz/d1FwZWX/VSTdNh6UQUH////w8PD+/v7+//+ysrL/YzPp6en+7+z+5uH/q4/R0ND4ckzZ3d/qLwX9/f3vVCv8///nMArvYUnyzMP91szm4uLzIwD5IAD5+/z5+fnFIxKzFgrQJxD4WCj4WS//n3/f7O/0iGbl9vr5KQDu+fzNHQmPgYHn7O3/VyjS4OTvLgjgh3uQgIDGFwD/zr7d3+Db29zbsqz4kX+ZLi335+L06uf37+vh5+n1YDj09/n3Qxfo6uv0TR//eEje5+nqKQf28fDY2dnnIwDwpJfd0M/wY0nguK//pIP///9xnuFlAAABAHRSTlP///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////8AU/cHJQAABdNJREFUeNq0lntcU2UYx5GDgM7mNiagwBHc1MFwU8bitrExUEcCisCsbbINxlQuOtgEubpRqKQIKqGCywmId1BDx1Uxo+ymZKVFBWUzky4WUYoW9J6zsyRD/fTp0++P89fv+z7P8z7P+77HZvRfyua/Ayfv52annjmTmu2/qaao+2nAhtuL3tj53EE6Hi9cPXUyMfXGxJ4nAeenfOWdGKYv9XNzKy6OpZ8u2HiJ6F8DPw7Yt3fl6jDWtXq9Xl8aSscLlUqlu3v/pQ8XdlLHBc79fqK0fNeuEdbWUDy3o8EQES/Oq0sSicuIWx7A4wDOi+7Yj4yw7MvddFwuIwAQ/c1iUUmltrCwKm4G5x+A86rX7Vks+67QoSxdAVeJABEIkKQtlEp5cbM5jwDnFgF/eRcLP+SFHxJylQwMqEOAiqAxhAXY9/YdxF8/5OXrPrk6C4sQn9frIkeATDbR9QI8Fth7wn6kvEuvw98hEhcSlQUMC9C7edarTCkC8M3TcNSHwIaVfixW1zWd15uEb3eHL4sBAA0ALrFkMnmDvi4okx0p2XEo5SEwxZu11d4+y8ur8davyTnhkzcqA2g+hszV5CvU7rfeLQEAUyVzVfdYgfO/6a9t7aJneeHzn8lJTuhbOofRQWtIn0omd3eb2vqlQWxmpEJy9ZinFbh9KiyMVVqQleVV7X89IUFwNLcpgKYtI5OprW2mRG0FAvAI5mB1EQasitXXlwsLhEKdu2R3jqcgeuaS5sqmIuA3td4sqQA1M/kaAsFxup0FOFlVWhoWyuAWcAt0ij8meHambHvnMvB3m0ymfQa5FKmZr5ERjF/jLMD9aj+/sCwGV8nlcqdmhws6o1tsts8nU7tbTSZ6khTNaIlGJoOC02AUyPUO9dPTAhiIlHMm9qXYtXB+sumhggCL69KlaEYIQDnQ3oIC2e5uocUNHR0BQIyy3KPRAHgefhZUcL5Zno4G4GtiZI3mDFsBCqQeTAwV+jTQOmhADe+/hAGgBKVWjgWIUZjNjQMLElDgzP5itwCDwQcVzWXKTBSgdpvoJVKpNYCK0tjoFJJsAYT0xIj+iIgIg8EQ3+uNRQDAYgNIyFIBj0ihmJ08cJaUlLHF4ub4+Ph+Q9nlZZ8OplgB001GOtsSgEegUCgDGPDyx/jYOrFYHC/qPYXrEwhSsBrArjrrCl0iQQU8BYUCQRlYSv7Vp/ElInB4NXOv54BGIwAH7gG7ZDKdKxb1goQUEgiCjLVY0ZsuHTydVFInipy7B5ecgALb1q/vQRtnMoUdB1ukkAGAFGU7iAI1BOX+yqSkEvbheQBI8OyM/mz79zZoI4Ba1xwfVqkgIwQFvoY1rihOJEzSaivZ3vNuISEGj27OE961gbEQa34ZVhGMRiPktBYbjdEbVe4iuTbz5z8uIsCEcDdxZiV9vg2WlC9PIgF+IykqBBu+0Ymfi/vT5ezDK+6twyX/+B5eFBTExoi2Nr/qfIkZCbDcwTreo+v9q3wqKirkX967982epQGV7EzQLa3vD+BEO9fvzJcQSCSSMbDWQw1bz3TNB01asOoLK1a8+EleBZuNdKu3yfe7Wb7HgZ9oRIDlDsjoYQC8UCYHLvmRI+JCJpPNZEZG8vmaV3QfxeRb/YFRlgDYvdS5hZjJZDKDkA/qBuMTwxseVkkkBMRPCjw7Ca3AClx5EKcCRkR8xI3YeQpgl8hI6PoDDrZjLzJQ9wxHCeoE0mhiUDuyPIWEyskh5O9X5ejo3dmOBNSKmHkKYFcBO7L9YH3gT+M8+j5wZrtCEhXvLzdRBsbB4j87xj/mBeKop+1oJKACBx5MM5hPoMDlUZNsL3DGe+Ng3CHXq5AZnPZGigWAgL3WwaN9HTz+K0rtUx8LdiSRIAgBjKTAQKcoBw9bdTT1se80LFBP/yL4QMaAk9NARm3UpLUh09UC+Il/ArDdxbR22wUhQAts29Nwdj1P/9eAWzoTknG45JzBFvh/+Tl5iv4UYABojC5AyFTaQwAAAABJRU5ErkJggg==');
    
    
    $DB = new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];
    
    $zorgplichtcategorien=array();
    $query="SELECT waarde as Zorgplicht FROM KeuzePerVermogensbeheerder WHERE Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND categorie='Zorgplichtcategorien' ORDER BY Afdrukvolgorde";
    $DB->SQL($query);
    $DB->Query();
    while($data=$DB->nextRecord())
      $zorgplichtcategorien[$data['Zorgplicht']]=$data;
    
    
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal,
              ZorgplichtPerBeleggingscategorie.Zorgplicht,
              beleggingscategorieOmschrijving ".
      "FROM TijdelijkeRapportage
             INNER JOIN ZorgplichtPerBeleggingscategorie ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
             WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek']."
              GROUP BY Zorgplicht
              ORDER BY beleggingscategorieVolgorde";
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    while($data=$DB->nextRecord())
    {
      $zorgplichtcategorien[$data['Zorgplicht']]=$data;
      $verdeling[$data['Zorgplicht']]['percentage'] = $data['totaal']/$totaalWaarde*100;
    }
    
    $query = "SELECT Portefeuilles.Portefeuille, Portefeuilles.Risicoklasse, ZorgplichtPerRisicoklasse.Zorgplicht,
    ZorgplichtPerRisicoklasse.Minimum,
ZorgplichtPerRisicoklasse.Maximum,
ZorgplichtPerRisicoklasse.norm,
Zorgplichtcategorien.Omschrijving
FROM Portefeuilles
INNER JOIN ZorgplichtPerRisicoklasse ON Portefeuilles.Risicoklasse = ZorgplichtPerRisicoklasse.Risicoklasse AND ZorgplichtPerRisicoklasse.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
JOIN Zorgplichtcategorien ON ZorgplichtPerRisicoklasse.Zorgplicht=Zorgplichtcategorien.Zorgplicht AND Zorgplichtcategorien.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
WHERE Portefeuilles.Portefeuille='".$this->portefeuille."' ORDER BY Zorgplicht";
    $DB->SQL($query);
    $DB->Query();
    $zorgplichtPerPortefeuille=false;
    while($zorgplicht = $DB->nextRecord())
    {
      $zorgplichtPerPortefeuille=true;
      $zorgplichtcategorien[$zorgplicht['Zorgplicht']]=$zorgplicht;
    }
    
    if($zorgplichtPerPortefeuille==false && count($this->pdf->portefeuilles)>1)
    {
      foreach($this->pdf->portefeuilles as $port)
      {
        $pWaarde=0;
        $precs=berekenPortefeuilleWaarde($port,$this->rapportageDatum, (substr($this->rapportageDatum,5,5)=='01-01')?true:false,$this->pdf->portefeuilledata['RapportageValuta'],$this->rapportageDatum);
        foreach($precs as $rec)
        {
          $pWaarde+=($rec['actuelePortefeuilleWaardeEuro']/$this->pdf->ValutaKoersEind);
        }
        $pAandeel=$pWaarde/$totaalWaarde;
        
        $query = "SELECT Portefeuilles.Portefeuille, Portefeuilles.Risicoklasse, ZorgplichtPerRisicoklasse.Zorgplicht,
    ZorgplichtPerRisicoklasse.Minimum,
ZorgplichtPerRisicoklasse.Maximum,
ZorgplichtPerRisicoklasse.norm,
Zorgplichtcategorien.Omschrijving
FROM Portefeuilles
INNER JOIN ZorgplichtPerRisicoklasse ON Portefeuilles.Risicoklasse = ZorgplichtPerRisicoklasse.Risicoklasse AND ZorgplichtPerRisicoklasse.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
JOIN Zorgplichtcategorien ON ZorgplichtPerRisicoklasse.Zorgplicht=Zorgplichtcategorien.Zorgplicht AND Zorgplichtcategorien.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
WHERE Portefeuilles.Portefeuille='".$port."' ORDER BY Zorgplicht";
        $DB->SQL($query);
        $DB->Query();
        while($zorgplicht = $DB->nextRecord())
        {
          $zorgplichtcategorien[$zorgplicht['Zorgplicht']]['norm']+=$zorgplicht['norm']*$pAandeel;
        }
      }
    }
    
    $query="SELECT vanaf FROM ZorgplichtPerPortefeuille WHERE ZorgplichtPerPortefeuille.Portefeuille='".$this->portefeuille."' AND vanaf < '".$this->perioden['eind']."' ORDER BY vanaf desc limit 1";
    $DB->SQL($query);// echo $query;exit;
    $DB->Query();
    $datum=$DB->nextRecord();
    if($datum['vanaf'] <> '')
      $vanafWhere="AND vanaf='".$datum['vanaf'] ."'";
    else
      $vanafWhere='';
    
    $query="SELECT
ZorgplichtPerPortefeuille.Zorgplicht,
ZorgplichtPerPortefeuille.Portefeuille,
ZorgplichtPerPortefeuille.Vermogensbeheerder,
ZorgplichtPerPortefeuille.Minimum,
ZorgplichtPerPortefeuille.Maximum,
ZorgplichtPerPortefeuille.norm,
Zorgplichtcategorien.Omschrijving
FROM
ZorgplichtPerPortefeuille
JOIN Zorgplichtcategorien ON ZorgplichtPerPortefeuille.Zorgplicht=Zorgplichtcategorien.Zorgplicht AND Zorgplichtcategorien.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
WHERE ZorgplichtPerPortefeuille.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND ZorgplichtPerPortefeuille.Portefeuille='".$this->portefeuille."' $vanafWhere
 ORDER BY Zorgplicht";
    $DB->SQL($query);
    $DB->Query();
    while($zorgplicht = $DB->nextRecord())
    {
      $zorgplichtcategorien[$zorgplicht['Zorgplicht']]=$zorgplicht;
    }
    /*
    $zorgplcihtConversie=array('Zakelijke waarden'=>'H-Aand','Alternatieven'=>'H-AltBel','Vastrentende waarden'=>'H-Oblig','Liquiditeiten'=>'H-Liq');
    foreach($zorgplichtcategorien as $zorgplicht=>$zorgplichtData)
    {
      $query="SELECT IndexPerBeleggingscategorie.Fonds,Fondsen.Omschrijving FROM IndexPerBeleggingscategorie
      JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds = Fondsen.Fonds
      WHERE Categoriesoort='Beleggingscategorien' AND Categorie='".$zorgplcihtConversie[$zorgplicht]."' AND Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
      AND (IndexPerBeleggingscategorie.Portefeuille='' OR IndexPerBeleggingscategorie.Portefeuille='".$this->portefeuille."') ORDER BY IndexPerBeleggingscategorie.Portefeuille desc limit 1";
      $DB->SQL($query);
      $DB->Query();
      $data = $DB->nextRecord();
      
      $zorgplichtcategorien[$zorgplicht]['fonds']=$data['Fonds'];
      $zorgplichtcategorien[$zorgplicht]['fondsOmschrijving']=$data['Omschrijving'];
    }
    */
    //listarray($zorgplichtcategorien);exit;
    //$index=new indexHerberekening();
    //$perioden=$index->getMaanden(db2jul($beginDatum),db2jul($eindDatum));
    
    foreach($zorgplichtcategorien as $zorgplicht=>$zorgplichtData)
    {
      
      
      $query="SELECT vanaf FROM benchmarkverdelingVanaf WHERE benchmark='".$zorgplichtData['fonds']."' AND vanaf < '".$this->perioden['eind']."' ORDER BY vanaf desc limit 1";
      $DB->SQL($query);
      $DB->Query();
      if($DB->records()>0)
      {
        $datum = $DB->nextRecord();
        $query = "SELECT benchmarkverdelingVanaf.fonds,benchmarkverdelingVanaf.percentage,Fondsen.Omschrijving FROM benchmarkverdelingVanaf
        JOIN Fondsen ON benchmarkverdelingVanaf.fonds = Fondsen.Fonds
        WHERE benchmark='".$zorgplichtData['fonds']."' AND vanaf = '" . $datum['vanaf'] . "'";
        
      }
      else
      {
        $query = "SELECT benchmarkverdeling.fonds,benchmarkverdeling.percentage,Fondsen.Omschrijving
        FROM benchmarkverdeling
        JOIN Fondsen ON benchmarkverdeling.fonds = Fondsen.Fonds
        WHERE benchmark='" . $zorgplichtData['fonds'] . "'";
      }
      $DB->SQL($query);
      $DB->Query();
      while ($data = $DB->nextRecord())
      {
        $zorgplichtcategorien[$zorgplicht]['fondsSamenselling'][$data['fonds']] = $data;
      }
    }
    
    
    
    
    //$this->pdf->ln();
    //$this->pdf->ln(2);
    $margeLinks=140;
    $this->pdf->setXY($this->pdf->marge+$margeLinks,146);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(100,4, vertaalTekst("Vermogensverdeling per",$this->pdf->rapport_taal)." ".date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum),0,0);
    $this->pdf->ln(2);
    $this->pdf->ln();
    $this->pdf->SetTextColor(0,0,0);
    
    $this->pdf->SetWidths(array($margeLinks,30,15,15,15,15,30,20,20,20));
    $xVinkPlaatje=$margeLinks+30+15+15+15+15+30+20+2;
    $this->pdf->SetAligns(array('L','L','R','R','R','R','R','R','R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','',vertaalTekst('Min.',$this->pdf->rapport_taal),vertaalTekst("Norm",$this->pdf->rapport_taal),vertaalTekst("Max." ,$this->pdf->rapport_taal),
                      vertaalTekst("Huidig",$this->pdf->rapport_taal),vertaalTekst("Tactische onder- /overweging",$this->pdf->rapport_taal),vertaalTekst("Profiel controle",$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach($zorgplichtcategorien as $zorgplichtCategorie=>$zorgData)
    {
      if(!isset($verdeling[$zorgplichtCategorie]['percentage']))
        $verdeling[$zorgplichtCategorie]['percentage']=0;
      
      if ($verdeling[$zorgplichtCategorie]['percentage']<=$zorgData['Maximum'] && $verdeling[$zorgplichtCategorie]['percentage']>=$zorgData['Minimum'] )
        $this->pdf->MemImage($this->checkImg, $xVinkPlaatje, $this->pdf->getY(), 3.5, 3.5);
      else
        $this->pdf->MemImage($this->deleteImg, $xVinkPlaatje, $this->pdf->getY(), 3.5, 3.5);
      
      $this->pdf->row(array('',vertaalTekst($zorgData['Omschrijving'],$this->pdf->rapport_taal),
                        $this->formatGetal($zorgData['Minimum'],1).'%',
                        $this->formatGetal($zorgData['norm'],1).'%',
                        $this->formatGetal($zorgData['Maximum'],1).'%',
                        $this->formatGetal($verdeling[$zorgplichtCategorie]['percentage'],1).'%',
                        $this->formatGetal($verdeling[$zorgplichtCategorie]['percentage']-$zorgData['norm'],1).'%'));
      
    }
    
    
    // listarray($zorgplichtcategorien);
    
    
  }
}
?>
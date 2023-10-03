<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/06/17 15:38:53 $
File Versie					: $Revision: 1.10 $

$Log: RapportPERFD_L75.php,v $
Revision 1.10  2020/06/17 15:38:53  rvv
*** empty log message ***

Revision 1.9  2020/06/03 15:41:22  rvv
*** empty log message ***

Revision 1.8  2020/04/29 09:44:14  rvv
*** empty log message ***

Revision 1.7  2018/10/09 05:31:26  rvv
*** empty log message ***

Revision 1.5  2018/10/08 06:58:27  rvv
*** empty log message ***

Revision 1.4  2018/07/04 08:25:01  rvv
*** empty log message ***

Revision 1.3  2018/06/23 14:21:39  rvv
*** empty log message ***

Revision 1.2  2018/06/09 15:58:54  rvv
*** empty log message ***

Revision 1.1  2018/05/30 16:10:58  rvv
*** empty log message ***

Revision 1.9  2018/05/23 13:51:26  rvv
*** empty log message ***

Revision 1.8  2018/04/30 05:37:37  rvv
*** empty log message ***

Revision 1.7  2018/04/07 15:21:44  rvv
*** empty log message ***

Revision 1.6  2018/03/31 18:06:01  rvv
*** empty log message ***

Revision 1.5  2018/03/14 17:17:41  rvv
*** empty log message ***

Revision 1.4  2018/03/11 10:53:28  rvv
*** empty log message ***

Revision 1.3  2018/03/10 18:24:22  rvv
*** empty log message ***

Revision 1.2  2018/03/03 17:13:43  rvv
*** empty log message ***

Revision 1.1  2018/02/28 16:48:45  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");


class RapportPERFD_L75
{
	function RapportPERFD_L75($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFD";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Historisch beleggingsresultaat";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;
		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	  $this->tweedeStart();

	  $this->rapportageDatumVanaf = "$RapStartJaar-01-01";


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
  //  $this->categorieKleuren['G-LIQ']= $this->categorieKleuren['Liquiditeiten'];


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

	  $indexDatum=substr($this->pdf->PortefeuilleStartdatum,0,10);

  $index=new indexHerberekening();
  $maanden=$index->getMaanden(db2jul($indexDatum),db2jul($this->rapportageDatum));
  $indexData=array();
  foreach ($maanden as $periode)
  {
    $indexData[]=array('datum'=>$periode['stop'],'index'=>100,'waardeHuidige'=>0,'specifiekeIndex'=>100,'extra'=>array('cat'=>array('VAR'=>0,'ZAK'=>0)));
  }



  $indexDataReal = $this->getWaarden($indexDatum ,$this->rapportageDatum ,$this->portefeuille);
	foreach($indexDataReal as $data)
		foreach($data['extra'] as $categorie=>$waarde)
			 $categorien[$categorie]=$categorie;
//listarray($indexDataReal);

//		$q="SELECT BeleggingscategoriePerFonds.Beleggingscategorie,
//Beleggingscategorien.Omschrijving FROM BeleggingscategoriePerFonds
// INNER JOIN Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
// WHERE Vermogensbeheerder =  '".$beheerder."' GROUP BY  BeleggingscategoriePerFonds.Beleggingscategorie ORDER BY afdrukvolgorde asc"; //WHERE Beleggingscategorie IN('LIQ','ZAK','VAR','Liquiditeiten')
		$q="SELECT Beleggingscategorien.Beleggingscategorie,Beleggingscategorien.Omschrijving 
FROM KeuzePerVermogensbeheerder 
JOIN Beleggingscategorien ON  KeuzePerVermogensbeheerder.waarde=Beleggingscategorien.Beleggingscategorie AND KeuzePerVermogensbeheerder.categorie='Beleggingscategorien' AND vermogensbeheerder='".$beheerder."'
ORDER BY KeuzePerVermogensbeheerder.afdrukvolgorde asc";
		$DB->SQL($q);
		$DB->Query();
		while($data=$DB->nextRecord())
		{
			//$categorien

			$this->categorieVolgorde[$data['Beleggingscategorie']]=0;//$data['hoofdcategorie'];
			$this->categorieOmschrijving[$data['Beleggingscategorie']]=vertaalTekst($data['Omschrijving'],$this->pdf->rapport_taal);
		}
//listarray($this->categorieOmschrijving);exit;


		foreach ($indexData as $index=>$maanden)
  {
    foreach ($indexDataReal as $realData)
    {
      if($realData['datum'] == $maanden['datum'])
        $indexData[$index]=$realData;
    }
  }


foreach ($indexDataReal as $index=>$data)
{
  if($data['datum'] != '0000-00-00')
  {
    foreach ($data['extra']['cat'] as $categorie=>$waarde)
    {
      if($categorie=='LIQ')
        $categorie='VAR';
      if($waarde <> 0)
        $gebruikteCategorie[$categorie]=$categorie;
    }
  }
}
$nieuweVolgorde=array();
foreach($this->categorieVolgorde as $categorie=>$waarde)
{
  if(in_array($categorie,$gebruikteCategorie))
    $nieuweVolgorde[$categorie]=$categorie;
}


$huidigeJaar=substr($this->rapportageDatum,0,4);
$huidigeJaarStartJul=db2jul(substr($this->rapportageDatum,0,4)."-01-01");
$this->categorieVolgorde=$nieuweVolgorde;

$somVelden=array('stortingen','onttrekkingen','gerealiseerd','ongerealiseerd','opbrengsten','kosten','rente','resultaatVerslagperiode');

		$barGraph=array();
    $lastJaar=0;
    $indexJaren=array();
foreach ($indexData as $index=>$data)
{
  if($data['datum'] != '0000-00-00')
  {
    $jaar=substr($data['periode'],0,4);
    if(1 || db2jul($data['datum']) <  $huidigeJaarStartJul)
    {
      if($jaar <> $lastJaar)
      {
        $jaarPerf=1;
        if(isset($indexJaren[$lastJaar]))
        {
          $rendamentWaarden[]=$indexJaren[$lastJaar];
          unset($indexJaren[$lastJaar]);
        }  
      }
      $jaarPerf=($jaarPerf * ($data['performance']+100)/100);
      $data['performance']=$jaarPerf*100;

      if(!isset($indexJaren[$jaar]))
        $indexJaren[$jaar]['waardeBegin']=$data['waardeBegin'];
      $indexJaren[$jaar]['waardeHuidige']=$data['waardeHuidige'];  
      $indexJaren[$jaar]['performance']=$data['performance']-100;
      $indexJaren[$jaar]['datum']=$jaar;
      
      $indexJaren[$jaar]['index']=$data['index'];
      foreach($somVelden as $veld)    
        $indexJaren[$jaar][$veld]+=$data[$veld];  
      $lastJaar=$jaar;
      
//      if($jaar==2015)
//      {
//        echo $data['datum']." ".$data['resultaatVerslagperiode']." -> ".$indexJaren[$jaar]['resultaatVerslagperiode']."<br>\n";
//      }
      
    }

  }
}
//listarray($rendamentWaarden); listarray($indexData);
$rendamentWaarden[]=$indexJaren[$lastJaar]; // lopende jaar toevoegen.

//listarray($barGraph['Plan']);ob_flush();

$grafiekData['Datum'][]="$RapStartJaar-12-01";
$firstMonth=true;
   if(count($rendamentWaarden) > 0)
   {
        $n=1;
        $this->pdf->fillCell = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
     //   $this->pdf->CellBorders = array('','US','US','US','US','US','US','US','US','US','US','US');
        $this->pdf->underlinePercentage=0.8;

       // $this->pdf->SetFillColor(221,224,229);
		    $this->pdf->SetFillColor($this->pdf->rapport_regelAchtergrond[0],$this->pdf->rapport_regelAchtergrond[1],$this->pdf->rapport_regelAchtergrond[2]);
        $totaalRendament=100;
        $totaalRendamentIndex=100;
		    foreach ($rendamentWaarden as $row)
		    {
		      $resultaat = $row['Opbrengsten']-$row['Kosten'];
          if(strlen($row['datum'])==4)
          {
            $datum = $row['datum'];
            $datumTxt = $row['datum'];
          }
          else
          {
            if($firstMonth==true)
            {
              $this->pdf->Ln(4);
              $firstMonth=false;
		        }
            $datum = db2jul($row['datum']);
            $datumTxt = date("Y",$datum).' '.vertaalTekst($__appvar["Maanden"][date("n",$datum)],$this->pdf->rapport_taal);
          }
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
					$barData[$datumTxt]['Totaal']=($row['waardeHuidige']);
		      $this->pdf->row(array($datumTxt ,
		                           $this->formatGetal($row['waardeBegin'],0),
		                           $this->formatGetal($row['stortingen']-$row['onttrekkingen'],0),
		                           $this->formatGetal($row['gerealiseerd']+$row['ongerealiseerd']+$row['rente'],0),
		                           $this->formatGetal($row['opbrengsten'],0),
		                           $this->formatGetal($row['kosten'],0),
		                           $this->formatGetal($row['resultaatVerslagperiode'],0),
		                           $this->formatGetal($row['waardeHuidige'],0),
		                           $this->formatGetal($row['performance'],2).'%',
		                           $this->formatGetal($row['index']-100,2).'%'));

		                           if(!isset($waardeBegin))
		                             $waardeBegin=$row['waardeBegin'];
		                           $totaalWaarde = $row['waardeHuidige'];
		                           $totaalResultaat += $row['resultaatVerslagperiode'];
		                           $totaalGerealiseerd += $row['gerealiseerd'];
		                           $totaalOngerealiseerd += $row['ongerealiseerd'];
		                           $totaalOpbrengsten += $row['opbrengsten'];
		                           $totaalKosten += $row['kosten'];
		                           $totaalRente += $row['rente'];
					                     $totaalOntrekkingen += $row['onttrekkingen'];
				                       $totaalStortingen += $row['stortingen'];
		                           $totaalStortingenOntrekkingen += $row['stortingen']-$row['onttrekkingen'];
		                           $totaalRendament = $row['index'];

		    $n++;
		    }
		    $this->pdf->fillCell=array();


        $this->pdf->ln(3);
        $this->pdf->CellBorders = array('','TS','TS','TS','TS','TS','TS','TS','','TS');
		    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		    $this->pdf->row(array(vertaalTekst('Samenvatting',$this->pdf->rapport_taal),
		                           $this->formatGetal($waardeBegin,0),
		                           $this->formatGetal($totaalStortingenOntrekkingen,0),
		                           $this->formatGetal($totaalGerealiseerd+$totaalOngerealiseerd+$totaalRente,0),
		                           $this->formatGetal($totaalOpbrengsten,0),
		                           $this->formatGetal($totaalKosten,0),
		                           $this->formatGetal($totaalResultaat,0),
		                           $this->formatGetal($totaalWaarde,0),
		                           '',
		                           $this->formatGetal($totaalRendament-100,2).'%'
		                           ));//$this->formatGetal($totaalRendamentIndex-100,2)
		   $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		   $this->pdf->CellBorders = array();

		 $totaalRegel=array('vanaf'=>$indexDatum,'beginvermogen'=>$waardeBegin,'resultaat'=>$totaalResultaat,'onttrekkingen'=>$totaalOntrekkingen,'stortingen'=>$totaalStortingen,'eindvermogen'=>$totaalWaarde);
		  }
		$this->toonSamenvatting($totaalRegel);

          if (count($barData) > 0)
          {
            $this->pdf->SetXY(15,170)		;//112
						$this->VBarDiagramVermogen(167,165,120,50,$barData,'Historische ontwikkeling vermogen');
          }
		/*
            if (count($grafiekData) > 1)
            {
              $this->pdf->SetXY(8,109+37);//104
              $this->pdf->Cell(0, 5, 'Rendement (cumulatief)', 0, 1);
              $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
              $this->pdf->SetXY(15,117+36)		;//112
              $valX = $this->pdf->GetX();
              $valY = $this->pdf->GetY();
              //function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
              $this->LineDiagram(270-50, 30, $grafiekData,$this->pdf->rapport_grafiek_pcolor,0,0,6,5,1);//50
              $this->pdf->SetXY($valX, $valY + 80);
            }
      */
		  $this->pdf->SetXY(8, 155);//165



		$this->pdf->ln(10);
		$this->pdf->SetX(108);


	   $this->pdf->fillCell = array();


		if($this->extraVulling)
		{
	   // verwijderTijdelijkeTabel($this->portefeuille,"$RapStartJaar-01-01");
		}
		$this->pdf->fillCell=array();
		$this->pdf->CellBorders = array();
		$this->pdf->SetDrawColor(0,0,0);
	}


	function toonSamenvatting ($totaalRegel)
	{

		$this->pdf->widthA = array(0, 85, 30, 5, 30, 120);
		$this->pdf->alignA = array('L', 'L', 'R', 'R', 'R');

		$this->pdf->widthB = array(0, 85, 30, 5, 30, 120);
		$this->pdf->alignB = array('L', 'L', 'R', 'R', 'R');

		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		$y=115;
$this->pdf->setY($y);
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
$this->pdf->Multicell(140,4,vertaalTekst('Ontwikkeling vermogen',$this->pdf->rapport_taal),'','C');
$this->pdf->setXY($this->pdf->marge,$y+8);
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
$this->pdf->ln();

$this->pdf->SetWidths($this->pdf->widthA);
$this->pdf->SetAligns($this->pdf->alignA);
$witruimte=4;
$this->pdf->SetFont($this->pdf->rapport_font, 'b' , $this->pdf->rapport_fontsize);

$this->pdf->row(array("", vertaalTekst("Resultaat verslagperiode", $this->pdf->rapport_taal), "", ""));
$this->pdf->ln($witruimte);
$this->pdf->excelData[]=array("", vertaalTekst("Resultaat verslagperiode", $this->pdf->rapport_taal), "", "");
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);


$this->pdf->row(array("", vertaalTekst("Vermogen per", $this->pdf->rapport_taal) . " " . date("j", db2jul($totaalRegel['vanaf'])) . " " . vertaalTekst($this->pdf->__appvar["Maanden"][date("n", db2jul($totaalRegel['vanaf']))], $this->pdf->taal) . " " . date("Y", db2jul($totaalRegel['vanaf'])), $this->formatGetal($totaalRegel['beginvermogen'], 0, true), ""));
$this->pdf->ln($witruimte);
$this->pdf->row(array("", vertaalTekst("Resultaat over verslagperiode", $this->pdf->rapport_taal), $this->formatGetal($totaalRegel['resultaat'], 0), ""));
$this->pdf->ln($witruimte);
$this->pdf->row(array("", vertaalTekst("Totaal stortingen gedurende verslagperiode", $this->pdf->rapport_taal), $this->formatGetal($totaalRegel['stortingen'] , 0), ""));
$this->pdf->ln($witruimte);
$this->pdf->row(array("", vertaalTekst("Totaal onttrekkingen gedurende verslagperiode", $this->pdf->rapport_taal), $this->formatGetal($totaalRegel['onttrekkingen'] *-1, 0), ""));
	//$this->pdf->Line($posSubtotaal + $extraLengte, $this->pdf->GetY(), $posSubtotaalEnd, $this->pdf->GetY());
$this->pdf->ln($witruimte);
$this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
$this->pdf->CellBorders=array('','','TS');
$this->pdf->row(array("", vertaalTekst("Vermogen per", $this->pdf->rapport_taal) . " " . date("j", db2jul($this->rapportageDatum)) . " " . vertaalTekst($this->pdf->__appvar["Maanden"][date("n", db2jul($this->rapportageDatum))], $this->pdf->taal) . " " . date("Y", db2jul($this->rapportageDatum)), $this->formatGetal($totaalRegel['eindvermogen'], 0), ""));
	//	$this->pdf->Line($posSubtotaal + $extraLengte, $this->pdf->GetY(), $posSubtotaalEnd, $this->pdf->GetY());
$this->pdf->CellBorders=array();

$this->pdf->ln($witruimte*2);
	//	$this->pdf->row(array("", vertaalTekst("Netto rendement over verslagperiode", $this->pdf->rapport_taal), $this->formatGetal($rendementProcent, 2). " %"));
	//		$this->pdf->excelData[]=array("", vertaalTekst("Netto rendement over verslagperiode", $this->pdf->rapport_taal), round($rendementProcent, 2), "%");
		/*
if ($this->pdf->rapport_PERF_jaarRendement)
{
$this->pdf->row(array("", vertaalTekst("Rendement lopende kalenderjaar", $this->pdf->rapport_taal), $this->formatGetal($rendementProcentJaar, 2). " %", ""));
$this->pdf->excelData[]=array("", vertaalTekst("Rendement lopende kalenderjaar", $this->pdf->rapport_taal), round($rendementProcentJaar, 2), "%", "");
} */

$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

$this->pdf->widthA = array(130, 70, 30, 5, 30, 120);
$this->pdf->alignA = array('L', 'L', 'R', 'R', 'R');

$this->pdf->widthB = array(130, 70, 30, 5, 30, 120);
$this->pdf->alignB = array('L', 'L', 'R', 'R', 'R');

$this->pdf->SetWidths($this->pdf->widthA);
$this->pdf->SetAligns($this->pdf->alignA);



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
	$datum = array();

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
	  if(db2jul($periode['stop'])>db2jul($periode['start']))
    {
      $indexData = array_merge($indexData, $this->BerekenMutaties2($periode['start'], $periode['stop'], $portefeuille));
      $indexData['datum'] = jul2sql(form2jul(substr($indexData['periodeForm'], -10, 10)));
      $indexData['index'] = ($indexData['index'] * (100 + $indexData['performance']) / 100);
      $data[$i] = $indexData;
      $i++;
    }
	}
	return $data;
	}

	function BerekenMutaties2($beginDatum,$eindDatum,$portefeuille,$valuta='EUR')
	{
	  if(substr($beginDatum,5,5)=='12-31')
	   $beginDatum=(substr($beginDatum,0,4)+1).'-01-01';

	  if ($valuta != "EUR" )
	    $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		$totaalWaarde =array();
		$db = new DB();

		$query="SELECT Portefeuilles.Startdatum FROM Portefeuilles WHERE Portefeuilles.Portefeuille='$portefeuille'";
		$db->SQL($query);
		$startDatum=$db->lookupRecord();


		/////

		if(count($this->pdf->portefeuilles)>1)
			$portefeuilles=$this->pdf->portefeuilles;
		else
			$portefeuilles[]=$portefeuille;

		$query="SELECT * FROM HistorischePortefeuilleIndex WHERE Portefeuille IN('".implode("','",$portefeuilles)."') AND Datum ='$eindDatum' AND categorie='totaal' AND periode='m'";
		$db->SQL($query);
		$db->Query();
		
		if($db->records()>0)//==count($portefeuilles))
		{
      $data=array();
			$data['valuta'] = $valuta;
			$data['periode'] = $beginDatum . "->" . $eindDatum;
			$data['periodeForm'] = date("d-m-Y", db2jul($beginDatum)) . " - " . date("d-m-Y", db2jul($eindDatum));
			while ($dbdata = $db->NextRecord())
			{
				$data['waardeBegin'] += round($dbdata['PortefeuilleBeginWaarde'], 2);
				$data['waardeHuidige'] += round($dbdata['PortefeuilleWaarde'], 2);
				$data['waardeMutatie'] += round($dbdata['PortefeuilleWaarde'] - $dbdata['PortefeuilleBeginWaarde'], 2);
				$data['stortingen'] += round($dbdata['Stortingen'], 2);
				$data['onttrekkingen'] += round($dbdata['Onttrekkingen'], 2);
				$data['resultaatVerslagperiode'] += round(round($dbdata['PortefeuilleWaarde'] - $dbdata['PortefeuilleBeginWaarde'], 2) - $dbdata['Stortingen'] + $dbdata['Onttrekkingen'], 2);
  			$data['gemiddelde'] += $dbdata['gemiddelde'];
	  		$data['kosten'] += round($dbdata['Kosten'], 2);
		  	$data['opbrengsten'] += round($dbdata['Opbrengsten'], 2);

			  $data['ongerealiseerd'] += round($dbdata['ongerealiseerd'], 2);
		  	$data['rente'] += round($dbdata['rente'], 2);
		  	$data['gerealiseerd'] += round($dbdata['gerealiseerd'], 2);
				$tmp=unserialize($dbdata['extra']);

				if(!is_array($data['extra']))
		    	$data['extra']=$tmp;
				else
				{
          foreach($tmp['cat'] as $cat=>$waarde)
						$data['extra']['cat'][$cat]+=$waarde;
  			}

		  }
			$data['performance'] = $data['resultaatVerslagperiode'] / $data['gemiddelde']*100;

			return $data;

		}




/////
		/*
        $query="SELECT
    BeleggingscategoriePerFonds.Vermogensbeheerder,
    Portefeuilles.Portefeuille,
    CategorienPerHoofdcategorie.Hoofdcategorie,
    Beleggingscategorien.Omschrijving,
    Beleggingscategorien.Afdrukvolgorde
    FROM
    BeleggingscategoriePerFonds
    INNER JOIN Portefeuilles ON BeleggingscategoriePerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
    INNER JOIN CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND BeleggingscategoriePerFonds.Vermogensbeheerder = CategorienPerHoofdcategorie.Vermogensbeheerder
    INNER JOIN Beleggingscategorien ON Beleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie
    WHERE Portefeuilles.Portefeuille='$portefeuille'
    GROUP BY CategorienPerHoofdcategorie.Hoofdcategorie
    ORDER BY Beleggingscategorien.Afdrukvolgorde";
          $db->SQL($query);
          $db->Query();
         $this->categorieVolgorde['LIQ']=0;
          while($data=$db->nextRecord())
              $this->categorieVolgorde[$data['Hoofdcategorie']]=0;
    */
    if(db2jul($beginDatum) <= db2jul($startDatum['Startdatum']))
    {
       if($this->voorStartdatumNegeren==true && db2jul($eindDatum) <= db2jul($startDatum['Startdatum']))
       return array('periode'=>$beginDatum."->".$eindDatum,'periodeForm'=>date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum)));

      $wegingsDatum=date('Y-m-d',db2jul($startDatum['Startdatum'])+86400); //$startDatum['Startdatum'];
    }
    else
      $wegingsDatum=$beginDatum;

		$startjaar=substr($beginDatum,0,4);
		if(db2jul($beginDatum) == mktime (0,0,0,1,1,$startjaar))
		 $beginjaar = true;
		else
		 $beginjaar = false;

		$koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,$valuta,true);
		//echo "att $koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,'EUR',true);<br>\n";

		$fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($portefeuille,$beginDatum,$beginjaar,$valuta,$beginDatum);
		$DB=new DB();

		if($valuta <> 'EUR')
	  	$valutaKoers=getValutaKoers($valuta,$beginDatum);
		else
		  $valutaKoers=1;
	  foreach ($fondswaarden['beginmaand'] as $regel)
	  {
			$regel['actuelePortefeuilleWaardeEuro'] = $regel['actuelePortefeuilleWaardeEuro'] / $valutaKoers;
			$totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
			$query="SELECT Fondsen.Portefeuille FROM Fondsen WHERE Fondsen.Fonds='".$regel['fonds']."' AND Fondsen.Huisfonds=1 AND Fondsen.Portefeuille<>''";
			if($this->pdf->lastPOST['doorkijk']==1 && $DB->QRecords($query)!=1)
			{

			}
			else
			{
				if ($regel['type'] == 'rente' && $regel['fonds'] != '')
				{
					$totaalWaarde['renteBegin'] += $regel['actuelePortefeuilleWaardeEuro'];
				}
			}
	  }

	  $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$eindDatum,false,$valuta,$beginDatum);
    $categorieVerdeling=$this->categorieVolgorde;

   // listarray($categorieVerdeling);
   	if($valuta <> 'EUR')
	  	$valutaKoers=getValutaKoers($valuta,$eindDatum);
		else
		  $valutaKoers=1;

		$huisfondsOpbrengst=0;
		$huisfondsKosten=0;
		$huisfondsStortingen=0;
		$huisfondsOnttrekkingen=0;
		$huisfondsStortingenEnOnttrekkingenGewogen=0;
	  foreach ($fondswaarden['eindmaand'] as $regel)
	  {
	    $regel['actuelePortefeuilleWaardeEuro']=$regel['actuelePortefeuilleWaardeEuro']/$valutaKoers;
      $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];

			if($regel['beleggingscategorie']=='')
				$regel['beleggingscategorie']='Leeg';
      if($regel['type']=='fondsen')
      {
				$query="SELECT Fondsen.Portefeuille FROM Fondsen WHERE Fondsen.Fonds='".$regel['fonds']."' AND Fondsen.Huisfonds=1 AND Fondsen.Portefeuille<>''";
        if($this->pdf->lastPOST['doorkijk']==1 && $DB->QRecords($query)==1)
				{
					$huisfonds=$DB->nextRecord();
					$waarden=bepaalHuisfondsResultaat($regel['fonds'],$portefeuille,$huisfonds['Portefeuille'],$beginDatum,$eindDatum);
					$totaalWaarde['eindResultaat']+=$waarden['aandeelKoersresultaat'];
					$huisfondsOpbrengst+=$waarden['aandeelDirecteopbrengst'];
					$huisfondsKosten+=$waarden['aandeeldirectekosten'];
					$huisfondsStortingen+=$waarden['aandeelStortingen'];
					$huisfondsOnttrekkingen+=$waarden['aandeelOnttrekkingen'];
					$huisfondsStortingenEnOnttrekkingenGewogen+=$waarden['aandeelStortingenEnOnttrekkingenGewogen'];

					foreach($waarden['verdeling']['beleggingscategorie'] as $beleggingscategorie=>$waardeEur)
					  $categorieVerdeling[$beleggingscategorie] += $waardeEur;
				}
				else
				{
					$totaalWaarde['beginResultaat'] += $regel['beginPortefeuilleWaardeEuro'];
					$totaalWaarde['eindResultaat'] += $regel['actuelePortefeuilleWaardeEuro'];
					$categorieVerdeling[$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
				}

      }
      elseif($regel['type']=='rente' && $regel['fonds'] != '')
      {
        $totaalWaarde['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling['Liquiditeiten'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rekening')
      {
        $categorieVerdeling['Liquiditeiten'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
	  }
	  $ongerealiseerd=($totaalWaarde['eindResultaat']-$totaalWaarde['beginResultaat']);
//echo  "$ongerealiseerd=(".$totaalWaarde['eindResultaat']."-".$totaalWaarde['beginResultaat'].") <br>\n";exit;

	$query = "SELECT ".
	"SUM(((TO_DAYS('".$eindDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
	"  / (TO_DAYS('".$eindDatum."') - TO_DAYS('".$wegingsDatum."')) ".
	"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
	"FROM  (Rekeningen, Portefeuilles,Grootboekrekeningen )
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	"WHERE ".
	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Boekdatum > '".$beginDatum."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$eindDatum."' AND
	Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
	$DB->SQL($query);
	$DB->Query();
	$weging = $DB->NextRecord();

  $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1']+$huisfondsStortingenEnOnttrekkingenGewogen;
	$stortingen = getStortingen($portefeuille,$beginDatum, $eindDatum,$valuta)+$huisfondsStortingen;
	$onttrekkingen = getOnttrekkingen($portefeuille,$beginDatum, $eindDatum,$valuta)+$huisfondsOnttrekkingen;

	$performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - ($stortingen-$onttrekkingen)) / $gemiddelde) * 100;
//echo "<br>\n $query <br>\n";
//echo "perf $eindDatum  $wegingsDatum $performance = (((".$totaalWaarde['eind']." - ".$totaalWaarde['begin'].") - ".$weging['totaal2'].") / $gemiddelde) * 100;<br>\n";
	  $waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;

		$query = "SELECT SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery)  AS totaalkosten
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

    $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaalOpbrengsten
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
		$opbrengsten['totaalOpbrengsten']+=$huisfondsOpbrengst;
		$kosten['totaalkosten']+=$huisfondsKosten;
    $opgelopenRente=$totaalWaarde['renteEind']-$totaalWaarde['renteBegin'];
    $valutaResultaat=$resultaatVerslagperiode-($koersResultaat+$ongerealiseerd+$opbrengsten['totaalOpbrengsten']+$kosten['totaalkosten']+$opgelopenRente);
    $ongerealiseerd+=$valutaResultaat;

    foreach ($categorieVerdeling as $cat=>$waarde)
      $categorieVerdeling[$cat]=$waarde."";

    $data['valuta']=$valuta;
    $data['periode']= $beginDatum."->".$eindDatum;
    $data['periodeForm']= date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum));
    $data['waardeBegin']=round($totaalWaarde['begin'],2);
    $data['waardeHuidige']=round($totaalWaarde['eind'],2);
    $data['waardeMutatie']=round($waardeMutatie,2);
    $data['stortingen']=round($stortingen,2);
    $data['onttrekkingen']=round($onttrekkingen,2);
    $data['resultaatVerslagperiode'] = round($resultaatVerslagperiode,2);
    $data['gemiddelde'] = $gemiddelde;
    $data['kosten'] = round($kosten['totaalkosten'],2);
    $data['opbrengsten'] = round($opbrengsten['totaalOpbrengsten'],2);
    $data['performance'] =$performance;
    $data['ongerealiseerd'] =$ongerealiseerd;
    $data['rente'] = $opgelopenRente;
    $data['gerealiseerd'] =$koersResultaat;
    $data['extra']['cat']=$categorieVerdeling;//listarray($data);
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
    $XDiag = $XPage ;
    $lDiag = floor($w);

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(23,55,94);
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
    $unit = $lDiag / count($data)+1;

    if($jaar)
      $unit = $lDiag / 13;

		//echo "line : $lDiag -> $unit  <br>\n";exit;

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
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(150,150,150)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
       break;
    }

    $n=0;
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(150,150,150)));
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
			//	$xStart+=$unit;
				$xStart = $XDiag + ($i) * $unit;
			$xEind  = $xStart+$unit;

    //  $this->pdf->Text($XDiag+($i)*$unit-10+$unit,$YDiag+$hDiag+8,date("d-m-Y",db2jul($legendDatum[$i])),25);

			$this->pdf->SetXY($xStart+0.5*$unit,$YDiag+$hDiag+1);
			$this->pdf->Cell($unit,4,date("d-m-Y",db2jul($legendDatum[$i])),0,0,'C');//

      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($xStart, $yval,$xEind, $yval2,$lineStyle );
      if ($i>0)
        $this->pdf->Rect($xStart-0.5, $yval-0.5, 1, 1 ,'F','',$color);
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
        $this->pdf->line($xStart, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        if ($i>0)
          $this->pdf->Rect($xStart-0.5, $yval-0.5, 1, 1 ,'F','',$color1);
         $yval = $yval2;
      }
    }
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }


	function VBarDiagramVermogen($x,$y,$w, $h, $data,$title='')
  {
	global $__appvar;

	$legendaWidth = 0;
	$this->pdf->setXY($x,$y-$h);
	$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
	$this->pdf->Multicell($w,4, vertaalTekst($title,$this->pdf->rapport_taal),'','C');
	$this->pdf->setXY($x,$y+12);
	$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);



	$grafiekPunt = array();
	$verwijder=array();
	$this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0,'width'=>0.01));
	$maxVal=100;
	foreach ($data as $datum=>$waarden)
	{
		$legenda[$datum] = $datum;
		$n=0;
		foreach ($waarden as $categorie=>$waarde)
		{
			$grafiek[$datum][$categorie]=$waarde;
			$grafiekCategorie[$categorie][$datum]=$waarde;
			$categorien[$categorie] = $n;
			$categorieId[$n]=$categorie ;

			if($waarde < 0)
			{
				$verwijder[$datum]=$datum;
				$grafiek[$datum][$categorie]=0;
				$grafiekCategorie[$categorie][$datum]=0;
			}
			$maxVal=max($maxVal,$waarde);

			if(!isset($colors[$categorie]))
			{
				if($this->attKleuren[$categorie])
					$colors[$categorie]=array($this->attKleuren[$categorie]['R']['value'],$this->attKleuren[$categorie]['G']['value'],$this->attKleuren[$categorie]['B']['value']);
				else
					$colors[$categorie]=array(rand(20,80),rand(20,80),rand(20,250));//array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
			}
			$n++;
		}
	}
	$colors['Totaal']=array(0,72,107);
	foreach ($verwijder as $datum)
	{
		foreach ($data[$datum] as $categorie=>$waarde)
		{
			$grafiekCategorie[$categorie][$datum]=0;
		}
	}

	$numBars = count($grafiek);
	//$numBars=12;

	if($color == null)
	{
		$color=array(155,155,155);
	}


	// foreach ($this->jaarWaarden['Totaal'] as $jaar=>$waarden)
	//    $maxVal=max($maxVal,$waarden['waarde']);

	$maxVal=round(ceil($maxVal/5000))*5000;

	// listarray();



	$minVal = 0;

	$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
	$XPage = $this->pdf->GetX();
	$YPage = $this->pdf->GetY();
	$margin = 2;
	$YstartGrafiek = $YPage - floor($margin * 1);
	$hGrafiek = ($h - $margin * 1);
	$XstartGrafiek = $XPage + $margin * 1 ;
	$bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

	$unit = $hGrafiek / $maxVal * -1;
	$nulYpos =0;

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

	$n=0;

	//	$this->pdf->TextWithRotation($XPage-14,$YPage-$h/2+5,'Vermogen in EUR',90);
	for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
	{
		$this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek+$bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
		$this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
		$this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte)."",0,0,'R');
		$n++;
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
//listarray($grafiek);
	foreach ($grafiek as $datum=>$data)
	{
		$data=array_reverse($data,true);
		foreach($data as $categorie=>$val)
		{
			if(!isset($YstartGrafiekLast[$datum]))
				$YstartGrafiekLast[$datum] = $YstartGrafiek;
			//Bar
			$xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
			$lval = $eBaton;
			$yval = $YstartGrafiekLast[$datum] + $nulYpos ;
			$hval = ($val * $unit );//* $this->jaarWaarden['Totaal'][$datum]['waarde']/100

			$this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
			$YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;

			if($legendaPrinted[$datum] != 1)
				$this->pdf->TextWithRotation($xval+3,$YstartGrafiek+4,$legenda[$datum],0);

			if($grafiekPunt[$categorie][$datum])
			{
				$this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
				if($lastX)
					$this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
				$lastX = $xval+.5*$eBaton;
				$lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
			}
			$legendaPrinted[$datum] = 1;
		}
		$i++;
	}
	$xval=$x+10;
	$yval=$y+15;
	$colors=array_reverse($colors,true);
	foreach ($colors as $cat=>$color)
	{
		$this->pdf->Rect($xval, $yval, 3, 3, 'DF',null,$colors[$cat]);
		$this->pdf->TextWithRotation($xval+7,$yval+2.5,vertaalTekst($cat ,$this->pdf->rapport_taal),0);
		$xval=$xval+22;
	}

	$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
}
?>
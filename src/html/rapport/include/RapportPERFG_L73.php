<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/11/03 18:45:31 $
File Versie					: $Revision: 1.10 $

$Log: RapportPERFG_L73.php,v $
Revision 1.10  2018/11/03 18:45:31  rvv
*** empty log message ***

Revision 1.9  2018/10/06 17:20:57  rvv
*** empty log message ***

Revision 1.8  2018/06/13 15:54:31  rvv
*** empty log message ***

Revision 1.7  2018/04/07 15:21:44  rvv
*** empty log message ***

Revision 1.6  2017/10/07 16:54:34  rvv
*** empty log message ***

Revision 1.5  2017/09/30 16:31:15  rvv
*** empty log message ***

Revision 1.4  2017/08/09 16:10:49  rvv
*** empty log message ***

Revision 1.3  2017/06/21 16:10:57  rvv
*** empty log message ***

Revision 1.2  2017/05/24 08:47:34  rvv
*** empty log message ***

Revision 1.1  2017/05/14 09:57:45  rvv
*** empty log message ***

Revision 1.12  2017/04/29 17:26:01  rvv
*** empty log message ***

Revision 1.11  2017/03/22 16:53:22  rvv
*** empty log message ***

Revision 1.10  2017/03/15 16:36:11  rvv
*** empty log message ***

Revision 1.9  2017/02/25 18:02:29  rvv
*** empty log message ***

Revision 1.8  2017/02/19 10:59:55  rvv
*** empty log message ***

Revision 1.7  2017/02/19 09:21:57  rvv
*** empty log message ***

Revision 1.6  2017/01/15 08:01:57  rvv
*** empty log message ***

Revision 1.5  2017/01/04 16:22:50  rvv
*** empty log message ***

Revision 1.4  2016/12/17 16:33:26  rvv
*** empty log message ***

Revision 1.3  2016/11/19 19:03:08  rvv
*** empty log message ***

Revision 1.2  2016/11/16 16:51:17  rvv
*** empty log message ***

Revision 1.1  2016/11/12 20:21:18  rvv
*** empty log message ***

Revision 1.1  2016/11/05 17:51:44  rvv
*** empty log message ***

Revision 1.7  2016/10/02 12:38:58  rvv
*** empty log message ***

Revision 1.6  2016/06/15 15:58:41  rvv
*** empty log message ***

Revision 1.5  2016/06/12 10:27:20  rvv
*** empty log message ***

Revision 1.4  2016/05/29 13:26:30  rvv
*** empty log message ***

Revision 1.3  2016/05/21 19:00:02  rvv
*** empty log message ***

Revision 1.2  2016/05/15 17:15:00  rvv
*** empty log message ***

Revision 1.1  2016/05/04 16:08:25  rvv
*** empty log message ***

Revision 1.3  2013/06/26 15:55:41  rvv
*** empty log message ***

Revision 1.2  2013/06/12 18:46:36  rvv
*** empty log message ***

Revision 1.1  2013/05/26 13:54:49  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");


class RapportPERFG_L73
{
	function RapportPERFG_L73($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Langjarig rendement en allocatie";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	 // $this->tweedeStart();


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
    $this->categorieKleuren['G-LIQ']= $this->categorieKleuren['Liquiditeiten'];


	 // $this->categorieOmschrijving=array('LIQ'=>'Liquiditeiten','ZAK'=>'Zakelijke waarden','VAR'=>'Vastrentende waarden','Liquiditeiten'=>'Liquiditeiten');

		$q="SELECT waarde as Beleggingscategorie,Omschrijving,KeuzePerVermogensbeheerder.Afdrukvolgorde FROM KeuzePerVermogensbeheerder 
    JOIN Beleggingscategorien ON KeuzePerVermogensbeheerder.waarde=Beleggingscategorien.Beleggingscategorie 
    WHERE categorie='Beleggingscategorien' AND Vermogensbeheerder = '".$beheerder."' ORDER BY KeuzePerVermogensbeheerder.Afdrukvolgorde desc";
    /*
		$DB->SQL($q); 
		$DB->Query();
		while($data=$DB->nextRecord())
		{
		  
		  $this->categorieVolgorde[$data['Beleggingscategorie']]=0;
		  $this->categorieOmschrijving[$data['Beleggingscategorie']]=$data['Omschrijving'];
		}
    */
   // $this->categorieVolgorde['LIQ']='Liquiditeiten';
		$q="SELECT hoofdcategorie,hoofdcategorieOmschrijving as Omschrijving,hoofdcategorieVolgorde FROM TijdelijkeRapportage WHERE Portefeuille='".$this->portefeuille."' AND hoofdcategorie <>'' GROUP BY hoofdcategorie  ORDER BY hoofdcategorieVolgorde asc"; //WHERE Beleggingscategorie IN('LIQ','ZAK','VAR','Liquiditeiten')
		$DB->SQL($q);
		$DB->Query();

		while($data=$DB->nextRecord())
		{
		  $this->categorieVolgorde[$data['hoofdcategorie']]=0;//$data['hoofdcategorie'];
		  $this->categorieOmschrijving[$data['hoofdcategorie']]=vertaalTekst($data['Omschrijving'],$this->pdf->rapport_taal);
		}
		if(!isset($this->categorieVolgorde['VAR']))
    {
      $this->categorieVolgorde['VAR'] = 0;
      $this->categorieOmschrijving['VAR'] = vertaalTekst('Vastrentende waarden', $this->pdf->rapport_taal);
    }
    if(!isset($this->categorieVolgorde['G-LIQ']))
    {
      $this->categorieVolgorde['G-LIQ'] = 0;
      $this->categorieOmschrijving['G-LIQ'] = vertaalTekst('Liquiditeiten', $this->pdf->rapport_taal);
    }



//listarray($this->categorieVolgorde);exit;
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
    
    $query="SELECT Portefeuilles.Startdatum FROM Portefeuilles WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";
    $DB->SQL($query);
    $DB->Query();
    $startdatum=$DB->nextRecord();
    //$this->pdf->PortefeuilleStartdatum
    
		//if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
		  $indexDatum=substr($startdatum['Startdatum'],0,10);
	//	else
	//	  $indexDatum=$this->rapportageDatumVanaf;

  $index=new indexHerberekening();
  $maanden=$index->getMaanden(db2jul($indexDatum),db2jul($this->rapportageDatum));
  $indexData=array();
	$barGraph=array();
  foreach ($maanden as $periode)
  {

		$nieuweMaandData = $this->BerekenMutaties2($periode['start'], $periode['stop'], $this->portefeuille);

		$totaalWaade=0;
		foreach ($nieuweMaandData['extra']['cat'] as $categorie=>$waarde)
		{
			if($categorie=='G-LIQ')
				$categorie='VAR';
			$barGraph['Index'][$periode['stop']][$categorie] += $waarde/$nieuweMaandData['waardeHuidige']*100;
			$totaalWaade+=$waarde;
		}
		if($totaalWaade==0)
       $barGraph['Index'][$periode['stop']]=array();

		$tmp=array('datum'=>$periode['stop'],'index'=>100,'waardeHuidige'=>0,'specifiekeIndex'=>100,'extra'=>array('cat'=>array('VAR'=>0,'ZAK'=>0)));
		foreach($nieuweMaandData as $index=>$waarden)
			$tmp[$index]=$waarden;

		$indexData[]=$tmp;
  }


		/*
  $indexDataReal = $this->getWaarden($indexDatum ,$this->rapportageDatum ,$this->portefeuille);



  foreach ($indexData as $index=>$maanden)
  {
    foreach ($indexDataReal as $realData)
    {
      if($realData['datum'] == $maanden['datum'])
        $indexData[$index]=$realData;
    }
  }
		*/

		//rvv

		$somVelden=array('stortingen','onttrekkingen','gerealiseerd','ongerealiseerd','opbrengsten','kosten','rente','resultaatVerslagperiode');
		$periodeWaarden=array();
		$rapportageJaar=substr($this->rapportageDatum,0,4);
		$rapportageMaand=substr($this->rapportageDatum,5,2);
		$rapportageKwartaal=ceil($rapportageMaand/3);
    $laatsteIndex=0;
		foreach ($indexData as $periode=>$data)
		{
        $index = ((1 + ($laatsteIndex / 100)) * (1 + ($data['performance'] / 100)) - 1) * 100;
        $data['index'] = $index;
        $laatsteIndex=$index;// ;

      $grafiekData['Datum'][] = $data['datum'];
      $grafiekData['Index'][] = $data['index'];
/*
      foreach ($data['extra']['cat'] as $categorie=>$waarde)
      {
        if($categorie=='G-LIQ')
          $categorie='VAR';
        $barGraph['Index'][$data['datum']][$categorie] += $waarde/$data['waardeHuidige']*100;
      }
*/
			unset($data['extra']);
			if($data['datum'] != '0000-00-00')
			{
				$jaar=substr($data['datum'],0,4);
				$maand=substr($data['datum'],5,2);
				$kwartaal=ceil($maand/3);
				$jaarKwartaal=$jaar.$kwartaal;

				if(1)//$jaar < $rapportageJaar)
				{
          $data['periode']=$jaar;
					if(!isset($periodeWaarden['jaar'][$jaar]))
						$periodeWaarden['jaar'][$jaar] = $data;
					else
					{
						foreach($somVelden as $veld)
							$periodeWaarden['jaar'][$jaar][$veld] += $data[$veld];

						if(!isset($periodeWaarden['jaar'][$jaar]['waardeBegin']))
							$periodeWaarden['jaar'][$jaar]['waardeBegin']=$data['waardeBegin'];

						$periodeWaarden['jaar'][$jaar]['waardeHuidige'] = $data['waardeHuidige'];
						$periodeWaarden['jaar'][$jaar]['index'] = $data['index'];
						$periodeWaarden['jaar'][$jaar]['datum'] = $data['datum'];
						$periodeWaarden['jaar'][$jaar]['performance'] = (( (1+$periodeWaarden['jaar'][$jaar]['performance']/100)  * (1+$data['performance']/100))-1)*100;
					}
				}
				if(1)
				{
          $data['periode']=$jaar.'Q'.$kwartaal;
					$periodeWaarden['kwartaal'][$jaarKwartaal] = $data;
				}
			}
		}
//listarray($barGraph);
		///rvv



$grafiekData['Datum'][]="$RapStartJaar-12-01";
$firstMonth=true;

  // if(count($periodeWaarden['kwartaal']) <10 )
  //   $verdeling='kwartaal';
  // else
     $verdeling='jaar';

   if(count($periodeWaarden[$verdeling]) > 0)
   {
        $n=1;
        $this->pdf->fillCell = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
     //   $this->pdf->CellBorders = array('','US','US','US','US','US','US','US','US','US','US','US');
        $this->pdf->underlinePercentage=0.8;

       // $this->pdf->SetFillColor(221,224,229);
		    $this->pdf->SetFillColor($this->pdf->rapport_regelAchtergrond[0],$this->pdf->rapport_regelAchtergrond[1],$this->pdf->rapport_regelAchtergrond[2]);
        $totaalRendament=100;
		    foreach ($periodeWaarden[$verdeling] as $row)
		    {
          $jaar=substr($row['datum'],0,4);
          $datumTxt=$row['periode'];

          if($verdeling=='kwartaal')
          {
            if(isset($laatsteJaar) && $laatsteJaar<>$jaar)
            {
              $this->pdf->fillCell=array();
               $subData=$periodeWaarden['jaar'][$laatsteJaar];
              $this->pdf->ln(3);
              $this->pdf->CellBorders = array('','TS','TS','TS','TS','TS','TS','TS','TS','TS');
              $this->pdf->row(array($laatsteJaar,
                                $this->formatGetal($subData['waardeBegin'],0),
                                $this->formatGetal($subData['stortingen'],0),
																$this->formatGetal($subData['onttrekkingen'],0),
                                $this->formatGetal($subData['gerealiseerd']+$subData['ongerealiseerd']+$subData['rente'],0),
                                $this->formatGetal($subData['opbrengsten'],0),
                                $this->formatGetal($subData['kosten'],0),
                                $this->formatGetal($subData['resultaatVerslagperiode'],0),
                                $this->formatGetal($subData['waardeHuidige'],0),
                                $this->formatGetal($subData['performance'],2).'%',
                                $this->formatGetal($subData['index'],2).'%'));
              $this->pdf->ln(3);
              $this->pdf->CellBorders = array();
            }
          }

          if($n%2==0)
            $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
          else
            $this->pdf->fillCell=array();

		       $this->pdf->row(array($datumTxt ,
		                           $this->formatGetal($row['waardeBegin'],0),
		                           $this->formatGetal($row['stortingen'],0),
														   $this->formatGetal($row['onttrekkingen'],0),
                               $this->formatGetal($row['gerealiseerd']+$row['ongerealiseerd']+$row['rente'],0),
		                           $this->formatGetal($row['opbrengsten'],0),
		                           $this->formatGetal($row['kosten'],0),
		                           $this->formatGetal($row['resultaatVerslagperiode'],0),
		                           $this->formatGetal($row['waardeHuidige'],0),
		                           $this->formatGetal($row['performance'],2).'%',
		                           $this->formatGetal($row['index'],2).'%'));
                               
		                           if(!isset($totaal['waardeBegin']))
                                 $totaal['waardeBegin']=$row['waardeBegin'];
		                           $totaal['waarde'] = $row['waardeHuidige'];
		                           $totaal['resultaat'] += $row['resultaatVerslagperiode'];
		                           $totaal['gerealiseerd'] += $row['gerealiseerd'];
		                           $totaal['ongerealiseerd'] += $row['ongerealiseerd'];
		                           $totaal['opbrengsten'] += $row['opbrengsten'];
		                           $totaal['kosten'] += $row['kosten'];
		                           $totaal['rente'] += $row['rente'];
		                           $totaal['stortingen'] += $row['stortingen'];
                               $totaal['ontrekkingen'] += $row['onttrekkingen'];
		                           $totaal['rendament'] = $row['index'];

        $laatsteJaar=$jaar;
		    $n++;
		    }
		    $this->pdf->fillCell=array();


        $this->pdf->ln(3);
        $this->pdf->CellBorders = array('','UU','UU','UU','UU','UU','UU','UU','UU','','UU');
		    $this->pdf->row(array(vertaalTekst('Samenvatting',$this->pdf->rapport_taal),
		                           $this->formatGetal($totaal['waardeBegin'],0),
		                           $this->formatGetal($totaal['stortingen'],0),
												       $this->formatGetal($totaal['ontrekkingen'],0),
		                           $this->formatGetal($totaal['gerealiseerd']+$totaal['ongerealiseerd']+$totaal['rente'] ,0),
		                           $this->formatGetal($totaal['opbrengsten'],0),
		                           $this->formatGetal($totaal['kosten'],0),
		                           $this->formatGetal($totaal['resultaat'],0),
		                           $this->formatGetal( $totaal['waarde'],0),
		                           '',
		                           $this->formatGetal($totaal['rendament'] ,2).'%'
		                           ));//$this->formatGetal($totaalRendamentIndex-100,2)
		                           	    $this->pdf->CellBorders = array();

		  }

		  if (count($barGraph) > 0)
		  {
		    $this->pdf->SetXY($this->pdf->marge,102)		;//112
		    	$this->pdf->Cell(0, 5, vertaalTekst('Vermogensverdeling',$this->pdf->rapport_taal), 0, 1);
  		    $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
		      $this->pdf->SetXY(15,140)		;//112
		      $this->VBarDiagram(270, 30, $barGraph['Index'],$barGraph['Plan']);
		  }

		  if (count($grafiekData) > 1)
		  {
        $this->pdf->SetXY(8,109+40);//104
  		  $this->pdf->Cell(0, 5, vertaalTekst('Rendement (cumulatief)',$this->pdf->rapport_taal), 0, 1);
  		  $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
  		  $this->pdf->SetXY(15,117+39)		;//112
        $valX = $this->pdf->GetX();
        $valY = $this->pdf->GetY();
        //function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
        $this->LineDiagram(270-50, 30, $grafiekData,$this->pdf->rapport_grafiek_pcolor,0,0,6,5,false);//50
        $this->pdf->SetXY($valX, $valY + 80);
		  }
		  $this->pdf->SetXY(8, 155);//165



		$this->pdf->ln(10);
		$this->pdf->SetX(108);


	  $this->pdf->MultiCell(170,4,$titel,0,'L');
	  $this->pdf->SetX(108);


	   $this->pdf->fillCell = array();

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

/*
	function getWaarden($datumBegin,$datumEind,$portefeuille)
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
		    $datum[$i]['start']=date("Y-m-d",mktime (0,0,0,$beginmaand+$i,0,$startjaar));
	    }
			$stopJul=mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar);
	    $datum[$i]['stop']=date("Y-m-d",$stopJul);
			$datum[$i]['kwartaal']=ceil(date('n',$stopJul)/3);
			if($datum[$i]['start']==$datum[$i]['stop'])
				unset($datum[$i]);
			$i++;
		}
	}
	if($i==0)
    $datum[$i]['start']=$datumBegin;
	else
	  $datum[$i]['start']=date("Y-m-d",mktime (0,0,0,$beginmaand+$i,0,$startjaar));
	$datum[$i]['stop']=$datumEind;
	$datum[$i]['kwartaal']=ceil(substr($datumEind,5,2)/3);


	$indexData['index']=100;
	$db=new DB();

	$kwartaalLookup=array('1'=>'-03-31','2'=>'-06-30','3'=>'-09-30','4'=>'-12-31');
  $somVelden=array('stortingen','onttrekkingen','gerealiseerd','ongerealiseerd','opbrengsten','kosten','rente','resultaatVerslagperiode');
    $unsetVelden=array('valuta','periodeForm','periode');//,'extra'
    $data=array();
    $laatsteIndex=0;
	foreach ($datum as $periode)
	{

	//	echo "$portefeuille ".$periode['stop']." ".$periode['kwartaal']."<br>\n";
		$jaar=substr($periode['stop'],0,4);
    $periodeTag=$jaar . '-' . $periode['kwartaal'];
		$query="SELECT periode,PortefeuilleBeginWaarde as waardeBegin,stortingen,onttrekkingen,opbrengsten,kosten,PortefeuilleWaarde as waardeHuidige ,IndexWaarde as performance,Periode,datum 
    FROM HistorischePortefeuilleIndex 
    WHERE  Portefeuille='$portefeuille' AND 
      ((periode='k' AND Datum='".$jaar.$kwartaalLookup[$periode['kwartaal']]."') OR (periode='m' AND Datum='".$periode['stop']."'))
    ORDER BY periode";
		$db->SQL($query);
		$record=$db->lookupRecord();

		if($record['periode']=='k')
		{
			$data[$periodeTag] = $record;
		}
		else
		{
			if($record['periode']=='m')
				$nieuweMaandData=$record;
			else
      {
        $nieuweMaandData = $this->BerekenMutaties2($periode['start'], $periode['stop'], $portefeuille);
        $nieuweMaandData['datum'] = substr($nieuweMaandData['periode'], -10, 10);
        foreach($unsetVelden as $veld)
          unset($nieuweMaandData[$veld]);
      }
      $vorigePeriode=$data[$periodeTag] ;
      if(isset($vorigePeriode['waardeBegin']))
        $nieuweMaandData['waardeBegin']=$vorigePeriode['waardeBegin'];
      foreach($somVelden as $veld)
      {
        $nieuweMaandData[$veld]+= $vorigePeriode[$veld];
      }
      $nieuweMaandData['performance']=((1+($vorigePeriode['performance']/100))*(1+($nieuweMaandData['performance']/100))-1)*100;
			$data[$periodeTag] = $nieuweMaandData;
   	}
	}
	return $data;
	}
*/

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

		if($valuta <> 'EUR')
	  	$valutaKoers=getValutaKoers($valuta,$beginDatum);
		else
		  $valutaKoers=1;
	  foreach ($fondswaarden['beginmaand'] as $regel)
	  {
	    $regel['actuelePortefeuilleWaardeEuro']=$regel['actuelePortefeuilleWaardeEuro']/$valutaKoers;
      $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
      if($regel['type']=='rente' && $regel['fonds'] != '')
        $totaalWaarde['renteBegin'] += $regel['actuelePortefeuilleWaardeEuro'];
	  }

	  $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$eindDatum,false,$valuta,$beginDatum);
    $categorieVerdeling=$this->categorieVolgorde;

   // listarray($categorieVerdeling);
   	if($valuta <> 'EUR')
	  	$valutaKoers=getValutaKoers($valuta,$eindDatum);
		else
		  $valutaKoers=1;

	  foreach ($fondswaarden['eindmaand'] as $regel)
	  {
	    $regel['actuelePortefeuilleWaardeEuro']=$regel['actuelePortefeuilleWaardeEuro']/$valutaKoers;
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
        $categorieVerdeling['VAR'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rekening')
      {
        $categorieVerdeling['G-LIQ'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
	  }


	  $ongerealiseerd=($totaalWaarde['eindResultaat']-$totaalWaarde['beginResultaat']);
	  $DB=new DB();

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

  $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
	$performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging['totaal2']) / $gemiddelde) * 100;
//echo "<br>\n $query <br>\n";
//echo "perf $eindDatum  $wegingsDatum $performance = (((".$totaalWaarde['eind']." - ".$totaalWaarde['begin'].") - ".$weging['totaal2'].") / $gemiddelde) * 100;<br>\n";
	  $waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
		$stortingen = getStortingen($portefeuille,$beginDatum, $eindDatum,$valuta);
		$onttrekkingen = getOnttrekkingen($portefeuille,$beginDatum, $eindDatum,$valuta);
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
    $data['extra']['cat']=$categorieVerdeling;
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
    $unit = $lDiag / (count($data)+1);

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
		$aantalWaarden=count($data);
		if($aantalWaarden>36)
		  $kwartalen=true;
		else
		  $kwartalen=false;
    for ($i=0; $i<$aantalWaarden; $i++)
    {
			//	$xStart+=$unit;
				$xStart = $XDiag + ($i) * $unit;
			$xEind  = $xStart+$unit;

    //  $this->pdf->Text($XDiag+($i)*$unit-10+$unit,$YDiag+$hDiag+8,date("d-m-Y",db2jul($legendDatum[$i])),25);

			$this->pdf->SetXY($xStart+0.5*$unit,$YDiag+$hDiag+1);
			//$this->pdf->Cell($unit,4,date("d-m-Y",db2jul($legendDatum[$i])),0,0,'C');//
      $datumJul=db2jul($legendDatum[$i]);
      if($kwartalen==false || in_array(date('n',$datumJul),array(3,6,9,12)))
        $legendaTonen=true;
      else
        $legendaTonen=false;
      if($legendaTonen)
        $this->pdf->TextWithRotation($xStart+0.5*$unit,$YDiag+$hDiag+8,date("d-m-Y",db2jul($legendDatum[$i])),30);

			$yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
			$this->pdf->line($xStart, $yval,$xEind, $yval2,$lineStyle );

			$this->pdf->Rect($xEind - 0.5, $yval2 - 0.5, 1, 1, 'F', '', $color);
			if($aantalWaarden<24)
			{
				$this->pdf->SetXY($xEind - 5, $yval2 - 4);
				$this->pdf->Cell(10, 4, $this->formatGetal($data[$i], 1) . "%", 0, 0, 'C');//
			}
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

  function VBarDiagram($w, $h, $data,$plan)
  {
      global $__appvar;
      $legendaWidth = 50;
      $grafiekPunt = array();
      $verwijder=array();

      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = jul2form(db2jul($datum));
        $n=0;
        $minVal=0;
        $maxVal=100;
				$grafiek[$datum]=array();
        foreach ($waarden as $categorie=>$waarde)
        {
          $grafiek[$datum][$categorie]=$waarde;
          $grafiekCategorie[$categorie][$datum]=$waarde;
          $categorien[$categorie] = $n;
          $categorieId[$n]=$categorie ;

          $maxVal=max(array($maxVal,$waarde));
          $minVal=min(array($minVal,$waarde));

          if($waarde < 0)
          {
             unset($grafiek[$datum][$categorie]);
             $grafiekNegatief[$datum][$categorie]=$waarde;
          }
          else
             $grafiekNegatief[$datum][$categorie]=0;


          if(!isset($colors[$categorie]))
            $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          $n++;
        }
      }



      $numBars = count($legenda);//12; //
      //$numBars=12;

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
      $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

      $n=0;
      

      foreach ($this->categorieVolgorde as $categorie=>$tmp)
      {
        if(is_array($grafiekCategorie[$categorie]))
        {
          $this->pdf->Rect($XstartGrafiek+$bGrafiek+3 , $YstartGrafiek-$hGrafiek+$n*6+2, 2, 2, 'DF',null,$colors[$categorie]);
          $this->pdf->SetXY($XstartGrafiek+$bGrafiek+6 ,$YstartGrafiek-$hGrafiek+$n*6+1.5 );
          $this->pdf->Cell(20, 3,$this->categorieOmschrijving[$categorie],0,0,'L');
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
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(150,150,150)));
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1)." %",0,0,'R');
        $n++;
        if($n >20)
         break;
      }

      $n=0;
      for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(150,150,150)));
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
	//	echo "line : $bGrafiek -> $eBaton  ".($this->pdf->NbVal + 1)."<br>\n";

      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    

      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;
  
    $kwartalen=false;
      if(count($grafiek)>36)
      {
        $kwartalen=true;
      }

   foreach ($grafiek as $datum=>$data)
   {
       $this->pdf->SetLineWidth(0.2);

      //foreach($data as $categorie=>$val)
      foreach(array_reverse($this->categorieVolgorde) as $categorie=>$tmp)
      {
        $val=$data[$categorie];
        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
          $hval = ($val * $unit);

				  if($hval <> 0)
					{ //echo $datum . " $hval <br>\n";
						$this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null, $colors[$categorie]);
						$YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum] + $hval;
						$this->pdf->SetTextColor(255, 255, 255);

						if (abs($hval) > 3 && $eBaton > 6)
						{
							$this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
							$this->pdf->SetXY($xval, $yval + ($hval / 2) - 2);
							$this->pdf->Cell($eBaton, 4, number_format($val, 1, ',', '.') . "%", 0, 0, 'C');
							$this->pdf->SetFont($this->pdf->rapport_font, '', 6);
						}

						$this->pdf->SetTextColor(0, 0, 0);

						if ($legendaPrinted[$datum] != 1)
						{
							//  $this->pdf->SetXY($xval,$YstartGrafiek+1);
							// $this->pdf->Cell($eBaton,4,$legenda[$datum],0,0,'C');//$this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+4,$legenda[$datum],0);
              
              $datumJul=db2jul($datum);
              if($kwartalen==false || in_array(date('n',$datumJul),array(3,6,9,12)))
                $legendaTonen=true;
              else
                $legendaTonen=false;
              
              if($legendaTonen)
						  	$this->pdf->TextWithRotation($xval - $eBaton / 2, $YstartGrafiek + 8, $legenda[$datum], 30);
              $legendaPrinted[$datum] = 1;
						}

           if($grafiekPunt[$categorie][$datum])
           {
              $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
              if($lastX)
                $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);

              $lastX = $xval+.5*$eBaton;
              $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
           }
				}
      

      }

		 if(isset($plan[$datum]) && $plan[$datum] <> 0)
		 {
			 $planY= $plan[$datum] * $unit + $YstartGrafiek;
			 $planX= $XstartGrafiek + (1 + $i ) * $vBar ;
			 if($lastPlanY <> 0)
			 {
				 $this->pdf->SetDrawColor(128);
				 $this->pdf->SetLineWidth(0.4);
				 $this->pdf->line($lastPlanX, $lastPlanY,$planX, $planY);
				 $this->pdf->SetDrawColor(0);
				 //	 echo "$lastX,$lastPlanY,$xval+.5*$eBaton, $planY <br>\n";
			 }
				 $lastPlanY=$planY;
			 $lastPlanX=$planX;
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
          if($hval <> 0)
					{
						$this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null, $colors[$categorie]);
						$YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum] + $hval;
						$this->pdf->SetTextColor(255, 255, 255);
						if (abs($hval) > 3)
						{
							$this->pdf->SetXY($xval, $yval + ($hval / 2) - 2);
							$this->pdf->Cell($eBaton, 4, number_format($val, 1, ',', '.') . "%", 0, 0, 'C');
						}
						$this->pdf->SetTextColor(0, 0, 0);

						if ($grafiekPunt[$categorie][$datum])
						{
							$this->pdf->Rect($xval + .5 * $eBaton - .5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek - .5, 1, 1, 'DF',null, array(128, 128, 128));
							if ($lastX)
							{
								$this->pdf->line($lastX, $lastY, $xval + .5 * $eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
							}
							$lastX = $xval + .5 * $eBaton;
							$lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
						}
					}
      }
      $i++;
   }
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
}
?>
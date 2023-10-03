<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/06/19 15:59:09 $
File Versie					: $Revision: 1.6 $

$Log: RapportPERFG_L10.php,v $
Revision 1.6  2019/06/19 15:59:09  rvv
*** empty log message ***

Revision 1.5  2019/05/18 16:29:36  rvv
*** empty log message ***

Revision 1.4  2019/01/16 16:28:10  rvv
*** empty log message ***

Revision 1.3  2018/12/03 06:50:50  rvv
*** empty log message ***

Revision 1.2  2018/12/01 19:51:30  rvv
*** empty log message ***

Revision 1.1  2018/10/07 08:29:03  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");


class RapportPERFG_L10
{
	function RapportPERFG_L10($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Historische performanceverloop";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	  $this->rapportageDatumVanaf = "$RapStartJaar-01-01";


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




//listarray($this->categorieVolgorde);
		// voor data
		$this->pdf->widthA = array(1,95,25,5,25,5,25,5,25,5,25,5,25,5,25,5);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R');


  	$this->pdf->widthB = array(1,95,30,10,30,115);
		$this->pdf->alignB = array('L','L','R','R','R');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->AddPage();
    $this->pdf->templateVars['PERFGPaginas']=$this->pdf->page;

		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];


$DB = new DB();
$query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE periode='m' AND Portefeuille = '".$this->portefeuille."' AND Categorie = 'Totaal' ORDER BY Datum ASC LIMIT 1 ";
$DB->SQL($query);
$DB->Query();
$datum = $DB->nextRecord();


if($datum['id'] > 0 && $this->pdf->lastPOST['perfPstart'] == 1)
{
  if($datum['month'] <10)
    $datum['month'] = "0".$datum['month'];
  $start = $datum['year'].'-'.$datum['month'].'-01';
}
else
  $start = substr($this->pdf->PortefeuilleStartdatum,0,10);
$eind = $this->rapportageDatum;

$datumStart = db2jul($start);
$datumStop  = db2jul($eind);

$index = new indexHerberekening();
$indexData = $index->getWaarden($start,$eind,$this->portefeuille,$this->pdf->portefeuilledata['SpecifiekeIndex']);

foreach ($indexData as $index=>$data)
{
  if($data['datum'] != '0000-00-00')
  {
    $rendamentWaarden[] = $data;
    $grafiekData['Datum'][] = $data['datum'];
    $grafiekData['Index'][] = $data['index']-100;
    $grafiekData['benchmarkIndex'][] = $data['specifiekeIndex']-100;

  }
}
  //  listarray($grafiekData);

  if(count($rendamentWaarden) > 0)
   {
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $this->pdf->underlinePercentage=0.8;
        //$this->pdf->SetFillColor(137,188,255);
     $this->pdf->SetFillColor($this->pdf->rapport_background_fill[0],$this->pdf->rapport_background_fill[1],$this->pdf->rapport_background_fill[2]);


     $totaal=array();
        $perioden=array('jaar','totaal');
        $jaarRendement=array();
        foreach($perioden as $periode)
          $jaarRendement[$periode]=100;
        
        $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
        $fill=false;
		    foreach ($rendamentWaarden as $row)
		    {
		      $resultaat = $row['Opbrengsten']-$row['Kosten'];
		      $datum = db2jul($row['datum']);
          $jaar = date("Y",$datum);
          if(isset($lastJaar) && $lastJaar!=$jaar)
          {
            $this->printTotaal($totaal,$lastJaar);
            $totaal['jaar']=array();
            $jaarRendement['jaar']=100;
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
          }
                                       
          foreach($perioden as $periode)
          {
            $jaarRendement[$periode] = ($jaarRendement[$periode]  * (100+$row['performance'])/100);
            //echo $row['datum']." ".round($row['performance'],6). " ".round($row['index'],6)." ".round($jaarRendement[$periode],6)." <br>\n";
		                           if(!isset($totaal[$periode]['waardeBegin']))
		                             $totaal[$periode]['waardeBegin']=$row['waardeBegin'];
		                           $totaal[$periode]['Waarde'] = $row['waardeHuidige'];
		                           $totaal[$periode]['Resultaat'] += $row['resultaatVerslagperiode'];
		                           $totaal[$periode]['Gerealiseerd'] += $row['gerealiseerd'];
		                           $totaal[$periode]['Ongerealiseerd'] += $row['ongerealiseerd'];
		                           $totaal[$periode]['Opbrengsten'] += $row['opbrengsten'];
		                           $totaal[$periode]['Kosten'] += $row['kosten'];
		                           $totaal[$periode]['Rente'] += $row['rente'];
		                           $totaal[$periode]['StortingenOntrekkingen'] += $row['stortingen']-$row['onttrekkingen'];
		                           $totaal[$periode]['Rendament'] = $row['index'];
                               $totaal[$periode]['JaarRendament'] = $jaarRendement[$periode];
          }
  	      $lastJaar=$jaar;
		    }
        $this->printTotaal($totaal,$lastJaar);

            
            $this->pdf->CellBorders = array('','TS','TS','TS','TS','TS','TS','TS','TS','','TS'); 
            $this->pdf->fillCell=array();
            $this->pdf->row(array('','','','','','','','','','','','')); 
            $this->pdf->SetY($this->pdf->GetY()-4);


        $this->pdf->ln(3);
        
        //$this->pdf->CellBorders = array('','UU','UU','UU','UU','UU','UU','UU','UU','UU','','UU');
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->CellBorders = array();
		    $this->pdf->row(array(vertaalTekst('Totaal',$this->pdf->rapport_taal),
		                           $this->formatGetal($totaal['totaal']['waardeBegin'],2),
		                           $this->formatGetal($totaal['totaal']['StortingenOntrekkingen'],2),
		                           $this->formatGetal($totaal['totaal']['Gerealiseerd']+$totaal['totaal']['Ongerealiseerd'],2),
		                           $this->formatGetal($totaal['totaal']['Opbrengsten'],2),
		                           $this->formatGetal($totaal['totaal']['Kosten'],2),
		                           $this->formatGetal($totaal['totaal']['Rente'],2),
		                           $this->formatGetal($totaal['totaal']['Resultaat'],2),
		                           $this->formatGetal($totaal['totaal']['Waarde'],2),
		                           '',
		                           $this->formatGetal($totaal['totaal']['Rendament']-100,2)."%"
		                           ));//$this->formatGetal($totaalRendamentIndex-100,2)
		    $this->pdf->CellBorders = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		  }
		  
		  
		  if(db2jul($start)<db2jul('2012-01-01'))
      {
        $index = new indexHerberekening();
        $indexData = $index->getWaarden('2012-01-01',$eind,$this->portefeuille,$this->pdf->portefeuilledata['SpecifiekeIndex']);
        $grafiekData=array();
        foreach ($indexData as $index=>$data)
        {
          if($data['datum'] != '0000-00-00')
          {
            $grafiekData['Datum'][] = $data['datum'];
            $grafiekData['Index'][] = $data['index']-100;
            $grafiekData['benchmarkIndex'][] = $data['specifiekeIndex']-100;
          }
        }
      }
    




		  if (count($grafiekData) > 1)
		  {
        $yShift=-3;
        $this->pdf->SetXY(8,111+$yShift);//104
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  		  $this->pdf->Cell(0, 5, vertaalTekst('Rendement',$this->pdf->rapport_taal).' ('.
                               vertaalTekst('cumulatief',$this->pdf->rapport_taal).' '.
                               vertaalTekst('in',$this->pdf->rapport_taal).' %)', 0, 1);
  		  $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
  		  $this->pdf->SetXY(15,117+$yShift)		;//112
        $valX = $this->pdf->GetX();
        $valY = $this->pdf->GetY();
      
        if(!isset($this->pdf->rapport_grafiek_pcolor))
          $this->pdf->rapport_grafiek_pcolor=array(106,100,122);//array(rand(0,255),rand(0,255),rand(0,255));
      
        if(!isset($this->pdf->rapport_grafiek_icolor))
          $this->pdf->rapport_grafiek_icolor=array(176,160,122);//array(rand(0,255),rand(0,255),rand(0,255));
        
        //function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
        $kleuren=array(array($this->pdf->rapport_grafiek_pcolor[0],$this->pdf->rapport_grafiek_pcolor[1],$this->pdf->rapport_grafiek_pcolor[2]),array($this->pdf->rapport_grafiek_icolor[0],$this->pdf->rapport_grafiek_icolor[1],$this->pdf->rapport_grafiek_icolor[2]));
        $this->LineDiagram(270, 60, $grafiekData,$kleuren,0,0,6,5,1);//50
        $this->pdf->SetXY($valX, $valY + 75+$yShift);
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        foreach($kleuren as $index=>$kleur)
        {
          $this->pdf->rect($this->pdf->getX()-2,$this->pdf->getY()+1,2,2,'F','',$kleur);
          if($index==0)
            $this->pdf->Cell(50, 4, 'Portefeuille', 0, 0, "L");
          if($index==1)
            $this->pdf->Cell(50, 4, $this->pdf->portefeuilledata['SpecifiekeIndex'], 0, 0, "L");
        }

		  }

	   $this->pdf->fillCell = array();



	}


  function printTotaal($totaal,$jaar)
{
   	    $this->pdf->row(array(vertaalTekst('Totaal '.$jaar,$this->pdf->rapport_taal),
		                           $this->formatGetal($totaal['jaar']['waardeBegin'],2),
		                           $this->formatGetal($totaal['jaar']['StortingenOntrekkingen'],2),
		                           $this->formatGetal($totaal['jaar']['Gerealiseerd']+$totaal['jaar']['Ongerealiseerd'],2),
		                           $this->formatGetal($totaal['jaar']['Opbrengsten'],2),
		                           $this->formatGetal($totaal['jaar']['Kosten'],2),
		                           $this->formatGetal($totaal['jaar']['Rente'],2),
		                           $this->formatGetal($totaal['jaar']['Resultaat'],2),
		                           $this->formatGetal($totaal['jaar']['Waarde'],2),
		                           $this->formatGetal($totaal['jaar']['JaarRendament']-100,2)."%",
                               $this->formatGetal($totaal['totaal']['Rendament']-100,2)."%"
		                           ));
 
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
    $data1 = $data['benchmarkIndex'];
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
    $lDiag = floor($w );
  
    $this->pdf->Rect($XPage,$YPage, $w, $h ,'D');

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(0,38,84);
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

    if($jaar && count($data) < 12)
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
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 2,'color'=>array(100,100,100)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
       break;
    }

    $n=0;
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 2,'color'=>array(100,100,100)));
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
    $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    //listarray($data);
   // $color=array(200,0,0);
   
   $legendaStep=ceil(count($data)/20);
    for ($i=0; $i<count($data); $i++)
    {
      $extrax=($unit*0.1*-1);
      if($i <> 0)
        $extrax1=($unit*0.1*-1);
        
        
     if($i%$legendaStep==0)
      $this->pdf->TextWithRotation($XDiag+($i)*$unit-10+$unit,$YDiag+$hDiag+8,jul2form(db2jul($legendDatum[$i])),25);

      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit+$extrax1, $yval, $XDiag+($i+1)*$unit+$extrax, $yval2,$lineStyle );
     // $this->pdf->Rect($XDiag+($i+1)*$unit-0.5+$extrax, $yval2-0.5, 1, 1 ,'F','',$color);
      
      if($data[$i] <> 0 && $i%$legendaStep==0)
        $this->pdf->Text($XDiag+($i+1)*$unit-1+$extrax,$yval2-2.5,$this->formatGetal($data[$i],1));
     
      
      $yval = $yval2;
    }

    if(is_array($data1))
    {
     // listarray($data1);
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color1);
      for ($i=0; $i<count($data1); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        //$this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color1);
        if($i%$legendaStep==0)
          $this->pdf->Text($XDiag+($i+1)*$unit-1,$yval2-2.5,$this->formatGetal($data1[$i],1));
         
        $yval = $yval2;
      }
    }
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }


}
?>
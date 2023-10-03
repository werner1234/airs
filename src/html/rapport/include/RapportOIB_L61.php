<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/09/23 17:42:26 $
File Versie					: $Revision: 1.5 $

$Log: RapportOIB_L61.php,v $
Revision 1.5  2017/09/23 17:42:26  rvv
*** empty log message ***

Revision 1.4  2016/11/02 16:34:11  rvv
*** empty log message ***

Revision 1.3  2015/12/13 09:03:13  rvv
*** empty log message ***

Revision 1.2  2015/09/13 11:32:29  rvv
*** empty log message ***

Revision 1.1  2015/09/05 16:48:04  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIB_L61
{
	function RapportOIB_L61($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Onderverdeling in beleggingscategorieën";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }




	function writeRapport()
	{
		global $__appvar;
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
		$this->pdf->AddPage();
    $this->pdf->templateVars['OIBPaginas']=$this->pdf->page;

		$rapportageDatum = $this->rapportageDatum;
		$rapportageDatumVanaf = $this->rapportageDatumVanaf;
	$portefeuille = $this->portefeuille;

	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
					 "FROM TijdelijkeRapportage WHERE ".
					 " rapportageDatum ='".$rapportageDatum."' AND ".
					 " portefeuille = '".$portefeuille."' "
					 .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$totaalWaarde = $DB->nextRecord();
	$totaalWaarde = $totaalWaarde['totaal'];

  $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
					 "FROM TijdelijkeRapportage WHERE ".
					 " rapportageDatum ='".$rapportageDatumVanaf."' AND ".
					 " portefeuille = '".$portefeuille."' "
					 .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$totaalWaardeBegin = $DB->nextRecord();
	$totaalWaardeBegin = $totaalWaardeBegin['totaal'];

	$query = "SELECT
			SUM(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro
			FROM
			TijdelijkeRapportage
			WHERE
			TijdelijkeRapportage.Portefeuille = '".$portefeuille."' AND
			TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."' AND
 			TijdelijkeRapportage.Type = 'rekening'
			" .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$totaalLiquiditeiten = $DB->nextRecord();
	$totaalLiquiditeiten = $totaalLiquiditeiten['WaardeEuro'];




	//Kleuren instellen
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);


$query="SELECT
if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.beleggingscategorie <> '',TijdelijkeRapportage.beleggingscategorie,'geen')) as categorie,
sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
 if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.beleggingscategorie <> '',Beleggingscategorien.Omschrijving,'geen')) as categorieOmschrijving
FROM TijdelijkeRapportage LEFT JOIN Beleggingscategorien on TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.beleggingscategorie
	WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'"
	.$__appvar['TijdelijkeRapportageMaakUniek'].
	" GROUP BY categorie
	ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde, categorie";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	while($cat = $DB->nextRecord())
	{
	   $data['beleggingscategorieEind']['data'][$cat['categorie']]['waardeEur']=$cat['WaardeEuro'];
	   $data['beleggingscategorieEind']['data'][$cat['categorie']]['Omschrijving']=$cat['categorieOmschrijving'];
	   $data['beleggingscategorieEind']['pieData'][$cat['categorieOmschrijving']]= $cat['WaardeEuro']/$totaalWaarde;
	   $data['beleggingscategorieEind']['kleurData'][$cat['categorieOmschrijving']]=$allekleuren['OIB'][$cat['categorie']];
	   $data['beleggingscategorieEind']['kleurData'][$cat['categorieOmschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaarde*100;
	}

	$query="SELECT
if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.beleggingscategorie <> '',TijdelijkeRapportage.beleggingscategorie,'geen')) as categorie,
sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
 if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.beleggingscategorie <> '',Beleggingscategorien.Omschrijving,'geen')) as categorieOmschrijving
FROM TijdelijkeRapportage LEFT JOIN Beleggingscategorien on TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.beleggingscategorie
	WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatumVanaf."'"
	.$__appvar['TijdelijkeRapportageMaakUniek'].
	" GROUP BY categorie
	ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde, categorie";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	while($cat = $DB->nextRecord())
	{
	   $data['beleggingscategorieBegin']['data'][$cat['categorie']]['waardeEur']=$cat['WaardeEuro'];
	   $data['beleggingscategorieBegin']['data'][$cat['categorie']]['Omschrijving']=$cat['categorieOmschrijving'];
	   $data['beleggingscategorieBegin']['pieData'][$cat['categorieOmschrijving']]= $cat['WaardeEuro']/$totaalWaardeBegin;
	   $data['beleggingscategorieBegin']['kleurData'][$cat['categorieOmschrijving']]=$allekleuren['OIB'][$cat['categorie']];
	   $data['beleggingscategorieBegin']['kleurData'][$cat['categorieOmschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaardeBegin*100;
	}


	$query="SELECT
TijdelijkeRapportage.regio,
Sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
TijdelijkeRapportage.regioOmschrijving as Omschrijving
FROM
TijdelijkeRapportage
WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'"
	.$__appvar['TijdelijkeRapportageMaakUniek'].
" GROUP BY regio 
HAVING WaardeEuro <> 0
ORDER BY TijdelijkeRapportage.regioVolgorde";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	while($cat = $DB->nextRecord())
	{
	   if($cat['regio']=='')
       $cat['regio']='geen';
     if($cat['Omschrijving']=='')
       $cat['Omschrijving']='geen';
         
	   $data['regioVerdeling']['data'][$cat['regio']]['waardeEur']=$cat['WaardeEuro'];
	   $data['regioVerdeling']['data'][$cat['regio']]['Omschrijving']=$cat['Omschrijving'];
	   $data['regioVerdeling']['pieData'][$cat['Omschrijving']]= $cat['WaardeEuro']/$totaalWaarde;
	   $data['regioVerdeling']['kleurData'][$cat['Omschrijving']]=$allekleuren['OIR'][$cat['regio']];
	   $data['regioVerdeling']['kleurData'][$cat['Omschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaarde*100;
	}


$this->pdf->setXY(30,37);
//$this->pdf->setXY(65,40);
$this->printPie($data['beleggingscategorieBegin']['pieData'],$data['beleggingscategorieBegin']['kleurData'],'Categorieverdeling '.date("d-m-Y",db2jul($rapportageDatumVanaf)),60,50);
$this->pdf->wLegend=0;
$this->pdf->setXY(120,37);
//$this->pdf->setXY(175,40);
$this->printPie($data['beleggingscategorieEind']['pieData'],$data['beleggingscategorieEind']['kleurData'],'Categorieverdeling '.date("d-m-Y",db2jul($rapportageDatum)),60,50);
$this->pdf->wLegend=0;

$this->pdf->setXY(210,37);
$this->printPie($data['regioVerdeling']['pieData'],$data['regioVerdeling']['kleurData'],'Regioverdeling '.date("d-m-Y",db2jul($rapportageDatum)),60,50);

$yHoogte=100;
$this->pdf->setXY($this->pdf->marge,$yHoogte);    
foreach ($data as $type=>$typeData)
{
  $xStart = 24 ;
  $x2 = $xStart + 2 + $margin;
  $y1 = $yHoogte + 3;
  $n=0;
  foreach ($typeData['data'] as $categorie=>$gegevens)
  {
    if(!is_array($regelData[$n]))
      $regelData[$n]=array('','','','','','','','','','');
    if($type=='beleggingscategorieBegin')
      $offset=0;
    if($type=='beleggingscategorieEind')
      $offset=4;
    if($type=='regioVerdeling')
      $offset=8;
  
     $x1=$xStart+$offset*22.5;

     $kleur=array($data[$type]['kleurData'][$gegevens['Omschrijving']]['R']['value'],
                  $data[$type]['kleurData'][$gegevens['Omschrijving']]['G']['value'],
                  $data[$type]['kleurData'][$gegevens['Omschrijving']]['B']['value']);
  
          $this->pdf->SetFillColor($kleur[0],$kleur[1],$kleur[2]);
          $this->pdf->Rect($x1, $y1, 2, 2, 'DF');
          $this->pdf->SetXY($x2,$y1);
         
          $y1+=4;
      
     $regelData[$n][0]='';
     $regelData[$n][1+$offset]=$gegevens['Omschrijving'];
     $regelData[$n][2+$offset]=$this->formatGetal($gegevens['waardeEur'],0);
     $regelData[$n][3+$offset]=$this->formatGetal($data[$type]['kleurData'][$gegevens['Omschrijving']]['percentage'],2).'%';
     $regelData[$n][4+$offset]='';
     $n++;

     $regelTotaal[$type]['waardeEur']+=$gegevens['waardeEur'];
     $regelTotaal[$type]['percentage']+=round($data[$type]['kleurData'][$gegevens['Omschrijving']]['percentage'],2);
  }

}
$this->pdf->setXY($this->pdf->marge,$yHoogte);

foreach ($regelData as $regelNr=>$regel)
{
  ksort($regel);
  $regelData[$regelNr]=$regel;
}


$this->pdf->SetWidths(array(20, 40,20,15, 15, 40,20,15, 15, 40,20,15));
//$this->pdf->SetWidths(array(45, 40,20,15, 40, 40,20,15, 15));
$this->pdf->SetAligns(array('L', 'L','R','R',  'L',  'L','R','R',  'L',  'L','R','R'));



//
$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
$this->pdf->CellBorders = array();
$this->pdf->ln(2);

$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
foreach ($regelData as $regel)
{
  $this->pdf->row($regel);
}

$this->pdf->underlinePercentage=0.8;
$this->pdf->CellBorders = array('','','TS','TS','','','TS','TS','','','TS','TS');
$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
$this->pdf->row(array('','Totaal '.date("d-m-Y",db2jul($rapportageDatumVanaf)), $this->formatGetal($regelTotaal['beleggingscategorieBegin']['waardeEur']),$this->formatGetal($regelTotaal['beleggingscategorieBegin']['percentage'],0).'%','',
'Totaal '.date("d-m-Y",db2jul($rapportageDatum)), $this->formatGetal($regelTotaal['beleggingscategorieEind']['waardeEur']),$this->formatGetal($regelTotaal['beleggingscategorieEind']['percentage'],0).'%'
,'','Totaal '.date("d-m-Y",db2jul($rapportageDatum)), $this->formatGetal($regelTotaal['regioVerdeling']['waardeEur']),$this->formatGetal($regelTotaal['regioVerdeling']['percentage'],0).'%'
));
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
unset($this->pdf->CellBorders);



$this->toonBenchmark();
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


	function getFondsKoers($fonds,$datum)
	{
	  $db=new DB();
	  $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
	  $db->SQL($query);
	  $koers=$db->lookupRecord();
	  return $koers['Koers'];
	}
  

	function getMaanden($julBegin, $julEind)
  {
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);

	  $i=0;
	  $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
  	while ($counterStart < $stop)
	  {
	    $counterStart = mktime (0,0,0,$beginmaand+$i,0,$beginjaar);
	    $counterEnd   = mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;

      if($i == 0)
      {
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
      }
	    else
	    {
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);
	      if(substr($datum[$i]['start'],5,5)=='12-31')
	        $datum[$i]['start']=(date('Y',$counterStart)+1)."-01-01";
	    }

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
       $i++;
	  }
	  return $datum;
  }
  
function fondsPerformance($fonds,$vanaf,$tot)
{
  
  $maanden=$this->getMaanden(db2jul($vanaf),db2jul($tot));
  $januari=substr($tot,0,4)."-01-01";
  
  $totalPerf=0;
  foreach($maanden as $maand)
  {
   $indexData=array('fondsKoers_eind'=>$this->getFondsKoers($fonds,$maand['stop']),
                    'fondsKoers_begin'=>$this->getFondsKoers($fonds,$maand['start']),
                    'fondsKoers_jan'=>$this->getFondsKoers($fonds,$januari));
                    
   $jaarPerf=($indexData['fondsKoers_eind'] - $indexData['fondsKoers_jan']) / ($indexData['fondsKoers_jan']/100 );                 
   $voorPerf=($indexData['fondsKoers_begin'] - $indexData['fondsKoers_jan']) / ($indexData['fondsKoers_jan']/100 );
   $totalPerf+=($jaarPerf-$voorPerf);
   //echo "m $fonds ".($jaarPerf-$voorPerf)." <br>\n";
  }
  //echo "t $fonds $totalPerf  $vanaf,$tot <br>\n";
  return $totalPerf;
}

function toonBenchmark()
{
  $DB=new DB();
  
  $RapStartJaar = date("Y", db2jul($this->rapportageDatum));
	if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
	  $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	  $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	else
	  $this->tweedePerformanceStart = "$RapStartJaar-01-01";
     
  $huidigeMaand=date('m',db2jul($this->rapportageDatum));
  $kwartaal=ceil($huidigeMaand/3);
  if($kwartaal==1)
    $beginKwartaal=$RapStartJaar.'-01-01';
  elseif($kwartaal==2)
    $beginKwartaal=$RapStartJaar.'-03-31';
  elseif($kwartaal==3)
    $beginKwartaal=$RapStartJaar.'-06-30';
  elseif($kwartaal==4)
    $beginKwartaal=$RapStartJaar.'-09-30';  
       
	if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($beginKwartaal))
	  $beginKwartaal = $this->pdf->PortefeuilleStartdatum;       
        
	$perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$beginKwartaal,'eind'=>$this->rapportageDatum);

$query="SELECT
BeleggingscategoriePerFonds.Beleggingscategorie,
Indices.Beursindex as Fonds,
Beleggingscategorien.Omschrijving as catOmschrijving,
Fondsen.Omschrijving,
2 as tweedeVolgorde,
IFNULL(Beleggingscategorien.Afdrukvolgorde,127) as hoofdVolgorde
FROM
Indices
JOIN Fondsen ON Indices.Beursindex=Fondsen.Fonds
INNER JOIN BeleggingscategoriePerFonds ON Indices.Vermogensbeheerder = BeleggingscategoriePerFonds.Vermogensbeheerder AND Indices.Beursindex = BeleggingscategoriePerFonds.Fonds
LEFT JOIN Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie=Beleggingscategorien.Beleggingscategorie
WHERE
Indices.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY hoofdVolgorde,Indices.Afdrukvolgorde,Fonds";
		$DB->SQL($query);
		$DB->Query();
		$benchmarkCategorie=array();
	  while($index = $DB->nextRecord())
		{
      if($index['catOmschrijving'] == '')
        $index['catOmschrijving']='Overige';

		  $benchmarkCategorie[$index['catOmschrijving']][]=$index['Fonds'];

		 	$indexData[$index['Fonds']]=$index;
      foreach ($perioden as $periode=>$datum)
      {
        $indexData[$index['Fonds']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Fonds'],$datum);
      }
      
     	$indexData[$index['Fonds']]['performanceJaar'] = $this->fondsPerformance($index['Fonds'],$perioden['jan'],$perioden['eind']);
			$indexData[$index['Fonds']]['performance'] =    $this->fondsPerformance($index['Fonds'],$perioden['begin'],$perioden['eind']);
 		}



    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->SetXY(8,135);
    
    $extraX=45;

  	$this->pdf->SetWidths(array(0,50,15,18,17,  $extraX,50,15,18,17,  36  ));//,20,23
  	$this->pdf->SetAligns(array('L','L','R','R','R',  'L','L','R','R','R',  'L'));
 	  $this->pdf->ln();
  //	$this->pdf->CellBorders = array('','U','U','U','U','','U','U','U','U','','U','U','U','U');
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize-2);
    
    $this->pdf->Rect($this->pdf->marge+0,$this->pdf->GetY(),array_sum($this->pdf->widths)-0,8,'F',null,array($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']));

    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
 	 // $this->pdf->CellBorders = array('','U','U','U','U');
 	  $this->pdf->row(array("","\n".vertaalTekst("Index",$this->pdf->rapport_taal),
                               "".vertaalTekst("Koers",$this->pdf->rapport_taal)." ".date("d-m-Y",db2jul($perioden['eind'])),
                               "".vertaalTekst("Rendement dit kwartaal",$this->pdf->rapport_taal),
                               "".vertaalTekst("Rendement dit jaar"),
    "","\n".vertaalTekst("Index",$this->pdf->rapport_taal),
                               "".vertaalTekst("Koers",$this->pdf->rapport_taal)." ".date("d-m-Y",db2jul($perioden['eind'])),
                               "".vertaalTekst("Rendement dit kwartaal",$this->pdf->rapport_taal),
                               "".vertaalTekst("Rendement dit jaar"),' '));
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  //	unset($this->pdf->CellBorders);

	$this->pdf->SetWidths(array(0,53,15,16,16,  $extraX,53,15,16,16,  $extraX,43,15,16,16));//,20,23
    $categorieVertalingen=array('Aandelen'=>'Aandelen (total return)' ,'Obligaties'=>'Obligaties (total return)');
    $startY=$this->pdf->GetY();
    $extraX=0;
	  $n=0;
  	foreach ($benchmarkCategorie as $categorie=>$fondsen)
  	{
			if($n>1)
				break;
  	  $this->pdf->SetY($startY);
  	  $this->pdf->SetWidths(array($extraX,53,15,16,16));
      $extraX += 100+45;
      
  	  $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize-2);
      $this->pdf->underline=1;
      if(isset($categorieVertalingen[$categorie]))
        $this->pdf->row(array("",vertaalTekst($categorieVertalingen[$categorie],$this->pdf->rapport_taal)));
      else
  	    $this->pdf->row(array("",vertaalTekst($categorie,$this->pdf->rapport_taal)));
      $this->pdf->underline=0;
  	  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize-2);

  	  foreach ($fondsen as $fonds)
  	  {
  	    $fondsData=$indexData[$fonds];
  	    $this->pdf->row(array('',$fondsData['Omschrijving'],
  	    $this->formatGetal($indexData[$fonds]['fondsKoers_eind'],2),
  	    $this->formatGetal($fondsData['performance'],2),
        $this->formatGetal($fondsData['performanceJaar'],2)));
  	  }
      $this->pdf->Ln();
			$n++;
  	}
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
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
          $factor=1.5;

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
/*
      for($i=0; $i<$this->pdf->NbVal; $i++) {
          $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->pdf->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1);
          $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
          $y1+=$hLegend + 2;
      }
*/
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
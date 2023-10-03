<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.13 $

$Log: RapportKERNV_L68.php,v $
Revision 1.13  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.12  2018/06/20 16:40:16  rvv
*** empty log message ***

Revision 1.11  2017/05/28 09:58:52  rvv
*** empty log message ***

Revision 1.10  2017/05/26 16:45:07  rvv
*** empty log message ***

Revision 1.9  2017/04/29 17:26:01  rvv
*** empty log message ***

Revision 1.8  2017/02/25 18:02:28  rvv
*** empty log message ***

Revision 1.7  2016/12/17 16:33:26  rvv
*** empty log message ***

Revision 1.6  2016/11/09 17:05:19  rvv
*** empty log message ***

Revision 1.5  2016/09/18 08:49:02  rvv
*** empty log message ***

Revision 1.4  2016/06/19 15:22:08  rvv
*** empty log message ***

Revision 1.3  2016/06/12 10:27:20  rvv
*** empty log message ***

Revision 1.2  2016/05/21 19:00:02  rvv
*** empty log message ***

Revision 1.1  2016/05/08 19:24:24  rvv
*** empty log message ***

Revision 1.2  2015/12/16 17:06:48  rvv
*** empty log message ***

Revision 1.1  2015/09/05 16:48:04  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportKERNZ_L68.php");

class RapportKERNV_L68
{
  /*
  function RapportKERNV_L68($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum, $valuta = 'EUR')
	{
    $this->rapport = new RapportKERNZ_L68($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum, $valuta = 'EUR');
    $this->rapport->pdf->rapport_titel = "Onderverdeling Vastrentende waarden";
    $this->rapport->rapportFilter=" AND TijdelijkeRapportage.hoofdcategorie='VAR' ";
	}


	function writeRapport()
	{
    $this->rapport->writeRapport();
	}
  */
	function RapportKERNV_L68($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "KERNZ";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Onderverdeling Vastrentende waarden";
		if($this->pdf->lastPOST['doorkijk']==1)
			$this->portefeuille = 'd_'.$portefeuille;
		else
	  	$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
    $this->rapportFilter=" AND TijdelijkeRapportage.hoofdcategorie='VAR' ";
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
    $this->pdf->templateVars['KERNVPaginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving['KERNVPaginas']=$this->pdf->rapport_titel;

		$rapportageDatum = $this->rapportageDatum;
		$rapportageDatumVanaf = $this->rapportageDatumVanaf;
	$portefeuille = $this->portefeuille;

	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
					 "FROM TijdelijkeRapportage WHERE ".
					 " rapportageDatum ='".$rapportageDatum."' AND ".
					 " portefeuille = '".$portefeuille."' ".$this->rapportFilter
					 .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$totaalWaarde = $DB->nextRecord();
	$totaalWaarde = $totaalWaarde['totaal'];

	$query = "SELECT
			SUM(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro
			FROM
			TijdelijkeRapportage
			WHERE
			TijdelijkeRapportage.Portefeuille = '".$portefeuille."' AND
			TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."' AND
 			TijdelijkeRapportage.Type = 'rekening' ".$this->rapportFilter." ".
       $__appvar['TijdelijkeRapportageMaakUniek'];
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
if(TijdelijkeRapportage.valuta <> '',TijdelijkeRapportage.valuta,'geen') as categorie,
sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
if(TijdelijkeRapportage.Beleggingscategorie <> '',TijdelijkeRapportage.valutaOmschrijving,'geen') as categorieOmschrijving
FROM TijdelijkeRapportage 
	WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."' ".$this->rapportFilter." "
	.$__appvar['TijdelijkeRapportageMaakUniek'].
	" GROUP BY categorie
	ORDER BY TijdelijkeRapportage.valutaVolgorde, categorie";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	while($cat = $DB->nextRecord())
	{
	   $data['valuta']['data'][$cat['categorie']]['waardeEur']=$cat['WaardeEuro'];
	   $data['valuta']['data'][$cat['categorie']]['Omschrijving']=$cat['categorieOmschrijving'];
	   $data['valuta']['pieData'][$cat['categorieOmschrijving']]= $cat['WaardeEuro']/$totaalWaarde;
	   $data['valuta']['kleurData'][$cat['categorieOmschrijving']]=$allekleuren['OIV'][$cat['categorie']];
	   $data['valuta']['kleurData'][$cat['categorieOmschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaarde*100;
	}

	$query="SELECT
if(Fondsen.rating <> '',Fondsen.rating,'geen') as categorie,
sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
if(Fondsen.rating <> '',Fondsen.rating,'Geen rating') as categorieOmschrijving
	FROM TijdelijkeRapportage
  LEFT JOIN Fondsen on TijdelijkeRapportage.fonds=Fondsen.fonds
  WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'".$this->rapportFilter." "
	.$__appvar['TijdelijkeRapportageMaakUniek'].
	" GROUP BY categorie
	ORDER BY Fondsen.rating";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	//	listarray($allekleuren['Rating']);
	$allekleuren['Rating']['geen']=array('R'=>array('value'=>30),'G'=>array('value'=>130),'B'=>array('value'=>50));
	while($cat = $DB->nextRecord())
	{
	   $data['rating']['data'][$cat['categorie']]['waardeEur']=$cat['WaardeEuro'];
	   $data['rating']['data'][$cat['categorie']]['Omschrijving']=$cat['categorieOmschrijving'];
	   $data['rating']['pieData'][$cat['categorieOmschrijving']]+= $cat['WaardeEuro']/$totaalWaarde;
	   $data['rating']['kleurData'][$cat['categorieOmschrijving']]=$allekleuren['Rating'][$cat['categorie']];
	   $data['rating']['kleurData'][$cat['categorieOmschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaarde*100;
	}


	  $this->cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		$this->cashfow->genereerTransacties();
		$regels = $this->cashfow->genereerRows();
		$huidigeJaar=date("Y",$this->pdf->rapport_datum);
		foreach ($this->cashfow->regelsRaw as $regel)
		{

		  if($regel[2]=='lossing')
		  {
		    $jaar=substr($regel['0'],6,4);
		   // echo "$jaar > ".($huidigeJaar+15)."<br>\n";
		    if($jaar > ($huidigeJaar+15))
		      $jaar='Overig';

		    $cashflowJaar[$jaar] +=$regel[3];
		    $cashflowTotaal +=$regel[3];
		  }
		}
    
$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
	  $query="SELECT
TijdelijkeRapportage.hoofdcategorieOmschrijving AS HcategorieOmschrijving,
TijdelijkeRapportage.historischeWaarde,
TijdelijkeRapportage.historischeValutakoers,
 SUM(IF(TijdelijkeRapportage.type = 'fondsen',(beginPortefeuilleWaardeEuro),0 )) / ".$this->pdf->ValutaKoersStart." AS beginPortefeuilleWaardeEuro,
SUM(IF(TijdelijkeRapportage.type = 'fondsen',TijdelijkeRapportage.beginwaardeLopendeJaar,0))  as beginwaardeLopendeJaar,
SUM(IF(TijdelijkeRapportage.type = 'fondsen',TijdelijkeRapportage.historischeWaarde,0)) as historischeWaarde,
SUM(IF(TijdelijkeRapportage.type = 'rente' , (actuelePortefeuilleWaardeEuro),0)) / ".$this->pdf->ValutaKoersEind." AS rente,
SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS actuelePortefeuilleWaardeEuro ,
 SUM(IF(TijdelijkeRapportage.type = 'fondsen',(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid * TijdelijkeRapportage.actueleValuta),0
 )) AS historischeWaardeEuro,
IF(TijdelijkeRapportage.type = 'rekening' ,actuelePortefeuilleWaardeInValuta, totaalAantal) as totaalAantal,
TijdelijkeRapportage.actueleFonds,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.actueleValuta,
TijdelijkeRapportage.fondsOmschrijving,
TijdelijkeRapportage.rekening,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.beleggingscategorieVolgorde as Afdrukvolgorde,
TijdelijkeRapportage.type,
TijdelijkeRapportage.beleggingscategorieOmschrijving as categorieOmschrijving,
Fondsen.rating as fondsRating,
Fondsen.Lossingsdatum,
Fondsen.variabeleCoupon,
Fondsen.Renteperiode,
Fondsen.Rentedatum,
emittentPerFonds.emittent,
TijdelijkeRapportage.fonds,
emittenten.rating as emittentRating,
TijdelijkeRapportage.fondsEenheid
FROM
TijdelijkeRapportage
Left Join Fondsen ON Fondsen.Fonds = TijdelijkeRapportage.Fonds
Left Join emittentPerFonds ON emittentPerFonds.Fonds = TijdelijkeRapportage.Fonds  AND emittentPerFonds.vermogensbeheerder='$beheerder'
LEFT Join emittenten ON emittentPerFonds.emittent = emittenten.emittent AND emittentPerFonds.vermogensbeheerder = '$beheerder'
WHERE
TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille='".$this->portefeuille."'
".$__appvar['TijdelijkeRapportageMaakUniek']." ".$this->rapportFilter." 
GROUP BY
TijdelijkeRapportage.fonds,TijdelijkeRapportage.rekening
ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde,TijdelijkeRapportage.fondsOmschrijving,TijdelijkeRapportage.rekening";

	debugSpecial($query,__FILE__,__LINE__);
    $this->db=new DB();
    $DB->SQL($query);
		$DB->Query();
    $durationChart=array('0-3'=>0,'3-5'=>0,'5-7'=>0,'7-10'=>0,'>10'=>0,'overig'=>0);
    $data['duration']['pieData']=$durationChart;
		$data['duration']['data']=array('0-3'=>array('Omschrijving'=>'0-3'),'3-5'=>array('Omschrijving'=>'3-5'),'5-7'=>array('Omschrijving'=>'5-7'),'7-10'=>array('Omschrijving'=>'7-10'),'>10'=>array('Omschrijving'=>'>10'),'overig'=>array('Omschrijving'=>'overig'));
    $kleuren=array('0-3'=>array(2,40,54),'3-5'=>array(58,89,23),'5-7'=>array(159,227,151),'7-10'=>array(173,160,122),'>10'=>array(145,130,85),'overig'=>array(5,120,160));
		foreach($kleuren as $periode=>$kleur)
		{
			$data['duration']['kleurData'][$periode]['R']['value']=$kleur[0];
			$data['duration']['kleurData'][$periode]['G']['value']=$kleur[1];
			$data['duration']['kleurData'][$periode]['B']['value']=$kleur[2];
    }
    
    $actueleWaardePortefeuille=0;
		while ($cat=$DB->nextRecord())
		{
			$rente=getRenteParameters($cat['fonds'], $this->rapportageDatum);
			foreach($rente as $key=>$value)
				$cat[$key]=$value;

     $actueleWaardePortefeuille+=$cat['actuelePortefeuilleWaardeEuro'];
     if($cat['Lossingsdatum'] <> '')
        $lossingsJul = adodb_db2jul($cat['Lossingsdatum']);
     else
        $lossingsJul=0;
     $rentedatumJul = adodb_db2jul($cat['Rentedatum']);
     $renteVanafJul = adodb_db2jul(jul2sql($this->pdf->rapport_datum));

    // $q = "SELECT Datum, Rentepercentage FROM Rentepercentages WHERE Fonds = '".$cat['fonds']."' ORDER BY Datum DESC LIMIT 1";
  		$koers=getRentePercentage($cat['fonds'],$this->rapportageDatum);

			$renteDag=0;
			  if($cat['variabeleCoupon'] == 1)
			  {
			    $rapportJul=adodb_db2jul($this->rapportageDatum);
			    $renteJul=adodb_db2jul($cat['Rentedatum']);
          $renteStap=($cat['Renteperiode']/12)*31556925.96;
          $renteDag=$renteJul;
          if($renteStap > 100000)
            while($renteDag<$rapportJul)
            {
              $renteDag+=$renteStap;
            }
			  }

       $ytm=0;
       $duration=0;
       $modifiedDuration=0;
       
       $aandeel=$cat['actuelePortefeuilleWaardeEuro']/$totaalWaarde*100;

        if($lossingsJul > 0)
	      {

	        //$this->huidigeWaardeTotaal += $fonds['actuelePortefeuilleWaardeEuro'];
	        //$this->lossingsWaardeTotaal += $fonds['totaalAantal'] * 100 * $fonds['fondsEenheid'] * $fonds['actueleValuta'];
		  	  $jaar = ($lossingsJul-$renteVanafJul)/31556925.96;

		  	  $p = $cat['actueleFonds'];
	        $r = $koers['Rentepercentage']/100;
	        $b = $this->cashfow->fondsDataKeyed[$cat['fonds']]['lossingskoers'];
	        $year = $jaar;

	        $ytm=  $this->cashfow->bondYTM($p,$r,$b,$year)*100;
	        $restLooptijd=($lossingsJul-$this->pdf->rapport_datum)/31556925.96;

	         $duration=$this->cashfow->waardePerFonds[$cat['fonds']]['ActueelWaardeJaar']/$this->cashfow->waardePerFonds[$cat['fonds']]['ActueelWaarde'];
	         if($cat['variabeleCoupon'] == 1 && $renteDag <> 0)
	           $modifiedDuration=($renteDag-db2jul($this->rapportageDatum))/86400/365;
	         else
	           $modifiedDuration=$duration/(1+$ytm/100);
	         

           $totalen['yield']+=$koers['Rentepercentage']*$cat['totaalAantal']/$cat['actuelePortefeuilleWaardeEuro']*$cat['actueleValuta']*$aandeel;
	         $totalen['ytm']+=$ytm*$aandeel;
	         $totalen['duration']+=$duration*$aandeel;
	         $totalen['modifiedDuration']+=$modifiedDuration*$aandeel;
	         $totalen['restLooptijd']+=$restLooptijd*$aandeel;
           
           
           if($duration<3)
             $bin='0-3';// $durationChart['0-3']+=$aandeel;
           elseif($duration<5)
             $bin='3-5';//$durationChart['3-5']+=$aandeel;
           elseif($duration<7)
             $bin='5-7';//$durationChart['5-7']+=$aandeel;
           elseif($duration<10)
             $bin='7-10';//$durationChart['7-10']+=$aandeel;
           else
             $bin='>10';//$durationChart['>10']+=$aandeel;
             
 
	      }
        else
        {
          $bin='overig';
        }
        $durationChart[$bin]+=$aandeel;

	      $data['duration']['data'][$bin]['waardeEur']+=$cat['actuelePortefeuilleWaardeEuro'];
	      $data['duration']['data'][$bin]['Omschrijving']=$bin;        
        $data['duration']['pieData'][$bin]+=$aandeel;
	      //$data['duration']['kleurData'][$bin]['R']['value']=$kleuren[$bin][0];
        //$data['duration']['kleurData'][$bin]['G']['value']=$kleuren[$bin][1];
        //$data['duration']['kleurData'][$bin]['B']['value']=$kleuren[$bin][2];
	      $data['duration']['kleurData'][$bin]['percentage']+=$aandeel;
        
    }
    //listarray($durationChart);
    //  listarray($data);
  /*
	while($cat = $DB->nextRecord())
	{
	   $data['duration']['data'][$cat['beleggingssector']]['waardeEur']=$cat['WaardeEuro'];
	   $data['duration']['data'][$cat['beleggingssector']]['Omschrijving']=$cat['Omschrijving'];
	   $data['duration']['pieData'][$cat['Omschrijving']]+= $cat['WaardeEuro']/$totaalWaarde;
	   $data['duration']['kleurData'][$cat['Omschrijving']]=$allekleuren['Rating'][$cat['beleggingssector']];
	   $data['duration']['kleurData'][$cat['Omschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaarde*100;
	}
*/

$this->pdf->setXY(30,35);
//$this->pdf->setXY(65,40);
$this->printPie($data['valuta']['pieData'],$data['valuta']['kleurData'],'Valutaverdeling '.date("d-m-Y",db2jul($rapportageDatum)),60,50);
$this->pdf->wLegend=0;
$this->pdf->setXY(120,35);
//$this->pdf->setXY(175,40);
$this->printPie($data['rating']['pieData'],$data['rating']['kleurData'],'Ratingverdeling '.date("d-m-Y",db2jul($rapportageDatum)),60,50);
$this->pdf->wLegend=0;

$this->pdf->setXY(210,35);
$this->printPie($data['duration']['pieData'],$data['duration']['kleurData'],'Durationverdeling '.date("d-m-Y",db2jul($rapportageDatum)),60,50);

foreach ($data as $type=>$typeData)
{
  $n=0;
  foreach ($typeData['data'] as $categorie=>$gegevens)
  {
    if(!is_array($regelData[$n]))
      $regelData[$n]=array('','','','','','','','','','');
    if($type=='valuta')
      $offset=0;
    if($type=='rating')
      $offset=4;
    if($type=='duration')
      $offset=8;

     $regelData[$n][0]='';
     $regelData[$n][1+$offset]=$gegevens['Omschrijving'];
     $regelData[$n][2+$offset]=$this->formatGetal($gegevens['waardeEur'],0);
     $regelData[$n][3+$offset]=$this->formatGetal($data[$type]['kleurData'][$gegevens['Omschrijving']]['percentage'],1).'%';
     $regelData[$n][4+$offset]='';
     $n++;

     $regelTotaal[$type]['waardeEur']+=$gegevens['waardeEur'];
     $regelTotaal[$type]['percentage']+=round($data[$type]['kleurData'][$gegevens['Omschrijving']]['percentage'],2);
  }

}


foreach ($regelData as $regelNr=>$regel)
{
  ksort($regel);
  $regelData[$regelNr]=$regel;
}

$this->pdf->setXY($this->pdf->marge,130);
$this->pdf->SetWidths(array(5, 50,20,15, 8, 50,20,15, 8, 50,20,15));
//$this->pdf->SetWidths(array(45, 40,20,15, 40, 40,20,15, 15));
$this->pdf->SetAligns(array('L', 'L','R','R',  'L',  'L','R','R',  'L',  'L','R','R'));



//
$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
$this->pdf->CellBorders = array();
$this->pdf->ln(2);

$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize-0.5);
foreach ($regelData as $regel)
{
  $this->pdf->row($regel);
}

$this->pdf->underlinePercentage=0.8;
$this->pdf->CellBorders = array('','','TS','TS','','','TS','TS','','','TS','TS');
$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize-0.5);
$this->pdf->row(array('','Totaal '.date("d-m-Y",db2jul($rapportageDatum)), $this->formatGetal($regelTotaal['valuta']['waardeEur']),$this->formatGetal($regelTotaal['valuta']['percentage'],1).'%','',
'Totaal '.date("d-m-Y",db2jul($rapportageDatum)), $this->formatGetal($regelTotaal['rating']['waardeEur']),$this->formatGetal($regelTotaal['rating']['percentage'],1).'%'
,'','Totaal '.date("d-m-Y",db2jul($rapportageDatum)), $this->formatGetal($regelTotaal['duration']['waardeEur']),$this->formatGetal($regelTotaal['duration']['percentage'],1).'%'
));
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
unset($this->pdf->CellBorders);
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
//listarray($kleurdata);
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
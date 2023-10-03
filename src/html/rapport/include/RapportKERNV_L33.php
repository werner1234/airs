<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/05/26 16:45:07 $
File Versie					: $Revision: 1.10 $

$Log: RapportKERNV_L33.php,v $
Revision 1.10  2017/05/26 16:45:07  rvv
*** empty log message ***

Revision 1.9  2016/10/29 15:40:53  rvv
*** empty log message ***

Revision 1.8  2016/10/26 16:13:40  rvv
*** empty log message ***

Revision 1.7  2013/07/17 15:53:14  rvv
*** empty log message ***

Revision 1.6  2013/04/27 16:29:28  rvv
*** empty log message ***

Revision 1.5  2013/04/24 13:22:02  rvv
*** empty log message ***

Revision 1.4  2013/04/20 16:34:57  rvv
*** empty log message ***

Revision 1.3  2013/04/06 16:16:31  rvv
*** empty log message ***

Revision 1.2  2013/04/03 14:58:34  rvv
*** empty log message ***

Revision 1.1  2013/03/23 16:19:36  rvv
*** empty log message ***

Revision 1.32  2013/03/06 16:59:51  rvv
*** empty log message ***

Revision 1.31  2013/03/03 10:34:49  rvv
*** empty log message ***

Revision 1.30  2013/02/27 17:04:41  rvv
*** empty log message ***

Revision 1.29  2012/12/30 14:27:11  rvv
*** empty log message ***

Revision 1.28  2012/09/05 18:19:11  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");

class RapportKERNV_L33
{
	function RapportKERNV_L33($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_startDatum = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
	  $this->vastRentend=true;

    $this->db=new DB();  
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
	    $this->cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		  $this->cashfow->genereerTransacties();
		  $this->cashfow->genereerRows();
      $this->pdf->rapport_titel = vertaalTekst("Kerngegevens obligatieportefeuille in",$this->pdf->rapport_taal)." ".$this->pdf->rapportageValuta;
      $this->vastWhere=" AND ( TijdelijkeRapportage.hoofdcategorie='G-RISM' OR Fondsen.Lossingsdatum <> '0000-00-00') AND TijdelijkeRapportage.Type <> 'rekening'";
      $this->cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		  $this->cashfow->genereerTransacties();
		  $this->cashfow->genereerRows();
		  $this->rapport($this->vastRentend);
	}
  
  function rapportZakelijk()
  {
    global $__appvar;
    $this->pdf->AddPage();
    $DB=new DB();
     $this->vastWhere=" AND TijdelijkeRapportage.hoofdcategorie='G-RISD'";
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal, rapportageDatum ".
					 "FROM TijdelijkeRapportage 
           WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' "
					 .$__appvar['TijdelijkeRapportageMaakUniek'].$this->vastWhere." GROUP BY rapportageDatum";
	  debugSpecial($query,__FILE__,__LINE__);
  	$DB->SQL($query); //echo $query."<br>\n";
  	$DB->Query();
  	while($totaal = $DB->nextRecord())
    {
  	  $totaalWaarde[$totaal['rapportageDatum']]=$totaal['totaal'];
    }
    
    $rapportageDatum = $this->rapportageDatum;
  	$query="SELECT beleggingssector,beleggingssectorOmschrijving,sum(actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro
    FROM TijdelijkeRapportage 
    WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'".
    $__appvar['TijdelijkeRapportageMaakUniek']. $this->vastWhere." 
    GROUP BY beleggingssector ORDER BY beleggingssectorVolgorde";
	  $DB->SQL($query); 
	  $DB->Query();
	 	while($fonds = $DB->nextRecord())
  	{ 
      if($fonds['beleggingssector']=='')
        $fonds['beleggingssector']='Geen';
      if($fonds['beleggingssectorOmschrijving']=='')
        $fonds['beleggingssectorOmschrijving']='Geen';  
     // $sectorOmschrijving[$fonds['beleggingssector']]=$fonds['beleggingssectorOmschrijving'];
     // $sectorverdeling[$fonds['beleggingssectorOmschrijving']]=$fonds['actuelePortefeuilleWaardeEuro']/$totaalWaarde[$this->rapportageDatum];
      
      $data['sectorverdeling']['kleurData'][$fonds['beleggingssectorOmschrijving']]=$this->allekleuren['OIS'][$fonds['beleggingssector']];
      $data['sectorverdeling']['kleurData'][$fonds['beleggingssectorOmschrijving']]['percentage']=$fonds['actuelePortefeuilleWaardeEuro']/$totaalWaarde[$this->rapportageDatum]*100;
	  }  

  	$query="SELECT regio,regioOmschrijving,sum(actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro
    FROM TijdelijkeRapportage 
    WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'".
    $__appvar['TijdelijkeRapportageMaakUniek']. $this->vastWhere." 
    GROUP BY regio ORDER BY regioVolgorde";
	  $DB->SQL($query); 
	  $DB->Query();
	 	while($fonds = $DB->nextRecord())
  	{ 
      if($fonds['regio']=='')
        $fonds['regio']='Geen';
      if($fonds['regioOmschrijving']=='')
        $fonds['regioOmschrijving']='Geen';  
     // $regioOmschrijving[$fonds['regio']]=$fonds['regioOmschrijving'];
     // $regioverdeling[$fonds['regioOmschrijving']]=$fonds['actuelePortefeuilleWaardeEuro']/$totaalWaarde[$this->rapportageDatum];
      $data['regioverdeling']['kleurData'][$fonds['regioOmschrijving']]=$this->allekleuren['OIR'][$fonds['regio']];
      $data['regioverdeling']['kleurData'][$fonds['regioOmschrijving']]['percentage']=$fonds['actuelePortefeuilleWaardeEuro']/$totaalWaarde[$this->rapportageDatum]*100;
	  
     
	  } 
  //listarray($this->allekleuren);
//$this->allekleuren
/*
foreach ($fondsRating as $rating=>$waarde)
{
  if($waarde <> 0)
  {
    $data['fondsRating']['kleurData'][$rating]=$this->allekleuren['Rating'][$rating];
    $data['fondsRating']['kleurData'][$rating]['percentage']=$waarde/$totaalWaarde[$rapportageDatum]*100;
  }
}
foreach ($emittentRating as $rating=>$waarde)
{
  if($waarde <> 0)
  {
    $data['emittentRating']['kleurData'][$rating]=$this->allekleuren['Rating'][$rating];
    $data['emittentRating']['kleurData'][$rating]['percentage']=$waarde/$totaalWaarde[$rapportageDatum]*100;
  }
}
*/
 // listarray($this->allekleuren['Rating']);

$this->pdf->setXY(20,66);
$this->printPie($data['fondsRating']['pieData'],$data['sectorverdeling']['kleurData'], vertaalTekst("Sectorverdeling",$this->pdf->rapport_taal),50,40,$hcat);
$this->pdf->setXY(170,66);
$this->printPie($data['emittentRating']['pieData'],$data['regioverdeling']['kleurData'], vertaalTekst("Regioverdeling",$this->pdf->rapport_taal),50,40,$hcat);


  }

 
  
	function rapport($vastrentend=false)
	{
		global $__appvar;
		$query = "SELECT Portefeuilles.startDatum,Portefeuilles.startdatumMeerjarenrendement, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
		$this->pdf->AddPage();
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
		$rapportageDatum = $this->rapportageDatum;
		if($this->pdf->rapport_startDatum <= db2jul($portefeuilledata['startDatum']))
		  $rapportageStartJaar=substr($portefeuilledata['startDatum'],0,10);
	  else
	   	$rapportageStartJaar= date("Y-01-01",$this->pdf->rapport_datum);

		$rapportageDatumVanaf = $this->rapportageDatumVanaf;
	  $portefeuille = $this->portefeuille;

	  $startDatumTekst=date("j",$this->pdf->rapport_datumvanaf)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datumvanaf);
    $rapDatumTekst=date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum);
    $startJaarDatumTekst=date("j",db2jul($rapportageStartJaar))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($rapportageStartJaar))],$this->pdf->rapport_taal)." ".date("Y",db2jul($rapportageStartJaar));


//    if ($this->pdf->rapportageValuta != "EUR" )
//	    $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= TijdelijkeRapportage.rapportageDatum ORDER BY Datum DESC LIMIT 1 ) ";
//	  else
	    $koersQuery = "";


  	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) $koersQuery AS totaal, rapportageDatum ".
					 "FROM TijdelijkeRapportage
					 Left Join Fondsen ON Fondsen.Fonds = TijdelijkeRapportage.Fonds WHERE ".
					 " TijdelijkeRapportage.portefeuille = '".$portefeuille."' "
					 .$__appvar['TijdelijkeRapportageMaakUniek'].$this->vastWhere." GROUP BY rapportageDatum";
	  debugSpecial($query,__FILE__,__LINE__);
  	$DB->SQL($query); //echo $query;exit;
  	$DB->Query();
  	while($totaal = $DB->nextRecord())
  	{
  	/*
  	  if($totaal['rapportageDatum']==$this->rapportageDatum)
  	    $totaal['totaal']=$totaal['totaal']/$this->pdf->ValutaKoersEind;
  	  if($totaal['rapportageDatum']==$rapportageStartJaar)
  	    $totaal['totaal']=$totaal['totaal']/$this->pdf->ValutaKoersStart;
  	  if($totaal['rapportageDatum']==$this->rapportageDatumVanaf)
  	    $totaal['totaal']=$totaal['totaal']/$this->pdf->ValutaKoersBegin;
*/
    if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta <> '')
	    $koers =	getValutaKoers($this->pdf->rapportageValuta,$totaal['rapportageDatum']);
	  else
	    $koers = 1;

  	  $totaalWaarde[$totaal['rapportageDatum']]=$totaal['totaal']/$koers;
  	}



    $this->pdf->SetWidths(array(60, 45,10,45));

	//Kleuren instellen
	$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
	$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
	$DB = new DB();
	$DB->SQL($q);
	$DB->Query();
	$kleuren = $DB->LookupRecord();
	$this->allekleuren = unserialize($kleuren['grafiek_kleur']);

  if($vastrentend)
	{

	  $query="SELECT Rating.rating FROM Rating ORDER BY Rating.Afdrukvolgorde";
	  $DB->SQL($query);
	  $DB->Query();
	 	while($fonds = $DB->nextRecord())
  	{
      $fondsRating[$fonds['rating']]=0;
      $emittentRating[$fonds['rating']]=0;
	  }

	  $query="SELECT
  	TijdelijkeRapportage.fonds,
  	TijdelijkeRapportage.fondsOmschrijving,
SUM(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro / 1) AS WaardeEuro,
TijdelijkeRapportage.beleggingscategorie AS categorie,
TijdelijkeRapportage.actueleFonds,
TijdelijkeRapportage.hoofdcategorie AS Hcategorie,
TijdelijkeRapportage.beleggingscategorieOmschrijving as categorieOmschrijving,
TijdelijkeRapportage.hoofdcategorieOmschrijving as HcategorieOmschrijving,
(TijdelijkeRapportage.beleggingscategorieVolgorde) AS volgorde,
Fondsen.rating as fondsRating,
Fondsen.Lossingsdatum,
Fondsen.Rentedatum,
Fondsen.Renteperiode,
Fondsen.variabeleCoupon,
emittentPerFonds.emittent,
TijdelijkeRapportage.type,
emittenten.rating as emittentRating
FROM TijdelijkeRapportage
Left Join Fondsen ON Fondsen.Fonds = TijdelijkeRapportage.Fonds
Left Join emittentPerFonds ON emittentPerFonds.Fonds = TijdelijkeRapportage.Fonds  AND emittentPerFonds.vermogensbeheerder='$beheerder'
LEFT Join emittenten ON emittentPerFonds.emittent = emittenten.emittent AND emittentPerFonds.vermogensbeheerder = '$beheerder'
WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'"
	.$__appvar['TijdelijkeRapportageMaakUniek'].$this->vastWhere."
	GROUP BY TijdelijkeRapportage.fonds,TijdelijkeRapportage.rekening";

	$DB->SQL($query);
	$DB->Query();

	while($fonds = $DB->nextRecord())
	{
		$rente=getRenteParameters($fonds['fonds'], $this->rapportageDatum);
		foreach($rente as $key=>$value)
			$fonds[$key]=$value;

	  if($fonds['fondsRating']=='')
	    $fonds['fondsRating']='geen';
	  if($fonds['emittentRating']=='')
	    $fonds['emittentRating']='geen';
    $fondsRating[$fonds['fondsRating']] += $fonds['WaardeEuro'];
    $emittentRating[$fonds['emittentRating']] += $fonds['WaardeEuro'];

     if($fonds['Lossingsdatum'] <> '')
       $lossingsJul = adodb_db2jul($fonds['Lossingsdatum']);
      else
       $lossingsJul=0;
     $rentedatumJul = adodb_db2jul($fonds['Rentedatum']);
     $renteVanafJul = adodb_db2jul(jul2sql($this->pdf->rapport_datum));

     $ytm=0;
$duration=0;
$modifiedDuration=0;
     if($lossingsJul > 0 && $fonds['type'] <> 'rekening' )
	   {
	     //$q = "SELECT Datum, Rentepercentage FROM Rentepercentages WHERE Fonds = '".$fonds['fonds']."' ORDER BY Datum DESC LIMIT 1";
			 $q = "SELECT Datum, Rentepercentage FROM Rentepercentages WHERE GeldigVanaf < '$this->rapportageDatum' AND Fonds = '".$fonds['fonds']."' ORDER BY Datum ASC, GeldigVanaf DESC LIMIT 1";
			 $koers=getRentePercentage($fonds['fonds'],$this->rapportageDatum);

			 $renteDag=0;
			 if($fonds['variabeleCoupon'] == 1)
			 {
			    $rapportJul=adodb_db2jul($this->rapportageDatum);
			    $renteJul=adodb_db2jul($fonds['Rentedatum']);
          $renteStap=($fonds['Renteperiode']/12)*31556925.96;
          $renteDag=$renteJul;
          if($renteStap > 100000)
            while($renteDag<$rapportJul)
            {
              $renteDag+=$renteStap;
            }
			 }

		   $jaar = ($lossingsJul-$renteVanafJul)/31556925.96;

		   $p = $fonds['actueleFonds'];
	     $r = $koers['Rentepercentage']/100;
	     $b = $this->cashfow->fondsDataKeyed[$fonds['fonds']]['lossingskoers'];
	     $y = $jaar;

	     $ytm=  $this->cashfow->bondYTM($p,$r,$b,$y)*100;
      // echo $fonds['fonds']." $ytm=  $this->cashfow->bondYTM($p,$r,$b,$y)*100; <br>\n";
       
	     $restLooptijd=($lossingsJul-$this->pdf->rapport_datum)/31556925.96;

	     $duration=$this->cashfow->waardePerFonds[$fonds['fonds']]['ActueelWaardeJaar']/$this->cashfow->waardePerFonds[$fonds['fonds']]['ActueelWaarde'];
       
       
       if($fonds['variabeleCoupon'] == 1 && $renteDag <> 0)
	       $modifiedDuration=($renteDag-db2jul($this->rapportageDatum))/86400/365;
	     else
	       $modifiedDuration=$duration/(1+$ytm/100);
         
   //   echo round($modifiedDuration,4)." ".$fonds['fonds']."<br>\n";
             
	     $aandeel=$fonds['WaardeEuro']/$totaalWaarde[$rapportageDatum];
	     //echo "".$fonds['fonds']." $ytm ".$totaalWaarde[$rapportageDatum]."<br>\n";
	     $totalen['yield']+=$koers['Rentepercentage']*$aandeel;
	     $totalen['ytm']+=$ytm*$aandeel;
	     $totalen['duration']+=$duration*$aandeel;
	     $totalen['modifiedDuration']+=$modifiedDuration*$aandeel;
	     $totalen['restLooptijd']+=$restLooptijd*$aandeel;
     }
	}


	//asort($fondsRating,SORT_NUMERIC);
	//asort($emittentRating,SORT_NUMERIC);


foreach ($fondsRating as $rating=>$waarde)
{
  if($waarde <> 0)
  {
    $data['fondsRating']['kleurData'][$rating]=$this->allekleuren['Rating'][$rating];
    $data['fondsRating']['kleurData'][$rating]['percentage']=$waarde/$totaalWaarde[$rapportageDatum]*100;
  }
}
foreach ($emittentRating as $rating=>$waarde)
{
  if($waarde <> 0)
  {
    $data['emittentRating']['kleurData'][$rating]=$this->allekleuren['Rating'][$rating];
    $data['emittentRating']['kleurData'][$rating]['percentage']=$waarde/$totaalWaarde[$rapportageDatum]*100;
  }
}

 // listarray($this->allekleuren['Rating']);

$this->pdf->setXY(20,66);
$this->printPie($data['fondsRating']['pieData'],$data['fondsRating']['kleurData'], vertaalTekst("Rating op instrument-niveau",$this->pdf->rapport_taal),50,40,$hcat);
$this->pdf->setXY(170,66);
$this->printPie($data['emittentRating']['pieData'],$data['emittentRating']['kleurData'], vertaalTekst("Rating op uitgevende-instelling",$this->pdf->rapport_taal),50,40,$hcat);
 $rapjaar=date('Y',$this->pdf->rapport_datum);

		//$cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		//$cashfow->genereerTransacties();
		//$regels = $cashfow->genereerRows();
      foreach ($this->cashfow->gegevens['jaar'] as $jaar => $waarden)
      {
	
				if ($jaar > ($rapjaar + 10))
				{
					$jaar = 'Overig';
				}
				
        //if($jaar <= $rapjaar+20)
        //
           $jaarTotalen[$jaar]['lossing']+=$waarden['lossing'];
          $jaarTotalen[$jaar]['rente']+=$waarden['rente'];
        //}
				
				
      }
//'Macaulay duration'=>$this->formatGetal($totalen['duration'],2),
		$this->pdf->setXY(20,140);
		$this->pdf->ln();
		$this->pdf->SetWidths(array(20,50,43));
		$this->pdf->SetAligns(array('L','L','R'));
		$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);

		$this->pdf->row(array('',vertaalTekst("Karakteristieken risicomijdende portefeuille",$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);


		$cashOptions=array(vertaalTekst("Gemiddelde coupon-yield",$this->pdf->rapport_taal)=>$this->formatGetal($totalen['yield'],3),
		                   vertaalTekst("Gemiddelde YTM",$this->pdf->rapport_taal)=>$this->formatGetal($totalen['ytm'],2),
		                   vertaalTekst("Modified duration",$this->pdf->rapport_taal)=>$this->formatGetal($totalen['modifiedDuration'],2),
		                   vertaalTekst('Resterende looptijd',$this->pdf->rapport_taal)=>$this->formatGetal($totalen['restLooptijd'],2));
		foreach ($cashOptions as $option=>$waarde)
		  $this->pdf->row(array('',$option,$waarde));

		  $this->pdf->setXY(160,190);
		  $this->VBarDiagram(160,60,$jaarTotalen);


	}
	else
	{
$query="SELECT
Sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS WaardeEuro,
TijdelijkeRapportage.beleggingscategorie as categorie,
CategorienPerHoofdcategorie.Hoofdcategorie  as Hcategorie,
Beleggingscategorien.Omschrijving AS categorieOmschrijving,
hoofdcategorien.Omschrijving AS HcategorieOmschrijving,
max(Beleggingscategorien.Afdrukvolgorde ) AS volgorde
FROM
TijdelijkeRapportage
Left Join Beleggingscategorien ON TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT Join CategorienPerHoofdcategorie ON TijdelijkeRapportage.beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.vermogensbeheerder='$beheerder'
LEFT Join Beleggingscategorien  as hoofdcategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = hoofdcategorien.Beleggingscategorie
	WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'"
	.$__appvar['TijdelijkeRapportageMaakUniek'].$this->vastWhere.
	" GROUP BY categorie
ORDER BY volgorde";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	while($cat = $DB->nextRecord())
	{
	  $categorien[$cat['HcategorieOmschrijving']][$cat['volgorde']]=$cat['categorie'];
	  $data['omschrijving'][$cat['categorie']]=$cat['categorieOmschrijving'];
	   $data['categorieEind']['data'][$cat['categorie']]['waardeEur']=$cat['WaardeEuro'];
	   $data['categorieEind']['data'][$cat['categorie']]['Omschrijving']=$cat['categorieOmschrijving'];
	  // $data['categorieEind']['pieData'][$cat['categorieOmschrijving']]= $cat['WaardeEuro']/$totaalWaarde[$this->rapportageDatum];
	  // $data['categorieEind']['kleurData'][$cat['categorieOmschrijving']]=$this->allekleuren['OIB'][$cat['categorie']];
	  // $data['categorieEind']['kleurData'][$cat['categorieOmschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaarde[$this->rapportageDatum]*100;
	}
	$query="SELECT
Sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin."  AS WaardeEuro,
TijdelijkeRapportage.beleggingscategorie as categorie,
CategorienPerHoofdcategorie.Hoofdcategorie  as Hcategorie,
Beleggingscategorien.Omschrijving AS categorieOmschrijving,
hoofdcategorien.Omschrijving AS HcategorieOmschrijving,
max(Beleggingscategorien.Afdrukvolgorde ) AS volgorde
FROM
TijdelijkeRapportage
Left Join Beleggingscategorien ON TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT Join CategorienPerHoofdcategorie ON TijdelijkeRapportage.beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.vermogensbeheerder='DOO'
LEFT Join Beleggingscategorien  as hoofdcategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = hoofdcategorien.Beleggingscategorie
	WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatumVanaf."'"
	.$__appvar['TijdelijkeRapportageMaakUniek'].$this->vastWhere.
	" GROUP BY categorie
ORDER BY volgorde";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	while($cat = $DB->nextRecord())
	{
	  $categorien[$cat['HcategorieOmschrijving']][$cat['volgorde']]=$cat['categorie'];
	   $data['omschrijving'][$cat['categorie']]=$cat['categorieOmschrijving'];
	   $data['categorieBegin']['data'][$cat['categorie']]['waardeEur']=$cat['WaardeEuro'];
	   $data['categorieBegin']['data'][$cat['categorie']]['Omschrijving']=$cat['categorieOmschrijving'];
	   $data['categorieBegin']['pieData'][$cat['categorieOmschrijving']]= $cat['WaardeEuro']/$totaalWaarde[$this->rapportageDatumVanaf];
	   $data['categorieBegin']['kleurData'][$cat['categorieOmschrijving']]=$this->allekleuren['OIB'][$cat['categorie']];
	   $data['categorieBegin']['kleurData'][$cat['categorieOmschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaarde[$this->rapportageDatumVanaf]*100;
	}
    $categorieVolgorde=array('Risicodragend'=>array('AAND'=>'Aandelen','ALTERN'=>'Alternatieven'),
                             'Risicomijdend'=>array('OBL-ST'=>'Staatsobligaties','OBL-FI'=>'Bedrijfsobligaties','Liquiditeiten'=>'Liquiditeiten'));
foreach ($categorieVolgorde as $Hcategorie=>$subCatData)
{
  foreach ($subCatData as $categorie=>$categorieOmschrijving)
  {

 	   $data['categorieEind']['pieData'][$categorieOmschrijving]= $data['categorieEind']['data'][$categorie]['waardeEur']/$totaalWaarde[$this->rapportageDatum];
 	   $data['categorieEind']['kleurData'][$categorieOmschrijving]=$this->allekleuren['OIB'][$categorie];
	   $data['categorieEind']['kleurData'][$categorieOmschrijving]['percentage']=$data['categorieEind']['data'][$categorie]['waardeEur']/$totaalWaarde[$this->rapportageDatum]*100;
	   $hcatValues[$Hcategorie]+=$data['categorieEind']['kleurData'][$categorieOmschrijving]['percentage'];

  }
  $n++;
}

$n=0;
foreach ($categorieVolgorde as $Hcategorie=>$subCatData)
{
  foreach ($subCatData as $categorie=>$categorieOmschrijving)
  {
    $hcat[$n]=vertaalTekst($Hcategorie,$this->pdf->rapport_taal)." (".$this->formatGetal($hcatValues[$Hcategorie],1)."%)";
     $n++;
  }
}

$julRapdatum=db2jul($rapportageDatum);
$this->pdf->wLegend=0;
$this->pdf->setXY(170,66);

$this->printPie($data['categorieEind']['pieData'],$data['categorieEind']['kleurData'],vertaalTekst('Opbouw vermogen per',$this->pdf->rapport_taal).' '.date("d ",$julRapdatum).vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$julRapdatum)],$this->pdf->rapport_taal).date(" Y",$julRapdatum),50,40,$hcat);
$this->pdf->wLegend=0;

$this->pdf->setXY(65,60);
$this->pdf->ln();
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
$this->pdf->row(array(vertaalTekst('Opbouw vermogen',$this->pdf->rapport_taal)));
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);

$this->pdf->SetAligns(array('L', 'R','L','R'));
$this->pdf->SetWidths(array(40, 45,10,45));
$this->pdf->row(array('',$startDatumTekst,'',$rapDatumTekst));
$this->pdf->SetWidths(array(30, 30,6,19, 30,7,18));
$this->pdf->SetAligns(array('L', 'R','L','R', 'R','L','R'));

$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
$hcat=array();

//listarray($categorien);exit;


//foreach ($categorien as $Hcategorie=>$categorieData)
foreach ($categorieVolgorde as $Hcategorie=>$subCatData)
{
  if($Hcategorie <> $lastHcategorie)
  {
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst($Hcategorie,$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
 //foreach ($categorieData as $categorie)
 foreach ($subCatData as $categorie=>$categorieOmschrijving)
 {

  $this->pdf->row(array('    '.vertaalTekst($categorieOmschrijving,$this->pdf->rapport_taal),
                        $this->formatGetal($data['categorieBegin']['data'][$categorie]['waardeEur'],0),'',
                        $this->formatGetal($data['categorieBegin']['pieData'][$data['omschrijving'][$categorie]]*100,1)."%",
                        $this->formatGetal($data['categorieEind']['data'][$categorie]['waardeEur'],0),'',
                        $this->formatGetal($data['categorieEind']['pieData'][$data['omschrijving'][$categorie]]*100,1)."%"));
  $totalen['categorieBegin']+=$data['categorieBegin']['data'][$categorie]['waardeEur'];
  $totalen['categorieBeginProcent']+=$data['categorieBegin']['pieData'][$data['omschrijving'][$categorie]]*100;
  $totalen['categorieEind']+=$data['categorieEind']['data'][$categorie]['waardeEur'];
  $totalen['categorieEindProcent']+=$data['categorieEind']['pieData'][$data['omschrijving'][$categorie]]*100;
 }
}
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
$this->pdf->CellBorders = array('T','T','T','T','T','T','T');
  $this->pdf->row(array('',
                        $this->formatGetal($totalen['categorieBegin'],0),'(a)',
                        $this->formatGetal($totalen['categorieBeginProcent'],1)."%",
                        $this->formatGetal($totalen['categorieEind'],0),'(b)',
                        $this->formatGetal($totalen['categorieEindProcent'],1)."%"
                        ));
 $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
unset($this->pdf->CellBorders);

  if ($this->pdf->rapportageValuta != "EUR")
	  $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	else
	  $koersQuery = "";


	$query = "SELECT SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery )  AS totaalcredit, ".
		  "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery )  AS totaaldebet ".
		  "FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen ".
		  "WHERE ".
		  "Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		  "Rekeningen.Portefeuille = '$portefeuille' AND ".
		  "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  "Rekeningmutaties.Verwerkt = '1' AND ".
		  "Rekeningmutaties.Boekdatum > '$rapportageStartJaar' AND ".
		  "Rekeningmutaties.Boekdatum <= '$rapportageDatum' AND ".
		  "Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND ".
		  "(Grootboekrekeningen.Kosten = '1' or Grootboekrekeningen.Opbrengst = '1' ) ";
 	$DB->SQL($query);
	$grootboekJaar = $DB->lookupRecord();
	$query = "SELECT SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery ) AS totaalcredit, ".
		  "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery ) AS totaaldebet ".
		  "FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen ".
		  "WHERE ".
		  "Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		  "Rekeningen.Portefeuille = '$portefeuille' AND ".
		  "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  "Rekeningmutaties.Verwerkt = '1' AND ".
		  "Rekeningmutaties.Boekdatum > '$rapportageDatumVanaf' AND ".
		  "Rekeningmutaties.Boekdatum <= '$rapportageDatum' AND ".
		  "Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND ".
		  "(Grootboekrekeningen.Kosten = '1' or Grootboekrekeningen.Opbrengst = '1' ) ";
 	$DB->SQL($query);
	$grootboekPeriode = $DB->lookupRecord();

	$data['mutatieRapPeriode']=$totaalWaarde[$rapportageDatum]-$totaalWaarde[$rapportageDatumVanaf];
$data['mutatieJaar']=$totaalWaarde[$rapportageDatum]-$totaalWaarde[$rapportageStartJaar];
$data['stortingRapPeriode']=getStortingen($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$this->pdf->rapportageValuta);
$data['onttrekkingRapPeriode']=getOnttrekkingen($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$this->pdf->rapportageValuta)*-1;
$data['stortingJaar']=getStortingen($portefeuille,$rapportageStartJaar,$rapportageDatum,$this->pdf->rapportageValuta);
$data['onttrekkingJaar']=getOnttrekkingen($portefeuille,$rapportageStartJaar,$rapportageDatum,$this->pdf->rapportageValuta)*-1;
$data['resultaatRapPeriode']=$data['mutatieRapPeriode']-$data['stortingRapPeriode']-$data['onttrekkingRapPeriode'];
$data['resultaatJaar']=$data['mutatieJaar']-$data['stortingJaar']-$data['onttrekkingJaar'];
$data['gerealiseerdRapPeriode']=gerealiseerdKoersresultaat($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$this->pdf->rapportageValuta,true);
$data['gerealiseerdJaar']=gerealiseerdKoersresultaat($portefeuille,$rapportageStartJaar,$rapportageDatum,$this->pdf->rapportageValuta);
$data['ongerealiseerdRapPeriode']=ongerealiseerdeKoersResultaat($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$this->pdf->rapportageValuta);
$data['ongerealiseerdJaar']=ongerealiseerdeKoersResultaat($portefeuille,$rapportageStartJaar,$rapportageDatum,$this->pdf->rapportageValuta);
$data['renteRapPeriode']=$this->renteResultaat($portefeuille,$rapportageDatumVanaf,$rapportageDatum);
$data['renteJaar']=$this->renteResultaat($portefeuille,$rapportageStartJaar,$rapportageDatum);

//$startDatum=$rapportageDatumVanaf;
//if(db2jul($portefeuilledata['startdatumMeerjarenrendement']) > db2jul($rapportageDatumVanaf))
//  $startDatum=$portefeuilledata['startdatumMeerjarenrendement'];
$data['perfRapPeriode']=performanceMeting($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
$data['perfJaar']      =performanceMeting($portefeuille,$rapportageStartJaar, $rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);



$this->pdf->ln(10);
$this->pdf->SetWidths(array(70, 15,10,45));
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);



$this->pdf->row(array(vertaalTekst('Netto performance',$this->pdf->rapport_taal)));
$this->pdf->ln();
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
$Y=$this->pdf->getY();
$this->pdf->row(array(vertaalTekst('van',$this->pdf->rapport_taal)." $startJaarDatumTekst ".vertaalTekst('t/m',$this->pdf->rapport_taal)." $rapDatumTekst"));
$this->pdf->row(array(vertaalTekst('van',$this->pdf->rapport_taal)." $startDatumTekst ".vertaalTekst('t/m',$this->pdf->rapport_taal)." $rapDatumTekst"));
$this->pdf->setY($Y);
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
$this->pdf->row(array('',$this->formatGetal($data['perfJaar'],1)."%"));
$this->pdf->row(array('',$this->formatGetal($data['perfRapPeriode'],1)."%"));

$this->pdf->SetWidths(array(40, 45,10,45));
$this->pdf->SetAligns(array('L', 'R','L','R'));
$this->pdf->ln(10);

$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
$this->pdf->row(array(vertaalTekst('Netto resultaat',$this->pdf->rapport_taal)));
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
$this->pdf->row(array('',"$startDatumTekst\n".vertaalTekst('t/m',$this->pdf->rapport_taal)." $rapDatumTekst",'',"$startJaarDatumTekst\n".vertaalTekst('t/m',$this->pdf->rapport_taal)." $rapDatumTekst"));
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
$this->pdf->row(array(vertaalTekst('Totaal vermogensmutatie',$this->pdf->rapport_taal), $this->formatGetal($data['mutatieRapPeriode']),'',$this->formatGetal($data['mutatieJaar'])));
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
$this->pdf->SetY($this->pdf->getY()-$this->pdf->rowHeight);
$this->pdf->row(array('', '','(b-a)',''));
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
$this->pdf->row(array(vertaalTekst('Saldo stortingen',$this->pdf->rapport_taal), $this->formatGetal($data['stortingRapPeriode']),'',$this->formatGetal($data['stortingJaar'])));
$this->pdf->row(array(vertaalTekst('Saldo onttrekkingen',$this->pdf->rapport_taal), $this->formatGetal($data['onttrekkingRapPeriode']),'',$this->formatGetal($data['onttrekkingJaar'])));
$this->pdf->line($this->pdf->marge,$this->pdf->getY(),$this->pdf->marge+array_sum($this->pdf->widths),$this->pdf->getY());
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
$this->pdf->row(array(vertaalTekst('Netto resultaat',$this->pdf->rapport_taal),$this->formatGetal($data['resultaatRapPeriode']),'',$this->formatGetal($data['resultaatJaar'])));
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
/*
$this->pdf->row(array('Gerealiseerd koersresultaat',$this->formatGetal($data['gerealiseerdRapPeriode']),'',$this->formatGetal($data['gerealiseerdJaar'])));
$this->pdf->row(array('Ongerealiseerd koersresultaat',$this->formatGetal($data['ongerealiseerdRapPeriode']),'',$this->formatGetal($data['ongerealiseerdJaar'])));
$this->pdf->row(array('Opgelopen rente',$this->formatGetal($data['renteRapPeriode']),'',$this->formatGetal($data['renteJaar'])));
$this->pdf->row(array('Directe opbrengsten',$this->formatGetal($grootboekPeriode['totaalcredit']),'',$this->formatGetal($grootboekJaar['totaalcredit'])));
$this->pdf->row(array('Kosten',$this->formatGetal($grootboekPeriode['totaaldebet']*-1),'',$this->formatGetal($grootboekJaar['totaaldebet']*-1)));
*/


$this->perfG(170,130,110,50,vertaalTekst('Ontwikkeling vermogen',$this->pdf->rapport_taal));

}
$this->pdf->templateVars['KERNVPaginas'] = $this->pdf->customPageNo;//+$this->pdf->extraPage

}



	function printPie($pieData,$kleurdata,$title='',$width=100,$height=100,$hcat)
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

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		while (list($key, $value) = each($pieData))
			if ($value < 0)
				$pieData[$key] = -1 * $value;

			//$this->pdf->SetXY(210, $this->pdf->headerStart);
			$y = $this->pdf->getY();
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+2);
			$this->pdf->setXY($startX+5,$y-3);
			$this->pdf->Cell(130,4,$title,0,0,"C");
			$this->pdf->setXY($startX,$y);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

      $this->pdf->setX($startX);
			$this->PieChart($width, $height, $pieData, '%l (%p)', $grafiekKleuren,$hcat);
			$hoogte = ($this->pdf->getY() - $y) + 8;
			$this->pdf->setY($y);

			$this->pdf->SetLineWidth($this->pdf->lineWidth);
			$this->pdf->setX($startX);

		//	$this->pdf->Rect($startX,$this->pdf->getY(),$width,$hoogte);

	}

	function PieChart($w, $h, $data, $format, $colors=null,$hcat)
  {

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      //$this->SetLegends($data,$format);

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
		$totaal=array_sum($data);
      foreach($data as $categorie=>$val)
      {
        $angle = floor(($val * 360) / doubleval($totaal));

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

      $x1 = $XPage + $radius*2 + 25 ;
      $x2 = $x1 + $hLegend + $margin;
      $y1 = $YDiag - ($radius) + $margin+5;

$this->pdf->SetXY($this->pdf->GetX(),$y1-5);



	  $i=0;
		foreach($data as $categorie=>$val)
		{
          //$this->pdf->SetXY($x2-30,$y1);
          $this->pdf->SetX($x2-20);
          $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->pdf->Rect($x1, $y1+$extraY, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1+$extraY);
          $this->pdf->Cell(20,$hLegend,$categorie,0,0,'L');//Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
			    $this->pdf->Cell(10,$hLegend, $this->formatGetal($val,1)."%",0,0,'R');
          $y1+=$hLegend + 2;

			$i++;
      }
      $this->pdf->SetFillColor(0,0,0);

  }

    function SetLegends($data, $format)
  {

  }

  function renteResultaat($portefeuille,$startDatum,$eindDatum)
  {
    global $__appvar;
    $DB=new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='$eindDatum' AND ".
						 " portefeuille = '$portefeuille' AND ".
						 " type = 'rente' ".$__appvar['TijdelijkeRapportageMaakUniek'];
		$DB->SQL($query);
		$DB->Query();
		$totaalA = $DB->nextRecord();

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='$startDatum' AND ".
						 " portefeuille = '$portefeuille' AND ".
						 " type = 'rente' ". $__appvar['TijdelijkeRapportageMaakUniek'] ;
		$DB->SQL($query);
		$DB->Query();
		$totaalB = $DB->nextRecord();

		if($this->pdf->rapportageValuta <> 'EUR' && $this->pdf->rapportageValuta <> '')
       $koers=getValutaKoers($this->pdf->rapportageValuta,$data['datum']);
    else
       $koers=1;

		$opgelopenRente = ($totaalA['totaal'] - $totaalB['totaal']) / $koers;
		return $opgelopenRente;
  }

  function perfG($xPositie,$yPositie,$width,$height,$title='')
	{
    $this->pdf->setXY($xPositie,$yPositie-10);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Multicell($w,5,$title,'','C');
    $this->pdf->setXY($xPositie,$yPositie-5);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

    $this->pdf->Multicell($w,5,vertaalTekst('inclusief stortingen en onttrekkingen',$this->pdf->rapport_taal),'','C');

    $this->pdf->setXY($XDiag+$w+2,$yPositie-10);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
    $this->pdf->Multicell($w,5,'X 1.000','','R');

    $this->pdf->setXY($xPositie,$yPositie);

    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0));
    $DB = new DB();
    if(isset($this->pdf->portefeuilles))
      $port= "IN('".implode("','",$this->pdf->portefeuilles)."') ";
    else
      $port= "= '".$this->portefeuille."'";
    $query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE periode='m' AND Portefeuille $port AND Categorie = 'Totaal'  ORDER BY Datum ASC LIMIT 1 ";
    $DB->SQL($query);
    $DB->Query();
    $datum = $DB->nextRecord();

    if($datum['id'] > 0 )//&& $this->pdf->lastPOST['perfPstart'] == 1
    {
      if($datum['month'] <10)
        $datum['month'] = "0".$datum['month'];
      $start = $datum['year'].'-'.$datum['month'].'-01';
    }
    else
      $start = $this->rapportageDatumVanaf;
    $eind = $this->rapportageDatum;

    $datumStart = db2jul($start);
    $datumStop  = db2jul($eind);

    $index = new indexHerberekening();
    $indexWaarden = $index->getWaarden($start,$eind,array($this->portefeuille,$this->pdf->portefeuilles));
    $aantalWaarden = count($indexWaarden);
    //echo $aantalWaarden;exit;
    $n=0;
    if($aantalWaarden < 13) // < dan een jaar gebruik maanden
    {
      $maandFilter=array(1,2,3,4,5,6,7,8,9,10,11,12);
    }
    elseif ($aantalWaarden < 49) // < 4 jaar gebruik kwartalen
    {
      $maandFilter=array(3,6,9,12);
    }
    else // gebruik jaren
    {
      $maandFilter=array(12);
    }

    foreach ($indexWaarden as $id=>$data)
    {
      if($this->pdf->rapportageValuta <> 'EUR' && $this->pdf->rapportageValuta <> '')
        $koers=getValutaKoers($this->pdf->rapportageValuta,$data['datum']);
      else
        $koers=1;
      $grafiekData['portefeuille'][$n]=$data['waardeHuidige']/$koers;
      $grafiekData['storingen'][$n]+=($data['stortingen']-$data['onttrekkingen'])/$koers;
      $datumArray[$n]=$data['datum'];
      $maand=date('m',db2jul($data['datum']));
      if(in_array($maand,$maandFilter))
        $n++;
    }


    $minVal = -1;
    $maxVal = 1;


    foreach ($grafiekData as $type=>$maxData)
    {
      foreach ($maxData as $waarde)
      {
        $maxVal=max($maxVal,$waarde);
        $minVal=min($minVal,$waarde);
      }
    }

    $w=$width;
    $h=$height;
    $horDiv = 10;

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );

    $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.3);
    $this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $procentWhiteSpace = 0.10;

    $band=($maxVal - $minVal);
    $stepSize=round($band / $horDiv);
    $stepSize=ceil($stepSize/(pow(10,strlen($stepSize))))*pow(10,strlen($stepSize));

    $maxVal = ceil($maxVal * (1 + ($procentWhiteSpace))/$stepSize)*$stepSize;
    $minVal = floor($minVal * (1 - ($procentWhiteSpace))/$stepSize)*$stepSize;
    $horDiv=($maxVal - $minVal)/$stepSize*2;
    if($horDiv > 10)
      $horDiv=($maxVal - $minVal)/$stepSize;

    $legendYstep = round(($maxVal - $minVal) / $horDiv);
    $vBar = ($lDiag / (count($grafiekData['portefeuille'])+ 1));
    $bGrafiek = $vBar * (count($grafiekData['portefeuille']) + 1);
    $eBaton = ($vBar * .5);

    $unith = $hDiag / ($maxVal - $minVal);
    $unitw = $vBar;//$lDiag / count($grafiekData['portefeuille']);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag,'FD','',array(245,245,245));
    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);
    $nulpunt = $YDiag + ($maxVal * $unith);
    $n=0;

    $this->pdf->Line($XDiag, $nulpunt, $XPage+$w ,$nulpunt,array('dash' => 1,'color'=>array(128,128,128)));
     for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$legendYstep)
     {
       $skipNull = true;
       $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('width' => 0.1,'dash' => 1,'color'=>array(128,128,128)));
       $this->pdf->Text($XDiag+$w+2, $i, $this->formatGetal(0-($n*$legendYstep/1000)));
       $n++;
       if($n >20)
        break;
     }

     $n=0;
     for($i=$nulpunt; $i > $top; $i-= $absUnit*$legendYstep)
     {
       $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('width' => 0.1,'dash' => 1,'color'=>array(128,128,128)));
       if($skipNull == true)
         $skipNull = false;
       else
         $this->pdf->Text($XDiag+$w+2, $i, ($this->formatGetal($n*$legendYstep/1000)));
       $n++;
       if($n >20)
         break;
     }
     $n=0;
     $laatsteI = count($datumArray)-1;
     $lijnenAantal = count($grafiekData);

          $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0,'width'=>0.1));
     foreach ($grafiekData['storingen'] as $i=>$waarde)
     {
       $yval2 = $YDiag + (($maxVal-$waarde) * $absUnit) ;
       $yval = $yval2;
       $xval = $XDiag + (1 + $i ) * $unitw - ($eBaton / 2);
       $lval = $eBaton;
       $hval = ($waarde * $unit);
       $hval =$nulpunt-$yval;
       $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,array(145,182,215)); //  //0,176,88
     }
     unset($yval);

     $lineStyle = array('width' => 0.75, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0));
     $maanden=array('null','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
     foreach ($grafiekData['portefeuille'] as $i=>$waarde)
     {
         if(!isset($datumPrinted[$i]))
         {
           $datumPrinted[$i] = 1;
           //if(substr($datumArray[$i],5,5)=='12-31' || $i == $laatsteI || $i==0)
           $julDatum=db2jul($datumArray[$i]);
           $this->pdf->TextWithRotation($XDiag+($i+1)*$unitw-6,$YDiag+$hDiag+10,vertaalTekst($maanden[date("n",$julDatum)],$pdf->rapport_taal).'-'.date("Y",$julDatum),45);
         }
         if($waarde)
         {
           $yval2 = $YDiag + (($maxVal-$waarde) * $absUnit) ;
           if($yval)
           {
             $markerSize=0.5;
             $this->pdf->line($XDiag+$i*$unitw, $yval, $XDiag+($i+1)*$unitw, $yval2,$lineStyle );
             $this->pdf->Rect($XDiag+$i*$unitw-0.5*$markerSize, $yval-0.5*$markerSize, $markerSize, $markerSize, 'DF',null,array(0,176,88));
           }
           $yval = $yval2;
         }
     }


     $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
     $this->pdf->SetFillColor(0,0,0);
     $this->pdf->CellBorders = array();
	}

	function VBarDiagram($w, $h, $data)
  {
      global $__appvar;
      $legendaWidth = 50;
      $grafiekPunt = array();
      $verwijder=array();

      $xPositie=$this->pdf->getX();
      $yPositie=$this->pdf->getY();
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
      $this->pdf->setXY($xPositie-20,$yPositie-$h-8);
      $this->pdf->Multicell($w,5,'Kasstroom uit obligatieportefeuille','','C');
      $this->pdf->setXY($xPositie+110,$yPositie-$h-8);
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
      $this->pdf->Multicell(20,5,'X 1.000','','L');
      $this->pdf->setXY($xPositie,$yPositie);


      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = $datum;
        $n=0;
        foreach ($waarden as $categorie=>$waarde)
        {
          $datumTotalen[$datum]+=$waarde;
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


          if(!isset($colors[$categorie]))
            $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          $n++;


        }
      }

    //  $colors=array('lossing'=>array($this->allekleuren['OIB']['OBL-ST']['R']['value'],$this->allekleuren['OIB']['OBL-ST']['G']['value'],$this->allekleuren['OIB']['OBL-ST']['B']['value']),
    //  'rente'=>array($this->allekleuren['OIB']['Liquiditeiten']['R']['value'],$this->allekleuren['OIB']['Liquiditeiten']['G']['value'],$this->allekleuren['OIB']['Liquiditeiten']['B']['value']));

		  $colors = array('lossing' => array(98,144,128),
										'rente'   => array(102, 102, 102));
      foreach ($verwijder as $datum)
      {
        foreach ($data[$datum] as $categorie=>$waarde)
        {
          $grafiek[$datum][$categorie]=0;
          $grafiekCategorie[$categorie][$datum]=0;
        }
      }

      $numBars = count($legenda);


      if($color == null)
      {
        $color=array(155,155,155);
      }
      $maxVal=max($datumTotalen);
      $minVal = 0;


      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

      $n=0;
      foreach (array_reverse($this->categorieVolgorde) as $categorie)
      {
        if(is_array($grafiekCategorie[$categorie]))
        {
          $this->pdf->Rect($XstartGrafiek+$bGrafiek+3 , $YstartGrafiek-$hGrafiek+$n*10+2, 2, 2, 'DF',null,$colors[$categorie]);
          $this->pdf->SetXY($XstartGrafiek+$bGrafiek+6 ,$YstartGrafiek-$hGrafiek+$n*10+1.5 );
          $this->pdf->Cell(20, 3,$this->categorieOmschrijving[$categorie],0,0,'L');
          $n++;
        }
      }
      $maxmaxVal=ceil($maxVal/(pow(10,strlen(round($maxVal)))))*pow(10,strlen(round($maxVal)));

      if($maxmaxVal/8 > $maxVal)
        $maxVal=$maxmaxVal/8;
      elseif($maxmaxVal/4 > $maxVal)
        $maxVal=$maxmaxVal/4;
      elseif($maxmaxVal/2 > $maxVal)
        $maxVal=$maxmaxVal/2;
      else
        $maxVal=$maxmaxVal;

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

      $this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $bGrafiek, $hGrafiek,'FD','',array(245,245,245));

      $n=0;

      for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek+$bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->SetXY($XstartGrafiek+$bGrafiek+1, $i-1.5);
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte/1000)."",0,0,'L');
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
         //   $this->pdf->SetXY($xval, $yval+($hval/2)-2);
         //   $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);

         if($legendaPrinted[$datum] != 1)
				 {
					 if(strlen($legenda[$datum])>4)
						 $this->pdf->TextWithRotation($xval - 0.75, $YstartGrafiek + 6.25, $legenda[$datum], 45);
					 else
					   $this->pdf->TextWithRotation($xval - 0.75, $YstartGrafiek + 5.25, $legenda[$datum], 45);
				 }
           //$this->pdf->TextWithRotation($XDiag+($i+1)*$unitw-6,$YDiag+$hDiag+10,vertaalTekst($maanden[date("n",$julDatum)],$pdf->rapport_taal).'-'.date("Y",$julDatum),45);

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


   $x1=$xval-50;
   $y1=$nulpunt+8;
   $hLegend=3;
   $legendaMarge=2;
   $vertaling['rente']='Coupons';
   $vertaling['lossing']='Lossingen';

         foreach ($colors as $categorie=>$color)
      {
      		$this->pdf->SetFont($this->rapport_font, '', 6);
		      $this->pdf->SetTextColor($this->rapport_fonds_fontcolor['R'],$this->rapport_fonds_fontcolor['G'],$this->rapport_fonds_fontcolor['B']);
		      $this->pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));

          $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
          $this->pdf->Rect($x1-5, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x1  ,$y1);
          $this->pdf->Cell(0,4,$vertaling[$categorie]);
         // $y1+= $hLegend + $legendaMarge;
          $x1+=40;
         $i++;

      }

    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
}
?>
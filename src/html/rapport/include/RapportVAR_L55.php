<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/05/26 16:45:07 $
 		File Versie					: $Revision: 1.9 $

 		$Log: RapportVAR_L55.php,v $
 		Revision 1.9  2017/05/26 16:45:07  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2016/03/02 16:59:05  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2014/08/06 15:41:01  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/06/08 15:27:58  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2014/05/21 09:32:51  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/05/17 16:35:44  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/05/05 15:52:25  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/04/30 16:03:17  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/04/19 16:16:18  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/01/06 10:09:57  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/09/05 18:19:11  rvv
 		*** empty log message ***
 		
 	
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
include_once($__appvar["basedir"]."/html/rapport/PDFOverzicht.php");

//ini_set('max_execution_time',60);
class RapportVAR_L55
{
	function RapportVAR_L55($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "RISK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

    $this->pdf->rapport_titel = "Rendement & Risicokenmerken";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->excelData=array();

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  else
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";

		$this->perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);


	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		global $__appvar;
    
    if($this->vastrentendeDeel(true)== true)
    {
      $this->pdf->addPage();
      $this->pdf->templateVars['VARPaginas'] = $this->pdf->page;
      $this->vastrentendeDeel();
    }
    else
    {
      $this->pdf->rapport_type = "";
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


  function vastrentendeDeel($check=false)
  {
    global $__appvar;
  	
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		unset($this->pdf->CellBorders);

		$this->cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		$this->cashfow->genereerTransacties();
		$this->cashfow->genereerRows();


		$DB = new DB();
		$this->db = new DB();
		$this->vastWhere=" AND ( TijdelijkeRapportage.hoofdcategorie='G-RISM' OR Fondsen.Lossingsdatum <> '0000-00-00')";


			  $query="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS actuelePortefeuilleWaardeEuro
			  FROM TijdelijkeRapportage
			  Left Join Fondsen ON Fondsen.Fonds = TijdelijkeRapportage.Fonds
			  WHERE TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND
			   TijdelijkeRapportage.portefeuille='".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek'].$this->vastWhere."";
    $DB->SQL($query);
    $waarde=$DB->lookupRecord();
    $waarde=$waarde['actuelePortefeuilleWaardeEuro'];

    $this->actueleWaardePortefeuille=$waarde;

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
".$__appvar['TijdelijkeRapportageMaakUniek'].$this->vastWhere."
GROUP BY
TijdelijkeRapportage.fonds,TijdelijkeRapportage.rekening
ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde,TijdelijkeRapportage.fondsOmschrijving,TijdelijkeRapportage.rekening";
		$DB->SQL($query);
		$DB->Query();
    
    if($check==true)
    { 
      if($DB->records() > 0)
        return true;
      else
        return false;  
    }

    $w=(297-30-80-2*$this->pdf->marge)/6;
		$this->pdf->SetWidths(array(30,80,$w,$w,$w,$w,$w,$w));
		$this->pdf->SetAligns(array("R",'L','R','L','R','R','R','R'));
    $this->pdf->CellBorders = array('U','U','U','U','U','U','U','U');

		$this->pdf->ln();
    
  	$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge,$this->pdf->GetY(),array_sum($this->pdf->widths), 8, 'F');
		 $this->pdf->row(array(vertaalTekst("Nominale\nWaarde",$this->pdf->rapport_taal),
     vertaalTekst("Instrument",$this->pdf->rapport_taal),
		 vertaalTekst("Actuele\nWaarde",$this->pdf->rapport_taal),
		 vertaalTekst("Rating instrument",$this->pdf->rapport_taal),
		 vertaalTekst("Coupon Rendement",$this->pdf->rapport_taal),
		 vertaalTekst("Markt Rendement",$this->pdf->rapport_taal),
		 vertaalTekst("Modified duration",$this->pdf->rapport_taal),
		 vertaalTekst("Resterende looptijd",$this->pdf->rapport_taal)));
   $this->pdf->SetAligns(array("R",'L','R','L','R','R','R','R'));
       unset($this->pdf->CellBorders);
       $this->pdf->ln();
    
    $n=0;   
		while ($data=$DB->nextRecord())
		{
			$rente=getRenteParameters($data['fonds'], $this->rapportageDatum);
			foreach($rente as $key=>$value)
				$data[$key]=$value;

      if($_POST['anoniem'] !=1 && $data['rekening'] <> '')
        $data['fondsOmschrijving'].=' '.substr($data['rekening'],0,strlen($data['rekening'])-3);

      $Hcategorie=$data['HcategorieOmschrijving'];
      if($Hcategorie=='')
        $Hcategorie='Hcat';

      //$data['actuelePortefeuilleWaardeEuro']=$data['actuelePortefeuilleWaardeEuro']-$data['rente'];
      if($data['type']=='rekening')
        $ongerealiseerdResultaat=0;
      else
        $ongerealiseerdResultaat=$data['actuelePortefeuilleWaardeEuro']-$data['beginPortefeuilleWaardeEuro'];

      $aandeel=$data['actuelePortefeuilleWaardeEuro']/$this->actueleWaardePortefeuille;

      $totalenCat[$data['categorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
      $totalenCat[$data['categorieOmschrijving']]['beginPortefeuilleWaardeEuro'] += $data['beginPortefeuilleWaardeEuro'];
      $totalenCat[$data['categorieOmschrijving']]['ongerealiseerdResultaat'] += $ongerealiseerdResultaat;
      $totalenCat[$data['categorieOmschrijving']]['aandeel'] += $aandeel;

      $totalenHcat[$Hcategorie]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
      $totalenHcat[$Hcategorie]['historischeWaardeEuro'] += $data['beginPortefeuilleWaardeEuro'];
      $totalenHcat[$Hcategorie]['ongerealiseerdResultaat'] += $ongerealiseerdResultaat;
      $totalenHcat[$Hcategorie]['aandeel'] += $aandeel;

      $totalen['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
      $totalen['beginPortefeuilleWaardeEuro'] += $data['beginPortefeuilleWaardeEuro'];
      $totalen['ongerealiseerdResultaat'] += $ongerealiseerdResultaat;
      $totalen['aandeel'] += $aandeel;

      $totalen['rente'] += $data['rente'];
     // listarray($waarden);
     if($data['Lossingsdatum'] <> '')
        $lossingsJul = adodb_db2jul($data['Lossingsdatum']);
     else
        $lossingsJul=0;
        $rentedatumJul = adodb_db2jul($data['Rentedatum']);
        $renteVanafJul = adodb_db2jul(jul2sql($this->pdf->rapport_datum));

			$koers=getRentePercentage($data['fonds'],$this->rapportageDatum);

			  $renteDag=0;
			  if($data['variabeleCoupon'] == 1)
			  {
			    $rapportJul=adodb_db2jul($this->rapportageDatum);
			    $renteJul=adodb_db2jul($data['Rentedatum']);
          $renteStap=($data['Renteperiode']/12)*31556925.96;
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

        if($lossingsJul > 0)
	      {

	        //$this->huidigeWaardeTotaal += $fonds['actuelePortefeuilleWaardeEuro'];
	        //$this->lossingsWaardeTotaal += $fonds['totaalAantal'] * 100 * $fonds['fondsEenheid'] * $fonds['actueleValuta'];
		  	  $jaar = ($lossingsJul-$renteVanafJul)/31556925.96;

		  	  $p = $data['actueleFonds'];
	        $r = $koers['Rentepercentage']/100;
	        $b = $this->cashfow->fondsDataKeyed[$data['fonds']]['lossingskoers'];
	        $y = $jaar;

	        $ytm=  $this->cashfow->bondYTM($p,$r,$b,$y)*100;
	        $restLooptijd=($lossingsJul-$this->pdf->rapport_datum)/31556925.96;

	         $duration=$this->cashfow->waardePerFonds[$data['fonds']]['ActueelWaardeJaar']/$this->cashfow->waardePerFonds[$data['fonds']]['ActueelWaarde'];
	         if($data['variabeleCoupon'] == 1 && $renteDag <> 0)
	           $modifiedDuration=($renteDag-db2jul($this->rapportageDatum))/86400/365;
	         else
	           $modifiedDuration=$duration/(1+$ytm/100);
	         $aandeel=$data['actuelePortefeuilleWaardeEuro']/$this->actueleWaardePortefeuille;

           $totalen['yield']+=$koers['Rentepercentage']*$data['totaalAantal']/$data['actuelePortefeuilleWaardeEuro']*$data['actueleValuta']*$aandeel;
	         $totalen['ytm']+=$ytm*$aandeel;
	         $totalen['duration']+=$duration*$aandeel;
	         $totalen['modifiedDuration']+=$modifiedDuration*$aandeel;
	         $totalen['restLooptijd']+=$restLooptijd*$aandeel;
	      }
	      else
	      {
	        $ytm=0;
	        $restLooptijd=0;
	        $duration=0;
	        $modifiedDuration=0;
	      }
      $n=fillLine($this->pdf,$n);
      $this->pdf->row(array($this->formatGetal(
      $data['totaalAantal'],0),
      $data['fondsOmschrijving'],
      $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),
      $data['fondsRating'],
      $this->formatGetal($koers['Rentepercentage']*$data['totaalAantal']/$data['actuelePortefeuilleWaardeEuro']*$data['actueleValuta'],2)."%",
      $this->formatGetal($ytm,2)."%",
      $this->formatGetal($modifiedDuration,2),
      $this->formatGetal($restLooptijd,2) ));
      $lastcategorieOmschrijving=$data['categorieOmschrijving'];
    }
		 $this->pdf->underlinePercentage=0.8;
    $this->pdf->CellBorders = array('','','','','TS','TS','TS','TS');
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->ln(2);

    unset($this->pdf->fillCell);
    $this->pdf->row(array('',vertaalTekst("Totale portefeuile gemiddeld",$this->pdf->rapport_taal),
    '','',
    $this->formatGetal($totalen['yield'],2)."%",
    $this->formatGetal($totalen['ytm'],2)."%",
    $this->formatGetal($totalen['modifiedDuration'],2),
    $this->formatGetal($totalen['restLooptijd'],2)));
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->underlinePercentage);
    
    
  }

}
?>
<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/06/24 17:29:01 $
File Versie					: $Revision: 1.8 $

$Log: RapportVAR_L76.php,v $
Revision 1.8  2020/06/24 17:29:01  rvv
*** empty log message ***

Revision 1.7  2020/06/06 15:48:23  rvv
*** empty log message ***

Revision 1.6  2020/05/30 15:30:39  rvv
*** empty log message ***

Revision 1.5  2020/05/27 16:14:13  rvv
*** empty log message ***

Revision 1.4  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.3  2018/04/28 18:36:15  rvv
*** empty log message ***

Revision 1.2  2018/04/22 09:30:29  rvv
*** empty log message ***

Revision 1.1  2018/04/18 16:18:39  rvv
*** empty log message ***

Revision 1.4  2017/05/26 16:45:07  rvv
*** empty log message ***

Revision 1.3  2015/11/01 17:25:34  rvv
*** empty log message ***

Revision 1.2  2014/07/30 15:36:51  rvv
*** empty log message ***

Revision 1.1  2014/07/16 16:01:16  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once("rapport/rapportATTberekening.php");

class RapportVAR_L76
{
	function RapportVAR_L76($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
	  $this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	  $this->db=new DB();
    $this->dataOnly=false;
    $this->varData=array();
	}

	function formatGetal($waarde, $dec)
	{
	  if($waarde==0)
	    return '';
	  else
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
		  $this->vastRentend=true;
      if($this->dataOnly==false)
      {
		    $this->pdf->rapport_type = "VAR";
		    $this->pdf->rapport_titel = "Overzicht obligatieportefeuille";
        $this->pdf->SetDrawColor(0);
		  }
      $this->vastWhere=" AND ( TijdelijkeRapportage.hoofdcategorie='VAR' OR Fondsen.Lossingsdatum <> '0000-00-00') AND TijdelijkeRapportage.Type <> 'rekening'";
		  $this->cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		  $this->cashfow->genereerTransacties();
		  $this->cashfow->genereerRows();
      $this->rapport();

	}
  


	function rapport()
	{
		global $__appvar;
    global $USR;
    
    
    $fill=true;


		$query = "SELECT Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();

				$query="SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." as actuelePortefeuilleWaardeEuro FROM
				TijdelijkeRapportage
				Left Join Fondsen ON Fondsen.Fonds = TijdelijkeRapportage.Fonds WHERE
		TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille='".$this->portefeuille."' ".$this->vastWhere.$__appvar['TijdelijkeRapportageMaakUniek']."";
		$DB->SQL($query);// echo $query."<br>\n";
		$DB->Query();
		$actueleWaarde = $DB->nextRecord();
		$portefeuilleWaarde=$actueleWaarde['actuelePortefeuilleWaardeEuro'];


		if($this->vastRentend==true)
		{
		  if($this->dataOnly==false)
      {
    $this->pdf->addPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas'] = $this->pdf->customPageNo;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas'] = $this->pdf->rapport_titel;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		unset($this->pdf->CellBorders);
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
Fondsen.Rentedatum,
Fondsen.Renteperiode,
Fondsen.variabeleCoupon,
TijdelijkeRapportage.fonds,
TijdelijkeRapportage.fondsEenheid
FROM
TijdelijkeRapportage
Left Join Fondsen ON Fondsen.Fonds = TijdelijkeRapportage.Fonds

WHERE
TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille='".$this->portefeuille."'
".$__appvar['TijdelijkeRapportageMaakUniek'].$this->vastWhere."
GROUP BY
TijdelijkeRapportage.fonds,TijdelijkeRapportage.rekening
ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde,TijdelijkeRapportage.fondsOmschrijving,TijdelijkeRapportage.rekening";
		$DB->SQL($query);// echo $query;exit;
		$DB->Query();

		while ($data=$DB->nextRecord())
		{
      $rente=getRenteParameters($data['fonds'], $this->rapportageDatum);
      foreach($rente as $key=>$value)
        $data[$key]=$value;

      if($_POST['anoniem'] !=1 && $data['rekening'] <> '')
        $data['fondsOmschrijving'].=' '.substr($data['rekening'],0,strlen($data['rekening'])-3);

      $Hcategorie=$data['HcategorieOmschrijving'];

      //$data['actuelePortefeuilleWaardeEuro']=$data['actuelePortefeuilleWaardeEuro']-$data['rente'];
      if($data['type']=='rekening')
        $ongerealiseerdResultaat=0;
      else
        $ongerealiseerdResultaat=$data['actuelePortefeuilleWaardeEuro']-$data['beginPortefeuilleWaardeEuro'];

      if($data['Lossingsdatum'] <> '' && $data['Lossingsdatum'] <> '0000-00-00')
        $aandeel=$data['actuelePortefeuilleWaardeEuro']/$portefeuilleWaarde;
      else
        $aandeel=0;  

      $totalenCat[$data['categorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
      $totalenCat[$data['categorieOmschrijving']]['beginPortefeuilleWaardeEuro'] += $data['beginPortefeuilleWaardeEuro'];
      $totalenCat[$data['categorieOmschrijving']]['ongerealiseerdResultaat'] += $ongerealiseerdResultaat;
     // if($data['Lossingsdatum'] <> '' && $data['Lossingsdatum'] <> '0000-00-00')
      $totalenCat[$data['categorieOmschrijving']]['aandeel'] += $aandeel;
      $totalenHcat[$Hcategorie]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
      $totalenHcat[$Hcategorie]['historischeWaardeEuro'] += $data['beginPortefeuilleWaardeEuro'];
      $totalenHcat[$Hcategorie]['ongerealiseerdResultaat'] += $ongerealiseerdResultaat;
      $totalenHcat[$Hcategorie]['aandeel'] += $aandeel;

      $totalen['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
      $totalen['beginPortefeuilleWaardeEuro'] += $data['beginPortefeuilleWaardeEuro'];
      $totalen['ongerealiseerdResultaat'] += $ongerealiseerdResultaat;
    //  if($data['Lossingsdatum'] <> '' && $data['Lossingsdatum'] <> '0000-00-00')
      $totalen['aandeel'] += $aandeel;
        

      if($data['categorieOmschrijving'] <> $lastcategorieOmschrijving)
      {
        if(!empty($lastcategorieOmschrijving))
        { //listarray($totalenCat[$lastcategorieOmschrijving]);
          $aandeelCat=$totalenCat[$lastcategorieOmschrijving]['aandeel'];
         if($this->dataOnly==false)
         {
          $this->pdf->CellBorders = array('','','','','','T','','T','T','T','T','T');
          $this->pdf->row(array('','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],0),'',
      $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['yield']/$aandeelCat,3),
      $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['ytm']/$aandeelCat,2),
      $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['modifiedDuration']/$aandeelCat,2),
      $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['restLooptijd']/$aandeelCat,2),
          $this->formatGetal($aandeelCat*100,1),''));
          /*
              $this->formatGetal($totalenC['yield'],3),
    $this->formatGetal($totalenC['ytm'],2),
    $this->formatGetal($totalenC['duration'],2),
    $this->formatGetal($totalenC['modifiedDuration'],2),
    $this->formatGetal($totalenC['restLooptijd'],2),
    */
          unset($this->pdf->CellBorders);
            }
          $totalenC=array();
        
        }
        if($this->dataOnly==false)
        {
        if($this->pdf->getY() > 180)
          $this->pdf->addPage();

        $this->pdf->SetFont($this->pdf->rapport_font,'BI',$this->pdf->rapport_fontsize);

        if($Hcategorie <> $lastHcategorie)
        {
          if($this->pdf->getY() > 180)
            $this->pdf->addPage();
        }
        }
        $lastHcategorie=$Hcategorie;
        if($this->dataOnly==false)
        {
        $this->pdf->row(array(vertaalTekst($data['categorieOmschrijving'],$this->pdf->rapport_taal)));
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        }
      }
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
	        $b = $this->cashfow->fondsDataKeyed[$data['fonds']]['lossingskoers'];// 100
	        $y = $jaar;

	        $ytm=  $this->cashfow->bondYTM($p,$r,$b,$y)*100;
          //if($data['fondsOmschrijving']=='Aegon FRN 04-perp.')
          //{ echo "$ytm <= $p,$r,$b,$y<br>";exit;}
	        $restLooptijd=($lossingsJul-$this->pdf->rapport_datum)/31556925.96;

	         //listarray($this->cashfow->waardePerFonds);
	         $duration=$this->cashfow->waardePerFonds[$data['fonds']]['ActueelWaardeJaar']/$this->cashfow->waardePerFonds[$data['fonds']]['ActueelWaarde'];

	         if($data['variabeleCoupon'] == 1 && $renteDag <> 0)
	           $modifiedDuration=($renteDag-db2jul($this->rapportageDatum))/86400/365;
	         else
	           $modifiedDuration=$duration/(1+$ytm/100);
             
          //echo round($modifiedDuration,4)." ".$data['fonds']."<br>\n";   

	         $aandeel=$data['actuelePortefeuilleWaardeEuro']/$portefeuilleWaarde;
           $totalen['yield']+=$koers['Rentepercentage']*$aandeel;
	         $totalen['ytm']+=$ytm*$aandeel;
	         $totalen['duration']+=$duration*$aandeel;
	         $totalen['modifiedDuration']+=$modifiedDuration*$aandeel;
	         $totalen['restLooptijd']+=$restLooptijd*$aandeel;
           
           $totalenCat[$data['categorieOmschrijving']]['yield']+=$koers['Rentepercentage']*$aandeel;
	         $totalenCat[$data['categorieOmschrijving']]['ytm']+=$ytm*$aandeel;
	         $totalenCat[$data['categorieOmschrijving']]['duration']+=$duration*$aandeel;
	         $totalenCat[$data['categorieOmschrijving']]['modifiedDuration']+=$modifiedDuration*$aandeel;
	         $totalenCat[$data['categorieOmschrijving']]['restLooptijd']+=$restLooptijd*$aandeel;
           
	         /*
	         $totalenC['yield']+=$koers['Rentepercentage']*$aandeel;
	         $totalenC['ytm']+=$ytm*$aandeel;
	         $totalenC['duration']+=$duration*$aandeel;
	         $totalenC['modifiedDuration']+=$modifiedDuration*$aandeel;
	         $totalenC['restLooptijd']+=$restLooptijd*$aandeel;
	         */
//	        $this->cashfow
	       // echo $data['fonds'].' '.round($jaar,1)." jaar $ytm <br>";
	       // $this->ytm[$fonds['fonds'].' looptijd '.round($jaar,1)." jaar."] = ;
	      }
	      else
	      {
	        $ytm=0;
	        $restLooptijd=0;
	        $duration=0;
	        $modifiedDuration=0;
	      }

        if($this->dataOnly==false)
        {
          $this->pdf->SetFillColor(230,230,230);
          if($fill==true)
		      {
		        $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
            $fill=false;
		      }
		      else
		      {
		        $this->pdf->fillCell=array();
		        $fill=true;
		      }
      $this->pdf->row(array('  '.$data['fondsOmschrijving'],$data['fondsRating'],$data['valuta'],$this->formatGetal($data['totaalAantal'],0),
      $this->formatGetal($data['actueleFonds'],2),$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),'',
        $this->formatGetal($koers['Rentepercentage'],3),$this->formatGetal($ytm,2),$this->formatGetal($modifiedDuration,2),
        $this->formatGetal($restLooptijd,2), $this->formatGetal($aandeel*100,1)));//,$this->formatGetal($duration,2)
        $this->pdf->fillCell=array();
    //  '',$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),$this->formatGetal($data['beginPortefeuilleWaardeEuro'],0),$this->formatGetal($ongerealiseerdResultaat,0),$this->formatGetal($aandeel,1).'%',$this->formatGetal($data['rente'],2)
      }
      $lastcategorieOmschrijving=$data['categorieOmschrijving'];
    }
  //  echo "$lastcategorieOmschrijving";
//listarray($totalenCat);    
        if($this->dataOnly==false)
        {
    if(!empty($lastcategorieOmschrijving))
    {
      $this->pdf->CellBorders = array('','','','','','T','','T','T','T','T','T');
      $aandeelCat=$totalenCat[$lastcategorieOmschrijving]['aandeel'];
      $this->pdf->row(array('','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],0),'',
      $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['yield']/$aandeelCat,3),
      $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['ytm']/$aandeelCat,2),
      $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['modifiedDuration']/$aandeelCat,2),
      $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['restLooptijd']/$aandeelCat,2),
      $this->formatGetal($aandeelCat*100,1),''));
      unset($this->pdf->CellBorders);
    }




    $this->pdf->CellBorders = array('','','','','','T','','T','T','T','T','T','T');
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->ln(2);
    $this->pdf->row(array(vertaalTekst("Totaal",$this->pdf->rapport_taal),'','','','',$this->formatGetal($totalen['actuelePortefeuilleWaardeEuro'],0),'',
    $this->formatGetal($totalen['yield']/$totalen['aandeel'],3),
    $this->formatGetal($totalen['ytm']/$totalen['aandeel'],2),
    $this->formatGetal($totalen['modifiedDuration']/$totalen['aandeel'],2),
    $this->formatGetal($totalen['restLooptijd']/$totalen['aandeel'],2),
    $this->formatGetal($totalen['aandeel']*100,1),'')); //    $this->formatGetal($totalen['duration'],2),
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
       }
       else
       {
        $aandeelCat=$totalenCat[$lastcategorieOmschrijving]['aandeel'];
       }

     $this->varData['totalen']['totaal']=$totalen;

       
		}
		else
		{


		$this->pdf->AddPage();

      $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas'] = $this->pdf->customPageNo;

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		unset($this->pdf->CellBorders);
//SUM(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid * TijdelijkeRapportage.actueleValuta) AS historischeWaardeEuro,


$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
	  $query="SELECT
	  hoofdcategorien.Omschrijving AS HcategorieOmschrijving,
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
Beleggingscategorien.Afdrukvolgorde,
TijdelijkeRapportage.type,
Beleggingscategorien.Omschrijving as categorieOmschrijving
FROM
TijdelijkeRapportage
LEFT Join Beleggingscategorien ON TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT Join CategorienPerHoofdcategorie ON TijdelijkeRapportage.beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.vermogensbeheerder='$beheerder'
LEFT Join Beleggingscategorien  as hoofdcategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = hoofdcategorien.Beleggingscategorie
WHERE
TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille='".$this->portefeuille."'
".$__appvar['TijdelijkeRapportageMaakUniek']."
GROUP BY
TijdelijkeRapportage.fonds,TijdelijkeRapportage.rekening
ORDER BY Beleggingscategorien.Afdrukvolgorde,TijdelijkeRapportage.fondsOmschrijving";
		$DB->SQL($query);
		$DB->Query();//  echo $query;  exit;

		$DB2=new DB();
    while($data = $DB->nextRecord())
    {
      if($_POST['anoniem'] !=1 && $data['rekening'] <> '')
      {
        //$data['fondsOmschrijving'].=' '.substr($data['rekening'],0,strlen($data['rekening'])-3);
        $query="SELECT Rekeningen.Rekening,Rekeningen.Valuta,
                if(Rekeningen.Depotbank <> '',rekeningBank.Omschrijving, Depotbanken.Omschrijving) as Omschrijving
                FROM
                Rekeningen
                Inner Join Portefeuilles ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille
                Inner Join Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
                LEFT Join Depotbanken as rekeningBank ON Rekeningen.Depotbank = rekeningBank.Depotbank
                WHERE Rekeningen.Rekening='".$data['rekening']."' AND Portefeuilles.Portefeuille <> 'C_$USR'";
        $DB2->SQL($query);
		    $depot=$DB2->lookupRecord();
		    $data['fondsOmschrijving'] = $depot['Omschrijving'].' '.substr($data['rekening'],0,strlen($data['rekening'])-3);


      }
      $Hcategorie=$data['HcategorieOmschrijving'];

      $data['actuelePortefeuilleWaardeEuro']=$data['actuelePortefeuilleWaardeEuro']-$data['rente'];
      if($data['type']=='rekening')
        $ongerealiseerdResultaat=0;
      else
        $ongerealiseerdResultaat=$data['actuelePortefeuilleWaardeEuro']-$data['beginPortefeuilleWaardeEuro'];

      $aandeel=$data['actuelePortefeuilleWaardeEuro']/$portefeuilleWaarde*100;

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

      if($data['categorieOmschrijving'] <> $lastcategorieOmschrijving)
      {
        if(!empty($lastcategorieOmschrijving))
        {
          if($totalen['rente'] <> 0)
          {
            $renteAandeel=$totalen['rente']/$portefeuilleWaarde*100;
            $totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'] += $totalen['rente'];
            $totalenCat[$lastcategorieOmschrijving]['aandeel'] += $renteAandeel;
            $totalen['actuelePortefeuilleWaardeEuro'] += $totalen['rente'];
            $totalen['aandeel'] += $renteAandeel;
            $totalenHcat[$lastHcategorie]['actuelePortefeuilleWaardeEuro'] += $totalen['rente'];
            $totalenHcat[$lastHcategorie]['aandeel'] += $renteAandeel;
            $this->pdf->ln(2.5);
            $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
            $this->pdf->row(array('  '.vertaalTekst('Opgelopen rente',$this->pdf->rapport_taal),'','','','', $this->formatGetal( $totalen['rente'],0),'','',$this->formatGetal($renteAandeel ,1),''));
            $totalen['rente']=0;
          }

          $this->pdf->CellBorders = array('','','','','','T','T','T','T','T');
          $this->pdf->row(array('','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],0),
          $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['beginPortefeuilleWaardeEuro'],0),
          $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['ongerealiseerdResultaat'],0),
          $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['aandeel'],1),''));
          unset($this->pdf->CellBorders);
        }
        if($this->pdf->getY() > 180)
          $this->pdf->addPage();

        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);

        if($Hcategorie <> $lastHcategorie)
        {
          if(!empty($lastHcategorie))
          {
            $this->pdf->ln(5);
            $this->pdf->CellBorders = array('','','','','','T','T','T','T','T');
            $this->pdf->row(array(vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastHcategorie,$this->pdf->rapport_taal),'','','','',$this->formatGetal($totalenHcat[$lastHcategorie]['actuelePortefeuilleWaardeEuro'],0),
            $this->formatGetal($totalenHcat[$lastHcategorie]['beginPortefeuilleWaardeEuro'],0),
            $this->formatGetal($totalenHcat[$lastHcategorie]['ongerealiseerdResultaat'],0),
            $this->formatGetal($totalenHcat[$lastHcategorie]['aandeel'],1),''));
            unset($this->pdf->CellBorders);
            $this->pdf->ln(10);
          }
          if($this->pdf->getY() > 180)
            $this->pdf->addPage();
          $this->pdf->SetFont($this->pdf->rapport_font,'BI',$this->pdf->rapport_fontsize);
          $this->pdf->row(array(vertaalTekst($Hcategorie,$this->pdf->rapport_taal)));
          $this->pdf->ln(2);
        }
        $lastHcategorie=$Hcategorie;

        $this->pdf->row(array('  '.vertaalTekst($data['categorieOmschrijving'],$this->pdf->rapport_taal)));
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      }
      $totalen['rente'] += $data['rente'];
     // listarray($waarden);
      $this->pdf->row(array('  '.$data['fondsOmschrijving'],$data['valuta'],$this->formatGetal($data['totaalAantal'],0),$this->formatGetal($data['actueleFonds'],2),$this->formatGetal($data['beginwaardeLopendeJaar'],2),
      '',$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),$this->formatGetal($data['beginPortefeuilleWaardeEuro'],0),$this->formatGetal($ongerealiseerdResultaat,0),$this->formatGetal($aandeel,1),$this->formatGetal($data['rente'],0)));
      $lastcategorieOmschrijving=$data['categorieOmschrijving'];
    }

    if(!empty($lastcategorieOmschrijving))
    {
      if($totalen['rente'] <> 0)
      {
        $renteAandeel=$totalen['rente']/$portefeuilleWaarde*100;
        $totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'] += $totalen['rente'];
        $totalenCat[$lastcategorieOmschrijving]['aandeel'] += $renteAandeel;
        $totalen['actuelePortefeuilleWaardeEuro'] += $totalen['rente'];
        $totalen['aandeel'] += $renteAandeel;
        $totalenHcat[$lastHcategorie]['actuelePortefeuilleWaardeEuro'] += $totalen['rente'];
        $totalenHcat[$lastHcategorie]['aandeel'] += $renteAandeel;
        $this->pdf->ln(2.5);
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $this->pdf->row(array('  '.vertaalTekst('Opgelopen rente',$this->pdf->rapport_taal),'','','','', '',$this->formatGetal( $totalen['rente'],0),'','',$this->formatGetal($renteAandeel ,1),''));
        $totalen['rente']=0;
      }

      $this->pdf->CellBorders = array('','','','','','','T','T','T','T','T');
      $this->pdf->row(array('','','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],0),
      $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['beginPortefeuilleWaardeEuro'],0),
      $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['ongerealiseerdResultaat'],0),
      $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['aandeel'],1),''));
      unset($this->pdf->CellBorders);
    }

    if(!empty($lastHcategorie))
    {
      $this->pdf->ln(5);
      $this->pdf->CellBorders = array('','','','','','','T','T','T','T','T');
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->row(array(vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastHcategorie,$this->pdf->rapport_taal),'','','','','',$this->formatGetal($totalenHcat[$lastHcategorie]['actuelePortefeuilleWaardeEuro'],0),
      $this->formatGetal($totalenHcat[$lastHcategorie]['beginPortefeuilleWaardeEuro'],0),
      $this->formatGetal($totalenHcat[$lastHcategorie]['ongerealiseerdResultaat'],0),
      $this->formatGetal($totalenHcat[$lastHcategorie]['aandeel'],1),''));
      unset($this->pdf->CellBorders);
      $this->pdf->ln(10);
      if($this->pdf->getY() > 180)
        $this->pdf->addPage();
    }

    $this->pdf->CellBorders = array('','','','','','','T','T','T','T','T');
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->ln(2);
    $this->pdf->row(array(vertaalTekst("Totaal",$this->pdf->rapport_taal),'','','','','',$this->formatGetal($totalen['actuelePortefeuilleWaardeEuro'],0),
    $this->formatGetal($totalen['beginPortefeuilleWaardeEuro'],0),
    $this->formatGetal($totalen['ongerealiseerdResultaat'],0),
    $this->formatGetal($totalen['aandeel'],1),''));
    unset($this->pdf->CellBorders);


    $this->pdf->pageBottom=array($this->pdf->pageTop[0],$this->pdf->GetY());
    $this->pdf->Line($this->pdf->pageTop[0],$this->pdf->pageTop[1],$this->pdf->pageTop[0],$this->pdf->GetY());

    $this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);

		$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);

		//printSamenstellingResultaat_L33($this->pdf,$this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		//printAEXVergelijking_L33($this->pdf,$this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);

		}

		$this->pdf->pageBottom=array($this->pdf->pageTop[0],$this->pdf->GetY());
    if($this->pdf->getY() > 175)
    {
      $this->pdf->addPage();
      unset($this->pdf->pageTop);
    }

    $this->pdf->SetWidths(array(280));
    $this->pdf->setY(175);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  }
}
?>
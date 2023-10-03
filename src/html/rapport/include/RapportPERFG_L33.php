<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/09/09 16:43:36 $
File Versie					: $Revision: 1.2 $

$Log: RapportPERFG_L33.php,v $
Revision 1.2  2018/09/09 16:43:36  rvv
*** empty log message ***

Revision 1.1  2018/09/08 17:43:29  rvv
*** empty log message ***

Revision 1.11  2018/02/21 17:15:09  rvv
*** empty log message ***

Revision 1.10  2018/02/17 19:18:57  rvv
*** empty log message ***

Revision 1.9  2017/05/26 16:45:07  rvv
*** empty log message ***

Revision 1.8  2016/02/25 11:26:38  rvv
*** empty log message ***

Revision 1.7  2016/02/24 17:11:47  rvv
*** empty log message ***

Revision 1.6  2016/02/15 06:56:41  rvv
*** empty log message ***

Revision 1.5  2016/02/06 16:42:56  rvv
*** empty log message ***

Revision 1.4  2013/04/24 13:22:02  rvv
*** empty log message ***

Revision 1.3  2013/04/20 16:34:57  rvv
*** empty log message ***

Revision 1.2  2013/04/03 14:58:34  rvv
*** empty log message ***

Revision 1.1  2013/03/23 16:19:36  rvv
*** empty log message ***

Revision 1.31  2013/03/13 17:01:08  rvv
*** empty log message ***

Revision 1.30  2012/10/07 14:57:18  rvv
*** empty log message ***

Revision 1.29  2012/09/30 11:18:17  rvv
*** empty log message ***

Revision 1.28  2012/09/05 18:19:11  rvv
*** empty log message ***

Revision 1.27  2012/08/22 15:46:00  rvv
*** empty log message ***

Revision 1.26  2012/08/11 13:17:53  rvv
*** empty log message ***

Revision 1.25  2012/07/29 10:24:33  rvv
*** empty log message ***

Revision 1.24  2012/06/30 14:42:50  rvv
*** empty log message ***

Revision 1.23  2012/05/27 08:33:10  rvv
*** empty log message ***

Revision 1.22  2012/05/12 15:11:00  rvv
*** empty log message ***

Revision 1.21  2012/04/21 15:38:14  rvv
*** empty log message ***

Revision 1.20  2012/04/01 07:40:26  rvv
*** empty log message ***

Revision 1.19  2012/02/19 16:13:11  rvv
*** empty log message ***

Revision 1.18  2011/12/24 16:35:21  rvv
*** empty log message ***

Revision 1.17  2011/12/18 14:26:44  rvv
*** empty log message ***

Revision 1.16  2011/11/30 18:36:35  rvv
*** empty log message ***

Revision 1.15  2011/11/27 12:46:47  rvv
*** empty log message ***

Revision 1.14  2011/09/10 17:54:37  rvv
*** empty log message ***

Revision 1.13  2011/08/07 09:02:51  rvv
*** empty log message ***

Revision 1.12  2011/07/08 06:45:17  cvs
*** empty log message ***

Revision 1.11  2011/07/03 06:42:47  rvv
*** empty log message ***

Revision 1.10  2011/05/25 17:43:01  rvv
*** empty log message ***

Revision 1.9  2011/05/14 10:51:09  rvv
*** empty log message ***

Revision 1.8  2011/04/13 14:58:34  rvv
*** empty log message ***

Revision 1.7  2011/04/13 09:54:39  rvv
*** empty log message ***

Revision 1.6  2011/04/11 18:03:14  rvv
*** empty log message ***

Revision 1.5  2011/04/11 17:55:48  rvv
*** empty log message ***

Revision 1.4  2011/04/09 14:35:27  rvv
*** empty log message ***

Revision 1.3  2011/04/03 08:35:46  rvv
*** empty log message ***

Revision 1.2  2011/03/18 15:02:38  rvv
*** empty log message ***

Revision 1.1  2011/03/13 18:36:37  rvv
*** empty log message ***

Revision 1.1  2011/02/13 17:50:29  rvv
*** empty log message ***

Revision 1.1  2011/02/06 14:36:59  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once("rapport/rapportATTberekening.php");

class RapportPERFG_L33
{
	function RapportPERFG_L33($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
	  $this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	  $this->db=new DB();
    $this->verdeling='regio';
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
		  $this->pdf->rapport_type = "PERFG";
		  $this->pdf->rapport_titel = "Overzicht obligaties per regio";
		  $this->vastWhere=" AND ( TijdelijkeRapportage.hoofdcategorie='G-RISM' OR Fondsen.Lossingsdatum <> '0000-00-00') AND TijdelijkeRapportage.Type <> 'rekening'";
		  $this->cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		  $this->cashfow->genereerTransacties();
		  $this->cashfow->genereerRows();
      $this->rapport();

	}

	function rapport()
	{
		global $__appvar;
    global $USR;
		$query = "SELECT Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();

				$query="SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." as actuelePortefeuilleWaardeEuro FROM
				TijdelijkeRapportage
				Left Join Fondsen ON Fondsen.Fonds = TijdelijkeRapportage.Fonds WHERE
		TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille='".$this->portefeuille."' ".$this->vastWhere.$__appvar['TijdelijkeRapportageMaakUniek']."";
		$DB->SQL($query); 
		$DB->Query();
		$actueleWaarde = $DB->nextRecord();
		$portefeuilleWaarde=$actueleWaarde['actuelePortefeuilleWaardeEuro'];


		if($this->vastRentend==true)
		{
 $this->pdf->addPage();
		$this->pdf->templateVars[$this->pdf->rapport_type.'Paginas'] = $this->pdf->customPageNo;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		unset($this->pdf->CellBorders);
$type=$this->verdeling;
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
TijdelijkeRapportage.".$type." as beleggingscategorie,
TijdelijkeRapportage.".$type."Volgorde as Afdrukvolgorde,
TijdelijkeRapportage.type,
TijdelijkeRapportage.".$type."Omschrijving as categorieOmschrijving,
Fondsen.rating as fondsRating,
Fondsen.Lossingsdatum,
Fondsen.Rentedatum,
Fondsen.Renteperiode,
Fondsen.variabeleCoupon,
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
ORDER BY TijdelijkeRapportage.".$type."Volgorde,TijdelijkeRapportage.fondsOmschrijving,TijdelijkeRapportage.rekening";
		$DB->SQL($query);
		$DB->Query();
//echo "<table><tr><td>Fonds</td><td> actueleKoers</td><td> Rentepercentage</td><td> lossingskoers </td><td>JarenTotLossing </td><td> ytm </td> <td>Lossingsdatum </td></tr>\n";
   
    $subTotaal=true;
    $eindTotaal=true;
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

      $aandeel=$data['actuelePortefeuilleWaardeEuro']/$portefeuilleWaarde;

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
          $aandeelCat=$totalenCat[$lastcategorieOmschrijving]['aandeel'];
          $this->pdf->CellBorders = array('','','','','','','T','','T','T','T','T','T');
          if($subTotaal==true)
            $this->pdf->row(array('','','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],0),'',
                            $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['yield']/$aandeelCat,3),
                            $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['ytm']/$aandeelCat,2),
                            $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['modifiedDuration']/$aandeelCat,2),
                            $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['restLooptijd']/$aandeelCat,2),
                            $this->formatGetal($aandeelCat*100,1),''));
          else
            $this->pdf->row(array('','','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],0),'',
             ' ',' ',' ',' ',
             $this->formatGetal($aandeelCat*100,1),''));
          /*
              $this->formatGetal($totalenC['yield'],3),
    $this->formatGetal($totalenC['ytm'],2),
    $this->formatGetal($totalenC['duration'],2),
    $this->formatGetal($totalenC['modifiedDuration'],2),
    $this->formatGetal($totalenC['restLooptijd'],2),
    */
          unset($this->pdf->CellBorders);
          $totalenC=array();
        }
        if($this->pdf->getY() > 185)
          $this->pdf->addPage();

        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);

        if($Hcategorie <> $lastHcategorie)
        {
          if($this->pdf->getY() > 185)
            $this->pdf->addPage();
        }
        $lastHcategorie=$Hcategorie;

        $this->pdf->row(array(vertaalTekst($data['categorieOmschrijving'],$this->pdf->rapport_taal)));
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
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
//echo jul2sql($this->pdf->rapport_datum);exit;
	        //$this->huidigeWaardeTotaal += $fonds['actuelePortefeuilleWaardeEuro'];
	        //$this->lossingsWaardeTotaal += $fonds['totaalAantal'] * 100 * $fonds['fondsEenheid'] * $fonds['actueleValuta'];
		  	  $jaar = ($lossingsJul-$renteVanafJul)/31556925.96;


/*
          $lossingsJaar=adodb_date("Y",$lossingsJul);
          $lossingsDag=adodb_date("z",$lossingsJul);

          $rapportageJaar=adodb_date("Y",$this->pdf->rapport_datum);
          $rapportageDag=adodb_date("z",$this->pdf->rapport_datum);   
          if($lossingsJaar==$rapportageJaar)
          {
            $dagen=($lossingsDag - $rapportageDag);
            $dagenInJaar=date('z',mktime(0,0,0,12,31,$rapportageJaar));
            $jaar=$dagen/$dagenInJaar;
            //echo $data['fonds']." ($lossingsDag - $rapportageDag)=".($lossingsDag - $rapportageDag)." | $lossingsDag/$dagenInJaar=$jaar";//exit;
          }   
*/
		  	  $p = $data['actueleFonds'];
	        $r = $koers['Rentepercentage']/100;
	        $b = $this->cashfow->fondsDataKeyed[$data['fonds']]['lossingskoers'];// 100
	        $y = $jaar;

	        $ytm=  $this->cashfow->bondYTM($p,$r,$b,$y)*100;
	        $restLooptijd=($lossingsJul-$this->pdf->rapport_datum)/31556925.96;
          
//echo "<tr><td>". $data['fonds'] . "</td><td> $p </td><td> $r </td><td> $b </td><td>$y  </td><td>  $ytm </td>  <td> ".$data['Lossingsdatum']."</td> </tr>\n";
	         //listarray($this->cashfow->waardePerFonds);
	         $duration=$this->cashfow->waardePerFonds[$data['fonds']]['ActueelWaardeJaar']/$this->cashfow->waardePerFonds[$data['fonds']]['ActueelWaarde'];

	         if($data['variabeleCoupon'] == 1 && $renteDag <> 0)
	           $modifiedDuration=($renteDag-db2jul($this->rapportageDatum))/86400/365;
	         else
	           $modifiedDuration=$duration/(1+$ytm/100);
             
       //   echo round($modifiedDuration,4)." ".$data['fonds']."<br>\n";   

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
           if($ytm==0||$duration==0||$modifiedDuration==0||$restLooptijd==0)
           {
             $subTotaal=false;
             $eindTotaal=false;
           }
      $this->pdf->row(array('  '.$data['fondsOmschrijving'],$data['fondsRating'],$data['emittentRating'],$data['valuta'],$this->formatGetal($data['totaalAantal'],0),
      $this->formatGetal($data['actueleFonds'],2),$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),'',
        $this->formatGetal($koers['Rentepercentage'],3),$this->formatGetal($ytm,2),$this->formatGetal($modifiedDuration,2),
        $this->formatGetal($restLooptijd,2), $this->formatGetal($aandeel*100,1)));//,$this->formatGetal($duration,2)
    //  '',$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),$this->formatGetal($data['beginPortefeuilleWaardeEuro'],0),$this->formatGetal($ongerealiseerdResultaat,0),$this->formatGetal($aandeel,1).'%',$this->formatGetal($data['rente'],2)
      $lastcategorieOmschrijving=$data['categorieOmschrijving'];
    }
  //  echo "$lastcategorieOmschrijving";
//listarray($totalenCat);    

    if(!empty($lastcategorieOmschrijving))
    {
      $this->pdf->CellBorders = array('','','','','','','T','','T','T','T','T','T');
      $aandeelCat=$totalenCat[$lastcategorieOmschrijving]['aandeel'];
      if($subTotaal==true)
        $this->pdf->row(array('','','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],0),'',
        $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['yield']/$aandeelCat,3),
        $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['ytm']/$aandeelCat,2),
        $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['modifiedDuration']/$aandeelCat,2),
        $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['restLooptijd']/$aandeelCat,2),
        $this->formatGetal($aandeelCat*100,1),''));
      else
        $this->pdf->row(array('','','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],0),'',
        '','','','',
        $this->formatGetal($aandeelCat*100,1),''));
          
      unset($this->pdf->CellBorders);
    }




    $this->pdf->CellBorders = array('','','','','','','T','','T','T','T','T','T','T');
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->ln(2);
    if($eindTotaal==true)
      $this->pdf->row(array(vertaalTekst("Totaal",$this->pdf->rapport_taal),'','','','','',$this->formatGetal($totalen['actuelePortefeuilleWaardeEuro'],0),'',
      $this->formatGetal($totalen['yield'],3),
      $this->formatGetal($totalen['ytm'],2),
      $this->formatGetal($totalen['modifiedDuration'],2),
      $this->formatGetal($totalen['restLooptijd'],2),
      $this->formatGetal($totalen['aandeel']*100,1),'')); //    $this->formatGetal($totalen['duration'],2),
    else
      $this->pdf->row(array(vertaalTekst("Totaal",$this->pdf->rapport_taal),'','','','','',$this->formatGetal($totalen['actuelePortefeuilleWaardeEuro'],0),'','','','','', $this->formatGetal($totalen['aandeel']*100,1)));  
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		}
	
//echo "</table>";
		$this->pdf->pageBottom=array($this->pdf->pageTop[0],$this->pdf->GetY());
    if($this->pdf->getY() > 185)
    {
      $this->pdf->addPage();
      unset($this->pdf->pageTop);
    }

    $this->pdf->SetWidths(array(280));
    $this->pdf->setY(190);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize-1);
    $rowHeight=$this->pdf->rowHeight;
    $this->pdf->rowHeight=3.0;
    if($this->pdf->rapport_taal==1)
      $this->pdf->row(array('The prices used in this report are all from sources that are deemed reliable. To be able to report as quickly as possible, especially at quarter end, we will use estimates of valuations, whenever a definitive valuation is not available. As soon as a final valuation is available, this value will be entered in our systems. As a result, the value of the investment portfolio and the calculated performance may, especially at quarter end, be slightly different in subsequent quarterly reports.'));
    else
      $this->pdf->row(array('De in deze rapportage gebruikte effectenkoersen worden verkregen uit door ons betrouwbaar geachte bronnen. Omwille van de snelheid van rapporteren kan, met name op de kwartaalultimo, gebruik gemaakt worden van schatting van koersen, daar waar niet direct na de kwartaalultimo een definitieve koers beschikbaar is. Zodra de definitieve koers beschikbaar is, zal deze in onze systemen worden ingevoerd. Derhalve kan de waarde van de effectenportefeuille en daarmee ook het berekende beleggingsresultaat, met name per kwartaalultimo, in de opeenvolgende kwartaalrapportages enigszins van elkaar verschillen.'));
    $this->pdf->rowHeight=$rowHeight;  }
}
?>
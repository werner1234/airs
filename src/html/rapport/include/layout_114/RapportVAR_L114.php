<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/06/06 15:48:23 $
File Versie					: $Revision: 1.16 $

$Log: RapportVAR_L77.php,v $
Revision 1.16  2020/06/06 15:48:23  rvv
*** empty log message ***

Revision 1.15  2019/12/04 15:56:35  rvv
*** empty log message ***

Revision 1.14  2019/09/14 17:09:05  rvv
*** empty log message ***

Revision 1.13  2019/03/09 18:46:18  rvv
*** empty log message ***

Revision 1.12  2019/02/23 18:32:59  rvv
*** empty log message ***

Revision 1.11  2018/11/22 07:25:26  rvv
*** empty log message ***

Revision 1.10  2018/10/24 16:00:59  rvv
*** empty log message ***

Revision 1.9  2018/10/20 18:05:20  rvv
*** empty log message ***

Revision 1.8  2018/10/17 15:37:17  rvv
*** empty log message ***

Revision 1.7  2018/10/13 17:18:13  rvv
*** empty log message ***

Revision 1.6  2018/10/10 15:50:56  rvv
*** empty log message ***

Revision 1.5  2018/10/06 17:20:57  rvv
*** empty log message ***

Revision 1.4  2018/09/19 17:35:08  rvv
*** empty log message ***

Revision 1.3  2018/09/05 09:41:28  rvv
*** empty log message ***

Revision 1.2  2018/09/05 09:29:06  rvv
*** empty log message ***

Revision 1.1  2018/05/20 10:39:12  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");

class RapportVAR_L114
{
	function RapportVAR_L114($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
	  $this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	  $this->db=new DB();
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
		  $this->pdf->rapport_type = "VAR";
		  $this->pdf->rapport_titel = "Overzicht obligatieportefeuille";
		  $this->vastWhere="AND (Fondsen.Lossingsdatum <> '0000-00-00') AND TijdelijkeRapportage.Type <> 'rekening' AND Fondsen.Fondssoort='OBL'";
		  $this->cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
      $this->cashfow->genereerTransacties();
		  $this->cashfow->genereerRows();
    
    $DB = new DB();
    $q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $DB->SQL($q);
    $DB->Query();
    $tmp = $DB->LookupRecord();
    $kleuren = unserialize($tmp['grafiek_kleur']);
    $this->ratingKleuren=array('AAA'=>array(255,204,0),'AA'=>array(102,102,102),'A'=>array(204,204,204),'BBB'=>array(255,255,102),'Non Inv. Grade'=>array(0,0,0),'Geen rating'=>array(255,255,255));
    
    $this->standaardKleurenKort=array(array(1,88,109),array(4,157,218),array(74,202,218),array(140,219,233),array(176,218,238),array(233,242,252));
    $this->standaardKleurenLang=array(array(1,88,109),array(1,117,140),array(4,157,218),array(0,176,202),array(74,202,218),array(140,219,233),array(137,204,233),array(176,218,238),
      array(233,242,252),array(156,222,202),array(114,195,139),array(71,168,76),array(43,150,34),array(30,127,22),array(18,104,11),array(6,82,0));

    foreach ($kleuren['Rating'] as $rating=>$waarde)
    {
      $this->ratingKleuren[$rating] = array($waarde['R']['value'], $waarde['G']['value'], $waarde['B']['value']);
      if($rating=='BBB')
      {
        $this->ratingKleuren['Non Inv. Grade'] = array($waarde['R']['value'], $waarde['G']['value'], $waarde['B']['value']);
      }
    }
    
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

    //$this->pdf->SetFillColor($this->pdf->rapport_regelAchtergrond[0],$this->pdf->rapport_regelAchtergrond[1],$this->pdf->rapport_regelAchtergrond[2]);
    unset($this->pdf->fillCell);
		if($this->vastRentend==true)
		{


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
Fondsen.ISINcode,
TijdelijkeRapportage.actueleValuta,
TijdelijkeRapportage.fondsOmschrijving,
TijdelijkeRapportage.rekening,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.beleggingscategorieVolgorde as Afdrukvolgorde,
TijdelijkeRapportage.type,
TijdelijkeRapportage.beleggingscategorieOmschrijving as categorieOmschrijvingUit,
Fondsen.rating as fondsRating,
TijdelijkeRapportage.Lossingsdatum,
TijdelijkeRapportage.Rentedatum,
TijdelijkeRapportage.Renteperiode,
Fondsen.variabeleCoupon,
emittentPerFonds.emittent,
TijdelijkeRapportage.fonds,
emittenten.rating as emittentRating,
ifnull( Rating.Afdrukvolgorde,100) as RatingAfdrukvolgorde,
TijdelijkeRapportage.fondsEenheid
FROM
TijdelijkeRapportage
Left Join Fondsen ON Fondsen.Fonds = TijdelijkeRapportage.Fonds
Left Join Rating ON (Fondsen.rating = Rating.rating )
Left Join emittentPerFonds ON emittentPerFonds.Fonds = TijdelijkeRapportage.Fonds  AND emittentPerFonds.vermogensbeheerder='$beheerder'
LEFT Join emittenten ON emittentPerFonds.emittent = emittenten.emittent AND emittentPerFonds.vermogensbeheerder = '$beheerder'
WHERE
TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille='".$this->portefeuille."'
".$__appvar['TijdelijkeRapportageMaakUniek'].$this->vastWhere."
GROUP BY
TijdelijkeRapportage.fonds,TijdelijkeRapportage.rekening
ORDER BY RatingAfdrukvolgorde, TijdelijkeRapportage.fonds";//TijdelijkeRapportage.beleggingscategorieVolgorde,TijdelijkeRapportage.fondsOmschrijving,TijdelijkeRapportage.rekening";
		$DB->SQL($query);
		$DB->Query();
//echo "<table><tr><td>Fonds</td><td> actueleKoers</td><td> Rentepercentage</td><td> lossingskoers </td><td>JarenTotLossing </td><td> ytm </td> <td>Lossingsdatum </td></tr>\n";
    $aantalRegels=$DB->records();
    
    if($aantalRegels==0)
      return '';
      
      $this->pdf->addPage();
      $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
      $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
      
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      unset($this->pdf->CellBorders);
      
    $subTotaal=true;
    $eindTotaal=true;
      $n=0;
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
          $this->pdf->CellBorders = array('','','','','','','','','T','','T','T','T','T','T');
          unset($this->pdf->fillCell);
          if($subTotaal==true)
            $this->pdf->row(array('','','','','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],0),'',
                            $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['yield']/$aandeelCat,3),
                            $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['ytm']/$aandeelCat,2),
                            $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['modifiedDuration']/$aandeelCat,2),
                            $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['restLooptijd']/$aandeelCat,2),
                            $this->formatGetal($aandeelCat*100,1),''));
          else
            $this->pdf->row(array('','','','','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],0),'',
             ' ',' ',' ',' ',
             $this->formatGetal($aandeelCat*100,1),''));

          unset($this->pdf->CellBorders);
          $totalenC=array();
        }
        if($this->pdf->getY() > 180)
          $this->pdf->addPage();

        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);

        if($Hcategorie <> $lastHcategorie)
        {
          if($this->pdf->getY() > 180)
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
		  	  $jaar = ($lossingsJul-$renteVanafJul)/31556925.96;

		  	  $p = $data['actueleFonds'];
	        $r = $koers['Rentepercentage']/100;
	        $b = $this->cashfow->fondsDataKeyed[$data['fonds']]['lossingskoers'];// 100
	        $y = $jaar;

	        $ytm=  $this->cashfow->bondYTM($p,$r,$b,$y)*100;
	        $restLooptijd=($lossingsJul-$this->pdf->rapport_datum)/31556925.96;
          
	         $duration=$this->cashfow->waardePerFonds[$data['fonds']]['ActueelWaardeJaar']/$this->cashfow->waardePerFonds[$data['fonds']]['ActueelWaarde'];

	           $modifiedDuration=$duration/(1+$ytm/100);


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


      //if($n%2 == 0)
      //  $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
      //else
     //   unset($this->pdf->fillCell);

      $this->pdf->row(array($data['fondsOmschrijving'],$data['ISINcode'], date('d-m',db2jul($data['Rentedatum'])),
                        $data['fondsRating'],$data['emittentRating'],$data['valuta'],$this->formatGetal($data['totaalAantal'],0),
        $this->formatGetal($data['actueleFonds'],2),$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),'',
        $this->formatGetal($koers['Rentepercentage'],3),$this->formatGetal($ytm,2),$this->formatGetal($modifiedDuration,2),
        $this->formatGetal($restLooptijd,2), $this->formatGetal($aandeel*100,1)));//,$this->formatGetal($duration,2)
    //  '',$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),$this->formatGetal($data['beginPortefeuilleWaardeEuro'],0),$this->formatGetal($ongerealiseerdResultaat,0),$this->formatGetal($aandeel,1).'%',$this->formatGetal($data['rente'],2)
      $lastcategorieOmschrijving=$data['categorieOmschrijving'];
      $n++;
    }
  //  echo "$lastcategorieOmschrijving";
//listarray($totalenCat);    

    if(!empty($lastcategorieOmschrijving))
    {
      unset($this->pdf->fillCell);
      $this->pdf->CellBorders = array('','','','','','','','','T','','T','T','T','T','T');
      $aandeelCat=$totalenCat[$lastcategorieOmschrijving]['aandeel'];
      if($subTotaal==true)
        $this->pdf->row(array('','','','','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],0),'',
        $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['yield']/$aandeelCat,3),
        $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['ytm']/$aandeelCat,2),
        $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['modifiedDuration']/$aandeelCat,2),
        $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['restLooptijd']/$aandeelCat,2),
        $this->formatGetal($aandeelCat*100,1),''));
      else
        $this->pdf->row(array('','','','','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],0),'',
        '','','','',
        $this->formatGetal($aandeelCat*100,1),''));
          
      unset($this->pdf->CellBorders);
    }




    $this->pdf->CellBorders = array('','','','','','','','','T','','T','T','T','T','T','T');
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->ln(2);
      unset($this->pdf->fillCell);
    if($eindTotaal==true)
      $this->pdf->row(array(vertaalTekst("Totaal",$this->pdf->rapport_taal),'','','','','','','',$this->formatGetal($totalen['actuelePortefeuilleWaardeEuro'],0),'',
      $this->formatGetal($totalen['yield'],3),
      $this->formatGetal($totalen['ytm'],2),
      $this->formatGetal($totalen['modifiedDuration'],2),
      $this->formatGetal($totalen['restLooptijd'],2),
      $this->formatGetal($totalen['aandeel']*100,1),'')); //    $this->formatGetal($totalen['duration'],2),
    else
      $this->pdf->row(array(vertaalTekst("Totaal",$this->pdf->rapport_taal),'','','','','','','',$this->formatGetal($totalen['actuelePortefeuilleWaardeEuro'],0),'','','','','', $this->formatGetal($totalen['aandeel']*100,1)));
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		}
	
//echo "</table>";
		$this->pdf->pageBottom=array($this->pdf->pageTop[0],$this->pdf->GetY());
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    if($aantalRegels>1)
    {
      if($this->pdf->getY()>130)
      {
        $this->pdf->addPage();
        $this->toonRating(20, 40);
        $this->toonDuration(150, 40);
      }
      else
      {
        $this->toonRating(20, 120);
        $this->toonDuration(150, 120);
      }
    }
  }
  
  
  function toonRating($x,$y)
  {
    $this->pdf->setXY($x,$y);
    global $__appvar;
    $DB = new DB();
    $query = "SELECT
SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as waardeEur ,
CategorienPerHoofdcategorie.Hoofdcategorie,
HoofdBeleggingscategorien.Omschrijving AS HoofdcategorieOmschrijving,
Fondsen.rating,
ifnull( Rating.Afdrukvolgorde,100) as Afdrukvolgorde
FROM
TijdelijkeRapportage
Left Join Beleggingscategorien ON (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie)
Left Join CategorienPerHoofdcategorie ON TijdelijkeRapportage.beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
Left Join Beleggingscategorien AS HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie
LEFT Join Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
 Left Join Rating ON (Fondsen.rating = Rating.rating )
WHERE (TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' ) AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".$__appvar['TijdelijkeRapportageMaakUniek']. $this->vastWhere."
AND TijdelijkeRapportage.`type` NOT IN ('rekening','rente')
GROUP BY Fondsen.rating
ORDER BY  Afdrukvolgorde";
    $DB->SQL($query);
    $DB->Query();
    
   // $this->ratingGrafiek=array('AAA'=>0,'AA'=>0,'A'=>0,'BBB'=>0,'Non Inv. Grade'=>0,'Geen rating'=>0);
  
    $ratingData=array();
    $ratingTotaalWaarde=0;
    while($rating = $DB->NextRecord())
    {
      if(substr($rating['rating'],0,3)=='AAA')
        $rating['rating']='AAA';
      elseif(substr($rating['rating'],0,2)=='AA')
        $rating['rating']='AA';
      elseif(substr($rating['rating'],0,1)=='A')
        $rating['rating']='A';
      elseif(substr($rating['rating'],0,3)=='BBB')
        $rating['rating']='BBB';
      elseif($rating['rating'] <> '' || substr($rating['rating'],0,2)=='BB'|| substr($rating['rating'],0,1)=='C' || $rating['rating']=='NR')
        $rating['rating']='Non Inv. Grade';
      else
        $rating['rating']='Geen rating';
      
      $ratingData[$rating['rating']] +=$rating['waardeEur'];
      $ratingTotaalWaarde +=$rating['waardeEur'];
      
      
    }
    $ratingGrafiekKleuren=array();
  
    if(count($ratingData)<7)
      $kleuren=$this->standaardKleurenKort;
    else
      $kleuren=$this->standaardKleurenLang;
  
    $i=0;
  //listarray($ratingData);
  //  arsort ($ratingData);
    foreach ($ratingData as  $rating=>$waarde)
    {
     // $waarden=$ratingData[$rating];
      $procent=$waarde/$ratingTotaalWaarde;
      $this->ratingGrafiek[$rating.' ('.$this->formatGetal($procent*100,1).' %)']=$procent*100;
      $ratingGrafiekKleuren[]=$kleuren[$i];//$this->ratingKleuren[$rating];
      $i++;
    }
    $this->ratingData=$ratingData;
    
    
    

    //$this->PieChart(50, 50,$this->ratingGrafiek, '%l (%p)',$ratingGrafiekKleuren);
  
    $legendaStart=$this->correctLegentHeight(count($this->ratingGrafiek));
    $this->PieChart(50,50,$this->ratingGrafiek,'%l',$ratingGrafiekKleuren,vertaalTekst('Rating',$this->pdf->rapport_taal),$legendaStart);
    
    return $this->ratingData;
  }
  
  function toonDuration($x,$y)
  {
    $this->pdf->setXY($x,$y);
    global $__appvar;
    $DB=new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
      "FROM TijdelijkeRapportage Left Join Fondsen ON Fondsen.Fonds = TijdelijkeRapportage.Fonds WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek']. $this->vastWhere;
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    
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
".$__appvar['TijdelijkeRapportageMaakUniek']. $this->vastWhere."
GROUP BY
TijdelijkeRapportage.fonds,TijdelijkeRapportage.rekening
ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde,TijdelijkeRapportage.fondsOmschrijving,TijdelijkeRapportage.rekening";
    
    $this->db=new DB();
    $DB->SQL($query);
    $DB->Query();
    $durationChart=array('0-1'=>0,'1-3'=>0,'3-7'=>0,'7-12'=>0,'>12'=>0,'overig'=>0);
    
    
    $actueleWaardePortefeuille=0;
    while ($data=$DB->nextRecord())
    {
      $rente=getRenteParameters($data['fonds'], $this->rapportageDatum);
      foreach($rente as $key=>$value)
        $data[$key]=$value;
      $actueleWaardePortefeuille+=$data['actuelePortefeuilleWaardeEuro'];
      
      
      
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
      
      $aandeel=$data['actuelePortefeuilleWaardeEuro']/$totaalWaarde['totaal']*100;
      
      if($lossingsJul > 0)
      {
        
        //$this->huidigeWaardeTotaal += $fonds['actuelePortefeuilleWaardeEuro'];
        //$this->lossingsWaardeTotaal += $fonds['totaalAantal'] * 100 * $fonds['fondsEenheid'] * $fonds['actueleValuta'];
        $jaar = ($lossingsJul-$renteVanafJul)/31556925.96;
        
        $p = $data['actueleFonds'];
        $r = $koers['Rentepercentage']/100;
        $b = $this->cashfow->fondsDataKeyed[$data['fonds']]['lossingskoers'];
        $year = $jaar;
        
        $ytm=  $this->cashfow->bondYTM($p,$r,$b,$year)*100;

        $restLooptijd=($lossingsJul-$this->pdf->rapport_datum)/31556925.96;
        
        $duration=$this->cashfow->waardePerFonds[$data['fonds']]['ActueelWaardeJaar']/$this->cashfow->waardePerFonds[$data['fonds']]['ActueelWaarde'];
        if($data['variabeleCoupon'] == 1 && $renteDag <> 0)
          $modifiedDuration=($renteDag-db2jul($this->rapportageDatum))/86400/365;
        else
          $modifiedDuration=$duration/(1+$ytm/100);
        
        
        $totalen['yield']+=$koers['Rentepercentage']*$data['totaalAantal']/$data['actuelePortefeuilleWaardeEuro']*$data['actueleValuta']*$aandeel;
        $totalen['ytm']+=$ytm*$aandeel;
        $totalen['duration']+=$duration*$aandeel;
        $totalen['modifiedDuration']+=$modifiedDuration*$aandeel;
        $totalen['restLooptijd']+=$restLooptijd*$aandeel;
        
        
        if($duration<1)
          $durationChart['0-1']+=$aandeel;
        elseif($duration<3)
          $durationChart['1-3']+=$aandeel;
        elseif($duration<7)
          $durationChart['3-7']+=$aandeel;
        elseif($duration<12)
          $durationChart['7-12']+=$aandeel;
        else
          $durationChart['>12']+=$aandeel;
        
        
      }
      else
      {
        $durationChart['overig']+=$aandeel;
      }
    }
    
    //$durationChartKleuren=array();
    //$kleuren=array('0-1'=>array(0,200,250),'1-3'=>array(10,190,240),'3-7'=>array(20,160,230),'7-12'=>array(30,140,220),'>12'=>array(40,110,220),'overig'=>array(40,80,210));
  //  arsort ($durationChart);
    if(count($durationChart)<7)
      $kleuren=$this->standaardKleurenKort;
    else
      $kleuren=$this->standaardKleurenLang;
    
    //listarray($kleuren);
    //listarray($durationChart);
    $durationChartFiltered=array();
    $i=0;
    foreach($durationChart as $key=>$value)
    {
      if($value<>0)
      {
        $durationChartKleuren[] = $kleuren[$i];
        $durationChartFiltered[$key."  (".$this->formatGetal($value,1).' %)']=$value;
        $i++;
      }
      else
      {
        unset($durationChart[$key]);
      }
    }
    // listarray($durationChart);
    /*
    $this->pdf->setXY($x,$y-10);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Cell(100,5,"Duration obligaties",0,0,'C');
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetXY($x,$y); //
    $this->PieChart(50, 50,$durationChart, '%l (%p)',$durationChartKleuren);
    */
    $legendaStart=$this->correctLegentHeight(count($durationChart));
    $this->PieChart(50,50,$durationChartFiltered,'%l',$durationChartKleuren,vertaalTekst('Duration',$this->pdf->rapport_taal),$legendaStart);
  
  
  }
  
  
  
  function correctLegentHeight($regels)
  {
    return array($this->pdf->GetX()+60,$this->pdf->GetY()+ 35 -($regels*4)/2);
    
  }
  
  function PieChart( $w, $h, $data, $format, $colors = null,$titel='',$legendaStart='')
  {
    
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetLegends($data, $format);
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $hLegend = 2;
    $radius = min($w - $margin * 4 - $hLegend, $h - $margin * 2); //
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
    $this->pdf->setDrawColor(255,255,255);
    foreach ($data as $val)
    {
      $angle = floor(($val * 360) / doubleval($this->pdf->sum));
      if ($angle != 0)
      {
        $angleEnd = $angleStart + $angle;
        $this->pdf->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
        $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
        $angleStart += $angle;
      }
      $i++;
    }
    if ($angleEnd != 360)
    {
      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    }
    $this->pdf->setDrawColor(0,0,0);
    //Legends
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
    $x1 = $XPage + $w + $radius * .5;
    $x2 = $x1 + $hLegend + $margin - 12;
    $y1 = $YDiag - ($radius) + $margin;
    
    for ($i = 0; $i < $this->pdf->NbVal; $i++)
    {
      $this->pdf->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
      $this->pdf->Rect($x1 - 12, $y1, $hLegend, $hLegend, 'DF');
      $this->pdf->SetXY($x2, $y1);
      if(strpos($this->pdf->legends[$i],'||')>0)
      {
        $parts=explode("||",$this->pdf->legends[$i]);
        $this->pdf->Cell(0, $hLegend, $parts[1]);
      }
      else
      {
        $this->pdf->Cell(0, $hLegend, $this->pdf->legends[$i]);
      }
      $y1 += $hLegend + $margin;
    }
  }
}
?>
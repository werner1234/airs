<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/15 16:39:18 $
File Versie					: $Revision: 1.5 $

$Log: RapportVAR_L81.php,v $
Revision 1.5  2020/07/15 16:39:18  rvv
*** empty log message ***

Revision 1.4  2019/06/16 09:51:00  rvv
*** empty log message ***

Revision 1.3  2019/03/06 19:21:33  rvv
*** empty log message ***

Revision 1.2  2019/01/06 12:44:28  rvv
*** empty log message ***

Revision 1.1  2018/12/27 15:11:17  rvv
*** empty log message ***

Revision 1.1  2018/10/03 15:42:01  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");

class RapportVAR_L81
{
	function RapportVAR_L81($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
	  $this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	  $this->db=new DB();
    $this->pdf->excelData[]=array("Categorie","Naam",'Coupon %','Coupondatum','Rating instr.','Valuta','ISIN-code','Nominaal','Koers','Opgelopen rente','Marktwaarde','Yield to Maturity','Modified duration','Resterende looptijd','% Port.');
    
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
		  $this->vastWhere="AND (TijdelijkeRapportage.hoofdcategorie='H-OBLIG' OR Fondsen.Lossingsdatum <> '0000-00-00') AND TijdelijkeRapportage.Type <> 'rekening' AND Fondsen.Fondssoort='OBL'";
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

    //$this->pdf->SetFillColor($this->pdf->rapport_regelAchtergrond[0],$this->pdf->rapport_regelAchtergrond[1],$this->pdf->rapport_regelAchtergrond[2]);
    unset($this->pdf->fillCell);
		if($this->vastRentend==true)
		{
      $this->pdf->addPage();
      $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
      $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		unset($this->pdf->CellBorders);

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
TijdelijkeRapportage.Lossingsdatum,
TijdelijkeRapportage.Rentedatum,
TijdelijkeRapportage.Renteperiode,
Fondsen.variabeleCoupon,
Fondsen.ISINcode,
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
//echo "<table><tr><td>Fonds</td><td> actueleKoers</td><td> Rentepercentage</td><td> lossingskoers </td><td>JarenTotLossing </td><td> ytm </td> <td>Lossingsdatum </td></tr>\n";
   
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
          $this->pdf->CellBorders = array('','T','','','','','','T','T','','T','T','T','T');
          unset($this->pdf->fillCell);
          if($subTotaal==true)
            $this->pdf->row(array('',                            $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['yield']/$aandeelCat,3),
                              '','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['rente'],0),
                            $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],0),'',

                            $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['ytm']/$aandeelCat,2),
                            $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['modifiedDuration']/$aandeelCat,2),
                            $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['restLooptijd']/$aandeelCat,2),
                            $this->formatGetal($aandeelCat*100,1),''));
          else
            $this->pdf->row(array('','','','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],0),'',
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
      $totalenCat[$data['categorieOmschrijving']]['rente']+= $data['rente'];
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

      $this->pdf->row(array('  '.$data['fondsOmschrijving'],$this->formatGetal($koers['Rentepercentage'],3),
                        date('d-m',db2jul($data['Rentedatum'])),
                        $data['fondsRating'],$data['valuta'],$this->formatGetal($data['totaalAantal'],0),
      $this->formatGetal($data['actueleFonds'],2),$this->formatGetal($data['rente'],0),$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),'',
        $this->formatGetal($ytm,2),$this->formatGetal($modifiedDuration,2),
        $this->formatGetal($restLooptijd,2), $this->formatGetal($aandeel*100,1)));//,$this->formatGetal($duration,2)
      
      $this->pdf->excelData[]=array($data['categorieOmschrijving'],$data['fondsOmschrijving'],
        round($koers['Rentepercentage'],3),
        date('d-m',db2jul($data['Rentedatum'])),
        $data['fondsRating'],
        $data['valuta'],
        $data['ISINcode'],
        round($data['totaalAantal'],0),
        round($data['actueleFonds'],2),round($data['rente'],0),
        round($data['actuelePortefeuilleWaardeEuro'],0),
        round($ytm,2),
        round($modifiedDuration,2),
        round($restLooptijd,2),
        round($aandeel*100,1));
  
      $lastcategorieOmschrijving=$data['categorieOmschrijving'];
      $n++;
    }
  //  echo "$lastcategorieOmschrijving";
//listarray($totalenCat);    

    if(!empty($lastcategorieOmschrijving))
    {
      unset($this->pdf->fillCell);
      $this->pdf->CellBorders = array('','T','','','','','','T','T','','T','T','T','T');
      $aandeelCat=$totalenCat[$lastcategorieOmschrijving]['aandeel'];
      if($subTotaal==true)
        $this->pdf->row(array('',        $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['yield']/$aandeelCat,3),
                          '','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['rente'],0),
        $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],0),'',
        $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['ytm']/$aandeelCat,2),
        $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['modifiedDuration']/$aandeelCat,2),
        $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['restLooptijd']/$aandeelCat,2),
        $this->formatGetal($aandeelCat*100,1),''));
      else
        $this->pdf->row(array('','','','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],0),'',
        '','','','',
        $this->formatGetal($aandeelCat*100,1),''));
          
      unset($this->pdf->CellBorders);
    }




    $this->pdf->CellBorders = array('','T','','','','','','T','T','','T','T','T','T','T');
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->ln(2);
      unset($this->pdf->fillCell);
    if($eindTotaal==true)
      $this->pdf->row(array(vertaalTekst("Totaal",$this->pdf->rapport_taal),      $this->formatGetal($totalen['yield'],3),
                        '','','','','',$this->formatGetal($totalen['rente'],0),
                        $this->formatGetal($totalen['actuelePortefeuilleWaardeEuro'],0),'',

      $this->formatGetal($totalen['ytm'],2),
      $this->formatGetal($totalen['modifiedDuration'],2),
      $this->formatGetal($totalen['restLooptijd'],2),
      $this->formatGetal($totalen['aandeel']*100,1),'')); //    $this->formatGetal($totalen['duration'],2),
    else
      $this->pdf->row(array(vertaalTekst("Totaal",$this->pdf->rapport_taal),'','','','','',$this->formatGetal($totalen['rente'],0),$this->formatGetal($totalen['actuelePortefeuilleWaardeEuro'],0),'','','','','', $this->formatGetal($totalen['aandeel']*100,1)));
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		}
	
//echo "</table>";
		$this->pdf->pageBottom=array($this->pdf->pageTop[0],$this->pdf->GetY());
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
   
  }
}
?>
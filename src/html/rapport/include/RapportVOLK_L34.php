<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/05/26 16:45:07 $
File Versie					: $Revision: 1.12 $

$Log: RapportVOLK_L34.php,v $
Revision 1.12  2017/05/26 16:45:07  rvv
*** empty log message ***

Revision 1.11  2015/11/07 16:45:15  rvv
*** empty log message ***

Revision 1.10  2015/11/04 16:54:21  rvv
*** empty log message ***

Revision 1.9  2015/11/03 05:19:03  rvv
*** empty log message ***

Revision 1.8  2015/11/02 18:51:54  rvv
*** empty log message ***

Revision 1.7  2015/07/15 06:57:37  rvv
*** empty log message ***

Revision 1.6  2013/11/06 16:55:30  rvv
*** empty log message ***

Revision 1.5  2012/04/14 16:51:17  rvv
*** empty log message ***

Revision 1.4  2011/11/16 19:22:09  rvv
*** empty log message ***

Revision 1.3  2011/10/12 17:57:09  rvv
*** empty log message ***

Revision 1.2  2011/10/09 16:54:45  rvv
*** empty log message ***

Revision 1.1  2011/09/25 16:23:28  rvv
*** empty log message ***

Revision 1.2  2011/09/10 17:54:37  rvv
*** empty log message ***

Revision 1.1  2011/05/08 09:42:27  rvv
*** empty log message ***

Revision 1.11  2011/04/13 14:58:34  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L34.php");

class RapportVOLK_L34
{
	function RapportVOLK_L34($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VOLK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Vermogensoverzicht";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec,$nulTonen=false)
	{
	  if($waarde==0 && $nulTonen==false)
	    return '';
	  else
  		return number_format($waarde,$dec,",",".");
	}

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	  if ($VierDecimalenZonderNullen)
	  {
	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '0000' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];
	       if ($decimaal != '0' && !$newDec)
	       {
	         $newDec = $i;
	       }
	     }
	     return number_format($waarde,$newDec,",",".");
	   }
	  else
	   return number_format($waarde,$dec,",",".");
	  }
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

  function addHeader($categorie)
  {
    $this->pdf->CellBorders = array(array('T','U','L'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array($categorie,'','','','','','','','',''));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    if($categorie=='Liquiditeiten')
      $koersdatum='Mutatiedatum';
    else
      $koersdatum='Koersdatum';  
    
		$this->pdf->row(array("Naam Fonds","Aantal","Valuta","Koers",$koersdatum,"Marktwaarde","Resultaat (%)","Weging","Depot"));
		unset($this->pdf->CellBorders);
  }



	function writeRapport()
	{
		global $__appvar;

		$this->pdf->AddPage();
   	$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor[r],$this->pdf->rapport_default_fontcolor[g],$this->pdf->rapport_default_fontcolor[b]);

		$this->pdf->templateVars['VOLKPaginas'] = $this->pdf->page;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		unset($this->pdf->CellBorders);
		 $totalen['rente']=0;

		$waarden=berekenPortefeuilleWaardeBewaarders($this->portefeuille, $this->rapportageDatum,false,'EUR',$this->rapportageDatumVanaf);
		vulTijdelijkeTabel($waarden,$this->portefeuille, $this->rapportageDatum);

		$query = "SELECT Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
		$rapDatumTekst=date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum);


		$query="SELECT SUM(actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage WHERE
		TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille='".$this->portefeuille."'";
		$DB->SQL($query); //echo $query."<br>\n";
		$DB->Query();
		$actueleWaarde = $DB->nextRecord();
		$portefeuilleWaarde=$actueleWaarde['actuelePortefeuilleWaardeEuro'];



$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
	  $query="SELECT
TijdelijkeRapportage.historischeWaarde,
TijdelijkeRapportage.beginPortefeuilleWaardeEuro as beginPortefeuilleWaardeEuroOld,
SUM(IF(TijdelijkeRapportage.type = 'fondsen',TijdelijkeRapportage.beginPortefeuilleWaardeEuro,0)) as beginPortefeuilleWaardeEuro,
TijdelijkeRapportage.historischeValutakoers,
SUM(IF(TijdelijkeRapportage.type = 'fondsen',TijdelijkeRapportage.historischeWaarde,0)) as historischeWaarde,
SUM(IF(TijdelijkeRapportage.type = 'rente' , (actuelePortefeuilleWaardeEuro),0)) AS rente,
SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS actuelePortefeuilleWaardeEuro,
 SUM(IF(TijdelijkeRapportage.type = 'fondsen',(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid * TijdelijkeRapportage.actueleValuta),0
 )) AS historischeWaardeEuro,
IF(TijdelijkeRapportage.type = 'rekening' ,actuelePortefeuilleWaardeInValuta, totaalAantal) as totaalAantal,
TijdelijkeRapportage.actueleFonds,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.actueleValuta,
TijdelijkeRapportage.fondsOmschrijving,
TijdelijkeRapportage.rekening,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.beleggingscategorieVolgorde,
TijdelijkeRapportage.type,
TijdelijkeRapportage.beleggingscategorieOmschrijving as categorieOmschrijving,
TijdelijkeRapportage.Bewaarder,
TijdelijkeRapportage.koersDatum
FROM
TijdelijkeRapportage
WHERE
TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille='".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
GROUP BY
TijdelijkeRapportage.fonds,TijdelijkeRapportage.Bewaarder,TijdelijkeRapportage.rekening
ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc,TijdelijkeRapportage.fondsOmschrijving";
		$DB->SQL($query);
		$DB->Query();

    while($data = $DB->nextRecord())
    {
      if($data['rekening'] <> '')
        $data['fondsOmschrijving'].=' '.substr($data['rekening'],0,strlen($data['rekening'])-3);

      $data['actuelePortefeuilleWaardeEuro']=$data['actuelePortefeuilleWaardeEuro']-$data['rente'];
      if($data['type']=='rekening')
        $ongerealiseerdResultaat=0;
      else
        $ongerealiseerdResultaat=$data['actuelePortefeuilleWaardeEuro']-$data['beginPortefeuilleWaardeEuro'];

      $aandeel=$data['actuelePortefeuilleWaardeEuro']/$portefeuilleWaarde*100;
      $ongerealiseerdResultaatProcent=($ongerealiseerdResultaat)/ABS($data['beginPortefeuilleWaardeEuro']) *100;
      //listarray($data);
      //echo $data['fondsOmschrijving']." ".$data['actuelePortefeuilleWaardeEuro']."-".$data['beginPortefeuilleWaardeEuro']."<br>\n";
      //echo $data['fondsOmschrijving']." $ongerealiseerdResultaatProcent=($ongerealiseerdResultaat)/ABS(".$data['beginPortefeuilleWaardeEuro'].") *100; <br>";

      $totalenCat[$data['categorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
      $totalenCat[$data['categorieOmschrijving']]['beginPortefeuilleWaardeEuro'] += $data['beginPortefeuilleWaardeEuro'];
      $totalenCat[$data['categorieOmschrijving']]['ongerealiseerdResultaat'] += $ongerealiseerdResultaat;
      $totalenCat[$data['categorieOmschrijving']]['aandeel'] += $aandeel;

      $totalen['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
      $totalen['beginPortefeuilleWaardeEuro'] += $data['beginPortefeuilleWaardeEuro'];
      $totalen['ongerealiseerdResultaat'] += $ongerealiseerdResultaat;
      $totalen['aandeel'] += $aandeel;

      if($data['categorieOmschrijving'] <> $lastcategorieOmschrijving)
      {
        if(!empty($lastcategorieOmschrijving))
        {
          $this->pdf->CellBorders = array('','','','','','T','T','T','');
          $this->pdf->row(array('','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],2),
          $this->formatGetal(($totalenCat[$lastcategorieOmschrijving]['ongerealiseerdResultaat'])/ABS($totalenCat[$lastcategorieOmschrijving]['beginPortefeuilleWaardeEuro'])*100,2,true).'%',
          $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['aandeel'],2,true).'%',''));
          unset($this->pdf->CellBorders);
          $this->pdf->ln();
        }
        if($this->pdf->getY() > 180)
          $this->pdf->addPage();
        $this->addHeader($data['categorieOmschrijving']);
      }
      $totalen['rente'] += $data['rente'];

      $this->pdf->row(array($data['fondsOmschrijving'],
                            $this->formatAantal($data['totaalAantal'],0,true),
                            $data['valuta'],
                            $this->formatGetal($data['actueleFonds'],2),
                            dbdate2form($data['koersDatum']),
                            $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],2),
                            $this->formatGetal($ongerealiseerdResultaatProcent,2,true).'%',
                            $this->formatGetal($aandeel,2).'%',
                            $data['Bewaarder']));
      $lastcategorieOmschrijving=$data['categorieOmschrijving'];
    }

    if(!empty($lastcategorieOmschrijving))
    {
          $this->pdf->CellBorders = array('','','','','','T','T','T','');
          $this->pdf->row(array('','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],2),
          $this->formatGetal(($totalenCat[$lastcategorieOmschrijving]['ongerealiseerdResultaat'])/ABS($totalenCat[$lastcategorieOmschrijving]['beginPortefeuilleWaardeEuro'])*100,2,true).'%',
          $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['aandeel'],2,true).'%',''));
          unset($this->pdf->CellBorders);
          $this->pdf->ln();
    }


    $this->pdf->CellBorders = array('','','','','','T','T','T','');
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->ln(2);
          $this->pdf->row(array('Beleggingen','','','','',$this->formatGetal($totalen['actuelePortefeuilleWaardeEuro'],2),
          $this->formatGetal(($totalen['ongerealiseerdResultaat'])/ABS($totalen['beginPortefeuilleWaardeEuro'])*100,2).'%',
          $this->formatGetal($totalen['aandeel'],2,true).'%',''));
    unset($this->pdf->CellBorders);
    $this->pdf->row(array('Opgelopen rente','','','','',$this->formatGetal($totalen['rente'],2),'',$this->formatGetal($totalen['rente']/$portefeuilleWaarde*100,2,true).'%'));
    $this->pdf->row(array('Totaal vermogen','','','','',$this->formatGetal($totalen['rente']+$totalen['actuelePortefeuilleWaardeEuro'],2),'',
    $this->formatGetal(($totalen['rente']/$portefeuilleWaarde*100)+($totalen['aandeel']),2,true).'%'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);

 			$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);


 		berekenPortefeuilleWaarde($this->portefeuille, $this->rapportageDatum,false,'EUR',$this->rapportageDatumVanaf);
    vulTijdelijkeTabel($waarden,$this->portefeuille, $this->rapportageDatum);
    $this->pdf->templateVars['VOLKPaginas2'] = $this->pdf->page;

  }



}
?>
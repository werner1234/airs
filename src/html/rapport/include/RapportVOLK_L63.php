<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/03/25 10:16:55 $
File Versie					: $Revision: 1.3 $

$Log: RapportVOLK_L63.php,v $
Revision 1.3  2018/03/25 10:16:55  rvv
*** empty log message ***

Revision 1.2  2018/02/07 17:22:29  rvv
*** empty log message ***

Revision 1.1  2016/01/09 18:58:30  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVOLK_L63
{
	function RapportVOLK_L63($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VOLK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Vermogensoverzicht";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
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

  function addHeader($categorie)
  {
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->CellBorders = array(array('T','U','L'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    //$this->preRow();
    $this->pdf->row(array($categorie,'','','','','','','','',''));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		//$this->preRow();
    $this->pdf->CellBorders = array(array('T','U','L'),array('U','T','L'),array('U','T','L'),array('U','T','L'),array('U','T','L'),array('U','T','L'),array('U','T','L'),array('U','T','L'),array('U','T','L'),array('U','T','L','R'));
    $this->pdf->row(array(" \n ",
                      vertaalTekst("Aantal",$this->pdf->rapport_taal)."\n ",
                      vertaalTekst("Koers",$this->pdf->rapport_taal)."\n ",
                      vertaalTekst("Valuta",$this->pdf->rapport_taal)."\n ",
                      vertaalTekst("Actuele Waarde",$this->pdf->rapport_taal)."\n ",
                      vertaalTekst("Kostprijs (geamortiseerd)",$this->pdf->rapport_taal)."",
                      vertaalTekst("Resultaat",$this->pdf->rapport_taal)."\n ",
                      vertaalTekst("Resultaat/\nKostprijs",$this->pdf->rapport_taal)."",
                      vertaalTekst("Belang",$this->pdf->rapport_taal)."\n ",
                      " \n "));
		unset($this->pdf->CellBorders);
    $this->pdf->SetFillColor($this->pdf->rapport_kop2_bgcolor['r'],$this->pdf->rapport_kop2_bgcolor['g'],$this->pdf->rapport_kop2_bgcolor['b']);
    $this->kopPrinted=true;   
 
  }



	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT Vermogensbeheerders.VerouderdeKoersDagen , Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM (Portefeuilles, Clienten)  Join Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
		$maxDagenOud=$portefeuilledata['VerouderdeKoersDagen'];
    $this->pdf->koersenMaxDagen=$maxDagenOud;
    
		$this->pdf->AddPage();
    $this->kopPrinted=false;
    $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
		$this->pdf->templateVars['VHOPaginas'] = $this->pdf->customPageNo;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		unset($this->pdf->CellBorders);
		 $totalen['rente']=0;




		$rapDatumTekst=date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum);


		$query="SELECT SUM(actuelePortefeuilleWaardeEuro)  / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage WHERE
		TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille='".$this->portefeuille."'";
		$DB->SQL($query); //echo $query."<br>\n";
		$DB->Query();
		$actueleWaarde = $DB->nextRecord();
		$portefeuilleWaarde=$actueleWaarde['actuelePortefeuilleWaardeEuro'];





$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
	  $query="SELECT
SUM(IF(TijdelijkeRapportage.type = 'rente' , (actuelePortefeuilleWaardeEuro),0)) / ".$this->pdf->ValutaKoersEind. " AS rente,
SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS actuelePortefeuilleWaardeEuro,
 SUM(IF(TijdelijkeRapportage.type = 'fondsen',(TijdelijkeRapportage.beginPortefeuilleWaardeEuro),0)) / TijdelijkeRapportage.historischeRapportageValutakoers AS historischeWaardeEuro,
IF(TijdelijkeRapportage.type = 'rekening' ,actuelePortefeuilleWaardeInValuta, totaalAantal) as totaalAantal,
TijdelijkeRapportage.actueleFonds,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.actueleValuta,
TijdelijkeRapportage.fondsOmschrijving,
TijdelijkeRapportage.rekening,
TijdelijkeRapportage.beleggingscategorie,
Beleggingscategorien.Afdrukvolgorde,
TijdelijkeRapportage.type,
Beleggingscategorien.Omschrijving as categorieOmschrijving,
round((UNIX_TIMESTAMP(TijdelijkeRapportage.rapportageDatum) - UNIX_TIMESTAMP(TijdelijkeRapportage.koersDatum))/86400) as koersLeeftijd,
TijdelijkeRapportage.Bewaarder
FROM
TijdelijkeRapportage
LEFT Join Beleggingscategorien ON TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie
WHERE
TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille='".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
GROUP BY
TijdelijkeRapportage.fonds,TijdelijkeRapportage.Bewaarder,TijdelijkeRapportage.rekening
ORDER BY Beleggingscategorien.Afdrukvolgorde,TijdelijkeRapportage.fondsOmschrijving";
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
        $ongerealiseerdResultaat=$data['actuelePortefeuilleWaardeEuro']-$data['historischeWaardeEuro'];

      $aandeel=$data['actuelePortefeuilleWaardeEuro']/$portefeuilleWaarde*100;
      $ongerealiseerdResultaatProcent=($ongerealiseerdResultaat)/ABS($data['historischeWaardeEuro']) *100;

      $totalenCat[$data['categorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
      $totalenCat[$data['categorieOmschrijving']]['historischeWaardeEuro'] += $data['historischeWaardeEuro'];
      $totalenCat[$data['categorieOmschrijving']]['ongerealiseerdResultaat'] += $ongerealiseerdResultaat;
      $totalenCat[$data['categorieOmschrijving']]['aandeel'] += $aandeel;

      $totalen['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
      $totalen['historischeWaardeEuro'] += $data['historischeWaardeEuro'];
      $totalen['ongerealiseerdResultaat'] += $ongerealiseerdResultaat;
      $totalen['aandeel'] += $aandeel;

      if($data['categorieOmschrijving'] <> $lastcategorieOmschrijving)
      {
        if(!empty($lastcategorieOmschrijving))
        {
          $this->pdf->CellBorders = array('','','','','T','T','T','T','T');
          $this->preRow("T");
          $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
          $this->pdf->row(array('','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],2),
            $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['historischeWaardeEuro'],2),
            $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['ongerealiseerdResultaat'],2),
            $this->formatGetal(($totalenCat[$lastcategorieOmschrijving]['ongerealiseerdResultaat'])/ABS($totalenCat[$lastcategorieOmschrijving]['historischeWaardeEuro'])*100,2).'%',
            $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['aandeel'],2).'%',''));
          unset($this->pdf->CellBorders);
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
          $this->pdf->ln();
        }
        if($this->pdf->getY() > 180)
          $this->pdf->addPage();
        $this->addHeader($data['categorieOmschrijving']);
      }
      $totalen['rente'] += $data['rente'];

      if($data['koersLeeftijd'] > $maxDagenOud && $data['actueleFonds'] <> 0)
			  $markering="*";
			else
			  $markering="";
      $this->preRow();
      $this->pdf->row(array($data['fondsOmschrijving'],
                            $this->formatGetal($data['totaalAantal'],0),
                            $this->formatGetal($data['actueleFonds'],2).$markering,
                            $data['valuta'],
                            $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],2),
                            $this->formatGetal($data['historischeWaardeEuro'],2),
                            $this->formatGetal($ongerealiseerdResultaat,2),
                            $this->formatGetal($ongerealiseerdResultaatProcent,2).'%',
                            $this->formatGetal($aandeel,2).'%',''));
      $lastcategorieOmschrijving=$data['categorieOmschrijving'];
      if($this->kopPrinted==true)
      {
        $this->pdf->Line($this->pdf->marge,$this->pdf->GetY()-$this->pdf->rowHeight,$this->pdf->marge+array_sum($this->pdf->widths),$this->pdf->GetY()-$this->pdf->rowHeight);      
        $this->kopPrinted=false;
      }
    }

    if(!empty($lastcategorieOmschrijving))
    {
          $this->pdf->CellBorders = array('','','','','T','T','T','T','T');
          $this->preRow('T');
          $this->pdf->row(array('','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],2),
          $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['historischeWaardeEuro'],2),
          $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['ongerealiseerdResultaat'],2),
          $this->formatGetal(($totalenCat[$lastcategorieOmschrijving]['ongerealiseerdResultaat'])/ABS($totalenCat[$lastcategorieOmschrijving]['historischeWaardeEuro'])*100,2).'%',
          $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['aandeel'],2).'%',''));
          unset($this->pdf->CellBorders);
          $this->pdf->ln();
    }


    $this->pdf->CellBorders = array('','','','','T','T','T','T','T');
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->ln(2);
    $this->preRow('F');
          $this->pdf->row(array('Beleggingen','','','',$this->formatGetal($totalen['actuelePortefeuilleWaardeEuro'],2),
          $this->formatGetal($totalen['historischeWaardeEuro'],2),
          $this->formatGetal($totalen['ongerealiseerdResultaat'],2),
          $this->formatGetal(($totalen['ongerealiseerdResultaat'])/ABS($totalen['historischeWaardeEuro'])*100,2).'%',
          $this->formatGetal($totalen['aandeel'],2).'%',''));
    if($this->kopPrinted==true)
    {
      $this->pdf->Line($this->pdf->marge,$this->pdf->GetY()-$this->pdf->rowHeight,$this->pdf->marge+array_sum($this->pdf->widths),$this->pdf->GetY()-$this->pdf->rowHeight);      
      $this->kopPrinted=false;
    }
    unset($this->pdf->CellBorders);
    $this->preRow('E');
    $this->pdf->row(array('Opgelopen rente','','','',$this->formatGetal($totalen['rente'],2),'','','',$this->formatGetal($totalen['rente']/$portefeuilleWaarde*100,2).'%',''));
    $this->preRow('E');
    $this->pdf->row(array('Totaal vermogen','','',$this->pdf->rapportageValuta,$this->formatGetal($totalen['rente']+$totalen['actuelePortefeuilleWaardeEuro'],2),'','','',
    $this->formatGetal(($totalen['rente']/$portefeuilleWaarde*100)+($totalen['aandeel']),2).'%',''));

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
    //$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);

  }
  
  function preRow($extra)
  {
    $this->pdf->CheckPageBreak($this->pdf->rowHeight);
    
    if($extra=='T' || $extra=='E' || $extra=='F')
    {
       $this->pdf->CellBorders = array(array('T','U','L'),array('U','T','L'),array('U','T','L'),array('U','T','L'),array('U','T','L'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T','R'));
    }
    elseif($extra=='F')
    {
      $this->pdf->CellBorders = array(array('L','T'),array('L','T'),array('L','T'),array('L','T'),array('L','T'),'T','T','T','T',array('R','T'));
    }
    elseif($this->pdf->GetY()< 51)
    {
      $this->pdf->CellBorders = array(array('L','T'),'T','T','T','T','T','T','T','T',array('R','T'));
    }
    elseif($this->pdf->GetY()> 188 || $extra=='E')
    {
      $this->pdf->CellBorders = array(array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),'U','U','U','U',array('R','U'));
    }
    else
      $this->pdf->CellBorders = array('L','L','L','L','L','','','','','R');
  }
}
?>
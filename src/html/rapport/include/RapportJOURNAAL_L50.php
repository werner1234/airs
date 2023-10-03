<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/06/27 16:21:20 $
File Versie					: $Revision: 1.12 $

$Log: RapportJOURNAAL.php,v $

*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportJOURNAAL_L50
{
  function RapportJOURNAAL_L50($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "INDEX";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);


    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;

  }



  function writeRapport()
  {
    global $__appvar;
    // listarray($this->pdf->lastPOST);
    $DB=new DB();

    $query="SELECT Rekening FROM Rekeningen WHERE Portefeuille = '".$this->portefeuille."'";
    $DB->SQL($query);
    $DB->Query();
    $rekeningen=array();
    while($rekening = $DB->nextRecord())
    {
      $rekeningen[]=$rekening['Rekening'];
    }

    if($this->pdf->lastPOST['verdeling']=='portefeuille')
    {
      $groupby=' GROUP BY Rekeningmutaties.Grootboekrekening';
      $verdelingTxt="Portefeuille";
    }
    else
    {
      $groupby=' GROUP BY Rekeningmutaties.Rekening, Rekeningmutaties.Grootboekrekening';
      $verdelingTxt="Rekening";
    }


    $query="SELECT SUM(Debet * Valutakoers) as Debet, SUM(Credit * Valutakoers) as Credit, SUM(Rekeningmutaties.Bedrag) as Bedrag, Rekening
    FROM Rekeningmutaties 
    WHERE YEAR(Boekdatum)='".date('Y',db2jul($this->rapportageDatumVanaf))."' AND 
          Boekdatum <='".$this->rapportageDatumVanaf."' AND 
          Rekening IN('".implode("','",$rekeningen)."') GROUP BY Rekening";
    $DB->SQL($query);
    $DB->Query();
    while($data = $DB->nextRecord())
    {
      $begin[$data['Rekening']]+=$data['Credit']-$data['Debet'];
      $begin[$this->portefeuille]+=$data['Credit']-$data['Debet'];
    }

    $query="SELECT SUM(Debet * Valutakoers) as Debet, SUM(Credit * Valutakoers) as Credit, SUM(Rekeningmutaties.Bedrag) as Bedrag, Rekening
    FROM Rekeningmutaties 
    WHERE YEAR(Boekdatum)='".date('Y',db2jul($this->rapportageDatumVanaf))."' AND 
          Boekdatum <='".$this->rapportageDatum."' AND 
          Rekening IN('".implode("','",$rekeningen)."') GROUP BY Rekening";
    $DB->SQL($query);
    $DB->Query();
    while($data = $DB->nextRecord())
    {
      $eind[$data['Rekening']]+=($data['Credit']-$data['Debet']);
      $eind[$this->portefeuille]+=($data['Credit']-$data['Debet']);
    }

    $query = "SELECT '".$this->portefeuille."' as Portefeuille, ".
      "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) as Debet, ".
      "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers) as Credit, ".
      "Rekeningmutaties.Rekening, ".
      "Rekeningmutaties.Grootboekrekening, ".
      "Grootboekrekeningen.Omschrijving AS gbOmschrijving ".
      "FROM Rekeningmutaties, Grootboekrekeningen ".
      "WHERE  ".
      " Rekeningmutaties.Rekening IN('".implode("','",$rekeningen)."') ".
      "AND Rekeningmutaties.Verwerkt = '1' ".
      "AND Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' ".
      "AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
      "AND Grootboekrekeningen.Afdrukvolgorde IS NOT NULL ".
      "AND Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening ".
      "AND (Grootboekrekeningen.Kosten = '1' OR Grootboekrekeningen.Opbrengst = '1' OR Grootboekrekeningen.Onttrekking = '1' OR Grootboekrekeningen.Storting = '1' OR Grootboekrekeningen.Kruispost = '1') ".
      " $groupby ORDER BY Grootboekrekeningen.Afdrukvolgorde, Rekeningmutaties.Boekdatum";

    $DB->SQL($query);
    $DB->Query();
    while($mutaties = $DB->nextRecord())
    {
      $jourmaalData[$mutaties[$verdelingTxt]][$mutaties['Grootboekrekening']]=$mutaties;
    }

    $query = "SELECT Fondsen.Omschrijving, '".$this->portefeuille."' as Portefeuille,".
      "Fondsen.Fondseenheid, 
    Rekeningmutaties.Rekening,".
      "Rekeningmutaties.Boekdatum, ".
      "Rekeningmutaties.Transactietype,
		 Rekeningmutaties.Valuta,
		 Rekeningmutaties.Afschriftnummer,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, Rekeningmutaties.Fonds,  ".
      "Rekeningmutaties.Fondskoers, ".
      "Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers as Debet, ".
      "Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers as Credit, ".
      "Rekeningmutaties.Valutakoers ".
      "FROM Rekeningmutaties, Fondsen, Grootboekrekeningen ".
      "WHERE ".
      "Rekeningmutaties.Fonds = Fondsen.Fonds AND ".
      "Rekeningmutaties.Rekening IN('".implode("','",$rekeningen)."') AND ".
      "Rekeningmutaties.Verwerkt = '1' AND ".
      "Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND ".
      "Rekeningmutaties.Transactietype <> 'B' AND ".
      "Grootboekrekeningen.FondsAanVerkoop = '1' AND ".
      "Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
      "Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
      "ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id ";
    $DB->SQL($query);
    $DB->Query();
    while($mutaties = $DB->nextRecord())
    {
      if(substr($mutaties['Transactietype'],0,1)=='A')
      {
        $jourmaalData[$mutaties[$verdelingTxt]]['FONDS aankoop']['Debet']+=$mutaties['Debet'];
        $jourmaalData[$mutaties[$verdelingTxt]]['FONDS aankoop']['Credit']+=$mutaties['Credit'];
      }
      if(substr($mutaties['Transactietype'],0,1)=='V')
      {

        $historie = berekenHistorischKostprijs($this->portefeuille, $mutaties['Fonds'], $mutaties['Boekdatum'],$this->pdf->rapportageValuta,substr($this->rapportageDatum,0,4).'-01-01');
        $beginwaarde=(abs($mutaties['Aantal'])*$historie['beginwaardeLopendeJaar']*$historie['beginwaardeValutaLopendeJaar']*$historie['fondsEenheid']);
        //  $result[$mutaties[$verdelingTxt]]+=($mutaties['Credit'] - $beginwaarde);
        $jourmaalData[$mutaties[$verdelingTxt]]['FONDS verkoop']['Credit']+= $beginwaarde;
      }
    }

    $this->pdf->excelData[]=array('','Rapport journaalpost');
    foreach($jourmaalData as $verdeling=>$data)
    {
      $totalen=array();
      $this->pdf->excelData[]=array($verdelingTxt.' '.$verdeling);
      $this->pdf->excelData[]=array('Grootboek','Af','Bij');
      foreach($data as $grootboek=>$mutatie)
      {
        $this->pdf->excelData[]=array($grootboek,round($mutatie['Debet'],2),round($mutatie['Credit'],2));
        $totalen['Debet']+=$mutatie['Debet'];
        $totalen['Credit']+=$mutatie['Credit'];
      }
      $mutatieWaarde=$eind[$verdeling]-$begin[$verdeling];
      $result=($mutatieWaarde-($totalen['Credit']-$totalen['Debet']));
      if($result < 0)
      {
        $result=abs($result);
        $totalen['Debet']+=$result;
        $this->pdf->excelData[]=array("RESULT",round($result,2));
      }
      else
      {
        $totalen['Credit']+=$result;
        $this->pdf->excelData[]=array("RESULT",'',round($result,2));
      }

      $this->pdf->excelData[]=array();
      $this->pdf->excelData[]=array('TOTAAL',round($totalen['Debet'],2),round($totalen['Credit'],2));
      $this->pdf->excelData[]=array();
      $this->pdf->excelData[]=array('MUTATIE',round($mutatieWaarde,2));
      $this->pdf->excelData[]=array();
      $this->pdf->excelData[]=array('Saldo begin',round($begin[$verdeling],2));
      $this->pdf->excelData[]=array('Saldo eind',round($eind[$verdeling],2));
      $this->pdf->excelData[]=array();
      $this->pdf->excelData[]=array();
      //listarray($this->pdf->excelData);exit;
      // $this->pdf->excelData[]=array('Saldo verschil',$eind['actuelePortefeuilleWaardeEuro']-$begin['actuelePortefeuilleWaardeEuro']);
    }

  }
}
?>

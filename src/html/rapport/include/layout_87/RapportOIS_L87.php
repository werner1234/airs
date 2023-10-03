<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/01/19 13:36:46 $
File Versie					: $Revision: 1.2 $

$Log: RapportOIS_L87.php,v $

*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"] . "/html/rapport/rapportVertaal.php");

class RapportOIS_L87
{
  function RapportOIS_L87($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "OIS";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->pdf->rapport_titel = 'Rendement aandelen vergeleken met benchmarks sectoren';
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
    $this->beleggingscategorieFilter = 'AAND';
    $this->gerealiseerd = array();
    $this->verdeling='Beleggingssector';
    $this->benchmarkTotalen=false;
  }
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde, $dec, ",", ".");
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
          if ($decimaal != '0' && !isset($newDec))
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
  
  function gerealiseerdResultaat($verdeling = 'Beleggingssector')
  {
    global $__appvar;
    
    if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
    {
      $koersQuery = " / (SELECT Koers FROM Valutakoersen WHERE Valuta='" . $this->pdf->rapportageValuta . "' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    }
    else
    {
      $koersQuery = "";
    }
    
    if ($this->beleggingscategorieFilter <> '')
    {
      $filter = "AND BeleggingscategoriePerFonds.Beleggingscategorie='" . $this->beleggingscategorieFilter . "'";
    }
    else
    {
      $filter = '';
    }
    
    $query = "SELECT Fondsen.Omschrijving,Fondsen.ISINCode, " .
      "Fondsen.Fondseenheid, " .
      "Rekeningmutaties.Boekdatum, " .
      "Rekeningmutaties.Transactietype,
	     Rekeningmutaties.Valuta,
		   Rekeningmutaties.Fonds,
		   Rekeningmutaties.Afschriftnummer,
       Rekeningmutaties.omschrijving as rekeningOmschrijving,
		   Rekeningmutaties.Aantal AS Aantal," .
      "Rekeningmutaties.Fondskoers, " .
      "Rekeningmutaties.Debet as Debet, " .
      "Rekeningmutaties.Credit as Credit, " .
      "Rekeningmutaties.Valutakoers,
		   1 $koersQuery as Rapportagekoers,
		   BeleggingssectorPerFonds.Beleggingssector,
		   Fondsen.standaardSector,
		   BeleggingssectorPerFonds.AttributieCategorie,
		   BeleggingscategoriePerFonds.Beleggingscategorie,
       BeleggingssectorPerFonds.Regio " .
      "FROM Rekeningmutaties
      JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
      JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
      JOIN Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
	    LEFT Join BeleggingssectorPerFonds ON Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder='" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'
		  LEFT Join BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder='" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "' " .
      "WHERE " .
      "Rekeningen.Portefeuille = '" . $this->portefeuille . "' AND " .
      "Rekeningmutaties.Verwerkt = '1' AND " .
      "Rekeningmutaties.Transactietype <> 'B' AND " .
      "Grootboekrekeningen.FondsAanVerkoop = '1' AND Rekeningmutaties.Fonds<>'' AND " .
      "Rekeningmutaties.Boekdatum > '" . $this->rapportageDatumVanaf . "' AND " .
      "Rekeningmutaties.Boekdatum <= '" . $this->rapportageDatum . "' $filter " .
      "ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    while ($mutaties = $DB->NextRecord())
    {
      if ($mutaties['Beleggingssector'] == '')
      {
        if($mutaties['standaardSector'] <> '')
          $mutaties['Beleggingssector'] = $mutaties['standaardSector'];
        else
          $mutaties['Beleggingssector'] = 'geen sec';
      }
      if ($mutaties['beleggingscategorie'] == '')
      {
        $mutaties['beleggingscategorie'] = 'geen cat';
      }
      if ($mutaties['AttributieCategorie'] == '')
      {
        $mutaties['AttributieCategorie'] = 'geen att';
      }
      
      
      //if($mutaties[Transactietype] != "A/S")
      $mutaties['Aantal'] = abs($mutaties['Aantal']);
      $t_aankoop_waarde = 0;
      $t_verkoop_waarde = 0;
      
      $resultaatlopende = 0;
      $resultaatvoorgaande = 0;
      
      
      switch ($mutaties['Transactietype'])
      {
        case "A" :
        case "A/O" :
        case "A/S" :
        case "D" :
        case "S" :
          // Aankoop // Aankoop / openen // Aankoop / sluiten // Deponering
          $t_aankoop_waarde = abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
          break;
        case "B" :
          // Beginstorting
          break;
        case "L" :
        case "V" :
        case "V/O" :
        case "V/S" :
          // Lichting // Verkopen // Verkopen / openen // Verkopen / sluiten
          $t_verkoop_waarde = abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
          break;
        default :
          $_error = "Fout ongeldig tranactietype!!";
          break;
      }
      
      if ($mutaties['Transactietype'] == "L" || $mutaties['Transactietype'] == "V" || $mutaties['Transactietype'] == "V/S" || $mutaties['Transactietype'] == "A/S")
      {
        $historie = berekenHistorischKostprijs($this->portefeuille, $mutaties['Fonds'], $mutaties['Boekdatum'], $this->pdf->rapportageValuta, $this->rapportageDatumVanaf, $mutaties['id']);
        if ($mutaties['Transactietype'] == "A/S")
        {
          $historischekostprijs = ($mutaties['Aantal'] * -1) * $historie['historischeWaarde'] * $historie['historischeValutakoers'] * $mutaties['Fondseenheid'];
          $beginditjaar = ($mutaties['Aantal'] * -1) * $historie['beginwaardeLopendeJaar'] * $historie['beginwaardeValutaLopendeJaar'] * $mutaties['Fondseenheid'];
        }
        else
        {
          $historischekostprijs = $mutaties['Aantal'] * $historie['historischeWaarde'] * $historie['historischeValutakoers'] * $mutaties['Fondseenheid'];
          $beginditjaar = $mutaties['Aantal'] * $historie['beginwaardeLopendeJaar'] * $historie['beginwaardeValutaLopendeJaar'] * $mutaties['Fondseenheid'];
        }
        if ($this->pdf->rapportageValuta != 'EUR' && $mutaties['Valuta'] == $this->pdf->rapportageValuta)
        {
          $historischekostprijs = $historischekostprijs / $historie['historischeValutakoers'];
          $beginditjaar = $beginditjaar / getValutaKoers($this->pdf->rapportageValuta, date("Y", db2jul($this->rapportageDatum) . '-01-01'));
        }
        elseif ($this->pdf->rapportageValuta != 'EUR')
        {
          $historischekostprijs = $historischekostprijs / $historie['historischeRapportageValutakoers'];
          $beginditjaar = $beginditjaar / getValutaKoers($this->pdf->rapportageValuta, date("Y", db2jul($this->rapportageDatum) . '-01-01'));
        }
        if ($historie['voorgaandejarenActief'] == 0)
        {
          $resultaatlopende = $t_verkoop_waarde - $historischekostprijs;
          if ($mutaties['Transactietype'] == "A/S")
          {
            $resultaatlopende = $t_aankoop_waarde - $historischekostprijs;
          }
        }
        else
        {
          $resultaatvoorgaande = $beginditjaar - $historischekostprijs;
          $resultaatlopende = $t_verkoop_waarde - $beginditjaar;
          if ($mutaties['Transactietype'] == "A/S")
          {
            $resultaatvoorgaande = $beginditjaar - $historischekostprijs;
            $resultaatlopende = ($t_aankoop_waarde * -1) - $beginditjaar;
          }
        }
        
        
      }
      else
      {
        $historischekostprijs = 0;
        $resultaatvoorgaande = 0;
        $percentageTotaal = 0;
      }
      
      $this->gerealiseerd[$mutaties[$verdeling]][$mutaties['Fonds']]['fonds'] = $mutaties['Fonds'];
      $this->gerealiseerd[$mutaties[$verdeling]][$mutaties['Fonds']]['fondsOmschrijving'] = $mutaties['Omschrijving'];
      $this->gerealiseerd[$mutaties[$verdeling]][$mutaties['Fonds']]['ISINCode'] = $mutaties['ISINCode'];
      $this->gerealiseerd[$mutaties[$verdeling]][$mutaties['Fonds']]['Valuta'] = $mutaties['Valuta'];
      
      $this->gerealiseerd[$mutaties[$verdeling]][$mutaties['Fonds']]['resultaatvoorgaande'] += $resultaatvoorgaande;
      $this->gerealiseerd[$mutaties[$verdeling]][$mutaties['Fonds']]['resultaat'] += $resultaatlopende;
      $this->gerealiseerd[$mutaties[$verdeling]][$mutaties['Fonds']]['aankoopWaarde'] += $t_aankoop_waarde;
      $this->gerealiseerd[$mutaties[$verdeling]][$mutaties['Fonds']]['verkoopWaarde'] += $t_verkoop_waarde;
//       $transactietypen[] = $mutaties['Transactietype'];
    }
    
    return $this->gerealiseerd;
  }
  /*
  function getDividend($fonds)
  {
    global $__appvar;
    
    if($fonds=='')
      return 0;
    
    $query="SELECT rapportageDatum,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro as actuelePortefeuilleWaardeEuro,
         TijdelijkeRapportage.type,
         TijdelijkeRapportage.totaalAantal
     FROM TijdelijkeRapportage
     WHERE
       TijdelijkeRapportage.fonds='$fonds' AND
       portefeuille = '".$this->portefeuille."'".$__appvar['TijdelijkeRapportageMaakUniek']. "
     GROUP BY rapportageDatum,TijdelijkeRapportage.type";
    
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $totaal=0;
    while($data = $DB->nextRecord())
    {
      if($data['type']=='rente')
        $rente[$data['rapportageDatum']]=$data['actuelePortefeuilleWaardeEuro'];
      elseif($data['type']=='fondsen')
        $aantal[$data['rapportageDatum']]=$data['totaalAantal'];
    }
    
    $totaal+=($rente[$this->rapportageDatum]-$rente[$this->rapportageDatumVanaf]);
    $totaalCorrected=$totaal;
    
    $query="SELECT Boekdatum,(Debet*Valutakoers) as Debet,(Credit*valutakoers) as Credit,Bedrag,Rekeningmutaties.Omschrijving
     FROM Rekeningmutaties
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
     JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND
     Rekeningmutaties.Boekdatum >= '".	$this->rapportageDatumVanaf."' AND
     Rekeningmutaties.Boekdatum <= '".	$this->rapportageDatum."' AND
     Rekeningmutaties.Fonds='$fonds' AND
     Grootboekrekeningen.Opbrengst=1";
    $DB->SQL($query);
    $DB->Query();
    //echo "$query <br>\n";
    while($data = $DB->nextRecord())
    {
      $boekdatum=substr($data['Boekdatum'],0,10);
      if(!isset($aantal[$data['Boekdatum']]))
      {
        $fondsAantal=fondsAantalOpdatum($this->portefeuille,$fonds,$data['Boekdatum']);
        $aantal[$boekdatum]=$fondsAantal['totaalAantal'];
      }
      $aandeel=1;
      
      if($aantal[$boekdatum] > $aantal[$this->rapportageDatum])
      {
        $aandeel=$aantal[$this->rapportageDatum]/$aantal[$boekdatum];
      }
      // echo "$fonds $aandeel  $boekdatum ".$this->rapportageDatum." ".($data['Credit']-$data['Debet'])."<br>\n";
      $totaal+=($data['Credit']-$data['Debet']);
      $totaalCorrected+=(($data['Credit']-$data['Debet'])*$aandeel);
    }
    
    
    return array('totaal'=>$totaal,'corrected'=>$totaalCorrected);
  }
  */
  
  
  function fondsPerformance($fonds,$datumBegin,$datumEind,$debug=false)
  {
    global $__appvar;
    $DB=new DB();
   // $datum=$this->getmaanden(db2jul($datumBegin),db2jul($datumEind));
    $totaalPerf = 100;
    $datum=array(array('start'=>$datumBegin,'stop'=>$datumEind));
    
    if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
      $koersQueryBoekdatum =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    else
      $koersQueryBoekdatum = "";
    
    foreach ($datum as $periode)
    {
      $datumBegin = $periode['start'];
      $datumEind = $periode['stop'];
      
      $RapStartJaar = date("Y", db2jul($datumBegin));
      if((db2jul($this->pdf->PortefeuilleStartdatum) >= db2jul($datumBegin)) && substr($datumBegin,5,6) <> '01-01')
        $datumBeginATT=date("Y-m-d",db2jul($datumBegin));
      else
        $datumBeginATT=$datumBegin;
      
      if(substr($datumBegin,5,6) == '01-01')
        $datumBeginWeging=date("Y-m-d",db2jul($datumBegin)-86400);
      else
        $datumBeginWeging=$datumBeginATT;
      
      $fondsQuery = 'Fonds';
      
      if(is_array($fonds))
        $fondsenWhere = " IN('".implode('\',\'',$fonds)."') ";
      else
      {
        if($fonds=='')
          return array();
        $fondsenWhere = " IN('$fonds') ";
      }
      
      if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
      {
        $koersQueryDatumBegin =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= '$datumBegin' ORDER BY Datum DESC LIMIT 1 ) ";
        $koersQueryDatumEind =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= '$datumEind' ORDER BY Datum DESC LIMIT 1 ) ";
      }
      else
      {
        $koersQueryDatum = "";
        $koersQueryDatumEind='';
      }
      
      $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) $koersQueryDatumBegin as actuelePortefeuilleWaardeEuro
               FROM TijdelijkeRapportage
               WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumBegin' AND
               (TijdelijkeRapportage.rekening $fondsenWhere OR TijdelijkeRapportage.fonds $fondsenWhere )".$__appvar['TijdelijkeRapportageMaakUniek'];
      $DB->SQL($query);
      $DB->Query();
      $start = $DB->NextRecord();
      $beginwaarde = $start['actuelePortefeuilleWaardeEuro'];
  

      
      $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) $koersQueryDatumEind as actuelePortefeuilleWaardeEuro
                FROM TijdelijkeRapportage
                WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumEind' AND
               (TijdelijkeRapportage.rekening $fondsenWhere OR TijdelijkeRapportage.fonds $fondsenWhere ) ".$__appvar['TijdelijkeRapportageMaakUniek'] ;
      $DB->SQL($query);
      $DB->Query();
      $eind = $DB->NextRecord();
      $eindwaarde = $eind['actuelePortefeuilleWaardeEuro'];
  
 
      if($beginwaarde == 0)
      {
        $query = "SELECT Rekeningmutaties.Boekdatum as Boekdatum,
	      (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) $koersQueryBoekdatum as waarde FROM  (Rekeningen, Portefeuilles)
	                LEFT JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening  WHERE Rekeningmutaties.$fondsQuery $fondsenWhere AND
	                Rekeningen.Portefeuille = '".$this->portefeuille."' AND	Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
	                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' ORDER BY Rekeningmutaties.Boekdatum asc LIMIT 1 ";
        $DB->SQL($query);
        $DB->Query();
        $start = $DB->NextRecord();
        if($start['Boekdatum'] != '')
        {
          $datumBegin = $start['Boekdatum'];
          $beginTransactieWaarde = $start['waarde'];
        }
        
        $beginCorrectie=true;
      }
      else
        $beginCorrectie=false;
      
      if($eindwaarde == 0)
      {
        $query = "SELECT Rekeningmutaties.Boekdatum + INTERVAL 1 DAY as Boekdatum FROM  (Rekeningen, Portefeuilles)
	                LEFT JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening  WHERE Rekeningmutaties.$fondsQuery $fondsenWhere AND
	                Rekeningen.Portefeuille = '".$this->portefeuille."' AND	Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
	                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' ORDER BY Rekeningmutaties.Boekdatum desc LIMIT 1 ";
        $DB->SQL($query);
        $DB->Query();
        $eind = $DB->NextRecord();
        if($eind['Boekdatum'] != '')
          $datumEind = $eind['Boekdatum'];
        $eindCorrectie=true;
      }
      else
        $eindCorrectie=false;
      
      
      $queryAttributieStortingenOntrekkingenRekening = "SELECT
	               (((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$datumBeginWeging."'))  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)$koersQueryBoekdatum )))*-1  AS gewogen, ".
        "((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)$koersQueryBoekdatum - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)$koersQueryBoekdatum)*-1   AS totaal ".
        "FROM  Rekeningmutaties JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	               WHERE (Rekeningmutaties.Fonds <> '' OR Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1) AND ".
        "Rekeningmutaties.Verwerkt = '1' AND ".
        "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
        "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               Rekeningmutaties.Rekening $fondsenWhere ";
      $DB->SQL($queryAttributieStortingenOntrekkingenRekening);
      $DB->Query();
      $AttributieStortingenOntrekkingenRekening=array();
      while($tmp=$DB->nextRecord())
      {
        $AttributieStortingenOntrekkingenRekening['gewogen']+=$tmp['gewogen'];
        $AttributieStortingenOntrekkingenRekening['totaal']+=$tmp['totaal'];
      }
 
      
      $queryRekeningDirecteKostenOpbrengsten = "SELECT
                 (((TO_DAYS('$datumEind') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('$datumEind') - TO_DAYS('$datumBeginWeging')) * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum )))  AS gewogen,
                 ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum)  AS totaal
                 FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                 JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
                 WHERE
                 (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND   Rekeningmutaties.Fonds = '' AND
                 Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                 Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBeginATT' AND
                 Rekeningmutaties.Boekdatum <= '$datumEind' AND
                 Rekeningmutaties.Rekening $fondsenWhere  ";
      $DB->SQL($queryRekeningDirecteKostenOpbrengsten);
      $DB->Query();
      $RekeningDirecteKostenOpbrengsten=array();
      while($tmp=$DB->nextRecord())
      {
        $RekeningDirecteKostenOpbrengsten['gewogen']+=$tmp['gewogen'];
        $RekeningDirecteKostenOpbrengsten['totaal']+=$tmp['totaal'];
      }
      //$RekeningDirecteKostenOpbrengsten = $DB->NextRecord();
      
      $queryFondsDirecteKostenOpbrengsten = "SELECT (((TO_DAYS('$datumEind') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('$datumEind') - TO_DAYS('$datumBeginWeging')) * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum )))  AS gewogen,
                       ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) $koersQueryBoekdatum AS totaal
                FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
                WHERE
                (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
                Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBeginATT' AND
                Rekeningmutaties.Boekdatum <= '$datumEind' AND
                Rekeningmutaties.Fonds $fondsenWhere";
      $DB->SQL($queryFondsDirecteKostenOpbrengsten);
      $DB->Query();
      $FondsDirecteKostenOpbrengsten=array();
      while($tmp=$DB->nextRecord())
      {
        $FondsDirecteKostenOpbrengsten['gewogen']+=$tmp['gewogen'];
        $FondsDirecteKostenOpbrengsten['totaal']+=$tmp['totaal'];
      }
      
      $queryAttributieStortingenOntrekkingen = "SELECT ".
        "(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$datumBeginWeging."')) ".
        "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum )))  AS gewogen, ".
        " ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum) AS totaal".
        " FROM  (Rekeningen, Portefeuilles)
	               Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
        "WHERE ".
        "Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
        "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
        "Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Transactietype <> 'B' AND ".
        "Rekeningmutaties.Boekdatum > '".$datumBeginATT."' AND ".
        "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
        "Rekeningmutaties.Grootboekrekening = 'FONDS' AND Rekeningmutaties.Fonds $fondsenWhere ";
      $DB->SQL($queryAttributieStortingenOntrekkingen); //echo "stort $queryAttributieStortingenOntrekkingen <br>\n";
      $DB->Query();
      $AttributieStortingenOntrekkingen=array();
      while($tmp=$DB->nextRecord())
      {
        $AttributieStortingenOntrekkingen['gewogen']+=$tmp['gewogen'];
        $AttributieStortingenOntrekkingen['totaal']+=$tmp['totaal'];
      }

      $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];
      //    $AttributieStortingenOntrekkingen['totaal'] +=$AttributieStortingenOntrekkingenRekening['totaal'];
      $query = "SELECT (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers) $koersQueryBoekdatum as totaal
 	              FROM Rekeningmutaties,Rekeningen
	              WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	              Rekeningen.Rekening $fondsenWhere  AND
 	              Rekeningmutaties.Verwerkt = '1' AND
	              Rekeningmutaties.Boekdatum > '$datumBeginATT' AND
	              Rekeningmutaties.Boekdatum <= '$datumEind'";
      
      $DB->SQL($query);
      $DB->Query();
      while($data = $DB->nextRecord());
        $AttributieStortingenOntrekkingen['totaal'] -=$data['totaal'];
      
      $directeKostenOpbrengsten['totaal'] = $RekeningDirecteKostenOpbrengsten['totaal'] + $FondsDirecteKostenOpbrengsten['totaal'];
      $directeKostenOpbrengsten['gewogen'] = $RekeningDirecteKostenOpbrengsten['gewogen'] + $FondsDirecteKostenOpbrengsten['gewogen'];
      
      if($beginCorrectie)
      {
        $AttributieStortingenOntrekkingen['gewogen']=$AttributieStortingenOntrekkingen['totaal'];
        $directeKostenOpbrengsten['gewogen']=$directeKostenOpbrengsten['totaal'];
      }
      if($eindCorrectie)
      {
        $AttributieStortingenOntrekkingen['gewogen']=0;
        $directeKostenOpbrengsten['gewogen']=0;
      }
      
      if($beginCorrectie && $eindCorrectie)
      {
        $performance=$AttributieStortingenOntrekkingen['totaal'] / $beginTransactieWaarde * -100;
        $resultaat=$beginTransactieWaarde-$AttributieStortingenOntrekkingen['totaal'];
    //     echo "$fondsenWhere perf $performance=".$AttributieStortingenOntrekkingen['totaal']."/ $beginTransactieWaarde * -100; <br>\n";
      }
      else
      {
        $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'] - $directeKostenOpbrengsten['gewogen'] ;
        if($beginwaarde > 0 && $gemiddelde <0)
        {
          //echo "$fondsenWhere $gemiddelde <br>\n";
          $gemiddelde=$gemiddelde*-1;
        }
        $resultaat=(($eindwaarde - $beginwaarde) + $AttributieStortingenOntrekkingen['totaal'] + $directeKostenOpbrengsten['totaal']);
        $performance = ($resultaat / $gemiddelde) * 100;
      }
  
      $debug=false;
      //$debug=true;
      
      if($debug)//
      {
        echo "    <br>\n" ;
        //echo "$datumBegin $datumEind ($beginCorrectie) ($eindCorrectie) $datumBeginWeging<br>\n";
        //echo "$queryAttributieStortingenOntrekkingenRekening <br>\n $queryAttributieStortingenOntrekkingen <br>\n";
        //echo "$fondsenWhere $datumBegin -> $datumEind <br>\n";
        //echo "gemiddelde= 	 $gemiddelde = begin $beginwaarde -  gewogenSo ".$AttributieStortingenOntrekkingen['gewogen']." - gewogenDko ".$directeKostenOpbrengsten['gewogen']."<br>\n " ;
        echo " $datumBegin -> $datumEind | $fondsenWhere <br>\n";
        echo "   $performance = ((($eindwaarde - $beginwaarde) + ".$AttributieStortingenOntrekkingen['totaal']." + ".$directeKostenOpbrengsten['totaal']." ) / $gemiddelde) * 100;	<br>\n";
        //ob_flush();
        //echo ($totaalPerf  * (100+$performance)/100)." = ($totaalPerf  * (100+$performance)/100) <br>\n";
      }
      $totaalPerf = ($totaalPerf  * (100+$performance)/100) ;
      
    }
    //if($debug)// && $fonds=='Citigroup'
    //  echo " perftotaal ".($totaalPerf-100) ."<br>\n ";
    
    return array('perf'=>($totaalPerf-100),
                 'actuelePortefeuilleWaardeEuro'=>$eindwaarde,
                 'directeKostenOpbrengsten'=>$directeKostenOpbrengsten['totaal'],
                 'AttributieStortingenOntrekkingen'=>$AttributieStortingenOntrekkingen['totaal']*-1,
                 'resultaat'=>$resultaat,
                 'gemiddelde'=>$gemiddelde);
  }


  function getAlleRegios()
  {
    $DB=new DB();
    $query="SELECT
KeuzePerVermogensbeheerder.vermogensbeheerder,
KeuzePerVermogensbeheerder.categorie,
KeuzePerVermogensbeheerder.waarde,
KeuzePerVermogensbeheerder.Afdrukvolgorde,
Regios.Omschrijving
FROM
KeuzePerVermogensbeheerder
INNER JOIN Regios ON KeuzePerVermogensbeheerder.waarde = Regios.Regio
WHERE
KeuzePerVermogensbeheerder.vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND KeuzePerVermogensbeheerder.categorie = 'Regios'
ORDER BY KeuzePerVermogensbeheerder.Afdrukvolgorde";
  
    $DB->SQL($query);
    $DB->Query();
    $sectoren=array();
    while($data = $DB->nextRecord())
    {
      $sectoren[$data['waarde']]=$data['Omschrijving'];
    }
    return $sectoren;
  }
  
  function getAlleSectoren()
  {
    if($this->beleggingscategorieFilter=='AAND')
      $waardeFilter="AND waarde like 'HEE-%'";
    elseif($this->beleggingscategorieFilter=='OBL')
      $waardeFilter="AND waarde like 'Obl-%'";
    
    $DB=new DB();
    $query="SELECT
KeuzePerVermogensbeheerder.vermogensbeheerder,
KeuzePerVermogensbeheerder.categorie,
KeuzePerVermogensbeheerder.waarde,
KeuzePerVermogensbeheerder.Afdrukvolgorde,
Beleggingssectoren.Omschrijving
FROM
KeuzePerVermogensbeheerder
INNER JOIN Beleggingssectoren ON KeuzePerVermogensbeheerder.waarde = Beleggingssectoren.Beleggingssector
WHERE
KeuzePerVermogensbeheerder.vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND KeuzePerVermogensbeheerder.categorie like 'Beleggingssector%'
$waardeFilter
ORDER BY KeuzePerVermogensbeheerder.Afdrukvolgorde";
  
    $DB->SQL($query);
    $DB->Query();
    $sectoren=array();
    while($data = $DB->nextRecord())
    {
      $sectoren[$data['waarde']]=$data['Omschrijving'];
    }
    return $sectoren;
  }
  
  function bepaalFondsWaarden()
  {
    global $__appvar;
    $DB=new DB();
    if ($this->beleggingscategorieFilter <> '')
    {
      $filter = "AND TijdelijkeRapportage.Beleggingscategorie='" . $this->beleggingscategorieFilter . "'";
    }
    else
    {
      $filter = '';
    }
  
    // haal totaalwaarde op om % te berekenen
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / " . $this->pdf->ValutaKoersEind . " AS totaal " .
      "FROM TijdelijkeRapportage WHERE " .
      " rapportageDatum ='" . $this->rapportageDatum . "' $filter AND " .
      " portefeuille = '" . $this->portefeuille . "' "
      . $__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query, __FILE__, __LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
  

    $query = "SELECT TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.".$this->verdeling."Omschrijving as VerdelingOmschrijving, ".
      " TijdelijkeRapportage.fonds, ".
      " TijdelijkeRapportage.actueleValuta, ".
      " TijdelijkeRapportage.totaalAantal, ".
      " TijdelijkeRapportage.beginwaardeLopendeJaar , ".
      " TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
      " TijdelijkeRapportage.Valuta, ".
      " TijdelijkeRapportage.beginPortefeuilleWaardeEuro /  ".$this->pdf->ValutaKoersBegin. " as beginPortefeuilleWaardeEuro, ".
      " TijdelijkeRapportage.actueleFonds,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				 TijdelijkeRapportage.beleggingscategorie,
				 TijdelijkeRapportage.portefeuille,
				 TijdelijkeRapportage.".$this->verdeling." as verdeling,
				  Fondsen.ISINCode".
      " FROM TijdelijkeRapportage
      JOIN Fondsen ON TijdelijkeRapportage.Fonds=Fondsen.Fonds
      WHERE ".
      " TijdelijkeRapportage.portefeuille = '".$this->portefeuille."'".
      "  $filter AND ".
      " TijdelijkeRapportage.type =  'fondsen' AND ".
      " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " ORDER BY TijdelijkeRapportage.".$this->verdeling."Volgorde, TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
    
    $DB->SQL($query);
    $DB->Query();
    $fondsVerdeling=array();
    $verdelingTotalen=array();
    $filterTotaal=array();
    $somVelden=array('actuelePortefeuilleWaardeEuro','weging','directeKostenOpbrengsten','resultaat','AttributieStortingenOntrekkingen','gemiddelde');
    $fondsenMetTransactie=$this->gerealiseerd;
    while($data = $DB->nextRecord())
    {
      if(isset($fondsenMetTransactie[$data['verdeling']][$data['fonds']]))
        unset($fondsenMetTransactie[$data['verdeling']][$data['fonds']]);
      $perf=$this->fondsPerformance($data['fonds'],$this->rapportageDatumVanaf,$this->rapportageDatum);
      $data['gemiddelde']=$perf['gemiddelde'];
      $data['resultaat']=$perf['resultaat'];
      $data['perf']=$perf['perf'];
      $data['AttributieStortingenOntrekkingen']=$perf['AttributieStortingenOntrekkingen'];
      $data['directeKostenOpbrengsten']=$perf['directeKostenOpbrengsten'];
      $data['weging']=$data['actuelePortefeuilleWaardeEuro']/$totaalWaarde['totaal']*100;
     
      $fondsVerdeling[$data['verdeling']][$data['fonds']]=$data;
  
      foreach($somVelden as $veld)
      {
        $verdelingTotalen[$data['verdeling']][$veld] += $data[$veld];
        $filterTotaal[$veld] += $data[$veld];
      }
      $verdelingTotalen[$data['verdeling']]['VerdelingOmschrijving']=$data['VerdelingOmschrijving'];
      $verdelingTotalen[$data['verdeling']]['fondsen'][$data['fonds']]=$data['fonds'];
      $filterTotaal['fondsen'][$data['fonds']]=$data['fonds'];
      //$filterTotaal['perfProcent']+=$data['perf']*$data['weging']/100;
    }
    foreach($fondsenMetTransactie as $verdeling=>$fondsen)
    {
      foreach($fondsen as $fonds=>$data)
      {
        $perf=$this->fondsPerformance($fonds,$this->rapportageDatumVanaf,$this->rapportageDatum);
        $data['gemiddelde']=$perf['gemiddelde'];
        $data['resultaat']=$perf['resultaat'];
        $data['perf']=$perf['perf'];
        $data['AttributieStortingenOntrekkingen']=$perf['AttributieStortingenOntrekkingen'];
        $data['directeKostenOpbrengsten']=$perf['directeKostenOpbrengsten'];
       // listarray($data);
        
        $fondsVerdeling[$verdeling][$fonds]=$data;
        $verdelingTotalen[$verdeling]['fondsen'][$data['fonds']]=$data['fonds'];
        $filterTotaal['fondsen'][$data['fonds']]=$data['fonds'];
      }
    }
    
    foreach($verdelingTotalen as $verdeling=>$verdelingDetails)
    {
      $perf=$this->fondsPerformance($verdelingDetails['fondsen'],$this->rapportageDatumVanaf,$this->rapportageDatum);
      $verdelingTotalen[$verdeling]['perf']=$perf;
    }
    

    $this->verdelingTotalen=$verdelingTotalen;
 // listarray($filterTotaal);
    
    $perf=$this->fondsPerformance($filterTotaal['fondsen'],$this->rapportageDatumVanaf,$this->rapportageDatum);
    $filterTotaal['perf']=$perf;
    $this->filterTotaal=$filterTotaal;
    return $fondsVerdeling;

  }
  
  function getBenchmark($verdelingscategorie)
  {
    $DB = new DB();
    $query="SELECT IndexPerBeleggingscategorie.Fonds, Fondsen.Omschrijving FROM IndexPerBeleggingscategorie
JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds=Fondsen.Fonds
WHERE Vermogensbeheerder='" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "' AND Categorie='".mysql_real_escape_string($verdelingscategorie)."'";
    $DB->SQL($query);
    $DB->Query();
    $fonds = $DB->nextRecord();
    $fonds['perf']=getFondsPerformance($fonds['Fonds'],$this->rapportageDatumVanaf,$this->rapportageDatum);
  
    $catTotaal=$this->verdelingTotalen[$verdelingscategorie];
    $fonds['portefeuillePerf'] = ($catTotaal['resultaatEUR'] / ($catTotaal['beginPortefeuilleWaardeEuro'] /100));
   
    return $fonds;
  }
  
  function writeRapport()
  {
    global $__appvar;
    $regels=array();
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas'] = $this->pdf->page;
    
    $this->gerealiseerdResultaat($this->verdeling);
    $fondsWaarden=$this->bepaalFondsWaarden();
    if($this->verdeling=='Beleggingssector')
    {
      $sectoren = $this->getAlleSectoren();
      //  listarray($this->verdelingTotalen);
    }
    elseif($this->verdeling='Regio')
    {
      $sectoren = $this->getAlleRegios();
    }
  
    if (1)
    {
      foreach ($fondsWaarden as $verdeling => $fondsen)
      {
        if (isset($this->verdelingTotalen[$verdeling]['VerdelingOmschrijving']))
        {
          $sectoren[$verdeling] = $this->verdelingTotalen[$verdeling]['VerdelingOmschrijving'];
        }
      }
    }
   // listarray($sectoren);
   // exit;
  
    $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
    $wegingTotaal=0;
    foreach($sectoren as $verdeling =>$verdelingOmschrijving)
    {
      if(isset($fondsWaarden[$verdeling]))
        $fondsen=$fondsWaarden[$verdeling];
      else
        $fondsen=array();
      if($this->benchmarkTotalen==false)
      {
         $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
         $this->pdf->row(array($verdelingOmschrijving));
          $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
      }
      $wegingSubTotaal=0;
      foreach($fondsen as $fonds=>$fondsDetails)
      {
        //listarray($fondsDetails);
        $wegingSubTotaal+=$fondsDetails['weging'];
        if($this->benchmarkTotalen==false)
        {
          $this->pdf->row(array($fondsDetails['fondsOmschrijving'],
                                  $fondsDetails['ISINCode'],
                                  $fondsDetails['Valuta'],
                                  $this->formatAantal($fondsDetails['totaalAantal'], 0),
                                  $this->formatGetal($fondsDetails['actueleFonds'], 2),
                                  $this->formatGetal($fondsDetails['actuelePortefeuilleWaardeInValuta'], 0),
                                  $this->formatGetal($fondsDetails['actuelePortefeuilleWaardeEuro'], 0),
                                  $this->formatGetal($fondsDetails['weging'], 2) . '%',
                                  $this->formatGetal(($fondsDetails['resultaat'] - $fondsDetails['directeKostenOpbrengsten']) / $fondsDetails['gemiddelde'] * 100, 2) . '%',
                                  $this->formatGetal($fondsDetails['directeKostenOpbrengsten'] / $fondsDetails['gemiddelde'] * 100, 2) . '%',
                                  $this->formatGetal($fondsDetails['AttributieStortingenOntrekkingen'], 0),
                                  $this->formatGetal($fondsDetails['perf'], 2) . '%',
                                ));//$this->formatGetal($fondsDetails['resultaatBenchmark'],2).'%'
        }
      }
//listarray($this->verdelingTotalen[$verdeling]['perf']);
      $wegingTotaal+=$wegingSubTotaal;
      $benchmark=$this->getBenchmark($verdeling);
      $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
      $regels[]=array($verdelingOmschrijving,($benchmark['Omschrijving']<>''?$benchmark['Omschrijving']:'Geen'),
      //  $this->formatGetal($this->verdelingTotalen[$verdeling]['perf']['actuelePortefeuilleWaardeEuro'],0),
        $this->formatGetal($wegingSubTotaal,2).'%',
        $this->formatGetal($this->verdelingTotalen[$verdeling]['perf']['perf'],2).'%',
        $this->formatGetal($benchmark['perf'],2).'%');
              if($this->benchmarkTotalen==false)
              {
                $this->pdf->row(array($benchmark['Omschrijving'], '', '', '', '', '',
                                  $this->formatGetal($this->verdelingTotalen[$verdeling]['perf']['actuelePortefeuilleWaardeEuro'], 0),
                                  $this->formatGetal($wegingSubTotaal, 2) . '%',
                                  '',//$this->formatGetal(($this->verdelingTotalen[$verdeling]['perf']['resultaat']-
                                  //                     $this->verdelingTotalen[$verdeling]['perf']['directeKostenOpbrengsten'])/$this->verdelingTotalen[$verdeling]['perf']['gemiddelde']*100,2).'%',
                                  '',//$this->formatGetal($this->verdelingTotalen[$verdeling]['perf']['directeKostenOpbrengsten']/$this->verdelingTotalen[$verdeling]['perf']['gemiddelde']*100,2).'%',
                                  '',//$this->formatGetal($this->verdelingTotalen[$verdeling]['perf']['AttributieStortingenOntrekkingen'],0),
                                  $this->formatGetal($this->verdelingTotalen[$verdeling]['perf']['perf'], 2) . '%',
                                  $this->formatGetal($benchmark['perf'], 2) . '%'));
                $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
                $this->pdf->ln(1);
              }
    
    }
  
    $DB = new DB();
    $query="SELECT IndexPerBeleggingscategorie.Fonds, Fondsen.Omschrijving FROM IndexPerBeleggingscategorie
JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds=Fondsen.Fonds
WHERE Vermogensbeheerder='" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "' AND Beleggingscategorie='".$this->beleggingscategorieFilter."'";
    $DB->SQL($query);
    $DB->Query();
    $fonds = $DB->nextRecord();
    $benchmark['perf']=getFondsPerformance($fonds['Fonds'],$this->rapportageDatumVanaf,$this->rapportageDatum);
  
    include_once("rapport/include/layout_87/ATTberekening_L87.php");
    $att=new ATTberekening_L87($this);
    $att->indexPerformance=false;
    $this->waarden['Periode']=$att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'Categorien');
    $this->filterTotaal['perf']['perf']=$this->waarden['Periode'][$this->beleggingscategorieFilter]['procent'];

    //echo $wegingTotaal;exit;
 // listarray($this->filterTotaal);exit;
    
    if($this->benchmarkTotalen)
    {
      $totalen = array('Totaal',$fonds['Omschrijving'],
       // $this->formatGetal($this->filterTotaal['perf']['actuelePortefeuilleWaardeEuro'], 0),
        $this->formatGetal($wegingTotaal, 2) . '%',
        $this->formatGetal($this->filterTotaal['perf']['perf'], 2) . '%',
        $this->formatGetal($benchmark['perf'], 2) . '%');
    }
    else
    {
      $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
      $this->pdf->row(array('Totaal', '', '', '', '', '',
                        $this->formatGetal($this->filterTotaal['perf']['actuelePortefeuilleWaardeEuro'], 0),
                        $this->formatGetal($wegingTotaal, 2) . '%',
                        '',//$this->formatGetal(($this->filterTotaal['perf']['resultaat']-$this->filterTotaal['perf']['directeKostenOpbrengsten'])/$this->filterTotaal['perf']['gemiddelde']*100,2).'%',
                        '',//$this->formatGetal($this->filterTotaal['perf']['directeKostenOpbrengsten']/$this->filterTotaal['perf']['gemiddelde']*100,2).'%',
                        '',//$this->formatGetal($this->filterTotaal['perf']['AttributieStortingenOntrekkingen'],0),
                        $this->formatGetal($this->filterTotaal['perf']['perf'], 2) . '%',
                        $this->formatGetal($benchmark['perf'], 2) . '%'));
    }
    if($this->benchmarkTotalen)
      return array($regels,$totalen);
    
    $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0, 0, 0);
  }
  
  function addBenchmarkTabel($data)
  {
    $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
    $this->pdf->setWidths(array(60,60,25,25,25));
    $this->pdf->setAligns(array('L','L','R','R','R'));
  
    if($this->pdf->rapport_type=='KERNZ')
      $verdeling='Sector';
    elseif($this->pdf->rapport_type=='OIH')
      $verdeling='Regio';
    else
      $verdeling='Segment';
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 297-$this->pdf->marge*2, 8, 'F');
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->row(array($verdeling, 'Benchmark', 'Gewicht in portefeuille', 'Rendement portefeuille', 'Rendement benchmark'));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0, 0, 0);
    foreach($data[0] as $regel)
      $this->pdf->row($regel);
    $this->pdf->ln(2);
    $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
    $this->pdf->row($data[1]);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  
  }
}

?>

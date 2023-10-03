<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/25 15:37:41 $
File Versie					: $Revision: 1.2 $

$Log: RapportOIB_L91.php,v $
Revision 1.2  2020/07/25 15:37:41  rvv
*** empty log message ***

Revision 1.1  2020/07/01 16:22:28  rvv
*** empty log message ***




*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"] . "/html/rapport/rapportVertaal.php");

class RapportHUIS_L103
{
  function RapportHUIS_L103($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "HUIS";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_titel = "Mandaat controle";
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
    $this->portefeuilleData = array();
  }
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde, $dec, ",", ".");
  }
  
  
  function fondsWaardeOpdatum($portefeuille, $fonds, $rapportageDatum, $valuta = 'EUR')
  {
    $a = 1;
    $fondsen[$a]['Fonds'] = $fonds;
    $jaar = date("Y", db2jul($rapportageDatum));
    if ($jaar >= 2019)
    {
      $meerderebeginBoekingen = true;
    }
    else
    {
      $meerderebeginBoekingen = false;
    }
    $beginDatum=$this->rapportageDatumVanaf;
   
    $beginJaar = date("Y", db2jul($beginDatum));
    
    $DB = new DB();
    $q = "SELECT Datum, Rentepercentage FROM Rentepercentages WHERE Fonds = '" . $fondsen[$a]['Fonds'] . "' AND Datum <= '" . $rapportageDatum . "' LIMIT 1";
    $DB->SQL($q);
    $DB->Query();
    $rente = array();
    if ($DB->records() > 0)
    {
      $rente = getRenteParameters($fondsen[$a]['Fonds'], $rapportageDatum);
      
      if ($rente['Rente30_360'] == 1)
      {
        $renteBerekenen = 2;
      }
      else
      {
        $renteBerekenen = 1;
      }
    }
    else
    {
      $renteBerekenen = 0;
    }
    $extraquery = " Portefeuilles.Portefeuille = '" . $portefeuille . "' AND 	";
    
    $idFilter = '';
    if ($id <> '')
    {
      $qMutaties = "SELECT Rekeningmutaties.* FROM Rekeningmutaties WHERE id ='$id'";
      $DB->SQL($qMutaties);
      $DB->Query();
      $verkoopRecord = $DB->nextRecord();
      $verkoopRecord['BoekdatumJul'] = db2jul($verkoopRecord['Boekdatum']);
      // $idFilter = " AND Rekeningmutaties.id <= '$id'";
    }
    else
    {
      $verkoopRecord = array();
    }
    if ($beginJaar != '1970' && $jaar != $beginJaar && $beginJaar < $jaar)
    {
      for ($jaren = $beginJaar; $jaren <= $jaar; $jaren++)
      {
        if (isset($jarenString))
        {
          $jarenString .= ",'$jaren'";
          if (isset($januariUitluiten))
          {
            $januariUitluiten .= ",'$jaren-01-01 00:00:00'";
          }
          else
          {
            $januariUitluiten = "'$jaren-01-01 00:00:00'";
          }
        }
        else
        {
          $jarenString = "'$jaren'";
        }
      }
      
      $boekjarenFilter = " ( YEAR(Rekeningmutaties.Boekdatum) IN ($jarenString) ) ";
      $januariFilter = " Rekeningmutaties.Boekdatum NOT IN ($januariUitluiten) ";
      
      $qMutaties = "SELECT Rekeningmutaties.*, " .
        " Fondsen.Renteperiode, " .
        " Fondsen.EersteRentedatum, " .
        " Fondsen.Rentedatum, " .
        " Fondsen.Fondseenheid, " .
        " Fondsen.Valuta, " .
        " Fondsen.Omschrijving AS FondsOmschrijving ,forward,forwardReferentieKoers,Huisfonds,Fondsen.Portefeuille as huisfondsPortefeuille" .
        " FROM Rekeningmutaties, " .
        " Rekeningen, Fondsen, Portefeuilles WHERE " . $extraquery .
        " Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND " .
        " Rekeningmutaties.Grootboekrekening = 'FONDS' AND " .
        " Rekeningmutaties.Rekening = Rekeningen.Rekening AND " .
        " Fondsen.Fonds = Rekeningmutaties.Fonds AND " .
        " Rekeningmutaties.Fonds = '" . $fondsen[$a]['Fonds'] . "' AND " .
        $boekjarenFilter . " AND " . $januariFilter . " AND " .
        " Rekeningmutaties.Verwerkt = '1' AND " .
        " Rekeningmutaties.Boekdatum <= '" . $rapportageDatum . "' $idFilter " .
        " ORDER BY Rekeningmutaties.Boekdatum ASC, Rekeningmutaties.id ";
    }
    else
    {
      $qMutaties = "SELECT Rekeningmutaties.*, " .
        " Fondsen.Renteperiode, " .
        " Fondsen.EersteRentedatum, " .
        " Fondsen.Rentedatum, " .
        " Fondsen.Fondseenheid, " .
        " Fondsen.Valuta, " .
        " Fondsen.Omschrijving AS FondsOmschrijving ,forward,forwardReferentieKoers,Huisfonds,Fondsen.Portefeuille as huisfondsPortefeuille " .
        " FROM Rekeningmutaties, " .
        " Rekeningen, Fondsen, Portefeuilles WHERE " . $extraquery .
        " Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND " .
        " Rekeningmutaties.Rekening = Rekeningen.Rekening AND " .
        " Fondsen.Fonds = Rekeningmutaties.Fonds AND " .
        " Rekeningmutaties.Fonds = '" . $fondsen[$a]['Fonds'] . "' AND " .
        " Rekeningmutaties.Grootboekrekening = 'FONDS' AND " .
        " YEAR(Rekeningmutaties.Boekdatum) = '" . $jaar . "' AND " .
        " Rekeningmutaties.Verwerkt = '1' AND " .
        " Rekeningmutaties.Boekdatum <= '" . $rapportageDatum . "' $idFilter " .
        " ORDER BY Rekeningmutaties.Boekdatum ASC, Rekeningmutaties.id ";
    }
    
    
    $DB->SQL($qMutaties);
    $DB->Query();
    
    $fondswaarden[$fondsen[$a]['Fonds']]['type'] = "fondsen";
    $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = 0;
    $fondswaarden[$fondsen[$a]['Fonds']]['historischeWaarde'] = 0;
    $fondswaarden[$fondsen[$a]['Fonds']]['historischeValutakoers'] = 1;
    $fondswaarden[$fondsen[$a]['Fonds']]['historischeRapportageValutakoers'] = 1;
    $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = 0;
    $fondswaarden[$fondsen[$a]['Fonds']]['fondsEenheid'] = 1;
    $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = 1;
    $fondswaarden[$fondsen[$a]['Fonds']]['renteBerekenen'] = $renteBerekenen;
    
    $fondswaarden[$fondsen[$a]['Fonds']]['voorgaandejarenActief'] = 1;
    
    $vorigeBeginwaardeValutaLopendeJaar = 0;
    $vorigeBeginwaardeLopendeJaar = 0;
    $vorigeHistorischeWaard = 0;
    $vorigeHistorischeValutakoers = 0;
    $vorigeTotaalAantal = 0;
    
    $julRapportage = db2jul($rapportageDatum);
    
    $julBeginDatum = db2jul($beginDatum);
    
    $counter = 0;
    $vorigeHistorischeWaarde = 0;
    $vorigeHistorischeRapportageValutakoers = 0;
    while ($mutatie = $DB->NextRecord())
    {
      
      if ($mutatie['BewaarderSort'] != '')
      {
        $mutatie['Bewaarder'] = $mutatie['BewaarderSort'];
      }
      
      $julBoekdatum = db2jul($mutatie['Boekdatum']);
      if ($julBoekdatum >= $verkoopRecord['BoekdatumJul'] && $id > 0 && $mutatie['id'] > $id)
      {
        //echo substr($verkoopRecord['Boekdatum'],0,10).">=" .$mutatie['Boekdatum']." <br>\n";
        break;
      }
      
      if ($mutatie['forward'] == 1)
      {
        //strtoupper($mutatie[Transactietype])
        $koersStartOpgehaald = true;
        $fondswaarden[$fondsen[$a]['Fonds']]['valuta'] = $mutatie['Valuta'];
        //$fondswaarden[$fondsen[$a]['Fonds']]['forward'] = 1;
        $fondswaarden[$fondsen[$a]['Fonds']]['fondsOmschrijving'] = $mutatie['FondsOmschrijving'];
        $fondswaarden[$fondsen[$a]['Fonds']]['actueleValuta'] = getValutaKoers($mutatie['Valuta'], $rapportageDatum);
        $fondswaarden[$fondsen[$a]['Fonds']]['actueleFonds'] = $fondswaarden[$fondsen[$a]['Fonds']]['actueleValuta'] - $mutatie['forwardReferentieKoers'];
        $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] += $mutatie['Aantal'];
        $fondswaarden[$fondsen[$a]['Fonds']]['fondsEenheid'] = $mutatie['Fondseenheid'];// 1/getValutaKoers($mutatie['Valuta'],$rapportageDatum);
        if ($julBeginDatum >= $julBoekdatum)
        {
          
          $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = getValutaKoers($mutatie['Valuta'], $beginDatum);
          $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] - $mutatie['forwardReferentieKoers'];
        }
      }
      else
      {
        
        if (($julBoekdatum > $julBeginDatum) && !isset($koersStartOpgehaald))//&& ($julBoekdatum > mktime(0,0,0,1,1,$jaar))
        {
          $koersStartOpgehaald = true;
          
          // haal startdatum valuta koers op!
          $startvaluta = array();
          $q = "SELECT Koers,Datum FROM Valutakoersen WHERE Valuta = '" . $mutatie['Valuta'] . "' AND Datum <= '" . $beginDatum . "' ORDER BY Datum DESC LIMIT 1";
          $DB2 = new DB();
          $DB2->SQL($q);
          $DB2->Query();
          $startvaluta = $DB2->NextRecord();
          $vorigeBeginwaardeValutaLopendeJaar = $startvaluta['Koers'];
          $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = $vorigeBeginwaardeValutaLopendeJaar;
          
          // haal startdatum fonds koers op!
          $startfonds = array();
          $q = "SELECT Koers,Datum FROM Fondskoersen WHERE Fonds = '" . $mutatie['Fonds'] . "' AND Datum <= '" . $beginDatum . "' ORDER BY Datum DESC LIMIT 1";
          $DB2 = new DB();
          $DB2->SQL($q);
          $DB2->Query();
          $startfonds = $DB2->NextRecord();
          if ($startfonds['Koers'] == '' && $mutatie['Huisfonds'] == 1 && $mutatie['huisfondsPortefeuille'] <> '')
          {
            $startfonds = bepaalHuisfondsKoers($mutatie['Fonds'], $mutatie['huisfondsPortefeuille'], $beginDatum);
          }
          $vorigeBeginwaardeLopendeJaar = $startfonds['Koers'];
          
          $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = $vorigeBeginwaardeLopendeJaar;
        }
        else
        {
          // echo $mutatie['Aantal']." ".$mutatie['Fonds']." ".$fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar']." $rapportageDatum <br> ";
        }
        
        
        // haal actuele valuta koers op!
        if (empty($fondswaarden[$fondsen[$a]['Fonds']]['actueleValuta']))
        {
          $actuelevaluta = array();
          $q = "SELECT Koers,Datum FROM Valutakoersen WHERE Valuta = '" . $mutatie['Valuta'] . "' AND Datum <= '" . $rapportageDatum . "' ORDER BY Datum DESC LIMIT 1";
          $DB2 = new DB();
          $DB2->SQL($q);
          $DB2->Query();
          $actuelevaluta = $DB2->NextRecord();
        }
        
        // haal actuele fonds koers op!
        if (empty($fondswaarden[$fondsen[$a]['Fonds']]['actueleFonds']))
        {
          $actuelefonds = array();
          $q = "SELECT Koers,Datum FROM Fondskoersen WHERE Fonds = '" . $mutatie['Fonds'] . "' AND Datum <= '" . $rapportageDatum . "' ORDER BY Datum DESC LIMIT 1";
          $DB2 = new DB();
          $DB2->SQL($q);
          $DB2->Query();
          $actuelefonds = $DB2->NextRecord();
          
          if ($actuelefonds['Koers'] == '' && $mutatie['Huisfonds'] == 1 && $mutatie['huisfondsPortefeuille'] <> '')
          {
            $actuelefonds = bepaalHuisfondsKoers($mutatie['Fonds'], $mutatie['huisfondsPortefeuille'], $rapportageDatum);
          }
        }
        
        //$mutatie['Aantal'] 	= $mutatie['Aantal'];
        $fondswaarden[$fondsen[$a]['Fonds']]['fondsEenheid'] = $mutatie['Fondseenheid'];
        $fondswaarden[$fondsen[$a]['Fonds']]['fondsOmschrijving'] = $mutatie['FondsOmschrijving'];
        $fondswaarden[$fondsen[$a]['Fonds']]['valuta'] = $mutatie['Valuta'];
        $fondswaarden[$fondsen[$a]['Fonds']]['eersteRentedatum'] = $mutatie['EersteRentedatum'];
        
        $fondswaarden[$fondsen[$a]['Fonds']]['rentedatum'] = $mutatie['Rentedatum'];
        $fondswaarden[$fondsen[$a]['Fonds']]['renteperiode'] = $mutatie['Renteperiode'];
        
        $fondswaarden[$fondsen[$a]['Fonds']]['actueleValuta'] = $actuelevaluta['Koers'];
        $fondswaarden[$fondsen[$a]['Fonds']]['actueleFonds'] = $actuelefonds['Koers'];
        $fondswaarden[$fondsen[$a]['Fonds']]['koersDatum'] = $actuelefonds['Datum'];
        
        if ($mutatie['Bewaarder'] != '' && empty($fondswaarden[$fondsen[$a]['Fonds']]['Bewaarder']))
        {
          $fondswaarden[$fondsen[$a]['Fonds']]['Bewaarder'] = $mutatie['Bewaarder'];
        }
//echo $mutatie['Fonds']."  ".$mutatie['Boekdatum'] . " ".getValutaKoers($valuta,$mutatie['Boekdatum'])."<br>";
        
        switch (strtoupper($mutatie['Transactietype']))
        {
          case "A" :
            // Aankoop
            if ($vorigeTotaalAantal < 0)
            {
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = $mutatie['Valutakoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = $mutatie['Fondskoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeWaarde'] = $mutatie['Fondskoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeValutakoers'] = $mutatie['Valutakoers'];
              if ($valuta != 'EUR')
              {
                $fondswaarden[$fondsen[$a]['Fonds']]['historischeRapportageValutakoers'] = getValutaKoers($valuta, $mutatie['Boekdatum']);
              }
              $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = $vorigeTotaalAantal + $mutatie['Aantal'];
              $fondswaarden[$fondsen[$a]['Fonds']]['voorgaandejarenActief'] = 0;
            }
            else
            {
              //echo $mutatie['Fonds']." ".$mutatie['Aantal']." ".$vorigeTotaalAantal;
              $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = ($vorigeTotaalAantal + $mutatie['Aantal']);
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeWaarde'] = ((($vorigeTotaalAantal * $vorigeHistorischeWaarde) +($mutatie['Aantal'] * $mutatie['Fondskoers']))/($vorigeTotaalAantal + $mutatie['Aantal']));
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeValutakoers'] = ((($vorigeTotaalAantal * $vorigeHistorischeWaarde * $vorigeHistorischeValutakoers)+($mutatie['Aantal'] * $mutatie['Fondskoers'] * $mutatie['Valutakoers']))/(($vorigeTotaalAantal * $vorigeHistorischeWaarde) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])));
              if ($valuta != 'EUR')
              {
                $fondswaarden[$fondsen[$a]['Fonds']]['historischeRapportageValutakoers'] = ((($vorigeTotaalAantal * $vorigeHistorischeWaarde * $vorigeHistorischeRapportageValutakoers)+($mutatie['Aantal'] * $mutatie['Fondskoers'] * getValutaKoers($valuta, $mutatie['Boekdatum'])))/(($vorigeTotaalAantal * $vorigeHistorischeWaarde) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])));
              }
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = ((($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar) + ($mutatie['Aantal'] * $mutatie['Fondskoers']))/($vorigeTotaalAantal + $mutatie['Aantal']));
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = ((($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar * $vorigeBeginwaardeValutaLopendeJaar)+($mutatie['Aantal'] * $mutatie['Fondskoers'] * $mutatie['Valutakoers']))/(($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])));
            }
            break;
          case "A/O" :
            // Aankoop / openen
            if ($vorigeTotaalAantal == 0)
            {
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = $mutatie['Valutakoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = $mutatie['Fondskoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeWaarde'] = $mutatie['Fondskoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeValutakoers'] = $mutatie['Valutakoers'];
              if ($valuta != 'EUR')
              {
                $fondswaarden[$fondsen[$a]['Fonds']]['historischeRapportageValutakoers'] = getValutaKoers($valuta, $mutatie['Boekdatum']);
              }
              $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = $mutatie['Aantal'];
              $fondswaarden[$fondsen[$a]['Fonds']]['voorgaandejarenActief'] = 1;
            }
            else
            {
              $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = ($vorigeTotaalAantal + $mutatie['Aantal']);
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeWaarde'] = ((($vorigeTotaalAantal * $vorigeHistorischeWaarde) + ($mutatie['Aantal'] * $mutatie['Fondskoers']))/($vorigeTotaalAantal + $mutatie['Aantal']));
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeValutakoers'] = ((($vorigeTotaalAantal * $vorigeHistorischeWaarde * $vorigeHistorischeValutakoers)+($mutatie['Aantal'] * $mutatie['Fondskoers'] * $mutatie['Valutakoers']))/(($vorigeTotaalAantal * $vorigeHistorischeWaarde) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])));
              if ($valuta != 'EUR')
              {
                $fondswaarden[$fondsen[$a]['Fonds']]['historischeRapportageValutakoers'] = ((($vorigeTotaalAantal * $vorigeHistorischeWaarde * $vorigeHistorischeRapportageValutakoers)+($mutatie['Aantal'] * $mutatie['Fondskoers'] * getValutaKoers($valuta, $mutatie['Boekdatum'])))/(($vorigeTotaalAantal * $vorigeHistorischeWaarde) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])));
              }
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = ((($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar) + ($mutatie['Aantal'] * $mutatie['Fondskoers']))/($vorigeTotaalAantal + $mutatie['Aantal']));
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = ((($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar * $vorigeBeginwaardeValutaLopendeJaar)+($mutatie['Aantal'] * $mutatie['Fondskoers'] * $mutatie['Valutakoers']))/(($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])));
            }
            break;
          case "A/S" :
            // Aankoop / sluiten
            $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = ($vorigeTotaalAantal + $mutatie['Aantal']);
            break;
          case "B" :
            // Beginstorting
            //$meerderebeginBoekingen=false;
            if ($counter > 0 && $meerderebeginBoekingen == true)
            {
              
              $q = "SELECT Koers,Datum FROM Fondskoersen WHERE Fonds = '" . $fondsen[$a]['Fonds'] . "' AND Datum <= '" . $mutatie['Boekdatum'] . "' ORDER BY Datum DESC LIMIT 1";
              $DB2 = new DB();
              $DB2->SQL($q); //echo "<br>$q <br>\n";
              $DB2->Query();
              $beginkoers = $DB2->NextRecord();
              
              $q = "SELECT Koers,Datum FROM Valutakoersen WHERE Valuta = '" . $fondswaarden[$fondsen[$a]['Fonds']]['valuta'] . "' AND Datum <= '" . $mutatie['Boekdatum'] . "' ORDER BY Datum DESC LIMIT 1";
              $DB2 = new DB();
              $DB2->SQL($q);
              $DB2->Query();
              $beginvaluta = $DB2->NextRecord();
              
              $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = ($vorigeTotaalAantal + $mutatie['Aantal']);
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeWaarde'] = ((($vorigeTotaalAantal * $vorigeHistorischeWaarde) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])) / ($vorigeTotaalAantal + $mutatie['Aantal']));
              // echo "!".$fondswaarden[$fondsen[$a]['Fonds']]['historischeWaarde']." = ((($vorigeTotaalAantal * $vorigeHistorischeWaarde) + (".$mutatie['Aantal']." * ".$mutatie['Fondskoers'].")) / ($vorigeTotaalAantal + ".$mutatie['Aantal']."));<br>\n";
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeValutakoers'] = ((($vorigeTotaalAantal * $vorigeHistorischeWaarde * $vorigeHistorischeValutakoers) + ($mutatie['Aantal'] * $mutatie['Fondskoers'] * $mutatie['Valutakoers'])) / (($vorigeTotaalAantal * $vorigeHistorischeWaarde) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])));
              if ($valuta != 'EUR')
              {
                $fondswaarden[$fondsen[$a]['Fonds']]['historischeRapportageValutakoers'] = ((($vorigeTotaalAantal * $vorigeHistorischeWaarde * $vorigeHistorischeRapportageValutakoers) + ($mutatie['Aantal'] * $mutatie['Fondskoers'] * getValutaKoers($valuta, $mutatie['Boekdatum']))) / (($vorigeTotaalAantal * $vorigeHistorischeWaarde) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])));
              }
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = ((($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar) + ($mutatie['Aantal'] * $beginkoers['Koers'])) / ($vorigeTotaalAantal + $mutatie['Aantal']));
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = ((($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar * $vorigeBeginwaardeValutaLopendeJaar) + ($mutatie['Aantal'] * $beginkoers['Koers'] * $beginvaluta['Koers'])) / (($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar) + ($mutatie['Aantal'] * $beginkoers['Koers'])));
            }
            else
            {
              $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = $vorigeTotaalAantal + $mutatie['Aantal'];
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeWaarde'] = $mutatie['Fondskoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeValutakoers'] = $mutatie['Valutakoers'];
              //echo "$counter ".$mutatie['Fonds']." ".$mutatie['Fondskoers']." |".$mutatie['Fondskoers']."| ".$fondswaarden[$fondsen[$a]['Fonds']]['historischeWaarde']."<br>\n";
              if ($valuta != 'EUR')
              {
                $fondswaarden[$fondsen[$a]['Fonds']]['historischeRapportageValutakoers'] = getValutaKoers($valuta, $mutatie['Boekdatum']);
              }
              
              $q = "SELECT Koers,Datum FROM Fondskoersen WHERE Fonds = '" . $fondsen[$a]['Fonds'] . "' AND Datum <= '" . $mutatie['Boekdatum'] . "' ORDER BY Datum DESC LIMIT 1";
              $DB2 = new DB();
              $DB2->SQL($q); //echo "<br>$q <br>\n";
              $DB2->Query();
              $beginkoers = $DB2->NextRecord();
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = $beginkoers['Koers'];
              
              $q = "SELECT Koers,Datum FROM Valutakoersen WHERE Valuta = '" . $fondswaarden[$fondsen[$a]['Fonds']]['valuta'] . "' AND Datum <= '" . $mutatie['Boekdatum'] . "' ORDER BY Datum DESC LIMIT 1";
              $DB2 = new DB();
              $DB2->SQL($q);
              $DB2->Query();
              $beginvaluta = $DB2->NextRecord();
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = $beginvaluta['Koers'];
              
            }
            
            if ($fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] == '' && $mutatie['Huisfonds'] == 1 && $mutatie['huisfondsPortefeuille'] <> '')
            {
              $beginKoersHuisfonds = bepaalHuisfondsKoers($mutatie['Fonds'], $mutatie['huisfondsPortefeuille'], $mutatie['Boekdatum']);
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = $beginKoersHuisfonds['Koers'];
            }
            
            break;
          case "D" :
          case "S" :
            // Deponering
            if ($vorigeTotaalAantal == 0)
            {
              // haal valutakoers op voor beginwaarde!
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = $mutatie['Valutakoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = $mutatie['Fondskoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeWaarde'] = $mutatie['Fondskoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeValutakoers'] = $mutatie['Valutakoers'];
              if ($valuta != 'EUR')
              {
                $fondswaarden[$fondsen[$a]['Fonds']]['historischeRapportageValutakoers'] = getValutaKoers($valuta, $mutatie['Boekdatum']);
              }
              $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = $mutatie['Aantal'];
              $fondswaarden[$fondsen[$a]['Fonds']]['voorgaandejarenActief'] = 1;
            }
            else
            {
              $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = ($vorigeTotaalAantal + $mutatie['Aantal']);
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeWaarde'] = ((($vorigeTotaalAantal * $vorigeHistorischeWaarde) + ($mutatie['Aantal'] * $mutatie['Fondskoers']))/($vorigeTotaalAantal + $mutatie['Aantal']));
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeValutakoers'] = ((($vorigeTotaalAantal * $vorigeHistorischeWaarde * $vorigeHistorischeValutakoers)+($mutatie['Aantal'] * $mutatie['Fondskoers'] * $mutatie['Valutakoers']))/(($vorigeTotaalAantal * $vorigeHistorischeWaarde) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])));
              if ($valuta != 'EUR')
              {
                $fondswaarden[$fondsen[$a]['Fonds']]['historischeRapportageValutakoers'] = ((($vorigeTotaalAantal*$vorigeHistorischeWaarde*$vorigeHistorischeRapportageValutakoers)+($mutatie['Aantal'] * $mutatie['Fondskoers'] * getValutaKoers($valuta, $mutatie['Boekdatum'])))/(($vorigeTotaalAantal * $vorigeHistorischeWaarde) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])));
              }
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = ((($vorigeTotaalAantal*$vorigeBeginwaardeLopendeJaar) + ($mutatie['Aantal'] * $mutatie['Fondskoers']))/($vorigeTotaalAantal + $mutatie['Aantal']));
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = ((($vorigeTotaalAantal*$vorigeBeginwaardeLopendeJaar * $vorigeBeginwaardeValutaLopendeJaar)+($mutatie['Aantal'] * $mutatie['Fondskoers'] * $mutatie['Valutakoers']))/(($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])));
            }
            break;
          case "L" :
            // Lichting
            $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = $vorigeTotaalAantal + $mutatie['Aantal'];
            break;
          case "V" :
            // Verkopen
            $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = $vorigeTotaalAantal + $mutatie['Aantal'];
            $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = $vorigeBeginwaardeLopendeJaar;
            break;
          case "V/O" :
            // Verkopen / openen
            if ($vorigeTotaalAantal == 0)
            {
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = $mutatie['Valutakoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = $mutatie['Fondskoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeWaarde'] = $mutatie['Fondskoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeValutakoers'] = $mutatie['Valutakoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = $mutatie['Aantal'];
              $fondswaarden[$fondsen[$a]['Fonds']]['voorgaandejarenActief'] = 1;
            }
            else
            {
              $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = ($vorigeTotaalAantal + $mutatie['Aantal']);
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeWaarde'] = ((($vorigeTotaalAantal * $vorigeHistorischeWaarde) + ($mutatie['Aantal'] * $mutatie['Fondskoers']))/($vorigeTotaalAantal + $mutatie['Aantal']));
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeValutakoers'] = ((($vorigeTotaalAantal * $vorigeHistorischeWaarde * $vorigeHistorischeValutakoers)+($mutatie['Aantal'] * $mutatie['Fondskoers'] * $mutatie['Valutakoers']))/(($vorigeTotaalAantal * $vorigeHistorischeWaarde) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])));
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = ((($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar) + ($mutatie['Aantal'] * $mutatie['Fondskoers']))/($vorigeTotaalAantal + $mutatie['Aantal']));
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = ((($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar * $vorigeBeginwaardeValutaLopendeJaar)+($mutatie['Aantal'] * $mutatie['Fondskoers'] * $mutatie['Valutakoers']))/(($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])));
              
            }
            
            break;
          case "V/S" :
            // Verkopen / sluiten
            $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = ($vorigeTotaalAantal + $mutatie['Aantal']);
            break;
          default :
            $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] += $mutatie['Aantal'];
            $_error = "Fout ongeldig tranactietype!!";
            break;
          
          
        }
        
        $vorigeTotaalAantal = $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'];
        $vorigeHistorischeWaarde = $fondswaarden[$fondsen[$a]['Fonds']]['historischeWaarde'];
        $vorigeHistorischeValutakoers = $fondswaarden[$fondsen[$a]['Fonds']]['historischeValutakoers'];
        $vorigeBeginwaardeLopendeJaar = $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'];
        $vorigeBeginwaardeValutaLopendeJaar = $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'];
        $vorigeHistorischeRapportageValutakoers = $fondswaarden[$fondsen[$a]['Fonds']]['historischeRapportageValutakoers'];
      }
      //echo $fondsen[$a]['Fonds']." " .	$fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar']. "<br>";flush();
      if (!isset($koersStartOpgehaald) && $beginDatum != "$jaar-01-01")
      {
        // haal startdatum valuta koers op!
        $startvaluta = array();
        $q = "SELECT Koers,Datum FROM Valutakoersen WHERE Valuta = '" . $fondswaarden[$fondsen[$a]['Fonds']]['valuta'] . "' AND Datum <= '" . $beginDatum . "' ORDER BY Datum DESC LIMIT 1";
        $DB2 = new DB();
        $DB2->SQL($q);
        $DB2->Query();
        $startvaluta = $DB2->NextRecord();
        $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = $startvaluta['Koers'];
        
        // haal startdatum fonds koers op!
        $startfonds = array();
        $q = "SELECT Koers,Datum FROM Fondskoersen WHERE Fonds = '" . $fondsen[$a]['Fonds'] . "' AND Datum <= '" . $beginDatum . "' ORDER BY Datum DESC LIMIT 1";
        $DB2 = new DB();
        $DB2->SQL($q);
        $DB2->Query();
        $startfonds = $DB2->NextRecord();
        $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = $startfonds['Koers'];
      }
      $counter++;
    }
    
    
    return $fondswaarden[$fondsen[$a]['Fonds']];
  }
  
  function getValuation($portefeuille, $datum,$consolidatie=false)
  {
    global $__appvar;
    $query = "SELECT
fondsOmschrijving,
totaalAantal,
actueleFonds,
totaalAantal*historischeWaarde*fondsEenheid*historischeValutakoers as historischeWaardeEur,
beginPortefeuilleWaardeEuro,
actuelePortefeuilleWaardeEuro,
TijdelijkeRapportage.type,
if(TijdelijkeRapportage.type='rekening',1,0) as volgorde,
valuta
FROM
TijdelijkeRapportage
WHERE
portefeuille='" . mysql_real_escape_string($portefeuille) . "' AND
rapportageDatum='$datum'
" . $__appvar['TijdelijkeRapportageMaakUniek']." ORDER BY volgorde,fondsOmschrijving ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $regels = array();
    $totaleWaarde = 0;
    $i=0;
    while ($data = $DB->nextRecord())
    {
      if($data['type']=='rekening' && $consolidatie==true)
      {
        $regels[$i]['fondsOmschrijving'] = 'Cash';
        $regels[$i]['totaalAantal'] += $data['totaalAantal'];
        $regels[$i]['actueleFonds'] = $data['actueleFonds'];
        $regels[$i]['historischeWaardeEur'] += $data['totaalAantal'];
        $regels[$i]['beginPortefeuilleWaardeEuro'] += $data['beginPortefeuilleWaardeEuro'];
        $regels[$i]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
      }
      else
      {
        $regels[$i] = $data;
        $i++;
      }
      $totaleWaarde += $data['actuelePortefeuilleWaardeEuro'];
    }
    foreach ($regels as $index => $data)
    {
      $regels[$index]['aandeel'] = $data['actuelePortefeuilleWaardeEuro'] / $totaleWaarde * 100;
    }
    
    return $regels;
  }
  
  function getValue($portefeuille, $datum)
  {
    global $__appvar;
    $query = "SELECT sum(actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro
FROM
TijdelijkeRapportage WHERE
portefeuille='" . mysql_real_escape_string($portefeuille) . "' AND
rapportageDatum='$datum'
" . $__appvar['TijdelijkeRapportageMaakUniek'];
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $data = $DB->nextRecord();
    
    return $data['actuelePortefeuilleWaardeEuro'];
  }
  
  function addValuation($portefeuille,$consolidatie=false)
  {
    $valutationData = $this->getValuation($portefeuille, $this->rapportageDatum,$consolidatie);
    
    $this->pdf->setWidths(array(65+ 23+ 23+ 23, 23, 20, 15));
    $this->pdf->setAligns(array('L', 'R', 'R', 'R', 'R', 'R', 'C'));
    $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst_l103('Summary of all your investments over the given period', $this->pdf->rapport_taal)));//'PORTFOLIO VALUATION'
    $this->pdf->setWidths(array(65, 23, 23, 23, 23, 20, 15));
    if($this->pdf->rapport_taal==3)//frans
    {
      $hoogte = 6+$this->pdf->rowHeight;
    }
    else
    {
      $hoogte = 6;
    }
  
  
    $this->pdf->rect($this->pdf->marge, $this->pdf->getY(), $this->pdf->w - $this->pdf->marge * 2, $hoogte, 'F');
    $this->pdf->line($this->pdf->marge, $this->pdf->getY(), $this->pdf->w - $this->pdf->marge, $this->pdf->getY());
    $this->pdf->line($this->pdf->marge, $this->pdf->getY()+$hoogte, $this->pdf->w - $this->pdf->marge, $this->pdf->getY()+$hoogte);
    $this->pdf->ln(1);
    $this->pdf->row(array(vertaalTekst_l103('Security Name', $this->pdf->rapport_taal),
                      vertaalTekst_l103('Quantity', $this->pdf->rapport_taal),
                      vertaalTekst_l103('Market Price', $this->pdf->rapport_taal),
                      vertaalTekst_l103('Book Value', $this->pdf->rapport_taal),
                      vertaalTekst_l103('Market Value', $this->pdf->rapport_taal),
                      vertaalTekst_l103('%of Assets', $this->pdf->rapport_taal),
                      vertaalTekst_l103('Currency', $this->pdf->rapport_taal)));
    $this->pdf->ln(1);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $waardeTotaal = 0;
    foreach ($valutationData as $row)
    {
      $this->pdf->row(array(vertaalTekst_l103($row['fondsOmschrijving'], $this->pdf->rapport_taal),
                        $this->formatGetal($row['totaalAantal'], 6),
                        $this->formatGetal($row['actueleFonds'], 2),
                        $this->formatGetal($row['historischeWaardeEur'], 2),
                        $this->formatGetal($row['actuelePortefeuilleWaardeEuro'], 2),
                        $this->formatGetal($row['aandeel'], 2) . '%',
                        $row['valuta']));
      $waardeTotaal += $row['actuelePortefeuilleWaardeEuro'];
    }
    
    $this->pdf->rect($this->pdf->marge, $this->pdf->getY(), $this->pdf->w - $this->pdf->marge * 2, 6, 'F');
    $this->pdf->line($this->pdf->marge, $this->pdf->getY(), $this->pdf->w - $this->pdf->marge, $this->pdf->getY());
    $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
    $this->pdf->ln(1);
    $this->pdf->setWidths(array(65 + 23 + 23 + 23, 23, 20 + 15));
    $this->pdf->setAligns(array('R', 'R', 'C'));
    $this->pdf->row(array(vertaalTekst_l103('Portfolio Value', $this->pdf->rapport_taal),
                      $this->formatGetal($waardeTotaal, 2), '', ''));
    $this->pdf->ln(2);
    $waarde = $this->getValue($portefeuille, $this->rapportageDatumVanaf);
    $this->pdf->row(array(vertaalTekst_l103('Portfolio Value for previous month end', $this->pdf->rapport_taal),
                      $this->formatGetal($waarde, 2), $this->pdf->portefeuilledata['periodStart']));
    $this->pdf->ln(1);
    $this->pdf->line($this->pdf->marge, $this->pdf->getY(), $this->pdf->w - $this->pdf->marge, $this->pdf->getY());
  }
  
  function getMaanden($julBegin, $julEind)
  {
    $eindjaar = date("Y", $julEind);
    $eindmaand = date("m", $julEind);
    $beginjaar = date("Y", $julBegin);
    $beginmaand = date("m", $julBegin);
    
    $i = 0;
    $stop = mktime(0, 0, 0, $eindmaand, 0, $eindjaar);
    $counterStart = 0;
    $datum = array();
    while ($counterStart < $stop)
    {
      $counterStart = mktime(0, 0, 0, $beginmaand + $i, 0, $beginjaar);
      $counterEnd = mktime(0, 0, 0, $beginmaand + $i + 1, 0, $beginjaar);
      if ($counterEnd >= $julEind)
      {
        $counterEnd = $julEind;
      }
      
      if ($i == 0)
      {
        $datum[$i]['start'] = date('Y-m-d', $julBegin);
      }
      else
      {
        $datum[$i]['start'] = date('Y-m-d', $counterStart);
      }
      
      $datum[$i]['stop'] = date('Y-m-d', $counterEnd);
      
      if ($datum[$i]['start'] == $datum[$i]['stop'])
      {
        unset($datum[$i]);
      }
      $i++;
    }
    
    return $datum;
  }
  
  function getLanden()
  {
    if(!isset($this->pdf->isoLanden))
    {
      $db = new DB();
      $query = "SELECT landCodeKort,OmschrijvingNL,OmschrijvingEN FROM ISOLanden order by landcode";
      $db->SQL($query);
      $db->Query();
      while ($data = $db->nextRecord())
      {
        $this->pdf->isoLanden[$data['landCodeKort']] = $data['OmschrijvingEN'];
      }
    }
  }
  
  function setPortefeuilleData($portefeuille)
  {
    $db = new DB();
    $query = "SELECT Startdatum,Einddatum,Memo FROM Portefeuilles WHERE Portefeuille='" . mysql_real_escape_string($portefeuille) . "'";
    $db->SQL($query);
    $db->Query();
    $this->portefeuilleData = $db->nextRecord();
    if(db2jul($this->portefeuilleData['Einddatum']) < db2jul($this->rapportageDatum))
    {
      return 'overslaan';
    }
    if(substr($this->portefeuilleData['Startdatum'], 0, 4)=='0000' || $this->portefeuilleData['Startdatum']=='')
    {
      return 'overslaan';
    }
    if (substr($this->portefeuilleData['Startdatum'], 0, 4) < 2000)
    {
      $this->portefeuilleData['Startdatum'] = $this->rapportageDatumVanaf;
    }
    
    if ($portefeuille <> $this->portefeuille)
    {
      //vulTijdelijkeTabel($this->berekenPortefeuilleWaarde($portefeuille, $this->rapportageDatumVanaf), $portefeuille, $this->rapportageDatumVanaf);
      //vulTijdelijkeTabel($this->berekenPortefeuilleWaarde($portefeuille, $this->rapportageDatum), $portefeuille, $this->rapportageDatum);
      vulTijdelijkeTabel(berekenPortefeuilleWaarde($portefeuille, $this->rapportageDatumVanaf,(substr($this->rapportageDatumVanaf, 5, 5) == '01-01')?true:false,'EUR',$this->rapportageDatumVanaf), $portefeuille, $this->rapportageDatumVanaf);
      vulTijdelijkeTabel(berekenPortefeuilleWaarde($portefeuille, $this->rapportageDatum,(substr($this->rapportageDatum, 5, 5) == '01-01')?true:false,'EUR',$this->rapportageDatumVanaf), $portefeuille, $this->rapportageDatum);
    }
    return 'ok';
  }
  
  function getMaandPerf($portefeuille, $start, $stop)
  {
    $db = new DB();
    $maanden = $this->getMaanden(db2jul($start), db2jul($stop));
    $maandPerformance = array();
    foreach ($maanden as $periode)
    {
      $query = "SELECT indexWaarde, Datum, PortefeuilleWaarde
		            FROM HistorischePortefeuilleIndex
		            WHERE
		            Categorie = 'Totaal' AND periode='m' AND
		            portefeuille = '" . $portefeuille . "' AND
		            Datum = '" . substr($periode['stop'], 0, 10) . "' ";
      $db->SQL($query);
      $db->Query();
      $dbData = $db->nextRecord();
      if (isset($dbData['indexWaarde']))
      {
        $maandPerformance[$periode['stop']] = $dbData['indexWaarde'];
      }
      else
      {
        $maandPerformance[$periode['stop']] = $this->calculatePerf($portefeuille, $periode['start'], $periode['stop']);
      }
    }
    
    return $maandPerformance;
  }
  
  function berekenPortefeuilleWaarde($portefeuille, $datum)
  {
    return berekenPortefeuilleWaardeQuick($portefeuille, $datum, (substr($datum, 5, 5) == '01-01')?true:false);
  }
  
  function calculatePerf($portefeuille, $beginDatum, $eindDatum, $valuta = 'EUR')
  {
    $DB = new DB();
    if (substr($beginDatum, 5, 5) == '12-31')
    {
      $beginDatum = (substr($beginDatum, 0, 4) + 1) . '-01-01';
    }
    
    if ($valuta != "EUR")
    {
      $koersQuery = " / (SELECT Koers FROM Valutakoersen WHERE Valuta='" . $valuta . "' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    }
    else
    {
      $koersQuery = "";
    }
    
    if (db2jul($beginDatum) <= db2jul($this->portefeuilleData['Startdatum']))
    {
      $wegingsDatum = date('Y-m-d', db2jul($this->portefeuilleData['Startdatum']) + 86400);
    } //$startDatum['Startdatum'];
    else
    {
      $wegingsDatum = $beginDatum;
    }
    
    
    $fondswaarden['beginmaand'] = $this->berekenPortefeuilleWaarde($portefeuille, $beginDatum);//,(substr($beginDatum, 5, 5) == '01-01')?true:false);//,$valuta,$beginDatum
    $totaalWaarde = array();
    foreach ($fondswaarden['beginmaand'] as $regel)
    {
      $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
    }
    $fondswaarden['eindmaand'] = $this->berekenPortefeuilleWaarde($portefeuille, $eindDatum);//,(substr($eindDatum, 5, 5) == '01-01')?true:false);//,$valuta,$beginDatum
    
    foreach ($fondswaarden['eindmaand'] as $regel)
    {
      $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];
    }
    
    $query = "SELECT " .
      "SUM(((TO_DAYS('" . $eindDatum . "') - TO_DAYS(Rekeningmutaties.Boekdatum)) " .
      "  / (TO_DAYS('" . $eindDatum . "') - TO_DAYS('" . $wegingsDatum . "')) " .
      "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, " .
      "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 " .
      "FROM  (Rekeningen, Portefeuilles,Grootboekrekeningen )
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening " .
      "WHERE " .
      "Rekeningen.Portefeuille = '" . $portefeuille . "' AND " .
      "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND " .
      "Rekeningmutaties.Verwerkt = '1' AND " .
      "Rekeningmutaties.Boekdatum > '" . $beginDatum . "' AND " .
      "Rekeningmutaties.Boekdatum <= '" . $eindDatum . "' AND
	Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
    $DB->SQL($query);
    $DB->Query();
    $weging = $DB->NextRecord();
    
    $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
    $performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging['totaal2']) / $gemiddelde) * 100;
    
    return $performance;
    
  }
  
  function addPerformance($portefeuille)
  {
    
    $maandPerformance = $this->getMaandPerf($portefeuille, $this->portefeuilleData['Startdatum'], $this->rapportageDatum);
    $maandPerf = 0;
    $periodePerf = 0;
    $ytdPerf = 0;
    $stdPerf = 0;
    $huidigeJaar = substr($this->rapportageDatum, 0, 4);
    foreach ($maandPerformance as $maand => $perf)
    {
      $maandPerf = $perf;
      if (db2jul($maand) > db2jul($this->rapportageDatumVanaf))
      {
        $periodePerf = ((1 + $periodePerf / 100) * (1 + $perf / 100) - 1) * 100;
      }
      if (substr($maand, 0, 4) == $huidigeJaar)
      {
        $ytdPerf = ((1 + $ytdPerf / 100) * (1 + $perf / 100) - 1) * 100;
      }
      $stdPerf = ((1 + $stdPerf / 100) * (1 + $perf / 100) - 1) * 100;
    }
    //listarray($maandPerformance);
    //ob_flush(); echo $this->rapportageDatum."$portefeuille $maandPerf $periodePerf $ytdPerf $stdPerf <br>\n";
    
    
    $this->pdf->setWidths(array(65+23+23+23, 23, 20, 15));
    $this->pdf->setAligns(array('L', 'R', 'R', 'R', 'R', 'R', 'C'));
    $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst_l103('Summary of account performance', $this->pdf->rapport_taal)));
    $this->pdf->setWidths(array(65, 23, 23, 23, 23, 20, 15));
    if($this->pdf->rapport_taal==3)//frans
    {
      $hoogte = 6+$this->pdf->rowHeight;
    }
    else
    {
      $hoogte = 6;
    }
    $this->pdf->rect($this->pdf->marge, $this->pdf->getY(), $this->pdf->w - $this->pdf->marge * 2, $hoogte, 'F');
    $this->pdf->line($this->pdf->marge, $this->pdf->getY(), $this->pdf->w - $this->pdf->marge, $this->pdf->getY());
    $this->pdf->line($this->pdf->marge, $this->pdf->getY() + $hoogte, $this->pdf->w - $this->pdf->marge, $this->pdf->getY() + $hoogte);
    $this->pdf->ln(1);
    $this->pdf->row(array('',
                      vertaalTekst_l103('This Month', $this->pdf->rapport_taal),
                      vertaalTekst_l103('Year to Date', $this->pdf->rapport_taal),
                      vertaalTekst_l103('Since Inception', $this->pdf->rapport_taal)));
    $this->pdf->ln(1);
    $this->pdf->row(array(vertaalTekst_l103('Rate of Return', $this->pdf->rapport_taal)));
    $this->pdf->ln($this->pdf->rowHeight * -1);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->row(array('',
                      $this->formatGetal($maandPerf, 2) . '%',
                      $this->formatGetal($ytdPerf, 2) . '%',
                      $this->formatGetal($stdPerf, 2) . '%'));
    
    $this->pdf->ln();
    
  }
  
  function getTransactions($portefeuille)
  {
    
    $query = "SELECT Fondsen.Omschrijving, " .
      "Fondsen.Fondseenheid, " .
      "Rekeningmutaties.Boekdatum, " .
      "Rekeningmutaties.id,
		Rekeningmutaties.Transactietype,
		Rekeningmutaties.Valuta,
		 Rekeningmutaties.Afschriftnummer,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, Rekeningmutaties.Fonds,  " .
      "Rekeningmutaties.Fondskoers, " .
      "Rekeningmutaties.Debet as Debet, " .
      "Rekeningmutaties.Credit as Credit, Rekeningmutaties.Bedrag, " .
      "Rekeningmutaties.Valutakoers
      FROM Rekeningmutaties, Fondsen, Rekeningen, Portefeuilles, Grootboekrekeningen " .
      "WHERE " .
      "Rekeningmutaties.Rekening = Rekeningen.Rekening AND " .
      "Rekeningmutaties.Fonds = Fondsen.Fonds AND " .
      "Rekeningen.Portefeuille = '" . mysql_real_escape_string($portefeuille) . "' AND " .
      "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND " .
      "Rekeningmutaties.Verwerkt = '1' AND " .
      "Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND " .
      "Rekeningmutaties.Transactietype <> 'B' AND " .
      "Grootboekrekeningen.FondsAanVerkoop = '1' AND " .
      "Rekeningmutaties.Boekdatum > '" . $this->rapportageDatumVanaf . "' AND " .
      "Rekeningmutaties.Boekdatum <= '" . $this->rapportageDatum . "' " .
      "ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $buffer = array();
    while ($mutaties = $DB->nextRecord())
    {
      //$omschrijvingParts=explode(" ",$mutaties['rekeningOmschrijving'],2);
      //$omschrijvingParts[0]=vertaalTekst_l103($omschrijvingParts[0], $this->pdf->rapport_taal);
      //$mutaties['rekeningOmschrijving']=implode(" ",$omschrijvingParts);
      $buffer[] = $mutaties;
      
    }
    
    return $buffer;
  }
  
  function getMutations($portefeuille)
  {
    $query = "SELECT Rekeningmutaties.Valuta, " .
      "Rekeningmutaties.Boekdatum, " .
      "Rekeningmutaties.Omschrijving ," .
      "ABS(Rekeningmutaties.Aantal) AS Aantal, " .
      "Rekeningmutaties.Debet as Debet, " .
      "Rekeningmutaties.Credit as Credit, " .
      "Rekeningmutaties.Bedrag as Bedrag, " .
      "Rekeningmutaties.Valutakoers, " .
      "Rekeningmutaties.Rekening, " .
      "Rekeningmutaties.Grootboekrekening, " .
      "Rekeningmutaties.Afschriftnummer, " .
      "Grootboekrekeningen.Omschrijving AS gbOmschrijving, " .
      "Grootboekrekeningen.Opbrengst, " .
      "Grootboekrekeningen.Kosten, " .
      "Grootboekrekeningen.Afdrukvolgorde " .
      "FROM Rekeningmutaties, Rekeningen,  Grootboekrekeningen " .
      "WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening " .
      "AND Rekeningen.Portefeuille = '" . mysql_real_escape_string($portefeuille) . "' " .
      "AND Rekeningmutaties.Verwerkt = '1' " .
      "AND Rekeningmutaties.Boekdatum > '" . $this->rapportageDatumVanaf . "' " .
      "AND Rekeningmutaties.Boekdatum <= '" . $this->rapportageDatum . "' " .
      "AND Grootboekrekeningen.Afdrukvolgorde IS NOT NULL " .
      "AND Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening " .
      "AND (Grootboekrekeningen.Kosten = '1' OR Grootboekrekeningen.Opbrengst = '1' OR Grootboekrekeningen.Onttrekking = '1' OR Grootboekrekeningen.Storting = '1' OR Grootboekrekeningen.Kruispost = '1') " .
      "ORDER BY  Rekeningmutaties.Boekdatum,gbOmschrijving,Omschrijving";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $buffer = array();
    while ($mutaties = $DB->nextRecord())
    {
      $omschrijvingParts=explode(" ",$mutaties['Omschrijving'],2);
      $omschrijvingParts[0]=vertaalTekst_l103($omschrijvingParts[0], $this->pdf->rapport_taal);
      $mutaties['Omschrijving']=implode(" ",$omschrijvingParts);
      $buffer[] = $mutaties;
      
    }
    
    return $buffer;
  }
  
  function addTransactions($portefeuille)
  {
    $transacties = $this->getTransactions($portefeuille);
    $this->pdf->ln();
    $this->pdf->setWidths(array(200));
    $this->pdf->setAligns(array('L', 'L', 'R', 'L', 'R', 'R', 'C'));
    $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst_l103('TRANSACTION SUMMARY', $this->pdf->rapport_taal)));
    $this->pdf->setWidths(array(20, 15, 23, 70, 23, 20, 15));
    $this->pdf->rect($this->pdf->marge, $this->pdf->getY(), $this->pdf->w - $this->pdf->marge * 2, 6, 'F');
    $this->pdf->line($this->pdf->marge, $this->pdf->getY(), $this->pdf->w - $this->pdf->marge, $this->pdf->getY());
    $this->pdf->line($this->pdf->marge, $this->pdf->getY() + 6, $this->pdf->w - $this->pdf->marge, $this->pdf->getY() + 6);
    $this->pdf->ln(1);
    $this->pdf->row(array(vertaalTekst_l103('Date', $this->pdf->rapport_taal),
                      vertaalTekst_l103('Action', $this->pdf->rapport_taal),
                      vertaalTekst_l103('Quantity', $this->pdf->rapport_taal),
                      vertaalTekst_l103('Security Name', $this->pdf->rapport_taal),
                      vertaalTekst_l103('Price', $this->pdf->rapport_taal),
                      vertaalTekst_l103('Net Amount', $this->pdf->rapport_taal),
                      vertaalTekst_l103('Currency', $this->pdf->rapport_taal)));
    $this->pdf->ln(2);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    foreach ($transacties as $regel)
    {
      $this->pdf->row(array(date('d-M-Y', db2jul($regel['Boekdatum'])),
                        $regel['Transactietype'],
                        $this->formatGetal($regel['Aantal'], 4),
                        $regel['Omschrijving'],
                        $this->formatGetal($regel['Fondskoers'], 2),
                        $this->formatGetal($regel['Bedrag'], 2),
                        $regel['Valuta']));
    }
    $this->pdf->ln();
  }
  
  function addMutations($portefeuille)
  {
    $mutations = $this->getMutations($portefeuille);
    if (count($mutations) == 0)
    {
      return '';
    }
    $this->pdf->ln();
    $this->pdf->setWidths(array(200));
    $this->pdf->setAligns(array('L', 'L', 'L', 'R', 'C'));
    $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
    $this->pdf->setWidths(array(20+ 30+ 70, 30, 20));
    $this->pdf->row(array(vertaalTekst_l103('Summary of your withdrawals and deposits', $this->pdf->rapport_taal)));
    $this->pdf->setWidths(array(20, 30, 70, 30, 20));
    $this->pdf->rect($this->pdf->marge, $this->pdf->getY(), $this->pdf->w - $this->pdf->marge * 2, 6, 'F');
    $this->pdf->line($this->pdf->marge, $this->pdf->getY(), $this->pdf->w - $this->pdf->marge, $this->pdf->getY());
    $this->pdf->line($this->pdf->marge, $this->pdf->getY() + 6, $this->pdf->w - $this->pdf->marge, $this->pdf->getY() + 6);
    $this->pdf->ln(1);
    $this->pdf->row(array(vertaalTekst_l103('Date', $this->pdf->rapport_taal),
                      vertaalTekst_l103('Action', $this->pdf->rapport_taal),
                      vertaalTekst_l103('Description', $this->pdf->rapport_taal),
                      vertaalTekst_l103('Net Amount', $this->pdf->rapport_taal),
                      vertaalTekst_l103('Currency', $this->pdf->rapport_taal)));
    $this->pdf->ln(2);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
    foreach ($mutations as $regel)
    {
      $this->pdf->row(array(date('d-M-Y', db2jul($regel['Boekdatum'])),
                        vertaalTekst_l103($regel['gbOmschrijving'], $this->pdf->rapport_taal),
                        $regel['Omschrijving'],
                        $this->formatGetal($regel['Bedrag'], 2),
                        $regel['Valuta']));
    }
    
    $this->pdf->ln();
  }
  
  function addComment()
  {
    $this->pdf->ln();
    $this->pdf->setWidths(array(210 - $this->pdf->marge * 2));
    $this->pdf->setAligns(array('L'));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
    $disclaimer=array();
    $disclaimer[1]='*Please note that current deposits relate to your contributions that have been posted to the app but have not yet been invested. These deposits will be invested in the coming days and will be counted in your next monthly account statement.';
    $disclaimer[0]='*Houd er rekening mee dat huidige stortingen betrekking hebben op uw bijdragen die op de app zijn gedaan maar nog niet zijn geïnvesteerd. Deze stortingen worden de komende dagen belegd en bij uw volgende maandelijkse rekeningoverzicht meegerekend.';
    $disclaimer[3]='*Veuillez noter que les dépôts en cours concernent vos contributions qui ont été comptabilisées dans l’application mais qui n’ont pas encore été investis. Ces dépôts seront investis dans les prochains jours et seront comptabilisés dans votre prochain relevé de compte mensuel.';
  
    if(isset($disclaimer[$this->pdf->rapport_taal]))
      $this->pdf->row(array($disclaimer[$this->pdf->rapport_taal]));
    else
      $this->pdf->row(array(vertaalTekst_l103('Please be advised that contributions take a few days to settle, and deposits with a settlement data after the end of the month will only be reflected on your upcoming monthly statement.',$this->pdf->rapport_taal)));
  }
  
  function addDisclaimer()
  {
    $this->pdf->ln(15);
    //echo "|".$this->pdf->getY()." | ".$this->pdf->h."<br>\n";ob_flush();
    if($this->pdf->getY()>$this->pdf->h-60)
    {
      $this->pdf->addPage('P');
     // $this->pdf->ln(10);
    }
    $this->pdf->setY($this->pdf->h-60);
    $this->pdf->setWidths(array(210 - $this->pdf->marge * 2));
    $this->pdf->setAligns(array('L'));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
   
    $disclaimer=array();
    $disclaimer[1]='Moka Asset Management Europe has a permit as referred to in Section 2:96 of the Dutch Financial Supervision Act ("Wft") and is supervised by De Nederlandsche Bank N.V. (DNB) and the Netherlands Authority for the Financial Markets (AFM). It has its registered office in Mijdrecht at Veenweg 158 C , and is registered at the Chamber of Commerce under trade register number 24275087. For more information please refer to our website https://moka.ai/france/investir

Investing brings risks, past performance is not a reliable indicator of future results. The value of your investments can fluctuate. You should be aware that certain types of funds might carry greater investment risk than other investment funds. The listed price data are based on the last known prices on the mentioned reporting date. The information in this report has been derived from sources we consider reliable. No guarantee or declaration is given concerning the correctness or completeness of this information. If you do not agree with this statement, please notify MOKA as soon as possible.';
    $disclaimer[0]='Moka Asset Management Europe beschikt over een vergunning als bedoeld in artikel 2:96 van de Wet op het financieel toezicht (Wft) en staat onder toezicht van De Nederlandsche Bank N.V. (DNB) en de Autoriteit Financiële Markten (AFM). Zij is statutair gevestigd te Mijdrecht aan de Veenweg 158 C en is ingeschreven bij de Kamer van Koophandel onder handelsregisternummer 24275087. Voor meer informatie verwijzen wij u naar onze website https://moka.ai/france/investir

Beleggen brengt risico\'s met zich mee, in het verleden behaalde resultaten zijn geen betrouwbare indicator voor toekomstige resultaten. De waarde van uw beleggingen kan fluctueren. U dient zich ervan bewust te zijn dat bepaalde soorten fondsen mogelijk een groter beleggingsrisico inhouden dan andere beleggingsfondsen. De vermelde koersgegevens zijn gebaseerd op de laatst bekende koersen op de genoemde rapportagedatum. De informatie in dit rapport is ontleend aan bronnen die wij betrouwbaar achten. Er wordt geen garantie of verklaring gegeven over de juistheid of volledigheid van deze informatie. Als u het niet eens bent met deze verklaring, meld dit dan zo snel mogelijk aan MOKA.';
    $disclaimer[3]='Moka Asset Management Europe est titulaire d\'une autorisation visée à l\'article 2:96 de la loi néerlandaise sur la surveillance financière ("Wft") et supervisée par la Nederlandsche Bank N.V. (DNB) ainsi que par l\'Autorité néerlandaise des marchés financiers (AFM). Elle a son siège social à Mijdrecht, Veenweg 158 C aux Pays Bas et est enregistrée à la Chambre de commerce sous le numéro 24275087. Pour plus d\'informations, veuillez consulter notre site web https://moka.ai/france/investir

Investir comporte des risques, les performances passées ne sont pas un indicateur fiable des résultats futurs. La valeur de vos investissements peut fluctuer. Vous devez savoir que certains types de fonds peuvent comporter un risque d\'investissement plus important que d\'autres fonds d\'investissement. Les données relatives aux prix indiqués sont basées sur les derniers prix connus à la date de la déclaration mentionnée. Les informations contenues dans ce rapport proviennent de sources que nous considérons comme fiables. Aucune garantie ou déclaration n\'est donnée quant à l\'exactitude ou l\'exhaustivité de ces informations. Si vous n\'êtes pas d\'accord avec cette déclaration, veuillez en informer le support client Moka dès que possible.';
    if(isset($disclaimer[$this->pdf->rapport_taal]))
      $this->pdf->row(array($disclaimer[$this->pdf->rapport_taal]));
    else
      $this->pdf->row(array($disclaimer[1]));
  }
  
  function addFront()
  {
    $text=array();
    $kop[0]="Maandelijks overzicht van de beleggingsrekening";
    $text[0]="Beste ".$this->pdf->portefeuilledata['Naam'].",

Hieronder vindt u het maandoverzicht van uw maatschappelijk verantwoorde beleggingen bij Moka, voor de afgelopen maand.
Op dit maandoverzicht staan uw beleggingsrekeningen, de fondsen waarin uw geld is belegd en de prestaties van uw beleggingen.

Het rapport is als volgt samengesteld:

1. Een overzicht van alle maatschappelijk verantwoorde fondsen (i.e. instrumenten) waarin uw geld is belegd
2. Een gedetailleerd rendementsoverzicht voor elke beleggingsrekening

Let op: elk beleggingsdoel dat in de Moka-applicatie wordt gecreëerd, komt overeen met een effectenrekening in uw rapport, waarvan de naam is samengesteld uit een reeks letters en cijfers.

Per account vindt u de volgende prestatie-indicatoren:
* Het totale bedrag dat tot dusver is geïnvesteerd ('totale geconsolideerde activa')
* Uw winsten of verliezen tijdens de periode als een percentage
* De prestatie (positief of negatief) van uw beleggingsdoelstelling voor de huidige maand, sinds het begin van het jaar en sinds het begin.

Op pagina 3 leest u hoe uw beleggingen eruitzien alsook de lijst met transacties die op uw rekening zijn uitgevoerd (aan- en verkopen van effecten, evenals stortingen en
geldopnames van en naar uw bankrekening).

Als u vragen heeft, bekijk dan onze FAQ of neem contact op met ons support team op support@moka.ai. Via onze blog stellen we ook een verscheidenheid aan inhoud beschikbaar mocht u meer informatie willen over beleggen.

Vriendelijke groeten,
Het Moka-team
";
  
    $kop[1]='Monthly overview of the investment account';
    $text[1]="Dear ".$this->pdf->portefeuilledata['Naam'].",

Below you will find the monthly overview of your socially responsible investments at Moka for the past month.
This monthly statement shows your investment accounts, the funds in which your money is invested and the performance of your investments.

The report is composed as follows:

1. An overview of all socially responsible funds (i.e. instruments) in which your money is invested
2. A detailed return statement for each investment account

Please note that each investment objective, that you created in the Moka application, corresponds to a trading account in your report, the name of which is composed of a series of letters and numbers.

You will find the following performance indicators per account:
* The total amount invested so far ('total consolidated assets')
* Your gains or losses during the period as a percentage
* The performance (positive or negative) of your investment objective for the current month, since the beginning of the year and since the start.

On page 3 you can read what your investments look like and the list of transactions made on your account (purchases and sales of securities, as well as deposits and cash withdrawals to and from your bank account).

If you have any questions, please check out our FAQ or contact our support team at support@moka.ai. We also make a variety of content available through our blog should you wish to learn more about investing.

Regards,
The Moka team";
  
  
    $kop[3]='Relevé de compte mensuel d’investissement';
    $text[3]="Bonjour ".$this->pdf->portefeuilledata['Naam'].",

Vous trouverez ci-dessous le relevé mensuel de vos investissements socialement responsables avec Moka, pour le mois écoulé.
Ce relevé mensuel recense vos comptes d’investissement, les fonds dans lesquels votre argent a été investi et la performance de vos investissements.

Le relevé est composé comme suit :
1.	Le récapitulatif de tous les fonds socialement responsables dans lesquels votre argent a été investi (dits « titres »)
2.	Le rapport détaillé de performance de chaque compte-titre

A noter, chaque objectif d’investissement créé dans l’application Moka correspond à un compte-titre dans votre rapport, dont le nom est composé d’une suite de chiffres et de lettres.

Pour chaque compte vous trouverez les indicateurs de performance suivants :
* Le montant total investi à ce stade (« total des actifs consolidés »)
* Vos gains ou pertes pendant la période en pourcentage
* La performance (positive ou négative) de votre objectif d’investissement pour la période du mois en cours, depuis le début de l’année et depuis sa création.

Vous trouverez le détail de la performance de vos investissements à partir de la page 3. Vous y trouverez également la liste des transactions effectuées sur votre compte (achats et ventes des titres, ainsi que les dépôts et les retraits d’argent de et vers votre compte bancaire).

En cas de questions, consultez notre FAQ (https://help.moka.ai/fr/collections/2452234-france) détaillée ci-dessous ou contactez notre équipe support à
support@moka.ai. Par le biais de notre blog (https://blog.moka.ai/), nous mettons également à disposition une variété de contenus pédagogiques pour en apprendre plus sur l’investissement.

Bien à vous,
L’équipe Moka";
    $this->pdf->addPage('P');
    $this->pdf->setY(80);
    $this->pdf->setWidths(array($this->pdf->w - $this->pdf->marge * 2));
  
    $this->pdf->setAligns(array('C'));
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    if(isset($text[$this->pdf->rapport_taal]))
      $this->pdf->row(array($kop[$this->pdf->rapport_taal]));
    else
      $this->pdf->row(array($kop[1]));
  
    $this->pdf->ln(8);
    
    $this->pdf->setAligns(array('L'));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

    if(isset($text[$this->pdf->rapport_taal]))
      $this->pdf->row(array($text[$this->pdf->rapport_taal]));
    else
      $this->pdf->row(array($text[1]));
    
  }
  
  
  function writeRapport()
  {
    $this->getLanden();
    $this->addFront();
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0], $this->pdf->rapport_fontcolor[1], $this->pdf->rapport_fontcolor[2]);
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor[0], $this->pdf->rapport_kop_bgcolor[1], $this->pdf->rapport_kop_bgcolor[2]);
 
    $this->setPortefeuilleData($this->portefeuille);
    $portefeuilles=$this->pdf->portefeuilles;
    $commentsGetoond=false;
    if (count($this->pdf->portefeuilles) > 1)
    {
      $this->pdf->AddPage('P');
      $this->addValuation($this->portefeuille,true);
      $this->addComment();
      $this->addDisclaimer();
      $commentsGetoond=true;
    }
    else
    {
      $portefeuilles=array($this->portefeuille);
    }
    
    foreach ($portefeuilles as $portefeuille)
    {
      $status=$this->setPortefeuilleData($portefeuille);
      if($status=='overslaan') // check op begin en einddatum
        continue;
  
      $this->pdf->subPortefeuille = $portefeuille;
      $this->pdf->AddPage('P');
      
      $this->addPerformance($portefeuille);
      $this->addValuation($portefeuille);
      $this->addTransactions($portefeuille);
      $this->addMutations($portefeuille);
      if($commentsGetoond==false)
        $this->addComment();
      $this->addDisclaimer();
      
    }
    unset($this->pdf->subPortefeuille);
    
  }
  
  
}

?>
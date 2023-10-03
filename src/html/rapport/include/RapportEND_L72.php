<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportVOLK_L72.php");

class RapportEND_L72
{
  
  function RapportEND_L72($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->portefeuille=$portefeuille;
    $this->rapportageDatumVanaf=$rapportageDatumVanaf;
    $this->rapportageDatum=$rapportageDatum;
    $this->VOLK=new RapportVOLK_L72($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->VOLK->viaEND=true;
  }
  
  
  function writeRapport()
  {
    $fondswaarden = $this->berekenPortefeuilleWaarde($this->portefeuille,$this->rapportageDatumVanaf,(substr($this->rapportageDatumVanaf, 5, 5) == '01-01')?true:false,$this->pdf->rapportageValuta,$this->rapportageDatumVanaf,2,true);
    vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,$this->rapportageDatumVanaf);
    $fondswaarden = $this->berekenPortefeuilleWaarde($this->portefeuille,$this->rapportageDatum,(substr($this->rapportageDatum, 5, 5) == '01-01')?true:false,$this->pdf->rapportageValuta,$this->rapportageDatumVanaf,2,true);
    vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,$this->rapportageDatum);
    $pre= new PreProcessor_L72($this->portefeuille,$this->rapportageDatum,$this->pdf);
    $this->VOLK->writeRapport();
  }
  
  
  function berekenPortefeuilleWaarde($portefeuille, $rapportageDatum, $min1dag = false, $rapportageValuta = 'EUR',$rapportageBeginDatum='',$afronding=2,$bewaarders=false)
  {
    /*
      datum = SQL datum
    */
    //if(substr($rapportageDatum,5,5)=='01-01')
    //  $min1dag=true;
    $fondswaardenClean = array();
    $fondswaardenRente = array();
    $rekeningwaarden 	 = array();
    
    
    $DB = new DB();
    $DB->SQL("SELECT Portefeuilles.Vermogensbeheerder,Vermogensbeheerders.geenStandaardSector FROM Portefeuilles
  LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
  WHERE Portefeuille='$portefeuille'");
    $DB->Query();
    $record = $DB->NextRecord();
    $geenStandaardSector=$record['geenStandaardSector'];
    $vermogensbeheerder=$record['Vermogensbeheerder'];
    
    
    $tmp=getAfdrukVolgordeOmschrijving($vermogensbeheerder);
    $afdrukvolgorde=$tmp['afdrukvolgorde'];
    $omschrijving=$tmp['omschrijving'];
    $hoofdcategorieOmschrijving=$tmp['hoofdcategorieOmschrijving'];
    $hoofdcategoriePerCategorie=$tmp['hoofdcategoriePerCategorie'];
    $hoofdsectorOmschrijving=$tmp['hoofdsectorOmschrijving'];
    $hoofdsectorPerSector=$tmp['hoofdsectorPerSector'];
    
    if($rapportageBeginDatum == '')
    {
      $beginJaar = '1970';
    }
    else
      $beginJaar = date("Y", db2jul($rapportageBeginDatum));
    $jaar = date("Y", db2jul($rapportageDatum));
    
    if ($beginJaar != '1970' && $jaar != $beginJaar && $jaar>$beginJaar)
    {
      for($jaren=$beginJaar;$jaren <= $jaar; $jaren++)
      {
        if(isset($jarenString))
        {
          $jarenString .= ",'$jaren'";
          
          if(isset($januariUitluiten))
            $januariUitluiten .=",'$jaren-01-01 00:00:00'";
          else
            $januariUitluiten = "'$jaren-01-01 00:00:00'";
        }
        else
        {
          $jarenString = "'$jaren'";
        }
      }
      $boekjarenFilter = " ( YEAR(Rekeningmutaties.Boekdatum) IN ($jarenString) ) ";
      $januariFilter = " Rekeningmutaties.Boekdatum NOT IN ($januariUitluiten) ";
      $q = "SELECT
	Rekeningmutaties.Fonds, ".
        "Portefeuilles.Depotbank,Portefeuilles.Vermogensbeheerder,
   Rekeningen.Depotbank as rekeningDepotbank,
  Fondsen.Lossingsdatum,
  Rekeningen.consolidatie,
  Fondsen.Forward
  FROM (Rekeningmutaties, Rekeningen, Portefeuilles, Fondsen)
	WHERE
	Rekeningmutaties.Fonds = Fondsen.Fonds AND
	Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	Rekeningen.Portefeuille = '".$portefeuille."' AND
	Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
  $boekjarenFilter AND $januariFilter AND
	Rekeningmutaties.Verwerkt = '1' AND
	Rekeningmutaties.Boekdatum <= '".$rapportageDatum."' AND
	Rekeningmutaties.GrootboekRekening = 'FONDS'
	GROUP BY Rekeningmutaties.Fonds
	ORDER BY Rekeningmutaties.Fonds";
    }
    else
    {
      $q = "SELECT
	Rekeningmutaties.Fonds, ".
        "Rekeningen.Depotbank as rekeningDepotbank,
   Portefeuilles.Depotbank,
   Portefeuilles.Vermogensbeheerder,
  Fondsen.Lossingsdatum,
  Rekeningen.consolidatie,
  Fondsen.Forward
  FROM (Rekeningmutaties, Rekeningen, Portefeuilles, Fondsen)
	WHERE
	Rekeningmutaties.Fonds = Fondsen.Fonds AND
	Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	Rekeningen.Portefeuille = '".$portefeuille."' AND
	Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
	YEAR(Rekeningmutaties.Boekdatum) = '".$jaar."' AND
	Rekeningmutaties.Verwerkt = '1' AND
	Rekeningmutaties.Boekdatum <= '".$rapportageDatum."' AND
  Rekeningmutaties.GrootboekRekening = 'FONDS'
	GROUP BY Rekeningmutaties.Fonds
	ORDER BY Rekeningmutaties.Fonds";
    }
    
    if($bewaarders==true)
    {
      $q = "SELECT
	Rekeningmutaties.Fonds, ".
        "Portefeuilles.Depotbank,
  Portefeuilles.Vermogensbeheerder,
  Fondsen.Lossingsdatum,
  Rekeningen.consolidatie,
  IF(Rekeningmutaties.Bewaarder <> '',	Rekeningmutaties.Bewaarder,IF (Rekeningen.Depotbank <> '',	Rekeningen.Depotbank,	Portefeuilles.Depotbank)) AS BewaarderSort
  FROM (Rekeningmutaties, Rekeningen, Portefeuilles, Fondsen)
	WHERE
	Rekeningmutaties.Fonds = Fondsen.Fonds AND
	Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	Rekeningen.Portefeuille = '".$portefeuille."' AND
	Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
	YEAR(Rekeningmutaties.Boekdatum) = '".$jaar."' AND
	Rekeningmutaties.Verwerkt = '1' AND
	Rekeningmutaties.Boekdatum <= '".$rapportageDatum."' AND
  Rekeningmutaties.GrootboekRekening = 'FONDS'
	GROUP BY Rekeningmutaties.Fonds , BewaarderSort
	ORDER BY Rekeningmutaties.Fonds";
    
    }
    $DB = new DB();
    $DB2 = new DB();
    $DB->SQL($q);
    $DB->Query();
    while($fonds = $DB->NextRecord())
    {
      $koppelingen = getFondsKoppelingen($fonds['Vermogensbeheerder'],$rapportageDatum,$fonds['Fonds'],$geenStandaardSector);
//listarray($koppelingen);
      foreach($koppelingen as $key=>$value)
        $fonds[$key]=$value;
      
      
      $fondsen[] = $fonds;
    }
    if($vermogensbeheerder=='RCN')
      $geenRente=true;
    else
      $geenRente=false;
    for($a=0; $a < count($fondsen); $a++)
    {
      // berekening van Fonds Waarden in een aparte functie gezet
      if($bewaarders==true)
      {
        $bewaarder=$fondsen[$a]['BewaarderSort'];
        $fondsen[$a]['rekeningDepotbank']=$bewaarder;
      }
      else
        $bewaarder='';
      $fondswaarden[$a] = $this->fondsWaardeOpdatum($portefeuille, $fondsen[$a]['Fonds'], $rapportageDatum, $rapportageValuta, $rapportageBeginDatum, '', $bewaarder);
      
    }
    
    
    for($a=0; $a <count($fondsen); $a++)
    {
      $fonds 	= $fondsen[$a];
      $data 	= $fondswaarden[$a];
      
      if(round($data['totaalAantal'],7) <> 0)
      {
        // bereken (virtuele) totalen met beginwaarden
        if($fonds['Forward']==1)
        {
          $beginPortefeuilleWaardeEuro  = ($data['totaalAantal']) * $data['beginwaardeLopendeJaar'];
          $beginPortefeuilleWaardeInValuta = $data['beginwaardeValutaLopendeJaar'] * $beginPortefeuilleWaardeEuro;
          
          // bereken totalen met actuele koers
          $actuelePortefeuilleWaardeEuro  = ($data['totaalAantal']) * $data['actueleFonds'];
          $actuelePortefeuilleWaardeInValuta = $data['actueleValuta'] * $actuelePortefeuilleWaardeEuro;
          
        }
        else
        {
          $beginPortefeuilleWaardeInValuta = ($data['fondsEenheid'] * $data['totaalAantal']) * $data['beginwaardeLopendeJaar'];
          $beginPortefeuilleWaardeEuro = $data['beginwaardeValutaLopendeJaar'] * $beginPortefeuilleWaardeInValuta;
          // bereken totalen met actuele koers
          $actuelePortefeuilleWaardeInValuta = ($data['fondsEenheid'] *$data['totaalAantal']) * $data['actueleFonds'];
          $actuelePortefeuilleWaardeEuro = $data['actueleValuta'] * $actuelePortefeuilleWaardeInValuta;
        }
        $renteParameters=getRenteParameters($fonds['Fonds'],$rapportageDatum);
        if($renteParameters['Lossingsdatum'] <> '0000-00-00' && $fonds['Lossingsdatum'] <> $renteParameters['Lossingsdatum'])
        {
          $fonds['Lossingsdatum'] = $renteParameters['Lossingsdatum'];
        }
        // maak nieuwe schone array
        $clean = $data;
        $clean['beginPortefeuilleWaardeInValuta'] 	= round($beginPortefeuilleWaardeInValuta,$afronding);
        $clean['beginPortefeuilleWaardeEuro'] 			= round($beginPortefeuilleWaardeEuro,$afronding);
        $clean['actuelePortefeuilleWaardeInValuta'] = round($actuelePortefeuilleWaardeInValuta,$afronding);
        $clean['actuelePortefeuilleWaardeEuro'] 		= round($actuelePortefeuilleWaardeEuro,$afronding);
        $clean['fonds'] 														= $fonds['Fonds'];
        $clean['beleggingssector'] 									= $fonds['beleggingssector'];
        $clean['beleggingscategorie'] 							= $fonds['beleggingscategorie'];
        $clean['Lossingsdatum']                   = $fonds['Lossingsdatum'];
        $clean['Regio']                           = $fonds['Regio'];
        $clean['AttributieCategorie']             = $fonds['AttributieCategorie'];
        $clean['afmCategorie']                    = $fonds['afmCategorie'];
        $clean['duurzaamCategorie']                    = $fonds['DuurzaamCategorie'];
        
        if($clean['Bewaarder']=='')
        {
          if($fonds['rekeningDepotbank']=='')
            $clean['Bewaarder']                     = $fonds['Depotbank'];
          else
            $clean['Bewaarder']                     = $fonds['rekeningDepotbank'];
        }
        
        $query="SELECT Omschrijving FROM FondsOmschrijvingVanaf WHERE Fonds='".$fonds['Fonds']."' AND Vanaf <= '$rapportageDatum' ORDER BY Vanaf DESC LIMIT 1";
        $DB->SQL($query);
        $DB->Query();
        $fondsOmschrijving = $DB->NextRecord();
        if($fondsOmschrijving['Omschrijving'] <> '')
          $clean['fondsOmschrijving']=$fondsOmschrijving['Omschrijving'];
        
        $query="SELECT FondsRapportagenaam FROM FondsExtraInformatie WHERE Fonds='".$fonds['Fonds']."'";
        $DB->SQL($query);
        $DB->Query();
        $fondsOmschrijving = $DB->NextRecord();
        if($fondsOmschrijving['FondsRapportagenaam'] <> '')
          $clean['fondsOmschrijving']=$fondsOmschrijving['FondsRapportagenaam'];
        
        $fondswaardenClean[] = $clean;
      }
    }
    
    //print_r($fondswaardenClean);
    //exit;
    
    $t = count($fondswaardenClean);
    for($a=0; $a <count($fondswaardenClean); $a++)
    {
      if($fondswaardenClean[$a]['renteBerekenen'] > 0 && $geenRente == false)
      {
        $rentebedrag = renteOverPeriode($fondswaardenClean[$a], $rapportageDatum, $min1dag, $fondswaardenClean[$a]['renteBerekenen']);
        $fondswaardenRente[$t] = $fondswaardenClean[$a];
        $fondswaardenRente[$t]['type'] = "rente";
        $fondswaardenRente[$t]['actuelePortefeuilleWaardeInValuta'] = round($rentebedrag,2);
        $fondswaardenRente[$t]['actuelePortefeuilleWaardeEuro'] = round($fondswaardenClean[$a]['actueleValuta'] * $rentebedrag,2);
        //$rentebedrag = renteOverPeriode($fondswaardenClean[$a], $rapportageBeginDatum, $min1dag, $fondswaardenClean[$a]['renteBerekenen']);
        $fondswaardenRente[$t]['beginPortefeuilleWaardeInValuta'] = 0;
        $fondswaardenRente[$t]['beginPortefeuilleWaardeEuro'] = 0;
        $t++;
      }
    }
    // merge rente array met fondsen array
    $fondswaardenClean = array_merge($fondswaardenClean, $fondswaardenRente);
    
    // voeg ook de rekeningen toe!
    //$portefeuille, $rapportageDatum
    
    $query = "SELECT DISTINCT(Rekeningafschriften.Rekening), ".
      " Rekeningen.Valuta, ".
      " Rekeningen.RenteBerekenen, ".
      " Rekeningen.Rente30_360, ".
      " Rekeningen.Tenaamstelling,
	  Rekeningen.AttributieCategorie,
	  Rekeningen.Beleggingscategorie,
    Rekeningen.Inactief,
	  ValutaPerRegio.Regio ".
      " FROM (Rekeningafschriften) ".
      " LEFT JOIN Rekeningen ON Rekeningafschriften.Rekening = Rekeningen.Rekening
   	LEFT JOIN ValutaPerRegio ON Rekeningen.Valuta = ValutaPerRegio.Valuta AND ValutaPerRegio.Vermogensbeheerder=(SELECT Vermogensbeheerder FROM Portefeuilles WHERE Portefeuille='$portefeuille') ".
      " WHERE ".
      " Rekeningen.Memoriaal <> '1' AND ".
      " Rekeningen.Portefeuille = '".$portefeuille."' AND ".
      " YEAR(Rekeningafschriften.Datum) = '".$jaar."' ";
    
    $DB1 = new DB();
    
    $q="SELECT id,afmCategorie,omschrijving,standaarddeviatie,correlatie FROM afmCategorien WHERE afmCategorie like '%liquiditeiten%'";
    $DB1->SQL($q);
    $liquiditeiten=$DB1->lookupRecord();
    $liqAFM=$liquiditeiten['afmCategorie'];
    
    $DB1->SQL($query);
    $DB1->Query();
    
    $t = count($fondswaardenClean);
    $u = 0;
    $depositoWaarden=array();
    while($data = $DB1->NextRecord())
    {
      if($data['Tenaamstelling'] )
      {
        $rekeningType = $data['Tenaamstelling'];
      }
      else
      {
        $rekeningType = "Effectenrekening ";
      }
      
      // haal actuele stand rekening op.
      $_beginJaar = substr($rapportageDatum,0,4)."-01-01";
      
      $DB2 = new DB();
      $subquery = "SELECT max(boekdatum) as datumMax  FROM Rekeningmutaties WHERE boekdatum >= '".$_beginJaar."' AND ".
        "Rekening = '".$data['Rekening']."' Group By Rekeningmutaties.Rekening";
      $DB2->SQL($subquery);
      $DB2->Query();
      $maxDatum = $DB2->nextRecord();
      if(db2jul($maxDatum['datumMax']) > db2jul($rapportageDatum))
        $maxDatum=$rapportageDatum;
      else
        $maxDatum=$maxDatum['datumMax'];
      
      $subquery = "SELECT SUM(Bedrag) as totaal FROM Rekeningmutaties WHERE boekdatum >= '".$_beginJaar."' AND boekdatum <= '".$rapportageDatum."' AND ".
        "Rekening = '".$data['Rekening']."' Group By Rekeningmutaties.Rekening";
      
      $DB2 = new DB();
      $DB2->SQL($subquery);
      $DB2->Query();
      $subdata = $DB2->nextRecord();
      
      $toonRekening=false;
      if(round($subdata['totaal'],2)	<> 0)
        $toonRekening=true;
      
      if($vermogensbeheerder=='RRP' || $vermogensbeheerder=='BOX')
      {
        if($data['Inactief'] < 1)
          $toonRekening=true;
//      else
//        $toonRekening=false;
      }
      
      if($toonRekening)
      {
        // haal actuele valuta koers op!
        $actuelevaluta = array();
        $q = "SELECT Koers,Datum FROM Valutakoersen WHERE Valuta = '".$data['Valuta']."' AND Datum <= '".$rapportageDatum."' ORDER BY Datum DESC LIMIT 1";
        $DB2 = new DB();
        $DB2->SQL($q);
        $DB2->Query();
        $actuelevaluta = $DB2->NextRecord();
        
        
        //
        
        if ($data['RenteBerekenen'] == 1)
        {
          $rente = depositoRenteOverPeriode($data['Rekening'],$rapportageDatum,$subdata['totaal'],$data['Rente30_360']);
          $depositoWaarden[$u]['type'] = 'rente';
          $depositoWaarden[$u]['fondsOmschrijving'] = $rekeningType." ".$data['Rekening'] ;
          $depositoWaarden[$u]['rekening'] = $data['Rekening'];
          $depositoWaarden[$u]['valuta'] = $data['Valuta'];
          $depositoWaarden[$u]['historischeValutakoers'] = $data['Valuta'];
          $depositoWaarden[$u]['actueleValuta'] = $actuelevaluta['Koers'];
          $depositoWaarden[$u]['actuelePortefeuilleWaardeInValuta'] = round($rente,2);
          $depositoWaarden[$u]['actuelePortefeuilleWaardeEuro'] = round($depositoWaarden[$u]['actueleValuta'] * $rente,2);
          $depositoWaarden[$u]['AttributieCategorie'] = $data['AttributieCategorie'];
          $depositoWaarden[$u]['beleggingscategorie'] = $data['Beleggingscategorie'];
          $depositoWaarden[$u]['Regio']               = $data['Regio'];
          $depositoWaarden[$u]['afmCategorie']        = $liqAFM;
          $u++;
        }
        
        $rekeningwaarden[$t]['type'] = "rekening";
        $rekeningwaarden[$t]['fondsOmschrijving'] = $rekeningType;
        $rekeningwaarden[$t]['rekening'] = $data['Rekening'];
        $rekeningwaarden[$t]['valuta'] = $data['Valuta'];
        $rekeningwaarden[$t]['historischeValutakoers'] = $data['Valuta'];
        $rekeningwaarden[$t]['actueleValuta'] = $actuelevaluta['Koers'];
        $rekeningwaarden[$t]['AttributieCategorie'] = $data['AttributieCategorie'];
        $rekeningwaarden[$t]['beleggingscategorie'] = $data['Beleggingscategorie'];
        if($data['Regio']=='')
          $data['Regio']='Geldrekeningen';
        $rekeningwaarden[$t]['Regio']               = $data['Regio'];
        $rekeningwaarden[$t]['afmCategorie']        = $liqAFM;
        $rekeningwaarden[$t]['koersDatum']    	= $maxDatum;
        
        
        $rekeningwaarden[$t]['actuelePortefeuilleWaardeInValuta'] = round($subdata['totaal'],2);
        $rekeningwaarden[$t]['actuelePortefeuilleWaardeEuro'] = round($rekeningwaarden[$t]['actueleValuta'] * $subdata['totaal'],2);
        $t++;
      }
    }
    
    
    // merge rekeningen array
    $fondswaardenClean = array_merge($fondswaardenClean, $rekeningwaarden);
    
    foreach ($depositoWaarden as $deposito)
    {
      $fondswaardenClean[] = $deposito;
    }
    
    
    
    foreach ($fondswaardenClean as $index=>$fondsData)
    {
      $fondswaardenClean[$index]['beleggingscategorieOmschrijving']=$omschrijving['Beleggingscategorien'][$fondsData['beleggingscategorie']];
      $fondswaardenClean[$index]['beleggingssectorOmschrijving']=$omschrijving['Beleggingssectoren'][$fondsData['beleggingssector']];
      $fondswaardenClean[$index]['hoofdcategorie']=$hoofdcategoriePerCategorie[$fondsData['beleggingscategorie']];
      $fondswaardenClean[$index]['hoofdsector']=$hoofdsectorPerSector[$fondsData['beleggingssector']];
      $fondswaardenClean[$index]['hoofdcategorieOmschrijving']=$hoofdcategorieOmschrijving[$fondswaardenClean[$index]['hoofdcategorie']];
      $fondswaardenClean[$index]['hoofdsectorOmschrijving']=$hoofdsectorOmschrijving[$fondswaardenClean[$index]['hoofdsector']];
      $fondswaardenClean[$index]['attributieCategorieOmschrijving']=$omschrijving['AttributieCategorien'][$fondsData['AttributieCategorie']];
      $fondswaardenClean[$index]['afmCategorieOmschrijving']=$omschrijving['afmCategorie'][$fondsData['afmCategorie']];
      $fondswaardenClean[$index]['regioOmschrijving']=$omschrijving['Regios'][$fondsData['Regio']];
      $fondswaardenClean[$index]['valutaOmschrijving']=$omschrijving['Valutas'][$fondsData['valuta']];
      $fondswaardenClean[$index]['duurzaamCategorieOmschrijving']=$omschrijving['DuurzaamCategorien'][$fondsData['duurzaamCategorie']];
      
      $fondswaardenClean[$index]['valutaVolgorde']=$afdrukvolgorde['Valutas'][$fondsData['valuta']];
      $fondswaardenClean[$index]['hoofdcategorieVolgorde']=$afdrukvolgorde['Beleggingscategorien'][$fondswaardenClean[$index]['hoofdcategorie']];
      $fondswaardenClean[$index]['hoofdsectorVolgorde']=$afdrukvolgorde['Beleggingssectoren'][$fondswaardenClean[$index]['hoofdsector']];
      $fondswaardenClean[$index]['beleggingssectorVolgorde']=$afdrukvolgorde['Beleggingssectoren'][$fondsData['beleggingssector']];
      $fondswaardenClean[$index]['beleggingscategorieVolgorde']=$afdrukvolgorde['Beleggingscategorien'][$fondsData['beleggingscategorie']];
      $fondswaardenClean[$index]['regioVolgorde']=$afdrukvolgorde['Regios'][$fondsData['Regio']];
      $fondswaardenClean[$index]['attributieCategorieVolgorde']=$afdrukvolgorde['AttributieCategorien'][$fondsData['AttributieCategorie']];
      $fondswaardenClean[$index]['duurzaamCategorieVolgorde']=$afdrukvolgorde['DuurzaamCategorien'][$fondsData['duurzaamCategorie']];
      if($afdrukvolgorde['afmCategorien'][$fondsData['afmCategorie']])
        $fondswaardenClean[$index]['afmCategorieVolgorde']=$afdrukvolgorde['afmCategorien'][$fondsData['afmCategorie']];
      
    }
    
    // return nieuwe array.
    return $fondswaardenClean;
  }
  
  function getRekeningBewaarder($rekening,$datum)
  {
    global $bebaardersPerRekening;
    
    if(isset($bebaardersPerRekening[$rekening][$datum]))
      return $bebaardersPerRekening[$rekening][$datum];
    
    $query="SELECT Depotbank FROM RekeningenHistorischeParameters WHERE Rekening='".mysql_real_escape_string($rekening)."' AND GebruikTot > '".mysql_real_escape_string($datum)."' ORDER BY GebruikTot ASC LIMIT 1";
    $db=new DB();
    $db->SQL($query);
    $data=$db->lookupRecord();
    if(isset($data['Depotbank']))
    {
      $bebaardersPerRekening[$rekening][$datum] = $data['Depotbank'];
    }
    else
    {
      $query="SELECT Depotbank FROM Rekeningen WHERE Rekening='".mysql_real_escape_string($rekening)."'";
      $db->SQL($query);
      $data=$db->lookupRecord();
      if(isset($data['Depotbank']))
        $bebaardersPerRekening[$rekening][$datum] = $data['Depotbank'];
      else
        $bebaardersPerRekening[$rekening][$datum] = '';
    }
    
    return $data['Depotbank'];
  }
  
  function fondsWaardeOpdatum($portefeuille, $fonds, $rapportageDatum, $valuta = 'EUR', $beginDatum = 0,$id='',$bewaarder='')
  {
    $a = 1;
    $fondsen[$a]['Fonds'] = $fonds;
    $jaar = date("Y", db2jul($rapportageDatum));
    if($jaar>=2019)
    {
      $meerderebeginBoekingen=true;
    }
    else
    {
      $meerderebeginBoekingen=false;
    }
    
    if($beginDatum == '')
    {
      $beginDatum = '1970-01-01';
      $beginJaar = '1970';
    }
    else
      $beginJaar = date("Y", db2jul($beginDatum));
    
    $DB = new DB();
    $q = "SELECT Datum, Rentepercentage FROM Rentepercentages WHERE Fonds = '".$fondsen[$a]['Fonds']."' AND Datum <= '".$rapportageDatum."' LIMIT 1";
    $DB->SQL($q);
    $DB->Query();
    $rente = array();
    if($DB->records() > 0)
    {
      $rente=getRenteParameters($fondsen[$a]['Fonds'],$rapportageDatum);
      
      if ($rente['Rente30_360'] == 1)
        $renteBerekenen = 2;
      else
        $renteBerekenen = 1;
    }
    else
    {
      $renteBerekenen = 0;
    }
    
    // als portefeuille een array is maak de mutatie selectie groter!
    if(is_array($portefeuille))
    {
      $extraquery = "";
      
      if($portefeuille['portefeuilleTm'])
        $extraquery .= " (Portefeuilles.Portefeuille >= '".$portefeuille['portefeuilleVan']."' AND Portefeuilles.Portefeuille <= '".$portefeuille['portefeuilleTm']."') AND";
      if($portefeuille['vermogensbeheerderTm'])
        $extraquery .= " (Portefeuilles.Vermogensbeheerder >= '".$portefeuille['vermogensbeheerderVan']."' AND Portefeuilles.Vermogensbeheerder <= '".$portefeuille['vermogensbeheerderTm']."') AND ";
      if($portefeuille['accountmanagerTm'])
        $extraquery .= " (Portefeuilles.Accountmanager >= '".$portefeuille['accountmanagerVan']."' AND Portefeuilles.Accountmanager <= '".$portefeuille['accountmanagerTm']."') AND ";
      if($portefeuille['depotbankTm'])
        $extraquery .= " (Portefeuilles.Depotbank >= '".$portefeuille['depotbankVan']."' AND Portefeuilles.Depotbank <= '".$portefeuille['depotbankTm']."') AND ";
      if($portefeuille['AFMprofielTm'])
        $extraquery .= " (Portefeuilles.AFMprofiel >= '".$portefeuille['AFMprofielVan']."' AND Portefeuilles.AFMprofiel <= '".$portefeuille['AFMprofielTm']."') AND ";
      if($portefeuille['RisicoklasseTm'])
        $extraquery .= " (Portefeuilles.Risicoklasse >= '".$portefeuille['RisicoklasseVan']."' AND Portefeuilles.Risicoklasse <= '".$portefeuille['RisicoklasseTm']."') AND ";
      if($portefeuille['SoortOvereenkomstTm'])
        $extraquery .= " (Portefeuilles.SoortOvereenkomst >= '".$portefeuille['SoortOvereenkomstVan']."' AND Portefeuilles.SoortOvereenkomst <= '".$portefeuille['SoortOvereenkomstTm']."') AND ";
      if($portefeuille['RemisierTm'])
        $extraquery .= " (Portefeuilles.Remisier >= '".$portefeuille['RemisierVan']."' AND Portefeuilles.Remisier <= '".$portefeuille['RemisierTm']."') AND ";
      if($portefeuille['clientTm'])
        $extraquery .= " (Portefeuilles.Client >= '".$portefeuille['clientVan']."' AND Portefeuilles.Client <= '".$portefeuille['clientTm']."') AND ";
      if (count($portefeuille['selectedPortefeuilles']) > 0)
      {
        $portefeuilleSelectie = implode('\',\'',$portefeuille['selectedPortefeuilles']);
        $extraquery .= " Portefeuilles.Portefeuille IN('$portefeuilleSelectie') AND ";
      }
      
    }
    else {
      $extraquery  = " Portefeuilles.Portefeuille = '".$portefeuille."' AND 	";
    }
    
    $idFilter='';
    if($id<>'')
    {
      $qMutaties = "SELECT Rekeningmutaties.* FROM Rekeningmutaties WHERE id ='$id'";
      $DB->SQL($qMutaties);
      $DB->Query();
      $verkoopRecord=$DB->nextRecord();
      $verkoopRecord['BoekdatumJul'] = db2jul($verkoopRecord['Boekdatum']);
      // $idFilter = " AND Rekeningmutaties.id <= '$id'";
    }
    else
    {
      $verkoopRecord = array();
    }
    if ($beginJaar != '1970' && $jaar != $beginJaar && $beginJaar < $jaar)
    {
      for($jaren=$beginJaar;$jaren <= $jaar; $jaren++)
      {
        if(isset($jarenString))
        {
          $jarenString .= ",'$jaren'";
          if(isset($januariUitluiten))
            $januariUitluiten .=",'$jaren-01-01 00:00:00'";
          else
            $januariUitluiten = "'$jaren-01-01 00:00:00'";
        }
        else
        {
          $jarenString = "'$jaren'";
        }
      }
      
      $boekjarenFilter = " ( YEAR(Rekeningmutaties.Boekdatum) IN ($jarenString) ) ";
      $januariFilter = " Rekeningmutaties.Boekdatum NOT IN ($januariUitluiten) ";
      
      $qMutaties = "SELECT Rekeningmutaties.*, ".
        " Fondsen.Renteperiode, ".
        " Fondsen.EersteRentedatum, ".
        " Fondsen.Rentedatum, ".
        " Fondsen.Fondseenheid, ".
        " Fondsen.Valuta, ".
        " Fondsen.Omschrijving AS FondsOmschrijving ,forward,forwardReferentieKoers,Huisfonds,Fondsen.Portefeuille as huisfondsPortefeuille".
        " FROM Rekeningmutaties, ".
        " Rekeningen, Fondsen, Portefeuilles WHERE ".$extraquery.
        " Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
        " Rekeningmutaties.Grootboekrekening = 'FONDS' AND ".
        " Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
        " Fondsen.Fonds = Rekeningmutaties.Fonds AND ".
        " Rekeningmutaties.Fonds = '".$fondsen[$a]['Fonds']."' AND ".
        $boekjarenFilter." AND ".$januariFilter." AND ".
        " Rekeningmutaties.Verwerkt = '1' AND ".
        " Rekeningmutaties.Boekdatum <= '".$rapportageDatum."' $idFilter ".
        " ORDER BY Rekeningmutaties.Boekdatum ASC, Rekeningmutaties.id ";
    }
    else
    {
      $qMutaties = "SELECT Rekeningmutaties.*, ".
        " Fondsen.Renteperiode, ".
        " Fondsen.EersteRentedatum, ".
        " Fondsen.Rentedatum, ".
        " Fondsen.Fondseenheid, ".
        " Fondsen.Valuta, ".
        " Fondsen.Omschrijving AS FondsOmschrijving ,forward,forwardReferentieKoers,Huisfonds,Fondsen.Portefeuille as huisfondsPortefeuille ".
        " FROM Rekeningmutaties, ".
        " Rekeningen, Fondsen, Portefeuilles WHERE ".$extraquery.
        " Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
        " Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
        " Fondsen.Fonds = Rekeningmutaties.Fonds AND ".
        " Rekeningmutaties.Fonds = '".$fondsen[$a]['Fonds']."' AND ".
        " Rekeningmutaties.Grootboekrekening = 'FONDS' AND ".
        " YEAR(Rekeningmutaties.Boekdatum) = '".$jaar."' AND ".
        " Rekeningmutaties.Verwerkt = '1' AND ".
        " Rekeningmutaties.Boekdatum <= '".$rapportageDatum."' $idFilter ".
        " ORDER BY Rekeningmutaties.Boekdatum ASC, Rekeningmutaties.id ";
    }
    
    if($bewaarder<>'')
      $qMutaties = "SELECT if(Rekeningmutaties.Bewaarder <> '',Rekeningmutaties.Bewaarder,IF(Rekeningen.Depotbank <>'', Rekeningen.Depotbank,Portefeuilles.Depotbank)) as BewaarderSort,
      Rekeningmutaties.*, ".
        " Fondsen.Renteperiode, ".
        " Fondsen.EersteRentedatum, ".
        " Fondsen.Rentedatum, ".
        " Fondsen.Fondseenheid, ".
        " Fondsen.Valuta, ".
        " Fondsen.Omschrijving AS FondsOmschrijving ,forward,forwardReferentieKoers,Huisfonds,Fondsen.Portefeuille as huisfondsPortefeuille, Rekeningmutaties.Bewaarder as rekMutBewaarder, Rekeningen.Depotbank as rekBewaarder, Rekeningen.Rekening".
        " FROM Rekeningmutaties, ".
        " Rekeningen, Fondsen, Portefeuilles WHERE ".$extraquery.
        " Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
        " Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
        " Fondsen.Fonds = Rekeningmutaties.Fonds AND ".
        " Rekeningmutaties.Fonds = '".$fondsen[$a]['Fonds']."' AND ".
        " (Rekeningmutaties.Grootboekrekening = 'FONDS' OR (Rekeningmutaties.Grootboekrekening = 'KRUIS'  AND Rekeningmutaties.Fonds <> '')) AND ".
        " YEAR(Rekeningmutaties.Boekdatum) = '".$jaar."' AND ".
        " Rekeningmutaties.Verwerkt = '1' AND ".
        " Rekeningmutaties.Boekdatum <= '".$rapportageDatum."' $idFilter HAVING BewaarderSort='".$bewaarder."'  ".
        " ORDER BY Rekeningmutaties.Boekdatum ASC, Rekeningmutaties.id";
    
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
    
    $counter=0;
    $vorigeHistorischeWaarde=0;
    $vorigeHistorischeRapportageValutakoers=0;
    while($mutatie = $DB->NextRecord())
    {
      
      if($mutatie['BewaarderSort']!='')
        $mutatie['Bewaarder']=$mutatie['BewaarderSort'];
      
      IF($mutatie['rekMutBewaarder']=='')
      {
        $bewaarder=$this->getRekeningBewaarder($mutatie['Rekening'],$rapportageDatum);
        //listarray($mutatie); listarray($bewaarder);
        if($bewaarder<>'')
          $mutatie['Bewaarder']=$bewaarder;
      }
      
      $julBoekdatum = db2jul($mutatie['Boekdatum']);
      if($julBoekdatum>=$verkoopRecord['BoekdatumJul'] && $id>0 && $mutatie['id']>$id)
      {
        //echo substr($verkoopRecord['Boekdatum'],0,10).">=" .$mutatie['Boekdatum']." <br>\n";
        break;
      }
      
      if($mutatie['forward']==1)
      {
        //strtoupper($mutatie[Transactietype])
        $koersStartOpgehaald=true;
        $fondswaarden[$fondsen[$a]['Fonds']]['valuta'] = $mutatie['Valuta'];
        //$fondswaarden[$fondsen[$a]['Fonds']]['forward'] = 1;
        $fondswaarden[$fondsen[$a]['Fonds']]['fondsOmschrijving'] = $mutatie['FondsOmschrijving'];
        $fondswaarden[$fondsen[$a]['Fonds']]['actueleValuta'] = getValutaKoers($mutatie['Valuta'],$rapportageDatum);
        $fondswaarden[$fondsen[$a]['Fonds']]['actueleFonds'] =  $fondswaarden[$fondsen[$a]['Fonds']]['actueleValuta'] - $mutatie['forwardReferentieKoers'];
        $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] += $mutatie['Aantal'];
        $fondswaarden[$fondsen[$a]['Fonds']]['fondsEenheid']= $mutatie['Fondseenheid'];// 1/getValutaKoers($mutatie['Valuta'],$rapportageDatum);
        if($julBeginDatum >= $julBoekdatum)
        {
          
          $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = getValutaKoers($mutatie['Valuta'],$beginDatum);
          $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] - $mutatie['forwardReferentieKoers'];
        }
      }
      else
      {
        
        if(($julBoekdatum > $julBeginDatum)  && !isset($koersStartOpgehaald))//&& ($julBoekdatum > mktime(0,0,0,1,1,$jaar))
        {
          $koersStartOpgehaald = true;
          
          // haal startdatum valuta koers op!
          $startvaluta = array();
          $q = "SELECT Koers,Datum FROM Valutakoersen WHERE Valuta = '".$mutatie['Valuta']."' AND Datum <= '".$beginDatum."' ORDER BY Datum DESC LIMIT 1";
          $DB2 = new DB();
          $DB2->SQL($q);
          $DB2->Query();
          $startvaluta = $DB2->NextRecord();
          $vorigeBeginwaardeValutaLopendeJaar = $startvaluta['Koers'];
          $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] =$vorigeBeginwaardeValutaLopendeJaar;
          
          // haal startdatum fonds koers op!
          $startfonds = array();
          $q = "SELECT Koers,Datum FROM Fondskoersen WHERE Fonds = '".$mutatie['Fonds']."' AND Datum <= '".$beginDatum."' ORDER BY Datum DESC LIMIT 1";
          $DB2 = new DB();
          $DB2->SQL($q);
          $DB2->Query();
          $startfonds = $DB2->NextRecord();
          if($startfonds['Koers']=='' && $mutatie['Huisfonds']==1 &&  $mutatie['huisfondsPortefeuille']<>'')
            $startfonds=bepaalHuisfondsKoers($mutatie['Fonds'],$mutatie['huisfondsPortefeuille'],$beginDatum);
          $vorigeBeginwaardeLopendeJaar = $startfonds['Koers'];
          
          $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] =$vorigeBeginwaardeLopendeJaar;
        }
        else
        {
          // echo $mutatie['Aantal']." ".$mutatie['Fonds']." ".$fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar']." $rapportageDatum <br> ";
        }
        
        
        // haal actuele valuta koers op!
        if(empty($fondswaarden[$fondsen[$a]['Fonds']]['actueleValuta']))
        {
          $actuelevaluta = array();
          $q = "SELECT Koers,Datum FROM Valutakoersen WHERE Valuta = '".$mutatie['Valuta']."' AND Datum <= '".$rapportageDatum."' ORDER BY Datum DESC LIMIT 1";
          $DB2 = new DB();
          $DB2->SQL($q);
          $DB2->Query();
          $actuelevaluta = $DB2->NextRecord();
        }
        
        // haal actuele fonds koers op!
        if(empty($fondswaarden[$fondsen[$a]['Fonds']]['actueleFonds']))
        {
          $actuelefonds = array();
          $q = "SELECT Koers,Datum FROM Fondskoersen WHERE Fonds = '".$mutatie['Fonds']."' AND Datum <= '".$rapportageDatum."' ORDER BY Datum DESC LIMIT 1";
          $DB2 = new DB();
          $DB2->SQL($q);
          $DB2->Query();
          $actuelefonds = $DB2->NextRecord();
          
          if($actuelefonds['Koers']=='' && $mutatie['Huisfonds']==1 &&  $mutatie['huisfondsPortefeuille']<>'')
          {
            $actuelefonds = bepaalHuisfondsKoers($mutatie['Fonds'], $mutatie['huisfondsPortefeuille'], $rapportageDatum);
          }
        }
        
        //$mutatie['Aantal'] 	= $mutatie['Aantal'];
        $fondswaarden[$fondsen[$a]['Fonds']]['fondsEenheid'] 			= $mutatie['Fondseenheid'];
        $fondswaarden[$fondsen[$a]['Fonds']]['fondsOmschrijving'] = $mutatie['FondsOmschrijving'];
        $fondswaarden[$fondsen[$a]['Fonds']]['valuta'] 						= $mutatie['Valuta'];
        $fondswaarden[$fondsen[$a]['Fonds']]['eersteRentedatum'] 	= $mutatie['EersteRentedatum'];
        
        $fondswaarden[$fondsen[$a]['Fonds']]['rentedatum'] 				= $mutatie['Rentedatum'];
        $fondswaarden[$fondsen[$a]['Fonds']]['renteperiode'] 			= $mutatie['Renteperiode'];
        
        $fondswaarden[$fondsen[$a]['Fonds']]['actueleValuta'] 		= $actuelevaluta['Koers'];
        $fondswaarden[$fondsen[$a]['Fonds']]['actueleFonds'] 			= $actuelefonds['Koers'];
        $fondswaarden[$fondsen[$a]['Fonds']]['koersDatum']  	= $actuelefonds['Datum'];
        
        if($mutatie['Bewaarder'] != '' && empty($fondswaarden[$fondsen[$a]['Fonds']]['Bewaarder']))
          $fondswaarden[$fondsen[$a]['Fonds']]['Bewaarder'] 			= $mutatie['Bewaarder'];
//echo $mutatie['Fonds']."  ".$mutatie['Boekdatum'] . " ".getValutaKoers($valuta,$mutatie['Boekdatum'])." ->".$mutatie['Bewaarder']."<br>";
        
        switch (strtoupper($mutatie['Transactietype']))
        {
          case "A" :
            // Aankoop
            if($vorigeTotaalAantal < 0)
            {
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = $mutatie['Valutakoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = $mutatie['Fondskoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeWaarde'] = $mutatie['Fondskoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeValutakoers'] = $mutatie['Valutakoers'];
              if ($valuta != 'EUR')
                $fondswaarden[$fondsen[$a]['Fonds']]['historischeRapportageValutakoers'] = getValutaKoers($valuta,$mutatie['Boekdatum']);
              $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = $vorigeTotaalAantal + $mutatie['Aantal'];
              $fondswaarden[$fondsen[$a]['Fonds']]['voorgaandejarenActief'] = 0;
            }
            else
            {
              //echo $mutatie['Fonds']." ".$mutatie['Aantal']." ".$vorigeTotaalAantal;
              $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = ($vorigeTotaalAantal + $mutatie['Aantal']);
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeWaarde'] = (
                (
                  ($vorigeTotaalAantal * $vorigeHistorischeWaarde) +
                  ($mutatie['Aantal'] * $mutatie['Fondskoers'])
                )
                /
                (
                  $vorigeTotaalAantal + $mutatie['Aantal']
                )
              );
              
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeValutakoers'] = (
                (
                  ($vorigeTotaalAantal * $vorigeHistorischeWaarde * $vorigeHistorischeValutakoers)
                  +
                  ($mutatie['Aantal'] * $mutatie['Fondskoers'] * $mutatie['Valutakoers'])
                )
                /
                (
                  ($vorigeTotaalAantal * $vorigeHistorischeWaarde) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])
                )
              );
              if ($valuta != 'EUR')
                $fondswaarden[$fondsen[$a]['Fonds']]['historischeRapportageValutakoers'] = (
                  (
                    ($vorigeTotaalAantal * $vorigeHistorischeWaarde * $vorigeHistorischeRapportageValutakoers)
                    +
                    ($mutatie['Aantal'] * $mutatie['Fondskoers'] * getValutaKoers($valuta,$mutatie['Boekdatum']))
                  )
                  /
                  (
                    ($vorigeTotaalAantal * $vorigeHistorischeWaarde) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])
                  )
                );
              
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = (
                (
                  ($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])
                )
                /
                (
                  $vorigeTotaalAantal + $mutatie['Aantal']
                )
              );
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = (
                (
                  ($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar * $vorigeBeginwaardeValutaLopendeJaar)
                  +
                  ($mutatie['Aantal'] * $mutatie['Fondskoers'] * $mutatie['Valutakoers'])
                )
                /
                (
                  ($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])
                )
              );
              
            }
            break;
          case "A/O" :
            // Aankoop / openen
            if($vorigeTotaalAantal == 0)
            {
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = $mutatie['Valutakoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = $mutatie['Fondskoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeWaarde'] = $mutatie['Fondskoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeValutakoers'] = $mutatie['Valutakoers'];
              if ($valuta != 'EUR')
                $fondswaarden[$fondsen[$a]['Fonds']]['historischeRapportageValutakoers'] = getValutaKoers($valuta,$mutatie['Boekdatum']);
              $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = $mutatie['Aantal'];
              $fondswaarden[$fondsen[$a]['Fonds']]['voorgaandejarenActief'] = 1;
              
            }
            else
            {
              $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = ($vorigeTotaalAantal + $mutatie['Aantal']);
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeWaarde'] = (
                (
                  ($vorigeTotaalAantal * $vorigeHistorischeWaarde) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])
                )
                /
                (
                  $vorigeTotaalAantal + $mutatie['Aantal']
                )
              );
              
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeValutakoers'] = (
                (
                  ($vorigeTotaalAantal * $vorigeHistorischeWaarde * $vorigeHistorischeValutakoers)
                  +
                  ($mutatie['Aantal'] * $mutatie['Fondskoers'] * $mutatie['Valutakoers'])
                )
                /
                (
                  ($vorigeTotaalAantal * $vorigeHistorischeWaarde) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])
                )
              );
              if ($valuta != 'EUR')
                $fondswaarden[$fondsen[$a]['Fonds']]['historischeRapportageValutakoers'] = (
                  (
                    ($vorigeTotaalAantal * $vorigeHistorischeWaarde * $vorigeHistorischeRapportageValutakoers)
                    +
                    ($mutatie['Aantal'] * $mutatie['Fondskoers'] * getValutaKoers($valuta,$mutatie['Boekdatum']))
                  )
                  /
                  (
                    ($vorigeTotaalAantal * $vorigeHistorischeWaarde) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])
                  )
                );
              
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = (
                (
                  ($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])
                )
                /
                (
                  $vorigeTotaalAantal + $mutatie['Aantal']
                )
              );
              //
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = (
                (
                  ($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar * $vorigeBeginwaardeValutaLopendeJaar)
                  +
                  ($mutatie['Aantal'] * $mutatie['Fondskoers'] * $mutatie['Valutakoers'])
                )
                /
                (
                  ($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])
                )
              );
            }
            break;
          case "A/S" :
            // Aankoop / sluiten
            $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = ($vorigeTotaalAantal + $mutatie['Aantal']);
            break;
          case "B" :
            // Beginstorting
            //$meerderebeginBoekingen=false;
            if($counter>0 && $meerderebeginBoekingen==true)
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
              
              // haal fonskoers op voor beginwaarde!
//				$q = "SELECT Koers,Datum FROM Fondskoersen WHERE Fonds = '".$fondsen[$a]['Fonds']."' AND YEAR(Datum) = '".$jaar."' ORDER BY Datum ASC LIMIT 1";
              $q = "SELECT Koers,Datum FROM Fondskoersen WHERE Fonds = '" . $fondsen[$a]['Fonds'] . "' AND Datum <= '" . $mutatie['Boekdatum'] . "' ORDER BY Datum DESC LIMIT 1";
              $DB2 = new DB();
              $DB2->SQL($q); //echo "<br>$q <br>\n";
              $DB2->Query();
              $beginkoers = $DB2->NextRecord();
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = $beginkoers['Koers'];
              
              // haal eerste valutakoers op.
//				$q = "SELECT Koers,Datum FROM Valutakoersen WHERE Valuta = '".$fondswaarden[$fondsen[$a]['Fonds']]['valuta']."' AND YEAR(Datum) = '".$jaar."' ORDER BY Datum ASC LIMIT 1";
              $q = "SELECT Koers,Datum FROM Valutakoersen WHERE Valuta = '" . $fondswaarden[$fondsen[$a]['Fonds']]['valuta'] . "' AND Datum <= '" . $mutatie['Boekdatum'] . "' ORDER BY Datum DESC LIMIT 1";
              $DB2 = new DB();
              $DB2->SQL($q);
              $DB2->Query();
              $beginvaluta = $DB2->NextRecord();
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = $beginvaluta['Koers'];
              
            }
            
            if($fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] =='' && $mutatie['Huisfonds']==1 &&  $mutatie['huisfondsPortefeuille']<>'')
            {
              $beginKoersHuisfonds=bepaalHuisfondsKoers($mutatie['Fonds'], $mutatie['huisfondsPortefeuille'], $mutatie['Boekdatum']);
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar']=$beginKoersHuisfonds['Koers'];
            }
            
            break;
          case "D" :
          case "S" :
            // Deponering
            if($vorigeTotaalAantal == 0)
            {
              // haal valutakoers op voor beginwaarde!
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = $mutatie['Valutakoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = $mutatie['Fondskoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeWaarde'] =  $mutatie['Fondskoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeValutakoers'] =  $mutatie['Valutakoers'];
              if ($valuta != 'EUR')
                $fondswaarden[$fondsen[$a]['Fonds']]['historischeRapportageValutakoers'] = getValutaKoers($valuta,$mutatie['Boekdatum']);
              $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = $mutatie['Aantal'];
              $fondswaarden[$fondsen[$a]['Fonds']]['voorgaandejarenActief'] = 1;
            }
            else
            {
              $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] 			= ($vorigeTotaalAantal + $mutatie['Aantal']);
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeWaarde'] = (
                (
                  ($vorigeTotaalAantal * $vorigeHistorischeWaarde) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])
                )
                /
                (
                  $vorigeTotaalAantal + $mutatie['Aantal']
                )
              );
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeValutakoers'] = (
                (
                  ($vorigeTotaalAantal * $vorigeHistorischeWaarde * $vorigeHistorischeValutakoers)
                  +
                  ($mutatie['Aantal'] * $mutatie['Fondskoers'] * $mutatie['Valutakoers'])
                )
                /
                (
                  ($vorigeTotaalAantal * $vorigeHistorischeWaarde) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])
                )
              );
              if ($valuta != 'EUR')
                $fondswaarden[$fondsen[$a]['Fonds']]['historischeRapportageValutakoers'] = (
                  (
                    ($vorigeTotaalAantal * $vorigeHistorischeWaarde * $vorigeHistorischeRapportageValutakoers)
                    +
                    ($mutatie['Aantal'] * $mutatie['Fondskoers'] * getValutaKoers($valuta,$mutatie['Boekdatum']))
                  )
                  /
                  (
                    ($vorigeTotaalAantal * $vorigeHistorischeWaarde) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])
                  )
                );
              
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = (
                (
                  ($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])
                )
                /
                (
                  $vorigeTotaalAantal + $mutatie['Aantal']
                )
              );
              
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = (
                (
                  ($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar * $vorigeBeginwaardeValutaLopendeJaar)
                  +
                  ($mutatie['Aantal'] * $mutatie['Fondskoers'] * $mutatie['Valutakoers'])
                )
                /
                (
                  ($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])
                )
              );
              
              //		$fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = $vorigeBeginwaardeValutaLopendeJaar;
              
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
            if($vorigeTotaalAantal == 0)
            {
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = $mutatie['Valutakoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = $mutatie['Fondskoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeWaarde'] = $mutatie['Fondskoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeValutakoers'] = $mutatie['Valutakoers'];
              $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = $mutatie['Aantal'] ;
              $fondswaarden[$fondsen[$a]['Fonds']]['voorgaandejarenActief'] = 1;
            }
            else
            {
              $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = ($vorigeTotaalAantal + $mutatie['Aantal']);
              
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeWaarde'] = (
                (
                  ($vorigeTotaalAantal * $vorigeHistorischeWaarde) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])
                )
                /
                (
                  $vorigeTotaalAantal + $mutatie['Aantal']
                )
              );
              
              $fondswaarden[$fondsen[$a]['Fonds']]['historischeValutakoers'] = (
                (
                  ($vorigeTotaalAantal * $vorigeHistorischeWaarde * $vorigeHistorischeValutakoers)
                  +
                  ($mutatie['Aantal'] * $mutatie['Fondskoers'] * $mutatie['Valutakoers'])
                )
                /
                (
                  ($vorigeTotaalAantal * $vorigeHistorischeWaarde) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])
                )
              );
              
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'] = (
                (
                  ($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])
                )
                /
                (
                  $vorigeTotaalAantal + $mutatie['Aantal']
                )
              );
              //
              $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = (
                (
                  ($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar * $vorigeBeginwaardeValutaLopendeJaar)
                  +
                  ($mutatie['Aantal'] * $mutatie['Fondskoers'] * $mutatie['Valutakoers'])
                )
                /
                (
                  ($vorigeTotaalAantal * $vorigeBeginwaardeLopendeJaar) + ($mutatie['Aantal'] * $mutatie['Fondskoers'])
                )
              );
              
            }
            
            break;
          case "V/S" :
            // Verkopen / sluiten
            $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = ($vorigeTotaalAantal + $mutatie['Aantal']);
            break;
          default :
            $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal']+=$mutatie['Aantal'];
            $_error = "Fout ongeldig tranactietype!!";
            break;
          
          
        }
        
        $vorigeTotaalAantal 									= $fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'];
        $vorigeHistorischeWaarde 							= $fondswaarden[$fondsen[$a]['Fonds']]['historischeWaarde'];
        $vorigeHistorischeValutakoers					= $fondswaarden[$fondsen[$a]['Fonds']]['historischeValutakoers'];
        $vorigeBeginwaardeLopendeJaar					= $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeLopendeJaar'];
        $vorigeBeginwaardeValutaLopendeJaar		= $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'];
        $vorigeHistorischeRapportageValutakoers = $fondswaarden[$fondsen[$a]['Fonds']]['historischeRapportageValutakoers'];
      }
      //echo $fondsen[$a]['Fonds']." " .	$fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar']. "<br>";flush();
      if(!isset($koersStartOpgehaald) && $beginDatum != "$jaar-01-01" )
      {
        // haal startdatum valuta koers op!
        $startvaluta = array();
        $q = "SELECT Koers,Datum FROM Valutakoersen WHERE Valuta = '".$fondswaarden[$fondsen[$a]['Fonds']]['valuta']."' AND Datum <= '".$beginDatum."' ORDER BY Datum DESC LIMIT 1";
        $DB2 = new DB();
        $DB2->SQL($q);
        $DB2->Query();
        $startvaluta = $DB2->NextRecord();
        $fondswaarden[$fondsen[$a]['Fonds']]['beginwaardeValutaLopendeJaar'] = $startvaluta['Koers'];
        
        // haal startdatum fonds koers op!
        $startfonds = array();
        $q = "SELECT Koers,Datum FROM Fondskoersen WHERE Fonds = '".$fondsen[$a]['Fonds']."' AND Datum <= '".$beginDatum."' ORDER BY Datum DESC LIMIT 1";
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
  

  
}

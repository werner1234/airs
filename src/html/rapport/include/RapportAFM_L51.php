<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/04/23 05:56:31 $
 		File Versie					: $Revision: 1.5 $

 		$Log: RapportAFM_L51.php,v $
 		Revision 1.5  2020/04/23 05:56:31  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2020/04/22 15:40:47  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2020/04/12 11:49:05  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2020/04/11 16:33:41  rvv
 		*** empty log message ***
 		
 
 
*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportAFM_L51
{
  function RapportAFM_L51($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "AFM";
    $this->pdf->rapport_deel = 'overzicht';
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->RapStartJaar = date("Y", $this->pdf->rapport_datumvanaf);
    if(is_array($this->pdf->portefeuilles))
      $this->portefeuilles=$this->pdf->portefeuilles;
    else
      $this->portefeuilles=array($portefeuille);
    
    if($this->RapStartJaar <> date("Y", $this->pdf->rapport_datum))
    {
      echo "Begin en einddatum moeten in hetzelfde jaar liggen";
      exit;
    }
    
    $this->pdf->rapport_titel = "Overzicht beleggingen";
    
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
    
    $this->perioden['start'] = $this->rapportageDatumVanaf;
    $this->perioden['eind'] = $this->rapportageDatum;
    
    $this->pdf->underlinePercentage = .7;
    $this->checkValues=false;
    
    $this->pdf->excelData[]=array("Sector",'Categorie','Fonds','Aantal','Koers',"Waarde in ".$this->pdf->rapportageValuta." ".date("d-m-y",$this->pdf->rapport_datumvanaf),
      "Stortingen/onttrekkingen","Resultaat verslagperiode","Waarde in ".$this->pdf->rapportageValuta." ".date("d-m-y",$this->pdf->rapport_datum),
      "Rendement verslagperiode");
    
  }
  
  function tweedeStart()
  {
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    if((db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf)) || (db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01")))
    {
      $this->tweedePerformanceStart = substr($this->pdf->PortefeuilleStartdatum,0,10);
    }
    else
      $this->tweedePerformanceStart = "$RapStartJaar-01-01";
  }
  
  function formatGetal($waarde, $dec, $percent = false,$limit = false,$nulTonen=false)
  {
    if(round($waarde,2) == 0.00)
    {
      if($nulTonen==false)
        return '';
    }
    
    if($percent == true)
    {
      if($limit)
      { //echo "$waarde <br>";
        if($waarde >= $limit || $waarde <= $limit * -1)
          return "p.m.";
      }
      return number_format($waarde,$dec,",",".").'%';
    }
    
    else
      return number_format($waarde,$dec,",",".");
    
  }
  
  function formatGetalKoers($waarde, $dec, $percent = false, $limit = false , $start = false)
  {
    if ($start == false)
    {
      $waarde = $waarde / $this->pdf->ValutaKoersEind;
    }
    else
    {
      $waarde = $waarde / $this->pdf->ValutaKoersStart;
    }
    return $this->formatGetal($waarde, $dec, $percent = false,$limit = false);
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
            //  echo $this->portefeuille." $waarde <br>";exit;
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
  
  function getDirecteOpbrengst($portefeuille,$vanaf,$tot,$filter)
  {
    $db = new DB();
    if($filter['fonds']<>'')
      $fondsFilter="AND Rekeningmutaties.Fonds='".mysql_real_escape_string($filter['fonds'])."'";
    else
      $fondsFilter='';
    

    if($fondsFilter=='')
      return 0;
    
    $query = "SELECT sum(Rekeningmutaties.Valutakoers * (Rekeningmutaties.Credit-Rekeningmutaties.Debet)) as opbrengst ".
      "FROM Rekeningmutaties, Rekeningen ".
      "WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening ".
      "AND Rekeningen.Portefeuille = '".$portefeuille."' ".
      "AND Rekeningmutaties.Verwerkt = '1' ".
      "AND Rekeningmutaties.Boekdatum > '".$vanaf."' ".
      "AND Rekeningmutaties.Boekdatum <= '".$tot."' ".
      "AND Rekeningmutaties.Grootboekrekening IN('DIV','DIVB','RENOB','HUUR') $fondsFilter";
    $db->SQL($query);
    $opbrengst=$db->lookupRecord();


    return $opbrengst['opbrengst'];
  }
  
  function writeRapport()
  {
    global $__appvar;

    $gebruikteCrmVelden = array(
      'Portefeuillesoort',
      'PortefeuilleNaam');
    
    $db = new DB();
    $query = "DESC CRM_naw";
    $db->SQL($query);
    $db->Query();
    $crmVelden = array();
    while ($data = $db->nextRecord())
    {
      $crmVelden[] = strtolower($data['Field']);
    }
    
    $nawSelect = '';
    $nietgevonden = array();
    foreach ($gebruikteCrmVelden as $veld)
    {
      if (in_array(strtolower($veld), $crmVelden))
      {
        $nawSelect .= ",CRM_naw.$veld ";
      }
      else
      {
        $nietgevonden[] = $veld;
      }
    }
    
    $rendementProcent = performanceMeting($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
    $portefeuilleRendement=array();
    foreach ($this->portefeuilles as $portefeuille)
    {
      $portefeuilleRendement[$portefeuille] = performanceMeting($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
    }
    
    $verdelingClient = array();
    $waarden=array();
    $subtotalen=array();
    $totalen=array();
    $beleggingscategorieen=array();
    $beleggingscategorieOmschrijving=array();
    $fondsRegelsPerPortefeuille=array();
    $mutatieLijstPerPortefeuille=array();
    $perioden=array($this->rapportageDatumVanaf,$this->rapportageDatum);
    
    foreach ($this->portefeuilles as $portefeuille)
    {
      $mutatieLijstPerPortefeuille[$portefeuille]=$this->genereerMutatieLijst($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
      foreach($perioden as $datum)
      {
        $fondsRegels=berekenPortefeuilleWaarde($portefeuille, $datum, (substr($datum, 5, 5) == '01-01')?true:false, $this->pdf->rapportageValuta, $this->rapportageDatumVanaf);
        $fondsRegelsPerPortefeuille[$portefeuille][$datum] = $fondsRegels;
      }
    }
    
    $portefeuilleDetails=array();
    foreach ($this->portefeuilles as $portefeuille)
    {
      $query = "SELECT Portefeuilles.portefeuille, Portefeuilles.Clientvermogensbeheerder $nawSelect FROM Portefeuilles LEFT JOIN CRM_naw ON Portefeuilles.portefeuille=CRM_naw.portefeuille WHERE Portefeuilles.portefeuille='$portefeuille' limit 1";
      $db->SQL($query);
      $pdata = $db->lookupRecord();
      $portefeuilleDetails[$portefeuille]=$pdata;
      
      if ($pdata['Clientvermogensbeheerder'] <> '')
      {
        $clientNaam = $pdata['Clientvermogensbeheerder'];
      }
      else
      {
        $clientNaam = $portefeuille;
      }
      if($pdata['PortefeuilleNaam']<>'')
        $portefeuilleNaam=$pdata['PortefeuilleNaam'];
      else
        $portefeuilleNaam='PortefeuilleNaam';
      
      //
      // listarray($mutatieLijst);
      $mutatieLijst=$mutatieLijstPerPortefeuille[$portefeuille];
      foreach($perioden as $datum)
      {
        $fondsRegels = $fondsRegelsPerPortefeuille[$portefeuille][$datum];// berekenPortefeuilleWaarde($portefeuille, $datum, (substr($datum, 5, 5) == '01-01')?true:false, $this->pdf->rapportageValuta, $this->rapportageDatumVanaf);
        if($pdata['Portefeuillesoort']=='Effecten')
        {
          
          $query = "SELECT sum(Rekeningmutaties.Valutakoers * (Rekeningmutaties.Credit-Rekeningmutaties.Debet)) as storting ".
            "FROM Rekeningmutaties, Rekeningen,  Grootboekrekeningen ".
            "WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening ".
            "AND Rekeningen.Portefeuille = '".$portefeuille."' ".
            "AND Rekeningmutaties.Verwerkt = '1' ".
            "AND Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' ".
            "AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
            "AND Grootboekrekeningen.Afdrukvolgorde IS NOT NULL ".
            "AND Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening ".
            "AND (Grootboekrekeningen.Storting = '1' OR Grootboekrekeningen.Onttrekking = '1') ";
          $db->SQL($query);
          $totaleStort=$db->lookupRecord(); //echo $query; listarray($totaleStort);exit;
          
          $liqCategorie='Liquiditeiten';
          foreach($fondsRegels as $regel)
          {
            if($regel['type']=='rekening')
            {
              $liqCategorie='Liquiditeiten';//geld
              if($regel['beleggingscategorie']<>'')
                $liqCategorie=$regel['beleggingscategorie'];
              
              $waarden[$liqCategorie][$portefeuille][$regel['rekening']][$datum]['actuelePortefeuilleWaardeEuro']+=$regel['actuelePortefeuilleWaardeEuro'];
              $query="SELECT  Rekeningen.Tenaamstelling FROM Rekeningen WHERE Rekeningen.Rekening='".mysql_real_escape_string($regel['rekening'])."'";
              $db->SQL($query);
              $tenaamstelling=$db->lookupRecord();
              if($tenaamstelling['Tenaamstelling']=='')
                $omschrijving=$regel['fondsOmschrijving'].' '.$regel['rekening'];
              else
                $omschrijving = $regel['fondsOmschrijving'];
              
              if(!isset($waarden[$liqCategorie][$portefeuille][$regel['rekening']]['fondsOmschrijving']))
                $waarden[$liqCategorie][$portefeuille][$regel['rekening']]['fondsOmschrijving']=$omschrijving;
              $waarden[$liqCategorie][$portefeuille][$regel['rekening']][$datum][$clientNaam.'_actuelePortefeuilleWaardeEuro']+=$regel['actuelePortefeuilleWaardeEuro'];
              
              if($datum==$this->rapportageDatum)
              {
                $waarden[$liqCategorie][$portefeuille][$regel['rekening']]['valuta']=$regel['valuta'];
                
                
                if($regel['valuta']<>'EUR')
                {
                  $st = getRekeningStortingenKruis($regel['rekening'], $this->rapportageDatumVanaf, $this->rapportageDatum, 'EUR', true);
                  $on = getRekeningOnttrekkingenKruis($regel['rekening'], $this->rapportageDatumVanaf, $this->rapportageDatum, 'EUR', true);
                  if ($_POST['debug'])
                  {
                    echo "<hr>" . $regel['rekening'] . " " . $this->rapportageDatumVanaf . " " . $this->rapportageDatum . "<br>\n";
                    listarray($st);
                    listarray($on);
                  }
                  $stortingen = $st['storting'] + $st['kruispost'];
                  $onttrekkingen = $on['onttrekking'] + $on['kruispost'];
                  $stortingenOnttrekkingen = ($stortingen - $onttrekkingen);
                  $waarden[$liqCategorie][$portefeuille][$regel['rekening']]['stortingenOnttrekkingen'] += $stortingenOnttrekkingen;
                }
                else
                {
                  $stortingen = getRekeningStortingen($regel['rekening'], $this->rapportageDatumVanaf, $this->rapportageDatum);
                  $onttrekkingen = getRekeningOnttrekkingen($regel['rekening'], $this->rapportageDatumVanaf, $this->rapportageDatum);
                  $stortingenOnttrekkingen = ($stortingen - $onttrekkingen);
                }
                //echo $regel['rekening']." $stortingenOnttrekkingen = ($stortingen - $onttrekkingen); <br>\n";ob_flush();
                
                $huidigeWaarde=  $regel['actuelePortefeuilleWaardeEuro'];
                $vorigeWaarde=$waarden[$liqCategorie][$portefeuille][$regel['rekening']][$this->rapportageDatumVanaf]['actuelePortefeuilleWaardeEuro'];
                
                $rekeningMutatieWaarde=$huidigeWaarde-$vorigeWaarde;
                //       echo  $waarden['effecten'][$portefeuille][$portefeuilleNaam]['stortingenOnttrekkingen'] ." = ". $regel['rekening']." ($stortingenOnttrekkingen-$rekeningMutatieWaarde)<br>\n";ob_flush();
                $waarden['effecten'][$portefeuille][$portefeuilleNaam]['stortingenOnttrekkingen'] +=($stortingenOnttrekkingen-$rekeningMutatieWaarde);
                $waarden['effecten'][$portefeuille][$portefeuilleNaam]['liqMutatieTotaal']+=($rekeningMutatieWaarde);
              }
              
              $subtotalen[$liqCategorie][$datum]['totaal']+=$regel['actuelePortefeuilleWaardeEuro'];
              $subtotalen[$liqCategorie][$datum][$clientNaam]+=$regel['actuelePortefeuilleWaardeEuro'];
            }
            else
            {
              
              $waarden['effecten'][$portefeuille][$portefeuilleNaam][$datum]['actuelePortefeuilleWaardeEuro'] += $regel['actuelePortefeuilleWaardeEuro'];
              if(!isset($waarden['effecten'][$portefeuille][$portefeuilleNaam]['fondsOmschrijving']))
                $waarden['effecten'][$portefeuille][$portefeuilleNaam]['fondsOmschrijving'] = $portefeuilleNaam;
              $waarden['effecten'][$portefeuille][$portefeuilleNaam][$datum][$clientNaam . '_actuelePortefeuilleWaardeEuro'] += $regel['actuelePortefeuilleWaardeEuro'];
              if($datum==$this->rapportageDatum)
              {
                // $waarden['effecten'][$portefeuille][$portefeuilleNaam]['stortingenOnttrekkingen'] += $mutatieLijst[$regel['fonds']][0];
              }
              if(!isset($waarden['effecten'][$portefeuille][$portefeuilleNaam]['performance']))
                $waarden['effecten'][$portefeuille][$portefeuilleNaam]['performance'] = $portefeuilleRendement[$portefeuille];
              
              // $totalen['stortingenOnttrekkingen']+= $mutatieLijst[$regel['fonds']][0];
              $subtotalen['effecten'][$datum]['totaal'] += $regel['actuelePortefeuilleWaardeEuro'];
              $subtotalen['effecten'][$datum][$clientNaam] += $regel['actuelePortefeuilleWaardeEuro'];
            }
            
            
            
            $totalen[$datum]['totaal_actuelePortefeuilleWaardeEuro']+=$regel['actuelePortefeuilleWaardeEuro'];
            $totalen[$datum][$clientNaam.'_actuelePortefeuilleWaardeEuro']+=$regel['actuelePortefeuilleWaardeEuro'];
            
          }
          // echo "$portefeuille $portefeuilleNaam ".$totaleStort['storting']."-".$waarden['effecten'][$portefeuille][$portefeuilleNaam]['liqMutatieTotaal']."<br>\n";
          $waarden['effecten'][$portefeuille][$portefeuilleNaam]['stortingenOnttrekkingen']=$totaleStort['storting']-$waarden['effecten'][$portefeuille][$portefeuilleNaam]['liqMutatieTotaal'];
        }
        else
        {
          foreach($fondsRegels as $regel)
          {
            $fondsRekeningKey = trim($regel['fonds'] . $regel['rekening']);
            /*
            if($regel['beleggingscategorie']=='EFFECT' || $regel['beleggingscategorie']=='AAND')
            {
              $regel['beleggingscategorie']='effecten';
            }
            */
            
            $waarden[$regel['beleggingscategorie']][$portefeuille][$fondsRekeningKey][$datum]['actuelePortefeuilleWaardeEuro'] += $regel['actuelePortefeuilleWaardeEuro'];
            if ($regel['type'] == 'rekening')
            {
              $query="SELECT  Rekeningen.Tenaamstelling FROM Rekeningen WHERE Rekeningen.Rekening='".mysql_real_escape_string($regel['rekening'])."'";
              $db->SQL($query);
              $tenaamstelling=$db->lookupRecord();
              if($tenaamstelling['Tenaamstelling']=='')
                $omschrijving = $regel['fondsOmschrijving'] . ' ' . $regel['rekening'];
              else
                $omschrijving = $regel['fondsOmschrijving'];
            }
            else
            {
              $omschrijving = $regel['fondsOmschrijving'];
            }
            
            if (!isset($waarden[$regel['beleggingscategorie']][$portefeuille][$fondsRekeningKey]['fondsOmschrijving']))
            {
              $waarden[$regel['beleggingscategorie']][$portefeuille][$fondsRekeningKey]['fondsOmschrijving'] = $omschrijving;
              $waarden[$regel['beleggingscategorie']][$portefeuille][$fondsRekeningKey]['fonds'] = $regel['fonds'];
            }
            $waarden[$regel['beleggingscategorie']][$portefeuille][$fondsRekeningKey][$datum][$clientNaam . '_actuelePortefeuilleWaardeEuro'] += $regel['actuelePortefeuilleWaardeEuro'];
            if ($regel['actueleFonds'] <> 0)
            {
              $waarden[$regel['beleggingscategorie']][$portefeuille][$fondsRekeningKey][$datum]['actueleFonds'] = $regel['actueleFonds'];
            }
            if ($regel['totaalAantal'] <> 0)
            {
              $waarden[$regel['beleggingscategorie']][$portefeuille][$fondsRekeningKey][$datum]['totaalAantal'] = $regel['totaalAantal'];
            }
            $subtotalen[$regel['beleggingscategorie']][$datum]['totaal'] += $regel['actuelePortefeuilleWaardeEuro'];
            $subtotalen[$regel['beleggingscategorie']][$datum][$clientNaam] += $regel['actuelePortefeuilleWaardeEuro'];
            $beleggingscategorieen[$regel['beleggingscategorie']] = $regel['beleggingscategorieVolgorde'];
            $beleggingscategorieOmschrijving[$regel['beleggingscategorie']] = $regel['beleggingscategorieOmschrijving'];
            
            if ($datum == $this->rapportageDatum)
            {
              if ($regel['type'] == 'rekening')
              {
                
                if($regel['valuta']<>'EUR')
                {
                  $st = getRekeningStortingen($regel['rekening'], $this->rapportageDatumVanaf, $this->rapportageDatum, 'EUR', true);//Kruis
                  $on = getRekeningOnttrekkingen($regel['rekening'], $this->rapportageDatumVanaf, $this->rapportageDatum, 'EUR', true);
                  if ($_POST['debug'])
                  {
                    echo "<hr>" . $regel['rekening'] . " " . $this->rapportageDatumVanaf . " " . $this->rapportageDatum . "<br>\n";
                    echo "stortingenOnttrekkingen: " . $waarden[$regel['beleggingscategorie']][$portefeuille][$fondsRekeningKey]['stortingenOnttrekkingen'] . "<br>\n";
                    listarray($st);
                    listarray($on);
                  }
                  $stortingen = $st['storting'] + $st['kruispost'];
                  $onttrekkingen = $on['onttrekking'] + $on['kruispost'];
                  //$stortingen=$st['kruispost'];
                  //$onttrekkingen=$on['kruispost'];
                  $stortingenOnttrekkingen = ($stortingen - $onttrekkingen);
                  
                  $waarden[$regel['beleggingscategorie']][$portefeuille][$fondsRekeningKey]['stortingenOnttrekkingen'] += $stortingenOnttrekkingen;
                  if ($_POST['debug'])
                  {
                    echo "stortingenOnttrekkingen: " . $waarden[$regel['beleggingscategorie']][$portefeuille][$fondsRekeningKey]['stortingenOnttrekkingen'] . "<br>\n";
                  }
                }
                /*
                
              $stortingen = getRekeningStortingen($regel['rekening'], $this->rapportageDatumVanaf, $this->rapportageDatum);
              $onttrekkingen = getRekeningOnttrekkingen($regel['rekening'], $this->rapportageDatumVanaf, $this->rapportageDatum);
              $stortingenOnttrekkingen=($stortingen - $onttrekkingen);
              
                              $totalen['stortingenOnttrekkingen']+= $stortingenOnttrekkingen;
                              $waarden[$regel['beleggingscategorie']][$portefeuille][$fondsRekeningKey]['stortingenOnttrekkingen']+=$stortingenOnttrekkingen;
                              */
                $waarden[$regel['beleggingscategorie']][$portefeuille][$fondsRekeningKey]['valuta']=$regel['valuta'];
                $waarden[$regel['beleggingscategorie']][$portefeuille][$fondsRekeningKey]['performance'] ='';
                
                
              }
              else if ($regel['type'] == 'fondsen')
              {
                $waarden[$regel['beleggingscategorie']][$portefeuille][$fondsRekeningKey]['performance'] = $this->fondsPerformance2($regel['fonds'], $this->rapportageDatumVanaf, $this->rapportageDatum);
                $query="SELECT Portefeuille FROM Fondsen WHERE Fondsen.fonds='".mysql_real_escape_string($regel['fonds'])."'";
                $db->SQL($query);
                $huisfondsPortefeuille=$db->lookupRecord();
                if($huisfondsPortefeuille['Portefeuille'] <> '')
                {
                  $aandeel=bepaalHuisfondsAandeel($regel['fonds'],$portefeuille,$datum);
                  $stortingen = getStortingen($huisfondsPortefeuille['Portefeuille'], $this->rapportageDatumVanaf, $this->rapportageDatum);
                  $onttrekkingen = getOnttrekkingen($huisfondsPortefeuille['Portefeuille'], $this->rapportageDatumVanaf, $this->rapportageDatum);
                  $waarden[$regel['beleggingscategorie']][$portefeuille][$fondsRekeningKey]['stortingenOnttrekkingen']=($stortingen-$onttrekkingen)*$aandeel;
                  // listarray($aandeel);
                  // listarray($huisfondsPortefeuille);
                  //  ob_flush();
                  
                  
                }
                else
                {
                  $waarden[$regel['beleggingscategorie']][$portefeuille][$fondsRekeningKey]['stortingenOnttrekkingen'] += $mutatieLijst[$regel['fonds']][0];
                }
              }
              
            }
            $totalen[$datum]['totaal_actuelePortefeuilleWaardeEuro'] += $regel['actuelePortefeuilleWaardeEuro'];
            $totalen[$datum][$clientNaam . '_actuelePortefeuilleWaardeEuro'] += $regel['actuelePortefeuilleWaardeEuro'];
            
          }
        }
      }
      
      $verdelingClient[$portefeuille]=$clientNaam;
    }
    // listarray($beleggingscategorieen);exit;
   // $beleggingscategorieen['effecten']=11;
    if(isset($liqCategorie))
    {
      if(!isset($beleggingscategorieen[$liqCategorie]))
        $beleggingscategorieen[$liqCategorie] = 220;
      if(!isset($beleggingscategorieOmschrijving[$liqCategorie]))
        $beleggingscategorieOmschrijving[$liqCategorie]='Liquiditeiten';
    }
   // $beleggingscategorieOmschrijving['effecten']='Effecten';
    // $beleggingscategorieOmschrijving[$liqCategorie]='Geld';
    asort($beleggingscategorieen);
    $clienten= array_unique($verdelingClient);
    //$verdelingscategorien=array_values($clienten);
    $verdelingscategorien=array();
    //listarray($beleggingscategorieen);exit;
    $this->pdf->rapport_header1 = array("Categorie\n    Beleggingen\n ","Waarde\nin ".$this->pdf->rapportageValuta."\n".date("d-m-y",$this->pdf->rapport_datumvanaf),
      "Stortingen/\n onttrekkingen\n ","Resultaat\nverslag-\nperiode","Waarde\nin ".$this->pdf->rapportageValuta."\n".date("d-m-y",$this->pdf->rapport_datum),"Weging\n\n ",
      "Directe\nOpbrengsten\n ","Rendement\nverslag-\nperiode",$verdelingscategorien[0]."\n\n ",$verdelingscategorien[1]."\n\n ");
  //  listarray($beleggingscategorieen);exit;
    
    $this->pdf->CellBorders = array();
    unset($this->pdf->widthsDefault);
    
    $this->pdf->addPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    
    $this->pdf->widthsDefault = $this->pdf->widths;
    $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'], $this->pdf->rapport_fonds_fontcolor['g'], $this->pdf->rapport_fonds_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->rapport_fonds_fontcolor['r'], $this->pdf->rapport_fonds_fontcolor['g'], $this->pdf->rapport_fonds_fontcolor['b']);
    foreach($beleggingscategorieen as $categorie=>$volgorde)
    {
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->row(array($beleggingscategorieOmschrijving[$categorie],
                        $this->formatGetal($subtotalen[$categorie][$this->rapportageDatumVanaf]['totaal'],0),'','',
                        $this->formatGetal($subtotalen[$categorie][$this->rapportageDatum]['totaal'],0),
                        $this->formatGetal($subtotalen[$categorie][$this->rapportageDatum]['totaal']/ $totalen[$this->rapportageDatum]['totaal_actuelePortefeuilleWaardeEuro']*100,1,true),'',
                        $this->formatGetal($subtotalen[$categorie][$this->rapportageDatum][$verdelingscategorien[0]],0),
                        $this->formatGetal($subtotalen[$categorie][$this->rapportageDatum][$verdelingscategorien[1]],0)));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      //  listarray($waarden[$categorie]);echo "test $categorie";
      foreach($waarden[$categorie] as $portefeuille=>$regels)
      {
        foreach($regels as $fondsRekening=>$regelWaarden)
        {
          //	listarray($regelWaarden);
          
          $stortingenOnttrekkingen=$regelWaarden['stortingenOnttrekkingen'];
          $resultaat = $regelWaarden[$this->rapportageDatum]['actuelePortefeuilleWaardeEuro'] - $regelWaarden[$this->rapportageDatumVanaf]['actuelePortefeuilleWaardeEuro'] - $regelWaarden['stortingenOnttrekkingen'];
          // echo  $fondsRekening." $categorie ".$regelWaarden['fondsOmschrijving']." | ".$stortingenOnttrekkingen ." | ".$resultaat.
          //   " | ".$regelWaarden[$this->rapportageDatum]['actuelePortefeuilleWaardeEuro']." - ".$regelWaarden[$this->rapportageDatumVanaf]['actuelePortefeuilleWaardeEuro']." - ".$regelWaarden['stortingenOnttrekkingen']."<br>\n";
          if($categorie=='Liquiditeiten')// && $portefeuilleDetails[$portefeuille]['Portefeuillesoort']<>'Effecten')
          {
            if($regelWaarden['valuta']=='EUR')
            {
              $stortingenOnttrekkingen = $resultaat;
              $resultaat = 0;
            }
            $nulTonen=false;
          }
          else
          {
            $nulTonen=true;
          }
  
         $opbrengst=$this->getDirecteOpbrengst($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$regelWaarden);
          
          $totalen['resultaat'] += $resultaat;
          $totalen['stortingenOnttrekkingen']+= $stortingenOnttrekkingen;
          $row=array($regelWaarden['fondsOmschrijving'],
            $this->formatGetal($regelWaarden[$this->rapportageDatumVanaf]['actuelePortefeuilleWaardeEuro'],0),
            $this->formatGetal($stortingenOnttrekkingen,0),
            $this->formatGetal($resultaat,0,false,false,$nulTonen),
            $this->formatGetal($regelWaarden[$this->rapportageDatum]['actuelePortefeuilleWaardeEuro'],0),
            $this->formatGetal($regelWaarden[$this->rapportageDatum]['actuelePortefeuilleWaardeEuro']/$totalen[$this->rapportageDatum]['totaal_actuelePortefeuilleWaardeEuro']*100,1,true,false,true),
            $this->formatGetal($opbrengst,0),
            $this->formatGetal($regelWaarden['performance'],1,true,false,true),
         //   $this->formatGetal($regelWaarden[$this->rapportageDatum][$verdelingscategorien[0].'_actuelePortefeuilleWaardeEuro'],0),
         //   $this->formatGetal($regelWaarden[$this->rapportageDatum][$verdelingscategorien[1].'_actuelePortefeuilleWaardeEuro'],0)
					);
          $this->pdf->row($row);
          
        }
      }
      $this->pdf->ln();
    }
    /*
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',
                      '','','',
                      '','','',
                    //  $this->formatGetal($totalen[$this->rapportageDatum][$verdelingscategorien[0].'_actuelePortefeuilleWaardeEuro'],0),
                    //  $this->formatGetal($totalen[$this->rapportageDatum][$verdelingscategorien[1].'_actuelePortefeuilleWaardeEuro'],0)
										));
    
    $waardeA = '';
    $waardeB = '';
    $lijnA='';
    $lijnB='';
    if($verdelingscategorien[0]<>'')
    {
      //$waardeA = $this->formatGetal($totalen[$this->rapportageDatum][$verdelingscategorien[0] . '_actuelePortefeuilleWaardeEuro'] / $totalen[$this->rapportageDatum]['totaal_actuelePortefeuilleWaardeEuro'] * 100, 0) . '%';
     // $lijnA='T';
    }
    if($verdelingscategorien[1]<>'')
    {
     // $waardeB = $this->formatGetal($totalen[$this->rapportageDatum][$verdelingscategorien[1] . '_actuelePortefeuilleWaardeEuro'] / $totalen[$this->rapportageDatum]['totaal_actuelePortefeuilleWaardeEuro'] * 100, 0) . '%';
    //  $lijnB='T';
    }
    
    $this->pdf->CellBorders=array('','','','','','','',$lijnA,$lijnB);
    
    $this->pdf->row(array('',
                      '','','',
                      '','','',
                      $waardeA,
                      $waardeB));
    */
    unset($this->pdf->CellBorders);
    $this->pdf->ln();
    
    $paginaHoogte=$this->pdf->getY()+10;
    if($paginaHoogte>$this->pdf->PageBreakTrigger)
      $this->pdf->addPage();
    
    
    
   // $this->pdf->SetTextColor(255,255,255);
    $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'], $this->pdf->rapport_fonds_fontcolor['g'], $this->pdf->rapport_fonds_fontcolor['b']);
    //  $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    
    $this->pdf->Rect($this->pdf->marge,$this->pdf->getY()-1,297-$this->pdf->marge*2,6,'F');
    $this->pdf->row(array('Totaal',
                      $this->formatGetal($totalen[$this->rapportageDatumVanaf]['totaal_actuelePortefeuilleWaardeEuro'],0),
                      $this->formatGetal($totalen['stortingenOnttrekkingen'],0),
                      $this->formatGetal($totalen['resultaat'],0),
                      $this->formatGetal($totalen[$this->rapportageDatum]['totaal_actuelePortefeuilleWaardeEuro'],0),
                      $this->formatGetal($totalen[$this->rapportageDatum]['totaal_actuelePortefeuilleWaardeEuro']/$totalen[$this->rapportageDatum]['totaal_actuelePortefeuilleWaardeEuro']*100,0,true),
                      $this->formatGetal($rendementProcent,1).'%',
                      '',
                      ''));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
  }
  
  function genereerMutatieLijst($portefeuille,$rapportageDatumVanaf,$rapportageDatum)
  {
    // loopje over Grootboekrekeningen Opbrengsten = 1
    if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    else
      $koersQuery = "";
    
    $query = "SELECT Fondsen.Omschrijving, ".
      "Fondsen.Fondseenheid, ".
      "Rekeningmutaties.Boekdatum, ".
      "Rekeningmutaties.Transactietype,
		Rekeningmutaties.Valuta,
		Rekeningmutaties.Fonds,
		 Rekeningmutaties.Afschriftnummer,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, Rekeningmutaties.Fonds,  ".
      "Rekeningmutaties.Fondskoers, ".
      "Rekeningmutaties.Debet as Debet, ".
      "Rekeningmutaties.Credit as Credit, ".
      "Rekeningmutaties.Valutakoers,
		 1 $koersQuery   as Rapportagekoers
		 ,BeleggingssectorPerFonds.Beleggingssector,BeleggingssectorPerFonds.AttributieCategorie, BeleggingscategoriePerFonds.Beleggingscategorie ".
      "FROM (Rekeningmutaties, Fondsen, Rekeningen, Portefeuilles, Grootboekrekeningen)
		LEFT Join BeleggingssectorPerFonds ON Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
		LEFT Join BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' ".
      "WHERE ".
      "Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
      "Rekeningmutaties.Fonds = Fondsen.Fonds AND ".
      "Rekeningen.Portefeuille = '".$portefeuille."' AND ".
      "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
      "Rekeningmutaties.Verwerkt = '1' AND ".
      "Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND ".
      "Rekeningmutaties.Transactietype <> 'B' AND ".
      "Grootboekrekeningen.FondsAanVerkoop = '1' AND ".
      "Rekeningmutaties.Boekdatum > '$rapportageDatumVanaf' AND ".
      "Rekeningmutaties.Boekdatum <= '$rapportageDatum' ".
      "ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    // haal koersresultaat op om % te berekenen
    
    
    $buffer = array();
    $sortBuffer = array();
    $totaal_aankoop_waarde=0;
    
    while($mutaties = $DB->nextRecord())
    {
      $buffer[] = $mutaties;
    }
    
    foreach ($buffer as $mutaties)
    {
      $mutaties['Aantal'] = abs($mutaties['Aantal']);
      $aankoop_koers = "";
      $aankoop_waardeinValuta = "";
      $aankoop_waarde = "";
      $verkoop_koers = "";
      $verkoop_waardeinValuta = "";
      $verkoop_waarde = "";
      $historisch_kostprijs = "";
      $resultaat_voorgaande = "";
      $resultaat_lopendeProcent = "";
      $resultaatlopende = 0 ;
      
      
      switch($mutaties['Transactietype'])
      {
        case "A" :
          // Aankoop
          $t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
          $t_aankoop_waardeinValuta = abs($mutaties['Debet']);
          $t_aankoop_koers					= $mutaties['Fondskoers'];
          
          $totaal_aankoop_waarde += $t_aankoop_waarde;
          
          if($t_aankoop_waarde > 0)
            $aankoop_koers 					= $t_aankoop_koers;
          if($t_aankoop_waardeinValuta > 0)
            $aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
          if($t_aankoop_koers > 0)
            $aankoop_waarde 				= $t_aankoop_waarde;
          break;
        case "A/O" :
          // Aankoop / openen
          $t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
          $t_aankoop_waardeinValuta = abs($mutaties['Debet']);
          $t_aankoop_koers					= $mutaties['Fondskoers'];
          
          $totaal_aankoop_waarde += $t_aankoop_waarde;
          
          if($t_aankoop_waarde > 0)
            $aankoop_koers 					= $t_aankoop_koers;
          if($t_aankoop_waardeinValuta > 0)
            $aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
          if($t_aankoop_koers > 0)
            $aankoop_waarde 				= $t_aankoop_waarde;
          break;
        case "A/S" :
          // Aankoop / sluiten
          $t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
          $t_aankoop_waardeinValuta = abs($mutaties['Debet']);
          $t_aankoop_koers					= $mutaties['Fondskoers'];
          
          $totaal_aankoop_waarde += $t_aankoop_waarde;
          
          if($t_aankoop_waarde > 0)
            $aankoop_koers 					= $t_aankoop_koers;
          if($t_aankoop_waardeinValuta > 0)
            $aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
          if($t_aankoop_koers > 0)
            $aankoop_waarde 				= $t_aankoop_waarde;
          
          break;
        case "B" :
          // Beginstorting
          break;
        case "D" :
        case "S" :
          // Deponering
          $t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
          $t_aankoop_waardeinValuta = abs($mutaties['Debet']);
          $t_aankoop_koers					= $mutaties['Fondskoers'];
          
          $totaal_aankoop_waarde += $t_aankoop_waarde;
          
          if($t_aankoop_waarde > 0)
            $aankoop_koers 					= $t_aankoop_koers;
          if($t_aankoop_waardeinValuta > 0)
            $aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
          if($t_aankoop_waarde > 0)
            $aankoop_waarde 				= $t_aankoop_waarde;
          break;
        case "L" :
          // Lichting
          $t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
          $t_verkoop_waardeinValuta = abs($mutaties['Credit']);
          $t_verkoop_koers					= $mutaties['Fondskoers'];
          
          $totaal_verkoop_waarde += $t_verkoop_waarde;
          
          if($t_verkoop_koers > 0)
            $verkoop_koers 					= $t_verkoop_koers;
          if($t_verkoop_waardeinValuta > 0)
            $verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
          if($t_verkoop_waarde > 0)
            $verkoop_waarde 				= $t_verkoop_waarde;
          break;
        case "V" :
          // Verkopen
          $t_verkoop_waarde 				= ($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
          $t_verkoop_waardeinValuta = ($mutaties['Credit']);
          $t_verkoop_koers					= $mutaties['Fondskoers'];
          
          $totaal_verkoop_waarde += $t_verkoop_waarde;
          
          //if($t_verkoop_koers > 0)
          $verkoop_koers 					= $t_verkoop_koers;
          //if($t_verkoop_waardeinValuta > 0)
          $verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
          //if($t_verkoop_waarde > 0)
          $verkoop_waarde 				= $t_verkoop_waarde;
          break;
        case "V/O" :
          // Verkopen / openen
          $t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
          $t_verkoop_waardeinValuta = abs($mutaties['Credit']);
          $t_verkoop_koers					= $mutaties['Fondskoers'];
          
          $totaal_verkoop_waarde += $t_verkoop_waarde;
          
          if($t_verkoop_koers > 0)
            $verkoop_koers 					= $t_verkoop_koers;
          if($t_verkoop_waardeinValuta > 0)
            $verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
          if($t_verkoop_waarde > 0)
            $verkoop_waarde 				= $t_verkoop_waarde;
          break;
        case "V/S" :
          // Verkopen / sluiten
          $t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
          $t_verkoop_waardeinValuta = abs($mutaties['Credit']);
          $t_verkoop_koers					= $mutaties['Fondskoers'];
          
          $totaal_verkoop_waarde += $t_verkoop_waarde;
          
          if($t_verkoop_koers > 0)
            $verkoop_koers 					= $t_verkoop_koers;
          if($t_verkoop_waardeinValuta > 0)
            $verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
          if($t_verkoop_waarde > 0)
            $verkoop_waarde 				= $t_verkoop_waarde;
          break;
        default :
          $_error = "Fout ongeldig tranactietype!!";
          break;
      }
      
      /*
        Alleen resultaat berekenen bij "Sluiten", niet bij "Openen".
      */
      
      if(	$mutaties['Transactietype'] == "L" ||
        $mutaties['Transactietype'] == "V" ||
        $mutaties['Transactietype'] == "V/S" ||
        $mutaties['Transactietype'] == "A/S")
      {
        
        $historie = berekenHistorischKostprijs($this->portefeuille, $mutaties['Fonds'], $mutaties['Boekdatum'],$this->pdf->rapportageValuta);
        
        if($mutaties['Transactietype'] == "A/S")
        {
          $historischekostprijs  = ($mutaties['Aantal'] * -1) * $historie['historischeWaarde']      * $historie['historischeValutakoers']        * $mutaties['Fondseenheid'];
          $beginditjaar          = ($mutaties['Aantal'] * -1) * $historie['beginwaardeLopendeJaar'] * $historie['beginwaardeValutaLopendeJaar']  * $mutaties['Fondseenheid'];
        }
        else
        {
          $historischekostprijs = $mutaties['Aantal']        * $historie['historischeWaarde']       * $historie['historischeValutakoers']        * $mutaties['Fondseenheid'];
          $beginditjaar         = $mutaties['Aantal']        * $historie['beginwaardeLopendeJaar']  * $historie['beginwaardeValutaLopendeJaar']  * $mutaties['Fondseenheid'];
        }
        if($this->pdf->rapportageValuta != 'EUR' && $mutaties['Valuta'] == $this->pdf->rapportageValuta)
        {
          $historischekostprijs = $historischekostprijs / $historie['historischeValutakoers'];
          $beginditjaar         = $beginditjaar         / getValutaKoers($this->pdf->rapportageValuta ,date("Y",db2jul($this->rapportageDatum).'-01-01'));
        }
				elseif ($this->pdf->rapportageValuta != 'EUR')
        {
          $historischekostprijs = $historischekostprijs / $historie['historischeRapportageValutakoers'];
          $beginditjaar         = $beginditjaar         / getValutaKoers($this->pdf->rapportageValuta ,date("Y",db2jul($this->rapportageDatum).'-01-01'));
        }
        
        if($historie[voorgaandejarenActief] == 0)
        {
          $resultaatvoorgaande = 0;
          $resultaatlopende = $t_verkoop_waarde - $historischekostprijs;
          if($mutaties['Transactietype'] == "A/S")
          {
            $resultaatvoorgaande = 0;
            $resultaatlopende = $t_aankoop_waarde - $historischekostprijs;
          }
        }
        else
        {
          $resultaatvoorgaande = $beginditjaar - $historischekostprijs;
          $resultaatlopende = $t_verkoop_waarde - $beginditjaar;
          if($mutaties['Transactietype'] == "A/S")
          {
            $resultaatvoorgaande = $beginditjaar - $historischekostprijs;
            $resultaatlopende = ($t_aankoop_waarde * -1) - $beginditjaar;
          }
        }
        $result_historischkostprijs = $historischekostprijs;
        $result_voorgaandejaren = $resultaatvoorgaande;
        $result_lopendejaar = $resultaatlopende;
        
        $totaal_resultaat_waarde += $resultaatlopende;
        
      }
      else
      {
        $result_historischkostprijs = "";
        $result_voorgaandejaren = "";
        $result_lopendejaar = "";
      }
      
      //	listarray($mutaties);
      $data[$mutaties['Fonds']][0]+=$aankoop_waarde-$verkoop_waarde;
      $data[$mutaties['Fonds']][1].=' '.$mutaties['Transactietype'];
      if($mutaties['Credit'])
        $data[$mutaties['Fonds']][2]+=$mutaties['Aantal'];
      else
        $data[$mutaties['Fonds']][2]+=$mutaties['Aantal'];
      $data[$mutaties['Fonds']][3]+=$aankoop_waarde;
      $data[$mutaties['Fonds']][4]+=$verkoop_waarde;
      $data[$mutaties['Fonds']][5]=$mutaties['Beleggingssector'];
      $data[$mutaties['Fonds']][6]=$mutaties['Beleggingscategorie'];
      $data[$mutaties['Fonds']][7]=$mutaties['AttributieCategorie'];
      
      
      /*
      $data[]=array(date("d-m",db2jul($mutaties['Boekdatum'])),
                    $mutaties['Transactietype'],
                    $mutaties['Fonds'],
                    $this->formatGetal($mutaties['Aantal'],0),
                    "",
                    $aankoop_koers,
                    $aankoop_waardeinValuta,
                    $aankoop_waarde,
                    $verkoop_koers,
                    $verkoop_waardeinValuta,
                    $verkoop_waarde,
                    $result_historischkostprijs,
                    $result_voorgaandejaren,
                    $result_lopendejaar,
                    $percentageTotaalTekst);
      */
    }
    //listarray($data);
    return $data;
  }
  
  function getRekeningMutaties($rekening,$van,$tot)
  {
    $db= new DB();
    
    if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
      $koersQueryBoekdatum =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    else
      $koersQueryBoekdatum = "";
    
    $query = "
	  SELECT
  SUM(((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers))$koersQueryBoekdatum)  as totaal
 	FROM
	Rekeningmutaties
  WHERE
	Rekeningmutaties.Rekening =  '$rekening'  AND
 	Rekeningmutaties.Verwerkt = '1' AND
	Rekeningmutaties.Boekdatum > '$van' AND
	Rekeningmutaties.Boekdatum <= '$tot'";
    
    $db->SQL($query);
    $db->Query();
    $data = $db->nextRecord();
    //echo "$rekening <br>\n $query<br>\n".$data['totaal']."<br>\n";
    return $data['totaal'];
  }
  
  
  
  function fondsKostenOpbrengsten($fonds,$datumBegin,$datumEind)
  {
    
    if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
      $koersQueryBoekdatum =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    else
      $koersQueryBoekdatum = "";
    
    
    $DB=new DB();
    $query = "SELECT
      Sum(((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))$koersQueryBoekdatum) AS totaalWaarde
      FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
      JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
      WHERE
      (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
      Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
      Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
      Rekeningmutaties.Boekdatum <= '$datumEind' AND
      Rekeningmutaties.Fonds = '$fonds'";
    $DB->SQL($query);
    //if($fonds=='Citigroup')
    //  echo "$fonds $query  <br>\n";
    $DB->Query();
    $totaalWaarde = $DB->NextRecord();
    
    return $totaalWaarde['totaalWaarde'];
  }
  
  
  function fondsPerformance2($fonds,$datumBegin,$datumEind,$debug=false)
  {
    global $__appvar;
    $DB=new DB();
    //$datum=$this->getmaanden(db2jul($datumBegin),db2jul($datumEind));
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
        $fondsenWhere = " IN('$fonds') ";
      
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
      //$AttributieStortingenOntrekkingenRekening = $DB->NextRecord();
      
      
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
      //$AttributieStortingenOntrekkingen = $DB->NextRecord(); // echo "$queryAttributieStortingenOntrekkingen <br>\n<br>\n";
      
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
      //listarray($RekeningDirecteKostenOpbrengsten);        listarray($FondsDirecteKostenOpbrengsten);
      if($beginCorrectie)
      {
        $AttributieStortingenOntrekkingen['gewogen']=$AttributieStortingenOntrekkingen['totaal'];//*-1;
        $directeKostenOpbrengsten['gewogen']=$directeKostenOpbrengsten['totaal'];//*-1;
      }
      if($eindCorrectie)
      {
        $AttributieStortingenOntrekkingen['gewogen']=0;
        $directeKostenOpbrengsten['gewogen']=0;
      }
      
      if($beginCorrectie && $eindCorrectie)
      {
        $performance=$AttributieStortingenOntrekkingen['totaal']/ $beginTransactieWaarde * -100;
        //echo "perf $performance=".$AttributieStortingenOntrekkingen['totaal']."/ $beginTransactieWaarde * -100; <br>\n";
      }
      else
      {
        $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'] - $directeKostenOpbrengsten['gewogen'] ;
        if($beginwaarde > 0 && $gemiddelde <0)
        {
          //echo "$fondsenWhere $gemiddelde <br>\n";
          $gemiddelde=$gemiddelde*-1;
        }
        $performance = ((($eindwaarde - $beginwaarde) + $AttributieStortingenOntrekkingen['totaal'] + $directeKostenOpbrengsten['totaal'] ) / $gemiddelde) * 100;
      }
      
      //		if(in_array($fonds,array('Adelphi EUR A','Adelphi Europe Fnd B EUR')))
      //$debug=true;
      //	else
      $debug=false;
      //	echo "$fonds $debug<br>\n";
      if($debug )//
      {
        echo "    <br>\n" ;
        echo "$datumBegin $datumEind ($beginCorrectie) ($eindCorrectie) $datumBeginWeging<br>\n";
        
        //    echo "$queryAttributieStortingenOntrekkingen <br>\n";
        //  echo "$queryAttributieStortingenOntrekkingenRekening <br>\n $queryRekeningDirecteKostenOpbrengsten <br>\n $queryRekeningDirecteKostenOpbrengsten <br>\n " ;
        //echo "$queryFondsDirecteKostenOpbrengsten <br>\n";
        echo "$queryAttributieStortingenOntrekkingenRekening <br>\n $queryAttributieStortingenOntrekkingen <br>\n";
        //  listarray($directeKostenOpbrengsten);
        //  listarray($AttributieStortingenOntrekkingen);
        
        echo "$fondsenWhere $datumBegin -> $datumEind <br>\n";
        echo "gemiddelde= 	 $gemiddelde = begin $beginwaarde -  gewogenSo ".$AttributieStortingenOntrekkingen['gewogen']." - gewogenDko ".$directeKostenOpbrengsten['gewogen']."<br>\n " ;
        echo "   $performance = ((($eindwaarde - $beginwaarde) + ".$AttributieStortingenOntrekkingen['totaal']." + ".$directeKostenOpbrengsten['totaal']." ) / $gemiddelde) * 100;	<br>\n";
        ob_flush();
        
        
        echo ($totaalPerf  * (100+$performance)/100)." = ($totaalPerf  * (100+$performance)/100) <br>\n";
      }
      $totaalPerf = ($totaalPerf  * (100+$performance)/100) ;
      
    }
    if($debug)// && $fonds=='Citigroup'
      echo " perftotaal ".($totaalPerf-100) ."<br>\n ";
    
    return ($totaalPerf-100);
  }
  
  
  function getKwartalen($julBegin, $julEind)
  {
    $eindjaar = date("Y",$julEind);
    $eindmaand = floor(date("m",$julEind)/3)*3;
    $beginjaar = date("Y",$julBegin);
    $startjaar = date("Y",$julBegin);
    $beginmaand = floor(date("m",$julBegin)/3)*3;
    
    $i=0;
    $stop=mktime (0,0,0,$eindmaand-3,0,$eindjaar);
    while ($counterStart <= $stop)
    {
      
      $counterStart = mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar);
      $counterEnd   = mktime (0,0,0,$beginmaand+$i+4,0,$beginjaar);
      if($counterEnd >= $julEind)
        $counterEnd = $julEind;
      if($i == 0)
      {
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
      }
      else
        $datum[$i]['start'] =date('Y-m-d',$counterStart);
      
      $datum[$i]['stop']=date('Y-m-d',$counterEnd);
      
      if($datum[$i]['start'] ==  $datum[$i]['stop'])
        unset($datum[$i]);
      $i=$i+3;
    }
    
    if($julEind > db2jul($datum[$i-3]['stop']))
    {
      $datum[$i]['start'] = $datum[$i-3]['stop'];
      $datum[$i]['stop'] = jul2sql($julEind);
    }
    return $datum;
  }
  
  function getMaanden($julBegin, $julEind)
  {
    $eindjaar = date("Y",$julEind);
    $eindmaand = date("m",$julEind);
    $beginjaar = date("Y",$julBegin);
    $startjaar = date("Y",$julBegin);
    $beginmaand = date("m",$julBegin);
    
    $i=0;
    $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
    while ($counterStart < $stop)
    {
      $counterStart = mktime (0,0,0,$beginmaand+$i,0,$beginjaar);
      $counterEnd   = mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar);
      if($counterEnd >= $julEind)
        $counterEnd = $julEind;
      
      if($i == 0)
      {
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
      }
      else
        $datum[$i]['start'] =date('Y-m-d',$counterStart);
      
      $datum[$i]['stop']=date('Y-m-d',$counterEnd);
      
      if($datum[$i]['start'] ==  $datum[$i]['stop'])
        unset($datum[$i]);
      $i++;
    }
    return $datum;
  }
  
  
}
?>

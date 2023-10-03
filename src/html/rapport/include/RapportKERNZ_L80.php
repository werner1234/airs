<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/24 06:30:58 $
File Versie					: $Revision: 1.8 $

$Log: RapportKERNZ_L80.php,v $
Revision 1.8  2020/05/24 06:30:58  rvv
*** empty log message ***

Revision 1.7  2020/05/23 16:39:00  rvv
*** empty log message ***

Revision 1.6  2020/05/16 15:57:02  rvv
*** empty log message ***

Revision 1.5  2019/12/01 07:51:04  rvv
*** empty log message ***

Revision 1.4  2019/04/07 11:06:41  rvv
*** empty log message ***

Revision 1.3  2019/04/06 17:11:28  rvv
*** empty log message ***

Revision 1.2  2019/02/03 13:43:54  rvv
*** empty log message ***

Revision 1.1  2019/01/30 16:47:26  rvv
*** empty log message ***

Revision 1.3  2018/12/09 13:00:15  rvv
*** empty log message ***

Revision 1.2  2018/12/08 18:28:30  rvv
*** empty log message ***

Revision 1.1  2018/10/03 15:42:01  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/RapportVKM.php");


class RapportKERNZ_L80
{
	function RapportKERNZ_L80($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{

	  $this->vkm=new RapportVKM(null,$portefeuille,$rapportageDatumVanaf,$rapportageDatum);
    $this->vkm->writeRapport();

		$this->pdf = &$pdf;
    if(count($pdf->portefeuilles)>1)
    {
      $this->consolidatie=true;
      $this->verdeling1='beleggingscategorie';
  
    }
    else
    {
      $this->consolidatie=false;
    }
		$this->pdf->rapport_type = "KERNZ";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Onderverdeling in beleggingscategorieën";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
	}

	function formatGetal($waarde, $dec)
	{
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
  
  function getVerdeling($verdeling)
  {
    global $__appvar;
    $DB=new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum ."' AND ".
      " portefeuille = '". $this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];
    
    $query = "SELECT TijdelijkeRapportage.type ,
       " . $verdeling . " as verdeling1,
       " . $verdeling . "Omschrijving as verdeling1Omschrijving,
	SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta,
	SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel,
  if(TijdelijkeRapportage.type='fondsen',1,(if(TijdelijkeRapportage.type='rente',2,3))) as volgorde
			 FROM TijdelijkeRapportage
			 WHERE TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND
			 TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "' " . $__appvar['TijdelijkeRapportageMaakUniek'] . "
GROUP BY TijdelijkeRapportage.type,	" . $verdeling . "
ORDER BY volgorde,	TijdelijkeRapportage." . $verdeling . "Volgorde ASC";
    debugSpecial($query, __FILE__, __LINE__);
    
    $DB->SQL($query);
    $DB->Query();
  //echo $verdeling;exit;
    if($verdeling=='beleggingscategorie'||$verdeling=='valuta')
    {
  
      $kleurdata = array();
      $grafiekCategorien = array();
      $kleurLookup = array('beleggingscategorie' => 'OIB', 'beleggingssector' => 'OIS', 'valuta' => 'OIV', 'regio' => 'OIR');
      $regels = array();
      $percentagePerCategorie = array();
      $kleurenPerCategorie = array();
      $overigeCategorie = '';
      $n = 0;
  
      $overigeCategorieOmschrijving = 'Overigen';
      while ($data = $DB->NextRecord())
      {
        if ($verdeling == 'beleggingssector')
        {
      
          if ($data['type'] == 'rekening')
          {
            $data['verdeling1'] = 'A-Diversen';
            $data['verdeling1Omschrijving'] = $overigeCategorieOmschrijving;
        
          }
        }
    
        $data['percentageVanTotaal'] = $data['subtotaalactueel'] / $totaalWaarde * 100;
    
        $percentagePerCategorie[$data['verdeling1Omschrijving']] += $data['percentageVanTotaal'];
    
        $kleurenPerCategorie[$data['verdeling1Omschrijving']] = $this->allekleuren[$kleurLookup[$verdeling]][$data['verdeling1']];
    
        $regels[$n] = $data;
      }
  
  
      $percentagePerCategorieSorted = $percentagePerCategorie;
      asort($percentagePerCategorieSorted);
      $percentagePerCategorieSorted = array_reverse($percentagePerCategorieSorted, true);
      $overige = array();
      if (count($percentagePerCategorieSorted) > 8)
      {
        $n = 0;
        foreach ($percentagePerCategorieSorted as $categorie => $percentage)
        {
          if ($n > 6 && $categorie <> $overigeCategorieOmschrijving)
          {
            $overige[$categorie] = $categorie;
          }
          if ($categorie <> $overigeCategorieOmschrijving)
          {
            $n++;
          }
        }
      }
  
  
      foreach ($percentagePerCategorie as $categorieOmschrijving => $percentage)
      {
        if (in_array($categorieOmschrijving, $overige))
        {
          $percentagePerCategorie[$overigeCategorieOmschrijving] += $percentage;
          unset($percentagePerCategorie[$categorieOmschrijving]);
        }
      }
  
      foreach ($percentagePerCategorie as $categorieOmschrijving => $percentage)
      {
    
        if (!isset($kleurdata[$categorieOmschrijving]))
        {
          $kleurdata[$categorieOmschrijving] = $kleurenPerCategorie[$categorieOmschrijving];
        }
        $kleurdata[$categorieOmschrijving]['percentage'] += $percentage;
        $grafiekCategorien[$categorieOmschrijving] += $percentage;
      }
   //   listarray(array('pieData' => $grafiekCategorien, 'kleurData' => $kleurdata));
      return array('pieData' => $grafiekCategorien, 'kleurData' => $kleurdata);
    }
    else
    {
  
      $doorkijkvertaling=array('regio'=>'Regios','beleggingssector'=>'Beleggingssectoren');
      $doorkijkSoort=$doorkijkvertaling[$verdeling];
      $belCategorien='';

      if($verdeling=='regio'||$verdeling='beleggingssector')
        $belCategorien=array('AAND');

  
      if(is_array($belCategorien) && count($belCategorien)>0)
        $fondsFilter="AND Beleggingscategorie IN('".implode("','",$belCategorien)."')";
      else
        $fondsFilter='';
  
  
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
        "FROM TijdelijkeRapportage WHERE ".
        " rapportageDatum ='".$this->rapportageDatum ."' AND ".
        " portefeuille = '". $this->portefeuille."' $fondsFilter"
        .$__appvar['TijdelijkeRapportageMaakUniek'];
      debugSpecial($query,__FILE__,__LINE__);
      $DB=new DB();
      $DB->SQL($query);
      $DB->Query();
      $totaalWaarde = $DB->nextRecord();
      $totaalWaarde = $totaalWaarde['totaal'];

      
      $vertaling=array('Beleggingscategorien'=>'Beleggingscategorie','Beleggingssectoren'=>'Beleggingssector','Regios'=>'Regio');
      $query = "SELECT fonds,rekening, actuelePortefeuilleWaardeEuro as waardeEUR, ".$vertaling[$doorkijkSoort]." as airsSoort
					FROM TijdelijkeRapportage	WHERE rapportageDatum ='".$this->rapportageDatum."'  $fondsFilter AND portefeuille = '" . $this->portefeuille . "'" .	$__appvar['TijdelijkeRapportageMaakUniek']." Order by fonds";
  
      $db=new DB();
      $db->SQL($query);
      $db->Query();
  
      $doorkijkVerdeling=array();
      $doorkijkVerdelingTmp=array();
      while($row = $db->nextRecord())
      {
        if ($row['fonds'] == '' && $doorkijkSoort <> 'Regios' && $doorkijkSoort <> 'Beleggingscategorien')
        {
          $row['fonds'] = $row['rekening'];
          $verdeling = array('Geldrekeningen' => array('weging' => 100, 'waarde' => $row['waardeEUR']));
        }
        else
        {
          if ($row['fonds'] == '')
          {
            $row['fonds'] = $row['rekening'];
          }
          //	listarray($row);
          $verdeling = $this->bepaalWegingPerFonds($row['fonds'], $doorkijkSoort, $row['airsSoort'], $row['waardeEUR'], $row['rekening']);
        }
  
  
        $totaalPercentage=0;
        if (is_array($verdeling))
        {
          $overige = false;
          $check = 0;
          
          foreach ($verdeling as $categorie => $percentage)
          {
  
        
            $check += $percentage['weging'];
            $totaalPercentage = ($percentage['weging'] * ($row['waardeEUR'] / $totaalWaarde));
            $doorkijkVerdelingTmp['categorien'][$categorie] += $totaalPercentage;
            $doorkijkVerdelingTmp['details'][$categorie]['percentage'] += $totaalPercentage;
            $doorkijkVerdelingTmp['details'][$categorie]['waardeEUR'] += $percentage['waarde'];

            
          }
  

          //listarray($kleurdata);exit;
          
          if ($check == 0)
          {
            $overige = true;
          }
    
        }
        else
        {
          $overige = true;
        }
  
        if ($overige == true)
        {
          $totaalPercentage = ($row['waardeEUR'] / $totaalWaarde) * 100;
          $doorkijkVerdelingTmp['categorien']['Overige'] += $totaalPercentage;
          $doorkijkVerdelingTmp['details']['Overige']['percentage'] += $totaalPercentage;
          $doorkijkVerdelingTmp['details']['Overige']['waardeEUR'] += $row['waardeEUR'];
    
        }
      }
  
  
      $kleurdata=array();
      $grafiekCategorien=array();
  
      foreach($this->doorkijkkleuren[$doorkijkSoort] as $categorie=>$kleuren)
      {
    
        if(isset($doorkijkVerdelingTmp['categorien'][$categorie]))
        {
          if (!isset($kleurdata[$categorie]))
          {
            $kleurdata[$categorie] = array('R' => array('value' => $this->doorkijkkleuren[$doorkijkSoort][$categorie][0]), 'G' => array('value' => $this->doorkijkkleuren[$doorkijkSoort][$categorie][1]), 'B' => array('value' => $this->doorkijkkleuren[$doorkijkSoort][$categorie][2]));
          }
          $kleurdata[$categorie]['percentage'] = $doorkijkVerdelingTmp['categorien'][$categorie];
      

          $grafiekCategorien[$categorie] += $kleurdata[$categorie]['percentage'];
          
        }
      }
      
      
      
      
      
      
      //listarray(array('pieData' => $grafiekCategorien, 'kleurData' => $kleurdata));
      return array('pieData' => $grafiekCategorien, 'kleurData' => $kleurdata);
      
    }
  
  }
  
  
  function bepaalWegingPerFonds($fonds,$doorkijkSoort,$airsCategorie,$waarde,$rekening)
  {
    $db=new DB();
    $query="SELECT MAX(datumVanaf) as vanafDatum FROM doorkijk_categorieWegingenPerFonds
WHERE fonds='".mysql_real_escape_string($fonds)."' AND msCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND  datumVanaf <= '" . $this->rapportageDatum . "' ";
    $db->executeQuery($query);
    $vanafDatum=$db->nextRecord();//listarray($query);
    
    $query="SELECT msCategorie,weging FROM doorkijk_categorieWegingenPerFonds
WHERE fonds='".mysql_real_escape_string($fonds)."' AND msCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND datumVanaf = '" . $vanafDatum['vanafDatum']. "'  ";
    $db->executeQuery($query);
    $wegingPerMsCategorie=array();
    while ($row = $db->nextRecord())
    {
      $wegingPerMsCategorie[$row['msCategorie']] = $row['weging'];

    }

    $wegingDoorkijkCategorie=array();
    $airsKoppelingen=array('REGION_ZOTHERND','ZSECTOR_OTHERND');
    if(count($wegingPerMsCategorie)>0)
    {
      foreach($airsKoppelingen as $categorie)
      {
        if (isset($wegingPerMsCategorie[$categorie]))
        {
          $query = "SELECT doorkijkCategorie FROM doorkijk_koppelingPerVermogensbeheerder WHERE bronKoppeling='$airsCategorie' AND doorkijkCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND systeem='AIRS' AND vermogensbeheerder='". $this->pdf->portefeuilledata['Vermogensbeheerder']."'";
          $db->executeQuery($query);
          //		listarray($wegingPerMsCategorie);
          //		echo $wegingPerMsCategorie[$categorie]."| $fonds | $airsCategorie | $doorkijkSoort | $query<br>\n";
          
          if($db->records()>0)
          {
            
            while ($row = $db->nextRecord())
            {
              $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['weging'] = $wegingPerMsCategorie[$categorie];
              $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['waarde'] = $waarde * $wegingPerMsCategorie[$categorie] / 100;
              

            }
            unset($wegingPerMsCategorie[$categorie]);
          }
        }
      }
      
      $msCategorienWhere=" bronKoppeling IN ('".implode("','",array_keys($wegingPerMsCategorie))."')";
      $query = "SELECT doorkijkCategorie,bronKoppeling as msCategorie FROM doorkijk_koppelingPerVermogensbeheerder
WHERE $msCategorienWhere AND doorkijkCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND systeem='MS' AND vermogensbeheerder='". $this->pdf->portefeuilledata['Vermogensbeheerder']."'";
      $db->executeQuery($query);
      
      while ($row = $db->nextRecord())
      {
        $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['weging'] += $wegingPerMsCategorie[$row['msCategorie']];
        $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['waarde'] += $waarde*$wegingPerMsCategorie[$row['msCategorie']]/100;
        

      }
    }
    else
    {
      $query = "SELECT doorkijkCategorie FROM doorkijk_koppelingPerVermogensbeheerder
WHERE bronKoppeling='$airsCategorie' AND doorkijkCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND systeem='AIRS' AND vermogensbeheerder='". $this->pdf->portefeuilledata['Vermogensbeheerder']."'";
      $db->executeQuery($query);
      
      while ($row = $db->nextRecord())
      {
        $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['weging'] = 100;
        $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['waarde'] = $waarde;

      }
    }

    
    return $wegingDoorkijkCategorie;
  }
  
  function getDoorkijkKleuren()
  {
    $db=new DB();
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $query = "SELECT doorkijkCategorie,doorkijkCategorieSoort,grafiekKleur, afdrukVolgorde
                   FROM doorkijk_categoriePerVermogensbeheerder
                   WHERE Vermogensbeheerder='$beheerder'
                   ORDER BY doorkijkCategorieSoort,afdrukVolgorde
                  ";
  
    $db->SQL($query);
    $db->Query();
    $this->kleuren=array();
    while($data = $db->nextRecord())
    {
      $this->doorkijkkleuren[$data['doorkijkCategorieSoort']][$data['doorkijkCategorie']]=unserialize($data['grafiekKleur']);
    }

  }
  

	function writeRapport()
	{
    global $__appvar;
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
		$this->getDoorkijkKleuren();
		
		$this->pdf->AddPage();
    $this->pdf->templateVars['OIBPaginas']=$this->pdf->page;

		$rapportageDatum = $this->rapportageDatum;
		$rapportageDatumVanaf = $this->rapportageDatumVanaf;
	$portefeuille = $this->portefeuille;

	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
					 "FROM TijdelijkeRapportage WHERE ".
					 " rapportageDatum ='".$rapportageDatum."' AND ".
					 " portefeuille = '".$portefeuille."' "
					 .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$totaalWaarde = $DB->nextRecord();
	$totaalWaarde = $totaalWaarde['totaal'];

  $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
					 "FROM TijdelijkeRapportage WHERE ".
					 " rapportageDatum ='".$rapportageDatumVanaf."' AND ".
					 " portefeuille = '".$portefeuille."' "
					 .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$totaalWaardeBegin = $DB->nextRecord();
	$totaalWaardeBegin = $totaalWaardeBegin['totaal'];


	//Kleuren instellen
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$this->allekleuren = unserialize($kleuren['grafiek_kleur']);
  
		$verdelingen=array('beleggingscategorie'=>array(),'valuta'=>array(),'beleggingssector'=>array(),'regio'=>array());
  
		$n=0;
		foreach($verdelingen as $categorie=>$data)
    {
      $verdeling=  $this->getVerdeling($categorie);
      if($categorie=='regio'||$categorie=='beleggingssector')
        $toevoeging=' Aandelen';
      else
        $toevoeging='';
      //listarray($verdeling);
      $this->pdf->setXY(10+$n*74, 37);
//$this->pdf->setXY(65,40);
      
      if(min($verdeling['pieData'])>0)
        $this->printPie($verdeling['pieData'], $verdeling['kleurData'],  ucfirst($categorie).$toevoeging , 30, 30);
      else
      {
        $this->pdf->setXY(10+$n*74+10, 37);
        BarDiagram($this->pdf, 40, 40, $verdeling['pieData'], '%l (%p)', $verdeling['kleurData'], ucfirst($categorie).$toevoeging);
      }
      
      
     
      $this->pdf->wLegend = 0;
      $n++;
    }
    
    $this->toonPerfGrafiek();
    $this->toonVKMWaarden();
    $this->toonPERFWaarden();
 
	}
  
  function toonVKMWaarden()
  {
    $this->pdf->SetXY($this->pdf->marge,120);
    $this->pdf->setWidths(array(100,50,15,20));
    $this->pdf->SetAligns(array('L','L','R','R'));
    /*
    $this->pdf->Row(array('','Indirecte (fonds)kosten',$this->formatGetal($this->vkm->vkmWaarde['totaalDoorlopendekosten'],0).' EUR'));
    $this->pdf->Row(array('','Indirecte (fonds)kosten ten opzichte van onderliggend vermogen',$this->formatGetal($this->vkm->vkmWaarde['doorlopendeKostenPercentage']*100,2).' %'));
    $this->pdf->ln();
    $this->pdf->Row(array('','Percentage van het gemiddeld indirect vermogen met een kostenfactor',$this->formatGetal($this->vkm->vkmWaarde['percentageIndirectVermogenMetKostenfactor']*100,2).' %'));
    $this->pdf->Row(array('','Herrekende indirecte (fonds)kosten',$this->formatGetal($this->vkm->vkmWaarde['doorlopendeKostenPercentage']/$this->vkm->vkmWaarde['percentageIndirectVermogenMetKostenfactor']*100,2).' %'));
    $this->pdf->Row(array('','Aandeel indirecte beleggingen',$this->formatGetal($this->vkm->vkmWaarde['fondsGemiddeldeWaarde']/$this->vkm->vkmWaarde['gemiddeldeWaarde']*100,2).' %'));
    $this->pdf->ln();
    $this->pdf->Row(array('','Gemiddeld vermogen',$this->formatGetal($this->vkm->vkmWaarde['gemiddeldeWaarde'],0).' EUR'));
    */
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->Row(array('','','EUR','Percentage'));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->Row(array('','Indirecte (fonds)kosten factor van de portefeuille',"\n".$this->formatGetal($this->vkm->vkmWaarde['totaalDoorlopendekosten'],0),"\n".$this->formatGetal($this->vkm->vkmWaarde['vkmPercentagePortefeuille'],2).' %'));
    $this->pdf->Row(array('','Totaal directe kosten',$this->formatGetal($this->vkm->vkmWaarde['totaalDirecteKosten'],0),$this->formatGetal($this->vkm->vkmWaarde['kostenPercentage'],2).' %'));
    $this->pdf->ln();
    $this->pdf->Row(array('','Vergelijkende kostenmaatstaf',$this->formatGetal($this->vkm->vkmWaarde['vkmWaarde']*$this->vkm->vkmWaarde['gemiddeldeWaarde']/100, 0), $this->formatGetal($this->vkm->vkmWaarde['vkmWaarde'], 2).' %'));
  
    //Dus fondskosten absoluut en relaties, directe kosten absoluut en relatief en totale kosten absoluut en relatief
  
  }
  
  function toonPERFWaarden()
  {
    global $__appvar;
    $DB=new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersBegin." ) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();
		$totaalWaardeVanaf = $DB->nextRecord();

		$waardeEind			  	= $totaalWaarde['totaal'];
		$waardeBegin 			 	= $totaalWaardeVanaf['totaal'];
		$waardeMutatie 	   	= $waardeEind - $waardeBegin;
		$stortingen 			 	= getStortingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$onttrekkingen 		 	= getOnttrekkingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;

	  $this->pdf->SetXY($this->pdf->marge,120);
    $this->pdf->setWidths(array(190,60,25));
    $this->pdf->SetAligns(array('L','L','R'));
    $posSubtotaal=$this->pdf->marge+190+60;
    $extraLengte=0;
    $posSubtotaalEnd=$this->pdf->marge+190+60+25;

			$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",$this->pdf->rapport_datumvanaf)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datumvanaf),$this->formatGetal($waardeBegin,2,true),""));
			$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatum)),$this->formatGetal($waardeEind,2),""));
			// subtotaal
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->ln();
			$this->pdf->row(array("",vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal),$this->formatGetal($waardeMutatie,2),""));
			$this->pdf->row(array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($stortingen,2),""));
			$this->pdf->row(array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($onttrekkingen,2),""));
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->ln();
			$this->pdf->row(array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($resultaatVerslagperiode,2),""));
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY()+1 ,$posSubtotaalEnd ,$this->pdf->GetY()+1);
			$this->pdf->ln();
      
      
  }
  
  function toonPerfGrafiek()
  {
    
    $DB = new DB();
$query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE periode='m' AND Portefeuille = '".$this->portefeuille."' AND Categorie = 'Totaal' ORDER BY Datum ASC LIMIT 1 ";
$DB->SQL($query);
$DB->Query();
$datum = $DB->nextRecord();


if($datum['id'] > 0 && $this->pdf->lastPOST['perfPstart'] == 1)
{
  if($datum['month'] <10)
    $datum['month'] = "0".$datum['month'];
  $start = $datum['year'].'-'.$datum['month'].'-01';
}
else
  $start = substr($this->pdf->PortefeuilleStartdatum,0,10);
$eind = $this->rapportageDatum;


    $index = new indexHerberekening();
$indexData = $index->getWaarden($start,$eind,$this->portefeuille,$this->pdf->portefeuilledata['SpecifiekeIndex']);

foreach ($indexData as $index=>$data)
{
  if($data['datum'] != '0000-00-00')
  {
    $rendamentWaarden[] = $data;
    $grafiekData['Datum'][] = $data['datum'];
    $grafiekData['Index'][] = $data['index']-100;
    if($this->pdf->portefeuilledata['SpecifiekeIndex']<>'')
      $grafiekData['benchmarkIndex'][] = $data['specifiekeIndex']-100;

  }
}
    
    	if (count($grafiekData) > 1)
		  {
        $yShift=-3;
        $this->pdf->SetXY(8,111+$yShift);//104
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  		  $this->pdf->Cell(0, 5, vertaalTekst('Rendement',$this->pdf->rapport_taal).' ('.
                               vertaalTekst('cumulatief',$this->pdf->rapport_taal).' '.
                               vertaalTekst('in',$this->pdf->rapport_taal).' %)', 0, 1);
  		  $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
  		  $this->pdf->SetXY(15,117+$yShift)		;//112
        $valX = $this->pdf->GetX();
        $valY = $this->pdf->GetY();
        //function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
        $kleuren=array(array(74,166,77),array(61,59,56));
        $this->LineDiagram(90, 60, $grafiekData,$kleuren,0,0,6,5,1);//50
        $this->pdf->SetXY($valX, $valY + 75+$yShift);
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
       
        foreach($kleuren as $index=>$kleur)
        {
          
          if($index==0)
          {
            $this->pdf->rect($this->pdf->getX()-2,$this->pdf->getY()+1,2,2,'F','',$kleur);
            $this->pdf->Cell(50, 4, 'Portefeuille', 0, 0, "L");
          }
          elseif($index==1 && trim($this->pdf->portefeuilledata['SpecifiekeIndex'])<>'')
          {
            $this->pdf->rect($this->pdf->getX()-2,$this->pdf->getY()+1,2,2,'F','',$kleur);
            $this->pdf->Cell(50, 4, $this->pdf->portefeuilledata['SpecifiekeIndex'], 0, 0, "L");
          }
        }

		  }
		 
  }
  
  
  function printPie($pieData,$kleurdata,$title='',$width=100,$height=100)
  {
    
    $col1=array(255,0,0); // rood
    $col2=array(0,255,0); // groen
    $col3=array(255,128,0); // oranje
    $col4=array(0,0,255); // blauw
    $col5=array(255,255,0); // geel
    $col6=array(255,0,255); // paars
    $col7=array(128,128,128); // grijs
    $col8=array(128,64,64); // bruin
    $col9=array(255,255,255); // wit
    $col0=array(0,0,0); //zwart
    $standaardKleuren=array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col0);
    // standaardkleuren vervangen voor eigen kleuren.
    $startX=$this->pdf->GetX();
    
    if(isset($kleurdata))
    {
      $grafiekKleuren = array();
      $a=0;
      while (list($key, $value) = each($kleurdata))
      {
        if ($value['R']['value'] == 0 && $value['G']['value'] == 0 && $value['B']['value'] == 0)
          $grafiekKleuren[]=$standaardKleuren[$a];
        else
          $grafiekKleuren[] = array($value['R']['value'],$value['G']['value'],$value['B']['value']);
        $pieData[$key] = $value['percentage'];
        $a++;
      }
    }
    else
      $grafiekKleuren = $standaardKleuren;
    
    while (list($key, $value) = each($pieData))
      if ($value < 0)
        $pieData[$key] = -1 * $value;
    
    //$this->pdf->SetXY(210, $this->pdf->headerStart);
    $y = $this->pdf->getY();
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->setXY($startX,$y-4);
    //	$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    
    $this->pdf->Cell($width+5,4,vertaalTekst($title, $this->pdf->rapport_taal),0,0,"C");
    $this->pdf->setXY($startX,$y);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    
    $this->pdf->setX($startX);
    $this->PieChart($width, $height, $pieData, '%l (%p)', $grafiekKleuren);
    $hoogte = ($this->pdf->getY() - $y) + 8;
    $this->pdf->setY($y);
    
    $this->pdf->SetLineWidth($this->pdf->lineWidth);
    $this->pdf->setX($startX);
    
    //	$this->pdf->Rect($startX,$this->pdf->getY(),$width,$hoogte);
    
  }
  
  function PieChart($w, $h, $data, $format, $colors=null)
  {
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->SetLegends($data,$format);
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 4;
    $hLegend = 2;
    $radius = min($w - $margin * 4 - $hLegend - $this->pdf->wLegend, $h - $margin * 2);
    $radius=min($w,$h);
    
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
    $aantal=count($data);
    foreach($data as $val)
    {
      $angle = floor(($val * 360) / doubleval($this->pdf->sum));
      
      if ($angle != 0)
      {
        $angleEnd = $angleStart + $angle;
        
        $avgAngle=($angleStart+$angleEnd)/360*M_PI;
        $factor=1.5;
        
        if($i==($aantal-1))
          $angleEnd=360;
        
        //  echo " $angle $angleStart + $angleEnd = ".(($angleStart+$angleEnd)/2)." ".$this->pdf->legends[$i]." | cos:".cos($avgAngle)." | sin:".sin($avgAngle)."  <br>\n";
        $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
        $this->pdf->Sector($XDiag+(sin($avgAngle)*$factor), $YDiag-(cos($avgAngle)*$factor), $radius, $angleStart, $angleEnd);
        $angleStart += $angle;
      }
      $i++;
    }
    //   if ($angleEnd != 360) {
    //      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    //  }
    
    //Legends
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
    $x1 = $XPage ;
    $x2 = $x1 + $hLegend + $margin;
    $y1 = $YDiag + ($radius) + $margin;
    
    for($i=0; $i<$this->pdf->NbVal; $i++) {
      $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
      $this->pdf->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
      $this->pdf->SetXY($x2,$y1);
      $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
      $y1+=$hLegend + 1;
    }
    
  }
  
  function SetLegends($data, $format)
  {
    $this->pdf->legends=array();
    $this->pdf->wLegend=0;
    
    $this->pdf->sum=array_sum($data);
    
    $this->pdf->NbVal=count($data);
    foreach($data as $l=>$val)
    {
      //$p=sprintf('%.1f',$val/$this->sum*100).'%';
      $p=sprintf('%.1f',$val).'%';
      $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
      $this->pdf->legends[]=$legend;
      $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->pdf->wLegend);
    }
  }
  
  
function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;

    $legendDatum= $data['Datum'];
    $data1 = $data['benchmarkIndex'];
    $data = $data['Index'];
    if(count($data1)>0)
      $bereikdata = array_merge($data,$data1);
    else
      $bereikdata =   $data;

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $w/12 );

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(0,38,84);
    $this->pdf->SetLineWidth(0.2);

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

    if ($maxVal == 0)
    {
      $maxVal = ceil(max($bereikdata));
      if ($maxVal < 0)
        $maxVal = 1;
    }
    if ($minVal == 0)
    {
      $minVal = floor(min($bereikdata));
      if ($minVal > 0)
        $minVal =-1;
    }

    $minVal = floor(($minVal-1) * 1.1);
    $maxVal = ceil(($maxVal+1) * 1.1);
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / count($data);

    if($jaar && count($data) < 12)
      $unit = $lDiag / 12;

    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
    {
      $xpos = $XDiag + $verInterval * $i;
    }

    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);

    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);

    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);

    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $n=0;
    for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
       break;
    }

    $n=0;
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");

      $n++;
      if($n >20)
         break;
    }

    //datum onder grafiek
    /*
    $datumStart = db2jul($legendDatum[0]);
    $datumStart = vertaalTekst($__appvar["Maanden"][date("n",$datumStart)],$pdf->rapport_taal).' '.date("Y",$datumStart);
    $datumStop  =  db2jul($legendDatum[count($legendDatum)-1])+86400;
    $datumStop  = vertaalTekst($__appvar["Maanden"][date("n",$datumStop)],$pdf->rapport_taal).' '.date("Y",$datumStop);
    $ypos = $YDiag + $hDiag + $margin*2;
    $xpos = $XDiag;
    $this->pdf->Text($xpos, $ypos,$datumStart);
    $xpos = $XPage+$w - $this->pdf->GetStringWidth($datumStop);
    $this->pdf->Text($xpos, $ypos,$datumStop);
*/
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    //listarray($data);
   // $color=array(200,0,0);
   
    $aantal=count($data);
    $legendaStep=ceil($aantal/12);

    for ($i=0; $i<count($data); $i++)
    {
      $extrax=($unit*0.1*-1);
      if($i <> 0)
        $extrax1=($unit*0.1*-1);
        
      if($i%$legendaStep==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-10+$unit,$YDiag+$hDiag+8,jul2form(db2jul($legendDatum[$i])),25);

      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit+$extrax1, $yval, $XDiag+($i+1)*$unit+$extrax, $yval2,$lineStyle );
    //  $this->pdf->Rect($XDiag+($i+1)*$unit-0.5+$extrax, $yval2-0.5, 1, 1 ,'F','',$color);
      
     // if($data[$i] <> 0)
     //   $this->pdf->Text($XDiag+($i+1)*$unit-1+$extrax,$yval2-2.5,$this->formatGetal($data[$i],1));
     
      
      $yval = $yval2;
    }

    if(is_array($data1))
    {
     // listarray($data1);
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color1);
      for ($i=0; $i<count($data1); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
      //  $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color1);
        
    //    $this->pdf->Text($XDiag+($i+1)*$unit-1,$yval2-2.5,$this->formatGetal($data1[$i],1));
         
        $yval = $yval2;
      }
    }
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width'=>0.1));
    $this->pdf->SetFillColor(0,0,0);
  }

}
?>
<?
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/07/20 16:28:44 $
 		File Versie					: $Revision: 1.20 $

 		$Log: RapportPERFG_L51.php,v $
 		Revision 1.20  2019/07/20 16:28:44  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2019/02/23 18:32:59  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2018/10/24 16:00:59  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2018/10/17 07:48:11  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2018/02/07 17:22:29  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2017/03/25 16:01:09  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2016/08/29 06:41:28  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2016/08/27 16:26:45  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2015/12/30 19:01:23  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2015/12/20 16:46:36  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2014/07/19 14:27:59  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2014/06/08 07:56:13  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2014/05/10 13:54:39  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2014/05/05 15:52:25  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/05/04 10:55:50  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2014/04/26 16:43:08  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/02/02 10:49:59  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/01/08 16:52:37  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/11/13 15:47:34  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/09/18 15:23:07  rvv
 		*** empty log message ***
 		
 		

*/

include_once('../indexBerekening.php');


class RapportPERFG_L51
{
  function RapportPERFG_L51($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		if($this->pdf->rapport_PERFGRAFIEK_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_PERFG_titel;
		else
			$this->pdf->rapport_titel = "Historisch rendement vanaf aanvang";


		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


	function getFondsKoers($fonds,$datum)
	{
	  $db=new DB();
	  $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
	  $db->SQL($query);
	  $koers=$db->lookupRecord();
	  return $koers['Koers'];
	}

function fondsPerformance($fonds,$vanaf,$tot)
{
 
  $indexData=array('fondsKoers_eind'=>$this->getFondsKoers($fonds,$tot),
                    'fondsKoers_begin'=>$this->getFondsKoers($fonds,$vanaf));
                    
  $perf=($indexData['fondsKoers_eind'] - $indexData['fondsKoers_begin']) / ($indexData['fondsKoers_begin']/100 );

  //echo "t $fonds $totalPerf  $vanaf,$tot <br>\n";
  if($perf<>0)
    return formatGetal($perf,1)."%";
}


  function writeRapport()
  {
    global $__appvar;
    $query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.kleurcode, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder,
Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client,
Vermogensbeheerders.grafiek_kleur
FROM Portefeuilles
JOIN Clienten ON Portefeuilles.Client = Clienten.Client
JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
WHERE Portefeuille = '" . $this->portefeuille . "' ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $this->portefeuilledata = $DB->nextRecord();
    $this->portefeuilledata['kleurcode'] = unserialize($this->portefeuilledata['kleurcode']);
    $this->alleKleuren=unserialize($this->portefeuilledata['grafiek_kleur']);
  
    $mogelijkeKleuren=array();//array(array(132,158,173),array(190,190,190),array(206,215,222));
    $aanwezigeKleuren=array();//array('132158173','190190190','206215222');
    foreach($this->alleKleuren as $soort=>$categorieData)
    {
      if($soort!='uit')
      {
        foreach ($categorieData as $cat => $kleurData)
        {
          if ($kleurData['R']['value'] <> 0 && $kleurData['G']['value'] <> 0 && $kleurData['B']['value'] <> 0)
          {
            $kleurString = $kleurData['R']['value'] . $kleurData['G']['value'] . $kleurData['B']['value'];
            if (in_array($kleurString, $aanwezigeKleuren))
            {
              continue;
            }
            $aanwezigeKleuren[] = $kleurString;
          
            $mogelijkeKleuren[] = array($kleurData['R']['value'], $kleurData['G']['value'], $kleurData['B']['value']);
          }
        }
      }
    }
    $this->mogelijkeKleuren=$mogelijkeKleuren;
  
    $query = "SELECT Portefeuilles.Vermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Portefeuille, Portefeuilles.Startdatum, " .
        " Portefeuilles.Einddatum, Portefeuilles.Client, Portefeuilles.Depotbank, Portefeuilles.RapportageValuta, Vermogensbeheerders.attributieInPerformance, " .
        " Clienten.Naam, Portefeuilles.ClientVermogensbeheerder FROM (Portefeuilles, Clienten ,Vermogensbeheerders)  WHERE " .
        " Portefeuilles.Client = Clienten.Client AND Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder" .
        " AND Portefeuilles.Portefeuille = '$this->portefeuille' ";
      $DB->SQL($query);
      $pdata = $DB->lookupRecord();
    if(substr($this->rapportageDatum, 0, 4) == substr($this->rapportageDatumVanaf, 0, 4))
    {
      if (db2jul($pdata['Startdatum']) <= db2jul(substr($this->rapportageDatum, 0, 4) . '-01-01'))
      {
        $this->rapportageDatumVanaf = substr($this->rapportageDatum, 0, 4) . '-01-01';
      }
      else
      {
        $this->rapportageDatumVanaf = substr($pdata['Startdatum'], 0, 10);
      }
    }
  
  
    $extraIndices=array();
    $extraIndicesPerformance=array();
    $extraIndicesTmp=array();
    foreach($this->pdf->lastPOST as $key=>$value)
    {
      if(substr($key,0,8)=='mmIndex_')
      {
        $extraIndices[]=$value;
        $extraIndicesTmp[$value]=0;
      }
    }
    $this->extraIndices=$extraIndices;
    $db=new DB();
    $n=0;
    foreach($extraIndices as $index=>$fonds)
    {
      if (!isset($fondsOmschrijvingen[$fonds]))
      {
        $query = "SELECT Fondsen.Omschrijving, BeleggingscategoriePerFonds.grafiekKleur FROM Fondsen LEFT JOIN BeleggingscategoriePerFonds ON Fondsen.Fonds=BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder='" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "' WHERE Fondsen.Fonds='$fonds' ";
        $db->SQL($query);
        $kleurData = $db->lookupRecord();
        $fondsOmschrijvingen[$fonds] = $kleurData['Omschrijving'];
        $extraIndicesKleur[$fonds] = $this->mogelijkeKleuren[$n];
        $tmp = unserialize($kleurData['grafiekKleur']);
        if (is_array($tmp))
        {
          $extraIndicesKleur[$fonds] = array($tmp['R']['value'], $tmp['G']['value'], $tmp['B']['value']);
        }
        else
        {
          $extraIndicesKleur[$fonds] = $this->mogelijkeKleuren[$n];
        }
        $extraLegenda[$kleurData['Omschrijving']] = $extraIndicesKleur[$fonds];
        $indexFondsenOmschrijving[$fonds]=$kleurData['Omschrijving'];
        $n++;
      }
    }
    $this->extraLegenda=$extraLegenda;
    $this->extraIndicesKleur=$extraIndicesKleur;
    foreach($extraIndicesKleur as $fonds=>$kleur)
      $indexKleuren[$fonds]=array('R'=>array('value'=>$kleur[0]),'G'=>array('value'=>$kleur[1]),'B'=>array('value'=>$kleur[2]));

    
    $this->berekening = new rapportATTberekening($pdata);
    $this->berekening->getAttributieCategorien();
    $this->berekening->pdata['pdf'] = true;
    $this->berekening->attributiePerformance($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, 'rapportagePeriode', $this->pdf->rapportageValuta, 'maand');
    unset($this->berekening->performance['rapportagePeriode']);

    foreach ($this->berekening->performance as $periode => $periodeData)
    {
      foreach ($periodeData['totaalWaarde'] as $categorie => $waarden)
      {
        if ($waarden['eind'] <> 0)
        {
          $categorieTonen[$categorie] = $categorie;
        }
      }
    }
  
    $laatsteWaarde=array();
    foreach ($this->berekening->categorien as $categorie)
    {
      if (in_array($categorie, $categorieTonen))
      {
        $indexData[$categorie][$this->rapportageDatumVanaf]['portefeuille'] = 100;
        $laatsteWaarde[$categorie] = 100;
      }
    }
  

    foreach ($this->berekening->performance as $periode => $periodeData)
    {
      $datum=substr($periode,11,10);
      foreach($extraIndices as $index)
      {
        $perf=getFondsPerformance($index,substr($periode,0,10),$datum);
        $extraIndicesPerformance[$datum][$index]=((1+$extraIndicesTmp[$index]/100)*(1+$perf/100)-1)*100;
        $extraIndicesTmp[$index]=$extraIndicesPerformance[$datum][$index];
      }
      
      $van = substr($periode, 0, 10);
      $tot = substr($periode, 11, 10);
      $perioden[] = array('van' => $van, 'tot' => $tot);
      foreach ($periodeData['totaal']['performance'] as $categorie => $performance)
      {
        if (in_array($categorie, $categorieTonen))
        {
          //echo "$van  $tot  $categorie  $performance<br>\n";
          $indexData[$categorie][$tot]['portefeuille'] = ($laatsteWaarde[$categorie]) * (100 + $performance) / 100;
  
          $laatsteWaarde[$categorie]=$indexData[$categorie][$tot]['portefeuille'];
        }
      }
      foreach($extraIndicesPerformance[$datum] as $indexFonds=>$indexRendement)
          $indexData['Totaal'][$tot][$indexFonds] = $extraIndicesPerformance[$datum][$indexFonds]=$indexRendement+100;
      //  $indexData['Totaal'][$tot]['extraIndices'][$indexFonds] = $extraIndicesPerformance[$datum][$indexFonds]=$indexRendement+100;
    }

//listarray($this->berekening->performance);

    if ($this->portefeuilledata['SpecifiekeIndex'] != '')
    {
      $lookupDB = new DB();
      $lookupQuery = "SELECT Fondsen.Omschrijving FROM Fondsen WHERE Fondsen.Fonds = '" . $this->portefeuilledata['SpecifiekeIndex'] . "'";
      $lookupDB->SQL($lookupQuery);
      $lookupRec = $lookupDB->lookupRecord();
      $indexFondsen['Totaal'] = $this->portefeuilledata['SpecifiekeIndex'];
      $indexNaam[$this->portefeuilledata['SpecifiekeIndex']] = $lookupRec['Omschrijving'];
      $indexFondsenOmschrijving['Totaal'] = $lookupRec['Omschrijving'];
    }
  
    $query = "SELECT Indices.Beursindex ,Indices.grafiekKleur
          FROM Indices
          WHERE Indices.Vermogensbeheerder = '" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'  ORDER BY Indices.Afdrukvolgorde  ";
  
    $DB->SQL($query);
    $DB->Query();
    while ($data = $DB->nextRecord())
    {
      $indexKleuren[$data['Beursindex']] = unserialize($data['grafiekKleur']);
    }
  
    /*
    $query="SELECT IndexPerAttributieCategorie.Fonds,Fondsen.Omschrijving,IndexPerAttributieCategorie.AttributieCategorie,Indices.grafiekKleur
    FROM IndexPerAttributieCategorie
    LEFT JOIN Indices ON IndexPerAttributieCategorie.Fonds=Indices.Beursindex AND Indices.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
    LEFT JOIN Fondsen ON IndexPerAttributieCategorie.Fonds=Fondsen.Fonds
    WHERE IndexPerAttributieCategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    */
    $query = "SELECT
IndexPerBeleggingscategorie.Fonds,
Fondsen.Omschrijving,IndexPerBeleggingscategorie.Categorie as AttributieCategorie,
Indices.grafiekKleur 
FROM IndexPerBeleggingscategorie 
LEFT JOIN Indices ON IndexPerBeleggingscategorie.Fonds=Indices.Beursindex AND Indices.Vermogensbeheerder = '" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'
LEFT JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds=Fondsen.Fonds 
WHERE IndexPerBeleggingscategorie.Categoriesoort='Attributiecategorien' AND IndexPerBeleggingscategorie.Vermogensbeheerder='" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'
AND (IndexPerBeleggingscategorie.Portefeuille='' OR IndexPerBeleggingscategorie.Portefeuille='$this->portefeuille')
ORDER BY AttributieCategorie, IndexPerBeleggingscategorie.Portefeuille asc";
    $DB->SQL($query);
    $DB->Query();
    while ($data = $DB->nextRecord())
    {
      $indexFondsen[$data['AttributieCategorie']] = $data['Fonds'];
      $indexFondsenOmschrijving[$data['AttributieCategorie']] = $data['Omschrijving'];
      $indexKleuren[$data['Fonds']] = unserialize($data['grafiekKleur']);
    }
  
  
    $query = "SELECT BeleggingscategoriePerFonds.grafiekKleur, BeleggingscategoriePerFonds.Fonds
          FROM  BeleggingscategoriePerFonds
          WHERE BeleggingscategoriePerFonds.Vermogensbeheerder = '" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "' AND BeleggingscategoriePerFonds.Fonds IN('" . implode("','", $indexFondsen) . "') ";
    $DB->SQL($query);
    $DB->Query();
    while ($data = $DB->nextRecord())
    {
      if ($data['grafiekKleur'] != '')
      {
        $indexKleuren[$data['Fonds']] = unserialize($data['grafiekKleur']);
      }
    }
  
    foreach ($indexData as $categorie => $periodeData)
    {
      $fonds = $indexFondsen[$categorie];
      $indexTabel['cumulatief'][$fonds]['cumulatief'] = 100;
      $vorigeDatum = '';
      foreach ($periodeData as $datum => $waarden)
      {
        if ($vorigeDatum != '')
        {
          $start = $vorigeDatum;
          $eind = $datum;
          $fonds = $indexFondsen[$categorie];
        
          $q0 = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '" . $eind . "' AND Fonds = '$fonds'  ORDER BY Datum DESC LIMIT 1";
          $q1 = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '" . $start . "' AND Fonds = '$fonds'  ORDER BY Datum DESC LIMIT 1";
          $DB->SQL($q0);
          $DB->Query();
          $koersEind = $DB->LookupRecord();
          $DB->SQL($q1);
          $DB->Query();
          $koersStart = $DB->LookupRecord();
          $perf = $koersEind['Koers'] / $koersStart['Koers'];
          if ($perf == 0)
          {
            $perf = 1;
          }
//      $indexWaarden[$id]['fondsPerf'][$fonds] = $perf  ;

//      if(empty($indexWaarden[$id-1]['fondsIndex'][$fonds]))
//        $indexWaarden[$id]['fondsIndex'][$fonds] = $indexWaarden[$id]['fondsPerf'][$fonds];
//      else
// 	      $indexWaarden[$id]['fondsIndex'][$fonds]  =($indexWaarden[$id]['fondsPerf'][$fonds]*$indexWaarden[$id-1]['fondsIndex'][$fonds]);
        
          if (empty($indexTabel['cumulatief'][$fonds]['cumulatief']))
          {
            $indexTabel['cumulatief'][$fonds]['cumulatief'] = 100;
          }
        
          $indexTabel['cumulatief'][$fonds]['cumulatief'] = ($indexTabel['cumulatief'][$fonds]['cumulatief'] * ($perf * 100)) / 100;
          $indexData[$categorie][$datum]['index'] = (($indexTabel['cumulatief'][$fonds]['cumulatief']));
  
          if($categorie=='Totaal')
          {
  

  
          //  listarray($waarden);exit;
          //  $extraLegenda
          //  $indexData[$categorie][$datum]['extraIndices'] = $waarden['extraIndices'];
            foreach($waarden['extraIndices'] as $fonds=>$rendement)
              $indexData[$categorie][$datum][$fonds] = $rendement;
          }
        }
        else
        {
          $indexData[$categorie][$datum]['index'] = 100;
          if($categorie=='Totaal')
          {
            foreach($extraIndices as $extraFonds)
            {
              //$indexData[$categorie][$datum]['extraIndices'][$extraFonds]=100;
              $indexData[$categorie][$datum][$extraFonds]=100;
            }
          }
        }

        
        $vorigeDatum = $datum;
      }
    }
  
    $this->pdf->AddPage();
    $this->pdf->templateVars['PERFGPaginas'] = $this->pdf->page;
    $this->pdf->templateVarsOmschrijving['PERFGPaginas'] = $this->pdf->rapport_titel;
    $this->pdf->SetLineStyle(array('color' => array(0, 0, 0), 'dash' => 0));
   
    $keysIndexFondsen=array_keys($indexFondsen);

  //listarray($indexData);
    foreach($indexData as $categorie=>$data)
    {
      if(!in_array($categorie,$keysIndexFondsen) && $categorie<>'Totaal')
      {
        unset($indexData[$categorie]);
      }
    }
   // listarray($indexData);
    $keys=array_keys($indexData);
    if(count($keys)>5)
    {
      foreach($keys as $n=>$categorie)
      {
        if($n<4)
        {
          $pagina1[$categorie] = $indexData[$categorie];
          if($n==0)
            $pagina2[$categorie]=$indexData[$categorie];
        }
        else
        {
          $pagina2[$categorie]=$indexData[$categorie];
        }
      }
  
      $this->perfgPage($pagina1, $indexFondsenOmschrijving, $indexKleuren, $indexFondsen);
      $this->pdf->AddPage();
      $this->perfgPage($pagina2, $indexFondsenOmschrijving, $indexKleuren, $indexFondsen);
  
      $this->perfgGrafieken($indexData, $indexFondsenOmschrijving, $indexKleuren, $indexFondsen);
    }
    else
    {
      
      $this->perfgPage($indexData, $indexFondsenOmschrijving, $indexKleuren, $indexFondsen);
      $this->perfgGrafieken($indexData, $indexFondsenOmschrijving, $indexKleuren, $indexFondsen);
      
    }
  }
  function perfgPage($indexData,$indexFondsenOmschrijving,$indexKleuren,$indexFondsen)
  {
  
    $lines = array();
    $header = array();
    $header[] = '';
    $header1[] = vertaalTekst('Datum', $this->pdf->rapport_taal);
    $omschrijvingen = $this->berekening->categorieOmschrijving;
    $omschrijvingen['Totaal'] = 'Totaal';
    foreach ($indexData as $categorie => $datumData)
    {
      if (isset($indexFondsen[$categorie]))
      {
        $header[] = vertaalTekst($omschrijvingen[$categorie], $this->pdf->rapport_taal);
        $header[]='';
        if(count($indexData)<4)
        {
          $header1[] = vertaalTekst('Portefeuille', $this->pdf->rapport_taal);
        }
        else
        {
          $header1[] = vertaalTekst('Port.', $this->pdf->rapport_taal);
        }
        $header1[] = vertaalTekst($indexFondsenOmschrijving[$categorie], $this->pdf->rapport_taal);
        $header1[] = vertaalTekst('Verschil', $this->pdf->rapport_taal);
        $header1[] = '';
      }
    }
    $kwartaalMomenten = array('03-31', '06-30', '09-30', '12-31');
    foreach ($indexData as $categorie => $datumData)
    {
      if (isset($indexFondsen[$categorie]))
      {
        if (count($datumData) > 15)
        {
          $kwartalen = true;
        }
        else
        {
          $kwartalen = false;
        }
        foreach ($datumData as $datum => $waarden)
        {
          $dag = substr($datum, 5, 5);
          if ($kwartalen == false || in_array($dag, $kwartaalMomenten) || $datum == $this->rapportageDatum)
          {
          
            if (!isset($lines[$datum]))
            {
              $lines[$datum][] = $datum;
            }
            $lines[$datum][] = $this->formatGetal($waarden['portefeuille'], 1);
            $lines[$datum][] = $this->formatGetal($waarden['index'], 1);
            $lines[$datum][] = $this->formatGetal($waarden['portefeuille']-$waarden['index'], 1);
            $lines[$datum][] ='';
          }
        }
      }
    }
  
    $w = (297 - $this->pdf->marge * 2) / 13;
//$w=20;
    $this->pdf->Rect($this->pdf->marge, $this->pdf->GetY(), $w * 13, 12, 'F', null, array($this->pdf->rapport_kop_bgcolor['r'], $this->pdf->rapport_kop_bgcolor['g'], $this->pdf->rapport_kop_bgcolor['b']));
    $this->pdf->SetDrawColor($this->pdf->rapport_kop_fontcolor[r], $this->pdf->rapport_kop_fontcolor[g], $this->pdf->rapport_kop_fontcolor[b]);
    $this->pdf->Line($this->pdf->marge, $this->pdf->GetY() + 12, 297 - $this->pdf->marge, $this->pdf->GetY() + 12);
    $this->pdf->SetDrawColor(0);
    unset($this->pdf->fillCell);
  
    if(count($indexData)<4)
    {
      $dw = 18;
      $pw = 18;
      $bw = 28;
      $vw = 15;
      $tw = 5;
    }
    else
    {
      $dw = 16;
      $pw = 10;
      $bw = 28;
      $vw = 12;
      $tw = 3.5;
    }
    
    $this->pdf->SetWidths(array($dw,  $pw+$bw+$vw,  $tw,   $pw+$bw+$vw, $tw, $pw+$w+$vw, $tw, $pw+$bw+$vw, $tw,  $pw+$bw+$vw, $tw,  $pw+$bw+$vw));
    $this->pdf->setAligns(array('L', 'C', 'C', 'C', 'L',  'C', 'C', 'C',  'L', 'C', 'C', 'C',  'L', 'C', 'C', 'C',  'L', 'C', 'C', 'C',));
  
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'], $this->pdf->rapport_kop_fontcolor['g'], $this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'], $this->pdf->rapport_kop_bgcolor['g'], $this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $dataStartY = $this->pdf->GetY() + $this->pdf->rowHeight * 3;
    $this->pdf->Row($header);
  
   
    $this->pdf->setAligns(array('R', 'R', 'R', 'R', 'L',  'R', 'R', 'R', 'L', 'R', 'R', 'R', 'L', 'R', 'R', 'R', 'L', 'R', 'R', 'R'));
    $this->pdf->SetWidths(array($dw, $pw,$bw,$vw, $tw, $pw,$bw,$vw, $tw, $pw,$bw,$vw, $tw, $pw,$bw,$vw, $tw, $pw,$bw,$vw));
    $this->pdf->Row($header1);
//$this->pdf->SetWidths(array($w,$w,$w,$w,$w,$w,$w,$w,$w,$w));
    unset($this->pdf->fillCell);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'], $this->pdf->rapport_fontcolor['g'], $this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetY($dataStartY);
    foreach ($lines as $line => $data)
    {
      $this->pdf->Row($data);
    }
  
  }
  function perfgGrafieken($indexData,$indexFondsenOmschrijving,$indexKleuren,$indexFondsen)
  {
    $this->pdf->addPage();

    $omschrijvingen = $this->berekening->categorieOmschrijving;
    $omschrijvingen['Totaal'] = 'Totaal';
    $tmp=array();

foreach($indexData as $categorie=>$periodeData)
{
  foreach($periodeData as $datum=>$waarden)
  {
    $tmp[$categorie]['portefeuille'][$datum]=$waarden['portefeuille'];
    $tmp[$categorie][$indexFondsenOmschrijving[$categorie]][$datum]=$waarden['index'];
    
    if(is_array($waarden['extraIndices']))
      $tmp[$categorie]['extraIndices'][$datum]=$waarden['extraIndices'];
    if($categorie=='Totaal')
      foreach($this->extraIndices as $fonds)
         $tmp[$categorie][$indexFondsenOmschrijving[$fonds]][$datum]=$waarden[$fonds];
      
  }
}
 //$portefeuilleKleur=$this->portefeuilledata['kleurcode'];

 $portefeuilleKleur=array(50,50,155);
/*
  $colors=array('portefeuille'=>$portefeuilleKleur,$indexFondsenOmschrijving['Totaal']=>array($indexKleuren[$indexFondsen['Totaal']]['R']['value'],
                                                                  $indexKleuren[$indexFondsen['Totaal']]['G']['value'],
                                                                  $indexKleuren[$indexFondsen['Totaal']]['B']['value']));

$this->LineDiagram(215,43,65,50,$tmp['Totaal'],'Totaal',$colors);
*/
//unset($tmp['Totaal']);
$w=0;
$y=0;
$n=0;
foreach($tmp as $categorie=>$data)
{
  if(isset($indexFondsen[$categorie]))
  {
    if($n<>0 && $n%2==0)
    {
      $w = 0;
      $y += 75;
    }
  
   // echo "$categorie $y <br>\n";ob_flush();
    if($y>149)
    {
      $this->pdf->addPage();
      $y=0;
    }
    
    $colors=array('portefeuille'=>$portefeuilleKleur,
                  $indexFondsenOmschrijving[$categorie]=>array($indexKleuren[$indexFondsen[$categorie]]['R']['value'],
                                                               $indexKleuren[$indexFondsen[$categorie]]['G']['value'],
                                                               $indexKleuren[$indexFondsen[$categorie]]['B']['value']));
      
   // if(isset($indexFondsen[$categorie]['extraIndices']))
  //    $colors['extraIndices']=array(200,100,100);
    if($categorie=='Totaal')
      foreach($this->extraIndices as $fonds)
        $colors[$indexFondsenOmschrijving[$fonds]]=array($indexKleuren[$fonds]['R']['value'],$indexKleuren[$fonds]['G']['value'],$indexKleuren[$fonds]['B']['value']);
      
    
    $this->LineDiagram(20+$w,$y+30,110,50,$data,$omschrijvingen[$categorie],$colors);
    $w+=140;
    $n++;
  }
}  

$this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
$this->pdf->SetFillColor(0,0,0);
$this->pdf->CellBorders = array();
	}
  
 
  
function LineDiagram($x,$y,$w, $h, $data, $title,$colors=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;
    
    //$this->pdf->Rect($x-10,$y-5,$w+20,$h+20);
    $this->pdf->setXY($x,$y);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Multicell($w,4,vertaalTekst($title,$this->pdf->rapport_taal),'','C');
    $this->pdf->setXY($x,$y+4);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    


    $bereikdata =   $data;

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $w/12 );



    if($color == null)
      $color=array(0,0,0);
    

    $this->pdf->SetLineWidth(0.2);
    
    
       $this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'F','',array($this->pdf->rapport_kop_bgcolor['r'],
                                                                     $this->pdf->rapport_kop_bgcolor['g'],
                                                                     $this->pdf->rapport_kop_bgcolor['b']));


    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

      $maxVal=98;
      $minVal=102;
    $aantalMaanden=0;

      foreach($data as $type=>$maandData)
      {

        if ($type <> '')
        {
          $legendaItems[$type] = $type;
        }
        $tmp = count($maandData);
        if ($tmp > $aantalMaanden)
        {
          $aantalMaanden = $tmp;
        }
        foreach ($maandData as $maand => $waardeRaw)
        {
          if(!is_array($waardeRaw))
            $waardeArray=array($waardeRaw);
          else
            $waardeArray=$waardeRaw;
          foreach($waardeArray as $index =>$waarde)
          {
            if ($waarde > $maxVal)
            {
              $maxVal = $waarde;
            }
            if ($waarde < $minVal)
            {
              $minVal = $waarde;
            }
          }
        }
      }

    $minVal = floor($minVal*0.99 / 5)*5;
    $maxVal = ceil($maxVal*1.01 / 5)*5;
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / $aantalMaanden;



    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
    {
      $xpos = $XDiag + $verInterval * $i;
    }

    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);

    $stapgrootte = (abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);

    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);

    $nulpunt = $YDiag + (($maxVal - $minVal) * $waardeCorrectie); 
    
   // echo "$nulpunt = $YDiag + (($maxVal - $minVal) * $waardeCorrectie); <br>\n";
    $nulLijn= $YDiag + (($maxVal-100) * $waardeCorrectie); 
    $offset=100-$minVal;//($maxVal - $minVal)-100;
    //$this->pdf->Line($XDiag, $nulLijn, $XPage+$w ,$nulLijn,array('dash' => 1,'color'=>array(0,0,0)));
    
    $n=0;
    for($i=$nulLijn; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,68,106)));
      $yGetal=$offset-($n*$stapgrootte)+$minVal;
      if($yGetal>=$minVal)
      {
       
      $this->pdf->Text($XDiag-8, $i, $this->formatGetal($yGetal,1) ." %");
      $this->pdf->Text($XDiag+$w, $i, $this->formatGetal($yGetal-100,1) ." %");
      }
      $n++;
      if($n >20)
       break;
    }

    $n=0;
    for($i=$nulLijn; $i >= $top; $i-= $absUnit*$stapgrootte)
    {

      
      if($skipNull == true)
        $skipNull = false;
      else
      {
        $yGetal=$offset-(-1*$n*$stapgrootte)+$minVal;
        if($yGetal<=$maxVal)
        {
          $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,68,106)));
          $this->pdf->Text($XDiag-8, $i,$this->formatGetal($yGetal,1)." %");
          $this->pdf->Text($XDiag+$w, $i,$this->formatGetal($yGetal-100,1)." %");
        }
      }
      $n++;      
      if($n >20)
         break;
    }

    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
  
   // $color=array(200,0,0);
   
      //  $colors=array('attributieEffect'=>array(0,52,121),'allocateEffect'=>array(87,165,25),'selectieEffect'=>array(108,31,128));

    //for ($i=0; $i<count($data); $i++)
    foreach($data as $type=>$maandData)
    {
      $i=0;
      $color=$colors[$type];
      
      if($type=='portefeuille')
        $lineWith=0.9*0.75;
      else
        $lineWith=0.5*0.5;  
 
      $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => $lineWith, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);

      $skipCounter=ceil($aantalMaanden/12);
  
     // echo $skipCounter;exit;
      
      foreach($maandData as $maand=>$waarde)
      {
        if($i%$skipCounter==0)
        {
          if(substr($maand,5,5)=='01-01')
            $maand=(substr($maand,0,4)-1).'-12-31';
          $this->pdf->TextWithRotation($XDiag+($i)*$unit-2+$unit,$YDiag+$hDiag+5,date('M',db2jul($maand)),0);
        }
        if(!is_array($waarde))
        {
          $yval2 = $YDiag + (($maxVal - $waarde) * $waardeCorrectie);
        }
        else
        {
          foreach($waarde as $extraB)
          {
            $yval2 = $YDiag + (($maxVal - $extraB) * $waardeCorrectie);
          }
        }
        
        if($i <> 0)
        {
          $this->pdf->line($XDiag+$i*$unit+$extrax1, $yval, $XDiag+($i+1)*$unit+$extrax, $yval2,$lineStyle );
        }
        $yval = $yval2;
        $i++;
      }
    }
    
    //$legendaItems=array('portefeuille','benchmark');
    $step=0;
    $aantal=count($legendaItems);
    foreach ($legendaItems as $index=>$item)
    {
    $kleur=$colors[$item];
    $this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
    $this->pdf->Rect($XPage+$step, $YPage+$h+10, 3, 3, 'DF','',$kleur);
    $this->pdf->SetXY($XPage+3+$step,$YPage+$h+10);
    $this->pdf->Cell(0,3,vertaalTekst($item,$this->pdf->rapport_taal));
    $step+=($w/$aantal);
    }
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
    
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }

//listarray($indexWaarden);
//listarray($tmp);
}
?>
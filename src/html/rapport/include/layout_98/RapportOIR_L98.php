<?php
/*
// WORDT OOK GEBRUIKT IN L93 L94 L96 L98 en L101
*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"] . "/html/rapport/rapportVertaal.php");

class RapportOIR_L98
{
  function RapportOIR_L98($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = $pdf;
    $this->pdf->rapport_type = "OIR";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_titel = "Allocaties inclusief uitsplitsing";
    
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
    $this->pdf->underlinePercentage = 0.8;
    if ($this->pdf->lastPOST['debug'])
    {
      $this->debug = true;
    }
    else
    {
      $this->debug = false;
    }
    $this->debugData = array();
    $this->normaleVerdeling = false;
  }
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde, $dec, ",", ".");
  }
  
  function formatGetalKoers($waarde, $dec, $start = false)
  {
    if ($start == false)
    {
      $waarde = $waarde / $this->pdf->ValutaKoersEind;
    }
    else
    {
      $waarde = $waarde / $this->pdf->ValutaKoersStart;
    }
    
    return number_format($waarde, $dec, ",", ".");
  }
  
  
  function bepaalWegingPerFonds($fonds, $doorkijkSoort, $airsCategorie, $waarde, $rekening)
  {
    $db = new DB();
    $query = "SELECT MAX(datumVanaf) as vanafDatum FROM doorkijk_categorieWegingenPerFonds
WHERE fonds='" . mysql_real_escape_string($fonds) . "' AND msCategoriesoort='" . mysql_real_escape_string($doorkijkSoort) . "' AND  datumVanaf <= '" . $this->rapportageDatum . "' ";
    $db->executeQuery($query);
    $vanafDatum = $db->nextRecord();//listarray($query);
    
    $query = "SELECT msCategorie,weging FROM doorkijk_categorieWegingenPerFonds
WHERE fonds='" . mysql_real_escape_string($fonds) . "' AND msCategoriesoort='" . mysql_real_escape_string($doorkijkSoort) . "' AND datumVanaf = '" . $vanafDatum['vanafDatum'] . "'  ";
    $db->executeQuery($query);
    $wegingPerMsCategorie = array();
    while ($row = $db->nextRecord())
    {
      $wegingPerMsCategorie[$row['msCategorie']] = $row['weging'];
      if ($this->debug)
      {
        $this->debugData['MSfondsWeging'][$doorkijkSoort][$fonds][$row['msCategorie']]['weging'] = $row['weging'];
        $this->debugData['MSfondsWeging'][$doorkijkSoort][$fonds][$row['msCategorie']]['waarde'] = $waarde * $row['weging'] / 100;
      }
    }
//echo $query;
    $wegingDoorkijkCategorie = array();
    $airsKoppelingen = array('REGION_ZOTHERND', 'ZSECTOR_OTHERND');
    if (count($wegingPerMsCategorie) > 0)
    {
      foreach ($airsKoppelingen as $categorie)
      {
        if (isset($wegingPerMsCategorie[$categorie]))
        {
          $query = "SELECT doorkijkCategorie FROM doorkijk_koppelingPerVermogensbeheerder WHERE bronKoppeling='$airsCategorie' AND doorkijkCategoriesoort='" . mysql_real_escape_string($doorkijkSoort) . "' AND systeem='AIRS' AND vermogensbeheerder='" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'";
          $db->executeQuery($query);
          //		listarray($wegingPerMsCategorie);
          //		echo $wegingPerMsCategorie[$categorie]."| $fonds | $airsCategorie | $doorkijkSoort | $query<br>\n";
          
          if ($db->records() > 0)
          {
            
            while ($row = $db->nextRecord())
            {
              $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['weging'] = $wegingPerMsCategorie[$categorie];
              $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['waarde'] = $waarde * $wegingPerMsCategorie[$categorie] / 100;
              
              if ($this->debug)
              {
                
                $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['weging'] = $wegingPerMsCategorie[$categorie];
                $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['waarde'] = $waarde * $wegingPerMsCategorie[$categorie] / 100;
              }
            }
            unset($wegingPerMsCategorie[$categorie]);
          }
        }
      }
      
      $msCategorienWhere = " bronKoppeling IN ('" . implode("','", array_keys($wegingPerMsCategorie)) . "')";
      $query = "SELECT doorkijkCategorie,bronKoppeling as msCategorie FROM doorkijk_koppelingPerVermogensbeheerder
WHERE $msCategorienWhere AND doorkijkCategoriesoort='" . mysql_real_escape_string($doorkijkSoort) . "' AND systeem='MS' AND vermogensbeheerder='" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'";
      $db->executeQuery($query);
      
      while ($row = $db->nextRecord())
      {
        $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['weging'] += $wegingPerMsCategorie[$row['msCategorie']];
        $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['waarde'] += $waarde * $wegingPerMsCategorie[$row['msCategorie']] / 100;
        
        if ($this->debug)
        {
          $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['weging'] += $wegingPerMsCategorie[$row['msCategorie']];
          $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['waarde'] += $waarde * $wegingPerMsCategorie[$row['msCategorie']] / 100;
        }
      }
    }
    else
    {
      $query = "SELECT doorkijkCategorie FROM doorkijk_koppelingPerVermogensbeheerder
WHERE bronKoppeling='$airsCategorie' AND doorkijkCategoriesoort='" . mysql_real_escape_string($doorkijkSoort) . "' AND systeem='AIRS' AND vermogensbeheerder='" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'";
      $db->executeQuery($query);
      
      while ($row = $db->nextRecord())
      {
        $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['weging'] = 100;
        $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['waarde'] = $waarde;
        
        if ($this->debug)
        {
          $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['weging'] = 100;
          $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['waarde'] = $waarde;
        }
      }
    }
    
    
    return $wegingDoorkijkCategorie;
  }
  
  function bepaalWegingNormaal($portefeuille, $doorkijkSoort, $belCategorie = '')
  {
    global $__appvar;
    if ($belCategorie <> '')
    {
      $fondsFilter = "AND Beleggingscategorie='$belCategorie'";
    }
    else
    {
      $fondsFilter = '';
    }
    
    $db = new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal
                  FROM TijdelijkeRapportage
                  WHERE rapportageDatum ='" . $this->rapportageDatum . "' $fondsFilter AND portefeuille = '" . $portefeuille . "'" . $__appvar['TijdelijkeRapportageMaakUniek'];
    $db->SQL($query);
    $db->Query();
    $totaalWaarde = $db->nextRecord();
    
    
    $vertaling = array('Beleggingscategorien' => 'Beleggingscategorie', 'Beleggingssectoren' => 'Beleggingssector', 'Regios' => 'Regio');
    $query = "SELECT TijdelijkeRapportage.type, if(" . $vertaling[$doorkijkSoort] . "Volgorde = 0,127," . $vertaling[$doorkijkSoort] . "Volgorde) as volgorde, sum(actuelePortefeuilleWaardeEuro) as waardeEUR, " . $vertaling[$doorkijkSoort] . " as verdeling , " . $vertaling[$doorkijkSoort] . "Omschrijving as Omschrijving
					FROM TijdelijkeRapportage	WHERE rapportageDatum ='" . $this->rapportageDatum . "'  $fondsFilter AND portefeuille = '" . $portefeuille . "'" . $__appvar['TijdelijkeRapportageMaakUniek'] . "
					GROUP BY " . $vertaling[$doorkijkSoort] . ", TijdelijkeRapportage.type
					ORDER BY volgorde";
    
    $db = new DB();
    $db->SQL($query);
    $db->Query();
    
    $doorkijkVerdeling = array();
    while ($row = $db->nextRecord())
    {
      if($row['type']=='rekening')
        $categorie='Liquiditeiten';
      else
        $categorie = $row['Omschrijving'];
      if ($categorie == '')
      {
          $categorie = 'Geen ' . $vertaling[$doorkijkSoort];
      }
      $totaalPercentage = $row['waardeEUR'] / $totaalWaarde['totaal'] * 100;
      $doorkijkVerdeling['categorien'][$categorie] += $totaalPercentage;
      $doorkijkVerdeling['details'][$categorie]['percentage'] += $totaalPercentage;
      $doorkijkVerdeling['details'][$categorie]['waardeEUR'] += $row['waardeEUR'];
    }
    //listarray($doorkijkVerdeling);
    return $doorkijkVerdeling;
  }
  
  function bepaalWeging($portefeuille,$doorkijkSoort, $belCategorie = '')
  {
    global $__appvar;
    if ($belCategorie <> '')
    {
      $fondsFilter = "AND Beleggingscategorie='$belCategorie'";
    }
    else
    {
      $fondsFilter = '';
    }
    
    $db = new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal
                  FROM TijdelijkeRapportage
                  WHERE rapportageDatum ='" . $this->rapportageDatum . "' $fondsFilter AND portefeuille = '" . $portefeuille . "'" . $__appvar['TijdelijkeRapportageMaakUniek'];
    $db->SQL($query);
    $db->Query();
    $totaalWaarde = $db->nextRecord();
    
    $vertaling = array('Beleggingscategorien' => 'Beleggingscategorie', 'Beleggingssectoren' => 'Beleggingssector', 'Regios' => 'Regio');
    $query = "SELECT fonds,rekening, actuelePortefeuilleWaardeEuro as waardeEUR, " . $vertaling[$doorkijkSoort] . " as airsSoort
					FROM TijdelijkeRapportage	WHERE rapportageDatum ='" . $this->rapportageDatum . "'  $fondsFilter AND portefeuille = '" . $portefeuille . "'" . $__appvar['TijdelijkeRapportageMaakUniek'] .
      " ORDER BY ".$vertaling[$doorkijkSoort]."Volgorde, fonds";
    
    $db = new DB();
    $db->SQL($query); //echo $query."<br>\n";exit;
    $db->Query();
    
    $doorkijkVerdeling = array();
    while ($row = $db->nextRecord())
    {
      if ($row['fonds'] == '' && $doorkijkSoort <> 'Regios' && $doorkijkSoort <> 'Beleggingscategorien')
      {
        $row['fonds'] = $row['rekening'];
        if(isset($this->kleuren[$doorkijkSoort]['Liquiditeiten']))
          $verdeling = array('Liquiditeiten' => array('weging' => 100, 'waarde' => $row['waardeEUR']));
        else
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
      $totaalPercentage = 0;
      if (is_array($verdeling))
      {
        $overige = false;
        $check = 0;
        foreach ($verdeling as $categorie => $percentage)
        {
          $check += $percentage['weging'];
          $totaalPercentage = ($percentage['weging'] * ($row['waardeEUR'] / $totaalWaarde['totaal']));
          $doorkijkVerdeling['categorien'][$categorie] += $totaalPercentage;
          $doorkijkVerdeling['details'][$categorie]['percentage'] += $totaalPercentage;
          $doorkijkVerdeling['details'][$categorie]['waardeEUR'] += $percentage['waarde'];
        }
        if ($check == 0)
        {
          $overige = true;
        }
        elseif (round($check, 5) <> 100)
        {
          if ($this->debug)
          {
            $this->debugData['afwijkingWeging'][$doorkijkSoort][$row['fonds']]['afwijking']['weging'] = $check - 100;
          }
        }
      }
      else
      {
        $overige = true;
      }
      
      if ($overige == true)
      {
        $totaalPercentage = ($row['waardeEUR'] / $totaalWaarde['totaal']) * 100;
        $doorkijkVerdeling['categorien']['Overige'] += $totaalPercentage;
        $doorkijkVerdeling['details']['Overige']['percentage'] += $totaalPercentage;
        $doorkijkVerdeling['details']['Overige']['waardeEUR'] += $row['waardeEUR'];
        
        if ($this->debug)
        {
          $this->debugData['NietGekoppeld'][$doorkijkSoort][$row['fonds']]['Overige']['weging'] = 100;
          $this->debugData['NietGekoppeld'][$doorkijkSoort][$row['fonds']]['Overige']['waarde'] = $row['waardeEUR'];
        }
      }
      if ($this->debug)
      {
        $row['percentage'] = $totaalPercentage;
        $this->debugData['portefeuilleData'][] = $row;
      }
      
    }
    
    return $doorkijkVerdeling;
  }
  
  function toonTabel($regels, $xOffset, $titel, $kleuren)
  {
    $this->pdf->setWidths(array($xOffset + 3, 45, 24, 15));
    $this->pdf->setAligns(array('L', 'L', 'R', 'R'));
    $this->pdf->setXY($this->pdf->marge, 100);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'], $this->pdf->rapport_fontcolor['g'], $this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->row(array('', $titel, 'Waarde EUR', 'in %'));
    $this->pdf->excelData[] = array($titel, 'Waarde EUR', 'in %');
    $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
    $this->pdf->ln(1);
    $totalen = array();

    foreach ($regels as $categorie => $data)
    {
      if (!isset($kleuren[$categorie]))
      {
        $kleuren[$categorie] = array(128, 128, 128);
      }
    }
    
    if ($this->normaleVerdeling == true)
    {
      foreach ($regels as $categorie => $data)
      {
        $data = $regels[$categorie];
        $this->pdf->rect($this->pdf->getX() + $xOffset, $this->pdf->getY() + 1, 2, 2, 'DF', '', $kleuren[$categorie]);
        $this->pdf->row(array('', $categorie, $this->formatGetal($data['waardeEUR'], 0), $this->formatGetal($data['percentage'], 2)));
        $this->pdf->excelData[] = array($categorie, round($data['waardeEUR'], 0), round($data['percentage'], 2));
        $totalen['waardeEUR'] += $data['waardeEUR'];
        $totalen['percentage'] += $data['percentage'];
      }
    }
    else
    {
      foreach ($kleuren as $categorie => $kleur)
      {
        //foreach($regels as $categorie=>$data)
        if (isset($regels[$categorie]))
        {
          $data = $regels[$categorie];
          $this->pdf->rect($this->pdf->getX() + $xOffset, $this->pdf->getY() + 1, 2, 2, 'DF', '', $kleuren[$categorie]);
          $this->pdf->row(array('', $categorie, $this->formatGetal($data['waardeEUR'], 0), $this->formatGetal($data['percentage'], 1)));
          $this->pdf->excelData[] = array($categorie, round($data['waardeEUR'], 0), round($data['percentage'], 1));
          $totalen['waardeEUR'] += $data['waardeEUR'];
          $totalen['percentage'] += $data['percentage'];
        }
      }
    }
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('', '', 'TS', 'TS');
    $this->pdf->row(array('', 'Totaal', $this->formatGetal($totalen['waardeEUR'], 0), $this->formatGetal($totalen['percentage'], 1) . ''));
    $this->pdf->excelData[] = array('Totaal', round($totalen['waardeEUR'], 0), round($totalen['percentage'], 2));
    $this->pdf->excelData[] = array();
    $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
  }
  
  function printPie($kleurdata, $xstart, $ystart, $titel,$kleurVolgorde)
  {
    $col1 = array(255, 0, 0); // rood
    $col2 = array(0, 255, 0); // groen
    $col3 = array(255, 128, 0); // oranje
    $col4 = array(0, 0, 255); // blauw
    $col5 = array(255, 255, 0); // geel
    $col6 = array(255, 0, 255); // paars
    $col7 = array(128, 128, 128); // grijs
    $col8 = array(128, 64, 64); // bruin
    $col9 = array(255, 255, 255); // wit
    $col0 = array(0, 0, 0); //zwart
    $standaardKleuren = array($col1, $col2, $col3, $col4, $col5, $col6, $col7, $col8, $col9, $col0);
    $pieData = array();

    
    if ($kleurdata && $kleurVolgorde)
    {
      $sorted = array();
      $percentages = array();
      $kleur = array();
      $valuta = array();
      

      foreach ($kleurVolgorde as $key => $k)
      {
        $percentages[] = $kleurdata[$key]['percentage'];
        $kleur[] = $kleurdata[$key]['kleur'];
        $valuta[] = $key;
      }
      //arsort($percentages);

      foreach ($percentages as $key => $percentage)
      {
        $sorted[$valuta[$key]]['kleur'] = $kleur[$key];
        $sorted[$valuta[$key]]['percentage'] = $percentage;
      }
      $kleurdata = $sorted; //columnSort($kleurdata, 'pecentage');
      $grafiekKleuren = array();
      
      $a = 0;
      foreach ($kleurdata as $key => $value)
      {
        if ($value['kleur'][0] == 0 && $value['kleur'][1] == 0 && $value['kleur'][2] == 0)
        {
          $grafiekKleuren[] = $standaardKleuren[$a];
        }
        else
        {
          $grafiekKleuren[] = array($value['kleur'][0], $value['kleur'][1], $value['kleur'][2]);
        }
        $pieData[$key] = $value['percentage'];
        $a++;
      }
    }
    else
    {
      $grafiekKleuren = $standaardKleuren;
    }
    
   // $this->pdf->SetTextColor(255, 255, 255);
  
    $trapport_printpie = true;
    foreach ($pieData as $key => $value)
    {
      if (round($value,2) < 0)
      {
        $trapport_printpie = false;
      }
    }
    
    if ($trapport_printpie)
    {
      $this->pdf->SetXY($xstart, $ystart - 4);
      $y = $this->pdf->getY();
      $this->pdf->SetFont($this->pdf->rapport_font, 'b', 10);
      if($this->pdf->portefeuilledata['Layout']==95)
        $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'], $this->pdf->rapport_kop_fontcolor['g'], $this->pdf->rapport_kop_fontcolor['b']);
      $this->pdf->Cell(50, 4, vertaalTekst($titel, $this->pdf->rapport_taal), 0, 1, "C");
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'], $this->pdf->rapport_fontcolor['g'], $this->pdf->rapport_fontcolor['b']);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      
      $this->pdf->SetXY($xstart, $ystart + 2);
      $this->PieChart(100, 55, $pieData, '%l (%p)', $grafiekKleuren);
      $this->pdf->setY($y);
      $this->pdf->SetLineWidth($this->pdf->lineWidth);
    }
    else
    {
      $this->pdf->SetXY($xstart, $ystart - 4);
      $y = $this->pdf->getY();
      $this->pdf->SetFont($this->pdf->rapport_font, 'b', 10);
      if($this->pdf->portefeuilledata['Layout']==95)
        $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'], $this->pdf->rapport_kop_fontcolor['g'], $this->pdf->rapport_kop_fontcolor['b']);
      $this->pdf->Cell(50, 4, vertaalTekst($titel, $this->pdf->rapport_taal), 0, 1, "C");
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'], $this->pdf->rapport_fontcolor['g'], $this->pdf->rapport_fontcolor['b']);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      
      $this->pdf->SetXY($xstart-10, $ystart);
      $this->BarDiagram(70, $pieData, '%l (%p)', $grafiekKleuren, '');
      $this->pdf->setY($y);
      $this->pdf->SetLineWidth($this->pdf->lineWidth);
    }
  }
  
  function PieChart($w, $h, $data, $format, $colors = null)
  {
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    //	$this->pdf->SetLegends($data,$format);
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $hLegend = 2;
    $radius = min($w - $margin * 2, $h - $margin * 2); //
    
    $radius = floor($radius / 2);
    $XDiag = $XPage + $margin + $radius;
    $YDiag = $YPage + $margin + $radius;
    if ($colors == null)
    {
      for ($i = 0; $i < count($data); $i++)
      {
        $gray = $i * intval(255 / count($data));
        $colors[$i] = array($gray, $gray, $gray);
      }
    }
    
    //Sectors
    $sum = array_sum($data);
    $this->pdf->SetLineWidth(0.2);
    $angleStart = 0;
    $angleEnd = 0;
    $i = 0;
    $angle = 0;
    //echo "<br>\n";
    foreach ($data as $val)
    {
      //$angle = round(($val * 360) / doubleval($sum),1);
      $angle = round(floor(($val * 360) / doubleval($sum) * 5) / 5, 1);
      //echo "$angle <br>\n"; ob_flush();
      if ($angle > 1)
      {
        $angleEnd = $angleStart + $angle;
        // echo "$angleEnd = $angleStart + $angle <br>\n"; ob_flush();
        $this->pdf->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
        $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
        $angleStart += $angle;
      }
      else
      {
        $this->pdf->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
      }
      $i++;
    }
    if ($angleEnd != 360)
    {
      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    }
    
  }
  function SetLegends2($data, $format)
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
      $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->wLegend);
    }
  }
  
  
  function BarDiagramVerschil($w, $h, $data, $format,$colorArray,$titel,$max=0)
  {
    // $pdfObject = &$object;
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->SetLegends2($data,$format);
    // listarray($data);
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $nbDiv=5;
    $legendWidth=0;
    $YDiag = $YPage;
    $hDiag = floor($h);
    $XDiag = $XPage +  $legendWidth;
    $lDiag = floor($w - $legendWidth);
    if($color == null)
      $color=array(155,155,155);
    if ($maxVal == 0) {
      $maxVal = max($data)*1.1;
    }
    if ($minVal == 0) {
      $minVal = min($data)*1.1;
    }
    if($minVal > 0)
      $minVal=0;
    //$maxVal=ceil($maxVal/10)*10;
    $maxVal=ceil($maxVal);
    $minVal=floor($minVal);
    if($max<>0)
    {
      $minVal = $max * -1;
      $maxVal = $max;
      //$nbDiv=5;
    }
   // echo "$minVal $maxVal<br>\n"; ob_flush();
    $offset=$minVal;
    $valIndRepere = ceil(round(($maxVal-$minVal) / $nbDiv,2)*100)/100;
    $bandBreedte = $valIndRepere * $nbDiv;
    $lRepere = floor($lDiag / $nbDiv);
    $unit = $lDiag / $bandBreedte;
    $hBar = ($hDiag / (count($data)+1));
    //  $hDiag = $hBar * ($this->pdf->NbVal + 1);
    
    //echo "$hBar <br>\n";
    $eBaton = floor($hBar * 80 / 100);
    $legendaStep=$unit;
    
    $legendaStep=$unit/$nbDiv*$bandBreedte;
    //if($bandBreedte/$legendaStep > $nbDiv)
    //  $legendaStep=$legendaStep*5;
    // if($bandBreedte/$legendaStep > $nbDiv)
    //  $legendaStep=$legendaStep*2;
    // if($bandBreedte/$legendaStep > $nbDiv)
    //   $legendaStep=$legendaStep/2*5;
    $valIndRepere=round($valIndRepere/$unit/5)*5;
    
    
    $this->pdf->SetLineWidth(0.2);
    $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $nullijn=$XDiag - ($offset * $unit);
    
    $i=0;
    
    
    
    $this->pdf->setXY($nullijn,$YDiag + $hDiag);
    $this->pdf->Cell(0.1, 5, ''.round(0,0),0,0,'C');
    
    //  $this->pdf->SetFont($this->pdf->rapport_font, '', 5);
    if(round($legendaStep,5) <> 0.0)
    {
      //for($x=$nullijn;$x<$XDiag; $x=$x-$legendaStep)
      for($x=$nullijn;$x>=$XDiag; $x=$x-$legendaStep)
      {
        $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
        $this->pdf->setXY($x,$YDiag + $hDiag);
        $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,1),0,0,'C');
        $i++;
        if($i>100)
          break;
      }
      
      $i=0;
      //for($x=$nullijn;$x>($XDiag+$lDiag); $x=$x+$legendaStep)
      for($x=$nullijn;$x<=($XDiag+$lDiag); $x=$x+$legendaStep)
      {
        $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
        $this->pdf->setXY($x,$YDiag + $hDiag);
        $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,1),0,0,'C');
        
        $i++;
        if($i>100)
          break;
      }
    }
    
    $i=0;
    
    $this->pdf->SetXY($XDiag, $YDiag);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+2);
    $this->pdf->Cell($w,-5, $titel,0,0,'C');
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

    foreach($colorArray as $categorie => $kleur)
    {
      if (isset($data[$categorie])) {
        $val = $data[$categorie];
      } else {
        continue;
      }

      if($val>$maxVal)
        $val=$maxVal;
      if($val<$maxVal*-1)
        $val=$maxVal*-1;
      $this->pdf->SetFillColor($kleur[0],$kleur[1],$kleur[2]);
      $xval = $nullijn;
      $lval = ($val * $unit);
      $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
      $hval = $eBaton;
      $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
      $this->pdf->SetXY($XPage+$w+2, $yval);
      $this->pdf->Cell($w , $hval, $this->pdf->legends[$i],0,0,'L');
      $i++;
    }
    
    //Scales
    $minPos=($minVal * $unit);
    $maxPos=($maxVal * $unit);
    
    $unit=($maxPos-$minPos)/$nbDiv;
    // echo "$minPos $maxPos -> $minVal $maxVal using $unit met null $nullijn";
    
    
  }
  function BarDiagram($w, $data, $format,$colorArray,$titel)
  {
    $this->pdf->SetFont($this->rapport_font, '', $this->rapport_fontsize);
    //$this->SetLegends2($data,$format);
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $nbDiv=5;
    $legendWidth=0;
    $aantal=count($data);
    if($aantal>10)
      $hBar = 4;
    else
      $hBar = 5;//floor($hDiag / ($this->pdf->NbVal + 1));
    $YDiag = $YPage+30-(($aantal*$hBar)/2);
    $XDiag = $XPage +  $legendWidth;
    $lDiag = floor($w - $legendWidth);
    $color=array(155,155,155);
    $maxVal = max($data)*1.1;
    $minVal = min($data)*1.1;
    if($minVal > 0)
      $minVal=0;
    $maxVal=ceil($maxVal/10)*10;
    
    $offset=$minVal;
    $valIndRepere = ceil(round(($maxVal-$minVal) / $nbDiv,2)*100)/100;
    $bandBreedte = $valIndRepere * $nbDiv;
    $unit = $lDiag / $bandBreedte;
    
    $eBaton = floor($hBar * 80 / 100);
    
    $this->pdf->SetLineWidth(0.2);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $nullijn=$XDiag - ($offset * $unit);
    $i=0;
    $this->pdf->setXY($XPage,$YPage);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', 8.5);
    $this->pdf->Cell($w,4,$titel,0,1,'L');
    $this->pdf->SetFont($this->pdf->rapport_font, '', 7);
    //listarray($data);
    foreach($data as $key=>$val)
    {
      $this->pdf->SetFillColor($colorArray[$i][0],$colorArray[$i][1],$colorArray[$i][2]);
      $xval = $nullijn;
      $lval = ($val * $unit);
      $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
      $hval = $eBaton;
      //echo "Rect($xval, $yval, $lval, $hval, 'DF'); <br>";
      $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
      //$this->pdf->SetXY($XPage, $yval);
      //$this->pdf->Cell($legendWidth , $hval, $this->pdf->legends[$i],0,0,'R');
      $i++;
    }

  }
  
  function vulPagina($belCategorie = '')
  {
    $pieTeller = 0;
  
    if($this->pdf->portefeuilledata['Layout']==95)
    {
      $doorkijkCategorieSoorten = array('Beleggingscategorien', 'Regios');
      $offsetStep=98*1.25;
      $xStartOffset=30;
    }
    else
    {
      $doorkijkCategorieSoorten = array('Beleggingscategorien', 'Regios', 'Beleggingssectoren');
      $offsetStep=98;
      $xStartOffset=0;
    }

    $doorkijkTitels = array('Beleggingscategorien' => 'Beleggingscategorie', 'Regios' => 'Regio', 'Beleggingssectoren' => 'Beleggingssector');//array();Beleggingscategorie
    if($this->verdeling=='')
    {
      $doorkijkCategorieSoorten = array('Regios', 'Regios', 'Regios');
      $doorkijkTitels = array('Regioverdeling portefeuille', '', 'Regioverdeling modelportefeuille');
    }
    elseif($this->verdeling=='Beleggingscategorien')
    {
      $doorkijkCategorieSoorten = array('Beleggingscategorien', 'Beleggingscategorien', 'Beleggingscategorien');
      $doorkijkTitels = array('Beleggingscategorieën portefeuille', '', 'Beleggingscategorieën modelportefeuille');
    }
    elseif($this->verdeling=='Beleggingssectoren')
    {
      $doorkijkCategorieSoorten = array('Beleggingssectoren', 'Beleggingssectoren', 'Beleggingssectoren');
      $doorkijkTitels = array('Beleggingssectoren portefeuille', '', 'Beleggingssectoren modelportefeuille');
    }
    $portefeuille=$this->portefeuille;
    $doorkijkData=array();
    $DB=new DB();
    $verschilData=array();
    $verschilKleuren=array();
    foreach ($doorkijkCategorieSoorten as $index => $doorkijkCategorieSoort)
    {
      if ($index == 2)
      {
        $portefeuille = $this->pdf->portefeuilledata['ModelPortefeuille'];
  
        $query="SELECT Fixed,Beleggingscategorie FROM ModelPortefeuilles WHERE Portefeuille='".$portefeuille."'";
        $DB->SQL($query);
        $DB->Query();
        $modelType = $DB->nextRecord();
        if($modelType['Fixed']==1||$modelType['Fixed']==2)
          $portefeuilleData = berekenFixedModelPortefeuille($portefeuille,  $this->rapportageDatum);
        else
          $portefeuilleData = berekenPortefeuilleWaarde($portefeuille, $this->rapportageDatum, (substr($this->rapportageDatum,5,5)=='01-01'?true:false));
        
        vulTijdelijkeTabel($portefeuilleData,$portefeuille,$this->rapportageDatum);
      }
  
      $xOffset = $index * $offsetStep;
      if ($doorkijkCategorieSoort == 'Beleggingscategorien')
      {
        $belCategorieFilter = '';
      }
      else
      {
        $belCategorieFilter = $belCategorie;
      }
  
      if ($index == 0 || $index == 2)
      {
        if ($this->normaleVerdeling == true)
        {
          $doorKijk = $this->bepaalWegingNormaal($portefeuille, $doorkijkCategorieSoort, $belCategorieFilter);
        }
        else
        {
          $doorKijk = $this->bepaalWeging($portefeuille, $doorkijkCategorieSoort, $belCategorieFilter);
        }
    
    
        $grafiekdata = array();
        $grafiekTonen = true;
        foreach ($doorKijk['categorien'] as $categorie => $percentage)
        {
      
          $grafiekdata[$categorie]['kleur'] = $this->kleuren[$doorkijkCategorieSoort][$categorie];//array('R' => array('value' => $kleuren[$categorie][0]),'G' => array('value' => $kleuren[$categorie][1]),'B' => array('value' => $kleuren[$categorie][2]));
          $grafiekdata[$categorie]['percentage'] = $percentage;
  
  
          $verschilKleuren[$categorie]=$this->kleuren[$doorkijkCategorieSoort][$categorie];//array('R' => array('value' => $kleuren[$categorie][0]),'G' => array('value' => $kleuren[$categorie][1]),'B' => array('value' => $kleuren[$categorie][2]));
          if($index==0)
            $verschilData[$categorie] = $percentage;
          else
            $verschilData[$categorie] -= $percentage;
          
        }
        if ($grafiekTonen == true)
        {
    //      $this->printPie($grafiekdata, 30 + $xStartOffset + $xOffset, 30, $doorkijkTitels[$doorkijkCategorieSoort]);
        } //+$yOffset);
        $pieTeller++;
    
    //    $this->toonTabel($doorKijk['details'], $xStartOffset + $xOffset, $doorkijkTitels[$doorkijkCategorieSoort], $this->kleuren[$doorkijkCategorieSoort]);
        $doorkijkData[$index]=array($grafiekdata,$doorKijk);
      }
    }
  
    $xStartOffset=0;
    foreach ($doorkijkCategorieSoorten as $index => $doorkijkCategorieSoort)
    {
      $xOffset = $index * $offsetStep;
      if ($index == 0 || $index == 2)
      {
        $this->printPie($doorkijkData[$index][0], 30 + $xStartOffset + $xOffset, 30, $doorkijkTitels[$index], $this->kleuren[$doorkijkCategorieSoort]);
        $this->toonTabel($doorkijkData[$index][1]['details'], $xStartOffset + $xOffset, $doorkijkTitels[$doorkijkCategorieSoort], $this->kleuren[$doorkijkCategorieSoort]);
      }
      if($index==1)
      {
        $this->pdf->setXY(30+$xOffset,30);
        $max=0;
        $this->BarDiagramVerschil(50,55, $verschilData,'',$this->kleuren[$doorkijkCategorieSoort],'Verschil portefeuille/model',$max);

        $this->pdf->setWidths(array($xStartOffset + $xOffset + 13, 45, 8, 10));
        $this->pdf->setAligns(array('R', 'L', 'L', 'R'));
        $this->pdf->setXY($this->pdf->marge, 100);
        $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'], $this->pdf->rapport_fontcolor['g'], $this->pdf->rapport_fontcolor['b']);
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
        $this->pdf->row(array('', '', '', 'in %'));
        $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
        $this->pdf->ln(1);

        foreach ($this->kleuren[$doorkijkCategorieSoort] as $categorie => $kleur) {
          if (isset($verschilData[$categorie])) {
            $this->pdf->rect($this->pdf->getX() + $xStartOffset + $xOffset + 10, $this->pdf->getY() + 1, 2, 2, 'DF', '', $kleur);
            $this->pdf->row(array('', $categorie, '', $this->formatGetal($verschilData[$categorie], '1')));
          }
        }

      }
    }
    if ($this->debug)
    {
      $wegingSoorten = array('DoorkijkfondsWeging', 'MSfondsWeging', 'NietGekoppeld', 'afwijkingWeging');
      $vertaling = array('afwijkingWeging' => 'afwijkingWeging som<>100%');
      foreach ($wegingSoorten as $wegingSoort)
      {
        $this->pdf->addPage();
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
        $this->pdf->setWidths(array(1, 50, 50, 30));
        if (isset($vertaling[$wegingSoort]))
        {
          $this->pdf->row(array('', $vertaling[$wegingSoort]));
          $this->pdf->excelData[] = array($vertaling[$wegingSoort]);
        }
        else
        {
          $this->pdf->row(array('', $wegingSoort));
          $this->pdf->excelData[] = array($wegingSoort);
          
        }
       // $startPage = $this->pdf->page;
        foreach ($doorkijkCategorieSoorten as $index => $doorkijkCategorieSoort)
        {
          $this->pdf->setWidths(array(1, 45, 45, 15, 30));
          $this->pdf->setAligns(array('L', 'L', 'L', 'R', 'R'));
          $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
          $this->pdf->row(array('', $doorkijkCategorieSoort));
          
          $categorieTotalen = array();
          $this->pdf->row(array('', 'fonds', 'categorie', 'perc', 'waarde'));
          $this->pdf->excelData[] = array('fonds', 'categorie', 'perc', 'waarde');
          $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
          foreach ($this->debugData[$wegingSoort][$doorkijkCategorieSoort] as $fonds => $verdelingData)
          {
            $n = 0;
            foreach ($verdelingData as $categorie => $percentage)
            {
              if ($wegingSoort == 'afwijkingWeging')
              {
                $weging = $this->formatGetal($percentage['weging'], 5);
                $waarde = '';
              }
              else
              {
                $weging = $this->formatGetal($percentage['weging'], 2);
                $waarde = $this->formatGetal($percentage['waarde'], 2);
              }
              if ($n == 0)
              {
                $this->pdf->row(array('', $fonds, $categorie, $weging, $waarde));
                $this->pdf->excelData[] = array($fonds, $categorie, round($percentage['weging'], 5), round($percentage['waarde'], 2));
              }
              else
              {
                $this->pdf->row(array('', '', $categorie, $weging, $waarde));
                $this->pdf->excelData[] = array('', $categorie, round($percentage['weging'], 5), round($percentage['waarde'], 2));
              }
              $categorieTotalen[$categorie]['waarde'] += $percentage['waarde'];
              $n++;
            }
            
          }
          
          
          $this->pdf->ln();
          $this->pdf->excelData[] = array();
          foreach ($categorieTotalen as $categorie => $waarden)
          {
            $this->pdf->row(array('', 'Totaal', $categorie, '', $this->formatGetal($waarden['waarde'], 2)));
            $this->pdf->excelData[] = array('Totaal', $categorie, '', round($waarden['waarde'], 2));
          }
          $this->pdf->ln();
          $this->pdf->excelData[] = array();
          
        }
      }
      //	listarray($this->debugData);
      
    }
  }
  
  
  function writeRapport()
  {
/*
// WORDT OOK GEBRUIKT IN L93 L94 L96 L98 en L101
*/
    $db = new DB();
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    
    if ($this->normaleVerdeling == true)
    {
      $query = "SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder='$beheerder'";
      $db->SQL($query);
      $db->Query();
      $this->kleuren = array();
      $data = $db->nextRecord();
      $alleKleuren = unserialize($data['grafiek_kleur']);
      
      $typen = array('OIB' => 'Beleggingscategorien.Beleggingscategorie', 'OIR' => 'Regios.Regio', 'OIS' => 'Beleggingssectoren.Beleggingssector');
      
      foreach ($alleKleuren as $type => $kleuren)
      {
        if (isset($typen[$type]))
        {
          $parts = explode(".", $typen[$type]);
          $query = "SELECT " . $parts[1] . " as verdeling, Omschrijving FROM " . $parts[0] . "";
          $db->SQL($query);
          $db->Query();
          $omschrijvingen = array();
          while ($data = $db->nextRecord())
          {
            $omschrijvingen[$data['verdeling']] = $data['Omschrijving'];
          }
          $omschrijvingen['Liquiditeiten']='Liquiditeiten';
          foreach ($kleuren as $verdeling => $kleurData)
          {
            $this->kleuren[$parts[0]][$omschrijvingen[$verdeling]] = array($kleurData['R']['value'], $kleurData['G']['value'], $kleurData['B']['value']);
          }
        }
      }
    }
    else
    {
      $query = "SELECT doorkijkCategorie,doorkijkCategorieSoort,grafiekKleur, afdrukVolgorde
                   FROM doorkijk_categoriePerVermogensbeheerder
                   WHERE Vermogensbeheerder='$beheerder'
                   ORDER BY doorkijkCategorieSoort,afdrukVolgorde";
      
      $db->SQL($query);
      $db->Query();
      $this->kleuren = array();
      while ($data = $db->nextRecord())
      {
        $this->kleuren[$data['doorkijkCategorieSoort']][$data['doorkijkCategorie']] = unserialize($data['grafiekKleur']);
      }
     // listarray( $this->kleuren);
    }
    /*
        $this->pdf->AddPage();
        $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
        $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
        $this->vulPagina();
    */
    $this->pdf->rapport_titel = "Verdeling";
    $this->pdf->AddPage('L');
    $this->pdf->templateVars[$this->pdf->rapport_type . 'Paginas'] = $this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type . 'Paginas'] = $this->pdf->rapport_titel;
    $this->vulPagina();
    
    
  }
}
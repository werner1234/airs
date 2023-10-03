<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"] . "/html/rapport/rapportVertaal.php");

class RapportGRAFIEK_L13
{
  function RapportGRAFIEK_L13($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = $pdf;
    $this->pdf->rapport_type = "GRAFIEK";
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
    $this->normaleVerdeling = true;

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
    $this->standaardKleuren = array($col1, $col2, $col3, $col4, $col5, $col6, $col7, $col8, $col9, $col0);

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

  function bepaalWegingNormaal($doorkijkSoort, $belCategorie = '')
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
                  WHERE rapportageDatum ='" . $this->rapportageDatum . "' $fondsFilter AND portefeuille = '" . $this->portefeuille . "'" . $__appvar['TijdelijkeRapportageMaakUniek'];
    $db->SQL($query);
    $db->Query();
    $totaalWaarde = $db->nextRecord();


    $vertaling = array('Hoofdcategorien' => 'hoofdcategorie', 'Hoofdsectoren' => 'hoofdsector', 'Regios' => 'Regio', 'Valutas' => 'Valuta');
    $query = "SELECT TijdelijkeRapportage.type, if(" . $vertaling[$doorkijkSoort] . "Volgorde = 0,127," . $vertaling[$doorkijkSoort] . "Volgorde) as volgorde, sum(actuelePortefeuilleWaardeEuro) as waardeEUR, " . $vertaling[$doorkijkSoort] . " as verdeling , " . $vertaling[$doorkijkSoort] . "Omschrijving as Omschrijving
					FROM TijdelijkeRapportage	WHERE rapportageDatum ='" . $this->rapportageDatum . "'  $fondsFilter AND portefeuille = '" . $this->portefeuille . "'" . $__appvar['TijdelijkeRapportageMaakUniek'] . "
					GROUP BY " . $vertaling[$doorkijkSoort] . ", TijdelijkeRapportage.type
					ORDER BY volgorde";

    $db = new DB();
    $db->SQL($query);
    $db->Query();

    $doorkijkVerdeling = array();
    while ($row = $db->nextRecord())
    {
      if($row['type']=='rekening' && empty ($row['Omschrijving']))
        $categorie='Liquiditeiten';
      else
        $categorie = $row['Omschrijving'];

      if ($categorie == '')
      {
        $categorie = 'Geen ' . $vertaling[$doorkijkSoort];
      }
      $totaalPercentage = $row['waardeEUR'] / $totaalWaarde['totaal'] * 100;
      $doorkijkVerdeling['categorien'][$categorie] += $totaalPercentage;

      $this->categorieVertaling[$categorie] = $row['verdeling'];
      $doorkijkVerdeling['details'][$categorie]['percentage'] += $totaalPercentage;
      $doorkijkVerdeling['details'][$categorie]['waardeEUR'] += $row['waardeEUR'];
    }

    return $doorkijkVerdeling;
  }

  function bepaalWeging($doorkijkSoort, $belCategorie = '')
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
                  WHERE rapportageDatum ='" . $this->rapportageDatum . "' $fondsFilter AND portefeuille = '" . $this->portefeuille . "'" . $__appvar['TijdelijkeRapportageMaakUniek'];
    $db->SQL($query);
    $db->Query();
    $totaalWaarde = $db->nextRecord();

    $vertaling = array('Beleggingscategorien' => 'Beleggingscategorie', 'Beleggingssectoren' => 'Beleggingssector', 'Regios' => 'Regio');
    $query = "SELECT fonds,rekening, actuelePortefeuilleWaardeEuro as waardeEUR, " . $vertaling[$doorkijkSoort] . " as airsSoort
					FROM TijdelijkeRapportage	WHERE rapportageDatum ='" . $this->rapportageDatum . "'  $fondsFilter AND portefeuille = '" . $this->portefeuille . "'" . $__appvar['TijdelijkeRapportageMaakUniek'] . " Order by fonds";

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
    $this->pdf->setXY($this->pdf->marge, 110);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'], $this->pdf->rapport_fontcolor['g'], $this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->row(array('', $titel, 'Waarde EUR', 'in %'));
    $this->pdf->excelData[] = array($titel, 'Waarde EUR', 'in %');
    $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
    $this->pdf->ln(1);
    $totalen = array();
  
    /*
$a=0;
foreach ($regels as $categorie => $data)
{
  if (
    (
      ! isset($kleuren[$categorie]) ||
      array_sum($kleuren[$categorie]) === 0
    )
    &&
    (
      ! isset ($kleuren[$this->categorieVertaling[$categorie]]) ||
      empty ($kleuren[$this->categorieVertaling[$categorie]][0]) &&
      empty ($kleuren[$this->categorieVertaling[$categorie]][1]) &&
      empty ($kleuren[$this->categorieVertaling[$categorie]][2])
    )
  ) {
    $kleuren[$categorie] = $this->standaardKleuren[$a];//array(128, 128, 128);
    $a++;
  }
}
*/
    if ($this->normaleVerdeling == true)
    {
      foreach ($regels as $categorie => $data)
      {
        $kleur = $kleuren[$categorie];
        if ( isset ($kleuren[$this->categorieVertaling[$categorie]]) ) {
          $kleur = $kleuren[$this->categorieVertaling[$categorie]];
        }
        $data = $regels[$categorie];
        $this->pdf->rect($this->pdf->getX() + $xOffset, $this->pdf->getY() + 1, 2, 2, 'DF', '', $kleur);
        $this->pdf->row(array('', $categorie, $this->formatGetal($data['waardeEUR'], 0), $this->formatGetal($data['percentage'], 1)));
        $this->pdf->excelData[] = array($categorie, round($data['waardeEUR'], 0), round($data['percentage'], 1));
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
    $this->pdf->excelData[] = array('Totaal', round($totalen['waardeEUR'], 0), round($totalen['percentage'], 1));
    $this->pdf->excelData[] = array();
    $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
  }

  function printPie($kleurdata, $xstart, $ystart, $titel)
  {
    $pieData = array();
    if ($kleurdata)
    {
      $sorted = array();
      $percentages = array();
      $kleur = array();
      $valuta = array();

      foreach ($kleurdata as $key => $data)
      {
        $percentages[] = $data['percentage'];
        $kleur[] = $data['kleur'];
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
          $grafiekKleuren[] = $this->standaardKleuren[$a];
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
      $grafiekKleuren = $this->standaardKleuren;
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
      $doorkijkCategorieSoorten = array('Hoofdcategorien', 'Valutas', 'Hoofdsectoren');
      $offsetStep=98;
      $xStartOffset=0;
    }

    $loopNr = 0;
    $doorkijkTitels = array('Hoofdcategorien' => 'Hoofdcategorie', 'Valutas' => 'Valuta', 'Hoofdsectoren' => 'Hoofdsector');//array();Beleggingscategorie
    foreach ($doorkijkCategorieSoorten as $index => $doorkijkCategorieSoort)
    {
      $xOffset = $index * $offsetStep;
      if ($doorkijkCategorieSoort == 'Beleggingscategorien')
      {
        $belCategorieFilter = '';
      }
      else
      {
        $belCategorieFilter = $belCategorie;
      }

      if ($this->normaleVerdeling == true)
      {
        $doorKijk = $this->bepaalWegingNormaal($doorkijkCategorieSoort, $belCategorieFilter);
      }
      else
      {
        $doorKijk = $this->bepaalWeging($doorkijkCategorieSoort, $belCategorieFilter);
      }

      $catKleuren = $this->kleuren[$doorkijkCategorieSoort];
      if ( $doorkijkCategorieSoort === 'Valutas' ) {
        $catKleuren = $this->kleuren['OIV'];
      } elseif ($doorkijkCategorieSoort === 'Hoofdsectoren' ) {
        $catKleuren = $this->kleuren['OIS'];
      } elseif($doorkijkCategorieSoort === 'Hoofdcategorien') {
        $catKleuren = $this->kleuren['OIB'];
      }

      $grafiekdata = array();
      $pieGrafiekdata = array();
      $grafiekTonen = true;
  


      foreach ($doorKijk['categorien'] as $categorie => $percentage)
      {
        if ( $doorkijkCategorieSoort === 'Hoofdsectoren' && $categorie === 'Liquiditeiten' ) {unset($doorKijk['details']['Liquiditeiten']); continue;}
  
     //   $pieGrafiekdata['kleur'][] = $catKleuren[$this->categorieVertaling[$categorie]];
        $pieGrafiekdata['kleur'][] = (isset($catKleuren[$this->categorieVertaling[$categorie]])?$catKleuren[$this->categorieVertaling[$categorie]]:$catKleuren[$categorie]);
        $pieGrafiekdata['percentage'][] = $percentage;
  
       // $grafiekdata[$categorie]['kleur'] = $catKleuren[$this->categorieVertaling[$categorie]];
        $grafiekdata[$categorie]['kleur'] =  (isset($catKleuren[$this->categorieVertaling[$categorie]])?$catKleuren[$this->categorieVertaling[$categorie]]:$catKleuren[$categorie]);
        $grafiekdata[$categorie]['percentage'] = $percentage;
      }


      $diameter = 30;
      $hoek = 30;
      $dikte = 10;
      $Xas= 53;
      $yas= 60;

      if ($grafiekTonen == true)
      {

        $this->pdf->SetXY($Xas - 50 + ($loopNr*(100)),$yas-15);
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', 11);
        $this->pdf->MultiCell(100,10,$doorkijkTitels[$doorkijkCategorieSoort],0,"C");
//        debug(array_keys($doorKijk['details']));
//        debug($doorKijk['details']);
//        $this->pdf->set3dLabels(array_keys($doorKijk['details']),$Xas + ($loopNr*100),$yas,$pieGrafiekdata['kleur']);
        $this->pdf->Pie3D($pieGrafiekdata['percentage'],$pieGrafiekdata['kleur'],$Xas + ($loopNr*100),$yas+10,$diameter,$hoek,$dikte,$doorkijkTitels[$doorkijkCategorieSoort], 'geen');
        $loopNr++;
//        $this->printPie($grafiekdata, 30 +$xStartOffset+ $xOffset, 50, $doorkijkTitels[$doorkijkCategorieSoort]);
      }
      $pieTeller++;



      $this->toonTabel($doorKijk['details'],$xStartOffset+ $xOffset, $doorkijkTitels[$doorkijkCategorieSoort], $catKleuren);
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

    $db = new DB();
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];

//    if ($this->normaleVerdeling == true)
//    {
    $query = "SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder='$beheerder'";
    $db->SQL($query);
    $db->Query();
    $this->kleuren = array();
    $data = $db->nextRecord();
    $alleKleuren = unserialize($data['grafiek_kleur']);
    
    $query="SELECT Hoofdsector,Beleggingssector FROM SectorenPerHoofdsector WHERE Vermogensbeheerder='$beheerder'";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      if(!isset($alleKleuren['OIS'][$data['Hoofdsector']]) || ($alleKleuren['OIS'][$data['Hoofdsector']]['R']['value']==0 &&
                                                               $alleKleuren['OIS'][$data['Hoofdsector']]['G']['value']==0 &&
                                                               $alleKleuren['OIS'][$data['Hoofdsector']]['B']['value']==0))
      {
        $alleKleuren['OIS'][$data['Hoofdsector']]=$alleKleuren['OIS'][$data['Beleggingssector']];
      }
    }


//      $typen = array('OIB' => 'Beleggingscategorien.Beleggingscategorie', 'OIR' => 'Regios.Regio', 'OIS' => 'Beleggingssectoren.Beleggingssector');
    foreach ($alleKleuren as $type => $kleuren)
    {

//        if (isset($typen[$type]))
//        {
//          $parts = explode(".", $typen[$type]);
//          $query = "SELECT " . $parts[1] . " as verdeling, Omschrijving FROM " . $parts[0] . "";
//          $db->SQL($query);
//          $db->Query();
//          $omschrijvingen = array();
//          while ($data = $db->nextRecord())
//          {
//            $omschrijvingen[$data['verdeling']] = $data['Omschrijving'];
//          }
//          $omschrijvingen['Liquiditeiten']='Liquiditeiten';
      foreach ($kleuren as $verdeling => $kleurData)
      {
        $this->kleuren[$type][$verdeling] = array($kleurData['R']['value'], $kleurData['G']['value'], $kleurData['B']['value']);
      }
    }
//      }

//    }
//    else
//    {
    $query = "SELECT doorkijkCategorie,doorkijkCategorieSoort,grafiekKleur, afdrukVolgorde
                   FROM doorkijk_categoriePerVermogensbeheerder 
                   WHERE Vermogensbeheerder='$beheerder'
                   ORDER BY doorkijkCategorieSoort,afdrukVolgorde";
    $db->SQL($query);
    $db->Query();

    while ($data = $db->nextRecord())
    {
      $this->kleuren[$data['doorkijkCategorieSoort']][$data['doorkijkCategorie']] = unserialize($data['grafiekKleur']);
    }
    // listarray( $this->kleuren);
//    }
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
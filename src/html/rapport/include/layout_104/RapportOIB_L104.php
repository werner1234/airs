<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIB_L104
{
  function RapportOIB_L104($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "OIB";
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
      if($this->debug)
      {
        $this->debugData['MSfondsWeging'][$doorkijkSoort][$fonds][$row['msCategorie']]['weging'] = $row['weging'];
        $this->debugData['MSfondsWeging'][$doorkijkSoort][$fonds][$row['msCategorie']]['waarde'] = $waarde*$row['weging']/100;
      }
    }
//echo $query;
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

              if ($this->debug)
              {
                $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['weging'] = 100;
                $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['waarde'] = $waarde;
              }
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

        if($this->debug)
        {
          $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['weging'] += $wegingPerMsCategorie[$row['msCategorie']];
          $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['waarde'] += $waarde*$wegingPerMsCategorie[$row['msCategorie']]/100;
        }
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

        if($this->debug)
        {
          $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['weging'] = 100;
          $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['waarde'] = $waarde;
        }
      }
    }


    return $wegingDoorkijkCategorie;
  }

  function bepaalWeging($doorkijkSoort,$belCategorien=array())
  {
    global $__appvar;
    if(is_array($belCategorien) && count($belCategorien)>0)
      $fondsFilter="AND Beleggingscategorie IN('".implode("','",$belCategorien)."')";
    else
      $fondsFilter='';

    $db = new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal
                  FROM TijdelijkeRapportage
                  WHERE rapportageDatum ='" . $this->rapportageDatum . "' $fondsFilter AND portefeuille = '" . $this->portefeuille . "'" .	$__appvar['TijdelijkeRapportageMaakUniek'];
    $db->SQL($query);
    $db->Query();
    $totaalWaarde = $db->nextRecord();

    $vertaling=array('Beleggingscategorien'=>'Beleggingscategorie','Beleggingssectoren'=>'Beleggingssector','Regios'=>'Regio');
    $query = "SELECT fonds,rekening, actuelePortefeuilleWaardeEuro as waardeEUR, ".$vertaling[$doorkijkSoort]." as airsSoort
					FROM TijdelijkeRapportage	WHERE rapportageDatum ='".$this->rapportageDatum."'  $fondsFilter AND portefeuille = '" . $this->portefeuille . "'" .	$__appvar['TijdelijkeRapportageMaakUniek']." Order by fonds";

    $db=new DB();
    $db->SQL($query); //echo $query."<br>\n";exit;
    $db->Query();

    $doorkijkVerdeling=array();
    while($row = $db->nextRecord())
    {
      if($row['fonds']=='' && $doorkijkSoort <> 'Regios' && $doorkijkSoort <> 'Beleggingscategorien' )
      {
        $row['fonds'] = $row['rekening'];
        $verdeling=array('Geldrekeningen'=>array('weging'=>100,'waarde'=>$row['waardeEUR']));
      }
      else
      {
        if($row['fonds']=='')
          $row['fonds'] = $row['rekening'];
        //	listarray($row);
        $verdeling = $this->bepaalWegingPerFonds($row['fonds'], $doorkijkSoort, $row['airsSoort'], $row['waardeEUR'],$row['rekening']);
      }
      $totaalPercentage=0;
      if(is_array($verdeling))
      {
        $overige=false;
        $check=0;
        foreach($verdeling as $categorie=>$percentage)
        {
          $check+=$percentage['weging'];
          $totaalPercentage=($percentage['weging'] * ($row['waardeEUR']/$totaalWaarde['totaal']) );
          $doorkijkVerdeling['categorien'][$categorie]+=$totaalPercentage;
          $doorkijkVerdeling['details'][$categorie]['percentage']+=$totaalPercentage;
          $doorkijkVerdeling['details'][$categorie]['waardeEUR']+=$percentage['waarde'];
        }
        if($check==0)
          $overige=true;
        elseif(round($check,5) <> 100)
        {
          if($this->debug)
            $this->debugData['afwijkingWeging'][$doorkijkSoort][$row['fonds']]['afwijking']['weging'] = $check-100;
        }
      }
      else
        $overige=true;

      if($overige==true)
      {
        $totaalPercentage=($row['waardeEUR'] / $totaalWaarde['totaal']) * 100;
        $doorkijkVerdeling['categorien']['Overige Fondsen'] += $totaalPercentage;
        $doorkijkVerdeling['details']['Overige Fondsen']['percentage']+=$totaalPercentage;
        $doorkijkVerdeling['details']['Overige Fondsen']['waardeEUR']+=$row['waardeEUR'];

        if($this->debug)
        {
          $this->debugData['NietGekoppeld'][$doorkijkSoort][$row['fonds']]['Overige Fondsen']['weging'] = 100;
          $this->debugData['NietGekoppeld'][$doorkijkSoort][$row['fonds']]['Overige Fondsen']['waarde']=$row['waardeEUR'];
        }
      }
      if($this->debug)
      {
        $row['percentage']=$totaalPercentage;
        $this->debugData['portefeuilleData'][] = $row;
      }

    }
    return $doorkijkVerdeling;
  }


  function writeRapport()
  {
    global $__appvar;
    $query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $portefeuilledata = $DB->nextRecord();
    $this->pdf->AddPage();
    $this->pdf->templateVars['OIBPaginas']=$this->pdf->page;

    $rapportageDatum = $this->rapportageDatum;

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






    //Kleuren instellen
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $allekleuren = unserialize($kleuren['grafiek_kleur']);


    $query="SELECT
if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.beleggingscategorie <> '',TijdelijkeRapportage.beleggingscategorie,'geen')) as categorie,
sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
 if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.beleggingscategorie <> '',Beleggingscategorien.Omschrijving,'geen')) as categorieOmschrijving
FROM TijdelijkeRapportage LEFT JOIN Beleggingscategorien on TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.beleggingscategorie
	WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'"
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " GROUP BY categorie
	ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde, categorie";

    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    while($cat = $DB->nextRecord())
    {
      $data['beleggingscategorieEind']['data'][$cat['categorie']]['waardeEur']=$cat['WaardeEuro'];
      $data['beleggingscategorieEind']['data'][$cat['categorie']]['Omschrijving']=$cat['categorieOmschrijving'];
      $data['beleggingscategorieEind']['pieData'][$cat['categorieOmschrijving']]= $cat['WaardeEuro']/$totaalWaarde;
      $data['beleggingscategorieEind']['kleurData'][$cat['categorieOmschrijving']]=$allekleuren['OIB'][$cat['categorie']];
      $data['beleggingscategorieEind']['kleurData'][$cat['categorieOmschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaarde*100;
    }



    $query="SELECT
TijdelijkeRapportage.valuta,
Sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
Valutas.Omschrijving
FROM
TijdelijkeRapportage
Inner Join Valutas ON Valutas.Valuta = TijdelijkeRapportage.valuta
WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'"
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " GROUP BY Valuta
ORDER BY Valutas.afdrukvolgorde";

    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    while($cat = $DB->nextRecord())
    {
      $data['valutaVerdeling']['data'][$cat['valuta']]['waardeEur']=$cat['WaardeEuro'];
      $data['valutaVerdeling']['data'][$cat['valuta']]['Omschrijving']=$cat['Omschrijving'];
      $data['valutaVerdeling']['pieData'][$cat['Omschrijving']]= $cat['WaardeEuro']/$totaalWaarde;
      $data['valutaVerdeling']['kleurData'][$cat['Omschrijving']]=$allekleuren['OIV'][$cat['valuta']];
      $data['valutaVerdeling']['kleurData'][$cat['Omschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaarde*100;
    }


    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $query = "SELECT doorkijkCategorie,doorkijkCategorieSoort,grafiekKleur, afdrukVolgorde
                   FROM doorkijk_categoriePerVermogensbeheerder
                   WHERE Vermogensbeheerder='$beheerder'
                   ORDER BY doorkijkCategorieSoort,afdrukVolgorde
                  ";

    $DB->SQL($query);
    $DB->Query();
    $this->kleuren=array();
    while($kdata = $DB->nextRecord())
    {
      $this->kleuren[$kdata['doorkijkCategorieSoort']][$kdata['doorkijkCategorie']]=unserialize($kdata['grafiekKleur']);
    }



    if($_POST['debug']==1)
    {
      $this->debug=true;
    }
    $tmp=$this->bepaalWeging('Beleggingssectoren',array('ZAK'));
    foreach($this->kleuren['Beleggingssectoren'] as $sector=>$kleurdata)
    {
      if(isset($tmp['details'][$sector]))
      {
        $doorkijkDetails=$tmp['details'][$sector];
        $data['sectorDoorkijk']['pieData'][$sector]['WaardeEur']=$doorkijkDetails['waardeEUR'];
        $data['sectorDoorkijk']['pieData'][$sector]['Omschrijving']=$sector;
        $data['sectorDoorkijk']['pieData'][$sector]= $doorkijkDetails['percentage']/100;
        $data['sectorDoorkijk']['kleurData'][$sector]=array('R'=>array('value'=>$kleurdata[0]),'G'=>array('value'=>$kleurdata[1]),'B'=>array('value'=>$kleurdata[2]));
        $data['sectorDoorkijk']['kleurData'][$sector]['percentage']=$doorkijkDetails['percentage'];

        $data['sectorDoorkijk']['data'][$sector]['waardeEur']=$doorkijkDetails['waardeEUR'];
        $data['sectorDoorkijk']['data'][$sector]['Omschrijving']=$sector;
      }
    }




    $this->pdf->setXY(75,37);
//$this->pdf->setXY(65,40);
    $this->printPie($data['beleggingscategorieEind']['pieData'],$data['beleggingscategorieEind']['kleurData'],'Categorieverdeling '.date("d-m-Y",db2jul($rapportageDatum)),60,50);
    $this->toonTabel($data['beleggingscategorieEind'],55+0,'Categorie');

    $this->pdf->wLegend=0;
    $this->pdf->setXY(165,37);
//$this->pdf->setXY(175,40);
    $this->printPie($data['valutaVerdeling']['pieData'],$data['valutaVerdeling']['kleurData'],'Valutaverdeling '.date("d-m-Y",db2jul($rapportageDatum)),60,50);
    $this->toonTabel($data['valutaVerdeling'],55+90,'Valuta');
    $this->pdf->wLegend=0;

//    $this->pdf->setXY(210,37);
//    $this->printPie($data['sectorDoorkijk']['pieData'],$data['sectorDoorkijk']['kleurData'],'Sectorverdeling Zakelijke waarden '.date("d-m-Y",db2jul($rapportageDatum)),60,50);
//    $this->toonTabel($data['sectorDoorkijk'],10+180,'Sector');


    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    $this->pdf->setXY($this->pdf->marge,180);
    $txt="Er van uitgaande dat uw voorkeuren, doelstellingen en persoonlijke situatie niet zijn veranderd, voldoen uw beleggingen ultimo het voorgaande kwartaal aan uw strategische asset allocatie.";
    $this->pdf->MultiCell(280,4,$txt,0,'L',0);
    unset($this->pdf->CellBorders);
  }



  function toonTabel($input,$xOffset,$titel)
  {
    $kleuren=array();
    foreach($input['data'] as $cat=>$data)
    {
      $regels[$data['Omschrijving']]=array('waardeEUR'=>$data['waardeEur'],'percentage'=>$input['pieData'][$data['Omschrijving']]);
      $kleuren[$data['Omschrijving']]=array($input['kleurData'][$data['Omschrijving']]['R']['value'],$input['kleurData'][$data['Omschrijving']]['G']['value'],$input['kleurData'][$data['Omschrijving']]['B']['value']);
    }
    //  listarray($regels['data']);exit;
    $this->pdf->setWidths(array($xOffset+3,45,24,13));
    $this->pdf->setAligns(array('L','L','R','R'));
    $this->pdf->setXY($this->pdf->marge,100);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',$titel,'Waarde EUR','in %'));
    $this->pdf->excelData[]=array($titel,'Waarde EUR','in %');
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    $this->pdf->ln(1);
    $totalen=array();
    //	listarray($kleuren);
    foreach($regels as $categorie=>$data)
      if(!isset($kleuren[$categorie]))
        $kleuren[$categorie]=array(128,128,128);

    foreach($kleuren as $categorie=>$kleur)
    {
      //foreach($regels as $categorie=>$data)
      if(isset($regels[$categorie]))
      {
        $data =$regels[$categorie];
        $this->pdf->rect($this->pdf->getX() + $xOffset, $this->pdf->getY() + 1, 2, 2, 'DF', '', $kleuren[$categorie]);
        $this->pdf->row(array('', $categorie, $this->formatGetal($data['waardeEUR'], 0), $this->formatGetal($data['percentage']*100, 2)));
        $this->pdf->excelData[] = array($categorie, round($data['waardeEUR'], 0), round($data['percentage'], 2));
        $totalen['waardeEUR'] += $data['waardeEUR'];
        $totalen['percentage'] += $data['percentage'];
      }
    }
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('','', 'SUB', 'SUB');
    $this->pdf->row(array('','Totaal', $this->formatGetal($totalen['waardeEUR'], 0), $this->formatGetal($totalen['percentage']*100, 2)));
    $this->pdf->excelData[]=array('Totaal', round($totalen['waardeEUR'], 0), round($totalen['percentage'], 2));
    $this->pdf->excelData[]=array();
    $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
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

    $this->pdf->Cell(50,4,vertaalTekst($title, $this->pdf->rapport_taal),0,0,"C");
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
    /*
          for($i=0; $i<$this->pdf->NbVal; $i++) {
              $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
              $this->pdf->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
              $this->pdf->SetXY($x2,$y1);
              $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
              $y1+=$hLegend + 2;
          }
    */
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

}
?>
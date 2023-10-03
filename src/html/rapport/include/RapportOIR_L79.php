<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/10/23 13:34:01 $
 		File Versie					: $Revision: 1.5 $

 		$Log: RapportOIR_L79.php,v $
 		Revision 1.5  2019/10/23 13:34:01  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2019/10/19 08:15:15  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2019/10/18 17:40:37  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2019/10/16 15:23:48  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2019/10/09 15:11:04  rvv
 		*** empty log message ***
 		

 
*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIR_L79
{
  function RapportOIR_L79($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "OIR";
    $this->pdf->rapport_deel = 'overzicht';
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->RapStartJaar = date("Y", $this->pdf->rapport_datumvanaf);
    if (is_array($this->pdf->portefeuilles))
    {
      $this->portefeuilles = $this->pdf->portefeuilles;
    }
    else
    {
      $this->portefeuilles = array($portefeuille);
    }
    
    
    $this->pdf->rapport_titel = "Overzicht beleggingen";
    
    
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
    
    
    $this->waarden = array();
    $this->subtotalen = array();
    $this->totalen = array();
    $this->categorieOmschrijving = array();
    $this->totaleWaarde=0;
    $this->totaleWaardeBegin=0;
    $this->totaleWaardeBez=0;
    $this->pdf->underlinePercentage=0.8;
    
  }
  
  function tweedeStart()
  {
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    if ((db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf)) || (db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01")))
    {
      $this->tweedePerformanceStart = substr($this->pdf->PortefeuilleStartdatum, 0, 10);
    }
    else
    {
      $this->tweedePerformanceStart = "$RapStartJaar-01-01";
    }
  }
  
  function formatGetal($waarde, $dec, $percent = false, $limit = false, $nulTonen = false)
  {
    if ($waarde == '')
    {
      if ($nulTonen == false)
      {
        return '';
      }
    }
    if (round($waarde, 2) != 0.00 || $percent == true)
    {
      if ($percent == true)
      {
        if ($limit)
        { //echo "$waarde <br>";
          if ($waarde >= $limit || $waarde <= $limit * -1)
          {
            return "p.m.";
          }
        }
        
        return number_format($waarde, $dec, ",", ".") . '%';
      }
      
      else
      {
        return number_format($waarde, $dec, ",", ".");
      }
    }
  }
  
  function formatGetalKoers($waarde, $dec, $percent = false, $limit = false, $start = false)
  {
    if ($start == false)
    {
      $waarde = $waarde / $this->pdf->ValutaKoersEind;
    }
    else
    {
      $waarde = $waarde / $this->pdf->ValutaKoersStart;
    }
    
    return $this->formatGetal($waarde, $dec, $percent = false, $limit = false);
    //return number_format($waarde,$dec,",",".");
  }
  
  function formatAantal($waarde, $dec, $VierDecimalenZonderNullen = false)
  {
    
    if ($VierDecimalenZonderNullen)
    {
      
      $getal = explode('.', $waarde);
      $decimaalDeel = $getal[1];
      if ($decimaalDeel != '0000')
      {
        for ($i = strlen($decimaalDeel); $i >= 0; $i--)
        {
          $decimaal = $decimaalDeel[$i - 1];
          if ($decimaal != '0' && !$newDec)
          {
            //  echo $this->portefeuille." $waarde <br>";exit;
            $newDec = $i;
          }
        }
        
        return number_format($waarde, $newDec, ",", ".");
      }
      else
      {
        return number_format($waarde, $dec, ",", ".");
      }
    }
    else
    {
      return number_format($waarde, $dec, ",", ".");
    }
  }
  
  function writeRapport()
  {
    global $__appvar;
    $gebruikteCrmVelden = array(
      'Portefeuillesoort',
      'PortefeuilleNaam');
  
    $this->pdf->addPage();
    $this->pdf->ln();
    $yStart=$this->pdf->getY();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
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
    
    /*
    $portefeuilleRendement = array();
    foreach ($this->portefeuilles as $portefeuille)
    {
      $portefeuilleRendement[$portefeuille] = performanceMeting($portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'], $this->pdf->rapportageValuta);
    }
    */
    
    //$fondsRegelsPerPortefeuilleEind = array();
    //$fondsRegelsPerPortefeuilleStart = array();
    $fondsRegelsPerPortefeuille = array();
    
    foreach ($this->portefeuilles as $portefeuille)
    {
      $mutatieLijstPerPortefeuille[$portefeuille]=$this->genereerMutatieLijst($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
      $opbrengstenPerPortefeuille[$portefeuille]=$this->getFondsKostenInkomsten($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
  
      $fondsRegels = berekenPortefeuilleWaarde($portefeuille, $this->rapportageDatum, (substr($this->rapportageDatum, 5, 5) == '01-01')?true:false, $this->pdf->rapportageValuta, $this->rapportageDatumVanaf);
      //$fondsRegelsPerPortefeuilleEind[$portefeuille] = $fondsRegels;
      foreach($fondsRegels as $fondsregel)
      {
        $key=$fondsregel['type']."|".$fondsregel['fonds']."|".$fondsregel['rekening'];
        $fondsRegelsPerPortefeuille[$portefeuille][$key]=$fondsregel;
      }
      
      $fondsRegels = berekenPortefeuilleWaarde($portefeuille, $this->rapportageDatumVanaf, (substr($this->rapportageDatumVanaf, 5, 5) == '01-01')?true:false, $this->pdf->rapportageValuta, $this->rapportageDatumVanaf);
      //$fondsRegelsPerPortefeuilleStart[$portefeuille] = $fondsRegels;
      foreach($fondsRegels as $fondsregel)
      {
        $key=$fondsregel['type']."|".$fondsregel['fonds']."|".$fondsregel['rekening'];
        if(!isset($fondsRegelsPerPortefeuille[$portefeuille][$key]))
        {
          $fondsRegelsPerPortefeuille[$portefeuille][$key] = $fondsregel;
          $fondsRegelsPerPortefeuille[$portefeuille][$key]['actuelePortefeuilleWaardeEuro'] =0;
        }
        $fondsRegelsPerPortefeuille[$portefeuille][$key]['werkelijkeBeginPortefeuilleWaardeEuro']=$fondsregel['actuelePortefeuilleWaardeEuro'];
      }
    }

    $portefeuilleDetails = array();
    $this->totaleWaarde=0;
    $this->totaleWaardeBegin=0;
    $variabelen=array('actuelePortefeuilleWaardeEuro','werkelijkeBeginPortefeuilleWaardeEuro');
    foreach ($this->portefeuilles as $portefeuille)
    {
      $query = "SELECT Portefeuilles.portefeuille, Portefeuilles.Clientvermogensbeheerder $nawSelect FROM Portefeuilles LEFT JOIN CRM_naw ON Portefeuilles.portefeuille=CRM_naw.portefeuille WHERE Portefeuilles.portefeuille='$portefeuille' limit 1";
      $db->SQL($query);
      $pdata = $db->lookupRecord();
      $portefeuilleDetails[$portefeuille] = $pdata;
      
      
      $fondsRegels = $fondsRegelsPerPortefeuille[$portefeuille];
      $mutatieLijst=$mutatieLijstPerPortefeuille[$portefeuille];
      $kostenLijst=$opbrengstenPerPortefeuille[$portefeuille];
      //listarray($kostenLijst);
      foreach ($fondsRegels as $fondsRekeningKey=>$regel)
      {
  
       
        if ($regel['actuelePortefeuilleWaardeEuro'] < 0)
        {
          $type = 'Financiering';
          //$regel['beleggingscategorie']='Schulden';
          //$regel['beleggingscategorieOmschrijving']='Schulden';
        }
        else
        {
          $type = 'Bezittingen';
          $this->totaleWaardeBez += $regel['actuelePortefeuilleWaardeEuro'];
        }
        
        if ($pdata['Portefeuillesoort'] == 'Effecten')
        {
          /*
          if($portefeuilleDetails[$portefeuille]['PortefeuilleNaam']<>'')
          {
            $omschrijving = 'Effecten ' . $portefeuilleDetails[$portefeuille]['PortefeuilleNaam'];
          }
          else
          {
            $omschrijving = 'Effecten ' . $portefeuille;
          }
          */
          $omschrijving=$regel['beleggingscategorieOmschrijving'];
        }
        else
        {
          $omschrijving = $regel['fondsOmschrijving'];
          $width=$this->pdf->getStringWidth($omschrijving);
          
          if($width>35)
          {
            $omschrijvingLenght=strlen($omschrijving);
            for($i=$omschrijvingLenght;$i>0;$i--)
            {
              $width=$this->pdf->getStringWidth(substr($omschrijving,0,$i));
              if($width<35)
              {
                $omschrijving=substr($omschrijving,0,$i);
                break;
              }
            }
          }
        }
        
        $portefeuilleCategorie=$portefeuille;

        if ($regel['type'] == 'rekening')
        {
          $stortingen = getRekeningStortingen($regel['rekening'], $this->rapportageDatumVanaf, $this->rapportageDatum);
          $onttrekkingen = getRekeningOnttrekkingen($regel['rekening'], $this->rapportageDatumVanaf, $this->rapportageDatum);
          $stortingenOnttrekkingen = ($stortingen - $onttrekkingen);
          
          $this->waarden[$type][$portefeuilleCategorie][$regel['beleggingscategorie']][$omschrijving]['stortingenOnttrekkingen'] += $stortingenOnttrekkingen;
          $this->subtotalen[$regel['beleggingscategorie']]['stortingenOnttrekkingen'] += $stortingenOnttrekkingen;
          $this->totalen[$type]['stortingenOnttrekkingen'] += $stortingenOnttrekkingen;
        }
        elseif ($regel['type'] <> 'rekening')
        {
            $this->waarden[$type][$portefeuilleCategorie][$regel['beleggingscategorie']][$omschrijving]['stortingenOnttrekkingen'] += $mutatieLijst[$regel['fonds']][0];
            $this->subtotalen[$regel['beleggingscategorie']]['stortingenOnttrekkingen'] +=$mutatieLijst[$regel['fonds']][0];
            $this->totalen[$type]['stortingenOnttrekkingen'] +=$mutatieLijst[$regel['fonds']][0];
  
          $this->waarden[$type][$portefeuilleCategorie][$regel['beleggingscategorie']][$omschrijving]['kosten'] +=$kostenLijst[$regel['fonds']]['kosten'];
          $this->subtotalen[$regel['beleggingscategorie']]['kosten'] +=$kostenLijst[$regel['fonds']]['kosten'];
          $this->totalen[$type]['kosten'] +=$kostenLijst[$regel['fonds']]['kosten'];

  
          $this->waarden[$type][$portefeuilleCategorie][$regel['beleggingscategorie']][$omschrijving]['opbrengst'] +=$kostenLijst[$regel['fonds']]['opbrengst'];
          $this->subtotalen[$regel['beleggingscategorie']]['opbrengst'] +=$kostenLijst[$regel['fonds']]['opbrengst'];
          $this->totalen[$type]['opbrengst'] +=$kostenLijst[$regel['fonds']]['opbrengst'];
          
        }
        
        foreach($variabelen as $variabele)
        {
          $this->waarden[$type][$portefeuilleCategorie][$regel['beleggingscategorie']][$omschrijving][$variabele] += $regel[$variabele];
          $this->subtotalen[$regel['beleggingscategorie']][$variabele] += $regel[$variabele];
          $this->totalen[$type][$variabele] += $regel[$variabele];
        }
        if($pdata['Portefeuillesoort'] == 'Effecten')
        {
          $this->waarden[$type][$portefeuilleCategorie][$regel['beleggingscategorie']][$omschrijving]['geenSubtotaal'] =true;
        }
        $this->categorieOmschrijving[$regel['beleggingscategorie']] = $regel['beleggingscategorieOmschrijving'];
  
        $this->totaleWaardeBegin += $regel['werkelijkeBeginPortefeuilleWaardeEuro'];
        $this->totaleWaarde += $regel['actuelePortefeuilleWaardeEuro'];
      }
    }
  
    foreach ($this->waarden as $categorie => $details)
    {
      ksort($this->waarden[$categorie]);
    }
    $this->waarden['Financiering']['Eigen vermogen']['Totaal']['Eigen vermogen']['werkelijkeBeginPortefeuilleWaardeEuro']=$this->totaleWaardeBegin*-1;
    $this->waarden['Financiering']['Eigen vermogen']['Totaal']['Eigen vermogen']['actuelePortefeuilleWaardeEuro']=$this->totaleWaarde*-1;
    $this->waarden['Financiering']['Eigen vermogen']['Totaal']['Eigen vermogen']['geenSubtotaal']=true;
    
    $this->portefeuilleDetails=$portefeuilleDetails;

    $this->toonTabel('Bezittingen',0);
    $this->pdf->setY($yStart);
    $this->toonTabel('Financiering',142);
    
  //  listarray($this->waarden);
    
  }
  
  function toonTabel($categorie,$extraX=0)
  {
    if($categorie=='Financiering')
      $factor=-1;
    else
      $factor=1;
    $variabelen=array('actuelePortefeuilleWaardeEuro','werkelijkeBeginPortefeuilleWaardeEuro','stortingenOnttrekkingen','kosten','opbrengst','herwaardering');
    $this->pdf->setAligns(array('L','L','R','C','R','R'));
    $this->pdf->setWidths(array($extraX,38,17,21+16+11,17,17));
   // $this->pdf->Row(array('',$categorie));
    $fontsizeBackup=$this->pdf->rapport_fontsize;
    $this->pdf->rapport_fontsize=$this->pdf->rapport_fontsize-1;
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge+$extraX, $this->pdf->getY(), 138 ,8, 'F');
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('',$categorie,'Waardering','Resultaat','Storting/','Waardering'));
    $this->pdf->setAligns(array('L','L','R','R','R','R','R','R'));
  	$this->pdf->setWidths(array($extraX,38,17,21,16,11,17,17));
    $this->pdf->Row(array('','',date('d-m-Y',db2jul($this->rapportageDatumVanaf)),'Herwaardering','Inkomsten','Kosten','Onttrekking',date('d-m-Y',db2jul($this->rapportageDatum))));
    
    $this->pdf->SetTextColor(0,0,0);
  
    $totalen=array();
    $subtotalen=array();
    foreach ($this->waarden[$categorie] as $portefeuille => $regels)
    {
      if($portefeuille=='')
        continue;


      foreach ($regels as $categorieNaam => $categorieDetails)
      {
        if($categorieNaam=='')
          continue;
        //$omschrijving= $this->categorieOmschrijving[$categorieNaam];
        //$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
        //$this->pdf->Row(array('',$omschrijving));
        //$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

        $portefeuilleSubtotaal=array();
        foreach($categorieDetails as $omschrijving=>$details)
        {
          $details['herwaardering'] = ($details['actuelePortefeuilleWaardeEuro'] - $details['werkelijkeBeginPortefeuilleWaardeEuro'] - $details['stortingenOnttrekkingen'] - $details['opbrengst'] - $details['kosten'])*$factor;
          foreach ($variabelen as $variabele)
          {
            $details[$variabele] = ($details[$variabele] / 1000);
    
            $totalen[$variabele] += $details[$variabele];
    
            $subtotalen[$variabele] += $details[$variabele];
          }
          //listarray($omschrijving);  listarray($details);
  
          $this->pdf->Row(array('', $omschrijving,
                            $this->formatGetal($details['werkelijkeBeginPortefeuilleWaardeEuro']*$factor, 0),
                            $this->formatGetal($details['herwaardering'], 0),
                            $this->formatGetal($details['opbrengst'], 0),
                            $this->formatGetal($details['kosten'], 0),
                            $this->formatGetal($details['stortingenOnttrekkingen'], 0),
                            $this->formatGetal($details['actuelePortefeuilleWaardeEuro']*$factor, 0)));
  
          if($details['geenSubtotaal']==false)
          {
            foreach ($details as $key => $value)
            {
              $portefeuilleSubtotaal[$key] += $value;
            }
          }
  
        }
        if(count($portefeuilleSubtotaal)>0)// && count($this->portefeuilles)>1)
        {
          $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);



          $this->pdf->Row(array('', 'Totaal '.$this->categorieOmschrijving[$categorieNaam],
                            $this->formatGetal($portefeuilleSubtotaal['werkelijkeBeginPortefeuilleWaardeEuro']*$factor, 0),
                            $this->formatGetal($portefeuilleSubtotaal['herwaardering'], 0),
                            $this->formatGetal($portefeuilleSubtotaal['opbrengst'], 0),
                            $this->formatGetal($portefeuilleSubtotaal['kosten'], 0),
                            $this->formatGetal($portefeuilleSubtotaal['stortingenOnttrekkingen'], 0),
                            $this->formatGetal($portefeuilleSubtotaal['actuelePortefeuilleWaardeEuro']*$factor, 0)));
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        }
      }

      if($this->portefeuilleDetails[$portefeuille]['PortefeuilleNaam']<>'')
        $omschrijving=$this->portefeuilleDetails[$portefeuille]['PortefeuilleNaam'];
      else
        $omschrijving=$portefeuille;

      $this->pdf->CellBorders=array('','T','T','T','T','T','T','T');
      $this->pdf->SetFillColor(226,238,241);
      $this->pdf->Rect($this->pdf->marge+$extraX, $this->pdf->getY(), 138, $this->pdf->rowHeight , 'F');
      $this->pdf->Row(array('','Subtotaal '.$omschrijving,
                        $this->formatGetal($subtotalen['werkelijkeBeginPortefeuilleWaardeEuro']*$factor,0),
                        $this->formatGetal($subtotalen['herwaardering'],0),
                        $this->formatGetal($subtotalen['opbrengst'],0),
                        $this->formatGetal($subtotalen['kosten'],0),
                        $this->formatGetal($subtotalen['stortingenOnttrekkingen'],0),
                        $this->formatGetal($subtotalen['actuelePortefeuilleWaardeEuro']*$factor,0)));
      $subtotalen=array();
      unset($this->pdf->CellBorders);
      
    }
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor(137,185,198);
    $this->pdf->Rect($this->pdf->marge+$extraX, $this->pdf->getY(),138, $this->pdf->rowHeight*2 , 'F');
    $this->pdf->SetTextColor(255);
    $this->pdf->ln(2);
    $this->pdf->Row(array('','Totaal',
                      $this->formatGetal($totalen['werkelijkeBeginPortefeuilleWaardeEuro']*$factor,0),
                      $this->formatGetal($totalen['herwaardering'],0),
                      $this->formatGetal($totalen['opbrengst'],0),
                      $this->formatGetal($totalen['kosten'],0),
                      $this->formatGetal($totalen['stortingenOnttrekkingen'],0),
                      $this->formatGetal($totalen['actuelePortefeuilleWaardeEuro']*$factor,0)));
    unset($this->pdf->CellBorders);
    
    /*
        if(1==0)
        {
        $grandtotaal = "grandtotaal";
        $this->pdf->SetFillColor(137,185,198);
        $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
        
        $this->pdf->line($this->pdf->marge,$this->pdf->getY(),297-$this->pdf->marge,$this->pdf->getY());
        $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 297-$this->pdf->marge*2, $this->pdf->rowHeight*2 , 'F');
        $this->pdf->ln(2);
        $this->pdf->SetTextColor(255);
        
      }
    else
    {
    $grandtotaal = "totaal";
    $this->pdf->SetFillColor(226,238,241);
    $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 297-$this->pdf->marge*2, $this->pdf->rowHeight , 'F');
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->line($this->pdf->marge,$this->pdf->getY(),297-$this->pdf->marge,$this->pdf->getY());
      }
    */
  
    $this->pdf->rapport_fontsize=$fontsizeBackup;
  }
  
  function getFondsKostenInkomsten($portefeuille,$rapportageDatumVanaf,$rapportageDatum)
  {
    // loopje over Grootboekrekeningen Opbrengsten = 1
    if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    else
      $koersQuery = "";
  
    $query="
    SELECT
	Rekeningmutaties.Fonds,
Grootboekrekeningen.kosten ,
Grootboekrekeningen.opbrengst ,
SUM((Rekeningmutaties.Credit-Rekeningmutaties.Debet)*Rekeningmutaties.Valutakoers) $koersQuery AS waarde
FROM
		Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
WHERE Rekeningen.Portefeuille = '".$portefeuille."'
AND Rekeningmutaties.Verwerkt = '1'
AND Rekeningmutaties.Fonds <> ''
AND (Grootboekrekeningen.kosten = '1' OR Grootboekrekeningen.Opbrengst = '1')
AND Rekeningmutaties.Boekdatum > '$rapportageDatumVanaf'
AND Rekeningmutaties.Boekdatum <= '$rapportageDatum'
GROUP BY
	Rekeningmutaties.Fonds,
Grootboekrekeningen.kosten ,
Grootboekrekeningen.opbrengst
ORDER BY
	Rekeningmutaties.Boekdatum,
	Rekeningmutaties.Fonds,
	Rekeningmutaties.id";
    
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();

    $waardenPerFonds = array();
  
    while($mutaties = $DB->nextRecord())
    {
      if($mutaties['kosten']==1)
        $waardenPerFonds[$mutaties['Fonds']]['kosten'] += $mutaties['waarde'];
      if($mutaties['opbrengst']==1)
        $waardenPerFonds[$mutaties['Fonds']]['opbrengst'] += $mutaties['waarde'];
    }
    return $waardenPerFonds;
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
  
}
?>
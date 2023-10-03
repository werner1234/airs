<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/07/03 15:37:22 $
 		File Versie					: $Revision: 1.1 $

 		$Log: RapportOIH_L79.php,v $
 		Revision 1.1  2019/07/03 15:37:22  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2019/06/22 16:33:34  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2019/06/12 15:23:56  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2019/06/09 14:53:29  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2019/06/05 16:39:26  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2019/06/02 10:04:16  rvv
 		*** empty log message ***
 		
 
*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIH_L79
{
  function RapportOIH_L79($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "OIH";
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
    
    $portefeuilleRendement = array();
    foreach ($this->portefeuilles as $portefeuille)
    {
      $portefeuilleRendement[$portefeuille] = performanceMeting($portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'], $this->pdf->rapportageValuta);
    }
    
    
    $fondsRegelsPerPortefeuille = array();
    
    foreach ($this->portefeuilles as $portefeuille)
    {
      
      $fondsRegels = berekenPortefeuilleWaarde($portefeuille, $this->rapportageDatum, (substr($this->rapportageDatum, 5, 5) == '01-01')?true:false, $this->pdf->rapportageValuta, $this->rapportageDatumVanaf);
      $fondsRegelsPerPortefeuille[$portefeuille] = $fondsRegels;
    }
    
    $portefeuilleDetails = array();
    $totaleWaarde=0;
    foreach ($this->portefeuilles as $portefeuille)
    {
      $query = "SELECT Portefeuilles.portefeuille, Portefeuilles.Clientvermogensbeheerder $nawSelect FROM Portefeuilles LEFT JOIN CRM_naw ON Portefeuilles.portefeuille=CRM_naw.portefeuille WHERE Portefeuilles.portefeuille='$portefeuille' limit 1";
      $db->SQL($query);
      $pdata = $db->lookupRecord();
      $portefeuilleDetails[$portefeuille] = $pdata;
      
      
      $fondsRegels = $fondsRegelsPerPortefeuille[$portefeuille];
      foreach ($fondsRegels as $regel)
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
          $omschrijving = 'Effecten ' . $portefeuille;
        }
        else
        {
          $omschrijving = $regel['fondsOmschrijving'];
        }
        
        $this->waarden[$type][$regel['beleggingscategorie']][$omschrijving]['actuelePortefeuilleWaardeEuro'] += $regel['actuelePortefeuilleWaardeEuro'];
        $this->categorieOmschrijving[$regel['beleggingscategorie']] = $regel['beleggingscategorieOmschrijving'];
        $this->subtotalen[$regel['beleggingscategorie']]['actuelePortefeuilleWaardeEuro'] += $regel['actuelePortefeuilleWaardeEuro'];
        $this->totalen[$type]['actuelePortefeuilleWaardeEuro'] += $regel['actuelePortefeuilleWaardeEuro'];
        
        $this->totaleWaarde += $regel['actuelePortefeuilleWaardeEuro'];

      }
      
    }
  
    foreach ($this->waarden as $categorie => $details)
    {
      ksort($this->waarden[$categorie]);
    }
    $this->waarden['Financiering']['Eigen vermogen']['Eigen vermogen']['actuelePortefeuilleWaardeEuro']=$this->totaleWaarde;
    
    $this->pdf->addPage();

    $yStart=$this->pdf->getY();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->toonTabel('Bezittingen',0);
    $this->pdf->setY($yStart);
    $this->toonTabel('Financiering',130);
    
  //  listarray($this->waarden);
    
  }
  
  function toonTabel($categorie,$extraX=0)
  {
  	$this->pdf->setWidths(array($extraX,40,40,40));
  	$this->pdf->setAligns(array('L','L','R','R'));
  
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->ln();
  
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge+$extraX, $this->pdf->getY(), 120 ,4, 'F');
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
  
    $this->pdf->Row(array('',$categorie,'Waarde in euro','Gewicht %'));
    
    $this->pdf->SetTextColor(0,0,0);
  
    $totalen=array();
    $subtotalen=array();
    foreach ($this->waarden[$categorie] as $categorie => $regels)
    {
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->Row(array('',$this->categorieOmschrijving[$categorie]));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      foreach ($regels as $omschrijving => $details)
      {
        $waarde=abs($details['actuelePortefeuilleWaardeEuro']);
      	$percentage=$waarde/$this->totaleWaardeBez*100;

        $this->pdf->Row(array('',$omschrijving,$this->formatGetal($waarde,0),$this->formatGetal($percentage,2)));
  
        $totalen['actuelePortefeuilleWaardeEuro']+=$waarde;
        $totalen['percentage']+=$percentage;
        $subtotalen['actuelePortefeuilleWaardeEuro']+=$waarde;
        $subtotalen['percentage']+=$percentage;
        
      }
      $this->pdf->CellBorders=array('','T','T','T');
      $this->pdf->SetFillColor(226,238,241);
      $this->pdf->Rect($this->pdf->marge+$extraX, $this->pdf->getY(), 120, $this->pdf->rowHeight , 'F');
      $this->pdf->Row(array('','Subtotaal ',$this->formatGetal($subtotalen['actuelePortefeuilleWaardeEuro'],0),$this->formatGetal($subtotalen['percentage'],2)));
      $subtotalen=array();
      unset($this->pdf->CellBorders);
      
    }
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor(137,185,198);
    $this->pdf->Rect($this->pdf->marge+$extraX, $this->pdf->getY(),120, $this->pdf->rowHeight*2 , 'F');
    $this->pdf->SetTextColor(255);
    $this->pdf->ln(2);
    $this->pdf->Row(array('','Totaal',$this->formatGetal($totalen['actuelePortefeuilleWaardeEuro'],0),$this->formatGetal($totalen['percentage'],2)));
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
    
    
  }
  
}
?>
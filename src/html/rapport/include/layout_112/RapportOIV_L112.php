<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/04 14:09:21 $
File Versie					: $Revision: 1.1 $

$Log: RapportVOLK_L93.php,v $


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIV_L112
{
  function RapportOIV_L112($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "OIV";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->pdf->rapport_titel = "Valutaverdeling";
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
  
    $this->valutaOmschrijvingen=array();
    $this->valutaVolgorde=array();
    $this->grafiekdata=array();
    $this->categorieVolgorde=array();
    $this->gegevens=array();
    $this->valutaKoers=array();
    $this->categorieOmschrijvingen=array();
    $this->fwdWaarden=array();
  }
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde, $dec, ",", ".");
  }
  
  function getVerdeling($fonds,$valuta)
  {
    $db=new DB();
    if($fonds=='')
    {
      return array($valuta=>1);
    }
    else
    {
      $msData=array();
      $query = "SELECT datumVanaf,weging,valuta FROM doorkijk_categorieWegingenPerFonds WHERE msCategoriesoort='Valutas' AND Fonds='".mysql_real_escape_string($fonds)."' AND datumVanaf<'".$this->rapportageDatum ."' ORDER BY datumVanaf desc limit 1";
      $db->SQL($query);
      $datum=$db->lookupRecord();
      if($datum['datumVanaf']=='')
        return array($valuta=>1);
      
//      $query = "SELECT datumProvider, (weging/100) as weging, msCategorie FROM doorkijk_categorieWegingenPerFonds
//JOIN doorkijk_koppelingPerVermogensbeheerder ON doorkijk_categorieWegingenPerFonds.msCategorie=doorkijk_koppelingPerVermogensbeheerder.doorkijkCategorie AND
//doorkijk_koppelingPerVermogensbeheerder.doorkijkCategoriesoort='Valutas' AND doorkijk_koppelingPerVermogensbeheerder.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND doorkijk_koppelingPerVermogensbeheerder.systeem='MS'
//WHERE msCategoriesoort='Valutas' AND Fonds='".mysql_real_escape_string($fonds)."' AND datumVanaf='".$datum['datumVanaf']."'  ";
  //if(msCategorie='Overig','$valuta',msCategorie) as
      $query = "SELECT datumProvider, (weging/100) as weging, valuta FROM doorkijk_categorieWegingenPerFonds
WHERE msCategoriesoort='Valutas' AND Fonds='".mysql_real_escape_string($fonds)."' AND datumVanaf='".$datum['datumVanaf']."'  ";
      
      $db->SQL($query);
      $db->Query();

      while($data=$db->nextRecord())
      {
        $msData[$data['valuta']]=+$data['weging'];
      }

      if(round(array_sum($msData),2)<>1)
      {
        $msData[$valuta]+=(1-array_sum($msData));
      }
    }
    if(count($msData)>0)
    {
      return $msData;
    }
    else
    {
      return array($valuta=>1);
    }
    
  }
  
  function getData()
  {
    global $__appvar;
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $kleuren = unserialize($kleuren['grafiek_kleur']);
  
    $query="SELECT valuta,Omschrijving,Afdrukvolgorde as valutaVolgorde FROM Valutas order by Afdrukvolgorde";
    $DB->SQL($query);
    $DB->Query();
   
  
    while($data=$DB->nextRecord())
    {
      $this->valutaVolgorde[$data['valuta']]=$data['valutaVolgorde'];
      $this->valutaOmschrijvingen[$data['valuta']]=$data['Omschrijving'];
    
      $this->grafiekdata['valuta']['percentage'][$data['Omschrijving']] = 0;
      $this->grafiekdata['valuta']['kleuren'][$data['Omschrijving']] = array();
    }
  
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];
  
    $query = "SELECT (actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaalEur,
    (actuelePortefeuilleWaardeInValuta) / ".$this->pdf->ValutaKoersEind. " AS totaalValuta,
    TijdelijkeRapportage.beleggingscategorie,
    TijdelijkeRapportage.Fonds,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.actueleValuta as valutaKoers,
TijdelijkeRapportage.beleggingscategorieVolgorde,
TijdelijkeRapportage.valutaVolgorde,
TijdelijkeRapportage.beleggingscategorieOmschrijving,
TijdelijkeRapportage.valutaOmschrijving,
(if(Fondsen.forward=1,actuelePortefeuilleWaardeInValuta,0)) as fwdValuta,
(if(Fondsen.forward=1,actuelePortefeuilleWaardeInValuta,0)) as fwdEur,
(if(Fondsen.forward=1,totaalAantal,0)) as fwdAantal
      FROM TijdelijkeRapportage
       LEFT JOIN Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
       WHERE
       TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatum."' AND
       TijdelijkeRapportage.portefeuille = '".$this->portefeuille."'"
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      "
ORDER BY TijdelijkeRapportage.valutaVolgorde, TijdelijkeRapportage.beleggingscategorieVolgorde,TijdelijkeRapportage.Fonds";
    debugSpecial($query,__FILE__,__LINE__);
  
    $DB->SQL($query);
    $DB->Query();
  
   
    while($data=$DB->nextRecord())
    {
      if($data['fwdEur']<>0)
      {
        $this->fwdWaarden[$data['valuta']]['valuta'] += $data['fwdValuta'];
        $this->fwdWaarden[$data['valuta']]['eur'] += $data['fwdEur'];
        $this->fwdWaarden[$data['valuta']]['aantal'] += $data['fwdAantal'];
      }
    
      $this->gegevens['Totaal'][$data['beleggingscategorie']]['totaalEur']+=$data['totaalEur'];
      $this->gegevens['Totaal']['Totaal']['totaalEur']+=$data['totaalEur'];
    
      $this->categorieVolgorde[$data['beleggingscategorie']]=$data['beleggingscategorieVolgorde'];
      $this->categorieOmschrijvingen[$data['beleggingscategorie']]=$data['beleggingscategorieOmschrijving'];
    
      $this->grafiekdata['beleggingscategorie']['percentage'][$data['beleggingscategorieOmschrijving']]+=$data['totaalEur']/$totaalWaarde*100;
      $this->grafiekdata['beleggingscategorie']['kleuren'][$data['beleggingscategorieOmschrijving']]=array($kleuren['OIB'][$data['beleggingscategorie']]['R']['value'],$kleuren['OIB'][$data['beleggingscategorie']]['G']['value'],$kleuren['OIB'][$data['beleggingscategorie']]['B']['value']);
    
    
      $this->valutaKoers[$data['valuta']] = $data['valutaKoers'];
      $verdeling=$this->getVerdeling($data['Fonds'],$data['valuta']);
    
      foreach($verdeling as $valuta=>$percentage)
      {
        if(!isset($this->valutaKoers[$valuta]))
          $this->valutaKoers[$valuta]=globalGetValutaKoers($valuta,$this->rapportageDatum);
      
        $data['valutaOmschrijving']=$this->valutaOmschrijvingen[$valuta];
      
        $this->gegevens[$valuta][$data['beleggingscategorie']]['totaalEur'] += $data['totaalEur']*$percentage;
        $this->gegevens[$valuta][$data['beleggingscategorie']]['totaalValuta'] += $data['totaalEur']/$this->valutaKoers[$valuta]*$percentage;
        $this->gegevens[$valuta]['TotaalValuta']['totaalValuta'] += $data['totaalEur']/$this->valutaKoers[$valuta]*$percentage;
        $this->gegevens[$valuta]['Totaal']['totaalEur'] += $data['totaalEur']*$percentage;
      
      
        $this->grafiekdata['valuta']['percentage'][$data['valutaOmschrijving']] += (($data['totaalEur']*$percentage) / $totaalWaarde * 100);
      
        $this->grafiekdata['valuta']['kleuren'][$data['valutaOmschrijving']] = array($kleuren['OIV'][$valuta]['R']['value'], $kleuren['OIV'][$valuta]['G']['value'], $kleuren['OIV'][$valuta]['B']['value']);
      }
    
    }

//		listarray($this->grafiekdata);
    asort($this->valutaVolgorde);
    asort($this->categorieVolgorde);
    $this->valutaVolgorde['Totaal']=500;
    $this->categorieVolgorde['TotaalValuta']=500;
    $this->categorieVolgorde['Totaal']=501;
    $this->valutaKoers['Totaal']=1;
    $this->valutaOmschrijvingen['Totaal']='Totaal in EUR';
    $this->categorieOmschrijvingen['TotaalValuta']='Totaal in Valuta';
    $this->categorieOmschrijvingen['Totaal']='Totaal in EUR';
    foreach($this->valutaVolgorde as $valuta=>$Vvolgorde)
    {
      if (!isset($this->valutaKoers[$valuta]))
      {
        $valutaOmschrijving = $this->valutaOmschrijvingen[$valuta];
        unset($this->grafiekdata['valuta']['percentage'][$valutaOmschrijving]);
        unset($this->grafiekdata['valuta']['kleuren'][$valutaOmschrijving]);
        continue;
      }
    }
  }
  
  
  function writeRapport()
  {
    global $__appvar;
    
    $query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '" . $this->portefeuille . "' AND Portefeuilles.Client = Clienten.Client ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $this->portefeuilledata = $DB->nextRecord();
    
    
    $this->pdf->widthB = array(5, 55, 18, 18, 20, 21, 1, 17, 21, 21, 18, 18, 18, 18, 12);
    $this->pdf->widthA = array(60, 18, 18, 20, 21, 1, 17, 21, 21, 18, 18, 18, 18, 12);
    $this->pdf->alignB = array('R', 'L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R');
    $this->pdf->alignA = array('L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R');
    
    
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas'] = $this->pdf->page;
    $this->getData();
   

    
  
    $header=array('Valuta','Valutakoers');
    $catWitdth=$this->pdf->w-$this->pdf->marge*2-70;
    $w=$catWitdth/count($this->categorieVolgorde);
    $headerWidths=array(50,20);
    $headerAligns=array('L','R');
    $underline=array('U','U');
    $topUnderline=array('T','T');
    foreach ($this->categorieVolgorde as $categorie=>$volgorde)
		{
			$headerWidths[]=$w;
      $headerAligns[]='R';
		  $header[]=$this->categorieOmschrijvingen[$categorie]."\n%";
      $underline[]='U';
      $topUnderline[]='T';
		}
		
		$this->pdf->setWidths($headerWidths);
    $this->pdf->setAligns($headerAligns);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders=$underline;
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->row($header);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach($this->valutaVolgorde as $valuta=>$Vvolgorde)
		{
		  if(!isset($this->valutaKoers[$valuta]))
      {
        $valutaOmschrijving=$this->valutaOmschrijvingen[$valuta];
        unset($this->grafiekdata['valuta']['percentage'][$valutaOmschrijving]);
        unset($this->grafiekdata['valuta']['kleuren'][$valutaOmschrijving]);
        continue;
      }
		  
			$row=array($this->valutaOmschrijvingen[$valuta],$this->formatGetal($this->valutaKoers[$valuta],4));
      $rowProcent=array('','');
			foreach($this->categorieVolgorde as $categorie=>$cVolgorde)
			{
        if($categorie=='TotaalValuta'&&$valuta=='Totaal')
          $row[]='';
				elseif($categorie=='Totaal'||$valuta=='Totaal')
				  $row[]=$this->formatGetal($this->gegevens[$valuta][$categorie]['totaalEur'],2);
				else
          $row[]=$this->formatGetal($this->gegevens[$valuta][$categorie]['totaalValuta'],2);
        if($categorie=='TotaalValuta')
          $rowProcent[]='';
        else
          $rowProcent[]=$this->formatGetal($this->gegevens[$valuta][$categorie]['totaalEur']/ $this->gegevens['Totaal']['Totaal']['totaalEur']*100,2).'%';
			}
			if($valuta=='Totaal')
      {
        $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
        $this->pdf->CellBorders=$topUnderline;
      }
			$this->pdf->Row($row);
      if($valuta=='Totaal')
        $this->pdf->CellBorders=$underline;
      $this->pdf->Row($rowProcent);
		}
		unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    if(count($this->fwdWaarden)>0)
    {
      $this->pdf->ln();
      foreach($this->fwdWaarden as $valuta=>$fwdWaarde)
      {
        $this->pdf->row(array('Totale forward positie',$valuta,$this->formatGetal($fwdWaarde['aantal'],0)));
        $this->pdf->row(array('Totale vermogen in valuta',$valuta,$this->formatGetal($this->gegevens[$valuta]['TotaalValuta']['totaalValuta'],0)));
        $this->pdf->ln();
        $this->pdf->row(array('Vermogen niet gehedgd',$valuta,$this->formatGetal($this->gegevens[$valuta]['TotaalValuta']['totaalValuta']+$fwdWaarde['aantal'],0)));
        $this->pdf->row(array('Hedge ratio',$valuta,$this->formatGetal(abs(100*($fwdWaarde['aantal']/$this->gegevens[$valuta]['TotaalValuta']['totaalValuta'])),2).'%'));
        $this->pdf->ln();
      }
    }
    //exit;
  
    //listarray($this->grafiekdata);
    if($this->pdf->getY()>100)
    {
      $this->pdf->addPage();
      $ystart=$this->pdf->getY()+10;
    }
    else
    {
      $ystart=108;
    }
    $this->pdf->setXY(50, $ystart);
    if(min($this->grafiekdata['valuta']['percentage']) >=0)
    {
      $this->pdf->MultiCell(50, 10, 'Valutaverdeling', 0, "C");
      $this->pdf->setXY(50, $ystart + 7);
      $this->pdf->PieChart(100, 50, $this->grafiekdata['valuta']['percentage'], '%l (%p)', array_values($this->grafiekdata['valuta']['kleuren']));
    }
    $this->pdf->setXY(180,$ystart);
    if(min($this->grafiekdata['beleggingscategorie']['percentage']) >=0)
    {
      $this->pdf->MultiCell(50, 10, 'Categorieverdeling', 0, "C");
      $this->pdf->setXY(180, $ystart + 7);
      $this->pdf->PieChart(100, 50, $this->grafiekdata['beleggingscategorie']['percentage'], '%l (%p)', array_values($this->grafiekdata['beleggingscategorie']['kleuren']));
    }
  }
}
?>

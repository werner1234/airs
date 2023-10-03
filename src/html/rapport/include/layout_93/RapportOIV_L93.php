<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/04 14:09:21 $
File Versie					: $Revision: 1.1 $

$Log: RapportVOLK_L93.php,v $


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIV_L93
{
  function RapportOIV_L93($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "OIV";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->pdf->rapport_titel = "Valutaverdeling";
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
  }
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde, $dec, ",", ".");
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
  
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $kleuren = unserialize($kleuren['grafiek_kleur']);
  
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
  
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaalEur,
    SUM(actuelePortefeuilleWaardeInValuta) / ".$this->pdf->ValutaKoersEind. " AS totaalValuta,
    TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.actueleValuta as valutaKoers,
TijdelijkeRapportage.beleggingscategorieVolgorde,
TijdelijkeRapportage.valutaVolgorde,
TijdelijkeRapportage.beleggingscategorieOmschrijving,
TijdelijkeRapportage.valutaOmschrijving
      FROM TijdelijkeRapportage WHERE
       rapportageDatum ='".$this->rapportageDatum."' AND
       portefeuille = '".$this->portefeuille."'"
      .$__appvar['TijdelijkeRapportageMaakUniek'].
		"GROUP BY TijdelijkeRapportage.valuta,TijdelijkeRapportage.beleggingscategorie
ORDER BY TijdelijkeRapportage.valutaVolgorde, TijdelijkeRapportage.beleggingscategorieVolgorde";
    debugSpecial($query,__FILE__,__LINE__);
    
    $DB->SQL($query);
    $DB->Query();
    $valutaVolgorde=array();
    $categorieVolgorde=array();
    $gegevens=array();
    $valutaKoers=array();
    $valutaOmschrijvingen=array();
    $categorieOmschrijvingen=array();
    $grafiekdata=array();
    while($data=$DB->nextRecord())
		{
		  $gegevens[$data['valuta']][$data['beleggingscategorie']]=$data;
      $gegevens[$data['valuta']]['TotaalValuta']['totaalValuta']+=$data['totaalValuta'];
      $gegevens[$data['valuta']]['Totaal']['totaalEur']+=$data['totaalEur'];
      $gegevens['Totaal'][$data['beleggingscategorie']]['totaalEur']+=$data['totaalEur'];
      $gegevens['Totaal']['Totaal']['totaalEur']+=$data['totaalEur'];
      
      $valutaVolgorde[$data['valuta']]=$data['valutaVolgorde'];
      $valutaOmschrijvingen[$data['valuta']]=$data['valutaOmschrijving'];
      $valutaKoers[$data['valuta']]=$data['valutaKoers'];
      $categorieVolgorde[$data['beleggingscategorie']]=$data['beleggingscategorieVolgorde'];
      $categorieOmschrijvingen[$data['beleggingscategorie']]=$data['beleggingscategorieOmschrijving'];
      
      

      $grafiekdata['valuta']['percentage'][$data['valutaOmschrijving']]+=$data['totaalEur']/$totaalWaarde*100;
      $grafiekdata['valuta']['kleuren'][$data['valutaOmschrijving']]=array($kleuren['OIV'][$data['valuta']]['R']['value'],$kleuren['OIV'][$data['valuta']]['G']['value'],$kleuren['OIV'][$data['valuta']]['B']['value']);
      
      
      $grafiekdata['beleggingscategorie']['percentage'][$data['beleggingscategorieOmschrijving']]+=$data['totaalEur']/$totaalWaarde*100;
      $grafiekdata['beleggingscategorie']['kleuren'][$data['beleggingscategorieOmschrijving']]=array($kleuren['OIB'][$data['beleggingscategorie']]['R']['value'],$kleuren['OIB'][$data['beleggingscategorie']]['G']['value'],$kleuren['OIB'][$data['beleggingscategorie']]['B']['value']);
		}
		//listarray($grafiekdata);
		asort($valutaVolgorde);
    asort($categorieVolgorde);
    $valutaVolgorde['Totaal']=500;
    $categorieVolgorde['TotaalValuta']=500;
    $categorieVolgorde['Totaal']=501;
    $valutaKoers['Totaal']=1;
    $valutaOmschrijvingen['Totaal']='Totaal in EUR';
    $categorieOmschrijvingen['TotaalValuta']='Totaal in Valuta';
    $categorieOmschrijvingen['Totaal']='Totaal in EUR';
		
    $header=array('Valuta','Valutakoers');
    $catWitdth=$this->pdf->w-$this->pdf->marge*2-70;
    $w=$catWitdth/count($categorieVolgorde);
    $headerWidths=array(50,20);
    $headerAligns=array('L','R');
    $underline=array('U','U');
    $topUnderline=array('T','T');
    foreach ($categorieVolgorde as $categorie=>$volgorde)
		{
			$headerWidths[]=$w;
      $headerAligns[]='R';
		  $header[]=$categorieOmschrijvingen[$categorie]."\n%";
      $underline[]='U';
      $topUnderline[]='T';
		}
		
		$this->pdf->setWidths($headerWidths);
    $this->pdf->setAligns($headerAligns);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders=$underline;
    $this->pdf->row($header);
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach($valutaVolgorde as $valuta=>$Vvolgorde)
		{
			$row=array($valutaOmschrijvingen[$valuta],$this->formatGetal($valutaKoers[$valuta],4));
      $rowProcent=array('','');
			foreach($categorieVolgorde as $categorie=>$cVolgorde)
			{
        if($categorie=='TotaalValuta'&&$valuta=='Totaal')
          $row[]='';
				elseif($categorie=='Totaal'||$valuta=='Totaal')
				  $row[]=$this->formatGetal($gegevens[$valuta][$categorie]['totaalEur'],2);
				else
          $row[]=$this->formatGetal($gegevens[$valuta][$categorie]['totaalValuta'],2);
        if($categorie=='TotaalValuta')
          $rowProcent[]='';
        else
          $rowProcent[]=$this->formatGetal($gegevens[$valuta][$categorie]['totaalEur']/ $gegevens['Totaal']['Totaal']['totaalEur']*100,2).'%';
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
    //exit;
  
    //listarray($grafiekdata);
		$this->pdf->setXY(50,108);
		$this->pdf->MultiCell(50,10,'Valutaverdeling',0,"C");
    $this->pdf->setXY(50,115);
    $this->pdf->PieChart(100, 50, $grafiekdata['valuta']['percentage'], '%l (%p)', array_values($grafiekdata['valuta']['kleuren']));
    $this->pdf->setXY(180,108);
    $this->pdf->MultiCell(50,10,'Categorieverdeling',0,"C");
    $this->pdf->setXY(180,115);
		$this->pdf->PieChart(100, 50, $grafiekdata['beleggingscategorie']['percentage'], '%l (%p)', array_values($grafiekdata['beleggingscategorie']['kleuren']));
  }
}
?>
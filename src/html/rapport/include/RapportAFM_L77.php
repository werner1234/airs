<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/08/21 15:32:46 $
File Versie					: $Revision: 1.28 $

$Log: RapportAFM_L77.php,v $

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportPERFG_L77.php");

class RapportAFM_L77
{

	function RapportAFM_L77($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "AFM";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "TOTAAL VERMOGEN";
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
	}



	function writeRapport()
  {
    global $__appvar;
    
    $DB = new DB();
    
    $this->perfg=new RapportPERFG_L77($this->pdf,$this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
  
    $this->perfg->getKleuren();
  
  
    if(isset($this->pdf->portefeuilles) && count($this->pdf->portefeuilles)>1)
    {
      $portefeuilles=$this->pdf->portefeuilles;
    }
    else
    {
      $portefeuilles=array($this->portefeuille);
    }
  
    $DB=new DB();
    $query="SELECT Portefeuilles.Portefeuille, Portefeuilles.depotbank,Depotbanken.Omschrijving FROM Portefeuilles JOIN Depotbanken ON Portefeuilles.Depotbank=Depotbanken.Depotbank WHERE Portefeuilles.portefeuille IN('".implode("','",$portefeuilles)."')";
    $DB->SQL($query);
    $DB->Query();
    $categorieen=array();
    while($data=$DB->nextRecord())
    {
      $categorieen[$data['depotbank']]=$data['depotbank'];
    }
  
  
   // $categorieen=array('totaal','Liquide','Illiquide');
    // $verdeling='Hoofdcategorie';
    // $waardeontwikkeling='depotbank';
    // $kleuren='DEP'

    $verdeling='Bewaarder';
    $waardeontwikkeling='Beleggingscategorie';
    $kleuren='OIB';
    foreach($categorieen as $categorie)
    {
      $hoofdcatData = $this->perfg->getComponentData($verdeling, $categorie, $waardeontwikkeling);

      if(isset($hoofdcatData['categorieOmschrijving'][$categorie]))
        $omschrijving=$hoofdcatData['categorieOmschrijving'][$categorie];
      else
        $omschrijving=$categorie;
      $this->pdf->rapport_titel = "Totaal vermogen $omschrijving";
      $this->pdf->AddPage();
      if (!isset($this->pdf->templateVars[$this->pdf->rapport_type . 'Paginas']))
      {
        $this->pdf->templateVars[$this->pdf->rapport_type.'_'.$categorie. 'Paginas'] = $this->pdf->page;
        $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'_'.$categorie. 'Paginas'] = $this->pdf->rapport_titel;//"Totaal vermogen per beheerder";//
      }
      $this->pdf->SetDrawColor(0, 0, 0);
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'], $this->pdf->rapport_fontcolor['g'], $this->pdf->rapport_fontcolor['b']);
  

      $this->perfg->toonTabel($hoofdcatData['verslagperiodeWaarden'], $hoofdcatData['jaarWaarden']);
      $this->perfg->addGrafiekWaardeontwikkeling($hoofdcatData['grafiekVerdelingOpDatum'], $kleuren,$hoofdcatData['categorieOmschrijving']);
      $this->perfg->addGrafiekRendement($hoofdcatData['huidigeJaarGrafiek']);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    }
  
    /*
     $categorieen=array('totaal','Liquide','Illiquide');
     $verdeling='Hoofdcategorie';
     $waardeontwikkeling='depotbank';
     $kleuren='DEP';
    foreach($categorieen as $categorie)
    {
      $this->pdf->rapport_titel = "TOTAAL VERMOGEN $categorie";
      $this->pdf->AddPage();
      if (!isset($this->pdf->templateVars[$this->pdf->rapport_type . 'Paginas']))
      {
        $this->pdf->templateVars[$this->pdf->rapport_type . 'Paginas'] = $this->pdf->page;
        $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type . 'Paginas'] = $this->pdf->rapport_titel;
      }
      $this->pdf->SetDrawColor(0, 0, 0);
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'], $this->pdf->rapport_fontcolor['g'], $this->pdf->rapport_fontcolor['b']);
    
      $hoofdcatData = $this->perfg->getComponentData($verdeling, $categorie, $waardeontwikkeling);
      $this->perfg->toonTabel($hoofdcatData['verslagperiodeWaarden'], $hoofdcatData['jaarWaarden']);
      $this->perfg->addGrafiekWaardeontwikkeling($hoofdcatData['grafiekVerdelingOpDatum'], $kleuren);
      $this->perfg->addGrafiekRendement($hoofdcatData['huidigeJaarGrafiek']);
    }
  */
  }
}
?>
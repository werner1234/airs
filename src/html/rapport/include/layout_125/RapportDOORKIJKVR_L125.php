<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_125/RapportDOORKIJK_L125.php");

class RapportDOORKIJKVR_L125
{
	function RapportDOORKIJKVR_L125($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->doorkijk=new RapportDOORKIJK_L125($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->pdf->rapport_type = "DOORKIJKVR";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Obligaties uitsplitsing";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->aandeel=1;
	}
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }
  
  function writeRapport()
  {
    global $__appvar;
    
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
      $this->doorkijk->kleuren[$data['doorkijkCategorieSoort']][$data['doorkijkCategorie']]=unserialize($data['grafiekKleur']);
    }
  
    $query="SELECT
doorkijk_categoriePerVermogensbeheerder.Vermogensbeheerder,
doorkijk_categoriePerVermogensbeheerder.doorkijkCategoriesoort,
doorkijk_categoriePerVermogensbeheerder.doorkijkCategorie,
doorkijk_categoriePerVermogensbeheerder.min,
doorkijk_categoriePerVermogensbeheerder.max
FROM
doorkijk_categoriePerVermogensbeheerder
WHERE doorkijk_categoriePerVermogensbeheerder.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY doorkijk_categoriePerVermogensbeheerder.doorkijkCategoriesoort,doorkijk_categoriePerVermogensbeheerder.afdrukVolgorde,doorkijk_categoriePerVermogensbeheerder.min,doorkijk_categoriePerVermogensbeheerder.doorkijkCategorie";
    $db->SQL($query); //echo $query."<br>\n";exit;
    $db->Query();
  
    while($row = $db->nextRecord())
    {
      $this->doorkijk->buckets[$row['doorkijkCategoriesoort']][$row['doorkijkCategorie']]=$row;
    }
  
  
  
  
    $query="SELECT beleggingscategorie
		FROM TijdelijkeRapportage
		WHERE rapportageDatum='".$this->rapportageDatum."' AND portefeuille = '".$this->portefeuille."' AND hoofdcategorie='H-Oblig' ".$__appvar['TijdelijkeRapportageMaakUniek']." GROUP by beleggingscategorie";

		$db->SQL($query);
		$db->Query();
		$beleggingscategorien=array();
		while($data = $db->nextRecord())
    {
      $beleggingscategorien[]=$data['beleggingscategorie'];
    }
    $this->pdf->AddPage();
    subHeader_L125($this->pdf, 28, array(140, 140), array('Looptijd', 'Kredietwaardigheid'));
    $this->pdf->line(148,42,148,180,array('color'=>$this->pdf->textGrijs,'width'=>0.1));
  
    
		$this->pdf->SetTextColor($this->pdf->pdf->rapport_fontcolor['r'],$this->pdf->pdf->rapport_fontcolor['g'],$this->pdf->pdf->rapport_fontcolor['b']);
    $this->doorkijk->vulPagina($beleggingscategorien);


  }
  
  
}


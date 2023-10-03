<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");

class RapportHUIS_L50
{
	function RapportHUIS_L50($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "HUIS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Uitsplitsing van effecten naar categorie van duurzaamheid";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->excelData=array();
    $this->categorieData=array();
    $this->categorieKleuren=array();
		
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


	function getDuurzaam($kleuren)
	{
	  global $__appvar;
    $DB = new DB();
		$query = "SELECT
SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as waardeEur,
TijdelijkeRapportage.duurzaamCategorie,
TijdelijkeRapportage.duurzaamCategorieOmschrijving AS DuurzaamOmschrijving,
if(TijdelijkeRapportage.duurzaamCategorie='',128,TijdelijkeRapportage.duurzaamCategorieVolgorde) as volgorde
FROM
TijdelijkeRapportage
WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."'  AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
GROUP BY TijdelijkeRapportage.duurzaamCategorie
ORDER BY volgorde, duurzaamCategorie"; //AND CategorienPerHoofdcategorie.Hoofdcategorie='WW-RISM'

		$DB->SQL($query);
		$DB->Query();
    $categorieData=array();
    $categorieWaarde=0;
		while($categorie = $DB->NextRecord())
	  {
	    if($categorie['duurzaamCategorie']=='')
      {
        $categorie['duurzaamCategorie'] = 'Geen categorie';
        $categorie['DuurzaamOmschrijving'] = 'Geen categorie';
      }

	    $categorieData[$categorie['duurzaamCategorie']]['waarde'] +=$categorie['waardeEur'];
      $categorieData[$categorie['duurzaamCategorie']]['omschrijving'] =$categorie['DuurzaamOmschrijving'];
	    $categorieWaarde +=$categorie['waardeEur'];
	  }
	  foreach ($categorieData as  $categorie=>$waarden)
	  {
      $categorieData[$categorie]['procent']=$waarden['waarde']/$categorieWaarde;
	    $this->categorieData[$waarden['omschrijving']]=$categorieData[$categorie]['procent']*100;
    
      if(isset($kleuren[$categorie]))
        $this->categorieKleuren[]=$kleuren[$categorie];
      else
        $this->categorieKleuren[]=array(100,100,100);
      
	  }
    //$this->categorieData=$categorieData;

    return $categorieData;
	}


	function writeRapport()
	{
		global $__appvar;
		$this->pdf->AddPage();
		$this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$DB = new DB();
		$q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$kleuren = unserialize($kleuren['grafiek_kleur']);

		foreach ($kleuren['DUU'] as $duu=>$waarde)
		  $duuKleuren[$duu]=array($waarde['R']['value'],$waarde['G']['value'],$waarde['B']['value']);

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->ln(10);

    $tabelData= $this->getDuurzaam($duuKleuren);

    $this->pdf->setY(130);
    $this->pdf->SetWidths(array(20,25,25,25,40,20,20));
		$this->pdf->SetAligns(array('L','L','R','R','R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('','Categorie','Absoluut','in %'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $categorieGrafiekKleuren=array();
    foreach ($tabelData as $categorie=>$data)
    {
      $this->pdf->Row(array('',$data['omschrijving'],$this->formatGetal($data['waarde'],0),$this->formatGetal($data['procent']*100,1)."%"));

    }
//listarray($this->categorieData);
 //   listarray($categorieGrafiekKleuren);


    $this->pdf->setXY(44,42);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Cell(0,5,"Verdeling naar categorie");
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->setXY(20,55);
	  $this->PieChart(50, 50,$this->categorieData, '%l (%p)',$this->categorieKleuren);





	}




  function PieChart($w, $h, $data, $format, $colors=null)
  {

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $hLegend = 2;
      $radius = min($w - $margin * 4 - $hLegend , $h - $margin * 2); //
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
      foreach($data as $val) {
          $angle = floor(($val * 360) / doubleval($this->pdf->sum));
          if ($angle != 0) {
              $angleEnd = $angleStart + $angle;
              $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
              $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
              $angleStart += $angle;
          }
          $i++;
      }
      if ($angleEnd != 360) {
          $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
      }

      //Legends
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

      $x1 = $XPage + $w + $radius ;
      $x2 = $x1 + $hLegend + $margin - 12;
      $y1 = $YDiag -($radius) + $margin;

      for($i=0; $i<$this->pdf->NbVal; $i++)
      {
          $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->pdf->Rect($x1-12, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1);
          $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
          $y1+=$hLegend + $margin;
      }

  }

}
?>
<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/04/20 16:59:35 $
File Versie					: $Revision: 1.4 $

$Log: RapportHUIS_L61.php,v $
Revision 1.4  2019/04/20 16:59:35  rvv
*** empty log message ***

Revision 1.3  2018/11/21 16:48:32  rvv
*** empty log message ***

Revision 1.2  2018/09/30 10:50:50  rvv
*** empty log message ***

Revision 1.1  2018/09/29 16:19:30  rvv
*** empty log message ***



*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/RapportVOLK_L61.php");
include_once("rapport/include/RapportGRAFIEK_L61.php");
//include_once("rapport/include/RapportGRAFIEK_L22.php");

class RapportHUIS_L61
{
	function RapportHUIS_L61($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "HUIS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

	}

	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();

		$portefeuilles=array();
		$query = "SELECT Fondsen.Portefeuille,
              Portefeuilles.Startdatum,
              Portefeuilles.Einddatum,
              Fondsen.Omschrijving,
              TijdelijkeRapportage.actuelePortefeuilleWaardeEuro
              FROM TijdelijkeRapportage 
              JOIN Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
              INNER JOIN Portefeuilles ON Fondsen.Portefeuille = Portefeuilles.Portefeuille
              JOIN FondsenBuitenBeheerfee ON TijdelijkeRapportage.fonds = FondsenBuitenBeheerfee.Fonds AND FondsenBuitenBeheerfee.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND FondsenBuitenBeheerfee.huisfonds=1
              WHERE Fondsen.Huisfonds=1 AND rapportageDatum ='".$this->rapportageDatum."' AND 
              TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND Fondsen.Portefeuille<>''"
						  .$__appvar['TijdelijkeRapportageMaakUniek']." ORDER BY Fondsen.Portefeuille";
		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->NextRecord())
 	  {
		  $portefeuilles[$data['Portefeuille']]=$data;
    }
   // listarray($portefeuilles);
   // $this->pdf->rapport_datumvanaf
   // ; 
    $kopBackup=$this->pdf->rapport_koptext;
    foreach($portefeuilles as $portefeuille=>$pdata)
    {
      $rapportageDatum['a'] = date("Y-m-d",$this->pdf->rapport_datumvanaf); 
      $rapportageDatum['b'] = date("Y-m-d",$this->pdf->rapport_datum);

	    if($this->pdf->rapport_datumvanaf < db2jul($pdata['Startdatum']))
	      $rapportageDatum['a'] = $pdata['Startdatum'];
	  
  	  if($this->pdf->rapport_datum > db2jul($pdata['Einddatum']))
  	  {
	    	echo "<b>Fout: Portefeille $portefeuille heeft een einddatum  (".date("d-m-Y",db2jul($pdata['Einddatum'])).")</b>";
	  	  exit;
	    }
    	if(db2jul($rapportageDatum[a]) > db2jul($rapportageDatum[b]))
	    {
	    	echo "<b>Fout: $portefeuille Van datum kan niet groter zijn dan  T/m datum! </b>";
		    exit;
	    }

      if(substr($rapportageDatum['a'],5,2)==01 && substr($rapportageDatum['a'],8,2)==01)
        $startjaar=true;
      else
        $startjaar=false;

     	$fondswaarden['a'] =  berekenPortefeuilleWaarde($portefeuille, $rapportageDatum['a'],$startjaar,$pdata['RapportageValuta'],$rapportageDatum['a']);
	    $fondswaarden['b'] =  berekenPortefeuilleWaarde($portefeuille, $rapportageDatum['b'],0,$pdata['RapportageValuta'],$rapportageDatum['a']);
     	vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,$rapportageDatum['a']);
	    vulTijdelijkeTabel($fondswaarden['b'] ,$portefeuille,$rapportageDatum['b']);
      $portefeuilleWaarde=0;
      foreach($fondswaarden['b'] as $fonds)
        $portefeuilleWaarde+=$fonds['actuelePortefeuilleWaardeEuro'];

			$aandeelVanPortefeuille=$pdata['actuelePortefeuilleWaardeEuro']/$portefeuilleWaarde;
			if($aandeelVanPortefeuille <>0)
			{
				$rapport = new RapportVOLK_L61($this->pdf, $portefeuille, $rapportageDatum['a'], $rapportageDatum['b']);
				$this->pdf->rapport_titel = 'Portefeuille-overzicht';
				$this->pdf->rapport_koptext = "\n \n" . $pdata['Omschrijving'] . "\n \n";
				$rapport->aandeel = $aandeelVanPortefeuille;
				$rapport->writeRapport();
			}
      $rapport=new RapportGRAFIEK_L61($this->pdf, $portefeuille, $rapportageDatum['a'], $rapportageDatum['b']);
      $this->pdf->rapport_titel='Portefeuille-overzicht';
      $this->pdf->rapport_koptext="\n \n".$pdata['Omschrijving']."\n \n";
      $rapport->writeRapport();
    }
    $this->pdf->rapport_koptext=$kopBackup;
    
	}

}
?>

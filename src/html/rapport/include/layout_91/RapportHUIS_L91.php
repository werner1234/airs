<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/04/10 15:50:36 $
File Versie					: $Revision: 1.8 $

$Log: RapportHUIS_L22.php,v $
Revision 1.8  2019/04/10 15:50:36  rvv
*** empty log message ***

Revision 1.7  2019/04/06 17:12:28  rvv
*** empty log message ***

Revision 1.6  2018/12/22 16:15:52  rvv
*** empty log message ***

Revision 1.5  2018/09/05 15:53:27  rvv
*** empty log message ***

Revision 1.4  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.3  2013/03/09 16:22:24  rvv
*** empty log message ***

Revision 1.2  2012/12/15 14:52:51  rvv
*** empty log message ***

Revision 1.1  2012/12/12 16:54:24  rvv
*** empty log message ***

Revision 1.6  2012/06/20 18:11:09  rvv
*** empty log message ***

Revision 1.5  2011/06/29 10:33:16  rvv
*** empty log message ***

Revision 1.4  2010/10/11 08:46:37  cvs
*** empty log message ***

Revision 1.3  2010/06/30 16:11:12  rvv
*** empty log message ***

Revision 1.2  2010/06/16 19:15:05  rvv
*** empty log message ***

Revision 1.1  2009/09/27 12:54:02  rvv
*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/layout_91/RapportVOLK_L91.php");
//include_once("rapport/include/RapportVOLK_L22.php");
//include_once("rapport/include/RapportGRAFIEK_L22.php");

class RapportHUIS_L91
{
	function RapportHUIS_L91($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "HUIS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->pdf->rapport_rendementText="Rendement over verslagperiode";
    $this->layoutNr=0;
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
              INNER JOIN FondsenBuitenBeheerfee ON TijdelijkeRapportage.fonds = FondsenBuitenBeheerfee.Fonds AND FondsenBuitenBeheerfee.Vermogensbeheerder='".$this->portefeuilledata['Vermogensbeheerder']."'
              WHERE Fondsen.Huisfonds=1 AND rapportageDatum ='".$this->rapportageDatum."' AND FondsenBuitenBeheerfee.LayoutNr=".$this->layoutNr." AND
              TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' "
						  .$__appvar['TijdelijkeRapportageMaakUniek']." ORDER BY TijdelijkeRapportage.BeleggingscategorieVolgorde, Fondsen.Portefeuille";
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
    	if(db2jul($rapportageDatum['a']) > db2jul($rapportageDatum['b']))
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
				$rapport = new RapportVOLK_L91($this->pdf, $portefeuille, $rapportageDatum['a'], $rapportageDatum['b']);
				$this->pdf->rapport_titel = 'Portefeuille-overzicht '."\n".$pdata['Omschrijving'];
				$this->pdf->rapport_koptext = "\n \n" . $pdata['Omschrijving'] . "\n \n";
				$rapport->aandeel = $aandeelVanPortefeuille;
        $rapport->grafieken = false;
				$rapport->writeRapport();
			}
      //$rapport=new RapportGRAFIEK_L22($this->pdf, $portefeuille, $rapportageDatum['a'], $rapportageDatum['b']);
      //$this->pdf->rapport_titel='Portefeuille-overzicht';
      //$this->pdf->rapport_koptext="\n \n".$pdata['Omschrijving']."\n \n";
      //$rapport->writeRapport();
    }
    $this->pdf->rapport_koptext=$kopBackup;
    
	}

}
?>

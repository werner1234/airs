<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/12/19 08:51:26 $
 		File Versie					: $Revision: 1.3 $

 		$Log: RapportTRANSFEE.php,v $
 		Revision 1.3  2015/12/19 08:51:26  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2015/12/19 08:43:20  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/01/11 15:50:39  rvv
 		*** empty log message ***
 		

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//ini_set('max_execution_time',60);
class RapportTRANSFEE
{
	function RapportTRANSFEE($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FRONT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
//		$this->pdf->rapport_titel = "Transactiekosten";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
	  if($waarde <> 0)
		  return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		global $__appvar;
    $this->pdf->AddPage('P');
 	$this->pdf->frontPage = true;
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

  
        
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, 
    Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, 
    Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client 
    FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
    
    
    		$portefeuilledata['Naam']=$this->pdf->portefeuilledata['Naam'];
		$portefeuilledata['Naam1']=$this->pdf->portefeuilledata['Naam1'];
		$portefeuilledata['Adres']=$this->pdf->portefeuilledata['Adres'];
		$portefeuilledata['pc']=$this->pdf->portefeuilledata['pc'];
		$portefeuilledata['Woonplaats']=$this->pdf->portefeuilledata['Woonplaats'];

		$this->pdf->SetFont($this->pdf->rapport_font,'',10);
		$this->pdf->SetY(35);
		$this->pdf->SetWidths(array(30,120));
		$this->pdf->row(array('',$portefeuilledata['Naam']));
	  if ($portefeuilledata['Naam1'] != '')
    {
      $this->pdf->row(array('',$portefeuilledata['Naam1']));
    }
    $this->pdf->row(array('',$portefeuilledata['Adres']));
  
    $plaats='';
		if($portefeuilledata['pc'] != '')
		  $plaats .= $portefeuilledata['pc']." ";
		$plaats .= $portefeuilledata['Woonplaats'];
		$this->pdf->row(array('',$plaats));


		$this->pdf->SetY(85);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->widthA = array(65,30,30,30);
		$this->pdf->alignA = array('L','R','R','R');



		// print categorie headers
	  $this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

  
  	// loopje over Grootboekrekeningen Opbrengsten = 1
		$query = "SELECT Fondsen.Omschrijving, ".
		"Fondsen.Fondseenheid, ".
		"Rekeningmutaties.Boekdatum, ".
		"Rekeningmutaties.Transactietype,
		Rekeningmutaties.Valuta,
		 Rekeningmutaties.Afschriftnummer,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, 
     Rekeningmutaties.Fonds,  ".
		"Rekeningmutaties.Fondskoers, ".
		"Rekeningmutaties.Debet as Debet, ".
		"Rekeningmutaties.Credit as Credit,
     Rekeningmutaties.Bedrag as Bedrag,  ".
		"Rekeningmutaties.Valutakoers,
		 1 $koersQuery as Rapportagekoers ".
		"FROM Rekeningmutaties, Fondsen, Rekeningen, Portefeuilles, Grootboekrekeningen ".
		"WHERE ".
		"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		"Rekeningmutaties.Fonds = Fondsen.Fonds AND ".
		"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		"Rekeningmutaties.Verwerkt = '1' AND ".
		"Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND ".
		"Rekeningmutaties.Transactietype <> 'B' AND ".
		"Grootboekrekeningen.FondsAanVerkoop = '1' AND ".
		"Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
		"Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
		"ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		// haal koersresultaat op om % te berekenen

		$transactietypen = array();
		$buffer = array();
		$sortBuffer = array();
    $kostenTotaal=0;
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('Instrument','Aantal','Mutatiebedrag','Transactiekosten '));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		while($mutaties = $DB->nextRecord())
		{
		  $buffer[]=$mutaties;
    }
    
    foreach($buffer as $mutatie)
    {
      /*
      if(strpos($mutatie['Fonds'],'%'))
        $query="UPDATE Fondsen SET fondssoort='OBL' WHERE Fonds='".$mutatie['Fonds']."'";
      else
        $query="UPDATE Fondsen SET fondssoort='AAND' WHERE Fonds='".$mutatie['Fonds']."'";
      $DB->SQL($query);
      $DB->Query();
      */
		  $query="SELECT fondssoort,Beurzen.beursregio FROM Fondsen LEFT JOIN Beurzen ON Fondsen.beurs=Beurzen.beurs WHERE Fonds='".$mutatie['Fonds']."'";
      $DB->SQL($query);
      $fondsData=$DB->lookupRecord();
		
      $query="SELECT
orderkosten.kostenpercentage,
orderkosten.kostenminimumbedrag,
orderkosten.brokerkostenpercentage,
orderkosten.brokerkostenminimumbedrag,
orderkosten.prijsPerStuk
FROM
orderkosten
WHERE 
(
 (orderkosten.vermogensbeheerder='".$portefeuilledata['Vermogensbeheerder']."' AND orderkosten.Portefeuille='' AND orderkosten.beursregio='".$fondsData['beursregio']."') OR 
 (orderkosten.vermogensbeheerder='".$portefeuilledata['Vermogensbeheerder']."' AND orderkosten.Portefeuille='' AND orderkosten.beursregio='') OR 
  orderkosten.Portefeuille='".$this->portefeuille."' 
)
AND orderkosten.fondssoort='".$fondsData['fondssoort']."'
ORDER BY orderkosten.Portefeuille desc LIMIT 1";
       $DB->SQL($query);
	     $kostenData=$DB->lookupRecord();
       
      //listarray($fondsData);
      //listarray($kostenData);
      if($kostenData['prijsPerStuk'] <> 0)
        $kosten=$mutatie['Aantal']*($kostenData['prijsPerStuk']);
      else
        $kosten=$mutatie['Bedrag']*($kostenData['kostenpercentage']/100);
      if($kosten < $kostenData['kostenminimumbedrag'])
        $kosten=$kostenData['kostenminimumbedrag'];
    

      
       $this->pdf->Row(array($mutatie["Omschrijving"],
          $this->formatGetal($mutatie["Aantal"]),
          $this->formatGetal($mutatie['Bedrag']),
          $this->formatGetal($kosten,2)));
          
      $kostenTotaal+=round($kosten,2);    

		}
    $this->pdf->Row(array('Totaal','','',$this->formatGetal($kostenTotaal,2)));
  
   
  
  
  
  
  }
}
?>
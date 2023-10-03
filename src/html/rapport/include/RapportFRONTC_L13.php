<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/11/15 17:03:35 $
File Versie					: $Revision: 1.7 $

$Log: RapportFRONTC_L13.php,v $
Revision 1.7  2017/11/15 17:03:35  rvv
*** empty log message ***

Revision 1.6  2017/10/11 14:57:30  rvv
*** empty log message ***

Revision 1.5  2012/05/17 06:48:05  rvv
*** empty log message ***

Revision 1.4  2012/05/16 13:27:15  rvv
*** empty log message ***

Revision 1.3  2012/05/09 18:47:45  rvv
*** empty log message ***

Revision 1.2  2012/03/28 15:55:19  rvv
*** empty log message ***

Revision 1.1  2012/03/25 13:27:46  rvv
*** empty log message ***

Revision 1.2  2011/12/04 12:56:56  rvv
*** empty log message ***

Revision 1.1  2011/07/20 13:36:42  rvv
*** empty log message ***

Revision 1.6  2011/07/03 06:42:47  rvv
*** empty log message ***

Revision 1.5  2011/04/09 14:35:27  rvv
*** empty log message ***

Revision 1.4  2011/04/03 08:35:46  rvv
*** empty log message ***

Revision 1.3  2011/03/23 17:01:48  rvv
*** empty log message ***

Revision 1.2  2011/03/18 15:02:38  rvv
*** empty log message ***

Revision 1.1  2011/03/17 05:01:11  rvv
*** empty log message ***

Revision 1.9  2011/01/15 12:11:41  rvv
*** empty log message ***

*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFRONTC_L13
{
	function RapportFRONTC_L13($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FRONT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_FRONT_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_FRONT_titel;

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatumVanafJul=db2jul($this->rapportageDatumVanaf);
		$this->rapportageDatum = $rapportageDatum;
		$this->rapportageDatumJul=db2jul($this->rapportageDatum);
		$this->pdf->extraPage =0;
		$this->DB = new DB();

		$this->rapportMaand 	= date("n",$this->rapportageDatumJul);
		$this->rapportDag 		= date("d",$this->rapportageDatumJul);
		$this->rapportJaar 		= date("Y",$this->rapportageDatumJul);

		$this->pdf->brief_font = $this->pdf->rapport_font;

	}



	function writeRapport()
	{
	  global $__appvar;

	  //$this->pdf->frontPage=true;
   // $this->pdf->last_rapport_type="FRONT";
$this->pdf->consolidatie=true;

    $portefeuilledata=array();
    foreach ($this->pdf->portefeuilles as $id=>$portefeuille)
		{
		$query = "SELECT
	            	Clienten.Naam as Naam,
                Clienten.Naam1 as Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Portefeuilles.Depotbank,
                Portefeuilles.PortefeuilleVoorzet,
                Accountmanagers.Naam as accountManager,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email,
                Depotbanken.Omschrijving as depotbankOmschrijving
		          FROM
		            Portefeuilles
		            LEFT JOIN Clienten ON Portefeuilles.Client = Clienten.Client
		            LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
		            LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
		            LEFT Join CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille
		            Join Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
		          WHERE
		            Portefeuilles.Portefeuille = '".$portefeuille."'";

		$this->DB->SQL($query);
		$this->DB->Query();
	  $this->pdf->consolidatieData[$id] = $this->DB->nextRecord();
		}

$this->pdf->portefeuilledata['Naam']=$this->pdf->consolidatieData[0]['Naam'];
$this->pdf->portefeuilledata['Naam1']=$this->pdf->consolidatieData[0]['Naam1'];
$this->pdf->portefeuilledata['Adres']=$this->pdf->consolidatieData[0]['Adres'];
$this->pdf->portefeuilledata['Woonplaats']=$this->pdf->consolidatieData[0]['Woonplaats'];
$this->pdf->portefeuilledata['Land']=$this->pdf->consolidatieData[0]['Land'];

	    	  $this->pdf->addPage('P');
	      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


		$this->pdf->SetWidths(array(25,140));
	  $this->pdf->SetAligns(array('R','L'));

$this->pdf->SetY(120);
	  $this->pdf->SetWidths(array(20,10,50,150));
	  $n=1;
	  $this->pdf->SetAligns(array('L','L','L','L'));
	  foreach ($this->pdf->consolidatieData as $id=>$pdata)
	  {
	  	$this->pdf->row(array('',$n,$pdata['Portefeuille'],$pdata['Naam']));
	  	$this->pdf->row(array('','',$pdata['depotbankOmschrijving'],$pdata['Naam1']));
	  	$n++;
	  }


    $this->pdf->SetWidths(array(20,30,100));
    $this->pdf->SetY(135+18);




	}
}
?>
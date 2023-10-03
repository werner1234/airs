<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2010/07/25 14:36:01 $
File Versie					: $Revision: 1.4 $

$Log: RapportFRONT_L26.php,v $
Revision 1.4  2010/07/25 14:36:01  rvv
*** empty log message ***

Revision 1.3  2010/07/11 16:00:05  rvv
*** empty log message ***

Revision 1.2  2010/06/09 16:40:14  rvv
*** empty log message ***

Revision 1.1  2010/05/30 12:46:25  rvv
*** empty log message ***

Revision 1.2  2010/05/19 16:24:10  rvv
*** empty log message ***

Revision 1.1  2010/05/05 18:37:43  rvv
*** empty log message ***

Revision 1.1  2010/03/31 17:26:12  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L26
{
	function RapportFront_L26($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FRONT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_FRONT_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_FRONT_titel;
		else
			$this->pdf->rapport_titel = "Titel pagina";

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


	function kopEnVoet()
	{
	  if(is_file($this->pdf->rapport_factuurHeader))
		{
			$this->pdf->Image($this->pdf->rapport_factuurHeader, 0, 10, 210, 34);
		}
		if(is_file($this->pdf->rapport_factuurFooter))
		{
			$this->pdf->Image($this->pdf->rapport_factuurFooter, 5, 255, 200, 37);
		}
	}


	function writeRapport()
	{
	  global $__appvar;
	  $this->pdf->addPage('P');
	  $this->pdf->frontPage = true;

    if(is_file($this->pdf->rapport_logo))
		{
			$this->pdf->Image($this->pdf->rapport_logo, 0, 10, 108, 15);
		}

	  $this->pdf->SetWidths(array(140,50));
	  $this->pdf->SetAligns(array('R','L'));
	  $this->pdf->SetFont($this->pdf->brief_font,'',11);
	  $this->pdf->SetY(20);
	  $this->pdf->row(array('','Breda, '.date("d-m-Y")));
	  $this->pdf->ln(10);
	  $this->pdf->row(array('',$this->pdf->portefeuilledata['AccountmanagerNaam']));
	  $this->pdf->ln(10);
	  $this->pdf->row(array('',$this->pdf->portefeuilledata['VermogensbeheerderTelefoon']));
	  $this->pdf->ln(10);
	  $this->pdf->row(array('','Portefeuille overzicht'));
	 // listarray($this->pdf->portefeuilledata);


	  $this->pdf->SetWidths(array(20,155));
	  $this->pdf->SetAligns(array('R','L'));
	  $curHeight=$this->pdf->rowHeight;
    $this->pdf->rowHeight = 5;


		$portefeuilledata['Naam']=$this->pdf->portefeuilledata['Naam'];
		$portefeuilledata['Naam1']=$this->pdf->portefeuilledata['Naam1'];
		$portefeuilledata['Adres']=$this->pdf->portefeuilledata['Adres'];
		$portefeuilledata['Woonplaats']=$this->pdf->portefeuilledata['Woonplaats'];
		$portefeuilledata['Land']=$this->pdf->portefeuilledata['Land'];

	  $extraDagen = 0; //2
	  $this->pdf->SetY(50);
		$this->pdf->row(array('',$portefeuilledata['Naam']));
    if ($portefeuilledata['Naam1'] != '')
      $this->pdf->row(array('',$portefeuilledata['Naam1']));
    $this->pdf->row(array('',$portefeuilledata['Adres']));
    $this->pdf->row(array('',$portefeuilledata['Woonplaats']));
    $this->pdf->row(array('',$portefeuilledata['Land']));

    $this->pdf->SetY(100);
    $this->pdf->row(array('','Uw portefeuilleoverzicht per '. date("d-m-Y",$this->rapportageDatumJul)));
    $this->pdf->SetFont($this->pdf->brief_font,'',11);
    $this->pdf->SetY(125);
    $this->pdf->row(array('','Geachte '.$this->pdf->portefeuilledata['verzendAanhef'].",\n\n"));

    	if($this->pdf->portefeuilledata['txtKoppeling'] !='')
		  {
		    $koppeling = stripslashes($this->pdf->portefeuilledata[$this->pdf->portefeuilledata['txtKoppeling']]);
		    $koppeling = stripslashes($koppeling);
		    $query = "SELECT * FROM custom_txt WHERE
		    type = '".$this->pdf->portefeuilledata['txtKoppeling']."' AND
		    field = '".$this->pdf->rapport_type."_".$koppeling."' AND
		    Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'  ";
		    $this->DB->SQL($query);
        $txtData = $this->DB->lookupRecord();
		    $titel = $txtData['title'];
		    $briefData = html_entity_decode(strip_tags($txtData['txt']));
		  }

   $this->pdf->row(array('',$briefData));
   $this->pdf->rowHeight = $curHeight;
   $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}
}
?>
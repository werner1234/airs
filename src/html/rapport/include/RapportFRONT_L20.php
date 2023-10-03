<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2012/12/22 15:34:10 $
File Versie					: $Revision: 1.4 $

$Log: RapportFRONT_L20.php,v $
Revision 1.4  2012/12/22 15:34:10  rvv
*** empty log message ***

Revision 1.3  2012/12/19 17:01:17  rvv
*** empty log message ***

Revision 1.2  2011/06/02 15:05:05  rvv
*** empty log message ***

Revision 1.1  2011/05/04 16:31:23  rvv
*** empty log message ***

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

class RapportFront_L20
{
	function RapportFront_L20($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
	  $this->pdf->addPage('L');
	  $this->pdf->frontPage = true;

    if(is_file($this->pdf->rapport_logo))
		{
		
		      $factor=0.06;
		  $xSize=1200*$factor;
		  $ySize=224*$factor;
		  //echo "$xSize $ySize <br>\n";exit;
	    $this->pdf->Image($this->pdf->rapport_logo, 0, 0, $xSize, $ySize);
		
		//	$this->pdf->Image($this->pdf->rapport_logo, 0, 10, 108, 15);
		}


		$query = "SELECT CRM_naw.naam,CRM_naw.naam1 FROM CRM_naw WHERE Portefeuille = '".$this->portefeuille."'  ";
	  $this->DB->SQL($query);
	  $portefeuilledata = $this->DB->lookupRecord();

	  $query = "SELECT
		            Clienten.Naam,
                Clienten.Naam1,
                Clienten.Client
		          FROM
		            Portefeuilles, Clienten , Accountmanagers, Vermogensbeheerders
		          WHERE
		            Portefeuille = '".$this->portefeuille."' AND
		            Portefeuilles.Client = Clienten.Client AND
                Accountmanagers.Accountmanager = Portefeuilles.Accountmanager AND
                Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder";
		$this->DB->SQL($query);
		$this->DB->Query();
		$portefeuilledata2 = $this->DB->nextRecord();

	  if($portefeuilledata['naam']=='')
		  $portefeuilledata['naam']=$portefeuilledata2['Naam'];
		if($portefeuilledata['naam1']=='')
		  $portefeuilledata['naam1']=$portefeuilledata2['Naam1'];
	  if($portefeuilledata['naam']=='')
		  $portefeuilledata['naam']=$portefeuilledata2['Client'];

	  $this->pdf->SetFont($this->pdf->rapport_font,'B',16);
    $this->pdf->SetY(80);
 	  $this->pdf->SetWidths(array(270));
	  $this->pdf->SetAligns(array('C'));
    $this->pdf->row(array(strtoupper('Vermogensopstelling per '.date("d",$this->rapportageDatumJul)." ".
		                                                 vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$pdf->rapport_taal)." ".
		                                                 date("Y",$this->rapportageDatumJul))));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+4);
    $this->pdf->SetY(100);

    if($portefeuilledata['naam']=='')
		  $portefeuilledata['naam']=$this->pdf->portefeuilledata['Naam'];
		if($portefeuilledata['naam1']=='')
		  $portefeuilledata['naam1']=$this->pdf->portefeuilledata['Naam1'];

		$this->pdf->row(array($portefeuilledata['naam']));
    //if ($portefeuilledata['naam1'] != '')
    //  $this->pdf->row(array($portefeuilledata['naam1']));



   $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}
}
?>
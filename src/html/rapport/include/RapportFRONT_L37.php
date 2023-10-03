<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2012/05/30 16:02:38 $
File Versie					: $Revision: 1.1 $

$Log: RapportFRONT_L37.php,v $
Revision 1.1  2012/05/30 16:02:38  rvv
*** empty log message ***

Revision 1.1  2012/05/27 08:33:11  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L37
{
	function RapportFront_L37($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FRONT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_OIS_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_FRONT_titel;
		else
			$this->pdf->rapport_titel = "Titel pagina";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatumVanafJul=db2jul($this->rapportageDatumVanaf);
		$this->rapportageDatum = $rapportageDatum;
		$this->rapportageDatumJul=db2jul($this->rapportageDatum);
		$this->DB = new DB();

	}


	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT
		            Clienten.Naam,
                Clienten.Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Accountmanagers.Naam as accountManager,
                 Vermogensbeheerders.Naam as vermogensbeheerderNaam,
                 Vermogensbeheerders.Adres as vermogensbeheerderAdres,
                 Vermogensbeheerders.Woonplaats as vermogensbeheerderWoonplaats,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email
		          FROM
		            Portefeuilles, Clienten , Accountmanagers, Vermogensbeheerders
		          WHERE
		            Portefeuille = '".$this->portefeuille."' AND
		            Portefeuilles.Client = Clienten.Client AND
                Accountmanagers.Accountmanager = Portefeuilles.Accountmanager AND
                Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder";
		$this->DB->SQL($query);
		$this->DB->Query();
		$portefeuilledata = $this->DB->nextRecord();

   //background
    $this->pdf->AddPage();

    $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;



    $this->pdf->SetY(50);
   	$this->pdf->widthA = array(10,40,10,150);
		$this->pdf->alignA = array('L','L','L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+8);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetWidths(array(10,150));
    $this->pdf->row(array('',vertaalTekst('Vermogensrapportage',$this->pdf->rapport_taal)));
    $this->pdf->SetWidths($this->pdf->widthA);
	  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->SetY(80);

		$this->pdf->row(array('',vertaalTekst('Client',$this->pdf->rapport_taal),':',$portefeuilledata['Portefeuille']));
    $this->pdf->ln(6);
		$rapportagePeriode = date("d",$this->rapportageDatumVanafJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumVanafJul).
		                                          ' '.vertaalTekst('t/m',$this->pdf->rapport_taal).' '.
		                                          date("d",$this->rapportageDatumJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumJul);
		$this->pdf->row(array('',vertaalTekst('Verslagperiode',$this->pdf->rapport_taal),':',$rapportagePeriode));
		$this->pdf->row(array('',vertaalTekst('Datum rapport',$this->pdf->rapport_taal),':',  $rapDatum=date("j")." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln(6);
	  $this->pdf->frontPage = true;


	}
}
?>
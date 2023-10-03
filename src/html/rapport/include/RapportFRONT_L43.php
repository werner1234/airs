<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2013/02/03 09:04:21 $
File Versie					: $Revision: 1.1 $

$Log: RapportFRONT_L43.php,v $
Revision 1.1  2013/02/03 09:04:21  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L43
{
	function RapportFront_L43($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

    $this->pdf->last_rapport_type="FRONT";

 //background
   $this->pdf->SetAutoPageBreak(false);
   $this->pdf->AddPage('L');

   $this->pdf->widthA = array(20,150);
	 $this->pdf->alignA = array('L','L');

	 $this->pdf->SetAligns($this->pdf->alignA);

	 //$this->pdf->Rotate(-90,148.5,148.5);

		if(is_file($this->pdf->rapport_logo))
		{
      //$factor=0.09;
		//  $xSize=492*$factor;
		//  $ySize=211*$factor;
	  $factor=0.15;
		  $xSize=417*$factor;
		  $ySize=100*$factor;
		  $this->pdf->Image($this->pdf->rapport_logo, 225, 10, $xSize, $ySize);
		}

		$portefeuilledata['Naam']=$this->pdf->portefeuilledata['Naam'];
		$portefeuilledata['Naam1']=$this->pdf->portefeuilledata['Naam1'];
		$portefeuilledata['Adres']=$this->pdf->portefeuilledata['Adres'];
		$portefeuilledata['pc']=$this->pdf->portefeuilledata['pc'];
		$portefeuilledata['Woonplaats']=$this->pdf->portefeuilledata['Woonplaats'];

		$this->pdf->SetFont($this->pdf->rapport_font,'',12);
		$this->pdf->SetY(35);
		$this->pdf->SetWidths(array(30,120));
		$this->pdf->row(array('',$portefeuilledata['Naam']));
		$this->pdf->ln(2);
    if ($portefeuilledata['Naam1'] != '')
    {
      $this->pdf->row(array('',$portefeuilledata['Naam1']));
      $this->pdf->ln(2);
    }
    $this->pdf->row(array('',$portefeuilledata['Adres']));
    $this->pdf->ln(2);

    $plaats='';
		if($portefeuilledata['pc'] != '')
		  $plaats .= $portefeuilledata['pc']." ";
		$plaats .= $portefeuilledata['Woonplaats'];
		$this->pdf->row(array('',$plaats));


    $this->pdf->SetWidths($this->pdf->widthA);

		$this->pdf->SetY(85);
		$this->pdf->SetFont($this->pdf->rapport_font,'B',12);

		$rapportagePeriode = 'Rapportageperiode '.date("d",$this->rapportageDatumVanafJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumVanafJul).
		                                          ' t/m '.
		                                          date("d",$this->rapportageDatumJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumJul);
		$this->pdf->row(array('',$rapportagePeriode));
		$this->pdf->ln(4);

		$oldPortefeuilleString = $portefeuilledata['Portefeuille'];
	  $i=1;
		for($j=0;$j<strlen($oldPortefeuilleString);$j++)
		{
		 if($i>3)
		 {
		  $portefeuilleString.='.';
		  $i=1;
		 }
		 $portefeuilleString.= $oldPortefeuilleString[$j];
		 $i++;
		}

		$rapportageRekening = 'Rapportage rekening nr.: '.$portefeuilleString;
		$this->pdf->row(array(' ',$rapportageRekening));

		$this->pdf->SetFont($this->pdf->rapport_font,'',12);

		$this->pdf->SetY(170);
		$this->pdf->SetFont($this->pdf->rapport_font,'',12);

		$this->pdf->row(array('','Datum: '.date("d")." ".vertaalTekst($__appvar["Maanden"][date("n")],$pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln(2);
		$this->pdf->row(array('','Telefoon: '.$portefeuilledata['Telefoon']));
	  $this->pdf->ln(2);
	  $this->pdf->row(array('','E-mail: '.$portefeuilledata['Email']));
	  $this->pdf->ln(2);
		$this->pdf->frontPage = true;
		$this->pdf->Rotate(0);
		$this->pdf->SetAutoPageBreak(true, 15);
//	  $this->pdf->rapport_type = "OIB";
	  $this->pdf->rapport_titel = "";//Inhoudsopgave
	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;

	//	listarray($vermogensbeheerder);
  //  listarray($this->pdf->portefeuilledata);
	}
}
?>
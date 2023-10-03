<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2013/06/15 15:55:18 $
File Versie					: $Revision: 1.2 $

$Log: RapportFRONT_L48.php,v $
Revision 1.2  2013/06/15 15:55:18  rvv
*** empty log message ***

Revision 1.1  2013/05/26 13:54:49  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L48
{
	function RapportFront_L48($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

	  $this->pdf->frontPage=true;
    $this->pdf->last_rapport_type="FRONT";
	  $this->pdf->addPage('L');

		if(is_file($this->pdf->rapport_logo))
		{
		  $factor=0.08;
		  $xSize=1000*$factor;
		  $ySize=620*$factor;
      $xStart=(297)/2-($xSize/2);
	    $this->pdf->Image($this->pdf->rapport_logo, $xStart, 20, $xSize, $ySize);
		}
    
    $this->pdf->SetWidths(array(290-$this->pdf->marge*2));
    $this->pdf->SetAligns(array('C'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',20);
    $this->pdf->SetY(90);
    $this->pdf->row(array(vertaalTekst("VERMOGENSRAPPORTAGE",$this->pdf->rapport_taal)));
    

    
    $rapportagePeriode = date("d",$this->rapportageDatumVanafJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumVanafJul).
		                                          ' '.vertaalTekst('t/m',$this->pdf->rapport_taal).' '.
		                                          date("d",$this->rapportageDatumJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumJul);
    $this->pdf->SetFont($this->pdf->rapport_font,'',16);
    $this->pdf->SetY(140);                                          
    $this->pdf->row(array('Cliënt: '.$this->pdf->portefeuilledata['Naam']));
    $this->pdf->Ln(10);
	  $this->pdf->row(array('Periode: '.$rapportagePeriode));
    
    $this->pdf->frontPage=true;
	  $this->pdf->rapport_type = "FRONT";
	  $this->pdf->rapport_titel = "";//Inhoudsopgave
	  $this->pdf->addPage('L');
	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;


/*
		$this->pdf->SetWidths(array(25,140));
	  $this->pdf->SetAligns(array('R','L'));
    $this->pdf->rowHeight = 5;

    $portefeuilledata=array();
		$portefeuilledata['Naam']=$this->pdf->portefeuilledata['Naam'];
		$portefeuilledata['Naam1']=$this->pdf->portefeuilledata['Naam1'];
		$portefeuilledata['Adres']=$this->pdf->portefeuilledata['Adres'];
		$portefeuilledata['Woonplaats']=$this->pdf->portefeuilledata['Woonplaats'];
		$portefeuilledata['Land']=$this->pdf->portefeuilledata['Land'];


  	$this->pdf->SetWidths(array(20,120));
  	$this->pdf->SetAligns(array('R','C','L'));
  	$this->pdf->SetY(60);
  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+8);
	  $this->pdf->row(array('',vertaalTekst("Vermogensrapportage",$this->pdf->rapport_taal)));
	  $this->pdf->SetY(85);
	  $this->pdf->SetAligns(array('R','L','L'));
	  $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
	  $this->pdf->row(array('',vertaalTekst("Persoonlijk en vertrouwelijk",$this->pdf->rapport_taal)));
	  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('',$portefeuilledata['Naam']));
    if($portefeuilledata['Naam1'])
	  	$this->pdf->row(array('',$portefeuilledata['Naam1']));
	  $this->pdf->row(array('',$portefeuilledata['Adres']));
    $this->pdf->row(array('',$portefeuilledata['Woonplaats']));
    $this->pdf->row(array('',$portefeuilledata['Land']));

    $this->pdf->SetWidths(array(20,30,100));
    $this->pdf->SetY(135);
    $this->pdf->row(array('',vertaalTekst("Depotbank",$this->pdf->rapport_taal),$this->pdf->portefeuilledata['DepotbankOmschrijving']));
    $this->pdf->ln(4);
    $this->pdf->row(array('',vertaalTekst('Rekeningnummer',$this->pdf->rapport_taal),$this->portefeuille));
    $this->pdf->ln(4);
    $rapportagePeriode = date("d",$this->rapportageDatumVanafJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumVanafJul).
		                                          ' '.vertaalTekst('t/m',$this->pdf->rapport_taal).' '.
		                                          date("d",$this->rapportageDatumJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumJul);

    $this->pdf->row(array('',vertaalTekst('Rapportageperiode',$this->pdf->rapport_taal),$rapportagePeriode));
    $this->pdf->ln(4);
    $this->pdf->row(array('',vertaalTekst('Datum',$this->pdf->rapport_taal),(date("d"))." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));


    $this->pdf->Line(10,180,285,180);
    $this->pdf->rowHeight = 4;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->DB=new DB();
    $query="SELECT Telefoon,Fax,Email,Naam,Adres,Woonplaats,website FROM Vermogensbeheerders WHERE Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $this->DB->SQL($query);
		$this->DB->Query();
		$vermogensbeheerder = $this->DB->nextRecord();

		$this->pdf->SetY(185);
		$this->pdf->SetWidths(array(175-$this->pdf->marge,$xSize));
		$this->pdf->SetAligns(array('R','C'));
		$this->pdf->row(array('',$vermogensbeheerder['Adres'].", ".$vermogensbeheerder['Woonplaats']));
		$this->pdf->row(array('',vertaalTekst('Telefoon',$this->pdf->rapport_taal).': '.$vermogensbeheerder['Telefoon']));
		$this->pdf->row(array('',$vermogensbeheerder['website']));
	  $this->pdf->frontPage=true;
	  $this->pdf->rapport_type = "OIB";
	  $this->pdf->rapport_titel = "";//Inhoudsopgave
	  $this->pdf->addPage('L');
	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;
*/
	//	listarray($vermogensbeheerder);
  //  listarray($this->pdf->portefeuilledata);
	}
}
?>
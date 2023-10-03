<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/09 04:31:53 $
File Versie					: $Revision: 1.4 $

$Log: RapportFRONT_L90.php,v $
Revision 1.4  2020/07/09 04:31:53  rvv
*** empty log message ***

Revision 1.3  2020/07/08 15:37:08  rvv
*** empty log message ***

Revision 1.2  2020/06/27 16:24:16  rvv
*** empty log message ***

Revision 1.1  2020/06/13 15:13:06  rvv
*** empty log message ***



*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L90
{
	function RapportFront_L90($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
	  $image=base64_decode('iVBORw0KGgoAAAANSUhEUgAAB3wAAALCCAMAAAACpFIjAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA/BpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIgeG1wTU06T3JpZ2luYWxEb2N1bWVudElEPSJ1dWlkOjY1RTYzOTA2ODZDRjExREJBNkUyRDg4N0NFQUNCNDA3IiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkYyREJDMkZGQzBERjExRUFBMjNCOEJDODFCMDhEQjREIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkYyREJDMkZFQzBERjExRUFBMjNCOEJDODFCMDhEQjREIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIElsbHVzdHJhdG9yIENDIDIzLjAgKFdpbmRvd3MpIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InV1aWQ6M2U1OTM0NTAtYmU0Yi00MWYzLWExYWMtZjBjNjBmNmNkOWY3IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOmU0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');

		if(is_file($this->pdf->rapport_logo))
		{
		  $factor=0.05;
		  $xSize=1916*$factor;
		  //$ySize=706*$factor;
      $xStart=(297)/2-($xSize/2);
      
	    //$this->pdf->Image($this->pdf->rapport_logo, $xStart, 20, $xSize, $ySize);
	    $this->pdf->MemImage($image,$xStart, 20, $xSize);
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
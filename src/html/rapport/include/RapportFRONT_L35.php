<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/02/17 19:18:57 $
File Versie					: $Revision: 1.7 $

$Log: RapportFRONT_L35.php,v $
Revision 1.7  2018/02/17 19:18:57  rvv
*** empty log message ***

Revision 1.6  2018/01/28 09:22:18  rvv
*** empty log message ***

Revision 1.5  2018/01/13 19:10:29  rvv
*** empty log message ***

Revision 1.4  2017/11/18 18:58:17  rvv
*** empty log message ***

Revision 1.3  2012/04/25 15:20:45  rvv
*** empty log message ***

Revision 1.2  2012/03/28 15:55:19  rvv
*** empty log message ***

Revision 1.1  2012/03/25 13:27:46  rvv
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

class RapportFront_L35
{
	function RapportFront_L35($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

	   $this->pdf->addPage('L');
	   $this->pdf->frontPage=true;

		$this->pdf->SetWidths(array(25,140));
	  $this->pdf->SetAligns(array('R','L'));
		$rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight = 5;

 //$this->pdf->rapport_logo='/develop/php/robert/AIRS/html/rapport/logo/logo_ave.png';

    if(is_file($this->pdf->rapport_logo))
		{
			if(substr($this->pdf->rapport_logo,-4)=='.png')
			{
				$factor = 0.025;
				$xSize = 1425 * $factor;
				$ySize = 699 * $factor;
			}
			else
			{
				$factor=0.12;
				$xSize=420*$factor;
				$ySize=168*$factor;
			}
	    $this->pdf->Image($this->pdf->rapport_logo,10,5, $xSize, $ySize);
		}

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

    if($this->pdf->lastPOST['anoniem'])
	    $this->pdf->portefeuilledata['DepotbankOmschrijving']='';

    $this->pdf->SetWidths(array(20,30,100));
    $this->pdf->SetY(135);
    $this->pdf->row(array('',vertaalTekst('Depotbank',$this->pdf->rapport_taal),$this->pdf->portefeuilledata['DepotbankOmschrijving']));
    $this->pdf->ln(4);
    $this->pdf->row(array('',vertaalTekst('Portefeuille',$this->pdf->rapport_taal),$this->pdf->portefeuilledata['Portefeuille']));
    $this->pdf->ln(4);
    $rapportagePeriode = date("d",$this->rapportageDatumVanafJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumVanafJul).
		                                          ' t/m '.
		                                          date("d",$this->rapportageDatumJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumJul);

    $this->pdf->row(array('',vertaalTekst('Rapportageperiode',$this->pdf->rapport_taal),$rapportagePeriode));
    $this->pdf->ln(4);
    $this->pdf->row(array('',vertaalTekst('Datum',$this->pdf->rapport_taal),(date("d"))." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln(4);
		$this->pdf->row(array('',vertaalTekst('Rapportage valuta',$this->pdf->rapport_taal),$this->pdf->portefeuilledata['RapportageValuta']));



		$this->pdf->Line(10,180,285,180);
		/*
    $this->pdf->rowHeight = 4;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->DB=new DB();
    $query="SELECT Telefoon,Fax,Email,Naam,Adres,Woonplaats,website FROM Vermogensbeheerders WHERE Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $this->DB->SQL($query);
		$this->DB->Query();
		$vermogensbeheerder = $this->DB->nextRecord();
*/
		//$this->pdf->SetY(185);
		//$this->pdf->SetWidths(array(175-$this->pdf->marge,$xSize));
		//$this->pdf->SetAligns(array('R','C'));
		//$this->pdf->row(array('',$vermogensbeheerder['Adres'].", ".$vermogensbeheerder['Woonplaats']));
		//$this->pdf->row(array('','Telefoon: '.$vermogensbeheerder['Telefoon']));
		//$this->pdf->row(array('',$vermogensbeheerder['website']));

		$this->pdf->rowHeight=$rowHeightBackup;
	  $this->pdf->frontPage=true;
    $this->pdf->last_rapport_type="FRONT";
	  $this->pdf->addPage('L');


	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;

	//	listarray($vermogensbeheerder);
  //  listarray($this->pdf->portefeuilledata);
	}
}
?>
<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2016/09/11 08:30:02 $
File Versie					: $Revision: 1.1 $

$Log: RapportFRONT_L69.php,v $
Revision 1.1  2016/09/11 08:30:02  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L69
{
	function RapportFront_L69($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
		$this->pdf->rapportCounter = count($this->pdf->page);

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

		//if($this->pdf->selectData['type'] != 'eMail')
		//  $this->voorBrief();
   //background

		///if ((count($this->pdf->pages) % 2))
		//{
		//  $this->pdf->frontPage=true;
  	//	$this->pdf->AddPage($this->pdf->CurOrientation);
		//}
		$this->pdf->frontPage = true;
    $this->pdf->AddPage('L');


		if(is_file($this->pdf->rapport_logo))
		{
	    $factor=0.031;
		  $xSize=1417*$factor;
		  $ySize=591*$factor;
			$this->pdf->Image($this->pdf->rapport_logo, 230, 180, $xSize, $ySize);
		}
    

		$fontsize = 16; //$this->pdf->rapport_fontsize
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
    
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->SetAligns(array('C'));
    $this->pdf->SetWidths(array(297-2*$this->pdf->marge));
		$this->pdf->SetY(45);

		$rapportagePeriode = date("d",$this->rapportageDatumVanafJul)." ".
		                     vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                     date("Y",$this->rapportageDatumVanafJul).
		                     ' - '.
		                     date("d",$this->rapportageDatumJul)." ".
		                     vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                     date("Y",$this->rapportageDatumJul);
    //$this->pdf->row(array('Capital Support'));
    $this->pdf->row(array($portefeuilledata['Naam']));
    $this->pdf->ln(5);
    //$this->pdf->row(array('Vermogensregierapportage'));
    $this->pdf->row(array($portefeuilledata['Naam1']));
    $this->pdf->ln(5);
		$this->pdf->row(array($rapportagePeriode));
		$this->pdf->ln(6);

    $this->pdf->SetWidths(array(15,50,150));
    $this->pdf->SetAligns(array('L','L','L'));
     
    $this->pdf->SetY(150);
    $this->pdf->SetFont($this->pdf->rapport_font,'',11);
    $this->pdf->underline=true;
		$this->pdf->row(array('','Samenstelling portefeuille'));
    $this->pdf->underline=false;
    $this->pdf->ln(1);
    $this->pdf->row(array('',$this->portefeuille,$this->pdf->portefeuilledata['Depotbank']));
    $this->pdf->ln();
    
    

	$this->pdf->SetY(170);
    $this->pdf->SetWidths(array(223,50));	
    $this->pdf->row(array('',''));//Den Haag
    $this->pdf->ln(1);
		$this->pdf->row(array('',date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));

  
	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $this->pdf->frontPage = true;

/*    
    $this->pdf->AutoPageBreak=false;
    $this->pdf->SetY(-10);
    $this->pdf->MultiCell(290,4,"Via onze website kunt u dagelijks uw portefeuille inzien.",0,'C');
    $this->pdf->AutoPageBreak=true;

   	$this->pdf->rapport_type = "FRONT";
	  $this->pdf->rapport_titel = "";//Inhoudsopgave
	  $this->pdf->addPage('L');
	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;
*/
	}
}
?>

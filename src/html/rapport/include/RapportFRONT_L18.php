<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2011/06/29 16:52:23 $
File Versie					: $Revision: 1.6 $

$Log: RapportFRONT_L18.php,v $
Revision 1.6  2011/06/29 16:52:23  rvv
*** empty log message ***

Revision 1.5  2009/02/21 10:36:02  rvv
*** empty log message ***

Revision 1.4  2008/07/01 07:12:34  rvv
*** empty log message ***

Revision 1.3  2008/05/16 08:13:26  rvv
*** empty log message ***

Revision 1.2  2008/03/18 12:39:08  rvv
*** empty log message ***

Revision 1.1  2008/03/18 09:56:48  rvv
*** empty log message ***

Revision 1.6  2008/01/23 07:37:03  rvv
*** empty log message ***

Revision 1.5  2007/11/16 11:22:27  rvv
*** empty log message ***

Revision 1.4  2007/10/04 11:57:04  rvv
*** empty log message ***

Revision 1.3  2007/09/26 15:30:33  rvv
*** empty log message ***

Revision 1.2  2007/07/05 12:28:39  rvv
*** empty log message ***

Revision 1.1  2007/06/29 11:38:56  rvv
L14 aanpassingen




*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L18
{
	function RapportFront_L18($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
		$this->pdf->extraPage =0;
		$this->DB = new DB();

	}

	function RowCell($data)
	{
	    //Calculate the height of the row
	    $nb=0;
	//    for($i=0;$i<count($data);$i++)
	 //       $nb=max($nb,$this->pdf->NbLines($this->pdf->widths[$i],$data[$i]));
	    $h=$this->pdf->rowHeight;//*$nb;
	    //Issue a page break first if needed

	    $this->pdf->CheckPageBreak($h);
	    //Draw the cells of the row
	    for($i=0;$i<count($data);$i++)
	    {
	        $w=$this->pdf->widths[$i];
	        $a=isset($this->pdf->aligns[$i]) ? $this->pdf->aligns[$i] : 'L';
	        //Save the current position
	        $x=$this->pdf->GetX();
	        $y=$this->pdf->GetY();
	        //Draw the border
	        //$this->Rect($x,$y,$w,$h);
	        //Print the text
	        $lines = $this->pdf->NbLines($this->pdf->widths[$i],$data[$i]);
	        // fill lines
	//function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='',$currentx=0)
	        $this->pdf->Cell($w,$this->pdf->rowHeight,$data[$i],0,$line,$a,$this->pdf->fillCell[$i]);

	        if($this->pdf->CellBorders[$i])
	        {
	          if($this->pdf->CellBorders[$i] == 'U')
	            $this->pdf->Line($x,$y+$h,$x+$w,$y+$h);
	        }
	        //Put the position to the right of the cell
	        $this->pdf->SetXY($x+$w,$y);
	    }
	    //Go to the next line
	    $this->pdf->Ln($h);
	}


	function writeRapport()
	{
		global $__appvar;
		$this->pdf->AddPage();
		$query = "SELECT
		            Clienten.Naam,
                Clienten.Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Portefeuilles.Risicoklasse,
                Accountmanagers.Naam as accountManager,
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

		$this->pdf->SetFont($this->pdf->rapport_font,'B',16);
		$this->pdf->switchFont('fonds');


   	$this->pdf->widthA = array(15,125);
		$this->pdf->alignA = array('L','L');

		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		$this->pdf->SetY(80);
		$this->pdf->row(array('',"Vermogensrapportage"));
		$rapportagePeriode = 'Rapportage per '.   date("d",$this->rapportageDatumJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumJul);
		$this->pdf->row(array('',$rapportagePeriode));
		$this->pdf->ln(20);

		$this->pdf->SetFont($this->pdf->rapport_font,'B',12);
		$this->pdf->row(array('',$portefeuilledata['Naam']));

    if ($portefeuilledata['Naam1'] != '')
      $this->pdf->row(array('',$portefeuilledata['Naam1']));
    $this->pdf->SetFont($this->pdf->rapport_font,'',12);
		$rapportageRekening = 'depotnummer '.$portefeuilledata['Portefeuille'];
		$this->pdf->row(array(' ',$rapportageRekening));

		$this->pdf->SetY(150);
		$this->pdf->SetFont($this->pdf->rapport_font,'',12);
		$this->pdf->row(array('','Uw beleggingsprofiel:'));
	  $this->pdf->row(array('','Model '.$portefeuilledata['Risicoklasse']));

		$this->pdf->geenBasisFooter = true;
		$this->pdf->AddPage();
		$this->pdf->IndexPage = $this->pdf->page;
		$this->pdf->templateVars = array();
    $this->pdf->last_rapport_type = $this->pdf->rapport_type;
    $this->pdf->last_rapport_titel = $this->pdf->rapport_titel;
	}
}
?>
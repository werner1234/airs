<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2016/07/20 16:12:53 $
File Versie					: $Revision: 1.4 $

$Log: RapportFRONT_L66.php,v $
Revision 1.4  2016/07/20 16:12:53  rvv
*** empty log message ***

Revision 1.3  2016/04/10 15:48:34  rvv
*** empty log message ***

Revision 1.2  2016/04/03 10:58:02  rvv
*** empty log message ***

Revision 1.1  2016/03/27 17:32:03  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L66
{
	function RapportFront_L66($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
	     $factor=0.03;
		   $xSize=1500*$factor;
		   $ySize=665*$factor;
       $logopos=(297/2)-($xSize/2);
	     $this->pdf->Image($this->pdf->rapport_logo, $logopos, 3, $xSize, $ySize);
		}

   	$this->pdf->widthA = array(99.5-$this->pdf->marge,180);
		$this->pdf->alignA = array('L','L','L');

		$fontsize = 10; //$this->pdf->rapport_fontsize

    


    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);

    $this->pdf->SetY(88);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize);
    
    
    $this->pdf->row(array(' ',vertaalTekst('PERSOONLIJK EN VERTROUWELIJK',$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam']));
    if($this->pdf->portefeuilledata['Naam1'] <> '')
    {
      $this->pdf->ln(1);
      $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam1']));
    }
    if($this->pdf->portefeuilledata['Adres']<>'')
    {
      $this->pdf->ln(1);
      $this->pdf->row(array('',$this->pdf->portefeuilledata['Adres']));
    }
    if($this->pdf->portefeuilledata['Adres']<>'')
    {
      $this->pdf->ln(1);
      $this->pdf->row(array('',$this->pdf->portefeuilledata['Woonplaats']));
    }
		if($this->pdf->portefeuilledata['Land']<>'')
		{
			$this->pdf->ln(1);
			$this->pdf->row(array('',$this->pdf->portefeuilledata['Land']));
		}
		
    $this->pdf->SetWidths(array(99.5-$this->pdf->marge,26,5,120));
		$rapportagePeriode = date("d",$this->rapportageDatumVanafJul)." ".
		                     vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                     date("Y",$this->rapportageDatumVanafJul).
		                     ' '.vertaalTekst('t/m',$this->pdf->rapport_taal).' '.
		                     date("d",$this->rapportageDatumJul)." ".
		                     vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                     date("Y",$this->rapportageDatumJul);
    $this->pdf->ln(5);
    $this->pdf->row(array('',vertaalTekst('Verslagperiode',$this->pdf->rapport_taal),":",$rapportagePeriode));
    $this->pdf->SetWidths(array(30,40,5,120));

		//$this->pdf->ln(40);
    $this->pdf->SetY(150);
		$this->pdf->row(array(' ',vertaalTekst('Vermogensrapportage',$this->pdf->rapport_taal),':',$portefeuilledata['Portefeuille']));
    $this->pdf->ln();
    $this->pdf->row(array(' ',vertaalTekst('Mandaat',$this->pdf->rapport_taal),':',vertaalTekst($this->pdf->portefeuilledata['Risicoklasse'],$this->pdf->rapport_taal)));
    $this->pdf->ln();
    
    $this->pdf->SetWidths(array(30,120));

    $this->pdf->ln(8); 


		$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
		$this->pdf->row(array('',vertaalTekst('Datum',$this->pdf->rapport_taal).': '.date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln(2);
		$this->pdf->row(array('',''));

/*
$this->pdf->AutoPageBreak=false;
$this->pdf->SetY(195);
$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize-2);
$this->pdf->Cell(297,5,'Eemnesserweg 11-3, 1251 NA Laren NH - www.ambassadorinvestments.nl - info@ambassadorinvestments.nl - 035-2031035',0,1,'C');
$this->pdf->Cell(297,5,'IBAN: NL59 ABNA 0516 0106 89 - KvK: 28087987 - BTW: NL8092.88.722 B01',0,1,'C');
$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
$this->pdf->AutoPageBreak=true;
*/

	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $this->pdf->frontPage = true;

  
	}
}
?>

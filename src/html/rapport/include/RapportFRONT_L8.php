<?php
/*
Author  						: $Author: cvs $
Laatste aanpassing	: $Date: 2012/03/29 10:54:16 $
File Versie					: $Revision: 1.5 $

$Log: RapportFRONT_L8.php,v $
Revision 1.5  2012/03/29 10:54:16  cvs
*** empty log message ***

Revision 1.4  2012/02/14 14:12:22  cvs
regel 131 acc man verwijderd

Revision 1.3  2011/02/24 17:46:56  rvv
*** empty log message ***

Revision 1.2  2009/11/20 09:38:15  rvv
*** empty log message ***

Revision 1.1  2009/01/20 17:45:20  rvv
*** empty log message ***

Revision 1.8  2008/05/16 08:12:57  rvv
*** empty log message ***

Revision 1.7  2008/03/18 09:30:24  rvv
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

class RapportFront_L8
{
	function RapportFront_L8($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

   //background
$this->pdf->AddPage();

   	$this->pdf->widthA = array(30,180);
		$this->pdf->alignA = array('L','L','L');

		$fontsize = 10; //$this->pdf->rapport_fontsize
		$this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		$this->pdf->SetY(48);
		$this->pdf->SetWidths(array(30,120));
		$this->pdf->row(array('',$portefeuilledata['Naam']));
		$this->pdf->ln(2);
    if ($portefeuilledata['Naam1'] != '')
    {
      $this->pdf->row(array('',$portefeuilledata['Naam1']));
      $this->pdf->ln(2);
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
    $this->pdf->row(array('',$portefeuilledata['Adres']));
    $this->pdf->ln(2);
    $this->pdf->row(array('',$portefeuilledata['Woonplaats']));

    $this->pdf->SetWidths($this->pdf->widthA);

		$this->pdf->SetY(80);

		$rapportagePeriode = vertaalTekst('Verslagperiode',$this->pdf->rapport_taal).' '.date("d",$this->rapportageDatumVanafJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumVanafJul).
		                                          ' '.vertaalTekst('t/m',$this->pdf->rapport_taal).' '.
		                                          date("d",$this->rapportageDatumJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumJul);
		$this->pdf->row(array('',$rapportagePeriode));
		$this->pdf->ln(6);

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

		$rapportageRekening = vertaalTekst('Rapportage rekening',$this->pdf->rapport_taal).' : '.$portefeuilleString;
		$this->pdf->row(array(' ',$rapportageRekening));


		$this->pdf->SetY(113);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
		$this->pdf->row(array('',vertaalTekst('Datum',$this->pdf->rapport_taal).': '.date("d")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln(2);
		$this->pdf->row(array('',''));

		$this->pdf->SetY(140);

    $explodedName=explode(" ",$portefeuilledata['vermogensbeheerderNaam']);
    foreach ($explodedName as $key=>$word)
      $explodedName[$key]=vertaalTekst($word,$this->pdf->rapport_taal);
		$portefeuilledata['vermogensbeheerderNaam']=implode(" ",$explodedName);


		$this->pdf->row(array());
	  $this->pdf->ln(1);
	  $this->pdf->row(array());
		$this->pdf->ln(1);
	  $this->pdf->row(array());
	  $this->pdf->ln(1);
	  $this->pdf->row(array());
		$this->pdf->ln(1);
		$this->pdf->SetWidths(array(30,10,50));
		$this->pdf->row(array());
    $this->pdf->ln(1);
	  $this->pdf->row(array());
	  $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->ln(1);
    $this->pdf->SetTextColor(0,0,255);
	  $this->pdf->row(array());
	  $this->pdf->ln(1);
    
	  $this->pdf->row(array('',$portefeuilledata['Email']));
	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $this->pdf->frontPage = true;





	}
}
?>
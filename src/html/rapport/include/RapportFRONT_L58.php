<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2015/04/24 13:13:11 $
File Versie					: $Revision: 1.3 $

$Log: RapportFRONT_L58.php,v $
Revision 1.3  2015/04/24 13:13:11  rvv
*** empty log message ***

Revision 1.2  2014/12/20 16:32:36  rvv
*** empty log message ***

Revision 1.1  2014/10/04 15:23:36  rvv
*** empty log message ***

Revision 1.7  2014/09/10 15:54:54  rvv
*** empty log message ***

Revision 1.6  2014/08/06 15:41:01  rvv
*** empty log message ***

Revision 1.5  2014/06/14 16:40:37  rvv
*** empty log message ***

Revision 1.4  2014/06/08 15:27:58  rvv
*** empty log message ***

Revision 1.3  2014/05/17 16:35:44  rvv
*** empty log message ***

Revision 1.2  2014/04/30 16:03:17  rvv
*** empty log message ***

Revision 1.1  2014/04/19 16:16:18  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L58
{
	function RapportFront_L58($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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


		$this->pdf->frontPage = true;
    $this->pdf->AddPage('L');


	
  	if(is_file($this->pdf->rapport_logo))
		{
      $logoWidth=17;
		  $logopos = 297/2-$logoWidth/2;
      $this->pdf->Image($this->pdf->rapport_logo, $logopos , 5, $logoWidth);	
      $this->pdf->Line($this->pdf->marge,27,297-$this->pdf->marge,27);
		}

   	$this->pdf->widthA = array(30,180);
		$this->pdf->alignA = array('L','L','L');

		$fontsize = $this->pdf->rapport_fontsize;

    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);


    $this->pdf->SetWidths($this->pdf->widthA);

		$this->pdf->SetY(58);

		$rapportagePeriode = vertaalTekst('Verslagperiode',$this->pdf->rapport_taal).' '.date("d",$this->rapportageDatumVanafJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumVanafJul).
		                                          ' '.vertaalTekst('t/m',$this->pdf->rapport_taal).' '.
		                                          date("d",$this->rapportageDatumJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumJul);
		$this->pdf->row(array('',$rapportagePeriode));
		$this->pdf->ln(6);

    $this->pdf->SetWidths(array(30,40,5,50));
		$this->pdf->row(array(' ',vertaalTekst('Vermogensrapportage',$this->pdf->rapport_taal),':',$portefeuilledata['Portefeuille']));//formatPortefeuille
    $this->pdf->ln();
    $this->pdf->row(array(' ',vertaalTekst('Portefeuilleprofiel',$this->pdf->rapport_taal),':',vertaalTekst($this->pdf->portefeuilledata['Risicoklasse'],$this->pdf->rapport_taal)));
    $this->pdf->ln();
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->ln(8); 
    $this->pdf->row(array(' ',vertaalTekst('PERSOONLIJK EN VERTROUWELIJK',$this->pdf->rapport_taal)));
    $this->pdf->ln(1);
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam']));
    if($this->pdf->portefeuilledata['Naam1'] <> '')
    {
      $this->pdf->ln(1);
      $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam1']));
    }
    $this->pdf->ln(1);
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Adres']));
    $this->pdf->ln(1);
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Woonplaats']));


		$this->pdf->SetY(133);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
		$this->pdf->row(array('',vertaalTekst('Datum',$this->pdf->rapport_taal).': '.date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln(2);
		$this->pdf->row(array('',''));


		$this->pdf->SetY(160);

    $explodedName=explode(" ",$portefeuilledata['vermogensbeheerderNaam']);
    foreach ($explodedName as $key=>$word)
      $explodedName[$key]=vertaalTekst($word,$this->pdf->rapport_taal);
		$portefeuilledata['vermogensbeheerderNaam']=implode(" ",$explodedName);

		$this->pdf->row(array('',$portefeuilledata['vermogensbeheerderNaam']));
	  $this->pdf->ln(1);
	  $this->pdf->row(array('',$portefeuilledata['vermogensbeheerderAdres']));
		$this->pdf->ln(1);
	  $this->pdf->row(array('',$portefeuilledata['vermogensbeheerderWoonplaats']));
	  $this->pdf->ln(1);
	  $this->pdf->row(array('',$portefeuilledata['Email']));
    $this->pdf->ln(1);
	  $this->pdf->row(array('',$portefeuilledata['Telefoon']));
	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $this->pdf->frontPage = true;
   
   
    $this->pdf->rapport_type = "FRONT";
	  $this->pdf->rapport_titel = "Inhoudsopgave";//Inhoudsopgave
	  $this->pdf->addPage('L');
      	if(is_file($this->pdf->rapport_logo))
		{
      $logoWidth=17;
		  $logopos = 297/2-$logoWidth/2;
      $this->pdf->Image($this->pdf->rapport_logo, $logopos , 5, $logoWidth);	
      $this->pdf->Line($this->pdf->marge,27,297-$this->pdf->marge,27);
		}

	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;

	}
}
?>

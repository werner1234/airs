<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/08 15:25:54 $
File Versie					: $Revision: 1.10 $

$Log: RapportFRONT_L54.php,v $
Revision 1.10  2020/07/08 15:25:54  rvv
*** empty log message ***

Revision 1.9  2019/11/16 17:12:28  rvv
*** empty log message ***

Revision 1.8  2019/10/19 16:45:25  rvv
*** empty log message ***

Revision 1.7  2019/10/11 17:40:07  rvv
*** empty log message ***

Revision 1.6  2017/09/09 18:01:12  rvv
*** empty log message ***

Revision 1.5  2017/04/22 16:44:09  rvv
*** empty log message ***

Revision 1.4  2016/01/17 18:10:27  rvv
*** empty log message ***

Revision 1.3  2015/10/14 16:12:05  rvv
*** empty log message ***

Revision 1.2  2014/04/12 16:28:12  rvv
*** empty log message ***

Revision 1.1  2014/03/02 10:26:23  rvv
*** empty log message ***

Revision 1.3  2013/11/23 17:23:24  rvv
*** empty log message ***

Revision 1.2  2013/03/17 10:58:29  rvv
*** empty log message ***

Revision 1.1  2013/03/13 17:01:08  rvv
*** empty log message ***

Revision 1.5  2012/10/24 15:45:39  rvv
*** empty log message ***

Revision 1.4  2012/10/17 09:16:53  rvv
*** empty log message ***

Revision 1.3  2012/10/07 14:57:17  rvv
*** empty log message ***

Revision 1.2  2012/09/23 08:51:44  rvv
*** empty log message ***

Revision 1.1  2012/06/17 13:04:11  rvv
*** empty log message ***

Revision 1.2  2012/06/09 13:43:40  rvv
*** empty log message ***

Revision 1.1  2012/05/27 08:33:11  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L54
{
	function RapportFront_L54($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

/*
			  $query = "SELECT
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.verzendAdres,
CRM_naw.verzendPc,
CRM_naw.verzendPlaats,
CRM_naw.verzendLand,
CRM_naw.verzendAanhef,
CRM_naw.ondernemingsvorm,
CRM_naw.titel,
CRM_naw.voorletters,
CRM_naw.tussenvoegsel,
CRM_naw.achternaam,
CRM_naw.achtervoegsel,
CRM_naw.part_naam,
CRM_naw.part_voorvoegsel,
CRM_naw.part_titel,
CRM_naw.part_voorletters,
CRM_naw.part_tussenvoegsel,
CRM_naw.part_achternaam,
CRM_naw.part_achtervoegsel,
CRM_naw.enOfRekening,
Portefeuilles.BetalingsinfoMee
FROM Portefeuilles  
LEFT JOIN  CRM_naw on Portefeuilles.portefeuille=CRM_naw.Portefeuille WHERE CRM_naw.Portefeuille = '".$this->portefeuille."'  ";

	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();
*/    
		$query = "SELECT
    CRM_naw.Adres,
CRM_naw.Pc,
CRM_naw.Plaats,
CRM_naw.Land,
                Portefeuilles.Portefeuille,
                Accountmanagers.Naam as accountManager,
                Vermogensbeheerders.Vermogensbeheerder,
                 Vermogensbeheerders.Naam as vermogensbeheerderNaam,
                 Vermogensbeheerders.Adres as vermogensbeheerderAdres,
                 Vermogensbeheerders.Woonplaats as vermogensbeheerderWoonplaats,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email
		          FROM
		            Portefeuilles
                JOIN Accountmanagers ON Accountmanagers.Accountmanager = Portefeuilles.Accountmanager 
                JOIN Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
                LEFT JOIN  CRM_naw on Portefeuilles.portefeuille=CRM_naw.Portefeuille 
		          WHERE
		            Portefeuilles.Portefeuille = '".$this->portefeuille."' ";
		$this->DB->SQL($query);
		$this->DB->Query();
		$portefeuilledata = $this->DB->nextRecord();
    
    if($_POST['anoniem']==1)
    {
      $portefeuilledata['Portefeuille']='000.000';
      $portefeuilledata['Adres']='';
      $portefeuilledata['Pc']='';
      $portefeuilledata['Plaats']='';
      $portefeuilledata['Land']='';
    }
    if($portefeuilledata['Pc']<>'')
      $portefeuilledata['Plaats']=$portefeuilledata['Pc'].'  '.$portefeuilledata['Plaats'];

	
    $this->pdf->AddPage('L');
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

		if(is_file($this->pdf->rapport_logo))
		{
			 // $pdfObject->Image($pdfObject->rapport_logo, 18, 3.5, 52, 20.6);
			$toekomstLogo=$__appvar["basedir"]."/html/rapport/logo/toekomstbeleggen.png";
			if($this->pdf->portefeuilledata['SoortOvereenkomst']=='Toekomstbeleggen' && file_exists($toekomstLogo))
			{
				$factor=0.04;
				$xSize=1605*$factor;//$x=885*$factor;
				$ySize=203*$factor;//$y=849*$factor;
				$logo = $toekomstLogo;
			}
			else
			{
				$factor=0.042;
				$xSize=1612*$factor;//$x=885*$factor;
				$ySize=666*$factor;//$y=849*$factor;
				//if ($this->pdf->portefeuilledata['Vermogensbeheerder'] == 'HAV')
				//{
				//	$xSize = 2509 * $factor;
			  //}
				$logo=$this->pdf->rapport_logo;
			}
		 $this->pdf->Image($logo, 297/2-$xSize/2, 13, $xSize, $ySize);
		}


   	$this->pdf->widthA = array(30,180);
		$this->pdf->alignA = array('L','L','L');

		$fontsize = 10; //$this->pdf->rapport_fontsize

    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);


    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);

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
		$this->pdf->row(array(' ',vertaalTekst('Vermogensrapportage',$this->pdf->rapport_taal),':',$portefeuilledata['Portefeuille']));
    $this->pdf->ln();
    $this->pdf->row(array(' ','Portefeuilleprofiel',':',$this->pdf->portefeuilledata['Risicoklasse']));
    $this->pdf->ln();
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->ln(8); 
    $this->pdf->row(array(' ','PERSOONLIJK EN VERTROUWELIJK'));
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam']));
    if($this->pdf->portefeuilledata['Naam1'] <> '')
    {
      $this->pdf->ln(1);
      $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam1']));
    }
    $this->pdf->ln(1);

    $this->pdf->row(array('',$portefeuilledata['Adres']));
    $this->pdf->ln(1);
    $this->pdf->row(array('',$portefeuilledata['Plaats']));


		$this->pdf->SetY(133);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
		$this->pdf->row(array('',vertaalTekst('Datum',$this->pdf->rapport_taal).': '.date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln(2);
		$this->pdf->row(array('',''));


		$this->pdf->SetY(160);

		//if($this->pdf->portefeuilledata['Vermogensbeheerder']=='HAV')
		if($this->pdf->portefeuilledata['SoortOvereenkomst']=='Toekomstbeleggen')
			$this->pdf->row(array('','Toekomstbeleggen.nl'));
		else
		  $this->pdf->row(array('',$portefeuilledata['vermogensbeheerderNaam']));
		//else
	  //	$this->pdf->row(array('','Comfort Vermogensbeheer'));
	  $this->pdf->ln(1);
	  $this->pdf->row(array('',$portefeuilledata['vermogensbeheerderAdres']));
		$this->pdf->ln(1);
	  $this->pdf->row(array('',$portefeuilledata['vermogensbeheerderWoonplaats']));
	  $this->pdf->ln(1);
		if($this->pdf->portefeuilledata['SoortOvereenkomst']=='Toekomstbeleggen')
			$this->pdf->row(array('','info@toekomstbeleggen.nl'));
		else
	    $this->pdf->row(array('',$portefeuilledata['Email']));
    $this->pdf->ln(1);
	  $this->pdf->row(array('',$portefeuilledata['Telefoon']));
	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $this->pdf->frontPage = true;
    
//    $this->pdf->AutoPageBreak=false;
//    $this->pdf->SetY(-10);
//    $this->pdf->MultiCell(290,4,"Via onze website kunt u dagelijks uw portefeuille inzien.",0,'C');
//    $this->pdf->AutoPageBreak=true;


	}
}
?>

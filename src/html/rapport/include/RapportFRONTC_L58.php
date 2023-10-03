<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/03/02 07:20:09 $
File Versie					: $Revision: 1.2 $

$Log: RapportFRONTC_L58.php,v $
Revision 1.2  2017/03/02 07:20:09  rvv
*** empty log message ***

Revision 1.1  2017/03/01 17:17:08  rvv
*** empty log message ***

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

class RapportFRONTC_L58
{
	function RapportFRONTC_L58($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

		$portefeuilledata=array();
		foreach ($this->pdf->portefeuilles as $portefeuille)
		{
			$query = "SELECT
	            	if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam,Clienten.Naam) as Naam,
                if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam1,Clienten.Naam1) as Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Portefeuilles.Depotbank,
                Portefeuilles.PortefeuilleVoorzet,
                Accountmanagers.Naam as accountManager,
                 Vermogensbeheerders.Naam as vermogensbeheerderNaam,
                 Vermogensbeheerders.Adres as vermogensbeheerderAdres,
                 Vermogensbeheerders.Woonplaats as vermogensbeheerderWoonplaats,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email,
                Depotbanken.Omschrijving as DepotbankOmschrijving
		          FROM
		            Portefeuilles
		            LEFT JOIN Clienten ON Portefeuilles.Client = Clienten.Client
		            LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
		            LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
		            LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille
		            LEFT JOIN Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
		          WHERE
		            Portefeuilles.Portefeuille = '".$portefeuille."'";

			$this->DB->SQL($query);
			$this->DB->Query();
			$portefeuilledata[] = $this->DB->nextRecord();

		}


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

		$this->pdf->SetWidths(array(30,50,75+75));
		$this->pdf->SetAligns(array('L','L','L','L'));
		$this->pdf->setY(80);
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('','Portefeuille','Depotbank',''));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		foreach ($portefeuilledata as $port)
		{
			$this->pdf->row(array('',$port['PortefeuilleVoorzet'].$port['Portefeuille'],$port['DepotbankOmschrijving']));
		}

		$this->pdf->SetY(133);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
		$this->pdf->row(array('',vertaalTekst('Datum',$this->pdf->rapport_taal).': '.date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln(2);
		$this->pdf->row(array('',''));


		$this->pdf->SetY(160);

		$portefeuilledata=$portefeuilledata[0];
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


		$this->pdf->SetY(160);
		$this->pdf->SetWidths(array(150,110));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize-1);
		$this->pdf->row(array('','Disclaimer'));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize-1);
		$this->pdf->row(array('','Middels de geconsolideerde vermogensrapportage streven wij naar een zo zorgvuldig mogelijke weergave van uw totaal belegde vermogen. Daarbij zijn wij afhankelijk van uw input. Evolf B.V. is niet verantwoordelijk voor de beleggingen die elders worden aangehouden. U kunt aan deze geconsolideerde rapportage geen rechten ontlenen.'));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
   
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

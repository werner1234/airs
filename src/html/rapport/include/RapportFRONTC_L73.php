<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/04/07 15:21:44 $
File Versie					: $Revision: 1.2 $

$Log: RapportFRONTC_L73.php,v $
Revision 1.2  2018/04/07 15:21:44  rvv
*** empty log message ***

Revision 1.1  2017/10/07 17:20:36  rvv
*** empty log message ***

Revision 1.4  2017/09/30 16:31:15  rvv
*** empty log message ***

Revision 1.3  2017/06/21 16:10:57  rvv
*** empty log message ***

Revision 1.2  2017/05/24 08:47:34  rvv
*** empty log message ***

Revision 1.1  2017/05/14 09:57:45  rvv
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

class RapportFRONTC_L73
{
	function RapportFRONTC_L73($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
		$this->rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight = 5;


    if(is_file($this->pdf->rapport_logo))
		{
		  $factor=0.035;
		  $xSize=1200*$factor;
		  $ySize=769*$factor;
	    $this->pdf->Image($this->pdf->rapport_logo,297-$xSize-($this->pdf->marge*1),$this->pdf->marge*1, $xSize, $ySize);
		}



		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
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
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email
                          FROM
                            Portefeuilles
                            LEFT JOIN Clienten ON Portefeuilles.Client = Clienten.Client
                            LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
                            LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
                            LEFT Join CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille
                          WHERE
                            Portefeuilles.Portefeuille = '".$portefeuille."'";

			$this->DB->SQL($query);
			$this->DB->Query();
			$portefeuilledata[] = $this->DB->nextRecord();

		}
		$this->pdf->SetY(60);
		$this->pdf->SetWidths(array(30,40,25,120));
		if($_POST['anoniem']!=1)
		{
			foreach($portefeuilledata as $pdata)
			{
				$this->pdf->row(array('',$pdata['Portefeuille'],$pdata['Depotbank'],$pdata['Naam']));
				$this->pdf->Ln(1);
			}
		}

		$this->pdf->SetAligns(array('L','L','L'));
		$this->pdf->SetY(150);
		$this->pdf->SetWidths(array($this->pdf->marge,100));
		$this->pdf->setFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
		$this->pdf->rect($this->pdf->marge*2,147,297-$this->pdf->marge*4,11,'F');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+10);
		$this->pdf->setTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
		$this->pdf->row(array('',vertaalTekst("Performance rapportage",$this->pdf->rapport_taal)));


		$rapportagePeriode = date("j",$this->rapportageDatumVanafJul)." ".
			vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
			date("Y",$this->rapportageDatumVanafJul).
			' t/m '.
			date("j",$this->rapportageDatumJul)." ".
			vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
			date("Y",$this->rapportageDatumJul);

		$this->pdf->setTextColor(0);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array($this->pdf->marge,30,100));
    $this->pdf->SetY(165);
    $this->pdf->row(array('',vertaalTekst('Datum',$this->pdf->rapport_taal),(date("d"))." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));

		$this->pdf->SetY(165);
		$this->pdf->SetAligns(array('L','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize+2);
		$this->pdf->SetWidths(array(297-$this->pdf->marge*3-130,130));
		$this->pdf->row(array('',vertaalTekst('Rapportageperiode',$this->pdf->rapport_taal).' '.$rapportagePeriode));
		$this->pdf->ln(2);
		$this->pdf->row(array(' ',vertaalTekst('Geconsolideerde vermogensrapportage',$this->pdf->rapport_taal)));
		$this->pdf->ln();



/*
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->DB=new DB();
    $query="SELECT Telefoon,Fax,Email,Naam,Adres,Woonplaats,website FROM Vermogensbeheerders WHERE Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $this->DB->SQL($query);
		$this->DB->Query();
		$vermogensbeheerder = $this->DB->nextRecord();

		//$this->pdf->SetY(185);
		//$this->pdf->SetWidths(array(175-$this->pdf->marge,$xSize));
		//$this->pdf->SetAligns(array('R','C'));
		//$this->pdf->row(array('',$vermogensbeheerder['Adres'].", ".$vermogensbeheerder['Woonplaats']));
		//$this->pdf->row(array('','Telefoon: '.$vermogensbeheerder['Telefoon']));
		//$this->pdf->row(array('',$vermogensbeheerder['website']));
	*/
		$this->pdf->rowHeight=$this->rowHeightBackup;

	  $this->pdf->frontPage=true;
    $this->pdf->last_rapport_type="FRONT";
	  $this->pdf->addPage('L');
		$this->pdf->setTextColor(0);
		$this->pdf->setFillColor(0);

	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;

	//	listarray($vermogensbeheerder);
  //  listarray($this->pdf->portefeuilledata);
	}
}
?>
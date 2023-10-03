<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2016/09/21 16:09:23 $
File Versie					: $Revision: 1.5 $

$Log: RapportHUIS_L70.php,v $
Revision 1.5  2016/09/21 16:09:23  rvv
*** empty log message ***

Revision 1.4  2016/09/04 14:42:06  rvv
*** empty log message ***

Revision 1.3  2016/08/31 16:18:01  rvv
*** empty log message ***

Revision 1.2  2016/08/13 16:55:26  rvv
*** empty log message ***

Revision 1.1  2016/07/02 09:36:54  rvv
*** empty log message ***

Revision 1.3  2016/06/30 06:28:24  rvv
*** empty log message ***

Revision 1.2  2016/06/29 16:04:07  rvv
*** empty log message ***

Revision 1.1  2016/05/29 10:19:26  rvv
*** empty log message ***

Revision 1.1  2016/05/15 17:15:00  rvv
*** empty log message ***



*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportHUIS_L70
{
	function RapportHUIS_L70($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "HUIS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Voorwoord";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatumVanafJul = db2jul($this->rapportageDatumVanaf);
		$this->rapportageDatum = $rapportageDatum;
		$this->rapportageDatumJul = db2jul($this->rapportageDatum);
		$this->pdf->rapportCounter = count($this->pdf->page);

		$this->DB = new DB();

	}


	function writeRapport()
	{
		global $__appvar;

    $fontsize = 11; //$this->pdf->rapport_fontsize
		$this->pdf->frontPage = true;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);

		if($this->pdf->portefeuilledata['txtKoppeling'] !='')
		{
			$koppeling = stripslashes($this->pdf->portefeuilledata[$this->pdf->portefeuilledata['txtKoppeling']]);
			$koppeling = stripslashes($koppeling);
			$query = "SELECT * FROM custom_txt WHERE
  type = '".$this->pdf->portefeuilledata['txtKoppeling']."' AND
  field = '".$this->pdf->rapport_type."_".$koppeling."' AND
  Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'  ";
			$this->DB->SQL($query);
			$txtData = $this->DB->lookupRecord();
			$titel = $txtData['title'];
			$template=new templateEmail($txtData['txt']);
			$templatedData=$template->templateData($template->getPortefeuileValues($this->portefeuille));

			$briefData = html_entity_decode(strip_tags($templatedData['body']));

			$txt = $briefData;//."\n".$this->pdf->portefeuilledata['AccountmanagerNaam'];
		}



		$rowHeightBackup=$this->pdf->rowHeight;
		$this->pdf->rowHeight = 5;
		//$this->pdf->SetFont($this->pdf->brief_font, '', 11);
		$this->pdf->SetWidths(array(1, 265));
		$this->pdf->SetAligns(array('L', 'L'));
		$this->pdf->AddPage('L');
		$this->pdf->templateVars['HUISPaginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving['HUISPaginas']=$this->pdf->rapport_titel;
		$portefeuilledata=$this->pdf->portefeuilledata;
		$portefeuilledata['tekst'] = $txt;
		$this->pdf->ln();
		$this->pdf->SetFont($this->pdf->rapport_font, 'B', $fontsize);
		$this->pdf->row(array('', $titel));
		$this->pdf->ln();
		$this->pdf->SetFont($this->pdf->rapport_font, '', $fontsize);
		$this->pdf->row(array('', $portefeuilledata['tekst']));

		$this->pdf->rowHeight = $rowHeightBackup;

	}
}
?>

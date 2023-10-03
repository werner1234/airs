<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/09/28 17:20:17 $
 		File Versie					: $Revision: 1.2 $

 		$Log: RapportFRONTC_L49.php,v $
 		Revision 1.2  2019/09/28 17:20:17  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/12/24 16:00:30  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/01/08 16:51:48  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2011/12/04 12:56:32  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2010/10/31 15:42:53  rvv
 		*** empty log message ***

 		Revision 1.3  2010/10/27 16:20:25  rvv
 		*** empty log message ***

 		Revision 1.2  2010/05/30 12:45:04  rvv
 		*** empty log message ***

 		Revision 1.1  2009/01/10 16:01:47  rvv
 		*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFRONTC_L49
{
	function RapportFRONTC_L49($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

		$this->DB = new DB();

	}


	function writeRapport()
	{
		global $__appvar;
			$this->pdf->frontPage = true;

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

 
    $this->pdf->AddPage();
    $this->pdf->setY(45);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize+2);
    $this->pdf->SetWidths(array(25,150));
    $this->pdf->row(array('','Consolidatie'));
    if($this->pdf->lastPOST['anoniem']==0)
    {
      $this->pdf->Ln(2);
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $this->pdf->setY(50);
      $this->pdf->SetWidths(array(25, 25, 35, 75, 75));
      $this->pdf->SetAligns(array('L', 'L', 'L', 'L', 'L'));
      $this->pdf->row(array('', 'Portefeuille', 'Depotbank', 'Naam'));
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      foreach ($portefeuilledata as $port)
      {
        $this->pdf->row(array('', $port['PortefeuilleVoorzet'] . $port['Portefeuille'], $port['Depotbank'], $port['Naam']));//, $port['Naam1']
      }
      $addedPortefeuilleInfo = true;
    }
    $this->pdf->AddPage();
  

	}
}
?>
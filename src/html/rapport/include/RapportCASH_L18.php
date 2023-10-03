<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2008/12/03 10:56:27 $
 		File Versie					: $Revision: 1.7 $
 		
 		$Log: RapportCASH_L18.php,v $
 		Revision 1.7  2008/12/03 10:56:27  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2008/11/18 11:16:41  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2008/11/13 10:11:26  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2008/09/15 08:04:24  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2008/06/04 08:21:07  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2008/05/16 08:13:26  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/03/18 09:56:48  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2007/12/14 14:12:19  rvv
 		*** empty log message ***
 		
 	
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");

class RapportCASH_L18
{
	function RapportCASH_L18($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "CASH";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumVanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Cash Flow Overzicht";
		$this->pdf->rapport_header = array('','Jaar',"Aflossing\nObligaties","Rente\nObligaties","Totaal\nPer Jaar");
		
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		

	}
	
	function formatGetal($waarde, $dec)
	{
	  if($waarde<>0)
		return number_format($waarde,$dec,",",".");
	}
	
	function writeRapport()
	{
	//  adodb_date_test();
		global $__appvar;
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();

		$cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		$cashfow->genereerTransacties();
		$regels = $cashfow->genereerRows();

$gegevens = $cashfow->gegevens;

	
	if(count($gegevens['jaar']) >0)
	{
	  $this->pdf->AddPage();
	  $this->pdf->templateVars['CASHPaginas']=$this->pdf->customPageNo+$this->pdf->extraPage;
	  $this->pdf->last_rapport_type = $this->pdf->rapport_type;
    $this->pdf->last_rapport_titel = $this->pdf->rapport_titel;		
	}


  $this->pdf->switchFont('fonds');
  $n=0;
  foreach ($gegevens['jaar'] as $jaar => $waarden)
  {
    $this->pdf->Row(array('',$jaar,$this->formatGetal($waarden['lossing']),$this->formatGetal($waarden['rente']),$this->formatGetal($waarden['lossing']+$waarden['rente'])));
    $n++;
    if ($n > 10)
      break;
  }

   $this->pdf->rapport_header = array('','Maand',"Aflossing\nObligaties","Rente\nObligaties","Totaal\nPer Jaar");
   if(count($gegevens['maand']) >0)
   {
     $this->pdf->addPage();
   }
  foreach ($gegevens['maand'] as $jaar => $waarden)
  {
    $this->pdf->Row(array('',$jaar,$this->formatGetal($waarden['lossing']),$this->formatGetal($waarden['rente']),$this->formatGetal($waarden['lossing']+$waarden['rente'])));
  }
  
  if(count($gegevens['maand']) >0)
  {
     $this->pdf->templateVars['CASHPaginas'] .= " - " . ($this->pdf->customPageNo+$this->pdf->extraPage);
  }
}
		
	

}
?>
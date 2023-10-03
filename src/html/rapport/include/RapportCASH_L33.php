<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/03/10 14:08:16 $
 		File Versie					: $Revision: 1.3 $

 		$Log: RapportCASH_L33.php,v $
 		Revision 1.3  2019/03/10 14:08:16  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/10/13 12:30:21  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/09/21 16:09:23  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2012/09/13 15:58:07  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2008/12/03 10:55:05  rvv
 		*** empty log message ***

 		Revision 1.5  2008/11/13 10:11:07  rvv
 		*** empty log message ***

 		Revision 1.4  2008/06/04 08:19:32  rvv
 		*** empty log message ***

 		Revision 1.3  2008/05/29 07:04:19  rvv
 		*** empty log message ***

 		Revision 1.2  2008/05/06 10:22:42  rvv
 		*** empty log message ***

 		Revision 1.1  2007/12/14 14:12:19  rvv
 		*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
//ini_set('max_execution_time',60);
class RapportCASH_L33
{
	function RapportCASH_L33($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "CASH";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		if($this->pdf->rapport_CASH_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_CASH_titel;
		else
			$this->pdf->rapport_titel = "Cashflow";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


	function writeRapport()
	{
		global $__appvar;
		$this->pdf->widthA = array(40,80,40,40,20,20,20);
		$this->pdf->alignA = array('L','L','L','R','R','R','R');
		$this->pdf->AddPage();
		$this->pdf->templateVars['CASHPaginas'] = $this->pdf->customPageNo;
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);

		// print categorie headers
	  $this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		$cashfow->genereerTransacties();
		$regels = $cashfow->genereerRows();

		foreach ($cashfow->regelsRaw as $regel)
		{
		  //listarray($regel);
			$regel[3]=$this->formatGetal($regel[3]/$this->pdf->ValutaKoersEind,0);
			$this->pdf->Row($regel);
		}
    $this->pdf->ln();
    if($this->pdf->debug)
	    $this->pdf->Row(array('','','totaal',$this->formatGetal($cashfow->totaalWaarde,0),'',$this->formatGetal($cashfow->totaalActueel,0),$this->formatGetal($cashfow->totaalActueelJaar,0)));
    else
   	  $this->pdf->Row(array('','','totaal',$this->formatGetal($cashfow->totaalWaarde/$this->pdf->ValutaKoersEind,0),''));

	  $this->pdf->Row(array('','','Macaulay Duration ',$this->formatGetal($cashfow->totaalActueelJaar/$cashfow->totaalActueel,2).' jaar'));
	  $this->pdf->ln();
    if($this->pdf->debug)
    	foreach ($cashfow->ytm as $fonds=>$ytm)
	     $this->pdf->Row(array('',$fonds,'YTM ',$this->formatGetal($ytm,0).' %'));


    $cashfow->ytm();
    foreach ($cashfow->YTMrows as $row)
      $this->pdf->Row($row);

    if($this->pdf->debug)
    {
      $this->pdf->ln();
      foreach ($cashfow->YTMdebugCells as $cell)
       $this->pdf->MultiCell(250,4, $cell, 0, "L");
    }

	}
}
?>
<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/11/06 15:53:41 $
 		File Versie					: $Revision: 1.2 $
 		
 		$Log: RapportCASHFLOWY.php,v $
 		Revision 1.2  2019/11/06 15:53:41  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/11/14 10:44:07  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2008/12/03 10:55:05  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2008/11/18 11:16:58  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2008/11/13 10:11:07  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2008/06/04 08:19:32  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2008/05/29 07:04:19  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/05/06 10:22:42  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2007/12/14 14:12:19  rvv
 		*** empty log message ***
 		
 	
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
//ini_set('max_execution_time',60);
class RapportCASHFLOWY
{
	function RapportCASHFLOWY($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 // 
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "CASHY";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		if($this->pdf->rapport_CASHY_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_CASHY_titel;
		else 
			$this->pdf->rapport_titel = "Cashflow Yearly";
		
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}
	
	function formatGetal($waarde, $dec)
	{
	  if($waarde <> 0)
		  return number_format($waarde,$dec,",",".");
	}

  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else 
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }
  

function fYTM($z,$p,$c,$b,$y)
{
  //$tmp = ($c + $b)* pow($z,$y+1) - $b * pow($z,$y) - ($c+$p)*$z + $p;
 	return ($c + $b)* pow($z,$y+1) - $b * pow($z,$y) - ($c+$p)*$z + $p;
}


function dfYTM($z,$p,$c,$b,$y)
{
  //$tmp = ($y+1)*($c + $b) * pow($z,$y) - $y * $b * pow($z,$y - 1) - ($c+$p);
 	return ($y+1)*($c + $b) * pow($z,$y) - $y * $b * pow($z,$y - 1) - ($c+$p);
}

function returnRate($pv,$fv,$y)
{
	return pow($fv/$pv,1.0/$y) - 1.0;
}

function bondYTM($p,$r,$b,$y)
{
	$z = $r;
	$c = $r*$b;
	$E = .00001;

	if ($r == 0)
	{
		return $this->returnRate($p,$b,$y);
	}
	for ($i = 0; i < 100; $i++)
	{
		if (abs($this->fYTM($z,$p,$c,$b,$y)) < $E) break;
		while (abs($this->dfYTM($z,$p,$c,$b,$y)) < $E) $z+= .1;
		$z = $z - ($this->fYTM($z,$p,$c,$b,$y)/$this->dfYTM($z,$p,$c,$b,$y));
	}
	if (abs($this->fYTM($z,$p,$c,$b,$y)) >= $E) return -1;  // error
	return (1/$z) - 1;
}
	
	function writeRapport()
	{
		global $__appvar;
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();

	
		$this->pdf->widthA = array(40,30,30,30,30,20,20);
		$this->pdf->alignA = array('L','L','R','R','R','R','R');
		
		$this->pdf->AddPage('P');
	
		// print categorie headers
	  $this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);	
		
	$cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		$cashfow->genereerTransacties();
		$regels = $cashfow->genereerRows();


	 foreach ($cashfow->gegevens['jaar'] as $jaar => $waarden)
  {
    $this->pdf->Row(array("",$jaar,$this->formatGetal($waarden['lossing']),$this->formatGetal($waarden['rente']),$this->formatGetal($waarden['lossing']+$waarden['rente'])));
  }

    $this->pdf->ln();
    if($this->pdf->debug)	 
	    $this->pdf->Row(array('','','','totaal',$this->formatGetal($cashfow->totaalWaarde,2),'',$this->formatGetal($cashfow->totaalActueel,2),$this->formatGetal($cashfow->totaalActueelJaar,2)));
    else 
   	  $this->pdf->Row(array('','','','totaal',$this->formatGetal($cashfow->totaalWaarde,2),''));
    
	  $this->pdf->Row(array('','','','Macaulay Duration ',$this->formatGetal($cashfow->totaalActueelJaar/$cashfow->totaalActueel,2).' jaar'));     
	  $this->pdf->ln();	 
    if($this->pdf->debug)	 
    	foreach ($cashfow->ytm as $fonds=>$ytm)
	     $this->pdf->Row(array('',$fonds,'YTM ',$this->formatGetal($ytm,2).' %'));     	  

     
    $cashfow->ytm();
    foreach ($cashfow->YTMrows as $row)
    {
      $row = array_merge(array(''),$row);
      $this->pdf->Row($row);
    }
    
    if($this->pdf->debug)	 
    {
      $this->pdf->ln();
      foreach ($cashfow->YTMdebugCells as $cell)
       $this->pdf->MultiCell(250,4, $cell, 0, "L");
    }
	

	

	  
	
	
  //ini_set('max_execution_time',10800);

	}
}
?>
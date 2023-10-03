<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/05/28 09:58:52 $
File Versie					: $Revision: 1.3 $

$Log: RapportMUT_L53.php,v $
Revision 1.3  2017/05/28 09:58:52  rvv
*** empty log message ***

Revision 1.2  2014/06/18 15:48:59  rvv
*** empty log message ***

Revision 1.1  2014/05/31 13:51:07  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportMUT_L53
{
	function RapportMUT_L53($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "MUT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Kosten";
    $this->portefeuille=$portefeuille;
	}
  
  function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


	function writeRapport()
	{
	  global $__appvar;
    $this->pdf->AddPage();
    $this->pdf->templateVars['MUTPaginas']=$this->pdf->page;
    $db=new DB();
    $query="SELECT Grootboekrekeningen.Omschrijving,
SUM(IF(Rekeningmutaties.Boekdatum>'".date('Y-m-d',$this->pdf->rapport_datumvanaf)."',(Rekeningmutaties.Credit-Rekeningmutaties.Debet)*Rekeningmutaties.Valutakoers,0)) AS bedragEurPeriode,
SUM((Rekeningmutaties.Credit-Rekeningmutaties.Debet)*Rekeningmutaties.Valutakoers) AS bedragEur
FROM
Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
INNER JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
WHERE 
Rekeningen.Portefeuille='".$this->portefeuille."' AND 
Rekeningmutaties.Boekdatum >='".date('Y',$this->pdf->rapport_datum)."-01-01' AND 
Rekeningmutaties.Boekdatum <='".date('Y-m-d',$this->pdf->rapport_datum)."'
AND Grootboekrekeningen.Kosten=1
GROUP BY Rekeningmutaties.Grootboekrekening
ORDER BY Rekeningmutaties.Grootboekrekening, Rekeningmutaties.id
";
    $db->SQL($query);
    $db->Query();

    $this->pdf->SetWidths(array(20,40,35,35));
    $this->pdf->Ln();
		$this->pdf->SetAligns(array('L','L','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('','Omschrijving','Rapportageperiode','Cumulatief '.date('Y',$this->pdf->rapport_datum)));
    $this->pdf->Line($this->pdf->marge+20,$this->pdf->GetY(),$this->pdf->marge+130,$this->pdf->GetY(),array('color'=>$this->pdf->rapport_balkKleur));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($this->pdf->rapport_regelKleur[0],$this->pdf->rapport_regelKleur[1],$this->pdf->rapport_regelKleur[2]);
    $totalen=array();
    while($data=$db->nextRecord())
    {
      if($fill==true)
		  {
		    $this->pdf->fillCell = array(0,1,1,1);
		    $fill=false;
		  }
		  else
		  {
		    $this->pdf->fillCell=array();
		    $fill=true;
		  }
      $this->pdf->Row(array('',$data['Omschrijving'],$this->formatGetal($data['bedragEurPeriode'],0),$this->formatGetal($data['bedragEur'],0)));
      $totalen['bedragEurPeriode']+=$data['bedragEurPeriode'];
      $totalen['bedragEur']+=$data['bedragEur'];
    }
  	$this->pdf->ln(2);
    $this->pdf->SetTextColor(255,255,255);
    $this->pdf->SetFillColor(127); 
    $this->pdf->fillCell = array(0,1,1,1);
		$this->pdf->Row(array('','Totaal',$this->formatGetal($totalen['bedragEurPeriode'],0),$this->formatGetal($totalen['bedragEur'],0)));
    $this->pdf->CellBorders = array();
    unset($this->pdf->fillCell);
    $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['R']);                      
    $this->pdf->SetFillColor($this->pdf->rapport_regelKleur[0],$this->pdf->rapport_regelKleur[1],$this->pdf->rapport_regelKleur[2]);
    $this->pdf->CellBorders = array();
    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
   

	}
}
?>
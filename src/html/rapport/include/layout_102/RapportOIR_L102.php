<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/24 06:30:58 $
File Versie					: $Revision: 1.8 $

$Log: RapportRISK_L80.php,v $
Revision 1.8  2020/05/24 06:30:58  rvv
*** empty log message ***

Revision 1.7  2020/05/23 16:39:00  rvv
*** empty log message ***

Revision 1.6  2019/12/01 08:15:05  rvv
*** empty log message ***

Revision 1.5  2019/12/01 07:51:04  rvv
*** empty log message ***

Revision 1.4  2019/07/06 15:43:47  rvv
*** empty log message ***

Revision 1.3  2019/01/30 16:47:26  rvv
*** empty log message ***

Revision 1.2  2019/01/12 17:08:31  rvv
*** empty log message ***

Revision 1.1  2018/10/03 15:42:01  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_102/RapportKERNZ_L102.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_102/RapportPERFG_L102.php");

class RapportOIR_L102
{

	function RapportOIR_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->kernz = new RapportKERNZ_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->perfg = new RapportPERFG_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->pdf->rapport_type = "OIR";
    $this->pdf->rapport_titel = "Prestatie t.o.v. peers";
		$this->portefeuille = $portefeuille;
	}
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }
	
	function getRefentiePortefeuilles($portefeuille)
  {
    $waarden=array();
    
    $db=new DB();
    $query="SELECT Referentieportefeuille FROM ReferentieportefeuillePerBeleggingscategorie WHERE Portefeuille='".$portefeuille."' ";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      $vkmData=array();//$this->kernz->getVKMdata($data['Referentieportefeuille']);
      $waarden[$data['Referentieportefeuille']]=$this->kernz->getPortefeuilleWaarden($data['Referentieportefeuille'],$vkmData[$data['Referentieportefeuille']]);
      $waarden[$data['Referentieportefeuille']]['Referentieportefeuille']=true;
    }
    return $waarden;
  }
  
  function getContractueleUitsluitingen($portefeuille)
  {
    $waarden=array();
    $db=new DB();
    $query="SELECT
contractueleUitsluitingen.fonds,
contractueleUitsluitingen.categorie,
contractueleUitsluitingen.portefeuille,
Fondsen.Omschrijving
FROM
contractueleUitsluitingen
INNER JOIN Fondsen ON contractueleUitsluitingen.fonds = Fondsen.Fonds
 WHERE contractueleUitsluitingen.Portefeuille='".$portefeuille."' ";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      $waarden[$data['fonds']]=$data;
      $waarden[$data['fonds']]['rendementProcent']=getFondsPerformance($data['fonds'],date('Y-m-d',$this->pdf->rapport_datumvanaf),date('Y-m-d',$this->pdf->rapport_datum));
      $waarden[$data['fonds']]['Referentieportefeuille']=true;
    }
    return $waarden;
  }

	function writeRapport()
  {
    global $__appvar;
    
    if(is_array($this->pdf->portefeuilles))
      $consolidatie=true;
    else
      $consolidatie=false;
  
    $portefeuilleWaarden=array();
    $rendementen=array();
    $namen=array();
    $vkmData=array();
  /*
    if($consolidatie)
    {
      foreach($this->pdf->portefeuilles as $portefeuille)
      {
        $portefeuilleWaarden[$portefeuille]=$this->kernz->getPortefeuilleWaarden($portefeuille,$vkmData[$portefeuille]);
        $tmp=$this->getRefentiePortefeuilles($portefeuille);
        foreach($tmp as $refPort=>$refPortdata)
        {
          $portefeuilleWaarden[$refPort]=$refPortdata;
        }
      }
    }
  */
    $portefeuilleWaarden[$this->portefeuille]=$this->kernz->getPortefeuilleWaarden($this->portefeuille,$vkmData[$this->portefeuille]);
    $tmp=$this->getRefentiePortefeuilles($this->portefeuille);
    foreach($tmp as $refPort=>$refPortdata)
    {
      $portefeuilleWaarden[$refPort]=$refPortdata;
    }
    foreach ($portefeuilleWaarden as $portefeuille=>$pdata)
    {
      $naam = $this->kernz->getCRMnaam($portefeuille);
      $namen[$portefeuille]=$naam;
      $rendementen[$portefeuille]=$pdata['rendementProcent'];
    }
    
    $tmp=$this->getContractueleUitsluitingen($this->portefeuille);
    foreach($tmp as $refPort=>$refPortdata)
    {
      $portefeuilleWaarden[$refPort]=$refPortdata;
      $namen[$refPortdata['fonds']]=$refPortdata['Omschrijving'];
      $rendementen[$refPortdata['fonds']]=$refPortdata['rendementProcent'];
    }

    arsort($rendementen);
    $eersteGrafiek=array();
    $this->pdf->SetFont($this->pdf->rapport_font,'', $this->pdf->rapport_fontsize);
  
    $this->pdf->subtitel=vertaalTekst("Verslagperiode",$this->pdf->rapport_taal)." ".date("j",$this->pdf->rapport_datumvanaf)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datumvanaf)." ".vertaalTekst("tot en met",$this->pdf->rapport_taal)." ".date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum);
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    
    $this->pdf->ln(8);
    /*
    $this->pdf->SetWidths(array(10,50+20));
    $this->pdf->SetAligns(array('L','L'));
    $this->pdf->row(array('','Verslagperiode '.date('d-m-Y', $this->pdf->rapport_datumvanaf).' t/m '.date('d-m-Y', $this->pdf->rapport_datum).''));
    */
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'b', $this->pdf->rapport_fontsize);
    $this->pdf->SetAligns(array('L','L','R'));
    $this->pdf->SetWidths(array(10,60,20));
    $this->pdf->row(array('',vertaalTekst('Portefeuille',$this->pdf->rapport_taal),vertaalTekst('Rendement',$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'', $this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor(200);//$this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']
    //listarray($namen);
    //listarray($rendementen);
    foreach($rendementen as $portefeuille=>$rendement)
    {
      if(isset($portefeuilleWaarden[$portefeuille]['Referentieportefeuille']) && $portefeuilleWaarden[$portefeuille]['Referentieportefeuille']==true)
      {
        $this->pdf->SetFont($this->pdf->rapport_font,'', $this->pdf->rapport_fontsize);
        $this->pdf->fillCell = array();
        $eersteGrafiek[($namen[$portefeuille]<>''?$namen[$portefeuille]:$portefeuille)]['benchmark']=$rendement;
      }
      else
      {
        $this->pdf->fillCell = array(0,1, 1);
        $this->pdf->SetFont($this->pdf->rapport_font,'b', $this->pdf->rapport_fontsize);
        $eersteGrafiek[($namen[$portefeuille]<>''?$namen[$portefeuille]:$portefeuille)]['portefeuille']=$rendement;
      }
      $this->pdf->row(array('',($namen[$portefeuille]<>''?$namen[$portefeuille]:$portefeuille),$this->formatGetal($rendement,1).'%'));
    }
   
    unset($this->pdf->fillCell);
  
    $this->pdf->AddPage();
    $extrax=95;
    $grafiekX=160+$extrax;
    $grafiekH=80;
    $this->pdf->setXY(120-$extrax,50+$grafiekH);
    
    $this->perfg->VBarDiagram2($grafiekX,$grafiekH,$eersteGrafiek,false,false,true);
  
    $this->pdf->subtitel='';
   

  }
 
}
?>
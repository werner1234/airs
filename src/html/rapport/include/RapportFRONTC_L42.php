<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2015/01/17 18:32:01 $
File Versie					: $Revision: 1.5 $

$Log: RapportFRONTC_L42.php,v $
Revision 1.5  2015/01/17 18:32:01  rvv
*** empty log message ***

Revision 1.4  2015/01/11 12:48:50  rvv
*** empty log message ***

Revision 1.3  2014/03/29 16:22:37  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportFRONT_L42.php");
class RapportFRONTC_L42
{
	function RapportFRONTC_L42($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->front=new RapportFRONT_L42($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->front->consolidatie=true;
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


		$this->pdf->brief_font = $this->pdf->rapport_font;
  }



	function writeRapport()
	{
	  global $__appvar;
    $this->front->writeRapport();
    return 0;
    /*
	  $this->pdf->frontPage=true;
    $this->pdf->last_rapport_type="FRONT";
	  $this->pdf->addPage('L');
    $pMarge=$this->pdf->marge;

      $this->pdf->Image('rapport/logo/rrp_front3.jpg',8,0,297-16,190);
    
   $query = "SELECT 
 if(CRM_naw.naam <> '',CRM_naw.naam,Clienten.naam) as naam, 
 if(CRM_naw.naam1 <> '',CRM_naw.naam1,Clienten.naam1) as naam1
FROM Portefeuilles
JOIN Clienten ON Portefeuilles.client=Clienten.client
LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille=CRM_naw.portefeuille
 WHERE Portefeuilles.Portefeuille IN ('".implode("','",$this->pdf->portefeuilles)."')  ";
 	  $this->DB->SQL($query);
    $this->DB->Query();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+2);  
    $this->pdf->SetWidths(array(19,100+100));
  	$this->pdf->SetAligns(array('L','L','L'));
    $this->pdf->SetY(18);
    $this->pdf->row(array('','Geconsolideerd overzicht'));
    $this->pdf->Ln(2);
    while($data=$this->DB->nextRecord())
    {
      $this->pdf->row(array('',$data['naam'].', '.$data['naam1']));
       $this->pdf->Ln(2);
    }
    
    
    //vertaalTekst('uw vermogensrapportage',$this->pdf->rapport_taal).' '.
                
        $koptekst = vertaalTekst('per',$this->pdf->rapport_taal).' '.
		            date("j ",$this->rapportageDatumJul).$__appvar["Maanden"][date("n",$this->rapportageDatumJul)].date(" Y",$this->rapportageDatumJul);

   $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+1);
   $this->pdf->SetXY(182,87);
   $this->pdf->SetTextColor(255);
   $this->pdf->Cell(6,4,'uw');
   $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize+1);
   $this->pdf->Cell(37,4,'vermogensrapportage');
   $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+1);
   $this->pdf->Cell(60,4,$koptekst);
   $this->pdf->SetTextColor(0); 
    
	  //$this->pdf->frontPage=true;
	  $this->pdf->rapport_type = "OIB";
	  $this->pdf->rapport_titel = "";//Inhoudsopgave
	  $this->pdf->addPage('L');
	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;
    */
	}
}
?>
<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/06/08 16:04:22 $
File Versie					: $Revision: 1.2 $

$Log: RapportEND.php,v $
Revision 1.2  2019/06/08 16:04:22  rvv
*** empty log message ***

Revision 1.1  2019/04/27 18:32:35  rvv
*** empty log message ***

Revision 1.3  2019/04/24 15:23:46  rvv
*** empty log message ***

Revision 1.2  2019/04/24 14:42:25  rvv
*** empty log message ***

Revision 1.1  2019/04/10 15:47:20  rvv
*** empty log message ***

Revision 1.4  2014/02/22 18:43:38  rvv
*** empty log message ***

Revision 1.3  2014/01/22 17:01:30  rvv
*** empty log message ***

Revision 1.2  2012/10/17 09:16:53  rvv
*** empty log message ***

Revision 1.1  2012/10/07 14:57:18  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportEND
{
	function RapportEND($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "END";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_END_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_END_titel;

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

	function pageCheck($extraMarge,$ystart)
	{
    if($this->pdf->getY()>$this->pdf->PageBreakTrigger-5)
    {
      if($extraMarge==0)
      {
        $extraMarge = 140;
        $this->pdf->setY($ystart);
      }
      else
      {
        $extraMarge=0;
        $this->pdf->addPage();
      }
      $this->pdf->SetWidths(array($extraMarge,40,90));
    }
    return $extraMarge;
	}


	function writeRapport()
	{
	  global $__appvar;

   	$this->pdf->rapport_type = "END";
		$this->pdf->rapport_titel = "Definitieoverzicht";
		$this->pdf->addPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    $fontSizeCorrectie=-1;
    $this->pdf->rowHeight= $this->pdf->rowHeight-1.5;
    $rowHeighBackup=$this->pdf->rowHeight;

		$this->pdf->SetAligns(array('L','L','L','L'));
    $this->pdf->SetWidths(array(0,30,210));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+$fontSizeCorrectie);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->ln();
    $begrippen=array();
    $db=new DB();
    $query="SELECT
begrippenCategorie.categorie,
begrippenCategorie.afdrukVolgorde,
begrippenRapport.begrip,
begrippenRapport.omschrijving,
begrippenRapport.afdrukVolgorde
FROM
begrippenRapport
LEFT JOIN begrippenCategorie ON begrippenRapport.categorieId = begrippenCategorie.id AND begrippenCategorie.vermogensbeheerder = begrippenRapport.vermogensbeheerder
WHERE begrippenRapport.vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY begrippenCategorie.afdrukVolgorde,begrippenCategorie.categorie, begrippenRapport.afdrukVolgorde,begrippenRapport.begrip ";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
		{
			if($data['categorie']=='')
        $data['categorie']='leeg';
      $begrippen[$data['categorie']][$data['begrip']]=$data['omschrijving'];
		}
  //  $this->pdf->Multicell(280,5,$txt,'','J');
		$col=0;
    $extraMarge=0;
    $ystart=$this->pdf->getY();
    foreach($begrippen as $categorie=>$begripData)
		{
			if($categorie<>'leeg')
      {
        $extraMarge=$this->pageCheck($extraMarge,$ystart);
        $this->pdf->SetWidths(array($extraMarge,120));
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize+$fontSizeCorrectie);
        $this->pdf->row(array('',$categorie));
        $this->pdf->ln(2);
			}
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+$fontSizeCorrectie);
      $this->pdf->SetWidths(array($extraMarge,40,100));
			foreach($begripData as $begrip=>$omschrijving)
			{
        $extraMarge=$this->pageCheck($extraMarge,$ystart);
        $this->pdf->row(array('',$begrip,$omschrijving));
			}
      $this->pdf->ln();
		
		}
    
    $this->pdf->rowHeight=$rowHeighBackup;


    	}
}
?>

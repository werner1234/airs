<?php

include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportEND_L33
{
	function RapportEND_L33($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "END";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_END_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_END_titel;

	}

	function pageCheck($extraMarge,$ystart,$yMarge=8)//$yMarge=11
	{
    if($this->pdf->getY()>$this->pdf->PageBreakTrigger-$yMarge)
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
    $this->pdf->templateVars['DEFPaginas'] = $this->pdf->customPageNo;
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
        $extraMarge=$this->pageCheck($extraMarge,$ystart,14);
        $this->pdf->SetWidths(array($extraMarge,120));
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize+$fontSizeCorrectie);
        $this->pdf->row(array('',vertaalTekst($categorie ,$this->pdf->rapport_taal)));
        $this->pdf->ln(2);
			}
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+$fontSizeCorrectie);
      $this->pdf->SetWidths(array($extraMarge,40,100));
			foreach($begripData as $begrip=>$omschrijving)
			{
        $extraMarge=$this->pageCheck($extraMarge,$ystart,6);
        $this->pdf->row(array('',vertaalTekst($begrip,$this->pdf->rapport_taal), vertaalTekst($omschrijving,$this->pdf->rapport_taal)));
			}
      $this->pdf->ln();
		
		}
    
    $this->pdf->rowHeight=$rowHeighBackup;


    	}
}
?>

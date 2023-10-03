<?php
include_once("rapportRekenClass.php");
include_once("rapport/Zorgplichtcontrole.php");
include_once("rapport/include/RapportEND_L25.php");

class RapportZORG_L25
{
	var $selectData;
	var $excelData;

	function RapportZORG_L25($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
    $this->end=new RapportEND_L25($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ZORG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		if($this->pdf->rapport_CASH_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_ZORG_titel;
		else
			$this->pdf->rapport_titel = "Zorgplichtcontrole";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
        }


	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}
	
	function printTotaal($categorieOmschrijving)
	{
    if($this->pdf->getY()>175)
    {
      $this->pdf->addPage();
      $this->pdf->ln();
    }
    $this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
    $this->pdf->CellBorders = array('','','','SUB','','SUB');
    $this->pdf->row(array('Totaal '.$categorieOmschrijving,'','',$this->formatGetal($this->waardeEurTotaal,2),'',$this->formatGetal($this->zorgtotaal,2)));
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0);
    $this->pdf->ln();
	}

	function writeRapport()
	{
		global $__appvar;
		$einddatum = $this->rapportageDatum;

		$zorgplicht = new Zorgplichtcontrole();

    $this->pdf->setWidths(array(120,30,30,30,30,30,70,30));
		$this->pdf->setAligns(array('L','R','R','R','R','R','L','R'));

		$fondswaardenClean = array();
		$fondswaardenRente = array();
		$rekeningwaarden 	 = array();
    
    $this->DB=new DB();
    $query="SELECT ZpMethode,Selectieveld1,Vermogensbeheerder FROM Portefeuilles WHERE Portefeuille='".$this->portefeuille."' ";
    $this->DB->SQL($query);
    $zpMethode = $this->DB->lookupRecord();
    
    $query="SELECT
KeuzePerVermogensbeheerder.vermogensbeheerder,
KeuzePerVermogensbeheerder.categorie,
KeuzePerVermogensbeheerder.waarde,
Zorgplichtcategorien.Omschrijving
FROM
KeuzePerVermogensbeheerder
INNER JOIN Zorgplichtcategorien ON KeuzePerVermogensbeheerder.waarde = Zorgplichtcategorien.Zorgplicht AND KeuzePerVermogensbeheerder.vermogensbeheerder = Zorgplichtcategorien.Vermogensbeheerder
WHERE KeuzePerVermogensbeheerder.vermogensbeheerder='".$zpMethode['Vermogensbeheerder']."' AND KeuzePerVermogensbeheerder.categorie='Zorgplichtcategorien'
ORDER BY KeuzePerVermogensbeheerder.Afdrukvolgorde,KeuzePerVermogensbeheerder.categorie";
    $this->DB->SQL($query);
    $this->DB->Query();
    $ZpCategorieen=array();
    while($data=$this->DB->nextRecord())
		{
      $ZpCategorieen[$data['waarde']]=$data['Omschrijving'];
		}
    

		  $pdata=$this->pdf->portefeuilledata;
		  $this->pdf->portefeuille = $pdata['Portefeuille'];
		  $this->pdf->rapport_kop = $pdata['Portefeuille']." - ".$pdata['Client']." - ".$pdata['Naam'];
		  $this->pdf->AddPage();
      $this->pdf->templateVars['ZORGPaginas']=$this->pdf->page;
      $this->pdf->templateVarsOmschrijving['ZORGPaginas']=$this->pdf->rapport_titel;
			$this->zorgMeting = "Voldoet ";
			$zorgMetingReden = "";
			$totalen = array();
      $vorigeZpdata=array();
			$this->waardeEurTotaalAlles =0;
			$portefeuille = $pdata['Portefeuille'];
		  $zpwaarde=$zorgplicht->zorgplichtMeting($pdata,$einddatum);
		  foreach($zpwaarde['detail'] as $categorie=>$details)
		  	if(!isset($ZpCategorieen[$categorie]))
          $ZpCategorieen[$categorie]=$categorie;
			foreach ($ZpCategorieen as $categorie=>$omschrijving)
			{
        $zorgplichData=$zpwaarde['detail'][$categorie];
			  foreach ($zorgplichData as $zpdata)
			  {
  		  if($zpdata['Zorgplicht'] != $vorigeZpdata['Zorgplicht'])
			  {
          if(round($this->waardeEurTotaal,1) <> 0.0)
            $this->printTotaal($ZpCategorieen[$vorigeZpdata['Zorgplicht']]);
          $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
          $this->pdf->SetTextColor($this->pdf->rapport_titel_fontcolor[0],$this->pdf->rapport_titel_fontcolor[1],$this->pdf->rapport_titel_fontcolor[2]);
  	      $this->pdf->row(array($omschrijving));
          $this->pdf->SetTextColor(0,0,0);
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  			  $this->zorgtotaal=0;
  			  $this->waardeEurTotaal =0;
 			  }
        $this->pdf->row(array($zpdata['fondsOmschrijving'], 
                              $this->formatGetal($zpdata['totaalAantal'],0),$this->formatGetal($zpdata['actueleFonds'],2), 
                              $this->formatGetal($zpdata['actuelePortefeuilleWaardeEuro'],2),
                              $this->formatGetal($zpdata['Percentage'],1),
                              $this->formatGetal($zpdata['totaal'],2)));
				$this->zorgtotaal += $zpdata['totaal'];
				$this->waardeEurTotaal += $zpdata['actuelePortefeuilleWaardeEuro'];
				$this->waardeEurTotaalAlles  += $zpdata['actuelePortefeuilleWaardeEuro'];
		  	$vorigeZpdata = $zpdata;
			  }
		  }
      if(round($this->waardeEurTotaal,1) <> 0.0)
		    $this->printTotaal();
	    $this->pdf->ln();
	    
    if($this->pdf->getY()>180)
    {
     $this->pdf->addPage();
      $this->pdf->ln();
    }
    //listarray($zpwaarde);
    //listarray($zpMethode);exit;
    $this->end->toonZorgplicht($zpwaarde, $zpMethode['ZpMethode']);
	    /*
			$this->pdf->row(array('Portefeuillewaarde',$this->formatGetal($zpwaarde['totaalWaarde'],2))); //$this->waardeEurTotaalAlles
			foreach ($zpwaarde['conclusie'] as $line)
			{
			     $this->pdf->row($line);
			}
			$this->pdf->excelData[] = array('');
	    */

	}
}
?>

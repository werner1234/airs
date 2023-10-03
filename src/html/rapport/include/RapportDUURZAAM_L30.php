<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2016/02/13 14:02:39 $
File Versie					: $Revision: 1.2 $

$Log: RapportDUURZAAM_L30.php,v $
Revision 1.2  2016/02/13 14:02:39  rvv
*** empty log message ***

Revision 1.1  2013/01/02 16:50:38  rvv
*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportDUURZAAM_L30
{
	function RapportDUURZAAM_L30($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "DUURZAAM";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		if($this->pdf->rapport_DUURZAAM_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_DUURZAAM_titel;
		else
			$this->pdf->rapport_titel = "Duurzaamheid";
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
    
    $this->pdf->widthA = array(80,30,30,30,30);
		$this->pdf->alignA = array('L','R','R','R','R');
		// voor kopjes
    $this->pdf->widthB = array(80,30+30+30+30);
		$this->pdf->alignB = array('L','C');
    $this->pdf->AddPage();
      
    $db=new DB();
    $query="SELECT TijdelijkeRapportage.fonds,
TijdelijkeRapportage.fondsOmschrijving,
TijdelijkeRapportage.type,
TijdelijkeRapportage.hoofdsector,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.beleggingssector,
TijdelijkeRapportage.beleggingssectorOmschrijving,
BeleggingscategoriePerFonds.duurzaamheid,
BeleggingscategoriePerFonds.duurzaamEcon,
BeleggingscategoriePerFonds.duurzaamSociaal,
BeleggingscategoriePerFonds.duurzaamMilieu
FROM
TijdelijkeRapportage
LEFT JOIN BeleggingscategoriePerFonds ON TijdelijkeRapportage.fonds = BeleggingscategoriePerFonds.Fonds AND 
          BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' 
WHERE 
TijdelijkeRapportage.type='fondsen' AND 
rapportageDatum ='".$this->rapportageDatum."' AND 
portefeuille = '".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
ORDER BY TijdelijkeRapportage.beleggingssectorVolgorde,TijdelijkeRapportage.fondsOmschrijving";
 
		$db->SQL($query);
		$db->Query();
    $velden=array('duurzaamEcon','duurzaamSociaal','duurzaamMilieu','duurzaamheid');
    $totalen=array('velden'=>$velden);
		while($data = $db->nextRecord())
    {
      if($data['beleggingssectorOmschrijving'] == '')
        $data['beleggingssectorOmschrijving']='Geen sector';

        if($lastSector <> $data['beleggingssectorOmschrijving'])
        {
          if($lastSector <> '')
          {  
            $this->subtotaal($totalen);
            $totalen=array('velden'=>$velden,'fondsAantal'=>0);
            $this->pdf->ln();  
          }        
          $this->pdf->SetFont($this->pdf->rapport_font,'bi',$this->pdf->rapport_fontsize);
          $this->pdf->row(array($data['beleggingssectorOmschrijving']));
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        }
      	$this->pdf->row(array('  '.$data['fondsOmschrijving'],
												$data['duurzaamEcon'],
												$data['duurzaamSociaal'],
												$data['duurzaamMilieu'],
                        $data['duurzaamheid']));
       
       foreach($velden as $veld)                 
         $totalen[$veld]+=$data[$veld]; 
       if($data['duurzaamEcon']<>0 || $data['duurzaamSociaal'] <> 0 || $data['duurzaamMilieu']<> 0 || $data['duurzaamheid']<>0 )  
         $totalen['fondsAantal']++;                  
              
         
       $lastSector=$data['beleggingssectorOmschrijving'];
       $totalen['omschrijving']='Gemiddelde '.$data['beleggingssectorOmschrijving'];
    }
    $this->subtotaal($totalen);

	}
  
  function subtotaal($totalen)
  {
    foreach($totalen['velden'] as $veld)
      $totalen[$veld]=$this->formatGetal($totalen[$veld]/$totalen['fondsAantal'],0);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
   	$this->pdf->row(array($totalen['omschrijving'],
												$totalen['duurzaamEcon'],
												$totalen['duurzaamSociaal'],
												$totalen['duurzaamMilieu'],
                        $totalen['duurzaamheid']));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);                    
  }
  
}
?>
<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2016/06/27 11:20:06 $
File Versie					: $Revision: 1.7 $

$Log: RapportZORG_L33.php,v $
Revision 1.7  2016/06/27 11:20:06  rvv
*** empty log message ***

Revision 1.6  2016/06/27 06:12:23  rvv
*** empty log message ***

Revision 1.5  2016/06/25 16:57:02  rvv
*** empty log message ***

Revision 1.4  2014/10/15 16:05:25  rvv
*** empty log message ***

Revision 1.3  2014/10/08 15:42:52  rvv
*** empty log message ***

Revision 1.2  2014/10/04 15:22:54  rvv
*** empty log message ***

Revision 1.1  2014/10/01 16:06:12  rvv
*** empty log message ***



*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");

class RapportZORG_L33
{
	function RapportZORG_L33($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ZORG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		$this->pdf->rapport_titel = "Afspraken cliënt";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatumVanafJul=db2jul($this->rapportageDatumVanaf);
		$this->rapportageDatum = $rapportageDatum;
		$this->rapportageDatumJul=db2jul($this->rapportageDatum);
		$this->pdf->extraPage =0;
		$this->DB = new DB();


		$this->rapportJaar 		= date("Y",$this->rapportageDatumJul);

		$this->pdf->brief_font = $this->pdf->rapport_font;

	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}
	function kopEnVoet()
	{
	  if(is_file($this->pdf->rapport_factuurHeader))
		{
			$this->pdf->Image($this->pdf->rapport_factuurHeader, 0, 10, 210, 34);
		}
		if(is_file($this->pdf->rapport_factuurFooter))
		{
			$this->pdf->Image($this->pdf->rapport_factuurFooter, 5, 255, 200, 37);
		}
	}


	function writeRapport()
	{
	  global $__appvar;
	  $this->pdf->addPage();
	  $this->pdf->templateVars['ZORGPaginas'] = $this->pdf->customPageNo;

    $velden=array();    
    $checkVelden=array('beleggingsDoelstelling','UitzondBel');
    $query = "desc CRM_naw";
    $this->DB->SQL($query);
    $this->DB->query();
    while($data=$this->DB->nextRecord('num'))
      $velden[]=$data[0];
    $extraVeld='';  
    foreach($checkVelden as $check)  
     if(in_array($check,$velden))
       $extraVeld.=','.$check;
 
 
 


    $beleggingsprofielen=array('100 RM'=>'Zeer defensief',
                               '20 RD - 80 RM'=>'Defensief',
                               '30 RD - 70 RM'=>'Extra gematigd defensief',
                               '40 RD - 60 RM'=>'Gematigd defensief',
                               '50 RD - 50 RM'=>'Neutraal',
                               '60 RD - 40 RM'=>'Gematigd offensief',
                               '70 RD - 30 RM'=>'Extra gematigd offensief',
                               '80 RD - 20 RM'=>'Offensief',
                               '100 RD'=>'Zeer offensief');
    $beleggingsklasse=array('100 RM'=>'100% Risicomijdend',
                               '20 RD - 80 RM'=>'20% Risicodragend - 80% Risicomijdend',
                               '30 RD - 70 RM'=>'30% Risicodragend - 70% Risicomijdend',
                               '40 RD - 60 RM'=>'40% Risicodragend - 60% Risicomijdend',
                               '50 RD - 50 RM'=>'50% Risicodragend - 50% Risicomijdend',
                               '60 RD - 40 RM'=>'60% Risicodragend - 40% Risicomijdend',
                               '70 RD - 30 RM'=>'70% Risicodragend - 30% Risicomijdend',
                               '80 RD - 20 RM'=>'80% Risicodragend - 20% Risicomijdend',
                               '100 RD'=>'100% Risicodragend');
    
	
	  $query = "SELECT verzendAanhef $extraVeld FROM CRM_naw WHERE portefeuille = '".$this->portefeuille."' ";
	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetY(60);
  	$this->pdf->SetWidths(array(20,30,5,100));
  	$this->pdf->SetAligns(array('L','L','L','L'));
    unset($this->pdf->CellBorders);
		$eersteTekens=substr($this->pdf->portefeuilledata['SoortOvereenkomst'],0,6);
		if($eersteTekens=='Advies' || $eersteTekens=='Beheer')
			$beleggingsvorm=$eersteTekens;
		else
		  $beleggingsvorm=$this->pdf->portefeuilledata['SoortOvereenkomst'];

  	$this->pdf->row(array('',vertaalTekst("Beleggingsvorm",$this->pdf->rapport_taal),':',vertaalTekst($beleggingsvorm,$this->pdf->rapport_taal)));
  	$this->pdf->row(array('',vertaalTekst("Beleggingsprofiel",$this->pdf->rapport_taal),':',vertaalTekst($beleggingsprofielen[$this->pdf->portefeuilledata['Risicoklasse']],$this->pdf->rapport_taal)));
  	$this->pdf->row(array('',vertaalTekst("Beleggingsklasse",$this->pdf->rapport_taal),':',vertaalTekst($beleggingsklasse[$this->pdf->portefeuilledata['Risicoklasse']],$this->pdf->rapport_taal)));
    
    $this->pdf->Ln(10);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(20,120));
	  $this->pdf->row(array('',vertaalTekst("Strategische- & tactische bandbreedtes per Categorie",$this->pdf->rapport_taal)));
    

    $this->toonZorgplicht();

    $this->pdf->Ln(10);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	$this->pdf->SetWidths(array(20,30,5,150));
  	$this->pdf->SetAligns(array('L','L','L','L'));
   	$this->pdf->row(array('',vertaalTekst("Doelstelling",$this->pdf->rapport_taal),':',vertaalTekst($crmData['beleggingsDoelstelling'],$this->pdf->rapport_taal)));
  	$this->pdf->row(array('',vertaalTekst("Uitgangspunten",$this->pdf->rapport_taal),':',vertaalTekst($crmData['UitzondBel'],$this->pdf->rapport_taal)));
 
	}



  function toonZorgplicht()
  {
    global $__appvar;
    $DB=new DB();

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];
    
    $this->totaalWaarde=$totaalWaarde;

    if($this->totaalWaarde == 0)
      return '';
      
$query="SELECT
SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->totaalWaarde." as percentage,
ZorgplichtPerBeleggingscategorie.Zorgplicht,
Zorgplichtcategorien.Omschrijving
FROM
Zorgplichtcategorien
INNER JOIN ZorgplichtPerBeleggingscategorie ON Zorgplichtcategorien.Zorgplicht = ZorgplichtPerBeleggingscategorie.Zorgplicht AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT JOIN TijdelijkeRapportage ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND TijdelijkeRapportage.Portefeuille =  '".$this->portefeuille."' AND
 TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
INNER JOIN CategorienPerHoofdcategorie ON ZorgplichtPerBeleggingscategorie.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
INNER JOIN Beleggingscategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = Beleggingscategorien.Beleggingscategorie 
 
WHERE Zorgplichtcategorien.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' 
GROUP BY ZorgplichtPerBeleggingscategorie.Zorgplicht 
ORDER BY Beleggingscategorien.Afdrukvolgorde
";

$query="SELECT 
SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->totaalWaarde."  as percentage, 
ZorgplichtPerBeleggingscategorie.Zorgplicht, Zorgplichtcategorien.Omschrijving
FROM
TijdelijkeRapportage
LEFT JOIN ZorgplichtPerBeleggingscategorie ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' 
LEFT JOIN Zorgplichtcategorien ON Zorgplichtcategorien.Zorgplicht = ZorgplichtPerBeleggingscategorie.Zorgplicht AND  Zorgplichtcategorien.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' 

WHERE 
 
   TijdelijkeRapportage.portefeuille =  '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
GROUP BY ZorgplichtPerBeleggingscategorie.Zorgplicht";

    $DB->SQL($query); 
    $DB->Query();
		while($data= $DB->nextRecord())
		{
		  if($data['Zorgplicht']=='')
      {
        $data['Zorgplicht']='Overige';
        $data['Omschrijving']="Overige";
		  }
		  $categorieWaarden[$data['Zorgplicht']]=$data['percentage']*100;
      $categorieOmschrijving[$data['Zorgplicht']]=$data['Omschrijving'];
		}
    
    $tmp=$this->pdf->portefeuilledata;
    $tmp['Portefeuille']=$this->portefeuille;
    $zorgplicht = new Zorgplichtcontrole();
  	$zpwaarde=$zorgplicht->zorgplichtMeting($tmp,$this->rapportageDatum);

    $tmp=array();
    foreach ($zpwaarde['conclusie'] as $index=>$regelData)
      $tmp[$regelData[0]]=$regelData;

    krsort($tmp);

//listarray($zpwaarde['conclusie']);echo $query;exit;
    //listarray($tmp);exit;

    $this->pdf->SetAligns(array('L','L','R','R','R','R','R'));
    
   	
     	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize); 
  	$this->pdf->SetWidths(array(20,40,16,16,16,20,20));
    $beginY=$this->pdf->getY();
    $this->pdf->row(array('','','Minimaal','Norm','Maximaal',"Werkelijk","Conclusie"));
    	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetAligns(array('L','L','R','R','R','R','R'));
  	//foreach ($tmp as $index=>$regelData)
    
    
  //  $this->pdf->MemImage($this->checkImg,100,$this->pdf->getY(),10,10);
    foreach ($categorieWaarden as $cat=>$percentage)
    {
      if($tmp[$cat][2])
        $risicogewogen=$tmp[$cat][2]."%";
      else
        $risicogewogen=''; 
      $min=$this->formatGetal($zpwaarde['categorien'][$cat]['Minimum'],0)."%";
      $max=$this->formatGetal($zpwaarde['categorien'][$cat]['Maximum'],0)."%";
      $norm=$this->formatGetal($zpwaarde['categorien'][$cat]['Norm'],0)."%";
  	  $this->pdf->row(array('',$categorieOmschrijving[$cat],$min,$norm,$max,$this->formatGetal($categorieWaarden[$cat],1)."%",$tmp[$cat][5]));//$risicogewogen
    }
    $this->pdf->Rect($this->pdf->marge+20,$beginY,128,count($categorieWaarden)*5+5);
  }
}
?>
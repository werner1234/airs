<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/05/29 15:45:16 $
File Versie					: $Revision: 1.3 $

$Log: RapportTRANSFEE_L51.php,v $
Revision 1.3  2019/05/29 15:45:16  rvv
*** empty log message ***

Revision 1.2  2019/05/25 16:22:07  rvv
*** empty log message ***

Revision 1.1  2018/04/18 16:17:01  rvv
*** empty log message ***

Revision 1.4  2016/08/27 16:26:45  rvv
*** empty log message ***

Revision 1.3  2016/06/09 05:49:23  rvv
*** empty log message ***

Revision 1.2  2016/06/08 15:42:01  rvv
*** empty log message ***

Revision 1.1  2016/06/05 12:37:50  rvv
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

class RapportTRANSFEE_L51
{
	function RapportTRANSFEE_L51($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "TRANSFEE";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		$this->pdf->rapport_titel = "Algemene toelichting";

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
	  $this->pdf->templateVars['TRANSFEEPaginas'] = $this->pdf->page;

    $velden=array();    
    $checkVelden=array('KlantInfo');//'MarktInfo',
    $query = "desc CRM_naw";
    $this->DB->SQL($query);
    $this->DB->query();
    while($data=$this->DB->nextRecord('num'))
      $velden[]=$data[0];
    $extraVeld='';  
    foreach($checkVelden as $check)  
     if(in_array($check,$velden))
       $extraVeld.=','.$check;
 
 	  $query = "SELECT verzendAanhef $extraVeld FROM CRM_naw WHERE portefeuille = '".$this->portefeuille."' ";
	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();

		$rapportagePeriode = date("d",$this->rapportageDatumVanafJul)." ".
			vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
			date("Y",$this->rapportageDatumVanafJul).
			' - '.
			date("d",$this->rapportageDatumJul)." ".
			vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
			date("Y",$this->rapportageDatumJul);
    
    
    $nb=$this->pdf->NbLines($this->pdf->widths[1],$crmData['KlantInfo']);
    $h=$this->pdf->rowHeight*$nb;
    $paginas=array();
    if($this->pdf->GetY()+$h>$this->pdf->PageBreakTrigger)
    {
      $tekstRegels=explode("\n",$crmData['KlantInfo']);
      $paginaYPositie=$this->pdf->GetY();
      foreach($tekstRegels as $index=>$tekst)
      {
        $paginaYPositie += $this->pdf->NbLines($this->pdf->widths[1], $tekst)*$this->pdf->rowHeight;
        if($paginaYPositie<$this->pdf->PageBreakTrigger)
        {
          $paginas[0].=$tekst."\n";
        }
        else
        {
          $paginas[1].=$tekst."\n";
        }
      }
    }

    $this->pdf->SetWidths(array(10,260));
		$this->pdf->SetAligns(array('L','L'));

		$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,'',11);
/*
		if($crmData['MarktInfo'] <> '')
		{
			$this->pdf->underline = true;
			$this->pdf->row(array('', vertaalTekst("Financiële markten in de periode", $this->pdf->rapport_taal) . ' ' . $rapportagePeriode));
			$this->pdf->underline = false;
			$this->pdf->row(array('', vertaalTekst($crmData['MarktInfo'], $this->pdf->rapport_taal)));
			$this->pdf->Ln(8);
		}
*/
    

		if($crmData['KlantInfo'] <> '')
		{
		  //$this->pdf->underline=true;
		  //$this->pdf->row(array('',vertaalTekst("Afspraken met financiële instellingen",$this->pdf->rapport_taal)));
		  //$this->pdf->underline=false;
      if(count($paginas)>0)
      {
        $this->pdf->row(array('', vertaalTekst($paginas[0], $this->pdf->rapport_taal)));
        $this->pdf->addPage();
        $this->pdf->row(array('', vertaalTekst($paginas[1], $this->pdf->rapport_taal)));
      }
      else
      {
        $this->pdf->row(array('', vertaalTekst($crmData['KlantInfo'], $this->pdf->rapport_taal)));
      }
		}

		/*
		$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
		$this->pdf->Rect($this->pdf->marge, $this->pdf->GetY()-2, 280, 8 , 'F');
		$this->pdf->SetWidths(array(280));
		$this->pdf->SetAligns(array('C'));
	  $this->pdf->row(array(vertaalTekst("Financiële markten in de periode",$this->pdf->rapport_taal).' '.$rapportagePeriode));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetAligns(array('L'));
		$this->pdf->row(array(vertaalTekst($crmData['MarktInfo'],$this->pdf->rapport_taal)));
    $this->pdf->Ln(10);
		//$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize+4);
		$this->pdf->SetFont($this->pdf->rapport_font,'',11);
		$this->pdf->Rect($this->pdf->marge, $this->pdf->GetY()-2, 280, 8 , 'F');
		$this->pdf->SetWidths(array(280));
		$this->pdf->SetAligns(array('C'));
		$this->pdf->row(array(vertaalTekst("Afspraken met financiële instellingen",$this->pdf->rapport_taal)));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetAligns(array('L'));
		$this->pdf->row(array(vertaalTekst($crmData['KlantInfo'],$this->pdf->rapport_taal)));
		*/

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
SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->totaalWaarde."  as percentage, 
ZorgplichtPerBeleggingscategorie.Zorgplicht, Zorgplichtcategorien.Omschrijving
FROM TijdelijkeRapportage
LEFT JOIN ZorgplichtPerBeleggingscategorie ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' 
LEFT JOIN Zorgplichtcategorien ON Zorgplichtcategorien.Zorgplicht = ZorgplichtPerBeleggingscategorie.Zorgplicht AND  Zorgplichtcategorien.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' 
WHERE TijdelijkeRapportage.portefeuille =  '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
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
    $this->pdf->SetAligns(array('L','L','R','R','R','R','R'));
   	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	$this->pdf->SetWidths(array(20,40,16,16,16,20,20));
    $beginY=$this->pdf->getY();
    $this->pdf->row(array('','','Minimaal','Norm','Maximaal',"Werkelijk","Conclusie"));
   	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetAligns(array('L','L','R','R','R','R','R'));
    foreach ($categorieWaarden as $cat=>$percentage)
    {
       $min=$this->formatGetal($zpwaarde['categorien'][$cat]['Minimum'],0)."%";
      $max=$this->formatGetal($zpwaarde['categorien'][$cat]['Maximum'],0)."%";
      $norm=$this->formatGetal($zpwaarde['categorien'][$cat]['Norm'],0)."%";
  	  $this->pdf->row(array('',$categorieOmschrijving[$cat],$min,$norm,$max,$this->formatGetal($categorieWaarden[$cat],1)."%",$tmp[$cat][5]));//$risicogewogen
    }
    $this->pdf->Rect($this->pdf->marge+20,$beginY,128,count($categorieWaarden)*5+5);
  }
}
?>
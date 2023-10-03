<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2016/06/19 15:22:08 $
File Versie					: $Revision: 1.2 $

$Log: RapportKERNZ_L65.php,v $
Revision 1.2  2016/06/19 15:22:08  rvv
*** empty log message ***

Revision 1.1  2016/06/15 15:58:41  rvv
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

class RapportKERNZ_L65
{
	function RapportKERNZ_L65($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "KERNZ";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		$this->pdf->rapport_titel = "Toelichting Asset Allocatie";

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


	function writeRapport()
	{
	  global $__appvar;

    $velden=array();    
    $checkVelden=array('ToelAand','ToelObl','ToelAltern','ToelLiq','FondsAand','FondsObl','FondsAltern','FondsLiq');
    $crmObject=new NAW();


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


    $aantalGevuldeVelden=0;
    $toelichting = array('ToelAand' => 'Aandelen', 'ToelObl' => 'Obligatie', 'ToelAltern' => 'Alternatieven', 'ToelLiq' => 'Liquiditeiten');
    foreach($toelichting as $veld=>$omschrijving)
    {
      if (isset($crmData[$veld]) && $crmData[$veld]<>'' )
      {
          $aantalGevuldeVelden++;
      }
    }
    if($aantalGevuldeVelden>0)
    {
      $this->pdf->addPage();
      $this->pdf->templateVars['KERNZPaginas'] = $this->pdf->page;
      $this->pdf->SetWidths(array(15, 160));
      $this->pdf->SetAligns(array('L', 'L'));
      $this->pdf->ln();
      $this->pdf->setTextColor(0,0,0);
      foreach ($toelichting as $veld => $omschrijving)
      {
        if (isset($crmData[$veld]) && $crmData[$veld]<>'')
        {
          if($crmObject->data['fields'][$veld]['description'] <> '')
            $omschrijving=$crmObject->data['fields'][$veld]['description'];

          $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
          $this->pdf->row(array('', '• ' . vertaalTekst($omschrijving, $this->pdf->rapport_taal)));
          $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
          $this->pdf->row(array('', '   ' . vertaalTekst($crmData[$veld], $this->pdf->rapport_taal)));
          $this->pdf->ln();
        }
      }
    }

    $aantalGevuldeVelden=0;
    $fondsen = array('FondsAand' => 'Aandelen', 'FondsObl' => 'Obligatie', 'FondsAltern' => 'Alternatieven', 'FondsLiq' => 'Liquiditeiten');
    foreach($fondsen as $veld=>$omschrijving)
    {
      if (isset($crmData[$veld]) && $crmData[$veld] <>'' )
      {
        $aantalGevuldeVelden++;
      }
    }
    if($aantalGevuldeVelden>0)
    {
      $this->pdf->rapport_titel = "Verantwoorde fondsen in Portefeuille";
      $this->pdf->addPage();
      $this->pdf->templateVars['KERNZ2Paginas'] = $this->pdf->page;
      $this->pdf->SetWidths(array(15, 160));
      $this->pdf->SetAligns(array('L', 'L'));
      $this->pdf->ln();
      $this->pdf->setTextColor(0,0,0);
      foreach ($fondsen as $veld => $omschrijving)
      {
        if($crmObject->data['fields'][$veld]['description'] <> '')
          $omschrijving=$crmObject->data['fields'][$veld]['description'];

        if(isset($crmData[$veld]) && $crmData[$veld] <>'' )
        {
          $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
          $this->pdf->row(array('', '• ' . vertaalTekst($omschrijving, $this->pdf->rapport_taal)));
          $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
          $this->pdf->row(array('', '   ' . vertaalTekst($crmData[$veld], $this->pdf->rapport_taal)));
          $this->pdf->ln();
        }
      }
    }
		/*

		$rapportagePeriode = date("d",$this->rapportageDatumVanafJul)." ".
			vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
			date("Y",$this->rapportageDatumVanafJul).
			' - '.
			date("d",$this->rapportageDatumJul)." ".
			vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
			date("Y",$this->rapportageDatumJul);
	  $this->pdf->Ln(10);
		$this->pdf->underline=true;
		$this->pdf->row(array('',vertaalTekst("Afspraken met financiële instellingen",$this->pdf->rapport_taal)));
		$this->pdf->underline=false;
		$this->pdf->row(array('',vertaalTekst($crmData['KlantInfo'],$this->pdf->rapport_taal)));

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



}
?>
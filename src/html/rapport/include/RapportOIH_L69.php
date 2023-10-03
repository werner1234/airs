<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2016/05/11 16:03:47 $
File Versie					: $Revision: 1.1 $

$Log: RapportOIH_L69.php,v $
Revision 1.1  2016/05/11 16:03:47  rvv
*** empty log message ***

Revision 1.1  2014/12/13 19:24:44  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIH_L69
{
	function RapportOIH_L69($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIH";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		$this->pdf->rapport_titel = "Verdeling over portefeuilles";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


	function printKop($title, $type="default")
	{
		switch($type)
		{
			case "b" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'b';
			break;
			case "bi" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'bi';
			break;
			case "i" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'i';
			break;
			default :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = '';
			break;
		}

		$this->pdf->SetFont($font,$fonttype,$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor[r],$this->pdf->rapport_default_fontcolor[g],$this->pdf->rapport_default_fontcolor[b]);
	}
  
  function getWaarden($portefeuille,$totDatum)
  {
    $fondsen=berekenPortefeuilleWaarde($portefeuille,$totDatum,false,$this->pdf->rapportageValuta,$vanafDatum);
    $totRapKoers=getValutaKoers($this->pdf->rapportageValuta,$totDatum);
	  $totaal=array();
    foreach($fondsen as $id=>$regel)
    {
      $totaal['waardeEur']+=round(($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers),2);
    }
    return $totaal;    
  }

  
	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$DB = new DB();

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->AddPage();

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) /".$this->pdf->ValutaKoersEind."  AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];
    
    if(is_array($this->pdf->__appvar['consolidatie']))
    {
      $fillPortefeuilles=$this->pdf->portefeuilles;
      foreach($fillPortefeuilles as $portefeuille)
      {
         $this->perfWaarden[$portefeuille]=$this->getWaarden($portefeuille,$this->rapportageDatum);
          $this->perfWaarden[$portefeuille]['percentage']=0;
         $this->perfWaarden[$portefeuille]['percentage']+=$this->perfWaarden[$portefeuille]['waardeEur']/$totaalWaarde;
      }
      
    }
    else
    {
       $this->perfWaarden[$this->portefeuille]=$this->getWaarden($this->portefeuille,$this->rapportageDatum);
       $this->perfWaarden[$this->portefeuille]['percentage']=0;
       $this->perfWaarden[$this->portefeuille]['percentage']+=$this->perfWaarden[$this->portefeuille]['waardeEur']/$totaalWaarde;    
    }
    $totalen=array();
    $this->pdf->SetWidths(array(40,40,40));
    $this->pdf->SetAligns(array('L','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Ln();
    $this->pdf->Row(array('Portefeuille','Waarde ',"Aandeel op totaal"));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach($this->perfWaarden as $portefeulle=>$waarden)
    {
      $this->pdf->Row(array($portefeulle,$this->formatGetal($waarden['waardeEur'],2),$this->formatGetal($waarden['percentage']*100,1)."%"));
      $totalen['waardeEur']+=$waarden['waardeEur'];
      $totalen['percentage']+=$waarden['percentage'];
    }
    $this->pdf->Row(array('Totaal',$this->formatGetal($totalen['waardeEur'],2),$this->formatGetal($totalen['percentage']*100,1)."%"));

  
  //listarray($this->perfWaarden);   

    
    

	}
}
?>
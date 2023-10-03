<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/02/22 18:46:19 $
File Versie					: $Revision: 1.7 $

$Log: RapportVAR_L12.php,v $
Revision 1.7  2020/02/22 18:46:19  rvv
*** empty log message ***

Revision 1.6  2020/02/05 17:12:14  rvv
*** empty log message ***

Revision 1.5  2019/07/06 15:43:47  rvv
*** empty log message ***

Revision 1.4  2019/06/22 16:31:44  rvv
*** empty log message ***

Revision 1.3  2019/06/12 15:23:21  rvv
*** empty log message ***

Revision 1.2  2019/05/08 15:11:07  rvv
*** empty log message ***

Revision 1.1  2019/03/23 17:05:54  rvv
*** empty log message ***

Revision 1.5  2019/02/20 16:51:10  rvv
*** empty log message ***

Revision 1.9  2019/02/16 19:37:13  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVAR_L12
{
	function RapportVAR_L12($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VAR";
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "";
    
    
   $this->perioden=array();
    $this->perioden['start']= $rapportageDatumVanaf;
   $this->perioden['eind']= $this->rapportageDatum;

    
    
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    if((db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf)) || (db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01")))
    {
      $this->tweedePerformanceStart = substr($this->pdf->PortefeuilleStartdatum,0,10);
    }
    else
    {
      $this->tweedePerformanceStart = "$RapStartJaar-01-01";
    }
    $this->perioden['jan']=$this->tweedePerformanceStart;
    $this->pdf->tweedePerformanceStart= db2jul($this->tweedePerformanceStart );

		$this->portefeuille = $portefeuille;

		$this->pdf->pieData = array();
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }


  
	function writeRapport()
	{
		
	
		$this->pdf->AddPage();
		$this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving[	$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->setDrawColor($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2]);
    
    $index=new indexHerberekening();
    $rendamentWaarden['Periode'] = $index->getWaarden($this->perioden['start'] ,$this->perioden['eind'] ,$this->portefeuille,'','maanden',$this->pdf->rapportageValuta);
    $rendamentWaarden['Jaar'] = $index->getWaarden($this->perioden['jan'] ,$this->perioden['eind'] ,$this->portefeuille,'','maanden',$this->pdf->rapportageValuta);
    $totalen=array();
   
    $db=new DB();
    $valuta=$this->pdf->rapportageValuta;
    if($valuta=='')
      $valuta='EUR';
    $query="SELECT Valuta,Omschrijving FROM Valutas WHERE Valuta='".$valuta."'";
    $db->SQL($query);
    $valutaOmschrijving=$db->lookupRecord();
    
    $somItems=array('stortingen','onttrekkingen','resultaatVerslagperiode','kosten','opbrengsten','waardeMutatie','ongerealiseerd','gerealiseerd');
    foreach($rendamentWaarden as $periode=>$maandWaarden)
    {
      foreach($maandWaarden as $maandIndex=>$waarden)
      {
        if (!isset($totalen[$periode]['waardeBegin']))
        {
          $totalen[$periode]['waardeBegin'] = $waarden['waardeBegin'];
        }
        $totalen[$periode]['waardeHuidige'] = $waarden['waardeHuidige'];
        foreach ($somItems as $item)
        {
          $totalen[$periode][$item] += $waarden[$item];
          if($item=='stortingen')
            $totalen[$periode]['stortOnt'] += $waarden[$item];
          if($item=='onttrekkingen')
            $totalen[$periode]['stortOnt'] -= $waarden[$item];
          if($item=='ongerealiseerd'||$item=='gerealiseerd')
            $totalen[$periode]['resultaat'] += $waarden[$item];
          
        }
        $totalen[$periode]['performance'] = $waarden['index']-100;
      }

    }
    $this->pdf->ln(12);
    $lnhoogte=3;
    $this->pdf->setWidths($this->pdf->widthsA);
    $this->pdf->setAligns( array('L','L','R','R','L','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','',strtolower($valutaOmschrijving['Omschrijving']),'','',strtolower($valutaOmschrijving['Omschrijving']),''));
    //$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $dec=2;
    $this->pdf->CellFontColor=array($this->pdf->rapport_kop_fontcolor,$this->pdf->rapport_fontcolor);
    $this->pdf->ln();
    $this->pdf->ln($lnhoogte);
    $this->pdf->row(array('Beginwaarde',''                                  ,$this->formatGetal($totalen['Periode']['waardeBegin'],$dec)                  ,'','',$this->formatGetal($totalen['Jaar']['waardeBegin'],$dec),''));
    $this->pdf->ln($lnhoogte);
    $this->pdf->row(array('Stortingen en onttrekkingen',''                  ,$this->formatGetal($totalen['Periode']['stortOnt'],$dec)                     ,'','',$this->formatGetal($totalen['Jaar']['stortOnt'],$dec),''));
    $this->pdf->ln($lnhoogte);
    $this->pdf->row(array('Gerealiseerd en ongerealiseerd resultaat',''     ,'',$this->formatGetal($totalen['Periode']['resultaat']  ,$dec) ,'','',$this->formatGetal($totalen['Jaar']['resultaat'],$dec),''));
    $this->pdf->ln($lnhoogte);
    $this->pdf->row(array('Ontvangen inkomsten (coupon, dividend, rente)','','',$this->formatGetal($totalen['Periode']['opbrengsten'],$dec)               ,'','',$this->formatGetal($totalen['Jaar']['opbrengsten'],$dec),''));
    $this->pdf->ln($lnhoogte);
    $this->pdf->CellBorders=array('','','','US','','','US');
    $this->pdf->row(array('Directe kosten',''                                       ,'',$this->formatGetal($totalen['Periode']['kosten'],$dec)                    ,'','',$this->formatGetal($totalen['Jaar']['kosten'],$dec),''));
    unset($this->pdf->CellBorders);
    $this->pdf->ln($lnhoogte*2);
    $this->pdf->row(array('Resultaat over verslagperiode',''                ,'',$this->formatGetal($totalen['Periode']['resultaatVerslagperiode'],$dec)  ,'','',$this->formatGetal($totalen['Jaar']['resultaatVerslagperiode'],$dec),''));
    $this->pdf->ln($lnhoogte*2);
    $this->pdf->row(array('Eindwaarde',''                                  ,$this->formatGetal($totalen['Periode']['waardeHuidige'],$dec),'','',$this->formatGetal($totalen['Jaar']['waardeHuidige'],$dec),''));
    $this->pdf->ln($lnhoogte);
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('Rendement',''                                  ,$this->formatGetal($totalen['Periode']['performance'],2).'%','','',$this->formatGetal($totalen['Jaar']['performance'],2).'%',''));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->CellFontColor);
//    listarray($totalen);
//    listarray($rendamentWaarden);
//  exit;

	}


}
?>
<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/04 16:40:47 $
File Versie					: $Revision: 1.4 $

$Log: RapportOIS_L57.php,v $
Revision 1.4  2020/03/04 16:40:47  rvv
*** empty log message ***

Revision 1.3  2019/10/30 16:47:58  rvv
*** empty log message ***

Revision 1.2  2014/12/31 18:09:06  rvv
*** empty log message ***

Revision 1.1  2014/12/28 14:29:08  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportOnderverdelingValutaLayout.php");

class RapportOIS_L57
{
	function RapportOIS_L57($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		$this->pdf->rapport_titel = "Onderverdeling in beleggingssector";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
    
    $this->pdf->hoofdSortering='Beleggingssector';
    $this->pdf->tweedeSortering='Beleggingscategorie';
    $this->paginaVar='OISPaginas';
	}

  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec)
	{
	  if($waarde==0)
      return '';
    else  
	  	return number_format($waarde,$dec,",",".");
	}

function setFontColor($type)
{
  if($type=='fonds')
    $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
  elseif($type=='kop')
    $this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
  else
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
}

	function writeRapport()
	{
		$DB = new DB();
		global $__appvar;
		$this->pdf->underlinePercentage=0.8;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
		// voor data
		$this->pdf->widthA = array(60,65,25,25,25,30,30,20);
		$this->pdf->alignA = array('L','L','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthB = $this->pdf->widthA;
		$this->pdf->alignB = $this->pdf->alignA;

		$this->pdf->AddPage();
		$this->pdf->templateVars[$this->paginaVar]=$this->pdf->page;
        $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

		// haal totaalwaarde op om % te berekenen
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
    if($totaalWaarde==0)
      $totaalWaarde=0.001;


     $typeFilters=array('normal'=>"AND TijdelijkeRapportage.type <> 'rente'",'rente'=>"AND TijdelijkeRapportage.type = 'rente'");
     
     foreach($typeFilters as $filterType=>$filterQuery)
     {
       if($filterType=='rente')
         $groupBy='GROUP BY hoofdCat,tweedeOmschrijving';
       else
         $groupBy='GROUP BY TijdelijkeRapportage.id';  
        
       $query = "SELECT TijdelijkeRapportage.".$this->pdf->hoofdSortering."Omschrijving AS hoofdOmschrijving,
      TijdelijkeRapportage.".$this->pdf->tweedeSortering."Omschrijving AS tweedeOmschrijving, 
		 TijdelijkeRapportage.".$this->pdf->hoofdSortering." as hoofdCat,
     TijdelijkeRapportage.rekening,
     TijdelijkeRapportage.totaalAantal,
     TijdelijkeRapportage.actueleFonds,
     TijdelijkeRapportage.Valuta,
     TijdelijkeRapportage.type,
      TijdelijkeRapportage.".$this->pdf->tweedeSortering." as tweedeCat,  
		 SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro,
     SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro/$totaalWaarde*100) as aandeel,
     if(TijdelijkeRapportage.type='rekening',concat(fondsOmschrijving,' ',TijdelijkeRapportage.rekening),fondsOmschrijving) as fondsOmschrijving,
     if(TijdelijkeRapportage.type='rekening',127,".$this->pdf->hoofdSortering."Volgorde) as hoofdVolgorde,
     if(TijdelijkeRapportage.type='rekening',127,".$this->pdf->tweedeSortering."Volgorde) as tweedeVolgorde
		 FROM TijdelijkeRapportage 
		 WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
		 TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' $filterQuery " 
		 .$__appvar['TijdelijkeRapportageMaakUniek']."
     $groupBy
		 ORDER BY hoofdVolgorde, tweedeVolgorde,
     TijdelijkeRapportage.fondsOmschrijving ";
		  debugSpecial($query,__FILE__,__LINE__);
		  $DB = new DB();
		  $DB->SQL($query);
	  	$DB->Query();

		  while($data = $DB->NextRecord())
		  { 
		    if($data['type']=='rekening')
        {
          if($data['hoofdCat']=='')
          {
            $data['hoofdCat']='Liquiditeiten';
            $data['hoofdOmschrijving']='Liquiditeiten';
          }
          if($data['tweedeCat']=='')
          {
            $data['tweedeCat']='Liquiditeiten';
            $data['tweedeOmschrijving']='Liquiditeiten';
          }        
        }
        if($filterType=='rente')
        {
          $data['totaalAantal']='';
          $data['actueleFonds']='';
          $data['Valuta']='';
          $data['fondsOmschrijving']='Opgelopen rente';
        }
         
		    $regelData[$data['hoofdCat']]['waardeEur']+=$data['actuelePortefeuilleWaardeEuro'];
        $regelData[$data['hoofdCat']]['aandeel']+=$data['aandeel'];
        $regelData[$data['hoofdCat']]['omschrijving']=$data['hoofdOmschrijving'];
        $regelData[$data['hoofdCat']]['data'][$data['tweedeCat']]['waardeEur']+=$data['actuelePortefeuilleWaardeEuro'];
        $regelData[$data['hoofdCat']]['data'][$data['tweedeCat']]['aandeel']+=$data['aandeel'];
        $regelData[$data['hoofdCat']]['data'][$data['tweedeCat']]['omschrijving']=$data['tweedeOmschrijving'];
        $regelData[$data['hoofdCat']]['data'][$data['tweedeCat']]['data'][]=$data;
		}
    }

    foreach($regelData as $hoofdIndeling=>$hoofdIndelingData)
    {
     	$this->setFontColor('kop');
      $this->pdf->SetFont($this->pdf->rapport_font,'BI',$this->pdf->rapport_fontsize);
      $this->pdf->row(array($hoofdIndelingData['omschrijving']));
      foreach($hoofdIndelingData['data'] as $tweedeIndeling=>$tweedeIndelingData)
      {
        $this->setFontColor('kop');
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->row(array($tweedeIndelingData['omschrijving']));//'      '.

        foreach($tweedeIndelingData['data'] as $regel)
        {
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
         	$this->setFontColor('fonds');
          $this->pdf->row(array('',$regel['fondsOmschrijving']));
          $this->pdf->ln($this->pdf->rowHeight*-1);
		      $this->setFontColor();
          $this->pdf->row(array('','',
                                      $this->formatGetal($regel['totaalAantal'],0),
                                      $this->formatGetal($regel['actueleFonds'],2),
                                      $regel['Valuta'],
                                      $this->formatGetal($regel['actuelePortefeuilleWaardeEuro'],2),
                                      $this->formatGetal($regel['aandeel'],1)."%"));
        }
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $this->setFontColor('fonds');
        $this->pdf->row(array('','','','','Subtotaal:'));//$tweedeIndelingData['omschrijving']
        $this->pdf->ln($this->pdf->rowHeight*-1);
        $this->setFontColor();
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->row(array('','','','','',
                                $this->formatGetal($tweedeIndelingData['waardeEur'],2),
                                $this->formatGetal($tweedeIndelingData['aandeel'],1)."%"));
      }
      $this->pdf->SetFont($this->pdf->rapport_font,'BI',$this->pdf->rapport_fontsize);
      $this->pdf->CellBorders = array('','','','','','SUB');
      $this->setFontColor('fonds');
      $this->pdf->row(array('Totaal '.$hoofdIndelingData['omschrijving']));
      $this->pdf->ln($this->pdf->rowHeight*-1);
      $this->setFontColor();
      $this->pdf->row(array('','','','','',$this->formatGetal($hoofdIndelingData['waardeEur'],2),
                            $this->formatGetal($hoofdIndelingData['aandeel'],1)."%"));
      unset($this->pdf->CellBorders);                         
      $this->pdf->ln();                         
    }
   
    $this->pdf->ln(2);
    $this->pdf->CellBorders = array('','','','','',array('TS','UU'));      
    $this->setFontColor('fonds');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',vertaalTekst('Totale actuele waarde portefeuille ',$this->pdf->rapport_taal)));
    $this->pdf->ln($this->pdf->rowHeight*-1);
    $this->setFontColor();
		$this->pdf->row(array('','','','','',
                          $this->formatGetal($totaalWaarde,$this->pdf->rapport_OIV_decimaal),
                          $this->formatGetal(100,1)."%")); 
      
  

			 $this->pdf->CellBorders = array();



	}
}
?>
<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/02/19 15:02:02 $
File Versie					: $Revision: 1.26 $

$Log: RapportVOLK_L25.php,v $
Revision 1.26  2020/02/19 15:02:02  rvv
*** empty log message ***

Revision 1.25  2020/02/01 18:11:55  rvv
*** empty log message ***

Revision 1.24  2019/05/04 18:22:49  rvv
*** empty log message ***

Revision 1.23  2017/08/23 15:23:02  rvv
*** empty log message ***

Revision 1.22  2016/07/24 09:50:58  rvv
*** empty log message ***

Revision 1.21  2016/07/16 15:16:49  rvv
*** empty log message ***

Revision 1.20  2016/05/15 17:15:00  rvv
*** empty log message ***

Revision 1.19  2016/03/03 05:32:41  rvv
*** empty log message ***

Revision 1.18  2016/03/02 16:59:05  rvv
*** empty log message ***

Revision 1.17  2016/01/24 09:52:26  rvv
*** empty log message ***

Revision 1.16  2016/01/03 09:16:56  rvv
*** empty log message ***

Revision 1.15  2015/08/30 11:45:18  rvv
*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVOLK_L25
{
	function RapportVOLK_L25($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VOLK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		if($this->pdf->rapport_VOLK_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_VOLK_titel;
		else
			$this->pdf->rapport_titel = "Vergelijkend overzicht lopend kalenderjaar";
		if(substr(jul2form($this->pdf->rapport_datumvanaf),0,5) != '01-01')
			$this->pdf->rapport_titel = "Vergelijkend overzicht rapportage periode";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

		$this->pdf->underlinePercentage=0.8;
    $this->aandeel=1;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	  {
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	    return number_format($this->pdf->ValutaKoersEind,2,",",".") ." - ".number_format($waarde,$dec,",",".");
	  }
	  else
	  {
	    $waarde = $waarde / $this->pdf->ValutaKoersBegin;
	    return number_format($this->pdf->ValutaKoersBegin,2,",",".") ." - ".number_format($waarde,$dec,",",".");
	  }
	  return number_format($waarde,$dec,",",".");
  }

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	  if ($VierDecimalenZonderNullen)
	  {
      $waarde=round($waarde,4);
	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '0000' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];
	       if ($decimaal != '0' && !isset($newDec))
	       {
	         $newDec = $i;
	       }
	     }
	     return number_format($waarde,$newDec,",",".");
	   }
	  else
	   return number_format($waarde,$dec,",",".");
	  }
	  else
	   return number_format($waarde,$dec,",",".");
	}
  
  function printSubTotaalNB($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde,$resultaat=true)
  {
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
  
    if(!isset($categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro']))
    {
      $this->pdf->CellBorders = array('','','','','','','','SUB','SUB');
      $this->pdf->row(array("Subtotaal ".$lastCategorieOmschrijving,'','','',
                        $this->formatGetal($this->aandeel*$categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
                        $this->formatGetal($this->aandeel*($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']/ ($totaalWaarde/100)),$this->pdf->rapport_VOLK_decimaal_proc)));
    }
    else
    {
      $this->pdf->CellBorders = array('','','','','SUB','','','SUB','SUB','','SUB','SUB','SUB');
      if($resultaat)
      {
        $resultaatProcent=$this->formatGetal($this->aandeel*(($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro'] - $categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'] + $categorieTotaal[$lastCategorieOmschrijving]['dividendCorrected']) / ($categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'] /100)),$this->pdf->rapport_VOLK_decimaal_proc);
        $resultaatWaarde=$this->formatGetal($this->aandeel*($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']-$categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro']),$this->pdf->rapport_VOLK_decimaal);
        $resultaatDividend=$this->formatGetal($this->aandeel*$categorieTotaal[$lastCategorieOmschrijving]['dividend'],$this->pdf->rapport_VOLK_decimaal);
      }
      else
      {
        $this->pdf->CellBorders = array('','','','','SUB','','','SUB','','SUB','','','');
        $resultaatProcent='';
        $resultaatWaarde='';
        $resultaatDividend='';
      }
  
      $this->pdf->row(array("Subtotaal ".$lastCategorieOmschrijving,'','','',
                        $this->formatGetal($this->aandeel*$categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
                        '','',
                        $this->formatGetal($this->aandeel*$categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
                        $this->formatGetal($this->aandeel*($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']/ ($totaalWaarde/100)),$this->pdf->rapport_VOLK_decimaal_proc),
                        '',
                        $resultaatWaarde,
                        $resultaatDividend,
                        $resultaatProcent));

    }
    $this->pdf->CellBorders = array();
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
  
  }


	function printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde,$resultaat=true)
	{
    $this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);

	  if($this->pdf->rapport_type=='VAR')
    {
      if(!isset($categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro']))
      {
        $this->pdf->CellBorders = array('','','','','','','','','','','','','SUB','SUB');
        $this->pdf->row(array("Subtotaal ".$lastCategorieOmschrijving,'','','','','','','','','','','',
                          $this->formatGetal($this->aandeel*$categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
                          $this->formatGetal($this->aandeel*($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']/ ($totaalWaarde/100)),$this->pdf->rapport_VOLK_decimaal_proc)));
      }
      else
      {
        $this->pdf->CellBorders = array('','','','','','SUB','','SUB','SUB','SUB','','','SUB','SUB');
        if($resultaat)
        {
          $resultaatProcent=$this->formatGetal($this->aandeel*(($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro'] - $categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'] + $categorieTotaal[$lastCategorieOmschrijving]['dividendCorrected']) / ($categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'] /100)),$this->pdf->rapport_VOLK_decimaal_proc);
          $resultaatWaarde=$this->formatGetal($this->aandeel*($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']-$categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro']),$this->pdf->rapport_VOLK_decimaal);
          $resultaatDividend=$this->formatGetal($this->aandeel*$categorieTotaal[$lastCategorieOmschrijving]['dividend'],$this->pdf->rapport_VOLK_decimaal);
        }
        else
        {
          $this->pdf->CellBorders = array('','','','','','SUB','','','','','','','SUB','SUB');
          $resultaatProcent='';
          $resultaatWaarde='';
          $resultaatDividend='';
        }
        $this->pdf->row(array("Subtotaal ".$lastCategorieOmschrijving,'','','','',
                          $this->formatGetal($this->aandeel*$categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
                          '',$resultaatWaarde,$resultaatDividend,$resultaatProcent,'','',
                          $this->formatGetal($this->aandeel*$categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
                          $this->formatGetal($this->aandeel*($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']/ ($totaalWaarde/100)),$this->pdf->rapport_VOLK_decimaal_proc)
                        ));
      }
    }
    else
    {
      if(!isset($categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro']))
      {
        $this->pdf->CellBorders = array('','','','','','','','','','','','SUB','SUB');
        $this->pdf->row(array("Subtotaal ".$lastCategorieOmschrijving,'','','','','','','','','','',
                          $this->formatGetal($this->aandeel*$categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
                          $this->formatGetal($this->aandeel*($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']/ ($totaalWaarde/100)),$this->pdf->rapport_VOLK_decimaal_proc)));
      }
      else
      {
        $this->pdf->CellBorders = array('','','','','SUB','','SUB','SUB','SUB','','','SUB','SUB');
        if($resultaat)
        {
          $resultaatProcent=$this->formatGetal($this->aandeel*(($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro'] - $categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'] + $categorieTotaal[$lastCategorieOmschrijving]['dividendCorrected']) / ($categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'] /100)),$this->pdf->rapport_VOLK_decimaal_proc);
          $resultaatWaarde=$this->formatGetal($this->aandeel*($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']-$categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro']),$this->pdf->rapport_VOLK_decimaal);
          $resultaatDividend=$this->formatGetal($this->aandeel*$categorieTotaal[$lastCategorieOmschrijving]['dividend'],$this->pdf->rapport_VOLK_decimaal);
        }
        else
        {
          $this->pdf->CellBorders = array('','','','','SUB','','','','','','','SUB','SUB');
          $resultaatProcent='';
          $resultaatWaarde='';
          $resultaatDividend='';
        }
        $this->pdf->row(array("Subtotaal ".$lastCategorieOmschrijving,'','','',
                          $this->formatGetal($this->aandeel*$categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
                          '',$resultaatWaarde,$resultaatDividend,$resultaatProcent,'','',
                          $this->formatGetal($this->aandeel*$categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
                          $this->formatGetal($this->aandeel*($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']/ ($totaalWaarde/100)),$this->pdf->rapport_VOLK_decimaal_proc)
                        ));
      }
    }

          $this->pdf->CellBorders = array();
          $this->pdf->ln();
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
    
  }



	function printKop($title, $type='',$ln=false)
	{
		if($ln)
	    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,$type,$this->pdf->rapport_fontsize);
    
    if($this->pdf->getY()>174)
      $this->pdf->addPage();
    
    if($type=='BI')
      $this->pdf->SetTextColor($this->pdf->rapport_titel_fontcolor[0],$this->pdf->rapport_titel_fontcolor[1],$this->pdf->rapport_titel_fontcolor[2]);
    else
      $this->pdf->SetTextColor($this->pdf->rapport_kop2_fontcolor['r'],$this->pdf->rapport_kop2_fontcolor['g'],$this->pdf->rapport_kop2_fontcolor['b']);
    
    $this->pdf->row(array($title));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0);
  
	}
  
  function getDividend($fonds)
  {
    global $__appvar;
    
    if($fonds=='')
      return 0;
  
  
    if($this->pdf->rapport_type=='VHO' || $this->pdf->rapport_type=='VAR')
    {
      $beginFilter='';
    }
    else
    {
      $beginFilter="Rekeningmutaties.Boekdatum >= '".	$this->rapportageDatumVanaf."' AND ";
    }
     $query="SELECT rapportageDatum,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro as actuelePortefeuilleWaardeEuro,
         TijdelijkeRapportage.type,
         TijdelijkeRapportage.totaalAantal
     FROM TijdelijkeRapportage
     WHERE 
       TijdelijkeRapportage.fonds='$fonds' AND
       portefeuille = '".$this->portefeuille."'".$__appvar['TijdelijkeRapportageMaakUniek']. "
     GROUP BY rapportageDatum,TijdelijkeRapportage.type";
  
     $DB = new DB();
  	 $DB->SQL($query); 
		 $DB->Query();
     $totaal=0;
     while($data = $DB->nextRecord())
     { 
       if($data['type']=='rente')
         $rente[$data['rapportageDatum']]=$data['actuelePortefeuilleWaardeEuro'];
       elseif($data['type']=='fondsen')  
         $aantal[$data['rapportageDatum']]=$data['totaalAantal'];
     }
     
     $totaal+=($rente[$this->rapportageDatum]-$rente[$this->rapportageDatumVanaf]);
     $totaalCorrected=$totaal;

     $query="SELECT Boekdatum,(Debet*Valutakoers) as Debet,(Credit*valutakoers) as Credit,Bedrag,Rekeningmutaties.Omschrijving 
     FROM Rekeningmutaties 
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening 
     JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND 
     $beginFilter
     Rekeningmutaties.Boekdatum <= '".	$this->rapportageDatum."' AND
     Rekeningmutaties.Fonds='$fonds' AND 
     Grootboekrekeningen.Opbrengst=1";
		$DB->SQL($query); 
		$DB->Query();
    //echo "$query <br>\n";
    while($data = $DB->nextRecord())
    { 
      $boekdatum=substr($data['Boekdatum'],0,10);
      if(!isset($aantal[$data['Boekdatum']]))
      {
        $fondsAantal=fondsAantalOpdatum($this->portefeuille,$fonds,$data['Boekdatum']);
        $aantal[$boekdatum]=$fondsAantal['totaalAantal'];
      }
      $aandeel=1;
      
      if($aantal[$boekdatum] > $aantal[$this->rapportageDatum])
      {
        $aandeel=$aantal[$this->rapportageDatum]/$aantal[$boekdatum];
      } 
     // echo "$fonds $aandeel  $boekdatum ".$this->rapportageDatum." ".($data['Credit']-$data['Debet'])."<br>\n";
      $totaal+=($data['Credit']-$data['Debet']);
      $totaalCorrected+=(($data['Credit']-$data['Debet'])*$aandeel);
  
      if($aantal[$boekdatum]==0)
      {
        $totaal=0;
        $totaalCorrected=0;
      }
      
    }

    
    return array('totaal'=>$totaal,'corrected'=>$totaalCorrected);
  }

	function writeRapport()
  {
		global $__appvar;
		$aandelenCategorie='AU-A IndAand';
   // $this->pdf->rapport_fontsize-=1;

    $query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '" . $this->portefeuille . "' AND Portefeuilles.Client = Clienten.Client ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $this->portefeuilledata = $DB->nextRecord();
  
    
    if ($this->pdf->portefeuilledata['Risicoklasse'] == 'Niet beursgenoteerd, risicodragend' && $this->pdf->rapport_type <> 'VAR')
    {
      if($this->pdf->rapport_type=='VHO')
        $this->pdf->rapport_type = "VHONB";
      else
        $this->pdf->rapport_type = "VOLKNB";
    }
    
    $this->pdf->AddPage();
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  
    if ($this->pdf->rapport_type=='VAR')
    {
      $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
      $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
      $this->VOLK_L25_Normaal($this->pdf->rapport_type);
    }
    elseif ($this->pdf->portefeuilledata['Risicoklasse'] == 'Niet beursgenoteerd, risicodragend')
    {
      if($this->pdf->rapport_type=='VHO')
        $this->pdf->rapport_type = "VHONB";
      else
        $this->pdf->rapport_type = "VOLKNB";
      $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
      $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
      $this->VOLK_L25_NB();
    }
    else
    {
      $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
      $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
      $this->VOLK_L25_Normaal();
    }
    
  /*
    if($this->aandeel==1)
    {
      if ($this->pdf->getY() > 170)
      {
        $this->pdf->addPage();
      }
  
      if ($this->pdf->rapport_VOLK_rendement == 1)
      {
        $this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
      }
      elseif ($this->pdf->rapport_VOLK_rendement == 2)
      {
        $this->pdf->printSamenstellingResultaat($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
      }
  
  
      if ($this->pdf->rapport_VOLK_valutaoverzicht == 1)
      {
        $this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
      }
      elseif ($this->pdf->rapport_VOLK_valutaoverzicht == 2)
      {
        $this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf, true);
      }
  
      if ($this->pdf->portefeuilledata['AEXVergelijking'] > 0) //|| $this->pdf->rapport_layout == 8 voor L8 de index er weer uitgehaald.
      {
        if (!$this->pdf->rapport_VOLK_geenIndex)
        {
          printAEXVergelijking($this->pdf, $this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);
        }
      }
    }
    */
  }
  
  function omschrijvingAfkorten($omschrijving,$ruimte)
  {
    $stringWidth=$this->pdf->GetStringWidth($omschrijving);
    $newString='';
    if($stringWidth >= $ruimte-2)
    {
      $omschrijvingRuimte = $ruimte - $this->pdf->GetStringWidth('...') - 2;
      for ($i = 0; $i < strlen($omschrijving); $i++)
      {
        $char = $omschrijving[$i];
        $omschrijvingRuimte -= $this->pdf->GetStringWidth($char);
        if ($omschrijvingRuimte < 0)
        {
          $newString = substr($omschrijving, 0, $i);
          break;
        }
      }
      $omschrijving = $newString . '...';
    }
    return $omschrijving;
  }
  
  function VOLK_L25_NB($type)
  {
    global $__appvar;
    // haal totaalwaarde op om % te berekenen
    $DB=new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];
//hoofdscategorieVolgorde.Afdrukvolgorde as hoofdcategorieAfdrukVolgorde,
    $query = "SELECT
TijdelijkeRapportage.type,TijdelijkeRapportage.rekening,
if(TijdelijkeRapportage.type='rente', ifnull(300,TijdelijkeRapportage.hoofdcategorieVolgorde+100),
if(TijdelijkeRapportage.beleggingscategorie = 'Liquiditeiten',400,
TijdelijkeRapportage.hoofdcategorieVolgorde)) as hoofdcategorieAfdrukVolgorde,
TijdelijkeRapportage.hoofdcategorieOmschrijving as hoofdcategorieOmschrijving,
TijdelijkeRapportage.Hoofdcategorie,
TijdelijkeRapportage.beleggingscategorieVolgorde as categorieAfdrukVolgorde,
TijdelijkeRapportage.beleggingscategorieOmschrijving as categorieOmschrijving,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.fondsOmschrijving, ".
      " TijdelijkeRapportage.fonds, ".
      " TijdelijkeRapportage.actueleValuta, ".
      " TijdelijkeRapportage.rentedatum, ".
      " TijdelijkeRapportage.renteperiode, ".
      " TijdelijkeRapportage.historischeWaarde, ".
      " TijdelijkeRapportage.historischeValutakoers, ".
      " (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal, ".
      " (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta, ".
      " TijdelijkeRapportage.Valuta,
        TijdelijkeRapportage.fondsEenheid, ".
      " TijdelijkeRapportage.totaalAantal, ".
      " TijdelijkeRapportage.beginwaardeLopendeJaar, ".
      " TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
      "IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. ") as beginPortefeuilleWaardeEuro,".
      " TijdelijkeRapportage.actueleFonds,
				TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				  TijdelijkeRapportage.beleggingscategorie,
				  TijdelijkeRapportage.valuta,
				   TijdelijkeRapportage.portefeuille,
				   TijdelijkeRapportage.historischeWaarde,
				   Valutas.Valutateken,
				   Fondsen.add_date as FondsenAddDate".
      " FROM TijdelijkeRapportage
LEFT Join Valutas ON TijdelijkeRapportage.valuta = Valutas.Valuta
LEFT Join Fondsen ON TijdelijkeRapportage.fonds = Fondsen.fonds
WHERE ".
      " TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
      " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " ORDER BY hoofdcategorieAfdrukVolgorde,
TijdelijkeRapportage.Hoofdcategorie,
categorieAfdrukVolgorde,
TijdelijkeRapportage.beleggingscategorie , FondsenAddDate,
TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
//" TijdelijkeRapportage.type =  'fondsen' AND ".
    debugSpecial($query,__FILE__,__LINE__);
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
  
    $lastCategorieOmschrijving='';
    $lastHoofdcategorieOmschrijving='';
    $categorieTotaal=array();
    $hoofdcategorieTotaal=array();
    while($data = $DB->NextRecord())
    {
      //categorietotalen
      if($data['type']=='fondsen' && $data['beleggingscategorie'] <> 'Liquiditeiten')
        $fondsData[]=$data;
      elseif($data['type']=='rente')
        $renteData[]=$data;
      else
        $liquiditeitenData[]=$data;
    }
  
    $regel=0;
    $this->pdf->SetFillColor($this->pdf->rapport_row_bg[0],$this->pdf->rapport_row_bg[1],$this->pdf->rapport_row_bg[2]);
    foreach ($fondsData as $data)
    {
    
      $dividend=$this->getDividend($data['fonds']);
      if($data['categorieOmschrijving'] != $lastCategorieOmschrijving && $lastCategorieOmschrijving !='' && is_array($categorieTotaal[$lastCategorieOmschrijving]))
      {
        $this->printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde);
        $regel=0;
      }
      if($data['hoofdcategorieOmschrijving'] != $lastHoofdcategorieOmschrijving)
      {
        $this->printKop($data['hoofdcategorieOmschrijving'],'BI',true);
        $regel=0;
      }
      if($data['categorieOmschrijving'] != $lastCategorieOmschrijving)
      {
        $this->printKop($data['categorieOmschrijving'],'B',false);
        $regel=0;
      }
      $resultaat = $data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro'];
//			$procentResultaat = (($data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro']) / ($data['beginPortefeuilleWaardeEuro'] /100));
      $procentResultaat = (($data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro'] + $dividend['corrected']) / ($data['beginPortefeuilleWaardeEuro'] /100));
      if($data['beginPortefeuilleWaardeEuro'] < 0)
        $procentResultaat = -1 * $procentResultaat;
      $percentageVanTotaal = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
      $percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";
    
      if($procentResultaat > 1000 || $procentResultaat < -1000)
        $procentResultaattxt = "p.m.";
      else
        $procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VOLK_decimaal_proc);
    
      $resultaattxt = "";
      if($resultaat <> 0)
        $resultaattxt = $this->formatGetal($this->aandeel*$resultaat,$this->pdf->rapport_VOLK_decimaal);
    
      if($regel%2!=0)
        $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
      else
        unset($this->pdf->fillCell);
    
      if($data['fondsEenheid']=='0.01')
        $percentageTeken=' %';
      else
        $percentageTeken='';
  
      if($this->pdf->rapport_type=='VHO')
      {
        $data['beginwaardeLopendeJaar']=$data['historischeWaarde'];
        $data['beginPortefeuilleWaardeEuro']=$data['historischeWaardeTotaalValuta'];
    
      }
      
      $omschrijving=$this->omschrijvingAfkorten("  ".$data['fondsOmschrijving'],$this->pdf->widths[0]);
      $this->pdf->row(array($omschrijving,date('Y-m',db2jul($data['FondsenAddDate'])),
                        $this->formatAantal($this->aandeel*$data['totaalAantal'],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
                        $data['Valutateken'],
                        $this->formatGetal($this->aandeel*$data['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
                        '',
                        $this->formatGetal($data['actueleFonds'],2).$percentageTeken,
                        $this->formatGetal($this->aandeel*$data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
                        $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc),
                        "",
                        $resultaattxt,
                        $this->formatGetal($this->aandeel*$dividend['totaal'],0),
                        $procentResultaattxt)	);
      unset($this->pdf->fillCell);
      $categorieTotaal[$data['categorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
      $categorieTotaal[$data['categorieOmschrijving']]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];
      $categorieTotaal[$data['categorieOmschrijving']]['dividend'] +=$dividend['totaal'];
      $categorieTotaal[$data['categorieOmschrijving']]['dividendCorrected'] +=$dividend['corrected'];
      $hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
      $hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];
      $hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['dividend'] +=$dividend['totaal'];
      $hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['dividendCorrected'] +=$dividend['corrected'];
    
      $lastHoofdcategorieOmschrijving=$data['hoofdcategorieOmschrijving'];
      $lastCategorieOmschrijving=$data['categorieOmschrijving'];
    }
  
    $this->printSubTotaalNB($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde);
  
    if(count($renteData) > 0)
    {
      $this->printKop(vertaalTekst("Opgelopen rente en coupondatum",$this->pdf->rapport_taal), "B");
      $totaalRenteInValuta = 0 ;
      //while($data = $DB->NextRecord())
      foreach ($renteData as $data)
      {
        //$regel++;
        $rentePeriodetxt = "  ".date("d-m",db2jul($data['rentedatum']));
        if($data['renteperiode'] <> 12 && $data['renteperiode'] <> 0)
          $rentePeriodetxt .= " / ".$data['renteperiode'];
      
        $percentageVanTotaal = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
        $this->pdf->Cell($this->pdf->widths[0],4,"  ".$data['fondsOmschrijving'].' '.$rentePeriodetxt, 0,0, "L");
        $this->pdf->setX($this->pdf->marge);
        $this->pdf->row(array('','',
                          $data['Valutateken'],'',
                          $this->formatGetal($this->aandeel*$data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
                          $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc),'','','','','','','',''));
        $categorieTotaal["Opgelopen Rente"]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
        unset($this->pdf->fillCell);
      }
      $this->printSubTotaalNB("Opgelopen Rente",$categorieTotaal,$totaalWaarde);
    }
  
  
    if(count($liquiditeitenData) > 0)
    {
      $this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),"B");
      $DB1 = new DB();
      foreach($liquiditeitenData as $data)
      {
        //$regel++;
        if($data['beginPortefeuilleWaardeEuro'] == 0)
        {
          $query="SELECT actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage WHERE portefeuille = '".$this->portefeuille."' AND rapportageDatum = '".$this->rapportageDatumVanaf."' AND rekening = '".$data['rekening']."' AND type='rekening' LIMIT 1";
          $DB1->SQL($query);
          $DB1->Query();
          $begin = $DB1->NextRecord();
          $data['beginPortefeuilleWaardeEuro']=$begin['actuelePortefeuilleWaardeEuro'];
        }
      
        $percentageVanTotaal = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
        $this->pdf->row(array("  ".$data['fondsOmschrijving'].' '.$data['rekening'],'',$data['Valutateken'],'',
                          $this->formatGetal($this->aandeel*$data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
                          $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc),'','',
                          $this->formatGetal($this->aandeel*$data['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),'','','','',''
      
                        ));
        unset($this->pdf->fillCell);
        $categorieTotaal["Liquiditeiten"]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
        $categorieTotaal["Liquiditeiten"]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];
      }
      $this->printSubTotaalNB("Liquiditeiten",$categorieTotaal,$totaalWaarde,false);
    } // einde liquide
  
    // check op totaalwaarde!
    $actueleWaardePortefeuille=0;
    foreach ($categorieTotaal as $categorie=>$waardes)
    {
      $actueleWaardePortefeuille+=$waardes['actuelePortefeuilleWaardeEuro'];
    }
    if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
    {
      echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
      ob_flush();
    
    }
  
    $this->pdf->CellBorders = array('','','','','','','','SUB','SUB');
    $this->pdf->row(array("Totale actuele waarde portefeuille",'','','','','','',$this->formatGetal($totaalWaarde,$this->pdf->rapport_VOLK_decimaal),
                      $this->formatGetal(($actueleWaardePortefeuille/$totaalWaarde*100),$this->pdf->rapport_VOLK_decimaal_proc)));
    $this->pdf->CellBorders = array();
    $this->pdf->ln();
  
    printAEXVergelijking($this->pdf,'',$this->rapportageDatumVanaf,$this->rapportageDatum);
    //$this->pdf->rapport_fontsize+=1;
    if($this->pdf->rapport_VOLK_rendement == 1)
    {
      $this->pdf->ln();
      $this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
    }
    
  }
    function VOLK_L25_Normaal($type)
    {
       global $__appvar;
		// haal totaalwaarde op om % te berekenen
      $DB=new DB();
		  $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						  .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];
//hoofdscategorieVolgorde.Afdrukvolgorde as hoofdcategorieAfdrukVolgorde,
      if ($this->pdf->rapport_type=='VAR')
        $extraSort='FondsenAddDate,';
      else
        $extraSort='';
      
		$query = "SELECT
TijdelijkeRapportage.type,TijdelijkeRapportage.rekening,
if(TijdelijkeRapportage.type='rente', ifnull(300,TijdelijkeRapportage.hoofdcategorieVolgorde+100),
if(TijdelijkeRapportage.beleggingscategorie = 'Liquiditeiten',400,
TijdelijkeRapportage.hoofdcategorieVolgorde))  as hoofdcategorieAfdrukVolgorde,
TijdelijkeRapportage.hoofdcategorieOmschrijving as hoofdcategorieOmschrijving,
TijdelijkeRapportage.Hoofdcategorie,
TijdelijkeRapportage.beleggingscategorieVolgorde as categorieAfdrukVolgorde,
TijdelijkeRapportage.beleggingscategorieOmschrijving as categorieOmschrijving,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.fondsOmschrijving, ".
				" TijdelijkeRapportage.fonds, ".
				" TijdelijkeRapportage.actueleValuta, ".
						" TijdelijkeRapportage.rentedatum, ".
		  " TijdelijkeRapportage.renteperiode, ".
      " TijdelijkeRapportage.historischeWaarde, ".
      " TijdelijkeRapportage.historischeValutakoers, ".
      " (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal, ".
      " (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta, ".
      " TijdelijkeRapportage.Valuta,
        TijdelijkeRapportage.fondsEenheid, ".
				" TijdelijkeRapportage.totaalAantal, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
				"IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. ") as beginPortefeuilleWaardeEuro,".
				" TijdelijkeRapportage.actueleFonds,
				TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				  TijdelijkeRapportage.beleggingscategorie,
				  TijdelijkeRapportage.valuta,
				   TijdelijkeRapportage.portefeuille,
				   TijdelijkeRapportage.historischeWaarde,
				   Valutas.Valutateken,
				    Fondsen.add_date as FondsenAddDate".
				" FROM TijdelijkeRapportage
LEFT Join Valutas ON TijdelijkeRapportage.valuta = Valutas.Valuta
LEFT Join Fondsen ON TijdelijkeRapportage.fonds = Fondsen.fonds
WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" ORDER BY hoofdcategorieAfdrukVolgorde,
TijdelijkeRapportage.Hoofdcategorie,
categorieAfdrukVolgorde,
TijdelijkeRapportage.beleggingscategorie , $extraSort
TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
//" TijdelijkeRapportage.type =  'fondsen' AND ".
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
    $fondsData=array();
    $renteData=array();
    $liquiditeitenData=array();
		while($data = $DB->NextRecord())
		{
		  //categorietotalen
		  if($data['type']=='fondsen' && $data['beleggingscategorie'] <> 'Liquiditeiten')
		    $fondsData[]=$data;
		  elseif($data['type']=='rente')
		    $renteData[]=$data;
		  else
		    $liquiditeitenData[]=$data;
		}
    
    $regel=0;
    $this->pdf->SetFillColor($this->pdf->rapport_row_bg[0],$this->pdf->rapport_row_bg[1],$this->pdf->rapport_row_bg[2]);
    $lastCategorieOmschrijving='';
    $lastHoofdcategorieOmschrijving='';
    $categorieTotaal=array();
    $hoofdcategorieTotaal=array();
		foreach ($fondsData as $data)
		{
      
      
      if($this->pdf->rapport_type=='VHO' || $type=='VAR')
      {
        $data['beginwaardeLopendeJaar']=$data['historischeWaarde'];
        $data['beginPortefeuilleWaardeEuro']=$data['historischeWaardeTotaalValuta'];
        
      }
      
		  $dividend=$this->getDividend($data['fonds']);
	    if($data['categorieOmschrijving'] != $lastCategorieOmschrijving && $lastCategorieOmschrijving !='' && is_array($categorieTotaal[$lastCategorieOmschrijving]))
      {
        $this->printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde);
        $regel=0;
      }
      if($data['hoofdcategorieOmschrijving'] != $lastHoofdcategorieOmschrijving)
      {
        $this->printKop($data['hoofdcategorieOmschrijving'],'BI',true);
        $regel=0;
      }
      if($data['categorieOmschrijving'] != $lastCategorieOmschrijving)
      {
        $this->printKop($data['categorieOmschrijving'],'B',false);
        $regel=0;
      }
     	$resultaat = $data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro'];
//			$procentResultaat = (($data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro']) / ($data['beginPortefeuilleWaardeEuro'] /100));
      $procentResultaat = (($data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro'] + $dividend['corrected']) / ($data['beginPortefeuilleWaardeEuro'] /100));		
    	if($data['beginPortefeuilleWaardeEuro'] < 0)
				$procentResultaat = -1 * $procentResultaat;
			$percentageVanTotaal = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
			$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";

			if($procentResultaat > 1000 || $procentResultaat < -1000)
				$procentResultaattxt = "p.m.";
			else
				$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VOLK_decimaal_proc);

			$resultaattxt = "";
 			if($resultaat <> 0)
				$resultaattxt = $this->formatGetal($this->aandeel * $resultaat,$this->pdf->rapport_VOLK_decimaal);
      $dividendtxt = "";
      if($dividend['totaal'] <> 0)
        $dividendtxt = $this->formatGetal($this->aandeel * $dividend['totaal'],$this->pdf->rapport_VOLK_decimaal);

      if($regel%2!=0)
        $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
      else
        unset($this->pdf->fillCell);

      if($data['fondsEenheid']=='0.01')
        $percentageTeken=' %';
      else
        $percentageTeken='';
      
      $omschrijving=$this->omschrijvingAfkorten("  ".$data['fondsOmschrijving'],$this->pdf->widths[0]);
     
      
      if($type=='VAR')
        $this->pdf->row(array($omschrijving,
                          date('Y-m',db2jul($data['FondsenAddDate'])),
                          $this->formatAantal($this->aandeel*$data['totaalAantal'],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
                          $data['Valutateken'],
                          $this->formatGetal($data['beginwaardeLopendeJaar'],2).$percentageTeken,
                          $this->formatGetal($this->aandeel*$data['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
                          "",$resultaattxt,$dividendtxt,$procentResultaattxt,'',
                          $this->formatGetal($data['actueleFonds'],2).$percentageTeken,
                          $this->formatGetal($this->aandeel*$data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
                          $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)));
      else
		  	$this->pdf->row(array($omschrijving,
				                $this->formatAantal($this->aandeel*$data['totaalAantal'],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
												$data['Valutateken'],
                        $this->formatGetal($data['beginwaardeLopendeJaar'],2).$percentageTeken,
                        $this->formatGetal($this->aandeel*$data['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
                        "",$resultaattxt,$dividendtxt,$procentResultaattxt,'',
												$this->formatGetal($data['actueleFonds'],2).$percentageTeken,
												$this->formatGetal($this->aandeel*$data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)));
      
    unset($this->pdf->fillCell);                    
		$categorieTotaal[$data['categorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
		$categorieTotaal[$data['categorieOmschrijving']]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];
    $categorieTotaal[$data['categorieOmschrijving']]['dividend'] +=$dividend['totaal'];
    $categorieTotaal[$data['categorieOmschrijving']]['dividendCorrected'] +=$dividend['corrected'];
		$hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
		$hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];
    $hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['dividend'] +=$dividend['totaal'];
    $hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['dividendCorrected'] +=$dividend['corrected'];

    $lastHoofdcategorieOmschrijving=$data['hoofdcategorieOmschrijving'];
    $lastCategorieOmschrijving=$data['categorieOmschrijving'];
		}
    
		$this->printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde);

		if(count($renteData) > 0)
		{
		  $this->printKop(vertaalTekst("Opgelopen rente en coupondatum",$this->pdf->rapport_taal), "B");
			$totaalRenteInValuta = 0 ;
			//while($data = $DB->NextRecord())
			foreach ($renteData as $data)
			{
			    //$regel++;
			  	$rentePeriodetxt = "  ".date("d-m",db2jul($data['rentedatum']));
					if($data['renteperiode'] <> 12 && $data['renteperiode'] <> 0)
						$rentePeriodetxt .= " / ".$data['renteperiode'];

					$percentageVanTotaal = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
					$this->pdf->Cell($this->pdf->widths[0],4,"  ".$data['fondsOmschrijving'].' '.$rentePeriodetxt, 0,0, "L");
					$this->pdf->setX($this->pdf->marge);
          if($type=='VAR')
            $this->pdf->row(array('','',
                              $data['Valutateken'],'','','','','','','','',
                              $this->formatGetal($this->aandeel*$data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
                              $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc),'','','','','','','','',''));
          else
				  	$this->pdf->row(array('','',
					              $data['Valutateken'],'','','','','','','','',
												$this->formatGetal($this->aandeel*$data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc),'','','','','','','','',''));
					$categorieTotaal["Opgelopen Rente"]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
          unset($this->pdf->fillCell);
			}
			$this->printSubTotaal("Opgelopen Rente",$categorieTotaal,$totaalWaarde);
		}


		if(count($liquiditeitenData) > 0)
		{
			$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),"B");
			$DB1 = new DB();
			foreach($liquiditeitenData as $data)
			{
        //$regel++;
			  if($data['beginPortefeuilleWaardeEuro'] == 0)
			  {
			    $query="SELECT actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage WHERE portefeuille = '".$this->portefeuille."' AND rapportageDatum = '".$this->rapportageDatumVanaf."' AND rekening = '".$data['rekening']."' AND type='rekening' LIMIT 1";
    		  $DB1->SQL($query);
		      $DB1->Query();
		      $begin = $DB1->NextRecord();
		      $data['beginPortefeuilleWaardeEuro']=$begin['actuelePortefeuilleWaardeEuro'];
		    }
      
					$percentageVanTotaal = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
					$this->pdf->row(array("  ".$data['fondsOmschrijving'].' '.$data['rekening'],'',$data['Valutateken'],'',
                            $this->formatGetal($this->aandeel*$data['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												'','','','','','',
												$this->formatGetal($this->aandeel*$data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)

												));
          unset($this->pdf->fillCell);              
					$categorieTotaal["Liquiditeiten"]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
					$categorieTotaal["Liquiditeiten"]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];
			}
			$this->printSubTotaal("Liquiditeiten",$categorieTotaal,$totaalWaarde,false);
		} // einde liquide

		// check op totaalwaarde!
		$actueleWaardePortefeuille=0;
		foreach ($categorieTotaal as $categorie=>$waardes)
		{
		  $actueleWaardePortefeuille+=$waardes['actuelePortefeuilleWaardeEuro'];
		}
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();

		}
  
    if($this->pdf->rapport_type=='VAR')
    {
      $this->pdf->CellBorders = array('', '','',  '', '', '', '', '', '', '', '', '', 'SUB', 'SUB');
      $this->pdf->row(array("Totale actuele waarde portefeuille", '', '', '', '', '', '', '', '', '', '', '', $this->formatGetal($this->aandeel * $totaalWaarde, $this->pdf->rapport_VOLK_decimaal),
                        $this->formatGetal(($actueleWaardePortefeuille / $totaalWaarde * 100), $this->pdf->rapport_VOLK_decimaal_proc)));
    }
    else
    {
      $this->pdf->CellBorders = array('', '', '', '', '', '', '', '', '', '', '', 'SUB', 'SUB');
      $this->pdf->row(array("Totale actuele waarde portefeuille", '', '', '', '', '', '', '', '', '', '', $this->formatGetal($this->aandeel * $totaalWaarde, $this->pdf->rapport_VOLK_decimaal),
                        $this->formatGetal(($actueleWaardePortefeuille / $totaalWaarde * 100), $this->pdf->rapport_VOLK_decimaal_proc)));
    }
    $this->pdf->CellBorders = array();
		$this->pdf->ln();
    $ystart=$this->pdf->getY();
		$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf,$omkeren=true,$kop='Valuta');
    if($this->pdf->rapport_type<>'VAR' && $this->aandeel == 1 )
	    printAEXVergelijking($this->pdf,'',$this->rapportageDatumVanaf,$this->rapportageDatum);
    $ystop1=$this->pdf->getY();
    if($this->pdf->rapport_VOLK_rendement == 1  && $this->aandeel == 1 )
    {
      $this->pdf->ln();
      $this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
    }
    $ystop2=$this->pdf->getY();
      if($this->pdf->rapport_type=='VAR')
      {
        if ($ystop2 > $ystart) //nog op dezelfde pagina
        {
          $this->pdf->setY($ystart + 8);
        }
        elseif ($ystop2>$ystop1)
        {
          $this->pdf->setY($ystop1 + 8);
        }
        $this->pdf->setWidths(array(120, 163));
        $this->pdf->setAligns(array('J','J'));
        $this->pdf->CellBorders = array('', array('L', 'T', 'R', 'U'));
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize-1);
        $this->pdf->row(array('', 'De informatie verstrekt in dit overzicht is geenszins geschikt voor fiscale doeleinden. Cliënt wordt middels separaat schrijven jaarlijks per project door de fondsbeheerder op de hoogte gebracht van de fiscale waardering welke vervolgens gebruikt kan worden voor de belastingaangifte. Dit overzicht geeft slechts de commerciële waarderingen weer conform het waarderingsbeleid van desbetreffende fondsbeheerder met als doel cliënt inzicht te verschaffen in het rendementsverloop van de niet-beursgenoteerde vastgoedinvesteringen. Gelet op het karakter van deze investeringen kunnen de waarderingen en rendementen vertraagd zijn. De informatie in dit overzicht is deels project-specifiek in plaats van cliënt-specifiek. Zo kunnen er kleine verschillen zijn op basis van de door cliënt gekozen stortingsdatum en/of door de bank van cliënt gehanteerde valutakoers. Vanwege cliënt-specifieke belastingtarieven worden Amerikaanse projecten bruto weergegeven. Rendementen van Europese projecten worden netto weergegeven. In het overzicht wordt ook rekening gehouden met het valutarisico indien het een niet-euro project betreft, of een euro project met onderliggend valutarisico. Dit overzicht is met de grootst mogelijke zorgvuldigheid samengesteld, waarbij Auréus Group streeft naar het geven van correcte en actuele informatie. Auréus Group is echter niet verantwoordelijk voor eventuele verliezen of andere schade als gevolg van incorrecte of incomplete verwerking van gegevens in het overzicht.'));
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        unset($this->pdf->CellBorders);
        
      }
	}
}
?>
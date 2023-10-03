<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/08/12 12:17:24 $
File Versie					: $Revision: 1.17 $

$Log: RapportVOLK_L42.php,v $
Revision 1.17  2017/08/12 12:17:24  rvv
*** empty log message ***

Revision 1.16  2017/07/29 17:18:20  rvv
*** empty log message ***

Revision 1.15  2016/04/30 15:33:27  rvv
*** empty log message ***

Revision 1.14  2015/05/02 14:57:32  rvv
*** empty log message ***

Revision 1.13  2014/12/20 16:32:36  rvv
*** empty log message ***

Revision 1.12  2014/12/17 16:14:40  rvv
*** empty log message ***

Revision 1.11  2014/10/25 14:39:09  rvv
*** empty log message ***

Revision 1.10  2014/05/25 14:38:33  rvv
*** empty log message ***

Revision 1.9  2013/10/19 15:57:25  rvv
*** empty log message ***

Revision 1.8  2013/10/16 15:35:04  rvv
*** empty log message ***

Revision 1.7  2013/08/24 15:48:47  rvv
*** empty log message ***

Revision 1.6  2013/08/18 12:23:35  rvv
*** empty log message ***

Revision 1.5  2013/08/10 15:48:01  rvv
*** empty log message ***

Revision 1.4  2013/07/28 09:59:15  rvv
*** empty log message ***

Revision 1.3  2013/06/09 18:01:53  rvv
*** empty log message ***

Revision 1.2  2013/03/27 17:02:38  rvv
*** empty log message ***

Revision 1.1  2013/03/23 16:19:36  rvv
*** empty log message ***

Revision 1.8  2013/03/17 10:58:29  rvv
*** empty log message ***

Revision 1.7  2013/02/27 17:04:41  rvv
*** empty log message ***

Revision 1.6  2013/02/20 16:51:31  rvv
*** empty log message ***

Revision 1.5  2013/02/20 15:12:14  rvv
*** empty log message ***

Revision 1.4  2013/02/03 09:04:21  rvv
*** empty log message ***

Revision 1.3  2013/01/27 14:14:24  rvv
*** empty log message ***

Revision 1.2  2013/01/20 13:27:16  rvv
*** empty log message ***

Revision 1.1  2013/01/16 16:54:04  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVOLK_L42
{
	function RapportVOLK_L42($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  global $__appvar;
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VOLK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

   	$this->pdf->rapport_titel = "Portefeuille overzicht per ".date("j",$this->pdf->rapport_datum)." ".vertaalTekst($__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum);
		if(!is_array($this->pdf->excelData))
		  $this->pdf->excelData 	= array();

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->pdf->underlinePercentage=0.8;
	}

	function formatGetal($waarde, $dec,$procent=false)
	{
    if($procent==true)
      return number_format($waarde,$dec,",",".").'%';
	  if($waarde==0)
      return '';
		return number_format($waarde,$dec,",",".");
	}

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	 	if($waarde==0)
      return '';
      
	  if ($VierDecimalenZonderNullen)
	  {
	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '0000' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];
	       if ($decimaal != '0' && !$newDec)
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


	function printKop($title, $type="default")
	{
	  //if($title == 'Opgelopen rente')
    //  return 0;

		switch($type)
		{
			case "b" :
        $spaties='';
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'b';
			break;
			case "bu" :
        $spaties='';
        $extraX=1.76388;
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'bu';
			break;
			case "bi" :
        $spaties='';
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'bi';
			break;
			case "u" :
        $spaties='';
        $extraX=2;
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'u';
			break;
			default :
        $spaties='';
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = '';
        $extraX=0;
			break;
		}

		$this->pdf->SetFont($font,$fonttype,$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
		$this->pdf->SetX($this->pdf->marge+$extraX);
		$this->pdf->MultiCell(90,4, $spaties.$title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor[r],$this->pdf->rapport_default_fontcolor[g],$this->pdf->rapport_default_fontcolor[b]);
	}

	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$DB = new DB();
		$query = "SELECT Vermogensbeheerders.VerouderdeKoersDagen , Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM (Portefeuilles, Clienten)  Join Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
		$maxDagenOud=$portefeuilledata['VerouderdeKoersDagen'];
    $this->pdf->excelData[]=array("Fondsnaam","Aantal","Valuta","Koers","","Kostprijs","Bruto rendement","Marktwaarde","Kostprijs","Netto resultaat","Netto rendement","Weging");

		$this->pdf->AddPage();
    $paginaBeginY=$this->pdf->GetY();
    $this->pdf->templateVars['VOLKPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['VOLKPaginas']=$this->pdf->rapport_titel;
    

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
    
    $query="SELECT
    if(TijdelijkeRapportage.type='fondsen',1,if(TijdelijkeRapportage.type='rente',2,3)) as hoofdVolgorde,
TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,
(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal, 
(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta, 
TijdelijkeRapportage.historischeWaarde,
TijdelijkeRapportage.beginwaardeLopendeJaar,
TijdelijkeRapportage.totaalAantal,
TijdelijkeRapportage.actueleFonds,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.fonds,
TijdelijkeRapportage.actueleValuta,
TijdelijkeRapportage.fondsOmschrijving,
TijdelijkeRapportage.rekening,
TijdelijkeRapportage.beleggingssector,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.hoofdcategorie,
TijdelijkeRapportage.beleggingscategorieOmschrijving,
TijdelijkeRapportage.beleggingssectorOmschrijving,
TijdelijkeRapportage.hoofdcategorieOmschrijving,
TijdelijkeRapportage.beginPortefeuilleWaardeEuro,
TijdelijkeRapportage.beginPortefeuilleWaardeInValuta,
actuelePortefeuilleWaardeInValuta,
TijdelijkeRapportage.type,
round((UNIX_TIMESTAMP(TijdelijkeRapportage.rapportageDatum) - UNIX_TIMESTAMP(TijdelijkeRapportage.koersDatum))/86400) as koersLeeftijd,
Rekeningen.IBANnr
FROM TijdelijkeRapportage
LEFT JOIN Rekeningen ON TijdelijkeRapportage.rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0
WHERE rapportageDatum ='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
ORDER BY 
hoofdVolgorde,
TijdelijkeRapportage.hoofdsectorVolgorde,
TijdelijkeRapportage.beleggingscategorieVolgorde,
TijdelijkeRapportage.beleggingssectorVolgorde,
TijdelijkeRapportage.fondsOmschrijving";
		$DB->SQL($query); 
		$DB->Query();
    $somVelden=array('actuelePortefeuilleWaardeEuro','beginPortefeuilleWaardeEuro','fondsOngerealiseerd','fondsValutaresultaat','aandeel','dividend','dividendCorrected');
		while($data = $DB->nextRecord())
    {
      $tmp=$this->getDividend($data['fonds']);
      $data['dividend']=$tmp['totaal'];
      $data['dividendCorrected']=$tmp['corrected'];
      
      if($data['type']=='rekening')
        $data['fondsOmschrijving']=$data['rekening'];
      $categorieOmschrijvingen[$data['beleggingscategorie']]=$data['beleggingscategorieOmschrijving'];
      $sectorOmschrijvingen[$data['beleggingssector']]=$data['beleggingssectorOmschrijving'];
      $hoofdcategorieOmschrijvingen[$data['hoofdcategorie']]=$data['hoofdcategorieOmschrijving'];
      
     
      if($data['hoofdVolgorde'] == 1)
      {
        $data['fondsOngerealiseerd'] = ($data['actuelePortefeuilleWaardeInValuta'] - $data['beginPortefeuilleWaardeInValuta']) * $data['actueleValuta'];
				$data['fondsValutaresultaat'] = $data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro'] - $data['fondsOngerealiseerd'] ;
        $procentResultaat = (($data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro']) / ($data['beginPortefeuilleWaardeEuro'] /100));
      }
      if($data['hoofdVolgorde'] > 1)
      {
        $data['historischeWaarde']=0;
        $data['beginPortefeuilleWaardeInValuta']=0;
        $data['totaalAantal']=0;
        $data['actueleFonds']=0;
        $data['beginPortefeuilleWaardeEuro']=0;
        if($data['hoofdVolgorde'] == 2)
        {
          $data['beleggingscategorie']='Opgelopen rente';   
          $data['beleggingssector']='Opgelopen rente';
        }
      }
      $aandeel=$data['actuelePortefeuilleWaardeEuro']/$totaalWaarde;
      $data['aandeel']=$aandeel*100;
  
      if($data['hoofdcategorie']=='')
        $data['hoofdcategorie']='geen H-cat';
      if($data['beleggingscategorie']=='')
        $data['beleggingscategorie']='geen cat';   
      if($data['beleggingssector']=='')
      {
        if($data['beleggingscategorie']=='Liquiditeiten')
          $data['beleggingssector']='Liquiditeiten'; 
        else
          $data['beleggingssector']='geen sec'; 
      }
      if($data['koersLeeftijd'] > $maxDagenOud && $data['actueleFonds'] <> 0)
			  $data['markering']="*";
		  else
			  $data['markering']="";        
          
      $waarden[$data['type']][$data['hoofdcategorie']][$data['beleggingscategorie']][$data['beleggingssector']][]=$data;
      
      
      foreach($somVelden as $veld)
      {
        $sectorTotalen[$data['hoofdcategorie']][$data['beleggingscategorie']][$data['beleggingssector']][$veld]+=$data[$veld];
        $categorieTotalen[$data['hoofdcategorie']][$data['beleggingscategorie']][$veld]+=$data[$veld];
        $hcatTotalen[$data['hoofdcategorie']][$veld]+=$data[$veld];
        $typeTotalen[$data['type']][$veld]+=$data[$veld];
        $totaal[$veld]+=$data[$veld];
      }
 
    }
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

  $sectorOmschrijvingen['Opgelopen rente']='Opgelopen rente';
  $sectorOmschrijvingen['Liquiditeiten']='Liquiditeiten';
  $regelsPerType=array();
  foreach($waarden as $type=>$hcatData)
  {
 //begin tellen
    $regelsPerType[$type]=0;
    foreach($hcatData as $hcat=>$catData)
    {
      if($type=='fondsen')
        $regelsPerType[$type]++; //$this->printKop($hoofdcategorieOmschrijvingen[$hcat],'b');
      foreach($catData as $cat=>$secData)
      {
        foreach($secData as $sec=>$fondsData)
        {
          $regelsPerType[$type]++; //$this->printKop($sectorOmschrijvingen[$sec],'u');
          foreach($fondsData as $fonds)
          {
            if($fonds['type'] <> 'rente')
              $regelsPerType[$type]++; //$this->printRow($fonds);
          }
          $regelsPerType[$type]++; //$this->printRow($sectorTotalen[$hcat][$cat][$sec],'totaal','',"Totaal ".$sectorOmschrijvingen[$sec]);
          $regelsPerType[$type]++; //$this->pdf->ln();
        }
        //$this->printRow($categorieTotalen[$hcat][$cat],'totaal','b',"Totaal $cat");
      }
      //$this->printRow($hcatTotalen[$hcat],'totaal','bi',"Totaal $hcat");
    }
    if($type=='fondsen')
    {
      $regelsPerType[$type]++; //$this->printRow($typeTotalen[$type],'totaal','b',"Totaal $type");
      $regelsPerType[$type]++; //$this->pdf->ln();
    }
  }
  
  $totaalRegels=0;
  $regelsPerPagina=floor(($this->pdf->PageBreakTrigger-$paginaBeginY)/$this->pdf->rowHeight);
  if(isset($regelsPerType['rente']))
  {
    $regelsPerType['rekening']+=$regelsPerType['rente'];
    unset($regelsPerType['rente']);
  }
  foreach($regelsPerType as $type=>$aantal)
  {
    $totaalRegels+=$aantal;
    $maximumAantalPaginas+=ceil($aantal/$regelsPerPagina);
  }
 
  $minimumAantalPaginas=ceil($totaalRegels/$regelsPerPagina);
  if($minimumAantalPaginas==$maximumAantalPaginas)
    $rekeningNieuwepagina=true;
  else
    $rekeningNieuwepagina=false;  
  //$huidigePagina=count($this->pdf->pages); 

 //eind tellen
 //listarray($waarden);
  foreach($waarden as $type=>$hcatData)
  {
    if($type=='rente' && $rekeningNieuwepagina==true )
    {
       $this->pdf->AddPage(); 
       }
    foreach($hcatData as $hcat=>$catData)
    {
      if($type=='fondsen')
      {
        $this->printKop($hoofdcategorieOmschrijvingen[$hcat],'b');
        $this->pdf->Ln();
      }  
      foreach($catData as $cat=>$secData)
      {
        //$this->printKop($cat,'b');
        foreach($secData as $sec=>$fondsData)
        {
          $this->printKop($sectorOmschrijvingen[$sec],'bu');
          foreach($fondsData as $fonds)
          {
            if($fonds['type'] <> 'rente')
              $this->printRow($fonds);
          }
          $this->printRow($sectorTotalen[$hcat][$cat][$sec],'totaal','b','');
          $this->pdf->ln(4);
        }
        //$this->printRow($categorieTotalen[$hcat][$cat],'totaal','b',"Totaal $cat");
      }
      //$this->printRow($hcatTotalen[$hcat],'totaal','bi',"Totaal $hcat");
    }
    if($type=='fondsen')
    {
      $this->printRow($typeTotalen[$type],'totaal','b',"Totaal $type");
      $this->pdf->ln();
    }
  }
 // listarray($totaal);
  unset($totaal['beginPortefeuilleWaardeEuro']);
  $this->printRow($totaal,'totaal','b','Totaal');

                            
	}


  function printRow($data,$type='',$style='',$omschrijving='')
  {
    switch($style)
		{
			case "b" :
        $spaties='';
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'b';
       	$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
			break;
			case "bi" :
        $spaties='';
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'bi';
        $this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
			break;
			case "u" :
        $spaties='';
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'u';
        $this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
			break;
			default :
        $spaties='  ';
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = '';
        $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor[r],$this->pdf->rapport_default_fontcolor[g],$this->pdf->rapport_default_fontcolor[b]);
			break;
		}
		$this->pdf->SetFont($font,$fonttype,$fontsize);
    
    if($type=='totaal')
    {
       $this->pdf->CellBorders=array('','','','','','','','','TS','TS','TS','','TS');
       $this->pdf->row(array($spaties.$omschrijving,'','','','','','','',$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),
                              $this->formatGetal($data['beginPortefeuilleWaardeEuro'],0),
                              $this->formatGetal($data['fondsOngerealiseerd']+$data['fondsValutaresultaat']+$data['dividend'],0),
                              '',
                              $this->formatGetal($data['aandeel'],1,true)
                              )); 
       unset($this->pdf->CellBorders);
    }
    else
    {
       $omschrijvingWidth=$this->pdf->GetStringWidth($spaties.$data['fondsOmschrijving']);
       if($omschrijvingWidth > 63 )
       {
          $data['fondsOmschrijving']=substr($data['fondsOmschrijving'],0,35).'...';
       }
       
      if($data['type'] <> 'rekening')
      {
        $rendementInValuta=$this->formatGetal((($data['actueleFonds']/$data['beginwaardeLopendeJaar'])-1)*100,2,true);
        $rendementInValutaGetal=(($data['actueleFonds']/$data['beginwaardeLopendeJaar'])-1)*100;
        $rendementInEur=$this->formatGetal(((($data['actuelePortefeuilleWaardeEuro']+$data['dividendCorrected'])/$data['beginPortefeuilleWaardeEuro'])-1)*100,2,true);
        $rendementInEurGetal=((($data['actuelePortefeuilleWaardeEuro']+$data['dividendCorrected'])/$data['beginPortefeuilleWaardeEuro'])-1)*100;

      }
      else
      {
        $rendementInValuta='';
        if($data['IBANnr'])
          $data['fondsOmschrijving']=$data['IBANnr'];
        else
        {
          $rekeningnummer = preg_replace("/[^0-9]/","", $data['fondsOmschrijving']);
          if($rekeningnummer <> '')
          {
            $tmp='';
            for($i=0;$i<strlen($rekeningnummer);$i++)
            {
              $tmp.=$rekeningnummer[$i];
              if($i%2 && strlen($tmp) < 9)
                $tmp.='.';
            }
            $data['fondsOmschrijving']=$tmp;
          }
        }
      }
      
      $stringWidth=$this->pdf->GetStringWidth($spaties.$data['fondsOmschrijving']);
      //echo $this->pdf->widths[0]." ".$data['fondsOmschrijving']." $stringWidth <br>\n";
      if($stringWidth >= $this->pdf->widths[0]-2)
      {
        $omschrijvingRuimte=$this->pdf->widths[0]-$this->pdf->GetStringWidth($spaties.'...')-2;
        for($i=0; $i<strlen($data['fondsOmschrijving']); $i++) 
        {
          $char=$data['fondsOmschrijving'][$i];
          $omschrijvingRuimte-=$this->pdf->GetStringWidth($char);
          if($omschrijvingRuimte<0)
          {
            $newString=substr($data['fondsOmschrijving'],0,$i);
            break;
          }
        } 
        $data['fondsOmschrijving']=$newString.'...';
        
      }
      $this->pdf->excelData[]=array($data['fondsOmschrijving'],
                              round($data['totaalAantal'],4),
                              $data['valuta'],
                              round($data['actueleFonds'],2),
                              $data['markering'],
                              round($data['beginwaardeLopendeJaar'],2),
                              round($rendementInValutaGetal,2),
                              round($data['actuelePortefeuilleWaardeEuro'],2),
                              round($data['beginPortefeuilleWaardeEuro'],2),
                              round($data['fondsOngerealiseerd']+$data['fondsValutaresultaat']+$data['dividend'],2),
                              round($rendementInEurGetal,2),
                              round($data['aandeel'],1)
                              );    
      $this->pdf->row(array($spaties.$data['fondsOmschrijving'],
                              $this->formatAantal($data['totaalAantal'],0),
                              $data['valuta'],
                              $this->formatGetal($data['actueleFonds'],2),
                              $data['markering'],
                              $this->formatGetal($data['beginwaardeLopendeJaar'],2),
                              $rendementInValuta,
                              '',
                              $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),
                              $this->formatGetal($data['beginPortefeuilleWaardeEuro'],0),
                              $this->formatGetal($data['fondsOngerealiseerd']+$data['fondsValutaresultaat']+$data['dividend'],0),
                              $rendementInEur,
                              $this->formatGetal($data['aandeel'],1,true)
                              ));
    }
  }
  
 function getDividend($fonds)
  {
    global $__appvar;
    
    if($fonds=='')
      return 0;
      
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
     Rekeningmutaties.Boekdatum >= '".	$this->rapportageDatumVanaf."' AND 
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
    }

    
    return array('totaal'=>$totaal,'corrected'=>$totaalCorrected);
  }
}
?>
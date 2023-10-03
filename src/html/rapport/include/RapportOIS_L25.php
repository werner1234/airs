<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIS_L25
{
	function RapportOIS_L25($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

	function formatGetal($waarde, $dec,$leegPM=false)
	{
	  if($leegPM==true && round($waarde,2)==0.00)
    {
      if(!isset($this->voettekstToegevoegd[$this->pdf->page]))
      {
        $x=$this->pdf->getX();
        $y=$this->pdf->getY();
        $this->voettekstToegevoegd[$this->pdf->page]=true;
        $this->pdf->AutoPageBreak=false;
        $this->pdf->setXY($this->pdf->w-125,$this->pdf->h-10);
        
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
        $this->pdf->MultiCell(100,4,'p.m.: er kan geen eenduidig waardebepalingen worden getoond',0,'R');
        $this->pdf->AutoPageBreak=true;
        $this->pdf->setXY($x,$y);
         $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      }
      return "p.m.";
    }
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
	  //return number_format($waarde,$dec,",",".");
  }

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	  //echo "$waarde, $dec, $VierDecimalenZonderNullen <br>\n";
	  if ($VierDecimalenZonderNullen)
	  {
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
	     if($newDec > 4)
         $newDec=4;
	     
	     return number_format($waarde,$newDec,",",".");
	   }
	  else
	   return number_format($waarde,$dec,",",".");
	  }
	  else
	   return number_format($waarde,$dec,",",".");
	}



	function printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde,$resultaat=true)
	{
          $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
          $this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
          if(!isset($categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro']))
          {
            $this->pdf->CellBorders = array('','','','','','','','','','','','SUB','SUB');
            $this->pdf->row(array("Subtotaal ".$lastCategorieOmschrijving,'','','','','','','','','','',
            $this->formatGetal($this->aandeel*$categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
            $this->formatGetal($this->aandeel*($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']/ ($this->aandeel*$totaalWaarde/100)),$this->pdf->rapport_VOLK_decimaal_proc)));
          }
          else
          {
            $this->pdf->CellBorders = array('','','','','SUB','','SUB','SUB','SUB','','','SUB','SUB');
            if($resultaat)
            {
              $resultaatProcent=$this->formatGetal($this->aandeel*(($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro'] - $categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'] + $categorieTotaal[$lastCategorieOmschrijving]['dividendCorrected']) / ($this->aandeel* $categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'] /100)),$this->pdf->rapport_VOLK_decimaal_proc);
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
                            $this->formatGetal($this->aandeel*$categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),'',$resultaatWaarde,$resultaatDividend,$resultaatProcent,'','',
            $this->formatGetal($this->aandeel*$categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
            $this->formatGetal($this->aandeel*($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']/ ($this->aandeel*$totaalWaarde/100)),$this->pdf->rapport_VOLK_decimaal_proc)
            ));
          }
          $this->pdf->CellBorders = array();
          $this->pdf->ln();
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0);
	}

	function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF = 0, $grandtotaal=false, $totaalG = 0, $totaalH = 0 )
	{
		return $totaalB;
	}

	function printKop($title, $type='',$ln=false)
	{
		if($ln)
	    $this->pdf->ln();
		//echo $title." ".$this->pdf->getY()."<br>\n";
		if($this->pdf->getY()>174)
		  $this->pdf->addPage();
    $this->pdf->SetFont($this->pdf->rapport_font,$type,$this->pdf->rapport_fontsize);
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
     $totaalCorrected=0;
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
  //  listarray($aantal);
//    if($fonds=='Delta Lloyd VAR 12-42')
//    {
//     echo $fonds." ".$rente[$this->rapportageDatum].' - '.$rente[$this->rapportageDatumVanaf]."<br>\n"; 
//      echo "$query <br>\n";// exit;
//    }  
    
    return array('totaal'=>$totaal,'corrected'=>$totaalCorrected);
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
  
	function writeRapport()
	{
		global $__appvar;
		$aandelenCategorie='AU-A IndAand';
   // $this->pdf->rapport_fontsize-=1;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
		if($this->aandeel <> 1)
      $this->pdf->excelData[]=array($this->pdf->rapport_titel);
    $this->pdf->excelData[]=array('Hoofdcategorie','Categorie','Sector',"Effect",'Aantal','Valuta','Begin Koers','Begin Waarde','Koersresultaat','Rente/Dividend','Resultaat %','Koers','Waarde','Aandeel vermogen');

		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

		// haal totaalwaarde op om % te berekenen
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
				" TijdelijkeRapportage.Valuta, 
        TijdelijkeRapportage.fondsEenheid,".
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
				   Valutas.Valutateken ,
           TijdelijkeRapportage.beleggingssector,
TijdelijkeRapportage.beleggingssectorOmschrijving,
           if(TijdelijkeRapportage.beleggingscategorie='".$aandelenCategorie."',TijdelijkeRapportage.beleggingssectorVolgorde,200) as sectorVolgorde".
				" FROM TijdelijkeRapportage
LEFT Join Valutas ON TijdelijkeRapportage.valuta = Valutas.Valuta
WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" ORDER BY hoofdcategorieAfdrukVolgorde,
TijdelijkeRapportage.Hoofdcategorie,
categorieAfdrukVolgorde,
TijdelijkeRapportage.beleggingscategorie ,sectorVolgorde,
TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
//" TijdelijkeRapportage.type =  'fondsen' AND ".
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		$aandelenOnschrijving='Aandelen';
		while($data = $DB->NextRecord())
		{
			if($data['beleggingscategorie']==$aandelenCategorie)
				$aandelenOnschrijving=$data['categorieOmschrijving'];
		  //categorietotalen
		  if($data['type']=='fondsen' && $data['beleggingscategorie'] <> 'Liquiditeiten')
		    $fondsData[]=$data;
		  elseif($data['type']=='rente')
		    $renteData[]=$data;
		  else
		    $liquiditeitenData[]=$data;
		}
    $lastSectorOmschrijving='';
    $lastCategorieOmschrijving='';
    $lastHoofdcategorieOmschrijving='';
    $sectorTotaal=array();
    $categorieTotaal=array();
    $hoofdcategorieTotaal=array();
		foreach ($fondsData as $data)
		{
		  $dividend=$this->getDividend($data['fonds']);
    
		  if($lastCategorieOmschrijving==$aandelenOnschrijving && $lastSectorOmschrijving=='Overige11')
      {
        echo "$lastCategorieOmschrijving==$aandelenOnschrijving && " . $data['beleggingssectorOmschrijving'] . " != $lastSectorOmschrijving && $lastSectorOmschrijving !='' <br>\n";
        listarray($sectorTotaal[$lastSectorOmschrijving]);
      }
      if($lastCategorieOmschrijving==$aandelenOnschrijving && ($data['beleggingssectorOmschrijving'] != $lastSectorOmschrijving || $data['categorieOmschrijving'] != $lastCategorieOmschrijving) && $lastSectorOmschrijving !='' && is_array($sectorTotaal[$lastSectorOmschrijving]))
      {
        $this->printSubTotaal($lastSectorOmschrijving, $sectorTotaal, $totaalWaarde);
      }
	    if($data['categorieOmschrijving'] != $lastCategorieOmschrijving && $lastCategorieOmschrijving !='' && is_array($categorieTotaal[$lastCategorieOmschrijving]))
          $this->printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde);
	    
	    if($data['hoofdcategorieOmschrijving'] != $lastHoofdcategorieOmschrijving)
        $this->printKop($data['hoofdcategorieOmschrijving'],'BI',true);
      if($data['categorieOmschrijving'] != $lastCategorieOmschrijving)
        $this->printKop($data['categorieOmschrijving'],'B',false);

      if($data['beleggingscategorie'] == $aandelenCategorie && $data['beleggingssectorOmschrijving'] <> $lastSectorOmschrijving)
      {
        if($lastSectorOmschrijving=='')
          $this->pdf->Ln();
        $this->printKop($data['beleggingssectorOmschrijving'],'B',false);
      }
			$resultaat = $data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro'];
   
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
      $dividendTxt='';
 			if($resultaat <> 0)
				$resultaattxt = $this->formatGetal($this->aandeel*$resultaat,$this->pdf->rapport_VOLK_decimaal);
      if($dividend['totaal'] <> 0)
        $dividendTxt = $this->formatGetal($this->aandeel*$dividend['totaal'],$this->pdf->rapport_VOLK_decimaal);
        
      if($data['fondsEenheid']=='0.01')
        $percentageTeken=' %';
      else
        $percentageTeken='';
      
      $omschrijving=$this->omschrijvingAfkorten("  ".$data['fondsOmschrijving'],$this->pdf->widths[0]);
      
      // echo $data['fondsOmschrijving']." ".$data['fondsEenheid']."<br>\n";
			$this->pdf->row(array($omschrijving,
				                $this->formatAantal($this->aandeel*$data['totaalAantal'],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
												$data['Valutateken'],
                        $this->formatGetal($data['beginwaardeLopendeJaar'],2).$percentageTeken,
                        $this->formatGetal($this->aandeel*$data['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
                        "",$resultaattxt,$dividendTxt,$procentResultaattxt,'',
                        $this->formatGetal($data['actueleFonds'],2,true).$percentageTeken,
												$this->formatGetal($this->aandeel*$data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal,true),
												$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)
												));
      
      $this->pdf->excelData[]=array($data['hoofdcategorieOmschrijving'],$data['categorieOmschrijving'],$data['beleggingssectorOmschrijving'],
        $data['fondsOmschrijving'],
        round($this->aandeel*$data['totaalAantal'],6),
        $data['Valutateken'],
        round($data['beginwaardeLopendeJaar'],2),
        round($this->aandeel*$data['beginPortefeuilleWaardeEuro'],2),
        round($this->aandeel*$resultaat,2),
        round($this->aandeel*$dividend['totaal'],2),
        round($procentResultaat,1),
        round($data['actueleFonds'],2),
        round($this->aandeel*$data['actuelePortefeuilleWaardeEuro'],2),
        round($percentageVanTotaal,2));
      
		$categorieTotaal[$data['categorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
		$categorieTotaal[$data['categorieOmschrijving']]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];
    $categorieTotaal[$data['categorieOmschrijving']]['dividend'] +=$dividend['totaal'];
    $categorieTotaal[$data['categorieOmschrijving']]['dividendCorrected'] +=$dividend['corrected'];
		
		$hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
		$hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];
    $hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['dividend'] +=$dividend['totaal'];
    $hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['dividendCorrected'] +=$dividend['corrected'];
    $sectorTotaal[$data['beleggingssectorOmschrijving']]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
		$sectorTotaal[$data['beleggingssectorOmschrijving']]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];
    $sectorTotaal[$data['beleggingssectorOmschrijving']]['dividend'] +=$dividend['totaal'];
    $sectorTotaal[$data['beleggingssectorOmschrijving']]['dividendCorrected'] +=$dividend['corrected'];
    
    $lastHoofdcategorieOmschrijving=$data['hoofdcategorieOmschrijving'];
    $lastCategorieOmschrijving=$data['categorieOmschrijving'];
    $lastSectorOmschrijving=$data['beleggingssectorOmschrijving'];
		}
		$this->printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde);

/*
		// selecteer rente
		$query = "SELECT Valutas.Valutateken,TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.fondsOmschrijving, ".
		" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
		" TijdelijkeRapportage.rentedatum, ".
		" TijdelijkeRapportage.renteperiode, ".
		" TijdelijkeRapportage.beginPortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersBegin. " as beginPortefeuilleWaardeEuro, ".
		" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro FROM ".
		" TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta) ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.type = 'rente'  AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		"  ".
		" ORDER BY Valutas.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
*/

		//if($DB->records() > 0)
		if(count($renteData) > 0)
		{
		  $this->printKop(vertaalTekst("Opgelopen rente en coupondatum",$this->pdf->rapport_taal), "B");
			$totaalRenteInValuta = 0 ;
			//while($data = $DB->NextRecord())
			foreach ($renteData as $data)
			{
			  	$rentePeriodetxt = "  ".date("d-m",db2jul($data['rentedatum']));
					if($data['renteperiode'] <> 12 && $data['renteperiode'] <> 0)
						$rentePeriodetxt .= " / ".$data['renteperiode'];

					$percentageVanTotaal = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
					$this->pdf->Cell($this->pdf->widths[0],4,"  ".$data['fondsOmschrijving'].' '.$rentePeriodetxt, 0,0, "L");
					$this->pdf->setX($this->pdf->marge);
					$this->pdf->row(array('','',
					              $data['Valutateken'],'','','','','','','','',
												$this->formatGetal($this->aandeel*$data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)));
					$categorieTotaal["Opgelopen Rente"]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
			}
			$this->printSubTotaal("Opgelopen Rente",$categorieTotaal,$totaalWaarde);
		}


/*
		// Liquiditeiten
		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.rekening as zoekRekening, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro, ".
			" (SELECT actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage WHERE portefeuille = '".$this->portefeuille."' AND rapportageDatum = '".$this->rapportageDatumVanaf."' AND rekening = zoekRekening AND type='rekening'  LIMIT 1)  / ".$this->pdf->ValutaKoersBegin. " as beginPortefeuilleWaardeEuro,".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,
			Rekeningen.Deposito, Rekeningen.Termijnrekening, Rekeningen.Memoriaal".
			" FROM TijdelijkeRapportage JOIN Rekeningen on Rekeningen.rekening = TijdelijkeRapportage.rekening  AND Rekeningen.Portefeuille = TijdelijkeRapportage.portefeuille
			WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekening'  ".
			" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.valuta asc";
		debugSpecial($query,__FILE__,__LINE__);


		$DB1->SQL($query);
		$DB1->Query();
*/
		//if($DB1->records() > 0)
		if(count($liquiditeitenData) > 0)
		{
			$totaalLiquiditeitenInValuta = 0;
			$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),"B");
			/*
			while($data = $DB1->NextRecord())
			{
			  $liqiteitenBuffer[] = $data;
			}
			foreach($liqiteitenBuffer as $data)
      */
					$DB1 = new DB();
			foreach($liquiditeitenData as $data)
			{

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
												$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc),'','',
												

												));
        
        $this->pdf->excelData[]=array($data['hoofdcategorieOmschrijving'],$data['categorieOmschrijving'],$data['beleggingssectorOmschrijving'],
          $data['fondsOmschrijving'],
          '',
          $data['Valutateken'],
          '',
          round($this->aandeel*$data['beginPortefeuilleWaardeEuro'],2),
          '','','','',
          round($this->aandeel*$data['actuelePortefeuilleWaardeEuro'],2),
          round($percentageVanTotaal,2));
        
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

		$this->pdf->CellBorders = array('','','','','','','','','','','','SUB','SUB');
		$this->pdf->row(array("Totale actuele waarde portefeuille",'','','','','','','','','','',$this->formatGetal($this->aandeel*$totaalWaarde,$this->pdf->rapport_VOLK_decimaal),
		$this->formatGetal(($actueleWaardePortefeuille/$totaalWaarde*100),$this->pdf->rapport_VOLK_decimaal_proc)));
    $this->pdf->CellBorders = array();
		$this->pdf->ln();
    
    $this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf,$omkeren=true,$kop='Valuta');
    if($this->aandeel == 1)
      printAEXVergelijking($this->pdf,'',$this->rapportageDatumVanaf,$this->rapportageDatum);
    

		if($this->pdf->rapport_VOLK_rendement == 1 && $this->aandeel == 1 )
    {
      $this->pdf->ln();
      $this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
    }
		elseif ($this->pdf->rapport_VOLK_rendement == 2 && $this->aandeel == 1 )
		  $this->pdf->printSamenstellingResultaat($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf) ;
    /*
    
        if($this->pdf->rapport_VOLK_valutaoverzicht == 1)
        {
          $this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
        }
        elseif($this->pdf->rapport_VOLK_valutaoverzicht == 2)
        {
          $this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf,true);
        }
        
        if($this->pdf->portefeuilledata[AEXVergelijking] > 0 ) //|| $this->pdf->rapport_layout == 8 voor L8 de index er weer uitgehaald.
        {
          if(!$this->pdf->rapport_VOLK_geenIndex)
            printAEXVergelijking($this->pdf,$this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);
        }
    */
    //$this->pdf->rapport_fontsize+=1;
	}
}
?>
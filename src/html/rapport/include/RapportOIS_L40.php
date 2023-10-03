<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportHuidigeSamenstellingLayout.php");

class RapportOIS_L40
{
	function RapportOIS_L40($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum, $valuta = 'EUR')
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "HSE";
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Huidige samenstelling effectenportefeuille";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->verdeling='beleggingssector';
    $this->pdf->tweedeDeel=false;
    $this->pdf->underlinePercentage=0.8;
    $this->modelDataPrinted=array();

	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec,$extraTeken='')
	{
	  if(round($waarde,2) <> 0)
	  	return number_format($waarde,$dec,",",".").$extraTeken;
	}

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	  if(round($waarde,2) == 0)
      return '';
	  elseif ($VierDecimalenZonderNullen)
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

	function printTotaal($title, $type,$fontStyle='')
	{
	  if($type=='hoofdcategorie')
	  {
	    $space='';
      $this->pdf->SetFillColor(0,78,58); 
      $this->pdf->SetTextColor(255,255,255);
      $this->pdf->CellBorders=$this->subtotaalCatBorders;
      $this->pdf->fillCell=array(1,1,1,1,1,1,1,1,1,1,1);
      $this->pdf->row(array(""," ",' ','','','','','','','',''));
      $this->pdf->setY($this->pdf->getY()-4);
      unset($this->pdf->fillCell);
      $extraln=1;
	  }
	  if($type=='beleggingscategorie')
	  {
	    $space='  ';
      $this->pdf->CellBorders=$this->subtotaalCatBorders;
	  }
	  if($type=='verdeling')
	  {
	    $space='    ';
      if($this->pdf->GetY() < 50 || $this->pdf->GetY() > 190)
        $this->pdf->CellBorders=$this->subtotaalCatBorders;
      else  
        $this->pdf->CellBorders=$this->subtotaalVerBorders;
	  }
    if($type=='alles')
	  {
	    $space='';
      $this->pdf->SetFillColor(0,78,58); 
      $this->pdf->SetTextColor(255,255,255);
      $this->pdf->CellBorders=$this->subtotaalCatBorders;
      $this->pdf->fillCell=array(1,1,1,1,1,1,1,1,1,1,1);
      $this->pdf->row(array(""," ",' ','','','','','','','',''));
      $this->pdf->setY($this->pdf->getY()-4);
      unset($this->pdf->fillCell);
      $extraln=1;
      $this->totalen[$type]['beginPortefeuilleWaardeEuro']=0;
      $this->totalen[$type]['eurResultaat']=0;
      $this->totalen[$type]['procentResultaat']=0;
      $title="actuele vermogen";
	  }
    if($title=='Liquiditeiten')
      $this->totalen[$type]['eurResultaat']=0;


    if($this->pdf->modelLayout==true)
    {
      if($title=='missingModel')
      {
        $tmpItems=array();
        foreach($this->modelData as $title=>$model)
        {
          if(!in_array($title,$this->modelDataPrinted))
            $tmpItems[$title]=$model;
        }
      }
      else
        $tmpItems=array($title=>$this->modelData[$title]);

  
      foreach($tmpItems as $title=>$modelData)
      {
        $this->pdf->SetFont($this->pdf->rapport_font,$fontStyle,$this->pdf->rapport_fontsize);
  	    $this->pdf->SetX($this->pdf->marge);
		    $this->pdf->Cell(150,4, $space.'Totaal '.$title, 0, "L");
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	  	  $this->pdf->setX($this->pdf->marge);
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
                         
      //listarray($this->modelData); 
        if(count($modelData)>1)
        { 
   	    	$this->pdf->row(array("","",$this->formatGetal($this->totalen[$type]['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_decimaal),
                          $this->formatGetal($modelData['percentageModel'],$this->pdf->rapport_decimaal),
                          $this->formatGetal($modelData['percentagePortefeuilleExclNorm'],$this->pdf->rapport_decimaal),
                          $this->formatGetal($modelData['afwijkingExclNorm'],$this->pdf->rapport_decimaal),
                          $this->formatGetal($modelData['mutatieExclNorm'],$this->pdf->rapport_decimaal),
                          $this->formatGetal($modelData['percentagePortefeuilleNorm'],$this->pdf->rapport_decimaal),
                          $this->formatGetal($modelData['afwijkingNorm'],$this->pdf->rapport_decimaal),
                          $this->formatGetal($modelData['mutatieNorm'],$this->pdf->rapport_decimaal),
                          '','',''));
          $this->modelDataPrinted[]=$title;                
        }                    
        else
        	$this->pdf->row(array("","",$this->formatGetal($this->totalen[$type]['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_decimaal),
             $this->formatGetal($this->modelTotaal[$title]['percentageModel'],$this->pdf->rapport_decimaal),
             $this->formatGetal($this->modelTotaal[$title]['percentagePortefeuille'],$this->pdf->rapport_decimaal)
             ,'','','','','',''));
      }
         
    }
    else
    {
      
      $this->pdf->SetFont($this->pdf->rapport_font,$fontStyle,$this->pdf->rapport_fontsize);
  	  $this->pdf->SetX($this->pdf->marge);
		  $this->pdf->Cell(150,4, $space.'Totaal '.$title, 0, "L");
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		  $this->pdf->setX($this->pdf->marge);
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
	  	$this->pdf->row(array("","",'','','',	//$this->formatGetal($this->totalen[$type]['beginPortefeuilleWaardeInValuta'],$this->pdf->rapport_decimaal),
													$this->formatGetal($this->totalen[$type]['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_decimaal),
                          '',
													'',//$this->formatGetal($this->totalen[$type]['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_decimaal),
													$this->formatGetal($this->totalen[$type]['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_decimaal),
                          $this->formatGetal($this->totalen[$type]['eurResultaat'],$this->pdf->rapport_decimaal),
                          $this->formatGetal($this->totalen[$type]['procentResultaat'],$this->pdf->rapport_decimaal,"%")));
    }
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders); 
    if($extraln==1) 
      $this->pdf->Ln();                  
		$this->totalen[$type]=array();
    $this->totalenRente[$type]=array();
	}

	function printKop($title, $type, $fontStyle="")
	{
	  $fill=0;
	  if($type=='hoofdcategorie')
	  {
	    $space='';
      
      if($this->pdf->GetY() > 185)
        $this->pdf->addPage();
      
      $this->pdf->SetFillColor(0,78,58); 
      $this->pdf->SetTextColor(255,255,255);
      $this->pdf->CellBorders=$this->subtotaalCatBorders;
      $this->pdf->fillCell=array(1,1,1,1,1,1,1,1,1,1,1);
      $this->pdf->row(array(""," ",' ','','','','','','','',''));
      $this->pdf->setY($this->pdf->getY()-4);
      unset($this->pdf->fillCell);
	  }
	  if($type=='beleggingscategorie')
	  {
	    $space='  ';
      if($this->pdf->GetY() > 190)
        $this->pdf->addPage();
      $this->pdf->SetFillColor(200,200,200);
      $this->pdf->CellBorders=$this->subtotaalCatBorders;
      $this->pdf->fillCell=array(1,1,1,1,1,1,1,1,1,1,1);
      $this->pdf->row(array(""," ",' ','','','','','','','',''));
      $this->pdf->setY($this->pdf->getY()-4);
      unset($this->pdf->fillCell);
	  }
	  if($type=='verdeling')
	  {
	   	//   echo $title." ".$this->pdf->GetY()."<br>\n";
	    $space='    ';
      if($this->pdf->GetY() < 50 || $this->pdf->GetY() > 190 )
        $this->pdf->CellBorders=$this->kopVerBorders;
      else
        $this->pdf->CellBorders=$this->subtotaalFondsBorders;
      $this->pdf->row(array("","",'','','','','','','','',''));
      $this->pdf->setY($this->pdf->getY()-4);
	  }
		$this->pdf->SetFont($this->pdf->rapport_font,$fontStyle,$this->pdf->rapport_fontsize);
		
		$this->pdf->SetX($this->pdf->marge);
    $width=array_sum($this->pdf->widthB);
		$this->pdf->MultiCell($width,4, $space.$title, 0, "L",$fill);
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

	}

	function writeRapport()
	{

	  # LOOP over H-CAT/CAT/(regio of sector)
	  # eerst fonds dan optie tonen.
	  # rapportagedatum +365 dagen is kortlopende
	  # P 229002
		global $__appvar;
    
		// rapport settings
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
    $markering='';
    
    
    $this->pdf->excelData[]=array("Hoofdcategorie","Beleggingscategorie",ucfirst($this->verdeling),"Omschrijving",'Valuta','Aantal','Koers per '.date('d-m-Y',$this->pdf->rapport_datumvanaf).' in valuta',
      'Waarde per '.date('d-m-Y',$this->pdf->rapport_datumvanaf).' in euro in valuta','Actuele Koers in valuta','Actuele Waarde in euro','Resultaat in euro *','Resultaat in %');
    

	  $query="SELECT Vermogensbeheerders.VerouderdeKoersDagen
    FROM Vermogensbeheerders Inner Join Portefeuilles ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
    WHERE portefeuille = '".$this->portefeuille."' ";
		$DB->SQL($query);
		$DB->Query();
		$dagen = $DB->nextRecord();
    $maxDagenOud=$dagen['VerouderdeKoersDagen'];

    if($this->pdf->modelLayout==true)
      $this->pdf->widthB = array(10,70,25,20,30,25,25,25,25,25,3);
    else
	    $this->pdf->widthB = array(10,65,20,20,30,30,15,20,30,20,20);
		$this->pdf->alignB = array('L','L','R','R','R','R','R','R','R','R','R');
    
    $this->subtotaalCatBorders=array(array('T','L','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U','R'));
    $this->subtotaalVerBorders=array(array('L','U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U','R'));
    $this->kopVerBorders=array(array('L','T'),array('T'),array('T'),array('T'),array('T'),array('T'),array('T'),array('T'),array('T'),array('T'),array('T','R'));
    $this->subtotaalFondsBorders=array(array('L'),'','','','','','','','','',array('R'));


		$this->pdf->AddPage();
    
  
		if($this->pdf->modelLayout)
    {
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $uitsluitingenGRO = array();
      foreach ($this->modelUitsluitingen['portefeuilleRegels'] as $index => $row)
      {
        if ($index > 0 && $row[0] <> '')
        {
          $uitsluitingenGRO[] = $row[0];
        }
      }
      if(count($uitsluitingenGRO)>0)
      {
        $this->pdf->MultiCell(200, 4, 'Uitgesloten van modelcontrole: ' . implode(', ', $uitsluitingenGRO), 0, "L", 0);
        $this->pdf->ln();
      }
      //listarray($uitsluitingen);
    }
		// haal totaalwaarde op om % te berekenen
		$DB = new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."'"
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		$actueleWaardePortefeuille = 0;

    if($categorien['Valuta'] == $this->pdf->rapportageValuta)
		  $beginQuery = 'beginwaardeValutaLopendeJaar';
		else
		  $beginQuery = $this->pdf->ValutaKoersBegin;

		$DB2 = new DB();

		$verdeling=$this->verdeling;
  	$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
    
    if($verdeling=='Fonds')
     $verdelingVolgorde="TijdelijkeRapportage.".$verdeling."Omschrijving"; 
    else
     $verdelingVolgorde="TijdelijkeRapportage.".$verdeling."Volgorde"; 

			$query = "SELECT
			 TijdelijkeRapportage.beleggingscategorie,
			 TijdelijkeRapportage.valuta,
			 TijdelijkeRapportage.fondsOmschrijving,
       TijdelijkeRapportage.Fonds,
			 TijdelijkeRapportage.actueleValuta,
			 TijdelijkeRapportage.totaalAantal,
			 TijdelijkeRapportage.beginwaardeLopendeJaar,
			 TijdelijkeRapportage.beginPortefeuilleWaardeInValuta,
			 TijdelijkeRapportage.beginPortefeuilleWaardeEuro / $beginQuery as beginPortefeuilleWaardeEuro,
			 TijdelijkeRapportage.actueleFonds, TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,
			 round((UNIX_TIMESTAMP(TijdelijkeRapportage.rapportageDatum) - UNIX_TIMESTAMP(TijdelijkeRapportage.koersDatum))/86400) as koersLeeftijd,
			 TijdelijkeRapportage.hoofdsector,
       TijdelijkeRapportage.hoofdcategorieOmschrijving,
       TijdelijkeRapportage.beleggingscategorie,
       TijdelijkeRapportage.beleggingscategorieOmschrijving,
       TijdelijkeRapportage.beleggingscategorieVolgorde,
       TijdelijkeRapportage.fondspaar, 
       TijdelijkeRapportage.type,
       TijdelijkeRapportage.".$verdeling." as verdeling,
       TijdelijkeRapportage.".$verdeling."Omschrijving as verdelingOmschrijving,
       $verdelingVolgorde,
       Fondsen.OptieBovenliggendFonds,
       if(Fondsen.OptieBovenliggendFonds='',TijdelijkeRapportage.Fonds ,Fondsen.OptieBovenliggendFonds) as onderliggendFonds
       ,TijdelijkeRapportage.Lossingsdatum,
       TijdelijkeRapportage.rekening
			 FROM TijdelijkeRapportage
			 LEFT Join Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
			 WHERE       
			 TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
			 TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".
			$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde,TijdelijkeRapportage.beleggingscategorieVolgorde, ".$verdelingVolgorde.",
         TijdelijkeRapportage.Lossingsdatum,
          onderliggendFonds,TijdelijkeRapportage.fondspaar, 
           TijdelijkeRapportage.Lossingsdatum, Fondsen.OptieBovenliggendFonds,
         TijdelijkeRapportage.type,TijdelijkeRapportage.fondsOmschrijving asc";
			debugSpecial($query,__FILE__,__LINE__); //TijdelijkeRapportage.type <> 'rente' AND
//echo $query;exit;
			$DB2->SQL($query);
			$DB2->Query();
			$somVelden=array('actuelePortefeuilleWaardeInValuta','actuelePortefeuilleWaardeEuro','beginPortefeuilleWaardeEuro');
			$vedelingen=array('hoofdcategorie'=>'bi','beleggingscategorie'=>'b','verdeling'=>'','alles'=>'');
			$omschrijvingVelden=array('hoofdcategorieOmschrijving'=>'hoofdcategorie','beleggingscategorieOmschrijving'=>'beleggingscategorie','verdelingOmschrijving'=>$verdeling);
			$regels=array();
      //$regels[]=array('hoofdcategorieOmschrijving'=>'hoofdcategorieOmschrijving','hoofdsector'=>'hoofdsector','verdeling'=>'verdeling','beleggingscategorie'=>'beleggingscategorie','fondsOmschrijving'=>'fondsOmschrijving','verdelingOmschrijving'=>'verdelingOmschrijving');
      while($subdata = $DB2->NextRecord())
			{
			   $regels[]=$subdata;
      }
      //listarray($regels);
      //exit;
      foreach($regels as $subdata)
      {
        if($subdata['type']=='rekening' && $_POST['anoniem']!=1)
          $subdata['fondsOmschrijving'].=" ".ereg_replace("[^0-9]","",$subdata['rekening']);
       
        if($subdata['beleggingscategorie']=='AAND')
        {
          foreach ($omschrijvingVelden as $veldNaam=>$omschrijving)
	 	        if($subdata[$veldNaam]=='' )
              $subdata[$veldNaam] ="Geen $omschrijving";
        }
          
			  foreach (array_reverse($vedelingen,true) as $type=>$weergave)
			  {
			    if($lastVerdeling[$type] <> $subdata[$type.'Omschrijving'] && isset($this->totalen[$type]))
          {
            if($this->pdf->modelLayout==true && $addModel==true && $type==$modelCategorieType)
            {
              $addModel=false;
              $this->printTotaal('missingModel','verdeling');
            }
			      $this->printTotaal($lastVerdeling[$type],$type,$weergave);
          }
			  }

			  foreach ($vedelingen as $type=>$weergave)
  			  if($subdata[$type.'Omschrijving'] <> '' && $lastVerdeling[$type] <> $subdata[$type.'Omschrijving'])
          {
            if($this->pdf->modelLayout==true && $this->modelCategorie == $subdata[$type])
            {
              $addModel=true;
              $modelCategorieType=$type;
            }
           
	  		  	$this->printKop(vertaalTekst($subdata[$type.'Omschrijving'],$this->pdf->rapport_taal),$type, $weergave);
          }
   
        if($subdata['type']=='rente')
        {
          $subdata['fondsOmschrijving']='lopende rente';
        }
          
       // echo $this->pdf->GetY()." ".$subdata['fondsOmschrijving']." <br>\n";
        if($this->pdf->GetY() > 194)  
          $this->printKop(vertaalTekst($subdata['beleggingscategorieOmschrijving'],$this->pdf->rapport_taal),'beleggingscategorie', 'b');

        if($this->pdf->GetY() > 190)
          $this->pdf->CellBorders=$this->subtotaalVerBorders;
        else
          $this->pdf->CellBorders=$this->subtotaalFondsBorders; 
          
				$this->pdf->setX($this->pdf->marge);
        
  			$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,$subdata['fondsOmschrijving']);//." |".$subdata['fondspaar'] 
				$this->pdf->setX($this->pdf->marge);
        $eurResultaat=$subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'];
       	$procentResultaat = ($eurResultaat / (abs($subdata['beginPortefeuilleWaardeEuro']) /100));
         
          
        if($subdata['type']=='rekening')
          $eurResultaat='';

        if($this->pdf->modelLayout==true)
            $this->pdf->row(array("",'',$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_decimaal),"","","","","","","",""));
        else
        {          
          if($subdata['type']=='rente')
          {
          	$this->pdf->row(array("","","","","","","","",
													$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_decimaal),'',''));
            $this->pdf->excelData[]=array($subdata['hoofdcategorieOmschrijving'],$subdata['beleggingscategorieOmschrijving'],$subdata['verdelingOmschrijving'],"","","","","","","",
              round($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_decimaal));
          }
          else
          {
            $this->pdf->row(array("",
                              "",
                              $subdata['valuta'],
                              $this->formatAantal($subdata['totaalAantal'], 0, true),
                              $this->formatGetal($subdata['beginwaardeLopendeJaar'], 2),
                              $this->formatGetal($subdata['beginPortefeuilleWaardeEuro'], $this->pdf->rapport_decimaal),
                              "",
                              $this->formatGetal($subdata['actueleFonds'], 2) . $markering,
                              $this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_decimaal),
                              $this->formatGetal($eurResultaat, $this->pdf->rapport_decimaal),
                              $this->formatGetal($procentResultaat, 2, "%")));
  
            $this->pdf->excelData[]=array($subdata['hoofdcategorieOmschrijving'],$subdata['beleggingscategorieOmschrijving'],$subdata['verdelingOmschrijving'],
              $subdata['fondsOmschrijving'],
              $subdata['valuta'],
              round($subdata['totaalAantal'], 4),
              round($subdata['beginwaardeLopendeJaar'], 2),
              round($subdata['beginPortefeuilleWaardeEuro'], $this->pdf->rapport_decimaal),
              round($subdata['actueleFonds'], 2) . $markering,
              round($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_decimaal),
              round($eurResultaat, $this->pdf->rapport_decimaal),
              round($procentResultaat, 2));
          }
        }                  
        unset($this->pdf->CellBorders);


      foreach ($vedelingen as $type=>$weergave)
      {
        $lastVerdeling[$type]=$subdata[$type.'Omschrijving'];
        foreach ($somVelden as $veld)
        {
          $this->totalen[$type][$veld]+=$subdata[$veld];
          if($subdata['type']=='rente')
            $this->totalenRente[$type][$veld]+=$subdata[$veld];
        }
//
        $this->totalen[$type]['eurResultaat']=($this->totalen[$type]['actuelePortefeuilleWaardeEuro'] - $this->totalen[$type]['beginPortefeuilleWaardeEuro']) - $this->totalenRente[$type]['actuelePortefeuilleWaardeEuro']  ;
    	  $this->totalen[$type]['procentResultaat'] = ($this->totalen[$type]['eurResultaat'] / (abs($this->totalen[$type]['beginPortefeuilleWaardeEuro']) /100));
      }
      
  //    listarray($subdata['fondsOmschrijving']);
//listarray($this->totalenRente);   

		}

	  foreach (array_reverse($vedelingen,true) as $type=>$weergave)
		{
		  if(isset($this->totalen[$type]) && $type <> 'alles' && $lastVerdeling[$type]<> '')
		     $this->printTotaal($lastVerdeling[$type],$type,$weergave);
		}

   	// print grandtotaal
		$this->pdf->ln();
		$this->printTotaal('','alles','B');
    $this->pdf->SetWidths(array(200));
    //$this->pdf->row(array("* Resultaat is exclusief lopende rente"));
    $this->pdf->MultiCell(200,$this->pdf->rowHeight,"* Resultaat is exclusief lopende rente",0,'L');

if($this->pdf->modelLayout==false)
{
////////////
    $this->pdf->tweedeDeel=true;
    $this->pdf->verdeling=$this->verdeling;
    $this->pdf->AddPage();
    //$this->pdf->rapportageDatumWaarde=$totaalWaarde;
		getTypeGrafiekData($this,$this->verdeling,"AND Beleggingscategorie='AAND'"); //
		//getTypeGrafiekData($this,'Regio');
    if($this->verdeling=='regio')
    {
      getTypeGrafiekData($this, 'Beleggingscategorie');
      $n=0;
      $categorieKleuren=array();
      foreach($this->pdf->grafiekData['Beleggingscategorie']['port']['procent'] as $categorie=>$percentage)
      {
        $categorieKleuren[$categorie]=$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur'][$n];
        $n++;
      }
      $categorie='AAND';
      $categorieOmschrijving='Aandelen';
      $aandelenDeel=$this->pdf->grafiekData['Beleggingscategorie']['port']['procent'][$categorie];
     // unset($this->pdf->veldOmschrijvingen['Beleggingscategorie'][$categorie]);
      unset($this->pdf->grafiekData['Beleggingscategorie']['port']['procent'][$categorie]);
      unset($this->pdf->grafiekData['Beleggingscategorie']['port']['waarde'][$categorie]);
      unset($this->pdf->grafiekData['Beleggingscategorie']['grafiek'][$categorieOmschrijving]);
      $backupCategorieVerdeling=$this->pdf->grafiekData['Beleggingscategorie'];
      $this->pdf->grafiekData['Beleggingscategorie']=array();
 
      $query="SELECT Regio,RegioOmschrijving,RegioVolgorde FROM TijdelijkeRapportage WHERE  TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
			 TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' AND Beleggingscategorie='AAND'" . $__appvar['TijdelijkeRapportageMaakUniek']." ORDER BY RegioVolgorde";
  		$DB2->SQL($query);
			$DB2->Query();
      $regioVolgorde=array();
	    while($data = $DB2->NextRecord())
			{
        $regioVolgorde[$data['Regio']]=$data['RegioVolgorde'];
        $regioOmschrijvingen[$data['Regio']]=$data['RegioOmschrijving'];
      }
	    $n=0;
      $lastCategorie='';
	    foreach($this->pdf->grafiekData['regio']['port']['procent'] as $regio=>$percentage)
      {
        $percentage=$percentage*$aandelenDeel;
        $volgorde=$regioVolgorde[$regio];
        $kleur=$this->pdf->grafiekData['regio']['grafiekKleur'][$n];
        if($volgorde<11)
        {
          $categorieOmschrijving = "Regio";
          $kleur=array(255,0,0);
        }
        elseif($volgorde<20)
        {
          $categorieOmschrijving = "Emerging markets";
          $kleur=array(255,50,50);
        }
        else
        {
          $categorieOmschrijving = "Thema's";
          $kleur=array(255,123,0);
        }
        $this->pdf->veldOmschrijvingen['Beleggingscategorie'][$categorieOmschrijving]=$categorieOmschrijving;
        $this->pdf->grafiekData['Beleggingscategorie']['port']['procent'][$categorieOmschrijving]+=$percentage;
        $this->pdf->grafiekData['Beleggingscategorie']['port']['waarde'][$categorieOmschrijving]+=$this->pdf->grafiekData['regio']['port']['waarde'][$regio];
        $this->pdf->grafiekData['Beleggingscategorie']['grafiek'][$categorieOmschrijving]+=$percentage*100;
        if($lastCategorie<>$categorieOmschrijving)
          $this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur'][]=$kleur;
        $n++;
        $lastCategorie=$categorieOmschrijving;
      }
      
      foreach($backupCategorieVerdeling['port']['procent'] as $categorie=>$percentage)
      {
        $this->pdf->grafiekData['Beleggingscategorie']['port']['procent'][$categorie]+=$percentage;
        $this->pdf->grafiekData['Beleggingscategorie']['port']['waarde'][$categorie]+=$backupCategorieVerdeling['port']['waarde'][$categorie];
        $this->pdf->grafiekData['Beleggingscategorie']['grafiek'][$this->pdf->veldOmschrijvingen['Beleggingscategorie'][$categorie]]+=$percentage*100;
        $this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur'][]=$categorieKleuren[$categorie];
      }
    }
    else
    {
      getTypeGrafiekData($this, 'Beleggingscategorie');
    }
  
    $y=$this->pdf->GetY();
    $this->pdf->setWidths(array(5,70,25,25));
		$this->pdf->setAligns(array('L','L','R','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array('','U','U','U','U');
    $this->pdf->row(array('','Beleggingscategorie','in euro','in %'));
    $this->pdf->CellBorders = array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $totaal=0;
    $totaalProcent=0;
    
   	foreach ($this->pdf->grafiekData['Beleggingscategorie']['port']['waarde'] as $categorie=>$waarde)
		{
	    $this->pdf->row(array('',$this->pdf->veldOmschrijvingen['Beleggingscategorie'][$categorie],$this->formatGetal($waarde,0),$this->formatGetal($this->pdf->grafiekData['Beleggingscategorie']['port']['procent'][$categorie]*100,1)));
      $totaal+=$waarde;
		  $totaalProcent+=$this->pdf->grafiekData['Beleggingscategorie']['port']['procent'][$categorie];
		}
    
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array('','',array('TS','UU'),array('TS','UU'));
    $this->pdf->row(array('','Totaal',$this->formatGetal($totaal,0),$this->formatGetal($totaalProcent*100,1)));
    $this->pdf->CellBorders = array();
    
    
    $this->pdf->SetY($y);
    $this->pdf->setWidths(array(150,70,25,25));
		$this->pdf->setAligns(array('L','L','R','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array('','U','U','U','U');
    $this->pdf->row(array('','Aandelen','in euro','in %'));
    $this->pdf->CellBorders = array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $totaal=0;
    $totaalProcent=0;
    foreach ($this->pdf->grafiekData[$this->verdeling]['port']['waarde'] as $categorie=>$waarde)
		{
		  if($this->pdf->veldOmschrijvingen[$this->verdeling][$categorie] == '')
        $omschrijving="Geen ".$this->verdeling;
      else
		    $omschrijving=$this->pdf->veldOmschrijvingen[$this->verdeling][$categorie];
	    $this->pdf->row(array('',$omschrijving,$this->formatGetal($waarde,0),$this->formatGetal($this->pdf->grafiekData[$this->verdeling]['port']['procent'][$categorie]*100,1)));
      $totaal+=$waarde;
		  $totaalProcent+=$this->pdf->grafiekData[$this->verdeling]['port']['procent'][$categorie];
		}
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array('','',array('TS','UU'),array('TS','UU'));
    $this->pdf->row(array('','Totaal',$this->formatGetal($totaal,0),$this->formatGetal($totaalProcent*100,1)));
    $this->pdf->CellBorders = array();


$diameter = 35;
$hoek = 30;
$dikte = 10;
$Xas= 75;
$yas= 140;
$grafiekTonenEen=true;
$grafiekTonenTwee=true;
//print_r($grafiekData);exit;
foreach ($this->pdf->grafiekData['Beleggingscategorie']['grafiek'] as $omschrijving=>$waarde)
{
  if($waarde < 0)
    $grafiekTonenEen=false;
  $grafiekData['OIB']['Omschrijving'][]=$omschrijving;
  $grafiekData['OIB']['Percentage'][]=$waarde;
}

//if($this->verdeling=='')
foreach ($this->pdf->grafiekData[$this->verdeling]['grafiek'] as $omschrijving=>$waarde)
{
  if($waarde < 0)
    $grafiekTonenTwee=false;
  if($omschrijving=='Overige')
    $omschrijving="Geen ".$this->verdeling;
  $grafiekData[$this->verdeling]['Omschrijving'][]=$omschrijving;
  $grafiekData[$this->verdeling]['Percentage'][]=$waarde;
}

if($grafiekTonenEen==True)
{
  $this->pdf->set3dLabels($grafiekData['OIB']['Omschrijving'],$Xas,$yas,$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur']);
  $this->pdf->Pie3D($grafiekData['OIB']['Percentage'],$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur'],$Xas,$yas,$diameter,$hoek,$dikte,"Beleggingscategorie");
}

if($grafiekTonenTwee==True)
{
  $this->pdf->set3dLabels($grafiekData[$this->verdeling]['Omschrijving'],$Xas+145,$yas,$this->pdf->grafiekData[$this->verdeling]['grafiekKleur']);
  $this->pdf->Pie3D($grafiekData[$this->verdeling]['Percentage'],$this->pdf->grafiekData[$this->verdeling]['grafiekKleur'],$Xas+145,$yas,$diameter,$hoek,$dikte,"Aandelen");
}
}

$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}
}
?>

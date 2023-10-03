<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");

class RapportHUIS_L102
{
	function RapportHUIS_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum, $valuta = 'EUR')
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "HUIS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_HSE_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_HSE_titel;
		else
			$this->pdf->rapport_titel = "Vermogensbalans";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->tweedeMarge=130;
    $this->crmData=array();
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
		return number_format($waarde,$dec,",",".");
	}


	function writeRapport()
	{
		global $__appvar;
		// rapport settings
		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type .'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type .'Paginas']=$this->pdf->rapport_titel;
		// haal totaalwaarde op om % te berekenen
    
    
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $zorg=new zorgplichtControle($this->pdf->portefeuilledata);
    $zorgPlichtResultaat=$zorg->zorgplichtMeting($this->pdf->portefeuilledata,$this->rapportageDatum);
    $zorgPlichtResultaat['zorgMetingReden']=str_replace("\n"," ",$zorgPlichtResultaat['zorgMetingReden']);
    
    $this->getCRMdata();
    
    $this->pdf->setY(30);
    $this->toonPositie($zorgPlichtResultaat);
    $this->pdf->setY(70);
    
    //$this->pdf->rapport_titel='';
    //$this->pdf->addPage();

	}
	function getCRMdata()
  {
    $gebruikteCrmVelden=array('OnrWoning','OnrVastgoed','OnrTotaal','ZakBuitenVen','ZakInVen','ZakTotaal','VerzPensioensparen','VerzSparen','VerzBedrijfsparen','VerzTotaal','BezDuurzameGoederen',
      'BezInboedel','BezVerzamelingen','BezTotaal','OnrLenWoning','OnrLenVastgoed','OnrLenTotaal','ZakLenBuitenVen','ZakLenInVen','ZakLenTotaal','VerzLenTotaal','BezLenDuurzameGoederen','BezLenInboedel',
      'BezLenVerzamelingen','BezLenTotaal','PersLeningen','VermAnder','VermNetto','ActivaTotaal','PassivaTotaal');
    
    $query = "DESC CRM_naw";
    $DB=new DB();
    $DB->SQL($query);
    $DB->Query();
    $crmVelden=array();
    while($data=$DB->nextRecord())
    {
      $crmVelden[]=strtolower($data['Field']);
    }
    $nawSelect='';
    foreach($gebruikteCrmVelden as $veld)
    {
      if(in_array(strtolower($veld),$crmVelden))
      {
        $nawSelect.=",CRM_naw.$veld ";
      }
    }
  
    $query="SELECT Portefeuilles.risicoklasse,laatstePortefeuilleWaarde.laatsteWaarde $nawSelect FROM Portefeuilles
 LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille=CRM_naw.Portefeuille
 LEFT JOIN laatstePortefeuilleWaarde ON Portefeuilles.Portefeuille=laatstePortefeuilleWaarde.Portefeuille
 WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";
    $DB->SQL($query);
    $this->crmData=$DB->lookupRecord();

  }
  
  function toonPositie()
  {
  


    $crmObject=new Naw();
    $activa=array('BalansDeelnemingen','BalansOnroerendGoed','Balans1eEigenWoning','BalansEffecten','BalansLiquiditeiten','BalansActivaOverig','BalansTotaalActiva');
    $activaTonen=array();
    $passiva=array('BalansEigenVermogen','BalansSchulden','BalansHypotheek','BalansBelastingClaim','BalansPassivaOverig','BalansTotaalPassiva');
    $passivaTonen=array();
    $omschrijvingen=array();
    foreach($crmObject->data['fields'] as $key=>$dataFields)
    {
      foreach($this->crmData as $veld=>$waarde)
      {
        if(strtolower($veld)==strtolower($key))
        {
          $omschrijvingen[$key] = $dataFields['description'];
        }
      }
    }
    $portefeuilleWaarden=array();
    $portefeuilleDetails=array();
    $categorieOmschrijving=array();
    $gegevens=berekenPortefeuilleWaarde($this->portefeuille,$this->rapportageDatum,(substr($this->rapportageDatum,5,5)=='01-01'?true:false));
    $activaTotaal=0;
    
    $totalePortefeuilleWaarde=0;
   // $verdeling='beleggingscategorie';
    $verdeling='hoofdcategorie';
    foreach($gegevens as $waarde)
    {
      if($waarde[$verdeling]=='')
      {
        $waarde[$verdeling]='GeenCategorie';
        $waarde[$verdeling.'Omschrijving']='Geen categorie';
      }
      $portefeuilleWaarden[$waarde[$verdeling]]+=$waarde['actuelePortefeuilleWaardeEuro'];
      $categorieVolgorde[$waarde[$verdeling]]=$waarde[$verdeling.'Volgorde'];
      $categorieOmschrijving[$waarde[$verdeling]]=$waarde[$verdeling.'Omschrijving'];
      $totalePortefeuilleWaarde+=$waarde['actuelePortefeuilleWaardeEuro'];
    }
    
    asort($categorieVolgorde);
    foreach($categorieVolgorde as $categorie=>$volgorde)
    {
      $portefeuilleDetails[$categorieOmschrijving[$categorie]]+=$portefeuilleWaarden[$categorie];
    }
    
    
    $beginY=$this->pdf->getY();
    $widths=array(100);
    $this->pdf->SetWidths(array(170));
    $this->pdf->SetAligns(array('C'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), array_sum($this->pdf->widths), 8 , 'F');
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    
    $this->pdf->row(array(vertaalTekst('VERMOGENSBALANS',$this->pdf->rapport_taal)));
  
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  
    $this->pdf->SetWidths(array(50+30,10,50+30));
    $this->pdf->SetAligns(array('C','','C'));
    $this->pdf->row(array(vertaalTekst('Activa',$this->pdf->rapport_taal),'',vertaalTekst('Passiva',$this->pdf->rapport_taal),''));
  
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    //$this->pdf->CellBorders = array('','R','','');
    $this->pdf->ln();
    $this->pdf->SetWidths(array(50,30,10,50,30));
    $this->pdf->SetAligns(array('L','R','C','L','R'));
    $velden[]=array('b','OnrTotaal' ,'OnrLenTotaal');
    $velden[]=array('','OnrWoning'  ,'OnrLenWoning');
    $velden[]=array('','OnrVastgoed','OnrLenVastgoed');
    $velden[]=array('','','');
    $velden[]=array('b','ZakTotaal','ZakLenTotaal');
    $velden[]=array('','ZakBuitenVen','ZakLenBuitenVen');
    $velden[]=array('','ZakInVen','ZakLenInVen');
    $velden[]=array('','','');
    $velden[]=array('b','VerzTotaal','VerzLenTotaal');
    $velden[]=array('','VerzPensioensparen','');
    $velden[]=array('','VerzSparen','');
    $velden[]=array('','VerzBedrijfsparen','');
    $velden[]=array('','','');
    $velden[]=array('b','BezTotaal','BezLenTotaal');
    $velden[]=array('','BezDuurzameGoederen','BezLenDuurzameGoederen');
    $velden[]=array('','BezInboedel','BezLenInboedel');
    $velden[]=array('','BezVerzamelingen','BezLenVerzamelingen');
    $velden[]=array('','','');
    $velden[]=array('a');
    $velden[]=array('','','');

    $velden[]=array('b','VermAnder','PersLeningen');
    $velden[]=array('','VermAnderPe','PersLenComPE');
    $velden[]=array('','VermAnderOV','PersLenOV');
    $velden[]=array('','','');
    $velden[]=array('','','VermNetto');
    $velden[]=array('','','');
    $velden[]=array('','ActivaTotaal','PassivaTotaal');
    
    $ballansRows=array();
    foreach($velden as $veldData)
    {
      if($veldData[0]=='a')
      {
        $ballansRows[] = array('b',array(vertaalTekst('Portefeuille activa',$this->pdf->rapport_taal),$this->formatGetal($totalePortefeuilleWaarde,0),'',vertaalTekst('Persoonlijke leningen',$this->pdf->rapport_taal),($this->crmData['PersLeningen'] <> ''?$this->formatGetal($this->crmData['PersLeningen'], 0):'')));
        foreach($portefeuilleDetails as $cat=>$waarde)
          $ballansRows[] = array('',array($cat,$this->formatGetal($waarde,0),'',''));
  
        $activaTotaal+=$totalePortefeuilleWaarde;
      }
      else
      {
        if($veldData[1]=='ActivaTotaal')
          $this->crmData[$veldData[1]]=$activaTotaal;
        if($veldData[2]=='VermNetto' || $veldData[2]=='PassivaTotaal')
          $this->crmData[$veldData[2]]+=$totalePortefeuilleWaarde;
        
        $ballansRows[] = array($veldData[0], array(($veldData[1] <> ''?vertaalTekst($omschrijvingen[$veldData[1]],$this->pdf->rapport_taal):''), ($veldData[1] <> ''?$this->formatGetal($this->crmData[$veldData[1]], 0):''), '',
          ($veldData[2] <> ''?vertaalTekst($omschrijvingen[$veldData[2]],$this->pdf->rapport_taal):''), ($veldData[2] <> ''?$this->formatGetal($this->crmData[$veldData[2]], 0):'')));
        if($veldData[0]=='b' || $veldData[1]=='VermAnder')
          $activaTotaal+=$this->crmData[$veldData[1]];
      }
    }
    foreach($ballansRows as $row)
    {
      $this->pdf->SetFont($this->pdf->rapport_font,$row[0],$this->pdf->rapport_fontsize);
      $this->pdf->row($row[1]);
    }
   

  }
  
  
}
?>

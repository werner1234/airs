<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/classes/portefeuilleVerdieptClass.php");

class RapportVOLK_L126
{
	function RapportVOLK_L126($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VOLK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Portefeuille overzicht";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->pdf->rapport_rendementText="Rendement over verslagperiode";
    $this->aandeel=1;
    $this->grafieken=true;
    
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
	  //return number_format($waarde,$dec,",",".");
  }

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	  if($this->aandeel <> 1)
	    $waarde=round($waarde,0);
	  if ($VierDecimalenZonderNullen)
	  {
	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '0000' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];
	       if (!isset($newDec) && $decimaal != '0')
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


function getDividend($fonds,$portefeuille='')
  {
    global $__appvar;
    
    if($fonds=='')
      return 0;
    
    if($portefeuille=='')
      $portefeuille=$this->portefeuille;
      
     $query="SELECT rapportageDatum,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro as actuelePortefeuilleWaardeEuro,
         TijdelijkeRapportage.type,
         TijdelijkeRapportage.totaalAantal
     FROM TijdelijkeRapportage
     WHERE 
       TijdelijkeRapportage.fonds='$fonds' AND
       portefeuille = '".$portefeuille."'".$__appvar['TijdelijkeRapportageMaakUniek']. "
     GROUP BY rapportageDatum,TijdelijkeRapportage.type";
  
     $DB = new DB();
  	 $DB->SQL($query); 
		 $DB->Query();
     $totaal=0;
    $aantal=array();
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
     WHERE Rekeningen.Portefeuille='".$portefeuille."' AND
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
      //        echo "$fonds $aandeel  $boekdatum ".$this->rapportageDatum." ".($data['Credit']-$data['Debet'])."<br>\n";
      $totaal+=($data['Credit']-$data['Debet']);
      $totaalCorrected+=(($data['Credit']-$data['Debet'])*$aandeel);
    }
    //echo $totaal." $totaalCorrected<br>\n";
    return array('totaal'=>$totaal,'corrected'=>$totaalCorrected);
  }
  
	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT Vermogensbeheerders.grafiek_kleur, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex,
 Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client
 FROM Portefeuilles JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder=Vermogensbeheerders.Vermogensbeheerder
 WHERE Portefeuille = '".$this->portefeuille."'  ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
		$kleuren=unserialize($this->portefeuilledata['grafiek_kleur']);
		
		$volgorden=array();
		$query="SELECT
KeuzePerVermogensbeheerder.vermogensbeheerder,
KeuzePerVermogensbeheerder.categorie,
KeuzePerVermogensbeheerder.waarde,
KeuzePerVermogensbeheerder.Afdrukvolgorde
FROM
KeuzePerVermogensbeheerder
WHERE KeuzePerVermogensbeheerder.vermogensbeheerder='".$this->portefeuilledata['Vermogensbeheerder']."'
ORDER BY KeuzePerVermogensbeheerder.categorie,
KeuzePerVermogensbeheerder.Afdrukvolgorde,KeuzePerVermogensbeheerder.waarde";
    $DB->SQL($query);
    $DB->Query();
    while($data = $DB->nextRecord())
    {
      $volgorden[$data['categorie']][$data['waarde']]=0;
    }
    $query="SELECT Valuta,Afdrukvolgorde FROM Valutas ORDER BY Afdrukvolgorde,Valuta";
    $DB->SQL($query);
    $DB->Query();
    while($data = $DB->nextRecord())
    {
      $volgorden['Valutas'][$data['Valuta']]=0;
    }
		

		$this->pdf->widthB = array(70,20,18,18,21,4,21,21,4,22,20,20,15);
		$this->pdf->alignB = array('L','R','R','R','R','R','R','R','R','R','R','R','R');
		// voor kopjes
		$this->pdf->widthA = 	$this->pdf->widthB ;//array(66+$omschrijvingExtra,18,15,21,21,15,21,21,15,22,22,$fondsresultwidth,15,15);
		$this->pdf->alignA =  $this->pdf->alignB;//array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');
    if($this->grafieken == true)
    {
      $this->pdf->AddPage();
    }
    $this->pdf->rapport_fondsVerdiept_fontcolor=array('r'=>100,'g'=>100,'b'=>100);
    

    // haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						  .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		$actueleWaardePortefeuille = 0;

    $query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
				" TijdelijkeRapportage.fonds, ".
				" TijdelijkeRapportage.actueleValuta, ".
				" TijdelijkeRapportage.totaalAantal * ".$this->aandeel." as totaalAantal, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar , ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta * ".$this->aandeel." as beginPortefeuilleWaardeInValuta,".
				" TijdelijkeRapportage.Valuta,TijdelijkeRapportage.ValutaVolgorde, ".
				" if(TijdelijkeRapportage.Valuta='".$this->pdf->rapportageValuta."',TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar, TijdelijkeRapportage.beginPortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersBegin.")  * ".$this->aandeel." as beginPortefeuilleWaardeEuro, ".
				" TijdelijkeRapportage.actueleFonds,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta * ".$this->aandeel." as actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." as actuelePortefeuilleWaardeEuro ,
				 TijdelijkeRapportage.beleggingscategorie,
				 TijdelijkeRapportage.beleggingscategorieOmschrijving,
				 TijdelijkeRapportage.regio,
				 TijdelijkeRapportage.regioOmschrijving,
				 TijdelijkeRapportage.type,
				 TijdelijkeRapportage.rekening,
				 Fondsen.Portefeuille as huisfondsPortefeuille,
				 if(Fondsen.OptieBovenliggendFonds='',TijdelijkeRapportage.Fonds ,Fondsen.OptieBovenliggendFonds) as onderliggendFonds,
				 TijdelijkeRapportage.hoofdcategorie,
				 TijdelijkeRapportage.hoofdcategorieOmschrijving,
				 TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
				" FROM TijdelijkeRapportage LEFT Join Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				"	ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde,
				 TijdelijkeRapportage.beleggingscategorieVolgorde,
				 TijdelijkeRapportage.Lossingsdatum,
				 TijdelijkeRapportage.fondspaar, TijdelijkeRapportage.fondsOmschrijving asc";//exit;
		
			// print detail (select from tijdelijkeRapportage)
			debugSpecial($query,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();
//echo $query."<br><br>";exit;
		  $verdelingen=array('grafiekBeleggingscategorie'=>array('procent'=>$volgorden['Beleggingscategorien'],'omschrijving'=>$volgorden['Beleggingscategorien'],'kleur'=>$volgorden['Beleggingscategorien']),
                         'grafiekRegio'=>array('procent'=>$volgorden['Regios'],'omschrijving'=>$volgorden['Regios'],'kleur'=>$volgorden['Regios']),
                         'grafiekValuta'=>array('procent'=>$volgorden['Valutas'],'omschrijving'=>$volgorden['Valutas'],'kleur'=>$volgorden['Valutas']));
    

		  $somvelden=array('percentageVanTotaal','beginPortefeuilleWaardeEuro','actuelePortefeuilleWaardeEuro','dividendTotaal','fondsResultaat','valutaResultaat','dividendCorrected');
			while($subdata = $DB2->NextRecord())
      {
      	if($subdata['type']=='fondsen')
        {
          $fondsResultaat = ($subdata['actuelePortefeuilleWaardeInValuta'] - $subdata['beginPortefeuilleWaardeInValuta']) * $subdata['actueleValuta'] / $this->pdf->ValutaKoersEind;
          $valutaResultaat = $subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] - $fondsResultaat;
          $dividend = $this->getDividend($subdata['fonds']);
    
          $dividend['totaal'] = $dividend['totaal'] * $this->aandeel;
          $dividend['corrected'] = $dividend['corrected'] * $this->aandeel;
          $fondsResultaatprocent = ($fondsResultaat / $subdata['beginPortefeuilleWaardeEuro']) * 100;
          $procentResultaat = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] + $dividend['corrected']) / ($subdata['beginPortefeuilleWaardeEuro'] / 100));
          if ($subdata['beginPortefeuilleWaardeEuro'] < 0)
          {
            $procentResultaat = -1 * $procentResultaat;
          }
        }
        else
				{
          $fondsResultaat=0;
          $valutaResultaat=0;
          $dividend=array();
          $fondsResultaatprocent=0;
          $procentResultaat=0;
				}
        $percentageVanTotaal = ($subdata['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde / 100);
        
        
  
        $subdata['fondsResultaat']=$fondsResultaat;
        $subdata['valutaResultaat']=$valutaResultaat;
        $subdata['dividendTotaal']=$dividend['totaal'];
        $subdata['dividendCorrected']=$dividend['corrected'];
        $subdata['fondsResultaatprocent']=$fondsResultaatprocent;
        $subdata['percentageVanTotaal']=$percentageVanTotaal;
        $subdata['procentResultaat']=$procentResultaat;//*$percentageVanTotaal/100;
  

          foreach ($somvelden as $veld)
          {
            $verdelingen['hoofdcategorie'][$subdata['hoofdcategorie']][$veld] += $subdata[$veld];
          }
    
        $verdelingen['hoofdcategorie'][$subdata['hoofdcategorie']]['procentResultaat']+=$procentResultaat*$percentageVanTotaal/100;
        //echo $subdata['hoofdcategorie']." ". $verdelingen['hoofdcategorie'][$subdata['hoofdcategorie']]['procentResultaat']."+=$procentResultaat*$percentageVanTotaal/100 <br>\n";
        $verdelingen['hoofdcategorie'][$subdata['hoofdcategorie']]['hoofdcategorieOmschrijving']=$subdata['hoofdcategorieOmschrijving'];
  
        $verdelingen['fondsenPerHoofdcategorie'][$subdata['hoofdcategorieOmschrijving']][$subdata['Valuta']][]=$subdata;
        $verdelingen['valutaPerHoofdcategorie'][$subdata['hoofdcategorieOmschrijving']][$subdata['Valuta']]=$subdata['ValutaVolgorde'];

        if($subdata['huisfondsPortefeuille']<>'')// && 1==2)
        {
          $huisfondswaarden = berekenPortefeuilleWaarde($subdata['huisfondsPortefeuille'], $this->rapportageDatum, 0, $this->pdf->rapportageValuta, $this->rapportageDatumVanaf);
          $huisfondsportefeuilleWaarde = 0;
          foreach ($huisfondswaarden as $fonds)
          {
            $huisfondsportefeuilleWaarde += $fonds['actuelePortefeuilleWaardeEuro'];
          }
          
          foreach ($huisfondswaarden as $fondsData)
          {
            $fondsData['percentageVanTotaal']=($fondsData['actuelePortefeuilleWaardeEuro']/$huisfondsportefeuilleWaarde)*$percentageVanTotaal;
            //echo "  ".$fondsData['percentageVanTotaal']."=(".$fondsData['actuelePortefeuilleWaardeEuro']."/$huisfondsportefeuilleWaarde)*$percentageVanTotaal ".$fondsData['fondsOmschrijving'].";<br>\n";
            $verdelingen['grafiekValuta']['procent'][$fondsData['valuta']] += $fondsData['percentageVanTotaal'];
            $verdelingen['grafiekValuta']['omschrijving'][$fondsData['valuta']] = $fondsData['valuta'];
            if (!is_array($verdelingen['grafiekValuta']['kleur'][$fondsData['valuta']]))
            {
              $verdelingen['grafiekValuta']['kleur'][$fondsData['valuta']] = array($kleuren['OIV'][$fondsData['valuta']]['R']['value'], $kleuren['OIV'][$fondsData['valuta']]['G']['value'], $kleuren['OIV'][$fondsData['valuta']]['B']['value']);
            }
  
            $verdelingen['grafiekBeleggingscategorie']['procent'][$fondsData['beleggingscategorie']] += $fondsData['percentageVanTotaal'];
            $verdelingen['grafiekBeleggingscategorie']['omschrijving'][$fondsData['beleggingscategorie']] = $fondsData['beleggingscategorieOmschrijving'];
            if (!is_array($verdelingen['grafiekBeleggingscategorie']['kleur'][$fondsData['beleggingscategorie']]))
            {
              $verdelingen['grafiekBeleggingscategorie']['kleur'][$fondsData['beleggingscategorie']] = array($kleuren['OIB'][$fondsData['beleggingscategorie']]['R']['value'], $kleuren['OIB'][$fondsData['beleggingscategorie']]['G']['value'], $kleuren['OIB'][$fondsData['beleggingscategorie']]['B']['value']);
            }
            if($fondsData['Regio']=='')
            {
              $fondsData['Regio'] = 'geenRegio';
              $fondsData['regioOmschrijving'] = 'Geen regio';
            }
            $verdelingen['grafiekRegio']['procent'][$fondsData['Regio']] += $fondsData['percentageVanTotaal'];
            $verdelingen['grafiekRegio']['omschrijving'][$fondsData['Regio']] = $fondsData['regioOmschrijving'];
            if (!is_array($verdelingen['grafiekRegio']['kleur'][$fondsData['Regio']]))
            {
              $verdelingen['grafiekRegio']['kleur'][$fondsData['Regio']] = array($kleuren['OIR'][$fondsData['Regio']]['R']['value'], $kleuren['OIR'][$fondsData['Regio']]['G']['value'], $kleuren['OIR'][$fondsData['Regio']]['B']['value']);
            }
          }
        }
        else
        {
          $verdelingen['grafiekValuta']['procent'][$subdata['Valuta']] += $subdata['percentageVanTotaal'];
          $verdelingen['grafiekValuta']['omschrijving'][$subdata['Valuta']] = $subdata['Valuta'];
          if (!is_array($verdelingen['grafiekValuta']['kleur'][$subdata['Valuta']]))
          {
            $verdelingen['grafiekValuta']['kleur'][$subdata['Valuta']] = array($kleuren['OIV'][$subdata['Valuta']]['R']['value'], $kleuren['OIV'][$subdata['Valuta']]['G']['value'], $kleuren['OIV'][$subdata['Valuta']]['B']['value']);
          }
  
          $verdelingen['grafiekBeleggingscategorie']['procent'][$subdata['beleggingscategorie']] += $subdata['percentageVanTotaal'];
          $verdelingen['grafiekBeleggingscategorie']['omschrijving'][$subdata['beleggingscategorie']] = $subdata['beleggingscategorieOmschrijving'];
          if (!is_array($verdelingen['grafiekBeleggingscategorie']['kleur'][$subdata['beleggingscategorie']]))
          {
            $verdelingen['grafiekBeleggingscategorie']['kleur'][$subdata['beleggingscategorie']] = array($kleuren['OIB'][$subdata['beleggingscategorie']]['R']['value'], $kleuren['OIB'][$subdata['beleggingscategorie']]['G']['value'], $kleuren['OIB'][$subdata['beleggingscategorie']]['B']['value']);
          }

          $verdelingen['grafiekRegio']['procent'][$subdata['regio']] += $subdata['percentageVanTotaal'];
          $verdelingen['grafiekRegio']['omschrijving'][$subdata['regio']] = $subdata['regioOmschrijving'];
          if (!is_array($verdelingen['grafiekRegio']['kleur'][$subdata['regio']]))
          {
            $verdelingen['grafiekRegio']['kleur'][$subdata['regio']] = array($kleuren['OIR'][$subdata['regio']]['R']['value'], $kleuren['OIR'][$subdata['regio']]['G']['value'], $kleuren['OIR'][$subdata['regio']]['B']['value']);
          }
        }
  
      }

      foreach($verdelingen as $deel=>$details)
      {
        if(substr($deel,0,7)=='grafiek')
        {
          foreach($details as $var=>$varDetails)
          {
            foreach ($varDetails as $key => $value)
            {
              if ($value === 0)
              {
                unset($verdelingen[$deel][$var][$key]);
              }
            }
          }
        }
      }
    
    $somVelden=array('percentageVanTotaal','actuelePortefeuilleWaardeEuro','dividendTotaal','fondsResultaat','valutaResultaat','dividendCorrected');//,'procentResultaat');
    $totalen=array();
    $totalenPerHoofdcategorie=array();
    if($this->grafieken == true)
    {
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
//listarray($verdelingen['hoofdcategorie']);
      foreach($verdelingen['hoofdcategorie'] as $hoofcategorie=>$hoofdcategorieDetails)
			{
        
      //  $hoofdcategorieDetails['procentResultaat'] = (($hoofdcategorieDetails['actuelePortefeuilleWaardeEuro'] - $hoofdcategorieDetails['beginPortefeuilleWaardeEuro'] + $hoofdcategorieDetails['dividendCorrected']) / ($hoofdcategorieDetails['beginPortefeuilleWaardeEuro'] / 100));
        $hoofdcategorieDetails['procentResultaat'] = ($hoofdcategorieDetails['dividendTotaal']+$hoofdcategorieDetails['fondsResultaat']+$hoofdcategorieDetails['valutaResultaat'])/$hoofdcategorieDetails['beginPortefeuilleWaardeEuro']*100;
			  $this->pdf->row(array($hoofdcategorieDetails['hoofdcategorieOmschrijving'],
													'',
													$this->formatGetal($hoofdcategorieDetails['percentageVanTotaal'],2),
													'',
                          $this->formatGetal($hoofdcategorieDetails['actuelePortefeuilleWaardeEuro'],2),
													'','',
                          $this->formatGetal($hoofdcategorieDetails['beginPortefeuilleWaardeEuro'],2),
													'',
                          $this->formatGetal($hoofdcategorieDetails['dividendTotaal'],2),
                          $this->formatGetal($hoofdcategorieDetails['fondsResultaat'],2),
                          $this->formatGetal($hoofdcategorieDetails['valutaResultaat'],2),
                          $this->formatGetal($hoofdcategorieDetails['procentResultaat'],2)));
			  
			  foreach($somVelden as $veld)
			  	$totalen[$veld]+=$hoofdcategorieDetails[$veld];
        
        $totalen['procentResultaat']+=$hoofdcategorieDetails['procentResultaat']*($hoofdcategorieDetails['actuelePortefeuilleWaardeEuro']/($totaalWaarde / 100))/100;
        $totalenPerHoofdcategorie[$hoofcategorie]=$totalen;
			}
    $this->pdf->setDrawColor($this->pdf->rapportLineColor[0],$this->pdf->rapportLineColor[1],$this->pdf->rapportLineColor[2]);
    $this->pdf->CellBorders=array('','','','',array('TS','UU'),'','','','',array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'));
    $this->pdf->ln();
    $this->pdf->row(array('Totaal',
                      '',
                      '',
                      '',
                      $this->formatGetal($totalen['actuelePortefeuilleWaardeEuro'],2)
                    //  '','',
                    //  '',
                    //  '',
                    //  $this->formatGetal($totalen['dividendTotaal'],2),
                    //  $this->formatGetal($totalen['fondsResultaat'],2),
                    //  $this->formatGetal($totalen['valutaResultaat'],2),
                    //  $this->formatGetal($totalen['procentResultaat'],2)
                    ));
    $portefeuilleTotaal=$totalen;
    unset($this->pdf->CellBorders);
    
    $grafiekY=110;
    $grafiekR=55;
    $pWidth=$this->pdf->w;
    $emptySpace=$pWidth-(3*$grafiekR);
    $emptySpacePart=$emptySpace/4;
//echo $pWidth." $grafiekR ". $emptySpace;exit;
    
    $query="SELECT
KeuzePerVermogensbeheerder.categorie,
KeuzePerVermogensbeheerder.waarde,
KeuzePerVermogensbeheerder.Afdrukvolgorde
FROM
KeuzePerVermogensbeheerder
WHERE KeuzePerVermogensbeheerder.vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY KeuzePerVermogensbeheerder.categorie,KeuzePerVermogensbeheerder.Afdrukvolgorde,KeuzePerVermogensbeheerder.waarde";
    $DB2->SQL($query);
    $DB2->Query();
    $categorieVolgorde=array();
    while($data=$DB2->nextRecord())
    {
      $categorieVolgorde[$data['categorie']][$data['waarde']]=$data['Afdrukvolgorde'];
    }
   // listarray($categorieVolgorde);
   // listarray($verdelingen);
    $categorieKoppelingen=array('grafiekBeleggingscategorie'=>'Beleggingscategorien',
     'grafiekRegio'=>'Regios');
    
    foreach($categorieKoppelingen as $bronKey=>$volgordeKey)
    {
      $tmpKeys=array();
      if(isset($categorieVolgorde[$volgordeKey]))
      {
        foreach($categorieVolgorde[$volgordeKey] as $cat=>$volgorde)
        {
          if(isset($verdelingen[$bronKey]))
          {
            $tmpKeys['procent']=$verdelingen[$bronKey]['procent'];
            $tmpKeys['omschrijving']=$verdelingen[$bronKey]['omschrijving'];
            $tmpKeys['kleur']=$verdelingen[$bronKey]['kleur'];
          }
        }
      }
      if(count($tmpKeys)>0)
      {
        $verdelingen[$bronKey] = $tmpKeys;
     //   listarray($tmpKeys);
      }
    }
    //listarray($verdelingen);

      $this->pdf->setXY($emptySpacePart, $grafiekY);
      $this->PieChart($grafiekR, $verdelingen['grafiekBeleggingscategorie']);
      $this->pdf->setXY($emptySpacePart * 2 + $grafiekR, $grafiekY);
      $this->PieChart($grafiekR, $verdelingen['grafiekValuta']);
      $this->pdf->setXY($emptySpacePart * 3 + $grafiekR * 2, $grafiekY);
      $this->PieChart($grafiekR, $verdelingen['grafiekRegio']);
  
      if($this->aandeel==1)
      {
        $this->pdf->ln(8);
        $this->pdf->setWidths(array(280));
        $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
        $this->pdf->row(array('Passendheid & Geschiktheid'));
        $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
        $this->pdf->row(array('In dit overzicht vindt u de huidige verdeling van uw vermogen over de vermogenscategorieën Zakelijke waarden, Vastrentende waarden en Liquiditeiten. Hierdoor bevestigen wij dat uw portefeuille in overeenstemming is met onze vastlegging van uw beleggingsdoelstelling, beleggingshorizon, kennis- en ervaringsniveau, risicobereidheid en verliescapaciteit (uw cliëntprofiel).'));
      }
    }
    else
    {
      foreach($verdelingen['hoofdcategorie'] as $hoofcategorie=>$hoofdcategorieDetails)
      {
        foreach($somVelden as $veld)
          $totalen[$veld]+=$hoofdcategorieDetails[$veld];
        $totalen['procentResultaat']+=$hoofdcategorieDetails['procentResultaat']*($hoofdcategorieDetails['actuelePortefeuilleWaardeEuro']/($totaalWaarde / 100))/100;
        $totalenPerHoofdcategorie[$hoofcategorie]=$totalen;
      }
      $portefeuilleTotaal=$totalen;
    }
    
    
    $somVelden=array('percentageVanTotaal','actuelePortefeuilleWaardeEuro','dividendTotaal','fondsResultaat','valutaResultaat','beginPortefeuilleWaardeEuro','dividendCorrected');//'procentResultaat',
    foreach($verdelingen['fondsenPerHoofdcategorie'] as $hoofdCategorieOmschrijving=>$valutaVerdeling)
		{
      if($hoofdCategorieOmschrijving!='Liquiditeiten')
      {
        if ($this->grafieken == true)
        {
          $this->pdf->rapport_titel = "Portefeuille overzicht " . $hoofdCategorieOmschrijving;
        }
        $this->pdf->addPage();
      }
      else
      {
        $this->pdf->ln();
      }
      $totalen=array();
      
      
      asort($verdelingen['valutaPerHoofdcategorie'][$hoofdCategorieOmschrijving]);
      //listarray($verdelingen['valutaPerHoofdcategorie'][$hoofdCategorieOmschrijving]);
      //listarray($fondsDetails);
      foreach($verdelingen['valutaPerHoofdcategorie'][$hoofdCategorieOmschrijving] as $valuta=>$volgordeId)
      {
        if($hoofdCategorieOmschrijving=='Liquiditeiten' && $this->grafieken == false)
        {
          // geen liq regels en ook geen kop.
        }
        else
        {
          $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
          $this->pdf->row(array($valuta));
          $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
        }
        foreach ($valutaVerdeling[$valuta] as $fondsRegel)
        {
          if($hoofdCategorieOmschrijving=='Liquiditeiten')
          {
            if($this->grafieken == true)
            {
              $this->pdf->row(array($fondsRegel['fondsOmschrijving'].' '.$fondsRegel['rekening'],
                                $this->formatAantal($fondsRegel['totaalAantal'], 0, true),
                                $this->formatGetal($fondsRegel['percentageVanTotaal'], 2),
                                $this->formatGetal($fondsRegel['actueleFonds'], 2),
                                $this->formatGetal($fondsRegel['actuelePortefeuilleWaardeEuro'], 2)));
            }
          }
          else
          {
            $this->pdf->row(array($fondsRegel['fondsOmschrijving'],
                              $this->formatAantal($fondsRegel['totaalAantal'], 0, true),
                              $this->formatGetal($fondsRegel['percentageVanTotaal'], 2),
                              $this->formatGetal($fondsRegel['actueleFonds'], 2),
                              $this->formatGetal($fondsRegel['actuelePortefeuilleWaardeEuro'], 2),
                              '',
                              $this->formatGetal($fondsRegel['beginwaardeLopendeJaar'], 2),
                              $this->formatGetal($fondsRegel['beginPortefeuilleWaardeEuro'], 2),
                              '',
                              $this->formatGetal($fondsRegel['dividendTotaal'], 2),
                              $this->formatGetal($fondsRegel['fondsResultaat'], 2),
                              $this->formatGetal($fondsRegel['valutaResultaat'], 2),
                              $this->formatGetal($fondsRegel['procentResultaat'], 2)));
          }
          foreach ($somVelden as $veld)
          {
            $totalen[$veld] += $fondsRegel[$veld];
          }
          $totalen['procentResultaat'] += $fondsRegel['procentResultaat']*($fondsRegel['actuelePortefeuilleWaardeEuro']/($totaalWaarde / 100))/100;;
        }
      }
      $this->pdf->setDrawColor($this->pdf->rapportLineColor[0],$this->pdf->rapportLineColor[1],$this->pdf->rapportLineColor[2]);
      $this->pdf->CellBorders=array('','','','',array('TS','UU'),'','',array('TS','UU'),'',array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'));
      $this->pdf->ln();
      if($hoofdCategorieOmschrijving=='Liquiditeiten')
      {
        $this->pdf->row(array('Totaal '.$hoofdCategorieOmschrijving,
                          '',
                          '',
                          '',
                          $this->formatGetal($totalen['actuelePortefeuilleWaardeEuro'],2)));
      }
      else
      {
        
        //$totalen['procentResultaat'] = (($totalen['actuelePortefeuilleWaardeEuro'] - $totalen['beginPortefeuilleWaardeEuro'] + $totalen['dividendCorrected']) / ($totalen['beginPortefeuilleWaardeEuro'] / 100));
        $totalen['procentResultaat'] = ($totalen['dividendTotaal'] + $totalen['fondsResultaat'] + $totalen['valutaResultaat']) / $totalen['beginPortefeuilleWaardeEuro'] * 100;
        $this->pdf->row(array('Totaal '.$hoofdCategorieOmschrijving,
                        '',
                        '',
                        '',
                        $this->formatGetal($totalen['actuelePortefeuilleWaardeEuro'],2),
                        '','',
                        $this->formatGetal($totalen['beginPortefeuilleWaardeEuro'],2),
                        '',
                        $this->formatGetal($totalen['dividendTotaal'],2),
                        $this->formatGetal($totalen['fondsResultaat'],2),
                        $this->formatGetal($totalen['valutaResultaat'],2),
                        $this->formatGetal($totalen['procentResultaat'],2)));
      }
      unset($this->pdf->CellBorders);
		}
    
    $this->pdf->setDrawColor($this->pdf->rapportLineColor[0],$this->pdf->rapportLineColor[1],$this->pdf->rapportLineColor[2]);
    $this->pdf->CellBorders=array('','','','',array('TS','UU'),'','','','',array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'));
    if($this->grafieken==true)
    {
      $this->pdf->ln();
      $this->pdf->row(array('Totaal',
                        '',
                        '',
                        '',
                        $this->formatGetal($portefeuilleTotaal['actuelePortefeuilleWaardeEuro'] - $totalenPerHoofdcategorie['ZAK']['actuelePortefeuilleWaardeEuro'], 2)));
    }
    else
    {
      $this->pdf->ln();
      $this->pdf->ln();
      $this->pdf->row(array('Totale belegging',
                        '',
                        '',
                        '',
                        $this->formatGetal($portefeuilleTotaal['actuelePortefeuilleWaardeEuro'],2)));
      /*,
                        '','',
                        '',
                        '',
                        $this->formatGetal($portefeuilleTotaal['dividendTotaal'],2),
                        $this->formatGetal($portefeuilleTotaal['fondsResultaat'],2),
                        $this->formatGetal($portefeuilleTotaal['valutaResultaat'],2),
                        $this->formatGetal($portefeuilleTotaal['procentResultaat'],2)));*/
    }
    unset($this->pdf->CellBorders);
    

	}

	function getFondsKoers($fonds,$datum)
	{
	    $DB2=new DB();
	  	$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$datum."' AND Fonds = '".$fonds."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers = $DB2->LookupRecord();
			return $koers['Koers'];
	}
  
  function PieChart($radius,$data)
  {
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $hLegend = 2;
    
    $radius = floor($radius / 2);
    $XDiag = $XPage + $margin + $radius;
    $YDiag = $YPage + $margin + $radius;
    
    //Sectors
    $sum=array_sum($data['procent']);
    $this->pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'round', 'join' => 'round'));
    $angleStart = 0;
    $angleEnd = 0;
    $angle=0;
    $this->pdf->SetDrawColor(255,255,255);
    foreach($data['procent'] as $key=>$val) {
      $angle = floor(($val * 360) / doubleval($sum));
      if ($angle != 0) {
        $angleEnd = $angleStart + $angle;
        $this->pdf->SetFillColor($data['kleur'][$key][0],$data['kleur'][$key][1],$data['kleur'][$key][2]);
        $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
        $angleStart += $angle;
      }
      
    }
    if ($angleEnd != 360) {
      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    }
    
        //Legends
        $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    

       $omschrijvingWidthTotal=0;
       foreach($data['omschrijving'] as $key=>$omschrijving)
			 {
         $omschrijvingWidthTotal+=$this->pdf->getStringWidth($omschrijving)+6;
       }
       $xCorrectie=$radius-($omschrijvingWidthTotal/2);
  //echo "$xCorrectie=$radius-$omschrijvingWidthTotal;";exit;
    $x1 = $XPage+$xCorrectie ;
    $x2 = $x1 + $margin;
    $y1 = $YDiag + ($radius) + $margin *2;
    
       foreach($data['omschrijving'] as $key=>$omschrijving)
			 {
         $omschrijvingWidth=$this->pdf->getStringWidth($omschrijving)+6;
          $this->pdf->SetFillColor($data['kleur'][$key][0],$data['kleur'][$key][1],$data['kleur'][$key][2]);
          $this->pdf->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1);
          $this->pdf->Cell(0,$hLegend,$omschrijving);
         $x1+=$omschrijvingWidth;
         $x2+=$omschrijvingWidth;
       }
    $this->pdf->SetLineWidth(0.3);
  }
}
?>

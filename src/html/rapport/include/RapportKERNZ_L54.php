<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");
include_once($__appvar["basedir"]."/html/rapport/CorrelatieStdevClass.php");

class RapportKERNZ_L54
{
	function RapportKERNZ_L54($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "KERNZ";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->portefeuille=$portefeuille;
    $this->rapportageDatumVanaf=$rapportageDatumVanaf;
    $this->rapportageDatum=$rapportageDatum;
		$this->pdf->rapport_titel = "Standaarddeviatie per instrument";

	}

	function formatGetal($waarde, $dec)
	{
		if($waarde==0)
			return '';
		else
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
          if (!isset($newDec) || $decimaal != '0')
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
  
  function getCorrelation($afmCategorieen)
	{
    $afmCategorieen=array_reverse($afmCategorieen);
    $afmCategorieen['totaal']='';
    $afmCategorieen=array_reverse($afmCategorieen);
    
    $aantalJaar=5;
    if(isset($_POST['RISK_jaren']) && $_POST['RISK_jaren']>0)
      $aantalJaar=$_POST['RISK_jaren'];
    
    $dev=new correlatieStdev($this->portefeuille,$this->rapportageDatum);
    $dev->bepaalPortefeuilleVerdeling($this->rapportageDatum,'afmCategorie');
    $dev->bepaalPeriode($aantalJaar);
    $dev->getKoersen();
    $dev->bepaalCorrelatieMatrix();
    $dev->berekenVariantie();

    $xlsData = array();
    $standaardDeviatiePerFonds=array();
    $correlatiePerFonds=array();
    $correlatiePerFondsInCategorie=array();
    
    foreach($afmCategorieen as $categorie)
    {
      $dev->berekenVariantie($this->rapportageDatum, $categorie);
      $datum = $dev->rapportageDatum;
      
      $xlsData[] = array('Verdeling op ' . $datum.' '.$categorie);
      $xlsData[] = array('Fonds', 'Percentage');

      if($categorie=='')
      {

        foreach ($dev->verdeling[$datum] as $component => $percentage)
        {
          $xlsData[] = array($component, $percentage * 100);
        }
     
      }
      else
      {
        $itemsInArray=$dev->verdelingCategorie[$datum][$categorie];
        foreach ($dev->verdelingCategorie[$datum][$categorie] as $component => $percentage)
        {
          $tmp=array();
          $xlsData[] = array($component, $percentage * 100);
          foreach($itemsInArray as $component2=>$percentage2)
          {
            if($component<>$component2)
              $tmp[] = $dev->correlatieMatrix[$component][$component2];
          }
          $correlatiePerFondsInCategorie[$component]=(array_sum(array_values($tmp)))/(count($tmp));
        }
       
      }
      
      $xlsData[] = array('');
      if($categorie=='')
      {
        $xlsData[] = array('');
        $xlsData[] = array('Fondsrendement stdev');
        $xlsData[] = array('Fonds', 'stdev');
        foreach ($dev->componenten as $component)
        {
          $xlsData[] = array($component, $dev->fondsStandaardDeviatie[$component]);
          $standaardDeviatiePerFonds[$component]=$dev->fondsStandaardDeviatie[$component];
        }
        
        $xlsData[] = array('');
        $xlsData[] = array('Correlatie matrix');
        $header = array('Fonds');
        foreach ($dev->componenten as $component)
        {
          $header[] = $component;
        }
        $xlsData[] = $header;
        foreach ($dev->correlatieMatrix as $component1 => $componentData2)
        {
          $row = array($component1);
          $correlatiePerFonds[$component1]=(array_sum(array_values($componentData2))-1)/(count($componentData2)-1);
          foreach ($componentData2 as $component2 => $correlatie)
          {
            $row[] = $correlatie;
          }
          $xlsData[] = $row;
        }
      }
      
      if($categorie=='')
      {
        
        $categorie='totaal';
      }
      $xlsData[] = array('');
      $xlsData[] = array('var', $dev->var[$categorie][$datum]);
      $xlsData[] = array('stdev', $dev->std[$categorie][$datum]);
      
      $xlsData[] = array('');
      $xlsData[] = array('var berekening');
      foreach ($dev->debugArray as $row)
      {
        $xlsData[] = array($row);
      }
      $xlsData[] = array('');
    }
    
    $this->pdf->excelData = $xlsData;

    return array('correlatiePerFonds'=>$correlatiePerFonds,'standaardDeviatiePerFonds'=>$standaardDeviatiePerFonds,'correlatiePerFondsInCategorie'=>$correlatiePerFondsInCategorie);

	}
  
	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
		$fondsresultwidth = 17;
		$omschrijvingExtra = 10;
    
		$query="SELECT Vermogensbeheerders.VerouderdeKoersDagen FROM Vermogensbeheerders Inner Join Portefeuilles ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder WHERE portefeuille = '".$this->portefeuille."' ";
		$DB->SQL($query);
		$DB->Query();
		$dagen = $DB->nextRecord();
    $maxDagenOud=$dagen['VerouderdeKoersDagen'];

		$this->pdf->widthA = array(20,75,15,21,25,25,30,25,35,22,23,17);
		$this->pdf->alignA = array('R','L','R','R','R','R','R','R','R','R','R','R','R','R');
		$this->pdf->underlinePercentage=0.8;

		$this->pdf->AddPage();

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
    
    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
    
   
    
    $query = "SELECT TijdelijkeRapportage.type, TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.afmCategorie,".
      " TijdelijkeRapportage.fonds, ".
      " TijdelijkeRapportage.actueleValuta, ".
      " TijdelijkeRapportage.hoofdcategorieOmschrijving, ".
      " TijdelijkeRapportage.beleggingscategorieOmschrijving, ".
      " TijdelijkeRapportage.afmCategorieOmschrijving, ".
      " TijdelijkeRapportage.Valuta, ".
      " TijdelijkeRapportage.totaalAantal as totaalAantal, ".
      " TijdelijkeRapportage.beginwaardeLopendeJaar, ".
      " TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
       "IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin.") as beginPortefeuilleWaardeEuro,".
      " TijdelijkeRapportage.actueleFonds,
        round((UNIX_TIMESTAMP(TijdelijkeRapportage.rapportageDatum) - UNIX_TIMESTAMP(TijdelijkeRapportage.koersDatum))/86400) as koersLeeftijd,
				TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				  TijdelijkeRapportage.beleggingscategorie,
				  TijdelijkeRapportage.valuta,
				   TijdelijkeRapportage.portefeuille ".
      " FROM TijdelijkeRapportage
           WHERE ".
      " TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
      " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " ORDER BY hoofdcategorieVolgorde, TijdelijkeRapportage.beleggingscategorieVolgorde, afmCategorieVolgorde asc, TijdelijkeRapportage.afmCategorie, TijdelijkeRapportage.fondsOmschrijving";


		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
    $renteTotaal=array();
    $dataset=array();
	 	while($row = $DB->NextRecord())
    {
      $dataset[$row['beleggingscategorieOmschrijving']][$row['afmCategorieOmschrijving']][]=$row;
      $afmCategorieen[$row['afmCategorie']]=$row['afmCategorie'];
    }
   
    $correlatiedata=$this->getCorrelation($afmCategorieen);
    
    $afmTotalen=array();
    $beleggingscategorieTotaal=array();
    $totaleWaarde=array();
    foreach($dataset as $beleggingscategorie=>$beleggingscategorieData)
    {
      $this->pdf->SetFont($this->pdf->rapport_font,"BI",$this->pdf->rapport_fontsize);
    	$this->pdf->Cell(100,$this->pdf->rowHeight,$beleggingscategorie,0,1,'L');
				//$this->pdf-> Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
      foreach ($beleggingscategorieData as $afmcategorie => $categorieData)
      {
        $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
        $this->pdf->Cell(100,$this->pdf->rowHeight,$afmcategorie,0,1,'L');
        $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
      	foreach($categorieData as $row)
        {
          $percentageVanTotaal = $row['actuelePortefeuilleWaardeEuro'] / $totaalWaarde * 100;
          $afmTotalen['actuelePortefeuilleWaardeEuro']+=$row['actuelePortefeuilleWaardeEuro'];
          $afmTotalen['percentageVanTotaal']+=$percentageVanTotaal;
          $beleggingscategorieTotaal['actuelePortefeuilleWaardeEuro']+=$row['actuelePortefeuilleWaardeEuro'];
          $beleggingscategorieTotaal['percentageVanTotaal']+=$percentageVanTotaal;
          $totaleWaarde['actuelePortefeuilleWaardeEuro']+=$row['actuelePortefeuilleWaardeEuro'];
          $totaleWaarde['percentageVanTotaal']+=$percentageVanTotaal;
  
          if ($row['type'] == 'rente')
          {
            $renteTotaal['actuelePortefeuilleWaardeEuro'] += $row['actuelePortefeuilleWaardeEuro'];
            continue;
          }
          if ($row['type'] == 'rekening')
          {
            $fonds = $row['valuta'];
          }
          else
          {
            $fonds = $row['fonds'];
          }
  
          $this->pdf->row(array($this->formatAantal($row['totaalAantal'], $this->pdf->rapport_VOLK_aantal_decimaal, $this->pdf->rapport_VOLK_aantalVierDecimaal),
                            $row['fondsOmschrijving'],
                            $row['Valuta'],
                            $this->formatGetal($row['actueleFonds'], 2),
                            $this->formatGetal($row['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),
                            $this->formatGetal($percentageVanTotaal, $this->pdf->rapport_VOLK_decimaal_proc),
                            $this->formatGetal($correlatiedata['standaardDeviatiePerFonds'][$fonds], 2),
                            $this->formatGetal($correlatiedata['correlatiePerFonds'][$fonds], 2),
                            $this->formatGetal($correlatiedata['correlatiePerFondsInCategorie'][$fonds], 2),
                          ));
          
        }
        if($renteTotaal['actuelePortefeuilleWaardeEuro']<>0)
				{
          $this->pdf->row(array('','Opgelopen rente','','',
                            $this->formatGetal($renteTotaal['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),
                            $this->formatGetal($renteTotaal['actuelePortefeuilleWaardeEuro']/$totaalWaarde*100, $this->pdf->rapport_VOLK_decimaal_proc),
                          ));
          $renteTotaal=array();
				}
        $this->pdf->aligns[1]='R';
      	$this->pdf->CellBorders=array('','','','','TS','TS');
        $this->pdf->row(array('','Subtotaal '.$afmcategorie,'','',
                          $this->formatGetal($afmTotalen['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),
                          $this->formatGetal($afmTotalen['percentageVanTotaal'], $this->pdf->rapport_VOLK_decimaal_proc)));
        $this->pdf->aligns[1]='L';
        $afmTotalen=array();
        unset($this->pdf->CellBorders);
    
      }
      $this->pdf->ln();
      $this->pdf->aligns[1]='R';
      $this->pdf->CellBorders=array('','','','','SUB','SUB');
      $this->pdf->row(array('','Subtotaal '.$beleggingscategorie,'','',
                        $this->formatGetal($beleggingscategorieTotaal['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),
                        $this->formatGetal($beleggingscategorieTotaal['percentageVanTotaal'], $this->pdf->rapport_VOLK_decimaal_proc)));
      $this->pdf->aligns[1]='L';
      $beleggingscategorieTotaal=array();
      unset($this->pdf->CellBorders);
      $this->pdf->ln();
    }
    $this->pdf->ln();
    $this->pdf->aligns[1]='R';
    $this->pdf->CellBorders=array('','','','',array('TS','UU'),array('TS','UU'));
    $this->pdf->row(array('','Totale waarde','','',
                      $this->formatGetal($totaleWaarde['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),
                      $this->formatGetal($totaleWaarde['percentageVanTotaal'], $this->pdf->rapport_VOLK_decimaal_proc)));
    $this->pdf->aligns[1]='L';
    $beleggingscategorieTotaal=array();
    unset($this->pdf->CellBorders);
    $this->pdf->ln();

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
}
?>
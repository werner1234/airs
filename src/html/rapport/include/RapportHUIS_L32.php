<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportHUIS_L32
{
	function RapportHUIS_L32($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "KERNV";
	  $this->pdf->rapport_datum = db2jul($rapportageDatum);
	  $this->pdf->rapport_titel = "Verdeling totaalvermogen";//Onderverdeling in beleggingscategorie";

	  $this->portefeuille = $portefeuille;
	  $this->rapportageDatumVanaf = $rapportageDatumVanaf;
	  $this->rapportageDatum = $rapportageDatum;
	  $this->pdf->pieData = array();
	  $this->index=new indexHerberekening();
    $this->pdf->underlinePercentage=0.8;
    $startJul=db2jul($this->pdf->PortefeuilleStartdatum);
    $rapportageJul=db2jul($this->rapportageDatum);
    $rapportageJaar=date('Y',$rapportageJul);
    $drieJaarTerug=($rapportageJaar-3).'-01-01';
    $beginJaar=($rapportageJaar).'-01-01';
    $beginJaarJul=db2jul($beginJaar);
    
    if($startJul<db2jul($drieJaarTerug))
    {
      $startJul=db2jul($drieJaarTerug);
    }

    
    if($beginJaarJul>$startJul)
    {
      $this->perioden = $this->index->getJaren($startJul, $beginJaarJul-86400);
      
      $kwartalen=$this->index->getKwartalen($beginJaarJul, $rapportageJul);
    
      foreach($kwartalen as $index=>$periode)
      {
        if( substr($periode['stop'],5,5) <> '01-01')
          $this->perioden[] = $periode;
      }
    //  listarray($this->perioden);exit;
    }
    else
    {
      $this->perioden=$this->index->getKwartalen($startJul, $rapportageJul);
    }
    $this->perioden=array_reverse($this->perioden);
   
  
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

	
	function bepaaldFondsWaardenVerdiept($portefeuille,$einddatum)
  {
    $startjaar=true;
    /*
    $gegevens=berekenPortefeuilleWaarde($portefeuille,$einddatum,$startjaar,'EUR',substr($einddatum,0,4).'-01-01');
    listarray($gegevens);
    return $gegevens;
    */
    $this->verdiept = new portefeuilleVerdiept($this->pdf,$portefeuille,$einddatum);
    $verdiepteFondsen = $this->verdiept->getFondsen();
  //listarray($portefeuille);listarray($verdiepteFondsen);ob_flush();
    foreach ($verdiepteFondsen as $fonds)
      $this->verdiept->bepaalVerdeling($fonds,$this->verdiept->FondsPortefeuilleData[$fonds],array('fonds'),$einddatum);
  
   
    $fondswaarden =  berekenPortefeuilleWaarde($portefeuille,$einddatum,$startjaar,'EUR',substr($einddatum,0,4).'-01-01');
    $correctieVelden=array('totaalAantal','ActuelePortefeuilleWaardeEuro','actuelePortefeuilleWaardeInValuta','beginPortefeuilleWaardeEuro','beginPortefeuilleWaardeInValuta');
    foreach($fondswaarden as $i=>$fondsData)
    {
      //
      if(isset($this->pdf->fondsPortefeuille[$fondsData['fonds']]))
      {
      //echo $fondsData['fonds'];ob_flush();exit;
        $fondsWaardeEigen=$fondsData['actuelePortefeuilleWaardeEuro'];
        $fondsWaardeHuis=$this->pdf->fondsPortefeuille[$fondsData['fonds']]['totaalWaarde'];
        $aandeel=$fondsWaardeEigen/$fondsWaardeHuis;
        //echo $fondsData['fonds'].	" $aandeel=$fondsWaardeEigen/$fondsWaardeHuis ";exit;
        unset($fondswaarden[$i]);
        foreach($this->pdf->fondsPortefeuille[$fondsData['fonds']]['verdeling'] as $type=>$details)
        {
          foreach ($details as $element => $emementDetail)
          {
          
            if(isset($emementDetail['overige']))
            {
              foreach($correctieVelden as $veld)
                $emementDetail['overige'][$veld]=$emementDetail['overige'][$veld]*$aandeel;
              unset($emementDetail['overige']['WaardeEuro']);
              unset($emementDetail['overige']['koersLeeftijd']);
              unset($emementDetail['overige']['FondsOmschrijving']);
              unset($emementDetail['overige']['Fonds']);
              $fondswaarden[] = $emementDetail['overige'];
            }
          }
        }
      }
    }
    $fondswaarden  = array_values($fondswaarden);
    $tmp=array();
    $conversies=array('ActuelePortefeuilleWaardeEuro'=>'actuelePortefeuilleWaardeEuro');
    foreach($fondswaarden as $mixedInstrument)
    {
      $instrument=array();
      foreach($mixedInstrument as $index=>$value)
      {
        if(isset($conversies[$index]))
          $instrument[$conversies[$index]] = $value;
        else
          $instrument[$index] = $value;
      }
      unset($instrument['voorgaandejarenactief']);
    
      $key='|'.$instrument['type'].'|'.$instrument['fonds'].'|'.$instrument['rekening'].'|';
      if(isset($tmp[$key]))
      {
        foreach($correctieVelden as $veld)
        {
          $veld=($veld);
          $tmp[$key][$veld] += $instrument[$veld];
        }
      }
      else
        $tmp[$key]=$instrument;
      //	listarray($instrument);
    }
    $fondswaarden  = array_values($tmp);
    //echo $portefeuille,$einddatum;listarray($fondswaarden);
    return $fondswaarden;
  }

  function setTxtColor($bedrag)
  {
    if($bedrag<0)
      $this->pdf->SetTextColor(200,0,0);
    else
      $this->pdf->SetTextColor(0,0,0);
  }
  
  function addKop($kop)
  {
    $this->pdf->ln();
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), $this->pdf->w-$this->pdf->marge*2, 8 , 'F');
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font,'B', $this->pdf->rapport_fontsize);
    $this->pdf->row($kop);
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'', $this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor(240,240,240);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
  }
  
	function writeRapport()
	{
		global $__appvar;
		$query = "SELECT Selectieveld1 ,Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
  
	  $q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
	 	$DB->SQL($q);
   	$DB->Query();
  	$kleuren = $DB->LookupRecord();
  	$kleuren = unserialize($kleuren['grafiek_kleur']);
  	$this->pdf->grafiekKleuren=$kleuren;
    $this->categorieKleuren=$kleuren['OIB'];
    
    $selectieveldPortefeuille=array();
    $selectieveldEenPortefeuille=array();
    $clientVermogensbeheerder=array();
    if(is_array($this->pdf->portefeuilles))
    {
      $query="SELECT Portefeuille,ClientVermogensbeheerder,Selectieveld1,Selectieveld2  FROM Portefeuilles WHERE Portefeuille IN('".implode("','",$this->pdf->portefeuilles)."') ORDER BY ClientVermogensbeheerder,Selectieveld2,Selectieveld1,Portefeuille";
      $DB->SQL($query);
      $DB->Query();
      while($portefeuille = $DB->NextRecord())
      {
        $clientVermogensbeheerder[$portefeuille['Portefeuille']]=$portefeuille['ClientVermogensbeheerder'];//$this->getCRMnaam($portefeuille['Portefeuille']);
        $selectieveldPortefeuille[$portefeuille['Portefeuille']]=$portefeuille['Selectieveld2'];
        $selectieveldEenPortefeuille[$portefeuille['Portefeuille']]=$portefeuille['Selectieveld1'];
      }
    }
    else
    {
      $clientVermogensbeheerder[$this->portefeuille]=$portefeuilledata['ClientVermogensbeheerder'];
      $selectieveldPortefeuille[$this->portefeuille]=$portefeuilledata['Selectieveld2'];
    }
    asort($clientVermogensbeheerder);

    //listarray($this->pdf->portefeuilles);
    $portefeuilleWaarden=array();
    $categorieOmschrijving=array();
    $query="SELECT beleggingscategorie,omschrijving FROM Beleggingscategorien";
    $DB->SQL($query);
    $DB->Query();
    while($data = $DB->NextRecord())
    {
      $categorieOmschrijving[$data['beleggingscategorie']]=$data['omschrijving'];
    }
    
    $query="SELECT
CategorienPerHoofdcategorie.Hoofdcategorie,
CategorienPerHoofdcategorie.Beleggingscategorie,
hcat.Omschrijving AS hcatOmschrijving,
hcat.Afdrukvolgorde AS hcatVolgorde,
KeuzePerVermogensbeheerder.Afdrukvolgorde AS catVolgorde,
Beleggingscategorien.Omschrijving as catOmschrijving
FROM
CategorienPerHoofdcategorie
INNER JOIN Beleggingscategorien AS hcat ON CategorienPerHoofdcategorie.Hoofdcategorie = hcat.Beleggingscategorie
INNER JOIN KeuzePerVermogensbeheerder ON CategorienPerHoofdcategorie.Beleggingscategorie = KeuzePerVermogensbeheerder.waarde AND CategorienPerHoofdcategorie.Vermogensbeheerder = KeuzePerVermogensbeheerder.vermogensbeheerder AND KeuzePerVermogensbeheerder.categorie = 'Beleggingscategorien'
INNER JOIN Beleggingscategorien ON CategorienPerHoofdcategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
WHERE CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY hcatVolgorde, catVolgorde";
    $DB->SQL($query);
    $DB->Query();
    while($data = $DB->NextRecord())
    {
      $portefeuilleWaarden[$data['Hoofdcategorie']][$data['Beleggingscategorie']]=array();
    }
    
		
    $totaalWaarde=array();
    foreach($this->perioden as $periode)
    {
        
        foreach ($clientVermogensbeheerder as $portefeuille => $client)
        {
          if ($this->pdf->lastPOST['doorkijk'] == 1)
          {
            vulTijdelijkeTabel(berekenPortefeuilleWaarde($portefeuille, $periode['stop']), $portefeuille, $periode['stop']);
            $gegevens = $this->bepaaldFondsWaardenVerdiept($portefeuille, $periode['stop']);
          }
          else
          {
            $gegevens = berekenPortefeuilleWaarde($portefeuille, $periode['stop']);
          }
      
          foreach ($gegevens as $waarde)
          {
            if($selectieveldPortefeuille[$portefeuille]=='Effecten')
            {
              $waarde['hoofdcategorie']='H-Effect';
              $waarde['beleggingscategorie']='Effecten';
              $omschrijving=($selectieveldEenPortefeuille[$portefeuille]<>''?$selectieveldEenPortefeuille[$portefeuille]:$portefeuille);
            }
            else
            {
              if ($waarde['hoofdcategorie'] == '')
              {
                $waarde['hoofdcategorie'] = 'GeenCategorie';
                $waarde['hoofdcategorieOmschrijving'] = 'Geen categorie';
              }
              if ($waarde['fondsOmschrijving'] == '')
              {
                $omschrijving = $waarde['rekening'];
              }
              else
              {
                $omschrijving = $waarde['fondsOmschrijving'];
              }
            }
            $portefeuilleWaarden[$waarde['hoofdcategorie']][$waarde['beleggingscategorie']][$omschrijving][$periode['stop']] += $waarde['actuelePortefeuilleWaardeEuro'];
            $totaalWaarde[$periode['stop']] += $waarde['actuelePortefeuilleWaardeEuro'];
          }
        }
    }
    
    
    foreach($portefeuilleWaarden as $hcat=>$catdata)
    {
      foreach($catdata as $cat=>$fondsData)
      {
        if(count($fondsData)==0)
          unset($portefeuilleWaarden[$hcat][$cat]);
      }
    }
    
   
    $jaarUltimo=array();
    $kop=array('Bezittingen','');
    foreach($this->perioden as $periode)
    {
      $kop[] = date('d-m-Y',db2jul($periode['stop']));
      $kop[] = '';
      $jaarUltimo[] = $periode['stop'];
    }
    
    $aantalCategorieen=count($jaarUltimo);
    if($aantalCategorieen<6)
      $aantalCategorieen=6;
    $maxWidth=297-63-$this->pdf->marge*2;
    
    $pw=2;
    $eurw=($maxWidth-$pw*$aantalCategorieen)/$aantalCategorieen;
    
    $this->pdf->widthA = array(60+3);
    $this->pdf->alignA = array('L');
    $subUnderline=array('','');
    $totaalUnderline=array('','');
    for($i=1;$i<25;$i++)
    {
      $this->pdf->widthA[]=$pw;
      $this->pdf->widthA[]=$eurw;
      $this->pdf->alignA[]='R';
      $subUnderline[]='TS';
      $subUnderline[]='';
      $totaalUnderline[]='SUB';
      $totaalUnderline[]='';
    }
		// voor data
  
		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    


		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		
    $this->addKop($kop);
    
    
    
    foreach($portefeuilleWaarden as $hoofdCategorie=>$categorieData)
    {
      if(count($categorieData)==0)
        continue;
      $hoofdcategorieTotaal=array();
      $hoofdcategorieTotaalRow=array((isset($categorieOmschrijving[$hoofdCategorie])?$categorieOmschrijving[$hoofdCategorie]:$hoofdCategorie),'');
      $this->pdf->SetFont($this->pdf->rapport_font,'BI', $this->pdf->rapport_fontsize);
      //$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
      $this->pdf->row($hoofdcategorieTotaalRow);
      $this->pdf->SetFont($this->pdf->rapport_font,'', $this->pdf->rapport_fontsize);
      
      foreach ($categorieData as $categorie => $fondsRegels)
      {
        $categorieTotaal=array();
        $categorieTotaalRow=array((isset($categorieOmschrijving[$categorie])?$categorieOmschrijving[$categorie]:$categorie),'');
        $this->pdf->SetFont($this->pdf->rapport_font,'B', $this->pdf->rapport_fontsize);
        $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
        $this->pdf->row($categorieTotaalRow);//KOP
        $this->pdf->SetFont($this->pdf->rapport_font,'', $this->pdf->rapport_fontsize);
       // $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
        $n=0;
        foreach ($fondsRegels as $fondsOmschrijving=>$periodeData)
        {
          $row = array($fondsOmschrijving, '');
          foreach ($this->perioden as $periode)
          {
              $row[] = $this->formatGetal($periodeData[$periode['stop']], 0);
              $row[] = '';
              $categorieTotaal[$periode['stop']]+=$periodeData[$periode['stop']];
              $hoofdcategorieTotaal[$periode['stop']]+=$periodeData[$periode['stop']];
          }
          if($n%2==0)
            $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
          else
            unset($this->pdf->fillCell);
          $n++;
          $this->pdf->row($row);
          
          if($this->pdf->getY()+10>$this->pdf->PageBreakTrigger)
          {
            $this->pdf->addPage();
            $this->addKop($kop);
          }
        }
        $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
        unset($this->pdf->fillCell);
       
        foreach ($this->perioden as $periode)
        {
          $categorieTotaalRow[] = $this->formatGetal($categorieTotaal[$periode['stop']], 0);
          $categorieTotaalRow[] = '';
        }
        $this->pdf->SetFont($this->pdf->rapport_font,'B', $this->pdf->rapport_fontsize);
        $this->pdf->CellBorders = $subUnderline;
        $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
        $this->pdf->row($categorieTotaalRow);
        $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
        unset($this->pdf->CellBorders);
        $this->pdf->ln();
        $this->pdf->SetFont($this->pdf->rapport_font,'', $this->pdf->rapport_fontsize);
        //$totaalWaarde[$jaarTotalen]
      }
      
      foreach ($this->perioden as $periode)
      {
        $hoofdcategorieTotaalRow[] = $this->formatGetal($hoofdcategorieTotaal[$periode['stop']], 0);
        $hoofdcategorieTotaalRow[] = '';
      }
      $this->pdf->SetFont($this->pdf->rapport_font,'BI', $this->pdf->rapport_fontsize);
      $this->pdf->CellBorders =$totaalUnderline;
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
      $this->pdf->row($hoofdcategorieTotaalRow);
      $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
      unset($this->pdf->CellBorders);
      $this->pdf->ln();
      $this->pdf->SetFont($this->pdf->rapport_font,'', $this->pdf->rapport_fontsize);
    }
    $totaalRegel=array('Totaal','');
    foreach($totaalWaarde as $jaar=>$bedrag)
    {
      $totaalRegel[]=$this->formatGetal($bedrag,0);;
      $totaalRegel[]='';
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'B', $this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->row($totaalRegel);
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
//listarray($totaalWaarde);exit;


		$regelDataTotaal=array();
		$totaalPercentage=0;

 
    $this->setTxtColor(10);
    unset($this->pdf->CellFontStyle);
    
    
    
    
    
  }
  



}
?>

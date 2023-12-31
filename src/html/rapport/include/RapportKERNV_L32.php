<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportOIB_L32.php");

class RapportKERNV_L32
{
	function RapportKERNV_L32($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->oib = new RapportOIB_L32($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->pdf->rapport_type = "KERNV";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Verdeling totaalvermogen";//Onderverdeling in beleggingscategorie";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
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


	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}

	function getCRMnaam($portefeuille)
	{
		$db = new DB();
		if($this->pdf->checkRappNaam==true)
		{
			$extraVeld=',RappNaam';
		}
		$query="SELECT naam $extraVeld FROM CRM_naw WHERE portefeuille='$portefeuille'";
		$db->SQL($query);
		$crmData=$db->lookupRecord();
		$naamParts=explode('-',$crmData['naam'],2);
		$naam=trim($naamParts[1]);

		if($crmData['RappNaam'] <> '')
			return $crmData['RappNaam'] ;
		elseif($naam<>'')
			return $naam;
		else
			return $portefeuille;
	}

	function printTotaal($title, $totaalA, $procent, $grandtotaal)
	{
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2];

		if(!empty($totaalA))
		{
			if($this->pdf->rapport_OIB_specificatie == 1)
				$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[3],$this->pdf->GetY());
			$totaalAtxt = $this->formatGetalKoers($totaalA,$this->pdf->rapport_OIB_decimaal);
		}

		if(!empty($procent))
			$totaalprtxt = $this->formatGetal($procent,1);

		$this->pdf->SetX($actueel);

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_omschr_fontcolor['r'],$this->pdf->rapport_totaal_omschr_fontcolor['g'],$this->pdf->rapport_totaal_omschr_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_omschr_fontstyle,$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthB[3],4,$title, 0,0, "R");

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_fontcolor['r'],$this->pdf->rapport_totaal_fontcolor['g'],$this->pdf->rapport_totaal_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_fontstyle,$this->pdf->rapport_fontsize);


		$this->pdf->Cell($this->pdf->widthB[4],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[5],4,$totaalprtxt, 0,1, "R");
		

		if($grandtotaal)
		{

			$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[3],$this->pdf->GetY());
			$this->pdf->Line($actueel+2,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[3],$this->pdf->GetY()+1);
		}

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln(2);

		return $totaalA;
	}

	function printKop($title, $type="default")
	{
		switch($type)
		{
			case "b" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'b';
			break;
			case "bi" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'bi';
			break;
			case "i" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'i';
			break;
			default :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = '';
			break;
		}

		if(($this->pdf->GetY() + 12) >= $this->pdf->pagebreak) {
			$this->pdf->AddPage();
			$this->pdf->ln();
		}
		$this->pdf->SetFont($font,$fonttype,$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
		$this->pdf->SetX($this->pdf->marge);
		$y = $this->pdf->getY();


		  $this->pdf->MultiCell($this->pdf->widthB[0],4, $title, 0, "L");


	  $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
		$this->pdf->SetY($y);
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
  
  function header($categorieVolgorde,$categorieOmschrijving,$extraW)
  {
    $this->pdf->SetFont($this->pdf->rapport_font,'B', $this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_kop_fontstyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(255,255,255);
    $this->pdf->SetX($this->pdf->marge);
    $this->pdf->Cell(33, 6, vertaalTekst("Investering", $this->pdf->rapport_taal), 0, 0, "L",1);
    foreach($categorieVolgorde as $categorie=>$volgorde)
    {
      $this->pdf->Cell(2, 6, '', 0, 0, "C", 0);
      $this->pdf->Cell(35+$extraW, 6, $categorieOmschrijving[$categorie], 0, 0, "C", 1);
    }
    $this->pdf->Cell(2, 6, '', 0, 0, "C",0);
    $this->pdf->Cell(35+$extraW, 6, vertaalTekst('Totaal (�)',$this->pdf->rapport_taal), 0, 0, "C",1);
    $this->pdf->Cell(2, 6, '', 0, 0, "C",0);
    $this->pdf->Cell(35+$extraW, 6, vertaalTekst('Totaal (%)',$this->pdf->rapport_taal), 0, 0, "C",1);
    $this->pdf->Ln();
    $this->pdf->SetTextColor(0,0,0);
  
  }
  
  function setTxtColor($bedrag,$zwart=false)
  {
    if($bedrag<0)
      $this->pdf->SetTextColor(200,0,0);
    elseif($zwart==false)
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    else
     $this->pdf->SetTextColor(0,0,0);
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
    
    $this->selectieveldPortefeuille=array();
    $this->clientVermogensbeheerder=array();
    if(is_array($this->pdf->portefeuilles))
    {
      $query="SELECT Portefeuille,ClientVermogensbeheerder,Selectieveld1  FROM Portefeuilles WHERE Portefeuille IN('".implode("','",$this->pdf->portefeuilles)."') ORDER BY ClientVermogensbeheerder,Selectieveld1,Portefeuille";
      $DB->SQL($query);
      $DB->Query();
      while($portefeuille = $DB->NextRecord())
      {
        $this->clientVermogensbeheerder[$portefeuille['Portefeuille']]=$portefeuille['ClientVermogensbeheerder'];//$this->getCRMnaam($portefeuille['Portefeuille']);
        $this->selectieveldPortefeuille[$portefeuille['Portefeuille']]=$portefeuille['Selectieveld1'];
      }
    }
    else
    {
      $this->clientVermogensbeheerder[$this->portefeuille]=$portefeuilledata['ClientVermogensbeheerder'];
      $this->selectieveldPortefeuille[$this->portefeuille]=$portefeuilledata['Selectieveld1'];
    }
    asort($this->clientVermogensbeheerder);
    

		if(is_array($this->pdf->portefeuilles))
			$consolidatie=true;
		else
			$consolidatie=false;
    //listarray($this->pdf->portefeuilles);
    $portefeuilleWaarden=array();
    $categorieOmschrijving=array();
		$aantalPortefeuilles=0;
    $totaalWaarde=0;
    if($consolidatie)
    {
			$aantalPortefeuilles=count($this->pdf->portefeuilles);
      foreach($this->clientVermogensbeheerder as $portefeuille=>$client)
      {
        $portefeuilleWaarden[$portefeuille]['belCatWaarde']=array();
        if($this->pdf->lastPOST['doorkijk']==1)
				{
					vulTijdelijkeTabel(berekenPortefeuilleWaarde($portefeuille,$this->rapportageDatum), $portefeuille, $this->rapportageDatum);
					$gegevens = $this->bepaaldFondsWaardenVerdiept($portefeuille, $this->rapportageDatum);
				}
        else
          $gegevens=berekenPortefeuilleWaarde($portefeuille,$this->rapportageDatum);
        
        foreach($gegevens as $waarde)
        {
					if($waarde['hoofdcategorie']=='')
					{
						$waarde['hoofdcategorie']='GeenCategorie';
						$waarde['hoofdcategorieOmschrijving']='Geen categorie';
					}
          $portefeuilleWaarden[$portefeuille]['belCatWaarde'][$waarde['hoofdcategorie']]+=$waarde['actuelePortefeuilleWaardeEuro'];
          $portefeuilleWaarden[$portefeuille]['totaleWaarde']+=$waarde['actuelePortefeuilleWaardeEuro'];
          $categorieVolgorde[$waarde['hoofdcategorie']]=$waarde['hoofdcategorieVolgorde'];
          $categorieOmschrijving[$waarde['hoofdcategorie']]=$waarde['hoofdcategorieOmschrijving'];
          $totaalWaarde+=$waarde['actuelePortefeuilleWaardeEuro'];
  
         // echo "$portefeuille |  ".$waarde['fondsOmschrijving']. " ".$waarde['actuelePortefeuilleWaardeEuro']." -> $totaalWaarde <br>\n";
        }
        // echo "$portefeuille |  ". " ".$portefeuilleWaarden[$portefeuille]['totaleWaarde']." | $totaalWaarde <br>\n";
      }
      foreach($portefeuilleWaarden as $portefeuille=>$waarden)
      {
        foreach($waarden['belCatWaarde'] as $categorie=>$waardeEur)
        {
          $percentage=($waardeEur/$waarden['totaleWaarde']);
          $portefeuilleWaarden[$portefeuille]['belCatPercentage'][$categorie]=$percentage;
          $portefeuilleWaarden[$portefeuille]['totalePercentage']+=$percentage;
        }
      }
    }
    else
    {
      if($this->pdf->lastPOST['doorkijk']==1)
      {
        $gegevens = $this->bepaaldFondsWaardenVerdiept($this->portefeuille, $this->rapportageDatum);
        foreach($gegevens as $waarde)
        {
					if($waarde['beleggingscategorie']=='')
					{
						$waarde['hoofdcategorie']='GeenCategorie';
						$waarde['hoofdcategorieOmschrijving']='Geen categorie';
					}
          $portefeuilleWaarden[$this->portefeuille]['belCatWaarde'][$waarde['hoofdcategorie']]+=$waarde['actuelePortefeuilleWaardeEuro'];
          $portefeuilleWaarden[$this->portefeuille]['totaleWaarde']+=$waarde['actuelePortefeuilleWaardeEuro'];
					$categorieVolgorde[$waarde['hoofdcategorie']]=$waarde['hoofdcategorieVolgorde'];
          $categorieOmschrijving[$waarde['hoofdcategorie']]=$waarde['hoofdcategorieOmschrijving'];
          $totaalWaarde+=$waarde['actuelePortefeuilleWaardeEuro'];
        }
        foreach($portefeuilleWaarden[$this->portefeuille]['belCatWaarde'] as $categorie=>$waardeEur)
        {
          $percentage=($waardeEur/$portefeuilleWaarden[$this->portefeuille]['totaleWaarde']);
          $portefeuilleWaarden[$this->portefeuille]['belCatPercentage'][$categorie]=$percentage;
          $portefeuilleWaarden[$this->portefeuille]['totalePercentage']+=$percentage;
        }
  
      }
      else
      {
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
      $portefeuilleWaarden[$this->portefeuille]['totaleWaarde']=$totaalWaarde;
      

		  $query = "SELECT TijdelijkeRapportage.hoofdcategorieOmschrijving as Omschrijving, TijdelijkeRapportage.hoofdcategorieVolgorde, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.actueleValuta, TijdelijkeRapportage.hoofdcategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS actuelePortefeuilleWaardeEuro ".
			" FROM TijdelijkeRapportage ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.hoofdcategorie".
			" ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde asc";
		  debugSpecial($query,__FILE__,__LINE__);

		  $DB->SQL($query);
		  $DB->Query();

  		while($categorien = $DB->NextRecord())
	  	{
	  	  if($categorien['hoofdcategorie']=='')
        {
          $categorien['hoofdcategorie']='GeenCategorie';
          $categorien['Omschrijving']='Geen categorie';
        }
         $categorieOmschrijving[$categorien['hoofdcategorie']]=$categorien['Omschrijving'];
         $categorieVolgorde[$categorien['hoofdcategorie']]=$categorien['hoofdcategorieVolgorde'];
         $portefeuilleWaarden[$this->portefeuille]['belCatWaarde'][$categorien['hoofdcategorie']]+=$categorien['actuelePortefeuilleWaardeEuro'];
         $percentage=($categorien['actuelePortefeuilleWaardeEuro']/$totaalWaarde);
         $portefeuilleWaarden[$this->portefeuille]['belCatPercentage'][$categorien['hoofdcategorie']]=$percentage;
         $portefeuilleWaarden[$this->portefeuille]['totalePercentage']+=$percentage;
      }
      }
    }
    
    //foreach($this->clientVermogensbeheerder as $portefeuille=>$client)
    //  $portefeuilleVolgorde[$client]=array();
    foreach ($portefeuilleWaarden as $portefeuille=>$pdata)
    {
      if (isset($this->selectieveldPortefeuille[$portefeuille]) && $this->selectieveldPortefeuille[$portefeuille] <> '')
      {
        $portefeuilleTxt = $this->selectieveldPortefeuille[$portefeuille];
      }
      else
      {
        $portefeuilleTxt = $portefeuille;
      }
      $portefeuilleVolgorde[$portefeuille]=$portefeuilleTxt;
    }
    asort($portefeuilleVolgorde ,SORT_STRING  );
    
    $portefeuilleWaardenSort=array();
    foreach($portefeuilleVolgorde as $portefeuille=>$omschrijving)
    {
      $portefeuilleWaardenSort[$this->clientVermogensbeheerder[$portefeuille]][$portefeuille]=$portefeuilleWaarden[$portefeuille];
    }
    ksort($portefeuilleWaardenSort ,SORT_STRING  );
   // listarray($portefeuilleWaardenSort);
  //2+35+extraw
    
    $aantalCategorieen=count($categorieVolgorde);
    $paginaWidth=(30+3)+(35+2)*($aantalCategorieen+2);
    $maxWidth=297-$this->pdf->marge*2;
    $extraRuimte=$maxWidth-$paginaWidth;
    //echo $paginaWidth." ";
    
    $maxPortefeuilles=400;
    $extraW=$extraRuimte/($aantalCategorieen+2);
    //echo $extraW;exit;
		// voor kopjes
		$pw=14;
    $eurw=5;
		$portw=23;
		

		$this->pdf->widthA = array(30+3,$eurw,$portw,$pw,$eurw,$portw,$pw,$eurw,$portw,$pw,$eurw,$portw,$pw,$eurw,$portw,$pw,$eurw,$portw,$pw,$eurw,$portw,$pw);
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
		// voor data
  
		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    

//listarray($this->pdf->clientVermogensbeheerder);exit;

  //  if(is_array($this->pdf->portefeuilles))
  //  {


		//  }
		// print categorie headers
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
 		$this->pdf->SetFont($this->pdf->rapport_font,'', $this->pdf->rapport_fontsize);

		asort ($categorieVolgorde);

		$regelDataTotaal=array();
		$totaalPercentage=0;

  	foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
      $regelDataTotaal[$portefeuille]=array('waarde'=>$belCatData['totaleWaarde'],'percentage'=>$belCatData['totalePercentage']*100);
    if($consolidatie)
      $regelDataTotaal['Totaal']=array('waarde'=>$totaalWaarde,'percentage'=>$totaalPercentage*100);

    $portefeuilleAantal=count($portefeuilleWaarden);
   // echo $portefeuilleAantal."<br>\n";listarray($portefeuilleWaarden);exit;
    
    $portrefeuilleDataPerBlok=array();
    $i=0;
		$blokken=ceil($portefeuilleAantal/$maxPortefeuilles);
		$n=1;
    foreach($this->clientVermogensbeheerder as $portefeuille=>$ClientVermogensbeheerder)
    {
      $belCatData=$portefeuilleWaarden[$portefeuille];
      $portrefeuilleDataPerBlok[$i][$portefeuille]=$belCatData;
      if($n%$maxPortefeuilles==0)
        $i++;
      $n++;
    }
		
  
		for($i=0;$i<$blokken;$i++)
		{
      $portefeuilleWaarden= $portrefeuilleDataPerBlok[$i];
   
		  if($i>0)
		    $this->pdf->addPage();
			//Kop regel
			$regel = array();
  
			array_push($regel, 'Portefeuille');
	  

			$this->pdf->SetWidths($this->pdf->widthB);
			$this->pdf->SetAligns($this->pdf->alignB);
      
      $barGraph=false;

      if(1)//count($this->pdf->portefeuilles) < 60)
      {

        $this->header($categorieVolgorde,$categorieOmschrijving,$extraW);
        $tmp=array( vertaalTekst('Beleggingscategorie', $this->pdf->rapport_taal), vertaalTekst('Totaal waarde',$this->pdf->rapport_taal),vertaalTekst('Totaal',$this->pdf->rapport_taal)." %");
        $categorieTotalen=array();
        $clientTotalen=array();
        $lastClient='';
        foreach($portefeuilleWaardenSort as $clientSort=>$portefeuilleWaarden)
        {
          foreach ($portefeuilleWaarden as $portefeuille => $pdata)
          {
            $this->setTxtColor(10);
            if ($this->pdf->getY() > 170)
            {
              $this->pdf->addPage();
              $this->header($categorieVolgorde, $categorieOmschrijving, $extraW);
            }
            $client = $this->clientVermogensbeheerder[$portefeuille];
            if ($client <> $lastClient && isset($clientTotalen[$lastClient]))
            {
              $this->clientTotaal($clientTotalen[$lastClient], $lastClient, $extraW);
              if ($this->pdf->getY() > 160)
              {
                $this->pdf->addPage();
                $this->header($categorieVolgorde, $categorieOmschrijving, $extraW);
              }
            }
    
            $this->pdf->SetX($this->pdf->marge);
            $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
            if (isset($this->selectieveldPortefeuille[$portefeuille]) && $this->selectieveldPortefeuille[$portefeuille] <> '')
            {
              $portefeuilleTxt = $this->selectieveldPortefeuille[$portefeuille];
            }
            else
            {
              $portefeuilleTxt = $portefeuille;
            }
            $this->pdf->Cell(33, 4, $portefeuilleTxt, 0, 0, "L", 0);
            $portWaarde = 0;
            foreach ($categorieVolgorde as $categorie => $volgorde)
            {
              $this->setTxtColor($pdata['belCatWaarde'][$categorie],false);
              $this->pdf->Cell(37 + $extraW, 4, $this->formatGetal($pdata['belCatWaarde'][$categorie], 0), 0, 0, "R");
              $categorieTotalen[$categorie] += $pdata['belCatWaarde'][$categorie];
              $portWaarde += $pdata['belCatWaarde'][$categorie];
      
              $clientTotalen[$client][$categorie] += $pdata['belCatWaarde'][$categorie];
      
            }
            $this->setTxtColor($portWaarde,false);
            $this->pdf->Cell(37 + $extraW, 4, $this->formatGetal($portWaarde, 0), 0, 0, "R");
            $this->setTxtColor($portWaarde / $totaalWaarde * 100,false);
            $this->pdf->Cell(37 + $extraW, 4, $this->formatGetal($portWaarde / $totaalWaarde * 100, 1) . ' %', 0, 1, "R");
            $this->setTxtColor(10);
            $clientTotalen[$client]['totaalWaarde'] += $portWaarde;
            $clientTotalen[$client]['totaalProcent'] += $portWaarde / $totaalWaarde * 100;
            $lastClient = $client;
          }
        }
      
        if(isset($clientTotalen[$lastClient]))
          $this->clientTotaal($clientTotalen[$lastClient],$lastClient,$extraW);
        $this->pdf->excelData[]=$tmp;
        $this->pdf->Ln(3);
        
        //$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
        $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
        $this->pdf->SetFont($this->pdf->rapport_font,'B', $this->pdf->rapport_fontsize);
        $this->pdf->Cell(33, 4,vertaalTekst('Totaal (�)',$this->pdf->rapport_taal), 0, 0, "L",0);
        foreach($categorieVolgorde as $categorie=>$volgorde)
        {
          $this->setTxtColor($categorieTotalen[$categorie]);
          $this->pdf->Cell(37+$extraW, 4,  $this->formatGetal($categorieTotalen[$categorie],0), 0, 0, "R");
        }
        $this->setTxtColor($totaalWaarde);
        $this->pdf->Cell(37+$extraW, 4,  $this->formatGetal($totaalWaarde,0), 0, 0, "R");
        $this->setTxtColor($totaalWaarde/$totaalWaarde*100);
        $this->pdf->Cell(37+$extraW, 4, $this->formatGetal($totaalWaarde/$totaalWaarde*100,1).' %', 0, 1, "R");
  
        $this->pdf->Ln(2);
        $this->pdf->Cell(33, 4,vertaalTekst('Totaal (%)',$this->pdf->rapport_taal), 0, 0, "L",0);
        foreach($categorieVolgorde as $categorie=>$volgorde)
        {
          $percentage=$categorieTotalen[$categorie]/$totaalWaarde*100;
          $this->setTxtColor($percentage);
          $this->pdf->Cell(37+$extraW, 4, $this->formatGetal($percentage,1).' %', 0, 0, "R");
  
          if($percentage<0)
            $barGraph=true;
          $categorieVerdeling['percentage'][$categorieOmschrijving[$categorie]]=$percentage;
          $categorieVerdeling['kleur'][]=array($this->pdf->grafiekKleuren['OIB'][$categorie]['R']['value'],$this->pdf->grafiekKleuren['OIB'][$categorie]['G']['value'],$this->pdf->grafiekKleuren['OIB'][$categorie]['B']['value']);
          $categorieVerdeling['kleurBar'][$categorieOmschrijving[$categorie]]=array($this->pdf->grafiekKleuren['OIB'][$categorie]['R']['value'],$this->pdf->grafiekKleuren['OIB'][$categorie]['G']['value'],$this->pdf->grafiekKleuren['OIB'][$categorie]['B']['value']);
  
          
        }
        $this->setTxtColor($totaalWaarde/$totaalWaarde*100);
        $this->pdf->Cell(37+$extraW, 4, $this->formatGetal($totaalWaarde/$totaalWaarde*100,1).' %', 0, 1, "R");
        
        
      }
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    //  $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
			
			
		}
    $this->setTxtColor(10);
    unset($this->pdf->CellFontStyle);
    
    
    //listarray($categorieVerdeling);
    if($this->pdf->getY() > 105 || $aantalPortefeuilles>15)
    {
     $this->pdf->addPage();
     $grafiekY=40;
    }
    else
    {
      $grafiekY = 120;
    }
    if($barGraph==false)
    {
      $this->pdf->setXY(20,$grafiekY);
      $this->PieChart(65, 65, $categorieVerdeling['percentage'], '%l (%p)',$categorieVerdeling['kleur']);
    }
    else
    {
      $this->pdf->setXY(50,$grafiekY);
      $this->BarDiagram(80, 100, $categorieVerdeling['percentage'], '%l (%p)',$categorieVerdeling['kleurBar']);//"Portefeuillewaarde � ".$this->formatGetal($this->portTotaal[$this->rapportageDatum],2)
    }
    
    if(isset($this->pdf->__appvar['consolidatie']))
    {
      $query = "SELECT
	            	if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam,Clienten.Naam) as Naam,
                if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam1,Clienten.Naam1) as Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Portefeuilles.Depotbank,
                Portefeuilles.PortefeuilleVoorzet,
                Portefeuilles.kleurcode,
                Accountmanagers.Naam as accountManager,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email,
                Depotbanken.Omschrijving as depotbankOmschrijving
		          FROM
		            Portefeuilles
		            LEFT JOIN Clienten ON Portefeuilles.Client = Clienten.Client
		            LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
		            LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
		            LEFT Join CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille
		            Join Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
		          WHERE
		            Portefeuilles.Portefeuille IN('".implode("','",$this->pdf->portefeuilles)."')
		            ORDER BY depotbankOmschrijving,Portefeuilles.Portefeuille";
      $DB->SQL($query);
      $DB->Query();
      while($tmp = $DB->nextRecord())
        $portefeuilledata[$tmp['Portefeuille']]=$tmp;
      
      $portefeuilleKleur=array();
      $portefeuilleKleurBar=array();
      $barGraph=false;
      
      $randomKleuren=array();
      foreach($this->pdf->grafiekKleuren['OIB'] as $categorie=>$kleur)
        $randomKleuren[]=array($kleur['R']['value'],$kleur['G']['value'],$kleur['B']['value']);
      $i=0;
      //listarray($clientTotalen);
      //listarray($portefeuilleWaarden);
      foreach ($clientTotalen as $portefeuille=>$waarde)
      {
        //listarray($waarde);
        $kleur=unserialize($portefeuilledata[$portefeuille]['kleurcode']);
        //$kleur=array();
        if($kleur[0]==0 && $kleur[1]==0 && $kleur[2]==0)
          $kleur = $randomKleuren[$i];
        
        if($kleur[0]==0 && $kleur[1]==0 && $kleur[2]==0)
          $kleur = array(rand(0, 255), rand(0, 255), rand(0, 255));
        
        $kop=($i+1).'.||'.$portefeuille;
        //	echo $kop."<br>\n";ob_flush();
        $portefeuilleAandeel[$kop]=$waarde['totaalWaarde']/$totaalWaarde*100;
        $portefeuilleKleur[]=$kleur;
        $portefeuilleKleurBar[$kop]=$kleur;
        if($waarde['totaalWaarde'] < 0)
          $barGraph=true;
        $i++;
      }
      
      $this->pdf->setY($grafiekY-10);
      $this->pdf->SetAligns(array('C','C'));
      $this->pdf->SetWidths(array(140,140));
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+4);
      $this->pdf->row(array(vertaalTekst("Verdeling over categorie�n",$this->pdf->rapport_taal),vertaalTekst("Verdeling over entiteit",$this->pdf->rapport_taal)));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      
      if($barGraph==false)
      {
        $this->pdf->setXY(160,$grafiekY);
        $this->PieChart(65, 65, $portefeuilleAandeel, '%l (%p)',$portefeuilleKleur);
      }
      else
      {
        $this->pdf->setXY(190,$grafiekY);
        $this->BarDiagram(80, 100, $portefeuilleAandeel, '%l (%p)',$portefeuilleKleurBar);//"Portefeuillewaarde � ".$this->formatGetal($this->portTotaal[$this->rapportageDatum],2)
      }
      
     
    }
    
    $this->oib->printBenchmarkvergelijking(true);
    
  }
  
  function clientTotaal($data,$client,$extraW)
  {
    unset($this->pdf->CellBorders);//=$topLine;
    $this->pdf->SetFont($this->pdf->rapport_font,'B', $this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->Cell(33, 4,vertaalTekst('Totaal',$this->pdf->rapport_taal).' '.$client, 0, 0, "L",0);
    foreach($data as $categorie=>$waarde)
    {
      //$this->setTxtColor($waarde);
      //$this->pdf->line($this->pdf->getX()+15,$this->pdf->getY(),$this->pdf->getX()+37+$extraW,$this->pdf->getY());
      if($categorie=='totaalProcent')
        $this->pdf->Cell(37+$extraW, 4, $this->formatGetal($waarde,1).' %', 0, 1, "R");
      else
        $this->pdf->Cell(37+$extraW, 4,  $this->formatGetal($waarde,0), 0, 0, "R");
      
    }
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'', $this->pdf->rapport_fontsize);
  }
  function SetLegends2($data, $format)
  {
    $this->pdf->legends=array();
    $this->pdf->wLegend=0;
    
    $this->pdf->sum=array_sum($data);
    $this->pdf->NbVal=count($data);
    foreach($data as $l=>$val)
    {
     // echo "$l <br>\n";
      $parts=explode('||',$l);
      $l=$parts[1];
      //$p=sprintf('%.1f',$val/$this->sum*100).'%';
      $p=sprintf('%.1f',$val).'%';
      $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
      //echo "$legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);<br>\n";
      $this->pdf->legends[]=$legend;
      $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->wLegend);
    }
  }
  
  function BarDiagram($w, $h, $data, $format,$colorArray,$titel)
  {
    
    $this->pdf->SetFont($this->rapport_font, '', $this->rapport_fontsize);
    $this->SetLegends2($data,$format);
   // listarray($this->pdf->legends);exit;
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $nbDiv=5;
    $legendWidth=10;
    $YDiag = $YPage;
    $hDiag = floor($h);
    $XDiag = $XPage +  $legendWidth;
    $lDiag = floor($w - $legendWidth);
    $color=array(155,155,155);
    $maxVal = max($data)*1.1;
    $minVal = min($data)*1.1;
    if($minVal > 0)
      $minVal=0;
    $maxVal=ceil($maxVal/10)*10;
    
    $offset=$minVal;
    $valIndRepere = ceil(round(($maxVal-$minVal) / $nbDiv,2)*100)/100;
    $bandBreedte = $valIndRepere * $nbDiv;
    $lRepere = floor($lDiag / $nbDiv);
    $unit = $lDiag / $bandBreedte;
    $hBar = 5;//floor($hDiag / ($this->pdf->NbVal + 1));
    $hDiag = $hBar * ($this->pdf->NbVal + 1);
    
    //echo "$hBar <br>\n";
    $eBaton = floor($hBar * 80 / 100);
    $legendaStep=$unit;
    
    $legendaStep=$unit/$nbDiv*$bandBreedte;
    //if($bandBreedte/$legendaStep > $nbDiv)
    //  $legendaStep=$legendaStep*5;
    // if($bandBreedte/$legendaStep > $nbDiv)
    //  $legendaStep=$legendaStep*2;
    // if($bandBreedte/$legendaStep > $nbDiv)
    //   $legendaStep=$legendaStep/2*5;
    $valIndRepere=round($valIndRepere/$unit/5)*5;
    
    
    $this->pdf->SetLineWidth(0.2);
    $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $nullijn=$XDiag - ($offset * $unit);
    
    $i=0;
    $nbDiv=10;
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 5);
    if(round($legendaStep,5) <> 0.0)
    {
      //for($x=$nullijn;$x<$XDiag; $x=$x-$legendaStep)
      for($x=$nullijn;$x>$XDiag; $x=$x-$legendaStep)
      {
        $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
        $this->pdf->setXY($x,$YDiag + $hDiag);
        $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,0),0,0,'C');
        $i++;
        if($i>100)
          break;
      }
      
      $i=0;
      //for($x=$nullijn;$x>($XDiag+$lDiag); $x=$x+$legendaStep)
      for($x=$nullijn;$x<($XDiag+$lDiag); $x=$x+$legendaStep)
      {
        $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
        $this->pdf->setXY($x,$YDiag + $hDiag);
        $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,0),0,0,'C');
        
        $i++;
        if($i>100)
          break;
      }
    }
    
    $i=0;
    
    $this->pdf->SetXY($XDiag-$legendWidth, $YDiag);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+4);
    $this->pdf->Cell($lDiag, -5, $titel,0,0,'C');
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);
//listarray($colorArray);listarray($data);
  
    
    foreach($data as $key=>$val)
    {
      $this->pdf->SetDrawColor($colorArray[$key][0],$colorArray[$key][1],$colorArray[$key][2]);
      $this->pdf->SetFillColor($colorArray[$key][0],$colorArray[$key][1],$colorArray[$key][2]);
      $xval = $nullijn;
      $lval = ($val * $unit);
      $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
      $hval = $eBaton;
      $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
      $this->pdf->SetXY($XPage, $yval);
      $this->pdf->Cell($legendWidth , $hval, $key." ".$this->pdf->legends[$i],0,0,'R');
      $i++;
    }
    
    //Scales
    $minPos=($minVal * $unit);
    $maxPos=($maxVal * $unit);
    
    $unit=($maxPos-$minPos)/$nbDiv;
    // echo "$minPos $maxPos -> $minVal $maxVal using $unit met null $nullijn";
    
    
  }
  
  function PieChart( $w, $h, $data, $format, $colors = null)
  {
    
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetLegends($data, $format);
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $hLegend = 2;
    $radius = min($w - $margin * 4 - $hLegend, $h - $margin * 2); //
    $radius = floor($radius / 2);
    $XDiag = $XPage + $margin + $radius;
    $YDiag = $YPage + $margin + $radius;
    if ($colors == null)
    {
      for ($i = 0; $i < $this->pdf->NbVal; $i++)
      {
        $gray = $i * intval(255 / $this->pdf->NbVal);
        $colors[$i] = array($gray, $gray, $gray);
      }
    }
    
    //Sectors
    $this->pdf->SetLineWidth(0.2);
    $angleStart = 0;
    $angleEnd = 0;
    $i = 0;
    $this->pdf->setDrawColor(255,255,255);
    foreach ($data as $val)
    {
      $angle = floor(($val * 360) / doubleval($this->pdf->sum));
      if ($angle != 0)
      {
        $angleEnd = $angleStart + $angle;
        $this->pdf->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
        $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
        $angleStart += $angle;
      }
      $i++;
    }
    if ($angleEnd != 360)
    {
      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    }
    $this->pdf->setDrawColor(0,0,0);
    //Legends
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
    $x1 = $XPage + $w + $radius * .5;
    $x2 = $x1 + $hLegend + $margin - 12;
    $y1 = $YDiag - ($radius) + $margin;

    for ($i = 0; $i < $this->pdf->NbVal; $i++)
    {
      $this->pdf->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
      $this->pdf->Rect($x1 - 12, $y1, $hLegend, $hLegend, 'DF');
      $this->pdf->SetXY($x2, $y1);
      
      if(strpos($this->pdf->legends[$i],'||')>0)
      {
        $parts=explode("||",$this->pdf->legends[$i]);
        $this->pdf->Cell(0, $hLegend, $parts[1]);
      }
      else
        $this->pdf->Cell(0, $hLegend, $this->pdf->legends[$i]);
      $y1 += $hLegend + $margin;
    }
  }



}
?>
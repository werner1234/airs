<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/06/08 05:43:48 $
 		File Versie					: $Revision: 1.18 $

 		$Log: RapportOIB_L77.php,v $
 		Revision 1.18  2020/06/08 05:43:48  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2020/06/06 15:48:23  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2019/09/14 17:09:05  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2019/02/23 18:32:59  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2018/12/07 11:57:08  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2018/12/06 17:51:24  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2018/11/22 07:25:26  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2018/10/24 16:00:59  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2018/10/20 18:05:20  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2018/10/17 15:37:17  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2018/10/13 17:18:13  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2018/10/10 15:50:56  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2018/10/08 06:36:49  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/10/07 10:19:56  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2018/10/06 17:20:57  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2018/09/26 15:53:28  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.1  2018/05/20 10:39:24  rvv
 		*** empty log message ***
 		

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportGRAFIEK_L25
{
	function RapportGRAFIEK_L25($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "GRAFIEK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titelKort = vertaalTekst("Vermogensverdeling",$this->pdf->rapport_taal);
		$this->pdf->rapport_titel = $this->pdf->rapport_titelKort;//." ".vertaalTekst("per",$this->pdf->rapport_taal)." ".date('d.m.Y',$this->pdf->rapport_datum);
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
    $this->tijdelijkeRapportageFilter='';
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
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

		$this->pdf->SetFont($font,$fonttype,$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor[r],$this->pdf->rapport_default_fontcolor[g],$this->pdf->rapport_default_fontcolor[b]);
	}
  
  
  function bepaalWegingPerFonds($fonds,$doorkijkSoort,$airsCategorie,$waarde,$rekening)
  {
    $db=new DB();
    $query="SELECT MAX(datumVanaf) as vanafDatum FROM doorkijk_categorieWegingenPerFonds
WHERE fonds='".mysql_real_escape_string($fonds)."' AND msCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND  datumVanaf <= '" . $this->rapportageDatum . "' ";
    $db->executeQuery($query);
    $vanafDatum=$db->nextRecord();//listarray($query);
    
    $query="SELECT msCategorie,weging FROM doorkijk_categorieWegingenPerFonds
WHERE fonds='".mysql_real_escape_string($fonds)."' AND msCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND datumVanaf = '" . $vanafDatum['vanafDatum']. "'  ";
    $db->executeQuery($query);
    $wegingPerMsCategorie=array();
    while ($row = $db->nextRecord())
    {
      $wegingPerMsCategorie[$row['msCategorie']] = $row['weging'];
      if($this->debug)
      {
        $this->debugData['MSfondsWeging'][$doorkijkSoort][$fonds][$row['msCategorie']]['weging'] = $row['weging'];
        $this->debugData['MSfondsWeging'][$doorkijkSoort][$fonds][$row['msCategorie']]['waarde'] = $waarde*$row['weging']/100;
      }
    }
//echo $query;
    $wegingDoorkijkCategorie=array();
    $airsKoppelingen=array('REGION_ZOTHERND','ZSECTOR_OTHERND');
    if(count($wegingPerMsCategorie)>0)
    {
      foreach($airsKoppelingen as $categorie)
      {
        if (isset($wegingPerMsCategorie[$categorie]))
        {
          $query = "SELECT doorkijkCategorie FROM doorkijk_koppelingPerVermogensbeheerder WHERE bronKoppeling='$airsCategorie' AND doorkijkCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND systeem='AIRS' AND vermogensbeheerder='". $this->pdf->portefeuilledata['Vermogensbeheerder']."'";
          $db->executeQuery($query);
          //		listarray($wegingPerMsCategorie);
          //		echo $wegingPerMsCategorie[$categorie]."| $fonds | $airsCategorie | $doorkijkSoort | $query<br>\n";
          
          if($db->records()>0)
          {
            
            while ($row = $db->nextRecord())
            {
              $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['weging'] = $wegingPerMsCategorie[$categorie];
              $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['waarde'] = $waarde * $wegingPerMsCategorie[$categorie] / 100;
              
              if ($this->debug)
              {
                $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['weging'] = 100;
                $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['waarde'] = $waarde;
              }
            }
            unset($wegingPerMsCategorie[$categorie]);
          }
        }
      }
      
      $msCategorienWhere=" bronKoppeling IN ('".implode("','",array_keys($wegingPerMsCategorie))."')";
      $query = "SELECT doorkijkCategorie,bronKoppeling as msCategorie FROM doorkijk_koppelingPerVermogensbeheerder
WHERE $msCategorienWhere AND doorkijkCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND systeem='MS' AND vermogensbeheerder='". $this->pdf->portefeuilledata['Vermogensbeheerder']."'";
      $db->executeQuery($query);
      
      while ($row = $db->nextRecord())
      {
        $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['weging'] += $wegingPerMsCategorie[$row['msCategorie']];
        $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['waarde'] += $waarde*$wegingPerMsCategorie[$row['msCategorie']]/100;
        
        if($this->debug)
        {
          $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['weging'] += $wegingPerMsCategorie[$row['msCategorie']];
          $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['waarde'] += $waarde*$wegingPerMsCategorie[$row['msCategorie']]/100;
        }
      }
    }
    else
    {
      $query = "SELECT doorkijkCategorie FROM doorkijk_koppelingPerVermogensbeheerder
WHERE bronKoppeling='$airsCategorie' AND doorkijkCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND systeem='AIRS' AND vermogensbeheerder='". $this->pdf->portefeuilledata['Vermogensbeheerder']."'";
      $db->executeQuery($query);
      
      while ($row = $db->nextRecord())
      {
        $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['weging'] = 100;
        $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['waarde'] = $waarde;
        
        if($this->debug)
        {
          $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['weging'] = 100;
          $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['waarde'] = $waarde;
        }
      }
    }
    
    
    return $wegingDoorkijkCategorie;
  }
  
  function bepaalWeging($doorkijkSoort,$belCategorien=array())
  {
    global $__appvar;
    if(is_array($belCategorien) && count($belCategorien)>0)
      $fondsFilter="AND Beleggingscategorie IN('".implode("','",$belCategorien)."')";
    else
      $fondsFilter='';
    
    $db = new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal
                  FROM TijdelijkeRapportage
                  WHERE rapportageDatum ='" . $this->rapportageDatum . "' $fondsFilter AND portefeuille = '" . $this->portefeuille . "'" .	$__appvar['TijdelijkeRapportageMaakUniek'].$this->tijdelijkeRapportageFilter;
    $db->SQL($query);
    $db->Query();
    $totaalWaarde = $db->nextRecord();
    
    $vertaling=array('Beleggingscategorien'=>'Beleggingscategorie','Beleggingssectoren'=>'Beleggingssector','Regios'=>'Regio');
    $query = "SELECT fonds,rekening, actuelePortefeuilleWaardeEuro as waardeEUR, ".$vertaling[$doorkijkSoort]." as airsSoort
					FROM TijdelijkeRapportage	WHERE rapportageDatum ='".$this->rapportageDatum."'  $fondsFilter AND portefeuille = '" . $this->portefeuille . "'" .	$__appvar['TijdelijkeRapportageMaakUniek'].$this->tijdelijkeRapportageFilter." Order by fonds";
    
    $db=new DB();
    $db->SQL($query); //echo $query."<br>\n";exit;
    $db->Query();
    
    $doorkijkVerdeling=array();
    while($row = $db->nextRecord())
    {
      if($row['fonds']=='' && $doorkijkSoort <> 'Regios' && $doorkijkSoort <> 'Beleggingscategorien' )
      {
        $row['fonds'] = $row['rekening'];
        $verdeling=array('Geldrekeningen'=>array('weging'=>100,'waarde'=>$row['waardeEUR']));
      }
      else
      {
        if($row['fonds']=='')
          $row['fonds'] = $row['rekening'];
        //	listarray($row);
        $verdeling = $this->bepaalWegingPerFonds($row['fonds'], $doorkijkSoort, $row['airsSoort'], $row['waardeEUR'],$row['rekening']);
      }
      $totaalPercentage=0;
      if(is_array($verdeling))
      {
        $overige=false;
        $check=0;
        foreach($verdeling as $categorie=>$percentage)
        {
          $check+=$percentage['weging'];
          $totaalPercentage=($percentage['weging'] * ($row['waardeEUR']/$totaalWaarde['totaal']) );
          $doorkijkVerdeling['categorien'][$categorie]+=$totaalPercentage;
          $doorkijkVerdeling['details'][$categorie]['percentage']+=$totaalPercentage;
          $doorkijkVerdeling['details'][$categorie]['waardeEUR']+=$percentage['waarde'];
        }
        if($check==0)
          $overige=true;
        elseif(round($check,5) <> 100)
        {
          if($this->debug)
            $this->debugData['afwijkingWeging'][$doorkijkSoort][$row['fonds']]['afwijking']['weging'] = $check-100;
        }
      }
      else
        $overige=true;
      
      if($overige==true)
      {
        $totaalPercentage=($row['waardeEUR'] / $totaalWaarde['totaal']) * 100;
        $doorkijkVerdeling['categorien']['Overige Fondsen'] += $totaalPercentage;
        $doorkijkVerdeling['details']['Overige Fondsen']['percentage']+=$totaalPercentage;
        $doorkijkVerdeling['details']['Overige Fondsen']['waardeEUR']+=$row['waardeEUR'];
        
        if($this->debug)
        {
          $this->debugData['NietGekoppeld'][$doorkijkSoort][$row['fonds']]['Overige Fondsen']['weging'] = 100;
          $this->debugData['NietGekoppeld'][$doorkijkSoort][$row['fonds']]['Overige Fondsen']['waarde']=$row['waardeEUR'];
        }
      }
      if($this->debug)
      {
        $row['percentage']=$totaalPercentage;
        $this->debugData['portefeuilleData'][] = $row;
      }
      
    }
    return $doorkijkVerdeling;
  }



	function writeRapport()
	{

	global $__appvar;
	$DB=new DB();
	$rapportageDatum = $this->rapportageDatum;
	$portefeuille = $this->portefeuille;

	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
					 "FROM TijdelijkeRapportage WHERE ".
					 " rapportageDatum ='".$rapportageDatum."' AND ".
					 " portefeuille = '".$portefeuille."' "
					 .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$totaalWaarde = $DB->nextRecord();
	$totaalWaarde = $totaalWaarde['totaal'];

	$query = "SELECT
			SUM(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro
			FROM
			TijdelijkeRapportage
			WHERE
			TijdelijkeRapportage.Portefeuille = '".$portefeuille."' AND
			TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."' AND
 			TijdelijkeRapportage.Type = 'rekening'
			" .$__appvar['TijdelijkeRapportageMaakUniek'].$this->tijdelijkeRapportageFilter;
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$totaalLiquiditeiten = $DB->nextRecord();
	$totaalLiquiditeiten = $totaalLiquiditeiten['WaardeEuro'];

	//Kleuren instellen
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);


		$this->pdf->rapport_GRAFIEK_sortering = $kleuren['grafiek_sortering'];

  $order = 'WaardeEuro DESC';
	$query="SELECT TijdelijkeRapportage.hoofdcategorie as beleggingscategorie,
	sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
	TijdelijkeRapportage.hoofdcategorieOmschrijving as Omschrijving
	FROM TijdelijkeRapportage
		WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'"
	.$__appvar['TijdelijkeRapportageMaakUniek'].$this->tijdelijkeRapportageFilter.
	" GROUP BY TijdelijkeRapportage.hoofdcategorie
	ORDER BY $order";
	debugSpecial($query,__FILE__,__LINE__);

	$DB->SQL($query);
	$DB->Query();
	$percentagebelcat=array();
	$labelcat=array();
	while($cat = $DB->nextRecord())
	{
	  if ($cat['beleggingscategorie']== "")
	  {
	  	if (round($cat['WaardeEuro'] - $totaalLiquiditeiten,1) != 0)
	  	{
	  		if(round($totaalLiquiditeiten,2) != 0)
	  		{
			$data['beleggingscategorie']['Liquiditeiten']['waardeEur']=$totaalLiquiditeiten;
			$data['beleggingscategorie']['Liquiditeiten']['Omschrijving']='Liquiditeiten';
			$cat['WaardeEuro'] = $cat['WaardeEuro'] - $totaalLiquiditeiten;
	  		}
		$cat['Omschrijving']="Geen categorie";
		$cat['beleggingscategorie']="Geen categorie";
	  	}
	  	else
	  	{
	  	$cat['Omschrijving']="Liquiditeiten";
		  $cat['Beleggingscategorie']="Liquiditeiten";
	  	}
	  }

    if ($this->pdf->rapport_GRAFIEK_sortering == 1 && $cat['Omschrijving'] == "Liquiditeiten" ) //liquiditeiten later toevoegen
    {
     $liquididiteiten['waardeEur'] = $cat['WaardeEuro'];
     $liquididiteiten['Omschrijving'] = "Liquiditeiten";
    }
    else
    {
	   $data['beleggingscategorie'][$cat['beleggingscategorie']]['waardeEur']=$cat['WaardeEuro'];
	   $data['beleggingscategorie'][$cat['beleggingscategorie']]['Omschrijving']=$cat['Omschrijving'];
    }
	}

	if ($this->pdf->rapport_GRAFIEK_sortering == 1 && round($liquididiteiten['waardeEur'],2) != 0 ) // liquiditeiten toevoegen
	{
	  $data['beleggingscategorie']['Liquiditeiten']['waardeEur']     = $liquididiteiten['waardeEur'];
	  $data['beleggingscategorie']['Liquiditeiten']['Omschrijving']  = $liquididiteiten['Omschrijving'];
	}


  $order = 'WaardeEuro DESC, TijdelijkeRapportage.valutaOmschrijving asc';
		$query = "SELECT ".
		" TijdelijkeRapportage.valutaOmschrijving AS Omschrijving, ".
		" TijdelijkeRapportage.valuta, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS WaardeEuro ".
		" FROM TijdelijkeRapportage ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."'"
		 .$__appvar['TijdelijkeRapportageMaakUniek'].$this->tijdelijkeRapportageFilter.
		" GROUP BY TijdelijkeRapportage.valuta ".
		" ORDER BY $order";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();

$data['valuta']=array();
	while($sec = $DB->nextRecord())
	{
	  if ($sec['valuta']== "")
	  {
	  	if (round($sec['WaardeEuro'] - $totaalLiquiditeiten,1) != 0)
	  	{
	  		if(round($totaalLiquiditeiten,2) != 0)
	  		{
			$data['valuta']['Liquiditeiten']['waardeEur']=$totaalLiquiditeiten;
			$data['valuta']['Liquiditeiten']['Omschrijving']='Liquiditeiten';
			$sec['WaardeEuro'] = $sec['WaardeEuro'] - $totaalLiquiditeiten;
	  		}
			$sec['Omschrijving']= 'Geen sector';
			$sec['valuta']= 'Geen sector';
	  	}
	  	else
	  	{
		$sec['Omschrijving']= 'Liquiditeiten';
		$sec['valuta']= 'Liquiditeiten';
	  	}
	  }
    if($sec['Omschrijving']=='')
      $sec['Omschrijving']= $sec['valuta'];
	  if ($this->pdf->rapport_GRAFIEK_sortering == 1 && $sec['Omschrijving'] == "Liquiditeiten" ) //liquiditeiten later toevoegen
    {
     $liquididiteiten['waardeEur'] = $sec['WaardeEuro'];
     $liquididiteiten['Omschrijving'] = "Liquiditeiten";
    }
    else
    {
	    $data['valuta'][$sec['valuta']]['waardeEur']=$sec['WaardeEuro'];
	    $data['valuta'][$sec['valuta']]['Omschrijving']=$sec['Omschrijving'];
    }
	}

		if ($this->pdf->rapport_GRAFIEK_sortering == 1 && round($liquididiteiten['waardeEur'],2) != 0 ) // liquiditeiten toevoegen
	{
	  $data['valuta']['Liquiditeiten']['waardeEur']     = $liquididiteiten['waardeEur'];
	  $data['valuta']['Liquiditeiten']['Omschrijving']  = $liquididiteiten['Omschrijving'];
	}

	if(count($data['valuta'])==0)
  {
    return '';
  }
	
		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titelKort;
    
		$grafieken = array();
		$grafieken[] = 'OIB';
		$grafieken[] = 'OIV';
		$groepen = array();
		$groepen[]=$data['beleggingscategorie'];
		$groepen[]=$data['valuta'];
    
    $standaardKleurenKort=array(array(1,88,109),array(4,157,218),array(74,202,218),array(140,219,233),array(176,218,238),array(233,242,252));
    $standaardKleurenLang=array(array(1,88,109),array(1,117,140),array(4,157,218),array(0,176,202),array(74,202,218),array(140,219,233),array(137,204,233),array(176,218,238),
                            array(233,242,252),array(156,222,202),array(114,195,139),array(71,168,76),array(43,150,34),array(30,127,22),array(18,104,11),array(6,82,0));

$allesTotaal = $totaalWaarde;

$grafiekKleuren = array();
for ($i=0; $i <4; $i++)
{
  $totaalWaarde = $allesTotaal;
	$restPercentage = 100;
		while (list($groep, $groepdata) = each($groepen[$i]))
		{
			$percentageGroep=($groepdata['waardeEur'] / $totaalWaarde) * 100 ;
			if (round($percentageGroep,1) > 2)
			{
        $restPercentage = $restPercentage - $percentageGroep;
  			$kleurdata[$i][$groep]['kleur'] = $allekleuren[$grafieken[$i]][$groep];
        
        $grafiekData[$grafieken[$i]]['Kleur'][]=array($allekleuren[$grafieken[$i]][$groep]['R']['value'],$allekleuren[$grafieken[$i]][$groep]['G']['value'],$allekleuren[$grafieken[$i]][$groep]['B']['value']);
  			//if ($percentageGroep < 0)
  			//	$percentageGroep = $percentageGroep * -1;
        $omschrijving=vertaalTekst($groepdata['Omschrijving'],$this->pdf->rapport_taal). " (" . $this->formatGetal(($groepdata['waardeEur'] / $totaalWaarde) * 100 ,1) ." %)";
  			$grafiekData[$grafieken[$i]]['Percentage'][$omschrijving] = round($percentageGroep,1) ;
   			$grafiekData[$grafieken[$i]]['Omschrijving'][] = $omschrijving   ;
			}
		}
		if (round($restPercentage,1) >0)
		{
		  $omschrijving=vertaalTekst("Overige",$this->pdf->rapport_taal) . " (" . $this->formatGetal($restPercentage,1) ." %)" ;;
	  	$grafiekData[$grafieken[$i]]['Percentage'][$omschrijving] = $restPercentage;
	  	$grafiekData[$grafieken[$i]]['Omschrijving'][] = $omschrijving;
      if (round($restPercentage,1) == 100)
        unset($grafiekData[$grafieken[$i]]);
		}
  

  
/*
    if(isset($grafiekData[$grafieken[$i]]))
    {
      if (count($grafiekData[$grafieken[$i]]['Omschrijving'])<7)
      {
        $grafiekKleuren[$i] = $standaardKleurenKort;
        $grafiekData[$grafieken[$i]]['Kleur'] = $standaardKleurenKort;
      }
      else
      {
        $grafiekKleuren[$i] = $standaardKleurenLang;
        $grafiekData[$grafieken[$i]]['Kleur'] = $standaardKleurenLang;

      }

    }
  */
}
//listarray($grafiekData);
//eind kleuren instellen
    
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $query = "SELECT doorkijkCategorie,doorkijkCategorieSoort,grafiekKleur, afdrukVolgorde
                   FROM doorkijk_categoriePerVermogensbeheerder
                   WHERE Vermogensbeheerder='$beheerder'
                   ORDER BY doorkijkCategorieSoort,afdrukVolgorde
                  ";
    
    $DB->SQL($query);
    $DB->Query();
    $this->kleuren=array();
    while($data = $DB->nextRecord())
    {
      $this->kleuren[$data['doorkijkCategorieSoort']][$data['doorkijkCategorie']]=unserialize($data['grafiekKleur']);
    }
    
    $belCategorien=array();
    
    /*
    $query="SELECT TijdelijkeRapportage.beleggingscategorie
	FROM TijdelijkeRapportage
  	WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	  AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'
	  AND TijdelijkeRapportage.beleggingscategorie='H-Aand' "
      .$__appvar['TijdelijkeRapportageMaakUniek'].$this->tijdelijkeRapportageFilter.
      " GROUP BY TijdelijkeRapportage.beleggingscategorie";
    debugSpecial($query,__FILE__,__LINE__);
    //echo $query;exit;
    $DB->SQL($query);
    $DB->Query();
    while($cat = $DB->nextRecord())
    {
      $belCategorien[]=$cat['beleggingscategorie'];
    }
    */
    
    if($_POST['debug']==1)
    {
      $this->debug=true;
    }
   
    $tmp=$this->bepaalWeging('Regios',$belCategorien);
    arsort ($tmp['categorien']);
    $restPercentage=0;
    foreach($tmp['categorien'] as $categorie=>$percentage)
    {
      if($percentage<2 && $categorie <> 'Overige')
      {
        $restPercentage+=$percentage;
        unset($tmp['categorien'][$categorie]);
        unset($tmp['details'][$categorie]);
      }
    }
    if(round($restPercentage,1)>0)
    {
      if(!isset($tmp['categorien']['Overige']))
        $tmp['categorien']['Overige']=0;
      $tmp['categorien']['Overige'] += $restPercentage;
    }
    if(count($tmp['categorien'])<7)
      $standaardKleuren=$standaardKleurenKort;
    else
      $standaardKleuren=$standaardKleurenLang;
    $i=0;
    foreach($tmp['categorien'] as $categorie=>$percentage)
    {
      if(isset($tmp['details'][$categorie]))
      {
        $omschrijving = vertaalTekst($categorie ,$this->pdf->rapport_taal) . " (" . $this->formatGetal($percentage, 1) . " %)";
        $grafiekData['OIR']['Percentage'][$omschrijving] = round($percentage, 1);
        $grafiekData['OIR']['Kleur'][] = $this->kleuren['Regios'][$categorie];//$standaardKleuren[$i];
        $i++;
      }
    }

    $tmp=$this->bepaalWeging('Beleggingssectoren',$belCategorien);
    arsort ($tmp['categorien']);
    $restPercentage=0;
    foreach($tmp['categorien'] as $categorie=>$percentage)
    {
      if($percentage<2 && $categorie <> 'Overige')
      {
        $restPercentage+=$percentage;
        unset($tmp['categorien'][$categorie]);
        unset($tmp['details'][$categorie]);
      }
    }
    if(round($restPercentage,1)>0)
    {
      if(!isset($tmp['categorien']['Overige']))
        $tmp['categorien']['Overige']=0;
      $tmp['categorien']['Overige'] += $restPercentage;
    }
    
    if(count($tmp['categorien'])<7)
      $standaardKleuren=$standaardKleurenKort;
    else
      $standaardKleuren=$standaardKleurenLang;
    

    $i=0;
    foreach($tmp['categorien'] as $categorie=>$percentage)
    {
        $omschrijving = vertaalTekst($categorie ,$this->pdf->rapport_taal) . " (" . $this->formatGetal($percentage, 1) . " %)";
        $grafiekData['OIS2']['Percentage'][$omschrijving] = round($percentage, 1);
        $grafiekData['OIS2']['Kleur'][] = $this->kleuren['Beleggingssectoren'][$categorie]; // $standaardKleuren[$i];
        $i++;
    }
    if($this->debug==true)
    {
      echo "Input categorieen:<br>\n";
      listarray($belCategorien);
      echo "DoorkijkInfo:<br>\n";
      listarray($this->debugData);
    }

$diameter = 35;
$hoek = 30;
$dikte = 10;
$Xas= 80;
$yas= 55;
//print_r($grafiekData);exit;
$headerHeight=30;
$lwb=(297/2)-$this->pdf->marge; //133.5
$vwh=((210-$headerHeight-$this->pdf->marge)/2+$headerHeight)-$headerHeight;
$chartsize=55;
$extraBarW=25;

$extraX=-18;

$this->pdf->setXY($this->pdf->marge+(($lwb/4)*1.5-$chartsize/2)+$extraX,$headerHeight);
//$this->pdf->setXY($this->pdf->marge+(($lwb/4)*1-$chartsize/2),$headerHeight);

if(min($grafiekData['OIB']['Percentage']) < 0)
  $this->BarDiagram($chartsize+$extraBarW,$chartsize,$grafiekData['OIB']['Percentage'],'%l',$grafiekData['OIB']['Kleur'],vertaalTekst('Beleggingscategorie',$this->pdf->rapport_taal));
else
{  
  $legendaStart=$this->correctLegentHeight(count($grafiekData['OIB']['Percentage']));
	PieChart_L25($this->pdf,$chartsize,$vwh,$grafiekData['OIB']['Percentage'],'%l',$grafiekData['OIB']['Kleur'],vertaalTekst('Beleggingscategorie',$this->pdf->rapport_taal),$legendaStart);
}

$this->pdf->setXY($this->pdf->marge+(($lwb/4)*5.5-$chartsize/2)+$extraX,$headerHeight);
//$this->pdf->setXY($this->pdf->marge+(($lwb/4)*3-$chartsize/2),$headerHeight);
//listarray($grafiekData);

if(min($grafiekData['OIV']['Percentage']) < 0)
  $this->BarDiagram($chartsize+$extraBarW,$chartsize,$grafiekData['OIV']['Percentage'],'%l',$grafiekData['OIV']['Kleur'],vertaalTekst('Valuta',$this->pdf->rapport_taal));
else
{ 
  $legendaStart=$this->correctLegentHeight(count($grafiekData['OIV']['Percentage']));
  PieChart_L25($this->pdf,$chartsize,$vwh,$grafiekData['OIV']['Percentage'],'%l',$grafiekData['OIV']['Kleur'],vertaalTekst('Valuta',$this->pdf->rapport_taal),$legendaStart);
}

$this->pdf->setXY($this->pdf->marge+(($lwb/4)*1.5-$chartsize/2)+$extraX,$headerHeight+$vwh-10);
//$this->pdf->setXY($this->pdf->marge+(($lwb/4)*5-$chartsize/2),$headerHeight);
if(min($grafiekData['OIS2']['Percentage']) < 0)
  $this->BarDiagram($chartsize+$extraBarW,$chartsize,$grafiekData['OIS2']['Percentage'],'%l',$grafiekData['OIS2']['Kleur'],vertaalTekst('Sector',$this->pdf->rapport_taal));
else
{ 
  $legendaStart=$this->correctLegentHeight(count($grafiekData['OIS2']['Percentage']));
  PieChart_L25($this->pdf,$chartsize,$vwh,$grafiekData['OIS2']['Percentage'],'%l',$grafiekData['OIS2']['Kleur'],vertaalTekst('Sector',$this->pdf->rapport_taal),$legendaStart);
}

if(isset($grafiekData['OIR']))
{
  $this->pdf->setXY($this->pdf->marge+(($lwb/4)*5.5-$chartsize/2)+$extraX,$headerHeight+$vwh-10);
 //  $this->pdf->setXY($this->pdf->marge+(($lwb/4)*7-$chartsize/2),$headerHeight);
  if(min($grafiekData['OIR']['Percentage']) < 0)
    $this->BarDiagram($chartsize+$extraBarW,$chartsize,$grafiekData['OIR']['Percentage'],'%l',$grafiekData['OIR']['Kleur'],vertaalTekst('Regio',$this->pdf->rapport_taal));
  else
  { 
    $legendaStart=$this->correctLegentHeight(count($grafiekData['OIR']['Percentage']));
    PieChart_L25($this->pdf,$chartsize,$vwh,$grafiekData['OIR']['Percentage'],'%l',$grafiekData['OIR']['Kleur'],vertaalTekst('Regio',$this->pdf->rapport_taal),$legendaStart);
  }
}

	}
  
  function correctLegentHeight($regels)
  {
    return array($this->pdf->GetX()+60,$this->pdf->GetY()+ 35 -($regels*4)/2);
     
  }
    function SetLegends2($data, $format)
  {
      $this->pdf->legends=array();
      $this->pdf->wLegend=0;

      $this->pdf->sum=array_sum($data);

      $this->pdf->NbVal=count($data);
      foreach($data as $l=>$val)
      {
          //$p=sprintf('%.1f',$val/$this->sum*100).'%';
          if($val <> 0)
          {
            $p=sprintf('%.1f',$val).'%';
            $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
          }
          else
            $legend='';
          $this->pdf->legends[]=$legend;
          $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->wLegend);
      }
  }
   function BarDiagram($w, $h, $data, $format,$colorArray,$titel)
  {

      $this->pdf->SetFont($this->rapport_font, '', $this->rapport_fontsize);
      $this->SetLegends2($data,$format);


      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $nbDiv=5;
      $legendWidth=25;
     // echo count($data);exit;
      $YDiag = $YPage+30-((count($data)*5)/2);
      $hDiag = floor($h);
      $XDiag = $XPage +  $legendWidth;
      $lDiag = floor($w - $legendWidth);
      if($color == null)
          $color=array(155,155,155);
      if ($maxVal == 0) {
          $maxVal = max($data)*1.1;
      }
      if ($minVal == 0) {
          $minVal = min($data)*1.1;
      }
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
      //$this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $nullijn=$XDiag - ($offset * $unit);
    
      $i=0;
      $nbDiv=10;
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $i=0;







      $this->pdf->setXY($XPage,$YPage);
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', 8.5);
      $this->pdf->Cell($w,4,$titel,0,1,'L');
      

      //$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);
      $this->pdf->SetFont($this->pdf->rapport_font, '', 7);
   //listarray($colorArray);exit;
      foreach($data as $key=>$val)
      {
          $this->pdf->SetFillColor($colorArray[$i][0],$colorArray[$i][1],$colorArray[$i][2]);
          $xval = $nullijn;
          $lval = ($val * $unit);
          $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
          $hval = $eBaton;
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'F');
          $this->pdf->SetXY($XPage, $yval);
          $this->pdf->Cell($legendWidth , $hval, $this->pdf->legends[$i],0,0,'R');
          $i++;
      }

      //Scales
      $minPos=($minVal * $unit);
      $maxPos=($maxVal * $unit);

      $unit=($maxPos-$minPos)/$nbDiv;
     // echo "$minPos $maxPos -> $minVal $maxVal using $unit met null $nullijn";


  }
  
    function set3dLabels($labels,$x,$y,$colors)
    {
      $xcor=-55;
      $xcor2=5;
      $ycor= 27;
      $aantal = count($labels);
      $h=3.5;
      $colMax=0;
      $maxAantal=0;
      for($i=0; $i<$aantal; $i++)
      {
    	    $hLegend=2.5;
    	    if ($i < $colMax)
    	    {
    	    $x1=$xcor+$x;
    	    $x2=$xcor+$x+$h;
    	    $y1=$ycor+$y+$i*$h;
    	    $y2=$ycor+$y+$i*$h;
    	    }
    	    else if($i < $colMax *2 && $i >$colMax -1)
		      {
		      $y1=$ycor+$y+($i-$maxAantal/2)*$h;
    	    $y2=$ycor+$y+($i-$maxAantal/2)*$h;
     	    $x1=$xcor2+$x;
    	    $x2=$xcor2+$x+$h;
		      }

		      if ($i<$maxAantal )
		      {
		      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
		      $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['b'],$this->pdf->rapport_fonds_fontcolor['b']);
		      $this->pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b'])));

          $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->pdf->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1);
          $this->pdf->Cell(0,$hLegend,$labels[$i]);
          $y1+=$hLegend;
		      }
      }
    }  
  
}
?>
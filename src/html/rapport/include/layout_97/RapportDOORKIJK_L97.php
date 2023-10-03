<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/02/29 16:23:08 $
File Versie					: $Revision: 1.3 $

$Log: RapportDOORKIJK_L97.php,v $

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportDOORKIJK_L97
{
	function RapportDOORKIJK_L97($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = $pdf;
		$this->pdf->rapport_type = "DOORKIJK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_titel = "Allocaties inclusief uitsplitsing";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->underlinePercentage=0.8;
		if($this->pdf->lastPOST['debug'])
		  $this->debug=true;
		else
			$this->debug=false;
		$this->debugData=array();
		$this->consolidatie=false;
		$this->categorie='Aandelen';
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
              $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['weging'] += $wegingPerMsCategorie[$categorie];
              $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['waarde'] += $waarde * $wegingPerMsCategorie[$categorie] / 100;
              
              if ($this->debug)
              {
                
                $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['weging'] += $wegingPerMsCategorie[$categorie];
                $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['waarde'] += $waarde * $wegingPerMsCategorie[$categorie] / 100;
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

      
      if(in_array($doorkijkSoort,array('Beleggingscategorien','Rating','Coupon','Looptijd','Regios','Beleggingssectoren')))
      {
        $query = "SELECT doorkijkCategorie FROM doorkijk_koppelingPerVermogensbeheerder
WHERE bronKoppeling='$airsCategorie' AND doorkijkCategoriesoort='" . mysql_real_escape_string($doorkijkSoort) . "' AND systeem='AIRS' AND vermogensbeheerder='" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'";
        $db->executeQuery($query);
        if($db->records())
        {
          while ($row = $db->nextRecord())
          {
            $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['weging'] = 100;
            $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['waarde'] = $waarde;
            
            if ($this->debug)
            {
              $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['weging'] = 100;
              $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['waarde'] = $waarde;
            }
          }
        }
        else
        {
          $wegingDoorkijkCategorie[$airsCategorie]['weging'] = 100;
          $wegingDoorkijkCategorie[$airsCategorie]['waarde'] = $waarde;
          
          if($this->debug)
          {
            $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$airsCategorie]['weging'] = 100;
            $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$airsCategorie]['waarde'] = $waarde;
          }
        }
      }
      else
      {
        $wegingDoorkijkCategorie[$airsCategorie]['weging'] = 100;
        $wegingDoorkijkCategorie[$airsCategorie]['waarde'] = $waarde;
        
        if($this->debug)
        {
          $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$airsCategorie]['weging'] = 100;
          $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$airsCategorie]['waarde'] = $waarde;
        }
        
      }
    }
    
    
    return $wegingDoorkijkCategorie;
  }

	function bepaalWeging($doorkijkSoort,$belCategorie='',$invertSelection=false)
	{
		global $__appvar;
		if(is_array($belCategorie))
    {
      if ($invertSelection == false)
      {
        $operator = '';
      }
      else
      {
        $operator = 'NOT';
      }
      
      $fondsFilter="AND Beleggingscategorie $operator IN('".implode($belCategorie,"','")."')";
    }
    else
    {
      if ($invertSelection == false)
      {
        $operator = '=';
      }
      else
      {
        $operator = '<>';
      }
  
      if ($belCategorie <> '')
      {
        $fondsFilter = "AND Beleggingscategorie $operator '$belCategorie'";
      }
      else
      {
        $fondsFilter = '';
      }
    }
		$db = new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal 
                  FROM TijdelijkeRapportage 
                  WHERE rapportageDatum ='" . $this->rapportageDatum . "' $fondsFilter AND portefeuille = '" . $this->portefeuille . "'" .	$__appvar['TijdelijkeRapportageMaakUniek'];
		$db->SQL($query);
		$db->Query();
		$totaalWaarde = $db->nextRecord();

		$vertaling=array('Beleggingscategorien'=>'Beleggingscategorie','Beleggingssectoren'=>'Beleggingssector','Regios'=>'Regio',
                     'Looptijd'=>'datediff(TijdelijkeRapportage.Lossingsdatum,TijdelijkeRapportage.rapportageDatum)/365.2421','Coupon'=>'TijdelijkeRapportage.rentePercentage','Rating'=>'Fondsen.Rating');
    
    
    $query = "SELECT TijdelijkeRapportage.fonds,TijdelijkeRapportage.rekening, sum(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as waardeEUR, ".$vertaling[$doorkijkSoort]." as airsSoort
					FROM TijdelijkeRapportage LEFT JOIN Fondsen ON TijdelijkeRapportage.Fonds=Fondsen.Fonds
					 WHERE TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatum."'  $fondsFilter AND TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "'" .	$__appvar['TijdelijkeRapportageMaakUniek']."
					  GROUP BY TijdelijkeRapportage.fonds
					  ORDER BY TijdelijkeRapportage.fonds";
    
	//	$query = "SELECT fonds,rekening, actuelePortefeuilleWaardeEuro as waardeEUR, ".$vertaling[$doorkijkSoort]." as airsSoort
	//				FROM TijdelijkeRapportage	WHERE rapportageDatum ='".$this->rapportageDatum."'  $fondsFilter AND portefeuille = '" . $this->portefeuille . "'" .	$__appvar['TijdelijkeRapportageMaakUniek']." Order by fonds";

		$db=new DB();
    $db2=new DB();
		$db->SQL($query); //echo $query."<br>\n";exit;
		$db->Query();

		$doorkijkVerdeling=array();
		while($row = $db->nextRecord())
		{
      if($doorkijkSoort=='Looptijd')
      {
        foreach($this->buckets[$doorkijkSoort] as $bucket=>$bucketSettings)
        {
          if(($row['airsSoort']>=$bucketSettings['min'] && $row['airsSoort']<$bucketSettings['max']) || ($bucketSettings['min']==$row['airsSoort'] && $bucketSettings['max']==$row['airsSoort']))
          {
            $row['airsSoort']=$bucket;
            break;
          }
        }
        if($row['airsSoort']=='')
          $row['airsSoort']='Overig';
      }
      elseif($doorkijkSoort=='Coupon' && $row['fonds']<>'')
      {
        $query="SELECT Rentepercentage FROM Rentepercentages WHERE fonds='".mysql_real_escape_string($row['fonds'])."' AND datum <= '".$this->rapportageDatum."' order by datum desc limit 1";
        $db2->SQL($query);
        $db2->Query();
        $rente= $db2->nextRecord();
        if(isset($rente['Rentepercentage']))
          $row['airsSoort']=$rente['Rentepercentage'];
        
        foreach($this->buckets[$doorkijkSoort] as $bucket=>$bucketSettings)
        {
          if(($row['airsSoort']>=$bucketSettings['min'] && $row['airsSoort']<$bucketSettings['max']) || ($bucketSettings['min']==$row['airsSoort'] && $bucketSettings['max']==$row['airsSoort']))
          {
            $row['airsSoort']=$bucket;
            break;
          }
        }
        if($row['airsSoort']===0 || $row['airsSoort']=='')
          $row['airsSoort']='Overig';
        
      }
      elseif($doorkijkSoort=='Rating')
      {
        if($row['airsSoort']=='')
          $row['airsSoort']='NR';
      }
			if($row['fonds']=='' && !in_array($doorkijkSoort,array('Regios','Beleggingscategorien','Rating','Coupon','Looptijd')))
			{
				$row['fonds'] = $row['rekening'];
				$verdeling=array('Geldrekeningen'=>array('weging'=>100,'waarde'=>$row['waardeEUR']));
			}
			else
			{
				if($row['fonds']=='')
			  	$row['fonds'] = $row['rekening'];
        
        if($row['rekening']<>'')
          $row['airsSoort']='Geldrekeningen';
				$verdeling = $this->bepaalWegingPerFonds($row['fonds'], $doorkijkSoort, $row['airsSoort'], $row['waardeEUR'],$row['rekening']);
			}
			
			$totaalPercentage=0;
			if(is_array($verdeling)&& count($verdeling)>0)
			{
				$overige=false;
				$check=0;
				foreach($verdeling as $categorie=>$percentage)
				{
					$check+=$percentage['weging'];
					$totaalPercentage=($percentage['weging'] * ($row['waardeEUR']/$totaalWaarde['totaal']));
					$doorkijkVerdeling['categorien'][$categorie]+=$totaalPercentage;
					$doorkijkVerdeling['details'][$categorie]['percentage']+=$totaalPercentage;
					$doorkijkVerdeling['details'][$categorie]['waardeEUR']+=$percentage['waarde'];
				}
				if($check==0)
        {
					$overige=true;
        }
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
				$doorkijkVerdeling['categorien']['Overige'] += $totaalPercentage;
				$doorkijkVerdeling['details']['Overige']['percentage']+=$totaalPercentage;
				$doorkijkVerdeling['details']['Overige']['waardeEUR']+=$row['waardeEUR'];

				if($this->debug)
				{
					$this->debugData['NietGekoppeld'][$doorkijkSoort][$row['fonds']]['Overige']['weging'] = 100;
					$this->debugData['NietGekoppeld'][$doorkijkSoort][$row['fonds']]['Overige']['waarde']=$row['waardeEUR'];
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

	function toonTabel($regels,$xOffset,$titel,$kleuren)
	{
		$this->pdf->setWidths(array($xOffset+3,45,24,15));
		$this->pdf->setAligns(array('L','L','R','R'));
		$this->pdf->setXY($this->pdf->marge,50);
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize+4);
		$this->pdf->row(array('',$titel,'EUR','%'));
		$this->pdf->excelData[]=array($titel,'EUR','%');
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
		$this->pdf->ln(1);
		$totalen=array();
	//	listarray($kleuren);
		foreach($regels as $categorie=>$data)
			if(!isset($kleuren[$categorie]))
				$kleuren[$categorie]=array(128,128,128);

		foreach($kleuren as $categorie=>$kleur)
		{
		//foreach($regels as $categorie=>$data)
			if(isset($regels[$categorie]))
			{
				$data =$regels[$categorie];
				$this->pdf->rect($this->pdf->getX() + $xOffset, $this->pdf->getY() + 1, 2, 2, 'DF', '', $kleuren[$categorie]);
				$this->pdf->row(array('', $categorie, $this->formatGetal($data['waardeEUR'], 0), $this->formatGetal($data['percentage'], 2)));
				$this->pdf->excelData[] = array($categorie, round($data['waardeEUR'], 0), round($data['percentage'], 2));
				$totalen['waardeEUR'] += $data['waardeEUR'];
				$totalen['percentage'] += $data['percentage'];
			}
		}
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('','', 'SUB', 'SUB');
    $this->pdf->row(array('','Totaal', $this->formatGetal($totalen['waardeEUR'], 0), $this->formatGetal($totalen['percentage'], 2)));
		$this->pdf->excelData[]=array('Totaal', round($totalen['waardeEUR'], 0), round($totalen['percentage'], 2));
		$this->pdf->excelData[]=array();
    $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
	}

	function printPie($kleurdata,$xstart,$ystart,$titel)
	{
			$col1=array(255,0,0); // rood
			$col2=array(0,255,0); // groen
			$col3=array(255,128,0); // oranje
			$col4=array(0,0,255); // blauw
			$col5=array(255,255,0); // geel
			$col6=array(255,0,255); // paars
			$col7=array(128,128,128); // grijs
			$col8=array(128,64,64); // bruin
			$col9=array(255,255,255); // wit
			$col0=array(0,0,0); //zwart
			$standaardKleuren=array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col0);
		if($kleurdata)
		{
      /*
        $sorted 		= array();
        $percentages 	= array();
        $kleur			= array();
        $valuta 		= array();

        foreach($kleurdata as $key=>$data)
        {
          $percentages[] 	= $data['percentage'];
          $kleur[] 			= $data['kleur'];
          $valuta[] 		= $key;
        }
        arsort($percentages);

        foreach($percentages as $key=>$percentage)
        {
          $sorted[$valuta[$key]]['kleur']=$kleur[$key];
          $sorted[$valuta[$key]]['percentage']=$percentage;
        }
        $kleurdata = $sorted; //columnSort($kleurdata, 'pecentage');
*/
			$pieData=array();
			$grafiekKleuren = array();

			$a=0;
			foreach($kleurdata as $key=>$value)
			{
				if ($value['kleur'][0] == 0 && $value['kleur'][1] == 0 && $value['kleur'][2] == 0)
					$grafiekKleuren[]=$standaardKleuren[$a];
				else
					$grafiekKleuren[] = array($value['kleur'][0],$value['kleur'][1],$value['kleur'][2]);
				$pieData[$key] = $value['percentage'];
				$a++;
			}
		}
		else
			$grafiekKleuren = $standaardKleuren;

		$this->pdf->SetTextColor($this->pdf->pdf->rapport_fontcolor['r'],$this->pdf->pdf->rapport_fontcolor['g'],$this->pdf->pdf->rapport_fontcolor['b']);

		$this->pdf->rapport_printpie = true;
		foreach($pieData as $key=>$value)
		{
			if ($value < 0)
					$this->pdf->rapport_printpie = false;
		}
    $legenda='%l (%p)';
		if($this->pdf->rapport_printpie)
		{
		  if($this->consolidatie)
      {
        $size = 40;
        if($titel=='')
        {
          $size = 55;
          $legenda='';
       //   $xstart=$xstart-10;
       //   $ystart=$ystart-10;
        }
      }
		  else
      {
        $size = 50;
      }
			$this->pdf->SetXY($xstart, $ystart);
			$y = $this->pdf->getY();
			$this->pdf->SetFont($this->pdf->pdf->rapport_font,'b',10);
			$this->pdf->Cell($size,4,vertaalTekst($titel, $this->pdf->rapport_taal),0,1,"C");
			$this->pdf->SetFont($this->pdf->pdf->rapport_font,'',$this->pdf->pdf->rapport_fontsize);
			$this->pdf->SetX($xstart);
			$this->PieChart(100, $size, $pieData, $legenda, $grafiekKleuren);
			$this->pdf->setY($y);
			$this->pdf->SetLineWidth($this->pdf->lineWidth);
		}
	}

	function PieChart($w, $h, $data, $format, $colors=null)
	{

		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
		$this->pdf->legends=array();
    $this->pdf->NbVal=0;
		if($this->consolidatie==true && $format<>'')
		  $this->pdf->SetLegends($data,$format);

		$XPage = $this->pdf->GetX();
		$YPage = $this->pdf->GetY();
		$margin = 2;
		$hLegend = 1;
		$radius = min($w - $margin * 4 - $hLegend , $h - $margin * 2); //
		$radius = floor($radius / 2);
		$XDiag = $XPage + $margin + $radius;
		$YDiag = $YPage + $margin + $radius;
		if($colors == null) {
			for($i = 0;$i < count($data); $i++) {
				$gray = $i * intval(255 / count($data));
				$colors[$i] = array($gray,$gray,$gray);
			}
		}

		//Sectors
		$sum=array_sum($data);
		$this->pdf->SetLineWidth(0.2);
		$angleStart = 0;
		$angleEnd = 0;
		$i = 0;
		foreach($data as $val) {
			$angle = floor(($val * 360) / doubleval($sum));
			if ($angle != 0) {
				$angleEnd = $angleStart + $angle;
				$this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
				$this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
				$angleStart += $angle;
			}
			$i++;
		}
		if ($angleEnd != 360) {
			$this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
		}

		//Legends
    if($this->consolidatie==true)
    {
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-1);
  
      $x1 = $XPage+2*$radius+$margin*2;
      $x2 = $x1 + $margin;
      $y1 = $YDiag - $radius;// + ($radius) + $margin;
  
      for ($i = 0; $i < $this->pdf->NbVal; $i++)
      {
        $this->pdf->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
        $this->pdf->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
        $this->pdf->SetXY($x2, $y1);
        $this->pdf->Cell(0, $hLegend, $this->pdf->legends[$i]);
        $y1 += $hLegend + $margin;
      }
    }
    /**/
	}
  
  function ophalenCRMRecord()
  {
    $gebruikteCrmVelden=array('naam','logo');
    $data = array();
    foreach($this->pdf->portefeuilles as $portefeuille)
    {
      $db = new DB();
      $query = "SELECT CRM_naw.id FROM CRM_naw WHERE CRM_naw.portefeuille='" . $portefeuille . "'";
      $db->SQL($query);
      $crmData = $db->lookupRecord();
      $naw = new NAW();
      $naw->getById($crmData['id']);
      
      $data[$portefeuille]=array();
      foreach ($gebruikteCrmVelden as $veld)
      {
        if (substr($veld, 0, 9) == 'Beheerder')
        {
          $data[$portefeuille][substr($veld, 9, 4)][$veld] = array('omschrijving' => $naw->data['fields'][$veld]['description'], 'waarde' => $naw->data['fields'][$veld]['value']);
        }
        else
        {
          $data[$portefeuille][$veld] = $naw->data['fields'][$veld]['value'];
        }
      }
      
      $DB = new DB();
      $query = "SELECT kleurcode FROM Portefeuilles where portefeuille='" . mysql_real_escape_string($portefeuille) . "'";
      $DB->SQL($query);
      $DB->Query();
      $kleur = $DB->nextRecord();
      $data[$portefeuille]['kleur']=unserialize($kleur['kleurcode']);
    }
    
    return $data;
  }
	
	function vulConsolidatiePagina($categorie)
  {
    global $__appvar;
    $db=new DB();
    $query="SELECT KeuzePerVermogensbeheerder.waarde as beleggingscategorie
FROM KeuzePerVermogensbeheerder
JOIN Portefeuilles ON KeuzePerVermogensbeheerder.vermogensbeheerder = Portefeuilles.Vermogensbeheerder
WHERE portefeuille='".$this->portefeuille ."' AND
KeuzePerVermogensbeheerder.categorieIXP='Aandelen' AND
KeuzePerVermogensbeheerder.categorie='Beleggingscategorien' ";
    $db->SQL($query);
    $db->Query();
    $beleggingscategorien=array();
    while($data = $db->nextRecord())
    {
      $beleggingscategorien[]=$data['beleggingscategorie'];
    }
    $crmData=$this->ophalenCRMRecord();

    
    //$doorkijkTitels=array('Beleggingscategorien'=>'Beleggingscategorie','Regios'=>'Regio','Beleggingssectoren'=>'Beleggingssector',//.strtolower($this->categorie)
      //'Rating'=>'Rating '.strtolower($this->categorie),'Looptijd'=>'Looptijd '.strtolower($this->categorie));//array();Beleggingscategorie
    $doorkijkTitels=array('Beleggingscategorien'=>' ','Regios'=>' ','Beleggingssectoren'=>' ',//.strtolower($this->categorie)
                          'Rating'=>' ','Looptijd'=>' ');//array();Beleggingscategorie
    if($categorie=='Aandelen')
    {
      $belCategorien = array('Aandelen' => $beleggingscategorien);
      $doorkijkCategorieSoorten=array('Regios','Beleggingssectoren');
    }
    elseif($categorie=='Obligaties')
    {
      $belCategorien = array('Obligaties' => $beleggingscategorien);
      $doorkijkCategorieSoorten=array('Rating','Looptijd');
    }
    //$belCategorien=array('Aandelen'=>$beleggingscategorien,'Obligaties'=>$beleggingscategorien);
    //$kolommen=array('Aandelen','Obligaties');
    $portefeuilleBackup=$this->portefeuille;
  
    $yPagina=$this->pdf->getY();
    foreach($this->pdf->portefeuilles as $portefeuille)
    {
  
      if($yPagina>180)
      {
        $this->pdf->addPage();
        $yPagina=0;
      }
    
      if($yPagina<40)
        $yPagina=40;
      $this->portefeuille=$portefeuille;
  
      $query="SELECT Depotbank,Risicoklasse,
if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam,Clienten.Naam) as Naam
       FROM Portefeuilles
       JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder=Vermogensbeheerders.Vermogensbeheerder
       LEFT JOIN Clienten ON Portefeuilles.Client = Clienten.Client
       LEFT Join CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille
       WHERE Portefeuilles.Portefeuille='$portefeuille'";

      $db->SQL($query);
      $pdata=$db->lookupRecord();
      if($pdata['Risicoklasse']=='')
        $pdata['Risicoklasse']='Risico';
      //listarray($pdata);exit;
      $fondswaarden = berekenPortefeuilleWaarde($portefeuille,$this->rapportageDatum,(substr($this->rapportageDatum, 5, 5) == '01-01')?true:false,$this->pdf->portefeuilledata['RapportageValuta'],$this->rapportageDatum);
      vulTijdelijkeTabel($fondswaarden ,$portefeuille,$this->rapportageDatum);
      
      $kol=0;
      $names=array('AbnAmro','actiam','BankSafra','BankTenCate','BAvanDoorn','bondcapital','capitael','dexxi','DoubleDividend','HeerenVermogensbeheer','helliot','ibeleggen','IBS','ING','junior','kempen','mercurius','Mpartners','optimix','robeco','stoic','TIP','vanEck','vanLieshout');
      foreach($belCategorien as $belCategorieOmschrijving=>$belCategorie)
      {
        $this->pdf->setXY(10,$yPagina+15);
        $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
        $this->pdf->multiCell(45,5,($pdata['Naam']<>''?$pdata['Naam']:$portefeuille.' '.$pdata['Depotbank']),0,'L',0);
        $this->pdf->setXY(10+40,$yPagina+15);
        $x=70;
        //$this->pdf->multiCell(45,5,$pdata['Risicoklasse'],0,'L',0);
  
       // if(!isset($crmData[$portefeuille]['logo']) || $crmData[$portefeuille]['logo']='')
       //   $crmData[$portefeuille]['logo']=$names[rand(0,count($names))];
  
        $logo=$__appvar["basedir"].'/html/rapport/include/layout_97/logo/'.$crmData[$portefeuille]['logo'].'.png';
  
        if(is_file($logo))
        {
          $img=getimagesize($logo);
    
          $imgx=$img[0];
          $imgy=$img[1];
          $verhouding=$imgx/$imgy;
          if($verhouding<2.2)
            $width=30*$verhouding/2.2;
          else
            $width=30;
    
          if($verhouding>2.5)
            $ypos=$yPagina+$verhouding*1.2;
          else
            $ypos=$yPagina;
    
          $this->pdf->Image($logo,$x-$width/2-12,$ypos+10,$width);
        }
        
  
        $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize+2);
      //  $this->pdf->setXY(140+($kol*90),$yPagina-5);
      //  $this->pdf->multiCell(30,5,$belCategorieOmschrijving,0,'L',0);
        
        foreach ($doorkijkCategorieSoorten as $index => $doorkijkCategorieSoort)
        {
          $xOffset = 60 +  ($index * 90)  + ($kol*110);
          

          if($belCategorieOmschrijving=='Aandelen')
            $invert=false;
          else
            $invert=true;
          $doorKijk = $this->bepaalWeging($doorkijkCategorieSoort, $belCategorie, $invert);
          // listarray($doorKijk);
          //$this->toonTabel($doorKijk['details'],$xOffset,$doorkijkTitels[$doorkijkCategorieSoort],$this->kleuren[$doorkijkCategorieSoort]);
          $grafiekdata = array();
          $grafiekTonen = true;
          /*
          foreach ($doorKijk['categorien'] as $categorie => $percentage)
          {
            $grafiekdata[$categorie]['kleur'] = $this->kleuren[$doorkijkCategorieSoort][$categorie];//array('R' => array('value' => $kleuren[$categorie][0]),'G' => array('value' => $kleuren[$categorie][1]),'B' => array('value' => $kleuren[$categorie][2]));
            $grafiekdata[$categorie]['percentage'] = $percentage;
            if ($percentage < 0)
            {
              $grafiekTonen = false;
            }
          }
          */
  
          $kleuren=$this->kleuren[$doorkijkCategorieSoort];
          foreach($doorKijk['categorien'] as $categorie=>$data)
            if(!isset($kleuren[$categorie]))
              $kleuren[$categorie]=array(128,128,128);
  
          foreach($kleuren as $categorie=>$kleur)
          {
            //foreach($regels as $categorie=>$data)
            if (isset($doorKijk['categorien'][$categorie]))
            {
      
              $grafiekdata[$categorie]['kleur'] = $kleur;//array('R' => array('value' => $kleuren[$categorie][0]),'G' => array('value' => $kleuren[$categorie][1]),'B' => array('value' => $kleuren[$categorie][2]));
              $grafiekdata[$categorie]['percentage'] = $doorKijk['categorien'][$categorie];
              if ($doorKijk['categorien'][$categorie] < 0)
              {
                $grafiekTonen = false;
              }
      
            }
          }
          
          
          
          if ($grafiekTonen == true)
          {
            $this->printPie($grafiekdata, 30 + $xOffset, $yPagina, $doorkijkTitels[$doorkijkCategorieSoort]);
          } //+$yOffset);
    
        }
        $kol++;
      }
  
      $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
      
      $yPagina+=50;
      if($yPagina<190)
        $this->pdf->Rect($this->pdf->marge, $yPagina-5, $this->pdf->w-$this->pdf->marge*2 ,0.5 , 'F');

      //echo "$yPagina <br>\n"; ob_flush();
    }
  
    $this->portefeuille=$portefeuilleBackup;
   // exit;
  }

	function vulPagina($belCategorie='')
	{
		$pieTeller = 0;

		$doorkijkCategorieSoorten=array('Beleggingscategorien','Regios','Beleggingssectoren');
		//$doorkijkTitels=array('Beleggingscategorien'=>'Beleggingscategorie','Regios'=>'Regio','Beleggingssectoren'=>'Beleggingssector');//array();Beleggingscategorie
    
    
    $doorkijkTitels=array('Beleggingscategorien'=>'Beleggingscategorie','Regios'=>'Regio\'s','Beleggingssectoren'=>'Sectoren',
                          'Rating'=>'Rating','Looptijd'=>'Looptijd ');//array();Beleggingscategorie //.strtolower($this->categorie)
    if($belCategorie=='AAND')
    {
      $doorkijkCategorieSoorten=array('Beleggingscategorien','Regios','Beleggingssectoren');
    }
    elseif($belCategorie=='OBL')
    {
      $doorkijkCategorieSoorten=array('Beleggingscategorien','Rating','Looptijd');
    }
    
		foreach($doorkijkCategorieSoorten as $index=>$doorkijkCategorieSoort)
		{
		  if($doorkijkCategorieSoort=='Beleggingscategorien')
        $filter='';
		  else
		    $filter=$belCategorie;
			$xOffset =  $index * 98;
			$doorKijk= $this->bepaalWeging($doorkijkCategorieSoort,$filter);

			$this->toonTabel($doorKijk['details'],$xOffset,$doorkijkTitels[$doorkijkCategorieSoort],$this->kleuren[$doorkijkCategorieSoort]);
			$grafiekdata=array();
			$grafiekTonen=true;
      $kleuren=$this->kleuren[$doorkijkCategorieSoort];
      
      foreach($doorKijk['categorien'] as $categorie=>$data)
        if(!isset($kleuren[$categorie]))
          $kleuren[$categorie]=array(128,128,128);
        
      foreach($kleuren as $categorie=>$kleur)
      {
        //foreach($regels as $categorie=>$data)
        if (isset($doorKijk['categorien'][$categorie]))
        {

          $grafiekdata[$categorie]['kleur'] = $kleur;//array('R' => array('value' => $kleuren[$categorie][0]),'G' => array('value' => $kleuren[$categorie][1]),'B' => array('value' => $kleuren[$categorie][2]));
          $grafiekdata[$categorie]['percentage'] = $doorKijk['categorien'][$categorie];
          if ($doorKijk['categorien'][$categorie] < 0)
          {
            $grafiekTonen = false;
          }
  
        }
      }
			if($grafiekTonen==true)
				$this->printPie( $grafiekdata,20+$xOffset,125,'');//$doorkijkTitels[$doorkijkCategorieSoort]); //+$yOffset);
			$pieTeller++;
		}

		if($this->debug)
		{
			$wegingSoorten=array('DoorkijkfondsWeging','MSfondsWeging','NietGekoppeld','afwijkingWeging');
			$vertaling=array('afwijkingWeging'=>'afwijkingWeging som<>100%');
			foreach($wegingSoorten as $wegingSoort)
			{
				$this->pdf->addPage();
				$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
				$this->pdf->setWidths(array(1,50,50,30));
				if(isset($vertaling[$wegingSoort]))
				{
					$this->pdf->row(array('', $vertaling[$wegingSoort]));
					$this->pdf->excelData[]=array($vertaling[$wegingSoort]);
				}
				else
				{
					$this->pdf->row(array('', $wegingSoort));
					$this->pdf->excelData[]=array($wegingSoort);
				}
				$startPage=$this->pdf->page;
				foreach($doorkijkCategorieSoorten as $index=>$doorkijkCategorieSoort)
				{
					$this->pdf->setWidths(array(1,45,45,15,30));
					$this->pdf->setAligns(array('L','L','L','R','R'));
					$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
					$this->pdf->row(array('',$doorkijkCategorieSoort));

					$categorieTotalen=array();
					$this->pdf->row(array('','fonds','categorie', 'perc','waarde'));
					$this->pdf->excelData[]=array('fonds','categorie', 'perc','waarde');
					$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
					foreach($this->debugData[$wegingSoort][$doorkijkCategorieSoort] as $fonds=>$verdelingData)
					{
						$n=0;
						foreach($verdelingData as $categorie=>$percentage)
						{
							if($wegingSoort=='afwijkingWeging')
							{
								$weging = $this->formatGetal($percentage['weging'], 5);
								$waarde = '';
							}
							else
							{
								$weging = $this->formatGetal($percentage['weging'], 2);
								$waarde = $this->formatGetal($percentage['waarde'], 2);
							}
							if($n==0)
							{
								$this->pdf->row(array('', $fonds, $categorie, $weging, $waarde));
								$this->pdf->excelData[]=array($fonds, $categorie, round($percentage['weging'],5), round($percentage['waarde'],2));
							}
							else
							{
								$this->pdf->row(array('', '', $categorie, $weging, $waarde));
								$this->pdf->excelData[]=array('', $categorie, round($percentage['weging'],5), round($percentage['waarde'],2));
							}
							$categorieTotalen[$categorie]['waarde']+=$percentage['waarde'];
							$n++;
						}
					}
					$this->pdf->ln();
					$this->pdf->excelData[]=array();
					foreach($categorieTotalen as $categorie=>$waarden)
					{
						$this->pdf->row(array('', 'Totaal', $categorie, '', $this->formatGetal($waarden['waarde'], 2)));
						$this->pdf->excelData[]=array('Totaal', $categorie, '', round($waarden['waarde'], 2));
					}
					$this->pdf->ln();
					$this->pdf->excelData[]=array();

				}
			}
			//	listarray($this->debugData);
		}
	}

	function writeRapport()
	{
		$db=new DB();
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$query = "SELECT doorkijkCategorie,doorkijkCategorieSoort,grafiekKleur, afdrukVolgorde
                   FROM doorkijk_categoriePerVermogensbeheerder 
                   WHERE Vermogensbeheerder='$beheerder'
                   ORDER BY doorkijkCategorieSoort,afdrukVolgorde 
                  ";

		$db->SQL($query);
		$db->Query();
		$this->kleuren=array();
		while($data = $db->nextRecord())
		{
			$this->kleuren[$data['doorkijkCategorieSoort']][$data['doorkijkCategorie']]=unserialize($data['grafiekKleur']);
		}
    
    
    $query="SELECT
doorkijk_categoriePerVermogensbeheerder.Vermogensbeheerder,
doorkijk_categoriePerVermogensbeheerder.doorkijkCategoriesoort,
doorkijk_categoriePerVermogensbeheerder.doorkijkCategorie,
doorkijk_categoriePerVermogensbeheerder.min,
doorkijk_categoriePerVermogensbeheerder.max
FROM
doorkijk_categoriePerVermogensbeheerder
WHERE doorkijk_categoriePerVermogensbeheerder.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY doorkijk_categoriePerVermogensbeheerder.doorkijkCategoriesoort,doorkijk_categoriePerVermogensbeheerder.afdrukVolgorde,doorkijk_categoriePerVermogensbeheerder.min,doorkijk_categoriePerVermogensbeheerder.doorkijkCategorie";
    $db->SQL($query); //echo $query."<br>\n";exit;
    $db->Query();
    
    while($row = $db->nextRecord())
    {
      $this->buckets[$row['doorkijkCategoriesoort']][$row['doorkijkCategorie']]=$row;
    }
    
    
    if(is_array($this->pdf->portefeuilles))
      $this->consolidatie=true;
    else
      $this->consolidatie=false;
    
    if($this->pdf->rapport_type=='DOORKIJK')
    {
      $categorieOmschrijving='Aandelen';
      $categorie='AAND';
    }
    else
    {
      $categorieOmschrijving='Obligaties';
      $categorie='OBL';
    }
    $this->pdf->rapport_titel = $categorieOmschrijving;
    $this->pdf->AddPage();
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge+92, 40,0.5, 150 , 'F');
    
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    $this->vulPagina($categorie);
    
    if($this->consolidatie==true)
    {
      $this->pdf->rapport_titel = $categorieOmschrijving;
      $this->pdf->AddPage();
      $this->vulConsolidatiePagina($categorieOmschrijving);
    }

	}
}
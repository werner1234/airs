<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/02/29 16:23:08 $
File Versie					: $Revision: 1.3 $

$Log: RapportDOORKIJK_L51.php,v $
Revision 1.3  2020/02/29 16:23:08  rvv
*** empty log message ***

Revision 1.2  2019/10/30 16:45:39  rvv
*** empty log message ***

Revision 1.1  2019/09/14 17:09:05  rvv
*** empty log message ***

Revision 1.12  2018/03/17 18:47:40  rvv
*** empty log message ***

Revision 1.11  2018/03/11 10:52:28  rvv
*** empty log message ***

Revision 1.10  2018/02/19 13:56:33  rvv
*** empty log message ***

Revision 1.9  2018/02/18 14:58:56  rvv
*** empty log message ***

Revision 1.8  2018/02/05 07:36:46  rvv
*** empty log message ***

Revision 1.7  2018/02/04 15:46:22  rvv
*** empty log message ***

Revision 1.6  2018/01/24 17:06:34  rvv
*** empty log message ***

Revision 1.5  2017/12/16 18:43:36  rvv
*** empty log message ***

Revision 1.4  2017/12/02 19:13:04  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportDOORKIJK_L51
{
	function RapportDOORKIJK_L51($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = $pdf;
		$this->pdf->rapport_type = "DOORKIJK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
  	$this->pdf->rapport_titel = "Doorkijk totale portefeuille";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->underlinePercentage=0.8;
		if($this->pdf->lastPOST['debug'])
		  $this->debug=true;
		else
			$this->debug=false;
		$this->debugData=array();
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
							$wegingDoorkijkCategorie[$row['doorkijkCategorie']]['weging'] = $wegingPerMsCategorie[$categorie];
							$wegingDoorkijkCategorie[$row['doorkijkCategorie']]['waarde'] = $waarde * $wegingPerMsCategorie[$categorie] / 100;

							if ($this->debug)
							{
							
								$this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['weging'] = $wegingPerMsCategorie[$categorie];
								$this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['waarde'] = $waarde * $wegingPerMsCategorie[$categorie] / 100;
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
                  WHERE rapportageDatum ='" . $this->rapportageDatum . "' $fondsFilter AND portefeuille = '" . $this->portefeuille . "'" .	$__appvar['TijdelijkeRapportageMaakUniek'];
		$db->SQL($query);
		$db->Query();
		$totaalWaarde = $db->nextRecord();

		$vertaling=array('Beleggingscategorien'=>'Beleggingscategorie','Beleggingssectoren'=>'Beleggingssector','Regios'=>'Regio');
		$query = "SELECT fonds,rekening, actuelePortefeuilleWaardeEuro as waardeEUR, ".$vertaling[$doorkijkSoort]." as airsSoort
					FROM TijdelijkeRapportage	WHERE rapportageDatum ='".$this->rapportageDatum."'  $fondsFilter AND portefeuille = '" . $this->portefeuille . "'" .	$__appvar['TijdelijkeRapportageMaakUniek']." Order by fonds";

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
		$this->pdf->setXY($this->pdf->marge,120);
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('',vertaalTekst($titel ,$this->pdf->rapport_taal),vertaalTekst('Waarde EUR',$this->pdf->rapport_taal),vertaalTekst('in %',$this->pdf->rapport_taal)));
		$this->pdf->excelData[]=array($titel,'Waarde EUR','in %');
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
				$this->pdf->rect($this->pdf->getX() + $xOffset, $this->pdf->getY() + 1, 2, 2, 'F', '', $kleuren[$categorie]);
				$this->pdf->row(array('', vertaalTekst($categorie ,$this->pdf->rapport_taal), $this->formatGetal($data['waardeEUR'], 0), $this->formatGetal($data['percentage'], 2)));
				$this->pdf->excelData[] = array($categorie, round($data['waardeEUR'], 0), round($data['percentage'], 2));
				$totalen['waardeEUR'] += $data['waardeEUR'];
				$totalen['percentage'] += $data['percentage'];
			}
		}
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('','', 'SUB', 'SUB');
    $this->pdf->row(array('',vertaalTekst('Totaal',$this->pdf->rapport_taal), $this->formatGetal($totalen['waardeEUR'], 0), $this->formatGetal($totalen['percentage'], 2)));
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
				$sorted 		= array();
				$percentages 	= array();
				$kleur			= array();
				$valuta 		= array();

			//$kleurdata=	array_reverse($kleurdata);
		//	listarray($kleurdata);
				foreach($kleurdata as $key=>$data)
				{
					$percentages[] 	= $data['percentage'];
					$kleur[] 			= $data['kleur'];
					$valuta[] 		= $key;
				}
				//arsort($percentages);

				foreach($percentages as $key=>$percentage)
				{
					$sorted[$valuta[$key]]['kleur']=$kleur[$key];
					$sorted[$valuta[$key]]['percentage']=$percentage;
				}
				$kleurdata = $sorted; //columnSort($kleurdata, 'pecentage');

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
    
    /*
    if(min($grafiekData['OIR']['Percentage']) < 0)
      $this->BarDiagram($chartsize+$extraBarW,$chartsize,$grafiekData['OIR']['Percentage'],'%l',$grafiekData['OIR']['Kleur'],vertaalTekst('Regio',$this->pdf->rapport_taal));
    else
    {
      $legendaStart=$this->correctLegentHeight(count($grafiekData['OIR']['Percentage']));
      PieChart_L51($this->pdf,$chartsize,$vwh,$grafiekData['OIR']['Percentage'],'%l',$grafiekData['OIR']['Kleur'],vertaalTekst('Regio',$this->pdf->rapport_taal),$legendaStart);
    }
*/
    
    if($this->pdf->rapport_printpie)
		{
			$this->pdf->SetXY($xstart, $ystart);
			$y = $this->pdf->getY();
      PieChart_L51($this->pdf,50,50,$pieData,'%l',$grafiekKleuren,vertaalTekst($titel,$this->pdf->rapport_taal),'geen');
      $this->pdf->setY($y);
      $this->pdf->SetLineWidth($this->pdf->lineWidth);
		}
		else
    {
      $this->pdf->SetXY($xstart, $ystart);
      $y = $this->pdf->getY();
      $this->BarDiagram(50,50,$pieData,'%l',$grafiekKleuren,vertaalTekst($titel,$this->pdf->rapport_taal));
      $this->pdf->setY($y);
      $this->pdf->SetLineWidth($this->pdf->lineWidth);
    }
	}
  
  function BarDiagram($w, $h, $data, $format,$colorArray,$titel)
  {
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
   // $this->SetLegends2($data,$format);
    $this->pdf->SetLegends($data,$format);
    
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
   
    $this->pdf->SetLineWidth(0.2);
    //$this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $nullijn=$XDiag - ($offset * $unit);
    
   
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

    

  }

	function vulPagina($belCategorien='')
	{
		$pieTeller = 0;

		$doorkijkCategorieSoorten=array('Beleggingscategorien','Regios','Beleggingssectoren');

		$doorkijkTitels=array('Beleggingscategorien'=>'Beleggingscategorie','Regios'=>'Regio','Beleggingssectoren'=>'Beleggingssector');//array();Beleggingscategorie
		foreach($doorkijkCategorieSoorten as $index=>$doorkijkCategorieSoort)
		{
			$xOffset =  $index * 98;
			$doorKijk= $this->bepaalWeging($doorkijkCategorieSoort,$belCategorien);

			$this->toonTabel($doorKijk['details'],$xOffset,$doorkijkTitels[$doorkijkCategorieSoort],$this->kleuren[$doorkijkCategorieSoort]);
			$grafiekdata=array();
		
			foreach($this->kleuren[$doorkijkCategorieSoort] as $categorie=>$kleurdata) //foreach($doorKijk['categorien'] as $categorie=>$percentage)
			{
				if(isset($doorKijk['categorien'][$categorie]))
				{
					$percentage=$doorKijk['categorien'][$categorie];
					$grafiekdata[$categorie]['kleur'] = $this->kleuren[$doorkijkCategorieSoort][$categorie];//array('R' => array('value' => $kleuren[$categorie][0]),'G' => array('value' => $kleuren[$categorie][1]),'B' => array('value' => $kleuren[$categorie][2]));
					$grafiekdata[$categorie]['percentage'] = $percentage;

				}
  		}

			  $this->printPie( $grafiekdata,30+$xOffset,40,$doorkijkTitels[$doorkijkCategorieSoort]); //+$yOffset);
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
  
  function setDoorkijk()
  {
    global $__appvar;
    $this->echtePortefeuille = $this->portefeuille;
    if ($this->pdf->lastPOST['doorkijk'] == 1)
    {
      include_once($__appvar["basedir"]."/classes/portefeuilleVerdieptClass.php");
      $this->verdiept = new portefeuilleVerdiept($this->pdf,$this->portefeuille,$this->rapportageDatum);
      $verdiepteFondsen = $this->verdiept->getFondsen();
      
      foreach ($verdiepteFondsen as $fonds)
      {
        $this->verdiept->bepaalVerdeling($fonds, $this->verdiept->FondsPortefeuilleData[$fonds], array('fonds'), $this->rapportageDatum);
      }

      if (substr($this->rapportageDatum, 5, 5) == '01-01')
      {
        $startjaar = true;
      }
      else
      {
        $startjaar = false;
      }
      
      $fondswaarden = berekenPortefeuilleWaarde($this->portefeuille, $this->rapportageDatum, $startjaar, 'EUR', substr($this->rapportageDatum, 0, 4) . '-01-01');
      $correctieVelden = array('totaalAantal', 'ActuelePortefeuilleWaardeEuro', 'actuelePortefeuilleWaardeInValuta', 'beginPortefeuilleWaardeEuro', 'beginPortefeuilleWaardeInValuta');
      foreach ($fondswaarden as $i => $fondsData)
      {
        if (isset($this->pdf->fondsPortefeuille[$fondsData['fonds']]))
        {
          
          $fondsWaardeEigen = $fondsData['actuelePortefeuilleWaardeEuro'];
          $fondsWaardeHuis = $this->pdf->fondsPortefeuille[$fondsData['fonds']]['totaalWaarde'];
          $aandeel = $fondsWaardeEigen / $fondsWaardeHuis;
          unset($fondswaarden[$i]);
          foreach ($this->pdf->fondsPortefeuille[$fondsData['fonds']]['verdeling'] as $type => $details)
          {
            foreach ($details as $element => $emementDetail)
            {
              
              if (isset($emementDetail['overige']))
              {
                foreach ($correctieVelden as $veld)
                {
                  $emementDetail['overige'][$veld] = $emementDetail['overige'][$veld] * $aandeel;
                }
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
      $fondswaarden = array_values($fondswaarden);
      $tmp = array();
      foreach ($fondswaarden as $mixedInstrument)
      {
        $instrument = array();
        foreach ($mixedInstrument as $index => $value)
        {
          $instrument[strtolower($index)] = $value;
        }
        unset($instrument['voorgaandejarenactief']);
        
        $key = '|' . $instrument['type'] . '|' . $instrument['fonds'] . '|' . $instrument['rekening'] . '|';
        if (isset($tmp[$key]))
        {
          foreach ($correctieVelden as $veld)
          {
            $veld = strtolower($veld);
            $tmp[$key][$veld] += $instrument[$veld];
          }
        }
        else
        {
          $tmp[$key] = $instrument;
        }
      }
      $fondswaarden = array_values($tmp);

      $this->portefeuille = 'v' . $this->portefeuille;
      vulTijdelijkeTabel($fondswaarden, $this->portefeuille, $this->rapportageDatum);
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
    
    $this->setDoorkijk();

		$this->pdf->AddPage();
    $this->pdf->templateVars['DOORKIJKPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['DOORKIJKPaginas']=$this->pdf->rapport_titel;
		$this->vulPagina();


		$beleggingscategorien=array('AAND');
		if(count($beleggingscategorien)>0)
		{
			$this->pdf->rapport_titel = "Doorkijk aandelenportefeuille";
			$this->pdf->AddPage();
      $this->pdf->templateVars['DOORKIJK2Paginas']=$this->pdf->page;
      $this->pdf->templateVarsOmschrijving['DOORKIJK2Paginas']=$this->pdf->rapport_titel;
			$this->vulPagina($beleggingscategorien);
		}
	}
}
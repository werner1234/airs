<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/02/29 16:23:08 $
File Versie					: $Revision: 1.6 $

$Log: RapportDOORKIJKVR_L49.php,v $
Revision 1.6  2020/02/29 16:23:08  rvv
*** empty log message ***

Revision 1.5  2019/09/07 16:07:48  rvv
*** empty log message ***

Revision 1.4  2019/09/04 15:31:42  rvv
*** empty log message ***

Revision 1.3  2019/06/22 16:31:44  rvv
*** empty log message ***

Revision 1.2  2019/06/19 15:59:09  rvv
*** empty log message ***

Revision 1.1  2019/06/15 20:53:26  rvv
*** empty log message ***

Revision 1.1  2018/06/30 17:43:55  rvv
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

class RapportDOORKIJKVR_L49
{
	function RapportDOORKIJKVR_L49($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = $pdf;
		$this->pdf->rapport_type = "DOORKIJKVR";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
  	$this->pdf->rapport_titel = '';//"Allocaties inclusief uitsplitsing";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->underlinePercentage=0.8;
		if($this->pdf->lastPOST['debug'])
		  $this->debug=true;
		else
			$this->debug=false;
		$this->debugData=array();
    $this->buckets=array();
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
      

		  if(in_array($doorkijkSoort,array('Rating','Coupon','Looptijd')))
      {
			$query = "SELECT doorkijkCategorie FROM doorkijk_koppelingPerVermogensbeheerder 
WHERE bronKoppeling='$airsCategorie' AND doorkijkCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND systeem='AIRS' AND vermogensbeheerder='". $this->pdf->portefeuilledata['Vermogensbeheerder']."'";
			$db->executeQuery($query);
        if($db->records())
        {

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

	function bepaalWeging($doorkijkSoort,$belCategorien=array())
	{
		global $__appvar;
		if(is_array($belCategorien) && count($belCategorien)>0)
			$fondsFilter="AND (Beleggingscategorie IN('".implode("','",$belCategorien)."') OR TijdelijkeRapportage.type='rekening')";
		else
			$fondsFilter='';

		$db = new DB();
    $db2=new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal 
                  FROM TijdelijkeRapportage 
                  WHERE rapportageDatum ='" . $this->rapportageDatum . "' $fondsFilter AND portefeuille = '" . $this->portefeuille . "'" .	$__appvar['TijdelijkeRapportageMaakUniek'];
		$db->SQL($query);
		$db->Query();
		$totaalWaarde = $db->nextRecord();

   
    $vertaling=array('Looptijd'=>'datediff(TijdelijkeRapportage.Lossingsdatum,TijdelijkeRapportage.rapportageDatum)/365.2421','Coupon'=>'TijdelijkeRapportage.rentePercentage','Rating'=>'Fondsen.Rating');
    
    
		$query = "SELECT TijdelijkeRapportage.fonds,TijdelijkeRapportage.rekening, sum(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as waardeEUR, ".$vertaling[$doorkijkSoort]." as airsSoort
					FROM TijdelijkeRapportage LEFT JOIN Fondsen ON TijdelijkeRapportage.Fonds=Fondsen.Fonds
					 WHERE TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatum."'  $fondsFilter AND TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "'" .	$__appvar['TijdelijkeRapportageMaakUniek']."
					  GROUP BY TijdelijkeRapportage.fonds
					  ORDER BY TijdelijkeRapportage.fonds";

		$db->SQL($query);
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
					$totaalPercentage=($percentage['weging'] * ($row['waardeEUR']/$totaalWaarde['totaal']) );
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

		$witCell=$this->pdf->witCell;
		$this->pdf->fillCell = array(0,1,0,1,0,1,0,1,0,1,0,1);
		$this->pdf->SetFillColor($this->pdf->achtergrondKop[0],$this->pdf->achtergrondKop[1],$this->pdf->achtergrondKop[2]);
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		$this->pdf->setWidths(array($xOffset+3,40,$witCell,24,$witCell,15));
		$this->pdf->setAligns(array('L','L','','R','','R'));
		$this->pdf->setXY($this->pdf->marge,35);
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('',$titel,'','Waarde EUR','','in %'));
		$this->pdf->excelData[]=array($titel,'Waarde EUR','in %');
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
	//	$this->pdf->ln(1);
		$totalen=array();
	//	listarray($kleuren);
		foreach($regels as $categorie=>$data)
			if(!isset($kleuren[$categorie]))
				$kleuren[$categorie]=array(128,128,128);
		$n=0;

		foreach($kleuren as $categorie=>$kleur)
		{
		//foreach($regels as $categorie=>$data)
			if(isset($regels[$categorie]))
			{

				$data =$regels[$categorie];
				$this->pdf->rect($this->pdf->getX() + $xOffset, $this->pdf->getY() + 2, 2, 2, 'DF', '', $kleuren[$categorie]);
				$n=$this->switchColor($n);
				$this->pdf->row(array('', $categorie, '',$this->formatGetal($data['waardeEUR'], 0), '',$this->formatGetal($data['percentage'], 2)));
				$this->pdf->excelData[] = array($categorie, round($data['waardeEUR'], 0), round($data['percentage'], 2));
				$totalen['waardeEUR'] += $data['waardeEUR'];
				$totalen['percentage'] += $data['percentage'];
			}
		}
		$this->pdf->SetFillColor($this->pdf->achtergrondTotaal[0],$this->pdf->achtergrondTotaal[1],$this->pdf->achtergrondTotaal[2]);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
   // $this->pdf->CellBorders = array('','', '','SUB','', 'SUB');
    $this->pdf->row(array('','Totaal','', $this->formatGetal($totalen['waardeEUR'], 0),'', $this->formatGetal($totalen['percentage'], 2)));
		$this->pdf->excelData[]=array('Totaal', round($totalen['waardeEUR'], 0), round($totalen['percentage'], 2));
		$this->pdf->excelData[]=array();
    $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
	}

	function switchColor($n)
	{
		$col1=$this->pdf->achtergrondLicht;
		$col2=$this->pdf->achtergrondDonker;

		if($n%2==0)
			$this->pdf->SetFillColor($col1[0],$col1[1],$col1[2]);
		else
			$this->pdf->SetFillColor($col2[0],$col2[1],$col2[2]);

		$n++;
		return $n;
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
					$percentages[] 	= $data[percentage];
					$kleur[] 			= $data[kleur];
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
				$pieData[$key] = $value[percentage];
				$a++;
			}
		}
		else
			$grafiekKleuren = $standaardKleuren;

		$this->pdf->SetTextColor($this->pdf->pdf->rapport_fontcolor[r],$this->pdf->pdf->rapport_fontcolor[g],$this->pdf->pdf->rapport_fontcolor[b]);

		$this->pdf->rapport_printpie = true;
		foreach($pieData as $key=>$value)
		{
			if ($value < 0)
					$this->pdf->rapport_printpie = false;
		}

		if($this->pdf->rapport_printpie)
		{
			$this->pdf->SetXY($xstart, $ystart);
			$y = $this->pdf->getY();
			$this->pdf->SetFont($this->pdf->pdf->rapport_font,'b',10);
			$this->pdf->Cell(50,4,vertaalTekst($titel, $this->pdf->rapport_taal),0,1,"C");
			$this->pdf->SetFont($this->pdf->pdf->rapport_font,'',$this->pdf->pdf->rapport_fontsize);
			$this->pdf->SetX($xstart);
			$this->PieChart(100, 50, $pieData, '%l (%p)', $grafiekKleuren);
			$this->pdf->setY($y);
			$this->pdf->SetLineWidth($this->pdf->lineWidth);
		}
	}

	function PieChart($w, $h, $data, $format, $colors=null)
	{

		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
	//	$this->pdf->SetLegends($data,$format);

		$XPage = $this->pdf->GetX();
		$YPage = $this->pdf->GetY();
		$margin = 2;
		$hLegend = 2;
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
/*
		//Legends
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

		$x1 = $XPage ;
		$x2 = $x1 + $hLegend + $margin - 12;
		$y1 = $YDiag + ($radius) + $margin;

		for($i=0; $i<$this->pdf->NbVal; $i++) {
			$this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
			$this->pdf->Rect($x1-12, $y1, $hLegend, $hLegend, 'DF');
			$this->pdf->SetXY($x2,$y1);
			$this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
			$y1+=$hLegend + $margin;
		}
*/
	}

	function vulPagina($belCategorien='')
	{
		$pieTeller = 0;

		$doorkijkCategorieSoorten=array('Looptijd','Coupon','Rating');
    //$doorkijkCategorieSoorten=array('Rating');//,'Coupon','Rating');

		$doorkijkTitels=array('Looptijd'=>'Looptijd','Coupon'=>'Coupon','Rating'=>'Rating');
		foreach($doorkijkCategorieSoorten as $index=>$doorkijkCategorieSoort)
		{
			$xOffset =  $index * 87;
			$doorKijk= $this->bepaalWeging($doorkijkCategorieSoort,$belCategorien);

			$this->toonTabel($doorKijk['details'],$xOffset,$doorkijkTitels[$doorkijkCategorieSoort],$this->kleuren[$doorkijkCategorieSoort]);
			$grafiekdata=array();
			$grafiekTonen=true;
			foreach($this->kleuren[$doorkijkCategorieSoort] as $categorie=>$kleurdata) //foreach($doorKijk['categorien'] as $categorie=>$percentage)
			{
				if(isset($doorKijk['categorien'][$categorie]))
				{
					$percentage=$doorKijk['categorien'][$categorie];
					$grafiekdata[$categorie]['kleur'] = $this->kleuren[$doorkijkCategorieSoort][$categorie];//array('R' => array('value' => $kleuren[$categorie][0]),'G' => array('value' => $kleuren[$categorie][1]),'B' => array('value' => $kleuren[$categorie][2]));
					$grafiekdata[$categorie]['percentage'] = $percentage;
					if ($percentage < 0)
					{
						$grafiekTonen = false;
					}
				}
  		}
			if($grafiekTonen==true)
      {
        $this->printPie($grafiekdata, 30 + $xOffset, 125, $doorkijkTitels[$doorkijkCategorieSoort]); //+$yOffset);
      }
      else
      {
          $this->pdf->setXY(20 + $xOffset, 125);
         $this->VBarDiagram2(77, 50, $grafiekdata, '', $doorkijkTitels[$doorkijkCategorieSoort]);
      }
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


		$query="SELECT KeuzePerVermogensbeheerder.waarde as beleggingscategorie
FROM KeuzePerVermogensbeheerder
JOIN Portefeuilles ON KeuzePerVermogensbeheerder.vermogensbeheerder = Portefeuilles.Vermogensbeheerder  
WHERE portefeuille='".$this->portefeuille ."' AND 
KeuzePerVermogensbeheerder.categorieIXP='Obligaties' ";
		$db->SQL($query);
		$db->Query();
		$beleggingscategorien=array();
		while($data = $db->nextRecord())
		{
			$beleggingscategorien[]=$data['beleggingscategorie'];
		}

		if(count($beleggingscategorien)>0)
		{
			$this->pdf->rapport_titel = "";//Allocaties inclusief uitsplitsing vastrentende waarden";

			$this->pdf->AddPage();
			$this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0));

			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
			$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
			$this->pdf->setY($this->pdf->rapportYstart+2);
			$this->pdf->Cell(100,4,'Allocaties inclusief uitsplitsing vastrentende waarden');
			$this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array($this->pdf->koplijn[0],$this->pdf->koplijn[1],$this->pdf->koplijn[2]),'dash'=>0));
			$this->pdf->Line($this->pdf->marge,$this->pdf->rapportYstart+$this->pdf->rowHeight+3,297-$this->pdf->marge,$this->pdf->rapportYstart+$this->pdf->rowHeight+3);

			$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
			$this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,0),'dash'=>0));

			$this->vulPagina($beleggingscategorien);
		}
	}
  
  
  function VBarDiagram2($w, $h, $data,$datalijn,$titel,$procent=true,$legendaLocatie='U')
  {
    global $__appvar;


    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();

    
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->setXY($XPage,$YPage+2);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Cell($w,4,$titel,0,1,'L');
    $this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array($this->pdf->koplijn[0],$this->pdf->koplijn[1],$this->pdf->koplijn[2]),'dash'=>0));
    $this->pdf->line($XPage,$YPage+$this->pdf->rowHeight+3,$XPage+$w,$YPage+$this->pdf->rowHeight+3);

    $YPage=$YPage+$h+15;

    $maxVal=1;
    $minVal=-1;
    foreach($data as $categorie=>$waarden)
     {
        
        if($waarden['percentage'] > $maxVal)
          $maxVal=ceil($waarden['percentage'] );
        if($waarden['percentage']  < $minVal)
          $minVal=floor($waarden['percentage'] );
     }
    
    if($procent==false)
      $maxVal=ceil($maxVal/pow(10,strlen($maxVal)-1))*pow(10,strlen($maxVal)-1);
    else
      $maxVal=ceil($maxVal/5)*5;
//echo $max;exit;
//echo "$minVal <br>\n";
    $minVal=floor($minVal/.5)*.5;
//
//echo "$minVal <br>\n<br>\n";
    $numBars = 1;//count($legenda);
    $color=array(155,155,155);
    
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
//      $XPage = $this->pdf->GetX();
//      $YPage = $this->pdf->GetY()+$h+15;
    $margin = 0;
    $margeLinks=10;
    $XPage+=$margeLinks;
    $w-=$margeLinks;
    
    $YstartGrafiek = $YPage - floor($margin * 1);
    $hGrafiek = ($h - $margin * 1);
    $XstartGrafiek = $XPage + $margin * 1 ;
    $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

    if($minVal < 0)
    {
      $unit = $hGrafiek / (-1 * $minVal + $maxVal) * -1;
      $nulYpos =  $unit * (-1 * $minVal);
    }
    else
    {
      $unit = $hGrafiek / $maxVal * -1;
      $nulYpos =0;
    }
    
    
    $horDiv = 4;
    $horInterval = $hGrafiek / $horDiv;
    $bereik = $hGrafiek/$unit;
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    
    $stapgrootte = round(abs($bereik)/$horDiv);
    $top = $YstartGrafiek-$h;
    $bodem = $YstartGrafiek;
    $absUnit =abs($unit);
    
    $nulpunt = $YstartGrafiek + $nulYpos;
    $n=0;
    
    if($procent==true)
      $legendaEnd=' %';
    else
      $legendaEnd='';
    
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
      $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1).$legendaEnd,0,0,'R');
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    for($i=$nulpunt; round($i) >= $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
      {
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte).$legendaEnd,0,0,'R');
      }
      $n++;
      if($n >20)
        break;
    }
    
    
    
    if($numBars > 0)
      $this->pdf->NbVal=$numBars;
    
    $vBar = ($bGrafiek);// / ($this->pdf->NbVal + 1));

    $eBaton = ($vBar * .8);
    
    
    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $aantalCategorien=count($data);
    $catCount=0;
     
      foreach($data as $categorie=>$gegevens)
      {

          $val=$gegevens['percentage'];
          $lval = $eBaton/$aantalCategorien;
          $xval = $XstartGrafiek + ($catCount * $lval)+ $vBar *.1 ;
          $yval = $YstartGrafiek + $nulYpos ;
          $hval = ($val * $unit);
          
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$gegevens['kleur']);
          

          $catCount++;
    
      }

    
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
}
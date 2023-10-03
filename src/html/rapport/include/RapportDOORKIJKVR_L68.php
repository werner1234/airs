<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/14 18:42:03 $
File Versie					: $Revision: 1.22 $

$Log: RapportDOORKIJKVR_L68.php,v $
Revision 1.22  2020/03/14 18:42:03  rvv
*** empty log message ***

Revision 1.21  2020/03/07 14:41:15  rvv
*** empty log message ***

Revision 1.20  2020/03/04 16:40:47  rvv
*** empty log message ***

Revision 1.19  2020/02/29 16:23:08  rvv
*** empty log message ***

Revision 1.18  2019/12/07 17:48:23  rvv
*** empty log message ***

Revision 1.17  2019/11/20 16:19:15  rvv
*** empty log message ***

Revision 1.16  2019/11/02 15:20:30  rvv
*** empty log message ***

Revision 1.15  2019/10/30 16:44:17  rvv
*** empty log message ***

Revision 1.14  2019/10/12 18:03:34  rvv
*** empty log message ***

Revision 1.13  2019/10/02 15:12:58  rvv
*** empty log message ***

Revision 1.12  2019/09/07 16:07:48  rvv
*** empty log message ***

Revision 1.11  2019/09/04 15:31:42  rvv
*** empty log message ***

Revision 1.10  2019/07/31 14:45:35  rvv
*** empty log message ***

Revision 1.9  2019/07/24 15:48:45  rvv
*** empty log message ***

Revision 1.8  2019/07/20 16:28:44  rvv
*** empty log message ***

Revision 1.7  2019/07/17 15:34:55  rvv
*** empty log message ***

Revision 1.6  2019/06/23 11:25:08  rvv
*** empty log message ***

Revision 1.5  2019/06/22 16:31:44  rvv
*** empty log message ***

Revision 1.4  2019/06/19 15:59:09  rvv
*** empty log message ***

Revision 1.3  2019/06/15 20:53:26  rvv
*** empty log message ***

Revision 1.2  2019/06/09 15:10:56  rvv
*** empty log message ***

Revision 1.1  2019/06/09 14:52:19  rvv
*** empty log message ***

Revision 1.3  2019/05/22 16:06:13  rvv
*** empty log message ***

Revision 1.2  2019/04/03 15:51:38  rvv
*** empty log message ***

Revision 1.1  2019/03/06 16:13:08  rvv
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

class RapportDOORKIJKVR_L68
{
	function RapportDOORKIJKVR_L68($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = $pdf;
		$this->pdf->rapport_type = "DOORKIJKVR";
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
    
    
		$query = "SELECT TijdelijkeRapportage.Beleggingscategorie, TijdelijkeRapportage.fonds,TijdelijkeRapportage.rekening, sum(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as waardeEUR, ".$vertaling[$doorkijkSoort]." as airsSoort
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
  //echo $row['fonds']." : ".$row['airsSoort']."<br>\n";
        $bucketFound=false;
        foreach($this->buckets[$doorkijkSoort] as $bucket=>$bucketSettings)
        {
          if(($row['airsSoort']>=$bucketSettings['min'] && $row['airsSoort']<$bucketSettings['max']) || ($bucketSettings['min']==$row['airsSoort'] && $bucketSettings['max']==$row['airsSoort']))
          {
            $row['airsSoort']=$bucket;
            $bucketFound=true;
            break;
          }
        }
       // echo $row['fonds']." : $bucketFound : ".$row['airsSoort']."<br><br>\n";
        if(($row['airsSoort']==0 && $bucketFound==false ) || $row['Beleggingscategorie']=='Lening int' || $row['Beleggingscategorie']=='VARBEZ')
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
//echo $doorkijkSoort. " ".$row['fonds']."<br>\n";
//				listarray($verdeling);
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
          $overige = true;
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

	function toonTabel($regels,$xOffset,$yStart,$titel,$kleuren,$maxRegels)
	{
		$this->pdf->setWidths(array($xOffset+12,25,25,15));
		$this->pdf->setAligns(array('L','L','R','R'));
		$this->pdf->setXY($this->pdf->marge,125);
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('',$titel,'Waarde EUR','in %'));
		$this->pdf->excelData[]=array($titel,'Waarde EUR','in %');
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
		$this->pdf->ln(1);
		$totalen=array();
	//	listarray($kleuren);
		foreach($regels as $categorie=>$data)
			if(!isset($kleuren[$categorie]))
				$kleuren[$categorie]=array(128,128,128);

		$getoondeRegels=0;

		foreach($kleuren as $categorie=>$kleur)
		{
		//foreach($regels as $categorie=>$data)
			if(isset($regels[$categorie]))
			{
				$data =$regels[$categorie];
				$this->pdf->rect($this->pdf->getX() + $xOffset+8, $this->pdf->getY() + 1, 2, 2, 'DF', '', $kleuren[$categorie]);
				$this->pdf->row(array('', $categorie, $this->formatGetal($data['waardeEUR'], 0), $this->formatGetal($data['percentage'], 1)));
				$this->pdf->excelData[] = array($categorie, round($data['waardeEUR'], 0), round($data['percentage'], 1));
				$totalen['waardeEUR'] += $data['waardeEUR'];
				$totalen['percentage'] += $data['percentage'];
        $getoondeRegels++;
			}
		}
		$witregels=$maxRegels-$getoondeRegels;
		if($witregels>0)
		  $this->pdf->ln($witregels*$this->pdf->rowHeight);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('','', 'TS', 'TS');
    $this->pdf->row(array('','Totaal', $this->formatGetal($totalen['waardeEUR'], 0), $this->formatGetal($totalen['percentage'], 1).''));
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
				$pieData[$key] = $value['percentage'];
				$a++;
			}
		}
		else
			$grafiekKleuren = $standaardKleuren;

		$this->pdf->SetTextColor(255,255,255);

		$this->pdf->rapport_printpie = true;
		foreach($pieData as $key=>$value)
		{
			if ($value < 0)
					$this->pdf->rapport_printpie = false;
		}

		if($this->pdf->rapport_printpie)
		{
			$this->pdf->SetXY($xstart, $ystart-4);
			$y = $this->pdf->getY();
			$this->pdf->SetFont($this->pdf->rapport_font,'b',10);
			$this->pdf->Cell(50,4,vertaalTekst($titel, $this->pdf->rapport_taal),0,1,"C");
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			
      $this->pdf->SetXY($xstart, $ystart+2);
			$this->PieChart(100, 55, $pieData, '%l (%p)', $grafiekKleuren);
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
		$radius = min($w - $margin * 2 , $h - $margin * 2); //
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

		foreach($data as $val)
		{
			//$angle = round(($val * 360) / doubleval($sum),1);
      $angle = round(  floor( ($val * 360) / doubleval($sum)*5 )/5  ,1);
      if ($angle > 1)
   		{
				$angleEnd = $angleStart + $angle;
				$this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
				$this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
				$angleStart += $angle;
			}
			else
      {
        $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
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

		$doorkijkCategorieSoorten=array('Looptijd','Rating','Coupon');
    //$doorkijkCategorieSoorten=array('Rating');//,'Coupon','Rating');

		$doorkijkTitels=array('Looptijd'=>'Looptijd','Rating'=>'Rating','Coupon'=>'Coupon');
		foreach($doorkijkCategorieSoorten as $index=>$doorkijkCategorieSoort)
		{
			$categorieRegels=0;
			$xOffset =  $index * 90;
			$doorKijk= $this->bepaalWeging($doorkijkCategorieSoort,$belCategorien);
      $doorkijkWaarden[$doorkijkCategorieSoort]=$doorKijk;
      //listarray($this->kleuren[$doorkijkCategorieSoort]);listarray($doorKijk);
			$grafiekdata=array();
			$extraTekst=false;
			
      foreach($this->kleuren[$doorkijkCategorieSoort] as $categorie=>$kleur)
      {
        if(isset($doorKijk['categorien'][$categorie]))
        {
					$categorieRegels++;
          if ($doorKijk['categorien'][$categorie] >= 0)
          {
            $grafiekdata[$categorie]['kleur'] = $this->kleuren[$doorkijkCategorieSoort][$categorie];//array('R' => array('value' => $kleuren[$categorie][0]),'G' => array('value' => $kleuren[$categorie][1]),'B' => array('value' => $kleuren[$categorie][2]));
            $grafiekdata[$categorie]['percentage'] = $doorKijk['categorien'][$categorie];
          }
          else
          {
            $extraTekst = true;
            
          }
        }
      }

      if($extraTekst==true)
      {
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize-2);
        //$this->pdf->text(17 + $xOffset, 35 + 60, 'Alleen categorieën met een positieve waarde zijn in de grafiek weergegeven');
				$this->pdf->setXY(32+$xOffset,32 + 60);
				$this->pdf->MultiCell(50,3,'Alleen categorieën met een positief percentage zijn in de grafiek weergegeven',false,'C');
      }
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $grafiekReturn = $this->printPie($grafiekdata, 30 + $xOffset, 35, $doorkijkTitels[$doorkijkCategorieSoort]); //+$yOffset);
    
      
      $pieTeller++;
      $maxY=max($maxY,$grafiekReturn['maxY']);
      $maxRegels=max($maxRegels,$categorieRegels);

		}
  	
    foreach($doorkijkCategorieSoorten as $index=>$doorkijkCategorieSoort)
    {
      $xOffset =  $index * 93 -3;
     // if($index==0)
     //   $xOffset=-3;
      $doorKijk=$doorkijkWaarden[$doorkijkCategorieSoort];
      $this->toonTabel($doorKijk['details'],$xOffset,$yStart,$doorkijkTitels[$doorkijkCategorieSoort],$this->kleuren[$doorkijkCategorieSoort],$maxRegels);
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
   
      $this->verdiept = new portefeuilleVerdiept($this->pdf,$this->portefeuille,$this->rapportageDatum);
      $verdiepteFondsen = $this->verdiept->getFondsen();

      foreach ($verdiepteFondsen as $fonds)
      {
        $this->verdiept->bepaalVerdeling($fonds, $this->verdiept->FondsPortefeuilleData[$fonds], array('fonds'), $this->rapportageDatum);
      }

//		listarray($verdiepteFondsen);exit;
//    listarray($this->verdiept->FondsPortefeuilleData);
//		listarray($this->pdf->fondsPortefeuille);
      
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
        //
        if (isset($this->pdf->fondsPortefeuille[$fondsData['fonds']]))
        {
          
          $fondsWaardeEigen = $fondsData['actuelePortefeuilleWaardeEuro'];
          $fondsWaardeHuis = $this->pdf->fondsPortefeuille[$fondsData['fonds']]['totaalWaarde'];
          $aandeel = $fondsWaardeEigen / $fondsWaardeHuis;
          
          //echo $fondsData['fonds'].	" $aandeel=$fondsWaardeEigen/$fondsWaardeHuis ";exit;
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
      $fondswaarden = array_values($fondswaarden);//listarray($fondswaarden);
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
        //	listarray($instrument);
      }
      $fondswaarden = array_values($tmp);


//		listarray($this->pdf->fondsPortefeuille[$fondsData['fonds']]['verdeling'] );
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
    $beleggingscategorien[]='Liquiditeiten';
  
		if(count($beleggingscategorien)>0)
		{
			$this->pdf->rapport_titel = "Onderverdeling vastrentende waarden";
			$this->pdf->AddPage();
      
      $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
      $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
      
      $this->setDoorkijk();
      
			$this->vulPagina($beleggingscategorien);
		}
	}
}
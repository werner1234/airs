<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/03/03 17:12:15 $
File Versie					: $Revision: 1.2 $

$Log: RapportDoorkijkFondsselectie.php,v $
Revision 1.2  2018/03/03 17:12:15  rvv
*** empty log message ***

Revision 1.1  2018/02/28 16:46:55  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportDoorkijkFondsselectie
{
	function RapportDoorkijkFondsselectie( $selectData )
	{
		global $__appvar;
		$this->selectData = $selectData;
		$this->pdf->excelData 	= array();

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "doorkijkFondsselectie";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;
		$this->pdf->__appvar=$__appvar;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);

		$this->pdf->tmdatum = $this->selectData[datumTm];
		// selectdata ook aan PDF geven
		$this->pdf->selectData = $this->selectData;

		$this->rapportageDatum = date('Y-m-d',$this->pdf->tmdatum);
		$this->portefeuille = 'doorkijk';

		$this->orderby = " Client ";
		$this->categorieKleuren=array();
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

	function bepaalWeging($doorkijkSoort)
	{
		global $__appvar;
		$db = new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal 
                  FROM TijdelijkeRapportage 
                  WHERE rapportageDatum ='" . $this->rapportageDatum . "' AND portefeuille = '" . $this->portefeuille . "'" .	$__appvar['TijdelijkeRapportageMaakUniek'];
		$db->SQL($query);
		$db->Query();
		$totaalWaarde = $db->nextRecord();

		$vertaling=array('Beleggingscategorien'=>'Beleggingscategorie','Beleggingssectoren'=>'Beleggingssector','Regios'=>'Regio');
		$query = "SELECT fonds,rekening, actuelePortefeuilleWaardeEuro as waardeEUR, ".$vertaling[$doorkijkSoort]." as airsSoort
					FROM TijdelijkeRapportage	WHERE rapportageDatum ='".$this->rapportageDatum."' AND portefeuille = '" . $this->portefeuille . "'" .	$__appvar['TijdelijkeRapportageMaakUniek']." Order by fonds";

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
		$this->pdf->setWidths(array($xOffset+3,45,15));
		$this->pdf->setAligns(array('L','L','R','R'));
		$this->pdf->setXY($this->pdf->marge,50);
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('',$titel,'in %'));
		$this->pdf->excelData[]=array($titel,'in %');
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
				$this->pdf->rect($this->pdf->getX() + $xOffset, $this->pdf->getY() + 1, 2, 2, 'DF', '', $kleur);
				$this->pdf->row(array('', $categorie, $this->formatGetal($data['percentage'], 2)));
				$this->pdf->excelData[] = array($categorie,  round($data['percentage'], 2));
				$totalen['waardeEUR'] += $data['waardeEUR'];
				$totalen['percentage'] += $data['percentage'];
			}
		}
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('','', 'SUB', 'SUB');
    $this->pdf->row(array('','Totaal', $this->formatGetal($totalen['percentage'], 2)));
		$this->pdf->excelData[]=array('Totaal', round($totalen['percentage'], 2));
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

				foreach($kleurdata as $key=>$data)
				{
					$percentages[] 	= $data[percentage];
					$kleur[] 			= $data[kleur];
					$valuta[] 		= $key;
				}
				arsort($percentages);

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

	function vulTijdelijkeRapportage()
	{
    $regels=$this->berekenPortefeuilleWaarde($this->portefeuille,$this->rapportageDatum);
		vulTijdelijkeTabel($regels,$this->portefeuille,$this->rapportageDatum);
		$totaleWaarde=0;
		$verdeling=array();
		foreach($regels as $index=>$fondsData)
		{
			$totaleWaarde+=$fondsData['actuelePortefeuilleWaardeEuro'];
		}
		$aantalKleuren=count($this->categorieKleuren);

		foreach($regels as $index=>$fondsData)
		{
			$verdeling['categorien'][$fondsData['fondsOmschrijving']]=$fondsData['actuelePortefeuilleWaardeEuro']/$totaleWaarde*100;
			$verdeling['details'][$fondsData['fondsOmschrijving']]=array('percentage'=>$fondsData['actuelePortefeuilleWaardeEuro']/$totaleWaarde*100,'waardeEUR'=>$fondsData['actuelePortefeuilleWaardeEuro']);

			$kleurIndex=$index;
			if($index > $aantalKleuren)
			{
				$kleurIndex=$index-$aantalKleuren;
			}
			$verdeling['kleuren'][$fondsData['fondsOmschrijving']]=$this->categorieKleuren[$kleurIndex];


		}

		return $verdeling;
	}


	function berekenPortefeuilleWaarde($portefeuille,$rapportageDatum)
	{
		$db=new DB();


		$query="SELECT Beleggingscategorien.Beleggingscategorie,Beleggingscategorien.Beleggingscategorie,CategorienPerHoofdcategorie.Hoofdcategorie, Hcat.Omschrijving, Hcat.Afdrukvolgorde FROM
          Beleggingscategorien
          Inner Join CategorienPerHoofdcategorie ON Beleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie
          Inner Join Beleggingscategorien as Hcat ON CategorienPerHoofdcategorie.Hoofdcategorie = Hcat.Beleggingscategorie
          WHERE CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' ";
		$db->SQL($query);
		$db->Query();
		while($data = $db->NextRecord())
		{
			$hoofdVerdeling['Hoofdcategorie']['Omschrijving'][$data['Hoofdcategorie']]=$data['Omschrijving'];
			$hoofdVerdeling['Hoofdcategorie']['Koppeling'][$data['Beleggingscategorie']]=$data['Hoofdcategorie'];
			$hoofdVerdeling['Hoofdcategorie']['Afdrukvolgorde'][$data['Hoofdcategorie']]=$data['Afdrukvolgorde'];
		}

		$query="SELECT Beleggingssectoren.Beleggingssector,SectorenPerHoofdsector.Hoofdsector, Hsec.Omschrijving, Hsec.Afdrukvolgorde FROM
          Beleggingssectoren
          Inner Join SectorenPerHoofdsector ON Beleggingssectoren.Beleggingssector= SectorenPerHoofdsector.Beleggingssector
          Inner Join Beleggingssectoren as Hsec ON SectorenPerHoofdsector.Hoofdsector  = Hsec.Beleggingssector
          WHERE SectorenPerHoofdsector.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
		$db->SQL($query);
		$db->Query();
		while($data = $db->NextRecord())
		{
			$hoofdVerdeling['Hoofdsector']['Omschrijving'][$data['Hoofdsector']]=$data['Omschrijving'];
			$hoofdVerdeling['Hoofdsector']['Koppeling'][$data['Beleggingssector']]=$data['Hoofdsector'];
			$hoofdVerdeling['Hoofdsector']['Afdrukvolgorde'][$data['Hoofdsector']]=$data['Afdrukvolgorde'];
		}

		$verdeling=array();
		$som=0;
		foreach($this->selectData['selectedFondsen'] as $fondsData)
		{

			$parts=explode('|',$fondsData);
			$verdeling[]=array('Fonds'=>$parts[1],'Percentage'=>$parts[0]);
			$som+=$parts[0];
		}
		if($som<100)
			$verdeling[]=array('Fonds'=>'Liquiditeiten','Percentage'=>(100-$som));

		$n=0;
		$totaalPercentage=0;
		$db2=new DB();
		foreach($verdeling as $data)
		{
			$totaalPercentage +=$data['Percentage'];
			$regels[$n]['actuelePortefeuilleWaardeEuro'] = $data['Percentage']*1000;
			$regels[$n]['fonds']=$data['Fonds'];

			if($data['Fonds'] == 'LIQ' || $data['Fonds'] == 'Liquiditeiten')
			{
				$regels[$n]['type'] = 'rekening';
				$regels[$n]['valuta'] = 'EUR';
				$regels[$n]['fondsOmschrijving']='Liquiditeiten';
				$regels[$n]['afmCategorie']='01liquiditeiten';
				$regels[$n]['Beleggingscategorie']='Liquiditeiten';
			}
			else
			{

				$query="SELECT FondsEenheid,Valuta,Omschrijving as fondsOmschrijving FROM Fondsen WHERE Fonds = '".$data['Fonds']."'";
				$db2->SQL($query);
				$fondsData=$db2->lookupRecord();

				$velden=array('Beleggingssector'=>array('tabelFonds'=>'BeleggingssectorPerFonds','tabelVeld'=>'Beleggingssectoren'),
											'AttributieCategorie'=>array('tabelFonds'=>'BeleggingssectorPerFonds','tabelVeld'=>'AttributieCategorien'),
											'Regio'=>array('tabelFonds'=>'BeleggingssectorPerFonds','tabelVeld'=>'Regios'),
											'Beleggingscategorie'=>array('tabelFonds'=>'BeleggingscategoriePerFonds','tabelVeld'=>'Beleggingscategorien'),
											'AfmCategorie'=>array('tabelFonds'=>'BeleggingscategoriePerFonds','tabelVeld'=>'afmCategorien'));

				foreach($velden as $veld=>$veldData)
				{
					$query="SELECT ".$veldData['tabelFonds'].".".$veld.",
        ".$veldData['tabelVeld'].".Afdrukvolgorde as tweedeAfdrukvolgorde,
        KeuzePerVermogensbeheerder.Afdrukvolgorde as eersteAfdrukvolgorde,
        ".$veldData['tabelVeld'].".Omschrijving as ".$veld."Omschrijving
        FROM ".$veldData['tabelFonds']." 
        LEFT JOIN ".$veldData['tabelVeld']." ON ".$veldData['tabelFonds'].".".$veld." = ".$veldData['tabelVeld'].".".$veld."
        LEFT JOIN KeuzePerVermogensbeheerder ON ".$veldData['tabelFonds'].".".$veld." = KeuzePerVermogensbeheerder.waarde AND KeuzePerVermogensbeheerder.categorie='".$veldData['tabelVeld']."' AND KeuzePerVermogensbeheerder.vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
        WHERE ".$veldData['tabelFonds'].".Fonds = '".addslashes($data['Fonds'])."' AND ".$veldData['tabelFonds'].".Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND Vanaf <= '".$rapportageDatum."' ORDER BY ".$veldData['tabelFonds'].".Vanaf DESC LIMIT 1";
					$db2->SQL($query);
					$tmp=$db2->lookupRecord();
					$fondsData[$veld]=$tmp[$veld];
					$fondsData[$veld.'Omschrijving']=$tmp[$veld.'Omschrijving'];
					if($tmp['eersteAfdrukvolgorde'] <> 0)
						$fondsData[$veld.'Afdrukvolgorde']=$tmp['eersteAfdrukvolgorde'];
					else
						$fondsData[$veld.'Afdrukvolgorde']=$tmp['tweedeAfdrukvolgorde'];
				}

				$query="SELECT
hoofdcategorieDetails.Beleggingscategorie,
hoofdcategorieDetails.Afdrukvolgorde AS tweedeAfdrukvolgorde,
KeuzePerVermogensbeheerder.Afdrukvolgorde AS eersteAfdrukvolgorde,
hoofdcategorieDetails.Omschrijving AS BeleggingscategorieOmschrijving
FROM
BeleggingscategoriePerFonds
LEFT JOIN Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT JOIN CategorienPerHoofdcategorie ON Beleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' 
LEFT JOIN Beleggingscategorien as hoofdcategorieDetails ON CategorienPerHoofdcategorie.Hoofdcategorie = hoofdcategorieDetails.Beleggingscategorie
LEFT JOIN KeuzePerVermogensbeheerder ON  hoofdcategorieDetails.Beleggingscategorie = KeuzePerVermogensbeheerder.waarde AND KeuzePerVermogensbeheerder.categorie = 'Beleggingscategorien' AND KeuzePerVermogensbeheerder.vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' 
WHERE BeleggingscategoriePerFonds.Fonds = '".addslashes($data['Fonds'])."' AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'  AND Vanaf <= '".$rapportageDatum."' 
ORDER BY BeleggingscategoriePerFonds.Vanaf DESC
LIMIT 1";
				$db2->SQL($query);
				$tmp=$db2->lookupRecord();
				$fondsData['Hoofdcategorie']=$tmp['Beleggingscategorie'];
				$fondsData['HoofdcategorieOmschrijving']=$tmp['BeleggingscategorieOmschrijving'];
				if($tmp['eersteAfdrukvolgorde'] <> '')
					$fondsData['HoofdcategorieAfdrukvolgorde']=$tmp['eersteAfdrukvolgorde'];
				else
					$fondsData['HoofdcategorieAfdrukvolgorde']=$tmp['tweedeAfdrukvolgorde'];

				$query="SELECT
hoofdsectorDetails.Beleggingssector,
hoofdsectorDetails.Afdrukvolgorde AS tweedeAfdrukvolgorde,
KeuzePerVermogensbeheerder.Afdrukvolgorde AS eersteAfdrukvolgorde,
hoofdsectorDetails.Omschrijving AS sectorOmschrijving
FROM
BeleggingssectorPerFonds
LEFT JOIN Beleggingssectoren ON BeleggingssectorPerFonds.Beleggingssector = Beleggingssectoren.Beleggingssector
LEFT JOIN SectorenPerHoofdsector ON Beleggingssectoren.Beleggingssector = SectorenPerHoofdsector.Beleggingssector AND SectorenPerHoofdsector.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' 
LEFT JOIN Beleggingssectoren as hoofdsectorDetails ON SectorenPerHoofdsector.Hoofdsector= hoofdsectorDetails.Beleggingssector
LEFT JOIN KeuzePerVermogensbeheerder ON hoofdsectorDetails.Beleggingssector = KeuzePerVermogensbeheerder.waarde AND KeuzePerVermogensbeheerder.categorie = 'Beleggingssectoren' AND KeuzePerVermogensbeheerder.vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' 
WHERE BeleggingssectorPerFonds.Fonds = '".addslashes($data['Fonds'])."' AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'  AND Vanaf <= '".$rapportageDatum."'
ORDER BY BeleggingssectorPerFonds.Vanaf DESC
LIMIT 1";
				$db2->SQL($query);
				$tmp=$db2->lookupRecord();
				$fondsData['Hoofdsector']=$tmp['Beleggingssector'];
				$fondsData['HoofdsectorOmschrijving']=$tmp['sectorOmschrijving'];
				if($tmp['eersteAfdrukvolgorde'] <> '')
					$fondsData['HoofdsectorAfdrukvolgorde']=$tmp['eersteAfdrukvolgorde'];
				else
					$fondsData['HoofdsectorAfdrukvolgorde']=$tmp['tweedeAfdrukvolgorde'];

				/*
              foreach($hoofdVerdeling as $veld=>$waarden)
              {
                if($veld=='Hoofdcategorie')
                  $koppeling='Beleggingscategorie';
                else
                  $koppeling='Beleggingssector';

                $fondsData[$veld]=$waarden['Koppeling'][$fondsData[$koppeling]];
                $fondsData[$veld.'Volgorde']=$waarden['Afdrukvolgorde'][$fondsData[$veld]];
                $fondsData[$veld.'Omschrijving']=$waarden['Omschrijving'][$fondsData[$veld]];
              }
            */

				foreach($fondsData as $key=>$value)
				{
					$key=str_replace('Afdrukvolgorde','Volgorde',$key);
					$regels[$n][$key]=$value;
				}
				//$regels[$n]['fondsOmschrijving']=$fondsData['Omschrijving'];
				$query="SELECT Koers,Datum FROM Fondskoersen WHERE Datum <= '$rapportageDatum' AND Fonds='".addslashes($data['Fonds'])."' ORDER BY Datum DESC limit 1 ";
				$db2->SQL($query);
				$fondsKoers=$db2->lookupRecord();
				if(!isset($fondsKoers['Koers']))
					$fondsKoers['koers']=0.001;
				$query="SELECT Koers FROM Valutakoersen WHERE Datum <= '$rapportageDatum' AND valuta='".$fondsData['Valuta']."' ORDER BY Datum DESC limit 1 ";
				$db2->SQL($query);
				$valutaKoers=$db2->lookupRecord();

				$regels[$n]['totaalAantal'] = $regels[$n]['actuelePortefeuilleWaardeEuro']/$fondsData['FondsEenheid']/$valutaKoers['Koers']/$fondsKoers['Koers'];
				$regels[$n]['type'] = 'fondsen';
				$regels[$n]['actueleFonds']=$fondsKoers['Koers'];
				$regels[$n]['actueleValuta']=$valutaKoers['Koers'];
				//$regels[$n]['FondsEenheid']=$fondsData['FondsEenheid'];
			}
			$n++;
		}

		return $regels;

	}

	function writeRapport()
	{
		$db=new DB();
		$beheerder = $this->selectData['VermogensbeheerderVan'];//$this->pdf->portefeuilledata['Vermogensbeheerder'];
		$this->pdf->portefeuilledata['Vermogensbeheerder']=$beheerder;
		$query = "SELECT doorkijkCategorie,doorkijkCategorieSoort,grafiekKleur, afdrukVolgorde
                   FROM doorkijk_categoriePerVermogensbeheerder 
                   WHERE Vermogensbeheerder='$beheerder'
                   ORDER BY doorkijkCategorieSoort,afdrukVolgorde 
                  ";

		$db->SQL($query);
		$db->Query();
		$kleuren=array();
		while($data = $db->nextRecord())
		{
			$kleuren[$data['doorkijkCategorieSoort']][$data['doorkijkCategorie']]=unserialize($data['grafiekKleur']);
		}

		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$vmkleuren = $DB->LookupRecord();
		$allekleuren = unserialize($vmkleuren['grafiek_kleur']);
		foreach($allekleuren['OIB'] as $kleur)
		{
			if($kleur['R']['value']<>0 && $kleur['G']['value']<>0 && $kleur['B']['value']<>0)
			{
				$this->categorieKleuren[]=array($kleur['R']['value'],$kleur['G']['value'],$kleur['B']['value']);
			}
		}


		$doorkijkCategorieSoorten=array('Fondsverdeling','Beleggingscategorien','Regios','Beleggingssectoren');
		/*
		$query = "SELECT DISTINCT msCategoriesoort
                  FROM doorkijk_msCategoriesoort ORDER BY msCategoriesoort";
		$db->SQL($query);
		$db->Query();
		$doorkijkCategorieSoorten=array();
		while($data = $db->nextRecord())
		{
			$doorkijkCategorieSoorten[] = $data['msCategoriesoort'];
		}
    */

		$fondsVerdeling=$this->vulTijdelijkeRapportage();
		$kleuren['Fondsverdeling']=$fondsVerdeling['kleuren'];

		$this->pdf->AddPage();
		$pieTeller = 0;

		$doorkijkTitels=array('Fondsverdeling'=>'Fondsverdeling','Beleggingscategorien'=>'Beleggingscategorie','Regios'=>'Regio','Beleggingssectoren'=>'Beleggingssector');//array();Beleggingscategorie
		foreach($doorkijkCategorieSoorten as $index=>$doorkijkCategorieSoort)
		{
			$xOffset =  $index * 70;
			if($doorkijkCategorieSoort=='Fondsverdeling')
				$doorKijk=$fondsVerdeling;
			else
			  $doorKijk= $this->bepaalWeging($doorkijkCategorieSoort);


			$this->toonTabel($doorKijk['details'],$xOffset,$doorkijkTitels[$doorkijkCategorieSoort],$kleuren[$doorkijkCategorieSoort]);
			$grafiekdata=array();
			$grafiekTonen=true;
			foreach($doorKijk['categorien'] as $categorie=>$percentage)
			{
				$grafiekdata[$categorie]['kleur'] = $kleuren[$doorkijkCategorieSoort][$categorie];//array('R' => array('value' => $kleuren[$categorie][0]),'G' => array('value' => $kleuren[$categorie][1]),'B' => array('value' => $kleuren[$categorie][2]));
				$grafiekdata[$categorie]['percentage'] = $percentage;
				if($percentage<0)
					$grafiekTonen=false;
  		}
			if($grafiekTonen==true)
			  $this->printPie( $grafiekdata,20+$xOffset,125,$doorkijkTitels[$doorkijkCategorieSoort]); //+$yOffset);
			$pieTeller++;
		}

		if($this->debug)
		{
//listarray(	$this->debugData['afwijkingWeging']);
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
}
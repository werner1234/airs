<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/11/17 17:17:08 $
 		File Versie					: $Revision: 1.4 $

 		$Log: RapportGRAFIEK_L16.php,v $
 		Revision 1.4  2010/11/17 17:17:08  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2009/01/10 16:02:18  rvv
 		*** empty log message ***

 		Revision 1.2  2007/10/05 13:29:55  rvv
 		*** empty log message ***

 		Revision 1.1  2007/10/04 12:09:12  rvv
 		*** empty log message ***

 		Revision 1.9  2007/09/26 15:30:33  rvv
 		*** empty log message ***

 		Revision 1.8  2007/05/04 06:18:38  rvv
 		sortering op afdrukvolgorde aangepast

 		Revision 1.7  2007/03/27 14:58:20  rvv
 		VreemdeValutaRapportage

 		Revision 1.6  2007/01/31 16:20:27  rvv
 		*** empty log message ***

 		Revision 1.5  2006/12/21 16:15:30  rvv
 		geen fout meer bij lege portefeuille

 		Revision 1.4  2006/12/14 11:18:37  rvv
 		Met valutaPerRegio

 		Revision 1.3  2006/11/10 11:56:12  rvv
 		Eigen kleuren aanpassing/toevoeging

 		Revision 1.2  2006/11/03 11:24:04  rvv
 		Na user update

 		Revision 1.1  2006/10/31 12:15:25  rvv
 		Voor user update


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportGRAFIEK_L16
{
	function RapportGRAFIEK_L16($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "GRAFIEK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_GRAFIEK_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_GRAFIEK_titel;
		else
			$this->pdf->rapport_titel = "Vermogensallocatie";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
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
			" .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$totaalLiquiditeiten = $DB->nextRecord();
	$totaalLiquiditeiten = $totaalLiquiditeiten['WaardeEuro'];


	if ($this->pdf->rapport_layout == 12 && file_exists("./rapport/include/RapportGRAFIEK_L".$this->pdf->rapport_layout.".php"))
	{
      include("./rapport/include/RapportGRAFIEK_L".$this->pdf->rapport_layout.".php");
	}
	else
	{

	//Kleuren instellen
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);
		$allekleuren['OIS2'] = $allekleuren['OIS'];

		$this->pdf->rapport_GRAFIEK_sortering = $kleuren['grafiek_sortering'];

if ($this->pdf->rapport_GRAFIEK_sortering == 1)
$order = 'Beleggingscategorien.Afdrukvolgorde ASC';
else
$order = 'WaardeEuro desc';


	$query="SELECT TijdelijkeRapportage.beleggingscategorie,
	sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
	Beleggingscategorien.Omschrijving,
	Beleggingscategorien.Beleggingscategorie
	FROM TijdelijkeRapportage
	LEFT JOIN  Beleggingscategorien on TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.beleggingscategorie
	WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'"
	.$__appvar['TijdelijkeRapportageMaakUniek'].
	" GROUP BY TijdelijkeRapportage.beleggingscategorie
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
	   $data['beleggingscategorie'][$cat['Beleggingscategorie']]['waardeEur']=$cat['WaardeEuro'];
	   $data['beleggingscategorie'][$cat['Beleggingscategorie']]['Omschrijving']=$cat['Omschrijving'];
    }
	}

	if ($this->pdf->rapport_GRAFIEK_sortering == 1 && round($liquididiteiten['waardeEur'],2) != 0 ) // liquiditeiten toevoegen
	{
	  $data['beleggingscategorie']['Liquiditeiten']['waardeEur']     = $liquididiteiten['waardeEur'];
	  $data['beleggingscategorie']['Liquiditeiten']['Omschrijving']  = $liquididiteiten['Omschrijving'];
	}

if ($this->pdf->rapport_GRAFIEK_sortering == 1)
$order = 'Regios.Afdrukvolgorde ASC';
else
$order = 'WaardeEuro desc';

	$query="SELECT
			TijdelijkeRapportage.Regio,
			Regios.Omschrijving,
			sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro
			FROM TijdelijkeRapportage
			LEFT JOIN Regios ON TijdelijkeRapportage.Regio = Regios.Regio
			WHERE TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."'
			AND TijdelijkeRapportage.Portefeuille = '".$portefeuille."' "
			.$__appvar['TijdelijkeRapportageMaakUniek']."
			GROUP BY TijdelijkeRapportage.Regio
			ORDER BY $order";
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	while($reg = $DB->nextRecord())
	{
		if ($reg['Regio']== "")
		{
		$reg['Omschrijving']="Geen regio";
		$reg['Regio'] = "Geen regio";
		}
	$data['regio'][$reg['Regio']]['waardeEur']=$reg['WaardeEuro'];
	$data['regio'][$reg['Regio']]['Omschrijving']=$reg['Omschrijving'];
	}


if ($this->pdf->rapport_GRAFIEK_sortering == 1)
  $order = 'Valutas.Afdrukvolgorde asc, Beleggingscategorien.Afdrukvolgorde asc';
else
  $order = 'actuelePortefeuilleWaardeEuro desc';

		$query = "SELECT ".
		" Valutas.Omschrijving AS Omschrijving, ".
		" TijdelijkeRapportage.valuta, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS WaardeEuro ".
		" FROM TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta)  ".
//		" LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie) ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."'"
		 .$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.valuta ".
		" ORDER BY $order";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();

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

	if ($this->pdf->rapport_GRAFIEK_sortering == 1)
$order = 'Beleggingssectoren.Afdrukvolgorde ASC';
else
$order = 'WaardeEuro desc';

	$query="SELECT
			TijdelijkeRapportage.Beleggingssector, Beleggingssectoren.Omschrijving,
			sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
			 Beleggingssectoren.Beleggingssector
			FROM TijdelijkeRapportage
			 JOIN Beleggingssectoren on TijdelijkeRapportage.Beleggingssector = Beleggingssectoren.Beleggingssector
			WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
			AND (TijdelijkeRapportage.type = 'fondsen' || TijdelijkeRapportage.type = 'rente' )
			AND TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek']."
			GROUP BY TijdelijkeRapportage.Beleggingssector
			ORDER BY $order ;"; //LEFT JOIN -> LEFT
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();

	while($sec = $DB->nextRecord())
	{
	  if ($sec['Beleggingssector'] == '')
	  {
	    $sec['Omschrijving']= 'Geen sector';
			$sec['Beleggingssector']= 'Geen sector';
	  }

    $data['sectoren'][$sec['Beleggingssector']]['waardeEur']=$sec['WaardeEuro'];
    $data['sectoren'][$sec['Beleggingssector']]['Omschrijving']=$sec['Omschrijving'];
    $sectorTotaal += $sec['WaardeEuro'];
	}


//Ophalen regio liquiditeiten.
/*
	$query = "SELECT
			sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
			TijdelijkeRapportage.valuta,
 			ValutaPerRegio.Regio
			FROM TijdelijkeRapportage
			LEFT JOIN  ValutaPerRegio on  ValutaPerRegio.Valuta = TijdelijkeRapportage.valuta
			WHERE TijdelijkeRapportage.Portefeuille =  '".$portefeuille."' AND
			TijdelijkeRapportage.type = 'rekening'
			AND TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."'
			GROUP BY TijdelijkeRapportage.valuta";
	$DB->SQL($query);
	$DB->Query();
	while($valuta = $DB->nextRecord())
	{
	if ($valuta['Regio'] == '')
	  $valuta['Regio'] = 'Geen regio';
	$data['regio'][$valuta['Regio']]['waardeEur'] = $data['regio'][$valuta['Regio']]['waardeEur'] + $valuta['WaardeEuro'];
	$data['regio']['Geen regio']['waardeEur'] = $data['regio']['Geen regio']['waardeEur'] - $valuta['WaardeEuro'];
	}
*/
		$this->pdf->AddPage();

		$grafieken = array();
		$grafieken[] = 'OIB';
		$grafieken[] = 'OIR';
		$grafieken[] = 'OIV';
		$grafieken[] = 'OIS2';

		$groepen = array();
		$groepen[]=$data['beleggingscategorie'];
		$groepen[]=$data['regio'];
		$groepen[]=$data['valuta'];
		$groepen[]=$data['sectoren'];

$standaardKleuren=array(array(255,0,0),	array(0,255,0),array(0,0,255),array(255,255,0),array(0,255,255),
						array(255,0,255),array(128,128,255),array(128,100,64),array(22,100,64),array(222,1,64)
						,array(255,0,100),array(100,255,0),array(155,0,0),array(0,155,0),array(0,0,155));

$allesTotaal = $totaalWaarde;

$grafiekKleuren = array();
for ($i=0; $i <4; $i++)
{
  if($i == 3)
 $totaalWaarde = $sectorTotaal;
 else
 $totaalWaarde = $allesTotaal;



	$restPercentage = 100;
		while (list($groep, $groepdata) = each($groepen[$i]))
		{
			$percentageGroep=($groepdata['waardeEur'] / $totaalWaarde) * 100 ;
			$restPercentage = $restPercentage - $percentageGroep;
			if (round($percentageGroep,1) != 0)
			{
  			$kleurdata[$i][$groep]['kleur'] = $allekleuren[$grafieken[$i]][$groep];
  			if ($percentageGroep < 0)
  				$percentageGroep = $percentageGroep * -1;
  			$grafiekData[$grafieken[$i]]['Percentage'][] = $percentageGroep ;
   			$grafiekData[$grafieken[$i]]['Omschrijving'][] =  $groepdata['Omschrijving'] . " (" . round(($groepdata['waardeEur'] / $totaalWaarde) * 100 ,1) ." %)" ;
			}
		}
		if (round($restPercentage,1) >0)
		{
		$grafiekData[$grafieken[$i]]['Percentage'][] = $restPercentage;
		$grafiekData[$grafieken[$i]]['Omschrijving'][] = "Rest percentage" . " (" . round($restPercentage,1) ." %)" ;
		}


		if($kleurdata[$i])
		{
		  $a=0;
		  while (list($key, $value) = each($kleurdata[$i]))
			{
			if ($value['kleur']['R']['value'] == 0 && $value['kleur']['G']['value'] == 0 && $value['kleur']['B']['value'] == 0)
			  {
			  	if ($a <15)
			  	{
			  	$grafiekKleuren[$i][]=$standaardKleuren[$a];
			  	$grafiekData[$grafieken[$i]]['Kleur'][] = $standaardKleuren[$a];
			  	}
			  	else
			  	{
			  	$grafiekKleuren[$i][]=$standaardKleuren[$a-15];
			  	$grafiekData[$grafieken[$i]]['Kleur'][] = $standaardKleuren[$a-15];
			  	}
			  }
			else
			  {
			  $grafiekKleuren[$i][] = array($value['kleur']['R']['value'],$value['kleur']['G']['value'],$value['kleur']['B']['value']);
			  $grafiekData[$grafieken[$i]]['Kleur'][] = array($value['kleur']['R']['value'],$value['kleur']['G']['value'],$value['kleur']['B']['value']);
			  }
			$a++;
			}
		}
		else
		{
		  $grafiekKleuren[$i] = $standaardKleuren;
		  $grafiekData[$grafieken[$i]]['Kleur'] = $standaardKleuren;
		}
}
//eind kleuren instellen

$diameter = 35;
$hoek = 30;
$dikte = 10;
$Xas= 80;
$yas= 55;
//print_r($grafiekData);exit;

$this->pdf->set3dLabels($grafiekData['OIB']['Omschrijving'],$Xas,$yas,$grafiekData['OIB']['Kleur']);
$this->pdf->Pie3D($grafiekData['OIB']['Percentage'],$grafiekData['OIB']['Kleur'],$Xas,$yas,$diameter,$hoek,$dikte,"Beleggingscategorie");

$this->pdf->set3dLabels($grafiekData['OIR']['Omschrijving'],$Xas+135,$yas,$grafiekData['OIR']['Kleur']);
$this->pdf->Pie3D($grafiekData['OIR']['Percentage'],$grafiekData['OIR']['Kleur'],$Xas+135,$yas,$diameter,$hoek,$dikte,"Regio");

$this->pdf->set3dLabels($grafiekData['OIV']['Omschrijving'],$Xas,$yas+80,$grafiekData['OIV']['Kleur']);
$this->pdf->Pie3D($grafiekData['OIV']['Percentage'],$grafiekData['OIV']['Kleur'],$Xas,$yas+80,$diameter,$hoek,$dikte,"Valuta");

$this->pdf->set3dLabels($grafiekData['OIS2']['Omschrijving'],$Xas+135,$yas+80,$grafiekData['OIS2']['Kleur']);
$this->pdf->Pie3D($grafiekData['OIS2']['Percentage'],$grafiekData['OIS2']['Kleur'],$Xas+135,$yas+80,$diameter,$hoek,$dikte,"Sector");

	}
	}
}
?>
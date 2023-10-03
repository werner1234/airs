<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/06/04 16:13:28 $
 		File Versie					: $Revision: 1.5 $

 		$Log: RapportGRAFIEK_L18.php,v $
 		Revision 1.5  2014/06/04 16:13:28  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/05/14 15:28:41  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2011/09/25 16:23:28  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2011/06/29 16:52:23  rvv
 		*** empty log message ***

 		Revision 1.1  2011/06/02 15:05:05  rvv
 		*** empty log message ***

 		Revision 1.12  2010/11/17 17:16:33  rvv
 		*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportGRAFIEK_L18
{
	function RapportGRAFIEK_L18($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "GRAFIEK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_GRAFIEK_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_GRAFIEK_titel;
		else
			$this->pdf->rapport_titel = "Sector- & Regioverdeling Aandelen";

			$this->pdf->last_rapport_titel=$this->pdf->rapport_titel;
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
					 " portefeuille = '".$portefeuille."' AND  beleggingscategorie='AAND' "
					 .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$totaalWaarde = $DB->nextRecord();
	$totaalWaarde = $totaalWaarde['totaal'];

	/*
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
*/



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


if ($this->pdf->rapport_GRAFIEK_sortering == 1)
$order = 'TijdelijkeRapportage.regioVolgorde';
else
$order = 'WaardeEuro desc';

	$query="SELECT
			TijdelijkeRapportage.Regio,
			TijdelijkeRapportage.regioOmschrijving as Omschrijving,
			sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro
			FROM TijdelijkeRapportage
			WHERE TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."'
			AND TijdelijkeRapportage.Portefeuille = '".$portefeuille."' AND  beleggingscategorie='AAND' "
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
	$totaleRegioWaarde +=$reg['WaardeEuro'];
	}

	if ($this->pdf->rapport_GRAFIEK_sortering == 1)
$order = 'TijdelijkeRapportage.beleggingssectorVolgorde ASC';
else
$order = 'WaardeEuro desc';

	$query="SELECT
			TijdelijkeRapportage.Beleggingssector,TijdelijkeRapportage.beleggingssectorOmschrijving as Omschrijving,
			sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
			 TijdelijkeRapportage.Beleggingssector
			FROM TijdelijkeRapportage
			WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
			AND TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."' AND  beleggingscategorie='AAND' "
			.$__appvar['TijdelijkeRapportageMaakUniek']."
			GROUP BY TijdelijkeRapportage.Beleggingssector
			ORDER BY $order ;";
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();

	while($sec = $DB->nextRecord())
	{
	  if ($sec['Beleggingssector']== "")
	  {
	  	if (round($sec['WaardeEuro'] - $totaalLiquiditeiten,1) != 0)
	  	{
	  		if(round($totaalLiquiditeiten,2) != 0)
	  		{
			$data['sectoren']['Liquiditeiten']['waardeEur']=$totaalLiquiditeiten;
			$data['sectoren']['Liquiditeiten']['Omschrijving']='Liquiditeiten';
			$sec['WaardeEuro'] = $sec['WaardeEuro'] - $totaalLiquiditeiten;
	  		}
			$sec['Omschrijving']= 'Geen sector';
			$sec['Beleggingssector']= 'Geen sector';
	  	}
	  	else
	  	{
		$sec['Omschrijving']= 'Liquiditeiten';
		$sec['Beleggingssector']= 'Liquiditeiten';
	  	}
	  }

		if ($this->pdf->rapport_GRAFIEK_sortering == 1 && $sec['Omschrijving'] == "Liquiditeiten" ) //liquiditeiten later toevoegen
    {
     $liquididiteiten['waardeEur'] = $sec['WaardeEuro'];
     $liquididiteiten['Omschrijving'] = "Liquiditeiten";
    }
    else
    {
	    $data['sectoren'][$sec['Beleggingssector']]['waardeEur']=$sec['WaardeEuro'];
	    $data['sectoren'][$sec['Beleggingssector']]['Omschrijving']=$sec['Omschrijving'];
    }
	}

	if ($this->pdf->rapport_GRAFIEK_sortering == 1 && round($liquididiteiten['waardeEur'],2) != 0 ) // liquiditeiten toevoegen
	{
	  $data['sectoren']['Liquiditeiten']['waardeEur']     = $liquididiteiten['waardeEur'];
	  $data['sectoren']['Liquiditeiten']['Omschrijving']  = $liquididiteiten['Omschrijving'];
	}


		$this->pdf->AddPage();
		$this->pdf->templateVars['GRAFIEKPaginas']=$this->pdf->customPageNo+$this->pdf->extraPage;

		$grafieken = array();
		$grafieken[] = 'OIR';
		$grafieken[] = 'OIS';

		$groepen = array();
		$groepen[]=$data['regio'];
		$groepen[]=$data['sectoren'];

$standaardKleuren=array(array(255,0,0),	array(0,255,0),array(0,0,255),array(255,255,0),array(0,255,255),
						array(255,0,255),array(128,128,255),array(128,100,64),array(22,100,64),array(222,1,64)
						,array(255,0,100),array(100,255,0),array(155,0,0),array(0,155,0),array(0,0,155));


$grafiekKleuren = array();
for ($i=0; $i <4; $i++)
{
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
$yas= 85;
//print_r($grafiekData);exit;

$this->set3dLabels($grafiekData['OIS']['Omschrijving'],$Xas,$yas,$grafiekData['OIS']['Kleur']);
$this->pdf->Pie3D($grafiekData['OIS']['Percentage'],$grafiekData['OIS']['Kleur'],$Xas,$yas,$diameter,$hoek,$dikte,"Sector",0);

$this->set3dLabels($grafiekData['OIR']['Omschrijving'],$Xas+135,$yas,$grafiekData['OIR']['Kleur']);
$this->pdf->Pie3D($grafiekData['OIR']['Percentage'],$grafiekData['OIR']['Kleur'],$Xas+135,$yas,$diameter,$hoek,$dikte,"Regio",0);
	}


    function set3dLabels($labels,$x,$y,$colors,$xcor=-55,$xcor2=5,$ycor= 27,$kort = 0)
    {
        $aantal = count($labels);
        if($kort == 0)
        {
          $maxAantal = 12;
          $colMax = 6;
        }
        else
        {
          $aantal = min(16,$aantal);
          $maxAantal = 16;
          $colMax = 16;
          if($kort == 2)
            $ycor = $ycor - ( $aantal * 2 ) +6;
          else
            $ycor = $ycor - ( $aantal * 2  );
        }

    	  for($i=0; $i<$aantal; $i++)
    	  {
    	    $hLegend=4;
          $bHight=6.0;
    	    if ($i < $maxAantal)
    	    {
    	    $x1=$xcor+$x;
    	    $x2=$xcor+$x+$bHight;
    	    $y1=$ycor+$y+$i*$bHight;
    	    $y2=$ycor+$y+$i*$bHight;
    	    }
   
		      if ($i<$maxAantal )
		      {
		      $this->pdf->SetFont($this->pdf->rapport_font, '', 10);
		      $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['b'],$this->pdf->rapport_fonds_fontcolor['b']);
		      $this->pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b'])));

          $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->pdf->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1);
          $this->pdf->Cell(0,$hLegend,$labels[$i]);
          $y1+=$hLegend;
		      }
        }
        $this->pdf->Rect($x+$xcor-5, $y-$ycor, 120, 55+ $bHight*12, 'D');
    }

}
?>
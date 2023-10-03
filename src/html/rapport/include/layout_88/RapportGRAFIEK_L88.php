<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/03/28 15:46:18 $
 		File Versie					: $Revision: 1.3 $

 		$Log: RapportGRAFIEK_L88.php,v $
 		Revision 1.3  2020/03/28 15:46:18  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2020/03/25 16:44:42  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2020/03/21 12:35:10  rvv
 		*** empty log message ***
 		
 
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportGRAFIEK_L88
{
	function RapportGRAFIEK_L88($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "GRAFIEK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_GRAFIEK_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_GRAFIEK_titel;
		else
			$this->pdf->rapport_titel = vertaalTekst("Vermogensallocatie",$this->pdf->rapport_taal);

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



	$query="SELECT TijdelijkeRapportage.beleggingscategorie,
	sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
	TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving,
	TijdelijkeRapportage.Beleggingscategorie
	FROM TijdelijkeRapportage
	WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'"
	.$__appvar['TijdelijkeRapportageMaakUniek'].
	" GROUP BY TijdelijkeRapportage.beleggingscategorie
	ORDER BY TijdelijkeRapportage.BeleggingscategorieVolgorde";
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

//if ($this->pdf->rapport_GRAFIEK_sortering == 1)
$order = 'Regios.Afdrukvolgorde ASC';
//else
//$order = 'WaardeEuro desc';

	$query="SELECT
			TijdelijkeRapportage.Regio,
			TijdelijkeRapportage.RegioOmschrijving,
			sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro
			FROM TijdelijkeRapportage

			WHERE TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."'
			AND TijdelijkeRapportage.Portefeuille = '".$portefeuille."' "
			.$__appvar['TijdelijkeRapportageMaakUniek']."
			GROUP BY TijdelijkeRapportage.Regio
			ORDER BY TijdelijkeRapportage.RegioVolgorde";
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


		$query = "SELECT ".
		" Valutas.Omschrijving AS Omschrijving, ".
		" TijdelijkeRapportage.valuta, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS WaardeEuro ".
		" FROM TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta)  ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."'"
		 .$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.valuta ".
		" ORDER BY TijdelijkeRapportage.ValutaVolgorde asc";

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

//	if ($this->pdf->rapport_GRAFIEK_sortering == 1)
$order = 'Beleggingssectoren.Afdrukvolgorde ASC';
//else
//$order = 'WaardeEuro desc';

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


		$this->pdf->AddPage();
    $this->pdf->templateVars['GRAFIEKPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['GRAFIEKPaginas']=$this->pdf->rapport_titel;
    
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
  			//if ($percentageGroep < 0)
  			//	$percentageGroep = $percentageGroep * -1;
        $omschrijving=vertaalTekst($groepdata['Omschrijving'],$this->pdf->rapport_taal). " (" . round(($groepdata['waardeEur'] / $totaalWaarde) * 100 ,1) ." %)";  
  			$grafiekData[$grafieken[$i]]['Percentage'][$omschrijving] = round($percentageGroep,1) ;
   			$grafiekData[$grafieken[$i]]['Omschrijving'][] = $omschrijving   ;
        $grafiekData[$grafieken[$i]]['waardeEur'][] = $groepdata['waardeEur'];
        $grafiekData[$grafieken[$i]]['proc'][] = $percentageGroep;
        $grafiekData[$grafieken[$i]]['short'][] = vertaalTekst($groepdata['Omschrijving'],$this->pdf->rapport_taal)   ;
			}
		}
		if (round($restPercentage,1) >0)
		{
		  $omschrijving=vertaalTekst("Rest percentage",$this->pdf->rapport_taal) . " (" . round($restPercentage,1) ." %)" ;; 
	  	$grafiekData[$grafieken[$i]]['Percentage'][$omschrijving] = $restPercentage;
	  	$grafiekData[$grafieken[$i]]['Omschrijving'][] = $omschrijving;
      if (round($restPercentage,1) == 100)
        unset($grafiekData[$grafieken[$i]]);
		}

    if(isset($grafiekData[$grafieken[$i]]))
    {
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
}
//eind kleuren instellen

$diameter = 35;
$hoek = 30;
$dikte = 10;
$Xas= 80;
$yas= 55;
//print_r($grafiekData);exit;
$headerHeight=35;
$lwb=(297/3)-$this->pdf->marge; //133.5
$vwh=((210-$headerHeight-$this->pdf->marge)/2+$headerHeight)-$headerHeight;
$chartsize=55;
$extraBarW=25;

$charts=array('OIB'=>'Beleggingscategorie','OIV'=>'Valuta','OIS2'=>'Sector');

$i=0;
foreach($charts as $type=>$omschrijving)
{
  $this->pdf->setXY($this->pdf->marge+25+$i*90, $headerHeight);
  //$this->pdf->setXY($this->pdf->marge+(($lwb/4)*1-$chartsize/2),$headerHeight);
  
  if (min($grafiekData[$type]['Percentage']) < 0)
  {
    $this->BarDiagram($chartsize + $extraBarW, $chartsize, $grafiekData[$type]['Percentage'], '%l', $grafiekData[$type]['Kleur'], vertaalTekst($omschrijving, $this->pdf->rapport_taal));
  }
  else
  {
   $legendaStart = $this->correctLegentHeight(count($grafiekData[$type]['Percentage']));
    PieChart_L88($this->pdf, $chartsize, $vwh, $grafiekData[$type]['Percentage'], '%l', $grafiekData[$type]['Kleur'], vertaalTekst($omschrijving, $this->pdf->rapport_taal), 'geen');//$legendaStart);
    $this->toonTabel($legendaStart,$grafiekData[$type]) ;
  }
  $i++;
}
    
    /*
    $this->pdf->setXY($this->pdf->marge+(($lwb/5)*1.5-$chartsize/2),$headerHeight);
    //$this->pdf->setXY($this->pdf->marge+(($lwb/4)*1-$chartsize/2),$headerHeight);
    
    if(min($grafiekData['OIB']['Percentage']) < 0)
      $this->BarDiagram($chartsize+$extraBarW,$chartsize,$grafiekData['OIB']['Percentage'],'%l',$grafiekData['OIB']['Kleur'],vertaalTekst('Beleggingscategorie',$this->pdf->rapport_taal));
    else
    {
      $legendaStart=$this->correctLegentHeight(count($grafiekData['OIB']['Percentage']));
      PieChart_L88($this->pdf,$chartsize,$vwh,$grafiekData['OIB']['Percentage'],'%l',$grafiekData['OIB']['Kleur'],vertaalTekst('Beleggingscategorie',$this->pdf->rapport_taal),$legendaStart);
    }
        
        $this->pdf->setXY($this->pdf->marge+(($lwb/5)*5.5-$chartsize/2),$headerHeight);
        //$this->pdf->setXY($this->pdf->marge+(($lwb/4)*3-$chartsize/2),$headerHeight);
        //listarray($grafiekData);
        
        if(min($grafiekData['OIR']['Percentage']) < 0)
          $this->BarDiagram($chartsize+$extraBarW,$chartsize,$grafiekData['OIR']['Percentage'],'%l',$grafiekData['OIR']['Kleur'],vertaalTekst('Regio',$this->pdf->rapport_taal));
        else
        {
          $legendaStart=$this->correctLegentHeight(count($grafiekData['OIR']['Percentage']));
          PieChart_L88($this->pdf,$chartsize,$vwh,$grafiekData['OIR']['Percentage'],'%l',$grafiekData['OIR']['Kleur'],vertaalTekst('Regio',$this->pdf->rapport_taal),$legendaStart);
        }
       
    $this->pdf->setXY($this->pdf->marge+(($lwb/5)*1.5-$chartsize/2),$headerHeight+$vwh-10);
    //$this->pdf->setXY($this->pdf->marge+(($lwb/4)*5-$chartsize/2),$headerHeight);
    if(min($grafiekData['OIV']['Percentage']) < 0)
      $this->BarDiagram($chartsize+$extraBarW,$chartsize,$grafiekData['OIV']['Percentage'],'%l',$grafiekData['OIB']['Kleur'],vertaalTekst('Valuta',$this->pdf->rapport_taal));
    else
    {
      $legendaStart=$this->correctLegentHeight(count($grafiekData['OIV']['Percentage']));
      PieChart_L88($this->pdf,$chartsize,$vwh,$grafiekData['OIV']['Percentage'],'%l',$grafiekData['OIV']['Kleur'],vertaalTekst('Valuta',$this->pdf->rapport_taal),$legendaStart);
    }
    
    if(isset($grafiekData['OIS2']))
    {
      $this->pdf->setXY($this->pdf->marge+(($lwb/5)*5.5-$chartsize/2),$headerHeight+$vwh-10);
     //  $this->pdf->setXY($this->pdf->marge+(($lwb/4)*7-$chartsize/2),$headerHeight);
      if(min($grafiekData['OIS2']['Percentage']) < 0)
        $this->BarDiagram($chartsize+$extraBarW,$chartsize,$grafiekData['OIS2']['Percentage'],'%l',$grafiekData['OIS2']['Kleur'],vertaalTekst('Sector',$this->pdf->rapport_taal));
      else
      {
        $legendaStart=$this->correctLegentHeight(count($grafiekData['OIS2']['Percentage']));
        PieChart_L88($this->pdf,$chartsize,$vwh,$grafiekData['OIS2']['Percentage'],'%l',$grafiekData['OIS2']['Kleur'],vertaalTekst('Sector',$this->pdf->rapport_taal),$legendaStart);
      }
    }
         */

	}
  
  function correctLegentHeight($regels)
  {
    return array($this->pdf->GetX()-15,$this->pdf->GetY()+ 70);
   // return array($this->pdf->GetX()+60,$this->pdf->GetY()+ 35 -($regels*4)/2);
     
  }
  
  function toonTabel($start,$data)
  {
   // listarray($data);
    $this->pdf->setY($start[1]);
    $this->pdf->setWidths(array($start[0]-10,40,20,20));
    $this->pdf->setAligns(array('L','L','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Omschrijving','Waarde','Percentage'));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    foreach($data['short'] as $index=>$omschrijving)
    {
      $this->pdf->Rect($start[0]-5, $this->pdf->getY()+.5, 3, 3 ,'F','',array($data['Kleur'][$index][0],$data['Kleur'][$index][1],$data['Kleur'][$index][2]));
      $this->pdf->row(array('',$omschrijving,$this->formatGetal($data['waardeEur'][$index],0),$this->formatGetal($data['proc'][$index],1).'%'));
    }
  // listarray($data);exit;
    //foreach($data[''])
    
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
         // $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->wLegend);
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
      /*
      $this->pdf->SetFont($this->pdf->rapport_font, '', 5);
      if(round($legendaStep,5) <> 0.0)
      {
        //for($x=$nullijn;$x<$XDiag; $x=$x-$legendaStep)
        for($x=$nullijn;$x>$XDiag; $x=$x-$legendaStep)
        {
         // $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
          $this->pdf->setXY($x,$YDiag + $hDiag);
          $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
          $i++;
          if($i>100)
            break;
        }

        $i=0;
        //for($x=$nullijn;$x>($XDiag+$lDiag); $x=$x+$legendaStep)
        for($x=$nullijn;$x<($XDiag+$lDiag); $x=$x+$legendaStep)
        {
        //  $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
          $this->pdf->setXY($x,$YDiag + $hDiag);
          $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
          
          $i++;
          if($i>100)
            break;
        }
      }
      */
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
      if($kort == 0)
      {
        $maxAantal = 14;
        $colMax = 7;
      }

      $h=3.5;
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
          $y1+=$hLegend + $margin;
		      }
      }
    }  
  
}
?>
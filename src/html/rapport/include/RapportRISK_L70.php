<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/07/07 05:31:43 $
 		File Versie					: $Revision: 1.5 $

 		$Log: RapportRISK_L70.php,v $
 		Revision 1.5  2016/07/07 05:31:43  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2016/06/29 16:04:07  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2016/06/12 10:20:31  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/06/08 15:40:53  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/05/22 18:49:26  rvv
 		*** empty log message ***
 		
 	


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportRISK_L70
{
	function RapportRISK_L70($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "RISK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

    $this->pdf->rapport_titel = vertaalTekst("Risico-verdeling",$this->pdf->rapport_taal);

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
	//	$this->vuurtorenIMG="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";
  //  $this->vuurtorenIMG="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";
    $this->vuurtorenIMG="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";
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

if ($this->pdf->rapport_GRAFIEK_sortering == 1)
$order = 'TijdelijkeRapportage.beleggingscategorieVolgorde ASC';
else
$order = 'WaardeEuro desc';


	$query="SELECT TijdelijkeRapportage.beleggingscategorie,
	sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
	TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving,
	TijdelijkeRapportage.Beleggingscategorie
	FROM TijdelijkeRapportage
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
$order = 'TijdelijkeRapportage.regioVolgorde ASC';
else
$order = 'WaardeEuro desc';

	$query="SELECT
			TijdelijkeRapportage.Regio,
			TijdelijkeRapportage.regioOmschrijving as Omschrijving,
			sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro
			FROM TijdelijkeRapportage
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
	$totaleRegioWaarde +=$reg['WaardeEuro'];
	}



		$this->pdf->AddPage();
		$this->pdf->templateVars['RISKPaginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving['RISKPaginas']=$this->pdf->rapport_titel;

		$grafieken = array();
		$grafieken[] = 'OIB';
		$grafieken[] = 'OIR';


		$groepen = array();
		$groepen[]=$data['beleggingscategorie'];
		$groepen[]=$data['regio'];


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

  			$grafiekData[$grafieken[$i]]['Percentage'][] = $percentageGroep ;
   			$grafiekData[$grafieken[$i]]['Omschrijving'][] =  vertaalTekst($groepdata['Omschrijving'],$this->pdf->rapport_taal) . " (" . round(($groepdata['waardeEur'] / $totaalWaarde) * 100 ,1) ." %)" ;
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
$yas= 60;
//listarray($grafiekData);exit;

$printOIBgrafiek=true;
if(count($grafiekData['OIB']['Percentage'])<1)
  $printOIBgrafiek=false;
foreach($grafiekData['OIB']['Percentage'] as $percentage)
  if($percentage < 0)
    $printOIBgrafiek=false;

if($printOIBgrafiek==true)
{
  $this->pdf->set3dLabels($grafiekData['OIB']['Omschrijving'],$Xas,$yas,$grafiekData['OIB']['Kleur']);
  //$this->pdf->Pie3D($grafiekData['OIB']['Percentage'],$grafiekData['OIB']['Kleur'],$Xas,$yas,$diameter,$hoek,$dikte, vertaalTekst("Onderverdeling in beleggingscategorie",$this->pdf->rapport_taal));
	$this->PieChart($Xas,$yas,50,$grafiekData['OIB']['Percentage'],$grafiekData['OIB']['Kleur'], vertaalTekst("Onderverdeling in beleggingscategorie",$this->pdf->rapport_taal));
}
else
  $this->pdf->Pie3D(array(),array(),$Xas,$yas,$diameter,$hoek,$dikte,vertaalTekst("Er kan geen juiste grafiek gepresenteerd worden",$this->pdf->rapport_taal));

$printOIRgrafiek=true;
if(count($grafiekData['OIR']['Percentage'])<1)
  $printOIRgrafiek=false;
foreach($grafiekData['OIR']['Percentage'] as $percentage)
  if($percentage < 0)
    $printOIRgrafiek=false;

if($printOIRgrafiek==true)
{
  $this->pdf->set3dLabels($grafiekData['OIR']['Omschrijving'],$Xas+135,$yas,$grafiekData['OIR']['Kleur']);
	//$this->pdf->Pie3D($grafiekData['OIR']['Percentage'],$grafiekData['OIR']['Kleur'],$Xas+135,$yas,$diameter,$hoek,$dikte,vertaalTekst("Onderverdeling in Regio",$this->pdf->rapport_taal));
	$this->PieChart($Xas+135,$yas,50,$grafiekData['OIR']['Percentage'],$grafiekData['OIR']['Kleur'], vertaalTekst("Onderverdeling in Regio",$this->pdf->rapport_taal));
}
else
  $this->pdf->Pie3D(array(),array(),$Xas+135,$yas,$diameter,$hoek,$dikte,vertaalTekst("Er kan geen juiste grafiek gepresenteerd worden",$this->pdf->rapport_taal));


$balkKleur=array(0,45,91);
$balkKleur=array(0,0,0);
$afm=AFMstd($portefeuille,$rapportageDatum,$this->pdf->debug);


$width=100;
 $this->pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));


//$this->pdf->Rect(155, 120, 120,70);
$this->pdf->setXY(155,124);
		$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
$this->pdf->MultiCell(120,4,vertaalTekst("Risicowijzer AFM",$this->pdf->rapport_taal),0,"C");
//$this->pdf->setXY(175,133);
$this->pdf->setXY(175,140);
$this->afmAfbeelding(80,$afm['std']);
//$this->pdf->setXY(155,180);
//$this->pdf->SetFont($this->pdf->rapport_font, '', 11);
//$this->pdf->MultiCell(120,4,"Standaarddeviatie portefeuille ".$this->formatGetal($afm['std'],1)."%",0,"C");


    $gebruikteCategorie=$this->addZorgBar();
    $this->plotZorgBar2(100,10,$gebruikteCategorie);
    
   if($this->pdf->rapport_datum>mktime(0,0,0,1,1,2014) && $this->pdf->rapport_datum < mktime(0,0,0,4,1,2014))
   {
    		$this->pdf->AddPage();
        $this->pdf->Ln();
        $this->pdf->Line($this->pdf->marge,$this->pdf->GetY(),297-$this->pdf->marge,$this->pdf->GetY());
        $this->pdf->Ln();
        $this->pdf->setAligns('L','L','L','L','L');
        $w=110;
        $this->pdf->SetWidths(array(15,$w,25,$w));
        $this->pdf->row(array('',
'Bijlage:  rapportage risicowijzers.

Stroeve &  Lemberger heeft de  afgelopen jaren veel aandacht geschonken aan het ontwerpen van een rapportage die u een goed inzicht geeft in de rendementsontwikkeling van uw vermogen en de wijze waarop uw vermogen is opgebouwd.

Vanaf dit kwartaal geven wij u ook inzicht in de risico’s waarmee het beleggen van uw vermogen gepaard gaat. Daartoe hebben wij een aantal parameters toegevoegd die naar onze mening transparant weergeven wat de actuele stand van zaken is met betrekking tot het risico dat u in uw portefeuille mag en wilt lopen.

Aan de rapportageset zijn twee risicowijzers toegevoegd:  Een risicowijzer die het risico weergeeft zoals dat specifiek door Stroeve & Lemberger wordt vastgesteld en waarop het risicoprofiel in uw overeenkomst is gebaseerd. Tevens  een risicowijzer gebaseerd op de methodologie zoals die door de Autoriteit Financiële Markten (AFM) wordt gehanteerd. Hieronder wordt kort het gedachtegoed beschreven achter de beide risicowijzers.

Stroeve & Lemberger wijst een risicogetal  toe aan elke individuele belegging binnen uw portefeuille. Hoe groter het risicogetal, des te groter de te verwachten beweeglijkheid van de individuele belegging. Onze beleggingsexperts analyseren op dagelijkse basis de beleggingsportefeuilles en wijzigen indien nodig de kengetallen op basis van hun inschattingen met betrekking tot het te verwachten risico. Het risicogetal van uw portefeuille is een gewogen gemiddelde van de risico’s van uw individuele beleggingen. In de risicowijzer geeft de overgang van het groene naar het rode gebied aan hoe de huidige samenstelling van uw portefeuille zich verhoudt tot de gemaakte afspraken.'
,
''
,
'

De AFM hanteert voor het meten van risico’s binnen uw portefeuille de standaarddeviatie van het rendement. De standaarddeviatie is een maatstaf voor de spreiding van de rendementen van beleggingen, gebaseerd op een analyse van historische rendementen en de correlatie tussen deze rendementen. Aan elke type belegging is door de AFM een standaarddeviatie toegewezen en het risico van uw portefeuille wordt berekend aan de hand van een formule waarin naast de wegingen van de individuele beleggingen ook de correlatie tussen deze beleggingen wordt meegenomen.

Er is een belangrijk verschil aan te wijzen tussen de beide berekeningsmethodes: De methodologie van Stroeve & Lemberger komt tot een risicogetal op basis van inschattingen van toekomstig risico, de methode van de AFM komt tot een risicogetal op basis van historische verhoudingen.

Wij zijn van mening dat door het toevoegen aan de rapportage van beide risicowijzers, u een optimaal inzicht verkrijgt in de risico’s waarmee het beheer van uw portefeuille gepaard gaat.

Mocht u vragen hebben over de rapportage in het algemeen en de toevoeging van de risicoparagraaf in het bijzonder dan kunt u contact opnemen met uw accountmanager. '));
        
   }
        
	}


	function PieChart($Xas,$yas,$w, $data, $colors=null,$titel='')
	{
		$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
		if($titel<>'')
		{
			$this->pdf->setXY($Xas-$w,$yas-$w/2-6);
			$this->pdf->MultiCell($w*2,4,$titel,0,'C');
		}

		$XDiag = $Xas;// $this->pdf->GetX();
		$YDiag = $yas;// $this->pdf->GetY();
		$radius = floor($w / 2);
		if($colors == null)
		{
			for($i = 0;$i < count($data); $i++) {
				$gray = $i * intval(255 / $this->NbVal);
				$colors[$i] = array($gray,$gray,$gray);
			}
		}

		//Sectors
		$this->pdf->SetLineWidth(0.2);
		$angleStart = 0;
		$angleEnd = 0;
		$i = 0;
		foreach($data as $val)
		{
			$angle = floor(($val * 360) / doubleval(array_sum($data)));
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
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

	}

function afmAfbeelding($w,$afmstd)
{
  $x=$this->pdf->getX();
  $y=$this->pdf->getY();

  $afmstd=ceil($afmstd);
  if($afmstd>25)
    $afmstd=25;
      
  $afmstd=sprintf('%02d',$afmstd);

  if(file_exists('images/risicowijzer/'.$afmstd.'.risicowijzer_afm.png'))  
    $this->pdf->Image('images/risicowijzer/'.$afmstd.'.risicowijzer_afm.png', $x, $y,$w );
}


  function addZorgBar()
  {
    include_once("rapport/Zorgplichtcontrole.php");
    $zorgplicht = new Zorgplichtcontrole();
	  $pdata=$this->pdf->portefeuilledata;
	  $zpwaarde=$zorgplicht->zorgplichtMeting($pdata,$this->rapportageDatum); 
    foreach($zpwaarde['categorien'] as $categorie=>$data)
    {
      if(!isset($data['fondsGekoppeld']))
      {
        $gebruikteCategorie=$data;
      }
    }
    foreach($zpwaarde['conclusie'] as $data)
    {
      if($data[0]==$gebruikteCategorie['Zorgplicht'])
      {
        $gebruikteCategorie['percentage']=$data[2];
      }
      $gebruikteCategorie['categorien'][$data[0]]=$data[2];
    }   
    return $gebruikteCategorie;
  }


  function plotZorgBar2($w,$h,$data)
  {
    $data['percentage']=str_replace(',','.',$data['percentage']);
    $yBegin=115;
    $xBegin=20;//155;
    $this->pdf->setXY($xBegin,$yBegin+6);//105 93
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->MultiCell(120,10,vertaalTekst('Risicowijzer Stroeve Lemberger',$this->pdf->rapport_taal),0,"C");
    //$this->pdf->Rect($xBegin,$yBegin+5,120,70);
    $kleurenGroen=array(32,135,73);
    $kleurenRood=array(197,21,50);
    $margeW=2;
    $margeH=0.2;
    $startKleur=array(0,200,50);
	  $eindkleur=array(200,50,50);
	  $stappen=100;
    $xStart=$xBegin+($margeW*1)+10;
    $xStap=($w-(2*$margeW))/$stappen;
    //$y=$yBegin+35;
    $y=$yBegin+45;
    $this->pdf->Rect($xBegin+10, $y, $w, $h, 'D', null, $kleur);
    $this->pdf->Rect($xStart,$y+($margeH*$h),($xStap*$data['Maximum']),$h*(1-2*$margeH),'F','F',$kleurenGroen);
    $this->pdf->Rect($xStart+($xStap*$data['Maximum']),$y+($margeH*$h),($xStap*($stappen-$data['Maximum'])),$h*(1-2*$margeH),'F','F',$kleurenRood);
    $this->pdf->MemImage(base64_decode($this->vuurtorenIMG), $xStart+($xStap*$data['percentage'])-5, $y-(10),5 );
    for($i=0;$i<=$stappen;$i=$i+20)
    {
      $this->pdf->setXY($xStart+($xStap*$i)-5,$y+$h+4);
      $this->pdf->MultiCell(10,4,"$i",0,'C');
    }
    
    
    //$this->pdf->setXY($xBegin+10,180);
    //$this->pdf->SetFont($this->pdf->rapport_font, '', 11);
    //$this->pdf->MultiCell($w,4,"Percentage aandelen ".$this->formatGetal($data['percentage'],1)."%",0,"C");
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  }


}
?>
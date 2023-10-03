<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2016/11/27 11:09:59 $
File Versie					: $Revision: 1.3 $

$Log: RapportOIS_L69.php,v $
Revision 1.3  2016/11/27 11:09:59  rvv
*** empty log message ***

Revision 1.2  2016/09/11 08:30:02  rvv
*** empty log message ***

Revision 1.1  2016/05/11 16:03:47  rvv
*** empty log message ***

Revision 1.1  2016/05/08 19:24:24  rvv
*** empty log message ***

Revision 1.2  2015/12/16 17:06:48  rvv
*** empty log message ***

Revision 1.1  2015/09/05 16:48:04  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIS_L69
{
	function RapportOIS_L69($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Onderverdeling portefeuille";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
    $this->rapportFilter="";
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




	function writeRapport()
	{
		global $__appvar;
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
		$this->pdf->AddPage();
    $this->pdf->templateVars['OIBPaginas']=$this->pdf->page;

		$rapportageDatum = $this->rapportageDatum;
		$rapportageDatumVanaf = $this->rapportageDatumVanaf;
	$portefeuille = $this->portefeuille;

	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
					 "FROM TijdelijkeRapportage WHERE ".
					 " rapportageDatum ='".$rapportageDatum."' AND ".
					 " portefeuille = '".$portefeuille."' ".$this->rapportFilter
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
 			TijdelijkeRapportage.Type = 'rekening' ".$this->rapportFilter." ".
       $__appvar['TijdelijkeRapportageMaakUniek'];
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
		$allekleuren['OIS']['Liquiditeiten']=array('R'=>array('value'=>93),'G'=>array('value'=>203),'B'=>array('value'=>106));


$query="SELECT
if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.beleggingscategorie <> '',TijdelijkeRapportage.beleggingscategorie,'geen')) as categorie,
sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
 if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.Beleggingscategorie <> '',Beleggingscategorien.Omschrijving,'geen')) as categorieOmschrijving
FROM TijdelijkeRapportage 
LEFT JOIN Beleggingscategorien on TijdelijkeRapportage.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
	WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."' ".$this->rapportFilter." "
	.$__appvar['TijdelijkeRapportageMaakUniek'].
	" GROUP BY categorie
	ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde, categorie";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	while($cat = $DB->nextRecord())
	{
	   $data['beleggingscategorie']['data'][$cat['categorie']]['waardeEur']=$cat['WaardeEuro'];
	   $data['beleggingscategorie']['data'][$cat['categorie']]['Omschrijving']=$cat['categorieOmschrijving'];
	   $data['beleggingscategorie']['pieData'][$cat['categorieOmschrijving']]= $cat['WaardeEuro']/$totaalWaarde;
	   $data['beleggingscategorie']['kleurData'][$cat['categorieOmschrijving']]=$allekleuren['OIB'][$cat['categorie']];
	   $data['beleggingscategorie']['kleurData'][$cat['categorieOmschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaarde*100;
	}

	$query="SELECT
if(TijdelijkeRapportage.regio <> '',TijdelijkeRapportage.regio,'geen') as categorie,
sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
if(TijdelijkeRapportage.regio <> '',TijdelijkeRapportage.regioOmschrijving,'geen') as categorieOmschrijving
	FROM TijdelijkeRapportage
  WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'".$this->rapportFilter." "
	.$__appvar['TijdelijkeRapportageMaakUniek'].
	" GROUP BY categorie
	ORDER BY TijdelijkeRapportage.regioVolgorde, TijdelijkeRapportage.regio";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	while($cat = $DB->nextRecord())
	{
	   $data['regio']['data'][$cat['categorie']]['waardeEur']=$cat['WaardeEuro'];
	   $data['regio']['data'][$cat['categorie']]['Omschrijving']=$cat['categorieOmschrijving'];
	   $data['regio']['pieData'][$cat['categorieOmschrijving']]+= $cat['WaardeEuro']/$totaalWaarde;
	   $data['regio']['kleurData'][$cat['categorieOmschrijving']]=$allekleuren['OIR'][$cat['categorie']];
	   $data['regio']['kleurData'][$cat['categorieOmschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaarde*100;
	}


	$query="SELECT
if(TijdelijkeRapportage.type='rekening','Liquiditeiten',TijdelijkeRapportage.beleggingssector) as beleggingssector,
Sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
TijdelijkeRapportage.beleggingssectorOmschrijving as Omschrijving
FROM
TijdelijkeRapportage
WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."' ".$this->rapportFilter." "
	.$__appvar['TijdelijkeRapportageMaakUniek'].
" GROUP BY 	beleggingssector,
TijdelijkeRapportage.type
ORDER BY TijdelijkeRapportage.beleggingssectorVolgorde,TijdelijkeRapportage.type";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	while($cat = $DB->nextRecord())
	{
	  if($cat['beleggingssector']=='Liquiditeiten')
		{
			$cat['Omschrijving'] = 'Liquiditeiten';
		}
		if($cat['beleggingssector']=='')
		{
			$cat['beleggingssector']='geen';
			$cat['Omschrijving'] = 'Geen sector';
		}
	   $data['sector']['data'][$cat['beleggingssector']]['waardeEur'] +=$cat['WaardeEuro'];
	   $data['sector']['data'][$cat['beleggingssector']]['Omschrijving']=$cat['Omschrijving'];
	   $data['sector']['pieData'][$cat['Omschrijving']] = ($data['sector']['data'][$cat['beleggingssector']]['waardeEur']/$totaalWaarde);
	   $data['sector']['kleurData'][$cat['Omschrijving']]=$allekleuren['OIS'][$cat['beleggingssector']];
	   $data['sector']['kleurData'][$cat['Omschrijving']]['percentage']=($data['sector']['data'][$cat['beleggingssector']]['waardeEur']/$totaalWaarde*100);
	}
	//	listarray($data['sector']['data']);
//echo $query;exit;
$this->pdf->setXY(30,37);
//$this->pdf->setXY(65,40);
$this->printPie($data['beleggingscategorie']['pieData'],$data['beleggingscategorie']['kleurData'],'Categorieverdeling '.date("d-m-Y",db2jul($rapportageDatum)),60,50);
$this->pdf->wLegend=0;
$this->pdf->setXY(120,37);
//$this->pdf->setXY(175,40);
$this->printPie($data['sector']['pieData'],$data['sector']['kleurData'],'Sectorverdeling '.date("d-m-Y",db2jul($rapportageDatum)),60,50);
$this->pdf->wLegend=0;

$this->pdf->setXY(210,37);
$this->printPie($data['regio']['pieData'],$data['regio']['kleurData'],'Regioverdeling '.date("d-m-Y",db2jul($rapportageDatum)),60,50);

foreach ($data as $type=>$typeData)
{
  $n=0;
  foreach ($typeData['data'] as $categorie=>$gegevens)
  {
    if(!is_array($regelData[$n]))
      $regelData[$n]=array('','','','','','','','','','');
    if($type=='beleggingscategorie')
      $offset=0;
    if($type=='sector')
      $offset=4;
    if($type=='regio')
      $offset=8;

     $regelData[$n][0]='';
     $regelData[$n][1+$offset]=$gegevens['Omschrijving'];
     $regelData[$n][2+$offset]=$this->formatGetal($gegevens['waardeEur'],0);
     $regelData[$n][3+$offset]=$this->formatGetal($data[$type]['kleurData'][$gegevens['Omschrijving']]['percentage'],2).'%';
     $regelData[$n][4+$offset]='';
     $n++;

     $regelTotaal[$type]['waardeEur']+=$gegevens['waardeEur'];
     $regelTotaal[$type]['percentage']+=round($data[$type]['kleurData'][$gegevens['Omschrijving']]['percentage'],2);
  }

}


foreach ($regelData as $regelNr=>$regel)
{
  ksort($regel);
  $regelData[$regelNr]=$regel;
}

$this->pdf->setXY($this->pdf->marge,135);
$this->pdf->SetWidths(array(5, 50,20,15, 8, 50,20,15, 8, 50,20,15));
//$this->pdf->SetWidths(array(45, 40,20,15, 40, 40,20,15, 15));
$this->pdf->SetAligns(array('L', 'L','R','R',  'L',  'L','R','R',  'L',  'L','R','R'));



//
$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
$this->pdf->CellBorders = array();
$this->pdf->ln(2);

$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize-0.5);
foreach ($regelData as $regel)
{
  $this->pdf->row($regel);
}

$this->pdf->underlinePercentage=0.8;
$this->pdf->CellBorders = array('','','TS','TS','','','TS','TS','','','TS','TS');
$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize-0.5);
$this->pdf->row(array('','Totaal '.date("d-m-Y",db2jul($rapportageDatum)), $this->formatGetal($regelTotaal['beleggingscategorie']['waardeEur']),$this->formatGetal($regelTotaal['beleggingscategorie']['percentage'],0).'%','',
'Totaal '.date("d-m-Y",db2jul($rapportageDatum)), $this->formatGetal($regelTotaal['sector']['waardeEur']),$this->formatGetal($regelTotaal['sector']['percentage'],0).'%'
,'','Totaal '.date("d-m-Y",db2jul($rapportageDatum)), $this->formatGetal($regelTotaal['regio']['waardeEur']),$this->formatGetal($regelTotaal['regio']['percentage'],0).'%'
));
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
unset($this->pdf->CellBorders);
	}



	function printPie($pieData,$kleurdata,$title='',$width=100,$height=100)
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
    // standaardkleuren vervangen voor eigen kleuren.
    $startX=$this->pdf->GetX();

		if(isset($kleurdata))
		{
		  $grafiekKleuren = array();
		  $a=0;
		  while (list($key, $value) = each($kleurdata))
			{
  			if ($value['R']['value'] == 0 && $value['G']['value'] == 0 && $value['B']['value'] == 0)
	  		  $grafiekKleuren[]=$standaardKleuren[$a];
		  	else
			    $grafiekKleuren[] = array($value['R']['value'],$value['G']['value'],$value['B']['value']);
		  	$pieData[$key] = $value['percentage'];
		  	$a++;
			}
		}
		else
		  $grafiekKleuren = $standaardKleuren;

		while (list($key, $value) = each($pieData))
			if ($value < 0)
				$pieData[$key] = -1 * $value;

			//$this->pdf->SetXY(210, $this->pdf->headerStart);
			$y = $this->pdf->getY();
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			$this->pdf->setXY($startX,$y-4);
      	$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
	
			$this->pdf->Cell(50,4,vertaalTekst($title, $this->pdf->rapport_taal),0,0,"C");
			$this->pdf->setXY($startX,$y);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

      $this->pdf->setX($startX);
			$this->PieChart($width, $height, $pieData, '%l (%p)', $grafiekKleuren);
			$hoogte = ($this->pdf->getY() - $y) + 8;
			$this->pdf->setY($y);

			$this->pdf->SetLineWidth($this->pdf->lineWidth);
			$this->pdf->setX($startX);

		//	$this->pdf->Rect($startX,$this->pdf->getY(),$width,$hoogte);

	}

	function PieChart($w, $h, $data, $format, $colors=null)
  {

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 4;
      $hLegend = 2;
      $radius = min($w - $margin * 4 - $hLegend - $this->pdf->wLegend, $h - $margin * 2);
      $radius=min($w,$h);

      $radius = floor($radius / 2);
      $XDiag = $XPage + $margin + $radius;
      $YDiag = $YPage + $margin + $radius;
      if($colors == null) {
          for($i = 0;$i < $this->pdf->NbVal; $i++) {
              $gray = $i * intval(255 / $this->pdf->NbVal);
              $colors[$i] = array($gray,$gray,$gray);
          }
      }

      //Sectors
      $this->pdf->SetLineWidth(0.2);
      $angleStart = 0;
      $angleEnd = 0;
      $i = 0;
      $aantal=count($data);
      foreach($data as $val)
      {
        $angle = floor(($val * 360) / doubleval($this->pdf->sum));

        if ($angle != 0)
        {
          $angleEnd = $angleStart + $angle;

          $avgAngle=($angleStart+$angleEnd)/360*M_PI;
          $factor=1.5;

          if($i==($aantal-1))
            $angleEnd=360;

        //  echo " $angle $angleStart + $angleEnd = ".(($angleStart+$angleEnd)/2)." ".$this->pdf->legends[$i]." | cos:".cos($avgAngle)." | sin:".sin($avgAngle)."  <br>\n";
              $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
              $this->pdf->Sector($XDiag+(sin($avgAngle)*$factor), $YDiag-(cos($avgAngle)*$factor), $radius, $angleStart, $angleEnd);
              $angleStart += $angle;
          }
          $i++;
      }
   //   if ($angleEnd != 360) {
    //      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    //  }

      //Legends
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

      $x1 = $XPage ;
      $x2 = $x1 + $hLegend + $margin;
      $y1 = $YDiag + ($radius) + $margin;

      for($i=0; $i<$this->pdf->NbVal; $i++) {
          $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->pdf->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1);
          $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
          $y1+=$hLegend + 2;
      }

  }

    function SetLegends($data, $format)
  {
      $this->pdf->legends=array();
      $this->pdf->wLegend=0;

      $this->pdf->sum=array_sum($data);

      $this->pdf->NbVal=count($data);
      foreach($data as $l=>$val)
      {
          //$p=sprintf('%.1f',$val/$this->sum*100).'%';
          $p=sprintf('%.1f',$val).'%';
          $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
          $this->pdf->legends[]=$legend;
          $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->pdf->wLegend);
      }
  }

}
?>
<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2016/03/19 16:51:09 $
File Versie					: $Revision: 1.2 $

$Log: RapportOIB_L66.php,v $
Revision 1.2  2016/03/19 16:51:09  rvv
*** empty log message ***

Revision 1.1  2016/03/06 14:37:11  rvv
*** empty log message ***




*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIB_L66
{
	function RapportOIB_L66($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Onderverdeling in beleggingscategorieën";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
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

	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal,TijdelijkeRapportage.hoofdcategorie ".
					 "FROM TijdelijkeRapportage WHERE ".
					 " rapportageDatum ='".$rapportageDatum."' AND ".
					 " portefeuille = '".$portefeuille."' "
					 .$__appvar['TijdelijkeRapportageMaakUniek'].' GROUP BY TijdelijkeRapportage.hoofdcategorie';
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
  $totaalWaarde=0;
  $hoofdcategorieWaarde=array();
  while($data= $DB->nextRecord())
  {
    $hoofdcategorieWaarde[$data['hoofdcategorie']] += $data['totaal'];
	  $totaalWaarde += $data['totaal'];
  }
  $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
					 "FROM TijdelijkeRapportage WHERE ".
					 " rapportageDatum ='".$rapportageDatumVanaf."' AND ".
					 " portefeuille = '".$portefeuille."' "
					 .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$totaalWaardeBegin = $DB->nextRecord();
	$totaalWaardeBegin = $totaalWaardeBegin['totaal'];

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

$query="SELECT KeuzePerVermogensbeheerder.waarde as sector,Omschrijving as sectorOmschrijving 
FROM KeuzePerVermogensbeheerder 
JOIN Beleggingssectoren ON KeuzePerVermogensbeheerder.waarde=Beleggingssectoren.Beleggingssector 
WHERE categorie='Beleggingssectoren' AND Vermogensbeheerder='$beheerder' 
ORDER BY KeuzePerVermogensbeheerder.afdrukvolgorde,Beleggingssectoren.afdrukvolgorde";
	$DB->SQL($query);
	$DB->Query();
	while($cat = $DB->nextRecord())
	{
	   $data['beleggingssector']['data'][$cat['sector']]['waardeEur']=0;
	   $data['beleggingssector']['data'][$cat['sector']]['Omschrijving']=$cat['sectorOmschrijving'];
	   $data['beleggingssector']['pieData'][$cat['sectorOmschrijving']]= 0;
	   $data['beleggingssector']['kleurData'][$cat['sectorOmschrijving']]=$allekleuren['OIS'][$cat['sector']];
	   $data['beleggingssector']['kleurData'][$cat['sectorOmschrijving']]['percentage']=0;
	}

$query="SELECT
if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.beleggingssector <> '',TijdelijkeRapportage.beleggingssector,'geen')) as sector,
sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
 if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.beleggingssector <> '',TijdelijkeRapportage.beleggingssectorOmschrijving,'geen')) as sectorOmschrijving
FROM TijdelijkeRapportage 
	WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."' AND TijdelijkeRapportage.hoofdcategorie='ZAK'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'"
	.$__appvar['TijdelijkeRapportageMaakUniek'].
	" GROUP BY sector
	ORDER BY TijdelijkeRapportage.beleggingssectorVolgorde, sector";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	while($cat = $DB->nextRecord())
	{
	   $data['beleggingssector']['data'][$cat['sector']]['waardeEur']=$cat['WaardeEuro'];
	   $data['beleggingssector']['data'][$cat['sector']]['Omschrijving']=$cat['sectorOmschrijving'];
	   $data['beleggingssector']['pieData'][$cat['sectorOmschrijving']]= $cat['WaardeEuro']/$hoofdcategorieWaarde['ZAK'];
	   $data['beleggingssector']['kleurData'][$cat['sectorOmschrijving']]=$allekleuren['OIS'][$cat['sector']];
	   $data['beleggingssector']['kleurData'][$cat['sectorOmschrijving']]['percentage']=$cat['WaardeEuro']/$hoofdcategorieWaarde['ZAK']*100;
	}

	$query="SELECT
if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.beleggingscategorie <> '',TijdelijkeRapportage.beleggingscategorie,'geen')) as categorie,
sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
 if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.beleggingscategorie <> '',TijdelijkeRapportage.beleggingscategorieOmschrijving,'geen')) as categorieOmschrijving
FROM TijdelijkeRapportage LEFT JOIN Beleggingscategorien on TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.beleggingscategorie
	WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'"
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
TijdelijkeRapportage.valuta,
Sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
Valutas.Omschrijving
FROM
TijdelijkeRapportage
Inner Join Valutas ON Valutas.Valuta = TijdelijkeRapportage.valuta
WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'"
	.$__appvar['TijdelijkeRapportageMaakUniek'].
" GROUP BY Valuta
ORDER BY Valutas.afdrukvolgorde";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	while($cat = $DB->nextRecord())
	{
	   $data['valutaVerdeling']['data'][$cat['valuta']]['waardeEur']=$cat['WaardeEuro'];
	   $data['valutaVerdeling']['data'][$cat['valuta']]['Omschrijving']=$cat['Omschrijving'];
	   $data['valutaVerdeling']['pieData'][$cat['Omschrijving']]= $cat['WaardeEuro']/$totaalWaarde;
	   $data['valutaVerdeling']['kleurData'][$cat['Omschrijving']]=$allekleuren['OIV'][$cat['valuta']];
	   $data['valutaVerdeling']['kleurData'][$cat['Omschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaarde*100;
	}

$this->pdf->setXY(30,37);
//$this->pdf->setXY(65,40);
if(min($data['beleggingscategorie']['pieData']) < 0)
  BarDiagram($this->pdf,60, 50, $data['beleggingscategorie']['pieData'],$data['beleggingscategorie']['kleurData'],'Categorieverdeling');
else
  $this->printPie($data['beleggingscategorie']['pieData'],$data['beleggingscategorie']['kleurData'],'Categorieverdeling',60,50);
$this->pdf->wLegend=0;
$this->pdf->setXY(120,37);
//$this->pdf->setXY(175,40);
$this->printPie($data['beleggingssector']['pieData'],$data['beleggingssector']['kleurData'],'Sectorverdeling zakelijke waarden',60,50);
$this->pdf->wLegend=0;

$this->pdf->setXY(210,37);
$this->printPie($data['valutaVerdeling']['pieData'],$data['valutaVerdeling']['kleurData'],'Valutaverdeling',60,50);


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
      foreach($kleurdata as $key=>$value)
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
			$this->PieChart($width, $height, $pieData, $grafiekKleuren);
			$hoogte = ($this->pdf->getY() - $y) + 8;
			$this->pdf->setY($y);

			$this->pdf->SetLineWidth($this->pdf->lineWidth);
			$this->pdf->setX($startX);

		//	$this->pdf->Rect($startX,$this->pdf->getY(),$width,$hoogte);

	}

	function PieChart($w, $h, $data, $colors=null)
  {

      $this->pdf->sum=array_sum($data);

      $this->pdf->NbVal=count($data);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      //$this->SetLegends($data,$format);

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

      //for($i=0; $i<$this->pdf->NbVal; $i++) 
      $i=0;
      foreach($data as $key=>$value)
      {
        
          $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->pdf->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1);
          $this->pdf->Cell(35,$hLegend,$key);
          $this->pdf->Cell(20,$hLegend, $this->formatGetal($value,1).'%',0,0,'R');
          
          
          $y1+=$hLegend + 2;
          $i++;
      }

  }


}
?>
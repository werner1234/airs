<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/09/12 11:41:19 $
File Versie					: $Revision: 1.8 $

$Log: RapportOIR_L36.php,v $
Revision 1.8  2018/09/12 11:41:19  rvv
*** empty log message ***

Revision 1.7  2017/07/08 17:17:33  rvv
*** empty log message ***

Revision 1.6  2017/07/01 13:24:28  rvv
*** empty log message ***

Revision 1.5  2017/06/28 15:29:22  rvv
*** empty log message ***

Revision 1.4  2017/06/24 16:31:57  rvv
*** empty log message ***

Revision 1.3  2017/05/31 16:09:43  rvv
*** empty log message ***

Revision 1.2  2017/05/25 15:00:42  rvv
*** empty log message ***

Revision 1.1  2017/05/06 17:28:52  rvv
*** empty log message ***

Revision 1.3  2014/03/27 19:30:31  rvv
*** empty log message ***

Revision 1.2  2014/03/26 18:26:15  rvv
*** empty log message ***

Revision 1.1  2014/03/19 16:39:09  rvv
*** empty log message ***

Revision 1.26  2014/02/08 17:42:52  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportOnderverdelingValutaLayout.php");

class RapportOIR_L36
{
	function RapportOIR_L36($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIR";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_OIV_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_OIV_titel;
		else
			$this->pdf->rapport_titel = "Onderverdeling";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
	}

  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		$DB = new DB();
		global $__appvar;

		// voor data
		$this->pdf->widthA = array(25,15,50,25,25,25,15,110);
		$this->pdf->alignA = array('L','R','L','R','R','R','R');

		// voor kopjes
		$this->pdf->widthB = array(40,50,25,25,25,15,102);
		$this->pdf->alignB = array('L','L','R','R','R','R');

		$this->pdf->AddPage();

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

    /*
    $query="SELECT Regio,Omschrijving,Afdrukvolgorde FROM Regios WHERE Regio='IV-Geen'";
    $DB->SQL($query);
    $DB->Query();
    $ivgeen = $DB->nextRecord();
    */
    $ivgeen['duurzaamCategorie']='Liquiditeiten';
    $ivgeen['Omschrijving']='Liquiditeiten';
    //if($ivgeen['Afdrukvolgorde']<1)
    $ivgeen['Afdrukvolgorde']=200;

    $grafieken=array();

    $DB = new DB();

    $query="SELECT id,DuurzaamCategorie,omschrijving,volgorde FROM (
SELECT DuurzaamCategorien.id, DuurzaamCategorien.duurzaamCategorie, DuurzaamCategorien.omschrijving, DuurzaamCategorien.Afdrukvolgorde as volgorde
FROM DuurzaamCategorien 
Join BeleggingssectorPerFonds ON DuurzaamCategorien.DuurzaamCategorie = BeleggingssectorPerFonds.DuurzaamCategorie AND 
BeleggingssectorPerFonds.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' 
UNION 
SELECT
DuurzaamCategorien.id, DuurzaamCategorien.DuurzaamCategorie, DuurzaamCategorien.omschrijving,DuurzaamCategorien.Afdrukvolgorde as volgorde
FROM KeuzePerVermogensbeheerder
JOIN DuurzaamCategorien ON KeuzePerVermogensbeheerder.waarde=DuurzaamCategorien.DuurzaamCategorie
WHERE categorie='DuurzaamCategorie' AND Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
) as DuurzaamCategorie
GROUP BY DuurzaamCategorie Order By volgorde";
    $DB->SQL($query);
    $DB->Query();
    while($categorien = $DB->NextRecord())
    {
      $categorien['percentage']=0;
      $grafieken['DUU'][$categorien['DuurzaamCategorie']]=$categorien; //toevoeging kleuren.

    }


		$query = "SELECT
Sum(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS WaardeEuro,
if(TijdelijkeRapportage.type='rekening','".$ivgeen['Omschrijving']."',TijdelijkeRapportage.duurzaamCategorieOmschrijving) AS omschrijving,
if(TijdelijkeRapportage.type='rekening',".$ivgeen['Afdrukvolgorde'].",TijdelijkeRapportage.duurzaamCategorieVolgorde) as volgorde,
if(TijdelijkeRapportage.type='rekening','".$ivgeen['duurzaamCategorie']."',TijdelijkeRapportage.duurzaamCategorie) as duurzaamCategorie
FROM TijdelijkeRapportage
WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND 
TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
.$__appvar['TijdelijkeRapportageMaakUniek']." 
GROUP BY omschrijving
ORDER BY volgorde";
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();
		while($categorien = $DB->NextRecord())
		{
			if($categorien['omschrijving']=='')
			{
				$categorien['duurzaamCategorie'] = 'geen';
				$categorien['omschrijving'] = 'geen';
			}
			$percentageVanTotaal=$categorien['WaardeEuro']/$totaalWaarde*100;
			$categorien['percentage']=$percentageVanTotaal;
			$grafieken['DUU'][$categorien['duurzaamCategorie']]=$categorien; //toevoeging kleuren.

		}

    $query="SELECT id,attributieCategorie,omschrijving,volgorde FROM (
SELECT AttributieCategorien.id, AttributieCategorien.AttributieCategorie, AttributieCategorien.Omschrijving,AttributieCategorien.Afdrukvolgorde as volgorde
FROM AttributieCategorien 
Join BeleggingssectorPerFonds ON AttributieCategorien.AttributieCategorie = BeleggingssectorPerFonds.AttributieCategorie AND 
BeleggingssectorPerFonds.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' 
UNION 
SELECT
AttributieCategorien.id, AttributieCategorien.AttributieCategorie, AttributieCategorien.Omschrijving,AttributieCategorien.Afdrukvolgorde as volgorde
FROM KeuzePerVermogensbeheerder
JOIN AttributieCategorien ON KeuzePerVermogensbeheerder.waarde=AttributieCategorien.AttributieCategorie
WHERE categorie='AttributieCategorien' AND Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
) as Attributie
GROUP BY AttributieCategorie Order By volgorde";
    $DB->SQL($query);
    $DB->Query();
    while($categorien = $DB->NextRecord())
    {
      $categorien['percentage']=0;
      $grafieken['ATT'][$categorien['attributieCategorie']]=$categorien; //toevoeging kleuren.

    }

/*
    $query="SELECT AttributieCategorie,Omschrijving,Afdrukvolgorde FROM AttributieCategorien WHERE AttributieCategorie='IV-Geen'";
    $DB->SQL($query);
    $DB->Query();
    $ivgeen = $DB->nextRecord();
    if($ivgeen['Afdrukvolgorde']<1)
*/
    $ivgeen['Afdrukvolgorde']=200;
    $ivgeen['AttributieCategorie']='Liquiditeiten';
    $ivgeen['Omschrijving']='Liquiditeiten';

		$query = "SELECT
Sum(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS WaardeEuro,
if(TijdelijkeRapportage.type='rekening','".$ivgeen['Omschrijving']."',TijdelijkeRapportage.attributieCategorieOmschrijving) AS omschrijving,
if(TijdelijkeRapportage.type='rekening',".$ivgeen['Afdrukvolgorde'].",TijdelijkeRapportage.attributieCategorieVolgorde) as volgorde,
if(TijdelijkeRapportage.type='rekening','".$ivgeen['AttributieCategorie']."',TijdelijkeRapportage.attributieCategorie) as attributieCategorie
FROM TijdelijkeRapportage
WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND 
TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek']." 
GROUP BY omschrijving
ORDER BY volgorde";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		while($categorien = $DB->NextRecord())
		{
			if($categorien['attributieCategorie']=='')
			{
				$categorien['attributieCategorie'] = 'geen';
				$categorien['omschrijving'] = 'geen';
			}
			$percentageVanTotaal=$categorien['WaardeEuro']/$totaalWaarde*100;
			$categorien['percentage']=$percentageVanTotaal;
			$grafieken['ATT'][$categorien['attributieCategorie']]=$categorien; //toevoeging kleuren.

		}

    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $kleuren = unserialize($kleuren['grafiek_kleur']);


   $grafiekData=array();
    $kleurdata=array();
    $waardeEur=array();
    foreach($grafieken as $type=>$gdata)
    {
      $kleurgroep=$kleuren[$type];

      foreach($gdata as $categorie=>$categorieData)
      {
        $kleur=$kleurgroep[$categorie];
        if(!is_array($kleur))
        {
          if($categorie=='Liquiditeiten')
            $kleur=array('R'=>array('value'=>100),'G'=>array('value'=>100),'B'=>array('value'=>100));
          else
            $kleur=array('R'=>array('value'=>rand(1,250)),'G'=>array('value'=>rand(1,250)),'B'=>array('value'=>rand(1,250)));

        }
        $kleurdata[$type][$categorieData['omschrijving']]=array($kleur['R']['value'],$kleur['G']['value'],$kleur['B']['value']);

        $grafiekData[$type][$categorieData['omschrijving']]=round($categorieData['percentage'],2);
        $waardeEur[$type][$categorieData['omschrijving']]=$categorieData['WaardeEuro'];
      }


    }

    $n=0;

    ksort($grafiekData);
    foreach($grafiekData as $type=>$data)
    {
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize+2);
      $this->pdf->SetXY(40 + (130*$n) , 50-6);
      if($type=='DUU')
        $this->pdf->MultiCell(80,4,'Sustainable Development Goals',0,'C');
      elseif($type=='ATT')
        $this->pdf->MultiCell(80,4,'Impact Thema\'s',0,'C');

      $this->pdf->SetXY(50 + (130*$n) , 50);

      $barGraph = false;
      foreach ($data as $cat => $waarde)
      {
        if ($waarde < 0)
        {
          $barGraph = true;
        }
      }

      if ($barGraph == false)
      {
        $this->PieChart(50, 50, $data, $waardeEur[$type], $kleurdata[$type]);
      }
      else
      {
        $this->BarDiagram(60, 140, $data, '%l (%p)', $kleurdata[$type], '');
      }
      $n++;
    }
   
    
    
  //  listarray($kleurdata);
//listarray($this->pdf->pieData);
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
          $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->wLegend);
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
      $legendWidth=10;
      $YDiag = $YPage;
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
      $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $nullijn=$XDiag - ($offset * $unit);
    
      $i=0;
      $nbDiv=10;
      
      $this->pdf->SetFont($this->pdf->rapport_font, '', 5);
      if(round($legendaStep,5) <> 0.0)
      {
        //for($x=$nullijn;$x<$XDiag; $x=$x-$legendaStep)
        for($x=$nullijn;$x>$XDiag; $x=$x-$legendaStep)
        {
          $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
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
          $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
          $this->pdf->setXY($x,$YDiag + $hDiag);
          $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
          
          $i++;
          if($i>100)
            break;
        }
      }
     
      $i=0;

      $this->pdf->SetXY($XDiag-$legendWidth, $YDiag);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+4);
      $this->pdf->Cell($lDiag, $hval-5, $titel,0,0,'C');
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);
      
   
      foreach($data as $key=>$val)
      {
          $this->pdf->SetFillColor($colorArray[$key][0],$colorArray[$key][1],$colorArray[$key][2]);
          $xval = $nullijn;
          $lval = ($val * $unit);
          $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
          $hval = $eBaton;
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
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
  
    
function PieChart($w, $h, $data, $dataWaarden, $colors=null,$hcat)
  {

      $this->pdf->sum=array_sum($data);
      $this->pdf->NbVal=count($data);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
     // $this->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 4;
      $hLegend = 2;
      $radius = min($w - $margin * 4 - $hLegend - $this->pdf->wLegend, $h - $margin * 2);
      $radius=min($w,$h);

      $radius = floor($radius / 2);
      $XDiag = $XPage + $margin + $radius;
      $YDiag = $YPage + $margin + $radius;


      //Sectors
      $this->pdf->SetLineWidth(0.2);
      $angleStart = 0;
      $angleEnd = 0;
      $i = 0;
      $aantal=count($data);
      foreach($data as $key=>$val)
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
              $this->pdf->SetFillColor($colors[$key][0],$colors[$key][1],$colors[$key][2]);
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

      $x1 = $XPage -10  ;
      $x2 = $x1 +  $margin;
      $y1 = $YDiag + ($radius) + $margin+5;

$this->pdf->SetXY($this->pdf->GetX(),$y1-5);

      //for($i=0; $i<$this->pdf->NbVal; $i++)
      foreach($data as $key=>$value)
      {
          //$this->pdf->SetXY($x2-30,$y1);
          $this->pdf->SetX($x2-$radius-10);
          $this->pdf->SetFillColor($colors[$key][0],$colors[$key][1],$colors[$key][2]);
          $this->pdf->Rect($x1, $y1+$extraY, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1+$extraY);
          $this->pdf->Cell(60,$hLegend,$key.' ('.$value.'%)');
          $this->pdf->Cell(20,$hLegend,'€ '.$this->formatGetal($dataWaarden[$key],2),0,0,'R');
          $y1+=$hLegend + 2;
          $lastHcat=$hcat[$i];
      }
      $this->pdf->SetFillColor(0,0,0);

  }

}
?>
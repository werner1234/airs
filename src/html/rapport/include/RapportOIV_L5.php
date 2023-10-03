<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.4 $

$Log: RapportOIV_L5.php,v $
Revision 1.4  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.3  2013/10/16 15:35:04  rvv
*** empty log message ***

Revision 1.2  2013/08/18 12:24:51  rvv
*** empty log message ***

Revision 1.1  2013/06/09 18:01:53  rvv
*** empty log message ***

Revision 1.3  2012/06/23 15:20:24  rvv
*** empty log message ***

Revision 1.2  2012/06/20 18:11:09  rvv
*** empty log message ***

Revision 1.1  2012/06/17 13:04:11  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIV_L5
{
	function RapportOIV_L5($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ATT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		if($this->pdf->rapport_OIB_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_OIB_titel;
		else
			$this->pdf->rapport_titel = "Onderverdeling in beleggingscategorie";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
		$this->pdf->underlinePercentage=0.8;
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


	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}


	function writeRapport()
	{
		global $__appvar;
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();

		// voor data
		$this->pdf->widthB = array(40,35,25,25,25,15,115);
		$this->pdf->alignB = array('L','L','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = array(40,35,25,25,25,15,115);
		$this->pdf->alignA = array('L','L','R','R','R','R','R');


		$this->pdf->AddPage();
    $this->pdf->templateVars['OIVPaginas']=$this->pdf->page;

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

		//getTypeGrafiekData($object,$type,$extraWhere='',$items=array())
		$this->getTypeGrafiekData($this,'Beleggingscategorie');
		$this->getTypeGrafiekData($this,'Valuta');
		//listarray($this->pdf->grafiekData);
		//listarray($this->pdf->veldOmschrijvingen);
		$this->pdf->setY(40);
		$this->pdf->setWidths(array(160,35+35,25,25));
		$this->pdf->setAligns(array('L','L','R','R'));
  	$this->pdf->CellBorders = array('','U','U','U','U');
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Valuta','in EUR','in %'));
    $this->pdf->CellBorders = array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $totaal=0;
    $totaalProcent=0;
  	foreach ($this->pdf->grafiekData['Valuta']['port']['waarde'] as $valuta=>$waarde)
		{
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->row(array('',$this->pdf->veldOmschrijvingen['Valuta'][$valuta],$this->formatGetal($waarde,0),$this->formatGetal($this->pdf->grafiekData['Valuta']['port']['procent'][$valuta]*100,1)));
		  $totaal+=$waarde;
		  $totaalProcent+=$this->pdf->grafiekData['Valuta']['port']['procent'][$valuta];
		}
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array('','',array('TS','UU'),array('TS','UU'));
    $this->pdf->row(array('','Totaal',$this->formatGetal($totaal,0),$this->formatGetal($totaalProcent*100,1)));
    $this->pdf->CellBorders = array();
		$this->pdf->setXY(23,130);
	  //PieChart($this->pdf,50, 45, $this->pdf->grafiekData['Valuta']['grafiek'], '%l (%p)',$this->pdf->grafiekData['Valuta']['grafiekKleur']);


	  $this->pdf->setY(40);
		$this->pdf->setWidths(array(5,35+35,25,25));
		$this->pdf->setAligns(array('L','L','R','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array('','U','U','U');
    $this->pdf->row(array('','Beleggingscategorie','in EUR','in %'));
    $this->pdf->CellBorders = array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $totaal=0;
    $totaalProcent=0;

    //$this->pdf->veldOmschrijvingen['Beleggingscategorie']['geenWaarden']="Liquiditeiten";
   	foreach ($this->pdf->grafiekData['Beleggingscategorie']['port']['waarde'] as $categorie=>$waarde)
		{
		      $y=$this->pdf->getY();
		      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		      $this->pdf->row(array('',$this->pdf->veldOmschrijvingen['Beleggingscategorie'][$categorie]));

		      $this->pdf->setY($y);
         
		  		 $this->pdf->row(array('','',$this->formatGetal($waarde,0),$this->formatGetal($this->pdf->grafiekData['Beleggingscategorie']['port']['procent'][$categorie]*100,1)));
		  		 $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		  		 $this->pdf->CellBorders = array();
		  
		  $totaal+=$waarde;
		  $totaalProcent+=$this->pdf->grafiekData['Beleggingscategorie']['port']['procent'][$categorie];
		}
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array('','',array('TS','UU'),array('TS','UU'));
    $this->pdf->row(array('','Totaal',$this->formatGetal($totaal,0),$this->formatGetal($totaalProcent*100,1)));
    $this->pdf->CellBorders = array();

    $this->pdf->setXY(170,130);
	  //PieChart($this->pdf,50, 45, $this->pdf->grafiekData['Beleggingscategorie']['grafiek'], '%l (%p)',$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur']);
    $this->pdf->ln(8);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


/*
if(isset($this->pdf->grafiekData['Beleggingscategorie']['grafiek']['Overige']))
{
  $this->pdf->grafiekData['Beleggingscategorie']['grafiek']['Liquiditeiten']=$this->pdf->grafiekData['Beleggingscategorie']['grafiek']['Overige'];
  unset($this->pdf->grafiekData['Beleggingscategorie']['grafiek']['Overige']);
}
*/


$diameter = 35;
$hoek = 30;
$dikte = 10;
$Xas= 75;
$yas= 140;
//print_r($grafiekData);exit;
foreach ($this->pdf->grafiekData['Beleggingscategorie']['grafiek'] as $omschrijving=>$waarde)
{

  $grafiekData['OIB']['data'][$omschrijving]=$waarde;
}

foreach ($this->pdf->grafiekData['Valuta']['grafiek'] as $omschrijving=>$waarde)
{

  $grafiekData['OIV']['data'][$omschrijving]=$waarde;
}
//listarray($this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur']);

/*
$this->pdf->set3dLabels($grafiekData['OIB']['Omschrijving'],$Xas,$yas,$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur']);
$this->pdf->Pie3D($grafiekData['OIB']['Percentage'],$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur'],$Xas,$yas,$diameter,$hoek,$dikte,"Beleggingscategorie");

$this->pdf->set3dLabels($grafiekData['OIV']['Omschrijving'],$Xas+155,$yas,$this->pdf->grafiekData['Valuta']['grafiekKleur']);
$this->pdf->Pie3D($grafiekData['OIV']['Percentage'],$this->pdf->grafiekData['Valuta']['grafiekKleur'],$Xas+155,$yas,$diameter,$hoek,$dikte,"Valuta");
*/
$this->pdf->setXY(70,120);
$this->BarDiagram(70,70,$grafiekData['OIB']['data'],'%l (%p)',$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur'],'Beleggingscategorie');

$this->pdf->setXY(210,120);
$this->BarDiagram(70,70,$grafiekData['OIV']['data'],'%l (%p)',$this->pdf->grafiekData['Valuta']['grafiekKleur'],'Beleggingscategorie');


  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
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
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $i=0;

      $this->pdf->SetXY($XDiag, $YDiag);
      $this->pdf->Cell($lDiag, $hval-4, $titel,0,0,'C');
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);
    //  listarray($colorArray);exit;
   
      foreach($data as $key=>$val)
      {
          $this->pdf->SetFillColor($colorArray[$i][0],$colorArray[$i][1],$colorArray[$i][2]);
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

	function getTypeGrafiekData($object,$type,$extraWhere='',$items=array())
	{
	  global $__appvar;
	  $DB = new DB();
	  if(!is_array($object->pdf->grafiekKleuren))
	  {
	    $q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$object->pdf->portefeuilledata['Vermogensbeheerder']."'";
	  	$DB->SQL($q);
  		$DB->Query();
  		$kleuren = $DB->LookupRecord();
  		$kleuren = unserialize($kleuren['grafiek_kleur']);
  		$object->pdf->grafiekKleuren=$kleuren;
	  }
    $kleurVertaling=array('Beleggingscategorie'=>'OIB','Valuta'=>'OIV','Regio'=>'OIR','Beleggingssector'=>'OIS');
	  $kleuren=$object->pdf->grafiekKleuren[$kleurVertaling[$type]];

	  if(!isset($object->pdf->rapportageDatumWaarde) || $extraWhere !='')
	  {
	   $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
								 " rapportageDatum = '".$object->rapportageDatum."' AND ".
								 " portefeuille = '".$object->portefeuille."' $extraWhere"
								 .$__appvar['TijdelijkeRapportageMaakUniek'];
  		$DB->SQL($query);
  		$DB->Query();
  		$portefwaarde = $DB->nextRecord();
  		$portTotaal = $portefwaarde['totaal'];
  		if($extraWhere=='')
  	  	$object->pdf->rapportageDatumWaarde=$portTotaal;
	  }
	  else
	    $portTotaal=$object->pdf->rapportageDatumWaarde;

		$query = "SELECT TijdelijkeRapportage.portefeuille, TijdelijkeRapportage.".$type."Omschrijving as Omschrijving, TijdelijkeRapportage.".$type." as type,SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel  ".
			" FROM TijdelijkeRapportage
  			WHERE (TijdelijkeRapportage.portefeuille = '".$object->portefeuille."') AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$object->rapportageDatum."' $extraWhere"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY ".$type."  ORDER BY TijdelijkeRapportage.".$type."Volgorde";
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{
		  $object->pdf->veldOmschrijvingen[$type][$categorien['type']]=vertaalTekst($categorien['Omschrijving'],$object->pdf->rapport_taal);
		  if ($categorien['type']=='')
		    $categorien['type']='geenWaarden';

		  if(count($items) > 0 && !in_array($categorien['type'],$items))
		  {
		    $categorien['type']='Overige';
		    $object->pdf->veldOmschrijvingen[$type][$categorien['type']]='Overige';
		    $kleuren[$categorien['type']]=array('R'=>array('value'=>100),'G'=>array('value'=>100),'B'=>array('value'=>100));
		  }


      $valutaData[$categorien['type']]['port']['waarde']+=$categorien['subtotaalactueel'];
    }

		foreach ($valutaData as $waarde=>$data)
		{
		  if(isset($data['port']['waarde']))
		  {
        $veldnaam=$object->pdf->veldOmschrijvingen[$type][$waarde];
        if($veldnaam=='')
          $veldnaam='Overige';

		    $typeData['port']['procent'][$waarde]=$data['port']['waarde']/$portTotaal;
		    $typeData['port']['waarde'][$waarde]=$data['port']['waarde'];
		    $typeData['grafiek'][$veldnaam]=$typeData['port']['procent'][$waarde]*100;

		    //if($veldnaam=='Overige' && isset($kleuren['Liquiditeiten']))
		    //  $waarde='Liquiditeiten';

		    $typeData['grafiekKleur'][]=array($kleuren[$waarde]['R']['value'],$kleuren[$waarde]['G']['value'],$kleuren[$waarde]['B']['value']);
		  }
		}

   $object->pdf->grafiekData[$type]=$typeData;

	}
}

?>
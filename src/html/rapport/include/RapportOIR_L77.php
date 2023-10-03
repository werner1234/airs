<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/06/08 05:43:48 $
 		File Versie					: $Revision: 1.18 $

 		$Log: RapportOIB_L77.php,v $
 		Revision 1.18  2020/06/08 05:43:48  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2020/06/06 15:48:23  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2019/09/14 17:09:05  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2019/02/23 18:32:59  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2018/12/07 11:57:08  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2018/12/06 17:51:24  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2018/11/22 07:25:26  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2018/10/24 16:00:59  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2018/10/20 18:05:20  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2018/10/17 15:37:17  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2018/10/13 17:18:13  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2018/10/10 15:50:56  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2018/10/08 06:36:49  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/10/07 10:19:56  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2018/10/06 17:20:57  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2018/09/26 15:53:28  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.1  2018/05/20 10:39:24  rvv
 		*** empty log message ***
 		

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIR_L77
{
	function RapportOIR_L77($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIR";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titelKort = vertaalTekst("Vermogensverdeling",$this->pdf->rapport_taal);
		$this->pdf->rapport_titel = $this->pdf->rapport_titelKort;//." ".vertaalTekst("per",$this->pdf->rapport_taal)." ".date('d.m.Y',$this->pdf->rapport_datum);
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
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
	}
  
 



	function writeRapport()
	{

	global $__appvar;
	$DB=new DB();
	$rapportageDatum = $this->rapportageDatum;
	$portefeuille = $this->portefeuille;
    
    
    $verdeling=array();
    $categorieen=array();
    $hoofdcategorieen=array();
    $categorienPerHoofdcategorie=array();
    $datumArray=array($this->rapportageDatumVanaf,$rapportageDatum);

  
    $query = "SELECT Beleggingscategorie,BeleggingscategorieOmschrijving, Hoofdcategorie,HoofdcategorieOmschrijving,sum(actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro,rapportageDatum
FROM TijdelijkeRapportage
WHERE rapportageDatum IN('" . implode("','",$datumArray) . "') AND portefeuille = '" . $portefeuille . "' ". $__appvar['TijdelijkeRapportageMaakUniek']."
GROUP BY Hoofdcategorie,Beleggingscategorie,rapportageDatum
ORDER BY HoofdcategorieVolgorde,BeleggingscategorieVolgorde,rapportageDatum" ;
    $DB->SQL($query);
    $DB->Query();
  
    while ($data = $DB->nextRecord())
    {
      $verdeling[$data['rapportageDatum']][$data['Hoofdcategorie']][$data['Beleggingscategorie']] += $data['actuelePortefeuilleWaardeEuro'];
      $categorieen[$data['Beleggingscategorie']]=$data['BeleggingscategorieOmschrijving'];
      $hoofdcategorieen[$data['Hoofdcategorie']]=$data['HoofdcategorieOmschrijving'];
      $categorienPerHoofdcategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]=$data['Beleggingscategorie'];
    }

  //listarray($verdeling);exit;

	//Kleuren instellen
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur, grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);
    
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titelKort;
    
    $this->pdf->SetAligns(array('L','R','R','C','L','R','R','C'));
    $this->pdf->setWidths(array(40,30,30,  40  ,40,30,30,10,));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 297-2*$this->pdf->marge, 8 , 'F');
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    
    $header=array();
    foreach($hoofdcategorieen as $Hoofdcategorie=>$hoofdcategorieOmschrijving)
    {
      $header[]=$hoofdcategorieOmschrijving;
      foreach($datumArray as $datum)
        $header[]=$datum;
      $header[]='';
      
    }
    $this->pdf->row($header);
    $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
    $this->pdf->ln();
    $rows=array();
    
    
    $headerHeight=60;
    $lwb=(297/2)-$this->pdf->marge; //133.5
    $vwh=((210-$headerHeight-$this->pdf->marge)/2+$headerHeight)-$headerHeight;
    $chartsize=50;
    $extraBarW=25;
    $extraX=-18;
    
    $offset=0;
    $grafiekOffset=0;
    foreach($hoofdcategorieen as $Hoofdcategorie=>$HoofdcategorieOmschrijving)
    {
      $catTotaalBegin=array_sum($verdeling[$this->rapportageDatumVanaf][$Hoofdcategorie]);
      $catTotaalEind=array_sum($verdeling[$this->rapportageDatum][$Hoofdcategorie]);
      $i=0;
      foreach($categorienPerHoofdcategorie[$Hoofdcategorie] as $categorie)
      {
        $rows[$i][0+$offset]=$categorieen[$categorie];
        $rows[$i][1+$offset]=$this->formatGetal($verdeling[$this->rapportageDatumVanaf][$Hoofdcategorie][$categorie],0);
        $rows[$i][2+$offset]=$this->formatGetal($verdeling[$this->rapportageDatum][$Hoofdcategorie][$categorie],0);
        $rows[$i][3+$offset]='';
        $i++;
        $grafiekData[0+$grafiekOffset]['Percentage'][$categorieen[$categorie]]=$verdeling[$this->rapportageDatum][$Hoofdcategorie][$categorie]/$catTotaalEind*100;
        $grafiekData[1+$grafiekOffset]['Percentage'][$categorieen[$categorie]]=$verdeling[$this->rapportageDatumVanaf][$Hoofdcategorie][$categorie]/$catTotaalBegin*100;
        $grafiekData[0+$grafiekOffset]['kleur'][]=array($allekleuren['OIB'][$categorie]['R']['value'],$allekleuren['OIB'][$categorie]['G']['value'],$allekleuren['OIB'][$categorie]['B']['value']);
        $grafiekData[1+$grafiekOffset]['kleur'][]=array($allekleuren['OIB'][$categorie]['R']['value'],$allekleuren['OIB'][$categorie]['G']['value'],$allekleuren['OIB'][$categorie]['B']['value']);
      }
      $rows[$i][0+$offset]='Totaal';
      $rows[$i][1+$offset]=$this->formatGetal($catTotaalBegin,0);
      $rows[$i][2+$offset]=$this->formatGetal($catTotaalEind,0);
      $rows[$i][3+$offset]='';
  
      $grafieken[0+$grafiekOffset]=array('x'=>$this->pdf->marge+(($lwb/4)*($offset+1.5)-$chartsize/2)+$extraX,'y'=>$headerHeight,        'titel'=>$HoofdcategorieOmschrijving.' '.$this->rapportageDatum);
      $grafieken[1+$grafiekOffset]=array('x'=>$this->pdf->marge+(($lwb/4)*($offset+1.5)-$chartsize/2)+$extraX,'y'=>$headerHeight+$vwh-10,'titel'=>$HoofdcategorieOmschrijving.' '.$this->rapportageDatumVanaf);
      
      $offset+=4;
      $grafiekOffset+=2;
    }
    foreach($rows as $row)
    {
      if($row[0]=='Totaal')
        $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      else
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->row($row);
    }
    
    foreach($grafieken as $grafiekIndex=>$grafiekSettings)
    {
  
      $this->pdf->setXY($grafiekSettings['x'],$grafiekSettings['y']);
      if(min($grafiekData[$grafiekIndex]['Percentage']) < 0)
        $this->BarDiagram($chartsize+$extraBarW,$chartsize,$grafiekData[$grafiekIndex]['Percentage'],'%l',$grafiekData[$grafiekIndex]['kleur'],$grafiekSettings['titel']);
      else
      {
        $legendaStart=$this->correctLegentHeight(count($grafiekData[$grafiekIndex]['Percentage']));
        PieChart_L77($this->pdf,$chartsize,$vwh,$grafiekData[$grafiekIndex]['Percentage'],'%l',$grafiekData[$grafiekIndex]['kleur'],$grafiekSettings['titel'],$legendaStart);
      }
    }
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}
  
  function correctLegentHeight($regels)
  {
    return array($this->pdf->GetX()+60,$this->pdf->GetY()+ 35 -($regels*4)/2);
     
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

      $valIndRepere=round($valIndRepere/$unit/5)*5;


      $this->pdf->SetLineWidth(0.2);
      //$this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $nullijn=$XDiag - ($offset * $unit);
    
      $i=0;
      $nbDiv=10;

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
  
}
?>
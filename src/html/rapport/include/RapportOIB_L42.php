<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.24 $

$Log: RapportOIB_L42.php,v $
Revision 1.24  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.23  2015/04/04 15:15:15  rvv
*** empty log message ***

Revision 1.22  2014/12/31 18:09:06  rvv
*** empty log message ***

Revision 1.21  2014/12/17 16:14:40  rvv
*** empty log message ***

Revision 1.20  2014/12/10 16:58:25  rvv
*** empty log message ***

Revision 1.19  2014/12/06 18:13:44  rvv
*** empty log message ***

Revision 1.18  2013/11/06 16:55:30  rvv
*** empty log message ***

Revision 1.17  2013/08/24 15:48:47  rvv
*** empty log message ***

Revision 1.16  2013/08/18 12:23:35  rvv
*** empty log message ***

Revision 1.15  2013/08/10 15:48:01  rvv
*** empty log message ***

Revision 1.14  2013/07/28 09:59:15  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L42.php");

class RapportOIB_L42
{

	function RapportOIB_L42($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		$this->pdf->rapport_titel = "Spreiding beleggingscategorieën en valuta's per ".
      date("j",$this->pdf->rapport_datum)." ".
      vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".
      date("Y",$this->pdf->rapport_datum);

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->jaarGeleden=date("Y-m-d",mktime(0,0,0,date('m',$this->pdf->rapport_datum),date('d',$this->pdf->rapport_datum),date('Y',$this->pdf->rapport_datum)-1));

    $this->att=new ATTberekening_L42($this);
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
		$this->pdf->SetLineWidth($this->pdf->lineWidth);
    $data=$this->ophalenOIBData();
    $valutas=$data['valutas'];
    $categorien=$data['categorien'];
    $hoofdcategorien=$data['hoofdcategorie'];
    $table=$data['table'];
    $categorieWidth=50;
    $totalWidth=297-140-$categorieWidth;// -100 rechter marge

    $colWidth=($totalWidth-$categorieWidth-($this->pdf->marge*2));
    $header=array('');
    $widths=array($categorieWidth);
    $aligns=array('L');
    //$headerItems=$valutas;
    $headerItems['Totaal']='Totaal';
    $totaalBorders=array('T');
    $headerBorders=array(array('T','U'));
    foreach($headerItems as $valuta=>$omschrijving)
    {
      $header[]=$valuta;
      //$header[]='in %';
      $widths[]=$colWidth;
      //$widths[]=$colWidth;
      $aligns[]='R';
      //$aligns[]='R';
      $totaalBorders[]='T';
      //$totaalBorders[]='T';
      $headerBorders[]=array('T','U');
      //$headerBorders[]=array('T','U');
    }

		$this->pdf->AddPage();
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);

    $this->pdf->setY(33);
    $this->pdf->MultiCell(array_sum($widths),5,"Valutaverdeling in Euro",0,'C');
    $this->pdf->setWidths($widths);
    $this->pdf->SetAligns($aligns);
    $this->pdf->templateVars['OIBPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['OIBPaginas']=$this->pdf->rapport_titel;
 		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders=$headerBorders;    
    $this->pdf->row($header);
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $categorieItems=$valutas;
    $categorieItems['Totaal']='Totaal';
    foreach($categorieItems as $categorie=>$categorieOmschrijving)
    {
      $tmpRow=array($categorieOmschrijving);
      foreach($headerItems as $valuta=>$omschrijving)
      {
        $tmpRow[]=$this->formatGetal($table['valuta'][$categorie]['waardeEUR'],0);
      }
      if($categorie=='Totaal')
        $this->pdf->CellBorders=$totaalBorders;
      $this->pdf->row($tmpRow);
    }
    
    //hcat
    
    $this->pdf->Ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->MultiCell(array_sum($widths),5,"Hoofd-beleggingscategorieën",0,'C');   
    
    $this->pdf->CellBorders=$headerBorders;    
    $this->pdf->row($header);
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $categorieItems=$hoofdcategorien;
    $categorieItems['Totaal']='Totaal';

    foreach($categorieItems as $categorie=>$categorieOmschrijving)
    {
      $tmpRow=array($categorieOmschrijving);
      foreach($headerItems as $valuta=>$omschrijving)
      {
        $tmpRow[]=$this->formatGetal($table['hoofdcategorie'][$categorie]['waardeEUR'],0);
      }
      if($categorie=='Totaal')
        $this->pdf->CellBorders=$totaalBorders;
      $this->pdf->row($tmpRow);
    }
    
    unset($this->pdf->CellBorders);
    
    $this->addCategorieBar($table,$categorien);
    $this->addValutaPie($table);
    $this->addcategoriePie($table,$hoofdcategorien);
    $this->toonVerdeling();
    
	}
  


  
  function ophalenOIBData()
  {
    
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$DB = new DB();
    
    $q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);
    $this->categorieKleuren=$allekleuren['OIB'];
    
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal 
    FROM TijdelijkeRapportage WHERE rapportageDatum ='".$this->rapportageDatum."' AND 
    portefeuille = '".$this->portefeuille."' "
		.$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];
  
    $query="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $DB->SQL($query);
    $data=$DB->lookupRecord();
    $this->kleuren=unserialize($data['grafiek_kleur']);
    if($this->kleuren['OIS']['Liquiditeiten']['G']['value']==0)
      $this->kleuren['OIS']['Liquiditeiten']=$this->kleuren['OIB']['Liquiditeiten'];
    foreach($this->kleuren as $groep=>$kleuren)
    {
      foreach($kleuren as $cat=>$kleurdata)
        $this->kleuren['alle'][$cat]=$kleurdata;
    }
      
  $query = "SELECT TijdelijkeRapportage.valuta,
  TijdelijkeRapportage.valutaOmschrijving,
  TijdelijkeRapportage.valutaVolgorde
FROM TijdelijkeRapportage WHERE 
TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND 
TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
.$__appvar['TijdelijkeRapportageMaakUniek'].
" GROUP BY TijdelijkeRapportage.valuta
ORDER BY TijdelijkeRapportage.valutaVolgorde asc";  
		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->nextRecord())
      $valutas[$data['valuta']]=$data['valutaOmschrijving'];

  $query = "SELECT TijdelijkeRapportage.beleggingssector,
TijdelijkeRapportage.beleggingssectorOmschrijving,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.beleggingscategorieOmschrijving,
  TijdelijkeRapportage.valutaVolgorde,
  if( TijdelijkeRapportage.beleggingssectorVolgorde=0,128, TijdelijkeRapportage.beleggingssectorVolgorde) as volgorde
FROM TijdelijkeRapportage WHERE 
TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND 
TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
.$__appvar['TijdelijkeRapportageMaakUniek'].
" GROUP BY TijdelijkeRapportage.beleggingscategorie,TijdelijkeRapportage.beleggingssector
ORDER BY volgorde";  
		$DB->SQL($query);  
		$DB->Query();
		while($data = $DB->nextRecord())
    {
      if($data['beleggingssector']=='')
      {
        $data['beleggingssector']=$data['beleggingscategorie'];
        $data['beleggingssectorOmschrijving']=$data['beleggingscategorieOmschrijving'];
      }
      $categorien[$data['beleggingssector']]=$data['beleggingssectorOmschrijving'];
    }

    
    $query = "SELECT 
Sum(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS actuelePortefeuilleWaardeInValuta,
Sum(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS actuelePortefeuilleWaardeEuro,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.beleggingssector,
TijdelijkeRapportage.beleggingscategorie
FROM TijdelijkeRapportage WHERE 
TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND 
TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
.$__appvar['TijdelijkeRapportageMaakUniek'].
" GROUP BY TijdelijkeRapportage.valuta,TijdelijkeRapportage.beleggingssector
ORDER BY TijdelijkeRapportage.beleggingssectorVolgorde,TijdelijkeRapportage.valutaVolgorde";
		$DB->SQL($query); 
		$DB->Query();
		while($data = $DB->nextRecord())
    {
      if($data['beleggingssector']=='')
        $data['beleggingssector']=$data['beleggingscategorie'];
      $table['valuta'][$data['valuta']]['waardeEUR']+=$data['actuelePortefeuilleWaardeEuro'];
      $table['valuta'][$data['valuta']]['aandeel']+=$data['actuelePortefeuilleWaardeEuro']/$totaalWaarde;
      $table['valuta']['Totaal']['waardeEUR']+=$data['actuelePortefeuilleWaardeEuro'];
      $table['valuta']['Totaal']['aandeel']+=$data['actuelePortefeuilleWaardeEuro']/$totaalWaarde;
      $table['Totaal'][$data['beleggingssector']]['waardeEUR']+=$data['actuelePortefeuilleWaardeEuro'];
      $table['Totaal'][$data['beleggingssector']]['aandeel']+=$data['actuelePortefeuilleWaardeEuro']/$totaalWaarde;
      $table['Totaal']['Totaal']['waardeEUR']+=$data['actuelePortefeuilleWaardeEuro'];
      $table['Totaal']['Totaal']['aandeel']+=$data['actuelePortefeuilleWaardeEuro']/$totaalWaarde;
    } 
    
    $query = "SELECT 
Sum(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS actuelePortefeuilleWaardeInValuta,
Sum(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS actuelePortefeuilleWaardeEuro,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.beleggingscategorieOmschrijving
FROM TijdelijkeRapportage WHERE 
TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND 
TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
.$__appvar['TijdelijkeRapportageMaakUniek'].
" GROUP BY TijdelijkeRapportage.beleggingscategorie
ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde";
		$DB->SQL($query); 
		$DB->Query();
		while($data = $DB->nextRecord())
    {
      $hoofdcategorie[$data['beleggingscategorie']]=$data['beleggingscategorieOmschrijving'];
      $table['hoofdcategorie'][$data['beleggingscategorie']]['waardeEUR']+=$data['actuelePortefeuilleWaardeEuro'];
      $table['hoofdcategorie'][$data['beleggingscategorie']]['aandeel']+=$data['actuelePortefeuilleWaardeEuro']/$totaalWaarde;
      $table['hoofdcategorie']['Totaal']['waardeEUR']+=$data['actuelePortefeuilleWaardeEuro'];
      $table['hoofdcategorie']['Totaal']['aandeel']+=$data['actuelePortefeuilleWaardeEuro']/$totaalWaarde;
    } 
    
    
    

    return array('table'=>$table,'valutas'=>$valutas,'categorien'=>$categorien,'hoofdcategorie'=>$hoofdcategorie);
  }


  

    
  function addCategorieBar($data,$categorien)
  {
    $min=0;
    $max=1;
    $kleuren=$this->kleuren['OIS'];
    foreach($categorien as $categorie=>$categorieOmschrijving)
    {
      $waarden=$data['Totaal'][$categorie];
      if($categorie <> 'Totaal')
      {
      $aandeel=$waarden['aandeel']*100;
	    $grafiekData[$categorien[$categorie]]=round($aandeel,5);
      if($aandeel > $max)
        $max=$aandeel;
      if($aandeel < $min)
        $min=$aandeel;
	    $grafiekKleurData[$categorien[$categorie]]=array($kleuren[$categorie]['R']['value'],$kleuren[$categorie]['G']['value'],$kleuren[$categorie]['B']['value']);
	    } 
    }
    $xLocatie=70;
    $yLocatie=112;

    $this->pdf->setXY($xLocatie,$yLocatie);
    $this->pdf->SetFont($this->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->MultiCell(110,5,"Sub-beleggingscategorieën",0,'C');
	  $this->pdf->setXY($xLocatie+2,$yLocatie+15);
    $this->pdf->Rect($xLocatie+2,117,110-5,80);
	  $this->BarDiagram(100, 60, $grafiekData, '%l (%p)',$grafiekKleurData,$max,$min,'');//Sector verdeling
}


function toonVerdeling()
{
  //if(!isset($this->waarden['categorie']))
  $this->waarden['categorie']=$this->att->bereken($this->jaarGeleden,  $this->rapportageDatum,'categorie');
 // $this->waarden['Hoofdcategorie']=$this->att->bereken($this->jaarGeleden,$this->rapportageDatum,'Hoofdcategorie');
 
 //listarray($this->waarden['categorie']);
  $categorien=array_keys($this->waarden['categorie']);
  foreach($categorien as $categorie)
  {
    if($categorie <> 'totaal')
    {
      foreach($this->waarden['categorie'][$categorie]['perfWaarden'] as $datum=>$perfData)
      {
       // echo "$categorie $datum  ".$perfData['aandeelOpTotaal']."<br>\n";
       // if($perfData['aandeelOpTotaal']<>0)
          $verdeling[$datum][$categorie]=$perfData['aandeelOpTotaal']*100;
        //echo $categorie.' '.$datum.' '; listarray($perfData['aandeelOpTotaal']*100);
      }
    }
  }


    $xLocatie=180;
    $extraHoogte=80;
    $yLocatie=112-$extraHoogte;
    $this->pdf->setXY($xLocatie,$yLocatie);
    $this->pdf->SetFont($this->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->MultiCell(110,5,"Ontwikkeling verdeling hoofd-beleggingscategorieën",0,'C');
	  $this->pdf->setXY($xLocatie+5,$yLocatie+15);
    $this->pdf->Rect($xLocatie,$yLocatie+5,109,80+$extraHoogte);
   
  //  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  $this->pdf->SetXY($xLocatie+10,$yLocatie+60+$extraHoogte);
  $this->VBarDiagram(99,50+$extraHoogte,$verdeling);
  
}

  function addcategoriePie($data,$omschrijvingen)
  {
    
    /*
    $this->att->indexPerformance=false;
    $this->waarden['categorie']=$this->att->bereken($this->rapportageDatumVanaf,  $this->rapportageDatum,'categorie');
    $categorien=array_keys($this->waarden['categorie']);

    $kleuren=$this->kleuren['OIB'];
    foreach($categorien as $categorie)
    {
      $perc=round($this->waarden['categorie'][$categorie]['aandeelOpTotaal']*100,5);
      if($perc <> 0 && $categorie <> 'totaal' )
      {
	      $grafiekData[$this->att->categorien[$categorie]]=$perc;
        $grafiekData3d[]=$perc;
        $grafiekOmschrijving3d[]=$this->att->categorien[$categorie]." (".round($perc,1)."%)";
	      $grafiekKleurData[]=array($kleuren[$categorie]['R']['value'],$kleuren[$categorie]['G']['value'],$kleuren[$categorie]['B']['value']);
        //listarray($this->waarden['categorie'][$categorie]['aandeelOpTotaal']);
      }
      
	  }
    */
    
    $kleuren=$this->kleuren['OIB'];
    //foreach($categorien as $categorie)
   // listarray($data);
    foreach($data['hoofdcategorie'] as $categorie=>$waarden)
    {
      if($categorie <> 'Totaal')
      {
        $aandeel=$waarden['aandeel']*100;
        $grafiekData[$categorie]=round($aandeel,1);
        $grafiekData3d[]=round($aandeel,1);
        $grafiekOmschrijving3d[]=$omschrijvingen[$categorie]." (".round($aandeel,1)."%)";
	      $grafiekKleurData[]=array($kleuren[$categorie]['R']['value'],$kleuren[$categorie]['G']['value'],$kleuren[$categorie]['B']['value']);
      }
	  }
    
      
    $xLocatie=5;
    $yLocatie=112;
    $this->pdf->setXY($xLocatie-5,$yLocatie);
    $this->pdf->SetFont($this->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->MultiCell(80,5,"Hoofd-beleggingscategorieën",0,'C');
	  $this->pdf->setXY($xLocatie+5,$yLocatie+15);
    $this->pdf->Rect($this->pdf->marge,117,61,80);
    $this->pdf->setXY(15,120);
    $legendaLocatie='z';
    //PieChart($this->pdf,80, 45, $grafiekData, '%l (%p)', $grafiekKleurData,$legendaLocatie);
    
    $grafiekX=38;
    $grafiekY=130;
    $radius=25;
    $this->pdf->Pie3D($grafiekData3d,$grafiekKleurData,$grafiekX,$grafiekY,$radius,30,5,'',0);
    $stringWidth=0;
    $hLegend=3;
    $this->pdf->SetFont($this->rapport_font, '', $this->pdf->rapport_fontsize-2);
    foreach($grafiekOmschrijving3d as $cat)
      $stringWidth=max($stringWidth,$this->pdf->GetStringWidth($cat));
    $stringWidth+=5;  
    $this->pdf->SetXY($grafiekX,$grafiekY);
    foreach($grafiekOmschrijving3d as $i=>$cat)
    {
      $this->pdf->SetFillColor($grafiekKleurData[$i][0],$grafiekKleurData[$i][1],$grafiekKleurData[$i][2]);
      $this->pdf->Rect($grafiekX-($stringWidth/2), $grafiekY+$radius+$i*5, $hLegend, $hLegend, 'DF');
      $this->pdf->SetXY($grafiekX-($stringWidth/2)+5,$grafiekY+$radius+$i*5);
      $this->pdf->Cell(0,$hLegend,$cat);
    }
    
  }

function addValutaPie($data)
{
  
   // $att=new ATTberekening_L42($this);
   // $att->indexPerformance=false;
   // $this->waarden['categorie']=$att->bereken($this->rapportageDatumVanaf,  $this->rapportageDatum,'categorie');
   // $categorien=array_keys($this->waarden['categorie']);
 //listarray($this->waarden['categorie']);
    $kleuren=$this->kleuren['OIV'];
    $min=0;
    $max=1;
    //foreach($categorien as $categorie)
   // listarray($data);
    foreach($data['valuta'] as $categorie=>$waarden)
    {
      if($categorie <> 'Totaal')
      {
        $aandeel=$waarden['aandeel']*100;
        $grafiekData[$categorie]=round($aandeel,1);
        $grafiekData3d[]=round($aandeel,1);
        $grafiekOmschrijving3d[]=$categorie." (".round($aandeel,1)."%)";
        if($aandeel > $max)
          $max=$aandeel;
        if($aandeel < $min)
          $min=$aandeel;
	      $grafiekKleurData[]=array($kleuren[$categorie]['R']['value'],$kleuren[$categorie]['G']['value'],$kleuren[$categorie]['B']['value']);
      }
	  }
    
    $startHeight=32;//105;
    $extrax=-108;
    $this->pdf->setXY(212+$extrax,$startHeight);
    $this->pdf->SetFont($this->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->MultiCell(80,5,"Valutaverdeling",0,'C');
    $this->pdf->Rect(215+$extrax,$startHeight+5,70,70);
	  $this->pdf->setXY(230+$extrax,$startHeight+05);
    //PieChart($this->pdf,80, 45, $grafiekData, '%l (%p)', $grafiekKleurData,'z');
    
//    $this->pdf->set3dLabels($grafiekOmschrijving3d,170,50,$grafiekKleurData);
    $grafiekX=143;
    $grafiekY=55;
    $radius=25;
    $this->pdf->Pie3D($grafiekData3d,$grafiekKleurData,$grafiekX,$grafiekY,$radius,30,5,'',0);
    $stringWidth=0;
    $hLegend=3;
    $this->pdf->SetFont($this->rapport_font, '', $this->pdf->rapport_fontsize-2);
    foreach($grafiekOmschrijving3d as $cat)
      $stringWidth=max($stringWidth,$this->pdf->GetStringWidth($cat));
    $stringWidth+=5;  
    $this->pdf->SetXY($grafiekX,$grafiekY);
    foreach($grafiekOmschrijving3d as $i=>$cat)
    {
      $this->pdf->SetFillColor($grafiekKleurData[$i][0],$grafiekKleurData[$i][1],$grafiekKleurData[$i][2]);
      $this->pdf->Rect($grafiekX-($stringWidth/2), $grafiekY+$radius+$i*5, $hLegend, $hLegend, 'DF');
      $this->pdf->SetXY($grafiekX-($stringWidth/2)+5,$grafiekY+$radius+$i*5);
      $this->pdf->Cell(0,$hLegend,$cat);
    }
	 // $this->BarDiagram(100, 60, $grafiekData, '%l (%p)',$grafiekKleurData,$min,$max);
}




    function BarDiagram($w, $h, $data, $format, $colorArray=null, $maxVal=0,$minVal=0 ,$titel)
  {

$nbDiv=4;
      $this->pdf->SetFont($this->rapport_font, '', $this->pdf->rapport_fontsize-2);
      $this->pdf->SetLegends($data,$format);


      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $legendWidth=50;
      $YDiag = $YPage + $margin;
      $hDiag = floor($h - $margin * 2);
      $XDiag = $XPage + $margin * 2 + $legendWidth;
      $lDiag = floor($w - $margin * 3 - $legendWidth);
      if($color == null)
          $color=array(155,155,155);
      if ($maxVal == 0) {
          $maxVal = max($data)*1.1;
      }
      //if ($minVal == 0) {
      //    $minVal = min($data)*1.1;
      //}

      $offset=$minVal;
      $valIndRepere = ceil(($maxVal-$minVal) / $nbDiv);
      $bandBreedte = $valIndRepere * $nbDiv;
      $lRepere = floor($lDiag / $nbDiv);
      $unit = $lDiag / $bandBreedte;
      $hBar = floor($hDiag / ($this->pdf->NbVal + 1));
      $hDiag = $hBar * ($this->pdf->NbVal + 1);
      $eBaton = floor($hBar * 80 / 100);
      $legendaStep=$unit;
      if($bandBreedte/$legendaStep > $nbDiv)
        $legendaStep=$legendaStep*5;
      if($bandBreedte/$legendaStep > $nbDiv)
        $legendaStep=$legendaStep*2;
      if($bandBreedte/$legendaStep > $nbDiv)
        $legendaStep=$legendaStep/2*5;
      $valIndRepere=round($valIndRepere/$unit/5)*5;


      $this->pdf->SetLineWidth($this->pdf->lineWidth);
      $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

      $nullijn=$XDiag - ($offset * $unit) +$margin;

      $i=0;
      $nbDiv=10;

      $this->pdf->SetFont($this->pdf->rapport_font, '', 5);
      if(round($legendaStep,5) <> 0.0)
      {
        for($x=$nullijn;$x>$XDiag; $x=$x-$legendaStep)
        {
          $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
          $this->pdf->setXY($x,$YDiag + $hDiag);
          $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,1),0,0,'C');
        }

        for($x=$nullijn;$x<($XDiag+$lDiag); $x=$x+$legendaStep)
        {
          $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
          $this->pdf->setXY($x,$YDiag + $hDiag);
          $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,1),0,0,'C');
        }
      }
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);
      $i=0;

      //$this->pdf->SetXY(0, $YDiag);
      //$this->pdf->Cell($nullijn, $hval-4, 'Onderwogen',0,0,'R');
      //$this->pdf->SetXY($nullijn, $YDiag);
      //$this->pdf->Cell(60, $hval-4, 'Overwogen',0,0,'L');
      $this->pdf->SetXY($XDiag, $YDiag);
      $this->pdf->Cell($lDiag, $hval-4, $titel,0,0,'C');
      foreach($data as $key=>$val)
      { 
          $this->pdf->SetFillColor($colorArray[$key][0],$colorArray[$key][1],$colorArray[$key][2]);
          //Bar
          $xval = $nullijn;
          $lval = ($val * $unit);
          $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
          $hval = $eBaton;
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
          //Legend
          $this->pdf->SetXY($XPage, $yval);
          $this->pdf->Cell($legendWidth , $hval, $this->pdf->legends[$i],0,0,'R');
          $i++;
      }

      //Scales
      $minPos=($minVal * $unit);
      $maxPos=($maxVal * $unit);

      $unit=($maxPos-$minPos)/$nbDiv;
     // echo "$minPos $maxPos -> $minVal $maxVal using $unit met null $nullijn";

      for ($i = $nullijn+$XDiag; $i <= $maxVal; $i=$i+$unit)
      {
          $xpos = $XDiag +  $i;
          $this->pdf->Line($xpos, $YDiag, $xpos, $YDiag + $hDiag);
          $val = $i * $valIndRepere;
          $xpos = $XDiag +  $i - $this->pdf->GetStringWidth($val) / 2;
          $ypos = $YDiag + $hDiag - $margin;
          $this->pdf->Text($xpos, $ypos, $val);
      }
  }
  
  function PieChart($w, $h, $data, $format, $colors=null)
  {

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $hLegend = 2;
      $radius = min($w - $margin * 4  , $h - $margin * 2); //
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
      $this->pdf->SetLineWidth($this->pdf->lineWidth);
      $angleStart = 0;
      $angleEnd = 0;
      $i = 0;
      foreach($data as $val) {
          $angle = floor(($val * 360) / doubleval($this->pdf->sum));
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

      //Legends
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

      $x1 = $XPage - $radius - 22 ;
      $x2 = $x1 + $hLegend + $margin - 12;
      $y1 = $YDiag - $radius + $hLegend*2;

      for($i=0; $i<$this->pdf->NbVal; $i++) {
          $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->pdf->Rect($x1-12, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1);
          $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
          $y1+=$hLegend + $hLegend;
      }

  }

  function VBarDiagram($w, $h, $data)
  {
      global $__appvar;
      $legendaWidth = 0;
      $grafiekPunt = array();
      $verwijder=array();
      
      $DB=new DB();
      $q="SELECT beleggingscategorie as Hoofdcategorie,Omschrijving as Omschrijving 
      FROM Beleggingscategorien ORDER BY Afdrukvolgorde desc";

 		$DB->SQL($q);
		$DB->Query();
		while($d=$DB->nextRecord())
		{
		  $this->categorieVolgorde[$d['Hoofdcategorie']]=$d['Hoofdcategorie'];
		  $this->categorieOmschrijving[$d['Hoofdcategorie']]=$d['Omschrijving'];
		}
      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = date("m-Y",db2jul($datum));
        $n=0;
        $minVal=0;
        $maxVal=100;
        foreach ($waarden as $categorie=>$waarde)
        {
          if($categorie=='LIQ')
            $categorie='Liquiditeiten';
          $grafiek[$datum][$categorie]=$waarde;
          $grafiekCategorie[$categorie][$datum]=$waarde;
          $categorien[$categorie] = $n;
          $categorieId[$n]=$categorie ;

          $maxVal=max(array($maxVal,$waarde));
          $minVal=min(array($minVal,$waarde));

          if($waarde < 0)
          {
             unset($grafiek[$datum][$categorie]);
             $grafiekNegatief[$datum][$categorie]=$waarde;
          }
          else
             $grafiekNegatief[$datum][$categorie]=0;


          if(!isset($colors[$categorie]))
          {
            if(isset($this->categorieKleuren))
              $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
            else
              $colors[$categorie]=array(rand(0,255),rand(0,255),rand(0,255));
          }
          $n++;
        }
      }



      $numBars = count($legenda);
      //$numBars=10;

      if($color == null)
      {
        $color=array(155,155,155);
      }

      if($maxVal <= 100)
        $maxVal=100;
      elseif($maxVal < 125)
        $maxVal=125;


      if($minVal >= 0)
        $minVal = 0;
      elseif($minVal > -25)
        $minVal=-25;



      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);
      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

      $n=0;
      $extraY=0;
      
    //  listarray($grafiekCategorie);
      $rowNr=ceil(count($grafiekCategorie)/2);
    //  echo $rowNr;exit;
      $items=0;
      foreach (array_reverse($this->categorieVolgorde) as $categorie)
      {
        if(is_array($grafiekCategorie[$categorie]))
        {
          $this->pdf->Rect($XstartGrafiek+3+$n*44 , $YstartGrafiek+7+$extraY, 2, 2, 'DF',null,$colors[$categorie]);
          $this->pdf->SetXY($XstartGrafiek+6+$n*44 ,$YstartGrafiek+6.5+$extraY );
          $this->pdf->Cell(20, 3,$this->categorieOmschrijving[$categorie],0,0,'L');
          $extraY+=4;
          $item++;
          if($item>3)
          {
            $n++;
            $extraY=0;
            $item=0;
          }  
          
        }
      }

      if($minVal < 0)
      {
        $unit = $hGrafiek / (-1 * $minVal + $maxVal) * -1;
        $nulYpos =  $unit * (-1 * $minVal);
      }
      else
      {
        $unit = $hGrafiek / $maxVal * -1;
        $nulYpos =0;
      }


      $horDiv = 5;
      $horInterval = $hGrafiek / $horDiv;
      $bereik = $hGrafiek/$unit;

      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      $this->pdf->SetTextColor(0,0,0);

      $stapgrootte = ceil(abs($bereik)/$horDiv);
      $top = $YstartGrafiek-$h;
      $bodem = $YstartGrafiek;
      $absUnit =abs($unit);

      $nulpunt = $YstartGrafiek + $nulYpos;
      $n=0;

      for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
      {
        $skipNull = true;
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1)." %",0,0,'R');
        $n++;
        if($n >20)
         break;
      }

      $n=0;
      for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        if($skipNull == true)
          $skipNull = false;
        else
        {
          $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
          $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte)." %",0,0,'R');
        }
        $n++;
        if($n >20)
          break;
      }



    if($numBars > 0)
      $this->pdf->NbVal=$numBars;

        $vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
        $bGrafiek = $vBar * ($this->pdf->NbVal + 1);
        $eBaton = ($vBar * 50 / 100);


      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth($this->pdf->lineWidth);

      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;

   foreach ($grafiek as $datum=>$data)
   {
     foreach($this->categorieVolgorde as $categorie)
     {
     $val=$data[$categorie];
        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
          $hval = ($val * $unit);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);

         if($legendaPrinted[$datum] != 1)
           $this->pdf->TextWithRotation($xval-1.5,$YstartGrafiek+4.5,$legenda[$datum],20);

         if($grafiekPunt[$categorie][$datum])
         {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
         $legendaPrinted[$datum] = 1;
      }
      $i++;
   }

   $i=0;
   $YstartGrafiekLast=array();
   foreach ($grafiekNegatief as $datum=>$data)
   {

     foreach($this->categorieVolgorde as $categorie)
     {
     $val=$data[$categorie];
          if(!isset($YstartGrafiekLast[$datum]))
            $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
          $hval = ($val * $unit);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);

         if($grafiekPunt[$categorie][$datum])
         {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
      }
      $i++;
   }
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }


}
?>
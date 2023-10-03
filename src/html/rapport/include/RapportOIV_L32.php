<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/04 14:09:21 $
File Versie					: $Revision: 1.1 $

$Log: RapportVOLK_L93.php,v $


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportKERNV_L32.php");

class RapportOIV_L32
{
  function RapportOIV_L32($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->kernv=new RapportKERNV_L32($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "OIV";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->pdf->rapport_titel = "Valutaverdeling";
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
  }
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde, $dec, ",", ".");
  }
  
  
  function writeRapport()
  {
    global $__appvar;
    
    $query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '" . $this->portefeuille . "' AND Portefeuilles.Client = Clienten.Client ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $this->portefeuilledata = $DB->nextRecord();
    
    
    $this->pdf->widthB = array(5, 55, 18, 18, 20, 21, 1, 17, 21, 21, 18, 18, 18, 18, 12);
    $this->pdf->widthA = array(60, 18, 18, 20, 21, 1, 17, 21, 21, 18, 18, 18, 18, 12);
    $this->pdf->alignB = array('R', 'L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R');
    $this->pdf->alignA = array('L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R');
    
    
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas'] = $this->pdf->page;
  
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $kleuren = unserialize($kleuren['grafiek_kleur']);
  
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
  
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaalEur,
    SUM(actuelePortefeuilleWaardeInValuta) / ".$this->pdf->ValutaKoersEind. " AS totaalValuta,
    TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.actueleValuta as valutaKoers,
TijdelijkeRapportage.beleggingscategorieVolgorde,
TijdelijkeRapportage.hoofdcategorieOmschrijving as HOmschrijving,
TijdelijkeRapportage.valutaVolgorde,
TijdelijkeRapportage.beleggingscategorieOmschrijving,
TijdelijkeRapportage.valutaOmschrijving
      FROM TijdelijkeRapportage WHERE
       rapportageDatum ='".$this->rapportageDatum."' AND
       portefeuille = '".$this->portefeuille."'"
      .$__appvar['TijdelijkeRapportageMaakUniek'].
		"GROUP BY TijdelijkeRapportage.valuta,TijdelijkeRapportage.beleggingscategorie
ORDER BY TijdelijkeRapportage.valutaVolgorde, TijdelijkeRapportage.beleggingscategorieVolgorde";
    debugSpecial($query,__FILE__,__LINE__);
    
    $DB->SQL($query);
    $DB->Query();
    $valutaVolgorde=array();
    $categorieVolgorde=array();
    $gegevens=array();
    $valutaKoers=array();
    $valutaOmschrijvingen=array();
    $categorieOmschrijvingen=array();
    $grafiekdata=array();
    $valutas=array('EUR','USD','CHF','GBP');
    while($data=$DB->nextRecord())
		{
		  if(!in_array($data['valuta'],$valutas))
      {
        $data['valuta'] = 'overige';
        $data['valutaOmschrijving'] = 'Overige';
      }
		  $gegevens[$data['valuta']][$data['beleggingscategorie']]=$data;
      $hoofdcategorieOmschrijving[$data['beleggingscategorie']]=vertaalTekst($data['HOmschrijving'],$this->pdf->rapport_taal);
   //   $gegevens[$data['valuta']]['TotaalValuta']['totaalValuta']+=$data['totaalValuta'];
      $gegevens[$data['valuta']]['Totaal']['totaalEur']+=$data['totaalEur'];
      $gegevens['Totaal'][$data['beleggingscategorie']]['totaalEur']+=$data['totaalEur'];
      $gegevens['Totaal']['Totaal']['totaalEur']+=$data['totaalEur'];
      
      $valutaVolgorde[$data['valuta']]=$data['valutaVolgorde'];
      $valutaOmschrijvingen[$data['valuta']]=$data['valutaOmschrijving'];
      $valutaKoers[$data['valuta']]=$data['valutaKoers'];
      $categorieVolgorde[$data['beleggingscategorie']]=$data['beleggingscategorieVolgorde'];
      $categorieOmschrijvingen[$data['beleggingscategorie']]=$data['beleggingscategorieOmschrijving'];
      
      

      $grafiekdata['valuta']['percentage'][$data['valutaOmschrijving']]+=$data['totaalEur']/$totaalWaarde*100;
      $grafiekdata['valuta']['kleuren'][$data['valutaOmschrijving']]=array($kleuren['OIV'][$data['valuta']]['R']['value'],$kleuren['OIV'][$data['valuta']]['G']['value'],$kleuren['OIV'][$data['valuta']]['B']['value']);
      
      
      $grafiekdata['beleggingscategorie']['percentage'][$data['beleggingscategorieOmschrijving']]+=$data['totaalEur']/$totaalWaarde*100;
      $grafiekdata['beleggingscategorie']['kleuren'][$data['beleggingscategorieOmschrijving']]=array($kleuren['OIB'][$data['beleggingscategorie']]['R']['value'],$kleuren['OIB'][$data['beleggingscategorie']]['G']['value'],$kleuren['OIB'][$data['beleggingscategorie']]['B']['value']);
		}
		//listarray($grafiekdata);
		asort($valutaVolgorde);
    asort($categorieVolgorde);
    $valutaVolgorde['Totaal']=500;
    //$categorieVolgorde['TotaalValuta']=500;
    $categorieVolgorde['Totaal']=501;
    $valutaKoers['Totaal']=1;
    $valutaOmschrijvingen['Totaal']='Totaal in Euro';
  //  $categorieOmschrijvingen['TotaalValuta']='Totaal in Valuta';
    $categorieOmschrijvingen['Totaal']='Totaal in Euro';
		
    $header=array('Categorie');//,'Valutakoers');
    $header2=array('');
    $catWitdth=$this->pdf->w-$this->pdf->marge*2-50;
    $w=$catWitdth/count($valutaVolgorde);
    $headerWidths=array(50);//,20);
    $headerDataWidths=array(50);//,20);
    $headerAligns=array('L');//,'R');
    $dataAligns=array('L');
    $underline=array('U');//,'U');
    $topUnderline=array('T');//,'T');
    $fill=array(1);
    $printed=array();
    foreach ($valutaVolgorde as $valuta=>$volgorde)
		{
			$headerWidths[]=$w;
      $headerDataWidths[]=$w*7/12;
      $headerDataWidths[]=$w*5/12;
      $headerAligns[]='C';
      $dataAligns[]='R';
      $dataAligns[]='R';
		  $header[]=$valutaOmschrijvingen[$valuta];
      $header2[]='€';
      $header2[]='%';
      $underline[]='U';
      $topUnderline[]='T';
      $topUnderline[]='T';
      $fill[]=1;
      $fill[]=1;
		}
		
		$this->pdf->setWidths($headerWidths);
    $this->pdf->setAligns($headerAligns);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
   // $this->pdf->CellBorders=$underline;
    
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), array_sum($this->pdf->widthB), 8 , 'F');
    $this->pdf->SetTextColor(255,255,255);
    $this->pdf->row($header);
    unset($this->pdf->CellBorders);
    
    $this->pdf->setWidths($headerDataWidths);
    $this->pdf->setAligns($dataAligns);
    $this->pdf->row($header2);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $n=0;
    $this->pdf->SetFillColor(230,230,230);
    $x=$this->pdf->marge;
    foreach($headerWidths as $w)
    {
      $x+=$w;
      if($w>0)
        $this->pdf->line($x,$this->pdf->getY()-8,$x,$this->pdf->getY(),array('width' => 0.5,'color'=>array(255,255,255)));
    }
    foreach($categorieVolgorde as $categorie=>$Vvolgorde)
		{
      if($n%2==0)
        $this->pdf->fillCell = $fill;
      else
        unset($this->pdf->fillCell);
      
			$row=array($categorieOmschrijvingen[$categorie]);//,$this->formatGetal($valutaKoers[$valuta],4));
      $rowProcent=array('');//,'');
			foreach($valutaVolgorde as $valuta=>$cVolgorde)
			{
        if($categorie=='TotaalValuta'&&$valuta=='Totaal')
          $row[]='';
				else
          $row[]=$this->formatGetal($gegevens[$valuta][$categorie]['totaalEur'],0);
        if($categorie=='TotaalValuta')
          $row[]='';
        else
          $row[]=$this->formatGetal($gegevens[$valuta][$categorie]['totaalEur']/$gegevens['Totaal']['Totaal']['totaalEur']*100,2).'%';
			}
			if($categorie=='Totaal')
      {
        $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
        $this->pdf->CellBorders=$topUnderline;
        unset($this->pdf->fillCell);
      }
      if(!isset($printed[$hoofdcategorieOmschrijving[$categorie]]))
      {
        $fillBackup=$this->pdf->fillCell;
        unset($this->pdf->fillCell);
        if(count($printed)<>0)
          $this->pdf->ln();
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
        $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
        $this->pdf->Row(array($hoofdcategorieOmschrijving[$categorie]));
        $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
        //$this->pdf->ln();
        $printed[$hoofdcategorieOmschrijving[$categorie]]=1;
        $this->pdf->fillCell=$fillBackup;
      }
      //if($categorie!='Totaal')
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
			$this->pdf->Row($row);
      $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
   //   if($valuta=='Totaal')
     //   $this->pdf->CellBorders=$underline;
     // $this->pdf->Row($rowProcent);
      $n++;
		}
		unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    //exit;
  
    if($this->pdf->getY()>105)
    {
      $this->pdf->addPage();
      $ystart=30;
    }
    else
    {
      $ystart=100;
    }
    //listarray($grafiekdata);
    $xCorrectie=-30;
  
    if(min($grafiekdata['valuta']['percentage'])<0)
    {
      $this->pdf->setXY(90 + $xCorrectie, $ystart + 8);
      $this->pdf->MultiCell(50, 10, 'Valutaverdeling', 0, "C");
      $this->pdf->setXY(60 + $xCorrectie,$ystart+15);
      $this->kernv->BarDiagram(80, 100, $grafiekdata['valuta']['percentage'], '%l (%p)', $grafiekdata['valuta']['kleuren']);//"Portefeuillewaarde € ".$this->formatGetal($this->portTotaal[$this->rapportageDatum],2)
    }
    else
    {
      $this->pdf->setXY(50 + $xCorrectie, $ystart + 8);
      $this->pdf->MultiCell(50, 10, 'Valutaverdeling', 0, "C");
      $this->pdf->setXY(50 + $xCorrectie, $ystart + 15);
      $this->PieChart(65, 65, $grafiekdata['valuta']['percentage'], '%l (%p)', array_values($grafiekdata['valuta']['kleuren']));
    }
  
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    if(min($grafiekdata['beleggingscategorie']['percentage'])<0)
    {
      $this->pdf->setXY(220 + $xCorrectie, $ystart + 8);
      $this->pdf->MultiCell(50, 10, 'Categorieverdeling', 0, "C");
      $this->pdf->setXY(190,$ystart+15);
      $this->kernv->BarDiagram(80, 100, $grafiekdata['beleggingscategorie']['percentage'], '%l (%p)', $grafiekdata['beleggingscategorie']['kleuren']);//"Portefeuillewaarde € ".$this->formatGetal($this->portTotaal[$this->rapportageDatum],2)
    }
    else
    {
      $this->pdf->setXY(180 + $xCorrectie, $ystart + 8);
      $this->pdf->MultiCell(50, 10, 'Categorieverdeling', 0, "C");
      $this->pdf->setXY(180 + $xCorrectie, $ystart + 15);
      $this->PieChart(65, 65, $grafiekdata['beleggingscategorie']['percentage'], '%l (%p)', array_values($grafiekdata['beleggingscategorie']['kleuren']));
    }
  
  
    
  }
  
  
  
  
  function PieChart( $w, $h, $data, $format, $colors = null)
  {
  
  
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetLegends($data, $format);
  
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $hLegend = 2;
    $radius = min($w - $margin * 4 - $hLegend, $h - $margin * 2); //
    $radius = floor($radius / 2);
    $XDiag = $XPage + $margin + $radius;
    $YDiag = $YPage + $margin + $radius;
    if ($colors == null)
    {
      for ($i = 0; $i < $this->pdf->NbVal; $i++)
      {
        $gray = $i * intval(255 / $this->pdf->NbVal);
        $colors[$i] = array($gray, $gray, $gray);
      }
    }
  
    //Sectors
    $this->pdf->SetLineWidth(0.2);
    $angleStart = 0;
    $angleEnd = 0;
    $i = 0;
    $this->pdf->setDrawColor(255, 255, 255);
    foreach ($data as $val)
    {
      $angle = floor(($val * 360) / doubleval($this->pdf->sum));
      if ($angle != 0)
      {
        $angleEnd = $angleStart + $angle;
        $this->pdf->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
        $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
        $angleStart += $angle;
      }
      $i++;
    }
    if ($angleEnd != 360)
    {
      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    }
    $this->pdf->setDrawColor(0, 0, 0);
    //Legends
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  
    $x1 = $XPage + $w + $radius * .5;
    $x2 = $x1 + $hLegend + $margin - 12;
    $y1 = $YDiag - ($radius) + $margin;
  
    for ($i = 0; $i < $this->pdf->NbVal; $i++)
    {
      $this->pdf->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
      $this->pdf->Rect($x1 - 12, $y1, $hLegend, $hLegend, 'DF');
      $this->pdf->SetXY($x2, $y1);
      if (strpos($this->pdf->legends[$i], '||') > 0)
      {
        $parts = explode("||", $this->pdf->legends[$i]);
        $this->pdf->Cell(0, $hLegend, $parts[1]);
      }
      else
      {
        $this->pdf->Cell(0, $hLegend, $this->pdf->legends[$i]);
      }
      $y1 += $hLegend + $margin;
    }
  
  
  }
    
  }
?>
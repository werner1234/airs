<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/18 17:44:11 $
File Versie					: $Revision: 1.6 $

$Log: RapportOIV_L80.php,v $
Revision 1.6  2020/03/18 17:44:11  rvv
*** empty log message ***

Revision 1.5  2020/03/15 10:25:30  rvv
*** empty log message ***

Revision 1.4  2020/03/14 18:42:03  rvv
*** empty log message ***

Revision 1.3  2019/01/30 16:47:26  rvv
*** empty log message ***

Revision 1.2  2019/01/12 17:08:31  rvv
*** empty log message ***

Revision 1.1  2018/12/08 18:28:30  rvv
*** empty log message ***

Revision 1.35  2011/06/25 16:51:45  rvv
*** empty log message ***

Revision 1.34  2010/06/30 16:10:10  rvv
*** empty log message ***

Revision 1.33  2010/06/02 09:13:01  rvv
*** empty log message ***

Revision 1.32  2009/11/20 09:37:51  rvv
*** empty log message ***

Revision 1.31  2009/01/20 17:44:09  rvv
*** empty log message ***

Revision 1.30  2008/06/30 07:58:44  rvv
*** empty log message ***

Revision 1.29  2007/10/04 11:57:04  rvv
*** empty log message ***

Revision 1.28  2007/06/29 12:16:31  rvv
*** empty log message ***

Revision 1.27  2007/06/29 11:38:56  rvv
L14 aanpassingen

Revision 1.26  2007/03/27 14:58:20  rvv
VreemdeValutaRapportage

Revision 1.25  2007/02/21 11:04:26  rvv
Client toevoeging

Revision 1.24  2007/01/31 16:20:27  rvv
*** empty log message ***

Revision 1.23  2006/11/27 13:33:02  rvv
Sortering werkt nu ook met eigen kleuren.

Revision 1.22  2006/11/27 09:27:15  rvv
grafiekkleuren uit vermogensbeheerder check

Revision 1.21  2006/11/10 11:56:12  rvv
Eigen kleuren aanpassing/toevoeging

Revision 1.20  2006/11/03 11:24:04  rvv
Na user update

Revision 1.19  2006/10/31 12:06:45  rvv
Voor user update

Revision 1.18  2006/10/20 14:55:53  rvv
*** empty log message ***

Revision 1.17  2006/05/09 07:48:11  jwellner
- afronding fondsaantal
- afronding controle bij afdrukken rapporten
- sorteren frontoffice selectie

Revision 1.16  2006/04/12 07:54:47  jwellner
*** empty log message ***

Revision 1.15  2005/12/19 13:23:27  jwellner
no message

Revision 1.14  2005/11/30 08:37:39  jwellner
layout stuff

Revision 1.13  2005/11/25 09:30:08  jwellner
- verdiept overzicht
- layout

Revision 1.12  2005/11/18 15:15:01  jwellner
no message

Revision 1.11  2005/11/17 07:25:02  jwellner
no message

Revision 1.10  2005/10/07 07:15:15  jwellner
rapportage

Revision 1.9  2005/09/30 09:45:45  jwellner
rapporten aangepast.

Revision 1.8  2005/09/29 15:00:18  jwellner
no message

Revision 1.7  2005/09/16 07:32:55  jwellner
aanpassingen rapportage.

Revision 1.6  2005/09/13 14:49:18  jwellner
rapportage toevoegingen

Revision 1.5  2005/09/12 12:04:16  jwellner
bugs en features

Revision 1.4  2005/09/09 11:31:46  jwellner
diverse aanpassingen zie e-mails Theo

Revision 1.3  2005/08/05 12:08:04  jwellner
no message

Revision 1.2  2005/08/01 13:05:25  jwellner
diverse kleine bugfixes :
- beheerfee nooit < 0

Revision 1.1  2005/07/15 11:21:00  jwellner
Layout verwijderd, alles samengevoegd in PDFRapport

Revision 1.3  2005/07/12 07:09:50  jwellner
no message

Revision 1.2  2005/07/08 13:52:01  jwellner
no message

Revision 1.1  2005/06/30 08:22:56  jwellner
Rapportage toegevoegd

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportOIB_L80.php");


class RapportOIV_L80
{
	function RapportOIV_L80($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
    
    $this->verdeling1='valuta';
    $this->verdeling2='beleggingscategorie';
    $this->pdf->underlinePercentage=0.8;
    
    if(count($pdf->portefeuilles)>1)
    {
      $this->consolidatie=true;
      $this->oib=new RapportOIB_L80($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    }
    else
    {
      $this->consolidatie=false;

    }
    
		$this->pdf->rapport_type = "OIV";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Onderverdeling in valuta";
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
    
    
    if($this->consolidatie==true)
    {
  
      $this->oib->verdeling1=$this->verdeling1;
      $this->oib->verdeling2=$this->verdeling1;
      
      $this->pdf->AddPage();
      $hoofdP = $this->oib->verdelingPerPortefeuille($this->portefeuille, $this->rapportageDatum);
  
      //
      //listarray($hoofdP);
  
      $this->pdf->setXY(30,37);
      $legendLocation=array(90,7+30-(count($hoofdP['verdeling'])*4)/2);
   //   echo count($hoofdP['verdeling']);exit;
      $this->oib->printPie($hoofdP['verdeling'],$hoofdP['kleuren'],$this->portefeuille.' '.date("d-m-Y",db2jul($this->rapportageDatum)),60,50,$legendLocation);
      $this->pdf->wLegend=0;
  
      $maxRows=count($hoofdP['verdeling']);
      $extraY=65;
      foreach($this->pdf->portefeuilles as $portefeuille)
      {
        $verdelingsData[$portefeuille] = $this->oib->verdelingPerPortefeuille($portefeuille, $this->rapportageDatum);
  
        $maxRows=max($maxRows,count($verdelingsData[$portefeuille]['verdeling']));
      }
      if($maxRows>6)
      {
        $this->pdf->addPage();
        $extraY=0;
      }
  
  
      $x=30;
      $n=0;
      foreach($verdelingsData as $portefeuille=>$data)
      {
       
        $this->pdf->setXY($x, 37+$extraY);
       // $data = $this->oib->verdelingPerPortefeuille($portefeuille, $this->rapportageDatum);
        $this->oib->printPie($data['verdeling'], $data['kleuren'], $portefeuille . ' ' . date("d-m-Y", db2jul($this->rapportageDatum)), 60, 50);
        $n++;
        $x += 90;
        if($n>2)
          break;
      }
      
     // exit;
    }
    else
    {
  
  
      $query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '" . $this->pdf->portefeuille . "' AND Portefeuilles.Client = Clienten.Client ";
      $DB = new DB();
      $DB->SQL($query);
      $DB->Query();
      $portefeuilledata = $DB->nextRecord();
  
      // voor data
      $this->pdf->widthB = array(40, 35, 25, 25, 25, 15, 115);
      $this->pdf->alignB = array('L', 'L', 'R', 'R', 'R', 'R', 'R');
  
      // voor kopjes
      $this->pdf->widthA = array(40, 35, 25, 25, 25, 15, 115);
      $this->pdf->alignA = array('L', 'L', 'R', 'R', 'R', 'R', 'R');
  
      $this->pdf->AddPage();
  
  
      // haal totaalwaarde op om % te berekenen
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal " .
        "FROM TijdelijkeRapportage WHERE " .
        " rapportageDatum ='" . $this->rapportageDatum . "' AND " .
        " portefeuille = '" . $this->portefeuille . "' "
        . $__appvar['TijdelijkeRapportageMaakUniek'];
      debugSpecial($query, __FILE__, __LINE__);
      $DB->SQL($query);
      $DB->Query();
      $totaalWaarde = $DB->nextRecord();
      $totaalWaarde = $totaalWaarde['totaal'];
  
      $query = "SELECT TijdelijkeRapportage.type ,
       " . $this->verdeling1 . " as verdeling1,
       " . $this->verdeling1 . "Omschrijving as verdeling1Omschrijving,
       " . $this->verdeling2 . " as verdeling2,
       " . $this->verdeling2 . "Omschrijving as verdeling2Omschrijving,
	SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta,
	SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel,
  if(TijdelijkeRapportage.type='fondsen',1,(if(TijdelijkeRapportage.type='rente',2,3))) as volgorde
			 FROM TijdelijkeRapportage
			 WHERE TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND
			 TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "' " . $__appvar['TijdelijkeRapportageMaakUniek'] . "
GROUP BY
TijdelijkeRapportage.type,
	" . $this->verdeling1 . ",
	" . $this->verdeling2 . "
ORDER BY
  volgorde,
	TijdelijkeRapportage." . $this->verdeling1 . "Volgorde ASC,
	TijdelijkeRapportage." . $this->verdeling2 . "Volgorde ASC";
      debugSpecial($query, __FILE__, __LINE__);
  
  
      $DB = new DB();
      $DB->SQL($query);
      $DB->Query();
      $gegevens = array();
      $dbBeleggingscategorien = array();
      $grafiekCategorien = array();
      $primaireVerdeling = array();
      while ($data = $DB->NextRecord())
      {
        if($this->verdeling1 == 'beleggingssector')
        {
   
          if($data['type']=='rekening')
          {
            $data['verdeling1']='A-Diversen';
            $data['verdeling1Omschrijving']='Overigen';
            
          }
        }
        $data['percentageVanTotaal'] = $data['subtotaalactueel'] / $totaalWaarde * 100;
        $gegevens[$data['volgorde']][$data['verdeling1']][$data['verdeling2']] = $data;
        $dbBeleggingscategorien[$data['verdeling1']] = $data['verdeling1Omschrijving'];
        $primaireVerdeling[$data['verdeling1']] += $data['percentageVanTotaal'];
      }
  
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $volgordeLookup = array('1' => 'Fondsen', '2' => 'Opgelopen rente', '3' => 'Liquiditeiten');
      $totalen = array();
  
      foreach ($gegevens as $volgorde => $verdeling1Data)
      {
        foreach ($verdeling1Data as $verdeling1 => $verdeling2Data)
        {
          foreach ($verdeling2Data as $verdeling2 => $data)
          {
            $percentageVanTotaal = $data['percentageVanTotaal'];
            $this->pdf->pieData[vertaalTekst($data['verdeling1Omschrijving'], $this->pdf->rapport_taal)] += $percentageVanTotaal;
            $grafiekCategorien[$data['verdeling1']] += $percentageVanTotaal;
          }
        }
      }
  
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
      $q = "SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '" . $beheerder . "'";
      $DB = new DB();
      $DB->SQL($q);
      $DB->Query();
      $kleuren = $DB->LookupRecord();
      $kleuren = unserialize($kleuren['grafiek_kleur']);
      $kleuren = $kleuren[$this->pdf->rapport_type];
      $kleurdata = array();
  
      $dbBeleggingscategorien['Opgelopen Rente'] = 'Opgelopen Rente'; //Voorkomen dat Opgelopen rente leeg blijft wanneer vermogensbeheerder kleuren niet geset.
      foreach ($grafiekCategorien as $cat => $percentage)
      {
        $groep = $dbBeleggingscategorien[$cat];
        $groep = vertaalTekst($groep, $this->pdf->rapport_taal);
        $kleurdata[$groep]['kleur'] = $kleuren[$cat];
        $kleurdata[$groep]['percentage'] = $percentage;
      }
    
      $x=$this->pdf->getX();
      $y=$this->pdf->getY();
      $this->pdf->setXY(205,40);
      $this->printPie($this->pdf->pieData, $kleurdata,$this->pdf->rapport_titel,50,50);
      $this->pdf->setXY($x,$y);
      
      
      foreach ($gegevens as $volgorde => $verdeling1Data)
      {
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
        $this->pdf->row(array($volgordeLookup[$volgorde]));
        $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
        foreach ($verdeling1Data as $verdeling1 => $verdeling2Data)
        {
          $subTotalen = array();
          $headerPrinted = false;
          foreach ($verdeling2Data as $verdeling2 => $data)
          {
            $percentageVanTotaal = $data['percentageVanTotaal'];
        
            if ($headerPrinted == false)
            {
              $this->pdf->Cell($this->pdf->widthB[0], 4, vertaalTekst($data['verdeling1Omschrijving'], $this->pdf->rapport_taal));
              $this->pdf->setX($this->pdf->marge);
              $headerPrinted = true;
            }
            $this->pdf->row(array('',
                              $data['verdeling2Omschrijving'],
                              $this->formatGetal($data['subtotaalactueelvaluta'], $this->pdf->rapport_OIB_decimaal),
                              $this->formatGetalKoers($data['subtotaalactueel'], $this->pdf->rapport_OIB_decimaal),
                              "",
                              $this->formatGetal($percentageVanTotaal, 1) . ""));
        
            $subTotalen['subtotaalactueel'] += $data['subtotaalactueel'];
            $totalen['subtotaalactueel'] += $data['subtotaalactueel'];
        
        
            //$this->pdf->pieData[vertaalTekst($data['verdeling1Omschrijving'], $this->pdf->rapport_taal)] += $percentageVanTotaal;
            //$grafiekCategorien[$data['verdeling1']] += $percentageVanTotaal;
        
          }
      
          $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
          $this->pdf->row(array('', '', '', '', $this->formatGetal($subTotalen['subtotaalactueel'], 0), $this->formatGetal($subTotalen['subtotaalactueel'] / $totaalWaarde * 100, 1)));
          $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
          $this->pdf->ln(2);
      
        }
      }
      $this->pdf->ln(2);
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $this->pdf->CellBorders = array('','','','','TS','TS');
      $this->pdf->row(array('Totaal', '', '', '', $this->formatGetal($totalen['subtotaalactueel'], 0), $this->formatGetal($totalen['subtotaalactueel'] / $totaalWaarde * 100, 1)));
      unset($this->pdf->CellBorders);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  
  

    }

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
      //while (list($key, $value) = each($kleurdata))
      foreach($kleurdata as $key=>$value)
      {
        if ($value['kleur']['R']['value'] == 0 && $value['kleur']['G']['value'] == 0 && $value['kleur']['B']['value'] == 0)
          $grafiekKleuren[]=$standaardKleuren[$a];
        else
          $grafiekKleuren[] = array($value['kleur']['R']['value'],$value['kleur']['G']['value'],$value['kleur']['B']['value']);
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
    //	$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    
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
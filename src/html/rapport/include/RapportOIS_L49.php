<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/05/13 16:27:34 $
File Versie					: $Revision: 1.6 $

$Log: RapportOIS_L49.php,v $
Revision 1.6  2017/05/13 16:27:34  rvv
*** empty log message ***

Revision 1.5  2016/04/23 15:33:07  rvv
*** empty log message ***

Revision 1.4  2014/03/22 15:47:14  rvv
*** empty log message ***

Revision 1.3  2013/12/18 17:10:42  rvv
*** empty log message ***

Revision 1.2  2013/12/14 17:22:13  rvv
*** empty log message ***

Revision 1.1  2013/12/14 17:16:30  rvv
*** empty log message ***

Revision 1.1  2013/06/05 15:56:07  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIS_L49
{

	function RapportOIS_L49($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		$this->pdf->rapport_titel = "";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

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
    $this->pdf->AddPage();
    //$this->maakNotities();
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    getTypeGrafiekData($this,'regio');
    getTypeGrafiekData($this,'beleggingssector');
    $this->pdf->setXY(20,$this->pdf->rapportYstart);
    $this->Categorieverdeling($this->pdf->grafiekData['regio'],$this->pdf->veldOmschrijvingen['regio'],'Regio');
    $this->pdf->setXY(160,$this->pdf->rapportYstart);
    $this->Categorieverdeling($this->pdf->grafiekData['beleggingssector'],$this->pdf->veldOmschrijvingen['beleggingssector'],'Sector');

  }
  
  function maakNotities()
  {
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize+2);
    $this->pdf->setY($this->pdf->rapportYstart);
  	$this->pdf->SetX($this->pdf->marge);
		$this->pdf->Cell(150,4,'Notites', 0, "L");
    $this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array($this->pdf->achtergrondlijn[0],$this->pdf->achtergrondlijn[1],$this->pdf->achtergrondlijn[2]),'dash'=>0));
    $stappen=26;
    $yStart=$this->pdf->rapportYstart+4;
    $yStop=190;
    $w=140;
    $stap=($yStop-$yStart)/$stappen;
    for($i=0;$i<=$stappen;$i++)
    {
      $this->pdf->Line($this->pdf->marge,$yStart+$i*$stap,$w,$yStart+$i*$stap);
    }
  }
  
  function switchColor($n)
  {
     $col1=$this->pdf->achtergrondLicht;
     $col2=$this->pdf->achtergrondDonker;

    if($n%2==0)
      $this->pdf->SetFillColor($col1[0],$col1[1],$col1[2]);
    else
      $this->pdf->SetFillColor($col2[0],$col2[1],$col2[2]);
  }
  
  
  
  function Categorieverdeling($data,$omschrijvingen,$titel)
	{
		global $__appvar;
    $startX=$this->pdf->GetX();
    $startY=$this->pdf->GetY();
    $this->pdf->setXY($startX,$this->pdf->rapportYstart);
    //$this->pdf->debug=true;

    PieChart($this->pdf,120, 70, $data['grafiek'], '%l', $data['grafiekKleur'],$titel.'verdeling portefeuille '.getKwartaal($this->pdf->rapport_datum).' kwartaal '.date('Y',$this->pdf->rapport_datum),'R');
    $totalen=array();
    $witCell=$this->pdf->witCell;
    $this->pdf->setWidths(array($startX-20,100-$witCell,$witCell,20));
    $this->pdf->SetAligns(array('L','L','C','R'));
    $this->pdf->Ln(8);

	  $this->pdf->fillCell = array(0,1,0,1);
    $n=0;
	  foreach($data['port']['procent'] as $categorie=>$percentage)
    {
      $this->switchColor($n);
      $n++;
      $this->pdf->row(array('',$omschrijvingen[$categorie],'',
                            $this->formatGetal($percentage*100,0).'%'));
      $totalen['percentage']+=$percentage;

    }
    $this->switchColor($n);
    $this->pdf->row(array('','Totaal','',$this->formatGetal($totalen['percentage']*100,0).'%'));
    unset($this->pdf->fillCell);
    checkPage($this->pdf);
    $eindY=$this->pdf->GetY();
  }
  
  
}



?>
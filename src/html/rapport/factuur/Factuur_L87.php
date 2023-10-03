<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/04/11 16:34:15 $
File Versie					: $Revision: 1.11 $

$Log: Factuur_L87.php,v $
Revision 1.11  2020/04/11 16:34:15  rvv
*** empty log message ***

Revision 1.10  2020/04/08 15:41:35  rvv
*** empty log message ***

Revision 1.9  2020/04/05 11:53:27  rvv
*** empty log message ***

Revision 1.8  2020/01/22 07:18:15  rvv
*** empty log message ***

Revision 1.7  2020/01/22 07:16:55  rvv
*** empty log message ***

Revision 1.6  2020/01/20 13:24:27  cvs
call 8319

Revision 1.5  2020/01/18 13:29:35  rvv
*** empty log message ***

Revision 1.4  2020/01/15 17:03:21  rvv
*** empty log message ***

Revision 1.3  2020/01/08 14:35:03  rvv
*** empty log message ***

Revision 1.2  2019/12/28 11:28:56  rvv
*** empty log message ***

Revision 1.1  2019/12/21 13:45:28  rvv
*** empty log message ***

Revision 1.4  2012/05/23 15:57:43  rvv
*** empty log message ***

Revision 1.3  2012/05/19 10:49:55  rvv
*** empty log message ***

Revision 1.2  2010/07/21 17:49:59  rvv
*** empty log message ***

Revision 1.1  2010/07/21 17:37:57  rvv
*** empty log message ***


*/

global $__appvar;

    $rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight = 5;
   	$this->pdf->underlinePercentage=0.8;
    $this->pdf->brief_font='Times';
		$this->pdf->rapport_type = "FACTUUR";
		$this->pdf->AddPage('P');
		$this->pdf->SetFont($this->pdf->rapport_font,'',10);

		$vanaf=db2jul($this->waarden['datumVan']);
		$tot=db2jul($this->waarden['datumTot']);

    if(is_file($this->pdf->rapport_logo))
		{
		//	$this->pdf->Image($this->pdf->rapport_logo, 0, 10, 108, 15);
		}


if(is_file($this->pdf->rapport_logo))
{
  $xSize=50;
  $logopos=($this->pdf->w - $xSize - 20);
  $this->pdf->Image($this->pdf->rapport_logo, $logopos, 20, $xSize);
}

		$kwartalen = array('null','eerste','tweede','derde','vierde');

    //listarray($this->waarden);
    if($this->waarden['SoortOvereenkomst']=='Advies')
      $soort='adviesfee';
    else
      $soort='beheerfee';
    
		$this->pdf->SetY(50);
		$this->pdf->SetWidths(array(22,150));
	  $this->pdf->SetAligns(array('R','L'));

		$this->pdf->row(array('',$this->waarden['clientNaam']));
		if($this->waarden['clientNaam1'] <> '')
		  $this->pdf->row(array('',$this->waarden['clientNaam1']));
		$this->pdf->row(array('',$this->waarden['clientAdres']));
		if($this->waarden['clientPostcode'] != '')
	  	$plaats = $this->waarden['clientPostcode'] . " " .$this->waarden['clientWoonplaats'];
	  else
	  	$plaats = $this->waarden['clientWoonplaats'];
		$this->pdf->row(array('',$plaats));
		$this->pdf->row(array('',$this->waarden['clientLand']));

		$this->pdf->SetY(90);
		$this->pdf->SetAligns(array('R','L'));

		$factuurNr=sprintf("%03d",$this->waarden['factuurNummer']);
		$this->pdf->row(array('',"Berekening $soort ".$kwartalen[$this->waarden['kwartaal']]." kwartaal ".date("Y",$tot)));
    $this->pdf->row(array('',"Depotnummer ".$this->waarden['portefeuille']));

//		$this->pdf->row(array('',"Factuurnummer: ".date("Y",$tot).".$factuurNr"));
		$this->pdf->row(array('',"Factuurnummer: ".date("Y").".$factuurNr"));
		$this->pdf->SetAligns(array('R','L'));


		$this->pdf->ln();


		$vanafTxt=date("d",$vanaf)." ".vertaalTekst($__appvar["Maanden"][date("n",$vanaf)],$pdf->rapport_taal)." ".date("Y",$vanaf);
		$totTxt=date("d",$tot)." ".vertaalTekst($__appvar["Maanden"][date("n",$tot)],$pdf->rapport_taal)." ".date("Y",$tot);
		$rapportagePeriode = $vanafTxt.' t/m '.$totTxt;

    $berekeningVanaf=$vanaf;
		$pstartJul=db2jul($this->waarden['portefeuilledata']['Startdatum']);
		if($pstartJul>$vanaf)
		  $berekeningVanaf=$pstartJul;
		
		
//listarray($this->waarden);


	$this->pdf->SetAligns(array('R','L'));
	$this->pdf->row(array('','Belegd vermogen exclusief liquiditeiten.'));
	$this->pdf->ln();
	$this->pdf->SetWidths(array(22,70,25,25,25));
	$this->pdf->SetAligns(array('R','L','R','R','R'));
  $this->pdf->row(array('','','','Aantal dagen'));



  
$staffelTotaal=$this->waarden['beheerfeePerPeriodeNor'];
$ultimoVanaf=$berekeningVanaf;
  $ultimoTot=$this->waarden['maandsData_1'];
  if($berekeningVanaf>$ultimoTot)
    $ultimoTot=$berekeningVanaf;
$fondsWaarde=$this->waarden['maandsFondsUitsluitingen'][date('Y-m-d',$this->waarden['maandsData_1'])];
$dagen=round(($ultimoTot-$ultimoVanaf)/86400);
if(date('d-m',$ultimoVanaf) =='01-01')
  $dagen++;
//echo "<br>\n".date('d-m-Y',$ultimoVanaf)." -> ".date('d-m-Y',$ultimoTot)."<br>\n";
	$this->pdf->row(array('','Belegd vermogen ultimo '.vertaalTekst($__appvar["Maanden"][date("n",$this->waarden['maandsData_1'])],$pdf->rapport_taal),$this->formatGetal($this->waarden['maandsWaarde_1']-$fondsWaarde,0),$dagen));
//echo "<br>".$this->formatGetal($this->waarden['maandsWaarde_1']-$fondsWaarde,0)."=".$this->waarden['maandsWaarde_1']."-$fondsWaarde <br>\n";

if($berekeningVanaf>$this->waarden['maandsData_1'])
  $ultimoVanaf=$berekeningVanaf;
else
  $ultimoVanaf=$this->waarden['maandsData_1'];

if($pstartJul>$ultimoVanaf)
  $ultimoVanaf=$pstartJul;

if($berekeningVanaf>$ultimoTot)
  $ultimoTot=$berekeningVanaf;
else
  $ultimoTot=$this->waarden['maandsData_2'];

if($pstartJul>$ultimoTot)
  $ultimoTot=$pstartJul;
if($pstartJul>$ultimoVanaf)
  $ultimoVanaf=$pstartJul;
$dagen=round(($ultimoTot-$ultimoVanaf)/86400);
if($dagen<0)$dagen=0;

$fondsWaarde=$this->waarden['maandsFondsUitsluitingen'][date('Y-m-d',$this->waarden['maandsData_2'])];
	$this->pdf->row(array('','Belegd vermogen ultimo '.vertaalTekst($__appvar["Maanden"][date("n",$this->waarden['maandsData_2'])],$pdf->rapport_taal),$this->formatGetal($this->waarden['maandsWaarde_2']-$fondsWaarde,0),$dagen));

//echo "<br>".$this->formatGetal($this->waarden['maandsWaarde_2']-$fondsWaarde,0)."=".$this->waarden['maandsWaarde_2']."-$fondsWaarde <br>\n";

if($berekeningVanaf>$this->waarden['maandsData_2'])
  $ultimoVanaf=$berekeningVanaf;
else
  $ultimoVanaf=$this->waarden['maandsData_2'];

if($berekeningVanaf>$ultimoTot)
  $ultimoTot=$berekeningVanaf;
else
  $ultimoTot=$this->waarden['maandsData_3'];

if($pstartJul>$ultimoTot)
  $ultimoTot=$pstartJul;
if($pstartJul>$ultimoVanaf)
  $ultimoVanaf=$pstartJul;
$dagen=round(($ultimoTot-$ultimoVanaf)/86400);
if($dagen<0)$dagen=0;

$fondsWaarde=$this->waarden['maandsFondsUitsluitingen'][date('Y-m-d',$this->waarden['maandsData_3'])];
	$this->pdf->row(array('','Belegd vermogen ultimo '.vertaalTekst($__appvar["Maanden"][date("n",$this->waarden['maandsData_3'])],$pdf->rapport_taal),$this->formatGetal($this->waarden['maandsWaarde_3']-$fondsWaarde,0),$dagen));

//echo "<br>".$this->formatGetal($this->waarden['maandsWaarde_3']-$fondsWaarde,0)."=".$this->waarden['maandsWaarde_3']."-$fondsWaarde <br>\n";
	//$this->pdf->CellBorders = array('','','T');
$this->pdf->ln();
	$this->pdf->row(array('','Gemiddeld vermogen:',$this->formatGetal($this->waarden['rekenvermogen'],0),$this->waarden['periodeDagen']['dagen']));
	//$this->pdf->CellBorders = array();
	$this->pdf->ln();
$this->pdf->SetWidths(array(22,200));
$this->pdf->row(array('','Berekening '.$soort.' op basis van uw belegd vermogen en aantal dagen:'));
$this->pdf->ln();
$this->pdf->SetWidths(array(22,70,25,25,25));
$this->pdf->row(array('','','Percentage','Grondslag','Vergoeding'));
$this->pdf->ln();
$staffelTotaal=$this->waarden['beheerfeePerPeriodeNor'];
$waardeTotaal=0;
if($this->waarden['BeheerfeePercentageVermogen'] <> 0)
{
  $restWaarde=$this->waarden['rekenvermogen'];
  $vorigeStaffel=0;
  $staffelTotaal=0;
//listarray($this->waarden['portefeuilledata']);

	for($i=1;$i<5;$i++)
  {
    $staffelWaarde = $this->waarden['portefeuilledata']['BeheerfeeStaffel' . $i]-$vorigeStaffel;
    $vorigeStaffel = $this->waarden['portefeuilledata']['BeheerfeeStaffel' . $i];
    $staffelPercentage = $this->waarden['portefeuilledata']['BeheerfeeStaffelPercentage' . $i];
    if ($staffelWaarde >= $restWaarde)
    {
      $waarde=$restWaarde;
      $restWaarde=0;
    }
    elseif ($restWaarde > $staffelWaarde)
    {
      $restWaarde = $restWaarde - $staffelWaarde;
      $waarde=$staffelWaarde;
    }
    $fee=$waarde*$staffelPercentage/100;
    
    $feeDeel = $fee * $this->waarden['periodeDeelVanJaar'];
    //if($feeDeel<>0)
   // {
      $this->waarden['staffelWaarden'][$i] = array('staffelEind' =>$this->waarden['portefeuilledata']['BeheerfeeStaffel' . $i], 'percentage' => $staffelPercentage, 'waarde' => $waarde, 'fee' => $fee, 'feeDeel' => $feeDeel);
      $staffelTotaal += $feeDeel;
      $waardeTotaal += $waarde;
   // }
  }
}
else
{
  $vorigeStaffel=0;
  for($i=1;$i<5;$i++)
  {
    $staffelWaarde= $this->waarden['portefeuilledata']['BeheerfeeStaffel' . $i];
    $staffelPercentage = $this->waarden['portefeuilledata']['BeheerfeeStaffelPercentage' . $i];
  
    $this->waarden['staffelWaarden'][$i]['staffelEind']=$staffelWaarde;// = array('staffelEind' => $staffelWaarde, 'percentage' => $staffelPercentage, 'waarde' => $waarde, 'fee' => $fee, 'feeDeel' => $feeDeel);
   $this->waarden['staffelWaarden'][$i]['percentage']=$staffelPercentage;
    $waardeTotaal +=  $this->waarden['staffelWaarden'][$i]['waarde'];
  }
}
//listarray($this->waarden);
foreach($this->waarden['staffelWaarden'] as $i=>$staffelData)
{
	if($i==1)
    $this->pdf->row(array('','Tot €'.$this->formatGetal($staffelData['staffelEind'],0),$this->formatGetal($staffelData['percentage'],2)."%",$this->formatGetal($staffelData['waarde'],2),$this->formatGetal($staffelData['feeDeel'],2)));
  elseif($i==4)
    $this->pdf->row(array('','Van €'.$this->formatGetal($vorige,0),$this->formatGetal($staffelData['percentage'],2)."%",$this->formatGetal($staffelData['waarde'],2),$this->formatGetal($staffelData['feeDeel'],2)));
  else
    $this->pdf->row(array('','Van €'.$this->formatGetal($vorige,0)." tot €".$this->formatGetal($staffelData['staffelEind'],0),$this->formatGetal($staffelData['percentage'],2)."%",$this->formatGetal($staffelData['waarde'],2),$this->formatGetal($staffelData['feeDeel'],2)));
	$vorige=$staffelData['staffelEind'];
}
$this->pdf->ln();
$this->pdf->row(array('','Totaal','',$this->formatGetal($waardeTotaal,2),$this->formatGetal($staffelTotaal,2)));
$this->pdf->ln();

if($this->waarden['BeheerfeePercentageVermogen'] <> 0 || $this->waarden['MinJaarbedragGebruikt']==1)
{
  $standaardInc=$staffelTotaal*(100+$this->waarden['btwTarief'])/100;
  $this->pdf->row(array('', 'Standaard '.$soort.' inclusief ' . $this->waarden['btwTarief'] . '% BTW', '', '', $this->formatGetal($standaardInc, 2) . ""));
  $this->pdf->ln();
  
  $this->pdf->row(array('','Uw tarief op basis van onze afspraak ','','',$this->formatGetal($this->waarden['beheerfeePerPeriode'],2)));
  $this->pdf->row(array('', 'BTW ( ' . $this->waarden['btwTarief'] . '%)', '', '', $this->formatGetal($this->waarden['btw'], 2) . ""));
  $this->pdf->ln();
  if($this->waarden['BeheerfeePercentageVermogen'] <> 0 )
  {

    $this->pdf->row(array('', 'Uw '.$soort.' inclusief ' . $this->waarden['btwTarief'] . '% BTW', $this->formatGetal($this->waarden['BeheerfeePercentageVermogen'], 2) . '%', '', $this->formatGetal($this->waarden['beheerfeeBetalenIncl'], 2) . ""));
    $this->pdf->ln();

    $this->pdf->row(array('', 'Uw voordeel t.o.v. standaardtarief', '', '', $this->formatGetal($standaardInc-$this->waarden['beheerfeeBetalenIncl'], 2) . ""));
  }
  else
    $this->pdf->row(array('', 'Uw '.$soort.' inclusief ' . $this->waarden['btwTarief'] . '% BTW', '', '', $this->formatGetal($this->waarden['beheerfeeBetalenIncl'], 2) . ""));
}
else
{
  $this->pdf->row(array('', 'BTW ( ' . $this->waarden['btwTarief'] . '%)', '', '', $this->formatGetal($this->waarden['btw'], 2) . ""));
  $this->pdf->ln();
  $this->pdf->row(array('', 'Uw '.$soort.' inclusief ' . $this->waarden['btwTarief'] . '% BTW', '', '', $this->formatGetal($this->waarden['beheerfeeBetalenIncl'], 2) . ""));
}


$this->pdf->AutoPageBreak=false;
$this->pdf->setXY(0,297-20);
//$this->pdf->SetFont($font,"",10);
//$this->pdf->SetTextColor(51,51,51);
$this->pdf->MultiCell(210,$this->pdf->rowHeight-0.5,"Heeren Vermogensbeheer BV | Henri Polaklaan 30 1018 CT Amsterdam
www.Heerenvb.nl | 020-214 21 03 | KVK 71280146 | BTW NL858649160B01 | NL32 INGB 0009 1039 61",0,'C',0);
$this->pdf->AutoPageBreak=true;

    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->rowHeight=$rowHeightBackup;
    ?>

<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/04/20 17:01:27 $
File Versie					: $Revision: 1.4 $

$Log: Factuur_L71.php,v $
Revision 1.4  2019/04/20 17:01:27  rvv
*** empty log message ***

Revision 1.3  2017/02/04 19:11:04  rvv
*** empty log message ***

Revision 1.2  2016/10/19 11:00:05  rvv
*** empty log message ***

Revision 1.1  2016/10/12 09:46:36  rvv
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


   	$this->pdf->underlinePercentage=0.8;
    $this->pdf->brief_font='Times';
		$this->pdf->rapport_type = "FACTUUR";
		$this->pdf->AddPage('P');

$rowHeightBackup=$this->pdf->rowHeight;
$this->pdf->rowHeight = 5;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$vanaf=db2jul($this->waarden['datumVan']);
		$tot=db2jul($this->waarden['datumTot']);
    if(is_file($this->pdf->rapport_logo))
		{
			if($this->pdf->CurOrientation=='P')
				$pageWidth=210;
			else
				$pageWidth=297;

			$factor=0.045;
			$xSize=1200*$factor;//$x=885*$factor;
			$ySize=350*$factor;//$y=849*$factor;
			$logoX=$pageWidth/2-$xSize/2;
			$this->pdf->Image($this->pdf->rapport_logo, $logoX, 2, $xSize, $ySize);
		}


		$this->pdf->SetY(60);
		$this->pdf->SetWidths(array(22,150));
	  $this->pdf->SetAligns(array('R','L'));

		$this->pdf->row(array('',$this->waarden['clientNaam']));
		if($this->waarden['clientNaam1'] <> '')
		  $this->pdf->row(array('',$this->waarden['clientNaam1']));
		$this->pdf->row(array('',$this->waarden['clientAdres']));
		if($this->waarden['clientPostcode'] != '')
	  	$plaats = $this->waarden['clientPostcode'] . "  " .$this->waarden['clientWoonplaats'];
	  else
	  	$plaats = $this->waarden['clientWoonplaats'];
		$this->pdf->row(array('',$plaats));
		$this->pdf->row(array('',$this->waarden['clientLand']));

		$this->pdf->SetY(110);
		//$this->pdf->SetAligns(array('R','C'));

    $vanafTxt=date("d",$vanaf)." ".vertaalTekst($__appvar["Maanden"][date("n",$vanaf)],$pdf->rapport_taal)." ".date("Y",$vanaf);
    $totTxt=date("d",$tot)." ".vertaalTekst($__appvar["Maanden"][date("n",$tot)],$pdf->rapport_taal)." ".date("Y",$tot);
    $rapportagePeriode = $vanafTxt.' t/m '.$totTxt;

		$this->pdf->row(array('',"Feenota: $totTxt" ));

		$this->pdf->SetAligns(array('R','L','R'));


		$this->pdf->ln(20);

	$this->pdf->CellBorders = array();
	$this->pdf->ln(20);
	$this->pdf->SetWidths(array(22,100,30,50));
  if(count($this->waarden['extraFactuurregels']['regels'])>0)
  {
    $extraRegelWaarde = $this->waarden['extraFactuurregels']['zonderBTW'] + $this->waarden['extraFactuurregels']['metBTW'];
    $extraRegelWaardeZonderBtw=$this->waarden['extraFactuurregels']['zonderBTW'];
  }
  else
  {
    $extraRegelWaarde = 0;
    $extraRegelWaardeZonderBtw=0;
  }
  
  $this->pdf->row(array('','Fee',"€ ".$this->formatGetal($this->waarden['beheerfeeBetalen'],2).""));
  $this->pdf->ln();
 // listarray($this->waarden);
  $this->pdf->row(array('','BTW ('.$this->waarden['btwTarief'].'%) over €'.$this->formatGetal($this->waarden['beheerfeeBetalen']-$extraRegelWaardeZonderBtw,2),"€ ".$this->formatGetal($this->waarden['btw'],2).""));
  $this->pdf->CellBorders = array('','',array('TS','UU'));
  $this->pdf->row(array('','Totaal',"€ ".$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2).""));//-$this->waarden['BeheerfeeBedragBuitenBTWPeriode']
  $this->pdf->ln();
  $this->pdf->CellBorders = array();
 

//  $this->pdf->ln();
//$this->pdf->CellBorders = array('','',array('TS','UU'));
 // $this->pdf->row(array('','Totaalbedrag',"€ ".$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2).""));
$this->pdf->CellBorders = array();
  $this->pdf->ln(12);
  $this->pdf->SetWidths(array(22,150));
	$this->pdf->row(array('',"Het totaalbedrag zal van uw rekening worden afgeschreven."));
$this->pdf->ln(12);
  $factuurNr=sprintf("%04d",$this->waarden['factuurNummer']);
$this->pdf->SetWidths(array(22,60,30,50));
$this->pdf->SetAligns(array('R','L','R','L'));

$query="SELECT Rekeningen.IBANnr FROM Rekeningen 
WHERE Rekeningen.Portefeuille='".$this->waarden['portefeuille']."' AND 
Rekeningen.Depotbank='".$this->waarden['Depotbank']."' AND 
Rekeningen.IBANnr<>'' AND inactief=0 AND Valuta='EUR' ORDER BY Rekeningen.Valuta limit 1";
$db=new DB();
$db->SQL($query);
$rekening=$db->lookupRecord();
if($rekening['IBANnr']<>'')
  $rekeningnr=$rekening['IBANnr'];
else
	$rekeningnr=$this->waarden['portefeuille'];

  $this->pdf->row(array('',"Rekeningnummer:",'',$rekeningnr));
  $this->pdf->row(array('',"Factuurnummer:",date("Y/",$tot),date("Y",$tot)."$factuurNr"));
  $this->pdf->row(array('',"Rekeningnummer VEC:",'',"NL45ISAE0000001620"));

$query="SELECT Naam,Adres,Woonplaats,telefoon,fax FROM Vermogensbeheerders WHERE Vermogensbeheerder='".$this->waarden['Vermogensbeheerder']."'";
$db->SQL($query);
$vmb=$db->lookupRecord();

$autoPageBreakBackup=$this->pdf->AutoPageBreak;
$this->pdf->AutoPageBreak=false;
$this->pdf->setY(275);
$this->pdf->rowHeight=$rowHeightBackup;
$this->pdf->SetFont($this->pdf->rapport_font,'',7);
$this->pdf->SetWidths(array(25,50,50,50));
$this->pdf->SetAligns(array('L','L','L','L'));
$this->pdf->SetTextColor(100,100,100);
$this->pdf->row(array('','N.V. De Vereenigde Effecten Compagnie',$vmb['Adres']           ,'KvK Alkmaar nr. 34.12.74.03'));
$this->pdf->row(array('','',                                     $vmb['Woonplaats']      ,'Vergunning AFM nr. BFW789'));
$this->pdf->row(array('','E: info@effectencompagnie.nl'         ,"T: ".$vmb['telefoon'] ,'Onder toezicht van DNB'));
$this->pdf->row(array('','I: www.effectencompagnie.nl'          ,"F: ".$vmb['fax'] ,'DSI registratie'));
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
$this->pdf->SetTextColor(0,0,0);
$this->pdf->AutoPageBreak=$autoPageBreakBackup;

//-------------------------------------------------------------------
$this->pdf->addPage('P');
$this->pdf->SetY(70);
//$this->pdf->SetAligns(array('R','C'));


$vanafTxt=date("d",$vanaf)." ".vertaalTekst($__appvar["Maanden"][date("n",$vanaf)],$pdf->rapport_taal)." ".date("Y",$vanaf);
$totTxt=date("d",$tot)." ".vertaalTekst($__appvar["Maanden"][date("n",$tot)],$pdf->rapport_taal)." ".date("Y",$tot);
$rapportagePeriode = $vanafTxt.' t/m '.$totTxt;
$this->pdf->SetWidths(array(22,150));
$this->pdf->row(array('',"Feenota: $totTxt" ));
$this->pdf->SetAligns(array('R','L'));

$this->pdf->ln(20);


//listarray($this->waarden);

$this->pdf->SetAligns(array('R','L'));
/*
	$this->pdf->row(array('','Wij hebben voor u het beheerloon berekend over het '.$kwartalen[$this->waarden['kwartaal']].' kwartaal.' ));
	$this->pdf->ln();
	$this->pdf->row(array('','Het beheerloon bedraagt '.$this->formatGetal($this->waarden['BeheerfeePercentageVermogen'],2).'% per jaar, dat wil zeggen '.$this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'],2).'% voor dit kwartaal van het gemiddelde vermogen dat u in deze periode aanhield op rekening '.$this->waarden['portefeuille'].'.'));
  $this->pdf->ln();
	$this->pdf->row(array('','Het gemiddeld vermogen is berekend door de maandultimo standen te middelen.' ));
	$this->pdf->ln();
*/
//listarray();
$this->pdf->SetWidths(array(22,70,30,50));
$this->pdf->SetAligns(array('R','L','R'));
$this->pdf->SetFont($this->pdf->rapport_font,'BU',$this->pdf->rapport_fontsize);
$this->pdf->row(array('','Bepaling vermogen:'));
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
$this->pdf->row(array('','Aanvangsvermogen per '.$vanafTxt.':',"€ ".$this->formatGetal($this->waarden['portefeuilleVerdeling']['beginWaarde']['totaal'],0).""));
$this->pdf->row(array('','Eindvermogen per '.$totTxt.':',"€ ".$this->formatGetal($this->waarden['portefeuilleVerdeling']['eindWaarde']['totaal'],0).""));
$this->pdf->CellBorders = array('','',array('TS','UU'));
$this->pdf->row(array('','Gemiddeld belegd vermogen:',"€ ".$this->formatGetal($this->waarden['gemiddeldeVermogen'],0).""));

if($this->waarden['huisfondsWaarde'] <> 0 || $this->waarden['BeheerfeeBedragBuitenFee'] <> 0)
{
  $this->pdf->CellBorders = array();
  $this->pdf->ln(4);
  if($this->waarden['huisfondsWaarde'] <> 0)
    $this->pdf->row(array('', 'Beleggingen via VEC Fondsbeheer', "€ " . $this->formatGetal($this->waarden['huisfondsWaarde'], 0) . ""));
  if($this->waarden['BeheerfeeBedragBuitenFee'] <> 0)
    $this->pdf->row(array('', 'Bedrag buiten beheerdee', "€ " . $this->formatGetal($this->waarden['BeheerfeeBedragBuitenFee'], 0) . ""));
  $this->pdf->CellBorders = array('', '', array('TS', 'UU'));
  $this->pdf->row(array('', 'Grondslag voor Fee-berekening:', "€ " . $this->formatGetal($this->waarden['rekenvermogen'], 0) . ""));
}


$this->pdf->CellBorders = array();
$this->pdf->ln(20);
$this->pdf->SetWidths(array(22,70,30,50));
if(count($this->waarden['extraFactuurregels']['regels'])>0)
{
  $extraRegelWaarde = $this->waarden['extraFactuurregels']['zonderBTW'] + $this->waarden['extraFactuurregels']['metBTW'];
  $extraRegelWaardeZonderBtw=$this->waarden['extraFactuurregels']['zonderBTW'];
}
else
{
  $extraRegelWaarde = 0;
  $extraRegelWaardeZonderBtw=0;
}

//if($extraRegelWaarde<>0)
//  $this->pdf->row(array('',"Doorberekening kosten","€ ".$this->formatGetal($extraRegelWaarde,2).""));
$this->pdf->SetWidths(array(22,70,30,20,20,15));
$this->pdf->SetAligns(array('L','L','R','R','R','R'));
$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
$this->pdf->row(array('','',"Bedrag","BTW",'Totaal','BTW %'));
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
$feeBedrag=$this->waarden['beheerfeeBetalen']-$this->waarden['administratieBedrag']-$extraRegelWaarde;
$this->pdf->row(array('','Fee',"€ ".$this->formatGetal($feeBedrag,2),
									$this->formatGetal($feeBedrag*$this->waarden['btwTarief']/100,2),
									$this->formatGetal($feeBedrag*(1+($this->waarden['btwTarief']/100)),2),
									$this->formatGetal($this->waarden['btwTarief'],0).'%'));
if(count($this->waarden['extraFactuurregels']['regels'])>0)
{
  $samenvoeging=array();
  foreach ($this->waarden['extraFactuurregels']['regels'] as $regel)
  {
    if($regel['btw']==1)
    {
      $regelTotaal = $regel['bedrag'] + $regel['btwBedrag'];
      $btwBedrag=$regel['btwBedrag'];
      $btw=($regelTotaal/$regel['bedrag']-1)*100;
    }
    else
    {
      $regelTotaal = $regel['bedrag'];
      $btw=0;
      $btwBedrag=0;
    }
    
    $samenvoeging[$regel['omschrijving']]['bedrag']+=$regel['bedrag'];
    $samenvoeging[$regel['omschrijving']]['btwBedrag']+=$btwBedrag;
    $samenvoeging[$regel['omschrijving']]['regelTotaal']+=$regelTotaal;
    $samenvoeging[$regel['omschrijving']]['btw']=$btw;
  }
  foreach ($samenvoeging as $omschrijving=>$data)
  {

    $this->pdf->row(array('',$omschrijving,"€ ".$this->formatGetal($data['bedrag'],2),$this->formatGetal($data['btwBedrag'],2),$this->formatGetal($data['regelTotaal'],2),
                      $this->formatGetal(($data['regelTotaal']/$data['bedrag']-1)*100,0).'%'));
  }
  //$extraRegelWaarde=$this->waarden['extraFactuurregels']['zonderBTW']+$this->waarden['extraFactuurregels']['metBTW'];
}
if($this->waarden['administratieBedrag'] <> 0)
$this->pdf->row(array('','Account Kosten',"€ ".$this->formatGetal($this->waarden['administratieBedrag'],2),
									$this->formatGetal($this->waarden['administratieBedrag']*$this->waarden['btwTarief']/100,2),
									$this->formatGetal($this->waarden['administratieBedrag']*(1+($this->waarden['btwTarief']/100)),2),
									$this->formatGetal($this->waarden['btwTarief'],0).'%'));
$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
$this->pdf->CellBorders = array('','',array('TS','UU'),array('TS','UU'),array('TS','UU'));
$this->pdf->row(array('','',"€ ".$this->formatGetal($this->waarden['beheerfeeBetalen'],2),
									$this->formatGetal($this->waarden['btw'],2),
                  $this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)
                ));
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

//listarray($this->waarden);
/*
$this->pdf->ln();
$this->pdf->row(array('','BTW ('.$this->waarden['btwTarief'].'%) over €'.$this->formatGetal($this->waarden['beheerfeeBetalen']-$extraRegelWaardeZonderBtw,2),"€ ".$this->formatGetal($this->waarden['btw'],2).""));
$this->pdf->CellBorders = array('','',array('TS','UU'));
$this->pdf->row(array('','Totaal bedrag',"€ ".$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2).""));//-$this->waarden['BeheerfeeBedragBuitenBTWPeriode']
*/
$this->pdf->ln();
$this->pdf->CellBorders = array();


<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/15 16:36:16 $
File Versie					: $Revision: 1.7 $

$Log: Factuur_L88.php,v $
Revision 1.7  2020/07/15 16:36:16  rvv
*** empty log message ***

Revision 1.6  2020/05/02 15:56:59  rvv
*** empty log message ***

Revision 1.5  2020/04/11 16:34:15  rvv
*** empty log message ***

Revision 1.4  2020/04/08 15:41:35  rvv
*** empty log message ***

Revision 1.3  2020/03/29 08:08:21  rvv
*** empty log message ***

Revision 1.2  2020/03/28 15:44:38  rvv
*** empty log message ***

Revision 1.1  2020/03/21 12:33:16  rvv
*** empty log message ***

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

      
      $factor=0.02;
      $xSize=1931*$factor;
      $ySize=701*$factor;
      
      $logoX=$this->pdf->w-$xSize-$this->pdf->marge*2;
			$this->pdf->Image($this->pdf->rapport_logo, $logoX, $this->pdf->marge*2, $xSize, $ySize);
		}


		$this->pdf->SetY(45);
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

		$this->pdf->SetY(80);
$factuurNr=$this->waarden['factuurNummer'];//sprintf("%07d",$this->waarden['factuurNummer']);
$vanafTxt=date("d",$vanaf)." ".vertaalTekst($__appvar["Maanden"][date("n",$vanaf)],$this->pdf->rapport_taal)." ".date("Y",$vanaf);
$totTxt=date("d",$tot)." ".vertaalTekst($__appvar["Maanden"][date("n",$tot)],$this->pdf->rapport_taal)." ".date("Y",$tot);
$rapportagePeriode = $vanafTxt.' t/m '.$totTxt;

$this->pdf->row(array('',"Amsterdam, ".date("d")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y") ));
$this->pdf->ln(12);
		$this->pdf->row(array('',"Factuurnummer: $factuurNr" ));
$this->pdf->ln(12);
		$this->pdf->SetAligns(array('R','L'));
$this->pdf->row(array('',$this->waarden['CRM_verzendAanhef'].','));

		$this->pdf->ln(12);




  $kwartalen=array(0,'eerste','tweede','derde','vierde');
	$this->pdf->SetAligns(array('R','L'));
$this->pdf->row(array('',"Onderstaand treft u de berekeningen van de beheervergoeding over het ".$kwartalen[$this->waarden['kwartaal']]." kwartaal ".date("Y",$tot)." aan."));
$this->pdf->ln();
//$this->pdf->ln();
	$this->pdf->SetWidths(array(22,100,10,30,50));
	$this->pdf->SetAligns(array('R','L','L','R'));
//	$this->pdf->row(array('','Aanvangsvermogen per '.$vanafTxt.':',"EUR".$this->formatGetal($this->waarden['portefeuilleVerdeling']['beginWaarde']['totaal'],0).""));
//	$this->pdf->row(array('','Eindvermogen per '.$totTxt.':',"EUR".$this->formatGetal($this->waarden['portefeuilleVerdeling']['eindWaarde']['totaal'],0).""));
//  $this->pdf->CellBorders = array('','',array('TS','UU'));
  $this->pdf->row(array('','Gemiddeld belegd vermogen '.$totTxt,"EUR",$this->formatGetal($this->waarden['rekenvermogen'],2).""));
//listarray($this->waarden['maandsFondsUitsluitingen']);
  /*
  if($this->waarden['huisfondsWaarde'] <> 0)
  {
		$this->pdf->CellBorders = array();
		$this->pdf->ln(4);
  	$this->pdf->row(array('', 'Beleggingen via VEC Fondsbeheer', "€ " . $this->formatGetal($this->waarden['huisfondsWaarde'], 0) . ""));
		$this->pdf->CellBorders = array('', '', array('TS', 'UU'));
		$this->pdf->row(array('', 'Grondslag voor Fee-berekening:', "€ " . $this->formatGetal($this->waarden['rekenvermogen'], 0) . ""));
  }
*/

	
	$this->pdf->CellBorders = array();
	$this->pdf->ln();
//$this->pdf->ln();
	$this->pdf->SetWidths(array(22,100,10,30,50));
$this->pdf->SetAligns(array('R','L','L','R'));
  $this->pdf->row(array('','Vermogensbeheervergoeding '.$this->waarden['kwartaal'].'e kwartaal '.($this->waarden['BeheerfeePercentageVermogenDeelVanJaar']<>0?$this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'],4).'%':''),"EUR",$this->formatGetal($this->waarden['beheerfeeBetalen']-$this->waarden['administratieBedrag'],2).""));//-$this->waarden['administratieBedrag']
 // $this->pdf->row(array('','Account Kosten',"€ ".$this->formatGetal($this->waarden['administratieBedrag'],2).""));

if($this->waarden['administratieBedrag'] <> 0)
{
  if($this->waarden['administratieBedrag'] < 0)
	{
    $this->pdf->row(array('','Korting vermogensbeheer',"EUR",$this->formatGetal($this->waarden['administratieBedrag'],2).""));//-$this->waarden['administratieBedrag']
	}
	else
	{
    $this->pdf->row(array('','Toeslag vermogensbeheer',"EUR",$this->formatGetal($this->waarden['administratieBedrag'],2).""));//-$this->waarden['administratieBedrag']
	}
}

if($this->waarden['huisfondsWaarde'] <> 0 || $this->waarden['BeheerfeeBedragBuitenFee'] <> 0)
{
  $this->pdf->SetWidths(array(22,100+10+30+50));
  $this->pdf->row(array('',"Bij de berekening is rekening gehouden met posities die buiten ons beheer vallen."));
  $this->pdf->SetWidths(array(22,100,10,30,50));
}



$this->pdf->CellBorders = array('','','T','T');
$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
  $this->pdf->row(array('','Subtotaal',"EUR",$this->formatGetal($this->waarden['beheerfeeBetalen'],2).""));
$this->pdf->CellBorders = array();
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  $this->pdf->ln();
 // listarray($this->waarden);
  $this->pdf->row(array('','BTW ('.$this->formatGetal($this->waarden['btwTarief'],2).'%)',"EUR",$this->formatGetal($this->waarden['btw'],2).""));
$this->pdf->CellBorders = array('','','T','T');
$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
  $this->pdf->row(array('','Totaal',"EUR",$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2).""));//-$this->waarden['BeheerfeeBedragBuitenBTWPeriode']
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  $this->pdf->ln();
  $this->pdf->CellBorders = array();
 

//  $this->pdf->ln();
//$this->pdf->CellBorders = array('','',array('TS','UU'));
 // $this->pdf->row(array('','Totaalbedrag',"€ ".$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2).""));
$this->pdf->CellBorders = array();
  $this->pdf->ln(12);
  $this->pdf->SetWidths(array(22,150));
	$this->pdf->row(array('',"De beheervergoeding wordt berekend over het gewogen gemiddelde van het beheerd vermogen.
Conform de vermogensbeheerovereenkomst is Mpartners gemachtigd ".$this->waarden['depotbankOmschrijving']."
opdracht te geven het bovenstaande bedrag ten laste van uw rekening af te schrijven."));
$this->pdf->ln();

//$this->pdf->SetWidths(array(22,150));
//$this->pdf->SetAligns(array('R','L','R','L'));

$query="SELECT Rekeningen.IBANnr FROM Rekeningen 
WHERE Rekeningen.Portefeuille='".$this->waarden['portefeuille']."' AND 
Rekeningen.Depotbank='".$this->waarden['Depotbank']."' AND 
Rekeningen.IBANnr<>''  ORDER BY Rekeningen.Valuta limit 1";
$db=new DB();
$db->SQL($query);
$rekening=$db->lookupRecord();
if($rekening['IBANnr']<>'')
  $rekeningnr=$rekening['IBANnr'];
else
	$rekeningnr=$this->waarden['portefeuille'];

//$this->pdf->SetAligns(array('L','L','L','L'));
//$this->pdf->SetWidths(array(22,150));
  $this->pdf->row(array('',"Bovengenoemde vergoeding zullen wij ten laste brengen van rekeningnummer ".$rekeningnr));

$this->pdf->ln();
$this->pdf->row(array('',"Met vriendelijke groet,


Mpartners"));
  //$this->pdf->row(array('',"Factuurnummer:",date("Y/",$tot),date("Y",$tot)."$factuurNr"));


$autoPageBreakBackup=$this->pdf->AutoPageBreak;
$this->pdf->AutoPageBreak=false;
$this->pdf->setY(275);
$this->pdf->rowHeight=$rowHeightBackup;
$this->pdf->SetFont($this->pdf->rapport_font,'',7);
$this->pdf->SetWidths(array(22,40,40,40,40));
$this->pdf->SetAligns(array('L','C','C','C','C'));
$this->pdf->SetTextColor(100,100,100);
$this->pdf->row(array('','IBAN: NL60 GILL 0211622907','BIC: GILLNLQA ','BTW Nummer: NL8223.23.655.B01','KVK: 34389387'));
$this->pdf->SetWidths(array(22,40,40,40,40));
$this->pdf->row(array('','','Vergunninghouder AFM','Ingeschreven in het register DSI'));
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0,0,0);
$this->pdf->AutoPageBreak=$autoPageBreakBackup;

    ?>

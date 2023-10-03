<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/10/21 09:43:16 $
File Versie					: $Revision: 1.3 $

$Log: Factuur_L78.php,v $
Revision 1.3  2018/10/21 09:43:16  rvv
*** empty log message ***

Revision 1.2  2018/10/20 18:20:52  rvv
*** empty log message ***

Revision 1.1  2018/06/24 11:13:51  rvv
*** empty log message ***

Revision 1.23  2017/10/02 06:04:00  rvv
*** empty log message ***

Revision 1.22  2017/09/30 16:30:24  rvv
*** empty log message ***

Revision 1.21  2017/03/20 06:41:15  rvv
*** empty log message ***

Revision 1.20  2017/02/04 19:11:04  rvv
*** empty log message ***

Revision 1.19  2016/10/19 18:41:21  rvv
*** empty log message ***

Revision 1.18  2016/07/24 09:50:17  rvv
*** empty log message ***

Revision 1.17  2016/02/04 11:56:13  rvv
*** empty log message ***

Revision 1.16  2016/01/27 07:39:03  rvv
*** empty log message ***

Revision 1.15  2014/04/12 16:27:35  rvv
*** empty log message ***

Revision 1.14  2012/09/13 15:59:12  rvv
*** empty log message ***

Revision 1.13  2012/02/09 12:14:28  cvs
adreswijziging

Revision 1.12  2011/12/14 19:00:28  rvv
*** empty log message ***

Revision 1.11  2011/10/12 18:02:04  rvv
*** empty log message ***

Revision 1.10  2011/09/14 09:26:56  rvv
*** empty log message ***

Revision 1.9  2011/07/08 06:43:47  cvs
*** empty log message ***

Revision 1.8  2011/03/13 18:37:31  rvv
*** empty log message ***

Revision 1.7  2011/01/11 08:23:38  cvs
*** empty log message ***

Revision 1.6  2011/01/10 08:02:21  cvs
*** empty log message ***

Revision 1.5  2011/01/08 14:26:52  rvv
*** empty log message ***

Revision 1.4  2011/01/05 18:52:30  rvv
*** empty log message ***

Revision 1.3  2010/07/02 12:23:19  cvs
*** empty log message ***

Revision 1.2  2010/07/02 12:03:34  cvs
*** empty log message ***

Revision 1.1  2010/05/05 18:38:23  rvv
*** empty log message ***

Revision 1.1  2010/03/31 17:26:47  rvv
*** empty log message ***



*/

global $__appvar;


	  $this->pdf->SetWidths(array(22,150));
	  $this->pdf->SetAligns(array('R','L'));
    $this->pdf->rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight = 5;
    
    $this->pdf->brief_font='Arial';
    if(file_exists(FPDF_FONTPATH.'calibril.php'))
		{
  	  if(!isset($this->pdf->fonts['calibri']))
	    {
		    $this->pdf->AddFont('calibri','','calibril.php');
		    $this->pdf->AddFont('calibri','B','calibri.php');
		    $this->pdf->AddFont('calibri','I','calibrii.php');
		    $this->pdf->AddFont('calibri','BI','calibribi.php');
	    }
		// $this->pdf->rapport_font = 'calibri';
      $this->pdf->brief_font='calibri';
		}   

$db=new DB();
$query="SELECT
Vermogensbeheerders.Naam,
Vermogensbeheerders.Adres,
Vermogensbeheerders.Woonplaats,
Vermogensbeheerders.Telefoon,
Vermogensbeheerders.Fax,
Vermogensbeheerders.Email,
Vermogensbeheerders.rekening,
Vermogensbeheerders.bank,
Vermogensbeheerders.website
FROM
Vermogensbeheerders
WHERE Vermogensbeheerders.Vermogensbeheerder='".$this->waarden['Vermogensbeheerder']."'";
$db->SQL($query);
$db->query();
$vermRecord=$db->nextRecord();

    
		$this->pdf->rapport_type = "FACTUUR";
		$this->pdf->AddPage('P');


		$this->pdf->SetY(50);
		$this->pdf->SetFont($this->pdf->brief_font,'B',11);
		$this->pdf->SetFont($this->pdf->brief_font,'',11);
		$this->pdf->row(array('',$this->waarden['CRM_naam']));
		if($this->waarden['CRM_naam1'] <> '')
		  $this->pdf->row(array('',$this->waarden['CRM_naam1']));
		$this->pdf->row(array('',$this->waarden['CRM_verzendAdres']));
		if($this->waarden['CRM_verzendPc'] != '')
	  	$plaats = $this->waarden['CRM_verzendPc'] . " " .$this->waarden['CRM_verzendPlaats'];
	  else
	  	$plaats = $this->waarden['CRM_verzendPlaats'];
		$this->pdf->row(array('',$plaats));
		$this->pdf->row(array('',$this->waarden['CRM_verzendLand']));

		$this->pdf->SetY(90);
		$this->pdf->SetAligns(array('R','L'));
    $this->pdf->row(array('','Wormer, '.(date("d"))." ".vertaalTekst($__appvar["Maanden"][date("n")],$pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln();

		$this->pdf->SetAligns(array('R','L'));

		$this->pdf->row(array('',"Geachte ".$this->waarden['CRM_verzendAanhef'].","));
		$this->pdf->ln();
		$vanaf=db2jul($this->waarden['datumVan']);
		$tot=db2jul($this->waarden['datumTot']);
		$vanafTxt=date("d",$vanaf)." ".vertaalTekst($__appvar["Maanden"][date("n",$vanaf)],$pdf->rapport_taal)." ".date("Y",$vanaf);
		$totTxt=date("d",$tot)." ".vertaalTekst($__appvar["Maanden"][date("n",$tot)],$pdf->rapport_taal)." ".date("Y",$tot);
		$rapportagePeriode = $vanafTxt.' t/m '.$totTxt;


    $kwartalen = array('null','eerste','tweede','derde','vierde');

    $txt="Hierbij ontvangt u van ons de nota voor het advies van uw vermogen gedurende het ".$kwartalen[$this->waarden['kwartaal']]." kwartaal ".date("Y",$tot).". Uw eindvermogen per $totTxt bedraagt € ".$this->formatGetal($this->waarden['rekenvermogen'], 2);
    $this->pdf->row(array('',$txt));
		$this->pdf->ln();


    $txt="Het bedrag van deze factuur zal binnen enkele dagen automatisch van uw rekeninge bij ".$this->waarden['depotbankOmschrijving'].", nummer ".$this->waarden['portefeuille']." worden afgeschreven.";
    $this->pdf->row(array('',$txt));

		//$this->pdf->SetFont($this->pdf->brief_font,'B',11);
		//$factuurNr=date("Y").".".sprintf("%03d",$this->waarden['factuurNummer']);
		//$this->pdf->row(array('',"Factuurnummer: $factuurNr"));
    $this->pdf->SetFont($this->pdf->brief_font,'B',11);
		$this->pdf->ln();
		$this->pdf->SetWidths(array(22,60,30,30));
    $this->pdf->SetAligns(array("L","L","R","R"));
   // $this->pdf->row(array('',"Vermogen per $vanafTxt",'€',$this->formatGetal($this->waarden['totaalWaardeVanaf'],2)));
   // $this->pdf->row(array('',"Vermogen per $totTxt",'€',$this->formatGetal($this->waarden['totaalWaarde'],2)));
    $this->pdf->row(array('', "Periode", 'Percentage','Bedrag'));
    $this->pdf->SetFont($this->pdf->brief_font,'',11);
    $this->pdf->row(array('',"Q".$this->waarden['kwartaal'].'-'.date("Y",$tot),$this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'],3).'%','€'.$this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar']*$this->waarden['rekenvermogen']/100,2)));
    if($this->waarden['administratieBedrag']<>0)
      $this->pdf->row(array('',"Wettelijke toezichtkosten",'','€'.$this->formatGetal($this->waarden['administratieBedrag'],2)));

    $this->pdf->ln();
    $this->pdf->row(array('',"BTW",$this->formatGetal($this->waarden['btwTarief'],1).'%','€'.$this->formatGetal($this->waarden['btw'],2)));
    $this->pdf->SetFont($this->pdf->brief_font,'B',11);
    $this->pdf->CellBorders = array('', '', '', 'T');
    $this->pdf->row(array('',"Totaal",'','€'.$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
    unset($this->pdf->CellBorders);
$this->pdf->ln(12);
$this->pdf->SetFont($this->pdf->brief_font,'',11);
$this->pdf->row(array('','Met vriendelijke groet,'));
$this->pdf->ln();
$this->pdf->row(array('',$vermRecord['Naam']));


if(is_file($this->pdf->rapport_logo))
{
  $this->pdf->SetFont($this->pdf->brief_font,'',9);
  $rhBackup=$this->pdf->rowHeight;
  $this->pdf->rowHeight=4;
  $this->pdf->Image($this->pdf->rapport_logo, 145, 200, 48);
  $this->pdf->SetY(220+3);
  $this->pdf->SetWidths(array(148-$this->pdf->marge+3,200));
  $this->pdf->SetFillColor(139,197,65);
  $this->pdf->rect(145+3,220,48-6,62,'F');
  $this->pdf->SetAligns(array('L','L','L'));
  $this->pdf->SetTextColor(5,105,52);
  $this->pdf->row(array('',$vermRecord['Adres']));
  $this->pdf->row(array('',$vermRecord['Woonplaats']));
  $this->pdf->ln();
  $this->pdf->row(array('','T '.$vermRecord['Telefoon']));
  $this->pdf->row(array('','F '.$vermRecord['Fax']));
  $this->pdf->row(array('',$vermRecord['Email']));
  $this->pdf->row(array('',$vermRecord['website']));
  $this->pdf->ln();
  $this->pdf->SetWidths(array(148-$this->pdf->marge+3,10,200));
  $this->pdf->row(array('','KVK','51793970'));
  $this->pdf->row(array('','BTW','NL850174454B01'));
  $this->pdf->row(array('','BANK',$vermRecord['rekening']));
  $this->pdf->ln();
  $this->pdf->SetWidths(array(148-$this->pdf->marge+3,200));
  $this->pdf->row(array('','Vergunninghouder AFM'));
  $this->pdf->row(array('','Deelnemer DSI'));
  $this->pdf->rowHeight=$rhBackup;
  $this->pdf->SetFillColor(0);
  $this->pdf->SetTextColor(0);
}



    ?>
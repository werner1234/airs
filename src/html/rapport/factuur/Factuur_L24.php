<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/07/27 18:13:15 $
File Versie					: $Revision: 1.18 $

$Log: Factuur_L24.php,v $
Revision 1.18  2019/07/27 18:13:15  rvv
*** empty log message ***

Revision 1.17  2019/07/05 16:39:37  rvv
*** empty log message ***

Revision 1.16  2019/06/19 15:51:37  rvv
*** empty log message ***

Revision 1.15  2019/06/16 09:51:35  rvv
*** empty log message ***

Revision 1.14  2017/04/26 15:16:49  rvv
*** empty log message ***

Revision 1.13  2017/04/23 12:50:28  rvv
*** empty log message ***

Revision 1.12  2017/04/12 08:30:57  rvv
*** empty log message ***

Revision 1.11  2016/05/18 15:29:13  rvv
*** empty log message ***

Revision 1.10  2016/05/16 07:34:58  rvv
*** empty log message ***

Revision 1.9  2016/05/15 17:17:06  rvv
*** empty log message ***

Revision 1.8  2016/03/13 16:25:09  rvv
*** empty log message ***

Revision 1.7  2016/01/13 17:11:03  rvv
*** empty log message ***

Revision 1.6  2016/01/06 16:46:37  rvv
*** empty log message ***

Revision 1.5  2015/10/25 13:06:52  rvv
*** empty log message ***

Revision 1.4  2015/10/21 13:49:17  rvv
*** empty log message ***

Revision 1.3  2015/10/18 13:46:50  rvv
*** empty log message ***

Revision 1.2  2015/10/14 16:12:43  rvv
*** empty log message ***

Revision 1.1  2010/03/31 17:26:47  rvv
*** empty log message ***



*/

global $__appvar;

    $this->pdf->brief_font='Times';
    $margeBackup=$this->pdf->marge;
    $rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->marge = 30;
    $this->pdf->rowHeight=4;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);

		$this->pdf->rapport_type = "FACTUUR";
		$this->pdf->AddPage('P');

    if(is_file($this->pdf->rapport_logo))
      $this->pdf->Image($this->pdf->rapport_logo, 85, 5, 43);

    $this->pdf->SetY($this->pdf->getY() +30);
		$this->pdf->SetFont($this->pdf->brief_font,'',10);
    $this->pdf->SetWidths(array(100,80));
    $this->pdf->SetAligns(array("L","L"));
		$this->pdf->row(array('',vertaalTekst("FACTUUR",$this->pdf->rapport_taal)));
		$kwartaal = ceil(date("n",db2jul($this->waarden['datumTot']))/3);
		$this->pdf->row(array($this->waarden['clientNaam']));
		if ($this->waarden['clientNaam1'] !='')
		  $this->pdf->row(array($this->waarden['clientNaam1']));
		$this->pdf->row(array($this->waarden['clientAdres']));
		$this->pdf->row(array(trim($this->waarden['clientPostcode'].' '.$this->waarden['clientWoonplaats'])));
		$this->pdf->row(array($this->waarden['clientLand']));

		$this->pdf->SetY(90);
    $this->pdf->row(array((date("d"))." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln();
		$this->pdf->cell(50,5,vertaalTekst("Factuurnummer",$this->pdf->rapport_taal)." : ".sprintf("%05d",$this->waarden['factuurNummer']),1,1,'L');


		$this->pdf->SetWidths(array(110,5,30));
    $this->pdf->SetAligns(array("L","R","R"));
    $this->pdf->ln();
    $this->pdf->row(array(vertaalTekst("Omschrijving",$this->pdf->rapport_taal),'',vertaalTekst("Bedrag",$this->pdf->rapport_taal)));
    $this->pdf->ln();
    $rapJul=db2jul($this->waarden['datumTot']);
    $this->pdf->row(array(vertaalTekst("Vermogen per",$this->pdf->rapport_taal)." ".(date("d",$rapJul))." ".vertaalTekst($__appvar["Maanden"][date("n",$rapJul)],$this->pdf->rapport_taal)." ".date("Y",$rapJul)."  € ".$this->formatGetal($this->waarden['totaalWaarde'],2)));
    $this->pdf->ln();
    $tmp=explode(".",$this->waarden['BeheerfeePercentageVermogenDeelVanJaar']);
    $aantalDecimalen=strlen($tmp[1]);

    if($aantalDecimalen < 1)
      $aantalDecimalen=1;
    elseif($aantalDecimalen>4)
      $aantalDecimalen=4;

$vastBedragPeriode=0;
if($this->waarden['BeheerfeeBedragVast'] <> 0)
{
  $vastBedragPeriode=$this->waarden['BeheerfeeBedragVast']*$this->waarden['periodeDeelVanJaar'];
}

    $feeBedrag=round($this->waarden['beheerfeePerPeriode']-$this->waarden['administratieBedrag']-$this->waarden['performancefee']-$vastBedragPeriode,2);
    if($vastBedragPeriode<>0)
    {
      $this->pdf->row(array(vertaalTekst("Beheerfee vast bedrag", $this->pdf->rapport_taal), '€', $this->formatGetal($vastBedragPeriode, 2)));
    }
    if($feeBedrag<>0)
    {
      $this->pdf->row(array(vertaalTekst("Beheerfee conform overeenkomst", $this->pdf->rapport_taal) . " (" . $this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'], $aantalDecimalen) . "% " . vertaalTekst("over", $this->pdf->rapport_taal) . " € " . $this->formatGetal($this->waarden['rekenvermogen'], 2) . ")", '€', $this->formatGetal($feeBedrag, 2)));
    }
    if($this->waarden['administratieBedrag'] <> 0)
    {
      $this->pdf->row(array(vertaalTekst("Administratie vergoeding", $this->pdf->rapport_taal), '€', $this->formatGetal($this->waarden['administratieBedrag'], 2)));
    }

    if($this->waarden['performancefeeRekenbedrag'] <> 0)
    {
      $this->pdf->row(array(vertaalTekst("Performancefee", $this->pdf->rapport_taal) . " " . $this->formatGetal($this->waarden['performancefeeRekenpercentage'], 2) . "% " . vertaalTekst("over netto performance", $this->pdf->rapport_taal) .
        " > " . $this->formatGetal($this->waarden['BeheerfeePerformanceDrempelPercentage'], 1) . " % € " . $this->formatGetal($this->waarden['performancefeeRekenbedrag'], 2) . ")", '€', $this->formatGetal($this->waarden['performancefee'], 2)));
    }
    if($this->waarden['btwTarief'] <> 0)
    {
      $this->pdf->row(array("" . $this->formatGetal($this->waarden['btwTarief'], 1) . "% " . vertaalTekst("BTW", $this->pdf->rapport_taal), '€', $this->formatGetal($this->waarden['btw'], 2)));
    }
  	$this->pdf->ln(10);
  	$this->pdf->Line($this->pdf->marge,$this->pdf->getY(),210-$this->pdf->marge,$this->pdf->getY());
  	$this->pdf->ln(2);
  	$this->pdf->row(array(vertaalTekst("Totaal",$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
  	$this->pdf->ln(10);
   $this->pdf->SetWidths(array(150));
   $facJul=time()+8*24*3600;
  
   if($this->waarden['IBAN']<>'' && $this->waarden['Depotbank'] <> 'TGB')
     $rekening=$this->waarden['IBAN'];
   else
     $rekening=$this->waarden['rekeningEur'];
     
  if($this->pdf->rapport_taal==1)
   $this->pdf->row(array("This amount will be debited from account number $rekening around ".(date("d",$facJul))." ".vertaalTekst($__appvar["Maanden"][date("n",$facJul)],$this->pdf->rapport_taal)." ".date("Y",$facJul)."."));
  else   
   $this->pdf->row(array(vertaalTekst("Het bedrag zal omstreeks",$this->pdf->rapport_taal)." ".(date("d",$facJul))." ".vertaalTekst($__appvar["Maanden"][date("n",$facJul)],$this->pdf->rapport_taal)." ".date("Y",$facJul)." ".vertaalTekst("van rekeningnummer",$this->pdf->rapport_taal)." ".$rekening." ".vertaalTekst("worden geïncasseerd",$this->pdf->rapport_taal)."."));

//vertaalTekst("Beheerfee",$this->pdf->rapport_taal)
  $db=new DB();
$query = "SELECT Vermogensbeheerders.Naam as vermogensbeheerderNaam,
                 Vermogensbeheerders.Adres as vermogensbeheerderAdres,
                 Vermogensbeheerders.Woonplaats as vermogensbeheerderWoonplaats,
                 Vermogensbeheerders.Telefoon,
                 Vermogensbeheerders.Fax,
                 Vermogensbeheerders.Email,
                 Vermogensbeheerders.website
		          FROM
		            Vermogensbeheerders
		          WHERE
                Vermogensbeheerders.Vermogensbeheerder ='".$this->waarden['Vermogensbeheerder']."'";
$db->SQL($query);
$db->Query();
$vermdata = $db->nextRecord();

$voet=$vermdata['vermogensbeheerderNaam'].'
'.$vermdata['vermogensbeheerderAdres'].' '.$vermdata['vermogensbeheerderWoonplaats'].' T '.$vermdata['Telefoon'].'
E '.$vermdata['Email'].'   I '.$vermdata['website'].'   KvK 33.248.757   btwnr. NL8258.21.599.B01   Rabobank NL47RABO00102876924';
$breakHeight=$this->pdf->PageBreakTrigger;
$this->pdf->PageBreakTrigger=297;
$this->pdf->SetXY(0,278);
$this->pdf->SetFont($this->pdf->brief_font,'',8);
$this->pdf->MultiCell(210,4,$voet,0,'C');
$this->pdf->PageBreakTrigger=$breakHeight;

$this->pdf->marge =$margeBackup;
$this->pdf->rowHeight=$rowHeightBackup;
$this->pdf->SetLeftMargin($this->pdf->marge);
$this->pdf->SetRightMargin($this->pdf->marge);
$this->pdf->SetTopMargin($this->pdf->marge);



?>
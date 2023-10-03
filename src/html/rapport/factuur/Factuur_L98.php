<?php

global $__appvar;
$this->pdf->rapport_type = "FACTUUR";

/*
if (file_exists(FPDF_FONTPATH . 'helvetica.php'))
{
  if(!isset($this->pdf->fonts['helvetica']))
  {
    $this->pdf->AddFont('helvetica','','helvetica.php');
    $this->pdf->AddFont('helvetica','I','helveticai.php');
    $this->pdf->AddFont('helvetica','B','helveticab.php');
    $this->pdf->AddFont('helvetica','BI','helveticabi.php');
  }
  $font = 'helvetica';
}
else
{
  $font = 'Times';
}
*/

if (file_exists(FPDF_FONTPATH . 'verdana.php'))
{
  if(!isset($this->pdf->fonts['verdana']))
  {
    $this->pdf->AddFont('verdana','','verdana.php');
    $this->pdf->AddFont('verdana','I','verdanai.php');
    $this->pdf->AddFont('verdana','B','verdanab.php');
    $this->pdf->AddFont('verdana','BI','verdanaib.php');
  }
  $font = 'verdana';
}


$fontsize =  10;

$this->pdf->AddPage('P');
$this->pdf->nextFactuur = true;

$DB = new DB();
$DB->SQL("SELECT
Vermogensbeheerders.Vermogensbeheerder,
Vermogensbeheerders.Naam,
Vermogensbeheerders.Adres,
Vermogensbeheerders.Woonplaats,
Vermogensbeheerders.Telefoon,
Vermogensbeheerders.Fax,
Vermogensbeheerders.Email,
Vermogensbeheerders.website
FROM
Vermogensbeheerders
WHERE Vermogensbeheerders.Vermogensbeheerder='" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'");
$vermData = $DB->lookupRecord();
$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'], $this->pdf->rapport_fontcolor['g'], $this->pdf->rapport_fontcolor['b']);
$logo = $__appvar['basedir'] . "/html/rapport/logo/" . $this->pdf->portefeuilledata['Logo'];
if (is_file($logo))
{
  $logoYpos = 15;
  if(!isset($logoSize))
    $xSize = 35;
  else
    $xSize = $logoSize;
  
  $this->pdf->Image($this->pdf->rapport_logo, $logoYpos, $logoYpos, $xSize);
  $this->pdf->SetFont($font, "", $fontsize);
}

if (isset($this->waarden['periodeDagen']['periode']) && $this->waarden['periodeDagen']['periode'] <> '')
{
  $parts = explode('->', $this->waarden['periodeDagen']['periode']);
  $vanjul = db2jul($parts[0]);
  $totjul = db2jul($parts[1]);
}
else
{
  $vanjul = db2jul($this->waarden['datumVan']);
  $totjul = db2jul($this->waarden['datumTot']);
  if (substr($this->waarden['datumVan'], 5, 5) != '01-01')
  {
    $vanjul += 86400;
  }
}
$vanDatum = date("j", $vanjul) . " " . vertaalTekst($this->pdf->__appvar["Maanden"][date("n", $vanjul)], $this->pdf->rapport_taal) . " " . date("Y", $vanjul);
$totDatum = date("j", $totjul) . " " . vertaalTekst($this->pdf->__appvar["Maanden"][date("n", $totjul)], $this->pdf->rapport_taal) . " " . date("Y", $totjul);
$totMaand = vertaalTekst($this->pdf->__appvar["Maanden"][date("n", $totjul)], $this->pdf->rapport_taal) . " " . date("Y", $totjul);
$nu = date("j") . " " . vertaalTekst($this->pdf->__appvar["Maanden"][date("n")], $this->pdf->rapport_taal) . " " . date("Y");


$this->DB = new DB();
$query = "SELECT
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.adres,
CRM_naw.pc,
CRM_naw.plaats,
CRM_naw.land,
CRM_naw.verzendPaAanhef,
Portefeuilles.BetalingsinfoMee
FROM CRM_naw Join Portefeuilles on CRM_naw.Portefeuille= Portefeuilles.portefeuille WHERE CRM_naw.Portefeuille = '" . $this->portefeuille . "'  ";
$this->DB->SQL($query);
$crmData = $this->DB->lookupRecord();

$extraMarge = 25 - $this->pdf->marge;
$this->pdf->SetY(55 - 8);
$this->pdf->SetWidths(array($extraMarge, 100, 80));
$this->pdf->SetFont($font, "", $fontsize);
$this->pdf->SetAligns(array("L", "L", "L", "R"));
$this->pdf->row(array('', $crmData['naam']));
$this->pdf->ln(1);
if( $crmData['naam1']<>'')
{
  $this->pdf->row(array('', $crmData['naam1']));
  $this->pdf->ln(1);
}

$this->pdf->row(array('', $crmData['adres']));
$this->pdf->ln(1);
$plaats = '';
$plaats = $crmData['pc'];
if ($crmData['plaats'] != '')
{
  $plaats .= "  " . $crmData['plaats'];
}
$this->pdf->row(array('', $plaats));
$this->pdf->ln(1);
$this->pdf->row(array('', $crmData['land']));

$this->pdf->SetY(105);
//$this->pdf->SetFont($font, "B", 12);
//$this->pdf->row(array('', "FACTUUR"));
//$this->pdf->SetFont($font, "", 10);
//$this->pdf->ln(2);

$this->pdf->SetWidths(array($extraMarge, 35, 80));
$this->pdf->row(array('', 'Factuurdatum:', $nu));
$this->pdf->row(array('', 'Factuurnummer:', date('y') . sprintf("%05d", $this->waarden['factuurNummer'])));
$this->pdf->row(array('', 'Portefeuille:', $this->portefeuille));

$this->pdf->SetY(135);
$this->pdf->SetWidths(array($extraMarge, 150, 12, 30));

$kwartalen = array('null', '1ste', '2e', '3e', '4e');
$jaar = date('Y', $totjul);
$feeTxt='Dexxi All-in fee ';

if ($this->waarden['BeheerfeeFacturatieVooraf'] == 1)
{
  $tmpKwartaal = $this->waarden['kwartaal'];
  $tmpKwartaal++;
  if ($tmpKwartaal == 5)
  {
    $tmpKwartaal = 1;
    $jaar++;
  }
  $kwartaal = $kwartalen[$tmpKwartaal];
  $totjulVooraf=$totjul+86400*15; //
  $totMaandVooraf = vertaalTekst($this->pdf->__appvar["Maanden"][date("n", $totjulVooraf)], $this->pdf->rapport_taal) . " " . date("Y", $totjulVooraf);
  $feeTxt.=$totMaandVooraf;
}
else
{
  $kwartaal = $kwartalen[$this->waarden['kwartaal']];
  $feeTxt.=$totMaand;
}




$this->pdf->SetAligns(array('L', 'L','R','R'));
$this->pdf->row(array('',
                  'Waarde portefeuille per ultimo '.$totDatum.' € '. $this->formatGetal($this->waarden['portefeuilleVerdeling']['eindWaarde']['totaal'], 2)));

$this->pdf->ln(8);
$this->pdf->SetWidths(array($extraMarge, 55, 8, 20));
$this->pdf->row(array('',
                  $feeTxt,
                  '€', $this->formatGetal($this->waarden['beheerfeeBetalenIncl'], 2)));
//$lijnx2 = 210 - 38;
$this->pdf->row(array('', 'Waarvan btw ' . $this->formatGetal($this->waarden['btwTarief'], 2) . '%', '€', $this->formatGetal($this->waarden['btw'], 2)));
//$this->pdf->Line($this->pdf->marge+$extraMarge+60, $this->pdf->GetY() + 1, $this->pdf->marge+$extraMarge+60+12+20, $this->pdf->GetY() + 1);
$this->pdf->ln();
$this->pdf->ln();
$this->pdf->SetAligns(array('L', 'L'));
$this->pdf->SetWidths(array($extraMarge, 150));
//$this->pdf->SetY(235);
$this->pdf->row(array('', "Conform afspraak zal bovenstaand bedrag automatisch geïncasseerd worden in de eerste week na afloop van de betreffende periode.


Met vriendelijke groet,"));
$this->pdf->ln();
$this->pdf->ln();
$this->pdf->SetFont($font, "B", $fontsize);
$this->pdf->row(array('', "Het team van Dexxi"));
$this->pdf->SetFont($font, "I", $fontsize);
$this->pdf->ln(2);
$this->pdf->row(array('', "# Iedereen is een Belegger"));

$this->pdf->AutoPageBreak = false;
$this->pdf->SetY(297 - 19);

//$this->pdf->Cell(210, 5, '' . $vermData['Naam'] . ' – ' . $vermData['Adres'] . ' – ' . $vermData['Woonplaats'] . '', 0, 1, 'C');
//$this->pdf->Cell(210, 5, 'Tel: ' . $vermData['Telefoon'] . ' – ' . $vermData['Email'] . ' – ' . $vermData['website'] . '', 0, 1, 'C');
$this->pdf->SetTextColor(128);
$this->pdf->SetFont($this->pdf->rapport_font, 'B', 8.75);
$this->pdf->Cell($this->pdf->w-$this->pdf->marge*2, 4, 'Dexxi is een handelsnaam van de Indexus Groep B.V.', 0, 1, 'C');
$this->pdf->SetFont($this->pdf->rapport_font, '',8.75);
$this->pdf->Cell($this->pdf->w-$this->pdf->marge*2, 4, str_replace(',','',$vermData['Adres']). ' '.$vermData['Woonplaats'].' | Tel. '.$vermData['Telefoon'].' | ' . $vermData['Email'] . '| KvK 34302845 | BTW nr. NL8195.24.463B01', 0, 1, 'C');
$this->pdf->Cell($this->pdf->w-$this->pdf->marge*2, 4, 'Geregistreed bij de AFM en deelnemende instelling DSI', 0, 1, 'C');
$this->pdf->SetTextColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
$this->pdf->Cell($this->pdf->w-$this->pdf->marge*2, 4, $vermData['website'], 0, 1, 'C');
$this->pdf->SetTextColor(0);

/*
$this->pdf->SetFont($this->pdf->rapport_font, 'B', 9);
$woorden=array(array('tekst'=>'ADRES','style'=>'b'),array('tekst'=>$vermData['Adres'].', '.$vermData['Woonplaats'],'style'=>''),
  array('tekst'=>'TEL.','style'=>'b'),array('tekst'=>$vermData['Telefoon'],'style'=>''),
  array('tekst'=>'KVK','style'=>'b'),array('tekst'=>'5567.5107','style'=>''),
  array('tekst'=>'IBAN','style'=>'b'),array('tekst'=>'NL13.ABNA.040.26.37.917','style'=>''),
  array('tekst'=>'BTW NR','style'=>'b'),array('tekst'=>'851813264.B01','style'=>''));
$this->pdf->SetTextColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
foreach($woorden as $woordData)
{
  //function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
  if($woordData['style']=='b')
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', 9);
  else
    $this->pdf->SetFont($this->pdf->rapport_font, '', 9);
  $w=$this->pdf->GetStringWidth($woordData['tekst'].' ');
  $this->pdf->cell($w,5,$woordData['tekst'].' ',0,0,'L');
}
*/


$this->pdf->AutoPageBreak=true;

/*
*/

/*
    $stringWidthVoor=$this->pdf->GetStringWidth('E info@sequoiabeheer.nl - ');
    $stringWidthAchter=$this->pdf->GetStringWidth(' - Rabobank NL66RABO0355054272 - KvK 09112027 - BTW nr. NL.8088.19.008.B01');
    $this->pdf->SetTextColor(0,170,236);
    $spacesVoor=round(($stringWidthVoor)/($this->pdf->CurrentFont['cw'][' ']*$this->pdf->FontSize/1000));
    $spacesAchter=round(($stringWidthAchter)/($this->pdf->CurrentFont['cw'][' ']*$this->pdf->FontSize/1000));
    $this->pdf->Ln(-4);
    $this->pdf->Row(array(str_repeat(' ',$spacesVoor).'www.sequoiabeheer.nl'.str_repeat(' ',$spacesAchter)));

*/


$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
$this->pdf->AutoPageBreak = true;
$this->pdf->SetTextColor(0, 0, 0);

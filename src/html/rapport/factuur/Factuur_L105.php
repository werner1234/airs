<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/01/27 17:30:20 $
File Versie					: $Revision: 1.6 $

$Log: Factuur_L92.php,v $

*/


global $__appvar;
$this->pdf->rapport_type = "FACTUUR";

/*
if (file_exists(FPDF_FONTPATH . 'Frutiger.php1'))
{
  if (!isset($this->pdf->fonts['frutiger']))
  {
    $this->pdf->AddFont('frutiger', '', 'Frutigerl.php');
    $this->pdf->AddFont('frutiger', 'B', 'Frutigerb.php');
    $this->pdf->AddFont('frutiger', 'R', 'Frutiger.php');
    $this->pdf->AddFont('frutiger', 'BI', 'Frutigerbi.php');
  }
  $font = 'frutiger';
}
else
{
  $font = 'Times';
}
*/
$font = $this->pdf->rapport_font;

$fontsize = 11;

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
  $logoYpos = 20;
  $xSize = 60;
  $this->pdf->Image($this->pdf->rapport_logo, $this->pdf->w-20-$xSize, $logoYpos, $xSize);
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
CRM_naw.verzendAanhef,
Portefeuilles.BetalingsinfoMee
FROM CRM_naw Join Portefeuilles on CRM_naw.Portefeuille= Portefeuilles.portefeuille WHERE CRM_naw.Portefeuille = '" . $this->portefeuille . "'  ";
$this->DB->SQL($query);
$crmData = $this->DB->lookupRecord();

$extraMarge = 25 - $this->pdf->marge;
$this->pdf->SetY(40);
$this->pdf->SetWidths(array($extraMarge, $this->pdf->w-50));
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
  $plaats .= "    " . $crmData['plaats'];
}
$this->pdf->row(array('', $plaats));
$this->pdf->ln(1);
$this->pdf->row(array('', $crmData['land']));

$this->pdf->SetY(105);


$this->pdf->row(array("", "Alphen aan den Rijn, ". date("j")." ".$this->__appvar["Maanden"][date("n")]." ".date("Y")));
$this->pdf->ln();
$this->pdf->row(array('', "Factuurnummer: B" . date('Y') . '' . sprintf("%05d", $this->waarden['factuurNummer'])));
$this->pdf->ln(6);
$this->pdf->row(array('', $crmData['verzendAanhef']));
$this->pdf->ln(2);
$this->pdf->row(array('', "Hierbij onze factuur met betrekking tot:"));
$this->pdf->ln();
$this->pdf->fillCell=array(0,1,1,1);
$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
$this->pdf->SetWidths(array($extraMarge, 125, 12,20));
$this->pdf->row(array('', "Omschrijving",'','Bedrag'));
$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
unset($this->pdf->fillCell);

$this->pdf->SetWidths(array($extraMarge, 125, 12, 20));
$lijnx2 = $this->pdf->marge+$extraMarge+125+12+20;
$kwartalen = array('null', '1ste', '2e', '3e', '4e');
$jaar = date('Y', $totjul);
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
}
else
{
  $kwartaal = $kwartalen[$this->waarden['kwartaal']];
}

$this->pdf->SetAligns(array("L", "L", "L", "R"));
$this->pdf->ln();
//$this->pdf->row(array('',
//                  '  Portefeuillewaarde € '.$this->formatGetal($this->waarden['basisRekenvermogen'],2).'',
//                  ($this->waarden['BeheerfeePercentageVermogenDeelVanJaar']<>0?'  Beheerfee '.$this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'],3).'%':'').' '));
$this->pdf->row(array('','Vermogensbeheer ' . $kwartaal . ' kwartaal ' . $jaar,'€', $this->formatGetal($this->waarden['beheerfeePerPeriode'] - $this->waarden['administratieBedrag'], 2)));
if($this->waarden['administratieBedrag']<>0)
{
  $this->pdf->row(array('','Kosten','€',$this->formatGetal($this->waarden['administratieBedrag'], 2)));
}

$this->pdf->ln(8);
$this->pdf->row(array('','Bedrag excl B.T.W.','€', $this->formatGetal($this->waarden['beheerfeePerPeriode'], 2)));
$this->pdf->row(array('', 'B.T.W ' . $this->formatGetal($this->waarden['btwTarief'], 0) . '% ', '€', $this->formatGetal($this->waarden['btw'], 2)));
$this->pdf->ln(8);
$this->pdf->SetFont($font, "B", $fontsize);
$this->pdf->row(array('', 'Bedrag incl. B.T.W.', '€', $this->formatGetal($this->waarden['beheerfeeBetalenIncl'], 2)));
$this->pdf->Line($this->pdf->marge+$extraMarge, $this->pdf->GetY() + 1, $lijnx2, $this->pdf->GetY() + 1);
$this->pdf->SetFont($font, "", $fontsize);
$this->pdf->ln(2);
$this->pdf->SetAligns(array('L', 'L'));

/*
if($this->waarden['BeheerfeeBedragBuitenFee']<>0)
{
  $this->pdf->SetFont($font, "", $fontsize);
  $this->pdf->row(array('', "Bij de berekening van de beheerfee is rekening gehouden met de speciale instructies die met u zijn afgesproken."));
  $this->pdf->row(array('', "Een bedrag van € ".$this->formatGetal($this->waarden['BeheerfeeBedragBuitenFee'], 2)." is niet meegenomen in de berekening van de beheerfee."));
  $this->pdf->SetFont($font, "", $fontsize);
}
*/

$this->pdf->SetWidths(array($extraMarge, $this->pdf->w-50));
$this->pdf->ln(20);

//9805 B onderdrukken wanneer deze voorkomt in het rekeningnummer
if ( $this->waarden['rekeningEur'][0] === 'B' ) {
  $this->waarden['rekeningEur'] = substr($this->waarden['rekeningEur'], 1);
}

$this->pdf->row(array('', "Het bedrag wordt automatisch van beleggingsrekening met nummer ".$this->waarden['rekeningEur'].' afgeschreven.'));
$this->pdf->ln(4);

$this->pdf->row(array('', "Hartelijke groet,

Namens EBI Capital

Ing. J. de Vries RBA
Alg. directeur
"));

$this->pdf->SetWidths(array(10, $this->pdf->w-20));
$this->pdf->AutoPageBreak = false;
$this->pdf->SetY(297 - 19);
$this->pdf->SetFont($this->pdf->rapport_font, '', 8);
$this->pdf->row(array('', "EBI Capital Partners is een handelsnaam van De Vries Investment Services B.V.| EBI Capital© is een merknaam van De Vries Investment Services B.V.
Bezoekadres : Dominicanenlaan 2 | 2408 KH | Alphen aan den Rijn | Correspondentie : Postbus 196, 2400 AD Alphen aan den Rijn
E-mail : info@ebicapital.nl | K.v.K : 28106889 | BTW nr. : 1832.58.617 B01 |  | Vergunningsnummer AFM : 14003123
"));
/*
$this->pdf->Cell(210, 5, '' . $vermData['Naam'] . ' · ' . $vermData['Adres'] . ' · ' . $vermData['Woonplaats'] . ' · ' . $vermData['Telefoon'] . ' · ' . $vermData['Email'] . ' · ' . $vermData['website'] . '', 0, 1, 'C');
//$this->pdf->Cell(210, 5, 'Tel: ' . $vermData['Telefoon'] . ' – ' . $vermData['Email'] . ' – ' . $vermData['website'] . '', 0, 1, 'C');
$this->pdf->Cell(210, 5, 'KVK nr. 34364453 · BTW nr: 821476531B01', 0, 1, 'C');
*/
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
$this->pdf->AutoPageBreak = true;
$this->pdf->SetTextColor(0, 0, 0);

$this->pdf->nextFactuur=true;
$this->pdf->frontPage=true;

?>
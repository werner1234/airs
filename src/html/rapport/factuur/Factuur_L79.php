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

$fontsize = 9;

$this->pdf->AddPage('P');
$this->pdf->nextFactuur = true;
$extraMarge = 25 - $this->pdf->marge;

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
  $this->pdf->Image($this->pdf->rapport_logo, $extraMarge+8, $logoYpos, $xSize);
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
CRM_naw.email,
Portefeuilles.BetalingsinfoMee
FROM CRM_naw Join Portefeuilles on CRM_naw.Portefeuille= Portefeuilles.portefeuille WHERE CRM_naw.Portefeuille = '" . $this->portefeuille . "'  ";
$this->DB->SQL($query);
$crmData = $this->DB->lookupRecord();

$this->pdf->SetY(55);
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

$this->pdf->ln(5);
$this->pdf->SetFont($font, "I", $fontsize);

$this->pdf->row(array('', 'Uitsluitend per email verzonden op:'));
$this->pdf->SetFont($font, "U", $fontsize);

$this->pdf->row(array('', $crmData['email']));

$this->pdf->SetFont($font, "", $fontsize);
$this->pdf->Cell($this->pdf->w-$this->pdf->marge-$extraMarge, 4,'Amsterdam,           ' . date('d') . ' '. $this->__appvar["Maanden"][date("n")] . ' ' . date('Y') , 0,1, "R");

$this->pdf->SetY(105);
$this->pdf->SetWidths(array($extraMarge, 125, 12, 20));
$lijnx2 = $this->pdf->marge+$extraMarge+125+12+20;

$this->pdf->row(array('', 'Hiermee brengen wij u in rekening:'));
$this->pdf->ln(6);


$this->pdf->Cell($this->pdf->w-$this->pdf->marge-4, 4,'FACTUUR', 0,1, "C");
$this->pdf->ln(4);
$this->pdf->Line($this->pdf->marge+$extraMarge, $this->pdf->GetY() , $lijnx2, $this->pdf->GetY());

$this->pdf->SetWidths(array($extraMarge,50,57,50));
$this->pdf->SetAligns(array("L", "L", "L", "R"));
//$this->pdf->CellBorders = array('', 'T', 'T', 'T');
$this->pdf->ln(2);
$this->pdf->row(array('', 'Factuurnummer:','' . date('Y') . '' . sprintf("%05d", $this->waarden['factuurNummer']), 'EUR'));
$this->pdf->ln(5);


$this->pdf->CellBorders = array('', '', '', '');



$kwartalen = array('null', 'eerste', 'tweede', 'derde', 'vierde');
$jaar = date('Y', $totjul);

$currentMonth = date('n');
$currentQuarter = ceil($currentMonth / 3);

$kwartaal = $kwartalen[$currentQuarter];

$this->pdf->SetWidths(array($extraMarge,120,12,25));
$this->pdf->SetAligns(array("L", "L", "L", "R"));
$this->pdf->ln();

$kortingFactor=1;
if($this->waarden['BeheerfeeKortingspercentage']<>0)
{
  $kortingFactor=1-$this->waarden['BeheerfeeKortingspercentage']/100;
}
elseif($this->waarden['BeheerfeeBedragBuitenFee']<>0)
{
  $kortingFactor=1-($this->waarden['BeheerfeeBedragBuitenFee'] / $this->waarden['basisRekenvermogen']);
}

$feeTotaal=$this->waarden['beheerfeePerPeriode'] - $this->waarden['administratieBedrag'];
$fee=($feeTotaal)/$kortingFactor;

$this->pdf->row(array('','Onze vergoeding voor het vermogensbeheer in het  ' . $kwartaal . ' kwartaal ' . $jaar,'',  $this->formatGetal($fee, 2)));// - $this->waarden['administratieBedrag']
$this->pdf->ln(5);

$this->pdf->SetWidths(array($extraMarge,70,25,38,25));
$this->pdf->SetAligns(array("L", "L", "R", "R", "R"));
$this->pdf->row(array('','Het voor u beheerde vermogen per heden:',$this->formatGetal($this->waarden['rekenvermogen'],2)) );
$this->pdf->row(array('','Maal het beheertarief per jaar:',$this->formatGetal($this->waarden['BeheerfeePercentageVermogen'],2). '%')  );
$this->pdf->row(array('','Aantal dagen:', 360/$this->waarden['BeheerfeeAantalFacturen'] . '/360') );

if ( $this->waarden['BeheerfeeBedragBuitenFee'] <> 0 ) {
  $this->pdf->SetWidths(array($extraMarge,120,12,25));
  $this->pdf->SetAligns(array("L", "L", "L", "R"));
  $this->pdf->row(array('',$this->waarden['FactuurMemo'], '',$this->formatGetal($feeTotaal-$fee, 2) ) );
}

$this->pdf->SetWidths(array($extraMarge,70,25,37,25));
$this->pdf->SetAligns(array("L", "L", "R", "R", "R"));

$this->pdf->row(array('', 'BTW ',$this->formatGetal($this->waarden['btwTarief'], 0) . '% ', '', $this->formatGetal($this->waarden['btw'], 2)));

$this->pdf->ln(8);
$this->pdf->Line($this->pdf->marge+$extraMarge + 130, $this->pdf->GetY()-3 , $lijnx2, $this->pdf->GetY()-3);
$this->pdf->row(array('', 'Totaal ', '', '',$this->formatGetal($this->waarden['beheerfeeBetalenIncl'], 2)));
$this->pdf->Line($this->pdf->marge+$extraMarge + 130, $this->pdf->GetY()+3 , $lijnx2, $this->pdf->GetY()+3);
$this->pdf->SetFont($font, "", $fontsize);
$this->pdf->ln(2);



$this->pdf->SetAligns(array('L', 'L'));
$this->pdf->SetWidths(array($extraMarge, $this->pdf->w-50));
$this->pdf->ln(20);


$this->pdf->row(array('', "Deze vergoeding dient komende dagen te worden voldaan op IBAN: NL57 ABNA 0246 3566 42,
ten name van Janivo Vermogensbeheer B.V."));
$this->pdf->ln(4);

if ( (int) $this->waarden['BetalingsinfoMee'] === 0 ) {
  $this->pdf->row(array('', "Overeenkomstig gemaakte afspraken zullen wij komende dagen uw rekening beheerd door
Janivo Vermogensbeheer B.V. voor deze kosten belasten."));
}
$this->pdf->AutoPageBreak=false;
$this->pdf->SetY(297-30);
$this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize-2);

$this->pdf->SetTextColor(49,133,157);
$this->pdf->SetAligns(array('L', 'L', 'L', 'L', 'L'));
$this->pdf->SetWidths(array($extraMarge, 40,40,40,40));

$this->pdf->row(array('', 'Postbus 7933', 'De Boelelaan 7', 'IBAN:', 'KvK nr: 683 67 155'));
$this->pdf->row(array('', '1008 AC Amsterdam', '1083 HJ Amsterdam ', 'NL57ABNA0246356642', 'BTW nr: 8574.13.752.B.01'));


?>
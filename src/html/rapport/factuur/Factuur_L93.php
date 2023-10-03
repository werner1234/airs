<?php

global $__appvar;
$this->pdf->rapport_type = "FACTUUR";


if (file_exists(FPDF_FONTPATH . 'Frutiger.php'))
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
$fontsize = 9;

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
  $xSize = 50;
  
  $this->pdf->Image($this->pdf->rapport_logo, 210 - $xSize - $logoYpos, $logoYpos, $xSize);
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
Portefeuilles.BetalingsinfoMee
FROM CRM_naw Join Portefeuilles on CRM_naw.Portefeuille= Portefeuilles.portefeuille WHERE CRM_naw.Portefeuille = '" . $this->portefeuille . "'  ";
$this->DB->SQL($query);
$crmData = $this->DB->lookupRecord();

$extraMarge = 25 - $this->pdf->marge;
$this->pdf->SetY(55 - 8);
$this->pdf->SetWidths(array($extraMarge, 100, 80));
$this->pdf->SetFont($font, "", 10);
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
$this->pdf->SetFont($font, "B", 12);
$this->pdf->row(array('', "FACTUUR"));
$this->pdf->SetFont($font, "", 10);
$this->pdf->ln(2);

$this->pdf->SetWidths(array($extraMarge, 30, 80));
$this->pdf->row(array('', 'Factuurnummer:', date('Y') . sprintf("%03d", $this->waarden['factuurNummer'])));
$this->pdf->row(array('', 'Factuurdatum:', $nu));
$this->pdf->row(array('', 'Portefeuille:', $this->portefeuille));

$this->pdf->SetY(135);
$this->pdf->SetWidths(array($extraMarge, 100, 12, 30));

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

$this->pdf->SetWidths(array($extraMarge, 40, 30));
$this->pdf->SetAligns(array("L", "L", "R"));
if($this->waarden['BeheerfeeBasisberekening']==5)
{
  $this->pdf->SetFont($font, "B", 10);
  $this->pdf->row(array('','Waarde per'));
  $this->pdf->SetFont($font, "", 10);
  for ($i = 1; $i < 5; $i++)
  {
    $this->pdf->row(array('', date('d-m-Y', $this->waarden['maandsData_' . $i]), $this->formatGetal($this->waarden['maandsWaarde_' . $i], 2)));
  }
  $this->pdf->ln(1);
  $this->pdf->CellBorders = array('', '', 'T');
  $this->pdf->row(array('', '', ''));
  $this->pdf->CellBorders = array();
  $this->pdf->ln(-3);
  $this->pdf->row(array('', '', $this->formatGetal($this->waarden['maandsGemiddelde'], 2)));
}
else
{
  $this->pdf->SetWidths(array($extraMarge, 60, 30));
  $this->pdf->row(array('', 'Vermogen voor feeberekening', $this->formatGetal($this->waarden['rekenvermogen'], 2)));
}

if($this->waarden['BeheerfeeBedragBuitenFee']<>0)
{
  $this->pdf->CellBorders = array();
  $this->pdf->row(array('', 'Uitgesloten van fee', $this->formatGetal($this->waarden['BeheerfeeBedragBuitenFee'], 2)));
  $this->pdf->ln(1);
  $this->pdf->CellBorders = array('','', 'T');
  $this->pdf->row(array('','',''));
  $this->pdf->CellBorders = array();
  $this->pdf->ln(-3);
  $this->pdf->row(array('', '', $this->formatGetal($this->waarden['rekenvermogen'], 2)));
}
$this->pdf->ln();
$this->pdf->CellBorders = array();

if($this->waarden['BeheerfeeMethode']==3)
{
  //vast percentage over bedrag
  //
  $this->pdf->row(array('', 'Feepercentage per jaar', $this->formatGetal($this->waarden['BeheerfeePercentageVermogen'], 2).'%'));
}
elseif($this->waarden['BeheerfeeMethode']==1||$this->waarden['BeheerfeeMethode']==2) //1=staffels 2=staffels+korting
{
  $this->pdf->SetAligns(array("L", "L", "L", "L"));
  
  $this->pdf->SetFont($font, "B", 10);
  $this->pdf->SetWidths(array($extraMarge, 35+ 30+30));
  $this->pdf->row(array('', 'Gehanteerde staffel beheerfee'));
  $this->pdf->SetWidths(array($extraMarge, 35, 30,30));
  $this->pdf->row(array('', 'Min.','Max.','Feeperc.'));
  $this->pdf->SetFont($font, "", 10);
  foreach($this->waarden['staffelWaarden'] as $i=>$staffeldata)//for ($i=1;$i<5;$i++)
  {
    if(!isset($min))
      $min='-';
    $this->pdf->row(array('', $min, $this->formatGetal($this->waarden['portefeuilledata']['BeheerfeeStaffel' . $i], 0),
                      $this->formatGetal($this->waarden['portefeuilledata']['BeheerfeeStaffelPercentage' . $i], 2).'%'));
    $min=$this->formatGetal($this->waarden['portefeuilledata']['BeheerfeeStaffel' . $i],0);
  }
  if($this->waarden['BeheerfeeKortingspercentage'] <> 0)
  {
    $this->pdf->ln();
    $this->pdf->row(array('', 'Kortingspercentage','',$this->formatGetal($this->waarden['BeheerfeeKortingspercentage'], 2).'%'));
  }
}
$this->pdf->ln(12);
//$this->waarden['BeheerfeeBedragBuitenFee']
//$this->waarden['rekenvermogen']
$this->pdf->SetAligns(array("L", "L", "L", "R"));
$this->pdf->SetWidths(array($extraMarge, 100, 12, 30));
$this->pdf->row(array('',
                  'Vaste service fee ' . $kwartaal . ' kwartaal ' . $jaar,
                  'EUR', $this->formatGetal($this->waarden['beheerfeePerPeriode'] - $this->waarden['administratieBedrag'], 2)));

if($this->waarden['administratieBedrag']<>0)
{
  $this->pdf->row(array('',
                    'Toezichthouderskosten DNB/AFM',
                    '-',
                    $this->formatGetal($this->waarden['administratieBedrag'], 2)));
  $this->pdf->Line($this->pdf->marge+$extraMarge+100, $this->pdf->GetY() + 1, $this->pdf->marge+$extraMarge+100+12+30, $this->pdf->GetY() + 1);
  $this->pdf->ln(2);
  $this->pdf->row(array('',
                    '',
                    '-',
                    $this->formatGetal($this->waarden['beheerfeePerPeriode'], 2)));
  
}

$lijnx2 = 210 - 38;
$this->pdf->ln(2);
$this->pdf->row(array('', 'BTW ' . $this->formatGetal($this->waarden['btwTarief'], 2) . '%', '-', $this->formatGetal($this->waarden['btw'], 2)));
$this->pdf->Line($this->pdf->marge+$extraMarge+100, $this->pdf->GetY() + 1, $this->pdf->marge+$extraMarge+100+12+30, $this->pdf->GetY() + 1);
$this->pdf->ln(2);
$this->pdf->row(array('', 'TOTAAL', 'EUR', $this->formatGetal($this->waarden['beheerfeeBetalenIncl'], 2)));
$this->pdf->ln(20);
$this->pdf->SetAligns(array('L', 'L'));
$this->pdf->SetWidths(array($extraMarge, 150));
$this->pdf->row(array('', "Gaarne de factuur betalen door overmaking op ondergenoemde rekening:

NL78ABNA0877268223 t.n.v. Beaufort Investments"));
$this->pdf->SetY(235);
$this->pdf->ln(3);

  $this->pdf->SetFont($font, "", $fontsize);
  
$this->pdf->SetWidths(array($extraMarge, 50,50,50));
$this->pdf->SetAligns(array("L", "L", "L", "L"));

$this->pdf->row(array('', $vermData['Naam'], $vermData['Telefoon'],'NL78ABNA0877268223'));
$this->pdf->row(array('', $vermData['Adres'],'+31 6 36482806','KvK 77808169'));
$this->pdf->row(array('', $vermData['Woonplaats'],$vermData['Email'],'BTW nr: NL861153960B01'));

/*

$this->pdf->row(array('', 'Tel: ' . $vermData['Telefoon']));
  $this->pdf->row(array('', $vermData['Email']));
  $this->pdf->row(array('', $vermData['website']));
  $this->pdf->row(array('', 'KVK nr. 77808169'));
  $this->pdf->row(array('', 'IBAN NL78ABNA0877 2682 23'));
  $this->pdf->row(array('', 'BTW nr: NL861153960B01'));

$this->pdf->AutoPageBreak = false;
$this->pdf->SetY(297 - 19);


$this->pdf->Cell(210, 5, '' . $vermData['Naam'] . ' – ' . $vermData['Adres'] . ' – ' . $vermData['Woonplaats'] . '', 0, 1, 'C');
$this->pdf->Cell(210, 5, 'Tel: ' . $vermData['Telefoon'] . ' – ' . $vermData['Email'] . ' – ' . $vermData['website'] . '', 0, 1, 'C');
$this->pdf->Cell(210, 5, 'KVK nr. XXX – BTW nr: XXX', 0, 1, 'C');

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



$this->pdf->AutoPageBreak=true;
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

?>
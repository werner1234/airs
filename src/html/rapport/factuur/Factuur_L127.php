<?php

global $__appvar;
$this->pdf->rapport_type = "FACTUUR";

$this->pdf->AddPage('P');
$this->pdf->nextFactuur = true;
$rowHeightBackup=$this->pdf->rowHeight;

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
  $logoYpos = 10;
  $xSize = 50;
  $this->pdf->Image($this->pdf->rapport_logo, 210/2 - $xSize/2, $logoYpos, $xSize);
  $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
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

$query="SELECT Handtekening FROM Accountmanagers WHERE Accountmanager='KBR'";
$this->DB->SQL($query);
$acc = $this->DB->lookupRecord();
if($acc['Handtekening']<>'')
{
  $accImage=base64_decode($acc['Handtekening']);

}

$extraMarge = 25 - $this->pdf->marge;
$this->pdf->SetWidths(array($extraMarge, 110, 80));
$this->pdf->ln(2);

$this->pdf->SetY(60);
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize+2);
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


$this->pdf->SetY(120);
$this->pdf->SetWidths(array($extraMarge, 35, 80));
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize+2);
$this->pdf->row(array('', 'Factuurnummer:', $this->portefeuille.'/'.date('Y',$totjul) . sprintf("%04d", $this->waarden['factuurNummer'])));
$this->pdf->ln(1);
$this->pdf->row(array('', 'Datum:', $nu));
//$this->pdf->ln(1);
//$this->pdf->row(array('', 'Portefeuille:', $this->portefeuille));
$this->pdf->ln(1);
$this->pdf->SetY(145);
$this->pdf->SetWidths(array($extraMarge, 110, 40));

$kwartalen = array('null', '1ste', '2e', '3e', '4e');
$jaar = date('Y', $totjul);
$maand=date("n", $totjul);
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
  $maand++;
  if($maand>12)
    $maand=1;
 
}
else
{
  $kwartaal = $kwartalen[$this->waarden['kwartaal']];
}

$fee=$this->waarden['basisRekenvermogen']* $this->waarden['BeheerfeePercentageVermogen']* (1+$this->waarden['btwTarief']/100)/$this->waarden['BeheerfeeAantalFacturen']/100;
$feeProc=$this->waarden['BeheerfeePercentageVermogen']* (1+$this->waarden['btwTarief']/100)/$this->waarden['BeheerfeeAantalFacturen'];


$factuurmaand=vertaalTekst($this->pdf->__appvar["Maanden"][$maand], $this->pdf->rapport_taal);

if(count($this->waarden['extraFactuurregels']['regels'])>0)
{
  $extraRegelWaarde = $this->waarden['extraFactuurregels']['zonderBTW'] + $this->waarden['extraFactuurregels']['metBTW'];
  $extraRegelWaardeZonderBtw=$this->waarden['extraFactuurregels']['zonderBTW'];
}
else
{
  $extraRegelWaarde = 0;
  $extraRegelWaardeZonderBtw = 0;
}
  $this->pdf->line($this->pdf->marge+$extraMarge,$this->pdf->getY()-4,$this->pdf->marge+$extraMarge+152,$this->pdf->getY()-4);
$this->pdf->SetWidths(array($extraMarge, 100+ 12+ 30+20));

$this->pdf->row(array('',"Hierbij brengen wij de beheerfee voor Dex over de maand $factuurmaand in rekening."));
$this->pdf->ln();
$this->pdf->SetWidths(array($extraMarge, 100, 12, 30));


//$this->pdf->row(array('',"Vermogen onder beheer per $vanjul","€",$this->formatGetal($this->waarden['totaalWaardeVanaf'],2)));
$this->pdf->row(array('',"Vermogen onder beheer per $totDatum","€",$this->formatGetal($this->waarden['totaalWaarde'],2)));
$this->pdf->ln();


$this->pdf->SetWidths(array($extraMarge, 100+12+30+20));
$this->pdf->row(array('',"Beheer fee € 1,00 per maand + 1/".$this->waarden['BeheerfeeAantalFacturen']." van ".$this->formatGetal($this->waarden['BeheerfeePercentageVermogen']*1.21,2) ."%, inclusief BTW, over het vermogen"));
$this->pdf->ln();
$this->pdf->SetWidths(array($extraMarge, 100, 12, 30));

//$this->pdf->row(array('','Beheer fee '.$factuurmaand,
//                  '€', $this->formatGetal(($this->waarden['beheerfeePerPeriode'] - $this->waarden['administratieBedrag']), 2)));

$this->pdf->row(array('','Beheer fee '.$factuurmaand,
                  '€', $this->formatGetal(($this->waarden['beheerfeePerPeriode'] - $this->waarden['administratieBedrag'])  , 2)));

//listarray($this->waarden);



//$this->pdf->row(array('','Beheer fee '.$factuurmaand.' '.$this->formatGetal($feeProc,4).'%',
//                  '€', $this->formatGetal($fee, 2)));


if($this->waarden['administratieBedrag']<>0)
{
  $this->pdf->row(array('',
                    'Administratiekosten',
                    '-',
                    $this->formatGetal($this->waarden['administratieBedrag'], 2)));
  $this->pdf->Line($this->pdf->marge+$extraMarge+100, $this->pdf->GetY() + 1, $this->pdf->marge+$extraMarge+100+12+30, $this->pdf->GetY() + 1);
  $this->pdf->ln();
  $this->pdf->row(array('',
                    '',
                    '-',
                    $this->formatGetal($this->waarden['beheerfeePerPeriode'], 2)));
  
}

$lijnx2 = 210 - 38;
$this->pdf->ln();
$this->pdf->row(array('', 'BTW ' . $this->formatGetal($this->waarden['btwTarief'], 2) . '%', '-', $this->formatGetal($this->waarden['btw'], 2)));
$this->pdf->ln();
$this->pdf->row(array('', 'TOTAAL', '€', $this->formatGetal($this->waarden['beheerfeeBetalenIncl'], 2)));
$this->pdf->ln(2);
$this->pdf->SetAligns(array('L', 'L'));
$this->pdf->SetWidths(array($extraMarge, 150));
$this->pdf->SetY(210);
$this->pdf->row(array('', "Dit bedrag zal een dezer dagen automatisch van de beleggingsrekening van Dex worden afgeschreven."));
$this->pdf->ln(8);
$this->pdf->row(array('','Met vriendelijke groet,'));
if(strlen($accImage)>10)
  $this->pdf->memImage($accImage,$this->pdf->marge+$extraMarge,$this->pdf->getY(),60);
$this->pdf->ln(20);
$this->pdf->row(array('','Team Pensioenpotje.nl'));



$this->pdf->AutoPageBreak = false;
$this->pdf->SetY(297 - 25);

/*
$this->pdf->Cell(210, 5, '' . $vermData['Naam'] . ' – ' . $vermData['Adres'] . ' – ' . $vermData['Woonplaats'] . '', 0, 1, 'C');
$this->pdf->Cell(210, 5, 'Tel: ' . $vermData['Telefoon'] . ' – ' . $vermData['Email'] . ' – ' . $vermData['website'] . '', 0, 1, 'C');
$this->pdf->Cell(210, 5, 'KVK nr. XXX – BTW nr: XXX', 0, 1, 'C');
*/

$this->pdf->SetAligns(array('L', 'L'));
$this->pdf->SetWidths(array(10, 20+ 60, 15, 80));
//$this->pdf->SetTextColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
$this->pdf->SetFont($this->pdf->rapport_font, 'B', 8);
$this->pdf->row(array('','','Adres',''));
$this->pdf->ln(-4);
$this->pdf->SetFont($this->pdf->rapport_font, '', 8);
  $this->pdf->row(array('','Pensioenpotje.nl is een handelsnaam van Mercurius Vermogensbeheer B.V.','','Nassaulaan 19, 2514 JT ’s-Gravenhage'));
$this->pdf->SetWidths(array(10, 15, 65, 15, 35, 12, 35));
  $this->pdf->SetFont($this->pdf->rapport_font, 'B', 8);
 
  $this->pdf->row(array('','Telefoon','','E-mail','','Bank',''));
$this->pdf->ln(-4);
$this->pdf->SetFont($this->pdf->rapport_font, '', 8);
$this->pdf->row(array('','','+31(0)85 105 01 02 ','','EMAIL-PEN','','NL89ABNA0501484108'));
$this->pdf->SetFont($this->pdf->rapport_font, 'B', 8);
$this->pdf->row(array('','KvK','','Internet','','BTW',''));
$this->pdf->ln(-4);
$this->pdf->SetFont($this->pdf->rapport_font, '', 8);
$this->pdf->row(array('','','KVKXXXXX','','Pensioenpotje.nl','','NL 818181709B01'));





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

$this->pdf->rowHeight=$rowHeightBackup;

$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
$this->pdf->AutoPageBreak = true;
$this->pdf->SetTextColor(0, 0, 0);

?>
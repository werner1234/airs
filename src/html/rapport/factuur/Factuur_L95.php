<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/01/27 17:30:20 $
File Versie					: $Revision: 1.6 $

$Log: Factuur_L92.php,v $

*/


global $__appvar;
$this->pdf->rapport_type = "FACTUUR";


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
  $logoYpos = 10;
  $xSize = 50;
  $this->pdf->Image($this->pdf->rapport_logo, 210 - $xSize - $logoYpos, $logoYpos, $xSize);
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

$extraMarge = 25 - $this->pdf->marge;
$this->pdf->SetWidths(array($extraMarge, 100, 80));
$this->pdf->SetXY($this->pdf->marge,60);
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+5);
$this->pdf->row(array('', "FACTUUR"));

$this->pdf->ln(2);

$this->pdf->SetY(75);
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
$this->pdf->SetWidths(array($extraMarge, 30, 80));
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
$this->pdf->row(array('', 'Kenmerk'));
$this->pdf->ln(1);
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize+2);
$this->pdf->row(array('', 'Datum:', $nu));
$this->pdf->ln(1);
$this->pdf->row(array('', 'Factuurnummer:', 'DH-'.date('Y',$totjul) . sprintf("%04d", $this->waarden['factuurNummer'])));
$this->pdf->ln(1);
$this->pdf->row(array('', 'Portefeuille:', $this->portefeuille));

$this->pdf->SetY(145);
$this->pdf->SetWidths(array($extraMarge, 110, 40));

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


$rowHeightBackup=$this->pdf->rowHeight;
$this->pdf->rowHeight=5;
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
$this->pdf->SetAligns(array("L", "L", "R", "R"));
$this->pdf->row(array('', 'Omschrijving','Bedrag'));
$this->pdf->ln(1);
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize+2);
$yStart=$this->pdf->getY();

$this->pdf->fillCell=array(0,1,1);
$this->pdf->setFillColor(226,239,217);
$this->pdf->setDrawColor(168,208,141);
$this->pdf->CellBorders=array('',array('T','U'),array('T','U'));
$this->pdf->row(array('',
                  'Advies en servicekosten HIP Capital',
                  '€ '. $this->formatGetal($this->waarden['beheerfeePerPeriode']- $this->waarden['administratieBedrag'], 2)));//

if($this->waarden['administratieBedrag']<>0)
{
  if(isset($this->pdf->fillCell))
    unset($this->pdf->fillCell);
  else
    $this->pdf->fillCell=array(0,1,1);
  $this->pdf->row(array('','Administratiekosten','€ '.$this->formatGetal($this->waarden['administratieBedrag'], 2)));
}



if($extraRegelWaarde<>0)
{
  if(isset($this->pdf->fillCell))
    unset($this->pdf->fillCell);
  else
    $this->pdf->fillCell=array(0,1,1);
  $this->pdf->row(array('','Extra kosten','€ '.$this->formatGetal($extraRegelWaarde, 2)));
}


if(isset($this->pdf->fillCell))
  unset($this->pdf->fillCell);
else
  $this->pdf->fillCell=array(0,1,1);

$this->pdf->row(array('','',''));

if(isset($this->pdf->fillCell))
  unset($this->pdf->fillCell);
else
  $this->pdf->fillCell=array(0,1,1);

$this->pdf->row(array('', 'BTW ' . $this->formatGetal($this->waarden['btwTarief'], 2) . '%', '€ '. $this->formatGetal($this->waarden['btw'], 2)));


if(isset($this->pdf->fillCell))
  unset($this->pdf->fillCell);
else
  $this->pdf->fillCell=array(0,1,1);
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
$this->pdf->row(array('', 'Totaal inclusief BTW', '€ '. $this->formatGetal($this->waarden['beheerfeeBetalenIncl'], 2)));
$xPos=$this->pdf->marge+$extraMarge+110;
$this->pdf->line($xPos,$yStart,$xPos,$this->pdf->getY());
$this->pdf->SetAligns(array('L', 'L'));
$this->pdf->SetWidths(array($extraMarge, 150));
$this->pdf->SetY(200);
unset($this->pdf->CellBorders);
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize+2);
if($this->waarden['BetalingsinfoMee']==true)
  $this->pdf->row(array('', "Gelieve het verschuldigde bedrag onder vermelding van factuurnummer binnen 14 dagen over te maken op IBAN: NL22 INGB 0007 8292 70 t.n.v. HIP Capital."));
else
  $this->pdf->row(array('', "Het totaalbedrag wordt conform afspraak automatisch ingehouden en in mindering gebracht op het giraal tegoed bij uw depotbank."));
$this->pdf->ln(3);



$this->pdf->AutoPageBreak = false;
$this->pdf->SetY(297 - 25);

/*
$this->pdf->Cell(210, 5, '' . $vermData['Naam'] . ' – ' . $vermData['Adres'] . ' – ' . $vermData['Woonplaats'] . '', 0, 1, 'C');
$this->pdf->Cell(210, 5, 'Tel: ' . $vermData['Telefoon'] . ' – ' . $vermData['Email'] . ' – ' . $vermData['website'] . '', 0, 1, 'C');
$this->pdf->Cell(210, 5, 'KVK nr. XXX – BTW nr: XXX', 0, 1, 'C');
*/

$icon['loc']=base64_decode('iVBORw0KGgoAAAANSUhEUgAAABEAAAARCAMAAAAMs7fIAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAGBQTFRFt9zHntK22uzi6PPs1Ondy+XVqta9jMmpk8ysd8Ga+fv57fXwgMWh5vHrrtjBls6wxePS4e/nptS78/j1odK28Pfz9/r4weHP+/380ufahsemz+fY/f7+/v79/f79/v7+nTMQxAAAAL9JREFUeNpM0FluxSAMBVBjSCBJS5oBMnnY/y4fgVYqH8g6MtfIoPUwixDVEupNGOe12/4J3NAd+/5d69KuP8HaDu26Tq+ISL4dDsPQ4+yE5H1l52T8Ej2aIxcpA6xJe0AIr+grNN3TdXsPk+lOEWCm0+PojidZg1RFNcJZ/qhPb1VqsuK9FckRNv6VHJ7SsqyuxDRh5zfOEFMBbjIGYNyvk/5yVJYv18eRlYVaj4wwGFvWUeKaMKV4aVvQR4ABAGrKGKjqXpjRAAAAAElFTkSuQmCC');
$icon['kvk']=base64_decode('iVBORw0KGgoAAAANSUhEUgAAABEAAAARCAMAAAAMs7fIAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAGBQTFRFkMqr8/j2bb2V5fLqpdS7yuXVecKd1+reut3Jmc6x3e3kw+LR+/387fXxtdrE6fPt4O/mq9a9z+fZmtG1rdrDjsyt+fv6t93J+vz6gcShweDOhsmn9/r4/v79/f39/v7+YU4smwAAAIBJREFUeNq0z8kSgyAQRdFmVAYFWoUItP7/X0oqu2xTuatXZ/fgur+Cn4VGQ5adQlVVUN+nQqIKiLo7g5vL6lSNFbmA1cG4PEtxrNiiQQ+Wz4bdEdK5oF4loyHadPKSp45cy/4WMCXT1KLHMd0LwiGsjJQVu+wWMAn4w69PjwADADZMGwm5P7k1AAAAAElFTkSuQmCC');
$icon['mail']=base64_decode('iVBORw0KGgoAAAANSUhEUgAAABEAAAARCAMAAAAMs7fIAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAGBQTFRF5/LreMKd0+nc9Pj17fXwwuDNi8emqda8ntC1stnC/Pz83u/ltdvG9/v4b7+XxuLRu97LudvF+/z8oNK3mc6xxOHO+/z7+vz7udzFq9a9/f39/f79/P38/v79/f7+/v7+cSzfRQAAAKNJREFUeNpkj+sOgyAMRovA5v2GQwXavv9brjLNXHZ+NM1pvuQrcMBInEFE5gDZiKKI+XQYprth4Nr7VZDhvZ8TAZtHry56NSRAq8qmk1yMCVpVGjFTu1iTELuidLYywPYl6950ZlxG3XwMzVC59ukK4ssQ6qIaoSY6jZSlVa8h3Iy0jsKZah77F9dq4LXYtm2atjwHffwV0o2Ax6e//Ju3AAMAJXgYUlCP9l0AAAAASUVORK5CYII=');
$icon['world']=base64_decode('iVBORw0KGgoAAAANSUhEUgAAABEAAAARCAMAAAAMs7fIAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAGBQTFRF0+nchMWi3e3jdL+YxeLStNrEptS79fn2yeTV5fHqmc+xrNa9u93J+vz74e/mvODO+Pv5r9jBwd/M/P386/Tu7/bxy+bY8PfzodG2k8urweHQ/f79/f39/v79/f7+/v7+EbGg4AAAAMNJREFUeNpUj9sSgyAMBQMV5K4UCwoG/v8vG22nMz1PsJMcWBjn+A+Ms42BeJ17H6M16GfDVGBaFtA/4kOUjD2YyZ7GaMsH66QEVdlR/U1mOQWbE/rMDo5IW8HM2ka+4hofEqknPcXqQzXg02720mAkwSKVcVEK03ZDwPPJJvrMJjcltCwX4Yx3iskHV5Ga6a3oSm8t7oLv/CZJSxfmtMFkFbZOBL12otbqIvgPIdE5gMrwunWvGdIjn8v8R76XT94CDADiUheJoHxI+gAAAABJRU5ErkJggg==');





$this->pdf->SetAligns(array('L', 'L'));
$this->pdf->SetWidths(array($extraMarge, 5,35, 5,45, 5,30, 5,40,));
//$this->pdf->SetTextColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
$this->pdf->SetFont($this->pdf->rapport_font, 'B', 8);
  $this->pdf->memImage($icon['loc'],$this->pdf->marge+$extraMarge,$this->pdf->getY(),4);
$this->pdf->memImage($icon['loc'],$this->pdf->marge+$extraMarge+$this->pdf->widths[1]+$this->pdf->widths[2],$this->pdf->getY(),4);
$this->pdf->memImage($icon['loc'],$this->pdf->marge+$extraMarge+$this->pdf->widths[1]+$this->pdf->widths[2]+$this->pdf->widths[3]+$this->pdf->widths[4],$this->pdf->getY(),4);
$this->pdf->memImage($icon['kvk'],$this->pdf->marge+$extraMarge+$this->pdf->widths[1]+$this->pdf->widths[2]+$this->pdf->widths[3]+$this->pdf->widths[4]+$this->pdf->widths[5]+$this->pdf->widths[6],$this->pdf->getY(),4);
  $this->pdf->row(array('','','Kantoor Achterhoek','','Kantoor \'s-Hertogenbosch','','Kantoor Venray'));
  $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
  $this->pdf->ln(-5);
  $this->pdf->row(array('','','','','','','','','74009907'));
$this->pdf->memImage($icon['mail'],$this->pdf->marge+$extraMarge+$this->pdf->widths[1]+$this->pdf->widths[2]+$this->pdf->widths[3]+$this->pdf->widths[4]+$this->pdf->widths[5]+$this->pdf->widths[6],$this->pdf->getY(),4);
  $this->pdf->row(array('','','Hanzestraat 1','','Utopialaan 49','','Keizersveld 50','','info@hipcapital.nl'));
$this->pdf->memImage($icon['world'],$this->pdf->marge+$extraMarge+$this->pdf->widths[1]+$this->pdf->widths[2]+$this->pdf->widths[3]+$this->pdf->widths[4]+$this->pdf->widths[5]+$this->pdf->widths[6],$this->pdf->getY(),4);
  $this->pdf->row(array('','','7006 RH','','5232 CD \'s-Hertogenbosch','','5803 AN Venray','','www.hipcapital.nl'));




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
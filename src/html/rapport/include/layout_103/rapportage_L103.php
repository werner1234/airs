<?php
// MOK
$pdf->rapport_layout = 103;
$pdf->marge = 8;
$pdf->top_marge = 25;

      
$pdf->rapport_taal = $data['Taal'];
$pdf->rapport_decimaal = 2;

$pdf->rapport_font = 'Arial';
$pdf->rapport_fontsize = '8';
$pdf->rapport_fontstyle = '';
$pdf->rapport_voetfontsize = '7';
$pdf->rapport_voettext = '';//vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
$pdf->rapport_voettext_rechts = '';//vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
$pdf->rapport_koptext = "";//\n\n{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}";

$pdf->rapport_kop_bgcolor = array(200,200,200);
$pdf->rapport_kop_fontcolor = array(0,0,0);
$pdf->rapport_kop_fontstyle = '';

$pdf->rapport_fontcolor =  array(0,0,0);


if($data['Logo']=='')
  $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/logo_mok.png";
else
  $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];

$pdf->SetLeftMargin($pdf->marge);
$pdf->SetRightMargin($pdf->marge);
$pdf->SetTopMargin($pdf->marge);

$pdf->underlinePercentage=.9;
$pdf->logoXsize=40;


?>
<?php
// ABN
$pdf->rapport_layout = 117;
$pdf->marge = 8;

$pdf->rapport_taal = $data['Taal'];
$pdf->rapport_decimaal = 2;


$pdf->rapport_font = 'arial';
$pdf->rapport_fontsize = '8';
$pdf->rapport_fontstyle = '';
$pdf->rapport_voetfontsize = '6';
$pdf->rapport_koptext = "{Naam1}\n{Naam2}";

$pdf->rapport_donker=array(30,38,42);
$pdf->rapport_donkergroen=array(24,86,90);
$pdf->rapport_groen=array(51,112,112);
$pdf->rapport_lichtgrijs=array(238,238,238);
$pdf->rapport_grijs=array(187,190,195);
$pdf->rapport_donkergrijs=array(84,100,108);
$pdf->rapport_kop_bgcolor = array('r' => 0, 'g' => 0, 'b' => 51); //35/45/104 //33/43/105 //'r'=>88,'g'=>119,'b'=>255
$pdf->rapport_kop_fontcolor = array(255,255,255);
$pdf->rapport_fontcolor = array(0,0,0);
$pdf->rapport_highRow=6;
$pdf->rapport_lowRow=4;

$pdf->rapport_kop_lineWidth=0.5;

$pdf->rapport_kop_fontstyle = 'b';


$pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
$pdf->SetLeftMargin($pdf->marge);
$pdf->SetRightMargin($pdf->marge);
$pdf->SetTopMargin($pdf->marge);
$pdf->SetAutoPageBreak(true,$pdf->marge);

?>
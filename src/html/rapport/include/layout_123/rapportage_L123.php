<?php
// ABN
$pdf->rapport_layout = 123;
$pdf->marge = 8;

$pdf->rapport_taal = $data['Taal'];
$pdf->rapport_decimaal = 2;


$pdf->rapport_font = 'arial';
$pdf->rapport_fontsize = '8';
$pdf->rapport_fontstyle = '';
$pdf->rapport_voetfontsize = '6';
$pdf->rapport_koptext = "{Naam1}\n{Naam2}";

$pdf->triodosGreen=array(0,98,6);
$pdf->triodosBlue=array(0,150,255);
$pdf->triodosDarkBlue=array(0,0,107);
$pdf->triodosLightPetrol=array(229,241,244);
$pdf->triodosPetrol=array(0,121,151);
$pdf->triodosImpactRed=array(255,147,123);

$pdf->rapport_donker=$pdf->triodosDarkBlue;
$pdf->rapport_donkergroen=$pdf->triodosDarkBlue;
$pdf->rapport_groen=$pdf->triodosDarkBlue;
$pdf->rapport_lichtgrijs=$pdf->triodosLightPetrol;
$pdf->rapport_grijs=$pdf->triodosLightPetrol;
$pdf->rapport_donkergrijs=$pdf->triodosPetrol;
$pdf->rapport_kop_bgcolor = array('r' => 0, 'g' => 0, 'b' => 51); //35/45/104 //33/43/105 //'r'=>88,'g'=>119,'b'=>255
$pdf->rapport_kop_fontcolor = array(255,255,255);
$pdf->rapport_fontcolor = array(0,0,0);
$pdf->rapport_highRow=6;
$pdf->rapport_lowRow=4;

$pdf->rapport_kop_lineWidth=0.5;

$pdf->rapport_kop_fontstyle = 'b';


$pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
$pdf->rapport_front_achtergrond = $__appvar['basedir'] . "/html/rapport/logo/tri_front.jpg";

$pdf->SetLeftMargin($pdf->marge);
$pdf->SetRightMargin($pdf->marge);
$pdf->SetTopMargin($pdf->marge);
$pdf->SetAutoPageBreak(true,$pdf->marge);

?>
<?php

$pdf->SetY(257);
$pdf->SetWidths(array(160,50));
$pdf->SetAligns(array('L','L'));
$pdf->SetFont($font,"",7);
$pdf->SetTextColor(140,132,83);

$pdf->row(array('','KVK Eindhoven 17130897'));
$pdf->Ln(1);
$pdf->row(array('','BTW NL 809575759B01'));
$pdf->Ln(1);
$pdf->row(array('','IBAN NL59INGB0683852256'));
$pdf->Ln(1);
$pdf->row(array('','BIC INGBNL2A'));

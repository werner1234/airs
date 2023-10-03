<?php
// PEN
$pdf->rapport_layout = 127;
$pdf->marge = 8;
$pdf->rapport_VOLK_volgorde_beginwaarde = 1;
$pdf->rapport_VOLK_geensubtotaal = 0;
$pdf->rapport_VOLK_decimaal = 2;
$pdf->rapport_VOLK_decimaal_proc = 2;
$pdf->rapport_VOLK_rendement = 0;
$pdf->rapport_VOLK_valutaoverzicht = 1;
$pdf->rapport_VOLK_link = $data['rapportLink'];
$pdf->rapport_VOLK_url = $data['rapportLinkUrl'];

$pdf->rapport_VHO_geenvaluta = 0;
$pdf->rapport_VHO_geensubtotaal = 0;
$pdf->rapport_VHO_volgorde_beginwaarde = 1;
$pdf->rapport_VHO_decimaal_proc = 1;
$pdf->rapport_VHO_decimaal = 2;
$pdf->rapport_VHO_valutaoverzicht = 1;
$pdf->rapport_VHO_rendement = 0;

$pdf->rapport_OIB_specificatie = 1;
$pdf->rapport_OIB_decimaal = 2;
$pdf->rapport_OIB_rendement = 0;
$pdf->rapport_OIB_valutaoverzicht = 0;

$pdf->rapport_OIV_rendement = 0;
$pdf->rapport_OIV_decimaal = 2;
$pdf->rapport_OIV_decimaal_proc = 1;

$pdf->rapport_OIS_valutaoverzicht = 1;
$pdf->rapport_OIS_rendement = 1;
$pdf->rapport_OIS_decimaal = 2;
$pdf->rapport_OIS_zorgplichtpercentage = 1;

$pdf->rapport_OIR_rendement = 1;
$pdf->rapport_OIR_valutaoverzicht = 1;
$pdf->rapport_OIR_decimaal = 2;
$pdf->rapport_OIR_geenrentespec = 1;

$pdf->rapport_HSE_volgorde_beginwaarde = 1;
$pdf->rapport_HSE_rendement = 1;
$pdf->rapport_HSE_valutaoverzicht = 1;

$pdf->rapport_TRANS_procent = 1;
$pdf->rapport_TRANS_decimaal = 2;

$pdf->rapport_inprocent = 0;
$pdf->rapport_taal = $data['Taal'];
$pdf->rapport_decimaal = 2;


if (file_exists(FPDF_FONTPATH . 'Lexend.php'))
{
  if (!isset($pdf->fonts['lexend']))
  {
    $pdf->AddFont('lexend', '', 'LexendL.php');
    $pdf->AddFont('lexend', 'B', 'LexendSB.php');
    $pdf->AddFont('lexend', 'I', 'LexendL.php');
    $pdf->AddFont('lexend', 'BI', 'LexendSB.php');
  }
  $pdf->rapport_font = 'lexend';
}
else
{
  $pdf->rapport_font = 'Times';
}
$pdf->rapport_fontsize = 8;
$pdf->rapport_fontstyle = '';
$pdf->rapport_voetfontsize = '6';
$pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
$pdf->rapport_koptext = vertaalTekst("Client", $pdf->rapport_taal) . " {ClientVermogensbeheerder} / " . vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank}\n{Naam1}\n{Naam2}";

$pdf->rapport_kop_bgcolor = array('r' => 152, 'g' => 230, 'b' => 223);
$pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
$pdf->rapport_kop_fontstyle = 'b';
$pdf->rapport_background_fill = array(200, 200, 200);
$pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
$pdf->rapport_kop2_fontstyle = '';

$pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
$pdf->rapport_kop3_fontstyle = 'bi';

$pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
$pdf->rapport_kop4_fontstyle = 'b';

$pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);

$pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);

$pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
$pdf->rapport_subtotaal_omschr_fontstyle = '';
$pdf->rapport_subtotaal_fontcolor = array('r' => 0);
$pdf->rapport_subtotaal_fontstyle = 'b';

$pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
$pdf->rapport_totaal_omschr_fontstyle = '';
$pdf->rapport_totaal_fontcolor = array('r' => 0);
$pdf->rapport_totaal_fontstyle = 'b';

$pdf->rapport_valuta_voorzet = "Waarden ";
$pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";

$pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
$pdf->SetLeftMargin($pdf->marge);
$pdf->SetRightMargin($pdf->marge);
$pdf->SetTopMargin($pdf->marge);
?>
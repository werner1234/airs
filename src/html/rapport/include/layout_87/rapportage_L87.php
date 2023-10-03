<?php
$pdf->rapport_layout = 87;
$pdf->marge = 8;
$pdf->rapport_VOLK_volgorde_beginwaarde = 1;
$pdf->rapport_VOLK_geensubtotaal = 0;
$pdf->rapport_VOLK_decimaal = 0;
$pdf->rapport_VOLK_decimaal_proc = 2;
$pdf->rapport_VOLK_rendement = 0;
$pdf->rapport_VOLK_valutaoverzicht = 1;
$pdf->rapport_VOLK_link = $data['rapportLink'];
$pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
$pdf->rapport_VOLK_aantalVierDecimaal = 1;
$pdf->rapport_valutaoverzicht_rev = true;

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
$pdf->rapport_TRANS_decimaal = 0;
$pdf->rapport_TRANS_legenda = 1;

$pdf->rapport_inprocent = 0;
$pdf->rapport_taal = $data['Taal'];
$pdf->rapport_decimaal = 2;

if (file_exists(FPDF_FONTPATH . 'rooney.php'))
{
  if (!isset($pdf->fonts['rooney']))
  {
    $pdf->AddFont('rooney', '', 'rooney.php');
    $pdf->AddFont('rooney', 'B', 'rooneyB.php');
    $pdf->AddFont('rooney', 'I', 'rooneyI.php');
    $pdf->AddFont('rooney', 'BI', 'rooneyBI.php');
  }
  $pdf->rapport_font = 'rooney';
}
$pdf->rapport_fontsize = '8';
$pdf->rapport_fontstyle = '';
$pdf->rapport_voetfontsize = '6';
$pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
$pdf->rapport_koptext = "{Naam1}\n{Naam2}";

$pdf->rapport_kop_bgcolor = array('r' => 236, 'g' => 102, 'b' => 8); //35/45/104 //33/43/105 //'r'=>88,'g'=>119,'b'=>255
$pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);//array('r'=>127,'g'=>128,'b'=>132);
$pdf->rapport_kop_fontstyle = 'b';

$pdf->rapport_background_fill = array(255,239,229);
$pdf->rapport_grafiek2 = array(36,17,47);

$pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);

$pdf->rapport_fontcolor=$pdf->rapport_default_fontcolor;
$pdf->rapport_grafiektxtcolor=array($pdf->rapport_default_fontcolor['r'],$pdf->rapport_default_fontcolor['g'],$pdf->rapport_default_fontcolor['b']);//$pdf->rapport_default_fontcolor;
$pdf->rapport_kop2_fontcolor = $pdf->rapport_default_fontcolor;
$pdf->rapport_kop2_fontstyle = '';

$pdf->rapport_kop3_fontcolor = $pdf->rapport_default_fontcolor;
$pdf->rapport_kop3_fontstyle = 'bi';

$pdf->rapport_kop4_fontcolor = $pdf->rapport_default_fontcolor;
$pdf->rapport_kop4_fontstyle = 'b';

$pdf->rapport_default_fontcolor = $pdf->rapport_default_fontcolor;

//$pdf->rapport_fonds_fontcolor = array('r'=>0,'g'=>73,'b'=>156);
$pdf->rapport_fonds_fontcolor = $pdf->rapport_default_fontcolor;

$pdf->rapport_subtotaal_omschr_fontcolor = $pdf->rapport_default_fontcolor;
$pdf->rapport_subtotaal_omschr_fontstyle = '';
$pdf->rapport_subtotaal_fontcolor = $pdf->rapport_default_fontcolor;
$pdf->rapport_subtotaal_fontstyle = 'b';

$pdf->rapport_totaal_omschr_fontcolor = $pdf->rapport_default_fontcolor;
$pdf->rapport_totaal_omschr_fontstyle = '';
$pdf->rapport_totaal_fontcolor = $pdf->rapport_default_fontcolor;
$pdf->rapport_totaal_fontstyle = 'b';

$pdf->rapport_valuta_voorzet = "Waarden ";
$pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";

$pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
$pdf->SetLeftMargin($pdf->marge);
$pdf->SetRightMargin($pdf->marge);
$pdf->SetTopMargin($pdf->marge);
?>
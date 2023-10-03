<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/04 14:50:07 $
File Versie					: $Revision: 1.2 $

$Log: rapportage_L98.php,v $




*/

$pdf->rapport_layout = 98; #DEX
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

$pdf->rapport_VHO_geenvaluta = 0;
$pdf->rapport_VHO_geensubtotaal = 0;
$pdf->rapport_VHO_volgorde_beginwaarde = 1;
$pdf->rapport_VHO_decimaal_proc = 1;
$pdf->rapport_VHO_decimaal = 2;
$pdf->rapport_VHO_valutaoverzicht = 1;
$pdf->rapport_VHO_rendement = 0;

$pdf->rapport_HSE_aantalVierDecimaal=1;

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
$pdf->rapport_TRANS_legenda = 1;

$pdf->rapport_inprocent = 0;
$pdf->rapport_taal = $data['Taal'];
$pdf->rapport_decimaal = 2;
/*
if (file_exists(FPDF_FONTPATH . 'calibri.php'))
{
  if (!isset($pdf->fonts['calibri']))
  {
    $pdf->AddFont('calibri', '', 'calibri.php');
    $pdf->AddFont('calibri', 'B', 'calibrib.php');
    $pdf->AddFont('calibri', 'I', 'calibrii.php');
    $pdf->AddFont('calibri', 'BI', 'calibribi.php');
  }
  $pdf->rapport_font = 'calibri';
}

if(file_exists(FPDF_FONTPATH.'helvetica.php'))
{
  if(!isset($pdf->fonts['helvetica']))
  {
    $pdf->AddFont('helvetica','','helvetica.php');
    $pdf->AddFont('helvetica','I','helveticai.php');
    $pdf->AddFont('helvetica','B','helveticab.php');
    $pdf->AddFont('helvetica','BI','helveticabi.php');
  }
  $pdf->rapport_font = 'helvetica';
}
*/
if(!isset($pdf->fonts['verdana']))
{
  $pdf->AddFont('Verdana');
  $pdf->AddFont('Verdana','B','verdanab.php');
  $pdf->AddFont('verdana','I','verdanai.php');
  $pdf->AddFont('Verdana','BI','verdanaib.php');
  $pdf->rapport_font = 'Verdana';
}


$pdf->rapport_fontsize = '7.5';
$pdf->rapport_fontstyle = '';
$pdf->rapport_voetfontsize = '6';
$pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
$pdf->rapport_koptext = "{Naam1}\n{Naam2}";

if($data['Vermogensbeheerder']=='IND_uit')
{
  $pdf->rapport_kop_bgcolor = array('r' => 190 , 'g' => 30, 'b' => 45);
  $pdf->rapport_background_fill = array(232, 108, 121);
  $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);
  $pdf->rapport_kop_line_color = array(255,255,255);
}
else
{
  $pdf->rapport_kop_bgcolor = array('r' => 70, 'g' => 189, 'b' => 198);
  $pdf->rapport_background_fill = array(188, 237, 241);
  $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
  $pdf->rapport_kop_line_color = array(0,0,0);
}


$pdf->rapport_kop_fontstyle = 'b';

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
//echo base64_encode(file_get_contents($pdf->rapport_logo));exit;
$pdf->SetLeftMargin($pdf->marge);
$pdf->SetRightMargin($pdf->marge);
$pdf->SetTopMargin($pdf->marge);
$pdf->rapport_logo_beeld=base64_decode('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');
$pdf->logoXsize= 40;

?>

<?php
//TOP & IBE
$pdf->rapport_layout = 125;
$pdf->marge = 8;
$pdf->top_marge = 25;

$pdf->rapport_valutaoverzicht_rev = 1;
$pdf->printValutaPerformanceOverzichtProcentTeken = true;

$pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
$pdf->rapport_VOLK_volgorde_beginwaarde = 2;
$pdf->rapport_VOLK_geensubtotaal = 1;
$pdf->rapport_VOLK_decimaal = 0;
$pdf->rapport_VOLK_decimaal_proc = 1;
$pdf->rapport_VOLK_rendement = 1;
$pdf->rapport_VOLK_valutaoverzicht = 2;
$pdf->rapport_VOLK_link = $data['rapportLink'];
$pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
$pdf->rapport_VOLK_aantalVierDecimaal = 1;
$pdf->rapport_VOLK_geenvaluta = 1;

$pdf->rapport_VHO_geenvaluta = 0;
$pdf->rapport_VHO_geensubtotaal = 0;
$pdf->rapport_VHO_volgorde_beginwaarde = 1;
$pdf->rapport_VHO_decimaal_proc = 1;
$pdf->rapport_VHO_decimaal = 0;
$pdf->rapport_VHO_valutaoverzicht = 1;
$pdf->rapport_VHO_indexUit = 1;
$pdf->rapport_VHO_rendement = 0;
$pdf->rapport_VHO_aantalVierDecimaal = 1;

$pdf->rapport_HSE_volgorde_beginwaarde = 1;
$pdf->rapport_HSE_valutaoverzicht = 2;
$pdf->rapport_HSE_geenrentespec = 1;
$pdf->rapport_HSE_aantalVierDecimaal = 1;

$pdf->rapport_OIH_geenrentespec = 1;

$pdf->rapport_MOD_valutaoverzicht = 1;

$pdf->rapport_OIB_titel = "Verdeling naar vermogenscategorie";
$pdf->rapport_OIB_specificatie = 1;
$pdf->rapport_OIB_decimaal = 0;
$pdf->rapport_OIB_rendement = 0;
$pdf->rapport_OIB_valutaoverzicht = 0;

$pdf->rapport_OIV_titel = "Valutaverdeling";
$pdf->rapport_OIV_rendement = 0;
$pdf->rapport_OIV_decimaal = 0;
$pdf->rapport_OIV_decimaal_proc = 1;

$pdf->rapport_OIS_valutaoverzicht = 2;
$pdf->rapport_OIS_rendement = 1;
$pdf->rapport_OIS_decimaal = 2;
$pdf->rapport_OIS_geenrentespec = 1;

$pdf->rapport_OIR_rendement = 1;
$pdf->rapport_OIR_valutaoverzicht = 2;
$pdf->rapport_OIR_decimaal = 2;
$pdf->rapport_OIR_geenrentespec = 1;

$pdf->rapport_TRANS_legenda = 1;
$pdf->rapport_TRANS_decimaal = 0;
$pdf->rapport_TRANS_decimaal2 = 0;

$pdf->rapport_PERF_titel = "Vermogensontwikkeling";
$pdf->rapport_PERF_displayType = 1;
$pdf->rapport_PERF_jaarRendement = 1;

$pdf->rapport_MUT2_decimaal = 2;

$pdf->rapport_inprocent = 0;
$pdf->rapport_taal = $data['Taal'];
$pdf->rapport_decimaal = 2;

$pdf->rapport_font = 'Arial';
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
$pdf->rapport_fontsize = '9';

$pdf->rapport_fontstyle = '';
$pdf->rapport_voetfontsize = '5';
$pdf->rapport_voettext = '';// vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
$pdf->rapport_voettext_rechts = '';// vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
$pdf->rapport_koptext = '';//"\n\n{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}";

$pdf->kopGroen=array(103,183,52);
$pdf->kopTextKleur=array(255,255,255);
$pdf->kopGrijs=array(227,227,227);
$pdf->grafiekBruin=array(109,93,81);
$pdf->textGrijs=array(180,180,180);
$pdf->textBlauw=array(32,38,75);
$pdf->textGroen=$pdf->kopGroen;
$pdf->okeGroen=array(34,177,76);
$pdf->foutRood=array(237,28,36);


  $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
  $pdf->rapport_kop_fontcolor = array('r' => 88, 'g' => 171, 'b' => 39);//array('r'=>236,'g'=>0,'b'=>140);
  $pdf->rapport_kop_fontstyle = '';
  $pdf->rapport_kop2_fontcolor = array('r' => 88, 'g' => 171, 'b' => 39);//array('r'=>236,'g'=>0,'b'=>140);

$pdf->rapport_kop3_fontcolor = array(0);
$pdf->rapport_kop3_fontstyle = 'bi';

$pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
$pdf->rapport_kop4_fontstyle = 'b';

$pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);

$pdf->rapport_fonds_fontcolor = array('r' => 0);

$pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
$pdf->rapport_subtotaal_omschr_fontstyle = '';
$pdf->rapport_subtotaal_fontcolor = array('r' => 0);
$pdf->rapport_subtotaal_fontstyle = 'b';

$pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
$pdf->rapport_totaal_omschr_fontstyle = '';
$pdf->rapport_totaal_fontcolor = array('r' => 0);
$pdf->rapport_totaal_fontstyle = 'b';

$pdf->rapport_valuta_voorzet = "Waarden ";
$pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";

if (file_exists($__appvar['basedir'] . "/html/rapport/logo/" . $data['Remisier'] . ".png"))
{
  $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Remisier'] . ".png";
}
else
{
  $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
}
$pdf->logoBeeld=base64_decode('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');
$pdf->logoXsize= 12;
$pdf->SetLeftMargin($pdf->marge);
$pdf->SetRightMargin($pdf->marge);
$pdf->SetTopMargin($pdf->marge);
$pdf->rapportBeginY=40;

<?php
//TOP & IBE
$pdf->rapport_layout = 107;
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
$pdf->rapport_VOLK_link = $data[rapportLink];
$pdf->rapport_VOLK_url = $data[rapportLinkUrl];
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
$pdf->rapport_taal = $data[Taal];
$pdf->rapport_decimaal = 2;

$pdf->rapport_font = 'Arial';
/*
if(file_exists(FPDF_FONTPATH.'calibri.php'))
{
if(!isset($pdf->fonts['calibri']))
{
  $pdf->AddFont('calibri','','calibri.php');
  $pdf->AddFont('calibri','B','calibrib.php');
  $pdf->AddFont('calibri','I','calibrii.php');
  $pdf->AddFont('calibri','BI','calibribi.php');
}
$pdf->rapport_font = 'calibri';
}
*/
$pdf->rapport_fontsize = '9';
//$pdf->rapport_fontsize = '10';

$pdf->rapport_fontstyle = '';
$pdf->rapport_voetfontsize = '5';
$pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
$pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
$pdf->rapport_koptext = "\n\n{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}";

if ($data['Vermogensbeheerder'] == 'IBE')
{
  $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
  $pdf->rapport_kop_fontcolor = array('r' => 88, 'g' => 171, 'b' => 39);//array('r'=>236,'g'=>0,'b'=>140);
  $pdf->rapport_kop_fontstyle = '';
  $pdf->rapport_kop2_fontcolor = array('r' => 88, 'g' => 171, 'b' => 39);//array('r'=>236,'g'=>0,'b'=>140);
  
}
else
{
  $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
  $pdf->rapport_kop_fontcolor = array('r' => 236, 'g' => 0, 'b' => 140);//array('r'=>0,'g'=>69,'b'=>132);
  $pdf->rapport_kop_fontstyle = '';
  $pdf->rapport_kop2_fontcolor = array('r' => 236, 'g' => 0, 'b' => 140);//array('r'=>0,'g'=>69,'b'=>132);
}
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
  /*
if($data['Remisier']=='topcapital')
{
  $pdf->rapport_kop2_fontcolor = array('r'=>236,'g'=>0,'b'=>140);
  $pdf->rapport_kop_fontcolor = array('r'=>236,'g'=>0,'b'=>140);
}
*/
}
else
{
  $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
}
$pdf->SetLeftMargin($pdf->marge);
$pdf->SetRightMargin($pdf->marge);
$pdf->SetTopMargin($pdf->marge);

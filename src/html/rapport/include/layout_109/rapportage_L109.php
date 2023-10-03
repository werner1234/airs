<?php
//MHN
$pdf->rapport_layout = 109;
$pdf->marge = 6;
$pdf->top_marge = 15;
$pdf->rapport_dontsortpie = true;
$pdf->kwartaalFactuurEindKwartaal=true;

$pdf->rapport_valutaoverzicht_rev = 1;
$pdf->rapport_resultaatText = "Resultaat lopend kalenderjaar";
$pdf->rapport_rendementText = "Rendement lopend kalenderjaar";


$pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
$pdf->rapport_VOLK_volgorde_beginwaarde = 2;
$pdf->rapport_VOLK_geensubtotaal = 0;
$pdf->rapport_VOLK_decimaal = 0;
$pdf->rapport_VOLK_decimaal_proc = 1;
$pdf->rapport_VOLK_rendement = 1;
$pdf->rapport_VOLK_valutaoverzicht = 2;
$pdf->rapport_VOLK_link = $data['rapportLink'];
$pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
$pdf->rapport_VOLK_aantalVierDecimaal = 1;

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

$pdf->rapport_MUT2_decimaal = 2;

$pdf->rapport_inprocent = 0;
$pdf->rapport_taal = $data['Taal'];
$pdf->rapport_decimaal = 2;

$pdf->rapport_font = 'Arial';
$pdf->rapport_fontsize = '9';
//$pdf->rapport_fontsize = '10';

if (file_exists(FPDF_FONTPATH . 'DINAlternate.php'))
{
  if (!isset($pdf->fonts['dinalternate']))
  {
    $pdf->AddFont('dinalternate', '', 'DINAlternate.php');
    $pdf->AddFont('dinalternate', 'B', 'DINAlternateB.php');
    $pdf->AddFont('dinalternate', 'I', 'DINAlternate.php');
    $pdf->AddFont('dinalternate', 'BI', 'DINAlternateB.php');
  
  }
  $pdf->rapport_font = 'dinalternate';
}

$pdf->rapport_fontstyle = '';
$pdf->rapport_voetfontsize = '6';
$pdf->rapport_voettext = vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime()) . " | " . vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
$pdf->rapport_voettext_rechts = '';
$pdf->rapport_koptext = "\n\n{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}\n{Risicoklasse}";

$pdf->blauwDonker=array(10,59,76);
$pdf->blauwLicht=array(112,149,171);
$pdf->bruinDonker=array(203,170,121);
$pdf->bruinLicht=array(239,237,231);
$pdf->grijsBlauw=array(168,189,201); //122.153.172 met doorzichtigheid 65%

$pdf->rapport_kopvoet_bg = array(175, 175, 175);
$pdf->rapport_titel_fontcolor = array(30,90,150);//array(163, 145, 97);//array(147,124,78); //array(191,143,0);
$pdf->rapport_grafiek_color = array(192, 23, 136);
$pdf->rapport_koptrans_color = array(30, 90, 150);
$pdf->rapport_row_bg = array(197, 220, 243);
$pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
$pdf->rapport_kop_fontcolor =  array('r' => 0);// array('r'=>0,'g'=>49,'b'=>60);//array('r' => 0, 'g' => 0, 'b' => 0);
$pdf->rapport_kop_fontstyle = '';

$pdf->rapport_kop2_fontcolor = array('r' => 0);

$pdf->rapport_kop3_fontcolor = array('r'=>0,'g'=>49,'b'=>60);$pdf->rapport_kop_fontcolor;// array(0);
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
$pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
$pdf->SetLeftMargin($pdf->marge);
$pdf->SetRightMargin($pdf->marge);
$pdf->SetTopMargin($pdf->marge);
$pdf->SetAutoPageBreak(true, 30);
$pdf->pagebreak = $pdf->PageBreakTrigger;
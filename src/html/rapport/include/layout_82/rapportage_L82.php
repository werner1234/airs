<?php
// VRIJ layout
// kopie FIN_L27
$pdf->rapport_layout = 82;
$pdf->marge = 8;
$pdf->rapport_dontsortpie = true;

$pdf->rapport_printAEXVergelijkingEur = 0;

$pdf->rapport_VOLK_volgorde_beginwaarde = 1;
$pdf->rapport_VOLK_geensubtotaal = 0;
$pdf->rapport_VOLK_decimaal = 0;
$pdf->rapport_VOLK_decimaal_proc = 1;
$pdf->rapport_VOLK_rendement = 1;
$pdf->rapport_VOLK_valutaoverzicht = 1;
$pdf->rapport_VOLK_aantalVierDecimaal = 1;
//$pdf->rapport_VOLK_titel = "Vergelijkend overzicht verslagperiode";
$pdf->rapport_VOLK_link = $data[rapportLink];
$pdf->rapport_VOLK_url = $data[rapportLinkUrl];
$pdf->rapport_VOLK_geenIndex = 0;

$pdf->rapport_HSE_volgorde_beginwaarde = 1;
$pdf->rapport_HSE_rendement = 1;
$pdf->rapport_HSE_valutaoverzicht = 1;
$pdf->rapport_HSE_aantalVierDecimaal = 1;
$pdf->rapport_HSE_geenIndex = 0;

$pdf->rapport_MOD_valutaoverzicht = 1;

$pdf->rapport_OIB_specificatie = 1;
$pdf->rapport_OIB_decimaal = 2;
$pdf->rapport_OIB_rendement = 0;
$pdf->rapport_OIB_valutaoverzicht = 0;

$pdf->rapport_OIV_rendement = 0;
$pdf->rapport_OIV_valutaoverzicht = 0;
$pdf->rapport_OIV_decimaal = 2;
$pdf->rapport_OIV_decimaal_proc = 1;

$pdf->rapport_OIS_valutaoverzicht = 1;
$pdf->rapport_OIS_rendement = 1;
$pdf->rapport_OIS_decimaal = 2;
$pdf->rapport_OIS_aantalVierDecimaal = 1;
$pdf->rapport_OIS_geenIndex = 1;

$pdf->rapport_OIR_rendement = 1;
$pdf->rapport_OIR_valutaoverzicht = 1;
$pdf->rapport_OIR_decimaal = 2;

$pdf->rapport_TRANS_procent = 1;
$pdf->rapport_TRANS_legenda = 1;
$pdf->rapport_TRANS_decimaal = 2;

$pdf->rapport_VHO_geenvaluta = 0;
$pdf->rapport_VHO_geensubtotaal = 0;
$pdf->rapport_VHO_volgorde_beginwaarde = 1;
$pdf->rapport_VHO_decimaal_proc = 1;
$pdf->rapport_VHO_decimaal = 0;
$pdf->rapport_VHO_valutaoverzicht = 1;
$pdf->rapport_VHO_rendement = 0;
$pdf->rapport_VHO_percentageTotaal = 1;
$pdf->rapport_VHO_aantalVierDecimaal = 1;
$pdf->rapport_VHO_geenIndex = 1;

$pdf->rapport_PERF_liquiditeiten = 1;
$pdf->rapport_PERF_jaarRendement = 1;
$pdf->rapport_perfIndexJanuari = 1;

$pdf->rapport_inprocent = 1;
$pdf->rapport_taal = $data[Taal];
$pdf->rapport_decimaal = 2;

$pdf->rapport_rendementText = 'Rendement over verslagperiode';

$pdf->rapport_font = 'Arial';
$pdf->rapport_fontsize = '9';
$pdf->rapport_fontstyle = '';
$pdf->rapport_voetfontsize = '6';
$pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend.\nDe totale waarde van uw portefeuille kan afwijken van de gegevens die u wellicht online bekeken heeft door kleine verschillen in gebruikte koersen.", $pdf->rapport_taal);
$pdf->rapport_koptext = vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank} / {Risicoklasse}\n{Naam1}\n{Naam2}";

$pdf->rapport_grafiek_color = array(0, 69, 124);
$pdf->rapport_kop_bgcolor = array('r' => 0, 'g' => 173, 'b' => 239);

//$pdf->rapport_kop_bgcolor = array('r'=>255,'g'=>255,'b'=>255);
$pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
$pdf->rapport_kop_fontstyle = '';

$pdf->rapport_kop2_fontcolor = array('r' => 0);

$pdf->rapport_kop3_fontcolor = array(0);
$pdf->rapport_kop3_fontstyle = 'b';

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
$pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
$pdf->rapport_factuurHeader = $__appvar['basedir'] . "/html/rapport/logo/factuurHeader.png";
$pdf->rapport_factuurFooter = $__appvar['basedir'] . "/html/rapport/logo/factuurFooter.png";
$pdf->SetLeftMargin($pdf->marge);
$pdf->SetRightMargin($pdf->marge);
$pdf->SetTopMargin($pdf->marge);

?>
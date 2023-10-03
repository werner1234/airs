<?php
// VLC layout
$pdf->rapport_layout = 83;

if(!is_array($pdf->valutaOmschrijvingen))
{
  $query="SELECT Valuta,Omschrijving FROM Valutas order by afdrukvolgorde";
  $db=new DB();
  $db->SQL($query);
  $db->Query();
  while($valuta=$db->nextRecord())
  {
    $pdf->valutaOmschrijvingen[$valuta['Valuta']]=strtolower($valuta['Omschrijving']);
  }
}

$pdf->marge = 8;
//$pdf->rowHeight = 5.5;
$pdf->rapport_VOLK_procent = 1;
$pdf->rapport_VOLK_volgorde_beginwaarde = 1;
$pdf->rapport_VOLK_geensubtotaal = 0;
$pdf->rapport_VOLK_decimaal = 2;
$pdf->rapport_VOLK_decimaal_proc = 2;
$pdf->rapport_VOLK_rendement = 0;
$pdf->rapport_VOLK_valutaoverzicht = 1;
$pdf->rapport_VOLK_rentePeriode = 1;
$pdf->rapport_VOLK_link = $data['rapportLink'];
$pdf->rapport_VOLK_url = $data['rapportLinkUrl'];

$pdf->rapport_VHO_geenvaluta = 0;
$pdf->rapport_VHO_geensubtotaal = 0;
$pdf->rapport_VHO_volgorde_beginwaarde = 1;
$pdf->rapport_VHO_decimaal_proc = 1;
$pdf->rapport_VHO_decimaal = 2;
$pdf->rapport_VHO_valutaoverzicht = 1;
$pdf->rapport_VHO_rendement = 0;
$pdf->rapport_VHO_percentageTotaal = 1;
$pdf->rapport_VHO_rentePeriode = 1;

$pdf->rapport_HSE_volgorde_beginwaarde = 1;
$pdf->rapport_HSE_rendement = 1;
$pdf->rapport_HSE_valutaoverzicht = 1;
$pdf->rapport_HSE_rentePeriode = 1;

$pdf->rapport_MOD_valutaoverzicht = 1;

$pdf->rapport_OIB_specificatie = 1;
$pdf->rapport_OIB_decimaal = 2;
$pdf->rapport_OIB_rendement = 0;
$pdf->rapport_OIB_valutaoverzicht = 0;
$pdf->rapport_OIB_rentePeriode = 1;

$pdf->rapport_OIV_rendement = 0;
$pdf->rapport_OIV_decimaal = 2;
$pdf->rapport_OIV_decimaal_proc = 1;
$pdf->rapport_OIV_rentePeriode = 1;

$pdf->rapport_OIS_valutaoverzicht = 1;
$pdf->rapport_OIS_rendement = 1;
$pdf->rapport_OIS_decimaal = 2;
$pdf->rapport_OIS_rentePeriode = 1;

$pdf->rapport_OIR_rendement = 1;
$pdf->rapport_OIR_decimaal = 2;
$pdf->rapport_OIR_rentePeriode = 1;
$pdf->rapport_OIR_valutaoverzicht = 1;

$pdf->rapport_OIH_rentePeriode = 1;

$pdf->rapport_PERF_liquiditeiten = 1;
$pdf->rapport_PERF_jaarRendement = 1;


$pdf->rapport_TRANS_procent = 1;
$pdf->rapport_TRANS_decimaal = 0;

$pdf->rapport_inprocent = 1;
$pdf->rapport_taal = $data['Taal'];
$pdf->rapport_decimaal = 2;

if (file_exists(FPDF_FONTPATH . 'Merriweather-Regular.php'))
{
  if (!isset($pdf->fonts['merriweather']))
  {
    $pdf->AddFont('merriweather', '', 'Merriweather-Regular.php');
    $pdf->AddFont('merriweather', 'B', 'Merriweather-Bold.php');
    $pdf->AddFont('merriweather', 'I', 'Merriweather-Italic.php');
    $pdf->AddFont('merriweather', 'BI', 'Merriweather-BoldItalic.php');
  }
  $pdf->rapport_font = 'merriweather';
}
else
{
  $pdf->rapport_font = 'Times';
}
$pdf->rapport_fontsize = '8';
$pdf->rapport_fontstyle = '';
$pdf->rapport_voetfontsize = '6';
$pdf->rapport_voettext = '';
$pdf->rapport_koptext = '';//vertaalTekst("Depotnummer", $pdf->rapport_taal) . " {PortefeuilleFormat}\n{Naam1}\n{Naam2}";
$pdf->rapport_consolidatieKoptext = '';//vertaalTekst("Geconsolideerd", $pdf->rapport_taal) . "\n{Naam1}\n{Naam2}";
$pdf->rapport_rendementText = 'Rendement over verslagperiode';

//			$pdf->rapport_kop_bgcolor = array('r'=>245,'g'=>240,'b'=>155);
$pdf->rapport_kop_bgcolor = array('r' => 55, 'g' => 96, 'b' => 145);
$pdf->rapport_grafiek_color =array(55,96,145);
//			$pdf->rapport_kop_fontcolor = array('r'=>38,'g'=>73,'b'=>156);
$pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);
$pdf->rapport_kop_fontstyle = 'b';

$pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);//array('r'=>38,'g'=>73,'b'=>156);
$pdf->rapport_kop2_fontstyle = '';

$pdf->rapport_kop3_fontcolor = array('r' => 204, 'g' => 51, 'b' => 5); //array('r'=>38,'g'=>73,'b'=>156);
$pdf->rapport_kop3_fontstyle = 'bi';

$pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);//array('r'=>38,'g'=>73,'b'=>156);
$pdf->rapport_kop4_fontstyle = 'b';

$pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);

$pdf->rapport_fonds_fontcolor = array('r' => 96, 'g' => 96, 'b' => 96);// array('r'=>38,'g'=>73,'b'=>156);
$pdf->rapport_fontcolor = $pdf->rapport_fonds_fontcolor ;

$pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);//array('r'=>38,'g'=>73,'b'=>156);
$pdf->rapport_subtotaal_omschr_fontstyle = '';
$pdf->rapport_subtotaal_fontcolor = array('r' => 0);
$pdf->rapport_subtotaal_fontstyle = 'b';

$pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0); //array('r'=>38,'g'=>73,'b'=>156);
$pdf->rapport_totaal_omschr_fontstyle = '';
$pdf->rapport_totaal_fontcolor = array('r' => 0);
$pdf->rapport_totaal_fontstyle = 'b';

$pdf->rapport_valuta_voorzet = "Waarden ";
$pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";

//$pdf->rapport_logo = $__appvar['basedir']."/html/rapport/logo/".$data[Logo];
$pdf->rapport_logo_tekst = "Van Lawick & Co.";// "Van Lawick & Co.\nINVESTMENT STRATEGIES";
$pdf->rapport_logo_font = "Times";
$pdf->rapport_logo_fontcolor = array('r' => 204, 'g' => 51, 'b' => 5);//array('r'=>0,'g'=>0,'b'=>128);
$pdf->rapport_logo_fontstyle = "b";
$pdf->rapport_logo_fontsize = "14";
$pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];

$pdf->rapport_logo_tekst2 = "Vermogensbeheer";
$pdf->rapport_logo_font2 = "Times";
$pdf->rapport_logo_fontcolor2 = array('r' => 0, 'g' => 0, 'b' => 0);
$pdf->rapport_logo_fontstyle2 = "";
$pdf->rapport_logo_fontsize2 = "10";

$pdf->SetLeftMargin($pdf->marge);
$pdf->SetRightMargin($pdf->marge);
$pdf->SetTopMargin($pdf->marge);
?>
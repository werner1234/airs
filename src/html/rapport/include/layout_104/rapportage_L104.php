<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/04 14:50:07 $
File Versie					: $Revision: 1.2 $

$Log: rapportage_L104.php,v $
Revision 1.2  2020/07/04 14:50:07  rvv
*** empty log message ***

Revision 1.1  2020/07/04 14:09:21  rvv
*** empty log message ***



*/

$pdf->rapport_layout = 104; #VLE
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
$pdf->rapport_TRANS_decimaal = 0;
$pdf->rapport_TRANS_legenda = 1;

$pdf->rapport_inprocent = 0;
$pdf->rapport_taal = $data['Taal'];
$pdf->rapport_decimaal = 2;

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
$pdf->rapport_fontsize = '8';
$pdf->rapport_fontstyle = '';
$pdf->rapport_voetfontsize = '6';
$pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
$pdf->rapport_koptext = "{Naam1}\n{Naam2}";

$pdf->rapport_kop_bgcolor = array('r' => 0, 'g' => 128, 'b' => 123); //35/45/104 //33/43/105 //'r'=>88,'g'=>119,'b'=>255
$pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);//array('r'=>127,'g'=>128,'b'=>132);
$pdf->rapport_kop_fontstyle = 'b';

$pdf->rapport_background_fill = array(246,207,178);

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
$pdf->rapport_logo_beeld='';//base64_decode('iVBORw0KGgoAAAANSUhEUgAAAgsAAADmCAMAAABsxDaJAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjBFQzQ3QjRBQkRFOTExRUFBNkQ0OUM5NzNFM0QxNEQwIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjBFQzQ3QjRCQkRFOTExRUFBNkQ0OUM5NzNFM0QxNEQwIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MEVDNDdCNDhCREU5MTFFQUE2RDQ5Qzk3M0UzRDE0RDAiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MEVDNDdCNDlCREU5MTFFQUE2RDQ5Qzk3M0UzRDE0RDAiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4R6l4NAAAAYFBMVEXz2sfjpnftxKfpuZXVeDXZhEb+/fv78erdklvnsov57OP14NHgnWrx1L3fmGLRbCTlrID89fDbi1H9+fb46N3uyq/wz7b35dfrv57hoXDRaR/dlFXWfjzTcizQZxz///+2WjeoAAAAIHRSTlP/////////////////////////////////////////AFxcG+0AABFwSURBVHja7J3nYuo4EIVdcC+4lyBb7/+Wi0lylxBG2Fhl5Gj+3yWLP46P5owki5rSsuq4t7rWGYYoKqJbua7TZlbfBP6b/0nLfKt6VWw5URqSaYbrY/Kq1HYdq6kNC8esMnNT7zxvqslLI8cqDQsHqsZJyfx+ncLCSUrDAvcXdZO1bjGmYUXIaboW8cJwLNyhs5pSiB606TTzqFM6xMdnoQ6CuOmbOChrcR/iN51rX9/UrDpVV01OAn4fGgzVzLO8wqqPyEKctEMx5tff5115VT5eX5GxzxWD3imq07S6TnnUcQCidviC8OUh8rZUzELdJFnnLEugwrbtImqTHb/huItSj7DKG12Li2T7lhtuwOD/8opsF5CJfZ4F1Tl0gj0s1EHTJ9fV6/YvuE5a184vv6vo3nladVewMfi/cqfZ6dncfNpRJ/vt35pVzULrI2zr7SwENyn+/wnmV0+6HvigfYrBd0XWtt+On9lkU+Xdu/LjJ5E37a6qe+ezO28WX5Pdb2AhsIbi6SNM23WPsI8uryp34i0vULK5quEdGpKCTHwq3KxNSTVLqur+OVqMV7KbMh6hXa75DV9WVdSvW1y5b5BwoyHZ6t7dauJXp2zbh4+zxDo5PpuFwGJycKvxhTLEw2V9Ra9dd1OQ98vZ9KoeJ7512uIanGmWW6SFWQi6YtXzy5jv2uKyqdIXQpqMZFet/mn6rTdxr8rH93q4f1M0T1koO3vt4xvg13o7XrZWynip+11OdtbKp1EPAki41sqXVJzOSurc/mbhuoRa//QGcB12eafAr6t0KrK/+lUkkElMrXpH1dF5VlXRAwsbdf3pS7C27Mt7Bah4EHmERzUrrKkoEqapXfFykm4UftRwz0Jc7Bf1wEnfJOGS18DKjk959culw2kSV69VyfJmpfXR/GOhHrY+vN+ykESX9yt71v1NCa+KXpBQiCRh8l4uksJZdY3fLMSblf1R9eps3EHCpXti6C/cSCAhUxbiYhJbL3qPZfGh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');
$pdf->logoXsize= 2068 * 0.04;;

?>
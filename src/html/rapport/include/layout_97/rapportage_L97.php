<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/04 14:50:07 $
File Versie					: $Revision: 1.2 $

$Log: rapportage_L92.php,v $

*/

   $pdf->rapport_layout = 97; #AFF
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
        $pdf->rapport_koptext = "";//{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 58, 'g' => 126, 'b' => 201); //35/45/104 //33/43/105 //'r'=>88,'g'=>119,'b'=>255
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);//array('r'=>127,'g'=>128,'b'=>132);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_background_fill = array(200, 200, 200);
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        //$pdf->rapport_fonds_fontcolor = array('r'=>0,'g'=>73,'b'=>156);
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
        $pdf->logoSize=13;
        $pdf->logoString=base64_decode('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');
?>
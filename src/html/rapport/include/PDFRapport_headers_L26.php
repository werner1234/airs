<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/11/04 17:40:21 $
 		File Versie					: $Revision: 1.15 $

 		$Log: PDFRapport_headers_L26.php,v $
 		Revision 1.15  2017/11/04 17:40:21  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2010/07/24 12:02:53  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2010/07/21 17:36:35  rvv
 		*** empty log message ***

 		Revision 1.11  2010/07/18 17:04:44  rvv
 		*** empty log message ***

 		Revision 1.10  2010/07/14 17:33:49  rvv
 		*** empty log message ***

 		Revision 1.9  2010/07/11 16:00:05  rvv
 		*** empty log message ***

 		Revision 1.8  2010/07/07 16:10:24  rvv
 		*** empty log message ***

 		Revision 1.7  2010/06/20 16:21:45  rvv
 		*** empty log message ***

 		Revision 1.6  2010/06/06 14:11:21  rvv
 		*** empty log message ***




*/
function Header_basis_L26($object)
{
 $pdfObject = &$object;

		if(!isset($pdfObject->AliasNbPages))
	  	$pdfObject->AliasNbPages();

    if ($pdfObject->rapport_type == "BRIEF")
    {
      $pdfObject->HeaderFACTUUR();
    }
    elseif ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->HeaderFACTUUR();
    }
    elseif ($pdfObject->rapport_type == "FRONT")
    {
		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor[r],$pdfObject->rapport_kop2_fontcolor[g],$pdfObject->rapport_kop2_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

		if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast  && $pdfObject->rapport_layout != 16)
  		$pdfObject->customPageNo = 0;
    }
    else
    {
  	if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  		$pdfObject->customPageNo = 0;

		$pdfObject->customPageNo++;

		$pdfObject->SetLineWidth(0.2);

		$pdfObject->SetDrawColor($pdfObject->rapport_lijn_rood['r'],$pdfObject->rapport_lijn_rood['g'],$pdfObject->rapport_lijn_rood['b']);

		if(empty($pdfObject->top_marge))
			$pdfObject->top_marge = $pdfObject->marge;
		$pdfObject->SetY($pdfObject->top_marge);

		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor[r],$pdfObject->rapport_kop2_fontcolor[g],$pdfObject->rapport_kop2_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$y = $pdfObject->GetY();

		// default header stuff
		$pdfObject->SetX($pdfObject->marge);

		if($pdfObject->__appvar['consolidatie']['rekeningOnderdrukken'])
		{
		$pdfObject->rapport_koptext = $pdfObject->rapport_consolidatieKoptext;
		}
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleFormat}", $pdfObject->rapport_portefeuilleFormat, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Portefeuille}", $pdfObject->rapport_portefeuille, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleVoorzet}", $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Depotbank}", $pdfObject->rapport_depotbank, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{DepotbankOmschrijving}", $pdfObject->rapport_depotbankOmschrijving, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Risicoklasse}", $pdfObject->rapport_risicoklasse, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Risicoprofiel}", $pdfObject->rapport_risicoprofiel, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Client}", $pdfObject->rapport_client, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{ClientVermogensbeheerder}", $pdfObject->rapport_clientVermogensbeheerder, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Accountmanager}", $pdfObject->rapport_accountmanager, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{ModelPortefeuille}", $pdfObject->portefeuilledata['ModelPortefeuille'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{VermogensbeheerderNaam}", $pdfObject->portefeuilledata['VermogensbeheerderNaam'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{SoortOvereenkomst}", $pdfObject->portefeuilledata['SoortOvereenkomst'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{crm.naam}", $pdfObject->portefeuilledata['crm.naam'], $pdfObject->rapport_koptext);

		if($pdfObject->__appvar['consolidatie']['rekeningOnderdrukken'])
		{
	  	$pdfObject->rapport_koptext = str_replace("{Naam1}", $pdfObject->__appvar['consolidatie']['portefeuillenaam1'], $pdfObject->rapport_koptext);
	  	$pdfObject->rapport_koptext = str_replace("{Naam2}", $pdfObject->__appvar['consolidatie']['portefeuillenaam2'], $pdfObject->rapport_koptext);
		}
		else
		{
		  $pdfObject->rapport_koptext = str_replace("{Naam1}", $pdfObject->rapport_naam1, $pdfObject->rapport_koptext);
		  $pdfObject->rapport_koptext = str_replace("{Naam2}", $pdfObject->rapport_naam2, $pdfObject->rapport_koptext);
		}
		$pdfObject->rapport_liquiditeiten_omschr = str_replace("{PortefeuilleVoorzet}",  $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_liquiditeiten_omschr);

		if($pdfObject->rapport_type == "MOD")
		{
			$logopos = 85;
		}
		else
		{
			$logopos = 130;
		}

		if(is_file($pdfObject->rapport_logo))
			  $pdfObject->Image($pdfObject->rapport_logo, $logopos -33, 5, 108, 15);

		if($pdfObject->rapport_type == "MOD")
			$x = 140;
		else
			$x = 210;

		//	$pdfObject->Rect($pdfObject->marge, $pdfObject->marge, 283, 42, 'F', $border_style = null, array(200,200,200));
			$widthsBackup=$pdfObject->widths;
 $pdfObject->SetWidths(array(25,177,25,50));
 $pdfObject->SetAligns(array('L','L','L','L'));
 $pdfObject->Row(array('','','Naam',$pdfObject->rapport_naam1.' '.$pdfObject->rapport_naam2));
 $pdfObject->Row(array('','','Depotnummer',$pdfObject->rapport_portefeuille));
 $pdfObject->Row(array('','','Depotbank',$pdfObject->portefeuilledata['DepotbankOmschrijving']));
 $pdfObject->Row(array('','','Risicoprofiel',$pdfObject->portefeuilledata['Risicoklasse']));
 $y=$pdfObject->getY();

 $pdfObject->setY($y+7);

 $pdfObject->Row(array('Beheerder',$pdfObject->portefeuilledata['AccountmanagerNaam'],'',''));
 $pdfObject->Row(array('Telefoonnr',$pdfObject->portefeuilledata['VermogensbeheerderTelefoon'],'',''));


    $pdfObject->setY($y);
 	  $pdfObject->SetX(100);
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->MultiCell(100,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
		$pdfObject->setY($y+11);
		$pdfObject->widths=$widthsBackup;


}
}

	function HeaderVKM_L26($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}
	
  function HeaderVOLK_L26($object)
	  {
	    $pdfObject = &$object;
      $dataWidth=array(20,21,52,18,18,18,5,18,18,18,5,18,18,18,18);
      $splits=array(3,5,6,9,10,14);
      $n=0;
      foreach ($dataWidth as $index=>$value)
      {
        if($index<=$splits[$n])
         $kopWidth[$n] += $value;
        if($index>=$splits[$n])
         $n++;
      }

      $pdfObject->ln();
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      $pdfObject->SetWidths($kopWidth);
      $pdfObject->SetAligns(array('L','C','L','C','L','C'));
      $pdfObject->CellBorders = array('','U','','U','','U');
      $pdfObject->Row(array('',"Beginwaarde",'','Actuele waarde','','Resultaat in Euro'));
      $pdfObject->CellBorders = array();

 	 	  $pdfObject->SetWidths($dataWidth);
	    $pdfObject->SetAligns(array('R','L','L','R','R','R','R','R','R','R','R','R','R','R','R'));

      $lastColors=$pdfObject->CellFontColor;
      unset($pdfObject->CellFontColor);
	    $pdfObject->Row(array("\nAantal","\nInstr.","\nOmschrijving","\nValuta","Referentie\nKoers*","Waarde\nin Euro**",'',"Actuele\nKoers*","Waarde\nin Euro**","Aandeel in\ntotaal",'',"Koers Resultaat","Valuta Resultaat","Totaal resultaat","In %"));
      $pdfObject->CellFontColor=$lastColors;
      $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
	    $pdfObject->SetLineWidth(0.1);
    }


	  function HeaderATT_L26($object)
	  {
	    $pdfObject = &$object;

      $pdfObject->ln();
      $dataWidth=array(18,55,18,18,18,18,22,18,18,18,18,18,18);
 	 	  $pdfObject->SetWidths($dataWidth);
	    $pdfObject->SetAligns(array('L','L','L','R','R','R','R','R','R','R','R','R','R','R'));
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      $pdfObject->ln();
      $lastColors=$pdfObject->CellFontColor;
      unset($pdfObject->CellFontColor);
      $pdfObject->Row(array("Risico\nCategorie","\nFonds","\nValuta","\n".date('d-m-Y',$pdfObject->rapport_datumvanaf),"\n".date('d-m-Y',$pdfObject->rapport_datum),"\nStortingen","\nOngerealiseerd","\nGerealiseerd","\nKosten","\nResultaat","Gemiddeld\nvermogen","\nWeging","Bijdrage\nrendement"));
      $pdfObject->CellFontColor=$lastColors;
      $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
	    $pdfObject->SetLineWidth(0.1);
    }

    function HeaderOIH_L26($object)
	  {
	    $pdfObject = &$object;
	    $pdfObject->ln(1);
	    $pdfObject->SetXY($pdfObject->marge,$pdfObject->getY());
	    $pdfObject->CellBorders = array();
	    $tint=30;
	    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r']+$tint,$pdfObject->rapport_kop_bgcolor['g']+$tint,$pdfObject->rapport_kop_bgcolor['b']+$tint);

	    if($pdfObject->rapport_deel == 'overzicht')
	    {
	    $pdfObject->fillCell = array(0,0,0,0,0,0,0,0,0,0,0,0);
	 	  $pdfObject->SetWidths(array(60,22,25,25,25,25,25,25,25,25));
	 	  $pdfObject->preFillColumn($pdfObject->getY()+3,195);
	 	  $pdfObject->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
	 	  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
      $pdfObject->setY($pdfObject->getY()+3);
      $pdfObject->SetAligns(array('L','C','C','C','C','C','C','C','C','C'));
      $pdfObject->Row($pdfObject->rapport_header1);
      $pdfObject->fillCell = array();
	    $pdfObject->CellBorders = array();
	    $pdfObject->Row(array(''));
	    $pdfObject->line(8,$pdfObject->getY()-4,array_sum($pdfObject->widths)+$pdfObject->marge,$pdfObject->getY()-4);
	    $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
	    }
	    $pdfObject->SetLineWidth(0.1);
	  }

	  function HeaderTRANS_L26($object)
	  {
	    $pdfObject = &$object;

	    $pdfObject->ln(8);
	      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      //$dataWidth=array(17,16,35,11,18,17,23,17,17,18,18,21,22,18,15);
      $dataWidth=array(17,16,45,11,18,17,23,17,17,18,18,18,15,18,15);
 	 	  $pdfObject->SetWidths($dataWidth);
	    $pdfObject->SetAligns(array('L','L','L','R','R','R','R','R','R','R','R','R','L','R','R'));
      $pdfObject->Row(array("\nBoekdatum","Transactie\ntype","\nFonds","\nValuta","\nAantal","Referentie\nkoers VV","Referentie koers\nValuta","referentie\nwaarde","Koers\nin VV","\nValutakoers","\nStortingen","Gerealiseerd\nkoers","Resultaat valuta","Totaal\ngerealiseerd","\nKosten"));
      $pdfObject->SetAligns(array('L','L','L','R','R','R','R','R','R','R','R','R','R','R','R'));
      $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
      $pdfObject->SetLineWidth(0.1);
	  }

	  function HeaderPERF_L26($object)
	  {
	    $pdfObject = &$object;
	    $pdfObject->ln(8);
	      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      //$dataWidth=array(40,24,24,24,24,24,24,24,24,24,15);
       $dataWidth=array(40,25,25,30,30,30,25,25,25,15);
 	 	  $pdfObject->SetWidths($dataWidth);
	    $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
     //  $pdfObject->Row(array("",date('d-m-Y',$pdfObject->rapport_datumvanaf),date('d-m-Y',$pdfObject->rapport_datum),"Stortingen","Onttrekkingen","gemiddeld vermogen","Bruto Resultaat","Bruto\nRendement","weging","Bijdrage a/h\nRendement"," "));
     $pdfObject->Row(array("\nRisicocategorie","\n".date('d-m-Y',$pdfObject->rapport_datumvanaf),"\n".date('d-m-Y',$pdfObject->rapport_datum),"Stortingen en Onttrekkingen","Gemiddeld\nvermogen","Bruto\nResultaat","Bruto\nRendement","\nWeging","Bijdrage a/h\nRendement"," "));


    $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
    	$pdfObject->SetLineWidth(0.1);

	  }



?>
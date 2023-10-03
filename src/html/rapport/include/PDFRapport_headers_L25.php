<?php

function Header_basis_L25($object)
{
 $pdfObject = &$object;

    if(!isset($pdfObject->beeldMerk))
      $pdfObject->beeldMerk=base64_decode('iVBORw0KGgoAAAANSUhEUgAAAVcAAAFYCAMAAADQuU//AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkRBRUIwRTVFNDUwMTExRUE4MDhFQzZGQkYzRTk4MTYxIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkRBRUIwRTVGNDUwMTExRUE4MDhFQzZGQkYzRTk4MTYxIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6REFFQjBFNUM0NTAxMTFFQTgwOEVDNkZCRjNFOTgxNjEiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6REFFQjBFNUQ0NTAxMTFFQTgwOEVDNkZCRjNFOTgxNjEiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4XJDvpAAABgFBMVEXv5dWUrrTQ3OOyxtO8zdJSeoXL2eHx9PaMkX7J1tyqwM2IpKubs7jOrXzStId7mqIzZHGMqrykvMp5nLGCo7YnWmfn7fF2lp7a4+UWTFru8/SUsMD48+z4+vujuL7l1LkCPkzWu5LdxqPO2txxl6zd5ereyqv+/v5wkpowYG2zxcmctcXo2cPLuJbg6OnF1N3r3clkh43i6e7b5Oo7aXWpvcK3ytVbgov59fD07OHA0dr5+/y6ys+uw9B+oLThzrHt8fPk6+11ma6uwcYbUF5Gcn3C0dT7/P32+Pn19/iGprnq8PIPR1bbw57p7u9sj5f9/Pv7+POPrb5CbnoeU2AJQ1H2+Pq/o3TZv5nz9viIp7mlm3xJbnIORlMFQE+VlX38/f39/f73+frX4uiPqbD9/v7Z4+mTr8DAz9PW4OPf6O2Ys8OLj3qovswHQVDD09yfuMeAnqXn7e7Pr3/9+/lVdXW+0NrYvpY/a3f1+Pn+/fwAPEtvlavMqnf///////8z1vyWAAAAgHRSTlP/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////ADgFS2cAAAp/SURBVHja7N0LV1XXFQXgKQasmEQQRNFoUFHjI74FI4KPaIxI26iYhLShSHy3Jlpbm1bM+usFEl8R7j2PtfZe87LmL7jzG3vss886546D514y/bcXTfLtL00y9d2cl4CHtbmrI1jwsBZw9QMLHtYirm5gwcNayNULLHhYi7k6gQUPa0FXH7DgYS3q6gIWPKyFXT3Agoe1uKsDWPCwlnDNDwse1jKu2WHBw1rKNTcseFjLuWaGBQ9rSde8sOBhLeuaFRY8rKVdc8KCh7W8a0ZY8LBWcM0HCx7WKq7ZYMHDWsk1Fyx4WKu5ZoIFD2tF1zyw4GGt6poFFjyslV1zwIKHtbprBljwsNZwTQ8LHtY6rslhwcNayzU1LHhY67kmhgUPa03XtLDgYa3rmhQWPKy1XVPCgoe1vmtCWPCwKrimgwUPq4ZrMljwsKq4poIFD6uOayJY8LAquaaBBQ+rlmsSWPCwqrmmgAUPq55rAljwsCq62sOC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');

	  if ($pdfObject->rapport_type == "BRIEF")
    {
      $pdfObject->HeaderFACTUUR();
    }
    elseif ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->HeaderFACTUUR();
    }
    elseif ($pdfObject->rapport_type == "SCENARIO2")
    {
      
      $pageWidth = $pdfObject->w;
      $pdfObject->SetXY($pdfObject->marge,10);
      $pdfObject->SetFont($pdfObject->rapport_font,'',20);
      $pdfObject->SetTextColor($pdfObject->rapport_titel_fontcolor[0] ,$pdfObject->rapport_titel_fontcolor[1],$pdfObject->rapport_titel_fontcolor[2]);
      $pdfObject->MultiCell($pageWidth,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'L');
      
      $pdfObject->rapport_koptext = '{Naam1} {Naam2}';
      $pdfObject->rapport_koptext = str_replace("{Naam1}", $pdfObject->rapport_naam1, $pdfObject->rapport_koptext);
      $pdfObject->rapport_koptext = str_replace("{Naam2}", $pdfObject->rapport_naam2, $pdfObject->rapport_koptext);
  
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize+3);
      $pdfObject->AutoPageBreak=false;
  
      //$pdfObject->SetTextColor(255,255,255);
      $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
      $pdfObject->SetXY(0,11);
      $pdfObject->MultiCell($pdfObject->w,4,$pdfObject->rapport_koptext,0,'C');
      $pdfObject->SetXY($pageWidth-100-$pdfObject->marge,16);
      if($pdfObject->rapport_risicoklasse<>'')
        $pdfObject->MultiCell(100,4,vertaalTekst('Beleggingsprofiel',$pdfObject->rapport_taal).': '.$pdfObject->rapport_risicoklasse,0,'R');
  
      
      $pdfObject->Ln(1);
      $pdfObject->SetXY($pdfObject->marge,16);
      $pdfObject->MultiCell($pageWidth,4,vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'L');
      $pdfObject->AutoPageBreak=true;
      //$pdfObject->memImage($pdfObject->beeldMerk,$pageWidth-16,$pageHeight-14,8);
  
      $pdfObject->setDrawColor($pdfObject->grijsBlauw[0],$pdfObject->grijsBlauw[1],$pdfObject->grijsBlauw[2]);
      $pdfObject->line($pdfObject->marge, 24, $pageWidth-$pdfObject->marge, 24);
      //$pdfObject->line($pdfObject->marge, $pageHeight-18, $pageWidth-$pdfObject->marge, $pageHeight-18);
      $pdfObject->setDrawColor(0,0,0);
    }
    elseif ($pdfObject->rapport_type == "MODEL" || $pdfObject->rapport_type == "PORTAL")
    {
    
    }
	  elseif ($pdfObject->rapport_type == "ORDERP" || $pdfObject->rapport_type == "ORDERL")
	  {

			if($pdfObject->rapport_type == 'ORDERP')
		  {
		  	$width = 210;
	  	}
	  	else
		  {
			  $width=297;
	  	}
			$pdfObject->Image($pdfObject->rapport_logo,$width/2-20,4,40);
		//$pdfObject->memImage($image,$width-16,198,8);
		  $query="SELECT Adres,Woonplaats FROM Vermogensbeheerders WHERE Vermogensbeheerder='".$pdfObject->portefeuilledata['Vermogensbeheerder']."'";
		  $db=new DB();
		  $db->SQL($query);
		  $verm=$db->lookupRecord();
		  if($pdfObject->page==1)
			{
				$pdfObject->SetFont('arial','B',10);
				$pdfObject->MultiCell($width - 2 * $pdfObject->marge, 5, $pdfObject->portefeuilledata['VermogensbeheerderNaam']."\n", 0, 'R');
				$pdfObject->SetFont('arial','',10);
				$pdfObject->MultiCell($width - 2 * $pdfObject->marge, 5, $verm['Adres'] .
																														"\n" . $verm['Woonplaats'] .
																														"\n T:" . $pdfObject->portefeuilledata['VermogensbeheerderTelefoon'] .
																														"\n E:" . $pdfObject->portefeuilledata['VermogensbeheerderEmail'], 0, 'R');

			}
		  else
			{
				$pdfObject->SetFont('arial','B',10);
				$pdfObject->MultiCell($width - 2 * $pdfObject->marge, 5, $pdfObject->portefeuilledata['VermogensbeheerderNaam'], 0, 'R');
			}
			$pdfObject->SetY(25);
	  }
    elseif ($pdfObject->rapport_type == "FRONT")
    {
		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

		if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  		$pdfObject->customPageNo = 0;
    }
    else
    {
      
      		// achtergrond kleur
  //  $hoogte=12;
	//	$pdfObject->SetFillColor($pdfObject->rapport_kopvoet_bg[0],$pdfObject->rapport_kopvoet_bg[1],$pdfObject->rapport_kopvoet_bg[2]);
	//	$pdfObject->Rect(0, 0, 297, $hoogte , 'F');
  //  $pdfObject->Rect(0, 210-$hoogte, 297, $hoogte , 'F');

    	if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  		  $pdfObject->customPageNo = 0;

		$pdfObject->customPageNo++;

		$pdfObject->SetLineWidth($pdfObject->lineWidth);

		if(empty($pdfObject->top_marge))
			$pdfObject->top_marge = $pdfObject->marge;
		$pdfObject->SetY($pdfObject->top_marge);

		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$y = $pdfObject->GetY();

		// default header stuff
		$pdfObject->SetX($pdfObject->marge);
  
		if ($pdfObject->rapport_clientVermogensbeheerderReal <> '')
    {
      $pdfObject->rapport_koptext = "{ClientVermogensbeheerder} - {Naam1} {Naam2}";
    }
    else
    {
      $pdfObject->rapport_koptext = "{Portefeuille} - {Naam1} {Naam2}";
    }
    
    if ($pdfObject->rapport_type == "MOD" && $_POST['mutatieportefeuille_customNaam'] <> '')
    {
      $pdfObject->rapport_koptext = $_POST['mutatieportefeuille_customNaam'] . " - {Naam1} {Naam2}";
    }
    elseif ($pdfObject->rapport_type == "SCENARIO" && $pdfObject->scenarioProspect==true)
    {
      $pdfObject->rapport_koptext = '{Naam1} {Naam2}';
    }
  
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
		$pdfObject->rapport_koptext = str_replace("{ClientVermogensbeheerder}", $pdfObject->rapport_clientVermogensbeheerderReal, $pdfObject->rapport_koptext);
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


		if($pdfObject->CurOrientation == "P")
		{
			$pageWidth = 210;
			$pageHeight=297;
		}
		else
		{
			$pageWidth = 297;
			$pageHeight=210;
		}

    $pdfObject->AutoPageBreak=false;
    
  	//$pdfObject->SetTextColor(255,255,255);
    $pdfObject->SetXY($pageWidth-100-$pdfObject->marge,16);
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		if($pdfObject->rapport_risicoklasse<>'')
		  $pdfObject->MultiCell(100,4,vertaalTekst('Beleggingsprofiel',$pdfObject->rapport_taal).': '.$pdfObject->rapport_risicoklasse,0,'R');
    $pdfObject->SetXY($pdfObject->marge,$pageHeight-15);
    $pdfObject->MultiCell(200,4,$pdfObject->rapport_koptext,0,'L');
    
       
    $pdfObject->SetXY(0,$pageHeight-12);
    $pdfObject->MultiCell($pageWidth,4,$pdfObject->customPageNo,0,'C');//vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
 	  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);


    $pdfObject->SetXY($pdfObject->marge,10);
		$pdfObject->SetFont($pdfObject->rapport_font,'',20);
    $pdfObject->SetTextColor($pdfObject->rapport_titel_fontcolor[0] ,$pdfObject->rapport_titel_fontcolor[1],$pdfObject->rapport_titel_fontcolor[2]);
		$pdfObject->MultiCell($pageWidth,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'L');


    $pdfObject->SetTextColor(0);
   
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->Ln(1);
    $pdfObject->SetXY($pdfObject->marge,16);
    if($pdfObject->rapport_type == "TRANS" || $pdfObject->rapport_type == "MUT")
  		$pdfObject->MultiCell($pageWidth,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'L');
    else
      $pdfObject->MultiCell($pageWidth,4,vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'L');
    $pdfObject->AutoPageBreak=true;

		if(is_file($pdfObject->rapport_logo))
		{ 
    	//	  1240,117	//	  548,117
  //    $pdfObject->Image($pdfObject->rapport_logo,$pageWidth-50,$pageHeight-12-22,40);
		}
			$pdfObject->memImage($pdfObject->beeldMerk,$pageWidth-16,$pageHeight-14,8);
  
    $pdfObject->setDrawColor($pdfObject->grijsBlauw[0],$pdfObject->grijsBlauw[1],$pdfObject->grijsBlauw[2]);
		$pdfObject->line($pdfObject->marge, 24, $pageWidth-$pdfObject->marge, 24);
		$pdfObject->line($pdfObject->marge, $pageHeight-18, $pageWidth-$pdfObject->marge, $pageHeight-18);
      $pdfObject->setDrawColor(0,0,0);
		if($pdfObject->CurOrientation == "P")
			$x = 160;
		else
			$x = 250;

    $pdfObject->AutoPageBreak=false;
    $pdfObject->SetY(-10);
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_voetfontsize);
    $pdfObject->MultiCell(240,4,$pdfObject->rapport_voettext,'0','L');
    $pdfObject->AutoPageBreak=true;

		$pdfObject->SetXY($x,23);

			//$pdfObject->MultiCell(40,4,vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo."\n\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
	    $pdfObject->SetX(100);

  }
	$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
  $pdfObject->headerStart = $pdfObject->getY()+12;
  if(isset($pdfObject->lastPortefeuille) && $pdfObject->rapport_portefeuille <> $pdfObject->lastPortefeuille)
  {
    //echo $pdfObject->rapport_portefeuille." <> ".$pdfObject->lastPortefeuille." ".$pdfObject->page."<br>\n";
    $pdfObject->rapportNewPage = $pdfObject->page;
  }
  $pdfObject->lastPortefeuille=$pdfObject->rapport_portefeuille;
}


function HeaderFRONT_L25($object)
{

}

function HeaderPORTAL_L25($object)
{

}

function HeaderINHOUD_L25($object)
{

}
  
  function HeaderSCENARIO_L25($object)
{

}
function HeaderSCENARIO2_L25($object)
{

}


function HeaderDOORKIJKVR_L25($object)
{

}

function HeaderORDERP_L25($object)
{

}

function HeaderJOURNAAL_L25($object)
{
exit;
}

function HeaderKERNZ_L25($object)
{

}

function HeaderKERNV_L25($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  $dataWidth=array(28,55,20,20,20,20,22,22,22,18,20,15);
  $pdfObject->SetWidths($dataWidth);
  $pdfObject->SetAligns(array('L','L','R','R','R','R','R','R','R','R','R','R','R'));
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->ln();
  $lastColors=$pdfObject->CellFontColor;
  unset($pdfObject->CellFontColor);
  $pdfObject->Row(array(vertaalTekst("Risico\nCategorie",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Fonds",$pdfObject->rapport_taal),
                    "\n".date('d-m-Y',$pdfObject->rapport_datumvanaf),
                    "\n".date('d-m-Y',$pdfObject->rapport_datum),
                    "\n".vertaalTekst("Mutaties",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Resultaat",$pdfObject->rapport_taal),
                    vertaalTekst("Gemiddeld vermogen",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Resultaat %",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Weging",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Bijdrage",$pdfObject->rapport_taal)."\n".vertaalTekst("rendement",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Benchmark",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Verschil",$pdfObject->rapport_taal)));
  $pdfObject->CellFontColor=$lastColors;
  $pdfObject->setDrawColor($pdfObject->grijsBlauw[0],$pdfObject->grijsBlauw[1],$pdfObject->grijsBlauw[2]);
  $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
  $pdfObject->SetLineWidth(0.1);
  $pdfObject->setDrawColor(0,0,0);
  if(is_array($pdfObject->widthsBackup))
    $pdfObject->widths=$pdfObject->widthsBackup;
  // listarray($pdfObject->widths);echo "new page <br>\n";
}

  function HeaderOIB_L25($object)
	{
    $pdfObject = &$object;
	  $pdfObject->ln();
	
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	//	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),297-$pdfObject->marge ,$pdfObject->GetY());
  }

function HeaderGRAFIEK_L25($object)
{
  $pdfObject = &$object;
}

function HeaderOIV_L25($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
 // $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),297-$pdfObject->marge ,$pdfObject->GetY());
}

function HeaderRISK_L25($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderHUIS_L25($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}
function HeaderMOD_L25($object)
{
	$pdfObject = &$object;
	$pdfObject->ln();
	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

	$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3];
	$eindhuidige 	= array_sum($pdfObject->widthB);

	// achtergrond kleur
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 12 , 'F');

	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

	// lijntjes onder beginwaarde in het lopende jaar

	$pdfObject->SetX($pdfObject->marge+$huidige+5);
	$pdfObject->MultiCell(90,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0, "C");

	$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());

	$tmpY = $pdfObject->GetY();

	$pdfObject->SetY($tmpY);
	$pdfObject->SetX($pdfObject->marge);

	$pdfObject->SetWidths($pdfObject->widthB);
	$pdfObject->SetAligns($pdfObject->alignB);
	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

	$pdfObject->row(array("","\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
										vertaalTekst("ISIN",$pdfObject->rapport_taal),
										vertaalTekst("Valuta",$pdfObject->rapport_taal),
										vertaalTekst("Aantal",$pdfObject->rapport_taal),
										vertaalTekst("Per stuk \nin valuta",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille \nin valuta",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille \nin EUR",$pdfObject->rapport_taal),
										vertaalTekst("In % Totaal",$pdfObject->rapport_taal)));

	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);

	$pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
	$pdfObject->setY($pdfObject->GetY()-8);
	$pdfObject->row(array(vertaalTekst("Categorie",$pdfObject->rapport_taal)));
	$pdfObject->ln();

	$pdfObject->SetWidths($pdfObject->widthB);
	$pdfObject->SetAligns($pdfObject->alignB);

	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

	$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderATT_L25($object)
	{
    $pdfObject = &$object;
    $colwidth=(297-3-2*$pdfObject->marge)/8;
    $pdfObject->widthA = array($colwidth,$colwidth,$colwidth,$colwidth,$colwidth,$colwidth,$colwidth,$colwidth+3);
		$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R');

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

		for($i=0;$i<count($pdfObject->widthA);$i++)
		  $pdfObject->fillCell[] = 1;

/*
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->ln(1);
		$pdfObject->Cell(100,4, '',0,0); //vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal)
		$pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("t/m",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
    $pdfObject->ln(1);
*/
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->ln();
    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
		$pdfObject->row(array("Maand",
		                      "Beginvermogen (€)",
		                      "Stortingen (€)",
                          "Onttrekkingen (€)",
		                      "Beleggingsresultaat (€)",
		                     	"Eindvermogen (€)",
		                      "Rendement (maand %)",
		                      "Rendement (Cumulatief %)"));
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);                      
    $sumWidth = array_sum($pdfObject->widthA);
	  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
	}

function HeaderAFM_L25($object)
{
  $pdfObject = &$object;
  $colwidth=(297-3-2*$pdfObject->marge)/8;
  $pdfObject->widthA = array($colwidth,$colwidth,$colwidth,$colwidth,$colwidth,$colwidth,$colwidth,$colwidth+3);
  $pdfObject->alignA = array('L','R','R','R','R','R','R','R','R');

  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);

  for($i=0;$i<count($pdfObject->widthA);$i++)
    $pdfObject->fillCell[] = 1;

  /*
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      $pdfObject->ln(1);
      $pdfObject->Cell(100,4, '',0,0); //vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal)
      $pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("t/m",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
      $pdfObject->ln(1);
  */
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
  $pdfObject->row(array("Maand",
    "Beginvermogen (€)",
    "Stortingen (€)",
    "Onttrekkingen (€)",
    "Beleggingsresultaat (€)",
    "Eindvermogen (€)",
    "Rendement (maand %)",
    "Rendement (Cumulatief %)"));
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $sumWidth = array_sum($pdfObject->widthA);
  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
}

  function HeaderPERFG_L25($object)
	{
    $pdfObject = &$object;
    $colwidth=(297-3-2*$pdfObject->marge)/8;
    $pdfObject->widthA = array($colwidth,$colwidth,$colwidth,$colwidth,$colwidth,$colwidth,$colwidth,$colwidth+3);
    $pdfObject->alignA = array('L','R','R','R','R','R','R','R');
    
    $pdfObject->SetWidths($pdfObject->widthA);
    $pdfObject->SetAligns($pdfObject->alignA);
    
    for($i=0;$i<count($pdfObject->widthA);$i++)
      $pdfObject->fillCell[] = 1;


  }
  
  	function HeaderVKM_L25($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}

function HeaderVKMS_L25($object)
{
  $pdfObject = &$object;
  $pdfObject->ln(8);
  //$pdfObject->HeaderVKM();
}

function HeaderVKMA_L25($object)
{
  $pdfObject = &$object;

}

function HeaderZORG_L25($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->Row(array('Fonds','Aantal','Koers',"Portefeuille\nwaarde EUR",'Percentage','Zorgwaarde EUR'));
  $pdfObject->Line($pdfObject->marge ,$pdfObject->GetY(), $pdfObject->w-$pdfObject->marge ,$pdfObject->GetY());
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  
}

function HeaderHSE_L25($object)
{
	$pdfObject = &$object;
	$pdfObject->HeaderHSE();
}

function HeaderEND_L25($object)
{
	$pdfObject = &$object;
	
}

function HeaderFiscaal_L25($object)
{
	$pdfObject = &$object;
	$pdfObject->HeaderFiscaal();
}

function HeaderCASHY_L25($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderINDEX_L25($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->ln();
}
  function HeaderPERF_L25($object)
	{
    $pdfObject = &$object;
	  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->ln();
	  $pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
	//	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),297-$pdfObject->marge,$pdfObject->GetY());
  }
  
  function HeaderMUT_L25($object)
	{
	  $pdfObject = &$object;
  	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$pdfObject->ln();
		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->row(array(vertaalTekst("Periode",$pdfObject->rapport_taal),
										 vertaalTekst("Bankafschrift",$pdfObject->rapport_taal),
										 vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
										 vertaalTekst("Boekdatum",$pdfObject->rapport_taal),
										 vertaalTekst("Rekening",$pdfObject->rapport_taal),
										 "",
										 vertaalTekst("Debet",$pdfObject->rapport_taal),
										 vertaalTekst("Credit",$pdfObject->rapport_taal),
										 ""));

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
    
    //$pdfObject->setDrawColor($pdfObject->grijsBlauw[0],$pdfObject->grijsBlauw[1],$pdfObject->grijsBlauw[2]);
    //$pdfObject->setDrawColor(0);
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
    //$pdfObject->setDrawColor(0,0,0);
		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  }  
  
	  function HeaderVOLK_L25($object,$type='VOLK')
	  {
	    $pdfObject = &$object;
	    if($pdfObject->resetHeader==true)
	      $widthBackup=$pdfObject->widths;
      if($type=='VAR')
      {
        $dataWidth = array(60,15, 15, 11, 20,23, 2, 21,22,20,20, 2, 17, 20, 17);// 18->30
        $splits = array(3, 5, 6, 10, 11, 14);
      }
      else
      {
        $dataWidth = array(63, 25, 11, 20, 25, 5, 23, 25, 20, 5, 18, 25, 18);// 18->30
        $splits = array(2, 4, 5, 8, 9, 12);
      }
      $n=0;
      $kopWidth=array();
      foreach ($dataWidth as $index=>$value)
      {
        if($index<=$splits[$n])
         $kopWidth[$n] += $value;
        if($index>=$splits[$n])
         $n++;
      }
      $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
      $pdfObject->ln();
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      $pdfObject->SetWidths($kopWidth);
      $pdfObject->SetAligns(array('L','C','L','C','L','C'));
      $pdfObject->CellBorders = array('','U','','U','','U');
      if($type=='VHO' || $type=='VAR')
        $begin='Historische kostprijs';
      else
        $begin='Beginwaarde van lopende jaar';
      if($type=='VAR')
        $pdfObject->Row(array('',$begin,'','Resultaat','',"Actuele waardes"));
      else
        $pdfObject->Row(array('',$begin,'','Resultaat (incl. rente/dividend)','',"Actuele waardes"));
      $pdfObject->CellBorders = array();

 	 	  $pdfObject->SetWidths($dataWidth);
	    
	    if($type=='VAR')
      {
        $pdfObject->SetAligns(array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R'));
        $pdfObject->Row(array('Categorie/Effect', 'Uitgifte', 'Aantal', 'Valuta', 'Koers', "Portefeuille\nin EUR", "", "Koersresultaat\nin EUR","Valutaresultaat\nin EUR", "Uitkeringen in EUR", "Resultaat in %", '', 'Koers', "Portefeuille\nin EUR", "in % van\nVermogen"));
      }
      else
      {
        $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R','R'));
        $pdfObject->Row(array('Categorie/Effect', 'Aantal', 'Valuta', 'Koers', "Portefeuille\nin EUR", "", "Koersresultaat\nin EUR", "Rente / Dividend in EUR", "Resultaat in %", '', 'Koers', "Portefeuille\nin EUR", "in % van\nVermogen"));
      }
	    $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
	    //$pdfObject->HeaderVOLK();
      if($pdfObject->resetHeader==true)
        $pdfObject->widths=$widthBackup;
    }

    function HeaderVOLKNB_L25($object,$type='VOLK')
    {
      $pdfObject = &$object;
      $dataWidth=array(80,18,12,20,35,5,17,20,18,5,20,20,15);
      $splits=array(3,4,5,8,9,12);
      $n=0;
      $kopWidth=array();
      foreach ($dataWidth as $index=>$value)
      {
        if($index<=$splits[$n])
          $kopWidth[$n] += $value;
        if($index>=$splits[$n])
        $n++;
      }
      if($type=='VHO' || $type =='VAR')
        $begin='Historische kostprijs';
      else
        $begin='Beginwaarde lop. jaar';
      
      if($type=='VAR')
      {
        $tweedeKop='Resultaat (incl. rente/dividend)';
        $derdeKop='Actuele waardes';
      }
      else
      {
        $tweedeKop='Actuele waardes';
        $derdeKop='Resultaat';
      }
  
      $pdfObject->ln();
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      $pdfObject->SetWidths($kopWidth);
     $pdfObject->SetAligns(array('L','C','L','C','L','C'));
      $pdfObject->CellBorders = array('','U','','U','','U');
      $pdfObject->Row(array('',$begin,'',$tweedeKop,'',$derdeKop));
      $pdfObject->CellBorders = array();
  
      $pdfObject->SetWidths($dataWidth);
      $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R','R'));
      if($type=='VAR')
        $pdfObject->Row(array('Categorie/Effect','Uitgifte','Aantal','Valuta','Koers',"Portefeuille\nin EUR","","Koersresultaat\nin EUR","Rente / Dividend in EUR","Resultaat in %",'','Koers',"Portefeuille\nin EUR","in % van\nVermogen"));
      else
        $pdfObject->Row(array('Categorie/Effect','Uitgifte','Aantal','Valuta',"Portefeuille in EUR",'','Koers',"Portefeuille\nin EUR","in % van\nVermogen",'','Koers Resultaat','Uitkering',"Totaal\nin %"));
  
      $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
  //$pdfObject->HeaderVOLK();
    }


function HeaderVOLKD_L25($object)
{
	$pdfObject = &$object;
	$dataWidth=array(66,23,12,20,22,18,9,18,17,15,15,9,20,18);
	$splits=array(2,5,6,10,11,13);
	$n=0;
  $kopWidth=array();
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
	$pdfObject->SetAligns(array('L','C','L','C','C','C','C'));
	$pdfObject->CellBorders = array('','U','','U','','U');
	$x=$pdfObject->marge;
	$oldx=0;
	foreach($pdfObject->CellBorders as $index=>$teken)
	{
		$x+=$kopWidth[$index];

		if($teken=='U')
		{

			$pdfObject->Line($oldx,$pdfObject->GetY()+4,$x,$pdfObject->GetY()+4);
		}

		$oldx=$x;
	}

	$pdfObject->CellBorders = array();
	$pdfObject->Row(array('',"Actuele waardes",'','Rendement lopend jaar','','Historisch resultaat'));


	$pdfObject->SetWidths($dataWidth);
	$pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R','R'));
	$pdfObject->Row(array('Categorie/Effect','Aantal','Valuta','Koers',"Portefeuille\nin EUR","in % van\nVermogen",'','Koers begin jaar','Koers Resultaat','Rente / Dividend',"Totaal\nin %","","Historische\nkostprijs","Historisch\nin %"));

	$pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
	//$pdfObject->HeaderVOLK();
}

   function HeaderTRANS_L25($object)
   {
    $pdfObject = &$object;
    $pdfObject->Ln();
  	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		// achtergrond kleur

		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
     $pdfObject->SetTextColor(0);
				// afdrukken header groups
		$inkoop			= $pdfObject->marge + $pdfObject->widthB[0] + $pdfObject->widthB[1] + $pdfObject->widthB[2] + $pdfObject->widthB[3];
		$inkoopEind = $inkoop + $pdfObject->widthB[4] + $pdfObject->widthB[5] + $pdfObject->widthB[6];

		$verkoop			= $inkoopEind;
		$verkoopEind = $verkoop + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];

		$resultaat			= $verkoopEind;
		$resultaatEind = $pdfObject->marge + array_sum($pdfObject->widthB);

			$pdfObject->SetX($inkoop);
			$pdfObject->Cell($inkoopEind - $inkoop,4, vertaalTekst("Gegevens inzake aankoop",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($verkoop);
			$pdfObject->Cell($verkoopEind - $verkoop,4, vertaalTekst("Gegevens inzake verkoop",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($resultaat);
			$pdfObject->Cell($resultaatEind - $resultaat,4, vertaalTekst("Resultaat bepaling",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->ln();
     $pdfObject->setDrawColor($pdfObject->grijsBlauw[0],$pdfObject->grijsBlauw[1],$pdfObject->grijsBlauw[2]);
		$pdfObject->Line(($inkoop+2),$pdfObject->GetY(),$inkoopEind,$pdfObject->GetY());
		$pdfObject->Line(($verkoop+2),$pdfObject->GetY(),$verkoopEind,$pdfObject->GetY());
		$pdfObject->Line(($resultaat+2),$pdfObject->GetY(),$resultaatEind,$pdfObject->GetY());
     $pdfObject->setDrawColor(0);
		// bij layout 1 zit het % totaal
		if($pdfObject->rapport_TRANS_procent == 1)
			$procentTotaal = "%";

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

			$pdfObject->row(array(vertaalTekst("Datum",$pdfObject->rapport_taal),
										 vertaalTekst("Aan/ Ver Koop",$pdfObject->rapport_taal),
										 vertaalTekst("Aantal",$pdfObject->rapport_taal),
										 vertaalTekst("Fonds",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop waarde in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Verkoop koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Verkoop waarde in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Verkoop waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Historische kostprijs in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Koersresultaat voorafgaand verslagperiode",$pdfObject->rapport_taal),
										 vertaalTekst("Koersresultaat gedurende verslagperiode",$pdfObject->rapport_taal),
										 $procentTotaal));
	   	$pdfObject->SetWidths($pdfObject->widthA);
	   	$pdfObject->SetAligns($pdfObject->alignA);
    	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
   }
   
   
function HeaderMODEL_L25($object)
  {

    $pdfObject = &$object;

    	$pdfObject->SetFont("Times","b",16);
  			$pdfObject->SetX($pdfObject->marge);
				$pdfObject->Cell(200,8, vertaalTekst("Modelcontrole", $pdfObject->rapport_taal) ,0,1,"L");
				$pdfObject->SetX(250);

				$pdfObject->SetFont("Times","b",10);
				$pdfObject->SetX($pdfObject->marge);
				//rij 3
				$pdfObject->SetFont("Times","b",10);
				$pdfObject->Cell(70,4, "Controledatum: ",0,0,"R");
				$pdfObject->SetFont("Times","",10);
				$pdfObject->Cell(50,4, date("j",$pdfObject->selectData['datumTm'])." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->tmdatum)],$pdfObject->taal)." ".date("Y",$pdfObject->selectData[datumTm]),0,1,"L");

				$pdfObject->SetFont("Times","b",10);
				$pdfObject->Cell(70,4, "Modelportefeuille: ",0,0,"R");
				$pdfObject->SetFont("Times","",10);
				$pdfObject->Cell(50,4, $pdfObject->selectData['modelcontrole_portefeuille'],0,1,"L");
				$pdfObject->SetFont("Times","b",10);

				if($pdfObject->selectData['modelcontrole_rapport'] == "vastbedrag")
				{
					$pdfObject->Cell(70,4, "Vast bedrag: ",0,0,"R");
					$pdfObject->SetFont("Times","",10);
					$pdfObject->Cell(50,4, $pdfObject->selectData['modelcontrole_vastbedrag'],0,1,"L");
				}
				else
				{

					if($pdfObject->selectData["modelcontrole_filter"] != "gekoppeld")
					{
						$extraTekst = " : niet gekoppeld depot";
					}
					else
						$extraTekst = "";

					$pdfObject->Cell(70,4, "Client: ",0,0,"R");
					$pdfObject->SetFont("Times","",10);
					$pdfObject->Cell(50,4, $pdfObject->clientOmschrijving,0,1,"L");

					$pdfObject->SetFont("Times","b",10);
					$pdfObject->Cell(70,4, "Naam: ",0,0,"R");
					$pdfObject->SetFont("Times","",10);
					$pdfObject->Cell(50,4, $pdfObject->naamOmschrijving.$extraTekst,0,1,"L");

				}

				$pdfObject->ln();
				$pdfObject->SetWidths(array(62,30,20,20,20,20,25,25,5,27,25));
				$pdfObject->SetAligns(array("L","L","R","R","R","R","R","R","R","R","R","R","R"));
				$pdfObject->Row(array("Fonds","ISIN-code",
												 "Model Percentage",
												 "Werkelijk Percentage",
												 "Grootste afwijking",
												 "Kopen",
												 "Verkopen",
												 "Overschrijding waarde EUR",
												 "",
												 "Waarde volgens percentage model",
												 "Koers in locale valuta"));

				$pdfObject->Line($pdfObject->marge ,$pdfObject->GetY(), $pdfObject->marge + 280,$pdfObject->GetY());
				$pdfObject->SetFont("Times","",10);

  }
  
function HeaderVHO_L25($object)
{
  $pdfObject = &$object;
  HeaderVOLK_L25($pdfObject,'VHO');
}

function HeaderVHONB_L25($object)
{
  $pdfObject = &$object;
  HeaderVOLKNB_L25($pdfObject,'VHO');
}

function HeaderVAR_L25($object)
{
  $pdfObject = &$object;
  HeaderVOLK_L25($pdfObject,'VAR');
}

if(!function_exists('printAEXVergelijking'))
{
	function printAEXVergelijking($object,$vermogensbeheerder, $rapportageDatumVanaf, $rapportageDatum)
	{
    $pdfObject = &$object;
    /*
    if(strpos($pdfObject->portefeuilledata['ModelPortefeuille'],'SRI')!==false)
    {
      $filter='AND specialeIndex=1';
    }
    else
    {
      $filter = 'AND specialeIndex=0';
    }
    */
    $filter='';

	  $query = "SELECT Indices.Beursindex, Fondsen.Omschrijving, Fondsen.Valuta
FROM Indices
 JOIN Fondsen ON  Indices.Beursindex = Fondsen.Fonds
WHERE  Indices.Vermogensbeheerder = '".$pdfObject->portefeuilledata['Vermogensbeheerder']."'  $filter ORDER BY Afdrukvolgorde";

    $border=0;
		$DB  = new DB();
		$DB2 = new DB();

		$DB->SQL($query);
		$DB->Query();
		$regels = $DB->records();
		$hoogte = ($regels * 4) + 8;
		if(($pdfObject->GetY() + $hoogte) > $pdfObject->pagebreak)
		{
			$pdfObject->AddPage();
			$pdfObject->ln();
		}

		$perfEur = 0;
		$perfVal = 1;
		$perfJan = 0;

		if($pdfObject->rapport_perfIndexJanuari == true)
	  {
	    $julRapDatumVanaf = db2jul($rapportageDatumVanaf);
	    $rapJaar = date('Y',$julRapDatumVanaf);
	    $dagMaand = date('d-m',$julRapDatumVanaf);
	    $januariDatum = $rapJaar.'-01-01';
	    	    if($dagMaand =='01-01')
        $pdfObject->rapport_perfIndexJanuari = false;
	  }
		if($pdfObject->rapport_printAEXVergelijkingEur == 1)
		{
		  $extraX = 26;
		  $perfEur = 1;
		  $perfVal = 0;
		  $perfJan = 0;
		}
		if($pdfObject->rapport_perfIndexJanuari == true)
	  {
		  $perfEur = 0;
		  $perfVal = 0;
		  $perfJan = 1;
	  }

	  if($pdfObject->printAEXVergelijkingProcentTeken)
	    $teken = '%';
	  else
	    $teken = '';


		if($pdfObject->rapport_perfIndexJanuari == true)
		  $extraX += 51;

		$pdfObject->ln();
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge,$pdfObject->getY(),130+9+$extraX,$hoogte,'F');
		$pdfObject->SetFillColor(0);
		$pdfObject->Rect($pdfObject->marge,$pdfObject->getY(),130+9+$extraX,$hoogte);
		$pdfObject->SetX($pdfObject->marge);

		// kopfontcolor
		//$pdfObject->SetTextColor($pdfObject->rapport_kop4_fontcolor['r'],$pdfObject->rapport_kop4_fontcolor['g'],$pdfObject->rapport_kop4_fontcolor['b']);
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_kop4_font,$pdfObject->rapport_kop4_fontstyle,$pdfObject->rapport_kop4_fontsize);
		$pdfObject->Cell(60,4, vertaalTekst("Index-vergelijking",$pdfObject->rapport_taal), 0,0, "L");

		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_fontstyle,$pdfObject->rapport_fontsize);
		//$pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor['r'],$pdfObject->rapport_fonds_fontcolor['g'],$pdfObject->rapport_fonds_fontcolor['b']);
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		if($pdfObject->rapport_perfIndexJanuari == true)
			$pdfObject->Cell(26,4, date("d-m-Y",db2jul($januariDatum)), $border,0, "R");
		$pdfObject->Cell(26,4, date("d-m-Y",db2jul($rapportageDatumVanaf)), $border,0, "R");
		$pdfObject->Cell(26,4, date("d-m-Y",db2jul($rapportageDatum)), $border,0, "R");

	  $pdfObject->Cell(26,4, vertaalTekst("Perf in %",$pdfObject->rapport_taal), $border,$perfVal, "R");

		if($pdfObject->rapport_printAEXVergelijkingEur == 1)
		  $pdfObject->Cell(26,4, vertaalTekst("Perf in % in EUR",$pdfObject->rapport_taal), $border,$perfEur, "R");
		if($pdfObject->rapport_perfIndexJanuari == true)
			$pdfObject->Cell(26,4, vertaalTekst("Jaar Perf.",$pdfObject->rapport_taal), $border,$perfJan, "R");

/*
		$query3 = "SELECT Portefeuilles.SpecifiekeIndex, Fondsen.Omschrijving, Fondsen.Valuta FROM Portefeuilles, Fondsen WHERE Portefeuilles.SpecifiekeIndex = Fondsen.Fonds AND Portefeuilles.Portefeuille = '". $pdfObject->rapport_portefeuille."' ";
		$DB2->SQL($query3);
		$DB2->Query();
		$perf = $DB2->nextRecord();
		if($perf['SpecifiekeIndex']<>'')
		{
			if($pdfObject->rapport_perfIndexJanuari == true)
			{
				$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$januariDatum."' AND Fonds = '".$perf['SpecifiekeIndex']."'  ORDER BY Datum DESC LIMIT 1";
				$DB2->SQL($q);
				$DB2->Query();
				$koers0 = $DB2->LookupRecord();
			}

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatumVanaf."' AND Fonds = '".$perf['SpecifiekeIndex']."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers1 = $DB2->LookupRecord();

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatum."' AND Fonds = '".$perf['SpecifiekeIndex']."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers2 = $DB2->LookupRecord();
			$tag='';
			if(substr($koers2['Datum'],0,10) <> substr($rapportageDatum,0,10))
			{
				$rapJul=db2jul($rapportageDatum);
				$dag=date('w',$rapJul);
				$dagenAchter=($rapJul-db2jul($koers2['Datum']))/86400;
				if($dagenAchter>3)
					$tag='*';
				elseif($dag <> 0 && $dag <> 6)
				  $tag='*';
			}


			$performanceJaar = ($koers2['Koers'] - $koers0['Koers']) / ($koers0['Koers']/100 );
			$performanceEur = ($koers2['Koers']*$valutaKoersStop['Koers'] - $koers1['Koers']*$valutaKoersStart['Koers']) / ($koers1['Koers']*$valutaKoersStart['Koers']/100 );

			//$performance=getFondsPerformanceGestappeld($perf['SpecifiekeIndex'],$pdfObject->rapport_portefeuille,$rapportageDatumVanaf,$rapportageDatum);
			$performance=getFondsPerformance($perf['SpecifiekeIndex'],$rapportageDatumVanaf,$rapportageDatum);

			$pdfObject->Cell(60,4, $perf[Omschrijving], 0,0, "L");
			if($pdfObject->rapport_perfIndexJanuari == true)
				$pdfObject->Cell(26,4, $pdfObject->formatGetal($koers0['Koers'],2), $border,0, "R");
			$pdfObject->Cell(26,4, $pdfObject->formatGetal($koers1['Koers'],2), $border,0, "R");
			$pdfObject->Cell(26,4, $pdfObject->formatGetal($koers2['Koers'],2).$tag, $border,0, "R");
			$pdfObject->Cell(26,4, $pdfObject->formatGetal($performance,2).$teken, $border,$perfVal, "R");
			if($pdfObject->rapport_printAEXVergelijkingEur == 1)
				$pdfObject->Cell(26,4, $pdfObject->formatGetal($performanceEur,2).$teken, $border,$perfEur, "R");
			if($pdfObject->rapport_perfIndexJanuari == true)
				$pdfObject->Cell(26,4, $pdfObject->formatGetal($performanceJaar,2).$teken, $border,$perfJan, "R");
		}
*/
		$tag='';
		while($perf = $DB->nextRecord())
		{
		  if($perf['Valuta'] != 'EUR')
		  {
		    if($pdfObject->rapport_perfIndexJanuari == true)
		    {
		      $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$januariDatum."' ORDER BY Datum DESC LIMIT 1 ";
		      $DB2->SQL($q);
			    $DB2->Query();
			    $valutaKoersJan = $DB2->LookupRecord();
			  }

		    $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$rapportageDatumVanaf."' ORDER BY Datum DESC LIMIT 1 ";
		    $DB2->SQL($q);
			  $DB2->Query();
			  $valutaKoersStart = $DB2->LookupRecord();

		    $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$rapportageDatum."' ORDER BY Datum DESC LIMIT 1 ";
		    $DB2->SQL($q);
			  $DB2->Query();
			  $valutaKoersStop = $DB2->LookupRecord();

		  }
		  else
		  {
		    $valutaKoersJan['Koers'] = 1;
		    $valutaKoersStart['Koers'] = 1;
		    $valutaKoersStop['Koers'] = 1;
		  }

		  if($pdfObject->rapport_perfIndexJanuari == true)
		  {
		    $q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$januariDatum."' AND Fonds = '".$perf['Beursindex']."'  ORDER BY Datum DESC LIMIT 1";
		  	$DB2->SQL($q);
		  	$DB2->Query();
		  	$koers0 = $DB2->LookupRecord();
		  }

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatumVanaf."' AND Fonds = '".$perf['Beursindex']."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers1 = $DB2->LookupRecord();

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatum."' AND Fonds = '".$perf['Beursindex']."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers2 = $DB2->LookupRecord();

			$tag='';
			if(substr($koers2['Datum'],0,10) <> substr($rapportageDatum,0,10))
			{
				$rapJul=db2jul($rapportageDatum);
				$dag=date('w',$rapJul);
				$dagenAchter=($rapJul-db2jul($koers2['Datum']))/86400;
				if($dagenAchter>3)
					$tag='*';
				elseif($dag <> 0 && $dag <> 6)
					$tag='*';
			}

			$performanceJaar = ($koers2['Koers'] - $koers0['Koers']) / ($koers0['Koers']/100 );
			$performance = ($koers2['Koers'] - $koers1['Koers']) / ($koers1['Koers']/100 );
			$performanceEur = ($koers2['Koers']*$valutaKoersStop['Koers'] - $koers1['Koers']*$valutaKoersStart['Koers']) / ($koers1['Koers']*$valutaKoersStart['Koers']/100 );
			$pdfObject->Cell(60,4, $perf['Omschrijving'], $border,0, "L");
		  if($pdfObject->rapport_perfIndexJanuari == true)
		     $pdfObject->Cell(26,4, $pdfObject->formatGetal($koers0['Koers'],2), $border,0, "R");
			$pdfObject->Cell(26,4, $pdfObject->formatGetal($koers1['Koers'],2), $border,0, "R");
			$pdfObject->Cell(26,4, $pdfObject->formatGetal($koers2['Koers'],2).$tag, $border,0, "R");
		  $pdfObject->Cell(26,4, $pdfObject->formatGetal($performance,2).$teken, $border,$perfVal, "R");
		  if($pdfObject->rapport_printAEXVergelijkingEur == 1)
		    $pdfObject->Cell(26,4, $pdfObject->formatGetal($performanceEur,2).$teken, $border,$perfEur, "R");
		  if($pdfObject->rapport_perfIndexJanuari == true)
		    $pdfObject->Cell(26,4, $pdfObject->formatGetal($performanceJaar,2).$teken, $border,$perfJan, "R");
		}
		if($tag=='*')
    {
      $pdfObject->Cell(60, 4, '* deze koers heeft een vertraging van één of meerdere dagen', 0, 0, "L");
      $pdfObject->ln();
    }



	}
}


if(!function_exists('PieChart_L25'))
{
  function PieChart_L25($pdfObject,$w,$h,$data, $format, $colors=null,$titel='',$legendaStart='')
  {
    
    $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
    $pdfObject->SetLegends($data,$format);
    
    
    $XPage = $pdfObject->GetX();
    $YPage = $pdfObject->GetY();
    //$pdfObject->debug=true;
    if($pdfObject->debug==true)
    {
      $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>'1,1'));
      $pdfObject->line($XPage+2,$YPage+$pdfObject->rowHeight-1,$XPage+2,$YPage+$pdfObject->rowHeight+4);
      $pdfObject->Rect($XPage,$YPage,$w,$h);
      $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>0));
    }
    $pdfObject->setXY($XPage,$YPage);
    $pdfObject->SetFont($pdfObject->rapport_font, 'B', 8.5);
    $pdfObject->Cell($w,4,$titel,0,1,'L');
    //$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
    
    $YPage=$YPage+$pdfObject->rowHeight+4;
    $pdfObject->setXY($XPage,$YPage);
    $margin = 4;
    $hLegend = 2;
    $radius = min($w, $h); //
    $radius = ($radius / 2)-4;
    $XDiag = $XPage + $margin + $radius;
    $YDiag = $YPage + $margin + $radius;
    if($colors == null) {
      for($i = 0;$i < $pdfObject->NbVal; $i++) {
        $gray = $i * intval(255 / $pdfObject->NbVal);
        $colors[$i] = array($gray,$gray,$gray);
      }
    }
    
    //Sectors
    $pdfObject->SetDrawColor(255,255,255);
    $pdfObject->SetLineWidth(0.1);
    $angleStart = 0;
    $angleEnd = 0;
    $i = 0;
    $factor =$radius+4;
    $pdfObject->SetFont($pdfObject->rapport_font, '', 7);
    foreach($data as $val)
    {
      $angle = (($val * 360) / doubleval($pdfObject->sum));
      //$pdfObject->SetDrawColor(255,255,0);
      $pdfObject->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
      if ($angle != 0)
      {
        $angleEnd = $angleStart + $angle;
        $avgAngle=($angleStart+$angleEnd)/360*M_PI;
        
        //$lineAngle=($angleEnd)/180*M_PI;
        //$pdfObject->line($XDiag,$YDiag,$XDiag+(sin($lineAngle)*$factor), $YDiag-(cos($lineAngle)*$factor));
        //echo ($angleEnd-$angleStart)."= ( $angleEnd-$angleStart ) $val  <br>\n";ob_flush();
        
        if(round($angleEnd,1)==360)
          $angleEnd=360;
        //    echo "$val : $XDiag, $YDiag, $radius, $angleStart, $angleEnd <br>\n";
        if(abs($angleEnd-$angleStart) > 1)
          $pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd,'F');
        
        if($val > 2)
        {
          //$pdfObject->SetXY($XDiag+(sin($avgAngle)*$factor)-5, $YDiag-(cos($avgAngle)*$factor)-2);
          if($pdfObject->debug==true)
          {
            $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255)));
            $pdfObject->line($XDiag,$YDiag,$XDiag+(sin($avgAngle)*$factor), $YDiag-(cos($avgAngle)*$factor));
          }
          $pdfObject->SetXY($XDiag+(sin($avgAngle)*$factor)-5, $YDiag-(cos($avgAngle)*$factor)-2);
          $pdfObject->Cell(10,4,number_format($val,1,',','.').'%',0,0,'C');
        }
        $angleStart += $angle;
      }
      $i++;
    }
    if ($angleEnd != 360)
    {
      $pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360,'F');
    }
    
    
    $i = 0;
    /* witte lijnen tussen taartpunten.
    foreach($data as $val)
    {
      $angle = (($val * 360) / doubleval($pdfObject->sum));
      $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.3527,'color'=>array(255,255,255)));
      if ($angle != 0 && $angle != 360)
      {
        $angleEnd = $angleStart + $angle;
        $lineAngle=($angleEnd)/180*M_PI;
        $pdfObject->line($XDiag,$YDiag,$XDiag+(sin($lineAngle)*$radius), $YDiag-(cos($lineAngle)*$radius));
        $angleStart += $angle;
      }
      $i++;
    }
*/
    $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
    $pdfObject->SetDrawColor(0,0,0);
    
    //Legends
    //$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
    
    $x1 = $XPage + $margin;
    $x2 = $x1 + $hLegend + 2 ;
    $y1 = $YDiag + ($radius) + $margin +5;
    
    if($pdfObject->debug==true)
    {
      $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>'1,1'));
      $pdfObject->line($XPage+2,$YDiag + ($radius) + $margin,$XPage+2,$YDiag + ($radius) + $margin +5);
      $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>0));
    }
    
    if(is_array($legendaStart))
    {
      $x1=$legendaStart[0];
      $y1=$legendaStart[1];
      $x2 = $x1 + $hLegend + 2 ;
      
    }
    
    for($i=0; $i<$pdfObject->NbVal; $i++) {
      $pdfObject->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
      $pdfObject->Rect($x1, $y1, $hLegend, $hLegend, 'F');
      $pdfObject->SetXY($x2,$y1);
      $pdfObject->Cell(0,$hLegend,$pdfObject->legends[$i]);
      $y1+=$hLegend*2;
    }
    
    $pdfObject->SetDrawColor(0,0,0);
    $pdfObject->SetFillColor(0,0,0);
  }
}

if(!function_exists('printPie'))
{
 	function printPie($object,$pieData,$kleurdata)
	{
    $pdfObject = &$object;
		// default colors
		// custom maken zet de kleuren in config/rapportage.php , en laad deze hier als ze bestaand, anders deze als default .
		if (is_array($pdfObject->customPieColors))
		{
		  $col1=$pdfObject->customPieColors["col1"];
		  $col2=$pdfObject->customPieColors["col2"];
		  $col3=$pdfObject->customPieColors["col3"];
		  $col4=$pdfObject->customPieColors["col4"];
		  $col5=$pdfObject->customPieColors["col5"];
		  $col6=$pdfObject->customPieColors["col6"];
		  $col7=$pdfObject->customPieColors["col7"];
		  $col8=$pdfObject->customPieColors["col8"];
		  $col9=$pdfObject->customPieColors["col9"];
		  $col0=$pdfObject->customPieColors["col0"];
		  $standaardKleuren=array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col0);
		}
		else
		{
		  $col1=array(255,0,0); // rood
		  $col2=array(0,255,0); // groen
		  $col3=array(255,128,0); // oranje
		  $col4=array(0,0,255); // blauw
		  $col5=array(255,255,0); // geel
		  $col6=array(255,0,255); // paars
		  $col7=array(128,128,128); // grijs
		  $col8=array(128,64,64); // bruin
		  $col9=array(255,255,255); // wit
		  $col0=array(0,0,0); //zwart
		  $standaardKleuren=array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col0);
		}

// standaardkleuren vervangen voor eigen kleuren.

		if($kleurdata)
		{
		  if(!$pdfObject->rapport_dontsortpie)
		  {
   			 $sorted 		= array();
   			 $percentages 	= array();
   			 $kleur			= array();
   			 $valuta 		= array();

  			while (list($key, $data) = each($kleurdata))
   			{
   			  $percentages[] 	= $data[percentage];
   			  $kleur[] 			= $data[kleur];
   			  $valuta[] 		= $key;
   			}
   			arsort($percentages);

   			while (list($key, $percentage) = each($percentages))
   			{
   			  $sorted[$valuta[$key]]['kleur']=$kleur[$key];
   			  $sorted[$valuta[$key]]['percentage']=$percentage;
   			}
			$kleurdata = $sorted; //columnSort($kleurdata, 'pecentage');
		  }

		  $pieData=array();
		  $grafiekKleuren = array();

		  $a=0;
		  while (list($key, $value) = each($kleurdata))
			{
			if ($value['kleur']['R']['value'] == 0 && $value['kleur']['G']['value'] == 0 && $value['kleur']['B']['value'] == 0)
			  {
			  $grafiekKleuren[]=$standaardKleuren[$a];
			  }
			else
			  {
			  $grafiekKleuren[] = array($value['kleur']['R']['value'],$value['kleur']['G']['value'],$value['kleur']['B']['value']);
			  }
			$pieData[$key] = $value[percentage];
			$a++;
			}
		}
		else
		  $grafiekKleuren = $standaardKleuren;

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->rapport_printpie = true;

		while (list($key, $value) = each($pieData))
		{
			if ($value < 0)
			{
				if($pdfObject->rapport_layout == 8 || $pdfObject->rapport_layout == 10 )
					$pieData[$key] = -1 * $value;
				else
					$pdfObject->rapport_printpie = false;
			}
		}

		if($pdfObject->rapport_printpie)
		{
			$pdfObject->SetXY(210, 30);
			$y = $pdfObject->getY();
			$pdfObject->SetFont($pdfObject->pdf->rapport_font,'b',10);
			$pdfObject->Cell(50,4,vertaalTekst($pdfObject->rapport_titel, $pdfObject->rapport_taal),0,1,"C");
			$pdfObject->SetFont($pdfObject->pdf->rapport_font,'',$pdfObject->pdf->rapport_fontsize);
			$pdfObject->SetX(210);
			$pdfObject->PieChart(100, 50, $pieData, '%l (%p)', $grafiekKleuren);
			$hoogte = ($pdfObject->getY() - $y) + 8;
			$pdfObject->setY($y);
			$pdfObject->SetLineWidth($pdfObject->lineWidth);
		}
	}

	function getFondsPerformance_L25($fonds,$beginDatum,$eindDatum)
	{
		$tag='';
		if(is_array($fonds))
		{
			$perf=0;
			foreach($fonds as $fondsDetail=>$percentage)
			{
				$beginKoers = getFondsKoers_L25($fondsDetail, $beginDatum);
				$eindKoers = getFondsKoers_L25($fondsDetail, $eindDatum);
				if($eindKoers['tag']<>'')
					$tag=$eindKoers['tag'];

				$perf += ($eindKoers['koers'] - $beginKoers['koers']) / ($beginKoers['koers']) *$percentage;
				// echo "$beginDatum->$eindDatum  $fondsDetail ".(($eindKoers - $beginKoers) / ($beginKoers) )."  | $percentage;<br>\n";
				// echo "$eindDatum $fondsDetail |  som=$perf |  ".(($eindKoers - $beginKoers) / ($beginKoers) *$percentage)." = ($eindKoers - $beginKoers) / ($beginKoers) *$percentage;<br>\n";
			}
		}
		else
		{
			$beginKoers = getFondsKoers_L25($fonds, $beginDatum);
			$eindKoers = getFondsKoers_L25($fonds, $eindDatum);
			if($eindKoers['tag']<>'')
				$tag=$eindKoers['tag'];
			$perf = ($eindKoers['koers'] - $beginKoers['koers']) / ($beginKoers['koers'] / 100);
		}
		//echo $perf."<br>\n";
		return array('perf'=>$perf,'tag'=>$tag);
	}

	function getFondsKoers_L25($fonds,$datum)
	{
		$db=new DB();
		$query="SELECT Koers,Datum FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
		$db->SQL($query);
		$koers=$db->lookupRecord();

		$tag='';
		if(substr($koers['Datum'],0,10) <> substr($datum,0,10))
		{
			$rapJul=db2jul($datum);
			$dag=date('w',$rapJul);
			$dagenAchter=($rapJul-db2jul($koers['Datum']))/86400;
			if($dagenAchter>3)
				$tag='*';
			elseif($dag <> 0 && $dag <> 6)
				$tag='*';
		}

		return array('koers'=>$koers['Koers'],'tag'=>$tag);
	}
}
?>
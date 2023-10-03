<?php

function Header_basis_L13($object)
{
  $pdfObject = &$object;
  if($pdfObject->CurOrientation == 'L')
	  $extra = 90;

  if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  		$pdfObject->customPageNo = 0;

		$pdfObject->customPageNo++;

  if($pdfObject->rapport_type == 'MOD') {
    $pdfObject->MultiCell(50, 4, $pdfObject->rapport_koptext, 0, 'L');
  }

	if(is_file($pdfObject->rapport_logo))
	{
		$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
    if($pdfObject->CurOrientation == 'L') {
      $pdfObject->Image($pdfObject->rapport_logo, 297/2-37/2, 10, 37);
    } else {
      if($pdfObject->rapport_type == 'MOD') {
        $pdfObject->Image($pdfObject->rapport_logo, 88, 10, 37);
      } else {
		    $pdfObject->Image($pdfObject->rapport_logo, 20, 10, 37);
      }
    }
	}
	else
	{
		$pdfObject->SetY(10);
		$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
		$pdfObject->MultiCell(90, 4, $pdfObject->portefeuilledata['VermogensbeheerderNaam'], 0, 'L');
	}
	$pdfObject->SetXY(120,10);
	$now = time();

	if(!in_array($pdfObject->rapport_type,array('HSE','HSEP','HUIS')))
	{
  	if($pdfObject->rapport_type == 'VHO')
      $pdfObject->MultiCell(80+$extra,4,date("j/n/Y",$now)." ".vertaalTekst("Blad" ,$pdfObject->rapport_taal)." : ".$pdfObject->customPageNo ."\n \n \n".vertaalTekst("Rapportage per" ,$pdfObject->rapport_taal)."".date("j/n/Y",$pdfObject->rapport_datum),0,'R');
	  elseif(in_array($pdfObject->rapport_type,array('KERNV','INDEX','GRAFIEK','VAR','VOLKD','OIH','OIR','AFM','TRANS','VKM')))
	    $pdfObject->MultiCell(80+$extra,4,vertaalTekst('Productiedatum',$pdfObject->rapport_taal).': '.date("j/n/Y",$now)." ".vertaalTekst("Blad" ,$pdfObject->rapport_taal)." : ".$pdfObject->customPageNo ."\n".vertaalTekst("Rapportagedatum" ,$pdfObject->rapport_taal).": ".date("j/n/Y",$pdfObject->rapport_datum),0,'R');
	  else
      $pdfObject->MultiCell(80+$extra,4,date("j/n/Y",$now)." ".vertaalTekst("Blad" ,$pdfObject->rapport_taal)." : ".$pdfObject->customPageNo .'',0,'R');
	}
	$pdfObject->ln(8);

	if(is_array($pdfObject->portefeuilles) && count($pdfObject->portefeuilles)>1)
	{
	  if(!is_array($pdfObject->consolidatieData))
	  {
	    $DB=new DB();
	    foreach ($pdfObject->portefeuilles as $id=>$portefeuille)
		  {
		    $query = "SELECT
	            	Clienten.Naam as Naam,
                Clienten.Naam1 as Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Portefeuilles.Depotbank,
                Portefeuilles.PortefeuilleVoorzet,
                Accountmanagers.Naam as accountManager,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email,
                Depotbanken.Omschrijving as depotbankOmschrijving
		          FROM
		            Portefeuilles
		            LEFT JOIN Clienten ON Portefeuilles.Client = Clienten.Client
		            LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
		            LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
		            LEFT Join CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille
		            Join Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
		          WHERE
		            Portefeuilles.Portefeuille = '".$portefeuille."'";

		    $DB->SQL($query);
		    $DB->Query();
	      $pdfObject->consolidatieData[$id] = $DB->nextRecord();
		  }
		  $pdfObject->portefeuilledata['Naam']=$pdfObject->consolidatieData[0]['Naam'];
      $pdfObject->portefeuilledata['Naam1']=$pdfObject->consolidatieData[0]['Naam1'];
      $pdfObject->portefeuilledata['Adres']=$pdfObject->consolidatieData[0]['Adres'];
      $pdfObject->portefeuilledata['Woonplaats']=$pdfObject->consolidatieData[0]['Woonplaats'];
      $pdfObject->portefeuilledata['Land']=$pdfObject->consolidatieData[0]['Land'];
	  }

    if($pdfObject->lastPOST['anoniem']==1)
    {
   	  $pdfObject->portefeuilledata['Naam']='Anoniem';
  	  if($pdfObject->portefeuilledata['Naam1'] <> '')
	      $pdfObject->portefeuilledata['Naam1']='Anoniem';
	    $pdfObject->portefeuilledata['Portefeuille']='000000';
	    $portefeuille=$pdfObject->portefeuilledata['Portefeuille'];
	    $pdfObject->portefeuilledata['Client']='';
	    $pdfObject->portefeuilledata['Adres']='';
	    $pdfObject->portefeuilledata['Woonplaats']='';
	    $pdfObject->portefeuilledata['Land']='';
	    $pdfObject->portefeuilledata['verzendPaAanhef']='Anoniem';
    }
	}

  $portefeuilleString='';
  $adres='';
	$oldPortefeuilleString = strval($pdfObject->rapport_portefeuille);
  $i=1;
	  $puntenAantal=0;
		for($j=0;$j<strlen($oldPortefeuilleString);$j++)
		{
		 if($i>2 && $puntenAantal <3)
		 {
		  $portefeuilleString.='.';
		  $i=1;
		  $puntenAantal ++;
		 }
		 $portefeuilleString.= $oldPortefeuilleString[$j];
		 $i++;
		}

if(isset($pdfObject->consolidatieData))
{
  $portefeuilleString='';
}

  if($pdfObject->rapport_type == 'MUT')// && $pdfObject->rapportageValuta != 'EUR' && $pdfObject->rapportageValuta != '')
  {
    $pdfObject->SetXY(100,29);
    $pdfObject->SetFont($pdfObject->rapport_font, 'b', $pdfObject->rapport_fontsize);
    $pdfObject->MultiCell(100, 4, vertaalTekst($pdfObject->rapport_titel, $pdfObject->rapport_taal), 0, 'C');
    $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
  }

if($pdfObject->CurOrientation == 'L')
{
   $pdfObject->SetXY($pdfObject->marge,18);
   $adres .= $pdfObject->portefeuilledata['Naam'] . "\n";
	 if(strlen($pdfObject->portefeuilledata['Naam1']) > 0) {
     $adres .= $pdfObject->portefeuilledata['Naam1'];
   } else {
     $adres .= ' ';
   }
	 $pdfObject->MultiCell(180,4,$adres,0,'L');
	 $pdfObject->MultiCell(180,4,$portefeuilleString,0,'L');
  $pdfObject->SetXY($pdfObject->marge,34);
}
else
{
  if($pdfObject->rapport_type !== 'MOD') {
//  //echo "<br>\n".$pdfObject->rapport_type." ".$pdfObject->rapport_portefeuille." ".$pdfObject->customPageNo." $portefeuilleString ";
    if ($pdfObject->customPageNo < 2)
    {
      if (in_array($pdfObject->rapport_type,array('HSE','FRONT','HUIS')))
        $pdfObject->SetXY(130, 45);
      else
        $pdfObject->SetXY(88, 10);

      $adres .= $pdfObject->portefeuilledata['Naam'];
      if (strlen($pdfObject->portefeuilledata['Naam1']) > 0)
        $adres .= "\n" . $pdfObject->portefeuilledata['Naam1'];
      if (strlen($pdfObject->portefeuilledata['Adres']) > 0)
        $adres .= "\n" . $pdfObject->portefeuilledata['Adres'];
      if (strlen($pdfObject->portefeuilledata['Woonplaats']) > 0)
        $adres .= "\n" . $pdfObject->portefeuilledata['Woonplaats'];
      if (strlen($pdfObject->portefeuilledata['Land']) > 0)
        $adres .= "\n" . $pdfObject->portefeuilledata['Land'];

      if (!in_array($pdfObject->rapport_type,array('HSE','HUIS')))
        $adres .= "\n" . $portefeuilleString;

      $pdfObject->MultiCell(88, 4, $adres, 0, 'L');
	}
	else
	{
      $pdfObject->SetXY(88, 10);
    if (!in_array($pdfObject->rapport_type,array('HSE','HUIS')))
	  {
        $adres .= $pdfObject->portefeuilledata['Naam'];
        if (strlen($pdfObject->portefeuilledata['Naam1']) > 0)
          $adres .= "\n" . $pdfObject->portefeuilledata['Naam1'];
        $pdfObject->MultiCell(180, 4, $adres . "\n" . $portefeuilleString, 0, 'L');
      }
    }
  }
	$pdfObject->SetY(45);
$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
}



}

function HeaderHUIS_L13($object)
{
  HeaderHSE_L13($object);
}

	function HeaderVKM_L13($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}

function HeaderVKMS_L13($object)
{
  $pdfObject = &$object;
}
function HeaderMOD_L13($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

  $huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
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
    vertaalTekst("Aantal",$pdfObject->rapport_taal),
    vertaalTekst("Per stuk \nin valuta",$pdfObject->rapport_taal),
    vertaalTekst("Portefeuille \nin valuta",$pdfObject->rapport_taal),
    vertaalTekst("Portefeuille \nin EUR",$pdfObject->rapport_taal),
    ($pdfObject->rapport_inprocent)?vertaalTekst("In % Totaal",$pdfObject->rapport_taal):""),
    vertaalTekst("Per stuk \nin valuta",$pdfObject->rapport_taal),
    vertaalTekst("Portefeuille \nin valuta",$pdfObject->rapport_taal),
    vertaalTekst("Portefeuille \nin EUR",$pdfObject->rapport_taal));

  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);

  $pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
  $pdfObject->setY($pdfObject->GetY()-8);
  $pdfObject->row(array(vertaalTekst("Categorie",$pdfObject->rapport_taal)));
  $pdfObject->ln();

  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  $pdfObject->setY($pdfObject->GetY()+3);
  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

  $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderEND_L13($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
}


function HeaderKERNZ_L13($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  //$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  
  $huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
  $eindhuidige 	= $huidige +$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];
  
  $actueel 			= $eindhuidige + $pdfObject->widthB[6];
  $eindactueel 	= $actueel + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];
  
  $resultaat 		= $eindactueel + $pdfObject->widthB[10];
  $eindresultaat = $resultaat +  $pdfObject->widthB[11] +  $pdfObject->widthB[12] +  $pdfObject->widthB[13]+  $pdfObject->widthB[14] +  $pdfObject->widthB[15];
  
  // achtergrond kleur
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

//    if($pdfObject->rapportageValuta=='EUR')
//      $teken='€';
//    else
  $teken=$pdfObject->rapportageValuta;
  // lijntjes onder beginwaarde in het lopende jaar
  $pdfObject->SetX($pdfObject->marge+$huidige-5);
  $y = $pdfObject->getY();
  $pdfObject->Cell(65,4, vertaalTekst("Kostprijs",$pdfObject->rapport_taal), 0,0,"C");
  $pdfObject->SetX($pdfObject->marge+$actueel);
  $pdfObject->Cell(65,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,0, "C");
  $pdfObject->SetX($pdfObject->marge+$resultaat);
  $pdfObject->Cell(60,4, vertaalTekst("",$pdfObject->rapport_taal), 0,1, "C");
  //$pdfObject->SetDrawColor(255,255,255);
//  $pdfObject->Line(($pdfObject->marge+$huidige),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
//  $pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
  //	$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
//  $pdfObject->SetDrawColor(0,0,0);
  
  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  $pdfObject->SetXY($pdfObject->marge,$y);
  
  $pdfObject->row(array("",
                    "\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Waarde ".$teken,$pdfObject->rapport_taal),
                    "",
                    "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Waarde ".$teken,$pdfObject->rapport_taal),
                    "\n".vertaalTekst("in %",$pdfObject->rapport_taal),
                    vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
                    vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
                    vertaalTekst("Direct\nresultaat",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("in %",$pdfObject->rapport_taal)
                  ));
  $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->w-$pdfObject->marge ,$pdfObject->GetY());
  $yeind=$pdfObject->getY();
  $pdfObject->setY($y);
  $pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);
  $pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  $pdfObject->setY($yeind+1);
}

function HeaderRISK_L13($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  $dataWidth=array(28,70,15,20,20,20,22,22,22,18,20);
  $pdfObject->SetWidths($dataWidth);
  $pdfObject->SetAligns(array('L','L','L','R','R','R','R','R','R','R','R','R','R'));
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->ln();
  $lastColors=$pdfObject->CellFontColor;
  unset($pdfObject->CellFontColor);
  $pdfObject->Row(array(vertaalTekst("Risico\nCategorie",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Fonds",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
                    "\n".date('d-m-Y',$pdfObject->rapport_datumvanaf),
                    "\n".date('d-m-Y',$pdfObject->rapport_datum),
                    "\n".vertaalTekst("Mutaties",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Resultaat",$pdfObject->rapport_taal),
                    vertaalTekst("Gemiddeld vermogen",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Resultaat %",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Weging",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Bijdrage",$pdfObject->rapport_taal)."\n".vertaalTekst("rendement",$pdfObject->rapport_taal)));
  $pdfObject->CellFontColor=$lastColors;
  $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
  $pdfObject->SetLineWidth(0.1);
  if(is_array($pdfObject->widthsBackup))
    $pdfObject->widths=$pdfObject->widthsBackup;
  // listarray($pdfObject->widths);echo "new page <br>\n";
}

function HeaderAFM_L13($object)
{
  $pdfObject = &$object;
  $pdfObject->Ln();
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'], $pdfObject->rapport_kop_bgcolor['g'], $pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297 - $pdfObject->marge * 2, 6, 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'], $pdfObject->rapport_kop_fontcolor['g'], $pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->Ln(10);
}
    function HeaderFRONT_L13($object)
	  {
  	  $pdfObject = &$object;
  	//  $pdfObject->HeaderPERF();
	  }

	  function HeaderPERF_L13($object)
	  {
  	  $pdfObject = &$object;
  	//  $pdfObject->HeaderPERF();
	  }

	  function HeaderPERFG_L13($object)
	  {
  	  $pdfObject = &$object;
  	//  $pdfObject->HeaderPERF();
	  }

function HeaderATT_L13($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderATT();
  $pdfObject->ln();
}
function HeaderHSEP_L13($object)
{
	$pdfObject = &$object;
  HeaderHSE_L13($object);
}

function HeaderOIH_L13($object)
{
	$pdfObject = &$object;
	//$pdfObject->ln();
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor2['r'],$pdfObject->rapport_kop_bgcolor2['g'],$pdfObject->rapport_kop_bgcolor2['b']);
	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), $pdfObject->w-$pdfObject->marge*2, $pdfObject->rowHeight*2 , 'F');
//	$pdfObject->ln();
	$pdfObject->SetTextColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);

	$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
	$eindhuidige 	= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];

	$actueel 			= $eindhuidige + $pdfObject->widthB[6];
	$eindactueel 	= array_sum($pdfObject->widthB);
	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	$pdfObject->ln($pdfObject->rowHeight*0.5);
	$pdfObject->row(array("%",vertaalTekst("Sectoren",$pdfObject->rapport_taal),'%',
										vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
										vertaalTekst("Aantal",$pdfObject->rapport_taal),
										vertaalTekst("Koers",$pdfObject->rapport_taal),
										vertaalTekst("Valuta",$pdfObject->rapport_taal),
										"",
										vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal)
										));
	$pdfObject->ln($pdfObject->rowHeight*0.5);
	//$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
  $pdfObject->SetTextColor(0);
}

  function HeaderHSE_L13($object)
	{
  	  $pdfObject = &$object;
	//	$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
	//	$eindhuidige 	= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];

//		$actueel 			= $eindhuidige + $pdfObject->widthB[6];
//		$eindactueel 	= array_sum($pdfObject->widthB);
	 $widthsBackup= $pdfObject->widths;
	 $alignsBackup= $pdfObject->aligns;

   // $pdfObject->SetFont('Arial');
		$pdfObject->SetY(10);
	//	$pdfObject->MultiCell(90,4,$pdfObject->portefeuilledata['VermogensbeheerderNaam'],0,'L');
		$pdfObject->SetXY(155,10);
		$now = time();
	  $pdfObject->MultiCell(50,4,date("j/n/Y",$now)." ". vertaalTekst("Blad" ,$pdfObject->rapport_taal)." : ".$pdfObject->customPageNo .'/{LastPage}',0,'R');

	if(isset($pdfObject->consolidatieData) && !isset($pdfObject->shortHeaderPrinted))
  {
    $pdfObject->shortHeaderPrinted=true;
	  $pdfObject->SetXY(130,45);
	  $adres = $pdfObject->portefeuilledata['Naam'];
	  if(strlen($pdfObject->portefeuilledata['Naam1']) > 0)
	    $adres .= "\n".$pdfObject->portefeuilledata['Naam1'];
	  $pdfObject->MultiCell(80,4,$adres,0,'L');
	  $pdfObject->SetXY(10,60);
  	$pdfObject->MultiCell(190,4,vertaalTekst('Positie-overzicht per' ,$pdfObject->rapport_taal).' '.date("j/n/Y",$pdfObject->rapport_datum) ,0,'C');
  	$pdfObject->SetY($pdfObject->GetY()+1);
  	$saldoText = vertaalTekst("Saldo geldrekeningen",$pdfObject->rapport_taal)." ".$pdfObject->saldoGeldrekeningen." ".$pdfObject->rapportageValuta;
	}
	elseif($pdfObject->customPageNo < 2)
	{
	  $pdfObject->SetXY(130,45);
	  $adres = $pdfObject->portefeuilledata['Naam'];
	  if(strlen($pdfObject->portefeuilledata['Naam1']) > 0)
	    $adres .= "\n".$pdfObject->portefeuilledata['Naam1'];
	  if(strlen($pdfObject->portefeuilledata['Adres']) > 0)
	    $adres .= "\n".$pdfObject->portefeuilledata['Adres'];
	  if(strlen($pdfObject->portefeuilledata['Woonplaats']) > 0)
	    $adres .= "\n".$pdfObject->portefeuilledata['Woonplaats'];
	  if(strlen($pdfObject->portefeuilledata['Land']) > 0)
	    $adres .= "\n".$pdfObject->portefeuilledata['Land'];
    $pdfObject->MultiCell(80,4,$adres ,0,'L');
/*
	  if(strlen($pdfObject->portefeuilledata['Naam1']) > 0)
	    $pdfObject->MultiCell(80,4,$pdfObject->portefeuilledata['Naam']."\n".$pdfObject->portefeuilledata['Naam1']."\n".$pdfObject->portefeuilledata['Adres']."\n".$pdfObject->portefeuilledata['Woonplaats'] ,0,'L');
	  else
	    $pdfObject->MultiCell(80,4,$pdfObject->portefeuilledata['Naam']."\n".$pdfObject->portefeuilledata['Adres']."\n".$pdfObject->portefeuilledata['Woonplaats'] ,0,'L');
*/
	 if(!$pdfObject->memoOnderdrukken)
    $pdfObject->MultiCell(190,4,$pdfObject->portefeuilledata['Memo'],0,'L');

  	$pdfObject->SetXY(10,80);
  	$pdfObject->MultiCell(190,4,vertaalTekst('Positie-overzicht per' ,$pdfObject->rapport_taal). ' '.date("j/n/Y",$pdfObject->rapport_datum) ,0,'C');
  	$pdfObject->SetY($pdfObject->GetY()+1);
  	$saldoText = vertaalTekst("Saldo geldrekeningen" ,$pdfObject->rapport_taal)." ".$pdfObject->saldoGeldrekeningen." ".$pdfObject->rapportageValuta;
	}
	else
	{
	  $pdfObject->SetXY(130,45);
	  $adres = $pdfObject->portefeuilledata['Naam'];
	  if(strlen($pdfObject->portefeuilledata['Naam1']) > 0)
	    $adres .= "\n".$pdfObject->portefeuilledata['Naam1'];
	  $pdfObject->MultiCell(80,4,$adres,0,'L');
	  $pdfObject->SetXY(10,60);
	  $saldoText ='';
	}




  	$pdfObject->Line($pdfObject->marge ,$pdfObject->GetY(), $pdfObject->marge + 194,$pdfObject->GetY());
    $pdfObject->SetY($pdfObject->GetY()+1);
		$pdfObject->SetWidths(array(60,75,60));
		$pdfObject->SetAligns(array('L','C','R'));

		$oldPortefeuilleString = strval($pdfObject->rapport_portefeuille);
	  $i=1;
	  $puntenAantal=0;
    $portefeuilleString='';
		for($j=0;$j<strlen($oldPortefeuilleString);$j++)
		{
		 if($i>2 && $puntenAantal <3)
		 {
		  $portefeuilleString.='.';
		  $i=1;
		  $puntenAantal ++;
		 }
		 $portefeuilleString.= $oldPortefeuilleString[$j];
		 $i++;
		}

if(isset($pdfObject->consolidatieData))
{
  $portefeuilleString='';
}

  	$pdfObject->row(array($portefeuilleString,
  	                 $saldoText,
  	                 vertaalTekst('Slotkoersen per' ,$pdfObject->rapport_taal).' '.date("j/n/Y",$pdfObject->rapport_datum)));
  	$pdfObject->SetY($pdfObject->GetY()+1);
  	$pdfObject->Line($pdfObject->marge ,$pdfObject->GetY()+1, $pdfObject->marge + 194,$pdfObject->GetY()+1);


  	$pdfObject->setY($pdfObject->GetY()+2);

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);

		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_subtotaal_fontstyle,$pdfObject->rapport_fontsize);

		if($pdfObject->rapport_type == "HUIS")
    {
      $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor2['r'],$pdfObject->rapport_kop_bgcolor2['g'],$pdfObject->rapport_kop_bgcolor2['b']);
      $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), $pdfObject->w-$pdfObject->marge*2, $pdfObject->rowHeight*2 , 'F');
      $pdfObject->SetTextColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
      $pdfObject->ln($pdfObject->rowHeight*0.5);
    }
		if($pdfObject->customPageNo < 2)
  		$pdfObject->row(array("",
		                  vertaalTekst("Aantal" ,$pdfObject->rapport_taal),
											"  ".vertaalTekst("Fonds",$pdfObject->rapport_taal),
											"",
											vertaalTekst("Actueel",$pdfObject->rapport_taal) ,
											"",
											vertaalTekst("Waarde EUR",$pdfObject->rapport_taal),
											""));
		else
	  	$pdfObject->row(array(''));

	 $pdfObject->SetWidths($widthsBackup);
	 $pdfObject->SetAligns($alignsBackup);
   if($pdfObject->rapport_type == "HUIS")
   {
     $pdfObject->SetTextColor(0);
     $pdfObject->ln($pdfObject->rowHeight*0.5);
   }
  }

function HeaderVAR_L13($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 8, 'F');
  $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

  $pdfObject->SetFillColor(39,62,102);
  $pdfObject->Rect($pdfObject->marge, 37, 297-$pdfObject->marge*2, 8, 'F');
  $pdfObject->SetTextColor(255,255,255);

  $pdfObject->ln(1);
  $pdfObject->Cell(100,4, vertaalTekst("Resultaat-analyse over verslagperiode",$pdfObject->rapport_taal),0,0);
  $pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
  $pdfObject->ln(3);
  
  $pdfObject->ln();
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);
//  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),297-$pdfObject->marge,$pdfObject->GetY());
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderVOLKD_L13($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  
  $huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
  $eindhuidige 	= $huidige +$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];
  
  $actueel 			= $eindhuidige + $pdfObject->widthB[6];
  $eindactueel 	= $actueel + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];
  
  $resultaat 		= $eindactueel + $pdfObject->widthB[10];
  $eindresultaat = $resultaat +  $pdfObject->widthB[11] +  $pdfObject->widthB[12] +  $pdfObject->widthB[13]+  $pdfObject->widthB[14] +  $pdfObject->widthB[15];
  
  $fillBackup=$pdfObject->fillCell;
  unset($pdfObject->fillCell);
  // achtergrond kleur
  $pdfObject->SetFillColor(39,62,102);
  $pdfObject->Rect($pdfObject->marge, 37, array_sum($pdfObject->widthB), 9 , 'F');

  
  // lijntjes onder beginwaarde in het lopende jaar
  $pdfObject->SetX($pdfObject->marge+$huidige-5);
  $y = $pdfObject->getY();
  $pdfObject->SetTextColor(255,255,255);
  $pdfObject->Cell(65,5, vertaalTekst("Beginwaarde huidig jaar",$pdfObject->rapport_taal), 0,0,"C");
  $pdfObject->SetX($pdfObject->marge+$actueel);
  $pdfObject->Cell(65,5, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,0, "C");
  $pdfObject->SetX($pdfObject->marge+$resultaat);
  $pdfObject->Cell(60,5, vertaalTekst("",$pdfObject->rapport_taal), 0,0, "C");
  $pdfObject->SetDrawColor(255,255,255);
  $pdfObject->Line(($pdfObject->marge+$huidige),$pdfObject->GetY()+4,$pdfObject->marge + $eindhuidige,$pdfObject->GetY()+4);
  $pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY()+4,$pdfObject->marge + $eindactueel,$pdfObject->GetY()+4);
  $pdfObject->SetDrawColor(0,0,0);
  
  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  $pdfObject->setXY($pdfObject->marge,$y+1);
//    if($pdfObject->rapportageValuta=='EUR')
//      $teken='€';
//    else
  $teken=$pdfObject->rapportageValuta;
  
  $pdfObject->row(array("",
                    "\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
                    "\n \n",
                    "\n".vertaalTekst("Waarde ".$teken,$pdfObject->rapport_taal),
                    "",
                    "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
                    "\n \n",
                    "\n".vertaalTekst("Waarde ".$teken,$pdfObject->rapport_taal),
                    "\n".vertaalTekst("in %",$pdfObject->rapport_taal),
                    vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
                    vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
                    vertaalTekst("Direct\nresultaat",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("in %",$pdfObject->rapport_taal)
                  ));
  
  
  $pdfObject->setY($y);
  $pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);
  $pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  $pdfObject->ln(6);
  $pdfObject->fillCell=$fillBackup;
}

function HeaderSMV_L13($object)
	 {
	   $pdfObject = &$object;
	   $pdfObject->ln();
     $pdfObject->SetFont($pdfObject->rapport_font,'B',($pdfObject->rapport_fontsize)-1);

    if(isset($pdfObject->lastPOST['GB_STORT_ONTTR']))
		{
			if (count($pdfObject->portefeuilles) > 1)
				$pdfObject->Row(array(vertaalTekst('Boekdatum' ,$pdfObject->rapport_taal), vertaalTekst('Saldo' ,$pdfObject->rapport_taal), vertaalTekst('Bedrag' ,$pdfObject->rapport_taal), 
													vertaalTekst('C/D',$pdfObject->rapport_taal),vertaalTekst('Rekening',$pdfObject->rapport_taal), vertaalTekst('Omschrijving',$pdfObject->rapport_taal)));
			else
				$pdfObject->Row(array(vertaalTekst('Boekdatum',$pdfObject->rapport_taal), vertaalTekst('Saldo',$pdfObject->rapport_taal), vertaalTekst('Bedrag',$pdfObject->rapport_taal), vertaalTekst('C/D',$pdfObject->rapport_taal),vertaalTekst('Omschrijving',$pdfObject->rapport_taal)));
		}
    else
       $pdfObject->Row(array(vertaalTekst('Boekdatum',$pdfObject->rapport_taal),vertaalTekst('Saldo',$pdfObject->rapport_taal),vertaalTekst('Bedrag' ,$pdfObject->rapport_taal) ,vertaalTekst('C/D',$pdfObject->rapport_taal),vertaalTekst('GB',$pdfObject->rapport_taal),vertaalTekst('Omschrijving',$pdfObject->rapport_taal)));
     $pdfObject->ln();
     $pdfObject->SetFont($pdfObject->rapport_font,'',($pdfObject->rapport_fontsize)-1);
	 }

	 function HeaderVHO_L13($object)
	 {
  	  $pdfObject = &$object;

  	  $gemiddelde=95;

  	  $eindGemiddelde = 170;
  	  $actueel=175;
  	  $eindactueel = 240;
  	  $resultaat = 245;
  	  $eindresultaat = 265;

  	  $pdfObject->SetX($gemiddelde);
			$pdfObject->Cell(90,4, vertaalTekst("Gemiddelde inkoopprijs",$pdfObject->rapport_taal), 0,0,"C");
			$pdfObject->Cell(60,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,0, "C");
  		$pdfObject->Cell(35,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,1, "C");

    	$pdfObject->Line(($pdfObject->marge+$gemiddelde),$pdfObject->GetY(),$pdfObject->marge + $eindGemiddelde,$pdfObject->GetY());
	  	$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
		  $pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());

		  $y = $pdfObject->getY();
		  $pdfObject->SetWidths(array(100));
			$pdfObject->row(array("Categorie\n"));
			$pdfObject->setY($y);
		  $pdfObject->SetWidths($pdfObject->widthB);
		  $pdfObject->SetAligns($pdfObject->alignB);

  	  $pdfObject->row(array("",
											"\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
											vertaalTekst("Aantal",$pdfObject->rapport_taal),
												vertaalTekst("Per stuk in valuta",$pdfObject->rapport_taal),
												'',
												vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
												"",
												vertaalTekst("Per stuk in valuta",$pdfObject->rapport_taal),
												'',
												vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal)));

			$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + 285,$pdfObject->GetY());
	 }

function HeaderTRANSFEE_L13($object)
{
  $pdfObject = &$object;
  $pdfObject->Ln();
 // $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize-1);
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

  
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);
  
  $pdfObject->row(array(vertaalTekst("Datum",$pdfObject->rapport_taal),
                    vertaalTekst("Aan/ Ver Koop",$pdfObject->rapport_taal),
                    vertaalTekst("Aantal",$pdfObject->rapport_taal),
                    vertaalTekst("Fonds",$pdfObject->rapport_taal),
                    vertaalTekst("Aankoop koers in valuta",$pdfObject->rapport_taal),
                    vertaalTekst("Aankoop datum",$pdfObject->rapport_taal),
                    vertaalTekst("Aankoop waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                    vertaalTekst("Verkoop koers in valuta",$pdfObject->rapport_taal),
                    vertaalTekst("Verkoop waarde in valuta",$pdfObject->rapport_taal),
                    vertaalTekst("Verkoop waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                    vertaalTekst("Historische kostprijs in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                    vertaalTekst("Koersresultaat voorafgaand verslagperiode",$pdfObject->rapport_taal),
                    vertaalTekst("Koersresultaat gedurende verslagperiode",$pdfObject->rapport_taal)));
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);
  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
}
 	 function HeaderTRANS_L13($object)
	 {
  	  $pdfObject = &$object;
  	  $pdfObject->SetXY(50,$pdfObject->getY()-8);
			$pdfObject->MultiCell(200,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
			$pdfObject->ln();

  	  if($pdfObject->lastPOST['TRANS_RESULT'])
  	  {
      $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor2['r'],$pdfObject->rapport_kop_bgcolor2['g'],$pdfObject->rapport_kop_bgcolor2['b']);
		  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
		//  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
      $pdfObject->SetTextColor(255,255,255);

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
      $pdfObject->setDrawColor(255,255,255);
			$pdfObject->Line(($inkoop+2),$pdfObject->GetY(),$inkoopEind,$pdfObject->GetY());
		  $pdfObject->Line(($verkoop+2),$pdfObject->GetY(),$verkoopEind,$pdfObject->GetY());
		  $pdfObject->Line(($resultaat+2),$pdfObject->GetY(),$resultaatEind,$pdfObject->GetY());
      $pdfObject->setDrawColor(0);

			$pdfObject->SetWidths($pdfObject->widthA);
		  $pdfObject->SetAligns($pdfObject->alignA);
			$pdfObject->row(array(vertaalTekst("Datum",$pdfObject->rapport_taal),
										 vertaalTekst("Aan/\nVerkoop",$pdfObject->rapport_taal),
										 vertaalTekst("Aantal",$pdfObject->rapport_taal),
										 vertaalTekst("Fonds",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop waarde in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Verkoop koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Verkoop waarde in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Verkoop waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Historische kostprijs in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Resultaat voorgaande jaren",$pdfObject->rapport_taal),
										 vertaalTekst("Resultaat lopende jaar",$pdfObject->rapport_taal),
										 $procentTotaal));
	//		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
        $pdfObject->ln();
  	  }
  	  else
  	  {
  	  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor2['r'],$pdfObject->rapport_kop_bgcolor2['g'],$pdfObject->rapport_kop_bgcolor2['b']);
		  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 14 , 'F');
		  $pdfObject->SetTextColor(255,255,255);

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

			$pdfObject->ln();
			$pdfObject->setDrawColor(255,255,255);
			$pdfObject->Line(($inkoop+2),$pdfObject->GetY(),$inkoopEind,$pdfObject->GetY());
		  $pdfObject->Line(($verkoop+2),$pdfObject->GetY(),$verkoopEind,$pdfObject->GetY());
		  $pdfObject->Line(($resultaat+2),$pdfObject->GetY(),$resultaatEind,$pdfObject->GetY());
      $pdfObject->setDrawColor(0);

			$pdfObject->SetWidths($pdfObject->widthA);
		  $pdfObject->SetAligns($pdfObject->alignA);
			$pdfObject->row(array(vertaalTekst("Datum",$pdfObject->rapport_taal),
										 vertaalTekst("Aan/\nVerkoop",$pdfObject->rapport_taal),
										 vertaalTekst("Aantal",$pdfObject->rapport_taal),
										 vertaalTekst("Fonds",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop waarde in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Verkoop koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Verkoop waarde in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Verkoop waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal)
										 ));
        $pdfObject->ln(1);
			//$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
  	  }
	 }

	 	function HeaderOIB_L13($object)
	  {
  	  $pdfObject = &$object;
  	  $pdfObject->headerStart=$pdfObject->getY()+15;
  	  $pdfObject->HeaderOIB();
	  }

function HeaderOIV_L13($object)
{
  $pdfObject = &$object;
  $pdfObject->headerStart=$pdfObject->getY()+15;
  $pdfObject->HeaderOIV();
  $pdfObject->setY($pdfObject->getY()-$pdfObject->rowHeight);
}

function HeaderMUT_L13($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderMUT();
}

function HeaderOIR_L13($object)
{
  $pdfObject = &$object;

  $pdfObject->SetFillColor(39,62,102);
  $pdfObject->Rect($pdfObject->marge, 33, 286, 6, 'F');

  $pdfObject->SetTextColor(255,255,255);
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

  $pdfObject->SetX(110);
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  $pdfObject->Write(4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ");
  $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
  $pdfObject->Write(4,date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ");
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  $pdfObject->Write(4,vertaalTekst("tot en met",$pdfObject->rapport_taal)." ");
  $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
  $pdfObject->Write(4,date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)." ");
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

  $pdfObject->ln(10);

  $pdfObject->setX(($pdfObject->marge + $pdfObject->widthB[0]+ $pdfObject->widthB[1]+ $pdfObject->widthB[2]));
  //$pdfObject->Cell(110,4,vertaalTekst("Inkomsten",$pdfObject->rapport_taal),0,1,"C");
  $pdfObject->ln(1);
  // achtergrond kleur
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);


  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

  $pdfObject->row(array(vertaalTekst("Boekdatum",$pdfObject->rapport_taal),
    vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
    '',
//    vertaalTekst("Uitgaven",$pdfObject->rapport_taal),
    vertaalTekst("Bruto",$pdfObject->rapport_taal),
//    vertaalTekst("Provisie",$pdfObject->rapport_taal),
    vertaalTekst("Kosten",$pdfObject->rapport_taal),
    vertaalTekst("Belasting",$pdfObject->rapport_taal),
    vertaalTekst("Netto",$pdfObject->rapport_taal)));

  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);

  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

  $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

}

function HeaderVOLK_L13($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderVOLK();
}

function HeaderCASH_L13($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderCASH();
}

function HeaderCASHY_L13($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderCASHY();
}

function HeaderGRAFIEK_L13($object)
{
  $pdfObject = &$object;

  $pdfObject->SetFillColor(39,62,102);
  $pdfObject->Rect($pdfObject->marge, 35, 286, 6, 'F');

  $pdfObject->SetTextColor(255,255,255);
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

  $pdfObject->ln(2);

  $pdfObject->Cell(100,4, vertaalTekst("Grafische verdeling",$pdfObject->rapport_taal),0,0);
  $pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);

}
function HeaderKERNV_L13($object)
{
  $pdfObject = &$object;
  // achtergrond kleur

  $pdfObject->SetFillColor(39,62,102);
  $pdfObject->Rect($pdfObject->marge, 35, 286, 6, 'F');

  $pdfObject->SetTextColor(255,255,255);
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);


  $pdfObject->ln(2);
  if($pdfObject->rapport_layout == 16 )
    $pdfObject->Cell(100,4, '',0,0);
  else
    $pdfObject->Cell(100,4, vertaalTekst("Overzicht waardeontwikkeling over verslagperiode",$pdfObject->rapport_taal),0,0);

  if($pdfObject->rapport_layout == 7 )
  {
    $pdfObject->Cell(100,4, date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
  }
  elseif($pdfObject->rapport_layout == 17 )
  {
    $pdfObject->SetDrawColor(0,0,0);
    $pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".$pdfObject->getKwartaal($pdfObject->rapport_datum)."e kwartaal",0,0);

  }
  else
  {
    $pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);

  }

  $pdfObject->ln(2);

  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->row(array("",
    "",
    "",
    "",
    "",
    ""));

  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);

//  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
}
/*
 	  function HeaderFRONT_L8($object)
	  {
  	  $pdfObject = &$object;
	  }

 	  function HeaderOIH_L8($object)
	  {
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIH();
	  }



	  function HeaderOIR_L8($object)
	  {
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIR();
	  }

		function HeaderHSE_L8($object)
	  {
  	  $pdfObject = &$object;
	    $pdfObject->HeaderHSE();
	  }

	  function HeaderOIB_L8($object)
	  {
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIB();
	  }

	  function HeaderOIV_L8($object)
	  {
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIV();
	  }



	  function HeaderPERFD_L8($object)
	  {
  	  $pdfObject = &$object;
  	//  $pdfObject->HeaderPERFD();
	  }

	  function HeaderVOLK_L8($object)
	  {
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderVOLK();
	  }

	  function HeaderVOLKD_L8($object)
	  {
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderVOLKD();
	  }

	  function HeaderVHO_L8($object)
	  {
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderVHO();
	  }
		  function HeaderTRANS_L8($object)
	  {
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderTRANS();
    }

	  	  function HeaderGRAFIEK_L8($object)
	  {
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderGRAFIEK();
  	  $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
	  }
	  	  function HeaderATT_L8($object)
	  {

  	  $pdfObject = &$object;
  	//  $pdfObject->HeaderATT();
	  }


	  */

function HeaderINDEX_L13($object)
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
?>

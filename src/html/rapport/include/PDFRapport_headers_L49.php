<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/09/30 04:20:50 $
 		File Versie					: $Revision: 1.27 $

 		$Log: PDFRapport_headers_L49.php,v $
 		Revision 1.27  2019/09/30 04:20:50  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2019/06/16 09:50:08  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2019/06/15 20:53:26  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2018/07/04 16:13:07  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2018/06/30 17:43:55  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2018/06/24 11:13:16  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2018/06/13 15:27:48  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2017/06/25 14:49:37  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2017/06/07 16:27:49  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2017/05/28 09:57:56  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2017/05/21 09:55:30  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2017/05/17 15:57:50  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2017/05/13 16:27:34  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2016/05/15 17:15:00  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2015/01/21 16:53:08  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2014/12/13 19:24:44  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2014/04/05 15:33:48  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2014/04/02 15:53:15  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2014/03/27 15:59:32  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/03/27 14:59:18  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2014/03/22 15:47:14  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/03/01 14:01:38  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2013/12/18 17:10:42  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/12/14 17:16:30  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/06/05 15:56:07  rvv
 		*** empty log message ***
 		
 	 		
*/

 function Header_basis_L49($object)
 { 
   $pdfObject = &$object;     
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

		if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  		$pdfObject->customPageNo = 0;
    }
    else
    {
  	if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  		$pdfObject->customPageNo = 0;

		$pdfObject->customPageNo++;

		$pdfObject->SetLineWidth($pdfObject->lineWidth);

		if(empty($pdfObject->top_marge))
			$pdfObject->top_marge = $pdfObject->marge;
		//$pdfObject->SetY($pdfObject->top_marge);

		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor[r],$pdfObject->rapport_kop2_fontcolor[g],$pdfObject->rapport_kop2_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$y = $pdfObject->GetY();

		// default header stuff
		$pdfObject->SetXY($pdfObject->marge,0);
    $pdfObject->last_rapport_type = $pdfObject->rapport_type;

		if($pdfObject->__appvar['consolidatie']['rekeningOnderdrukken'])
		{
		$pdfObject->rapport_koptext = $pdfObject->rapport_consolidatieKoptext;
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleFormat}", $pdfObject->rapport_portefeuilleFormat, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Portefeuille}", $pdfObject->rapport_portefeuille, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleVoorzet}", $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Depotbank}", $pdfObject->rapport_depotbank, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{DepotbankOmschrijving}", $pdfObject->rapport_depotbankOmschrijving, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Risicoklasse}", $pdfObject->rapport_risicoklasse, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Risicoprofiel}", $pdfObject->rapport_risicoprofiel, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Client}", $pdfObject->rapport_client, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{ClientVermogensbeheerder}", $pdfObject->rapport_clientVermogensbeheerder, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Naam1}", $pdfObject->__appvar['consolidatie']['portefeuillenaam1'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Naam2}", $pdfObject->__appvar['consolidatie']['portefeuillenaam2'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Accountmanager}", $pdfObject->rapport_accountmanager, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{VermogensbeheerderNaam}", $pdfObject->portefeuilledata['VermogensbeheerderNaam'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{crm.naam}", $pdfObject->portefeuilledata['crm.naam'], $pdfObject->rapport_koptext);
    $namen=$pdfObject->__appvar['consolidatie']['portefeuillenaam1'];
    if($pdfObject->__appvar['consolidatie']['portefeuillenaam1'] <> '')
      $namen.="\n".$pdfObject->__appvar['consolidatie']['portefeuillenaam1'];
		}
		else
		{
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleFormat}", $pdfObject->rapport_portefeuilleFormat, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Portefeuille}", $pdfObject->rapport_portefeuille, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleVoorzet}", $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Depotbank}", $pdfObject->rapport_depotbank, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{DepotbankOmschrijving}", $pdfObject->rapport_depotbankOmschrijving, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Risicoklasse}", $pdfObject->rapport_risicoklasse, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Risicoprofiel}", $pdfObject->rapport_risicoprofiel, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Client}", $pdfObject->rapport_client, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{ClientVermogensbeheerder}", $pdfObject->rapport_clientVermogensbeheerder, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Naam1}", $pdfObject->rapport_naam1, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Naam2}", $pdfObject->rapport_naam2, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Accountmanager}", $pdfObject->rapport_accountmanager, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{ModelPortefeuille}", $pdfObject->portefeuilledata['ModelPortefeuille'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{VermogensbeheerderNaam}", $pdfObject->portefeuilledata['VermogensbeheerderNaam'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{SoortOvereenkomst}", $pdfObject->portefeuilledata['SoortOvereenkomst'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{crm.naam}", $pdfObject->portefeuilledata['crm.naam'], $pdfObject->rapport_koptext);
    $namen=$pdfObject->rapport_naam1;
    if($pdfObject->rapport_naam2 <> '')
      $namen.="\n".$pdfObject->rapport_naam2;
		}

if($namen=='')
  $namen=$pdfObject->rapport_portefeuille;
$pdfObject->rapport_koptext = str_replace("{Namen}", $namen, $pdfObject->rapport_koptext);
$rapport_datum=date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum);
$pdfObject->rapport_koptext = str_replace("{RapportageDatum}", $rapport_datum, $pdfObject->rapport_koptext);


		$pdfObject->rapport_liquiditeiten_omschr = str_replace("{PortefeuilleVoorzet}",  $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_liquiditeiten_omschr);

		if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY")
		{
			$logopos = 85;
		}
		else
		{
			$logopos = 130;
		}

		//rapport_risicoklasse


	 
    $pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
		$pdfObject->SetY($y);

		
			$x = 160;


		$pdfObject->SetY($y);
		$pdfObject->SetX($x);


	  //$pdfObject->MultiCell(40,4,vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)."\n\n",0,'R');
	  $pdfObject->SetY($y+15);
    $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
	  $pdfObject->SetX(0);
	  $pdfObject->MultiCell(297,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
		$pdfObject->headerStart = $pdfObject->getY()+4;
		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
		$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
    $pdfObject->setY($pdfObject->rapportYstart);
 
    }
 }

 	function HeaderVKMD_L49($object)
	{
		$pdfObject = &$object;
    $widthBackup=$pdfObject->widths;
    $dataWidth=array(28,50,20,20,20,20,20,18,18,18,18,18,15);
    
    $pdfObject->SetWidths($dataWidth);
    $pdfObject->SetAligns(array('L','L','R','R','R','R','R','R','R','R','R','R','R'));
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
 
    $lastColors=$pdfObject->CellFontColor;
    unset($pdfObject->CellFontColor);
    unset($pdfObject->CellBorders);
    if(!isset($pdfObject->vmkHeaderOnderdrukken))
    {
      /*
      $pdfObject->SetFillColor($pdfObject->achtergrondKop[0],$pdfObject->achtergrondKop[1],$pdfObject->achtergrondKop[2]);
      $pdfObject->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1);

      $pdfObject->Row(array(vertaalTekst("Risico\ncategorie", $pdfObject->rapport_taal),
                   "\n" . vertaalTekst("Fonds", $pdfObject->rapport_taal),
                   "\n" . date('d-m-Y', $pdfObject->rapport_datumvanaf),
                   "\n" . date('d-m-Y', $pdfObject->rapport_datum),
                   "\n" . vertaalTekst("Mutaties", $pdfObject->rapport_taal),
                   "\n" . vertaalTekst("Resultaat", $pdfObject->rapport_taal),
                   vertaalTekst("Gemiddeld vermogen", $pdfObject->rapport_taal),
                   vertaalTekst("Doorl. kosten %", $pdfObject->rapport_taal),
                   vertaalTekst("Trans Cost %", $pdfObject->rapport_taal),
                   vertaalTekst("Perf Fee %", $pdfObject->rapport_taal)."\n ",
                   vertaalTekst("Fondskost. absoluut", $pdfObject->rapport_taal),
                   "\n" . vertaalTekst("Weging", $pdfObject->rapport_taal),
                   vertaalTekst("VKM\nBijdrage", $pdfObject->rapport_taal)));
      unset($pdfObject->fillCell);
      unset($pdfObject->vmkHeaderOnderdrukken);
      */
    //  $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
    }

    $pdfObject->widths=$widthBackup;
    $pdfObject->CellFontColor=$lastColors;
    $pdfObject->SetLineWidth(0.1);
	}
	


function HeaderRISK_L49($object)
{
  $pdfObject = &$object;
  //$pdfObject->HeaderVOLK();
}

function HeaderVKMS_L49($object)
{
  $pdfObject = &$object;
  //$pdfObject->HeaderVOLK();
}

function HeaderDOORKIJK_L49($object)
{
  $pdfObject = &$object;
  //$pdfObject->HeaderVOLK();
}

function HeaderDOORKIJKVR_L49($object)
{
  $pdfObject = &$object;
  //$pdfObject->HeaderVOLK();
}


function HeaderGRAFIEK_L49($object)
{
  $pdfObject = &$object;
  //$pdfObject->HeaderVOLK();
}

function HeaderMUT_L49($object)
{
  $pdfObject = &$object;
  $pdfObject->setY($pdfObject->rapportYstart);
  $width=297-(2*$pdfObject->marge);

  $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);//50+100+50+30+30
  $pdfObject->setWidths(array(0.13*$width-$pdfObject->witCell,$pdfObject->witCell,
                          0.46*$width-$pdfObject->witCell,$pdfObject->witCell,
                          0.15*$width-$pdfObject->witCell,$pdfObject->witCell,
                          0.13*$width-$pdfObject->witCell,$pdfObject->witCell,
                          0.13*$width-$pdfObject->witCell,$pdfObject->witCell
                         ));
  $pdfObject->SetAligns(array('L','C',
                          'L','C',
                          'L','C',
                          'R','C',
                          'R'));
  $pdfObject->SetFillColor($pdfObject->achtergrondKop[0],$pdfObject->achtergrondKop[1],$pdfObject->achtergrondKop[2]);
  $pdfObject->fillCell = array(1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1);
  $pdfObject->row(array('Boekdatum','','Omschrijving','',"Rekening",'',"Debet",'','Credit'));


  unset($pdfObject->fillCell);
  $pdfObject->Ln();
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
}

function HeaderPERFG_L49($object)
	{ 
	  $pdfObject = &$object;
		//$pdfObject->HeaderPERFG();
	}
  
  function HeaderPERF_L49($object)
	{ 
	  $pdfObject = &$object;
		//$pdfObject->HeaderPERFG();
	}

function HeaderPERFD_L49($object)
{
  $pdfObject = &$object;
  //$pdfObject->HeaderPERFG();
}
  function HeaderOIS_L49($object)
	{ 
	  $pdfObject = &$object;
		//$pdfObject->HeaderPERFG();
	} 
  
  function HeaderVAR_L49($object)
	{ 
	  $pdfObject = &$object;
		//$pdfObject->HeaderPERFG();
	} 
  	function HeaderATT_L49($object)
	{ 
	  $pdfObject = &$object;
    $pdfObject->setY($pdfObject->rapportYstart);

	}
  
	function HeaderOIB_L49($object)
	{
    $pdfObject = &$object;
    $pdfObject->setY($pdfObject->rapportYstart);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
    
	}
  
	function HeaderHSE_L49($object)
	{
    $pdfObject = &$object;
    $pdfObject->setY($pdfObject->rapportYstart);
    
    if($pdfObject->toonHeader==true)
    {
      $width=297-(2*$pdfObject->marge);
      
	  	$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);//50+100+50+30+30
      $pdfObject->setWidths(array(0.12*$width-$pdfObject->witCell,$pdfObject->witCell,
                                  0.30*$width-$pdfObject->witCell,$pdfObject->witCell,
                                  0.08*$width-$pdfObject->witCell,$pdfObject->witCell,

                              0.07*$width-$pdfObject->witCell,$pdfObject->witCell,
                              0.07*$width-$pdfObject->witCell,$pdfObject->witCell,
                              0.1*$width-$pdfObject->witCell,$pdfObject->witCell,
                              0.1*$width-$pdfObject->witCell,$pdfObject->witCell,
                              0.1*$width-$pdfObject->witCell,$pdfObject->witCell,
                              0.06*$width));
      $pdfObject->SetAligns(array('L','C','L','C','R','C','R','C','R','C','R','C','R','C','R','C','R'));
      $pdfObject->SetFillColor($pdfObject->achtergrondKop[0],$pdfObject->achtergrondKop[1],$pdfObject->achtergrondKop[2]);
      $pdfObject->fillCell = array(1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1);
      $pdfObject->row(array('Categorie','','Titel','','Aantal','','Koers','','Valuta','','Waarde VV','','Waarde EUR','','Beginwaarde','','Weging'));
      unset($pdfObject->fillCell);
      $pdfObject->Ln();
    }
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
	}

function HeaderTRANS_L49($object)
{
  $pdfObject = &$object;
  $pdfObject->setY($pdfObject->rapportYstart);

//  if($pdfObject->toonHeader==true)
//  {
    $width=297-(2*$pdfObject->marge);

    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);//50+100+50+30+30
    $pdfObject->setWidths(array(0.1*$width-$pdfObject->witCell,$pdfObject->witCell,
                            0.1*$width-$pdfObject->witCell,$pdfObject->witCell,
                            0.1*$width-$pdfObject->witCell,$pdfObject->witCell,
                            0.3*$width-$pdfObject->witCell,$pdfObject->witCell,
                            0.15*$width-$pdfObject->witCell,$pdfObject->witCell,
                            0.15*$width-$pdfObject->witCell,$pdfObject->witCell,
                            0.1*$width));
    $pdfObject->SetAligns(array('L','C',
                                'L','C',
                                'R','C',
                            'L','C',
                            'R','C',
                            'R','C',
                            'R','C',
                            'R'));
    $pdfObject->SetFillColor($pdfObject->achtergrondKop[0],$pdfObject->achtergrondKop[1],$pdfObject->achtergrondKop[2]);
    $pdfObject->fillCell = array(1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1);
    $pdfObject->row(array('Datum','','Soort Tr.','','Aantal','','Fonds','',"Waarde in ".$pdfObject->rapportageValuta,'',"Kostprijs in ".$pdfObject->rapportageValuta,'','Resultaat'));


    unset($pdfObject->fillCell);
    $pdfObject->Ln();
//  }
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
}


if(!function_exists('getTypeGrafiekData'))
{
  function getTypeGrafiekData($object, $type, $extraWhere = '', $items = array())
  {
    global $__appvar;
    $DB = new DB();
    if (!is_array($object->pdf->grafiekKleuren))
    {
      $q = "SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '" . $object->pdf->portefeuilledata['Vermogensbeheerder'] . "'";
      $DB->SQL($q);
      $DB->Query();
      $kleuren = $DB->LookupRecord();
      $kleuren = unserialize($kleuren['grafiek_kleur']);
      $object->pdf->grafiekKleuren = $kleuren;
    }
    $kleurVertaling = array('Beleggingscategorie' => 'OIB', 'Valuta' => 'OIV', 'regio' => 'OIR', 'beleggingssector' => 'OIS');
    $geenWaardeKoppeling = array('Beleggingscategorie' => 'geenWaarden', 'Valuta' => 'geenWaarden', 'regio' => 'Geen regio', 'beleggingssector' => 'Geen sector');
    
    $kleuren = $object->pdf->grafiekKleuren[$kleurVertaling[$type]];

    if (!isset($object->pdf->rapportageDatumWaarde) || $object->portefeuille <> $object->grafiekdataPortefeuille || $extraWhere != '')
    {
      $object->grafiekdataPortefeuille=$object->portefeuille;
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE " .
        " rapportageDatum = '" . $object->rapportageDatum . "' AND " .
        " portefeuille = '" . $object->portefeuille . "' $extraWhere"
        . $__appvar['TijdelijkeRapportageMaakUniek'];
      $DB->SQL($query);
      $DB->Query();
      $portefwaarde = $DB->nextRecord();
      $portTotaal = $portefwaarde['totaal'];
      if ($extraWhere == '')
      {
        $object->pdf->rapportageDatumWaarde = $portTotaal;
      }
    }
    else
    {
      $portTotaal = $object->pdf->rapportageDatumWaarde;
    }

    $query = "SELECT TijdelijkeRapportage.portefeuille, 
                     TijdelijkeRapportage." . $type . "Omschrijving as Omschrijving, 
                     if(TijdelijkeRapportage." . $type . " <> '',TijdelijkeRapportage." . $type . ",
                        if(TijdelijkeRapportage.type='rekening','Liquiditeiten',TijdelijkeRapportage." . $type . ")) as type,
                     if(TijdelijkeRapportage." . $type . "Volgorde>0,TijdelijkeRapportage." . $type . "Volgorde,127) as afdrukvolgorde,   
                     SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel  " .
      " FROM TijdelijkeRapportage
  			WHERE (TijdelijkeRapportage.portefeuille = '" . $object->portefeuille . "') AND " .
      " TijdelijkeRapportage.rapportageDatum = '" . $object->rapportageDatum . "' $extraWhere"
      . $__appvar['TijdelijkeRapportageMaakUniek'] .
      " GROUP BY " . $type . "  ORDER BY afdrukvolgorde";
    debugSpecial($query, __FILE__, __LINE__);

    $DB->SQL($query);
    $DB->Query();

    while ($categorien = $DB->NextRecord())
    {
      if ($categorien['type'] == '')
      {
        $categorien['type'] = 'Overige';
      }
      if ($categorien['Omschrijving'] == '')
      {
        $categorien['Omschrijving'] = $categorien['type'];
      }
      $object->pdf->veldOmschrijvingen[$type][$categorien['type']] = vertaalTekst($categorien['Omschrijving'], $object->pdf->rapport_taal);
      if ($categorien['type'] == '')
      {
        $categorien['type'] = 'geenWaarden';
      }

      if (count($items) > 0 && !in_array($categorien['type'], $items))
      {
        $categorien['type'] = 'Overige';
        $object->pdf->veldOmschrijvingen[$type][$categorien['type']] = 'Overige';
        $kleuren[$categorien['type']] = array('R' => array('value' => 100), 'G' => array('value' => 100), 'B' => array('value' => 100));
      }


      $valutaData[$categorien['type']]['port']['waarde'] += $categorien['subtotaalactueel'];
    }

    foreach ($valutaData as $waarde => $data)
    {
      if (isset($data['port']['waarde']))
      {
        $veldnaam = $object->pdf->veldOmschrijvingen[$type][$waarde];
        if ($veldnaam == '')
        {
          $veldnaam = 'Overige';
         // listarray($object->pdf->veldOmschrijvingen);
        }
        if ($waarde == 'geenWaarden')
        {
          $waarde = $geenWaardeKoppeling[$type];
        }

        $typeData['port']['procent'][$waarde] = $data['port']['waarde'] / $portTotaal;
        $typeData['port']['waarde'][$waarde] = $data['port']['waarde'];
        $typeData['grafiek'][$veldnaam] = $typeData['port']['procent'][$waarde] * 100;

        if ($kleuren[$waarde]['R']['value'] != 0 || $kleuren[$waarde]['G']['value'] <> 0 || $kleuren[$waarde]['B']['value'] <> 0)
        {
          $typeData['grafiekKleur'][] = array($kleuren[$waarde]['R']['value'], $kleuren[$waarde]['G']['value'], $kleuren[$waarde]['B']['value']);
        }
        else
        {
        //  listarray($kleuren);
        //  echo "$type $waarde <br>\n";ob_flush();
          $typeData['grafiekKleur'][] = array(rand(0, 255), rand(0, 255), rand(0, 255));
        }

      }
    }

    $object->pdf->grafiekData[$type] = $typeData;

  }
}


if(!function_exists('PieChart'))
{
  function PieChart($pdfObject, $w, $h, $data, $format, $colors = null, $titel = '', $legendaStart = '')
  {

    $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
    $pdfObject->SetLegends($data, $format);


    $XPage = $pdfObject->GetX();
    $YPage = $pdfObject->GetY();

    if ($pdfObject->debug == true)
    {
      $pdfObject->SetLineStyle(array('cap' => 'round', 'width' => 0.1, 'color' => array(0, 0, 255), 'dash' => '1,1'));

      $pdfObject->Rect($XPage, $YPage, $w, $h);
      $pdfObject->SetLineStyle(array('cap' => 'round', 'width' => 0.1, 'color' => array(0, 0, 255), 'dash' => 0));
    }

    $pdfObject->setXY($XPage, $YPage + 2);
    $pdfObject->SetFont($pdfObject->rapport_font, 'B', $pdfObject->rapport_fontsize + 2);
    $pdfObject->Cell($w, 4, $titel, 0, 1, 'L');
    $pdfObject->SetLineStyle(array('cap' => 'round', 'width' => 0.1, 'color' => array($pdfObject->koplijn[0], $pdfObject->koplijn[1], $pdfObject->koplijn[2]), 'dash' => 0));
    $pdfObject->line($XPage, $YPage + $pdfObject->rowHeight + 3, $XPage + $w, $YPage + $pdfObject->rowHeight + 3);
    //$pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>0));
    //$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
    //$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);

    $YPage = $YPage + $pdfObject->rowHeight + 4;
    $pdfObject->setXY($XPage, $YPage);
    $margin = 6;
    $hLegend = 2;
    $radius = min($w - $margin, $h - $margin); //
    $radius = ($radius / 2) - $margin;
    $XDiag = $XPage + $margin + $radius;
    $YDiag = $YPage + $margin + $radius;
    if ($colors == null)
    {
      for ($i = 0; $i < count($data); $i++)
      {
        $gray = $i * intval(255 / $pdfObject->NbVal);
        $colors[$i] = array($gray, $gray, $gray);
      }
    }

    //Sectors
    $pdfObject->SetDrawColor(255, 255, 255);
    $pdfObject->SetLineWidth(0.5);
    $angleStart = 0;
    $angleEnd = 0;
    $i = 0;
    $factor = $radius - 6;
    $pdfObject->SetFont($pdfObject->rapport_font, '', 7);
    $pdfObject->SetTextColor(255);//

    $toonGrafiek = true;
    foreach ($data as $val)
    {
      if ($val < 0)
      {
        $toonGrafiek = false;
      }
    }

    if ($toonGrafiek == true)
    {
      foreach ($data as $val)
      {
        $angle = (($val * 360) / doubleval($pdfObject->sum));
        //$pdfObject->SetDrawColor(255,255,0);
        $pdfObject->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
        if ($angle != 0)
        {
          $angleEnd = $angleStart + $angle;
          $avgAngle = ($angleStart + $angleEnd) / 360 * M_PI;
//echo "$val $angleStart, $angleEnd ".($angleEnd-$angleStart)."<br>\n";
          //$lineAngle=($angleEnd)/180*M_PI;
          $pdfObject->line($XDiag, $YDiag, $XDiag + (sin($lineAngle) * $factor), $YDiag - (cos($lineAngle) * $factor));
          if (($angleEnd - $angleStart) > 0.3)
          {
            $pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd, 'F');
          }

          //  echo $angle." $angleStart, $angleEnd, <br>\n";

          if ($val > 2)
          {
            //$pdfObject->SetXY($XDiag+(sin($avgAngle)*$factor)-5, $YDiag-(cos($avgAngle)*$factor)-2);
            if ($pdfObject->debug == true)
            {
              $pdfObject->SetLineStyle(array('cap' => 'round', 'width' => 0.1, 'color' => array(0, 0, 255)));
              $pdfObject->line($XDiag, $YDiag, $XDiag + (sin($avgAngle) * $factor), $YDiag - (cos($avgAngle) * $factor));
            }
            $pdfObject->SetXY($XDiag + (sin($avgAngle) * $factor) - 5, $YDiag - (cos($avgAngle) * $factor) - 2);
            $pdfObject->Cell(10, 4, number_format($val, 0, ',', '.') . '%', 0, 0, 'C');
          }
          $angleStart += $angle;
        }
        $i++;
      }
      if ($angleEnd != 360)
      {
        $pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
      }
      $pdfObject->SetTextColor(0);//
      

      $i = 0;
      foreach ($data as $val)
      {
        $angle = (($val * 360) / doubleval($pdfObject->sum));
        $pdfObject->SetLineStyle(array('cap' => 'round', 'width' => 0.3527, 'color' => array(255, 255, 255)));
        if ($angle != 0 && $angle != 360)
        {
          $angleEnd = $angleStart + $angle;
          $lineAngle = ($angleEnd) / 180 * M_PI;
          $pdfObject->line($XDiag, $YDiag, $XDiag + (sin($lineAngle) * $radius), $YDiag - (cos($lineAngle) * $radius));
          $angleStart += $angle;
        }
        $i++;
      }

      $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
      $pdfObject->SetDrawColor(0, 0, 0);

      //Legends
      $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);

      $x1 = $XPage + $margin;
      $x2 = $x1 + $hLegend + 2;
      $y1 = $YDiag + ($radius) + $margin + 5;
      
      /*
      if($pdfObject->debug==true)
      {
        $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>'1,1'));
        $pdfObject->line($XPage+2,$YDiag + ($radius) + $margin,$XPage+2,$YDiag + ($radius) + $margin +5);
        $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>0));
      } 
      */
      
      if (is_array($legendaStart))
      {
        $x1 = $legendaStart[0];
        $y1 = $legendaStart[1];
        $x2 = $x1 + $hLegend + 2;
        
      }
      elseif ($legendaStart == 'R')
      {
        $x1 = $XPage + $radius * 2 + $margin * 2;
        $x2 = $x1 + $margin / 2;
        $y1 = $YDiag - $radius;
      }
      
      
      for ($i = 0; $i < $pdfObject->NbVal; $i++)
      {
        $pdfObject->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
        $pdfObject->Rect($x1, $y1, $hLegend, $hLegend, 'F');
        $pdfObject->SetXY($x2, $y1);
        $pdfObject->Cell(0, $hLegend, $pdfObject->legends[$i]);
        $y1 += $hLegend * 2;
      }
      
    }//eind ToonGrafiek
    $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
    $pdfObject->SetY($YPage + $h);
    $pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r], $pdfObject->rapport_fontcolor[g], $pdfObject->rapport_fontcolor[b]);

  }
}

if(!function_exists('getFondsKoers'))
{
  function getFondsKoers($fonds, $datum)
  {
    $db = new DB();
    $query = "SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
    $db->SQL($query);
    $koers = $db->lookupRecord();

    return $koers['Koers'];
  }
}

if(!function_exists('getValutaKoers'))
{
  function getValutaKoers($valuta, $datum)
  {
    $db = new DB();
    $query = "SELECT Koers FROM Valutakoersen WHERE Valuta='$valuta' AND Datum <= '$datum' order by Datum desc limit 1";
    $db->SQL($query);
    $koers = $db->lookupRecord();

    return $koers['Koers'];
  }
}


if(!function_exists('getKwartaal'))
{
  function getKwartaal($juldatum)
  {
    $kwartaal = ceil(date("n", $juldatum) / 3);
    $kwartaal = $kwartaal . 'e';

    return $kwartaal;
  }
}


if(!function_exists('paginaVoet'))
{
  function paginaVoet($object)
  {
    $pdfObject = &$object;

  }
}

if(!function_exists('checkPage'))
{
  function checkPage($object)
  {
    $pdfObject = &$object;
    
    $pdfObject->SetFont($pdfObject->rapport_font, '', 6);
    $kwartaal = ceil(date("n", $pdfObject->rapport_datum) / 3);
    $jaar = date("Y", $pdfObject->rapport_datum);
    $naam = $pdfObject->portefeuilledata['Client'];
    if ($pdfObject->__appvar['consolidatie']['portefeuillenaam1'] <> '')
    {
      $naam = $pdfObject->__appvar['consolidatie']['portefeuillenaam1'];
    }
    if ($pdfObject->__appvar['consolidatie']['portefeuillenaam1'] <> '')
    {
      $naam .= ' ' . $pdfObject->__appvar['consolidatie']['portefeuillenaam2'];
    }
    
    if($pdfObject->lastPOST['anoniem']==1)
    {
      $txt = "$jaar/$kwartaal";
    }
    else
    {
      $txt = "" . $naam . " - $jaar/$kwartaal";
    }

    
    $pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r], $pdfObject->rapport_fontcolor[g], $pdfObject->rapport_fontcolor[b]);

    if (count($pdfObject->pages) % 2 == 0)
    {
      $pdfObject->TextWithRotation($pdfObject->marge - 2.5, 210 - $pdfObject->margeOnder, $txt, 90);
    }
    else
    {

      $pdfObject->TextWithRotation(297 - $pdfObject->marge + 4.5, 210 - $pdfObject->margeOnder - $pdfObject->GetStringWidth($txt), $txt, 270);

    }
  }
}

?>
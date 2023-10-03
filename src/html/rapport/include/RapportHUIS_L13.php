<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportHUIS_L13
{
  function RapportHUIS_L13($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "HUIS";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->pdf->rapport_titel = '';//"Huidige samenstelling effectenportefeuille";

    $this->pdf->rapport_koptextBackup = $this->pdf->rapport_koptext;
    $this->pdf->rapport_fontBackup = $this->pdf->rapport_font;
    $this->pdf->rapport_koptext = '';
    //	$this->pdf->rapport_font = 'Arial';

    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
  }

  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }

  function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
  {
    if ($VierDecimalenZonderNullen)
    {
      $getal = explode('.',$waarde);
      $decimaalDeel = $getal[1];
      if ($decimaalDeel != '0000' )
      {
        for ($i = strlen($decimaalDeel); $i >=0; $i--)
        {
          $decimaal = $decimaalDeel[$i-1];
          if ($decimaal != '0' && !$newDec)
          {
            $newDec = $i;
          }
        }
        return number_format($waarde,$newDec,",",".");
      }
      else
        return number_format($waarde,$dec,",",".");
    }
    else
      return number_format($waarde,$dec,",",".");
  }

  function printSubTotaal($title, $totaalA, $totaalB, $totaalC)
  {
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_subtotaal_fontstyle,$this->pdf->rapport_fontsize);

    $begin = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] ;
    $actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4];
    $verschil = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] + $this->pdf->widthB[5];

    $this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[4],$this->pdf->GetY());
    $this->pdf->Line($begin+2,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
    $this->pdf->Line($verschil+2,$this->pdf->GetY(),$verschil + $this->pdf->widthB[6],$this->pdf->GetY());

    if(!empty($totaalA))
      $totaalAtxt = $this->formatGetal($totaalA,2);

    if(!empty($totaalB))
      $totaalBtxt = $this->formatGetal($totaalB,2);

    if(!empty($totaalC))
      $totaalCtxt = $this->formatGetal($totaalC,2);

    $this->pdf->SetX(0);
    $this->pdf->Cell($begin ,4, $title, 0,0, "R");
    $this->pdf->Cell($this->pdf->widthB[4],4,$totaalAtxt, 0,0, "R");
    $this->pdf->Cell($this->pdf->widthB[5],4,$totaalBtxt, 0,0, "R");
    $this->pdf->Cell($this->pdf->widthB[6],4,$totaalCtxt, 0,1, "R");
  }

  function printTotaal($title, $totaalA, $totaalB, $totaalC)
  {
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);

    // lege regel
    $this->pdf->ln();

    $begin 	 = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] ;
    $actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] ;
    $verschil = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] + $this->pdf->widthB[5];

    if(!empty($totaalA))
      $totaalAtxt = $this->formatGetal($totaalA,2);

    if(!empty($totaalB))
      $totaalBtxt = $this->formatGetal($totaalB,2);

    if(!empty($totaalC))
      $totaalCtxt = $this->formatGetal($totaalC,2);

    $this->pdf->Line($begin+2,$this->pdf->GetY(),$begin + $this->pdf->widthB[4],$this->pdf->GetY());
    $this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[5],$this->pdf->GetY());
    $this->pdf->Line($verschil+2,$this->pdf->GetY(),$verschil + $this->pdf->widthB[6],$this->pdf->GetY());

    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_subtotaal_fontstyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetX(0);

    $this->pdf->Cell($begin-$this->pdf->widthB[4],4, $title, 0,0, "R");

    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);

    $this->pdf->SetX(0);
    $this->pdf->Cell($begin ,4, "", 0,0, "R");
    $this->pdf->Cell($this->pdf->widthB[4],4,$totaalAtxt, 0,0, "R");
    $this->pdf->Cell($this->pdf->widthB[5],4,$totaalBtxt, 0,0, "R");
    $this->pdf->Cell($this->pdf->widthB[6],4,$totaalCtxt, 0,1, "R");

    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_subtotaal_fontstyle,$this->pdf->rapport_fontsize);

    $this->pdf->setDash(1,1);


    if(!empty($totaalA))
      $this->pdf->Line($begin+2,$this->pdf->GetY(),$begin + $this->pdf->widthB[4],$this->pdf->GetY());

    $this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[5],$this->pdf->GetY());

    $this->pdf->setDash();

    $this->pdf->ln();

    return $totaalA;
  }

  function printKop($title, $type="default")
  {
    /*
    switch($type)
    {
      case "b" :
        $font = $this->pdf->rapport_font;
        $fontsize = $this->pdf->rapport_fontsize;
        $fonttype = 'b';
        break;
      case "bi" :
        $font = $this->pdf->rapport_font;
        $fontsize = $this->pdf->rapport_fontsize;
        $fonttype = 'bi';
        break;
      case "i" :
        $font = $this->pdf->rapport_font;
        $fontsize = $this->pdf->rapport_fontsize;
        $fonttype = 'i';
        break;
      default :
        $font = $this->pdf->rapport_font;
        $fontsize = $this->pdf->rapport_fontsize;
        $fonttype = '';
        break;
    }
    */
    $this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
    $this->pdf->SetX($this->pdf->marge);
    $this->pdf->MultiCell(90,4,vertaalTekst( $title ,$this->pdf->rapport_taal), 0, "L");
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
  }

  function writeRapport()
  {
    global $__appvar;
    $DB = new DB();

    // voor data

    $this->pdf->widthB = array(0,23,62,30,30,10,30);
    $this->pdf->alignB = array('L','R','L','R','R','R','R');

    $this->pdf->widthC = array(0,23    ,14,11,  8,  8,  5,  14,    2,  30,30,10,30);
    $this->pdf->alignC = array('L','R','L','L','L','R','R','R',  'C',  'R','R','R','R');

    // voor kopjes
    $this->pdf->widthA = array(55,20,20,25,25,35);
    $this->pdf->alignA = array('L','R','R','R','R','R','R','R','R');

    $this->pdf->startPagina = $this->pdf->customPageNo;

    if($this->pdf->rapportToonRente == false)
      $renteFilter=" AND Type <> 'rente' ";
    else
      $renteFilter='';
//// liquiditeiten

    $query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
      " TijdelijkeRapportage.actueleValuta , ".
      " TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
      " TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
      " TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille, TijdelijkeRapportage.valuta ".
      " FROM TijdelijkeRapportage WHERE ".
      " TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
      " TijdelijkeRapportage.type = 'rekening'  ".
      " AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'].$renteFilter.
      " ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
    debugSpecial($query,__FILE__,__LINE__);
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();

    $totaalLiquiditeitenInValuta = 0;
    $rekeningAantal = 0;
    while($data = $DB->NextRecord())
    {
      $totaalLiquiditeitenEuro += $data['actuelePortefeuilleWaardeEuro'];

      $liquiditeitenData[]=array("","",
        " ".$data['fondsOmschrijving'],
        "",
        $this->formatGetal($data['actuelePortefeuilleWaardeInValuta'],2),
        $data['valuta'],
        $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],2));
      $rekeningAantal ++;

    }

////

    $this->pdf->saldoGeldrekeningen= $this->formatGetal($totaalLiquiditeitenEuro,2);

    $this->pdf->AddPage('P');
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

    // haal totaalwaarde op om % te berekenen
    $DB = new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."'".$renteFilter
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];

    $actueleWaardePortefeuille = 0;

    $query = "SELECT Beleggingscategorien.Omschrijving,
						 TijdelijkeRapportage.valuta,
						 TijdelijkeRapportage.beleggingscategorie,
						 SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) AS subtotaalbegin,
						 SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel
				FROM  TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta)
					  LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie)
				WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."'
				AND TijdelijkeRapportage.type = 'fondsen'
				AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
      .$__appvar['TijdelijkeRapportageMaakUniek'].$renteFilter.
      " GROUP BY TijdelijkeRapportage.beleggingscategorie ". //, TijdelijkeRapportage.valuta
      " ORDER BY Beleggingscategorien.Afdrukvolgorde asc, Valutas.Afdrukvolgorde asc";
    debugSpecial($query,__FILE__,__LINE__);
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();

    while($categorien = $DB->NextRecord())
    {
      $subtotaalverschil = 0;
      // print categorie headers
      $this->pdf->SetWidths($this->pdf->widthA);
      $this->pdf->SetAligns($this->pdf->alignA);

      //$categorien['Omschrijving'] = strtoupper($categorien['Omschrijving']);
      // print totaal op hele categorie.
      if($lastCategorie <> $categorien['Omschrijving'] && !empty($lastCategorie) )
      {
        $actueleWaardePortefeuille += $totaalactueel;
        $totalen[$categorien['beleggingscategorie']] = array('omschrijving'=>$lastCategorie,
          'totaal'=>$totaalactueel);

        //  $title = vertaalTekst("Subtotaal" ,$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie ,$this->pdf->rapport_taal);
        //	$actueleWaardePortefeuille += $this->printTotaal($title, $totaalactueel,$totaalbegin, $totaalverschil);
        $totaalbegin = 0;
        $totaalactueel = 0;
        $totaalverschil = 0;

      }

      if($lastCategorie <> $categorien['Omschrijving'])
      {
        if($this->pdf->GetY() > 250)
          $this->pdf->AddPage('P');
        $this->printKop($categorien['Omschrijving'], "bi");
      }
      // subkop (valuta)
//			$this->printKop("Waarden ".$categorien[valuta], "");

      // print detail (select from tijdelijkeRapportage)

      $subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
        " TijdelijkeRapportage.actueleValuta, ".
        " TijdelijkeRapportage.totaalAantal, ".
        //		" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
        //		" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
        " TijdelijkeRapportage.beginPortefeuilleWaardeEuro, ".
        " TijdelijkeRapportage.actueleFonds, ".
        "  TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
        "  TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
        "   TijdelijkeRapportage.beleggingscategorie,  ".
        "   TijdelijkeRapportage.valuta, ".
        "  TijdelijkeRapportage.portefeuille, ".
        " Fondsen.OptieExpDatum, ".
        " UNIX_TIMESTAMP(Fondsen.Rentedatum) as coupondatum ".
        " FROM TijdelijkeRapportage, Fondsen WHERE ".
        " TijdelijkeRapportage.Fonds = Fondsen.Fonds AND ".
        " TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
        " TijdelijkeRapportage.beleggingscategorie =  '".$categorien['beleggingscategorie']."' AND ".
        //		" TijdelijkeRapportage.valuta =  '".$categorien[valuta]."' AND ".
        " TijdelijkeRapportage.type =  'fondsen' AND ".
        " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
        .$__appvar['TijdelijkeRapportageMaakUniek'].$renteFilter.
        " ORDER BY  Fondsen.OptieBovenliggendFonds  ,Fondsen.OptieExpDatum , TijdelijkeRapportage.fondsOmschrijving  asc";
      //	echo "<br>".$subquery."<br>".$categorien[beleggingscategorie];
      debugSpecial($subquery,__FILE__,__LINE__);
      $DB2 = new DB();
      $DB2->SQL($subquery);
      $DB2->Query();//exit;
      while($subdata = $DB2->NextRecord())
      {//listarray($subdata); echo $subquery; exit;
        $this->pdf->SetWidths($this->pdf->widthB);
        $this->pdf->SetAligns($this->pdf->alignB);

        //$this->formatGetal($subdata[beginwaardeLopendeJaar],2),
        //$this->formatGetal($subdata[beginPortefeuilleWaardeInValuta],2),
        $verschil = $subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'];

        $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);



        if ($subdata['OptieExpDatum'] != '')
          $vervalDatum = substr($subdata['OptieExpDatum'],4,2).'/'.substr($subdata['OptieExpDatum'],2,2);
        elseif($subdata['coupondatum'] > 0)
          $vervalDatum = date("d/m",$subdata['coupondatum']);
        else
          $vervalDatum = '';

        if ($subdata['OptieExpDatum'] != '')
        {
          $omschrijving=$subdata['fondsOmschrijving'];
          $omschrijving=str_replace(';', ' ; ', $omschrijving);
          $omschrijving=preg_replace('!\s+!', ' ', $omschrijving);

          $optieParts=explode(" ", $omschrijving,6);
          $this->pdf->SetWidths($this->pdf->widthC);
          $this->pdf->SetAligns($this->pdf->alignC);
          $this->pdf->row(array("",
            $this->formatAantal($subdata['totaalAantal'],0,true),
            " ".trim($optieParts[0]),trim($optieParts[1]),trim($optieParts[2]),trim($optieParts[3]),trim($optieParts[4]),trim($optieParts[5]),'',
            $vervalDatum,
            $this->formatGetal($subdata['actueleFonds'],2),$subdata['valuta'],
            $this->formatGetal($subdata['actuelePortefeuilleWaardeEuro']),
          ));
        }
        else
        {
          $this->pdf->row(array("",
            $this->formatAantal($subdata['totaalAantal'],0,true),
            " ".$subdata['fondsOmschrijving'],
            $vervalDatum,
            $this->formatGetal($subdata['actueleFonds'],2),$subdata['valuta'],
            $this->formatGetal($subdata['actuelePortefeuilleWaardeEuro']),
          ));
        }
        $valutaWaarden[$categorien['valuta']] = $subdata['actueleValuta'];
      }

      $totaalactueel += $categorien['subtotaalactueel'];
      $lastCategorie = $categorien['Omschrijving'];
    }

    // totaal voor de laatste categorie

    $totalen[$lastCategorie] = array('omschrijving'=>$lastCategorie,
      'totaal'=>$totaalactueel);
    $actueleWaardePortefeuille +=$totaalactueel;// $this->printTotaal("Subtotaal ".$lastCategorie, $totaalactueel,$totaalbegin, $totaalverschil);



    $totaalRenteInValuta = 0 ;

    //	while($categorien = $DB->NextRecord())
    //	{
    $subtotaalRenteInEUR = 0;

    $subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
      " TijdelijkeRapportage.actueleValuta , ".
      " TijdelijkeRapportage.rentedatum, ".
      " TijdelijkeRapportage.renteperiode, ".
      " TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
      " TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
      " TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
      " FROM TijdelijkeRapportage WHERE ".
      " TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
      " TijdelijkeRapportage.type = 'rente' ".$renteFilter.
      //		"AND TijdelijkeRapportage.valuta =  '".$categorien[valuta]."'".
      " AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".
      $__appvar['TijdelijkeRapportageMaakUniek'].
      " ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
    debugSpecial($subquery,__FILE__,__LINE__);
    $DB2 = new DB();
    $DB2->SQL($subquery);
    $DB2->Query();
    if($DB2->records())
    {
      if($this->pdf->rapportToonRente)
      {
        if($this->pdf->GetY() > 250)
          $this->pdf->AddPage('P');
        $this->printKop("Opgelopen rente","bi");
      }
      while ($subdata = $DB2->NextRecord())
      {
        if ($this->pdf->rapport_HSE_rentePeriode)
        {
          $rentePeriodetxt = "  " . date("d-m", db2jul($subdata['rentedatum']));
          if ($subdata['renteperiode'] <> 12 && $subdata['renteperiode'] <> 0)
          {
            $rentePeriodetxt .= " / " . $subdata['renteperiode'];
          }
        }

        $subtotaalRenteInEUR += $subdata['actuelePortefeuilleWaardeEuro'];
        $this->pdf->SetWidths($this->pdf->widthB);
        $this->pdf->SetAligns($this->pdf->alignB);

        $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
        if ($this->pdf->rapportToonRente)
        {
          $this->pdf->row(array("", "", " " . $subdata['fondsOmschrijving'] . $rentePeriodetxt, "", "", '',
            $this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'])));
        }

      }
    }

    if($this->pdf->rapportToonRente)
      $totalen['Rente'] = array('omschrijving'=>'Opgelopen rente','totaal'=>$subtotaalRenteInEUR);

    $totaalRenteInEUR += $subtotaalRenteInEUR;
    //	}

    // totaal op rente
    $actueleWaardePortefeuille += $totaalRenteInEUR;// $this->printTotaal("Subtotaal Opgelopen rente: ", $totaalRenteInValuta,"","");

    // Liquiditeiten
//
    if($rekeningAantal > 1)
    {
      $this->printKop("Liquiditeiten","bi");
      $totaalLiquiditeitenInValuta = 0;
      $this->pdf->SetWidths($this->pdf->widthB);
      $this->pdf->SetAligns($this->pdf->alignB);
      $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
      while (list($key, $data) = each($liquiditeitenData))
      {
        $this->pdf->row($data);
      }
    }
    $totalen['Liquiditeiten'] = array('omschrijving'=>'Liquiditeiten',
      'totaal'=>$totaalLiquiditeitenEuro);
    $actueleWaardePortefeuille += $totaalLiquiditeitenEuro;//$this->printTotaal("", $totaalLiquiditeitenEuro, "","");

    // check op totaalwaarde!
    if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
    {
      echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
      ob_flush();
    }


    if(!isset($this->pdf->rowHeight))
      $this->pdf->rowHeight=4;
    if(!isset($this->pdf->PageBreakTrigger))
      $this->pdf->PageBreakTrigger=282;

    $aantal = count($totalen)+3;
    $hoogte = $aantal * $this->pdf->rowHeight ;
    if($this->pdf->GetY() + $hoogte > $this->pdf->PageBreakTrigger)
    {
      //echo $this->pdf->GetY()." + $hoogte > ".$this->pdf->PageBreakTrigger."<br>\n";exit;
      $this->pdf->addPage('P');
    }
    $this->pdf->ln();
    $this->pdf->ln();
    $beginTekst = vertaalTekst("Totaal categorie",$this->pdf->rapport_taal);//per categorie in ".$this->pdf->rapportageValuta;
    while (list($key, $data) = each($totalen))
    {
      $this->pdf->SetWidths(array(50,50,30,20,20));
      $this->pdf->SetAligns(array('L','L','R','R'));
      if(round($data['totaal'],2) <> 0.0)
      {
        $this->pdf->Row(Array($beginTekst,
          vertaalTekst($data['omschrijving'],$this->pdf->rapport_taal),
          $this->formatGetal($data['totaal'], 2),
          $this->formatGetal(($data['totaal'] / $actueleWaardePortefeuille) * 100, 2) . ' %'
        ));
        $beginTekst = '';
      }
      $totaalCategorieEur += $data['totaal'];
    }
    $this->pdf->Line(112,$this->pdf->GetY(),138,$this->pdf->GetY());
    $this->pdf->Line(144,$this->pdf->GetY(),158,$this->pdf->GetY());
    $this->pdf->SetY($this->pdf->GetY()+1);
    $this->pdf->SetWidths(array(50,50,30,20,20));
    $this->pdf->SetAligns(array('L','L','R','R'));

    $this->pdf->Row(Array('',
      '',
      $this->formatGetal($actueleWaardePortefeuille,2),
      $this->formatGetal(($totaalCategorieEur/$actueleWaardePortefeuille)*100,2).' %'
    )) ;

//
    $this->pdf->ln();
    reset($totalen);

    $query = "SELECT
sum(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) as inValuta,
sum(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as inEur,
TijdelijkeRapportage.valuta,
Valutas.omschrijving
FROM TijdelijkeRapportage, Valutas
WHERE
TijdelijkeRapportage.valuta = Valutas.valuta AND
TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'
" .$__appvar['TijdelijkeRapportageMaakUniek'].$renteFilter."
GROUP BY TijdelijkeRapportage.valuta
ORDER BY Valutas.afdrukvolgorde asc";
    $DB->SQL($query);
    $DB->Query();


    $aantal = $DB->records()+1;
    $hoogte = $aantal * $this->pdf->rowHeight +10;

    if($this->pdf->GetY() + $hoogte > $this->pdf->PageBreakTrigger)
      $this->pdf->addPage('P');

    $this->pdf->ln();
    $beginTekst = vertaalTekst("Totaal valuta",$this->pdf->rapport_taal);//per valuta in ".$this->pdf->rapportageValuta;
    while($data = $DB->NextRecord())
    {
      $this->pdf->SetWidths(array(50,50,30,20,20));
      $this->pdf->SetAligns(array('L','L','R','R'));
      $this->pdf->Row(Array($beginTekst,
        vertaalTekst($data['omschrijving'],$this->pdf->rapport_taal),
        $this->formatGetal($data['inEur'],2),
        $this->formatGetal(($data['inEur']/$actueleWaardePortefeuille)*100,2).' %'
      )) ;
      $valutalTotaalEur += $data['inEur'];
      $beginTekst ='';
    }
    $this->pdf->Line(112,$this->pdf->GetY(),138,$this->pdf->GetY());
    $this->pdf->Line(144,$this->pdf->GetY(),158,$this->pdf->GetY());
    $this->pdf->SetY($this->pdf->GetY()+1);

    $this->pdf->SetWidths(array(50,50,30,20,20));
    $this->pdf->SetAligns(array('L','L','R','R'));
    $this->pdf->Row(Array('',
      '',
      $this->formatGetal($actueleWaardePortefeuille,2),
      $this->formatGetal(($valutalTotaalEur/$actueleWaardePortefeuille)*100,2).' %'
    )) ;

    for($i=$this->pdf->startPagina;$i<=$this->pdf->PageNo();$i++)
    {
      if(isset($this->pdf->pages[$i]))
        $this->pdf->pages[$i] = str_replace('{LastPage}', $this->pdf->customPageNo, $this->pdf->pages[$i]);
    }

//    if($this->pdf->portefeuilledata['AEXVergelijking'] > 0)
//	    $this->printAEXVergelijking($this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);

    $this->restoreKoptekst();

  }

  function restoreKoptekst()
  {
    $this->pdf->rapport_koptext = $this->pdf->rapport_koptextBackup;
    $this->pdf->rapport_font    = $this->pdf->rapport_fontBackup;
  }


}
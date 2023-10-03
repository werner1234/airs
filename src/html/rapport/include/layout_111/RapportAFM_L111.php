<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportAFM_L111
{
  function RapportAFM_L111($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "AFM";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

    $this->pdf->rapport_titel = "Portefeuille";


    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
    $this->verdeling='beleggingscategorie';
    //$this->verdeling='hoofdcategorie';
  }

  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }

  function formatGetalKoers($waarde, $dec , $start = false)
  {
    if ($start == false)
    {
      $waarde = $waarde / $this->pdf->ValutaKoersEind;
      return number_format($this->pdf->ValutaKoersEind,2,",",".") ." - ".number_format($waarde,$dec,",",".");
    }
    else
    {
      $waarde = $waarde / $this->pdf->ValutaKoersBegin;
      return number_format($this->pdf->ValutaKoersBegin,2,",",".") ." - ".number_format($waarde,$dec,",",".");
    }
    return number_format($waarde,$dec,",",".");
  }



  function getDividend($fonds)
  {
    global $__appvar;

    if($fonds=='')
      return 0;

    $query="SELECT rapportageDatum,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro as actuelePortefeuilleWaardeEuro,
         TijdelijkeRapportage.type,
         TijdelijkeRapportage.totaalAantal
     FROM TijdelijkeRapportage
     WHERE 
       TijdelijkeRapportage.fonds='$fonds' AND
       portefeuille = '".$this->portefeuille."'".$__appvar['TijdelijkeRapportageMaakUniek']. "
     GROUP BY rapportageDatum,TijdelijkeRapportage.type";

    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $totaal=0;
    while($data = $DB->nextRecord())
    {
      if($data['type']=='rente')
        $rente[$data['rapportageDatum']]=$data['actuelePortefeuilleWaardeEuro'];
      elseif($data['type']=='fondsen')
        $aantal[$data['rapportageDatum']]=$data['totaalAantal'];
    }

    $totaal+=($rente[$this->rapportageDatum]-$rente[$this->rapportageDatumVanaf]);
    $totaalCorrected=$totaal;

    $query="SELECT Boekdatum,(Debet*Valutakoers) as Debet,(Credit*valutakoers) as Credit,Bedrag,Rekeningmutaties.Omschrijving 
     FROM Rekeningmutaties 
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening 
     JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND 
     Rekeningmutaties.Boekdatum >= '".	$this->rapportageDatumVanaf."' AND 
     Rekeningmutaties.Boekdatum <= '".	$this->rapportageDatum."' AND
     Rekeningmutaties.Fonds='$fonds' AND 
     Grootboekrekeningen.Opbrengst=1";
    $DB->SQL($query);
    $DB->Query();
    //echo "$query <br>\n";
    while($data = $DB->nextRecord())
    {
      $boekdatum=substr($data['Boekdatum'],0,10);
      if(!isset($aantal[$data['Boekdatum']]))
      {
        $fondsAantal=fondsAantalOpdatum($this->portefeuille,$fonds,$data['Boekdatum']);
        $aantal[$boekdatum]=$fondsAantal['totaalAantal'];
      }
      $aandeel=1;

      if($aantal[$boekdatum] > $aantal[$this->rapportageDatum])
      {
        $aandeel=$aantal[$this->rapportageDatum]/$aantal[$boekdatum];
      }
      // echo "$fonds $aandeel  $boekdatum ".$this->rapportageDatum." ".($data['Credit']-$data['Debet'])."<br>\n";
      $totaal+=($data['Credit']-$data['Debet']);
      $totaalCorrected+=(($data['Credit']-$data['Debet'])*$aandeel);
    }


    return array('totaal'=>$totaal,'corrected'=>$totaalCorrected);
  }

  function getDataHuidigejaar($portefeuille)
  {
    global $__appvar;


    $DB=new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];

    $this->pdf->SetDrawColor(0,0,0);
    // haal totaalwaarde op om % te berekenen

    $subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
      " TijdelijkeRapportage.fonds, ".
      " TijdelijkeRapportage.actueleValuta, ".
      " TijdelijkeRapportage.totaalAantal, ".
      " TijdelijkeRapportage.beginwaardeLopendeJaar , ".
      " TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
      " TijdelijkeRapportage.Valuta, ".
      " TijdelijkeRapportage.beginPortefeuilleWaardeEuro /  ".$this->pdf->ValutaKoersBegin. " as beginPortefeuilleWaardeEuro, ".
      " TijdelijkeRapportage.actueleFonds,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				 TijdelijkeRapportage.type,
				 Fondsen.isinCode as isinCode,
				 TijdelijkeRapportage.historischeWaarde,
				 (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal,
(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta,
				 TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille, TijdelijkeRapportage.rekening ".
      " FROM TijdelijkeRapportage
				  LEFT JOIN Fondsen ON TijdelijkeRapportage.Fonds=Fondsen.Fonds WHERE ".
      " TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND ".
      " TijdelijkeRapportage.type IN('fondsen') AND ".
      " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";//exit;

    // print detail (select from tijdelijkeRapportage)
    debugSpecial($subquery,__FILE__,__LINE__);
    $DB2 = new DB();
    $DB2->SQL($subquery);
    $DB2->Query();
    $resulaten=array();
    $fondsGegevens=array();
    while($subdata = $DB2->NextRecord())
    {

      $dividend=$this->getDividend($subdata['fonds'],$portefeuille);
      $resultaatEur=($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] + $dividend['corrected']);
      $subdata['resultaatEur']=$resultaatEur;
      $procentResultaat = ($resultaatEur / ($subdata['beginPortefeuilleWaardeEuro'] /100));
      $aandeel=$subdata['actuelePortefeuilleWaardeEuro']/$totaalWaarde;
      $procentResultaatBijdrage=$procentResultaat*$aandeel;

      if($subdata['beginPortefeuilleWaardeEuro'] < 0)
        $procentResultaat = -1 * $procentResultaat;


      if($procentResultaat < 1000 || $procentResultaat > -1000)
      {
        $resulaten[$subdata['fonds']]=$procentResultaatBijdrage;
        $subdata['rendement']=$procentResultaat;
        $subdata['rendementBijdrage']=$procentResultaatBijdrage;
      }
      $fondsGegevens[$subdata['fonds']]=$subdata;

    }
    asort($resulaten);
    $i=0;
    $negatief=array();
    foreach($resulaten as $fonds=>$rendment)
    {
      $negatief[$fonds]=$fondsGegevens[$fonds];
      if($i==9)
        break;
      $i++;
    }
    $resulaten=array_reverse($resulaten,true);
    $i=0;
    $positief=array();
    foreach($resulaten as $fonds=>$rendment)
    {
      $positief[$fonds]=$fondsGegevens[$fonds];
      if($i==9)
        break;
      $i++;
    }

    return array('positief'=>$positief,'negatief'=>$negatief);

  }

  function toonTopTien()
  {
    //  $this->pdf->ln(10);
    $data = $this->getDataHuidigejaar($this->portefeuille);


    $xPos = $this->pdf->getX();
    $xStartPos = $xPos;

    $this->toonTabelTop10($xStartPos,$data['positief'],vertaalTekst("Grootste bijdrage", $this->pdf->rapport_taal));
    $xStartPos +=140;
    $this->toonTabelTop10($xStartPos,$data['negatief'],vertaalTekst("Kleinste bijdrage", $this->pdf->rapport_taal));
    $this->pdf->fillCell=array();
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0,0,0);
  }

  function toonTabel($tabeldata,$titel, $xStartPos = 10)
  {

    $this->pdf->SetWidths(array($xStartPos,10,70,40));
    $this->pdf->SetAligns(array('L','L','L','R'));
    $this->pdf->row(array('','','Fonds',vertaalTekst($titel, $this->pdf->rapport_taal)));
    $n=1;
    foreach($tabeldata as $fonds=>$fondsData)
    {
      $this->pdf->row(array('',$n,$fondsData['fondsOmschrijving'],round($fondsData['rendementBijdrage']*100)));
      $n++;
    }

  }

  function toonTabelTop10($xmarge,$tabeldata,$titel)
  {
    $this->pdf->setY(100);
    $this->pdf->SetWidths(array($xmarge,10,78,20,12));
//    $this->pdf->SetWidths(array($xmarge,6,48,20,12));
    $this->pdf->SetAligns(array('L','L','L','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
//    $this->pdf->CellBorders=array('',array('L','T','U'),array('T','U','L'),array('T','U','L'),array('T','U','L','R'));
    $this->pdf->row(array('','',$titel,'in EUR','in %'));
//    $this->pdf->CellBorders=array('',array('L'),array('L'),array('L'),array('L','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $n=1;
    $aantal=count($tabeldata);
    foreach($tabeldata as $fonds=>$fondsData)
    {
      //$this->pdf->row(array('',$n,$fondsData['fondsOmschrijving'],round($fondsData['rendementBijdrage']*100)));
      $omschrijving=$this->testTxtLength($fondsData['fondsOmschrijving'],2);
      if($n==$aantal)
      {
//        $this->pdf->CellBorders=array('',array('L','U'),array('U','L'),array('U','L'),array('U','L','R'));
      }
      $this->pdf->row(array('',$n,$omschrijving,$this->formatGetal($fondsData['resultaatEur'],0,false,true),$this->formatGetal($fondsData['rendementBijdrage'],2,false,true)));//
      $n++;
    }

  }

  function testTxtLength($txt,$cell=1)
  {
    $stringWidth=$this->pdf->GetStringWidth($txt."   ");
    if($stringWidth < $this->pdf->widths[$cell])
    {
      return $txt;
    }
    else
    {
      $tmpTxt=$txt;
      for($i=strlen($txt); $i > 0; $i--)
      {
        if($this->pdf->GetStringWidth($tmpTxt."...   ")>$this->pdf->widths[$cell])
          $tmpTxt=substr($txt,0,$i);
        else
          return $tmpTxt.'...';
      }
      return $tmpTxt;
    }
  }

  function writeRapport()
  {
    global $__appvar;

    $query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $this->portefeuilledata = $DB->nextRecord();

    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];


    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    $this->pdf->SetDrawColor(0,0,0);
    // haal totaalwaarde op om % te berekenen

    $subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
      " TijdelijkeRapportage.fonds, ".
      " TijdelijkeRapportage.actueleValuta, ".
      " TijdelijkeRapportage.totaalAantal, ".
      " TijdelijkeRapportage.beginwaardeLopendeJaar , ".
      " TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
      " TijdelijkeRapportage.Valuta, ".
      " TijdelijkeRapportage.beginPortefeuilleWaardeEuro /  ".$this->pdf->ValutaKoersBegin. " as beginPortefeuilleWaardeEuro, ".
      " TijdelijkeRapportage.actueleFonds,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				 TijdelijkeRapportage.type,
				 Fondsen.isinCode as isinCode,
				 TijdelijkeRapportage.historischeWaarde,
				 (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal,
(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta,
				 TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille, TijdelijkeRapportage.rekening ".
      " FROM TijdelijkeRapportage
				  LEFT JOIN Fondsen ON TijdelijkeRapportage.Fonds=Fondsen.Fonds WHERE ".
      " TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
      " TijdelijkeRapportage.type IN('fondsen') AND ".
      " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";//exit;

    // print detail (select from tijdelijkeRapportage)
    debugSpecial($subquery,__FILE__,__LINE__);
    $DB2 = new DB();
    $DB2->SQL($subquery);
    $DB2->Query();
    $resulaten=array();
    $fondsGegevens=array();
    while($subdata = $DB2->NextRecord())
    {

      $dividend=$this->getDividend($subdata['fonds']);
      $procentResultaat = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] + $dividend['corrected']) / ($subdata['beginPortefeuilleWaardeEuro'] /100));
      $aandeel=$subdata['actuelePortefeuilleWaardeEuro']/$totaalWaarde;
      $procentResultaatBijdrage=$procentResultaat*$aandeel;

      if($subdata['beginPortefeuilleWaardeEuro'] < 0)
        $procentResultaat = -1 * $procentResultaat;


      if($procentResultaat > 1000 || $procentResultaat < -1000)
      {
        $procentResultaattxt = "p.m.";
      }
      else
      {
        $procentResultaattxt = $this->formatGetal($procentResultaat, 1);
        $resulaten[$subdata['fonds']]=$procentResultaatBijdrage;
        $subdata['rendement']=$procentResultaat;
        $subdata['rendementBijdrage']=$procentResultaatBijdrage;
      }
      $fondsGegevens[$subdata['fonds']]=$subdata;

    }
    asort($resulaten);
    $i=0;
    $negatief=array();
    foreach($resulaten as $fonds=>$rendment)
    {
      $negatief[$fonds]=$fondsGegevens[$fonds];
      if($i==9)
        break;
      $i++;
    }
    $resulaten=array_reverse($resulaten,true);
    $i=0;
    $positief=array();
    foreach($resulaten as $fonds=>$rendment)
    {
      $positief[$fonds]=$fondsGegevens[$fonds];
      if($i==9)
        break;
      $i++;
    }

    $this->pdf->ln(10);
    $xPos = $this->pdf->getX();
    $yPos = $this->pdf->getY();
    $xStartPos = $xPos;

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    $this->toonTabel($positief,"Positieve bijdrage\n(uitgedrukt in bps)", $xStartPos);

    $this->pdf->setY($yPos);
    $xStartPos +=140;
    $this->toonTabel($negatief,"Negatieve bijdrage\n(uitgedrukt in bps)", $xStartPos);



    $this->toonTopTien();

    $this->pdf->fillCell=array();


    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0,0,0);
  }
}
?>
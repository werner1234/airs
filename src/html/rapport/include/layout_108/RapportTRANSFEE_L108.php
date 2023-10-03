<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.3 $

$Log: RapportOIB_L51.php,v $
Revision 1.3  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.2  2014/07/06 12:38:11  rvv
*** empty log message ***

Revision 1.1  2013/09/18 15:23:07  rvv
*** empty log message ***

Revision 1.17  2013/08/24 15:48:47  rvv
*** empty log message ***

Revision 1.16  2013/08/18 12:23:35  rvv
*** empty log message ***

Revision 1.15  2013/08/10 15:48:01  rvv
*** empty log message ***

Revision 1.14  2013/07/28 09:59:15  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTRANSFEE_L108
{
  
  function RapportTRANSFEE_L108($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "TRANSFEE";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    
    $this->pdf->rapport_titel = "Mutatie overzicht";//.date(' m-Y',$this->pdf->rapport_datum);
    
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
    $this->jaarGeleden=date("Y-m-d",mktime(0,0,0,date('m',$this->pdf->rapport_datum),date('d',$this->pdf->rapport_datum),date('Y',$this->pdf->rapport_datum)-1));
    
  }
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }
  
  function formatGetalKoers($waarde, $dec , $start = false)
  {
    if ($start == false)
      $waarde = $waarde / $this->pdf->ValutaKoersEind;
    else
      $waarde = $waarde / $this->pdf->ValutaKoersStart;
    
    return number_format($waarde,$dec,",",".");
  }
  
  
  
  function writeRapport()
  {
    global $__appvar;
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    
    $DB = new DB();
  
    $transactietypenOmschrijving= array('A'=>'Aankoop',
                                        'A/O'=>'Aankoop / openen',
                                        'A/S'=>'Aankoop / sluiten',
                                        'D'=>'Deponering',
                                        'L'=>'Lichting',
                                        'V'=>'Verkoop',
                                        'V/O'=>'Verkoop / openen',
                                        'V/S'=>'Verkoop / sluiten');
  

      $query = "SELECT TijdelijkeRapportage.FondsOmschrijving as Omschrijving, ".
        " TijdelijkeRapportage.fonds, ".
        " TijdelijkeRapportage.valuta, ".
        " TijdelijkeRapportage.totaalAantal, ".
        " TijdelijkeRapportage.actuelePortefeuilleWaardeEuro".
        " FROM (TijdelijkeRapportage)".
        " WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
        " TijdelijkeRapportage.type = 'fondsen' AND ".
        " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
        .$__appvar['TijdelijkeRapportageMaakUniek'];
       
    $DB->SQL($query);
    $DB->Query();
    while($fondsData = $DB->NextRecord())
    {
      $fondsDetails[$fondsData['fonds']]=$fondsData;
    }
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->setWidths(array(20,160,20,100));
    $this->pdf->SetAligns(array('L','L','R','L'));
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam'],'Email:',$this->pdf->portefeuilledata['email']));
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Woonplaats']));
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Land']));
    $this->pdf->ln();
    $this->pdf->SetAligns(array('L','L'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->setWidths(array(40,120,30,100));
    $this->pdf->row(array('Investeerders ID',$this->pdf->portefeuilledata['Portefeuille']));
    $this->pdf->row(array('Investeerders naam',$this->pdf->portefeuilledata['Naam']));
    $this->pdf->ln();
    $this->pdf->SetLineWidth($this->pdf->lineWidth);
    $this->pdf->underlinePercentage=0.8;
    
    //$header=array('ultimo','Begin aantal','Begin Koers','Waarde','Stortingen / Onttrekkingen EUR','Waarde Part. ultimo','Aantal Stortingen / Onttrekkingen','Aantal ultimo','Waarde ultimo','Beleggingsresultaat','Rendement Mnd','Rendement Cum');
    $header=array('Datum','Datum mutatie waarde fonds','Omschrijving');
//    $widths=array(20,22,22,22,25,25,25,25,25,25,22,22);
		$widths=array(60,60,120);
    $aligns=array('L','R','L');
    //$headerBorders=array('U','U','U','U','U','U','U','U','U','U','U','U');
    $headerBorders=array(array('L','T','U'),array('T','U'),array('T','U','R'));
    $kopBorders=array(array('L','T','U'),array('T','U','R'));
    
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    
    //$this->pdf->setY(25);
    $this->pdf->setWidths($widths);
    $this->pdf->SetAligns($aligns);
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
  
  
    $query = "SELECT Fondsen.Omschrijving, ".
      "Fondsen.Fondseenheid, ".
      "Fondsen.Valuta as fondsValuta, ".
      "Rekeningmutaties.Boekdatum, ".
      "Rekeningmutaties.id,
		Rekeningmutaties.Transactietype,
		Fondsen.ISINCode,
		Rekeningmutaties.Valuta,
		 Rekeningmutaties.Afschriftnummer,
		 Rekeningmutaties.Rekening,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, Rekeningmutaties.Fonds,  ".
      "Rekeningmutaties.Fondskoers, ".
      "Rekeningmutaties.Bedrag as Bedrag, ".
      "Rekeningmutaties.Debet as Debet, ".
      "Rekeningmutaties.Credit as Credit, ".
      "Rekeningmutaties.Valutakoers,
		 1 as Rapportagekoers ".
      "FROM Rekeningmutaties
       JOIN Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
       JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
       JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
       JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening ".
      "WHERE ".
      "Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
      "Rekeningmutaties.Verwerkt = '1' AND ".
      "Rekeningmutaties.Transactietype <> 'B' AND ".
      "Grootboekrekeningen.FondsAanVerkoop = '1' AND ".
      "Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
      "Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
      "ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id";
  
    $DB->SQL($query);
    $DB->Query();
    $buffer=array();
    while($mutaties = $DB->nextRecord())
    {
      $buffer[]=$mutaties;
    }

    
  //  listarray($buffer);
    $n=0;
    foreach($buffer as $fondsData)
    {
        if(substr($fondsData['Transactietype'],0,1)=='A')
        {
          $grootboek = 'STORT';
          $geldMelding='ontvangen';
          $omschrijving='Wij bevestigen hierbij aan u dat de hieronder vermelde mutatie in '.$fondsData['Omschrijving'].' heeft plaatsgevonden. Het totale transactiebedrag zal worden geconverteerd in participaties tegen de fondskoers op de mutatiedatum. U participeert mee in het fonds per de eerste dag van de maand volgende op de ontvangst van het door u betaalde investeringsbedrag.';
        }
        else
        {
          $grootboek = 'ONTTR';
          $geldMelding='uitbetaald';
          $omschrijving='Wij bevestigen hierbij aan u dat de hieronder vermelde mutatie in '.$fondsData['Omschrijving'].' heeft plaatsgevonden. Het aantal door u teruggevraagde participaties is omgerekend met de fondskoers op de mutatiedatum. Het tevens hieronder vermelde totale transactiebedrag zal zo spoedig mogelijk aan u worden uitbetaald. Mocht u ervoor gekozen te hebben het totale transactiebedrag in een van onze andere fondsen te investeren, ontvangt u hiervan bericht.';
        }
        $query="SELECT Boekdatum FROM Rekeningmutaties WHERE Rekening='".$fondsData['Rekening']."' AND Grootboekrekening='".$grootboek."' AND abs(Bedrag)='".abs($fondsData['Bedrag'])."'
        AND Boekdatum>=date('".$fondsData['Boekdatum']."') - interval 30 day AND Boekdatum<=date('".$fondsData['Boekdatum']."') + interval 30 day";
        $DB->SQL($query);
        $geldBoeking=$DB->lookupRecord();
        if($geldBoeking['Boekdatum']=='')
        {
          if(db2jul($fondsData['Boekdatum'])<db2jul('2021-01-02'))
          {
            $geldBoeking['Boekdatum']='2021-01-01';
          }
          else
          {
            $geldBoeking['Boekdatum']='';
          }
        }

        if($n>0)
        {
          $this->pdf->addPage();
          $this->pdf->setY(40);
        }
        $this->pdf->setWidths(array(200,40));
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $this->pdf->CellBorders=$kopBorders;
        $this->pdf->Row(array('Fonds en participatieklasse','Valuta'));
        unset($this->pdf->CellBorders);
        $this->pdf->Row(array($fondsData['Omschrijving'].', ISIN:'.$fondsData['ISINCode'],$fondsData['fondsValuta']));
        $this->pdf->ln();
        $this->pdf->setWidths($widths);
  
        $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
        $this->pdf->CellBorders=$headerBorders;
        $this->pdf->row($header);
        unset($this->pdf->CellBorders);
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $this->pdf->Row(array(date('d-M-Y',db2jul($fondsData['Boekdatum'])),date('d-M-Y',db2jul($fondsData['Boekdatum'])),$transactietypenOmschrijving[$fondsData['Transactietype']]));
        $this->pdf->ln();
        $this->pdf->setWidths(array(200,40));
        $this->pdf->Row(array($omschrijving));//'Wij bevestigen dat de hieronder volgende mutatie in '.$fondsData['Omschrijving'].' heeft plaatsgevonden. Het door u betaalde investeringsbedrag zal worden geconverteerd in participaties van '.$fondsData['Omschrijving'].' tegen de koerswaarde per datum mutatiewaarde. U participeert in het fonds per de 1e van de dag van de maand volgende op de ontvangst van uw betaling.'));
        $this->pdf->ln();
        $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
        $this->pdf->Row(array('Trade Detail'));
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $this->pdf->Row(array('Aantal participaties:',$this->formatGetal($fondsData['Aantal'],4)));
        $this->pdf->Row(array('Fondskoers op datum mutatie:',$this->formatGetal($fondsData['Fondskoers'],4)));
        $this->pdf->Row(array('Totaal transactiebedrag:','€'.$this->formatGetal($fondsData['Bedrag'],2)));
        $this->pdf->Row(array('Bedrag '.$geldMelding.' op:',($geldBoeking['Boekdatum']==''?'':date('d-M-Y',db2jul($geldBoeking['Boekdatum'])))));
        $this->pdf->ln();
        $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
        $this->pdf->Row(array('Samenvatting:'));
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $this->pdf->Row(array('Totale waarde portefeuille','€'.$this->formatGetal($fondsDetails[$fondsData['Fonds']]['actuelePortefeuilleWaardeEuro'],2)));
        $this->pdf->Row(array('Waarde per datum:',date('d-M-Y',db2jul($this->rapportageDatum))));
        $this->pdf->ln();
        $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
        $this->pdf->Row(array('Aantal participaties:'));
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $this->pdf->Row(array('Aantal participaties voor mutatie:',$this->formatGetal($fondsDetails[$fondsData['Fonds']]['totaalAantal']-$fondsData['Aantal'],4)));
        $this->pdf->Row(array('Aantal participaties na mutatie:',$this->formatGetal($fondsDetails[$fondsData['Fonds']]['totaalAantal'],4)));
        $this->pdf->ln();
        $n++;
    }
  
  
    $this->pdf->setWidths(array(45,150));
    $this->pdf->SetAligns(array('L','J'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('','Mocht u vragen hebben naar aanleiding van dit bericht, verzoeken wij u contact op te nemen met Cedar Accountancy & Belastingadvies B.V. per e-mail naar: investors@cederadministratie.nl'));
    
    
  }
  
  
}
?>
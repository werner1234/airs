<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/08/31 13:13:05 $
File Versie					: $Revision: 1.19 $

$Log: RapportHSE_L83.php,v $
Revision 1.19  2019/08/31 13:13:05  rvv
*** empty log message ***

Revision 1.18  2019/07/13 17:50:20  rvv
*** empty log message ***

Revision 1.17  2019/07/10 13:16:20  rvv
*** empty log message ***

Revision 1.16  2019/07/10 07:02:25  rvv
*** empty log message ***

Revision 1.15  2019/06/08 16:05:23  rvv
*** empty log message ***

Revision 1.14  2019/05/25 16:22:55  rvv
*** empty log message ***

Revision 1.13  2019/05/18 16:28:56  rvv
*** empty log message ***

Revision 1.12  2019/05/15 15:31:34  rvv
*** empty log message ***

Revision 1.11  2019/05/11 16:49:13  rvv
*** empty log message ***

Revision 1.10  2019/05/04 18:23:53  rvv
*** empty log message ***

Revision 1.9  2019/04/24 14:42:25  rvv
*** empty log message ***

Revision 1.8  2019/04/20 16:59:05  rvv
*** empty log message ***

Revision 1.7  2019/04/17 10:00:59  rvv
*** empty log message ***

Revision 1.6  2019/04/17 09:11:59  rvv
*** empty log message ***

Revision 1.5  2019/04/16 11:32:08  rvv
*** empty log message ***

Revision 1.4  2019/04/06 17:13:45  rvv
*** empty log message ***

Revision 1.3  2019/03/31 12:17:58  rvv
*** empty log message ***

Revision 1.2  2019/03/10 14:09:58  rvv
*** empty log message ***

Revision 1.2  2016/12/03 19:22:25  rvv
*** empty log message ***

*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportHSE_L83
{
  function RapportHSE_L83($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "HSE";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->pdf->rapport_titel = vertaalTekst("Samenstelling effectenportefeuille in",$this->pdf->rapport_taal).' '. vertaalTekst($this->pdf->valutaOmschrijvingen[$this->pdf->portefeuilledata['RapportageValuta']],$this->pdf->rapport_taal);
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
    $this->bedrag_decimaal = 0;
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
  
  
  function getDividend($fonds,$vanafStart=false)
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
    
    if($vanafStart==true)
    {
      $beginFilter='';
    }
    else
    {
      $beginFilter="Rekeningmutaties.Boekdatum >= '".	$this->rapportageDatumVanaf."' AND";
    }
    
    $query="SELECT Boekdatum,(Debet*Valutakoers) as Debet,(Credit*valutakoers) as Credit,Bedrag,Rekeningmutaties.Omschrijving
     FROM Rekeningmutaties
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
     JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND
     $beginFilter
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
  
  
  
  function writeRapport()
  {
    global $__appvar;
    
    $query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $this->portefeuilledata = $DB->nextRecord();
  
  
    $this->pdf->SetWidths(array(88,5,20,18,19,19,25,18,20,25,22));
    $this->pdf->SetAligns(array('L','L','R','L','R','R','R','R','R','R','R'));
    
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    if(checkVLC_Fondsomschrijving()==true)
    {
      $join="LEFT JOIN FondsExtraInformatie ON TijdelijkeRapportage.fonds=FondsExtraInformatie.Fonds";
      $selectFondsOmschrijving="if(isnull(FondsExtraInformatie.VLC_Fondsomschrijving),TijdelijkeRapportage.fondsOmschrijving,FondsExtraInformatie.VLC_Fondsomschrijving) as fondsOmschrijving,";
    }
    else
    {
      $join='';
      $selectFondsOmschrijving='TijdelijkeRapportage.fondsOmschrijving,';
    }
    
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
    
    
    $subquery = "SELECT  $selectFondsOmschrijving".
      " TijdelijkeRapportage.fonds,
        TijdelijkeRapportage.beleggingscategorie,
          TijdelijkeRapportage.beleggingscategorieOmschrijving,
         TijdelijkeRapportage.regio,
         TijdelijkeRapportage.rekening,
         Rekeningen.Tenaamstelling,
         TijdelijkeRapportage.type,
         TijdelijkeRapportage.regioOmschrijving,".
      " TijdelijkeRapportage.actueleValuta, ".
      " sum(IF(TijdelijkeRapportage.type = 'rekening' ,actuelePortefeuilleWaardeInValuta,
      IF(TijdelijkeRapportage.type = 'fondsen' ,totaalAantal, 0))) as totaalAantal,
       SUM( IF(TijdelijkeRapportage.type = 'fondsen' ,actuelePortefeuilleWaardeInValuta, 0)) as actuelePortefeuilleWaardeInValuta,
      SUM( IF(TijdelijkeRapportage.type = 'fondsen' ,beginPortefeuilleWaardeInValuta, 0)) as beginPortefeuilleWaardeInValuta, ".
      " TijdelijkeRapportage.beginwaardeLopendeJaar , ".
      " TijdelijkeRapportage.Valuta, ".
      "  SUM(IF(TijdelijkeRapportage.type = 'fondsen',(beginPortefeuilleWaardeEuro),0 )) AS beginPortefeuilleWaardeEuro, ".
      " TijdelijkeRapportage.actueleFonds,
				 SUM(IF(TijdelijkeRapportage.type IN('fondsen','rekening') , (actuelePortefeuilleWaardeEuro),0)) / ".$this->pdf->ValutaKoersEind." AS actuelePortefeuilleWaardeEuro ,
				 SUM(IF(TijdelijkeRapportage.type = 'rente' , (actuelePortefeuilleWaardeEuro),0)) / ".$this->pdf->ValutaKoersEind." AS rente,
				 TijdelijkeRapportage.historischeWaarde,
				 (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal,
        (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta,
				 TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
      " FROM TijdelijkeRapportage
      LEFT JOIN Rekeningen on TijdelijkeRapportage.Rekening=Rekeningen.Rekening AND Rekeningen.portefeuille = '".$this->portefeuille."'
      $join
       WHERE ".
      " TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
      " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " GROUP BY TijdelijkeRapportage.fonds, TijdelijkeRapportage.rekening
      ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde,TijdelijkeRapportage.beleggingscategorie,
      TijdelijkeRapportage.regioVolgorde,TijdelijkeRapportage.regio,
      TijdelijkeRapportage.fondsOmschrijving asc";//exit;

    // print detail (select from tijdelijkeRapportage)
    debugSpecial($subquery,__FILE__,__LINE__);
    $DB2 = new DB();
    $DB2->SQL($subquery);
    $DB2->Query();

    $kopPrinted=false;
    $lastCategorie='';
    $subtotaal=array();
    $totalen=array();
    while($subdata = $DB2->NextRecord())
    {
      $query="SELECT (fondskosten.percentage + fondskosten.transCostFund + fondskosten.perfFeeFund) as kostenPercentage FROM fondskosten WHERE fondskosten.fonds='".$subdata['fonds']."' AND datum <='".$this->rapportageDatum."' ORDER BY datum desc limit 1";
      $DB->SQL($query);
      $DB->Query();
      $kosten = $DB->nextRecord();
      $kostenPercentage = $kosten['kostenPercentage'];


      $subdata['actuelePortefeuilleWaardeEuro'] += $subdata['rente'] ;
      $dividend=$this->getDividend($subdata['fonds']);
      $dividendVHO=$this->getDividend($subdata['fonds'],true);
      
      $fondsResultaat = ($subdata['actuelePortefeuilleWaardeInValuta'] - $subdata['beginPortefeuilleWaardeInValuta']) * $subdata['actueleValuta'] / $this->pdf->ValutaKoersEind;
      $fondsResultaatprocent = ($fondsResultaat / $subdata['beginPortefeuilleWaardeEuro']) * 100;
      $valutaResultaat = $subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] - $fondsResultaat;
      
      $procentResultaat = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] + $dividend['corrected']) / ($subdata['beginPortefeuilleWaardeEuro'] /100));
      
      if($subdata['beginPortefeuilleWaardeEuro'] < 0)
        $procentResultaat = -1 * $procentResultaat;
      
      $procentResultaatVHO = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['historischeWaardeTotaalValuta'] + $dividendVHO['corrected']) / ($subdata['historischeWaardeTotaalValuta'] /100));
      if($subdata['historischeWaardeTotaalValuta'] < 0)
        $procentResultaatVHO = -1 * $procentResultaatVHO;
      
      $percentageVanTotaal = ($subdata['actuelePortefeuilleWaardeEuro'] / $totaalWaarde) * 100;
      
      
      $percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,1);
      
      if($procentResultaat > 1000 || $procentResultaat < -1000)
        $procentResultaattxt = "p.m.";
      else
        $procentResultaattxt = $this->formatGetal($procentResultaat,2);
      
      $fondsResultaattxt = "";

      if($fondsResultaat <> 0)
        $fondsResultaattxt = $this->formatGetal($fondsResultaat,$this->bedrag_decimaal);

      
      if($kostenPercentage<>0)
        $kostenPercentagetxt = $this->formatGetal($kostenPercentage,2);
      else
        $kostenPercentagetxt = '';
      
      // print fondsomschrijving appart ivm met apparte fontkleur
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      if($subdata['beleggingscategorie'] <> $lastCategorie)
      {
        if(isset($subtotaal['actuelePortefeuilleWaardeEuro']))
        {
          $this->pdf->row(array('', '', '', '', '', '', $this->formatGetal($subtotaal['actuelePortefeuilleWaardeEuro'], $this->bedrag_decimaal)
                          , $this->formatGetal($subtotaal['percentageVanTotaal'], 1),''
                          , $this->formatGetal($subtotaal['fondsResultaat'], $this->bedrag_decimaal)));

          foreach($subtotaal as $veld=>$waarde)
            $totalen[$veld]+=$waarde;
          $subtotaal=array();
        }
        if($this->pdf->getY()+12>$this->pdf->PageBreakTrigger)
        {
          $this->pdf->addPage();
        }
        $this->pdf->row(array(vertaalTekst($subdata['beleggingscategorieOmschrijving'],$this->pdf->rapport_taal)));
        $lastCategorie=$subdata['beleggingscategorie'];
      }
      
      if($subdata['regio'] <> $lastRegio)
      {
        if($subdata['regioOmschrijving']<>'')
          $this->pdf->row(array("   ".vertaalTekst($subdata['regioOmschrijving'],$this->pdf->rapport_taal)));
      }
      $lastRegio=$subdata['regio'];
  
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  
      $omschrijving='   '.$subdata['fondsOmschrijving'];
  
      $omschrijvingWidth = $this->pdf->GetStringWidth($subdata['fondsOmschrijving']);
      $cellWidth = 90 - 2;
      if ($omschrijvingWidth > $cellWidth)
      {
        $dotWidth = $this->pdf->GetStringWidth('...');
        $chars = strlen($subdata['fondsOmschrijving']);
        $newOmschrijving = $subdata['fondsOmschrijving'];
        for ($i = 3; $i < $chars; $i++)
        {
          $omschrijvingWidth = $this->pdf->GetStringWidth(substr($newOmschrijving, 0, $chars - $i));
          if ($cellWidth > ($omschrijvingWidth + $dotWidth))
          {
            $omschrijving = substr($newOmschrijving, 0, $chars - $i) . '...';
            break;
          }
        }
      }
  
      $historischeWaarde=$this->formatGetal($subdata['historischeWaarde'],2);
      $actueleFonds=$this->formatGetal($subdata['actueleFonds'],2);
      $actuelePortefeuilleWaardeEuro=$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->bedrag_decimaal);
      
      if($subdata['type']=='rekening')
      {
        $kostenPercentagetxt='';
        $fondsResultaattxt='';
        $procentResultaattxt='';
        $historischeWaarde='';
        $actueleFonds='';
        if($_POST['anoniem']<>1)
        {
           if($subdata['Tenaamstelling']=='')
           {
             $omschrijving = $omschrijving . " " . preg_replace('/[A-Z]{3}/m', '', $subdata['rekening']);
           }
        }
      }
      else
      {
        $subtotaal['fondsResultaat'] +=$fondsResultaat;
      }
      
      $this->pdf->row(array($omschrijving,
                        '',//$subdata['regioOmschrijving'],
                        $this->formatAantal($subdata['totaalAantal'],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
                        $subdata['Valuta'],
                        $historischeWaarde,
                        $actueleFonds,
                        $actuelePortefeuilleWaardeEuro,
                        $percentageVanTotaaltxt,
                        $kostenPercentagetxt,
                        $fondsResultaattxt,
                        $procentResultaattxt ));
      
      

      $subtotaal['percentageVanTotaal'] +=$percentageVanTotaal;
      $subtotaal['procentResultaat'] += $procentResultaat;
     
      $subtotaal['procentResultaatVHO'] += $procentResultaatVHO;
      $subtotaal['actuelePortefeuilleWaardeEuro'] += $subdata['actuelePortefeuilleWaardeEuro'];
      
      $subtotaal['totaalDividend'] += $dividend['totaal'];
      $subtotaal['totaalDividendCorrected'] += $dividend['corrected'];
      $subtotaal['totaalDividendCorrectedVHO'] += $dividendVHO['corrected'];
      
    }
  
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    if(isset($subtotaal['actuelePortefeuilleWaardeEuro']))
    {
      $this->pdf->row(array('', '', '', '', '', '', $this->formatGetal($subtotaal['actuelePortefeuilleWaardeEuro'], $this->bedrag_decimaal)
                      , $this->formatGetal($subtotaal['percentageVanTotaal'], 1),''
                      , $this->formatGetal($subtotaal['fondsResultaat'], $this->bedrag_decimaal)));
      $this->pdf->ln();
      foreach($subtotaal as $veld=>$waarde)
        $totalen[$veld]+=$waarde;
      $subtotaal=array();
    }
  
    if(isset($totalen['actuelePortefeuilleWaardeEuro']))
    {
      $this->pdf->row(array(vertaalTekst('Totaal',$this->pdf->rapport_taal), '', '', '', '', '', $this->formatGetal($totalen['actuelePortefeuilleWaardeEuro'], $this->bedrag_decimaal)
                      , $this->formatGetal($totalen['percentageVanTotaal'], 0),''
                      , $this->formatGetal($totalen['fondsResultaat'], $this->bedrag_decimaal)));
    }
  
    if($this->pdf->rapport_VOLK_valutaoverzicht == 1)
    {
      $this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
    }
    
    
  }
}
?>

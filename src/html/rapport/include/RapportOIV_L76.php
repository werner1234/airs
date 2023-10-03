<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");


class RapportOIV_L76
{
	function RapportOIV_L76($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIV";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "";

		if ($this->pdf->rapportageValuta != 'EUR' && $this->pdf->rapportageValuta != '')
		  $this->pdf->rapport_titel .= " in ".$this->pdf->rapportageValuta;

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}
  
  
  function writeRapport()
  {
    global $__appvar;
    $DB = new DB();
    
    $this->pdf->AddPage();
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize-2);
    $this->pdf->setAligns(array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
    $this->pdf->setWidths(array(5,45,15,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12));
    
    $mutaties['periode']=$this->genereerMutatieLijst($this->rapportageDatumVanaf,$this->rapportageDatum);
    
    // haal totaalwaarde op om % te berekenen
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) /".$this->pdf->ValutaKoersEind."  AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];
  
    $query = "SELECT actuelePortefeuilleWaardeEuro,rekening,Fondsomschrijving,rapportageDatum ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum IN('".$this->rapportageDatum."','".$this->rapportageDatumVanaf."') AND type='rekening' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    $DB->SQL($query);
    $DB->Query();
    $rekeningen=array();
    while($data = $DB->nextRecord())
    {
      $rekeningen[$data['rekening']][$data['rapportageDatum']]=$data;
    }
    
    $query="SELECT
Rekeningmutaties.Fonds,
Sum(Rekeningmutaties.Debet*Rekeningmutaties.Valutakoers) AS Debet,
Sum(Rekeningmutaties.Credit*Rekeningmutaties.Valutakoers) AS Credit,
SUM(Rekeningmutaties.Debet) AS DebetValuta,
SUM(Rekeningmutaties.Credit) AS CreditValuta,
Sum(Aantal) as Aantal,
Rekeningmutaties.Grootboekrekening,
if(SUBSTR(Rekeningmutaties.Transactietype,1,1)='A','aankoop',
if(SUBSTR(Rekeningmutaties.Transactietype,1,1)='V','verkoop',
if(SUBSTR(Rekeningmutaties.Transactietype,1,1)='D','aankoop',
if(SUBSTR(Rekeningmutaties.Transactietype,1,1)='L','verkoop','')))) as Transactietype,
Fondsen.Fondseenheid,
Fondsen.Valuta
FROM
Rekeningmutaties 
JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening AND Rekeningen.Portefeuille = '".$this->portefeuille."'
LEFT JOIN Fondsen on Rekeningmutaties.Fonds=Fondsen.Fonds
WHERE
Rekeningmutaties.Verwerkt = '1' AND
Rekeningmutaties.Transactietype <> 'B' AND 
Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND
Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND
Rekeningmutaties.Fonds <> ''
GROUP BY Rekeningmutaties.Grootboekrekening, Rekeningmutaties.Fonds,Transactietype
ORDER BY Rekeningmutaties.Grootboekrekening, Rekeningmutaties.Fonds,Transactietype";
    $DB->SQL($query);
    $DB->Query();
    while($data=$DB->nextRecord())
    {
      if($data['Transactietype']=='')
        $data['Transactietype']='geen';
      $totaal=$data['Credit']-$data['Debet'];
      
      $data['Koers']=($data['DebetValuta']-$data['CreditValuta'])/($data['Aantal'])/$data['Fondseenheid'];
      
      $fondsTransacties[$data['Fonds']][$data['Grootboekrekening']][$data['Transactietype']]=$data;
      $fondsTransacties[$data['Fonds']][$data['Grootboekrekening']]['totaalCreditWaarde']+=$data['Credit'];
      $fondsTransacties[$data['Fonds']][$data['Grootboekrekening']]['totaalDebetWaarde']+=$data['Debet'];
      $fondsTransacties[$data['Fonds']][$data['Grootboekrekening']]['totaalWaarde']+=$totaal;
      $fondsTransacties[$data['Fonds']][$data['Grootboekrekening']]['totaalAantal']+=$data['Aantal'];
    }
    
    
    
    $query="SELECT
Rekeningmutaties.Fonds,
(Rekeningmutaties.Debet*Rekeningmutaties.Valutakoers) AS Debet,
(Rekeningmutaties.Credit*Rekeningmutaties.Valutakoers) AS Credit,
(Aantal) as Aantal,
Rekeningmutaties.Grootboekrekening,
if(SUBSTR(Rekeningmutaties.Transactietype,1,1)='A','aankoop',
if(SUBSTR(Rekeningmutaties.Transactietype,1,1)='V','verkoop',
if(SUBSTR(Rekeningmutaties.Transactietype,1,1)='D','aankoop',
if(SUBSTR(Rekeningmutaties.Transactietype,1,1)='L','verkoop','')))) as Transactietype,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Omschrijving
FROM
Rekeningmutaties 
JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening AND Rekeningen.Portefeuille = '".$this->portefeuille."'
WHERE
Rekeningmutaties.Verwerkt = '1' AND
Rekeningmutaties.Transactietype <> 'B' AND 
Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND
Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND
Rekeningmutaties.Fonds <> '' AND Rekeningmutaties.Grootboekrekening IN('KOST','KOBU','FONDS','RENOB')
ORDER BY Rekeningmutaties.Grootboekrekening, Rekeningmutaties.Fonds,Transactietype";
    $DB->SQL($query);
    $DB->Query();
    $TransactietypeLookup=array();
    while($data=$DB->nextRecord())
    {
      if($data['Transactietype'] <> '')
        $TransactietypeLookup[$data['Fonds']][$data['Boekdatum']][$data['Omschrijving']]=$data;
      else
      {
        if($data['Grootboekrekening']=='KOST'||$data['Grootboekrekening']=='KOBU')
        {
          if(isset($TransactietypeLookup[$data['Fonds']][$data['Boekdatum']][$data['Omschrijving']]))
          {
            $transactietype=$TransactietypeLookup[$data['Fonds']][$data['Boekdatum']][$data['Omschrijving']]['Transactietype'];
            $fondsTransacties[$data['Fonds']]['FONDS'][$transactietype]['kosten']+=($data['Credit']-$data['Debet']);
          }
        }
        elseif($data['Grootboekrekening']=='RENOB')
        {
          if(isset($TransactietypeLookup[$data['Fonds']][$data['Boekdatum']][$data['Omschrijving']]))
          {
            $transactietype=$TransactietypeLookup[$data['Fonds']][$data['Boekdatum']][$data['Omschrijving']]['Transactietype'];
            if(strtolower(substr($data['Omschrijving'],0,7))=='verkoop')
              $fondsTransacties[$data['Fonds']]['FONDS'][$transactietype]['RENOBverkoop']+=($data['Credit']-$data['Debet']);
            else
              $fondsTransacties[$data['Fonds']]['FONDS'][$transactietype]['RENOB']+=($data['Credit']-$data['Debet']);
          }
          else
          {
            $fondsTransacties[$data['Fonds']]['RENOB']['zonderFonds'] += ($data['Credit'] - $data['Debet']);
          }
        }
        //  elseif(          if($data['Grootboekrekening']))
      }
      
    }
    
    $query = "SELECT Fonds,rapportageDatum,actueleValuta,Beleggingscategorie,	actuelePortefeuilleWaardeEuro,actuelePortefeuilleWaardeInValuta,beginPortefeuilleWaardeInValuta,beginPortefeuilleWaardeEuro,type,totaalAantal FROM TijdelijkeRapportage WHERE rapportageDatum IN('".$this->rapportageDatumVanaf."','".$this->rapportageDatum."') AND  portefeuille = '".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek']." ORDER BY Fonds,rapportageDatum,type";
    $DB->SQL($query);
    $DB->Query();
    while($data=$DB->nextRecord())
    {
      if($data['Fonds']<>'')
      {
        if($data['Beleggingscategorie'] <> '')
          $fondsen[$data['Fonds']]['Beleggingscategorie']=$data['Beleggingscategorie'];
        
        if($data['type']=='rente')
        {
          $fondsen[$data['Fonds']][$data['rapportageDatum']]['renteActuelePortefeuilleWaardeEuro'] = $data['actuelePortefeuilleWaardeEuro'];
        }
        else
        {
          $fondsen[$data['Fonds']][$data['rapportageDatum']]['actuelePortefeuilleWaardeEuro']=$data['actuelePortefeuilleWaardeEuro'];
          $fondsen[$data['Fonds']][$data['rapportageDatum']]['actuelePortefeuilleWaardeInValuta']=$data['actuelePortefeuilleWaardeInValuta'];
          $fondsen[$data['Fonds']][$data['rapportageDatum']]['beginPortefeuilleWaardeEuro']=$data['beginPortefeuilleWaardeEuro'];
          $fondsen[$data['Fonds']][$data['rapportageDatum']]['beginPortefeuilleWaardeInValuta']=$data['beginPortefeuilleWaardeInValuta'];

          $fondsen[$data['Fonds']][$data['rapportageDatum']]['totaalAantal']=$data['totaalAantal'];
        }
      }
    }
    
    foreach($mutaties['periode'] as $fonds=>$mutatieData)
    {
      if(!isset($fondsen[$fonds][$this->rapportageDatumVanaf]))
        $fondsen[$fonds][$this->rapportageDatumVanaf]=array('Beleggingscategorie'=>$mutatieData['6'],'actuelePortefeuilleWaardeEuro'=>0,'beginPortefeuilleWaardeInValuta'=>0);
      if(!isset($fondsen[$fonds][$this->rapportageDatum]))
        $fondsen[$fonds][$this->rapportageDatum]=array('Beleggingscategorie'=>$mutatieData['6'],'actuelePortefeuilleWaardeEuro'=>0,'beginPortefeuilleWaardeInValuta'=>0);
      
      $fondsen[$fonds]['trans']=$mutatieData;
    }
    
    foreach($fondsen as $fonds=>$fondsData)
    {
      $query="SELECT ISINCode,Rating,Renteperiode,EersteRentedatum,Rentedatum,Lossingsdatum,Omschrijving,Fondseenheid,Valuta FROM Fondsen WHERE Fonds='$fonds'";
      $DB->SQL($query);
      
      $extraFondsData=$DB->lookupRecord();
      foreach($extraFondsData as $key=>$value)
        $fondsen[$fonds][$key]=$value;
      
      $rente=getRenteParameters($fonds, $this->rapportageDatum);
      foreach($rente as $key=>$value)
        $fondsen[$fonds][$key]=$value;
      
      //$query="SELECT Rentepercentage FROM Rentepercentages WHERE Fonds='".mysql_real_escape_string($fonds)."' AND Datum < '".$this->rapportageDatum."' order by Datum desc";
      //$DB->SQL($query);
      //$rente=$DB->lookupRecord();
      
      $rente=getRentePercentage($fonds,$this->rapportageDatum);
      
      if($rente['Rentepercentage'])
        $fondsen[$fonds]['Rentepercentage']=$rente['Rentepercentage'];
      
      $perioden=array($this->rapportageDatumVanaf,$this->rapportageDatum);
      foreach($perioden as $datum)
      {
        $q = "SELECT Koers,Datum FROM Fondskoersen WHERE Fonds = '".mysql_real_escape_string($fonds)."' AND Datum <= '".$datum."' ORDER BY Datum DESC LIMIT 1";
        $DB->SQL($q);
        $koersen=$DB->lookupRecord();
        $fondsen[$fonds][$datum]['koers']=$koersen['Koers'];
        
        $q = "SELECT Koers,Datum FROM Valutakoersen WHERE Valuta = '".mysql_real_escape_string($fondsen[$fonds]['Valuta'])."' AND Datum <= '".$datum."' ORDER BY Datum DESC LIMIT 1";
        $DB->SQL($q);
        $koersen=$DB->lookupRecord();
        $fondsen[$fonds][$datum]['actueleValuta']=$koersen['Koers'];
      }
      
      
      if($fondsen[$fonds]['Renteperiode']>0)
      {
        $timer=0;
        $jaar=substr($fondsen[$fonds]['EersteRentedatum'],0,4);
        $start= db2jul($fondsen[$fonds]['EersteRentedatum']);
        $rentedatumJul=db2jul($fondsen[$fonds]['Rentedatum']);
        $renteDag=date('d',$rentedatumJul);
        $renteMaand=date('m',$rentedatumJul);
        $eind=db2jul($this->rapportageDatum);
        $timer=$start;
        $maanden=0;
        
        while($timer<=$eind)
        {
          $maanden+=$fondsen[$fonds]['Renteperiode'];
          $timer=mktime(0,0,0,$renteMaand+$maanden,$renteDag,$jaar);
        }
        $fondsen[$fonds]['VolgendeRentedatum']=date('d-m-Y',$timer);
      }
    }
  
    
    $this->pdf->excelData[] = array('','','','110','020','030','040','050+51','060','070','080','090','91','100','110','120','130','140','150','050','51');
    $this->pdf->Row(array('','','','110','020','030','040','050+51','060','070','080','090','91','100','110','120','130','140','150','050','51'));
    $this->pdf->ln();
    $this->pdf->excelData[] = array('Indelen','Fonds','Beleggingscategorie','Beginvermogen Q','Aankoopwaarde totaal','Verkoopwaarde totaal','Valutaresultaat','Koersresultaat',
      'Overige wijzigingen marktwaarde','Rectificaties','Eindvermogen Q','Ontvangen bruto-dividend Q','Saldo opgelopen rente bij start Q','Opgelopen rente gedurende Q','Meeverkochte rente gedurende Q',
      'Meegekochte rente gedurende Q','Ontvangen coupons (bruto) gedurende Q','Herwaardering en andere wijzigingen','Saldo opgelopen rente bij einde Q','Gerealiseerd koersresultaat','Ongerealiseerd koersresultaat');

    
    foreach($fondsen as $fonds=>$fondsData)
    {
      //listarray($fondsTransacties[$fonds]['FONDS']['verkoop']);
      $aankoopwaardeInValuta=$fondsTransacties[$fonds]['FONDS']['aankoop']['DebetValuta'];//$fondsTransacties[$fonds]['FONDS']['aankoop']['Aantal']*$fondsTransacties[$fonds]['FONDS']['aankoop']['Koers']*$fondsData['Fondseenheid'];
      $aankoopwaardeInEur=$fondsTransacties[$fonds]['FONDS']['aankoop']['Debet'];//$aankoopwaardeInValuta*$fondsData[$this->rapportageDatum]['actueleValuta'];
      $verkoopwaardeInValuta=$fondsTransacties[$fonds]['FONDS']['verkoop']['CreditValuta']*-1;//$fondsTransacties[$fonds]['FONDS']['verkoop']['Aantal']*$fondsTransacties[$fonds]['FONDS']['verkoop']['Koers']*$fondsData['Fondseenheid'];
      $verkoopwaardeInEur=$fondsTransacties[$fonds]['FONDS']['verkoop']['Credit']*-1;//$verkoopwaardeInValuta*$fondsData[$this->rapportageDatum]['actueleValuta'];
      $mutatieWaardeInEur=$fondsData[$this->rapportageDatum]['actuelePortefeuilleWaardeEuro']-$fondsData[$this->rapportageDatumVanaf]['actuelePortefeuilleWaardeEuro'];

      $mutatieWaardeInValuta=($fondsData[$this->rapportageDatum]['actuelePortefeuilleWaardeEuro']/$fondsData[$this->rapportageDatum]['actueleValuta'])-($fondsData[$this->rapportageDatumVanaf]['actuelePortefeuilleWaardeEuro']/$fondsData[$this->rapportageDatumVanaf]['actueleValuta']);

      //$ongerealiseerdeWaardeInValuta=($fondsData[$this->rapportageDatum]['actuelePortefeuilleWaardeValuta']-$fondsData[$this->rapportageDatum]['beginPortefeuilleWaardeInValuta']);
     //$ongerealiseerdeWaardeInEUR=($fondsData[$this->rapportageDatum]['actuelePortefeuilleWaardeEuro']-$fondsData[$this->rapportageDatum]['beginPortefeuilleWaardeEuro']);
  
  
      $ongerealiseerdeWaardeInEUR = ($fondsData[$this->rapportageDatum]['actuelePortefeuilleWaardeInValuta'] - $fondsData[$this->rapportageDatum]['beginPortefeuilleWaardeInValuta']) * $fondsData[$this->rapportageDatum]['actueleValuta'] / $this->pdf->ValutaKoersEind;
      $valutaResultaat = $fondsData[$this->rapportageDatum]['actuelePortefeuilleWaardeEuro'] - $fondsData[$this->rapportageDatum]['beginPortefeuilleWaardeEuro'] - $ongerealiseerdeWaardeInEUR;
  
  
      $fondsen[$data['Fonds']][$data['rapportageDatum']]['actuelePortefeuilleWaardeEuro']=$data['actuelePortefeuilleWaardeEuro'];
      $fondsen[$data['Fonds']][$data['rapportageDatum']]['actuelePortefeuilleWaardeInValuta']=$data['actuelePortefeuilleWaardeInValuta'];
      $fondsen[$data['Fonds']][$data['rapportageDatum']]['beginPortefeuilleWaardeEuro']=$data['beginPortefeuilleWaardeEuro'];
      $fondsen[$data['Fonds']][$data['rapportageDatum']]['beginPortefeuilleWaardeInValuta']=$data['beginPortefeuilleWaardeInValuta'];

      
  
      if(strpos($fondsData['trans']['transacties'],'V') > 0)
      {
        $gerealiseerdResultaat=$fondsData['trans']['resultaat'];
      }
      else
      {
        $gerealiseerdResultaat=0;
      }
      $resultaatEur=$mutatieWaardeInEur-$aankoopwaardeInEur-$verkoopwaardeInEur;

      
    //  $valutaResultaat=($fondsData[$this->rapportageDatum]['actueleValuta']-$fondsData[$this->rapportageDatumVanaf]['actueleValuta'])*$ongerealiseerdeWaardeInValuta;//-$gerealiseerdResultaat;
      $renob=($fondsTransacties[$fonds]['RENOB']['geen']['Credit']-$fondsTransacties[$fonds]['RENOB']['geen']['Debet']);
      $renobVerkoop=$fondsTransacties[$fonds]['FONDS']['verkoop']['RENOBverkoop'];
      $renme=$fondsTransacties[$fonds]['RENME']['geen']['Credit']-$fondsTransacties[$fonds]['RENME']['geen']['Debet'];
      $row=array(' ',//A
        $fondsData['Omschrijving'],//B
        $fondsData['Beleggingscategorie'],//C
        $fondsData[$this->rapportageDatumVanaf]['actuelePortefeuilleWaardeEuro'],//D 10
        $aankoopwaardeInEur,//E 20
        $verkoopwaardeInEur,//F 30
        $valutaResultaat, //G 40 valutaresultaat
        $ongerealiseerdeWaardeInEUR+$gerealiseerdResultaat, //H
        '',// //I 60
        '', //J 70
        $fondsData[$this->rapportageDatum]['actuelePortefeuilleWaardeEuro'],//K 080
        $fondsTransacties[$fonds]['DIV']['geen']['Credit'], //L
        $fondsData[$this->rapportageDatumVanaf]['renteActuelePortefeuilleWaardeEuro'],//M 91
        ($fondsData[$this->rapportageDatumVanaf]['renteActuelePortefeuilleWaardeEuro']-$fondsData[$this->rapportageDatum]['renteActuelePortefeuilleWaardeEuro']+$renob-$renobVerkoop+$renme),//100 N
        $renobVerkoop,//O Verkochte rente //110
        $renme,//P 120
        $renob,//130 Q
        '',//140 R
        $fondsData[$this->rapportageDatum]['renteActuelePortefeuilleWaardeEuro'], //S
        $gerealiseerdResultaat,
        $ongerealiseerdeWaardeInEUR);
      
      
     // echo "$fonds ".($fondsData[$this->rapportageDatum]['renteActuelePortefeuilleWaardeEuro']-$fondsData[$this->rapportageDatumVanaf]['renteActuelePortefeuilleWaardeEuro']+$renob-$renobVerkoop+$renme)."=".$fondsData[$this->rapportageDatum]['renteActuelePortefeuilleWaardeEuro']."-".$fondsData[$this->rapportageDatumVanaf]['renteActuelePortefeuilleWaardeEuro']."+$renob-$renobVerkoop+$renme <br>\n";//100 N
      
      $this->pdf->excelData[] = $row;
      $pdfRow=array();
      foreach ($row as $veld)
      {
        if(isNumeric($veld))
          $veld=$this->formatGetal($veld,0);
        $pdfRow[]=$veld;
      }
      $this->pdf->Row($pdfRow);
    //  listarray($fondsData);
    //  listarray($resultaatEur);
    }
    //  listarray($fondsTransacties);
    // listarray($fondsen);
    // listarray($this->pdf->excelData);
//	exit;
  
    foreach($rekeningen as $rekening=>$rekeningData)
    {
      $row=array(' ',
        $rekening,//A $this->rapportageDatumVanaf."','".$this->rapportageDatum.
        'Liquiditeiten',//B
        $rekeningData[$this->rapportageDatumVanaf]['actuelePortefeuilleWaardeEuro'],//C 10
        '',//$aankoopwaardeInEur,//D 20
        '',//$verkoopwaardeInEur,//E 30
        '',//$valutaResultaat, //F 40 valutaresultaat
        '',//$gerealiseerdResultaat, //G 50 gerealiseerd resultaat
        '',//$resultaatOngerealiseerdInEur, //H 51 ongerealiseerd resultaat
        '', //J 70
        $rekeningData[$this->rapportageDatum]['actuelePortefeuilleWaardeEuro'],//K 080
        '',//$fondsTransacties[$fonds]['DIV']['geen']['Credit'],
        '',//$fondsData[$this->rapportageDatumVanaf]['renteActuelePortefeuilleWaardeEuro'],//M 91
        '',//$fondsData[$this->rapportageDatum]['renteActuelePortefeuilleWaardeEuro']-$fondsData[$this->rapportageDatumVanaf]['renteActuelePortefeuilleWaardeEuro'],//100
        '',//$fondsTransacties[$fonds]['FONDS']['verkoop']['RENOBverkoop'],//O Verkochte rente //110
        '',//$fondsTransacties[$fonds]['RENME']['geen']['Credit']-$fondsTransacties[$fonds]['RENME']['geen']['Debet'],//P 120
        '',//($fondsTransacties[$fonds]['RENOB']['geen']['Credit']-$fondsTransacties[$fonds]['RENOB']['geen']['Debet'])-$fondsTransacties[$fonds]['FONDS']['verkoop']['RENOBverkoop'],//130
        '',//140
        '',//$fondsData[$this->rapportageDatum]['renteActuelePortefeuilleWaardeEuro']//150
        );
      $this->pdf->excelData[] = $row;
      $pdfRow=array();
      foreach ($row as $veld)
      {
        if(isNumeric($veld))
          $veld=$this->formatGetal($veld,0);
        $pdfRow[]=$veld;
      }
      $this->pdf->Row($pdfRow);
    }
  }
  
  
  
  
  
  
  function genereerMutatieLijst($rapportageDatumVanaf,$rapportageDatum)
  {
    // loopje over Grootboekrekeningen Opbrengsten = 1
    if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    else
      $koersQuery = "";
    
    $query = "SELECT Fondsen.Omschrijving, ".
      "Fondsen.Fondseenheid, ".
      "Rekeningmutaties.Boekdatum, ".
      "Rekeningmutaties.Transactietype,
		Rekeningmutaties.Valuta,
		Rekeningmutaties.Fonds,
		Rekeningmutaties.id,
		 Rekeningmutaties.Afschriftnummer,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, Rekeningmutaties.Fonds,  ".
      "Rekeningmutaties.Fondskoers, ".
      "Rekeningmutaties.Debet as Debet, ".
      "Rekeningmutaties.Credit as Credit, ".
      "Rekeningmutaties.Valutakoers,
		 1 $koersQuery   as Rapportagekoers
		 ,BeleggingscategoriePerFonds.Beleggingscategorie ".
      "FROM (Rekeningmutaties, Fondsen, Rekeningen, Portefeuilles, Grootboekrekeningen)
		LEFT Join BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' ".
      "WHERE ".
      "Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
      "Rekeningmutaties.Fonds = Fondsen.Fonds AND ".
      "Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
      "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
      "Rekeningmutaties.Verwerkt = '1' AND ".
      "Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND ".
      "Rekeningmutaties.Transactietype <> 'B' AND ".
      "Grootboekrekeningen.FondsAanVerkoop = '1' AND ".
      "Rekeningmutaties.Boekdatum > '$rapportageDatumVanaf' AND ".
      "Rekeningmutaties.Boekdatum <= '$rapportageDatum' ".
      "ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    // haal koersresultaat op om % te berekenen
    
    
    $buffer = array();
    $sortBuffer = array();
    $totaal_aankoop_waarde=0;
    $totaal_verkoop_waarde=0;
    $totaal_resultaat_waarde=0;
    while($mutaties = $DB->nextRecord())
    {
      $buffer[] = $mutaties;
    }
    $data=array();
    foreach ($buffer as $mutaties)
    {
      $mutaties['Aantal'] = abs($mutaties['Aantal']);
      $aankoop_koers = "";
      $aankoop_waardeinValuta = "";
      $aankoop_waarde = "";
      $verkoop_koers = "";
      $verkoop_waardeinValuta = "";
      $verkoop_waarde = "";
      $historisch_kostprijs = "";
      $resultaat_voorgaande = "";
      $resultaat_lopendeProcent = "";
      $resultaatlopende = 0 ;
      
      
      switch($mutaties['Transactietype'])
      {
        case "A" :
          // Aankoop
          $t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
          $t_aankoop_waardeinValuta = abs($mutaties['Debet']);
          $t_aankoop_koers					= $mutaties['Fondskoers'];
          
          $totaal_aankoop_waarde += $t_aankoop_waarde;
          
          if($t_aankoop_waarde > 0)
            $aankoop_koers 					= $t_aankoop_koers;
          if($t_aankoop_waardeinValuta > 0)
            $aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
          if($t_aankoop_koers > 0)
            $aankoop_waarde 				= $t_aankoop_waarde;
          break;
        case "A/O" :
          // Aankoop / openen
          $t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
          $t_aankoop_waardeinValuta = abs($mutaties['Debet']);
          $t_aankoop_koers					= $mutaties['Fondskoers'];
          
          $totaal_aankoop_waarde += $t_aankoop_waarde;
          
          if($t_aankoop_waarde > 0)
            $aankoop_koers 					= $t_aankoop_koers;
          if($t_aankoop_waardeinValuta > 0)
            $aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
          if($t_aankoop_koers > 0)
            $aankoop_waarde 				= $t_aankoop_waarde;
          break;
        case "A/S" :
          // Aankoop / sluiten
          $t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
          $t_aankoop_waardeinValuta = abs($mutaties['Debet']);
          $t_aankoop_koers					= $mutaties['Fondskoers'];
          
          $totaal_aankoop_waarde += $t_aankoop_waarde;
          
          if($t_aankoop_waarde > 0)
            $aankoop_koers 					= $t_aankoop_koers;
          if($t_aankoop_waardeinValuta > 0)
            $aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
          if($t_aankoop_koers > 0)
            $aankoop_waarde 				= $t_aankoop_waarde;
          
          break;
        case "B" :
          // Beginstorting
          break;
        case "D" :
        case "S" :
          // Deponering
          $t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
          $t_aankoop_waardeinValuta = abs($mutaties['Debet']);
          $t_aankoop_koers					= $mutaties['Fondskoers'];
          
          $totaal_aankoop_waarde += $t_aankoop_waarde;
          
          if($t_aankoop_waarde > 0)
            $aankoop_koers 					= $t_aankoop_koers;
          if($t_aankoop_waardeinValuta > 0)
            $aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
          if($t_aankoop_waarde > 0)
            $aankoop_waarde 				= $t_aankoop_waarde;
          break;
        case "L" :
          // Lichting
          $t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
          $t_verkoop_waardeinValuta = abs($mutaties['Credit']);
          $t_verkoop_koers					= $mutaties['Fondskoers'];
          
          $totaal_verkoop_waarde += $t_verkoop_waarde;
          
          if($t_verkoop_koers > 0)
            $verkoop_koers 					= $t_verkoop_koers;
          if($t_verkoop_waardeinValuta > 0)
            $verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
          if($t_verkoop_waarde > 0)
            $verkoop_waarde 				= $t_verkoop_waarde;
          break;
        case "V" :
          // Verkopen
          $t_verkoop_waarde 				= ($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
          $t_verkoop_waardeinValuta = ($mutaties['Credit']);
          $t_verkoop_koers					= $mutaties['Fondskoers'];
          
          $totaal_verkoop_waarde += $t_verkoop_waarde;
          
          //if($t_verkoop_koers > 0)
          $verkoop_koers 					= $t_verkoop_koers;
          //if($t_verkoop_waardeinValuta > 0)
          $verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
          //if($t_verkoop_waarde > 0)
          $verkoop_waarde 				= $t_verkoop_waarde;
          break;
        case "V/O" :
          // Verkopen / openen
          $t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
          $t_verkoop_waardeinValuta = abs($mutaties['Credit']);
          $t_verkoop_koers					= $mutaties['Fondskoers'];
          
          $totaal_verkoop_waarde += $t_verkoop_waarde;
          
          if($t_verkoop_koers > 0)
            $verkoop_koers 					= $t_verkoop_koers;
          if($t_verkoop_waardeinValuta > 0)
            $verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
          if($t_verkoop_waarde > 0)
            $verkoop_waarde 				= $t_verkoop_waarde;
          break;
        case "V/S" :
          // Verkopen / sluiten
          $t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
          $t_verkoop_waardeinValuta = abs($mutaties['Credit']);
          $t_verkoop_koers					= $mutaties['Fondskoers'];
          
          $totaal_verkoop_waarde += $t_verkoop_waarde;
          
          if($t_verkoop_koers > 0)
            $verkoop_koers 					= $t_verkoop_koers;
          if($t_verkoop_waardeinValuta > 0)
            $verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
          if($t_verkoop_waarde > 0)
            $verkoop_waarde 				= $t_verkoop_waarde;
          break;
        default :
          $_error = "Fout ongeldig tranactietype!!";
          break;
      }
      
      
      //	Alleen resultaat berekenen bij "Sluiten", niet bij "Openen".
      
      
      if(	$mutaties['Transactietype'] == "L" ||
        $mutaties['Transactietype'] == "V" ||
        $mutaties['Transactietype'] == "V/S" ||
        $mutaties['Transactietype'] == "A/S")
      {
        
        $historie = berekenHistorischKostprijs($this->portefeuille, $mutaties['Fonds'], $mutaties['Boekdatum'],$this->pdf->rapportageValuta,$this->rapportageDatumVanaf,$mutaties['id']);
        
        if($mutaties['Transactietype'] == "A/S")
        {
          $historischekostprijs  = ($mutaties['Aantal'] * -1) * $historie['historischeWaarde']      * $historie['historischeValutakoers']        * $mutaties['Fondseenheid'];
          $beginditjaar          = ($mutaties['Aantal'] * -1) * $historie['beginwaardeLopendeJaar'] * $historie['beginwaardeValutaLopendeJaar']  * $mutaties['Fondseenheid'];
        }
        else
        {
          $historischekostprijs = $mutaties['Aantal']        * $historie['historischeWaarde']       * $historie['historischeValutakoers']        * $mutaties['Fondseenheid'];
          $beginditjaar         = $mutaties['Aantal']        * $historie['beginwaardeLopendeJaar']  * $historie['beginwaardeValutaLopendeJaar']  * $mutaties['Fondseenheid'];
        }
        if($this->pdf->rapportageValuta != 'EUR' && $mutaties['Valuta'] == $this->pdf->rapportageValuta)
        {
          $historischekostprijs = $historischekostprijs / $historie['historischeValutakoers'];
          $beginditjaar         = $beginditjaar         / getValutaKoers($this->pdf->rapportageValuta ,date("Y",db2jul($this->rapportageDatum).'-01-01'));
        }
        elseif ($this->pdf->rapportageValuta != 'EUR')
        {
          $historischekostprijs = $historischekostprijs / $historie['historischeRapportageValutakoers'];
          $beginditjaar         = $beginditjaar         / getValutaKoers($this->pdf->rapportageValuta ,date("Y",db2jul($this->rapportageDatum).'-01-01'));
        }
        
        if($historie['voorgaandejarenActief'] == 0)
        {
          $resultaatvoorgaande = 0;
          $resultaatlopende = $t_verkoop_waarde - $historischekostprijs;
          if($mutaties['Transactietype'] == "A/S")
          {
            $resultaatvoorgaande = 0;
            $resultaatlopende = $t_aankoop_waarde - $historischekostprijs;
          }
        }
        else
        {
          $resultaatvoorgaande = $beginditjaar - $historischekostprijs;
          $resultaatlopende = $t_verkoop_waarde - $beginditjaar;
          if($mutaties['Transactietype'] == "A/S")
          {
            $resultaatvoorgaande = $beginditjaar - $historischekostprijs;
            $resultaatlopende = ($t_aankoop_waarde * -1) - $beginditjaar;
          }
        }
        $result_historischkostprijs = $historischekostprijs;
        $result_voorgaandejaren = $resultaatvoorgaande;
        $result_lopendejaar = $resultaatlopende;
        
        $totaal_resultaat_waarde += ($resultaatlopende+$result_voorgaandejaren);
        
      }
      else
      {
        $result_historischkostprijs = "";
        $result_voorgaandejaren = "";
        $result_lopendejaar = "";
      }
      
      //	listarray($mutaties);
      $data[$mutaties['Fonds']]['aankoopWaarde']+=$aankoop_waarde;
      $data[$mutaties['Fonds']]['verkoopWaarde']+=$verkoop_waarde;
      $data[$mutaties['Fonds']]['resultaat']+=$resultaatlopende;
      $data[$mutaties['Fonds']]['transacties'].=' '.$mutaties['Transactietype'];
      if($mutaties['Credit'])
      {
        $data[$mutaties['Fonds']]['totaalAantal']-=$mutaties['Aantal'];
        $data[$mutaties['Fonds']]['verkoopAantal']+=$mutaties['Aantal'];
      }
      else
      {
        $data[$mutaties['Fonds']]['totaalAantal']+=$mutaties['Aantal'];
        $data[$mutaties['Fonds']]['aankoopAantal']+=$mutaties['Aantal'];
      }
      $data[$mutaties['Fonds']]['Beleggingscategorie']=$mutaties['Beleggingscategorie'];
      

      
    }
    
    return $data;
  }

}
?>
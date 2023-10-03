<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.14 $

$Log: RapportFISCAAL_L33.php,v $
Revision 1.14  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.13  2017/05/27 09:45:52  rvv
*** empty log message ***

Revision 1.12  2016/04/15 04:09:41  rvv
*** empty log message ***

Revision 1.11  2015/12/19 08:29:17  rvv
*** empty log message ***

Revision 1.10  2015/04/22 15:25:19  rvv
*** empty log message ***

Revision 1.9  2014/12/03 17:30:11  rvv
*** empty log message ***

Revision 1.8  2014/10/27 11:37:17  rvv
*** empty log message ***

Revision 1.7  2014/10/25 14:39:09  rvv
*** empty log message ***

Revision 1.6  2014/10/23 06:47:04  rvv
*** empty log message ***

Revision 1.5  2014/10/23 06:28:14  rvv
*** empty log message ***

Revision 1.4  2014/10/23 05:09:42  rvv
*** empty log message ***

Revision 1.3  2014/10/22 15:50:27  rvv
*** empty log message ***

Revision 1.2  2014/10/19 08:52:15  rvv
*** empty log message ***

Revision 1.1  2014/10/15 16:05:49  rvv
*** empty log message ***

Revision 1.2  2014/06/04 16:12:52  rvv
*** empty log message ***

Revision 1.1  2012/12/19 17:00:51  rvv
*** empty log message ***

Revision 1.56  2011/06/25 16:51:45  rvv
*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFISCAAL_L33
{
	function RapportFISCAAL_L33($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "geen";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "obligaties";
    if(!isset($this->pdf->excelData) || count($this->pdf->excelData)<1)
	    $this->pdf->excelData[] = array('ISIN code','Omschrijving','','Rating','Soort obligatie','Datum jaar coupon','Jaar van aflossing','Resterende looptijd','Coupon rente','Nominale waarde begin kwartaal','Nominale aankopen in kwartaal','Nominale verkopen in kwartaal','Nominale waarde eind kwartaal','Koers einde kwartaal','Kostprijs begin kwartaal','Herwaardering begin kwartaal','Aankoop koers in kwartaal','Aankoopprijs in kwartaal [nominaal x koers]','Provisie aankopen in kwartaal','Verkoop koers in kwartaal','Verkoopprijs in kwartaal [nominaal x koers]','Provisie verkopen in kwartaal','Netto verkoopprijs [verkoopprijs - provisie]','Kostprijs verkopen','Herwaardering verkopen','Gerealiseerde verkoopresultaat','Kostprijs einde kwartaal','Herwaardering einde kwartaal','Marktwaarde einde kwartaal','Rentereservering begin kwartaal','Verdiende rente kwartaal','Ontvangen coupon rente','Aangekochte rente','Verkochte rente','Rentereservering einde kwartaal');
      
      
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}


	function writeRapport()
	{
		global $__appvar;
		$DB = new DB();

  	$this->pdf->AddPage();

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
Fondsen.Fondseenheid
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

		$query = "SELECT Fonds,rapportageDatum,actueleValuta,Beleggingscategorie,actuelePortefeuilleWaardeEuro,type,totaalAantal FROM TijdelijkeRapportage WHERE rapportageDatum IN('".$this->rapportageDatumVanaf."','".$this->rapportageDatum."') AND  portefeuille = '".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek']." ORDER BY Fonds,rapportageDatum,type";    
  	$DB->SQL($query);
		$DB->Query();  
    while($data=$DB->nextRecord())
    {
      if($data['Fonds']<>'')
      {
        if($data['Beleggingscategorie'] <> '')
          $fondsen[$data['Fonds']]['Beleggingscategorie']=$data['Beleggingscategorie'];
          
        if($data['type']=='rente')
          $fondsen[$data['Fonds']][$data['rapportageDatum']]['renteActuelePortefeuilleWaardeEuro']=$data['actuelePortefeuilleWaardeEuro'];
        else
        {
          $fondsen[$data['Fonds']][$data['rapportageDatum']]['actuelePortefeuilleWaardeEuro']=$data['actuelePortefeuilleWaardeEuro'];
          $fondsen[$data['Fonds']][$data['rapportageDatum']]['totaalAantal']=$data['totaalAantal'];
        }
      }
    }
 
    foreach($mutaties['periode'] as $fonds=>$mutatieData)
    {
      if(!isset($fondsen[$fonds][$this->rapportageDatumVanaf]))
        $fondsen[$fonds][$this->rapportageDatumVanaf]=array('Beleggingscategorie'=>$mutatieData['6'],'actuelePortefeuilleWaardeEuro'=>0);
      if(!isset($fondsen[$fonds][$this->rapportageDatum]))
        $fondsen[$fonds][$this->rapportageDatum]=array('Beleggingscategorie'=>$mutatieData['6'],'actuelePortefeuilleWaardeEuro'=>0);
        
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

    $eurValuta=array('DEM','NLG','ITL');
    foreach($fondsen as $fonds=>$fondsData)
    { 
      $looptijdRest=substr($fondsData['Lossingsdatum'],0,4)-substr($this->rapportageDatum,0,4);
      if($looptijdRest<1)
        $code='A';
      elseif($looptijdRest<5)
        $code='B';
      elseif($looptijdRest<10)
        $code='C';
      else
        $code='D';
      
      if(in_array($fondsData['Valuta'],$eurValuta))
        $nominaalFactor=$fondsData[$this->rapportageDatum]['actueleValuta'];
      else
        $nominaalFactor=1;

      $this->pdf->excelData[] = array($fondsData['ISINCode'],//A
                                        $fondsData['Omschrijving'],//B
                                        $code,//C
                                        $fondsData['Rating'],//D
                                        $fondsData['Beleggingscategorie'],//E
                                        $fondsData['VolgendeRentedatum'],//F
                                        $fondsData['Lossingsdatum'],//G
                                        $looptijdRest,//H
                                        number_format($fondsData['Rentepercentage'],5),//I
                                        $fondsData[$this->rapportageDatumVanaf]['totaalAantal']*$nominaalFactor,//J
                                        $fondsData['trans']['aankoopAantal']*$nominaalFactor,//K
                                        $fondsData['trans']['verkoopAantal']*$nominaalFactor,//L
                                        $fondsData[$this->rapportageDatum]['totaalAantal']*$nominaalFactor,//M
                                        $fondsData[$this->rapportageDatum]['koers'],//N
                                        '',//'Kostprijs begin kwartaal' //O
                                        '',//'Herwaardering begin kwartaal'//P
                                        $fondsTransacties[$fonds]['FONDS']['aankoop']['Koers'],//'Aankoop koers in kwartaal',//Q
                                        $fondsTransacties[$fonds]['FONDS']['aankoop']['Aantal']*$fondsTransacties[$fonds]['FONDS']['aankoop']['Koers']*$fondsData['Fondseenheid']*$fondsData[$this->rapportageDatum]['actueleValuta'],//'Aankoopprijs in kwartaal [nominaal x koers]',
                                        $fondsTransacties[$fonds]['FONDS']['aankoop']['kosten'],//S
                                        $fondsTransacties[$fonds]['FONDS']['verkoop']['Koers'],//'Verkoop koers in kwartaal',//T
                                        $fondsTransacties[$fonds]['FONDS']['verkoop']['Aantal']*$fondsTransacties[$fonds]['FONDS']['verkoop']['Koers']*$fondsData['Fondseenheid']*$fondsData[$this->rapportageDatum]['actueleValuta'],//'Verkoopprijs in kwartaal [nominaal x koers]',
                                        $fondsTransacties[$fonds]['FONDS']['verkoop']['kosten'],//'Provisie verkopen in kwartaal',//V
                                        ($fondsTransacties[$fonds]['FONDS']['verkoop']['Aantal']*$fondsTransacties[$fonds]['FONDS']['verkoop']['Koers']*$fondsData['Fondseenheid']*$fondsData[$this->rapportageDatum]['actueleValuta'])-$fondsTransacties[$fonds]['FONDS']['verkoop']['kosten'],//'Netto verkoopprijs [verkoopprijs - provisie]',//W
                                        '',//'Kostprijs verkopen',//X
                                        '',//'Herwaardering verkopen',//Y
                                        '',//'Gerealiseerde verkoopresultaat',//Z
                                        '',//'Kostprijs einde kwartaal',//AA
                                        '',//'Herwaardering einde kwartaal',//AB
                                        $fondsData[$this->rapportageDatum]['koers']*$fondsData[$this->rapportageDatum]['totaalAantal']*$fondsData['Fondseenheid']*$fondsData[$this->rapportageDatum]['actueleValuta'],//Eindwaarde ex opg. Rente//AC
                                        $fondsData[$this->rapportageDatumVanaf]['renteActuelePortefeuilleWaardeEuro'],//AD
                                        $fondsData[$this->rapportageDatum]['renteActuelePortefeuilleWaardeEuro']+
                                          $fondsTransacties[$fonds]['RENME']['totaalWaarde']+
                                          $fondsTransacties[$fonds]['RENOB']['totaalWaarde']-
                                          $fondsData[$this->rapportageDatumVanaf]['renteActuelePortefeuilleWaardeEuro'],//'Verdiende rente kwartaal',//AE
                                        $fondsTransacties[$fonds]['RENOB']['zonderFonds'],//Ontvangen coupon rente //AF
                                        $fondsTransacties[$fonds]['RENME']['totaalWaarde'],//Aangekochte rente //AG
                                        $fondsTransacties[$fonds]['FONDS']['verkoop']['RENOB'],//Verkochte rente //AH
                                        $fondsData[$this->rapportageDatum]['renteActuelePortefeuilleWaardeEuro']); //AI
                                        //Opg Rente Einde + renme -renme+renob-opgelopenrente begin

    }
  //  listarray($fondsTransacties);
  // listarray($fondsen);
  // listarray($this->pdf->excelData);
//	exit;
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

		while($mutaties = $DB->nextRecord())
		{
			$buffer[] = $mutaties;
		}

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
						$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties[Debet]);
						$t_aankoop_koers					= $mutaties[Fondskoers];

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
						$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties[Debet]);
						$t_aankoop_koers					= $mutaties[Fondskoers];

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
						$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties[Debet]);
						$t_aankoop_koers					= $mutaties[Fondskoers];

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
						$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties[Credit]);
						$t_verkoop_koers					= $mutaties[Fondskoers];

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
						$t_verkoop_waarde 				= ($mutaties[Credit]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = ($mutaties[Credit]);
						$t_verkoop_koers					= $mutaties[Fondskoers];

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
						$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties[Credit]);
						$t_verkoop_koers					= $mutaties[Fondskoers];

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
						$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties[Credit]);
						$t_verkoop_koers					= $mutaties[Fondskoers];

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

				$historie = berekenHistorischKostprijs($this->portefeuille, $mutaties[Fonds], $mutaties[Boekdatum],$this->pdf->rapportageValuta);

				if($mutaties['Transactietype'] == "A/S")
				{
					$historischekostprijs  = ($mutaties[Aantal] * -1) * $historie[historischeWaarde]      * $historie[historischeValutakoers]        * $mutaties[Fondseenheid];
					$beginditjaar          = ($mutaties[Aantal] * -1) * $historie[beginwaardeLopendeJaar] * $historie[beginwaardeValutaLopendeJaar]  * $mutaties[Fondseenheid];
				}
				else
				{
					$historischekostprijs = $mutaties[Aantal]        * $historie[historischeWaarde]       * $historie[historischeValutakoers]        * $mutaties[Fondseenheid];
				  $beginditjaar         = $mutaties[Aantal]        * $historie[beginwaardeLopendeJaar]  * $historie[beginwaardeValutaLopendeJaar]  * $mutaties[Fondseenheid];
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

				if($historie[voorgaandejarenActief] == 0)
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

				$totaal_resultaat_waarde += $resultaatlopende;

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
        

				/*
				$data[]=array(date("d-m",db2jul($mutaties['Boekdatum'])),
											$mutaties['Transactietype'],
											$mutaties['Fonds'],
											$this->formatGetal($mutaties['Aantal'],0),
											"",
											$aankoop_koers,
											$aankoop_waardeinValuta,
											$aankoop_waarde,
											$verkoop_koers,
											$verkoop_waardeinValuta,
											$verkoop_waarde,
											$result_historischkostprijs,
											$result_voorgaandejaren,
											$result_lopendejaar,
											$percentageTotaalTekst);
				*/

		}
   
		//listarray($data);
		return $data;
	}  
}
?>

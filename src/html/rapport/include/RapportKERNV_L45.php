<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/02/10 18:09:12 $
File Versie					: $Revision: 1.3 $

$Log: RapportKERNV_L45.php,v $
Revision 1.3  2018/02/10 18:09:12  rvv
*** empty log message ***

Revision 1.2  2017/05/27 09:45:52  rvv
*** empty log message ***

Revision 1.1  2017/02/11 17:30:10  rvv
*** empty log message ***

Revision 1.10  2016/04/16 17:12:30  rvv
*** empty log message ***

Revision 1.9  2015/12/19 08:29:17  rvv
*** empty log message ***

Revision 1.8  2015/06/10 16:01:15  rvv
*** empty log message ***

Revision 1.7  2015/05/15 07:04:23  rvv
*** empty log message ***

Revision 1.6  2015/05/13 15:45:13  rvv
*** empty log message ***

Revision 1.5  2015/05/10 08:02:25  rvv
*** empty log message ***

Revision 1.4  2015/05/06 15:35:53  rvv
*** empty log message ***

Revision 1.3  2015/05/04 08:02:26  rvv
*** empty log message ***

Revision 1.2  2015/05/04 07:32:59  rvv
*** empty log message ***

Revision 1.1  2015/05/02 14:57:32  rvv
*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportKERNV_L45
{
	function RapportKERNV_L45($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "geen";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "obligaties";
    if(!isset($this->pdf->excelData) || count($this->pdf->excelData)<1)
      $this->pdf->excelData[] = array('ISIN','Fonds','Beleggingscategorie','Rating','TypeInstrument','Valuta','Positie','Historische koers','Historisch Waarde EUR','Ytd-kostprijs','Actuele koers','Waarde in EUR','Opgelopen rente in EUR','Totale waarde in EUR','Actueel rentepercentage','Mod. Duration','Lossingsdatum','Portefeuille','Koersdatum');
    
    //$this->pdf->excelData[] = array('ISIN code','Omschrijving','','Rating','Soort obligatie','Datum jaar coupon','Jaar van aflossing','Resterende looptijd','Coupon rente','Nominale waarde begin kwartaal','Nominale aankopen in kwartaal','Nominale verkopen in kwartaal','Nominale waarde eind kwartaal','Koers einde kwartaal','Kostprijs begin kwartaal','Herwaardering begin kwartaal','Aankoop koers in kwartaal','Aankoopprijs in kwartaal [nominaal x koers]','Provisie aankopen in kwartaal','Verkoop koers in kwartaal','Verkoopprijs in kwartaal [nominaal x koers]','Provisie verkopen in kwartaal','Netto verkoopprijs [verkoopprijs - provisie]','Kostprijs verkopen','Herwaardering verkopen','Gerealiseerde verkoopresultaat','Kostprijs einde kwartaal','Herwaardering einde kwartaal','Marktwaarde einde kwartaal','Rentereservering begin kwartaal','Verdiende rente kwartaal','Ontvangen coupon rente','Aangekochte rente','Verkochte rente','Rentereservering einde kwartaal');
      
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}


	function writeRapport()
	{
		global $__appvar;
		$DB = new DB();

  	$this->pdf->AddPage();
      $this->cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		  $this->cashfow->genereerTransacties();
		  $this->cashfow->genereerRows();
    //$mutaties['periode']=$this->genereerMutatieLijst($this->rapportageDatumVanaf,$this->rapportageDatum);
 
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

		$query="DESC FondsExtraInformatie";
		$DB->SQL($query);
		$DB->Query();
		$typeInstrumentLookup=false;
    while($data = $DB->nextRecord())
		{
			if($data['Field']=='TypeInstrument')
				$typeInstrumentLookup=true;
		}
    
                                      
		$query = "SELECT Fonds,rapportageDatum,actueleValuta,Beleggingscategorie,actuelePortefeuilleWaardeEuro,type,totaalAantal,rekening,Valuta,
    Fondsomschrijving as Omschrijving,koersDatum,portefeuille,
    historischeWaarde,beginwaardeLopendeJaar,actueleFonds, 
(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeEUR    
    FROM TijdelijkeRapportage WHERE rapportageDatum IN('".$this->rapportageDatum."') AND  portefeuille = '".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek']." ORDER BY Fonds,rapportageDatum,type";    
  	$DB->SQL($query); //'".$this->rapportageDatumVanaf."',
		$DB->Query(); 
    while($data=$DB->nextRecord())
    {
      if($data['Fonds']<>'')
      {
        $fondsen[$data['Fonds']]['Valuta']=$data['Valuta'];
        $fondsen[$data['Fonds']]['Omschrijving']=$data['Omschrijving'];
        $fondsen[$data['Fonds']]['koersDatum']=$data['koersDatum'];
        $fondsen[$data['Fonds']]['portefeuille']=$data['portefeuille'];
        
        if($data['Beleggingscategorie'] <> '')
          $fondsen[$data['Fonds']]['Beleggingscategorie']=$data['Beleggingscategorie'];
          
        if($data['type']=='rente')
          $fondsen[$data['Fonds']]['renteActuelePortefeuilleWaardeEuro']=$data['actuelePortefeuilleWaardeEuro'];
        else
        {
          $fondsen[$data['Fonds']]['actuelePortefeuilleWaardeEuro']=$data['actuelePortefeuilleWaardeEuro'];
          $fondsen[$data['Fonds']]['totaalAantal']=$data['totaalAantal'];
          $fondsen[$data['Fonds']]['historischeWaarde']=$data['historischeWaarde'];
          $fondsen[$data['Fonds']]['beginwaardeLopendeJaar']=$data['beginwaardeLopendeJaar'];
          $fondsen[$data['Fonds']]['actueleFonds']=$data['actueleFonds'];
					$fondsen[$data['Fonds']]['historischeWaardeEUR']=$data['historischeWaardeEUR'];

        }
      }
      elseif($data['type']=='rekening')
      {
        $fondsen[$data['rekening']]=$data;
      }
    }
 

    
    foreach($fondsen as $fonds=>$fondsData)
    {
      $query="SELECT ISINCode,fondssoort,Rating,Renteperiode,EersteRentedatum,Rentedatum,Lossingsdatum,Fondseenheid,variabeleCoupon FROM Fondsen WHERE Fonds='$fonds'";
      $DB->SQL($query);
      $extraFondsData=$DB->lookupRecord();
      foreach($extraFondsData as $key=>$value)
        $fondsen[$fonds][$key]=$value;

	   if($typeInstrumentLookup==true)
		 {
			 $query="SELECT TypeInstrument FROM FondsExtraInformatie WHERE Fonds='$fonds'";
			 $DB->SQL($query);
			 $extraFondsData=$DB->lookupRecord();
			 foreach($extraFondsData as $key=>$value)
				 $fondsen[$fonds][$key]=$value;
		 }


			$rente=getRenteParameters($fonds, $this->rapportageDatum);
			foreach($rente as $key=>$value)
				$fondsen[$fonds][$key]=$value;

      $query="SELECT
emittentPerFonds.emittent,
emittenten.naam as emittentNaam
FROM
emittentPerFonds
INNER JOIN emittenten ON emittentPerFonds.emittent = emittenten.emittent
WHERE emittentPerFonds.vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND  emittentPerFonds.fonds='$fonds'"; 
      $DB->SQL($query);

      $extraFondsData=$DB->lookupRecord();
      foreach($extraFondsData as $key=>$value)
        $fondsen[$fonds][$key]=$value;        

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

    foreach($fondsen as $fonds=>$fondsData)
    { 
      //$looptijdRest=substr($fondsData['Lossingsdatum'],0,4)-substr($this->rapportageDatum,0,4);
      
      if(in_array($fondsData['Valuta'],$eurValuta))
        $nominaalFactor=$fondsData['actueleValuta'];
      else
        $nominaalFactor=1;
        

      $rente=$fondsData['renteActuelePortefeuilleWaardeEuro'];//-$fondsData[$this->rapportageDatumVanaf]['renteActuelePortefeuilleWaardeEuro'];

      if($fondsData['Lossingsdatum'] <> '')
       $lossingsJul = adodb_db2jul($fondsData['Lossingsdatum']);
      else
       $lossingsJul=0;
     $rentedatumJul = adodb_db2jul($fondsData['Rentedatum']);
     $renteVanafJul = adodb_db2jul(jul2sql($this->pdf->rapport_datum));

     $ytm=0;
     $duration=0;
     $modifiedDuration=0;
     if($lossingsJul > 0)
	   {
	     $koers = $fondsData['Rentepercentage'];
         
       $renteDag=0;
			 if($fondsData['variabeleCoupon'] == 1)
			 {
			    $rapportJul=adodb_db2jul($this->rapportageDatum);
			    $renteJul=adodb_db2jul($fondsData['Rentedatum']);
          $renteStap=($fondsData['Renteperiode']/12)*31556925.96;
          $renteDag=$renteJul;
          if($renteStap > 100000)
            while($renteDag<$rapportJul)
            { 
              $renteDag+=$renteStap;
              //if($fondsData['ISINCode']=='XS1108681625'){echo $fondsData['Renteperiode']." ".$fondsData['Rentedatum']." ".date('d-m-Y',$renteDag)."<br>\n";}
            }
			 }

		   $jaar = ($lossingsJul-$renteVanafJul)/31556925.96;

		   $p = $fondsData['actueleFonds'];
	     $r = $fondsData['Rentepercentage']/100;
	     $b = $this->cashfow->fondsDataKeyed[$fonds]['lossingskoers'];
	     $y = $jaar;

	     $ytm=  $this->cashfow->bondYTM($p,$r,$b,$y)*100;
      // echo $fonds['fonds']." $ytm=  $this->cashfow->bondYTM($p,$r,$b,$y)*100; <br>\n";
       
	     $restLooptijd=($lossingsJul-$this->pdf->rapport_datum)/31556925.96;

	     $duration=$this->cashfow->waardePerFonds[$fonds]['ActueelWaardeJaar']/$this->cashfow->waardePerFonds[$fonds]['ActueelWaarde'];

       if($fondsData['variabeleCoupon'] == 1 && $renteDag <> 0)
	       $modifiedDuration=($renteDag-db2jul($this->rapportageDatum))/86400/365;
	     else
	       $modifiedDuration=$duration/(1+$ytm/100);
     /*
       if($fondsData['ISINCode']=='XS1108681625')
       {
         listarray($fondsData);
         echo "variabeleCoupon:1 $modifiedDuration=(".date("Y-m-d",$renteDag)." - ".$this->rapportageDatum.")dagen/365; <br>\n";
         echo "variabeleCoupon:0 $modifiedDuration=$duration/(1+$ytm/100);<br>\n";
         exit;
       }   
      */
     }
     if($fondsData['Lossingsdatum']=='0000-00-00')
       $lossingsDatum='';
     else
       $lossingsDatum=$fondsData['Lossingsdatum'];
       
      $this->pdf->excelData[] = array($fondsData['ISINCode'],
                                      $fondsData['Omschrijving'],
                                      $fondsData['Beleggingscategorie'],
                                      $fondsData['Rating'],
				                              $fondsData['TypeInstrument'],
                                      $fondsData['Valuta'],
                                      array($fondsData['totaalAantal'],'bedrag'),
                                      array($fondsData['historischeWaarde'],'koers'),
				                              array($fondsData['historischeWaardeEUR'],'bedrag'),
                                      array($fondsData['beginwaardeLopendeJaar'],'koers'),
                                      array($fondsData['actueleFonds'],'koers'),
                                      array($fondsData['actuelePortefeuilleWaardeEuro'],'bedrag'),
                                      array($rente,'bedrag'),
                                      array($fondsData['actuelePortefeuilleWaardeEuro']+$rente,'bedrag'),
                                      number_format($fondsData['Rentepercentage'],5),
                                      round($modifiedDuration,2),
                                      $lossingsDatum,
                                      $fondsData['portefeuille'],
                                      $fondsData['koersDatum']
                                      );
                                        //Opg Rente Einde + renme -renme+renob-opgelopenrente begin

    }
    
    $this->pdf->excelOpmaak['koers']=array('setNumFormat'=>'4');//'setBgColor'=>'26','setFgColor'=>'8','setSize'=>'10',
    $this->pdf->excelOpmaak['bedrag']=array('setNumFormat'=>'3');//'setBgColor'=>'26','setFgColor'=>'8','setSize'=>'10',

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

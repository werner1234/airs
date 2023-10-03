<?php
/*
Author  						: $Author: rm $
Laatste aanpassing	: $Date: 2019/06/21 14:52:45 $
File Versie					: $Revision: 1.22 $

$Log: RapportTRANS_L72.php,v $
Revision 1.22  2019/06/21 14:52:45  rm
7880

Revision 1.21  2018/12/14 16:43:21  rvv
*** empty log message ***

Revision 1.20  2018/11/17 17:34:53  rvv
*** empty log message ***

Revision 1.19  2018/10/17 15:37:17  rvv
*** empty log message ***

Revision 1.18  2018/10/10 15:50:56  rvv
*** empty log message ***

Revision 1.17  2018/10/03 15:42:01  rvv
*** empty log message ***

Revision 1.16  2018/04/07 15:21:44  rvv
*** empty log message ***

Revision 1.15  2018/03/31 18:06:01  rvv
*** empty log message ***

Revision 1.14  2017/10/25 16:00:04  rvv
*** empty log message ***

Revision 1.13  2017/04/15 19:11:50  rvv
*** empty log message ***

Revision 1.12  2017/03/01 17:17:08  rvv
*** empty log message ***

Revision 1.11  2017/02/19 09:21:57  rvv
*** empty log message ***

Revision 1.10  2017/02/15 13:03:09  rvv
*** empty log message ***

Revision 1.9  2017/02/11 17:30:10  rvv
*** empty log message ***

Revision 1.8  2017/02/08 12:32:32  rvv
*** empty log message ***

Revision 1.7  2017/01/29 10:25:25  rvv
*** empty log message ***

Revision 1.6  2016/12/30 15:31:00  rvv
*** empty log message ***

Revision 1.5  2016/12/14 14:00:06  rvv
*** empty log message ***

Revision 1.4  2016/12/14 12:12:58  rvv
*** empty log message ***

Revision 1.3  2016/12/10 10:36:55  rvv
*** empty log message ***

Revision 1.2  2016/12/04 10:08:56  rvv
*** empty log message ***

Revision 1.1  2016/12/03 19:22:25  rvv
*** empty log message ***

Revision 1.1  2016/06/15 15:58:41  rvv
*** empty log message ***

Revision 1.2  2016/03/19 16:51:09  rvv
*** empty log message ***

Revision 1.1  2016/03/06 14:37:11  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportTransactieoverzichtLayout.php");

class RapportTRANS_L72
{
	function RapportTRANS_L72($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "TRANS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Transactie-overzicht";

		if ($this->pdf->rapportageValuta != 'EUR' && $this->pdf->rapportageValuta != '')
		  $this->pdf->rapport_titel .= " in ".$this->pdf->rapportageValuta;

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;


	  $this->pdf->excelData[]=array("Naam fonds",'Transactietype',"Aantal","Koers","Val.","Val.Koers","Bruto","Kosten","Belastingen","Rente","Netto","Resultaat","Datum");

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
	       if ($decimaal != '0' && !isset($newDec))
	       {
	       //  echo $this->portefeuille." $waarde <br>";exit;
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



	function addHeader($categorie)
	{
		if($this->pdf->getY()+($this->pdf->rowHeight*1.5*2) > $this->pdf->PageBreakTrigger)
			$this->pdf->addPage();
		$rowHeightBackup=$this->pdf->rowHeight;
		$this->pdf->rowHeight=$rowHeightBackup*1.5;
		$this->pdf->CellBorders = array(array('T','U','L'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_mut_headercolor['r'],$this->pdf->rapport_mut_headercolor['g'],$this->pdf->rapport_mut_headercolor['b']);
		$this->pdf->row(array($categorie,'','','','','','','','','','','',''));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array(array('U','L'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U','R'));
		$this->pdf->row(array(vertaalTekst("Naam fonds",$this->pdf->rapport_taal),
											vertaalTekst("Aantal",$this->pdf->rapport_taal),
											vertaalTekst("Koers",$this->pdf->rapport_taal),
											vertaalTekst("Val.",$this->pdf->rapport_taal),
											vertaalTekst("Val.Koers",$this->pdf->rapport_taal),
											vertaalTekst("Bruto",$this->pdf->rapport_taal),
											vertaalTekst("Kosten",$this->pdf->rapport_taal),
                      vertaalTekst("Belastingen",$this->pdf->rapport_taal),
											vertaalTekst("Rente",$this->pdf->rapport_taal),
											vertaalTekst("Netto",$this->pdf->rapport_taal),
											vertaalTekst("Resultaat",$this->pdf->rapport_taal),
											vertaalTekst("Datum",$this->pdf->rapport_taal)));
		$this->pdf->SetTextColor(0);
		unset($this->pdf->CellBorders);
		$this->pdf->rowHeight=$rowHeightBackup;
		$this->pdf->ln(1);
	}

	function addTotaal($totaal,$addU=false)
	{
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array('','','','','',array('T'),array('T'),array('T'),array('T'),array('T'),array('T'),'');
		if($addU==true)
			$this->pdf->CellBorders = array('','','','','',array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),'');

		$this->pdf->row(array('',
											'',
											'',
											'',
											'',
											$this->formatGetal($totaal['bruto'],2),
											$this->formatGetal($totaal['kosten'],2),
                      $this->formatGetal($totaal['belasting'],2),
											$this->formatGetal($totaal['rente'],2),
											$this->formatGetal($totaal['netto'],2),
											$this->formatGetal($totaal['resultaat'],2),
											''));
		unset($this->pdf->CellBorders);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

	}

	function gemiddeldeTransactieValutaKoers($fonds)
	{
		$valutaKoers=$this->pdf->ValutaKoersBegin;
		if($fonds=='')
			return $this->pdf->ValutaKoersBegin;

		$query="SELECT Boekdatum,Debet,Credit,Bedrag,Omschrijving ,((Credit*Valutakoers)-(Debet*Valutakoers)) as BedragEur,Transactietype
     FROM Rekeningmutaties 
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening 
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND 
     Rekeningmutaties.Boekdatum >= '".	$this->rapportageDatumVanaf."' AND 
     Rekeningmutaties.Boekdatum <= '".	$this->rapportageDatum."' AND
     Rekeningmutaties.Fonds='$fonds' AND Grootboekrekening='FONDS' AND Rekeningmutaties.Transactietype NOT IN('V','L','A/S','V/S')";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$totaalEur=0;
		$waardeRapportageKoers=0;
		while($data = $DB->nextRecord())
		{
			if($data['Transactietype']=='B')
			{
				$tmp=fondsWaardeOpdatum($this->portefeuille,$fonds,$data['Boekdatum'],'EUR');
				$bedrag = ($tmp['fondsEenheid'] * $tmp['totaalAantal']) * $tmp['beginwaardeLopendeJaar'] *  $tmp['beginwaardeValutaLopendeJaar'];
			}
			else
				$bedrag=abs($data['BedragEur']);

			$valutaKoers=getValutaKoers($this->pdf->rapportageValuta,$data['Boekdatum']);
			if($valutaKoers=='')
				$valutaKoers=$this->pdf->ValutaKoersBegin;
			//$waardeRapportageKoers+=($bedrag*$valutaKoers);
			$waardeRapportageKoers+=($bedrag/$valutaKoers);

			//echo "$fonds $bedrag*$valutaKoers=".($bedrag*$valutaKoers)."<br>\n";
			$totaalEur+=$bedrag;
		}
		//$gemiddeldeValutakoers=$waardeRapportageKoers/$totaalEur;
		//echo "$fonds $gemiddeldeValutakoers=$waardeRapportageKoers/$totaalEur; <br>\n";
		$gemiddeldeValutakoers=$totaalEur/$waardeRapportageKoers;
		// echo "$fonds $gemiddeldeValutakoers=$totaalEur/$waardeRapportageKoers; <br>\n";

		if($gemiddeldeValutakoers <> 0)
			return $gemiddeldeValutakoers;
		else
			return $valutaKoers;
	}

	function writeRapport()
	{


	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		$DB = new DB();
		$db2 = new DB();
//18
		//5 *3
		// voor data
		$this->pdf->widthB = array(70,18,18,12,18,24,18,19,18,24,21,20);
		$this->pdf->alignB = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');
		// voor kopjes
		$this->pdf->widthA = $this->pdf->widthB;
		$this->pdf->alignA = $this->pdf->alignB;


		if($this->pdf->rapport_MUT_kwartaal == 1 && ($this->pdf->selectData['backoffice'] == true) )
		{
			$maand = date("n",db2jul($this->rapportageDatum));
			$kwartaal = floor(($maand / 4)+1);
			switch($kwartaal)
			{
				case 1 :
					$this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-01-01";
					$this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
				break;
				case 2 :
					$this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-03-31";
					$this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
				break;
				case 3 :
					$this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-06-31";
					$this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
				break;
				case 4 :
					$this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-09-30";
					$this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
				break;
			}
		}

		$this->pdf->AddPage();
		$this->pdf->setWidths($this->pdf->widthB);
		$this->pdf->setAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

		// loopje over Grootboekrekeningen Opbrengsten = 1
		$query = "SELECT Fondsen.Omschrijving, ".
		"Fondsen.Fondseenheid, ".
		"Rekeningmutaties.Boekdatum, ".
		"Rekeningmutaties.id,
		Rekeningmutaties.Transactietype,
		Rekeningmutaties.Valuta,
		 Rekeningmutaties.Afschriftnummer,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, Rekeningmutaties.Fonds,  ".
		"Rekeningmutaties.Fondskoers, ".
		"Rekeningmutaties.Debet as Debet, ".
		"Rekeningmutaties.Credit as Credit, ".
		"Rekeningmutaties.Valutakoers,
		 1 $koersQuery as Rapportagekoers,
		 BeleggingscategoriePerFonds.Beleggingscategorie,
     Beleggingscategorien.Omschrijving as BeleggingscategorieOmschrijving,
      Rekeningmutaties.bankTransactieId,
      if(Fondsen.fondssoort='OBL','Obligaties','Aandelen') as soort ".
		"FROM Rekeningmutaties
		JOIN Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds  
		JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening 
		JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
		JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening 
		JOIN BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '". $this->pdf->portefeuilledata['Vermogensbeheerder']."'
    JOIN Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie ".
		"WHERE ".
		"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		"Rekeningmutaties.Verwerkt = '1' AND ".
		"Rekeningmutaties.Transactietype <> 'B' AND ".
		"Grootboekrekeningen.FondsAanVerkoop = '1' AND ".
		"Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
		"Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
		"ORDER BY soort,Rekeningmutaties.Transactietype,Rekeningmutaties.Boekdatum,Rekeningmutaties.Fonds,Rekeningmutaties.Valuta,  Rekeningmutaties.id";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		// haal koersresultaat op om % te berekenen

		$koersresultaat = gerealiseerdKoersresultaat($this->portefeuille,$this->rapportageDatumVanaf, $this->rapportageDatum,$this->pdf->rapportageValuta,true,'Totaal',false);
		$transactietypen = array();

		$buffer = array();

		while($mutaties = $DB->nextRecord())
		{

			if ($this->pdf->rapportageValuta != "EUR" )
			{
				if($mutaties['Valuta']==$this->pdf->rapportageValuta)
					$koers=$mutaties['Valutakoers'];
				else
					$koers = getValutaKoers($this->pdf->rapportageValuta,$mutaties['Boekdatum']);
			}
			else
				$koers = 1;

			if($mutaties['bankTransactieId']=='')
				$filter=" AND Fonds='".mysql_real_escape_string($mutaties['Fonds'])."' ";
			else
				$filter=" AND bankTransactieId='".$mutaties['bankTransactieId']."' ";

			$query="SELECT SUM(Debet*Valutakoers) as Debet, SUM(Credit*Valutakoers) as Credit, Grootboekrekening 
              FROM Rekeningmutaties 
              JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
              WHERE Rekeningen.Portefeuille = '".$this->portefeuille."' AND Boekdatum='".$mutaties['Boekdatum']."' $filter AND Grootboekrekening <> 'FONDS'
              GROUP BY Grootboekrekening";
			$db2->SQL($query);
			$db2->Query();
			$transData=array();
			while($transDbData=$db2->nextRecord())
			{
				if($transDbData['Grootboekrekening']=='KOST' || $transDbData['Grootboekrekening']=='KOBU')
					$categorie='kosten';
				elseif($transDbData['Grootboekrekening']=='RENTE' || $transDbData['Grootboekrekening']=='RENME' || $transDbData['Grootboekrekening']=='RENOB')
					$categorie='rente';
				elseif($transDbData['Grootboekrekening']=='ROER'||$transDbData['Grootboekrekening']=='TOB' )
					$categorie='belasting';
				else
					$categorie=$transDbData['Grootboekrekening'];

				$transData[$categorie]+=($transDbData['Credit']-$transDbData['Debet'])/$koers;
				$transData['totaal']+=($transDbData['Credit']-$transDbData['Debet'])/$koers;
			}
			$mutaties['transData']=$transData;
			$mutaties['valutaKoersRegel']=$koers;
			$buffer[] = $mutaties;
		}
		$transactietypenOmschrijving=array('A'=>'Aankoop','A/O'=>'Aankoop / openen','A/S'=>'Aankoop / sluiten','D'=>'Deponering','L'=>'Lichting','V'=>'Verkoop','V/O'=>'Verkoop / openen','V/S'=>'Verkoop / sluiten');
		$categorieTotalen=array();
		$totalen=array();
		foreach ($buffer as $mutaties)
		{

      $koers=$mutaties['valutaKoersRegel'];
			//if($mutaties[Transactietype] != "A/S")
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

			$t_aankoop_koers=0;
      $t_aankoop_waardeinValuta=0;
			$t_aankoop_waarde=0;
			$t_verkoop_koers=0;
			$t_verkoop_waardeinValuta=0;
			$t_verkoop_waarde=0;



			switch($mutaties['Transactietype'])
			{
					case "A" :
						// Aankoop
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
						$t_aankoop_koers					= $mutaties[Fondskoers];

					//	$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $this->formatGetal($t_aankoop_koers, 2);
						if($t_aankoop_waardeinValuta > 0)
							$aankoop_waardeinValuta = $this->formatGetal($t_aankoop_waardeinValuta,$this->pdf->rapport_TRANS_decimaal);
						if($t_aankoop_koers > 0)
							$aankoop_waarde 				= $this->formatGetal($t_aankoop_waarde,$this->pdf->rapport_TRANS_decimaal);
					break;
					case "A/O" :
						// Aankoop / openen
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
						$t_aankoop_koers					= $mutaties['Fondskoers'];

				//		$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $this->formatGetal($t_aankoop_koers,2);
						if($t_aankoop_waardeinValuta > 0)
							$aankoop_waardeinValuta = $this->formatGetal($t_aankoop_waardeinValuta,$this->pdf->rapport_TRANS_decimaall);
						if($t_aankoop_koers > 0)
							$aankoop_waarde 				= $this->formatGetal($t_aankoop_waarde,$this->pdf->rapport_TRANS_decimaal);
					break;
					case "A/S" :
						// Aankoop / sluiten
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
						$t_aankoop_koers					= $mutaties['Fondskoers'];

					//	$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $this->formatGetal($t_aankoop_koers,2);
						if($t_aankoop_waardeinValuta > 0)
							$aankoop_waardeinValuta = $this->formatGetal($t_aankoop_waardeinValuta,$this->pdf->rapport_TRANS_decimaal);
						if($t_aankoop_koers > 0)
							$aankoop_waarde 				= $this->formatGetal($t_aankoop_waarde,$this->pdf->rapport_TRANS_decimaal);

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

					//	$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $this->formatGetal($t_aankoop_koers,2);
						if($t_aankoop_waardeinValuta > 0)
							$aankoop_waardeinValuta = $this->formatGetal($t_aankoop_waardeinValuta,$this->pdf->rapport_TRANS_decimaal);
						if($t_aankoop_waarde > 0)
							$aankoop_waarde 				= $this->formatGetal($t_aankoop_waarde,$this->pdf->rapport_TRANS_decimaal);
					break;
					case "L" :
							// Lichting
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
						$t_verkoop_koers					= $mutaties['Fondskoers'];

					//	$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $this->formatGetal($t_verkoop_koers,2);
						if($t_verkoop_waardeinValuta > 0)
							$verkoop_waardeinValuta = $this->formatGetal($t_verkoop_waardeinValuta,$this->pdf->rapport_TRANS_decimaal);
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $this->formatGetal($t_verkoop_waarde,$this->pdf->rapport_TRANS_decimaal);
					break;
					case "V" :
							// Verkopen
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
				//		echo " $t_verkoop_waarde 				= abs(".$mutaties[Credit].") * ".$mutaties[Valutakoers]."  * ".$mutaties['Rapportagekoers']."  ";
						$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
						$t_verkoop_koers					= $mutaties['Fondskoers'];

					//	$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $this->formatGetal($t_verkoop_koers,2);
						if($t_verkoop_waardeinValuta > 0)
							$verkoop_waardeinValuta = $this->formatGetal($t_verkoop_waardeinValuta,$this->pdf->rapport_TRANS_decimaal);
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $this->formatGetal($t_verkoop_waarde,$this->pdf->rapport_TRANS_decimaal);
					break;
					case "V/O" :
							// Verkopen / openen
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
						$t_verkoop_koers					= $mutaties['Fondskoers'];

				//		$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $this->formatGetal($t_verkoop_koers,2);
						if($t_verkoop_waardeinValuta > 0)
							$verkoop_waardeinValuta = $this->formatGetal($t_verkoop_waardeinValuta,$this->pdf->rapport_TRANS_decimaal);
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $this->formatGetal($t_verkoop_waarde,$this->pdf->rapport_TRANS_decimaal);
					break;
					case "V/S" :
					 		// Verkopen / sluiten
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
						$t_verkoop_koers					= $mutaties['Fondskoers'];

				//		$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $this->formatGetal($t_verkoop_koers,2);
						if($t_verkoop_waardeinValuta > 0)
							$verkoop_waardeinValuta = $this->formatGetal($t_verkoop_waardeinValuta,$this->pdf->rapport_TRANS_decimaal);
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $this->formatGetal($t_verkoop_waarde,$this->pdf->rapport_TRANS_decimaal);
					break;
					default :
								$_error = "Fout ongeldig tranactietype!!";
					break;
			}

			/*
				Alleen resultaat berekenen bij "Sluiten", niet bij "Openen".
			*/

			if(	$mutaties['Transactietype'] == "L" ||
					$mutaties['Transactietype'] == "V" ||
					$mutaties['Transactietype'] == "V/S" ||
					$mutaties['Transactietype'] == "A/S")
			{


					$historie = berekenHistorischKostprijs($this->portefeuille, $mutaties['Fonds'], $mutaties['Boekdatum'], $this->pdf->rapportageValuta, '',$mutaties['id']);
					if ($mutaties['Transactietype'] == "A/S")
					{
						$historischekostprijs = ($mutaties['Aantal'] * -1) * $historie['historischeWaarde'] * $historie['historischeValutakoers'] * $mutaties['Fondseenheid'];
						$beginditjaar = ($mutaties['Aantal'] * -1) * $historie['beginwaardeLopendeJaar'] * $historie['beginwaardeValutaLopendeJaar'] * $mutaties['Fondseenheid'];
					}
					else
					{
						$historischekostprijs = $mutaties['Aantal'] * $historie['historischeWaarde'] * $historie['historischeValutakoers'] * $mutaties['Fondseenheid'];
						$beginditjaar = $mutaties['Aantal'] * $historie['beginwaardeLopendeJaar'] * $historie['beginwaardeValutaLopendeJaar'] * $mutaties['Fondseenheid'];
						//echo 	"<br>\n$beginditjaar = ".$mutaties['Aantal']." * ".$historie['beginwaardeLopendeJaar']." * ".$historie['beginwaardeValutaLopendeJaar']." * ".$mutaties['Fondseenheid']."<br>\n";
					}
					if ($this->pdf->rapportageValuta != 'EUR' && $mutaties['Valuta'] == $this->pdf->rapportageValuta)
					{
						$historischekostprijs = $historischekostprijs / $historie['historischeValutakoers'];
						$beginditjaar = $beginditjaar / getValutaKoers($this->pdf->rapportageValuta, date("Y", db2jul($this->rapportageDatum) . '-01-01'));
						//echo $beginditjaar;
					}
					elseif ($this->pdf->rapportageValuta != 'EUR')
					{
						$historischekostprijs = $historischekostprijs / $historie['historischeRapportageValutakoers'];
						$beginditjaar = $beginditjaar / getValutaKoers($this->pdf->rapportageValuta, date("Y", db2jul($this->rapportageDatum) . '-01-01'));
					}

					if ($historie['voorgaandejarenActief'] == 0)
					{
						$resultaatvoorgaande = 0;
						$resultaatlopende = $t_verkoop_waarde - $historischekostprijs;
						if ($mutaties['Transactietype'] == "A/S")
						{
							$resultaatvoorgaande = 0;
							$resultaatlopende = $t_aankoop_waarde - $historischekostprijs;
						}
					}
					else
					{
						$resultaatvoorgaande = $beginditjaar - $historischekostprijs;
						$resultaatlopende = $t_verkoop_waarde - $beginditjaar;
						//echo $historie['fondsOmschrijving']."<br>\n";
						//echo "resultaatvoorgaande: $resultaatvoorgaande = $beginditjaar - $historischekostprijs;<br>\n";ob_flush();
            //echo "resultaatlopende: $resultaatlopende = $t_verkoop_waarde - $beginditjaar;<br>\n";ob_flush();
						if ($mutaties['Transactietype'] == "A/S")
						{
							$resultaatvoorgaande = $beginditjaar - $historischekostprijs;
							$resultaatlopende = ($t_aankoop_waarde * -1) - $beginditjaar;
						}
					}

					$result_historischkostprijs = $this->formatGetal($historischekostprijs, $this->pdf->rapport_TRANS_decimaal);
					$result_voorgaandejaren = $this->formatGetal($resultaatvoorgaande, $this->pdf->rapport_TRANS_decimaal2);
					$result_lopendejaar = $this->formatGetal($resultaatlopende, $this->pdf->rapport_TRANS_decimaal2);
					$result_lopendeTotaal = $resultaatlopende ;//+ $resultaatvoorgaande;

					//	$totaal_resultaat_waarde += $resultaatlopende;
		
			}
			else
			{
				$result_historischkostprijs = "";
				$result_voorgaandejaren = "";
				$result_lopendejaar = "";
				$historischekostprijs=0;
		  	$resultaatvoorgaande=0;
		  	$percentageTotaal=0;
				$result_lopendeTotaal=0;
			}


			$this->pdf->setX($this->pdf->marge);
			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
			// % van totaal
			if($this->pdf->rapport_TRANS_procent == 1)
			{
				$percentageTotaal = ABS(($resultaatlopende / ($resultaatvoorgaande + $historischekostprijs)) *100);

				if($resultaatlopende < 0)
					$percentageTotaal = (-1*$percentageTotaal);

				if($percentageTotaal <>0)
				{
					if($percentageTotaal > 1000 || $percentageTotaal < -1000)
						$percentageTotaalTekst = "p.m.";
					else
						$percentageTotaalTekst = $this->formatGetal($percentageTotaal,1);

				}
				else
					$percentageTotaalTekst = "";
			}


			$kop=vertaalTekst($mutaties['soort'],$this->pdf->rapport_taal)." ".vertaalTekst($transactietypenOmschrijving[$mutaties['Transactietype']],$this->pdf->rapport_taal);

			if(!isset($lastKop) ||  $kop <> $lastKop)
			{
				if(count($categorieTotalen) > 0)
				{
					$this->addTotaal($categorieTotalen);
				}
				$this->addHeader($kop);
				$categorieTotalen=array();
			}
			elseif ($this->pdf->getY()+$this->pdf->rowHeight > $this->pdf->pagebreak)
			{
				$this->pdf->addPage();
				$this->addHeader($kop);
			}
			$lastKop=$kop;



      $datum=date("d-m-Y",db2jul($mutaties['Boekdatum']));
      $bruto=($mutaties['Credit']-$mutaties['Debet'])*$mutaties['Valutakoers']/$koers;
		  $this->pdf->row(array($mutaties['Omschrijving'],
													$this->formatGetal($mutaties['Aantal'],0),
													$this->formatGetal($mutaties['Fondskoers'],2),
													$mutaties['Valuta'],
													$this->formatGetal($mutaties['Valutakoers']/$koers,4),
													$this->formatGetal($bruto,2),
													$this->formatGetal(($mutaties['transData']['kosten']),2),
                          $this->formatGetal(($mutaties['transData']['belasting']),2),
													$this->formatGetal(($mutaties['transData']['rente']),2),
													$this->formatGetal(($bruto+$mutaties['transData']['totaal']),2),
													$this->formatGetal($result_lopendeTotaal,2),
													$datum));
			$categorieTotalen['bruto']+=$bruto;
			$categorieTotalen['kosten']+=$mutaties['transData']['kosten'];
			$categorieTotalen['rente']+=$mutaties['transData']['rente'];
			$categorieTotalen['belasting']+=$mutaties['transData']['belasting'];
			$categorieTotalen['netto']+=($bruto+$mutaties['transData']['totaal']);
			$categorieTotalen['resultaat']+=$result_lopendeTotaal;

			$totalen['bruto']+=$bruto;
			$totalen['kosten']+=$mutaties['transData']['kosten'];
			$totalen['rente']+=$mutaties['transData']['rente'];
			$totalen['belasting']+=$mutaties['transData']['belasting'];
			$totalen['netto']+=($bruto+$mutaties['transData']['totaal']);
			$totalen['resultaat']+=$result_lopendeTotaal;
/*
					$this->pdf->excelData[]=array(date("d-m",db2jul($mutaties['Boekdatum'])),
											$mutaties['Transactietype'],
											round($mutaties['Aantal'],0),
											$mutaties['Omschrijving'],
                      $t_aankoop_koers,
                      $t_aankoop_waardeinValuta,
											$t_aankoop_waarde,
											$t_verkoop_koers,
											$t_verkoop_waardeinValuta,
											$t_verkoop_waarde,
											$historischekostprijs,
											$resultaatvoorgaande,
											$resultaatlopende,
											$percentageTotaal);
*/
			$this->pdf->excelData[]=array($mutaties['Omschrijving'],
				                $mutaties['Transactietype'],
												round($mutaties['Aantal'],0),
												round($mutaties['Fondskoers'],2),
												$mutaties['Valuta'],
												round($mutaties['Valutakoers']/$koers,4),
				                round($bruto,2),
				                round(($mutaties['transData']['kosten']),2),
                        round(($mutaties['transData']['belasting']),2),
				                round(($mutaties['transData']['rente']),2),
				                round(($bruto+$mutaties['transData']['totaal']),2),
			                 	round($result_lopendeTotaal,2),
												$datum);


			$transactietypen[] = $mutaties['Transactietype'];
		}
		$this->addTotaal($categorieTotalen,true);
		$this->pdf->ln(1);
		$this->addTotaal($totalen);


		if($this->pdf->rapport_TRANS_legenda == 1)
		{
			$this->pdf->ln();

			$transactietypen = array_unique($transactietypen);
			sort($transactietypen);

			$hoogte = (count($transactietypen) * 4) ;
			if(($this->pdf->GetY() + $hoogte + 8) >= $this->pdf->pagebreak) {
				$this->pdf->AddPage();
				$this->pdf->ln();
			}

			$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
			//$this->pdf->SetX($this->pdf->marge + $this->pdf->widthB[0]);
			$this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),110,$hoogte,'F');
			$this->pdf->SetFillColor(0);
			$this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),110,$hoogte);
			//$this->pdf->SetX($this->pdf->marge);
			$this->pdf->SetX($this->pdf->marge);

			// kopfontcolor

			$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
			$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

			reset($transactietypen);

	 		foreach($transactietypen as  $key=>$val)
   		{
				$this->pdf->Cell(30,4, $val, 0,0, "L");
				$this->pdf->Cell(80,4, vertaalTekst($transactietypenOmschrijving[$val],$this->pdf->rapport_taal), 0,1, "L");
			}
		}

		if(isset($this->pdf->rapport_TRANS_disclaimerText))
		{
		  $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize-2);
		  $this->pdf->MultiCell(280,4, $this->pdf->rapport_TRANS_disclaimerText, 0, "L");
		  $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
		}

		if($this->pdf->getY()<187)
			$this->pdf->setY(187);
		$this->pdf->ln();
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize-2.5);
		$this->pdf->MultiCell(280,4, 'Box Consultants B.V. bevestigt hierbij dat alle uitgevoerde effectentransacties geschikt en passend zijn bij de beleggingsdoelstelling, financiële situatie en kennis en ervaring. Het gehanteerde beleggingsbeleid is passend bij uw risicoprofiel en eventuele ESG-voorkeuren.', 0, "L");
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);


	}
}
?>

<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/18 17:44:11 $
File Versie					: $Revision: 1.24 $

$Log: RapportMUT_L72.php,v $
Revision 1.24  2020/03/18 17:44:11  rvv
*** empty log message ***

Revision 1.23  2018/12/14 16:43:21  rvv
*** empty log message ***

Revision 1.22  2018/12/05 16:36:17  rvv
*** empty log message ***

Revision 1.21  2018/11/28 13:18:46  rvv
*** empty log message ***

Revision 1.20  2018/11/17 17:34:53  rvv
*** empty log message ***

Revision 1.19  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.18  2017/07/09 11:57:36  rvv
*** empty log message ***

Revision 1.17  2017/03/01 17:17:08  rvv
*** empty log message ***

Revision 1.16  2017/02/22 17:15:06  rvv
*** empty log message ***

Revision 1.15  2017/02/19 09:21:57  rvv
*** empty log message ***

Revision 1.14  2017/02/15 15:52:40  rvv
*** empty log message ***

Revision 1.13  2017/02/15 13:29:02  rvv
*** empty log message ***

Revision 1.12  2017/02/15 13:03:09  rvv
*** empty log message ***

Revision 1.11  2017/02/11 17:30:10  rvv
*** empty log message ***

Revision 1.10  2017/02/08 12:32:32  rvv
*** empty log message ***

Revision 1.9  2017/01/21 17:47:43  rvv
*** empty log message ***

Revision 1.8  2016/12/30 15:31:00  rvv
*** empty log message ***

Revision 1.7  2016/12/14 14:35:30  rvv
*** empty log message ***

Revision 1.6  2016/12/14 14:00:06  rvv
*** empty log message ***

Revision 1.5  2016/12/14 12:12:58  rvv
*** empty log message ***

Revision 1.4  2016/12/11 15:20:35  rvv
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

class RapportMUT_L72
{
	function RapportMUT_L72($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "MUT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Mutatie-overzicht";

		if ($this->pdf->rapportageValuta != 'EUR' && $this->pdf->rapportageValuta != '')
		  $this->pdf->rapport_titel .= " in ".$this->pdf->rapportageValuta;

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;


	  $this->pdf->excelData[]=array("Omschrijving",'Grootboekrekening',"Val.","Val.Koers.","Bruto","Kosten","Belasting","Rente","Netto","Datum");

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
		$this->pdf->CellBorders = array(array('T','U','L'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_mut_headercolor['r'],$this->pdf->rapport_mut_headercolor['g'],$this->pdf->rapport_mut_headercolor['b']);
		$this->pdf->row(array(vertaalTekst($categorie,$this->pdf->rapport_taal),'','','','','','','','','','',''));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array(array('U','L'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U','R'));
		$this->pdf->row(array(vertaalTekst("Omschrijving",$this->pdf->rapport_taal),
									    vertaalTekst("Val.",$this->pdf->rapport_taal),
											vertaalTekst("Val.Koers",$this->pdf->rapport_taal),
											vertaalTekst("Bruto",$this->pdf->rapport_taal),
											vertaalTekst("Kosten",$this->pdf->rapport_taal),
											vertaalTekst('Belasting',$this->pdf->rapport_taal),
											vertaalTekst('Rente',$this->pdf->rapport_taal),
											vertaalTekst("Netto",$this->pdf->rapport_taal),
											vertaalTekst("Datum",$this->pdf->rapport_taal)));
		$this->pdf->SetTextColor(0);
		unset($this->pdf->CellBorders);
		$this->pdf->rowHeight=$rowHeightBackup;
		$this->pdf->ln(1);
	}

	function addTotaal($totaal,$addU=false)
	{
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array('','','',array('T'),array('T'),array('T'),array('T'),array('T'),'');
		if($addU==true)
			$this->pdf->CellBorders = array('','','',array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),'');

		$this->pdf->row(array('',
											'',
											'',
											$this->formatGetal($totaal['bruto'],2),
											$this->formatGetal($totaal['kosten'],2),
											$this->formatGetal($totaal['belasting'],2),
											$this->formatGetal($totaal['rente'],2),
											$this->formatGetal($totaal['netto'],2),
											''));
		unset($this->pdf->CellBorders);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

	}
	function writeRapport()
	{



	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		$DB = new DB();
		$db2 = new DB();

		// voor data
		$this->pdf->widthB = array(90,20,27,25,23,23,23,23,23);//108/2
		$this->pdf->alignB = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');
		// voor kopjes
		$this->pdf->widthA = $this->pdf->widthB;
		$this->pdf->alignA = $this->pdf->alignB;


		if($this->pdf->rapport_MUT_kwartaal == 1 && ($this->pdf->selectData[backoffice] == true) )
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

		foreach ($this->pdf->lastPOST as $key=>$value)
		{
			if(substr($key,0,4)=='MUT_' && $value==1)
			{
				$grootboeken[]=substr($key,4);
				$filter = 1;
			}
		}

		if($filter == 1)
		{
			$grootboekSelectie = implode('\',\'',$grootboeken);
			$extraquery .= "AND Rekeningmutaties.Grootboekrekening IN('$grootboekSelectie')  ";
		}

		// loopje over Grootboekrekeningen Opbrengsten = 1
		$query = "SELECT Rekeningmutaties.Boekdatum, ".
		"Rekeningmutaties.Transactietype,
		Rekeningmutaties.Grootboekrekening,
		Rekeningmutaties.Valuta,
		 Rekeningmutaties.Afschriftnummer,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, Rekeningmutaties.Fonds,  ".
		"Rekeningmutaties.Fondskoers, ".
		"Rekeningmutaties.Debet as Debet, ".
		"Rekeningmutaties.Credit as Credit, ".
		"Rekeningmutaties.Valutakoers,
		 1 $koersQuery as Rapportagekoers,
     Rekeningmutaties.bankTransactieId ,
IF (
	Rekeningmutaties.Grootboekrekening IN ('DIV'),
	'1-Dividenden',
IF (
	Rekeningmutaties.Grootboekrekening IN ('RENOB'),
	'2-Coupons',
IF (
	Grootboekrekeningen.Kosten = 1,
	'3-Kosten',

IF (
	Rekeningmutaties.Grootboekrekening IN ('STORT', 'ONTTR', 'KRUIS'),
	concat(
		'4-',
		Rekeningmutaties.Grootboekrekening
	),
IF (
	Rekeningmutaties.Grootboekrekening IN ('RENTE'),
	concat(
		'5-',
		Rekeningmutaties.Grootboekrekening
	),
	'9-overige'
)
)
)
)
) AS soort,
 Rekeningmutaties.Grootboekrekening,
 Grootboekrekeningen.Omschrijving as grootboekOmschrijving
      ".
		"FROM Rekeningmutaties
		JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening 
		JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
		JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening ".
		"WHERE ".
		"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		"Rekeningmutaties.Verwerkt = '1' AND ".
		"Rekeningmutaties.Transactietype <> 'B' AND ".
		"Grootboekrekeningen.FondsAanVerkoop ='0' AND ". //Rekeningmutaties.Fonds='' AND
		"Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
		"Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' $extraquery".
		"ORDER BY soort,Rekeningmutaties.Boekdatum, Rekeningmutaties.id";
		$DB = new DB();
    $DB2 = new DB();
		$DB->SQL($query);
		$DB->Query();

		// haal koersresultaat op om % te berekenen

		//$koersresultaat = gerealiseerdKoersresultaat($this->portefeuille,$this->rapportageDatumVanaf, $this->rapportageDatum,$this->pdf->rapportageValuta);
		$transactietypen = array();

		$buffer = array();
    $soortVertaling=array('4-STORT'=>'Stortingen','4-ONTTR'=>'Onttrekkingen','4-Kruis'=>'Kruisposten','4-KRUIS'=>'Kruisposten');
		$fondsFilter=false;
		$fondsFilterSql='';
		while($mutaties = $DB->nextRecord())
		{
      $valutaKoers=0;
			if ($this->pdf->rapportageValuta != "EUR" )
			{
				if($mutaties['Valuta']==$this->pdf->rapportageValuta )//|| $mutaties['Grootboekrekening']=='KRUIS'
					$koers=$mutaties['Valutakoers'];
				else
				{
     
					if($mutaties['Valuta']=='EUR' && strtolower($mutaties['Grootboekrekening'])=='kruis')
					{
					   $query="SELECT Rekeningmutaties.Valutakoers FROM Rekeningmutaties
             JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening WHERE
             Rekeningen.Portefeuille = '".$this->portefeuille."' AND Grootboekrekening='".$mutaties['Grootboekrekening']."' AND Rekeningmutaties.Valuta<>'EUR' AND Boekdatum='".$mutaties['Boekdatum'] ."' AND bankTransactieId='".$mutaties['bankTransactieId'] ."' ";
            $DB2->SQL($query);
            $DB2->Query();
            $valutaKoers=$DB2->nextRecord();
            $valutaKoers=$valutaKoers['Valutakoers'];
            //listarray($valutaKoers); ob_flush();
      
					}
          if($valutaKoers<>0)
					{
						$koers=$valutaKoers;
					}
					else
          {
            $koers = getValutaKoers($this->pdf->rapportageValuta, $mutaties['Boekdatum']);
          }
				}
			}
			else
				$koers = 1;
      
     // listarray($mutaties);
     // listarray($koers);
      
			$soortParts=explode("-",$mutaties['soort']);
			$soortId=$soortParts[0];
			if($soortId >=3)
			{
				$fondsFilter = true;
				$fondsFilterSql=" AND Rekeningmutaties.Fonds = ''";
			}
			else
			{
				if($mutaties['Fonds']<>'')
				  $fondsFilterSql = " AND Rekeningmutaties.Fonds = '".mysql_real_escape_string($mutaties['Fonds'])."'";
			}
			if($fondsFilter==true && $mutaties['Fonds'] <> '')
				continue;
		//	listarray($mutaties);
			if($mutaties['soort']=='9-overige')
				continue;

			$query="SELECT SUM(Debet*Valutakoers) as Debet, SUM(Credit*Valutakoers) as Credit, Valutakoers, Grootboekrekening 
              FROM Rekeningmutaties 
              JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
              WHERE Rekeningen.Portefeuille = '".$this->portefeuille."' AND 
              Boekdatum='".$mutaties['Boekdatum']."' AND bankTransactieId='".$mutaties['bankTransactieId']."' AND Grootboekrekening <> 'FONDS' $fondsFilterSql
              GROUP BY Grootboekrekening";
			$db2->SQL($query);
			$db2->Query();
			$transData=array();
			while($transDbData=$db2->nextRecord())
			{//listarray($transDbData);


				if($transDbData['Grootboekrekening']=='KOST' || $transDbData['Grootboekrekening']=='KOBU')
					$categorie='kosten';
				elseif($transDbData['Grootboekrekening']=='RENTE')
					$categorie='rente';
				elseif($transDbData['Grootboekrekening']=='DIVBE' )
					$categorie='belasting';
				else
					$categorie=$transDbData['Grootboekrekening'];

				if($mutaties['soort']=='3-Kosten'&& $categorie=='kosten')
					continue;

				//if($transDbData['Grootboekrekening']=='KRUIS')
				//	$koers=1;//$transDbData['Valutakoers'];

				$transData[$categorie]+=($transDbData['Credit']-$transDbData['Debet'])/$koers;
				$transData['totaal']+=($transDbData['Credit']-$transDbData['Debet'])/$koers;
			}


			$mutaties['valutaKoersRegel']=$koers;
			$mutaties['transData']=$transData;
			$buffer[] = $mutaties;
		}
		$transactietypenOmschrijving=array('A'=>'Aankoop','A/O'=>'Aankoop / openen','A/S'=>'Aankoop / sluiten','D'=>'Deponering','L'=>'Lichting','V'=>'Verkoop','V/O'=>'Verkoop / openen','V/S'=>'Verkoop / sluiten');
		$categorieTotalen=array();
		$totalen=array();
		foreach ($buffer as $mutaties)
		{
			$koers=$mutaties['valutaKoersRegel'];

			if(isset($soortVertaling[$mutaties['soort']]))
				$kop=$soortVertaling[$mutaties['soort']];
			else
			  $kop=substr($mutaties['soort'],2); //	$kop=$mutaties['grootboekOmschrijving'];

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
		 // if($mutaties['soort']=='3-Kosten')
		 // 	$netto=$bruto;
		//	else
		  	$netto=$bruto+$mutaties['transData']['kosten']+$mutaties['transData']['belasting'];
			
		  $this->pdf->row(array($mutaties['rekeningOmschrijving'],
													$mutaties['Valuta'],
													$this->formatGetal($mutaties['Valutakoers']/$koers,4),
													$this->formatGetal($bruto,2),
													$this->formatGetal(($mutaties['transData']['kosten']),2),
													$this->formatGetal(($mutaties['transData']['belasting']),2),
									  			$this->formatGetal(($mutaties['transData']['rente']),2),
												  $this->formatGetal(($netto),2),
													$datum));
			$categorieTotalen['bruto']+=$bruto;
			$categorieTotalen['kosten']+=$mutaties['transData']['kosten'];
			$categorieTotalen['rente']+=$mutaties['transData']['rente'];
			$categorieTotalen['belasting']+=$mutaties['transData']['belasting'];
			$categorieTotalen['netto']+=$netto;
			$categorieTotalen['resultaat']+=$result_lopendeTotaal;

			$totalen['bruto']+=$bruto;
			$totalen['kosten']+=$mutaties['transData']['kosten'];
			$totalen['rente']+=$mutaties['transData']['rente'];
			$totalen['belasting']+=$mutaties['transData']['belasting'];
			$totalen['netto']+=$netto;//$bruto+$mutaties['transData']['totaal'];
			$totalen['resultaat']+=$result_lopendeTotaal;

					$this->pdf->excelData[]=array($mutaties['rekeningOmschrijving'],
						$mutaties['Grootboekrekening'],
            $mutaties['Valuta'],
            round($mutaties['Valutakoers']/$koers,4),
            round($bruto,2),
            round(($mutaties['transData']['kosten']),2),
            round(($mutaties['transData']['belasting']),2),
            round(($mutaties['transData']['rente']),2),
            round(($netto),2),
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

			$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor[r],$this->pdf->rapport_kop_bgcolor[g],$this->pdf->rapport_kop_bgcolor[b]);
			//$this->pdf->SetX($this->pdf->marge + $this->pdf->widthB[0]);
			$this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),110,$hoogte,'F');
			$this->pdf->SetFillColor(0);
			$this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),110,$hoogte);
			//$this->pdf->SetX($this->pdf->marge);
			$this->pdf->SetX($this->pdf->marge);

			// kopfontcolor

			$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[r],$this->pdf->rapport_kop_fontcolor[g],$this->pdf->rapport_kop_fontcolor[b]);
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

	}
}
?>

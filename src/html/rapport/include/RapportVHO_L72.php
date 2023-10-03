<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/11/09 16:40:36 $
File Versie					: $Revision: 1.30 $

$Log: RapportVHO_L72.php,v $
Revision 1.30  2019/11/09 16:40:36  rvv
*** empty log message ***

Revision 1.29  2018/12/12 16:19:08  rvv
*** empty log message ***

Revision 1.28  2017/08/16 15:58:06  rvv
*** empty log message ***

Revision 1.27  2017/07/22 18:22:05  rvv
*** empty log message ***

Revision 1.26  2017/05/26 16:45:07  rvv
*** empty log message ***

Revision 1.25  2017/04/15 19:11:50  rvv
*** empty log message ***

Revision 1.24  2017/04/12 08:33:15  rvv
*** empty log message ***

Revision 1.23  2017/04/05 15:39:45  rvv
*** empty log message ***

Revision 1.22  2017/03/29 15:57:04  rvv
*** empty log message ***

Revision 1.21  2017/03/22 16:53:22  rvv
*** empty log message ***

Revision 1.20  2017/03/08 16:51:39  rvv
*** empty log message ***

Revision 1.19  2017/02/18 17:32:08  rvv
*** empty log message ***

Revision 1.18  2017/02/15 15:52:40  rvv
*** empty log message ***

Revision 1.17  2017/02/15 11:25:53  rvv
*** empty log message ***

Revision 1.16  2017/02/08 13:44:17  rvv
*** empty log message ***

Revision 1.15  2017/02/08 12:32:32  rvv
*** empty log message ***

Revision 1.14  2017/02/01 08:58:17  rvv
*** empty log message ***

Revision 1.13  2017/01/29 10:25:25  rvv
*** empty log message ***

Revision 1.12  2017/01/18 17:02:28  rvv
*** empty log message ***

Revision 1.11  2017/01/11 17:12:46  rvv
*** empty log message ***

Revision 1.10  2017/01/07 16:23:16  rvv
*** empty log message ***

Revision 1.9  2016/12/28 19:38:27  rvv
*** empty log message ***

Revision 1.8  2016/12/21 16:33:56  rvv
*** empty log message ***

Revision 1.7  2016/12/10 10:36:55  rvv
*** empty log message ***

Revision 1.6  2016/11/30 12:26:19  rvv
*** empty log message ***

Revision 1.5  2016/11/27 18:07:45  rvv
*** empty log message ***

Revision 1.4  2016/11/27 11:09:00  rvv
*** empty log message ***

Revision 1.1  2016/11/12 20:21:18  rvv
*** empty log message ***

Revision 1.1  2016/09/28 15:53:55  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/rapportATTberekening.php");
include_once("rapport/include/ATTberekening_L72.php");

class RapportVHO_L72
{
	function RapportVHO_L72($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VHO";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Vermogensoverzicht";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		if($this->pdf->rapportageValuta<>''&& $this->pdf->rapportageValuta<>'EUR')
			$this->currencySign=$this->pdf->rapportageValuta;
		else
			$this->currencySign='€';
		$this->pdf->excelData[]=array('Naam Fonds','Aantal','Valuta','Koers','Marktwaarde','Kostprijs','Resultaat ('.$this->currencySign.')','Resultaat (%)','Weging','Depot');
    $this->DB = new DB();
	}

	function formatGetal($waarde, $dec)
	{
		if($waarde==0)
			return '';
		else
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

	function addHeader($categorie,$belcat='')
	{
		$rowHeightBackup=$this->pdf->rowHeight;
		$this->pdf->rowHeight=$rowHeightBackup*1.5;
		$this->pdf->CellBorders = array(array('T','U','L'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
		$this->pdf->row(array(vertaalTekst($categorie,$this->pdf->rapport_taal),'','','','','','','','',''));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$backupAligns=$this->pdf->aligns;
		$backupWidths=$this->pdf->widths;
		if($categorie=='Liquiditeiten'||$belcat=='VAL-TERM'||$belcat=='Spaar')
		{
			$this->pdf->widths[0]=$this->pdf->widths[0]-24;
			$this->pdf->widths[1]=$this->pdf->widths[1]+24;
			$this->pdf->aligns[1]='L';
			$naamFonds="Rekeningsoort";
			$aantal='IBAN';
			$resultaat=vertaalTekst('Saldo in VV',$this->pdf->rapport_taal);
		}else
		{
			$naamFonds="Naam Fonds";
			$aantal='Aantal';
			$resultaat=vertaalTekst('Resultaat',$this->pdf->rapport_taal).' ('.$this->currencySign.')';
		}

		$this->pdf->CellBorders = array(array('U','L'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U','R'));
		$this->pdf->row(array(vertaalTekst($naamFonds,$this->pdf->rapport_taal),
											vertaalTekst($aantal,$this->pdf->rapport_taal),
											vertaalTekst("Valuta",$this->pdf->rapport_taal),
											vertaalTekst("Koers",$this->pdf->rapport_taal),
											vertaalTekst("Marktwaarde",$this->pdf->rapport_taal),
											vertaalTekst("Kostprijs",$this->pdf->rapport_taal),
											$resultaat,
											vertaalTekst('Resultaat (%)',$this->pdf->rapport_taal),
											vertaalTekst("Weging",$this->pdf->rapport_taal),
											vertaalTekst("Depot",$this->pdf->rapport_taal)));
		$this->pdf->aligns=$backupAligns;
		$this->pdf->widths=$backupWidths;
		$this->pdf->SetTextColor(0);
		unset($this->pdf->CellBorders);
		$this->pdf->rowHeight=$rowHeightBackup;
		$this->pdf->ln(1);
	}


	function gemiddeldeTransactieKoersInValuta($fondsData)
	{
		$fonds=$fondsData['fonds'];
		$valutaKoers=$this->pdf->ValutaKoersBegin;
		if($fonds=='')
			return $this->pdf->ValutaKoersBegin;

		$DB = new DB();
		$query="SELECT Boekdatum,Aantal
     FROM Rekeningmutaties 
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening 
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND 
     Rekeningmutaties.Boekdatum <= '".	$this->rapportageDatum."' AND
     Rekeningmutaties.Fonds='$fonds' AND Grootboekrekening='FONDS'
     AND NOT (Rekeningmutaties.Transactietype = 'B' AND MONTH(Boekdatum)= 1 AND DAY(Boekdatum) = 1)";
		$DB->SQL($query);
		$DB->Query();
		$startDatum='1990-01-01';
		$totaalAantal=0;
		while($data = $DB->nextRecord())
		{
			$totaalAantal+=$data['Aantal'];
			if($totaalAantal==0)
				$startDatum=$data['Boekdatum'];
		}


		$query="SELECT Boekdatum,Debet,Credit,Bedrag,Omschrijving,Aantal,((Credit*Valutakoers)-(Debet*Valutakoers)) as BedragEur 
     FROM Rekeningmutaties 
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening 
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND 
     Rekeningmutaties.Boekdatum <= '".	$this->rapportageDatum."' AND Rekeningmutaties.Boekdatum >'$startDatum' AND
     Rekeningmutaties.Fonds='$fonds' AND Grootboekrekening='FONDS'
     AND NOT (Rekeningmutaties.Transactietype = 'B' AND MONTH(Boekdatum)= 1 AND DAY(Boekdatum) = 1) 
     AND Rekeningmutaties.Transactietype NOT IN('V','V/O','V/S','L')";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		$waardeEur=0;
		$waardeValuta=0;
		while($data = $DB->nextRecord())
		{
			$bedrag=$data['BedragEur']*-1;
			$valutaKoers=getValutaKoers($this->pdf->rapportageValuta,$data['Boekdatum']);
			$waardeEur+=$bedrag;
			//$waardeValuta+=$bedrag*$valutaKoers;
			$waardeValuta+=$bedrag/$valutaKoers;
		}
		$gemiddeldeValutaKoers=$waardeEur/$waardeValuta;
		return $gemiddeldeValutaKoers;
	}

	function writeRapport()
	{
		global $__appvar;

		$this->pdf->AddPage();
		$this->pdf->templateVars['VHOPaginas'] = $this->pdf->page;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		unset($this->pdf->CellBorders);
		$totalen['rente']=0;

    $query = "desc FondsExtraInformatie";
    $this->DB->SQL($query);
    $this->DB->query();
    $extraVeld='';
    while($data=$this->DB->nextRecord('num'))
    {
      if($data[0]=='KPWaardering')
        $extraVeld='FondsExtraInformatie.KPWaardering,';
    }
		$att=new ATTberekening_L72();
		$waarden=berekenPortefeuilleWaardeBewaarders($this->portefeuille, $this->rapportageDatum,false,$this->pdf->rapportageValuta,$this->rapportageDatumVanaf);
		vulTijdelijkeTabel($waarden,$this->portefeuille, $this->rapportageDatum);
		runPreProcessor($this->portefeuille);


		$query = "SELECT Vermogensbeheerders.VerouderdeKoersDagen , Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM (Portefeuilles, Clienten)  Join Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
		$maxDagenOud=$portefeuilledata['VerouderdeKoersDagen'];
		$dagVanWeek=date('w',$this->pdf->rapport_datum);
		if($dagVanWeek==6)
			$maxDagenOud+=1;
		elseif($dagVanWeek==0)
			$maxDagenOud+=2;
		$rapDatumTekst=date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum);


		$query="SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." as actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage WHERE
		TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille='".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek'];
		$DB->SQL($query); //echo $query."<br>\n";
		$DB->Query();
		$actueleWaarde = $DB->nextRecord();
		$portefeuilleWaarde=$actueleWaarde['actuelePortefeuilleWaardeEuro'];





		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$query="SELECT
$extraVeld
TijdelijkeRapportage.historischeValutakoers,
SUM(IF(TijdelijkeRapportage.type = 'fondsen',TijdelijkeRapportage.beginwaardeLopendeJaar,0)) as historischeWaarde,
SUM(IF(TijdelijkeRapportage.type = 'rente' , (actuelePortefeuilleWaardeEuro),0)) / ".$this->pdf->ValutaKoersEind."  AS rente,
SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS actuelePortefeuilleWaardeEuro,
 SUM(IF(TijdelijkeRapportage.type = 'fondsen',(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid * TijdelijkeRapportage.historischeValutakoers),0 )) AS historischeWaardeEuro,
SUM(IF(TijdelijkeRapportage.type = 'rekening' ,actuelePortefeuilleWaardeInValuta, IF(TijdelijkeRapportage.type = 'fondsen',totaalAantal,0))) as totaalAantal,
SUM(IF(TijdelijkeRapportage.type = 'rekening' ,1, IF(TijdelijkeRapportage.type = 'fondsen',totaalAantal,0))) as tonen,
TijdelijkeRapportage.actueleFonds,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.actueleValuta,
TijdelijkeRapportage.fondsOmschrijving,
TijdelijkeRapportage.rekening,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.beleggingscategorieVolgorde ,
if(TijdelijkeRapportage.fonds<>'','fondsen',if(TijdelijkeRapportage.type = 'rente','rekening',type)) as type,
TijdelijkeRapportage.beleggingscategorieOmschrijving as categorieOmschrijving,
round((UNIX_TIMESTAMP(TijdelijkeRapportage.rapportageDatum) - UNIX_TIMESTAMP(TijdelijkeRapportage.koersDatum))/86400) as koersLeeftijd,
TijdelijkeRapportage.Bewaarder,
Rekeningen.IBANnr,
Rekeningen.Tenaamstelling,
Depotbanken.Omschrijving as BewaarderNaam
FROM
TijdelijkeRapportage
LEFT JOIN Rekeningen ON TijdelijkeRapportage.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie='0'
LEFT JOIN Depotbanken ON TijdelijkeRapportage.Bewaarder = Depotbanken.Depotbank OR Depotbanken.Depotbank=Rekeningen.Depotbank
LEFT JOIN FondsExtraInformatie ON TijdelijkeRapportage.fonds = FondsExtraInformatie.fonds
WHERE
TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille='".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
GROUP BY
TijdelijkeRapportage.fonds,TijdelijkeRapportage.Bewaarder,TijdelijkeRapportage.rekening
HAVING tonen <> 0
ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde ,TijdelijkeRapportage.fondspaar,TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.ValutaVolgorde";
		$DB->SQL($query);
		$DB->Query();

		$buffer=array();
		$ibanCellen=array();
		$n=0;
		while($data = $DB->nextRecord())
		{
			$data["BewaarderNaam"]=vertaalTekst($data["BewaarderNaam"],$this->pdf->rapport_taal);

			if($data['valuta'] == $this->pdf->rapportageValuta)
			{
				$data['historischeWaardeEuro'] = $data['historischeWaardeEuro']  / $data['historischeValutakoers'];
			}
			elseif($this->pdf->rapportageValuta <> '' && $this->pdf->rapportageValuta <> 'EUR' )
			{
				$data['historischeWaardeEuro'] = $data['historischeWaardeEuro'] /   $this->gemiddeldeTransactieKoersInValuta($data);
			}
			else
			{
				$data['historischeWaardeEuro'] = $data['historischeWaardeEuro'] / $this->pdf->ValutaKoersBegin;
			}

			if($this->pdf->lastPOST['anoniem']==1)
				$data['IBANnr']='';
			$buffer[]=$data;
		}

		$rekeningCount=0;
		$buffer2=array();
		if(isset($this->pdf->__appvar['consolidatie']) && is_array($this->pdf->__appvar['consolidatie']))
		{
			$tmpBuffer=array();

			foreach($buffer as $data)
			{
				if ($data['type'] == 'rekening')
				{
					$tmpBuffer['rekening'][$data['categorieOmschrijving']][$data['valuta']] += $data['actuelePortefeuilleWaardeEuro'] - $data['rente'];
					$rekeningCount++;
				}
				else
					$tmpBuffer['overige'][]=$data;
			}
			foreach($tmpBuffer['overige'] as $waarde)
				$buffer2[]=$waarde;
			foreach($tmpBuffer['rekening'] as $liqCategorie=>$rekeningen)
				foreach($rekeningen as $valuta=>$waardeEur)
			    	$buffer2[]=array('valuta'=>$valuta,'Tenaamstelling'=>vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal)." $valuta",'actuelePortefeuilleWaardeEuro'=>$waardeEur,'categorieOmschrijving'=>$liqCategorie,'type'=>'rekening');
		}

		if($rekeningCount>8)
		{
			$buffer=$buffer2;
		}
		else
		{
			foreach($buffer as $data)
			{
				if ($data['type'] == 'rekening'||$data['type'] == 'rente')
				{
					$ibanParts = str_split(str_replace(' ', '', $data['IBANnr']), 4);
					foreach ($ibanParts as $index => $cell)
					{
						$width = $this->pdf->GetStringWidth($cell) + 1;
						$ibanCellen[$index] = max($width, $ibanCellen[$index]);
					}
				}
			}
		}

		foreach($buffer as $data)
		{
			if(!isset($categorieAantallen[$data['categorieOmschrijving']]))
				$categorieAantallen[$data['categorieOmschrijving']]=0;
			$categorieAantallen[$data['categorieOmschrijving']]++;

		}

		$totalenCat=array();
    $extraVoet='';
		foreach($buffer as $data)
		{
			//if($data['rekening'] <> '')
			//	$data['fondsOmschrijving'].=' '.substr($data['rekening'],0,strlen($data['rekening'])-3);

			$data['actuelePortefeuilleWaardeEuro']=$data['actuelePortefeuilleWaardeEuro']-$data['rente'];
			if($data['type']=='rekening')
				$ongerealiseerdResultaat=0;
			else
				$ongerealiseerdResultaat=$data['actuelePortefeuilleWaardeEuro']-$data['historischeWaardeEuro'];

			$aandeel=$data['actuelePortefeuilleWaardeEuro']/$portefeuilleWaarde*100;
			$ongerealiseerdResultaatProcent=($ongerealiseerdResultaat)/ABS($data['historischeWaardeEuro']) *100;

			$totalenCat[$data['categorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
			$totalenCat[$data['categorieOmschrijving']]['historischeWaardeEuro'] += $data['historischeWaardeEuro'];
			$totalenCat[$data['categorieOmschrijving']]['ongerealiseerdResultaat'] += $ongerealiseerdResultaat;
			$totalenCat[$data['categorieOmschrijving']]['aandeel'] += $aandeel;

			$totalen['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
			$totalen['historischeWaardeEuro'] += $data['historischeWaardeEuro'];
			$totalen['ongerealiseerdResultaat'] += $ongerealiseerdResultaat;
			$totalen['aandeel'] += $aandeel;

			if($data['categorieOmschrijving'] <> $lastcategorieOmschrijving)
			{
				if(!empty($lastcategorieOmschrijving))
				{
					$this->pdf->CellBorders = array('','','','','T','T','T','T','T');
					$this->pdf->row(array('','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],2),
														$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['historischeWaardeEuro'],2),
														$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['ongerealiseerdResultaat'],2),
														$this->formatGetal(($totalenCat[$lastcategorieOmschrijving]['ongerealiseerdResultaat'])/ABS($totalenCat[$lastcategorieOmschrijving]['historischeWaardeEuro'])*100,2).'%',
														$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['aandeel'],2).'%',''));
					unset($this->pdf->CellBorders);
					$this->pdf->ln();
				}

				$aantalRegels=$categorieAantallen[$data['categorieOmschrijving']];
				$beschikbareRuimte=$this->pdf->pagebreak-$this->pdf->getY();
				$passendeRegels=floor($beschikbareRuimte/$this->pdf->rowHeight)-3;
				$regelsOver=$passendeRegels-$aantalRegels;
				if(($regelsOver < 0 && $regelsOver > -5 && $aantalRegels < 5) || $passendeRegels <2)
				{
					$this->pdf->addPage();
				}

				if($this->pdf->getY() > 180)
					$this->pdf->addPage();
				$this->addHeader($data['categorieOmschrijving'],$data['beleggingscategorie']);
			}
			elseif ($this->pdf->getY()+$this->pdf->rowHeight > $this->pdf->pagebreak)
			{
				$this->pdf->addPage();
				$this->addHeader($data['categorieOmschrijving'],$data['beleggingscategorie']);
			}

			$totalen['rente'] += $data['rente'];

      if($data['KPWaardering']==1)
      {
        $markering = "**";
        $extraVoet="\nInstrumenten met bij de koers ** zijn gewaardeerd op kostprijs.";
      }
			elseif($data['koersLeeftijd'] > $maxDagenOud && $data['actueleFonds'] <> 0)
      {
        $markering = "*";
      }
			else
      {
        $markering = "";
      }

			if($data['type']=='rekening'||$data['type'] == 'rente')
			{
				$resultaat=$this->formatGetal($data['totaalAantal'],0);
				$aantal='';
				$ibanParts=str_split(str_replace(' ','',$data['IBANnr']),4);
				//$ibanParts=array_reverse($ibanParts);
				$xBegin=62;
				$x=$xBegin;

				foreach($ibanParts as $index=>$waarde)
				{
					if($index==0)
					{
						$x=$xBegin;
						$this->pdf->setX($x);
					}
					$cellWidth=$ibanCellen[$index];
					$x+=$cellWidth;
						$align = 'L';
					$this->pdf->cell($cellWidth,4,$waarde,0,0,$align);
					$this->pdf->setX($x);
				}
				$this->pdf->setX($this->pdf->marge);
				$totalen['rekening'] += $data['actuelePortefeuilleWaardeEuro'];
				//$data['fondsOmschrijving']=$data['Tenaamstelling'];
			}
			else
			{
				$resultaat=$this->formatGetal($ongerealiseerdResultaat,2);
				$aantal=$att->formatAantal($data['totaalAantal'],0,true);
			}


			$this->pdf->row(array($data['fondsOmschrijving'],
												$aantal,
												$data['valuta'],
												$this->formatGetal($data['actueleFonds'],2).$markering,
												$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],2),
												$this->formatGetal($data['historischeWaardeEuro'],2),
												$resultaat,
												$this->formatGetal($ongerealiseerdResultaatProcent,2).'%',
												$this->formatGetal($aandeel,2).'%',
												$data['BewaarderNaam']));
			$this->pdf->excelData[]=array($data['fondsOmschrijving'],
				$data['totaalAantal'],
				$data['valuta'],
				round($data['actueleFonds'],2),
				round($data['actuelePortefeuilleWaardeEuro'],2),
				round($data['historischeWaardeEuro'],2),
				round($ongerealiseerdResultaat,2),
				round($ongerealiseerdResultaatProcent,2),
				round($aandeel,2),
				$data['BewaarderNaam']);
			$lastcategorieOmschrijving=$data['categorieOmschrijving'];
		}

		if(!empty($lastcategorieOmschrijving))
		{
			$this->pdf->CellBorders = array('','','','','T','T','T','T','T');
			$this->pdf->row(array('','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],2),
												$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['historischeWaardeEuro'],2),
												$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['ongerealiseerdResultaat'],2),
												$this->formatGetal(($totalenCat[$lastcategorieOmschrijving]['ongerealiseerdResultaat'])/ABS($totalenCat[$lastcategorieOmschrijving]['historischeWaardeEuro'])*100,2).'%',
												$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['aandeel'],2).'%',''));
			unset($this->pdf->CellBorders);
			$this->pdf->ln();
		}

		$ruimte=$this->pdf->rowHeight*6;
		if($this->pdf->getY()+$ruimte > $this->pdf->pagebreak)
			$this->pdf->addPage();

		$this->pdf->CellBorders = array(array('T','L'),'T','T','T','T','T','T','T','T',array('T','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->ln(2);
		$this->pdf->row(array(vertaalTekst('Beleggingen',$this->pdf->rapport_taal),'','',$this->pdf->rapportageValuta,$this->formatGetal($totalen['actuelePortefeuilleWaardeEuro']-$totalen['rekening'],2),'','',
											$this->formatGetal(($totalen['ongerealiseerdResultaat'])/ABS($totalen['historischeWaardeEuro'])*100,2).'%',
											$this->formatGetal(($totalen['actuelePortefeuilleWaardeEuro']-$totalen['rekening'])/$portefeuilleWaarde*100,2,true).'%',''));
		$this->pdf->CellBorders = array(array('L'),'','','','','','','','',array('R'));
		$this->pdf->row(array(vertaalTekst('Opgelopen rente',$this->pdf->rapport_taal),'','',$this->pdf->rapportageValuta,$this->formatGetal($totalen['rente'],2),'','','',$this->formatGetal($totalen['rente']/$portefeuilleWaarde*100,2,true).'%',''));
		$this->pdf->CellBorders = array(array('T','L'),'T','T','T','T','T','T','T','T','R');
		$this->pdf->row(array(vertaalTekst('Belegd vermogen',$this->pdf->rapport_taal),'','',$this->pdf->rapportageValuta,$this->formatGetal($totalen['rente']+$totalen['actuelePortefeuilleWaardeEuro']-$totalen['rekening'],2),'','','',
											$this->formatGetal((($totalen['rente']-$totalen['rekening'])/$portefeuilleWaarde*100)+($totalen['aandeel']),2,true).'%',''));
		$this->pdf->CellBorders = array(array('L'),'','','','','','','','',array('R'));
		$this->pdf->row(array('','','','','','','','','',''));
		$this->pdf->row(array(vertaalTekst('Liquiditeiten',$this->pdf->rapport_taal),'','',$this->pdf->rapportageValuta,$this->formatGetal($totalen['rekening'],2),'','','',
											$this->formatGetal(($totalen['rekening']/$portefeuilleWaarde*100),2,true).'%',''));

		$this->pdf->CellBorders = array(array('T','L','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('U','R'));
		$this->pdf->row(array(vertaalTekst('Totaal vermogen',$this->pdf->rapport_taal),'','',$this->pdf->rapportageValuta,$this->formatGetal($totalen['rente']+$totalen['actuelePortefeuilleWaardeEuro'],2),'','','',
											$this->formatGetal(($totalen['rente']/$portefeuilleWaarde*100)+($totalen['aandeel']),2,true).'%',''));

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		unset($this->pdf->CellBorders);
		/*
		$this->pdf->CellBorders = array('','','','','T','T','T','T','T');
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->ln(2);
		$this->pdf->row(array('Beleggingen','','','',$this->formatGetal($totalen['actuelePortefeuilleWaardeEuro'],2),
											$this->formatGetal($totalen['historischeWaardeEuro'],2),
											$this->formatGetal($totalen['ongerealiseerdResultaat'],2),
											$this->formatGetal(($totalen['ongerealiseerdResultaat'])/ABS($totalen['historischeWaardeEuro'])*100,2).'%',
											$this->formatGetal($totalen['aandeel'],2).'%',''));
		unset($this->pdf->CellBorders);
		$this->pdf->row(array('Opgelopen rente','','','',$this->formatGetal($totalen['rente'],2),'','','',$this->formatGetal($totalen['rente']/$portefeuilleWaarde*100,2).'%'));
		$this->pdf->row(array('Totaal vermogen','','','',$this->formatGetal($totalen['rente']+$totalen['actuelePortefeuilleWaardeEuro'],2),'','','',
											$this->formatGetal(($totalen['rente']/$portefeuilleWaarde*100)+($totalen['aandeel']),2).'%'));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		unset($this->pdf->CellBorders);
*/
		$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		$this->pdf->MultiCell(200,5,vertaalTekst("Koersen met een * zijn ouder dan de rapportagedatum.$extraVoet",$this->pdf->rapport_taal),0,'L');
		//berekenPortefeuilleWaarde($this->portefeuille, $this->rapportageDatum,false,'EUR',$this->rapportageDatumVanaf);
		//vulTijdelijkeTabel($waarden,$this->portefeuille, $this->rapportageDatum);
		$this->pdf->templateVars['VHOPaginas2'] = $this->pdf->page;
	}
}
?>
<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/18 17:44:11 $
File Versie					: $Revision: 1.4 $

$Log: RapportMUT_L73.php,v $
Revision 1.4  2020/03/18 17:44:11  rvv
*** empty log message ***

Revision 1.3  2018/11/16 16:41:32  rvv
*** empty log message ***

Revision 1.2  2017/07/01 17:03:24  rvv
*** empty log message ***

Revision 1.1  2017/06/10 18:09:58  rvv
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

class RapportMUT_L73
{
	function RapportMUT_L73($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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


	  $this->pdf->excelData[]=array("Datum",'Aan/verkoop','Aantal','Fonds','Aankoopkoers in valuta','Aankoopwaarde in valuta','Aankoopwaarde in EUR',
	  'Verkoopkoers in valuta','Verkoopwaarde in valuta','Verkoopwaarde in valuta','Historsiche kostprijs in EUR','Resultaat voorgaande jaren','Resultaat lopende jaar','%');

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
		$this->pdf->CellBorders = array(array('T','U','L'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_mut_headercolor['r'],$this->pdf->rapport_mut_headercolor['g'],$this->pdf->rapport_mut_headercolor['b']);
		unset($this->pdf->fillCell);
		//$this->pdf->fillCell = array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
	//	$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);

		$this->pdf->row(array(vertaalTekst($categorie,$this->pdf->rapport_taal),'','','','','','',''));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array(array('U','L'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U','R'));

//listarray($this->pdf->rapport_kop_bgcolor);
	//	$this->pdf->fillCell = array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
		//$this->pdf->SetFillColor(150,175,200);
		$this->pdf->row(array(vertaalTekst("Datum",$this->pdf->rapport_taal),
											vertaalTekst("Beschrijving",$this->pdf->rapport_taal),
									    vertaalTekst("Val.",$this->pdf->rapport_taal),
											vertaalTekst("Rekening",$this->pdf->rapport_taal),
											vertaalTekst("Bedrag",$this->pdf->rapport_taal),
											vertaalTekst("Wisselkoers",$this->pdf->rapport_taal),
											vertaalTekst('Bedrag in EUR',$this->pdf->rapport_taal),
											''
											));

		$this->pdf->SetTextColor(0);
		unset($this->pdf->CellBorders);
		$this->pdf->rowHeight=$rowHeightBackup;
		//$this->pdf->ln(1);
	}

	function addTotaal($totaal,$addU=false)
	{
		//$this->pdf->fillCell = array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
		//$this->pdf->SetFillColor(150,175,200);
		unset($this->pdf->fillCell);
		$rowHeightBackup=$this->pdf->rowHeight;
		$this->pdf->rowHeight=$rowHeightBackup*1.5;
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array(array('T','U','L'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U','R'));
		if($addU==true)
			$this->pdf->CellBorders = array('','','',array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),'');

		$this->pdf->row(array($totaal['omschrijving'],
											'',
											'',
											'',
											'',
											'',
											$this->formatGetal($totaal['bruto'],2),
											''));
		unset($this->pdf->CellBorders);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->rowHeight=$rowHeightBackup;
		$this->pdf->ln();


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
		$this->pdf->widthB = array(30,105,22,30,26,23,26,18);//108/2
		$this->pdf->alignB = array('L','L','L','L','R','R','R','R','R','R','R','R','R','R');
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
		$this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
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
    Rekeningmutaties.Grootboekrekening,
    Rekeningmutaties.Rekening,
    Grootboekrekeningen.Omschrijving as grootboekOmschrijving
      ".
		"FROM Rekeningmutaties
		JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening 
		JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
		JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening ".
		"WHERE ".
		"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		"Rekeningmutaties.Verwerkt = '1' AND ".
		"Rekeningmutaties.Transactietype <> 'B' AND Rekeningmutaties.Grootboekrekening <> 'VERM' AND ".
		"Grootboekrekeningen.FondsAanVerkoop ='0' AND ". //Rekeningmutaties.Fonds='' AND
		"Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
		"Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' $extraquery".
		"ORDER BY Rekeningmutaties.Grootboekrekening,Rekeningmutaties.Boekdatum, Rekeningmutaties.id";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		// haal koersresultaat op om % te berekenen

		//$koersresultaat = gerealiseerdKoersresultaat($this->portefeuille,$this->rapportageDatumVanaf, $this->rapportageDatum,$this->pdf->rapportageValuta);
		$transactietypen = array();

		$buffer = array();
    $soortVertaling=array('4-STORT'=>'Stortingen','4-ONTTR'=>'Onttrekkingen','4-Kruis'=>'Kruisposten');
		$fondsFilter=false;
		$fondsFilterSql='';
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


			$mutaties['valutaKoersRegel']=$koers;

			$buffer[] = $mutaties;
		}
		$transactietypenOmschrijving=array('A'=>'Aankoop','A/O'=>'Aankoop / openen','A/S'=>'Aankoop / sluiten','D'=>'Deponering','L'=>'Lichting','V'=>'Verkoop','V/O'=>'Verkoop / openen','V/S'=>'Verkoop / sluiten');
		$categorieTotalen=array();
		$totalen=array();
		$n=0;
		foreach ($buffer as $mutaties)
		{


			$koers=$mutaties['valutaKoersRegel'];

		  $kop=$mutaties['grootboekOmschrijving'];

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


			if($n%2 == 0)
			{
				$this->pdf->fillCell = array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
				$this->pdf->SetFillColor($this->pdf->rapport_regelAchtergrond[0],$this->pdf->rapport_regelAchtergrond[1],$this->pdf->rapport_regelAchtergrond[2]);
			}
			else
				unset($this->pdf->fillCell);

			$omschrijvingWidth = $this->pdf->GetStringWidth($mutaties['rekeningOmschrijving']);
			$cellWidth = $this->pdf->widths[1] - 2;
			$omschrijving='';
			if ($omschrijvingWidth > $cellWidth)
			{
				$dotWidth = $this->pdf->GetStringWidth('...');
				$chars = strlen($mutaties['rekeningOmschrijving']);
				$newOmschrijving = $mutaties['rekeningOmschrijving'];
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
			else
			{
				$omschrijving = $mutaties['rekeningOmschrijving'];
			}


			$this->pdf->row(array($datum,
												$omschrijving,
												$mutaties['Valuta'],
                        $mutaties['Rekening'],
												$this->formatGetal($mutaties['Credit']-$mutaties['Debet'],2),
												$this->formatGetal($mutaties['Valutakoers']/$koers,4),
												$this->formatGetal($bruto,2),''));
			$categorieTotalen['bruto']+=$bruto;
			$categorieTotalen['kosten']+=$mutaties['transData']['kosten'];
			$categorieTotalen['rente']+=$mutaties['transData']['rente'];
			$categorieTotalen['belasting']+=$mutaties['transData']['belasting'];

			$totalen['bruto']+=$bruto;
			$totalen['kosten']+=$mutaties['transData']['kosten'];
			$totalen['rente']+=$mutaties['transData']['rente'];
			$totalen['belasting']+=$mutaties['transData']['belasting'];
      $n++;
		}
		$this->addTotaal($categorieTotalen);
		$this->pdf->ln(1);
		$this->addTotaal($totalen);

	}
}
?>

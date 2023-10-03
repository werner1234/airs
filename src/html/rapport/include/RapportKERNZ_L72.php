<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/11/21 16:48:32 $
File Versie					: $Revision: 1.45 $

$Log: RapportKERNZ_L72.php,v $
Revision 1.45  2018/11/21 16:48:32  rvv
*** empty log message ***

Revision 1.44  2018/11/17 17:34:53  rvv
*** empty log message ***

Revision 1.43  2018/08/29 16:16:29  rvv
*** empty log message ***

Revision 1.42  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.41  2018/08/04 11:54:53  rvv
*** empty log message ***

Revision 1.40  2018/06/16 17:42:56  rvv
*** empty log message ***

Revision 1.39  2018/02/14 16:53:20  rvv
*** empty log message ***

Revision 1.38  2018/02/10 18:09:12  rvv
*** empty log message ***

Revision 1.37  2018/02/03 18:54:04  rvv
*** empty log message ***

Revision 1.36  2017/08/19 18:18:00  rvv
*** empty log message ***

Revision 1.35  2017/06/10 18:08:40  rvv
*** empty log message ***

Revision 1.34  2017/05/25 14:35:58  rvv
*** empty log message ***

Revision 1.33  2017/05/20 18:16:29  rvv
*** empty log message ***

Revision 1.32  2017/05/10 14:44:58  rvv
*** empty log message ***

Revision 1.31  2017/05/03 14:35:54  rvv
*** empty log message ***

Revision 1.30  2017/04/26 15:19:25  rvv
*** empty log message ***

Revision 1.29  2017/04/15 19:11:50  rvv
*** empty log message ***

Revision 1.28  2017/04/08 18:22:43  rvv
*** empty log message ***

Revision 1.27  2017/04/05 15:39:45  rvv
*** empty log message ***

Revision 1.26  2017/03/22 16:53:22  rvv
*** empty log message ***

Revision 1.25  2017/02/25 18:02:29  rvv
*** empty log message ***

Revision 1.24  2017/02/22 17:15:06  rvv
*** empty log message ***

Revision 1.23  2017/02/18 17:32:08  rvv
*** empty log message ***

Revision 1.22  2017/02/15 11:25:53  rvv
*** empty log message ***

Revision 1.21  2017/02/11 17:30:10  rvv
*** empty log message ***

Revision 1.20  2017/02/08 13:44:17  rvv
*** empty log message ***

Revision 1.19  2017/02/08 12:32:32  rvv
*** empty log message ***

Revision 1.18  2017/01/29 10:25:25  rvv
*** empty log message ***

Revision 1.17  2017/01/19 07:11:26  rvv
*** empty log message ***

Revision 1.16  2017/01/18 17:02:28  rvv
*** empty log message ***

Revision 1.15  2017/01/11 17:12:46  rvv
*** empty log message ***

Revision 1.14  2016/12/30 15:31:00  rvv
*** empty log message ***

Revision 1.13  2016/12/29 10:37:25  rvv
*** empty log message ***

Revision 1.12  2016/12/28 19:38:27  rvv
*** empty log message ***

Revision 1.11  2016/11/30 14:34:19  rvv
*** empty log message ***

Revision 1.10  2016/11/30 12:26:19  rvv
*** empty log message ***

Revision 1.9  2016/11/27 11:09:00  rvv
*** empty log message ***

Revision 1.8  2016/11/23 13:06:18  rvv
*** empty log message ***

Revision 1.7  2016/11/19 19:03:08  rvv
*** empty log message ***

Revision 1.6  2016/11/16 16:51:17  rvv
*** empty log message ***

Revision 1.5  2016/11/13 14:09:56  rvv
*** empty log message ***

Revision 1.4  2016/11/12 20:21:18  rvv
*** empty log message ***

Revision 1.3  2016/11/06 10:40:23  rvv
*** empty log message ***

Revision 1.2  2016/10/30 13:02:59  rvv
*** empty log message ***

Revision 1.1  2016/10/09 14:45:08  rvv
*** empty log message ***

Revision 1.4  2016/05/29 13:26:30  rvv
*** empty log message ***

Revision 1.3  2016/05/21 19:00:02  rvv
*** empty log message ***

Revision 1.2  2016/05/15 17:15:00  rvv
*** empty log message ***

Revision 1.1  2016/05/08 19:24:24  rvv
*** empty log message ***

Revision 1.2  2015/12/16 17:06:48  rvv
*** empty log message ***

Revision 1.1  2015/09/05 16:48:04  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L72.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");
include_once('../indexBerekening.php');


class RapportKERNZ_L72
{
	function RapportKERNZ_L72($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "KERNZ";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Performance overzicht";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();


		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
		$RapStopJaar = date("Y", db2jul($this->rapportageDatum));
		$this->ytdRendement=0;
		$this->maandRendement=0;
		$portefeuilleStartJul=db2jul($this->pdf->PortefeuilleStartdatum);
		if($RapStartJaar != $RapStopJaar)
			$this->tweedePerformanceStart = "$RapStopJaar-01-01";
		else
		{
			if($portefeuilleStartJul > db2jul($this->rapportageDatumVanaf))
				$this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
			elseif($portefeuilleStartJul > db2jul("$RapStartJaar-01-01"))
				$this->tweedePerformanceStart = date('Y-m-d',($portefeuilleStartJul-0*86400));

			else
				$this->tweedePerformanceStart = "$RapStartJaar-01-01";
		}
		$rapportageMaand=substr($this->rapportageDatum,5,2);
		$rapportageJaar=substr($this->rapportageDatum,0,4);
		$this->laatsteMaandBegin=date("Y-m-d",mktime(0,0,0,$rapportageMaand,0,$rapportageJaar));

		if($portefeuilleStartJul > db2jul($this->laatsteMaandBegin))
			$this->laatsteMaandBegin =  date('Y-m-d',($portefeuilleStartJul-0*86400));

  //  $this->rapportFilter="";// AND TijdelijkeRapportage.hoofdcategorie='ZAK'
	}


	function formatGetal($waarde, $dec,$nulNietTonen=false,$max=0)
	{
		if($max==='pm')
			return 'pm';

		if($max<>0)
		{
			if(abs($waarde) > $max)
				return "pm";
		}
		if($nulNietTonen==true && round($waarde,$dec)==0)
			return '';
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
		$this->pdf->templateVars['KERNZPaginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving['KERNZPaginas']=$this->pdf->rapport_titel;


		$rapportageDatum = $this->rapportageDatum;

	$portefeuille = $this->portefeuille;
		$DB=new DB();
	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind."  AS totaal ".
					 "FROM TijdelijkeRapportage WHERE ".
					 " rapportageDatum ='".$rapportageDatum."' AND ".
					 " portefeuille = '".$portefeuille."' ".$this->rapportFilter
					 .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$totaalWaarde = $DB->nextRecord();
	$totaalWaarde = $totaalWaarde['totaal'];
		$this->totaalWaarde=$totaalWaarde;

	//Kleuren instellen
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);


$query="SELECT
if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.hoofdcategorie <> '',TijdelijkeRapportage.hoofdcategorie,'geen')) as categorie,
sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS WaardeEuro,
 if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.hoofdcategorieOmschrijving <> '',TijdelijkeRapportage.hoofdcategorieOmschrijving,'geen')) as categorieOmschrijving
FROM TijdelijkeRapportage 
LEFT JOIN Beleggingscategorien on TijdelijkeRapportage.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
	WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."' ".$this->rapportFilter." "
	.$__appvar['TijdelijkeRapportageMaakUniek'].
	" GROUP BY categorie
	ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde, categorie";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$negatief=false;
	while($cat = $DB->nextRecord())
	{
	   $data['hoofdcategorie']['data'][$cat['categorieOmschrijving']]['waardeEur']=$cat['WaardeEuro'];
	   $data['hoofdcategorie']['data'][$cat['categorieOmschrijving']]['Omschrijving']=$cat['categorieOmschrijving'];
	   $data['hoofdcategorie']['pieData'][$cat['categorieOmschrijving']]= $cat['WaardeEuro']/$totaalWaarde;
	   $data['hoofdcategorie']['kleurData'][$cat['categorieOmschrijving']]=array($allekleuren['OIB'][$cat['categorie']]['R']['value'],$allekleuren['OIB'][$cat['categorie']]['G']['value'],$allekleuren['OIB'][$cat['categorie']]['B']['value']);
	    $percentage=$cat['WaardeEuro']/$totaalWaarde*100;
	    $data['hoofdcategorie']['kleurData'][$cat['categorieOmschrijving']]['percentage']=$percentage;
		$data['hoofdcategorie']['percentage'][$cat['categorieOmschrijving']]=$percentage;
		 if($percentage<0)
			 $negatief=true;
	}

		$this->pdf->setDrawColor($this->pdf->rapport_kaderkleur[0],$this->pdf->rapport_kaderkleur[1],$this->pdf->rapport_kaderkleur[2]);
		$this->pdf->Rect($this->pdf->marge, 24,297-$this->pdf->marge*2, 78, 'D');
		$this->pdf->Rect($this->pdf->marge, 104,297-$this->pdf->marge*2, 93, 'D');
		$this->pdf->line(297/2, 104,297/2, 104+93);
		$this->pdf->setDrawColor(0);
    $this->pdf->setXY(20,36);
    $data['hoofdcategorie']['title']=vertaalTekst('Asset Allocatie per',$this->pdf->rapport_taal).' '.vertaalTekst($__appvar["Maanden"][date("n",db2jul($rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($rapportageDatum));
    if($negatief==true)
		{
			$this->pdf->setXY(30,36);
			$this->BarDiagram(90, 80, $data['hoofdcategorie']['percentage'], '%l (%p)', $data['hoofdcategorie']['kleurData'], $data['hoofdcategorie']['title']);
		}
		else
		  $this->PieChart(60,50,$data['hoofdcategorie']);


		$julDatum = db2jul($this->rapportageDatum);
    $start=date('Y-m-d', mktime(0,0,0,date('m',$julDatum),0,date('Y',$julDatum)));

		$this->pdf->setXY(120,31);
		$this->addPerf($start,$this->rapportageDatum,135);


		//$index=new indexHerberekening();
		//$index->forceDbLoad=true;
		$this->forceDbLoad=true;
		$jaarstart=substr($this->rapportageDatum,0,5)."01-01";
		if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($jaarstart))
			$jaarstart=substr($this->pdf->PortefeuilleStartdatum,0,10);
		$this->waarden['Jaar'] = $this->getWaarden($jaarstart, $this->rapportageDatum ,$this->portefeuille,'','maanden',$this->pdf->rapportageValuta);//,'','maanden', $this->pdf->rapportageValuta

//listarray($this->waarden['Jaar']);
		$line=array();
		$bar=array();
		$perfCum=1;

		$jaar=substr($this->rapportageDatum,0,5);
		for($i=0;$i<12;$i++)
		{
			if(substr($this->rapportageDatum,5,2)==$i+1)
				$bar[$this->rapportageDatum]['portefeuille']=0;
			else
		  	$bar[date('Y-m-d',mktime(0,0,0,$i+2,0,$jaar))]['portefeuille']=0;
		}
	//	listarray($this->waarden['Jaar']);
	//	listarray($bar);
		$i=0;
		foreach($this->waarden['Jaar'] as $index=>$values)
		{
			$bar[$values['datum']]['portefeuille']=$values['performance'];

			$this->maandRendement=$values['performance'];

			$this->ytdRendement=$values['index']-100;
		}

		$this->pdf->setXY(158,180);
		$this->VBarDiagram2(120,60,$bar);


		$this->pdf->setXY(30,118);
		$this->addZorgBar(100,60);

		$this->pdf->addPage();

		$this->pdf->setDrawColor($this->pdf->rapport_kaderkleur[0],$this->pdf->rapport_kaderkleur[1],$this->pdf->rapport_kaderkleur[2]);
		$this->pdf->Rect($this->pdf->marge, 24,297-$this->pdf->marge*2, 78, 'D');
		$this->pdf->Rect($this->pdf->marge, 104,297-$this->pdf->marge*2, 93, 'D');
		$this->pdf->line(297/2, 104,297/2, 104+93);
		$this->pdf->setDrawColor(0);

    $this->pdf->ln();
		$this->ATTblok(24);

		$this->pdf->setY(110);
		$this->historieBlok($totaalWaarde);





		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
unset($this->pdf->CellBorders);
	}

	function historieBlok($totaalWaarde)
	{
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$query = "SELECT Portefeuilles.startDatum, Portefeuilles.startdatumMeerjarenrendement, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();

		//$att=new ATTberekening_L72($this);


		//$indexBerekening=new indexHerberekening();
		$this->forceDbLoad=true;
		$historischeStart=substr($portefeuilledata['startDatum'],0,10);

		if($portefeuilledata['startdatumMeerjarenrendement'] <> '0000-00-00')
	  	$historischeStart=$portefeuilledata['startdatumMeerjarenrendement'];
		//$startJaar=substr($historischeStart,0,4);
		$rapportageJaar=substr($this->rapportageDatum,0,4);
		$rapportageMaand=substr($this->rapportageDatum,5,2);
    $vijfJaarTerug=mktime(0,0,0,1,1,$rapportageJaar-5);
		//echo $rapportageMaand;exit;
	//	echo date('d-m-Y',$vijfJaarTerug);exit;
    if(db2jul($historischeStart) < $vijfJaarTerug)
		{
			$historischeStart=date('Y-m-d',$vijfJaarTerug);
			$periodeTxt="afgelopen 5 jaar";
		}
		else
		{
			$periodeTxt="sinds inceptie" ;
		}
//
		$this->waarden['historie'] = $this->getWaarden($historischeStart,  $this->rapportageDatum ,$this->portefeuille,$this->pdf->portefeuilledata['SpecifiekeIndex'],'maanden',$this->pdf->rapportageValuta);// //,'maanden', $this->pdf->rapportageValuta
		$line=array();
		$bar=array();
		$perfCum=1;
		$index=0;
		$indexGeanualiseerd=0;
		$lastJaar=0;

		$aantal=count($this->waarden['historie']);
		$vanaf=$aantal-60;
		foreach($this->waarden['historie'] as $i=>$values)
		{
			$juldate=db2jul($values['datum']);

			$jaar=substr($values['datum'],0,4);
			if($jaar <> $lastJaar)
				$indexJaar=0;
			$lastJaar=$jaar;

			if(!isset($eersteJaar))
				$eersteJaar=vertaalTekst($this->pdf->__appvar["Maanden"][intval(substr($values['datum'],5,2))],$this->pdf->rapport_taal)." ".$jaar;

			$index=((1+$index)*(1+$values['performance']/100))-1;
			$indexJaar=((1+$indexJaar)*(1+$values['performance']/100))-1;

			$jaarresultaten[$jaar]['perf'] = $indexJaar;
			if($i>$vanaf)
			{
				$rendementen[] = $values['performance'];
				$indexGeanualiseerd=((1+$indexGeanualiseerd)*(1+$values['performance']/100))-1;
				//echo $values['datum']."|".$values['performance']."|$indexGeanualiseerd <br>\n";
			}
			$line['portefeuille'][]=$index*100;
			$line['benchmark'][]=$values['specifiekeIndex']-100;
			$line['datum'][]=date("M-Y",$juldate);


		}
		$aantal=count($rendementen);


    $jaarresultaten=array_reverse($jaarresultaten,true);
		$huidigeJaar=date('Y',$this->pdf->rapport_datum);

		$this->pdf->SetWidths(array(155,80,25,10));
		$this->pdf->SetAligns(array('L','L','R','R'));

		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('',vertaalTekst('Performance historie vanaf',$this->pdf->rapport_taal)." $eersteJaar",''));
		$this->pdf->ln();
		$this->pdf->CellBorders=array('','U','U','U');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('','',vertaalTekst("Portefeuille",$this->pdf->rapport_taal),''));
		unset($this->pdf->CellBorders);

		foreach($jaarresultaten as $jaar=>$perf)
		{
			if($jaar==$huidigeJaar)
				$ytd = 'ytd';
			else
				$ytd='';
			$this->pdf->row(array('',$jaar,$this->formatGetal($perf['perf']*100,2)."%",$ytd));


		}


			$correctie = pow(12, 0.5);
			$this->pdf->ln();
		if($aantal > 11)
		{
			$indexGeanualiseerd=$indexGeanualiseerd+1;
  		$geanualiseerdRendement=(pow($indexGeanualiseerd, (12 / $aantal))-1)*100;

		//	$geanualiseerdRendement = pow($indexGeanualiseerd, (12 / $aantal)) * ($indexGeanualiseerd) * ($indexGeanualiseerd * 100) / ($aantal / 12);
			//echo "$geanualiseerdRendement = pow($index, (12 / $aantal)) * ($index) * ($index * 100) / ($aantal / 12);";exit;
			$this->pdf->row(array('', vertaalTekst('Geannualiseerd rendement',$this->pdf->rapport_taal).' ' . vertaalTekst($periodeTxt,$this->pdf->rapport_taal), $this->formatGetal($geanualiseerdRendement, 2) . "%"));
			$this->pdf->row(array('', vertaalTekst('Geannualiseerde standaarddeviatie',$this->pdf->rapport_taal).' ' . vertaalTekst($periodeTxt,$this->pdf->rapport_taal), $this->formatGetal(standard_deviation($rendementen) * $correctie, 2) . "%"));
		}
		$afm= AFMstd($this->portefeuille,$this->rapportageDatum);
		$this->pdf->row(array('', vertaalTekst('AFM-Standaarddeviatie',$this->pdf->rapport_taal), $this->formatGetal($afm['std'], 1) . '%'));

		$stdev=new rapportSDberekening($this->portefeuille,$this->rapportageDatum);
		$stdev->settings['julStartdatum']=db2jul($historischeStart);
		$stdev->settings['Startdatum']=$historischeStart;

		$stdev->addReeks('totaal');
		$stdev->addReeks('benchmark',$this->pdf->portefeuilledata['SpecifiekeIndex']);
		//$stdev->addReeks('afm');
		$stdev->berekenWaarden();
		$riskData=$stdev->riskAnalyze();

		if(is_array($riskData))
		{

			$this->pdf->row(array('', vertaalTekst('Value at Risk',$this->pdf->rapport_taal), '€ ' . $this->formatGetal((100 - $riskData['valueAtRisk']) / 100 * $totaalWaarde, 0)));//'Value at Risk geeft het verwachte maximale verlies aan met een waarschijnlijkheid van 95%. De historische VaR is bepaald aan de hand van de werkelijke jaarlijkse rendementsverdeling over de afgelopen tien jaar.'
			$this->pdf->row(array('', vertaalTekst('Maximum Draw Down',$this->pdf->rapport_taal), $this->formatGetal($riskData['maxDrawdown'], 1) . '%'));//'Maximum Drawdown geeft de maximale daling weer vanaf de hoogste waarde in een specifieke periode. Deze periode betreft in de overzichten een periode van tien jaar.'
		}
//listarray($stdev);exit;


		$this->pdf->setXY(20,120);
		$this->LineDiagram(115,50,$line);
	}

	function ATTblok($ystart)
	{
		global $__appvar;
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$query = "SELECT Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
		$portefeuilleStartJul=db2jul($portefeuilledata['startDatum']) ;
		if($portefeuilleStartJul < db2jul(date("Y-01-01",$this->pdf->rapport_datumvanaf)))
			$rapportageStartJaar= date("Y-01-01",$this->pdf->rapport_datumvanaf);
		else
			$rapportageStartJaar=date('Y-m-d',($portefeuilleStartJul-0*86400));
		$this->tweedePerformanceStart=$rapportageStartJaar;


		$query="SELECT SUM(actuelePortefeuilleWaardeEuro)  / ".$this->pdf->ValutaKoersEind."  AS totaal,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.beleggingscategorieOmschrijving
FROM
TijdelijkeRapportage
WHERE TijdelijkeRapportage.portefeuille='".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."'".
			$__appvar['TijdelijkeRapportageMaakUniek']." GROUP BY beleggingscategorie";
			//echo $query."<br>\n"; echo $this->totaalWaarde;exit;
		$DB->SQL($query);
		$DB->Query();
		$categorieVerdeling=array();
		while($cat = $DB->nextRecord())
		{
			$categorieVerdeling[$cat['beleggingscategorie']] = $cat['totaal']/$this->totaalWaarde*100;
			
		}


		$att=new ATTberekening_L72($this);
		$att->specifiekeIndex=$this->pdf->portefeuilledata['SpecifiekeIndex'];
		$att->indexPerformance=true;
		//echo $this->laatsteMaandBegin." ".$this->tweedePerformanceStart."<br>\n";exit;
		$this->waarden['Periode']=$att->bereken($this->laatsteMaandBegin,  $this->rapportageDatum,$this->pdf->rapportageValuta);//$this->rapportageDatumVanaf
		$this->waarden['Jaar']=$att->bereken($this->tweedePerformanceStart,  $this->rapportageDatum,$this->pdf->rapportageValuta);

    $this->pdf->setY($ystart);
		$this->pdf->SetWidths(array(60,20,32+32,35+32,40+30));
		$this->pdf->SetAligns(array('L','R','C','C','C'));
		$this->pdf->CellBorders=array('','','L','L','L');
		$this->pdf->row(array(vertaalTekst("Performance analyse",$this->pdf->rapport_taal),
											vertaalTekst("allocatie",$this->pdf->rapport_taal),
											vertaalTekst("rendement",$this->pdf->rapport_taal),
											vertaalTekst("portefeuillebijdrage",$this->pdf->rapport_taal),
											vertaalTekst("rendement benchmark",$this->pdf->rapport_taal)));


		$this->pdf->SetWidths(array(60,20,32,32,35,32,40,30));
		$this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R'));
		$this->pdf->CellBorders=array(array('','U',''),array('U',''),array('L','U',''),array('U',''),array('L','U',''),array('U',''),array('L','U',''),array('','U',''));
		$this->pdf->setX($this->pdf->marge);
		$this->pdf->row(array("","",vertaalTekst("laatste maand",$this->pdf->rapport_taal),
											vertaalTekst("YTD",$this->pdf->rapport_taal),
											vertaalTekst("laatste maand",$this->pdf->rapport_taal),
											vertaalTekst("YTD",$this->pdf->rapport_taal),
											vertaalTekst("laatste maand",$this->pdf->rapport_taal),
											vertaalTekst("YTD",$this->pdf->rapport_taal)));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		unset($this->pdf->CellBorders);
   	$bovencat=$att->categorien;
		// $bovencat['totaal']='Totaal';
		$this->pdf->CellBorders=array('','','L','','L','','L','');
		$max=1000;
		foreach ($bovencat as $categorie=>$categorieOmschrijving)
		{
			if(in_array($categorie,array('LIQ','Spaar','VAL-TERM','CALL-DEP')))
				$maxPerc='pm';
			else
				$maxPerc=$max;

			if($this->waarden['Periode'][$categorie]['weging'] <> 0 || $this->waarden['Jaar'][$categorie]['weging'] <> 0)
			{
				$this->pdf->row(array(vertaalTekst($categorieOmschrijving,$this->pdf->rapport_taal),
													$this->formatGetal($categorieVerdeling[$categorie], 2,false,$max),
													$this->formatGetal($this->waarden['Periode'][$categorie]['procent'], 2,false,$maxPerc),
													$this->formatGetal($this->waarden['Jaar'][$categorie]['procent'], 2,false,$maxPerc),
													$this->formatGetal($this->waarden['Periode'][$categorie]['bijdrage'], 2,false,$max),
													$this->formatGetal($this->waarden['Jaar'][$categorie]['bijdrage'], 2,false,$max),
													$this->formatGetal($this->waarden['Periode'][$categorie]['indexPerf'], 2,false,$max),
													$this->formatGetal($this->waarden['Jaar'][$categorie]['indexPerf'], 2,false,$max)));

				$this->jaarTotalen['weging']+=$categorieVerdeling[$categorie];
			}
		}

		$totalen=array();
		$jaarTotaal=$this->waarden['Jaar']['totaal'];
		unset($this->waarden['Jaar']['totaal']);
		foreach ($this->waarden['Jaar'] as $categorie=>$categorieData)
		{
			foreach ($categorieData['perfWaarden'] as $maand=>$maandWaarden)
			{
				if($maand <> '')
				{
					$totalen[$maand]['allocateEffect']+=($maandWaarden['weging']-$maandWaarden['indexBijdrageWaarde'])*$maandWaarden['indexPerf']*100;
					$totalen[$maand]['selectieEffect']+=($maandWaarden['procent']-$maandWaarden['indexPerf'])*$maandWaarden['weging']*100;
					$totalen[$maand]['portBijdrage']+=$maandWaarden['bijdrage']*100;
					$totalen[$maand]['indexBijdrage']+=$maandWaarden['indexBijdrage']*100;
					$totalen[$maand]['overperfBijdrage']+=$maandWaarden['relContrib']*100;
					//echo "$categorie ".round($maandWaarden['bijdrage']*100,3)." -> ".round($totalen[$maand]['portBijdrage'],2)."<br>\n";
				}
			}
		}

		foreach($this->waarden['Jaar'] as $categorie=>$weging)
		{
		//	$this->jaarTotalen['weging']+=$weging['weging'];
			$this->jaarTotalen['indexBijdrageWaarde']+=$weging['indexBijdrageWaarde'];
		}
		foreach ($totalen as $maand=>$maandWaarden)
		{
			foreach ($maandWaarden as $veld=>$waarde)
			{
				if(!isset($laatste[$veld]))
					$laatste[$veld]=0;
				$this->jaarTotalen[$veld]=((1+$maandWaarden[$veld]/100)*(1+$laatste[$veld]/100)-1)*100;
				$laatste[$veld]=$this->jaarTotalen[$veld];
			}
		}

		$this->pdf->CellBorders=array(array('','','T'),array('','T'),array('L','','T'),array('','T'),array('L','','T'),array('','T'),array('L','','T'),array('','','T'));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array(vertaalTekst("Totaal",$this->pdf->rapport_taal),
											$this->formatGetal($this->jaarTotalen['weging'],2,false,$max),
											$this->formatGetal($this->waarden['Periode']['totaal']['procent'],2,false,$max),
											$this->formatGetal($jaarTotaal['procent'],2,false,$max),//$this->jaarTotalen['portBijdrage']
											$this->formatGetal($this->waarden['Periode']['totaal']['bijdrage'],2,false,$max),
											$this->formatGetal($jaarTotaal['bijdrage'] ,2,false,$max),'',''));//jaarTotalen['portBijdrage']
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders=array('','','L','','L','','L','');
		$this->kostenData=array('rendementMaand'=>$this->maandRendement-$this->waarden['Periode']['totaal']['procent'],
														'rendementYTD'=>$this->ytdRendement-$jaarTotaal['procent'],
														'bijdrageMaand'=>$this->maandRendement-$this->waarden['Periode']['totaal']['bijdrage'],
														'bijdrageYTD'=>$this->ytdRendement-$jaarTotaal['bijdrage']);
		//listarray($this->kostenData);
		$this->pdf->row(array(vertaalTekst("Kosten",$this->pdf->rapport_taal),'',$this->formatGetal($this->maandRendement-$this->waarden['Periode']['totaal']['procent'],2,false,$max),
											$this->formatGetal($this->ytdRendement-$jaarTotaal['procent'],2,false,$max),//$this->jaarTotalen['portBijdrage']
											$this->formatGetal($this->maandRendement-$this->waarden['Periode']['totaal']['bijdrage'],2,false,$max),
											$this->formatGetal($this->ytdRendement-$jaarTotaal['bijdrage'],2,false,$max),'',''));//$this->jaarTotalen['portBijdrage']

		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders=array(array('','','T'),array('','T'),array('L','','T'),array('','T'),array('L','','T'),array('','T'),array('L','','T'),array('','','T'));
		$this->pdf->row(array(vertaalTekst("Totaal na kosten",$this->pdf->rapport_taal),$this->formatGetal($this->jaarTotalen['weging'],2,false,$max),
											$this->formatGetal($this->maandRendement,2,false,$max),
											$this->formatGetal($this->ytdRendement,2,false,$max),
											$this->formatGetal($this->maandRendement,2,false,$max),
											$this->formatGetal($this->ytdRendement,2,false,$max),
											$this->formatGetal($this->waarden['Periode']['totaal']['indexPerf'], 2, true,$max),
											$this->formatGetal($jaarTotaal['indexPerf'], 2, true,$max)));
		unset($this->pdf->CellBorders);
	}

	function BarDiagram($w, $h, $data, $format,$colorArray,$titel)
	{

		$this->pdf->SetFont($this->rapport_font, '', $this->rapport_fontsize);

		$this->pdf->legends=array();
		$this->pdf->wLegend=0;

		$this->pdf->sum=array_sum($data);

		$this->pdf->NbVal=count($data);
		foreach($data as $l=>$val)
		{
			//$p=sprintf('%.1f',$val/$this->sum*100).'%';
			if($val <> 0)
			{
				$p=sprintf('%.1f',$val).'%';
				$legend=str_replace(array('%l','%v','%p'),array(vertaalTekst($l,$this->pdf->rapport_taal),$val,$p),$format);
			}
			else
				$legend='';
			$this->pdf->legends[]=$legend;
			$this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->wLegend);
		}

		$XPage = $this->pdf->GetX();
		$YPage = $this->pdf->GetY();
		$margin = 0;
		$nbDiv=5;
		$legendWidth=10;
		$YDiag = $YPage;
		$hDiag = floor($h);
		$XDiag = $XPage +  $legendWidth;
		$lDiag = floor($w - $legendWidth);
		if($color == null)
			$color=array(155,155,155);
		if ($maxVal == 0) {
			$maxVal = max($data)*1.1;
		}
		if ($minVal == 0) {
			$minVal = min($data)*1.1;
		}
		if($minVal > 0)
			$minVal=0;
		$maxVal=ceil($maxVal/10)*10;

		$offset=$minVal;
		$valIndRepere = ceil(round(($maxVal-$minVal) / $nbDiv,2)*100)/100;
		$bandBreedte = $valIndRepere * $nbDiv;
		$lRepere = floor($lDiag / $nbDiv);
		$unit = $lDiag / $bandBreedte;
		$hBar = 5;//floor($hDiag / ($this->pdf->NbVal + 1));
		$hDiag = $hBar * ($this->pdf->NbVal + 1);

		//echo "$hBar <br>\n";
		$eBaton = floor($hBar * 80 / 100);
		$legendaStep=$unit;

		$legendaStep=$unit/$nbDiv*$bandBreedte;
		//if($bandBreedte/$legendaStep > $nbDiv)
		//  $legendaStep=$legendaStep*5;
		// if($bandBreedte/$legendaStep > $nbDiv)
		//  $legendaStep=$legendaStep*2;
		// if($bandBreedte/$legendaStep > $nbDiv)
		//   $legendaStep=$legendaStep/2*5;
		$valIndRepere=round($valIndRepere/$unit/5)*5;


		$this->pdf->SetLineWidth(0.2);
		$this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
		$this->pdf->SetFillColor($color[0],$color[1],$color[2]);
		$nullijn=$XDiag - ($offset * $unit);

		$i=0;
		$nbDiv=10;

		$this->pdf->SetFont($this->pdf->rapport_font, '', 5);
		if(round($legendaStep,5) <> 0.0)
		{
			//for($x=$nullijn;$x<$XDiag; $x=$x-$legendaStep)
			for($x=$nullijn;$x>$XDiag; $x=$x-$legendaStep)
			{
				$this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
				$this->pdf->setXY($x,$YDiag + $hDiag);
				$this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
				$i++;
				if($i>100)
					break;
			}

			$i=0;
			//for($x=$nullijn;$x>($XDiag+$lDiag); $x=$x+$legendaStep)
			for($x=$nullijn;$x<($XDiag+$lDiag); $x=$x+$legendaStep)
			{
				$this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
				$this->pdf->setXY($x,$YDiag + $hDiag);
				$this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');

				$i++;
				if($i>100)
					break;
			}
		}
		$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
		$i=0;

		$this->pdf->SetXY($XDiag, $YDiag);
		$this->pdf->Cell($lDiag, $hval-4, $titel,0,0,'C');
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);

		foreach($data as $key=>$val)
		{
			$this->pdf->SetFillColor($colorArray[$key][0],$colorArray[$key][1],$colorArray[$key][2]);
			$xval = $nullijn;
			$lval = ($val * $unit);
			$yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
			$hval = $eBaton;
			$this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
			$this->pdf->SetXY($XPage, $yval);
			$this->pdf->Cell($legendWidth , $hval, $this->pdf->legends[$i],0,0,'R');
			$i++;
		}

		//Scales
		$minPos=($minVal * $unit);
		$maxPos=($maxVal * $unit);

		$unit=($maxPos-$minPos)/$nbDiv;
		// echo "$minPos $maxPos -> $minVal $maxVal using $unit met null $nullijn";


	}

	function PieChart($w, $h, $data)
  {

		$col1=array(255,0,0); // rood
		$col2=array(0,255,0); // groen
		$col3=array(255,128,0); // oranje
		$col4=array(0,0,255); // blauw
		$col5=array(255,255,0); // geel
		$col6=array(255,0,255); // paars
		$col7=array(128,128,128); // grijs
		$col8=array(128,64,64); // bruin
		$col9=array(255,255,255); // wit
		$col0=array(0,0,0); //zwart
		$colors=array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col0);
		// standaardkleuren vervangen voor eigen kleuren.
		$startX=$this->pdf->GetX();
		$y = $this->pdf->getY();
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->setXY($startX,$y-4);
		$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);

		$this->pdf->Cell(50,4,vertaalTekst($data['title'], $this->pdf->rapport_taal),0,0,"C");
		$this->pdf->setXY($startX,$y);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
      $margin = 4;
      $hLegend = 2;
      $radius=min($w,$h);

      $radius = floor($radius / 2);
      $XDiag = $XPage + $margin + $radius;
      $YDiag = $YPage + $margin + $radius;

		  $plotData=$data['pieData'];
      //Sectors
      $this->pdf->SetLineWidth(0.2);
      $angleStart = 0;
      $i = 0;
      $aantal=count($plotData);
		  $sum=array_sum($plotData);
      foreach($plotData as $omschrijving=>$val)
      {

				$kleur=array();
				if(isset($data['kleurData'][$omschrijving]))
					$kleur=$data['kleurData'][$omschrijving];
				if($kleur[0] == 0 && $kleur[1] == 0 && $kleur[2] == 0)
					$kleur=$colors[$i];

        $angle = floor(($val * 360) / doubleval($sum));

        if ($angle != 0)
        {
          $angleEnd = $angleStart + $angle;

          $avgAngle=($angleStart+$angleEnd)/360*M_PI;
          $factor=0;

          if($i==($aantal-1))
            $angleEnd=360;



					$this->pdf->SetFillColor($kleur[0],$kleur[1],$kleur[2]);

          $this->pdf->Sector($XDiag+(sin($avgAngle)*$factor), $YDiag-(cos($avgAngle)*$factor), $radius, $angleStart, $angleEnd);
          $angleStart += $angle;
        }
				$legenda[$omschrijving]['kleur']=$kleur;
				$legenda[$omschrijving]['waarde']=$data['data'][$omschrijving]['waardeEur'];
				$legenda[$omschrijving]['percentage']=$data['kleurData'][$omschrijving]['percentage'];
				$i++;
      }

      //Legends
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

      $x1 = $XPage + $margin*2 +$radius *2 ;
      $x2 = $x1 + $hLegend + $margin;
      $y1 = $YPage +$radius-((count($legenda)+1)*($hLegend + 2)/2)  ;
		  $totaal=0;
      foreach($legenda as $omschrijving=>$waarden)
			{
          $this->pdf->SetFillColor($waarden['kleur'][0],$waarden['kleur'][1],$waarden['kleur'][2]);
          $this->pdf->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1);
          $this->pdf->Cell(30,$hLegend,vertaalTekst($omschrijving, $this->pdf->rapport_taal));
				  $this->pdf->Cell(15,$hLegend,$this->formatGetal($waarden['percentage'],1)."%",false,false,'R');
				  $totaal+=$waarden['percentage'];
          $y1+=$hLegend + 2;
      }
		$this->pdf->line($x2,$y1,$x2+45,$y1);
		$this->pdf->SetXY($x2,$y1+2);
		$this->pdf->Cell(30,$hLegend,vertaalTekst('Totaal', $this->pdf->rapport_taal));
		$this->pdf->Cell(15,$hLegend,$this->formatGetal($totaal,1)."%",false,false,'R');

  }


	function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$periode='maand')
	{
		global $__appvar;

		$legendDatum= $data['datum'];
		$data1 = $data['benchmark'];
		if(max($data1) == -100)
			$data1=array();
			
		$data = $data['portefeuille'];

		$bereikdata =   array_merge($data,$data1);


		$XPage = $this->pdf->GetX();
		$YPage = $this->pdf->GetY();
		$margin = 2;
		$YDiag = $YPage + $margin;
		$hDiag = floor($h - $margin * 1);
		$XDiag = $XPage + $margin * 1 ;
		$lDiag = floor($w - $margin * 1 );

		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->SetXY($XPage-8,$YPage-10);
		$this->pdf->Cell(0,3,vertaalTekst('Cumulatieve historische rendementen', $this->pdf->rapport_taal));

		$this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));

		if(is_array($color[0]))
		{
			$color1= $color[1];
			$color = $color[0];
		}

		if($color == null)
			$color=array(12,37,119);
		$this->pdf->SetLineWidth(0.2);

		$this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->SetFillColor($color[0],$color[1],$color[2]);

		if ($maxVal == 0)
		{
			$maxVal = ceil(max($bereikdata));
			if ($maxVal < 0)
				$maxVal = 1;
		}
		if ($minVal == 0)
		{
			$minVal = floor(min($bereikdata));
			if ($minVal > 0)
				$minVal =-1;
		}

		$minVal = floor(($minVal-1) * 1.1);
		$maxVal = ceil(($maxVal+1) * 1.1);
		$legendYstep = ($maxVal - $minVal) / $horDiv;
		$verInterval = ($lDiag / $verDiv);
		$horInterval = ($hDiag / $horDiv);
		$waardeCorrectie = $hDiag / ($maxVal - $minVal);
		$unit = $lDiag / count($data);

		//if($periode=='maand')
		//	$unit = $lDiag / 13;

		for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
			$xpos = $XDiag + $verInterval * $i;

		$this->pdf->SetFont($this->pdf->rapport_font, '', 8);
		$this->pdf->SetTextColor(0,0,0);
		$this->pdf->SetDrawColor(0,0,0);

		$stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
		$unith = $hDiag / (-1 * $minVal + $maxVal);

		$top = $YPage;
		$bodem = $YDiag+$hDiag;
		$absUnit =abs($unith);

		$nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
		$n=0;
		for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
		{
			$skipNull = true;
			$this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
			$this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
			$n++;
			if($n >20)
				break;
		}

		$n=0;
		for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
		{
			$this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
			if($skipNull == true)
				$skipNull = false;
			else
				$this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");

			$n++;
			if($n >20)
				break;
		}
		$yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
		$lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
		$jaren=ceil(count($data)/12);
		for ($i=0; $i<count($data); $i++)
		{
			if($i%$jaren==0)
			{
				$this->pdf->TextWithRotation($XDiag + ($i) * $unit - 7 + $unit, $YDiag + $hDiag + 11, $legendDatum[$i], 25);
			}
			$yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
			$this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
		//	if ($i>0)
		//		$this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color);
	//		if ($i==count($data1)-1)
	//			$this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color);
			$yval = $yval2;
		}

		for ($i=0; $i<count($data)-1; $i++)
		{
			if($i%$jaren==0)
			{
				$lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
				$this->pdf->line($XDiag + ($i + 1) * $unit, $YDiag + $h, $XDiag + ($i + 1) * $unit, $YDiag + $h + 1, $lineStyle);
			}
		}


		$yval = $YDiag + (($maxVal) * $waardeCorrectie) ;

		$lineStyle = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' =>  array(200,0,0));
		for ($i=0; $i<count($data1); $i++)
		{
			$yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
			$this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
			$yval = $yval2;
		}
		$this->pdf->SetLineStyle(array('color'=>array(12,37,119)));


		//   $XPage
		// $YPage
  
		$legendaItems=array('portefeuille');
		if(count($data1)>0)
			$legendaItems[]='benchmark';
		$step=5;
		foreach ($legendaItems as $index=>$item)
		{
			if($index==0)
				$kleur=$color;
			else
				$kleur=array(200,0,0);
			$this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
			$this->pdf->Rect($XPage+$step, $YPage+$h+15, 3, 3, 'DF','',$kleur);
			$this->pdf->SetXY($XPage+3+$step,$YPage+$h+15);
			$this->pdf->Cell(0,3,vertaalTekst($item, $this->pdf->rapport_taal));
			$step+=($w/2);
		}
		$this->pdf->SetDrawColor(0,0,0);
		$this->pdf->SetFillColor(0,0,0);
	}

	function addPerf($vanaf,$tot,$offset)
	{
		global $__appvar;
		$this->pdf->widthA = array(5+$offset,80,30,30,120);
		$this->pdf->alignA = array('L','L','R','R','R');

		// voor kopjes
		$this->pdf->widthB = array(1+$offset,95,30,30,120);
		$this->pdf->alignB = array('L','L','R','R','R');

		if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
			$koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
		else
			$koersQuery = "";

		$kopStyle = "u";
		// ***************************** ophalen data voor afdruk ************************ //
		$kostenPerGrootboek = array();
		$opbrengstenPerGrootboek=array();
		if(db2jul($vanaf)<db2jul($this->tweedePerformanceStart))
			$tweedeVanaf=$vanaf;
		else
			$tweedeVanaf=$this->tweedePerformanceStart;

		$perioden=array(array($vanaf,$tot),array($tweedeVanaf,$tot));
		$totaalKosten=array();
		$totaalOpbrengst=array();

		foreach($perioden as $index=>$periode)
		{
			$totaalA=array();
			$totaalB=array();
			$vanaf=substr($periode[0],0,10);
			$tot=$periode[1];
			$DB = new DB();

			if(substr($vanaf,5,5)=='01-01')
			  $beginJaar=true;
			else
			  $beginJaar=false;
//echo $this->pdf->rapportageValuta."$index $vanaf,$beginJaar <br>\n";ob_flush();
			//$totRapKoers=1;
			$vanRapKoers=getValutaKoers($this->pdf->rapportageValuta,$vanaf);
			$fondsen=berekenPortefeuilleWaarde($this->portefeuille,$vanaf,$beginJaar,$this->pdf->rapportageValuta,$vanaf);
		//	echo "$vanaf <br>\n";ob_flush();
			$totaalWaardeVanaf['totaal']=0;
			foreach($fondsen as $id=>$regel)
			{//echo "$vanaf ".$regel['actuelePortefeuilleWaardeEuro']."<br>\n";
				$totaalWaardeVanaf['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$vanRapKoers);
				if($regel['type']=='rente')
				{
					$totaalB['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$vanRapKoers);
				}
			}

			$totaalWaarde['totaal']=0;
			$totRapKoers=getValutaKoers($this->pdf->rapportageValuta,$tot);
			$fondsen=berekenPortefeuilleWaarde($this->portefeuille,$tot,false,$this->pdf->rapportageValuta,$vanaf);
			$totaal=array();
			foreach($fondsen as $id=>$regel)
			{
				$totaalWaarde['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
				if($regel['type']=='rente')
				{
					$totaalA['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
				}
				if($regel['type']=='fondsen')
				{
					if($regel['valuta']==$this->pdf->rapportageValuta)
					{
						$totaal['totaalB'] += $regel['actuelePortefeuilleWaardeInValuta'];
					  $totaal['totaalA'] += $regel['beginPortefeuilleWaardeInValuta'];
          }
					else
					{
						$totaal['totaalB'] += ($regel['actuelePortefeuilleWaardeEuro'] / $totRapKoers);
						$totaal['totaalA'] += ($regel['beginPortefeuilleWaardeEuro'] / $vanRapKoers);
					}
				}
			}

			$waardeEind[$index] = $totaalWaarde['totaal'];
			$waardeBegin[$index] = $totaalWaardeVanaf['totaal'];
			$waardeMutatie[$index] = $waardeEind[$index] - $waardeBegin[$index];

			/*
			$stortingenTmp = getStortingenKruis($this->portefeuille, $vanaf, $tot, $this->pdf->rapportageValuta,true);
			$stortingen[$index]         = $stortingenTmp['storting']+$stortingenTmp['kruispost'];
			$onttrekkingenTmp=getOnttrekkingenKruis($this->portefeuille, $vanaf, $tot, $this->pdf->rapportageValuta,true);
	//	$interneboeking     = $stortingenTmp['kruispost']-$stortingenTmp['kruispost'];
			$onttrekkingen[$index] =($onttrekkingenTmp['onttrekking']+$onttrekkingenTmp['kruispost'])*-1;
			*/
			$stortingen[$index] = getStortingen($this->portefeuille, $vanaf, $tot, $this->pdf->rapportageValuta);	                         $stortingenTmp = getStortingenKruis($this->portefeuille, $vanaf, $tot, $this->pdf->rapportageValuta,true);
			$onttrekkingen[$index] = getOnttrekkingen($this->portefeuille, $vanaf, $tot, $this->pdf->rapportageValuta)*-1;


			$resultaatVerslagperiode[$index] = $waardeMutatie[$index] - $stortingen[$index] - $onttrekkingen[$index];
			//$rendementProcent[$index] = performanceMeting($this->portefeuille, $vanaf, $tot, $this->pdf->portefeuilledata['PerformanceBerekening'], $this->pdf->rapportageValuta);

			$ongerealiseerdeKoersResultaat[$index] = $totaal['totaalB'] - $totaal['totaalA'];

			$totaalOpbrengst[$index] += $ongerealiseerdeKoersResultaat[$index];

			$gerealiseerdeKoersResultaat[$index] = gerealiseerdKoersresultaat($this->portefeuille, $vanaf, $tot, $this->pdf->rapportageValuta, true);
			$totaalOpbrengst[$index] += $gerealiseerdeKoersResultaat[$index];

			$opgelopenRente[$index] = ($totaalA['totaal'] - $totaalB['totaal']) / $this->pdf->ValutaKoersEind;
			$totaalOpbrengst[$index] += $opgelopenRente[$index];

			$query = "SELECT DISTINCT(Grootboekrekeningen.Grootboekrekening) as Grootboekrekening , Grootboekrekeningen.Omschrijving FROM Grootboekrekeningen WHERE Grootboekrekeningen.Opbrengst = '1' ORDER BY Grootboekrekeningen.Afdrukvolgorde";
			$DB->SQL($query);
			$DB->Query();
			while ($gb = $DB->nextRecord())
			{
				$query = "SELECT  " .
					"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, " .
					"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet " .
					"FROM Rekeningmutaties, Rekeningen, Portefeuilles " .
					"WHERE " .
					"Rekeningmutaties.Rekening = Rekeningen.Rekening AND " .
					"Rekeningen.Portefeuille = '" . $this->portefeuille . "' AND " .
					"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND " .
					"Rekeningmutaties.Verwerkt = '1' AND " .
					"Rekeningmutaties.Boekdatum > '" . $vanaf . "' AND " .
					"Rekeningmutaties.Boekdatum <= '" . $tot . "' AND " .
					"Rekeningmutaties.Grootboekrekening = '" . $gb['Grootboekrekening'] . "' ";
				$DB2 = new DB();
				$DB2->SQL($query);
				$DB2->Query();

				while ($opbrengst = $DB2->nextRecord())
				{
					if($gb['Grootboekrekening']=='RENTE' || $gb['Grootboekrekening']=='RENOB')
						$gb['Omschrijving']="Ontvangen rente";
					if($gb['Grootboekrekening']=='DIV')
						$gb['Omschrijving']="Ontvangen dividend";


					$opbrengstWaarde=($opbrengst['totaalcredit'] - $opbrengst['totaaldebet']);
					if($opbrengstWaarde <> 0 )
					{
					  $opbrengstenPerGrootboek[$gb['Omschrijving']][$index] += $opbrengstWaarde;
					  $totaalOpbrengst[$index] += $opbrengstWaarde;
					}
				}
			}
			$opbrengstenPerGrootboek['Ingehouden bronbelasting'][$index] += 0;

		
			$query = "SELECT DISTINCT(Grootboekrekeningen.Grootboekrekening), Grootboekrekeningen.Omschrijving FROM Grootboekrekeningen WHERE Grootboekrekeningen.Kosten = '1' ORDER BY Grootboekrekeningen.Afdrukvolgorde";
			$DB = new DB();
			$DB->SQL($query);
			$DB->Query();

			while ($gb = $DB->nextRecord())
			{
				$query = "SELECT  " .
					"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, " .
					"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet " .
					"FROM Rekeningmutaties, Rekeningen, Portefeuilles " .
					"WHERE " .
					"Rekeningmutaties.Rekening = Rekeningen.Rekening AND " .
					"Rekeningen.Portefeuille = '" . $this->portefeuille . "' AND " .
					"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND " .
					"Rekeningmutaties.Verwerkt = '1' AND " .
					"Rekeningmutaties.Boekdatum > '" . $vanaf . "' AND " .
					"Rekeningmutaties.Boekdatum <= '" . $tot . "' AND " .
					"Rekeningmutaties.Grootboekrekening = '" . $gb['Grootboekrekening'] . "' ";
				$DB2 = new DB();
				$DB2->SQL($query);
				$DB2->Query();
				$grootboekLookup=array();
				while ($kosten = $DB2->nextRecord())
				{
					$kostenWaarde=($kosten['totaalcredit'] - $kosten['totaaldebet']);
					if($kostenWaarde <> 0)
					{
						if($gb['Grootboekrekening']=='ROER' || $gb['Grootboekrekening']=='TOB')
						{
							$gb['Omschrijving'] = "Ingehouden bronbelasting";
							$opbrengstenPerGrootboek[$gb['Omschrijving']][$index] += $kostenWaarde;
              $totaalOpbrengst[$index] += $kostenWaarde;
						}
						else
						{
							$kostenPerGrootboek[$gb['Omschrijving']][$index] = $kostenWaarde;
							$grootboekLookup[$gb['Grootboekrekening']] = $gb['Omschrijving'];
							//if($gb['Grootboekrekening']<>'DIVB')
							$totaalKosten[$index] += $kostenWaarde;
						}

					}
				}

			}
			//$kostenProcent = ($totaalKosten / $waardeEind) * 100;
			// het overgebleven is de koers resultaat op valutas (om de getalletjes te laten kloppen).
			$koersResulaatValutas[$index] = $resultaatVerslagperiode[$index] - ($totaalOpbrengst[$index] + $totaalKosten[$index]);
			$totaalOpbrengst[$index] += $koersResulaatValutas[$index];
			// ***************************** einde ophalen data voor afdruk ************************ //
		}

		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];

		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		$eersteJul=db2jul($perioden[0][1]);
		$eersteDatum=vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$eersteJul)],$this->pdf->rapport_taal)." ".date('Y',$eersteJul);
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->setX($this->pdf->marge);
		$this->pdf->CellBorders=array('','U','U','U');
		$this->pdf->row(array("",vertaalTekst("Ontwikkeling van het vermogen",$this->pdf->rapport_taal),
											$eersteDatum,
											vertaalTekst("Cumulatief",$this->pdf->rapport_taal)." ".date("Y",db2jul($perioden[1][1]))));
		$this->pdf->CellBorders=array();
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Beginwaarde totaal vermogen",$this->pdf->rapport_taal),                  $this->formatGetal($waardeBegin[0],2,true),             $this->formatGetal($waardeBegin[1],2,true)));
		$this->pdf->row(array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal),   $this->formatGetal($stortingen[0],2),                   $this->formatGetal($stortingen[1],2)));
		$this->pdf->row(array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($onttrekkingen[0],2),                $this->formatGetal($onttrekkingen[1],2)));
		$this->pdf->row(array("",vertaalTekst("Ongerealiseerde koersresultaten",$this->pdf->rapport_taal),              $this->formatGetal($ongerealiseerdeKoersResultaat[0],2),$this->formatGetal($ongerealiseerdeKoersResultaat[1],2)));
		$this->pdf->row(array("",vertaalTekst("Gerealiseerde koersresultaten",$this->pdf->rapport_taal),                $this->formatGetal($gerealiseerdeKoersResultaat[0],2),  $this->formatGetal($gerealiseerdeKoersResultaat[1],2)));
		$this->pdf->row(array("",vertaalTekst("Koersresultaten valuta's",$this->pdf->rapport_taal),                     $this->formatGetal($koersResulaatValutas[0],2),         $this->formatGetal($koersResulaatValutas[1],2)));
		$this->pdf->row(array("",vertaalTekst("Resultaat opgelopen rente",$this->pdf->rapport_taal),                    $this->formatGetal($opgelopenRente[0],2),               $this->formatGetal($opgelopenRente[1],2)));
		foreach($opbrengstenPerGrootboek as $key=>$values)
			$this->pdf->row(array("",vertaalTekst($key,$this->pdf->rapport_taal),                                         $this->formatGetal($values[0],2),                       $this->formatGetal($values[1],2)));
    //if($kostenPerGrootboek[$grootboekLookup['DIVB']][0] <> 0 || $kostenPerGrootboek[$grootboekLookup['DIVB']][1] <> 0 )
	//		$this->pdf->row(array("",vertaalTekst($grootboekLookup['DIVB'],$this->pdf->rapport_taal),                     $this->formatGetal($kostenPerGrootboek[$grootboekLookup['DIVB']][0],2),                    $this->formatGetal($kostenPerGrootboek[$grootboekLookup['DIVB']][1],2)));

		$this->pdf->row(array("",vertaalTekst("Diverse kosten",$this->pdf->rapport_taal),                                         $this->formatGetal($totaalKosten[0],2),                    $this->formatGetal($totaalKosten[1],2)));
		$this->pdf->CellBorders=array('','T','T','T');
		$this->pdf->row(array("",vertaalTekst("Eindwaarde totaalvermogen",$this->pdf->rapport_taal),                    $this->formatGetal($waardeEind[0],2),                   $this->formatGetal($waardeEind[1],2)));
		$this->pdf->CellBorders=array();
		$this->pdf->ln();
	//	$this->pdf->row(array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal),                $this->formatGetal($resultaatVerslagperiode[0],2),      $this->formatGetal($resultaatVerslagperiode[1],2)));

	}

	function addZorgBar($w,$h,$color=null, $horDiv=4, $verDiv=4)
	{
		global $__appvar;
		include_once("rapport/Zorgplichtcontrole.php");
		$zorgplicht = new Zorgplichtcontrole();
		$pdata=$this->pdf->portefeuilledata;
		if($pdata['Portefeuille']=='000000')
		  $pdata['Portefeuille']=$pdata['PortefeuilleOrigineel'];
		$zpwaarde=$zorgplicht->zorgplichtMeting($pdata,$this->rapportageDatum); //listarray($zpwaarde);
		$db=new DB();
		$query="SELECT Zorgplicht,Omschrijving FROM Zorgplichtcategorien WHERE vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
		$db->SQL($query);
		$db->Query();
		while($data=$db->NextRecord())
			$zorgplichtOmschrijving[$data['Zorgplicht']]=$data['Omschrijving'];
		$categorien=array();
		foreach($zpwaarde['categorien'] as $categorie=>$data)
		{
			$data['Norm']=($data['Maximum']-$data['Minimum'])/2;
			$categorien[$categorie]=$data;
		}

		foreach($zpwaarde['conclusie'] as $data)
		{
			foreach($categorien as $categorie=>$categorieData)
			{
				if($data[0]==$categorie)
				{
					$categorien[$categorie]['percentage']=$data[2];
				}
				// $categorien[$categorie]['categorien'][$data[0]]=$data[2];
			}
		}


		$XPage = $this->pdf->GetX();
		$YPage = $this->pdf->GetY();
		$margin = 0;
		$YDiag = $YPage + $margin;
		$hDiag = floor($h - $margin * 1);
		$XDiag = $XPage + $margin * 1 ;
		$lDiag = floor($w - $margin * 1 );

		$this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));

		if(is_array($color[0]))
		{
			$color1= $color[1];
			$color = $color[0];
		}

		if($color == null)
			$color=array($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);

		$this->pdf->SetLineWidth(0.2);

		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->SetXY($XPage-8,$YPage-10);
		$this->pdf->Cell(0,3,vertaalTekst('Huidige allocatie en bandbreedtes',$this->pdf->rapport_taal));

		$this->pdf->SetFillColor($color[0],$color[1],$color[2]);

		$minVal = 0;
		$maxVal = 100;
		$verInterval = ($lDiag / $verDiv);
		$waardeCorrectie = $hDiag / ($maxVal - $minVal);
		$unit = $lDiag / count($categorien)+1;

		for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
			$xpos = $XDiag + $verInterval * $i;

		$this->pdf->SetFont($this->pdf->rapport_font, '', 8);
		$this->pdf->SetTextColor(0,0,0);
		$this->pdf->SetDrawColor(0,0,0);

		$stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
		$unith = $hDiag / (-1 * $minVal + $maxVal);
		$top = $YPage;
		$bodem = $YDiag+$hDiag;
		$absUnit =abs($unith);

		$nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
		$n=0;
		for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
		{
			$skipNull = true;
			$this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
			$this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
			$n++;
			if($n >20)
				break;
		}

		$n=0;
		for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
		{
			$this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
			if($skipNull == true)
				$skipNull = false;
			else
				$this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");

			$n++;
			if($n >20)
				break;
		}
		$yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
		$lineStyle = array('width' => 5.0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);




		$i=0;
		foreach($categorien as $categorie=>$data)
		{

		  $this->pdf->TextWithRotation($XDiag+($i)*$unit-7+$unit,$YDiag+$hDiag+11,$legendDatum[$i],25);
			$yval = $YDiag + (($maxVal-$data['Minimum']) * $waardeCorrectie) ;
			$yval2 = $YDiag + (($maxVal-$data['Maximum']) * $waardeCorrectie) ;

			$lineStyle = array('width' => 5.0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
			$this->pdf->line($XDiag+($i+0.5)*$unit, $yval, $XDiag+($i+0.5)*$unit, $yval2,$lineStyle );

			$yval = $YDiag + (($maxVal-$data['percentage']) * $waardeCorrectie) ;
			$lineStyle = array('width' => 2.0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,176,80));
			$this->pdf->line($XDiag+($i+0.4)*$unit, $yval, $XDiag+($i+0.6)*$unit, $yval,$lineStyle );

			$this->pdf->setXY($XDiag+($i+0.5)*$unit-1,$YDiag+$h+2);
			$this->pdf->Cell(2,4,vertaalTekst($zorgplichtOmschrijving[$data['Zorgplicht']], $this->pdf->rapport_taal),0,0,"C");

//			$this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color);

			$i++;
		}

		$this->pdf->SetLineStyle(array('width' => 0.1,'color'=>array(0,0,0)));
		$legendaItems=array('huidige positionering'=>array(0,176,80),'bandbreedte'=>$color);
		$step=5;
		foreach ($legendaItems as $omschrijving=>$kleur)
		{
			$this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
			$this->pdf->Rect($XPage+$step, $YPage+$h+10, 3, 3, 'DF','',$kleur);
			$this->pdf->SetXY($XPage+3+$step,$YPage+$h+10);
			$this->pdf->Cell(0,3,vertaalTekst($omschrijving,$this->pdf->rapport_taal));
			$step+=($w/2);
		}
		$this->pdf->SetDrawColor(0,0,0);
		$this->pdf->SetFillColor(0,0,0);


	}

	function VBarDiagram2($w, $h, $data, $format, $color=null,$nbDiv=4,$numBars=0)
	{
		global $__appvar;
		$legendDatum = $data['datum'];
		//$data = $data['portefeuille'];


		//$this->pdf->SetLegends($data,$format);

		$XPage = $this->pdf->GetX();
		$YPage = $this->pdf->GetY();
		$margin = 2;
		$YstartGrafiek = $YPage - floor($margin * 1);
		$hGrafiek = ($h - 0 * 1);
		$XstartGrafiek = $XPage + $margin * 1 ;
		$bGrafiek = ($w - $margin * 1);

		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->SetXY($XPage,$YPage-$h-12);
		$this->pdf->Cell(0,3,vertaalTekst('Rendementen per maand',$this->pdf->rapport_taal));
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

		$this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $w- $margin, $hGrafiek,'D',''); //,array(245,245,245)
		if($color == null)
			$color=array($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);

		$maxVal=0;
		$minVal=0;
		$maanden=array();
		foreach($data as $maand=>$maandData)
		{
			$maanden[$maand]=$maand;
			foreach($maandData as $type=>$waarde)
			{
				if($waarde > $maxVal)
					$maxVal = $waarde;
				if($waarde < $minVal)
					$minVal = $waarde;
			}
		}
		if($maxVal > 1)
			$maxVal=ceil($maxVal);
		if($minVal < -1)
			$minVal=floor($minVal);
		$minVal = $minVal * 1.1;
		$maxVal = $maxVal * 1.1;
		if ($maxVal <0)
			$maxVal=0;

		if($minVal < 0)
		{
			$unit = $hGrafiek / (-1 * $minVal + $maxVal) * -1;
			$nulYpos =  $unit * (-1 * $minVal);
		}
		else
		{
			$unit = $hGrafiek / $maxVal * -1;
			$nulYpos =0;
		}

		$horDiv = 10;
		$horInterval = $hGrafiek / $horDiv;
		$bereik = $hGrafiek/$unit;

		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-1);
		$this->pdf->SetTextColor(0,0,0);

		$stapgrootte = ceil(abs($bereik)/$horDiv*10)/10;
		$top = $YstartGrafiek-$h;
		$bodem = $YstartGrafiek;
		$absUnit =abs($unit);

		$nulpunt = $YstartGrafiek + $nulYpos;
		$n=0;

		for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
		{
			$skipNull = true;
			$this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
			$this->pdf->setXY($XstartGrafiek-12, $i);
			$this->pdf->MultiCell(12, 3, ($n*$stapgrootte*-1) ." %", 0, 'R');
		//	$this->pdf->Text($XstartGrafiek-8.5, $i, ($n*$stapgrootte*-1) ." %");
			$n++;
			if($n >20)
				break;
		}

		$n=0;
		for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
		{
			$this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
			if($skipNull == true)
				$skipNull = false;
			else
			{
				//$this->pdf->Text($XstartGrafiek - 8.5, $i, $n * $stapgrootte . " %");

				$this->pdf->setXY($XstartGrafiek-12, $i);
				$this->pdf->MultiCell(12, 3, ($n*$stapgrootte) ." %", 0, 'R');
			}
			$n++;
			if($n >20)
				break;
		}

		$numBars=count($data);
		if($numBars > 0)
			$this->pdf->NbVal=$numBars;

	//	$colors=array('allocateEffect'=>array(0,52,121),'selectieEffect'=>array(87,165,25),'attributieEffect'=>array(108,31,128)); //

		$vBar = ($bGrafiek /$numBars)/2; //4
		//$bGrafiek = $vBar * ($this->pdf->NbVal );
		$eBaton = ($vBar * 50 / 100);
		$this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
		$this->pdf->SetLineWidth(0.2);
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-1);
		$this->pdf->SetFillColor($color[0],$color[1],$color[2]);
		$i=0;
	//	$this->pdf->SetFont($this->pdf->rapport_font, '', 6);

		foreach($data as $maand=>$maandData)
		{

			foreach($maandData as $type=>$val)
			{
			//	$color=$colors[$type];
				//Bar
				$xval = $XstartGrafiek + ($i + 1) * $vBar - $eBaton / 2;
				$lval = $eBaton;
				$yval = $YstartGrafiek + $nulYpos;
				$hval = ($val * $unit);
				$this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$color);
				$this->pdf->SetTextColor(255,255,255);
				if(abs($hval) > 3 && $eBaton > 4)
				{
				//$this->pdf->SetXY($xval, $yval+($hval/2)-2);
				//	$this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
				}
				$this->pdf->SetTextColor(0,0,0);
				$i++;
				$lastVal=$val;
			}



		//	$this->pdf->Text($XstartGrafiek + ($i -2) * $vBar - $eBaton / 2,$YstartGrafiek +3 ,date('M',));

			$this->pdf->line( $XstartGrafiek + ($i-1) * $vBar, $YstartGrafiek, $XstartGrafiek + ($i-1) * $vBar,$YstartGrafiek +11 );
			$i++;
			$this->pdf->setXY($XstartGrafiek + ($i -2) * $vBar - $eBaton / 2,$YstartGrafiek +1 );
			$this->pdf->MultiCell(12, 3, date("M\ny",db2jul($maand)), 0, 'C');
			$this->pdf->setXY($XstartGrafiek + ($i -2) * $vBar - $eBaton / 2,$YstartGrafiek +8 );
			$this->pdf->MultiCell(12, 3, $this->formatGetal($lastVal,2), 0, 'C');


		}
		$this->pdf->line( $XstartGrafiek + $bGrafiek, $YstartGrafiek, $XstartGrafiek +$bGrafiek,$YstartGrafiek +11 );
		$this->pdf->line( $XstartGrafiek, $YstartGrafiek+11, $XstartGrafiek + $bGrafiek, $YstartGrafiek +11 );
		$this->pdf->line( $XstartGrafiek, $YstartGrafiek+7, $XstartGrafiek + $bGrafiek, $YstartGrafiek +7 );
		$this->pdf->setXY($XstartGrafiek +$bGrafiek,$YstartGrafiek +1 );
		$this->pdf->MultiCell(12, 3,"\nYTD", 0, 'C');
		$this->pdf->setXY($XstartGrafiek +$bGrafiek ,$YstartGrafiek +8 );
		$this->pdf->MultiCell(12, 3, $this->formatGetal($this->ytdRendement,2)."%", 0, 'C');
		//

			}



	function getMaanden($julBegin, $julEind)
	{
		$eindjaar = date("Y",$julEind);
		$eindmaand = date("m",$julEind);
		$beginjaar = date("Y",$julBegin);
		$startjaar = date("Y",$julBegin);
		$beginmaand = date("m",$julBegin);

		$i=0;
		$stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
		while ($counterStart < $stop)
		{
			$counterStart = mktime (0,0,0,$beginmaand+$i,0,$beginjaar);
			$counterEnd   = mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar);
			if($counterEnd >= $julEind)
				$counterEnd = $julEind;

			if($i == 0)
			{
				$datum[$i]['start'] = date('Y-m-d',$julBegin);
			}
			else
				$datum[$i]['start'] =date('Y-m-d',$counterStart);

			$datum[$i]['stop']=date('Y-m-d',$counterEnd);

			if($datum[$i]['start'] ==  $datum[$i]['stop'])
				unset($datum[$i]);
			$i++;
		}
		return $datum;
	}


	function BerekenMutaties2($beginDatum,$eindDatum,$portefeuille,$valuta='EUR')
	{
		if(substr($beginDatum,5,5)=='12-31')
			$beginDatum=(substr($beginDatum,0,4)+1).'-01-01';

		if ($valuta != "EUR" )
			$koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
		else
			$koersQuery = "";

		$totaalWaarde =array();
		$db = new DB();

		$query="SELECT Portefeuilles.Startdatum FROM Portefeuilles WHERE Portefeuilles.Portefeuille='$portefeuille'";
		$db->SQL($query);
		$startDatum=$db->lookupRecord();

		$query="SELECT
Beleggingscategorien.Beleggingscategorie,
Beleggingscategorien.Omschrijving,
Beleggingscategorien.Afdrukvolgorde,
BeleggingscategoriePerFonds.Vermogensbeheerder,
Portefeuilles.Portefeuille
FROM
Beleggingscategorien
Inner Join BeleggingscategoriePerFonds ON Beleggingscategorien.Beleggingscategorie = BeleggingscategoriePerFonds.Beleggingscategorie
Inner Join Portefeuilles ON BeleggingscategoriePerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
WHERE Portefeuilles.Portefeuille='$portefeuille'
GROUP BY Beleggingscategorien.Beleggingscategorie
ORDER BY Afdrukvolgorde desc";
		$db->SQL($query);
		$db->Query();
		$this->categorieVolgorde['LIQ']=0;
		while($data=$db->nextRecord())
			$this->categorieVolgorde[$data['Beleggingscategorie']]=0;

		if(db2jul($beginDatum) <= db2jul($startDatum['Startdatum']))
		{
			if($this->voorStartdatumNegeren==true && db2jul($eindDatum) <= db2jul($startDatum['Startdatum']))
				return array('periode'=>$beginDatum."->".$eindDatum,'periodeForm'=>date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum)));

			$wegingsDatum=date('Y-m-d',db2jul($startDatum['Startdatum'])+86400); //$startDatum['Startdatum'];
		}
		else
			$wegingsDatum=$beginDatum;

		$startjaar=substr($beginDatum,0,4);
		if(db2jul($beginDatum) == mktime (0,0,0,1,1,$startjaar))
			$beginjaar = true;
		else
			$beginjaar = false;

		$koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,$valuta,true);
		//echo "att $koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,'EUR',true);<br>\n";

		$fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($portefeuille,$beginDatum,$beginjaar,$valuta,$beginDatum);

		if($valuta <> 'EUR')
			$valutaKoers=getValutaKoers($valuta,$beginDatum);
		else
			$valutaKoers=1;
		foreach ($fondswaarden['beginmaand'] as $regel)
		{
			$regel['actuelePortefeuilleWaardeEuro']=$regel['actuelePortefeuilleWaardeEuro']/$valutaKoers;
			$totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
			if($regel['type']=='rente' && $regel['fonds'] != '')
				$totaalWaarde['renteBegin'] += $regel['actuelePortefeuilleWaardeEuro'];
		}

		$fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$eindDatum,false,$valuta,$beginDatum);
		$categorieVerdeling=$this->categorieVolgorde;

		// listarray($categorieVerdeling);
		if($valuta <> 'EUR')
			$valutaKoers=getValutaKoers($valuta,$eindDatum);
		else
			$valutaKoers=1;

		foreach ($fondswaarden['eindmaand'] as $regel)
		{
			$regel['actuelePortefeuilleWaardeEuro']=$regel['actuelePortefeuilleWaardeEuro']/$valutaKoers;
			$totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];

			if($regel['type']=='fondsen')
			{
				$totaalWaarde['beginResultaat'] += $regel['beginPortefeuilleWaardeEuro'];
				$totaalWaarde['eindResultaat'] += $regel['actuelePortefeuilleWaardeEuro'];
				$categorieVerdeling[$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
			}
			elseif($regel['type']=='rente' && $regel['fonds'] != '')
			{
				$totaalWaarde['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
				$categorieVerdeling['VAR'] += $regel['actuelePortefeuilleWaardeEuro'];
			}
			elseif($regel['type']=='rekening')
			{
				$categorieVerdeling['LIQ'] += $regel['actuelePortefeuilleWaardeEuro'];
			}
		}


		$ongerealiseerd=($totaalWaarde['eindResultaat']-$totaalWaarde['beginResultaat']);
		$DB=new DB();

		$query = "SELECT ".
			"SUM(((TO_DAYS('".$eindDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
			"  / (TO_DAYS('".$eindDatum."') - TO_DAYS('".$wegingsDatum."')) ".
			"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
			"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
			"FROM  (Rekeningen, Portefeuilles,Grootboekrekeningen )
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
			"WHERE ".
			"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
			"Rekeningmutaties.Verwerkt = '1' AND ".
			"Rekeningmutaties.Boekdatum > '".$beginDatum."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$eindDatum."' AND
	Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
		$DB->SQL($query);
		$DB->Query();
		$weging = $DB->NextRecord();

		if($totaalWaarde['begin']==0)
			$gemiddelde = $totaalWaarde['begin'] + $weging['totaal2'];
		else
		  $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
		$performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging['totaal2']) / $gemiddelde) * 100;
//echo "<br>\n $query <br>\n";
//echo "perf $eindDatum  $wegingsDatum $performance = (((".$totaalWaarde['eind']." - ".$totaalWaarde['begin'].") - ".$weging['totaal2'].") / $gemiddelde) * 100;<br>\n";
		$waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
		$stortingen = getStortingen($portefeuille,$beginDatum, $eindDatum,$valuta);
		$onttrekkingen = getOnttrekkingen($portefeuille,$beginDatum, $eindDatum,$valuta);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;

		$query = "SELECT SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery)  AS totaalkosten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Kosten = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
		$db->SQL($query);
		$kosten = $db->lookupRecord();

		$query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaalOpbrengsten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Opbrengst = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
		$db->SQL($query);
		$opbrengsten = $db->lookupRecord();

		$opgelopenRente=$totaalWaarde['renteEind']-$totaalWaarde['renteBegin'];
		$valutaResultaat=$resultaatVerslagperiode-($koersResultaat+$ongerealiseerd+$opbrengsten['totaalOpbrengsten']+$kosten['totaalkosten']+$opgelopenRente);
		$ongerealiseerd+=$valutaResultaat;

		foreach ($categorieVerdeling as $cat=>$waarde)
			$categorieVerdeling[$cat]=$waarde."";

		$data['valuta']=$valuta;
		$data['periode']= $beginDatum."->".$eindDatum;
		$data['periodeForm']= date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum));
		$data['waardeBegin']=round($totaalWaarde['begin'],2);
		$data['waardeHuidige']=round($totaalWaarde['eind'],2);
		$data['waardeMutatie']=round($waardeMutatie,2);
		$data['stortingen']=round($stortingen,2);
		$data['onttrekkingen']=round($onttrekkingen,2);
		$data['resultaatVerslagperiode'] = round($resultaatVerslagperiode,2);
		$data['gemiddelde'] = $gemiddelde;
		$data['kosten'] = round($kosten['totaalkosten'],2);
		$data['opbrengsten'] = round($opbrengsten['totaalOpbrengsten'],2);
		$data['performance'] =$performance;
		$data['ongerealiseerd'] =$ongerealiseerd;
		$data['rente'] = $opgelopenRente;
		$data['gerealiseerd'] =$koersResultaat;
		$data['extra']['cat']=$categorieVerdeling;
		return $data;

	}

	function indexPerformance($fonds,$van,$tot)
	{
		global $__appvar;
		$DB = new DB();



		$query="SELECT fonds,percentage FROM benchmarkverdeling WHERE benchmark='$fonds'";
		$DB->SQL($query);
		$DB->Query();
		$verdeling=array();
		while($data=$DB->nextRecord())
			$verdeling[$data['fonds']]=$data['percentage'];

		if(count($verdeling)==0)
			$verdeling[$fonds]=100;

		$totalPerf=0;
		foreach($verdeling as $fonds=>$percentage)
		{

			$query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '".substr($tot,0,4)."-01-01' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
			$DB->SQL($query);
			$janKoers=$DB->lookupRecord();

			$query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$van' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
			$DB->SQL($query);
			$startKoers=$DB->lookupRecord();

			$query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$tot' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
			$DB->SQL($query);
			$eindKoers=$DB->lookupRecord();

			$perfVoorPeriode=($startKoers['Koers'] - $janKoers['Koers']) / ($janKoers['Koers']);
			$perfJaar=($eindKoers['Koers'] - $janKoers['Koers']) / ($janKoers['Koers']);
			$perf=$perfJaar-$perfVoorPeriode;
			//$perf=($eindKoers['Koers'] - $startKoers['Koers']) / ($startKoers['Koers']);
			$totalPerf+=($perf*$percentage/100);
		//	echo "$fonds $tot ".$eindKoers['Koers']." $perf<br>\n";
		}
	//	echo $fonds." $van -> $tot $totalPerf <br>\n";ob_flush();
	//	$perf= $totalPerf;
	//	$tmp= array('perf'=>$perf,'bijdrage'=>$perf*$fondsData['Percentage'],'datum'=>$tot,'percentage'=>$fondsData['Percentage'],'categorie'=>$categorie);//'koersVan'=>$startKoers['Koers'],'koersEind'=>$eindKoers['Koers'] //,'waarden'=>$waarden)

		return $totalPerf;
	}

	function getWaarden($datumBegin,$datumEind,$portefeuille,$specifiekeIndex='',$methode='maanden',$valuta='EUR',$output='')
	{
		if(is_array($portefeuille))
		{
			$portefeuilles=$portefeuille[1];
			$portefeuille=$portefeuille[0];
		}
		$db=new DB();
		$julBegin = db2jul($datumBegin);
		$beginDatum=date("Y-m-d",$julBegin);
		$julEind = db2jul($datumEind);

		$eindjaar = date("Y",$julEind);
		$eindmaand = date("m",$julEind);
		$beginjaar = date("Y",$julBegin);
		$startjaar = date("Y",$julBegin);
		$beginmaand = date("m",$julBegin);
		$begindag = date("d",$julBegin);

		$vorigeIndex = 100;
		$stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
		$datum == array();

		if($methode=='maanden')
		{
			$datum=$this->getMaanden($julBegin,$julEind);
			$type='m';
		}



		$i=1;
		$indexData['index']=100;
		$indexData['specifiekeIndex']=100;
		$kwartaalBegin=100;

		$huidigeIndex=$specifiekeIndex;
		$jsonOutput=array('label'=>$portefeuille,'data'=>array());
		foreach ($datum as $periode)
		{
			if($specifiekeIndex != '')
			{
				//if($specifiekeIndex )
        /*
//				$query="SELECT specifiekeIndex FROM HistorischeSpecifiekeIndex WHERE portefeuille='$portefeuille' AND tot > '".$periode['stop']."' ORDER BY tot desc limit 1";
				$db->SQL($query);
				$oldIndex=$db->lookupRecord();

				if($oldIndex['specifiekeIndex'] <> '')
				{
					$specifiekeIndex=$oldIndex['specifiekeIndex'];
					unset($startSpecifiekeIndexKoers);
				}
				else
				{
					if($huidigeIndex <> $specifiekeIndex)
						unset($startSpecifiekeIndexKoers);
					$specifiekeIndex=$huidigeIndex;
				}
        */
/*
				if(empty($startSpecifiekeIndexKoers))
				{
					$query = "SELECT Koers FROM Fondskoersen WHERE fonds = '".$specifiekeIndex."' AND Datum <= '".$periode['start']."' ORDER BY Datum DESC limit 1 ";
					$db->SQL($query);
					$specifiekeIndexData = $db->lookupRecord();
					$startSpecifiekeIndexKoers=$specifiekeIndexData['Koers'];
				}
				$query = "SELECT Koers FROM Fondskoersen WHERE fonds = '".$specifiekeIndex."' AND Datum <= '".$periode['stop']."' ORDER BY Datum DESC limit 1 ";
				$db->SQL($query);
				$specifiekeIndexData = $db->lookupRecord();
				$specifiekeIndexKoers = $specifiekeIndexData['Koers'];
*/
				$specifiekeIndexWaarden[$i] = (1+$this->indexPerformance($specifiekeIndex,$periode['start'],$periode['stop']))*100;//=($specifiekeIndexKoers/$startSpecifiekeIndexKoers)*100;

			}

			$query = "SELECT indexWaarde, Datum, PortefeuilleWaarde, PortefeuilleBeginWaarde, Stortingen, Onttrekkingen, Opbrengsten, Kosten ,Categorie, gerealiseerd,ongerealiseerd,rente,extra
		            FROM HistorischePortefeuilleIndex
		            WHERE
		            Categorie = 'Totaal' AND periode='$type' AND
		            portefeuille = '".$portefeuille."' AND
		            Datum = '".substr($periode['stop'],0,10)."' ";

			if(db2jul($periode['start']) == db2jul($periode['stop']))
			{

			}
			elseif($db->QRecords($query) > 0 && ($valuta == 'EUR' || $valuta == '' || $this->forceDbLoad==true) )
			{
				$dbData = $db->nextRecord();
				$indexData['periodeForm'] = jul2form(db2jul($periode['start']))." - ".jul2form(db2jul($periode['stop']));
				$indexData['periode']= $periode['start']."->".$periode['stop'];
				$indexData['waardeMutatie'] = $dbData['PortefeuilleWaarde']-$dbData['PortefeuilleBeginWaarde'];
				$indexData['waardeBegin'] = $dbData['PortefeuilleWaarde']-$indexData['waardeMutatie'];
				$indexData['waardeHuidige'] = $dbData['PortefeuilleWaarde'];
				$indexData['stortingen'] = $dbData['Stortingen'];
				$indexData['onttrekkingen'] = $dbData['Onttrekkingen'];
				$indexData['resultaatVerslagperiode'] =  $indexData['waardeMutatie'] - $indexData['stortingen'] + $indexData['onttrekkingen'];
				$indexData['kosten'] = $dbData['Kosten'];
				$indexData['opbrengsten'] = $dbData['Opbrengsten'];
				$indexData['performance'] = $dbData['indexWaarde'];
				//$indexData['resultaatVerslagperiode'] = $dbData['Opbrengsten']-$dbData['Kosten'];
				$indexData['gerealiseerd'] = $dbData['gerealiseerd'];
				$indexData['ongerealiseerd'] = $dbData['ongerealiseerd'];
				$indexData['rente'] = $dbData['rente'];
				$indexData['extra'] = unserialize($dbData['extra']);
			}
			else
			{
				if(isset($portefeuilles) && ($valuta == 'EUR' || $valuta == ''  || $this->forceDbLoad==true ))
				{
					$query = "SELECT  Datum, sum(PortefeuilleWaarde) as PortefeuilleWaarde, sum(PortefeuilleBeginWaarde) as PortefeuilleBeginWaarde,
	  	    sum(Stortingen) as Stortingen, sum(Onttrekkingen) as Onttrekkingen, sum(Opbrengsten) as Opbrengsten, sum(Kosten) as Kosten ,Categorie, SUM(gerealiseerd) as gerealiseerd,
	  	    sum(ongerealiseerd) as ongerealiseerd, sum(rente) as rente, sum(gemiddelde) as gemiddelde,extra
		            FROM HistorischePortefeuilleIndex
		            WHERE
		            Categorie = 'Totaal' AND periode='$type' AND
		            portefeuille IN ('".implode("','",$portefeuilles)."') AND
		            Datum = '".substr($periode['stop'],0,10)."' GROUP BY Datum";

					if($db->QRecords($query) > 0)
					{
						$dbData = $db->nextRecord();
						$indexData['periodeForm'] = jul2form(db2jul($periode['start']))." - ".jul2form(db2jul($periode['stop']));
						$indexData['periode']= $periode['start']."->".$periode['stop'];
						$indexData['waardeMutatie'] = $dbData['PortefeuilleWaarde']-$dbData['PortefeuilleBeginWaarde'];
						$indexData['waardeBegin'] = $dbData['PortefeuilleWaarde']-$indexData['waardeMutatie'];
						$indexData['waardeHuidige'] = $dbData['PortefeuilleWaarde'];
						$indexData['stortingen'] = $dbData['Stortingen'];
						$indexData['onttrekkingen'] = $dbData['Onttrekkingen'];
						$indexData['resultaatVerslagperiode'] =  $indexData['waardeMutatie'] - $indexData['stortingen'] + $indexData['onttrekkingen'];
						$indexData['kosten'] = $dbData['Kosten'];
						$indexData['opbrengsten'] = $dbData['Opbrengsten'];
						$indexData['performance'] = $indexData['resultaatVerslagperiode']/$dbData['gemiddelde']*100;
						//$indexData['resultaatVerslagperiode'] = $dbData['Opbrengsten']-$dbData['Kosten'];
						$indexData['gerealiseerd'] = $dbData['gerealiseerd'];
						$indexData['ongerealiseerd'] = $dbData['ongerealiseerd'];
						$indexData['rente'] = $dbData['rente'];
						$indexData['extra'] = unserialize($dbData['extra']);
						//listarray($indexData);
					}
					else
						$indexData = array_merge($indexData,$this->BerekenMutaties2($periode['start'],$periode['stop'],$portefeuille));
				}
				else
					$indexData = array_merge($indexData,$this->BerekenMutaties2($periode['start'],$periode['stop'],$portefeuille,$valuta));
			}

			$indexData['datum'] = jul2sql(form2jul(substr($indexData['periodeForm'],-10,10)));
//          echo $indexData['periode']." ".$indexData['performance']."<br>\n";

			//	if(empty($specifiekeIndexWaarden[$i-1]))
					$indexData['specifiekeIndexPerformance'] = $specifiekeIndexWaarden[$i]-100;
			//	else
			//		$indexData['specifiekeIndexPerformance'] =($specifiekeIndexWaarden[$i]/$specifiekeIndexWaarden[$i-1])*100 -100;
				$indexData['specifiekeIndex'] = ($indexData['specifiekeIndex']  * (100+$indexData['specifiekeIndexPerformance'])/100) ;

		//	listarray($indexData['specifiekeIndex']);
				if(empty($indexData['index']))
					$indexData['index']=100;
				$indexData['index'] = ($indexData['index']  * (100+$indexData['performance'])/100);
				$data[$i] = $indexData;

			$i++;
		}

		return $data;
	}

}
?>
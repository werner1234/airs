<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/06/03 15:41:22 $
File Versie					: $Revision: 1.23 $

$Log: RapportKERNZ_L75.php,v $
Revision 1.23  2020/06/03 15:41:22  rvv
*** empty log message ***

Revision 1.22  2020/05/16 15:57:02  rvv
*** empty log message ***

Revision 1.21  2019/11/16 17:12:28  rvv
*** empty log message ***

Revision 1.20  2019/05/25 16:22:07  rvv
*** empty log message ***

Revision 1.19  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.18  2018/07/28 14:45:48  rvv
*** empty log message ***

Revision 1.17  2018/07/25 15:37:42  rvv
*** empty log message ***

Revision 1.16  2018/07/04 08:25:01  rvv
*** empty log message ***

Revision 1.15  2018/06/30 17:43:55  rvv
*** empty log message ***

Revision 1.14  2018/06/24 11:13:16  rvv
*** empty log message ***

Revision 1.13  2018/06/23 14:21:39  rvv
*** empty log message ***

Revision 1.12  2018/06/18 05:58:32  rvv
*** empty log message ***

Revision 1.11  2018/06/17 15:51:53  rvv
*** empty log message ***

Revision 1.10  2018/06/16 17:42:56  rvv
*** empty log message ***

Revision 1.9  2018/05/30 16:10:58  rvv
*** empty log message ***

Revision 1.8  2018/05/24 05:32:01  rvv
*** empty log message ***

Revision 1.7  2018/05/21 10:58:19  rvv
*** empty log message ***

Revision 1.6  2018/05/19 16:24:53  rvv
*** empty log message ***

Revision 1.5  2018/04/28 18:36:15  rvv
*** empty log message ***

Revision 1.4  2018/04/07 15:21:44  rvv
*** empty log message ***

Revision 1.3  2018/03/31 18:06:01  rvv
*** empty log message ***

Revision 1.2  2018/03/18 10:55:47  rvv
*** empty log message ***

Revision 1.1  2018/03/17 18:48:55  rvv
*** empty log message ***

Revision 1.4  2018/03/11 10:53:28  rvv
*** empty log message ***

Revision 1.3  2018/03/10 18:24:22  rvv
*** empty log message ***

Revision 1.13  2017/12/09 17:54:25  rvv
*** empty log message ***

Revision 1.12  2017/10/01 14:29:55  rvv
*** empty log message ***

Revision 1.11  2017/04/12 15:38:14  rvv
*** empty log message ***

Revision 1.10  2016/10/23 11:32:33  rvv
*** empty log message ***

Revision 1.9  2016/10/02 12:38:58  rvv
*** empty log message ***

Revision 1.8  2016/09/18 08:49:02  rvv
*** empty log message ***

Revision 1.7  2016/09/07 15:42:21  rvv
*** empty log message ***

Revision 1.6  2016/06/19 15:22:08  rvv
*** empty log message ***

Revision 1.5  2016/06/12 10:27:20  rvv
*** empty log message ***

Revision 1.4  2016/05/29 13:26:30  rvv
*** empty log message ***

Revision 1.3  2016/05/15 17:15:00  rvv
*** empty log message ***

Revision 1.2  2016/05/08 19:24:24  rvv
*** empty log message ***

Revision 1.1  2016/05/04 16:08:25  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");
include_once($__appvar["basedir"]."/html/rapport/include/ATTberekening_L75.php");

class RapportKERNZ_L75
{
	function RapportKERNZ_L75($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "KERNZ";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Samenvatting liquide vermogen";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
		$this->filterCategorie='Liquide';
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


	function getCRMnaam($portefeuille)
	{
		$db = new DB();
		$query="SELECT naam FROM CRM_naw WHERE portefeuille='$portefeuille'";
		$db->SQL($query);
		$crmData=$db->lookupRecord();
		$naamParts=explode('-',$crmData['naam'],2);
		$naam=trim($naamParts[1]);
		if($naam<>'')
			return $naam;
		else
			return $portefeuille;
	}


	function writeRapport()
	{

		$att=new ATTberekening_L75($this);
		$data=$att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'Hoofdcategorie');
		if($_POST['debug']==1)
		{
			listarray($data);
			exit;
		}
		$DB = new DB();
		$q = "SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'";
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$kleuren = unserialize($kleuren['grafiek_kleur']);
		$kleuren = $kleuren['OIB'];
		$this->oibKleuren=$kleuren;

		if(isset($data[$this->filterCategorie]) && ($data[$this->filterCategorie]['beginwaarde'] <> 0 || $data[$this->filterCategorie]['eindwaarde'] <> 0 || $data[$this->filterCategorie]['resultaat'] <> 0))
		{
			$this->pdf->AddPage();
			$this->pdf->templateVars[$this->pdf->rapport_type . 'Paginas'] = $this->pdf->page;
			$this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type . 'Paginas'] = $this->pdf->rapport_titel;

			if ($_POST['debug'] == 1)
			{
				$this->pdf->line(0, 30, 297, 30);
				$this->pdf->line(0, 23, 297, 23);
				$this->pdf->line(297 / 2, 23, 297 / 2, 210);
				$this->pdf->line(0, (210 - 30) / 2 + 30, 297, (210 - 30) / 2 + 30);
				$this->pdf->line(0, (210 - 30) / 2 + 23, 297, (210 - 30) / 2 + 23);
			}

			$this->PERF_L35($data);
			$this->OIB_L68();
			$this->addVermogensverloop();
			$this->pdf->fillCell = array();
			$this->pdf->CellBorders = array();
		}

		$this->filterCategorie = 'Illiquide';
		if(isset($data[$this->filterCategorie])&& ($data[$this->filterCategorie]['beginwaarde'] <> 0 || $data[$this->filterCategorie]['eindwaarde'] <> 0 || $data[$this->filterCategorie]['resultaat'] <> 0))
		{
			$this->pdf->rapport_type = "KERNZ2";
			$this->pdf->rapport_titel = "Samenvatting illiquide vermogen";
			$this->pdf->AddPage();
			$this->pdf->templateVars[$this->pdf->rapport_type . 'Paginas'] = $this->pdf->page;
			$this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type . 'Paginas'] = $this->pdf->rapport_titel;
			$this->PERF_L35($data);
			$this->OIB_L68();
			$this->addVermogensverloop();
			$this->pdf->fillCell = array();
			$this->pdf->CellBorders = array();
		}

	}

	function PERF_L35($data)
	{

		$this->writePerfOngeconsolideerd($data);

	}


	function writePerfOngeconsolideerd($data)
	{
		global $__appvar;


		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r], $this->pdf->rapport_fontcolor[g], $this->pdf->rapport_fontcolor[b]);
		$x = $this->pdf->setX();
		$y = $this->pdf->getY();

		$this->pdf->widthA = array(0, 85, 30, 5, 30, 120);
		$this->pdf->alignA = array('L', 'L', 'R', 'R', 'R');

		$this->pdf->SetDrawColor(0, 0, 0);
		$this->pdf->SetFillColor(0, 0, 0);


		$this->pdf->setXY($x, $y);

		// ***************************** ophalen data voor afdruk ************************ //

		$this->pdf->setY(25);
		$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
		$this->pdf->Multicell(140,4,vertaalTekst('Ontwikkeling vermogen',$this->pdf->rapport_taal),'','C');
		$this->pdf->setXY($this->pdf->marge,$y+8);
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
		$this->pdf->ln();

		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		$witruimte=4;
		$this->pdf->SetFont($this->pdf->rapport_font, 'b' , $this->pdf->rapport_fontsize);

		$this->pdf->row(array("", vertaalTekst("Resultaat verslagperiode", $this->pdf->rapport_taal), "", ""));
		$this->pdf->ln($witruimte);
		$this->pdf->excelData[]=array("", vertaalTekst("Resultaat verslagperiode", $this->pdf->rapport_taal), "", "");
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

    $categorie=$this->filterCategorie;

		$this->pdf->row(array("", vertaalTekst("Waarde portefeuille per", $this->pdf->rapport_taal) . " " . date("j", db2jul($this->rapportageDatumVanaf)) . " " .
			vertaalTekst($this->pdf->__appvar["Maanden"][date("n", db2jul($this->rapportageDatumVanaf))], $this->pdf->rapport_taal) . " " . date("Y", db2jul($this->rapportageDatumVanaf)),
			$this->formatGetal($data[$categorie]['beginwaarde'], 0, true), ""));

		$this->pdf->ln($witruimte);
		$this->pdf->row(array("", vertaalTekst("Resultaat over verslagperiode", $this->pdf->rapport_taal), $this->formatGetal($data[$categorie]['resultaatNetto'], 0), ""));
		$this->pdf->ln($witruimte);
		$this->pdf->row(array("", vertaalTekst("Totaal stortingen gedurende verslagperiode", $this->pdf->rapport_taal), $this->formatGetal($data[$categorie]['storting'], 0), ""));
		$this->pdf->ln($witruimte);
		$this->pdf->row(array("", vertaalTekst("Totaal onttrekkingen gedurende verslagperiode", $this->pdf->rapport_taal), $this->formatGetal($data[$categorie]['onttrekking'], 0), ""));
		//$this->pdf->Line($posSubtotaal + $extraLengte, $this->pdf->GetY(), $posSubtotaalEnd, $this->pdf->GetY());
		$this->pdf->ln($witruimte);
		$this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
		$this->pdf->CellBorders=array('','','TS');
		$this->pdf->row(array("", vertaalTekst("Waarde portefeuille per", $this->pdf->rapport_taal) . " " . date("j", db2jul($this->rapportageDatum)) . " " . vertaalTekst($this->pdf->__appvar["Maanden"][date("n", db2jul($this->rapportageDatum))], $this->pdf->rapport_taal) . " " . date("Y", db2jul($this->rapportageDatum)), $this->formatGetal($data[$categorie]['eindwaarde'], 0), ""));
		//	$this->pdf->Line($posSubtotaal + $extraLengte, $this->pdf->GetY(), $posSubtotaalEnd, $this->pdf->GetY());
		$this->pdf->CellBorders=array();

	//	if($this->filterCategorie<>'Illiquide')
	//	{
			$this->pdf->ln($witruimte * 2);
			$this->pdf->row(array("", vertaalTekst("Netto rendement over verslagperiode", $this->pdf->rapport_taal), $this->formatGetal($data[$categorie]['procentNetto'], 2) . " %"));
	//	}
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);




	}





	function addVermogensverloop()
	{
		$db=new DB();
		$query="SELECT min(Boekdatum) as startDatum FROM Rekeningmutaties JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening WHERE Rekeningen.Portefeuille='".$this->portefeuille."'";
		$db->SQL($query);
		$db->Query();
		$begin=$db->nextRecord();

		$att=new ATTberekening_L75($this,'maand');
		$perfData=$att->bereken($this->rapportageDatumVanaf ,$this->rapportageDatum,'Hoofdcategorie');// $begin['startDatum'] substr($this->pdf->PortefeuilleStartdatum,0,10)

		if(is_array($this->pdf->portefeuilles)&& count($this->pdf->portefeuilles)>0)
			$portefeuilles=$this->pdf->portefeuilles;
		else
			$portefeuilles=array($this->portefeuille);

		$maanden = array("", "Jan", "Feb", "Mrt", "Apr", "Mei", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dec");
		foreach ($perfData[$this->filterCategorie]['perfWaarden'] as $datum=>$data)
		{
			$julDate=db2jul($datum);

			$jaar=$maanden[date('n',$julDate)] ;//substr($datum,2,5);  date('y',$julDate).'-'.
			if($data['eindwaarde']==0)
			{
				$query="SELECT sum(PortefeuilleWaarde) as PortefeuilleWaarde FROM HistorischePortefeuilleIndex WHERE Categorie = '".$this->filterCategorie."' AND periode='m' AND  portefeuille IN ('".implode("','",$portefeuilles)."') AND
		            Datum = '".$datum."' GROUP BY Datum";
				$db->SQL($query);
				$db->Query();
				$hpi=$db->nextRecord();
				$barData[$jaar][$this->filterCategorie]=($hpi['PortefeuilleWaarde']);
			}
			else
				$barData[$jaar][$this->filterCategorie]=($data['eindwaarde']);
		}
		$this->VBarDiagramVermogen(167,75,120,50,$barData,'Ontwikkeling vermogen in verslagperiode');

	}



	function VBarDiagramVermogen($x,$y,$w, $h, $data,$title='')
	{
		global $__appvar;

		$legendaWidth = 0;
		$this->pdf->setXY($x,$y-$h);
		$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
		$this->pdf->Multicell($w,4, vertaalTekst($title,$this->pdf->rapport_taal),'','C');
		$this->pdf->setXY($x,$y+12);
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);



		$grafiekPunt = array();
		$verwijder=array();
		$this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0,'width'=>0.01));
		$maxVal=100;
		foreach ($data as $datum=>$waarden)
		{
			$legenda[$datum] = $datum;
			$n=0;
			foreach ($waarden as $categorie=>$waarde)
			{
				$grafiek[$datum][$categorie]=$waarde;
				$grafiekCategorie[$categorie][$datum]=$waarde;
				$categorien[$categorie] = $n;
				$categorieId[$n]=$categorie ;

				if($waarde < 0)
				{
					$verwijder[$datum]=$datum;
					$grafiek[$datum][$categorie]=0;
					$grafiekCategorie[$categorie][$datum]=0;
				}
				$maxVal=max($maxVal,$waarde);

				if(!isset($colors[$categorie]))
				{
					if($this->oibKleuren[$categorie])
						$colors[$categorie]=array($this->oibKleuren[$categorie]['R']['value'],$this->oibKleuren[$categorie]['G']['value'],$this->oibKleuren[$categorie]['B']['value']);
					else
						$colors[$categorie]=array(rand(20,80),rand(20,80),rand(20,250));//array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
				}
				$n++;
			}
		}

//		$colors['Totaal']=array(0,72,107);
		foreach ($verwijder as $datum)
		{
			foreach ($data[$datum] as $categorie=>$waarde)
			{
				$grafiek[$datum][$categorie]=0;
				$grafiekCategorie[$categorie][$datum]=0;
			}
		}

		$numBars = count($grafiek);
		 $numBars=12;

		if($color == null)
		{
			$color=array(155,155,155);
		}


		// foreach ($this->jaarWaarden['Totaal'] as $jaar=>$waarden)
		//    $maxVal=max($maxVal,$waarden['waarde']);

		$maxVal=round(ceil($maxVal/5000))*5000;

		// listarray();



		$minVal = 0;

		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
		$XPage = $this->pdf->GetX();
		$YPage = $this->pdf->GetY();
		$margin = 2;
		$YstartGrafiek = $YPage - floor($margin * 1);
		$hGrafiek = ($h - $margin * 1);
		$XstartGrafiek = $XPage + $margin * 1 ;
		$bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

		$unit = $hGrafiek / $maxVal * -1;
		$nulYpos =0;

		$horDiv = 5;
		$horInterval = $hGrafiek / $horDiv;
		$bereik = $hGrafiek/$unit;

		$this->pdf->SetFont($this->pdf->rapport_font, '', 6);
		$this->pdf->SetTextColor(0,0,0);

		$stapgrootte = (abs($bereik)/$horDiv);
		$top = $YstartGrafiek-$h;
		$bodem = $YstartGrafiek;
		$absUnit =abs($unit);

		$nulpunt = $YstartGrafiek + $nulYpos;

		$n=0;

	//	$this->pdf->TextWithRotation($XPage-14,$YPage-$h/2+5,'Vermogen in EUR',90);
		for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
		{
			$this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek+$bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
			$this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
			$this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte)."",0,0,'R');
			$n++;
		}

		if($numBars > 0)
			$this->pdf->NbVal=$numBars;

		$vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
		$bGrafiek = $vBar * ($this->pdf->NbVal + 1);
		$eBaton = ($vBar * 50 / 100);


		$this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
		$this->pdf->SetLineWidth(0.2);

		$this->pdf->SetFillColor($color[0],$color[1],$color[2]);
		$i=0;
//listarray($grafiek);
		foreach ($grafiek as $datum=>$data)
		{
			$data=array_reverse($data,true);
			foreach($data as $categorie=>$val)
			{
				if(!isset($YstartGrafiekLast[$datum]))
					$YstartGrafiekLast[$datum] = $YstartGrafiek;
				//Bar
				$xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
				$lval = $eBaton;
				$yval = $YstartGrafiekLast[$datum] + $nulYpos ;
				$hval = ($val * $unit );//* $this->jaarWaarden['Totaal'][$datum]['waarde']/100

				$this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
				$YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;

				if($legendaPrinted[$datum] != 1)
					$this->pdf->TextWithRotation($xval,$YstartGrafiek+4,vertaalTekst($legenda[$datum],$this->pdf->rapport_taal),0);

				if($grafiekPunt[$categorie][$datum])
				{
					$this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
					if($lastX)
						$this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
					$lastX = $xval+.5*$eBaton;
					$lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
				}
				$legendaPrinted[$datum] = 1;
			}
			$i++;
		}
		/*
		$xval=$x+10;
		$yval=$y+15;
		$colors=array_reverse($colors,true);
		foreach ($colors as $cat=>$color)
		{
			$this->pdf->Rect($xval, $yval, 3, 3, 'DF',null,$colors[$cat]);
			$this->pdf->TextWithRotation($xval+7,$yval+2.5,$cat,0);
			$xval=$xval+22;
		}
*/
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
	}

	function OIB_L68()
	{
		global $__appvar;
		$this->pdf->setY(130);
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '" . $this->pdf->portefeuille . "' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();

		if (!is_array($this->pdf->grafiekKleuren))
		{
			$q = "SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'";
			$DB->SQL($q);
			$DB->Query();
			$kleuren = $DB->LookupRecord();
			$kleuren = unserialize($kleuren['grafiek_kleur']);
			$this->pdf->grafiekKleuren = $kleuren;
		}

		//if(is_array($this->pdf->portefeuilles))
		//		$consolidatie=true;
		//else
  		$consolidatie = false;

		$portefeuilleWaarden = array();
		$aantalPortefeuilles = 0;
		$totaalWaarde = 0;
		if ($consolidatie)
		{
			$aantalPortefeuilles = count($this->pdf->portefeuilles);
			foreach ($this->pdf->portefeuilles as $portefeuille)
			{
				$portefeuilleWaarden[$portefeuille]['belCatWaarde'] = array();
				if ($this->pdf->lastPOST['doorkijk'] == 1)
				{
					vulTijdelijkeTabel(berekenPortefeuilleWaarde($portefeuille, $this->rapportageDatum), $portefeuille, $this->rapportageDatum);
					$gegevens = bepaaldFondsWaardenVerdiept_L75($portefeuille, $this->rapportageDatum,$this->pdf);
				}
				else
				{
					$gegevens = berekenPortefeuilleWaarde($portefeuille, $this->rapportageDatum);
				}
				foreach ($gegevens as $waarde)
				{
					if($waarde['hoofdcategorie']!=$this->filterCategorie && $this->filterCategorie!='totaal')
						continue;

					$portefeuilleWaarden[$portefeuille]['belCatWaarde'][$waarde['beleggingscategorie']] += $waarde['actuelePortefeuilleWaardeEuro'];
					$portefeuilleWaarden[$portefeuille]['totaleWaarde'] += $waarde['actuelePortefeuilleWaardeEuro'];
					$categorieVolgorde[$waarde['beleggingscategorieVolgorde']] = $waarde['beleggingscategorie'];
					$categorieOmschrijving[$waarde['beleggingscategorie']] = $waarde['beleggingscategorieOmschrijving'];
					$totaalWaarde += $waarde['actuelePortefeuilleWaardeEuro'];
				}
			}
			foreach ($portefeuilleWaarden as $portefeuille => $waarden)
			{
				foreach ($waarden['belCatWaarde'] as $categorie => $waardeEur)
				{
					$percentage = ($waardeEur / $waarden['totaleWaarde']);
					$portefeuilleWaarden[$portefeuille]['belCatPercentage'][$categorie] = $percentage;
					$portefeuilleWaarden[$portefeuille]['totalePercentage'] += $percentage;
				}
			}
		}
		else
		{
			if ($this->pdf->lastPOST['doorkijk'] == 1)
			{
				 //$this->bepaaldFondsWaardenVerdiept($this->portefeuille, $this->rapportageDatum);
				$gegevens =bepaaldFondsWaardenVerdiept_L75($this->portefeuille, $this->rapportageDatum,$this->pdf);
				foreach ($gegevens as $waarde)
				{
					if($waarde['hoofdcategorie']!=$this->filterCategorie && $this->filterCategorie!='totaal')
						continue;

					$portefeuilleWaarden[$this->portefeuille]['belCatWaarde'][$waarde['beleggingscategorie']] += $waarde['actuelePortefeuilleWaardeEuro'];
					$portefeuilleWaarden[$this->portefeuille]['totaleWaarde'] += $waarde['actuelePortefeuilleWaardeEuro'];
					$categorieVolgorde[$waarde['beleggingscategorieVolgorde']] = $waarde['beleggingscategorie'];
					$categorieOmschrijving[$waarde['beleggingscategorie']] = vertaalTekst($waarde['beleggingscategorieOmschrijving'],$this->pdf->rapport_taal);
					$totaalWaarde += $waarde['actuelePortefeuilleWaardeEuro'];
				}
				foreach ($portefeuilleWaarden[$this->portefeuille]['belCatWaarde'] as $categorie => $waardeEur)
				{
					$percentage = ($waardeEur / $portefeuilleWaarden[$this->portefeuille]['totaleWaarde']);
					$portefeuilleWaarden[$this->portefeuille]['belCatPercentage'][$categorie] = $percentage;
					$portefeuilleWaarden[$this->portefeuille]['totalePercentage'] += $percentage;
				}

			}
			else
			{
				// haal totaalwaarde op om % te berekenen
				if($this->filterCategorie!='totaal')
					$filter="AND hoofdcategorie='".$this->filterCategorie."'";
				else
					$filter='';

				$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal " .
					"FROM TijdelijkeRapportage WHERE " .
					" rapportageDatum ='" . $this->rapportageDatum . "' $filter AND " .
					" portefeuille = '" . $this->portefeuille . "' "
					. $__appvar['TijdelijkeRapportageMaakUniek'];
				debugSpecial($query, __FILE__, __LINE__);
				$DB->SQL($query);
				$DB->Query();
				$totaalWaarde = $DB->nextRecord();
				$totaalWaarde = $totaalWaarde['totaal'];
				$portefeuilleWaarden[$this->portefeuille]['totaleWaarde'] = $totaalWaarde;


				$query = "SELECT TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving, TijdelijkeRapportage.beleggingscategorieVolgorde, " .
					" TijdelijkeRapportage.valuta, TijdelijkeRapportage.actueleValuta, TijdelijkeRapportage.beleggingscategorie, " .
					" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS actuelePortefeuilleWaardeEuro " .
					" FROM TijdelijkeRapportage " .
					" WHERE TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' $filter AND " .
					" TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "'"
					. $__appvar['TijdelijkeRapportageMaakUniek'] .
					" GROUP BY TijdelijkeRapportage.beleggingscategorie" .
					" ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc";
				debugSpecial($query, __FILE__, __LINE__);

				$DB->SQL($query);
				$DB->Query();

				while ($categorien = $DB->NextRecord())
				{
					if ($categorien['beleggingscategorie'] == '')
					{
						$categorien['beleggingscategorie'] = 'GeenCategorie';
						$categorien['Omschrijving'] = 'Geen categorie';
					}
					$categorieOmschrijving[$categorien['beleggingscategorie']] = vertaalTekst($categorien['Omschrijving'],$this->pdf->rapport_taal);
					$categorieVolgorde[$categorien['beleggingscategorieVolgorde']] = $categorien['beleggingscategorie'];
					$portefeuilleWaarden[$this->portefeuille]['belCatWaarde'][$categorien['beleggingscategorie']] += $categorien['actuelePortefeuilleWaardeEuro'];
					$percentage = ($categorien['actuelePortefeuilleWaardeEuro'] / $totaalWaarde);
					$portefeuilleWaarden[$this->portefeuille]['belCatPercentage'][$categorien['beleggingscategorie']] = $percentage;
					$portefeuilleWaarden[$this->portefeuille]['totalePercentage'] += $percentage;
				}
			}
		}
		//listarray($portefeuilleWaarden);

		// voor kopjes


		$this->pdf->setXY(0,115);
		$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
		$this->pdf->Multicell(297/2,4,vertaalTekst('Verdeling over categorieën',$this->pdf->rapport_taal),'','C');

		$this->pdf->ln(8);

		$pw = 14;
		$portw = 23;
		$tw = $pw + $portw;
		$this->pdf->widthA = array(80, $portw, $pw, $portw, $pw, $portw, $pw, $portw, $pw, $portw, $pw, $portw, $pw, $portw, $pw);
		$this->pdf->alignA = array('L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R');
		// voor data
		$this->pdf->widthB = array(85, $tw, $tw, $tw, $tw, $tw, $tw, $tw);
		$this->pdf->alignB = array('L', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
		if (is_array($this->pdf->portefeuilles))
		{
			$query = "SELECT Portefeuille,ClientVermogensbeheerder FROM Portefeuilles WHERE Portefeuille IN('" . implode("','", $this->pdf->portefeuilles) . "')";
			$DB->SQL($query);
			$DB->Query();
			while ($portefeuille = $DB->NextRecord())
			{
				$this->pdf->clientVermogensbeheerder[$portefeuille['Portefeuille']] = $this->getCRMnaam($portefeuille['Portefeuille']);
			}
		}


		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);

		$lijn1 = $this->pdf->widthB[0];
		$this->pdf->SetX($this->pdf->marge + $lijn1);
		$this->pdf->MultiCell(35, 4, vertaalTekst("Waarden", $this->pdf->rapport_taal), 0, "C");
		$witruimte=3;
		$this->pdf->ln($witruimte);
		$this->pdf->row(array(vertaalTekst("Beleggingscategorie", $this->pdf->rapport_taal),
											vertaalTekst("in " . $this->pdf->rapportageValuta, $this->pdf->rapport_taal),
											vertaalTekst("in %", $this->pdf->rapport_taal)));
		//}
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		ksort($categorieVolgorde);
		$regelData=array();
		$regelDataTotaal=array();
		$totaalPercentage=0;
		$barGraph=false;
		foreach($categorieVolgorde as $categorie)
		{
			$regelTotaal=0;
			foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
			{
				$regelData[$portefeuille][$categorieOmschrijving[$categorie]]=array('waarde'=>$this->formatGetal($belCatData['belCatWaarde'][$categorie],0),'percentage'=>$this->formatGetal($belCatData['belCatPercentage'][$categorie]*100,1));
				$regelTotaal+=$belCatData['belCatWaarde'][$categorie];
			}
			if($consolidatie)
			{
				$percentage=$regelTotaal/$totaalWaarde;
				$regelData['Totaal'][$categorieOmschrijving[$categorie]]=array('waarde'=>$this->formatGetal($regelTotaal,0),'percentage'=>$this->formatGetal($percentage*100,1));

				//echo "$portefeuille $percentage=$regelTotaal/$totaalWaarde; ->$totaalPercentage <br>\n";
				$totaalPercentage+=$percentage;
			}
			if($regelTotaal<0)
				$barGraph=true;
			$categorieVerdeling['percentage'][$categorieOmschrijving[$categorie]]=$regelTotaal/$totaalWaarde*100;
			$categorieVerdeling['kleur'][]=array($this->pdf->grafiekKleuren['OIB'][$categorie]['R']['value'],$this->pdf->grafiekKleuren['OIB'][$categorie]['G']['value'],$this->pdf->grafiekKleuren['OIB'][$categorie]['B']['value']);
			$categorieVerdeling['kleurBar'][$categorieOmschrijving[$categorie]]=array($this->pdf->grafiekKleuren['OIB'][$categorie]['R']['value'],$this->pdf->grafiekKleuren['OIB'][$categorie]['G']['value'],$this->pdf->grafiekKleuren['OIB'][$categorie]['B']['value']);
		}

		$regel=array('Totalen');
		foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
			$regelDataTotaal[$portefeuille]=array('waarde'=>$this->formatGetal($belCatData['totaleWaarde'],0),'percentage'=>$this->formatGetal($belCatData['totalePercentage']*100,1));
		if($consolidatie)
			$regelDataTotaal['Totaal']=array('waarde'=>$this->formatGetal($totaalWaarde,0),'percentage'=>$this->formatGetal($totaalPercentage*100,1));

		$portefeuilleAantal=count($portefeuilleWaarden);
		$blokken=ceil($portefeuilleAantal/5);



		for($i=0;$i<$blokken;$i++)
		{

			//Kop regel
			$regel = array();
			array_push($regel, 'Beleggingscategorie');
			if($i==0 && $consolidatie==true)
				array_push($regel, 'Totaal');
			else
				array_push($regel, '');
			//array_push($regel, '');
			$min=$i*5;
			$max=($i+1)*5;
			$n=0;
			$this->pdf->SetWidths($this->pdf->widthB);
			$this->pdf->SetAligns($this->pdf->alignB);
			foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
			{
				if($n>=$min && $n<$max)
				{
					$kop=$this->getCRMnaam($portefeuille);
					array_push($regel, $kop);
					//array_push($regel,'');
				}
				$n++;
			}

			if($aantalPortefeuilles>5)
			{
				$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
				$this->pdf->Row($regel);
				$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
			}
			//categorieen
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);
			foreach($categorieVolgorde as $categorie)
			{
				$regel = array();
				if($i==0  && $consolidatie==true)
				{
					array_push($regel, $categorieOmschrijving[$categorie]);
					array_push($regel, $regelData['Totaal'][$categorieOmschrijving[$categorie]]['waarde']);
					array_push($regel, $regelData['Totaal'][$categorieOmschrijving[$categorie]]['percentage']);
				}
				else
				{
					array_push($regel, $categorieOmschrijving[$categorie]);
					if($consolidatie==true)
						$cols=2;
					else
						$cols=0;
					for($a=0;$a<$cols;$a++)
						array_push($regel,'');
				}
				$min=$i*5;
				$max=($i+1)*5;
				$n=0;
				foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
				{
					if($n>=$min && $n<$max)
					{
						array_push($regel, $regelData[$portefeuille][$categorieOmschrijving[$categorie]]['waarde']);
						array_push($regel, $regelData[$portefeuille][$categorieOmschrijving[$categorie]]['percentage']);
					}
					$n++;
				}
				$this->pdf->ln(2);
				$this->pdf->Row($regel);
			}

			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);
			//Totaal regel
			$regel = array();
			if($i==0  && $consolidatie==true)
			{
				array_push($regel, 'Totalen');
				array_push($regel, $regelDataTotaal['Totaal']['waarde']);
				array_push($regel, $regelDataTotaal['Totaal']['percentage']);
			}
			else
			{
				if($consolidatie==true)
					$cols=3;
				else
					$cols=1;
				for($a=0;$a<$cols;$a++)
					array_push($regel,'');
			}
			$max=($i+1)*5;
			$n=0;
			foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
			{
				if($n>=$min && $n<$max)
				{
					array_push($regel, $regelDataTotaal[$portefeuille]['waarde']);
					array_push($regel, $regelDataTotaal[$portefeuille]['percentage']);
				}
				$n++;
			}
			$this->pdf->SetFont($this->pdf->rapport_font,'B', $this->pdf->rapport_fontsize);
			$this->pdf->ln(2);

			$this->pdf->CellBorders=array('','TS','TS');
			$this->pdf->Row($regel);
			$this->pdf->SetFont($this->pdf->rapport_font,'', $this->pdf->rapport_fontsize);
			$this->pdf->ln();
		}



		$grafiekY=120;

		$xOffset=297/2;

		if($barGraph==false)
		{

			$this->pdf->setXY($xOffset,115);
			$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
			$this->pdf->Multicell(297/2,4,vertaalTekst('Grafische verdeling vermogen',$this->pdf->rapport_taal),'','C');
			$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);


			$this->pdf->setXY(20+$xOffset,$grafiekY-5);
			PieChart_L75($this->pdf,65, 65, $categorieVerdeling['percentage'], '%l (%p)',$categorieVerdeling['kleur'],'',array(20+$xOffset+65+5,$grafiekY+20));
		}
		else
		{

			$this->pdf->setXY($xOffset,115);
			$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
			$this->pdf->Multicell(297/2,4,vertaalTekst('Grafische verdeling vermogen',$this->pdf->rapport_taal),'','C');
			$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
			
			$this->pdf->setXY(50+$xOffset,$grafiekY);
			$this->BarDiagram(80, 100, $categorieVerdeling['percentage'], '%l (%p)',$categorieVerdeling['kleurBar']);//"Portefeuillewaarde € ".$this->formatGetal($this->portTotaal[$this->rapportageDatum],2)
		}

	}



	function SetLegends2($data, $format)
	{
		$this->pdf->legends=array();
		$this->pdf->wLegend=0;

		$this->pdf->sum=array_sum($data);

		$this->pdf->NbVal=count($data);
		foreach($data as $l=>$val)
		{
			//$p=sprintf('%.1f',$val/$this->sum*100).'%';
			$p=sprintf('%.1f',$val).'%';
			$legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
			$this->pdf->legends[]=$legend;
			$this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->wLegend);
		}
	}

	function BarDiagram($w, $h, $data, $format,$colorArray,$titel)
	{
		$pdfObject = &$object;
		$this->pdf->SetFont($this->rapport_font, '', $this->rapport_fontsize);
		$this->SetLegends2($data,$format);


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
				$this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,0),0,0,'C');
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
				$this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,0),0,0,'C');

				$i++;
				if($i>100)
					break;
			}
		}

		$i=0;

		$this->pdf->SetXY($XDiag-$legendWidth, $YDiag);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+4);
		$this->pdf->Cell($lDiag, $hval-5, $titel,0,0,'C');
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);
//listarray($colorArray);listarray($data);
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


}
?>
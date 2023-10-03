<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/03/10 14:08:16 $
 		File Versie					: $Revision: 1.17 $

 		$Log: RapportCASHY_L33.php,v $
 		Revision 1.17  2019/03/10 14:08:16  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2016/10/29 15:40:53  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2016/10/26 16:13:40  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2016/10/13 12:30:21  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2016/09/19 11:27:55  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2016/09/18 08:48:00  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2013/04/24 13:22:02  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2013/04/03 14:58:34  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2013/03/27 17:02:38  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2013/03/23 16:19:36  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2012/08/22 15:46:00  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2012/04/21 15:38:14  rvv
 		*** empty log message ***

 		Revision 1.5  2011/12/24 17:41:10  rvv
 		*** empty log message ***

 		Revision 1.4  2011/12/24 16:35:21  rvv
 		*** empty log message ***

 		Revision 1.3  2011/12/18 14:26:44  rvv
 		*** empty log message ***

 		Revision 1.2  2011/11/30 18:36:35  rvv
 		*** empty log message ***

 		Revision 1.1  2011/11/27 12:46:47  rvv
 		*** empty log message ***

 		Revision 1.6  2008/12/03 10:55:05  rvv
 		*** empty log message ***

 		Revision 1.5  2008/11/18 11:16:58  rvv
 		*** empty log message ***

 		Revision 1.4  2008/11/13 10:11:07  rvv
 		*** empty log message ***

 		Revision 1.3  2008/06/04 08:19:32  rvv
 		*** empty log message ***

 		Revision 1.2  2008/05/29 07:04:19  rvv
 		*** empty log message ***

 		Revision 1.1  2008/05/06 10:22:42  rvv
 		*** empty log message ***

 		Revision 1.1  2007/12/14 14:12:19  rvv
 		*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
//ini_set('max_execution_time',60);
class RapportCASHY_L33
{
	function RapportCASHY_L33($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{

			$this->pdf = &$pdf;
			$this->pdf->rapport_type = "CASHY";
			$this->pdf->rapport_datum = db2jul($rapportageDatum);
			$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
			$this->pdf->rapport_titel = "Kasstroom uit Obligatieportefeuille";
			$this->portefeuille = $portefeuille;
			$this->rapportageDatumVanaf = $rapportageDatumVanaf;
			$this->rapportageDatum = $rapportageDatum;
			$this->pdf->excelData = array();
		}

		function formatGetal($waarde, $dec)
		{
			return number_format($waarde, $dec, ",", ".");
		}


		function writeRapport()
		{
			global $__appvar;
			$this->pdf->AddPage();
			$this->pdf->templateVars['CASHYPaginas'] = $this->pdf->customPageNo;
			$this->pdf->SetWidths(array(10, 25, 25, 25, 40, 20, 20));
			$this->pdf->SetAligns(array('L', 'L', 'R', 'R', 'R', 'R', 'R'));

			// print categorie headers
			$rapJaar = substr($this->rapportageDatum, 0, 4);

			$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
			$cashflowJaar = array();
			$cashflowTotaal = 0;
			$cashfow = new Cashflow($this->portefeuille, db2jul($rapJaar . '-01-01'), $this->pdf->rapport_datum, $this->pdf->debug);
			$cashfow->genereerTransacties();
			$regels = $cashfow->genereerRows();


			for ($jaar = $rapJaar; $jaar <= ($rapJaar + 1); $jaar++)
			{
				for ($i = 1; $i < 5; $i++)
				{
					$cashflowKwartalen[$jaar . "Q" . $i]['lossing'] += 0;
					$cashflowKwartalen[$jaar . "Q" . $i]['rente'] += 0;
				}
			}

			foreach ($cashfow->regelsRaw as $regel)
			{
				$jaar = substr($regel['0'], 6, 4);
				if ($jaar > ($rapJaar + 10))
				{
					$jaar = 'Overig';
				}
				$maand = intval(substr($regel['0'], 3, 2));
				$kwartaal = ceil($maand / 3);
				$cashflowJaar[$jaar]['lossing'] += 0;
				$cashflowJaar[$jaar]['rente'] += 0;
				if ($jaar == $rapJaar || $jaar == $rapJaar + 1)
				{
					$cashflowKwartalen[$jaar . 'Q' . $kwartaal][$regel[2]] += $regel[3]/$this->pdf->ValutaKoersEind;
				}


				$cashflowJaar[$jaar][$regel[2]] += $regel[3]/$this->pdf->ValutaKoersEind;
				$cashflowTotaal += $regel[3];
			}



			$this->pdf->setY(80);
			$this->pdf->SetWidths(array(160, 15, 25, 25, 25));
			$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
			$this->pdf->Row(array('', 'Periode', 'Coupon', 'Lossing', 'Totaal'));
			$this->pdf->Line($this->pdf->marge + 160, $this->pdf->GetY(), $this->pdf->marge + 250, $this->pdf->GetY(), array('color' => $this->pdf->rapport_balkKleur));
			$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
			$totalen = array();
			foreach ($cashflowJaar as $jaar => $waarden)
			{

				$totalen['lossing'] += $waarden['lossing'];
				$totalen['rente'] += $waarden['rente'];
				$this->pdf->Row(array('', $jaar, $this->formatGetal($waarden['rente'], 0),
													$this->formatGetal($waarden['lossing'], 0),
													$this->formatGetal($waarden['lossing'] + $waarden['rente'], 0)));
			}

			$this->pdf->ln(2);

			$this->pdf->SetTextColor(255,255,255);
			$this->pdf->SetFillColor(98,144,128);
			$this->pdf->fillCell = array(0,1,1,1,1,1,1,1,1,1,1,1);
			$this->pdf->Row(array('', 'Totaal',
												$this->formatGetal($totalen['rente'], 0),
												$this->formatGetal($totalen['lossing'], 0),
												$this->formatGetal($totalen['lossing'] + $totalen['rente'], 0)));
			$this->pdf->CellBorders = array();
			$this->pdf->SetTextColor(0);
			unset($this->pdf->fillCell);
			$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['R']);
			$this->pdf->SetFillColor($this->pdf->rapport_regelKleur[0], $this->pdf->rapport_regelKleur[1], $this->pdf->rapport_regelKleur[2]);
			$this->pdf->CellBorders = array();
			$this->pdf->SetLineStyle(array('dash' => 0, 'color' => array(0, 0, 0)));
			//$this->pdf->setXY(20, 105);
		//	$this->VBarDiagram(160, 60, $cashflowKwartalen, "Kasstroom op kwartaal basis");
			$this->pdf->setXY(20, 145);
			$this->VBarDiagram(160, 60, $cashflowJaar, "Kasstroom uit obligatieportefeuille");
		}


		function VBarDiagram($w, $h, $data, $titel)
		{
			global $__appvar;
			$legendaWidth = 50;
			$grafiekPunt = array();
			$verwijder = array();

			$xPositie = $this->pdf->getX();
			$yPositie = $this->pdf->getY();
			$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize + 2);
			$this->pdf->setXY($xPositie - 20, $yPositie - $h - 8);
			$this->pdf->Multicell($w, 5, $titel, '', 'C');
			$this->pdf->setXY($xPositie + 110, $yPositie - $h - 8);
			$this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
			$this->pdf->Multicell(20, 5, 'X 1.000', '', 'L');
			$this->pdf->setXY($xPositie, $yPositie);


			foreach ($data as $datum => $waarden)
			{
				$legenda[$datum] = $datum;
				$n = 0;
				foreach ($waarden as $categorie => $waarde)
				{
					$datumTotalen[$datum] += $waarde;
					$grafiek[$datum][$categorie] = $waarde;
					$grafiekCategorie[$categorie][$datum] = $waarde;
					$categorien[$categorie] = $n;
					$categorieId[$n] = $categorie;
					if ($waarde < 0)
					{
						$verwijder[$datum] = $datum;
						$grafiek[$datum][$categorie] = 0;
						$grafiekCategorie[$categorie][$datum] = 0;
					}


					if (!isset($colors[$categorie]))
					{
						$colors[$categorie] = array($this->categorieKleuren[$categorie]['R']['value'], $this->categorieKleuren[$categorie]['G']['value'], $this->categorieKleuren[$categorie]['B']['value']);
					}
					$n++;


				}
			}

			$colors = array('lossing' => array(98,144,128),
											'rente'   => array(102, 102, 102));

			foreach ($verwijder as $datum)
			{
				foreach ($data[$datum] as $categorie => $waarde)
				{
					$grafiek[$datum][$categorie] = 0;
					$grafiekCategorie[$categorie][$datum] = 0;
				}
			}

			$numBars = count($legenda);


			if ($color == null)
			{
				$color = array(155, 155, 155);
			}
			$maxVal = max($datumTotalen);
			$minVal = 0;


			$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
			$XPage = $this->pdf->GetX();
			$YPage = $this->pdf->GetY();
			$margin = 2;
			$YstartGrafiek = $YPage - floor($margin * 1);
			$hGrafiek = ($h - $margin * 1);
			$XstartGrafiek = $XPage + $margin * 1;
			$bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

			$n = 0;


			foreach (array_reverse($this->categorieVolgorde) as $categorie)
			{
				if (is_array($grafiekCategorie[$categorie]))
				{
					$this->pdf->Rect($XstartGrafiek + $bGrafiek + 3, $YstartGrafiek - $hGrafiek + $n * 10 + 2, 2, 2, 'DF',null, $colors[$categorie]);
					$this->pdf->SetXY($XstartGrafiek + $bGrafiek + 6, $YstartGrafiek - $hGrafiek + $n * 10 + 1.5);
					$this->pdf->Cell(20, 3, $this->categorieOmschrijving[$categorie], 0, 0, 'L');
					$n++;
				}
			}
			$maxmaxVal = ceil($maxVal / (pow(10, strlen(round($maxVal))))) * pow(10, strlen(round($maxVal)));

			if ($maxmaxVal / 8 > $maxVal)
			{
				$maxVal = $maxmaxVal / 8;
			}
			elseif ($maxmaxVal / 4 > $maxVal)
			{
				$maxVal = $maxmaxVal / 4;
			}
			elseif ($maxmaxVal / 2 > $maxVal)
			{
				$maxVal = $maxmaxVal / 2;
			}
			else
			{
				$maxVal = $maxmaxVal;
			}

			$unit = $hGrafiek / $maxVal * -1;


			$nulYpos = 0;

			$horDiv = 5;
			$horInterval = $hGrafiek / $horDiv;
			$bereik = $hGrafiek / $unit;

			$this->pdf->SetFont($this->pdf->rapport_font, '', 6);
			$this->pdf->SetTextColor(0, 0, 0);

			$stapgrootte = (abs($bereik) / $horDiv);
			$top = $YstartGrafiek - $h;
			$bodem = $YstartGrafiek;
			$absUnit = abs($unit);

			$nulpunt = $YstartGrafiek + $nulYpos;

			$this->pdf->Rect($XstartGrafiek, $YstartGrafiek - $hGrafiek, $bGrafiek, $hGrafiek, 'FD', '', array(245, 245, 245));

			$n = 0;

			if ($absUnit > 0 && $stapgrootte > 0)
			{
				for ($i = $nulpunt; $i > $top; $i -= $absUnit * $stapgrootte)
				{
					$this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek, $i, array('dash' => 1, 'color' => array(0, 0, 0)));
					$this->pdf->SetXY($XstartGrafiek + $bGrafiek + 1, $i - 1.5);
					$this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
					$this->pdf->Cell(10, 3, $this->formatGetal($n * $stapgrootte / 1000) . "", 0, 0, 'L');
					$n++;
				}
			}

			if ($numBars > 0)
			{
				$this->pdf->NbVal = $numBars;
			}

			$vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
			$bGrafiek = $vBar * ($this->pdf->NbVal + 1);
			$eBaton = ($vBar * 50 / 100);


			$this->pdf->SetLineStyle(array('dash' => 0, 'color' => array(0, 0, 0)));
			$this->pdf->SetLineWidth(0.2);

			$this->pdf->SetFillColor($color[0], $color[1], $color[2]);
			$i = 0;

			foreach ($grafiek as $datum => $data)
			{
				foreach ($data as $categorie => $val)
				{
					if (!isset($YstartGrafiekLast[$datum]))
					{
						$YstartGrafiekLast[$datum] = $YstartGrafiek;
					}
					//Bar
					$xval = $XstartGrafiek + (1 + $i) * $vBar - $eBaton / 2;
					$lval = $eBaton;
					$yval = $YstartGrafiekLast[$datum] + $nulYpos;
					$hval = ($val * $unit);

					$this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null, $colors[$categorie]);
					$YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum] + $hval;
					$this->pdf->SetTextColor(255, 255, 255);
					if (abs($hval) > 3)
					{
						//   $this->pdf->SetXY($xval, $yval+($hval/2)-2);
						//   $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
					}
					$this->pdf->SetTextColor(0, 0, 0);

					if ($legendaPrinted[$datum] != 1)
					{
						if (strlen($legenda[$datum]) == 4)
						{
							$this->pdf->TextWithRotation($xval - 0.75, $YstartGrafiek + 5.25, $legenda[$datum], 45);
						}
						else
						{
							$this->pdf->TextWithRotation($xval - 0.75, $YstartGrafiek + 6.25, $legenda[$datum], 45);
						}
					}
					//$this->pdf->TextWithRotation($XDiag+($i+1)*$unitw-6,$YDiag+$hDiag+10,vertaalTekst($maanden[date("n",$julDatum)],$pdf->rapport_taal).'-'.date("Y",$julDatum),45);

					if ($grafiekPunt[$categorie][$datum])
					{
						$this->pdf->Rect($xval + .5 * $eBaton - .5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek - .5, 1, 1, 'DF',null, array(128, 128, 128));
						if ($lastX)
						{
							$this->pdf->line($lastX, $lastY, $xval + .5 * $eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
						}
						$lastX = $xval + .5 * $eBaton;
						$lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
					}
					$legendaPrinted[$datum] = 1;
				}
				$i++;
			}


			$x1 = $xval - 50;
			$y1 = $nulpunt + 8;
			$hLegend = 3;
			$legendaMarge = 2;
			$vertaling['rente'] = 'Coupons';
			$vertaling['lossing'] = 'Lossingen';

			foreach ($colors as $categorie => $color)
			{
				$this->pdf->SetFont($this->rapport_font, '', 6);
				$this->pdf->SetTextColor($this->rapport_fonds_fontcolor['R'], $this->rapport_fonds_fontcolor['G'], $this->rapport_fonds_fontcolor['B']);
				$this->pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));

				$this->pdf->SetFillColor($color[0], $color[1], $color[2]);
				$this->pdf->Rect($x1 - 5, $y1, $hLegend, $hLegend, 'DF');
				$this->pdf->SetXY($x1, $y1);
				$this->pdf->Cell(0, 4, $vertaling[$categorie]);
				// $y1+= $hLegend + $legendaMarge;
				$x1 += 40;
				$i++;

			}

			$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
		}
	}


?>
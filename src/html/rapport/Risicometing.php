<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/11/16 17:36:34 $
File Versie					: $Revision: 1.23 $

$Log: Risicometing.php,v $
Revision 1.23  2019/11/16 17:36:34  rvv
*** empty log message ***

Revision 1.22  2018/08/29 16:13:10  rvv
*** empty log message ***

Revision 1.21  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.20  2018/08/02 04:26:17  rvv
*** empty log message ***

Revision 1.19  2016/05/11 16:02:54  rvv
*** empty log message ***

Revision 1.18  2014/02/05 15:59:12  rvv
*** empty log message ***

Revision 1.17  2013/08/28 16:02:00  rvv
*** empty log message ***

Revision 1.16  2012/09/23 08:52:54  rvv
*** empty log message ***

Revision 1.15  2012/05/06 12:00:14  rvv
*** empty log message ***

Revision 1.14  2011/04/30 16:27:12  rvv
*** empty log message ***

Revision 1.13  2008/06/30 07:58:44  rvv
*** empty log message ***

Revision 1.12  2007/08/02 14:46:01  rvv
*** empty log message ***

Revision 1.11  2007/04/03 13:26:33  rvv
*** empty log message ***

Revision 1.10  2007/02/21 11:04:26  rvv
Client toevoeging

Revision 1.9  2006/11/03 11:24:04  rvv
Na user update

Revision 1.8  2006/10/31 12:12:15  rvv
Voor user update

Revision 1.7  2006/06/09 13:50:38  jwellner
*** empty log message ***

Revision 1.6  2006/02/03 08:00:27  jwellner
diverse bugfixen

Revision 1.5  2005/11/07 10:29:17  jwellner
no message

Revision 1.4  2005/11/01 11:20:08  jwellner
diverse aanpassingen

Revision 1.3  2005/09/12 09:10:42  jwellner
diverse aanpassingen / bugfixes gemeld in e-mails theo

Revision 1.2  2005/09/09 11:31:46  jwellner
diverse aanpassingen zie e-mails Theo

Revision 1.1  2005/09/07 07:33:23  jwellner
no message


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");

class Risicometing
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function Risicometing( $selectData) {

		$this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "risicometing";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;

		$this->pdf->marge = 8;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);

		$this->pdf->tmdatum = $this->selectData['datumTm'];

		$this->orderby  = " Portefeuilles.ClientVermogensbeheerder ";

		$this->pdf->excelData = array();
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		global $__appvar;
		$einddatum = jul2sql($this->selectData['datumTm']);

		$rapportageDatum['a'] = jul2sql($this->selectData['datumVan']);
		$rapportageDatum['b'] = jul2sql($this->selectData['datumTm']);

		$selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();

		$this->pdf->__appvar = $this->__appvar;

		$fondswaardenClean = array();
		$fondswaardenRente = array();
		$rekeningwaarden 	 = array();

		$jaar = date("Y",$this->datumTm);

		// controle op einddatum portefeuille
		//$extraquery  .= " Portefeuilles.Einddatum > '".jul2db($this->selectData[datumTm])."' AND";

		if($records <= 0)		{
			echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
			$this->progressbar->hide();
			exit;
		}

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / $records;
		}

		$this->pdf->AddPage();

		// print CSV kop
		$this->pdf->excelData[] = array("Naam", "Portefeuille","Accountmanager",
                        'Soort overeenkomst',
                        'Risicoprofiel',
												"Totale waarde portefeuille",
												"Rendement",
												"afm-#",
												"standaarddeviatie",
			                  "benchmark rendement",
			                  "standaarddeviatie benchmark");

		if(date("d-m",$this->datumVan)=='01-01')
			$startjaar = true;
		else
			$startjaar = false;
		

		foreach($portefeuilles as $pdata)
		{
			// waarden op 0 stellen
			$risicoTotaal = 0;

			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
			}
			// set portefeuillenr
			// load settings.
			$portefeuille = $pdata['Portefeuille'];
			$this->pdf->portefeuille = $pdata['Portefeuille'];

			loadLayoutSettings($this->pdf, $pdata['Portefeuille']);

			if(db2jul($rapportageDatum['a']) < db2jul($pdata['Startdatum']))
				$startdatum = $pdata['Startdatum'];
		  else
				$startdatum = $rapportageDatum['a'];

			$fondswaarden['a'] =  berekenPortefeuilleWaarde($portefeuille,  $startdatum, $startjaar);
			$fondswaarden['b'] =  berekenPortefeuilleWaarde($portefeuille,  $einddatum);

			vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,$startdatum);
			vulTijdelijkeTabel($fondswaarden['b'] ,$portefeuille,$einddatum);

			$DB3 = new DB();
			// haal totaalwaarde op om % te berekenen
			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
							 "FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$einddatum."' AND ".
							 " portefeuille = '".$portefeuille."' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query,__FILE__,__LINE__);
			$DB3->SQL($query);
			$DB3->Query();
			$totaalWaarde = $DB3->nextRecord();
			$totaalWaarde = $totaalWaarde['totaal'];

			if($pdata['RapportageValuta'] =='')
        $pdata['RapportageValuta']='EUR';
			$performance = performanceMeting($portefeuille, $startdatum, $einddatum, $pdata['PerformanceBerekening'], $pdata['RapportageValuta']);
			$afm=AFMstd($portefeuille,$einddatum,false);

			$query="SELECT specifiekeIndex FROM Portefeuilles WHERE portefeuille='$portefeuille'";
			$DB3->SQL($query);
			$DB3->Query();
			$specifiekeIndex = $DB3->nextRecord();

		  $benchmark=$specifiekeIndex['specifiekeIndex'];//getFondsverdeling($specifiekeIndex['specifiekeIndex']);
			$benchmarkRendement=getFondsPerformanceGestappeld($benchmark, $portefeuille, $startdatum,$einddatum);

      $query="SELECT SdMethodiek FROM Vermogensbeheerders WHERE Vermogensbeheerder='".$pdata['Vermogensbeheerder']."'";
      $DB3->SQL($query);
      $sdInfo=$DB3->lookupRecord();
      if($sdInfo['SdMethodiek'] <> '')
      {
         $stdev=new rapportSDberekening($portefeuille,$einddatum);
         $stdev->addReeks('totaal');
			   $stdev->addReeks('benchmark',$specifiekeIndex['specifiekeIndex']);
         $stdev->berekenWaarden();
         $tmp=$stdev->getUitvoer();
         $standaarddeviatie=$tmp['totaal'];
			   $standaarddeviatieBM=$tmp['benchmark'];
      }
      else
      {     
        $index = new indexHerberekening();
        $dagen=(db2jul($einddatum)-db2jul($pdata['Startdatum']))/86400;
        if($dagen < 365)
        {
          $perioden='geenData';
          $yearCount=0;
        }
        elseif($dagen > (3*365))
        {
          $perioden='maanden';
          $yearCount=12;
        }
        else
        {
          $perioden='halveMaanden';
          $yearCount=24;
        }
        $indexWaarden = $index->getWaarden($pdata['Startdatum'],$einddatum,$portefeuille,$specifiekeIndex['specifiekeIndex'],$perioden);

        $portPerfAvg=0;
        $indexArray=array();
				$indexBMArray=array();
        foreach ($indexWaarden as $id=>$waarden)
        {
          $portPerfAvg+=$waarden['performance'];
          $indexArray[$waarden['datum']]=100+$waarden['performance'];
					$indexBMArray[$waarden['datum']]=100+$waarden['specifiekeIndexPerformance'];
        }
        $portPerfAvg=$portPerfAvg/count($indexWaarden);
        $standaarddeviatiePeriode=standard_deviation($indexArray);
				$standaarddeviatieBMPeriode=standard_deviation($indexBMArray);

				//listarray($indexArray);
        $standaarddeviatie= $standaarddeviatiePeriode*sqrt($yearCount); //Annualized standard deviation = Standard Deviation * SQRT(N) where N = number of periods in 1 year.
				//echo "<br>\n $perioden $standaarddeviatiePeriode =  $standaarddeviatiePeriode*".sqrt($yearCount)."<br>\n";
				$standaarddeviatieBM= $standaarddeviatiePeriode*sqrt($standaarddeviatieBMPeriode);
      }



			$this->pdf->excelData[] = array($pdata['Naam'],$pdata['Portefeuille'],
                                $pdata['Accountmanager'],
                                $pdata['SoortOvereenkomst'], 
																$pdata['Risicoklasse'],
																round($totaalWaarde,2),
																round($performance,2),
																round($afm['std'],2),
																round($standaarddeviatie,2),
			                          round($benchmarkRendement,2),
				                        round($standaarddeviatieBM,2));


			// schrijf data !
			//$this->pdf->SetX($this->pdf->marge);
			$this->pdf->Cell(30 , 4 , $pdata['Portefeuille'] , 0, 0, "R");
			$this->pdf->Cell(80,  4 , $pdata['Naam'] , 0, 0, "L");
	  	$this->pdf->Cell(30 , 4 , $pdata['Risicoklasse'], 0, 0, "R");
			$this->pdf->Cell(30 , 4 , $this->formatGetal($totaalWaarde,2) , 0, 0, "R");
			$this->pdf->Cell(27 , 4 , $this->formatGetal($performance,2) , 0, 0, "R");
			$this->pdf->Cell(27 , 4 , $this->formatGetal($afm['std'],2) , 0, 0, "R");
			$this->pdf->Cell(27 , 4 , $this->formatGetal($standaarddeviatie,2) , 0, 0, "R");
			$this->pdf->Cell(27 , 4 , $this->formatGetal($benchmarkRendement,2) , 0, 1, "R");



			verwijderTijdelijkeTabel($portefeuille, $this->selectData['datumTm']);
			
		}

		if($this->progressbar)
			$this->progressbar->hide();
	}




}
?>
<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/07/10 15:39:38 $
File Versie					: $Revision: 1.48 $

$Log: Factuur.php,v $
Revision 1.48  2019/07/10 15:39:38  rvv
*** empty log message ***

Revision 1.47  2018/08/01 17:56:34  rvv
*** empty log message ***

Revision 1.46  2018/03/28 15:46:46  rvv
*** empty log message ***

Revision 1.45  2018/01/13 18:58:25  rvv
*** empty log message ***

Revision 1.44  2018/01/11 18:45:58  rvv
*** empty log message ***

Revision 1.43  2018/01/10 16:27:53  rvv
*** empty log message ***

Revision 1.42  2017/12/13 13:12:58  cvs
no message

Revision 1.41  2017/10/28 18:03:52  rvv
*** empty log message ***

Revision 1.40  2017/10/25 15:57:10  rvv
*** empty log message ***

Revision 1.39  2017/08/12 12:18:02  rvv
*** empty log message ***

Revision 1.38  2017/04/08 18:21:38  rvv
*** empty log message ***

Revision 1.37  2017/02/01 16:45:45  rvv
*** empty log message ***

Revision 1.36  2016/10/19 14:50:31  rvv
*** empty log message ***

Revision 1.35  2016/10/13 13:18:38  rvv
*** empty log message ***

Revision 1.34  2016/10/12 16:27:43  rvv
*** empty log message ***

Revision 1.33  2016/05/25 14:16:24  rvv
*** empty log message ***

Revision 1.32  2015/10/25 13:06:52  rvv
*** empty log message ***

Revision 1.31  2015/07/29 16:09:50  rvv
*** empty log message ***

Revision 1.30  2015/06/27 15:53:28  rvv
*** empty log message ***

Revision 1.29  2015/04/11 17:09:28  rvv
*** empty log message ***

Revision 1.28  2015/03/22 10:55:06  rvv
*** empty log message ***

Revision 1.27  2015/02/25 17:26:49  rvv
*** empty log message ***

Revision 1.26  2014/01/18 17:25:33  rvv
*** empty log message ***

Revision 1.25  2013/11/02 17:04:50  rvv
*** empty log message ***

Revision 1.24  2013/10/16 15:42:20  rvv
*** empty log message ***

Revision 1.23  2013/10/09 15:57:52  rvv
*** empty log message ***

Revision 1.22  2013/10/07 17:22:47  rvv
*** empty log message ***

Revision 1.21  2013/10/07 12:31:43  rvv
*** empty log message ***

Revision 1.20  2013/10/05 15:59:34  rvv
*** empty log message ***

Revision 1.19  2013/08/07 17:19:51  rvv
*** empty log message ***

Revision 1.18  2013/07/10 16:02:14  rvv
*** empty log message ***

Revision 1.17  2013/05/08 15:40:51  rvv
*** empty log message ***

Revision 1.16  2013/04/24 16:00:31  rvv
*** empty log message ***

Revision 1.15  2013/03/13 17:01:47  rvv
*** empty log message ***

Revision 1.14  2012/11/07 17:08:46  rvv
*** empty log message ***

Revision 1.13  2012/07/11 15:50:50  rvv
*** empty log message ***

Revision 1.12  2012/06/30 14:45:30  rvv
*** empty log message ***

Revision 1.11  2012/05/23 15:57:43  rvv
*** empty log message ***

Revision 1.10  2011/09/14 09:26:56  rvv
*** empty log message ***

Revision 1.9  2011/01/05 18:52:30  rvv
*** empty log message ***

Revision 1.8  2010/07/18 17:08:06  rvv
*** empty log message ***

Revision 1.7  2010/01/06 16:48:35  rvv
*** empty log message ***

Revision 1.6  2009/12/15 17:13:42  rvv
*** empty log message ***

Revision 1.5  2009/01/20 17:44:09  rvv
*** empty log message ***

Revision 1.4  2008/03/18 09:42:38  rvv
*** empty log message ***

Revision 1.3  2008/01/10 16:27:31  rvv
*** empty log message ***

Revision 1.2  2007/11/16 11:28:08  rvv
*** empty log message ***

Revision 1.1  2007/08/02 14:46:59  rvv
*** empty log message ***

*/


include_once("rapportRekenClass.php");
include_once("rapport/factuur/FactuurRekenClass.php");

include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class Factuur
{
	var $exceldata;

	function Factuur($pdf, $portefeuille, $vandatum, $tmdatum, $selectieData)
	{ 
	//	$this->excelData 	= array();
		$this->pdf = &$pdf;
		$this->portefeuille = $portefeuille;
		$this->vandatum = $vandatum;
		$this->tmdatum = $tmdatum;

		if(is_array($selectieData))
		{
		  $this->selectieData = $selectieData;
		}
		else
		$this->extrastart = $extrastart;
    
    if(!isset($this->pdf->CsvHeader))
      $this->getCsvHeader($this->pdf->rapport_layout);

		if (!isset($pdf->excelData))
      $this->writeCsvLine($this->pdf->CsvHeader);

		if (!isset($pdf->excelDataFactuur))
      $this->writeCsvLine($this->pdf->CsvHeader,true);

    if (!isset($pdf->excelData2))
      $this->writeCsvLine2($this->pdf->CsvHeader);

		if($this->selectieData)
		  $this->berekening = new factuurBerekening($portefeuille, $vandatum, $tmdatum,$this->selectieData['drempelPercentage'],false);
		else
		  $this->berekening = new factuurBerekening($portefeuille, $vandatum, $tmdatum,$this->pdf->FactuurDrempelPercentage,true);

		$this->waarden = $this->berekening->berekenWaarden();
    if(!$this->waarden && $this->berekening->afbreken==true && $pdf->factuurInXls==false)
    {
      echo "<br>\nGeen factuur voor ".$this->portefeuille."";// 
      //listarray($this->pdf);exit;
      //$this->pdf->geenFactuur[$this->portefeuille]=$this->berekening->data;
      $this->writeCsvLine2($this->berekening->data);
      //unset($this->berekening->data);
    }
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

  function writeCsvLine($data,$factuurOnly=false) //$data = array('collum'=>'value')
	{
	  $csvLine = array();
		foreach($this->pdf->CsvHeader as $key=>$value)
		{
	    if (is_array($data[$key]))
	      $csvLine[]=array($data[$key][0],$data[$key][1]);
	    else
	      $csvLine[]=$data[$key];
	  }
    if($factuurOnly==false)
	    $this->pdf->excelData[]=$csvLine;
    $this->pdf->excelDataFactuur[]=$csvLine;
	}
  
  function writeCsvLine2($data) //$data = array('collum'=>'value')
	{ 

	  $csvLine = array();
	  foreach($this->pdf->CsvHeader as $key=>$value)
	  {
	    if (is_array($data[$key]))
	      $csvLine[]=array($data[$key][0],$data[$key][1]);
	    else
	      $csvLine[]=$data[$key];
	  }
	  $this->pdf->excelData2[]=$csvLine;
	}

	function writeRapport()
	{
	  $this->waarden['factuurNummer'] = $this->factuurnummer;
	  $this->pdf->SetDrawColor(0,0,0);
    if (file_exists("./rapport/factuur/Factuur_L".$this->pdf->rapport_layout.".php"))
		{
		  include("./rapport/factuur/Factuur_L".$this->pdf->rapport_layout.".php");
		}
		else
		{
		  include("./rapport/factuur/FactuurDefault.php");
		}
		$this->pdf->concept[$this->portefeuille]=$this->waarden;

    if(!is_array($this->pdf->extraAdres) || count($this->pdf->extraAdres)<1)
    {
			if(isset($this->waarden) && is_array($this->waarden))
			{
				$this->writeCsvLine($this->waarden);
				$this->waardenDb['Rapport'] = 'Factuur';
				$dbVelden = array('client'                                  => 'Client',
													'clientNaam'                              => 'Naam',
													'clientNaam1'                             => 'Naam1',
													'clientAdres'                             => 'Adres',
													'clientPostcode'                          => 'Postcode',
													'clientWoonplaats'                        => 'Woonplaats',
													'clientTelefoon'                          => 'Telefoon',
													'clientFax'                               => 'Fax',
													'clientEmail'                             => 'Email',
													'datumVan'                                => 'DatumVan',
													'datumTot'                                => 'DatumTot',
													'factuurNummer'                           => 'Factuurnummer',
													'portefeuille'                            => 'Portefeuille',
													'RapportageValuta'                        => 'valuta',
													'totaalWaardeVanaf'                       => 'Beginwaarde',
													'totaalWaarde'                            => 'Eindwaarde',
													'gemiddeldeVermogen'                      => 'GemiddeldeWaarde',
													'maandsWaarde_1'                          => 'DrieMaandsUltimoWaarde 1',
													'maandsWaarde_2'                          => 'DrieMaandsUltimoWaarde 2',
													'maandsWaarde_3'                          => 'DrieMaandsUltimoWaarde 3',
													'maandsWaarde_4'                          => 'VierMaandsUltimoWaarde 4',
													'maandsGemiddelde'                        => 'drieMaandsGemiddelde',
													'beheerfeeOpJaarbasis'                    => 'BeheerfeePerJaar',
													'performancefee'                          => 'Performancefee',
													'administratieBedrag'                     => 'BeheerfeeBedrag',
													'BeheerfeeTeruggaveHuisfondsenPercentage' => 'BeheerfeeTeruggaveHuisfondsPercentage',
													'BeheerfeeRemisiervergoedingsPercentage'  => 'BeheerfeeRemisiervergoedingsPercentage',
													'totaalTransactie'                        => 'BetaaldeProvisie',
													'beheerfeeBetalen'                        => 'TebetalenBeheerfee',
													'btw'                                     => 'BTW',
													'beheerfeeBetalenIncl'                    => 'TeBetalenBeheerfee+BTW',
													'stortingenOntrekkingen'                  => 'TotaalStortingen',
													'resultaat'                               => 'NettoVermogenstoename',
													'performancePeriode'                      => 'PerformancePeriode',
													'performanceJaar'                         => 'PerformanceJaar',
													'rekenvermogen'                           => 'Fee berekend over',
													'BeheerfeePercentageVermogenDeelVanJaar'  => 'BeheerfeePercentage',
													'nettoVermogenstoenameYtd'                => 'NettoVermogenstoenameYtd',
													'beginwaardeJaar'                         => 'BeginwaardeJaar',
													'periodeDeelVanJaar'                      => 'periodeDeelVanJaar',
													'huisfondsWaarde'                         => 'huisfondsWaarde',
													'debiteurnr'                              => 'debiteurnr');
				foreach ($dbVelden as $veld => $omschrijving)
				{
					$this->waardenDb[$veld] = $this->waarden[$veld];
				}
			}
     }
	}

	function getCsvHeader($layout)
	{

	  switch($layout)
		{
			case 5 :
			$this->pdf->excelOpmaak= array();
      $this->pdf->excelOpmaak['header']=array('setBgColor'=>'57','setFgColor'=>'black');
      $this->pdf->CsvHeader = array (
            'client'=>'Client',
		        'clientNaam'=>'Naam',
            'clientNaam1'=>'Naam1',
            'clientAdres'=>'Adres',
            'clientWoonplaats'=>'Woonplaats',
            'clientTelefoon'=>'Telefoon',
            'clientFax'=>'Fax',
            'clientEmail'=>'Email',
            'Accountmanager'=>'Account manager',
            'Depotbank'=> 'Depotbank',
            'datumVan'=> 'DatumVan',
            'datumTot' => 'DatumTot',
            'factuurNummer' => 'Factuurnummer',
            'portefeuille' => 'Portefeuille',
            'RapportageValuta' => 'valuta',
            'totaalWaardeVanaf' => 'Beginwaarde',
            'totaalWaarde' => 'Eindwaarde',
            'gemiddeldeVermogen'  => 'GemiddeldeWaarde',
            'beheerfeeOpJaarbasis' => 'BeheerfeePerJaar',
            'administratieBedrag' => 'BeheerfeeBedrag',
            'BeheerfeeTeruggaveHuisfondsenPercentage' => 'BeheerfeeTeruggaveHuisfondsPercentage',
            'BeheerfeeRemisiervergoedingsPercentage' => 'BeheerfeeRemisiervergoedingsPercentage',
            'totaalTransactie' => 'BetaaldeProvisie',
            'beheerfeeBetalen' => 'TebetalenBeheerfee',
            'btw' => 'BTW',
            'beheerfeeBetalenIncl' => 'TeBetalenBeheerfee+BTW',
            'stortingenOntrekkingen' => 'TotaalStortingen',
            'resultaat' => 'NettoVermogenstoename',
            'performancePeriode' => 'PerformancePeriode',
            'performanceJaar' => 'PerformanceJaar',
            'bestandsvergoeding'=>'bestandsvergoeding',
            'rekenvermogen' => 'Fee berekend over',
            'IBAN'=>'IBAN',
						'afwijkendeOmzetsoort'=>'afwijkendeOmzetsoort',
						'debiteurnr'=>'debiteurnr');
			break;
			case 12 :  // WATERLAND LET OP NUMMERIEKE KOLOMMAPPING EXACTONLINE EXPORT
      $this->pdf->CsvHeader = array (
			      'client'=>'Client',
		        'clientNaam'=>'Naam',
            'clientNaam1'=>'Naam1',
            'clientAdres'=>'Adres',
            'clientPostcode'=>'Postcode',
            'clientWoonplaats'=>'Woonplaats',
            'clientTelefoon'=>'Telefoon',
            'clientFax'=>'Fax',
            'clientEmail'=>'Email',
            'datumVan' => 'DatumVan',
            'datumTot' => 'DatumTot',
            'factuurNummer' => 'Factuurnummer',
            'portefeuille' => 'Portefeuille',
            'RapportageValuta' => 'valuta',
            'totaalWaardeVanaf' => 'Beginwaarde',
            'totaalWaarde' => 'Eindwaarde',
            'gemiddeldeVermogen'  => 'GemiddeldeWaarde',
            'maandsWaarde_1' => 'DrieMaandsUltimoWaarde 1',
            'maandsWaarde_2' => 'DrieMaandsUltimoWaarde 2',
            'maandsWaarde_3' => 'DrieMaandsUltimoWaarde 3',
            'rekenvermogen' => 'Fee berekend over',
            'beheerfeeOpJaarbasis' => 'BeheerfeePerJaar',
            'performancefee' => 'Performancefee',
            'administratieBedrag' => 'BeheerfeeBedrag',
            'BeheerfeeTeruggaveHuisfondsenPercentage' => 'BeheerfeeTeruggaveHuisfondsPercentage',
            'BeheerfeeRemisiervergoedingsPercentage' => 'BeheerfeeRemisiervergoedingsPercentage',
            'totaalTransactie' => 'BetaaldeProvisie',
            'beheerfeeBetalen' => 'TebetalenBeheerfee',
            'btw' => 'BTW',
            'beheerfeeBetalenIncl' => 'TeBetalenBeheerfee+BTW',
            'stortingenOntrekkingen' => 'TotaalStortingen',
            'resultaat' => 'NettoVermogenstoename',
            'performancePeriode' => 'PerformancePeriode',
            'performanceJaar' => 'PerformanceJaar',
            'bestandsvergoeding'=>'bestandsvergoeding',
            'IBAN'=>'IBAN',
						'afwijkendeOmzetsoort'=>'afwijkendeOmzetsoort',
						'debiteurnr'=>'debiteurnr',
				    'overigeKosten'=>'Overige kosten',
            'SoortOvereenkomst'=>'SoortOvereenkomst');
			break;
			case 25 :
						  $this->pdf->CsvHeader = array(
			      'client'=>'Client',
		        'clientNaam'=>'Naam',
            'clientNaam1'=>'Naam1',
            'clientAdres'=>'Adres',
            'clientPostcode'=>'Postcode',
            'clientWoonplaats'=>'Woonplaats',
            'clientTelefoon'=>'Telefoon',
            'clientFax'=>'Fax',
            'clientEmail'=>'Email',
            'datumVan' => 'DatumVan',
            'datumTot' => 'DatumTot',
            'factuurNummer' => 'Factuurnummer',
            'portefeuille' => 'Portefeuille',
            'RapportageValuta' => 'valuta',
            'totaalWaardeVanaf' => 'Beginwaarde',
            'totaalWaarde' => 'Eindwaarde',
            'gemiddeldeVermogen'  => 'GemiddeldeWaarde',
            'beheerfeeOpJaarbasis' => 'BeheerfeePerJaar',
            'performancefee' => 'Performancefee',
            'administratieBedrag' => 'BeheerfeeBedrag',
            'BeheerfeeTeruggaveHuisfondsenPercentage' => 'BeheerfeeTeruggaveHuisfondsPercentage',
            'BeheerfeeRemisiervergoedingsPercentage' => 'BeheerfeeRemisiervergoedingsPercentage',
            'totaalTransactie' => 'BetaaldeProvisie',
            'beheerfeeBetalen' => 'TebetalenBeheerfee',
            'btw' => 'BTW',
            'beheerfeeBetalenIncl' => 'TeBetalenBeheerfee+BTW',
            'stortingenOntrekkingen' => 'TotaalStortingen',
            'resultaat' => 'NettoVermogenstoename',
            'performancePeriode' => 'PerformancePeriode',
            'performanceJaar' => 'PerformanceJaar',
            'depotbankOmschrijving' => 'Depotbank',
            'BeheerfeePercentageVermogenDeelVanJaar' => 'BeheerfeePercentage',
            'CRM_naam'    => 'CRM_naam',
		        'CRM_naam1'    => 'CRM_naam1',
		        'CRM_verzendAanhef'    => 'VerzendAanhef',
		        'CRM_verzendAdres'  => 'Adres',
		        'CRM_verzendPc'         => 'Postcode',
		        'CRM_verzendPlaats'  => 'Plaats',
		        'CRM_verzendLand'   => 'Land',
		        'rekeningEur' => 'rekening EUR',
		        'basisRekenvermogen' => 'basisRekenvermogen',
		        'rekenvermogen' =>'rekenvermogen',
            'bestandsvergoeding'=>'bestandsvergoeding',
            'IBAN'=>'IBAN',
						'afwijkendeOmzetsoort'=>'afwijkendeOmzetsoort',
						'debiteurnr'=>'debiteurnr');
      break;
      case 38 :
						  $this->pdf->CsvHeader = array(
			      'client'=>'Client',
		        'clientNaam'=>'Naam',
            'clientNaam1'=>'Naam1',
            'clientAdres'=>'Adres',
            'clientPostcode'=>'Postcode',
            'clientWoonplaats'=>'Woonplaats',
            'clientTelefoon'=>'Telefoon',
            'clientFax'=>'Fax',
            'clientEmail'=>'Email',
            'datumVan' => 'DatumVan',
            'datumTot' => 'DatumTot',
            'factuurNummer' => 'Factuurnummer',
            'portefeuille' => 'Portefeuille',
            'Accountmanager'=>'Account manager',
            'RapportageValuta' => 'valuta',
            'totaalWaardeVanaf' => 'Beginwaarde',
            'totaalWaarde' => 'Eindwaarde',
            'gemiddeldeVermogen'  => 'GemiddeldeWaarde',
            'maandsWaarde_1' => 'DrieMaandsUltimoWaarde 1',
            'maandsWaarde_2' => 'DrieMaandsUltimoWaarde 2',
            'maandsWaarde_3' => 'DrieMaandsUltimoWaarde 3',
            'maandsGemiddelde' => 'drieMaandsGemiddelde',
            'beheerfeeOpJaarbasis' => 'BeheerfeePerJaar',
            'performancefee' => 'Performancefee',
            'administratieBedrag' => 'BeheerfeeBedrag',
            'BeheerfeeTeruggaveHuisfondsenPercentage' => 'BeheerfeeTeruggaveHuisfondsPercentage',
            'BeheerfeeRemisiervergoedingsPercentage' => 'BeheerfeeRemisiervergoedingsPercentage',
            'totaalTransactie' => 'BetaaldeProvisie',
            'beheerfeeBetalen' => 'TebetalenBeheerfee',
            'btw' => 'BTW',
            'beheerfeeBetalenIncl' => 'TeBetalenBeheerfee+BTW',
            'stortingenOntrekkingen' => 'TotaalStortingen',
            'resultaat' => 'NettoVermogenstoename',
            'performancePeriode' => 'PerformancePeriode',
            'performanceJaar' => 'PerformanceJaar',
            'depotbankOmschrijving' => 'Depotbank',
            'BeheerfeePercentageVermogenDeelVanJaar' => 'BeheerfeePercentage',
            'CRM_naam'    => 'CRM_naam',
		        'CRM_naam1'    => 'CRM_naam1',
		        'CRM_verzendAanhef'    => 'VerzendAanhef',
		        'CRM_verzendAdres'  => 'Adres',
		        'CRM_verzendPc'         => 'Postcode',
		        'CRM_verzendPlaats'  => 'Plaats',
		        'CRM_verzendLand'   => 'Land',
		        'rekeningEur' => 'rekening EUR',
            'bestandsvergoeding'=>'bestandsvergoeding',
            'BetalingsinfoMee'=>'BetalingsinfoMee',
            'rekenvermogen' => 'Fee berekend over',
            'IBAN'=>'IBAN',
						'SoortOvereenkomst'=>'SoortOvereenkomst',
						'huisfondsWaarde' => 'huisfondsWaarde',
						'BeheerfeeBedragBuitenBTWPeriode' => 'Bedrag buiten BTW',
						'overigeKosten'=>'Overige kosten',
						'afwijkendeOmzetsoort'=>'afwijkendeOmzetsoort',
						'debiteurnr'=>'debiteurnr');
      break;
      case 45 :
			  $this->pdf->CsvHeader = array(
			      'client'=>'Client',
		        'clientNaam'=>'Naam',
            'clientWoonplaats'=>'Woonplaats',
            'datumVan' => 'DatumVan',
            'datumTot' => 'DatumTot',
            'factuurNummer' => 'Factuurnummer',
            'portefeuille' => 'Portefeuille',
            'Accountmanager'=>'Accountmanager',
            'totaalWaardeVanaf' => 'Beginwaarde',
            'totaalWaarde' => 'Eindwaarde',
            'beheerfeeOpJaarbasis' => 'BeheerfeePerJaar',
            'performancefee' => 'Performancefee',
            'BeheerfeeTeruggaveHuisfondsenPercentage' => 'BeheerfeeTeruggaveHuisfondsPercentage',
            'BeheerfeeRemisiervergoedingsPercentage' => 'BeheerfeeRemisiervergoedingsPercentage',
            'totaalTransactie' => 'BetaaldeProvisie',
            'beheerfeeBetalen' => 'TebetalenBeheerfee',
            'btw' => 'BTW',
            'beheerfeeBetalenIncl' => 'TeBetalenBeheerfee+BTW',
            'stortingenOntrekkingen' => 'TotaalStortingen',
            'resultaat' => 'NettoVermogenstoename',
            'performancePeriode' => 'PerformancePeriode',
            'performanceJaar' => 'PerformanceJaar',
            'depotbankOmschrijving' => 'Depotbank',
            'BeheerfeePercentageVermogenDeelVanJaar' => 'BeheerfeePercentage',
            'huisfondsKorting'=>'huisfondsKorting',
            'huisfondsFeeJaar'=>'huisfondsFeeJaar',
            'periodeDeelVanJaar'=>'periodeDeelVanJaar',
            'transactiefee'=>'transactiefee',
            'rekenvermogen' => 'Fee berekend over',
            'IBAN'=>'IBAN',
						'afwijkendeOmzetsoort'=>'afwijkendeOmzetsoort',
						'debiteurnr'=>'debiteurnr'
             );
			break;
      case 55 :
			  $this->pdf->CsvHeader = array(
			      'client'=>'Client',
		        'clientNaam'=>'Naam',
            'clientNaam1'=>'Naam1',
            'clientAdres'=>'Adres',
            'clientPostcode'=>'Postcode',
            'clientWoonplaats'=>'Woonplaats',
            'clientTelefoon'=>'Telefoon',
            'clientFax'=>'Fax',
            'clientEmail'=>'Email',
            'datumVan' => 'DatumVan',
            'datumTot' => 'DatumTot',
            'factuurNummer' => 'Factuurnummer',
            'portefeuille' => 'Portefeuille',
            'RapportageValuta' => 'valuta',
            'totaalWaardeVanaf' => 'Beginwaarde',
            'totaalWaarde' => 'Eindwaarde',
            'gemiddeldeVermogen'  => 'GemiddeldeWaarde',
            'beheerfeeOpJaarbasis' => 'BeheerfeePerJaar',
            'performancefee' => 'Performancefee',
            'administratieBedrag' => 'BeheerfeeBedrag',
            'BeheerfeeTeruggaveHuisfondsenPercentage' => 'BeheerfeeTeruggaveHuisfondsPercentage',
            'BeheerfeeRemisiervergoedingsPercentage' => 'BeheerfeeRemisiervergoedingsPercentage',
            'totaalTransactie' => 'BetaaldeProvisie',
            'beheerfeeBetalen' => 'TebetalenBeheerfee',
            'btw' => 'BTW',
            'beheerfeeBetalenIncl' => 'TeBetalenBeheerfee+BTW',
            'stortingenOntrekkingen' => 'TotaalStortingen',
            'resultaat' => 'NettoVermogenstoename',
            'performancePeriode' => 'PerformancePeriode',
            'performanceJaar' => 'PerformanceJaar',
            'depotbankOmschrijving' => 'Depotbank',
            'BeheerfeePercentageVermogenDeelVanJaar' => 'BeheerfeePercentage',
            'CRM_naam'    => 'CRM_naam',
		        'CRM_naam1'    => 'CRM_naam1',
		        'CRM_verzendAanhef'    => 'VerzendAanhef',
		        'CRM_verzendAdres'  => 'Adres',
		        'CRM_verzendPc'         => 'Postcode',
		        'CRM_verzendPlaats'  => 'Plaats',
		        'CRM_verzendLand'   => 'Land',
		        'rekeningEur' => 'rekening EUR',
            'bestandsvergoeding'=>'bestandsvergoeding',
            'BetalingsinfoMee'=>'BetalingsinfoMee',
            'huisfondsKorting'=>'huisfondsKorting',
            'huisfondsFeeJaar'=>'huisfondsFeeJaar',
            'periodeDeelVanJaar'=>'periodeDeelVanJaar',
            'transactiefee'=>'transactiefee',
            'rekenvermogen' => 'Fee berekend over',
            'IBAN'=>'IBAN',
            'maandsWaarde_1' => 'VierMaandsUltimoWaarde 1',
            'maandsWaarde_2' => 'VierMaandsUltimoWaarde 2',
            'maandsWaarde_3' => 'VierMaandsUltimoWaarde 3',
            'maandsWaarde_4' => 'VierMaandsUltimoWaarde 4',
            'NettoVermogenstoenameYtd' => 'NettoVermogenstoenameYtd',
            'BeginwaardeJaar' => 'BeginwaardeJaar',
						'afwijkendeOmzetsoort'=>'afwijkendeOmzetsoort',
						'debiteurnr'=>'debiteurnr'
             ); 
			break;      
			default : // DOO LET OP NUMMERIEKE KOLOMMAPPING EXACT EXPORT
			  $this->pdf->CsvHeader = array(
			      'client'=>'Client',
		        'clientNaam'=>'Naam',
            'clientNaam1'=>'Naam1',
            'clientAdres'=>'Adres',
            'clientPostcode'=>'Postcode',
            'clientWoonplaats'=>'Woonplaats',
            'clientTelefoon'=>'Telefoon',
            'clientFax'=>'Fax',
            'clientEmail'=>'Email',
            'datumVan' => 'DatumVan',
            'datumTot' => 'DatumTot',
            'factuurNummer' => 'Factuurnummer',
            'portefeuille' => 'Portefeuille',
            'RapportageValuta' => 'valuta',
            'totaalWaardeVanaf' => 'Beginwaarde',
            'totaalWaarde' => 'Eindwaarde',
            'gemiddeldeVermogen'  => 'GemiddeldeWaarde',
            'beheerfeeOpJaarbasis' => 'BeheerfeePerJaar',
            'performancefee' => 'Performancefee',
            'administratieBedrag' => 'BeheerfeeBedrag',
            'BeheerfeeTeruggaveHuisfondsenPercentage' => 'BeheerfeeTeruggaveHuisfondsPercentage',
            'BeheerfeeRemisiervergoedingsPercentage' => 'BeheerfeeRemisiervergoedingsPercentage',
            'totaalTransactie' => 'BetaaldeProvisie',
            'beheerfeeBetalen' => 'TebetalenBeheerfee',
            'btw' => 'BTW',
            'beheerfeeBetalenIncl' => 'TeBetalenBeheerfee+BTW',
            'stortingenOntrekkingen' => 'TotaalStortingen',
            'resultaat' => 'NettoVermogenstoename',
            'performancePeriode' => 'PerformancePeriode',
            'performanceJaar' => 'PerformanceJaar',
            'depotbankOmschrijving' => 'Depotbank',
            'BeheerfeePercentageVermogenDeelVanJaar' => 'BeheerfeePercentage',
            'CRM_naam'    => 'CRM_naam',
		        'CRM_naam1'    => 'CRM_naam1',
		        'CRM_verzendAanhef'    => 'VerzendAanhef',
		        'CRM_verzendAdres'  => 'Adres',
		        'CRM_verzendPc'         => 'Postcode',
		        'CRM_verzendPlaats'  => 'Plaats',
		        'CRM_verzendLand'   => 'Land',
		        'rekeningEur' => 'rekening EUR',
            'bestandsvergoeding'=>'bestandsvergoeding',
            'BetalingsinfoMee'=>'BetalingsinfoMee',
            'huisfondsKorting'=>'huisfondsKorting',
            'huisfondsFeeJaar'=>'huisfondsFeeJaar',
            'periodeDeelVanJaar'=>'periodeDeelVanJaar',
            'transactiefee'=>'transactiefee',
            'rekenvermogen' => 'Fee berekend over',
            'IBAN'=>'IBAN',
            'SoortOvereenkomst'=>'SoortOvereenkomst',
						'huisfondsWaarde' => 'huisfondsWaarde',
						'BeheerfeeBedragBuitenBTWPeriode' => 'Bedrag buiten BTW',
				  	'Accountmanager'=>'Accountmanager',
						'overigeKosten'=>'Overige kosten',
						'afwijkendeOmzetsoort'=>'afwijkendeOmzetsoort',
					  'debiteurnr'=>'debiteurnr'
             );
			break;
		}
     /*
    [huisfondsKorting] =>
    [remisierBedrag] =>
		[BeheerfeeAantalFacturen] =>
    [BeheerfeeBasisberekening] =>
    [BeheerfeePercentageVermogenDeelVanJaar] =>
    [MinJaarbedragGebruikt] =>
    [SoortOvereenkomst] =>
    [beheerfeePerPeriode] =>
    [btwTarief] =>
    [kwartaal] =>
    [onttrekkingen] =>
    [periodeDeelVanJaar] =>
    [rapportJaar] =>
    [rekenvermogen] =>
    [stortingen] =>
    [waardeLiquiditeitenEind] =>
    [waardeLiquiditeitenVanaf] =>
		*/
	}

}
?>
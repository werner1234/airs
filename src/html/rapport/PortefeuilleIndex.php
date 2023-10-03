<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/11/16 17:36:34 $
 		File Versie					: $Revision: 1.9 $

 		$Log: PortefeuilleIndex.php,v $
 		Revision 1.9  2019/11/16 17:36:34  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2011/09/14 09:26:56  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2009/03/14 13:24:27  rvv
 		*** empty log message ***

 		Revision 1.6  2007/08/02 14:46:01  rvv
 		*** empty log message ***

 		Revision 1.5  2007/04/03 13:26:33  rvv
 		*** empty log message ***

 		Revision 1.4  2007/02/21 11:04:26  rvv
 		Client toevoeging

 		Revision 1.3  2006/09/22 09:19:41  rvv
 		Berekening aangepast
 		$perfIndex = ($datum[$i]['performance'] * $jaarmultiplier + 100 );
 		naar
 		$perfIndex = ($datum[$i]['performance'] + 100)*$jaarmultiplier;

 		Revision 1.2  2006/09/21 07:24:25  rvv
 		Performance meting startdatum nu vanaf eind vorige jaar ipv vorige maand.
 		Geen performance geeft nu een leeg veld.
 		Voor eerste meting 100,00 weergeven.

 		Revision 1.1  2006/09/19 11:10:45  rvv
 		Toevoeging Portefeuille index


*/

include_once("rapportRekenClass.php");

class PortefeuilleIndex
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function PortefeuilleIndex( $selectData )
	{
		$this->pdf = new PDFRapport('L','mm');
	  $this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->orderby  = " Portefeuilles.ClientVermogensbeheerder ";

		$this->pdf->excelData = array();
	}


	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{

		$einddatum = jul2sql($this->selectData[datumTm]);


		$fondswaardenClean = array();
		$fondswaardenRente = array();
		$rekeningwaarden 	 = array();

		$jaar = date("Y",$this->datumTm);

    $selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();


		if($records <= 0)
		{
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
			$eindjaar = date("Y",$this->selectData['datumTm']);
			$eindmaand = date("m",$this->selectData['datumTm']);
			$beginjaar = date("Y",$this->selectData['datumVan']);
			$startjaar = date("Y",$this->selectData['datumVan']);
			$beginmaand = date("m",$this->selectData['datumVan']);
			$ready = false;
			$i=0;
			$stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
			$jaarmultiplier = 1;

			while ($ready == false)
			{
				$datum[$i]['start']=jul2db(mktime (0,0,0,1,0,$startjaar));
				$datum[$i]['stop']=jul2db(mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar));
				$datum[$i]['jaarmultiplier'] = $jaarmultiplier;
				if (db2jul($datum[$i]['stop']) > mktime (0,0,0,1,0,$startjaar+1)) //jaar oversteek.
				{
					$startjaar ++;
					$datum[$i]['start']=jul2db(mktime (0,0,0,1,0,$startjaar));
					$datum[$i]['stop']=jul2db(mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar));
					$datum[$i]['jaarmultiplierset'] = "yes";
				}


			if (mktime (0,0,0,$beginmaand+$i+2,0,$beginjaar) > $stop)
			  {
		 	  $ready = true;
			  }
			$i++;
			}
		// print CSV kop
		$this->pdf->excelData[] = array("nr",
								 "Client",
								 "Portefeuille",
								 "Risico Klasse",
								 );
		for ($i=0; $i < count($datum); $i++) //Maanden toevoegen aan header.
		{
		  array_push($this->pdf->excelData['0'], date("M Y",db2jul($datum[$i]['stop'])));
		}

	 	$j=1;  //regelnummer
		foreach($portefeuilles as $pdata)
		{

			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
			}
			$portefeuille = $pdata[Portefeuille];

		for ($i=0; $i < count($datum); $i++) //Bereken Performance voor data
		{
			if ($i==0)
			{
			  $jaarmultiplier = 1;
			}
			  $datum[$i]['jaarmultiplier'] = $jaarmultiplier;
			if(db2jul($pdata['Startdatum']) > db2jul($datum[$i]['stop'])) //Wanneer de portefeuille nog niet bestond geen performance.
			{
			$datum[$i]['performance']=0;
			}
			elseif(db2jul($pdata['Startdatum']) > db2jul($datum[$i]['start']) ) //Wanneer de portefeuille nog niet op 31-12 vorig jaar bestond andere startdatum
			{
		  	  $fondswaarden[a] =  berekenPortefeuilleWaardeQuick($portefeuille,  $pdata['Startdatum']);
		  	  $fondswaarden[b] =  berekenPortefeuilleWaardeQuick($portefeuille,  $datum[$i]['stop']);
		  	  vulTijdelijkeTabel($fondswaarden[a] ,$portefeuille,$pdata['Startdatum']);
		  	  vulTijdelijkeTabel($fondswaarden[b] ,$portefeuille,$datum[$i]['stop']);

			  $perf = performanceMeting($portefeuille, $pdata['Startdatum'], $datum[$i]['stop'],$pdata['PerformanceBerekening']);
	  		  $datum[$i]['performance'] = $perf;
			  $datum[$i]['portefeuille'] = $portefeuille;//extra debug info

			  //verwijderTijdelijkeTabel($portefeuille,$pdata['Startdatum']);
	    	//  verwijderTijdelijkeTabel($portefeuille,$datum[$i]['stop']);
			}
			else // Normale berekening.
			{
			  if ($datum[$i-1]['start'] != $datum[$i]['start'])
			  {
			    $fondswaarden[a] =  berekenPortefeuilleWaardeQuick($portefeuille,  $datum[$i]['start']);
			    vulTijdelijkeTabel($fondswaarden[a] ,$portefeuille,$datum[$i]['start']);
		      }
		  	  $fondswaarden[b] =  berekenPortefeuilleWaardeQuick($portefeuille,  $datum[$i]['stop']);
		  	  vulTijdelijkeTabel($fondswaarden[b] ,$portefeuille,$datum[$i]['stop']);

			  $perf = performanceMeting($portefeuille, $datum[$i]['start'], $datum[$i]['stop'],$pdata['PerformanceBerekening']);
	  		  $datum[$i]['performance'] = $perf;
			  $datum[$i]['portefeuille'] = $portefeuille;//extra debug info

		   	  if ($datum[$i+1]['start'] != $datum[$i]['start'])
			  {
				//verwijderTijdelijkeTabel($portefeuille,$datum[$i]['start']);
			  }
	    	//	verwijderTijdelijkeTabel($portefeuille,$datum[$i]['stop']);
			}
		  }
		  verwijderTijdelijkeTabel($portefeuille);

			// schrijf data !
			$this->pdf->excelData[] = array($j,
									 $pdata[Client],
									 $pdata[Portefeuille],
									 $pdata[Risicoklasse],
									 );
			for ($i=0; $i < count($datum); $i++)
			{
			  if ($datum[$i]['jaarmultiplierset'] == "yes" )
			  {
			  	$jaarmultiplier = $datum[$i-1]['jaarmultiplier'] * (($datum[$i-1]['performance'] + 100 )/100);
			  }
			  $datum[$i]['jaarmultiplier']= $jaarmultiplier;
			  $perfIndex = ($datum[$i]['performance'] + 100)*$jaarmultiplier;
			  $datum[$i]['performance-index']=$perfIndex;
			  if(db2jul($pdata['Startdatum']) > db2jul($datum[$i]['stop']))
			  {
			  	$this->pdf->excelData[$j][$i+4] = "";
			  }
			  else
			  {
			  	if ($i > 1 && $this->pdf->excelData[$j][$i+2] == "" )
			  		 $this->pdf->excelData[$j][$i+3] = $this->formatGetal(100,2);	//Toon 100 in het veld voor de eerstbekende performance
			  	$this->pdf->excelData[$j][$i+4] = $this->formatGetal($perfIndex,2);
			  }
			}
		$j++; //regelnummer verhogen.
		}

		if($this->progressbar)
			$this->progressbar->hide();
	}

}
?>
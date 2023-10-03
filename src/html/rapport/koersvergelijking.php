<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/08/02 18:22:25 $
 		File Versie					: $Revision: 1.1 $

 		$Log: koersvergelijking.php,v $
 		Revision 1.1  2017/08/02 18:22:25  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2017/07/30 10:19:17  rvv
 		*** empty log message ***
 		


*/
include_once("rapportRekenClass.php");

class koersVergelijking
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function koersVergelijking( $selectData )
	{
		$this->selectData = $selectData;
		$this->pdf->excelData 	= array();

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "koersControle";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);
		$this->aantalfondsen =0;
		$this->valutas =0;

		$this->pdf->tmdatum = $this->selectData['datumTm'];
		// selectdata ook aan PDF geven
		$this->pdf->selectData = $this->selectData;
		$this->labelPerRegel=array();
		$this->onbekendekoers=array();
		$this->fondsen = array();
		$this->koersControleCheck='';
		$this->orderby = " Client ";
		$this->wherePortefeuilles='';
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function valutaCheck()
	{
		// check Valuta
		global $__appvar,$USR;
		$date = jul2sql($this->selectData['datumTm'],true);
		$datum =date("d-m-Y",$this->selectData['datumTm']);

		$DB=new DB();
		$actieveValuta = array();
		if($__appvar["bedrijf"]=="HOME")
		{
			$query = "SELECT Valuta FROM ActieveFondsen GROUP BY Valuta";
			$DB->SQL($query);
			$DB->Query();

			while ($valutadata = $DB->NextRecord())
			{
				$actieveValuta[] = $valutadata['Valuta'];
			}
		}
		if(count($actieveValuta)>0)
			$query = "SELECT Valuta FROM Valutas WHERE Valuta IN ('".implode("','",$actieveValuta)."')";
		else
			$query = "SELECT Valuta FROM Valutas";
		$DB->SQL($query);
		$DB->Query();

		$pro_multiplier = (100 / ($DB->Records()+1));

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$this->progressbar->setLabelValue('txt1', 'Valuta controle...');
			$pro_step = 0;
		}
		$DB2 	= new DB();
		while($data = $DB->NextRecord())
		{
			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
			}
			$query = "SELECT id FROM Valutakoersen WHERE datum = '".$date."' AND Valuta = '".$data['Valuta']."'";
			$DB2->SQL($query);
			$DB2->Query();
			if($DB2->Records() <= 0)
			{
				// if aanvullen, aanvullen koers, haal laatste op!
				if($this->selectData['aanvullen'] == 1)
				{
					//select
					$query = "SELECT Koers FROM Valutakoersen WHERE Valuta = '".$data['Valuta']."' ORDER BY datum DESC LIMIT 1";
					$DB2->SQL($query);
					$DB2->Query();
					if($DB2->Records() == 1)
					{
						$koers = $DB2->NextRecord();
						$query = "INSERT INTO Valutakoersen SET datum = '".$date."' , Valuta = '".$data['Valuta']."', Koers = '".$koers['Koers']."', ".
							" add_user = '".$USR."', ".
							" add_date = NOW(), ".
							" change_user = '".$USR."',".
							" change_date = NOW()";
						$DB2->SQL($query);
						$DB2->Query();
						$this->valutas++;
					}
					else
					{
						$this->onbekendekoers[] = "Valutakoers ".$data['Valuta']." : geen laatst ingevoerde koerswaarde gevonden!";
					}
				}
				else
				{
					$query = "SELECT datum FROM Valutakoersen WHERE Valuta = '".$data['Valuta']."' AND datum <='".$date."' ORDER BY datum DESC LIMIT 1";
					$DB2->SQL($query);
					$DB2->Query();
					$laatsteKoers=$DB2->NextRecord();
					$this->onbekendekoers[] = "Valutakoers ".$data['Valuta']." op ".$datum." niet gevonden (laatste ".date('d-m-Y',db2jul($laatsteKoers['datum'])).")";
				}
			}
		}
		return $this->onbekendekoers;
	}

	function bepaalActieveFondsen()
	{
		include_once("../classes/bepaalActieveFondsenClass.php");
		$actieveFondsen = new bepaalActieveFondsen();
		$actieveFondsen->verbose=true;
		$actieveFondsen->createTable();
		$actieveFondsen->fillTable();
	  $xlsdata=$actieveFondsen->createXls(true);
		foreach($xlsdata as $row)
			$this->pdf->excelData[]=$row;
	}

	function checkOuderdom()
	{
     global $__appvar;

		$date = jul2sql($this->selectData['datumTm'],true);
		$dateVan = jul2sql($this->selectData['datumVan'],true);

		if($this->progressbar)
		{
			$pro_multiplier = (100 / (count($this->fondsen) + 1));
			$this->progressbar->moveStep(0);
			$this->progressbar->setLabelValue('txt1', 'Koers controle...');
			$pro_step = 0;
		}
		$DB2 	= new DB();

		// clean array
		$this->pdf->excelData[]=array('Fonds','FondsImportCode','fondssoort','koersControleOverslaan',
			'laatste datum','laatste koers','voorLaatste datum','voorlaatste koers','verschil','percentage','absoluut%','add_date','label','koersmethodiek','aantalPortefeuilles');
		foreach ($this->fondsen as $fonds=>$fondsData)
		{
			$query="SELECT Fonds,FondsImportCode,ISINCode,identifierVWD,identifierFactSet,
      koersmethodiek,Fondseenheid,Einddatum,Lossingsdatum,OptieExpDatum,Valuta,KoersAltijdAanvragen,fondssoort,koersControle
      FROM Fondsen WHERE Fonds='".mysql_real_escape_string($fonds)."' ".$this->FondsEinddatumFilter." ".$this->koersControleCheck;
			$DB2->SQL($query);
			$fondsInfo=$DB2->lookupRecord();
			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
			}
			if(isset($fondsInfo['Fonds']))
			{
				$koersen=array();
				$query = "SELECT Koers, datum, add_date FROM Fondskoersen WHERE Fonds = '".$fonds."' AND datum <= '".$date."' ORDER BY datum DESC LIMIT 1";
				$DB2->SQL($query);
				$DB2->Query();
				$koersen[0]=$DB2->nextRecord();

				$query = "SELECT Koers, datum, add_date FROM Fondskoersen WHERE Fonds = '".$fonds."' AND datum <= '".$dateVan."' ORDER BY datum DESC LIMIT 1";
				$DB2->SQL($query);
				$DB2->Query();
				$koersen[1]=$DB2->nextRecord();

					$verschil='';
					$procentVerschil='';
					if($koersen[0]['Koers'] <> '' && $koersen[1]['Koers'] <>'')
					{
						$verschil=$koersen[0]['Koers']-$koersen[1]['Koers'];
						$procentVerschil=round($verschil/$koersen[1]['Koers']*100,3);
					}
					if(isset($this->labelPerRegel[$fondsInfo['Fonds']]))
					{
						$label=$this->labelPerRegel[$fondsInfo['Fonds']];
						unset($this->labelPerRegel[$fondsInfo['Fonds']]);
					}
					else
						$label='geen koersupdate';

				if($this->selectData['ouderdomDagen'] <> '')
				{

					if(($this->selectData['datumTm'] -db2jul($koersen[0]['datum'])) >  ($this->selectData['ouderdomDagen'] * 86400))
					{
					  echo "Laatste koers van $fonds is ".(($this->selectData['datumTm'] -db2jul($koersen[0]['datum']))/86400)." dagen oud. Fonds overgeslagen.<br>\n";
            continue;
					}

					if ((db2jul($koersen[0]['datum']) - db2jul($koersen[1]['datum'])) > ($this->selectData['ouderdomDagen'] * 86400))
					{
						echo "Koersen voor $fonds liggen " .((db2jul($koersen[0]['datum'])-db2jul($koersen[1]['datum']))/86400)." dagen uit elkaar. Fonds overgeslagen<br>\n";
						continue;
					}
				}

				if($this->selectData['ouderdomPercentage'] <> '')
				{
					if($procentVerschil<$this->selectData['ouderdomPercentage'])
						continue;
				}
					$tmp=array($fonds,$fondsInfo['FondsImportCode'],$fondsInfo['fondssoort'],$fondsInfo['koersControle'],
						substr($koersen[0]['datum'],0,10),$koersen[0]['Koers'],
						substr($koersen[1]['datum'],0,10),$koersen[1]['Koers'],
						round($verschil,3),
						$procentVerschil,
						abs($procentVerschil),
						date('d-m-Y',db2jul($koersen[0]['add_date'])),
						$label,
						$fondsInfo['koersmethodiek'],
						$fondsData['PortefeuilleAantal']);
					$this->pdf->excelData[]=$tmp;


			}
		}

			if(isset($this->labelPerRegel))
			{
				$this->pdf->excelData[]=array('inactieve fondsen.');
				foreach($this->labelPerRegel as $fonds=>$label)
				{
					$query="SELECT Fonds,FondsImportCode,fondssoort,koersControle,koersmethodiek FROM Fondsen WHERE Fonds='".mysql_real_escape_string($fonds)."' ";
					$DB2->SQL($query);
					$fondsInfo=$DB2->lookupRecord();

					$query = "SELECT Koers, datum, add_date FROM Fondskoersen WHERE Fonds = '".$fonds."' AND datum <= '".$date."' ORDER BY datum DESC LIMIT 2";
					$DB2->SQL($query);
					$DB2->Query();
					$koersen=array();
					while ($data=$DB2->nextRecord())
					{
						$koersen[]=$data;
					}
					$verschil='';
					$procentVerschil='';
					if($koersen[0]['Koers'] <> '' && $koersen[1]['Koers'] <>'')
					{
						$verschil=$koersen[0]['Koers']-$koersen[1]['Koers'];
						$procentVerschil=round($verschil/$koersen[1]['Koers']*100,3);
					}

					$tmp=array($fonds,$fondsInfo['FondsImportCode'],$fondsInfo['fondssoort'],$fondsInfo['koersControle'],
						substr($koersen[0]['datum'],0,10),$koersen[0]['Koers'],
						substr($koersen[1]['datum'],0,10),$koersen[1]['Koers'],
						round($verschil,3),
						$procentVerschil,
						abs($procentVerschil),
						date('d-m-Y',db2jul($koersen[0]['add_date'])),
						$label,
						$fondsInfo['koersmethodiek']);
					$this->pdf->excelData[]=$tmp;
				}
			}
	}


	function bepaalFondsen()
	{
		global $USR;
		$date = jul2sql($this->selectData['datumTm'],true);
		$jaar = date("Y",$this->selectData['datumTm'] );
		$extraquery  = " Portefeuilles.Einddatum > '".$date."' AND";

		if(is_array($this->selectData['geimporteerdeFondsen']))
			$extraquery .= " Rekeningmutaties.Fonds IN ('".implode("','",$this->selectData['geimporteerdeFondsen'])."') AND";


		if($this->selectData['koersControleCheck']==1)
			$this->koersControleCheck="AND Fondsen.koersControle='0' ";
		else
			$this->koersControleCheck='';

		$q="SELECT
round(Sum(Rekeningmutaties.Aantal),4) AS Aantal,
Rekeningmutaties.Fonds,
Portefeuilles.Portefeuille
FROM
Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
INNER JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.Einddatum > '".$date."'
WHERE $extraquery
Rekeningmutaties.Boekdatum >= '".$jaar."-01-01' AND Rekeningmutaties.Boekdatum <= '".$date."' AND Rekeningmutaties.Fonds <> '' ".$this->wherePortefeuilles."
GROUP BY Rekeningmutaties.Fonds,Portefeuilles.Portefeuille
HAVING Aantal <> 0 
ORDER BY Rekeningmutaties.Fonds";

		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();


		while($data=$DB->nextRecord())
		{
			if(!isset($this->fondsen[$data['Fonds']]))
			{
				$this->fondsen[$data['Fonds']]=array();
				$this->fondsen[$data['Fonds']]['Aantal']=0;
				$this->fondsen[$data['Fonds']]['PortefeuilleAantal']=0;
			}
			$this->fondsen[$data['Fonds']]['PortefeuilleAantal']++;
			$this->fondsen[$data['Fonds']]['Aantal']+=$data['Aantal'];
		}


		$q = "SELECT Indices.Beursindex FROM Indices GROUP BY Indices.Beursindex ORDER BY Indices.Beursindex";
		$DB->SQL($q);
		$DB->Query();
		while($fonds = $DB->NextRecord())
		{
			if(!isset($this->fondsen[$fonds['Beursindex']]))
			{
				$this->fondsen[$fonds['Beursindex']]=array();
				$this->fondsen[$fonds['Beursindex']]['Aantal']=0;
			}
		}
		$q = "SELECT Fondsen.Fonds,Fondsen.EindDatum FROM Fondsen WHERE Fondsen.fondssoort='INDEX' AND Fondsen.EindDatum='0000-00-00'";
		$DB->SQL($q);
		$DB->Query();
		while($fonds = $DB->NextRecord())
		{
			if(!isset($this->fondsen[$fonds['Fonds']]))
			{
				$this->fondsen[$fonds['Fonds']]=array();
				$this->fondsen[$fonds['Fonds']]['Aantal']=0;
			}
		}


	}


	function writeRapport()
	{
		global $__appvar;
		if(!checkAccess("superapp"))
			$this->selectData['aanvullen']=0;

		if($this->selectData['gebruikPortefeuilleSelectie']==1)
		{
			$selectie = new portefeuilleSelectie($this->selectData, $this->orderby);
			$portefeuilles = $selectie->getSelectie();
			$portefeuilleList = array_keys($portefeuilles);
			$this->wherePortefeuilles = " AND Portefeuilles.Portefeuille IN('" . implode("','", $portefeuilleList) . "') ";
		}
    
    if($this->selectData['gebruikInactieveFondsen']==1)
      $this->FondsEinddatumFilter="";
    else
      $this->FondsEinddatumFilter="AND (Einddatum > now() OR Einddatum < '1980-01-01') ";

		$this->pdf->excelData[]=array("Ouderdomsanalyse (".date("d-m-Y",$this->selectData['datumTm']).")");
		$this->valutaCheck();
		$this->bepaalFondsen();
		$this->checkOuderdom();


		for ($a=0; $a < count($this->onbekendekoers); $a++)
		{
			echo "<br>".$this->onbekendekoers[$a];
		}

		if($this->progressbar)
			$this->progressbar->hide();
	}


}
?>
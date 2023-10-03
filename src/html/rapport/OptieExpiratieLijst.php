<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/05/25 09:42:51 $
 		File Versie					: $Revision: 1.16 $

 		$Log: OptieExpiratieLijst.php,v $
 		Revision 1.16  2020/05/25 09:42:51  cvs
 		call 8640
 		
 		Revision 1.15  2012/06/23 15:19:58  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2012/02/19 16:12:11  rvv
 		*** empty log message ***

 		Revision 1.13  2011/09/25 16:24:17  rvv
 		*** empty log message ***

 		Revision 1.12  2010/11/14 10:41:30  rvv
 		Opties via selectieclass

 		Revision 1.11  2010/07/28 17:18:43  rvv
 		*** empty log message ***

 		Revision 1.10  2009/04/05 09:24:18  rvv
 		*** empty log message ***

 		Revision 1.9  2009/03/25 17:47:01  rvv
 		*** empty log message ***

 		Revision 1.8  2008/06/30 07:58:44  rvv
 		*** empty log message ***

 		Revision 1.7  2008/05/16 08:12:57  rvv
 		*** empty log message ***

 		Revision 1.6  2007/08/02 14:46:01  rvv
 		*** empty log message ***

 		Revision 1.5  2007/06/05 11:38:25  rvv
 		*** empty log message ***

 		Revision 1.4  2007/04/03 13:26:33  rvv
 		*** empty log message ***

 		Revision 1.3  2007/02/21 11:04:26  rvv
 		Client toevoeging

 		Revision 1.2  2006/12/14 12:57:24  rvv
 		Aandeelkoers toegevoegd.

 		Revision 1.1  2006/12/05 12:12:24  rvv
 		Optie toevoeging


*/

include_once("rapportRekenClass.php");

class OptieExpiratieLijst
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function OptieExpiratieLijst(  $selectData )
	{

		$this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->pdf = new PDFOptieOverzicht('L','mm');
		$this->pdf->rapport_type = "optieExpiratieLijst";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);

		$this->pdf->SetFont("Times","",10);


		$this->pdf->vandatum = $this->selectData[datumVan];
		$this->pdf->tmdatum = $this->selectData[datumTm];
		$this->pdf->OptieExpJaar = $this->selectData['expiratieJaar'] ;
		$this->pdf->OptieExpMaand = $this->selectData['expiratieMaand'];

	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
	  global $__appvar;

  	$jaar = date("Y",$this->selectData['datumTm']);
		$this->pdf->__appvar = $this->__appvar;

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

		$rapportageDatum[a] = jul2sql($this->selectData['datumVan']);
		$rapportageDatum[b] = jul2sql($this->selectData['datumTm']);

    $expiratieJaar = $this->selectData['expiratieJaar'];
	  $expiratieMaand = $this->selectData['expiratieMaand'];

    if ($expiratieMaand <10 )
      $expiratieMaand='0'.$expiratieMaand;


		if ($expiratieJaar == '' && $expiratieJaar == '')
		  $fondsenExpiratieWhere = "Fondsen.OptieExpDatum <> '' AND" ;
		else
		  $fondsenExpiratieWhere = "Fondsen.OptieExpDatum = '".$expiratieJaar.$expiratieMaand."' AND" ;

		foreach($portefeuilles as $pdata)
		{

		  $crmNaam=getCrmNaam($pdata['Portefeuille']);

		  $query="SELECT profielOverigeBeperkingen FROM CRM_naw WHERE Portefeuille='".$pdata['Portefeuille']."'";
		  $db=new DB();
		  $db->SQL($query);
		  $profielOverigeBeperkingen=$db->lookupRecord();
		  $pdata['profielOverigeBeperkingen']=$profielOverigeBeperkingen['profielOverigeBeperkingen'];

      if($crmNaam)
      {
        $pdata['Naam'] = $crmNaam['naam'];
        $pdata['Naam1'] = $crmNaam['naam1'];
      }

			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
			}

			$portefeuille = $pdata['Portefeuille'];
			if(db2jul($rapportageDatum['a']) < db2jul($pdata['Startdatum']))
			{
				$startdatum = $pdata['Startdatum'];
			}
			else
			{
				$startdatum = $rapportageDatum['a'];
			}
			$julrapport 		= db2jul($startdatum);
			$rapportMaand 	= date("m",$julrapport);
			$rapportDag 		= date("d",$julrapport);

			if($rapportMaand == 1 && $rapportDag == 1)
				$startjaar = true;
			else
				$startjaar = false;

			$einddatum = $rapportageDatum['b'];


		$query1 = 	"SELECT
	 				Rekeningmutaties.Fonds,
	 				Fondsen.OptieBovenliggendFonds,
	 				Fondsen.Valuta
	 				FROM (Rekeningmutaties, Rekeningen, Portefeuilles)
					JOIN Fondsen on Fondsen.Fonds =  Rekeningmutaties.Fonds
					WHERE
					$fondsenExpiratieWhere
	 				Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	 				Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND  Portefeuilles.Portefeuille = '$portefeuille' AND
	 				Rekeningen.consolidatie = 0 AND
	 				YEAR(Rekeningmutaties.Boekdatum) = '$jaar' AND
	 				Rekeningmutaties.Verwerkt = '1' AND
	 				Rekeningmutaties.Boekdatum <= '$einddatum' AND
	 				Rekeningmutaties.GrootboekRekening = 'FONDS'
	 				GROUP BY Rekeningmutaties.Fonds ";

		$DB2 = new DB();
		$DB2->SQL($query1);

		$DB2->Query();

		$records = $DB2->records();

		$fondsen = array();
		while($fonds = $DB2->NextRecord())
		{
		  $fondsen[] = $fonds;
		}

		$fondswaarden = array();
		for($a=0; $a <count($fondsen); $a++)
		{
		$fondswaarden[$fondsen[$a]['Fonds']] = fondsAantalOpdatum($portefeuille, $fondsen[$a]['Fonds'], $einddatum);
		$fonds 	= $fondsen[$a];
		$data 	= $fondswaarden[$fonds['Fonds']];

  		if(round($data[totaalAantal],4) <> 0)
		  {

			// bereken totalen met actuele koers
			$actuelePortefeuilleWaardeInValuta 	= ($data['fondsEenheid']  * $data['totaalAantal']) * $data['actueleFonds'];
			$actuelePortefeuilleWaardeEuro 		=  $data['actueleValuta'] * $actuelePortefeuilleWaardeInValuta;

			// maak nieuwe schone array
			$clean = $data;
			$clean['beginPortefeuilleWaardeInValuta'] 	= $beginPortefeuilleWaardeInValuta;
			$clean['beginPortefeuilleWaardeEuro'] 		= $beginPortefeuilleWaardeEuro;
			$clean['actuelePortefeuilleWaardeInValuta'] 	= $actuelePortefeuilleWaardeInValuta;
			$clean['actuelePortefeuilleWaardeEuro'] 		= $actuelePortefeuilleWaardeEuro;
			$clean['fonds'] 								= $fonds['Fonds'];
			$clean['beleggingssector'] 					= $fonds['Beleggingssector'];
			$clean['beleggingscategorie'] 				= $fonds['Beleggingscategorie'];

			$q = "SELECT Koers,Datum FROM Fondskoersen WHERE Fonds = '".$fondsen[$a]['OptieBovenliggendFonds'].
															"' AND Datum >= '".$einddatum."' ORDER BY Datum ASC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$actuelefonds = $DB2->NextRecord();

			$clean['aandeelKoers'] = $actuelefonds['Koers'] ;

			$optieData[$portefeuille][] = $clean;

		  }
		}
		$portefeuilleData[$portefeuille] = $pdata;
		}

		$this->pdf->AddPage();

		$this->pdf->SetFont("Times","bu",10);


		$this->pdf->excelData[] = array("",
								"Portefeuille",
								"Client",
								"Naam",
								"Fonds",
								"Fonds koers",
								"Aantal",
								"Koers",
								"Waarde in Euro",'Risicoprofiel','Overige beperkingen');


		$this->pdf->SetFont("Times","",10);

		// nog een keer een loop over de portefeuilles!

		$tel = 0;

		while (list($portefeuille, $opties) = each($optieData))
		{
			$tel ++;
			for ($n=0; $n < count($opties); $n++)
			{
			// schrijf data !
			if ($n == 0)
			{
			$this->pdf->row(array(	$tel,
									$portefeuille,
									$portefeuilleData[$portefeuille]['Client'],
									$portefeuilleData[$portefeuille]['Naam'],
									$opties[$n]['fondsOmschrijving'],
									$this->formatGetal($opties[$n]['aandeelKoers'],2),
									$this->formatGetal($opties[$n]['totaalAantal'],0),
									$this->formatGetal($opties[$n]['actueleFonds'],2),
									$this->formatGetal($opties[$n]['actuelePortefeuilleWaardeEuro'],2)));
			}
			else
			{
			$this->pdf->row(array("","","","",
									$opties[$n]['fondsOmschrijving'],
									$this->formatGetal($opties[$n]['aandeelKoers'],2),
									$this->formatGetal($opties[$n]['totaalAantal'],0),
									$this->formatGetal($opties[$n]['actueleFonds'],2),
									$this->formatGetal($opties[$n]['actuelePortefeuilleWaardeEuro'],2)));

			}
			$this->pdf->excelData[] = array($tel,
									 $portefeuille,
									 $portefeuilleData[$portefeuille]['Client'],
									 $portefeuilleData[$portefeuille]['Naam'],
									 $opties[$n]['fondsOmschrijving'] ,
									 round($opties[$n]['aandeelKoers'],2),
									 round($opties[$n]['totaalAantal'],0),
									 round($opties[$n]['actueleFonds'],2),
									 round($opties[$n]['actuelePortefeuilleWaardeEuro'],2),
									 $portefeuilleData[$portefeuille]['Risicoklasse'],
									 $portefeuilleData[$portefeuille]['profielOverigeBeperkingen']);

			$this->pdf->mutatieData[]=array('portefeuille'=>$portefeuille,'optieData'=>$opties[$n]);
//			$totaalWaarde += $opties[$n]['actuelePortefeuilleWaardeEuro']	;
			}

		}
/*
		$this->pdf->SetFont("Times","b",10);
		$this->pdf->ln();
		$this->pdf->Line($this->pdf->marge + 240,$this->pdf->GetY(), $this->pdf->marge + 265,$this->pdf->GetY());

		// druk totaal af

		$this->pdf->row(array("","","","","","",'Totaal',$this->formatGetal($totaalWaarde,2)));

		$this->pdf->SetFont("Times","",10);
*/
		if($this->progressbar)
			$this->progressbar->hide();
	}

	function OutputCSV($filename, $type)
	{
		if($fp = fopen($filename,"w+"))
		{
			$excelData = generateCSV($this->pdf->excelData);
			fwrite($fp,$excelData);
			fclose($fp);
		}
		else
		{
			echo "Fout: kan niet schrijven naar ".$filename;
		}

	}

	function expitatieVerwerken()
	{
	  global $USR;
    $db=new DB();

    if ($db->QRecords("SELECT id FROM TijdelijkeRekeningmutaties WHERE  TijdelijkeRekeningmutaties.change_user = '$USR' ") > 0)
    {
   	echo "<br>
    <br>
    De tabel TijdelijkeRekeningmutaties is niet leeg voor gebruiker ($USR) (".$db->QRecords("SELECT id FROM TijdelijkeRekeningmutaties WHERE  TijdelijkeRekeningmutaties.change_user = '$USR' ").")<br>
    <br>
    de import is geannuleerd ";
	  exit;
    }

	  foreach ($this->pdf->mutatieData as $regel)
	  {
	    $query="SELECT * FROM Rekeningen WHERE Portefeuille = '".$regel['portefeuille']."' AND Memoriaal='1' AND Inactief = 0 ";
      $db->SQL($query);
	    $rekening=$db->lookupRecord();

      if($regel['optieData']['totaalAantal'] > 0)
      {
        $transactietype = 'V/S';
        $omschrijving='Verkoop '.$regel['optieData']['fonds'];
      }
      else
      {
        $transactietype = 'A/S';
        $omschrijving='Aankoop '.$regel['optieData']['fonds'];
      }
	    $query="INSERT INTO TijdelijkeRekeningmutaties
	            SET Rekening='".$rekening['Rekening']."',
	            Omschrijving='$omschrijving',
	            Boekdatum= '".jul2db($this->selectData['datumTm'])."',
	            Grootboekrekening='FONDS',
	            Valuta='".$regel['optieData']['valuta']."',
	            Valutakoers='".$regel['optieData']['actueleValuta']."',
	            Fonds='".$regel['optieData']['fonds']."',
	            Aantal='".$regel['optieData']['totaalAantal'] * -1 ."',
	            Fondskoers='0',
	            Debet='0',
	            Credit='0',
	            Bedrag='0',
	            Transactietype='$transactietype',
	            Memoriaalboeking='1',
	            add_date=NOW(),
	            add_user='$USR',
	            change_date=NOW(),
	            change_user='$USR'";
	    $db->SQL($query);
	    $db->Query();
	  }
	}
}
?>
<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.26 $

$Log: Geaggregeerdoverzicht.php,v $
Revision 1.26  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.25  2017/04/16 10:33:47  rvv
*** empty log message ***

Revision 1.24  2016/01/23 17:51:35  rvv
*** empty log message ***

Revision 1.23  2016/01/17 18:11:29  rvv
*** empty log message ***

Revision 1.22  2015/11/22 14:30:47  rvv
*** empty log message ***

Revision 1.21  2012/06/30 14:46:21  rvv
*** empty log message ***

Revision 1.20  2012/01/08 10:19:45  rvv
*** empty log message ***

Revision 1.19  2011/11/09 18:55:59  rvv
*** empty log message ***

Revision 1.18  2010/11/24 20:19:15  rvv
*** empty log message ***

Revision 1.17  2010/11/24 11:57:48  rvv
*** empty log message ***

Revision 1.16  2010/11/17 17:16:33  rvv
*** empty log message ***

Revision 1.15  2010/07/28 17:18:43  rvv
*** empty log message ***

Revision 1.14  2009/05/10 08:59:47  rvv
*** empty log message ***

Revision 1.13  2008/12/17 11:11:55  rvv
*** empty log message ***

Revision 1.12  2008/06/30 07:58:44  rvv
*** empty log message ***

Revision 1.11  2008/05/16 08:12:57  rvv
*** empty log message ***

Revision 1.10  2007/08/02 14:46:01  rvv
*** empty log message ***

Revision 1.9  2007/04/03 13:26:33  rvv
*** empty log message ***

Revision 1.8  2007/02/21 11:04:26  rvv
Client toevoeging

Revision 1.7  2006/11/03 11:24:04  rvv
Na user update

Revision 1.6  2006/10/31 11:59:39  rvv
Voor user update

Revision 1.5  2005/12/14 15:16:07  jwellner
no message

Revision 1.4  2005/11/07 10:29:17  jwellner
no message

Revision 1.3  2005/09/12 09:10:42  jwellner
diverse aanpassingen / bugfixes gemeld in e-mails theo

Revision 1.2  2005/09/09 11:31:46  jwellner
diverse aanpassingen zie e-mails Theo

Revision 1.1  2005/09/07 07:33:23  jwellner
no message


*/

include_once("rapportRekenClass.php");

class Geaggregeerdoverzicht
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function Geaggregeerdoverzicht( $selectData ) {

	  global $USR;
		$this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->orderby = "Clienten.Client";

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "geaggregeerd";
		$this->pdf->rapport_titel = "Geaggregeerd portefeuille overzicht.";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);

		$this->pdf->vandatum = $this->selectData['datumVan'];
		$this->pdf->tmdatum  = $this->selectData['datumTm'];


		$this->dbTable="CREATE TABLE `reportbuilder_$USR` (
`id` INT NOT NULL AUTO_INCREMENT ,
`Rapport` VARCHAR( 25 ) NOT NULL ,
`Fonds` VARCHAR( 25 ) NOT NULL ,
`FondsOmschrijving` VARCHAR( 50 ) NOT NULL ,
`totaalAantal` DOUBLE NOT NULL ,
`actueleFonds` DOUBLE NOT NULL ,
`actuelePortefeuilleWaardeInValuta` DOUBLE NOT NULL ,
`actuelePortefeuilleWaardeEuro` DOUBLE NOT NULL ,
`percentageVanTotaal` DOUBLE NOT NULL ,
`add_date` datetime ,
PRIMARY KEY ( `id` ),
KEY `Fonds` (`Fonds`)
) ";





	}

	function formatGetal($waarde, $dec,$zesDecimalenZonderNullen=false)
	{
	 	if ($zesDecimalenZonderNullen)
	  {
	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '000000' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];
	       if ($decimaal != '0' && !$newDec)
	       {
	         $newDec = $i;
	       }
	     }
	     return number_format($waarde,$newDec,",",".");
	   }
	  }
		return number_format($waarde,$dec,",",".");
	}

	function getVerdeling($fonds)
	{
	  $db=new DB();
	  $jaar = date("Y",$this->selectData['datumTm']);
	  if(!$this->bedrijven)
	  {
	    $query="SELECT
VermogensbeheerdersPerBedrijf.Bedrijf,
Portefeuilles.Portefeuille
FROM
VermogensbeheerdersPerBedrijf
Inner Join Portefeuilles ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
order by Bedrijf";
	   $db->SQL($query);
		 $db->Query();
  	 while($bedrijf = $db->NextRecord())
  	 {
  	   $this->bedrijven[$bedrijf['Bedrijf']]=0;
			 $this->portefeuilleBedijf[$bedrijf['Portefeuille']]=$bedrijf['Bedrijf'];
  	 }
  	 $this->bedrijven['geen']=0;
	  }
	  $aantal=$this->bedrijven;
		$query = "SELECT Rekeningmutaties.Fonds, SUM(Rekeningmutaties.Aantal) as aantal ,Portefeuilles.Portefeuille
		FROM Rekeningmutaties
		JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
		JOIN  Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
		WHERE ".$this->extraquery."
		  YEAR(Rekeningmutaties.Boekdatum) = '".$jaar."' AND Rekeningmutaties.Verwerkt = '1' AND
		  Rekeningmutaties.Boekdatum <= '".jul2db($this->selectData['datumTm'])."' AND Rekeningmutaties.Grootboekrekening = 'FONDS' AND Rekeningmutaties.Fonds='$fonds'
		GROUP BY Portefeuilles.Portefeuille";
	  $db->SQL($query);
	  $db->Query();
    while($fonds = $db->NextRecord())
  	{
  	  $bedrijf=$this->portefeuilleBedijf[$fonds['Portefeuille']];
  	  if($bedrijf=='')
  	  {
  	    $bedrijf='geen';
  	    $this->portefeuillesZonderBedrijf[]=$fonds['Portefeuille'];
  	  }
  	  $aantal[$bedrijf]+=$fonds['aantal'];
  	}
  	return $aantal;
  }

	function berekenWaarde()
	{
		$this->tijdelijkePortefeuille = mktime();

		$this->pdf->__appvar = $this->__appvar;

		$fondswaardenClean = array();
		$fondswaardenRente = array();
		$rekeningwaarden 	 = array();

		$jaar = date("Y",$this->selectData[datumTm]);

		$selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();
    $portefeuilleList=array_keys($portefeuilles);
		$extraquery=" Portefeuilles.Portefeuille IN('".implode("','",$portefeuilleList)."') AND ";
		$this->extraquery=$extraquery;

		$q = "SELECT ".
		" Rekeningmutaties.Fonds, ".
		" (SELECT  Beleggingssector FROM  BeleggingssectorPerFonds WHERE BeleggingssectorPerFonds.Fonds = Rekeningmutaties.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND Vanaf <=  '".jul2db($this->selectData['datumTm'])."' ORDER BY BeleggingssectorPerFonds.Vanaf  DESC LIMIT 1) as Beleggingssector,
	  	(SELECT  Beleggingscategorie FROM  BeleggingscategoriePerFonds WHERE BeleggingscategoriePerFonds.Fonds = Rekeningmutaties.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND Vanaf <= '".jul2db($this->selectData['datumTm'])."'  ORDER BY Vanaf  DESC LIMIT 1) as Beleggingscategorie ".
		" FROM (Rekeningmutaties, Rekeningen, Portefeuilles) ".
		" $join
	  	WHERE ".
		" Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		" Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".$extraquery.
		" YEAR(Rekeningmutaties.Boekdatum) = '".$jaar."' AND ".
		" Rekeningmutaties.Verwerkt = '1' AND ".
		" Rekeningmutaties.Boekdatum <= '".jul2db($this->selectData['datumTm'])."' AND ".
		" Rekeningmutaties.Grootboekrekening = 'FONDS' ".
		" GROUP BY Rekeningmutaties.Fonds ".
		" ORDER BY Rekeningmutaties.Fonds ";

		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();

		$records = $DB->records();

		while($fonds = $DB->NextRecord())
			$fondsen[] = $fonds;

		// build portefeuille array
		$pselectie = $this->selectData;

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / $records;
		}

		for($a=0; $a < count($fondsen); $a++)
		{
			// berekening van Fonds Waarden in een aparte functie gezet
			$fondswaarden[$fondsen[$a]['Fonds']] = fondsAantalOpdatum($pselectie, $fondsen[$a]['Fonds'], jul2db($this->selectData['datumTm']),$portefeuilleList);
			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
			}
		}

		// clean array
		for($a=0; $a <count($fondsen); $a++)
		{
			$fonds 	= $fondsen[$a];
			$data 	= $fondswaarden[$fonds['Fonds']];

			if(round($data['totaalAantal'],4) <> 0)
			{

				// bereken totalen met actuele koers
				$actuelePortefeuilleWaardeInValuta 	= ($data['fondsEenheid']  * $data['totaalAantal']) * $data['actueleFonds'];
				$actuelePortefeuilleWaardeEuro 			=  $data['actueleValuta'] * $actuelePortefeuilleWaardeInValuta;

				// maak nieuwe schone array
				$clean = $data;
				$clean['beginPortefeuilleWaardeInValuta'] 	= $beginPortefeuilleWaardeInValuta;
				$clean['beginPortefeuilleWaardeEuro'] 			= $beginPortefeuilleWaardeEuro;
				$clean['actuelePortefeuilleWaardeInValuta'] = $actuelePortefeuilleWaardeInValuta;
				$clean['actuelePortefeuilleWaardeEuro'] 		= $actuelePortefeuilleWaardeEuro;
				$clean['fonds'] 														= $fonds['Fonds'];
				$clean['beleggingssector'] 									= $fonds['Beleggingssector'];
				$clean['beleggingscategorie'] 							= $fonds['Beleggingscategorie'];


				$fondswaardenClean[] = $clean;
			}
		}
		// bereken de rente
		$t = count($fondswaardenClean);
		for($a=0; $a <count($fondswaardenClean); $a++)
		{
			if($fondswaardenClean[$a]['renteBerekenen'] == 1)
			{
				$rentebedrag = renteOverPeriode($fondswaardenClean[$a], jul2db($this->selectData['datumTm']), $min1dag);
				$fondswaardenRente[$t] = $fondswaardenClean[$a];
				$fondswaardenRente[$t]['type'] = "rente";
				$fondswaardenRente[$t]['actuelePortefeuilleWaardeInValuta'] = $rentebedrag;
				$fondswaardenRente[$t]['actuelePortefeuilleWaardeEuro'] = $fondswaardenClean[$a]['actueleValuta'] * $rentebedrag;
				$t++;
			}
		}


		// merge rente array met fondsen array
		$fondswaardenClean = array_merge($fondswaardenClean, $fondswaardenRente);
		// haal actuele stand rekening op.
    $_beginJaar = substr(jul2db($this->selectData['datumTm']),0,4)."-01-01";

    $query = "SELECT Rekeningen.Valuta, SUM(Rekeningmutaties.Bedrag) as totaal ".
  						" FROM Rekeningmutaties, Rekeningen, Portefeuilles ".
  						$join.
  					  " WHERE ".
  						" Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
							" Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".$extraquery.
							" Rekeningen.Memoriaal < 1 AND ".
  						" Rekeningmutaties.boekdatum >= '".$_beginJaar."' AND ".
  						" Rekeningmutaties.boekdatum <= '".jul2db($this->selectData['datumTm'])."' ".
              " GROUP BY Rekeningen.Valuta ".
              " ORDER BY Rekeningen.Valuta";

		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();

		$t = count($fondswaardenClean);
		while($data = $DB1->NextRecord())
		{
			$q = "SELECT Koers,Datum FROM Valutakoersen WHERE Valuta = '".$data['Valuta']."' AND Datum <= '".jul2db($this->selectData['datumTm'])."' ORDER BY Datum DESC LIMIT 1";

			$DB2 = new DB();
			$DB2->SQL($q);
			$DB2->Query();
			$actuelevaluta = $DB2->NextRecord();

			$rekeningwaarden[$t]['type'] 							= "rekening";
			$rekeningwaarden[$t]['fondsOmschrijving'] = $data['Valuta'];
			$rekeningwaarden[$t]['rekening'] 					= "";
			$rekeningwaarden[$t]['valuta'] 						= $data['Valuta'];
			$rekeningwaarden[$t]['actueleValuta'] 		= $actuelevaluta['Koers'];

			// get actuele valuta
			$rekeningwaarden[$t]['actuelePortefeuilleWaardeInValuta'] = $data['totaal'];
			$rekeningwaarden[$t]['actuelePortefeuilleWaardeEuro'] 		= $rekeningwaarden[$t]['actueleValuta'] * $data['totaal'];
			$t++;
		}
		// merge rekeningen array
		$fondswaardenClean = array_merge($fondswaardenClean, $rekeningwaarden);
		return $fondswaardenClean;
	}

	function printKop($title)
	{
		$this->pdf->SetFont("Times", "bi", 10);
		$this->pdf->Cell(100 , 4 , $title , 0, 1, "L");
		$this->pdf->SetFont("Times", "", 10);

		$this->pdf->excelData[] = array($title);
	}

	function writeRapport()
	{
		global $__appvar;
		$fondswaardenClean = $this->berekenWaarde();

		$einddatum = substr(jul2db($this->selectData['datumTm']),0,10);

		//print_r($fondswaardenClean);
		vulTijdelijkeTabel($fondswaardenClean, $this->tijdelijkePortefeuille,$einddatum);

		$this->pdf->AddPage();
		$this->pdf->SetFont("Times","bu",10);

		//$this->pdf->Cell(185 , 4 , "" , 0, 0, "L");
		//$this->pdf->Cell(65 , 4 , "Geaggregeerd Portefeuille Overzicht" , 0,1, "L");

	// haal totaalwaarde op om % te berekenen
		$DB = new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$einddatum."' AND ".
						 " portefeuille = '".$this->tijdelijkePortefeuille."' ";
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		$actueleWaardePortefeuille = 0;

		$query = "SELECT Beleggingscategorien.Omschrijving, ".
		" TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) AS subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel, ".
		" Valutas.Omschrijving AS ValutaOmschrijving".
		" FROM TijdelijkeRapportage ".
		" LEFT JOIN Valutas ON (TijdelijkeRapportage.valuta = Valutas.Valuta)  ".
		" LEFT JOIN Beleggingscategorien ON (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie) ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->tijdelijkePortefeuille."' AND ".
		" TijdelijkeRapportage.type = 'fondsen' AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$einddatum."'"
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta ".
		" ORDER BY Beleggingscategorien.Afdrukvolgorde asc, Valutas.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		// CSV header
		$this->pdf->excelData[] = array("",
														 "Fondsomschrijving",
														 "Aantal",
														 "Fondskoers",
														 "Fondstotaal",
														 "Fondstotaal EUR",
														 "Perc. %");
		if($__appvar['bedrijf']=='HOME')
		{
		  $this->getVerdeling('');
		  foreach ($this->bedrijven as $bedrijf=>$aantal)
		    $this->pdf->excelData[0][]=$bedrijf;
		}

		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			// print totaal op hele categorie.
			if($lastCategorie <> $categorien['Omschrijving'] && !empty($lastCategorie) )
			{
				$this->pdf->SetFont("Times", "b", 10);

				$percentageVanTotaal = $totaalactueel / ($totaalWaarde/100);

				$this->pdf->Line($this->pdf->marge + 235 ,$this->pdf->GetY(), $this->pdf->marge + 260,$this->pdf->GetY());
				$this->pdf->Line($this->pdf->marge + 262 ,$this->pdf->GetY(), $this->pdf->marge + 280,$this->pdf->GetY());

				$this->pdf->Cell(10 , 4 , "" , 0, 0, "R");
				$this->pdf->Cell(160, 4 , "" , 0, 0, "L");
				$this->pdf->Cell(20 , 4 , "" , 0, 0, "R");
				$this->pdf->Cell(20 , 4 , "Subtotaal ".$lastCategorie , 0, 0, "R");
				$this->pdf->Cell(25 , 4 , "" , 0, 0, "R");
				$this->pdf->Cell(25 , 4 , $this->formatGetal($totaalactueel,2) , 0, 0, "R");
				$this->pdf->Cell(20 , 4 , $this->formatGetal($percentageVanTotaal,2)." %", 0, 1, "R");

				$this->pdf->ln();
				$totaalactueel = 0;
			}

			$this->pdf->SetFont("Times", "", 10);
			if($lastCategorie <> $categorien['Omschrijving'])
			{
				$this->printKop($categorien['Omschrijving']);
			}
			// subkop (valuta)
			$this->pdf->Cell(100 , 4 , "Waarden ".$categorien['valuta'] , 0, 1, "L");
			$this->pdf->excelData[] = array("Waarden ".$categorien['valuta']);

			// print detail (select from tijdelijkeRapportage)

			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.fonds, ".
			" TijdelijkeRapportage.actueleValuta, ".
			" TijdelijkeRapportage.totaalAantal, ".
			" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.actueleFonds, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.beleggingscategorie, ".
			" TijdelijkeRapportage.valuta, ".
			" TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->tijdelijkePortefeuille."' AND ".
			" TijdelijkeRapportage.beleggingscategorie =  '".$categorien['beleggingscategorie']."' AND ".
			" TijdelijkeRapportage.valuta =  '".$categorien['valuta']."' AND ".
			" TijdelijkeRapportage.type =  'fondsen' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$einddatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";

			debugSpecial($subquery,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query();
			while($subdata = $DB2->NextRecord())
			{

				$percentageVanTotaal = $subdata['actuelePortefeuilleWaardeEuro'] / ($totaalWaarde / 100);

				$this->pdf->Cell(10 , 4 , "" , 0, 0, "R");
				$this->pdf->Cell(160, 4 , $subdata['fondsOmschrijving'] , 0, 0, "L");
				$this->pdf->Cell(20 , 4 , $this->formatGetal($subdata['totaalAantal'],0,true) , 0, 0, "R");
				$this->pdf->Cell(20 , 4 , $this->formatGetal($subdata['actueleFonds'],2) , 0, 0, "R");
				$this->pdf->Cell(25 , 4 , $this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'],2) , 0, 0, "R");
				$this->pdf->Cell(25 , 4 , $this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],2) , 0, 0, "R");
				$this->pdf->Cell(20 , 4 , $this->formatGetal($percentageVanTotaal,2)." %", 0, 1, "R");


				$this->pdf->excelData[] = array("",
																 $subdata['fondsOmschrijving'],
																 round($subdata['totaalAantal'],6),
																 round($subdata['actueleFonds'],2),
																 round($subdata['actuelePortefeuilleWaardeInValuta'],2),
																 round($subdata['actuelePortefeuilleWaardeEuro'],2),
																 round($percentageVanTotaal,2));

					  $this->dbWaarden[]=array(
				        'Rapport'=>'Geaggregeerdoverzicht',
			  				'Fonds'=>$subdata['fonds'],
			  				'FondsOmschrijving'=>$subdata['fondsOmschrijving'],
			  				'totaalAantal'=> round($subdata['totaalAantal'],6),
								'actueleFonds'=> round($subdata['actueleFonds'],2),
				        'actuelePortefeuilleWaardeInValuta'=>round($subdata['actuelePortefeuilleWaardeInValuta'],2),
				        'actuelePortefeuilleWaardeEuro'=>round($subdata['actuelePortefeuilleWaardeEuro'],2),
				        'percentageVanTotaal'=>round($percentageVanTotaal,2));


				if($__appvar['bedrijf']=='HOME')
		    {
				  $aantal=$this->getVerdeling($subdata['fonds']);
					foreach ($aantal as $bedrijf=>$aantal)
		        $this->pdf->excelData[count($this->pdf->excelData)-1][]=$aantal;
			  }
			}
			// subtotaal


			$this->pdf->Line($this->pdf->marge + 235 ,$this->pdf->GetY(), $this->pdf->marge + 260,$this->pdf->GetY());
			$this->pdf->Line($this->pdf->marge + 262 ,$this->pdf->GetY(), $this->pdf->marge + 280,$this->pdf->GetY());

			$percentageVanTotaal = $categorien[subtotaalactueel] / ($totaalWaarde/100);

			$this->pdf->Cell(10 , 4 , "" , 0, 0, "R");
			$this->pdf->Cell(160, 4 , "" , 0, 0, "L");
			$this->pdf->Cell(20 , 4 , "" , 0, 0, "R");
			$this->pdf->Cell(20 , 4 , "" , 0, 0, "R");
			$this->pdf->Cell(25 , 4 , "" , 0, 0, "R");
			$this->pdf->Cell(25 , 4 , $this->formatGetal($categorien['subtotaalactueel'],2) , 0, 0, "R");
			$this->pdf->Cell(20 , 4 , $this->formatGetal($percentageVanTotaal,2)." %", 0, 1, "R");

			// totaal op categorie tellen
			$totaalactueel += $categorien[subtotaalactueel];

			$lastCategorie = $categorien[Omschrijving];
		}

		// totaal

		$this->pdf->SetFont("Times", "b", 10);
		$percentageVanTotaal = $totaalactueel / ($totaalWaarde/100);
			//			$title = "Subtotaal ".$lastCategorie;

		$this->pdf->Line($this->pdf->marge + 235 ,$this->pdf->GetY(), $this->pdf->marge + 260,$this->pdf->GetY());
		$this->pdf->Line($this->pdf->marge + 262 ,$this->pdf->GetY(), $this->pdf->marge + 280,$this->pdf->GetY());

		$this->pdf->Cell(10 , 4 , "" , 0, 0, "R");
		$this->pdf->Cell(160, 4 , "" , 0, 0, "L");
		$this->pdf->Cell(20 , 4 , "" , 0, 0, "R");
		$this->pdf->Cell(20 , 4 , "Subtotaal ".$lastCategorie , 0, 0, "R");
		$this->pdf->Cell(25 , 4 , "" , 0, 0, "R");
		$this->pdf->Cell(25 , 4 , $this->formatGetal($totaalactueel,2) , 0, 0, "R");
		$this->pdf->Cell(20 , 4 , $this->formatGetal($percentageVanTotaal,2)." %", 0, 1, "R");

		$this->pdf->ln();
		$totaalactueel = 0;
		$this->pdf->SetFont("Times", "", 10);

		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, TijdelijkeRapportage.beleggingscategorie, SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) subtotaalbegin, SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) subtotaalactueel FROM ".
		" TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta) ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->tijdelijkePortefeuille."' AND TijdelijkeRapportage.RenteBerekenen = '1' ".
		" AND TijdelijkeRapportage.rapportageDatum = '".$einddatum."' "
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.valuta ".
		" ORDER BY Valutas.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		$this->printKop("Opgelopen Rente",$this->pdf->rapport_kop3_fontstyle);

		$totaalRenteInValuta = 0 ;

		while($categorien = $DB->NextRecord())
		{
			$subtotaalRenteInValuta = 0;
			$this->printKop("Waarden ".$categorien[valuta],$this->pdf->rapport_kop4_fontstyle);

			// print detail (select from tijdelijkeRapportage)

			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->tijdelijkePortefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rente'  AND ".
			" TijdelijkeRapportage.valuta =  '".$categorien['valuta']."'".
			" AND TijdelijkeRapportage.rapportageDatum = '".$einddatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
			debugSpecial($subquery,__FILE__,__LINE__);

			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query();
			while($subdata = $DB2->NextRecord())
			{
				$subtotaalRenteInValuta += $subdata['actuelePortefeuilleWaardeEuro'];
				// print fondsomschrijving appart ivm met apparte fontkleur
				$percentageVanTotaal = $subdata['actuelePortefeuilleWaardeEuro'] / ($totaalWaarde/100);

				$this->pdf->Cell(10 , 4 , "" , 0, 0, "R");
				$this->pdf->Cell(160, 4 , $subdata['fondsOmschrijving'] , 0, 0, "L");
				$this->pdf->Cell(20 , 4 , "" , 0, 0, "R");
				$this->pdf->Cell(20 , 4 , "" , 0, 0, "R");
				$this->pdf->Cell(25 , 4 , $this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'],2) , 0, 0, "R");
				$this->pdf->Cell(25 , 4 , $this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],2) , 0, 0, "R");
				$this->pdf->Cell(20 , 4 , $this->formatGetal($percentageVanTotaal,2)." %", 0, 1, "R");

				$this->pdf->excelData[] = array("",
																 $subdata['fondsOmschrijving'],
																 "",
																 "",
																 round($subdata['actuelePortefeuilleWaardeInValuta'],2),
																 round($subdata['actuelePortefeuilleWaardeEuro'],2),
																 round($percentageVanTotaal,2));


			}

			// print subtotaal
			$percentageVanTotaal = $subtotaalRenteInValuta / ($totaalWaarde/100);


			$this->pdf->Line($this->pdf->marge + 235 ,$this->pdf->GetY(), $this->pdf->marge + 260,$this->pdf->GetY());
			$this->pdf->Line($this->pdf->marge + 262 ,$this->pdf->GetY(), $this->pdf->marge + 280,$this->pdf->GetY());

			$this->pdf->Cell(10 , 4 , "" , 0, 0, "R");
			$this->pdf->Cell(160, 4 , "" , 0, 0, "L");
			$this->pdf->Cell(20 , 4 , "" , 0, 0, "R");
			$this->pdf->Cell(20 , 4 , "" , 0, 0, "R");
			$this->pdf->Cell(25 , 4 , "" , 0, 0, "R");
			$this->pdf->Cell(25 , 4 , $this->formatGetal($subtotaalRenteInValuta,2) , 0, 0, "R");
			$this->pdf->Cell(20 , 4 , $this->formatGetal($percentageVanTotaal,2)." %", 0, 1, "R");

			$this->pdf->ln();

			$totaalRenteInValuta += $subtotaalRenteInValuta;
		}

		// totaal op rente

		$this->pdf->SetFont("Times", "b", 10);
		$percentageVanTotaal = $totaalRenteInValuta / ($totaalWaarde/100);

		$this->pdf->Line($this->pdf->marge + 235 ,$this->pdf->GetY(), $this->pdf->marge + 260,$this->pdf->GetY());
		$this->pdf->Line($this->pdf->marge + 262 ,$this->pdf->GetY(), $this->pdf->marge + 280,$this->pdf->GetY());

		$this->pdf->Cell(10 , 4 , "" , 0, 0, "R");
		$this->pdf->Cell(160, 4 , "" , 0, 0, "L");
		$this->pdf->Cell(20 , 4 , "" , 0, 0, "R");
		$this->pdf->Cell(20 , 4 , "Subtotaal Opgelopen rente " , 0, 0, "R");
		$this->pdf->Cell(25 , 4 , "" , 0, 0, "R");
		$this->pdf->Cell(25 , 4 , $this->formatGetal($totaalRenteInValuta,2) , 0, 0, "R");
		$this->pdf->Cell(20 , 4 , $this->formatGetal($percentageVanTotaal,2)." %", 0, 1, "R");

		$this->pdf->ln();


		// Liquiditeiten
		$this->printKop("Liquiditeiten",$this->pdf->rapport_kop3_fontstyle);

		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving,TijdelijkeRapportage.Fonds,  ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.rekening , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->tijdelijkePortefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekening'  ".
			" AND TijdelijkeRapportage.rapportageDatum = '".$einddatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.valuta asc";

		debugSpecial($query,__FILE__,__LINE__);
		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();

		$totaalLiquiditeitenInValuta = 0;

		while($data = $DB1->NextRecord())
		{

			$totaalLiquiditeitenEuro += $data['actuelePortefeuilleWaardeEuro'];
			$percentageVanTotaal = $data['actuelePortefeuilleWaardeEuro'] / ($totaalWaarde/100);

			$this->pdf->Cell(10 , 4 , "" , 0, 0, "R");
			$this->pdf->Cell(160, 4 , $data[fondsOmschrijving] , 0, 0, "L");
			$this->pdf->Cell(20 , 4 , "", 0, 0, "R");
			$this->pdf->Cell(20 , 4 , "", 0, 0, "R");
			$this->pdf->Cell(25 , 4 , $this->formatGetal($data['actuelePortefeuilleWaardeInValuta'],2) , 0, 0, "R");
			$this->pdf->Cell(25 , 4 , $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],2) , 0, 0, "R");
			$this->pdf->Cell(20 , 4 , $this->formatGetal($percentageVanTotaal,2)." %", 0, 1, "R");

			$this->pdf->excelData[] = array("",
															 $data['fondsOmschrijving'],
															 "",
															 "",
															 round($data['actuelePortefeuilleWaardeInValuta'],2),
															 round($data['actuelePortefeuilleWaardeEuro'],2),
															 round($percentageVanTotaal,2));

		  $this->dbWaarden[]=array(
				        'Rapport'=>'Geaggregeerdoverzicht',
			  				'Fonds'=>$data['rekening'],
			  				'FondsOmschrijving'=>$data['Fonds'],
			  				'totaalAantal'=> round(0,0),
								'actueleFonds'=> round(0,2),
				        'actuelePortefeuilleWaardeInValuta'=>round($data['actuelePortefeuilleWaardeInValuta'],2),
				        'actuelePortefeuilleWaardeEuro'=>round($data['actuelePortefeuilleWaardeEuro'],2),
				        'percentageVanTotaal'=>round($percentageVanTotaal,2));


		}
		// totaal liquiditeiten

		$this->pdf->SetFont("Times", "b", 10);
		$percentageVanTotaal = $totaalLiquiditeitenEuro / ($totaalWaarde/100);

		$this->pdf->Line($this->pdf->marge + 235 ,$this->pdf->GetY(), $this->pdf->marge + 260,$this->pdf->GetY());
		$this->pdf->Line($this->pdf->marge + 262 ,$this->pdf->GetY(), $this->pdf->marge + 280,$this->pdf->GetY());

		$this->pdf->Cell(10 , 4 , "" , 0, 0, "R");
		$this->pdf->Cell(160, 4 , "" , 0, 0, "L");
		$this->pdf->Cell(20 , 4 , "" , 0, 0, "R");
		$this->pdf->Cell(20 , 4 , "Subtotaal Liquiditeiten " , 0, 0, "R");
		$this->pdf->Cell(25 , 4 , "" , 0, 0, "R");
		$this->pdf->Cell(25 , 4 , $this->formatGetal($totaalLiquiditeitenEuro,2) , 0, 0, "R");
		$this->pdf->Cell(20 , 4 , $this->formatGetal($percentageVanTotaal,2)." %", 0, 1, "R");

		$this->pdf->ln();

		// check op totaalwaarde!

		$this->pdf->SetFont("Times", "b", 10);

		$this->pdf->Line($this->pdf->marge + 235 ,$this->pdf->GetY(), $this->pdf->marge + 260,$this->pdf->GetY());

		$this->pdf->Cell(10 , 4 , "" , 0, 0, "R");
		$this->pdf->Cell(160, 4 , "" , 0, 0, "L");
		$this->pdf->Cell(20 , 4 , "" , 0, 0, "R");
		$this->pdf->Cell(20 , 4 , "" , 0, 0, "R");
		$this->pdf->Cell(25 , 4 , "" , 0, 0, "R");
		$this->pdf->Cell(25 , 4 , $this->formatGetal($totaalWaarde,2) , 0, 0, "R");
		$this->pdf->Cell(20 , 4 , "", 0, 1, "R");

		$this->pdf->ln();

		if($this->progressbar)
			$this->progressbar->hide();

		verwijderTijdelijkeTabel($this->tijdelijkePortefeuille,$einddatum);
	}


	function getCVS()
	{
	  return $this->pdf->excelData;
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

	function OutputDatabase()
	{
	  global $USR;
	  $db=new DB();
	  $table="reportbuilder_$USR";
	  $query="SHOW TABLES like '$table'";
	  if($db->QRecords($query) > 0)
	  {
	    $db->SQL("DROP table $table");
	    $db->Query();
	  }
    if($this->dbTable)
    {
      $db->SQL($this->dbTable);
	    $db->Query();
	    $query="show variables like 'character_set_database'";
      $db->SQL($query);
      $db->Query();
      $charset=$db->lookupRecord();
      $charset=$charset['Value'];
      $query="ALTER TABLE `$table` CONVERT TO CHARACTER SET $charset";
      $db->SQL($query);
      $db->Query();
    }
    if(is_array($this->dbWaarden))
    {
      foreach ($this->dbWaarden as $rege=>$waarden)
      {
        $query="INSERT INTO $table SET add_date=now() ";

        foreach ($waarden as $key=>$value)
        {
          $query.=",$key='".addslashes($value)."' ";
        }

        $db->SQL($query);
	      $db->Query();
      }
    }
	}
}
?>
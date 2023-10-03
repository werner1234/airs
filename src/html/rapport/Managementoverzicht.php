<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/11/16 17:36:34 $
File Versie					: $Revision: 1.43 $

$Log: Managementoverzicht.php,v $
Revision 1.43  2019/11/16 17:36:34  rvv
*** empty log message ***

Revision 1.42  2017/12/06 16:48:06  rvv
*** empty log message ***

Revision 1.41  2017/03/29 15:56:14  rvv
*** empty log message ***

Revision 1.40  2016/01/31 09:52:08  rvv
*** empty log message ***

Revision 1.39  2015/11/18 17:06:10  rvv
*** empty log message ***

Revision 1.38  2015/11/14 13:25:54  rvv
*** empty log message ***

Revision 1.37  2014/12/21 10:32:26  rvv
*** empty log message ***

Revision 1.36  2014/04/05 15:33:11  rvv
*** empty log message ***

Revision 1.35  2013/08/28 16:02:00  rvv
*** empty log message ***

Revision 1.34  2013/08/07 17:18:57  rvv
*** empty log message ***

Revision 1.33  2012/08/05 10:46:20  rvv
*** empty log message ***

Revision 1.32  2012/07/14 13:19:37  rvv
*** empty log message ***

Revision 1.31  2012/07/11 15:49:25  rvv
*** empty log message ***

Revision 1.30  2012/06/23 15:19:58  rvv
*** empty log message ***

Revision 1.29  2012/04/08 08:11:42  rvv
*** empty log message ***

Revision 1.28  2011/12/24 16:34:55  rvv
*** empty log message ***

Revision 1.27  2011/12/11 10:58:18  rvv
*** empty log message ***

Revision 1.26  2011/09/14 09:26:56  rvv
*** empty log message ***

Revision 1.25  2010/10/31 15:42:33  rvv
*** empty log message ***

Revision 1.24  2010/10/17 09:22:15  rvv
Gebruik van rapportagevaluta voor perf berekening.

Revision 1.23  2009/06/07 10:27:29  rvv
*** empty log message ***

Revision 1.22  2009/01/20 17:44:08  rvv
*** empty log message ***

Revision 1.21  2007/08/02 14:46:01  rvv
*** empty log message ***

Revision 1.20  2007/04/20 12:21:16  rvv
*** empty log message ***

Revision 1.19  2007/04/03 13:26:33  rvv
*** empty log message ***

Revision 1.18  2007/02/21 11:04:26  rvv
Client toevoeging

Revision 1.17  2006/11/03 11:24:04  rvv
Na user update

Revision 1.16  2006/10/31 11:59:40  rvv
Voor user update

Revision 1.15  2006/09/20 14:24:21  rvv
vergelijking AEX uitgezet

Revision 1.14  2006/06/09 13:50:38  jwellner
*** empty log message ***

Revision 1.13  2006/02/03 08:00:27  jwellner
diverse bugfixen

Revision 1.12  2006/01/13 15:46:51  jwellner
diverse aanpassingen

Revision 1.11  2005/12/09 13:28:51  jwellner
bugfix managementoverzicht

Revision 1.10  2005/12/08 13:55:21  jwellner
Modelcontrole rapport

Revision 1.9  2005/11/07 10:29:17  jwellner
no message

Revision 1.8  2005/10/14 16:17:56  jwellner
no message

Revision 1.7  2005/10/12 15:12:31  jwellner
fix in mysqlObject , change dat vullen bij nieuw record!

Revision 1.6  2005/09/12 09:10:42  jwellner
diverse aanpassingen / bugfixes gemeld in e-mails theo

Revision 1.5  2005/08/30 15:15:41  jwellner
no message

Revision 1.4  2005/08/26 07:16:27  jwellner
met snelle portefeuille waarde berekening

Revision 1.3  2005/08/24 10:42:55  jwellner
no message

Revision 1.2  2005/08/05 09:44:23  jwellner
no message

Revision 1.1  2005/08/03 14:15:44  jwellner
- FrontOffice aanpassingen
- BackOffice toegevoegd
- Facturatie Bugfix.
- Managementoverzicht


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");

class Managementoverzicht
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function Managementoverzicht(  $selectData )
	{
    global $USR;
		$this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "managementoverzicht";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;
		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);
		$this->pdf->vandatum = $this->selectData['datumVan'];
		$this->pdf->tmdatum = $this->selectData['datumTm'];


		if($this->selectData['manExtraVelden'] == 1)
		  $this->extraVeldenTonen=true;
		else
		  $this->extraVeldenTonen=false;
    
    if($this->selectData['manStdev'] == 1)
      $this->stdevTonen=true;
    else
      $this->stdevTonen=false;

    $this->orderByVelden=array('Vermogensbeheerder','Accountmanager','Risicoklasse','SoortOvereenkomst');
    $this->lagen=array();
    foreach($this->orderByVelden as $veld)
    {
      if($this->selectData['orderby'.$veld] == 1 )
        $this->lagen[]=$veld;
    }
    
    if($this->selectData['orderbyVermogensbeheerder'] == 1 || $this->selectData['orderbyAccountmanager'] == 1 ||
       $this->selectData['orderbyRisicoklasse'] == 1 || $this->selectData['orderbySoortOvereenkomst'] == 1)
    {
      $this->orderby='';
      foreach($this->orderByVelden as $veld)
      {
        if($this->selectData['orderby'.$veld] == 1)
        {
          if($this->orderby != '')
            $this->orderby.=",";
          $this->orderby  .= " Portefeuilles.".$veld;
        }
      }
    }   
		else
		{
			$this->orderby  = " Clienten.Client ";
		}

		$query="SELECT Grootboekrekeningen.Grootboekrekening FROM Grootboekrekeningen";
		$db=new DB();
		$db->SQL($query);
		$db->Query();
		while($data=$db->nextRecord())
		  $grootboeken[]=$data['Grootboekrekening'];

		$grootboekDb="`".implode("` DOUBLE NOT NULL,\n`",$grootboeken)."` DOUBLE NOT NULL,";
  
$this->dbTable="CREATE TABLE `reportbuilder_$USR` (
`id` INT NOT NULL AUTO_INCREMENT ,
`Rapport` VARCHAR( 20 ) NOT NULL ,
`Portefeuille` VARCHAR( 24 ) NOT NULL ,
`Vermogensbeheerder` VARCHAR( 10 ) NOT NULL ,
`Client` VARCHAR( 16 ) NOT NULL ,
`Naam` VARCHAR( 50 ) NOT NULL ,
`Naam1` VARCHAR( 50 ) NOT NULL ,
`beginvermogen` DOUBLE NOT NULL ,
`totaalvermogen` DOUBLE NOT NULL ,
`stortingen` DOUBLE NOT NULL ,
`onttrekkingen` DOUBLE NOT NULL ,
`inprocenttotaal` DOUBLE NOT NULL ,
`performance` DOUBLE NOT NULL ,
`resultaat` DOUBLE NOT NULL ,
`AFMstd` DOUBLE NOT NULL ,
`werkelijkeStdev` DOUBLE NOT NULL ,
$grootboekDb
`liquiditeiten` DOUBLE NOT NULL ,
`add_date` datetime ,
PRIMARY KEY ( `id` ),
KEY `Portefeuille` (`Portefeuille`)
)";



	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}
	
	function tekstVerkorten($text,$width)
  {
    $omschrijvingWidth = $this->pdf->GetStringWidth($text);
    $cellWidth = $width - 2;
    $omschrijving = $text;
    if ($omschrijvingWidth > $cellWidth)
    {
      $dotWidth = $this->pdf->GetStringWidth('...');
      $chars = strlen($text);
      $newOmschrijving=$text;
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
    return $omschrijving;
  }

	function writeRapport()
	{
	global $__appvar;
		$this->pdf->__appvar = $this->__appvar;

    $selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie(false);
    $this->afmTotalen=array();
    $startTijd=time();
    logIt("Management overzicht start");
		if($records <= 0)		{
			echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
			$this->progressbar->hide();
			exit;
		}

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / $records /2;
		}

		$rapportageDatum['a'] = jul2sql($this->selectData['datumVan']);
		$rapportageDatum['b'] = jul2sql($this->selectData['datumTm']);
		// vul eerst de tijdelijketabel

		foreach($portefeuilles as $pdata)
		{
      foreach($this->orderByVelden as $veld)
      {
        if(trim($pdata[$veld])=='')
          $pdata[$veld]='leeg';
      }
      
      //'Vermogensbeheerder','Accountmanager','Risicoklasse','SoortOvereenkomst'
      
			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
				logScherm("Portefeuille: ".$pdata['Portefeuille']." (Vullen tijdelijke rapportage)");
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

			$fondswaarden['a'] =  berekenPortefeuilleWaarde($portefeuille,  $startdatum, $startjaar,'EUR',$startdatum);
			$fondswaarden['b'] =  berekenPortefeuilleWaarde($portefeuille,  $einddatum , false     ,'EUR',$startdatum);

			vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,$startdatum);
			vulTijdelijkeTabel($fondswaarden['b'] ,$portefeuille,$einddatum);

			// tel totaal op!
			$DB2 = new DB();
			$queryTotaal = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
							 "FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$einddatum."' AND ".
							 " portefeuille = '".$portefeuille."' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($queryTotaal,__FILE__,__LINE__);

			$DB2->SQL($queryTotaal);
			$DB2->Query();
			$totaalWaarde = $DB2->nextRecord();
			$this->grandtotaalWaarde += $totaalWaarde['totaal'];
      
		}
		$this->pdf->AddPage();

		$this->pdf->SetFont("Times","bu",10);

		$this->pdf->Cell(185 , 4 , "" , 0, 0, "L");
		$this->pdf->Cell(65 , 4 , "Totaal vermogen in EURO" , 0,1, "L");

		$this->pdf->SetFont("Times","b",10);

		$this->pdf->Cell(10 , 4 , "" , 0, 0, "R");
		$this->pdf->Cell(45 , 4 , "Client" , 0, 0, "L");
		$this->pdf->Cell(90 , 4 , "Naam" , 0, 0, "L");
		$this->pdf->Cell(20 , 4 , "Portefeuille" , 0, 0, "L");
		$this->pdf->Cell(20 , 4 , "Depotbank" , 0, 0, "L");
		$this->pdf->Cell(25 , 4 , "Absoluut" , 0, 0, "R");
		$this->pdf->Cell(20 , 4 , "in %", 0, 0, "R");
		$this->pdf->Cell(20 , 4 , "Performance", 0, 0, "R");
		$this->pdf->Cell(20 , 4 , "AFM-#", 0, 0, "R");
    if($this->stdevTonen==true)
    {
      $this->pdf->Cell(14 , 4 , "stdev", 0, 0, "R");
    }
    $this->pdf->ln();
		if($this->extraVeldenTonen == true)
		{
      $DB2->SQL("SELECT Grootboekrekening,Omschrijving FROM Grootboekrekeningen WHERE (Grootboekrekeningen.Kosten=1 OR Grootboekrekeningen.Opbrengst=1)");
      $DB2->Query();
      while($gb=$DB2->nextRecord())
      {
        $grootboeken[$gb['Grootboekrekening']]=$gb['Omschrijving'];
      }

		   $row= array("",
		  												"Client",
			  											"Naam",
															"Portefeuille",
                              "Accountmanager",
                              'Soort overeenkomst',
                              'Risicoprofiel',
															"Depotbank",
															"Beginvermogen",
															"Stortingen",
															"Onttrekkingen",
															"Resultaat",
															"Koersongerealiseerd",
															"Koersgerealiseerd",
															"Rente",
															"koersResulaatValutas");
				foreach ($grootboeken as $grootboek)
				  $row[]=$grootboek;

       $row[]="Absoluut";
       $row[]="in %";
       $row[]="Performance";

      $header=$row;


/*
																		  $this->pdf->excelData[] = array($this->tel,
																$pdata['Client'],
																$pdata['Naam'],
																$pdata['Portefeuille'],
																$pdata['Depotbank'],
																round($this->extraVelden['beginvermogen'],2),
																round($this->extraVelden['stortingen'],2),
																round($this->extraVelden['onttrekkingen'],2),
																round($this->extraVelden['resultaat'],2),
																round($this->extraVelden['koersongerealiseerd'],2),
																round($this->extraVelden['koersgerealiseerd'],2),
																round($this->extraVelden['rente'],2),
																round($this->extraVelden['VKSTO'],2),
																round($this->extraVelden['DIV'],2),
																round($this->extraVelden['RENTE'],2),
																round($this->extraVelden['RENME'],2),
																round($this->extraVelden['RENOB'],2),
																round($this->extraVelden['KOST'],2),
																round($this->extraVelden['BEH'],2),
																round($this->extraVelden['BEW'],2),
																round($this->extraVelden['KNBA'],2),
																round($totaalWaarde,2),
																round($percentage,2),
																round($performance,2)
																*/
		}
    else
    {
	  	$header = array("",
		  													"Client",
			  												"Naam",
                                "Portefeuille",
                                "Accountmanager",
                                "Depotbank",
                                "Risicoprofiel",
                                "SoortOvereenkomst",
															"Absoluut",
															"in %",
															"Performance",
															"AFM-#");
    }
  if($this->stdevTonen==true)
  {
    $header[]='stdev';
    
  }
    $this->pdf->excelData[]=$header;




		$this->pdf->Line($this->pdf->marge ,$this->pdf->GetY(), $this->pdf->marge + 270,$this->pdf->GetY());

		$this->pdf->SetFont("Times","",10);

		$this->tel = 0;
    $performanceTotaal=0;
		foreach($portefeuilles as $pdata)
		{
		  foreach($this->orderByVelden as $veld)
      {
        if(trim($pdata[$veld])=='')
          $pdata[$veld]='leeg';
      }
      
		  if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
				logScherm("Portefeuille: ".$pdata['Portefeuille']." (Gegevens ophalen)");
			}

			$this->tel ++;
			$portefeuille = $pdata['Portefeuille'];

			if(db2jul($rapportageDatum[a]) < db2jul($pdata['Startdatum']))
			{
				$startdatum = $pdata['Startdatum'];
			}
			else
			{
				$startdatum = $rapportageDatum['a'];
			}

			$einddatum = $rapportageDatum['b'];


      foreach($this->orderByVelden as $veld)
      {
        if($this->selectData['orderby'.$veld] == 1)
        {


        }
      }
      
      
      

      $this->printTotaal('SoortOvereenkomst',$pdata['SoortOvereenkomst']);
      $this->printTotaal('Risicoklasse',$pdata['Risicoklasse']);
      $this->printTotaal('Accountmanager',$pdata['Accountmanager']);
      $this->printTotaal('Vermogensbeheerder',$pdata['Vermogensbeheerder']);
      
      
      $this->printKop('Vermogensbeheerder',$pdata['Vermogensbeheerder']);
      $this->printKop('Accountmanager',$pdata['Accountmanager']);
      $this->printKop('Risicoklasse',$pdata['Risicoklasse']);
      $this->printKop('SoortOvereenkomst',$pdata['SoortOvereenkomst']);
      
    

			$DB2 = new DB();
			if($this->extraVeldenTonen==true)
			{
			  $this->extraVelden=array();
				$kostenOpbrengstTotaal=0;
		  	// haal beginwaarde op
		  	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
			  				 "FROM TijdelijkeRapportage WHERE ".
				  			 " rapportageDatum ='".$startdatum."' AND ".
					  		 " portefeuille = '".$portefeuille."' "
						  	 .$__appvar['TijdelijkeRapportageMaakUniek'];
		  	debugSpecial($query,__FILE__,__LINE__);

		  	$DB2->SQL($query);
		  	$DB2->Query();
		  	$totaalWaarde= $DB2->nextRecord();
		  	$this->extraVelden['beginvermogen'] = $totaalWaarde['totaal'];


		  	$queryTotaal = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
			  				 "FROM TijdelijkeRapportage WHERE ".
				  			 " rapportageDatum ='".$einddatum."' AND ".
					  		 " portefeuille = '".$portefeuille."' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
			  debugSpecial($queryTotaal,__FILE__,__LINE__);
  			$DB2->SQL($queryTotaal);
	  		$DB2->Query();
		  	$totaalWaarde = $DB2->nextRecord();
			  $this->extraVelden['eindvermogen'] = $totaalWaarde['totaal'];
		  	$this->extraVelden['stortingen'] = getStortingen($portefeuille,$startdatum,$einddatum);
		  	$this->extraVelden['onttrekkingen'] = getOnttrekkingen($portefeuille,$startdatum,$einddatum);
		  	$waardeMutatie 	   	= $this->extraVelden['eindvermogen'] - $this->extraVelden['beginvermogen'];
		  	$this->extraVelden['resultaat'] = $waardeMutatie - $this->extraVelden['stortingen'] + $this->extraVelden['onttrekkingen'];

		  	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaalB, ".
	 						 "SUM(beginPortefeuilleWaardeEuro) AS totaalA ".
							 "FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$einddatum."' AND ".
							 " portefeuille = '".$portefeuille."' AND ".
							 " type = 'fondsen' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
			  debugSpecial($query,__FILE__,__LINE__);
			  $DB2->SQL($query);
			  $DB2->Query();
			  $totaal = $DB2->nextRecord();
  			$this->extraVelden['koersongerealiseerd'] = $totaal['totaalB'] - $totaal['totaalA'];
	 			$this->extraVelden['koersgerealiseerd'] = gerealiseerdKoersresultaat($portefeuille, $startdatum, $einddatum);


		  	// ophalen van rente totaal A en rentetotaal B
		  	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
			  				 "FROM TijdelijkeRapportage WHERE ".
				  			 " rapportageDatum ='".$einddatum."' AND ".
					  		 " portefeuille = '".$portefeuille."' AND ".
						  	 " type = 'rente' "
							   .$__appvar['TijdelijkeRapportageMaakUniek'];
			  debugSpecial($query,__FILE__,__LINE__);
  			$DB2->SQL($query);
	  		$DB2->Query();
		  	$totaalA = $DB2->nextRecord();
  			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
	  						 "FROM TijdelijkeRapportage WHERE ".
		  					 " rapportageDatum ='".$startdatum."' AND ".
			  				 " portefeuille = '".$portefeuille."' AND ".
				  			 " type = 'rente' "
					  		 .$__appvar['TijdelijkeRapportageMaakUniek'];
	  		debugSpecial($query,__FILE__,__LINE__);
		  	$DB2->SQL($query);
		  	$DB2->Query();
			  $totaalB = $DB2->nextRecord();
		  	$this->extraVelden['rente'] = $totaalA['totaal'] - $totaalB['totaal'];

	 			$query = "SELECT Grootboekrekeningen.Omschrijving,Grootboekrekeningen.Grootboekrekening, ".
		  	"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers) AS totaalcredit, ".
		  	"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) AS totaaldebet ,
		  	 Grootboekrekeningen.Kosten  ".
		  	"FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen ".
		  	"WHERE ".
		  	"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		  	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
		  	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  	"Rekeningmutaties.Verwerkt = '1' AND ".
		  	"Rekeningmutaties.Boekdatum > '".$startdatum."' AND ".
		  	"Rekeningmutaties.Boekdatum <= '".$einddatum."' AND ".
		  	"Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening  AND (Grootboekrekeningen.Kosten=1 OR Grootboekrekeningen.Opbrengst=1)".
		  	"GROUP BY Rekeningmutaties.Grootboekrekening ".
		  	"ORDER BY Grootboekrekeningen.Afdrukvolgorde ";
		  	$DB2->SQL($query);
		  	$DB2->Query();
		  	while($grootboek = $DB2->nextRecord())
		  	{
			    $this->extraVelden[$grootboek['Grootboekrekening']] = ($grootboek['totaalcredit'] - $grootboek['totaaldebet'] );
		      $kostenOpbrengstTotaal+=($grootboek['totaalcredit'] - $grootboek['totaaldebet']);
		  	}
        $this->extraVelden['koersResulaatValutas']  =  $this->extraVelden['resultaat'] - $this->extraVelden['koersongerealiseerd'] - $this->extraVelden['koersgerealiseerd'] - $this->extraVelden['rente'] - $kostenOpbrengstTotaal;
			}
			elseif($this->selectData['filetype']=='database')
      {
        $this->extraVelden=array();
        $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
          "FROM TijdelijkeRapportage WHERE ".
          " rapportageDatum ='".$startdatum."' AND ".
          " portefeuille = '".$portefeuille."' "
          .$__appvar['TijdelijkeRapportageMaakUniek'];
        debugSpecial($query,__FILE__,__LINE__);
  
        $DB2->SQL($query);
        $DB2->Query();
        $totaalWaarde= $DB2->nextRecord();
        $this->extraVelden['beginvermogen'] = $totaalWaarde['totaal'];
        $queryTotaal = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
          "FROM TijdelijkeRapportage WHERE ".
          " rapportageDatum ='".$einddatum."' AND ".
          " portefeuille = '".$portefeuille."' "
          .$__appvar['TijdelijkeRapportageMaakUniek'];
        debugSpecial($queryTotaal,__FILE__,__LINE__);
        $DB2->SQL($queryTotaal);
        $DB2->Query();
        $totaalWaarde = $DB2->nextRecord();
        $this->extraVelden['eindvermogen'] = $totaalWaarde['totaal'];
        $this->extraVelden['stortingen'] = getStortingen($portefeuille,$startdatum,$einddatum);
        $this->extraVelden['onttrekkingen'] = getOnttrekkingen($portefeuille,$startdatum,$einddatum);
        $waardeMutatie 	   	= $this->extraVelden['eindvermogen'] - $this->extraVelden['beginvermogen'];
        $this->extraVelden['resultaat'] = $waardeMutatie - $this->extraVelden['stortingen'] + $this->extraVelden['onttrekkingen'];
      }


			$queryTotaal = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
							 "FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$einddatum."' AND ".
							 " portefeuille = '".$portefeuille."' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($queryTotaal,__FILE__,__LINE__);

			$DB2->SQL($queryTotaal);
			$DB2->Query();
			$totaalWaarde = $DB2->nextRecord();
			$totaalWaarde = $totaalWaarde['totaal'];

      $percentage=0;
			if($this->grandtotaalWaarde <> 0)
				$percentage = $totaalWaarde / ($this->grandtotaalWaarde/100);
      //echo "performanceMeting($portefeuille, $startdatum, $einddatum, ".$pdata['PerformanceBerekening'].",".$pdata['RapportageValuta'].");";
      if($pdata['RapportageValuta'] =='')
        $pdata['RapportageValuta']='EUR';
			$performance 			  = performanceMeting($portefeuille, $startdatum, $einddatum, $pdata['PerformanceBerekening'], $pdata['RapportageValuta']);
			$performanceTotaal += $totaalWaarde * $performance;
      

			$afm=AFMstd($portefeuille,$einddatum,false);
      $afmAandeel = ($percentage/100 * $afm['std']);
      $this->afmTotalen['totaal']+=$afmAandeel;
      foreach($this->orderByVelden as $veld)
      {
        $this->afmTotalen[$veld][$pdata[$veld]]+=$afmAandeel*100;
      }
      
      if($this->stdevTonen==true)
      {
        $stdev=new rapportSDberekening($portefeuille,$einddatum);
        $stdev->addReeks('totaal');
        $stdev->berekenWaarden();
      }
      
			// schrijf data !
			$this->pdf->Cell(10 , 4 , $this->tel , 0, 0, "R");
			$this->pdf->Cell(45 , 4 , $this->tekstVerkorten($pdata['Client'],45) , 0, 0, "L");
			$this->pdf->Cell(90 , 4 , $this->tekstVerkorten($pdata['Naam'],90) , 0, 0, "L");
			$this->pdf->Cell(20 , 4 , $pdata['Portefeuille'] , 0, 0, "L");
			$this->pdf->Cell(20 , 4 , $pdata['Depotbank'] , 0, 0, "L");
			$this->pdf->Cell(25 , 4 , $this->formatGetal($totaalWaarde,2) , 0, 0, "R");
			$this->pdf->Cell(20 , 4 , $this->formatGetal($percentage,2), 0, 0, "R");
			$this->pdf->Cell(20 , 4 , $this->formatGetal($performance,2), 0, 0, "R");
			$this->pdf->Cell(20 , 4 , $this->formatGetal($afm['std'],2), 0, 0, "R");
      if($this->stdevTonen==true)
      {
        $this->pdf->Cell(14 , 4 , $this->formatGetal($stdev->uitvoer['totaal'],2), 0,0 , "R");
      }
      $this->pdf->ln();
			if($this->extraVeldenTonen == true)
			{

			  $row = array($this->tel,
																$pdata['Client'],
																$pdata['Naam'],
																$pdata['Portefeuille'],
                                $pdata['Accountmanager'],
                                $pdata['SoortOvereenkomst'], 
                                $pdata['Risicoklasse'],
																$pdata['Depotbank'],
																round($this->extraVelden['beginvermogen'],2),
																round($this->extraVelden['stortingen'],2),
																round($this->extraVelden['onttrekkingen'],2),
																round($this->extraVelden['resultaat'],2),
																round($this->extraVelden['koersongerealiseerd'],2),
																round($this->extraVelden['koersgerealiseerd'],2),
																round($this->extraVelden['rente'],2),
																round($this->extraVelden['koersResulaatValutas'],2));
			  foreach ($grootboeken as $gb=>$grootboek)
				  $row[]=round($this->extraVelden[$gb],2);

  		  $row[]=round($totaalWaarde,2);
	      $row[]=round($percentage,2);
			  $row[]=round($performance,2);
			}
      else
      {
        $row = array($this->tel,
          $pdata['Client'],
          $pdata['Naam'],
          $pdata['Portefeuille'],
          $pdata['Accountmanager'],
          $pdata['Depotbank'],
          $pdata['Risicoklasse'],
          $pdata['SoortOvereenkomst'],
          round($totaalWaarde,2),
          round($percentage,2),
          round($performance,2),
          round($afm['std'],2));
        
      }
      if($this->stdevTonen==true)
      {
        $row[]=round($stdev->uitvoer['totaal'],2);
      }
      $this->pdf->excelData[] = $row;

if($this->selectData['filetype']=='database')
{
  $tmparray=array('Rapport'=>'Management',
				        'Client' => $pdata['Client'],
				        'Naam'=>$pdata['Naam'],
				        'Naam1'=>$pdata['Naam1'],
				        'Portefeuille'=>$pdata['Portefeuille'],
				        'Vermogensbeheerder'=>$pdata['Vermogensbeheerder'],
				        'totaalvermogen'=>round($totaalWaarde,2),
                'beginvermogen'=>round($this->extraVelden['beginvermogen'],2),
                'resultaat'=>round($this->extraVelden['resultaat'],2),
				        'inprocenttotaal'=>round($percentage,2),
				        'performance'=>round($performance,2),
                'stortingen'=>round($this->extraVelden['stortingen'],2),
                'onttrekkingen'=>round($this->extraVelden['onttrekkingen'],2),
                'AFMstd'=>round($afm['std'],2));
  if($this->stdevTonen==true)
  {
    $tmparray['werkelijkeStdev']=round($stdev->uitvoer['totaal'],2);
  }
  			$query = "SELECT Grootboekrekeningen.Omschrijving,Grootboekrekeningen.Grootboekrekening, ".
			"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers) AS totaalcredit, ".
			"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) AS totaaldebet ".
			"FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen ".
			"WHERE ".
			"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
			"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
			"Rekeningmutaties.Verwerkt = '1' AND ".
			"Rekeningmutaties.Boekdatum > '".$startdatum."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$einddatum."' AND ".
			"Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening ".
			"GROUP BY Rekeningmutaties.Grootboekrekening ".
			"ORDER BY Grootboekrekeningen.Afdrukvolgorde ";

			$DB2->SQL($query);
			$DB2->Query();
			while($grootboek = $DB2->nextRecord())
			{
			  if($grootboek['Grootboekrekening'] <> '')
			    $tmparray[$grootboek['Grootboekrekening']]=round(($grootboek['totaalcredit']-$grootboek['totaaldebet']),2);
			}

			$queryTotaal = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
							 "FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$einddatum."' AND ".
							 " portefeuille = '".$portefeuille."' AND ".
							 " type = 'rekening' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
			$DB2->SQL($queryTotaal);
			$DB2->Query();
			$totaalWaardeLiq = $DB2->nextRecord();
			$tmparray['liquiditeiten'] = $totaalWaardeLiq['totaal'];

			$this->dbWaarden[]=$tmparray;
      }

			verwijderTijdelijkeTabel($portefeuille,$startdatum);

	
      foreach($this->orderByVelden as $veld)
      {
        if($this->selectData['orderby'.$veld] == 1)
        {
          $this->totalen[$veld]['totaal']+= $totaalWaarde;
          $this->totalen[$veld]['performanceTotaal']+= $totaalWaarde * $performance;
          $this->totalen[$veld]['vorige']=$pdata[$veld];
        }
      }
		}


      $this->printTotaal('Vermogensbeheerder',$pdata['Vermogensbeheerder'],true);
      $this->printTotaal('Accountmanager',$pdata['Accountmanager'],true);
      $this->printTotaal('Risicoklasse',$pdata['Risicoklasse'],true);
      $this->printTotaal('SoortOvereenkomst',$pdata['SoortOvereenkomst'],true);
      
      
      	// subtotaal vermogensbeheerder & accountmanager
/*
		if($vorigeAccountmanager <> "" && $this->selectData['orderbyAccountmanager'] == 1)
		{
				if($this->tel > 0)
				{
					if($this->grandtotaalWaarde <> 0)
						$percentage = $accountmanagerTotaal / ($this->grandtotaalWaarde/100);

					$perf = $accountPerformanceTotaal / $accountmanagerTotaal;

					$this->pdf->ln();
					$this->pdf->SetFont("Times","b",10);
					$this->pdf->Cell(165 , 4 , "Subtotaal ".$vorigeAccountmanager , 0, 0, "R");
					$this->pdf->Cell(20 , 4 , "" , 0, 0, "L");
					$this->pdf->Cell(25 , 4 , $this->formatGetal($accountmanagerTotaal,2) , 0, 0, "R");
					$this->pdf->Cell(20 , 4 , $this->formatGetal($percentage,2), 0, 0, "R");
					$this->pdf->Cell(20 , 4 , $this->formatGetal($perf,2), 0, 0, "R");
          $this->pdf->Cell(20 , 4 , $this->formatGetal($this->afmTotalen['Accountmanager'][$vorigeAccountmanager]/$percentage,2), 0, 1, "R");
				}

				$this->pdf->ln();
				$this->pdf->SetFont("Times","",10);
		}

		if($vorigeVermogensbeheerder <> "" && $this->selectData['orderbyVermogensbeheerder'] == 1)
		{
				if($this->tel > 0)
				{
					if($this->grandtotaalWaarde <> 0)
						$percentage = $vermogensbeheerderTotaal / ($this->grandtotaalWaarde/100);

					$perf = $vermogensbeheerderPerformanceTotaal / $vermogensbeheerderTotaal;

					$this->pdf->ln();
					$this->pdf->SetFont("Times","b",10);
					$this->pdf->Cell(165 , 4 , "Subtotaal ".$vorigeVermogensbeheerder , 0, 0, "R");
					$this->pdf->Cell(20 , 4 , "" , 0, 0, "L");
					$this->pdf->Cell(25 , 4 , $this->formatGetal($vermogensbeheerderTotaal,2) , 0, 0, "R");
					$this->pdf->Cell(20 , 4 , $this->formatGetal($percentage,2), 0, 0, "R");
					$this->pdf->Cell(20 , 4 , $this->formatGetal($perf,2), 0, 0, "R");
          $this->pdf->Cell(20 , 4 , $this->formatGetal($this->afmTotalen['Vermogensbeheerder'][$vorigeVermogensbeheerder]/$percentage,2), 0, 1, "R");
				}
				$this->pdf->ln();
				$this->pdf->SetFont("Times","",10);
		}
*/
		$this->pdf->SetFont("Times","b",10);

		if($this->tel > 0)
			$performanceTotaal = $performanceTotaal / $this->grandtotaalWaarde;

		$this->pdf->ln();

		$this->pdf->Line($this->pdf->marge + 187,$this->pdf->GetY(), $this->pdf->marge + 210,$this->pdf->GetY());
		$this->pdf->Line($this->pdf->marge + 212,$this->pdf->GetY(), $this->pdf->marge + 230,$this->pdf->GetY());
		$this->pdf->Line($this->pdf->marge + 232,$this->pdf->GetY(), $this->pdf->marge + 250,$this->pdf->GetY());

		// druk totaal af
		$this->pdf->Cell(10 , 4 , "" , 0, 0, "R");
		$this->pdf->Cell(35 , 4 , "" , 0, 0, "L");
		$this->pdf->Cell(120 , 4 , "Totaal" , 0, 0, "R");
		$this->pdf->Cell(20 , 4 , "" , 0, 0, "L");
		$this->pdf->Cell(25 , 4 , $this->formatGetal($this->grandtotaalWaarde,2) , 0, 0, "R");
		$this->pdf->Cell(20 , 4 , $this->formatGetal(100,2), 0, 0, "R");
		$this->pdf->Cell(20 , 4 , $this->formatGetal($performanceTotaal,2), 0, 0, "R");
    $this->pdf->Cell(20 , 4 , $this->formatGetal($this->afmTotalen['totaal'],2), 0, 1, "R");

		$this->pdf->SetFont("Times","",10);
		if($this->progressbar)
			$this->progressbar->hide();
    
    $stopTijd=time();
    logIt("Management overzicht stop");
    logIt("Management overzicht runtijd: ".($stopTijd-$startTijd).' seconden');
	}
  
  function printTotaal($veld,$waarde,$last=false)
  {
    if($this->selectData['orderby'.$veld] == 1)
    {
      if($waarde <> $this->totalen[$veld]['vorige'] || $last==true)
		  {
  			if($this->tel > 0 && $this->totalen[$veld]['vorige'] <> '')
				{
					if($this->grandtotaalWaarde <> 0)
						$percentage = $this->totalen[$veld]['totaal'] / ($this->grandtotaalWaarde/100);

					$perf = $this->totalen[$veld]['performanceTotaal'] / $this->totalen[$veld]['totaal'];

					$this->pdf->ln();
					$this->pdf->SetFont("Times","b",10);
					$this->pdf->Cell(165 , 4 , "Subtotaal $veld ".$this->totalen[$veld]['vorige'] , 0, 0, "R");
					$this->pdf->Cell(20 , 4 , "" , 0, 0, "L");
					$this->pdf->Cell(25 , 4 , $this->formatGetal($this->totalen[$veld]['totaal'],2) , 0, 0, "R");
					$this->pdf->Cell(20 , 4 , $this->formatGetal($percentage,2), 0, 0, "R");
					$this->pdf->Cell(20 , 4 , $this->formatGetal($perf,2), 0, 0, "R");
          $this->pdf->Cell(20 , 4 , $this->formatGetal($this->afmTotalen[$veld][$this->totalen[$veld]['vorige']]/$percentage,2), 0, 1, "R");
          
          
					$this->totalen[$veld]['totaal'] = 0;
					$this->totalen[$veld]['performanceTotaal'] = 0;
				}
      }   
    }
  }

  function printKop($veld,$waarde,$last=false)
  {
    if($this->selectData['orderby'.$veld] == 1)
    {
      if($waarde <> $this->totalen[$veld]['vorige'] || $last==true)
		  {
        if($last==false)
        {
          $this->pdf->ln();
	    	  $this->pdf->SetFont("Times","bi",10);
	    	  $this->pdf->Cell(30 , 4 , $veld." ".$waarde , 0, 1, "L");
					
	      	$this->pdf->excelData[] = array();
	      	$this->pdf->excelData[] = array("",$veld." ".$waarde);
	      	$this->pdf->excelData[] = array();
        	$this->pdf->SetFont("Times","",10); 
        }
      }   
    }
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
        //listarray($waarden);
        foreach ($waarden as $key=>$value)
        {
          $query.=",`$key`='".addslashes($value)."' ";
        }
        $db->SQL($query);
	      $db->Query();
      }
    }

	}
}
?>
<?php

/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/01/20 12:13:28 $
File Versie					: $Revision: 1.9 $

$Log: ZorgplichtBuilder.php,v $
Revision 1.9  2019/01/20 12:13:28  rvv
*** empty log message ***

Revision 1.8  2016/01/09 18:57:51  rvv
*** empty log message ***

Revision 1.7  2015/11/12 07:46:18  rvv
*** empty log message ***

Revision 1.6  2014/12/21 10:32:26  rvv
*** empty log message ***

Revision 1.5  2014/05/21 15:20:33  rvv
*** empty log message ***

Revision 1.4  2013/05/12 11:18:58  rvv
*** empty log message ***

Revision 1.3  2012/08/11 13:06:05  rvv
*** empty log message ***

Revision 1.2  2012/07/25 16:00:32  rvv
*** empty log message ***

Revision 1.1  2012/05/30 16:02:08  rvv
*** empty log message ***


*/

define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");

include_once("rapportRekenClass.php");
include_once("../classes/AE_cls_fpdf.php");
include_once("rapport/Zorgplichtcontrole.php");


class ZorgplichtBuilder
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	var $tmp_table;
	var $tmp_table_struct;

	function ZorgplichtBuilder(  $selectData )
	{
	  global $USR;
		$this->selectData = $selectData;
		$this->excelData 	= array();
		$this->datum = mktime();
		$this->tmp_table = "tmp_reportbuilder_$USR";
		$this->tmp_table_struct = "CREATE TABLE `".$this->tmp_table."` (
`id` INT NOT NULL AUTO_INCREMENT ,
`Portefeuille` VARCHAR( 24 ) NOT NULL ,
`Vermogensbeheerder` VARCHAR( 10 ) NOT NULL ,
`Client` VARCHAR( 16 ) NOT NULL ,
`Consolidatie` tinyint(4) NOT NULL default '0',
`Naam` VARCHAR( 50 ) NOT NULL ,
`Depotbank` VARCHAR( 10 ) NOT NULL ,
`Startdatum` DATETIME NOT NULL ,
`Einddatum` DATETIME NOT NULL ,
`ClientVermogensbeheerder` VARCHAR( 10 ) NOT NULL ,
`Accountmanager` VARCHAR( 15 ) NOT NULL ,
`Risicoprofiel` VARCHAR( 15 ) NOT NULL ,
`SoortOvereenkomst` VARCHAR( 15 ) NOT NULL ,
`Risicoklasse` VARCHAR( 50 ) NOT NULL ,
`Remisier` VARCHAR( 15 ) NOT NULL ,
`AFMprofiel` VARCHAR( 15 ) NOT NULL ,
`ModelPortefeuille` VARCHAR( 24 ) NOT NULL ,
`totaalvermogen` DOUBLE NOT NULL ,
`inprocenttotaal` DOUBLE NOT NULL ,
`conclusie` VARCHAR( 100 ) ,
`reden` VARCHAR( 250 ) ,
`norm` VARCHAR( 250 ) ,
PRIMARY KEY ( `id` )
) ";

		$this->removeTmpTable();
		$this->createTmpTable();
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function removeTmpTable()
	{
		$DB = new DB();
		$select = "SHOW TABLES LIKE '".$this->tmp_table."'";
	  $DB->SQL($select);
	  if ($DB->lookupRecord())
	  {
			$DB->SQL("DROP TABLE `".$this->tmp_table."` ");
			return $DB->Query();
	  }
	}

	function createTmpTable()
	{
		$DB = new DB();
		$DB->SQL($this->tmp_table_struct);
		return $DB->Query();
	}

	function writeRapport()
	{
	global $__appvar,$USR;
		$einddatumJul = $this->datum;
		$einddatum = jul2sql($this->datum);

		$jaar = date("Y",$einddatumJul);




for ($i=1; $i <3; $i++)
{
	$where='where'. $i;
	if(count($this->selectData[$where]) > 0 )
		{
			$where2 = "";
			for($a = 0; $a < count($this->selectData[$where]); $a++)
			{
			$andor = "";
			if((count($this->selectData[$where])) > ($a+1))
			  {
				$andor = $this->selectData[$where][$a][andor];
			  }
			if(	$this->selectData[$where][$a]['field'] != "" )
			  {
				if($this->selectData[$where][$a]['operator'] == "LIKE")
				{
				  $waarde = $this->selectData[$where][$a]['search'];
				  if (strstr($waarde,"%"))  // als wildcards aangegeven dan die gebruiken
				  {
				    $where2 .= "Portefeuilles.".$this->selectData[$where][$a]['field']." ".
						  				$this->selectData[$where][$a]['operator']." '".
							  			$this->selectData[$where][$a]['search']."' ".$andor." ";
				  }
				  else                    // als geen wildcards aangegeven dan %zoek%
				  {
					  $where2 .= "Portefeuilles.".$this->selectData[$where][$a]['field']." ".
							  			$this->selectData[$where][$a]['operator']." '%".
						  				$this->selectData[$where][$a]['search']."%' ".$andor." ";
				  }
				}
				else
					$where2 .= "Portefeuilles.".$this->selectData[$where][$a]['field']." ".
										$this->selectData[$where][$a]['operator']." '".
										$this->selectData[$where][$a]['search']."' ".$andor." ";
			  }
			}
			if($where2 <> "")
				$extraquery .= " AND ( ".$where2." ) ";
		  }
		// controle op einddatum portefeuille
}

		// controle op einddatum portefeuille
		if($this->selectData['inactiefOpnemen'] == 1)
	    $extraquery='';
	  else
	    $extraquery  .= " AND Portefeuilles.Einddatum > '".$einddatum."'  ";
    
    if($_SESSION['reportBuilder']['incConsolidaties']==1)
    {
      $consolidatieFilter='AND Portefeuilles.consolidatie<2';
    }
    else
    {
      $consolidatieFilter='AND Portefeuilles.consolidatie=0';
    }
    
	if(checkAccess($type))
  {
	  $join = "";
	  $beperktToegankelijk = '';
  }
  else
  {
  	$join = " INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND  VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
	  				JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";

  	if($_SESSION['usersession']['gebruiker']['internePortefeuilles'] == '1')
        $internDepotToegang="OR Portefeuilles.interndepot=1";

	  if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
	    $beperktToegankelijk = " AND (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' $internDepotToegang) ";
    else
	    $beperktToegankelijk = " AND (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
  }

	 $join .= " JOIN Vermogensbeheerders on Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder ";

		$query = " SELECT ".
					" Portefeuilles.ClientVermogensbeheerder, ".
					" Portefeuilles.Portefeuille, ".
					" Portefeuilles.Depotbank, ".
					" Portefeuilles.Client, ".
					" Portefeuilles.Startdatum, ".
					" Portefeuilles.Einddatum, ".
					" Portefeuilles.Consolidatie, ".
					" Portefeuilles.Vermogensbeheerder, ".
					" Portefeuilles.Depotbank, ".
					" Portefeuilles.Accountmanager, ".
					" Portefeuilles.Risicoprofiel, ".
					" Portefeuilles.SoortOvereenkomst, ".
					" Portefeuilles.Risicoklasse, ".
					" Portefeuilles.Remisier, ".
					" Portefeuilles.AFMprofiel, ".
					" Portefeuilles.ModelPortefeuille, ".
					" Vermogensbeheerders.PerformanceBerekening, ".
					" Clienten.Naam ".
					" FROM (Portefeuilles, Clienten)  $join WHERE Portefeuilles.Client = Clienten.Client ".$extraquery." $consolidatieFilter ".$beperktToegankelijk;


		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$records = $DB->records();

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 50 / $records;
		}

		if ($this->selectData['datumVanaf'] != "")
	   {
	    $begindatum	= $this->selectData['datumVanaf'];
	    $begindatum = form2jul($begindatum);
	    $begindatum = jul2sql($begindatum);
	   }
	   else
     {
	    $begindatumJul = mktime(1,1,1,1,1,$jaar);
	    $begindatum = jul2sql($begindatumJul);
	   }

		while($pdata = $DB->nextRecord())
		{
			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
			}

			$portefeuille = $pdata['Portefeuille'];
			if(db2jul($begindatum) < db2jul($pdata['Startdatum']))
			{
				$startdatum = $pdata['Startdatum']; //31-08-2006 rvv $begindatum vervangen voor startdatum.
			}
			else
			{
				$startdatum = $begindatum;
			}

			$julrapport 		= db2jul($startdatum);
			$rapportMaand 	= date("m",$julrapport);
			$rapportDag 		= date("d",$julrapport);

			if($rapportMaand == 1 && $rapportDag == 1)
				$startjaar = true;
			else
				$startjaar = false;

			$fondswaarden['a'] =  berekenPortefeuilleWaarde($portefeuille,  $startdatum, $startjaar,'',$startdatum);
			$fondswaarden['b'] =  berekenPortefeuilleWaarde($portefeuille,  $einddatum,'','',$startdatum);

			vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,$startdatum);
			vulTijdelijkeTabel($fondswaarden['b'] ,$portefeuille,$einddatum);
			//rvv
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
			$grandtotaalWaarde += $totaalWaarde['totaal'];

		}

				// nog een keer een loop over de portefeuilles!
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		if($DB->records() <= 0)
		{
			echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
			$this->progressbar->hide();
			exit;
		}
		$tel = 0;
		while($pdata = $DB->nextRecord())
		{
      $crmData=getCrmNaam($pdata['Portefeuille']);
		  if($crmData['CrmClientNaam'] == 1)
		  {
		     $pdata['Naam']=$crmData['naam'];
		     $pdata['Naam1']=$crmData['naam1'];
		  }


			$tel ++;
			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
				logScherm("Portefeuille: ".$pdata['Portefeuille']);
			}
		//	$startdatum = jul2sql($begindatumJul);
			$portefeuille = $pdata['Portefeuille'];
			if(db2jul($begindatum) < db2jul($pdata['Startdatum']))
			{
				$startdatum = $pdata[Startdatum];
			}
			else
			{
				$startdatum = $begindatum;
			}
			$julrapport 		= db2jul($startdatum);
			$rapportMaand 	= date("m",$julrapport);
			$rapportDag 		= date("d",$julrapport);

			//rvvend
			// doe berekeningen.
			$DB2 = new DB();
			$queryTotaal = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
							 "FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$einddatum."' AND ".
							 " portefeuille = '".$portefeuille."' AND ".
							 " type = 'rekening' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($queryTotaal,__FILE__,__LINE__);

			$DB2->SQL($queryTotaal);
			$DB2->Query();
			$totaalWaarde = $DB2->nextRecord();
			$liquiditeiten = $totaalWaarde[totaal];


			$queryTotaal = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
							 "FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$einddatum."' AND ".
							 " portefeuille = '".$portefeuille."' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($queryTotaal,__FILE__,__LINE__);
			$DB2->SQL($queryTotaal);
			$DB2->Query();
			$totaalWaarde = $DB2->nextRecord();
			$totaalvermogen = $totaalWaarde[totaal];

			$inprocenttotaal = $totaalvermogen / $grandtotaalWaarde * 100;

			$zorgplichtMeting=new Zorgplichtcontrole($pdata);
			$zorgPlichtResultaat=$zorgplichtMeting->zorgplichtMeting($pdata,$einddatum);
			$conclusie= $zorgPlichtResultaat['zorgMeting'];
			$reden= $zorgPlichtResultaat['zorgMetingReden'];
      $xlsUitvoerNorm='';
      foreach($zorgPlichtResultaat['conclusieDetail'] as $categorie=>$categorieData)
        $xlsUitvoerNorm.=$categorie.'='.$categorieData['norm'].';';
			// Insert into tmp table.
			$insert = "INSERT INTO `".$this->tmp_table."` SET ".
								" Portefeuille = '".mysql_escape_string($pdata['Portefeuille'])."' ".
								",Vermogensbeheerder = '".mysql_escape_string($pdata['Vermogensbeheerder'])."' ".
								",Client = '".mysql_escape_string($pdata['Client'])."' ".
                ",Consolidatie = '".mysql_escape_string($pdata['Consolidatie'])."' ".
								",Naam = '".mysql_escape_string($pdata['Naam'])."' ".
								",Depotbank = '".mysql_escape_string($pdata['Depotbank'])."' ".
								",Startdatum = '".mysql_escape_string($pdata['Startdatum'])."' ".
								",Einddatum = '".mysql_escape_string($pdata['Einddatum'])."' ".
								",ClientVermogensbeheerder = '".mysql_escape_string($pdata['ClientVermogensbeheerder'])."' ".
								",Accountmanager = '".mysql_escape_string($pdata['Accountmanager'])."' ".
								",Risicoklasse = '".mysql_escape_string($pdata['Risicoklasse'])."' ".
								",Remisier = '".mysql_escape_string($pdata['Remisier'])."' ".
								",AFMprofiel = '".mysql_escape_string($pdata['AFMprofiel'])."' ".
								",ModelPortefeuille = '".mysql_escape_string($pdata['ModelPortefeuille'])."' ".
								",totaalvermogen = '".mysql_escape_string(round($totaalvermogen,2))."' ".
								",inprocenttotaal = '".mysql_escape_string(round($inprocenttotaal,2))."' ".
								",conclusie = '".mysql_escape_string($conclusie)."' ".
								",reden = '".mysql_escape_string($reden)."' ".
                ",norm = '".mysql_real_escape_string($xlsUitvoerNorm)."'";

      $totaalvermogen = '';$inprocenttotaal='';$conclusie='';$reden='';
//echo $insert;
			$DBt = new DB();
			$DBt->SQL($insert);
			$DBt->Query();

			//verwijderTijdelijkeTabel($portefeuille,$einddatum);
			verwijderTijdelijkeTabel($portefeuille,$startdatum);

			// en weer verwijderen ?
		}
		// maak nu maar de 2e selectie en de CSV
		$this->buildCSV();

		if($this->progressbar)
			$this->progressbar->hide();
		$this->removeTmpTable();
	}

	function buildCSV()
	{
		// rebuild query on temp table.

for ($i=3; $i <4; $i++)
{
	$where='where'. $i;
	if(count($this->selectData[$where]) > 0 )
		{
			$where2 = "";
			for($a = 0; $a < count($this->selectData[$where]); $a++)
			{
			$andor = "";
			if((count($this->selectData[$where])) > ($a+1))
			  {
				$andor = $this->selectData[$where][$a]['andor'];
			  }
			if(	$this->selectData[$where][$a]['field'] != "" )
			  {
				if($this->selectData[$where][$a]['operator'] == "LIKE")
				{
				  $waarde = $this->selectData[$where][$a]['search'];
				  if (strstr($waarde,"%"))  // als wildcards aangegeven dan die gebruiken
				  {
				    $where2 .= 			$this->selectData[$where][$a]['field']." ".
						  				$this->selectData[$where][$a]['operator']." '".
							  			$this->selectData[$where][$a]['search']."' ".$andor." ";
				  }
				  else                    // als geen wildcards aangegeven dan %zoek%
				  {
					  $where2 .= 		$this->selectData[$where][$a]['field']." ".
							  			$this->selectData[$where][$a]['operator']." '%".
						  				$this->selectData[$where][$a]['search']."%' ".$andor." ";
				  }
				}
				else
					$where2 .= 			$this->selectData[$where][$a]['field']." ".
										$this->selectData[$where][$a]['operator']." '".
										$this->selectData[$where][$a]['search']."' ".$andor." ";
			  }
			}
			if($where2 <> "")
				$extraquery .= " AND ( ".$where2." ) ";
		  }
		// controle op einddatum portefeuille

		$extraquery  .= " AND Einddatum > '".$einddatum."' ";
}


		// maak veld selectie
		// maak CSV header
		for($a=0; $a < count($this->selectData['fields']); $a++)
		{
			if($this->selectData['fields'][$a] <> $this->selectData['groupby'][0]['actionField'])
			{
				$selectThis[] = $this->selectData['fields'][$a];
				$header[] = $this->selectData['fields'][$a];
			}
			else
			{
				$selectThis[] = $this->selectData['groupby'][0]['actionType']."(".$this->selectData['groupby'][0]['actionField'].") AS ".
												$this->selectData['groupby'][0]['actionType']."_".$this->selectData['fields'][$a];
				$header[] = $this->selectData['groupby'][0]['actionType']."_".$this->selectData['fields'][$a];
			}
		}

		$fields = implode(", ",$selectThis);

		$this->excelData[] = $header;

		if(count($this->selectData['groupby']) > 0)
		{
				$groupby = " GROUP BY ".$this->selectData['groupby'][0]['field'];
		}

		if(count($this->selectData['orderby']) > 0)
		{
			for($a = 0; $a < count($this->selectData['orderby']); $a++)
			{
				$sort[] = $this->selectData['orderby'][$a]['field'];
				if(!empty($this->selectData['orderby'][$a]['order']))
					$order[] = $this->selectData['orderby'][$a]['order'];
			}
		}

		// order by
		for($a=0; $a <= count($sort); $a++)
		{
			if(!empty($sort[$a]))
				$orderbyarray[] = $sort[$a]." ".$order[$a];
		}
		if($orderbyarray)
			$orderby .= " ORDER BY ".implode(", ",$orderbyarray);

		$query = "SELECT $fields FROM `".$this->tmp_table."` WHERE 1 ".$extraquery." ".$groupby." ".$orderby;
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		while($cdata = $DB->nextRecord("num"))
		{
			$this->excelData[] = $cdata;
		}

		$this->removeTmpTable();
	}


	function OutputCSV($filename, $type)
	{
		if($fp = fopen($filename,"w+"))
		{
			$csvdata = generateCSV($this->excelData);
			fwrite($fp,$csvdata);
			fclose($fp);
		}
		else
		{
			echo "Fout: kan niet schrijven naar ".$filename;
		}

	}

	function OutputXls($filename,$type="S")
	{
	  include_once('../classes/excel/Writer.php');

		$workbook = new Spreadsheet_Excel_Writer($filename);
    $worksheet =& $workbook->addWorksheet();
    $this->excelOpmaak['date']=array('setNumFormat'=>'DD-MM-YYYY');
    while(list($opmaakSleutel,$eigenschappen)=each($this->excelOpmaak))
    {
        $opmaak[$opmaakSleutel] =& $workbook->addFormat();
        while(list($eigenschap,$value)=each($eigenschappen))
        {
          $opmaak[$opmaakSleutel]->$eigenschap($value);
        }
    }

    if ($this->nullenOnderdrukken == 1)
	  {
	   $this->excelData =  $this->verwijderNulwaarden($this->excelData);
	  }

	   for($regel = 0; $regel < count($this->excelData); $regel++ )
	   {
		   for($col = 0; $col < count($this->excelData[$regel]); $col++)
		   {
		     if (is_array($this->excelData[$regel][$col]))
		     {
		       //$opmaak[$opmaakSleutel]
		       $celOpmaak = $this->excelData[$regel][$col][1]; //1=opmaak
		       $worksheet->write($regel, $col, $this->excelData[$regel][$col][0],$opmaak[$celOpmaak]);	//0=waarde
		     }
		     else
		     {
		       $waarde=$this->excelData[$regel][$col];
		       $worksheet->write($regel, $col, $waarde);
		     }
		   }
	   }

	   $workbook->close();
	}

	 	function fillXlsSheet($worksheet)
	{
    while(list($opmaakSleutel,$eigenschappen)=each($this->excelOpmaak))
    {
        $opmaak[$opmaakSleutel] =& $workbook->addFormat();
        while(list($eigenschap,$value)=each($eigenschappen))
        {
          $opmaak[$opmaakSleutel]->$eigenschap($value);
        }

    }

	   for($regel = 0; $regel < count($this->excelData); $regel++ )
	   {
		   for($col = 0; $col < count($this->excelData[$regel]); $col++)
		   {
		     if (is_array($this->excelData[$regel][$col]))
		     {
		       //$opmaak[$opmaakSleutel]
		       $celOpmaak = $this->excelData[$regel][$col][1]; //1=opmaak
		       $worksheet->write($regel, $col, $this->excelData[$regel][$col][0],$opmaak[$celOpmaak]);	//0=waarde
		     }
		     else
		     {
		       $worksheet->write($regel, $col, $this->excelData[$regel][$col]);
		     }
		   }
	   }
	}
}
?>

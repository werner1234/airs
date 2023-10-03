<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2014/03/12 10:02:21 $
 		File Versie					: $Revision: 1.12 $

 		$Log: controlePortefeuilles_readCVSFile.php,v $
 		Revision 1.12  2014/03/12 10:02:21  cvs
 		*** empty log message ***
 		
 		Revision 1.11  2012/05/22 15:19:52  cvs
 		controle op missen portefeuilles in bankbestand

 		Revision 1.10  2011/02/25 09:40:55  cvs
 		*** empty log message ***

 		Revision 1.9  2011/01/19 14:37:56  cvs
 		*** empty log message ***

 		Revision 1.8  2009/01/20 17:46:01  rvv
 		*** empty log message ***

 		Revision 1.7  2006/11/03 11:22:35  rvv
 		Na user update

 		Revision 1.6  2006/10/31 11:53:19  rvv
 		Voor user update.

 		Revision 1.5  2005/12/19 16:31:01  cvs
 		*** empty log message ***

 		Revision 1.4  2005/10/11 09:54:05  cvs
 		veld StroeveCode en port controle op stroevecode

 		Revision 1.3  2005/09/28 14:38:36  cvs
 		liquiditeiten fout ivm termijnrekeningen

 		Revision 1.2  2005/09/21 09:39:39  cvs
 		indexfout verwijderd en verwijderen tijdelijke bestanden

 		Revision 1.1  2005/09/09 14:51:11  cvs
 		aanpassing tbv inlezen gilissen



 		functie in controlePortefeuilles.php
*/
include_once('../classes/AE_cls_TMPdb.php');


function InsertAIRSsection($portefeuilleInCsv)
{
  global $datum,$DB1,$outputArray,$verlopenPortefeuille,$__appvar,$vermogenbeheerderFound;

  // kijk of portefeuille is verlopen
  $query = "SELECT Einddatum, Portefeuille, Vermogensbeheerder FROM Portefeuilles WHERE  Portefeuille = '".$portefeuilleInCsv."' AND  Depotbank = 'TGB' AND Einddatum > NOW()";
  $DB1->SQL($query);
	if ($portRec = $DB1->lookupRecord()) //
	{
	  //
    $vermogenbeheerderFound = $portRec['Vermogensbeheerder'];
	  $fondswaarden =  berekenPortefeuilleWaardeQuick($portefeuilleInCsv, $datum);

    if(count($fondswaarden) > 0 )
	  {
	    vulTijdelijkeTabel($fondswaarden ,$portefeuilleInCsv,$datum);
	    $query = 	"
	  	  SELECT
           TijdelijkeRapportage.fondsOmschrijving,
           TijdelijkeRapportage.actueleValuta ,
           TijdelijkeRapportage.rekening ,
           TijdelijkeRapportage.type ,
           TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
           TijdelijkeRapportage.totaalAantal,
           TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille
         FROM
           TijdelijkeRapportage
         WHERE
           TijdelijkeRapportage.portefeuille = '$portefeuilleInCsv' AND
           TijdelijkeRapportage.rapportageDatum = '$datum'
           ".$__appvar['TijdelijkeRapportageMaakUniek']."
         ORDER BY TijdelijkeRapportage.valuta asc";

	    debugSpecial($query,__FILE__,__LINE__);
	    $DB1->SQL($query);
	    $DB1->Query();

	    $idx = 0;
	    while ($recordData = $DB1->nextRecord())
	    {

	      if ($recordData['type'] == "rekening")
	      {
	        //$outputArray[$portefeuilleInCsv][A][$idx][match] = 0;
	        $outputArray[$portefeuilleInCsv][A][$idx]['aantal']       = getAIRSvaluta(trim($recordData['rekening']));
	        $outputArray[$portefeuilleInCsv][A][$idx]['fonds']        = "Liquiditeiten ".$recordData['rekening'];
	        $outputArray[$portefeuilleInCsv][A][$idx]['rekening']     = $recordData['rekening'];
          $outputArray[$portefeuilleInCsv][A][$idx]['portefeuille'] = trim($recordData['portefeuille']);
	      }
	      else
	      {
	        //$outputArray[$portefeuilleInCsv][A][$idx][match] = 0;
	        $outputArray[$portefeuilleInCsv][A][$idx]['aantal']       = $recordData['totaalAantal'];
	        $outputArray[$portefeuilleInCsv][A][$idx]['fonds']        = $recordData['fondsOmschrijving'];
	        $outputArray[$portefeuilleInCsv][A][$idx]['portefeuille'] = trim($recordData['portefeuille']);
	        $outputArray[$portefeuilleInCsv][A][$idx]['valuta']       = trim($recordData['valuta']);
	      }
	      $idx++;
	    }
	  }
	}
	else
	  $verlopenPortefeuille[] = $portefeuilleInCsv;  // push waarde in de te negeren portefeuilles
}

function getAIRSvaluta($rekeningnr)
{
  global $datum;
  $tmpDB = New DB();
 $query = "
SELECT Rekeningen.Valuta, SUM(Rekeningmutaties.Bedrag) as totaal
FROM Rekeningmutaties, Rekeningen
WHERE
	Rekeningmutaties.Rekening = Rekeningen.Rekening AND

	Rekeningmutaties.boekdatum >= '".substr($datum,0,4)."' AND
  Rekeningmutaties.Rekening = '".$rekeningnr."' AND
	Rekeningmutaties.boekdatum <= '".$datum."'
GROUP BY Rekeningen.Valuta
ORDER BY Rekeningen.Valuta";


   $tmpDB->SQL($query);
  if( $data = $tmpDB->lookupRecord())
    return $data[totaal];
  else
    return "Geen AIRS info";
}





function readCvsFile($filename)
{


	global $ndx, $csvRegels,$prb,$outputArray,$error,$DB,$DB1,$datum,$portefeuilleArray,$tijd, $USR;
  global $vermogenbeheerderFound;

  // maak tijdelijke tabel aan.
  $tb = new tempDB;
  $tempTableName = "___TMP_PC_".$USR;
  $tb->setTableName($tempTableName);
  $tb->addTableField("isin","varchar(20)");
  $tb->addTableField("fout","varchar(60)");
  $tb->createTable();
  $dbT = new db();   // object tbv TMP table

	$start = mktime();
	$error = array();
	if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
	$csvRegels = Count(file($filename));

  $pro_multiplier = 100/$csvRegels;
  $row = 0;
  $ndx= 0;
  $vermogenbeheerderFound = "";
  $prb->setLabelValue('txt1','inlezen van CSV bestand ('.$csvRegels.' records)');
  while ($data = fgetcsv($handle, 1000, ";"))
  {
    if (!is_numeric($data[0])) continue;  // sla lege regels over

    $row++;

  	$pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);
    $rowValues = array();
    $rowValues["bron"]   = "bank";
    $rowValues["aantal"] = trim($data[7]);
    $_portefeuille = trim($data[0]);

    if (trim($data[3]) == "")
    {
      $_portefeuille = trim($data[0]);
      $query = "SELECT Portefeuille FROM Portefeuilles WHERE  Portefeuille = '".trim($data[0])."'";
     	$DB->SQL($query);
     	$DB->Query();
     	if (!$_temp = $DB->nextRecord())  // rekeningnr bestaat niet als port.
      {
        $query = "SELECT Rekeningen.Rekening, Rekeningen.Portefeuille, Portefeuilles.Depotbank
                 FROM Rekeningen Inner Join Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
                 WHERE Rekening = '".trim($data[0]).trim($data[2])."'";

        $DB->SQL($query);
     	  $DB->Query();
     	  if (!$_temp = $DB->nextRecord())  // geen portefeuille bij rekeningnr
        {
          $_portefeuille = "FOUT";
          $rowValues["fout"] = "GEEN PORTEFEUILLE ".trim($data[0]).trim($data[2]);
        }
        else
          $_portefeuille = $_temp["Portefeuille"];
        if ($_temp["Depotbank"] <> "TGB") continue;
      }

      $bankRekening = trim($data[0]).trim($data[2]);
     	$rowValues["rekeningnr"]   = $bankRekening;
      $rowValues["fonds"]        = "Liquiditeiten $bankRekening";
      $rowValues["portefeuille"] = $_portefeuille;
    }
    else
    {

      $rowValues["portefeuille"] = $_portefeuille;
    	if ($_GET['bank'] == "stroeveEigen")
    	  $_isin = trim($data[12]);
    	else
    	  $_isin = trim($data[16]);

    	$rowValues["isin"] = $_isin;
  		if ( $_isin <> "")
    	{
    	  if ($_GET['bank'] == "stroeveEigen")
    	  {
    	    $fcode = "stroeveCode";
    	  }
    	  else
    	  {
    	    $fcode = "ISINCode";
    	  }
   	 		$query = "SELECT * FROM Fondsen WHERE $fcode = '".$_isin."' LIMIT 1 ";
     		$DB->SQL($query);
     		$DB->Query();
     		if (!$fonds = $DB->nextRecord())
     			$rowValues["fonds"] = "$fcode code komt niet voor fonds tabel ($_isin)";
     		else
     		{
     	  	$rowValues["fonds"] = addslashes($fonds[Omschrijving]);
     		}

    	}
      else
      {
      	$rowValues["fonds"] = "Geen $fcode code bij ".trim($data[3]).", regel ".($ndx+1);
      }

    }

    $TMPquery = " INSERT INTO $tempTableName SET ";
    foreach ($rowValues as  $key => $value)
    {
      $TMPquery .= "`$key` = '$value' ,";
    }
    $TMPquery = substr($TMPquery,0, -1);  // strip laatste komma
    $dbT->executeQuery($TMPquery);

  	$ndx++;
  }

  $TMPquery = "SELECT id FROM $tempTableName WHERE portefeuille <> 'FOUT'";
  $dbT->executeQuery($TMPquery);
  $TMPrecords = $dbT->records();

  $prb->setLabelValue('txt1','verzamelen AIRS en BANK gegevens('.$TMPrecords.' records)');
  $pro_multiplier = 100/$TMPrecords;
  $pro_step = 0;

  $TMPquery = "SELECT portefeuille FROM $tempTableName WHERE portefeuille <> 'FOUT' GROUP BY portefeuille ORDER BY portefeuille";
  $dbT->executeQuery($TMPquery);
  $dbT1 = new db();
  while ($TMPrec = $dbT->nextrecord())
  {
    $idx = 0;
    $portefeuille = $TMPrec["portefeuille"];
    $portefeuilleArray[] = $portefeuille;
    $pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);
    $TMPquery = "SELECT * FROM $tempTableName WHERE portefeuille = '$portefeuille' ";
    $dbT1->executeQuery($TMPquery);
    while ($TMPdata = $dbT1->nextrecord())
    {
      $outputArray[$portefeuille]['B'][$idx]['aantal']       = $TMPdata['aantal'];
      $outputArray[$portefeuille]['B'][$idx]['fonds']        = $TMPdata['fonds'];
	    $outputArray[$portefeuille]['B'][$idx]['rekening']     = $TMPdata['rekeningnr'];
      $outputArray[$portefeuille]['B'][$idx]['portefeuille'] = trim($TMPdata['portefeuille']);
      $idx++;
    }
    InsertAIRSsection($portefeuille);
  }

//listarray($outputArray);

  fclose($handle);
  $prb->hide();
  $tijd = mktime() - $start;
  unlink($filename);
  if (Count($error) == 0)
  	return true;
  else
  	return false;

}
?>
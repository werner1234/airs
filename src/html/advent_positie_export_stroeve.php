<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2013/12/11 10:06:26 $
 		File Versie					: $Revision: 1.1 $

 		$Log: advent_positie_export_stroeve.php,v $
 		Revision 1.1  2013/12/11 10:06:26  cvs
 		*** empty log message ***
 		
 
 		functie in controlePortefeuilles.php
*/
include_once('../classes/AE_cls_TMPdb.php');


function readStroeve($filename)
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
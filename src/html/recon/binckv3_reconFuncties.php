<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/04/12 08:03:51 $
 		File Versie					: $Revision: 1.1 $

 		$Log: binckv3_reconFuncties.php,v $
 		Revision 1.1  2019/04/12 08:03:51  cvs
 		call 7712
 		
 		Revision 1.12  2018/03/28 12:37:00  cvs
 		call 3503
 		
 		Revision 1.11  2017/10/20 08:36:34  cvs
 		call 6276
 		
 		Revision 1.10  2017/04/03 12:04:41  cvs
 		no message
 		
 		Revision 1.9  2016/07/22 12:03:34  cvs
 		binckcode optie wildcard % stond verkeerd
 		
 		Revision 1.8  2016/05/11 14:08:49  cvs
 		call 4907
 		
 		Revision 1.7  2015/12/01 09:03:15  cvs
 		*** empty log message ***
 		
 		Revision 1.6  2015/04/21 13:32:04  cvs
 		*** empty log message ***
 		
 		Revision 1.5  2015/03/26 09:47:00  cvs
 		*** empty log message ***
 		
 		Revision 1.4  2015/03/16 12:40:16  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2014/11/13 12:33:01  cvs
 		dbs 3118
 		
 		Revision 1.2  2014/10/01 13:38:44  cvs
 		meenemen opties
 		
 		Revision 1.1  2014/08/06 12:34:09  cvs
 		*** empty log message ***
 		
*/

function binck_recon_readBank($filename)
{
  global $prb, $batch, $recon, $airsOnly, $cronRun;

  $verbose = !$cronRun;

  $db = new DB();

  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";

    return false;
  }
  $csvRegels = Count(file($filename));

  $dbFonds = new DB();

  $pro_multiplier = 100 / $csvRegels;
  $row = 0;
  $ndx = 0;

  //$prb->show();
  while ($data = fgetcsv($handle, 1000, ";"))
  {
    //listarray($data);
    $data[0] = stripBOM($data[0]);
    if (!is_numeric(trim($data[0])))
    {
      continue;
    }  // sla lege regels over
    if (trim($data[4]) == "DIV")
    {
      continue;
    }  // DIV boekingen overslaan  2008-09-26

    ////////////////////////////////////////////
    $teller++;
    $pro_step += $pro_multiplier;

    $row = $data;
    $record = array("depot" => "b", "batch" => $batch . "/" . $teller);  // reset $record per ingelezen regel

    $portefeuille = trim($data[0]);
    $rekeninnr = trim($data[0]);
    $valuta = trim($data[2]); // let op alleen bij geld rekeningen
    $aantal = binckGetal($data[9]);
    $isin = trim($data[3]);
    $binck = $data[16];

    $record["bankCode"] = $binck;

    if (trim($data[15]) == "")  // cash
    {
      $record["type"] = "cash";
      $record["rekening"] = $rekeninnr;
      $record["datum1"] = "";
      $record["valuta"] = $valuta;
      $record["bedragRaw"] = binckGetal($aantal);
      $record["DC"] = ($aantal >= 0)?"D":"C";
      $record["datum2"] = "";
      $record["iban"] = $rekeninnr;
      $record["bedrag"] = binckGetal($aantal);
    }
    else
    {


      $record["fonds"] = $data[17];
      if ($data[4] == "CALL" OR $data[4] == "PUT" OR $data[4] == "FUT")  // opties
      {
        if ($data[4] == "FUT")
        {
          $split = explode(" ", $data[17]);

          $end = count($split);
          $binckCode = $split[0] . " %" . $split[$end - 2] . " " . $split[$end - 1];
        }
        else
        {
          $split = explode(" ", $data[17]);

          $end = count($split);
          $binckCode = $split[0] . " %" . $split[$end - 4] . " " . $split[$end - 3] . " " . $split[$end - 2] . " " . $split[$end - 1];
        }


        $q = "SELECT * FROM Fondsen WHERE binckCode LIKE '" . $binckCode . "' ";
//        debug($q);
        if ($fRec = $db->lookupRecordByQuery($q))
        {
          $record["bankCode"] = $fRec['binckCode'];
          $record["fonds"] = $fRec['Fonds'];
        }
        else
        {
          $record["bankCode"] = "";
        }

      }
      else // aandelen etc
      {

        // eerst AIRS fondscode ophalen

        if ($binck <> "")
        {
          $q = "SELECT * FROM Fondsen WHERE binckCode='$binck' ";
          if ($fRec = $db->lookupRecordByQuery($q))
          {
            $record["fonds"] = $fRec['Fonds'];
          }
        }
        else
        {
          $q = "SELECT * FROM Fondsen WHERE ISINCode='$isin' AND Valuta ='" . $valuta . "'";
          if ($fRec = $db->lookupRecordByQuery($q) AND $isin <> "")
          {
            $record["fonds"] = $fRec['Fonds'];
          }

        }
      }
      $record["type"] = "sec";
      $record["portefeuille"] = $portefeuille;
      $record["datum1"] = "";
      $record["datum2"] = "";
      $record["soort"] = "";
      $record["aantalRaw"] = $aantal;
      $record["aantal"] = $aantal;
      $record["ISIN"] = $isin;


      $record["PE"] = "";
      $record["valuta"] = trim($data[7]);
      $record["koersRaw"] = $data[8];
      $record["koers"] = $data[8];
    }
    $record["batch"] = $batch;
    $output[] = $record;

    $recon->addRecord($record);


  }

  if ($verbose)
  {
    echo "<li>AIRS data ophalen";
    ob_flush();
    flush();
  }
  $recon->fillTableFormAIRS();

  if ($verbose)
  {
    echo "<li>AIRS portefeuilles ophalen";
    ob_flush();
    flush();
  }
  if ($recon->AirsVerwerkingIntern)
  {
    $airsOnly = $recon->getAirsPortefeuilles();
  }

  if ($verbose)
  {
    echo "<li>AIRS rekeningnummers ophalen";
    ob_flush();flush();
  }
  if ($recon->AirsVerwerkingIntern)
  {
    $airsOnly = $recon->getAirsCashRekeningen();
  }
  
  $recon->fillVB();

  if ($verbose)
  {
    $prb->hide();
  }

  unlink($filename);
  return $teller;
}

function binckGetal($in)
{
  return str_replace(",", ".", trim($in));
}

function binck_validateFile($filename)
{   
  global $error, $filetype;
  $error = array();
  if (!$handle = @fopen($filename, "r"))
  {
	$error[] = "FOUT bestand $filename is niet leesbaar";
	return false;
  }

  $data = fgetcsv($handle, 2048, ";");
  $data[0] = stripBOM($data[0]);

  $validateStr1 = is_numeric($data[0]);     // portefeuille veld is numeriek
  $validateStr2 = (substr($data[1],0,2) );  // datumveld begin met 20
  $validateStr3 = (strlen($data[2]) >= 3);  // valutaveld gevuld

  if ( $validateStr1 AND $validateStr2 AND $validateStr3)
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen Binck bestand";
  }

    

  fclose($handle);
  
  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
} 		

?>
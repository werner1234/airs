<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/01/15 13:42:23 $
 		File Versie					: $Revision: 1.5 $

 		$Log: kbc_reconFuncties.php,v $
 		Revision 1.5  2020/01/15 13:42:23  cvs
 		call 8152
 		
 		Revision 1.4  2019/10/30 15:21:45  cvs
 		call 8152
 		
 		Revision 1.3  2019/10/21 07:19:27  cvs
 		call 8152
 		
 		Revision 1.1  2019/10/07 07:59:28  cvs
 		call 8152
 		
 		Revision 1.4  2019/03/22 08:21:48  cvs
 		call 7642
 		
 		Revision 1.3  2018/07/20 07:24:03  cvs
 		no message
 		
 		Revision 1.2  2018/06/12 12:58:34  cvs
 		alleen "-" in prodsuctcode van CASH rekening meenemen in de recon
 		
 		Revision 1.1  2018/05/30 14:44:28  cvs
 		call 6851
 		
 		Revision 1.3  2017/04/03 11:54:47  cvs
 		no message
 		
 		Revision 1.2  2015/12/01 09:03:15  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2015/06/22 08:05:49  cvs
 		*** empty log message ***
 		

 		
*/

function recon_readBank($filename)
{
  global $prb, $batch, $recon, $airsOnly,$error, $cronRun, $set;
  $ontdubbelArray = array();
  
  if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
  $csvRegels = Count(file($filename));
//  $prb->max = 100;
//  $prb->addLabel('text','txt1','Inlezen bankbestand, '.count($rawData).' regels');	// add Text as Label 'txt1' and value 'Please wait'
//  $prb->moveMin();
  $pro_multiplier = 100/$csvRegels;
  //$prb->show();	
  $teller = 0;
  
  $tmpGeldArray = array();
  
  
  

  while ($data = fgetcsv($handle, 4096, "\t"))
  {
    $pro_step += $pro_multiplier;
    $teller++;
    if($data[0] == "") { continue; } // skip empty rows
    if(substr(strtolower(implode(";", $data)), 0, strlen($set["headerRow"])) == strtolower($set["headerRow"])){ continue; }//skip header rows

    // int db record
    $record = array("depot" => "Knox", "batch" => $batch . "/" . $teller);  // reset $record per ingelezen regel

    /*
    0 PortfolioNumber
    1 ISINCode
    2 AccountNumber
    3 ProductOrAccountCCY
    4 InternalProductId
    5 NumberOfSharesCashBalance
    6 Price
    7 PriceDate
    */
    $transactionType    = "sec";
    $portefeuille       = trim($data[0]);
    $rekeningNummer     = trim($data[2]);

    if(strtoupper(substr($rekeningNummer, -1)) == "C")
    {
      $transactionType = "cash";
    }

    // portefeuille strip S suffix portefeuille // if(strtoupper(substr($portefeuille, -1) == "S")) {$portefeuille  = substr($portefeuille, 0, strlen($portefeuille) - 1); }
    // AccountNumber strip suffix ending with C
    // strip last char rekeningnummer $rekeningNummer  = substr($rekeningNummer, 0, strlen($rekeningNummer) - 1);


    $record["type"]         = $transactionType;
    $record["portefeuille"] = $portefeuille;
    $record["rekening"]     = $rekeningNummer;
    $record["datum1"]       = $recon->testDate;
    $record["datum2"]       = $recon->testDate;
    $record["valuta"]       = $data[3];
    $record["batch"]        = $batch;
    $record["soort"]        = "";

    if ($transactionType == "cash")
    {
      $record["iban"]         = "";
      $record["page"]         = "";
      $record["bedragRaw"]    = $data[5];
      $record["bedrag"]       = $data[5];

    }
    else
    {
      $record["aantalRaw"]    = $data[5];
      $record["aantal"]       = $data[5];
      $record["ISIN"]         = trim($data[1]);
      $record["bankCode"]     = $data[4];
      $record["PE"]           = 1;

      $db = new DB();
      $query = "SELECT * FROM Fondsen WHERE KNOXcode LIKE '{$record["bankCode"]}' ";

      if ($fRec = $db->lookupRecordByQuery($query))
      {
        $record["fonds"]    = $fRec['Fonds'];
      }

    }
    $output[] = $record;
    $recon->addRecord($record);

  }

  if (!$cronRun)
  {
    echo "<li>AIRS data ophalen";
    ob_flush();
    flush();
  }
  $recon->fillTableFormAIRS();

  if (!$cronRun)
  {
    echo "<li>AIRS portefeuilles ophalen";
    ob_flush();
    flush();
  }
  if ($recon->AirsVerwerkingIntern)
  {
    $airsOnly = $recon->getAirsPortefeuilles();
  }

  if (!$cronRun)
  {
    echo "<li>AIRS rekeningnummers ophalen";
    ob_flush();
    flush();
  }
  if ($recon->AirsVerwerkingIntern)
  {
    if ($transactionType == "cash")
    {
      $airsOnly = $recon->getAirsCashRekeningen();
    }
  }

  
  $recon->fillVB();
  
  //$prb->hide();  
  
  unlink($filename);
  return $teller;
}

function validateFile($filename)
{   
  global $error, $cronRun, $set;
  $err = array();

  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT positiebestand $filename is niet leesbaar";
    return false;
  }

  // skip header rows, check file format after
  while($data = fgetcsv($handle, 4096, "\t"))
  {
    if($data[0] == "") { continue; } // skip empty rows
    if(substr(strtolower(implode(";", $data)), 0, strlen($set["headerRow"])) == strtolower($set["headerRow"])){ continue; }//skip header rows
    break; // end of file or data row found
  }

  if(empty($data[0]))
  {
    $err[] = "FOUT geen data in KNOX positiebestand ";
  }

  // controleer eerste data regel
  if ((
    !empty($data[0])      and
    strlen($data[3]) == 3 and // ProductOrAccountCCY == valuta, 3 lang
    is_numeric($data[5])      // NumberOfSharesCashBalance is number
  ) == false )
  {
    $err[] = "FOUT geen KNOX positiebestand ";
  }

  fclose($handle);


  $error = array_merge($error, $err);

  if (Count($err) == 0)
  {
    return true;
  }
  else
  {
    return false;
  }
} 		




?>
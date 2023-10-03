<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/09/23 17:14:23 $
 		File Versie					: $Revision: 1.8 $

 		$Log: abn_reconFuncties.php,v $
 		Revision 1.8  2018/09/23 17:14:23  cvs
 		call 7175
 		
 		Revision 1.7  2018/08/12 09:32:47  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2015/12/01 09:03:15  cvs
 		*** empty log message ***
 		
 		Revision 1.5  2015/04/21 13:32:04  cvs
 		*** empty log message ***
 		
 		Revision 1.4  2015/03/16 12:40:16  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2014/12/16 07:30:49  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2014/11/13 12:33:01  cvs
 		dbs 3118
 		
 		Revision 1.1  2014/11/13 10:46:04  cvs
 		dbs  3118
 		
 		Revision 1.1  2014/10/17 14:29:31  cvs
 		dbs 2745
 		
 		Revision 1.1  2014/08/06 12:34:09  cvs
 		*** empty log message ***
 		
*/


function recon_readBank($fileMT5, $fileMT9, $useISIN=false)
{
  global $prb, $batch, $recon, $airsOnly, $cronRun;
  
  
  $db = new DB();
  
	$csvRegels = Count(file($fileMT5));
  $handle = @fopen($fileMT5, "r");
  $pro_multiplier = 100/$csvRegels;
  $row = 0;
  $ndx= 0;

  //$prb->setLabelValue('txt1','inlezen van MT5XX bestand ('.$csvRegels.' regels)');
  //$prb->step = 0;
  //$prb->show();
  if (!$cronRun)
  {
    echo "<li>inlezen MT5XX bankbestand";
    ob_flush();flush();
  }

  $bankOutput = array();
  while ($data = fgets($handle, 4096))
  {
    if ($data[0] == " ") $data = substr($data,1);  // als eerste char een spatie deze wegknippen

    $regtel++;
    if (!$cronRun)
    {
      $prb->moveNext();
    }
    $skipToNextRecord = false;
    switch (trim($data))
    {
      case "ABNANL2A":
          //cycle
        break;
      case "500":
      case "501":
      case "510":
      case "554":
      case "940":  //type record
        $skipToNextRecord = true;
        break;
      case "571":
        $skipToNextRecord = false;
        $dataSet[$ndx][type] = $data;
        break;
      case "-":  // einde record
        $skipToNextRecord = false;
        $ndx++;
        break;
      default:
        if ($skipToNextRecord == true OR !isset($dataSet[$ndx][type]))
          break;
        if (substr($data,0,1) <> ":")
        {
          $dataSet[$ndx][txt] = substr($dataSet[$ndx][txt],0,-1)." ".$data;
        }
        else
        {
          $_regel = explode(":",$data);
          $_prevKey = $_regel[1];
          $dataSet[$ndx][txt] .= $_regel[1]."&&".$_regel[2];  // vul data velden
        }
        break;
     }

  }

  $dataSet571 = $dataSet;

	$handle = @fopen($fileMT9, "r");
	$dataSet = array();
  $csvRegels = Count(file($fileMT9));

  $pro_multiplier = 100/$csvRegels;
  $row = 0;
  $ndx= 0;
  $regtel = 0;
  if (!$cronRun)
  {
    echo "<li>inlezen MT940 bankbestand";
    ob_flush();
    flush();
  }
  //$prb->setLabelValue('txt1','inlezen van MT940 bestand ('.$csvRegels.' regels)');
  //$prb->step = 0;
  $bankOutput = array();
  while ($data = fgets($handle, 4096))
  {
    if ($data[0] == " ") $data = substr($data,1);  // als eerste char een spatie deze wegknippen
	$regtel++;
	//$prb->moveNext();
	$skipToNextRecord = false;
	switch (trim($data))
  {
   	case "ABNANL2A":
        //cycle
   		break;
   	case "500":
   	case "501":
   	case "510":
   	case "554":
 	  case "571":
 	    $geent940++;
   	  $skipToNextRecord = true;
   	  break;
   	case "940":  //type record
   	  $t940++;
   	  $skipToNextRecord = false;
   	  $dataSet[$ndx][type] = $data;
 			break;
   	case "-":  // einde record
   	  $skipToNextRecord = false;
      $ndx++;
 			break;
  	default:
  	  if ($skipToNextRecord == true OR !isset($dataSet[$ndx][type]))
  	    break;
  	  if (substr($data,0,1) <> ":")
   	  {
   	    $dataSet[$ndx][txt] = substr($dataSet[$ndx][txt],0,-1)." ".$data;
   	  }
   	  else
   	  {
   	  	$_regel = explode(":",$data);
   	  	$_prevKey = $_regel[1];
   	  	$dataSet[$ndx][txt] .= $_regel[1]."&&".$_regel[2];  // vul data velden
   	  }
   		break;
   }

  }

  $dataSet940 = $dataSet;
  unset($dataSet);
  unlink($fileMT5);
  unlink($fileMT9);

  //debug($dataSet571);
  //debug($dataSet940);

  reset($dataSet940);
  $ndx = 0;

  $dataSetSize = count($dataSet940);
  $pro_multiplier = 100/$dataSetSize;
  $row = 0;
  $ndx= 0;

  foreach ($dataSet940 as $dataRec)
  {
    $data = convertMt940($dataRec);
    $data = $data[0];
    $record = array("depot" => "AAB");  // reset $record per ingelezen regel
    $record["type"]      = "cash";
    $record["rekening"]  = $data["rekeningnr"];
    $record["datum1"]    = "";
    $record["valuta"]    = $data["valuta"];
    $record["bedragRaw"] = $data["bedrag"];
    $record["DC"]        = ($data["bedrag"] >= 0)?"D":"C";
    $record["datum2"]    = "";
    $record["iban"]      = $data["rekeningnr"];
    $record["bedrag"]    = str_replace(",",".",$data["bedrag"]);
    $record["batch"]   = $batch;  
    $output[] = $record;
    $recon->addRecord($record);
  }
  
  unset($dataSet940); 
  $db = new DB();
  
  $csvRegels = count($dataSet571);
  $pro_multiplier = 100/$csvRegels;
  $pro_step = 0;
  $teller = 0;
  if (!$cronRun)
  {
    echo '<li>converteren van MT571 records (' . $csvRegels . ' records)';
    ob_flush();
    flush();
  }
  //$prb->setLabelValue('txt1','converteren van MT571 records ('.$csvRegels.' records)');
  reset($dataSet571);
  foreach ($dataSet571 as $data)
  {

    $rec571 = convertMt571($data);
    
    for ($ndx=0; $ndx < count($rec571); $ndx++)
    {
      $data = $rec571[$ndx];
   
      $teller++;
      $pro_step += $pro_multiplier;
      //$prb->moveStep($pro_step);
     // $prb->setLabelValue('txt1','Inlezen bankbestand, '.$teller." / ".$csvRegels.' regels');
      //$prb->moveNext();
   
      if (trim($data["AABcode"]) <> "")
      {
        $q = "SELECT * FROM Fondsen WHERE AABCode='".trim($data["AABcode"])."' OR ABRCode='".trim($data["AABcode"])."' ";
        
        $fondsRec = $db->lookupRecordByQuery($q);
      }
      if (trim($fondsRec["AABCode"])  <> "")
      {
        $bankcode = trim($fondsRec["AABCode"]);
        //$bankcode = trim($data["AABcode"]);  // geef de bankcode uit banfile terug.. 20160502
      }
      else
      {
        $bankcode = $data["AABcode"];
      }
      $record = array("depot" => "AAB");  // reset $record per ingelezen regel
      $record["type"]         = "sec";
      $record["portefeuille"] = $data["portefeuille"];
      $record["datum1"]       = "";
      $record["datum2"]       = "";
      $record["soort"]        = "";
      $record["aantalRaw"]    = $data["aantal"];
      $record["aantal"]       = $data["aantal"];
      $record["ISIN"]         = $fondsRec["ISINCode"];
      $record["bankCode"]     = $bankcode;
      $record["fonds"]        = $fondsRec["Fonds"];
      $record["PE"]           = "";
      $record["valuta"]       = $data["fondsValuta"];
      $record["batch"]        = $batch;
      $record["fBankCode"]    = $data["AABcode"];
      //$record["koers"]        = $data[11]/$data[8];
      $output[] = $record;
      $recon->addRecord($record);
   
    }
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
    ob_flush();flush();
  }

  $airsOnly = $recon->getAirsPortefeuilles();

  if (!$cronRun)
  {
    echo "<li>AIRS rekeningnummers ophalen";
    ob_flush();
    flush();
  }
  $airsOnly = $recon->getAirsCashRekeningen();
  
  $recon->fillVB();
  
  //$prb->hide();    
  return $teller;
}

function validateFile($filename,$filename2)
{   
  global $error, $cronRun;
  $error = array();
  if (!$cronRun)
  {
    echo "<li>start validatie bestanden";
    ob_flush();
    flush();
  }
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT MT5xx bestand $filename is niet leesbaar";
    return false;
  }

  $dataRaw = fgets($handle, 4096);
  $dataRaw2 = trim(fgets($handle, 4096));
  if (trim($dataRaw) == "ABNANL2A"  AND
      substr($dataRaw2,0,1) == "5"   ) 
  {
  }  
  else
  {
      $error[] = "Bestand validatie mislukt, geen MT5xx bestand";
  }
  fclose($handle);
  
  if (!$handle = @fopen($filename2, "r"))
  {
    $error[] = "FOUT MT940 bestand $filename is niet leesbaar";
  }

  $dataRaw = fgets($handle, 4096);
  $dataRaw2 = trim(fgets($handle, 4096));
  if (trim($dataRaw) == "ABNANL2A"  AND
      substr($dataRaw2,0,1) == "9"    )
  {
  }  
  else
  {
      $error[] = "Bestand validatie mislukt, geen MT940 bestand";
  }
  fclose($handle);

  return (Count($error) == 0);
} 		

function cnvBedrag($txt)
{
	return str_replace(',','.',$txt);
}

function convertMt571($record)
{
  $data = array();
  $dnx = 0;
  $_data = explode(chr(10),$record[txt]);
  //listarray($_data);
  $wr = array();
  $subRecord = 0;
  for ($subLoop = 0; $subLoop < count($_data);$subLoop++)
  {
    $_r = explode("&&",$_data[$subLoop]);

    switch ($_r[0])
    {
      case "83a":
        $wr[$subRecord][portefeuille] = intval($_r[1]);
        break;
      case "72":
        $subRecord++;
        $wr[$subRecord][portefeuille] = $wr[$subRecord-1][portefeuille];
        break;
      case "33B":
        $wr[$subRecord][fondsValuta] = substr($_r[1],0,3);
        break;
      case "35B":
        $wr[$subRecord][AABcode] = substr($_r[1],5,6);
        break;
      case "35H":
        if (substr($_r[1],0,1) == "N")
          $sign = -1;
        else
          $sign = 1;

        for($xx=0;$xx < strlen($_r[1]);$xx++)
        {
          $_l = 	substr($_r[1],$xx,1);
          if ($_l >= "0" AND $_l <= "9")
            $wr[$subRecord][aantal] .= $_l;
          elseif ($_l == ",")
            $wr[$subRecord][aantal] .= ".";
        }
        $wr[$subRecord][aantal] = $wr[$subRecord][aantal] * $sign;
        break;
    }
  }

  for ($ndx=0; $ndx < count($wr); $ndx++)  // ontdubbelen en aantallen optellen
  {
    $a = $wr[$ndx];
    $tmpWR[$a["portefeuille"]][$a["AABcode"]]["aantal"] += $a["aantal"];
    $tmpWR[$a["portefeuille"]][$a["AABcode"]]["fondsValuta"] = $a["fondsValuta"];
  }
  $teller = 0;
  $wr = array();

  foreach ($tmpWR as $portefeuille => $fondsArray) // oude Array layout
  {
   	 foreach ($fondsArray as $AABCode => $aantalArray)
   	 {
   	   $aantal = $aantalArray["aantal"];
       $fondsValuta = $aantalArray["fondsValuta"];
   	   if ($aantal <> 0)
   	   {
   	     $wr[] = array("portefeuille" => $portefeuille,
   	                   "aantal"       => $aantal,
   	                   "AABcode"      => $AABCode,
                       "fondsValuta"  => $fondsValuta);
   	   }
   	 }
  }
  //listarray($wr);
  return $wr;  // geeft arrayset met deelrecords terug

}

function convertMt940($record)
{
  $data = array();
  $dnx = 0;
  $_data = explode(chr(10),$record[txt]);
  $wr = array();
  $subRecord = 0;
  for ($subLoop = 0; $subLoop < count($_data);$subLoop++)
  {
    $_r = explode("&&",$_data[$subLoop]);
    $_tempRec[$_r[0]] = $_r[1];
    switch ($_r[0])
    {
      case "25":
        $wr[rekeningnr] = intval($_r[1]);
        break;
      case "62F":
        if (substr($_r[1],0,1) == "D")
          $sign = -1;
        else
          $sign = 1;

        $_tmp = substr($_r[1],10);
        $wr[bedrag]      = cnvBedrag($_tmp) * $sign;
        $wr[valuta]      = substr($_r[1],7,3);
        break;
    }
  }
  $data[$dnx] = $wr;
  return $data;  // geeft arrayset met de
}

function getRekeningNr($port,$valuta)
{
  $DB = new DB();
  $query = "SELECT Rekening FROM Rekeningen WHERE consolidatie=0 AND Portefeuille = '$port' AND Memoriaal = 0 AND Valuta='$valuta'";
  $DB->SQL($query);
  $record = $DB->lookupRecord();
  return $record["Rekening"];

}

?>
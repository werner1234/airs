<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/12 09:32:47 $
 		File Versie					: $Revision: 1.2 $

 		$Log: ubs_reconFuncties.php,v $
 		Revision 1.2  2018/08/12 09:32:47  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2017/09/20 06:20:23  cvs
 		megaupdate 2722
 		

 		
*/
function recon_readBank($filename, $filetype)
{
  global $prb, $batch, $recon, $airsOnly,$error;
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



  if ($filetype == "FND")
  {
    while ($data = fgetcsv($handle, 8192, ";"))
    {


      //debug($data);

      $teller++;
      if ($teller < 9)                             continue;  // headers overslaan
      if ($data[11] != "TRAD")                     continue;  // alleen de TRAD regels gebruiken

      $pro_step += $pro_multiplier;

      $record = array("depot" => "UBS","batch"=>$batch."/".$teller);  // reset $record per ingelezen regel

      $record["type"]         = "sec";
      $record["portefeuille"] = $data[14];
      $record["datum1"]       = $recon->testDate;
      $record["datum2"]       = $recon->testDate;
      $record["soort"]        = "";
      $record["aantalRaw"]    = $data[24];
      $record["aantal"]       = $data[24];
      $record["ISIN"]         = trim($data[19]);
      $record["valuta"]       = $data[54];
      $record["bankCode"]     = $data[22];
      $record["fonds"]        = $data[21];
      $record["PE"]           = 1;

      $record["koersRaw"]     = 0;
      $record["koers"]        = 0;

      $record["batch"] = $batch;
      $output[] = $record;

      $recon->addRecord($record);
    }

  }
  else
  {
    while ($data = fgetcsv($handle, 1000, ";"))
    {
      $teller++;
      if ($teller < 9)                             continue;  // headers overslaan

      $reknr = substr($data[2],0,14)."s1";
      $val = ($data[4] == "D")?-1*$data[7]:$data[7];

      $record = array("depot" => "UBS","batch"=>$batch."/".$teller);  // reset $record per ingelezen regel

      $record["type"]      = "cash";
        // $record["portefeuille"] = $reknr;
      $record["rekening"]  = $reknr;
      $record["datum1"]    = $recon->testDate;
      $record["valuta"]    = $data[6];
      $record["bedragRaw"] = $val;
      $record["DC"]        = ($val < 0)?"D":"C";
      $record["datum2"]    = $recon->testDate;
      $record["iban"]      = "";
      $record["page"]      = "";
      $record["bedrag"]    = $val;
      $record["batch"] = $batch;

      $output[] = $record;

      $recon->addRecord($record);

    }

  }



  if ($filetype == "FND")
  {
    echo "<li>AIRS data ophalen";
    ob_flush();flush();
    $recon->fillTableFormAIRS();


    echo "<li>AIRS portefeuilles ophalen";
    ob_flush();flush();
    $airsOnly = $recon->getAirsPortefeuilles();

  }
  else  // GLD
  {
    echo "<li>AIRS rekeningnummers ophalen";
    ob_flush();flush();
    foreach ($tmpGeldArray as $key => $values)  // extra loop om hoogste pagina te selecteren voor saldo call 3529
    {
      $tmpRecord = $values[0];                                       // vul temp rec met eerst gevonden record
      if (count($values) > 1 )                                       // als er meer dan 1 pagina is gevonden dan hoogste pagina gaan zoeken
      {
        for ($x=1; $x < count($values); $x++)
        {
          if ( (int) $values[$x]["page"] > (int) $tmpRecord["page"])
          {
            $tmpRecord = $values[$x];                               // als huidige pagenr > dan opgeslagen dan overschrijven
          }
        }
      }
      unset($tmpRecord["page"]);                                    // verwijder paginanr om SQL error te voorkomen
      $output[] = $tmpRecord;
      $recon->addRecord($tmpRecord);
    }

    $airsOnly = $recon->getAirsCashRekeningen();
  }

  $recon->fillVB();

  //$prb->hide();

  unlink($filename);
  return $teller;
}




function validateFile($filename,$filename2)
{   
  global $error;
  $error = array();
  echo "<li>start validatie bestanden";
  ob_flush();flush();
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT MT5xx bestand $filename is niet leesbaar";
    return false;
  }

  $dataRaw = array();
  for($x=0; $x < 5; $x++)
  {
    $dataRaw[] = trim(fgets($handle, 4096));
  }


  if (! trim($dataRaw[4]) == "AI535" )
  {
    $error[] = "Bestand validatie mislukt, geen AI535 bestand";
  }  
  fclose($handle);

  if (!$handle = @fopen($filename2, "r"))
  {
    $error[] = "FOUT MT940 bestand $filename is niet leesbaar";
  }
  $dataRaw = array();
  for($x=0; $x < 5; $x++)
  {
    $dataRaw[] = trim(fgets($handle, 4096));
  }

  if (!trim($dataRaw[4]) == "AI941" )
  {
      $error[] = "Bestand validatie mislukt, geen AI941 bestand";
  }
  fclose($handle);

  return (Count($error) == 0);
} 		

function cnvBedrag($txt)
{
	return str_replace(',','.',$txt);
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
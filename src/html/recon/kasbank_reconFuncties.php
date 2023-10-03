<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/09/23 17:14:23 $
 		File Versie					: $Revision: 1.10 $

 		$Log: kasbank_reconFuncties.php,v $
 		Revision 1.10  2018/09/23 17:14:23  cvs
 		call 7175
 		
 		Revision 1.9  2016/05/30 08:00:44  cvs
 		call 4848: derde bestand Kasbankl
 		
 		Revision 1.8  2015/12/01 09:03:15  cvs
 		*** empty log message ***
 		
 		Revision 1.7  2015/04/21 13:32:04  cvs
 		*** empty log message ***
 		
 		Revision 1.6  2015/03/26 09:47:00  cvs
 		*** empty log message ***
 		
 		Revision 1.5  2015/03/16 12:40:16  cvs
 		*** empty log message ***
 		
 		Revision 1.4  2014/12/16 07:30:49  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2014/11/13 12:33:01  cvs
 		dbs 3118
 		
 		Revision 1.2  2014/11/13 10:46:04  cvs
 		dbs  3118
 		
 		Revision 1.1  2014/08/06 12:34:09  cvs
 		*** empty log message ***
 		
*/

function recon_readBank($filename, $filetype, $skipGetAirs=false)
{
  global $prb, $batch, $recon, $airsOnly,$dubbelPos;
  $rawData = file($filename);
  
  $prb = new ProgressBar();	// create new ProgressBar
//  $prb->pedding = 2;	// Bar Pedding
//  $prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
//  $prb->setFrame();          	                // set ProgressBar Frame
//  $prb->bgr_color = "#F8F5AD";
//  $prb->color = "#808080";
//  $prb->frame['left'] = 50;	                  // Frame position from left
//  $prb->frame['top'] = 	80;	                  // Frame position from top
//  $prb->frame['width'] = 	550;	                  // Frame position from top
//  $prb->frame['heigth'] = 	100;	                  // Frame position from top
//  $prb->frame['color'] = 	"beige";	                  // Frame position from top
//  $prb->addLabel('text','txt1','Inlezen bankbestand ...');	// add Text as Label 'txt1' and value 'Please wait'
//  $prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
//  
//  $prb->max = 100;
//  $prb->addLabel('text','txt1','Inlezen bankbestand, '.count($rawData).' regels');	// add Text as Label 'txt1' and value 'Please wait'
//  $prb->moveMin();
//  $pro_multiplier = 100/count($rawData);
//  //$prb->show();	
  echo "<li>inlezen bankbestand";
  ob_flush();flush();
  $teller = 0;
  $prevRow = "";
  $dubbelPos = 0;
  for ($x=0; $x < count($rawData); $x++)
  {
    
    $teller++;
    $pro_step += $pro_multiplier;
  //  $prb->moveStep($pro_step);
  //  $prb->setLabelValue('txt1','Inlezen bankbestand, '.$teller." / ".count($rawData).' regels');
    
 //   $prb->moveNext();
    $row = $rawData[$x];
    $record = array("depot" => "kasbank","batch"=>$batch."/".$teller);  // reset $record per ingelezen regel
    if ($filetype == "FND")
    {

      $record["type"]         = "sec";
      $record["portefeuille"] = substr(trim(textPart($row,22,31)),-9);
      $record["datum1"]       = $recon->kasbankDateToDb(textPart($row,32,37) );
      $record["datum2"]       = $recon->kasbankDateToDb(textPart($row,38,43) );
      $record["soort"]        = textPart($row,44,50);
      $record["aantalRaw"]    = textPart($row,51,65);
      $record["aantal"]       = textPart($row,213,233)/1000000;
      //$record["aantal"]       = (int)textPart($row,213,233)/1000000;
      $record["ISIN"]         = textPart($row,66,77);
      $record["bankCode"]     = textPart($row,78,83);
      $record["fonds"]        = textPart($row,84,103);
      $record["PE"]           = textPart($row,104,104);
      $record["valuta"]       = textPart($row,105,107);
      $record["koersRaw"]     = textPart($row,108,117);
      $record["koers"]        = (int)textPart($row,108,117)/10000;
      
    } 
    elseif ($filetype == "POS")  // call 4848 derde bestand
    {
      if ($row == $prevRow)
      {
        $dubbelPos++;
        continue;  // dubbele regels negeren
      }
      $data = convertRow($row);


      $record["type"]         = "sec";
      $record["portefeuille"] = $data[4];
      $record["datum1"]       = substr($data[1],0,4)."-".substr($data[1],4,2)."-".substr($data[1],6,2);
      //$record["datum2"]       = $recon->kasbankDateToDb(textPart($row,38,43) );
      $record["soort"]        = $data[8]; //todo: wat is soort??
      $record["aantalRaw"]    = $data["aantal"];
      $record["aantal"]       = $data["aantal"];
      //$record["aantal"]       = (int)textPart($row,213,233)/1000000;
      $record["airsCode"]     = $data["fonds"];
      $record["fonds"]        = $data["fonds"];
      $record["bankCode"]     = $data["fonds"];
      //$record["PE"]           = ""; //todo: wat is PE??
      $record["valuta"]       = $data[12];
      $record["koersRaw"]     = $data[22];
      $record["koers"]        = $data[22]/100;

//      debug($record);
      $prevRow = $row;
      
    }
    else  // GLD
    {
      $record["type"]      = "cash";
      $record["rekening"]  = (int)textPart($row,33,42);
      $record["datum1"]    = $recon->kasbankDateToDb(textPart($row,123,128) );
      $record["valuta"]    = textPart($row,129,131);
      $record["bedragRaw"] = textPart($row,132,148);
      $record["DC"]        = (textPart($row,149,149) == "1")?"D":"C";
      $record["datum2"]    = $recon->kasbankDateToDb(textPart($row,150,155) );
      $record["iban"]      = trim(textPart($row,203,250));
      $factor = $record["DC"] == "C"?-1:1; 
      $record["bedrag"]    = $factor * (int)$record["bedragRaw"]/100;
    }
    
    $record["batch"] = $batch;

    $output[] = $record;
  
    $recon->addRecord($record);
    
    
  }
  if ($filetype <> "GLD")
  {
    echo "<li>AIRS data ophalen";
    ob_flush();flush();
    if (!$skipGetAirs)
    {
      $recon->fillTableFormAIRS();
      echo "<li>AIRS portefeuilles ophalen ($filetype)";
      ob_flush();flush();
      $airsOnly = $recon->getAirsPortefeuilles();
    }
  
  }
  else  // GLD
  {
    echo "<li>AIRS rekeningnummers ophalen";
    ob_flush();flush();
    $airsOnly = $recon->getAirsCashRekeningen();
  }
  
  $recon->fillVB();
  
  //$prb->hide();    
  unlink($filename);
  return $teller;
}

function convertRow($rawData)
{
  $maanden = array("", "Jan", "Feb", "Mrt", "Apr", "Mei", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dec");
  //$data 7/8/9/10 herleiden tot AIRS fondscode
  // bv AP C jun16 35

  $data = array();
  $data[1]   = textPart($rawData,1,8);    // datum
  $data[2]   = textPart($rawData,9,11);
  $data[3]   = textPart($rawData,12,13);
  $data[4]   = textPart($rawData,14,22);  // portefeuille
  $data[5]   = textPart($rawData,23,28);
  $data[6]   = textPart($rawData,29,30);
  $data[7]   = textPart($rawData,31,36);  // symbool  fonds.fonds eerste deel tot spatie
  $data[8]   = textPart($rawData,37,37);  // call/put
  $data[9]   = textPart($rawData,38,45);  // exp datum
  $data[10]  = textPart($rawData,46,52);  // strike  delen door 100
  $data[11]  = textPart($rawData,53,53);
  $data[12]  = textPart($rawData,54,56);  // valuta
  $data[13]  = textPart($rawData,57,64);
  $data[14]  = textPart($rawData,65,69);  // long
  $data[15]  = textPart($rawData,70,70);
  $data[16]  = textPart($rawData,71,75);
  $data[17]  = textPart($rawData,76,76);
  $data[18]  = textPart($rawData,77,81);  // short
  $data[19]  = textPart($rawData,82,82);
  $data[20]  = textPart($rawData,83,87);
  $data[21]  = textPart($rawData,88,88);
  $data[22]  = textPart($rawData,89,96);  // prijs
  $data[23]  = textPart($rawData,97,97);
  $data[24]  = textPart($rawData,98,105); // settlement koers
  $data[25]  = textPart($rawData,106,106);
  $data[26]  = textPart($rawData,107,114);
  $data[27]  = textPart($rawData,115,121);
  $data[28]  = textPart($rawData,122,127);
  $data[29]  = textPart($rawData,128,128);
  $data[30]  = textPart($rawData,129,133);
  $data[31]  = textPart($rawData,134,141);
  $data[32]  = textPart($rawData,142,142);
  $data[33]  = textPart($rawData,143,150);
  $m =  substr($data[9],4,2);
  $y = substr($data[9],2,2);

  $timeCode = $maanden[ (int)$m ].$y;
  $price = (int)$data[10];
  $price = $price/100;
  $price = strstr($price,".")?number_format($price,2):$price;

  $data["fonds"] = $data[7]." ".$data[8]." ".$timeCode." ".$price;
  $data["aantal"] = $data[14] - $data[18];


  return $data;
}


function validateFile($filename,$filename2)
{   
  global $error, $filetype;
  $error = array();
  echo "<li>start validatie bestanden";
  ob_flush();flush();

  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }

  $dataRaw = fgets($handle, 4096);
  $validateStr1 = textPart($dataRaw,21,21);
  
    if ( !$validateStr1 == "A")
  {
    $error[] = "Bestand validatie mislukt, geen Kasbank FND bestand";
    return false;
  }  
  fclose($handle);
  
  if (!$handle = @fopen($filename2, "r"))
  {
    $error[] = "FOUT bestand $filename2 is niet leesbaar";
    return false;
  }

  $dataRaw = fgets($handle, 4096);
  $validateStr2 = textPart($dataRaw,16,16);
  
  if ( !$validateStr2 == "A")
  {
    $error[] = "Bestand validatie mislukt, geen Kasbank GLD bestand";
    return false;
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
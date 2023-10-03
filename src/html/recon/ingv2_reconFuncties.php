<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/09/04 08:29:42 $
 		File Versie					: $Revision: 1.5 $

 		$Log: ingv2_reconFuncties.php,v $
 		Revision 1.5  2019/09/04 08:29:42  cvs
 		call 8046
 		
 		Revision 1.4  2019/02/27 15:58:38  cvs
 		call 6621
 		
 		Revision 1.3  2018/06/12 12:18:08  cvs
 		eerste regel werd overgeslagen
 		
 		Revision 1.2  2018/05/23 13:13:14  cvs
 		call 6621
 		
 		Revision 1.1  2018/05/02 12:59:48  cvs
 		call 6621
 		
 		Revision 1.4  2018/01/03 16:23:59  cvs
 		validatie fail na aanpassing bestandsformaat
 		
 		Revision 1.3  2015/12/01 09:03:15  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2015/05/08 12:10:28  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2015/04/21 13:32:04  cvs
 		*** empty log message ***
 		
 	
 		
*/

/*
 * 1 .portfolioID,
 * 2 .portfolioCurrency,
 * 3 .securityID,
 * 4 .securityFullname,
 * 5 .securityType,
 * 6 .securityISIN,
 * 7 .securityBloombergTicker,
 * 8 .securityCurrency,
 * 9 .holdingsdate,
 * 10.holdingsQuantity,
 * 11.holdingsPrice,
 * 12.holdingsPriceFactor,
 * 13.securityPriceDate,
 * 14.holdingsAccruedInterest,
 * 15.holdingsMarketValueinLocalCurrency,
 * 16.holdingsCostValueinLocalCurrencyIncCosts,
 * 17.holdingsCostValueinLocalCurrencyExCosts,
 * 18.holdingsFXSecurityCurrToPortfolioCurr,
 * 19.holdingsMarketValueinPortfolioCurrency,
 * 20.holdingsCostValueinPortfolioCurrencyIncCosts,
 * 21.holdingsCostValueinPortfolioCurrencyExCosts,
 * 22.holdingsLastPurchaseDate,
 * 23.holdingsLastTradeDate
 * 24.holdingsRemark
 */

function recon_readBank($filename,$useISIN=false)
{
  global $prb, $batch, $recon, $airsOnly;
  
  
  $db = new DB();
  
  if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
	$csvRegels = Count(file($filename));
    
  $pro_multiplier = $csvRegels/100;
  $teller = 0;
  $ndx= 0;
  $prev_step = 0;
  //$prb->show();
  while ($data = fgetcsv($handle, 4096, ","))
  {
    //if (!is_numeric(trim($data[0]))) continue;  // sla lege regels over

  
  ////////////////////////////////////////////  
    $teller++;
    if ($teller < 2)
    {
      continue; // sla header over
    }


    $pro_step = intval( $teller /$pro_multiplier );
    if ($prev_step < $pro_step)
    {
        //  $prb->moveStep($pro_step);
          $prev_step = $pro_step;
    }


    //$prb->setLabelValue('txt1','Inlezen bankbestand, '.$teller." / ".$csvRegels.' regels');
    //$prb->moveNext();
    $row = $data;
    
    $record = array("depot" => "b",                // regel uit bankbestand
                    "batch"=>$batch."/".$teller);  // reset $record per ingelezen regel
    
    $portefeuille = trim($data[0]);
    $valuta       = trim($data[7]);
    $rekeninnr    = trim($data[0]);
    $aantal       = trim($data[9]);
    $isin         = trim($data[5]);
    $ingCode      = $data[2];

    if (substr($isin,0,4) == "DIV:")
    {
      continue;   // DIV toekenningen overslaan
    }

    if (trim($data[4]) == "CASH" )
    {
      
      $record["type"]      = "cash";
      $record["rekening"]  = $rekeninnr;
      $record["datum1"]    = "";
      $record["valuta"]    = $valuta;
      $record["bedragRaw"] = $aantal;
      $record["DC"]        = ($aantal >= 0)?"D":"C";
      $record["datum2"]    = "";
      $record["iban"]      = $rekeninnr;
      $record["bedrag"]    = $aantal;
    } 
    else
    {
      if (
            (stristr($data[4], "obligaties") AND $data[11] == 0.01) OR
            (stristr($data[4], "bonds") AND $data[11] == 0.01) OR
            stristr($data[4], "opties")   OR
            stristr($data[4], "options")
         )
      {
        $aantalFactor = 1;
      }
      else
      {
        $aantalFactor = $data[11];
      }




      $record["type"]         = "sec";
      $record["portefeuille"] = $portefeuille;
      $record["datum1"]       = "";
      $record["datum2"]       = "";
      $record["soort"]        = "";
      $record["aantalRaw"]    = $aantal * $aantalFactor;
      $record["aantal"]       = $aantal * $aantalFactor;
      $record["ISIN"]         = $isin;
      $record["bankCode"]     = $ingCode;
      $record["fonds"]        = $data[3];
      $record["PE"]           = "";
      $record["valuta"]       = $valuta;
      $record["koersRaw"]     = $data[10];
      $record["koers"]        = $data[10];
      $record["koersDatum"]   = $data[12];
    }
    $record["batch"] = $batch;
    $output[] = $record;
  
    $recon->addRecord($record);
    
    
  }
  
  echo "<li>AIRS data ophalen";
  ob_flush();flush();
  $recon->fillTableFormAIRS();
  
    
  echo "<li>AIRS portefeuilles ophalen";
  ob_flush();flush();
  $airsOnly = $recon->getAirsPortefeuilles();

  
  echo "<li>AIRS rekeningnummers ophalen";
  ob_flush();flush();
  $airsOnly = $recon->getAirsCashRekeningen();
  
  $recon->fillVB();
  
  //$prb->hide();    
  unlink($filename);
  return $teller;
}

function validateFile($filename)
{   
  global $error;
  $error = array();
  echo "<li>start validatie bestanden";
  ob_flush();flush();
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }

  $data = fgetcsv($handle, 4096, ",");
  $data = fgetcsv($handle, 4096, ",");
  //debug($data);
  $validateStr1 = is_numeric($data[0]);
  $validateStr2 = is_numeric($data[2]);
  
  if ( $validateStr1 AND $validateStr2)
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen ING bestand";
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
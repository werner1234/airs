<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/02/11 14:52:35 $
 		File Versie					: $Revision: 1.2 $

 		$Log: ubp_reconFuncties.php,v $
 		Revision 1.2  2020/02/11 14:52:35  cvs
 		call 5295
 		
 		Revision 1.1  2017/04/03 13:39:46  cvs
 		call 5639
 		

*/

/*
 *  0 => 'COMMON_ASSET_TYPE',
  1 => 'COMMON_NBR_CO',
  2 => 'COMMON_NBR_CLIENT',
  3 => 'COMMON_CUR_PTF',
  4 => 'COMMON_CUR_INST',
  5 => 'COMMON_DATE_POS',
  6 => 'ACCT_NBR_ACCT',
  7 => 'ACCT_NAME_HEADING_ACCT',
  8 => 'ACCT_AMT_BAL_ACCT_INST',
  9 => 'ACCT_AMT_BAL_ACCT_PTF',
  10 => 'ACCT_AMT_BAL_ACCT_CHF',
  11 => 'DEP_DATE_MATR',
  12 => 'DEP_AMT_DEPOSIT_INST',
  13 => 'DEP_AMT_DEPOSIT_PTF',
  14 => 'DEP_AMT_DEPOSIT_CHF',
  15 => 'DEP_RATE_BID_APPLIED',
  16 => 'DEP_CODE_TYPE_DEP_INST',
  17 => 'DEP_NBR_ISSUING_CO',
  18 => 'DEP_DATE_ISS_DEPOSIT',
  19 => 'DEP_NET_MAT_AMOUNT_INST',
  20 => 'DEP_NET_MAT_AMOUNT_PTF',
  21 => 'DEP_NET_MAT_AMOUNT_CHF',
  22 => 'DEP_AMT_COMMISSION_INST',
  23 => 'DEP_AMT_COMMISSION_PTF',
  24 => 'DEP_AMT_COMMISSION_CHF',
  25 => 'DEP_ACCRUED_INTEREST_INST',
  26 => 'DEP_ACCRUED_INTEREST_PTF',
  27 => 'DEP_ACCRUED_INTEREST_CHF',
  28 => 'DEP_TERM_INTEREST_AMOUNT_INST',
  29 => 'DEP_TERM_INTEREST_AMOUNT_PTF',
  30 => 'DEP_TERM_INTEREST_AMOUNT_CHF',
  31 => 'FX_NBR_DEAL',
  32 => 'FX_DATE_OPERATION',
  33 => 'FX_DATE_MATR_DEAL',
  34 => 'FX_CODE_CUR_ACH',
  35 => 'FX_CODE_CUR_VTE',
  36 => 'FX_RATE_DEAL',
  37 => 'FX_AMT_DEAL_ACH_INST',
  38 => 'FX_AMT_DEAL_VTE_INST',
  39 => 'FX_AMT_DEAL_PTF',
  40 => 'FX_AMT_DEAL_CHF',
  41 => 'LOANS_NBR_ACCT',
  42 => 'LOANS_NAME_HEADING_ACCT',
  43 => 'LOANS_AMT_BAL_ACCT_INST',
  44 => 'LOANS_AMT_BAL_ACCT_PTF',
  45 => 'LOANS_AMT_BAL_ACCT_CHF',
  46 => 'LOANS_RATE_INT_DR_ACCT',
  47 => 'SEC_INTERNAL_NUMBER',
  48 => 'SEC_ISINNUMBER',
  49 => 'SEC_INSTRUMENT',
  50 => 'SEC_CODE_TYPE_SECUR_CID',
  51 => 'SEC_ISS_TYP_NME',
  52 => 'SEC_QTY',
  53 => 'SEC_LATSPRICE',
  54 => 'SEC_AMT_ESTIM_INST',
  55 => 'SEC_AMT_ESTIM_PTF',
  56 => 'SEC_AMT_ESTIM_CHF',
  57 => 'SEC_PCT_INT_RATE',
  58 => 'SEC_DATE_MATR_SECUR',
  59 => 'DATE_CREATION',
  60 => 'DATE_LASTUPDATE',
  61 => 'SEC_COST_PRICE',
  62 => 'SEC_AVG_HIST_PRICE',
  63 => 'SEC_BOND_NAME',
  64 => 'SEC_FREQUENCY',
  65 => 'ACCT_CODE_IBAN',
  66 => 'ACCT_CODE_CAT_ACCT',
  67 => 'ACCT_NAME_ACCT_ENG',
  68 => 'DEP_NBR_DEAL_DEPOSIT',
 */
function toDate($in)
{
  return substr($in,0,4)."-".substr($in,4,2)."-".substr($in,6,2);
}



function recon_readBank($filename,$useISIN=false)
{
  global $prb, $batch, $recon, $airsOnly, $meldArray,$stat;
  
  
  $db = new DB();
  
  if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
	$csvRegels = Count(file($filename));
    
  $pro_multiplier = $csvRegels/100;
  $row = 0;
  $ndx= 0;
  $prev_step = 0;
  //$prb->show();
  while ($data = fgetcsv($handle, 8192, ";"))
  {

//    debug($data);

    if (!is_numeric(trim($data[1]))) continue;  // sla lege regels over

    $teller++;
    $stat = array();
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
    $tcSoort = trim(strtolower($data[0]));
    $stat[$tcSoort]++;
    $record["portefeuille"] = ontnullen(trim($data[2]));
    switch($tcSoort)
    {
      case "forward":
        $record["type"]      = "cash";
        $rekeningnr = ontnullen(trim($data[2]))."FWD";
        $record["rekening"]  = $rekeningnr;
        $record["datum1"]    = toDate($data[5]);
        $record["valuta"]    = trim($data[34]);
        $bedrag = $data[37];
        $record["bedragRaw"] = $bedrag;
        $record["DC"]        = ($bedrag >= 0)?"D":"C";
        $record["datum2"]    = "";
        $record["iban"]      = $rekeningnr;
        $record["bedrag"]    = $bedrag;
        $record["batch"] = $batch;
        $output[] = $record;
        $recon->addRecord($record);

        $record["type"]      = "cash";
        $rekeningnr = ontnullen(trim($data[2]))."FWD";
        $record["valuta"]    = trim($data[35]);
        $bedrag = $data[38];
        $record["bedragRaw"] = $bedrag;
        $record["DC"]        = ($bedrag >= 0)?"D":"C";
        $record["bedrag"]    = $bedrag;
        $output[] = $record;
        $recon->addRecord($record);

        break;
      case "account":
        $record["type"]      = "cash";
        $rekeningnr = ontnullen(trim($data[6]));
        $record["rekening"]  = $rekeningnr;
        $record["datum1"]    = toDate($data[5]);
        $record["valuta"]    = trim($data[4]);
        $bedrag = $data[8];
        $record["bedragRaw"] = $bedrag;
        $record["DC"]        = ($bedrag >= 0)?"D":"C";
        $record["datum2"]    = "";
        $record["iban"]      = $rekeningnr;
        $record["bedrag"]    = $bedrag;
        $record["batch"] = $batch;
        $output[] = $record;
        $recon->addRecord($record);
        break;
      case "loans":
        $record["type"]      = "cash";
        $rekeningnr = ontnullen(trim($data[2]))."LEN";
        $record["rekening"]  = $rekeningnr;
        $record["datum1"]    = toDate($data[5]);
        $record["valuta"]    = trim($data[4]);
        $bedrag = $data[43];
        $record["bedragRaw"] = $bedrag;
        $record["DC"]        = ($bedrag >= 0)?"D":"C";
        $record["datum2"]    = "";
        $record["iban"]      = $rekeningnr;
        $record["bedrag"]    = $bedrag;
        $record["batch"] = $batch;
        $output[] = $record;
        $recon->addRecord($record);
        break;
      case "security":
        $record["type"]         = "sec";
        $record["portefeuille"] = ontnullen(trim($data[2]));
        $record["datum1"]       = toDate($data[5]);
        $record["datum2"]       = "";
        $record["soort"]        = "";
        $aantal = trim($data[52]);
        $record["aantalRaw"]    = $aantal;
        $record["aantal"]       = $aantal;
        $record["ISIN"]         = trim($data[48]);
        $record["bankCode"]     = trim($data[47]);
//        $record["bankCode"]     = substr(trim($data[47]),0,-2);
        $record["fonds"]        = trim($data[49]);
        $record["PE"]           = "";
        $record["valuta"]       = trim($data[4]);
        $record["koersRaw"]     = $data[53];
        $record["koers"]        = $data[53];
        $record["batch"] = $batch;
        $output[] = $record;
        $recon->addRecord($record);
        break;
      default:
        // onbekend code
        $meldArray[] = "onbekende transactie soort: ".$data[0];
    }

    
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

  $data = fgetcsv($handle, 1000, ";");
  //debug($data);
  $validateStr1 = $data[0] == "COMMON_ASSET_TYPE";
  $validateStr2 = $data[1] == "COMMON_NBR_CO";
  
  if ( $validateStr1 AND $validateStr2)
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen UBP bestand";
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
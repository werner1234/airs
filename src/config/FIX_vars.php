<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/08/25 14:07:12 $
 		File Versie					: $Revision: 1.7 $

 		$Log: FIX_vars.php,v $
 		Revision 1.7  2016/08/25 14:07:12  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2016/05/25 15:42:38  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2016/05/11 09:15:09  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2015/11/22 14:24:57  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2015/09/23 14:58:14  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2015/09/16 16:21:17  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/08/26 15:46:28  rvv
 		*** empty log message ***
 		


*/
$__fixVars["msgType"] = array( 
    "new" => "nieuwe order",
    "mod" => "wijzig order",
    "del" => "verwijder order"
    
    );
$__fixVars["leg_type"] = array( 
    "C",
    "P",
    "F"  );

$__fixVars['side']   = array(
    "buy",
    "sell" );

$__fixVars['type']   = array(
    "mkt",
    "lim" );

$__fixVars['ord_status'] = array(
  "0" => "New",
  "1" => "Partially",
  "2" => "Traded",
  "3" => "Done for Day",
  "4" => "Cancelled",
  "5" => "Replaced",
  "6" => "Pending Cancel",
  "8" => "Rejected",
  "A" => "Pending New",
  "B" => "Calculated",
  "C" => "Expired",
  "E" => "Pending Replace",
  "S" => "Cancelled by Market Operation",
  "O" => "Eliminated by corporate event",
  "CP" => "Charm queue"
  
);

$__fixVars['exec_type'] = array(
  "0" => "New", 
  "1" => "Partially filled", 
  "2" => "Filled", 
  "3" => "Done for Day", 
  "4" => "Cancelled", 
  "5" => "Replaced", 
  "6" => "Pending Cancel", 
  "8" => "Rejected", 
  "A" => "Pending New",
  "B" => "Calculated", 
  "C" => "Expired", 
  "D" => "Restated", 
  "E" => "Pending Replace", 
  "F" => "Trade", 
  "G" => "Trade Correct", 
  "H" => "Trade Cancel",
  "O" => "Eliminated by corporate event"  
    );

$__fixVars["fields"] = array(
    "id",
    "type",
    "msg_id",
    "destination",
    "client_order_id",
    "order_id",
    "exchange",
    "security_id",
    "bank_security_id",
    "side",
    "ord_type",
    "expire_date",
    "price",
    "gross_trade_amt",
    "order_qty",
    "min_qty",
    "max_floor",
    "currency",
    "account",
    "client_id",
    "free_text",
    "no_legs",
    "symbol",
    "expiry",
    "strike",
    "leg_type",
    "leg_ratio",
    "leg_side",
    "exec_id",
    "transact_time",
    "ord_status",
    "exec_type",
    "leaves_qty",
    "cum_qty",
    "order_rej_reason",
    "text",
    "fixQueueReference",
    "last_price",
    "last_shares",
    "ref_type",
    "push_fiat"
);

$__fixVars['BankDepotCodes']=array(
'AAB'=>'AABCode',//,ABRCode
'AABIAM'=>'AABCode',//ABRCode
'AAB BE'=>'AABBE',
'BIN'=>'binckCode',
'BINB'=>'binckCode',
'CS'=>'CSCode',
'CS AG'=>'CSCode',
'GIRO'=>'giroCode',
'FVL'=>'FVLCode',
'KAS'=>'kasbankCode',
'KNOX'=>'KNOXcode',
'SNS'=>'snsSecCode',
'SAXO'=>'SAXOcode',
'TGB'=>'stroeveCode',
'NIBC'=>'snsSecCode');

$__fixVars["queueMappingFields"] = array(
      "typeBericht"       =>  "type",
      "fondssoort"        =>  "security_type",
      "berichtId"         =>  "msg_id",
      "depotbank"         =>  "destination",
      "ordernr"           =>  "client_order_id",
      "orderIdDepotbank"  =>  "order_id",
      "beurs"             =>  "exchange",
      "ISIN"              =>  "security_id",
      "bankCode"          =>  "bank_security_id",
      "transactieSoort"   =>  "side",
      "typeTransactie"    =>  "ord_type",
      "limietDatum"       =>  "expire_date",
      "limietKoers"       =>  "price",
      "aantal"            =>  "order_qty",
      "bedrag"            =>  "gross_trade_amt",
      "valuta"            =>  "currency",
      "vermogenbeheerder" =>  "account",
      "portefeuille"      =>  "client_id",
      "uitvoeringsId"     =>  "exec_id",
      "orderStatus"       =>  "ord_status",
      "executieType"      =>  "exec_type",
      "rejectReden"       =>  "order_rej_reason",
      "tekst"             =>  "text",
      "legs"              =>  "legs",
      "bulk"              =>  "bulk",
      "no_legs"           =>  "no_legs",
      "push_fiat"         =>  "push_fiat"
    );




function fix_cnvDate($inDate,$OutdateFormat="ymd",$indateFormat="db")
{
  if ($indateFormat <> "db")  // juliandate
  {
    $inDate = date("Y-m-d H:i:s",$inDate);  // reformat to mysql datetime
  }
  
  $parts = explode(" ",$indate);
  $dateStr = str_replace("-","",$parts[0]);
  
  switch (strtolower($OutdateFormat) )
  {
    case "ym":
      return substr($dateStr,0,6);
      break;
    case "ymdt":
      return $dateStr."-".$parts[1];
      break;
    default:  // ymd
      return $dateStr;
  }
}

?>
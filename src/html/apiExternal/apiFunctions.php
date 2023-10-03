<?php
/*
    AE-ICT sourcemodule created 11 jan. 2021
    Author              : Chris van Santen
    Filename            : apiFunctions.php


*/

include_once "../../classes/AE_cls_mysql.php";
//////////////////////////////////////////////////////////////////////////////////////
/// functies tbv CRM
//////////////////////////////////////////////////////////////////////////////////////

function findCRMidByPortefeuille($portefeuille)
{
  global $__dbDebug;
  $db = new DB();
  $db->debug = $__dbDebug;
  $query = "SELECT id FROM CRM_naw WHERE portefeuille='$portefeuille'";
  if ($rec = $db->lookupRecordByQuery($query))
  {
    return (int)$rec["id"];
  }
  else
  {
    return false;
  }
}

function slashArray($array)
{
  foreach ($array as $k=>$v)
  {
    $out[$k] = addslashes($v);
  }

  return $out;
}

function apiGetCRMById($id, $dataset="id")
{
  global $error, $result, $__glob, $__dbDebug;


  if (is_array($dataset) )
  {
    $fields = "`".implode("`,`",$dataset)."` ";
  }
  else
  {
    $fields = $dataset;
  }

  if (strstr($fields,"*") AND !$__glob["wildcardsAllowed"])    // wordt ergens in de fieldset een * gebruikt dan eerst een wildcard test doen..
  {
    $error[] = "wildcards in fieldset not allowed";
  }

  if ($fields == "`*` " )
  {
    if  ($__glob["wildcardsAllowed"])
    {
      $fields = "*";
    }
    else
    {
      $error[] = "wildcards not allowed";
    }

  }

  if (count($error) == 0)
  {
    if (is_int($id) AND $id > 0)
    {
      $db = new DB();
      $db->debug = $__dbDebug;
      $query =   "
     SELECT 
       $fields 
     FROM 
       (CRM_naw)
     WHERE 
       id = $id ";
//print_r($query);
      if (!$rec = $db->lookupRecordByQuery($query))
      {
        $error[] = "CRM record with id $id not found";
      }
      else
      {
        $crm = new Naw();
        $crm->getById($id);
        $fieldData = $crm->data["fields"];
//        $result = slashArray($rec);
        return $rec;
      }


    }
    else
    {
      $error[] = "invalid CRM id";
    }
  }

}

function apiGetPortefeuilleByPortnr($portnr, $dataset="id")
{
  global $error, $result, $__glob, $__dbDebug;


  if (is_array($dataset) )
  {
    $fields = "`".implode("`,`",$dataset)."` ";
  }
  else
  {
    $fields = $dataset;
  }

  if (strstr($fields,"*") AND !$__glob["wildcardsAllowed"])    // wordt ergens in de fieldset een * gebruikt dan eerst een wildcard test doen..
  {
    $error[] = "wildcards in fieldset not allowed";
  }

  if ($fields == "`*` " )
  {
    if  ($__glob["wildcardsAllowed"])
    {
      $fields = "*";
    }
    else
    {
      $error[] = "wildcards not allowed";
    }

  }

  if (count($error) == 0)
  {

    if (!strstr($portnr," ") AND $portnr != "")
    {
      $db = new DB();
      $db->debug = $__dbDebug;
      $query =   "
     SELECT 
       $fields 
     FROM 
       (Portefeuilles)
     WHERE 
       Portefeuille = '$portnr' ";
//print_r($query);
      if (!$rec = $db->lookupRecordByQuery($query))
      {
        $error[] = "Portefeuile record $portnr not found";
      }
      else
      {
        foreach ($rec as $k=>$v)
        {
          $fieldInfo[$k] = array(
            "description"  => $pFieldData[$k]["description"],
            "dataType"     => $pFieldData[$k]["form_type"],
            "dataSize"     => $pFieldData[$k]["form_size"],
            "form_options" => $pFieldData[$k]["form_options"],
          );
        }

        $rec["fieldInfo"] = $fieldInfo;
//        print_r($rec);
//        $result = slashArray($rec);
        return $rec;
      }


    }
    else
    {
      $error[] = "invalid CRM id";
    }
  }

}

//////////////////////////////////////////////////////////////////////////////////////
/// functies tbv HTML rapporten
//////////////////////////////////////////////////////////////////////////////////////


//////////////////////////////////////////////////////////////////////////////////////
/// standaard functies
//////////////////////////////////////////////////////////////////////////////////////


function toJson($data)  // output Json string
{
  include_once "../../classes/AE_cls_Json.php";
  $json = new AE_Json();
  return $json->json_encode($data);
}

function sanatizeInput($input, $length=25)
{
//  $input = preg_replace ('/<[^>]*>/', $input);
  $input = strip_tags($input);
  $input = preg_replace('/<script\b[^>]*>(.*?)<\/script>/i', "",$input) ;
  $input = preg_replace('/[^a-zA-Z0-9-_@,. \']+/', '', $input);
//  $input = str_replace("'", "`", $input);
  return substr($input,0,$length);
}

function sanatizePortefeuille($input, $length=25)
{
//  $input = preg_replace ('/<[^>]*>/', $input);
  $input = strip_tags($input);
  $input = preg_replace('/[^a-zA-Z0-9-_, ]+/', '', $input);
  return substr($input,0,$length);
}

function strip($input)  // normalize and sanatize input
{
  $input = strtolower($input);
  $input = preg_replace('/[^a-z0-9 -]+/', '', $input);
  $input = str_replace(' ', '-', $input);
  return trim($input, '-');
}

function checkQueriesPerHour($ip)  // check connections from IP in the last hour
{
  global $__dbDebug;
  $db = new DB();
  $db->debug = $__dbDebug;
  $query = "SELECT count(id) as connects FROM `APIextern_logging` WHERE ip = '{$ip}' AND add_date > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
  $rec = $db->lookupRecordByQuery($query);
  return (int) $rec["connects"];
}

function logApiCall($s)  // logApicall to table
{
  global $error, $__ses, $__dbDebug;
  $db = new DB();
  $db->debug = $__dbDebug;
  $query = "
    INSERT INTO
      `APIextern_logging`
    SET 
        `add_user`   = 'apiEngine'
      , `add_date` = NOW()
      , `ip`       = '".$s["ipaddress"]."'
      , `referer`  = '".$s["referer"]."'
      , `request`  = '".toJson($s["data"])."'
      , `errors`   = '".toJson($error)."'
      , `results`  = ''
     ";

   $db->executeQuery($query);
   $__ses["logId"] = $db->last_id();
   return true;
}


function addToExternQueue($data)
{
  global $error, $__ses, $__dbDebug;
  $san = array();
  foreach ($data as $k=>$v)
  {
    $san[$k] = str_replace("'","`",sanatizeInput($v, 150));
  }
  $data = $san;

  $db = new DB();
  $db->debug = $__dbDebug;
  $query = "
    INSERT INTO
      `APIextern_queue` 
    SET 
        `add_user`   = 'apiEngine'
      , `add_date` = NOW()
      , `action`       = '".$data["action"]."'
      , `submitterIp`  = '".$__ses["ipaddress"]."'
      , `dataFields`   = '".toJson($data)."'
      , `type`         = '".(($data["new"] == 1)?"new":"update")."'
     ";

  $db->executeQuery($query);
  return true;
}

function getVragenLijstPerRelatieById($id)
{
  global $__dbDebug;
  $db = new DB();
  $db->debug = $__dbDebug;
  $query = "SELECT * FROM VragenLijstenPerRelatie WHERE id = {(int)$id}";
  if ($rec = $db->lookupRecordByQuery($query))
  {
    return $rec;
  }
  else
  {
    return false;
  }

}

function UpdateLogApiCall()  // update logApiCall for exit
{
  global $error, $result, $__ses, $__dbDebug;
  $db = new DB();
  $db->debug = $__dbDebug;
  $query = "
    UPDATE
      `APIextern_logging`
    SET 
        `errors`   = '".mysql_real_escape_string(toJson($error))."'
      , `results`  = '".mysql_real_escape_string(toJson($result))."'
    WHERE 
      id = ".(int)$__ses["logId"]."  
     ";
  $db->executeQuery($query);
  return true;
}

function ad($var)
{
  global $__debug;
  if ($__debug)
  {
    $bt = debug_backtrace();
    print_r("--DEBUG".str_repeat("-", 73));
    print_r("\nfile: ".$bt[0]["file"].":".$bt[0]["line"]."\n");
    print_r(var_export($var),true);
    print_r("\n".str_repeat("=", 80));
    print_r("\n");
  }
}

function debugFile($input="")
{

  global $__appvar, $__debug,$USR;

  $bt = debug_backtrace();

  if ($__debug)
  {
    $writeFilename = getcwd()."/debugFile.log";

    if (!$writeHandle = fopen($writeFilename, 'a+'))
    {
      echo "Cannot open file ($writeHandle)";
      return false;
    }
    if (is_array($input))
    {
      $input = var_export($input,true);
    }

    $outp = date("Ymd H:i ")." :: ".basename($bt[0]["file"]).":".$bt[0]["line"]." :: ".$input;
    fwrite($writeHandle, $outp."\n\n");
    fclose($writeHandle);


  }
  return true;

}

function encodeJul()
{
  global $__glob;
  $julOffset = $__glob["julOffset"];
  $now = mktime()-$julOffset;
  $out = "";
  $txt = (string) $now;
  for ($x=0; $x < strlen($txt); $x++)
  {
    $r = (string)rand(1,9);
    $out .= "{$txt[$x]}{$r}";
  }
//  ad(array(
//    "encodeJul"=>"",
//    "now" => $now,
//    "txt" =>$txt,
//    "out" =>$out
//     ));
  return rand(11,99).$out.rand(11,99);
}

function decodeJul($txt)
{
  global $__glob;
  if (strlen($txt) < 16) // ongeldige doctok
  {
    return 0;
  }
  $julOffset  = $__glob["julOffset"];
  $txt = substr($txt,2,-2);
  $out = "";
  for ($x=0; $x < strlen($txt); $x +=2)
  {
    $out .= "$txt[$x]";
  }
//ad(array(
//  "out" => $out,
//  "juloffset" => $julOffset,
//  "result" => abs((int)$out + $julOffset)
//   ));
  return abs((int)$out + $julOffset);
}

function checkDocTok($txt)
{
  global $__glob;
  $julOffset  = $__glob["julOffset"];
  $maxTimeOut = (int)$__glob["julTimeOut"];
  $inStamp    = decodeJul($txt);
  $ttl        = ($inStamp+$maxTimeOut);
  $now        = mktime() ;
  $output     = ( $ttl > $now );

//  ad(array(
//    "scramble :".$txt,
//    "offset :".$julOffset,
//    "intime :{$inStamp}",
//    "timeout sec : $maxTimeOut",
//    "timeout :".($inStamp + $maxTimeOut),
//    "now : $now" ,
//    "switch :".(boolean)$output,
//    "left :".($ttl-$now)));
  return $output;
}



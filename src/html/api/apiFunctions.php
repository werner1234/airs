<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/05/13 13:24:14 $
    File Versie         : $Revision: 1.4 $

    $Log: apiFunctions.php,v $
    Revision 1.4  2019/05/13 13:24:14  cvs
    call 7630

    Revision 1.3  2018/09/26 09:30:07  cvs
    update naar DEMO

    Revision 1.2  2018/01/24 14:59:28  cvs
    call 6527

    Revision 1.1  2017/08/18 14:41:16  cvs
    call 5815



*/
include_once "../../classes/AE_cls_mysql.php";

//////////////////////////////////////////////////////////////////////////////////////
/// functies tbv CRM
//////////////////////////////////////////////////////////////////////////////////////

function findCRMidByPortefeuille($portefeuille)
{
  $db = new DB();
  $query = "SELECT id FROM CRM_naw WHERE portefeuille='$portefeuille'";
//print_r($query);
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
  global $error, $result, $__glob;


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
      $query =   "
     SELECT 
       $fields 
     FROM 
       (CRM_naw)
     WHERE 
       id = $id ";

      if (!$rec = $db->lookupRecordByQuery($query))
      {
        $error[] = "CRM record with id $id not found";
      }
      else
      {
        $crm = new Naw();
        $crm->getById($id);
        $fieldData = $crm->data["fields"];
        foreach ($rec as $k=>$v)
        {
          $fieldInfo[$k] = array(
            "description"  => $fieldData[$k]["description"],
            "dataType"     => $fieldData[$k]["form_type"],
            "dataSize"     => $fieldData[$k]["form_size"],
            "form_options" => $fieldData[$k]["form_options"],
          );
        }

        $rec["fieldInfo"] = $fieldInfo;
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
  global $error, $result, $__glob;


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

function strip($input)  // normalize and sanatize input
{
  $input = strtolower($input);
  $input = preg_replace('/[^a-z0-9 -+]+/', '', $input);
  $input = str_replace(' ', '-', $input);
  return trim($input, '-');
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

function sanatizeArray($arr)
{
  $out = array();
  foreach($arr as $key=>$value)
  {
    $out[$key] = str_replace("'", "`", $value);
    $out[$key] = str_replace('"', "``",$out[$key]);
  }
  return $out;
}

function sanatizeDatum($input, $length=10)
{
  $input = strip_tags($input);
  $input = preg_replace('/[^0-9 -]+/', '', $input);
  return substr($input,0,$length);
}

function sanatizeCategorie($input, $length=25)
{
  $input = strip_tags($input);
  $input = preg_replace('/[^a-zA-Z0-9-_., ]+/', '', $input);
  return substr($input,0,$length);
}

function sanatizePortefeuille($input, $length=25)
{
//  $input = preg_replace ('/<[^>]*>/', $input);
  $input = strip_tags($input);
  $input = preg_replace('/[^a-zA-Z0-9-_, ]+/', '', $input);
  return substr($input,0,$length);
}
function checkQueriesPerHour($ip)  // check connections from IP in the last hour
{
  $db = new DB();
  $query = "SELECT count(id) as connects FROM `API_logging` WHERE ip = '$ip' AND add_date > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
  $rec = $db->lookupRecordByQuery($query);
  return (int) $rec["connects"];
}

function logApiCall($s)  // logApicall to table
{
  global $error, $__ses;
  $db = new DB();
  $sClean = $s["data"];

  $query = "
    INSERT INTO
      `API_logging`
    SET 
        `add_user`   = 'apiEngine'
      , `add_date` = NOW()
      , `ip`       = '".$s["ipaddress"]."'
      , `referer`  = '".$s["referer"]."'
      , `request`  = '".toJson($sClean)."'
      , `errors`   = '".toJson($error)."'
      , `results`  = ''
     ";

   $db->executeQuery($query);
   $__ses["logId"] = $db->last_id();
   return true;
}

function getVragenLijstPerRelatieById($id)
{
  $db = new DB();
  $query = "SELECT * FROM VragenLijstenPerRelatie WHERE id = $id";
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
  global $error, $result, $logArray, $__ses, $startTime;
  $duration = (microtime() - $startTime) * 1000;
  $db = new DB();
  $r = (count($logArray) > 0)?$logArray:$result;

  $query = "
    UPDATE
      `API_logging`
    SET 
        `errors`   = '".mysql_real_escape_string(toJson($error))."'
      , `results`  = 'Duration: {$duration} seconds, \n".mysql_real_escape_string(toJson($r))."'
    WHERE 
      id = ".$__ses["logId"]."  
     ";
  $db->executeQuery($query);
  return true;
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


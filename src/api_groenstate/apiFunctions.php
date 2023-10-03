<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/09/28 12:34:34 $
    File Versie         : $Revision: 1.2 $

    $Log: apiFunctions.php,v $
    Revision 1.2  2018/09/28 12:34:34  cvs
    call 7097

    Revision 1.1  2018/09/28 11:33:34  cvs
    call 7097



*/
include_once "../classes/AE_cls_mysql.php";
//////////////////////////////////////////////////////////////////////////////////////
/// functies tbv CRM
//////////////////////////////////////////////////////////////////////////////////////

function findCRMidByPortefeuille($portefeuille)
{
  $db = new DB();
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

//////////////////////////////////////////////////////////////////////////////////////
/// standaard functies
//////////////////////////////////////////////////////////////////////////////////////


function toJson($data)  // output Json string
{
  include_once "../classes/AE_cls_Json.php";
  $json = new AE_Json();
  return $json->json_encode($data);
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
  $db = new DB();
  $query = "SELECT count(id) as connects FROM `API_GRO_logging` WHERE ip = '$ip' AND add_date > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
  $rec = $db->lookupRecordByQuery($query);
  return (int) $rec["connects"];
}

function logApiCall($s)  // logApicall to table
{
  global $error, $__ses;

  $db = new DB();
  $query = "
    INSERT INTO
      `API_GRO_logging`
    SET 
        `add_user`   = 'apiEngine'
      , `add_date` = NOW()
      , `ip`       = '".$s["ipaddress"]."'
      , `referer`  = '".$s["referer"]."'
      , `request`  = '".toJson($s)."'
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
  global $error, $result, $__ses;
  $db = new DB();
  $query = "
    UPDATE
      `API_GRO_logging`
    SET 
        `errors`   = '".mysql_real_escape_string(toJson($error))."'
      , `results`  = '".mysql_real_escape_string(toJson($result))."'
    WHERE 
      id = ".$__ses["logId"]."  
     ";
  $db->executeQuery($query);
  return true;
}


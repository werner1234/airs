<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/09/23 17:14:23 $
 		File Versie					: $Revision: 1.6 $

 		$Log: xml_readInputDir.php,v $
 		Revision 1.6  2018/09/23 17:14:23  cvs
 		call 7175
 		
 		Revision 1.5  2014/08/06 12:35:39  cvs
 		*** empty log message ***
 		
 		Revision 1.4  2014/07/17 11:34:44  cvs
 		wijziging veldennamen AIRS zijde
 		
 		Revision 1.3  2014/05/09 06:30:06  cvs
 		groensys veld naam splitsen naar AIRS naam en naam1
 		
 		Revision 1.2  2014/03/12 11:18:50  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2014/01/22 13:04:17  cvs
 		Groensys koppeling

*/

/*

standaard map is c:\AIRS\groensys tenzij anders opgegeven in local_vars.php in var $__appvar["gs_path"];

*/

$disable_auth = true;
include_once("wwwvars.php");

if ($__appvar["gs_path"] <> "")
  $path = $__appvar["gs_path"];
else
  $path = "c:\\AIRS\\groensys\\";


/// GROENSYS            => AIRS
$mapping = array(
  "naam"                            => "naam",
  "aanhef"                   => "verzendAanhef",
  "email"                           => "email",
  "postAdres_straatEnHuisNummer"    => "adres",
  "postAdres_postcode"              => "pc",
  "postAdres_plaats"                => "plaats",
  "postAdres_land"                  => "land",
  "verzendAdres_straatEnHuisNummer" => "verzendAdres",
  "verzendAdres_postcode"           => "verzendPc",
  "verzendAdres_plaats"             => "verzendPlaats",
  "verzendAdres_land"               => "verzendLand",
);



$db = new DB();
// Write the contents back to the file


$files = getXMLFilesFromDir($path."/out/");
//listarray($files);


?>
<table border=1>
<tr>
  <td>id</td>
  <td>actie</td>
  <td>naam</td>
</tr>
<?


for ($x=0; $x < count($files); $x++)
{
  $lockedFile = $files[$x];
  //.".lock";
  //rename($files[$x],$lockedFile);
  $content = file_get_contents($lockedFile);
  $xml=simplexml_load_file($lockedFile);
//  debug($xml);
//  $a = count($xml->airsRecord->adressen->adres);
  $tempAdres = array();
  foreach ($xml->airsRecord->adressen->adres as $item)
  {
    $attr = array();
    foreach($item->attributes() as $a => $b)
    {
      $b = (array) $b;
      $attr[$a] = $b[0];
    }
    $soort = $attr["adresType"];
    $item = (array)$item;
    unset($item['@attributes']);
    $item["soort"] = $soort;
    $tempAdres[] = $item;
  }


  $resultData = array();
  $resultData["id"] = $xml->id;

  listarray( ">>>> $x = ".$files[$x]);

  $groensysArray = (array) $xml->airsRecord;
  unset($groensysArray["adressen"]);


  foreach ($tempAdres as $item)
  {

    if ($item["soort"] == "postAdres")
    {
      $groensysArray["postAdres_straatEnHuisNummer"]    = (string) $item["straatEnHuisNummer"];
      $groensysArray["postAdres_postcode"]              = (string) $item["postcode"];
      $groensysArray["postAdres_plaats"]                = (string) $item["plaats"];
      $groensysArray["postAdres_land"]                  = (string) $item["land"];
      $groensysArray["verzendAdres_straatEnHuisNummer"] = (string) $item["straatEnHuisNummer"];
      $groensysArray["verzendAdres_postcode"]           = (string) $item["postcode"];
      $groensysArray["verzendAdres_plaats"]             = (string) $item["plaats"];
      $groensysArray["verzendAdres_land"]               = (string) $item["land"];
    }
    else
    {
      $groensysArray["verzendAdres_straatEnHuisNummer"] = (string) $item["straatEnHuisNummer"];
      $groensysArray["verzendAdres_postcode"]           = (string) $item["postcode"];
      $groensysArray["verzendAdres_plaats"]             = (string) $item["plaats"];
      $groensysArray["verzendAdres_land"]               = (string) $item["land"];
    }
  }

  switch (strtolower($xml->operation))
  {
    case "delete":
      $crmRec = lookupCMR($xml->airsRecord->portefeuilleNummer,$xml->correlationId);
      if ($crmRec["result"] == 0)
      {
        $query = "
        UPDATE CRM_naw SET
          aktief = 0,
          memo='".date("j-n-Y G:i")." verwijdert in Groensys\n".$crmRec["record"]["memo"]."'
        WHERE externID = '$xml->correlationId'";
        if ($db->executeQuery($query))
        {
          $resultData["resultCode"] = "0";
          $resultData["result"] = "OK - record op inaktief gezet";
          updateTrackAndTrace($crmRec["record"]["id"], "aktief", $crmRec["record"]["aktief"], 0);
        }
        else
        {
          $resultData["resultCode"] = "-100";
          $resultData["result"] = "FOUT - ".mysql_error();
        }
        XmlResponse($files[$x],$resultData);

      }
      else
      {
        $resultData["resultCode"] = "-10";
        $resultData["result"] = "geen record voor delete";
        XmlResponse($files[$x],$resultData);
      }
      break;
    case "update":
      $crmRec = lookupCMR($xml->airsRecord->portefeuilleNummer,$xml->correlationId);
      if ($crmRec["result"] == 0)
      {
        debug("found");
        $queryData = buildQuery($groensysArray);
        $queryDataOuput = implode(" , \n",$queryData[1]);
        if ($queryData[0])
        {
          echo "succes ".$queryDataOuput;
        }
        else
        {
          echo "error ".$queryDataOuput;
        }

        $query = "
        UPDATE CRM_naw SET
          $queryDataOuput
          , memo='".date("j-n-Y G:i")." bijgewerkt vanuit Groensys\n".$crmRec["record"]["memo"]."'
          WHERE externID = '$xml->correlationId'";
debug($query);
        if ($db->executeQuery($query))
        {
          for ($y=0; $y < count($queryData[2]); $y++)
          {
            $t = $queryData[2][$y][0];
            $v = $queryData[2][$y][1];
            updateTrackAndTrace($crmRec["record"]["id"], $t, $crmRec["record"][$t],$v);
          }
          $resultData["resultCode"] = "0";
          $resultData["result"] = "OK - record bijgewerkt";
        }
        else
        {
          $resultData["resultCode"] = "-100";
          $resultData["result"] = "FOUT - ".mysql_error();
        }
      }
      else
      {
        $resultData["resultCode"] = "-10";
        $resultData["result"] = "geen passend record voor update";
      }

      XmlResponse($files[$x],$resultData);
      break;
    case "insert":
      $crmRec = lookupCMR($xml->airsRecord->portefeuilleNummer,$xml->correlationId);
      if ($crmRec["result"] == 0)
      {
        $resultData["resultCode"] = "-10";
        $resultData["result"] = "record bestaat al";
        XmlResponse($files[$x],$resultData);
      }
      else
      {
        $queryData = buildQuery($groensysArray,"add");
        $queryDataOuput = implode(" , \n",$queryData[1]);
        if ($queryData[0])
        {
           $query = "
           INSERT INTO CRM_naw SET
           $queryDataOuput
           , memo='".date("j-n-Y G:i")." aangemaakt vanuit Groensys\n".$crmRec["record"]["memo"]."'
           , externID = '$xml->correlationId'
           , portefeuille = '".$xml->airsRecord->portefeuilleNummer."'
           , aktief = 1
           , debiteur = 1 ";
          if ($db->executeQuery($query))
          {
            $resultData["resultCode"] = "0";
            $resultData["result"] = "OK - record toegevoegd";
            updateTrackAndTrace($db->last_id(), "naam","added",$xml->airsRecord->naam);
          }
          else
          {
            $resultData["resultCode"] = "-100";
            $resultData["result"] = "FOUT - ".mysql_error();
          }
          XmlResponse($files[$x],$resultData);
        }
      }
      break;
    default:
      $resultData["resultCode"] = "-999";
      $resultData["result"] = "onbekende actie";
      XmlResponse($files[$x],$resultData);

  }



?>
<tr>
  <td><?=$xml->id?></td>
  <td><?=$xml->operation?></td>
  <td><?=$xml->airsRecord->naam?></td>
  <td><?=$xml->airsRecord->portefeuilleNummer?></td>
  <td><?=$xml->correlationId?></td>
  <td><?=$files[$x]?></td>
</tr>
<?

  echo "<pre>";
  print_r(lookupCMR($xml->airsRecord->portefeuilleNummer,$xml->correlationId));
  echo "</pre>";
  $data = array();
  $data["id"] = $xml->id;
  $data["resultCode"] = "0";
  $data["naam"] = $xml->airsRecord->naam;

  $data["stamp"] = date("Y-m-d H:i:s");
  $data["file"] = $files[$x];


/*
echo "<PRE>";
print_r($xml);
echo "</PRE>";
*/

}

echo "</table>";
exit;

// functies

function getXMLFilesFromDir($dir)
{
  $files = array();
  if ($handle = opendir($dir))
  {
    while (false !== ($file = readdir($handle)))
    {
      if ($file != "."   AND
          $file != ".."  )
      {
        if(!is_dir($dir.'/'.$file))
        {
          $fileParts = explode(".",$file);
          if (strtolower($fileParts[(count($fileParts)-1)]) == "xml")
            $files[] = $dir.'/'.$file;
        }
      }
    }
    closedir($handle);
  }

  return $files;
}

function XmlResponse($file,$data)
{
  global $path;
//listarray($file);
//listarray($data);
  $responseTemplate = <<< EOB
<?xml version="1.0"?>
<airsResponse xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="http://www.groenstate.nl/GroenSys/Integration/Airs">
  <id>{id}</id>
  <resultCode>{resultCode}</resultCode>
  <result>{result}</result>
</airsResponse>
EOB;

 // unlink($file);  //verwijder importbestand
  listarray($file);
  $file = str_ireplace("/OUT/","/in/",$file);
  listarray($file);
  $responseTemplate = str_replace("{id}",$data["id"], $responseTemplate);
  $responseTemplate = str_replace("{resultCode}",$data["resultCode"], $responseTemplate);
  $responseTemplate = str_replace("{result}",$data["result"], $responseTemplate);
  $responseTemplate = str_replace("{naam}",$data["naam"], $responseTemplate);
  $responseTemplate = str_replace("{stamp}",$data["stamp"], $responseTemplate);
  $responseTemplate = str_replace("{file}",$data["file"], $responseTemplate);
  file_put_contents($file, $responseTemplate);

}


function buildQuery($xmlRecord,$action="u")
{
  $db = new DB();
  global $mapping;
  $query = "SHOW COLUMNS FROM CRM_naw;";
  $db->executeQuery($query);
  while ($fldRec = $db->nextRecord())
  {
    $AIRSfieldNames[] = $fldRec["Field"];
  }
  //$xml = (array) $xmlRecord->airsRecord;  // convert object to array
  $xml = (array) $xmlRecord;  // convert object to array
 // listarray($xml);
 // listarray($mapping);
  foreach ($xml as $key => $value)
  {
    if ($mapping[$key] <> "")
    {
      if ($mapping[$key] == "naam")
      {
        $parts = explode("\n",str_replace("\r","",$value));
        $data[] = " `naam` = '".mysql_escape_string( trim($parts[0]) )."'";
        $data[] = " `naam1` = '".mysql_escape_string( trim($parts[1]) )."'";
      }
      else
      {
        $data[] = " `".$mapping[$key]."` = '".mysql_escape_string($value)."'";  
      }
      
      $ttData[] = array($key, $value);
    }
    else
    {
      $error[] = $key;
    }

  }

  //if (count($error)>0)
  if (count($data) < 1 )
    return array(false,$error);
  else
    return array(true, $data, $ttData);
}


function updateTrackAndTrace($id, $field, $old, $new)
{
  $db = new DB();
  $query  = "INSERT INTO trackAndTrace SET ";
  $query .= " `tabel` = 'CRM_naw', ";
  $query .= " `recordId` = '$id', ";
  $query .= " `veld` = '$field', ";
  $query .= " `oudeWaarde` = '$old', ";
  $query .= " `nieuweWaarde` = '$new', ";
  $query .= " `add_date` = NOW(), ";
  $query .= " `add_user` = 'xml' ";
  return $db->executeQuery($query);
}

function lookupCMR($portefeuille, $externID)
{
  $db = new DB();
  $query = "SELECT * FROM CRM_naw WHERE portefeuille = '".trim($portefeuille)."' AND externID = '".trim($externID)."' ";
  $query = "SELECT * FROM CRM_naw WHERE portefeuille = '".trim($portefeuille)."' ";
  $db->SQL($query);
  if ($CRMrec = $db->lookupRecord())
  {
    $out["result"] = 0;
    $out["msg"]    = "portefeuille and externID gevonden";
    $out["record"] = $CRMrec;
  }
  else
  {
    $query = "SELECT * FROM CRM_naw WHERE portefeuille = '".trim($portefeuille)."' ";
    $db->SQL($query);
    if ($CRMrec = $db->lookupRecord())
    {
      $out["result"] = -1;
      $out["msg"]    = "portefeuille gevonden, geen geldig externID";
      $out["record"] = $CRMrec;
    }
    else
    {
      $out["result"] = -1;
      $out["msg"]    = "geen geldige portefeuille voor match gevonden";
    }
  }

  return $out;
}

?>
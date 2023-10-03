<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/08/04 10:47:37 $
 		File Versie					: $Revision: 1.12 $
*/

$disable_auth = true;
include("wwwvars.php");
include_once("../classes/AE_cls_digidoc.php");
include_once "../classes/AE_cls_dd_helper.php";



$db     = new DB();
$query  = "SELECT Vermogensbeheerders.ddInleesLocatie,Vermogensbeheerders.ddInleesPortefeuillePreg FROM Vermogensbeheerders $join WHERE ddInleesLocatie <> '' $where";

$settings         = $db->lookupRecordByQuery($query);
$locatie          = $settings['ddInleesLocatie'];
$portefeuillePreg = $settings['ddInleesPortefeuillePreg'];


if (strtoupper($locatie) == "AWS")
{
  $dd = new AE_cls_dd_helper(getVermogensbeheerderField("Vermogensbeheerder"));

  if ($dd->error())
  {
    echo "<PRE>";
    print_r($dd->errorArray);
    echo "</PRE>";
    exit;
  }

  $locatie = $dd->path;
}

$deleteFiles = array();
if (is_dir($locatie))
{
    if ($dh = opendir($locatie))
    {
      while ($file = readdir($dh))
      {
        $fullFile = $locatie ."/". $file;
        if(filetype($fullFile) == 'file')
        {
          $deleteFile = checkFile($fullFile,$portefeuillePreg);
          if ($deleteFile)
          {
            $deleteFiles[] = $deleteFile;
          }
        }
      }
      foreach ($deleteFiles as $file)
		  {
		    removeFile($file);
		  }

      echo vtb('Klaar met inlezen %s', array($locatie)) . " <br>\n";
      closedir($dh);
    }
    else
    {
      echo vtb('Geen rechten om %s te openen..', array($locatie)) . " <br>\n";
    }
}
else
{
  echo vtb('Kan locatie: %s niet openen..', array($locatie)) . " <br>\n";
}

function checkFile($filename,$portefeuillePreg='')
{
  $db     = new DB();
  $table  = 'CRM_naw';
  $store  = false;
  $file   = basename($filename);

  preg_match("/^id[\d]*/i",$file, $matches);

  if ( count($matches) > 0 && $matches[0] != '' )
  {
    $id=substr($matches[0],2);
    if ($db->QRecords("SELECT id FROM $table WHERE id = '$id'") > 0 )
    {
      $store = true;
    }
  }

  preg_match("/^[\d]*/i",$file, $matches);

  if (count($matches) > 0 && $matches[0] != '' )
  {
    if ($db->QRecords("SELECT id FROM $table WHERE portefeuille = '".$matches[0]."'") > 0)
    {
      $store        = true;
      $portefeuille = $matches[0];
      $id           = $db->nextRecord();
      $id           = $id['id'];
    }
  }
  if ($portefeuillePreg <> '')
  {
    preg_match($portefeuillePreg,$file, $matches);
    if (count($matches) > 0 && $matches[0] != '')
    {
      if ($db->QRecords("SELECT id FROM $table WHERE portefeuille = '".$matches[0]."'") > 0)
      {
        $store  = true;
        $id     = $db->nextRecord();
        $id     = $id['id'];
      }
    }
  }
  if (substr($file,0,2) == 'id' || substr($file,0,2) == 'ID')
  {
    $file = substr($file, strpos($file, $id) + strlen($id));
  }

  $query = "SELECT id FROM dd_reference WHERE module_id = '$id' AND (description = '".mysql_escape_string($file)."' OR keywords = '".mysql_escape_string($file)."')";
  if ($db->QRecords($query) > 0)
  {
    echo vtb('Document %s is al gekoppeld aan relatieId %s', array($file, $id)) . " <br>\n";
    return false;
  }

  if(!is_writable($filename))
  {
    $store = false;
    echo vtb('Kan document %s niet verwijderen.', array($file)) . " <br>\n";
    echo perm($file)."<br>\n";
  }
  elseif ($store == true && $id > 0)
  {
    $filesize   = filesize($filename);
    $filetype   = mime_content_type($filename);
    $fileHandle = fopen($filename, "r");
    $docdata    = fread($fileHandle, $filesize);
    fclose($fileHandle);
    $dd = new digidoc();
    $rec ["filename"]     = $file;
    $rec ["filesize"]     = "$filesize";
    $rec ["filetype"]     = "$filetype";
    $rec ["description"]  = str_replace($portefeuille,'',str_replace($portefeuille.'_','',$file));
    $rec ["blobdata"]     = $docdata;
    $rec ["keywords"]     = $file;
    $rec ["module"]       = $table;
    $rec ["module_id"]    = $id;
    $dd->useZlib          = true;
    if($dd->addDocumentToStore($rec) == false)
    {
      return false;
    }
    else
    {
      return $filename;
    }
  }
  else
  {
    echo vtb('Kan geen relatie vinden bij document: %s', array($file)) . " \n<br>";
  }
}

function removeFile($filename)
{
	if (!unlink($filename))
	{
	    echo vtb('Kan document %s niet verwijderen.', array($filename)) . " <br>\n";
      echo perm($filename)."<br>\n";
	}
	else
	{
	  echo vtb('Document %s toegevoegd en verwijderd.', array($filename)) . " <br>\n";
	}
}


function perm($file)
{
  $perms = fileperms($file);

  if     (($perms & 0xC000) == 0xC000)    { $info = 's'; } // Socket
  elseif (($perms & 0xA000) == 0xA000)    { $info = 'l'; } // Symbolic Link
  elseif (($perms & 0x8000) == 0x8000)    { $info = '-'; } // Regular
  elseif (($perms & 0x6000) == 0x6000)    { $info = 'b'; } // Block special
  elseif (($perms & 0x4000) == 0x4000)    { $info = 'd'; } // Directory
  elseif (($perms & 0x2000) == 0x2000)    { $info = 'c'; } // Character special
  elseif (($perms & 0x1000) == 0x1000)    { $info = 'p'; } // FIFO pipe
  else                                    { $info = 'u'; } // Unknown

// Owner
  $info .= (($perms & 0x0100) ? 'r' : '-');
  $info .= (($perms & 0x0080) ? 'w' : '-');
  $info .= (($perms & 0x0040) ?
              (($perms & 0x0800) ? 's' : 'x' ) :
              (($perms & 0x0800) ? 'S' : '-'));

  // Group
  $info .= (($perms & 0x0020) ? 'r' : '-');
  $info .= (($perms & 0x0010) ? 'w' : '-');
  $info .= (($perms & 0x0008) ?
              (($perms & 0x0400) ? 's' : 'x' ) :
              (($perms & 0x0400) ? 'S' : '-'));

  // World
  $info .= (($perms & 0x0004) ? 'r' : '-');
  $info .= (($perms & 0x0002) ? 'w' : '-');
  $info .= (($perms & 0x0001) ?
              (($perms & 0x0200) ? 't' : 'x' ) :
              (($perms & 0x0200) ? 'T' : '-'));

  echo $info;

}


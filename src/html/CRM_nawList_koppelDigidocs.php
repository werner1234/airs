<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/02/05 15:40:27 $
    File Versie         : $Revision: 1.4 $

    $Log: CRM_nawList_koppelDigidocs.php,v $
    Revision 1.4  2020/02/05 15:40:27  cvs
    call 4986

    Revision 1.3  2017/01/05 13:36:08  cvs
    call 4986, portaldocu check

    Revision 1.2  2017/01/04 16:23:20  cvs
    call 4986

    Revision 1.1  2016/07/01 12:49:30  cvs
    call 4986



*/


$errors = array();


if ($_FILES["importfile"]["error"] == 0 AND $_FILES["importfile"]["size"] > 0)
{
  $ddTel = 0;
  $ddPortaal = 0;
  $db=new DB();
  $rec=array();

  foreach ($_POST as $key => $val)
  {
    $split = explode("_",$key);
    if ($split[0] == "check")
    {
      $nawRecs[] = $split[1];
    }
  }

  $filename   = $_FILES['importfile']['tmp_name'];
  $file       = $_FILES['importfile']['name'];
  $filesize   = filesize($filename);
  $filetype   = mime_content_type($filename);
  $fileHandle = fopen($filename, "r");
  $docdata    = fread($fileHandle, $filesize);
  fclose($fileHandle);

  $rec ["filename"]    = $file;
  $rec ["filesize"]    = "$filesize";
  $rec ["filetype"]    = "$filetype";
  $rec ["description"] = $_POST['dd_omschrijving'];
  $rec ["blobdata"]    = $docdata;
  $rec ["keywords"]    = "";
  $rec ["module"]      = 'CRM_naw';
  $rec ["categorie"]   = $_POST['dd_categorie'];

  foreach ($nawRecs as $clientId)
  {
    $naw = getNawById($clientId);
    $portefeuille = $naw["portefeuille"];

    $dd = new digidoc();
    $dd->useZlib = false;
    $rec ["module_id"] = $clientId;
    $dd->addDocumentToStore($rec);
    $ddTel++;

    if($_POST['dd_portaal'] == "1")
    {
      if (trim($naw['portefeuille']) == '' AND $naw['CRMGebrNaam'] != '')
      {
        $portefeuille ='P'.str_pad($naw['CRMGebrNaam'], 6, '0', STR_PAD_LEFT);
      }
      else
      {
        $portefeuille = $naw["portefeuille"];
      }

      if ($clientId = checkPortefeuilleInPortal($portefeuille))
      {

        $airsRefId = $dd->referenceId;
        $pRec = $rec;
        $dd = new digidoc(DBportaal);
        $dd->useZlib = false;
        $pRec ["module_id"] = $clientId;
        $pRec ["module"] = 'clienten';
        $extraVelden = array(
          'portaalKoppelId'=>$airsRefId,
          'reportDate'=>date('Y-m-d'),
          'clientID'=>$clientId
        );

        if($dd->addDocumentToStore($pRec,$extraVelden) == false)
        {
          $error[] =  $portefeuille.": Niet gelukt om document in de portaal te plaatsen.<br>\n";flush(); ob_flush();
        }
        else
        {
          $ddPortaal++;
          $query = "UPDATE dd_reference SET portaalKoppelId='" . $dd->referenceId . "' WHERE id='$airsRefId'";
          $db->executeQuery($query);
        }
      }
      else
      {
        $error[] = "portefeuille ".$portefeuille.": bestaat niet in portaal";
      }
    }


  }
  echo "<li> $ddTel relaties gekoppeld aan document";
  if ($_POST['dd_portaal'] == "1")
  {
    echo "<li> $ddPortaal documunten naar portaal verstuurd";
  }
  if (count($error) > 0 )
  {
    echo "<li>".implode("<li>", $error);
  }
  
}
else
{
  echo "geen geldig bestand gekoppeld";
}

function checkPortefeuilleInPortal($portefeuille)
{
  $dbPortaal = new DB(DBportaal);
  $query="SELECT id FROM clienten WHERE portefeuille='".mysql_real_escape_string($portefeuille)."'";

  $clientData = $dbPortaal->lookupRecordByQuery($query);
  if((int) $clientData['id'] < 1 )
  {
    return false;
  }
  else
  {
    return $clientData['id'];
  }


}




function getNawById($id)
{
  $db = new DB();
  $q = "SELECT * FROM CRM_naw WHERE id=$id";

  if ($rec = $db->lookupRecordByQuery($q) )
  {
    return $rec;
  }
  else
  {
    return false;
  }

}


?>
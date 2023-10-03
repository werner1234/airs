<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/10/24 11:38:24 $
    File Versie         : $Revision: 1.6 $

    $Log: AIRS_cls_CRM_naw_importHelper.php,v $
    Revision 1.6  2018/10/24 11:38:24  cvs
    call 6713

    Revision 1.5  2018/03/12 10:28:44  cvs
    call 6713

    Revision 1.4  2017/11/17 11:01:58  cvs
    call 6145

    Revision 1.3  2017/11/17 08:00:22  cvs
    call 6145

    Revision 1.2  2017/11/13 13:32:43  cvs
    call 6145 bevindingen

    Revision 1.1  2017/11/08 07:30:40  cvs
    call 6145



*/

class CRM_naw_importHelper
{

  var $prefix;
  var $hiddenFields;
  var $crmFields;
  var $fieldTypes;
  var $dateInputFormat;
  var $nummericInputFormat;
  var $error;
  var $profile;
  var $settings;
  var $profileNames;
  var $db;
  var $user;
  var $trackAndTraceQueue;
  var $externId;
  var $airsId;
  var $CRM_nawRec = array();
  var $csvLog = "";
  var $dryrun;
  var $importFields;

  function CRM_naw_importHelper($profileName="default")
  {
    global $USR;
    $this->externId = -1;
    $this->airsId = -1;
    $this->dryrun = false;
    $this->user = $USR;
    $this->db = new DB();
    $this->prefix = "CRM_nawImport_";
    $this->profile = $profileName;

    $this->loadProfile($profileName);


    $this->hiddenFields = array(

      "change_user",
      "change_date",
      "add_user",
      "add_date",
      "aktief",
      "rvvtest",
      "id",
      "externID"
    );

    $this->loadProfileNames();

    $this->fieldTypes = array("Tekst","Numeriek","Datum");
    $this->getCRM_nawFields();

  }

  function loadProfileNames()
  {
    $cfg = new AE_config();
    $raw = trim($cfg->getData("CRM_naw_profiles"));
    $this->profileNames = explode("||", $raw);
    array_unshift($this->profileNames, "default");
  }

  function saveProfileNames()
  {
    $cfg = new AE_config();
    $raw =array();
    sort($this->profileNames);
    foreach ($this->profileNames as $item)
    {
      if (trim($item) != "" AND trim($item) != "default" AND !in_array(trim($item), $raw))
      {
        $raw[] = trim($item);
      }
    }
    $cfg->addItem("CRM_naw_profiles", implode("||", $raw));
  }

  function loadProfile()
  {
     $cfg = new AE_config();
     $raw = $cfg->getData($this->prefix.$this->profile);
     $this->settings = unserialize($raw);
  }

  function saveProfile()
  {
    $cfg = new AE_config();
    $cfg->addItem($this->prefix.$this->profile, serialize($this->settings));
  }

  function getSetting($field)
  {
    return $this->settings[$field];
  }

  function setSetting($field, $value)
  {
    $this->settings[$field] = $value;
  }

  function mapColumns($headerArray)
  {
    $headKeys   = array();
    $matrix     = array();
    $fieldsUsed = array();
    $notUsed    = array();
    $this->externId = -1;

    for ($x=0; $x<count($headerArray);$x++)
    {
      $headKeys[$headerArray[$x]] = $x;
    }
//    debug($headKeys);

    $columMapping = $this->getSetting("columMapping");

    foreach ($columMapping as $k=>$v)
    {
      if ($k == "externID")
      {
        $this->externId = $headKeys[$v["field"]];
      }
      if ($k == "id")
      {
        $this->airsId = $headKeys[$v["field"]];
      }

      $matrix[$k] = array("crmField"=>$k, "type" => $v["type"],"importField" => $v["field"],"col"=>$headKeys[$v["field"]]);
      $fieldsUsed[] = $v["field"];
      //$matrix[$headKeys[$v["field"]]] = array("crmField"=>$k, "type" => $v["type"],"importField" => $v["field"]);
    }
    foreach ($headerArray as $fld)
    {
      if(!in_array($fld, $fieldsUsed))
      {
        $notUsed[] = $fld;
      }
    }
    return array($matrix,$notUsed);
  }

  function showSettings()
  {
    debug($this->settings,"import instellingen");
  }

  function setupConverters($postArray)
  {
    $this->error = array();
    $this->dateInputFormat = array();
    $this->nummericInputFormat = $postArray["decimalChar"];
    $dateFormat = "*".$postArray["dateFormat"];

    if ($postArray["dateDelimiter"] == "geen")
    {
      if (strlen($postArray["dateFormat"]) != 8)
      {
        $this->error[] = "datum format niet 8 lang";
      }
      else
      {
        $Y = stripos($dateFormat,"YYYY");
        if (  $Y !== false)
        {
          $this->dateInputFormat["Y"] = array($Y-1,4);
        }
        else
        {
          $this->error[] = "incorrect jaar format";
        }


        $M = stripos($dateFormat,"MM");
        if ( $M !== false)
        {
          $this->dateInputFormat["M"] = array($M-1,2);
        }
        else
        {
          $this->error[] = " incorrect maand format";
        }
        $D = stripos($dateFormat,"DD");
        if ( $D !== false)
        {
          $this->dateInputFormat["D"] = array($D-1,2);
        }
        else
        {
          $this->error[] = " incorrect dag format";
        }
      }
    }
    else
    {
      $split = explode($postArray["dateDelimiter"], $postArray["dateFormat"]);
      $count = -1;
      $formatArray = array("L"=>$postArray["dateDelimiter"]);
      foreach ($split as $item)
      {
        $count++;
        switch(strtolower(substr($item, 0,1)))
        {
          case "d":  $formatArray["D"] = $count;    break;
          case "m":  $formatArray["M"] = $count;    break;
          case "y":  $formatArray["Y"] = $count;    break;
          default:
        }
      }
      if (count($formatArray) != 4)
      {
        $this->error[] = " incorrect datum format";
      }
      else
      {
        $this->dateInputFormat["L"] = $formatArray;
      }
    }

    return (count($this->error) == 0);
  }

  function showErrors()
  {
     return "<li>".implode("</li><li>",$this->error)."</li>";
  }

  function convertDate($inDate)
  {
    $inDate = trim($inDate);

    if ($this->dateInputFormat["L"] != "")
    {
      $fArray = $this->dateInputFormat["L"];
      $theDate = explode($fArray["L"], $inDate);
      $yy = $theDate[$fArray["Y"]];
      $mm = $theDate[$fArray["M"]];
      $dd = $theDate[$fArray["D"]];
    }
    else
    {
      $yy = substr($inDate,$this->dateInputFormat["Y"][0],$this->dateInputFormat["Y"][1]);
      $mm = substr($inDate,$this->dateInputFormat["M"][0],$this->dateInputFormat["M"][1]);
      $dd = substr($inDate,$this->dateInputFormat["D"][0],$this->dateInputFormat["D"][1]);
    }

    if ((int)$yy < 100)  {  $yy = 2000 + (int)$yy;  }
    if ((int)$mm < 10)   {  $mm = "0".(int)$mm;     }
    if ((int)$dd < 10)   {  $dd = "0".(int)$dd;     }

    return $yy."-".$mm."-".$dd;
  }

  function convertNummeric($inNum)
  {
    $inNum = trim($inNum);
    if ($this->nummericInputFormat == ".")
    {
      $clean = preg_replace("/[^0-9.]/", "", $inNum);  // . als decimaal
    }
    else
    {
      $clean = preg_replace("/[^0-9,]/", "", $inNum);  // , als decimaal
      $clean = str_replace(",", ".", $clean);
    }
    return $clean;
  }

  function getCRM_nawFields()
  {
    global $_DB_resources;
    $this->crmFields = array();
    include_once "records/CRM_naw.php";
    $crm = new naw();

    $db = new DB();
    $query = "
      SELECT 
        COLUMN_NAME AS field
      FROM 
        INFORMATION_SCHEMA.COLUMNS
      WHERE 
        table_name = 'CRM_naw' AND 
        table_schema = '".$_DB_resources[1]['db']."'
      ORDER BY
        COLUMN_NAME";
     $db->executeQuery($query);

     while($rec = $db->nextRecord())
     {
       if (!in_array($rec["field"], $this->hiddenFields))
       {
         $this->crmFields[$rec["field"]] = $crm->data["fields"][$rec["field"]]["description"];
       }
     }
  }

  function getInput($field, $value)
  {
     $options = array(
       "delimiter"     => array(",|komma gescheiden","tab|tab gescheiden",";|puntkomma gescheiden"),
       "decimalChar"   => array(".|punt",",|komma"),
       "dateDelimiter" => array("-|-","/|/","geen|geen delimter"),
       "koppelMethode" => array("externId|extern ID","airsId|AIRS id"),
     );

     switch ($field)
     {
       case "delimiter":
       case "decimalChar":
       case "dateDelimiter":
       case "koppelMethode":

         $out = "<select name='$field' id='$field' >";
         foreach ($options[$field] as $item)
         {
           $i = explode("|",$item);
           $selected = ($i[0] == $value)?"SELECTED":"";
           $out .= "\n\t<option value='{$i[0]}' $selected>{$i[1]}</option>";
         }
         $out .= "\n</select>";
         break;
       case "profile":
         $out = "<select name='$field' id='$field' >";
         foreach ($this->profileNames as $item)
         {
           $out .= "\n\t<option value='{$item}' >{$item}</option>";
         }
         $out .= "\n</select>";
         break;
       default:
         $out = "<input name='$field' id='$field' value='$value' />";
     }
     return $out;
  }

  function fieldMappingHtml($importFields, $methode)
  {
    $this->importFields = $importFields;
    $ind = 0;
    foreach ($importFields as $field)
    {
      $importIndex[$ind] = $field;
      $ind++;
    }
    $outFilled = array();
    $out       = array();
    $importMapping = $this->getSetting("columMapping");
//debug($importMapping);
    if ($methode == "airsId")
    {
      $outFilled[] = "
      <tr >
        <td style='color:maroon; font-weight: bold'>id (".$this->crmFields['id'].")</td>
        
        <td>
          <select name='import_id' id='import_id'>".$this->importOptions($importMapping["id"]["field"])."</select>
        </td>
        <td>
          <select name='type_id'>".$this->typeOptions($importMapping["id"]["type"])."</select>
        </td>
      </tr>";
    }
    else
    {
      unset($importMapping["id"]); // evt id mapping verwijderen
    }

    $outFilled[] = "
      <tr >
        <td style='color: maroon; font-weight: bold'>externId (".$this->crmFields['externId'].")</td>
        
        <td>
          <select name='import_externID' id='import_externID'>".$this->importOptions($importMapping["externID"]["field"])."</select>
        </td>
        <td>
          <select name='type_externID'>".$this->typeOptions($importMapping["externID"]["type"])."</select>
        </td>
      </tr>";

    foreach ($this->crmFields as $crmField=>$description)
    {
      if ($crmField == "id" OR $crmField == "externID")  // deze worden hierboven geforceerd getoond
      {
        continue;
      }
      if ($crmField == "portefeuille")
      {
        $out[] = "
      <tr>
        <td>$crmField ($description)</td>
        
        <td style='color: maroon; font-weight: bold'>
          Deze mag niet extern gewijzigd worden!
        </td>
      </tr>";
        continue;
      }

      $selectedImportType = $importMapping[$crmField]["type"];
      $selectedImportField = $importMapping[$crmField]["field"];

      if (!is_null($selectedImportField))
      {

        $outFilled[] = "
      <tr >
        <td >$crmField ($description)</td>
        
        <td>
          <select name='import_$crmField' id='import_$crmField'>".$this->importOptions($selectedImportField)."</select>
        </td>
        <td>
          <select name='type_$crmField'>".$this->typeOptions($selectedImportType)."</select>
        </td>
        <td>
          <button class='btnRelease' data-id='$crmField' title='ontkoppel veld'>X</button>
        </td>
      </tr>";
      }
      else
      {
        $out[] = "
      <tr>
        <td>$crmField ($description)</td>
        
        <td>
          <select name='import_$crmField' id='import_$crmField'>".$this->importOptions($selectedImportField)."</select>
        </td>
        <td>
          <select name='type_$crmField'>".$this->typeOptions($selectedImportType)."</select>
        </td>
        <td></td>
      </tr>";
      }

    }
    return implode("\n", $outFilled)." <tr class='filledRow' ><td >&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>  ".implode("\n", $out);

  }

  function getCRM_nawRecordByExternId($externId)
  {
    $this->CRM_nawRec = array();
    $query = "SELECT * FROM CRM_naw WHERE externID = '$externId'";

    return ($this->CRM_nawRec = $this->db->lookupRecordByQuery($query));
  }
  function getCRM_nawRecordByAirsId($airsId)
  {
    $this->CRM_nawRec = array();
    $query = "SELECT * FROM CRM_naw WHERE id = '$airsId'";
//    debug($query);
    return ($this->CRM_nawRec = $this->db->lookupRecordByQuery($query));
  }

  function resetTrackAndTrace()
  {
    $this->trackAndTraceQueue = array();
  }

  function queueTrackAndTrace($field, $newValue)
  {
    $cRec = $this->CRM_nawRec;

    if ($cRec[$field] != $newValue)
    {
      $this->trackAndTraceQueue[] = array(
        'CRM_naw',
        $cRec["id"],
        $field,
        $cRec[$field],
        $newValue,
        $this->user
      );
    }
  }

  function commitTrackAndTrace($CRM_nawId,$externId,$soort,$zoekveld="")
  {
    foreach ($this->trackAndTraceQueue as $item)
    {
      if ($CRM_nawId != 0)
      {
        $item[1] = (int)$CRM_nawId;   // id CRM record
        $crmId = (int)$CRM_nawId;
      }
      if (!$this->dryrun)
      {
        addTrackAndTrace($item[0],$item[1],$item[2],$item[3],$item[4],$item[5]);
      }


    }
    $this->csvLog[] = array($CRM_nawId,$externId,$soort,$zoekveld);
  }

  function importOptions($selectedImportField="")
  {
    $importOptions = "\n<option value=''></option>";
    foreach ($this->importFields as $iField)
    {
      $iSelected = ($iField == $selectedImportField)?"SELECTED":"";
      $importOptions .= "\n<option value='$iField' $iSelected>$iField</option>";
    }
    return $importOptions;
  }

  function typeOptions($selectedImportType="")
  {
    $typeOptions = "";
    foreach ($this->fieldTypes as $tField)
    {
      $selected = ($tField == $selectedImportType)?"SELECTED":"";
      $typeOptions .= "\n<option value='$tField' $selected>$tField</option>";
    }
    return $typeOptions;
  }

}


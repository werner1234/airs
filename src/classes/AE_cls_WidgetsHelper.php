<?php

/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/02/01 12:44:20 $
    File Versie         : $Revision: 1.4 $

    $Log: AE_cls_WidgetsHelper.php,v $
    Revision 1.4  2018/02/01 12:44:20  cvs
    update naar airsV2

    Revision 1.3  2017/12/15 13:52:21  cvs
    call 6257

    Revision 1.2  2017/05/29 11:58:15  rm
    widget

    Revision 1.1  2017/05/29 07:51:23  cvs
    no message

    Revision 1.2  2017/01/13 09:31:11  cvs
    4 widgets werkend

    Revision 1.1  2016/12/22 09:42:24  cvs
    call 4830 eerste commit



*/

class AE_cls_WidgetsHelper
{
  var $columnData;
  var $columnSettings;
  var $settingsField;
  var $widthMultiplier = 1;
  var $user;
  var $db;
  var $cfg;
  var $uid;
  var $htmlInputTemplate = '<li>{checkbox} {omschrijving} ({veldnaam}) </li>';



  function makeAccessOptions($value)
  {

   $values = array(
     "alle"  => vt("Alle portefeuilles"),
     "eigen" => vt("Eigen portefeuilles"),
     "accMan" => vt("Eigen portefeuilles & 2e aanspreekpunt")
   );

   foreach ($values as $k=>$item)
   {
      $selected = ($value == $k)?"SELECTED":"";
      $out .= "<option value='$k' $selected>$item</option>";
   }
   return $out;

  }

  function AE_cls_WidgetsHelper($columnData, $settingsField)
  {
    $this->db = new DB();
    $this->cfg = new AE_config();
    $this->user = $_SESSION["USR"];
    $this->columnData = $columnData;
    $this->settingsField = $settingsField;
    $this->uid = rand(11111,99999);
    $this->getSettings();
  }

  function resetSettings()
  {
     $settingsArray = array();
     foreach ($this->columnData as $k=>$v)
     {

        $flag = $v["fixed"] != 1?0:1;
        $settingsArray[$k] = $flag;
     }
     $this->cfg->addItem($this->settingsField, serialize($settingsArray));
  }

  function getSettings()
  {
    $kol = 0;  // aantal kolomen
    $totWidth = 0;
    $this->columnSettings = unserialize($this->cfg->getData($this->settingsField));
    if (!$this->columnSettings)
    {
      $this->columnSettings = array();  // verkomt looping
      $this->resetSettings();
      $this->getSettings();
    }
    foreach ($this->columnSettings as $k=>$v)
    {
      $this->columnData[$k]["show"] = $v;  // verwerk settings in de kolomdata
      if ($v == 1)
      {
        $kol++;
        $totWidth += $this->columnData[$k]["width"];
      }

    }
    $this->widthMultiplier = 100/$totWidth;
  }

  function getWidth($in)
  {
    $w = round(($this->widthMultiplier * $in),1)-0.1;
    return " style='width:".$w."% !important;' ";
  }

  function makeHtmlInput()
  {
    $tel =0;
    $kolomList = '';
    $out = "<fieldset><legend>" . vt('Kolommen selecteren') . "</legend>";
    $templ = new AE_template();
    $templ->loadTemplateFromString($this->htmlInputTemplate,"kolom");
    foreach ($this->columnData as $k=>$v)
    {
      $data = array(
        "veldnaam"     => $v["koptxt"],
        "omschrijving" => $v["title"]
      );
//      debug($v,$k);
      if ($v["fixed"] != 1)
      {
        $tel++;
        $selected = ($v["show"] == 1)?"checked":"";
        $data["checkbox"] = "<input type='checkbox' data-ae='$tel' class='kolCheck".$this->uid."' id='checkbx_$k' $selected/>";
      }
      else
      {
        $data["checkbox"] = "<input type='checkbox' data-ae='$tel' class='kolCheck".$this->uid."' id='checkbx_$k' DISABLED checked/>";
      }
      $kolomList .= $templ->parseBlock("kolom", $data);
    }
    $out .= '<ul style="list-style: none;">' . $kolomList . '</ul><br />';
    return $out."</fieldset>";
  }

}
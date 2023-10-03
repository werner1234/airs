<?php

/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/05/13 13:22:51 $
    File Versie         : $Revision: 1.2 $

    $Log: AE_cls_Morningstar.php,v $
    Revision 1.2  2019/05/13 13:22:51  cvs
    call 7630

    Revision 1.1  2019/04/24 14:11:41  cvs
    call 7630


*/

class AE_cls_Morningstar
{
  var $db;
  var $cfg;
  var $user;
  var $bedrijfscode;
  var $portefeuille;
  var $vb;
  var $level = 0;
  var $levelNames = array(
    '0' => '--',
    '1' => 'Standaard',
    '2' => 'MS Direct',
    '3' => 'MS Docu',
    '4' => 'MS Compleet'
  );
  var $doorkijkStandaard = array(
    "Beleggingscategorien",
    "Beleggingssectoren",
    "Regios",
    "Valutas",
  );

  function AE_cls_Morningstar($portefeuille = "")
  {
    global $__appvar;
    $this->db    = new DB();
    $this->cfg   = new AE_config();
    if ($portefeuille == "")
    {
      $this->vb    = $this->getVermogensbeheerderField("Vermogensbeheerder");
      $this->level = $this->getVermogensbeheerderField("morningstar");
      if (isset($_SESSION["USR"]))
      {
        $this->user  = $_SESSION["USR"];
      }
    }
    else
    {
      $this->portefeuille = $portefeuille;
      $this->vb    = $this->getVermogensbeheerderFieldPerPortefeuille("Vermogensbeheerder");
      $this->level = $this->getVermogensbeheerderFieldPerPortefeuille("morningstar");
      $this->user  = "api";

    }


    $this->bedrijfscode = $__appvar["bedrijf"];

  }

  function allowed()
  {
    if ($this->bedrijfscode == "HOME")
    {
      return true;
    }

    $allowed = false;
    $arg_list = func_get_args();
    foreach ($arg_list as $allow)
    {
      if ($allow == $this->level)
      {
        $allowed = true;
      }
    }
    return $allowed;
  }

  function doorkijkFilter()
  {
    if ($this->bedrijfscode == "HOME")
    {
      return "";
    }
    $showall = false;
    $arg_list = func_get_args();
    foreach ($arg_list as $allow)
    {
      if ($allow == $this->level)
      {
        $showall = true;
      }
    }
    if ($showall)
    {
      return "";
    }
    else
    {
      return " AND msCategoriesoort IN ('".implode("','", $this->doorkijkStandaard)."') ";
    }
  }


  function getVermogensbeheerderField($field)
  {
    global $USR;
    $db = new DB();
    $query = "
        SELECT 
          Vermogensbeheerders.$field as veld 
        FROM 
          Vermogensbeheerders
        Inner Join VermogensbeheerdersPerGebruiker ON 
          Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
        WHERE 
           VermogensbeheerdersPerGebruiker.Gebruiker='$USR'";

    $tmp=$db->lookupRecordByQuery($query);
    return $tmp['veld'];
  }

  function getVermogensbeheerderFieldPerPortefeuille($field)
  {
    global $USR;
    $db = new DB();
    $query = "
        SELECT 
          Vermogensbeheerders.$field as veld 
        FROM 
          Vermogensbeheerders
        Inner Join Portefeuilles ON 
          Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
        WHERE 
           Portefeuilles.Portefeuille='".$this->portefeuille."'";

    $tmp=$db->lookupRecordByQuery($query);
    return $tmp['veld'];
  }

}
<?php
/*
    AE-ICT sourcemodule created 17 aug. 2022
    Author              : Chris van Santen
    Filename            : AE_cls_dd_helper.php


*/

Class AE_cls_dd_helper
{
  var $path       = "/var/www/data/dd_inlees";
  var $develop    = false;
  var $errorSet   = false;
  var $errorArray = array();

  function AE_cls_dd_helper($vb="dummy")
  {
    global $__develop;
    if ($__develop )
    {
      $this->path    = "/develop/php/chris/AIRS2022_losse_branches/data/dd_inlees";
      $this->develop = true;
    }

    if ($_SESSION["dd_path"] != "")
    {
      $this->path = $_SESSION["dd_path"];
    }
    else
    {
      $db               = new DB();
      $query            = "SELECT * FROM Vermogensbeheerders WHERE ddInleesLocatie <> '' AND `Vermogensbeheerder` = '{$vb}'";
      $settings         = $db->lookupRecordByQuery($query);
      $directory        = $settings['ddInleesLocatie'];
      if (strtoupper($directory) != "AWS")
      {
        $this->setError("niet ingeregeld voor AWS");
        exit;
      }
      else
      {
        $directory = $this->path;
        if (!is_dir($directory))
        {
          mkdir($directory);
        }
        if (is_dir($directory))
        {
          $_SESSION["dd_path"] = $directory;
          return $directory;
        }
        else
        {
          $this->setError("FOUT: kan map niet aanmaken!");
          return false;
        }

      }

    }
  }



  function showInfo()
  {
    return var_export($this, true);
  }

  function setError($txt)
  {
    $this->errorArray[] = $txt;
    $this->errorSet = true;
  }

  function error()
  {
    return $this->errorSet;
  }




} 		


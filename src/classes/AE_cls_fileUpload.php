<?PHP
/*
    AE-ICT sourcemodule created 21 aug. 2019
    Author              : Chris van Santen
    Filename            : AE_cls_fileUpload.php

    $Log: AE_cls_fileUpload.php,v $
    Revision 1.2  2019/08/23 11:43:22  cvs
    call 8024

*/


class AE_cls_fileUpload
{

  var $badExtensions = array(
    "php",
    "exe",
    "sh",
    "vbs",
    "vb",
    "html",
    "cgi",
    "cmd",
    "dll"
  );
  
  function AE_cls_fileUpload()
  {
    
  }

  function checkExtension($filename, $allowedExtensions = array())
  {
    $fParts = explode(".", strtolower($filename));
    $indx = count($fParts)-1;
    $extension = $fParts[$indx];

    if (count($allowedExtensions) > 0)
    {
//      debug($allowedExtensions,$extension);
      return in_array($extension, $allowedExtensions); // true als extensie gevonden
    }
    else
    {
//      debug($this->badExtensions,$extension);
      return !in_array($extension, $this->badExtensions); // true als extensie niet in badExtensions
    }
  }
  
}
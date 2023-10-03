<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2009/03/09 08:13:00 $
 		File Versie					: $Revision: 1.4 $

 		$Log: debugSpecial.php,v $
 		Revision 1.4  2009/03/09 08:13:00  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2006/11/03 11:25:29  rvv
 		extra debug info

 		Revision 1.2  2006/10/31 13:36:06  cvs
 		*** empty log message ***

 		Revision 1.1  2006/10/31 12:42:54  rvv
 		user update


*/

function debugSpecial($input="",$file,$regel)
{

  global $__appvar, $__debugSpecialIsSet,$USR;

  if ($__debugSpecialIsSet == true)
  {
    $writeFilename = $__appvar["tempdir"]."debugSpecial.log";

    if (!$writeHandle = fopen($writeFilename, 'a+'))
    {
      echo "Cannot open file ($writeHandle)";
      return false;
    }
    $fpath = str_replace("\\","/",$file);
    $parts = explode("/",$file);
    $file = $parts[count($parts)-1];
    if (is_array($input))
    $input = var_export($input,true);
    $outp = date("Ymd H:i ").$file." (".$regel.") ".$input;
    fwrite($writeHandle, $outp."\n");
    fclose($writeHandle);


  }
  return true;

}


?>
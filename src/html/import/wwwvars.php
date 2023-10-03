<?php
if (file_exists("../../config/local_vars.php"))
{
	/*
		normale app
	*/

  if (version_compare(phpversion(), '5.3.0', '<'))
  {
    include_once("AE_lib2.php3");
  }


	include_once("../../config/local_vars.php");
	include_once("../../config/vars.php");
	include_once("../../config/auth.php");
}
else if (file_exists("../config/tn_vars.php"))
{
	/*
		superapp vars
	*/
  if (version_compare(phpversion(), '5.3.0', '<'))
  {
    include_once("AE_lib2.php3");
  }

	include_once("../../config/tn_vars.php");
	include_once("../../config/vars.php");
	include_once("../../config/auth.php");
	
}
else 
{
	header("Location: ../setup.php");
	exit;
}
?>
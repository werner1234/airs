<?php
/*
    AE-ICT sourcemodule created 10 jul. 2020
    Author              : Chris van Santen
    Filename            : wwwvars.php

    $Log: wwwvars.php,v $
    Revision 1.1  2020/07/10 13:56:42  cvs
    call 8750


*/

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
else
{
	header("Location: ../setup.php");
	exit;
}

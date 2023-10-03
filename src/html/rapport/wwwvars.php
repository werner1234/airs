<?php
/*
Author  						: $Author: cvs $
Laatste aanpassing	: $Date: 2018/09/23 17:14:23 $
File Versie					: $Revision: 1.2 $

$Log: wwwvars.php,v $
Revision 1.2  2018/09/23 17:14:23  cvs
call 7175

Revision 1.1  2005/06/30 08:22:56  jwellner
Rapportage toegevoegd

*/
if (file_exists("../../config/local_vars.php"))
{
	/*
		normale app
	*/
	include_once("AE_lib2.php3");

	include_once("../../config/local_vars.php");


	include_once("../../config/vars.php");
	include_once("../../config/auth.php");
}
else if (file_exists("../config/tn_vars.php"))
{
	/*
		superapp vars
	*/
	include_once("AE_lib2.php3");
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
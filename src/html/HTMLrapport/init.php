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

	include_once $__appvar["basedir"]."/config/vars.php";

	include_once $__appvar["basedir"]."/config/auth.php";

}
else 
{
	header("Location: setup.php");
	exit;
}

$rapportBackButtons = '';
if( ! isset ($__appvar["crmOnly"]) )
{
//	$rapportBackButtons .= '<a href="../CRM_nawList.php?sql=debiteur" class="btn-new btn-default  pull-right"> <i class="fa fa-address-card-o" aria-hidden="true"></i>' . vt('Mijn cliënten') . ' </a>';
	$rapportBackButtons .= '<a href="../rapportFrontofficeClientSelectie.php" class="btn-new btn-default  pull-right"> <i class="fa fa-line-chart" aria-hidden="true"></i> ' . vt('Klantrapportage') . '</a>';
}

?>
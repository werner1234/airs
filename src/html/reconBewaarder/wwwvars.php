<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2017/09/20 06:21:04 $
    File Versie         : $Revision: 1.1 $

    $Log: wwwvars.php,v $
    Revision 1.1  2017/09/20 06:21:04  cvs
    megaupdate 2722



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
	include_once("recon_functies.php");
  include_once('../../classes/AE_cls_progressbar.php');
  include_once('../../classes/reconcilatieBewClass.php');
  
}
?>
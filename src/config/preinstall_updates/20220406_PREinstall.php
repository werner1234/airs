<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/12/13 12:34:57 $
 		File Versie					: $Revision: 1.2 $

 		$Log: 20141203_PREinstall.php,v $
 		Revision 1.2  2014/12/13 12:34:57  rvv
 		*** empty log message ***

 		Revision 1.1  2014/08/09 15:05:04  rvv
 		*** empty log message ***



*/
include_once("wwwvars.php");

if(file_exists("../classes/records/FondsParticipatieVerloop.php"))
{
  unlink("../classes/records/FondsParticipatieVerloop.php");
}

?>
<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2012/06/30 12:51:47 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20120412_PREinstall.php,v $
 		Revision 1.1  2012/06/30 12:51:47  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/12/31 18:13:52  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/11/23 19:09:29  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/05/25 11:09:36  rvv
 		*** empty log message ***

 		Revision 1.1  2011/05/04 16:25:11  rvv
 		*** empty log message ***


*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

if($__appvar["bedrijf"]=='WAT')
{
  $db=new DB();
  $query="UPDATE BestandsvergoedingPerPortefeuille SET bedragUitbetaald=bedragBerekend,change_user='sys',change_date=now() WHERE bedragUitbetaald <> 0 AND datumUitbetaald <> '0000-00-00' ";
  $db->SQL($query);
  $db->Query();
}

?>

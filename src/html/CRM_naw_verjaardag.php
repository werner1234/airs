<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/12/11 10:57:35 $
 		File Versie					: $Revision: 1.16 $

 		$Log: CRM_naw_verjaardag.php,v $
 		Revision 1.16  2011/12/11 10:57:35  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2011/11/23 18:54:09  rvv
 		*** empty log message ***

 		Revision 1.14  2011/11/09 18:50:11  rvv
 		*** empty log message ***

 		Revision 1.13  2011/05/08 09:34:53  rvv
 		*** empty log message ***

 		Revision 1.12  2011/05/04 16:28:41  rvv
 		*** empty log message ***

 		Revision 1.11  2011/03/02 20:11:35  rvv
 		*** empty log message ***

 		Revision 1.10  2010/12/05 09:51:02  rvv
 		*** empty log message ***

 		Revision 1.9  2010/08/04 15:25:56  rvv
 		*** empty log message ***

 		Revision 1.8  2010/05/30 11:50:01  rvv
 		*** empty log message ***

 		Revision 1.7  2010/05/23 14:02:56  rvv
 		*** empty log message ***

 		Revision 1.6  2010/05/02 10:16:05  rvv
 		*** empty log message ***

 		Revision 1.5  2010/04/07 13:12:06  rvv
 		*** empty log message ***

 		Revision 1.4  2010/02/14 12:33:09  rvv
 		*** empty log message ***

 		Revision 1.3  2010/01/06 16:46:50  rvv
 		*** empty log message ***

 		Revision 1.2  2007/11/27 13:19:18  cvs
 		CRM
 		- verjaardaglijst
 		- velden omzetten van extra velden naar naw
 		- excel van tijdelijke rekening mutaties

 		Revision 1.1  2007/10/09 06:27:49  cvs
 		CRM update DGC


*/

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();
$DB = new DB();

$mainHeader = "Verjaardagslijst";

foreach ($_POST as $key=>$value)
{
  if(substr($key,0,6)=='kaart_')
  {
    $data=explode("_",substr($key,6));
    if(!isset($data[2]))
    {
      if($data[0]==1)
        $veld="kaartVerstuurd";
      elseif($data[0]==2)
        $veld="kaartVerstuurdPartner";
      $query="UPDATE CRM_naw SET $veld=NOW() WHERE id='$data[1]'";
    }
    else
    {
      $query="UPDATE CRM_naw_adressen SET kaartVerstuurd=NOW() WHERE id='$data[0]' AND rel_id='$data[1]'";
    }
    $DB->SQL($query);
    $DB->Query();
  }
}

$_SESSION[NAV] = null;

$content[pageHeader] = "<br><div class='edit_actionTxt'>
&nbsp;  <b>$mainHeader</b> $subHeader
</div><br>";

$content['javascript'] .= "";

//echo $__appvar["basedir"].$__appvar["templateContentHeader"];
echo template($__appvar["templateContentHeader"],$content);



  ?><form method="POST" ><?
echo createVerjaardagslijst();
  ?><br><br><input type="submit" value="verwerk kaartselectie." ></form><?






$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem("terug",$_SESSION['savedReturnUrl']);
if($_POST)
  $_SESSION['savedReturnUrl'] = $_SERVER["REQUEST_URI"];

?>

<?
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>
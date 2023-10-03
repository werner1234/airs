<?php
/* 	
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/03/06 15:11:46 $
 		File Versie					: $Revision: 1.8 $
 		
 		$Log: index.php,v $
 		Revision 1.8  2020/03/06 15:11:46  cvs
 		call 8437
 		
 		Revision 1.7  2019/04/12 07:24:09  cvs
 		x
 		
 		Revision 1.6  2017/01/05 14:09:56  cvs
 		call 5542 tweede update
 		
 		Revision 1.5  2009/01/20 17:46:01  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2006/06/08 14:47:14  cvs
 		*** empty log message ***
 		
 	
*/
// set module naam voor autenticatie leeg = iedereen.
$__appvar["module"] = "";
// SET in wwwvars ie:  $__appvar["userLevel"] = _READ;
// include wwwvars
include_once("wwwvars.php");

$cfg = new AE_config();
$tfa = ($cfg->getData("wwBeleid_2factor") == "tfa");

if (!$_SESSION["2factor"]["passed"] AND $tfa)
{
  include_once "../classes/AE_cls_template.php";
  include_once "../classes/AE_cls_2factor.php";
  $uId = $_SESSION["usersession"]["gebruiker"]["id"];
  $twof = new AE_cls_2factor($uId);
  //$twof->postUrl = "_a.php";
//debug($_POST);

  if ($_POST["action"] == "2factor")
  {

    if (!$twof->checkLogin($_POST))
    {
      header("location:".$PHP_SELF);
      exit;
    }
  }
  else
  {
    echo template($__appvar["templateContentHeader"],$content);
    echo $twof->pushLogin();
    echo "<div style='text-align: center'><a href='login.php?logout=true'>reset login</a></div>";
    echo template($__appvar["templateRefreshFooter"],$content);
    exit;
  }
}


include_once("mainmenu.php");

$crmUrl = false;

if ($_GET["goCRM"] != "")
{
  $crmUrl = "CRM_nawList.php?sql=all&page=1&selectie=".$_GET["goCRM"];
}
$content = array();
$content["style"] = '<link href="style/main.css" rel="stylesheet" type="text/css" media="screen">';
$content["menuList"] = $menuList;
session_start();

$tgc = new AE_cls_toegangsControle();
if ($tgc->blacklisted)
{
  header("Location: blocked.php");
  exit;
}

//$content["initial_content"]="welcomeNw.php";

if ($_SESSION["wwb_WWchange"])
{
  $content["initial_content"]="wwb_wachtwoordWijzigen.php";
}



echo template($__appvar["templateHeader"],$content);
?>

<script>
<?
  if ($crmUrl)
    {
?>
      setTimeout(function()
      {
        window.open('<?=$crmUrl?>','content');
      }, 800);
<?
    }
?>
</script>
<?
echo template($__appvar["templateFooter"],$content);
?>
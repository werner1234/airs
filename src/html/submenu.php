<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/06/02 14:20:13 $
 		File Versie					: $Revision: 1.29 $

 		$Log: submenu.php,v $
 		Revision 1.29  2017/06/02 14:20:13  cvs
 		no message
 		
 		Revision 1.28  2017/06/02 09:03:44  cvs
 		no message
 		
 		Revision 1.27  2017/05/29 09:31:34  cvs
 		no message
 		
 		Revision 1.26  2016/08/25 12:19:03  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2016/05/07 14:54:16  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2015/01/24 19:51:15  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2015/01/21 16:59:58  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2013/11/09 16:19:17  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2013/03/20 16:56:01  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2012/12/19 17:00:08  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2012/11/25 13:15:50  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2012/01/22 13:44:07  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2012/01/21 07:20:05  rvv
 		*** empty log message ***

 		Revision 1.16  2012/01/18 19:05:04  rvv
 		*** empty log message ***

 		Revision 1.15  2012/01/15 11:02:29  rvv
 		*** empty log message ***

 		Revision 1.14  2011/12/31 18:16:36  rvv
 		*** empty log message ***

 		Revision 1.13  2010/11/28 16:23:06  rvv
 		*** empty log message ***

 		Revision 1.12  2010/11/27 16:15:25  rvv
 		*** empty log message ***

 		Revision 1.11  2010/09/15 09:37:42  rvv
 		*** empty log message ***

 		Revision 1.10  2009/04/25 15:47:21  rvv
 		*** empty log message ***

 		Revision 1.9  2009/01/20 17:46:01  rvv
 		*** empty log message ***

 		Revision 1.8  2008/12/30 15:33:21  rvv
 		*** empty log message ***

 		Revision 1.7  2006/01/19 07:44:30  cvs
 		*** empty log message ***


*/
// ophalen uit session ?
$disable_auth = true;
$__appvar["module"] = "";
include_once("wwwvars.php");
// session start pas na include Objecten!
if (!class_exists("Shortcut")) include_once("../classes/AE_cls_shortcut.php");
session_start();
//echo 'updates';
$content['javascript']='';
//$content = array();
$content['style'] = '
  
  <link href="style/submenu.css" rel="stylesheet" type="text/css" media="screen">
  ';

$db=new DB();
$htmlcode='';
if($__appvar['master'] == true && $__appvar['bedrijf'] == 'HOME')
{

  $query = "SELECT id FROM klantMutaties WHERE verwerkt <> 1 ";
  if($db->QRecords($query)>0)
  {
    $items[]=array(vt("Nieuwe Klantmutaties"),"klantmutatiesList.php");
  }
  
  $query = "SELECT id FROM fondsAanvragen WHERE verwerkt = 0 ";
  if($db->QRecords($query)>0)
  {
    $items[]=array(vt("Nieuwe fondsaanvraag"),"fondsaanvragenList.php?filterNew=1");
  }
  
  $query = "SELECT id FROM fondskoersAanvragen WHERE verwerkt = 0 AND change_date>now()-interval 1 year";
  if($db->QRecords($query)>0)
  {
    $items[]=array(vt("Nieuwe fondskoersaanvraag"),"fondskoersaanvragenList.php?filterNew=1");
  }

  $query = "SELECT Rekeningen.Memoriaal FROM Rekeningen,VoorlopigeRekeningafschriften WHERE   Rekeningen.Rekening=VoorlopigeRekeningafschriften.Rekening AND VoorlopigeRekeningafschriften.verwerkt <> 1 GROUP BY Rekeningen.Memoriaal";
  if($db->QRecords($query)>0)
  {
    $menu = new Submenu();
    while($data=$db->nextRecord())
    {
      if($data['Memoriaal']==1)
        $items[]=array("Nieuwe Memoriaalboekingen","voorlopigeRekeningafschriftenList.php?memoriaal=true");
      else
        $items[]=array("Nieuwe Rekeningmutaties","voorlopigeRekeningafschriftenList.php");
    }
    $htmlcode.= $menu->getHtml()."<br>";
  }


  if(count($items) > 0)
  {
    $menu = new Submenu();
    foreach ($items as $data)
      $menu->addItem($data[0],$data[1]);
    $htmlcode .=$menu->getHtml()."<br>";
  }
}

$ingevoerdeOrders='';
$query="SELECT Vermogensbeheerders.OrderOrderdesk,Vermogensbeheerders.check_module_ORDER
FROM Vermogensbeheerders
Inner Join VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder 
WHERE VermogensbeheerdersPerGebruiker.Gebruiker='".$_SESSION['usersession']['gebruiker']['Gebruiker']."' limit 1 ";
$db->SQL($query);
$orderOrderdesk=$db->lookupRecord();
if($orderOrderdesk['OrderOrderdesk']==1)
{
  if($orderOrderdesk['check_module_ORDER']==2)
  {
    if($db->QRecords("SELECT id FROM OrdersV2 WHERE orderStatus=0"))
    {
      $menu = new Submenu();
      $menu->addItem('Nieuwe orders','ordersListV2.php?status=ingevoerd',array("style"=>'font-size:22px;'));
      $ingevoerdeOrders .= $menu->getHtml()."<br>";
    }
  }
  else
  {
    if($db->QRecords("SELECT id FROM Orders WHERE laatsteStatus=0"))
    {
      $menu = new Submenu();
      $menu->addItem('Nieuwe orders','ordersList.php?status=ingevoerd',array("style"=>'font-size:22px;'));
      $ingevoerdeOrders .= $menu->getHtml()."<br>";
    }
    if($db->QRecords("SELECT id FROM Orders WHERE laatsteStatus=-1 AND add_date < now()- interval 10 MINUTE"))
    {
      $db->SQL("UPDATE Orders SET laatsteStatus=0 WHERE laatsteStatus=-1 AND add_date < now()- interval 10 MINUTE");
      $db->Query();
    }
  }
}


if($_SESSION['submenu'])
{
	$content['body'] .= $_SESSION['submenu']->onLoad;
}

echo template($__appvar["templateContentHeader"],$content);
if($_SESSION['shortcut'] && !$_SESSION['btr'])
{
  echo $_SESSION['shortcut']->getHtml();

}

echo "<div id='subMenuDiv'>$htmlcode $ingevoerdeOrders</div>";
/**
 * Kijk of het object Sumenu in de session staat.
 *
 * Als dit het geval is echo dan de HTML
 *
 **/
if($_SESSION['submenu'])
{
	echo $_SESSION['submenu']->getHtml();
}
//listarray($__logVar);

if(is_object($_SESSION['submenu']))
{
  
  if($_SESSION['submenu']->menuItems[1]['url'] != 'CRM_nawList.php?sql=deb' )
    $_SESSION['submenu'] = "";
}
session_write_close();
echo template($__appvar["templateContentFooter"],$content);
?>
<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2017/06/09 07:05:57 $
    File Versie         : $Revision: 1.2 $

    $Log: mainmenu_shortcut.php,v $
    Revision 1.2  2017/06/09 07:05:57  cvs
    call 5581

    Revision 1.1  2017/05/29 07:52:54  cvs
    no message



*/
session_start();
if (!class_exists("Shortcut")) include_once("../classes/AE_cls_shortcut.php");
$menuList =  $mnu->createMenu();

if (GetModuleAccess('alleenNAW') == 1)
{
  $crmScript = 'CRM_nawOnlyList.php';
}
else
{
  $crmScript = 'CRM_nawList.php';
}
// BTR:TODO
// $crmScript = 'redirect_to_btr.php?section=CRM';

$_SESSION["shortcut"] = New Shortcut();
$_SESSION["shortcut"]->addItem("house","welcome.php",array("title"=>"Startpagina"));
$_SESSION["shortcut"]->addItem("user1",$crmScript,array("title"=>"Alle relaties"));
$_SESSION["shortcut"]->addItem("user2",'redirect_to_btr.php?section=Home',array("title"=>"Nieuwe frontend"));
if (getVermogensbeheerderField("check_module_ORDER") == 2)  // alleen bij ordersV2
{
  $_SESSION["shortcut"]->addItem("currency_euro","ordersEditV2.php?action=new&returnUrl=ordersList.php?status=ingevoerd",array("title"=>"Nieuwe order"));
}
if(!isset($__appvar["crmOnly"]))
{
  $_SESSION["shortcut"]->addItem("line-chart", "rapportFrontofficeClientSelectie.php", array("title" => "Rapportages"));
}